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
define ('DATA_TEACHERS_ONLY', 1);
define ('DATA_STUDENTS_ONLY', 2);
define ('DATA_TEACHERS_AND_STUDENTS', 3);
define ('DATA_MAX_ENTRIES', 50);
define ('DATA_PERPAGE_SINGLE', 1);

class data_field_base {     /// Base class for Database Field Types (see field/*/field.class.php)

    var $type = 'unknown';  /// Subclasses must override the type with their name
    var $data = NULL;       /// The database object that this field belongs to
    var $field = NULL;      /// The field object itself, if we know it

    var $iconwidth = 16;    /// Width of the icon for this fieldtype
    var $iconheight = 16;   /// Width of the icon for this fieldtype


/// Constructor function
    function data_field_base($field=0, $data=0) {   // Field or data or both, each can be id or object

        if (empty($field) && empty($data)) {
            error('Programmer error: You must specify field and/or data when defining field class. ');
        }

        if (!empty($field)) {
            if (is_object($field)) {
                $this->field = $field;  // Programmer knows what they are doing, we hope
            } else if (!$this->field = get_record('data_fields','id',$field)) {
                error('Bad field ID encountered: '.$field);
            }
            if (empty($data)) {
                if (!$this->data = get_record('data','id',$this->field->dataid)) {
                    error('Bad data ID encountered in field data');
                }
            }
        }

        if (empty($this->data)) {         // We need to define this properly
            if (!empty($data)) {
                if (is_object($data)) {
                    $this->data = $data;  // Programmer knows what they are doing, we hope
                } else if (!$this->data = get_record('data','id',$data)) {
                    error('Bad data ID encountered: '.$data);
                }
            } else {                      // No way to define it!
                error('Data id or object must be provided to field class');
            }
        }

        if (empty($this->field)) {         // We need to define some default values
            $this->define_default_field();
        }
    }

    
/// This field just sets up a default field object
    function define_default_field() {
        if (empty($this->data->id)) {
            notify('Programmer error: dataid not defined in field class');
        }
        $this->field = new object;
        $this->field->id = 0;
        $this->field->dataid = $this->data->id;
        $this->field->type   = $this->type;
        $this->field->param1 = '';
        $this->field->param2 = '';
        $this->field->param3 = '';
        $this->field->name = '';
        $this->field->description = '';

        return true;
    }

/// Set up the field object according to data in an object.  Now is the time to clean it!
    function define_field($data) {
        $this->field->type        = $this->type;
        $this->field->dataid      = $this->data->id;

        $this->field->name        = trim($data->name);
        $this->field->description = trim($data->description);

        if (isset($data->param1)) {
            $this->field->param1 = trim($data->param1);
        }
        if (isset($data->param2)) {
            $this->field->param2 = trim($data->param2);
        }
        if (isset($data->param3)) {
            $this->field->param3 = trim($data->param3);
        }
        if (isset($data->param4)) {
            $this->field->param4 = trim($data->param4);
        }
        if (isset($data->param5)) {
            $this->field->param5 = trim($data->param5);
        }

        return true;
    }
    
/// Insert a new field in the database
/// We assume the field object is already defined as $this->field
    function insert_field() {
        if (empty($this->field)) {
            notify('Programmer error: Field has not been defined yet!  See define_field()');
            return false;
        }

        if (!$this->field->id = insert_record('data_fields',$this->field)){
            notify('Insertion of new field failed!');
            return false;
        }
        return true;
    }


/// Update a field in the database
    function update_field() {
        if (!update_record('data_fields', $this->field)) {
            notify('updating of new field failed!');
            return false;
        }
        return true;
    }

/// Delete a field completely
    function delete_field() {
        if (!empty($this->field->id)) {
            delete_records('data_fields', 'id', $this->field->id);
            $this->delete_content();
        }
        return true;
    }

/// Print the relevant form element in the ADD template for this field
    function display_add_field($recordid=0){
        if ($recordid){
            $content = get_field('data_content', 'content', 'fieldid', $this->field->id, 'recordid', $recordid);
        } else {
            $content = '';
        }
        
        $str = '<div title="'.$this->field->description.'">';
        $str .= '<input style="width:300px;" type="text" name="field_'.$this->field->id.'" id="field_'.$this->field->id.'" value="'.s($content).'" />';
        $str .= '</div>';

        return $str;
    }

/// Print the relevant form element to define the attributes for this field
/// viewable by teachers only.
    function display_edit_field() {
        global $CFG;

        if (empty($this->field)) {   // No field has been defined yet, try and make one
            $this->define_default_field();
        }
        print_simple_box_start('center','80%');

        echo '<form name="editfield" action="'.$CFG->wwwroot.'/mod/data/field.php" method="post">'."\n";
        echo '<input type="hidden" name="d" value="'.$this->data->id.'" />'."\n";
        if (empty($this->field->id)) {
            echo '<input type="hidden" name="mode" value="add" />'."\n";
            $savebutton = get_string('add');
        } else {
            echo '<input type="hidden" name="fid" value="'.$this->field->id.'" />'."\n";
            echo '<input type="hidden" name="mode" value="update" />'."\n";
            $savebutton = get_string('savechanges');
        }
        echo '<input type="hidden" name="type" value="'.$this->type.'" />'."\n";
        echo '<input name="sesskey" value="'.sesskey().'" type="hidden" />'."\n";
   
        print_heading($this->name());

        require_once($CFG->dirroot.'/mod/data/field/'.$this->type.'/mod.html');

        echo '<div align="center">';
        echo '<input type="submit" value="'.$savebutton.'" />'."\n";
        echo '<input type="submit" name="cancel" value="'.get_string('cancel').'" />'."\n";
        echo '</div">';

        echo '</form>';

        print_simple_box_end();
    }
    
/// Display the content of the field in browse mode
    function display_browse_field($recordid, $template) {
        if ($content = get_record('data_content','fieldid', $this->field->id, 'recordid', $recordid)) {
            if (isset($content->content)) {                
                if ($this->field->param1 == '1') {  // We are autolinking this field, so disable linking within us
                    //$content->content = '<span class="nolink">'.$content->content.'</span>';
                    //$content->content1 = FORMAT_HTML;
                    $options->filter=false;
                }
                $options->para = false;
                $str = format_text($content->content, $content->content1, $options);
            } else {
                $str = '';
            }
            return $str;
        }
        return false;
    }
    
/// Update the content of one data field in the data_content table
    function update_content($recordid, $value, $name=''){
        $content = new object;
        $content->fieldid = $this->field->id;
        $content->recordid = $recordid;
        $content->content = clean_param($value, PARAM_NOTAGS);

        if ($oldcontent = get_record('data_content','fieldid', $this->field->id, 'recordid', $recordid)) {
            $content->id = $oldcontent->id;
            return update_record('data_content', $content);
        } else {
            return insert_record('data_content', $content);
        }
    }
    
/// Delete all content associated with the field
    function delete_content($recordid=0) {

        $this->delete_content_files($recordid);
        
        if ($recordid) {
            return delete_records('data_content', 'fieldid', $this->field->id, 'recordid', $recordid);
        } else {
            return delete_records('data_content', 'fieldid', $this->field->id);
        }
    }

/// Deletes any files associated with this field
    function delete_content_files($recordid='') {
        global $CFG;

        require_once($CFG->libdir.'/filelib.php');

        $dir = $CFG->dataroot.'/'.$this->data->course.'/'.$CFG->moddata.'/data/'.$this->data->id.'/'.$this->field->id;
        if ($recordid) {
            $dir .= '/'.$recordid;
        }

        return fulldelete($dir);
    }
    

/// Check if a field from an add form is empty
    function notemptyfield($value, $name) {
        return !empty($value);
    }
    
/// Just in case a field needs to print something before the whole form
    function print_before_form() {
    }

/// Just in case a field needs to print something after the whole form
    function print_after_form() {
    }
    
    
/// Returns the sortable field for the content. By default, it's just content
/// but for some plugins, it could be content 1 - content4
    function get_sort_field() {
        return 'content';
    }

/// Returns the SQL needed to refer to the column.  Some fields may need to CAST() etc.
    function get_sort_sql($fieldname) {
        return $fieldname;
    }

/// Returns the name/type of the field
    function name(){
        return get_string('name'.$this->type, 'data');
    }
    
/// Prints the respective type icon
    function image() {
        global $CFG;

        $str = '<a href="field.php?d='.$this->data->id.'&amp;fid='.$this->field->id.'&amp;mode=display&amp;sesskey='.sesskey().'">';
        $str .= '<img src="'.$CFG->modpixpath.'/data/field/'.$this->type.'/icon.gif" ';
        $str .= 'height="'.$this->iconheight.'" width="'.$this->iconwidth.'" border="0" alt="'.$this->type.'" title="'.$this->type.'" /></a>';
        return $str;
    }

    
}  //end of major class data_field_base



