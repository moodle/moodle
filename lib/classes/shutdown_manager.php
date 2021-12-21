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
 * Shutdown management class.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Shutdown management class.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_shutdown_manager {
    /** @var array list of custom callbacks */
    protected static $callbacks = [];
    /** @var array list of custom signal callbacks */
    protected static $signalcallbacks = [];
    /** @var bool is this manager already registered? */
    protected static $registered = false;

    /**
     * Register self as main shutdown handler.
     *
     * @private to be called from lib/setup.php only!
     */
    public static function initialize() {
        if (self::$registered) {
            debugging('Shutdown manager is already initialised!');
        }
        self::$registered = true;
        register_shutdown_function(array('core_shutdown_manager', 'shutdown_handler'));

        // Signal handlers should only be used when dealing with a CLI script.
        // In the case of PHP called in a web server the server is the owning process and should handle the signal chain
        // properly itself.
        // The 'pcntl' extension is optional and not available on Windows.
        if (CLI_SCRIPT && extension_loaded('pcntl') && function_exists('pcntl_async_signals')) {
            // We capture and handle SIGINT (Ctrl+C) and SIGTERM (termination requested).
            pcntl_async_signals(true);
            pcntl_signal(SIGINT, ['core_shutdown_manager', 'signal_handler']);
            pcntl_signal(SIGTERM, ['core_shutdown_manager', 'signal_handler']);
        }
    }

    /**
     * Signal handler for SIGINT, and SIGTERM.
     *
     * @param   int     $signo The signal being handled
     */
    public static function signal_handler(int $signo) {
        // Note: There is no need to manually call the shutdown handler.
        // The fact that we are calling exit() in this script means that the standard shutdown handling is performed
        // anyway.
        switch ($signo) {
            case SIGTERM:
                // Replicate native behaviour.
                echo "Terminated: {$signo}\n";

                // The standard exit code for SIGTERM is 143.
                $exitcode = 143;
                break;
            case SIGINT:
                // Replicate native behaviour.
                echo "\n";

                // The standard exit code for SIGINT (Ctrl+C) is 130.
                $exitcode = 130;
                break;
            default:
                // The signal handler was called with a signal it was not expecting.
                // We should exit and complain.
                echo "Warning: \core_shutdown_manager::signal_handler() was called with an unexpected signal ({$signo}).\n";
                $exitcode = 1;
        }

        // Normally we should exit unless a callback tells us to wait.
        $shouldexit = true;
        foreach (self::$signalcallbacks as $data) {
            list($callback, $params) = $data;
            try {
                array_unshift($params, $signo);
                $shouldexit = call_user_func_array($callback, $params) && $shouldexit;
            } catch (Throwable $e) {
                // @codingStandardsIgnoreStart
                error_log('Exception ignored in signal function ' . get_callable_name($callback) . ': ' . $e->getMessage());
                // @codingStandardsIgnoreEnd
            }
        }

        if ($shouldexit) {
            exit ($exitcode);
        }
    }

    /**
     * Register custom signal handler function.
     *
     * If a handler returns false the signal will be ignored.
     *
     * @param callable $callback
     * @param array $params
     * @return void
     */
    public static function register_signal_handler($callback, array $params = null): void {
        if (!is_callable($callback)) {
            // @codingStandardsIgnoreStart
            error_log('Invalid custom signal function detected ' . var_export($callback, true));
            // @codingStandardsIgnoreEnd
        }
        self::$signalcallbacks[] = [$callback, $params ?? []];
    }

    /**
     * Register custom shutdown function.
     *
     * @param callable $callback
     * @param array $params
     * @return void
     */
    public static function register_function($callback, array $params = null): void {
        if (!is_callable($callback)) {
            // @codingStandardsIgnoreStart
            error_log('Invalid custom shutdown function detected '.var_export($callback, true));
            // @codingStandardsIgnoreEnd
        }
        self::$callbacks[] = [$callback, $params ? array_values($params) : []];
    }

    /**
     * @private - do NOT call directly.
     */
    public static function shutdown_handler() {
        global $DB;

        // Custom stuff first.
        foreach (self::$callbacks as $data) {
            list($callback, $params) = $data;
            try {
                call_user_func_array($callback, $params);
            } catch (Throwable $e) {
                // @codingStandardsIgnoreStart
                error_log('Exception ignored in shutdown function '.get_callable_name($callback).': '.$e->getMessage());
                // @codingStandardsIgnoreEnd
            }
        }

        // Handle DB transactions, session need to be written afterwards
        // in order to maintain consistency in all session handlers.
        if ($DB->is_transaction_started()) {
            if (!defined('PHPUNIT_TEST') or !PHPUNIT_TEST) {
                // This should not happen, it usually indicates wrong catching of exceptions,
                // because all transactions should be finished manually or in default exception handler.
                $backtrace = $DB->get_transaction_start_backtrace();
                error_log('Potential coding error - active database transaction detected during request shutdown:'."\n".format_backtrace($backtrace, true));
            }
            $DB->force_transaction_rollback();
        }

        // Close sessions - do it here to make it consistent for all session handlers.
        \core\session\manager::write_close();

        // Other cleanup.
        self::request_shutdown();

        // Stop profiling.
        if (function_exists('profiling_is_running')) {
            if (profiling_is_running()) {
                profiling_stop();
            }
        }

        // NOTE: do not dispose $DB and MUC here, they might be used from legacy shutdown functions.
    }

    /**
     * Standard shutdown sequence.
     */
    protected static function request_shutdown() {
        global $CFG;

        // Help apache server if possible.
        $apachereleasemem = false;
        if (function_exists('apache_child_terminate') && function_exists('memory_get_usage') && ini_get_bool('child_terminate')) {
            $limit = (empty($CFG->apachemaxmem) ? 64*1024*1024 : $CFG->apachemaxmem); // 64MB default.
            if (memory_get_usage() > get_real_size($limit)) {
                $apachereleasemem = $limit;
                @apache_child_terminate();
            }
        }

        // Deal with perf logging.
        if (defined('MDL_PERF') || (!empty($CFG->perfdebug) and $CFG->perfdebug > 7)) {
            if ($apachereleasemem) {
                error_log('Mem usage over '.$apachereleasemem.': marking Apache child for reaping.');
            }
            if (defined('MDL_PERFTOLOG')) {
                $perf = get_performance_info();
                error_log("PERF: " . $perf['txt']);
            }
            if (defined('MDL_PERFINC')) {
                $inc = get_included_files();
                $ts  = 0;
                foreach ($inc as $f) {
                    if (preg_match(':^/:', $f)) {
                        $fs = filesize($f);
                        $ts += $fs;
                        $hfs = display_size($fs);
                        error_log(substr($f, strlen($CFG->dirroot)) . " size: $fs ($hfs)", null, null, 0);
                    } else {
                        error_log($f , null, null, 0);
                    }
                }
                if ($ts > 0 ) {
                    $hts = display_size($ts);
                    error_log("Total size of files included: $ts ($hts)");
                }
            }
        }
    }
}
