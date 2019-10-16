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
        'keep-alive' => 0,
        'showsql' => false,
        'showdebugging' => false,
    ], [
        'h' => 'help',
        'e' => 'execute',
        'k' => 'keep-alive',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help'] or empty($options['execute'])) {
    $help = <<<EOT
Ad hoc cron tasks.

Options:
 -h, --help                Print out this help
     --showsql             Show sql queries before they are executed
     --showdebugging       Show developer level debugging information
 -e, --execute             Run all queued adhoc tasks
 -k, --keep-alive=N        Keep this script alive for N seconds and poll for new adhoc tasks

Example:
\$sudo -u www-data /usr/bin/php admin/tool/task/cli/adhoc_task.php --execute

EOT;

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
if (empty($options['keep-alive'])) {
    $options['keep-alive'] = 0;
}

if (!empty($CFG->showcronsql)) {
    $DB->set_debug(true);
}
if (!empty($CFG->showcrondebugging)) {
    set_debugging(DEBUG_DEVELOPER, true);
}

core_php_time_limit::raise();

// Increase memory limit.
raise_memory_limit(MEMORY_EXTRA);

// Emulate normal session - we use admin account by default.
cron_setup_user();

// Start output log.
$timestart = time();
$timenow = $timestart;
$finishtime = $timenow + (int)$options['keep-alive'];
$humantimenow = date('r', $timenow);
mtrace("Server Time: {$humantimenow}\n");

// Run all adhoc tasks.
$taskcount = 0;
$waiting = false;
while (!\core\task\manager::static_caches_cleared_since($timestart)) {

    $task = \core\task\manager::get_next_adhoc_task($timenow);

    if ($task) {
        if ($waiting) {
            cli_writeln('');
        }
        $waiting = false;
        cron_run_inner_adhoc_task($task);
        $taskcount++;
        unset($task);
    } else {
        if (time() > $finishtime) {
            break;
        }
        if (!$waiting) {
            cli_write('Waiting for more adhoc tasks to be queued ');
        } else {
            cli_write('.');
        }
        $waiting = true;
        sleep(1);
        $timenow = time();
    }
}
if ($waiting) {
    cli_writeln('');
}

mtrace("Ran {$taskcount} adhoc tasks found at {$humantimenow}");

