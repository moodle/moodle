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
 * Cron functions.
 *
 * @package    core
 * @subpackage admin
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute cron tasks
 */
function cron_run() {
    global $DB, $CFG, $OUTPUT;

    if (CLI_MAINTENANCE) {
        echo "CLI maintenance mode active, cron execution suspended.\n";
        exit(1);
    }

    if (moodle_needs_upgrading()) {
        echo "Moodle upgrade pending, cron execution suspended.\n";
        exit(1);
    }

    require_once($CFG->libdir.'/adminlib.php');

    if (!empty($CFG->showcronsql)) {
        $DB->set_debug(true);
    }
    if (!empty($CFG->showcrondebugging)) {
        set_debugging(DEBUG_DEVELOPER, true);
    }

    core_php_time_limit::raise();
    $starttime = microtime();

    // Increase memory limit
    raise_memory_limit(MEMORY_EXTRA);

    // Emulate normal session - we use admin accoutn by default
    cron_setup_user();

    // Start output log
    $timenow  = time();
    mtrace("Server Time: ".date('r', $timenow)."\n\n");

    // Record start time and interval between the last cron runs.
    $laststart = get_config('tool_task', 'lastcronstart');
    set_config('lastcronstart', $timenow, 'tool_task');
    if ($laststart) {
        // Record the interval between last two runs (always store at least 1 second).
        set_config('lastcroninterval', max(1, $timenow - $laststart), 'tool_task');
    }

    // Run all scheduled tasks.
    cron_run_scheduled_tasks($timenow);

    // Run adhoc tasks.
    cron_run_adhoc_tasks($timenow);

    mtrace("Cron script completed correctly");

    gc_collect_cycles();
    mtrace('Cron completed at ' . date('H:i:s') . '. Memory used ' . display_size(memory_get_usage()) . '.');
    $difftime = microtime_diff($starttime, microtime());
    mtrace("Execution took ".$difftime." seconds");
}

/**
 * Execute all queued scheduled tasks, applying necessary concurrency limits and time limits.
 *
 * @param   int     $timenow The time this process started.
 * @throws \moodle_exception
 */
function cron_run_scheduled_tasks(int $timenow) {
    // Allow a restriction on the number of scheduled task runners at once.
    $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
    $maxruns = get_config('core', 'task_scheduled_concurrency_limit');
    $maxruntime = get_config('core', 'task_scheduled_max_runtime');

    $scheduledlock = null;
    for ($run = 0; $run < $maxruns; $run++) {
        // If we can't get a lock instantly it means runner N is already running
        // so fail as fast as possible and try N+1 so we don't limit the speed at
        // which we bring new runners into the pool.
        if ($scheduledlock = $cronlockfactory->get_lock("scheduled_task_runner_{$run}", 0)) {
            break;
        }
    }

    if (!$scheduledlock) {
        mtrace("Skipping processing of scheduled tasks. Concurrency limit reached.");
        return;
    }

    $starttime = time();

    // Run all scheduled tasks.
    try {
        while (!\core\local\cli\shutdown::should_gracefully_exit() &&
                !\core\task\manager::static_caches_cleared_since($timenow) &&
                $task = \core\task\manager::get_next_scheduled_task($timenow)) {
            cron_run_inner_scheduled_task($task);
            unset($task);

            if ((time() - $starttime) > $maxruntime) {
                mtrace("Stopping processing of scheduled tasks as time limit has been reached.");
                break;
            }
        }
    } finally {
        // Release the scheduled task runner lock.
        $scheduledlock->release();
    }
}

/**
 * Execute all queued adhoc tasks, applying necessary concurrency limits and time limits.
 *
 * @param   int     $timenow The time this process started.
 * @param   int     $keepalive Keep this function alive for N seconds and poll for new adhoc tasks.
 * @param   bool    $checklimits Should we check limits?
 * @throws \moodle_exception
 */
