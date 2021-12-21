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

namespace mod_bigbluebuttonbn\external;

use context_module;
use external_api;
use external_files;
use external_format_value;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_util;
use external_value;
use external_warnings;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * External service to get activity per course
 *
 * This is mainly used by the mobile application.
 *
 * @package   mod_bigbluebuttonbn
 * @category  external
 * @copyright 2018 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_bigbluebuttonbns_by_courses extends external_api {
    /**
     * Describes the parameters for get_bigbluebuttonbns_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.11
     */
    public static function execute_parameters() {
        return new external_function_parameters([
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id'), 'Array of course ids', VALUE_DEFAULT, []
                ),
            ]
        );
    }

    /**
     * Returns a list of bigbluebuttonbns in a provided list of courses.
     * If no list is provided all bigbluebuttonbns that the user can view will be returned.
     *
     * @param array $courseids course ids
     * @return array of warnings and bigbluebuttonbns
     * @since Moodle 3.11
     */
    public static function execute($courseids = []) {
        global $USER;
        $warnings = [];
        $returnedbigbluebuttonbns = [];

        ['courseids' => $courseids] = self::validate_parameters(self::execute_parameters(), ['courseids' => $courseids]);
        $mycourses = [];
        if (empty($courseids)) {
            $mycourses = enrol_get_my_courses();
            $courseids = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($courseids)) {
            [$courses, $warnings] = external_util::validate_courses($courseids, $mycourses);

            // Get the bigbluebuttonbns in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $bigbluebuttonbns = get_all_instances_in_courses("bigbluebuttonbn", $courses, $USER->id);
            foreach ($bigbluebuttonbns as $bigbluebuttonbn) {
                $context = context_module::instance($bigbluebuttonbn->coursemodule);
                // Entry to return.
                $bigbluebuttonbn->name = external_format_string($bigbluebuttonbn->name, $context->id);

                [$bigbluebuttonbn->intro, $bigbluebuttonbn->introformat] = external_format_text($bigbluebuttonbn->intro,
                    $bigbluebuttonbn->introformat, $context->id, 'mod_bigbluebuttonbn', 'intro', null);
                $bigbluebuttonbn->introfiles = external_util::get_area_files($context->id,
                    'mod_bigbluebuttonbn', 'intro', false, false);

                $returnedbigbluebuttonbns[] = $bigbluebuttonbn;
            }
        }

        $result = [
            'bigbluebuttonbns' => $returnedbigbluebuttonbns,
            'warnings' => $warnings
        ];
        return $result;
    }

    /**
     * Describes the get_bigbluebuttonbns_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.11
     */
    public static function execute_returns() {
        return new external_single_structure([
                'bigbluebuttonbns' => new external_multiple_structure(
                    new external_single_structure([
                            'id' => new external_value(PARAM_INT, 'Module id'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module id'),
                            'course' => new external_value(PARAM_INT, 'Course id'),
                            'name' => new external_value(PARAM_RAW, 'Name'),
                            'intro' => new external_value(PARAM_RAW, 'Description'),
                            'meetingid' => new external_value(PARAM_RAW, 'Meeting id'),
                            'introformat' => new external_format_value('intro', 'Summary format'),
                            'introfiles' => new external_files('Files in the introduction text'),
                            'timemodified' => new external_value(PARAM_INT, 'Last time the instance was modified'),
                            'section' => new external_value(PARAM_INT, 'Course section id'),
                            'visible' => new external_value(PARAM_INT, 'Module visibility'),
                            'groupmode' => new external_value(PARAM_INT, 'Group mode'),
                            'groupingid' => new external_value(PARAM_INT, 'Grouping id'),
                        ]
                    )
                ),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
