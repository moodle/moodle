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
 * task tests (all)
 */
class backup_task_testcase extends advanced_testcase {

    protected $moduleid;  // course_modules id used for testing
    protected $sectionid; // course_sections id used for testing
    protected $courseid;  // course id used for testing
    protected $userid;      // user record used for testing

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
        $CFG->backup_file_logger_level = backup::LOG_NONE;
        $CFG->backup_database_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level_extra = backup::LOG_NONE;
    }

    /**
     * test base_task class
     */
    function test_base_task() {

        $bp = new mock_base_plan('planname'); // We need one plan
        // Instantiate
        $bt = new mock_base_task('taskname', $bp);
        $this->assertTrue($bt instanceof base_task);
        $this->assertEquals($bt->get_name(), 'taskname');
        $this->assertTrue(is_array($bt->get_settings()));
        $this->assertEquals(count($bt->get_settings()), 0);
        $this->assertTrue(is_array($bt->get_steps()));
        $this->assertEquals(count($bt->get_steps()), 0);
    }

    /**
     * test backup_task class
     */
    function test_backup_task() {

        // We need one (non interactive) controller for instatiating plan
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        // We need one plan
        $bp = new backup_plan($bc);
        // Instantiate task
        $bt = new mock_backup_task('taskname', $bp);
        $this->assertTrue($bt instanceof backup_task);
        $this->assertEquals($bt->get_name(), 'taskname');

        // Calculate checksum and check it
        $checksum = $bt->calculate_checksum();
        $this->assertTrue($bt->is_checksum_correct($checksum));

        $bc->destroy();
    }

    /**
     * wrong base_task class tests
     */
    function test_base_task_wrong() {

        // Try to pass one wrong plan
        try {
            $bt = new mock_base_task('tasktest', new stdclass());
            $this->assertTrue(false, 'base_task_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_task_exception);
            $this->assertEquals($e->errorcode, 'wrong_base_plan_specified');
        }

        // Add wrong step to task
        $bp = new mock_base_plan('planname'); // We need one plan
        // Instantiate
        $bt = new mock_base_task('taskname', $bp);
        try {
            $bt->add_step(new stdclass());
            $this->assertTrue(false, 'base_task_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_task_exception);
            $this->assertEquals($e->errorcode, 'wrong_base_step_specified');
        }

    }

    /**
     * wrong backup_task class tests
     */
    function test_backup_task_wrong() {

        // Try to pass one wrong plan
        try {
            $bt = new mock_backup_task('tasktest', new stdclass());
            $this->assertTrue(false, 'backup_task_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_task_exception);
            $this->assertEquals($e->errorcode, 'wrong_backup_plan_specified');
        }
    }
}
