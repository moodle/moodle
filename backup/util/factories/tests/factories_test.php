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

namespace core_backup;

use backup;
use backup_factory;
use database_logger;
use error_log_logger;
use file_logger;
use output_indented_logger;

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff
global $CFG;
require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php');
require_once($CFG->dirroot . '/backup/backup.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/base_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/error_log_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/output_indented_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/database_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/file_logger.class.php');
require_once($CFG->dirroot . '/backup/util/factories/backup_factory.class.php');

/**
 * @package    core_backup
 * @category   test
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factories_test extends \advanced_testcase {

    public function setUp(): void {
        global $CFG;
        parent::setUp();

        $this->resetAfterTest(true);

        $CFG->backup_error_log_logger_level = backup::LOG_NONE;
        $CFG->backup_output_indented_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level = backup::LOG_NONE;
        $CFG->backup_database_logger_level = backup::LOG_NONE;
        unset($CFG->backup_file_logger_extra);
        $CFG->backup_file_logger_level_extra = backup::LOG_NONE;
    }

    /**
     * test get_logger_chain() method
     */
    public function test_backup_factory(): void {
        global $CFG;

        // Default instantiate, all levels = backup::LOG_NONE
        // With debugdisplay enabled
        $CFG->debugdisplay = true;
        $logger1 = backup_factory::get_logger_chain(backup::INTERACTIVE_YES, backup::EXECUTION_INMEDIATE, 'test');
        $this->assertTrue($logger1 instanceof error_log_logger);  // 1st logger is error_log_logger
        $this->assertEquals($logger1->get_level(), backup::LOG_NONE);
        $logger2 = $logger1->get_next();
        $this->assertTrue($logger2 instanceof output_indented_logger);  // 2nd logger is output_indented_logger
        $this->assertEquals($logger2->get_level(), backup::LOG_NONE);
        $logger3 = $logger2->get_next();
        $this->assertTrue($logger3 instanceof file_logger);  // 3rd logger is file_logger
        $this->assertEquals($logger3->get_level(), backup::LOG_NONE);
        $logger4 = $logger3->get_next();
        $this->assertTrue($logger4 instanceof database_logger);  // 4th logger is database_logger
        $this->assertEquals($logger4->get_level(), backup::LOG_NONE);
        $logger5 = $logger4->get_next();
        $this->assertTrue($logger5 === null);

        // With debugdisplay disabled
        $CFG->debugdisplay = false;
        $logger1 = backup_factory::get_logger_chain(backup::INTERACTIVE_YES, backup::EXECUTION_INMEDIATE, 'test');
        $this->assertTrue($logger1 instanceof error_log_logger);  // 1st logger is error_log_logger
        $this->assertEquals($logger1->get_level(), backup::LOG_NONE);
        $logger2 = $logger1->get_next();
        $this->assertTrue($logger2 instanceof file_logger);  // 2nd logger is file_logger
        $this->assertEquals($logger2->get_level(), backup::LOG_NONE);
        $logger3 = $logger2->get_next();
        $this->assertTrue($logger3 instanceof database_logger);  // 3rd logger is database_logger
        $this->assertEquals($logger3->get_level(), backup::LOG_NONE);
        $logger4 = $logger3->get_next();
        $this->assertTrue($logger4 === null);

        // Instantiate with debugging enabled and $CFG->backup_error_log_logger_level not set
        $CFG->debugdisplay = true;
        unset($CFG->backup_error_log_logger_level);
        $logger1 = backup_factory::get_logger_chain(backup::INTERACTIVE_YES, backup::EXECUTION_INMEDIATE, 'test');
        $this->assertTrue($logger1 instanceof error_log_logger);  // 1st logger is error_log_logger
        $this->assertEquals($logger1->get_level(), backup::LOG_DEBUG); // and must have backup::LOG_DEBUG level
        // Set $CFG->backup_error_log_logger_level to backup::LOG_WARNING and test again
        $CFG->backup_error_log_logger_level = backup::LOG_WARNING;
        $logger1 = backup_factory::get_logger_chain(backup::INTERACTIVE_YES, backup::EXECUTION_INMEDIATE, 'test');
        $this->assertTrue($logger1 instanceof error_log_logger);  // 1st logger is error_log_logger
        $this->assertEquals($logger1->get_level(), backup::LOG_WARNING); // and must have backup::LOG_WARNING level

        // Instantiate in non-interactive mode, output_indented_logger must be out
        $logger1 = backup_factory::get_logger_chain(backup::INTERACTIVE_NO, backup::EXECUTION_INMEDIATE, 'test');
        $logger2 = $logger1->get_next();
        $this->assertTrue($logger2 instanceof file_logger);  // 2nd logger is file_logger (output_indented_logger skiped)

        // Define extra file logger and instantiate, should be 5th and last logger
        $CFG->backup_file_logger_extra = $CFG->tempdir.'/test.html';
        $CFG->backup_file_logger_level_extra = backup::LOG_NONE;
        $logger1 = backup_factory::get_logger_chain(backup::INTERACTIVE_YES, backup::EXECUTION_INMEDIATE, 'test');
        $logger2 = $logger1->get_next();
        $logger3 = $logger2->get_next();
        $logger4 = $logger3->get_next();
        $logger5 = $logger4->get_next();
        $this->assertTrue($logger5 instanceof file_logger);  // 5rd logger is file_logger (extra)
        $this->assertEquals($logger3->get_level(), backup::LOG_NONE);
        $logger6 = $logger5->get_next();
        $this->assertTrue($logger6 === null);
    }
}
