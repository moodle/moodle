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
 * The gradebook overview report
 *
 * @package   gradereport_overview
 * @copyright 2007 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/overview/lib.php';

$courseid = optional_param('id', SITEID, PARAM_INT);
$userid   = optional_param('userid', $USER->id, PARAM_INT);

$PAGE->set_url(new moodle_url('/grade/report/overview/index.php', array('id' => $courseid, 'userid' => $userid)));

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    throw new \moodle_exception('invalidcourseid');
}
require_login(null, false);
$PAGE->set_course($course);

$context = context_course::instance($course->id);
$systemcontext = context_system::instance();
$personalcontext = null;

// If we are accessing the page from a site context then ignore this check.
if ($courseid != SITEID) {
    require_capability('gradereport/overview:view', $context);
}

if (empty($userid)) {
    require_capability('moodle/grade:viewall', $context);

} else {
    if (!$DB->get_record('user', array('id'=>$userid, 'deleted'=>0)) or isguestuser($userid)) {
        throw new \moodle_exception('invaliduserid');
    }
    $personalcontext = context_user::instance($userid);
}

if (isset($personalcontext) && $courseid == SITEID) {
    $PAGE->set_context($personalcontext);
} else {
    $PAGE->set_context($context);
}
if ($userid == $USER->id) {
    $settings = $PAGE->settingsnav->find('mygrades', null);
    $settings->make_active();
} else if ($courseid != SITEID && $userid) {
    // Show some other navbar thing.
    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
    $PAGE->navigation->extend_for_user($user);
}

$access = grade_report_overview::check_access($systemcontext, $context, $personalcontext, $course, $userid);

if (!$access) {
    // no access to grades!
    throw new \moodle_exception('nopermissiontoviewgrades', 'error',  $CFG->wwwroot.'/course/view.php?id='.$courseid);
}

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'overview', 'courseid'=>$course->id, 'userid'=>$userid));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'overview';

// First make sure we have proper final grades.
grade_regrade_final_grades_if_required($course);

$actionbar = new \core_grades\output\general_action_bar($context,
    new moodle_url('/grade/report/overview/index.php', ['id' => $courseid]), 'report', 'overview');

if (has_capability('moodle/grade:viewall', $context) && $courseid != SITEID) {
    // Please note this would be extremely slow if we wanted to implement this properly for all teachers.
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

    if (empty($userid)) {
        print_grade_page_head($courseid, 'report', 'overview', false, false, false,
            true, null, null, null, $actionbar);

        groups_print_course_menu($course, $gpr->get_return_url('index.php?id='.$courseid, array('userid'=>0)));

        if ($user_selector) {
            $renderer = $PAGE->get_renderer('gradereport_overview');
            echo $renderer->graded_users_selector('overview', $course, $userid, $currentgroup, false);
        }
        // do not list all users

    } else { // Only show one user's report
        $report = new grade_report_overview($userid, $gpr, $context);
        print_grade_page_head($courseid, 'report', 'overview', get_string('pluginname', 'gradereport_overview') .
            ' - ' . fullname($report->user), false, false, true, null, null,
            $report->user, $actionbar);
        groups_print_course_menu($course, $gpr->get_return_url('index.php?id='.$courseid, array('userid'=>0)));

        if ($user_selector) {
            $renderer = $PAGE->get_renderer('gradereport_overview');
            echo $renderer->graded_users_selector('overview', $course, $userid, $currentgroup, false);
        }

        if ($currentgroup and !groups_is_member($currentgroup, $userid)) {
            echo $OUTPUT->notification(get_string('groupusernotmember', 'error'));
        } else {
            if ($report->fill_table()) {
                echo '<br />'.$report->print_table(true);
            }
        }
    }
} else { // Non-admins and users viewing from the site context can just see their own report.

    // Create a report instance
    $report = new grade_report_overview($userid, $gpr, $context);

    if (!empty($report->studentcourseids)) {
        // If the course id matches the site id then we don't have a course context to work with.
        // Display a standard page.
        if ($courseid == SITEID) {
            $PAGE->set_pagelayout('standard');
            $header = get_string('grades', 'grades') . ' - ' . fullname($report->user);
            $PAGE->set_title($header);
            $PAGE->set_heading(fullname($report->user));

            if ($USER->id != $report->user->id) {
                $PAGE->navigation->extend_for_user($report->user);
                if ($node = $PAGE->settingsnav->get('userviewingsettings'.$report->user->id)) {
                    $node->forceopen = true;
                }
            } else if ($node = $PAGE->settingsnav->get('usercurrentsettings', navigation_node::TYPE_CONTAINER)) {
                $node->forceopen = true;
            }

            echo $OUTPUT->header();
            if ($report->fill_table(true, true)) {
                echo html_writer::tag('h3', get_string('coursesiamtaking', 'grades'));
                echo '<br />' . $report->print_table(true);
            }
        } else { // We have a course context. We must be navigating from the gradebook.
            print_grade_page_head($courseid, 'report', 'overview', get_string('pluginname', 'gradereport_overview')
                . ' - ' . fullname($report->user), false, false, true, null, null,
                $report->user, $actionbar);
            if ($report->fill_table()) {
                echo '<br />' . $report->print_table(true);
            }
        }
    } else {
        $PAGE->set_pagelayout('standard');
        $header = get_string('grades', 'grades') . ' - ' . fullname($report->user);
        $PAGE->set_title($header);
        $PAGE->set_heading(fullname($report->user));
        echo $OUTPUT->header();
    }

    if (count($report->teachercourses)) {
        echo html_writer::tag('h3', get_string('coursesiamteaching', 'grades'));
        $report->print_teacher_table();
    }

    if (empty($report->studentcourseids) && empty($report->teachercourses)) {
        // We have no report to show the user. Let them know something.
        echo $OUTPUT->notification(get_string('noreports', 'grades'), 'notifymessage');
    }
}

grade_report_overview::viewed($context, $courseid, $userid);

echo $OUTPUT->footer();
