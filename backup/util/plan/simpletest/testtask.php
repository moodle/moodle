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
require_once($CFG->dirroot . '/backup/util/interfaces/executable.class.php');
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

/*
 * task tests (all)
 */
class backup_task_test extends UnitTestCase {

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
        $this->skipIf(empty($this->moduleid), 'backup_task_test require at least one course module to exist');
        $this->skipIf(empty($this->sectionid),'backup_task_test require at least one course section to exist');
        $this->skipIf(empty($this->courseid), 'backup_task_test require at least one course to exist');
        $this->skipIf(empty($this->userid),'backup_task_test require one valid user to exist');
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
     * test base_task class
     */
    function test_base_task() {

        $bp = new mock_base_plan('planname'); // We need one plan
        // Instantiate
        $bt = new mock_base_task('taskname', $bp);
        $this->assertTrue($bt instanceof base_task);
        $this->assertEqual($bt->get_name(), 'taskname');
        $this->assertTrue(is_array($bt->get_settings()));
        $this->assertEqual(count($bt->get_settings()), 0);
        $this->assertTrue(is_array($bt->get_steps()));
        $this->assertEqual(count($bt->get_steps()), 0);
    }

    /*
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
        $this->assertEqual($bt->get_name(), 'taskname');

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
            $this->assertEqual($e->errorcode, 'wrong_base_plan_specified');
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
            $this->assertEqual($e->errorcode, 'wrong_base_step_specified');
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
            $this->assertEqual($e->errorcode, 'wrong_backup_plan_specified');
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
