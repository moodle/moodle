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
 * @package    core_backup
 * @category   phpunit
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/fixtures/plan_fixtures.php');


/**
 * plan tests (all)
 */
class backup_plan_testcase extends advanced_testcase {

    protected $moduleid;  // course_modules id used for testing
    protected $sectionid; // course_sections id used for testing
    protected $courseid;  // course id used for testing
    protected $userid;      // user record used for testing

    protected function setUp() {
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
        $CFG->backup_file_logger_level = backup::LOG_NONE;
        $CFG->backup_database_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level_extra = backup::LOG_NONE;
    }

    /**
     * test base_plan class
     */
    function test_base_plan() {

        // Instantiate
        $bp = new mock_base_plan('name');
        $this->assertTrue($bp instanceof base_plan);
        $this->assertEquals($bp->get_name(), 'name');
        $this->assertTrue(is_array($bp->get_settings()));
        $this->assertEquals(count($bp->get_settings()), 0);
        $this->assertTrue(is_array($bp->get_tasks()));
        $this->assertEquals(count($bp->get_tasks()), 0);
    }

    /**
     * test backup_plan class
     */
    function test_backup_plan() {

        // We need one (non interactive) controller for instantiating plan
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        // Instantiate one backup plan
        $bp = new backup_plan($bc);
        $this->assertTrue($bp instanceof backup_plan);
        $this->assertEquals($bp->get_name(), 'backup_plan');

        // Calculate checksum and check it
        $checksum = $bp->calculate_checksum();
        $this->assertTrue($bp->is_checksum_correct($checksum));

        $bc->destroy();
    }

    /**
     * wrong base_plan class tests
     */
    function test_base_plan_wrong() {

        // We need one (non interactive) controller for instantiating plan
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        // Instantiate one backup plan
        $bp = new backup_plan($bc);
        // Add wrong task
        try {
            $bp->add_task(new stdclass());
            $this->assertTrue(false, 'base_plan_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_plan_exception);
            $this->assertEquals($e->errorcode, 'wrong_base_task_specified');
        }
    }

    /**
     * wrong backup_plan class tests
     */
    function test_backup_plan_wrong() {

        // Try to pass one wrong controller
        try {
            $bp = new backup_plan(new stdclass());
            $this->assertTrue(false, 'backup_plan_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_plan_exception);
            $this->assertEquals($e->errorcode, 'wrong_backup_controller_specified');
        }
        try {
            $bp = new backup_plan(null);
            $this->assertTrue(false, 'backup_plan_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_plan_exception);
            $this->assertEquals($e->errorcode, 'wrong_backup_controller_specified');
        }

        // Try to build one non-existent format plan (when creating the controller)
        // We need one (non interactive) controller for instatiating plan
        try {
            $bc = new backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, 'non_existing_format',
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
            $this->assertTrue(false, 'backup_controller_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_controller_exception);
            $this->assertEquals($e->errorcode, 'backup_check_unsupported_format');
            $this->assertEquals($e->a, 'non_existing_format');
        }
    }
}

