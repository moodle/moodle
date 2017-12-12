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
     * Test queue_adhoc_task "if not scheduled".
     */
    public function test_queue_adhoc_task_if_not_scheduled() {
        global $DB;

        $this->resetAfterTest(true);

        // Schedule adhoc task.
        $task1 = new \core\task\adhoc_test_task();
        $task1->set_custom_data(array('courseid' => 10));
        $this->assertNotEmpty(\core\task\manager::queue_adhoc_task($task1, true));
        $this->assertEquals(1, $DB->count_records('task_adhoc', array('classname' => '\core\task\adhoc_test_task')));

        // Schedule same adhoc task with different custom data.
        $task2 = new \core\task\adhoc_test_task();
        $task2->set_custom_data(array('courseid' => 1));
        $this->assertNotEmpty(\core\task\manager::queue_adhoc_task($task2, true));
        $this->assertEquals(2, $DB->count_records('task_adhoc', array('classname' => '\core\task\adhoc_test_task')));

        // Schedule same adhoc task with same custom data.
        $task3 = new \core\task\adhoc_test_task();
        $task3->set_custom_data(array('courseid' => 1));
        $this->assertEmpty(\core\task\manager::queue_adhoc_task($task3, true));
        $this->assertEquals(2, $DB->count_records('task_adhoc', array('classname' => '\core\task\adhoc_test_task')));

        // Schedule same adhoc task without custom data.
        $task4 = new \core\task\adhoc_test_task();
        $this->assertNotEmpty(\core\task\manager::queue_adhoc_task($task4, true));
        $this->assertEquals(3, $DB->count_records('task_adhoc', array('classname' => '\core\task\adhoc_test_task')));

        // Schedule same adhoc task without custom data (again).
        $task5 = new \core\task\adhoc_test_task();
        $this->assertEmpty(\core\task\manager::queue_adhoc_task($task5, true));
        $this->assertEquals(3, $DB->count_records('task_adhoc', array('classname' => '\core\task\adhoc_test_task')));

    }
}
