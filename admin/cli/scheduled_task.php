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
 * @package    core
 * @subpackage cli
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once("$CFG->libdir/clilib.php");

list($options, $unrecognized) = cli_get_params(
    [
        'help' => false,
        'list' => false,
        'execute' => false,
        'showsql' => false,
        'showdebugging' => false,
        'force' => false,
    ], [
        'h' => 'help',
        'f' => 'force',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help'] or (!$options['list'] and !$options['execute'])) {
    $help =
    "Scheduled cron tasks.

    Options:
    --execute=\\some\\task  Execute scheduled task manually
    --list                List all scheduled tasks
    --showsql             Show sql queries before they are executed
    --showdebugging       Show developer level debugging information
    -h, --help            Print out this help
    -f, --force           Execute task even if cron is disabled

    Example:
    \$sudo -u www-data /usr/bin/php admin/cli/scheduled_task.php --execute=\\core\\task\\session_cleanup_task

    ";

    echo $help;
    die;
}

if ($options['showdebugging'] || !empty($CFG->showcrondebugging)) {
    set_debugging(DEBUG_DEVELOPER, true);
}

if ($options['showsql'] || !empty($CFG->showcronsql)) {
    $DB->set_debug(true);
}
if ($options['list']) {
    cli_heading("List of scheduled tasks ($CFG->wwwroot)");

    $shorttime = get_string('strftimedatetimeshort');

    $tasks = \core\task\manager::get_all_scheduled_tasks();
    echo str_pad(get_string('scheduledtasks', 'tool_task'), 50, ' ') . ' ' . str_pad(get_string('runpattern', 'tool_task'), 17, ' ')
        . ' ' . str_pad(get_string('lastruntime', 'tool_task'), 40, ' ') . get_string('nextruntime', 'tool_task') . "\n";
    foreach ($tasks as $task) {
        $class = '\\' . get_class($task);
        $schedule = $task->get_minute() . ' '
            . $task->get_hour() . ' '
            . $task->get_day() . ' '
            . $task->get_day_of_week() . ' '
            . $task->get_month() . ' '
            . $task->get_day_of_week();
        $nextrun = $task->get_next_run_time();
        $lastrun = $task->get_last_run_time();

        $plugininfo = core_plugin_manager::instance()->get_plugin_info($task->get_component());
        $plugindisabled = $plugininfo && $plugininfo->is_enabled() === false && !$task->get_run_if_component_disabled();

        if ($plugindisabled) {
            $nextrun = get_string('plugindisabled', 'tool_task');
        } else if ($task->get_disabled()) {
            $nextrun = get_string('taskdisabled', 'tool_task');
        } else if ($nextrun > time()) {
            $nextrun = userdate($nextrun);
        } else {
            $nextrun = get_string('asap', 'tool_task');
        }

        if ($lastrun) {
            $lastrun = userdate($lastrun);
        } else {
            $lastrun = get_string('never');
        }

        echo str_pad($class, 50, ' ') . ' ' . str_pad($schedule, 17, ' ') .
            ' ' . str_pad($lastrun, 40, ' ') . ' ' . $nextrun . "\n";
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

    if (!get_config('core', 'cron_enabled') && !$options['force']) {
        mtrace('Cron is disabled. Use --force to override.');
        exit(1);
    }

    \core\task\manager::scheduled_task_starting($task);

    // Increase memory limit.
    raise_memory_limit(MEMORY_EXTRA);

    // Emulate normal session - we use admin account by default.
    \core\cron::setup_user();

    // Execute the task.
    \core\local\cli\shutdown::script_supports_graceful_exit();
    $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
    if (!$cronlock = $cronlockfactory->get_lock('core_cron', 10)) {
        mtrace('Cannot obtain cron lock');
        exit(129);
    }
    if (!$lock = $cronlockfactory->get_lock('\\' . get_class($task), 10)) {
        $cronlock->release();
        mtrace('Cannot obtain task lock');
        exit(130);
    }

    $task->set_lock($lock);
    if (!$task->is_blocking()) {
        $cronlock->release();
    } else {
        $task->set_cron_lock($cronlock);
    }

    \core\cron::run_inner_scheduled_task($task);
}
