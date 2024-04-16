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

use core\notification;
use mod_data\local\importer\preset_existing_importer;
use mod_data\local\importer\preset_importer;
use mod_data\local\importer\preset_upload_importer;
use mod_data\manager;

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
    list($course, $cm) = get_course_and_cm_from_cmid($id, manager::MODULE);
    $manager = manager::create_from_coursemodule($cm);
    $url->param('id', $cm->id);
} else {   // We must have $d.
    $instance = $DB->get_record('data', ['id' => $d], '*', MUST_EXIST);
    $manager = manager::create_from_instance($instance);
    $cm = $manager->get_coursemodule();
    $course = get_course($cm->course);
    $url->param('d', $d);
}

$PAGE->set_url($url);
$data = $manager->get_instance();
$context = $manager->get_context();

require_login($course, true, $cm);
require_capability('mod/data:managetemplates', $context);

$actionbar = new \mod_data\output\action_bar($data->id, $PAGE->url);

$PAGE->add_body_class('mediumwidth');
$PAGE->set_heading($course->fullname);
$PAGE->activityheader->disable();

// Fill in missing properties needed for updating of instance.
$data->course     = $cm->course;
$data->cmidnumber = $cm->idnumber;
$data->instance   = $cm->instance;

/************************************
 *        Data Processing           *
 ***********************************/
$renderer = $manager->get_renderer();

