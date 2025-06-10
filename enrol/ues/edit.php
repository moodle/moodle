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
 * @package    enrol_ues
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Philip Cali, Adam Zapletal, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Include the requirements.
require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/enrol/ues/edit_form.php');

// Grap the courseid.
$courseid = required_param('id', PARAM_INT);

// Set the course object from the courseid.
$course = course_get_format($courseid)->get_course();

// Get the course category.
$category = $DB->get_record(
    'course_categories', array('id' => $course->category), '*', MUST_EXIST
);

// Set up the page.
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/course/edit.php', array('id' => $courseid));

// Ensure the user is logged in and has access to the course in question.
require_login($course);

// Set the context for the course in question.
$context = context_course::instance($courseid);

// Ensure the user can modify the course settings.
require_capability('moodle/course:update', $context);

// Stolen from course/edit.php.
$returnto = 'url';
if (!empty($courseid)) {
    $returnurl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $courseid));
} else {
    $returnurl = new moodle_url($CFG->wwwroot . '/course/');
}

$editoroptions = array(
    'maxfiles' => EDITOR_UNLIMITED_FILES,
    'maxbytes' => $CFG->maxbytes,
    'trusttext' => false,
    'noclean' => true,
    'context' => $context
);

$course = file_prepare_standard_editor(
    $course, 'summary', $editoroptions,
    $context, 'course', 'summary', 0
);

$form = new ues_course_edit_form(null, array(
    'course' => $course,
    'category' => $category,
    'editoroptions' => $editoroptions,
    'returnto' => $returnto,
    'returnurl' => $returnurl,
    'lang' => $CFG->lang
));

$return = new moodle_url('/course/view.php', array('id' => $courseid));

if ($form->is_cancelled()) {
    redirect($return);
} else if ($data = $form->get_data()) {
    update_course($data, $editoroptions);
    rebuild_course_cache($courseid);
    redirect($return);
}

$streditsettings = get_string('editcoursesettings');
$PAGE->navbar->add($streditsettings);
$PAGE->set_title($streditsettings);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($streditsettings);

$form->display();

echo $OUTPUT->footer();
