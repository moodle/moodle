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

//  Display the calendar page.

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/calendar/lib.php');

$courseid = optional_param('course', 0, PARAM_INT);
$view = optional_param('view', 'upcoming', PARAM_ALPHA);
$day  = optional_param('cal_d', 0, PARAM_INT);
$mon  = optional_param('cal_m', 0, PARAM_INT);
$yr   = optional_param('cal_y', 0, PARAM_INT);

$site = get_site();

$url = new moodle_url('/calendar/view.php');
if ($courseid !== 0) {
    $url->param('course', $courseid);
}
if ($view !== 'upcoming') {
    $url->param('view', $view);
}
if ($day !== 0) {
    $url->param('cal_d', $day);
}
if ($mon !== 0) {
    $url->param('cal_m', $mon);
}
if ($yr !== 0) {
    $url->param('cal_y', $yr);
}
$PAGE->set_url($url);

//TODO: the courseid handling in /calendar/ is a bloody mess!!!

if ($courseid && $courseid != SITEID) {
    require_login($courseid);
} else if ($CFG->forcelogin) {
    $PAGE->set_context(get_context_instance(CONTEXT_SYSTEM)); //TODO: wrong
    require_login();
} else {
    $PAGE->set_context(get_context_instance(CONTEXT_SYSTEM)); //TODO: wrong
}

$calendar = new calendar_information($day, $mon, $yr);
$calendar->courseid = $courseid;

// Initialize the session variables
calendar_session_vars();

//add_to_log($course->id, "course", "view", "view.php?id=$course->id", "$course->id");
$now = usergetdate(time());
$pagetitle = '';

$strcalendar = get_string('calendar', 'calendar');

$link = calendar_get_link_href(new moodle_url(CALENDAR_URL.'view.php', array('view'=>'upcoming', 'course'=>$courseid)),
                               $now['mday'], $now['mon'], $now['year']);
$PAGE->navbar->add($strcalendar, $link);

if(!checkdate($mon, $day, $yr)) {
    $day = intval($now['mday']);
    $mon = intval($now['mon']);
    $yr = intval($now['year']);
}
$time = make_timestamp($yr, $mon, $day);

switch($view) {
    case 'day':
        $PAGE->navbar->add(userdate($time, get_string('strftimedate')));
        $pagetitle = get_string('dayview', 'calendar');
    break;
    case 'month':
        $PAGE->navbar->add(userdate($time, get_string('strftimemonthyear')));
        $pagetitle = get_string('detailedmonthview', 'calendar');
    break;
    case 'upcoming':
        $pagetitle = get_string('upcomingevents', 'calendar');
    break;
}
// If a course has been supplied in the URL, change the filters to show that one
if (!empty($courseid)) {
    if ($course = $DB->get_record('course', array('id'=>$courseid))) {
        if ($course->id == SITEID) {
            // If coming from the home page, show all courses
            $SESSION->cal_courses_shown = calendar_get_default_courses(true);
            calendar_set_referring_course(0);

        } else {
            // Otherwise show just this one
            $SESSION->cal_courses_shown = $course->id;
            calendar_set_referring_course($SESSION->cal_courses_shown);
        }
    }
} else {
    $course = null;
}
if (!isloggedin() or isguestuser()) {
    $defaultcourses = calendar_get_default_courses();
    calendar_set_filters($calendar->courses, $calendar->groups, $calendar->users, $defaultcourses, $defaultcourses);
} else {
    calendar_set_filters($calendar->courses, $calendar->groups, $calendar->users);
}
// Let's see if we are supposed to provide a referring course link
// but NOT for the "main page" course
if ($SESSION->cal_course_referer != SITEID &&
   ($shortname = $DB->get_field('course', 'shortname', array('id'=>$SESSION->cal_course_referer))) !== false) {
    require_login(); //TODO: very wrong!!
    if (empty($course)) {
        $course = $DB->get_record('course', array('id'=>$SESSION->cal_course_referer)); // Useful to have around
    }
}

$strcalendar = get_string('calendar', 'calendar');
$prefsbutton = calendar_preferences_button();

// Print title and header
$PAGE->set_title("$site->shortname: $strcalendar: $pagetitle");
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_button($prefsbutton);
$PAGE->set_pagelayout('standard');

$renderer = $PAGE->get_renderer('core_calendar');
$calendar->add_sidecalendar_blocks($renderer, true, $view);

echo $OUTPUT->header();
echo $renderer->start_layout();
echo html_writer::start_tag('div', array('class'=>'heightcontainer'));

switch($view) {
    case 'day':
        echo $renderer->show_day($calendar);
    break;
    case 'month':
        echo $renderer->show_month_detailed($calendar);
    break;
    case 'upcoming':
        echo $renderer->show_upcoming_events($calendar, get_user_preferences('calendar_lookahead', CALENDAR_UPCOMING_DAYS), get_user_preferences('calendar_maxevents', CALENDAR_UPCOMING_MAXEVENTS));
    break;
}

//Link to calendar export page
echo $OUTPUT->container_start('bottom');
if (!empty($CFG->enablecalendarexport)) {
    echo $OUTPUT->single_button(new moodle_url('export.php', array('course'=>$courseid)), get_string('exportcalendar', 'calendar'));
    if (isloggedin()) {
        $authtoken = sha1($USER->username . $USER->password . $CFG->calendar_exportsalt);
        $link = new moodle_url('/calendar/export_execute.php', array('preset_what'=>'all', 'prest_time'=>'recentupcoming', 'username'=>$USER->username, 'authtoken'=>$authtoken));
        $icon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('i/ical'), 'height'=>'14', 'width'=>'36', 'alt'=>get_string('ical', 'calendar'), 'title'=>get_string('quickdownloadcalendar', 'calendar')));
        echo html_writer::tag('a', $icon, array('href'=>$link));
    }
}
echo $OUTPUT->container_end();
echo html_writer::end_tag('div');
echo $renderer->complete_layout();
echo $OUTPUT->footer();
