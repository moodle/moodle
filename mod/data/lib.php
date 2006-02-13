<?php  // $Id$
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2005 Moodle Pty Ltd    http://moodle.com                //
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

/// Some constants
define ('PARTICIPANTS_T', 1);
define ('PARTICIPANTS_S', 2);
define ('PARTICIPANTS_TS', 3);
define ('DATAMAXENTRIES', 50);
define ('PERPAGE_SINGLE', 1);

class data_field_base {    //base class (text field)

    var $type = 'text';     /// Base class implements "text" field type completely.
    var $id;    //field id

    //constructor
    function data_field_base($fid=0){
        $this->id = $fid;
    }
    
    //returns the name/type of the field
    function name(){
        return get_string('name'.$this->type, 'data');
    }
    
    //prints the respective type icon
    function image(){
        global $CFG;
        return '<img src="'.$CFG->modpixpath.'/data/field/'.$this->type.'/icon.gif" height="16" width="16" border="0"
                     alt="'.$this->type.'" title="'.$this->type.'">';
    }
    
    function insert_field($dataid, $type='text', $name, $des='', $autolink=0){
        $newfield = new object;
        $newfield->dataid = $dataid;
        $newfield->type = $type;
        $newfield->name = $name;
        $newfield->description = $des;
        $newfield->param1 = $autolink;
        if (!insert_record('data_fields',$newfield)){
            notify('Insertion of new field failed!');
        }
    }

    /***********************************************
     * prints the form element in the add template *
     * eg:                                         *
     * Your Name: [       ]                        *
     ***********************************************/
    function display_add_field($id, $rid=0){
        global $CFG;
        if (!$field = get_record('data_fields','id',$id)){
            notify("that is not a valid field id!");
            exit;
        }

        if ($rid){
            $content = get_record('data_content','fieldid',$id,'recordid',$rid);
            if (isset($content->content)){
                $content = $content->content;
            }
        }
        else {
            $content = '';
        }
        
        $str = '';
        /*
        if ($field->description){
            $str .= '<img src="'.$CFG->pixpath.'/help.gif" alt="'.$field->description.'" title="'.$field->description.'">&nbsp;';
        }
        */
        $str .= '<div title="'.$field->description.'">';
        $str .= '<input style="width:300px;" type="text" name="field_'.$field->id.'" id="field_'.$field->id.'" value="'.$content.'" />';
        $str .= '</div>';
        return $str;
    }

    /**********************************************************************
     * prints the field that defines the attributes for editting a field, *
     * only accessible by teachers                                        *
     * eg.                                                                *
     * name of the picture: [        ]                                    *
     * size  [      ]                                                     *
     * width [      ]                                                     *
     **********************************************************************/
    function display_edit_field($id, $dataid=0){
        global $CFG, $course;

        $newfield = 0;
        if (!$field = get_record('data_fields','id',$id)){
            if ($dataid){    //if is a new field
                $field->dataid = $dataid;
                $newfield = 1;
                $field->id = 0;
                $field->param1 = '';
                $field->param2 = '';
                $field->param3 = '';
                $field->name = '';
                $field->description = '';
            }
            else {
                notify("that is not a valid field id!");
                exit;
            }
        }
        print_simple_box_start('center','80%');
        require_once($CFG->dirroot.'/mod/data/field/'.$this->type.'/mod.html');
        print_simple_box_end();
    }
    
    /*********************************************************************
     * prints the browse mode content when viewed                         *
     * eg.                                                                *
     * [icon] Word.doc                                                    *
     **********************************************************************/
    function display_browse_field($fieldid, $recordid, $template){
        if ($content = get_record('data_content', 'fieldid', $fieldid, 'recordid', $recordid)){
            if (isset($content->content)) {                
                $options->para = false;
                $str = format_text($content->content, $content->content1, $options);
            } else {
                $str = '';
            }
            return $str;
        }
        return false;
    }
    
