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
 * Handles calendar functions for the gregorian calendar.
 *
 * @package calendar_type_plugin_gregorian
 * @author Shamim Rezaie <support@foodle.org>
 * @author Mark Nelson <markn@moodle.com>
 * @copyright 2008 onwards Foodle Group {@link http://foodle.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_type_plugin_gregorian extends calendar_type_plugin_base {

    /**
     * Returns a list of all the possible days for all months.
     *
     * This is used to generate the select box for the days
     * in the date selector elements. Some months contain more days
     * than others so this function should return all possible days as
     * we can not predict what month will be chosen (the user
     * may have JS turned off and we need to support this situation in
     * Moodle).
     *
     * @return array the days
     */
    public function get_days() {
        $days = array();

        for ($i = 1; $i <= 31; $i++) {
            $days[$i] = $i;
        }

        return $days;
    }

    /**
     * Returns a list of all the names of the months.
     *
     * @return array the month names
     */
    public function get_months() {
        $months = array();

        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = userdate(gmmktime(12, 0, 0, $i, 15, 2000), '%B');
        }

        return $months;
    }

    /**
     * Returns the minimum year of the calendar.
     *
     * @return int the minumum year
     */
    public function get_min_year() {
        return 1900;
    }

    /**
     * Returns the maximum year of the calendar.
     *
     * @return int the max year
     */
    public function get_max_year() {
        return 2050;
    }

    /**
     * Provided with a day, month, year, hour and minute in a specific
     * calendar type convert it into the equivalent Gregorian date.
     *
     * In this function we don't need to do anything except pass the data
     * back as an array. This is because the date received is Gregorian.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted day, month, year, hour and minute.
     */
    public function convert_from_gregorian($year, $month, $day, $hour = 0, $minute = 0) {
        $date = array();
        $date['year'] = $year;
        $date['month'] = $month;
        $date['day'] = $day;
        $date['hour'] = $hour;
        $date['minute'] = $minute;

        return $date;
    }

    /**
     * Provided with a day, month, year, hour and minute in a specific
     * calendar type convert it into the equivalent Gregorian date.
     *
     * In this function we don't need to do anything except pass the data
     * back as an array. This is because the date received is Gregorian.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted day, month, year, hour and minute.
     */
    public function convert_to_gregorian($year, $month, $day, $hour = 0, $minute = 0) {
        $date = array();
        $date['year'] = $year;
        $date['month'] = $month;
        $date['day'] = $day;
        $date['hour'] = $hour;
        $date['minute'] = $minute;

        return $date;
    }
}
