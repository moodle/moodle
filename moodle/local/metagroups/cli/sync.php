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
 * @package    local_metagroups
 * @copyright  2014 Paul Holden (pholden@greenhead.ac.uk)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/local/metagroups/locallib.php');

// Ensure errors are well explained.
set_debugging(DEBUG_DEVELOPER, true);

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
    array('course' => null, 'verbose' => false, 'help' => false),
    array('c' => 'course', 'v' => 'verbose', 'h' => 'help')
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
        "Execute initial meta-course group synchronization.

This is recommended if installing plugin into a site with existing courses and groups, or after adding
a new metacourse enrolment instance to a course with existing groups (use the --course switch).

Options:
-c, --course          Course ID (if not specified, then all courses will be synchronized)
-v, --verbose         Print verbose progess information
-h, --help            Print out this help

Example:
\$ sudo -u www-data /usr/bin/php local/metagroups/sync.php
";

    echo $help;
    die;
}

if (empty($options['verbose'])) {
    $trace = new null_progress_trace();
} else {
    $trace = new text_progress_trace();
}

local_metagroups_sync($trace, $options['course']);
$trace->finished();

exit(0);
