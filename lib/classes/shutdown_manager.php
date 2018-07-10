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
    protected static $callbacks = array();
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
    }

    /**
     * Register custom shutdown function.
     *
     * @param callable $callback
     * @param array $params
     */
    public static function register_function($callback, array $params = null) {
        self::$callbacks[] = array($callback, $params);
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
                if (!is_callable($callback)) {
                    error_log('Invalid custom shutdown function detected '.var_export($callback, true));
                    continue;
                }
                if ($params === null) {
                    call_user_func($callback);
                } else {
                    call_user_func_array($callback, $params);
                }
            } catch (Exception $e) {
                error_log('Exception ignored in shutdown function '.get_callable_name($callback).': '.$e->getMessage());
            } catch (Throwable $e) {
                // Engine errors in PHP7 throw exceptions of type Throwable (this "catch" will be ignored in PHP5).
                error_log('Exception ignored in shutdown function '.get_callable_name($callback).': '.$e->getMessage());
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
