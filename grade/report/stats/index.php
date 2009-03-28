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
 * Index page for the stats report plugin.
 * Based on the grader report plugin but
 * for text based statistics.
 * @package gradebook
 */

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/stats/lib.php';

$courseid = required_param('id', PARAM_INT);
$toggle = optional_param('toggle', NULL, PARAM_INT);
$toggle_type = optional_param('toggle_type', 0, PARAM_ALPHANUM);

/// basic access checks
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('gradereport/stats:view', $context);

/// get tracking object
$gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'stats', 'courseid'=>$courseid));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'stats';

/// Build navigation
$strgrades  = get_string('grades');
$reportname = get_string('modulename', 'gradereport_stats');
$navigation = grade_build_nav(__FILE__, $reportname, $courseid);

/// Handle toggle change request
if (!is_null($toggle) && !empty($toggle_type)) {
    set_user_preferences(array('grade_report_statsshow'.$toggle_type => $toggle));
}

grade_regrade_final_grades($courseid);

/// Get report object
$report = new grade_report_stats($courseid, $gpr, $context);

print_grade_page_head($courseid, 'report', 'stats', $reportname);

/// Build report to output
$report->load_users();
$report->harvest_data();
$report->report_data();
$report->adapt_data();

/// Print report
echo $report->group_selector;
echo '<div class="clearer"></div>';
echo $report->get_toggles_html();
echo '<div class="clearer"></div>';
echo $report->html;

/// Print footer
print_footer($course);

?>
