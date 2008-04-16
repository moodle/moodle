<?php  // $Id$
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2005 Martin Dougiamas  http://dougiamas.com             //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

    require_once('../../config.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/blocklib.php');

    $id    = optional_param('id', 0, PARAM_INT);  // course module id
    $d     = optional_param('d', 0, PARAM_INT);   // database id
    $mode  = optional_param('mode', 'singletemplate', PARAM_ALPHA);

    if ($id) {
        if (! $cm = get_coursemodule_from_id('data', $id)) {
            error('Course Module ID was incorrect');
        }
        if (! $course = get_record('course', 'id', $cm->course)) {
            error('Course is misconfigured');
        }
        if (! $data = get_record('data', 'id', $cm->instance)) {
            error('Course module is incorrect');
        }

    } else {
        if (! $data = get_record('data', 'id', $d)) {
            error('Data ID is incorrect');
        }
        if (! $course = get_record('course', 'id', $data->course)) {
            error('Course is misconfigured');
        }
        if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
            error('Course Module ID was incorrect');
        }
    }

    require_login($course->id, false, $cm);

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/data:managetemplates', $context);

    if (!count_records('data_fields','dataid',$data->id)) {      // Brand new database!
        redirect($CFG->wwwroot.'/mod/data/field.php?d='.$data->id);  // Redirect to field entry
    }

    add_to_log($course->id, 'data', 'templates view', "templates.php?id=$cm->id&amp;d=$data->id", $data->id, $cm->id);


/// Print the page header

    $strdata = get_string('modulenameplural','data');

    // For the javascript for inserting template tags: initialise the default textarea to
    // 'edit_template' - it is always present in all different possible views.

    $editorobj = 'editor_'.md5('template');

    $bodytag = 'onload="';
    $bodytag .= 'if (typeof('.$editorobj.') != \'undefined\') { currEditor = '.$editorobj.'; } ';
    $bodytag .= 'currTextarea = document.getElementById(\'tempform\').template;';
    $bodytag .= '" ';

    // Javascript to insert the field tags into the textarea.
    $meta = '<script type="text/javascript">'."\n";
    $meta .= '//<![CDATA['."\n";
    $meta .= 'function insert_field_tags(selectlist) {';
    $meta .= '  if (typeof(currEditor) != \'undefined\' && currEditor._editMode == \'wysiwyg\') {';
        // HTMLArea-specific
    $meta .= '     currEditor.insertHTML(selectlist.options[selectlist.selectedIndex].value); '; 
    $meta .= '  } else {';
        // For inserting when in HTMLArea code view or for normal textareas
    $meta .= '     insertAtCursor(currTextarea, selectlist.options[selectlist.selectedIndex].value);';   
    $meta .= '  }'."\n";
    $meta .= '}'."\n";
    $meta .= '//]]>'."\n";
    $meta .= '</script>'."\n";
    
    $navigation = build_navigation('', $cm);
    print_header_simple($data->name, '', $navigation,
                        '', $meta, true, update_module_button($cm->id, $course->id, get_string('modulename', 'data')),
                        navmenu($course, $cm), '', $bodytag);

    print_heading(format_string($data->name));


/// Groups needed for Add entry tab
    $currentgroup = groups_get_activity_group($cm);
    $groupmode = groups_get_activity_groupmode($cm);

/// Print the tabs.
    $currenttab = 'templates';
    include('tabs.php');

/// Processing submitted data, i.e updating form.
    $resettemplate = false;

