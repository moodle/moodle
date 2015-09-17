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
 * This page lets users to manage site wide learning plan templates.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$id = optional_param('id', 0, PARAM_INT);
$pagecontextid = required_param('pagecontextid', PARAM_INT);  // Reference to where we can from.

if (!empty($id)) {
    // Always use the context from the framework when it exists.
    $template = new \tool_lp\template($id);
    $context = $template->get_context();
} else {
    $context = context::instance_by_id($pagecontextid);
}

// We check that we have the permission to edit this framework, in its own context.
require_login();
require_capability('tool/lp:templatemanage', $context);

// We keep the original context in the URLs, so that we remain in the same context.
$url = new moodle_url("/admin/tool/lp/edittemplate.php", array('id' => $id, 'pagecontextid' => $pagecontextid));
$templatesurl = new moodle_url('/admin/tool/lp/learningplans.php', array('pagecontextid' => $pagecontextid));
$formurl = new moodle_url("/admin/tool/lp/edittemplate.php", array('pagecontextid' => $pagecontextid));

$title = get_string('templates', 'tool_lp');
if (empty($id)) {
    $pagetitle = get_string('addnewtemplate', 'tool_lp');
} else {
    $pagetitle = get_string('edittemplate', 'tool_lp');
}
// Set up the page.
$PAGE->navigation->override_active_url($templatesurl);
$PAGE->set_context(context::instance_by_id($pagecontextid));
$PAGE->set_pagelayout('admin');
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$output = $PAGE->get_renderer('tool_lp');

$form = new \tool_lp\form\template($formurl->out(false), array('id' => $id, 'context' => $context));

if ($form->is_cancelled()) {
    redirect($templatesurl);
}

echo $output->header();
echo $output->heading($pagetitle);

$data = $form->get_data();
if ($data) {
    // Save the changes and continue back to the manage page.
    // Massage the editor data.
    $data->descriptionformat = $data->description['format'];
    $data->description = $data->description['text'];
    if (empty($data->id)) {
        // Create new template.
        require_sesskey();
        $data->contextid = $context->id;
        \tool_lp\api::create_template($data);
        echo $output->notification(get_string('templatecreated', 'tool_lp'), 'notifysuccess');
        echo $output->continue_button($templatesurl);
    } else {
        require_sesskey();
        \tool_lp\api::update_template($data);
        echo $output->notification(get_string('templateupdated', 'tool_lp'), 'notifysuccess');
        echo $output->continue_button($templatesurl);
    }
} else {
    $form->display();
}


echo $output->footer();
