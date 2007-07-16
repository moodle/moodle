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


    require_once("../config.php");
    require_once("../lib/gradelib.php");

    $courseid = required_param('id');                   // course id
    $report   = optional_param('report', get_user_preferences('grade_defaultreport', 'user'), PARAM_FILE); 
    $edit     = optional_param('edit', -1, PARAM_BOOL); // sticky editting mode

/// Make sure they can even access this course

    if (!$course = get_record('course', 'id', $courseid)) {
        print_error('nocourseid');
    }

    require_login($course->id);

    $context = get_context_instance(CONTEXT_COURSE, $course->id);


/// Now check what reports are available

    if ($reports = get_list_of_plugins('grade/report', 'CVS')) {         // Get all installed reports
        foreach ($reports as $key => $plugin) {                      // Remove ones we can't see
            if (!has_capability('gradereport/'.$plugin.':view', $context)) {
                unset($reports[$key]);
            }
        }
    }

    if (!$reports) {
        print_error('nogradereports', 'grade');
    }


/// Make sure the currently selected one makes sense

    if (!in_array($report, $reports)) {
        reset($reports);
        list($key, $report) = each($reports);  // Just pick the first one
    }

    if ($report != get_user_preferences('grade_defaultreport', 'user')) {
        set_user_preference('grade_defaultreport', $report);
    }


/// Create menu of reports

    $reportnames = array();

    if (count($reports) > 1) {
        foreach ($reports as $plugin) {
            $reportnames[$plugin] = get_string('modulename', 'gradereport_'.$plugin);
        }
    }

    asort($reportnames);    // Alphabetical sort


/// Build navigation

    $strgrades = get_string('grades');
    $navlinks = array();
    $navlinks[] = array('name' => $strgrades, 'link' => $CFG->wwwroot . '/grade/index.php?id='.$courseid, 'type' => 'misc');
    $navlinks[] = array('name' => $reportnames[$report], 'link' => '', 'type' => 'misc');

    $navigation = build_navigation($navlinks);


/// Build editing on/off buttons

    if (!isset($USER->gradeediting)) {
        $USER->gradeediting = 0;
    }

    if (($edit == 1) and confirm_sesskey()) {
        $USER->gradeediting = 1;
    } else if (($edit == 0) and confirm_sesskey()) {
        $USER->gradeediting = 0;
    }

    // params for the turn editting on
    $options = array();
    $options['id'] = $courseid;
    $options['report'] = $report;

    if ($USER->gradeediting) {
        $options['edit'] = 0;
        $string = get_string('turneditingoff');
    } else {
        $options['edit'] = 1;
        $string = get_string('turneditingon');
    }

    $options['sesskey'] = sesskey();
    $link = 'report.php';


    $buttons = print_single_button($link, $options, $string, 'get', '_self', true);


/// Print header

    print_header_simple($strgrades.':'.$reportnames[$report], ':'.$strgrades, $navigation,
                        '', '', true, $buttons, navmenu($course));

/// Print the report selector at the top if there is more than one report

    if ($reportnames) {
        popup_form($CFG->wwwroot.'/grade/report.php?id='.$course->id.'&amp;report=', $reportnames,
                   'choosegradereport', $report, '', '', '', false, 'self', get_string('gradereports', 'grades').':');
    }


/// Now simply include the report here and we're done

    include_once($CFG->dirroot.'/grade/report/'.$report.'/index.php');

    print_footer($course);


?>
