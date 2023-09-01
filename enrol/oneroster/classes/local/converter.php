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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local;

use DateTime;
use DateTimeZone;

/**
 * One Roster Type conversion utilities.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class converter {

    /**
     * Convert from ISO 8601 Date to unix time stamp.
     *
     * @param   string $date ISO 8601 Date format
     * @return  int
     */
    public static function from_date_to_unix(string $date): int {
        /*
         * The One Roster specification describes dates as follows:
         * `date`:  Denotes a date format.
         *          Dates MUST be expressed using ISO 8601 format (http://tools.ietf.org/html/rfc3339), more commonly
         *          formatted as "YYYY-MM-DD" e.g. "2002-04-23"
         *
         * Source https://www.imsglobal.org/oneroster-v11-final-specification#_Toc480452032.
         */
        $datetime = DateTime::createFromFormat(
            'Y-m-d',
            $date,
            new DateTimeZone('UTC')
        );
        $datetime->setTime(0, 0, 0, 0);

        return $datetime->getTimestamp();
    }

    /**
     * Convert from ISO 8601 DateTime to unix time stamp.
     *
     * @param   string $date ISO 8601 DateTime format
     * @return  int
     */
    public static function from_datetime_to_unix(string $date): int {
        /*
         * The One Roster specification describes dates as follows:
         * `date`:  Denotes a timestamp format.
         *          DateTimes MUST be expressed in W3C profile of ISO 8601 and MUST contain the UTC timezone e.g.
         *          "2012-04-23T18:25:43.511Z"
         *
         * Source https://www.imsglobal.org/oneroster-v11-final-specification#_Toc480452032.
         */
        $datetime = DateTime::createFromFormat(
            'Y-m-d\TG:i:s.+',
            $date,
            new DateTimeZone('UTC')
        );
        if ($datetime) {
            return $datetime->getTimestamp();
        }

        return 0;
    }
}
