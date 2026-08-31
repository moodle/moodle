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

use core\url;

/**
 * Test class for adhoc tasks.
 *
 * @package core
 * @category test
 * @copyright 2013 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\task\manager
 */
final class adhoc_task_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();

        require_once(__DIR__ . '/../fixtures/task_fixtures.php');
    }

    /**
     * Test getting name of task that implements it's own get_name method
     *
     * @covers \core\task\adhoc_task
     */
    public function test_get_name(): void {
        $task = new \core\task\adhoc_test_task();
        $this->assertEquals('Test adhoc class', $task->get_name());
    }

    /**
     * Test getting name of task that uses the default implementation of get_name
     *
     * @covers \core\task\adhoc_task
     */
    public function test_get_name_default(): void {
        $task = new \mod_fake\task\adhoc_component_task();
        $this->assertEquals('Adhoc component task', $task->get_name());
    }

    /**
     * Test basic adhoc task execution.
     */
    public function test_get_next_adhoc_task_now(): void {
        $this->resetAfterTest(true);

        $clock = $this->mock_clock_with_frozen();

        // Create an adhoc task.
        $task = new adhoc_test_task();

        // Queue it.
        manager::queue_adhoc_task($task);

        // Get it from the scheduler.
        $task = manager::get_next_adhoc_task($clock->time());
        $this->assertInstanceOf('\\core\\task\\adhoc_test_task', $task);
        $task->execute();
        manager::adhoc_task_complete($task);
    }

    /**
     * Test basic adhoc task execution.
     */
    public function test_get_next_adhoc_task_class(): void {
        $this->resetAfterTest(true);

        $clock = $this->mock_clock_with_frozen();

        // Create an adhoc task.
        $task = new \core\task\adhoc_test_task();

        // Queue it.
        manager::queue_adhoc_task($task);

        $classname = get_class($task);

        // The task will not be returned.
        $this->assertNull(manager::get_next_adhoc_task($clock->time(), true, "{$classname}notexists"));

        // Get it from the scheduler.
        $task = manager::get_next_adhoc_task($clock->time(), true, $classname);
        $this->assertInstanceOf('\\core\\task\\adhoc_test_task', $task);
        $task->execute();
        manager::adhoc_task_complete($task);
    }

    /**
     * Test adhoc task failure retry backoff.
     */
    public function test_get_next_adhoc_task_fail_retry(): void {
        $this->resetAfterTest(true);

        $clock = $this->mock_clock_with_frozen();

        // Create an adhoc task.
        $task = new adhoc_test_task();
        manager::queue_adhoc_task($task);

        $now = $clock->time();

        // Get it from the scheduler, execute it, and mark it as failed.
        $task = manager::get_next_adhoc_task($now);
        $taskid = $task->get_id();
        $task->execute();
        manager::adhoc_task_failed($task);

        // The task will not be returned immediately.
        $this->assertNull(manager::get_next_adhoc_task($now));

        // Should get the adhoc task (retry after delay). Fail it again.
        $clock->bump(120);
        $task = manager::get_next_adhoc_task($clock->time());
        $this->assertInstanceOf('\\core\\task\\adhoc_test_task', $task);
        $this->assertEquals($taskid, $task->get_id());
        $task->execute();
        manager::adhoc_task_failed($task);

        // Should get the adhoc task immediately.
        $task = manager::get_adhoc_task($taskid);
        $this->assertInstanceOf('\\core\\task\\adhoc_test_task', $task);
        $this->assertEquals($taskid, $task->get_id());
        $task->execute();
        manager::adhoc_task_complete($task);

        // Should not get any task.
        $this->assertNull(manager::get_next_adhoc_task($now));
    }

    /**
     * Test that failed tasks eventually hit the maximum delay.
     *
     * @covers \core\task\adhoc_task
     */
    public function test_get_next_adhoc_task_maximum_fail_delay(): void {
        $this->resetAfterTest(true);

        $clock = $this->mock_clock_with_frozen();
        $now = $clock->time();

        // Create an adhoc task.
        $task = new adhoc_test_task();
        $attemptsavailable = $task->get_attempts_available();
        manager::queue_adhoc_task($task);

        // Exhaust all attempts available.
        for ($x = 0; $x < $attemptsavailable; $x++) {
            $delay = $task->get_fail_delay() * 2;
            $task = manager::get_next_adhoc_task($now + $delay);
            $task->execute();
            manager::adhoc_task_failed($task);
        }
        // Check that the fail delay is now set to 24 hours (the maximum amount of times).
        $this->assertEquals(DAYSECS, $task->get_fail_delay());
    }

    /**
     * Test adhoc task failure retry backoff.
     */
    public function test_adhoc_task_with_retry_flag(): void {
        global $DB;
        $this->resetAfterTest();

        $clock = $this->mock_clock_with_frozen();

        $now = $clock->time();
        // Create a normal adhoc task.
        $task = new adhoc_test_task();
        $taskid1 = manager::queue_adhoc_task(task: $task);

        // This is a normal task, so it should have limited attempts.
        $attemptsavailable = $DB->get_field(
            table: 'task_adhoc',
            return: 'attemptsavailable',
            conditions: ['id' => $taskid1]
        );
        $this->assertEquals(expected: 12, actual: $attemptsavailable);

        // Get the task from the scheduler, execute it, and mark it as failed.
        $task = manager::get_next_adhoc_task(timestart: $now);
        $taskid1 = $task->get_id();
        $task->execute();
        manager::adhoc_task_failed(task: $task);

        // Now that the task has failed, there should be one less attempt available.
        $attemptsavailable = $DB->get_field(
            table: 'task_adhoc',
            return: 'attemptsavailable',
            conditions: ['id' => $taskid1]
        );
        $this->assertEquals(expected: 12 - 1, actual: $attemptsavailable);

        // Create a no-retry adhoc task.
        $now = $clock->time();
        $task = new no_retry_adhoc_task();
        $taskid2 = manager::queue_adhoc_task(task: $task);

        // This is no-retry task, so it should have only 1 attempt available.
        $attemptsavailable = $DB->get_field(
            table: 'task_adhoc',
            return: 'attemptsavailable',
            conditions: ['id' => $taskid2]
        );
        $this->assertEquals(
            expected: 1,
            actual: $attemptsavailable,
        );

        // Get the task from the scheduler, execute it, and mark it as failed.
        $task = manager::get_next_adhoc_task(timestart: $now);
        $taskid2 = $task->get_id();
        $task->execute();
        manager::adhoc_task_failed(task: $task);

        // This is no-retry task, the remaining available attempts should be reduced to 0.
        $attemptsavailable = $DB->get_field(
            table: 'task_adhoc',
            return: 'attemptsavailable',
            conditions: ['id' => $taskid2]
        );
        $this->assertEquals(
            expected: 0,
            actual: $attemptsavailable,
        );

        // There will be two records in the task_adhoc table, one for each task.
        $this->assertEquals(
            expected: 2,
            actual: $DB->count_records(table: 'task_adhoc')
        );
        // But get_next_adhoc_task() should return only the allowed re-try task.
        // The no-retry task should not be returned because it has no remaining attempts.
        do {
            $task = manager::get_next_adhoc_task(timestart: $now + 86400);
            if ($task) {
                manager::adhoc_task_failed(task: $task);
                $this->assertEquals(
                    expected: $taskid1,
                    actual: $task->get_id(),
                );
            }
        } while ($task);

        // Mark the normal task as complete.
        $task = manager::get_adhoc_task(taskid: $taskid1);
        manager::adhoc_task_complete($task);

        // There will be one record in the task_adhoc table.
        $this->assertEquals(
            expected: 1,
            actual: $DB->count_records(table: 'task_adhoc')
        );

        // But get_next_adhoc_task() should return nothing.
        $this->assertNull(manager::get_next_adhoc_task(timestart: $now + 86400));
    }

    /**
     * Test adhoc task failure cleanup.
     */
    public function test_adhoc_task_clean_up(): void {
        global $DB, $CFG;
        $this->resetAfterTest();

        $clock = $this->mock_clock_with_frozen();

        // Create two no-retry adhoc tasks.
        $task1 = new no_retry_adhoc_task();
        $taskid1 = manager::queue_adhoc_task(task: $task1);
        $task2 = new no_retry_adhoc_task();
        $taskid2 = manager::queue_adhoc_task(task: $task2);

        // Get the tasks and mark it as failed.
        $task = manager::get_adhoc_task($taskid1);
        manager::adhoc_task_failed(task: $task);
        $task = manager::get_adhoc_task($taskid2);
        manager::adhoc_task_failed(task: $task);

        // These are no-retry tasks, the remaining available attempts should be reduced to 0.
        $this->assertEquals(
            expected: 0,
            actual: $DB->get_field(
                table: 'task_adhoc',
                return: 'attemptsavailable',
                conditions: ['id' => $taskid1],
            ),
        );
        $this->assertEquals(
            expected: 0,
            actual: $DB->get_field(
                table: 'task_adhoc',
                return: 'attemptsavailable',
                conditions: ['id' => $taskid2],
            ),
        );

        // There will be two records in the task_adhoc table.
        $this->assertEquals(
            expected: 2,
            actual: $DB->count_records(table: 'task_adhoc'),
        );

        // Clean up failed adhoc tasks. This will clean nothing because the tasks are not old enough.
        manager::clean_failed_adhoc_tasks();

        // There will be two records in the task_adhoc table.
        $this->assertEquals(
            expected: 2,
            actual: $DB->count_records(table: 'task_adhoc'),
        );

        // Update the time of the task2 to be older more than 2 days.
        $DB->set_field(
            table: 'task_adhoc',
            newfield: 'firststartingtime',
            newvalue: $clock->time() - (DAYSECS * 2) - 10, // Plus 10 seconds to make sure it is older than 2 days.
            conditions: ['id' => $taskid2],
        );

        // Clean up failed adhoc tasks. This will clean nothing because the tasks are not old enough.
        manager::clean_failed_adhoc_tasks();

        // There will be two records in the task_adhoc table.
        $this->assertEquals(
            expected: 2,
            actual: $DB->count_records(table: 'task_adhoc'),
        );

        // Update the time of the task1 to be older than the cleanup time.
        $DB->set_field(
            table: 'task_adhoc',
            newfield: 'firststartingtime',
            // Plus 10 seconds to make sure it is older than the retention time.
            newvalue: $clock->time() - $CFG->task_adhoc_failed_retention - 10,
            conditions: ['id' => $taskid1],
        );

        // Clean up failed adhoc tasks. task1 should be cleaned now.
        manager::clean_failed_adhoc_tasks();

        // There will be one record in the task_adhoc table.
        $this->assertEquals(
            expected: 1,
            actual: $DB->count_records(table: 'task_adhoc'),
        );

        // Update the duration of the Failed ad hoc task retention period to one day.
        $CFG->task_adhoc_failed_retention = DAYSECS;

        // Clean up failed adhoc tasks. task2 should be cleaned now.
        manager::clean_failed_adhoc_tasks();

        // The task_adhoc table should be empty now.
        $this->assertEquals(
            expected: 0,
            actual: $DB->count_records(table: 'task_adhoc'),
        );
    }

    /**
     * Test adhoc task failure will retain the time information.
     */
    public function test_adhoc_task_failed_will_retain_time_info(): void {
        global $DB;
        $this->resetAfterTest();

        $clock = $this->mock_clock_with_frozen();
        $now = $clock->time();

        // Create an adhoc task.
        $task = new adhoc_test_task();
        // Queue it.
        $taskid = manager::queue_adhoc_task(task: $task);

        // Update the timecreated of the task to be older.
        $DB->set_field(
            table: 'task_adhoc',
            newfield: 'timecreated',
            newvalue: $clock->time() - DAYSECS,
            conditions: ['id' => $taskid],
        );

        // Get the timecreated value before marking the task as failed.
        $timecreatedbefore = $DB->get_field(
            table: 'task_adhoc',
            return: 'timecreated',
            conditions: ['id' => $taskid],
        );

        // Get the task from the scheduler.
        $task = manager::get_next_adhoc_task(timestart: $now);
        // Execute the task.
        $task->execute();
        // Mark the task as failed.
        manager::adhoc_task_failed(task: $task);

        // Get the timecreated value after marking the task as failed.
        $timecreatedafter = $DB->get_field(
            table: 'task_adhoc',
            return: 'timecreated',
            conditions: ['id' => $taskid],
        );

        // The timecreated values should be the same.
        $this->assertEquals($timecreatedbefore, $timecreatedafter);
    }

    /**
     * Test future adhoc task execution.
     */
    public function test_get_next_adhoc_task_future(): void {
        $this->resetAfterTest(true);

        $clock = $this->mock_clock_with_frozen();
        $now = $clock->time();

        // Create an adhoc task in future.
        $task = new adhoc_test_task();
        $task->set_next_run_time($now + 1000);
        manager::queue_adhoc_task($task);

        // Fetching the next task should not return anything.
        $this->assertNull(manager::get_next_adhoc_task($now));

        // Fetching in the future should return the task.
        $task = manager::get_next_adhoc_task($now + 1020);
        $this->assertInstanceOf('\\core\\task\\adhoc_test_task', $task);
        $task->execute();
        manager::adhoc_task_complete($task);
    }

    /**
     * Test queueing an adhoc task belonging to a component, where we set the task component accordingly
     */
    public function test_queue_adhoc_task_for_component(): void {
        $this->resetAfterTest();

        $task = new \mod_forum\task\send_user_digests();
        $task->set_component('mod_test');

        manager::queue_adhoc_task($task);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test queueing an adhoc task belonging to a component, where we do not set the task component
     */
    public function test_queue_task_for_component_without_set_component(): void {
        $this->resetAfterTest();

        $task = new \mod_forum\task\send_user_digests();

        manager::queue_adhoc_task($task);
        $this->assertDebuggingNotCalled();

        // Assert the missing component was set.
        $this->assertEquals('mod_forum', $task->get_component());
    }

    /**
     * Test queueing an adhoc task belonging to an invalid component, where we do not set the task component
     */
    public function test_queue_task_for_invalid_component_without_set_component(): void {
        $this->resetAfterTest();

        $task = new \mod_fake\task\adhoc_component_task();

        manager::queue_adhoc_task($task);
        $this->assertdebuggingcalledcount(
            2,
            array_fill(0, 2, 'Component not set and the class namespace does not match a valid component (mod_fake).'),
        );
    }

    /**
     * Test empty set of adhoc tasks
     */
    public function test_get_adhoc_tasks_empty_set(): void {
        $this->resetAfterTest(true);

        $this->assertEquals([], manager::get_adhoc_tasks('\\core\\task\\adhoc_test_task'));
    }

    /**
     * Test correct set of adhoc tasks is returned for class.
     */
    public function test_get_adhoc_tasks_result_set(): void {
        $this->resetAfterTest(true);

        for ($i = 0; $i < 3; $i++) {
            $task = new adhoc_test_task();
            manager::queue_adhoc_task($task);
        }

        for ($i = 0; $i < 3; $i++) {
            $task = new adhoc_test2_task();
            manager::queue_adhoc_task($task);
        }

        $adhoctests = manager::get_adhoc_tasks('\\core\\task\\adhoc_test_task');
        $adhoctest2s = manager::get_adhoc_tasks('\\core\\task\\adhoc_test2_task');

        $this->assertCount(3, $adhoctests);
        $this->assertCount(3, $adhoctest2s);

        foreach ($adhoctests as $task) {
            $this->assertInstanceOf('\\core\\task\\adhoc_test_task', $task);
        }

        foreach ($adhoctest2s as $task) {
            $this->assertInstanceOf('\\core\\task\\adhoc_test2_task', $task);
        }
    }

    /**
     * Ensure that the reschedule_or_queue_adhoc_task function will schedule a new task if no tasks exist.
     */
    public function test_reschedule_or_queue_adhoc_task_no_existing(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Schedule adhoc task.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        manager::reschedule_or_queue_adhoc_task($task);
        $this->assertEquals(1, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));
        $this->assertNotEmpty($DB->get_field('task_adhoc', 'identityhash', []));
    }

    /**
     * Ensure that the reschedule_or_queue_adhoc_task function will revive an exhausted matching task.
     */
    public function test_reschedule_or_queue_adhoc_task_after_failure(): void {
        global $DB;
        $this->resetAfterTest();

        $clock = $this->mock_clock_with_frozen();

        // Schedule adhoc task.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $task->set_next_run_time($clock->time()); // Not realistic. Normally in the future but does not matter.
        manager::reschedule_or_queue_adhoc_task($task);
        $this->assertEquals(1, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));
        $taskrecord1 = manager::get_queued_adhoc_task_record($task);
        $this->assertObjectHasProperty('id', $taskrecord1);
        $this->assertEquals($clock->time(), $taskrecord1->nextruntime);

        // Now mark the task permanently failed.
        $DB->update_record('task_adhoc', (object) [
            'id' => $taskrecord1->id,
            'faildelay' => 86400,
            'attemptsavailable' => 0,
        ]);

        // Now, schedule the task again. It should revive and reschedule the existing task.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $task->set_next_run_time($clock->time() + HOURSECS);
        manager::reschedule_or_queue_adhoc_task($task);
        $this->assertEquals(1, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));
        $taskrecord2 = manager::get_queued_adhoc_task_record($task);
        $this->assertEquals($taskrecord1->id, $taskrecord2->id);
        $this->assertEquals($clock->time() + HOURSECS, $taskrecord2->nextruntime);
        $this->assertEquals(0, $taskrecord2->faildelay);
        $this->assertEquals(12, $taskrecord2->attemptsavailable);
    }

    /**
     * Ensure that the reschedule_or_queue_adhoc_task function will schedule a new task if a task for the same user does
     * not exist.
     */
    public function test_reschedule_or_queue_adhoc_task_different_user(): void {
        $this->resetAfterTest(true);
        $user = \core_user::get_user_by_username('admin');

        // Schedule adhoc task.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        manager::reschedule_or_queue_adhoc_task($task);

        // Schedule adhoc task for a different user.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $task->set_userid($user->id);
        manager::reschedule_or_queue_adhoc_task($task);

        $this->assertEquals(2, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));
    }

    /**
     * Ensure that the reschedule_or_queue_adhoc_task function will schedule a new task if a task with different custom
     * data exists.
     */
    public function test_reschedule_or_queue_adhoc_task_different_data(): void {
        $this->resetAfterTest(true);

        // Schedule adhoc task.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        manager::reschedule_or_queue_adhoc_task($task);

        // Schedule adhoc task for a different user.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 11]);
        manager::reschedule_or_queue_adhoc_task($task);

        $this->assertEquals(2, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));
    }

    /**
     * Ensure that the reschedule_or_queue_adhoc_task function will not make any change for matching data if no time was
     * specified.
     */
    public function test_reschedule_or_queue_adhoc_task_match_no_change(): void {
        $this->resetAfterTest(true);

        $clock = $this->mock_clock_with_frozen();

        // Schedule adhoc task.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $task->set_next_run_time($clock->time() + DAYSECS);
        manager::reschedule_or_queue_adhoc_task($task);

        $before = manager::get_adhoc_tasks('core\task\adhoc_test_task');

        // Schedule the task again but do not specify a time.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        manager::reschedule_or_queue_adhoc_task($task);

        $this->assertEquals(1, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));
        $this->assertEquals($before, manager::get_adhoc_tasks('core\task\adhoc_test_task'));
    }

    /**
     * Ensure that the reschedule_or_queue_adhoc_task function will update the run time if there are planned changes.
     */
    public function test_reschedule_or_queue_adhoc_task_match_update_runtime(): void {
        $this->resetAfterTest(true);

        $clock = $this->mock_clock_with_frozen();

        $initialruntime = $clock->time() + DAYSECS;
        $newruntime = $clock->time() + WEEKSECS;

        // Schedule adhoc task.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $task->set_next_run_time($initialruntime);
        manager::reschedule_or_queue_adhoc_task($task);

        $before = manager::get_adhoc_tasks('core\task\adhoc_test_task');

        // Schedule the task again.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $task->set_next_run_time($newruntime);
        manager::reschedule_or_queue_adhoc_task($task);

        $tasks = manager::get_adhoc_tasks('core\task\adhoc_test_task');
        $this->assertEquals(1, count($tasks));
        $this->assertNotEquals($before, $tasks);
        $firsttask = reset($tasks);
        $this->assertEquals($newruntime, $firsttask->get_next_run_time());
    }

    /**
     * Test queue_adhoc_task "if not scheduled".
     */
    public function test_queue_adhoc_task_if_not_scheduled(): void {
        $this->resetAfterTest(true);
        $user = \core_user::get_user_by_username('admin');

        // Schedule adhoc task.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $this->assertNotEmpty(manager::queue_adhoc_task($task, true));
        $this->assertEquals(1, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule adhoc task with a user.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $task->set_userid($user->id);
        $this->assertNotEmpty(manager::queue_adhoc_task($task, true));
        $this->assertEquals(2, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task with different custom data.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 1]);
        $taskid3a = manager::queue_adhoc_task($task, true);
        $this->assertNotEmpty($taskid3a);
        $this->assertEquals(3, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task with same custom data.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 1]);
        $taskid3b = manager::queue_adhoc_task($task, true);
        $this->assertEquals($taskid3a, $taskid3b);
        $this->assertEquals(3, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task with same custom data and a user.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 1]);
        $task->set_userid($user->id);
        $taskid4 = manager::queue_adhoc_task($task, true);
        $this->assertNotEmpty($taskid4);
        $this->assertEquals(4, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task without custom data.
        // Note: This task was created earlier.
        $task = new adhoc_test_task();
        $taskid5a = manager::queue_adhoc_task($task, true);
        $this->assertNotEmpty($taskid5a);
        $this->assertEquals(5, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task without custom data (again).
        $task5 = new adhoc_test_task();
        $taskid5b = manager::queue_adhoc_task($task5, true);
        $this->assertEquals($taskid5a, $taskid5b);
        $this->assertEquals(5, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task without custom data but with a userid.
        $task6 = new adhoc_test_task();
        $user = \core_user::get_user_by_username('admin');
        $task6->set_userid($user->id);
        $taskid6a = manager::queue_adhoc_task($task6, true);
        $this->assertNotEmpty($taskid6a);
        $this->assertEquals(6, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task again without custom data but with a userid.
        $task6 = new adhoc_test_task();
        $user = \core_user::get_user_by_username('admin');
        $task6->set_userid($user->id);
        $taskid6b = manager::queue_adhoc_task($task6, true);
        $this->assertEquals($taskid6a, $taskid6b);
        $this->assertEquals(6, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));
    }

    /**
     * Test that, after a permanent failure, queue_adhoc_task(..., checkforexisting: true) works.
     */
    public function test_queue_adhoc_task_if_not_scheduled_after_failure(): void {
        global $DB;
        $this->resetAfterTest();

        // Schedule adhoc task.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $taskid1 = manager::queue_adhoc_task($task, true);
        $this->assertNotEmpty($taskid1);
        $this->assertEquals(1, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));
        $taskrecord1 = manager::get_queued_adhoc_task_record($task);
        $this->assertObjectHasProperty('id', $taskrecord1);
        $this->assertEquals($taskid1, $taskrecord1->id);

        // Verify again that re-scheduling the same task returns the existing task id.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $this->assertEquals($taskrecord1->id, manager::queue_adhoc_task($task, true));
        $this->assertEquals(1, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));
        $taskrecord2 = manager::get_queued_adhoc_task_record($task);
        $this->assertEquals($taskrecord1->id, $taskrecord2->id);

        // Now mark the task permanently failed.
        $DB->update_record('task_adhoc', (object) [
            'id' => $taskrecord1->id,
            'faildelay' => 86400,
            'attemptsavailable' => 0,
        ]);

        // Now, schedule the task again. The existing task should be revived.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $this->assertEquals($taskrecord1->id, manager::queue_adhoc_task($task, true));
        $this->assertEquals(1, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));
        $taskrecord3 = manager::get_queued_adhoc_task_record($task);
        $this->assertEquals($taskrecord1->id, $taskrecord3->id);
        $this->assertEquals(0, $taskrecord3->faildelay);
        $this->assertEquals(12, $taskrecord3->attemptsavailable);
    }

    /**
     * Test that when no userid is specified, it returns empty from the DB
     * too.
     * @covers \core\task\adhoc_task
     */
    public function test_adhoc_task_user_empty(): void {
        $this->resetAfterTest(true);

        $clock = $this->mock_clock_with_frozen();

        // Create an adhoc task in future.
        $task = new adhoc_test_task();
        manager::queue_adhoc_task($task);

        // Get it back from the scheduler.
        $now = $clock->time();
        $task = manager::get_next_adhoc_task($now);
        manager::adhoc_task_complete($task);

        $this->assertEmpty($task->get_userid());
    }

    /**
     * Test that when a userid is specified, that userid is subsequently
     * returned.
     *
     * @covers \core\task\adhoc_task
     */
    public function test_adhoc_task_user_set(): void {
        $this->resetAfterTest(true);

        // Create an adhoc task in future.
        $task = new adhoc_test_task();
        $user = \core_user::get_user_by_username('admin');
        $task->set_userid($user->id);
        manager::queue_adhoc_task($task);

        // Get it back from the scheduler.
        $clock = $this->mock_clock_with_frozen();
        $now = $clock->time();
        $task = manager::get_next_adhoc_task($now);
        manager::adhoc_task_complete($task);

        $this->assertEquals($user->id, $task->get_userid());
    }

    /**
     * Test adhoc task with the first starting time.
     */
    public function test_adhoc_task_get_first_starting_time(): void {
        global $DB;
        $this->resetAfterTest(true);

        $clock = $this->mock_clock_with_frozen();
        $now = $clock->time();

        // Create an adhoc task.
        $task = new adhoc_test_task();
        // Queue it.
        $taskid = manager::queue_adhoc_task(task: $task);

        // Get the firststartingtime value.
        $firststartingtime = $DB->get_field(
            table: 'task_adhoc',
            return: 'firststartingtime',
            conditions: ['id' => $taskid],
        );
        $this->assertNull($firststartingtime);

        // This will make sure that the task will be started after the $now value.
        $clock->bump(5);

        // Get the task from the scheduler.
        $task = manager::get_next_adhoc_task(timestart: $now);
        // Mark the task as starting.
        manager::adhoc_task_starting($task);
        // Execute the task.
        $task->execute();
        // Mark the task as failed.
        manager::adhoc_task_failed(task: $task);

        // Get the firststartingtime value.
        $origintimestarted = $DB->get_field(
            table: 'task_adhoc',
            return: 'firststartingtime',
            conditions: ['id' => $taskid],
        );
        $this->assertNotNull($origintimestarted);
        $this->assertGreaterThan($now, $origintimestarted);

        // Time travel 24 hours into the future.
        $clock->bump(DAYSECS * 3);
        $now = $clock->time();
        // Get the task from the scheduler.
        $task = manager::get_next_adhoc_task(timestart: $now);
        // Mark the task as starting.
        manager::adhoc_task_starting($task);
        // Execute the task.
        $task->execute();
        // Mark the task as failed.
        manager::adhoc_task_failed(task: $task);

        // Get the firststartingtime value.
        $firststartingtime = $DB->get_field(
            table: 'task_adhoc',
            return: 'firststartingtime',
            conditions: ['id' => $taskid],
        );

        // The firststartingtime value should not be changed.
        $this->assertEquals($origintimestarted, $firststartingtime);
    }

    /**
     * Test get_concurrency_limit() method to return 0 by default.
     *
     * @covers \core\task\adhoc_task
     */
    public function test_get_concurrency_limit(): void {
        $this->resetAfterTest(true);
        $task = new adhoc_test_task();
        $concurrencylimit = $task->get_concurrency_limit();
        $this->assertEquals(0, $concurrencylimit);
    }

    /**
     * Test get_concurrency_limit() method to return a default value set in config.
     * @covers \core\task\adhoc_task
     */
    public function test_get_concurrency_limit_default(): void {
        $this->resetAfterTest(true);
        set_config('task_concurrency_limit_default', 10);
        $task = new adhoc_test_task();
        $concurrencylimit = $task->get_concurrency_limit();
        $this->assertEquals(10, $concurrencylimit);
    }

    /**
     * Test get_concurrency_limit() method to return a value for specific task class.
     * @covers \core\task\adhoc_task
     */
    public function test_get_concurrency_limit_for_task(): void {
        global $CFG;
        $this->resetAfterTest(true);
        set_config('task_concurrency_limit_default', 10);
        $CFG->task_concurrency_limit = ['core\task\adhoc_test_task' => 5];
        $task = new adhoc_test_task();
        $concurrencylimit = $task->get_concurrency_limit();
        $this->assertEquals(5, $concurrencylimit);
    }

    /**
     * Test adhoc task sorting.
     */
    public function test_get_next_adhoc_task_sorting(): void {
        $this->resetAfterTest(true);

        $clock = $this->mock_clock_with_frozen();

        // Create adhoc tasks.
        $task1 = new adhoc_test_task();
        $task1->set_next_run_time(1510000000);
        $task1->set_custom_data('Task 1');
        manager::queue_adhoc_task($task1);

        $task2 = new adhoc_test_task();
        $task2->set_next_run_time(1520000000);
        $task2->set_custom_data('Task 2');
        manager::queue_adhoc_task($task2);

        $task3 = new adhoc_test_task();
        $task3->set_next_run_time(1520000000);
        $task3->set_custom_data('Task 3');
        manager::queue_adhoc_task($task3);

        // Shuffle tasks.
        $task1->set_next_run_time(1540000000);
        manager::reschedule_or_queue_adhoc_task($task1);

        $task3->set_next_run_time(1530000000);
        manager::reschedule_or_queue_adhoc_task($task3);

        $task2->set_next_run_time(1530000000);
        manager::reschedule_or_queue_adhoc_task($task2);

        // Confirm, that tasks are sorted by nextruntime and then by id (ascending).
        $task = manager::get_next_adhoc_task($clock->time());
        $this->assertEquals('Task 2', $task->get_custom_data());
        manager::adhoc_task_complete($task);

        $task = manager::get_next_adhoc_task($clock->time());
        $this->assertEquals('Task 3', $task->get_custom_data());
        manager::adhoc_task_complete($task);

        $task = manager::get_next_adhoc_task($clock->time());
        $this->assertEquals('Task 1', $task->get_custom_data());
        manager::adhoc_task_complete($task);
    }

    /**
     * Test adhoc task run from CLI.
     */
    public function test_run_adhoc_from_cli(): void {
        $this->resetAfterTest(true);

        $taskid = 1;

        if (!manager::is_runnable()) {
            $this->markTestSkipped("Cannot run tasks");
        }

        ob_start();
        manager::run_adhoc_from_cli($taskid);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertMatchesRegularExpression(
            sprintf('!admin/cli/adhoc_task.php\W+--id=%d\W+--force!', $taskid),
            $output
        );
    }

    /**
     * Test adhoc class run from CLI.
     */
    public function test_run_all_adhoc_from_cli(): void {
        $this->resetAfterTest(true);

        $classname = 'fake';

        if (!manager::is_runnable()) {
            $this->markTestSkipped("Cannot run tasks");
        }

        ob_start();
        manager::run_all_adhoc_from_cli(false, $classname);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertMatchesRegularExpression(
            sprintf('!admin/cli/adhoc_task.php\W+--classname=%s\W+--force!', $classname),
            $output
        );
    }

    /**
     * Test send messages when adhoc task reaches the max fail delay time.
     *
     * @covers \core\task\failed_task_callbacks::send_failed_task_max_delay_message
     */
    public function test_adhoc_message_max_fail_delay(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $clock = $this->mock_clock_with_frozen();

        // Redirect messages.
        $messagesink = $this->redirectMessages();

        // Create an adhoc task.
        $task = new adhoc_test_task();
        manager::queue_adhoc_task($task);

        $now = $clock->time();

        // Get it from the scheduler, execute it, and mark it as failed.
        $task = manager::get_next_adhoc_task($now);
        $taskid = $task->get_id();
        $task->execute();

        // Catch the message. The task has not reach the max time delay yet.
        manager::adhoc_task_failed($task);
        $messages = $messagesink->get_messages();
        $this->assertCount(0, $messages);

        // Should get the adhoc task immediately.
        $task = manager::get_adhoc_task($taskid);
        $task->set_fail_delay(86400);
        $this->assertInstanceOf('\\core\\task\\adhoc_test_task', $task);
        $this->assertEquals($taskid, $task->get_id());
        $task->execute();

        // Catch the message.
        manager::adhoc_task_failed($task);
        $messages = $messagesink->get_messages();
        $this->assertCount(1, $messages);

        // Get the task and execute it second time.
        $task = manager::get_adhoc_task($taskid);
        // Set the fail delay to 12 hours.
        $task->set_fail_delay(43200);
        $task->execute();
        manager::adhoc_task_failed($task);

        // Catch the message.
        $messages = $messagesink->get_messages();
        $this->assertCount(2, $messages);

        // Get the task and execute it third time.
        $task = manager::get_adhoc_task($taskid);
        // Set the fail delay to 48 hours.
        $task->set_fail_delay(172800);
        $task->execute();
        manager::adhoc_task_failed($task);

        // Catch the message.
        $messages = $messagesink->get_messages();
        $this->assertCount(3, $messages);

        // Check first message information.
        $this->assertStringContainsString('Task failed: Test adhoc class', $messages[0]->subject);
        $this->assertEquals('failedtaskmaxdelay', $messages[0]->eventtype);
        $this->assertEquals('-10', $messages[0]->useridfrom);
        $this->assertEquals('2', $messages[0]->useridto);
        $this->assertEquals('Task logs', $messages[0]->contexturlname);
        $this->assertEquals(
            (new url('/admin/tasklogs.php', ['filter' => get_class($task)]))->out(false),
            $messages[0]->contexturl,
        );

        // Close sink.
        $messagesink->close();
    }

    /**
     * Test that is_adhoc_task_delayed() returns false by default and true after set_soft_retry_delay().
     *
     * @covers \core\task\adhoc_task::is_adhoc_task_delayed
     */
    public function test_is_adhoc_task_delayed(): void {
        $task = new adhoc_test_task();
        $this->assertFalse($task->is_adhoc_task_delayed());

        $task->set_soft_retry_delay();
        $this->assertTrue($task->is_adhoc_task_delayed());
    }

    /**
     * Test that set_soft_retry_delay() accepts null and positive integers.
     *
     * @covers \core\task\adhoc_task::set_soft_retry_delay
     */
    public function test_set_soft_retry_delay_accepts_valid_values(): void {
        $task = new adhoc_test_task();

        // Null triggers exponential backoff.
        $task->set_soft_retry_delay(null);
        $this->assertNull($task->get_soft_retry_delay());
        $this->assertTrue($task->is_adhoc_task_delayed());

        // Positive integer sets an explicit delay.
        $task2 = new adhoc_test_task();
        $task2->set_soft_retry_delay(300);
        $this->assertEquals(300, $task2->get_soft_retry_delay());
        $this->assertTrue($task2->is_adhoc_task_delayed());
    }

    /**
     * Test that set_soft_retry_delay() rejects zero and negative values.
     *
     * @covers \core\task\adhoc_task::set_soft_retry_delay
     * @dataProvider invalid_soft_retry_delay_provider
     * @param int $value The invalid delay value to test.
     */
    public function test_set_soft_retry_delay_rejects_invalid_values(int $value): void {
        $task = new adhoc_test_task();
        $this->expectException(\coding_exception::class);
        $task->set_soft_retry_delay($value);
    }

    /**
     * Data provider for test_set_soft_retry_delay_rejects_invalid_values.
     *
     * @return array
     */
    public static function invalid_soft_retry_delay_provider(): array {
        return [
            'zero'     => [0],
            'negative' => [-1],
        ];
    }

    /**
     * Test logically identical tasks are not scheduled twice.
     *
     * @covers \core\task\manager::queue_adhoc_task
     */
    public function test_identical_tasks_are_deduplicated(): void {
        $this->resetAfterTest();

        $task1 = new \core\task\adhoc_test_task();
        $task1->set_custom_data(['quizid' => 1]);

        $task2 = new \core\task\adhoc_test_task();
        $task2->set_custom_data(['quizid' => 1]);

        \core\task\manager::queue_adhoc_task($task1, true);
        \core\task\manager::queue_adhoc_task($task2, true);

        $records = \core\task\manager::get_adhoc_tasks('\\core\\task\\adhoc_test_task');
        $this->assertCount(1, $records);
    }

    /**
     * Test task classes do not deduplicate.
     *
     * @covers \core\task\manager::queue_adhoc_task
     */
    public function test_different_task_classes_are_not_deduplicated(): void {
        global $DB;
        $this->resetAfterTest();

        $task1 = new \core\task\adhoc_test_task();
        $task1->set_custom_data(['quizid' => 5]);

        $task2 = new \core\task\adhoc_test2_task();
        $task2->set_custom_data(['quizid' => 5]);

        \core\task\manager::queue_adhoc_task($task1, true);
        \core\task\manager::queue_adhoc_task($task2, true);
        $records = $DB->count_records('task_adhoc');
        $this->assertEquals(2, $records);
    }

    /**
     * Test that the generated key matches the expected SHA-1 payload
     *
     * @covers \core\task\manager::build_task_identity_hash
     */
    public function test_key_matches_expected_sha1_payload(): void {
        $this->resetAfterTest();

        $task = new \core\task\adhoc_test_task();
        $task->set_component('core_testcomponent');
        $task->set_custom_data(['alpha' => 1, 'beta' => 2]);
        $task->set_userid(123);

        $hash = manager::build_task_identity_hash($task);

        $component = $task->get_component();
        $classname = manager::get_canonical_class_name($task);
        $userid = $task->get_userid();
        $customdata = json_encode(['alpha' => 1, 'beta' => 2], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $expectedpayload = implode('|', [$component, $classname, $userid, $customdata]);
        $expectedhash = sha1($expectedpayload);

        $this->assertSame($expectedhash, $hash);
    }

    /**
     * Test that the task key changes when custom data is modified.
     *
     * @covers \core\task\manager::build_task_identity_hash
     */
    public function test_key_changes_when_customdata_changes(): void {
        $this->resetAfterTest();

        $task = new \core\task\adhoc_test_task();
        $task->set_component('core_testcomponent');
        $task->set_custom_data(['value' => 1]);
        $key1 = manager::build_task_identity_hash($task);

        $task->set_custom_data(['value' => 2]);
        $key2 = manager::build_task_identity_hash($task);

        $this->assertNotSame($key1, $key2);
    }

    /**
     * Test build_task_identity_hash canonicalizes JSON key order.
     *
     * @covers \core\task\manager::build_task_identity_hash
     */
    public function test_build_task_identity_hash_json_key_order(): void {
        $this->resetAfterTest();

        $task1 = new \core\task\adhoc_test_task();
        $task1->set_component('mod_quiz');
        $task1->set_custom_data(['a' => 1, 'b' => 2]);

        $task2 = new \core\task\adhoc_test_task();
        $task2->set_component('mod_quiz');
        $task2->set_custom_data(['b' => 2, 'a' => 1]);

        $hash1 = manager::build_task_identity_hash($task1);
        $hash2 = manager::build_task_identity_hash($task2);

        $this->assertEquals($hash1, $hash2);
    }

    /**
     * Test that the generated key matches the expected SHA-1 payload
     *
     * @covers \core\task\manager::build_task_identity_hash
     */
    public function test_same_task_different_userid_is_not_deduplicated(): void {
        $this->resetAfterTest();

        $user = \core_user::get_user_by_username('admin');

        // Queue one task with userid.
        $task1 = new \core\task\adhoc_test_task();
        $task1->set_component('core_testcomponent');
        $task1->set_custom_data(['alpha' => 1, 'beta' => 2]);
        $task1->set_userid($user->id);

        \core\task\manager::queue_adhoc_task($task1, true);

        // Queue a second task without userid.
        $task2 = new \core\task\adhoc_test_task();
        $task2->set_component('core_testcomponent');
        $task2->set_custom_data(['alpha' => 1, 'beta' => 2]);

        \core\task\manager::queue_adhoc_task($task2, true);

        // Check that two tasks have been queued as they are different based on the identity hash.
        $this->assertEquals(2, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));
    }

    /**
     * Test that tasks queued without duplicate detection are not removed by a later deduplicated task.
     *
     * @covers \core\task\manager::queue_adhoc_task
     */
    public function test_tasks_without_duplicate_detection_are_not_removed(): void {
        global $DB;

        $this->resetAfterTest();

        $task = new \core\task\adhoc_test_task();
        $task->set_component('core_testcomponent');
        $task->set_custom_data(['alpha' => 1, 'beta' => 2]);

        $taskids = [];
        for ($i = 0; $i < 3; $i++) {
            $taskids[] = \core\task\manager::queue_adhoc_task($task);
        }

        $records = $DB->get_records_list('task_adhoc', 'id', $taskids);
        $identityhashes = array_map(fn(\stdClass $record): string => $record->identityhash, $records);
        $this->assertCount(3, array_unique($identityhashes));

        $taskid = \core\task\manager::queue_adhoc_task($task, true);

        $this->assertContains($taskid, $taskids);
        $this->assertEquals(3, $DB->count_records('task_adhoc', ['component' => 'core_testcomponent']));
    }

    /**
     * Test that an existing task without identityhash is updated when $checkforexisting is true
     *
     * @covers \core\task\manager::queue_adhoc_task
     */
    public function test_existing_task_without_identityhash_is_updated(): void {
        $this->resetAfterTest();
        global $DB;
        // Queue one task without identityhash.
        $task1 = new \core\task\adhoc_test_task();
        $task1->set_component('core_testcomponent');
        $task1->set_custom_data(['alpha' => 1, 'beta' => 2]);

        // Simulate a task queued before identity hashes were introduced.
        $taskid = \core\task\manager::queue_adhoc_task($task1);
        $DB->set_field('task_adhoc', 'identityhash', null, ['id' => $taskid]);
        $queuedtask = $DB->get_record('task_adhoc', ['id' => $taskid]);
        $this->assertNull($queuedtask->identityhash);

        // Queue a second task without identityhash.
        $task2 = new \core\task\adhoc_test_task();
        $task2->set_component('core_testcomponent');
        $task2->set_custom_data(['alpha' => 1, 'beta' => 2]);

        // Queue task and check for existing ones. If found, it should update the task with identityhash.
        \core\task\manager::queue_adhoc_task($task2, true);

        // Verify only one task record exists (no duplicate was created).
        $taskcount = $DB->count_records('task_adhoc', ['component' => 'core_testcomponent']);
        $this->assertEquals(1, $taskcount);

        // The queued task should now have an identityhash.
        $updatedtask = $DB->get_record('task_adhoc', ['id' => $taskid]);
        $this->assertNotEmpty($updatedtask->identityhash);
    }

    /**
     * Test that one existing task without an identity hash is updated and the other matching tasks are retained.
     *
     * @covers \core\task\manager::queue_adhoc_task
     */
    public function test_existing_duplicated_tasks_without_identityhash_are_retained(): void {
        $this->resetAfterTest();
        global $DB;
        // Queue one task without identityhash.
        $task1 = new \core\task\adhoc_test_task();
        $task1->set_component('core_testcomponent');
        $task1->set_custom_data(['alpha' => 1, 'beta' => 2]);

        // Queue multiple tasks without identityhash.
        \core\task\manager::queue_adhoc_task($task1);
        \core\task\manager::queue_adhoc_task($task1);
        \core\task\manager::queue_adhoc_task($task1);
        // Simulate tasks queued before identity hashes were introduced.
        $DB->set_field('task_adhoc', 'identityhash', null, ['component' => 'core_testcomponent']);
        $taskcount = $DB->count_records('task_adhoc', ['component' => 'core_testcomponent']);
        $this->assertEquals(3, $taskcount);

        // Queue the same task this time checking if the task is already queued.
        $task2 = new \core\task\adhoc_test_task();
        $task2->set_component('core_testcomponent');
        $task2->set_custom_data(['alpha' => 1, 'beta' => 2]);

        // Queue task and check for existing ones. If found, it should update the task with identityhash.
        $taskid = \core\task\manager::queue_adhoc_task($task2, true);

        // Verify all legacy tasks remain queued.
        $taskcount = $DB->count_records('task_adhoc', ['component' => 'core_testcomponent']);
        $this->assertEquals(3, $taskcount);

        // The queued task should now have an identityhash.
        $updatedtask = $DB->get_record('task_adhoc', ['id' => $taskid]);
        $this->assertNotEmpty($updatedtask->identityhash);

        // Further duplicate checks should continue returning the retained task without changing the other tasks.
        $this->assertEquals($taskid, \core\task\manager::queue_adhoc_task($task2, true));
        $this->assertEquals(3, $DB->count_records('task_adhoc', ['component' => 'core_testcomponent']));
    }

    /**
     * Test that existing duplicated tasks without identity hashes are retained, including running tasks.
     *
     * @covers \core\task\manager::queue_adhoc_task
     */
    public function test_existing_duplicated_tasks_in_execution_is_not_deleted(): void {
        $this->resetAfterTest();
        global $DB;
        // Queue one task.
        $task1 = new \core\task\adhoc_test_task();
        $task1->set_component('core_testcomponent');
        $task1->set_custom_data(['alpha' => 1, 'beta' => 2]);

        // Queue one task that has started executing.
        $task2 = new \core\task\adhoc_test_task();
        $task2->set_component('core_testcomponent');
        $task2->set_custom_data(['alpha' => 1, 'beta' => 2]);
        $task2->set_timestarted(time());

        // Queue multiple tasks for same component and get id for a task in execution.
        \core\task\manager::queue_adhoc_task($task1);
        \core\task\manager::queue_adhoc_task($task1);
        \core\task\manager::queue_adhoc_task($task1);
        $taskinexecution = \core\task\manager::queue_adhoc_task($task2);
        // Simulate tasks queued before identity hashes were introduced.
        $DB->set_field('task_adhoc', 'identityhash', null, ['component' => 'core_testcomponent']);
        $taskcount = $DB->count_records('task_adhoc', ['component' => 'core_testcomponent']);
        $this->assertEquals(4, $taskcount);

        // Queue the same task this time checking if the task is already queued.
        $task3 = new \core\task\adhoc_test_task();
        $task3->set_component('core_testcomponent');
        $task3->set_custom_data(['alpha' => 1, 'beta' => 2]);

        // Queue task and check for existing ones. If found, it should ignore the one that has started executing.
        \core\task\manager::queue_adhoc_task($task3, true);

        // Verify all tasks remain queued.
        $taskcount = $DB->count_records('task_adhoc', ['component' => 'core_testcomponent']);
        $this->assertEquals(4, $taskcount);

        // The task in execution should remain.
        $updatedtask = $DB->get_record('task_adhoc', ['id' => $taskinexecution]);
        $this->assertNotEmpty($updatedtask->timestarted);
    }

    /**
     * Test that a running task remains deduplicated.
     */
    public function test_running_task_keeps_identityhash(): void {
        global $DB;

        $this->resetAfterTest();
        $clock = $this->mock_clock_with_frozen();
        $now = $clock->time();

        $task = new \core\task\adhoc_test_task();
        $task->set_component('core_testcomponent');
        $task->set_custom_data(['quizid' => 123]);
        $taskid = \core\task\manager::queue_adhoc_task($task, true);

        $record = $DB->get_record('task_adhoc', ['id' => $taskid], '*', MUST_EXIST);
        $this->assertNotEmpty($record->identityhash);

        $runningtask = \core\task\manager::get_next_adhoc_task($now);
        $this->assertNotNull($runningtask);
        \core\task\manager::adhoc_task_starting($runningtask);

        $startedrecord = $DB->get_record('task_adhoc', ['id' => $taskid], '*', MUST_EXIST);
        $this->assertEquals($record->identityhash, $startedrecord->identityhash);
        $this->assertNotEmpty($startedrecord->timestarted);

        $duplicatetask = new \core\task\adhoc_test_task();
        $duplicatetask->set_component('core_testcomponent');
        $duplicatetask->set_custom_data(['quizid' => 123]);
        $duplicatetaskid = \core\task\manager::queue_adhoc_task($duplicatetask, true);

        $this->assertEquals($taskid, $duplicatetaskid);
        $this->assertEquals(1, $DB->count_records('task_adhoc', ['component' => 'core_testcomponent']));

        \core\task\manager::adhoc_task_complete($runningtask);
    }

    /**
     * Test that exhausted adhoc tasks are reset when queued again with duplicate detection.
     *
     * @covers \core\task\manager::queue_adhoc_task
     */
    public function test_reset_task_with_exhausted_attempts_during_insert(): void {
        global $DB;

        $this->resetAfterTest();

        $task = new \core\task\adhoc_test_task();
        $task->set_component('core_testcomponent');
        $task->set_custom_data(['quizid' => 123]);
        $taskid = \core\task\manager::queue_adhoc_task($task, true);
        $this->assertIsInt($taskid);

        // Get the task record from the database.
        $initialrecord = $DB->get_record('task_adhoc', ['component' => 'core_testcomponent'], '*', MUST_EXIST);
        $this->assertEquals(12, $initialrecord->attemptsavailable);
        $this->assertNotEmpty($initialrecord->identityhash);

        // Simulate task failing 12 times by manually updating the database to exhaust attempts.
        $clock = \core\di::get(\core\clock::class);
        $currenttime = $clock->time();

        // Update the task to simulate complete failure (0 attempts, with fail delay and starting time).
        $exhaustedrecord = new \stdClass();
        $exhaustedrecord->id = $initialrecord->id;
        $exhaustedrecord->attemptsavailable = 0;
        $exhaustedrecord->faildelay = 86400;
        // Schedule in future due to failure.
        $exhaustedrecord->nextruntime = $currenttime + 86400;
        $DB->update_record('task_adhoc', $exhaustedrecord);

        // Verify the task is now in failed state.
        $failedrecord = $DB->get_record('task_adhoc', ['id' => $initialrecord->id]);
        $this->assertEquals(0, $failedrecord->attemptsavailable);
        $this->assertEquals(86400, $failedrecord->faildelay);
        $this->assertTrue($failedrecord->nextruntime > $currenttime);

        // Create a new identical task (same component, class, and custom data).
        $newtask = new \core\task\adhoc_test_task();
        $newtask->set_component('core_testcomponent');
        $newtask->set_custom_data(['quizid' => 123]);

        // Store the original values to verify reset.
        $originalattempts = $newtask->get_attempts_available();

        // Queue the identical task again with duplicate detection enabled.
        // This should trigger the reset logic for the exhausted task.
        // Should return the id of existing task (but gets reset).
        $result = \core\task\manager::queue_adhoc_task($newtask, true);
        $this->assertEquals($initialrecord->id, $result);

        // Verify that the existing task was reset.
        $resetrecord = $DB->get_record('task_adhoc', ['id' => $initialrecord->id]);

        // Check that all the reset fields match what we expect from the code.
        $this->assertEquals($originalattempts, $resetrecord->attemptsavailable);
        $this->assertEquals(0, $resetrecord->faildelay);

        // Verify only one task record exists (no duplicate was created).
        $taskcount = $DB->count_records('task_adhoc', ['component' => 'core_testcomponent']);
        $this->assertEquals(1, $taskcount);

        // Verify the identity hash is preserved (same task identity).
        $this->assertEquals($initialrecord->identityhash, $resetrecord->identityhash);
    }

    /**
     * Test that malformed JSON in custom data throws an exception.
     */
    public function test_malformed_json_throws_exception(): void {
        $this->resetAfterTest();

        $malformedjson = '{"invalid": json, "missing": quote}';
        $task = new \core\task\adhoc_test_task();
        $task->set_component('mod_quiz');
        $task->set_custom_data_as_string($malformedjson);

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessageMatches('/Invalid JSON in adhoc task customdata/');

        \core\task\manager::build_task_identity_hash($task);
    }
}
