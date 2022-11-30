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
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_recyclebin;

defined('MOODLE_INTERNAL') || die();

define('TOOL_RECYCLEBIN_COURSECAT_BIN_FILEAREA', 'recyclebin_coursecat');

/**
 * Represents a category's recyclebin.
 *
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_bin extends base_bin {

    /**
     * @var int The category id.
     */
    protected $_categoryid;

    /**
     * Constructor.
     *
     * @param int $categoryid The category id.
     */
    public function __construct($categoryid) {
        $this->_categoryid = $categoryid;
    }

    /**
     * Is this recyclebin enabled?
     *
     * @return bool true if enabled, false if not.
     */
    public static function is_enabled() {
        return get_config('tool_recyclebin', 'categorybinenable');
    }

    /**
     * Returns an item from the recycle bin.
     *
     * @param int $itemid Item ID to retrieve.
     * @return \stdClass the item.
     */
    public function get_item($itemid) {
        global $DB;

        $item = $DB->get_record('tool_recyclebin_category', array(
            'id' => $itemid
        ), '*', MUST_EXIST);

        $item->name = get_course_display_name_for_list($item);

        return $item;
    }

    /**
     * Returns a list of items in the recycle bin for this course.
     *
     * @return array the list of items.
     */
    public function get_items() {
        global $DB;

        $items = $DB->get_records('tool_recyclebin_category', array(
            'categoryid' => $this->_categoryid
        ));

        foreach ($items as $item) {
            $item->name = get_course_display_name_for_list($item);
        }

        return $items;
    }

    /**
     * Store a course in the recycle bin.
     *
     * @param \stdClass $course Course
     * @throws \moodle_exception
     */
    public function store_item($course) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

        // As far as recycle bin is using MODE_AUTOMATED, it observes the backup_auto_storage
        // settings (storing backups @ real location and potentially not including files).
        // For recycle bin we want to ensure that backup files are always stored in Moodle file
        // area and always contain the users' files. In order to achieve that, we hack the
        // setting here via $CFG->forced_plugin_settings, so it won't interfere other operations.
        // See MDL-65218 and MDL-35773 for more information.
        // This hack will be removed once recycle bin switches to use its own backup mode, with
        // own preferences and 100% separate from MOODLE_AUTOMATED.
        // TODO: Remove this as part of MDL-65228.
        $CFG->forced_plugin_settings['backup'] = ['backup_auto_storage' => 0, 'backup_auto_files' => 1];

        // Backup the course.
        $user = get_admin();
        $controller = new \backup_controller(
            \backup::TYPE_1COURSE,
            $course->id,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_AUTOMATED,
            $user->id
        );
        $controller->execute_plan();

        // We don't need the forced setting anymore, hence unsetting it.
        // TODO: Remove this as part of MDL-65228.
        unset($CFG->forced_plugin_settings['backup']);

        // Grab the result.
        $result = $controller->get_results();
        if (!isset($result['backup_destination'])) {
            throw new \moodle_exception('Failed to backup activity prior to deletion.');
        }

        // Have finished with the controller, let's destroy it, freeing mem and resources.
        $controller->destroy();

        // Grab the filename.
        $file = $result['backup_destination'];
        if (!$file->get_contenthash()) {
            throw new \moodle_exception('Failed to backup activity prior to deletion (invalid file).');
        }

        // Record the activity, get an ID.
        $item = new \stdClass();
        $item->categoryid = $course->category;
        $item->shortname = $course->shortname;
        $item->fullname = $course->fullname;
        $item->timecreated = time();
        $binid = $DB->insert_record('tool_recyclebin_category', $item);

        // Create the location we want to copy this file to.
        $filerecord = array(
            'contextid' => \context_coursecat::instance($course->category)->id,
            'component' => 'tool_recyclebin',
            'filearea' => TOOL_RECYCLEBIN_COURSECAT_BIN_FILEAREA,
            'itemid' => $binid,
            'timemodified' => time()
        );

        // Move the file to our own special little place.
        $fs = get_file_storage();
        if (!$fs->create_file_from_storedfile($filerecord, $file)) {
            // Failed, cleanup first.
            $DB->delete_records('tool_recyclebin_category', array(
                'id' => $binid
            ));

            throw new \moodle_exception("Failed to copy backup file to recyclebin.");
        }

        // Delete the old file.
        $file->delete();

        // Fire event.
        $event = \tool_recyclebin\event\category_bin_item_created::create(array(
            'objectid' => $binid,
            'context' => \context_coursecat::instance($course->category)
        ));
        $event->trigger();
    }

    /**
     * Restore an item from the recycle bin.
     *
     * @param \stdClass $item The item database record
     * @throws \moodle_exception
     */
    public function restore_item($item) {
        global $CFG, $OUTPUT, $PAGE;

        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        require_once($CFG->dirroot . '/course/lib.php');

        $user = get_admin();

        // Grab the course category context.
        $context = \context_coursecat::instance($this->_categoryid);

        // Get the backup file.
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'tool_recyclebin', TOOL_RECYCLEBIN_COURSECAT_BIN_FILEAREA, $item->id,
            'itemid, filepath, filename', false);

        if (empty($files)) {
            throw new \moodle_exception('Invalid recycle bin item!');
        }

        if (count($files) > 1) {
            throw new \moodle_exception('Too many files found!');
        }

        // Get the backup file.
        $file = reset($files);

        // Get a backup temp directory name and create it.
        $tempdir = \restore_controller::get_tempdir_name($context->id, $user->id);
        $fulltempdir = make_backup_temp_directory($tempdir);

        // Extract the backup to tmpdir.
        $fb = get_file_packer('application/vnd.moodle.backup');
        $fb->extract_to_pathname($file, $fulltempdir);

        // Build a course.
        $course = new \stdClass();
        $course->category = $this->_categoryid;
        $course->shortname = $item->shortname;
        $course->fullname = $item->fullname;
        $course->summary = '';

        // Create a new course.
        $course = create_course($course);
        if (!$course) {
            throw new \moodle_exception("Could not create course to restore into.");
        }

        // As far as recycle bin is using MODE_AUTOMATED, it observes the General restore settings.
        // For recycle bin we want to ensure that backup files are always restore the users and groups information.
        // In order to achieve that, we hack the setting here via $CFG->forced_plugin_settings,
        // so it won't interfere other operations.
        // See MDL-65218 and MDL-35773 for more information.
        // This hack will be removed once recycle bin switches to use its own backup mode, with
        // own preferences and 100% separate from MOODLE_AUTOMATED.
        // TODO: Remove this as part of MDL-65228.
        $CFG->forced_plugin_settings['restore'] = ['restore_general_users' => 1, 'restore_general_groups' => 1];

        // Define the import.
        $controller = new \restore_controller(
            $tempdir,
            $course->id,
            \backup::INTERACTIVE_NO,
            \backup::MODE_AUTOMATED,
            $user->id,
            \backup::TARGET_NEW_COURSE
        );

        // Prechecks.
        if (!$controller->execute_precheck()) {
            $results = $controller->get_precheck_results();

            // Check if errors have been found.
            if (!empty($results['errors'])) {
                // Delete the temporary file we created.
                fulldelete($fulltempdir);

                // Delete the course we created.
                delete_course($course, false);

                echo $OUTPUT->header();
                $backuprenderer = $PAGE->get_renderer('core', 'backup');
                echo $backuprenderer->precheck_notices($results);
                echo $OUTPUT->continue_button(new \moodle_url('/course/index.php', array('categoryid' => $this->_categoryid)));
                echo $OUTPUT->footer();
                exit();
            }
        }

        // Run the import.
        $controller->execute_plan();

        // We don't need the forced setting anymore, hence unsetting it.
        // TODO: Remove this as part of MDL-65228.
        unset($CFG->forced_plugin_settings['restore']);

        // Have finished with the controller, let's destroy it, freeing mem and resources.
        $controller->destroy();

        // Fire event.
        $event = \tool_recyclebin\event\category_bin_item_restored::create(array(
            'objectid' => $item->id,
            'context' => $context
        ));
        $event->add_record_snapshot('tool_recyclebin_category', $item);
        $event->trigger();

        // Cleanup.
        fulldelete($fulltempdir);
        $this->delete_item($item);
    }

    /**
     * Delete an item from the recycle bin.
     *
     * @param \stdClass $item The item database record
     * @throws \coding_exception
     */
    public function delete_item($item) {
        global $DB;

        // Grab the course category context.
        $context = \context_coursecat::instance($this->_categoryid, IGNORE_MISSING);
        if (!empty($context)) {
            // Delete the files.
            $fs = get_file_storage();
            $fs->delete_area_files($context->id, 'tool_recyclebin', TOOL_RECYCLEBIN_COURSECAT_BIN_FILEAREA, $item->id);
        } else {
            // Course category has been deleted. Find records using $item->id as this is unique for coursecat recylebin.
            $files = $DB->get_recordset('files', [
                'component' => 'tool_recyclebin',
                'filearea' => TOOL_RECYCLEBIN_COURSECAT_BIN_FILEAREA,
                'itemid' => $item->id,
            ]);
            $fs = get_file_storage();
            foreach ($files as $filer) {
                $file = $fs->get_file_instance($filer);
                $file->delete();
            }
            $files->close();
        }

        // Delete the record.
        $DB->delete_records('tool_recyclebin_category', array(
            'id' => $item->id
        ));

        // The coursecat might have been deleted, check we have a context before triggering event.
        if (!$context) {
            return;
        }

        // Fire event.
        $event = \tool_recyclebin\event\category_bin_item_deleted::create(array(
            'objectid' => $item->id,
            'context' => \context_coursecat::instance($item->categoryid)
        ));
        $event->add_record_snapshot('tool_recyclebin_category', $item);
        $event->trigger();
    }

    /**
     * Can we view items in this recycle bin?
     *
     * @return bool returns true if they can view, false if not
     */
    public function can_view() {
        $context = \context_coursecat::instance($this->_categoryid);
        return has_capability('tool/recyclebin:viewitems', $context);
    }

    /**
     * Can we restore items in this recycle bin?
     *
     * @return bool returns true if they can restore, false if not
     */
    public function can_restore() {
        $context = \context_coursecat::instance($this->_categoryid);
        return has_capability('tool/recyclebin:restoreitems', $context);
    }

    /**
     * Can we delete items in this recycle bin?
     *
     * @return bool returns true if they can delete, false if not
     */
    public function can_delete() {
        $context = \context_coursecat::instance($this->_categoryid);
        return has_capability('tool/recyclebin:deleteitems', $context);
    }
}
