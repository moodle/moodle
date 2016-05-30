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
 * Display user activity reports for a course (totals)
 *
 * @package    report_log
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/report/log/locallib.php');
require_once($CFG->dirroot.'/lib/tablelib.php');

$userid   = required_param('id', PARAM_INT);
$courseid = required_param('course', PARAM_INT);
$mode     = optional_param('mode', 'today', PARAM_ALPHA);
$page     = optional_param('page', 0, PARAM_INT);
$perpage  = optional_param('perpage', 100, PARAM_INT);
$logreader   = optional_param('logreader', '', PARAM_COMPONENT); // Log reader which will be used for displaying logs.

if ($mode !== 'today' and $mode !== 'all') {
    $mode = 'today';
}

$user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$coursecontext   = context_course::instance($course->id);
$personalcontext = context_user::instance($user->id);

if ($courseid == SITEID) {
    $PAGE->set_context($personalcontext);
}

if ($USER->id != $user->id and has_capability('moodle/user:viewuseractivitiesreport', $personalcontext)
        and !is_enrolled($coursecontext, $USER) and is_enrolled($coursecontext, $user)) {
    //TODO: do not require parents to be enrolled in courses - this is a hack!
    require_login();
    $PAGE->set_course($course);
} else {
    require_login($course);
}

list($all, $today) = report_log_can_access_user_report($user, $course);

if ($mode === 'today') {
    if (!$today) {
        require_capability('report/log:viewtoday', $coursecontext);
    }
} else {
    if (!$all) {
        require_capability('report/log:view', $coursecontext);
    }
}

$stractivityreport = get_string('activityreport');

$PAGE->set_pagelayout('report');
$PAGE->set_url('/report/log/user.php', array('id' => $user->id, 'course' => $course->id, 'mode' => $mode));
$PAGE->navigation->extend_for_user($user);
$PAGE->navigation->set_userid_for_parent_checks($user->id); // see MDL-25805 for reasons and for full commit reference for reversal when fixed.
$PAGE->set_title("$course->shortname: $stractivityreport");

// Create the appropriate breadcrumb.
$navigationnode = array(
        'url' => new moodle_url('/report/log/user.php', array('id' => $user->id, 'course' => $course->id, 'mode' => $mode))
    );
if ($mode === 'today') {
    $navigationnode['name'] = get_string('todaylogs');
} else {
    $navigationnode['name'] = get_string('alllogs');
}
$PAGE->add_report_nodes($user->id, $navigationnode);

if ($courseid == SITEID) {
    $PAGE->set_heading(fullname($user));
} else {
    $PAGE->set_heading($course->fullname);
}

// Trigger a user logs viewed event.
$event = \report_log\event\user_report_viewed::create(array('context' => $coursecontext, 'relateduserid' => $userid,
        'other' => array('mode' => $mode)));
$event->trigger();

echo $OUTPUT->header();
if ($courseid != SITEID) {
    $userheading = array(
            'user' => $user,
            'usercontext' => $personalcontext,
        );
    echo $OUTPUT->context_header($userheading, 2);
}

// Time to filter records from.
if ($mode === 'today') {
    $timefrom = usergetmidnight(time());
} else {
    $timefrom = 0;
}

$output = $PAGE->get_renderer('report_log');
$reportlog = new report_log_renderable($logreader, $course, $user->id, 0, '', -1, -1, false, false, true, false, $PAGE->url,
        $timefrom, '', $page, $perpage, 'timecreated DESC');

// Setup table if log reader is enabled.
if (!empty($reportlog->selectedlogreader)) {
    $reportlog->setup_table();
    $reportlog->tablelog->is_downloadable(false);
}

echo $output->reader_selector($reportlog);

if ($mode === 'today') {
    echo '<div class="graph">';
    report_log_print_graph($course, $user->id, "userday.png", 0, $logreader);
    echo '</div>';
    echo $output->render($reportlog);
} else {
    echo '<div class="graph">';
    report_log_print_graph($course, $user->id, "usercourse.png", 0, $logreader);
    echo '</div>';
    $reportlog->selecteddate = 0;
    echo $output->render($reportlog);
}

echo $OUTPUT->footer();