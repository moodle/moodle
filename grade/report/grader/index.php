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
 * The gradebook grader report
 *
 * @package   gradereport_grader
 * @copyright 2007 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/user/renderer.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/report/grader/lib.php');

$courseid      = required_param('id', PARAM_INT);        // course id
$page          = optional_param('page', 0, PARAM_INT);   // active page
$edit          = optional_param('edit', -1, PARAM_BOOL); // sticky editting mode

$sortitemid    = optional_param('sortitemid', 0, PARAM_ALPHANUMEXT);
$sort          = optional_param('sort', '', PARAM_ALPHA);
$action        = optional_param('action', 0, PARAM_ALPHAEXT);
$move          = optional_param('move', 0, PARAM_INT);
$type          = optional_param('type', 0, PARAM_ALPHA);
$target        = optional_param('target', 0, PARAM_ALPHANUM);
$toggle        = optional_param('toggle', null, PARAM_INT);
$toggle_type   = optional_param('toggle_type', 0, PARAM_ALPHANUM);

$graderreportsifirst  = optional_param('sifirst', null, PARAM_NOTAGS);
$graderreportsilast   = optional_param('silast', null, PARAM_NOTAGS);

$studentsperpage = optional_param('perpage', null, PARAM_INT);

$PAGE->set_url(new moodle_url('/grade/report/grader/index.php', array('id'=>$courseid)));
$PAGE->set_pagelayout('report');
$PAGE->requires->js_call_amd('gradereport_grader/stickycolspan', 'init');
$PAGE->requires->js_call_amd('gradereport_grader/search', 'init');
$PAGE->requires->js_call_amd('gradereport_grader/feedback_modal', 'init');

// basic access checks
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    throw new \moodle_exception('invalidcourseid');
}
require_login($course);
$context = context_course::instance($course->id);

// The report object is recreated each time, save search information to SESSION object for future use.
if (isset($graderreportsifirst)) {
    $SESSION->gradereport["filterfirstname-{$context->id}"] = $graderreportsifirst;
}
if (isset($graderreportsilast)) {
    $SESSION->gradereport["filtersurname-{$context->id}"] = $graderreportsilast;
}

if (isset($studentsperpage) && $studentsperpage >= 0) {
    set_user_preference('grade_report_studentsperpage', $studentsperpage);
}

require_capability('gradereport/grader:view', $context);
require_capability('moodle/grade:viewall', $context);

// return tracking object
$gpr = new grade_plugin_return(
    array(
        'type' => 'report',
        'plugin' => 'grader',
        'course' => $course,
        'page' => $page
    )
);

// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'grader';

// Build editing on/off buttons.
$buttons = '';

$PAGE->set_other_editing_capability('moodle/grade:edit');
if ($PAGE->user_allowed_editing() && !$PAGE->theme->haseditswitch) {
    if ($edit != - 1) {
        $USER->editing = $edit;
    }

    // Page params for the turn editing on button.
    $options = $gpr->get_options();
    $buttons = $OUTPUT->edit_button(new moodle_url($PAGE->url, $options), 'get');
}

$gradeserror = array();

// Handle toggle change request
if (!is_null($toggle) && !empty($toggle_type)) {
    set_user_preferences(array('grade_report_show'.$toggle_type => $toggle));
}

// Perform actions
if (!empty($target) && !empty($action) && confirm_sesskey()) {
    grade_report_grader::do_process_action($target, $action, $courseid);
}

// Do this check just before printing the grade header (and only do it once).
grade_regrade_final_grades_if_required($course);

//Initialise the grader report object that produces the table
//the class grade_report_grader_ajax was removed as part of MDL-21562
if ($sort && strcasecmp($sort, 'desc') !== 0) {
    $sort = 'asc';
}
// We have lots of hardcoded 'ASC' and 'DESC' strings in grade/report/grader.lib :(. So we need to uppercase the sort.
$sort = strtoupper($sort);

$report = new grade_report_grader($courseid, $gpr, $context, $page, $sortitemid, $sort);

// We call this a little later since we need some info from the grader report.
$PAGE->requires->js_call_amd('gradereport_grader/collapse', 'init', [
    'userID' => $USER->id,
    'courseID' => $courseid,
    'defaultSort' => $report->get_default_sortable()
]);

