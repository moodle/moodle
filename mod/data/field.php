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

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->dirroot.'/mod/data/preset_form.php');

$id             = optional_param('id', 0, PARAM_INT);            // course module id
$d              = optional_param('d', 0, PARAM_INT);             // database id
$fid            = optional_param('fid', 0 , PARAM_INT);          // update field id
$newtype        = optional_param('newtype','',PARAM_ALPHA);      // type of the new field
$mode           = optional_param('mode','',PARAM_ALPHA);
$action         = optional_param('action', '', PARAM_ALPHA);
$fullname       = optional_param('fullname', '', PARAM_PATH);    // Directory the preset is in.
$defaultsort    = optional_param('defaultsort', 0, PARAM_INT);
$defaultsortdir = optional_param('defaultsortdir', 0, PARAM_INT);
$cancel         = optional_param('cancel', 0, PARAM_BOOL);

if ($cancel) {
    $mode = 'list';
}

$url = new moodle_url('/mod/data/field.php');
if ($fid !== 0) {
    $url->param('fid', $fid);
}
if ($newtype !== '') {
    $url->param('newtype', $newtype);
}
if ($mode !== '') {
    $url->param('mode', $mode);
}
if ($defaultsort !== 0) {
    $url->param('defaultsort', $defaultsort);
}
if ($defaultsortdir !== 0) {
    $url->param('defaultsortdir', $defaultsortdir);
}
if ($cancel !== 0) {
    $url->param('cancel', $cancel);
}
if ($action !== '') {
    $url->param('action', $action);
}

