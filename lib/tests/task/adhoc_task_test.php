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
 * Test class for adhoc tasks.
 *
 * @package core
 * @category test
 * @copyright 2013 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\task\manager
 */
class adhoc_task_test extends \advanced_testcase {

    /**
     * Test getting name of task that implements it's own get_name method
     *
     * @covers \core\task\adhoc_task::get_name
     */
    public function test_get_name(): void {
        $task = new \core\task\adhoc_test_task();
        $this->assertEquals('Test adhoc class', $task->get_name());
    }

    /**
     * Test getting name of task that uses the default implementation of get_name
     *
     * @covers \core\task\adhoc_task::get_name
     */
    public function test_get_name_default(): void {
        $task = new \mod_fake\task\adhoc_component_task();
        $this->assertEquals('Adhoc component task', $task->get_name());
    }

    /**
     * Test basic adhoc task execution.
     *
     * @covers ::get_next_adhoc_task
     */
    public function test_get_next_adhoc_task_now() {
        $this->resetAfterTest(true);

        // Create an adhoc task.
        $task = new adhoc_test_task();

        // Queue it.
        manager::queue_adhoc_task($task);

        $now = time();
        // Get it from the scheduler.
        $task = manager::get_next_adhoc_task($now);
        $this->assertInstanceOf('\\core\\task\\adhoc_test_task', $task);
        $task->execute();
        manager::adhoc_task_complete($task);
    }

    /**
     * Test basic adhoc task execution.
     *
     * @covers ::get_next_adhoc_task
     */
    public function test_get_next_adhoc_task_class() {
        $this->resetAfterTest(true);

        // Create an adhoc task.
        $task = new \core\task\adhoc_test_task();

        // Queue it.
        manager::queue_adhoc_task($task);

        $now = time();
        $classname = get_class($task);

        // The task will not be returned.
        $this->assertNull(manager::get_next_adhoc_task($now, true, "{$classname}notexists"));

        // Get it from the scheduler.
        $task = manager::get_next_adhoc_task($now, true, $classname);
        $this->assertInstanceOf('\\core\\task\\adhoc_test_task', $task);
        $task->execute();
        manager::adhoc_task_complete($task);
    }

