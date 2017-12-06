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
 * This should not be used outside of core_calendar.
 *
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {
    /**
     * Get all events restricted by various parameters, taking in to account user and group overrides.
     *
     * @param int|null      $timestartfrom         Events with timestart from this value (inclusive).
     * @param int|null      $timestartto           Events with timestart until this value (inclusive).
     * @param int|null      $timesortfrom          Events with timesort from this value (inclusive).
     * @param int|null      $timesortto            Events with timesort until this value (inclusive).
     * @param int|null      $timestartaftereventid Restrict the events in the timestart range to ones after this ID.
     * @param int|null      $timesortaftereventid  Restrict the events in the timesort range to ones after this ID.
     * @param int           $limitnum              Return at most this number of events.
     * @param int|null      $type                  Return only events of this type.
     * @param array|null    $usersfilter           Return only events for these users.
     * @param array|null    $groupsfilter          Return only events for these groups.
     * @param array|null    $coursesfilter         Return only events for these courses.
     * @param bool          $withduration          If true return only events starting within specified
     *                                             timestart otherwise return in progress events as well.
     * @param bool          $ignorehidden          If true don't return hidden events.
     * @return \core_calendar\local\event\entities\event_interface[] Array of event_interfaces.
     */
    public static function get_events(
        $timestartfrom = null,
        $timestartto = null,
        $timesortfrom = null,
        $timesortto = null,
        $timestartaftereventid = null,
        $timesortaftereventid = null,
        $limitnum = 20,
        $type = null,
        array $usersfilter = null,
        array $groupsfilter = null,
        array $coursesfilter = null,
        $withduration = true,
        $ignorehidden = true
    ) {
        global $USER;

        $vault = \core_calendar\local\event\container::get_event_vault();

        $timestartafterevent = null;
        $timesortafterevent = null;

        if ($timestartaftereventid && $event = $vault->get_event_by_id($timestartaftereventid)) {
            $timestartafterevent = $event;
        }

        if ($timesortaftereventid && $event = $vault->get_event_by_id($timesortaftereventid)) {
            $timesortafterevent = $event;
        }

        return $vault->get_events(
            $timestartfrom,
            $timestartto,
            $timesortfrom,
            $timesortto,
            $timestartafterevent,
            $timesortafterevent,
            $limitnum,
            $type,
            $usersfilter,
            $groupsfilter,
            $coursesfilter,
            $withduration,
            $ignorehidden
        );
    }

    /**
     * Get a list of action events for the logged in user by the given
     * timesort values.
     *
     * @param int|null $timesortfrom The start timesort value (inclusive)
     * @param int|null $timesortto The end timesort value (inclusive)
     * @param int|null $aftereventid Only return events after this one
     * @param int $limitnum Limit results to this amount (between 1 and 50)
     * @return array A list of action_event_interface objects
     * @throws \moodle_exception
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

        $vault = \core_calendar\local\event\container::get_event_vault();

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
     * @throws limit_invalid_parameter_exception
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

        $vault = \core_calendar\local\event\container::get_event_vault();

        $afterevent = null;
        if ($aftereventid && $event = $vault->get_event_by_id($aftereventid)) {
            $afterevent = $event;
        }

        return $vault->get_action_events_by_course(
            $USER, $course, $timesortfrom, $timesortto, $afterevent, $limitnum);
    }

    /**
     * Get a list of action events for the logged in user by the given
     * courses and timesort values.
     *
     * The limit number applies per course, not for the result set as a whole.
     * E.g. Requesting 3 courses with a limit of 10 will result in up to 30
     * events being returned (up to 10 per course).
     *
     * @param array $courses The courses the events must belong to
     * @param int|null $timesortfrom The start timesort value (inclusive)
     * @param int|null $timesortto The end timesort value (inclusive)
     * @param int $limitnum Limit results per course to this amount (between 1 and 50)
     * @return array A list of action_event_interface objects indexed by course id
     */
    public static function get_action_events_by_courses(
        $courses = [],
        $timesortfrom = null,
        $timesortto = null,
        $limitnum = 20
    ) {
        $return = [];

        foreach ($courses as $course) {
            $return[$course->id] = self::get_action_events_by_course(
                $course,
                $timesortfrom,
                $timesortto,
                null,
                $limitnum
            );
        }

        return $return;
    }
}
