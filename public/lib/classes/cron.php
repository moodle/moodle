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

namespace core;

use coding_exception;
use core_php_time_limit;
use moodle_exception;
use stdClass;

// Disable the moodle.PHP.ForbiddenFunctions.FoundWithAlternative sniff for this file.
// It detects uses of error_log() which are valid in this file.
// phpcs:disable moodle.PHP.ForbiddenFunctions.FoundWithAlternative

/**
 * Cron and adhoc task functionality.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cron {

    /** @var ?stdClass A copy of the standard cron 'user' */
    protected static ?stdClass $cronuser = null;

    /** @var ?stdClass The cron user's session data */
    protected static ?stdClass $cronsession = null;

    /**
     * Use a default value of 3 minutes.
     * The recommended cron frequency is every minute, and the default adhoc concurrency is 3.
     * A default value of 3 minutes allows all adhoc tasks to be run concurrently at their default value.
     *
     * @var int The default keepalive value for the main cron runner
     */
    public const DEFAULT_MAIN_PROCESS_KEEPALIVE = 3 * MINSECS;

    /**
     * @var int The max keepalive value for the main cron runner
     */
    public const MAX_MAIN_PROCESS_KEEPALIVE = 15 * MINSECS;

    /**
     * Execute cron tasks
     *
     * @param int|null $keepalive The keepalive time for this cron run.
     */
    public static function run_main_process(?int $keepalive = null): void {
        global $CFG, $DB;

        if (CLI_MAINTENANCE) {
            echo "CLI maintenance mode active, cron execution suspended.\n";
            exit(1);
        }

        if (moodle_needs_upgrading()) {
            echo "Moodle upgrade pending, cron execution suspended.\n";
            exit(1);
        }

        require_once($CFG->libdir . '/adminlib.php');

        if (!empty($CFG->showcronsql)) {
            $DB->set_debug(true);
        }
        if (!empty($CFG->showcrondebugging)) {
            set_debugging(DEBUG_DEVELOPER, true);
        }

        core_php_time_limit::raise();

        // Increase memory limit.
        raise_memory_limit(MEMORY_EXTRA);

        // Emulate normal session. - we use admin account by default.
        self::setup_user();

        // Start output log.
        $timenow = time();
        mtrace("Server Time: " . date('r', $timenow) . "\n\n");

        // Record start time and interval between the last cron runs.
        $laststart = get_config('tool_task', 'lastcronstart');
        set_config('lastcronstart', $timenow, 'tool_task');
        if ($laststart) {
            // Record the interval between last two runs (always store at least 1 second).
            set_config('lastcroninterval', max(1, $timenow - $laststart), 'tool_task');
        }

        // Determine the time when the cron should finish.
        if ($keepalive === null) {
            $keepalive = get_config('core', 'cron_keepalive');
            if ($keepalive === false) {
                $keepalive = self::DEFAULT_MAIN_PROCESS_KEEPALIVE;
            }
        }

        if ($keepalive > self::MAX_MAIN_PROCESS_KEEPALIVE) {
            // Attempt to prevent abnormally long keepalives.
            mtrace("Cron keepalive time is too long, reducing to 15 minutes.");
            $keepalive = self::MAX_MAIN_PROCESS_KEEPALIVE;
        }

        // Calculate the finish time based on the start time and keepalive.
        $finishtime = $timenow + $keepalive;

        do {
            $startruntime = microtime();

            // Run all scheduled tasks.
            self::run_scheduled_tasks(time(), $timenow);

            // Run adhoc tasks.
            self::run_adhoc_tasks(time(), 0, true, $timenow);

            mtrace("Cron run completed correctly");

            gc_collect_cycles();

            $completiontime = date('H:i:s');
            $difftime = microtime_diff($startruntime, microtime());
            $memoryused = display_size(memory_get_usage());

            $message = "Cron completed at {$completiontime} in {$difftime} seconds. Memory used: {$memoryused}.";

            // Check if we should continue to run.
            // Only continue to run if:
            // - The finish time has not been reached; and
            // - The graceful exit flag has not been set; and
            // - The static caches have not been cleared since the start of the cron run.
            $remaining = $finishtime - time();
            $runagain = $remaining > 0;
            $runagain = $runagain && !\core\local\cli\shutdown::should_gracefully_exit();
            $runagain = $runagain && !\core\task\manager::static_caches_cleared_since($timenow);

            if ($runagain) {
                $message .= " Continuing to check for tasks for {$remaining} more seconds.";
                mtrace($message);
                sleep(1);

                // Re-check the graceful exit and cache clear flags after sleeping as these may have changed.
                $runagain = $runagain && !\core\local\cli\shutdown::should_gracefully_exit();
                $runagain = $runagain && !\core\task\manager::static_caches_cleared_since($timenow);
            } else {
                mtrace($message);
            }
        } while ($runagain);
    }

    /**
     * Execute all queued scheduled tasks, applying necessary concurrency limits and time limits.
     *
     * @param   int       $startruntime The time this run started.
     * @param   null|int  $startprocesstime The time the process that owns this runner started.
     * @throws \moodle_exception
     */
    public static function run_scheduled_tasks(
        int $startruntime,
        ?int $startprocesstime = null,
    ): void {
        // Allow a restriction on the number of scheduled task runners at once.
        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
        $maxruns = get_config('core', 'task_scheduled_concurrency_limit');
        $maxruntime = get_config('core', 'task_scheduled_max_runtime');

        if ($startprocesstime === null) {
            $startprocesstime = $startruntime;
        }

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
            while (
                !\core\local\cli\shutdown::should_gracefully_exit() &&
                !\core\task\manager::static_caches_cleared_since($startprocesstime) &&
                $task = \core\task\manager::get_next_scheduled_task($startruntime)
            ) {
                self::run_inner_scheduled_task($task);
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
     * @param   int     $startruntime The time this run started.
     * @param   int     $keepalive Keep this public static function alive for N seconds and poll for new adhoc tasks.
     * @param   bool    $checklimits Should we check limits?
     * @param   null|int $startprocesstime The time this process started.
     * @param   int|null $maxtasks Limit number of tasks to run`
     * @param   null|string $classname Run only tasks of this class
     * @throws \moodle_exception
     */
    public static function run_adhoc_tasks(
        int $startruntime,
        $keepalive = 0,
        $checklimits = true,
        ?int $startprocesstime = null,
        ?int $maxtasks = null,
        ?string $classname = null,
    ): void {
        // Allow a restriction on the number of adhoc task runners at once.
        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
        $maxruns = get_config('core', 'task_adhoc_concurrency_limit');
        $maxruntime = get_config('core', 'task_adhoc_max_runtime');

        if ($startprocesstime === null) {
            $startprocesstime = $startruntime;
        }

        $adhoclock = null;
        if ($checklimits) {
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

        $humantimenow = date('r', $startruntime);
        $finishtime = $startruntime + $keepalive;
        $waiting = false;
        $taskcount = 0;

        // Run all adhoc tasks.
        while (
            !\core\local\cli\shutdown::should_gracefully_exit() &&
            !\core\task\manager::static_caches_cleared_since($startprocesstime)
        ) {

            if ($checklimits && (time() - $startruntime) >= $maxruntime) {
                if ($waiting) {
                    $waiting = false;
                    mtrace('');
                }
                mtrace("Stopping processing of adhoc tasks as time limit has been reached.");
                break;
            }

            try {
                $task = \core\task\manager::get_next_adhoc_task(time(), $checklimits, $classname);
            } catch (\Throwable $e) {
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
                self::run_inner_adhoc_task($task);
                self::set_process_title("Waiting for next adhoc task");
                $taskcount++;
                if ($maxtasks && $taskcount >= $maxtasks) {
                    break;
                }
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
                self::set_process_title("Waiting {$timeleft}s for next adhoc task");
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
     * Execute an adhoc task.
     *
     * @param   int       $taskid
     */
    public static function run_adhoc_task(int $taskid): void {
        $task = \core\task\manager::get_adhoc_task($taskid);
        if (!$task->get_fail_delay() && $task->get_next_run_time() > time()) {
            throw new \moodle_exception('wontrunfuturescheduledtask');
        }

        self::run_inner_adhoc_task($task);
        self::set_process_title("Running adhoc task $taskid");
    }

    /**
     * Execute all failed adhoc tasks.
     *
     * @param string|null  $classname Run only tasks of this class
     */
    public static function run_failed_adhoc_tasks(?string $classname = null): void {
        global $DB;

        $where = 'faildelay > 0';
        $params = [];
        if ($classname) {
            $where .= ' AND classname = :classname';
            $params['classname'] = \core\task\manager::get_canonical_class_name($classname);
        }

        // Only rerun the failed tasks that allow to be re-tried or have the remaining attempts available.
        $where .= ' AND (attemptsavailable > 0 OR attemptsavailable IS NULL)';
        $tasks = $DB->get_records_sql("SELECT * from {task_adhoc} WHERE $where", $params);
        foreach ($tasks as $t) {
            self::run_adhoc_task($t->id);
        }
    }

    /**
     * Shared code that handles running of a single scheduled task within the cron.
     *
     * Not intended for calling directly outside of this library!
     *
     * @param \core\task\task_base $task
     */
    public static function run_inner_scheduled_task(\core\task\task_base $task) {
        global $CFG, $DB;
        $debuglevel = $CFG->debug;
        $debugdisplay = $CFG->debugdisplay;
        $CFG->debugdisplay = 1;

        \core\task\manager::scheduled_task_starting($task);
        \core\task\logmanager::start_logging($task);

        $fullname = $task->get_name() . ' (' . get_class($task) . ')';
        mtrace('Execute scheduled task: ' . $fullname);
        self::set_process_title('Scheduled task: ' . get_class($task));
        self::trace_time_and_memory();
        memory_reset_peak_usage();
        $predbqueries = null;
        $predbqueries = $DB->perf_get_queries();
        $pretime = microtime(1);

        // Ensure that we have a clean session with the correct cron user.
        self::setup_user();

        try {
            get_mailer('buffer');
            self::prepare_core_renderer();
            // Temporarily increase debug level if task has failed and debugging isn't already at maximum.
            if ($debuglevel !== DEBUG_DEVELOPER && $faildelay = $task->get_fail_delay()) {
                mtrace('Debugging increased temporarily due to faildelay of ' . $faildelay);
                set_debugging(DEBUG_DEVELOPER, 1);
            }
            $task->execute();
            if ($DB->is_transaction_started()) {
                throw new coding_exception("Task left transaction open");
            }
            if (isset($predbqueries)) {
                mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
                mtrace("... used " . (microtime(1) - $pretime) . " seconds");
            }
            mtrace('... used ' . display_size(memory_get_peak_usage()) . ' peak memory');
            mtrace('Scheduled task complete: ' . $fullname);
            \core\task\manager::scheduled_task_complete($task);
        } catch (\Throwable $e) {
            if ($DB && $DB->is_transaction_started()) {
                error_log('Database transaction aborted automatically in ' . get_class($task));
                $DB->force_transaction_rollback();
            }
            if (isset($predbqueries)) {
                mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
                mtrace("... used " . (microtime(1) - $pretime) . " seconds");
            }
            mtrace('... used ' . display_size(memory_get_peak_usage()) . ' peak memory');
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
            // Reset debugging if it changed.
            if ($CFG->debug !== $debuglevel) {
                set_debugging($debuglevel);
            }

            // Reset debugdisplay back.
            $CFG->debugdisplay = $debugdisplay;

            // Reset back to the standard admin user.
            self::setup_user();
            self::set_process_title('Waiting for next scheduled task');
            self::prepare_core_renderer(true);
        }
        get_mailer('close');
    }

    /**
     * Shared code that handles running of a single adhoc task within the cron.
     *
     * @param \core\task\adhoc_task $task
     */
    public static function run_inner_adhoc_task(\core\task\adhoc_task $task) {
        global $CFG, $DB;
        $debuglevel = $CFG->debug;
        $debugdisplay = $CFG->debugdisplay;
        $CFG->debugdisplay = 1;

        \core\task\manager::adhoc_task_starting($task);
        \core\task\logmanager::start_logging($task);

        mtrace("Execute adhoc task: " . get_class($task));
        mtrace("Adhoc task id: " . $task->get_id());
        mtrace("Adhoc task custom data: " . $task->get_custom_data_as_string());
        self::set_process_title('Adhoc task: ' . $task->get_id() . ' ' . get_class($task));
        self::trace_time_and_memory();
        memory_reset_peak_usage();
        $predbqueries = null;
        $predbqueries = $DB->perf_get_queries();
        $pretime = microtime(1);

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
                // A user missing in the database will never reappear so the task needs to be failed to ensure that locks are
                // removed, and then removed to prevent future runs.
                // A task running as a user should only be run as that user.
                \core\task\manager::adhoc_task_failed($task);
                $DB->delete_records('task_adhoc', ['id' => $task->get_id()]);

                return;
            }

            self::setup_user($user);
        } else {
            // No user specified, ensure that we have a clean session with the correct cron user.
            self::setup_user();
        }

        try {
            get_mailer('buffer');
            self::prepare_core_renderer();
            // Temporarily increase debug level if task has failed and debugging isn't already at maximum.
            if ($debuglevel !== DEBUG_DEVELOPER && $faildelay = $task->get_fail_delay()) {
                mtrace('Debugging increased temporarily due to faildelay of ' . $faildelay);
                set_debugging(DEBUG_DEVELOPER, 1);
            }
            $task->execute();
            if ($DB->is_transaction_started()) {
                throw new coding_exception("Task left transaction open");
            }
            if (isset($predbqueries)) {
                mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
                mtrace("... used " . (microtime(1) - $pretime) . " seconds");
            }
            mtrace('... used ' . display_size(memory_get_peak_usage()) . ' peak memory');
            mtrace("Adhoc task complete: " . get_class($task));
            \core\task\manager::adhoc_task_complete($task);
        } catch (\Throwable $e) {
            if ($DB && $DB->is_transaction_started()) {
                error_log('Database transaction aborted automatically in ' . get_class($task));
                $DB->force_transaction_rollback();
            }
            if (isset($predbqueries)) {
                mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
                mtrace("... used " . (microtime(1) - $pretime) . " seconds");
            }
            mtrace('... used ' . display_size(memory_get_peak_usage()) . ' peak memory');
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
            // Reset debug level if it changed.
            if ($CFG->debug !== $debuglevel) {
                set_debugging($debuglevel);
            }

            // Reset debugdisplay back.
            $CFG->debugdisplay = $debugdisplay;

            // Reset back to the standard admin user.
            self::setup_user();
            self::prepare_core_renderer(true);
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
    public static function set_process_title(string $title) {
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
    public static function trace_time_and_memory() {
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
    public static function prepare_core_renderer($restore = false) {
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

    /**
     * Sets up a user and course environment in cron.
     *
     * Note: This function is intended only for use in:
     * - the cron runner scripts
     * - individual tasks which extend the adhoc_task and scheduled_task classes
     * - unit tests related to tasks
     * - other parts of the cron/task system
     *
     * Please note that this function stores cache data statically.
     * @see reset_user_cache() to reset this cache.
     *
     * @param null|stdClass $user full user object, null means default cron user (admin)
     * @param null|stdClass $course full course record, null means $SITE
     * @param null|bool $leavepagealone If specified, stops it messing with global page object
     */
    public static function setup_user(?stdClass $user = null, ?stdClass $course = null, bool $leavepagealone = false): void {
        // This function uses the $GLOBALS super global. Disable the VariableNameLowerCase sniff for this function.
        // phpcs:disable moodle.NamingConventions.ValidVariableName.VariableNameLowerCase
        global $CFG, $SITE, $PAGE;

        if (!CLI_SCRIPT && !$leavepagealone) {
            throw new coding_exception('It is not possible to use \core\cron\setup_user() in normal requests!');
        }

        if (empty(self::$cronuser)) {
            // The cron user is essentially the admin user, but with some value removed.
            // We ginore the timezone language, and locale preferences - use the site default instead.
            self::$cronuser = get_admin();
            self::$cronuser->timezone = $CFG->timezone;
            self::$cronuser->lang = '';
            self::$cronuser->theme = '';
            unset(self::$cronuser->description);

            self::$cronsession = new stdClass();
        }

        if (!$user) {
            // Cached default cron user (==modified admin for now).
            \core\session\manager::init_empty_session();
            \core\session\manager::set_user(self::$cronuser);
            $GLOBALS['SESSION'] = self::$cronsession;
        } else {
            // Emulate real user session - needed for caps in cron.
            if ($GLOBALS['USER']->id != $user->id) {
                \core\session\manager::init_empty_session();
                \core\session\manager::set_user($user);
            }
        }

        // TODO MDL-19774 relying on global $PAGE in cron is a bad idea.
        // Temporary hack so that cron does not give fatal errors.
        if (!$leavepagealone) {
            $PAGE = new \moodle_page();
            $PAGE->set_course($course ?? $SITE);
        }

        // TODO: it should be possible to improve perf by caching some limited number of users here.
        // phpcs:enable
    }

    /**
     * Resets the cache for the cron user used by `setup_user()`.
     */
    public static function reset_user_cache(): void {
        self::$cronuser = null;
        self::$cronsession = null;
        \core\session\manager::init_empty_session();
    }
}