    /****************************
     * updates a field          *
     ****************************/
    function update($fieldobject){
        //special thing is checking param1, if not present, set to 0
        $fieldobject->param1 = isset($fieldobject->param1)?$fieldobject->param1:0;
        if (!update_record('data_fields',$fieldobject)){
            notify ('upate failed');
        }
    }
    
    /************************************
     * store content of this field type *
     ************************************/
    function store_data_content($fieldid, $recordid, $value, $name=''){
        $content = new object;
        $content->fieldid = $fieldid;
        $content->recordid = $recordid;
        $content->content = clean_param($value, PARAM_NOTAGS);
        insert_record('data_content', $content);
    }

    /*************************************
     * update content of this field type *
     *************************************/
    function update_data_content($fieldid, $recordid, $value, $name=''){
        //if data_content already exit, we update
        if ($oldcontent = get_record('data_content','fieldid', $fieldid, 'recordid', $recordid)){
            $content = new object;
            $content->fieldid = $fieldid;
            $content->recordid = $recordid;
            $content->content = clean_param($value, PARAM_NOTAGS);
            $content->id = $oldcontent->id;
            update_record('data_content',$content);
        }
        else {    //make 1 if there isn't one already
            $this->store_data_content($fieldid, $recordid, $value, $name='');
        }
    }
    
    /*************************************************************
     * this function checks if a field from an add form is empty *
     * input $param string $value                                *
     *       $param string $name                                 *
     * output boolean                                            *
     *************************************************************/
    function notemptyfield($value, $name) {
        return !empty($value);
    }
    
    /*********************************************************************
     * This function deletes all data_contents associated with the field *
     *********************************************************************/
    function delete_data_contents() {
        return delete_records('data_content', 'fieldid', $this->id);
    }
    
    /*************************************************************************
     * This function checks if there's any file associated with this file,   *
     * and is uploaded, if so, it deletes it. E.g file or picture type field *
     *************************************************************************/
    function delete_data_content_files($dataid, $recordid, $content) {
        //does nothing
    }
    
    
    /*************************************************************************
     * This function prints anything that the field wants to output after    *
     * the html form. This is handy for the javascript rich text editor      *
     *************************************************************************/
    function print_after_form() {
        // Overridden as needed in sub classes.
    }
    
    
    /* returns the sortable field for the content. By default, it's just content
     * but for some plugins, it could be content 1 - content4
     */
    function get_sort_field() {
        return 'content';
    }
    
}//end of class data_field_base


///Field methods
/*****************************************************************************
/* Given a mode and a dataid, generate a default case template               *
 * input @param mode - addtemplate, singletemplate, listtempalte, rsstemplate*
 *       @param dataid                                                       *
 * output null                                                               *
 *****************************************************************************/
function data_generate_default_form($dataid, $mode){
    if (!$dataid && !$mode){
        return false;
    }
    
    //get all the fields for that database
    if ($fields = get_records('data_fields','dataid',$dataid)){
        $data->id = $dataid;
        $str = '';    //the string to write to the given $data->{$mode}
        //this only applies to add and single template
   
        $str .= '<div align="center">';
        $str .= '<table>';

        foreach ($fields as $cfield){

            $str .= '<tr><td valign="top" align="right">';
            $str .= $cfield->name.':';
            $str .= '</td>';

            $str .='<td>';
            $str .= '[['.$cfield->name.']]';
            $str .= '</td></tr>';
            unset($g);
            
        }
        if ($mode!='addtemplate' and $mode!='rsstemplate'){    //if not adding, we put tags in there
            $str .= '<tr><td align="center" colspan="2">##Edit##  ##More##  ##Delete##  ##Approve##</td></tr>';
        }

        $str .= '</table>';
        $str .= '</div>';

        if ($mode == 'listtemplate'){
            $str .= '<br />';
        }
        $data->{$mode} = $str;
        //make the header and footer for listtempalte
        update_record('data', $data);
    }
}


