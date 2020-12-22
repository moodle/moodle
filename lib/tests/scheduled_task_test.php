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
 * This file contains the unittests for scheduled tasks.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2013 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/fixtures/task_fixtures.php');

/**
 * Test class for scheduled task.
 *
 * @package core
 * @category task
 * @copyright 2013 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_scheduled_task_testcase extends advanced_testcase {

    /**
     * Test the cron scheduling method
     */
    public function test_eval_cron_field() {
        $testclass = new \core\task\scheduled_test_task();

        $this->assertEquals(20, count($testclass->eval_cron_field('*/3', 0, 59)));
        $this->assertEquals(31, count($testclass->eval_cron_field('1,*/2', 0, 59)));
        $this->assertEquals(15, count($testclass->eval_cron_field('1-10,5-15', 0, 59)));
        $this->assertEquals(13, count($testclass->eval_cron_field('1-10,5-15/2', 0, 59)));
        $this->assertEquals(3, count($testclass->eval_cron_field('1,2,3,1,2,3', 0, 59)));
        $this->assertEquals(1, count($testclass->eval_cron_field('-1,10,80', 0, 59)));
    }

    public function test_get_next_scheduled_time() {
        global $CFG;
        $this->resetAfterTest();

        $this->setTimezone('Europe/London');

        // Test job run at 1 am.
        $testclass = new \core\task\scheduled_test_task();

        // All fields default to '*'.
        $testclass->set_hour('1');
        $testclass->set_minute('0');
        // Next valid time should be 1am of the next day.
        $nexttime = $testclass->get_next_scheduled_time();

        $oneamdate = new DateTime('now', new DateTimeZone('Europe/London'));
        $oneamdate->setTime(1, 0, 0);
        // Make it 1 am tomorrow if the time is after 1am.
        if ($oneamdate->getTimestamp() < time()) {
            $oneamdate->add(new DateInterval('P1D'));
        }
        $oneam = $oneamdate->getTimestamp();

        $this->assertEquals($oneam, $nexttime, 'Next scheduled time is 1am.');

        // Disabled flag does not affect next time.
        $testclass->set_disabled(true);
        $nexttime = $testclass->get_next_scheduled_time();
        $this->assertEquals($oneam, $nexttime, 'Next scheduled time is 1am.');

        // Now test for job run every 10 minutes.
        $testclass = new \core\task\scheduled_test_task();

        // All fields default to '*'.
        $testclass->set_minute('*/10');
        // Next valid time should be next 10 minute boundary.
        $nexttime = $testclass->get_next_scheduled_time();

        $minutes = ((intval(date('i') / 10))+1) * 10;
        $nexttenminutes = mktime(date('H'), $minutes, 0);

        $this->assertEquals($nexttenminutes, $nexttime, 'Next scheduled time is in 10 minutes.');

        // Disabled flag does not affect next time.
        $testclass->set_disabled(true);
        $nexttime = $testclass->get_next_scheduled_time();
        $this->assertEquals($nexttenminutes, $nexttime, 'Next scheduled time is in 10 minutes.');

        // Test hourly job executed on Sundays only.
        $testclass = new \core\task\scheduled_test_task();
        $testclass->set_minute('0');
        $testclass->set_day_of_week('7');

        $nexttime = $testclass->get_next_scheduled_time();

        $this->assertEquals(7, date('N', $nexttime));
        $this->assertEquals(0, date('i', $nexttime));

        // Test monthly job
        $testclass = new \core\task\scheduled_test_task();
        $testclass->set_minute('32');
        $testclass->set_hour('0');
        $testclass->set_day('1');

        $nexttime = $testclass->get_next_scheduled_time();

        $this->assertEquals(32, date('i', $nexttime));
        $this->assertEquals(0, date('G', $nexttime));
        $this->assertEquals(1, date('j', $nexttime));
    }

    public function test_timezones() {
        global $CFG, $USER;

        // The timezones used in this test are chosen because they do not use DST - that would break the test.
        $this->resetAfterTest();

        $this->setTimezone('Asia/Kabul');

        $testclass = new \core\task\scheduled_test_task();

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
        $this->assertStringContainsString('2:15 AM', core_text::strtoupper($userdate));
    }

    public function test_reset_scheduled_tasks_for_component_customised(): void {
        $this->resetAfterTest(true);

        $tasks = \core\task\manager::load_scheduled_tasks_for_component('moodle');

        // Customise a task.
        $task = reset($tasks);
        $task->set_minute('1');
        $task->set_hour('2');
        $task->set_month('3');
        $task->set_day_of_week('4');
        $task->set_day('5');
        $task->set_customised('1');
        \core\task\manager::configure_scheduled_task($task);

        // Now call reset.
        \core\task\manager::reset_scheduled_tasks_for_component('moodle');

        // Fetch the task again.
        $taskafterreset = \core\task\manager::get_scheduled_task(get_class($task));

        // The task should still be the same as the customised.
        $this->assertTaskEquals($task, $taskafterreset);
    }

    public function test_reset_scheduled_tasks_for_component_deleted(): void {
        global $DB;
        $this->resetAfterTest(true);

        // Delete a task to simulate the fact that its new.
        $tasklist = \core\task\manager::load_scheduled_tasks_for_component('moodle');

        // Note: This test must use a task which does not use any random values.
        $task = \core\task\manager::get_scheduled_task(core\task\session_cleanup_task::class);

        $DB->delete_records('task_scheduled', array('classname' => '\\' . trim(get_class($task), '\\')));
        $this->assertFalse(\core\task\manager::get_scheduled_task(core\task\session_cleanup_task::class));

        // Now call reset on all the tasks.
        \core\task\manager::reset_scheduled_tasks_for_component('moodle');

        // Assert that the second task was added back.
        $taskafterreset = \core\task\manager::get_scheduled_task(core\task\session_cleanup_task::class);
        $this->assertNotFalse($taskafterreset);

        $this->assertTaskEquals($task, $taskafterreset);
        $this->assertCount(count($tasklist), \core\task\manager::load_scheduled_tasks_for_component('moodle'));
    }

    public function test_reset_scheduled_tasks_for_component_changed_in_source(): void {
        $this->resetAfterTest(true);

        // Delete a task to simulate the fact that its new.
        // Note: This test must use a task which does not use any random values.
        $task = \core\task\manager::get_scheduled_task(core\task\session_cleanup_task::class);

        // Get a copy of the task before maing changes for later comparison.
        $taskbeforechange = \core\task\manager::get_scheduled_task(core\task\session_cleanup_task::class);

        // Edit a task to simulate a change in its definition (as if it was not customised).
        $task->set_minute('1');
        $task->set_hour('2');
        $task->set_month('3');
        $task->set_day_of_week('4');
        $task->set_day('5');
        \core\task\manager::configure_scheduled_task($task);

        // Fetch the task out for comparison.
        $taskafterchange = \core\task\manager::get_scheduled_task(core\task\session_cleanup_task::class);

        // The task should now be different to the original.
        $this->assertTaskNotEquals($taskbeforechange, $taskafterchange);

        // Now call reset.
        \core\task\manager::reset_scheduled_tasks_for_component('moodle');

        // Fetch the task again.
        $taskafterreset = \core\task\manager::get_scheduled_task(core\task\session_cleanup_task::class);

        // The task should now be the same as the original.
        $this->assertTaskEquals($taskbeforechange, $taskafterreset);
    }

    /**
     * Tests that the reset function deletes old tasks.
     */
    public function test_reset_scheduled_tasks_for_component_delete() {
        global $DB;
        $this->resetAfterTest(true);

        $count = $DB->count_records('task_scheduled', array('component' => 'moodle'));
        $allcount = $DB->count_records('task_scheduled');

        $task = new \core\task\scheduled_test_task();
        $task->set_component('moodle');
        $record = \core\task\manager::record_from_scheduled_task($task);
        $DB->insert_record('task_scheduled', $record);
        $this->assertTrue($DB->record_exists('task_scheduled', array('classname' => '\core\task\scheduled_test_task',
            'component' => 'moodle')));

        $task = new \core\task\scheduled_test2_task();
        $task->set_component('moodle');
        $record = \core\task\manager::record_from_scheduled_task($task);
        $DB->insert_record('task_scheduled', $record);
        $this->assertTrue($DB->record_exists('task_scheduled', array('classname' => '\core\task\scheduled_test2_task',
            'component' => 'moodle')));

        $aftercount = $DB->count_records('task_scheduled', array('component' => 'moodle'));
        $afterallcount = $DB->count_records('task_scheduled');

        $this->assertEquals($count + 2, $aftercount);
        $this->assertEquals($allcount + 2, $afterallcount);

        // Now check that the right things were deleted.
        \core\task\manager::reset_scheduled_tasks_for_component('moodle');

        $this->assertEquals($count, $DB->count_records('task_scheduled', array('component' => 'moodle')));
        $this->assertEquals($allcount, $DB->count_records('task_scheduled'));
        $this->assertFalse($DB->record_exists('task_scheduled', array('classname' => '\core\task\scheduled_test2_task',
            'component' => 'moodle')));
        $this->assertFalse($DB->record_exists('task_scheduled', array('classname' => '\core\task\scheduled_test_task',
            'component' => 'moodle')));
    }

    public function test_get_next_scheduled_task() {
        global $DB;

        $this->resetAfterTest(true);
        // Delete all existing scheduled tasks.
        $DB->delete_records('task_scheduled');
        // Add a scheduled task.

        // A task that runs once per hour.
        $record = new stdClass();
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
        $task = \core\task\manager::get_next_scheduled_task($now);
        $this->assertInstanceOf('\core\task\scheduled_test_task', $task);
        $task->execute();

        \core\task\manager::scheduled_task_complete($task);
        // Should get handed the second task.
        $task = \core\task\manager::get_next_scheduled_task($now);
        $this->assertInstanceOf('\core\task\scheduled_test2_task', $task);
        $task->execute();

        \core\task\manager::scheduled_task_failed($task);
        // Should not get any task.
        $task = \core\task\manager::get_next_scheduled_task($now);
        $this->assertNull($task);

        // Should get the second task (retry after delay).
        $task = \core\task\manager::get_next_scheduled_task($now + 120);
        $this->assertInstanceOf('\core\task\scheduled_test2_task', $task);
        $task->execute();

        \core\task\manager::scheduled_task_complete($task);

        // Should not get any task.
        $task = \core\task\manager::get_next_scheduled_task($now);
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
        $task = \core\task\manager::get_next_scheduled_task($now);
        $this->assertInstanceOf('\core\task\scheduled_test2_task', $task);
        $task->execute();
        \core\task\manager::scheduled_task_complete($task);

        // Should get handed the first task.
        $task = \core\task\manager::get_next_scheduled_task($now);
        $this->assertInstanceOf('\core\task\scheduled_test_task', $task);
        $task->execute();
        \core\task\manager::scheduled_task_complete($task);

        // Should not get any task.
        $task = \core\task\manager::get_next_scheduled_task($now);
        $this->assertNull($task);
    }

    public function test_get_broken_scheduled_task() {
        global $DB;

        $this->resetAfterTest(true);
        // Delete all existing scheduled tasks.
        $DB->delete_records('task_scheduled');
        // Add a scheduled task.

        // A broken task that runs all the time.
        $record = new stdClass();
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
        $task = \core\task\manager::get_next_scheduled_task($now);
        $this->assertDebuggingCalled();
        $this->assertNull($task);
    }

    /**
     * Tests the use of 'R' syntax in time fields of tasks to get
     * tasks be configured with a non-uniform time.
     */
    public function test_random_time_specification() {

        // Testing non-deterministic things in a unit test is not really
        // wise, so we just test the values have changed within allowed bounds.
        $testclass = new \core\task\scheduled_test_task();

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
    public function test_file_temp_cleanup_task() {
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

        $task = \core\task\manager::get_scheduled_task('\\core\\task\\file_temp_cleanup_task');
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
    public function test_clear_fail_delay() {

        $this->resetAfterTest();

        // Get an example task to use for testing. Task is set to run every minute by default.
        $taskname = '\core\task\send_new_user_passwords_task';

        // Pretend task started running and then failed 3 times.
        $before = time();
        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
        for ($i = 0; $i < 3; $i ++) {
            $task = \core\task\manager::get_scheduled_task($taskname);
            $lock = $cronlockfactory->get_lock('\\' . get_class($task), 10);
            $task->set_lock($lock);
            \core\task\manager::scheduled_task_failed($task);
        }

        // Confirm task is now delayed by several minutes.
        $task = \core\task\manager::get_scheduled_task($taskname);
        $this->assertEquals(240, $task->get_fail_delay());
        $this->assertGreaterThan($before + 230, $task->get_next_run_time());

        // Clear the fail delay and re-get the task.
        \core\task\manager::clear_fail_delay($task);
        $task = \core\task\manager::get_scheduled_task($taskname);

        // There should be no delay and it should run within the next minute.
        $this->assertEquals(0, $task->get_fail_delay());
        $this->assertLessThan($before + 70, $task->get_next_run_time());
    }

    /**
     * Data provider for test_tool_health_category_find_missing_parents.
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
                        'week'  => '2',
                        'month' => '4',
                        'disabled' => 0,
                    ),
                    '\core\task\scheduled_test2_task' => array(
                        'min'   => '*',
                        'hour'  => '*',
                        'day'   => '*',
                        'week'  => '*',
                        'month' => '*',
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
                        'week'  => '4',
                        'month' => '5',
                        'disabled' => 0,
                    ),
                    '\core\task\scheduled_test2_task' => array(
                        'min'   => '1',
                        'hour'  => '2',
                        'day'   => '3',
                        'week'  => '4',
                        'month' => '5',
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
        $record = new stdClass();
        $record->component = 'test_scheduled_task';

        foreach ($tasks as $task) {
            $record->classname = $task;
            $DB->insert_record('task_scheduled', $record);

            $scheduledtask = \core\task\manager::get_scheduled_task($task);
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
        $record = new stdClass();
        $record->component = 'test_scheduled_task';
        $record->classname = '\core\task\scheduled_test_task';
        $record->disabled = 0;
        $DB->insert_record('task_scheduled', $record);

        // And disabled test.
        $record->classname = '\core\task\scheduled_test2_task';
        $record->disabled = 1;
        $DB->insert_record('task_scheduled', $record);

        $now = time();

        $scheduledtask = \core\task\manager::get_next_scheduled_task($now);
        $this->assertInstanceOf('\core\task\scheduled_test2_task', $scheduledtask);
        $scheduledtask->execute();
        \core\task\manager::scheduled_task_complete($scheduledtask);
    }

    /**
     * Assert that the specified tasks are equal.
     *
     * @param   \core\task\task_base $task
     * @param   \core\task\task_base $comparisontask
     */
    public function assertTaskEquals(\core\task\task_base $task, \core\task\task_base $comparisontask): void {
        // Convert both to an object.
        $task = \core\task\manager::record_from_scheduled_task($task);
        $comparisontask = \core\task\manager::record_from_scheduled_task($comparisontask);

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
    public function assertTaskNotEquals(\core\task\task_base $task, \core\task\task_base $comparisontask): void {
        // Convert both to an object.
        $task = \core\task\manager::record_from_scheduled_task($task);
        $comparisontask = \core\task\manager::record_from_scheduled_task($comparisontask);

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
}
