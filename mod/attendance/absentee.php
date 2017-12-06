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
require_once($CFG->libdir.'/coursecatlib.php');

$category = optional_param('category', 0, PARAM_INT);
$attendancecm = optional_param('id', 0, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);
$sort = optional_param('tsort', 'timesent', PARAM_ALPHA);

if (!empty($category)) {
    $context = context_coursecat::instance($category);
    $coursecat = coursecat::get($category);
    $courses = $coursecat->get_courses(array('recursive' => true, 'idonly' => true));
    $PAGE->set_category_by_id($category);
    require_login();
} else if (!empty($attendancecm)) {
    $cm             = get_coursemodule_from_id('attendance', $attendancecm, 0, false, MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $att            = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);
    $courses = array($course->id);
    $context = context_module::instance($cm->id);
    require_login($course, false, $cm);
} else {
    admin_externalpage_setup('managemodules');
    $context = context_system::instance();
    $courses = array(); // Show all courses.
}
// Check permissions.
require_capability('mod/attendance:viewreports', $context);

$exportfilename = 'attendance-absentee.csv';

$PAGE->set_url('/mod/attendance/absentee.php', array('category' => $category, 'id' => $attendancecm));

$PAGE->set_heading($SITE->fullname);

$table = new flexible_table('attendanceabsentee');
$table->define_baseurl($PAGE->url);

if (!$table->is_downloading($download, $exportfilename)) {
    if (!empty($attendancecm)) {
        $pageparams = new mod_attendance_sessions_page_params();
        $att = new mod_attendance_structure($att, $cm, $course, $context, $pageparams);
        $output = $PAGE->get_renderer('mod_attendance');
        $tabs = new attendance_tabs($att, attendance_tabs::TAB_ABSENTEE);
        echo $output->header();
        echo $output->heading(get_string('attendanceforthecourse', 'attendance').' :: ' .format_string($course->fullname));
        echo $output->render($tabs);
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

$table->define_columns(array('coursename', 'aname', 'userid', 'numtakensessions', 'percent', 'timesent'));
$table->define_headers(array(get_string('course'),
    get_string('pluginname', 'attendance'),
    get_string('user'),
    get_string('takensessions', 'attendance'),
    get_string('averageattendance', 'attendance'),
    get_string('triggered', 'attendance')));
$table->sortable(true);
$table->set_attribute('cellspacing', '0');
$table->set_attribute('class', 'generaltable generalbox');
$table->show_download_buttons_at(array(TABLE_P_BOTTOM));
$table->setup();

// Work out direction of sort required.
$sortcolumns = $table->get_sort_columns();
// Now do sorting if specified.

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
    if (!$table->is_downloading($download, $exportfilename)) {
        $url = new moodle_url('/mod/attendance/index.php', array('id' => $record->courseid));
        $name = html_writer::link($url, $record->coursename);
    } else {
        $name = $record->coursename;
    }
    $url = new moodle_url('/mod/attendance/view.php', array('studentid' => $record->userid,
                                                                'id' => $record->cmid, 'view' => ATT_VIEW_ALL));
    $attendancename = html_writer::link($url, $record->aname);

    $username = html_writer::link($url, fullname($record));
    $percent = round($record->percent * 100)."%";
    $timesent = "-";
    if (!empty($record->timesent)) {
        $timesent = userdate($record->timesent);
    }

    $table->add_data(array($name, $attendancename, $username, $record->numtakensessions, $percent, $timesent));
}
$table->finish_output();

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}