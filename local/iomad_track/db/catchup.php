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
 * @package   local_iomad_signup
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/clilib.php');

// Get cli options.
list($options, $unrecognized) = cli_get_params(
    array(
        'courses'           => false,
        'fix'               => false,
        'help'              => false
    ),
    array(
        'h' => 'help',
        'c' => 'courses',
        'f' => 'fix'
    )
);

if ($options['help'] || empty($options['courses'])) {
    $help =
"Checks and fixes that course modules and sections reference each other correctly.

Compares DB fields course_sections.sequence and course_modules.section
checking that:
- course_sections.sequence contains each module id not more than once in the course
- for each moduleid from course_sections.sequence the field course_modules.section
  refers to the same section id (this means course_sections.sequence is more
  important if they are different)
- each module in the course is present in one of course_sections.sequence
- section sequences do not contain non-existing course modules

If there are any mismatches, the message is displayed. If --fix is specified,
the records in DB are corrected.

This script may run for a long time on big systems if called for all courses.

Avoid executing the script when another user may simultaneously edit any of the
courses being checked (recommended to run in mainenance mode).

Options:
-c, --courses         List courses that need to be checked (comma-separated
                      values or * for all). Required
-f, --fix             Fix the mismatches in DB. If not specified check only and
                      report problems to STDERR
-h, --help            Print out this help

Example:
\$sudo -u www-data /usr/bin/php admin/cli/fix_course_sequence.php --courses=*
\$sudo -u www-data /usr/bin/php admin/cli/fix_course_sequence.php --courses=2,3,4 --fix
";
}

require('install.php');

$comprecords = $DB->get_records_sql("select * from {course_completions} where timecompleted is not null and id not in (select cc.id from {course_completions} cc JOIN {local_iomad_track} lit ON (cc.course = lit.courseid AND cc.userid = lit.userid AND cc.timeenrolled = lit.timeenrolled AND cc.timecompleted = lit.timecompleted))");
foreach ($comprecords as $comprec) {
    if ($user = $DB->get_record('user', array('id'=> $comprec->userid))) {
        $course = $DB->get_record('course', array('id'=>$comprec->course));

        // Get the final grade for the course.
        if ($graderec = $DB->get_record_sql("SELECT gg.* FROM {grade_grades} gg
                                             JOIN {grade_items} gi ON (gg.itemid = gi.id
                                                                   AND gi.itemtype = 'course'
                                                                   AND gi.courseid = :courseid)
                                             WHERE gg.userid = :userid", array('courseid' => $comprec->course,
                                                                               'userid' => $comprec->userid))) {
            $finalgrade = $graderec->finalgrade;
        } else {
            $finalgrade = 0;
        }
        $company = company::by_userid($user->id);
        $companyrec = $DB->get_record('company', array('id' => $company->id));
        $department = $DB->get_record_sql("SELECT d.* from {department} d JOIN {company_users} cu ON (d.id = cu.departmentid) WHERE cu.companyid = :companyid AND cu.userid = :userid", array('companyid' => $companyrec->id, 'userid' => $user->id));

        if ($DB->get_record('iomad_courses', array('courseid' => $course->id, 'licensed' => 1))) {
            // Its a licensed course, get the last license.
            $licenserecs = $DB->get_records_sql("SELECT * FROM {companylicense_users}
                                                 WHERE userid = :userid AND licensecourseid = :licensecourseid AND issuedate < :issuedate
                                                 AND licensid IN (SELECT id from {companylicense} WHERE companyid = :companyid)
                                                 ORDER BY issuedate DESC",
                                                 array('licensecourseid' => $course->id, 'userid' => $user->id, 'companyid' => $companyrec->id, 'issuedate' => $comprecord->timecompleted),
                                                 0,1);
            $licenserec = array_pop($licenserecs);
            if ($license = $DB->get_record('companylicense', array('id' => $licenserec->licenseid))) {
                $licenseid = $license->id;
                $licensename = $license->name;
            } else {
                $licenseid = 0;
                $licensename = '';
            }
        } else {
            $licenseid = 0;
            $licensename = '';
        }

        // Record the completion event.
        $completion = new \StdClass();
        $completion->courseid = $comprec->course;
        $completion->userid = $comprec->userid;
        $completion->timeenrolled = $comprec->timeenrolled;
        $completion->timestarted = $comprec->timestarted;
        $completion->timecompleted = $comprec->timecompleted;
        $completion->finalscore = $finalgrade;
        $completion->courseid = $course->id;
        $completion->coursename = $course->fullname;
        $completion->firstname = $user->firstname;
        $completion->lastname = $user->lastname;
        $completion->companyid = $companyrec->id;
        $completion->companyname = $companyrec->name;
        $completion->departmentid = $department->id;
        $completion->departmentname = $department->name;
        $completion->licenseid = $licenseid;
        $completion->licensename = $licensename;

        if ($trackid = $DB->insert_record('local_iomad_track', $completion)) {
            //xmldb_local_iomad_track_record_certificates($comprec->course, $comprec->userid, $trackid);
        }

    }
}
