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
 * Depending on the current enrolment method, this page
 * presents the user with whatever they need to know when
 * they try to enrol in a course.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once("../config.php");
require_once("lib.php");
require_once("$CFG->dirroot/enrol/enrol.class.php");

$id           = required_param('id', PARAM_INT);
$loginasguest = optional_param('loginasguest', 0, PARAM_BOOL); // hmm, is this still needed?

$url = new moodle_url('/course/enrol.php', array('id'=>$id));
if ($loginasguest !== 0) {
    $url->param('loginasguest', $loginasguest);
}
$PAGE->set_url($url);

if (!isloggedin() or isguestuser()) {
    // do not use require_login here because we are usually comming from it
    redirect(get_login_url());
}

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error("That's an invalid course id");
}

if (! $context = get_context_instance(CONTEXT_COURSE, $course->id) ) {
    print_error("That's an invalid course id");
}

/// do not use when in course login as
if (session_is_loggedinas() and $USER->loginascontext->contextlevel == CONTEXT_COURSE) {
    print_error('loginasnoenrol', '', $CFG->wwwroot.'/course/view.php?id='.$USER->loginascontext->instanceid);
}

$enrol = enrolment_factory::factory($course->enrol); // do not use if (!$enrol... here, it can not work in PHP4 - see MDL-7529

/// Refreshing all current role assignments for the current user

load_all_capabilities();

/// Double check just in case they are actually enrolled already and
/// thus got to this script by mistake.  This might occur if enrolments
/// changed during this session or something

if (has_capability('moodle/course:participate', $context)) {
    if (!empty($SESSION->wantsurl)) {
        $destination = $SESSION->wantsurl;
        unset($SESSION->wantsurl);
    } else {
        $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
    }
    redirect($destination);   // Bye!
}

/// Check if the course is a meta course  (bug 5734)
if ($course->metacourse) {
    echo $OUTPUT->header();
    notice(get_string('coursenotaccessible'), "$CFG->wwwroot/index.php");
}

/// Users can't enroll to site course
if ($course->id == SITEID) {
    echo $OUTPUT->header();
    notice(get_string('enrollfirst'), "$CFG->wwwroot/index.php");
}

/// Double check just in case they are enrolled to start in the future

if ($course->enrolperiod) {   // Only active if the course has an enrolment period in effect
    if ($roles = get_user_roles($context, $USER->id)) {
        foreach ($roles as $role) {
            if ($role->timestart and ($role->timestart >= time())) {
                $message = get_string('enrolmentnotyet', '', userdate($student->timestart));
                echo $OUTPUT->header();
                notice($message, "$CFG->wwwroot/index.php");
            }
        }
    }
}

/// Check if the course is enrollable
if (!method_exists($enrol, 'print_entry')) {
    echo $OUTPUT->header();
    notice(get_string('enrolmentnointernal'), "$CFG->wwwroot/index.php");
}

if (!$course->enrollable ||
        ($course->enrollable == 2 && $course->enrolstartdate > 0 && $course->enrolstartdate > time()) ||
        ($course->enrollable == 2 && $course->enrolenddate > 0 && $course->enrolenddate <= time())
        ) {
    $PAGE->set_title($course->shortname);
    $PAGE->set_heading($course->fullname);
    $PAGE->navbar->add($course->shortname);
    echo $OUTPUT->header();
    notice(get_string('notenrollable'), "$CFG->wwwroot/index.php");
}

/// Check the submitted enrolment information if there is any (eg could be enrolment key)

if ($form = data_submitted()) {
    $enrol->check_entry($form, $course);   // Should terminate/redirect in here if it's all OK
}

/// Otherwise, we print the entry form.

$enrol->print_entry($course);

/// Easy!