$numusers = $report->get_numusers(true, true);

$actionbar = new \gradereport_grader\output\action_bar($context, $report, $numusers);
print_grade_page_head($COURSE->id, 'report', 'grader', false, false, $buttons, true,
    null, null, null, $actionbar, false);

// make sure separate group does not prevent view
if ($report->currentgroup == -2) {
    echo $OUTPUT->heading(get_string("notingroup"));
    echo $OUTPUT->footer();
    exit;
}

$warnings = [];
$isediting = has_capability('moodle/grade:edit', $context) && isset($USER->editing) && $USER->editing;
if ($isediting && ($data = data_submitted()) && confirm_sesskey()) {
    // Processing posted grades here.
    $warnings = $report->process_data($data);
}

// Final grades MUST be loaded after the processing.
$report->load_users();
$report->load_final_grades();

//show warnings if any
foreach ($warnings as $warning) {
    echo $OUTPUT->notification($warning);
}

$displayaverages = true;
if ($numusers == 0) {
    $displayaverages = false;
}

$reporthtml = $report->get_grade_table($displayaverages);

$studentsperpage = $report->get_students_per_page();

// Print per-page dropdown.
$pagingoptions = grade_report_grader::PAGINATION_OPTIONS;
if ($studentsperpage) {
    $pagingoptions[] = $studentsperpage; // To make sure the current preference is within the options.
}
$pagingoptions = array_unique($pagingoptions);
sort($pagingoptions);
$pagingoptions = array_combine($pagingoptions, $pagingoptions);
if ($numusers > grade_report_grader::MAX_STUDENTS_PER_PAGE) {
    $pagingoptions['0'] = grade_report_grader::MAX_STUDENTS_PER_PAGE;
} else {
    $pagingoptions['0'] = get_string('all');
}

$perpagedata = [
    'baseurl' => new moodle_url('/grade/report/grader/index.php', ['id' => s($courseid), 'report' => 'grader']),
    'options' => []
];
foreach ($pagingoptions as $key => $name) {
    $perpagedata['options'][] = [
        'name' => $name,
        'value' => $key,
        'selected' => $key == $studentsperpage,
    ];
}

$footercontent = html_writer::div(
    $OUTPUT->render_from_template('gradereport_grader/perpage', $perpagedata)
    , 'col-auto'
);

// The number of students per page is always limited even if it is claimed to be unlimited.
$studentsperpage = $studentsperpage ?: grade_report_grader::MAX_STUDENTS_PER_PAGE;
$footercontent .= html_writer::div(
    $OUTPUT->paging_bar($numusers, $report->page, $studentsperpage, $report->pbarurl),
    'col'
);

// print submit button
if (!empty($USER->editing) && $report->get_pref('quickgrading')) {
    echo '<form action="index.php" enctype="application/x-www-form-urlencoded" method="post" id="gradereport_grader">'; // Enforce compatibility with our max_input_vars hack.
    echo '<div>';
    echo '<input type="hidden" value="'.s($courseid).'" name="id" />';
    echo '<input type="hidden" value="'.sesskey().'" name="sesskey" />';
    echo '<input type="hidden" value="'.time().'" name="timepageload" />';
    echo '<input type="hidden" value="grader" name="report"/>';
    echo '<input type="hidden" value="'.$page.'" name="page"/>';
    echo $gpr->get_form_fields();
    echo $reporthtml;

    $footercontent .= html_writer::div(
        '<input type="submit" id="gradersubmit" class="btn btn-primary" value="'.s(get_string('savechanges')).'" />',
        'col-auto'
    );

    $stickyfooter = new core\output\sticky_footer($footercontent);
    echo $OUTPUT->render($stickyfooter);

    echo '</div></form>';
} else {
    echo $reporthtml;

    $stickyfooter = new core\output\sticky_footer($footercontent);
    echo $OUTPUT->render($stickyfooter);
}

$event = \gradereport_grader\event\grade_report_viewed::create(
    array(
        'context' => $context,
        'courseid' => $courseid,
    )
);
$event->trigger();

echo $OUTPUT->footer();
