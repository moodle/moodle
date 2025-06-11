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

namespace factor_sms;

/**
 * Helper class for shared sms gateway functions
 *
 * @package     factor_sms
 * @author      Alex Morris <alex.morris@catalyst.net.nz>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * This function internationalises a number to E.164 standard.
     * https://46elks.com/kb/e164
     *
     * @param string $phonenumber the phone number to format.
     * @return string the formatted phone number.
     */
    public static function format_number(string $phonenumber): string {
        // Remove all whitespace, dashes and brackets.
        $phonenumber = preg_replace('/[ \(\)-]/', '', $phonenumber);

        // Number is already in international format. Do nothing.
        if (str_starts_with ($phonenumber, '+')) {
            return $phonenumber;
        }

        // Strip leading 0 if found.
        if (str_starts_with ($phonenumber, '0')) {
            $phonenumber = substr($phonenumber, 1);
        }

        // Prepend country code.
        $countrycode = get_config('factor_sms', 'countrycode');
        $phonenumber = !empty($countrycode) ? '+' . $countrycode . $phonenumber : $phonenumber;

        return $phonenumber;
    }

    /**
     * Validate phone number with E.164 format. https://en.wikipedia.org/wiki/E.164
     *
     * @param string $phonenumber from the given user input
     * @return bool
     */
    public static function is_valid_phonenumber(string $phonenumber): bool {
        $phonenumber = self::format_number($phonenumber);
        return (preg_match("/^\+[1-9]\d{1,14}$/", $phonenumber)) ? true : false;
    }
}