/// html editor is by default disabled
    $editor = isset($SESSION->data_use_editor) ? $SESSION->data_use_editor : (can_use_html_editor() ? 1 : 0);

    if (($mytemplate = data_submitted($CFG->wwwroot.'/mod/data/templates.php')) && confirm_sesskey()) {
        $newtemplate->id = $data->id;
        $newtemplate->{$mode} = $mytemplate->template;

        if (!empty($mytemplate->switcheditor)) {
            $editor = $editor ? 0 : 1;
            $SESSION->data_use_editor = $editor;
        } else if (!empty($mytemplate->defaultform)) {
            // Reset the template to default, but don't save yet.
            $resettemplate = true;
            $data->{$mode} = data_generate_default_template($data, $mode, 0, false, false);
            if ($mode == 'listtemplate') {
                $data->listtemplateheader = '';
                $data->listtemplatefooter = '';
            }
        } else {
            if (isset($mytemplate->listtemplateheader)){
                $newtemplate->listtemplateheader = $mytemplate->listtemplateheader;
            }
            if (isset($mytemplate->listtemplatefooter)){
                $newtemplate->listtemplatefooter = $mytemplate->listtemplatefooter;
            }
            if (isset($mytemplate->rsstitletemplate)){
                $newtemplate->rsstitletemplate = $mytemplate->rsstitletemplate;
            }

            // Check for multiple tags, only need to check for add template.
            if ($mode != 'addtemplate' or data_tags_check($data->id, $newtemplate->{$mode})) {
                if (update_record('data', $newtemplate)) {
                    notify(get_string('templatesaved', 'data'), 'notifysuccess');
                }
            }
            add_to_log($course->id, 'data', 'templates saved', "templates.php?id=$cm->id&amp;d=$data->id", $data->id, $cm->id);
        }
    } else {
        echo '<div class="littleintro" style="text-align:center">'.get_string('header'.$mode,'data').'</div>';
    }

/// If everything is empty then generate some defaults
    if (empty($data->addtemplate) and empty($data->singletemplate) and
        empty($data->listtemplate) and empty($data->rsstemplate)) {
        data_generate_default_template($data, 'singletemplate');
        data_generate_default_template($data, 'listtemplate');
        data_generate_default_template($data, 'addtemplate');
        data_generate_default_template($data, 'asearchtemplate');           //Template for advanced searches.
        data_generate_default_template($data, 'rsstemplate');
    }


    echo '<form id="tempform" action="templates.php?d='.$data->id.'&amp;mode='.$mode.'" method="post">';
    echo '<div>';
    echo '<input name="sesskey" value="'.sesskey().'" type="hidden" />';
    // Print button to autogen all forms, if all templates are empty

    if (!$resettemplate) {
        // Only reload if we are not resetting the template to default.
        $data = get_record('data', 'id', $d);
    }
    print_simple_box_start('center','80%');
    echo '<table cellpadding="4" cellspacing="0" border="0">';

