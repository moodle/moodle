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
     * @param bool $expandr - if true (default) an 'R' value in a time is expanded to an appropriate int.
     *      If false, they are left as 'R'
     * @return \core\task\scheduled_task[] - List of scheduled tasks for this component.
     */
    public static function load_default_scheduled_tasks_for_component($componentname, $expandr = true) {
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
            $scheduledtask = self::scheduled_task_from_record($record, $expandr);
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
            $classname = self::get_canonical_class_name($task);

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
        return false !== self::get_queued_adhoc_task_record($task);
    }

    /**
     * Checks if the task with the same classname, component and customdata is already scheduled
     *
     * @param adhoc_task $task
     * @return bool
     */
    protected static function get_queued_adhoc_task_record($task) {
        global $DB;

        $record = self::record_from_adhoc_task($task);
        $params = [$record->classname, $record->component, $record->customdata];
        $sql = 'classname = ? AND component = ? AND ' .
            $DB->sql_compare_text('customdata', \core_text::strlen($record->customdata) + 1) . ' = ?';

        if ($record->userid) {
            $params[] = $record->userid;
            $sql .= " AND userid = ? ";
        }
        return $DB->get_record_select('task_adhoc', $sql, $params);
    }

    /**
     * Schedule a new task, or reschedule an existing adhoc task which has matching data.
     *
     * Only a task matching the same user, classname, component, and customdata will be rescheduled.
     * If these values do not match exactly then a new task is scheduled.
     *
     * @param \core\task\adhoc_task $task - The new adhoc task information to store.
     * @since Moodle 3.7
     */
    public static function reschedule_or_queue_adhoc_task(adhoc_task $task) : void {
        global $DB;

        if ($existingrecord = self::get_queued_adhoc_task_record($task)) {
            // Only update the next run time if it is explicitly set on the task.
            $nextruntime = $task->get_next_run_time();
            if ($nextruntime && ($existingrecord->nextruntime != $nextruntime)) {
                $DB->set_field('task_adhoc', 'nextruntime', $nextruntime, ['id' => $existingrecord->id]);
            }
        } else {
            // There is nothing queued yet. Just queue as normal.
            self::queue_adhoc_task($task);
        }
    }

    /**
     * Queue an adhoc task to run in the background.
     *
     * @param \core\task\adhoc_task $task - The new adhoc task information to store.
     * @param bool $checkforexisting - If set to true and the task with the same user, classname, component and customdata
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

        $classname = self::get_canonical_class_name($task);

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
        $record->classname = self::get_canonical_class_name($task);
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
        $record->timestarted = $task->get_timestarted();
        $record->hostname = $task->get_hostname();
        $record->pid = $task->get_pid();

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
        $record->classname = self::get_canonical_class_name($task);
        $record->id = $task->get_id();
        $record->component = $task->get_component();
        $record->blocking = $task->is_blocking();
        $record->nextruntime = $task->get_next_run_time();
        $record->faildelay = $task->get_fail_delay();
        $record->customdata = $task->get_custom_data_as_string();
        $record->userid = $task->get_userid();
        $record->timecreated = time();
        $record->timestarted = $task->get_timestarted();
        $record->hostname = $task->get_hostname();
        $record->pid = $task->get_pid();

        return $record;
    }

    /**
     * Utility method to create an adhoc task from a DB record.
     *
     * @param \stdClass $record
     * @return \core\task\adhoc_task
     */
    public static function adhoc_task_from_record($record) {
        $classname = self::get_canonical_class_name($record->classname);
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
        if (isset($record->timestarted)) {
            $task->set_timestarted($record->timestarted);
        }
        if (isset($record->hostname)) {
            $task->set_hostname($record->hostname);
        }
        if (isset($record->pid)) {
            $task->set_pid($record->pid);
        }

        return $task;
    }

    /**
     * Utility method to create a task from a DB record.
     *
     * @param \stdClass $record
     * @param bool $expandr - if true (default) an 'R' value in a time is expanded to an appropriate int.
     *      If false, they are left as 'R'
     * @return \core\task\scheduled_task|false
     */
    public static function scheduled_task_from_record($record, $expandr = true) {
        $classname = self::get_canonical_class_name($record->classname);
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
            $task->set_minute($record->minute, $expandr);
        }
        if (isset($record->hour)) {
            $task->set_hour($record->hour, $expandr);
        }
        if (isset($record->day)) {
            $task->set_day($record->day);
        }
        if (isset($record->month)) {
            $task->set_month($record->month);
        }
        if (isset($record->dayofweek)) {
            $task->set_day_of_week($record->dayofweek, $expandr);
        }
        if (isset($record->faildelay)) {
            $task->set_fail_delay($record->faildelay);
        }
        if (isset($record->disabled)) {
            $task->set_disabled($record->disabled);
        }
        if (isset($record->timestarted)) {
            $task->set_timestarted($record->timestarted);
        }
        if (isset($record->hostname)) {
            $task->set_hostname($record->hostname);
        }
        if (isset($record->pid)) {
            $task->set_pid($record->pid);
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

        $classname = self::get_canonical_class_name($classname);
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

        $classname = self::get_canonical_class_name($classname);
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
     * @param bool $expandr - if true (default) an 'R' value in a time is expanded to an appropriate int.
     *      If false, they are left as 'R'
     * @return \core\task\scheduled_task|false
     */
    public static function get_default_scheduled_task($classname, $expandr = true) {
        $task = self::get_scheduled_task($classname);
        $componenttasks = array();

        // Safety check in case no task was found for the given classname.
        if ($task) {
            $componenttasks = self::load_default_scheduled_tasks_for_component(
                    $task->get_component(), $expandr);
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
     * Ensure quality of service for the ad hoc task queue.
     *
     * This reshuffles the adhoc tasks queue to balance by type to ensure a
     * level of quality of service per type, while still maintaining the
     * relative order of tasks queued by timestamp.
     *
     * @param array $records array of task records
     * @param array $records array of same task records shuffled
     */
    public static function ensure_adhoc_task_qos(array $records): array {

        $count = count($records);
        if ($count == 0) {
            return $records;
        }

        $queues = []; // This holds a queue for each type of adhoc task.
        $limits = []; // The relative limits of each type of task.
        $limittotal = 0;

        // Split the single queue up into queues per type.
        foreach ($records as $record) {
            $type = $record->classname;
            if (!array_key_exists($type, $queues)) {
                $queues[$type] = [];
            }
            if (!array_key_exists($type, $limits)) {
                $limits[$type] = 1;
                $limittotal += 1;
            }
            $queues[$type][] = $record;
        }

        $qos = []; // Our new queue with ensured quality of service.
        $seed = $count % $limittotal; // Which task queue to shuffle from first?

        $move = 1; // How many tasks to shuffle at a time.
        do {
            $shuffled = 0;

            // Now cycle through task type queues and interleaving the tasks
            // back into a single queue.
            foreach ($limits as $type => $limit) {

                // Just interleaving the queue is not enough, because after
                // any task is processed the whole queue is rebuilt again. So
                // we need to deterministically start on different types of
                // tasks so that *on average* we rotate through each type of task.
                //
                // We achieve this by using a $seed to start moving tasks off a
                // different queue each time. The seed is based on the task count
                // modulo the number of types of tasks on the queue. As we count
                // down this naturally cycles through each type of record.
                if ($seed < 1) {
                    $shuffled = 1;
                    $seed += 1;
                    continue;
                }
                $tasks = array_splice($queues[$type], 0, $move);
                $qos = array_merge($qos, $tasks);

                // Stop if we didn't move any tasks onto the main queue.
                $shuffled += count($tasks);
            }
            // Generally the only tasks that matter are those that are near the start so
            // after we have shuffled the first few 1 by 1, start shuffling larger groups.
            if (count($qos) >= (4 * count($limits))) {
                $move *= 2;
            }
        } while ($shuffled > 0);

        return $qos;
    }

    /**
     * This function will dispatch the next adhoc task in the queue. The task will be handed out
     * with an open lock - possibly on the entire cron process. Make sure you call either
     * {@link adhoc_task_failed} or {@link adhoc_task_complete} to release the lock and reschedule the task.
     *
     * @param int $timestart
     * @param bool $checklimits Should we check limits?
     * @return \core\task\adhoc_task or null if not found
     * @throws \moodle_exception
     */
    public static function get_next_adhoc_task($timestart, $checklimits = true) {
        global $DB;

        $where = '(nextruntime IS NULL OR nextruntime < :timestart1)';
        $params = array('timestart1' => $timestart);
        $records = $DB->get_records_select('task_adhoc', $where, $params, 'nextruntime ASC, id ASC', '*', 0, 2000);
        $records = self::ensure_adhoc_task_qos($records);

        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');

        $skipclasses = array();

        foreach ($records as $record) {

            if (in_array($record->classname, $skipclasses)) {
                // Skip the task if it can't be started due to per-task concurrency limit.
                continue;
            }

            if ($lock = $cronlockfactory->get_lock('adhoc_' . $record->id, 0)) {

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

                $tasklimit = $task->get_concurrency_limit();
                if ($checklimits && $tasklimit > 0) {
                    if ($concurrencylock = self::get_concurrent_task_lock($task)) {
                        $task->set_concurrency_lock($concurrencylock);
                    } else {
                        // Unable to obtain a concurrency lock.
                        mtrace("Skipping $record->classname adhoc task class as the per-task limit of $tasklimit is reached.");
                        $skipclasses[] = $record->classname;
                        $lock->release();
                        continue;
                    }
                }

                // The global cron lock is under the most contention so request it
                // as late as possible and release it as soon as possible.
                if (!$cronlock = $cronlockfactory->get_lock('core_cron', 10)) {
                    $lock->release();
                    throw new \moodle_exception('locktimeout');
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

        return null;
    }

    /**
     * This function will dispatch the next scheduled task in the queue. The task will be handed out
     * with an open lock - possibly on the entire cron process. Make sure you call either
     * {@link scheduled_task_failed} or {@link scheduled_task_complete} to release the lock and reschedule the task.
     *
     * @param int $timestart - The start of the cron process - do not repeat any tasks that have been run more recently than this.
     * @return \core\task\scheduled_task or null
     * @throws \moodle_exception
     */
    public static function get_next_scheduled_task($timestart) {
        global $DB;
        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');

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

                // The global cron lock is under the most contention so request it
                // as late as possible and release it as soon as possible.
                if (!$cronlock = $cronlockfactory->get_lock('core_cron', 10)) {
                    $lock->release();
                    throw new \moodle_exception('locktimeout');
                }

                if (!$task->is_blocking()) {
                    $cronlock->release();
                } else {
                    $task->set_cron_lock($cronlock);
                }
                return $task;
            }
        }

        return null;
    }

    /**
     * This function indicates that an adhoc task was not completed successfully and should be retried.
     *
     * @param \core\task\adhoc_task $task
     */
    public static function adhoc_task_failed(adhoc_task $task) {
        global $DB;
        // Finalise the log output.
        logmanager::finalise_log(true);

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

        // Reschedule and then release the locks.
        $task->set_timestarted();
        $task->set_hostname();
        $task->set_pid();
        $task->set_next_run_time(time() + $delay);
        $task->set_fail_delay($delay);
        $record = self::record_from_adhoc_task($task);
        $DB->update_record('task_adhoc', $record);

        $task->release_concurrency_lock();
        if ($task->is_blocking()) {
            $task->get_cron_lock()->release();
        }
        $task->get_lock()->release();
    }

    /**
     * Records that a adhoc task is starting to run.
     *
     * @param adhoc_task $task Task that is starting
     * @param int $time Start time (leave blank for now)
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public static function adhoc_task_starting(adhoc_task $task, int $time = 0) {
        global $DB;
        $pid = (int)getmypid();
        $hostname = (string)gethostname();

        if (empty($time)) {
            $time = time();
        }

        $task->set_timestarted($time);
        $task->set_hostname($hostname);
        $task->set_pid($pid);

        $record = self::record_from_adhoc_task($task);
        $DB->update_record('task_adhoc', $record);
    }

    /**
     * This function indicates that an adhoc task was completed successfully.
     *
     * @param \core\task\adhoc_task $task
     */
    public static function adhoc_task_complete(adhoc_task $task) {
        global $DB;

        // Finalise the log output.
        logmanager::finalise_log();
        $task->set_timestarted();
        $task->set_hostname();
        $task->set_pid();

        // Delete the adhoc task record - it is finished.
        $DB->delete_records('task_adhoc', array('id' => $task->get_id()));

        // Release the locks.
        $task->release_concurrency_lock();
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
        // Finalise the log output.
        logmanager::finalise_log(true);

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

        $task->set_timestarted();
        $task->set_hostname();
        $task->set_pid();

        $classname = self::get_canonical_class_name($task);

        $record = $DB->get_record('task_scheduled', array('classname' => $classname));
        $record->nextruntime = time() + $delay;
        $record->faildelay = $delay;
        $record->timestarted = null;
        $record->hostname = null;
        $record->pid = null;
        $DB->update_record('task_scheduled', $record);

        if ($task->is_blocking()) {
            $task->get_cron_lock()->release();
        }
        $task->get_lock()->release();
    }

    /**
     * Clears the fail delay for the given task and updates its next run time based on the schedule.
     *
     * @param scheduled_task $task Task to reset
     * @throws \dml_exception If there is a database error
     */
    public static function clear_fail_delay(scheduled_task $task) {
        global $DB;

        $record = new \stdClass();
        $record->id = $DB->get_field('task_scheduled', 'id',
                ['classname' => self::get_canonical_class_name($task)]);
        $record->nextruntime = $task->get_next_scheduled_time();
        $record->faildelay = 0;
        $DB->update_record('task_scheduled', $record);
    }

    /**
     * Records that a scheduled task is starting to run.
     *
     * @param scheduled_task $task Task that is starting
     * @param int $time Start time (0 = current)
     * @throws \dml_exception If the task doesn't exist
     */
    public static function scheduled_task_starting(scheduled_task $task, int $time = 0) {
        global $DB;
        $pid = (int)getmypid();
        $hostname = (string)gethostname();

        if (!$time) {
            $time = time();
        }

        $task->set_timestarted($time);
        $task->set_hostname($hostname);
        $task->set_pid($pid);

        $classname = self::get_canonical_class_name($task);
        $record = $DB->get_record('task_scheduled', ['classname' => $classname], '*', MUST_EXIST);
        $record->timestarted = $time;
        $record->hostname = $hostname;
        $record->pid = $pid;
        $DB->update_record('task_scheduled', $record);
    }

    /**
     * This function indicates that a scheduled task was completed successfully and should be rescheduled.
     *
     * @param \core\task\scheduled_task $task
     */
    public static function scheduled_task_complete(scheduled_task $task) {
        global $DB;

        // Finalise the log output.
        logmanager::finalise_log();
        $task->set_timestarted();
        $task->set_hostname();
        $task->set_pid();

        $classname = self::get_canonical_class_name($task);
        $record = $DB->get_record('task_scheduled', array('classname' => $classname));
        if ($record) {
            $record->lastruntime = time();
            $record->faildelay = 0;
            $record->nextruntime = $task->get_next_scheduled_time();
            $record->timestarted = null;
            $record->hostname = null;
            $record->pid = null;

            $DB->update_record('task_scheduled', $record);
        }

        // Reschedule and then release the locks.
        if ($task->is_blocking()) {
            $task->get_cron_lock()->release();
        }
        $task->get_lock()->release();
    }

    /**
     * Gets a list of currently-running tasks.
     *
     * @param  string $sort Sorting method
     * @return array Array of scheduled and adhoc tasks
     * @throws \dml_exception
     */
    public static function get_running_tasks($sort = ''): array {
        global $DB;
        if (empty($sort)) {
            $sort = 'timestarted ASC, classname ASC';
        }
        $params = ['now1' => time(), 'now2' => time()];

        $sql = "SELECT subquery.*
                  FROM (SELECT concat('s', ts.id) as uniqueid,
                               ts.id,
                               'scheduled' as type,
                               ts.classname,
                               (:now1 - ts.timestarted) as time,
                               ts.timestarted,
                               ts.hostname,
                               ts.pid
                          FROM {task_scheduled} ts
                         WHERE ts.timestarted IS NOT NULL
                         UNION ALL
                        SELECT concat('a', ta.id) as uniqueid,
                               ta.id,
                               'adhoc' as type,
                               ta.classname,
                               (:now2 - ta.timestarted) as time,
                               ta.timestarted,
                               ta.hostname,
                               ta.pid
                          FROM {task_adhoc} ta
                         WHERE ta.timestarted IS NOT NULL) subquery
              ORDER BY " . $sort;

        return $DB->get_records_sql($sql, $params);
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

    /**
     * Gets class name for use in database table. Always begins with a \.
     *
     * @param string|task_base $taskorstring Task object or a string
     */
    protected static function get_canonical_class_name($taskorstring) {
        if (is_string($taskorstring)) {
            $classname = $taskorstring;
        } else {
            $classname = get_class($taskorstring);
        }
        if (strpos($classname, '\\') !== 0) {
            $classname = '\\' . $classname;
        }
        return $classname;
    }

    /**
     * Gets the concurrent lock required to run an adhoc task.
     *
     * @param   adhoc_task $task The task to obtain the lock for
     * @return  \core\lock\lock The lock if one was obtained successfully
     * @throws  \coding_exception
     */
    protected static function get_concurrent_task_lock(adhoc_task $task): ?\core\lock\lock {
        $adhoclock = null;
        $cronlockfactory = \core\lock\lock_config::get_lock_factory(get_class($task));

        for ($run = 0; $run < $task->get_concurrency_limit(); $run++) {
            if ($adhoclock = $cronlockfactory->get_lock("concurrent_run_{$run}", 0)) {
                return $adhoclock;
            }
        }

        return null;
    }

    /**
     * Find the path of PHP CLI binary.
     *
     * @return string|false The PHP CLI executable PATH
     */
    protected static function find_php_cli_path() {
        global $CFG;

        if (!empty($CFG->pathtophp) && is_executable(trim($CFG->pathtophp))) {
            return $CFG->pathtophp;
        }

        return false;
    }

    /**
     * Returns if Moodle have access to PHP CLI binary or not.
     *
     * @return bool
     */
    public static function is_runnable():bool {
        return self::find_php_cli_path() !== false;
    }

    /**
     * Executes a cron from web invocation using PHP CLI.
     *
     * @param \core\task\task_base $task Task that be executed via CLI.
     * @return bool
     * @throws \moodle_exception
     */
    public static function run_from_cli(\core\task\task_base $task):bool {
        global $CFG;

        if (!self::is_runnable()) {
            $redirecturl = new \moodle_url('/admin/settings.php', ['section' => 'systempaths']);
            throw new \moodle_exception('cannotfindthepathtothecli', 'core_task', $redirecturl->out());
        } else {
            // Shell-escaped path to the PHP binary.
            $phpbinary = escapeshellarg(self::find_php_cli_path());

            // Shell-escaped path CLI script.
            $pathcomponents = [$CFG->dirroot, $CFG->admin, 'cli', 'scheduled_task.php'];
            $scriptpath     = escapeshellarg(implode(DIRECTORY_SEPARATOR, $pathcomponents));

            // Shell-escaped task name.
            $classname = get_class($task);
            $taskarg   = escapeshellarg("--execute={$classname}") . " " . escapeshellarg("--force");

            // Build the CLI command.
            $command = "{$phpbinary} {$scriptpath} {$taskarg}";

            // Execute it.
            passthru($command);
        }

        return true;
    }
}
