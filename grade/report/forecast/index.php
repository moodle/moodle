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
 * The gradebook forecast report page
 *
 * @package    gradereport_forecast
 * @copyright  2016 Louisiana State University, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/forecast/lib.php';

$PAGE->requires->jquery();
$PAGE->requires->js('/grade/report/forecast/js/forecast.js');
$PAGE->requires->js('/grade/report/forecast/js/modal.js');

$courseid = required_param('id', PARAM_INT);
$userid   = optional_param('userid', $USER->id, PARAM_INT);

$PAGE->set_url(new moodle_url('/grade/report/forecast/index.php', array('id'=>$courseid)));

/// basic access checks
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}
require_login($course);
$PAGE->set_pagelayout('report');

$context = context_course::instance($course->id);
require_capability('gradereport/forecast:view', $context);

if (empty($userid)) {
    require_capability('moodle/grade:viewall', $context);

} else {
    if (!$DB->get_record('user', array('id'=>$userid, 'deleted'=>0)) or isguestuser($userid)) {
        print_error('invaliduser');
    }
}

$enabled = grade_get_setting($courseid, 'report_forecast_enabledforstudents', $CFG->grade_report_forecast_enabledforstudents);
$view_all = has_capability('moodle/grade:viewall', $context);
$view_own = $userid == $USER->id and has_capability('moodle/grade:view', $context) and $course->showgrades;
$view_user = has_capability('moodle/grade:viewall', context_user::instance($userid)) and $course->showgrades;
$url = $CFG->wwwroot . '/course/view.php?id=' . $courseid;

if (!($view_all or ($view_own && $enabled) or $view_user)) {
    // Be sure users are not automatically redirected back here if disabled after use
    $USER->grade_last_report[$course->id] = 'user';
    // redirect them back to the course, letting them know it was disabled
    redirect($url, get_string('nopermissiontouseforecast', 'gradereport_forecast'), 1);
    // if somehow they don't redirect, print an error and die
    print_error('nopermissiontouseforecast', 'gradereport_forecast',  $url);
    echo $OUTPUT->footer();
    die();
}

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'forecast', 'courseid'=>$courseid, 'userid'=>$userid));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}

$USER->grade_last_report[$course->id] = 'forecast';

// First make sure we have proper final grades.
grade_regrade_final_grades_if_required($course);

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
        $user_selector = false;
    } else {
        $user_selector = true;
    }

    $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
    $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
    $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $context);

    // If a user is selected, show the report
    if ( ! empty($userid)) {
        $report = new grade_report_forecast($courseid, $gpr, $context, $userid);

        // echo '<pre>';
        // var_dump($report->gtree);
        // echo '</pre>';

        $studentnamelink = html_writer::link(new moodle_url('/user/view.php', array('id' => $report->user->id, 'course' => $courseid)), fullname($report->user));
        print_grade_page_head($courseid, 'report', 'forecast', get_string('pluginname', 'gradereport_forecast') . ' - ' . $studentnamelink,
                false, false, true, null, null, $report->user);

        groups_print_course_menu($course, $gpr->get_return_url('index.php?id='.$courseid, array('userid'=>0)));

        if ($user_selector) {
            $renderer = $PAGE->get_renderer('gradereport_forecast');
            $showallusersoptions = false;
            echo $renderer->graded_users_selector('forecast', $course, $userid, $currentgroup, $showallusersoptions);
        }

        if ($currentgroup and !groups_is_member($currentgroup, $userid)) {
            echo $OUTPUT->notification(get_string('groupusernotmember', 'error'));
        } else {
            if ($report->fill_table()) {
                echo '<br />'.$report->print_table(true);
            }
        }
    } else {
        // @TODO: show a "blank" grade tree that is not populated with any real grades
        
        // Add tabs
        print_grade_page_head($courseid, 'report', 'forecast');
        groups_print_course_menu($course, $gpr->get_return_url('index.php?id='.$courseid, array('userid'=>0)));

        if ($user_selector) {
            $renderer = $PAGE->get_renderer('gradereport_forecast');
            echo $renderer->graded_users_selector('forecast', $course, $userid, $currentgroup, false);
        }
    }
} else { //Students will see just their own report

    // Create a report instance
    $report = new grade_report_forecast($courseid, $gpr, $context, $userid);

    // print the page
    print_grade_page_head($courseid, 'report', 'forecast', get_string('pluginname', 'gradereport_forecast'). ' - '.fullname($report->user));

    if ($report->fill_table()) {
        echo '<br />'.$report->print_table(true);
    }
}

if (isset($report)) {
    // Trigger report viewed event.
    $report->viewed();
} else {
    echo html_writer::tag('div', '', array('class' => 'clearfix'));
    echo $OUTPUT->notification(get_string('selctauser'));
}

if (grade_get_setting($courseid, 'report_forecast_mustmakeenabled', $CFG->grade_report_forecast_mustmakeenabled)) {
    echo $report->getMustMakeModal();
}

echo '<script>$(document).ready(function() { listenForInputChanges(' . grade_get_setting($courseid, 'report_forecast_debouncewaittime', $CFG->grade_report_forecast_debouncewaittime) . ') });</script>';

echo $OUTPUT->footer();
