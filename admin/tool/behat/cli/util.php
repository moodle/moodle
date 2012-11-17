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
 * CLI script
 *
 * @package    tool_behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/behat/locallib.php');

// now get cli options
list($options, $unrecognized) = cli_get_params(
    array(
        'help'               => false,
        'stepsdefinitions'   => false,
        'runtests'           => false,
        'filter'             => false,
        'tags'               => false,
        'extra'              => false,
        'with-javascript'    => false,
        'testenvironment'    => false
    ),
    array(
        'h' => 'help'
    )
);

$help = "
Behat tool

Ensure the user who executes the action has permissions over behat installation

Options:
--stepsdefinitions   Displays the available steps definitions (accepts --filter=\"\" option to restrict the list to the matching definitions)
--runtests           Runs the tests (accepts --with-javascript option, --tags=\"\" option to execute only the matching tests and --extra=\"\" to specify extra behat options)
--testenvironment    Allows the test environment to be accesses through the built-in server (accepts value 'enable' or 'disable')

-h, --help     Print out this help

Example from Moodle root directory:
\$ php admin/tool/behat/cli/util.php --runtests --tags=\"tool_behat\"

More info in http://docs.moodle.org/dev/Acceptance_testing#Usage
";

if (!empty($options['help'])) {
    echo $help;
    exit(0);
}

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$commands = array('stepsdefinitions', 'runtests', 'testenvironment');
foreach ($commands as $command) {
    if ($options[$command]) {
        $action = $command;
    }
}

if (empty($action)) {
    mtrace('No command selected');
    echo $help;
    exit(0);
}

switch ($action) {

    case 'stepsdefinitions':
        tool_behat::stepsdefinitions($options['filter']);
        break;

    case 'runtests':
        tool_behat::runtests($options['with-javascript'], $options['tags'], $options['extra']);
        break;

    case 'testenvironment':
        tool_behat::switchenvironment($options['testenvironment']);
        break;
}


mtrace(get_string('finished', 'tool_behat'));
