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
 *
 * @package    enrol_workdaystudent
 * @copyright  2023 onwards LSU Online & Continuing Education
 * @copyright  2023 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use enrol_workdaystudent\form\unenroll_form;

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/enrol/workdaystudent/classes/unenroll_form.php');

$courseid = required_param('id', PARAM_INT);

// Ensure user is logged in and has permission.
require_login($courseid);
$context = \context_course::instance($courseid);

if(!is_siteadmin()) {
    require_capability('enrol/workdaystudent:unenroll', $context);
}

// Set up page.
$PAGE->set_url(new moodle_url('/enrol/workdaystudent/unenroll_candidates.php', ['id' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_heading(get_string('unenrollcandidates', 'enrol_workdaystudent'));
$PAGE->set_pagelayout('report');

echo $OUTPUT->header();

global $DB;

// Run query safely.
$sql = "SELECT u.id,
        u.firstname,
        u.lastname,
        u.username,
        u.firstnamephonetic,
        u.lastnamephonetic,
        u.middlename,
        u.alternatename
    FROM {course} c
    INNER JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
    INNER JOIN {enrol} e ON e.courseid = c.id AND e.enrol = 'workdaystudent'
    INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id
    INNER JOIN {user} u on u.id = ue.userid
    INNER JOIN {role_assignments} ra ON ra.contextid = ctx.id AND ra.userid = ue.userid AND ra.roleid = 5
    WHERE c.id = :courseid
      AND ue.userid NOT IN (
        SELECT stu.userid
        FROM {enrol_wds_sections} sec
        INNER JOIN {enrol_wds_student_enroll} se ON sec.section_listing_id = se.section_listing_id
        INNER JOIN {enrol_wds_students} stu ON stu.universal_id = se.universal_id AND se.status = 'enrolled'
        WHERE sec.moodle_status = :courseid2
    )
    ORDER BY u.lastname ASC, u.firstname ASC
";

$parms = ['courseid' => $courseid, 'courseid2' => $courseid];
$users = $DB->get_records_sql($sql, $parms);

// Build form.
$mform = new unenroll_form(null, ['users' => $users, 'courseid' => $courseid]);

// If we cancel, redirect to the course in question.
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', ['id' => $courseid]));

// We've submitted the form.
} else if ($data = $mform->get_data()) {

    $courseid = $data->id;

    // Make sure we have users.
    if ($users) {

        // Get the workday student enrollment plugin.
        $enrol = enrol_get_plugin('workdaystudent');

        // Get all teh workday student enrollment instances for this course.
        $instances = enrol_get_instances($courseid, true);

        // Loop through the instances.
        foreach ($instances as $instance) {

            // Make sure we're ONLY enrolling workday students.
            if ($instance->enrol === 'workdaystudent') {

                // Loop through all these poor unfortunate souls.
                foreach ($users as $user) {

                    // Unenroll them.
                    $enrol->unenrol_user($instance, $user->id);
                }
            }
        }
    }

    // Redirect back to the course groups config page.
    redirect(
        new moodle_url('/group/index.php', ['id' => $courseid]),
        get_string('unenrollsuccess', 'enrol_workdaystudent'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$mform->display();
echo $OUTPUT->footer();

