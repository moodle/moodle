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
require_once $CFG->dirroot.'/grade/report/user/lib.php';

$courseid = required_param('id');
$userid   = optional_param('userid', $USER->id, PARAM_INT);

/// basic access checks
if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}
require_login($course);

$context     = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('gradereport/user:view', $context);

if (empty($userid)) {
    require_capability('moodle/grade:viewall', $context);

} else {
    if (!get_complete_user_data('id', $userid)) {
        error("Incorrect userid");
    }
}

$access = true;
if (has_capability('moodle/grade:viewall', $context)) {
    //ok - can view all course grades

} else if ($userid == $USER->id and has_capability('moodle/grade:view', $context) and $course->showgrades) {
    //ok - can view own grades

} else if ($has_capability('moodle/grade:viewall', get_context_instance(CONTEXT_USER, $userid)) and $course->showgrades) {
    // ok - can view grades of this user- parent most probably

} else {
    $access = false;
}

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'user', 'courseid'=>$courseid, 'userid'=>$userid));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'user';

/// Build navigation
$strgrades  = get_string('grades');
$reportname = get_string('modulename', 'gradereport_user');

$navigation = grade_build_nav(__FILE__, $reportname, $courseid);

/// Print header
print_header_simple($strgrades.': '.$reportname, ': '.$strgrades, $navigation,
                    '', '', true, '', navmenu($course));

/// Print the plugin selector at the top
print_grade_plugin_selector($courseid, 'report', 'user');


if ($access) {

    //first make sure we have proper final grades - this must be done before constructing of the grade tree
    grade_regrade_final_grades($courseid);

    if (has_capability('moodle/grade:viewall', $context)) { //Teachers will see all student reports
        /// Print graded user selector at the top
        echo '<div id="graded_users_selector">';
        print_graded_users_selector($course, 'report/user/index.php?id=' . $course->id, $userid);
        echo '</div>';
        echo "<p style = 'page-break-after: always;'></p>";

        if ($userid === 0) {
            $gui = new graded_users_iterator($course);
            $gui->init();
            while ($userdata = $gui->next_user()) {
                $user = $userdata->user;
                $report = new grade_report_user($courseid, $gpr, $context, $user->id);
                print_heading(get_string('modulename', 'gradereport_user'). ' - '.fullname($report->user));
                if ($report->fill_table()) {
                    echo $report->print_table(true);
                }
                echo "<p style = 'page-break-after: always;'></p>";
            }
            $gui->close();
        } elseif ($userid) { // Only show one user's report
            $report = new grade_report_user($courseid, $gpr, $context, $userid);
            print_heading(get_string('modulename', 'gradereport_user'). ' - '.fullname($report->user));
            if ($report->fill_table()) {
                echo $report->print_table(true);
            } 
        }
    } else { //Students will see just their own report 

        // Create a report instance
        $report = new grade_report_user($courseid, $gpr, $context, $userid);

        // print the page
        print_heading(get_string('modulename', 'gradereport_user'). ' - '.fullname($report->user));

        if ($report->fill_table()) {
            echo $report->print_table(true);
        }
    }

} else {
    // no access to grades!
    echo "Can not view grades."; //TODO: localize
}
print_footer($course);

?>
