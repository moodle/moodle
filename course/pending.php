<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Allow the administrator to look through a list of course requests and approve or reject them.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package course
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/request_form.php');

require_login();
require_capability('moodle/site:approvecourse', context_system::instance());

$approve = optional_param('approve', 0, PARAM_INT);
$reject = optional_param('reject', 0, PARAM_INT);

$baseurl = $CFG->wwwroot . '/course/pending.php';
admin_externalpage_setup('coursespending');

/// Process approval of a course.
if (!empty($approve) and confirm_sesskey()) {
    /// Load the request.
    $course = new course_request($approve);
    $courseid = $course->approve();

    if ($courseid !== false) {
        redirect(new moodle_url('/course/edit.php', ['id' => $courseid, 'returnto' => 'pending']));
    } else {
        print_error('courseapprovedfailed');
    }
}

/// Process rejection of a course.
if (!empty($reject)) {
    // Load the request.
    $course = new course_request($reject);

    // Prepare the form.
    $rejectform = new reject_request_form($baseurl);
    $default = new stdClass();
    $default->reject = $course->id;
    $rejectform->set_data($default);

/// Standard form processing if statement.
    if ($rejectform->is_cancelled()){
        redirect($baseurl);

    } else if ($data = $rejectform->get_data()) {

        /// Reject the request
        $course->reject($data->rejectnotice);

        /// Redirect back to the course listing.
        redirect($baseurl, get_string('courserejected'));
    }

/// Display the form for giving a reason for rejecting the request.
    echo $OUTPUT->header($rejectform->focus());
    $rejectform->display();
    echo $OUTPUT->footer();
    exit;
}

/// Print a list of all the pending requests.
echo $OUTPUT->header();

$pending = $DB->get_records('course_request');
if (empty($pending)) {
    echo $OUTPUT->heading(get_string('nopendingcourses'));
} else {
    echo $OUTPUT->heading(get_string('coursespending'));
    $role = $DB->get_record('role', array('id' => $CFG->creatornewroleid), '*', MUST_EXIST);
    echo $OUTPUT->notification(get_string('courserequestwarning', 'core', role_get_name($role)), 'notifyproblem');

/// Build a table of all the requests.
    $table = new html_table();
    $table->attributes['class'] = 'pendingcourserequests generaltable';
    $table->align = array('center', 'center', 'center', 'center', 'center', 'center');
    $table->head = array(get_string('shortnamecourse'), get_string('fullnamecourse'), get_string('requestedby'),
            get_string('summary'), get_string('category'), get_string('requestreason'), get_string('action'));

    foreach ($pending as $course) {
        $course = new course_request($course);

        // Check here for shortname collisions and warn about them.
        $course->check_shortname_collision();

        $category = $course->get_category();

        $row = array();
        $row[] = format_string($course->shortname);
        $row[] = format_string($course->fullname);
        $row[] = fullname($course->get_requester());
        $row[] = format_text($course->summary, $course->summaryformat);
        $row[] = $category->get_formatted_name();
        $row[] = format_string($course->reason);
        $row[] = $OUTPUT->single_button(new moodle_url($baseurl, array('approve' => $course->id, 'sesskey' => sesskey())), get_string('approve'), 'get') .
                 $OUTPUT->single_button(new moodle_url($baseurl, array('reject' => $course->id)), get_string('rejectdots'), 'get');

    /// Add the row to the table.
        $table->data[] = $row;
    }

/// Display the table.
    echo html_writer::table($table);

/// Message about name collisions, if necessary.
    if (!empty($collision)) {
        print_string('shortnamecollisionwarning');
    }
}

/// Finish off the page.
echo $OUTPUT->single_button($CFG->wwwroot . '/course/index.php', get_string('backtocourselisting'));
echo $OUTPUT->footer();
