<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The main interface for recycle bin methods.
 *
 * @package    local_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_recyclebin;

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a category's recyclebin.
 *
 * @package    local_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category extends recyclebin
{
    private $_categoryid;

    /**
     * Constructor.
     *
     * @param int $categoryid Category ID.
     */
    public function __construct($categoryid) {
        $this->_categoryid = $categoryid;
    }

    /**
     * Is this recyclebin enabled?
     */
    public static function is_enabled() {
        return get_config('local_recyclebin', 'enablecategory');
    }

    /**
     * Returns an item from the recycle bin.
     *
     * @param $item int Item ID to retrieve.
     */
    public function get_item($itemid) {
        global $DB;

        $item = $DB->get_record('local_recyclebin_category', array(
            'id' => $itemid
        ), '*', MUST_EXIST);

        $item->name = get_course_display_name_for_list($item);

        return $item;
    }

    /**
     * Returns a list of items in the recycle bin for this course.
     */
    public function get_items() {
        global $DB;

        $items = $DB->get_records('local_recyclebin_category', array(
            'category' => $this->_categoryid
        ));

        foreach ($items as $item) {
            $item->name = get_course_display_name_for_list($item);
        }

        return $items;
    }

    /**
     * Store a course in the recycle bin.
     *
     * @param $course stdClass Course
     * @throws \coding_exception
     * @throws \invalid_dataroot_permissions
     * @throws \moodle_exception
     */
    public function store_item($course) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

        // Backup the course.
        $user = get_admin();
        $controller = new \backup_controller(
            \backup::TYPE_1COURSE,
            $course->id,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL,
            $user->id
        );
        $controller->execute_plan();

        // Grab the result.
        $result = $controller->get_results();
        if (!isset($result['backup_destination'])) {
            throw new \moodle_exception('Failed to backup activity prior to deletion.');
        }

        // Grab the filename.
        $file = $result['backup_destination'];
        if (!$file->get_contenthash()) {
            throw new \moodle_exception('Failed to backup activity prior to deletion (invalid file).');
        }

        // Make sure our backup dir exists.
        $bindir = $CFG->dataroot . '/recyclebin';
        if (!file_exists($bindir)) {
            make_writable_directory($bindir);
        }

        // Record the activity, get an ID.
        $binid = $DB->insert_record('local_recyclebin_category', array(
            'category' => $course->category,
            'shortname' => $course->shortname,
            'fullname' => $course->fullname,
            'deleted' => time()
        ));

        // Move the file to our own special little place.
        if (!$file->copy_content_to($bindir . '/course-' . $binid)) {
            // Failed, cleanup first.
            $DB->delete_records('local_recyclebin_category', array(
                'id' => $binid
            ));

            throw new \moodle_exception("Failed to copy backup file to recyclebin.");
        }

        // Delete the old file.
        $file->delete();

        // Fire event.
        $event = \local_recyclebin\event\course_stored::create(array(
            'objectid' => $binid,
            'context' => \context_coursecat::instance($course->category)
        ));
        $event->trigger();
    }

    /**
     * Restore an item from the recycle bin.
     *
     * @param stdClass $item The item database record
     * @throws \Exception
     * @throws \coding_exception
     * @throws \moodle_exception
     * @throws \restore_controller_exception
     */
    public function restore_item($item) {
        global $CFG;

        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        require_once($CFG->dirroot . '/course/lib.php');

        $user = get_admin();

        // Build a course.
        $course = new \stdClass();
        $course->category = $this->_categoryid;
        $course->shortname = $item->shortname;
        $course->fullname = $item->fullname;
        $course->summary = '';

        // TODO - Maybe handle non-unique shortnames, missing categories, etc?

        // Create a new course.
        $course = create_course($course);
        if (!$course) {
            throw new \moodle_exception("Could not create course to restore into.");
        }

        // Get the pathname.
        $source = $CFG->dataroot . '/recyclebin/course-' . $item->id;
        if (!file_exists($source)) {
            throw new \moodle_exception('Invalid recycle bin item!');
        }

        // Grab the course context.
        $context = \context_coursecat::instance($this->_categoryid);

        // Grab a tmpdir.
        $tmpdir = \restore_controller::get_tempdir_name($context->id, $user->id);

        // Extract the backup to tmpdir.
        $fb = get_file_packer('application/vnd.moodle.backup');
        $fb->extract_to_pathname($source, $CFG->tempdir . '/backup/' . $tmpdir . '/');

        // Define the import.
        $controller = new \restore_controller(
            $tmpdir,
            $course->id,
            \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL,
            $user->id,
            \backup::TARGET_NEW_COURSE
        );

        // Prechecks.
        if (!$controller->execute_precheck()) {
            $results = $controller->get_precheck_results();

            if (isset($results['errors'])) {
                debugging(var_export($results, true));
                throw new \moodle_exception("Restore failed.");
            }

            if (isset($results['warnings'])) {
                debugging(var_export($results['warnings'], true));
            }
        }

        // Run the import.
        $controller->execute_plan();

        // Fire event.
        $event = \local_recyclebin\event\course_restored::create(array(
            'objectid' => $item->id,
            'context' => $context
        ));
        $event->add_record_snapshot('local_recyclebin_category', $item);
        $event->trigger();

        // Cleanup.
        $this->delete_item($item, true);
    }

    /**
     * Delete an item from the recycle bin.
     *
     * @param stdClass $item The item database record
     * @param boolean $noevent Whether or not to fire a purged event.
     * @throws \coding_exception
     */
    public function delete_item($item, $noevent = false) {
        global $CFG, $DB;

        // Delete the file.
        unlink($CFG->dataroot . '/recyclebin/course-' . $item->id);

        // Delete the record.
        $DB->delete_records('local_recyclebin_category', array(
            'id' => $item->id
        ));

        if ($noevent) {
            return;
        }

        // Fire event.
        $event = \local_recyclebin\event\course_purged::create(array(
            'objectid' => $item->id,
            'context' => \context_coursecat::instance($item->category)
        ));
        $event->add_record_snapshot('local_recyclebin_category', $item);
        $event->trigger();
    }

    /**
     * Can we view this item?
     *
     * @param stdClass $item The item database record
     */
    public function can_view($item) {
        $context = \context_coursecat::instance($item->category);
        return has_capability('local/recyclebin:view_course', $context);
    }

    /**
     * Can we restore this?
     *
     * @param stdClass $item The item database record
     */
    public function can_restore($item) {
        $context = \context_coursecat::instance($item->category);
        return has_capability('local/recyclebin:restore_course', $context);
    }

    /**
     * Can we delete this?
     *
     * @param stdClass $item The item database record
     */
    public function can_delete($item) {
        $context = \context_coursecat::instance($item->category);
        return has_capability('local/recyclebin:delete_course', $context);
    }
}
