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

$courseid      = optional_param('id', 0 , PARAM_INT);

/// Make sure they can even access this course

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course->id);

$context = get_context_instance(CONTEXT_COURSE, $course->id);

$strgrades = get_string('grades');
$strgraderreport = get_string('graderreport', 'grades');
$strgradepreferences = get_string('gradepreferences', 'grades');

$crumbs[] = array('name' => $strgrades, 'link' => $CFG->wwwroot . '/grade/index.php?id='.$courseid, 'type' => 'misc');
$crumbs[] = array('name' => $strgraderreport, 
    'link' => $CFG->wwwroot . '/grade/report.php?id=' . $courseid . '&amp;report=grader', 'type' => 'misc');
$crumbs[] = array('name' => $strgradepreferences, 'link' => '', 'type' => 'misc');

$navigation = build_navigation($crumbs);

print_header_simple($strgrades.': '.$strgraderreport,': '.$strgradepreferences, $navigation, 
                    '', '', true, '', navmenu($course));
print_heading(get_string('preferences'));
// Add tabs
$currenttab = 'preferences'; 
include('tabs.php');

print_footer($course);
?>
