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
 * @package    ues_reprocess
 * @copyright  2024 onwards LSUOnline & Continuing Education
 * @copyright  2024 onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Make sure this can only run via CLI.
define('CLI_SCRIPT', true);

$timestart = microtime(true);

// Include the main Moodle config.
require_once(__DIR__ . '/../../../../config.php');

// Include the Workday Student helper class.
require_once(__DIR__ . '/../workdaystudent.php');

global $DB;

$coursesql = "SELECT c.id,
    c.fullname
    FROM {enrol} e
        INNER JOIN {course} c
            ON c.id = e.courseid
        INNER JOIN {enrol_wds_sections} sec
            ON sec.moodle_status = c.id
            AND sec.idnumber = c.idnumber
        INNER JOIN {enrol_wds_periods} per
            ON per.academic_period_id = sec.academic_period_id
            AND per.start_date < UNIX_TIMESTAMP()
            AND per.end_date > UNIX_TIMESTAMP()
    WHERE e.enrol = 'workdaystudent'
    GROUP BY c.id";

$courses = $DB->get_records_sql($coursesql);

$extrasql = "SELECT CONCAT(c.id, '_', ue.userid) AS uniquer,
    c.fullname,
    CONCAT(u.firstname, ' ', u.lastname) AS student_fn,
    ue.userid
    FROM {course} c
        INNER JOIN {context} ctx
            ON ctx.instanceid = c.id
            AND ctx.contextlevel = 50
        INNER JOIN {enrol} e
            ON e.courseid = c.id
        INNER JOIN {user_enrolments} ue
            ON ue.enrolid = e.id
        INNER JOIN {role_assignments} ra
            ON ra.contextid = ctx.id
            AND ra.userid = ue.userid
            AND ra.roleid = 5
        INNER JOIN {enrol_wds_sections} sec
            ON sec.idnumber = c.idnumber
            AND sec.moodle_status = c.id
        INNER JOIN {user} u
            ON u.id = ue.userid
        INNER JOIN {enrol_wds_periods} per
            ON per.academic_period_id = sec.academic_period_id
            AND per.start_date < UNIX_TIMESTAMP()
            AND per.end_date > UNIX_TIMESTAMP()
    WHERE c.id = :courseid
        AND ue.userid NOT IN (
            SELECT stu.userid
            FROM {enrol_wds_sections} sec
                INNER JOIN mdl_enrol_wds_student_enroll se
                    ON sec.section_listing_id = se.section_listing_id
                INNER JOIN mdl_enrol_wds_students stu
                    ON stu.universal_id = se.universal_id
                    AND se.status = 'enrolled'
            WHERE sec.moodle_status = :courseid2
        )
    GROUP BY uniquer";

foreach($courses as $course) {

    $parms = ['courseid' => $course->id, 'courseid2' => $course->id];

    $users = $DB->get_records_sql($extrasql, $parms);

    $ucount = count($users);

    // Make sure we have users.
    if ($users && ($ucount > 0)) {

        mtrace("\nUnenrolling $ucount orphaned users from $course->fullname - id: $course->id.");

continue;

        // Get the workday student enrollment plugin.
        $enrol = enrol_get_plugin('workdaystudent');

        // Get all the workday student enrollment instances for this course.
        $instances = enrol_get_instances($course->id, true);

        // Loop through the instances.
        foreach ($instances as $instance) {

            // Make sure we're ONLY enrolling workday students.
            if ($instance->enrol === 'workdaystudent') {

                // Loop through all these poor unfortunate souls.
                foreach ($users as $user) {

                    mtrace("  Unenrolling $user->student_fn - id: $user->userid from $user->fullname - id: $course->id.");
                    // Unenroll them.
//                    $enrol->unenrol_user($instance, $user->userid);

                }
            }
        }
    }
}
