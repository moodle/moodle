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

class data_field_multimenu extends data_field_base {

    var $type = 'multimenu';

    function display_add_field($recordid=0) {
        global $DB;

        if ($recordid){
            $content = $DB->get_field('data_content', 'content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid));
            $content = explode('##', $content);
        } else {
            $content = array();
        }

        $str = '<div title="'.s($this->field->description).'">';
        $str .= '<input name="field_' . $this->field->id . '[xxx]" type="hidden" value="xxx"/>'; // hidden field - needed for empty selection
        $str .= '<select name="field_' . $this->field->id . '[]" id="field_' . $this->field->id . '" multiple="multiple">';

        foreach (explode("\n",$this->field->param1) as $option) {
            $option = trim($option);
            $str .= '<option value="' . s($option) . '"';

            if (in_array($option, $content)) {
                // Selected by user.
                $str .= ' selected = "selected"';
            }

            $str .= '>';
            $str .= $option . '</option>';
        }
        $str .= '</select>';
        $str .= '</div>';

        return $str;
    }

    function display_search_field($value = '') {
        global $CFG, $DB;

        if (is_array($value)){
            $content     = $value['selected'];
            $allrequired = $value['allrequired'] ? true : false;
        } else {
            $content     = array();
            $allrequired = false;
        }

        static $c = 0;

        $str = '<select name="f_'.$this->field->id.'[]" multiple="multiple">';

        // display only used options
        $varcharcontent =  $DB->sql_compare_text('content', 255);
        $sql = "SELECT DISTINCT $varcharcontent AS content
                  FROM {data_content}
                 WHERE fieldid=? AND content IS NOT NULL";

        $usedoptions = array();
        if ($used = $DB->get_records_sql($sql, array($this->field->id))) {
            foreach ($used as $data) {
                $valuestr = $data->content;
                if ($valuestr === '') {
                    continue;
                }
                $values = explode('##', $valuestr);
                foreach ($values as $value) {
                    $usedoptions[$value] = $value;
                }
            }
        }

        $found = false;
        foreach (explode("\n",$this->field->param1) as $option) {
            $option = trim($option);
            if (!isset($usedoptions[$option])) {
                continue;
            }
            $found = true;
            $str .= '<option value="' . s($option) . '"';

            if (in_array($option, $content)) {
                // Selected by user.
                $str .= ' selected = "selected"';
            }
            $str .= '>' . $option . '</option>';
        }
        if (!$found) {
            // oh, nothing to search for
            return '';
        }

        $str .= '</select>';

        $str .= html_writer::checkbox('f_'.$this->field->id.'_allreq', null, $allrequired, get_string('selectedrequired', 'data'));

        return $str;

    }

    function parse_search_field() {
        $selected    = optional_param('f_'.$this->field->id, array(), PARAM_NOTAGS);
        $allrequired = optional_param('f_'.$this->field->id.'_allreq', 0, PARAM_BOOL);
        if (empty($selected)) {
            // no searching
            return '';
        }
        return array('selected'=>$selected, 'allrequired'=>$allrequired);
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $name = "df_multimenu_{$i}_";
        $params = array();
        $varcharcontent = $DB->sql_compare_text("{$tablealias}.content", 255);

        $allrequired = $value['allrequired'];
        $selected    = $value['selected'];

        if ($selected) {
            $conditions = array();
            $j=0;
            foreach ($selected as $sel) {
                $j++;
                $xname = $name.$j;
                $likesel = str_replace('%', '\%', $sel);
                $likeselsel = str_replace('_', '\_', $likesel);
                $conditions[] = "({$tablealias}.fieldid = {$this->field->id} AND ({$varcharcontent} = :{$xname}a
                                                                               OR {$tablealias}.content LIKE :{$xname}b
                                                                               OR {$tablealias}.content LIKE :{$xname}c
                                                                               OR {$tablealias}.content LIKE :{$xname}d))";
                $params[$xname.'a'] = $sel;
                $params[$xname.'b'] = "$likesel##%";
                $params[$xname.'c'] = "%##$likesel";
                $params[$xname.'d'] = "%##$likesel##%";
            }
            if ($allrequired) {
                return array(" (".implode(" AND ", $conditions).") ", $params);
            } else {
                return array(" (".implode(" OR ", $conditions).") ", $params);
            }
        } else {
            return array(" ", array());
        }
    }

    function update_content($recordid, $value, $name='') {
        global $DB;

        $content = new stdClass();
        $content->fieldid  = $this->field->id;
        $content->recordid = $recordid;
        $content->content  = $this->format_data_field_multimenu_content($value);

        if ($oldcontent = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            $content->id = $oldcontent->id;
            return $DB->update_record('data_content', $content);
        } else {
            return $DB->insert_record('data_content', $content);
        }
    }

    function format_data_field_multimenu_content($content) {
        if (!is_array($content)) {
            return NULL;
        }
        $options = explode("\n", $this->field->param1);
        $options = array_map('trim', $options);

        $vals = array();
        foreach ($content as $key=>$val) {
            if ($key === 'xxx') {
                continue;
            }
            if (!in_array($val, $options)) {
                continue;
            }
            $vals[] = $val;
        }

        if (empty($vals)) {
            return NULL;
        }

        return implode('##', $vals);
    }


    function display_browse_field($recordid, $template) {
        global $DB;

        if ($content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            if (empty($content->content)) {
                return false;
            }

            $options = explode("\n",$this->field->param1);
            $options = array_map('trim', $options);

            $contentArr = explode('##', $content->content);
            $str = '';
            foreach ($contentArr as $line) {
                if (!in_array($line, $options)) {
                    // hmm, looks like somebody edited the field definition
                    continue;
                }
                $str .= $line . "<br />\n";
            }
            return $str;
        }
        return false;
    }
}

