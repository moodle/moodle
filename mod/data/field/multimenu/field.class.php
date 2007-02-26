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

class data_field_multimenu extends data_field_base {

    var $type = 'multimenu';

    function data_field_multimenu($field=0, $data=0) {
        parent::data_field_base($field, $data);
    }


    function display_add_field($recordid=0) {

        if ($recordid){
            $content = get_field('data_content', 'content', 'fieldid', $this->field->id, 'recordid', $recordid);
            $content = explode('##', $content);
        } else {
            $content = array();
        }

        $str = '<div title="'.s($this->field->description).'">';
        $str .= '<select name="field_' . $this->field->id . '[]" id="field_' . $this->field->id . '" multiple="multiple">';

        foreach (explode("\n",$this->field->param1) as $option) {
            $option = trim($option);
            $str .= '<option value="' . s($option) . '"';

            if (array_search($option, $content) !== false) {
                // Selected by user.
                $str .= ' selected >';
            } else {
                $str .= '>';
            }
            $str .= $option . '</option>';
        }
        $str .= '</select>';
        $str .= '</div>';

        return $str;
    }
    
    function display_search_field($value = '') {
        global $CFG;
        $temp = get_records_sql_menu('SELECT id, content from '.$CFG->prefix.'data_content WHERE fieldid='.$this->field->id.' GROUP BY content ORDER BY content');
        $options = array();
        if(!empty($temp)) {
            $options[''] = '';              //Make first index blank.
            foreach ($temp as $key) {
                $options[$key] = $key;  //Build following indicies from the sql.
            }
        }
        return choose_from_menu($options, 'f_'.$this->field->id, $value, 'choose', '',  0, true);
    }
    
    function parse_search_field() {
        return optional_param('f_'.$this->field->id, '', PARAM_NOTAGS);
    }

    function generate_sql($tablealias, $value) {
        return " ({$tablealias}.fieldid = {$this->field->id} AND {$tablealias}.content = '$value') "; 
    }

    function update_content($recordid, $value, $name='') {
        $content = new object;
        $content->fieldid = $this->field->id;
        $content->recordid = $recordid;
        $content->content = $this->format_data_field_multimenu_content($value);

        if ($oldcontent = get_record('data_content','fieldid', $this->field->id, 'recordid', $recordid)) {
            $content->id = $oldcontent->id;
            return update_record('data_content', $content);
        } else {
            return insert_record('data_content', $content);
        }
    }

    function format_data_field_multimenu_content($content) {
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
}
?>
