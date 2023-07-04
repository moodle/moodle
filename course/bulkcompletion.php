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
 * Bulk activity completion selection
 *
 * @package     core_completion
 * @category    completion
 * @copyright   2017 Adrian Greeve
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/completionlib.php');

$id = required_param('id', PARAM_INT); // Course id.
$cmids = optional_param_array('cmid', [], PARAM_INT);

// Perform some basic access control checks.
if ($id) {

    if ($id == SITEID) {
        // Don't allow editing of 'site course' using this form.
        throw new \moodle_exception('cannoteditsiteform');
    }

    if (!$course = $DB->get_record('course', array('id' => $id))) {
        throw new \moodle_exception('invalidcourseid');
    }
    require_login($course);

} else {
    require_login();
    throw new \moodle_exception('needcourseid');
}

// Set up the page.
navigation_node::override_active_url(new moodle_url('/course/completion.php', array('id' => $course->id)));
$PAGE->set_course($course);
$PAGE->set_url('/course/bulkcompletion.php', array('id' => $course->id));
$PAGE->set_title($course->shortname);
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('admin');

// Check access.
if (!core_completion\manager::can_edit_bulk_completion($id)) {
    require_capability('moodle/course:manageactivities', context_course::instance($course->id));
}

// Get all that stuff I need for the renderer.
$manager = new \core_completion\manager($id);
$bulkcompletiondata = $manager->get_activities_and_headings();

$renderer = $PAGE->get_renderer('core_course', 'bulk_activity_completion');

// Print the form.
echo $OUTPUT->header();

$actionbar = new \core_course\output\completion_action_bar($course->id, $PAGE->url);
echo $renderer->render_course_completion_action_bar($actionbar);

$PAGE->requires->js_call_amd('core_form/changechecker', 'watchFormById', ['theform']);

echo $renderer->bulkcompletion($bulkcompletiondata);

echo $OUTPUT->footer();
