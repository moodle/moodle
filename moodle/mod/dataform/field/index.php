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
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * The Dataform has been developed as an enhanced counterpart
 * of Moodle's Database activity module (1.9.11+ (20110323)).
 * To the extent that Dataform code corresponds to Database code,
 * certain copyrights on the Database module may obtain.
 */

require_once('../../../config.php');
require_once("$CFG->libdir/tablelib.php");

$urlparams = new stdClass;

$urlparams->d = optional_param('d', 0, PARAM_INT);
$urlparams->id = optional_param('id', 0, PARAM_INT);
$urlparams->fid = optional_param('fid', 0 , PARAM_INT);          // update field id

// Fields list actions.
$urlparams->new        = optional_param('new', 0, PARAM_ALPHA);     // type of the new field
$urlparams->delete     = optional_param('delete', 0, PARAM_SEQUENCE);   // ids (comma delimited) of fields to delete
$urlparams->duplicate  = optional_param('duplicate', 0, PARAM_SEQUENCE);   // ids (comma delimited) of fields to duplicate
$urlparams->visible    = optional_param('visible', 0, PARAM_INT);     // id of field to hide/(show to owner)/show to all
$urlparams->editable    = optional_param('editable', 0, PARAM_INT);     // id of field to set editing

$urlparams->confirmed    = optional_param('confirmed', 0, PARAM_INT);

// Set a dataform object.
$df = mod_dataform_dataform::instance($urlparams->d, $urlparams->id);
$df->require_manage_permission('fields');

$df->set_page('field/index', array('urlparams' => $urlparams));
$PAGE->set_context($df->context);

// Activate navigation node.
navigation_node::override_active_url(new moodle_url('/mod/dataform/field/index.php', array('id' => $df->cm->id)));

// DATA PROCESSING.
$fieldman = $df->field_manager;
if ($urlparams->duplicate and confirm_sesskey()) {
    // Duplicate requested fields.
    $fieldman->process_fields('duplicate', $urlparams->duplicate, $urlparams->confirmed);
} else if ($urlparams->delete and confirm_sesskey()) {
    // Delete requested fields.
    $fieldman->process_fields('delete', $urlparams->delete, $urlparams->confirmed);
} else if ($urlparams->visible and confirm_sesskey()) {
    // Set field visibility.
    $fieldman->process_fields('visible', $urlparams->visible, true);    // confirmed by default
} else if ($urlparams->editable and confirm_sesskey()) {
    // Set field editability.
    $fieldman->process_fields('editable', $urlparams->editable, true);    // confirmed by default
}

// Get the fields.
$fields = $fieldman->get_fields(array('forceget' => true, 'sort' => 'name'));
$internalfields = array();

// Seperate internal fields.
$internalfieldtypes = $fieldman->get_internal_field_types();
foreach ($fields as $fieldid => $field) {
    if (array_key_exists($fieldid, $internalfieldtypes)) {
        $internalfields[$fieldid] = $field;
        unset($fields[$fieldid]);
    }
}

$output = $df->get_renderer();
echo $output->header(array('tab' => 'fields', 'heading' => $df->name, 'urlparams' => $urlparams));

// Display subplugin selector.
$exclude = array('entryactions', 'entryauthor', 'entrygroup', 'entrytime');
echo $output->subplugin_select('field', array('exclude' => $exclude));

// Print admin style list of user defined fields.
echo $output->fields_admin_list('external', '', $fields);

// Print admin style list of internal fields.
echo $output->fields_admin_list('internal', get_string('fieldsinternal', 'dataform'), $internalfields);

echo $output->footer();
