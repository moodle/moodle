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
 * Attendance course summary report.
 *
 * @package    mod_attendance
 * @copyright  2017 onwards Dan Marsden http://danmarsden.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mod/attendance/lib.php');
require_once($CFG->dirroot.'/mod/attendance/locallib.php');
require_once($CFG->libdir.'/tablelib.php');

$category = optional_param('category', 0, PARAM_INT);
$attendancecm = optional_param('id', 0, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);
$sort = optional_param('tsort', 'timesent', PARAM_ALPHA);

if (!empty($category)) {
    $context = context_coursecat::instance($category);
    $coursecat = core_course_category::get($category);
    $courses = $coursecat->get_courses(['recursive' => true, 'idonly' => true]);
    $PAGE->set_category_by_id($category);
    require_login();
} else if (!empty($attendancecm)) {
    $cm             = get_coursemodule_from_id('attendance', $attendancecm, 0, false, MUST_EXIST);
    $course         = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $att            = $DB->get_record('attendance', ['id' => $cm->instance], '*', MUST_EXIST);
    $courses = [$course->id];
    $context = context_module::instance($cm->id);
    require_login($course, false, $cm);
} else {
    admin_externalpage_setup('managemodules');
    $context = context_system::instance();
    $courses = []; // Show all courses.
}
// Check permissions.
require_capability('mod/attendance:viewreports', $context);

$exportfilename = 'attendance-absentee.csv';

$PAGE->set_url('/mod/attendance/absentee.php', ['category' => $category, 'id' => $attendancecm]);

$PAGE->set_heading($SITE->fullname);

$table = new flexible_table('attendanceabsentee');
$table->define_baseurl($PAGE->url);

if (!$table->is_downloading($download, $exportfilename)) {
    if (!empty($attendancecm)) {
        $pageparams = new mod_attendance_sessions_page_params();
        $att = new mod_attendance_structure($att, $cm, $course, $context, $pageparams);
        $output = $PAGE->get_renderer('mod_attendance');
        echo $output->header();
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('absenteereport', 'mod_attendance'));
        if (empty($category)) {
            // Only show tabs if displaying via the admin page.
            $tabmenu = attendance_print_settings_tabs('absentee');
            echo $tabmenu;
        }
    }

}
$columns = ['coursename', 'aname', 'userid'];
$headers = [get_string('course'),
    get_string('pluginname', 'attendance'),
    get_string('user'), ];

$extrafields = [];
if (!empty($CFG->showuseridentity) && has_capability('moodle/site:viewuseridentity', $context)) {
    $extrafields = explode(',', $CFG->showuseridentity);
    foreach ($extrafields as $field) {
        if (strpos($field, 'profile_field') !== 0) {
            $columns[] = $field;
            $headers[] = get_string($field);
        }
    }
}
$columns = array_merge($columns, ['numtakensessions', 'percent', 'timesent']);
$headers = array_merge($headers, [
    get_string('takensessions', 'attendance'),
    get_string('averageattendance', 'attendance'),
    get_string('triggered', 'attendance')]);

$table->define_columns($columns);
$table->define_headers($headers);

$table->sortable(true);
$table->set_attribute('cellspacing', '0');
$table->set_attribute('class', 'generaltable generalbox');
$table->show_download_buttons_at([TABLE_P_BOTTOM]);
$table->setup();

// Work out direction of sort required.
$sortcolumns = $table->get_sort_columns();
// Now do sorting if specified.

// Sanity check $sort var before including in sql. Make sure it matches a known column.
$allowedsort = array_diff(array_keys($table->columns), $table->column_nosort);
if (!in_array($sort, $allowedsort)) {
    $sort = '';
}

$orderby = ' ORDER BY percent ASC';
if (!empty($sort)) {
    $direction = ' DESC';
    if (!empty($sortcolumns[$sort]) && $sortcolumns[$sort] == SORT_ASC) {
        $direction = ' ASC';
    }
    $orderby = " ORDER BY $sort $direction";

}

$records = attendance_get_users_to_notify($courses, $orderby);
foreach ($records as $record) {
    $row = [];
    if (!$table->is_downloading($download, $exportfilename)) {
        $url = new moodle_url('/mod/attendance/index.php', ['id' => $record->courseid]);
        $row[] = html_writer::link($url, $record->coursename);

        $url = new moodle_url('/mod/attendance/view.php', ['studentid' => $record->userid,
            'id' => $record->cmid, 'view' => ATT_VIEW_ALL]);
        $row[] = html_writer::link($url, $record->aname);

        $row[] = html_writer::link($url, fullname($record));
    } else {
        $row[] = $record->coursename;
        $row[] = $record->aname;
        $row[] = fullname($record);
    }
    foreach ($extrafields as $field) {
        if (isset($record->$field)) {
            $row[] = $record->$field;
        } else {
            $row[] = '';
        }
    }
    $row[] = $record->numtakensessions;
    $row[] = round($record->percent * 100)."%";
    $timesent = "-";
    if (!empty($record->timesent)) {
        $timesent = userdate($record->timesent);
    }
    $row[] = $timesent;

    $table->add_data($row);
}
$table->finish_output();

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}
