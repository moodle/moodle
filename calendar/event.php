<?php

/////////////////////////////////////////////////////////////////////////////
//                                                                         //
// NOTICE OF COPYRIGHT                                                     //
//                                                                         //
// Moodle - Calendar extension                                             //
//                                                                         //
// Copyright (C) 2003-2004  Greek School Network            www.sch.gr     //
//                                                                         //
// Designed by:                                                            //
//     Avgoustos Tsinakos (tsinakos@teikav.edu.gr)                         //
//     Jon Papaioannou (pj@moodle.org)                                     //
//                                                                         //
// Programming and development:                                            //
//     Jon Papaioannou (pj@moodle.org)                                     //
//                                                                         //
// For bugs, suggestions, etc contact:                                     //
//     Jon Papaioannou (pj@moodle.org)                                     //
//                                                                         //
// The current module was developed at the University of Macedonia         //
// (www.uom.gr) under the funding of the Greek School Network (www.sch.gr) //
// The aim of this project is to provide additional and improved           //
// functionality to the Asynchronous Distance Education service that the   //
// Greek School Network deploys.                                           //
//                                                                         //
// This program is free software; you can redistribute it and/or modify    //
// it under the terms of the GNU General Public License as published by    //
// the Free Software Foundation; either version 2 of the License, or       //
// (at your option) any later version.                                     //
//                                                                         //
// This program is distributed in the hope that it will be useful,         //
// but WITHOUT ANY WARRANTY; without even the implied warranty of          //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           //
// GNU General Public License for more details:                            //
//                                                                         //
//          http://www.gnu.org/copyleft/gpl.html                           //
//                                                                         //
/////////////////////////////////////////////////////////////////////////////

/**
 * This file is part of the Calendar section Moodle
 *
 * @copyright 2003-2004 Jon Papaioannou (pj@moodle.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 * @package calendar
 */

