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

class data_field_date extends data_field_base {

    var $type = 'date';

    var $day   = 0;
    var $month = 0;
    var $year  = 0;

    function data_field_date($field=0, $data=0) {
        parent::data_field_base($field, $data);
    }

    function display_add_field($recordid=0) {

        if ($recordid) {
            $content = (int) get_field('data_content', 'content', 'fieldid', $this->field->id, 'recordid', $recordid);
        } else {
            $content = time();
        }

        $str = '<div title="'.s($this->field->description).'">';
        $str .= print_date_selector('field_'.$this->field->id.'_day', 'field_'.$this->field->id.'_month',
                                    'field_'.$this->field->id.'_year', $content, true);
        $str .= '</div>';

        return $str;
    }

    function update_content($recordid, $value, $name='') {

        $names = explode('_',$name);
        $name = $names[2];          // day month or year

        $this->$name = $value;

        if ($this->day and $this->month and $this->year) {  // All of them have been collected now

            $content = new object;
            $content->fieldid = $this->field->id;
            $content->recordid = $recordid;
            $content->content = make_timestamp($this->year, $this->month, $this->day, 12, 0, 0, 0, false);

            if ($oldcontent = get_record('data_content','fieldid', $this->field->id, 'recordid', $recordid)) {
                $content->id = $oldcontent->id;
                return update_record('data_content', $content);
            } else {
                return insert_record('data_content', $content);
            }
        }
    }

    function display_browse_field($recordid, $template) {

        global $CFG;

        if ($content = get_field('data_content', 'content', 'fieldid', $this->field->id, 'recordid', $recordid)){
            return userdate($content, get_string('strftimedate'), 0);
        }
    }

    function get_sort_sql($fieldname) {
        return 'CAST('.$fieldname.' AS unsigned)';
    }


}

?>
