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
 * CLI script to reset dashboards.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2021 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->dirroot.'/my/lib.php');

list($options, $unrecognized) = cli_get_params([
    'help'    => false,
    'execute' => false,
], [
    'h' => 'help',
    'e' => 'execute',
]);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized), 2);
}

if (!$options['execute']) {
    $help = <<<EOF
Resets Moodle dashboards for all users

Options:
-h, --help          Print out this help
-e, --execute       Actually run the reset

Example:
\$ sudo -u www-data /usr/bin/php admin/cli/dashboard_reset.php -e

EOF;

    echo $help;
    exit(0);
}

$progressbar = new progress_bar();
$progressbar->create();
my_reset_page_for_all_users(MY_PAGE_PRIVATE, 'my-index', $progressbar);