/*****************************************************************************
/* Given a template and a dataid, generate a default case template               *
 * input @param template - addtemplate, singletemplate, listtempalte, rsstemplate*
 *       @param dataid                                                       *
 * output null                                                               *
 *****************************************************************************/
function data_generate_default_template(&$data, $template, $recordid=0, $form=false, $update=true) {

    if (!$data && !$template) {
        return false;
    }
    if ($template == 'csstemplate') {
        return '';
    }
    
    //get all the fields for that database
    if ($fields = get_records('data_fields', 'dataid', $data->id, 'id')) {
   
        $str = '<div align="center">';
        $str .= '<table>';

        foreach ($fields as $field) {

            $str .= '<tr><td valign="top" align="right">';
            $str .= $field->name.':';
            $str .= '</td>';

            $str .='<td>';
            if ($form) {   /// Print forms instead of data
                $fieldobj = data_get_field($field, $data);
                $str .= $fieldobj->display_add_field($recordid);

            } else {           /// Just print the tag
                $str .= '[['.$field->name.']]';
            }
            $str .= '</td></tr>';
            
        }
        if ($template != 'addtemplate' and $template != 'rsstemplate') {    //if not adding, we put tags in there
            $str .= '<tr><td align="center" colspan="2">##Edit##  ##More##  ##Delete##  ##Approve##</td></tr>';
        }

        $str .= '</table>';
        $str .= '</div>';

        if ($template == 'listtemplate'){
            $str .= '<hr />';
        }

        if ($update) {
            $newdata->id = $data->id;
            $newdata->{$template} = $str;
            if (!update_record('data', $newdata)) {
                notify('Error updating template');
            } else {
                $data->{$template} = $str;
            }
        }

        return $str;
    }
}


