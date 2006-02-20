<?php
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

global $CFG;

require_once($CFG->dirroot.'/mod/data/lib.php');

class data_field_menu extends data_field_base {

    function data_field_menu($fid=0){
        parent::data_field_base($fid);
    }

    var $type = 'menu';
    var $id;    //field id

    function insert_field($dataid, $type='menu', $name, $des='') {
        $newfield = new object;
        $newfield->dataid = $dataid;
        $newfield->type = $type;
        $newfield->name = $name;
        $newfield->description = $des;
        if (!insert_record('data_fields',$newfield)) {
            notify('Insertion of new field failed!');
        }
    }

    function display_add_field($id, $rid=0) {
        global $CFG;
        if (!$field = get_record('data_fields','id',$id)) {
            notify("that is not a valid field id!");
            exit;
        }
        $content = '';
        
        //look for that record and pull it out
        if ($rid) {
            $datacontent = get_record('data_content','fieldid',$id,'recordid',$rid);
            if (isset($datacontent->content)) {
                $content = $datacontent->content;
            }
        }

        $str = '<div title="'.$field->description.'"><table><tr><td>';
        /*
        if ($field->description) {
            $str .= '<img src="'.$CFG->pixpath.'/help.gif" alt="'.$field->description.'" title="'.$field->description.'">&nbsp;';
        }
        */
        $str .= get_string('menu','data').': </td><td>';
        
        $str .= '<select name="field_'.$field->id.'" id="field_'.$field->id.'">';
        $str .= '<option value="">' . get_string('menuchoose', 'data') . '</option>';
        
        foreach (explode("\n",$field->param1) as $option) {
            if (trim($content) == trim($option)) {    // If selected
                $str.='<option value="'.ltrim(rtrim($option)).'" selected="selected">'.ltrim(rtrim($option)).'</option>';
            } else {
                $str.='<option value="'.ltrim(rtrim($option)).'">'.ltrim(rtrim($option)).'</option>';
            }
        }
        $str .= '</select></td></tr></table></div>';

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
