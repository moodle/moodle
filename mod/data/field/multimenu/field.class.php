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
    /**
     * priority for globalsearch indexing
     *
     * @var int
     * */
    protected static $priority = self::LOW_PRIORITY;

    public function supports_preview(): bool {
        return true;
    }

    public function get_data_content_preview(int $recordid): stdClass {
        $options = explode("\n", $this->field->param1);
        $options = array_map('trim', $options);
        $selected = $options[$recordid % count($options)];
        $selected .= '##' . $options[($recordid + 1) % count($options)];
        return (object)[
            'id' => 0,
            'fieldid' => $this->field->id,
            'recordid' => $recordid,
            'content' => $selected,
            'content1' => null,
            'content2' => null,
            'content3' => null,
            'content4' => null,
        ];
    }

    function display_add_field($recordid = 0, $formdata = null) {
        global $DB, $OUTPUT;

        if ($formdata) {
            $fieldname = 'field_' . $this->field->id;
            if (isset($formdata->$fieldname)) {
                $content = $formdata->$fieldname;
            } else {
                $content = array();
            }
        } else if ($recordid) {
            $content = $DB->get_field('data_content', 'content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid));
            $content = explode('##', $content);
        } else {
            $content = array();
        }

        $str = '<div title="'.s($this->field->description).'">';
        $str .= '<input name="field_' . $this->field->id . '[xxx]" type="hidden" value="xxx"/>'; // hidden field - needed for empty selection

        $str .= '<label for="field_' . $this->field->id . '">';
        $str .= '<legend><span class="accesshide">' . $this->field->name;

        if ($this->field->required) {
            $str .= '&nbsp;' . get_string('requiredelement', 'form') . '</span></legend>';
            $str .= '<div class="inline-req">';
            $str .= $OUTPUT->pix_icon('req', get_string('requiredelement', 'form'));
            $str .= '</div>';
        } else {
            $str .= '</span></legend>';
        }
        $str .= '</label>';
        $str .= '<select name="field_' . $this->field->id . '[]" id="field_' . $this->field->id . '"';
        $str .= ' multiple="multiple" class="mod-data-input form-control">';

        foreach (explode("\n", $this->field->param1) as $option) {
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

        $str = '<label class="accesshide" for="f_' . $this->field->id . '">' . $this->field->name . '</label>';
        $str .= '<select id="f_'.$this->field->id.'" name="f_'.$this->field->id.'[]" multiple="multiple" class="form-control">';

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

        $str .= html_writer::checkbox('f_'.$this->field->id.'_allreq', null, $allrequired,
            get_string('selectedrequired', 'data'), array('class' => 'mr-1'));

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
        $content = $this->get_data_content($recordid);
        if (!$content || empty($content->content)) {
            return '';
        }
        $options = explode("\n", $this->field->param1);
        $options = array_map('trim', $options);

        $contentarray = explode('##', $content->content);
        $str = '';
        foreach ($contentarray as $line) {
            if (!in_array($line, $options)) {
                // Hmm, looks like somebody edited the field definition.
                continue;
            }
            $str .= $line . "<br />\n";
        }
        return $str;
    }

    /**
     * Check if a field from an add form is empty
     *
     * @param mixed $value
     * @param mixed $name
     * @return bool
     */
    function notemptyfield($value, $name) {
        unset($value['xxx']);
        return !empty($value);
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
