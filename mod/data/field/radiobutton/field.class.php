<?php
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

    function display_add_field($recordid = 0, $formdata = null) {
        global $CFG, $DB;

        if ($formdata) {
            $fieldname = 'field_' . $this->field->id;
            $content = $formdata->$fieldname;
        } else if ($recordid){
            $content = trim($DB->get_field('data_content', 'content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid)));
        } else {
            $content = '';
        }
        $str = '';
        if ($this->field->required) {
            $str .= '<div title="' . get_string('requiredfieldhint', 'data', s($this->field->description)) . '">';
        } else {
            $str .= '<div title="' . s($this->field->description) . '">';
        }
        $str .= '<fieldset><legend><span class="accesshide">'.$this->field->name.'</span></legend>';

        $i = 0;
        $requiredstr = '';
        if ($this->field->required) {
            $requiredstr = '<span class="requiredfield">' . get_string('requiredfieldshort', 'data') . '</span>';
        }
        $options = explode("\n",$this->field->param1);
        foreach ($options as $radio) {
            $radio = trim($radio);
            if ($radio === '') {
                continue; // skip empty lines
            }
            $str .= '<input type="radio" id="field_'.$this->field->id.'_'.$i.'" name="field_' . $this->field->id . '" ';
            $str .= 'value="' . s($radio) . '" ';

            if ($content == $radio) {
                // Selected by user.
                $str .= 'checked />';
            } else {
                $str .= '/>';
            }

            $str .= '<label for="field_'.$this->field->id.'_'.$i.'">'.$radio.'</label>';
            if ($i == count($options) - 1) {
                $str .= $requiredstr;
            }
            $str .= '<br />';
            $i++;
        }
        $str .= '</fieldset>';
        $str .= '</div>';
        return $str;
    }

     function display_search_field($value = '') {
        global $CFG, $DB;

        $varcharcontent = $DB->sql_compare_text('content', 255);
        $used = $DB->get_records_sql(
            "SELECT DISTINCT $varcharcontent AS content
               FROM {data_content}
              WHERE fieldid=?
             ORDER BY $varcharcontent", array($this->field->id));

        $options = array();
        if(!empty($used)) {
            foreach ($used as $rec) {
                $options[$rec->content] = $rec->content;  //Build following indicies from the sql.
            }
        }
        $return = html_writer::label(get_string('nameradiobutton', 'data'), 'menuf_'. $this->field->id, false, array('class' => 'accesshide'));
        $return .= html_writer::select($options, 'f_'.$this->field->id, $value);
        return $return;
    }

    function parse_search_field() {
        return optional_param('f_'.$this->field->id, '', PARAM_NOTAGS);
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $name = "df_radiobutton_$i";
        $varcharcontent = $DB->sql_compare_text("{$tablealias}.content", 255);

        return array(" ({$tablealias}.fieldid = {$this->field->id} AND $varcharcontent = :$name) ", array($name=>$value));
    }

}

