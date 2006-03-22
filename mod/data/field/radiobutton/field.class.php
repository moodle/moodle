<?php // $Id$
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-onwards Moodle Pty Ltd  http://moodle.com          //
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

class data_field_radiobutton extends data_field_base {

    var $type = 'radiobutton';
    
    function data_field_radiobutton($field=0, $data=0) {
        parent::data_field_base($field, $data);
    }
    
    
    function display_add_field($recordid=0) {
        global $CFG;

        if ($recordid){
            $content = trim(get_field('data_content', 'content', 'fieldid', $this->field->id, 'recordid', $recordid));
        } else {
            $content = '';
        }

        $str = '<div title="'.$this->field->description.'">';
        
        foreach (explode("\n",$this->field->param1) as $radio) {
            $radio = trim($radio);
            $str .= '<input type="radio" name="field_' . $this->field->id . '" ';
            $str .= 'value="' . $radio . '" ';

            if ($content == $radio) {
                // Selected by user.
                $str .= 'checked />';
            } else {
                $str .= '/>';
            }

            $str .= $radio . '<br />';
        }
        $str .= '</div>';
        return $str;
    }

}
?>
