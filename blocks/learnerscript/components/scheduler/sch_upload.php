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
 * Bulk user schedule script from a comma separated file
 *
 * @package    block
 * @subpackage LearnerScript
 * @copyright  eAbyas Info Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once('sch_upload_lib.php');
require_once('sch_upload_form.php');
use block_learnerscript\local\ls as ls;
use block_learnerscript\local\schedule;
$iid = optional_param('iid', 0, PARAM_INT);
$reportid = optional_param('id', 0, PARAM_INT);
$courseid = optional_param('courseid', SITEID, PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);

@set_time_limit(60 * 60); // 1 hour should be enough.
raise_memory_limit(MEMORY_HUGE);

require_login();

$learnerscript = get_config('block_learnerscript', 'ls_serialkey');
if (empty($learnerscript)) {
    throw new moodle_exception("License Key Is Required");
    exit();
}

$errorstr = get_string('error');
$stryes = get_string('yes');
$strno = get_string('no');
$stryesnooptions = array(0 => $strno, 1 => $stryes);

global $USER, $DB, $PAGE, $OUTPUT;
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/blocks/learnerscript/components/scheduler/sch_upload.php?id=' . $reportid);
$PAGE->set_heading($SITE->fullname);
$strheading = get_string('pluginname', 'block_learnerscript') . ' : ' . get_string('uploadusers', 'block_learnerscript');
$PAGE->set_title($strheading);

$PAGE->requires->data_for_js("M.cfg.accessls", $learnerscript, true);

// $PAGE->requires->js(new moodle_url('https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'));
$PAGE->requires->jquery_plugin('ui-css');

if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
	print_error('reportdoesnotexists', 'block_learnerscript');
}

$PAGE->navbar->add($report->name, new moodle_url('/blocks/learnerscript/viewreport.php',
					array('id' => $reportid, 'courseid' => $courseid)));
$PAGE->navbar->add(get_string('uploadusers', 'block_learnerscript'));
$returnurl = new moodle_url('/blocks/learnerscript/components/scheduler/schedule.php?id=' . $reportid . '&courseid=' . $courseid);
$returnurl1 = new moodle_url('/blocks/learnerscript/components/scheduler/sch_upload.php?id=' . $reportid . '&courseid=' . $courseid);
$stdfields = array('Email', 'Export Format', 'Export to filesystem', 'Frequency', 'Schedule', 'Role', 'Contextlevel');
$prffields = array();
$samplefields = array(
	'email' => 'Email',
	'exportformat' => 'Export Format',
	'exporttofilesystem' => 'Export to filesystem',
	'frequency' => 'Frequency',
	'schedule' => 'Schedule',
	'roleid' => 'Role', 
	'contextlevel' => 'Contextlevel'
);

$scheduling = new schedule();

