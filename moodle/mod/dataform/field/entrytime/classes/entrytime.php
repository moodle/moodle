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
 * @subpackage entrytime
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_entrytime_entrytime extends \mod_dataform\pluginbase\dataformfield_internal {

    const INTERNALID = -4;

    /**
     * Returns instance defaults for for the field
     * (because internal fields do not have DB record).
     *
     * @return null|stdClass
     */
    public static function get_default_data($dfid) {
        $field = (object) array(
            'id' => self::INTERNALID,
            'dataid' => $dfid,
            'type' => 'entrytime',
            'name' => get_string('fieldname', 'dataformfield_entrytime'),
            'description' => '',
            'visible' => 2,
            'editable' => -1,
        );
        return $field;
    }

    /**
     * Overrides {@link dataformfield::prepare_import_content()}
     * to set import into entry::timecreated and entry::timemodified.
     *
     * @return stdClass
     */
    public function prepare_import_content($data, $importsettings, $csvrecord = null, $entryid = 0) {

        // Time created and modified.
        $timecreated = $timemodified = 0;
        foreach (array('timecreated', 'timemodified') as $timevar) {
            $csvname = '';
            if (!empty($importsettings[$timevar])) {
                $setting = $importsettings[$timevar];
                if (!empty($setting['name'])) {
                    $csvname = $setting['name'];

                    if (isset($csvrecord[$csvname]) and $timestamp = $this->str_to_time($csvrecord[$csvname])) {
                        ${$timevar} = $timestamp;
                    }
                }
            }
        }

        if ($timecreated) {
            $data->{"entry_{$entryid}_timecreated"} = $timecreated;
            if ($timemodified and $timemodified > $timecreated) {
                $data->{"entry_{$entryid}_timemodified"} = $timemodified;
            }
        }

        return $data;
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
                return null;
            }
        } else {
            $value = strtotime($value);
            // Must have valid timestamps.
            if ($value === false) {
                return null;
            }
        }

        return parent::get_search_sql(array($element, $not, $operator, $value));
    }

    /**
     * Returns the field alias for sql queries.
     *
     * @param string The field element to query
     * @return string
     */
    protected function get_sql_alias($element = null) {
        return 'e';
    }

    /**
     * Return array of sort options menu as
     * $fieldid,element => name, for the filter form.
     *
     *
     * @return null|array
     */
    public function get_sort_options_menu() {
        $fieldid = $this->id;
        $fieldname = $this->name;
        return array(
            "$fieldid,timecreated" => "$fieldname ". get_string('timecreated', 'dataformfield_entrytime'),
            "$fieldid,timemodified" => "$fieldname ". get_string('timemodified', 'dataformfield_entrytime'),
        );
    }

    /**
     * Returns an array of distinct content of the field.
     *
     * @param string $element
     * @param int $sortdir Sort direction 0|1 ASC|DESC
     * @return array
     */
    public function get_distinct_content($element, $sortdir = 0) {
        global $DB;

        $sortdir = $sortdir ? 'DESC' : 'ASC';
        $contentfull = $this->get_sort_sql();

        $sql = "SELECT DISTINCT $contentfull
                    FROM {dataform_entries} e
                    WHERE $contentfull IS NOT NULL'.
                    ORDER BY $contentfull $sortdir";

        $distinctvalues = array();
        if ($options = $DB->get_records_sql($sql)) {
            foreach ($options as $data) {
                $value = $data->{$this->internalname};
                if ($value === '') {
                    continue;
                }
                $distinctvalues[] = $value;
            }
        }
        return $distinctvalues;
    }

    /**
     *
     */
    public function format_search_value($searchparams) {
        list($not, $operator, $value) = $searchparams;
        if (is_array($value)) {
            $from = userdate($value[0]);
            $to = userdate($value[1]);
        } else {
            $from = userdate(time());
            $to = userdate(time());
        }
        if ($operator != 'BETWEEN') {
            return $not. ' '. $operator. ' '. $from;
        } else {
            return $not. ' '. $operator. ' '. $from. ' and '. $to;
        }
    }

    /**
     * Converts a valid time string to a unix timestamp.
     *
     * @return null|int Unix time stamp or null
     */
    protected function str_to_time($timestr) {
        if ($timestr) {
            if (((string) (int) $timestr === $timestr)
                    && ($timestr <= PHP_INT_MAX)
                    && ($timestr >= ~PHP_INT_MAX)) {
                // It's a timestamp.
                return $timestr;

            } else if ($timestr = strtotime($timestr)) {
                // It's a valid time string.
                return $timestr;
            }
        }
        return null;
    }

}
