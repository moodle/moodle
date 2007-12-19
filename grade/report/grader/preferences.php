<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
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
set_time_limit(0);
require_once '../../../config.php';
require_once $CFG->libdir . '/gradelib.php';
require_once '../../lib.php';

$courseid      = required_param('id', PARAM_INT);

/// Make sure they can even access this course

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course->id);

$context = get_context_instance(CONTEXT_COURSE, $course->id);
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
require_capability('gradereport/grader:view', $context);

require('preferences_form.php');
$mform = new grader_report_preferences_form('preferences.php', compact('course'));

if ($mform->is_cancelled()){
    redirect($CFG->wwwroot . '/grade/report/grader/index.php?id='.$courseid);
}

// If data submitted, then process and store.
if ($data = $mform->get_data()) {
    foreach ($data as $preference => $value) {
        if (substr($preference, 0, 6) !== 'grade_') {
            continue;
        }

        if ($value == GRADE_REPORT_PREFERENCE_DEFAULT || strlen($value) == 0) {
            unset_user_preference($preference);
        } else {
            set_user_preference($preference, $value);
        }
    }

    redirect($CFG->wwwroot . '/grade/report/grader/index.php?id='.$courseid); // message here breaks accessability and is sloooowww
    exit;
}

$strgrades = get_string('grades');
$strgraderreport = get_string('modulename', 'gradereport_grader');
$strgradepreferences = get_string('gradepreferences', 'grades');

$navigation = grade_build_nav(__FILE__, $strgradepreferences, $courseid);

print_header_simple($strgrades.': '.$strgraderreport . ': ' . $strgradepreferences,': '.$strgradepreferences, $navigation,
                    '', '', true, '', navmenu($course));

/// Print the plugin selector at the top
print_grade_plugin_selector($course->id, 'report', 'grader');

// Add tabs
$currenttab = 'preferences';
include('tabs.php');

// If USER has admin capability, print a link to the site config page for this report
if (has_capability('moodle/site:config', $systemcontext)) {
    echo '<div id="siteconfiglink"><a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=gradereportgrader">';
    echo get_string('changereportdefaults', 'grades');
    echo "</a></div>\n";
}

print_simple_box_start("center");

$mform->display();
print_simple_box_end();

print_footer($course);
?>
