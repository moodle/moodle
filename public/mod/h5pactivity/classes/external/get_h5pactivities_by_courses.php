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

namespace mod_h5pactivity\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use context_module;
use core_h5p\factory;

/**
 * This is the external method for returning a list of h5p activities.
 *
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_h5pactivities_by_courses extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters (
            [
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id'), 'Array of course ids', VALUE_DEFAULT, []
                ),
            ]
        );
    }

    /**
     * Returns a list of h5p activities in a provided list of courses.
     * If no list is provided all h5p activities that the user can view will be returned.
     *
     * @param  array $courseids course ids
     * @return array of h5p activities and warnings
     * @since Moodle 3.9
     */
    public static function execute(array $courseids): array {
        global $PAGE;

        $warnings = [];
        $returnedh5pactivities = [];

        $params = external_api::validate_parameters(self::execute_parameters(), [
            'courseids' => $courseids
        ]);

        $mycourses = [];
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            $factory = new factory();

            list($courses, $warnings) = \core_external\util::validate_courses($params['courseids'], $mycourses);
            $output = $PAGE->get_renderer('core');

            // Get the h5p activities in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $h5pactivities = get_all_instances_in_courses('h5pactivity', $courses);
            foreach ($h5pactivities as $h5pactivity) {
                $context = context_module::instance($h5pactivity->coursemodule);
                // Remove fields that are not from the h5p activity (added by get_all_instances_in_courses).
                unset($h5pactivity->coursemodule, $h5pactivity->context,
                    $h5pactivity->visible, $h5pactivity->section,
                    $h5pactivity->groupmode, $h5pactivity->groupingid);

                $exporter = new h5pactivity_summary_exporter($h5pactivity,
                    ['context' => $context, 'factory' => $factory]);
                $summary = $exporter->export($output);
                $returnedh5pactivities[] = $summary;
            }
        }

        $h5pglobalsettings = [
            'enablesavestate' => get_config('mod_h5pactivity', 'enablesavestate'),
        ];
        if (!empty($h5pglobalsettings['enablesavestate'])) {
            $h5pglobalsettings['savestatefreq'] = get_config('mod_h5pactivity', 'savestatefreq');
        }

        $result = [
            'h5pactivities' => $returnedh5pactivities,
            'h5pglobalsettings' => $h5pglobalsettings,
            'warnings' => $warnings
        ];
        return $result;
    }

    /**
     * Describes the get_h5pactivities_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.9
     */
    public static function execute_returns() {
        return new external_single_structure(
            [
                'h5pactivities' => new external_multiple_structure(
                    h5pactivity_summary_exporter::get_read_structure()
                ),
                'h5pglobalsettings' => new external_single_structure(
                    [
                        'enablesavestate' => new external_value(PARAM_BOOL, 'Whether saving state is enabled.'),
                        'savestatefreq' => new external_value(PARAM_INT, 'How often (in seconds) state is saved.', VALUE_OPTIONAL),
                    ],
                    'H5P global settings',
                    VALUE_OPTIONAL,
                ),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