require_once('../config.php');
require_once($CFG->dirroot.'/calendar/event_form.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->dirroot.'/course/lib.php');

require_login();

$action = optional_param('action', 'new', PARAM_ALPHA);
$eventid = optional_param('id', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$cal_y = optional_param('cal_y', 0, PARAM_INT);
$cal_m = optional_param('cal_m', 0, PARAM_INT);
$cal_d = optional_param('cal_d', 0, PARAM_INT);

if ($courseid === 0) {
    $courseid = optional_param('course', 0, PARAM_INT);
}

$url = new moodle_url('/calendar/event.php', array('action'=>$action));
if ($eventid !== 0) $url->param('id', $eventid);
if ($courseid !== 0) $url->param('course', $courseid);
if ($cal_y !== 0) $url->param('cal_y', $cal_y);
if ($cal_m !== 0) $url->param('cal_m', $cal_m);
if ($cal_d !== 0) $url->param('cal_d', $cal_d);
$PAGE->set_url($url);
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
$PAGE->set_pagelayout('standard');

if ($action === 'delete' && $eventid>0) {
    $deleteurl = new moodle_url('/calendar/delete.php', array('id'=>$eventid));
    if ($courseid > 0) {
        $deleteurl->param('course', $courseid);
    }
    redirect($deleteurl);
}

$viewcalendarurl = new moodle_url(CALENDAR_URL.'view.php');
$viewcalendarurl->params($PAGE->url->params());
$viewcalendarurl->remove_params(array('id','action'));

$now = usergetdate(time());

if (isguestuser()) {
    // Guests cannot do anything with events
    redirect(new moodle_url(CALENDAR_URL.'view.php', array('view'=>'upcoming', 'course'=>$courseid)));
}

$focus = '';

$site = get_site();

calendar_session_vars();

// If a course has been supplied in the URL, change the filters to show that one
$courseexists = false;
if ($courseid > 0) {
    if ($courseid == SITEID) {
        // If coming from the site page, show all courses
        $SESSION->cal_courses_shown = calendar_get_default_courses(true);
        calendar_set_referring_course(0);
    } else if ($DB->record_exists('course', array('id'=>$courseid))) {
        $courseexists = true;
        // Otherwise show just this one
        $SESSION->cal_courses_shown = $courseid;
        calendar_set_referring_course($SESSION->cal_courses_shown);
    }
}

if (!empty($SESSION->cal_course_referer)) {
    // TODO: This is part of the Great $course Hack in Moodle. Replace it at some point.
    $course = $DB->get_record('course', array('id'=>$SESSION->cal_course_referer));
} else {
    $course = $site;
}

require_login($course, false);

$calendar = new calendar_information($cal_d, $cal_m, $cal_y);
$calendar->courseid = $courseid;

$strcalendar = get_string('calendar', 'calendar');
$link = clone($viewcalendarurl);
$link->param('view', 'upcoming');

$formoptions = new stdClass;

if ($eventid !== 0) {
    $title = get_string('editevent', 'calendar');
    $event = calendar_event::load($eventid);
    if (!calendar_edit_event_allowed($event)) {
        print_error('nopermissions');
    }
    $event->action = $action;
    $event->course = $courseid;
    $event->timedurationuntil = $event->timestart + $event->timeduration;
    $event->count_repeats();

    if (!calendar_add_event_allowed($event)) {
        print_error('nopermissions');
    }
} else {
    $title = get_string('newevent', 'calendar');
    calendar_get_allowed_types($formoptions->eventtypes, $USER->id);
    $event = new stdClass();
    $event->action = $action;
    $event->course = $courseid;
    $event->timeduration = 0;
    if ($formoptions->eventtypes->courses) {
        if ($courseexists) {
            $event->courseid = $courseid;
            $event->eventtype = 'course';
        } else {
            unset($formoptions->eventtypes->courses);
            unset($formoptions->eventtypes->groups);
        }
    }
    if($cal_y && $cal_m && $cal_d && checkdate($cal_m, $cal_d, $cal_y)) {
        $event->timestart = make_timestamp($cal_y, $cal_m, $cal_d, 0, 0, 0);
    } else if($cal_y && $cal_m && checkdate($cal_m, 1, $cal_y)) {
        if($cal_y == $now['year'] && $cal_m == $now['mon']) {
            $event->timestart = make_timestamp($cal_y, $cal_m, $now['mday'], 0, 0, 0);
        } else {
            $event->timestart = make_timestamp($cal_y, $cal_m, 1, 0, 0, 0);
        }
    }
    $event = new calendar_event($event);
    if (!calendar_add_event_allowed($event)) {
        print_error('nopermissions');
    }
}

$properties = $event->properties(true);
$formoptions->event = $event;
$formoptions->hasduration = ($event->timeduration > 0);
$mform = new event_form(null, $formoptions);
$mform->set_data($properties);
$data = $mform->get_data();
if ($data) {
    if ($data->duration == 1) {
        $data->timeduration = $data->timedurationuntil- $data->timestart;
    } else if ($data->duration == 2) {
        $data->timeduration = $data->timedurationminutes * MINSECS;
    } else {
        $data->timeduration = 0;
    }

    $event->update($data);
    $eventurl = new moodle_url(CALENDAR_URL.'view.php', array('view'=>'day'));
    if (!empty($event->courseid)) {
        $eventurl->param('course', $event->courseid);
    }
    $eventurl->param('cal_d', date('j', $event->timestart));
    $eventurl->param('cal_m', date('n', $event->timestart));
    $eventurl->param('cal_y', date('Y', $event->timestart));
    $eventurl->set_anchor('event_'.$event->id);
    redirect($eventurl);
}

$PAGE->navbar->add($strcalendar, $link);
$PAGE->navbar->add($title);
$PAGE->set_title($site->shortname.': '.$strcalendar.': '.$title);
$PAGE->set_heading($COURSE->fullname);

calendar_set_filters($calendar->courses, $calendar->groups, $calendar->users);
$renderer = $PAGE->get_renderer('core_calendar');
$calendar->add_sidecalendar_blocks($renderer);

echo $OUTPUT->header();
echo $renderer->start_layout();
echo $OUTPUT->heading($title);
$mform->display();
echo $renderer->complete_layout();
echo $OUTPUT->footer();
