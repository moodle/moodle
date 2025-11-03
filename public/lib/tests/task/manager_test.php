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

// We need to keep this here because there is a provider
// needing \core\task\adhoc_test_task and cannot move it
// to setUpBeforeClass() or similar. Whenever we allow to
// autoload fixtures, this can be removed.
require_once(__DIR__ . '/../fixtures/task_fixtures.php');

/**
 * This file contains the unit tests for the task manager.
 *
 * @package   core
 * @category  test
 * @copyright 2019 Brendan Heywood <brendan@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\task\manager
 */
final class manager_test extends \advanced_testcase {
    /**
     * Data provider for test_get_candidate_adhoc_tasks.
     *
     * @return array
     */
    public static function get_candidate_adhoc_tasks_provider(): array {
        return [
            [
                'concurrencylimit' => 5,
                'limit' => 100,
                'pertasklimits' => [],
                'tasks' => [
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                ],
                'expected' => [
                    adhoc_test_task::class,
                    adhoc_test_task::class,
                    adhoc_test_task::class,
                    adhoc_test_task::class,
                    adhoc_test_task::class,
                ],
            ],
            [
                'concurrencylimit' => 5,
                'limit' => 100,
                'pertasklimits' => [],
                'tasks' => [
                    new adhoc_test_task(time() - 20, time()),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                ],
                'expected' => [
                    adhoc_test_task::class,
                    adhoc_test_task::class,
                    adhoc_test_task::class,
                    adhoc_test_task::class,
                ],
            ],
            [
                'concurrencylimit' => 1,
                'limit' => 100,
                'pertasklimits' => [],
                'tasks' => [
                    new adhoc_test_task(time() - 20, time()),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                ],
                'expected' => [],
            ],
            [
                'concurrencylimit' => 2,
                'limit' => 100,
                'pertasklimits' => [],
                'tasks' => [
                    new adhoc_test_task(time() - 20, time()),
                    new adhoc_test_task(time() - 20, time()),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                ],
                'expected' => [],
            ],
            [
                'concurrencylimit' => 2,
                'limit' => 100,
                'pertasklimits' => [],
                'tasks' => [
                    new adhoc_test_task(time() - 20, time()),
                    new adhoc_test_task(time() - 20, time()),
                    new adhoc_test2_task(time() - 20, time()),
                    new adhoc_test2_task(time() - 20, time()),
                    new adhoc_test3_task(time() - 20, null),
                ],
                'expected' => [adhoc_test3_task::class],
            ],
            [
                'concurrencylimit' => 2,
                'limit' => 2,
                'pertasklimits' => [],
                'tasks' => [
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test2_task(time() - 20, null),
                ],
                'expected' => [
                    adhoc_test_task::class,
                    adhoc_test_task::class,
                ],
            ],
            [
                'concurrencylimit' => 2,
                'limit' => 2,
                'pertasklimits' => [],
                'tasks' => [
                    new adhoc_test_task(time() - 20, time()),
                    new adhoc_test_task(time() - 20, time()),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test2_task(time() - 20, null),
                ],
                'expected' => [
                    adhoc_test2_task::class,
                ],
            ],
            [
                'concurrencylimit' => 3,
                'limit' => 100,
                'pertasklimits' => [],
                'tasks' => [
                    new adhoc_test_task(time() - 20, time()),
                    new adhoc_test_task(time() - 20, time()),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test2_task(time() - 20, time()),
                    new adhoc_test2_task(time() - 20, time()),
                    new adhoc_test2_task(time() - 20, null),
                    new adhoc_test3_task(time() - 20, time()),
                    new adhoc_test3_task(time() - 20, time()),
                    new adhoc_test3_task(time() - 20, null),
                    new adhoc_test4_task(time() - 20, time()),
                    new adhoc_test4_task(time() - 20, time()),
                    new adhoc_test4_task(time() - 20, null),
                    new adhoc_test5_task(time() - 20, time()),
                    new adhoc_test5_task(time() - 20, time()),
                    new adhoc_test5_task(time() - 20, null),
                ],
                'expected' => [
                    adhoc_test_task::class,
                    adhoc_test2_task::class,
                    adhoc_test3_task::class,
                    adhoc_test4_task::class,
                    adhoc_test5_task::class,
                ],
            ],
            [
                'concurrencylimit' => 3,
                'limit' => 100,
                'pertasklimits' => [
                    'adhoc_test_task' => 2,
                    'adhoc_test2_task' => 2,
                    'adhoc_test3_task' => 2,
                    'adhoc_test4_task' => 2,
                    'adhoc_test5_task' => 2,
                ],
                'tasks' => [
                    new adhoc_test_task(time() - 20, time()),
                    new adhoc_test_task(time() - 20, time()),
                    new adhoc_test_task(time() - 20, null),
                    new adhoc_test2_task(time() - 20, time()),
                    new adhoc_test2_task(time() - 20, time()),
                    new adhoc_test2_task(time() - 20, null),
                    new adhoc_test3_task(time() - 20, time()),
                    new adhoc_test3_task(time() - 20, time()),
                    new adhoc_test3_task(time() - 20, null),
                    new adhoc_test4_task(time() - 20, time()),
                    new adhoc_test4_task(time() - 20, time()),
                    new adhoc_test4_task(time() - 20, null),
                    new adhoc_test5_task(time() - 20, time()),
                    new adhoc_test5_task(time() - 20, time()),
                    new adhoc_test5_task(time() - 20, null),
                ],
                'expected' => [],
            ],
        ];
    }

