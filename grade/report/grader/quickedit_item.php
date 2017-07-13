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
 * Allow the editing of grades for a grade item
 *
 * @package   gradereport_grader
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/grader/lib.php';

$courseid      = required_param('id', PARAM_INT);        // course id
$itemid        = required_param('itemid', PARAM_INT);        // item id
$page          = optional_param('page', 0, PARAM_INT);   // active page
$perpageurl    = optional_param('perpage', 0, PARAM_INT);

$url = new moodle_url('/grade/report/grader/quickedit_item.php', array('id'=>$courseid, 'itemid'=>$itemid));
if ($page !== 0) {
    $url->param('page', $page);
}
if ($perpage !== 0) {
    $url->param('perpage', $perpage);
}
$PAGE->set_url($url);


/// basic access checks
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}

if (!$item = $DB->get_record('grade_items', array('id' => $itemid))) {
    print_error('noitemid', 'grades');
}

require_login($course);
$context = context_course::instance($course->id);

require_capability('gradereport/grader:view', $context);
require_capability('moodle/grade:viewall', $context);
require_capability('moodle/grade:edit', $context);

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'grader', 'courseid'=>$courseid, 'page'=>$page));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'grader';

// Initialise the grader report object
$report = new grade_report_grader($courseid, $gpr, $context, $page);

/// processing posted grades & feedback here
if ($data = data_submitted() and confirm_sesskey() and has_capability('moodle/grade:edit', $context)) {
    $warnings = $report->process_data($data);
} else {
    $warnings = array();
}

// Override perpage if set in URL
if ($perpageurl) {
    $report->user_prefs['studentsperpage'] = $perpageurl;
}

// final grades MUST be loaded after the processing
$report->load_users();
$numusers = $report->get_numusers();
$report->load_final_grades();

/// Print header
$a->item = $item->itemname;
$reportname = get_string('quickedititem', 'gradereport_grader', $a);
print_grade_page_head($COURSE->id, 'report', 'grader', $reportname);

echo $report->group_selector;
echo '<div class="clearer"></div>';

//show warnings if any
foreach($warnings as $warning) {
    echo $OUTPUT->notification($warning);
}

$studentsperpage = $report->get_pref('studentsperpage');
// Don't use paging if studentsperpage is empty or 0 at course AND site levels
if (!empty($studentsperpage)) {
    echo $OUTPUT->paging_bar($numusers, $report->page, $studentsperpage, $report->pbarurl);
}

/// TODO Print links to previous - next grade items in this course
/// TODO Print Quick Edit Interface here
/// TODO The teacher may only be allowed to view one group: check capabilities

// print submit button
echo '<div class="submit"><input type="submit" value="'.get_string('update').'" /></div>';
echo '</div></form>';

// prints paging bar at bottom for large pages
if (!empty($studentsperpage) && $studentsperpage >= 20) {
    echo $OUTPUT->paging_bar($numusers, $report->page, $studentsperpage, $report->pbarurl);
}

echo $OUTPUT->footer();

