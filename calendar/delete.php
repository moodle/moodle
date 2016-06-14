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
 * This file is part of the Calendar section Moodle
 * It is responsible for deleting a calendar entry + optionally its repeats
 *
 * @copyright 2009 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package calendar
 */

require_once('../config.php');
require_once($CFG->dirroot.'/calendar/event_form.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/calendar/renderer.php');

$eventid = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);
$repeats = optional_param('repeats', false, PARAM_BOOL);
$courseid = optional_param('course', 0, PARAM_INT);

$PAGE->set_url('/calendar/delete.php', array('id'=>$eventid));

if(!$site = get_site()) {
    redirect(new moodle_url('/admin/index.php'));
}

$event = calendar_event::load($eventid);

/**
 * We are going to be picky here, and require that any event types other than
 * group and site be associated with a course. This means any code that is using
 * custom event types (and there are a few) will need to associate thier event with
 * a course
 */
if ($event->eventtype !== 'user' && $event->eventtype !== 'site') {
    $courseid = $event->courseid;
}
$course = $DB->get_record('course', array('id'=>$courseid));
require_login($course);
if (!$course) {
    $PAGE->set_context(context_system::instance()); //TODO: wrong
}

// Check the user has the required capabilities to edit an event
if (!calendar_edit_event_allowed($event)) {
    print_error('nopermissions');
}

// Count the repeats, do we need to consider the possibility of deleting repeats
$event->timedurationuntil = $event->timestart + $event->timeduration;
$event->count_repeats();

// Is used several times, and sometimes with modification if required
$viewcalendarurl = new moodle_url(CALENDAR_URL.'view.php', array('view'=>'upcoming'));
$viewcalendarurl->param('time', $event->timestart, '%Y');

// If confirm is set (PARAM_BOOL) then we have confirmation of initention to delete
if ($confirm) {
    // Confirm the session key to stop CSRF
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad');
    }
    // Delete the event and possibly repeats
    $event->delete($repeats);
    // If the event has an associated course then we need to include it in the redirect link
    if (!empty($event->courseid) && $event->courseid > 0) {
        $viewcalendarurl->param('course', $event->courseid);
    }
    // And redirect
    redirect($viewcalendarurl);
}

// Prepare the page to show the confirmation form
$title = get_string('deleteevent', 'calendar');
$strcalendar = get_string('calendar', 'calendar');

$PAGE->navbar->add($strcalendar, $viewcalendarurl);
$PAGE->navbar->add($title);
$PAGE->set_title($site->shortname.': '.$strcalendar.': '.$title);
$PAGE->set_heading($COURSE->fullname);
echo $OUTPUT->header();
echo $OUTPUT->box_start('eventlist');

// Delete this event button is always shown
$url = new moodle_url(CALENDAR_URL.'delete.php', array('id'=>$event->id, 'confirm'=>true));
$buttons = $OUTPUT->single_button($url, get_string('delete'));

// And add the cancel button.
$buttons .= $OUTPUT->single_button($viewcalendarurl, get_string('cancel'));

// And show the buttons and notes.
echo $OUTPUT->box_start('generalbox', 'notice');
echo html_writer::tag('p', get_string('confirmeventdelete', 'calendar', format_string($event->name)));

// If there are repeated events then add a Delete Repeated button.
if (!empty($event->eventrepeats) && $event->eventrepeats > 0) {
    $url = new moodle_url(CALENDAR_URL.'delete.php', array('id'=>$event->repeatid, 'confirm'=>true, 'repeats'=>true));
    $buttons .= $OUTPUT->single_button($url, get_string('deleteall'));
    echo html_writer::tag('p', get_string('youcandeleteallrepeats', 'calendar', $event->eventrepeats));
}

echo $OUTPUT->box($buttons, 'buttons');
echo $OUTPUT->box_end();

echo $OUTPUT->box_end();
echo $OUTPUT->footer();
