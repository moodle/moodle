<?php  // $Id$
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

class data_field_checkbox extends data_field_base {

    var $type = 'checkbox';
    
    function data_field_checkbox($field=0, $data=0) {
        parent::data_field_base($field, $data);
    }
    
    function display_add_field($recordid=0) {
        global $CFG;
       
        $content = array();

        if ($recordid) {
            $content = get_field('data_content', 'content', 'fieldid', $this->field->id, 'recordid', $recordid);
            $content = explode('##', $content);
        }

        $str = '<div title="'.s($this->field->description).'">';
        $str .= '<fieldset><legend><span class="accesshide">'.$this->field->name.'</span></legend>';
 
        $i = 0;
        foreach (explode("\n", $this->field->param1) as $checkbox) {
            $checkbox = trim($checkbox);
            if ($checkbox === '') {
                continue; // skip empty lines
            }
            $str .= '<input type="checkbox" id="field_'.$this->field->id.'_'.$i.'" name="field_' . $this->field->id . '[]" ';
            $str .= 'value="' . s($checkbox) . '" ';
            
            if (array_search($checkbox, $content) !== false) {
                $str .= 'checked />';
            } else {
                $str .= '/>';
            }
            $str .= '<label for="field_'.$this->field->id.'_'.$i.'">'.$checkbox.'</label><br />';
            $i++;
        }
        $str .= '</fieldset>'; 
        $str .= '</div>';
        return $str;
    }

    function update_content($recordid, $value, $name='') {
        $content = new object();
        $content->fieldid = $this->field->id;
        $content->recordid = $recordid;
        $content->content = $this->format_data_field_checkbox_content($value);
       
        if ($oldcontent = get_record('data_content','fieldid', $this->field->id, 'recordid', $recordid)) {
            $content->id = $oldcontent->id;
            return update_record('data_content', $content);
        } else {
            return insert_record('data_content', $content);
        }
    }
    
    function display_browse_field($recordid, $template) {
        
        if ($content = get_record('data_content', 'fieldid', $this->field->id, 'recordid', $recordid)){
            $contentArr = array();
            if (!empty($content->content)) {
                $contentArr = explode('##', $content->content);
            }
            $str = '';
            foreach ($contentArr as $line) {
                $str .= $line . "<br />\n";
            }
            return $str;
        }
        return false;
    }

    function format_data_field_checkbox_content($content) {
        if (!is_array($content)) {
            $str = $content;
        } else {
            $str = '';
            foreach ($content as $val) {
                $str .= $val . '##';
            }
            $str = substr($str, 0, -2);
        }
        $str = clean_param($str, PARAM_NOTAGS);
        return $str;
    }
}
?>
