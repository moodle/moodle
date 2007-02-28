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

        $str = '<div title="'.s($this->field->description).'">';
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

        echo '<form id="editfield" action="'.$CFG->wwwroot.'/mod/data/field.php" method="post">'."\n";
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
        echo '</div>';

        echo '</form>';

        print_simple_box_end();
    }

/// Display the content of the field in browse mode
    function display_browse_field($recordid, $template) {
        if ($content = get_record('data_content','fieldid', $this->field->id, 'recordid', $recordid)) {
            if (isset($content->content)) {
                $options = new object();
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
        $content = new object();
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
        $str .= 'height="'.$this->iconheight.'" width="'.$this->iconwidth.'" alt="'.$this->type.'" title="'.$this->type.'" /></a>';
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
    if ($template == 'csstemplate' or $template == 'jstemplate' ) {
        return '';
    }

    //get all the fields for that database
    if ($fields = get_records('data_fields', 'dataid', $data->id, 'id')) {

        $str = '<div class="defaulttemplate">';
        $str .= '<table cellpadding="5">';

        foreach ($fields as $field) {

            $str .= '<tr><td valign="top" align="right">';
            // Yu: commenting this out, the id was wrong and will fix later
            //if ($template == 'addtemplate') {
                //$str .= '<label';
                //if (!in_array($field->type, array('picture', 'checkbox', 'date', 'latlong', 'radiobutton'))) {
                //    $str .= ' for="[['.$field->name.'#id]]"';
                //}
                //$str .= '>'.$field->name.'</label>';
                
            //} else {
                $str .= $field->name.': ';
            //}
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
        if ($template == 'listtemplate') {
            $str .= '<tr><td align="center" colspan="2">##edit##  ##more##  ##delete##  ##approve##</td></tr>';
        } else if ($template == 'singletemplate') {
            $str .= '<tr><td align="center" colspan="2">##edit##  ##delete##  ##approve##</td></tr>';
        }

        $str .= '</table>';
        $str .= '</div>';

        if ($template == 'listtemplate'){
            $str .= '<hr />';
        }

        if ($update) {
            $newdata = new object();
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
        $idpart = '#id';

    } else {
        $prestring = '';
        $poststring = '';
        $idpart = '';
    }

    $newdata = new object();
    $newdata->id = $data->id;
    $newdata->singletemplate = addslashes(str_ireplace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->singletemplate));

    $newdata->listtemplate = addslashes(str_ireplace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->listtemplate));

    $newdata->addtemplate = addslashes(str_ireplace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->addtemplate));

    $newdata->addtemplate = addslashes(str_ireplace('[['.$searchfieldname.'#id]]',
            $prestring.$newfieldname.$idpart.$poststring, $data->addtemplate));

    $newdata->rsstemplate = addslashes(str_ireplace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->rsstemplate));

    return update_record('data', $newdata);
}


/********************************************************
 * Appends a new field at the end of the form template. *
 ********************************************************/
