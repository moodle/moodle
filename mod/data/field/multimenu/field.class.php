<?php ///Class file for textarea field, extends base_field
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

/// Please refer to lib.php for method comments

class data_field_multimenu extends data_field_base {

    var $type = 'multimenu';
    var $id;

    
    function data_field_multimenu($fid=0){
        parent::data_field_base($fid);
    }
    
    
    /***********************************************
     * Saves the field into the database           *
     ***********************************************/
    function insert_field($dataid, $type='multimenu', $name, $desc='', $options='') {
        $newfield = new object;
        $newfield->dataid = $dataid;
        $newfield->type = $type;
        $newfield->name = $name;
        $newfield->description = $desc;
        $newfield->param1 = $options;
        
        if (!insert_record('data_fields', $newfield)) {
            notify('Insertion of new field failed!');
        }
    }
    
    
    /***********************************************
     * Prints the form element in the add template *
     ***********************************************/
    function display_add_field($id, $rid=0) {
        global $CFG;
        if (!$field = get_record('data_fields', 'id', $id)){
            notify('That is not a valid field id!');
            exit;
        }
        if ($rid) {
            $content = get_record('data_content', 'fieldid', $id, 'recordid', $rid);
            if (isset($content->content)) {
                $content = $content->content;
                $content = explode('##', $content);
            }
        }
        else {
            $content = array();
        }
        $str = '';

        if ($field->description) {
            $str .= '<img src="'.$CFG->pixpath.'/help.gif" alt="'.$field->description.'" title="'.$field->description.'" />&nbsp;';
        }
        
        $str .= '<select name="field_' . $field->id . '[]" id="field_' . $field->id . '" multiple="multiple">';
        
        foreach (explode("\n",$field->param1) as $option) {
            $option = ltrim(rtrim($option));
            $str .= '<option value="' . $option . '"';

            if (array_search($option, $content) !== false) {
                // Selected by user.
                $str .= ' selected >';
            }
            else {
                $str .= '>';
            }
            $str .= $option . '</option>';
        }
        $str .= '</select>';
        
        return $str;
    }


    function display_edit_field($id, $mode=0) {
        parent::display_edit_field($id, $mode);
    }
    
    

    function update($fieldobject) {
        $fieldobject->param2 = trim($fieldobject->param1);
        
        if (!update_record('data_fields',$fieldobject)){
            notify ('upate failed');
        }
    }
    
    
    function store_data_content($fieldid, $recordid, $value) {
        $content = new object;
        $content->fieldid = $fieldid;
        $content->recordid = $recordid;
        $content->content = $this->format_data_field_multimenu_content($value);
        insert_record('data_content', $content);
    }
    
    
    function update_data_content($fieldid, $recordid, $value) {
        $content = new object;
        $content->fieldid = $fieldid;
        $content->recordid = $recordid;
        $content->content = $this->format_data_field_multimenu_content($value);
        
        if ($oldcontent = get_record('data_content', 'fieldid', $fieldid, 'recordid', $recordid)) {
            $content->id = $oldcontent->id;
            update_record('data_content', $content);
        }
        else {
            $this->store_data_content($fieldid, $recordid, $value);
        }
    }
    
    
    function format_data_field_multimenu_content($contentArr) {
         $str = '';
         foreach ($contentArr as $val) {
             $str .= $val . '##';
         }
         $str = substr($str, 0, -2);
         $str = clean_param($str, PARAM_NOTAGS);
         return $str;
    }
    
    
    function display_browse_field($fieldid, $recordid) {
        global $CFG, $USER, $course;

        $field = get_record('data_fields', 'id', $fieldid);

        if ($content = get_record('data_content', 'fieldid', $fieldid, 'recordid', $recordid)) {
            return $content->content;
        }
        return false;
    }
}
?>
