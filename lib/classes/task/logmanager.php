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
 * Task log manager.
 *
 * @package    core
 * @category   task
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Task log manager.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class logmanager {

    /** @var int Do not log anything */
    const MODE_NONE = 0;

    /** @var int Log all tasks */
    const MODE_ALL = 1;

    /** @var int Only log fails */
    const MODE_FAILONLY = 2;

    /** @var int The default chunksize to use in ob_start */
    const CHUNKSIZE = 1;

    /**
     * @var \core\task\task_base The task being logged.
     */
    protected static $task = null;

    /**
     * @var \stdClass Metadata about the current log
     */
    protected static $taskloginfo = null;

    /**
     * @var \resource The current filehandle used for logging
     */
    protected static $fh = null;

    /**
     * @var string The path to the log file
     */
    protected static $logpath = null;

    /**
     * @var bool Whether the task logger has been registered with the shutdown handler
     */
    protected static $tasklogregistered = false;

    /**
     * @var int The level of output buffering in place before starting.
     */
    protected static $oblevel = null;

    /**
     * @var bool Output logged content to screen.
     */
    protected static $outputloggedcontent = true;

    /**
     * Create a new task logger for the specified task, and prepare for logging.
     *
     * @param   \core\task\task_base    $task The task being run
     */
    public static function start_logging(task_base $task) {
        global $CFG, $DB;

        if (!self::should_log()) {
            return;
        }

        // We register a shutdown handler to ensure that logs causing any failures are correctly disposed of.
        // Note: This must happen before the per-request directory is requested because the shutdown handler deletes the logfile.
        if (!self::$tasklogregistered) {
            \core_shutdown_manager::register_function(function() {
                // These will only actually do anything if capturing is current active when the thread ended, which
                // constitutes a failure.
                \core\task\logmanager::finalise_log(true);
            });

            // Create a brand new per-request directory basedir.
            get_request_storage_directory(true, true);

            self::$tasklogregistered = true;
        }

        if (self::is_current_output_buffer()) {
            // We cannot capture when we are already capturing.
            throw new \coding_exception('Logging is already in progress for task "' . get_class(self::$task) . '". ' .
                'Nested logging is not supported.');
        }

        // Store the initial data about the task and current state.
        self::$task = $task;
        self::$taskloginfo = (object) [
            'dbread'    => $DB->perf_get_reads(),
            'dbwrite'   => $DB->perf_get_writes(),
            'timestart' => microtime(true),
        ];

        // For simplicity's sake we always store logs on disk and flush at the end.
        self::$logpath = make_request_directory() . DIRECTORY_SEPARATOR . "task.log";
        self::$fh = fopen(self::$logpath, 'w+');

        // Note the level of the current output buffer.
        // Note: You cannot use ob_get_level() as it will return `1` when the default output buffer is enabled.
        if ($obstatus = ob_get_status()) {
            self::$oblevel = $obstatus['level'];
        } else {
            self::$oblevel = null;
        }

        self::$outputloggedcontent = !empty($CFG->task_logtostdout);

        // Start capturing output.
        ob_start([\core\task\logmanager::class, 'add_line'], self::CHUNKSIZE);
    }

    /**
     * Whether logging is possible and should be happening.
     *
     * @return  bool
     */
    protected static function should_log() : bool {
        global $CFG;

        // Respect the config setting.
        if (isset($CFG->task_logmode) && empty($CFG->task_logmode)) {
            return false;
        }

        $loggerclass = self::get_logger_classname();
        if (empty($loggerclass)) {
            return false;
        }

        return $loggerclass::is_configured();
    }

    /**
     * Return the name of the logging class to use.
     *
     * @return  string
     */
    public static function get_logger_classname() : string {
        global $CFG;

        if (!empty($CFG->task_log_class)) {
            // Configuration is present to use an alternative task logging class.
            return $CFG->task_log_class;
        }

        // Fall back on the default database logger.
        return database_logger::class;
    }

    /**
     * Whether this task logger has a report available.
     *
     * @return  bool
     */
    public static function has_log_report() : bool {
        $loggerclass = self::get_logger_classname();

        return $loggerclass::has_log_report();
    }

    /**
     * Whether to use the standard settings form.
     */
    public static function uses_standard_settings() : bool {
        $classname = self::get_logger_classname();
        if (!class_exists($classname)) {
            return false;
        }

        if (is_a($classname, database_logger::class, true)) {
            return true;
        }

        return false;
    }

    /**
     * Get any URL available for viewing relevant task log reports.
     *
     * @param   string      $classname The task class to fetch for
     * @return  \moodle_url
     */
    public static function get_url_for_task_class(string $classname) : \moodle_url {
        $loggerclass = self::get_logger_classname();

        return $loggerclass::get_url_for_task_class($classname);
    }

    /**
     * Whether we are the current log collector.
     *
     * @return  bool
     */
    protected static function is_current_output_buffer() : bool {
        if (empty(self::$taskloginfo)) {
            return false;
        }

        if ($ob = ob_get_status()) {
            return 'core\\task\\logmanager::add_line' == $ob['name'];
        }

        return false;
    }

    /**
     * Whether we are capturing at all.
     *
     * @return  bool
     */
    protected static function is_capturing() : bool {
        $buffers = ob_get_status(true);
        foreach ($buffers as $ob) {
            if ('core\\task\\logmanager::add_line' == $ob['name']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Finish writing for the current task.
     *
     * @param   bool    $failed
     */
    public static function finalise_log(bool $failed = false) {
        global $CFG, $DB, $PERF;

        if (!self::should_log()) {
            return;
        }

        if (!self::is_capturing()) {
            // Not capturing anything.
            return;
        }

        // Ensure that all logs are closed.
        $buffers = ob_get_status(true);
        foreach (array_reverse($buffers) as $ob) {
            if (null !== self::$oblevel) {
                if ($ob['level'] <= self::$oblevel) {
                    // Only close as far as the initial output buffer level.
                    break;
                }
            }

            // End and flush this buffer.
            ob_end_flush();

            if ('core\\task\\logmanager::add_line' == $ob['name']) {
                break;
            }
        }
        self::$oblevel = null;

        // Flush any remaining buffer.
        self::flush();

        // Close and unset the FH.
        fclose(self::$fh);
        self::$fh = null;

        if ($failed || empty($CFG->task_logmode) || self::MODE_ALL == $CFG->task_logmode) {
            // Finalise the log.
            $loggerclass = self::get_logger_classname();
            $loggerclass::store_log_for_task(
                self::$task,
                self::$logpath,
                $failed,
                $DB->perf_get_reads() - self::$taskloginfo->dbread,
                $DB->perf_get_writes() - self::$taskloginfo->dbwrite,
                self::$taskloginfo->timestart,
                microtime(true)
            );
        }

        // Tidy up.
        self::$logpath = null;
        self::$taskloginfo = null;
    }

    /**
     * Flush the current output buffer.
     *
     * This function will ensure that we are the current output buffer handler.
     */
    public static function flush() {
        // We only call ob_flush if the current output buffer belongs to us.
        if (self::is_current_output_buffer()) {
            ob_flush();
        }
    }

    /**
     * Add a log record to the task log.
     *
     * @param   string  $log
     * @return  string
     */
    public static function add_line(string $log) : string {
        if (empty(self::$taskloginfo)) {
            return $log;
        }

        if (empty(self::$fh)) {
            return $log;
        }

        if (self::is_current_output_buffer()) {
            fwrite(self::$fh, $log);
        }

        if (self::$outputloggedcontent) {
            return $log;
        } else {
            return '';
        }
    }
}
