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
 * This page lets users to manage site wide competencies.
 *
 * @package    report_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$id = required_param('id', PARAM_INT);

$params = array('id' => $id);
$course = $DB->get_record('course', $params, '*', MUST_EXIST);
require_login($course);
$context = context_course::instance($course->id);
$currentuser = optional_param('user', null, PARAM_INT);
$currentmodule = optional_param('mod', null, PARAM_INT);
if ($currentmodule > 0) {
    $cm = get_coursemodule_from_id('', $currentmodule, 0, false, MUST_EXIST);
    $context = context_module::instance($cm->id);
}

// Fetch current active group.
$groupmode = groups_get_course_groupmode($course);
$currentgroup = groups_get_course_group($course, true);
if (empty($currentuser)) {
    $gradable = get_enrolled_users($context, 'moodle/competency:coursecompetencygradable', $currentgroup, 'u.id', null, 0, 1);
    if (empty($gradable)) {
        $currentuser = 0;
    } else {
        $currentuser = array_pop($gradable)->id;
    }
} else {
    $gradable = get_enrolled_users($context, 'moodle/competency:coursecompetencygradable', $currentgroup, 'u.id');
    if (count($gradable) == 0) {
        $currentuser = 0;
    } else if (!in_array($currentuser, array_keys($gradable))) {
        $currentuser = array_shift($gradable)->id;
    }
}

$urlparams = array('id' => $id);
$navurl = new moodle_url('/report/competency/index.php', $urlparams);
$urlparams['user'] = $currentuser;
$urlparams['mod'] = $currentmodule;
$url = new moodle_url('/report/competency/index.php', $urlparams);

$title = get_string('pluginname', 'report_competency');
$coursename = format_string($course->fullname, true, array('context' => $context));

$PAGE->navigation->override_active_url($navurl);
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($coursename);
$PAGE->set_pagelayout('incourse');

$output = $PAGE->get_renderer('report_competency');

echo $output->header();
$baseurl = new moodle_url('/report/competency/index.php');
$nav = new \report_competency\output\user_course_navigation($currentuser, $course->id, $baseurl, $currentmodule);
echo $output->render($nav);
if ($currentuser > 0) {
    $user = core_user::get_user($currentuser);
    $usercontext = context_user::instance($currentuser);
    $userheading = array(
        'heading' => fullname($user),
        'user' => $user,
        'usercontext' => $usercontext
    );
    if ($currentmodule > 0) {
        $title = get_string('filtermodule', 'report_competency', format_string($cm->name));
    }
    echo $output->context_header($userheading, 3);
}
echo $output->container('', 'clearfix');
echo $output->heading($title, 3);

if ($currentuser > 0) {
    $page = new \report_competency\output\report($course->id, $currentuser, $currentmodule);
    echo $output->render($page);
} else {
    echo $output->container('', 'clearfix');
    echo $output->notify_problem(get_string('noparticipants', 'tool_lp'));
}
echo $output->footer();
