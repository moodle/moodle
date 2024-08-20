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

namespace core_course\external;

defined('MOODLE_INTERNAL') || die();

use context_user;
use core_calendar_external;
use core_course_external;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

require_once("{$CFG->dirroot}/calendar/externallib.php");
require_once("{$CFG->dirroot}/course/externallib.php");

/**
 * Class for fetching courses which have action event(s) and match given filter parameters.
 *
 * @package    core_course
 * @copyright  2022 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_enrolled_courses_with_action_events_by_timeline_classification extends external_api {

    /**
     * Returns the description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'classification' => new external_value(PARAM_ALPHA, 'future, inprogress, or past'),
                'limit' => new external_value(PARAM_INT, 'Result set limit', VALUE_DEFAULT, 0),
                'offset' => new external_value(PARAM_INT, 'Result set offset', VALUE_DEFAULT, 0),
                'sort' => new external_value(PARAM_TEXT, 'Sort string', VALUE_DEFAULT, null),
                'customfieldname' => new external_value(PARAM_ALPHANUMEXT, 'Used when classification = customfield',
                    VALUE_DEFAULT, null),
                'customfieldvalue' => new external_value(PARAM_RAW, 'Used when classification = customfield',
                    VALUE_DEFAULT, null),
                'searchvalue' => new external_value(PARAM_RAW, 'The value a user wishes to search against',
                    VALUE_DEFAULT, null),
                'eventsfrom' => new external_value(PARAM_INT, 'Optional starting timestamp for action events',
                VALUE_DEFAULT, null),
                'eventsto' => new external_value(PARAM_INT, 'Optional ending timestamp for action events',
                VALUE_DEFAULT, null),
            ]
        );
    }

    /**
     * Get courses matching the given timeline classification which have action event(s).
     *
     * Fetches courses by timeline classification which have at least one action event within the specified filtering.
     *
     * @param  string $classification past, inprogress, or future
     * @param  int $limit Number of courses with events to attempt to fetch
     * @param  int $offset Offset the full course set before timeline classification is applied
     * @param  string $sort SQL sort string for results
     * @param  string $customfieldname Custom field name used when when classification is customfield
     * @param  string $customfieldvalue Custom field value used when when classification is customfield
     * @param  string $searchvalue Text search being applied
     * @param  int $eventsfrom The start timestamp (inclusive) to search from for action events in the course
     * @param  int $eventsto The end timestamp (inclusive) to search to for action events in the course
     * @return array list of courses and any warnings
     */
    public static function execute(
        string $classification,
        int $limit = 0,
        int $offset = 0,
        ?string $sort = null,
        ?string $customfieldname = null,
        ?string $customfieldvalue = null,
        ?string $searchvalue = null,
        ?int $eventsfrom = null,
        ?int $eventsto = null
    ): array {
        global $USER;

        self::validate_context(context_user::instance($USER->id));

        $params = self::validate_parameters(
            self::execute_parameters(),
            [
                'classification' => $classification,
                'limit' => $limit,
                'offset' => $offset,
                'sort' => $sort,
                'customfieldname' => $customfieldname,
                'customfieldvalue' => $customfieldvalue,
                'searchvalue' => $searchvalue,
                'eventsfrom' => $eventsfrom,
                'eventsto' => $eventsto,
            ]
        );

        $classification = $params['classification'];
        $limit = $params['limit'];
        $offset = $params['offset'];
        $sort = $params['sort'];
        $customfieldname = $params['customfieldname'];
        $customfieldvalue = $params['customfieldvalue'];
        $searchvalue = clean_param($params['searchvalue'], PARAM_TEXT);
        $eventsfrom = $params['eventsfrom'];
        $eventsto = $params['eventsto'];
        $morecoursestofetch = true;
        $morecoursespossible = true;
        $coursesfinal = [];

        do {
            // Fetch courses.
            [
                'courses' => $coursesfetched,
                'nextoffset' => $offset,
            ] = core_course_external::get_enrolled_courses_by_timeline_classification($classification, $limit,
                    $offset, $sort, $customfieldname, $customfieldvalue, $searchvalue);

            $courseids = array_column($coursesfetched, 'id');
            $coursesfetched = array_combine($courseids, $coursesfetched);

            if (!empty($courseids)) {
                // Need to check this to know how many are expected (since it is possible for this to be less than the limit).
                $numcoursesfetched = count($courseids);
                $numfetchedwithevents = 0;

                // If less courses are fetched than we requested, we know it is not possible for more courses to be available.
                if ($numcoursesfetched < $limit) {
                    $morecoursestofetch = false;
                    $morecoursespossible = false;
                }

                // Try to fetch one action event within the time/search parameters for each course.
                $events = core_calendar_external::get_calendar_action_events_by_courses($courseids, $eventsfrom, $eventsto, 1,
                    $searchvalue);

                foreach ($events->groupedbycourse as $courseevents) {
                    $courseid = $courseevents->courseid;

                    // Only include courses which contain at least one event.
                    if (empty($courseevents->events)) {
                        unset($coursesfetched[$courseid]);
                    } else {
                        $numfetchedwithevents++;
                    }
                }

                // Add courses with events to the final course list in order.
                $coursesfinal = array_merge($coursesfinal, $coursesfetched);

                // If any courses did not have events, adjust the limit so we can attempt to fetch as many as are still required.
                if ($numfetchedwithevents < $numcoursesfetched) {
                    $limit -= $numfetchedwithevents;
                } else {
                    // If we have found as many courses as required or are available, no need to attempt fetching more.
                    $morecoursestofetch = false;
                }
            } else {
                $morecoursestofetch = false;
                $morecoursespossible = false;
            }

        } while ($morecoursestofetch);

        static $isrecursivecall = false;
        $morecoursesavailable = false;

        // Recursively call this method to check if at least one more course is available if we know that is a possibility.
        if (!$isrecursivecall && $morecoursespossible) {
            // Prevent infinite recursion.
            $isrecursivecall = true;

            $additionalcourses = self::execute(
                $classification, 1, $offset, $sort, $customfieldname, $customfieldvalue, $searchvalue, $eventsfrom, $eventsto
            );

            if (!empty($additionalcourses['courses'])) {
                $morecoursesavailable = true;
            }
        }

        return [
            'courses' => $coursesfinal,
            'nextoffset' => $offset,
            'morecoursesavailable' => $morecoursesavailable,
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return \core_external\external_description
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            [
                'courses' => new external_multiple_structure(course_summary_exporter::get_read_structure(), 'Course'),
                'nextoffset' => new external_value(PARAM_INT, 'Offset for the next request'),
                'morecoursesavailable' => new external_value(PARAM_BOOL,
                    'Whether more courses with events exist within the provided parameters'),
            ]
        );
    }
}
