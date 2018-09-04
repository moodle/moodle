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
 * Prints the intro page particular instance of a hotpot
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/lib/completionlib.php');
require_once(dirname(__FILE__).'/locallib.php');

$id  = optional_param('id', 0, PARAM_INT); // course_module ID, or
$hp  = optional_param('hp', 0, PARAM_INT); // hotpot instance ID

if ($id) {
    $cm      = get_coursemodule_from_id('hotpot', $id, 0, false, MUST_EXIST);
    $course  = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $hotpot  = $DB->get_record('hotpot', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $hotpot  = $DB->get_record('hotpot', array('id' => $hp), '*', MUST_EXIST);
    $course  = $DB->get_record('course', array('id' => $hotpot->course), '*', MUST_EXIST);
    $cm      = get_coursemodule_from_instance('hotpot', $hotpot->id, $course->id, false, MUST_EXIST);
}

// Check login
require_login($course, true, $cm);
require_capability('mod/hotpot:view', $PAGE->context);

// Log this request
hotpot_add_to_log($course->id, 'hotpot', 'view', 'view.php?id='.$cm->id, $hotpot->id, $cm->id);

// Create an object to represent the current HotPot activity
$hotpot = hotpot::create($hotpot, $cm, $course, $PAGE->context);

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

if (empty($hotpot->entrypage)) {
    // go straight to attempt.php
    redirect($hotpot->attempt_url());
}

// delete attempts, if requested
$action    = optional_param('action', '', PARAM_ALPHA);
$confirmed = optional_param('confirmed', 0, PARAM_INT);
if (function_exists('optional_param_array')) {
    $selected  = optional_param_array('selected', 0, PARAM_INT);
} else {
    $selected  = optional_param('selected', 0, PARAM_INT);
}
if ($action=='deleteselected') {
    require_sesskey();
    if ($confirmed) {
        $hotpot->delete_attempts($selected);
        $hotpot->update_completion_state($completion);
    } else {
        // show a confirm button ?
    }
}

// Set editing mode
if ($PAGE->user_allowed_editing()) {
    hotpot::set_user_editing();
}

// initialize $PAGE (and compute blocks)
$PAGE->set_url($hotpot->view_url());
$PAGE->set_title($hotpot->name);
$PAGE->set_heading($course->fullname);

$output = $PAGE->get_renderer('mod_hotpot');

////////////////////////////////////////////////////////////////////////////////
// Output starts here                                                         //
////////////////////////////////////////////////////////////////////////////////

echo $output->header();

// Guests can't do a HotPot, so offer them a choice of logging in or going back.
if (isguestuser()) {
    if (function_exists('get_local_referer')) {
        // Moodle >= 2.8
        $referer = get_local_referer(false);
    } else {
        // Moodle <= 2.7
        $referer = get_referer(false);
    }
    $message = html_writer::tag('p', get_string('guestsno', 'quiz'));
    $message .= html_writer::tag('p', get_string('liketologin'));
    echo $output->confirm($message, get_login_url(), $referer);
    echo $output->footer();
    exit;
}

// If user is not enrolled in this course in a good enough role, show a link to course enrolment page.
if (! ($hotpot->can_attempt() || $hotpot->can_preview())) {
    $message = html_writer::tag('p', get_string('youneedtoenrol', 'quiz'));
    $message .= html_writer::tag('p', $output->continue_button($hotpot->course_url()));
    echo $output->box($message, 'generalbox', 'notice');
    echo $output->footer();
    exit;
}

// Print quiz name and description
echo $output->heading($hotpot);

// show entry page text, if required
echo $output->description_box($hotpot, 'entry');

// show entry page options, if required
echo $output->entryoptions($hotpot);

// show entry page warnings, if any
echo $output->entrywarnings($hotpot);

// show view/review/continue button
echo $output->view_attempt_button($hotpot);

echo $output->footer();
