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

class data_field_textarea extends data_field_base {

    var $type = 'textarea';
    var $id;

    
    function data_field_textarea($fid=0){
        parent::data_field_base($fid);
    }
    
    
    /***********************************************
     * Saves the field into the database           *
     ***********************************************/
    function insert_field($dataid, $type='textarea', $name, $desc='', $autolink=0, $width='', $height='') {
        $newfield = new object;
        $newfield->dataid = $dataid;
        $newfield->type = $type;
        $newfield->name = $name;
        $newfield->description = $desc;
        $newfield->param1 = $autolink;
        $newfield->param2 = $width;
        $newfield->param3 = $height;
        
        echo '<pre>';
        print_r($newfield);
        echo '</pre>';
        
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
            }
        }
        else {
            $content = '';
        }
        $str = '';

        if ($field->description) {
            $str .= '<img src="'.$CFG->pixpath.'/help.gif" alt="'.$field->description.'" title="'.$field->description.'" />&nbsp;';
        }
        $str .= '<textarea name="field_' . $field->id . '" id="field_'.$field->id . '"';
        if (!empty($field->param2) && !empty($field->param3)) {
            $str .= ' style="width:' . $field->param2. 'px; height:' . $field->param3 . 'px;"';
        }
        $str .= '>' . $content . '</textarea>';
        return $str;
    }


    function display_edit_field($id, $mode=0) {
        parent::display_edit_field($id, $mode);
    }
        

    function update($fieldobject) {
        $fieldobject->param1 = trim($fieldobject->param1);
        $fieldobject->param2 = trim($fieldobject->param2);
        
        if (!update_record('data_fields',$fieldobject)){
            notify ('upate failed');
        }
    }
}
?>