/// Add the HTML editor(s).
    $usehtmleditor = $editor && can_use_html_editor() && ($mode != 'csstemplate') && ($mode != 'jstemplate');
    if ($mode == 'listtemplate'){
        // Print the list template header.
        echo '<tr>';
        echo '<td>&nbsp;</td>';
        echo '<td>';
        echo '<div style="text-align:center"><label for="edit-listtemplateheader">'.get_string('header','data').'</label></div>';
        print_textarea($usehtmleditor, 10, 72, 0, 0, 'listtemplateheader', $data->listtemplateheader);
        echo '</td>';
        echo '</tr>';
    }

    // Print the main template.

    echo '<tr><td valign="top">';
    if ($mode != 'csstemplate' and $mode != 'jstemplate') {
        // Add all the available fields for this data.
        echo '<label for="availabletags">'.get_string('availabletags','data').'</label>';
        helpbutton('tags', get_string('tags'), 'data');
        echo '<br />';


        echo '<select name="fields1[]" id="availabletags" size="12" onclick="insert_field_tags(this)">';

        $fields = get_records('data_fields', 'dataid', $data->id);
        echo '<optgroup label="'.get_string('fields', 'data').'">';
        foreach ($fields as $field) {
            echo '<option value="[['.$field->name.']]" title="'.$field->description.'">'.$field->name.' - [['.$field->name.']]</option>';
        }
        echo '</optgroup>';

        if ($mode == 'addtemplate') {
            echo '<optgroup label="'.get_string('fieldids', 'data').'">';
            foreach ($fields as $field) {
                if (in_array($field->type, array('picture', 'checkbox', 'date', 'latlong', 'radiobutton'))) {
                    continue; //ids are not usable for these composed items
                }
                echo '<option value="[['.$field->name.'#id]]" title="'.$field->description.' id">'.$field->name.' id - [['.$field->name.'#id]]</option>';
            }
            echo '</optgroup>';
        }

        // Print special tags. fix for MDL-7031
        if ($mode != 'addtemplate' && $mode != 'asearchtemplate') {             //Don't print special tags when viewing the advanced search template and add template.
            echo '<optgroup label="'.get_string('buttons', 'data').'">';
            echo '<option value="##edit##">' .get_string('edit', 'data'). ' - ##edit##</option>';
            echo '<option value="##delete##">' .get_string('delete', 'data'). ' - ##delete##</option>';
            echo '<option value="##approve##">' .get_string('approve', 'data'). ' - ##approve##</option>';
            if ($mode != 'singletemplate') {
                // more points to single template - not useable there
                echo '<option value="##more##">' .get_string('more', 'data'). ' - ##more##</option>';
                echo '<option value="##moreurl##">' .get_string('moreurl', 'data'). ' - ##moreurl##</option>';
            }
            echo '</optgroup>';
            echo '<optgroup label="'.get_string('other', 'data').'">';
            echo '<option value="##timeadded##">'.get_string('timeadded', 'data'). ' - ##timeadded##</option>';
            echo '<option value="##timemodified##">'.get_string('timemodified', 'data'). ' - ##timemodified##</option>';
            echo '<option value="##user##">' .get_string('user'). ' - ##user##</option>';
            if ($mode != 'singletemplate') {
                // more points to single template - not useable there
                echo '<option value="##comments##">' .get_string('comments', 'data'). ' - ##comments##</option>';
            }
            echo '</optgroup>';
        }

        if ($mode == 'asearchtemplate') {
            echo '<optgroup label="'.get_string('other', 'data').'">';
            echo '<option value="##firstname##">' .get_string('authorfirstname', 'data'). ' - ##firstname##</option>';
            echo '<option value="##lastname##">' .get_string('authorlastname', 'data'). ' - ##lastname##</option>';
            echo '</optgroup>';
        }

        echo '</select>';
        echo '<br /><br /><br /><br /><input type="submit" name="defaultform" value="'.get_string('resettemplate','data').'" />';
        if (can_use_html_editor()) {
            echo '<br /><br />';
            if ($editor) {
                $switcheditor = get_string('editordisable', 'data');
            } else {
                $switcheditor = get_string('editorenable', 'data');
            }
            echo '<input type="submit" name="switcheditor" value="'.s($switcheditor).'" />';
        }
    } else {
        echo '<br /><br /><br /><br /><input type="submit" name="defaultform" value="'.get_string('resettemplate','data').'" />';
    }
    echo '</td>';

    echo '<td>';
    if ($mode == 'listtemplate'){
        echo '<div style="text-align:center"><label for="edit-template">'.get_string('multientry','data').'</label></div>';
    } else {
        echo '<div style="text-align:center"><label for="edit-template">'.get_string($mode,'data').'</label></div>';
    }

    print_textarea($usehtmleditor, 20, 72, 0, 0, 'template', $data->{$mode});
    echo '</td>';
    echo '</tr>';

    if ($mode == 'listtemplate'){
        echo '<tr>';
        echo '<td>&nbsp;</td>';
        echo '<td>';
        echo '<div style="text-align:center"><label for="edit-listtemplatefooter">'.get_string('footer','data').'</label></div>';
        print_textarea($usehtmleditor, 10, 72, 0, 0, 'listtemplatefooter', $data->listtemplatefooter);
        echo '</td>';
        echo '</tr>';
    } else if ($mode == 'rsstemplate') {
        echo '<tr>';
        echo '<td>&nbsp;</td>';
        echo '<td>';
        echo '<div style="text-align:center"><label for="edit-rsstitletemplate">'.get_string('rsstitletemplate','data').'</label></div>';
        print_textarea($usehtmleditor, 10, 72, 0, 0, 'rsstitletemplate', $data->rsstitletemplate);
        echo '</td>';
        echo '</tr>';
    }

    echo '<tr><td style="text-align:center" colspan="2">';
    echo '<input type="submit" value="'.get_string('savetemplate','data').'" />&nbsp;';

    echo '</td></tr></table>';


    print_simple_box_end();
    echo '</div>';
    echo '</form>';
    if ($usehtmleditor) {
        use_html_editor('template');
        if ($mode == 'listtemplate'){
            use_html_editor('listtemplateheader');
            use_html_editor('listtemplatefooter');
        } else if ($mode == 'rsstemplate'){
            use_html_editor('rsstitletemplate');
        }
    }

/// Finish the page
    print_footer($course);
?>
