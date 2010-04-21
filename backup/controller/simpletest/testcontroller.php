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
 * controller tests (all)
 */
class backup_controller_test extends UnitTestCase {

    public static $includecoverage = array('backup/controller');
    public static $excludecoverage = array('backup/controller/simpletest');

    protected $moduleid;  // course_modules id used for testing
    protected $sectionid; // course_sections id used for testing
    protected $courseid;  // course id used for testing
    protected $user;      // user record used for testing

    protected $todelete;  // array of records to be deleted after tests
    protected $errorlogloggerlevel; // To store level $CFG->backup_error_log_logger_level
    protected $outputindentedloggerlevel; // To store level $CFG->backup_output_indented_logger_level
    protected $fileloggerlevel;     // To store level $CFG->backup_file_logger_level
    protected $databaseloggerlevel; // To store level $CFG->backup_database_logger_level
    protected $fileloggerlevelextra;// To store level $CFG->backup_file_logger_level_extra

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
        $this->outputindentedloggerlevel = isset($CFG->backup_output_indented_logger_level) ? $CFG->backup_output_indented_logger_level : null;
        $this->fileloggerlevel = isset($CFG->backup_file_logger_level) ? $CFG->backup_file_logger_level : null;
        $this->databaseloggerlevel = isset($CFG->backup_database_logger_level) ? $CFG->backup_database_logger_level : null;
        $this->fileloggerlevelextra = isset($CFG->backup_file_logger_level_extra) ? $CFG->backup_file_logger_level_extra : null;

        parent::__construct();
    }

    function skip() {
        $this->skipIf(empty($this->moduleid), 'backup_controller_test require at least one course module to exist');
        $this->skipIf(empty($this->sectionid),'backup_controller_test require at least one course section to exist');
        $this->skipIf(empty($this->courseid), 'backup_controller_test require at least one course to exist');
        $this->skipIf(empty($this->userid),'backup_controller_test require one valid user to exist');
    }

    function setUp() {
        global $CFG;

        // Disable all loggers
        $CFG->backup_error_log_logger_level = backup::LOG_NONE;
        $CFG->backup_output_indented_logger_level = backup::LOG_NONE;
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
        if ($this->outputindentedloggerlevel !== null) {
            $CFG->backup_output_indented_logger_level = $this->outputindentedloggerlevel;
        } else {
            unset($CFG->backup_output_indented_logger_level);
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

    /*
     * test base_setting class
     */
    function test_backup_controller() {

        // Instantiate non interactive backup_controller
        $bc = new mock_backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
                                         backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        $this->assertTrue($bc instanceof backup_controller);
        $this->assertEqual($bc->get_status(), backup::STATUS_AWAITING);
        // Instantiate interactive backup_controller
        $bc = new mock_backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
                                         backup::INTERACTIVE_YES, backup::MODE_GENERAL, $this->userid);
        $this->assertTrue($bc instanceof backup_controller);
        $this->assertEqual($bc->get_status(), backup::STATUS_SETTING_UI);
        $this->assertEqual(strlen($bc->get_backupid()), 32); // is one md5

        // Save and load one backup controller to check everything is in place
        $bc = new mock_backup_controller(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
                                         backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        $recid = $bc->save_controller();
        $newbc = mock_backup_controller::load_controller($bc->get_backupid());
        $this->assertTrue($newbc instanceof backup_controller); // This means checksum and load worked ok

        $this->todelete[] = array('backup_controllers', $recid); // mark this record for deletion
    }
}

/*
 * helper extended @backup_controller class that makes some methods public for testing
 */
class mock_backup_controller extends backup_controller {

    public function save_controller() {
        parent::save_controller();
    }
}
