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
    require_once($CFG->libdir.'/uploadlib.php');

    require_login();

    $id              = optional_param('id', 0, PARAM_INT);  // course module id
    $d               = optional_param('d', 0, PARAM_INT);   // database id
    $rid             = optional_param('rid', 0, PARAM_INT); // record id
    $fielddelimiter  = optional_param('fielddelimiter', ',', PARAM_CLEANHTML); // characters used as field delimiters for csv file import
    $fieldenclosure = optional_param('fieldenclosure', '', PARAM_CLEANHTML);   // characters used as record delimiters for csv file import

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

    require_login($course, false, $cm);

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/data:uploadentries', $context);

    if (has_capability('mod/data:managetemplates', $context)) {
        if (!count_records('data_fields','dataid',$data->id)) {      // Brand new database!
            redirect($CFG->wwwroot.'/mod/data/field.php?d='.$data->id);  // Redirect to field entry
        }
    }

    if ($rid){    //editting a record, do you have access to edit this?
        if (!has_capability('mod/data:manageentries', $context) or !data_isowner($rid) or !confirm_sesskey()){
            error (get_string('noaccess','data'));
        }
    }


/// Print the page header
    $strdata = get_string('modulenameplural','data');
    
    $navlinks = array();
    $navlinks[] = array('name' => $strdata, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navlinks[] = array('name' => format_string($data->name), 'link' => '', 'type' => 'activityinstance');
    $navigation = build_navigation($navlinks);
    
    print_header_simple($data->name, "", $navigation, "", "", true, "", navmenu($course));
    print_heading(format_string($data->name));

/// Groups needed for Add entry tab
    $currentgroup = groups_get_activity_group($cm);
    $groupmode = groups_get_activity_groupmode($cm);

/// Print the tabs
    $currenttab = 'add';
    include('tabs.php');


    $um = new upload_manager('recordsfile', false, false, null, false, 0);

    if ($um->preprocess_files() && confirm_sesskey()) {
        $filename = $um->files['recordsfile']['tmp_name'];

        // Large files are likely to take their time and memory. Let PHP know
        // that we'll take longer, and that the process should be recycled soon
        // to free up memory.
        @set_time_limit(0);
        @raise_memory_limit("96M");
        if (function_exists('apache_child_terminate')) {
            @apache_child_terminate();
        }

        //Fix mac/dos newlines
        $text = my_file_get_contents($filename);
        $text = preg_replace('!\r\n?!',"\n",$text);
        $fp = fopen($filename, "w");
        fwrite($fp, $text);
        fclose($fp);

        $recordsadded = 0;

        if (!$records = data_get_records_csv($filename, $fielddelimiter, $fieldenclosure)) {
            error('get_records_csv failed to read data from the uploaded file. Please check file for field name typos and formatting errors.');
        } else {
            //$db->debug = true;
            $fieldnames = array_shift($records);

            foreach ($records as $record) {
                if ($recordid = data_add_record($data, 0)) {  // add instance to data_record
                    $fields = get_records('data_fields', 'dataid', $data->id, '', 'name, id, type');

                    // do a manual round of inserting, to make sure even empty contents get stored
                    foreach ($fields as $field) {
                        $content->recordid = $recordid;
                        $content->fieldid = $field->id;
                        insert_record('data_content', $content);
                    }
                    // for each field in the add form, add it to the data_content.
                    foreach ($record as $key => $value) {
                        $name = $fieldnames[$key];
                        $field = $fields[$name];
                        require_once($CFG->dirroot.'/mod/data/field/'.$field->type.'/field.class.php');
                        $newfield = 'data_field_'.$field->type;
                        $currentfield = new $newfield($field->id);

                        $currentfield->update_content($recordid, $value, $name);
                    }
                    $recordsadded++;
                }
            } // End foreach
        } // End else
    }//sun without love motivo atillas

    if ($recordsadded > 0) {
        notify($recordsadded. ' '. get_string('recordssaved', 'data'));
    } else {
        notify(get_string('recordsnotsaved', 'data'));
    }
    echo '<p />';


/// Finish the page
    print_footer($course);




function my_file_get_contents($filename, $use_include_path = 0) {
/// Returns the file as one big long string

    $data = "";
    $file = @fopen($filename, "rb", $use_include_path);
    if ($file) {
        while (!feof($file)) {
            $data .= fread($file, 1024);
        }
        fclose($file);
    }
    return $data;
}



// Read the records from the given file.
// Perform a simple field count check for each record.
function data_get_records_csv($filename, $fielddelimiter=',', $fieldenclosure="\n") {
    global $db;

    if (empty($fielddelimiter)) {
        $fielddelimiter = ',';
    }
    if (empty($fieldenclosure)) {
        $fieldenclosure = "\n";
    }

    if (!$fp = fopen($filename, "r")) {
        error('get_records_csv failed to open '.$filename);
    }
    $fieldnames = array();
    $rows = array();

    $fieldnames = fgetcsv($fp, 4096, $fielddelimiter, $fieldenclosure);

    if (empty($fieldnames)) {
        fclose($fp);
        return false;
    }
    $rows[] = $fieldnames;

    while (($data = fgetcsv($fp, 4096, $fielddelimiter, $fieldenclosure)) !== false) {
        if (count($data) > count($fieldnames)) {
            // For any given record, we can't have more data entities than the number of fields.
            fclose($fp);
            return false;
        }
        $rows[] = $data;
    }

    fclose($fp);
    return $rows;
}

?>
