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

namespace core_courseformat\external;

use core\context\course as context_course;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;

/**
 * Web service to log the course overview information page has been visited on an external application.
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class log_view_overview_information extends external_api {
    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(
                PARAM_INT,
                'course id',
                VALUE_REQUIRED,
            ),
        ]);
    }

    /**
     * Execute the webservice to get overview information.
     *
     * @param int $courseid The course ID.
     * @return array The overview information.
     * @throws \moodle_exception if the course ID is invalid.
     */
    public static function execute(int $courseid): array {
        global $SITE;

        [
            'courseid' => $courseid,
        ] = external_api::validate_parameters(
            self::execute_parameters(),
            [
                'courseid' => $courseid,
            ],
        );

        $course = get_course($courseid);
        if (!$course) {
            throw new \moodle_exception('invalidcourseid', 'error', '', $courseid);
        }
        if ($course->id == SITEID) {
            throw new \moodle_exception('The site home course overview page is not supported.');
        }

        $context = context_course::instance($course->id);
        self::validate_context($context);

        require_capability('moodle/course:viewoverview', $context);

        $event = \core\event\course_overview_viewed::create(['context' => $context]);
        $event->add_record_snapshot('course', $course);
        $event->trigger();

        $result = [
            'status' => true,
            'warnings' => [],
        ];
        return $result;
    }

    /**
     * Webservice returns.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'status: true if success'),
            'warnings' => new external_warnings(),
        ]);
    }
}
