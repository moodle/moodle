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
 * This file contains the unittests for adhock tasks.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2013 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/fixtures/task_fixtures.php');


/**
 * Test class for adhoc tasks.
 *
 * @package core
 * @category task
 * @copyright 2013 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\task\manager
 */
class core_adhoc_task_testcase extends advanced_testcase {

    /**
     * Test basic adhoc task execution.
     */
    public function test_get_next_adhoc_task_now() {
        $this->resetAfterTest(true);

        // Create an adhoc task.
        $task = new \core\task\adhoc_test_task();

        // Queue it.
        \core\task\manager::queue_adhoc_task($task);

        $now = time();
        // Get it from the scheduler.
        $task = \core\task\manager::get_next_adhoc_task($now);
        $this->assertInstanceOf('\\core\\task\\adhoc_test_task', $task);
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);
    }

    /**
     * Test adhoc task failure retry backoff.
     *
     * @covers ::get_next_adhoc_task
     */
    public function test_get_next_adhoc_task_fail_retry() {
        $this->resetAfterTest(true);

        // Create an adhoc task.
        $task = new \core\task\adhoc_test_task();
        \core\task\manager::queue_adhoc_task($task);

        $now = time();

        // Get it from the scheduler, execute it, and mark it as failed.
        $task = \core\task\manager::get_next_adhoc_task($now);
        $task->execute();
        \core\task\manager::adhoc_task_failed($task);

        // The task will not be returned immediately.
        $this->assertNull(\core\task\manager::get_next_adhoc_task($now));

        // Should get the adhoc task (retry after delay).
        $task = \core\task\manager::get_next_adhoc_task($now + 120);
        $this->assertInstanceOf('\\core\\task\\adhoc_test_task', $task);
        $task->execute();

        \core\task\manager::adhoc_task_complete($task);

        // Should not get any task.
        $this->assertNull(\core\task\manager::get_next_adhoc_task($now));
    }

    /**
     * Test future adhoc task execution.
     * @covers ::get_next_adhoc_task
     */
    public function test_get_next_adhoc_task_future() {
        $this->resetAfterTest(true);

        $now = time();
        // Create an adhoc task in future.
        $task = new \core\task\adhoc_test_task();
        $task->set_next_run_time($now + 1000);
        \core\task\manager::queue_adhoc_task($task);

        // Fetching the next task should not return anything.
        $this->assertNull(\core\task\manager::get_next_adhoc_task($now));

        // Fetching in the future should return the task.
        $task = \core\task\manager::get_next_adhoc_task($now + 1020);
        $this->assertInstanceOf('\\core\\task\\adhoc_test_task', $task);
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);
    }

    /**
     * Test queueing an adhoc task belonging to a component, where we set the task component accordingly
     * @covers ::queue_adhoc_task
     */
    public function test_queue_adhoc_task_for_component(): void {
        $this->resetAfterTest();

        $task = new \mod_forum\task\refresh_forum_post_counts();
        $task->set_component('mod_test');

        \core\task\manager::queue_adhoc_task($task);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test queueing an adhoc task belonging to a component, where we do not set the task component
     * @covers ::queue_adhoc_task
     */
    public function test_queue_task_for_component_without_set_component(): void {
        $this->resetAfterTest();

        $task = new \mod_forum\task\refresh_forum_post_counts();

        \core\task\manager::queue_adhoc_task($task);
        $this->assertDebuggingNotCalled();

        // Assert the missing component was set.
        $this->assertEquals('mod_forum', $task->get_component());
    }

    /**
     * Test queueing an adhoc task belonging to an invalid component, where we do not set the task component
     * @covers ::queue_adhoc_task
     */
    public function test_queue_task_for_invalid_component_without_set_component(): void {
        $this->resetAfterTest();

        $task = new \mod_fake\task\adhoc_component_task();

        \core\task\manager::queue_adhoc_task($task);
        $this->assertDebuggingCalled('Component not set and the class namespace does not match a valid component (mod_fake).');
    }

    /**
     * Test empty set of adhoc tasks
     * @covers ::get_adhoc_tasks
     */
    public function test_get_adhoc_tasks_empty_set() {
        $this->resetAfterTest(true);

        $this->assertEquals([], \core\task\manager::get_adhoc_tasks('\\core\\task\\adhoc_test_task'));
    }

    /**
     * Test correct set of adhoc tasks is returned for class.
     * @covers ::get_adhoc_tasks
     */
    public function test_get_adhoc_tasks_result_set() {
        $this->resetAfterTest(true);

        for ($i = 0; $i < 3; $i++) {
            $task = new \core\task\adhoc_test_task();
            \core\task\manager::queue_adhoc_task($task);
        }

        for ($i = 0; $i < 3; $i++) {
            $task = new \core\task\adhoc_test2_task();
            \core\task\manager::queue_adhoc_task($task);
        }

        $adhoctests = \core\task\manager::get_adhoc_tasks('\\core\\task\\adhoc_test_task');
        $adhoctest2s = \core\task\manager::get_adhoc_tasks('\\core\\task\\adhoc_test2_task');

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
     * @covers ::reschedule_or_queue_adhoc_task
     */
    public function test_reschedule_or_queue_adhoc_task_no_existing() {
        $this->resetAfterTest(true);

        // Schedule adhoc task.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        \core\task\manager::reschedule_or_queue_adhoc_task($task);
        $this->assertEquals(1, count(\core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task')));
    }

    /**
     * Ensure that the reschedule_or_queue_adhoc_task function will schedule a new task if a task for the same user does
     * not exist.
     * @covers ::reschedule_or_queue_adhoc_task
     */
    public function test_reschedule_or_queue_adhoc_task_different_user() {
        $this->resetAfterTest(true);
        $user = \core_user::get_user_by_username('admin');

        // Schedule adhoc task.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        \core\task\manager::reschedule_or_queue_adhoc_task($task);

        // Schedule adhoc task for a different user.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $task->set_userid($user->id);
        \core\task\manager::reschedule_or_queue_adhoc_task($task);

        $this->assertEquals(2, count(\core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task')));
    }

    /**
     * Ensure that the reschedule_or_queue_adhoc_task function will schedule a new task if a task with different custom
     * data exists.
     * @covers ::reschedule_or_queue_adhoc_task
     */
    public function test_reschedule_or_queue_adhoc_task_different_data() {
        $this->resetAfterTest(true);

        // Schedule adhoc task.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        \core\task\manager::reschedule_or_queue_adhoc_task($task);

        // Schedule adhoc task for a different user.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(['courseid' => 11]);
        \core\task\manager::reschedule_or_queue_adhoc_task($task);

        $this->assertEquals(2, count(\core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task')));
    }

    /**
     * Ensure that the reschedule_or_queue_adhoc_task function will not make any change for matching data if no time was
     * specified.
     * @covers ::reschedule_or_queue_adhoc_task
     */
    public function test_reschedule_or_queue_adhoc_task_match_no_change() {
        $this->resetAfterTest(true);

        // Schedule adhoc task.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $task->set_next_run_time(time() + DAYSECS);
        \core\task\manager::reschedule_or_queue_adhoc_task($task);

        $before = \core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task');

        // Schedule the task again but do not specify a time.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        \core\task\manager::reschedule_or_queue_adhoc_task($task);

        $this->assertEquals(1, count(\core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task')));
        $this->assertEquals($before, \core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task'));
    }

    /**
     * Ensure that the reschedule_or_queue_adhoc_task function will update the run time if there are planned changes.
     * @covers ::reschedule_or_queue_adhoc_task
     */
    public function test_reschedule_or_queue_adhoc_task_match_update_runtime() {
        $this->resetAfterTest(true);
        $initialruntime = time() + DAYSECS;
        $newruntime = time() + WEEKSECS;

        // Schedule adhoc task.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $task->set_next_run_time($initialruntime);
        \core\task\manager::reschedule_or_queue_adhoc_task($task);

        $before = \core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task');

        // Schedule the task again.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $task->set_next_run_time($newruntime);
        \core\task\manager::reschedule_or_queue_adhoc_task($task);

        $tasks = \core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task');
        $this->assertEquals(1, count($tasks));
        $this->assertNotEquals($before, $tasks);
        $firsttask = reset($tasks);
        $this->assertEquals($newruntime, $firsttask->get_next_run_time());
    }

    /**
     * Test queue_adhoc_task "if not scheduled".
     * @covers ::queue_adhoc_task
     */
    public function test_queue_adhoc_task_if_not_scheduled() {
        $this->resetAfterTest(true);
        $user = \core_user::get_user_by_username('admin');

        // Schedule adhoc task.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(array('courseid' => 10));
        $this->assertNotEmpty(\core\task\manager::queue_adhoc_task($task, true));
        $this->assertEquals(1, count(\core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule adhoc task with a user.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(array('courseid' => 10));
        $task->set_userid($user->id);
        $this->assertNotEmpty(\core\task\manager::queue_adhoc_task($task, true));
        $this->assertEquals(2, count(\core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task with different custom data.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(array('courseid' => 1));
        $this->assertNotEmpty(\core\task\manager::queue_adhoc_task($task, true));
        $this->assertEquals(3, count(\core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task with same custom data.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(array('courseid' => 1));
        $this->assertEmpty(\core\task\manager::queue_adhoc_task($task, true));
        $this->assertEquals(3, count(\core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task with same custom data and a user.
        $task = new \core\task\adhoc_test_task();
        $task->set_custom_data(array('courseid' => 1));
        $task->set_userid($user->id);
        $this->assertNotEmpty(\core\task\manager::queue_adhoc_task($task, true));
        $this->assertEquals(4, count(\core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task without custom data.
        // Note: This task was created earlier.
        $task = new \core\task\adhoc_test_task();
        $this->assertNotEmpty(\core\task\manager::queue_adhoc_task($task, true));
        $this->assertEquals(5, count(\core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task without custom data (again).
        $task5 = new \core\task\adhoc_test_task();
        $this->assertEmpty(\core\task\manager::queue_adhoc_task($task5, true));
        $this->assertEquals(5, count(\core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task without custom data but with a userid.
        $task6 = new \core\task\adhoc_test_task();
        $user = \core_user::get_user_by_username('admin');
        $task6->set_userid($user->id);
        $this->assertNotEmpty(\core\task\manager::queue_adhoc_task($task6, true));
        $this->assertEquals(6, count(\core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task again without custom data but with a userid.
        $task6 = new \core\task\adhoc_test_task();
        $user = \core_user::get_user_by_username('admin');
        $task6->set_userid($user->id);
        $this->assertEmpty(\core\task\manager::queue_adhoc_task($task6, true));
        $this->assertEquals(6, count(\core\task\manager::get_adhoc_tasks('core\task\adhoc_test_task')));
    }

    /**
     * Test that when no userid is specified, it returns empty from the DB
     * too.
     * @covers \core\task\adhoc_task::get_userid
     */
    public function test_adhoc_task_user_empty() {
        $this->resetAfterTest(true);

        // Create an adhoc task in future.
        $task = new \core\task\adhoc_test_task();
        \core\task\manager::queue_adhoc_task($task);

        // Get it back from the scheduler.
        $now = time();
        $task = \core\task\manager::get_next_adhoc_task($now);
        \core\task\manager::adhoc_task_complete($task);

        $this->assertEmpty($task->get_userid());
    }

    /**
     * Test that when a userid is specified, that userid is subsequently
     * returned.
     *
     * @covers \core\task\adhoc_task::set_userid
     * @covers \core\task\adhoc_task::get_userid
     */
    public function test_adhoc_task_user_set() {
        $this->resetAfterTest(true);

        // Create an adhoc task in future.
        $task = new \core\task\adhoc_test_task();
        $user = \core_user::get_user_by_username('admin');
        $task->set_userid($user->id);
        \core\task\manager::queue_adhoc_task($task);

        // Get it back from the scheduler.
        $now = time();
        $task = \core\task\manager::get_next_adhoc_task($now);
        \core\task\manager::adhoc_task_complete($task);

        $this->assertEquals($user->id, $task->get_userid());
    }

    /**
     * Test get_concurrency_limit() method to return 0 by default.
     *
     * @covers \core\task\adhoc_task::get_concurrency_limit
     */
    public function test_get_concurrency_limit() {
        $this->resetAfterTest(true);
        $task = new \core\task\adhoc_test_task();
        $concurrencylimit = $task->get_concurrency_limit();
        $this->assertEquals(0, $concurrencylimit);
    }

    /**
     * Test get_concurrency_limit() method to return a default value set in config.
     * @covers \core\task\adhoc_task::get_concurrency_limit
     */
    public function test_get_concurrency_limit_default() {
        $this->resetAfterTest(true);
        set_config('task_concurrency_limit_default', 10);
        $task = new \core\task\adhoc_test_task();
        $concurrencylimit = $task->get_concurrency_limit();
        $this->assertEquals(10, $concurrencylimit);
    }

    /**
     * Test get_concurrency_limit() method to return a value for specific task class.
     * @covers \core\task\adhoc_task::get_concurrency_limit
     */
    public function test_get_concurrency_limit_for_task() {
        global $CFG;
        $this->resetAfterTest(true);
        set_config('task_concurrency_limit_default', 10);
        $CFG->task_concurrency_limit = array('core\task\adhoc_test_task' => 5);
        $task = new \core\task\adhoc_test_task();
        $concurrencylimit = $task->get_concurrency_limit();
        $this->assertEquals(5, $concurrencylimit);
    }

    /**
     * Test adhoc task sorting.
     * @covers ::get_next_adhoc_task
     */
    public function test_get_next_adhoc_task_sorting() {
        $this->resetAfterTest(true);

        // Create adhoc tasks.
        $task1 = new \core\task\adhoc_test_task();
        $task1->set_next_run_time(1510000000);
        $task1->set_custom_data_as_string('Task 1');
        \core\task\manager::queue_adhoc_task($task1);

        $task2 = new \core\task\adhoc_test_task();
        $task2->set_next_run_time(1520000000);
        $task2->set_custom_data_as_string('Task 2');
        \core\task\manager::queue_adhoc_task($task2);

        $task3 = new \core\task\adhoc_test_task();
        $task3->set_next_run_time(1520000000);
        $task3->set_custom_data_as_string('Task 3');
        \core\task\manager::queue_adhoc_task($task3);

        // Shuffle tasks.
        $task1->set_next_run_time(1540000000);
        \core\task\manager::reschedule_or_queue_adhoc_task($task1);

        $task3->set_next_run_time(1530000000);
        \core\task\manager::reschedule_or_queue_adhoc_task($task3);

        $task2->set_next_run_time(1530000000);
        \core\task\manager::reschedule_or_queue_adhoc_task($task2);

        // Confirm, that tasks are sorted by nextruntime and then by id (ascending).
        $task = \core\task\manager::get_next_adhoc_task(time());
        $this->assertEquals('Task 2', $task->get_custom_data_as_string());
        \core\task\manager::adhoc_task_complete($task);

        $task = \core\task\manager::get_next_adhoc_task(time());
        $this->assertEquals('Task 3', $task->get_custom_data_as_string());
        \core\task\manager::adhoc_task_complete($task);

        $task = \core\task\manager::get_next_adhoc_task(time());
        $this->assertEquals('Task 1', $task->get_custom_data_as_string());
        \core\task\manager::adhoc_task_complete($task);
    }
}
