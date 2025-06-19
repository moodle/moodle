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
 * Gradebook rubrics report
 * @package    gradereport_rubrics
 * @copyright  2014 Learning Technology Services, www.lts.ie - Lead Developer: Karen Holland
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir .'/gradelib.php');
require_once($CFG->dirroot.'/grade/lib.php');
use gradereport_rubrics\report;
require_once("select_form.php");

$activityid = optional_param('activityid', 0, PARAM_INT);
$displaylevel = optional_param('displaylevel', 1, PARAM_INT);
$displayremark = optional_param('displayremark', 1, PARAM_INT);
$displaysummary = optional_param('displaysummary', 1, PARAM_INT);
$displayidnumber = optional_param('displayidnumber', 1, PARAM_INT);
$displayemail = optional_param('displayemail', 1, PARAM_INT);
$format = optional_param('format', '', PARAM_ALPHA);
$courseid = required_param('id', PARAM_INT); // Course id.

if (!$course = get_course($courseid)) {
    throw new moodle_exception(get_string('invalidcourseid', 'gradereport_rubrics'));
}

// CSV format.
$excel = $format == 'excelcsv';
$csv = $format == 'csv' || $excel;

if (!$csv) {
    $PAGE->set_url(new moodle_url('/grade/report/rubrics/index.php', array('id' => $courseid)));
}

require_login($courseid);
if (!$csv) {
    $PAGE->set_pagelayout('report');
}

$context = context_course::instance($course->id);

require_capability('gradereport/rubrics:view', $context);

$activityname = '';

// Set up the form.
$mform = new report_rubrics_select_form(null, array('courseid' => $courseid));

// Did we get anything from the form?
if ($formdata = $mform->get_data()) {
    // Get the users rubrics.
    $activityid = $formdata->activityid;
}

if ($activityid != 0) {
    $cm = get_fast_modinfo($courseid)->cms[$activityid];
    $activityname = format_string($cm->name, true, ['context' => $context]);
    // Determine whether or not to display general feedback.
    $displayfeedback = report::GRADABLES[$cm->modname]['showfeedback'] ?? false;
}

if (!$csv) {
    print_grade_page_head($COURSE->id, 'report', 'rubrics',
        get_string('pluginname', 'gradereport_rubrics') .
        $OUTPUT->help_icon('pluginname', 'gradereport_rubrics'));

    // Display the form.
    $mform->display();

    grade_regrade_final_grades($courseid); // First make sure we have proper final grades.
}

$gpr = new grade_plugin_return(array('type' => 'report', 'plugin' => 'grader',
    'courseid' => $courseid)); // Return tracking object.
$report = new report(
    $courseid,
    $gpr,
    $context,
    $activityid,
    $format,
    $format == 'excelcsv',
    $format == 'csv' || $excel,
    ($displaylevel == 1),
    ($displayremark == 1),
    ($displaysummary == 1),
    ($displayidnumber == 1),
    ($displayemail == 1),
    $activityname,
    $displayfeedback ?? false,
    null
); // Initialise the grader report object.

$table = $report->show();
echo $table;

echo $OUTPUT->footer();
