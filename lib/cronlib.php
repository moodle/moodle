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
 */
function cron_run_scheduled_tasks(int $timenow) {
    // Allow a restriction on the number of scheduled task runners at once.
    $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
    $maxruns = get_config('core', 'task_scheduled_concurrency_limit');
    $maxruntime = get_config('core', 'task_scheduled_max_runtime');

    $scheduledlock = null;
    for ($run = 0; $run < $maxruns; $run++) {
        if ($scheduledlock = $cronlockfactory->get_lock("scheduled_task_runner_{$run}", 1)) {
            break;
        }
    }

    if (!$scheduledlock) {
        mtrace("Skipping processing of scheduled tasks. Concurrency limit reached.");
        return;
    }

    $starttime = time();

    // Run all scheduled tasks.
    while (!\core\task\manager::static_caches_cleared_since($timenow) &&
            $task = \core\task\manager::get_next_scheduled_task($timenow)) {
        cron_run_inner_scheduled_task($task);
        unset($task);

        if ((time() - $starttime) > $maxruntime) {
            mtrace("Stopping processing of scheduled tasks as time limit has been reached.");
            break;
        }
    }

    // Release the scheduled task runner lock.
    $scheduledlock->release();
}

/**
 * Execute all queued adhoc tasks, applying necessary concurrency limits and time limits.
 *
 * @param   int     $timenow The time this process started.
 */
