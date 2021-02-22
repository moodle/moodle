<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * CLI script to manually get the meeting report.
 *
 * @package    mod_zoom
 * @copyright  2020 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
        array(
                'help' => false,
                'start' => false,
                'end' => false,
                'hostuuid' => false,
                'courseid' => false
        ),
        array(
                'h' => 'help'
        )
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help'] || empty($options['start'] || empty($options['end']))) {
    $help = "CLI script to manually get the meeting report for a given start and end date.

Options:
-h, --help          Print out this help
--start             Required. In YYYY-MM-DD format
--end               Required. In YYYY-MM-DD format
--hostuuid          Optional. Specific host we want to get meetings for.
--courseid          Optional. If given, will find all hosts for course and get meeting reports.

Example:
\$sudo -u www-data /usr/bin/php mod/zoom/cli/get_meeting_report.php --start=2020-03-31 --end=2020-04-01
";
    cli_error($help);
}

$hostuuids = null;
if (!empty($options['hostuuid'])) {
    $hostuuids = array($options['hostuuid']);
} else if (!empty($options['courseid'])) {
    // Find all hosts for course.
    $hostuuids = $DB->get_fieldset_select('zoom', 'DISTINCT host_id', 'course=:courseid',
            array('courseid' => $options['courseid']));
    if (empty($hostuuids)) {
        cli_error('No hosts found for course');
    }
}

// Turn on debugging so we can see the detailed progress.
set_debugging(DEBUG_DEVELOPER, true);

$meetingtask = new mod_zoom\task\get_meeting_reports();
$meetingtask->execute($options['start'], $options['end'], $hostuuids);

cli_writeln('DONE!');