    /**
     * Test that the candidate adhoc tasks are returned in the right order.
     *
     * @dataProvider get_candidate_adhoc_tasks_provider
     *
     * @param int $concurrencylimit The max number of runners each task can consume
     * @param int $limit SQL limit
     * @param array $pertasklimits Per-task limits
     * @param array $tasks Array of tasks to put in DB and retrieve
     * @param array $expected Array of expected classnames
     */
    public function test_get_candidate_adhoc_tasks(
        int $concurrencylimit,
        int $limit,
        array $pertasklimits,
        array $tasks,
        array $expected
    ): void {
        $this->resetAfterTest();

        foreach ($tasks as $task) {
            manager::queue_adhoc_task($task);
        }

        $candidates = manager::get_candidate_adhoc_tasks(time(), $limit, $concurrencylimit, $pertasklimits);
        $this->assertEquals(
            array_map(
                function (string $classname): string {
                    return '\\' . $classname;
                },
                $expected
            ),
            array_column($candidates, 'classname')
        );
    }

    /**
     * Test that adhoc tasks are set as failed when shutdown is called during execution.
     */
    public function test_adhoc_task_running_will_fail_when_shutdown(): void {
        $this->resetAfterTest();
        $this->preventResetByRollback();

        $task1 = new adhoc_test_task();
        $task1->set_next_run_time(time() - 20);
        manager::queue_adhoc_task($task1);

        $next1 = manager::get_next_adhoc_task(time());
        \core\task\manager::adhoc_task_starting($next1);

        self::assertEmpty(manager::get_failed_adhoc_tasks());

        // Trigger shutdown handler.
        \core\shutdown_manager::shutdown_handler();

        $failedtasks = manager::get_failed_adhoc_tasks();

        self::assertCount(1, $failedtasks);
        self::assertEquals($next1->get_id(), $failedtasks[0]->get_id());
    }

    /**
     * Test that scheduled tasks are set as failed when shutdown is called during execution.
     */
    public function test_scheduled_task_running_will_fail_when_shutdown(): void {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();

        // Disable all the tasks, so we can insert our own and be sure it's the only one being run.
        $DB->set_field('task_scheduled', 'disabled', 1);

        $task1 = new scheduled_test_task();
        $task1->set_minute('*');
        $task1->set_next_run_time(time() - HOURSECS);
        $DB->insert_record('task_scheduled', manager::record_from_scheduled_task($task1));

        $next1 = \core\task\manager::get_next_scheduled_task(time());
        \core\task\manager::scheduled_task_starting($next1);

        $running = manager::get_running_tasks();
        $this->assertCount(1, $running);

        // Trigger shutdown handler.
        \core\shutdown_manager::shutdown_handler();

        $running = manager::get_running_tasks();
        $this->assertCount(0, $running);

        $scheduledtask1 = manager::get_scheduled_task(scheduled_test_task::class);
        self::assertGreaterThan($next1->get_fail_delay(), $scheduledtask1->get_fail_delay());
    }

