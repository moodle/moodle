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

require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/ues/publiclib.php');

// Get the required Data Access Objects.
ues::require_daos();

// Make sure the user is logged in.
require_login();

// Check to be sure the site admin is using this page, if not redirect to /my.
if (!is_siteadmin($USER->id)) {
    redirect(new moodle_url('/my'));
}

// Set the semesterid variable.
$semesterid = optional_param('id', null, PARAM_INT);

// Simpler strings.
$s = ues::gen_str();

// Set the blockname.
$blockname = $s('pluginname');

// Set the action.
$action = $s('semester_cleanup');

// Set the baseurl for later.
$baseurl = new moodle_url('/admin/settings.php', array(
    'section' => 'enrolsettingsues'
));

// Set up the page.
$PAGE->set_context(context_system::instance());
$PAGE->set_title($blockname. ': '. $action);
$PAGE->set_heading($blockname. ': '. $action);
$PAGE->set_url('/enrol/ues/cleanup.php');
$PAGE->set_pagetype('admin-settings-ues-semester-cleanup');
$PAGE->set_pagelayout('admin');

// Set the navbar up.
$PAGE->navbar->add($blockname, $baseurl);
$PAGE->navbar->add($action);

// Begin the page output.
echo $OUTPUT->header();
echo $OUTPUT->heading($action);

// Ensure we have a semester id.
if ($semesterid) {
    // Get the semester data based on the supplied id.
    $semesterparam = array('id' => $semesterid);
    $semester = ues_semester::get($semesterparam, true); // LSU Enhancement include ignore flag

    // Set the URL base.
    $base = '/enrol/ues/cleanup.php';

    // If we don't have a semester, error out.
    if (empty($semester)) {
        print_error('no_semester', 'enrol_ues');
    }

    // If the user wants to delete a semester, do it.
    if (data_submitted()) {
        // Report the drop.
        echo $OUTPUT->box_start();
        echo html_writer::start_tag('pre');
        // Delete the semester.
        ues::drop_semester($semester, true);
        echo html_writer::end_tag('pre');
        echo $OUTPUT->box_end();
        echo $OUTPUT->continue_button(new moodle_url($base));
    } else {
        // Just show the continue and cancel links.
        $continue = new moodle_url($base, $semesterparam);
        $cancel = new moodle_url($base);
        echo $OUTPUT->confirm($s('drop_semester', $semester), $continue, $cancel);
    }
    // Output the page footer.
    echo $OUTPUT->footer();
    die();
}

// Get all semesters.
$semesters = ues_semester::get_all();

// Get the in-session semesters.
$insession = ues_semester::in_session();

// Make sure we have semesters to work with.
if (empty($semesters)) {
    echo $OUTPUT->box_start();
    echo $OUTPUT->notification($s('no_semesters'));
    echo $OUTPUT->box_end();
    echo $OUTPUT->continue_button($baseurl);
    echo $OUTPUT->footer();
    die();
}

// Set up the table.
$table = new html_table();

// Build the table and populate the headings.
$table->head = array(
    $s('year'), get_string('name'), $s('campus'), $s('session_key'),
    $s('sections'), $s('in_session'), get_string('action')
);

// Set up the data.
$table->data = array();

// Build the remove link and icon.
$makeremovelink = function($semester) use ($OUTPUT, $s) {
    $removeicon = $OUTPUT->pix_icon('i/cross_red_big', $s('drop_semester', $semester));
    $url = new moodle_url('/enrol/ues/cleanup.php', array('id' => $semester->id));
    return html_writer::link($url, $removeicon);
};

// Loop through the semesters and populate the table.
foreach ($semesters as $semester) {
    $line = array(
        $semester->year,
        $semester->name,
        $semester->campus,
        $semester->session_key,
        ues_section::count(array('semesterid' => $semester->id)),
        isset($insession[$semester->id]) ? 'Y' : 'N',
        $makeremovelink($semester)
    );
    $table->data[] = new html_table_row($line);
}

// Write the table to screen.
echo html_writer::table($table);

// Finish outputting the page.
echo $OUTPUT->footer();
