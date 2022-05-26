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
 * This file is part of the User section Moodle
 *
 * @copyright 2003-2004 Jon Papaioannou (pj@moodle.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 * @package calendar
 */

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/calendar/lib.php');

if (empty($CFG->enablecalendarexport)) {
    die('no export');
}

$courseid = optional_param('course', SITEID, PARAM_INT);
$categoryid  = optional_param('category', null, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$day = optional_param('cal_d', 0, PARAM_INT);
$mon = optional_param('cal_m', 0, PARAM_INT);
$year = optional_param('cal_y', 0, PARAM_INT);
$time = optional_param('time', 0, PARAM_INT);
$generateurl = optional_param('generateurl', 0, PARAM_BOOL);


// If a day, month and year were passed then convert it to a timestamp. If these were passed
// then we can assume the day, month and year are passed as Gregorian, as no where in core
// should we be passing these values rather than the time. This is done for BC.
if (!empty($day) && !empty($mon) && !empty($year)) {
    if (checkdate($mon, $day, $year)) {
        $time = make_timestamp($year, $mon, $day);
    } else {
        $time = time();
    }
} else if (empty($time)) {
    $time = time();
}

$url = new moodle_url('/calendar/export.php', array('time' => $time));
$managesubscriptionsurl = new moodle_url('/calendar/managesubscriptions.php');

if ($courseid != SITEID && !empty($courseid)) {
    // Course ID must be valid and existing.
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $courses = array($course->id => $course);
    $url->param('course', $course->id);
    $managesubscriptionsurl->param('course', $course->id);

    navigation_node::override_active_url(new moodle_url('/course/view.php', ['id' => $course->id]));
    $PAGE->navbar->add(
        get_string('calendar', 'calendar'),
        new moodle_url('/calendar/view.php', ['view' => 'month', 'course' => $course->id])
    );
} else {
    $course = get_site();
    $courses = calendar_get_default_courses();

    if (!empty($categoryid)) {
        $managesubscriptionsurl->param('category', $categoryid);

        navigation_node::override_active_url(new moodle_url('/course/index.php', ['categoryid' => $categoryid]));
        $PAGE->set_category_by_id($categoryid);
        $PAGE->navbar->add(
            get_string('calendar', 'calendar'),
            new moodle_url('/calendar/view.php', ['view' => 'month', 'category' => $categoryid])
        );
    } else {
        $PAGE->navbar->add(get_string('calendar', 'calendar'), new moodle_url('/calendar/view.php', ['view' => 'month']));
    }
}
require_login($course, false);

if ($action !== '') {
    $url->param('action', $action);
}

$PAGE->set_url($url);

$calendar = new calendar_information(0, 0, 0, $time);
$calendar->set_sources($course, $courses);

$pagetitle = get_string('export', 'calendar');

$PAGE->navbar->add(get_string('managesubscriptions', 'calendar'), $managesubscriptionsurl);
$PAGE->navbar->add(get_string('exportcalendar', 'calendar'), $url);

// Print title and header.
$headingstr = get_string('calendar', 'core_calendar');
$headingstr = ($courseid != SITEID && !empty($courseid)) ? "{$headingstr}: {$COURSE->shortname}" : $headingstr;
$PAGE->set_title($course->shortname.': '.get_string('calendar', 'calendar').': '.$pagetitle);
$PAGE->set_heading($headingstr);
$PAGE->set_pagelayout('standard');
$PAGE->set_secondary_navigation(false);

$renderer = $PAGE->get_renderer('core_calendar');
$calendar->add_sidecalendar_blocks($renderer);

// Get the calendar type we are using.
$calendartype = \core_calendar\type_factory::get_calendar_instance();
$now = $calendartype->timestamp_to_date_array($time);

$weekend = CALENDAR_DEFAULT_WEEKEND;
if (isset($CFG->calendar_weekend)) {
    $weekend = intval($CFG->calendar_weekend);
}
$numberofdaysinweek = $calendartype->get_num_weekdays();

$formdata = array(
    // Let's populate some vars to let "common tasks" be somewhat smart...
    // If today it's weekend, give the "next week" option.
    'allownextweek' => $weekend & (1 << $now['wday']),
    // If it's the last week of the month, give the "next month" option.
    'allownextmonth' => calendar_days_in_month($now['mon'], $now['year']) - $now['mday'] < $numberofdaysinweek,
    // If today it's weekend but tomorrow it isn't, do NOT give the "this week" option.
    'allowthisweek' => !(($weekend & (1 << $now['wday'])) && !($weekend & (1 << (($now['wday'] + 1) % $numberofdaysinweek))))
);

// Disable submit protection so that the submit buttons continue working after being pressed.
$exportform = new core_calendar_export_form($FULLME, $formdata, 'POST', '', ['data-double-submit-protection' => 'off']);
$calendarurl = '';
if ($data = $exportform->get_data()) {
    $params = array();
    $params['userid']      = $USER->id;
    $params['authtoken']   = calendar_get_export_token($USER);
    $params['preset_what'] = $data->events['exportevents'];
    $params['preset_time'] = $data->period['timeperiod'];

    $link = new moodle_url('/calendar/export_execute.php', $params);
    if (!empty($data->generateurl)) {
        $exporturlcontext = ['calendarexporturl' => $link->out(false)];
        $exporturl = $OUTPUT->render_from_template('core_calendar/export_calendar_url', $exporturlcontext);
        $calendarurl = html_writer::div($exporturl, 'generalbox calendarurl mt-3');
    }

    if (!empty($data->export)) {
        redirect($link);
    }
}

echo $OUTPUT->header();
echo $renderer->start_layout();
echo $OUTPUT->heading(get_string('exportcalendar', 'calendar'));

if ($action != 'advanced') {
    $exportform->display();
}

echo $calendarurl;

echo $renderer->complete_layout();

echo $OUTPUT->footer();
