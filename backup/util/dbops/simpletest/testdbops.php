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
 * dbops tests (all)
 */
class backup_dbops_test extends UnitTestCase {

    public static $includecoverage = array('backup/util/dbops');
    public static $excludecoverage = array('backup/util/dbops/simpletest');

    protected $moduleid;  // course_modules id used for testing
    protected $sectionid; // course_sections id used for testing
    protected $courseid;  // course id used for testing
    protected $user;      // user record used for testing

    protected $todelete;  // array of records to be deleted after tests

    protected $errorlogloggerlevel; // To store $CFG->backup_error_log_logger_level
    protected $fileloggerlevel; // To store level $CFG->backup_file_logger_level
    protected $databaseloggerlevel; // To store $CFG->backup_database_logger_level
    protected $outputindentedloggerlevel; // To store $CFG->backup_output_indented_logger_level
    protected $fileloggerextra; // To store $CFG->backup_file_logger_extra
    protected $fileloggerlevelextra; // To store level $CFG->backup_file_logger_level_extra
    protected $debugging; // To store $CFG->debug
    protected $debugdisplay; // To store $CFG->debugdisplay

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
        parent::__construct();
    }

    function setUp() {
        global $CFG;
        parent::setUp();
        // Avoid any file logger to be created, we'll restore original settings on tearDown()
        // Fetch the rest of CFG variables to be able to restore them after tests
        // and normalize default values
        $this->errorlogloggerlevel = isset($CFG->backup_error_log_logger_level) ? $CFG->backup_error_log_logger_level : null;
        $CFG->backup_error_log_logger_level = backup::LOG_NONE;

        $this->outputindentedloggerlevel = isset($CFG->backup_output_indented_logger_level) ? $CFG->backup_output_indented_logger_level : null;
        $CFG->backup_output_indented_logger_level = backup::LOG_NONE;

        $this->fileloggerlevel = isset($CFG->backup_file_logger_level) ? $CFG->backup_file_logger_level : null;
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        $this->databaseloggerlevel = isset($CFG->backup_database_logger_level) ? $CFG->backup_database_logger_level : null;
        $CFG->backup_database_logger_level = backup::LOG_NONE;

        $this->fileloggerextra = isset($CFG->backup_file_logger_extra) ? $CFG->backup_file_logger_extra : null;
        unset($CFG->backup_file_logger_extra);
        $this->fileloggerlevelextra = isset($CFG->backup_file_logger_level_extra) ? $CFG->backup_file_logger_level_extra : null;
        $CFG->backup_file_logger_level_extra = backup::LOG_NONE;

        $this->debugging = isset($CFG->debug) ? $CFG->debug : null;
        $this->debugdisplay = isset($CFG->debugdisplay) ? $CFG->debugdisplay : null;
    }

    function skip() {
        $this->skipIf(empty($this->moduleid), 'backup_dbops_test require at least one course module to exist');
        $this->skipIf(empty($this->sectionid),'backup_dbops_test require at least one course section to exist');
        $this->skipIf(empty($this->courseid), 'backup_dbops_test require at least one course to exist');
        $this->skipIf(empty($this->userid),'backup_dbops_test require one valid user to exist');
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

        if ($this->fileloggerextra !== null) {
            $CFG->backup_file_logger_extra = $this->fileloggerextra;
        } else {
            unset($CFG->backup_file_logger_extra);
        }
        if ($this->fileloggerlevelextra !== null) {
            $CFG->backup_file_logger_level_extra = $this->fileloggerlevelextra;
        } else {
            unset($CFG->backup_file_logger_level_extra);
        }
        // Restore the rest of $CFG settings
        if ($this->debugging !== null) {
            $CFG->debug = $this->debugging;
        } else {
            unset($CFG->debug);
        }
        if ($this->debugdisplay !== null) {
            $CFG->debugdisplay = $this->debugdisplay;
        } else {
            unset($CFG->debugdisplay);
        }
        parent::tearDown();
    }

    /*
     * test backup_ops class
     */
    function test_backup_dbops() {
        // Nothing to do here, abstract class + exception, will be tested by the rest
    }

    /*
     * test backup_controller_dbops class
     */
    function test_backup_controller_dbops() {
        global $DB;

        $dbman = $DB->get_manager(); // Going to use some database_manager services for testing

        // Instantiate non interactive backup_controller
        $bc = new mock_backup_controller4dbops(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
                                    backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        $this->assertTrue($bc instanceof backup_controller);
        // Calculate checksum
        $checksum = $bc->calculate_checksum();
        $this->assertEqual(strlen($checksum), 32); // is one md5

        // save controller
        $recid = backup_controller_dbops::save_controller($bc, $checksum);
        $this->assertTrue($recid);
        $this->todelete[] = array('backup_controllers', $recid); // mark this record for deletion
        // save it again (should cause update to happen)
        $recid2 = backup_controller_dbops::save_controller($bc, $checksum);
        $this->assertTrue($recid2);
        $this->todelete[] = array('backup_controllers', $recid2); // mark this record for deletion
        $this->assertEqual($recid, $recid2); // Same record in both save operations

        // Try incorrect checksum
        $bc = new mock_backup_controller4dbops(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
                                    backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        $checksum = $bc->calculate_checksum();
        try {
            $recid = backup_controller_dbops::save_controller($bc, 'lalala');
            $this->assertTrue(false, 'backup_dbops_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_dbops_exception);
            $this->assertEqual($e->errorcode, 'backup_controller_dbops_saving_checksum_mismatch');
        }

        // Try to save non backup_controller object
        $bc = new stdclass();
        try {
            $recid = backup_controller_dbops::save_controller($bc, 'lalala');
            $this->assertTrue(false, 'backup_controller_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_controller_exception);
            $this->assertEqual($e->errorcode, 'backup_controller_expected');
        }

        // save and load controller (by backupid). Then compare
        $bc = new mock_backup_controller4dbops(backup::TYPE_1ACTIVITY, $this->moduleid, backup::FORMAT_MOODLE,
                                    backup::INTERACTIVE_NO, backup::MODE_GENERAL, $this->userid);
        $checksum = $bc->calculate_checksum(); // Calculate checksum
        $backupid = $bc->get_backupid();
        $this->assertEqual(strlen($backupid), 32); // is one md5
        $recid = backup_controller_dbops::save_controller($bc, $checksum); // save controller
        $this->todelete[] = array('backup_controllers', $recid); // mark this record for deletion
        $newbc = backup_controller_dbops::load_controller($backupid); // load controller
        $this->assertTrue($newbc instanceof backup_controller);
        $newchecksum = $newbc->calculate_checksum();
        $this->assertEqual($newchecksum, $checksum);

        // try to load non-existing controller
        try {
            $bc = backup_controller_dbops::load_controller('1234567890');
            $this->assertTrue(false, 'backup_dbops_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_dbops_exception);
            $this->assertEqual($e->errorcode, 'backup_controller_dbops_nonexisting');
        }

        // backup_ids_temp table tests
        // If, for any reason table exists, drop it
        if ($dbman->table_exists('backup_ids_temp')) {
            $dbman->drop_temp_table(new xmldb_table('backup_ids_temp'));
        }
        // Check backup_ids_temp table doesn't exist
        $this->assertFalse($dbman->table_exists('backup_ids_temp'));
        // Create and check it exists
        backup_controller_dbops::create_backup_ids_temp_table('testingid');
        $this->assertTrue($dbman->table_exists('backup_ids_temp'));
        // Drop and check it doesn't exists anymore
        backup_controller_dbops::drop_backup_ids_temp_table('testingid');
        $this->assertFalse($dbman->table_exists('backup_ids_temp'));
    }
}

class mock_backup_controller4dbops extends backup_controller {

    /**
     * Change standard behavior so the checksum is also stored and not onlt calculated
     */
    public function calculate_checksum() {
        $this->checksum = parent::calculate_checksum();
        return $this->checksum;
    }
}
