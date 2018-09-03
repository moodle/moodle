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

class data_field_checkbox extends data_field_base {

    var $type = 'checkbox';
    /**
     * priority for globalsearch indexing
     *
     * @var int
     */
    protected static $priority = self::LOW_PRIORITY;

    function display_add_field($recordid = 0, $formdata = null) {
        global $CFG, $DB, $OUTPUT;

        $content = array();

        if ($formdata) {
            $fieldname = 'field_' . $this->field->id;
            $content = $formdata->$fieldname;
        } else if ($recordid) {
            $content = $DB->get_field('data_content', 'content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid));
            $content = explode('##', $content);
        } else {
            $content = array();
        }

        $str = '<div title="' . s($this->field->description) . '">';
        $str .= '<fieldset><legend><span class="accesshide">'.$this->field->name;
        if ($this->field->required) {
            $str .= '$nbsp;' . get_string('requiredelement', 'form');
            $str .= '</span></legend>';
            $image = $OUTPUT->pix_icon('req', get_string('requiredelement', 'form'));
            $str .= html_writer::div($image, 'inline-req');
        } else {
            $str .= '</span></legend>';
        }

        $i = 0;
        foreach (explode("\n", $this->field->param1) as $checkbox) {
            $checkbox = trim($checkbox);
            if ($checkbox === '') {
                continue; // skip empty lines
            }
            $str .= '<input type="hidden" name="field_' . $this->field->id . '[]" value="" />';
            $str .= '<input type="checkbox" id="field_'.$this->field->id.'_'.$i.'" name="field_' . $this->field->id . '[]" ';
            $str .= 'value="' . s($checkbox) . '" class="mod-data-input m-r-1" ';

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

    function display_search_field($value='') {
        global $CFG, $DB;

        if (is_array($value)) {
            $content = $value['checked'];
            $allrequired = $value['allrequired'] ? true : false;
        } else {
            $content = array();
            $allrequired = false;
        }

        $str = '';
        $found = false;
        $marginclass = ['class' => 'm-r-1'];
        foreach (explode("\n",$this->field->param1) as $checkbox) {
            $checkbox = trim($checkbox);
            if (in_array($checkbox, $content)) {
                $str .= html_writer::checkbox('f_'.$this->field->id.'[]', s($checkbox), true, $checkbox, $marginclass);
            } else {
                $str .= html_writer::checkbox('f_'.$this->field->id.'[]', s($checkbox), false, $checkbox, $marginclass);
            }
            $str .= html_writer::empty_tag('br');
            $found = true;
        }
        if (!$found) {
            return '';
        }

        $requiredstr = get_string('selectedrequired', 'data');
        $str .= html_writer::checkbox('f_'.$this->field->id.'_allreq', null, $allrequired, $requiredstr, $marginclass);
        return $str;
    }

    public function parse_search_field($defaults = null) {
        $paramselected = 'f_'.$this->field->id;
        $paramallrequired = 'f_'.$this->field->id.'_allreq';

        if (empty($defaults[$paramselected])) { // One empty means the other ones are empty too.
            $defaults = array($paramselected => array(), $paramallrequired => 0);
        }

        $selected    = optional_param_array($paramselected, $defaults[$paramselected], PARAM_NOTAGS);
        $allrequired = optional_param($paramallrequired, $defaults[$paramallrequired], PARAM_BOOL);

        if (empty($selected)) {
            // no searching
            return '';
        }
        return array('checked'=>$selected, 'allrequired'=>$allrequired);
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $name = "df_checkbox_{$i}_";
        $params = array();
        $varcharcontent = $DB->sql_compare_text("{$tablealias}.content", 255);

        $allrequired = $value['allrequired'];
        $selected    = $value['checked'];

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
        $content->fieldid = $this->field->id;
        $content->recordid = $recordid;
        $content->content = $this->format_data_field_checkbox_content($value);

        if ($oldcontent = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            $content->id = $oldcontent->id;
            return $DB->update_record('data_content', $content);
        } else {
            return $DB->insert_record('data_content', $content);
        }
    }

    function display_browse_field($recordid, $template) {
        global $DB;

        if ($content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            if (strval($content->content) === '') {
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

    function format_data_field_checkbox_content($content) {
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

    /**
     * Check whether any boxes in the checkbox where checked.
     *
     * @param mixed $value The submitted values
     * @param mixed $name
     * @return bool
     */
    function notemptyfield($value, $name) {
        $found = false;
        foreach ($value as $checkboxitem) {
            if (strval($checkboxitem) !== '') {
                $found = true;
                break;
            }
        }
        return $found;
    }

    /**
     * Returns the presentable string value for a field content.
     *
     * The returned string should be plain text.
     *
     * @param stdClass $content
     * @return string
     */
    public static function get_content_value($content) {
        $arr = explode('##', $content->content);

        $strvalue = '';
        foreach ($arr as $a) {
            $strvalue .= $a . ' ';
        }

        return trim($strvalue, "\r\n ");
    }

    /**
     * Return the plugin configs for external functions.
     *
     * @return array the list of config parameters
     * @since Moodle 3.3
     */
    public function get_config_for_external() {
        // Return all the config parameters.
        $configs = [];
        for ($i = 1; $i <= 10; $i++) {
            $configs["param$i"] = $this->field->{"param$i"};
        }
        return $configs;
    }
}
