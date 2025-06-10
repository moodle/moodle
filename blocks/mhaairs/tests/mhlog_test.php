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
 * PHPUnit MHLog tests.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->dirroot/blocks/mhaairs/block_mhaairs_util.php");

/**
 * PHPUnit mhaairs log test case.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @group       block_mhaairs
 * @group       block_mhaairs_log
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs_log_testcase extends advanced_testcase {

    /**
     * Tests creating a logger instance.
     *
     * @return void
     */
    public function test_instance() {
        $this->resetAfterTest();

        // Logs disabled.
        set_config('block_mhaairs_gradelog', 0);

        $logger = MHLog::instance();
        $this->assertEquals(true, $logger instanceof MHLog);
        $this->assertEquals(false, $logger->logenabled);
        $this->assertNotEquals(null, $logger->filepath);

        // Logs enabled.
        set_config('block_mhaairs_gradelog', 1);

        $logger = MHLog::instance();
        $this->assertEquals(true, $logger instanceof MHLog);
        $this->assertEquals(true, $logger->logenabled);
        $this->assertNotEquals(null, $logger->filepath);
    }

    /**
     * Tests adding data to the current log file.
     *
     * @return void
     */
    public function test_logging() {
        $this->resetAfterTest();

        // Logs enabled.
        set_config('block_mhaairs_gradelog', 1);

        // First logger.
        $logger = MHLog::instance();
        // Add to the log.
        $result = $logger->log('Hello world');
        // Verify that putting the content in the log file did not fail.
        $this->assertNotEquals(false, $result);
        // Verify we have 1 log file.
        $result = count($logger->logs);
        $this->assertEquals(1, $result);
    }

    /**
     * Tests deleting logs.
     *
     * @return void
     */
    public function test_delete_logs() {
        $this->resetAfterTest();

        // Enable logs.
        set_config('block_mhaairs_gradelog', 1);

        // Add 3 loggers.
        $log1 = MHLog::instance();
        $log2 = MHLog::instance();
        $log3 = MHLog::instance();

        // Add 3 loggers.
        $log1->log('Hello world');
        $log2->log('Hello universe');
        $log3->log('Big bang');

        // Verify we have 3 log files.
        $result = count(MHLog::instance()->logs);
        $this->assertEquals(3, $result);

        // Delete one.
        $log1->delete();
        // Verify we have 2 log files.
        $result = count(MHLog::instance()->logs);
        $this->assertEquals(2, $result);

        // Delete all.
        MHLog::instance()->delete_all();
        // Verify we have no log files.
        $result = count(MHLog::instance()->logs);
        $this->assertEquals(0, $result);
    }
}
