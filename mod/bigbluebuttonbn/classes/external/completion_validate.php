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

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;

/**
 * External service to validate completion.
 *
 * @package   mod_bigbluebuttonbn
 * @category  external
 * @copyright 2018 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_validate extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'bigbluebuttonbnid' => new external_value(PARAM_INT, 'bigbluebuttonbn instance id'),
        ]);
    }

    /**
     * Mark activity as complete
     *
     * @param int $bigbluebuttonbnid the bigbluebuttonbn instance id
     * @return array (empty array for now)
     */
    public static function execute(
        int $bigbluebuttonbnid
    ): array {
        // Validate the bigbluebuttonbnid ID.
        [
            'bigbluebuttonbnid' => $bigbluebuttonbnid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'bigbluebuttonbnid' => $bigbluebuttonbnid,
        ]);
        $result = ['warnings' => []];
        // Fetch the session, features, and profile.
        $instance = instance::get_from_instanceid($bigbluebuttonbnid);
        if ($instance) {
            $context = $instance->get_context();

            // Validate that the user has access to this activity.
            self::validate_context($context);

            // Get list with all the users enrolled in the course.
            [$sort, $sqlparams] = users_order_by_sql('u');
            if (has_capability('moodle/course:update', $context)) {
                $users = get_enrolled_users($context, 'mod/bigbluebuttonbn:view', 0, 'u.*', $sort);
                foreach ($users as $user) {
                    // Enqueue a task for processing the completion.
                    bigbluebutton_proxy::enqueue_completion_event($instance->get_instance_data(), $user->id);
                }
            } else {
                $result['warnings'][] = [
                    'item' => 'mod_bigbluebuttonbn',
                    'itemid' => $instance->get_instance_id(),
                    'warningcode' => 'nopermissions',
                    'message' => get_string('nopermissions', 'error', 'completion_validate')
                ];
            }
        } else {
            $result['warnings'][] = [
                'item' => 'mod_bigbluebuttonbn',
                'itemid' => $bigbluebuttonbnid,
                'warningcode' => 'indexerrorbbtn',
                'message' => get_string('index_error_bbtn', 'mod_bigbluebuttonbn', $bigbluebuttonbnid)
            ];
        }
        // We might want to return a status here or some warnings.
        return $result;
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'warnings' => new external_warnings(),
        ]);
    }
}