function data_append_new_field_to_templates($data, $newfieldname) {

    $newdata = new object();
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

    $cm = get_coursemodule_from_instance('data', $data->id);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $record = new object();
    $record->userid = $USER->id;
    $record->dataid = $data->id;
    $record->groupid = $groupid;
    $record->timecreated = $record->timemodified = time();
    if (has_capability('mod/data:approve', $context)) {
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

    if (empty($data->assessed)) {
        $data->assessed = 0;
    }

    $data->timemodified = time();

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

    if (empty($data->assessed)) {
        $data->assessed = 0;
    }

    $data->timemodified = time();

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
    $sql = 'SELECT c.* FROM '.$CFG->prefix.'data_records r LEFT JOIN '.
           $CFG->prefix.'data_content c ON c.recordid = r.id WHERE r.dataid = '.$id;

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
        $result = new object();
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

    $cm = get_coursemodule_from_instance('data', $data->id);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

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
        $isteacher = has_capability('mod/data:managetemplates', $context);
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
            $patterns[]='[['.$field->field->name.']]';
            $replacement[] = highlight($search, $field->display_browse_field($record->id, $template));
        }

    /// Replacing special tags (##Edit##, ##Delete##, ##More##)
        $patterns[]='##edit##';
        $patterns[]='##delete##';
        if (has_capability('mod/data:manageentries', $context) or data_isowner($record->id)) {
            $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/edit.php?d='
                             .$data->id.'&amp;rid='.$record->id.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
            $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/view.php?d='
                             .$data->id.'&amp;delete='.$record->id.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/delete.gif" class="iconsmall" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
        } else {
            $replacement[] = '';
            $replacement[] = '';
        }
        $patterns[]='##more##';
        $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&amp;rid='.$record->id.'"><img src="'.$CFG->pixpath.'/i/search.gif" class="iconsmall" alt="'.get_string('more', 'data').'" title="'.get_string('more', 'data').'" /></a>';

        $patterns[]='##moreurl##';
        $replacement[] = $CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&amp;rid='.$record->id;

        $patterns[]='##user##';
        $replacement[] = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$record->userid.
                               '&amp;course='.$data->course.'">'.fullname($record).'</a>';

        $patterns[]='##approve##';
        if (has_capability('mod/data:approve', $context) && ($data->approval) && (!$record->approved)){
            $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&amp;approve='.$record->id.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/i/approve.gif" class="iconsmall" alt="'.get_string('approve').'" /></a>';
        } else {
            $replacement[] = '';
        }

        $patterns[]='##comments##';
        if (($template == 'listtemplate') && ($data->comments)) {
            $comments = count_records('data_comments','recordid',$record->id);
            $replacement[] = '<a href="view.php?rid='.$record->id.'#comments">'.get_string('commentsn','data', $comments).'</a>';
        } else {
            $replacement[] = '';
        }

        ///actual replacement of the tags
        $newtext = str_ireplace($patterns, $replacement, $data->{$template});

        /// no more html formatting and filtering - see MDL-6635
        if ($return) {
            return $newtext;
        } else {
            echo $newtext;

            // hack alert - return is always false in singletemplate anyway ;-)
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
}


/************************************************************************
 * function that takes in the current data, number of items per page,   *
 * a search string and prints a preference box in view.php              *
 *                                                                      *
 * This preference box prints a searchable advanced search template if  *
 *     a) A template is defined                                         *
 *  b) The advanced search checkbox is checked.                         *
 *                                                                      *
 * input @param object $data                                            *
 *       @param int $perpage                                            *
 *       @param string $search                                          *
 * output null                                                          *
 ************************************************************************/
function data_print_preference_form($data, $perpage, $search, $sort='', $order='ASC', $search_array = '', $advanced = 0, $mode= ''){
    global $CFG;
    
    $cm = get_coursemodule_from_instance('data', $data->id);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    echo '<br /><div class="datapreferences">';
    echo '<form id="options" action="view.php" method="get">';
    echo '<div>';
    echo '<input type="hidden" name="d" value="'.$data->id.'" />';
    if ($mode =='asearch') {
        $advanced = 1;
        echo '<input type="hidden" name="mode" value="list" />';
    }
    echo '<label for="pref_perpage">'.get_string('pagesize','data').'</label> ';
    $pagesizes = array(2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,15=>15,
                       20=>20,30=>30,40=>40,50=>50,100=>100,200=>200,300=>300,400=>400,500=>500,1000=>1000);
    choose_from_menu($pagesizes, 'perpage', $perpage, '', '', '0', false, false, 0, 'pref_perpage');
     echo '<div id="reg_search" style="display: ';
    if ($advanced) {
        echo 'none';
    }
    else {
        echo 'inline';
    }
    echo ';" >&nbsp;&nbsp;&nbsp;<label for="pref_search">'.get_string('search').'</label> <input type="text" size="16" name="search" id= "pref_search" value="'.s($search).'" /></div>';
    echo '&nbsp;&nbsp;&nbsp;<label for="pref_sortby">'.get_string('sortby').'</label> ';
    //foreach field, print the option
    $fields = get_records('data_fields','dataid',$data->id, 'name');
    echo '<select name="sort" id="pref_sortby"><option value="0">'.get_string('dateentered','data').'</option>';
    foreach ($fields as $field) {
        if ($field->id == $sort) {
            echo '<option value="'.$field->id.'" selected="selected">'.$field->name.'</option>';
        } else {
            echo '<option value="'.$field->id.'">'.$field->name.'</option>';
        }
    }
    echo '</select>';
    echo '<label for="pref_order" class="accesshide">'.get_string('order').'</label>';
    echo '<select id="pref_order" name="order">';
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

    if ($advanced) {
        $checked = ' checked="checked" ';
    }
    else {
        $checked = '';
    }
    print '
            
        <script type="text/javascript">
        //<![CDATA[
        <!-- Start
        // javascript for hiding/displaying advanced search form

        function showHideAdvSearch(checked) {
            var divs = document.getElementsByTagName(\'div\');
            for(i=0;i<divs.length;i++) {
                if(divs[i].id.match(\'data_adv_form\')) {
                    if(checked) {
                        divs[i].style.display = \'inline\';
                    }
                    else {
                        divs[i].style.display = \'none\';
                    }
                }
                else if (divs[i].id.match(\'reg_search\')) {
                    if (!checked) {
                        divs[i].style.display = \'inline\';
                    }
                    else {
                        divs[i].style.display = \'none\';
                    }
                }
            }
        }
        //  End -->
        //]]>
        </script>';
    echo '&nbsp;<input type="checkbox" name="advanced" value="1" '.$checked.' onchange="showHideAdvSearch(this.checked);" />'.get_string('advancedsearch', 'data');
    echo '&nbsp;<input type="submit" value="'.get_string('savesettings','data').'" />';
    
    echo '<br />';
    echo '<div class="dataadvancedsearch" id="data_adv_form" style="display: ';
    
    if ($advanced) {
        echo 'inline';
    }
    else {
        echo 'none';
    }
    echo ';margin-left:auto;margin-right:auto;" >';
    echo '<table class="boxaligncenter">';
    
    // print ASC or DESC
    echo '<tr><td colspan="2">&nbsp;</td></tr>';
    $i = 0;

    // Determine if we are printing all fields for advanced search, or the template for advanced search
    // If a template is not defined, use the deafault template and display all fields.
    if(empty($data->asearchtemplate)) {
        data_generate_default_template($data, 'asearchtemplate');
    }

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

        $isteacher = has_capability('mod/data:managetemplates', $context);
    }

    /// Replacing tags
    $patterns = array();
    $replacement = array();

    /// Then we generate strings to replace for normal tags
    foreach ($fields as $field) {
        $patterns[]='/\[\['.$field->field->name.'\]\]/i';
        $searchfield = data_get_field_from_id($field->field->id, $data); 
        if (!empty($search_array[$field->field->id]->data)) {
            $replacement[] = $searchfield->display_search_field($search_array[$field->field->id]->data);
        } else {
            $replacement[] = $searchfield->display_search_field();
        }
    }
    
    ///actual replacement of the tags
    $newtext = preg_replace($patterns, $replacement, $data->asearchtemplate);
    $options->para=false;
    $options->noclean=true;
    echo '<tr><td>';
    echo format_text($newtext, FORMAT_HTML, $options);
    echo '</td></tr>';

    echo '<tr><td colspan="4" style="text-align: center;"><br/><input type="submit" value="'.get_string('savesettings','data').'" /><input type="reset" value="'.get_string('resetsettings','data').'" /></td></tr>';
    echo '</table>';
    echo '</div>';
    echo '</div>';
    echo '</form>';
    echo '</div>'; 

    
}

function data_print_ratings($data, $record) {
    global $USER;

    $cm = get_coursemodule_from_instance('data', $data->id);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if ($data->assessed and !empty($USER->id) and (has_capability('mod/data:rate', $context) or has_capability('mod/data:viewrating', $context) or data_isowner($record->id))) {
        if ($ratingsscale = make_grades_menu($data->scale)) {
            $ratingsmenuused = false;

            echo '<div class="ratings" style="text-align:center">';
            echo '<form id="form" method="post" action="rate.php">';

            if (has_capability('mod/data:rate', $context) and !data_isowner($record->id)) {
                data_print_ratings_mean($record->id, $ratingsscale, has_capability('mod/data:viewrating', $context));
                echo '&nbsp;';
                data_print_rating_menu($record->id, $USER->id, $ratingsscale);
                $ratingsmenuused = true;

            } else {
                data_print_ratings_mean($record->id, $ratingsscale, true);
            }

            if ($data->scale < 0) {
                if ($scale = get_record('scale', 'id', abs($data->scale))) {
                    print_scale_menu_helpbutton($data->course, $scale );
                }
            }

            if ($ratingsmenuused) {
                echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
                echo '<input type="submit" value="'.get_string('sendinratings', 'data').'" />';
            }
            echo '</form>';
            echo '</div>';
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
function data_print_comments($data, $record, $page=0, $mform=false) {

    global $CFG;

    echo '<a name="comments"></a>';

    if ($comments = get_records('data_comments','recordid',$record->id)) {
        foreach ($comments as $comment) {
            data_print_comment($data, $comment, $page);
        }
        echo '<br />';
    }

    if (!isloggedin() or isguest()) {
        return;
    }

    $editor = optional_param('addcomment', 0, PARAM_BOOL);

    if (!$mform and !$editor) {
        echo '<div class="newcomment" style="text-align:center">';
        echo '<a href="view.php?d='.$data->id.'&amp;page='.$page.'&amp;mode=single&amp;addcomment=1">'.get_string('addcomment', 'data').'</a>';
        echo '</div>';
    } else {
        if (!$mform) {
            require_once('comment_form.php');
            $mform = new mod_data_comment_form('comment.php');
            $mform->set_data(array('mode'=>'add', 'page'=>$page, 'rid'=>$record->id));
        }
        echo '<div class="newcomment" style="text-align:center">';
        $mform->display();
        echo '</div>';
    }
}

//prints a single comment entry
function data_print_comment($data, $comment, $page=0) {

    global $USER, $CFG;

    $cm = get_coursemodule_from_instance('data', $data->id);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $stredit = get_string('edit');
    $strdelete = get_string('delete');

    $user = get_record('user','id',$comment->userid);

    echo '<table cellspacing="0" align="center" width="50%" class="datacomment forumpost">';

    echo '<tr class="header"><td class="picture left">';
    print_user_picture($comment->userid, $data->course, $user->picture);
    echo '</td>';

    echo '<td class="topic starter" align="left"><div class="author">';
    $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));
    $by = new object();
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
    echo format_text($comment->content, $comment->format);

/// Commands

    echo '<div class="commands">';
    if (data_isowner($comment->recordid) or has_capability('mod/data:managecomments', $context)) {
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

    $LIKE = sql_ilike();
    if ($fieldid) {
        return record_exists_sql("SELECT * from {$CFG->prefix}data_fields df
                                  WHERE df.name $LIKE '$name' AND df.dataid = $dataid
                                    AND ((df.id < $fieldid) OR (df.id > $fieldid))");
    } else {
        return record_exists_sql("SELECT * from {$CFG->prefix}data_fields df
                                  WHERE df.name $LIKE '$name' AND df.dataid = $dataid");
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


/**
 * Converts a database (module instance) to use the Roles System
 * @param $data         - a data object with the same attributes as a record
 *                        from the data database table
 * @param $datamodid    - the id of the data module, from the modules table
 * @param $teacherroles - array of roles that have moodle/legacy:teacher
 * @param $studentroles - array of roles that have moodle/legacy:student
 * @param $guestroles   - array of roles that have moodle/legacy:guest
 * @param $cmid         - the course_module id for this data instance
 * @return boolean      - data module was converted or not
 */
function data_convert_to_roles($data, $teacherroles=array(), $studentroles=array(), $cmid=NULL) {

    global $CFG;

    if (!isset($data->participants) && !isset($data->assesspublic)
            && !isset($data->groupmode)) {
        // We assume that this database has already been converted to use the
        // Roles System. above fields get dropped the data module has been
        // upgraded to use Roles.
        return false;
    }

    if (empty($cmid)) {
        // We were not given the course_module id. Try to find it.
        if (!$cm = get_coursemodule_from_instance('data', $data->id)) {
            notify('Could not get the course module for the data');
            return false;
        } else {
            $cmid = $cm->id;
        }
    }
    $context = get_context_instance(CONTEXT_MODULE, $cmid);


    // $data->participants:
    // 1 - Only teachers can add entries
    // 3 - Teachers and students can add entries
    switch ($data->participants) {
        case 1:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:writeentry', CAP_PREVENT, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:writeentry', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
        case 3:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:writeentry', CAP_ALLOW, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:writeentry', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
    }

    // $data->assessed:
    // 2 - Only teachers can rate posts
    // 1 - Everyone can rate posts
    // 0 - No one can rate posts
    switch ($data->assessed) {
        case 0:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:rate', CAP_PREVENT, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:rate', CAP_PREVENT, $teacherrole->id, $context->id);
            }
            break;
        case 1:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:rate', CAP_ALLOW, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:rate', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
        case 2:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:rate', CAP_PREVENT, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:rate', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
    }

    // $data->assesspublic:
    // 0 - Students can only see their own ratings
    // 1 - Students can see everyone's ratings
    switch ($data->assesspublic) {
        case 0:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:viewrating', CAP_PREVENT, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:viewrating', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
        case 1:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:viewrating', CAP_ALLOW, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:viewrating', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
    }

    if (empty($cm)) {
        $cm = get_record('course_modules', 'id', $cmid);
    }

    // $cm->groupmode:
    // 0 - No groups
    // 1 - Separate groups
    // 2 - Visible groups
    switch ($cm->groupmode) {
        case 0:
            break;
        case 1:
            foreach ($studentroles as $studentrole) {
                assign_capability('moodle/site:accessallgroups', CAP_PREVENT, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
        case 2:
            foreach ($studentroles as $studentrole) {
                assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
    }
    return true;
}

/*
 * Returns the best name to show for a preset
 */
function data_preset_name($shortname, $path) {

    /// We are looking inside the preset itself as a first choice, but also in normal data directory
    $string = get_string('presetname'.$shortname, 'data', NULL, $path.'/lang/');

    if (substr($string, 0, 1) == '[') {
        return $shortname;
    } else {
        return $string;
    }
}

/*
 * Returns an array of all the available presets
 */
function data_get_available_presets($context) {
    global $CFG, $USER;

    $presets = array();

    if ($dirs = get_list_of_plugins('mod/data/preset')) {
        foreach ($dirs as $dir) {
            $fulldir = $CFG->dirroot.'/mod/data/preset/'.$dir;

            if (is_directory_a_preset($fulldir)) {
                $preset = new object;
                $preset->path = $fulldir;
                $preset->userid = 0;
                $preset->shortname = $dir;
                $preset->name = data_preset_name($dir, $fulldir);
                if (file_exists($fulldir.'/screenshot.jpg')) {
                    $preset->screenshot = $CFG->wwwroot.'/mod/data/preset/'.$dir.'/screenshot.jpg';
                } else if (file_exists($fulldir.'/screenshot.png')) {
                    $preset->screenshot = $CFG->wwwroot.'/mod/data/preset/'.$dir.'/screenshot.png';
                } else if (file_exists($fulldir.'/screenshot.gif')) {
                    $preset->screenshot = $CFG->wwwroot.'/mod/data/preset/'.$dir.'/screenshot.gif';
                }
                $presets[] = $preset;
            }
        }
    }

    if ($userids = get_list_of_plugins('data/preset', '', $CFG->dataroot)) {
        foreach ($userids as $userid) {
            $fulldir = $CFG->dataroot.'/data/preset/'.$userid;

            if ($userid == 0 || $USER->id == $userid || has_capability('mod/data:viewalluserpresets', $context)) {

                if ($dirs = get_list_of_plugins('data/preset/'.$userid, '', $CFG->dataroot)) {
                    foreach ($dirs as $dir) {
                        $fulldir = $CFG->dataroot.'/data/preset/'.$userid.'/'.$dir;

                        if (is_directory_a_preset($fulldir)) {
                            $preset = new object;
                            $preset->path = $fulldir;
                            $preset->userid = $userid;
                            $preset->shortname = $dir;
                            $preset->name = data_preset_name($dir, $fulldir);
                            if (file_exists($fulldir.'/screenshot.jpg')) {
                                $preset->screenshot = $CFG->wwwroot.'/mod/data/preset/'.$dir.'/screenshot.jpg';
                            } else if (file_exists($fulldir.'/screenshot.png')) {
                                $preset->screenshot = $CFG->wwwroot.'/mod/data/preset/'.$dir.'/screenshot.png';
                            } else if (file_exists($fulldir.'/screenshot.gif')) {
                                $preset->screenshot = $CFG->wwwroot.'/mod/data/preset/'.$dir.'/screenshot.gif';
                            }
                            $presets[] = $preset;
                        }
                    }
                }
            }
        }
    }

    return $presets;
}


function data_print_header($course, $cm, $data, $currenttab='') {

    global $CFG, $displaynoticegood, $displaynoticebad;

    $strdata = get_string('modulenameplural','data');

    print_header_simple($data->name, '', "<a href='index.php?id=$course->id'>$strdata</a> -> $data->name",
            '', '', true, update_module_button($cm->id, $course->id, get_string('modulename', 'data')),
            navmenu($course, $cm));

    print_heading(format_string($data->name));

/// Groups needed for Add entry tab
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = get_and_set_current_group($course, $groupmode);
    } else {
        $currentgroup = 0;
    }

    /// Print the tabs

    if ($currenttab) {
        include_once('tabs.php');
    }

    /// Print any notices

    if (!empty($displaynoticegood)) {
        notify($displaynoticegood, 'notifysuccess');    // good (usually green)
    } else if (!empty($displaynoticebad)) {
        notify($displaynoticebad);                     // bad (usuually red)
    }
}

function data_user_can_add_entry($data, $currentgroup=false, $groupmode='') {
    global $USER;

    if (!$cm = get_coursemodule_from_instance('data', $data->id)) {
        error('Course Module ID was incorrect');
    }
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if (!has_capability('mod/data:writeentry', $context) and !has_capability('mod/data:manageentries',$context)) {
        return false;
    }

    if ($currentgroup) {
        return (has_capability('moodle/site:accessallgroups', $context) or ismember($currentgroup));
    } else {
        //else it might be group 0 in visible mode
        if ($groupmode == VISIBLEGROUPS){
            return (ismember($currentgroup));
        } else {
            return true;
        }
    }
}


?>
