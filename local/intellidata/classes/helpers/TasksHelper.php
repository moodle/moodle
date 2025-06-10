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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\helpers;

use core\task\manager;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class TasksHelper {

    /** Tasks list table */
    const TASKS_TABLE = 'task_scheduled';

    /** Tasks logs table */
    const LOG_TABLE = 'task_log';

    /**
     * Validate adhoc tasks.
     *
     * @param $table
     * @return bool
     * @throws \dml_exception
     */
    public static function validate_adhoc_tasks($table) {
        global $DB;

        // Ignore validation for Moodle 3.3.
        if (!method_exists('\\core\\task\\manager', 'get_running_tasks')) {
            return true;
        }

        $runningtasks = manager::get_running_tasks();
        $adhoctasks = $DB->get_records('task_adhoc', ['classname' => '\local_intellidata\task\export_adhoc_task']);

        if (count($runningtasks)) {
            foreach ($runningtasks as $task) {

                if ($task->classname != '\local_intellidata\task\export_adhoc_task') {
                    continue;
                }

                if (!empty($adhoctasks[$task->id]->customdata)) {
                    $customdata = json_decode($adhoctasks[$task->id]->customdata);

                    // Validate datatype.
                    if (!empty($customdata->datatypes) && in_array($table, $customdata->datatypes)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Get IntelliData tasks logs.
     *
     * @param array $params
     * @return array
     * @throws \dml_exception
     */
    public static function get_logs($params = []) {
        global $DB;

        // Check if DB table exists.
        $dbman = $DB->get_manager();
        if (!$dbman->table_exists(self::LOG_TABLE)) {
            return [];
        }

        $where = ['component = :component'];
        $sqlparams = [
            'component' => ParamsHelper::PLUGIN,
        ];

        if (!empty($params['timestart'])) {
            $where[] = 'timestart >= :timestart';
            $sqlparams['timestart'] = $params['timestart'];
        }

        if (!empty($params['timeend'])) {
            $where[] = 'timeend <= :timeend';
            $sqlparams['timeend'] = $params['timeend'];
        }

        if (!empty($params['taskname'])) {
            $where[] = 'classname = :classname';
            $sqlparams['classname'] = 'local_intellidata\\task\\' . $params['taskname'];
        }

        $where = implode(' AND ', $where);

        return $DB->get_records_sql("
            SELECT *
              FROM {" . self::LOG_TABLE . "}
             WHERE $where", $sqlparams);
    }

    /**
     * Get plugin cron jobs config.
     *
     * @return array
     * @throws \dml_exception
     */
    public static function get_tasks_config() {
        global $DB;

        // Check if DB table exists.
        $dbman = $DB->get_manager();
        if (!$dbman->table_exists(self::TASKS_TABLE)) {
            return [];
        }

        return $DB->get_records(
            self::TASKS_TABLE,
            ['component' => ParamsHelper::PLUGIN]
        );
    }

    /**
     * Create adhoc task.
     *
     * @return array
     * @throws \dml_exception
     */
    public static function create_adhoc_task($taskname, $params = null, $nextruntime = 0) {

        $taskname = 'local_intellidata\\task\\' . $taskname;

        // Create next adhoc task.
        $task = new $taskname;

        if ($params) {
            $task->set_custom_data($params);
        }

        if ($nextruntime) {
            $task->set_next_run_time($nextruntime);
        }

        manager::queue_adhoc_task($task);
    }

    /**
     * Delete adhoc task.
     *
     * @param int $taskid
     * @return bool
     * @throws \dml_exception
     */
    public static function delete_adhoc_task(int $taskid) {
        global $DB;

        $record = $DB->get_record('task_adhoc', ['id' => $taskid]);

        if ($record &&
            ($record->component == ParamsHelper::PLUGIN || stripos($record->classname, ParamsHelper::PLUGIN) !== false)
        ) {

            $task = manager::adhoc_task_from_record($record);

            // Complete task if it is running.
            if (self::is_task_running($task)) {
                manager::adhoc_task_complete($task);
            } else {
                // Delete task if it is not running.
                $DB->delete_records('task_adhoc', ['id' => $task->get_id()]);
            }

            return true;
        }

        return false;
    }

    /**
     * Validate if task is currently running.
     *
     * @param $task
     * @return bool
     */
    public static function is_task_running($task) {
        return method_exists($task, 'get_timestarted') && $task->get_timestarted();
    }

    /**
     * Create adhoc task to refresh migration progress.
     *
     * @return array
     * @throws \dml_exception
     */
    public static function init_refresh_export_progress_adhoc_task() {
        self::create_adhoc_task('refresh_export_progress_adhoc_task');
    }


    /**
     * Get IntelliData adhoc tasks scheduled.
     *
     * @param array $params
     * @return array
     * @throws \dml_exception
     */
    public static function get_adhoc_tasks() {
        global $DB;

        $whereclasslike = $DB->sql_like(
            'classname', ':classname', false, false, false
        );

        return $DB->get_records_select(
            "task_adhoc",
            '(component = :component OR ' . $whereclasslike . ')',
            [
                'component' => ParamsHelper::PLUGIN,
                'classname' => '%' . ParamsHelper::PLUGIN . '%',
            ],
            'id'
        );
    }

    /**
     * Save scheduled task.
     *
     * @param string $classname
     * @param array $data
     * @return bool
     */
    public static function save_scheduled_task(string $classname, array $data) {

        $task = manager::get_scheduled_task($classname);

        if ($task && stripos($classname, ParamsHelper::PLUGIN) !== false) {

            $task->set_minute($data['minute']);
            $task->set_hour($data['hour']);
            $task->set_month($data['month']);
            $task->set_day_of_week($data['dayofweek']);
            $task->set_day($data['day']);
            $task->set_disabled($data['disabled']);
            $task->set_customised(true);

            manager::configure_scheduled_task($task);
            return true;
        }

        return false;
    }
}
