<?php // $Id$

/// This creates and handles the whole user report interface, sans header and footer

require_once($CFG->dirroot.'/grade/report/user/lib.php');

// get the params
if (!$userid = optional_param('user', 0, PARAM_INT)) {
    // current user
    $userid = $USER->id;
}

// Create a report instance
$report = new grade_report_user($courseid, $gpr, $context, $userid);

// find total number of participants
$numusers = $report->get_numusers();

$gradetotal = 0;
$gradesum = 0;

// print the page
print_heading(get_string('modulename', 'gradereport_user'). " - ".fullname($report->user));

if ($report->fill_table()) {
    echo $report->print_table(true);
}
?>
