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

$userid   = required_param('id', PARAM_INT);
$courseid = required_param('course', PARAM_INT);
$mode     = optional_param('mode', 'today', PARAM_ALPHA);
$page     = optional_param('page', 0, PARAM_INT);
$perpage  = optional_param('perpage', 100, PARAM_INT);

if ($mode !== 'today' and $mode !== 'all') {
    $mode = 'today';
}

$user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);

$coursecontext   = context_course::instance($course->id);
$personalcontext = context_user::instance($user->id);

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

add_to_log($course->id, 'course', 'report log', "report/log/user.php?id=$user->id&course=$course->id&mode=$mode", $course->id);

$stractivityreport = get_string('activityreport');

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/report/log/user.php', array('id'=>$user->id, 'course'=>$course->id, 'mode'=>$mode));
$PAGE->navigation->extend_for_user($user);
$PAGE->navigation->set_userid_for_parent_checks($user->id); // see MDL-25805 for reasons and for full commit reference for reversal when fixed.
$PAGE->set_title("$course->shortname: $stractivityreport");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

if ($mode === 'today') {
    echo '<div class="graph">';
    report_log_print_graph($course, $user->id, "userday.png");
    echo '</div>';
    print_log($course, $user->id, usergetmidnight(time()), "l.time DESC", $page, $perpage,
              "user.php?course=$course->id&amp;id=$user->id&amp;mode=$mode");
} else {
    echo '<div class="graph">';
    report_log_print_graph($course, $user->id, "usercourse.png");
    echo '</div>';
    print_log($course, $user->id, 0, "l.time DESC", $page, $perpage,
              "user.php?course=$course->id&amp;id=$user->id&amp;mode=$mode");
}

echo $OUTPUT->footer();