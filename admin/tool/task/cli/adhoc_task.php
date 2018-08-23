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
 * Task executor for adhoc tasks.
 *
 * @package    tool_task
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once("{$CFG->libdir}/clilib.php");
require_once("{$CFG->libdir}/cronlib.php");

list($options, $unrecognized) = cli_get_params(
    [
        'execute' => false,
        'help' => false,
        'showsql' => false,
        'showdebugging' => false,
    ], [
        'h' => 'help',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help'] or empty($options['execute'])) {
    $help =
"Scheduled cron tasks.

Options:
--showsql             Show sql queries before they are executed
--showdebugging       Show developer level debugging information
--execute             Run all queued adhoc tasks
-h, --help            Print out this help

Example:
\$sudo -u www-data /usr/bin/php admin/tool/task/cli/adhoc_task.php --execute

";

    echo $help;
    die;
}

if ($options['showdebugging']) {
    set_debugging(DEBUG_DEVELOPER, true);
}

if ($options['showsql']) {
    $DB->set_debug(true);
}

if (CLI_MAINTENANCE) {
    echo "CLI maintenance mode active, cron execution suspended.\n";
    exit(1);
}

if (moodle_needs_upgrading()) {
    echo "Moodle upgrade pending, cron execution suspended.\n";
    exit(1);
}

if (empty($options['execute'])) {
    exit(0);
}

if (!empty($CFG->showcronsql)) {
    $DB->set_debug(true);
}
if (!empty($CFG->showcrondebugging)) {
    set_debugging(DEBUG_DEVELOPER, true);
}

core_php_time_limit::raise();
$starttime = microtime();

// Increase memory limit.
raise_memory_limit(MEMORY_EXTRA);

// Emulate normal session - we use admin accoutn by default.
cron_setup_user();

// Start output log.
$timenow = time();
$humantimenow = date('r', $timenow);
mtrace("Server Time: {$humantimenow}\n");

// Run all adhoc tasks.
$taskcount = 0;
while (!\core\task\manager::static_caches_cleared_since($timenow) &&
        $task = \core\task\manager::get_next_adhoc_task($timenow)) {
    cron_run_inner_adhoc_task($task);
    $taskcount++;
    unset($task);
}
mtrace("Ran {$taskcount} adhoc tasks found at {$humantimenow}");
