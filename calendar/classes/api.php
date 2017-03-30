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
 * Contains class containing the calendar API.
 *
 * @package    core_calendar
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/calendar/lib.php');

use core_calendar\local\api as local_api;

/**
 * Class containing the calendar API.
 *
 * @package    core_calendar
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Get calendar events
     *
     * @param int $tstart Start time of time range for events
     * @param int $tend End time of time range for events
     * @param array|int|boolean $users array of users, user id or boolean for all/no user events
     * @param array|int|boolean $groups array of groups, group id or boolean for all/no group events
     * @param array|int|boolean $courses array of courses, course id or boolean for all/no course events
     * @param boolean $withduration whether only events starting within time range selected
     *                              or events in progress/already started selected as well
     * @param boolean $ignorehidden whether to select only visible events or all events
     * @return array $events of selected events or an empty array if there aren't any (or there was an error)
     */
    public static function get_events($tstart, $tend, $users, $groups, $courses, $withduration = true, $ignorehidden = true) {
        $fixedparams = array_map(function($param) {
            if ($param === true) {
                return null;
            }

            if (!is_array($param)) {
                return [$param];
            }

            return $param;
        }, [$users, $groups, $courses]);

        $mapper = \core_calendar\local\event\core_container::get_event_mapper();
        $events = local_api::get_events(
            $tstart,
            $tend,
            null,
            null,
            null,
            null,
            40,
            null,
            $fixedparams[0],
            $fixedparams[1],
            $fixedparams[2],
            $withduration,
            $ignorehidden
        );

        return array_reduce($events, function($carry, $event) use ($mapper) {
            return $carry + [$event->get_id() => $mapper->from_event_to_stdclass($event)];
        }, []);
    }

    /**
     * Get a list of action events for the logged in user by the given
     * timesort values.
     *
     * @param int|null $timesortfrom The start timesort value (inclusive)
     * @param int|null $timesortto The end timesort value (inclusive)
     * @param int|null $aftereventid Only return events after this one
     * @param int $limitnum Limit results to this amount (between 1 and 50)
     * @return array A list of event type objects
     */
    public static function get_action_events_by_timesort(
        $timesortfrom = null,
        $timesortto = null,
        $aftereventid = null,
        $limitnum = 20
    ) {
        $mapper = \core_calendar\local\event\core_container::get_event_mapper();
        $events = local_api::get_action_events_by_timesort($timesortfrom, $timesortto, $aftereventid, $limitnum);

        return array_map(function($event) use ($mapper) {
            return $mapper->from_event_to_legacy_event($event);
        }, $events);
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
        $mapper = \core_calendar\local\event\core_container::get_event_mapper();
        $events = local_api::get_action_events_by_course(
            $course, $timesortfrom, $timesortto, $aftereventid, $limitnum);

        return array_map(function($event) use ($mapper) {
            return $mapper->from_event_to_legacy_event($event);
        }, $events);
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
     * @return array A list of event objects indexed by course id
     */
    public static function get_action_events_by_courses(
        $courses = [],
        $timesortfrom = null,
        $timesortto = null,
        $limitnum = 20
    ) {
        $return = [];
        $mapper = \core_calendar\local\event\core_container::get_event_mapper();
        $eventsbycourses = local_api::get_action_events_by_courses(
            $courses, $timesortfrom, $timesortto, $limitnum);

        foreach (array_keys($eventsbycourses) as $courseid) {
            $return[$courseid] = array_map(function($event) use ($mapper) {
                return $mapper->from_event_to_legacy_event($event);
            }, $eventsbycourses[$courseid]);
        }

        return $return;
    }
}
