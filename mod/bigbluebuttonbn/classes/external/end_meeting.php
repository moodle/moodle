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

use core\notification;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\bigbluebutton;
use mod_bigbluebuttonbn\local\exceptions\bigbluebutton_exception;
use mod_bigbluebuttonbn\logger;
use mod_bigbluebuttonbn\meeting;
use restricted_context_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * External service to end a meeting.
 *
 * @package   mod_bigbluebuttonbn
 * @category  external
 * @copyright 2018 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class end_meeting extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'bigbluebuttonbnid' => new external_value(PARAM_INT, 'bigbluebuttonbn instance id'),
            'groupid' => new external_value(PARAM_INT, 'bigbluebuttonbn group id', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Updates a recording
     *
     * @param int $bigbluebuttonbnid the bigbluebuttonbn instance id
     * @param int $groupid the groupid (either 0 or the groupid)
     * @return array (empty array for now)
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     * @throws restricted_context_exception
     */
    public static function execute(
        int $bigbluebuttonbnid,
        int $groupid
    ): array {
        // Validate the bigbluebuttonbnid ID.
        [
            'bigbluebuttonbnid' => $bigbluebuttonbnid,
            'groupid' => $groupid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'bigbluebuttonbnid' => $bigbluebuttonbnid,
            'groupid' => $groupid,
        ]);

        // Fetch the session, features, and profile.
        $instance = instance::get_from_instanceid($bigbluebuttonbnid);
        if (empty($instance)) {
            throw new \moodle_exception('Unknown Instance');
        }
        if (!groups_group_visible($groupid, $instance->get_course(), $instance->get_cm())) {
            throw new restricted_context_exception();
        }
        $instance->set_group_id($groupid);
        $context = $instance->get_context();

        // Validate that the user has access to this activity and to manage recordings.
        self::validate_context($context);

        if (!$instance->user_can_end_meeting()) {
            throw new restricted_context_exception();
        }
        // Execute the end command.
        $meeting = new meeting($instance);
        try {
            $meeting->end_meeting();
        } catch (bigbluebutton_exception $e) {
            return [
                'warnings' => [
                    [
                        'item' => $instance->get_meeting_name(),
                        'itemid' => $instance->get_instance_id(),
                        'warningcode' => 'notFound',
                        'message' => $e->getMessage()
                    ]
                ]
            ];
        }
        logger::log_meeting_ended_event($instance);
        // Update the cache.
        $meeting->update_cache();
        notification::add(get_string('end_session_notification', 'mod_bigbluebuttonbn'), notification::INFO);
        return [];
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'warnings' => new \external_warnings()
        ]);
    }
}
