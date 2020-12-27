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
 * This file contains the unit tests for the database task logger.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core\task\database_logger;

/**
 * This file contains the unit tests for the database task logger.
 *
 * @copyright 2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class task_database_logger_testcase extends advanced_testcase {

    /**
     * @var \moodle_database The original database prior to mocking
     */
    protected $DB;

    /**
     * Setup to backup the database before mocking.
     */
    public function setUp(): void {
        global $DB;

        $this->DB = $DB;
    }

    /**
     * Tear down to unmock the database where it was mocked.
     */
    public function tearDown(): void {
        global $DB;

        $DB = $this->DB;
        $this->DB = null;
    }

    /**
     * Ensure that store_log_for_task works with a passing scheduled task.
     */
    public function test_store_log_for_task_scheduled() {
        global $DB;

        $this->resetAfterTest();

        $endtime = microtime(true);
        $starttime = $endtime - 4;

        $logdir = make_request_directory();
        $logpath = "{$logdir}/log.txt";
        file_put_contents($logpath, 'Example content');

        $task = new \core\task\cache_cron_task();
        database_logger::store_log_for_task($task, $logpath, false, 1, 2, $starttime, $endtime);

        $logs = $DB->get_records('task_log');
        $this->assertCount(1, $logs);

        $log = reset($logs);
        $this->assertEquals(file_get_contents($logpath), $log->output);
        $this->assertEquals(0, $log->result);
        $this->assertEquals(database_logger::TYPE_SCHEDULED, $log->type);
        $this->assertEquals('core\task\cache_cron_task', $log->classname);
        $this->assertEquals(0, $log->userid);
    }

    /**
     * Ensure that store_log_for_task works with a passing adhoc task.
     */
    public function test_store_log_for_task_adhoc() {
        global $DB;

        $this->resetAfterTest();

        $endtime = microtime(true);
        $starttime = $endtime - 4;

        $logdir = make_request_directory();
        $logpath = "{$logdir}/log.txt";
        file_put_contents($logpath, 'Example content');

        $task = $this->getMockBuilder(\core\task\adhoc_task::class)
            ->setMethods(['get_component', 'execute'])
            ->getMock();

        $task->method('get_component')->willReturn('core_test');

        database_logger::store_log_for_task($task, $logpath, false, 1, 2, $starttime, $endtime);

        $logs = $DB->get_records('task_log');
        $this->assertCount(1, $logs);

        $log = reset($logs);
        $this->assertEquals(file_get_contents($logpath), $log->output);
        $this->assertEquals(0, $log->result);
        $this->assertEquals(database_logger::TYPE_ADHOC, $log->type);
    }

    /**
     * Ensure that store_log_for_task works with a failing scheduled task.
     */
    public function test_store_log_for_task_failed_scheduled() {
        global $DB;

        $this->resetAfterTest();

        $endtime = microtime(true);
        $starttime = $endtime - 4;

        $logdir = make_request_directory();
        $logpath = "{$logdir}/log.txt";
        file_put_contents($logpath, 'Example content');

        $task = new \core\task\cache_cron_task();
        database_logger::store_log_for_task($task, $logpath, true, 1, 2, $starttime, $endtime);

        $logs = $DB->get_records('task_log');
        $this->assertCount(1, $logs);

        $log = reset($logs);
        $this->assertEquals(file_get_contents($logpath), $log->output);
        $this->assertEquals(1, $log->result);
        $this->assertEquals(database_logger::TYPE_SCHEDULED, $log->type);
        $this->assertEquals('core\task\cache_cron_task', $log->classname);
        $this->assertEquals(0, $log->userid);
    }

    /**
     * Ensure that store_log_for_task works with a failing adhoc task.
     */
    public function test_store_log_for_task_failed_adhoc() {
        global $DB;

        $this->resetAfterTest();

        $endtime = microtime(true);
        $starttime = $endtime - 4;

        $logdir = make_request_directory();
        $logpath = "{$logdir}/log.txt";
        file_put_contents($logpath, 'Example content');

        $task = $this->getMockBuilder(\core\task\adhoc_task::class)
            ->setMethods(['get_component', 'execute'])
            ->getMock();

        $task->method('get_component')->willReturn('core_test');

        database_logger::store_log_for_task($task, $logpath, true, 1, 2, $starttime, $endtime);

        $logs = $DB->get_records('task_log');
        $this->assertCount(1, $logs);

        $log = reset($logs);
        $this->assertEquals(file_get_contents($logpath), $log->output);
        $this->assertEquals(1, $log->result);
        $this->assertEquals(database_logger::TYPE_ADHOC, $log->type);
        $this->assertEquals(0, $log->userid);
    }
    /**
     * Ensure that store_log_for_task works with a passing adhoc task run as a specific user.
     */
    public function test_store_log_for_task_adhoc_userid() {
        global $DB;

        $this->resetAfterTest();

        $endtime = microtime(true);
        $starttime = $endtime - 4;

        $logdir = make_request_directory();
        $logpath = "{$logdir}/log.txt";
        file_put_contents($logpath, 'Example content');

        $task = $this->getMockBuilder(\core\task\adhoc_task::class)
            ->setMethods(['get_component', 'execute', 'get_userid'])
            ->getMock();

        $task->method('get_component')->willReturn('core_test');
        $task->method('get_userid')->willReturn(99);

        database_logger::store_log_for_task($task, $logpath, false, 1, 2, $starttime, $endtime);

        $logs = $DB->get_records('task_log');
        $this->assertCount(1, $logs);

        $log = reset($logs);
        $this->assertEquals(file_get_contents($logpath), $log->output);
        $this->assertEquals(0, $log->result);
        $this->assertEquals(database_logger::TYPE_ADHOC, $log->type);
        $this->assertEquals(99, $log->userid);
    }

    /**
     * Ensure that the delete_task_logs function performs necessary deletion tasks.
     *
     * @dataProvider    delete_task_logs_provider
     * @param   mixed   $ids
     */
    public function test_delete_task_logs($ids) {
        $DB = $this->mock_database();
        $DB->expects($this->once())
            ->method('delete_records_list')
            ->with(
                $this->equalTo('task_log'),
                $this->equalTo('id'),
                $this->callback(function($deletedids) use ($ids) {
                    sort($ids);
                    $idvalues = array_values($deletedids);
                    sort($idvalues);

                    return $ids == $idvalues;
                })
            );

        database_logger::delete_task_logs($ids);
    }

    /**
     * Data provider for delete_task_logs tests.
     *
     * @return  array
     */
    public function delete_task_logs_provider() : array {
        return [
            [
                [0],
                [1],
                [1, 2, 3, 4, 5],
            ],
        ];
    }

    /**
     * Ensure that the retention period applies correctly.
     */
    public function test_cleanup_retention() {
        global $DB;

        $this->resetAfterTest();

        // Set a high value for task_logretainruns so that it does no interfere.
        set_config('task_logretainruns', 1000);

        // Create sample log data - 1 run per hour for 3 days - round down to the start of the hour to avoid time race conditions.
        $date = new DateTime();
        $date->setTime($date->format('G'), 0);
        $baselogtime = $date->getTimestamp();

        for ($i = 0; $i < 3 * 24; $i++) {
            $task = new \core\task\cache_cron_task();
            $logpath = __FILE__;
            database_logger::store_log_for_task($task, $logpath, false, 1, 2, $date->getTimestamp(), $date->getTimestamp() + MINSECS);

            $date->sub(new \DateInterval('PT1H'));
        }

        // Initially there should be 72 runs.
        $this->assertCount(72, $DB->get_records('task_log'));

        // Note: We set the retention time to a period like DAYSECS minus an adjustment.
        // The adjustment is to account for the time taken during setup.

        // With a retention period of 2 * DAYSECS, there should only be 47-48 left.
        set_config('task_logretention', (2 * DAYSECS) - (time() - $baselogtime));
        \core\task\database_logger::cleanup();
        $this->assertGreaterThanOrEqual(47, $DB->count_records('task_log'));
        $this->assertLessThanOrEqual(48, $DB->count_records('task_log'));

        // The oldest should be no more than 48 hours old.
        $oldest = $DB->get_records('task_log', [], 'timestart DESC', 'timestart', 0, 1);
        $oldest = reset($oldest);
        $this->assertGreaterThan(time() - (48 * DAYSECS), $oldest->timestart);

        // With a retention period of DAYSECS, there should only be 23 left.
        set_config('task_logretention', DAYSECS - (time() - $baselogtime));
        \core\task\database_logger::cleanup();
        $this->assertGreaterThanOrEqual(23, $DB->count_records('task_log'));
        $this->assertLessThanOrEqual(24, $DB->count_records('task_log'));

        // The oldest should be no more than 24 hours old.
        $oldest = $DB->get_records('task_log', [], 'timestart DESC', 'timestart', 0, 1);
        $oldest = reset($oldest);
        $this->assertGreaterThan(time() - (24 * DAYSECS), $oldest->timestart);

        // With a retention period of 0.5 DAYSECS, there should only be 11 left.
        set_config('task_logretention', (DAYSECS / 2) - (time() - $baselogtime));
        \core\task\database_logger::cleanup();
        $this->assertGreaterThanOrEqual(11, $DB->count_records('task_log'));
        $this->assertLessThanOrEqual(12, $DB->count_records('task_log'));

        // The oldest should be no more than 12 hours old.
        $oldest = $DB->get_records('task_log', [], 'timestart DESC', 'timestart', 0, 1);
        $oldest = reset($oldest);
        $this->assertGreaterThan(time() - (12 * DAYSECS), $oldest->timestart);
    }

    /**
     * Ensure that the run-count retention applies.
     */
    public function test_cleanup_retainruns() {
        global $DB;

        $this->resetAfterTest();

        // Set a high value for task_logretention so that it does not interfere.
        set_config('task_logretention', YEARSECS);

        // Create sample log data - 2 tasks, once per hour for 3 days.
        $date = new DateTime();
        $date->setTime($date->format('G'), 0);
        $firstdate = $date->getTimestamp();

        for ($i = 0; $i < 3 * 24; $i++) {
            $task = new \core\task\cache_cron_task();
            $logpath = __FILE__;
            database_logger::store_log_for_task($task, $logpath, false, 1, 2, $date->getTimestamp(), $date->getTimestamp() + MINSECS);

            $task = new \core\task\badges_cron_task();
            $logpath = __FILE__;
            database_logger::store_log_for_task($task, $logpath, false, 1, 2, $date->getTimestamp(), $date->getTimestamp() + MINSECS);

            $date->sub(new \DateInterval('PT1H'));
        }
        $lastdate = $date->getTimestamp();

        // Initially there should be 144 runs - 72 for each task.
        $this->assertEquals(144, $DB->count_records('task_log'));
        $this->assertEquals(72, $DB->count_records('task_log', ['classname' => \core\task\cache_cron_task::class]));
        $this->assertEquals(72, $DB->count_records('task_log', ['classname' => \core\task\badges_cron_task::class]));

        // Grab the records for comparison.
        $cachecronrecords = array_values($DB->get_records('task_log', ['classname' => \core\task\cache_cron_task::class], 'timestart DESC'));
        $badgescronrecords = array_values($DB->get_records('task_log', ['classname' => \core\task\badges_cron_task::class], 'timestart DESC'));

        // Configured to retain 144 should have no effect.
        set_config('task_logretainruns', 144);
        \core\task\database_logger::cleanup();
        $this->assertEquals(144, $DB->count_records('task_log'));
        $this->assertEquals(72, $DB->count_records('task_log', ['classname' => \core\task\cache_cron_task::class]));
        $this->assertEquals(72, $DB->count_records('task_log', ['classname' => \core\task\badges_cron_task::class]));

        // The list of records should be identical.
        $this->assertEquals($cachecronrecords, array_values($DB->get_records('task_log', ['classname' => \core\task\cache_cron_task::class], 'timestart DESC')));
        $this->assertEquals($badgescronrecords, array_values($DB->get_records('task_log', ['classname' => \core\task\badges_cron_task::class], 'timestart DESC')));

        // Configured to retain 72 should have no effect either.
        set_config('task_logretainruns', 72);
        \core\task\database_logger::cleanup();
        $this->assertEquals(144, $DB->count_records('task_log'));
        $this->assertEquals(72, $DB->count_records('task_log', ['classname' => \core\task\cache_cron_task::class]));
        $this->assertEquals(72, $DB->count_records('task_log', ['classname' => \core\task\badges_cron_task::class]));

        // The list of records should now only contain the first 72 of each.
        $this->assertEquals(
            array_slice($cachecronrecords, 0, 72),
            array_values($DB->get_records('task_log', ['classname' => \core\task\cache_cron_task::class], 'timestart DESC'))
        );
        $this->assertEquals(
            array_slice($badgescronrecords, 0, 72),
            array_values($DB->get_records('task_log', ['classname' => \core\task\badges_cron_task::class], 'timestart DESC'))
        );

        // Configured to only retain 24 should bring that down to a total of 48, or 24 each.
        set_config('task_logretainruns', 24);
        \core\task\database_logger::cleanup();
        $this->assertEquals(48, $DB->count_records('task_log'));
        $this->assertEquals(24, $DB->count_records('task_log', ['classname' => \core\task\cache_cron_task::class]));
        $this->assertEquals(24, $DB->count_records('task_log', ['classname' => \core\task\badges_cron_task::class]));

        // The list of records should now only contain the first 24 of each.
        $this->assertEquals(
            array_slice($cachecronrecords, 0, 24),
            array_values($DB->get_records('task_log', ['classname' => \core\task\cache_cron_task::class], 'timestart DESC'))
        );
        $this->assertEquals(
            array_slice($badgescronrecords, 0, 24),
            array_values($DB->get_records('task_log', ['classname' => \core\task\badges_cron_task::class], 'timestart DESC'))
        );

        // Configured to only retain 5 should bring that down to a total of 10, or 5 each.
        set_config('task_logretainruns', 5);
        \core\task\database_logger::cleanup();
        $this->assertEquals(10, $DB->count_records('task_log'));
        $this->assertEquals(5, $DB->count_records('task_log', ['classname' => \core\task\cache_cron_task::class]));
        $this->assertEquals(5, $DB->count_records('task_log', ['classname' => \core\task\badges_cron_task::class]));

        // The list of records should now only contain the first 5 of each.
        $this->assertEquals(
            array_slice($cachecronrecords, 0, 5),
            array_values($DB->get_records('task_log', ['classname' => \core\task\cache_cron_task::class], 'timestart DESC'))
        );
        $this->assertEquals(
            array_slice($badgescronrecords, 0, 5),
            array_values($DB->get_records('task_log', ['classname' => \core\task\badges_cron_task::class], 'timestart DESC'))
        );

        // Configured to only retain 0 should bring that down to none.
        set_config('task_logretainruns', 0);
        \core\task\database_logger::cleanup();
        $this->assertEquals(0, $DB->count_records('task_log'));
    }

    /**
     * Ensure that the retention period applies correctly when combined with the run count retention.
     */
    public function test_cleanup_combined() {
        global $DB;

        $this->resetAfterTest();

        // Create sample log data - 2 tasks, once per hour for 3 days.
        $date = new DateTime();
        $date->setTime($date->format('G'), 0);
        $baselogtime = $date->getTimestamp();

        for ($i = 0; $i < 3 * 24; $i++) {
            $task = new \core\task\cache_cron_task();
            $logpath = __FILE__;
            database_logger::store_log_for_task($task, $logpath, false, 1, 2, $date->getTimestamp(), $date->getTimestamp() + MINSECS);

            $task = new \core\task\badges_cron_task();
            $logpath = __FILE__;
            database_logger::store_log_for_task($task, $logpath, false, 1, 2, $date->getTimestamp(), $date->getTimestamp() + MINSECS);

            $date->sub(new \DateInterval('PT1H'));
        }

        // Initially there should be 144 runs - 72 for each task.
        $this->assertEquals(144, $DB->count_records('task_log'));
        $this->assertEquals(72, $DB->count_records('task_log', ['classname' => \core\task\cache_cron_task::class]));
        $this->assertEquals(72, $DB->count_records('task_log', ['classname' => \core\task\badges_cron_task::class]));

        // Note: We set the retention time to a period like DAYSECS minus an adjustment.
        // The adjustment is to account for the time taken during setup.

        // With a retention period of 2 * DAYSECS, there should only be 94-96 left.
        // The run count is a higher number so it will have no effect.
        set_config('task_logretention', (2 * DAYSECS) - (time() - $baselogtime));
        set_config('task_logretainruns', 50);
        \core\task\database_logger::cleanup();
        $this->assertGreaterThanOrEqual(94, $DB->count_records('task_log'));
        $this->assertLessThanOrEqual(96, $DB->count_records('task_log'));
        $this->assertGreaterThanOrEqual(47, $DB->count_records('task_log', ['classname' => \core\task\cache_cron_task::class]));
        $this->assertLessThanOrEqual(48, $DB->count_records('task_log', ['classname' => \core\task\cache_cron_task::class]));
        $this->assertGreaterThanOrEqual(47, $DB->count_records('task_log', ['classname' => \core\task\badges_cron_task::class]));
        $this->assertLessThanOrEqual(48, $DB->count_records('task_log', ['classname' => \core\task\badges_cron_task::class]));

        // We should retain the most recent 48 so the oldest will be no more than 48 hours old.
        $oldest = $DB->get_records('task_log', [], 'timestart DESC', 'timestart', 0, 1);
        $oldest = reset($oldest);
        $this->assertGreaterThan(time() - (48 * DAYSECS), $oldest->timestart);

        // Reducing the retain runs count to 10 should reduce the total logs to 20, overriding the time constraint.
        set_config('task_logretainruns', 10);
        \core\task\database_logger::cleanup();
        $this->assertEquals(20, $DB->count_records('task_log'));
        $this->assertEquals(10, $DB->count_records('task_log', ['classname' => \core\task\cache_cron_task::class]));
        $this->assertEquals(10, $DB->count_records('task_log', ['classname' => \core\task\badges_cron_task::class]));

        // We should retain the most recent 10 so the oldeste will be no more than 10 hours old.
        $oldest = $DB->get_records('task_log', [], 'timestart DESC', 'timestart', 0, 1);
        $oldest = reset($oldest);
        $this->assertGreaterThan(time() - (10 * DAYSECS), $oldest->timestart);
    }

    /**
     * Mock the database.
     */
    protected function mock_database() {
        global $DB;

        $DB = $this->getMockBuilder(\moodle_database::class)
            ->getMock();

        $DB->method('get_record')
            ->willReturn((object) []);

        return $DB;
    }
}
