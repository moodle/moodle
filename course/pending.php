<?php  // $Id$

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
 *//** */

    require_once(dirname(__FILE__) . '/../config.php');
    require_once($CFG->libdir . '/adminlib.php');
    require_once($CFG->dirroot . '/course/lib.php');
    require_once($CFG->dirroot . '/course/request_form.php');

    require_login();
    require_capability('moodle/site:approvecourse', get_context_instance(CONTEXT_SYSTEM));

    $approve = optional_param('approve', 0, PARAM_INT);
    $reject = optional_param('reject', 0, PARAM_INT);

    $baseurl = $CFG->wwwroot . '/course/pending.php';
    admin_externalpage_setup('coursespending');

/// Process approval of a course.
    if (!empty($approve) and confirm_sesskey()) {
    /// Load the request.
        if (!$course = $DB->get_record('course_request', array('id' => $approve))) {
            print_error('unknowncourserequest');
        }

    /// Get the category courses are added to.
        $category = get_course_category($CFG->defaultrequestcategory);

    /// Build up a course record based on the request.
        $course->category = $category->id;
        $course->sortorder = $category->sortorder; // place as the first in category 
        $course->requested = 1;
        unset($course->reason);
        unset($course->id);
        $teacherid = $course->requester;
        unset($course->requester);
        if (!empty($CFG->restrictmodulesfor) && $CFG->restrictmodulesfor != 'none' && !empty($CFG->restrictbydefault)) {
            $course->restrictmodules = 1;
        }

    /// Insert the record.
        if ($courseid = $DB->insert_record('course', $course)) {
            $course = $DB->get_record('course', array('id' => $courseid));
            blocks_add_default_course_blocks($course);
            $context = get_context_instance(CONTEXT_COURSE, $courseid);
            role_assign($CFG->creatornewroleid, $teacherid, 0, $context->id); // assing teacher role
            if (!empty($CFG->restrictmodulesfor) && $CFG->restrictmodulesfor != 'none' && !empty($CFG->restrictbydefault)) {
                // if we're all or requested we're ok.
                $allowedmods = explode(',',$CFG->defaultallowedmodules);
                update_restricted_mods($course,$allowedmods);
            }
            $DB->delete_records('course_request', array('id'=>$approve));
            $success = 1;
            fix_course_sortorder();
        }
        if (!empty($success)) {
            $user = $DB->get_record('user', array('id' => $teacherid));
            $a->name = $course->fullname;
            $a->url = $CFG->wwwroot.'/course/view.php?id=' . $courseid;
            $eventdata = new object();
            $eventdata->modulename        = 'moodle';
            $eventdata->component         = 'course';
            $eventdata->name              = 'courserequestapproved';
            $eventdata->userfrom          = $USER;
            $eventdata->userto            = $user;
            $eventdata->subject           = get_string('courseapprovedsubject');
            $eventdata->fullmessage       = get_string('courseapprovedemail2', 'moodle', $a);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';
            events_trigger('message_send', $eventdata);

            redirect($CFG->wwwroot.'/course/edit.php?id=' . $courseid);
        } else {
            print_error('courseapprovedfailed');
        }
    }

/// Process rejection of a course.
    if (!empty($reject)) {
    /// Load the request.
        if (!$course = $DB->get_record('course_request', array('id' => $reject))) {
            print_error('unknowncourserequest');
        }

    /// Prepare the form.
        $rejectform = new reject_request_form($baseurl);
        $default = new stdClass();
        $default->reject = $reject;
        $rejectform->set_data($default);

    /// Standard form processing if statement.
        if ($rejectform->is_cancelled()){
            redirect($baseurl);
    
        } else if ($data = $rejectform->get_data()) {
        /// Send an email to the requester.
            $user = $DB->get_record('user', array('id' => $course->requester));
            $eventdata = new object();
            $eventdata->modulename        = 'moodle';
            $eventdata->component         = 'course';
            $eventdata->name              = 'courserequestrejected';
            $eventdata->userfrom          = $USER;
            $eventdata->userto            = $user;
            $eventdata->subject           = get_string('courserejectsubject');
            $eventdata->fullmessage       = get_string('courserejectemail', 'moodle', $data->rejectnotice);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';
            events_trigger('message_send', $eventdata);

        /// Delete the request
            $DB->delete_records('course_request', array('id' => $course->id));

        /// Redirect back to the course listing.
            redirect($baseurl, get_string('courserejected'));
        }

    /// Display the form for giving a reason for rejecting the request.
        admin_externalpage_print_header($rejectform->focus());
        $rejectform->display();
        echo $OUTPUT->footer();
        exit;
    }

/// Print a list of all the pending requests.
    admin_externalpage_print_header();

    $pending = $DB->get_records('course_request');
    if (empty($pending)) {
        echo $OUTPUT->heading(get_string('nopendingcourses'));
    } else {
        echo $OUTPUT->heading(get_string('coursespending'));

    /// Build a table of all the requests.
        $table->class = 'pendingcourserequests generaltable';
        $table->align = array('center', 'center', 'center', 'center', 'center', 'center', 'center');
        $table->head = array('&nbsp;', get_string('shortname'), get_string('fullname'),
                get_string('requestedby'), get_string('summary'), get_string('requestreason'), get_string('action'));
        $strrequireskey = get_string('requireskey');

    /// Loop over requested courses.
        foreach ($pending as $course) {
            $requester = $DB->get_record('user', array('id'=>$course->requester));
            $row = array();

        /// Check here for shortname collisions and warn about them.
            if ($DB->record_exists('course', array('shortname' => $course->shortname))) {
                $course->shortname .= ' [*]';
                $collision = 1;
            }

        /// Show an enrolment key icon in the first column if applicable.
            if (!empty($course->password)) {
                $row[] = '<img hspace="1" alt="'.$strrequireskey.'" class="icon" src="'.$OUTPUT->old_icon_url('i/key') . '" />';
            } else {
                $row[] = '';
            }

        /// Info in the other columns.
            $row[] = format_string($course->shortname);
            $row[] = format_string($course->fullname);
            $row[] = fullname($requester);
            $row[] = format_string($course->summary);
            $row[] = format_string($course->reason);
            $row[] = print_single_button($baseurl, array('approve' => $course->id, 'sesskey' => sesskey()), get_string('approve'), 'get', '', true) .
                    print_single_button($baseurl, array('reject' => $course->id), get_string('rejectdots'), 'get', '', true);

        /// Add the row to the table.
            $table->data[] = $row;
        }

    /// Display the table.
        print_table($table);

    /// Message about name collisions, if necessary.
        if (!empty($collision)) {
            print_string('shortnamecollisionwarning');
        }
    }

/// Finish off the page.
    print_single_button($CFG->wwwroot . '/course/index.php', array(), get_string('backtocourselisting'));
    echo $OUTPUT->footer();
?>
