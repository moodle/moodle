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

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->libdir . '/gradelib.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$groupid = required_param('group', PARAM_INT);
$periodid = required_param('period', PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$group = $DB->get_record('groups', array('id' => $groupid), '*', MUST_EXIST);

$context = context_course::instance($course->id);

require_capability('block/post_grades:canpost', $context);

grade_regrade_final_grades($course->id);

$s = ues::gen_str('block_post_grades');

$periods = post_grades::active_periods($course);

if (empty($periods) or !isset($periods[$periodid])) {
    print_error('notactive', 'block_post_grades');
}

$period = $periods[$periodid];

$validgroups = post_grades::valid_groups($course);

if (!isset($validgroups[$groupid])) {
    print_error('notvalidgroup', 'block_post_grades', '', $group->name);
}

$blockname = $s('pluginname');
$heading = $s($period->post_type);

$title = $group->name . ': ' . $heading;

$baseurl = new moodle_url('/blocks/post_grades/confirm.php', array(
    'courseid' => $courseid,
    'period' => $periodid,
    'group' => $groupid
));

$PAGE->set_url($baseurl);
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->navbar->add($blockname);
$PAGE->navbar->add($heading);
$PAGE->navbar->add($group->name);

$output = $PAGE->get_renderer('block_post_grades');

$screen = post_grades::create($period, $course, $group);

echo $output->header();
echo $output->heading($heading);

$return = $screen->get_return_state();

if ($return->is_ready()) {
    // Post grade link.
    if ($return instanceof post_grades_delegating_return) {
        // Instructor can use regular re-routing.
        echo $output->confirm_return($return, false);
    }
    echo $output->confirm_period($course, $group, $period);
} else {
    echo $output->confirm_return($return);
}

echo $output->box_start();
echo $screen->html();
echo $output->box_end();

echo $output->footer();