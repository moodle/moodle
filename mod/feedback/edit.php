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
 * prints the form to edit the feedback items such moving, deleting and so on
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */

require_once('../../config.php');
require_once('lib.php');

feedback_init_feedback_session();

$id = required_param('id', PARAM_INT);

if (($formdata = data_submitted()) AND !confirm_sesskey()) {
    throw new \moodle_exception('invalidsesskey');
}

$switchitemrequired = optional_param('switchitemrequired', false, PARAM_INT);
$deleteitem = optional_param('deleteitem', false, PARAM_INT);

list($course, $cm) = get_course_and_cm_from_cmid($id, 'feedback');

$context = context_module::instance($cm->id);
require_login($course, false, $cm);
require_capability('mod/feedback:edititems', $context);
$feedback = $PAGE->activityrecord;
$feedbackstructure = new mod_feedback_structure($feedback, $cm);
$url = new moodle_url('/mod/feedback/edit.php', ['id' => $cm->id]);

if ($switchitemrequired) {
    require_sesskey();
    $items = $feedbackstructure->get_items();
    if (isset($items[$switchitemrequired])) {
        feedback_switch_item_required($items[$switchitemrequired]);
    }
    redirect($url);
}

if ($deleteitem) {
    require_sesskey();
    $items = $feedbackstructure->get_items();
    if (isset($items[$deleteitem])) {
        feedback_delete_item($deleteitem);
    }
    redirect($url);
}

//Get the feedbackitems
$lastposition = 0;
$feedbackitems = $DB->get_records('feedback_item', ['feedback' => $feedback->id], 'position');
if (is_array($feedbackitems)) {
    $feedbackitems = array_values($feedbackitems);
    if (count($feedbackitems) > 0) {
        $lastitem = $feedbackitems[count($feedbackitems)-1];
        $lastposition = $lastitem->position;
    } else {
        $lastposition = 0;
    }
}
$lastposition++;

$PAGE->set_url($url);
$PAGE->set_heading($course->fullname);
$PAGE->set_title($feedback->name);
$actionbar = new \mod_feedback\output\edit_action_bar($cm->id, $url, $lastposition);
$PAGE->activityheader->set_attrs([
    'hidecompletion' => true,
    'description' => ''
]);
$PAGE->add_body_class('limitedwidth');

//Adding the javascript module for the items dragdrop.
if (count($feedbackitems) > 1) {
    $PAGE->requires->strings_for_js([
        'pluginname',
        'move_item',
        'position',
    ], 'feedback');
    $PAGE->requires->yui_module(
        'moodle-mod_feedback-dragdrop',
        'M.mod_feedback.init_dragdrop',
        [['cmid' => $cm->id]]
    );
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('edit_items', 'mod_feedback'), 3);

/** @var \mod_feedback\output\renderer $renderer */
$renderer = $PAGE->get_renderer('mod_feedback');
echo $renderer->main_action_bar($actionbar);
$form = new mod_feedback_complete_form(mod_feedback_complete_form::MODE_EDIT,
        $feedbackstructure, 'feedback_edit_form');
echo '<div id="feedback_dragarea">'; // The container for the dragging area.
$form->display();
echo '</div>';

echo $OUTPUT->footer();
