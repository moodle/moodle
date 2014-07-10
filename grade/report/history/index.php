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
 * The gradebook grade history report
 *
 * @package    gradereport_history
 * @copyright  2013 NetSpot Pty Ltd (https://www.netspot.com.au)
 * @author     Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/user/renderer.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/report/history/lib.php');
require_once($CFG->libdir.'/csvlib.class.php');

$courseid      = required_param('id', PARAM_INT);        // Course id.
$page          = optional_param('page', 0, PARAM_INT);   // Active page.
$sortitemid    = optional_param('sortitemid', 0, PARAM_ALPHANUM);
$export = optional_param('exportbutton', false, PARAM_BOOL);

$PAGE->set_pagelayout('report');
$PAGE->set_url(new moodle_url('/grade/report/history/index.php', array('id' => $courseid)));

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
require_login($course);
$context = context_course::instance($course->id);

require_capability('gradereport/history:view', $context);
require_capability('moodle/grade:viewall', $context);

// Return tracking object.
$gpr = new grade_plugin_return(array('type' => 'report', 'plugin' => 'history', 'courseid' => $courseid, 'page' => $page));

// Last selected report session tracking.
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'history';

$select = "itemtype != 'course' AND itemname != '' AND courseid = :courseid";
$itemids = $DB->get_records_select_menu('grade_items', $select, array('courseid' => $course->id), 'itemname ASC', 'id, itemname');
$itemids = array(0 => get_string('allgradeitems', 'gradereport_history')) + $itemids;

$sql = "SELECT u.id, ".$DB->sql_concat('u.lastname', "' '", 'u.firstname')."
        FROM {user} u
        JOIN {grade_grades_history} ggh ON ggh.usermodified = u.id
        JOIN {grade_items} gi ON gi.id = ggh.itemid
        WHERE gi.courseid = :courseid
        GROUP BY u.id
        ORDER BY u.lastname ASC, u.firstname ASC";

$graders = $DB->get_records_sql_menu($sql, array('courseid' => $course->id));
$graders = array(0 => get_string('allgraders', 'gradereport_history')) + $graders;

$output = $PAGE->get_renderer('gradereport_history');

$button = grade_report_history::get_user_select_button($course->id);
$params = array('course' => $course, 'itemids' => $itemids, 'graders' => $graders, 'userbutton' => null);
$mform = new gradereport_history_filter_form(null, $params);
$filters = array();
if ($data = $mform->get_data()) {
    $filters = (array)$data;

    if (!empty($filters['datetill'])) {
        $filters['datetill'] += DAYSECS - 1; // Set to end of the chosen day.
    }
} else {
    $filters = array(
        'id' => $courseid,
        'userids' => optional_param('userids', '', PARAM_SEQUENCE),
        'itemid' => optional_param('itemid', 0, PARAM_INT),
        'grader' => optional_param('grader', 0, PARAM_INT),
        'datefrom' => optional_param('datefrom', 0, PARAM_INT),
        'datetill' => optional_param('datetill', 0, PARAM_INT),
        'revisedonly' => optional_param('revisedonly', 0, PARAM_INT),
    );
}



$report = new grade_report_history($courseid, $gpr, $context, $filters, $page, $sortitemid);

$report->load_users();

$historytable = $report->get_history_table();
$numrows = $report->numrows;

$names = array();
foreach ($report->get_selected_users() as $key => $user) {
    $names[$key] = $user->firstname.' '.$user->lastname;
}
$filters['userfullnames'] = implode(',', $names);

// Now that we have the names, reinitialise the button so its able to control them.
$button = grade_report_history::get_user_select_button($course->id, $names);
$userbutton = $output->render_select_user_button($button);
$params = array('course' => $course, 'itemids' => $itemids, 'graders' => $graders, 'userbutton' => $userbutton);
$mform = new gradereport_history_filter_form(null, $params);
$mform->set_data($filters);

if ($export) {
    $filename = $COURSE->shortname;

    $data = $report->get_table_data();
    csv_export_writer::download_array($filename, $data);
}

$reportname = $output->report_title($report->get_selected_users());
// Print header.
print_grade_page_head($COURSE->id, 'report', 'history', $reportname, false, '');

if (!empty($report->perpage) && $report->perpage < $report->numrows) {
    echo $OUTPUT->paging_bar($numrows, $report->page, $report->perpage, $report->pbarurl);
}

$mform->display();
echo $historytable;

// Prints paging bar at bottom for large pages.
if (!empty($report->perpage) && $report->perpage < $report->numrows) {
    echo $OUTPUT->paging_bar($numrows, $report->page, $report->perpage, $report->pbarurl);
}
echo $OUTPUT->footer();
