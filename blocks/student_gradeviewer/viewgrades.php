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
 * @package    block_ues_people
 * @copyright  2008 Onwards - Louisiana State University
 * @copyright  2008 Onwards - Philip Cali, Jason Peak, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/student_gradeviewer/lib.php');
require_once($CFG->dirroot . '/blocks/student_gradeviewer/classes/total_grade.php');
require_once($CFG->dirroot . '/grade/lib.php');

require_login();

$id = required_param('id', PARAM_INT);
$courseid = optional_param('courseid', null, PARAM_INT);

$user = $DB->get_record('user', array('id' => $id), '*', MUST_EXIST);

$context = context_system::instance();

$mentor = (
    has_capability('block/student_gradeviewer:sportsgrades', $context) or
    has_capability('block/student_gradeviewer:viewgrades', $context)
);

if (!$mentor) {
    print_error('no_permission', 'block_student_gradeviewer');
}

$baseurl = new moodle_url('/blocks/student_gradeviewer/viewgrades.php', array(
    'id' => $id
));

$s = ues::gen_str('block_student_gradeviewer');

$blockname = $s('pluginname');
$student = fullname($user);

$PAGE->set_context($context);
$PAGE->set_url($baseurl);
$PAGE->navbar->add($blockname);
$PAGE->navbar->add($student);
$PAGE->set_title($s('viewgrades', $student));
$PAGE->set_heading("$blockname: $student");

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help(
    $s('viewgrades', $student), 'viewgrades', 'block_student_gradeviewer'
);

$i = function($key) {
    return get_string($key, 'grades');
};

$courses = enrol_get_users_courses($id);

if (empty($courses)) {
    echo $OUTPUT->notification($s('no_courses', $student));
    echo $OUTPUT->footer();
    exit();
}

$options = array_map(student_gradeviewer::grade_gen($id), $courses);
$courseid = empty($courseid) ? key($courses) : $courseid;

echo $OUTPUT->box_start();
echo html_writer::start_tag('ul', array('class' => 'course-grades'));
foreach ($options as $cid => $display) {
    $url = new moodle_url($baseurl, array('courseid' => $cid));
    $link = html_writer::link($url, $display);
    $params = array('class' => 'graded-course');

    if ($cid == $courseid) {
        $params['class'] .= ' selected';
        $link = html_writer::tag('strong', $link);
    }

    echo html_writer::tag('li', $link, $params);
}
echo html_writer::end_tag('ul');
echo $OUTPUT->box_end();

$course = $courses[$courseid];

echo $OUTPUT->heading($course->fullname, 3);

$graded = explode(',', get_config('moodle', 'gradebookroles'));

grade_regrade_final_grades($course->id);

$table = new html_table();

$table->head = array(
    $i('itemname'), $i('category'),
    $i('overridden') . $OUTPUT->help_icon('overridden', 'grades'),
    $i('excluded') . $OUTPUT->help_icon('excluded', 'grades'),
    $i('hidden') . $OUTPUT->help_icon('hidden', 'grades'),
    $i('range'), $i('rank'), $i('feedback'), $i('finalgrade') . ' / ' . $i('grademax')
);

$tree = new grade_tree($course->id, true, true, null, !$CFG->enableoutcomes);

$context = context_course::instance($course->id);

// In case there is more than one role, grab a user that is graded.
$totalusers = array();
foreach ($graded as $gradedrole) {
    $totalusers = $totalusers + get_role_users($gradedrole, $context);
}

foreach ($tree->get_items() as $item) {
    $line = array();

    $parent = $item->get_parent_category();

    // Load item, but don't create it.
    $grade = $item->get_grade($id, false);

    if (empty($grade->id)) {
        $grade->finalgrade = null;
        $grade->feedback = null;
        $grade->feedbackformat = FORMAT_MOODLE;
    }
    $grade->grade_item = $item;

    $decimals = $item->get_decimals();

    $line[] = $item->get_name();
    $line[] = $parent->get_name();
    $line[] = $grade->is_overridden() ? 'Y' : 'N';
    $line[] = $grade->is_excluded() ? 'Y' : 'N';
    $line[] = $item->is_hidden() ? 'Y' : 'N';
    $line[] = format_float($item->grademin, $decimals) . ' - ' .
        format_float($item->grademax, $decimals);
    $line[] = student_gradeviewer::rank($context, $grade, $totalusers);
    $line[] = format_text($grade->feedback, $grade->feedbackformat);
    if ($item->itemtype == 'course') {
        $finalsggrade = sg_get_grade_for_course($course->id, $user->id);
            $line[] = $finalsggrade[0] ?
            $finalsggrade[0] . ' / ' . $finalsggrade[1] :
            grade_format_gradevalue($grade->finalgrade, $item) . ' / ' . grade_format_gradevalue($item->grademax, $item);
    } else {
        $line[] = grade_format_gradevalue($grade->finalgrade, $item) . ' / ' . grade_format_gradevalue($item->grademax, $item);
    }
    $table->data[] = $line;
}

$params = array('class' => 'table-output');
echo html_writer::tag('div', html_writer::table($table), $params);

echo $OUTPUT->footer();
