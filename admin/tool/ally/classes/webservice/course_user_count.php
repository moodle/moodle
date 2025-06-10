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
 * Gets the amount of students and instructors per course.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\webservice;

use tool_ally\local;

/**
 * Gets the amount of students and instructors per course.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_user_count extends loggable_external_api {
    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {
        return new \external_function_parameters([
            'id' => new \external_value(PARAM_INT, 'Course id'),
        ]);
    }

    /**
     * @return \external_single_structure
     */
    public static function service_returns() {
        return new \external_single_structure([
            'id' => new \external_value(PARAM_INT, 'Course id'),
            'studentcount' => new \external_value(PARAM_INT, 'Student count.'),
            'instructorcount' => new \external_value(PARAM_INT, 'Instructor count.'),
        ]);
    }

    /**
     * @param int $id Course id.
     * @return array
     */
    public static function execute_service($id) {
        $syscontext = \context_system::instance();
        self::validate_context($syscontext);

        require_capability('moodle/course:view', $syscontext);
        require_capability('moodle/course:viewhiddencourses', $syscontext);

        $params = self::validate_parameters(self::service_parameters(), ['id' => $id]);

        $context = \context_course::instance($params['id']);

        $instructorcap = 'moodle/course:manageactivities';
        $anyusercap = '';
        $onlyactive = true;

        $instructorcount = local::count_enrolled_users($context, $instructorcap, null, $onlyactive);
        $anyusercount = local::count_enrolled_users($context, $anyusercap, null, $onlyactive);

        $studentcount = $anyusercount - $instructorcount;

        return [
            'id' => $context->instanceid,
            'studentcount' => $studentcount,
            'instructorcount' => $instructorcount,
        ];
    }
}
