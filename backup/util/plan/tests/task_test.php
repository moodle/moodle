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

// Include all the needed stuff
global $CFG;
require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/executable.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/loggable.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/processable.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/annotable.class.php');
require_once($CFG->dirroot . '/backup/backup.class.php');
require_once($CFG->dirroot . '/backup/util/factories/backup_factory.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_controller_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_helper.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_general_helper.class.php');
require_once($CFG->dirroot . '/backup/util/checks/backup_check.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/base_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/error_log_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/file_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/database_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/output_indented_logger.class.php');
require_once($CFG->dirroot . '/backup/controller/backup_controller.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_plan.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_plan.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_task.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_task.class.php');
require_once($CFG->dirroot . '/backup/util/xml/contenttransformer/xml_contenttransformer.class.php');
require_once($CFG->dirroot . '/backup/util/settings/base_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/root/root_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/section/section_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/activity/activity_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_settingslib.php');
require_once($CFG->dirroot . '/backup/util/settings/base_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/activity/activity_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_plan.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_plan.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_task.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_task.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_step.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_step.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_execution_step.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_structure_step.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_atom.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_attribute.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_final_element.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_nested_element.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_optigroup.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_processor.class.php');
require_once($CFG->dirroot . '/backup/util/structure/backup_attribute.class.php');
require_once($CFG->dirroot . '/backup/util/structure/backup_final_element.class.php');
require_once($CFG->dirroot . '/backup/util/structure/backup_nested_element.class.php');
require_once($CFG->dirroot . '/backup/util/structure/backup_optigroup.class.php');
require_once($CFG->dirroot . '/backup/util/structure/backup_optigroup_element.class.php');
require_once($CFG->dirroot . '/backup/util/structure/backup_structure_processor.class.php');
require_once($CFG->dirroot . '/backup/util/output/output_controller.class.php');
require_once($CFG->dirroot . '/backup/util/ui/backup_ui_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/setting_dependency.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_plan_dbops.class.php');


/**
 * task tests (all)
 */
class backup_task_test extends advanced_testcase {

    protected $moduleid;  // course_modules id used for testing
    protected $sectionid; // course_sections id used for testing
    protected $courseid;  // course id used for testing
    protected $userid;      // user record used for testing

    public function setUp() {
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

        $bp = new mock_base_plan3('planname'); // We need one plan
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
        $bp = new mock_base_plan3('planname'); // We need one plan
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

/**
 * Instantiable class extending base_task in order to be able to perform tests
 */
class mock_base_task extends base_task {
    public function build() {
    }

    public function define_settings() {
    }
}

/**
 * Instantiable class extending backup_task in order to be able to perform tests
 */
class mock_backup_task extends backup_task {
    public function build() {
    }

    public function define_settings() {
    }
}

/**
 * Instantiable class extending base_plan in order to be able to perform tests
 */
class mock_base_plan3 extends base_plan {
    public function build() {
    }
}