if ($id) {
    $url->param('id', $id);
    $PAGE->set_url($url);
    if (! $cm = get_coursemodule_from_id('data', $id)) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record('course', array('id'=>$cm->course))) {
        print_error('coursemisconf');
    }
    if (! $data = $DB->get_record('data', array('id'=>$cm->instance))) {
        print_error('invalidcoursemodule');
    }

} else {
    $url->param('d', $d);
    $PAGE->set_url($url);
    if (! $data = $DB->get_record('data', array('id'=>$d))) {
        print_error('invalidid', 'data');
    }
    if (! $course = $DB->get_record('course', array('id'=>$data->course))) {
        print_error('invalidcoursemodule');
    }
    if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/data:managetemplates', $context);

$formimportzip = new data_import_preset_zip_form();
$formimportzip->set_data(array('d' => $data->id));

$actionbar = new \mod_data\output\action_bar($data->id, $PAGE->url);

$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($course->fullname);
$PAGE->activityheader->disable();

// Fill in missing properties needed for updating of instance.
$data->course     = $cm->course;
$data->cmidnumber = $cm->idnumber;
$data->instance   = $cm->instance;

/************************************
 *        Data Processing           *
 ***********************************/
$renderer = $PAGE->get_renderer('mod_data');

if ($formimportzip->is_cancelled()) {
    redirect(new moodle_url('/mod/data/field.php', ['d' => $data->id]));
} else if ($formdata = $formimportzip->get_data()) {
    $fieldactionbar = $actionbar->get_fields_action_bar();
    data_print_header($course, $cm, $data, false, $fieldactionbar);
    echo $OUTPUT->heading(get_string('importpreset', 'data'), 2, 'mb-4');
    $file = new stdClass;
    $file->name = $formimportzip->get_new_filename('importfile');
    $file->path = $formimportzip->save_temp_file('importfile');
    $importer = new data_preset_upload_importer($course, $cm, $data, $file->path);
    echo $renderer->import_setting_mappings($data, $importer);
    echo $OUTPUT->footer();
    exit(0);
}

if ($action == 'finishimport' && confirm_sesskey()) {
    data_print_header($course, $cm, $data, false);
    $overwritesettings = optional_param('overwritesettings', false, PARAM_BOOL);

    if (!$fullname) {
        $presetdir = $CFG->tempdir . '/forms/' . required_param('directory', PARAM_FILE);
        if (!file_exists($presetdir) || !is_dir($presetdir)) {
            throw new moodle_exception('cannotimport', 'error');
        }
        $importer = new data_preset_upload_importer($course, $cm, $data, $presetdir);
    } else {
        $importer = new data_preset_existing_importer($course, $cm, $data, $fullname);
    }

    $importer->import($overwritesettings);
    $strimportsuccess = get_string('importsuccess', 'data');
    $straddentries = get_string('addentries', 'data');
    $strtodatabase = get_string('todatabase', 'data');

    if (!$DB->get_records('data_records', array('dataid' => $data->id))) {
        echo $OUTPUT->notification("$strimportsuccess <a href='edit.php?d=$data->id'>$straddentries</a> $strtodatabase",
            'notifysuccess');
    } else {
        echo $OUTPUT->notification("$strimportsuccess", 'notifysuccess');
    }

    echo $OUTPUT->continue_button(new moodle_url('/mod/data/field.php', ['d' => $data->id]));
    echo $OUTPUT->footer();
    exit;
}

switch ($mode) {

    case 'add':    ///add a new field
        if (confirm_sesskey() and $fieldinput = data_submitted()){

            //$fieldinput->name = data_clean_field_name($fieldinput->name);

        /// Only store this new field if it doesn't already exist.
            if (($fieldinput->name == '') or data_fieldname_exists($fieldinput->name, $data->id)) {

                $displaynoticebad = get_string('invalidfieldname','data');

            } else {

            /// Check for arrays and convert to a comma-delimited string
                data_convert_arrays_to_strings($fieldinput);

            /// Create a field object to collect and store the data safely
                $type = required_param('type', PARAM_FILE);
                $field = data_get_field_new($type, $data);

                $field->define_field($fieldinput);
                $field->insert_field();

            /// Update some templates
                data_append_new_field_to_templates($data, $fieldinput->name);

                $displaynoticegood = get_string('fieldadded','data');
            }
        }
        break;


    case 'update':    ///update a field
        if (confirm_sesskey() and $fieldinput = data_submitted()){

            //$fieldinput->name = data_clean_field_name($fieldinput->name);

            if (($fieldinput->name == '') or data_fieldname_exists($fieldinput->name, $data->id, $fieldinput->fid)) {

                $displaynoticebad = get_string('invalidfieldname','data');

            } else {
            /// Check for arrays and convert to a comma-delimited string
                data_convert_arrays_to_strings($fieldinput);

            /// Create a field object to collect and store the data safely
                $field = data_get_field_from_id($fid, $data);
                $oldfieldname = $field->field->name;

                $field->field->name = $fieldinput->name;
                $field->field->description = $fieldinput->description;
                $field->field->required = !empty($fieldinput->required) ? 1 : 0;

                for ($i=1; $i<=10; $i++) {
                    if (isset($fieldinput->{'param'.$i})) {
                        $field->field->{'param'.$i} = $fieldinput->{'param'.$i};
                    } else {
                        $field->field->{'param'.$i} = '';
                    }
                }

                $field->update_field();

            /// Update the templates.
                data_replace_field_in_templates($data, $oldfieldname, $field->field->name);

                $displaynoticegood = get_string('fieldupdated','data');
            }
        }
        break;


    case 'delete':    // Delete a field
        if (confirm_sesskey()){

            if ($confirm = optional_param('confirm', 0, PARAM_INT)) {


                // Delete the field completely
                if ($field = data_get_field_from_id($fid, $data)) {
                    $field->delete_field();

                    // Update the templates.
                    data_replace_field_in_templates($data, $field->field->name, '');

                    // Update the default sort field
                    if ($fid == $data->defaultsort) {
                        $rec = new stdClass();
                        $rec->id = $data->id;
                        $rec->defaultsort = 0;
                        $rec->defaultsortdir = 0;
                        $DB->update_record('data', $rec);
                    }

                    $displaynoticegood = get_string('fielddeleted', 'data');
                }

            } else {

                data_print_header($course,$cm,$data, false);
                echo $OUTPUT->heading(get_string('deletefield', 'data'), 2, 'mb-4');

                // Print confirmation message.
                $field = data_get_field_from_id($fid, $data);

                echo $OUTPUT->confirm('<strong>'.$field->name().': '.$field->field->name.'</strong><br /><br />'. get_string('confirmdeletefield','data'),
                             'field.php?d='.$data->id.'&mode=delete&fid='.$fid.'&confirm=1',
                             'field.php?d='.$data->id);

                echo $OUTPUT->footer();
                exit;
            }
        }
        break;


    case 'sort':    // Set the default sort parameters
        if (confirm_sesskey()) {
            $rec = new stdClass();
            $rec->id = $data->id;
            $rec->defaultsort = $defaultsort;
            $rec->defaultsortdir = $defaultsortdir;

            $DB->update_record('data', $rec);
            redirect($CFG->wwwroot.'/mod/data/field.php?d='.$data->id, get_string('changessaved'), 2);
            exit;
        }
        break;

    case 'import':
        $PAGE->navbar->add(get_string('importpreset', 'data'));
        $fieldactionbar = $actionbar->get_fields_action_bar();
        data_print_header($course, $cm, $data, false, $fieldactionbar);

        echo $OUTPUT->heading(get_string('importpreset', 'data'), 2, 'mb-4');
        echo $formimportzip->display();
        echo $OUTPUT->footer();
        exit;

    case 'usepreset':
        $PAGE->navbar->add(get_string('usestandard', 'data'));
        $fieldactionbar = $actionbar->get_fields_action_bar();
        data_print_header($course, $cm, $data, false, $fieldactionbar);

        if ($action === 'select') {
            if (!empty($fullname)) {
                echo $OUTPUT->heading(get_string('usestandard', 'data'), 2, 'mb-4');
                $importer = new data_preset_existing_importer($course, $cm, $data, $fullname);
                echo $renderer->import_setting_mappings($data, $importer);
            }
        } else {
            echo $OUTPUT->heading(get_string('presets', 'data'), 2, 'mb-4');
            $presets = data_get_available_presets($context);
            $presetstable = new \mod_data\output\presets($data->id, $presets,
                new \moodle_url('/mod/data/field.php'));
            echo $renderer->render_presets($presetstable, false);
        }
        echo $OUTPUT->footer();
        exit;

    default:
        break;
}



/// Print the browsing interface

///get the list of possible fields (plugins)
$plugins = core_component::get_plugin_list('datafield');
$menufield = array();

foreach ($plugins as $plugin=>$fulldir){
    $menufield[$plugin] = get_string('pluginname', 'datafield_'.$plugin);    //get from language files
}
asort($menufield);    //sort in alphabetical order
$PAGE->force_settings_menu(true);

$PAGE->set_pagetype('mod-data-field-' . $newtype);
if (($mode == 'new') && (!empty($newtype))) { // Adding a new field.
    data_print_header($course, $cm, $data,'fields');
    echo $OUTPUT->heading(get_string('newfield', 'data'));

    $field = data_get_field_new($newtype, $data);
    $field->display_edit_field();

} else if ($mode == 'display' && confirm_sesskey()) { /// Display/edit existing field
    data_print_header($course, $cm, $data,'fields');
    echo $OUTPUT->heading(get_string('editfield', 'data'));

    $field = data_get_field_from_id($fid, $data);
    $field->display_edit_field();

} else {                                              /// Display the main listing of all fields
    $fieldactionbar = $actionbar->get_fields_action_bar(true, true, true);
    data_print_header($course, $cm, $data, 'fields', $fieldactionbar);
    echo $OUTPUT->heading(get_string('managefields', 'data'), 2, 'mb-4');

    if (!$DB->record_exists('data_fields', array('dataid'=>$data->id))) {
        echo $OUTPUT->notification(get_string('nofieldindatabase','data'));  // nothing in database
        echo $OUTPUT->notification(get_string('pleaseaddsome','data', 'preset.php?id='.$cm->id));      // link to presets

    } else {    //else print quiz style list of fields

        $table = new html_table();
        $table->head = array(
            get_string('fieldname', 'data'),
            get_string('type', 'data'),
            get_string('required', 'data'),
            get_string('fielddescription', 'data'),
            get_string('action', 'data'),
        );
        $table->align = array('left', 'left', 'left', 'left');
        $table->wrap = array(false,false,false,false);

        if ($fff = $DB->get_records('data_fields', array('dataid'=>$data->id),'id')){
            foreach ($fff as $ff) {

                $field = data_get_field($ff, $data);

                $baseurl = new moodle_url('/mod/data/field.php', array(
                    'd'         => $data->id,
                    'fid'       => $field->field->id,
                    'sesskey'   => sesskey(),
                ));

                $displayurl = new moodle_url($baseurl, array(
                    'mode'      => 'display',
                ));

                $deleteurl = new moodle_url($baseurl, array(
                    'mode'      => 'delete',
                ));

                $table->data[] = array(
                    html_writer::link($displayurl, $field->field->name),
                    $field->image() . '&nbsp;' . $field->name(),
                    $field->field->required ? get_string('yes') : get_string('no'),
                    shorten_text($field->field->description, 30),
                    html_writer::link($displayurl, $OUTPUT->pix_icon('t/edit', get_string('edit'))) .
                        '&nbsp;' .
                        html_writer::link($deleteurl, $OUTPUT->pix_icon('t/delete', get_string('delete'))),
                );
            }
        }
        echo html_writer::table($table);
    }

    echo '<div class="sortdefault">';
    echo '<form id="sortdefault" action="'.$CFG->wwwroot.'/mod/data/field.php" method="get">';
    echo '<div>';
    echo '<input type="hidden" name="d" value="'.$data->id.'" />';
    echo '<input type="hidden" name="mode" value="sort" />';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo '<label for="defaultsort">'.get_string('defaultsortfield','data').'</label>';
    echo '<select id="defaultsort" name="defaultsort" class="custom-select">';
    if ($fields = $DB->get_records('data_fields', array('dataid'=>$data->id))) {
        echo '<optgroup label="'.get_string('fields', 'data').'">';
        foreach ($fields as $field) {
            if ($data->defaultsort == $field->id) {
                echo '<option value="'.$field->id.'" selected="selected">'.$field->name.'</option>';
            } else {
                echo '<option value="'.$field->id.'">'.$field->name.'</option>';
            }
        }
        echo '</optgroup>';
    }
    $options = array();
    $options[DATA_TIMEADDED]    = get_string('timeadded', 'data');
// TODO: we will need to change defaultsort db to unsinged to make these work in 2.0
/*        $options[DATA_TIMEMODIFIED] = get_string('timemodified', 'data');
    $options[DATA_FIRSTNAME]    = get_string('authorfirstname', 'data');
    $options[DATA_LASTNAME]     = get_string('authorlastname', 'data');
    if ($data->approval and has_capability('mod/data:approve', $context)) {
        $options[DATA_APPROVED] = get_string('approved', 'data');
    }*/
    echo '<optgroup label="'.get_string('other', 'data').'">';
    foreach ($options as $key => $name) {
        if ($data->defaultsort == $key) {
            echo '<option value="'.$key.'" selected="selected">'.$name.'</option>';
        } else {
            echo '<option value="'.$key.'">'.$name.'</option>';
        }
    }
    echo '</optgroup>';
    echo '</select>';

    $options = array(0 => get_string('ascending', 'data'),
                     1 => get_string('descending', 'data'));
    echo html_writer::label(get_string('sortby'), 'menudefaultsortdir', false, array('class' => 'accesshide'));
    echo html_writer::select($options, 'defaultsortdir', $data->defaultsortdir, false, array('class' => 'custom-select'));
    echo '<input type="submit" class="btn btn-secondary ml-1" value="'.get_string('save', 'data').'" />';
    echo '</div>';
    echo '</form>';
    echo '</div>';

}

/// Finish the page
echo $OUTPUT->footer();

