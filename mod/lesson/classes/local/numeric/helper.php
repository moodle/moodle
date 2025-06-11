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
 * Lesson's numeric helper lib.
 *
 * Contains any helper functions for the numeric pagetyep
 *
 * @package    mod_lesson
 * @copyright  2020 Peter Dias <peter@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lesson\local\numeric;

/**
 * Lesson numeric page helper
 *
 * @copyright  2020 Peter Dias<peter@moodle.com>
 * @package core_lesson
 */
class helper {

    /**
     * Helper function to unformat a given numeric value from locale specific values with n:n signifying ranges to standards
     * with decimal point numbers/ranges
     *
     * @param string $value The value to be formatted
     * @return string|float|bool $formattedvalue unformatted value
     *              String - If it is a range it will return a value e.g. 2:4
     *              Float - if it's a properly formatted float
     *              Null - If empty and could not be converted
     */
    public static function lesson_unformat_numeric_value(string $value) {
        if (strpos($value, ':')) {
            list($min, $max) = explode(':', $value);
            $formattedvalue = unformat_float($min) . ':' . unformat_float($max);
        } else {
            $formattedvalue = unformat_float($value);
        }

        return $formattedvalue;
    }

    /**
     * Helper function to format a given value into locale specific values with n:n signifying ranges
     *
     * @param string|number $value The value to be formatted
     * @return string $formattedvalue Formatted value OR $value if not numeric
     */
    public static function lesson_format_numeric_value($value): string {
        $formattedvalue = $value;
        if (strpos($value, ':')) {
            list($min, $max) = explode(':', $value);
            $formattedvalue = $min . ':' . $max;
            if (is_numeric($min) && is_numeric($max)) {
                $formattedvalue = format_float($min, strlen($min), true, true) . ':'
                    . format_float($max, strlen($max), true, true);
            }
        } else {
            $formattedvalue = is_numeric($value) ? format_float($value, strlen($value), true, true) : $value;
        }

        return $formattedvalue;
    }

}
