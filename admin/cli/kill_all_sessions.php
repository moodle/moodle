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
 * CLI script to kill user sessions.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2017 Alexander Bias <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');

[$options, $unrecognized] = cli_get_params([
    'help' => false,
    'run' => false,
    'for-users' => false,
], [
    'h' => 'help',
]);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized), 2);
}

if ($options['help'] || !empty($options['for-users']) && !is_string($options['for-users'])) {
    $help = <<<EOL
Kill all Moodle sessions

Options:
-h, --help            Print out this help.
    --for-users       A comma-separated list of user ids.
    --run             Execute sessions termination, otherwise script will be run in a dry mode.

Example:
\$sudo -u www-data /usr/bin/php admin/cli/kill_all_sessions.php --run
\$sudo -u www-data /usr/bin/php admin/cli/kill_all_sessions.php --for-users=123,456 --run

EOL;

    echo $help;
    exit(0);
}

if (!empty($options['for-users'])) {
    $userids = explode(',', $options['for-users']);
    foreach ($userids as $userid) {
        if (!empty((int) $userid)) {
            if ($options['run']) {
                \core\session\manager::destroy_user_sessions((int) $userid);
                cli_writeln('All sessions for user with ID ' . $userid . ' have been destroyed');
            } else {
                cli_writeln('Dry run - all sessions for user with ID ' . $userid . ' will been destroyed');
            }
        } else {
            cli_writeln('Invalid user ID: ' . $userid);
        }
    }
} else {
    if ($options['run']) {
        \core\session\manager::destroy_all();
        cli_writeln('All sessions for all users have been destroyed');
    } else {
        cli_writeln('Dry run - all sessions for all users will be destroyed');
    }
}

exit(0);