function cron_run_adhoc_tasks(int $timenow, $keepalive = 0, $checklimits = true) {
    // Allow a restriction on the number of adhoc task runners at once.
    $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
    $maxruns = get_config('core', 'task_adhoc_concurrency_limit');
    $maxruntime = get_config('core', 'task_adhoc_max_runtime');

    if ($checklimits) {
        $adhoclock = null;
        for ($run = 0; $run < $maxruns; $run++) {
            // If we can't get a lock instantly it means runner N is already running
            // so fail as fast as possible and try N+1 so we don't limit the speed at
            // which we bring new runners into the pool.
            if ($adhoclock = $cronlockfactory->get_lock("adhoc_task_runner_{$run}", 0)) {
                break;
            }
        }

        if (!$adhoclock) {
            mtrace("Skipping processing of adhoc tasks. Concurrency limit reached.");
            return;
        }
    }

    $humantimenow = date('r', $timenow);
    $finishtime = $timenow + $keepalive;
    $waiting = false;
    $taskcount = 0;

    // Run all adhoc tasks.
    while (!\core\local\cli\shutdown::should_gracefully_exit() &&
            !\core\task\manager::static_caches_cleared_since($timenow)) {

        if ($checklimits && (time() - $timenow) >= $maxruntime) {
            if ($waiting) {
                $waiting = false;
                mtrace('');
            }
            mtrace("Stopping processing of adhoc tasks as time limit has been reached.");
            break;
        }

        try {
            $task = \core\task\manager::get_next_adhoc_task(time(), $checklimits);
        } catch (Exception $e) {
            if ($adhoclock) {
                // Release the adhoc task runner lock.
                $adhoclock->release();
            }
            throw $e;
        }

        if ($task) {
            if ($waiting) {
                mtrace('');
            }
            $waiting = false;
            cron_run_inner_adhoc_task($task);
            cron_set_process_title("Waiting for next adhoc task");
            $taskcount++;
            unset($task);
        } else {
            $timeleft = $finishtime - time();
            if ($timeleft <= 0) {
                break;
            }
            if (!$waiting) {
                mtrace('Waiting for more adhoc tasks to be queued ', '');
            } else {
                mtrace('.', '');
            }
            $waiting = true;
            cron_set_process_title("Waiting {$timeleft}s for next adhoc task");
            sleep(1);
        }
    }

    if ($waiting) {
        mtrace('');
    }

    mtrace("Ran {$taskcount} adhoc tasks found at {$humantimenow}");

    if ($adhoclock) {
        // Release the adhoc task runner lock.
        $adhoclock->release();
    }
}

/**
 * Shared code that handles running of a single scheduled task within the cron.
 *
 * Not intended for calling directly outside of this library!
 *
 * @param \core\task\task_base $task
 */
function cron_run_inner_scheduled_task(\core\task\task_base $task) {
    global $CFG, $DB;

    \core\task\manager::scheduled_task_starting($task);
    \core\task\logmanager::start_logging($task);

    $fullname = $task->get_name() . ' (' . get_class($task) . ')';
    mtrace('Execute scheduled task: ' . $fullname);
    cron_set_process_title('Scheduled task: ' . get_class($task));
    cron_trace_time_and_memory();
    $predbqueries = null;
    $predbqueries = $DB->perf_get_queries();
    $pretime = microtime(1);
    try {
        get_mailer('buffer');
        cron_prepare_core_renderer();
        $task->execute();
        if ($DB->is_transaction_started()) {
            throw new coding_exception("Task left transaction open");
        }
        if (isset($predbqueries)) {
            mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
            mtrace("... used " . (microtime(1) - $pretime) . " seconds");
        }
        mtrace('Scheduled task complete: ' . $fullname);
        \core\task\manager::scheduled_task_complete($task);
    } catch (Exception $e) {
        if ($DB && $DB->is_transaction_started()) {
            error_log('Database transaction aborted automatically in ' . get_class($task));
            $DB->force_transaction_rollback();
        }
        if (isset($predbqueries)) {
            mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
            mtrace("... used " . (microtime(1) - $pretime) . " seconds");
        }
        mtrace('Scheduled task failed: ' . $fullname . ',' . $e->getMessage());
        if ($CFG->debugdeveloper) {
            if (!empty($e->debuginfo)) {
                mtrace("Debug info:");
                mtrace($e->debuginfo);
            }
            mtrace("Backtrace:");
            mtrace(format_backtrace($e->getTrace(), true));
        }
        \core\task\manager::scheduled_task_failed($task);
    } finally {
        // Reset back to the standard admin user.
        cron_setup_user();
        cron_set_process_title('Waiting for next scheduled task');
        cron_prepare_core_renderer(true);
    }
    get_mailer('close');
}

/**
 * Shared code that handles running of a single adhoc task within the cron.
 *
 * @param \core\task\adhoc_task $task
 */
