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

    require_login();
    
    $id    = optional_param('id', 0, PARAM_INT);    // course module id
    $d     = optional_param('d', 0, PARAM_INT);    // database id
    $fid   = optional_param('fid', 0 , PARAM_INT);    //update field id
    $newtype = optional_param('fieldmenu','',PARAM_ALPHA);    //type of the new field

    //action specifies what action is performed when data is submitted
    $mode = optional_param('mode','',PARAM_ALPHA);
    $displaynotice = '';    //str to print after an operation,
    
    if ($id) {
        if (! $cm = get_record('course_modules', 'id', $id)) {
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

    if (!isteacheredit($course->id)){
        error(get_string('noaccess','data'));
    }

    $strdata = get_string('modulenameplural','data');

    print_header_simple($data->name, '', "<a href='index.php?id=$course->id'>$strdata</a> -> $data->name", 
                                     '', '', true, '', navmenu($course, $cm));

    print_heading(format_string($data->name));
    
    
    /************************************
     *        Data Processing           *
     ***********************************/
    switch ($mode){

        case 'add':    ///add a new field
            if (confirm_sesskey() and $fieldinput = data_submitted($CFG->wwwroot.'/mod/data/fields.php')){

            /// Only store this new field if it doesn't already exist.
                if (data_fieldname_exists($fieldinput->name, $data->id)) {

                    $displaynotice = get_string('invalidfieldname','data');

                } else {   
                    
                /// Check for arrays and convert to a comma-delimited string
                    data_convert_arrays_to_strings($fieldinput);

                /// Create a field object to collect and store the data safely
                    $type = required_param('type', PARAM_FILE);
                    $field = data_get_field_new($type, $data);

                    $field->define_field($fieldinput);
                    $field->insert_field();

                /// Update some templates
                    data_append_new_field_to_templates($data, $field->field->name);

                    add_to_log($course->id, 'data', 'fields add', 
                               "fields.php?d=$data->id&amp;mode=display&amp;fid=$fid", $fid, $cm->id);
                    
                    $displaynotice = get_string('fieldadded','data');
                }
            }
            break;


        case 'update':    ///update a field
            if (confirm_sesskey() and $fieldinput = data_submitted($CFG->wwwroot.'/mod/data/fields.php')){

                $fieldinput->name = optional_param('name','',PARAM_NOTAGS);

                if (data_fieldname_exists($fieldinput->name, $data->id, $fid)) {
                    $displaynotice = get_string('invalidfieldname','data');

                } else {
                /// Check for arrays and convert to a comma-delimited string
                    data_convert_arrays_to_strings($fieldinput);

                /// Create a field object to collect and store the data safely
                    $field = data_get_field_from_id($fid, $data);
                    $oldfieldname = $field->field->name;
                    $field->update_field($fieldinput);
                    
                /// Update the templates.
                    data_replace_field_in_templates($data, $oldfieldname, $field->field->name);
                    
                    add_to_log($course->id, 'data', 'fields update', 
                               "fields.php?d=$data->id&amp;mode=display&amp;fid=$fid", $fid, $cm->id);
                    
                    $displaynotice = get_string('fieldupdated','data');
                }
            }
            break;


        case 'delete':    // Delete a field
            if (confirm_sesskey()){
                if ($confirm = optional_param('confirm', 0, PARAM_INT)) {

                    // Delete the field completely
                    $field = data_get_field_from_id($fid, $data);
                    $field->delete_field();

                    // Update the templates.
                    data_replace_field_in_templates($data, $field->field->name, '');
                    
                    add_to_log($course->id, 'data', 'fields delete', 
                               "fields.php?d=$data->id", $field->field->name, $cm->id);

                    $displaynotice = get_string('fielddeleted', 'data');

                } else {
                    // Print confirmation message.
                    $field = data_get_field_from_id($fid, $data);

                    print_simple_box_start('center', '60%');
                    echo '<div align="center">';
                    echo '<form action = "fields.php?d='.$data->id.'&amp;mode=delete&amp;fid='.$fid.'" method="post">';
                    echo '<input name="sesskey" value="'.sesskey().'" type="hidden" />';
                    echo '<input name="confirm" value="1" type="hidden" />';
                    echo '<strong>'.$field->field->name.'</strong> - '.get_string('confirmdeletefield','data');
                    echo '<p>';
                    echo '<input type="submit" value="'.get_string('ok').'" /> ';
                    echo '<input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1);" />';
                    echo '<p>';
                    echo '</form>';
                    echo '</div>';
                    print_simple_box_end();
                    echo '</td></tr></table>';
                    print_footer($course);
                    exit;
                }
            }
            break;

        default:
            break;
    }

/// Print the tabs

    $currenttab = 'fields';
    include('tabs.php'); 

/// Print the browsing interface
    
    ///get the list of possible fields (plugins)
    $directories = get_list_of_plugins('mod/data/field/');
    $menufield = array();

    foreach ($directories as $directory){
        $menufield[$directory] = get_string($directory,'data');    //get from language files
    }
    asort($menufield);    //sort in alphabetical order
    
    notify($displaynotice);    //print message, if any

    if (($mode == 'new') && confirm_sesskey()) {          ///  Adding a new field
        $field = data_get_field_new($newtype, $data);
        $field->display_edit_field();

    } else if ($mode == 'display' && confirm_sesskey()) { /// Display/edit existing field
        $field = data_get_field_from_id($fid, $data);
        $field->display_edit_field();

    } else {                                              /// Display the main listing of all fields

        echo '<form name="fieldform" action="fields.php" method="post">';
        echo '<input name="d" type="hidden" value="'.$data->id.'" />';
        echo '<input type="hidden" name="mode" value="" />';
        echo '<input name="sesskey" value="'.sesskey().'" type="hidden" />';
        print_simple_box_start('center','50%');

        echo '<table width="100%"><tr>';
        echo '<td>'.get_string('newfield','data').' ';
        choose_from_menu($menufield,'fieldmenu','0','choose','fieldform.mode.value=\'new\';fieldform.submit();','0');
        helpbutton('fields', get_string('addafield','data'), 'data');
        echo '</td></tr>';
        
        if (!record_exists('data_fields','dataid',$data->id)) {
            echo '<tr><td colspan="2">'.get_string('nofieldindatabase','data').'</td></tr>';  // nothing in database

        } else {    //else print quiz style list of fields
            echo '<tr><td>';
            print_simple_box_start('center','90%');
            echo '<table width="100%"><tr><td align="center"><b>'.get_string('action','data').
                 '</b></td><td><b>'.get_string('fieldname','data').
                 '</b></td><td align="center"><b>'.get_string('type','data').'</b></td></tr>';

            if ($fff = get_records('data_fields','dataid',$data->id)){
                foreach ($fff as $ff) {
                    $field = data_get_field($ff, $data);

                    ///Print Action Column

                    echo '<tr><td align="center">';
                    echo '<a href="fields.php?d='.$data->id.'&amp;mode=display&amp;fid='.$field->field->id.'&amp;sesskey='.sesskey().'">';
                    echo '<img src="'.$CFG->pixpath.'/t/edit.gif" height="11" width="11" border="0" alt="'.get_string('edit').'" /></a>';
                    echo '&nbsp;';
                    echo '<a href="fields.php?d='.$data->id.'&amp;mode=delete&amp;fid='.$field->field->id.'&amp;sesskey='.sesskey().'">';
                    echo '<img src="'.$CFG->pixpath.'/t/delete.gif" height="11" width="11" border="0" alt="'.get_string('delete').'" /></a>';
                    echo '</td>';

                    ///Print Fieldname Column

                    echo '<td>';
                    echo '<a href="fields.php?mode=display&amp;d='.$data->id;
                    echo '&amp;fid='.$field->field->id.'&amp;sesskey='.sesskey().'">'.$field->field->name.'</a>';
                    echo '</td>';

                    ///Print Type Column

                    echo '<td align="center">';
                    echo $field->image();    //print type icon
                    echo '</td></tr>';
                }
            }
            echo '</table>';
            print_simple_box_end();
        }    //close else
        
        echo '</td></tr></table>';

        print_simple_box_end();
        echo '</form>';

    }

/// Finish the page
    print_footer($course);

?>
