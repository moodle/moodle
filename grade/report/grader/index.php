<?php // $Id$

/// This creates and handles the whole grader report interface, sans header and footer

require_once($CFG->dirroot.'/grade/report/grader/grader_report.php');
$gradeserror = array();

// get the params ($report, $courseid and $context are already set in grade/report.php)
$page          = optional_param('page', 0, PARAM_INT);
$sortitemid    = optional_param('sortitemid', 0, PARAM_ALPHANUM); // sort by which grade item
$action        = optional_param('action', 0, PARAM_ALPHA);
$move          = optional_param('move', 0, PARAM_INT);
$type          = optional_param('type', 0, PARAM_ALPHA);
$target        = optional_param('target', 0, PARAM_ALPHANUM);
$toggle        = optional_param('toggle', NULL, PARAM_INT);
$toggle_type   = optional_param('toggle_type', 0, PARAM_ALPHANUM);

// Handle toggle change request
if (!is_null($toggle) && !empty($toggle_type)) {
    set_user_preferences(array('grade_report_show' . $toggle_type => $toggle));
}

// Initialise the grader report object
$report = new grade_report_grader($courseid, $context, $page, $sortitemid);

/// processing posted grades & feedback here
if ($data = data_submitted() and confirm_sesskey()) {
    $report->process_data($data);
}

// Override perpage if set in URL
if ($perpageurl = optional_param('perpage', 0, PARAM_INT)) {
    $report->user_prefs['studentsperpage'] = $perpageurl;
}

// Perform actions on categories, items and grades
if (!empty($target) && !empty($action) && confirm_sesskey()) {
    $report->process_action($target, $action);
}

// first make sure we have all final grades
// TODO: check that no grade_item has needsupdate set
grade_regrade_final_grades($courseid);

$report->load_users();
$numusers = $report->get_numusers();
$report->load_final_grades();

if (!$context = get_context_instance(CONTEXT_COURSE, $report->gtree->courseid)) {
    return false;
}

print_heading('Grader Report');

// Add tabs
$currenttab = 'graderreport';
include('tabs.php');

echo $report->group_selector;
echo $report->get_toggles_html();
print_paging_bar($numusers, $report->page, $report->get_pref('studentsperpage'), $report->pbarurl);
echo '<br />';

$reporthtml = '<table class="boxaligncenter">';
$reporthtml .= $report->get_headerhtml();
$reporthtml .= $report->get_scalehtml();
$reporthtml .= $report->get_studentshtml();
$reporthtml .= $report->get_groupsumhtml();
$reporthtml .= $report->get_gradesumhtml();
$reporthtml .= "</table>";
// print submit button
if ($USER->gradeediting) {
    echo '<form action="report.php" method="post">';
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
?>
