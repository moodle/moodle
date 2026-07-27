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
 * @package    core
 * @subpackage cli
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once("{$CFG->libdir}/clilib.php");

list($options, $unrecognized) = cli_get_params(
    [
        'classname' => null,
        'execute' => false,
        'failed' => false,
        'force' => false,
        'help' => false,
        'id' => null,
        'ignorelimits' => false,
        'keep-alive' => 0,
        'showdebugging' => false,
        'showsql' => false,
        'taskslimit' => null,
    ], [
        'c' => 'classname',
        'e' => 'execute',
        'f' => 'force',
        'h' => 'help',
        'i' => 'ignorelimits',
        'k' => 'keep-alive',
        'l' => 'taskslimit',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOT
Ad hoc cron tasks.

Options:
 -c, --classname        Run tasks with a certain classname (FQN)
 -e, --execute          Run all queued adhoc tasks
     --failed           Run only tasks that failed, ie those with a fail delay
 -f, --force            Run even if cron is disabled
 -h, --help             Print out this help
     --id=N             Run (failed) task with id
 -i  --ignorelimits     Ignore task_adhoc_concurrency_limit and task_adhoc_max_runtime limits
 -k, --keep-alive=N     Keep this script alive for N seconds and poll for new adhoc tasks
     --showdebugging    Show developer level debugging information
     --showsql          Show sql queries before they are executed
 -l, --taskslimit=N     Run at most N tasks

Examples:

Run all due queued tasks:
sudo -u www-data /usr/bin/php admin/cli/adhoc_task.php --execute

Run all queued tasks of specific class:
sudo -u www-data /usr/bin/php admin/cli/adhoc_task.php --classname='\\core_course\\task\\course_delete_modules'

Run a specific task:
sudo -u www-data /usr/bin/php admin/cli/adhoc_task.php --id=123456

Run a specific task with debugging:
sudo -u www-data /usr/bin/php admin/cli/adhoc_task.php --id=123456 --showsql --showdebugging

To profile a long running task:
sudo -u www-data /usr/bin/php admin/cli/adhoc_task.php --taskslimit=1 --classname='\\some\\class\\name' --ignorelimits


EOT;

if ($options['help']) {
    echo $help;
    exit(0);
}

if (CLI_MAINTENANCE) {
    echo "CLI maintenance mode active, cron execution suspended.\n";
    exit(1);
}

if (moodle_needs_upgrading()) {
    echo "Moodle upgrade pending, cron execution suspended.\n";
    exit(1);
}

if (!get_config('core', 'cron_enabled') && !$options['force']) {
    mtrace('Cron is disabled. Use --force to override.');
    exit(1);
}

// Common debugging options.
if ($options['showdebugging']) {
    set_debugging(DEBUG_DEVELOPER, true);
}

if ($options['showsql']) {
    $DB->set_debug(true);
}

if (!empty($CFG->showcronsql)) {
    $DB->set_debug(true);
}
if (!empty($CFG->showcrondebugging)) {
    set_debugging(DEBUG_DEVELOPER, true);
}

// Process params.
core_php_time_limit::raise();

// Increase memory limit.
raise_memory_limit(MEMORY_EXTRA);

// Emulate normal session - we use admin account by default.
\core\cron::setup_user();

\core\local\cli\shutdown::script_supports_graceful_exit();
$humantimenow = date('r', time());
mtrace("Server Time: {$humantimenow}\n");

$classname = $options['classname'];

// Run a single adhoc task only, if requested.
if (!empty($options['id'])) {
    $taskid = (int) $options['id'];
    \core\cron::run_adhoc_task($taskid);
    exit(0);
}

// Run all failed tasks.
if (!empty($options['failed'])) {
    \core\cron::run_failed_adhoc_tasks($classname);
    exit(0);
}

// Examine params and determine if we should run.
$execute = (bool) $options['execute'];
$keepalive = empty($options['keep-alive']) ? 0 : (int) $options['keep-alive'];
$taskslimit = empty($options['taskslimit']) ? null : (int) $options['taskslimit'];
$checklimits = empty($options['ignorelimits']);

if ($classname || $keepalive || $taskslimit) {
    $execute = true;
}

// Output the help text if no criteria for running the adhoc tasks are given.
if (!$execute) {
    echo $help;
    exit(0);
}

\core\cron::run_adhoc_tasks(time(), $keepalive, $checklimits, null, $taskslimit, $classname);
