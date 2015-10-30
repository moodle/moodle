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

namespace calendartype_test_example;
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
     * Returns the name of the calendar.
     *
     * @return string the calendar name
     */
    public function get_name() {
        return 'test_example';
    }

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
     * Returns the minimum year for the calendar.
     *
     * @return int The minimum year
     */
    public function get_min_year() {
        return 1900;
    }

    /**
     * Returns the maximum year for the calendar
     *
     * @return int The maximum year
     */
    public function get_max_year() {
        return 2050;
    }

    /**
     * Returns an array of years.
     *
     * @param int $minyear
     * @param int $maxyear
     * @return array the years.
     */
    public function get_years($minyear = null, $maxyear = null) {
        if (is_null($minyear)) {
            $minyear = $this->get_min_year();
        }

        if (is_null($maxyear)) {
            $maxyear = $this->get_max_year();
        }

        $years = array();
        for ($i = $minyear; $i <= $maxyear; $i++) {
            $years[$i] = $i;
        }

        return $years;
    }

    /**
     * Returns a multidimensional array with information for day, month, year
     * and the order they are displayed when selecting a date.
     * The order in the array will be the order displayed when selecting a date.
     * Override this function to change the date selector order.
     *
     * @param int $minyear The year to start with.
     * @param int $maxyear The year to finish with.
     * @return array Full date information.
     */
    public function get_date_order($minyear = null, $maxyear = null) {
        $dateinfo = array();
        $dateinfo['day'] = $this->get_days();
        $dateinfo['month'] = $this->get_months();
        $dateinfo['year'] = $this->get_years($minyear, $maxyear);

        return $dateinfo;
    }

    /**
     * Returns the number of days in a week.
     *
     * @return int the number of days
     */
    public function get_num_weekdays() {
        return 7;
    }

    /**
     * Returns an indexed list of all the names of the weekdays.
     *
     * The list starts with the index 0. Each index, representing a
     * day, must be an array that contains the indexes 'shortname'
     * and 'fullname'.
     *
     * @return array array of days
     */
    public function get_weekdays() {
        return '';
    }

    /**
     * Returns the index of the starting week day.
     *
     * @return int
     */
    public function get_starting_weekday() {
        return '';
    }

    /**
     * Returns the index of the weekday for a specific calendar date.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     */
    public function get_weekday($year, $month, $day) {
        return '';
    }

    /**
     * Returns the number of days in a given month.
     *
     * @param int $year
     * @param int $month
     * @return int the number of days
     */
    public function get_num_days_in_month($year, $month) {
        return '';
    }

    /**
     * Get the previous month.
     *
     * If the current month is January, it will get the last month of the previous year.
     *
     * @param int $year
     * @param int $month
     * @return array previous month and year
     */
    public function get_prev_month($year, $month) {
        return '';
    }

    /**
     * Get the next month.
     *
     * If the current month is December, it will get the first month of the following year.
     *
     * @param int $year
     * @param int $month
     * @return array the following month and year
     */
    public function get_next_month($year, $month) {
        return '';
    }

    /**
     * Returns a formatted string that represents a date in user time.
     *
     * @param int $time the timestamp in UTC, as obtained from the database
     * @param string $format strftime format
     * @param int|float|string $timezone the timezone to use
     *        {@link http://docs.moodle.org/dev/Time_API#Timezone}
     * @param bool $fixday if true then the leading zero from %d is removed,
     *        if false then the leading zero is maintained
     * @param bool $fixhour if true then the leading zero from %I is removed,
     *        if false then the leading zero is maintained
     * @return string the formatted date/time
     */
    public function timestamp_to_date_string($time, $format, $timezone, $fixday, $fixhour) {
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
    public function timestamp_to_date_array($time, $timezone = 99) {
        $gregoriancalendar = \core_calendar\type_factory::get_calendar_instance('gregorian');
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

    /**
     * This return locale for windows os.
     *
     * @return string locale
     */
    public function locale_win_charset() {
        return get_string('localewincharset', 'langconfig');
    }
}
