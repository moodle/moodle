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

/**
 * This file contains unit tests for the 'task running' data.
 *
 * @package   core
 * @category  test
 * @copyright 2019 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class running_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        require_once(__DIR__ . '/../fixtures/task_fixtures.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test for ad-hoc tasks.
     */
    public function test_adhoc_task_running(): void {
        $this->resetAfterTest();

        // Specify lock factory. The reason is that Postgres locks don't work within a single
        // process (i.e. if you try to get a lock that you already locked, it will just let you)
        // which is usually OK but not here where we are simulating running two tasks at once in
        // the same process.
        set_config('lock_factory', '\core\lock\db_record_lock_factory');

        // Create and queue 2 new ad-hoc tasks.
        $task1 = new adhoc_test_task();
        $task1->set_next_run_time(time() - 20);
        manager::queue_adhoc_task($task1);
        $task2 = new adhoc_test2_task();
        $task2->set_next_run_time(time() - 10);
        manager::queue_adhoc_task($task2);

        // Check no tasks are marked running.
        $running = manager::get_running_tasks();
        $this->assertEmpty($running);

        // Mark the first task running and check results.
        $before = time();
        $next1 = manager::get_next_adhoc_task(time());
        manager::adhoc_task_starting($next1);
        $after = time();
        $running = manager::get_running_tasks();
        $this->assertCount(1, $running);
        foreach ($running as $item) {
            $this->assertEquals('adhoc', $item->type);
            $this->assertLessThanOrEqual($after, $item->timestarted);
            $this->assertGreaterThanOrEqual($before, $item->timestarted);
        }

        // Mark the second task running and check results.
        $next2 = manager::get_next_adhoc_task(time());
        manager::adhoc_task_starting($next2);
        $running = manager::get_running_tasks();
        $this->assertCount(2, $running);

        // Second task completes successfully.
        manager::adhoc_task_complete($next2);
        $running = manager::get_running_tasks();
        $this->assertCount(1, $running);

        // First task fails.
        manager::adhoc_task_failed($next1);
        $running = manager::get_running_tasks();
        $this->assertCount(0, $running);
    }

    /**
     * Test for scheduled tasks.
     */
    public function test_scheduled_task_running(): void {
        global $DB;
        $this->resetAfterTest();

        // Check no tasks are marked running.
        $running = manager::get_running_tasks();
        $this->assertEmpty($running);

        // Disable all the tasks, except two, and set those two due to run.
        $DB->set_field_select('task_scheduled', 'disabled', 1, 'classname != ? AND classname != ?',
                ['\core\task\session_cleanup_task', '\core\task\file_trash_cleanup_task']);
        $DB->set_field('task_scheduled', 'nextruntime', 1,
                ['classname' => '\core\task\session_cleanup_task']);
        $DB->set_field('task_scheduled', 'nextruntime', 1,
                ['classname' => '\core\task\file_trash_cleanup_task']);
        $DB->set_field('task_scheduled', 'lastruntime', time() - 1000,
                ['classname' => '\core\task\session_cleanup_task']);
        $DB->set_field('task_scheduled', 'lastruntime', time() - 500,
                ['classname' => '\core\task\file_trash_cleanup_task']);

        // Get the first task and start it off.
        $next1 = manager::get_next_scheduled_task(time());
        $before = time();
        manager::scheduled_task_starting($next1);
        $after = time();
        $running = manager::get_running_tasks();
        $this->assertCount(1, $running);
        foreach ($running as $item) {
            $this->assertLessThanOrEqual($after, $item->timestarted);
            $this->assertGreaterThanOrEqual($before, $item->timestarted);
            $this->assertEquals('\core\task\session_cleanup_task', $item->classname);
        }

        // Mark the second task running and check results. We have to change the times so the other
        // one comes up first, otherwise it repeats the same one.
        $DB->set_field('task_scheduled', 'lastruntime', time() - 1500,
                ['classname' => '\core\task\file_trash_cleanup_task']);

        // Make sure that there is a time gap between task to sort them as expected.
        sleep(1);
        $next2 = manager::get_next_scheduled_task(time());
        manager::scheduled_task_starting($next2);

        // Check default sorting by timestarted.
        $running = manager::get_running_tasks();
        $this->assertCount(2, $running);
        $item = array_shift($running);
        $this->assertEquals('\core\task\session_cleanup_task', $item->classname);
        $item = array_shift($running);
        $this->assertEquals('\core\task\file_trash_cleanup_task', $item->classname);

        // Check sorting by time ASC.
        $running = manager::get_running_tasks('time ASC');
        $this->assertCount(2, $running);
        $item = array_shift($running);
        $this->assertEquals('\core\task\file_trash_cleanup_task', $item->classname);
        $item = array_shift($running);
        $this->assertEquals('\core\task\session_cleanup_task', $item->classname);

        // Complete the file trash one.
        manager::scheduled_task_complete($next2);
        $running = manager::get_running_tasks();
        $this->assertCount(1, $running);

        // Other task fails.
        manager::scheduled_task_failed($next1);
        $running = manager::get_running_tasks();
        $this->assertCount(0, $running);
    }

    /**
     * Test for adhoc task cleanup.
     *
     * @covers \core\task\manager::cleanup_metadata()
     */
    public function test_adhoc_cleanup_metadata(): void {
        global $DB;
        $this->resetAfterTest(true);

        $clock = $this->mock_clock_with_frozen();

        // Specify lock factory to avoid previously mentioned issues with Postgres locks.
        set_config('lock_factory', '\core\lock\db_record_lock_factory');

        // Disable all scheduled tasks except the cleanup task.
        $classname = 'core\task\task_lock_cleanup_task';
        $DB->set_field_select('task_scheduled', 'disabled', 1, 'classname != ?', ["\\$classname"]);
        $DB->set_field('task_scheduled', 'nextruntime', 1, ['classname' => "\\$classname"]);

        // Create an adhoc task.
        $task = new adhoc_test_task();
        $task->set_next_run_time($clock->time() - MINSECS);

        // Queue and start the adhoc task.
        manager::queue_adhoc_task($task);
        $task = manager::get_next_adhoc_task($clock->time());
        manager::adhoc_task_starting($task);

        // Release the lock to simulate an adhoc task that has been destroyed but hasn't been cleaned up.
        $task->get_lock()->release();
        $this->assertCount(1, manager::get_running_tasks());

        // Run the cleanup scheduled task one hour later.
        $clock->bump(HOURSECS);
        $cleanuptask = manager::get_next_scheduled_task($clock->time());
        manager::scheduled_task_starting($cleanuptask);
        logmanager::start_logging($cleanuptask);
        $this->assertCount(2, manager::get_running_tasks());
        $cleanuptask->execute();

        // Confirm the task has been cleaned up.
        $this->assertCount(1, manager::get_running_tasks());

        // Check the task log hasn't been finalised for the cleanup task.
        $record = $DB->get_record('task_log', ['classname' => $classname]);
        $this->assertEmpty($record);

        // Now complete the task and make sure it was successful (0 = success, 1 = fail).
        manager::scheduled_task_complete($cleanuptask);
        $record = $DB->get_record('task_log', ['classname' => $classname]);
        $this->assertEquals(0, $record->result);
    }

    /**
     * Test for scheduled task cleanup.
     *
     * @covers \core\task\manager::cleanup_metadata()
     */
    public function test_scheduled_cleanup_metadata(): void {
        global $DB;
        $this->resetAfterTest(true);

        $clock = $this->mock_clock_with_frozen();

        // Specify lock factory to avoid previously mentioned issues with Postgres locks.
        set_config('lock_factory', '\core\lock\db_record_lock_factory');

        // Disable all scheduled tasks except the cleanup task.
        $classname = 'core\task\task_lock_cleanup_task';
        $DB->set_field_select('task_scheduled', 'disabled', 1, 'classname != ?', ["\\$classname"]);
        $DB->set_field('task_scheduled', 'nextruntime', $clock->time() + MINSECS, ['classname' => "\\$classname"]);

        // Create a new scheduled task.
        $task = new scheduled_test_task();
        $task->set_next_run_time($clock->time() - MINSECS);

        // Insert and start the test scheduled task.
        $DB->insert_record('task_scheduled', manager::record_from_scheduled_task($task));
        $task = manager::get_next_scheduled_task($clock->time());
        manager::scheduled_task_starting($task);

        // Release the lock to simulate a scheduled task that has been destroyed but hasn't been cleaned up.
        $task->get_lock()->release();
        $this->assertCount(1, manager::get_running_tasks());

        // Run the cleanup scheduled task one hour later.
        $clock->bump(HOURSECS);
        $cleanuptask = manager::get_next_scheduled_task($clock->time());
        manager::scheduled_task_starting($cleanuptask);
        logmanager::start_logging($cleanuptask);
        $this->assertCount(2, manager::get_running_tasks());
        $cleanuptask->execute();

        // Confirm the task has been cleaned up.
        $this->assertCount(1, manager::get_running_tasks());

        // Check the task log hasn't been finalised for the cleanup task.
        $record = $DB->get_record('task_log', ['classname' => $classname]);
        $this->assertEmpty($record);

        // Now complete the task and make sure it was successful (0 = success, 1 = fail).
        manager::scheduled_task_complete($cleanuptask);
        $record = $DB->get_record('task_log', ['classname' => $classname]);
        $this->assertEquals(0, $record->result);
    }
}
