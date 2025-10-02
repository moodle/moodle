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
use core_courseformat\output\local\overview\overviewtable;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use stdClass;
use core\output\renderer_helper;

/**
 * Class get_overview_information
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_overview_information extends external_api {
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
            'modname' => new external_value(
                PARAM_ALPHANUMEXT,
                'The module name, or "resource" to get all resources overview',
                VALUE_REQUIRED,
            ),
        ]);
    }

    /**
     * Execute the webservice to get overview information.
     *
     * @param int $courseid The course ID.
     * @param string $modname The module name.
     * @return stdClass The overview information.
     * @throws \moodle_exception if the course ID is invalid or the module name is not found.
     */
    public static function execute(int $courseid, string $modname): stdClass {
        [
            'courseid' => $courseid,
            'modname' => $modname,
        ] = external_api::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'modname' => $modname,
        ]);

        // Validate course ID.
        $course = get_course($courseid);
        if (!$course) {
            throw new \moodle_exception('invalidcourseid', 'error', '', $courseid);
        }

        $context = context_course::instance($course->id);
        self::validate_context($context);

        require_capability('moodle/course:viewoverview', $context);

        $page = \core\di::get(renderer_helper::class)->get_page();
        $format = course_get_format($course);
        $renderer = $format->get_renderer($page);

        $overvietableclass = $format->get_output_classname('overview\\overviewtable');
        /** @var overviewtable $overviewtable */
        $overviewtable = new $overvietableclass($course, $modname);

        $exporter = $overviewtable->get_exporter($context);
        return $exporter->export($renderer);
    }

    /**
     * Webservice returns.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return overviewtable::get_read_structure();
    }
}
