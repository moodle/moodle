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

$id = required_param('id', PARAM_INT);       // Course id.
$modids = optional_param_array('modids', [], PARAM_INT);

if ($id) {
    if (!$course = $DB->get_record('course', array('id' => $id))) {
        throw new \moodle_exception('invalidcourseid');
    }
}

if ($id == SITEID) {
    $context = context_system::instance();
    $title = get_string('defaultcompletion', 'completion');
    $heading = format_string($SITE->fullname, true, ['context' => $context]);
} else {
    $context = context_course::instance($id);
    $title = $course->shortname;
    $heading = $course->fullname;
}
require_login($course);
require_capability('moodle/course:manageactivities', $context);

// Set up the page.
if ($id != SITEID) {
    navigation_node::override_active_url(new moodle_url('/course/completion.php', array('id' => $course->id)));
    $PAGE->set_course($course);
}
$PAGE->set_url('/course/defaultcompletion.php', ['id' => $id]);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);
$PAGE->set_heading($heading);


// Get list of modules that have been sent in the form.
$manager = new \core_completion\manager($course->id);
$allmodules = $manager->get_activities_and_resources(false);
$modules = [];
foreach ($allmodules->modules as $module) {
    if ($module->canmanage && in_array($module->id, $modids)) {
        $modules[$module->id] = $module;
    }
}

$form = null;
if (!empty($modules)) {
    $form = new core_completion_defaultedit_form(
        null,
        ['course' => $course, 'modules' => $modules, 'displaycancel' => false, 'forceuniqueid' => true]
    );
    if (!$form->is_cancelled() && $data = $form->get_data()) {
        $data->modules = $modules;
        $manager->apply_default_completion($data, $form->has_custom_completion_rules(), $form->get_suffix());
    }
}

$renderer = $PAGE->get_renderer('core_course', 'bulk_activity_completion');

// Print the form.
echo $renderer->header();

if ($id == SITEID) {
    echo $renderer->heading($title);
} else {
    $actionbar = new \core_course\output\completion_action_bar($course->id, $PAGE->url);
    echo $renderer->render_course_completion_action_bar($actionbar);
}

echo $renderer->defaultcompletion($allmodules, $modules, $form);

echo $OUTPUT->footer();