    /**
     * Test adhoc task failure retry backoff.
     *
     * @covers ::get_next_adhoc_task
     * @covers ::get_adhoc_task
     */
    public function test_get_next_adhoc_task_fail_retry() {
        $this->resetAfterTest(true);

        // Create an adhoc task.
        $task = new adhoc_test_task();
        manager::queue_adhoc_task($task);

        $now = time();

        // Get it from the scheduler, execute it, and mark it as failed.
        $task = manager::get_next_adhoc_task($now);
        $taskid = $task->get_id();
        $task->execute();
        manager::adhoc_task_failed($task);

        // The task will not be returned immediately.
        $this->assertNull(manager::get_next_adhoc_task($now));

        // Should get the adhoc task (retry after delay). Fail it again.
        $task = manager::get_next_adhoc_task($now + 120);
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
     * Test future adhoc task execution.
     * @covers ::get_next_adhoc_task
     */
    public function test_get_next_adhoc_task_future() {
        $this->resetAfterTest(true);

        $now = time();
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
     * @covers ::queue_adhoc_task
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
     * @covers ::queue_adhoc_task
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
     * @covers ::queue_adhoc_task
     */
    public function test_queue_task_for_invalid_component_without_set_component(): void {
        $this->resetAfterTest();

        $task = new \mod_fake\task\adhoc_component_task();

        manager::queue_adhoc_task($task);
        $this->assertDebuggingCalled('Component not set and the class namespace does not match a valid component (mod_fake).');
    }

    /**
     * Test empty set of adhoc tasks
     * @covers ::get_adhoc_tasks
     */
    public function test_get_adhoc_tasks_empty_set() {
        $this->resetAfterTest(true);

        $this->assertEquals([], manager::get_adhoc_tasks('\\core\\task\\adhoc_test_task'));
    }

    /**
     * Test correct set of adhoc tasks is returned for class.
     * @covers ::get_adhoc_tasks
     */
    public function test_get_adhoc_tasks_result_set() {
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
     * @covers ::reschedule_or_queue_adhoc_task
     */
    public function test_reschedule_or_queue_adhoc_task_no_existing() {
        $this->resetAfterTest(true);

        // Schedule adhoc task.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        manager::reschedule_or_queue_adhoc_task($task);
        $this->assertEquals(1, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));
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
     * @covers ::reschedule_or_queue_adhoc_task
     */
    public function test_reschedule_or_queue_adhoc_task_different_data() {
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
     * @covers ::reschedule_or_queue_adhoc_task
     */
    public function test_reschedule_or_queue_adhoc_task_match_no_change() {
        $this->resetAfterTest(true);

        // Schedule adhoc task.
        $task = new adhoc_test_task();
        $task->set_custom_data(['courseid' => 10]);
        $task->set_next_run_time(time() + DAYSECS);
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
     * @covers ::reschedule_or_queue_adhoc_task
     */
    public function test_reschedule_or_queue_adhoc_task_match_update_runtime() {
        $this->resetAfterTest(true);
        $initialruntime = time() + DAYSECS;
        $newruntime = time() + WEEKSECS;

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
     * @covers ::queue_adhoc_task
     */
    public function test_queue_adhoc_task_if_not_scheduled() {
        $this->resetAfterTest(true);
        $user = \core_user::get_user_by_username('admin');

        // Schedule adhoc task.
        $task = new adhoc_test_task();
        $task->set_custom_data(array('courseid' => 10));
        $this->assertNotEmpty(manager::queue_adhoc_task($task, true));
        $this->assertEquals(1, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule adhoc task with a user.
        $task = new adhoc_test_task();
        $task->set_custom_data(array('courseid' => 10));
        $task->set_userid($user->id);
        $this->assertNotEmpty(manager::queue_adhoc_task($task, true));
        $this->assertEquals(2, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task with different custom data.
        $task = new adhoc_test_task();
        $task->set_custom_data(array('courseid' => 1));
        $this->assertNotEmpty(manager::queue_adhoc_task($task, true));
        $this->assertEquals(3, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task with same custom data.
        $task = new adhoc_test_task();
        $task->set_custom_data(array('courseid' => 1));
        $this->assertEmpty(manager::queue_adhoc_task($task, true));
        $this->assertEquals(3, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task with same custom data and a user.
        $task = new adhoc_test_task();
        $task->set_custom_data(array('courseid' => 1));
        $task->set_userid($user->id);
        $this->assertNotEmpty(manager::queue_adhoc_task($task, true));
        $this->assertEquals(4, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task without custom data.
        // Note: This task was created earlier.
        $task = new adhoc_test_task();
        $this->assertNotEmpty(manager::queue_adhoc_task($task, true));
        $this->assertEquals(5, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task without custom data (again).
        $task5 = new adhoc_test_task();
        $this->assertEmpty(manager::queue_adhoc_task($task5, true));
        $this->assertEquals(5, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task without custom data but with a userid.
        $task6 = new adhoc_test_task();
        $user = \core_user::get_user_by_username('admin');
        $task6->set_userid($user->id);
        $this->assertNotEmpty(manager::queue_adhoc_task($task6, true));
        $this->assertEquals(6, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));

        // Schedule same adhoc task again without custom data but with a userid.
        $task6 = new adhoc_test_task();
        $user = \core_user::get_user_by_username('admin');
        $task6->set_userid($user->id);
        $this->assertEmpty(manager::queue_adhoc_task($task6, true));
        $this->assertEquals(6, count(manager::get_adhoc_tasks('core\task\adhoc_test_task')));
    }

    /**
     * Test that when no userid is specified, it returns empty from the DB
     * too.
     * @covers \core\task\adhoc_task::get_userid
     */
    public function test_adhoc_task_user_empty() {
        $this->resetAfterTest(true);

        // Create an adhoc task in future.
        $task = new adhoc_test_task();
        manager::queue_adhoc_task($task);

        // Get it back from the scheduler.
        $now = time();
        $task = manager::get_next_adhoc_task($now);
        manager::adhoc_task_complete($task);

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
        $task = new adhoc_test_task();
        $user = \core_user::get_user_by_username('admin');
        $task->set_userid($user->id);
        manager::queue_adhoc_task($task);

        // Get it back from the scheduler.
        $now = time();
        $task = manager::get_next_adhoc_task($now);
        manager::adhoc_task_complete($task);

        $this->assertEquals($user->id, $task->get_userid());
    }

    /**
     * Test get_concurrency_limit() method to return 0 by default.
     *
     * @covers \core\task\adhoc_task::get_concurrency_limit
     */
    public function test_get_concurrency_limit() {
        $this->resetAfterTest(true);
        $task = new adhoc_test_task();
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
        $task = new adhoc_test_task();
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
        $task = new adhoc_test_task();
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
        $task1 = new adhoc_test_task();
        $task1->set_next_run_time(1510000000);
        $task1->set_custom_data_as_string('Task 1');
        manager::queue_adhoc_task($task1);

        $task2 = new adhoc_test_task();
        $task2->set_next_run_time(1520000000);
        $task2->set_custom_data_as_string('Task 2');
        manager::queue_adhoc_task($task2);

        $task3 = new adhoc_test_task();
        $task3->set_next_run_time(1520000000);
        $task3->set_custom_data_as_string('Task 3');
        manager::queue_adhoc_task($task3);

        // Shuffle tasks.
        $task1->set_next_run_time(1540000000);
        manager::reschedule_or_queue_adhoc_task($task1);

        $task3->set_next_run_time(1530000000);
        manager::reschedule_or_queue_adhoc_task($task3);

        $task2->set_next_run_time(1530000000);
        manager::reschedule_or_queue_adhoc_task($task2);

        // Confirm, that tasks are sorted by nextruntime and then by id (ascending).
        $task = manager::get_next_adhoc_task(time());
        $this->assertEquals('Task 2', $task->get_custom_data_as_string());
        manager::adhoc_task_complete($task);

        $task = manager::get_next_adhoc_task(time());
        $this->assertEquals('Task 3', $task->get_custom_data_as_string());
        manager::adhoc_task_complete($task);

        $task = manager::get_next_adhoc_task(time());
        $this->assertEquals('Task 1', $task->get_custom_data_as_string());
        manager::adhoc_task_complete($task);
    }

    /**
     * Test adhoc task run from CLI.
     * @covers ::run_adhoc_from_cli
     */
    public function test_run_adhoc_from_cli() {
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
     * @covers ::run_all_adhoc_from_cli
     */
    public function test_run_all_adhoc_from_cli() {
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
}
