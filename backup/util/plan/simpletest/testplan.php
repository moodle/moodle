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
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

/*
 * plan tests (all)
 */
class backup_plan_test extends UnitTestCase {

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
        $this->skipIf(empty($this->moduleid), 'backup_plan_test require at least one course module to exist');
        $this->skipIf(empty($this->sectionid),'backup_plan_test require at least one course section to exist');
        $this->skipIf(empty($this->courseid), 'backup_plan_test require at least one course to exist');
        $this->skipIf(empty($this->userid),'backup_plan_test require one valid user to exist');
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
     * test base_plan class
     */
    function test_base_plan() {

        // Instantiate
        $bp = new mock_base_plan('name');
        $this->assertTrue($bp instanceof base_plan);
        $this->assertEqual($bp->get_name(), 'name');
        $this->assertTrue(is_array($bp->get_settings()));
        $this->assertEqual(count($bp->get_settings()), 0);
        $this->assertTrue(is_array($bp->get_tasks()));
        $this->assertEqual(count($bp->get_tasks()), 0);
    }

    /*
     * test backup_plan class
     */
    function test_backup_plan() {

        // We need one (non interactive) controller for instatiating plan
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
                                    backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        // Instantiate one backup plan
        $bp = new backup_plan($bc);
        $this->assertTrue($bp instanceof backup_plan);
        $this->assertEqual($bp->get_name(), 'backup_plan');

        // Calculate checksum and check it
        $checksum = $bp->calculate_checksum();
        $this->assertTrue($bp->is_checksum_correct($checksum));
    }

    /**
     * wrong base_plan class tests
     */
    function test_base_plan_wrong() {

        // We need one (non interactive) controller for instatiating plan
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
            $this->assertEqual($e->errorcode, 'wrong_base_task_specified');
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
            $this->assertEqual($e->errorcode, 'wrong_backup_controller_specified');
        }
        try {
            $bp = new backup_plan(null);
            $this->assertTrue(false, 'backup_plan_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_plan_exception);
            $this->assertEqual($e->errorcode, 'wrong_backup_controller_specified');
        }

        // Try to build one non-existent format plan (when creating the controller)
        // We need one (non interactive) controller for instatiating plan
        try {
            $bc = new backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, 'non_existing_format',
                                        backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
            $this->assertTrue(false, 'backup_controller_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_controller_exception);
            $this->assertEqual($e->errorcode, 'backup_check_unsupported_format');
            $this->assertEqual($e->a, 'non_existing_format');
        }
    }
}

/**
 * Instantiable class extending base_plan in order to be able to perform tests
 */
class mock_base_plan extends base_plan {
    public function build() {
    }
}
