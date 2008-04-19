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
        global $CFG;

        if (is_array($value)){
            $content     = $value['selected'];
            $allrequired = $value['allrequired'] ? 'checked = "checked"' : '';
        } else {
            $content     = array();
            $allrequired = '';
        }

        static $c = 0;

        $str = '<select name="f_'.$this->field->id.'[]" multiple="multiple">';

        // display only used options
        $usedoptions = array();
        $sql = "SELECT DISTINCT content
                  FROM {$CFG->prefix}data_content
                 WHERE fieldid={$this->field->id} AND content IS NOT NULL";
        if ($used = get_records_sql($sql)) {
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

            if (in_array(addslashes($option), $content)) {
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

        $str .= '&nbsp;<input name="f_'.$this->field->id.'_allreq" id="f_'.$this->field->id.'_allreq'.$c.'" type="checkbox" '.$allrequired.'/>';
        $str .= '<label for="f_'.$this->field->id.'_allreq'.$c.'">'.get_string('selectedrequired', 'data').'</label>';
        $c++;

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
        $allrequired = $value['allrequired'];
        $selected    = $value['selected'];

        if ($selected) {
            $conditions = array();
            foreach ($selected as $sel) {
                $likesel = str_replace('%', '\%', $sel);
                $likeselsel = str_replace('_', '\_', $likesel);
                $conditions[] = "({$tablealias}.fieldid = {$this->field->id} AND ({$tablealias}.content = '$sel'
                                                                               OR {$tablealias}.content LIKE '$likesel##%'
                                                                               OR {$tablealias}.content LIKE '%##$likesel'
                                                                               OR {$tablealias}.content LIKE '%##$likesel##%'))";
            }
            if ($allrequired) {
                return " (".implode(" AND ", $conditions).") ";
            } else {
                return " (".implode(" OR ", $conditions).") ";
            }
        } else {
            return " ";
        }
    }

    function update_content($recordid, $value, $name='') {
        $content = new object;
        $content->fieldid  = $this->field->id;
        $content->recordid = $recordid;
        $content->content  = $this->format_data_field_multimenu_content($value);

        if ($oldcontent = get_record('data_content','fieldid', $this->field->id, 'recordid', $recordid)) {
            $content->id = $oldcontent->id;
            return update_record('data_content', $content);
        } else {
            return insert_record('data_content', $content);
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
            if (!in_array(stripslashes($val), $options)) {
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

        if ($content = get_record('data_content', 'fieldid', $this->field->id, 'recordid', $recordid)) {
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
?>