/*********************************************************
 * generates an empty add form, if there is none.        *
 * input: @(int)id, id of the data                       *
 * output: null                                          *
 *********************************************************/
function data_generate_empty_add_form($id, $rid=0){

    $currentdata = get_record('data','id',$id);
    //check if there is an add entry
    if (!$currentdata->addtemplate){
        echo '<div align="center">'.get_string('emptyadd', 'data').'</div><p></p>';
        //get all the field entry, and print studnet version

        if ($fields = get_records('data_fields','dataid',$currentdata->id)){
            $str = '';    //the string to write to the given $data->{$mode}
            //this only applies to add and single template

            $str .= '<div align="center">';
            $str .= '<table><tr>';

            foreach ($fields as $cfield){

                $str .= '<td valign="top" align="right">';
                $str .= $cfield->name.':';
                $str .= '</td>';

                $str .='<td valign="top">';
                $g = data_get_field($cfield);
                $str .= $g->display_add_field($cfield->id,$rid);
                $str .= '</td>';
                unset($g);
            }

            $str .= '</tr></table>';
            $str .= '</div><p></p>';

        }

        echo $str;
    }
}


/***********************************************************************
 * Search for a field name and replaces it with another one in all the *
 * form templates. Set $newfieldname as '' if you want to delete the   *
 * field from the form.                                                *
 ***********************************************************************/
function data_replace_field_in_forms($dataid, $searchfieldname, $newfieldname) {
    if (!$data = get_record('data', 'id', $dataid)) {
        // Form does not exist.
        return false;
    }
    if (!empty($newfieldname)) {
        $prestring = '[[';
        $poststring = ']]';
    }
    else {
        $prestring = '';
        $poststring = '';
    }
    
    $data->singletemplate = str_replace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->singletemplate);
    
    $data->listtemplate = str_replace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->listtemplate);
    
    $data->addtemplate = str_replace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->addtemplate);
    
    $data->rsstemplate = str_replace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->rsstemplate);
    
    update_record('data', $data);
}


/********************************************************
 * Appends a new field at the end of the form template. *
 ********************************************************/
function data_append_field_in_form($dataid, $newfieldname) {
    if (!$data = get_record('data', 'id', $dataid)) {
        // Form does not exist.
        return false;
    }
    if (!empty($data->singletemplate)) {
        $data->singletemplate .= ' [[' . $newfieldname .']]';
    }
    if (!empty($data->rsstemplate)) {
        $data->rsstemplate .= ' [[' . $newfieldname . ']]';
    }
    update_record('data', $data);
}


/************************************************************************
 * give a name in the format of field_## where ## is the field id,      *
 * this function creates an instance of the particular subfield class   *
 ************************************************************************/
function data_get_field_from_name($name){
    $namestring = explode('_',$name);
    $fieldid = $namestring[1];//the one after _ is the id.
    $field = get_record('data_fields','id',$fieldid);

    if ($field){
        return data_get_field($field);
    } else {
        return false;
    }
}

/************************************************************************
 * returns a subclass field object given a record of the field, used to *
 * invoke plugin methods                                                *
 * input: $param $field - record from db                                *
 ************************************************************************/
function data_get_field($field){
    if ($field){
        require_once('field/'.$field->type.'/field.class.php');
        $newfield = 'data_field_'.$field->type;
        $newfield = new $newfield($field->id);
        return $newfield;
    }
}


/***************************************************************************
 * given record id, returns true if the record belongs to the current user *
 * input @param $rid - record id                                           *
 * output bool                                                             *
 ***************************************************************************/
function data_isowner($rid){
    global $USER;

    if (empty($USER->id)) {
        return false;
    }

    if ($record = get_record('data_records','id',$rid)) {
        return ($record->userid == $USER->id);
    }

    return false;
}

