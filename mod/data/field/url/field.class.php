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

class data_field_url extends data_field_base {
    var $type = 'url';

    function data_field_text($field=0, $data=0) {
        parent::data_field_base($field, $data);
    }

    function display_add_field($recordid=0) {
        global $CFG;
        $url = '';
        $text = '';
        if ($recordid) {
            if ($content = get_record('data_content', 'fieldid', $this->field->id, 'recordid', $recordid)) {
                $url  = $content->content;
                $text = $content->content1;
            }
        }
        $url = empty($url) ? 'http://' : $url;
        $str = '<div title="'.s($this->field->description).'">';
        if (!empty($this->field->param1) and empty($this->field->param2)) {
            $str .= '<table><tr><td align="right">';
            $str .= get_string('url','data').':</td><td><input type="text" name="field_'.$this->field->id.'_0" id="field_'.$this->field->id.'_0" value="'.$url.'" size="60" /></td></tr>';
            $str .= '<tr><td align="right">'.get_string('text','data').':</td><td><input type="text" name="field_'.$this->field->id.'_1" id="field_'.$this->field->id.'_1" value="'.s($text).'" size="60" /></td></tr>';
            $str .= '</table>';
        } else {
            // Just the URL field
            $str .= '<input type="text" name="field_'.$this->field->id.'_0" id="field_'.$this->field->id.'_0" value="'.s($url).'" size="60" />';
        }
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
        return " ({$tablealias}.fieldid = {$this->field->id} AND {$tablealias}.content LIKE '%{$value}%') ";
    }

    function display_browse_field($recordid, $template) {
        if ($content = get_record('data_content', 'fieldid', $this->field->id, 'recordid', $recordid)) {
            $url = empty($content->content)? '':$content->content;
            $text = empty($content->content1)? '':$content->content1;
            if (empty($url) or ($url == 'http://')) {
                return '';
            }
            if (!empty($this->field->param2)) {
                // param2 forces the text to something
                $text = $this->field->param2;
            }
            if ($this->field->param1) {
                // param1 defines whether we want to autolink the url.
                if (!empty($text)) {
                    $str = '<a href="'.$url.'">'.$text.'</a>';
                } else {
                    $str = '<a href="'.$url.'">'.$url.'</a>';
                }
            } else {
                $str = $url;
            }
            return $str;
        }
        return false;
    }

    function update_content($recordid, $value, $name='') {
        $content = new object;
        $content->fieldid = $this->field->id;
        $content->recordid = $recordid;
        $names = explode('_', $name);
        switch ($names[2]) {
            case 0:
                // update link
                $content->content = clean_param($value, PARAM_URL);
                break;
            case 1:
                // add text
                $content->content1 = clean_param($value, PARAM_NOTAGS);
                break;
            default:
                break;
        }

        if ($oldcontent = get_record('data_content','fieldid', $this->field->id, 'recordid', $recordid)) {
            $content->id = $oldcontent->id;
            return update_record('data_content', $content);
        } else {
            return insert_record('data_content', $content);
        }
    }

    function notemptyfield($value, $name) {
        $names = explode('_',$name);
        $value = clean_param($value, PARAM_URL);
        //clean first
        if ($names[2] == '0') {
            return ($value!='http://' && !empty($value));
        }
        return false;
    }

    function export_text_value($record) {
        return $record->content . " " . $record->content1;
    }

}

?>
