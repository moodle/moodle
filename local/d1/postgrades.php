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
 * @package    enrol_d1
 * @copyright  2022 onwards Louisiana State University
 * @copyright  2022 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once('../../config.php');
require_once($CFG->dirroot . '/local/d1/classes/d1.php');

// Disables the time limit.
set_time_limit(0);

// Set up the page params.
$pageparams = [
    'courseid' => required_param('courseid', PARAM_INT),
    'limits' => required_param('limits', PARAM_INT),
];

// Set the $courseid variable.
$courseid = $pageparams['courseid'];

// Set limits to a bool.
$limits   = $pageparams['limits'] == 1 ? true : false;

// Authentication.
require_login();
if (!$limits && !is_siteadmin()) {
    lsupgd1::redirect_to_url($CFG->wwwroot . '/course/view.php?id=' . $courseid);
}

// Set the context.
$context = \context_system::instance();

// Set the course context.
$coursecontext = \context_course::instance($courseid);

// Require the capability to reprocess courses to view this page.
require_capability('local/d1:postgrades', $coursecontext);

// Set the title.
$title = get_string('postgrades', 'local_d1');
$pagetitle = $title;

// Set the url for this page.
$url = new moodle_url('/local/d1/postgrades.php?courseid=' . $courseid . '&limits=' . $limits);

// Set the PAGE vars.
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

// Grab the course object from the supplied courseid.
$course = $DB->get_record('course', array('id' => $courseid));

// Navbar Bread Crumbs
$PAGE->navbar->add($course->idnumber, new moodle_url('/course/view.php?id=' . $courseid));

// Output the header.
echo $OUTPUT->header();

// Output the starting pre tag for live updates.
echo html_writer::start_tag('pre');

// Set the start time.
$starttime = microtime(true);

// Get a D1 token.
$token = lsupgd1::get_token();

// Is this course a PD or ODL course?
$coursetype = lsupgd1::pd_odl($course);

mtrace("Posting grades for the $coursetype course: $course->fullname.");

// Do stuff according to where we are.
if ($coursetype === "odl") {

    // Get the relevant ODL grade postings for the course in question.
    $postings = lsupgd1::get_odl_dgps(false, $course->idnumber, $limits);
    mtrace("  Posting " . count($postings) . " grades for the $coursetype course: $course->fullname.");
} else if ($coursetype === "pd") {

    // Get the relevant PD grade postings for the course in question.
    $postings = lsupgd1::get_pd_dgps(false, $course->idnumber, $limits);
    mtrace("  Posting " . count($postings) . " grades for the $coursetype course: $course->fullname.");
} else {
    // We are neither in a PD or ODL course.
    $postings = null;
    mtrace("  No grades to post for the non-PD or non-ODL course: $course->idnumber.");
}

// If we have stuff to post, do it.
if (!empty($postings)) {
    // Post the grades.
    $pgs = lsupgd1::course_grade_postings($postings);
    mtrace("Posted " . count($pgs) . " grades for the $coursetype course: $course->fullname.");
} else {
    mtrace("Posted 0 grades for the $coursetype course: $course->fullname.");
}


// Calculate how long posting grades took.
$elapsedtime = round(microtime(true) - $starttime, 3);

mtrace("\n\nThis entire process took " . $elapsedtime . " seconds.");

// Output the ending pre tag for live updates.
echo html_writer::end_tag('pre');

// Output the footer.
echo $OUTPUT->footer();