    public function test_get_next_adhoc_task_will_respect_failed_tasks(): void {
        // Create three tasks, one is burned on the first get_next_adhoc_task() call to build up the cache,
        // the second will be set to failed and the third is required to make the "uniquetasksinqueue" query
        // within the get_next_adhoc_task() function not returning a different count of remaining unique tasks.
        manager::queue_adhoc_task(new adhoc_test_task());
        manager::queue_adhoc_task(new adhoc_test_task());
        manager::queue_adhoc_task(new adhoc_test_task());
        $timestart = time();

        $candidates = manager::get_candidate_adhoc_tasks($timestart, 4, null);
        $this->assertEquals(count($candidates), 3);
        $task1 = manager::adhoc_task_from_record(array_shift($candidates));
        $task2 = manager::adhoc_task_from_record(array_shift($candidates));
        $task3 = manager::adhoc_task_from_record(array_shift($candidates));

        // Build up the cache by getting the first task.
        $task = manager::get_next_adhoc_task($timestart);
        // Release the lock by completing the task to avoid "A lock was created but not released" error if the assertion fails.
        manager::adhoc_task_complete($task);
        $this->assertEquals($task->get_id(), $task1->get_id());

        // Make $task2 failed...
        try {
            // Expecting "Error: Call to a member function release() on null" as the task was not locked before.
            manager::adhoc_task_failed($task2);
        } catch (\Throwable $t) {
            // Ignoring "Call to a member function release() on null" and throw anything else.
            if ($t->getMessage() != "Call to a member function release() on null") {
                throw $t;
            }
        }
        $task = manager::get_next_adhoc_task($timestart);
        // Release the lock by completing the task to avoid "A lock was created but not released" error if the assertion fails.
        manager::adhoc_task_complete($task);
        // Task $task2 should not be returned because it has failed meanwhile and
        // therefore has its nextruntime in the future...
        $this->assertNotEquals($task->get_id(), $task2->get_id());

        // Just to make sure check that the complete queue is as expected.
        $this->assertEquals($task->get_id(), $task3->get_id());
        // Now the queue should be empty...
        $task = manager::get_next_adhoc_task($timestart);
        $this->assertNull($task);

        $this->resetAfterTest();
    }

    /**
     * Test verifying \core\task\manager behaviour for scheduled tasks when dealing with deprecated plugin types.
     *
     * This only verifies that existing tasks will not be listed, or returned for execution via existing APIs, like:
     * - {@see \core\task\manager::get_all_scheduled_tasks}
     * - {@see \core\task\manager::get_next_scheduled_task}
     *
     * I.e. Nothing prevents task->execute() from running if called directly.
     *
     * @return void
     */
    public function test_scheduled_tasks_deprecated_plugintype(): void {
        $this->resetAfterTest();
        global $DB, $CFG;

        $fakepluginroot = $CFG->libdir . '/tests/fixtures/fakeplugins/fake/fullfeatured';
        require_once($fakepluginroot . '/classes/plugininfo/fullsubtype.php');
        require_once($fakepluginroot . '/classes/plugininfo/fulldeprecatedsubtype.php');
        require_once($fakepluginroot . '/fullsubtype/example/classes/task/scheduled_test.php');
        require_once($fakepluginroot . '/fulldeprecatedsubtype/test/classes/task/scheduled_test.php');

        // Inject stub plugininfo instances into a stub plugin manager, then inject that into the static cache via reflection.
        // When the manager code calls core_plugin_manager::instance(), it'll get back the stub.
        $stubavailableplugininfo = $this->createStub(\fake_fullfeatured\plugininfo\fullsubtype::class);
        $stubavailableplugininfo->method('is_deprecated')->willReturn(false);
        $stubavailableplugininfo->component = "fullsubtype_example";
        $stubdeprecatedplugininfo = $this->createStub(\fake_fullfeatured\plugininfo\fulldeprecatedsubtype::class);
        $stubdeprecatedplugininfo->method('is_deprecated')->willReturn(true);
        $stubdeprecatedplugininfo->component = "fulldeprecatedsubtype_test";

        $stubpluginman = $this->createStub(\core_plugin_manager::class);
        $stubpluginman
            ->method('get_plugin_info')
            ->will($this->returnValueMap([
                ['fullsubtype_example', $stubavailableplugininfo],
                ['fulldeprecatedsubtype_test', $stubdeprecatedplugininfo],
            ]));

        $pluginman = new \ReflectionClass(\core_plugin_manager::class);
        $pluginman->setStaticPropertyValue('singletoninstance', $stubpluginman);

        $DB->delete_records('task_scheduled');

        // Non-deprecated plugin type: is listed and is returned during scheduling.
        $scheduledtask = new \fullsubtype_example\task\scheduled_test();
        $DB->insert_record('task_scheduled', \core\task\manager::record_from_scheduled_task($scheduledtask));
        $records = $DB->get_records('task_scheduled');
        $this->assertCount(1, $records);

        $this->assertInstanceOf(
            \fullsubtype_example\task\scheduled_test::class,
            \core\task\manager::get_all_scheduled_tasks()[0]
        );
        $now = time();
        $task = \core\task\manager::get_next_scheduled_task($now);
        $this->assertInstanceOf(\fullsubtype_example\task\scheduled_test::class, $task);
        manager::scheduled_task_complete($task);

        // Deprecated plugin type: isn't listed and isn't returned during scheduling.
        $DB->delete_records('task_scheduled');
        $scheduledtask = new \fulldeprecatedsubtype_test\task\scheduled_test();
        $DB->insert_record('task_scheduled', \core\task\manager::record_from_scheduled_task($scheduledtask));
        $records = $DB->get_records('task_scheduled');
        $this->assertCount(1, $records);

        $this->assertEmpty(\core\task\manager::get_all_scheduled_tasks());
        $this->assertNull(\core\task\manager::get_next_scheduled_task($now));

        // Task can still be executed directly.
        $this->expectExceptionMessage('task->execute() called');
        $scheduledtask->execute();
    }

