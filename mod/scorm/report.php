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

// This script uses installed report plugins to print scorm reports

require_once("../../config.php");
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/mod/scorm/locallib.php');
require_once($CFG->dirroot.'/mod/scorm/reportsettings_form.php');
require_once($CFG->dirroot.'/mod/scorm/report/reportlib.php');
require_once($CFG->libdir.'/formslib.php');
include_once("report/default.php"); // Parent class
define('SCORM_REPORT_DEFAULT_PAGE_SIZE', 20);
define('SCORM_REPORT_ATTEMPTS_ALL_STUDENTS', 0);
define('SCORM_REPORT_ATTEMPTS_STUDENTS_WITH', 1);
define('SCORM_REPORT_ATTEMPTS_STUDENTS_WITH_NO', 2);

$id = required_param('id', PARAM_INT);// Course Module ID, or

$action = optional_param('action', '', PARAM_ALPHA);
$attemptids = optional_param('attemptid', array(), PARAM_RAW);
$download = optional_param('download', '', PARAM_RAW);
$mode = optional_param('mode', '', PARAM_ALPHA); // Report mode

$url = new moodle_url('/mod/scorm/report.php');

if ($action !== '') {
    $url->param('action', $action);
}
if ($mode !== '') {
    $url->param('mode', $mode);
}

$url->param('id', $id);
$cm = get_coursemodule_from_id('scorm', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
$scorm = $DB->get_record('scorm', array('id'=>$cm->instance), '*', MUST_EXIST);
    
$PAGE->set_url($url);

require_login($course->id, false, $cm);

$contextmodule = get_context_instance(CONTEXT_MODULE, $cm->id);

require_capability('mod/scorm:viewreport', $contextmodule);

add_to_log($course->id, 'scorm', 'report', 'report.php?id='.$cm->id, $scorm->id, $cm->id);
$userdata = null;
if (!empty($download)) {
    $noheader = true;
}
/// Print the page header
if (empty($noheader)) {

    $strreport = get_string('report', 'scorm');
    $strattempt = get_string('attempt', 'scorm');

    $PAGE->set_title("$course->shortname: ".format_string($scorm->name));
    $PAGE->set_heading($course->fullname);
    $PAGE->navbar->add($strreport, new moodle_url('/mod/scorm/report.php', array('id'=>$cm->id)));

    echo $OUTPUT->header();
    $currenttab = 'reports';
    require($CFG->dirroot . '/mod/scorm/tabs.php');
    echo $OUTPUT->heading(format_string($scorm->name));
}

if ($action == 'delete' && has_capability('mod/scorm:deleteresponses', $contextmodule) && confirm_sesskey()) {
    if (scorm_delete_responses($attemptids, $scorm)) { //delete responses.
        add_to_log($course->id, 'scorm', 'delete attempts', 'report.php?id=' . $cm->id, implode(",", $attemptids), $cm->id);
        echo $OUTPUT->notification(get_string('scormresponsedeleted', 'scorm'), 'notifysuccess');
    }
}
$reportlist = scorm_report_list($contextmodule);
if (count($reportlist)==0){
    print_error('erroraccessingreport', 'scorm');
}

if (empty($mode)) {
// Default to listing of plugins.
    foreach ($reportlist as $reportname) {
        $reportclassname = "scorm_{$reportname}_report";
        $report = new $reportclassname();
        $html = $report->canview($id,$contextmodule);
        if (!empty($html)) {
            echo '<div class="plugin">';
            echo $html;
            echo '</div>';
       }
    }
//end of default mode condition.
} else if (!in_array($mode, $reportlist)){
    print_error('erroraccessingreport', 'scorm');
}
// Open the selected Scorm report and display it

// DISPLAY PLUGIN REPORT
if(!empty($mode))
{
    $reportclassname = "scorm_{$mode}_report";
    if (!class_exists($reportclassname)) {
        print_error('reportnotfound', 'scorm', '', $mode);
    }
    $report = new $reportclassname();
    
    if (!$report->display($scorm, $cm, $course, $attemptids, $action, $download)) { // Run the report!
        print_error("preprocesserror", 'scorm');
    }
    if (!$report->settings($scorm, $cm, $course)) { // Run the report!
        print_error("preprocesserror", 'scorm');
    }
}

// Print footer

if (empty($noheader)) {
    echo $OUTPUT->footer();
}
