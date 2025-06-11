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

namespace theme_snap\webservice;

use theme_snap\services\course;
use core_external\external_api;
use core_external\external_value;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_multiple_structure;

defined('MOODLE_INTERNAL') || die();

/**
 * Course card web service
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ws_course_card extends external_api {
    /**
     * @return external_function_parameters
     */
    public static function service_parameters() {
        $parameters = [
            'courseshortname' => new external_value(PARAM_TEXT, 'Course shortname', VALUE_REQUIRED),
            'favorited' => new external_value(PARAM_TEXT, 'Is this course currently a favorite', VALUE_DEFAULT),
        ];
        return new external_function_parameters($parameters);
    }

    /**
     * @return external_single_structure
     */
    public static function service_returns() {
        $keys = [
            'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED),
            'shortname' => new external_value(PARAM_TEXT, 'Course shortname', VALUE_REQUIRED),
            'fullname' => new external_value(PARAM_TEXT, 'Full name of course', VALUE_REQUIRED),
            // Note PARAM_URL returns an object which wont work with a template.
            'url' => new external_value(PARAM_RAW, 'Course url', VALUE_REQUIRED),
            'visibleavatars' => new external_multiple_structure(
                new external_value(PARAM_RAW, 'Avatar HTML'),
                'An array of visible avatars, each as a single html string.', VALUE_DEFAULT, array()
            ),
            'hiddenavatars' => new external_multiple_structure(
                new external_value(PARAM_RAW, 'Avatar HTML'),
                'An array of hidden avatars, each as a single html string.', VALUE_DEFAULT, array()
            ),
            'showextralink' => new external_value(PARAM_BOOL, 'Show an extra avatar link', VALUE_REQUIRED),
            'published' => new external_value(PARAM_BOOL, 'Is this course published', VALUE_REQUIRED),
            'favorited' => new external_value(PARAM_BOOL, 'Is this course marked as a favorite', VALUE_REQUIRED),
        ];

        return new external_single_structure($keys, 'coursecard');
    }

    /**
     * @param string $courseshortname
     * @param null|int $favorited
     * @return array
     */
    public static function service($courseshortname, $favorited = null) {
        $service = course::service();
        $course = $service->coursebyshortname($courseshortname, 'id');

        $context = \context_course::instance($course->id);
        self::validate_context($context);

        if ($favorited !== null) {
            $service->setfavorite($courseshortname, $favorited == 1);
        }
        $coursecard = $service->cardbyshortname($courseshortname);
        // Convert renderable to array and skip protected / private - casting with (array) includes protected / private.
        return (array)json_decode($coursecard->model);
    }
}
