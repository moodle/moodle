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

    $mode ='addtemplate';    //define the mode for this page, only 1 mode available
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
    if ((!isteacher($course->id)) && $data->participants == PARTICIPANTS_T) {
        error ('students are not allowed to participate in this activity');
    }

    if ($rid){    //editting a record, do you have access to edit this?
        if (!isteacher($course->id) or !data_isowner($rid) or !confirm_sesskey()){
            error (get_string('noaccess','data'));
        }
    }
  

/// Print the page header

    $strdata = get_string('modulenameplural','data');

    print_header_simple($data->name, "", "<a href='index.php?id=$course->id'>$strdata</a> -> $data->name", "", "", true, "", navmenu($course, $cm));

    print_heading(format_string($data->name));

/// Check to see if groups are being used here
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, 'add.php?d='.$data->id.'&amp;sesskey='.sesskey().'&amp;');
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

    $currenttab = 'add';
    include('tabs.php');


/********************************************
 * code to handle form processing           *
 * add individual data_content              *
 ********************************************/
    $entrysaved = false;    //flag for displaying entry saved msg

    if ($datarecord = data_submitted($CFG->wwwroot.'/mod/data/add.php') and confirm_sesskey()) {
        //if rid is present, we are in updating mode
        if ($rid){

            //set flag to unapproved after each edit
            $record = get_record('data_records','id',$rid);
            if (isteacher($course->id)) {
                $record->approved = 1;
            } else {
                $record->approved = 0;
            }
           
            $record->groupid = $currentgroup;
            update_record('data_records',$record);

            foreach ($datarecord as $name=>$value){
                //this creates a new field subclass object
                if ($name != 'MAX_FILE_SIZE' && $name != 'sesskey' and $name!='d' and $name!='rid'){
                    if (($currentfield = data_get_field_from_name($name)) !== false) {
                        //use native subclass method to store field data
                        $currentfield->update_data_content($currentfield->id, $rid, $value, $name);
                    }
                }
            }

            add_to_log($course->id, 'data', 'update', "view.php?d=$data->id&amp;rid=$rid", $data->id, $cm->id);

            redirect($CFG->wwwroot.'/mod/data/view.php?d='.$data->id);

        } else {    //we are adding a new record

            ///Empty form checking - you can't submit an empty form!
            $emptyform = true;   //a bad beginning
            $defaults = array();    //this is a list of strings to be ignored in empty check
            
            foreach ($datarecord as $name => $value){    //check to see if everything is empty

                if ($name != 'MAX_FILE_SIZE' and $name != 'sesskey' and $name!='d' and $name!='rid'){
                    //call native method to check validity
                    $currentfield = data_get_field_from_name($name);
                    if ($currentfield->notemptyfield($value, $name)){
                        $emptyform = false;    //if anything is valid, this form is not empty!
                    }
                }
            }    ///End of Empty form checking


            if (!$emptyform && $recordid = data_add_record($data->id, $currentgroup)){    //add instance to data_record
                $fields = get_records('data_fields','dataid',$data->id);
                
                //do a manual round of inserting, to make sure even empty conentes get stored
                foreach ($fields as $field) {
                    $content ->recordid = $recordid;
                    $content ->fieldid = $field->id;
                    insert_record('data_content',$content);
                }
                //for each field in the add form, add it to the data_content.
                foreach ($datarecord as $name => $value){
                    if ($name != 'MAX_FILE_SIZE' && $name != 'sesskey' and $name!='d' and $name!='rid'){  //hack to skip these inputs
                        $currentfield = data_get_field_from_name($name);
                        //use native subclass method to sore field data
                        $currentfield->update_data_content($currentfield->id, $recordid, $value, $name);
                    }
                }
                $entrysaved = true;
            }
            
            if ($emptyform){    //nothing gets written to database
                notify(get_string('emptyaddform','data'));
            }
            
            if ($entrysaved) {
                add_to_log($course->id, 'data', 'add', "view.php?d=$data->id&amp;rid=$recordid", $data->id, $cm->id);
            }
        }
    }
/**************************
 * End of form processing *
 **************************/
    
/// Print entry saved msg, if any
    if (!empty($entrysaved)){
        notify (get_string('entrysaved','data'));
        echo '<p />';
    }


///Check if maximum number of entry as specified by this database is reached
///Of course, you can't be stopped if you are an editting teacher! =)
    if (data_atmaxentries($data) and !isteacheredit($course->id)){
        notify (get_string('atmaxentry','data'));
        print_footer($course);
        exit;
    }

    /// Print the browsing interface

    $patterns = array();    //tags to replace
    $replacement = array();    //html to replace those yucky tags

    //form goes here first in case add template is empty
    echo '<form enctype="multipart/form-data" action="add.php" method="post">';
    echo '<input name="d" value="'.$data->id.'" type="hidden" />';
    echo '<input name="rid" value="'.$rid.'" type="hidden" />';
    echo '<input name="sesskey" value="'.sesskey().'" type="hidden" />';
    print_simple_box_start('center','80%');
    
    if (!$rid){
        print_heading(get_string('newentry','data'), '', 2);
    }
    
    /******************************************
     * Regular expression replacement section *
     ******************************************/
    if ($data->addtemplate){
        $possiblefields = get_records('data_fields','dataid',$data->id);
        
        ///then we generate strings to replace
        foreach ($possiblefields as $cfield){
            $patterns[]="/\[\[".$cfield->name."\]\]/i";
            $g = data_get_field($cfield);
            $replacements[] = $g->display_add_field($cfield->id, $rid);
            
            unset($g);
        }
        $newtext = preg_replace($patterns, $replacements, $data->{$mode});
    }
    else {    //if the add template is not yet defined, print the default form!
        data_generate_empty_add_form($data->id, $rid);
        $newtext = '';
    }
   
    echo $newtext;
    echo '<div align="center"><input type="submit" value="'.get_string('save','data').'" />';
    if ($rid){
        echo '&nbsp;<input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1)">';
    }
    echo '</div>';
    print_simple_box_end();
    echo '</form>';

/// Finish the page
    
    // Print the stuff that need to come after the form fields.
    $storedFields = get_records('data_fields', 'dataid', $data->id);
    foreach ($storedFields as $sf) {
        $fieldClass = 'data_field_' . $sf->type;
        $fieldObj = new $fieldClass($sf->id);
        
        echo "\n\n";
        $fieldObj->print_after_form();
    }
    
    print_footer($course);
?>
