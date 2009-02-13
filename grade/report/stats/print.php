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
 * Printer page for the stats report plugin.
 * @package gradebook
 */

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/stats/lib.php';

$courseid = required_param('id');

$reportname = get_string('modulename', 'gradereport_stats');

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
require_capability('gradereport/stats:view', $context);

/// get tracking object
$gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'stats', 'courseid'=>$courseid));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'stats';

grade_regrade_final_grades($courseid);

/// Get report object
$report = new grade_report_stats($courseid, $gpr, $context);


/// Build report to output
$report->load_users();
$report->harvest_data();
$report->report_data();
$report->adapt_data(true);

/// Print report
echo '<html><title>' . $reportname . ' for '  . $course->shortname .  '</title><body>';
echo '<div style="font-size:25px; text-align:center; font-weight:bold">' . $course->fullname . ': ' . $reportname. '</div>';
echo '<div style="text-align:center;">' . userdate(time()) . '</div>';
echo '<br/><br/>';
echo $report->html;
echo '</body></html>';
?>