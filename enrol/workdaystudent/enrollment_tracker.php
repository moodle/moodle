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
require_once($CFG->libdir.'/tablelib.php');
require_once(__DIR__ . '/classes/enrollment_tracker_data.php');

global $DB, $OUTPUT, $PAGE, $CFG;

// Get course ID and course/context objects and parms.
$courseid = required_param('courseid', PARAM_INT);
$sorter = optional_param('tsort', null, PARAM_TEXT);
$sortdir = optional_param('tdir', null, PARAM_TEXT);

require_login($courseid);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);

// Capability check.
if (!is_siteadmin()) {
    require_capability('enrol/workdaystudent:viewenrollmenttracker', $context);
}

// Page setup.
$PAGE->set_context($context);
$PAGE->set_pagetype('course-view');
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/enrol/workdaystudent/enrollment_tracker.php', [
    'courseid' => $course->id,
    'tsort' => 'registered_date',
    'tdir' => 3
]);

$PAGE->set_title(get_string('enrollmenttracker', 'enrol_workdaystudent'));
$PAGE->set_heading(get_string('enrollmentdata', 'enrol_workdaystudent'));

// Navbar Bread Crumbs.
$PAGE->navbar->add($course->fullname, new moodle_url('/course/view.php?id=' . $courseid));

// Output the header.
echo $OUTPUT->header();

// Build out the sort.
if (is_null($sorter)) {
    $sort = [];
} else {
    $sort = [
        'field' => $sorter,
        'dir' => $sortdir
    ];
}

// Fetch enrollments.
$enrollments = enrollment_tracker_data::get_historical_enrollments($course->id, $sort);

// Process and display data.
if (empty($enrollments)) {

    echo $OUTPUT->notification(get_string('noenrollmentdata', 'enrol_workdaystudent'));
} else {

    // Build out the table.
    $table = new flexible_table('enrol-workdaystudent-tracker');
    $table->set_attribute('class', 'generaltable boxaligncenter');

    // Define table columns and headers
    $columns = array('studentname', 'email', 'section_number', 'status', 'registered_date', 'drop_date');
    $headers = array(
        get_string('studentname', 'enrol_workdaystudent'),
        get_string('email', 'moodle'),
        get_string('section_number', 'enrol_workdaystudent'),
        get_string('enrolmentstatus', 'enrol_workdaystudent'),
        get_string('adddatetime', 'enrol_workdaystudent'),
        get_string('dropdatetime', 'enrol_workdaystudent')
    );
    $table->define_columns($columns);
    $table->define_headers($headers);
    $table->define_baseurl($PAGE->url);

    // Enable sorting, default sort by registered_date.
    $table->sortable(true, 'registered_date', SORT_ASC); 

    $table->setup();

    foreach ($enrollments as $enrollment) {

        // Student Name.
        $studentdetails = enrollment_tracker_data::get_student_name_details($enrollment->universal_id);

        if ($studentdetails && isset($studentdetails->firstname)) {
            $studentnamedisplay = $studentdetails->firstname .
                                   (!empty($studentdetails->middlename) ? ' ' . $studentdetails->middlename : '') .
                                   ' ' . $studentdetails->lastname;
        } else {
            // Fallback if student details are incomplete or not found.
            $studentnamedisplay = get_string('unknownuser', 'enrol_workdaystudent') . 
                                   ' (ID: ' . $enrollment->universal_id . ')';
        }

        // Student email.
        $studentemail = $studentdetails->email;

        // Add Date (assuming $enrollment->registered_date is a direct timestamp).
        $formattedadddate = !empty($enrollment->registered_date) ?
            enrollment_tracker_data::format_date_for_display(
                (int)$enrollment->registered_date
            ) : '-';

        // Drop Date (assuming $enrollment->drop_date is a direct timestamp).
        $formatteddropdate = !empty($enrollment->drop_date) ?
            enrollment_tracker_data::format_date_for_display(
                (int)$enrollment->drop_date
            ) : '-';

        $table->add_data(
            array(
                $studentnamedisplay,
                \html_writer::link("mailto:$studentemail", $studentemail),
                $enrollment->section_number,
                $enrollment->status,
                $formattedadddate,
                $formatteddropdate
            )
        );
    }

    $table->print_html();
}

// Output the footer.
echo $OUTPUT->footer();
