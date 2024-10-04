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
 * Starred courses block external API
 *
 * @package    block_starredcourses
 * @category   external
 * @copyright  2018 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/externallib.php');

use core_course\external\course_summary_exporter;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_value;

/**
 * Starred courses block external functions.
 *
 * @copyright  2018 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_starredcourses_external extends core_course_external {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.6
     */
    public static function get_starred_courses_parameters() {
        return new external_function_parameters([
            'limit' => new external_value(PARAM_INT, 'Limit', VALUE_DEFAULT, 0),
            'offset' => new external_value(PARAM_INT, 'Offset', VALUE_DEFAULT, 0)
        ]);
    }

    /**
     * Get users starred courses.
     *
     * @param int $limit Limit
     * @param int $offset Offset
     *
     * @return  array list of courses and warnings
     */
    public static function get_starred_courses($limit, $offset) {
        global $USER, $PAGE;

        $params = self::validate_parameters(self::get_starred_courses_parameters(), [
            'limit' => $limit,
            'offset' => $offset
        ]);

        $limit = $params['limit'];
        $offset = $params['offset'];

        $usercontext = context_user::instance($USER->id);

        self::validate_context($usercontext);
        $PAGE->set_context($usercontext);
        $renderer = $PAGE->get_renderer('core');

        // Get the user favourites service, scoped to a single user (their favourites only).
        $userservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);

        // Get the favourites, by type, for the user.
        $favourites = $userservice->find_favourites_by_type('core_course', 'courses', $offset, $limit);

        $favouritecourseids = [];
        if ($favourites) {
            $favouritecourseids = array_map(
                function($favourite) {
                    return $favourite->itemid;
                }, $favourites);
        }

        // Get all courses that the current user is enroled in, restricted down to favourites.
        $filteredcourses = [];
        if ($favouritecourseids) {
            $courses = course_get_enrolled_courses_for_logged_in_user(0, 0, null, null,
                COURSE_DB_QUERY_LIMIT, $favouritecourseids);
            list($filteredcourses, $processedcount) = course_filter_courses_by_favourites(
                $courses,
                $favouritecourseids,
                0
            );
        }
        // Grab the course ids.
        $filteredcourseids = array_column($filteredcourses, 'id');

        // Filter out any favourites that are not in the list of enroled courses.
        $filteredfavourites = array_filter($favourites, function($favourite) use ($filteredcourseids) {
            return in_array($favourite->itemid, $filteredcourseids);
        });

        // Sort the favourites getting last added first.
        usort($filteredfavourites, function($a, $b) {
            if ($a->timemodified == $b->timemodified) return 0;
            return ($a->timemodified > $b->timemodified) ? -1 : 1;
        });

        $formattedcourses = array();
        foreach ($filteredfavourites as $favourite) {
            $course = get_course($favourite->itemid);
            $context = \context_course::instance($favourite->itemid);
            $canviewhiddencourses = has_capability('moodle/course:viewhiddencourses', $context);

            if ($course->visible || $canviewhiddencourses) {
                $exporter = new course_summary_exporter($course, ['context' => $context, 'isfavourite' => true]);
                $formattedcourse = $exporter->export($renderer);
                $formattedcourses[] = $formattedcourse;
            }
        }

        return $formattedcourses;
    }

    /**
     * Returns description of method result value
     *
     * @return \core_external\external_description
     * @since Moodle 3.6
     */
    public static function get_starred_courses_returns() {
        return new external_multiple_structure(course_summary_exporter::get_read_structure());
    }
}
