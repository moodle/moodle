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
 * @package moodlecore
 * @subpackage backup-tests
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevent direct access to this file
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

// Include all the needed stuff
require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/processable.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/annotable.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/executable.class.php');
require_once($CFG->dirroot . '/backup/backup.class.php');
require_once($CFG->dirroot . '/backup/util/factories/backup_factory.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_array_iterator.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_controller_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_structure_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_helper.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_general_helper.class.php');
require_once($CFG->dirroot . '/backup/util/checks/backup_check.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/base_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/error_log_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/file_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/database_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/output_indented_logger.class.php');
require_once($CFG->dirroot . '/backup/util/xml/output/xml_output.class.php');
require_once($CFG->dirroot . '/backup/util/xml/output/file_xml_output.class.php');
require_once($CFG->dirroot . '/backup/util/xml/xml_writer.class.php');
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
require_once($CFG->dirroot . '/backup/controller/backup_controller.class.php');
require_once($CFG->dirroot . '/backup/util/settings/base_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/activity/activity_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_plan.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_plan.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_task.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_task.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_step.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_step.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_structure_step.class.php');

/*
 * step tests (all)
 */
class backup_step_test extends UnitTestCase {

    public static $includecoverage = array('backup/util/plan');
    public static $excludecoverage = array('backup/util/plan/simpletest');

    protected $moduleid;  // course_modules id used for testing
    protected $sectionid; // course_sections id used for testing
    protected $courseid;  // course id used for testing
    protected $user;      // user record used for testing

    function __construct() {
        global $DB, $USER, $CFG;

        $this->moduleid  = 0;
        $this->sectionid = 0;
        $this->courseid  = 0;
        $this->userid = $USER->id;
        $this->todelete = array();

        // Check we have (at least) one course_module
        if ($coursemodule = $DB->get_record('course_modules', array(), '*', IGNORE_MULTIPLE)) {
            $this->moduleid  = $coursemodule->id;
            $this->sectionid = $coursemodule->section;
            $this->courseid  = $coursemodule->course;
        }

        // Avoid any logger to be created, we'll restore original settings on tearDown()
        $this->errorlogloggerlevel = isset($CFG->backup_error_log_logger_level) ? $CFG->backup_error_log_logger_level : null;
        $this->fileloggerlevel = isset($CFG->backup_file_logger_level) ? $CFG->backup_file_logger_level : null;
        $this->databaseloggerlevel = isset($CFG->backup_database_logger_level) ? $CFG->backup_database_logger_level : null;
        $this->fileloggerlevelextra = isset($CFG->backup_file_logger_level_extra) ? $CFG->backup_file_logger_level_extra : null;

        parent::__construct();
    }

    function skip() {
        $this->skipIf(empty($this->moduleid), 'backup_step_test require at least one course module to exist');
        $this->skipIf(empty($this->sectionid),'backup_step_test require at least one course section to exist');
        $this->skipIf(empty($this->courseid), 'backup_step_test require at least one course to exist');
        $this->skipIf(empty($this->userid),'backup_step_test require one valid user to exist');
    }

    function setUp() {
        global $CFG;

        // Disable all loggers
        $CFG->backup_error_log_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level = backup::LOG_NONE;
        $CFG->backup_database_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level_extra = backup::LOG_NONE;
    }

    function tearDown() {
        global $DB, $CFG;
        // Delete all the records marked to
        foreach ($this->todelete as $todelete) {
            $DB->delete_records($todelete[0], array('id' => $todelete[1]));
        }
        // Restore original file_logger levels
        if ($this->errorlogloggerlevel !== null) {
            $CFG->backup_error_log_logger_level = $this->errorlogloggerlevel;
        } else {
            unset($CFG->backup_error_log_logger_level);
        }
        if ($this->fileloggerlevel !== null) {
            $CFG->backup_file_logger_level = $this->fileloggerlevel;
        } else {
            unset($CFG->backup_file_logger_level);
        }
        if ($this->databaseloggerlevel !== null) {
            $CFG->backup_database_logger_level = $this->databaseloggerlevel;
        } else {
            unset($CFG->backup_database_logger_level);
        }
        if ($this->fileloggerlevelextra !== null) {
            $CFG->backup_file_logger_level_extra = $this->fileloggerlevelextra;
        } else {
            unset($CFG->backup_file_logger_level_extra);
        }
    }

