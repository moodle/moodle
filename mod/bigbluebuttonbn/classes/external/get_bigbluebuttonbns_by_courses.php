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

use core_course\external\helper_for_get_mods_by_courses;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use core_external\util as external_util;

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
                helper_for_get_mods_by_courses::format_name_and_intro($bigbluebuttonbn, 'mod_bigbluebuttonbn');
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
                    new external_single_structure(array_merge(
                        helper_for_get_mods_by_courses::standard_coursemodule_elements_returns(),
                        [
                            'meetingid' => new external_value(PARAM_RAW, 'Meeting id'),
                            'timemodified' => new external_value(PARAM_INT, 'Last time the instance was modified'),
                        ]
                    ))
                ),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
