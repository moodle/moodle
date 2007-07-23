<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2007  Martin Dougiamas  http://dougiamas.com       //
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
require_capability('gradereport/grader:manage', $context);

// If data submitted, then process and store.
if ($form = data_submitted()) {
    foreach ($form as $preference => $value) {
        switch ($preference) {
            case 'persistflt':
                set_user_preference('calendar_persistflt', intval($value));
                break;
            default:
                if ($value == GRADE_REPORT_PREFERENCE_DEFAULT) {
                    unset_user_preference($preference);
                } else {
                    set_user_preference($preference, $value);
                }
                break;
        }
    }

    redirect($CFG->wwwroot . '/grade/report.php?report=grader&amp;id='.$courseid, get_string('changessaved'), 1);
    exit;
}

$strgrades = get_string('grades');
$strgraderreport = get_string('modulename', 'gradereport_grader');
$strgradepreferences = get_string('gradepreferences', 'grades');

$navlinks = array();
$navlinks[] = array('name' => $strgrades, 'link' => $CFG->wwwroot . '/grade/index.php?id='.$courseid, 'type' => 'misc');
$navlinks[] = array('name' => $strgraderreport,
    'link' => $CFG->wwwroot . '/grade/report.php?id=' . $courseid . '&amp;report=grader', 'type' => 'misc');
$navlinks[] = array('name' => $strgradepreferences, 'link' => '', 'type' => 'misc');

$navigation = build_navigation($navlinks);

print_header_simple($strgrades.': '.$strgraderreport . ': ' . $strgradepreferences,': '.$strgradepreferences, $navigation,
                    '', '', true, '', navmenu($course));

/// Print the plugin selector at the top
print_grade_plugin_selector($course->id, 'report', 'grader');

// Add tabs
$currenttab = 'preferences';
include('tabs.php');

print_simple_box_start("center");

include('./preferences_form.php');
$mform = new grader_report_preferences_form('preferences.php', compact('course'));
echo $mform->display();
print_simple_box_end();

print_footer($course);
?>
