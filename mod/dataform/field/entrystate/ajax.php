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
 * This page receives entrystate ajax submissions
 *
 * @package dataformfield
 * @subpackage entrystate
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once('../../../../config.php');

$result = new stdClass;

// If session has expired and its an ajax request so we cant do a page redirect.
if ( !isloggedin() ) {
    $result->error = get_string('sessionerroruser', 'error');
    echo json_encode($result);
    die();
}

$d = required_param('d', PARAM_INT);
$fieldid = required_param('fieldid', PARAM_INT);
$entryid = required_param('entryid', PARAM_INT);
$newstate = required_param('state', PARAM_INT);

// Instantiate the Dataform.
$df = mod_dataform_dataform::instance($d);
require_login($df->course->id, false, $df->cm);

// Sesskey.
require_sesskey();

$PAGE->set_context($df->context);
$PAGE->set_url('/mod/dataform/field/entrystate/ajax.php', array('contextid' => $df->context->id));

// Get the field.
$field = $df->field_manager->get_field_by_id($fieldid);

// Get the entry.
$entry = $DB->get_record('dataform_entries', array('id' => $entryid));

// Try to update.
if ($error = $field->update_state($entry, $newstate)) {
    $result->error = $error;
    echo json_encode($result);
    die();
}

// Update grade if needed.
$df->grade_manager->update_calculated_grades($entry, "##\d*:$field->name##");

$entry->state = $newstate;
$entry->baseurl = '';

// Result.
$result->success = true;
$result->statescontent = $field->renderer->get_browse_content($entry);
$result->entryid = $entryid;

echo json_encode($result);
