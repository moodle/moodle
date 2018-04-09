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
    /**
     * priority for globalsearch indexing
     *
     * @var int
     */
    protected static $priority = self::HIGH_PRIORITY;

    function display_add_field($recordid = 0, $formdata = null) {
        global $CFG, $DB, $OUTPUT;

        if ($formdata) {
            $fieldname = 'field_' . $this->field->id;
            if (isset($formdata->$fieldname)) {
                $content = $formdata->$fieldname;
            } else {
                $content = '';
            }
        } else if ($recordid) {
            $content = trim($DB->get_field('data_content', 'content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid)));
        } else {
            $content = '';
        }

        $str = '<div title="' . s($this->field->description) . '">';
        $str .= '<fieldset><legend><span class="accesshide">' . $this->field->name;

        if ($this->field->required) {
            $str .= '&nbsp;' . get_string('requiredelement', 'form') . '</span></legend>';
            $image = $OUTPUT->pix_icon('req', get_string('requiredelement', 'form'));
            $str .= html_writer::div($image, 'inline-req');
        } else {
            $str .= '</span></legend>';
        }

        $i = 0;
        $requiredstr = '';
        $options = explode("\n", $this->field->param1);
        foreach ($options as $radio) {
            $radio = trim($radio);
            if ($radio === '') {
                continue; // skip empty lines
            }
            $str .= '<input type="radio" id="field_'.$this->field->id.'_'.$i.'" name="field_' . $this->field->id . '" ';
            $str .= 'value="' . s($radio) . '" class="mod-data-input m-r-1" ';

            if ($content == $radio) {
                // Selected by user.
                $str .= 'checked />';
            } else {
                $str .= '/>';
            }

            $str .= '<label for="field_'.$this->field->id.'_'.$i.'">'.$radio.'</label><br />';
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
        $return = html_writer::label(get_string('fieldtypelabel', "datafield_" . $this->type),
            'menuf_' . $this->field->id, false, array('class' => 'accesshide'));
        $return .= html_writer::select($options, 'f_'.$this->field->id, $value, null, array('class' => 'custom-select'));
        return $return;
    }

    public function parse_search_field($defaults = null) {
        $param = 'f_'.$this->field->id;
        if (empty($defaults[$param])) {
            $defaults = array($param => '');
        }
        return optional_param($param, $defaults[$param], PARAM_NOTAGS);
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $name = "df_radiobutton_$i";
        $varcharcontent = $DB->sql_compare_text("{$tablealias}.content", 255);

        return array(" ({$tablealias}.fieldid = {$this->field->id} AND $varcharcontent = :$name) ", array($name=>$value));
    }

    /**
     * Check if a field from an add form is empty
     *
     * @param mixed $value
     * @param mixed $name
     * @return bool
     */
    function notemptyfield($value, $name) {
        return strval($value) !== '';
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

