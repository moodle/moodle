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
 * Contains helper class for digital consent.
 *
 * @package     core_auth
 * @copyright   2018 Mihail Geshoski <mihail@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_auth;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class for digital consent.
 *
 * @copyright 2018 Mihail Geshoski <mihail@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class digital_consent {

    /**
     * Returns true if age and location verification is enabled in the site.
     *
     * @return bool
     */
    public static function is_age_digital_consent_verification_enabled() {
        global $CFG;

        return !empty($CFG->agedigitalconsentverification);
    }

    /**
     * Checks if a user is a digital minor.
     *
     * @param int $age
     * @param string $country The country code (ISO 3166-2)
     * @return bool
     */
    public static function is_minor($age, $country) {
        global $CFG;

        $ageconsentmap = $CFG->agedigitalconsentmap;
        $agedigitalconsentmap = self::parse_age_digital_consent_map($ageconsentmap);

        return array_key_exists($country, $agedigitalconsentmap) ?
            $age < $agedigitalconsentmap[$country] : $age < $agedigitalconsentmap['*'];
    }

    /**
     * Parse the agedigitalconsentmap setting into an array.
     *
     * @param  string $ageconsentmap The value of the agedigitalconsentmap setting
     * @return array $ageconsentmapparsed
     */
    public static function parse_age_digital_consent_map($ageconsentmap) {

        $ageconsentmapparsed = array();
        $countries = get_string_manager()->get_list_of_countries(true);
        $isdefaultvaluepresent = false;
        $lines = preg_split('/\r|\n/', $ageconsentmap, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($lines as $line) {
            $arr = explode(",", $line);
            // Handle if there is more or less than one comma separator.
            if (count($arr) != 2) {
                throw new \moodle_exception('agedigitalconsentmapinvalidcomma', 'error', '', $line);
            }
            $country = trim($arr[0]);
            $age = trim($arr[1]);
            // Check if default.
            if ($country == "*") {
                $isdefaultvaluepresent = true;
            }
            // Handle if the presented value for country is not valid.
            if ($country !== "*" && !array_key_exists($country, $countries)) {
                throw new \moodle_exception('agedigitalconsentmapinvalidcountry', 'error', '', $country);
            }
            // Handle if the presented value for age is not valid.
            if (!is_numeric($age)) {
                throw new \moodle_exception('agedigitalconsentmapinvalidage', 'error', '', $age);
            }
            $ageconsentmapparsed[$country] = $age;
        }
        // Handle if a default value does not exist.
        if (!$isdefaultvaluepresent) {
            throw new \moodle_exception('agedigitalconsentmapinvaliddefault');
        }

        return $ageconsentmapparsed;
    }
}
