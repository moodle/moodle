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
 * This file is part of the Database module for Moodle
 *
 * @copyright 2005 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package mod_data
 */

use mod_data\manager;

require_once('../../config.php');
require_once('lib.php');

$id    = optional_param('id', 0, PARAM_INT);  // course module id
$d     = optional_param('d', 0, PARAM_INT);   // database id
$mode  = optional_param('mode', 'addtemplate', PARAM_ALPHA);
$action  = optional_param('action', '', PARAM_ALPHA);
$useeditor = optional_param('useeditor', null, PARAM_BOOL);

$url = new moodle_url('/mod/data/templates.php');

if ($id) {
    list($course, $cm) = get_course_and_cm_from_cmid($id, manager::MODULE);
    $manager = manager::create_from_coursemodule($cm);
    $url->param('d', $cm->instance);
} else {   // We must have $d.
    $instance = $DB->get_record('data', ['id' => $d], '*', MUST_EXIST);
    $manager = manager::create_from_instance($instance);
    $cm = $manager->get_coursemodule();
    $course = get_course($cm->course);
    $url->param('d', $d);
}

$instance = $manager->get_instance();
$context = $manager->get_context();

$url->param('mode', $mode);
$PAGE->set_url($url);

require_login($course, false, $cm);
require_capability('mod/data:managetemplates', $context);

if ($action == 'resetalltemplates') {
    require_sesskey();
    $manager->reset_all_templates();
    redirect($PAGE->url, get_string('templateresetall', 'mod_data'), null, \core\output\notification::NOTIFY_SUCCESS);
}

$manager->set_template_viewed();

if ($useeditor !== null) {
    // The useeditor param was set. Update the value for this template.
    data_set_config($instance, "editor_{$mode}", !!$useeditor);
}

$PAGE->requires->js('/mod/data/data.js');
$PAGE->set_title($instance->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('admin');
$PAGE->force_settings_menu(true);
$PAGE->activityheader->disable();
$PAGE->add_body_class('mediumwidth');

echo $OUTPUT->header();

$renderer = $manager->get_renderer();
// Check if it is an empty database with no fields.
if (!$manager->has_fields()) {
    echo $renderer->render_templates_zero_state($manager);
    echo $OUTPUT->footer();
    // Don't check the rest of the options. There is no field, there is nothing else to work with.
    exit;
}

$actionbar = new \mod_data\output\action_bar($instance->id, $url);
echo $actionbar->get_templates_action_bar();

if (($formdata = data_submitted()) && confirm_sesskey()) {
    if (!empty($formdata->defaultform)) {
        // Reset the template to default.
        if (!empty($formdata->resetall)) {
            $manager->reset_all_templates();
            $notificationstr = get_string('templateresetall', 'mod_data');
        } else {
            $manager->reset_template($mode);
            $notificationstr = get_string('templatereset', 'data');
        }
    } else {
        $manager->update_templates($formdata);
        $notificationstr = get_string('templatesaved', 'data');
    }
}

if (!empty($notificationstr)) {
    echo $OUTPUT->notification($notificationstr, 'notifysuccess');
}

$templateeditor = new \mod_data\output\template_editor($manager, $mode);
echo $renderer->render($templateeditor);

/// Finish the page
echo $OUTPUT->footer();
