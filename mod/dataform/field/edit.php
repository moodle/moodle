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
 * @package dataformfield
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');

$urlparams = new stdClass;
// Dataform id.
$urlparams->d          = required_param('d', PARAM_INT);
// Type of a field to edit.
$urlparams->type       = optional_param('type', '', PARAM_ALPHA);
// Field id to edit.
$urlparams->fid        = optional_param('fid', 0, PARAM_INT);

// Set a dataform object.
$df = mod_dataform_dataform::instance($urlparams->d);

$df->set_page('field/edit', array('urlparams' => $urlparams));
$df->require_manage_permission('fields');

if ($urlparams->fid) {
    // Force get.
    $field = $df->field_manager->get_field_by_id($urlparams->fid, true);
} else if ($urlparams->type) {
    $field = $df->field_manager->get_field($urlparams->type);
}

$internalfield = ($field instanceof \mod_dataform\pluginbase\dataformfield_internal);

// Must have add instance capability in the dataform context for user fields.
if (!$internalfield) {
    $requiredcapability = "dataformfield/$field->type:addinstance";
    require_capability($requiredcapability, $df->context);
}

$mform = $field->get_form();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/dataform/field/index.php', array('d' => $df->id)));
}

// Process validated.
if ($data = $mform->get_data()) {

    if (!$field->id) {
        // Add new field.
        $field->create($data);

    } else {
        // Update field.
        $data->id = $field->id;

        // Prepare for replacing patterns.
        $oldname = $field->name;
        $oldpatterns = $field->renderer->get_patterns();

        $field->update($data);

        // Replace patterns.
        if ($oldname != $field->name) {
            $newpatterns = $field->renderer->get_patterns();
            $df->view_manager->replace_patterns_in_views($oldpatterns, $newpatterns);
        }
    }

    if ($data->submitbutton != get_string('savecont', 'dataform')) {
        redirect(new moodle_url('/mod/dataform/field/index.php', array('d' => $df->id)));
    }

    // Continue to edit so refresh the form.
    $mform = $field->get_form();
}

// Activate navigation node.
navigation_node::override_active_url(new moodle_url('/mod/dataform/field/index.php', array('id' => $df->cm->id)));

$output = $df->get_renderer();
echo $output->header(array('tab' => 'fields', 'heading' => $df->name, 'nonotifications' => true, 'urlparams' => $urlparams));

$formheading = $field->id ? get_string('fieldedit', 'dataform', $field->name) : get_string('fieldnew', 'dataform', $field->get_typename());
echo html_writer::tag('h2', format_string($formheading), array('class' => 'mdl-align'));

// Display form.
$mform->set_data($field->data);
$mform->display();

echo $output->footer();
