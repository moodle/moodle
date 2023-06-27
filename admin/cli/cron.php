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
 * CLI cron
 *
 * This script looks through all the module directories for cron.php files
 * and runs them.  These files can contain cleanup functions, email functions
 * or anything that needs to be run on a regular basis.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');      // cli only functions
require_once($CFG->libdir.'/cronlib.php');

// now get cli options
list($options, $unrecognized) = cli_get_params(
    [
        'help' => false,
        'stop' => false,
        'list' => false,
        'force' => false,
        'enable' => false,
        'disable' => false,
        'disable-wait' => false,
    ], [
        'h' => 'help',
        's' => 'stop',
        'l' => 'list',
        'f' => 'force',
        'e' => 'enable',
        'd' => 'disable',
        'w' => 'disable-wait',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
"Execute periodic cron actions.

Options:
-h, --help               Print out this help
-s, --stop               Notify all other running cron processes to stop after the current task
-l, --list               Show the list of currently running tasks and how long they have been running
-f, --force              Execute task even if cron is disabled
-e, --enable             Enable cron
-d, --disable            Disable cron
-w, --disable-wait=600   Disable cron and wait until all tasks finished or fail after N seconds (optional param)

Example:
\$sudo -u www-data /usr/bin/php admin/cli/cron.php
";

    echo $help;
    die;
}

if ($options['stop']) {
    // By clearing the caches this signals to other running processes
    // to exit after finishing the current task.
    \core\task\manager::clear_static_caches();
    die;
}

if ($options['enable']) {
    set_config('cron_enabled', 1);
    mtrace('Cron has been enabled for the site.');
    exit(0);
}

if ($options['disable']) {
    set_config('cron_enabled', 0);
    \core\task\manager::clear_static_caches();
    mtrace('Cron has been disabled for the site.');
    exit(0);
}

if ($options['list']) {
    $tasks = \core\task\manager::get_running_tasks();
    mtrace('The list of currently running tasks:');
    $format = "%7s %-12s %-9s %-20s %-52s\n";
    printf ($format,
        'PID',
        'HOST',
        'TYPE',
        'TIME',
        'CLASSNAME'
    );
    foreach ($tasks as $task) {
        printf ($format,
            $task->pid,
            substr($task->hostname, 0, 12),
            $task->type,
            format_time(time() - $task->timestarted),
            substr($task->classname, 0, 52)
        );
    }
    exit(0);
}

if ($wait = $options['disable-wait']) {
    $started = time();
    if (true === $wait) {
        // Default waiting time.
        $waitsec = 600;
    } else {
        $waitsec = $wait;
        $wait = true;
    }

    set_config('cron_enabled', 0);
    \core\task\manager::clear_static_caches();
    mtrace('Cron has been disabled for the site.');
    mtrace('Allocating '. format_time($waitsec) . ' for the tasks to finish.');

    $lastcount = 0;
    while ($wait) {
        $tasks = \core\task\manager::get_running_tasks();

        if (count($tasks) == 0) {
            mtrace('');
            mtrace('All scheduled and adhoc tasks finished.');
            exit(0);
        }

        if (time() - $started >= $waitsec) {
            mtrace('');
            mtrace('Wait time ('. format_time($waitsec) . ') elapsed, but ' . count($tasks) . ' task(s) still running.');
            mtrace('Exiting with code 1.');
            exit(1);
        }

        if (count($tasks) !== $lastcount) {
            mtrace('');
            mtrace(count($tasks) . " tasks currently running.", '');
            $lastcount = count($tasks);
        } else {
            mtrace('.', '');
        }

        sleep(1);
    }
}

if (!get_config('core', 'cron_enabled') && !$options['force']) {
    mtrace('Cron is disabled. Use --force to override.');
    exit(1);
}

\core\local\cli\shutdown::script_supports_graceful_exit();

cron_run();
