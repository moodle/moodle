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

//2/19/07:  Advanced search of the date field is currently disabled because it does not track
// pre 1970 dates and does not handle blank entrys.  Advanced search functionality for this field
// type can be enabled once these issues are addressed in the core API.

class data_field_date extends data_field_base {

    var $type = 'date';

    var $day   = 0;
    var $month = 0;
    var $year  = 0;

    function display_add_field($recordid=0) {
        global $DB, $OUTPUT;

        if ($recordid) {
            $content = (int)$DB->get_field('data_content', 'content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid));
        } else {
            $content = time();
        }

        $str = '<div title="'.s($this->field->description).'">';
        $dayselector = html_writer::select_time('days', 'field_'.$this->field->id.'_day', $content);
        $monthselector = html_writer::select_time('months', 'field_'.$this->field->id.'_month', $content);
        $yearselector = html_writer::select_time('years', 'field_'.$this->field->id.'_year', $content);
        $str .= $dayselector . $monthselector . $yearselector;
        $str .= '</div>';

        return $str;
    }

    //Enable the following three functions once core API issues have been addressed.
    function display_search_field($value=0) {
        $selectors = html_writer::select_time('days', 'f_'.$this->field->id.'_d', $value)
           . html_writer::select_time('months', 'f_'.$this->field->id.'_m', $value)
           . html_writer::select_time('years', 'f_'.$this->field->id.'_y', $value);
       return $selectors;

        //return print_date_selector('f_'.$this->field->id.'_d', 'f_'.$this->field->id.'_m', 'f_'.$this->field->id.'_y', $value, true);
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $name = "df_date_$i";
        $varcharcontent = $DB->sql_compare_text("{$tablealias}.content");
        return array(" ({$tablealias}.fieldid = {$this->field->id} AND $varcharcontent = :$name) ", array($name=>$value));
    }

    function parse_search_field() {

        $day   = optional_param('f_'.$this->field->id.'_d', 0, PARAM_INT);
        $month = optional_param('f_'.$this->field->id.'_m', 0, PARAM_INT);
        $year  = optional_param('f_'.$this->field->id.'_y', 0, PARAM_INT);
        if (!empty($day) && !empty($month) && !empty($year)) {
            return make_timestamp($year, $month, $day, 12, 0, 0, 0, false);
        }
        else {
            return 0;
        }

    }

    function update_content($recordid, $value, $name='') {
        global $DB;

        $names = explode('_',$name);
        $name = $names[2];          // day month or year

        $this->$name = $value;

        if ($this->day and $this->month and $this->year) {  // All of them have been collected now

            $content = new stdClass();
            $content->fieldid = $this->field->id;
            $content->recordid = $recordid;
            $content->content = make_timestamp($this->year, $this->month, $this->day, 12, 0, 0, 0, false);

            if ($oldcontent = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
                $content->id = $oldcontent->id;
                return $DB->update_record('data_content', $content);
            } else {
                return $DB->insert_record('data_content', $content);
            }
        }
    }

    function display_browse_field($recordid, $template) {
        global $CFG, $DB;

        if ($content = $DB->get_field('data_content', 'content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            return userdate($content, get_string('strftimedate'), 0);
        }
    }

    function get_sort_sql($fieldname) {
        global $DB;
        return $DB->sql_cast_char2int($fieldname, true);
    }


}


