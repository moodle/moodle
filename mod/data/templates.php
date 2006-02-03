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

    require_login();

    $id    = optional_param('id', 0, PARAM_INT);  // course module id
    $d     = optional_param('d', 0, PARAM_INT);   // database id
    $mode  = optional_param('mode', '', PARAM_ALPHA);

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
    
    if (isteacher($course->id)) {
        if (!count_records('data_fields','dataid',$data->id)) {      // Brand new database!
            redirect($CFG->wwwroot.'/mod/data/fields.php?d='.$data->id);  // Redirect to field entry
        }
    }

    add_to_log($course->id, 'data', 'view', "view.php?id=$cm->id", $data->id, $cm->id);


/// Print the page header

    $strdata = get_string('modulenameplural','data');

    print_header_simple($data->name, "", "<a href='index.php?id=$course->id'>$strdata</a> -> $data->name", "", "", true, "", navmenu($course));

    print_heading(format_string($data->name));
    
     ///processing submitted data, i.e updating form
    if (($mytemplate = data_submitted()) && confirm_sesskey()){

        //generate default template
        if (!empty($mytemplate->defaultform)){
            data_generate_default_form($data->id, $mode);
        }
        else if (!empty($mytemplate->allforms)){    //generate all default templates
            data_generate_default_form($data->id, 'singletemplate');
            data_generate_default_form($data->id, 'listtemplate');
            data_generate_default_form($data->id, 'addtemplate');
            data_generate_default_form($data->id, 'rsstemplate');
        }
        else {

            $newtemplate->id = $data->id;
            $newtemplate->{$mode} = $mytemplate->template;

            if (isset($mytemplate->listtemplateheader)){
                $newtemplate->listtemplateheader = $mytemplate->listtemplateheader;
            }
            if (isset($mytemplate->listtemplatefooter)){
                $newtemplate->listtemplatefooter = $mytemplate->listtemplatefooter;
            }

            //check for multiple tags, only need to check for add template
            if ($mode != 'addtemplate' or data_tags_check($data->id, $newtemplate->{$mode})){
                update_record('data',$newtemplate);
            }
        }
    }

/// Check to see if groups are being used here
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "view.php?id=$cm->id");
    } else {
        $currentgroup = 0;
    }

    if ($currentgroup) {
        $groupselect = " AND groupid = '$currentgroup'";
        $groupparam = "&amp;groupid=$currentgroup";
    } else {
        $groupselect = "";
        $groupparam = "";
    }

/// Print the tabs
    $currenttab = 'templates';
    include('tabs.php'); 

/// Print the browsing interface

    echo '<div align="center">'.get_string('header'.$mode,'data').'</div><br />';

    echo '<form name="tempform" action="templates.php?d='.$data->id.'&amp;mode='.$mode.'" method="POST">';
    echo '<input name="sesskey" value="'.sesskey().'" type="hidden">';
    //print button to autogen all forms, if all templates are empty

    $data = get_record('data', 'id', $d);    //reload because of possible updates so far!

    if (empty($data->addtemplate) and empty($data->singletemplate) and empty($data->listtemplate) and empty($data->rsstemplate)){
        echo '<div align="center"><input type="submit" name="allforms" value="'.get_string('autogenallforms','data').'" /></div>';
    }
    
    print_simple_box_start('center','80%');
    echo '<table><tr><td colspan="2">';

    ///add all the available fields for this data
    echo get_string('availabletags','data');
    helpbutton('tags', get_string('tags','data'), 'data');
    echo '</td></tr><tr><td valign="top">';
    echo '<select name="fields1[]" size="10" onclick="insertAtCursor(document.tempform.template, this.options[selectedIndex].value)">';    //the insertAtCursor thing only works when editting in plain text =(
    if ($fields = get_records('data_fields','dataid',$data->id)){
        foreach ($fields as $field) {
            echo '<option value="[['.$field->name.']]">'.$field->name.'('.$field->type.')</option>';
            echo '[['.$field->name.']]'.'<br />';
        }
    }
    //print special tags
    echo '<option value="##edit##">##Edit##</option>';
    echo '<option value="##more##">##More##</option>';
    echo '<option value="##delete##">##Delete##</option>';
    echo '</select>';

    ///add the HTML editor(s)
    echo '</td><td>';
    $usehtmleditor = can_use_html_editor();
    if ($mode == 'listtemplate'){
        echo '<div align="center">'.get_string('header','data').'</div>';
        print_textarea($usehtmleditor, 10, 72, 0, 0, 'listtemplateheader', $data->listtemplateheader);
    }
    if ($mode == 'listtemplate'){
        echo '<div align="center">'.get_string('multientry','data').'</div>';
    }
    print_textarea($usehtmleditor, 20, 72, 0, 0, 'template', $data->{$mode});
    if ($mode == 'listtemplate'){
        echo '<div align="center">'.get_string('footer','data').'</div>';
        print_textarea($usehtmleditor, 10, 72, 0, 0, 'listtemplatefooter', $data->listtemplatefooter);
    }
    echo '</td></tr>';

    echo '<tr><td align="center" colspan="2">';
    echo '<input type="submit" value="'.get_string('savetemplate','data').'" />&nbsp;';
    if (!$data->{$mode}){
        echo '<input type="submit" name="defaultform" value="'.get_string('generatedefault','data').'" />';
    }
    
    echo '</td></tr></table>';
    print_simple_box_end();
    echo '</form>';
    if ($usehtmleditor) {
        use_html_editor('template');
        if ($mode == 'listtemplate'){
            use_html_editor('listtemplateheader');
            use_html_editor('listtemplatefooter');
        }
    }

/// Finish the page
    
    print_footer($course);

?>
