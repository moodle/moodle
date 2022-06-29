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
 * Manage the various templates available
 *
 * @author Peter Dias
 * @copyright 2021 Peter Dias
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */

require_once("../../config.php");
require_once("lib.php");

$id = required_param('id', PARAM_INT);
$mode = optional_param('mode', '', PARAM_ALPHA);
$templateid = optional_param('deletetemplate', 0, PARAM_INT);

list($course, $cm) = get_course_and_cm_from_cmid($id, 'feedback');
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/feedback:edititems', $context);

$feedback = $PAGE->activityrecord;
$systemcontext = context_system::instance();

$params = ['id' => $id];
if ($mode) {
    $params += ['mode' => $mode];
}
$url = new moodle_url('/mod/feedback/manage_templates.php', $params);
if ($mode == 'manage') {
    navigation_node::override_active_url($url);
} else {
    navigation_node::override_active_url(new moodle_url('/mod/feedback/view.php', $params));
}

$PAGE->set_url($url);
$actionbar = new \mod_feedback\output\edit_action_bar($cm->id, $url);

$PAGE->set_heading($course->fullname);
$PAGE->set_title($feedback->name);

// Process template deletion.
if ($templateid) {
    require_sesskey();
    require_capability('mod/feedback:deletetemplate', $context);
    $template = $DB->get_record('feedback_template', ['id' => $templateid], '*', MUST_EXIST);

    if ($template->ispublic) {
        require_capability('mod/feedback:createpublictemplate', $systemcontext);
        require_capability('mod/feedback:deletetemplate', $systemcontext);
    }

    feedback_delete_template($template);
    $successurl = new moodle_url('/mod/feedback/manage_templates.php', ['id' => $id]);
    redirect($url, get_string('template_deleted', 'feedback'), null, \core\output\notification::NOTIFY_SUCCESS);
}
$PAGE->activityheader->set_attrs([
    "hidecompletion" => true,
    "description" => ''
]);
echo $OUTPUT->header();
/** @var \mod_feedback\output\renderer $renderer */
$renderer = $PAGE->get_renderer('mod_feedback');
if (!$mode) {
    echo $renderer->main_action_bar($actionbar);
}
echo $OUTPUT->heading(get_string('templates', 'mod_feedback'), 3);

// First we get the course templates.
$templates = feedback_get_template_list($course, 'own');
echo $OUTPUT->box_start('coursetemplates');
echo $OUTPUT->heading(get_string('course'), 4);

$baseurl = new moodle_url('/mod/feedback/use_templ.php', $params);
$tablecourse = new mod_feedback_templates_table('feedback_template_course_table', $baseurl, $mode);
$tablecourse->display($templates);
echo $OUTPUT->box_end();

$templates = feedback_get_template_list($course, 'public');
echo $OUTPUT->box_start('publictemplates');
echo $OUTPUT->heading(get_string('public', 'feedback'), 4);
$tablepublic = new mod_feedback_templates_table('feedback_template_public_table', $baseurl, $mode);
$tablepublic->display($templates);
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
