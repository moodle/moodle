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
 * Display user activity reports for a course
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once("../config.php");
require_once("lib.php");

$id      = required_param('id',PARAM_INT);       // course id
$user    = required_param('user',PARAM_INT);     // user id
$mode    = optional_param('mode', "todaylogs", PARAM_ALPHA);

$url = new moodle_url('/course/user.php', array('id'=>$id,'user'=>$user, 'mode'=>$mode));

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
$user = $DB->get_record("user", array("id"=>$user, 'deleted'=>0), '*', MUST_EXIST);

if ($mode === 'outline' or $mode === 'complete') {
    $url = new moodle_url('/report/outline/user.php', array('id'=>$user->id, 'course'=>$course->id, 'mode'=>$mode));
    redirect($url);
}
if ($mode === 'todaylogs' or $mode === 'alllogs') {
    $logmode = ($mode === 'todaylogs') ? 'today' : 'all';
    $url = new moodle_url('/report/log/user.php', array('id'=>$user->id, 'course'=>$course->id, 'mode'=>$logmode));
    redirect($url);
}
if ($mode === 'stats') {
    $url = new moodle_url('/report/stats/user.php', array('id'=>$user->id, 'course'=>$course->id));
    redirect($url);
}
if ($mode === 'coursecompletions' or $mode === 'coursecompletion') {
    $url = new moodle_url('/report/completion/user.php', array('id'=>$user->id, 'course'=>$course->id));
    redirect($url);
}

$coursecontext   = context_course::instance($course->id);
$personalcontext = context_user::instance($user->id);

$PAGE->set_context($personalcontext);

$PAGE->set_url('/course/user.php', array('id'=>$id, 'user'=>$user->id, 'mode'=>$mode));

require_login();
$PAGE->set_pagelayout('report');
if (has_capability('moodle/user:viewuseractivitiesreport', $personalcontext) and !is_enrolled($coursecontext)) {
    // do not require parents to be enrolled in courses ;-)
    $PAGE->set_course($course);
} else {
    require_login($course);
}

if ($user->deleted) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('userdeleted'));
    echo $OUTPUT->footer();
    die;
}

// prepare list of allowed modes
$myreports  = ($course->showreports and $USER->id == $user->id);
$anyreport  = has_capability('moodle/user:viewuseractivitiesreport', $personalcontext);

$modes = array();

// Used for grade reports, it represents whether we should be viewing the report as ourselves, or as the targetted user.
$viewasuser = false;

if (has_capability('moodle/grade:viewall', $coursecontext)) {
    //ok - can view all course grades
    $modes[] = 'grade';

} else if ($course->showgrades and $user->id == $USER->id and has_capability('moodle/grade:view', $coursecontext)) {
    //ok - can view own grades
    $modes[] = 'grade';

} else if ($course->showgrades and has_capability('moodle/grade:viewall', $personalcontext)) {
    // ok - can view grades of this user - parent most probably
    $modes[] = 'grade';
    $viewasuser = true;

} else if ($course->showgrades and $anyreport) {
    // ok - can view grades of this user - parent most probably
    $modes[] = 'grade';
    $viewasuser = true;
}

if (empty($modes)) {
    require_capability('moodle/user:viewuseractivitiesreport', $personalcontext);
}

if (!in_array($mode, $modes)) {
    // forbidden or non-existent mode
    $mode = reset($modes);
}

$eventdata = array(
    'context' => $coursecontext,
    'relateduserid' => $user->id,
    'other' => array('mode' => $mode),
);
$event = \core\event\course_user_report_viewed::create($eventdata);
$event->trigger();

$stractivityreport = get_string("activityreport");

$PAGE->navigation->extend_for_user($user);
$PAGE->navigation->set_userid_for_parent_checks($user->id); // see MDL-25805 for reasons and for full commit reference for reversal when fixed.
$PAGE->set_title("$course->shortname: $stractivityreport ($mode)");
$PAGE->set_heading(fullname($user));

switch ($mode) {
    case "grade":
        // Change the navigation to point to the my grade node (If we are a student).
        if ($USER->id == $user->id) {
            require_once($CFG->dirroot . '/user/lib.php');
            // Make the dashboard active so that it shows up in the navbar correctly.
            $gradenode = $PAGE->settingsnav->find('dashboard', null)->make_active();
            // Get the correct 'My grades' url to point to.
            $activeurl = user_mygrades_url();
            $navbar = $PAGE->navbar->add(get_string('mygrades', 'grades'), $activeurl, navigation_node::TYPE_SETTING);
            $activenode = $navbar->add($course->shortname);
            $activenode->make_active();
            // Find the course node and collapse it.
            $coursenode = $PAGE->navigation->find($course->id, navigation_node::TYPE_COURSE);
            $coursenode->collapse = true;
            $coursenode->make_inactive();
            $url = new moodle_url('/course/user.php', array('id' => $id, 'user' => $user->id, 'mode' => $mode));
            $reportnode = $activenode->add(get_string('pluginname', 'gradereport_user'), $url);
        } else {
            // Check to see if the active node is a user name.
            $currentcoursenode = $PAGE->navigation->find('currentcourse', null);
            $activenode = $currentcoursenode->find_active_node();
            if (strpos($activenode->key, 'user') === false) { // No user name found.
                $userurl = new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $course->id));
                // Add the user name.
                $PAGE->navbar->add(fullname($user), $userurl, navigation_node::TYPE_SETTING);
            }
            $gradeurl = new moodle_url('/course/user.php', array('id' => $id, 'user' => $user->id, 'mode' => $mode));
            // Add the 'grades' node to the navbar.
            $navbar = $PAGE->navbar->add(get_string('grades', 'grades'), $gradeurl, navigation_node::TYPE_SETTING);
        }
        echo $OUTPUT->header();

        if (empty($CFG->grade_profilereport) or !file_exists($CFG->dirroot.'/grade/report/'.$CFG->grade_profilereport.'/lib.php')) {
            $CFG->grade_profilereport = 'user';
        }
        require_once $CFG->libdir.'/gradelib.php';
        require_once $CFG->dirroot.'/grade/lib.php';
        require_once $CFG->dirroot.'/grade/report/'.$CFG->grade_profilereport.'/lib.php';

        $functionname = 'grade_report_'.$CFG->grade_profilereport.'_profilereport';
        if (function_exists($functionname)) {
            $functionname($course, $user, $viewasuser);
        }
        break;

        break;
    default:
        // It's unlikely to reach this piece of code, as the mode is never empty and it sets mode as grade in most of the cases.
        // Display the page header to avoid breaking the navigation. A course/user.php review will be done in MDL-49939.
        echo $OUTPUT->header();
}


echo $OUTPUT->footer();
