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
 * CLI script to run the merger.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2013 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_mergeusers\local\cli\gathering_merger;
use tool_mergeusers\local\config;
use tool_mergeusers\local\user_merger;

define("CLI_SCRIPT", true);

require_once(dirname(__DIR__, 4) . '/config.php');

// Force always debugging information, to show administrators what it is happening.
ini_set('display_errors', true);
ini_set('error_reporting', E_ALL);

global $CFG;
require_once($CFG->libdir . '/clilib.php');

// Now get cli options.
[$options, $unrecognized] = cli_get_params(
    [
        'debugdb'    => false,
        'alwaysrollback' => false,
        'help'    => false,
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized), 2);
}

if ($options['help']) {
    $help =
        "Command Line user merger. These are the available options:

Options:
--help            Print out this help
--debugdb         Output all db statements used to do the merge
--alwaysrollback  Useful for testing without actual changes on the system.
                  Do the full merge but rollback the transaction at the last opportunity.
                  When using this option, the exception used for the rollback aborts the CLI script.
                  You will have to execute this script again manually to test another merge.
";

    echo $help;
    exit(0);
}

// Loads current configuration.
$config = config::instance();

$config->debugdb = !empty($options['debugdb']);
$config->alwaysrollback = !empty($options['alwaysrollback']);

// Initializes merger tool.
// Mqy abort execution if database is not supported.
$mut = new user_merger($config);
$merger = new gathering_merger($mut);

// Initializes gathering instance.
$gatheringname = $config->gathering;
$gathering = new $gatheringname();

// Collects and performs user merges.
$merger->merge($gathering);
