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
 * deletes a template
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */

require_once("../../config.php");
require_once("lib.php");

$current_tab = 'templates';

$id = required_param('id', PARAM_INT);
$deletetempl = optional_param('deletetempl', false, PARAM_INT);

$baseurl = new moodle_url('/mod/feedback/delete_template.php', array('id' => $id));
$PAGE->set_url($baseurl);

list($course, $cm) = get_course_and_cm_from_cmid($id, 'feedback');
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/feedback:deletetemplate', $context);

$feedback = $PAGE->activityrecord;
$systemcontext = context_system::instance();

// Process template deletion.
if ($deletetempl) {
    require_sesskey();
    $template = $DB->get_record('feedback_template', array('id' => $deletetempl), '*', MUST_EXIST);

    if ($template->ispublic) {
        require_capability('mod/feedback:createpublictemplate', $systemcontext);
        require_capability('mod/feedback:deletetemplate', $systemcontext);
    }

    feedback_delete_template($template);
    redirect($baseurl, get_string('template_deleted', 'feedback'));
}

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");
$strdeletefeedback = get_string('delete_template', 'feedback');

navigation_node::override_active_url(new moodle_url('/mod/feedback/edit.php',
        array('id' => $id, 'do_show' => 'templates')));
$PAGE->set_heading($course->fullname);
$PAGE->set_title($feedback->name);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($feedback->name));
/// print the tabs
require('tabs.php');

// Print the main part of the page.
echo $OUTPUT->heading($strdeletefeedback, 3);

// First we get the course templates.
$templates = feedback_get_template_list($course, 'own');
echo $OUTPUT->box_start('coursetemplates');
echo $OUTPUT->heading(get_string('course'), 4);
$tablecourse = new mod_feedback_templates_table('feedback_template_course_table', $baseurl);
$tablecourse->display($templates);
echo $OUTPUT->box_end();
// Now we get the public templates if it is permitted.
if (has_capability('mod/feedback:createpublictemplate', $systemcontext) AND
    has_capability('mod/feedback:deletetemplate', $systemcontext)) {
    $templates = feedback_get_template_list($course, 'public');
    echo $OUTPUT->box_start('publictemplates');
    echo $OUTPUT->heading(get_string('public', 'feedback'), 4);
    $tablepublic = new mod_feedback_templates_table('feedback_template_public_table', $baseurl);
    $tablepublic->display($templates);
    echo $OUTPUT->box_end();
}

$url = new moodle_url('/mod/feedback/edit.php', array('id' => $id, 'do_show' => 'templates'));
echo $OUTPUT->single_button($url, get_string('back'), 'post');

echo $OUTPUT->footer();

