<?php // $Id$

///////////////////////////////////////////////////////////////////////////
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

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/overview/lib.php';

$courseid = optional_param('id', $COURSE->id, PARAM_INT);
$userid   = optional_param('userid', $USER->id, PARAM_INT);

/// basic access checks
if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}
require_login($course);

if (!$user = get_complete_user_data('id', $userid)) {
    error("Incorrect userid");
}

$context     = get_context_instance(CONTEXT_COURSE, $course->id);
$usercontext = get_context_instance(CONTEXT_PERSONAL, $user->id);
require_capability('gradereport/overview:view', $context);

$access = true;
if (has_capability('moodle/grade:viewall', $context)) {
    //ok - can view all course grades

} else if ($user->id == $USER->id and has_capability('moodle/grade:view', $context) and $course->showgrades) {
    //ok - can view own grades

} else if (has_capability('moodle/grade:view', $usercontext) and $course->showgrades) {
    // ok - can view grades of this user- parent most probably

} else {
    $acces = false;
}

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'overview', 'courseid'=>$course->id, 'userid'=>$userid));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'overview';

/// Build navigation
$strgrades  = get_string('grades');
$reportname = get_string('modulename', 'gradereport_overview');

$navigation = grade_build_nav(__FILE__, $reportname, $course->id);

/// Print header
print_header_simple($strgrades.': '.$reportname, ': '.$strgrades, $navigation,
                    '', '', true, '', navmenu($course));

/// Print the plugin selector at the top
print_grade_plugin_selector($course->id, 'report', 'overview');

if ($access) {

    //first make sure we have proper final grades - this must be done before constructing of the grade tree
    grade_regrade_final_grades($course->id);

    // Create a report instance
    $report = new grade_report_overview($userid, $gpr, $context);

    $gradetotal = 0;
    $gradesum = 0;

    // print the page
    print_heading(get_string('modulename', 'gradereport_overview'). ' - '.fullname($report->user));

    if ($report->fill_table()) {
        echo $report->print_table(true);
    }

} else {
    // no access to grades!
    echo "Can not view grades."; //TODO: localize
}
print_footer($course);

?>
