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
 * @package    tool
 * @subpackage checklearningrecords
 * @copyright  2020 E-Learn Design https://www.e-learndesign
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../../config.php');
require(__DIR__.'/../lib.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/adminlib.php');

$help =
    "fix issue with stored learning records.

-h, --help                      Print out this help.
-c, --check                     Check how many issues there are.
-f, --fix                       Fix all issues.

Example:
\$ sudo -u www-data /usr/bin/php admin/tool/checklearningrecords/cli/checklearningrecords.php --check
";

list($options, $unrecognized) = cli_get_params(
    array(
        'check'  => false,
        'fix' => false,
        'help'    => false,
    ),
    array(
        'h' => 'help',
    )
);

if ($options['help'] || (!$options['check'] && !$options['fix'])) {
    echo $help;
    exit(0);
}

if ($options['check']) {
    mtrace(get_string('brokenlicenses', 'tool_checklearningrecords', count($brokenlicenses)));
    mtrace(get_string('brokencompletions', 'tool_checklearningrecords', count($brokencompletions)));
    mtrace(get_string('missingcompletions', 'tool_checklearningrecords', count($missingcompletions)));
}

if ($options['fix']) {
    // Get the incomplete completion records
    $brokencompletions = $DB->get_records_sql("SELECT * FROM {local_iomad_track}
                                               WHERE
                                               (timecompleted > 0
                                                AND timestarted IS NULL)
                                               OR
                                               (timecompleted > 0
                                                AND timeenrolled IS NULL)
                                               OR
                                               (timestarted > 0
                                                AND timeenrolled IS NULL)");

    do_fixbrokencompletions($brokencompletions);

    // Get the incomplete license records
    $brokenlicenses = $DB->get_records_sql("SELECT * FROM {local_iomad_track}
                                            WHERE
                                            (licenseid > 0
                                             AND licenseallocated IS NULL)
                                            OR
                                            (licenseid = 0
                                             AND licenseallocated > 0
                                             AND licensename != 'HISTORIC')");

    do_fixbrokenlicenses($brokenlicenses);

    // Get the incomplete completion records
    $missingcompletions = $DB->get_records_sql("SELECT lit.*,cc.id as ccid,cc.timeenrolled AS cctimeenrolled, cc.timestarted AS cctimestarted, cc.timecompleted AS cctimecompleted
                                                FROM {local_iomad_track} lit
                                                JOIN {course_completions} cc
                                                ON (lit.userid = cc.userid AND lit.courseid = cc.course)
                                                WHERE
                                                cc.timecompleted > 0
                                                AND lit.timecompleted IS NULL
                                                AND lit.timestarted > 0");

    do_fixmissingcompletions($missingcompletions);

    // Sort out all expiry.
    // Calculate the timeexpired for all users.
    // Get the courses where there is a expired value.
    $expirycourses = $DB->get_records_sql("SELECT courseid,validlength FROM {iomad_courses}
                                            WHERE validlength > 0");
    foreach ($expirycourses as $expirycourse) {
        $offset = $expirycourse->validlength * 24 * 60 * 60;
        $DB->execute("UPDATE {local_iomad_track}
                      SET timeexpires = timecompleted + :offset
                      WHERE courseid = :courseid
                      AND timecompleted > 0",
                      array('courseid' => $expirycourse->courseid,
                            'offset' => $offset));
    }
}
