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
 * Edit course tags
 *
 * @package    core_course
 * @copyright  2015 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../config.php");
require_once($CFG->dirroot . '/course/tags_form.php');

$id = required_param('id', PARAM_INT); // Course id.
$returnurl = optional_param('return', null, PARAM_LOCALURL);
$course = get_course($id);

require_login();

// Check capabilities but do not call require_login($course) - the user does not have to be enrolled.
$context = context_course::instance($course->id);
if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
    print_error('coursehidden', '', $CFG->wwwroot .'/');
}
require_capability('moodle/course:tag', $context);
if (!core_tag_tag::is_enabled('core', 'course')) {
    print_error('tagsaredisabled', 'tag');
}

$PAGE->set_course($course);
$PAGE->set_pagelayout('incourse');
$PAGE->set_url('/course/tags.php', array('id' => $course->id));
$PAGE->set_title(get_string('coursetags', 'tag'));
$PAGE->set_heading($course->fullname);

$form = new coursetags_form();
$data = array('id' => $course->id, 'tags' => core_tag_tag::get_item_tags_array('core', 'course', $course->id));
$form->set_data($data);

$redirecturl = $returnurl ? new moodle_url($returnurl) : course_get_url($course);
if ($form->is_cancelled()) {
    redirect($redirecturl);
} else if ($data = $form->get_data()) {
    core_tag_tag::set_item_tags('core', 'course', $course->id, context_course::instance($course->id), $data->tags);
    redirect($redirecturl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('coursetags', 'tag'));

$form->display();

echo $OUTPUT->footer();