$mform1 = new bulkschreports($CFG->wwwroot . '/blocks/learnerscript/components/scheduler/sch_upload.php?id=' . $reportid, array('reportid' => $reportid));
if ($mform1->is_cancelled()) {
	redirect($returnurl);
} elseif ($formdata = $mform1->get_data()) {
	echo $OUTPUT->header();
	echo "<script src='https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'></script>";
	$iid = csv_import_reader::get_new_iid('uploaduser');
	$cir = new csv_import_reader($iid, 'uploaduser');
	$content = $mform1->get_file_content('userfile');
	$readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name);
	unset($content);
	if ($readcount === false) {
		print_error('csvloaderror', '', $returnurl);
	} else if ($readcount == 0) {
		print_error('csvemptyfile', 'error', $returnurl);
	}
	// Test if columns ok(to validate the csv file content).
	// $filecolumns = uu_validate_user_upload_columns($cir, $stdfields, $prffields, $returnurl);
	$linenum = 1;
	$subline = 1;
	$errorscount = 0;
	$mfieldscount = 0;
	$successcreatedcount = 0;
	$reportclass = (new ls)->create_reportclass($reportid);
	// Test if columns ok(to validate the csv file content).
	$filecolumns = uu_validate_user_upload_columns($cir, $stdfields, $prffields, $returnurl);
	$upt = New uu_progress_tracker();
	$cir->init();
	loop:
	while ($line = $cir->next()) {
		$upt->flush();
		$linenum++;
		$schedule_data = new stdClass();
		// Add fields to user object.
		foreach ($line as $keynum => $value) {
			if (!isset($filecolumns[$keynum])) {
				// This should not happen.
				continue;
			}
			$k = $filecolumns[$keynum];
			$key = array_search($k, $samplefields);
			$schedule_data->$key = $value;
		}

		// Add default values for remaining fields.
		$formdefaults = array();
		foreach ($stdfields as $field) {
			if (isset($schedule_data->$field)) {
				continue;
			}
			// All validation moved to form2.
			if (isset($formdata->$field)) {
				// Process templates.
				$formdefaults[$field] = true;
			}
		}
		foreach ($prffields as $field) {
			if (isset($schedule_data->$field)) {
				continue;
			}
			if (isset($formdata->$field)) {
				// Process templates.
				$formdefaults[$field] = true;
			}
		}
		$validations = formatdata_validation($reportid, $schedule_data, $linenum, $formatteddata, $reportclass);
		if (count($validations['errors']) > 0) {
			echo implode(' ', $validations['errors']);
		}
		if (!empty($validations['errors']) > 0 || !empty($validations['mfields']) > 0) {
			$errorscount++;
			$mfieldscount++;
		} else {
			$formatteddata->reportid = $reportid;
			$formatteddata->userid = $USER->id;
			$formatteddata->timecreated = time();
			$formatteddata->timemodified = time();
			$formatteddata->nextschedule = $scheduling->next($formatteddata);
			$uploadusers = $DB->insert_record('block_ls_schedule', $formatteddata);
			if ($uploadusers) {
				$successcreatedcount++;
			}
		}

	}

	$cir->cleanup(true);

	echo $OUTPUT->box_start('boxwidthnarrow boxaligncenter generalbox', 'uploadresults');
	echo '<div class="panel panel-primary">';
	if ($successcreatedcount > 0) {
		echo '<div class="alert alert-success" role="alert">' . $successcreatedcount . ' record(s) successfully created.</div>';'<h6 style= "color:red;"> ' . ($linenum - 1) . '   Users are updated  </h6>';
	}
	if ($mfieldscount > 0) {
		echo '<div class="panel-body">' . get_string('uploaderrors', 'block_learnerscript') . ': ' . $mfieldscount . '</div>';
	}

	echo '</div>';
	if ($mfieldscount > 0) {
		echo '<h4> Please fill the sheet without any errors. Refer Help Manual for assistance.</h4>';
	}

	echo $OUTPUT->box_end();

	echo '<div class="text-center"><a href="' . $CFG->wwwroot . '/blocks/learnerscript/components/scheduler/schedule.php?id=' . $reportid . '&courseid=' . $courseid . '"><button>Continue</button></a></div>' . '<br />';
	echo $OUTPUT->footer();
	die;

	// Continue to form2.
} else {
	echo $OUTPUT->header();
	echo "<script src='https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'></script>";
	echo $OUTPUT->heading(get_string('uploadusers', 'block_learnerscript'));
	echo '<div  class="samplecsv"><a href="'.$CFG->wwwroot.'/blocks/learnerscript/components/scheduler/sch_sample.php?format=csv&id=' . $reportid . '"><button>' . get_string('sample_csv', 'block_learnerscript') . '</button></a><a href="'.$CFG->wwwroot.'/blocks/learnerscript/components/scheduler/help.php?id=' . $reportid . '"><button>' . get_string('manual', 'block_learnerscript') . '</button></a></div>';
	$mform1->display();

	echo $OUTPUT->footer();
	die;
}