/***********************************************************************
 * Search for a field name and replaces it with another one in all the *
 * form templates. Set $newfieldname as '' if you want to delete the   *
 * field from the form.                                                *
 ***********************************************************************/
function data_replace_field_in_templates($data, $searchfieldname, $newfieldname) {
    if (!empty($newfieldname)) {
        $prestring = '[[';
        $poststring = ']]';

    } else {
        $prestring = '';
        $poststring = '';
    }
    
    $newdata->id = $data->id;
    $newdata->singletemplate = addslashes(str_replace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->singletemplate));
    
    $newdata->listtemplate = addslashes(str_replace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->listtemplate));
    
    $newdata->addtemplate = addslashes(str_replace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->addtemplate));
    
    $newdata->rsstemplate = addslashes(str_replace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->rsstemplate));
    
    return update_record('data', $newdata);
}


/********************************************************
 * Appends a new field at the end of the form template. *
 ********************************************************/
function data_append_new_field_to_templates($data, $newfieldname) {

    $newdata->id = $data->id;
    $change = false;

    if (!empty($data->singletemplate)) {
        $newdata->singletemplate = addslashes($data->singletemplate.' [[' . $newfieldname .']]');
        $change = true;
    }
    if (!empty($data->addtemplate)) {
        $newdata->addtemplate = addslashes($data->addtemplate.' [[' . $newfieldname . ']]');
        $change = true;
    }
    if (!empty($data->rsstemplate)) {
        $newdata->rsstemplate = addslashes($data->singletemplate.' [[' . $newfieldname . ']]');
        $change = true;
    }
    if ($change) {
        update_record('data', $newdata);
    }
}


/************************************************************************
 * given a field name *
 * this function creates an instance of the particular subfield class   *
 ************************************************************************/
function data_get_field_from_name($name, $data){
    $field = get_record('data_fields','name',$name);

    if ($field) {
        return data_get_field($field, $data);
    } else {
        return false;
    }
}

/************************************************************************
 * given a field id *
 * this function creates an instance of the particular subfield class   *
 ************************************************************************/
function data_get_field_from_id($fieldid, $data){
    $field = get_record('data_fields','id',$fieldid);

    if ($field) {
        return data_get_field($field, $data);
    } else {
        return false;
    }
}

