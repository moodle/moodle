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
 * @package   core_backup
 * @category  test
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_backup;

use backup;
use backup_controller;
use restore_controller;

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff.
global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Tests for the backup and restore controller classes.
 *
 * @package   core_backup
 * @category  test
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controller_test extends \advanced_testcase {

    protected $moduleid;  // course_modules id used for testing
    protected $sectionid; // course_sections id used for testing
    protected $courseid;  // course id used for testing
    protected $userid;    // user used if for testing

    protected function setUp(): void {
        global $DB, $CFG;
        parent::setUp();

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', array('course'=>$course->id), array('section'=>3));
        $coursemodule = $DB->get_record('course_modules', array('id'=>$page->cmid));

        $this->moduleid  = $coursemodule->id;
        $this->sectionid = $DB->get_field("course_sections", 'id', array("section"=>$coursemodule->section, "course"=>$course->id));
        $this->courseid  = $coursemodule->course;
        $this->userid = 2; // admin

        // Disable all loggers
        $CFG->backup_error_log_logger_level = backup::LOG_NONE;
        $CFG->backup_output_indented_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level = backup::LOG_NONE;
        $CFG->backup_database_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level_extra = backup::LOG_NONE;
    }

    /**
     * Test get_copy
     *
     * @covers \restore_controller::get_copy
     */
    public function test_restore_controller_get_copy(): void {
        $copydata = (object)["some" => "copydata"];
        $rc = new \restore_controller(1729, $this->courseid, backup::INTERACTIVE_NO, backup::MODE_COPY,
                $this->userid, backup::TARGET_NEW_COURSE, null, backup::RELEASESESSION_NO, $copydata);

        $this->assertEquals($copydata, $rc->get_copy());
    }

    /**
     * Test instantiating a restore controller for a course copy without providing copy data.
     *
     * @covers \restore_controller::__construct
     */
    public function test_restore_controller_copy_without_copydata(): void {
        $this->expectException(\restore_controller_exception::class);

        new \restore_controller(1729, $this->courseid, backup::INTERACTIVE_NO, backup::MODE_COPY,
                $this->userid, backup::TARGET_NEW_COURSE);
    }

    /*
     * test base_setting class
     */
    public function test_backup_controller(): void {
        // Instantiate non interactive backup_controller
        $bc = new mock_backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        $this->assertTrue($bc instanceof backup_controller);
        $this->assertEquals($bc->get_status(), backup::STATUS_AWAITING);
        // Instantiate interactive backup_controller
        $bc = new mock_backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_YES, backup::MODE_GENERAL, $this->userid);
        $this->assertTrue($bc instanceof backup_controller);
        $this->assertEquals($bc->get_status(), backup::STATUS_SETTING_UI);
        $this->assertEquals(strlen($bc->get_backupid()), 32); // is one md5

        // Save and load one backup controller to check everything is in place
        $bc = new mock_backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        $recid = $bc->save_controller();
        $newbc = mock_backup_controller::load_controller($bc->get_backupid());
        $this->assertTrue($newbc instanceof backup_controller); // This means checksum and load worked ok
    }

    public function test_backup_controller_include_files(): void {
        // A MODE_GENERAL controller - this should include files
        $bc = new mock_backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        $this->assertEquals($bc->get_include_files(), 1);


        // The MODE_IMPORT and MODE_SAMESITE should not include files in the backup.
        // A MODE_IMPORT controller
        $bc = new mock_backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $this->userid);
        $this->assertEquals($bc->get_include_files(), 0);

        // A MODE_SAMESITE controller
        $bc = new mock_backup_controller(backup::TYPE_1COURSE, $this->courseid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $this->userid);
        $this->assertEquals($bc->get_include_files(), 0);
    }

    /**
     * Test set kept roles method.
     */
    public function test_backup_controller_set_kept_roles(): void {
        $this->expectException(\backup_controller_exception::class);

        // Set up controller as a non-copy operation.
        $bc = new \backup_controller(backup::TYPE_1COURSE, $this->courseid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid, backup::RELEASESESSION_YES);

        $bc->set_kept_roles(array(1, 3, 5));
    }

    /**
     * Tests the restore_controller.
     */
    public function test_restore_controller_is_executing(): void {
        global $CFG;

        // Make a backup.
        make_backup_temp_directory('');
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $this->userid);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // The progress class will get called during restore, so we can use that
        // to check the executing flag is true.
        $progress = new core_backup_progress_restore_is_executing();

        // Set up restore.
        $rc = new restore_controller($backupid, $this->courseid,
                backup::INTERACTIVE_NO, backup::MODE_SAMESITE, $this->userid,
                backup::TARGET_EXISTING_ADDING);
        $this->assertTrue($rc->execute_precheck());

        // Check restore is NOT executing.
        $this->assertFalse(restore_controller::is_executing());

        // Execute restore.
        $rc->set_progress($progress);
        $rc->execute_plan();

        // Check restore is NOT executing afterward either.
        $this->assertFalse(restore_controller::is_executing());
        $rc->destroy();

        // During restore, check that executing was true.
        $this->assertTrue(count($progress->executing) > 0);
        $alltrue = true;
        foreach ($progress->executing as $executing) {
            if (!$executing) {
                $alltrue = false;
                break;
            }
        }
        $this->assertTrue($alltrue);
    }

    /**
     * Test prepare copy method.
     */
    public function test_restore_controller_prepare_copy(): void {
        $this->expectException(\restore_controller_exception::class);

        global $CFG;

        // Make a backup.
        make_backup_temp_directory('');
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $this->userid);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Set up restore.
        $rc = new restore_controller($backupid, $this->courseid,
            backup::INTERACTIVE_NO, backup::MODE_SAMESITE, $this->userid,
            backup::TARGET_EXISTING_ADDING);
        $rc->prepare_copy();
    }

    /**
     * Test restore of deadlock causing backup.
     */
    public function test_restore_of_deadlock_causing_backup(): void {
        global $USER, $CFG;
        $this->preventResetByRollback();

        $foldername = 'deadlock';
        $fp = get_file_packer('application/vnd.moodle.backup');
        $tempdir = make_backup_temp_directory($foldername);
        $files = $fp->extract_to_pathname($CFG->dirroot . '/backup/controller/tests/fixtures/deadlock.mbz', $tempdir);

        $this->setAdminUser();
        $controller = new restore_controller(
            'deadlock',
            $this->courseid,
            backup::INTERACTIVE_NO,
            backup::MODE_GENERAL,
            $USER->id,
            backup::TARGET_NEW_COURSE
        );
        $this->assertTrue($controller->execute_precheck());
        $controller->execute_plan();
        $controller->destroy();
    }
}


/**
 * Progress class that records the result of restore_controller::is_executing calls.
 *
 * @package core_backup
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_backup_progress_restore_is_executing extends \core\progress\base {
    /** @var array Array of results from calling function */
    public $executing = array();

    public function update_progress() {
        $this->executing[] = restore_controller::is_executing();
    }
}


/*
 * helper extended @backup_controller class that makes some methods public for testing
 */
class mock_backup_controller extends backup_controller {

    public function save_controller($includeobj = true, $cleanobj = false) {
        parent::save_controller($includeobj, $cleanobj);
    }
}
