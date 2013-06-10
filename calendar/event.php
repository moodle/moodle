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
$courseid = optional_param('courseid', SITEID, PARAM_INT);
$courseid = optional_param('course', $courseid, PARAM_INT);
$cal_y = optional_param('cal_y', 0, PARAM_INT);
$cal_m = optional_param('cal_m', 0, PARAM_INT);
$cal_d = optional_param('cal_d', 0, PARAM_INT);

$url = new moodle_url('/calendar/event.php', array('action' => $action));
if ($eventid != 0) {
    $url->param('id', $eventid);
}
if ($courseid != SITEID) {
    $url->param('course', $courseid);
}
if ($cal_y !== 0) {
    $url->param('cal_y', $cal_y);
}
if ($cal_m !== 0) {
    $url->param('cal_m', $cal_m);
}
if ($cal_d !== 0) {
    $url->param('cal_d', $cal_d);
}
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');

if ($courseid != SITEID && !empty($courseid)) {
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $courses = array($course->id => $course);
    $issite = false;
} else {
    $course = get_site();
    $courses = calendar_get_default_courses();
    $issite = true;
}
require_login($course, false);

if ($action === 'delete' && $eventid > 0) {
    $deleteurl = new moodle_url('/calendar/delete.php', array('id'=>$eventid));
    if ($courseid > 0) {
        $deleteurl->param('course', $courseid);
    }
    redirect($deleteurl);
}

$calendar = new calendar_information($cal_d, $cal_m, $cal_y);
$calendar->prepare_for_view($course, $courses);

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
    calendar_get_allowed_types($formoptions->eventtypes, $course);
    $event = new stdClass();
    $event->action = $action;
    $event->course = $courseid;
    $event->courseid = $courseid;
    $event->timeduration = 0;
    if ($formoptions->eventtypes->courses) {
        if (!$issite) {
            $event->eventtype = 'course';
        } else {
            unset($formoptions->eventtypes->courses);
            unset($formoptions->eventtypes->groups);
        }
    }
    if($cal_y && $cal_m && $cal_d && checkdate($cal_m, $cal_d, $cal_y)) {
        $event->timestart = make_timestamp($cal_y, $cal_m, $cal_d, 0, 0, 0);
    } else if($cal_y && $cal_m && checkdate($cal_m, 1, $cal_y)) {
        $now = usergetdate(time());
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

    $params = array(
        'view' => 'day',
        'cal_d' => userdate($event->timestart, '%d'),
        'cal_m' => userdate($event->timestart, '%m'),
        'cal_y' => userdate($event->timestart, '%Y'),
    );
    $eventurl = new moodle_url('/calendar/view.php', $params);
    if (!empty($event->courseid) && $event->courseid != SITEID) {
        $eventurl->param('course', $event->courseid);
    }
    $eventurl->set_anchor('event_'.$event->id);
    redirect($eventurl);
}

$viewcalendarurl = new moodle_url(CALENDAR_URL.'view.php', $PAGE->url->params());
$viewcalendarurl->remove_params(array('id', 'action'));
$viewcalendarurl->param('view', 'upcoming');
$strcalendar = get_string('calendar', 'calendar');

$PAGE->navbar->add($strcalendar, $viewcalendarurl);
$PAGE->navbar->add($title);
$PAGE->set_title($course->shortname.': '.$strcalendar.': '.$title);
$PAGE->set_heading($course->fullname);

$renderer = $PAGE->get_renderer('core_calendar');
$calendar->add_sidecalendar_blocks($renderer);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);
$mform->display();
echo $OUTPUT->footer();
