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
    require_once("$CFG->libdir/rsslib.php");

    require_once('pagelib.php');
    require_login();

    $id    = optional_param('id', 0, PARAM_INT);  // course module id
    $d     = optional_param('d', 0, PARAM_INT);   // database id
    $search = optional_param('search','',PARAM_NOTAGS);    //search string
    $page = optional_param('page', 0, PARAM_INT);    //offset of the current record
    $rid = optional_param('rid', 0, PARAM_INT);    //record id
    $perpagemenu = optional_param('perpage1', 0, PARAM_INT);    //value from drop down
    $sort = optional_param('sort',0,PARAM_INT);    //sort by field
    $order = optional_param('order','ASC',PARAM_ALPHA);    //sort order
    $group = optional_param('group','0',PARAM_INT);    //groupid
    
    
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
        if (!record_exists('data_fields','dataid',$data->id)) {      // Brand new database!
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

/// RSS meta
    $rssmeta = '';
    if (isset($CFG->enablerssfeeds) && isset($CFG->data_enablerssfeeds) && $data->rssarticles > 0) {
        $rsspath = rss_get_url($course->id, $USER->id, 'data', $data->id);
        $rssmeta = '<link rel="alternate" type="application/rss+xml" ';
        $rssmeta .= 'title ="'.$course->shortname.': %fullname%" href="'.$rsspath.'" />';
    }
    
/// Print the page header
    $PAGE->print_header($course->shortname.': %fullname%', '', $rssmeta);
    
    echo '<table id="layout-table"><tr>';

    if(!empty($CFG->showblocksonmodpages) && (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing())) {
        echo '<td style="width: '.$blocks_preferred_width.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        echo '</td>';
    }

    echo '<td id="middle-column">';

    print_heading(format_string($data->name));
    
    // Do we need to show a link to the RSS feed for the records?
    if (isset($CFG->enablerssfeeds) && isset($CFG->data_enablerssfeeds) && $data->rssarticles > 0) {
        echo '<div style="float:right;">';
        rss_print_link($course->id, $USER->id, 'data', $data->id, get_string('rsstype'));
        echo '</div>';
        echo '<div style="clear:both;"></div>';
    }
    
    if ($data->intro) {
        print_simple_box(format_text($data->intro), 'center', '70%', '', 5, 'generalbox', 'intro');
        echo '<br />';
    }

/// Check to see if groups are being used here
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, 
                                            'view.php?d='.$data->id.'&amp;search='.s($search).'&amp;sort='.s($sort).
                                            '&amp;order='.s($order).'&amp;');
    } else {
        $currentgroup = 0;
    }

    if ($currentgroup) {
        $groupselect = " AND (r.groupid = '$currentgroup' OR r.groupid = 0)";
        $groupparam = "&amp;groupid=$currentgroup";
    } else {
        $groupselect = "";
        $groupparam = "";
    }

/// Print the tabs

    $currenttab = 'browse';
    include('tabs.php'); 

