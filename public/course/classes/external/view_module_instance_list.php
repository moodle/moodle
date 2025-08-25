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

use core\context\course as context_course;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;

/**
 * Web service to log that a module instance list has been viewed on an external application.
 *
 * @package    core_course
 * @copyright  2025 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_module_instance_list extends external_api {
    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(
                PARAM_INT,
                'Course id',
                VALUE_REQUIRED,
            ),
            'modname' => new external_value(
                PARAM_ALPHANUMEXT,
                'The module name, or "resource" if viewing resources list',
                VALUE_REQUIRED,
            ),
        ]);
    }

    /**
     * Execute the webservice to log that the module instance list has been viewed.
     *
     * @param int $courseid The course ID.
     * @param string $modname The module name.
     * @return array The status of the operation and warnings.
     * @throws \moodle_exception
     */
    public static function execute(int $courseid, string $modname): array {
        [
            'courseid' => $courseid,
            'modname' => $modname,
        ] = external_api::validate_parameters(
            self::execute_parameters(),
            [
                'courseid' => $courseid,
                'modname' => $modname,
            ],
        );

        $course = get_course($courseid);
        if (!$course) {
            throw new \moodle_exception('invalidcourseid', 'error', '', $courseid);
        }

        $context = context_course::instance($course->id);
        self::validate_context($context);

        $eventclassname = "mod_$modname\\event\\course_module_instance_list_viewed";
        if ($modname === 'resource') {
            $eventclassname = 'core\\event\\course_resources_list_viewed';
        }

        if (!class_exists($eventclassname)) {
            throw new \moodle_exception("Event not found for modname '$modname'");
        }

        $event = $eventclassname::create(['context' => $context]);
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
