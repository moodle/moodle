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
    $rid   = optional_param('rid', 0, PARAM_INT);    //record id

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
    
    if (isteacher($course->id)) {
        if (!count_records('data_fields','dataid',$data->id)) {      // Brand new database!
            redirect($CFG->wwwroot.'/mod/data/fields.php?d='.$data->id);  // Redirect to field entry
        }
    }

    ///checking for participants
    if ((!isteacher($course->id)) && $data->participants ==PARTICIPANTS_T) {
        error ('students are not allowed to participate in this activity');
    }

    if ($rid){    //editting a record, do you have access to edit this?
        if (!isteacher($course->id) or !data_isowner($rid) or !confirm_sesskey()){
            error (get_string('noaccess','data'));
        }
    }
  

/// Print the page header

    $strdata = get_string('modulenameplural','data');

    print_header_simple($data->name, "", "<a href='index.php?id=$course->id'>$strdata</a> -> $data->name", "", "", true, "", navmenu($course));

    print_heading(format_string($data->name));

/// Print the tabs

    $currenttab = 'add';
    include('tabs.php');

    if ($records = data_get_records_csv($CFG->dataroot.'/'.$course->id.'/dataimport.csv')) {

        $db->debug =true;
        $fieldnames = array_shift($records);

        foreach ($records as $record) {

            if ($recordid = data_add_record($data->id, 0)) {    //add instance to data_record
                $fields = get_records('data_fields','dataid',$data->id, '', 'name,id,type');
                print_object($fields);
                
                //do a manual round of inserting, to make sure even empty conentes get stored
                foreach ($fields as $field) {
                    $content->recordid = $recordid;
                    $content->fieldid = $field->id;
                    insert_record('data_content',$content);
                }
                //for each field in the add form, add it to the data_content.
                foreach ($record as $key => $value) {
                    $name = $fieldnames[$key];
                    $field = $fields[$name];
                    require_once($CFG->dirroot.'/mod/data/field/'.$field->type.'/field.class.php');
                    $newfield = 'data_field_'.$field->type;
                    $currentfield = new $newfield($field->id);

                    $currentfield->update_data_content($currentfield->id, $recordid, $value, $name);
                }
            }
        }
    }

/// Print entry saved msg, if any
    if (!empty($entrysaved)){
        notify (get_string('entrysaved','data'));
        echo '<p />';
    }


/// Finish the page
    
    print_footer($course);




function data_get_records_csv($file) {
    global $CFG, $db;

    if(!($handle = @fopen($file, 'r'))) {
        error('get_records_csv failed to open '.$file);
    }

    $rows = array();

    $fieldnames = fgetcsv($handle, 4096);
    if(empty($fieldnames)) {
        fclose($handle);
        return false;
    }

    $rows[] = $fieldnames;

    while (($data = fgetcsv($handle, 4096)) !== false) {
        $rows[] = $data;
    }

    fclose($handle);
    return $rows;
}
?>
