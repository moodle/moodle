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
 * @package    enrol_workdaystudent
 * @copyright  2025 onwards LSU Online & Continuing Education
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

// Make sure we're logged in.
require_login();

// If you are not supposed to be here, go away now.
if (!is_siteadmin()) {
    // Send them to the front page.
    redirect(new moodle_url('/'));
}

// Set the system context.
$context = context_system::instance();

// Extra super sure.
require_capability('moodle/site:config', $context);

// Set up the page.
$PAGE->set_url(new moodle_url('/enrol/workdaystudent/orphanedcourses.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);
$PAGE->set_title(get_string('orphanedusercourses', 'enrol_workdaystudent'));
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('orphanedusercourses', 'enrol_workdaystudent'));

// Instantiate the DB engine.
global $DB;

// First, get relevant courses.
$coursesql = "SELECT c.id, c.fullname
                FROM {enrol} e
                    INNER JOIN {course} c ON c.id = e.courseid
                    INNER JOIN {enrol_wds_sections} sec ON sec.moodle_status = c.id
                        AND sec.idnumber = c.idnumber
                    INNER JOIN {enrol_wds_periods} per ON per.academic_period_id = sec.academic_period_id
                        AND per.start_date < UNIX_TIMESTAMP()
                        AND per.end_date > UNIX_TIMESTAMP()
            WHERE e.enrol = 'workdaystudent'
            GROUP BY c.id";

// Get 'em.
$courses = $DB->get_records_sql($coursesql);

// Table output.
$table = new html_table();

// Setthe table header.
$table->head = [get_string('course'), get_string('numorphanedusers', 'enrol_workdaystudent'), get_string('actions')];

// Loop through courses and get any with orphaned users.
foreach ($courses as $course) {

    // Build out the parms.
    $parms = ['courseid' => $course->id, 'courseid2' => $course->id];

    // Build the SQL.
    $extrasql = "SELECT CONCAT(c.id, '_', ue.userid) AS uniquer,
                    c.fullname,
                    CONCAT(u.firstname, ' ', u.lastname) AS student_fn,
                    ue.userid
                    FROM {course} c
                        INNER JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
                        INNER JOIN {enrol} e ON e.courseid = c.id
                        INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id
                        INNER JOIN {role_assignments} ra ON ra.contextid = ctx.id
                            AND ra.userid = ue.userid
                            AND ra.roleid = 5
                        INNER JOIN {enrol_wds_sections} sec ON sec.idnumber = c.idnumber
                            AND sec.moodle_status = c.id
                        INNER JOIN {user} u ON u.id = ue.userid
                        INNER JOIN {enrol_wds_periods} per ON per.academic_period_id = sec.academic_period_id
                            AND per.start_date < UNIX_TIMESTAMP()
                            AND per.end_date > UNIX_TIMESTAMP()
                  WHERE c.id = :courseid
                      AND ue.userid NOT IN (
                          SELECT stu.userid
                              FROM {enrol_wds_sections} sec
                                  INNER JOIN {enrol_wds_student_enroll} se ON sec.section_listing_id = se.section_listing_id
                                  INNER JOIN {enrol_wds_students} stu ON stu.universal_id = se.universal_id
                                      AND se.status = 'enrolled'
                         WHERE sec.moodle_status = :courseid2
                    )
               GROUP BY uniquer";

    // Get the users.
    $users = $DB->get_records_sql($extrasql, $parms);

    // Count the users.
    $ucount = count($users);

    // Only output courses with orphans.
    if ($ucount > 0) {

        // Bould the course link.
        $link = html_writer::link(
            new moodle_url('/course/view.php', ['id' => $course->id]),
            $course->fullname
        );

        // Add the rest of the data.
        $table->data[] = [
            $link,
            $ucount,
            html_writer::link(
                new moodle_url('/enrol/workdaystudent/unenroll_candidates.php', ['id' => $course->id]),
                get_string('viewdetails', 'enrol_workdaystudent'))
        ];
    }
}

// If we have table data, output the table.
if (!empty($table->data)) {
    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('noorphanedusers', 'enrol_workdaystudent'), 'notifymessage');
}

echo $OUTPUT->footer();
