<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package dataformfield
 * @subpackage time
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_time_time extends mod_dataform\pluginbase\dataformfield {
    public $dateonly;
    public $masked;
    public $startyear;
    public $stopyear;
    public $displayformat;

    public function __construct($field) {
        parent::__construct($field);
        $this->date_only = $this->param1;
        $this->masked = $this->param5;
        $this->start_year = $this->param2;
        $this->stop_year = $this->param3;
        $this->display_format = $this->param4;
    }

    /**
     *
     */
    public function content_names() {
        return array('', 'year', 'month', 'day', 'hour', 'minute', 'enabled');
    }

    /**
     *
     */
    protected function format_content($entry, array $values = null) {
        $fieldid = $this->id;
        $oldcontents = array();
        $contents = array();
        // Old contents.
        if (isset($entry->{"c{$fieldid}_content"})) {
            $oldcontents[] = $entry->{"c{$fieldid}_content"};
        }

        // New contents.
        $value = (count($values) === 1 ? reset($values) : $values);
        if ($timestamp = $this->get_timestamp_from_value($value)) {
            $contents[] = $timestamp;
        }

        return array($contents, $oldcontents);
    }

    /**
     *
     */
    protected function get_timestamp_from_value($value) {
        if (empty($value)) {
            return null;
        }

        $timestamp = null;

        // Timestamp or time string.
        if (!is_array($value)) {
            if (((string) (int) $value === (string) $value)
                    && ($value <= PHP_INT_MAX)
                    && ($value >= ~PHP_INT_MAX)) {
                // It's a timestamp.
                $timestamp = $value;

            } else if ($value = strtotime($value)) {
                // It's a valid time string.
                $timestamp = $value;
            }

        } else {
            // Assuming any of year, month, day, hour, minute is passed.
            $enabled = 0;
            $year = 0;
            $month = 1;
            $day = 1;
            $hour = 0;
            $minute = 0;
            foreach ($value as $name => $val) {
                if (!empty($val)) {
                    ${$name} = $val;
                }
            }
            if ($enabled and $year) {
                $calendartype = \core_calendar\type_factory::get_calendar_instance();
                $gregoriandate = $calendartype->convert_to_gregorian(
                    $year,
                    $month,
                    $day,
                    $hour,
                    $minute
                );
                $timestamp = make_timestamp(
                    $gregoriandate['year'],
                    $gregoriandate['month'],
                    $gregoriandate['day'],
                    $gregoriandate['hour'],
                    $gregoriandate['minute']
                );
            }
        }

        return $timestamp;
    }

    /**
     *
     */
    public function get_search_sql($search) {
        list($element, $not, $operator, $value) = $search;

        // Time list separated by ..
        if (strpos($value, '..') !== false) {
            $value = array_map('strtotime', explode('..', $value));
            // Must have valid timestamps.
            if (in_array(false, $value, true)) {
                $value = '#';
            }
        } else {
            $value = strtotime($value);
            // Must have valid timestamps.
            if ($value === false) {
                $value = '#';
            }
        }

        return parent::get_search_sql(array($element, $not, $operator, $value));
    }

    /**
     * Overriding parent method to process time strings.
     * Process the first pattern of the field and expects timestamp or valid time string.
     * (@link dataformfield::prepare_import_content()}
     *
     * @return bool
     */
    public function prepare_import_content($data, $importsettings, $csvrecord = null, $entryid = null) {
        // Import only from csv.
        if (!$csvrecord) {
            return $data;
        }

        $fieldid = $this->id;
        $csvname = '';

        $setting = reset($importsettings);
        if (!empty($setting['name'])) {
            $csvname = $setting['name'];
        }

        if ($csvname and isset($csvrecord[$csvname]) and $csvrecord[$csvname] !== '') {
            $timestr = !empty($csvrecord[$csvname]) ? $csvrecord[$csvname] : null;
            if ($timestr) {
                $data->{"field_{$fieldid}_{$entryid}"} = $this->get_timestamp_from_value($timestr);
            }
        }

        return $data;
    }

    /**
     *
     */
    public function get_sql_compare_text($column = 'content') {
        global $DB;
        return $DB->sql_cast_char2int("c{$this->id}.$column", true);
    }

}
