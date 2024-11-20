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

namespace core_calendar;

/**
 * Defines functions used by calendar type plugins.
 *
 * This library provides a unified interface for calendar types.
 *
 * @package core_calendar
 * @copyright 2008 onwards Foodle Group {@link http://foodle.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class type_base {

    /**
     * Returns the name of the calendar.
     *
     * This is the non-translated name, usually just
     * the name of the calendar folder.
     *
     * @return string the calendar name
     */
    abstract public function get_name();

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
    abstract public function get_days();

    /**
     * Returns a list of all the names of the months.
     *
     * @return array the month names
     */
    abstract public function get_months();

    /**
     * Returns the minimum year for the calendar.
     *
     * @return int The minimum year
     */
    abstract public function get_min_year();

    /**
     * Returns the maximum year for the calendar
     *
     * @return int The maximum year
     */
    abstract public function get_max_year();

    /**
     * Returns an array of years.
     *
     * @param int $minyear
     * @param int $maxyear
     * @return array the years
     */
    abstract public function get_years($minyear = null, $maxyear = null);

    /**
     * Returns a multidimensional array with information for day, month, year
     * and the order they are displayed when selecting a date.
     * The order in the array will be the order displayed when selecting a date.
     * Override this function to change the date selector order.
     *
     * @param int $minyear The year to start with
     * @param int $maxyear The year to finish with
     * @return array Full date information
     */
    abstract public function get_date_order($minyear = null, $maxyear = null);

    /**
     * Returns the number of days in a week.
     *
     * @return int the number of days
     */
    abstract public function get_num_weekdays();

    /**
     * Returns an indexed list of all the names of the weekdays.
     *
     * The list starts with the index 0. Each index, representing a
     * day, must be an array that contains the indexes 'shortname'
     * and 'fullname'.
     *
     * @return array array of days
     */
    abstract public function get_weekdays();

    /**
     * Returns the index of the starting week day.
     *
     * This may vary, for example in the Gregorian calendar, some may consider Monday
     * as the start of the week, where as others may consider Sunday the start.
     *
     * @return int
     */
    abstract public function get_starting_weekday();

    /**
     * Returns the index of the weekday for a specific calendar date.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     */
    abstract public function get_weekday($year, $month, $day);

    /**
     * Returns the number of days in a given month.
     *
     *
     * @param int $year
     * @param int $month
     * @return int the number of days
     */
    abstract public function get_num_days_in_month($year, $month);

    /**
     * Get the previous month.
     *
     * @param int $year
     * @param int $month
     * @return array previous month and year
     */
    abstract public function get_prev_month($year, $month);

    /**
     * Get the next month.
     *
     * @param int $year
     * @param int $month
     * @return array the following month and year
     */
    abstract public function get_next_month($year, $month);

    /**
     * Returns a formatted string that represents a date in user time.
     *
     * @param int $time the timestamp in UTC, as obtained from the database
     * @param string $format strftime format
     * @param int|float|string $timezone the timezone to use
     *        {@link https://moodledev.io/docs/apis/subsystems/time#timezone}
     * @param bool $fixday if true then the leading zero from %d is removed,
     *        if false then the leading zero is maintained
     * @param bool $fixhour if true then the leading zero from %I is removed,
     *        if false then the leading zero is maintained
     * @return string the formatted date/time
     */
    abstract public function timestamp_to_date_string($time, $format, $timezone, $fixday, $fixhour);

    /**
     * Given a $time timestamp in GMT (seconds since epoch), returns an array that represents
     * the date in user time.
     *
     * @param int $time timestamp in GMT
     * @param float|int|string $timezone the timezone to use to calculate the time
     *        {@link https://moodledev.io/docs/apis/subsystems/time#timezone}
     * @return array an array that represents the date in user time
     */
    abstract public function timestamp_to_date_array($time, $timezone = 99);

    /**
     * Provided with a day, month, year, hour and minute in the specific
     * calendar type convert it into the equivalent Gregorian date.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted date
     */
    abstract public function convert_to_gregorian($year, $month, $day, $hour = 0, $minute = 0);

    /**
     * Provided with a day, month, year, hour and minute in a Gregorian date
     * convert it into the specific calendar type date.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted date
     */
    abstract public function convert_from_gregorian($year, $month, $day, $hour = 0, $minute = 0);

    /**
     * This return locale for windows os.
     *
     * @return string locale
     */
    abstract public function locale_win_charset();

    /**
     * Provided with a day, month, year, hour and minute in the specific
     * calendar type convert it into the equivalent Unix Time Stamp.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return int timestamp
     */
    public function convert_to_timestamp($year, $month, $day, $hour = 0, $minute = 0) {
        $gregorianinfo = $this->convert_to_gregorian($year, $month, $day, $hour, $minute);
        return make_timestamp(
            $gregorianinfo['year'],
            $gregorianinfo['month'],
            $gregorianinfo['day'],
            $gregorianinfo['hour'],
            $gregorianinfo['minute'],
            0);
    }

    /**
     * Get the previous day.
     *
     * @param int $daytimestamp The day timestamp.
     * @return int previous day timestamp
     */
    public function get_prev_day($daytimestamp) {
        $date = new \DateTime();
        $date->setTimestamp($daytimestamp);
        $date->modify('-1 day');

        return $date->getTimestamp();
    }

    /**
     * Get the next day.
     *
     * @param int $daytimestamp The day timestamp.
     * @return int the following day
     */
    public function get_next_day($daytimestamp) {
        $date = new \DateTime();
        $date->setTimestamp($daytimestamp);
        $date->modify('+1 day');

        return $date->getTimestamp();
    }
}
