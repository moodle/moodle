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
 * CLI script to fix duplicate course idnumbers.
 *
 * @package    enrol_workdaystudent
 * @copyright  2025 onwards LSUOnline & Continuing Education
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/enrol/workdaystudent/classes/testwds2.php');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
    [
        'help' => false
    ],
    [
        'h' => 'help'
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help = "Fix duplicate course idnumbers for Workday Student Enrollment.

Options:
-h, --help            Print this help.

Example:
\$ php fix_duplicate_idnumbers.php
";

    echo $help;
    exit(0);
}

// Start the timer.
$starttime = microtime(true);

echo "Starting duplicate course idnumber fix...\n";

// Run the fix.
$stats = workdaystudent::fix_duplicate_course_idnumbers();

$timeelapsed = round(microtime(true) - $starttime, 2);

echo "\nProcess completed in $timeelapsed seconds.\n";
echo "Found {$stats['duplicates_found']} duplicate idnumbers.\n";
echo "Fixed {$stats['courses_fixed']} courses.\n";
