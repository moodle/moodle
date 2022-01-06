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
 * Prints attendance info for particular user
 *
 * @package    mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');

$pageparams = new mod_attendance_view_page_params();

$id                     = required_param('id', PARAM_INT);
$edit                   = optional_param('edit', -1, PARAM_BOOL);
$pageparams->studentid  = optional_param('studentid', null, PARAM_INT);
$pageparams->mode       = optional_param('mode', mod_attendance_view_page_params::MODE_THIS_COURSE, PARAM_INT);
$pageparams->view       = optional_param('view', null, PARAM_INT);
$pageparams->curdate    = optional_param('curdate', null, PARAM_INT);
$pageparams->groupby    = optional_param('groupby', 'course', PARAM_ALPHA);
$pageparams->sesscourses = optional_param('sesscourses', 'current', PARAM_ALPHA);

$cm             = get_coursemodule_from_id('attendance', $id, 0, false, MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$attendance    = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/attendance:view', $context);

$pageparams->init($cm);
$att = new mod_attendance_structure($attendance, $cm, $course, $context, $pageparams);

// Not specified studentid for displaying attendance?
// Redirect to appropriate page if can.
if (!$pageparams->studentid) {
    $capabilities = array(
        'mod/attendance:manageattendances',
        'mod/attendance:takeattendances',
        'mod/attendance:changeattendances'
    );
    if (has_any_capability($capabilities, $context)) {
        redirect($att->url_manage());
    } else if (has_capability('mod/attendance:viewreports', $context)) {
        redirect($att->url_report());
    }
}

if (isset($pageparams->studentid) && $USER->id != $pageparams->studentid) {
    // Only users with proper permissions should be able to see any user's individual report.
    require_capability('mod/attendance:viewreports', $context);
    $userid = $pageparams->studentid;
} else {
    // A valid request to see another users report has not been sent, show the user's own.
    $userid = $USER->id;
}

$url = $att->url_view($pageparams->get_significant_params());
$PAGE->set_url($url);

$buttons = '';
$capabilities = array('mod/attendance:takeattendances', 'mod/attendance:changeattendances');
if (has_any_capability($capabilities, $context) &&
    $pageparams->mode == mod_attendance_view_page_params::MODE_ALL_SESSIONS) {

    if (!isset($USER->attendanceediting)) {
        $USER->attendanceediting = false;
    }

    if (($edit == 1) and confirm_sesskey()) {
        $USER->attendanceediting = true;
    } else if ($edit == 0 and confirm_sesskey()) {
        $USER->attendanceediting = false;
    }

    if ($USER->attendanceediting) {
        $options['edit'] = 0;
        $string = get_string('turneditingoff');
    } else {
        $options['edit'] = 1;
        $string = get_string('turneditingon');
    }
    $options['sesskey'] = sesskey();
    $button = new single_button(new moodle_url($PAGE->url, $options), $string, 'post');
    $PAGE->set_button($OUTPUT->render($button));
}

$userdata = new attendance_user_data($att, $userid);

// Create url for link in log screen.
$filterparams = array(
    'view' => $userdata->pageparams->view,
    'curdate' => $userdata->pageparams->curdate,
    'startdate' => $userdata->pageparams->startdate,
    'enddate' => $userdata->pageparams->enddate
);
$params = array_merge($userdata->pageparams->get_significant_params(), $filterparams);

$header = new mod_attendance_header($att);

if (empty($userdata->pageparams->studentid)) {
    $relateduserid = $USER->id;
} else {
    $relateduserid = $userdata->pageparams->studentid;
}

if (($formdata = data_submitted()) && confirm_sesskey() && $edit == -1) {
    $userdata->take_sessions_from_form_data($formdata);

    // Trigger updated event.
    $event = \mod_attendance\event\session_report_updated::create(array(
        'relateduserid' => $relateduserid,
        'context' => $context,
        'other' => $params));
    $event->add_record_snapshot('course_modules', $cm);
    $event->trigger();

    redirect($url, get_string('attendancesuccess', 'attendance'));
} else {
    // Trigger viewed event.
    $event = \mod_attendance\event\session_report_viewed::create(array(
        'relateduserid' => $relateduserid,
        'context' => $context,
        'other' => $params));
    $event->add_record_snapshot('course_modules', $cm);
    $event->trigger();
}

$PAGE->set_title($course->shortname. ": ".$att->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(true);
$PAGE->navbar->add(get_string('attendancereport', 'attendance'));

$output = $PAGE->get_renderer('mod_attendance');

echo $output->header();

echo $output->render($header);
echo $output->render($userdata);

echo $output->footer();