/************************************************************************
 * given a field id *
 * this function creates an instance of the particular subfield class   *
 ************************************************************************/
function data_get_field_new($type, $data) {
    global $CFG;

    require_once($CFG->dirroot.'/mod/data/field/'.$type.'/field.class.php');
    $newfield = 'data_field_'.$type;
    $newfield = new $newfield(0, $data);
    return $newfield;
}

/************************************************************************
 * returns a subclass field object given a record of the field, used to *
 * invoke plugin methods                                                *
 * input: $param $field - record from db                                *
 ************************************************************************/
function data_get_field($field, $data) {
    global $CFG;

    if ($field) {
        require_once('field/'.$field->type.'/field.class.php');
        $newfield = 'data_field_'.$field->type;
        $newfield = new $newfield($field, $data);
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
function data_add_record($data, $groupid=0){
    global $USER;

    $record->userid = $USER->id;
    $record->dataid = $data->id;
    $record->groupid = $groupid;
    $record->timecreated = $record->timemodified = time();
    if (isteacher($data->course)) {
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
    $fields = get_records('data_fields','dataid',$dataid);
    ///then we generate strings to replace
    $tagsok = true; //let's be optimistic
    foreach ($fields as $field){
        $pattern="/\[\[".$field->name."\]\]/i";
        if (preg_match_all($pattern, $template, $dummy)>1){
            $tagsok = false;
            notify ('[['.$field->name.']] - '.get_string('multipletags','data'));
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
            if ($g = data_get_field($field, $data)){
                $g->delete_content_files($id, $content->recordid, $content->content);
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

    global $CFG;

    if ($countrecords = count_records('data_records', 'dataid', $data->id, 'userid', $user->id)) {
        $result->info = get_string('numrecords', 'data', $countrecords);
        $lastrecord   = get_record_sql('SELECT id,timemodified FROM '.$CFG->prefix.'data_records 
                                         WHERE dataid = '.$data->id.' AND userid = '.$user->id.' 
                                      ORDER BY timemodified DESC', true);
        $result->time = $lastrecord->timemodified;
        return $result;
    }
    return NULL;

}

/************************************************************************
 * Prints all the records uploaded by this user                         *
 ************************************************************************/
function data_user_complete($course, $user, $mod, $data) {

    if ($records = get_records_select('data_records', 'dataid = '.$data->id.' AND userid = '.$user->id, 
                                                      'timemodified DESC')) {

        data_print_template('singletemplate', $records, $data);

    }
}

/************************************************************************
 * returns a list of participants of this database                      *
 ************************************************************************/
function data_get_participants($dataid) {
//Returns the users with data in one data
//(users with records in data_records, data_comments and data_ratings)
    global $CFG;

    $records = get_records_sql("SELECT DISTINCT u.id, u.id
                                FROM {$CFG->prefix}user u,
                                     {$CFG->prefix}data_records r
                                WHERE r.dataid = '$dataid'
                                  AND u.id = r.userid");

    $comments = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}data_records r,
                                      {$CFG->prefix}data_comments c
                                 WHERE r.dataid = '$dataid'
                                   AND u.id = r.userid 
                                   AND r.id = c.recordid");

    $ratings = get_records_sql("SELECT DISTINCT u.id, u.id
                                FROM {$CFG->prefix}user u,
                                     {$CFG->prefix}data_records r,
                                     {$CFG->prefix}data_ratings a
                                WHERE r.dataid = '$dataid'
                                  AND u.id = r.userid 
                                  AND r.id = a.recordid");
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
    if ($ratings){
        foreach ($ratings as $rating) {
            $participants[$rating->id] = $rating;
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
function data_print_template($template, $records, $data, $search='',$page=0, $return=false) {
    global $CFG;

    static $fields = NULL;
    static $isteacher;
    static $dataid = NULL;

    if (empty($dataid)) {
        $dataid = $data->id;
    } else if ($dataid != $data->id) {
        $fields = NULL;
    }

    if (empty($fields)) {
        $fieldrecords = get_records('data_fields','dataid', $data->id);
        foreach ($fieldrecords as $fieldrecord) {
            $fields[]= data_get_field($fieldrecord, $data);
        }
        $isteacher = isteacher($data->course);
    }

    if (empty($records)) {
        return;
    }

    foreach ($records as $record) {   /// Might be just one for the single template

    /// Replacing tags
        $patterns = array();
        $replacement = array();

    /// Then we generate strings to replace for normal tags
        foreach ($fields as $field) {
            $patterns[]='/\[\['.$field->field->name.'\]\]/i';
            $replacement[] = highlight($search, $field->display_browse_field($record->id, $template));
        }

    /// Replacing special tags (##Edit##, ##Delete##, ##More##)
        $patterns[]='/\#\#Edit\#\#/i';
        $patterns[]='/\#\#Delete\#\#/i';
        if ($isteacher or data_isowner($record->id)) {
            $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/edit.php?d='
                             .$data->id.'&amp;rid='.$record->id.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/edit.gif" height="11" width="11" border="0" alt="'.get_string('edit').'" /></a>';
            $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/view.php?d='
                             .$data->id.'&amp;delete='.$record->id.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/delete.gif" height="11" width="11" border="0" alt="'.get_string('delete').'" /></a>';
        } else {
            $replacement[] = '';
            $replacement[] = '';
        }
        $patterns[]='/\#\#More\#\#/i';
        $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&amp;rid='.$record->id.'"><img src="'.$CFG->pixpath.'/i/search.gif" height="11" width="11" border="0" alt="'.get_string('more').'" /></a>';

        $patterns[]='/\#\#MoreURL\#\#/i';
        $replacement[] = $CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&amp;rid='.$record->id;

        $patterns[]='/\#\#User\#\#/i';
        $replacement[] = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$record->userid.
                               '&amp;course='.$data->course.'">'.fullname($record).'</a>';

        $patterns[]='/\#\#Approve\#\#/i';
        if ($isteacher && ($data->approval) && (!$record->approved)){
            $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&amp;approve='.$record->id.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->wwwroot.'/mod/glossary/check.gif" height="11" width="11" border="0" alt="'.get_string('approve').'" /></a>';
        } else {
            $replacement[] = '';
        }
        
        $patterns[]='/\#\#Comments\#\#/i';
        if (($template == 'listtemplate') && ($data->comments)) {
            $comments = count_records('data_comments','recordid',$record->id);
            $replacement[] = '<a href="view.php?rid='.$record->id.'#comments">'.get_string('commentsn','data', $comments).'</a>';
        } else {
            $replacement[] = '';
        }

        ///actual replacement of the tags
        $newtext = preg_replace($patterns, $replacement, $data->{$template});
        $options->para=false;
        $options->noclean=true;
        if ($return) {
            return format_text($newtext, FORMAT_HTML, $options);
        } else {
            echo format_text($newtext, FORMAT_HTML, $options); 
        }

        /**********************************
         *    Printing Ratings Form       *
         *********************************/
        if ($template == 'singletemplate') {    //prints ratings options
            data_print_ratings($data, $record);
        }

        /**********************************
         *    Printing Ratings Form       *
         *********************************/
        if (($template == 'singletemplate') && ($data->comments)) {    //prints ratings options
            data_print_comments($data, $record, $page);
        }

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
    echo '<br /><div class="datapreferences" align="center">';
    echo '<form name="options" action="view.php" method="get">';
    echo '<input type="hidden" name="d" value="'.$data->id.'" />';
    echo get_string('pagesize','data').':';
    $pagesizes = array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,15=>15,
                       20=>20,30=>30,40=>40,50=>50,100=>100,200=>200,300=>300,400=>400,500=>500,1000=>1000);
    choose_from_menu($pagesizes, 'perpage', $perpage, 'choose', '', '0');
    echo '&nbsp;'.get_string('search').': <input type="text" size="16" name="search" value="'.s($search).'" />';
    echo '&nbsp;'.get_string('sortby').':';
    //foreach field, print the option
    $fields = get_records('data_fields','dataid',$data->id, 'name');
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
    if ($order == 'ASC') {
        echo '<option value="ASC" selected="selected">'.get_string('ascending','data').'</option>';
    } else {
        echo '<option value="ASC">'.get_string('ascending','data').'</option>';
    }
    if ($order == 'DESC') {
        echo '<option value="DESC" selected="selected">'.get_string('descending','data').'</option>';
    } else {
        echo '<option value="DESC">'.get_string('descending','data').'</option>';
    }
    echo '</select>';
    //print ASC or DESC
    echo '<input type="submit" value="'.get_string('savesettings','data').'" />';
    echo '</form>';
    echo '</div>';
}

function data_print_ratings($data, $record) {
    global $USER;

    $ratingsmenuused = false;
    if ($data->ratings and !empty($USER->id)) {
        if ($ratings->scale = make_grades_menu($data->scale)) {
            $ratings->assesspublic = $data->assesspublic;
            $ratings->allow = (($data->assessed != 2 or isteacher($data->course)) && !isguest());
            if ($ratings->allow) {
                echo '<div class="ratings" align="center">';
                echo '<form name="form" method="post" action="rate.php">';
                $useratings = true;

                if ($useratings) {
                    if ((isteacher($data->course) or $ratings->assesspublic) and !data_isowner($record->id)) {
                        data_print_ratings_mean($record->id, $ratings->scale, isteacher($data->course));
                        if (!empty($ratings->allow)) {
                            echo '&nbsp;';
                            data_print_rating_menu($record->id, $USER->id, $ratings->scale);
                            $ratingsmenuused = true;
                        }

                    } else if (data_isowner($record->id)) {
                        data_print_ratings_mean($record->id, $ratings->scale, true);

                    } else if (!empty($ratings->allow) ) {
                        data_print_rating_menu($record->id, $USER->id, $ratings->scale);
                        $ratingsmenuused = true;
                    }
                }

                if ($data->scale < 0) {
                    if ($scale = get_record("scale", "id", abs($data->scale))) {
                        print_scale_menu_helpbutton($data->course, $scale );
                    }
                }

                if ($ratingsmenuused) {
                    echo '<input type="hidden" name="id" value="'.$data->course.'" />';
                    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
                    echo "<input type=\"submit\" value=\"".get_string("sendinratings", "data")."\" />";
                }
                echo "</form>";
                echo '</div>';
            }
        }
    }
}

function data_print_ratings_mean($recordid, $scale, $link=true) {
/// Print the multiple ratings on a post given to the current user by others.
/// Scale is an array of ratings

    static $strrate;

    $mean = data_get_ratings_mean($recordid, $scale);

    if ($mean !== "") {

        if (empty($strratings)) {
            $strratings = get_string("ratings", "data");
        }

        echo "$strratings: ";
        if ($link) {
            link_to_popup_window ("/mod/data/report.php?id=$recordid", "ratings", $mean, 400, 600);
        } else {
            echo "$mean ";
        }
    }
}


function data_get_ratings_mean($recordid, $scale, $ratings=NULL) {
/// Return the mean rating of a post given to the current user by others.
/// Scale is an array of possible ratings in the scale
/// Ratings is an optional simple array of actual ratings (just integers)

    if (!$ratings) {
        $ratings = array();
        if ($rates = get_records("data_ratings", "recordid", $recordid)) {
            foreach ($rates as $rate) {
                $ratings[] = $rate->rating;
            }
        }
    }

    $count = count($ratings);

    if ($count == 0) {
        return "";

    } else if ($count == 1) {
        return $scale[$ratings[0]];

    } else {
        $total = 0;
        foreach ($ratings as $rating) {
            $total += $rating;
        }
        $mean = round( ((float)$total/(float)$count) + 0.001);  // Little fudge factor so that 0.5 goes UP

        if (isset($scale[$mean])) {
            return $scale[$mean]." ($count)";
        } else {
            return "$mean ($count)";    // Should never happen, hopefully
        }
    }
}


function data_print_rating_menu($recordid, $userid, $scale) {
/// Print the menu of ratings as part of a larger form.
/// If the post has already been - set that value.
/// Scale is an array of ratings

    static $strrate;

    if (!$rating = get_record("data_ratings", "userid", $userid, "recordid", $recordid)) {
        $rating->rating = 0;
    }

    if (empty($strrate)) {
        $strrate = get_string("rate", "data");
    }

    choose_from_menu($scale, $recordid, $rating->rating, "$strrate...");
}


function data_get_ratings($recordid, $sort="u.firstname ASC") {
/// Returns a list of ratings for a particular post - sorted.
    global $CFG;
    return get_records_sql("SELECT u.*, r.rating
                              FROM {$CFG->prefix}data_ratings r,
                                   {$CFG->prefix}user u
                             WHERE r.recordid = $recordid
                               AND r.userid = u.id
                             ORDER BY $sort");

}


//prints all comments + a text box for adding additional comment
function data_print_comments($data, $record, $page=0) {

    global $CFG;

    echo '<a name="comments"></a>';

    if ($comments = get_records('data_comments','recordid',$record->id)) {
        foreach ($comments as $comment) {
            data_print_comment($data, $comment, $page);
        }
    }
    
    if (isloggedin() and !isguest()) {
        echo '<div class="newcomment" align="center"><form method="post" action="'.$CFG->wwwroot.'/mod/data/comment.php">';
        echo '<input type="hidden" name="mode" value="add" />';
        echo '<input type="hidden" name="page" value="'.$page.'" />';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<input type="hidden" name="rid" value="'.$record->id.'" />';
    
        echo '<textarea rows="8" cols="50" name="commentcontent"></textarea>';
        echo '<br /><input type="submit" value="'.get_string('addcomment','data').'" />';
        echo '</form></div>';
    }
}

//prints a single comment entry
function data_print_comment($data, $comment, $page=0) {

    global $USER, $CFG;
    
    $stredit = get_string('edit');
    $strdelete = get_string('delete');

    $user = get_record('user','id',$comment->userid);

    echo '<table cellspacing="0" align="center" width="50%" class="datacomment forumpost">';

    echo '<tr class="header"><td class="picture left">';
    print_user_picture($comment->userid, $data->course, $user->picture);
    echo '</td>';

    echo '<td class="topic starter" align="left"><div class="author">';
    $fullname = fullname($user, isteacher($comment->userid));
    $by->name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.
                $user->id.'&amp;course='.$data->course.'">'.$fullname.'</a>';
    $by->date = userdate($comment->modified);
    print_string('bynameondate', 'data', $by);
    echo '</div></td></tr>';

    echo '<tr><td class="left side">';
    if ($groups = user_group($data->course, $comment->userid)) {
        print_group_picture($groups, $data->course, false, false, true);
    } else {
        echo '&nbsp;';
    }

/// Actual content

    echo '</td><td class="content" align="left">'."\n";

    // Print whole message
    $options->para = false;
    echo format_text($comment->content, FORMAT_MOODLE, $options);

/// Commands

    echo '<div class="commands">';
    if (data_isowner($comment->recordid) or isteacher($data->course)) {
            echo '<a href="'.$CFG->wwwroot.'/mod/data/comment.php?rid='.$comment->recordid.'&amp;mode=edit&amp;commentid='.$comment->id.'&amp;page='.$page.'">'.$stredit.'</a>';
            echo '| <a href="'.$CFG->wwwroot.'/mod/data/comment.php?rid='.$comment->recordid.'&amp;mode=delete&amp;commentid='.$comment->id.'&amp;page='.$page.'">'.$strdelete.'</a>';
    }

    echo '</div>';

    echo '</td></tr></table>'."\n\n";
}


// For Participantion Reports
function data_get_view_actions() {
    return array('view');
}

function data_get_post_actions() {
    return array('add','update','record delete');
}

function data_fieldname_exists($name, $dataid, $fieldid=0) {
    global $CFG;

    if ($fieldid) { 
        return record_exists_sql("SELECT * from {$CFG->prefix}data_fields AS df 
                                  WHERE df.name LIKE '$name' AND df.dataid = $dataid
                                    AND ((df.id < $fieldid) OR (df.id > $fieldid))");
    } else {
        return record_exists_sql("SELECT * from {$CFG->prefix}data_fields AS df 
                                  WHERE df.name LIKE '$name' AND df.dataid = $dataid");
    }
}

function data_convert_arrays_to_strings(&$fieldinput) {
    foreach ($fieldinput as $key => $val) {
        if (is_array($val)) {
            $str = '';
            foreach ($val as $inner) {
                $str .= $inner . ',';
            }
            $str = substr($str, 0, -1);

            $fieldinput->$key = $str;
        }
    }
}

function data_clean_field_name($fn) {
    $fn = trim($fn);
    //hack from clean_filename - to be replaced by something nicer later
    $fn = preg_replace("/[\\000-\\x2c\\x2f\\x3a-\\x40\\x5b-\\x5e\\x60\\x7b-\\177]/s", '_', $fn);
    $fn = preg_replace("/_+/", '_', $fn);
    $fn = preg_replace("/\.\.+/", '.', $fn);
    return $fn;
}

?>