if ($action == 'finishimport' && confirm_sesskey()) {
    $overwritesettings = optional_param('overwritesettings', false, PARAM_BOOL);
    $importer = preset_importer::create_from_parameters($manager);
    $importer->finish_import_process($overwritesettings, $data);
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

                if (!empty($validationerrors = $field->validate($fieldinput))) {
                    $displaynoticebad = html_writer::alist($validationerrors);
                    $mode = 'new';
                    $newtype = $type;
                    break;
                }

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
                if (!empty($validationerrors = $field->validate($fieldinput))) {
                    $displaynoticebad = html_writer::alist($validationerrors);
                    $mode = 'display';
                    break;
                }
                $oldfieldname = $field->field->name;

                $field->field->name = trim($fieldinput->name);
                $field->field->description = trim($fieldinput->description);
                $field->field->required = !empty($fieldinput->required) ? 1 : 0;

                for ($i=1; $i<=10; $i++) {
                    if (isset($fieldinput->{'param'.$i})) {
                        $field->field->{'param'.$i} = trim($fieldinput->{'param'.$i});
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
                $titleparts = [
                    get_string('deletefield', 'data'),
                    format_string($data->name),
                    format_string($course->fullname),
                ];
                $PAGE->set_title(implode(moodle_page::TITLE_SEPARATOR, $titleparts));
                data_print_header($course,$cm,$data, false);
                echo $OUTPUT->heading(get_string('deletefield', 'data'), 2, 'mb-4');

                // Print confirmation message.
                $field = data_get_field_from_id($fid, $data);

                if ($field->type === 'unknown') {
                    $fieldtypename = get_string('unknown', 'data');
                } else {
                    $fieldtypename = $field->name();
                }
                echo $OUTPUT->confirm('<strong>' . $fieldtypename . ': ' . s($field->field->name) . '</strong><br /><br />' .
                        get_string('confirmdeletefield', 'data'),
                        'field.php?d=' . $data->id . '&mode=delete&fid=' . $fid . '&confirm=1',
                        'field.php?d=' . $data->id,
                        ['type' => single_button::BUTTON_DANGER]);

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

    case 'usepreset':
        $importer = preset_importer::create_from_parameters($manager);
        if (!$importer->needs_mapping() || $action == 'notmapping') {
            $backurl = new moodle_url('/mod/data/field.php', ['id' => $cm->id]);
            if ($importer->import(false)) {
                notification::success(get_string('importsuccess', 'mod_data'));
            } else {
                notification::error(get_string('cannotapplypreset', 'mod_data'));
            }
            redirect($backurl);
        }
        $PAGE->navbar->add(get_string('usestandard', 'data'));
        $fieldactionbar = $actionbar->get_fields_mapping_action_bar();
        data_print_header($course, $cm, $data, false, $fieldactionbar);
        $importer = new preset_existing_importer($manager, $fullname);
        echo $renderer->importing_preset($data, $importer);
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
    if (!is_dir($fulldir)) {
        continue;
    }
    $menufield[$plugin] = get_string('pluginname', 'datafield_'.$plugin);    //get from language files
}
asort($menufield);    //sort in alphabetical order
$PAGE->force_settings_menu(true);

$PAGE->set_pagetype('mod-data-field-' . $newtype);
$titleparts = [
    format_string($data->name),
    format_string($course->fullname),
];
if (($mode == 'new') && (!empty($newtype))) { // Adding a new field.
    array_unshift($titleparts, get_string('newfield', 'data'));
    $PAGE->set_title(implode(moodle_page::TITLE_SEPARATOR, $titleparts));
    data_print_header($course, $cm, $data, 'fields');
    echo $OUTPUT->heading(get_string('newfield', 'data'));

    $field = data_get_field_new($newtype, $data);
    $field->display_edit_field();

} else if ($mode == 'display' && confirm_sesskey()) { /// Display/edit existing field
    array_unshift($titleparts, get_string('editfield', 'data'));
    $PAGE->set_title(implode(moodle_page::TITLE_SEPARATOR, $titleparts));
    data_print_header($course, $cm, $data, 'fields');
    echo $OUTPUT->heading(get_string('editfield', 'data'));

    $field = data_get_field_from_id($fid, $data);
    $field->display_edit_field();

} else {                                              /// Display the main listing of all fields
    array_unshift($titleparts, get_string('managefields', 'data'));
    $PAGE->set_title(implode(moodle_page::TITLE_SEPARATOR, $titleparts));
    $hasfields = $manager->has_fields();
    // Check if it is an empty database with no fields.
    if (!$hasfields) {
        echo $OUTPUT->header();
        echo $renderer->render_fields_zero_state($manager);
        echo $OUTPUT->footer();
        // Don't check the rest of the options. There is no field, there is nothing else to work with.
        exit;
    }
    $fieldactionbar = $actionbar->get_fields_action_bar(true);
    data_print_header($course, $cm, $data, 'fields', $fieldactionbar);

    echo $OUTPUT->box_start();
    echo get_string('fieldshelp', 'data');
    echo $OUTPUT->box_end();
    echo $OUTPUT->box_start('d-flex flex-row-reverse');
    echo $OUTPUT->render($actionbar->get_create_fields(true));
    echo $OUTPUT->box_end();
    $table = new html_table();
    $table->head = [
        get_string('fieldname', 'data'),
        get_string('type', 'data'),
        get_string('required', 'data'),
        get_string('fielddescription', 'data'),
        '&nbsp;',
    ];
    $table->align = ['left', 'left', 'left', 'left'];
    $table->wrap = [false,false,false,false];
    $table->responsive = false;

    $fieldrecords = $manager->get_field_records();
    $missingfieldtypes = [];
    foreach ($fieldrecords as $fieldrecord) {

        $field = data_get_field($fieldrecord, $data);

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

        $actionmenu = new action_menu();
        $actionmenu->set_kebab_trigger();
        $actionmenu->set_action_label(get_string('actions'));
        $actionmenu->set_additional_classes('fields-actions');

        // It display a notification when the field type does not exist.
        if ($field->type === 'unknown') {
            $missingfieldtypes[] = $field->field->name;
            $fieltypedata = $field->field->type;
        } else {
            $fieltypedata = $field->image() . '&nbsp;' . $field->name();
            // Edit icon, only displayed when the field type is known.
            $actionmenu->add(new action_menu_link_secondary(
                $displayurl,
                null,
                get_string('edit'),
            ));
        }

        // Delete.
        $actionmenu->add(new action_menu_link_secondary(
            $deleteurl,
            null,
            get_string('delete'),
        ));
        $actionmenutemplate = $actionmenu->export_for_template($OUTPUT);

        $table->data[] = [
            s($field->field->name),
            $fieltypedata,
            $field->field->required ? get_string('yes') : get_string('no'),
            shorten_text($field->field->description, 30),
            $OUTPUT->render_from_template('core/action_menu', $actionmenutemplate)
        ];

    }
    if (!empty($missingfieldtypes)) {
        echo $OUTPUT->notification(get_string('missingfieldtypes', 'data') . html_writer::alist($missingfieldtypes));
    }
    echo html_writer::table($table);

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
                echo '<option value="'.$field->id.'" selected="selected">'.s($field->name).'</option>';
            } else {
                echo '<option value="'.$field->id.'">'.s($field->name).'</option>';
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

    // Add a sticky footer.
    echo $renderer->render_fields_footer($manager);

    echo '</div>';
}

/// Finish the page
echo $OUTPUT->footer();
