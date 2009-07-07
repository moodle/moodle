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

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/user/lib.php';

$courseid = required_param('id', PARAM_INT);
$userid   = optional_param('userid', $USER->id, PARAM_INT);

/// basic access checks
if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}
require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('gradereport/user:view', $context);

if (empty($userid)) {
    require_capability('moodle/grade:viewall', $context);

} else {
    if (!get_record('user', 'id', $userid, 'deleted', 0) or isguestuser($userid)) {
        error("Incorrect userid");
    }
}

$access = false;
if (has_capability('moodle/grade:viewall', $context)) {
    //ok - can view all course grades
    $access = true;

} else if ($userid == $USER->id and has_capability('moodle/grade:view', $context) and $course->showgrades) {
    //ok - can view own grades
    $access = true;

} else if (has_capability('moodle/grade:viewall', get_context_instance(CONTEXT_USER, $userid)) and $course->showgrades) {
    // ok - can view grades of this user- parent most probably
    $access = true;
}

if (!$access) {
    // no access to grades!
    error("Can not view grades.", $CFG->wwwroot.'/course/view.php?id='.$courseid); //TODO: localize
}

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'user', 'courseid'=>$courseid, 'userid'=>$userid));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'user';


//first make sure we have proper final grades - this must be done before constructing of the grade tree
grade_regrade_final_grades($courseid);

if (has_capability('moodle/grade:viewall', $context)) { //Teachers will see all student reports
    $groupmode    = groups_get_course_groupmode($course);   // Groups are being used
    $currentgroup = groups_get_course_group($course, true);

    if (!$currentgroup) {      // To make some other functions work better later
        $currentgroup = NULL;
    }

    $isseparategroups = ($course->groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context));

    if ($isseparategroups and (!$currentgroup)) {
        // no separate group access, can view only self
        $userid = $USER->id;
        $user_selector = '';
    } else {
        /// Print graded user selector at the top
        $user_selector = '<div id="graded_users_selector">';
        $user_selector .= print_graded_users_selector($course, 'report/user/index.php?id=' . $course->id, $userid, $currentgroup, true, true);
        $user_selector .= '</div>';
        $user_selector .= "<p style = 'page-break-after: always;'></p>";
    }

    if (empty($userid)) {
        $gui = new graded_users_iterator($course, null, $currentgroup);
        $gui->init();
        // Add tabs
        print_grade_page_head($courseid, 'report', 'user');
        groups_print_course_menu($course, $gpr->get_return_url('index.php?id='.$courseid, array('userid'=>0)));

        echo $user_selector.'<br />';
        while ($userdata = $gui->next_user()) {
            $user = $userdata->user;
            $report = new grade_report_user($courseid, $gpr, $context, $user->id);
            print_heading(get_string('modulename', 'gradereport_user'). ' - '.fullname($report->user));

            if ($report->fill_table()) {
                echo '<br />'.$report->print_table(true);
            }
            echo "<p style = 'page-break-after: always;'></p>";
        }
        $gui->close();
    } else { // Only show one user's report
        $report = new grade_report_user($courseid, $gpr, $context, $userid);
        print_grade_page_head($courseid, 'report', 'user', get_string('modulename', 'gradereport_user'). ' - '.fullname($report->user));
        groups_print_course_menu($course, $gpr->get_return_url('index.php?id='.$courseid, array('userid'=>0)));

        echo $user_selector;

        if ($currentgroup and !groups_is_member($currentgroup, $userid)) {
            notify(get_string('groupusernotmember', 'error'));
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
    print_grade_page_head($courseid, 'report', 'user', get_string('modulename', 'gradereport_user'). ' - '.fullname($report->user));

    if ($report->fill_table()) {
        echo '<br />'.$report->print_table(true);
    }
}

print_footer($course);

?>
