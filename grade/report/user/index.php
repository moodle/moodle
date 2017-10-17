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
 * The gradebook user report
 *
 * @package   gradereport_user
 * @copyright 2007 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/user/lib.php';

$courseid = required_param('id', PARAM_INT);
$userid   = optional_param('userid', $USER->id, PARAM_INT);
$userview = optional_param('userview', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/grade/report/user/index.php', array('id'=>$courseid)));

if ($userview == 0) {
    $userview = get_user_preferences('gradereport_user_view_user', GRADE_REPORT_USER_VIEW_USER);
} else {
    set_user_preference('gradereport_user_view_user', $userview);
}

/// basic access checks
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}
require_login($course);
$PAGE->set_pagelayout('report');

$context = context_course::instance($course->id);
require_capability('gradereport/user:view', $context);

if (empty($userid)) {
    require_capability('moodle/grade:viewall', $context);

} else {
    if (!$DB->get_record('user', array('id'=>$userid, 'deleted'=>0)) or isguestuser($userid)) {
        print_error('invaliduser');
    }
}

$access = false;
if (has_capability('moodle/grade:viewall', $context)) {
    //ok - can view all course grades
    $access = true;

} else if ($userid == $USER->id and has_capability('moodle/grade:view', $context) and $course->showgrades) {
    //ok - can view own grades
    $access = true;

} else if (has_capability('moodle/grade:viewall', context_user::instance($userid)) and $course->showgrades) {
    // ok - can view grades of this user- parent most probably
    $access = true;
}

if (!$access) {
    // no access to grades!
    print_error('nopermissiontoviewgrades', 'error',  $CFG->wwwroot.'/course/view.php?id='.$courseid);
}

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'user', 'courseid'=>$courseid, 'userid'=>$userid));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'user';

// First make sure we have proper final grades.
grade_regrade_final_grades_if_required($course);

if (has_capability('moodle/grade:viewall', $context)) { //Teachers will see all student reports
    $groupmode    = groups_get_course_groupmode($course);   // Groups are being used
    $currentgroup = $gpr->groupid;

    if (!$currentgroup) {      // To make some other functions work better later
        $currentgroup = NULL;
    }

    $isseparategroups = ($course->groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context));

    if ($isseparategroups and (!$currentgroup)) {
        // no separate group access, can view only self
        $userid = $USER->id;
        $user_selector = false;
    } else {
        $user_selector = true;
    }

    $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
    $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
    $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $context);

    $renderer = $PAGE->get_renderer('gradereport_user');

    if ($userview == GRADE_REPORT_USER_VIEW_USER) {
        $viewasuser = true;
    } else {
        $viewasuser = false;
    }

    if (empty($userid)) {
        $gui = new graded_users_iterator($course, null, $currentgroup);
        $gui->require_active_enrolment($showonlyactiveenrol);
        $gui->init();
        // Add tabs
        print_grade_page_head($courseid, 'report', 'user');
        groups_print_course_menu($course, $gpr->get_return_url('index.php?id='.$courseid, array('userid'=>0)));

        if ($user_selector) {
            echo $renderer->graded_users_selector('user', $course, $userid, $currentgroup, true);
        }

        echo $renderer->view_user_selector($userid, $userview);

        while ($userdata = $gui->next_user()) {
            $user = $userdata->user;
            $report = new grade_report_user($courseid, $gpr, $context, $user->id, $viewasuser);

            $studentnamelink = html_writer::link(new moodle_url('/user/view.php', array('id' => $report->user->id, 'course' => $courseid)), fullname($report->user));
            echo $OUTPUT->heading(get_string('pluginname', 'gradereport_user') . ' - ' . $studentnamelink);

            if ($report->fill_table()) {
                echo '<br />'.$report->print_table(true);
            }
            echo "<p style = 'page-break-after: always;'></p>";
        }
        $gui->close();
    } else { // Only show one user's report
        $report = new grade_report_user($courseid, $gpr, $context, $userid, $viewasuser);

        $studentnamelink = html_writer::link(new moodle_url('/user/view.php', array('id' => $report->user->id, 'course' => $courseid)), fullname($report->user));
        print_grade_page_head($courseid, 'report', 'user', get_string('pluginname', 'gradereport_user') . ' - ' . $studentnamelink,
                false, false, true, null, null, $report->user);

        groups_print_course_menu($course, $gpr->get_return_url('index.php?id='.$courseid, array('userid'=>0)));

        if ($user_selector) {
            $showallusersoptions = true;
            echo $renderer->graded_users_selector('user', $course, $userid, $currentgroup, $showallusersoptions);
        }

        echo $renderer->view_user_selector($userid, $userview);

        if ($currentgroup and !groups_is_member($currentgroup, $userid)) {
            echo $OUTPUT->notification(get_string('groupusernotmember', 'error'));
        } else {
            if ($report->fill_table()) {
                echo '<br />'.$report->print_table(true);
            }
        }
    }
} else { //Students will see just their own report

    // Create a report instance
    $report = new grade_report_user($courseid, $gpr, $context, $userid);

    // print the page
    print_grade_page_head($courseid, 'report', 'user', get_string('pluginname', 'gradereport_user'). ' - '.fullname($report->user));

    if ($report->fill_table()) {
        echo '<br />'.$report->print_table(true);
    }
}

if (isset($report)) {
    // Trigger report viewed event.
    $report->viewed();
} else {
    echo html_writer::tag('div', '', array('class' => 'clearfix'));
    echo $OUTPUT->notification(get_string('nostudentsyet'));
}

echo $OUTPUT->footer();