/***********************************************************************
 * has a user reached the max number of entries?                       *
 * input object $data                                                  *
 * output bool                                                         *
 ***********************************************************************/
function data_atmaxentries($data){
    if (!$data->maxentries){
        return false;

    } else {
        return (data_numentries($data) >= $data->maxentries);
    }
}

/**********************************************************************
 * returns the number of entries already made by this user            *
 * input @param object $data                                          *
 * uses global $CFG, $USER                                            *
 * output int                                                         *
 **********************************************************************/
function data_numentries($data){
    global $USER;
    global $CFG;
    $sql = 'SELECT COUNT(*) FROM '.$CFG->prefix.'data_records WHERE dataid='.$data->id.' AND userid='.$USER->id;
    return count_records_sql($sql);
}

/****************************************************************
 * function that takes in a dataid and adds a record            *
 * this is used everytime an add template is submitted          *
 * input @param int $dataid, $groupid                           *
 * output bool                                                  *
 ****************************************************************/
function data_add_record($dataid, $groupid=0){
    global $USER, $course;
    $record->userid = $USER->id;
    $record->groupid = $groupid;
    $record->dataid = $dataid;
    $record->timecreated = time();
    $record->timemodified = time();
    if (isteacher($course->id)) {
        $record->approved = 1;
    } else {
        $record->approved = 0;
    }
    return insert_record('data_records',$record);
}

/*******************************************************************
 * check the multple existence any tag in a template               *
 * input @param string                                             *
 * output true-valid, false-invalid                                *
 * check to see if there are 2 or more of the same tag being used. *
 * input @param int $dataid,                                       *
 *       @param string $template                                   *
 * output bool                                                     *
 *******************************************************************/
function data_tags_check($dataid, $template){
    //first get all the possible tags
    $possiblefields = get_records('data_fields','dataid',$dataid);
    ///then we generate strings to replace
    $tagsok = true; //let's be optimistic
    foreach ($possiblefields as $cfield){
        $pattern="/\[\[".$cfield->name."\]\]/i";
        if (preg_match_all($pattern, $template, $dummy)>1){
            $tagsok = false;
            notify ('[['.$cfield->name.']] - '.get_string('multipletags','data'));
        }
    }
    //else return true
    return $tagsok;
}

/************************************************************************
 * Adds an instance of a data                                           *
 ************************************************************************/
function data_add_instance($data) {
    global $CFG;

    if (empty($data->ratings)) {
        $data->ratings = 0;
    }

    $data->timemodified = time();

    if (!empty($data->availablefromenable)) {
        $data->timeavailablefrom  = make_timestamp($data->availablefromyear, $data->availablefrommonth, $data->availablefromday,
                                                  $data->availablefromhour, $data->availablefromminute, 0);
    } else {
        $data->timeavailablefrom = 0;
    }

    if (!empty($data->availabletoenable)) {
        $data->timeavailableto  = make_timestamp($data->availabletoyear, $data->availabletomonth, $data->availabletoday,
                                                 $data->availabletohour, $data->availabletominute, 0);
    } else {
        $data->timeavailableto = 0;
    }

    if (!empty($data->viewfromenable)) {
        $data->timeviewfrom  = make_timestamp($data->viewfromyear, $data->viewfrommonth, $data->viewfromday,
                                              $data->viewfromhour, $data->viewfromminute, 0);
    } else {
        $data->timeviewfrom = 0;
    }
 
    if (!empty($data->viewtoenable)) {
        $data->timeviewto  = make_timestamp($data->viewtoyear, $data->viewtomonth, $data->viewtoday,
                                              $data->viewtohour, $data->viewtominute, 0);
    } else {
        $data->timeviewto = 0;
    }


    if (! $data->id = insert_record('data', $data)) {
        return false;
    }

    return $data->id;
}

/************************************************************************
 * updates an instance of a data                                        *
 ************************************************************************/
