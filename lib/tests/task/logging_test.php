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
 * This file contains the unit tests for the task logging system.
 *
 * @package   core
 * @category  test
 * @copyright 2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../fixtures/task_fixtures.php');

/**
 * This file contains the unit tests for the task logging system.
 *
 * @package   core
 * @category  test
 * @copyright 2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class logging_test extends \advanced_testcase {

    /**
     * @var \moodle_database The original database prior to mocking
     */
    protected $DB;

    /**
     * Relevant tearDown for logging tests.
     */
    public function tearDown(): void {
        global $DB;

        // Ensure that any logging is always ended.
        logmanager::finalise_log();

        if (null !== $this->DB) {
            $DB = $this->DB;
            $this->DB = null;
        }
    }

    /**
     * When the logmode is set to none, logging should not start.
     */
    public function test_logmode_none() {
        global $CFG;
        $this->resetAfterTest();

        $CFG->task_logmode = logmanager::MODE_NONE;

        $initialbufferstate = ob_get_status();

        $task = $this->get_test_adhoc_task();
        logmanager::start_logging($task);

        // There will be no additional output buffer.
        $this->assertEquals($initialbufferstate, ob_get_status());
    }

    /**
     * When the logmode is set to all that log capture is started.
     */
    public function test_start_logmode_all() {
        global $CFG;
        $this->resetAfterTest();

        $CFG->task_logmode = logmanager::MODE_ALL;

        $initialbufferstate = ob_get_status();

        $task = $this->get_test_adhoc_task();
        logmanager::start_logging($task);

        // Fetch the new output buffer state.
        $state = ob_get_status();

        // There will be no additional output buffer.
        $this->assertNotEquals($initialbufferstate, $state);
    }

    /**
     * When the logmode is set to fail that log capture is started.
     */
    public function test_start_logmode_fail() {
        global $CFG;
        $this->resetAfterTest();

        $CFG->task_logmode = logmanager::MODE_FAILONLY;

        $initialbufferstate = ob_get_status();

        $task = $this->get_test_adhoc_task();
        logmanager::start_logging($task);

        // Fetch the new output buffer state.
        $state = ob_get_status();

        // There will be no additional output buffer.
        $this->assertNotEquals($initialbufferstate, $state);
    }

    /**
     * When the logmode is set to fail, passing adhoc tests should not be logged.
     */
    public function test_logmode_fail_with_passing_adhoc_task() {
        global $CFG;
        $this->resetAfterTest();

        $CFG->task_logmode = logmanager::MODE_FAILONLY;

        $logger = $this->get_mocked_logger();

        $initialbufferstate = ob_get_status();

        $task = $this->get_test_adhoc_task();
        logmanager::start_logging($task);

        manager::adhoc_task_complete($task);

        $this->assertEmpty($logger::$storelogfortask);
    }

    /**
     * When the logmode is set to fail, passing scheduled tests should not be logged.
     */
    public function test_logmode_fail_with_passing_scheduled_task() {
        global $CFG;
        $this->resetAfterTest();

        $CFG->task_logmode = logmanager::MODE_FAILONLY;

        $logger = $this->get_mocked_logger();

        $initialbufferstate = ob_get_status();

        $task = $this->get_test_scheduled_task();
        logmanager::start_logging($task);

        manager::scheduled_task_complete($task);

        $this->assertEmpty($logger::$storelogfortask);
    }

    /**
     * When the logmode is set to fail, failing adhoc tests should be logged.
     */
    public function test_logmode_fail_with_failing_adhoc_task() {
        global $CFG;

        $this->resetAfterTest();

        // Mock the database. Marking jobs as failed updates a DB record which doesn't exist.
        $this->mock_database();

        $task = $this->get_test_adhoc_task();

        $CFG->task_logmode = logmanager::MODE_FAILONLY;

        $logger = $this->get_mocked_logger();

        logmanager::start_logging($task);
        manager::adhoc_task_failed($task);

        $this->assertCount(1, $logger::$storelogfortask);
        $this->assertEquals($task, $logger::$storelogfortask[0][0]);
        $this->assertTrue($logger::$storelogfortask[0][2]);
    }

    /**
     * When the logmode is set to fail, failing scheduled tests should be logged.
     */
    public function test_logmode_fail_with_failing_scheduled_task() {
        global $CFG;

        $this->resetAfterTest();

        // Mock the database. Marking jobs as failed updates a DB record which doesn't exist.
        $this->mock_database();

        $task = $this->get_test_scheduled_task();

        $CFG->task_logmode = logmanager::MODE_FAILONLY;

        $logger = $this->get_mocked_logger();

        logmanager::start_logging($task);
        manager::scheduled_task_failed($task);

        $this->assertCount(1, $logger::$storelogfortask);
        $this->assertEquals($task, $logger::$storelogfortask[0][0]);
        $this->assertTrue($logger::$storelogfortask[0][2]);
    }

    /**
     * When the logmode is set to fail, failing adhoc tests should be logged.
     */
    public function test_logmode_any_with_failing_adhoc_task() {
        global $CFG;

        $this->resetAfterTest();

        // Mock the database. Marking jobs as failed updates a DB record which doesn't exist.
        $this->mock_database();

        $task = $this->get_test_adhoc_task();

        $CFG->task_logmode = logmanager::MODE_FAILONLY;

        $logger = $this->get_mocked_logger();

        logmanager::start_logging($task);
        manager::adhoc_task_failed($task);

        $this->assertCount(1, $logger::$storelogfortask);
        $this->assertEquals($task, $logger::$storelogfortask[0][0]);
        $this->assertTrue($logger::$storelogfortask[0][2]);
    }

    /**
     * When the logmode is set to fail, failing scheduled tests should be logged.
     */
    public function test_logmode_any_with_failing_scheduled_task() {
        global $CFG;

        $this->resetAfterTest();

        // Mock the database. Marking jobs as failed updates a DB record which doesn't exist.
        $this->mock_database();

        $task = $this->get_test_scheduled_task();

        $CFG->task_logmode = logmanager::MODE_FAILONLY;

        $logger = $this->get_mocked_logger();

        logmanager::start_logging($task);
        manager::scheduled_task_failed($task);

        $this->assertCount(1, $logger::$storelogfortask);
        $this->assertEquals($task, $logger::$storelogfortask[0][0]);
        $this->assertTrue($logger::$storelogfortask[0][2]);
    }

    /**
     * When the logmode is set to fail, passing adhoc tests should be logged.
     */
    public function test_logmode_any_with_passing_adhoc_task() {
        global $CFG;

        $this->resetAfterTest();

        $this->mock_database();

        $task = $this->get_test_adhoc_task();

        $CFG->task_logmode = logmanager::MODE_ALL;

        $logger = $this->get_mocked_logger();

        logmanager::start_logging($task);
        manager::adhoc_task_complete($task);

        $this->assertCount(1, $logger::$storelogfortask);
        $this->assertEquals($task, $logger::$storelogfortask[0][0]);
        $this->assertFalse($logger::$storelogfortask[0][2]);
    }

    /**
     * When the logmode is set to fail, passing scheduled tests should be logged.
     */
    public function test_logmode_any_with_passing_scheduled_task() {
        global $CFG;

        $this->resetAfterTest();

        $this->mock_database();

        $task = $this->get_test_scheduled_task();

        $CFG->task_logmode = logmanager::MODE_ALL;

        $logger = $this->get_mocked_logger();

        logmanager::start_logging($task);
        manager::scheduled_task_complete($task);

        $this->assertCount(1, $logger::$storelogfortask);
        $this->assertEquals($task, $logger::$storelogfortask[0][0]);
        $this->assertFalse($logger::$storelogfortask[0][2]);
    }

    /**
     * Ensure that start_logging cannot be called in a nested fashion.
     */
    public function test_prevent_nested_logging() {
        $this->resetAfterTest();

        $task = $this->get_test_adhoc_task();
        logmanager::start_logging($task);

        $this->expectException(\coding_exception::class);
        logmanager::start_logging($task);
    }

    /**
     * Ensure that logging can be called after a previous log has finished.
     */
    public function test_repeated_usages() {
        $this->resetAfterTest();

        $logger = $this->get_mocked_logger();

        $task = $this->get_test_adhoc_task();
        logmanager::start_logging($task);
        logmanager::finalise_log();

        logmanager::start_logging($task);
        logmanager::finalise_log();

        $this->assertCount(2, $logger::$storelogfortask);
        $this->assertEquals($task, $logger::$storelogfortask[0][0]);
        $this->assertFalse($logger::$storelogfortask[0][2]);
        $this->assertEquals($task, $logger::$storelogfortask[1][0]);
        $this->assertFalse($logger::$storelogfortask[1][2]);
    }

    /**
     * Enusre that when finalise_log is called when logging is not active, nothing happens.
     */
    public function test_finalise_log_no_logging() {
        $initialbufferstate = ob_get_status();

        logmanager::finalise_log();

        // There will be no additional output buffer.
        $this->assertEquals($initialbufferstate, ob_get_status());
    }

    /**
     * When log capture is enabled, calls to the flush function should cause log output to be both returned and captured.
     */
    public function test_flush_on_own_buffer() {
        $this->resetAfterTest();

        $logger = $this->get_mocked_logger();

        $testoutput = "I am the output under test.\n";

        $task = $this->get_test_adhoc_task();
        logmanager::start_logging($task);

        echo $testoutput;

        $this->expectOutputString($testoutput);
        logmanager::flush();

        // Finalise the log.
        logmanager::finalise_log();

        $this->assertCount(1, $logger::$storelogfortask);
        $this->assertEquals($testoutput, file_get_contents($logger::$storelogfortask[0][1]));
    }

    /**
     * When log capture is enabled, calls to the flush function should not affect any subsequent ob_start.
     */
    public function test_flush_does_not_flush_inner_buffers() {
        $this->resetAfterTest();

        $logger = $this->get_mocked_logger();

        $testoutput = "I am the output under test.\n";

        $task = $this->get_test_adhoc_task();
        logmanager::start_logging($task);

        ob_start();
        echo $testoutput;
        ob_end_clean();

        logmanager::flush();

        // Finalise the log.
        logmanager::finalise_log();

        $this->assertCount(1, $logger::$storelogfortask);

        // The task logger should not have captured the content of the inner buffer.
        $this->assertEquals('', file_get_contents($logger::$storelogfortask[0][1]));
    }

    /**
     * When log capture is enabled, calls to the flush function should not affect any subsequent ob_start.
     */
    public function test_inner_flushed_buffers_are_logged() {
        $this->resetAfterTest();

        $logger = $this->get_mocked_logger();

        $testoutput = "I am the output under test.\n";

        $task = $this->get_test_adhoc_task();
        logmanager::start_logging($task);

        // We are going to flush the inner buffer. That means that we should expect the output immediately.
        $this->expectOutputString($testoutput);

        ob_start();
        echo $testoutput;
        ob_end_flush();

        // Finalise the log.
        logmanager::finalise_log();

        $this->assertCount(1, $logger::$storelogfortask);

        // The task logger should not have captured the content of the inner buffer.
        $this->assertEquals($testoutput, file_get_contents($logger::$storelogfortask[0][1]));
    }

    /**
     * Get an example adhoc task to use for testing.
     *
     * @return  adhoc_task
     */
    protected function get_test_adhoc_task(): adhoc_task {
        $task = $this->getMockForAbstractClass(adhoc_task::class);
        $task->set_component('core');

        // Mock a lock on the task.
        $lock = $this->getMockBuilder(\core\lock\lock::class)
            ->disableOriginalConstructor()
            ->getMock();
        $task->set_lock($lock);

        return $task;
    }

    /**
     * Get an example scheduled task to use for testing.
     *
     * @return  scheduled_task
     */
    protected function get_test_scheduled_task(): scheduled_task {
        $task = $this->getMockForAbstractClass(scheduled_task::class);

        // Mock a lock on the task.
        $lock = $this->getMockBuilder(\core\lock\lock::class)
            ->disableOriginalConstructor()
            ->getMock();
        $task->set_lock($lock);

        return $task;
    }

    /**
     * Create and configure a mocked task logger.
     *
     * @return  logging_test_mocked_logger
     */
    protected function get_mocked_logger() {
        global $CFG;

        // We will modify config for the alternate logging class therefore we mnust reset after the test.
        $this->resetAfterTest();

        // Note PHPUnit does not support mocking static functions.
        $CFG->task_log_class = logging_test_mocked_logger::class;
        logging_test_mocked_logger::test_reset();

        return $CFG->task_log_class;
    }

    /**
     * Mock the database.
     */
    protected function mock_database() {
        global $DB;

        // Store the old Database for restoration in reset.
        $this->DB = $DB;

        $DB = $this->getMockBuilder(\moodle_database::class)
            ->getMock();

        $DB->method('get_record')
            ->willReturn((object) []);
    }
}

