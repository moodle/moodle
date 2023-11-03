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

/** LearnerScript
 * A Moodle block for creating LearnerScript
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
require_once "../../config.php";
use block_learnerscript\form;
use block_learnerscript\local\ls;

$id 		= optional_param('id', 0, PARAM_INT);
$courseid 	= optional_param('courseid', SITEID, PARAM_INT);
$delete 	= optional_param('delete', 0, PARAM_BOOL);
$confirm 	= optional_param('confirm', 0, PARAM_BOOL);
$show 		= optional_param('show', 0, PARAM_BOOL);
$hide 		= optional_param('hide', 0, PARAM_BOOL);
$duplicate 	= optional_param('duplicate', 0, PARAM_BOOL);

$report = null;

$learnerscript = get_config('block_learnerscript', 'ls_serialkey');
if (empty($learnerscript)) {
	throw new moodle_exception(get_string('licencekeyrequired','block_learnerscript'));
	exit();
}
$lsreportconfigstatus = get_config('block_learnerscript', 'lsreportconfigstatus');
if (!$lsreportconfigstatus) {
    redirect(new moodle_url($CFG->wwwroot . '/blocks/learnerscript/lsconfig.php?import=1'));
    exit;
}

if (!$course = $DB->get_record("course", array("id" => $courseid))) {
	print_error("nosuchcourseid", 'block_learnerscript');
}

// Force user login in course (SITE or Course)
if ($course->id == SITEID) {
	require_login();
	$context = context_system::instance();
} else {
	require_login($course->id);
	$context = context_course::instance($course->id);
}

if (!has_capability('block/learnerscript:managereports', $context) && !has_capability('block/learnerscript:manageownreports', $context)) {
	print_error('badpermissions', 'block_learnerscript');
}

$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');

$PAGE->requires->data_for_js("M.cfg.accessls", $learnerscript , true);
// $PAGE->requires->js(new moodle_url('https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'));
$PAGE->requires->jquery_plugin('ui-css');

if ($id) {
	if (!$report = $DB->get_record('block_learnerscript', array('id' => $id))) {
		print_error('reportdoesnotexists', 'block_learnerscript');
	}

	if (!has_capability('block/learnerscript:managereports', $context) && $report->ownerid != $USER->id) {
		print_error('badpermissions', 'block_learnerscript');
	}

	$title = format_string($report->name);

	$courseid = $report->courseid;
	if (!$course = $DB->get_record("course", array("id" => $courseid))) {
		print_error("nosuchcourseid", 'block_learnerscript');
	}
	require_once $CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php';

	$properties = new stdClass();
	$properties->courseid = $courseid;
	$properties->start = 0;
	$properties->length = 10;
	$properties->search = '';
	$properties->filters = array();
	$properties->ls_startdate = 0;
	$properties->ls_enddate = time();

	$reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
	$reportclass = new $reportclassname($report->id, $properties);
	$PAGE->set_url('/blocks/learnerscript/editreport.php', array('id' => $id));
} else {
	$title = get_string('newreport', 'block_learnerscript');
	$PAGE->set_url('/blocks/learnerscript/editreport.php', null);
}

if ($report) {
	$title = format_string($report->name);
} else {
	$title = get_string('report', 'block_learnerscript');
}

$courseurl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $courseid));
$PAGE->navbar->add($course->shortname, $courseurl);

if (!empty($report->courseid)) {
	$params = array('courseid' => $report->courseid);
} else {
	$params = array('courseid' => $courseid);
}

$managereporturl = new moodle_url($CFG->wwwroot . '/blocks/learnerscript/managereport.php');
$PAGE->navbar->add(get_string('managereports', 'block_learnerscript'), $managereporturl);

$PAGE->navbar->add($title);

// Common actions
if (($show || $hide) && confirm_sesskey()) {
	$visible = ($show) ? 1 : 0;
	if (!$DB->set_field('block_learnerscript', 'visible', $visible, array('id' => $report->id))) {
		print_error('cannotupdatereport', 'block_learnerscript');
	}
	header("Location: $CFG->wwwroot/blocks/learnerscript/managereport.php");
	die;
}

if ($duplicate && confirm_sesskey()) {
	$newreport = new stdclass();
	$newreport = $report;
	unset($newreport->id);
	$newreport->name = get_string('copyasnoun') . ' ' . $newreport->name;
	$newreport->summary = $newreport->summary;
	if (!$newreportid = $DB->insert_record('block_learnerscript', $newreport)) {
		print_error('cannotduplicate', 'block_learnerscript');
	}
	header("Location: $CFG->wwwroot/blocks/learnerscript/managereport.php");
	die;
}

if ($delete && confirm_sesskey()) {
	if (!$confirm) {
		$PAGE->set_title($title);
		$PAGE->set_heading($title);
		$PAGE->set_cacheable(true);
		echo $OUTPUT->header();
		echo "<script src='https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'></script>";
		$message = get_string('confirmdeletereport', 'block_learnerscript');
		$optionsyes = array('id' => $report->id, 'delete' => $delete, 'sesskey' => sesskey(), 'confirm' => 1);
		$optionsno = array();
		$buttoncontinue = new single_button(new moodle_url('editreport.php', $optionsyes), get_string('yes'), 'get');
		$buttoncancel = new single_button(new moodle_url('managereport.php', $optionsno), get_string('no'), 'get');
		echo $OUTPUT->confirm($message, $buttoncontinue, $buttoncancel);
		echo $OUTPUT->footer();
		exit;
	} else {
		(new ls)->delete_report($report,$context);
		header("Location: $CFG->wwwroot/blocks/learnerscript/managereport.php");
		die;
	}
}


if (!empty($report)) {
	$editform = new block_learnerscript\form\report_edit_form('editreport.php', compact('report', 'courseid', 'context'));
} else {
	$editform = new block_learnerscript\form\report_edit_form('editreport.php', compact('courseid', 'context'));
}

if (!empty($report)) {
    $components = (new ls)->cr_unserialize($reportclass->config->components);
    $sqlconfig = (isset($components['customsql']['config'])) ? $components['customsql']['config'] : array();
    if(!empty($sqlconfig->querysql)){
    	$report->querysql = $sqlconfig->querysql;
	}
	$report->description['text'] = $report->summary;
	$editform->set_data($report);
}

if ($editform->is_cancelled()) {
	if (!empty($report)) {
		redirect($CFG->wwwroot . '/blocks/learnerscript/viewreport.php?id=' . $report->id);
	} else {
		redirect($CFG->wwwroot . '/blocks/learnerscript/managereport.php');
	}

} else if ($data = $editform->get_data()) {
	// $data->export = isset($data->exportformats) ? implode(',', array_keys($data->exportformats)) : '';
	if (empty($report)) {
		$data->ownerid = $USER->id;
		$data->courseid = $courseid;
		$data->visible = 1;
		if ($data->type == 'sql' && !has_capability('block/learnerscript:managesqlreports', $context)) {
			print_error('nosqlpermissions');
		}
		$data->id = (new ls)->add_report($data,$context);
	} else {
		$data->type = $report->type;
		(new ls)->update_report($data,$context);
	}
	if($data->type == 'statistics'){
		redirect($CFG->wwwroot . '/blocks/learnerscript/viewreport.php?id=' . $data->id . '');
    } else{
		redirect($CFG->wwwroot . '/blocks/learnerscript/design.php?id=' . $data->id . '');
    }
}

$PAGE->set_context($context);

$PAGE->set_pagelayout('incourse');

$PAGE->set_title($title);

$PAGE->set_heading($title);

$PAGE->set_cacheable(true);

echo $OUTPUT->header();
echo "<script src='https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'></script>";
echo html_writer::start_tag('div',array('id' => 'licenceresult', 'class' => 'lsacccess'));

if ($id) {
	$renderer = $PAGE->get_renderer('block_learnerscript');
	if (has_capability('block/learnerscript:managereports', $context) ||
    (has_capability('block/learnerscript:manageownreports', $context)) && $report->ownerid == $USER->id) {
	    // $plots = (new block_learnerscript\local\ls)->get_components_data($report->id, 'plot');
	    $plots = false;
	    $calcbutton = false;
	    $plotoptions = new \block_learnerscript\output\plotoption($plots, $report->id, $calcbutton,'editicon');
	    echo $renderer->render($plotoptions);
	}
}
$editform->display();

echo html_writer::end_tag('div');
echo $OUTPUT->footer();
