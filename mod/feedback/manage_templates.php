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
$templateid = optional_param('deletetemplate', 0, PARAM_INT);

list($course, $cm) = get_course_and_cm_from_cmid($id, 'feedback');
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/feedback:edititems', $context);

$feedback = $PAGE->activityrecord;
$systemcontext = context_system::instance();

$params = ['id' => $id];
$url = new moodle_url('/mod/feedback/manage_templates.php', $params);

$PAGE->set_url($url);

$PAGE->set_heading($course->fullname);

/** @var \mod_feedback\output\renderer $renderer */
$renderer = $PAGE->get_renderer('mod_feedback');
$renderer->set_title(
        [format_string($feedback->name), format_string($course->fullname)],
        get_string('templates', 'feedback')
);

$PAGE->add_body_class('limitedwidth');

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
echo $OUTPUT->heading(get_string('templates', 'mod_feedback'), 3);

// First we get the course templates.
$templates = feedback_get_template_list($course, 'own');
echo $OUTPUT->box_start('coursetemplates');
echo $OUTPUT->heading(get_string('coursetemplates', 'mod_feedback'), 4);

$baseurl = new moodle_url('/mod/feedback/use_templ.php', $params);
$tablecourse = new mod_feedback_templates_table('feedback_template_course_table', $baseurl);
$tablecourse->display($templates);
echo $OUTPUT->box_end();

$templates = feedback_get_template_list($course, 'public');
echo $OUTPUT->box_start('publictemplates');
echo $OUTPUT->heading(get_string('sitetemplates', 'mod_feedback'), 4);
$tablepublic = new mod_feedback_templates_table('feedback_template_public_table', $baseurl);
$tablepublic->display($templates);
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
