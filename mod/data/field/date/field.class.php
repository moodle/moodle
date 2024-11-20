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

    public function supports_preview(): bool {
        return true;
    }

    public function get_data_content_preview(int $recordid): stdClass {
        return (object)[
            'id' => 0,
            'fieldid' => $this->field->id,
            'recordid' => $recordid,
            'content' => (string) time(),
            'content1' => null,
            'content2' => null,
            'content3' => null,
            'content4' => null,
        ];
    }

    function display_add_field($recordid = 0, $formdata = null) {
        global $DB, $OUTPUT;

        if ($formdata) {
            $fieldname = 'field_' . $this->field->id . '_day';
            $day   = $formdata->$fieldname;
            $fieldname = 'field_' . $this->field->id . '_month';
            $month   = $formdata->$fieldname;
            $fieldname = 'field_' . $this->field->id . '_year';
            $year   = $formdata->$fieldname;

            $calendartype = \core_calendar\type_factory::get_calendar_instance();
            $gregoriandate = $calendartype->convert_to_gregorian($year, $month, $day);
            $content = make_timestamp(
                $gregoriandate['year'],
                $gregoriandate['month'],
                $gregoriandate['day'],
                $gregoriandate['hour'],
                $gregoriandate['minute'],
                0,
                0,
                false);
        } else if ($recordid) {
            $content = (int)$DB->get_field('data_content', 'content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid));
        } else {
            $content = time();
        }

        $str = '<div title="'.s($this->field->description).'" class="mod-data-input d-flex flex-wrap align-items-center">';

        $dayselector = html_writer::select_time(
            type: 'days',
            name: "field_{$this->field->id}_day",
            currenttime: $content,
            timezone: 0,
        );
        $monthselector = html_writer::select_time(
            type: 'months',
            name: "field_{$this->field->id}_month",
            currenttime: $content,
            timezone: 0,
        );
        $yearselector = html_writer::select_time(
            type: 'years',
            name: "field_{$this->field->id}_year",
            currenttime: $content,
            timezone: 0,
        );

        $str .= $dayselector . $monthselector . $yearselector;
        $str .= '</div>';

        return $str;
    }

    // Enable the following three functions once core API issues have been addressed.

    /**
     * Display the search field in advanced search page
     * @param mixed $value
     * @return string
     * @throws coding_exception
     */
    public function display_search_field($value = null) {
        $currenttime = time();
        $selectors = html_writer::select_time('days', 'f_' . $this->field->id . '_d', $value['timestamp'] ?? $currenttime)
            . html_writer::select_time('months', 'f_' . $this->field->id . '_m', $value['timestamp'] ?? $currenttime)
            . html_writer::select_time('years', 'f_' . $this->field->id . '_y', $value['timestamp'] ?? $currenttime);
        $datecheck = html_writer::checkbox('f_' . $this->field->id . '_z', 1, $value['usedate'] ?? 0);
        $str = '<div class="d-flex flex-wrap">' . $selectors . ' ' . $datecheck . ' ' . get_string('usedate', 'data') . '</div>';

        return $str;
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $name = "df_date_$i";
        $varcharcontent = $DB->sql_compare_text("{$tablealias}.content");
        return array(" ({$tablealias}.fieldid = {$this->field->id} AND $varcharcontent = :$name) ", array($name => $value['timestamp']));
    }

    public function parse_search_field($defaults = null) {
        $paramday = 'f_'.$this->field->id.'_d';
        $parammonth = 'f_'.$this->field->id.'_m';
        $paramyear = 'f_'.$this->field->id.'_y';
        $paramusedate = 'f_'.$this->field->id.'_z';
        if (empty($defaults[$paramday])) {  // One empty means the other ones are empty too.
            $defaults = array($paramday => 0, $parammonth => 0, $paramyear => 0, $paramusedate => 0);
        }

        $day   = optional_param($paramday, $defaults[$paramday], PARAM_INT);
        $month = optional_param($parammonth, $defaults[$parammonth], PARAM_INT);
        $year  = optional_param($paramyear, $defaults[$paramyear], PARAM_INT);
        $usedate = optional_param($paramusedate, $defaults[$paramusedate], PARAM_INT);

        $data = array();
        if (!empty($day) && !empty($month) && !empty($year) && $usedate == 1) {
            $calendartype = \core_calendar\type_factory::get_calendar_instance();
            $gregoriandate = $calendartype->convert_to_gregorian($year, $month, $day);

            $data['timestamp'] = make_timestamp(
                $gregoriandate['year'],
                $gregoriandate['month'],
                $gregoriandate['day'],
                $gregoriandate['hour'],
                $gregoriandate['minute'],
                0,
                0,
                false);
            $data['usedate'] = 1;
            return $data;
        } else {
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

            $calendartype = \core_calendar\type_factory::get_calendar_instance();
            $gregoriandate = $calendartype->convert_to_gregorian($this->year, $this->month, $this->day);
            $content->content = make_timestamp(
                $gregoriandate['year'],
                $gregoriandate['month'],
                $gregoriandate['day'],
                $gregoriandate['hour'],
                $gregoriandate['minute'],
                0,
                0,
                false);

            if ($oldcontent = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
                $content->id = $oldcontent->id;
                return $DB->update_record('data_content', $content);
            } else {
                return $DB->insert_record('data_content', $content);
            }
        }
    }

    function display_browse_field($recordid, $template) {
        $content = $this->get_data_content($recordid);
        if (!$content || empty($content->content)) {
            return '';
        }
        return userdate($content->content, get_string('strftimedate'), 0);
    }

    function get_sort_sql($fieldname) {
        global $DB;
        return $DB->sql_cast_char2real($fieldname, true);
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
