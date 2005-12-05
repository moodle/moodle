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

    define('PAGE_DATA_VIEW',   'mod-data-view');
    define('PAGE_DATA', PAGE_DATA_VIEW);

    require_once('pagelib.php');
    require_login();

    page_map_class(PAGE_DATA_VIEW, 'page_data');
    $DEFINEDPAGES = array(PAGE_DATA_VIEW,);

    $id    = optional_param('id', 0, PARAM_INT);  // course module id
    $d     = optional_param('d', 0, PARAM_INT);   // database id
    $search = optional_param('search','',PARAM_NOTAGS);    //search string
    $page = optional_param('page', 0, PARAM_INT);    //offset of the current record
    $rid = optional_param('rid', 0, PARAM_INT);    //record id
    $perpagemenu = optional_param('perpage1', 0, PARAM_INT);    //value from drop down

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
    
    //set user preference if available
    if (isset($_POST['updatepref'])){
   
        if (!$perpage = $perpagemenu){    //if menu not in use, use the text field
            $perpage = (int)optional_param('perpage',10);
        }
        $perpage = ($perpage <= 0) ? 10 : $perpage ;
        set_user_preference('data_perpage', $perpage);
    }
    
    $d = $data->id;//set this so tabs can work properly
    add_to_log($course->id, 'data', 'view', "view.php?id=$cm->id", $data->id, $cm->id);


// Initialize $PAGE, compute blocks
    $PAGE       = page_create_instance($data->id);
    $pageblocks = blocks_setup($PAGE);
    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);

    if (!empty($edit) && $PAGE->user_allowed_editing()) {
        if ($edit == 'on') {
            $USER->editing = true;
        } else if ($edit == 'off') {
            $USER->editing = false;
        }
    }
/// Print the page header
    $PAGE->print_header($course->shortname.': %fullname%');

    echo '<table id="layout-table"><tr>';

    if(!empty($CFG->showblocksonmodpages) && (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing())) {
        echo '<td style="width: '.$blocks_preferred_width.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        echo '</td>';
    }

    echo '<td id="middle-column">';

    print_heading(format_string($data->name));

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

    $currenttab = 'browse';
    include('tabs.php'); 

/// Print the browsing interface

    /***************************
     * code to delete a record *
     ***************************/
    if (($delete = optional_param('delete',0,PARAM_INT)) && confirm_sesskey()){
        if (isteacheredit($course) or data_isowner($delete)){
            if ($confirm = optional_param('confirm',0,PARAM_INT)){
                //find all contents in this record?
                if ($contents = get_records('data_content','recordid',$delete)){

                    //for each content, delete the file associated
                    foreach ($contents as $content){
                        $field = get_record('data_fields','id',$content->fieldid);

                        if ($g = data_get_field($field)){    //it is possible that the field is deleted by teacher
                            $g->delete_data_content_files($data->id, $delete, $content->content);
                        }
                    }
                    delete_records('data_records','id',$delete);
                    delete_records('data_content','recordid',$delete);
                    notify (get_string('recorddeleted','data'));
                }
            }
            else {    //prints annoying confirmation dialogue
                $field = get_record('data_records','id',$delete);
                print_simple_box_start('center', '60%');
                echo '<div align="center">';
                echo '<form action = "view.php?d='.$data->id.'&amp;delete='.$delete.'" method="post">';
                //add sesskey
                echo get_string('confirmdeleterecord','data');
                echo '<p />';
                echo '<input type="hidden" value="'.sesskey().'" name="sesskey">';
                echo '<input type="submit" value="'.get_string('ok').'"> ';
                echo '<input type="hidden" name="confirm" value="1">';
                echo '<input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1);" />';
                echo '</form>';
                echo '</div>';
                print_simple_box_end();
                echo '</td></tr></table>';
                print_footer($course);
                exit;
            }
        }
    }

    //if not editting teacher, check whether user has sufficient records to view
    if (!isteacheredit($course->id) and data_numentries($data) < $data->requiredentriestoview){
        notify (($data->requiredentriestoview - data_numentris(data)).'&nbsp;'.get_string('insufficiententries','data'));
        print_footer($course);
        exit;
    }

    if ($rid){    //set per page to 1, if looking for 1 specific record
        set_user_preference('data_perpage', PERPAGE_SINGLE);
    }
    
    /*****************************
     * Setting up page variables *
     *****************************/
    
    $perpage = get_user_preferences('data_perpage', 10);    //get default per page
    
    $baseurl = 'view.php?d='.$data->id.'&amp;search='.$search.'&amp;';

    if ($rid){    //only used for single mode, but rid should not appear in multi view anyway
        $sqlo = 'SELECT COUNT(*) FROM '.$CFG->prefix
                .'data_records WHERE id < '.$rid.' AND dataid='.$data->id;
        $page = count_records_sql($sqlo);
    }

    if ($search){    //if in search mode, only search text fields

        $sql = 'SELECT DISTINCT c.recordid, c.recordid FROM '.$CFG->prefix.'data_content c LEFT JOIN '
               .$CFG->prefix.'data_fields f on c.fieldid = f.id WHERE f.dataid = '
               .$data->id.' AND c.content LIKE "%'.$search.'%" ';

        $sqlcount = 'SELECT COUNT(DISTINCT c.recordid) FROM '.$CFG->prefix
                    .'data_content c LEFT JOIN '.$CFG->prefix
                    .'data_fields f on c.fieldid = f.id WHERE f.dataid = '
                    .$data->id.' AND c.content LIKE "%'.$search.'%" ';
    }
    else {  //else get everything

        $sql = 'SELECT * FROM '.$CFG->prefix.'data_records WHERE dataid ='.$data->id.' ORDER BY id ASC ';
        $sqlcount = 'SELECT COUNT(*) FROM '.$CFG->prefix
                    .'data_records WHERE dataid ='.$data->id.' ';
    }
    
    $limit = $perpage > 1 ? sql_paging_limit($page * $perpage, $perpage)
                            : $limit = sql_paging_limit($page, PERPAGE_SINGLE);

    $sql = $sql . $limit;
    $totalcount = count_records_sql($sqlcount);

    if (!$records = get_records_sql($sql)){
        if ($search){
            notify(get_string('nomatch','data'));
        }
        else {
            notify(get_string('norecords','data'));
        }
            
        data_print_preference_form($data, $perpage, $search);
        print_footer($course);
        exit;
    }
    
    //print header for multi view
    if ($perpage > 1){

        echo $data->listtemplateheader;
        $listmode = 'listtemplate';
        if (empty($data->listtemplate)){
            notify(get_string('nolisttemplate','data'));
        }
    }
    else {
        $listmode = 'singletemplate';
        if (empty($data->singletemplate)){
            notify(get_string('nosingletemplate','data'));
        }
    }

    print_paging_bar($totalcount, $page, $perpage, $baseurl, $pagevar='page');
    
    //for each record we find, we do a string replacement for tags.
    data_print_template($records, $data, $search, $listmode);

    print_paging_bar($totalcount, $page, $perpage, $baseurl, $pagevar='page');

    if ($perpage > 1){
        echo $data->listtemplatefooter;    //print footer
    }

    data_print_preference_form($data, $perpage, $search);

/// Finish the page

    echo '</td></tr></table>';

    print_footer($course);

?>
