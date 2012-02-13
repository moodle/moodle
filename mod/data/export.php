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
 * @package mod-data
 */

require_once('../../config.php');
require_once('lib.php');
require_once('export_form.php');

// database ID
$d = required_param('d', PARAM_INT);

$PAGE->set_url('/mod/data/export.php', array('d'=>$d));

if (! $data = $DB->get_record('data', array('id'=>$d))) {
    print_error('wrongdataid', 'data');
}

if (! $cm = get_coursemodule_from_instance('data', $data->id, $data->course)) {
    print_error('invalidcoursemodule');
}

if(! $course = $DB->get_record('course', array('id'=>$cm->course))) {
    print_error('invalidcourseid', '', '', $cm->course);
}

// fill in missing properties needed for updating of instance
$data->course     = $cm->course;
$data->cmidnumber = $cm->idnumber;
$data->instance   = $cm->instance;

if (! $context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
    print_error('invalidcontext', '');
}

require_login($course->id, false, $cm);
require_capability(DATA_CAP_EXPORT, $context);

// get fields for this database
$fieldrecords = $DB->get_records('data_fields', array('dataid'=>$data->id), 'id');

if(empty($fieldrecords)) {
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (has_capability('mod/data:managetemplates', $context)) {
        redirect($CFG->wwwroot.'/mod/data/field.php?d='.$data->id);
    } else {
        print_error('nofieldindatabase', 'data');
    }
}

// populate objets for this databases fields
$fields = array();
foreach ($fieldrecords as $fieldrecord) {
    $fields[]= data_get_field($fieldrecord, $data);
}


$mform = new mod_data_export_form('export.php?d='.$data->id, $fields, $cm);

if($mform->is_cancelled()) {
    redirect('view.php?d='.$data->id);
} elseif (!$formdata = (array) $mform->get_data()) {
    // build header to match the rest of the UI
    $PAGE->set_title($data->name);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    $url = new moodle_url('/mod/data/export.php', array('d' => $d));
    groups_print_activity_menu($cm, $url);
    echo $OUTPUT->heading(format_string($data->name));

    // these are for the tab display
    $currentgroup = groups_get_activity_group($cm);
    $groupmode = groups_get_activity_groupmode($cm);
    $currenttab = 'export';
    include('tabs.php');
    $mform->display();
    echo $OUTPUT->footer();
    die;
}

$selectedfields = array();
foreach ($formdata as $key => $value) {
    //field form elements are field_1 field_2 etc. 0 if not selected. 1 if selected.
    if (strpos($key, 'field_')===0 && !empty($value)) {
        $selectedfields[] = substr($key, 6);
    }
}

$currentgroup = groups_get_activity_group($cm);

$exportdata = data_get_exportdata($data->id, $fields, $selectedfields, $currentgroup);
$count = count($exportdata);
switch ($formdata['exporttype']) {
    case 'csv':
        data_export_csv($exportdata, $formdata['delimiter_name'], $data->name, $count);
        break;
    case 'xls':
        data_export_xls($exportdata, $data->name, $count);
        break;
    case 'ods':
        data_export_ods($exportdata, $data->name, $count);
        break;
}

die();