/// Print the browsing interface

    if (optional_param('approved','0',PARAM_INT)) {
        print_heading(get_string('recordapproved','data'));
    }

    /***************************
     * code to delete a record *
     ***************************/
    if (($delete = optional_param('delete',0,PARAM_INT)) && confirm_sesskey()) {
        if (isteacheredit($course) or data_isowner($delete)){
            if ($confirm = optional_param('confirm',0,PARAM_INT)) {
                if ($contents = get_records('data_content','recordid', $delete)) {
                    foreach ($contents as $content) {  // Delete files or whatever else this field allows
                        if ($field = data_get_field_from_id($content->fieldid, $data)) { // Might not be there
                            $field->delete_content($content->recordid);
                        }
                    }
                }
                delete_records('data_content','recordid',$delete);
                delete_records('data_records','id',$delete);
                    
                add_to_log($course->id, 'data', 'record delete', "view.php?id=$cm->id", $data->id, $cm->id);
                    
                notify(get_string('recorddeleted','data'));

            } else {   // Print a confirmation page
                notice_yesno(get_string('confirmdeleterecord','data'), 
                             'view.php?d='.$data->id.'&amp;delete='.$delete.'&amp;confirm=1&amp;sesskey='.sesskey(),
                             'view.php?d='.$data->id);

                print_footer($course);
                exit;
            }
        }
    }

    //if not editting teacher, check whether user has sufficient records to view
    if (!isteacheredit($course->id) and data_numentries($data) < $data->requiredentriestoview){
        notify (($data->requiredentriestoview - data_numentris(data)).'&nbsp;'.get_string('insufficiententries','data'));
        echo '</td></tr></table>';
        print_footer($course);
        exit;
    }

    if ($rid){    //set per page to 1, if looking for 1 specific record
        set_user_preference('data_perpage', DATA_PERPAGE_SINGLE);
    }
  
    /*****************************
     * Setting up page variables *
     *****************************/
    
    $perpage = get_user_preferences('data_perpage', 10);    //get default per page

    $baseurl = 'view.php?d='.$data->id.'&amp;search='.s($search).'&amp;sort='.s($sort).'&amp;order='.s($order).'&amp;group='.$currentgroup.'&amp;';


    //if database requires approval, then we need to do some work
    //and get those approved entries, or entries belongs to owner
    if ((!isteacher($course->id)) && ($data->approval)){
        $approvesql = ' AND (r.approved=1 OR r.userid='.$USER->id.') ';
    } else {
        $approvesql = '';
    }

    if ($rid){    //only used for single mode, but rid should not appear in multi view anyway
        $ridsql = 'AND r.id < '.$rid.' ';

    } else {
        $ridsql = '';
    }

    if ($sort) {    //supports (sort and search)
        //first find the field that we are sorting
        $sortfield = data_get_field_from_id($sort, $data);
        $sortcontent = $sortfield->get_sort_field();
        ///SEARCH AND SORT SQL
        $sql = 'SELECT DISTINCT c.recordid, c.recordid
                FROM '.$CFG->prefix.'data_content c, '
                .$CFG->prefix.'data_records r, '
                .$CFG->prefix.'data_content c1
                WHERE c.recordid = r.id
                AND c1.recordid = r.id
                AND r.dataid = '.$data->id.'
                AND c.fieldid = '.$sort.' '.$groupselect.'
                AND ((c1.content LIKE "%'.$search.'%") OR
                     (c1.content1 LIKE "%'.$search.'%") OR
                     (c1.content2 LIKE "%'.$search.'%") OR
                     (c1.content3 LIKE "%'.$search.'%") OR
                     (c1.content4 LIKE "%'.$search.'%")) '.$approvesql.'
                ORDER BY c.'.$sortcontent.' '.$order.' ';

        $sqlcount = 'SELECT COUNT(DISTINCT c.recordid)
                FROM '.$CFG->prefix.'data_content c, '
                .$CFG->prefix.'data_records r, '
                .$CFG->prefix.'data_content c1
                WHERE c.recordid = r.id
                AND c1.recordid = r.id
                AND r.dataid = '.$data->id.'
                AND c.fieldid = '.$sort.' '.$groupselect.'
                AND ((c1.content LIKE "%'.$search.'%") OR
                     (c1.content1 LIKE "%'.$search.'%") OR
                     (c1.content2 LIKE "%'.$search.'%") OR
                     (c1.content3 LIKE "%'.$search.'%") OR
                     (c1.content4 LIKE "%'.$search.'%")) '.$approvesql.'
                ORDER BY c.'.$sortcontent.' '.$order.' ';

        //sqlindex is used to find the number of entries smaller than the current rid
        //useful for zooming into single view from multi view (so we can keep track
        //of exact and relative position of records
        $sqlindex = 'SELECT COUNT(DISTINCT c.recordid)
                FROM '.$CFG->prefix.'data_content c, '
                .$CFG->prefix.'data_records r, '
                .$CFG->prefix.'data_content c1
                WHERE c.recordid = r.id
                AND c1.recordid = r.id
                AND r.dataid = '.$data->id.'
                AND c.fieldid = '.$sort.' '.$ridsql.' '.$groupselect.'
                AND ((c1.content LIKE "%'.$search.'%") OR
                     (c1.content1 LIKE "%'.$search.'%") OR
                     (c1.content2 LIKE "%'.$search.'%") OR
                     (c1.content3 LIKE "%'.$search.'%") OR
                     (c1.content4 LIKE "%'.$search.'%")) '.$approvesql.'
                ORDER BY c.'.$sortcontent.' '.$order.' ';
                
    } else if ($search){    //search only, no sort. if in search mode, only search text fields

        $sql = 'SELECT DISTINCT c.recordid, c.recordid
                FROM '.$CFG->prefix.'data_content c, '
                .$CFG->prefix.'data_fields f, '
                .$CFG->prefix.'data_records r
                WHERE c.recordid = r.id '.$groupselect.' '.$approvesql.' AND
                c.fieldid = f.id AND f.dataid = '
                .$data->id.' AND c.content LIKE "%'.$search.'%" ORDER BY r.id '.$order.' ';

        $sqlcount = 'SELECT COUNT(DISTINCT c.recordid)
                FROM '.$CFG->prefix.'data_content c, '
                .$CFG->prefix.'data_fields f, '
                .$CFG->prefix.'data_records r
                WHERE c.recordid = r.id '.$groupselect.' '.$approvesql.' AND
                c.fieldid = f.id AND f.dataid = '
                .$data->id.' AND c.content LIKE "%'.$search.'%" ORDER BY r.id '.$order.' ';

        $sqlindex = 'SELECT COUNT(DISTINCT c.recordid)
                FROM '.$CFG->prefix.'data_content c, '
                .$CFG->prefix.'data_fields f, '
                .$CFG->prefix.'data_records r
                WHERE c.recordid = r.id '.$groupselect.' '.$approvesql.' AND
                c.fieldid = f.id AND f.dataid = '
                .$data->id.'  '.$ridsql.' AND c.content LIKE "%'.$search.'%" ORDER BY r.id '.$order.' ';

    } else {  //else get everything, no search, no sort

        $sql = 'SELECT * FROM '.$CFG->prefix.'data_records r WHERE r.dataid ='.$data->id.' '.$groupselect.' '.$approvesql.' ORDER BY r.id '.$order.' ';
        $sqlcount = 'SELECT COUNT(*) FROM '.$CFG->prefix
                    .'data_records r WHERE r.dataid ='.$data->id.' '.$groupselect.' '.$approvesql.'ORDER BY r.id '.$order.' ';

        $sqlindex = 'SELECT COUNT(*) FROM '.$CFG->prefix
                    .'data_records r WHERE r.dataid ='.$data->id.' '.$groupselect.' '.$ridsql.' '.$approvesql .'ORDER BY r.id '.$order.' ';
    }
    
    if ($rid) {    //this is used in zooming
        $page = count_records_sql($sqlindex);
    }
    
    $limit = $perpage > 1 ? sql_paging_limit($page * $perpage, $perpage)
                            : $limit = sql_paging_limit($page, DATA_PERPAGE_SINGLE);

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
        echo '</td></tr></table>';
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
    data_print_template($records, $data, $search, $listmode, $sort, $page, $rid, $order, $currentgroup);
    print_paging_bar($totalcount, $page, $perpage, $baseurl, $pagevar='page');

    if ($perpage > 1){
        echo $data->listtemplatefooter;    //print footer
    }

    data_print_preference_form($data, $perpage, $search, $sort, $order);
    
    // Finish the page
    echo '</td></tr></table>';
    
    print_footer($course);

?>
