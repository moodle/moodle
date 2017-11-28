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
 * Scheduled and adhoc task management.
 *
 * @package    core
 * @category   task
 * @copyright  2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

define('CORE_TASK_TASKS_FILENAME', 'db/tasks.php');
/**
 * Collection of task related methods.
 *
 * Some locking rules for this class:
 * All changes to scheduled tasks must be protected with both - the global cron lock and the lock
 * for the specific scheduled task (in that order). Locks must be released in the reverse order.
 * @copyright  2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * Given a component name, will load the list of tasks in the db/tasks.php file for that component.
     *
     * @param string $componentname - The name of the component to fetch the tasks for.
     * @return \core\task\scheduled_task[] - List of scheduled tasks for this component.
     */
    public static function load_default_scheduled_tasks_for_component($componentname) {
        $dir = \core_component::get_component_directory($componentname);

        if (!$dir) {
            return array();
        }

        $file = $dir . '/' . CORE_TASK_TASKS_FILENAME;
        if (!file_exists($file)) {
            return array();
        }

        $tasks = null;
        include($file);

        if (!isset($tasks)) {
            return array();
        }

        $scheduledtasks = array();

        foreach ($tasks as $task) {
            $record = (object) $task;
            $scheduledtask = self::scheduled_task_from_record($record);
            // Safety check in case the task in the DB does not match a real class (maybe something was uninstalled).
            if ($scheduledtask) {
                $scheduledtask->set_component($componentname);
                $scheduledtasks[] = $scheduledtask;
            }
        }

        return $scheduledtasks;
    }

    /**
     * Update the database to contain a list of scheduled task for a component.
     * The list of scheduled tasks is taken from @load_scheduled_tasks_for_component.
     * Will throw exceptions for any errors.
     *
     * @param string $componentname - The frankenstyle component name.
     */
    public static function reset_scheduled_tasks_for_component($componentname) {
        global $DB;
        $tasks = self::load_default_scheduled_tasks_for_component($componentname);
        $validtasks = array();

        foreach ($tasks as $taskid => $task) {
            $classname = get_class($task);
            if (strpos($classname, '\\') !== 0) {
                $classname = '\\' . $classname;
            }

            $validtasks[] = $classname;

            if ($currenttask = self::get_scheduled_task($classname)) {
                if ($currenttask->is_customised()) {
                    // If there is an existing task with a custom schedule, do not override it.
                    continue;
                }

                // Update the record from the default task data.
                self::configure_scheduled_task($task);
            } else {
                // Ensure that the first run follows the schedule.
                $task->set_next_run_time($task->get_next_scheduled_time());

                // Insert the new task in the database.
                $record = self::record_from_scheduled_task($task);
                $DB->insert_record('task_scheduled', $record);
            }
        }

        // Delete any task that is not defined in the component any more.
        $sql = "component = :component";
        $params = array('component' => $componentname);
        if (!empty($validtasks)) {
            list($insql, $inparams) = $DB->get_in_or_equal($validtasks, SQL_PARAMS_NAMED, 'param', false);
            $sql .= ' AND classname ' . $insql;
            $params = array_merge($params, $inparams);
        }
        $DB->delete_records_select('task_scheduled', $sql, $params);
    }

    /**
     * Checks if the task with the same classname, component and customdata is already scheduled
     *
     * @param adhoc_task $task
     * @return bool
     */
    protected static function task_is_scheduled($task) {
        global $DB;
        $record = self::record_from_adhoc_task($task);
        $params = [$record->classname, $record->component, $record->customdata];
        $sql = 'classname = ? AND component = ? AND ' .
            $DB->sql_compare_text('customdata', \core_text::strlen($record->customdata) + 1) . ' = ?';

        if ($record->userid) {
            $params[] = $record->userid;
            $sql .= " AND userid = ? ";
        }
        return $DB->record_exists_select('task_adhoc', $sql, $params);
    }

    /**
     * Queue an adhoc task to run in the background.
     *
     * @param \core\task\adhoc_task $task - The new adhoc task information to store.
     * @param bool $checkforexisting - If set to true and the task with the same classname, component and customdata
     *     is already scheduled then it will not schedule a new task. Can be used only for ASAP tasks.
     * @return boolean - True if the config was saved.
     */
    public static function queue_adhoc_task(adhoc_task $task, $checkforexisting = false) {
        global $DB;

        if ($userid = $task->get_userid()) {
            // User found. Check that they are suitable.
            \core_user::require_active_user(\core_user::get_user($userid, '*', MUST_EXIST), true, true);
        }

        $record = self::record_from_adhoc_task($task);
        // Schedule it immediately if nextruntime not explicitly set.
        if (!$task->get_next_run_time()) {
            $record->nextruntime = time() - 1;
        }

        // Check if the same task is already scheduled.
        if ($checkforexisting && self::task_is_scheduled($task)) {
            return false;
        }

        // Queue the task.
        $result = $DB->insert_record('task_adhoc', $record);

        return $result;
    }

    /**
     * Change the default configuration for a scheduled task.
     * The list of scheduled tasks is taken from {@link load_scheduled_tasks_for_component}.
     *
     * @param \core\task\scheduled_task $task - The new scheduled task information to store.
     * @return boolean - True if the config was saved.
     */
    public static function configure_scheduled_task(scheduled_task $task) {
        global $DB;

        $classname = get_class($task);
        if (strpos($classname, '\\') !== 0) {
            $classname = '\\' . $classname;
        }

        $original = $DB->get_record('task_scheduled', array('classname'=>$classname), 'id', MUST_EXIST);

        $record = self::record_from_scheduled_task($task);
        $record->id = $original->id;
        $record->nextruntime = $task->get_next_scheduled_time();
        $result = $DB->update_record('task_scheduled', $record);

        return $result;
    }

    /**
     * Utility method to create a DB record from a scheduled task.
     *
     * @param \core\task\scheduled_task $task
     * @return \stdClass
     */
    public static function record_from_scheduled_task($task) {
        $record = new \stdClass();
        $record->classname = get_class($task);
        if (strpos($record->classname, '\\') !== 0) {
            $record->classname = '\\' . $record->classname;
        }
        $record->component = $task->get_component();
        $record->blocking = $task->is_blocking();
        $record->customised = $task->is_customised();
        $record->lastruntime = $task->get_last_run_time();
        $record->nextruntime = $task->get_next_run_time();
        $record->faildelay = $task->get_fail_delay();
        $record->hour = $task->get_hour();
        $record->minute = $task->get_minute();
        $record->day = $task->get_day();
        $record->dayofweek = $task->get_day_of_week();
        $record->month = $task->get_month();
        $record->disabled = $task->get_disabled();

        return $record;
    }

    /**
     * Utility method to create a DB record from an adhoc task.
     *
     * @param \core\task\adhoc_task $task
     * @return \stdClass
     */
    public static function record_from_adhoc_task($task) {
        $record = new \stdClass();
        $record->classname = get_class($task);
        if (strpos($record->classname, '\\') !== 0) {
            $record->classname = '\\' . $record->classname;
        }
        $record->id = $task->get_id();
        $record->component = $task->get_component();
        $record->blocking = $task->is_blocking();
        $record->nextruntime = $task->get_next_run_time();
        $record->faildelay = $task->get_fail_delay();
        $record->customdata = $task->get_custom_data_as_string();
        $record->userid = $task->get_userid();

        return $record;
    }

    /**
     * Utility method to create an adhoc task from a DB record.
     *
     * @param \stdClass $record
     * @return \core\task\adhoc_task
     */
    public static function adhoc_task_from_record($record) {
        $classname = $record->classname;
        if (strpos($classname, '\\') !== 0) {
            $classname = '\\' . $classname;
        }
        if (!class_exists($classname)) {
            debugging("Failed to load task: " . $classname, DEBUG_DEVELOPER);
            return false;
        }
        $task = new $classname;
        if (isset($record->nextruntime)) {
            $task->set_next_run_time($record->nextruntime);
        }
        if (isset($record->id)) {
            $task->set_id($record->id);
        }
        if (isset($record->component)) {
            $task->set_component($record->component);
        }
        $task->set_blocking(!empty($record->blocking));
        if (isset($record->faildelay)) {
            $task->set_fail_delay($record->faildelay);
        }
        if (isset($record->customdata)) {
            $task->set_custom_data_as_string($record->customdata);
        }

        if (isset($record->userid)) {
            $task->set_userid($record->userid);
        }

        return $task;
    }

    /**
     * Utility method to create a task from a DB record.
     *
     * @param \stdClass $record
     * @return \core\task\scheduled_task
     */
    public static function scheduled_task_from_record($record) {
        $classname = $record->classname;
        if (strpos($classname, '\\') !== 0) {
            $classname = '\\' . $classname;
        }
        if (!class_exists($classname)) {
            debugging("Failed to load task: " . $classname, DEBUG_DEVELOPER);
            return false;
        }
        /** @var \core\task\scheduled_task $task */
        $task = new $classname;
        if (isset($record->lastruntime)) {
            $task->set_last_run_time($record->lastruntime);
        }
        if (isset($record->nextruntime)) {
            $task->set_next_run_time($record->nextruntime);
        }
        if (isset($record->customised)) {
            $task->set_customised($record->customised);
        }
        if (isset($record->component)) {
            $task->set_component($record->component);
        }
        $task->set_blocking(!empty($record->blocking));
        if (isset($record->minute)) {
            $task->set_minute($record->minute);
        }
        if (isset($record->hour)) {
            $task->set_hour($record->hour);
        }
        if (isset($record->day)) {
            $task->set_day($record->day);
        }
        if (isset($record->month)) {
            $task->set_month($record->month);
        }
        if (isset($record->dayofweek)) {
            $task->set_day_of_week($record->dayofweek);
        }
        if (isset($record->faildelay)) {
            $task->set_fail_delay($record->faildelay);
        }
        if (isset($record->disabled)) {
            $task->set_disabled($record->disabled);
        }

        return $task;
    }

    /**
     * Given a component name, will load the list of tasks from the scheduled_tasks table for that component.
     * Do not execute tasks loaded from this function - they have not been locked.
     * @param string $componentname - The name of the component to load the tasks for.
     * @return \core\task\scheduled_task[]
     */
    public static function load_scheduled_tasks_for_component($componentname) {
        global $DB;

        $tasks = array();
        // We are just reading - so no locks required.
        $records = $DB->get_records('task_scheduled', array('component' => $componentname), 'classname', '*', IGNORE_MISSING);
        foreach ($records as $record) {
            $task = self::scheduled_task_from_record($record);
            // Safety check in case the task in the DB does not match a real class (maybe something was uninstalled).
            if ($task) {
                $tasks[] = $task;
            }
        }

        return $tasks;
    }

    /**
     * This function load the scheduled task details for a given classname.
     *
     * @param string $classname
     * @return \core\task\scheduled_task or false
     */
    public static function get_scheduled_task($classname) {
        global $DB;

        if (strpos($classname, '\\') !== 0) {
            $classname = '\\' . $classname;
        }
        // We are just reading - so no locks required.
        $record = $DB->get_record('task_scheduled', array('classname'=>$classname), '*', IGNORE_MISSING);
        if (!$record) {
            return false;
        }
        return self::scheduled_task_from_record($record);
    }

    /**
     * This function load the adhoc tasks for a given classname.
     *
     * @param string $classname
     * @return \core\task\adhoc_task[]
     */
    public static function get_adhoc_tasks($classname) {
        global $DB;

        if (strpos($classname, '\\') !== 0) {
            $classname = '\\' . $classname;
        }
        // We are just reading - so no locks required.
        $records = $DB->get_records('task_adhoc', array('classname' => $classname));

        return array_map(function($record) {
            return self::adhoc_task_from_record($record);
        }, $records);
    }

    /**
     * This function load the default scheduled task details for a given classname.
     *
     * @param string $classname
     * @return \core\task\scheduled_task or false
     */
    public static function get_default_scheduled_task($classname) {
        $task = self::get_scheduled_task($classname);
        $componenttasks = array();

        // Safety check in case no task was found for the given classname.
        if ($task) {
            $componenttasks = self::load_default_scheduled_tasks_for_component($task->get_component());
        }

        foreach ($componenttasks as $componenttask) {
            if (get_class($componenttask) == get_class($task)) {
                return $componenttask;
            }
        }

        return false;
    }

    /**
     * This function will return a list of all the scheduled tasks that exist in the database.
     *
     * @return \core\task\scheduled_task[]
     */
    public static function get_all_scheduled_tasks() {
        global $DB;

        $records = $DB->get_records('task_scheduled', null, 'component, classname', '*', IGNORE_MISSING);
        $tasks = array();

        foreach ($records as $record) {
            $task = self::scheduled_task_from_record($record);
            // Safety check in case the task in the DB does not match a real class (maybe something was uninstalled).
            if ($task) {
                $tasks[] = $task;
            }
        }

        return $tasks;
    }

    /**
     * This function will dispatch the next adhoc task in the queue. The task will be handed out
     * with an open lock - possibly on the entire cron process. Make sure you call either
     * {@link adhoc_task_failed} or {@link adhoc_task_complete} to release the lock and reschedule the task.
     *
     * @param int $timestart
     * @return \core\task\adhoc_task or null if not found
     */
    public static function get_next_adhoc_task($timestart) {
        global $DB;
        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');

        if (!$cronlock = $cronlockfactory->get_lock('core_cron', 10)) {
            throw new \moodle_exception('locktimeout');
        }

        $where = '(nextruntime IS NULL OR nextruntime < :timestart1)';
        $params = array('timestart1' => $timestart);
        $records = $DB->get_records_select('task_adhoc', $where, $params);

        foreach ($records as $record) {

            if ($lock = $cronlockfactory->get_lock('adhoc_' . $record->id, 0)) {
                $classname = '\\' . $record->classname;

                // Safety check, see if the task has been already processed by another cron run.
                $record = $DB->get_record('task_adhoc', array('id' => $record->id));
                if (!$record) {
                    $lock->release();
                    continue;
                }

                $task = self::adhoc_task_from_record($record);
                // Safety check in case the task in the DB does not match a real class (maybe something was uninstalled).
                if (!$task) {
                    $lock->release();
                    continue;
                }

                $task->set_lock($lock);
                if (!$task->is_blocking()) {
                    $cronlock->release();
                } else {
                    $task->set_cron_lock($cronlock);
                }
                return $task;
            }
        }

        // No tasks.
        $cronlock->release();
        return null;
    }

    /**
     * This function will dispatch the next scheduled task in the queue. The task will be handed out
     * with an open lock - possibly on the entire cron process. Make sure you call either
     * {@link scheduled_task_failed} or {@link scheduled_task_complete} to release the lock and reschedule the task.
     *
     * @param int $timestart - The start of the cron process - do not repeat any tasks that have been run more recently than this.
     * @return \core\task\scheduled_task or null
     */
    public static function get_next_scheduled_task($timestart) {
        global $DB;
        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');

        if (!$cronlock = $cronlockfactory->get_lock('core_cron', 10)) {
            throw new \moodle_exception('locktimeout');
        }

        $where = "(lastruntime IS NULL OR lastruntime < :timestart1)
                  AND (nextruntime IS NULL OR nextruntime < :timestart2)
                  AND disabled = 0
                  ORDER BY lastruntime, id ASC";
        $params = array('timestart1' => $timestart, 'timestart2' => $timestart);
        $records = $DB->get_records_select('task_scheduled', $where, $params);

        $pluginmanager = \core_plugin_manager::instance();

        foreach ($records as $record) {

            if ($lock = $cronlockfactory->get_lock(($record->classname), 0)) {
                $classname = '\\' . $record->classname;
                $task = self::scheduled_task_from_record($record);
                // Safety check in case the task in the DB does not match a real class (maybe something was uninstalled).
                if (!$task) {
                    $lock->release();
                    continue;
                }

                $task->set_lock($lock);

                // See if the component is disabled.
                $plugininfo = $pluginmanager->get_plugin_info($task->get_component());

                if ($plugininfo) {
                    if (($plugininfo->is_enabled() === false) && !$task->get_run_if_component_disabled()) {
                        $lock->release();
                        continue;
                    }
                }

                // Make sure the task data is unchanged.
                if (!$DB->record_exists('task_scheduled', (array) $record)) {
                    $lock->release();
                    continue;
                }

                if (!$task->is_blocking()) {
                    $cronlock->release();
                } else {
                    $task->set_cron_lock($cronlock);
                }
                return $task;
            }
        }

        // No tasks.
        $cronlock->release();
        return null;
    }

    /**
     * This function indicates that an adhoc task was not completed successfully and should be retried.
     *
     * @param \core\task\adhoc_task $task
     */
    public static function adhoc_task_failed(adhoc_task $task) {
        global $DB;
        $delay = $task->get_fail_delay();

        // Reschedule task with exponential fall off for failing tasks.
        if (empty($delay)) {
            $delay = 60;
        } else {
            $delay *= 2;
        }

        // Max of 24 hour delay.
        if ($delay > 86400) {
            $delay = 86400;
        }

        $classname = get_class($task);
        if (strpos($classname, '\\') !== 0) {
            $classname = '\\' . $classname;
        }

        $task->set_next_run_time(time() + $delay);
        $task->set_fail_delay($delay);
        $record = self::record_from_adhoc_task($task);
        $DB->update_record('task_adhoc', $record);

        if ($task->is_blocking()) {
            $task->get_cron_lock()->release();
        }
        $task->get_lock()->release();
    }

    /**
     * This function indicates that an adhoc task was completed successfully.
     *
     * @param \core\task\adhoc_task $task
     */
    public static function adhoc_task_complete(adhoc_task $task) {
        global $DB;

        // Delete the adhoc task record - it is finished.
        $DB->delete_records('task_adhoc', array('id' => $task->get_id()));

        // Reschedule and then release the locks.
        if ($task->is_blocking()) {
            $task->get_cron_lock()->release();
        }
        $task->get_lock()->release();
    }

    /**
     * This function indicates that a scheduled task was not completed successfully and should be retried.
     *
     * @param \core\task\scheduled_task $task
     */
    public static function scheduled_task_failed(scheduled_task $task) {
        global $DB;

        $delay = $task->get_fail_delay();

        // Reschedule task with exponential fall off for failing tasks.
        if (empty($delay)) {
            $delay = 60;
        } else {
            $delay *= 2;
        }

        // Max of 24 hour delay.
        if ($delay > 86400) {
            $delay = 86400;
        }

        $classname = get_class($task);
        if (strpos($classname, '\\') !== 0) {
            $classname = '\\' . $classname;
        }

        $record = $DB->get_record('task_scheduled', array('classname' => $classname));
        $record->nextruntime = time() + $delay;
        $record->faildelay = $delay;
        $DB->update_record('task_scheduled', $record);

        if ($task->is_blocking()) {
            $task->get_cron_lock()->release();
        }
        $task->get_lock()->release();
    }

    /**
     * This function indicates that a scheduled task was completed successfully and should be rescheduled.
     *
     * @param \core\task\scheduled_task $task
     */
    public static function scheduled_task_complete(scheduled_task $task) {
        global $DB;

        $classname = get_class($task);
        if (strpos($classname, '\\') !== 0) {
            $classname = '\\' . $classname;
        }
        $record = $DB->get_record('task_scheduled', array('classname' => $classname));
        if ($record) {
            $record->lastruntime = time();
            $record->faildelay = 0;
            $record->nextruntime = $task->get_next_scheduled_time();

            $DB->update_record('task_scheduled', $record);
        }

        // Reschedule and then release the locks.
        if ($task->is_blocking()) {
            $task->get_cron_lock()->release();
        }
        $task->get_lock()->release();
    }

    /**
     * This function is used to indicate that any long running cron processes should exit at the
     * next opportunity and restart. This is because something (e.g. DB changes) has changed and
     * the static caches may be stale.
     */
    public static function clear_static_caches() {
        global $DB;
        // Do not use get/set config here because the caches cannot be relied on.
        $record = $DB->get_record('config', array('name'=>'scheduledtaskreset'));
        if ($record) {
            $record->value = time();
            $DB->update_record('config', $record);
        } else {
            $record = new \stdClass();
            $record->name = 'scheduledtaskreset';
            $record->value = time();
            $DB->insert_record('config', $record);
        }
    }

    /**
     * Return true if the static caches have been cleared since $starttime.
     * @param int $starttime The time this process started.
     * @return boolean True if static caches need resetting.
     */
    public static function static_caches_cleared_since($starttime) {
        global $DB;
        $record = $DB->get_record('config', array('name'=>'scheduledtaskreset'));
        return $record && (intval($record->value) > $starttime);
    }
}
