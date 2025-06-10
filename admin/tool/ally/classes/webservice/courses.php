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
 * Ally course retrieval webservice class.
 *
 * @package   tool_ally
 * @author    Sam Chaffee
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\webservice;

/**
 * Ally course retrieval webservice class.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courses extends loggable_external_api {

    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {
        return new \external_function_parameters(
                [
                    'page'    => new \external_value(PARAM_INT, 'page number (0 based)', VALUE_DEFAULT, -1),
                    'perpage' => new \external_value(PARAM_INT, 'items per page', VALUE_DEFAULT, 100),
                ]
        );
    }

    /**
     * @return \external_multiple_structure
     */
    public static function service_returns() {
        return new \external_multiple_structure(
                new \external_single_structure(
                        [
                            'id' => new \external_value(PARAM_INT, 'course id'),
                            'shortname' => new \external_value(PARAM_TEXT, 'course short name'),
                            'fullname' => new \external_value(PARAM_TEXT, 'full name'),
                        ]
                )
        );
    }

    /**
     * @param int $page
     * @param int $perpage
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     */
    public static function execute_service($page, $perpage) {
        global $DB;

        $syscontext = \context_system::instance();
        self::validate_context($syscontext);

        require_capability('moodle/course:view', $syscontext);
        require_capability('moodle/course:viewhiddencourses', $syscontext);

        $params = self::validate_parameters(self::service_parameters(), ['page' => $page, 'perpage' => $perpage]);

        $limitfrom = 0;
        $limitnum = 0;
        if ($params['page'] > -1 && $params['perpage'] > 0) {
            $limitfrom = $params['page'] * $params['perpage'];
            $limitnum = $params['perpage'];
        }

        $courses = $DB->get_recordset('course', null, 'id', 'id, shortname, fullname',
                $limitfrom, $limitnum);

        $courseinfos = [];
        foreach ($courses as $course) {
            $context = \context_course::instance($course->id);
            $courseinfos[] = [
                'id' => $course->id,
                'fullname' => \external_format_string($course->fullname, $context->id),
                'shortname' => \external_format_string($course->shortname, $context->id),
            ];
        }

        $courses->close();

        return $courseinfos;
    }
}