function cron_run_inner_adhoc_task(\core\task\adhoc_task $task) {
    global $DB, $CFG;

    \core\task\manager::adhoc_task_starting($task);
    \core\task\logmanager::start_logging($task);

    mtrace("Execute adhoc task: " . get_class($task));
    cron_set_process_title('Adhoc task: ' . $task->get_id() . ' ' . get_class($task));
    cron_trace_time_and_memory();
    $predbqueries = null;
    $predbqueries = $DB->perf_get_queries();
    $pretime      = microtime(1);

    if ($userid = $task->get_userid()) {
        // This task has a userid specified.
        if ($user = \core_user::get_user($userid)) {
            // User found. Check that they are suitable.
            try {
                \core_user::require_active_user($user, true, true);
            } catch (moodle_exception $e) {
                mtrace("User {$userid} cannot be used to run an adhoc task: " . get_class($task) . ". Cancelling task.");
                $user = null;
            }
        } else {
            // Unable to find the user for this task.
            // A user missing in the database will never reappear.
            mtrace("User {$userid} could not be found for adhoc task: " . get_class($task) . ". Cancelling task.");
        }

        if (empty($user)) {
            // A user missing in the database will never reappear so the task needs to be failed to ensure that locks are removed,
            // and then removed to prevent future runs.
            // A task running as a user should only be run as that user.
            \core\task\manager::adhoc_task_failed($task);
            $DB->delete_records('task_adhoc', ['id' => $task->get_id()]);

            return;
        }

        cron_setup_user($user);
    }

    try {
        get_mailer('buffer');
        cron_prepare_core_renderer();
        $task->execute();
        if ($DB->is_transaction_started()) {
            throw new coding_exception("Task left transaction open");
        }
        if (isset($predbqueries)) {
            mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
            mtrace("... used " . (microtime(1) - $pretime) . " seconds");
        }
        mtrace("Adhoc task complete: " . get_class($task));
        \core\task\manager::adhoc_task_complete($task);
    } catch (Exception $e) {
        if ($DB && $DB->is_transaction_started()) {
            error_log('Database transaction aborted automatically in ' . get_class($task));
            $DB->force_transaction_rollback();
        }
        if (isset($predbqueries)) {
            mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
            mtrace("... used " . (microtime(1) - $pretime) . " seconds");
        }
        mtrace("Adhoc task failed: " . get_class($task) . "," . $e->getMessage());
        if ($CFG->debugdeveloper) {
            if (!empty($e->debuginfo)) {
                mtrace("Debug info:");
                mtrace($e->debuginfo);
            }
            mtrace("Backtrace:");
            mtrace(format_backtrace($e->getTrace(), true));
        }
        \core\task\manager::adhoc_task_failed($task);
    } finally {
        // Reset back to the standard admin user.
        cron_setup_user();
        cron_prepare_core_renderer(true);
    }
    get_mailer('close');
}

/**
 * Sets the process title
 *
 * This makes it very easy for a sysadmin to immediately see what task
 * a cron process is running at any given moment.
 *
 * @param string $title process status title
 */
function cron_set_process_title(string $title) {
    global $CFG;
    if (CLI_SCRIPT) {
        require_once($CFG->libdir . '/clilib.php');
        $datetime = userdate(time(), '%b %d, %H:%M:%S');
        cli_set_process_title_suffix("$datetime $title");
    }
}

/**
 * Output some standard information during cron runs. Specifically current time
 * and memory usage. This method also does gc_collect_cycles() (before displaying
 * memory usage) to try to help PHP manage memory better.
 */
function cron_trace_time_and_memory() {
    gc_collect_cycles();
    mtrace('... started ' . date('H:i:s') . '. Current memory use ' . display_size(memory_get_usage()) . '.');
}

/**
 * Prepare the output renderer for the cron run.
 *
 * This involves creating a new $PAGE, and $OUTPUT fresh for each task and prevents any one task from influencing
 * any other.
 *
 * @param   bool    $restore Whether to restore the original PAGE and OUTPUT
 */
function cron_prepare_core_renderer($restore = false) {
    global $OUTPUT, $PAGE;

    // Store the original PAGE and OUTPUT values so that they can be reset at a later point to the original.
    // This should not normally be required, but may be used in places such as the scheduled task tool's "Run now"
    // functionality.
    static $page = null;
    static $output = null;

    if (null === $page) {
        $page = $PAGE;
    }

    if (null === $output) {
        $output = $OUTPUT;
    }

    if (!empty($restore)) {
        $PAGE = $page;
        $page = null;

        $OUTPUT = $output;
        $output = null;
    } else {
        // Setup a new General renderer.
        // Cron tasks may produce output to be used in web, so we must use the appropriate renderer target.
        // This allows correct use of templates, etc.
        $PAGE = new \moodle_page();
        $OUTPUT = new \core_renderer($PAGE, RENDERER_TARGET_GENERAL);
    }
}
