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
// (at your option) any later version.                                   // //                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once($CFG->dirroot.'/lib/filelib.php');
require_once($CFG->dirroot.'/repository/lib.php');

class data_field_textarea extends data_field_base {

    var $type = 'textarea';

    function display_add_field($recordid=0) {
        global $CFG, $DB, $OUTPUT, $PAGE;

        $text   = '';
        $format = 0;

        $str = '<div title="'.$this->field->description.'">';

        editors_head_setup();

        $options = array();
        $options['trusttext'] = false;
        $options['forcehttps'] = false;
        $options['subdirs'] = false;
        $options['maxfiles'] = 0;
        $options['maxbytes'] = 0;
        $options['changeformat'] = 0;
        $options['noclean'] = false;

        $itemid = $this->field->id;
        $field = 'field_'.$itemid;

        if ($recordid && $content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))){
            $text   = $content->content;
            $format = $content->content1;
            $text = clean_text($text, $format);
        } else if (can_use_html_editor()) {
            $format = FORMAT_HTML;
        } else {
            $format = FORMAT_PLAIN;
        }

        $editor = editors_get_preferred_editor($format);
        $strformats = format_text_menu();
        $formats =  $editor->get_supported_formats();
        foreach ($formats as $fid) {
            $formats[$fid] = $strformats[$fid];
        }
        $editor->use_editor($field, $options);
        $str .= '<div><textarea id="'.$field.'" name="'.$field.'" rows="'.$this->field->param3.'" cols="'.$this->field->param2.'">'.s($text).'</textarea></div>';
        $str .= '<div><select name="'.$field.'_content1">';
        foreach ($formats as $key=>$desc) {
            $selected = ($format == $key) ? 'selected="selected"' : '';
            $str .= '<option value="'.s($key).'" '.$selected.'>'.$desc.'</option>';
        }
        $str .= '</select>';
        $str .= '</div>';

        $str .= '</div>';
        return $str;
    }


    function display_search_field($value = '') {
        return '<input type="text" size="16" name="f_'.$this->field->id.'" value="'.$value.'" />';
    }

    function parse_search_field() {
        return optional_param('f_'.$this->field->id, '', PARAM_NOTAGS);
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $name = "df_textarea_$i";
        return array(" ({$tablealias}.fieldid = {$this->field->id} AND ".$DB->sql_like("{$tablealias}.content", ":$name", false).") ", array($name=>"%$value%"));
    }

    function print_after_form() {
    }


    function update_content($recordid, $value, $name='') {
        global $DB;

        $content = new stdClass();
        $content->fieldid = $this->field->id;
        $content->recordid = $recordid;

        $names = explode('_', $name);
        if (!empty($names[2])) {
            $content->$names[2] = clean_param($value, PARAM_NOTAGS);  // content[1-4]
        } else {
            $content->content = clean_param($value, PARAM_CLEAN);
        }

        if ($oldcontent = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            $content->id = $oldcontent->id;
            return $DB->update_record('data_content', $content);
        } else {
            return $DB->insert_record('data_content', $content);
        }
    }
}

