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
require_once($CFG->dirroot . '/backup/backup.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/base_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/error_log_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/output_indented_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/database_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/file_logger.class.php');
require_once($CFG->dirroot . '/backup/util/factories/backup_factory.class.php');

/*
 * backup_factory tests (all)
 */
class backup_factories_test extends UnitTestCase {

    public static $includecoverage = array('backup/util/factories');
    public static $excludecoverage = array('backup/util/factories/simpletest');

    protected $errorlogloggerlevel; // To store $CFG->backup_error_log_logger_level
    protected $fileloggerlevel; // To store level $CFG->backup_file_logger_level
    protected $databaseloggerlevel; // To store $CFG->backup_database_logger_level
    protected $outputindentedloggerlevel; // To store $CFG->backup_output_indented_logger_level
    protected $fileloggerextra; // To store $CFG->backup_file_logger_extra
    protected $fileloggerlevelextra; // To store level $CFG->backup_file_logger_level_extra
    protected $debugging; // To store $CFG->debug
    protected $debugdisplay; // To store $CFG->debugdisplay

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

    function tearDown() {
        global $CFG;
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
     * test get_logger_chain() method
     */
    function test_backup_factory() {
        global $CFG;

        // Default instantiate, all levels = backup::LOG_NONE
        // With debugdisplay enabled
        $CFG->debugdisplay = true;
        $logger1 = backup_factory::get_logger_chain(backup::INTERACTIVE_YES, backup::EXECUTION_INMEDIATE, 'test');
        $this->assertTrue($logger1 instanceof error_log_logger);  // 1st logger is error_log_logger
        $this->assertEqual($logger1->get_level(), backup::LOG_NONE);
        $logger2 = $logger1->get_next();
        $this->assertTrue($logger2 instanceof output_indented_logger);  // 2nd logger is output_indented_logger
        $this->assertEqual($logger2->get_level(), backup::LOG_NONE);
        $logger3 = $logger2->get_next();
        $this->assertTrue($logger3 instanceof file_logger);  // 3rd logger is file_logger
        $this->assertEqual($logger3->get_level(), backup::LOG_NONE);
        $logger4 = $logger3->get_next();
        $this->assertTrue($logger4 instanceof database_logger);  // 4th logger is database_logger
        $this->assertEqual($logger4->get_level(), backup::LOG_NONE);
        $logger5 = $logger4->get_next();
        $this->assertTrue($logger5 === null);

        // With debugdisplay disabled
        $CFG->debugdisplay = false;
        $logger1 = backup_factory::get_logger_chain(backup::INTERACTIVE_YES, backup::EXECUTION_INMEDIATE, 'test');
        $this->assertTrue($logger1 instanceof error_log_logger);  // 1st logger is error_log_logger
        $this->assertEqual($logger1->get_level(), backup::LOG_NONE);
        $logger2 = $logger1->get_next();
        $this->assertTrue($logger2 instanceof file_logger);  // 2nd logger is file_logger
        $this->assertEqual($logger2->get_level(), backup::LOG_NONE);
        $logger3 = $logger2->get_next();
        $this->assertTrue($logger3 instanceof database_logger);  // 3rd logger is database_logger
        $this->assertEqual($logger3->get_level(), backup::LOG_NONE);
        $logger4 = $logger3->get_next();
        $this->assertTrue($logger4 === null);

        // Instantiate with debugging enabled and $CFG->backup_error_log_logger_level not set
        $CFG->debugdisplay = true;
        $CFG->debug = DEBUG_DEVELOPER;
        unset($CFG->backup_error_log_logger_level);
        $logger1 = backup_factory::get_logger_chain(backup::INTERACTIVE_YES, backup::EXECUTION_INMEDIATE, 'test');
        $this->assertTrue($logger1 instanceof error_log_logger);  // 1st logger is error_log_logger
        $this->assertEqual($logger1->get_level(), backup::LOG_DEBUG); // and must have backup::LOG_DEBUG level
        // Set $CFG->backup_error_log_logger_level to backup::LOG_WARNING and test again
        $CFG->backup_error_log_logger_level = backup::LOG_WARNING;
        $logger1 = backup_factory::get_logger_chain(backup::INTERACTIVE_YES, backup::EXECUTION_INMEDIATE, 'test');
        $this->assertTrue($logger1 instanceof error_log_logger);  // 1st logger is error_log_logger
        $this->assertEqual($logger1->get_level(), backup::LOG_WARNING); // and must have backup::LOG_WARNING level

        // Instantiate in non-interactive mode, output_indented_logger must be out
        $logger1 = backup_factory::get_logger_chain(backup::INTERACTIVE_NO, backup::EXECUTION_INMEDIATE, 'test');
        $logger2 = $logger1->get_next();
        $this->assertTrue($logger2 instanceof file_logger);  // 2nd logger is file_logger (output_indented_logger skiped)

        // Define extra file logger and instantiate, should be 5th and last logger
        $CFG->backup_file_logger_extra = '/tmp/test.html';
        $CFG->backup_file_logger_level_extra = backup::LOG_NONE;
        $logger1 = backup_factory::get_logger_chain(backup::INTERACTIVE_YES, backup::EXECUTION_INMEDIATE, 'test');
        $logger2 = $logger1->get_next();
        $logger3 = $logger2->get_next();
        $logger4 = $logger3->get_next();
        $logger5 = $logger4->get_next();
        $this->assertTrue($logger5 instanceof file_logger);  // 5rd logger is file_logger (extra)
        $this->assertEqual($logger3->get_level(), backup::LOG_NONE);
        $logger6 = $logger5->get_next();
        $this->assertTrue($logger6 === null);
    }
}
