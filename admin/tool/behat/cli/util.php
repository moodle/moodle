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
 * CLI tool
 *
 * @package    tool_behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/behat/locallib.php');

// CLI options.
list($options, $unrecognized) = cli_get_params(
    array(
        'help'    => false,
        'enable'  => false,
        'disable' => false,
    ),
    array(
        'h' => 'help'
    )
);

$help = "
Behat tool

Options:
--enable Enables test environment and updates tests list
--disable Disables test environment

-h, --help     Print out this help

Example from Moodle root directory:
\$ php admin/tool/behat/cli/util.php --enable

More info in http://docs.moodle.org/dev/Acceptance_testing#Running_tests
";

if (!empty($options['help'])) {
    echo $help;
    exit(0);
}

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

// Run command.
if ($options['enable']) {
    $action = 'enable';
} else if ($options['disable']) {
    $action = 'disable';
} else {
    echo $help;
    exit(0);
}

tool_behat::switchenvironment($action);

mtrace(get_string('testenvironment' . $action, 'tool_behat'));

