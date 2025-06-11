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
 * Course TOC chapters web service.
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ws_course_toc_chapters extends external_api {
    /**
     * @return external_function_parameters
     */
    public static function service_parameters() {
        $parameters = [
            'courseshortname' => new external_value(PARAM_TEXT, 'Course shortname', VALUE_REQUIRED),
        ];
        return new external_function_parameters($parameters);
    }

    /**
     * @return external_single_structure
     */
    public static function service_returns() {
        $keys = [
            'chapters' => new external_single_structure(
                [
                    'chapters' => new external_multiple_structure(
                        new external_single_structure(
                            definition_helper::define_class_for_webservice('theme_snap\renderables\course_toc_chapter')
                        ),
                        'Table of content chapters',
                        VALUE_REQUIRED
                    ),
                    'listlarge' => new external_value(PARAM_ALPHAEXT, 'Additional class if the list is considered large'),
                ]
            ),
        ];

        return new external_single_structure($keys, 'course_toc_chapters');
    }

    /**
     * @param string $courseshortname
     * @return array
     */
    public static function service($courseshortname) {
        $service = course::service();
        return ['chapters' => $service->course_toc_chapters($courseshortname)];
    }
}
