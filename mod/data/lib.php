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

// Some constants
define ('DATA_MAX_ENTRIES', 50);
define ('DATA_PERPAGE_SINGLE', 1);
define ('DATA_FIRSTNAME', -1);
define ('DATA_LASTNAME', -2);
define ('DATA_APPROVED', -3);
define ('DATA_TIMEADDED', 0);
define ('DATA_TIMEMODIFIED', -4);

define ('DATA_CAP_EXPORT', 'mod/data:viewalluserpresets');
// Users having assigned the default role "Non-editing teacher" can export database records
// Using the mod/data capability "viewalluserpresets" for Moodle 1.9.x, so no change in the role system is required.
// In Moodle >= 2, new roles may be introduced and used instead. 

class data_field_base {     // Base class for Database Field Types (see field/*/field.class.php)

    var $type = 'unknown';  // Subclasses must override the type with their name
    var $data = NULL;       // The database object that this field belongs to
    var $field = NULL;      // The field object itself, if we know it

    var $iconwidth = 16;    // Width of the icon for this fieldtype
    var $iconheight = 16;   // Width of the icon for this fieldtype


// Constructor function
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


// This field just sets up a default field object
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

// Set up the field object according to data in an object. Now is the time to clean it!
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

// Insert a new field in the database
// We assume the field object is already defined as $this->field
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


// Update a field in the database
    function update_field() {
        if (!update_record('data_fields', $this->field)) {
            notify('updating of new field failed!');
            return false;
        }
        return true;
    }

// Delete a field completely
    function delete_field() {
        if (!empty($this->field->id)) {
            delete_records('data_fields', 'id', $this->field->id);
            $this->delete_content();
        }
        return true;
    }

// Print the relevant form element in the ADD template for this field
    function display_add_field($recordid=0){
        if ($recordid){
            $content = get_field('data_content', 'content', 'fieldid', $this->field->id, 'recordid', $recordid);
        } else {
            $content = '';
        }

        // beware get_field returns false for new, empty records MDL-18567
        if ($content===false) {
            $content='';
        }

        $str = '<div title="'.s($this->field->description).'">';
        $str .= '<input style="width:300px;" type="text" name="field_'.$this->field->id.'" id="field_'.$this->field->id.'" value="'.s($content).'" />';
        $str .= '</div>';

        return $str;
    }

// Print the relevant form element to define the attributes for this field
// viewable by teachers only.
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

        echo '<div class="mdl-align">';
        echo '<input type="submit" value="'.$savebutton.'" />'."\n";
        echo '<input type="submit" name="cancel" value="'.get_string('cancel').'" />'."\n";
        echo '</div>';

        echo '</form>';

        print_simple_box_end();
    }

// Display the content of the field in browse mode
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

// Update the content of one data field in the data_content table
    function update_content($recordid, $value, $name='') {
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

// Delete all content associated with the field
    function delete_content($recordid=0) {

        $this->delete_content_files($recordid);

        if ($recordid) {
            return delete_records('data_content', 'fieldid', $this->field->id, 'recordid', $recordid);
        } else {
            return delete_records('data_content', 'fieldid', $this->field->id);
        }
    }

// Deletes any files associated with this field
    function delete_content_files($recordid='') {
        global $CFG;

        require_once($CFG->libdir.'/filelib.php');

        $dir = $CFG->dataroot.'/'.$this->data->course.'/'.$CFG->moddata.'/data/'.$this->data->id.'/'.$this->field->id;
        if ($recordid) {
            $dir .= '/'.$recordid;
        }

        return fulldelete($dir);
    }


// Check if a field from an add form is empty
    function notemptyfield($value, $name) {
        return !empty($value);
    }

// Just in case a field needs to print something before the whole form
    function print_before_form() {
    }

// Just in case a field needs to print something after the whole form
    function print_after_form() {
    }


// Returns the sortable field for the content. By default, it's just content
// but for some plugins, it could be content 1 - content4
    function get_sort_field() {
        return 'content';
    }

// Returns the SQL needed to refer to the column.  Some fields may need to CAST() etc.
    function get_sort_sql($fieldname) {
        return $fieldname;
    }

// Returns the name/type of the field
    function name() {
        return get_string('name'.$this->type, 'data');
    }

// Prints the respective type icon
    function image() {
        global $CFG;

        $str = '<a href="field.php?d='.$this->data->id.'&amp;fid='.$this->field->id.'&amp;mode=display&amp;sesskey='.sesskey().'">';
        $str .= '<img src="'.$CFG->modpixpath.'/data/field/'.$this->type.'/icon.gif" ';
        $str .= 'height="'.$this->iconheight.'" width="'.$this->iconwidth.'" alt="'.$this->type.'" title="'.$this->type.'" /></a>';
        return $str;
    }

//  Per default, it is assumed that fields support text exporting. Override this (return false) on fields not supporting text exporting. 
    function text_export_supported() {
        return true;
    }

//  Per default, return the record's text value only from the "content" field. Override this in fields class if necesarry. 
    function export_text_value($record) {
        if ($this->text_export_supported()) {
            return $record->content;
        }
    }

}


/*
/* Given a template and a dataid, generate a default case template
 * input @param template - addtemplate, singletemplate, listtempalte, rsstemplate
 *       @param dataid
 * output null
 */
