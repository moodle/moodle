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

$courseid = optional_param('course', SITEID, PARAM_INT);
$view = optional_param('view', 'upcoming', PARAM_ALPHA);
$day  = optional_param('cal_d', 0, PARAM_INT);
$mon  = optional_param('cal_m', 0, PARAM_INT);
$yr   = optional_param('cal_y', 0, PARAM_INT);

$url = new moodle_url('/calendar/view.php');
if ($courseid != SITEID) {
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

if ($courseid != SITEID && !empty($courseid)) {
    $course = $DB->get_record('course', array('id' => $courseid));
    $courses = array($course->id => $course);
    $issite = false;
    navigation_node::override_active_url(new moodle_url('/course/view.php', array('id' => $course->id)));
} else {
    $course = get_site();
    $courses = calendar_get_default_courses();
    $issite = true;
}
require_course_login($course);

$calendar = new calendar_information($day, $mon, $yr);
$calendar->prepare_for_view($course, $courses);

$now = usergetdate(time());
$pagetitle = '';

$strcalendar = get_string('calendar', 'calendar');

if (!checkdate($mon, $day, $yr)) {
    $day = intval($now['mday']);
    $mon = intval($now['mon']);
    $yr = intval($now['year']);
}
$time = make_timestamp($yr, $mon, $day);

switch($view) {
    case 'day':
        $PAGE->navbar->add(userdate($time, get_string('strftimedate')));
        $pagetitle = get_string('dayviewtitle', 'calendar', userdate($time, get_string('strftimedaydate')));
    break;
    case 'month':
        $PAGE->navbar->add(userdate($time, get_string('strftimemonthyear')));
        $pagetitle = get_string('detailedmonthviewtitle', 'calendar', userdate($time, get_string('strftimemonthyear')));
    break;
    case 'upcoming':
        $pagetitle = get_string('upcomingevents', 'calendar');
    break;
}

// Print title and header
$PAGE->set_pagelayout('standard');
$PAGE->set_title("$course->shortname: $strcalendar: $pagetitle");
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_button(calendar_preferences_button($course));

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
        echo $renderer->show_month_detailed($calendar, $url);
    break;
    case 'upcoming':
        $defaultlookahead = CALENDAR_DEFAULT_UPCOMING_LOOKAHEAD;
        if (isset($CFG->calendar_lookahead)) {
            $defaultlookahead = intval($CFG->calendar_lookahead);
        }
        $lookahead = get_user_preferences('calendar_lookahead', $defaultlookahead);

        $defaultmaxevents = CALENDAR_DEFAULT_UPCOMING_MAXEVENTS;
        if (isset($CFG->calendar_maxevents)) {
            $defaultmaxevents = intval($CFG->calendar_maxevents);
        }
        $maxevents = get_user_preferences('calendar_maxevents', $defaultmaxevents);
        echo $renderer->show_upcoming_events($calendar, $lookahead, $maxevents);
    break;
}

//Link to calendar export page.
echo $OUTPUT->container_start('bottom');
if (!empty($CFG->enablecalendarexport)) {
    echo $OUTPUT->single_button(new moodle_url('export.php', array('course'=>$courseid)), get_string('exportcalendar', 'calendar'));
    if (calendar_user_can_add_event($course)) {
        echo $OUTPUT->single_button(new moodle_url('/calendar/managesubscriptions.php', array('course'=>$courseid)), get_string('managesubscriptions', 'calendar'));
    }
    if (isloggedin()) {
        $authtoken = sha1($USER->id . $USER->password . $CFG->calendar_exportsalt);
        $link = new moodle_url('/calendar/export_execute.php', array('preset_what'=>'all', 'preset_time'=>'recentupcoming', 'userid' => $USER->id, 'authtoken'=>$authtoken));
        $icon = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('i/ical'), 'height'=>'14', 'width'=>'36', 'alt'=>get_string('ical', 'calendar'), 'title'=>get_string('quickdownloadcalendar', 'calendar')));
        echo html_writer::tag('a', $icon, array('href'=>$link));
    }
}

echo $OUTPUT->container_end();
echo html_writer::end_tag('div');
echo $renderer->complete_layout();
echo $OUTPUT->footer();