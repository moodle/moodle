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
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Web service to fetch course content items for a specific section.
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_section_content_items extends external_api {
    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'ID of the course', VALUE_REQUIRED),
            'sectionid' => new external_value(PARAM_INT, 'The section id', VALUE_REQUIRED),
        ]);
    }

    /**
     * Given a course ID fetch all accessible modules for that course
     *
     * @param int $courseid The course we want to fetch the modules for
     * @param int $sectionid The section we want to fetch the modules for
     * @return array Contains array of modules and their metadata
     */
    public static function execute(int $courseid, int $sectionid): array {
        global $USER;

        [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
        ] = external_api::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
        ]);

        $coursecontext = context_course::instance($courseid);
        self::validate_context($coursecontext);

        $course = get_course($courseid);
        $sectioninfo = get_fast_modinfo($course)->get_section_info_by_id($sectionid);

        $contentitemservice = \core_course\local\factory\content_item_service_factory::get_content_item_service();

        $contentitems = $contentitemservice->get_content_items_for_user_in_course($USER, $course, [], $sectioninfo);
        return ['content_items' => $contentitems];
    }

    /**
     * Webservice returns.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'content_items' => new external_multiple_structure(
                \core_course\local\exporters\course_content_item_exporter::get_read_structure()
            ),
        ]);
    }
}
