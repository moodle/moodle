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
    require_capability('mod/data:manageentries', $context);

/// Print the page header
    $strdata = get_string('modulenameplural','data');
    
    $navigation = build_navigation('', $cm);
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

        // Fix mac/dos newlines and clean BOM
        // TODO: Switch to cvslib when possible
        $textlib = textlib_get_instance();
        $text = my_file_get_contents($filename);
        $text = preg_replace('!\r\n?!',"\n",$text);
        $text = $textlib->trim_utf8_bom($text); // remove Unicode BOM from first line
        $fp = fopen($filename, "w");
        fwrite($fp, $text);
        fclose($fp);

        $recordsadded = 0;

        if (!$records = data_get_records_csv($filename, $fielddelimiter, $fieldenclosure)) {
            print_error('csvfailed','data',"{$CFG->wwwroot}/mod/data/edit.php?d={$data->id}");
        } else {
            //$db->debug = true;
            $fieldnames = array_shift($records);

            // check the fieldnames are valid
            $fields = get_records('data_fields', 'dataid', $data->id, '', 'name, id, type');
            $errorfield = '';
            foreach ($fieldnames as $name) {
                if (!isset($fields[$name])) {
                    $errorfield .= "'$name' ";
                }
            }

            if (!empty($errorfield)) {
                print_error('fieldnotmatched','data',"{$CFG->wwwroot}/mod/data/edit.php?d={$data->id}",$errorfield);
            }

            foreach ($records as $record) {
                if ($recordid = data_add_record($data, 0)) {  // add instance to data_record
                    $fields = get_records('data_fields', 'dataid', $data->id, '', 'name, id, type');

                    // Insert new data_content fields with NULL contents:
                    foreach ($fields as $field) {
                        $content = new object();
                        $content->recordid = $recordid;
                        $content->fieldid = $field->id;
                        if (! insert_record('data_content', $content)) {
                            print_error('cannotinsertrecord', '', '', $recordid);
                        }
                    }
                    // Fill data_content with the values imported from the CSV file:
                    foreach ($record as $key => $value) {
                        $name = $fieldnames[$key];
                        $field = $fields[$name];
                        $content = new object();
                        $content->fieldid = $field->id;
                        $content->recordid = $recordid;
                        if ($field->type == 'textarea') {
                            // the only field type where HTML is possible
                            $value = clean_param($value, PARAM_CLEANHTML);
                        } else {
                            // remove potential HTML:
                            $patterns[] = '/</';
                            $replacements[] = '&lt;';
                            $patterns[] = '/>/';
                            $replacements[] = '&gt;';
                            $value = preg_replace($patterns, $replacements, $value);
                        }
                        $value = addslashes($value);
                        // for now, only for "latlong" and "url" fields, but that should better be looked up from
                        // $CFG->dirroot . '/mod/data/field/' . $field->type . '/field.class.php'
                        // once there is stored how many contents the field can have. 
                        if (preg_match("/^(latlong|url)$/", $field->type)) {
                            $values = explode(" ", $value, 2);
                            $content->content  = $values[0];
                            $content->content1 = $values[1];
                        } else {
                            $content->content = $value;
                        }
                        $oldcontent = get_record('data_content', 'fieldid', $field->id, 'recordid', $recordid);
                        $content->id = $oldcontent->id;
                        if (! update_record('data_content', $content)) {
                            print_error('cannotupdaterecord', '', '', $recordid);
                        }
                    }
                    $recordsadded++;
                    print get_string('added', 'moodle', $recordsadded) . ". " . get_string('entry', 'data') . " (ID $recordid)<br />\n";
                }
            }
        }
    }

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
