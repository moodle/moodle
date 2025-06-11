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
 * Course completion web service.
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ws_course_completion extends external_api {
    /**
     * @return external_function_parameters
     */
    public static function service_parameters() {
        $parameters = [
            'courseshortname' => new external_value(PARAM_TEXT, 'Course shortname', VALUE_REQUIRED),
            'unavailablesections' => new external_value(PARAM_SEQUENCE, 'Unvailable section ids', VALUE_REQUIRED),
            'unavailablemods' => new external_value(PARAM_SEQUENCE, 'Unvailable module ids', VALUE_REQUIRED),
        ];
        return new external_function_parameters($parameters);
    }

    /**
     * @return external_single_structure
     */
    public static function service_returns() {
        $keys = [
            'unavailablesections' => new external_value(PARAM_SEQUENCE, 'Unavailable sections', VALUE_REQUIRED),
            'unavailablemods' => new external_value(PARAM_SEQUENCE, 'Unavailable mods', VALUE_REQUIRED),
            'changedsectionhtml' => new external_multiple_structure(
                new external_single_structure(
                    [
                        'number' => new external_value(PARAM_INT, 'section number'),
                        'html'   => new external_value(PARAM_RAW, 'html'),
                    ],
                    'Newly available sections', VALUE_REQUIRED
                )
            ),
            'changedmodhtml' => new external_multiple_structure(
                new external_single_structure(
                    [
                        'id' => new external_value(PARAM_INT, 'id'),
                        'html' => new external_value(PARAM_RAW, 'html'),
                    ],
                    'Newly available mods', VALUE_REQUIRED
                )
            ),
            'toc' => new external_single_structure(
                definition_helper::define_class_for_webservice('theme_snap\renderables\course_toc'),
                'Table of contents', VALUE_REQUIRED
            ),
        ];
        return new external_single_structure($keys, 'course_completion');
    }

    /**
     * @param string $courseshortname
     * @param string $unavailablesections
     * @param string $unavailablemods
     * @return array
     */
    public static function service($courseshortname, $unavailablesections, $unavailablemods) {
        $service = course::service();
        $previouslyunavailablesections = !empty($unavailablesections) ? explode(',', $unavailablesections) : [];
        $previouslyunavailablemods = !empty($unavailablemods) ? explode(',', $unavailablemods) : [];
        $coursecompletion = $service->course_completion(
            $courseshortname,
            $previouslyunavailablesections,
            $previouslyunavailablemods
        );
        return $coursecompletion;
    }
}
