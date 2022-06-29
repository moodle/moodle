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
 * print the confirm dialog to use template and create new items from template
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */

require_once("../../config.php");
require_once("lib.php");

$id = required_param('id', PARAM_INT);
$templateid = optional_param('templateid', false, PARAM_INT);
$mode = optional_param('mode', '', PARAM_ALPHA);

if (!$templateid) {
    redirect('edit.php?id='.$id);
}

$url = new moodle_url('/mod/feedback/use_templ.php', array('id'=>$id, 'templateid'=>$templateid));
$PAGE->set_url($url);

list($course, $cm) = get_course_and_cm_from_cmid($id, 'feedback');
$context = context_module::instance($cm->id);

require_login($course, true, $cm);

$feedback = $PAGE->activityrecord;
$feedbackstructure = new mod_feedback_structure($feedback, $cm, 0, $templateid);

require_capability('mod/feedback:edititems', $context);

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$params = ['id' => $id];
$params += ($mode ? ['mode' => $mode] : []);
$activeurl = new moodle_url('/mod/feedback/manage_templates.php', $params);
$PAGE->set_url($activeurl);

if ($mode == 'manage') {
    navigation_node::override_active_url($activeurl);
} else {
    navigation_node::override_active_url(new moodle_url('/mod/feedback/view.php', $params));
}

$PAGE->set_heading($course->fullname);
$PAGE->set_title($feedback->name);
$PAGE->activityheader->set_attrs([
    "hidecompletion" => true,
    "description" => ''
]);
$actionbar = new \mod_feedback\output\edit_template_action_bar($cm->id, $templateid, $mode);
/** @var \mod_feedback\output\renderer $renderer */
$renderer = $PAGE->get_renderer('mod_feedback');

echo $OUTPUT->header();
echo $renderer->main_action_bar($actionbar);

$form = new mod_feedback_complete_form(mod_feedback_complete_form::MODE_VIEW_TEMPLATE,
        $feedbackstructure, 'feedback_preview_form', ['templateid' => $templateid]);
$form->display();

echo $OUTPUT->footer();

