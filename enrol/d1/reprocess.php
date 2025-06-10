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
require_once($CFG->dirroot . '/enrol/d1/lib.php');

// Disables the time limit.
set_time_limit(0);

// Set up the page params.
$pageparams = [
    'courseid' => required_param('courseid', PARAM_INT),
];

// Set teh $courseid variable.
$courseid = $pageparams['courseid'];

// Set the context.
$context = \context_system::instance();
// Set the course context.
$coursecontext = \context_course::instance($courseid); 

// Require the capability to reprocess courses to view this page.
require_capability('enrol/d1:reprocess', $coursecontext);

// Authentication.
require_login();
if (!is_siteadmin()) {
    enrol_d1_plugin::redirect_to_url($CFG->wwwroot);
}

// Grab the course object from the supplied courseid.
$course = $DB->get_record('course', array('id' => $courseid));

// Set the title.
$title = get_string('reprocess', 'enrol_d1');
$pagetitle = $title;

// Set the url for this page.
$url = new moodle_url('/enrol/d1/reprocess.php?courseid=' . $courseid);

// Set the PAGE vars.
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

// Navbar Bread Crumbs
$PAGE->navbar->add($course->idnumber, new moodle_url('/course/view.php?id=' . $courseid));

echo $OUTPUT->header();

echo html_writer::start_tag('pre');
$starttime = microtime(true);

mtrace("Beginning enrollment for $course->idnumber.");

enrol_d1_plugin::run_d1_full_enroll($courseid);

$elapsedtime = round(microtime(true) - $starttime, 3);
mtrace("\n\nThis entire process took " . $elapsedtime . " seconds.");
echo html_writer::end_tag('pre');

echo $OUTPUT->footer();