function data_update_instance($data) {
    global $CFG;
    
    $data->id = $data->instance;
    
    if (empty($data->ratings)) {
        $data->ratings = 0;
    }
    
    $data->timemodified = time();

    if (!empty($data->availablefromenable)) {
        $data->timeavailablefrom  = make_timestamp($data->availablefromyear, $data->availablefrommonth, $data->availablefromday,
                                                  $data->availablefromhour, $data->availablefromminute, 0);
    } else {
        $data->timeavailablefrom = 0;
    }

    if (!empty($data->availabletoenable)) {
        $data->timeavailableto  = make_timestamp($data->availabletoyear, $data->availabletomonth, $data->availabletoday,
                                                 $data->availabletohour, $data->availabletominute, 0);
    } else {
        $data->timeavailableto = 0;
    }

    if (!empty($data->viewfromenable)) {
        $data->timeviewfrom  = make_timestamp($data->viewfromyear, $data->viewfrommonth, $data->viewfromday,
                                              $data->viewfromhour, $data->viewfromminute, 0);
    } else {
        $data->timeviewfrom = 0;
    }
 
    if (!empty($data->viewtoenable)) {
        $data->timeviewto  = make_timestamp($data->viewtoyear, $data->viewtomonth, $data->viewtoday,
                                              $data->viewtohour, $data->viewtominute, 0);
    } else {
        $data->timeviewto = 0;
    }
    
    if (! $data->instance = update_record('data', $data)) {
        return false;
    }
    return $data->instance;
    
}

/************************************************************************
 * deletes an instance of a data                                        *
 ************************************************************************/
function data_delete_instance($id) {    //takes the dataid

    global $CFG;

    if (! $data = get_record('data', 'id', $id)) {
        return false;
    }

    /// Delete all the associated information

    // get all the records in this data
    $sql = 'SELECT c.* FROM '.$CFG->prefix.'data_records as r LEFT JOIN '.
           $CFG->prefix.'data_content as c ON c.recordid = r.id WHERE r.dataid = '.$id;
    
    if ($contents = get_records_sql($sql)){

        foreach($contents as $content){
            
            $field = get_record('data_fields','id',$content->fieldid);
            if ($g = data_get_field($field)){
                $g->delete_data_content_files($id, $content->recordid, $content->content);
            }
            //delete the content itself
            delete_records('data_content','id', $content->id);
        }
    }

    // delete all the records and fields
    delete_records('data_records', 'dataid', $id);
    delete_records('data_fields','dataid',$id);

    // Delete the instance itself

    if (! delete_records('data', 'id', $id)) {
        return false;
    }
    return true;
}

/************************************************************************
 * returns a summary of data activity of this user                      *
 ************************************************************************/
function data_user_outline($course, $user, $mod, $data) {

    global $USER, $CFG;
    
    $sql = 'SELECT * from '.$CFG->prefix.'data_records WHERE dataid = "'.$data->id.'" AND userid = "'.$USER->id.'" ORDER BY timemodified DESC';
    
    if ($records = get_records_sql($sql)){
        $result->info = count($records).' '.get_string('numrecords', 'data');
        $lastrecord = array_pop($records);
        $result->time = $lastrecord->timemodified;
        return $result;
    }
    return NULL;

}

/************************************************************************
 * Prints all the records uploaded by this user                         *
 ************************************************************************/
function data_user_complete($course, $user, $mod, $data) {

    global $USER, $CFG;

    $sql = 'SELECT * from '.$CFG->prefix.'data_records WHERE dataid = "'.
           $data->id.'" AND userid = "'.$USER->id.'" ORDER BY timemodified DESC';

    if ($records = get_records_sql($sql)){

        data_print_template($records, $data, '', 'singletemplate');

    }
}

/************************************************************************
 * returns a list of participants of this database                      *
 ************************************************************************/
