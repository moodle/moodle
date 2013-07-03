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
 * @author Shamim Rezaie <support@foodle.org>
 * @author Mark Nelson <markn@moodle.com>
 * @copyright 2008 onwards Foodle Group {@link http://foodle.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class type_base {

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
    public abstract function get_days();

    /**
     * Returns a list of all the names of the months.
     *
     * @return array the month names
     */
    public abstract function get_months();

    /**
     * Returns the minimum year of the calendar.
     *
     * @return int the minumum year
     */
    public abstract function get_min_year();

    /**
     * Returns the maximum year of the calendar.
     *
     * @return int the max year
     */
    public abstract function get_max_year();

    /**
     * Provided with a day, month, year, hour and minute in the specific
     * calendar type convert it into the equivalent Gregorian date.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted day, month and year.
     */
    public abstract function convert_to_gregorian($year, $month, $day, $hour = 0, $minute = 0);

    /**
     * Provided with a day, month, year, hour and minute in a Gregorian date
     * convert it into the specific calendar type date.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted day, month and year.
     */
    public abstract function convert_from_gregorian($year, $month, $day, $hour = 0, $minute = 0);

    /**
     * Returns a formatted string that represents a date in user time.
     *
     * Returns a formatted string that represents a date in user time
     * <b>WARNING: note that the format is for strftime(), not date().</b>
     * Because of a bug in most Windows time libraries, we can't use
     * the nicer %e, so we have to use %d which has leading zeroes.
     * A lot of the fuss in the function is just getting rid of these leading
     * zeroes as efficiently as possible.
     *
     * If parameter fixday = true (default), then take off leading
     * zero from %d, else maintain it.
     *
     * @param int $date the timestamp in UTC, as obtained from the database.
     * @param string $format strftime format. You should probably get this using
     *        get_string('strftime...', 'langconfig');
     * @param int|float|string  $timezone by default, uses the user's time zone. if numeric and
     *        not 99 then daylight saving will not be added.
     *        {@link http://docs.moodle.org/dev/Time_API#Timezone}
     * @param bool $fixday if true (default) then the leading zero from %d is removed.
     *        If false then the leading zero is maintained.
     * @param bool $fixhour if true (default) then the leading zero from %I is removed.
     * @return string the formatted date/time.
     */
    function userdate($date, $format, $timezone, $fixday, $fixhour) {
        global $CFG;

        if (empty($format)) {
            $format = get_string('strftimedaydatetime', 'langconfig');
        }

        if (!empty($CFG->nofixday)) { // Config.php can force %d not to be fixed.
            $fixday = false;
        } else if ($fixday) {
            $formatnoday = str_replace('%d', 'DD', $format);
            $fixday = ($formatnoday != $format);
            $format = $formatnoday;
        }

        // Note: This logic about fixing 12-hour time to remove unnecessary leading
        // zero is required because on Windows, PHP strftime function does not
        // support the correct 'hour without leading zero' parameter (%l).
        if (!empty($CFG->nofixhour)) {
            // Config.php can force %I not to be fixed.
            $fixhour = false;
        } else if ($fixhour) {
            $formatnohour = str_replace('%I', 'HH', $format);
            $fixhour = ($formatnohour != $format);
            $format = $formatnohour;
        }

        // Add daylight saving offset for string timezones only, as we can't get dst for
        // float values. if timezone is 99 (user default timezone), then try update dst.
        if ((99 == $timezone) || !is_numeric($timezone)) {
            $date += dst_offset_on($date, $timezone);
        }

        $timezone = get_user_timezone_offset($timezone);

        // If we are running under Windows convert to windows encoding and then back to UTF-8
        // (because it's impossible to specify UTF-8 to fetch locale info in Win32).
        if (abs($timezone) > 13) { // Server time.
            $datestring = date_format_string($date, $format, $timezone);
            if ($fixday) {
                $daystring  = ltrim(str_replace(array(' 0', ' '), '', strftime(' %d', $date)));
                $datestring = str_replace('DD', $daystring, $datestring);
            }
            if ($fixhour) {
                $hourstring = ltrim(str_replace(array(' 0', ' '), '', strftime(' %I', $date)));
                $datestring = str_replace('HH', $hourstring, $datestring);
            }
        } else {
            $date += (int)($timezone * 3600);
            $datestring = date_format_string($date, $format, $timezone);
            if ($fixday) {
                $daystring  = ltrim(str_replace(array(' 0', ' '), '', gmstrftime(' %d', $date)));
                $datestring = str_replace('DD', $daystring, $datestring);
            }
            if ($fixhour) {
                $hourstring = ltrim(str_replace(array(' 0', ' '), '', gmstrftime(' %I', $date)));
                $datestring = str_replace('HH', $hourstring, $datestring);
            }
        }

        return $datestring;
    }

    /**
     * Given a $time timestamp in GMT (seconds since epoch), returns an array that
     * represents the date in user time.
     *
     * @param int $time Timestamp in GMT
     * @param float|int|string $timezone offset's time with timezone, if float and not 99, then no
     *        dst offset is applyed {@link http://docs.moodle.org/dev/Time_API#Timezone}
     * @return array An array that represents the date in user time
     */
    function usergetdate($time, $timezone) {
        // Save input timezone, required for dst offset check.
        $passedtimezone = $timezone;

        $timezone = get_user_timezone_offset($timezone);

        if (abs($timezone) > 13) { // Server time.
            return getdate($time);
        }

        // Add daylight saving offset for string timezones only, as we can't get dst for
        // float values. if timezone is 99 (user default timezone), then try update dst.
        if ($passedtimezone == 99 || !is_numeric($passedtimezone)) {
            $time += dst_offset_on($time, $passedtimezone);
        }

        $time += intval((float)$timezone * HOURSECS);

        $datestring = gmstrftime('%B_%A_%j_%Y_%m_%w_%d_%H_%M_%S', $time);

        // Be careful to ensure the returned array matches that produced by getdate() above.
        list (
            $getdate['month'],
            $getdate['weekday'],
            $getdate['yday'],
            $getdate['year'],
            $getdate['mon'],
            $getdate['wday'],
            $getdate['mday'],
            $getdate['hours'],
            $getdate['minutes'],
            $getdate['seconds']
            ) = explode('_', $datestring);

        // Set correct datatype to match with getdate().
        $getdate['seconds'] = (int) $getdate['seconds'];
        $getdate['yday'] = (int) $getdate['yday'] - 1;
        $getdate['year'] = (int) $getdate['year'];
        $getdate['mon'] = (int) $getdate['mon'];
        $getdate['wday'] = (int) $getdate['wday'];
        $getdate['mday'] = (int) $getdate['mday'];
        $getdate['hours'] = (int) $getdate['hours'];
        $getdate['minutes']  = (int) $getdate['minutes'];

        return $getdate;
    }
}
