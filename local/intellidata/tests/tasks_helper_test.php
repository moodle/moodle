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
 * Tasks helper test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');

use local_intellidata\helpers\TasksHelper;
use local_intellidata\repositories\required_tables_repository;
use local_intellidata\task\export_adhoc_task;

/**
 * Tasks helper test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class tasks_helper_test extends \advanced_testcase {

    /**
     * Set up the test.
     *
     * @return void
     */
    public function setUp(): void {
        self::setAdminUser();

        setup_helper::setup_tests_config();
    }

    /**
     * Test create adhoc task.
     *
     * @return void
     * @throws \dml_exception
     * @covers \local_intellidata\helpers\TasksHelper::create_adhoc_task
     */
    public function test_create_adhoc_task() {
        global $DB, $USER;

        $this->resetAfterTest(true);

        TasksHelper::create_adhoc_task('export_adhoc_task', ['datatypes' => ['users'], 'start' => 100]);

        // Validate that the task is created.
        $taskrecord = $DB->get_record('task_adhoc', ['classname' => '\local_intellidata\task\export_adhoc_task']);
        $this->assertTrue($taskrecord->id > 0);

        $task = \core\task\manager::adhoc_task_from_record($taskrecord);
        $taskdata = $task->get_custom_data();

        $this->assertEquals(['users'], $taskdata->datatypes);
        $this->assertEquals(100, $taskdata->start);
    }

    /**
     * Test delete adhoc task.
     *
     * @return void
     * @throws \dml_exception
     * @covers \local_intellidata\helpers\TasksHelper::delete_adhoc_task
     */
    public function test_delete_adhoc_task() {
        global $DB;

        $this->resetAfterTest(true);

        // Create two tasks.
        TasksHelper::create_adhoc_task('export_adhoc_task', ['datatypes' => ['users'], 'start' => 100]);
        TasksHelper::create_adhoc_task('export_adhoc_task', ['datatypes' => ['courses']]);

        $tasksrecords = $DB->get_records('task_adhoc', ['classname' => '\local_intellidata\task\export_adhoc_task']);
        $this->assertEquals(2, count($tasksrecords));

        // Validate that the first task is deleted.
        $firsttask = reset($tasksrecords);
        TasksHelper::delete_adhoc_task($firsttask->id);
        $this->assertFalse(
            $DB->record_exists('task_adhoc', ['id' => $firsttask->id])
        );

        // Validate that the second task exists.
        $secondtask = end($tasksrecords);
        $this->assertTrue(
            $DB->record_exists('task_adhoc', ['id' => $secondtask->id])
        );
    }

    /**
     * Test init refresh export progress adhoc task.
     *
     * @return void
     * @throws \dml_exception
     * @covers \local_intellidata\helpers\TasksHelper::init_refresh_export_progress_adhoc_task
     */
    public function test_init_refresh_export_progress_adhoc_task() {
        global $DB;

        $this->resetAfterTest(true);

        // Create two tasks.
        TasksHelper::init_refresh_export_progress_adhoc_task();

        $this->assertTrue(
            $DB->record_exists('task_adhoc', [
                'classname' => '\local_intellidata\task\refresh_export_progress_adhoc_task',
            ])
        );
    }

    /**
     * Test get tasks config.
     *
     * @return void
     * @throws \dml_exception
     * @covers \local_intellidata\helpers\TasksHelper::get_tasks_config
     */
    public function test_get_tasks_config() {

        $this->resetAfterTest(true);

        // Create two tasks.
        $configs = TasksHelper::get_tasks_config();

        if (count($configs)) {
            $tasks = $this->get_tasks_list();

            $this->assertEquals(count($configs), count($tasks));

            foreach ($configs as $config) {
                $this->assertContains($config->classname, $tasks);
            }

        } else {
            $this->assertEquals(count($configs), 0);
        }
    }

    /**
     * Get tasks list.
     *
     * @return array
     */
    private function get_tasks_list() {
        global $CFG;

        $taskclasses = $tasks = [];
        require_once($CFG->dirroot . '/local/intellidata/db/tasks.php');

        foreach ($tasks as $task) {
            $taskclasses[] = '\\' . $task['classname'];
        }

        return $taskclasses;
    }
}