function data_get_participants($dataid) {
//Returns the users with data in one resource
//(NONE, byt must exists on EVERY mod !!)
    global $CFG;
    
    $records = get_records_sql('SELECT DISTINCT u.id, u.id from '.$CFG->prefix.
                               'user u, '.$CFG->prefix.'data_records r WHERE r.dataid="'.$dataid.'"
                               AND u.id = r.userid');
    
    $comments = get_records_sql('SELECT DISTINCT u.id, u.id from '.$CFG->prefix.
                                'user u, '.$CFG->prefix.'data_comments c WHERE dataid="'.$dataid.'"
                                AND u.id = c.userid');

    $participants = array();
    
    if ($records){
        foreach ($records as $record) {
            $participants[$record->id] = $record;
        }
    }
    if ($comments){
        foreach ($comments as $comment) {
            $participants[$comment->id] = $comment;
        }
    }
                                  
    return $participants;
}

function data_get_coursemodule_info($coursemodule) {
/// Given a course_module object, this function returns any
/// "extra" information that may be needed when printing
/// this activity in a course listing.
///
/// See get_array_of_activities() in course/lib.php
///

   global $CFG;

   $info = NULL;

   return $info;
}

///junk functions
/************************************************************************
 * takes a list of records, the current data, a search string,          *
 * and mode to display prints the translated template                   *
 * input @param array $records                                          *
 *       @param object $data                                            *
 *       @param string $search                                          *
 *       @param string $template                                        *
 * output null                                                          *
 ************************************************************************/
function data_print_template($records, $data, $search, $template, $sort, $page=0, $rid=0, $order='', $group='', $return=false){
    global $CFG, $course;
    
    foreach ($records as $record) {    //only 1 record for single mode

        //replacing tags
        $patterns = array();
        $replacement = array();
        if ($search || $sort){    //the ids are different for the 2 searches
            $record->id = $record->recordid;
        }

        $possiblefields = get_records('data_fields','dataid',$data->id);
        
        ///then we generate strings to replace for normal tags
        foreach ($possiblefields as $cfield) {
            $patterns[]='/\[\['.$cfield->name.'\]\]/i';
            $g = data_get_field($cfield);
            $replacement[] = highlight($search, $g->display_browse_field($cfield->id, $record->id, $template));
            unset($g);
        }

        $record = get_record('data_records','id',$record->id);
        ///replacing special tags (##Edit##, ##Delete##, ##More##)
        $patterns[]='/\#\#Edit\#\#/i';
        $patterns[]='/\#\#Delete\#\#/i';
        $patterns[]='/\#\#More\#\#/i';
        $patterns[]='/\#\#Approve\#\#/i';

        if (data_isowner($record->id) or isteacheredit($course->id)){
            $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/add.php?d='
                             .$data->id.'&amp;rid='.$record->id.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/edit.gif" height="11" width="11" border="0" alt="'.get_string('edit').'" /></a>';
        }else {
            $replacement[] = '';
        }

        if (data_isowner($record->id) or isteacheredit($course->id)){
            $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/view.php?d='
                             .$data->id.'&amp;delete='.$record->id.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/delete.gif" height="11" width="11" border="0" alt="'.get_string('delete').'" /></a>';
        }else {
            $replacement[] = '';
        }
        $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&amp;rid='.$record->id.'&amp;search='.$search.'&amp;sort='.$sort.'&amp;order='.$order.'&amp;group='.$group.'&amp;"><img src="'.$CFG->pixpath.'/i/search.gif" height="11" width="11" border="0" alt="'.get_string('more').'" /></a>';

        if (isteacher($course->id) && ($data->approval) && (!$record->approved)){
            $replacement[] = data_print_approve_button($record->id, $data->id, $page, $rid, $search, $sort, $order);
        }else {
            $replacement[] = '';
        }

        ///actual replacement of the tags
        $newtext = preg_replace($patterns, $replacement, $data->{$template});
        
        if ($return) {
            return $newtext;
        }
        else {
            echo $newtext;    //prints the template with tags replaced
        }
        //if this record is not yet approved, and database requires approval, print silly button
    }
}


/************************************************************************
 * function that takes in the current data, number of items per page,   *
 * a search string and prints a preference box in view.php              *
 * input @param object $data                                            *
 *       @param int $perpage                                            *
 *       @param string $search                                          *
 * output null                                                          *
 ************************************************************************/
function data_print_preference_form($data, $perpage, $search, $sort='', $order='ASC'){
    echo '<br />';
    echo '<form name="options" action="view.php?d='.$data->id.'&amp;search='.s($search).'&amp;sort='.s($sort).'&amp;order='.s($order).'" method="post">';
    echo '<input type="hidden" id="updatepref" name="updatepref" value="1" />';
    echo '<table id="sortsearch" align="center">';
    echo '<tr>'.
         '<td class="c0 r1"><label for="perpage">'.get_string('pagesize','data').':</label></td>';
    echo '<td class="c1 r1">';
    $pagesizes = array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,15=>15,
                       20=>20,30=>30,40=>40,50=>50,100=>100,200=>200,300=>300,400=>400,500=>500,1000=>1000);
    choose_from_menu($pagesizes,'perpage1',$perpage,'choose','','0');
    echo '</td></tr>';
    echo '<tr>'.
         '<td class="c0 r0">'.get_string('search').':</td>'.
         '<td class="c1 r0"><input type="text" size="16" name="search" value="'.s($search).'" /></td>'.
         '</tr>';
    echo '<tr>';
    echo '<tr><td class="c0 r2">';
    echo get_string('sortby').':';
    echo '</td><td class="c1 r2">';
    //foreach field, print the option
    $fields = get_records('data_fields','dataid',$data->id);
    echo '<select name="sort"><option value="0">'.get_string('dateentered','data').'</option>';
    foreach ($fields as $field) {
        if ($field->id == $sort) {
            echo '<option value="'.$field->id.'" selected="selected">'.$field->name.'</option>';
        } else {
            echo '<option value="'.$field->id.'">'.$field->name.'</option>';
        }
    }
    echo '</select>';
    echo '<select name="order">';
    if ($order == "ASC") {
        echo '<option value="ASC" SELECTED>'.get_string('ascending','data').'</option>';
    } else {
        echo '<option value="ASC">'.get_string('ascending','data').'</option>';
    }
    if ($order == "DESC") {
        echo '<option value="DESC" SELECTED>'.get_string('descending','data').'</option>';
    } else {
        echo '<option value="DESC">'.get_string('descending','data').'</option>';
    }
    //print ASC or DESC
    echo '</td></tr><tr>';
    echo '<td colspan="2" align="center" class="r3">';
    echo '<input type="submit" value="'.get_string('savesettings').'" />';
    echo '</td></tr></table>';
    echo '</form>';
}

//silly function that prints a button
function data_print_approve_button($recordid, $d, $page='0', $rid='0', $search='', $sort='', $order='') {
    $str= '<div align="center"><form action="approve.php" method="GET">';
    $str.=  '<input type="hidden" name="d" value="'.$d.'" />';
    $str.= '<input type="hidden" name="rid" value="'.$rid.'" />';
    $str.= '<input type="hidden" name="page" value="'.$page.'" />';
    $str.= '<input type="hidden" name="search" value="'.$search.'" />';
    $str.= '<input type="hidden" name="sort" value="'.$sort.'" />';
    $str.= '<input type="hidden" name="order" value="'.$order.'" />';
    $str.= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    $str.= '<input type="hidden" name="recordid" value="'.$recordid.'" />';
    $str.= '<input type="submit" value="'.get_string('approve').'" />';
    $str.= '</form></div>';
    return $str;
}

//silly function that approves a record
function data_approve_record($recordid) {
    $record = get_record('data_records','id',$recordid);
    $record->approved = 1;
    update_record('data_records',$record);
}
?>
