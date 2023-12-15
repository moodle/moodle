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

use core\lock\lock;
use core\lock\lock_factory;

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
     * @var int Used to tell the adhoc task queue to fairly distribute tasks.
     */
    const ADHOC_TASK_QUEUE_MODE_DISTRIBUTING = 0;

    /**
     * @var int Used to tell the adhoc task queue to try and fill unused capacity.
     */
    const ADHOC_TASK_QUEUE_MODE_FILLING = 1;

    /**
     * @var array A cached queue of adhoc tasks
     */
    public static $miniqueue;

    /**
     * @var int The last recorded number of unique adhoc tasks.
     */
    public static $numtasks;

    /**
     * @var string Used to determine if the adhoc task queue is distributing or filling capacity.
     */
    public static $mode;

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
            $scheduledtask = self::scheduled_task_from_record($record, $expandr, false);
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
     * @return \stdClass|false
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
        unset($record->lastruntime);
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
     * @throws \moodle_exception
     */
    public static function adhoc_task_from_record($record) {
        $classname = self::get_canonical_class_name($record->classname);
        if (!class_exists($classname)) {
            throw new \moodle_exception('invalidtaskclassname', '', '', $record->classname);
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
     * @param bool $override - if true loads overridden settings from config.
     * @return \core\task\scheduled_task|false
     */
    public static function scheduled_task_from_record($record, $expandr = true, $override = true) {
        $classname = self::get_canonical_class_name($record->classname);
        if (!class_exists($classname)) {
            debugging("Failed to load task: " . $classname, DEBUG_DEVELOPER);
            return false;
        }
        /** @var \core\task\scheduled_task $task */
        $task = new $classname;

        if ($override) {
            // Update values with those defined in the config, if any are set.
            $record = self::get_record_with_config_overrides($record);
        }

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
        $task->set_overridden(self::scheduled_task_has_override($classname));

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
     * @param bool $failedonly
     * @param bool $skiprunning do not return tasks that are in the running state
     * @return array
     */
    public static function get_adhoc_tasks(string $classname, bool $failedonly = false, bool $skiprunning = false): array {
        global $DB;

        $conds[] = 'classname = ?';
        $params[] = self::get_canonical_class_name($classname);

        if ($failedonly) {
            $conds[] = 'faildelay > 0';
        }
        if ($skiprunning) {
            $conds[] = 'timestarted IS NULL';
        }

        // We are just reading - so no locks required.
        $sql = 'SELECT * FROM {task_adhoc}';
        if ($conds) {
            $sql .= ' WHERE '.implode(' AND ', $conds);
        }
        $rs = $DB->get_records_sql($sql, $params);
        return array_map(function($record) {
            return self::adhoc_task_from_record($record);
        }, $rs);
    }

    /**
     * This function returns adhoc tasks summary per component classname
     *
     * @return array
     */
    public static function get_adhoc_tasks_summary(): array {
        global $DB;

        $now = time();
        $records = $DB->get_records('task_adhoc');
        $summary = [];
        foreach ($records as $r) {
            if (!isset($summary[$r->component])) {
                $summary[$r->component] = [];
            }

            if (isset($summary[$r->component][$r->classname])) {
                $classsummary = $summary[$r->component][$r->classname];
            } else {
                $classsummary = [
                    'nextruntime' => null,
                    'count' => 0,
                    'failed' => 0,
                    'running' => 0,
                    'due' => 0,
                ];
            }

            $classsummary['count']++;
            $nextruntime = (int)$r->nextruntime;
            if (!$classsummary['nextruntime'] || $nextruntime < $classsummary['nextruntime']) {
                $classsummary['nextruntime'] = $nextruntime;
            }

            if ((int)$r->timestarted > 0) {
                $classsummary['running']++;
            } else {
                if ((int)$r->faildelay > 0) {
                    $classsummary['failed']++;
                }

                if ($nextruntime <= $now) {
                    $classsummary['due']++;
                }
            }

            $summary[$r->component][$r->classname] = $classsummary;
        }
        return $summary;
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
     * This function will return a list of all adhoc tasks that have a faildelay
     *
     * @param int $delay filter how long the task has been delayed
     * @return \core\task\adhoc_task[]
     */
    public static function get_failed_adhoc_tasks(int $delay = 0): array {
        global $DB;

        $tasks = [];
        $records = $DB->get_records_sql('SELECT * from {task_adhoc} WHERE faildelay > ?', [$delay]);

        foreach ($records as $record) {
            try {
                $tasks[] = self::adhoc_task_from_record($record);
            } catch (\moodle_exception $e) {
                debugging("Failed to load task: $record->classname", DEBUG_DEVELOPER, $e->getTrace());
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
     * @deprecated since Moodle 4.1 MDL-67648 - please do not use this method anymore.
     * @todo MDL-74843 This method will be deleted in Moodle 4.5
     * @see \core\task\manager::get_next_adhoc_task
     */
    public static function ensure_adhoc_task_qos(array $records): array {
        debugging('The method \core\task\manager::ensure_adhoc_task_qos is deprecated.
             Please use \core\task\manager::get_next_adhoc_task instead.', DEBUG_DEVELOPER);

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
     * @param string|null $classname Return only task of this class
     * @return \core\task\adhoc_task|null
     * @throws \moodle_exception
     */
    public static function get_next_adhoc_task(int $timestart, ?bool $checklimits = true, ?string $classname = null): ?adhoc_task {
        global $DB;

        $concurrencylimit = get_config('core', 'task_adhoc_concurrency_limit');
        $cachedqueuesize = 1200;

        $uniquetasksinqueue = array_map(
            ['\core\task\manager', 'adhoc_task_from_record'],
            $DB->get_records_sql(
                'SELECT classname FROM {task_adhoc} WHERE nextruntime < :timestart GROUP BY classname',
                ['timestart' => $timestart]
            )
        );

        if (!isset(self::$numtasks) || self::$numtasks !== count($uniquetasksinqueue)) {
            self::$numtasks = count($uniquetasksinqueue);
            self::$miniqueue = [];
        }

        $concurrencylimits = [];
        if ($checklimits) {
            $concurrencylimits = array_map(
                function ($task) {
                    return $task->get_concurrency_limit();
                },
                $uniquetasksinqueue
            );
        }

        /*
         * The maximum number of cron runners that an individual task is allowed to use.
         * For example if the concurrency limit is 20 and there are 5 unique types of tasks
         * in the queue, each task should not be allowed to consume more than 3 (i.e., ⌊20/6⌋).
         * The + 1 is needed to prevent the queue from becoming full of only one type of class.
         * i.e., if it wasn't there and there were 20 tasks of the same type in the queue, every
         * runner would become consumed with the same (potentially long-running task) and no more
         * tasks can run. This way, some resources are always available if some new types
         * of tasks enter the queue.
         *
         * We use the short-ternary to force the value to 1 in the case when the number of tasks
         * exceeds the runners (e.g., there are 8 tasks and 4 runners, ⌊4/(8+1)⌋ = 0).
         */
        $slots = floor($concurrencylimit / (count($uniquetasksinqueue) + 1)) ?: 1;
        if (empty(self::$miniqueue)) {
            self::$mode = self::ADHOC_TASK_QUEUE_MODE_DISTRIBUTING;
            self::$miniqueue = self::get_candidate_adhoc_tasks(
                $timestart,
                $cachedqueuesize,
                $slots,
                $concurrencylimits
            );
        }

        // The query to cache tasks is expensive on big data sets, so we use this cheap
        // query to get the ordering (which is the interesting part about the main query)
        // We can use this information to filter the cache and also order it.
        $runningtasks = $DB->get_records_sql(
            'SELECT classname, COALESCE(COUNT(*), 0) running, MIN(timestarted) earliest
               FROM {task_adhoc}
              WHERE timestarted IS NOT NULL
                    AND nextruntime < :timestart
           GROUP BY classname
           ORDER BY running ASC, earliest DESC',
            ['timestart' => $timestart]
        );

        /*
         * Each runner has a cache, so the same task can be in multiple runners' caches.
         * We need to check that each task we have cached hasn't gone over its fair number
         * of slots. This filtering is only applied during distributing mode as when we are
         * filling capacity we intend for fast tasks to go over their slot limit.
         */
        if (self::$mode === self::ADHOC_TASK_QUEUE_MODE_DISTRIBUTING) {
            self::$miniqueue = array_filter(
                self::$miniqueue,
                function (\stdClass $task) use ($runningtasks, $slots) {
                    return !array_key_exists($task->classname, $runningtasks) || $runningtasks[$task->classname]->running < $slots;
                }
            );
        }

        /*
         * If this happens that means each task has consumed its fair share of capacity, but there's still
         * runners left over (and we are one of them). Fetch tasks without checking slot limits.
         */
        if (empty(self::$miniqueue) && array_sum(array_column($runningtasks, 'running')) < $concurrencylimit) {
            self::$mode = self::ADHOC_TASK_QUEUE_MODE_FILLING;
            self::$miniqueue = self::get_candidate_adhoc_tasks(
                $timestart,
                $cachedqueuesize,
                false,
                $concurrencylimits
            );
        }

        // Used below to order the cache.
        $ordering = array_flip(array_keys($runningtasks));

        // Order the queue so it's consistent with the ordering from the DB.
        usort(
            self::$miniqueue,
            function ($a, $b) use ($ordering) {
                return ($ordering[$a->classname] ?? -1) - ($ordering[$b->classname] ?? -1);
            }
        );

        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');

        $skipclasses = array();

        foreach (self::$miniqueue as $taskid => $record) {

            if (!empty($classname) && $record->classname != self::get_canonical_class_name($classname)) {
                // Skip the task if The class is specified, and doesn't match.
                continue;
            }

            if (in_array($record->classname, $skipclasses)) {
                // Skip the task if it can't be started due to per-task concurrency limit.
                continue;
            }

            if ($lock = $cronlockfactory->get_lock('adhoc_' . $record->id, 0)) {

                // Safety check, see if the task has been already processed by another cron run.
                $record = $DB->get_record('task_adhoc', array('id' => $record->id));
                if (!$record) {
                    $lock->release();
                    unset(self::$miniqueue[$taskid]);
                    continue;
                }

                // Safety check in case the task in the DB does not match a real class (maybe something was uninstalled).
                try {
                    $task = self::adhoc_task_from_record($record);
                } catch (\moodle_exception $e) {
                    debugging("Failed to load task: $record->classname", DEBUG_DEVELOPER);
                    $lock->release();
                    unset(self::$miniqueue[$taskid]);
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
                        unset(self::$miniqueue[$taskid]);
                        $lock->release();
                        continue;
                    }
                }

                self::set_locks($task, $lock, $cronlockfactory);
                unset(self::$miniqueue[$taskid]);

                return $task;
            } else {
                unset(self::$miniqueue[$taskid]);
            }
        }

        return null;
    }

    /**
     * Return a list of candidate adhoc tasks to run.
     *
     * @param int $timestart Only return tasks where nextruntime is less than this value
     * @param int $limit Limit the list to this many results
     * @param int|null $runmax Only return tasks that have less than this value currently running
     * @param array $pertasklimits An array of classname => limit specifying how many instance of a task may be returned
     * @return array Array of candidate tasks
     */
    public static function get_candidate_adhoc_tasks(
        int $timestart,
        int $limit,
        ?int $runmax,
        array $pertasklimits = []
    ): array {
        global $DB;

        $pertaskclauses = array_map(
            function (string $class, int $limit, int $index): array {
                $limitcheck = $limit > 0 ? " AND COALESCE(run.running, 0) < :running_$index" : "";
                $limitparam = $limit > 0 ? ["running_$index" => $limit] : [];

                return [
                    "sql" => "(q.classname = :classname_$index" . $limitcheck . ")",
                    "params" => ["classname_$index" => $class] + $limitparam
                ];
            },
            array_keys($pertasklimits),
            $pertasklimits,
            $pertasklimits ? range(1, count($pertasklimits)) : []
        );

        $pertasksql = implode(" OR ", array_column($pertaskclauses, 'sql'));
        $pertaskparams = $pertaskclauses ? array_merge(...array_column($pertaskclauses, 'params')) : [];

        $params = ['timestart' => $timestart] +
                ($runmax ? ['runmax' => $runmax] : []) +
                $pertaskparams;

        return $DB->get_records_sql(
            "SELECT q.id, q.classname, q.timestarted, COALESCE(run.running, 0) running, run.earliest
              FROM {task_adhoc} q
         LEFT JOIN (
                       SELECT classname, COUNT(*) running, MIN(timestarted) earliest
                         FROM {task_adhoc} run
                        WHERE timestarted IS NOT NULL
                     GROUP BY classname
                   ) run ON run.classname = q.classname
             WHERE nextruntime < :timestart
                   AND q.timestarted IS NULL " .
            (!empty($pertasksql) ? "AND (" . $pertasksql . ") " : "") .
            ($runmax ? "AND (COALESCE(run.running, 0)) < :runmax " : "") .
         "ORDER BY COALESCE(run.running, 0) ASC, run.earliest DESC, q.nextruntime ASC, q.id ASC",
            $params,
            0,
            $limit
        );
    }

    /**
     * This function will get an adhoc task by id. The task will be handed out
     * with an open lock - possibly on the entire cron process. Make sure you call either
     * {@see ::adhoc_task_failed} or {@see ::adhoc_task_complete} to release the lock and reschedule the task.
     *
     * @param int $taskid
     * @return \core\task\adhoc_task|null
     * @throws \moodle_exception
     */
    public static function get_adhoc_task(int $taskid): ?adhoc_task {
        global $DB;

        $record = $DB->get_record('task_adhoc', ['id' => $taskid]);
        if (!$record) {
            throw new \moodle_exception('invalidtaskid');
        }

        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');

        if ($lock = $cronlockfactory->get_lock('adhoc_' . $record->id, 0)) {
            // Safety check in case the task in the DB does not match a real class (maybe something was uninstalled).
            try {
                $task = self::adhoc_task_from_record($record);
            } catch (\moodle_exception $e) {
                $lock->release();
                throw $e;
            }

            self::set_locks($task, $lock, $cronlockfactory);
            return $task;
        }

        return null;
    }

    /**
     * This function will set locks on the task.
     *
     * @param adhoc_task    $task
     * @param lock          $lock task lock
     * @param lock_factory  $cronlockfactory
     * @throws \moodle_exception
     */
    private static function set_locks(adhoc_task $task, lock $lock, lock_factory $cronlockfactory): void {
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
                  ORDER BY lastruntime, id ASC";
        $params = array('timestart1' => $timestart, 'timestart2' => $timestart);
        $records = $DB->get_records_select('task_scheduled', $where, $params);

        $pluginmanager = \core_plugin_manager::instance();

        foreach ($records as $record) {

            $task = self::scheduled_task_from_record($record);
            // Safety check in case the task in the DB does not match a real class (maybe something was uninstalled).
            // Also check to see if task is disabled or enabled after applying overrides.
            if (!$task || $task->get_disabled()) {
                continue;
            }

            if ($lock = $cronlockfactory->get_lock(($record->classname), 0)) {
                $classname = '\\' . $record->classname;

                $task->set_lock($lock);

                // See if the component is disabled.
                $plugininfo = $pluginmanager->get_plugin_info($task->get_component());

                if ($plugininfo) {
                    if (($plugininfo->is_enabled() === false) && !$task->get_run_if_component_disabled()) {
                        $lock->release();
                        continue;
                    }
                }

                if (!self::scheduled_task_has_override($record->classname)) {
                    // Make sure the task data is unchanged unless an override is being used.
                    if (!$DB->record_exists('task_scheduled', (array)$record)) {
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
                  FROM (SELECT " . $DB->sql_concat("'s'", 'ts.id') . " as uniqueid,
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
                        SELECT " . $DB->sql_concat("'a'", 'ta.id') . " as uniqueid,
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
     * Cleanup stale task metadata.
     */
    public static function cleanup_metadata() {
        global $DB;

        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
        $runningtasks = self::get_running_tasks();

        foreach ($runningtasks as $runningtask) {
            if ($runningtask->timestarted > time() - HOURSECS) {
                continue;
            }

            if ($runningtask->type == 'adhoc') {
                $lock = $cronlockfactory->get_lock('adhoc_' . $runningtask->id, 0);
            }

            if ($runningtask->type == 'scheduled') {
                $lock = $cronlockfactory->get_lock($runningtask->classname, 0);
            }

            // If we got this lock it means one of three things:
            //
            // 1. The task was stopped abnormally and the metadata was not cleaned up
            // 2. This is the process running the cleanup task
            // 3. We took so long getting to it in this loop that it did finish, and we now have the lock
            //
            // In the case of 1. we need to make the task as failed, in the case of 2. and 3. we do nothing.
            if (!empty($lock)) {
                if ($runningtask->classname == "\\" . \core\task\task_lock_cleanup_task::class) {
                    $lock->release();
                    continue;
                }

                // We need to get the record again to verify whether or not we are dealing with case 3.
                $taskrecord = $DB->get_record('task_' . $runningtask->type, ['id' => $runningtask->id]);

                if ($runningtask->type == 'scheduled') {
                    // Empty timestarted indicates that this task finished (case 3) and was properly cleaned up.
                    if (empty($taskrecord->timestarted)) {
                        $lock->release();
                        continue;
                    }

                    $task = self::scheduled_task_from_record($taskrecord);
                    $task->set_lock($lock);
                    self::scheduled_task_failed($task);
                } else if ($runningtask->type == 'adhoc') {
                    // Ad hoc tasks are removed from the DB if they finish successfully.
                    // If we can't re-get this task, that means it finished and was properly
                    // cleaned up.
                    if (!$taskrecord) {
                        $lock->release();
                        continue;
                    }

                    $task = self::adhoc_task_from_record($taskrecord);
                    $task->set_lock($lock);
                    self::adhoc_task_failed($task);
                }
            }
        }
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
    public static function get_canonical_class_name($taskorstring) {
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
     * @param scheduled_task $task Task that be executed via CLI.
     * @return bool
     * @throws \moodle_exception
     */
    public static function run_from_cli(scheduled_task $task): bool {
        global $CFG;

        if (!self::is_runnable()) {
            $redirecturl = new \moodle_url('/admin/settings.php', ['section' => 'systempaths']);
            throw new \moodle_exception('cannotfindthepathtothecli', 'tool_task', $redirecturl->out());
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
            self::passthru_via_mtrace($command);
        }

        return true;
    }

    /**
     * This behaves similar to passthru but filters every line via
     * the mtrace function so it can be post processed.
     *
     * @param string $command to run
     * @return void
     */
    public static function passthru_via_mtrace(string $command) {
        $descriptorspec = [
            0 => ['pipe', 'r'], // STDIN.
            1 => ['pipe', 'w'], // STDOUT.
            2 => ['pipe', 'w'], // STDERR.
        ];
        flush();
        $process = proc_open($command, $descriptorspec, $pipes, realpath('./'), []);
        if (is_resource($process)) {
            while ($s = fgets($pipes[1])) {
                mtrace($s, '');
                flush();
            }
        }

        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);
    }

    /**
     * Executes an ad hoc task from web invocation using PHP CLI.
     *
     * @param int   $taskid Task to execute via CLI.
     * @throws \moodle_exception
     */
    public static function run_adhoc_from_cli(int $taskid) {
        // Shell-escaped task name.
        $taskarg = escapeshellarg("--id={$taskid}");

        self::run_adhoc_from_cli_base($taskarg);
    }

    /**
     * Executes ad hoc tasks from web invocation using PHP CLI.
     *
     * @param bool|null   $failedonly
     * @param string|null $classname  Task class to execute via CLI.
     * @throws \moodle_exception
     */
    public static function run_all_adhoc_from_cli(?bool $failedonly = false, ?string $classname = null) {
        $taskargs = [];
        if ($failedonly) {
            $taskargs[] = '--failed';
        }
        if ($classname) {
            // Shell-escaped task select.
            $taskargs[] = escapeshellarg("--classname={$classname}");
        }

        self::run_adhoc_from_cli_base($taskargs ? implode(' ', $taskargs) : '--execute');
    }

    /**
     * Executes an ad hoc task from web invocation using PHP CLI.
     *
     * @param string $taskarg Task to execute via CLI.
     * @throws \moodle_exception
     */
    private static function run_adhoc_from_cli_base(string $taskarg): void {
        global $CFG;

        if (!self::is_runnable()) {
            $redirecturl = new \moodle_url('/admin/settings.php', ['section' => 'systempaths']);
            throw new \moodle_exception('cannotfindthepathtothecli', 'tool_task', $redirecturl->out());
        }

        // Shell-escaped path to the PHP binary.
        $phpbinary = escapeshellarg(self::find_php_cli_path());

        // Shell-escaped path CLI script.
        $pathcomponents = [$CFG->dirroot, $CFG->admin, 'cli', 'adhoc_task.php'];
        $scriptpath = escapeshellarg(implode(DIRECTORY_SEPARATOR, $pathcomponents));

        // Build the CLI command.
        $command = "{$phpbinary} {$scriptpath} {$taskarg} --force";

        // We cannot run it in phpunit.
        if (PHPUNIT_TEST) {
            echo $command;
            return;
        }

        // Execute it.
        self::passthru_via_mtrace($command);
    }

    /**
     * For a given scheduled task record, this method will check to see if any overrides have
     * been applied in config and return a copy of the record with any overridden values.
     *
     * The format of the config value is:
     *      $CFG->scheduled_tasks = array(
     *          '$classname' => array(
     *              'schedule' => '* * * * *',
     *              'disabled' => 1,
     *          ),
     *      );
     *
     * Where $classname is the value of the task's classname, i.e. '\core\task\grade_cron_task'.
     *
     * @param \stdClass $record scheduled task record
     * @return \stdClass scheduled task with any configured overrides
     */
    protected static function get_record_with_config_overrides(\stdClass $record): \stdClass {
        global $CFG;

        $scheduledtaskkey = self::scheduled_task_get_override_key($record->classname);
        $overriddenrecord = $record;

        if ($scheduledtaskkey) {
            $overriddenrecord->customised = true;
            $taskconfig = $CFG->scheduled_tasks[$scheduledtaskkey];

            if (isset($taskconfig['disabled'])) {
                $overriddenrecord->disabled = $taskconfig['disabled'];
            }
            if (isset($taskconfig['schedule'])) {
                list (
                    $overriddenrecord->minute,
                    $overriddenrecord->hour,
                    $overriddenrecord->day,
                    $overriddenrecord->month,
                    $overriddenrecord->dayofweek
                ) = explode(' ', $taskconfig['schedule']);
            }
        }

        return $overriddenrecord;
    }

    /**
     * This checks whether or not there is a value set in config
     * for a scheduled task.
     *
     * @param string $classname Scheduled task's classname
     * @return bool true if there is an entry in config
     */
    public static function scheduled_task_has_override(string $classname): bool {
        return self::scheduled_task_get_override_key($classname) !== null;
    }

    /**
     * Get the key within the scheduled tasks config object that
     * for a classname.
     *
     * @param string $classname the scheduled task classname to find
     * @return string the key if found, otherwise null
     */
    public static function scheduled_task_get_override_key(string $classname): ?string {
        global $CFG;

        if (isset($CFG->scheduled_tasks)) {
            // Firstly, attempt to get a match against the full classname.
            if (isset($CFG->scheduled_tasks[$classname])) {
                return $classname;
            }

            // Check to see if there is a wildcard matching the classname.
            foreach (array_keys($CFG->scheduled_tasks) as $key) {
                if (strpos($key, '*') === false) {
                    continue;
                }

                $pattern = '/' . str_replace('\\', '\\\\', str_replace('*', '.*', $key)) . '/';

                if (preg_match($pattern, $classname)) {
                    return $key;
                }
            }
        }

        return null;
    }
}
