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

class data_field_radiobutton extends data_field_base {

    var $type = 'radiobutton';
    var $id;

    
    function data_field_radiobutton($fid=0){
        parent::data_field_base($fid);
    }
    
    
    /***********************************************
     * Saves the field into the database           *
     ***********************************************/
    function insert_field($dataid, $type='radiobutton', $name, $desc='', $options='') {
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
            }
        }
        else {
            $content = '';
        }
        $str = '';
        /*
        if ($field->description) {
            $str .= '<img src="'.$CFG->pixpath.'/help.gif" alt="'.$field->description.'" title="'.$field->description.'" />&nbsp;';
        }
        */
        $str .= '<div title="'.$field->description.'">';
        
        foreach (explode("\n",$field->param1) as $radio) {
            $radio = ltrim(rtrim($radio));
            $str .= '<input type="radio" name="field_' . $field->id . '" id="field_';
            $str .= $field->id . '" value="' . $radio . '" ';

            if ($content == $radio) {
                // Selected by user.
                $str .= 'checked />';
            }
            else {
                $str .= '/>';
            }

            $str .= $radio . '<br />';
        }
        $str .= '</div>';
        return $str;
    }


    function display_edit_field($id, $mode=0) {
        parent::display_edit_field($id, $mode);
    }
        

    function update($fieldobject) {
        $fieldobject->param1 = trim($fieldobject->param1);
        
        if (!update_record('data_fields',$fieldobject)){
            notify ('upate failed');
        }
    }
}
?>
