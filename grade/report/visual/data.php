<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
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
 * Page to be read in by the flex application.
 * Outputs data for a visulasation in tab format.
 */
 
/// Get a session id from the URI request and make a cookie
/// for it temparaly. This is needed as the flex application will
/// not have the users oringal cookie and only the session information
/// witch is passed to it.
$cookiewasset = false;
if(empty($_COOKIE) && isset($_GET['sessionid']) && isset($_GET['sessioncookie']) && isset($_GET['sessiontest'])) {
    $_COOKIE['MoodleSession' . $_GET['sessioncookie']] = $_GET['sessionid'];
    $_COOKIE['MoodleSessionTest' . $_GET['sessioncookie']] = $_GET['sessiontest'];
    $cookiewasset = true;
}

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/visual/lib.php';

$courseid = required_param('id');
$visid = optional_param('visid');

/// basic access checks
if(isset($DB) && !is_null($DB)) {
    $course = $DB->get_record('course', array('id' => $courseid));
} else {
    $course = get_record('course', 'id', $courseid);
}
if (!$course) {
        print_error('nocourseid');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('gradereport/visual:view', $context);

/// get tracking object
$gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'visual', 'courseid'=>$courseid));
$report = new grade_report_visual($courseid, $gpr, $context, $visid);

/// Make sure the user is allowed see this visualization
require_capability(grade_report_visual::get_visualization($report->visid, $context)->capability, $context);

grade_regrade_final_grades($courseid);

/// Turn of error reporting as hummans will not be seeing 
/// this and it will be read by the front end. Notices and 
/// warnings will break the format and stop the
/// front end from working.
error_reporting(0);

/// Get report object
$report->load_users();
$report->harvest_data();
$report->report_data();
$report->adapt_data();

/// Clean up cookie if it was created.
if($cookiewasset) {
    $_COOKIE['MoodleSession' . $_GET['sessioncookie']] = null;
    $_COOKIE['MoodleSessionTest' . $_GET['sessioncookie']] = null;
}
?>