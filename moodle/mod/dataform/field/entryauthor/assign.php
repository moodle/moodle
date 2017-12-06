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
 * This page receives entry author asign me requests.
 *
 * @package dataformfield_entryauthor
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../../config.php');

$d = required_param('d', PARAM_INT);
$viewid = required_param('vid', PARAM_INT);
$entryid = required_param('eid', PARAM_INT);
$action = required_param('action', PARAM_ALPHA);
$ret = required_param('ret', PARAM_RAW);

// Instantiate the Dataform.
$df = mod_dataform_dataform::instance($d);
require_login($df->course->id, false, $df->cm);

// Sesskey.
require_sesskey();

$PAGE->set_context($df->context);
$PAGE->set_url('/mod/dataform/field/entryauthor/assign.php');

$fieldid = dataformfield_entryauthor_entryauthor::INTERNALID;
$field = $df->field_manager->get_field_by_id($fieldid);

// Assign.
if ($action == 'assign') {
    $field->assign_user($USER->id, $entryid, $viewid);
}

// Unassign.
if ($action == 'unassign') {
    $field->assign_user(0, $entryid, $viewid);
}

redirect(urldecode($ret));