function cron_run_adhoc_tasks(int $timenow) {
    // Allow a restriction on the number of adhoc task runners at once.
    $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
    $maxruns = get_config('core', 'task_adhoc_concurrency_limit');
    $maxruntime = get_config('core', 'task_adhoc_max_runtime');

    $adhoclock = null;
    for ($run = 0; $run < $maxruns; $run++) {
        if ($adhoclock = $cronlockfactory->get_lock("adhoc_task_runner_{$run}", 1)) {
            break;
        }
    }

    if (!$adhoclock) {
        mtrace("Skipping processing of adhoc tasks. Concurrency limit reached.");
        return;
    }

    $starttime = time();

    // Run all adhoc tasks.
    while (!\core\task\manager::static_caches_cleared_since($timenow) &&
            $task = \core\task\manager::get_next_adhoc_task($timenow)) {
        cron_run_inner_adhoc_task($task);
        unset($task);

        if ((time() - $starttime) > $maxruntime) {
            mtrace("Stopping processing of adhoc tasks as time limit has been reached.");
            break;
        }
    }

    // Release the adhoc task runner lock.
    $adhoclock->release();
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

    \core\task\logmanager::start_logging($task);

    $fullname = $task->get_name() . ' (' . get_class($task) . ')';
    mtrace('Execute scheduled task: ' . $fullname);
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

    \core\task\logmanager::start_logging($task);

    mtrace("Execute adhoc task: " . get_class($task));
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
 * Runs a single cron task. This function assumes it is displaying output in pseudo-CLI mode.
 *
 * The function will fail if the task is disabled.
 *
 * Warning: Because this function closes the browser session, it may not be safe to continue
 * with other processing (other than displaying the rest of the page) after using this function!
 *
 * @param \core\task\scheduled_task $task Task to run
 * @return bool True if cron run successful
 */
function cron_run_single_task(\core\task\scheduled_task $task) {
    global $CFG, $DB, $USER;

    if (CLI_MAINTENANCE) {
        echo "CLI maintenance mode active, cron execution suspended.\n";
        return false;
    }

    if (moodle_needs_upgrading()) {
        echo "Moodle upgrade pending, cron execution suspended.\n";
        return false;
    }

    // Check task and component is not disabled.
    $taskname = get_class($task);
    if ($task->get_disabled()) {
        echo "Task is disabled ($taskname).\n";
        return false;
    }
    $component = $task->get_component();
    if ($plugininfo = core_plugin_manager::instance()->get_plugin_info($component)) {
        if ($plugininfo->is_enabled() === false && !$task->get_run_if_component_disabled()) {
            echo "Component is not enabled ($component).\n";
            return false;
        }
    }

    // Enable debugging features as per config settings.
    if (!empty($CFG->showcronsql)) {
        $DB->set_debug(true);
    }
    if (!empty($CFG->showcrondebugging)) {
        set_debugging(DEBUG_DEVELOPER, true);
    }

    // Increase time and memory limits.
    core_php_time_limit::raise();
    raise_memory_limit(MEMORY_EXTRA);

    // Switch to admin account for cron tasks, but close the session so we don't send this stuff
    // to the browser.
    session_write_close();
    $realuser = clone($USER);
    cron_setup_user(null, null, true);

    // Get lock for cron task.
    $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
    if (!$cronlock = $cronlockfactory->get_lock('core_cron', 1)) {
        echo "Unable to get cron lock.\n";
        return false;
    }
    if (!$lock = $cronlockfactory->get_lock($taskname, 1)) {
        $cronlock->release();
        echo "Unable to get task lock for $taskname.\n";
        return false;
    }
    $task->set_lock($lock);
    if (!$task->is_blocking()) {
        $cronlock->release();
    } else {
        $task->set_cron_lock($cronlock);
    }

    // Run actual tasks.
    cron_run_inner_scheduled_task($task);

    // Go back to real user account.
    cron_setup_user($realuser, null, true);

    return true;
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
 * Executes cron functions for a specific type of plugin.
 *
 * @param string $plugintype Plugin type (e.g. 'report')
 * @param string $description If specified, will display 'Starting (whatever)'
 *   and 'Finished (whatever)' lines, otherwise does not display
 */
function cron_execute_plugin_type($plugintype, $description = null) {
    global $DB;

    // Get list from plugin => function for all plugins
    $plugins = get_plugin_list_with_function($plugintype, 'cron');

    // Modify list for backward compatibility (different files/names)
    $plugins = cron_bc_hack_plugin_functions($plugintype, $plugins);

    // Return if no plugins with cron function to process
    if (!$plugins) {
        return;
    }

    if ($description) {
        mtrace('Starting '.$description);
    }

    foreach ($plugins as $component=>$cronfunction) {
        $dir = core_component::get_component_directory($component);

        // Get cron period if specified in version.php, otherwise assume every cron
        $cronperiod = 0;
        if (file_exists("$dir/version.php")) {
            $plugin = new stdClass();
            include("$dir/version.php");
            if (isset($plugin->cron)) {
                $cronperiod = $plugin->cron;
            }
        }

        // Using last cron and cron period, don't run if it already ran recently
        $lastcron = get_config($component, 'lastcron');
        if ($cronperiod && $lastcron) {
            if ($lastcron + $cronperiod > time()) {
                // do not execute cron yet
                continue;
            }
        }

        mtrace('Processing cron function for ' . $component . '...');
        cron_trace_time_and_memory();
        $pre_dbqueries = $DB->perf_get_queries();
        $pre_time = microtime(true);

        $cronfunction();

        mtrace("done. (" . ($DB->perf_get_queries() - $pre_dbqueries) . " dbqueries, " .
                round(microtime(true) - $pre_time, 2) . " seconds)");

        set_config('lastcron', time(), $component);
        core_php_time_limit::raise();
    }

    if ($description) {
        mtrace('Finished ' . $description);
    }
}

/**
 * Used to add in old-style cron functions within plugins that have not been converted to the
 * new standard API. (The standard API is frankenstyle_name_cron() in lib.php; some types used
 * cron.php and some used a different name.)
 *
 * @param string $plugintype Plugin type e.g. 'report'
 * @param array $plugins Array from plugin name (e.g. 'report_frog') to function name (e.g.
 *   'report_frog_cron') for plugin cron functions that were already found using the new API
 * @return array Revised version of $plugins that adds in any extra plugin functions found by
 *   looking in the older location
 */
function cron_bc_hack_plugin_functions($plugintype, $plugins) {
    global $CFG; // mandatory in case it is referenced by include()d PHP script

    if ($plugintype === 'report') {
        // Admin reports only - not course report because course report was
        // never implemented before, so doesn't need BC
        foreach (core_component::get_plugin_list($plugintype) as $pluginname=>$dir) {
            $component = $plugintype . '_' . $pluginname;
            if (isset($plugins[$component])) {
                // We already have detected the function using the new API
                continue;
            }
            if (!file_exists("$dir/cron.php")) {
                // No old style cron file present
                continue;
            }
            include_once("$dir/cron.php");
            $cronfunction = $component . '_cron';
            if (function_exists($cronfunction)) {
                $plugins[$component] = $cronfunction;
            } else {
                debugging("Invalid legacy cron.php detected in $component, " .
                        "please use lib.php instead");
            }
        }
    } else if (strpos($plugintype, 'grade') === 0) {
        // Detect old style cron function names
        // Plugin gradeexport_frog used to use grade_export_frog_cron() instead of
        // new standard API gradeexport_frog_cron(). Also applies to gradeimport, gradereport
        foreach(core_component::get_plugin_list($plugintype) as $pluginname=>$dir) {
            $component = $plugintype.'_'.$pluginname;
            if (isset($plugins[$component])) {
                // We already have detected the function using the new API
                continue;
            }
            if (!file_exists("$dir/lib.php")) {
                continue;
            }
            include_once("$dir/lib.php");
            $cronfunction = str_replace('grade', 'grade_', $plugintype) . '_' .
                    $pluginname . '_cron';
            if (function_exists($cronfunction)) {
                $plugins[$component] = $cronfunction;
            }
        }
    }

    return $plugins;
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
