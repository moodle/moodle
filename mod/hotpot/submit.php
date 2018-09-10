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
require_once(dirname(__FILE__).'/locallib.php');

$id       = optional_param('id', 0, PARAM_INT); // hotpot_attempts id
$attempt  = $DB->get_record('hotpot_attempts', array('id' => $id), '*', MUST_EXIST);
$hotpot   = $DB->get_record('hotpot', array('id' => $attempt->hotpotid), '*', MUST_EXIST);
$course   = $DB->get_record('course', array('id' => $hotpot->course), '*', MUST_EXIST);
$cm       = get_coursemodule_from_instance('hotpot', $hotpot->id, $course->id, false, MUST_EXIST);

// Check login
require_login($course, true, $cm);
require_capability('mod/hotpot:attempt', $PAGE->context);

// Create an object to represent this attempt at the current HotPot activity
$hotpot = hotpot::create($hotpot, $cm, $course, $PAGE->context, $attempt);

// Log this request
hotpot_add_to_log($course->id, 'hotpot', 'submit', 'view.php?id='.$cm->id, $hotpot->id, $cm->id);

// Set editing mode
if ($PAGE->user_allowed_editing()) {
    hotpot::set_user_editing();
}

// initialize $PAGE (and compute blocks)
$PAGE->set_url($hotpot->submit_url());
$PAGE->set_title($hotpot->name);
$PAGE->set_heading($course->fullname);

// get renderer subtype (e.g. attempt_hp_6_jcloze_xml)
// and load the appropriate storage class for this attempt
$subtype = $hotpot->get_attempt_renderer_subtype();
$subdir = str_replace('_', '/', $subtype);
require_once($CFG->dirroot.'/mod/hotpot/'.$subdir.'/storage.php');
require_once($CFG->dirroot.'/mod/hotpot/'.$subdir.'/renderer.php');

// create the renderer for this attempt
$output = $PAGE->get_renderer('mod_hotpot', $subtype);

// Guests can't do a HotPot, so offer them a choice of logging in or going back.
if (isguestuser()) {
    echo $output->header();
    if (function_exists('get_local_referer')) {
        // Moodle >= 2.8
        $referer = get_local_referer(false);
    } else {
        // Moodle <= 2.7
        $referer = get_referer(false);
    }
    $message = html_writer::tag('p', get_string('guestsno', 'quiz')).
               html_writer::tag('p', get_string('liketologin'));
    echo $output->confirm($message, get_login_url(), $referer);
    echo $output->footer();
    exit;
}

// If user is not enrolled in this course in a good enough role, show a link to course enrolment page.
if (! ($hotpot->can_attempt() || $hotpot->can_preview())) {
    echo $output->header();
    $message = html_writer::tag('p', get_string('youneedtoenrol', 'quiz')).
               html_writer::tag('p', $output->continue_button($hotpot->course_url()));
    echo $output->box($message, 'generalbox', 'notice');
    echo $output->footer();
    exit;
}

// store the results (use call_user_func to prevent syntax errors in PHP 5.2.x)
$class = 'mod_hotpot_'.$subtype.'_storage';
$storage = call_user_func(array($class, 'store'), $hotpot);

$completion = new completion_info($course);
$hotpot->update_completion_state($completion);

// redirect the browser (only if necessary)
if ($response = $output->require_response($hotpot)) {
    $output->send_response($response); // script stops here
}

// print access warnings, if required
if ($warnings = $output->entrywarnings($hotpot)) {
    echo $output->header();
    echo $warnings;
    echo $output->footer();
    exit;
}

////////////////////////////////////////////////////////////////////////////////
// Output starts here                                                         //
////////////////////////////////////////////////////////////////////////////////

echo $output->header();

// Print quiz name and description
echo $output->heading($hotpot);

// show exit page
echo $output->exitfeedback($hotpot);

echo $output->description_box($hotpot, 'exit');

echo $output->exitlinks($hotpot);

echo $output->continue_button($hotpot->course_url());

echo $output->footer();
