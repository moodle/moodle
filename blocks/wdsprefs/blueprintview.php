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
 * Blueprint Shells interface for faculty to create blueprint shells for courses
 * 
 * @package    block_wdsprefs
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Include required Moodle core.
require('../../config.php');

// Get the main wdsprefs class.
require_once("$CFG->dirroot/blocks/wdsprefs/classes/wdsprefs.php");

// Include the form for blueprint selection.
require_once("$CFG->dirroot/blocks/wdsprefs/blueprint_form.php");

// Require user to be logged in.
require_login();

// Get system context for permissions.
$context = context_system::instance();

// Set up page URL
$url = new moodle_url('/blocks/wdsprefs/blueprintview.php');

// Set up the page.
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('wdsprefs:blueprinttitle', 'block_wdsprefs'));
$PAGE->set_heading(get_string('wdsprefs:blueprintheading', 'block_wdsprefs'));

// Add breadcrumbs.
$PAGE->navbar->add(
    get_string('home'),
    new moodle_url('/')
);
$PAGE->navbar->add(
    get_string('wdsprefs:blueprint', 'block_wdsprefs'),
    new moodle_url('/blocks/wdsprefs/blueprintview.php')
);

// Set page layout.
$PAGE->set_pagelayout('base');

// Load required CSS.
$PAGE->requires->css('/blocks/wdsprefs/styles.css');

// Output the header.
echo $OUTPUT->header();

// Check if the user is an instructor.
$isinstructor = true; //!is_siteadmin() ? wdsprefs::get_instructor($USER) : true;

if (!$isinstructor) {
    echo $OUTPUT->notification(get_string('wdsprefs:noinstructor', 'block_wdsprefs'), 'notifyproblem');
    echo $OUTPUT->footer();
    exit;
}

// User id.
$uid = $USER->id;

// Get all taught courses.
$taughtcourses = wdsprefs::get_instructor_courses($uid);

// Get existing blueprint shells for this user.
$existingblueprints = wdsprefs::get_user_blueprints($uid);

// First display a table of existing blueprint shells.
if (!empty($existingblueprints)) {
    echo html_writer::tag('h3', get_string('wdsprefs:existingblueprints', 'block_wdsprefs'));

    $table = new html_table();
    $table->head = array(
        get_string('wdsprefs:course', 'block_wdsprefs'),
        get_string('wdsprefs:status', 'block_wdsprefs'),
        get_string('wdsprefs:datecreated', 'block_wdsprefs'),
        get_string('wdsprefs:actions', 'block_wdsprefs')
    );

    foreach ($existingblueprints as $blueprint) {
        $courseinfo = wdsprefs::get_course_info_by_definition_id($blueprint->course_definition_id);
        $coursename = $courseinfo->course_subject_abbreviation . ' ' . $courseinfo->course_number;
        
        $row = array();
        $row[] = $coursename;
        $row[] = get_string('wdsprefs:blueprintstatus_' . $blueprint->status, 'block_wdsprefs');
        $row[] = userdate($blueprint->timecreated);
        
        // Action buttons.
        $actions = '';
        if ($blueprint->moodle_course_id) {
            $courseurl = new moodle_url('/course/view.php', array('id' => $blueprint->moodle_course_id));
            $actions .= html_writer::link($courseurl, get_string('wdsprefs:viewcourse', 'block_wdsprefs'), 
                            array('class' => 'btn btn-sm btn-primary', 'target' => '_blank'));
        }
        $row[] = $actions;
        
        $table->data[] = $row;
    }

    echo html_writer::table($table);
}

// Display the form to request new blueprint shells.
echo html_writer::tag('h3', get_string('wdsprefs:requestblueprint', 'block_wdsprefs'));

if (empty($taughtcourses)) {
    echo $OUTPUT->notification(get_string('wdsprefs:nocourses', 'block_wdsprefs'), 'notifyinfo');
    echo $OUTPUT->footer();
    exit;
}

// Display form to select a course for blueprint creation.
$form = new blueprint_form(null, array('courses' => $taughtcourses));

// Process form submission.
if ($form->is_cancelled()) {
    redirect(new moodle_url('/'));
} else if ($data = $form->get_data()) {

    // Process the form data.
    $result = wdsprefs::create_blueprint_shell($uid, $data->course_definition_id);

    if ($result) {
        redirect(
            new moodle_url('/blocks/wdsprefs/blueprintview.php'),
            get_string('wdsprefs:blueprintsuccess', 'block_wdsprefs'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        redirect(
            new moodle_url('/blocks/wdsprefs/blueprintview.php'),
            get_string('wdsprefs:blueprintfailed', 'block_wdsprefs'),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
} else {

    // Display the form.
    $form->display();
}

// Output the footer.
echo $OUTPUT->footer();
