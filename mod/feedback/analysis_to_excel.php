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
 * prints an analysed excel-spreadsheet of the feedback
 *
 * @copyright Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */

require_once("../../config.php");
require_once("lib.php");
require_once("$CFG->libdir/excellib.class.php");

$id = required_param('id', PARAM_INT); // Course module id.
$courseid = optional_param('courseid', '0', PARAM_INT);

$url = new moodle_url('/mod/feedback/analysis_to_excel.php', array('id' => $id));
if ($courseid) {
    $url->param('courseid', $courseid);
}
$PAGE->set_url($url);

list($course, $cm) = get_course_and_cm_from_cmid($id, 'feedback');
require_login($course, false, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/feedback:viewreports', $context);

$feedback = $PAGE->activityrecord;

// Buffering any output. This prevents some output before the excel-header will be send.
ob_start();
ob_end_clean();

// Get the questions (item-names).
$feedbackstructure = new mod_feedback_structure($feedback, $cm, $course->id);
if (!$items = $feedbackstructure->get_items(true)) {
    print_error('no_items_available_yet', 'feedback', $cm->url);
}

$mygroupid = groups_get_activity_group($cm);

// Creating a workbook.
$filename = "feedback_" . clean_filename($cm->get_formatted_name()) . ".xls";
$workbook = new MoodleExcelWorkbook($filename);

// Creating the worksheet.
error_reporting(0);
$worksheet1 = $workbook->add_worksheet();
error_reporting($CFG->debug);
$worksheet1->hide_gridlines();
$worksheet1->set_column(0, 0, 10);
$worksheet1->set_column(1, 1, 30);
$worksheet1->set_column(2, 20, 15);

// Creating the needed formats.
$xlsformats = new stdClass();
$xlsformats->head1 = $workbook->add_format(['bold' => 1, 'size' => 12]);
$xlsformats->head2 = $workbook->add_format(['align' => 'left', 'bold' => 1, 'bottum' => 2]);
$xlsformats->default = $workbook->add_format(['align' => 'left', 'v_align' => 'top']);
$xlsformats->value_bold = $workbook->add_format(['align' => 'left', 'bold' => 1, 'v_align' => 'top']);
$xlsformats->procent = $workbook->add_format(['align' => 'left', 'bold' => 1, 'v_align' => 'top', 'num_format' => '#,##0.00%']);

// Writing the table header.
$rowoffset1 = 0;
$worksheet1->write_string($rowoffset1, 0, userdate(time()), $xlsformats->head1);

// Get the completeds.
$completedscount = feedback_get_completeds_group_count($feedback, $mygroupid, $courseid);
if ($completedscount > 0) {
    // Write the count of completeds.
    $rowoffset1++;
    $worksheet1->write_string($rowoffset1,
        0,
        $cm->get_module_type_name(true).': '.strval($completedscount),
        $xlsformats->head1);
}

$rowoffset1++;
$worksheet1->write_string($rowoffset1,
    0,
    get_string('questions', 'feedback').': '. strval(count($items)),
    $xlsformats->head1);

$rowoffset1 += 2;
$worksheet1->write_string($rowoffset1, 0, get_string('item_label', 'feedback'), $xlsformats->head1);
$worksheet1->write_string($rowoffset1, 1, get_string('question', 'feedback'), $xlsformats->head1);
$worksheet1->write_string($rowoffset1, 2, get_string('responses', 'feedback'), $xlsformats->head1);
$rowoffset1++;

foreach ($items as $item) {
    // Get the class of item-typ.
    $itemobj = feedback_get_item_class($item->typ);
    $rowoffset1 = $itemobj->excelprint_item($worksheet1,
        $rowoffset1,
        $xlsformats,
        $item,
        $mygroupid,
        $courseid);
}

$workbook->close();
