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
 * Contains class containing the internal calendar API.
 *
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local;

defined('MOODLE_INTERNAL') || die();

use core_calendar\local\event\exceptions\limit_invalid_parameter_exception;

/**
 * Class containing the local calendar API.
 *
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Get a list of action events for the logged in user by the given
     * timesort values.
     *
     * @param int|null $timesortfrom The start timesort value (inclusive)
     * @param int|null $timesortto The end timesort value (inclusive)
     * @param int|null $aftereventid Only return events after this one
     * @param int $limitnum Limit results to this amount (between 1 and 50)
     * @return array A list of action_event_interface objects
     */
    public static function get_action_events_by_timesort(
        $timesortfrom = null,
        $timesortto = null,
        $aftereventid = null,
        $limitnum = 20
    ) {
        global $USER;

        if (is_null($timesortfrom) && is_null($timesortto)) {
            throw new \moodle_exception("Must provide a timesort to and/or from value");
        }

        if ($limitnum < 1 || $limitnum > 50) {
            throw new \moodle_exception("Limit must be between 1 and 50 (inclusive)");
        }

        $vault = \core_calendar\local\event\core_container::get_event_vault();

        $afterevent = null;
        if ($aftereventid && $event = $vault->get_event_by_id($aftereventid)) {
            $afterevent = $event;
        }

        return $vault->get_action_events_by_timesort($USER, $timesortfrom, $timesortto, $afterevent, $limitnum);
    }

    /**
     * Get a list of action events for the logged in user by the given
     * course and timesort values.
     *
     * @param \stdClass $course The course the events must belong to
     * @param int|null $timesortfrom The start timesort value (inclusive)
     * @param int|null $timesortto The end timesort value (inclusive)
     * @param int|null $aftereventid Only return events after this one
     * @param int $limitnum Limit results to this amount (between 1 and 50)
     * @return array A list of action_event_interface objects
     */
    public static function get_action_events_by_course(
        $course,
        $timesortfrom = null,
        $timesortto = null,
        $aftereventid = null,
        $limitnum = 20
    ) {
        global $USER;

        if ($limitnum < 1 || $limitnum > 50) {
            throw new limit_invalid_parameter_exception(
                "Limit must be between 1 and 50 (inclusive)");
        }

        $vault = \core_calendar\local\event\core_container::get_event_vault();

        $afterevent = null;
        if ($aftereventid && $event = $vault->get_event_by_id($aftereventid)) {
            $afterevent = $event;
        }

        return $vault->get_action_events_by_course(
            $USER, $course, $timesortfrom, $timesortto, $afterevent, $limitnum);
    }
}
