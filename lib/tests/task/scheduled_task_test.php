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

namespace core\task;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../fixtures/task_fixtures.php');

/**
 * Test class for scheduled task.
 *
 * @package core
 * @category test
 * @copyright 2013 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\task\scheduled_task
 */
class scheduled_task_test extends \advanced_testcase {

    /**
     * Data provider for {@see test_eval_cron_field}
     *
     * @return array
     */
    public static function eval_cron_provider(): array {
        return [
            // At every 3rd <unit>.
            ['*/3', 0, 29, [0, 3, 6, 9, 12, 15, 18, 21, 24, 27]],
            // At <unit> 1 and every 2nd <unit>.
            ['1,*/2', 0, 29, [0, 1, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28]],
            // At every <unit> from 1 through 10 and every <unit> from 5 through 15.
            ['1-10,5-15', 0, 29, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]],
            // At every <unit> from 1 through 10 and every 2nd <unit> from 5 through 15.
            ['1-10,5-15/2', 0, 29, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 15]],
            // At every <unit> from 1 through 10 and every 2nd <unit> from 5 through 29.
            ['1-10,5/2', 0, 29, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 15, 17, 19, 21, 23, 25, 27, 29]],
            // At <unit> 1, 2, 3.
            ['1,2,3,1,2,3', 0, 29, [1, 2, 3]],
            // Invalid.
            ['-1,10,80', 0, 29, []],
            // Invalid.
            ['-1', 0, 29, []],
        ];
    }

    /**
     * Test the cron scheduling method
     *
     * @param string $field
     * @param int $min
     * @param int $max
     * @param int[] $expected
     *
     * @dataProvider eval_cron_provider
     */
    public function test_eval_cron_field(string $field, int $min, int $max, array $expected): void {
        $testclass = new scheduled_test_task();

        $this->assertEquals($expected, $testclass->eval_cron_field($field, $min, $max));
    }

    public function test_get_next_scheduled_time(): void {
        global $CFG;
        $this->resetAfterTest();

        $this->setTimezone('Europe/London');

        // Let's specify the hour we are going to use initially for the test.
        // (note that we pick 01:00 that is tricky for Europe/London, because
        // it's exactly the Daylight Saving Time Begins hour.
        $testhour = 1;

        // Test job run at 1 am.
        $testclass = new scheduled_test_task();

        // All fields default to '*'.
        $testclass->set_hour($testhour);
        $testclass->set_minute('0');
        // Next valid time should be 1am of the next day.
        $nexttime = $testclass->get_next_scheduled_time();

        $oneamdate = new \DateTime('now', new \DateTimeZone('Europe/London'));
        $oneamdate->setTime($testhour, 0, 0);

        // Once a year (currently last Sunday of March), when changing to Daylight Saving Time,
        // Europe/London 01:00 simply doesn't exists because, exactly at 01:00 the clock
        // is advanced by one hour and becomes 02:00. When that happens, the DateInterval
        // calculations cannot be to advance by 1 day, but by one less hour. That is exactly when
        // the next scheduled run will happen (next day 01:00).
        $isdaylightsaving = false;
        if ($testhour < (int)$oneamdate->format('H')) {
            $isdaylightsaving = true;
        }

        // Make it 1 am tomorrow if the time is after 1am.
        if ($oneamdate->getTimestamp() < time()) {
            $oneamdate->add(new \DateInterval('P1D'));
            if ($isdaylightsaving) {
                // If today is Europe/London Daylight Saving Time Begins, expectation is 1 less hour.
                $oneamdate->sub(new \DateInterval('PT1H'));
            }
        }
        $oneam = $oneamdate->getTimestamp();

        $this->assertEquals($oneam, $nexttime, 'Next scheduled time is 1am.');

        // Disabled flag does not affect next time.
        $testclass->set_disabled(true);
        $nexttime = $testclass->get_next_scheduled_time();
        $this->assertEquals($oneam, $nexttime, 'Next scheduled time is 1am.');

        // Now test for job run every 10 minutes.
        $testclass = new scheduled_test_task();

        // All fields default to '*'.
        $testclass->set_minute('*/10');
        // Next valid time should be next 10 minute boundary.
        $nexttime = $testclass->get_next_scheduled_time();

        $minutes = ((intval(date('i') / 10)) + 1) * 10;
        $nexttenminutes = mktime(date('H'), $minutes, 0);

        $this->assertEquals($nexttenminutes, $nexttime, 'Next scheduled time is in 10 minutes.');

        // Disabled flag does not affect next time.
        $testclass->set_disabled(true);
        $nexttime = $testclass->get_next_scheduled_time();
        $this->assertEquals($nexttenminutes, $nexttime, 'Next scheduled time is in 10 minutes.');

        // Test hourly job executed on Sundays only.
        $testclass = new scheduled_test_task();
        $testclass->set_minute('0');
        $testclass->set_day_of_week('7');

        $nexttime = $testclass->get_next_scheduled_time();

        $this->assertEquals(7, date('N', $nexttime));
        $this->assertEquals(0, date('i', $nexttime));

        // Test monthly job.
        $testclass = new scheduled_test_task();
        $testclass->set_minute('32');
        $testclass->set_hour('0');
        $testclass->set_day('1');

        $nexttime = $testclass->get_next_scheduled_time();

        $this->assertEquals(32, date('i', $nexttime));
        $this->assertEquals(0, date('G', $nexttime));
        $this->assertEquals(1, date('j', $nexttime));
    }

    /**
     * Data provider for get_next_scheduled_time_detail.
     *
     * Note all times in here are in default Australia/Perth time zone.
     *
     * @return array[] Function parameters for each run
     */
    public static function get_next_scheduled_time_detail_provider(): array {
        return [
            // Every minute = next minute.
            ['2023-11-01 15:15', '*', '*', '*', '*', '*', '2023-11-01 15:16'],
            // Specified minute (coming up) = same hour, that minute.
            ['2023-11-01 15:15', '18', '*', '*', '*', '*', '2023-11-01 15:18'],
            // Specified minute (passed) = next hour, that minute.
            ['2023-11-01 15:15', '11', '*', '*', '*', '*', '2023-11-01 16:11'],
            // Range of minutes = same hour, next matching value.
            ['2023-11-01 15:15', '*/15', '*', '*', '*', '*', '2023-11-01 15:30'],
            // Specified hour, any minute = first minute that hour.
            ['2023-11-01 15:15', '*', '20', '*', '*', '*', '2023-11-01 20:00'],
            // Specified hour, specified minute = that time.
            ['2023-11-01 15:15', '13', '20', '*', '*', '*', '2023-11-01 20:13'],
            // Any minute, range of hours = next hour in range, 00:00.
            ['2023-11-01 15:15', '*', '*/6', '*', '*', '*', '2023-11-01 18:00'],
            // Specified minute, range of hours = next hour where minute not passed, that minute.
            ['2023-11-01 18:15', '10', '*/6', '*', '*', '*', '2023-11-02 00:10'],
            // Specified day, any hour/minute.
            ['2023-11-01 15:15', '*', '*', '3', '*', '*', '2023-11-03 00:00'],
            // Specified day (next month), any hour/minute.
            ['2023-11-05 15:15', '*', '*', '3', '*', '*', '2023-12-03 00:00'],
            // Specified day, specified hour.
            ['2023-11-01 15:15', '*', '17', '3', '*', '*', '2023-11-03 17:00'],
            // Specified day, specified minute.
            ['2023-11-01 15:15', '17', '*', '3', '*', '*', '2023-11-03 00:17'],
            // 30th of every month, February.
            ['2023-01-31 15:15', '15', '10', '30', '*', '*', '2023-03-30 10:15'],
            // Friday, any time. 2023-11-01 is a Wednesday, so it will run in 2 days.
            ['2023-11-01 15:15', '*', '*', '*', '5', '*', '2023-11-03 00:00'],
            // Friday, any time (but it's already Friday).
            ['2023-11-03 15:15', '*', '*', '*', '5', '*', '2023-11-03 15:16'],
            // Sunday (week rollover).
            ['2023-11-01 15:15', '*', '*', '*', '0', '*', '2023-11-05 00:00'],
            // Specified days and day of week (days come first).
            ['2023-11-01 15:15', '*', '*', '2,4,6', '5', '*', '2023-11-02 00:00'],
            // Specified days and day of week (day of week comes first).
            ['2023-11-01 15:15', '*', '*', '4,6,8', '5', '*', '2023-11-03 00:00'],
            // Specified months.
            ['2023-11-01 15:15', '*', '*', '*', '*', '6,8,10,12', '2023-12-01 00:00'],
            // Specified months (crossing year).
            ['2023-11-01 15:15', '*', '*', '*', '*', '6,8,10', '2024-06-01 00:00'],
            // Specified months and day of week (i.e. first Sunday in December).
            ['2023-11-01 15:15', '*', '*', '*', '0', '6,8,10,12', '2023-12-03 00:00'],
            // It's already December, but the next Friday is not until next month.
            ['2023-12-30 15:15', '*', '*', '*', '5', '6,8,10,12', '2024-06-07 00:00'],
            // Around end of year.
            ['2023-12-31 23:00', '10', '3', '*', '*', '*', '2024-01-01 03:10'],
            // Some impossible requirements...
            ['2023-12-31 23:00', '*', '*', '30', '*', '2', scheduled_task::NEVER_RUN_TIME],
            ['2023-12-31 23:00', '*', '*', '31', '*', '9,4,6,11', scheduled_task::NEVER_RUN_TIME],
            // Normal years and leap years.
            ['2021-01-01 23:00', '*', '*', '28', '*', '2', '2021-02-28 00:00'],
            ['2021-01-01 23:00', '*', '*', '29', '*', '2', '2024-02-29 00:00'],
            // Missing leap year over century. Longest possible gap between runs.
            ['2096-03-01 00:00', '59', '23', '29', '*', '2', '2104-02-29 23:59'],
        ];
    }

    /**
     * Tests get_next_scheduled_time using a large number of example scenarios.
     *
     * @param string $now Current time (strtotime format)
     * @param string $minute Minute restriction list for task
     * @param string $hour Hour restriction list for task
     * @param string $day Day restriction list for task
     * @param string $dayofweek Day of week restriction list for task
     * @param string $month Month restriction list for task
     * @param string|int $expected Expected run time (strtotime format or time int)
     * @dataProvider get_next_scheduled_time_detail_provider
     */
    public function test_get_next_scheduled_time_detail(string $now, string $minute, string $hour,
            string $day, string $dayofweek, string $month, string|int $expected): void {
        // Create test task with specified times.
        $task = new scheduled_test_task();
        $task->set_minute($minute);
        $task->set_hour($hour);
        $task->set_day($day);
        $task->set_day_of_week($dayofweek);
        $task->set_month($month);

        // Check function results.
        $nowtime = strtotime($now);
        if (is_int($expected)) {
            $expectedtime = $expected;
        } else {
            $expectedtime = strtotime($expected);
        }
        $actualtime = $task->get_next_scheduled_time($nowtime);
        $this->assertEquals($expectedtime, $actualtime, 'Expected ' . $expected . ', actual ' . date('Y-m-d H:i', $actualtime));
    }

    /**
     * Tests get_next_scheduled_time around DST changes, with regard to the continuity of frequent
     * tasks.
     *
     * We want frequent tasks to keep progressing as normal and not randomly stop for an hour, or
     * suddenly decide they need to happen in the past.
     */
    public function test_get_next_scheduled_time_dst_continuity(): void {
        $this->resetAfterTest();
        $this->setTimezone('Europe/London');

        // Test task is set to run every 20 minutes (:00, :20, :40).
        $task = new scheduled_test_task();
        $task->set_minute('*/20');

        // DST change forwards. Check times in GMT to ensure it progresses as normal.
        $before = strtotime('2023-03-26 00:59 GMT');
        $this->assertEquals(strtotime('2023-03-26 00:59 Europe/London'), $before);
        $one = $task->get_next_scheduled_time($before);
        $this->assertEquals(strtotime('2023-03-26 01:00 GMT'), $one);
        $this->assertEquals(strtotime('2023-03-26 02:00 Europe/London'), $one);
        $two = $task->get_next_scheduled_time($one);
        $this->assertEquals(strtotime('2023-03-26 01:20 GMT'), $two);
        $three = $task->get_next_scheduled_time($two);
        $this->assertEquals(strtotime('2023-03-26 01:40 GMT'), $three);
        $four = $task->get_next_scheduled_time($three);
        $this->assertEquals(strtotime('2023-03-26 02:00 GMT'), $four);

        // DST change backwards.
        $before = strtotime('2023-10-29 00:59 GMT');
        // The 'before' time is 01:59 Europe/London, but we won't explicitly test that because
        // there are two 01:59s so it might fail depending on implementation.
        $one = $task->get_next_scheduled_time($before);
        $this->assertEquals(strtotime('2023-10-29 01:00 GMT'), $one);
        // We cannot compare against the Eerope/London time (01:00) because there are two 01:00s.
        $two = $task->get_next_scheduled_time($one);
        $this->assertEquals(strtotime('2023-10-29 01:20 GMT'), $two);
        $three = $task->get_next_scheduled_time($two);
        $this->assertEquals(strtotime('2023-10-29 01:40 GMT'), $three);
        $four = $task->get_next_scheduled_time($three);
        $this->assertEquals(strtotime('2023-10-29 02:00 GMT'), $four);
        // This time is now unambiguous in Europe/London.
        $this->assertEquals(strtotime('2023-10-29 02:00 Europe/London'), $four);
    }

    public function test_timezones(): void {
        global $CFG, $USER;

        // The timezones used in this test are chosen because they do not use DST - that would break the test.
        $this->resetAfterTest();

        $this->setTimezone('Asia/Kabul');

        $testclass = new scheduled_test_task();

        // Scheduled tasks should always use servertime - so this is 03:30 GMT.
        $testclass->set_hour('1');
        $testclass->set_minute('0');

        // Next valid time should be 1am of the next day.
        $nexttime = $testclass->get_next_scheduled_time();

        // GMT+05:45.
        $USER->timezone = 'Asia/Kathmandu';
        $userdate = userdate($nexttime);

        // Should be displayed in user timezone.
        // I used http://www.timeanddate.com/worldclock/fixedtime.html?msg=Moodle+Test&iso=20160502T01&p1=113
        // setting my location to Kathmandu to verify this time.
        $this->assertStringContainsString('2:15 AM', \core_text::strtoupper($userdate));
    }

    public function test_reset_scheduled_tasks_for_component_customised(): void {
        $this->resetAfterTest(true);

        $tasks = manager::load_scheduled_tasks_for_component('moodle');

        // Customise a task.
        $task = reset($tasks);
        $task->set_minute('1');
        $task->set_hour('2');
        $task->set_day('3');
        $task->set_month('4');
        $task->set_day_of_week('5');
        $task->set_customised('1');
        manager::configure_scheduled_task($task);

        // Now call reset.
        manager::reset_scheduled_tasks_for_component('moodle');

        // Fetch the task again.
        $taskafterreset = manager::get_scheduled_task(get_class($task));

        // The task should still be the same as the customised.
        $this->assertTaskEquals($task, $taskafterreset);
    }

    public function test_reset_scheduled_tasks_for_component_deleted(): void {
        global $DB;
        $this->resetAfterTest(true);

        // Delete a task to simulate the fact that its new.
        $tasklist = manager::load_scheduled_tasks_for_component('moodle');

        // Note: This test must use a task which does not use any random values.
        $task = manager::get_scheduled_task(session_cleanup_task::class);

        $DB->delete_records('task_scheduled', array('classname' => '\\' . trim(get_class($task), '\\')));
        $this->assertFalse(manager::get_scheduled_task(session_cleanup_task::class));

        // Now call reset on all the tasks.
        manager::reset_scheduled_tasks_for_component('moodle');

        // Assert that the second task was added back.
        $taskafterreset = manager::get_scheduled_task(session_cleanup_task::class);
        $this->assertNotFalse($taskafterreset);

        $this->assertTaskEquals($task, $taskafterreset);
        $this->assertCount(count($tasklist), manager::load_scheduled_tasks_for_component('moodle'));
    }

    public function test_reset_scheduled_tasks_for_component_changed_in_source(): void {
        $this->resetAfterTest(true);

        // Delete a task to simulate the fact that its new.
        // Note: This test must use a task which does not use any random values.
        $task = manager::get_scheduled_task(session_cleanup_task::class);

        // Get a copy of the task before maing changes for later comparison.
        $taskbeforechange = manager::get_scheduled_task(session_cleanup_task::class);

        // Edit a task to simulate a change in its definition (as if it was not customised).
        $task->set_minute('1');
        $task->set_hour('2');
        $task->set_day('3');
        $task->set_month('4');
        $task->set_day_of_week('5');
        manager::configure_scheduled_task($task);

        // Fetch the task out for comparison.
        $taskafterchange = manager::get_scheduled_task(session_cleanup_task::class);

        // The task should now be different to the original.
        $this->assertTaskNotEquals($taskbeforechange, $taskafterchange);

        // Now call reset.
        manager::reset_scheduled_tasks_for_component('moodle');

        // Fetch the task again.
        $taskafterreset = manager::get_scheduled_task(session_cleanup_task::class);

        // The task should now be the same as the original.
        $this->assertTaskEquals($taskbeforechange, $taskafterreset);
    }

    /**
     * Tests that the reset function deletes old tasks.
     */
    public function test_reset_scheduled_tasks_for_component_delete(): void {
        global $DB;
        $this->resetAfterTest(true);

        $count = $DB->count_records('task_scheduled', array('component' => 'moodle'));
        $allcount = $DB->count_records('task_scheduled');

        $task = new scheduled_test_task();
        $task->set_component('moodle');
        $record = manager::record_from_scheduled_task($task);
        $DB->insert_record('task_scheduled', $record);
        $this->assertTrue($DB->record_exists('task_scheduled', array('classname' => '\core\task\scheduled_test_task',
            'component' => 'moodle')));

        $task = new scheduled_test2_task();
        $task->set_component('moodle');
        $record = manager::record_from_scheduled_task($task);
        $DB->insert_record('task_scheduled', $record);
        $this->assertTrue($DB->record_exists('task_scheduled', array('classname' => '\core\task\scheduled_test2_task',
            'component' => 'moodle')));

        $aftercount = $DB->count_records('task_scheduled', array('component' => 'moodle'));
        $afterallcount = $DB->count_records('task_scheduled');

        $this->assertEquals($count + 2, $aftercount);
        $this->assertEquals($allcount + 2, $afterallcount);

        // Now check that the right things were deleted.
        manager::reset_scheduled_tasks_for_component('moodle');

        $this->assertEquals($count, $DB->count_records('task_scheduled', array('component' => 'moodle')));
        $this->assertEquals($allcount, $DB->count_records('task_scheduled'));
        $this->assertFalse($DB->record_exists('task_scheduled', array('classname' => '\core\task\scheduled_test2_task',
            'component' => 'moodle')));
        $this->assertFalse($DB->record_exists('task_scheduled', array('classname' => '\core\task\scheduled_test_task',
            'component' => 'moodle')));
    }

    public function test_get_next_scheduled_task(): void {
        global $DB;

        $this->resetAfterTest(true);
        // Delete all existing scheduled tasks.
        $DB->delete_records('task_scheduled');
        // Add a scheduled task.

        // A task that runs once per hour.
        $record = new \stdClass();
        $record->blocking = true;
        $record->minute = '0';
        $record->hour = '0';
        $record->dayofweek = '*';
        $record->day = '*';
        $record->month = '*';
        $record->component = 'test_scheduled_task';
        $record->classname = '\core\task\scheduled_test_task';

        $DB->insert_record('task_scheduled', $record);
        // And another one to test failures.
        $record->classname = '\core\task\scheduled_test2_task';
        $DB->insert_record('task_scheduled', $record);
        // And disabled test.
        $record->classname = '\core\task\scheduled_test3_task';
        $record->disabled = 1;
        $DB->insert_record('task_scheduled', $record);

        $now = time();

        // Should get handed the first task.
        $task = manager::get_next_scheduled_task($now);
        $this->assertInstanceOf('\core\task\scheduled_test_task', $task);
        $task->execute();

        manager::scheduled_task_complete($task);
        // Should get handed the second task.
        $task = manager::get_next_scheduled_task($now);
        $this->assertInstanceOf('\core\task\scheduled_test2_task', $task);
        $task->execute();

        manager::scheduled_task_failed($task);
        // Should not get any task.
        $task = manager::get_next_scheduled_task($now);
        $this->assertNull($task);

        // Should get the second task (retry after delay).
        $task = manager::get_next_scheduled_task($now + 120);
        $this->assertInstanceOf('\core\task\scheduled_test2_task', $task);
        $task->execute();

        manager::scheduled_task_complete($task);

        // Should not get any task.
        $task = manager::get_next_scheduled_task($now);
        $this->assertNull($task);

        // Check ordering.
        $DB->delete_records('task_scheduled');
        $record->lastruntime = 2;
        $record->disabled = 0;
        $record->classname = '\core\task\scheduled_test_task';
        $DB->insert_record('task_scheduled', $record);

        $record->lastruntime = 1;
        $record->classname = '\core\task\scheduled_test2_task';
        $DB->insert_record('task_scheduled', $record);

        // Should get handed the second task.
        $task = manager::get_next_scheduled_task($now);
        $this->assertInstanceOf('\core\task\scheduled_test2_task', $task);
        $task->execute();
        manager::scheduled_task_complete($task);

        // Should get handed the first task.
        $task = manager::get_next_scheduled_task($now);
        $this->assertInstanceOf('\core\task\scheduled_test_task', $task);
        $task->execute();
        manager::scheduled_task_complete($task);

        // Should not get any task.
        $task = manager::get_next_scheduled_task($now);
        $this->assertNull($task);
    }

    public function test_get_broken_scheduled_task(): void {
        global $DB;

        $this->resetAfterTest(true);
        // Delete all existing scheduled tasks.
        $DB->delete_records('task_scheduled');
        // Add a scheduled task.

        // A broken task that runs all the time.
        $record = new \stdClass();
        $record->blocking = true;
        $record->minute = '*';
        $record->hour = '*';
        $record->dayofweek = '*';
        $record->day = '*';
        $record->month = '*';
        $record->component = 'test_scheduled_task';
        $record->classname = '\core\task\scheduled_test_task_broken';

        $DB->insert_record('task_scheduled', $record);

        $now = time();
        // Should not get any task.
        $task = manager::get_next_scheduled_task($now);
        $this->assertDebuggingCalled();
        $this->assertNull($task);
    }

    /**
     * Tests the use of 'R' syntax in time fields of tasks to get
     * tasks be configured with a non-uniform time.
     */
    public function test_random_time_specification(): void {

        // Testing non-deterministic things in a unit test is not really
        // wise, so we just test the values have changed within allowed bounds.
        $testclass = new scheduled_test_task();

        // The test task defaults to '*'.
        $this->assertIsString($testclass->get_minute());
        $this->assertIsString($testclass->get_hour());

        // Set a random value.
        $testclass->set_minute('R');
        $testclass->set_hour('R');
        $testclass->set_day_of_week('R');

        // Verify the minute has changed within allowed bounds.
        $minute = $testclass->get_minute();
        $this->assertIsInt($minute);
        $this->assertGreaterThanOrEqual(0, $minute);
        $this->assertLessThanOrEqual(59, $minute);

        // Verify the hour has changed within allowed bounds.
        $hour = $testclass->get_hour();
        $this->assertIsInt($hour);
        $this->assertGreaterThanOrEqual(0, $hour);
        $this->assertLessThanOrEqual(23, $hour);

        // Verify the dayofweek has changed within allowed bounds.
        $dayofweek = $testclass->get_day_of_week();
        $this->assertIsInt($dayofweek);
        $this->assertGreaterThanOrEqual(0, $dayofweek);
        $this->assertLessThanOrEqual(6, $dayofweek);
    }

    /**
     * Test that the file_temp_cleanup_task removes directories and
     * files as expected.
     */
    public function test_file_temp_cleanup_task(): void {
        global $CFG;
        $backuptempdir = make_backup_temp_directory('');

        // Create directories.
        $dir = $backuptempdir . DIRECTORY_SEPARATOR . 'backup01' . DIRECTORY_SEPARATOR . 'courses';
        mkdir($dir, 0777, true);

        // Create files to be checked and then deleted.
        $file01 = $dir . DIRECTORY_SEPARATOR . 'sections.xml';
        file_put_contents($file01, 'test data 001');
        $file02 = $dir . DIRECTORY_SEPARATOR . 'modules.xml';
        file_put_contents($file02, 'test data 002');
        // Change the time modified for the first file, to a time that will be deleted by the task (greater than seven days).
        touch($file01, time() - (8 * 24 * 3600));

        $task = manager::get_scheduled_task('\\core\\task\\file_temp_cleanup_task');
        $this->assertInstanceOf('\core\task\file_temp_cleanup_task', $task);
        $task->execute();

        // Scan the directory. Only modules.xml should be left.
        $filesarray = scandir($dir);
        $this->assertEquals('modules.xml', $filesarray[2]);
        $this->assertEquals(3, count($filesarray));

        // Change the time modified on modules.xml.
        touch($file02, time() - (8 * 24 * 3600));
        // Change the time modified on the courses directory.
        touch($backuptempdir . DIRECTORY_SEPARATOR . 'backup01' . DIRECTORY_SEPARATOR .
                'courses', time() - (8 * 24 * 3600));
        // Run the scheduled task to remove the file and directory.
        $task->execute();
        $filesarray = scandir($backuptempdir . DIRECTORY_SEPARATOR . 'backup01');
        // There should only be two items in the array, '.' and '..'.
        $this->assertEquals(2, count($filesarray));

        // Change the time modified on all of the files and directories.
        $dir = new \RecursiveDirectoryIterator($CFG->tempdir);
        // Show all child nodes prior to their parent.
        $iter = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::CHILD_FIRST);

        for ($iter->rewind(); $iter->valid(); $iter->next()) {
            if ($iter->isDir() && !$iter->isDot()) {
                $node = $iter->getRealPath();
                touch($node, time() - (8 * 24 * 3600));
            }
        }

        // Run the scheduled task again to remove all of the files and directories.
        $task->execute();
        $filesarray = scandir($CFG->tempdir);
        // All of the files and directories should be deleted.
        // There should only be three items in the array, '.', '..' and '.htaccess'.
        $this->assertEquals([ '.', '..', '.htaccess' ], $filesarray);
    }

    /**
     * Test that the function to clear the fail delay from a task works correctly.
     */
    public function test_clear_fail_delay(): void {

        $this->resetAfterTest();

        // Get an example task to use for testing. Task is set to run every minute by default.
        $taskname = '\core\task\send_new_user_passwords_task';

        // Pretend task started running and then failed 3 times.
        $before = time();
        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
        for ($i = 0; $i < 3; $i ++) {
            $task = manager::get_scheduled_task($taskname);
            $lock = $cronlockfactory->get_lock('\\' . get_class($task), 10);
            $task->set_lock($lock);
            manager::scheduled_task_failed($task);
        }

        // Confirm task is now delayed by several minutes.
        $task = manager::get_scheduled_task($taskname);
        $this->assertEquals(240, $task->get_fail_delay());
        $this->assertGreaterThan($before + 230, $task->get_next_run_time());

        // Clear the fail delay and re-get the task.
        manager::clear_fail_delay($task);
        $task = manager::get_scheduled_task($taskname);

        // There should be no delay and it should run within the next minute.
        $this->assertEquals(0, $task->get_fail_delay());
        $this->assertLessThan($before + 70, $task->get_next_run_time());
    }

    /**
     * Data provider for test_scheduled_task_override_values.
     */
    public static function provider_schedule_overrides(): array {
        return array(
            array(
                'scheduled_tasks' => array(
                    '\core\task\scheduled_test_task' => array(
                        'schedule' => '10 13 1 2 4',
                        'disabled' => 0,
                    ),
                    '\core\task\scheduled_test2_task' => array(
                        'schedule' => '* * * * *',
                        'disabled' => 1,
                    ),
                ),
                'task_full_classnames' => array(
                    '\core\task\scheduled_test_task',
                    '\core\task\scheduled_test2_task',
                ),
                'expected' => array(
                    '\core\task\scheduled_test_task' => array(
                        'min'   => '10',
                        'hour'  => '13',
                        'day'   => '1',
                        'month' => '2',
                        'week'  => '4',
                        'disabled' => 0,
                    ),
                    '\core\task\scheduled_test2_task' => array(
                        'min'   => '*',
                        'hour'  => '*',
                        'day'   => '*',
                        'month' => '*',
                        'week'  => '*',
                        'disabled' => 1,
                    ),
                )
            ),
            array(
                'scheduled_tasks' => array(
                    '\core\task\*' => array(
                        'schedule' => '1 2 3 4 5',
                        'disabled' => 0,
                    )
                ),
                'task_full_classnames' => array(
                    '\core\task\scheduled_test_task',
                    '\core\task\scheduled_test2_task',
                ),
                'expected' => array(
                    '\core\task\scheduled_test_task' => array(
                        'min'   => '1',
                        'hour'  => '2',
                        'day'   => '3',
                        'month' => '4',
                        'week'  => '5',
                        'disabled' => 0,
                    ),
                    '\core\task\scheduled_test2_task' => array(
                        'min'   => '1',
                        'hour'  => '2',
                        'day'   => '3',
                        'month' => '4',
                        'week'  => '5',
                        'disabled' => 0,
                    ),
                )
            )
        );
    }


    /**
     * Test to ensure scheduled tasks are updated by values set in config.
     *
     * @param array $overrides
     * @param array $tasks
     * @param array $expected
     * @dataProvider provider_schedule_overrides
     */
    public function test_scheduled_task_override_values(array $overrides, array $tasks, array $expected): void {
        global $CFG, $DB;

        $this->resetAfterTest();

        // Add overrides to the config.
        $CFG->scheduled_tasks = $overrides;

        // Set up test scheduled task record.
        $record = new \stdClass();
        $record->component = 'test_scheduled_task';

        foreach ($tasks as $task) {
            $record->classname = $task;
            $DB->insert_record('task_scheduled', $record);

            $scheduledtask = manager::get_scheduled_task($task);
            $expectedresults = $expected[$task];

            // Check that the task is actually overridden.
            $this->assertTrue($scheduledtask->is_overridden(), 'Is overridden');

            // Check minute is correct.
            $this->assertEquals($expectedresults['min'], $scheduledtask->get_minute(), 'Minute check');

            // Check day is correct.
            $this->assertEquals($expectedresults['day'], $scheduledtask->get_day(), 'Day check');

            // Check hour is correct.
            $this->assertEquals($expectedresults['hour'], $scheduledtask->get_hour(), 'Hour check');

            // Check week is correct.
            $this->assertEquals($expectedresults['week'], $scheduledtask->get_day_of_week(), 'Day of week check');

            // Check week is correct.
            $this->assertEquals($expectedresults['month'], $scheduledtask->get_month(), 'Month check');

            // Check to see if the task is disabled.
            $this->assertEquals($expectedresults['disabled'], $scheduledtask->get_disabled(), 'Disabled check');
        }
    }

    /**
     * Check that an overridden task is sent to be processed.
     */
    public function test_scheduled_task_overridden_task_can_run(): void {
        global $CFG, $DB;

        $this->resetAfterTest();

        // Delete all existing scheduled tasks.
        $DB->delete_records('task_scheduled');

        // Add overrides to the config.
        $CFG->scheduled_tasks = [
            '\core\task\scheduled_test_task' => [
                'disabled' => 1
            ],
            '\core\task\scheduled_test2_task' => [
                'disabled' => 0
            ],
        ];

        // A task that runs once per hour.
        $record = new \stdClass();
        $record->component = 'test_scheduled_task';
        $record->classname = '\core\task\scheduled_test_task';
        $record->disabled = 0;
        $DB->insert_record('task_scheduled', $record);

        // And disabled test.
        $record->classname = '\core\task\scheduled_test2_task';
        $record->disabled = 1;
        $DB->insert_record('task_scheduled', $record);

        $now = time();

        $scheduledtask = manager::get_next_scheduled_task($now);
        $this->assertInstanceOf('\core\task\scheduled_test2_task', $scheduledtask);
        $scheduledtask->execute();
        manager::scheduled_task_complete($scheduledtask);
    }

    /**
     * Assert that the specified tasks are equal.
     *
     * @param   \core\task\task_base $task
     * @param   \core\task\task_base $comparisontask
     */
    public function assertTaskEquals(task_base $task, task_base $comparisontask): void {
        // Convert both to an object.
        $task = manager::record_from_scheduled_task($task);
        $comparisontask = manager::record_from_scheduled_task($comparisontask);

        // Reset the nextruntime field as it is intentionally dynamic.
        $task->nextruntime = null;
        $comparisontask->nextruntime = null;

        $args = array_merge(
            [
                $task,
                $comparisontask,
            ],
            array_slice(func_get_args(), 2)
        );

        call_user_func_array([$this, 'assertEquals'], $args);
    }

    /**
     * Assert that the specified tasks are not equal.
     *
     * @param   \core\task\task_base $task
     * @param   \core\task\task_base $comparisontask
     */
    public function assertTaskNotEquals(task_base $task, task_base $comparisontask): void {
        // Convert both to an object.
        $task = manager::record_from_scheduled_task($task);
        $comparisontask = manager::record_from_scheduled_task($comparisontask);

        // Reset the nextruntime field as it is intentionally dynamic.
        $task->nextruntime = null;
        $comparisontask->nextruntime = null;

        $args = array_merge(
            [
                $task,
                $comparisontask,
            ],
            array_slice(func_get_args(), 2)
        );

        call_user_func_array([$this, 'assertNotEquals'], $args);
    }

    /**
     * Assert that the lastruntime column holds an original value after a scheduled task is reset.
     */
    public function test_reset_scheduled_tasks_for_component_keeps_original_lastruntime(): void {
        global $DB;
        $this->resetAfterTest(true);

        // Set lastruntime for the scheduled task.
        $DB->set_field('task_scheduled', 'lastruntime', 123456789, ['classname' => '\core\task\session_cleanup_task']);

        // Reset the task.
        manager::reset_scheduled_tasks_for_component('moodle');

        // Fetch the task again.
        $taskafterreset = manager::get_scheduled_task(session_cleanup_task::class);

        // Confirm, that lastruntime is still in place.
        $this->assertEquals(123456789, $taskafterreset->get_last_run_time());
    }

    /**
     * Data provider for {@see test_is_component_enabled}
     *
     * @return array[]
     */
    public static function is_component_enabled_provider(): array {
        return [
            'Enabled component' => ['auth_cas', true],
            'Disabled component' => ['auth_ldap', false],
            'Invalid component' => ['auth_invalid', false],
        ];
    }

    /**
     * Tests whether tasks belonging to components consider the component to be enabled
     *
     * @param string $component
     * @param bool $expected
     *
     * @dataProvider is_component_enabled_provider
     */
    public function test_is_component_enabled(string $component, bool $expected): void {
        $this->resetAfterTest();

        // Set cas as the only enabled auth component.
        set_config('auth', 'cas');

        $task = new scheduled_test_task();
        $task->set_component($component);

        $this->assertEquals($expected, $task->is_component_enabled());
    }

    /**
     * Test whether tasks belonging to core components considers the component to be enabled
     */
    public function test_is_component_enabled_core(): void {
        $task = new scheduled_test_task();
        $this->assertTrue($task->is_component_enabled());
    }

    /**
     * Test disabling and enabling individual tasks.
     */
    public function test_disable_and_enable_task(): void {
        $this->resetAfterTest();

        // We use a real task because the manager doesn't know about the test tasks.
        $taskname = '\core\task\send_new_user_passwords_task';

        $task = manager::get_scheduled_task($taskname);
        $defaulttask = manager::get_default_scheduled_task($taskname);
        $this->assertTaskEquals($task, $defaulttask);

        // Disable task and verify drift.
        $task->disable();
        $this->assertTaskNotEquals($task, $defaulttask);
        $this->assertEquals(1, $task->get_disabled());
        $this->assertEquals(false, $task->has_default_configuration());

        // Enable task and verify not drifted.
        $task->enable();
        $this->assertTaskEquals($task, $defaulttask);
        $this->assertEquals(0, $task->get_disabled());
        $this->assertEquals(true, $task->has_default_configuration());

        // Modify task and verify drift.
        $task->set_hour(1);
        \core\task\manager::configure_scheduled_task($task);
        $this->assertTaskNotEquals($task, $defaulttask);
        $this->assertEquals(1, $task->get_hour());
        $this->assertEquals(false, $task->has_default_configuration());

        // Disable task and verify drift.
        $task->disable();
        $this->assertTaskNotEquals($task, $defaulttask);
        $this->assertEquals(1, $task->get_disabled());
        $this->assertEquals(1, $task->get_hour());
        $this->assertEquals(false, $task->has_default_configuration());

        // Enable task and verify drift.
        $task->enable();
        $this->assertTaskNotEquals($task, $defaulttask);
        $this->assertEquals(0, $task->get_disabled());
        $this->assertEquals(1, $task->get_hour());
        $this->assertEquals(false, $task->has_default_configuration());
    }

    /**
     * Test send messages when a task reaches the max fail delay time.
     */
    public function test_message_max_fail_delay(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Redirect messages.
        $messagesink = $this->redirectMessages();
        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');

        // Get an example task to use for testing. Task is set to run every minute by default.
        $taskname = '\core\task\send_new_user_passwords_task';
        $task = manager::get_scheduled_task($taskname);
        $lock = $cronlockfactory->get_lock('\\' . get_class($task), 10);
        $task->set_lock($lock);
        // Catch the message.
        manager::scheduled_task_failed($task);
        $messages = $messagesink->get_messages();
        $this->assertCount(0, $messages);

        // Set the max fail delay time.
        $task = manager::get_scheduled_task($taskname);
        $lock = $cronlockfactory->get_lock('\\' . get_class($task), 10);
        $task->set_lock($lock);
        $task->set_fail_delay(86400);
        $task->execute();
        // Catch the message.
        manager::scheduled_task_failed($task);
        $messages = $messagesink->get_messages();
        $this->assertCount(1, $messages);

        // Get the task and execute it second time.
        $task = manager::get_scheduled_task($taskname);
        $lock = $cronlockfactory->get_lock('\\' . get_class($task), 10);
        $task->set_lock($lock);
        // Set the fail delay to 12 hours.
        $task->set_fail_delay(43200);
        $task->execute();
        manager::scheduled_task_failed($task);
        // Catch the message.
        $messages = $messagesink->get_messages();
        $this->assertCount(2, $messages);

        // Get the task and execute it third time.
        $task = manager::get_scheduled_task($taskname);
        $lock = $cronlockfactory->get_lock('\\' . get_class($task), 10);
        $task->set_lock($lock);
        // Set the fail delay to 48 hours.
        $task->set_fail_delay(172800);
        $task->execute();
        manager::scheduled_task_failed($task);
        // Catch the message.
        $messages = $messagesink->get_messages();
        $this->assertCount(3, $messages);

        // Check first message information.
        $this->assertStringContainsString('Task failed: Send new user passwords', $messages[0]->subject);
        $this->assertEquals('failedtaskmaxdelay', $messages[0]->eventtype);
        $this->assertEquals('-10', $messages[0]->useridfrom);
        $this->assertEquals('2', $messages[0]->useridto);

        // Close sink.
        $messagesink->close();
    }
}
