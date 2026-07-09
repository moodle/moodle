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

namespace block_timeline\external;

use core_calendar\external\events_related_objects_cache;
use core_calendar\external\event_exporter;
use core_calendar\local\api as local_api;
use core_course\external\course_summary_exporter;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');

/**
 * External function to fetch enrolled courses together with their timeline events.
 *
 * Returns a paginated list of in-progress courses, each with up to
 * EVENTS_PER_COURSE action events for the given time window.
 *
 * @package    block_timeline
 * @copyright  2026 Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_courses_with_events extends external_api {
    /** Maximum number of events returned per course (EVENTS_PER_PAGE + 1 sentinel for hasMore detection). */
    const EVENTS_PER_COURSE = 7;

    /** Number of courses fetched per page. */
    const COURSES_PER_PAGE = 2;

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'starttime'   => new external_value(PARAM_INT, 'Events on or after this timestamp', VALUE_DEFAULT, null),
            'endtime'     => new external_value(PARAM_INT, 'Events on or before this timestamp', VALUE_DEFAULT, null),
            'limit'       => new external_value(PARAM_INT, 'Number of courses per page', VALUE_DEFAULT, self::COURSES_PER_PAGE),
            'offset'      => new external_value(PARAM_INT, 'Courses result set offset', VALUE_DEFAULT, 0),
            'searchvalue' => new external_value(PARAM_RAW, 'Optional search string', VALUE_DEFAULT, null),
        ]);
    }

    /**
     * Fetch in-progress enrolled courses with their action events.
     *
     * @param int|null $starttime Events on or after this timestamp.
     * @param int|null $endtime Events on or before this timestamp.
     * @param int $limit Number of courses per page.
     * @param int $offset Courses result set offset.
     * @param string|null $searchvalue Optional search string.
     * @return array
     */
    public static function execute(
        ?int $starttime = null,
        ?int $endtime = null,
        int $limit = self::COURSES_PER_PAGE,
        int $offset = 0,
        ?string $searchvalue = null
    ): array {
        global $PAGE, $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'starttime'   => $starttime,
            'endtime'     => $endtime,
            'limit'       => $limit,
            'offset'      => $offset,
            'searchvalue' => $searchvalue,
        ]);

        if (!isloggedin() || isguestuser()) {
            throw new \require_login_exception('You must be logged in to view timeline courses.');
        }
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        $searchvalue = isset($params['searchvalue']) ? clean_param($params['searchvalue'], PARAM_TEXT) : null;

        // Fetch enrolled in-progress courses for this page.
        $requiredproperties = course_summary_exporter::define_properties();
        $fields        = join(',', array_keys($requiredproperties));
        $hiddencourses = get_hidden_courses_on_timeline();

        $courses = course_get_enrolled_courses_for_logged_in_user(
            0,
            $params['offset'],
            'fullname ASC',
            $fields,
            COURSE_DB_QUERY_LIMIT,
            [],
            $hiddencourses
        );
        [$pagedcourses] = course_filter_courses_by_timeline_classification(
            $courses,
            COURSE_TIMELINE_ALL,
            $params['limit'] + 1
        );

        $morecoursesavailable = count($pagedcourses) > $params['limit'];
        if ($morecoursesavailable) {
            array_pop($pagedcourses);
        }
        $nextoffset = $params['offset'] + count($pagedcourses);

        if (empty($pagedcourses)) {
            return ['courses' => [], 'nextoffset' => $nextoffset, 'morecoursesavailable' => false];
        }

        // Fetch events for all courses in one call.
        $courseobjects = array_values($pagedcourses);
        $events = local_api::get_action_events_by_courses(
            $courseobjects,
            $params['starttime'],
            $params['endtime'],
            self::EVENTS_PER_COURSE,
            $searchvalue
        );

        // Index exported events by course id.
        $renderer   = $PAGE->get_renderer('core_calendar');
        $eventscache = new events_related_objects_cache(array_merge(...array_values($events)));

        $eventsbycourse = [];
        foreach ($events as $courseid => $courseevents) {
            $eventsbycourse[$courseid] = [];
            foreach ($courseevents as $event) {
                $relatedobjects = [
                    'context' => $eventscache->get_context($event),
                    'course'  => $eventscache->get_course($event),
                ];
                $exporter = new event_exporter($event, $relatedobjects);
                $exportedevent = (array) $exporter->export($renderer);
                $exportedevent['formattedday'] = userdate(
                    $exportedevent['timeusermidnight'],
                    get_string('strftimedaydate', 'langconfig')
                );
                $eventsbycourse[$courseid][] = $exportedevent;
            }
        }

        // Build the combined response.
        $exportedcourses = [];
        foreach ($courseobjects as $course) {
            \context_helper::preload_from_record($course);
            $coursecontext = \context_course::instance($course->id);
            $exporter      = new course_summary_exporter($course, ['context' => $coursecontext]);
            $exported      = (array) $exporter->export($PAGE->get_renderer('core'));

            $exported['events'] = $eventsbycourse[$course->id] ?? [];
            $exportedcourses[]  = $exported;
        }

        return [
            'courses'              => $exportedcourses,
            'nextoffset'           => $nextoffset,
            'morecoursesavailable' => $morecoursesavailable,
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        $coursestructure = course_summary_exporter::get_read_structure();
        // Append events array to the standard course structure.
        $eventstructure = event_exporter::get_read_structure();
        $eventstructure->keys['formattedday'] = new external_value(PARAM_TEXT, 'Day formatted for display using the site language');
        $coursestructure->keys['events'] = new external_multiple_structure(
            $eventstructure,
            'Action events for this course'
        );

        return new external_single_structure([
            'courses'              => new external_multiple_structure($coursestructure, 'List of courses with events'),
            'nextoffset'           => new external_value(PARAM_INT, 'Offset for next page request'),
            'morecoursesavailable' => new external_value(PARAM_BOOL, 'Whether more courses are available'),
        ]);
    }
}