    /**
     * Test verifying \core\task\manager behaviour for adhoc tasks when dealing with deprecated plugin types.
     *
     * This only verifies that new tasks cannot be queued via:
     * - {@see \core\task\manager::queue_adhoc_task}
     * - {@see \core\task\manager::get_next_adhoc_task()}
     *
     * I.e. Nothing prevents task->execute() from running if called directly.
     *
     * @return void
     */
    public function test_queue_adhoc_task_deprecated_plugintype(): void {
        $this->resetAfterTest();
        global $DB, $CFG;

        $fakepluginroot = $CFG->libdir . '/tests/fixtures/fakeplugins/fake/fullfeatured';
        require_once($fakepluginroot . '/classes/plugininfo/fullsubtype.php');
        require_once($fakepluginroot . '/classes/plugininfo/fulldeprecatedsubtype.php');
        require_once($fakepluginroot . '/classes/plugininfo/fulldeletedsubtype.php');
        require_once($fakepluginroot . '/fullsubtype/example/classes/task/adhoc_test.php');
        require_once($fakepluginroot . '/fulldeprecatedsubtype/test/classes/task/adhoc_test.php');
        require_once($fakepluginroot . '/fulldeletedsubtype/demo/classes/task/adhoc_test.php');

        // Inject stub plugininfo instances into a stub plugin manager, then inject that into the static cache via reflection.
        // When the manager code calls core_plugin_manager::instance(), it'll get back the stub.
        $stubavailableplugininfo = $this->createStub(\fake_fullfeatured\plugininfo\fullsubtype::class);
        $stubavailableplugininfo->method('is_deprecated')->willReturn(false);
        $stubavailableplugininfo->method('is_deleted')->willReturn(false);
        $stubavailableplugininfo->component = "fullsubtype_example";
        $stubdeprecatedplugininfo = $this->createStub(\fake_fullfeatured\plugininfo\fulldeprecatedsubtype::class);
        $stubdeprecatedplugininfo->method('is_deprecated')->willReturn(true);
        $stubdeprecatedplugininfo->method('is_deleted')->willReturn(false);
        $stubdeprecatedplugininfo->component = "fulldeprecatedsubtype_test";
        $stubdeletedplugininfo = $this->createStub(\fake_fullfeatured\plugininfo\fulldeletedsubtype::class);
        $stubdeletedplugininfo->method('is_deprecated')->willReturn(false);
        $stubdeletedplugininfo->method('is_deleted')->willReturn(true);
        $stubdeletedplugininfo->component = "fulldeletedsubtype_demo";
        $stubpluginman = $this->createStub(\core_plugin_manager::class);
        $stubpluginman->method('get_plugin_info')
            ->will($this->returnValueMap([
                ['fullsubtype_example', $stubavailableplugininfo],
                ['fulldeprecatedsubtype_test', $stubdeprecatedplugininfo],
                ['fulldeletedsubtype_demo', $stubdeletedplugininfo],
            ]));
        $pluginmanrc = new \ReflectionClass(\core_plugin_manager::class);
        $pluginmanrc->setStaticPropertyValue('singletoninstance', $stubpluginman);

        $task1 = new \fullsubtype_example\task\adhoc_test(); // Available plugin type.
        $task2 = new \fulldeprecatedsubtype_test\task\adhoc_test(); // Deprecated plugin type.
        $task3 = new \fulldeletedsubtype_demo\task\adhoc_test(); // Deleted plugin type.

        $DB->delete_records('task_adhoc');

        // Task from a non-deprecated plugin type can be queued.
        $this->assertIsInt(manager::queue_adhoc_task($task1));
        $now = time();
        $classname = get_class($task1);
        $taskfromqueue = manager::get_next_adhoc_task($now, true, $classname);
        $this->assertNotNull($taskfromqueue);
        $taskfromqueue->execute();
        manager::adhoc_task_complete($taskfromqueue);

        // Task from a deprecated plugin type cannot be queued.
        $this->assertTrue(\core_plugin_manager::instance()->get_plugin_info('fulldeprecatedsubtype_test')->is_deprecated());
        $this->assertFalse(manager::queue_adhoc_task($task2));
        $classname = get_class($task2);
        $this->assertNull(manager::get_next_adhoc_task($now, true, $classname));

        // Task from a deleted plugin type cannot be queued.
        $this->assertTrue(\core_plugin_manager::instance()->get_plugin_info('fulldeletedsubtype_demo')->is_deleted());
        $this->assertFalse(manager::queue_adhoc_task($task3));
        $classname = get_class($task3);
        $this->assertNull(manager::get_next_adhoc_task($now, true, $classname));
    }