    /**
     * test base_step class
     */
    function test_base_step() {

        $bp = new mock_base_plan('planname'); // We need one plan
        $bt = new mock_base_task('taskname', $bp); // We need one task
        // Instantiate
        $bs = new mock_base_step('stepname', $bt);
        $this->assertTrue($bs instanceof base_step);
        $this->assertEqual($bs->get_name(), 'stepname');
    }

    /*
     * test backup_step class
     */
    function test_backup_step() {

        // We need one (non interactive) controller for instatiating plan
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
                                    backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        // We need one plan
        $bp = new backup_plan($bc);
        // We need one task
        $bt = new mock_backup_task('taskname', $bp);
        // Instantiate step
        $bs = new mock_backup_step('stepname', $bt);
        $this->assertTrue($bs instanceof backup_step);
        $this->assertEqual($bs->get_name(), 'stepname');

    }

    /**
     * test backup_structure_step class
     */
    function test_backup_structure_step() {
        global $CFG;

        $file = $CFG->dataroot . '/temp/test/test_backup_structure_step.txt';
        // Remove the test dir and any content
        @remove_dir(dirname($file));
        // Recreate test dir
        if (!check_dir_exists(dirname($file), true, true)) {
            throw new moodle_exception('error_creating_temp_dir', 'error', dirname($file));
        }

        // We need one (non interactive) controller for instatiating plan
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
                                    backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        // We need one plan
        $bp = new backup_plan($bc);
        // We need one task with mocked basepath
        $bt = new mock_backup_task_basepath('taskname');
        $bp->add_task($bt);
        // Instantiate backup_structure_step (and add it to task)
        $bs = new mock_backup_structure_step('steptest', basename($file), $bt);
        // Execute backup_structure_step
        $bs->execute();

        // Test file has been created
        $this->assertTrue(file_exists($file));

        // Some simple tests with contents
        $contents = file_get_contents($file);
        $this->assertTrue(strpos($contents, '<?xml version="1.0"') !== false);
        $this->assertTrue(strpos($contents, '<test id="1">') !== false);
        $this->assertTrue(strpos($contents, '<field1>value1</field1>') !== false);
        $this->assertTrue(strpos($contents, '<field2>value2</field2>') !== false);
        $this->assertTrue(strpos($contents, '</test>') !== false);

        unlink($file); // delete file

        // Remove the test dir and any content
        @remove_dir(dirname($file));
    }

    /**
     * wrong base_step class tests
     */
    function test_base_step_wrong() {

        // Try to pass one wrong task
        try {
            $bt = new mock_base_step('teststep', new stdclass());
            $this->assertTrue(false, 'base_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_step_exception);
            $this->assertEqual($e->errorcode, 'wrong_base_task_specified');
        }
    }

    /**
     * wrong backup_step class tests
     */
    function test_backup_test_wrong() {

        // Try to pass one wrong task
        try {
            $bt = new mock_backup_step('teststep', new stdclass());
            $this->assertTrue(false, 'backup_step_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_step_exception);
            $this->assertEqual($e->errorcode, 'wrong_backup_task_specified');
        }
    }
}

/**
 * Instantiable class extending base_step in order to be able to perform tests
 */
class mock_base_step extends base_step {
    public function execute() {
    }
}

/**
 * Instantiable class extending backup_step in order to be able to perform tests
 */
class mock_backup_step extends backup_step {
    public function execute() {
    }
}

/**
 * Instantiable class extending backup_task in order to mockup get_taskbasepath()
 */
class mock_backup_task_basepath extends backup_task {

    public function build() {
        // Nothing to do
    }

    public function define_settings() {
        // Nothing to do
    }

    public function get_taskbasepath() {
        global $CFG;
        return $CFG->dataroot . '/temp/test';
    }
}

/**
 * Instantiable class extending backup_structure_step in order to be able to perform tests
 */
class mock_backup_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Create really simple structure (1 nested with 1 attr and 2 fields)
        $test = new backup_nested_element('test',
                                          array('id'),
                                          array('field1', 'field2')
                                         );
        $test->set_source_array(array(array('id' => 1, 'field1' => 'value1', 'field2' => 'value2')));

        return $test;
    }
}

/**
 * Instantiable class extending activity_backup_setting to be added to task and perform tests
 */
class mock_fullpath_activity_setting extends activity_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // Nothing to do
    }
}

/**
 * Instantiable class extending activity_backup_setting to be added to task and perform tests
 */
class mock_backupid_activity_setting extends activity_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // Nothing to do
    }
}
