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

use core_course\dndupload_handler;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Class for exporting a course file handlers.
 *
 * @package    core_courseformat
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.2
 */
class file_handlers extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'course id', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * Return the list of available file handlers.
     *
     * @param int $courseid the course id
     * @return array of file hanlders.
     */
    public static function execute(int $courseid): array {
        global $CFG;

        require_once($CFG->dirroot . '/course/lib.php');

        $params = external_api::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
        ]);
        $courseid = $params['courseid'];

        self::validate_context(\context_course::instance($courseid));

        $format = course_get_format($courseid);
        $course = $format->get_course();

        $handler = new dndupload_handler($course, null);

        $data = $handler->get_js_data();
        return $data->filehandlers ?? [];
    }

    /**
     * Webservice returns.
     *
     * @return external_multiple_structure
     */
    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure([
                'extension' => new external_value(PARAM_TEXT, 'File extension'),
                'module' => new external_value(PARAM_TEXT, 'Target module'),
                'message' => new external_value(PARAM_TEXT, 'Output message'),
            ])
        );
    }
}
