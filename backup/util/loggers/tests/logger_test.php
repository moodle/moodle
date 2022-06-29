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
 * Logger tests (all).
 * @package    core_backup
 * @category   test
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_backup;

use backup;
use base_logger;
use base_logger_exception;
use database_logger;
use error_log_logger;
use file_logger;
use output_indented_logger;
use output_text_logger;

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff
global $CFG;
require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php');
require_once($CFG->dirroot . '/backup/backup.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/base_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/error_log_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/output_text_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/output_indented_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/database_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/file_logger.class.php');


/**
 * Logger tests (all).
 *
 * @package    core_backup
 * @category   test
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class logger_test extends \basic_testcase {

    /**
     * test base_logger class
     */
    function test_base_logger() {
        // Test logger with simple action (message * level)
        $lo = new mock_base_logger1(backup::LOG_ERROR);
        $msg = 13;
        $this->assertEquals($lo->process($msg, backup::LOG_ERROR), $msg * backup::LOG_ERROR);
        // With lowest level must return true
        $lo = new mock_base_logger1(backup::LOG_ERROR);
        $msg = 13;
        $this->assertTrue($lo->process($msg, backup::LOG_DEBUG));

        // Chain 2 loggers, we must get as result the result of the inner one
        $lo1 = new mock_base_logger1(backup::LOG_ERROR);
        $lo2 = new mock_base_logger2(backup::LOG_ERROR);
        $lo1->set_next($lo2);
        $msg = 13;
        $this->assertEquals($lo1->process($msg, backup::LOG_ERROR), $msg + backup::LOG_ERROR);

        // Try circular reference
        $lo1 = new mock_base_logger1(backup::LOG_ERROR);
        try {
            $lo1->set_next($lo1); //self
            $this->assertTrue(false, 'base_logger_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_logger_exception);
            $this->assertEquals($e->errorcode, 'logger_circular_reference');
            $this->assertTrue($e->a instanceof \stdClass);
            $this->assertEquals($e->a->main, get_class($lo1));
            $this->assertEquals($e->a->alreadyinchain, get_class($lo1));
        }

        $lo1 = new mock_base_logger1(backup::LOG_ERROR);
        $lo2 = new mock_base_logger2(backup::LOG_ERROR);
        $lo3 = new mock_base_logger3(backup::LOG_ERROR);
        $lo1->set_next($lo2);
        $lo2->set_next($lo3);
        try {
            $lo3->set_next($lo1);
            $this->assertTrue(false, 'base_logger_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_logger_exception);
            $this->assertEquals($e->errorcode, 'logger_circular_reference');
            $this->assertTrue($e->a instanceof \stdClass);
            $this->assertEquals($e->a->main, get_class($lo1));
            $this->assertEquals($e->a->alreadyinchain, get_class($lo3));
        }

        // Test stopper logger
        $lo1 = new mock_base_logger1(backup::LOG_ERROR);
        $lo2 = new mock_base_logger2(backup::LOG_ERROR);
        $lo3 = new mock_base_logger3(backup::LOG_ERROR);
        $lo1->set_next($lo2);
        $lo2->set_next($lo3);
        $msg = 13;
        $this->assertFalse($lo1->process($msg, backup::LOG_ERROR));

        // Test checksum correct
        $lo1 = new mock_base_logger1(backup::LOG_ERROR);
        $lo1->is_checksum_correct(get_class($lo1) . '-' . backup::LOG_ERROR);

        // Test get_levelstr()
        $lo1 = new mock_base_logger1(backup::LOG_ERROR);
        $this->assertEquals($lo1->get_levelstr(backup::LOG_NONE), 'undefined');
        $this->assertEquals($lo1->get_levelstr(backup::LOG_ERROR), 'error');
        $this->assertEquals($lo1->get_levelstr(backup::LOG_WARNING), 'warn');
        $this->assertEquals($lo1->get_levelstr(backup::LOG_INFO), 'info');
        $this->assertEquals($lo1->get_levelstr(backup::LOG_DEBUG), 'debug');

        // Test destroy.
        $lo1 = new mock_base_logger1(backup::LOG_ERROR);
        $lo2 = new mock_base_logger2(backup::LOG_ERROR);
        $lo1->set_next($lo2);
        $this->assertInstanceOf('base_logger', $lo1->get_next());
        $this->assertNull($lo2->get_next());
        $lo1->destroy();
        $this->assertNull($lo1->get_next());
        $this->assertNull($lo2->get_next());
    }

    /**
     * test error_log_logger class
     */
    function test_error_log_logger() {
        // Not much really to test, just instantiate and execute, should return true
        $lo = new error_log_logger(backup::LOG_ERROR);
        $this->assertTrue($lo instanceof error_log_logger);
        $message = 'This log exists because you have run Moodle unit tests: Ignore it';
        $result = $lo->process($message, backup::LOG_ERROR);
        $this->assertTrue($result);
    }

    /**
     * test output_text_logger class
     */
    function test_output_text_logger() {
        // Instantiate without date nor level output
        $lo = new output_text_logger(backup::LOG_ERROR);
        $this->assertTrue($lo instanceof output_text_logger);
        $message = 'testing output_text_logger';
        ob_start(); // Capture output
        $result = $lo->process($message, backup::LOG_ERROR);
        $contents = ob_get_contents();
        ob_end_clean(); // End capture and discard
        $this->assertTrue($result);
        $this->assertTrue(strpos($contents, $message) !== false);

        // Instantiate with date and level output
        $lo = new output_text_logger(backup::LOG_ERROR, true, true);
        $this->assertTrue($lo instanceof output_text_logger);
        $message = 'testing output_text_logger';
        ob_start(); // Capture output
        $result = $lo->process($message, backup::LOG_ERROR);
        $contents = ob_get_contents();
        ob_end_clean(); // End capture and discard
        $this->assertTrue($result);
        $this->assertTrue(strpos($contents,'[') === 0);
        $this->assertTrue(strpos($contents,'[error]') !== false);
        $this->assertTrue(strpos($contents, $message) !== false);
        $this->assertTrue(substr_count($contents , '] ') >= 2);
    }

    /**
     * test output_indented_logger class
     */
    function test_output_indented_logger() {
        // Instantiate without date nor level output
        $options = array('depth' => 2);
        $lo = new output_indented_logger(backup::LOG_ERROR);
        $this->assertTrue($lo instanceof output_indented_logger);
        $message = 'testing output_indented_logger';
        ob_start(); // Capture output
        $result = $lo->process($message, backup::LOG_ERROR, $options);
        $contents = ob_get_contents();
        ob_end_clean(); // End capture and discard
        $this->assertTrue($result);
        if (defined('STDOUT')) {
            $check = '  ';
        } else {
            $check = '&nbsp;&nbsp;';
        }
        $this->assertTrue(strpos($contents, str_repeat($check, $options['depth']) . $message) !== false);

        // Instantiate with date and level output
        $options = array('depth' => 3);
        $lo = new output_indented_logger(backup::LOG_ERROR, true, true);
        $this->assertTrue($lo instanceof output_indented_logger);
        $message = 'testing output_indented_logger';
        ob_start(); // Capture output
        $result = $lo->process($message, backup::LOG_ERROR, $options);
        $contents = ob_get_contents();
        ob_end_clean(); // End capture and discard
        $this->assertTrue($result);
        $this->assertTrue(strpos($contents,'[') === 0);
        $this->assertTrue(strpos($contents,'[error]') !== false);
        $this->assertTrue(strpos($contents, $message) !== false);
        $this->assertTrue(substr_count($contents , '] ') >= 2);
        if (defined('STDOUT')) {
            $check = '  ';
        } else {
            $check = '&nbsp;&nbsp;';
        }
        $this->assertTrue(strpos($contents, str_repeat($check, $options['depth']) . $message) !== false);
    }

    /**
     * test database_logger class
     */
    function test_database_logger() {
        // Instantiate with date and level output (and with specs from the global moodle "log" table so checks will pass
        $now = time();
        $datecol = 'time';
        $levelcol = 'action';
        $messagecol = 'info';
        $logtable = 'log';
        $columns = array('url' => 'http://127.0.0.1');
        $loglevel = backup::LOG_ERROR;
        $lo = new mock_database_logger(backup::LOG_ERROR, $datecol, $levelcol, $messagecol, $logtable, $columns);
        $this->assertTrue($lo instanceof database_logger);
        $message = 'testing database_logger';
        $result = $lo->process($message, $loglevel);
        // Check everything is ready to be inserted to DB
        $this->assertEquals($result['table'], $logtable);
        $this->assertTrue($result['columns'][$datecol] >= $now);
        $this->assertEquals($result['columns'][$levelcol], $loglevel);
        $this->assertEquals($result['columns'][$messagecol], $message);
        $this->assertEquals($result['columns']['url'], $columns['url']);
    }

    /**
     * test file_logger class
     */
    function test_file_logger() {
        global $CFG;

        $file = $CFG->tempdir . '/test/test_file_logger.txt';
        // Remove the test dir and any content
        @remove_dir(dirname($file));
        // Recreate test dir
        if (!check_dir_exists(dirname($file), true, true)) {
            throw new \moodle_exception('error_creating_temp_dir', 'error', dirname($file));
        }

        // Instantiate with date and level output, and also use the depth option
        $options = array('depth' => 3);
        $lo1 = new file_logger(backup::LOG_ERROR, true, true, $file);
        $this->assertTrue($lo1 instanceof file_logger);
        $message1 = 'testing file_logger';
        $result = $lo1->process($message1, backup::LOG_ERROR, $options);
        $this->assertTrue($result);

        // Another file_logger is going towrite there too without closing
        $options = array();
        $lo2 = new file_logger(backup::LOG_WARNING, true, true, $file);
        $this->assertTrue($lo2 instanceof file_logger);
        $message2 = 'testing file_logger2';
        $result = $lo2->process($message2, backup::LOG_WARNING, $options);
        $this->assertTrue($result);

        // Destroy loggers.
        $lo1->destroy();
        $lo2->destroy();

        // Load file results to analyze them
        $fcontents = file_get_contents($file);
        $acontents = explode(PHP_EOL, $fcontents); // Split by line
        $this->assertTrue(strpos($acontents[0], $message1) !== false);
        $this->assertTrue(strpos($acontents[0], '[error]') !== false);
        $this->assertTrue(strpos($acontents[0], '      ') !== false);
        $this->assertTrue(substr_count($acontents[0] , '] ') >= 2);
        $this->assertTrue(strpos($acontents[1], $message2) !== false);
        $this->assertTrue(strpos($acontents[1], '[warn]') !== false);
        $this->assertTrue(strpos($acontents[1], '      ') === false);
        $this->assertTrue(substr_count($acontents[1] , '] ') >= 2);
        unlink($file); // delete file

        // Try one html file
        check_dir_exists($CFG->tempdir . '/test');
        $file = $CFG->tempdir . '/test/test_file_logger.html';
        $options = array('depth' => 1);
        $lo = new file_logger(backup::LOG_ERROR, true, true, $file);
        $this->assertTrue($lo instanceof file_logger);
        $this->assertTrue(file_exists($file));
        $message = 'testing file_logger';
        $result = $lo->process($message, backup::LOG_ERROR, $options);
        $lo->close(); // Closes logger.
        // Get file contents and inspect them
        $fcontents = file_get_contents($file);
        $this->assertTrue($result);
        $this->assertTrue(strpos($fcontents, $message) !== false);
        $this->assertTrue(strpos($fcontents, '[error]') !== false);
        $this->assertTrue(strpos($fcontents, '&nbsp;&nbsp;') !== false);
        $this->assertTrue(substr_count($fcontents , '] ') >= 2);
        unlink($file); // delete file

        // Instantiate, write something, force deletion, try to write again
        check_dir_exists($CFG->tempdir . '/test');
        $file = $CFG->tempdir . '/test/test_file_logger.html';
        $lo = new mock_file_logger(backup::LOG_ERROR, true, true, $file);
        $this->assertTrue(file_exists($file));
        $message = 'testing file_logger';
        $result = $lo->process($message, backup::LOG_ERROR);
        $lo->close();
        $this->assertNull($lo->get_fhandle());
        try {
            $result = @$lo->process($message, backup::LOG_ERROR); // Try to write again
            $this->assertTrue(false, 'base_logger_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_logger_exception);
            $this->assertEquals($e->errorcode, 'error_writing_file');
        }

        // Instantiate without file
        try {
            $lo = new file_logger(backup::LOG_WARNING, true, true, '');
            $this->assertTrue(false, 'base_logger_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_logger_exception);
            $this->assertEquals($e->errorcode, 'missing_fullpath_parameter');
        }

        // Instantiate in (near) impossible path
        $file =  $CFG->tempdir . '/test_azby/test_file_logger.txt';
        try {
            $lo = new file_logger(backup::LOG_WARNING, true, true, $file);
            $this->assertTrue(false, 'base_logger_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_logger_exception);
            $this->assertEquals($e->errorcode, 'file_not_writable');
            $this->assertEquals($e->a, $file);
        }

        // Instantiate one file logger with level = backup::LOG_NONE
        $file =  $CFG->tempdir . '/test/test_file_logger.txt';
        $lo = new file_logger(backup::LOG_NONE, true, true, $file);
        $this->assertTrue($lo instanceof file_logger);
        $this->assertFalse(file_exists($file));
        $lo->close();

        // Remove the test dir and any content
        @remove_dir(dirname($file));
    }
}


/**
 * helper extended base_logger class that implements some methods for testing
 * Simply return the product of message and level
 */
class mock_base_logger1 extends base_logger {

    protected function action($message, $level, $options = null) {
        return $message * $level; // Simply return that, for testing
    }
    public function get_levelstr($level) {
        return parent::get_levelstr($level);
    }
}

/**
 * helper extended base_logger class that implements some methods for testing
 * Simply return the sum of message and level
 */
class mock_base_logger2 extends base_logger {

    protected function action($message, $level, $options = null) {
        return $message + $level; // Simply return that, for testing
    }
}

/**
 * helper extended base_logger class that implements some methods for testing
 * Simply return 8
 */
class mock_base_logger3 extends base_logger {

    protected function action($message, $level, $options = null) {
        return false; // Simply return false, for testing stopper
    }
}

/**
 * helper extended database_logger class that implements some methods for testing
 * Returns the complete info that normally will be used by insert record calls
 */
class mock_database_logger extends database_logger {

    protected function insert_log_record($table, $columns) {
        return array('table' => $table, 'columns' => $columns);
    }
}

/**
 * helper extended file_logger class that implements some methods for testing
 * Returns the, usually protected, handle
 */
class mock_file_logger extends file_logger {

    function get_fhandle() {
        return $this->fhandle;
    }
}
