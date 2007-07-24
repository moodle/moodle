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
require_once $CFG->dirroot.'/grade/report/grader/lib.php';

$courseid      = required_param('id');                   // course id
$page          = optional_param('page', 0, PARAM_INT);   // active page
$perpageurl    = optional_param('perpage', 0, PARAM_INT);
$edit          = optional_param('edit', -1, PARAM_BOOL); // sticky editting mode

$sortitemid    = optional_param('sortitemid', 0, PARAM_ALPHANUM); // sort by which grade item
$action        = optional_param('action', 0, PARAM_ALPHA);
$move          = optional_param('move', 0, PARAM_INT);
$type          = optional_param('type', 0, PARAM_ALPHA);
$target        = optional_param('target', 0, PARAM_ALPHANUM);
$toggle        = optional_param('toggle', NULL, PARAM_INT);
$toggle_type   = optional_param('toggle_type', 0, PARAM_ALPHANUM);

/// basic access checks
if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('gradereport/grader:view', $context);

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'grader', 'courseid'=>$courseid, 'page'=>$page));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'grader';

/// Build navigation

$strgrades  = get_string('grades');
$reportname = get_string('modulename', 'gradereport_grader');

$navlinks = array(array('name'=>$strgrades, 'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
                  array('name'=>$reportname, 'link'=>'', 'type'=>'misc'));
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

// page params for the turn editting on
$options = $gpr->get_options();
$options['sesskey'] = sesskey();

if ($USER->gradeediting) {
    $options['edit'] = 0;
    $string = get_string('turneditingoff');
} else {
    $options['edit'] = 1;
    $string = get_string('turneditingon');
}

$buttons = print_single_button('index.php', $options, $string, 'get', '_self', true);

$gradeserror = array();

// Handle toggle change request
if (!is_null($toggle) && !empty($toggle_type)) {
    set_user_preferences(array('grade_report_show'.$toggle_type => $toggle));
}

//first make sure we have proper final grades - this must be done before constructing of the grade tree
grade_regrade_final_grades($courseid);

// Initialise the grader report object
$report = new grade_report_grader($courseid, $gpr, $context, $page, $sortitemid);

/// processing posted grades & feedback here
if ($data = data_submitted() and confirm_sesskey()) {
    $report->process_data($data);
}

// Override perpage if set in URL
if ($perpageurl) {
    $report->user_prefs['studentsperpage'] = $perpageurl;
}

// Perform actions on categories, items and grades
if (!empty($target) && !empty($action) && confirm_sesskey()) {
    $report->process_action($target, $action);
}

$report->load_users();
$numusers = $report->get_numusers();
$report->load_final_grades();

/// Print header
print_header_simple($strgrades.': '.$reportname, ': '.$strgrades, $navigation,
                        '', '', true, $buttons, navmenu($course));

/// Print the plugin selector at the top
print_grade_plugin_selector($courseid, 'report', 'grader');

// Add tabs
$currenttab = 'graderreport';
require('tabs.php');

echo $report->group_selector;

echo '<div class="clearer"></div>';

echo $report->get_toggles_html();
print_paging_bar($numusers, $report->page, $report->get_pref('studentsperpage'), $report->pbarurl);

$reporthtml = '<table class="gradestable">';
$reporthtml .= $report->get_headerhtml();
$reporthtml .= $report->get_rangehtml();
$reporthtml .= $report->get_studentshtml();
$reporthtml .= $report->get_avghtml(true);
$reporthtml .= $report->get_avghtml();
$reporthtml .= "</table>";

// print submit button
if ($USER->gradeediting) {
    echo '<form action="index.php" method="post">';
    echo '<div>';
    echo '<input type="hidden" value="'.$courseid.'" name="id" />';
    echo '<input type="hidden" value="'.sesskey().'" name="sesskey" />';
    echo '<input type="hidden" value="grader" name="report"/>';
}

echo $reporthtml;

// print submit button
if ($USER->gradeediting && ($report->get_pref('quickfeedback') || $report->get_pref('quickgrading'))) {
    echo '<div class="submit"><input type="submit" value="'.get_string('update').'" /></div>';
    echo '</div></form>';
}

// prints paging bar at bottom for large pages
if ($report->get_pref('studentsperpage') >= 20) {
    print_paging_bar($numusers, $report->page, $report->get_pref('studentsperpage'), $report->pbarurl);
}

print_footer($course);

?>
