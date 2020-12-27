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
 * Flatfile CLI tool.
 *
 * Notes:
 *   - it is required to use the web server account when executing PHP CLI scripts
 *   - you need to change the "www-data" to match the apache user account
 *   - use "su" if "sudo" not available
 *
 * Update
 *
 * This plugin now has a enrolment sync scheduled task. Scheduled tasks were
 * introduced in Moodle 2.7.  It is possible to override the scheduled tasks
 * configuration and run a single scheduled task immediately using the
 * admin/tool/task/cli/schedule_task.php script. This is the recommended
 * method to use for immediate enrollment synchronisation.
 *
 * Usage help:
 * $ php admin/tool/task/cli/schedule_task.php -h
 *
 * Execute task:
 * $ sudo -u www-data /usr/bin/php admin/tool/task/cli/schedule_task.php /
 * --execute=\\enrol_flatfile\\task\\flatfile_sync_task
 *
 * @package    enrol_flatfile
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once("$CFG->libdir/clilib.php");

// Now get cli options.
list($options, $unrecognized) = cli_get_params(array('verbose'=>false, 'help'=>false), array('v'=>'verbose', 'h'=>'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
        "Process flat file enrolments, update expiration and send notifications.

Options:
-v, --verbose         Print verbose progress information
-h, --help            Print out this help

Example:
\$ sudo -u www-data /usr/bin/php enrol/flatfile/cli/sync.php
";

    echo $help;
    die;
}

if (!enrol_is_enabled('flatfile')) {
    cli_error('enrol_flatfile plugin is disabled, synchronisation stopped', 2);
}

/** @var $plugin enrol_flatfile_plugin */
$plugin = enrol_get_plugin('flatfile');

if (empty($options['verbose'])) {
    $trace = new null_progress_trace();
} else {
    $trace = new text_progress_trace();
}

$result = $plugin->sync($trace);

exit($result);
