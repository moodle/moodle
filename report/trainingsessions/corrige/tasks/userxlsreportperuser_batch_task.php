<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This script handles the report generation in batch task for a single group.
 * It will produce a group Excel worksheet report that is pushed immediately to output
 * for downloading by a batch agent. No file is stored into the system.
 * groupid must be provided.
 * This script should be sheduled in a CURL call stack or a multi_CURL parallel call.
 *
 * @package    report_trainingsessions
 * @category   report
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');

ob_start();

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/xlsrenderers.php');
require_once($CFG->dirroot.'/report/trainingsessions/lib/excellib.php');

$id = required_param('id', PARAM_INT); // The course id.
$userid = required_param('userid', PARAM_INT); // The group id.
$reportscope = optional_param('scope', 'currentcourse', PARAM_TEXT); // Only currentcourse is consistant.

ini_set('memory_limit', '512M');

if (!$course = $DB->get_record('course', array('id' => $id))) {
    die ('Invalid course ID');
}
$context = context_course::instance($course->id);
$PAGE->set_context($context);

if (!$user = $DB->get_record('user', array('id' => $userid))) {
    // Do NOT print_error here as we are a document writer.
    die ('Invalid user ID');
}

$input = report_trainingsessions_batch_input($course);

// Security.
report_trainingsessions_back_office_access($course, $userid);

$coursestructure = report_trainingsessions_get_course_structure($course->id, $items);

// Generate XLS.

$filename = "trainingsessions_user_{$userid}_report_".$input->filenametimesession.'.xls';

$workbook = new MoodleExcelWorkbookTS("-");
if (!$workbook) {
    die("Excel Librairies Failure");
}

$auser = $DB->get_record('user', array('id' => $userid));

// Sending HTTP headers.
ob_end_clean();
$workbook->send($filename);

$xlsformats = report_trainingsessions_xls_formats($workbook);
$startrow = report_trainingsessions_count_header_rows($course->id);

$row = $startrow;
$worksheet = report_trainingsessions_init_worksheet($auser->id, $row, $xlsformats, $workbook);

$logusers = $auser->id;
$logs = use_stats_extract_logs($input->from, $input->to, $auser->id, $course->id);
$aggregate = use_stats_aggregate_logs($logs, $input->from, $input->to);

$overall = report_trainingsessions_print_xls($worksheet, $coursestructure, $aggregate, $done, $row, $xlsformats);

$grantotal = report_trainingsessions_calculate_course_structure($coursestructure, $aggregate, $done, $items);

$grantotal->from = $input->from;
$grantotal->activityelapsed = 0 + @$aggregate['activities'][$id]->elapsed;
$grantotal->otherelapsed = 0 + @$aggregate['other'][$id]->elapsed;
$grantotal->courseelapsed = 0 + @$aggregate['course'][$id]->elapsed;

$grantotal->elapsed = 0 + @$aggregate['coursetotal'][$id]->elapsed;
$grantotal->elapsedlastweek = 0 + @$aggregatelastweek['coursetotal'][$id]->elapsed;
$grantotal->extelapsed = 0 + @$aggregate['coursetotal'][$id]->elapsed + @$aggregate['coursetotal'][0]->elapsed;
$grantotal->extelapsed += @$aggregate['coursetotal'][SITEID]->elapsed;
$grantotal->extelapsedlastweek = 0 + @$aggregatelastweek['coursetotal'][$id]->elapsed + @$aggregatelastweek['coursetotal'][0]->elapsed;
$grantotal->extelapsedlastweek += @$aggregatelastweek['coursetotal'][SITEID]->elapsed;
$grantotal->extother = 0 + @$aggregate['coursetotal'][0]->elapsed + @$aggregate['coursetotal'][SITEID]->elapsed;
$grantotal->extotherlastweek = 0 + @$aggregatelastweek['coursetotal'][0]->elapsed + @$aggregatelastweek['coursetotal'][SITEID]->elapsed;

$grantotal->activityevents = 0 + @$aggregate['activities'][$id]->events;
$grantotal->otherevents = 0 + @$aggregate['other'][$id]->events;
$grantotal->courseevents = 0 + @$aggregate['course'][$id]->events;
$grantotal->events = 0 + $grantotal->activityevents + $grantotal->otherevents + $grantotal->courseevents;
$grantotal->items = $grantotal->events; // Compatibility ?
$grantotal->extevents = 0 + @$aggregate['coursetotal'][$id]->events + @$aggregate['coursetotal'][0]->events;
$grantotal->extevents += @$aggregate['coursetotal'][SITEID]->events;

report_trainingsessions_print_header_xls($worksheet, $auser->id, $course->id, $grantotal, $xlsformats);

if (!empty($aggregate['sessions'])) {
    $worksheet = report_trainingsessions_init_worksheet($auser->id, $startrow, $xlsformats, $workbook, 'sessions');
    report_trainingsessions_print_sessions_xls($worksheet, 15, $aggregate['sessions'], $course, $xlsformats);
    report_trainingsessions_print_header_xls($worksheet, $auser->id, $course->id, $grantotal, $xlsformats);
}

$workbook->close();
