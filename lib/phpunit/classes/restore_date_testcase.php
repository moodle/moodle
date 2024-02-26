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
 * Restore dates test case.
 *
 * @package    core
 * @category   test
 * @copyright  2017 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');


/**
 * Advanced PHPUnit test case customised for testing restore dates in Moodle.
 *
 * @package    core
 * @category   test
 * @copyright  2017 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class restore_date_testcase extends advanced_testcase {
    /**
     * @var int Course start date.
     */
    protected $startdate;

    /**
     * @var int Course restore date.
     */
    protected $restorestartdate;

    /**
     * Setup.
     */
    public function setUp(): void {
        global $CFG;

        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->startdate = strtotime('1 Jan 2017 00:00 GMT');
        $this->restorestartdate = strtotime('1 Feb 2017 00:00 GMT');
        $CFG->enableavailability = true;
    }

    /**
     * Backs a course up and restores it.
     *
     * @param stdClass $course Course object to backup
     * @param int $newdate If non-zero, specifies custom date for new course
     * @return int ID of newly restored course
     */
    protected function backup_and_restore($course, $newdate = 0) {
        global $USER, $CFG;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do backup with default settings.
        set_config('backup_general_users', 1, 'backup');
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id,
            backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_GENERAL,
            $USER->id);
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $fp = get_file_packer('application/vnd.moodle.backup');
        $filepath = $CFG->dataroot . '/temp/backup/test-restore-course';
        $file->extract_to_pathname($fp, $filepath);
        $bc->destroy();

        // Do restore to new course with default settings.
        $newcourseid = restore_dbops::create_new_course(
            $course->fullname, $course->shortname . '_2', $course->category);
        $rc = new restore_controller('test-restore-course', $newcourseid,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
            backup::TARGET_NEW_COURSE);

        if (empty($newdate)) {
            $newdate = $this->restorestartdate;
        }

        $rc->get_plan()->get_setting('course_startdate')->set_value($newdate);
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        return $newcourseid;
    }

    /**
     * Helper method to create a course and a module.
     *
     * @param string $modulename
     * @param array|stdClass $record
     * @return array
     */
    protected function create_course_and_module($modulename, $record = []) {
        if ($modulename == 'chat') {
            $manager = \core_plugin_manager::resolve_plugininfo_class('mod');
            $manager::enable_plugin('chat', 1);
        }
        if ($modulename == 'survey') {
            $manager = \core_plugin_manager::resolve_plugininfo_class('mod');
            $manager::enable_plugin('survey', 1);
        }

        // Create a course with specific start date.
        $record = (array)$record;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['startdate' => $this->startdate]);
        $record = array_merge(['course' => $course->id], $record);
        $module = $this->getDataGenerator()->create_module($modulename, $record);
        return [$course, $module];
    }

    /**
     * Verify that the given properties are not rolled.
     *
     * @param stdClass $oldinstance
     * @param stdClass $newinstance
     * @param [] $props
     */
    protected function assertFieldsNotRolledForward($oldinstance, $newinstance, $props) {
        foreach ($props as $prop) {
            $this->assertEquals($oldinstance->$prop, $newinstance->$prop, "'$prop' should not roll forward.");
        }
    }

    /**
     * Verify that the given properties are rolled.
     *
     * @param stdClass $oldinstance
     * @param stdClass $newinstance
     * @param [] $props
     */
    protected function assertFieldsRolledForward($oldinstance, $newinstance, $props) {
        $diff = $this->get_diff();
        foreach ($props as $prop) {
            $this->assertEquals(($oldinstance->$prop + $diff), $newinstance->$prop, "'$prop' doesn't roll as expected.");
        }
    }

    /**
     * Get time diff between start date and restore date in seconds.
     *
     * @return mixed
     */
    protected function get_diff() {
        return ($this->restorestartdate - $this->startdate);
    }

}