/**
 * Mocked logger.
 *
 * @copyright 2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class logging_test_mocked_logger implements task_logger {

    /**
     * @var bool Whether this is configured.
     */
    public static $isconfigured = true;

    /**
     * @var array Arguments that store_log_for_task was called with.
     */
    public static $storelogfortask = [];

    /**
     * @var bool Whether this logger has a report.
     */
    public static $haslogreport = true;

    /**
     * Reset the test class.
     */
    public static function test_reset() {
        self::$isconfigured = true;
        self::$storelogfortask = [];
        self::$haslogreport = true;
    }

    /**
     * Whether the task is configured and ready to log.
     *
     * @return  bool
     */
    public static function is_configured(): bool {
        return self::$isconfigured;
    }

    /**
     * Store the log for the specified task.
     *
     * @param   task_base   $task The task that the log belongs to.
     * @param   string      $logpath The path to the log on disk
     * @param   bool        $failed Whether the task failed
     * @param   int         $dbreads The number of DB reads
     * @param   int         $dbwrites The number of DB writes
     * @param   float       $timestart The start time of the task
     * @param   float       $timeend The end time of the task
     */
    public static function store_log_for_task(task_base $task, string $logpath, bool $failed,
            int $dbreads, int $dbwrites, float $timestart, float $timeend) {
        self::$storelogfortask[] = func_get_args();
    }

    /**
     * Whether this task logger has a report available.
     *
     * @return  bool
     */
    public static function has_log_report(): bool {
        return self::$haslogreport;
    }

    /**
     * Get any URL available for viewing relevant task log reports.
     *
     * @param   string      $classname The task class to fetch for
     * @return  \moodle_url
     */
    public static function get_url_for_task_class(string $classname): \moodle_url {
        return new \moodle_url('');
    }

}
