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
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\restricted_context_exception;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;
use mod_bigbluebuttonbn\meeting;

/**
 * External service to fetch meeting information.
 *
 * @package   mod_bigbluebuttonbn
 * @category  external
 * @copyright 2018 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class meeting_info extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'bigbluebuttonbnid' => new external_value(PARAM_INT, 'bigbluebuttonbn instance id'),
            'groupid' => new external_value(PARAM_INT, 'bigbluebuttonbn group id', VALUE_DEFAULT, 0),
            'updatecache' => new external_value(PARAM_BOOL, 'update cache ?', VALUE_DEFAULT, false),
        ]);
    }

    /**
     * Fetch meeting information.
     *
     * @param int $bigbluebuttonbnid the bigbluebuttonbn instance id
     * @param int $groupid
     * @param bool $updatecache
     * @return array
     * @throws \moodle_exception
     * @throws restricted_context_exception
     */
    public static function execute(
        int $bigbluebuttonbnid,
        int $groupid,
        bool $updatecache = false
    ): array {
        // Validate the bigbluebuttonbnid ID.
        [
            'bigbluebuttonbnid' => $bigbluebuttonbnid,
            'groupid' => $groupid,
            'updatecache' => $updatecache,
        ] = self::validate_parameters(self::execute_parameters(), [
            'bigbluebuttonbnid' => $bigbluebuttonbnid,
            'groupid' => $groupid,
            'updatecache' => $updatecache,
        ]);

        // Fetch the session, features, and profile.
        $instance = instance::get_from_instanceid($bigbluebuttonbnid);
        $instance->set_group_id($groupid);
        if (!groups_group_visible($groupid, $instance->get_course(), $instance->get_cm())) {
            throw new restricted_context_exception();
        }
        $context = $instance->get_context();

        // Validate that the user has access to this activity and to manage recordings.
        self::validate_context($context);

        // Check if the BBB server is working.
        $serverversion = bigbluebutton_proxy::get_server_version();
        if ($serverversion === null) {
            throw new \moodle_exception('general_error_no_answer', 'mod_bigbluebuttonbn',
                bigbluebutton_proxy::get_server_not_available_url($instance),
                bigbluebutton_proxy::get_server_not_available_message($instance));
        }
        $meetinginfo = (array) meeting::get_meeting_info_for_instance($instance, $updatecache);

        // Make the structure WS friendly.
        array_walk($meetinginfo['features'], function(&$value, $key){
            $value = ['name' => $key, 'isenabled' => (bool) $value];
        });
        return $meetinginfo;
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
                'cmid' => new external_value(PARAM_INT, 'CM id'),
                'userlimit' => new external_value(PARAM_INT, 'User limit'),
                'bigbluebuttonbnid' => new external_value(PARAM_RAW, 'bigbluebuttonbn instance id'),
                'groupid' => new external_value(PARAM_INT, 'bigbluebuttonbn group id', VALUE_DEFAULT, 0),
                'meetingid' => new external_value(PARAM_RAW, 'Meeting id'),
                'openingtime' => new external_value(PARAM_INT, 'Opening time', VALUE_OPTIONAL),
                'closingtime' => new external_value(PARAM_INT, 'Closing time', VALUE_OPTIONAL),
                'statusrunning' => new external_value(PARAM_BOOL, 'Status running', VALUE_OPTIONAL),
                'statusclosed' => new external_value(PARAM_BOOL, 'Status closed', VALUE_OPTIONAL),
                'statusopen' => new external_value(PARAM_BOOL, 'Status open', VALUE_OPTIONAL),
                'statusmessage' => new external_value(PARAM_TEXT, 'Status message', VALUE_OPTIONAL),
                'startedat' => new external_value(PARAM_INT, 'Started at', VALUE_OPTIONAL),
                'moderatorcount' => new external_value(PARAM_INT, 'Moderator count', VALUE_OPTIONAL),
                'participantcount' => new external_value(PARAM_INT, 'Participant count', VALUE_OPTIONAL),
                'moderatorplural' => new external_value(PARAM_BOOL, 'Several moderators ?', VALUE_OPTIONAL),
                'participantplural' => new external_value(PARAM_BOOL, 'Several participants ?', VALUE_OPTIONAL),
                'canjoin' => new external_value(PARAM_BOOL, 'Can join'),
                'ismoderator' => new external_value(PARAM_BOOL, 'Is moderator'),
                'presentations' => new external_multiple_structure(
                    new external_single_structure([
                        'url' => new external_value(PARAM_URL, 'presentation URL'),
                        'iconname' => new external_value(PARAM_RAW, 'icon name'),
                        'icondesc' => new external_value(PARAM_TEXT, 'icon text'),
                        'name' => new external_value(PARAM_TEXT, 'presentation name'),
                    ])
                ),
                'joinurl' => new external_value(PARAM_URL, 'Join URL'),
                'guestaccessenabled' => new external_value(PARAM_BOOL, 'Guest access enabled', VALUE_OPTIONAL),
                'guestjoinurl' => new external_value(PARAM_URL, 'Guest URL', VALUE_OPTIONAL),
                'guestpassword' => new external_value(PARAM_RAW, 'Guest join password', VALUE_OPTIONAL),
                'features' => new external_multiple_structure(
                    new external_single_structure([
                        'name' => new external_value(PARAM_ALPHA, 'Feature name.'),
                        'isenabled' => new external_value(PARAM_BOOL, 'Whether the feature is enabled.'),
                    ]), 'List of features for the instance', VALUE_OPTIONAL
                ),
            ]
        );
    }
}
