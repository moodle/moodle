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
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$id = required_param('id', PARAM_INT);

$params = array('id' => $id);
$course = $DB->get_record('course', $params, '*', MUST_EXIST);
require_login($course);
$context = context_course::instance($course->id);
$currentgroup = optional_param('group', null, PARAM_INT);
$urlparams = array('id' => $id, 'group' => $currentgroup);

$url = new moodle_url('/report/competency/index.php', $urlparams);
$title = get_string('pluginname', 'report_competency');
$PAGE->set_url($url);
$PAGE->set_title($title);
$coursename = format_text($course->fullname, false, array('context' => $context));
$PAGE->set_heading($coursename);
$PAGE->set_pagelayout('incourse');

$output = $PAGE->get_renderer('report_competency');
echo $output->header();
echo $output->heading($title);

$select = groups_allgroups_course_menu($course, $url, true, $currentgroup);

// User cannot see any group.
if (empty($select)) {
    echo $OUTPUT->heading(get_string("notingroup"));
    echo $OUTPUT->footer();
    exit;
} else {
    echo $select;
}

// Fetch current active group.
$groupmode = groups_get_course_groupmode($course);
$currentgroup = $SESSION->activegroup[$course->id][$groupmode][$course->defaultgroupingid];

// Will exclude suspended users if required.
$defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
$showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
$showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $context);

$page = new \report_competency\output\report($course->id, $currentgroup, $showonlyactiveenrol);
echo $output->render($page);

echo $output->footer();
