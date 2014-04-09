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
 * CLI task execution.
 *
 * @package    tool_task
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once("$CFG->libdir/clilib.php");
require_once("$CFG->libdir/cronlib.php");

list($options, $unrecognized) = cli_get_params(
    array('help' => false, 'list' => false, 'execute' => false),
    array('h' => 'help')
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help'] or (!$options['list'] and !$options['execute'])) {
    $help =
"Scheduled cron tasks.

Options:
--execute=\\\\some\\\\task  Execute scheduled task manually
--list                List all scheduled tasks
-h, --help            Print out this help

Example:
\$sudo -u www-data /usr/bin/php admin/tool/task/cli/scheduled_task.php --execute=\\\\core\\\\task\\\\session_cleanup_task

";

    echo $help;
    die;
}

if ($options['list']) {
    cli_heading("List of scheduled tasks ($CFG->wwwroot)");

    $shorttime = get_string('strftimedatetimeshort');

    $tasks = \core\task\manager::get_all_scheduled_tasks();
    foreach ($tasks as $task) {
        $class = '\\' . get_class($task);
        $schedule = $task->get_minute() . ' '
            . $task->get_hour() . ' '
            . $task->get_day() . ' '
            . $task->get_day_of_week() . ' '
            . $task->get_month() . ' '
            . $task->get_day_of_week();
        $nextrun = $task->get_next_run_time();

        if ($task->get_disabled()) {
            $nextrun = get_string('disabled', 'tool_task');
        } else if ($nextrun > time()) {
            $nextrun = userdate($nextrun);
        } else {
            $nextrun = get_string('asap', 'tool_task');
        }

        echo str_pad($class, 50, ' ') . ' ' . str_pad($schedule, 17, ' ') . ' ' . $nextrun . "\n";
    }
    exit(0);
}

if ($execute = $options['execute']) {
    if (!$task = \core\task\manager::get_scheduled_task($execute)) {
        mtrace("Task '$execute' not found");
        exit(1);
    }

    if (moodle_needs_upgrading()) {
        mtrace("Moodle upgrade pending, cannot execute tasks.");
        exit(1);
    }

    // Increase memory limit.
    raise_memory_limit(MEMORY_EXTRA);

    // Emulate normal session - we use admin account by default.
    cron_setup_user();

    $predbqueries = $DB->perf_get_queries();
    $pretime = microtime(true);
    try {
        mtrace("Scheduled task: " . $task->get_name());
        // NOTE: it would be tricky to move this code to \core\task\manager class,
        //       because we want to do detailed error reporting.
        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
        if (!$cronlock = $cronlockfactory->get_lock('core_cron', 10)) {
            mtrace('Cannot obtain cron lock');
            exit(129);
        }
        if (!$lock = $cronlockfactory->get_lock('\\' . get_class($task), 10)) {
            mtrace('Cannot obtain task lock');
            exit(130);
        }
        $task->set_lock($lock);
        if (!$task->is_blocking()) {
            $cronlock->release();
        } else {
            $task->set_cron_lock($cronlock);
        }
        $task->execute();
        if (isset($predbqueries)) {
            mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
            mtrace("... used " . (microtime(1) - $pretime) . " seconds");
        }
        mtrace("Task completed.");
        \core\task\manager::scheduled_task_complete($task);
        exit(0);
    } catch (Exception $e) {
        if ($DB->is_transaction_started()) {
            $DB->force_transaction_rollback();
        }
        mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
        mtrace("... used " . (microtime(true) - $pretime) . " seconds");
        mtrace("Task failed: " . $e->getMessage());
        \core\task\manager::scheduled_task_failed($task);
        exit(1);
    }
}