function data_generate_default_template(&$data, $template, $recordid=0, $form=false, $update=true) {

    if (!$data && !$template) {
        return false;
    }
    if ($template == 'csstemplate' or $template == 'jstemplate' ) {
        return '';
    }

    // get all the fields for that database
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
            if ($form) {   // Print forms instead of data
                $fieldobj = data_get_field($field, $data);
                $str .= $fieldobj->display_add_field($recordid);

            } else {           // Just print the tag
                $str .= '[['.$field->name.']]';
            }
            $str .= '</td></tr>';

        }
        if ($template == 'listtemplate') {
            $str .= '<tr><td align="center" colspan="2">##edit##  ##more##  ##delete##  ##approve##</td></tr>';
        } else if ($template == 'singletemplate') {
            $str .= '<tr><td align="center" colspan="2">##edit##  ##delete##  ##approve##</td></tr>';
        } else if ($template == 'asearchtemplate') {
            $str .= '<tr><td valign="top" align="right">'.get_string('authorfirstname', 'data').': </td><td>##firstname##</td></tr>';
            $str .= '<tr><td valign="top" align="right">'.get_string('authorlastname', 'data').': </td><td>##lastname##</td></tr>';
        }

        $str .= '</table>';
        $str .= '</div>';

        if ($template == 'listtemplate'){
            $str .= '<hr />';
        }

        if ($update) {
            $newdata = new object();
            $newdata->id = $data->id;
            $newdata->{$template} = addslashes($str);
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
 * given a field name                                                   *
 * this function creates an instance of the particular subfield class   *
 ************************************************************************/
function data_get_field_from_name($name, $data){
    $field = get_record('data_fields', 'name', $name, 'dataid', $data->id);
    if ($field) {
        return data_get_field($field, $data);
    } else {
        return false;
    }
}

/************************************************************************
 * given a field id                                                     *
 * this function creates an instance of the particular subfield class   *
 ************************************************************************/
function data_get_field_from_id($fieldid, $data) {
    $field = get_record('data_fields', 'id', $fieldid, 'dataid', $data->id);
    if ($field) {
        return data_get_field($field, $data);
    } else {
        return false;
    }
}

/************************************************************************
 * given a field id                                                     *
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
function data_atmaxentries($data) {
    if (!$data->maxentries) {
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
function data_numentries($data) {
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
function data_add_record($data, $groupid=0) {
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
function data_tags_check($dataid, $template) {
    // first get all the possible tags
    $fields = get_records('data_fields','dataid',$dataid);
    // then we generate strings to replace
    $tagsok = true; // let's be optimistic
    foreach ($fields as $field) {
        $pattern="/\[\[".$field->name."\]\]/i";
        if (preg_match_all($pattern, $template, $dummy)>1) {
            $tagsok = false;
            notify ('[['.$field->name.']] - '.get_string('multipletags','data'));
        }
    }
    // else return true
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

    $data = stripslashes_recursive($data);
    data_grade_item_update($data);
    return $data->id;
}

/************************************************************************
 * updates an instance of a data                                        *
 ************************************************************************/
function data_update_instance($data) {
    global $CFG;
    $data->timemodified = time();
    $data->id = $data->instance;

    if (empty($data->assessed)) {
        $data->assessed = 0;
    }
    if (empty($data->notification)) {
        $data->notification = 0;
    }
    if (! update_record('data', $data)) {
        return false;
    }

    $data = stripslashes_recursive($data);
    data_grade_item_update($data);
    return true;
}

/************************************************************************
 * deletes an instance of a data                                        *
 ************************************************************************/
function data_delete_instance($id) {    // takes the dataid

    global $CFG;
    if (! $data = get_record('data', 'id', $id)) {
        return false;
    }

    // Delete all the associated information
    // get all the records in this data
    $sql = 'SELECT c.* FROM '.$CFG->prefix.'data_records r LEFT JOIN '.
           $CFG->prefix.'data_content c ON c.recordid = r.id WHERE r.dataid = '.$id;

    if ($contents = get_records_sql($sql)) {
        foreach($contents as $content) {
            $field = get_record('data_fields','id',$content->fieldid);
            if ($g = data_get_field($field, $data)) {
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
    $result = delete_records('data', 'id', $id);
    data_grade_item_delete($data);
    return $result;
}

/************************************************************************
 * returns a summary of data activity of this user                      *
 ************************************************************************/
function data_user_outline($course, $user, $mod, $data) {
    global $CFG;
    require_once("$CFG->libdir/gradelib.php");

    $grades = grade_get_grades($course->id, 'mod', 'data', $data->id, $user->id);
    if (empty($grades->items[0]->grades)) {
        $grade = false;
    } else {
        $grade = reset($grades->items[0]->grades);
    }

    if ($countrecords = count_records('data_records', 'dataid', $data->id, 'userid', $user->id)) {
        $result = new object();
        $result->info = get_string('numrecords', 'data', $countrecords);
        $lastrecord   = get_record_sql('SELECT id,timemodified FROM '.$CFG->prefix.'data_records
                                         WHERE dataid = '.$data->id.' AND userid = '.$user->id.'
                                      ORDER BY timemodified DESC', true);
        $result->time = $lastrecord->timemodified;
        if ($grade) {
            $result->info .= ', ' . get_string('grade') . ': ' . $grade->str_long_grade;
        }
        return $result;
    } else if ($grade) {
        $result = new object();
        $result->info = get_string('grade') . ': ' . $grade->str_long_grade;
        $result->time = $grade->dategraded;
        return $result;
    }
    return NULL;
}

/************************************************************************
 * Prints all the records uploaded by this user                         *
 ************************************************************************/
function data_user_complete($course, $user, $mod, $data) {
    global $CFG;
    require_once("$CFG->libdir/gradelib.php");

    $grades = grade_get_grades($course->id, 'mod', 'data', $data->id, $user->id);
    if (!empty($grades->items[0]->grades)) {
        $grade = reset($grades->items[0]->grades);
        echo '<p>'.get_string('grade').': '.$grade->str_long_grade.'</p>';
        if ($grade->str_feedback) {
            echo '<p>'.get_string('feedback').': '.$grade->str_feedback.'</p>';
        }
    }
    if ($records = get_records_select('data_records', 'dataid = '.$data->id.' AND userid = '.$user->id,
                                                      'timemodified DESC')) {
        data_print_template('singletemplate', $records, $data);
    }
}

/**
 * Return grade for given user or all users.
 *
 * @param int $dataid id of data
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function data_get_user_grades($data, $userid=0) {
    global $CFG;
    $user = $userid ? "AND u.id = $userid" : "";
    $sql = "SELECT u.id, u.id AS userid, avg(drt.rating) AS rawgrade
              FROM {$CFG->prefix}user u, {$CFG->prefix}data_records dr,
                   {$CFG->prefix}data_ratings drt
             WHERE u.id = dr.userid AND dr.id = drt.recordid
                   AND drt.userid != u.id AND dr.dataid = $data->id
                   $user
          GROUP BY u.id";
    return get_records_sql($sql);
}

/**
 * Update grades by firing grade_updated event
 *
 * @param object $data null means all databases
 * @param int $userid specific user only, 0 mean all
 */
function data_update_grades($data=null, $userid=0, $nullifnone=true) {
    global $CFG;
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }

    if ($data != null) {
        if ($grades = data_get_user_grades($data, $userid)) {
            data_grade_item_update($data, $grades);
        } else if ($userid and $nullifnone) {
            $grade = new object();
            $grade->userid   = $userid;
            $grade->rawgrade = NULL;
            data_grade_item_update($data, $grade);
        } else {
            data_grade_item_update($data);
        }
    } else {
        $sql = "SELECT d.*, cm.idnumber as cmidnumber
                  FROM {$CFG->prefix}data d, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
                 WHERE m.name='data' AND m.id=cm.module AND cm.instance=d.id";
        if ($rs = get_recordset_sql($sql)) {
            while ($data = rs_fetch_next_record($rs)) {
                if ($data->assessed) {
                    data_update_grades($data, 0, false);
                } else {
                    data_grade_item_update($data);
                }
            }
            rs_close($rs);
        }
    }
}

/**
 * Update/create grade item for given data
 *
 * @param object $data object with extra cmidnumber
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return object grade_item
 */
function data_grade_item_update($data, $grades=NULL) {
    global $CFG;
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }
    $params = array('itemname'=>$data->name, 'idnumber'=>$data->cmidnumber);
    if (!$data->assessed or $data->scale == 0) {
        $params['gradetype'] = GRADE_TYPE_NONE;
    } else if ($data->scale > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $data->scale;
        $params['grademin']  = 0;
    } else if ($data->scale < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$data->scale;
    }
    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    }

    return grade_update('mod/data', $data->course, 'mod', 'data', $data->id, 0, $grades, $params);
}

/**
 * Delete grade item for given data
 *
 * @param object $data object
 * @return object grade_item
 */
function data_grade_item_delete($data) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    return grade_update('mod/data', $data->course, 'mod', 'data', $data->id, 0, NULL, array('deleted'=>1));
}

/************************************************************************
 * returns a list of participants of this database                      *
 ************************************************************************/
function data_get_participants($dataid) {
// Returns the users with data in one data
// (users with records in data_records, data_comments and data_ratings)
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
    if ($records) {
        foreach ($records as $record) {
            $participants[$record->id] = $record;
        }
    }
    if ($comments) {
        foreach ($comments as $comment) {
            $participants[$comment->id] = $comment;
        }
    }
    if ($ratings) {
        foreach ($ratings as $rating) {
            $participants[$rating->id] = $rating;
        }
    }
    return $participants;
}

// junk functions
/************************************************************************
 * takes a list of records, the current data, a search string,          *
 * and mode to display prints the translated template                   *
 * input @param array $records                                          *
 *       @param object $data                                            *
 *       @param string $search                                          *
 *       @param string $template                                        *
 * output null                                                          *
 ************************************************************************/
function data_print_template($template, $records, $data, $search='', $page=0, $return=false) {
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
    foreach ($records as $record) {   // Might be just one for the single template
    // Replacing tags
        $patterns = array();
        $replacement = array();
    // Then we generate strings to replace for normal tags
        foreach ($fields as $field) {
            $patterns[]='[['.$field->field->name.']]';
            $replacement[] = highlight($search, $field->display_browse_field($record->id, $template));
        }
    // Replacing special tags (##Edit##, ##Delete##, ##More##)
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

        $moreurl = $CFG->wwwroot . '/mod/data/view.php?d=' . $data->id . '&amp;rid=' . $record->id;
        if ($search) {
            $moreurl .= '&amp;filter=1';
        }
        $patterns[]='##more##';
        $replacement[] = '<a href="' . $moreurl . '"><img src="' . $CFG->pixpath . '/i/search.gif" class="iconsmall" alt="' . get_string('more', 'data') . '" title="' . get_string('more', 'data') . '" /></a>';

        $patterns[]='##moreurl##';
        $replacement[] = $moreurl;

        $patterns[]='##user##';
        $replacement[] = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$record->userid.
                               '&amp;course='.$data->course.'">'.fullname($record).'</a>';
        
        $patterns[] = '##timeadded##';
        $replacement[] = userdate($record->timecreated); 

        $patterns[] = '##timemodified##';
        $replacement [] = userdate($record->timemodified);

        $patterns[]='##approve##';
        if (has_capability('mod/data:approve', $context) && ($data->approval) && (!$record->approved)) {
            $replacement[] = '<span class="approve"><a href="'.$CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&amp;approve='.$record->id.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/i/approve.gif" class="icon" alt="'.get_string('approve').'" /></a></span>';
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

        // actual replacement of the tags
        $newtext = str_ireplace($patterns, $replacement, $data->{$template});

        // no more html formatting and filtering - see MDL-6635
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
function data_print_preference_form($data, $perpage, $search, $sort='', $order='ASC', $search_array = '', $advanced = 0, $mode= '') {
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
    // foreach field, print the option
    echo '<select name="sort" id="pref_sortby">';
    if ($fields = get_records('data_fields','dataid',$data->id, 'name')) {
        echo '<optgroup label="'.get_string('fields', 'data').'">';
        foreach ($fields as $field) {
            if ($field->id == $sort) {
                echo '<option value="'.$field->id.'" selected="selected">'.$field->name.'</option>';
            } else {
                echo '<option value="'.$field->id.'">'.$field->name.'</option>';
            }
        }
        echo '</optgroup>';
    }
    $options = array();
    $options[DATA_TIMEADDED]    = get_string('timeadded', 'data');
    $options[DATA_TIMEMODIFIED] = get_string('timemodified', 'data');
    $options[DATA_FIRSTNAME]    = get_string('authorfirstname', 'data');
    $options[DATA_LASTNAME]     = get_string('authorlastname', 'data');
    if ($data->approval and has_capability('mod/data:approve', $context)) {
        $options[DATA_APPROVED] = get_string('approved', 'data');
    }
    echo '<optgroup label="'.get_string('other', 'data').'">';
    foreach ($options as $key => $name) {
        if ($key == $sort) {
            echo '<option value="'.$key.'" selected="selected">'.$name.'</option>';
        } else {
            echo '<option value="'.$key.'">'.$name.'</option>';
        }
    }
    echo '</optgroup>';
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
    echo '&nbsp;<input type="hidden" name="advanced" value="0" />';
    echo '&nbsp;<input type="hidden" name="filter" value="1" />';
    echo '&nbsp;<input type="checkbox" id="advancedcheckbox" name="advanced" value="1" '.$checked.' onchange="showHideAdvSearch(this.checked);" /><label for="advancedcheckbox">'.get_string('advancedsearch', 'data').'</label>';
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

    // Replacing tags
    $patterns = array();
    $replacement = array();

    // Then we generate strings to replace for normal tags
    foreach ($fields as $field) {
        $fieldname = $field->field->name;
        $fieldname = preg_quote($fieldname, '/');
        $patterns[] = "/\[\[$fieldname\]\]/i";
        $searchfield = data_get_field_from_id($field->field->id, $data);
        if (!empty($search_array[$field->field->id]->data)) {
            $replacement[] = $searchfield->display_search_field($search_array[$field->field->id]->data);
        } else {
            $replacement[] = $searchfield->display_search_field();
        }
    }
    $fn = !empty($search_array[DATA_FIRSTNAME]->data) ? $search_array[DATA_FIRSTNAME]->data : '';
    $ln = !empty($search_array[DATA_LASTNAME]->data) ? $search_array[DATA_LASTNAME]->data : '';
    $patterns[]    = '/##firstname##/';
    $replacement[] = '<input type="text" size="16" name="u_fn" value="'.$fn.'" />';
    $patterns[]    = '/##lastname##/';
    $replacement[] = '<input type="text" size="16" name="u_ln" value="'.$ln.'" />';

    // actual replacement of the tags
    $newtext = preg_replace($patterns, $replacement, $data->asearchtemplate);
    $options = new object();
    $options->para=false;
    $options->noclean=true;
    echo '<tr><td>';
    echo format_text($newtext, FORMAT_HTML, $options);
    echo '</td></tr>';
    echo '<tr><td colspan="4" style="text-align: center;"><br/><input type="submit" value="'.get_string('savesettings','data').'" /><input type="submit" name="resetadv" value="'.get_string('resetsettings','data').'" /></td></tr>';
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
            echo '<input type="hidden" name="dataid" value="'.$data->id.'" />';
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
// Print the multiple ratings on a post given to the current user by others.
// Scale is an array of ratings

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
// Return the mean rating of a post given to the current user by others.
// Scale is an array of possible ratings in the scale
// Ratings is an optional simple array of actual ratings (just integers)
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
// Print the menu of ratings as part of a larger form.
// If the post has already been - set that value.
// Scale is an array of ratings
    static $strrate;
    if (!$rating = get_record("data_ratings", "userid", $userid, "recordid", $recordid)) {
        $rating->rating = -999;
    }
    if (empty($strrate)) {
        $strrate = get_string("rate", "data");
    }
    choose_from_menu($scale, $recordid, $rating->rating, "$strrate...", '', -999);
}


function data_get_ratings($recordid, $sort="u.firstname ASC") {
// Returns a list of ratings for a particular post - sorted.
    global $CFG;
    return get_records_sql("SELECT u.*, r.rating
                              FROM {$CFG->prefix}data_ratings r,
                                   {$CFG->prefix}user u
                             WHERE r.recordid = $recordid
                               AND r.userid = u.id
                             ORDER BY $sort");
}


// prints all comments + a text box for adding additional comment
function data_print_comments($data, $record, $page=0, $mform=false) {
    global $CFG;
    $cm = get_coursemodule_from_instance('data', $data->id);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $cancomment = has_capability('mod/data:comment', $context);
    echo '<a name="comments"></a>';
    if ($comments = get_records('data_comments','recordid',$record->id)) {
        foreach ($comments as $comment) {
            data_print_comment($data, $comment, $page);
        }
        echo '<br />';
    }
    if (!isloggedin() or isguest() or !$cancomment) {
        return;
    }
    $editor = optional_param('addcomment', 0, PARAM_BOOL);
    if (!$mform and !$editor) {
        echo '<div class="newcomment" style="text-align:center">';
        echo '<a href="view.php?d='.$data->id.'&amp;rid='.$record->id.'&amp;mode=single&amp;addcomment=1">'.get_string('addcomment', 'data').'</a>';
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

// prints a single comment entry
function data_print_comment($data, $comment, $page=0) {
    global $USER, $CFG;
    $cm = get_coursemodule_from_instance('data', $data->id);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $stredit = get_string('edit');
    $strdelete = get_string('delete');
    $user = get_record('user','id',$comment->userid);
    echo '<table cellspacing="0" align="center" width="50%" class="datacomment forumpost">';
    echo '<tr class="header"><td class="picture left">';
    print_user_picture($user, $data->course, $user->picture);
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
    if ($groups = groups_get_all_groups($data->course, $comment->userid, $cm->groupingid)) {
        print_group_picture($groups, $data->course, false, false, true);
    } else {
        echo '&nbsp;';
    }

// Actual content
    echo '</td><td class="content" align="left">'."\n";
    // Print whole message
    echo format_text($comment->content, $comment->format);

// Commands
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

    switch ($cm->groupmode) {
        case NOGROUPS:
            break;
        case SEPARATEGROUPS:
            foreach ($studentroles as $studentrole) {
                assign_capability('moodle/site:accessallgroups', CAP_PREVENT, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
        case VISIBLEGROUPS:
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

    // We are looking inside the preset itself as a first choice, but also in normal data directory
    $string = get_string('modulename', 'datapreset_'.$shortname);

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
    $navigation = build_navigation('', $cm);
    print_header_simple($data->name, '', $navigation,
            '', '', true, update_module_button($cm->id, $course->id, get_string('modulename', 'data')),
            navmenu($course, $cm));
    print_heading(format_string($data->name));

// Groups needed for Add entry tab
    $currentgroup = groups_get_activity_group($cm);
    $groupmode = groups_get_activity_groupmode($cm);
    // Print the tabs
    if ($currenttab) {
        include('tabs.php');
    }
    // Print any notices
    if (!empty($displaynoticegood)) {
        notify($displaynoticegood, 'notifysuccess');    // good (usually green)
    } else if (!empty($displaynoticebad)) {
        notify($displaynoticebad);                     // bad (usuually red)
    }
}

function data_user_can_add_entry($data, $currentgroup, $groupmode) {
    global $USER;
    if (!$cm = get_coursemodule_from_instance('data', $data->id)) {
        error('Course Module ID was incorrect');
    }
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (!has_capability('mod/data:writeentry', $context) and !has_capability('mod/data:manageentries',$context)) {
        return false;
    }
    if (!$groupmode or has_capability('moodle/site:accessallgroups', $context)) {
        return true;
    }

    if ($currentgroup) {
        return groups_is_member($currentgroup);
    } else {
        //else it might be group 0 in visible mode
        if ($groupmode == VISIBLEGROUPS){
            return true;
        } else {
            return false;
        }
    }
}


function is_directory_a_preset($directory) {
    $directory = rtrim($directory, '/\\') . '/';
    $status = file_exists($directory.'singletemplate.html') &&
              file_exists($directory.'listtemplate.html') &&
              file_exists($directory.'listtemplateheader.html') &&
              file_exists($directory.'listtemplatefooter.html') &&
              file_exists($directory.'addtemplate.html') &&
              file_exists($directory.'rsstemplate.html') &&
              file_exists($directory.'rsstitletemplate.html') &&
              file_exists($directory.'csstemplate.css') &&
              file_exists($directory.'jstemplate.js') &&
              file_exists($directory.'preset.xml');
    return $status;
}


function clean_preset($folder) {
    $status = @unlink($folder.'/singletemplate.html') &&
              @unlink($folder.'/listtemplate.html') &&
              @unlink($folder.'/listtemplateheader.html') &&
              @unlink($folder.'/listtemplatefooter.html') &&
              @unlink($folder.'/addtemplate.html') &&
              @unlink($folder.'/rsstemplate.html') &&
              @unlink($folder.'/rsstitletemplate.html') &&
              @unlink($folder.'/csstemplate.css') &&
              @unlink($folder.'/jstemplate.js') &&
              @unlink($folder.'/preset.xml');

    // optional
    @unlink($folder.'/asearchtemplate.html');
    return $status;
}


class PresetImporter {
    function PresetImporter($course, $cm, $data, $userid, $shortname) {
        global $CFG;
        $this->course = $course;
        $this->cm = $cm;
        $this->data = $data;
        $this->userid = $userid;
        $this->shortname = $shortname;
        $this->folder = data_preset_path($course, $userid, $shortname);
    }

    function get_settings() {
        global $CFG;

        if (!is_directory_a_preset($this->folder)) {
            error("$this->userid/$this->shortname Not a preset");
        }

        /* Grab XML */
        $presetxml = file_get_contents($this->folder.'/preset.xml');
        $parsedxml = xmlize($presetxml, 0);

        $allowed_settings = array('intro', 'comments', 'requiredentries', 'requiredentriestoview',
                                  'maxentries', 'rssarticles', 'approval', 'defaultsortdir', 'defaultsort');

        /* First, do settings. Put in user friendly array. */
        $settingsarray = $parsedxml['preset']['#']['settings'][0]['#'];
        $settings = new StdClass();

        foreach ($settingsarray as $setting => $value) {
            if (!is_array($value)) {
                continue;
            }
            if (!in_array($setting, $allowed_settings)) {
                // unsupported setting
                continue;
            }
            $settings->$setting = $value[0]['#'];
        }

        /* Now work out fields to user friendly array */
        $fieldsarray = $parsedxml['preset']['#']['field'];
        $fields = array();
        foreach ($fieldsarray as $field) {
            if (!is_array($field)) {
                continue;
            }
            $f = new StdClass();
            foreach ($field['#'] as $param => $value) {
                if (!is_array($value)) {
                    continue;
                }
                $f->$param = addslashes($value[0]['#']);
            }
            $f->dataid = $this->data->id;
            $f->type = clean_param($f->type, PARAM_ALPHA);
            $fields[] = $f;
        }
        /* Now add the HTML templates to the settings array so we can update d */
        $settings->singletemplate     = file_get_contents($this->folder."/singletemplate.html");
        $settings->listtemplate       = file_get_contents($this->folder."/listtemplate.html");
        $settings->listtemplateheader = file_get_contents($this->folder."/listtemplateheader.html");
        $settings->listtemplatefooter = file_get_contents($this->folder."/listtemplatefooter.html");
        $settings->addtemplate        = file_get_contents($this->folder."/addtemplate.html");
        $settings->rsstemplate        = file_get_contents($this->folder."/rsstemplate.html");
        $settings->rsstitletemplate   = file_get_contents($this->folder."/rsstitletemplate.html");
        $settings->csstemplate        = file_get_contents($this->folder."/csstemplate.css");
        $settings->jstemplate         = file_get_contents($this->folder."/jstemplate.js");

        //optional
        if (file_exists($this->folder."/asearchtemplate.html")) {
            $settings->asearchtemplate = file_get_contents($this->folder."/asearchtemplate.html");
        } else {
            $settings->asearchtemplate = NULL;
        }

        $settings->instance = $this->data->id;

        /* Now we look at the current structure (if any) to work out whether we need to clear db
           or save the data */
        if (!$currentfields = get_records('data_fields', 'dataid', $this->data->id)) {
            $currentfields = array();
        }

        return array($settings, $fields, $currentfields);
    }

    function import_options() {
        if (!confirm_sesskey()) {
            error("Sesskey Invalid");
        }
        $strblank = get_string('blank', 'data');
        $strcontinue = get_string('continue');
        $strwarning = get_string('mappingwarning', 'data');
        $strfieldmappings = get_string('fieldmappings', 'data');
        $strnew = get_string('new');
        $sesskey = sesskey();
        list($settings, $newfields,  $currentfields) = $this->get_settings();
        echo '<div class="presetmapping"><form action="preset.php" method="post">';
        echo '<div>';
        echo '<input type="hidden" name="action" value="finishimport" />';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<input type="hidden" name="d" value="'.$this->data->id.'" />';
        echo '<input type="hidden" name="fullname" value="'.$this->userid.'/'.$this->shortname.'" />';
        if (!empty($currentfields) && !empty($newfields)) {
            echo "<h3>$strfieldmappings ";
            helpbutton('fieldmappings', $strfieldmappings, 'data');
            echo '</h3><table>';
            foreach ($newfields as $nid => $newfield) {
                echo "<tr><td><label for=\"id_$newfield->name\">$newfield->name</label></td>";
                echo '<td><select name="field_'.$nid.'" id="id_'.$newfield->name.'">';
                $selected = false;
                foreach ($currentfields as $cid => $currentfield) {
                    if ($currentfield->type == $newfield->type) {
                        if ($currentfield->name == $newfield->name) {
                            echo '<option value="'.$cid.'" selected="selected">'.$currentfield->name.'</option>';
                            $selected=true;
                        }
                        else {
                            echo '<option value="'.$cid.'">'.$currentfield->name.'</option>';
                        }
                    }
                }
                if ($selected)
                    echo '<option value="-1">-</option>';
                else
                    echo '<option value="-1" selected="selected">-</option>';
                echo '</select></td></tr>';
            }
            echo '</table>';
            echo "<p>$strwarning</p>";
        } else if (empty($newfields)) {
            error("New preset has no defined fields!");
        }
        echo '<div class="overwritesettings"><label for="overwritesettings">'.get_string('overwritesettings', 'data');
        echo '<input id="overwritesettings" name="overwritesettings" type="checkbox" /></label></div>';
        echo '<input class="button" type="submit" value="'.$strcontinue.'" /></div></form></div>';
    }

    function import() {
        global $CFG;
        list($settings, $newfields, $currentfields) = $this->get_settings();
        $preservedfields = array();
        $overwritesettings = optional_param('overwritesettings', 0, PARAM_BOOL);
        /* Maps fields and makes new ones */
        if (!empty($newfields)) {
            /* We require an injective mapping, and need to know what to protect */
            foreach ($newfields as $nid => $newfield) {
                $cid = optional_param("field_$nid", -1, PARAM_INT);
                if ($cid == -1) continue;
                if (array_key_exists($cid, $preservedfields)) error("Not an injective map");
                else $preservedfields[$cid] = true;
            }
            foreach ($newfields as $nid => $newfield) {
                $cid = optional_param("field_$nid", -1, PARAM_INT);
                /* A mapping. Just need to change field params. Data kept. */
                if ($cid != -1 and isset($currentfields[$cid])) {
                    $fieldobject = data_get_field_from_id($currentfields[$cid]->id, $this->data);
                    foreach ($newfield as $param => $value) {
                        if ($param != "id") {
                            $fieldobject->field->$param = $value;
                        }
                    }
                    unset($fieldobject->field->similarfield);
                    $fieldobject->update_field();
                    unset($fieldobject);
                }
                /* Make a new field */
                else {
                    include_once("field/$newfield->type/field.class.php");
                    if (!isset($newfield->description)) {
                        $newfield->description = '';
                    }
                    $classname = 'data_field_'.$newfield->type;
                    $fieldclass = new $classname($newfield, $this->data);
                    $fieldclass->insert_field();
                    unset($fieldclass);
                }
            }
        }

        /* Get rid of all old unused data */
        if (!empty($preservedfields)) {
            foreach ($currentfields as $cid => $currentfield) {
                if (!array_key_exists($cid, $preservedfields)) {
                    /* Data not used anymore so wipe! */
                    print "Deleting field $currentfield->name<br />";
                    $id = $currentfield->id;
                    //Why delete existing data records and related comments/ratings??
/*
                    if ($content = get_records('data_content', 'fieldid', $id)) {
                        foreach ($content as $item) {
                            delete_records('data_ratings', 'recordid', $item->recordid);
                            delete_records('data_comments', 'recordid', $item->recordid);
                            delete_records('data_records', 'id', $item->recordid);
                        }
                    }*/
                    delete_records('data_content', 'fieldid', $id);
                    delete_records('data_fields', 'id', $id);
                }
            }
        }

    // handle special settings here
        if (!empty($settings->defaultsort)) {
            if (is_numeric($settings->defaultsort)) {
                // old broken value
                $settings->defaultsort = 0;
            } else {
                $settings->defaultsort = (int)get_field('data_fields', 'id', 'dataid', $this->data->id, 'name', addslashes($settings->defaultsort));
            }
        } else {
            $settings->defaultsort = 0;
        }

        // do we want to overwrite all current database settings?
        if ($overwritesettings) {
            // all supported settings
            $overwrite = array_keys((array)$settings);
        } else {
            // only templates and sorting
            $overwrite = array('singletemplate', 'listtemplate', 'listtemplateheader', 'listtemplatefooter',
                               'addtemplate', 'rsstemplate', 'rsstitletemplate', 'csstemplate', 'jstemplate',
                               'asearchtemplate', 'defaultsortdir', 'defaultsort');
        }

        // now overwrite current data settings
        foreach ($this->data as $prop=>$unused) {
            if (in_array($prop, $overwrite)) {
                $this->data->$prop = $settings->$prop;
            }
        }

        data_update_instance(addslashes_object($this->data));
        if (strstr($this->folder, '/temp/')) {
        // Removes the temporary files
            clean_preset($this->folder); 
        }
        return true;
    }
}

function data_preset_path($course, $userid, $shortname) {
    global $USER, $CFG;
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    $userid = (int)$userid;
    if ($userid > 0 && ($userid == $USER->id || has_capability('mod/data:viewalluserpresets', $context))) {
        return $CFG->dataroot.'/data/preset/'.$userid.'/'.$shortname;
    } else if ($userid == 0) {
        return $CFG->dirroot.'/mod/data/preset/'.$shortname;
    } else if ($userid < 0) {
        return $CFG->dataroot.'/temp/data/'.-$userid.'/'.$shortname;
    }
    return 'Does it disturb you that this code will never run?';
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the data.
 * @param $mform form passed by reference
 */
function data_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'dataheader', get_string('modulenameplural', 'data'));
    $mform->addElement('checkbox', 'reset_data', get_string('deleteallentries','data'));

    $mform->addElement('checkbox', 'reset_data_notenrolled', get_string('deletenotenrolled', 'data'));
    $mform->disabledIf('reset_data_notenrolled', 'reset_data', 'checked');

    $mform->addElement('checkbox', 'reset_data_ratings', get_string('deleteallratings'));
    $mform->disabledIf('reset_data_ratings', 'reset_data', 'checked');

    $mform->addElement('checkbox', 'reset_data_comments', get_string('deleteallcomments'));
    $mform->disabledIf('reset_data_comments', 'reset_data', 'checked');
}

/**
 * Course reset form defaults.
 */
function data_reset_course_form_defaults($course) {
    return array('reset_data'=>0, 'reset_data_ratings'=>1, 'reset_data_comments'=>1, 'reset_data_notenrolled'=>0);
}

/**
 * Removes all grades from gradebook
 * @param int $courseid
 * @param string optional type
 */
function data_reset_gradebook($courseid, $type='') {
    global $CFG;
    $sql = "SELECT d.*, cm.idnumber as cmidnumber, d.course as courseid
              FROM {$CFG->prefix}data d, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
             WHERE m.name='data' AND m.id=cm.module AND cm.instance=d.id AND d.course=$courseid";
    if ($datas = get_records_sql($sql)) {
        foreach ($datas as $data) {
            data_grade_item_update($data, 'reset');
        }
    }
}

/**
 * Actual implementation of the rest coures functionality, delete all the
 * data responses for course $data->courseid.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function data_reset_userdata($data) {
    global $CFG;
    require_once($CFG->libdir.'/filelib.php');
    $componentstr = get_string('modulenameplural', 'data');
    $status = array();
    $allrecordssql = "SELECT r.id
                        FROM {$CFG->prefix}data_records r
                             INNER JOIN {$CFG->prefix}data d ON r.dataid = d.id
                       WHERE d.course = {$data->courseid}";
    $alldatassql = "SELECT d.id
                      FROM {$CFG->prefix}data d
                     WHERE d.course={$data->courseid}";
    // delete entries if requested
    if (!empty($data->reset_data)) {
        delete_records_select('data_ratings', "recordid IN ($allrecordssql)");
        delete_records_select('data_comments', "recordid IN ($allrecordssql)");
        delete_records_select('data_content', "recordid IN ($allrecordssql)");
        delete_records_select('data_records', "dataid IN ($alldatassql)");
        if ($datas = get_records_sql($alldatassql)) {
            foreach ($datas as $dataid=>$unused) {
                fulldelete("$CFG->dataroot/$data->courseid/moddata/data/$dataid");
            }
        }
        if (empty($data->reset_gradebook_grades)) {
            // remove all grades from gradebook
            data_reset_gradebook($data->courseid);
        }
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallentries', 'data'), 'error'=>false);
    }

    // remove entries by users not enrolled into course
    if (!empty($data->reset_data_notenrolled)) {
        $recordssql = "SELECT r.id, r.userid, r.dataid, u.id AS userexists, u.deleted AS userdeleted
                         FROM {$CFG->prefix}data_records r
                              INNER JOIN {$CFG->prefix}data d ON r.dataid = d.id
                              LEFT OUTER JOIN {$CFG->prefix}user u ON r.userid = u.id
                        WHERE d.course = {$data->courseid} AND r.userid > 0";
        $course_context = get_context_instance(CONTEXT_COURSE, $data->courseid);
        $notenrolled = array();
        $fields = array();
        if ($rs = get_recordset_sql($recordssql)) {
            while ($record = rs_fetch_next_record($rs)) {
                if (array_key_exists($record->userid, $notenrolled) or !$record->userexists or $record->userdeleted
                  or !has_capability('moodle/course:view', $course_context , $record->userid)) {
                    delete_records('data_ratings', 'recordid', $record->id);
                    delete_records('data_comments', 'recordid', $record->id);
                    delete_records('data_content', 'recordid', $record->id);
                    delete_records('data_records', 'id', $record->id);
                    // HACK: this is ugly - the recordid should be before the fieldid!
                    if (!array_key_exists($record->dataid, $fields)) {
                        if ($fs = get_records('data_fields', 'dataid', $record->dataid)) {
                            $fields[$record->dataid] = array_keys($fs);
                        } else {
                            $fields[$record->dataid] = array();
                        }
                    }
                    foreach($fields[$record->dataid] as $fieldid) {
                        fulldelete("$CFG->dataroot/$data->courseid/moddata/data/$record->dataid/$fieldid/$record->id");
                    }
                    $notenrolled[$record->userid] = true;
                }
            }
            rs_close($rs);
            $status[] = array('component'=>$componentstr, 'item'=>get_string('deletenotenrolled', 'data'), 'error'=>false);
        }
    }

    // remove all ratings
    if (!empty($data->reset_data_ratings)) {
        delete_records_select('data_ratings', "recordid IN ($allrecordssql)");
        if (empty($data->reset_gradebook_grades)) {
            // remove all grades from gradebook
            data_reset_gradebook($data->courseid);
        }
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallratings'), 'error'=>false);
    }

    // remove all comments
    if (!empty($data->reset_data_comments)) {
        delete_records_select('data_comments', "recordid IN ($allrecordssql)");
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallcomments'), 'error'=>false);
    }

    // updating dates - shift may be negative too
    if ($data->timeshift) {
        shift_course_mod_dates('data', array('timeavailablefrom', 'timeavailableto', 'timeviewfrom', 'timeviewto'), $data->timeshift, $data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('datechanged'), 'error'=>false);
    }
    return $status;
}

/**
 * Returns all other caps used in module
 */
function data_get_extra_capabilities() {
    return array('moodle/site:accessallgroups', 'moodle/site:viewfullnames');
}


?>