    /**
     * Test verifying \core\task\manager can still return and run adhoc tasks queued prior to plugin deprecation.
     *
     *  This only verifies that existing tasks can be fetched and run via:
     *  - {@see \core\task\manager::get_next_adhoc_task()}
     *
     * @return void
     */
    public function test_run_existing_adhoc_task_deprecated_plugintype(): void {
        $this->resetAfterTest();
        global $DB, $CFG;

        $fakepluginroot = $CFG->libdir . '/tests/fixtures/fakeplugins/fake/fullfeatured';
        require_once($fakepluginroot . '/classes/plugininfo/fullsubtype.php');
        require_once($fakepluginroot . '/fullsubtype/example/classes/task/adhoc_test.php');

        // Inject stub plugininfo instances into a stub plugin manager, then inject that into the static cache via reflection.
        // When the task code calls core_plugin_manager::instance(), it'll get back the stub.
        $stubavailableplugininfo = $this->createStub(\fake_fullfeatured\plugininfo\fullsubtype::class);
        $stubavailableplugininfo->method('is_deprecated')->willReturn(false);
        $stubavailableplugininfo->component = "fullsubtype_example";
        $stubpluginman = $this->createStub(\core_plugin_manager::class);
        $stubpluginman
            ->method('get_plugin_info')
            ->will($this->returnValueMap([
                ['fullsubtype_example', $stubavailableplugininfo],
            ]));
        $pluginmanrc = new \ReflectionClass(\core_plugin_manager::class);
        $pluginmanrc->setStaticPropertyValue('singletoninstance', $stubpluginman);

        $task1 = new \fullsubtype_example\task\adhoc_test(); // An available plugin.

        $DB->delete_records('task_adhoc');

        // Queue the task for the available plugin.
        $this->assertIsInt(manager::queue_adhoc_task($task1));
        $this->assertEquals(1, $DB->count_records('task_adhoc'));

        // Now, deprecate the plugin type by redefining the stubs and reinjecting into the stub plugin manager.
        $stubdeprecatedplugininfo = $this->createStub(\fake_fullfeatured\plugininfo\fullsubtype::class);
        $stubdeprecatedplugininfo->method('is_deprecated')->willReturn(true);
        $stubdeprecatedplugininfo->component = "fullsubtype_example";
        $stubpluginman = $this->createStub(\core_plugin_manager::class);
        $stubpluginman
            ->method('get_plugin_info')
            ->will($this->returnValueMap([
                ['fullsubtype_example', $stubdeprecatedplugininfo],
            ]));
        $pluginmanrc->setStaticPropertyValue('singletoninstance', $stubpluginman);

        // Assert prior-queued tasks can be fetched and run.
        $this->assertTrue(\core_plugin_manager::instance()->get_plugin_info('fullsubtype_example')->is_deprecated());
        $classname = get_class($task1);
        $now = time();
        $taskfromqueue = manager::get_next_adhoc_task($now, true, $classname);
        $this->assertNotNull($taskfromqueue);
        $taskfromqueue->execute();
        manager::adhoc_task_complete($taskfromqueue);
    }
}
