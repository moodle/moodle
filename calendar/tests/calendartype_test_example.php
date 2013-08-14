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

namespace calendartype_test;
use \core_calendar\type_base;

/**
 * Handles calendar functions for the test calendar.
 *
 * The test calendar is going to be 2 years, 2 days, 2 hours and 2 minutes
 * in the future of the Gregorian calendar.
 *
 * @package core_calendar
 * @copyright 2013 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class structure extends type_base {

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
            $months[$i] = $i;
        }

        return $months;
    }

    /**
     * Returns the minimum year of the calendar.
     *
     * @return int the minumum year
     */
    public function get_min_year() {
        return 1970;
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
     * Returns a formatted string that represents a date in user time.
     *
     * @param int $date the timestamp in UTC, as obtained from the database
     * @param string $format strftime format
     * @param int|float|string $timezone the timezone to use
     *        {@link http://docs.moodle.org/dev/Time_API#Timezone}
     * @param bool $fixday if true then the leading zero from %d is removed,
     *        if false then the leading zero is maintained
     * @param bool $fixhour if true then the leading zero from %I is removed,
     *        if false then the leading zero is maintained
     * @return string the formatted date/time
     */
    public function timestamp_to_date_string($date, $format, $timezone, $fixday, $fixhour) {
        return '';
    }

    /**
     * Given a $time timestamp in GMT (seconds since epoch), returns an array that represents
     * the date in user time.
     *
     * @param int $time timestamp in GMT
     * @param float|int|string $timezone the timezone to use to calculate the time
     *        {@link http://docs.moodle.org/dev/Time_API#Timezone}
     * @return array an array that represents the date in user time
     */
    public function timestamp_to_date_array($time, $timezone) {
        $gregoriancalendar = \core_calendar\type_factory::factory('gregorian');
        $date = $gregoriancalendar->timestamp_to_date_array($time, $timezone);
        $newdate = $this->convert_from_gregorian($date['year'], $date['mon'], $date['mday'],
            $date['hours'], $date['minutes']);

        $date['year'] = $newdate['year'];
        $date['mon'] = $newdate['month'];
        $date['mday'] = $newdate['day'];
        $date['hours'] = $newdate['hour'];
        $date['minutes']  = $newdate['minute'];

        return $date;
    }

    /**
     * Provided with a day, month, year, hour and minute
     * convert it into the equivalent Gregorian date.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted day, month, year, hour and minute.
     */
    public function convert_to_gregorian($year, $month, $day, $hour = 0, $minute = 0) {
        $timestamp = make_timestamp($year, $month, $day, $hour, $minute);
        $date = date('Y/n/j/H/i', strtotime('-2 year, -2 months, -2 days, -2 hours, -2 minutes', $timestamp));

        list($year, $month, $day, $hour, $minute) = explode('/', $date);

        return array('year' => (int) $year,
                     'month' => (int) $month,
                     'day' => (int) $day,
                     'hour' => (int) $hour,
                     'minute' => (int) $minute);

    }

    /**
     * Provided with a day, month, year, hour and minute in a Gregorian date
     * convert it into the specific calendar type date.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted day, month, year, hour and minute.
     */
    public function convert_from_gregorian($year, $month, $day, $hour = 0, $minute = 0) {
        $timestamp = make_timestamp($year, $month, $day, $hour, $minute);
        $date = date('Y/n/j/H/i', strtotime('+2 year, +2 months, +2 days, +2 hours, +2 minutes', $timestamp));

        list($year, $month, $day, $hour, $minute) = explode('/', $date);

        return array('year' => (int) $year,
                     'month' => (int) $month,
                     'day' => (int) $day,
                     'hour' => (int) $hour,
                     'minute' => (int) $minute);
    }
}
