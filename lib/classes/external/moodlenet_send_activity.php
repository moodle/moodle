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

namespace core\external;

use context_course;
use core\http_client;
use core\moodlenet\activity_sender;
use core\moodlenet\moodlenet_client;
use core\moodlenet\utilities;
use core\oauth2\api;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use moodle_url;

/**
 * The external API to send activity to MoodleNet.
 *
 * @package    core
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodlenet_send_activity extends external_api {

    /**
     * Describes the parameters for sending the activity.
     *
     * @return external_function_parameters
     * @since Moodle 4.2
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'issuerid' => new external_value(PARAM_INT, 'OAuth 2 issuer ID', VALUE_REQUIRED),
            'cmid' => new external_value(PARAM_INT, 'Course module ID', VALUE_REQUIRED),
            'shareformat' => new external_value(PARAM_INT, 'Share format', VALUE_REQUIRED),
        ]);
    }

    /**
     * External function to send the activity to MoodleNet.
     *
     * @param int $issuerid The MoodleNet OAuth 2 issuer ID
     * @param int $cmid The course module ID of the activity that being shared
     * @param int $shareformat The share format being used, as defined by \core\moodlenet\activity_sender
     * @return array
     * @since Moodle 4.2
     */
    public static function execute(int $issuerid, int $cmid, int $shareformat): array {
        global $CFG, $USER;

        [
            'issuerid' => $issuerid,
            'cmid' => $cmid,
            'shareformat' => $shareformat,
        ] = self::validate_parameters(self::execute_parameters(), [
            'issuerid' => $issuerid,
            'cmid' => $cmid,
            'shareformat' => $shareformat,
        ]);

        // Check capability.
        [$course] = get_course_and_cm_from_cmid($cmid);
        $coursecontext = context_course::instance($course->id);
        $usercanshare = utilities::can_user_share($coursecontext, $USER->id);
        if (!$usercanshare) {
            return self::return_errors($cmid, 'errorpermission',
                get_string('nopermissions', 'error', get_string('moodlenet:sharetomoodlenet', 'moodle')));
        }

        // Check format.
        if (!in_array($shareformat, [activity_sender::SHARE_FORMAT_BACKUP])) {
            return self::return_errors($shareformat, 'errorinvalidformat', get_string('invalidparameter', 'debug'));
        }

        // Get the issuer.
        $issuer = api::get_issuer($issuerid);
        // Validate the issuer and check if it is enabled or not.
        if (!utilities::is_valid_instance($issuer)) {
            return self::return_errors($issuerid, 'errorissuernotenabled', get_string('invalidparameter', 'debug'));
        }

        // Get the OAuth Client.
        if (!$oauthclient = api::get_user_oauth_client(
            $issuer,
            new moodle_url($CFG->wwwroot),
            moodlenet_client::API_SCOPE_CREATE_RESOURCE
        )) {
            return self::return_errors($issuerid, 'erroroauthclient', get_string('invalidparameter', 'debug'));
        }

        // Check login state.
        if (!$oauthclient->is_logged_in()) {
            return self::return_errors($issuerid, 'erroroauthclient', get_string('moodlenet:issuerisnotauthorized', 'moodle'));
        }

        // Get the HTTP Client.
        $client = new http_client();

        // Share activity.
        try {
            $moodlenetclient = new moodlenet_client($client, $oauthclient);
            $activitysender = new activity_sender($cmid, $USER->id, $moodlenetclient, $oauthclient, $shareformat);
            $result = $activitysender->share_activity();
            if (empty($result['drafturl'])) {
                return self::return_errors($result['responsecode'], 'errorsendingactivity',
                    get_string('moodlenet:cannotconnecttoserver', 'moodle'));
            }
        } catch (\moodle_exception $e) {
            return self::return_errors(0, 'errorsendingactivity', $e->getMessage());
        }

        return [
            'status' => true,
            'resourceurl' => $result['drafturl'],
            'warnings' => [],
        ];
    }

    /**
     * Describes the data returned from the external function.
     *
     * @return external_single_structure
     * @since Moodle 4.2
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'Status: true if success'),
            // We used PARAM_TEXT instead of PARAM_URL because the URL return from MoodleNet may contain some characters.
            // It does not match with PARAM_URL, but the URL still works.
            // Since we just show the response resource URL to the user for them to navigate to MoodleNet, it would be safe.
            'resourceurl' => new external_value(PARAM_TEXT, 'Resource URL from MoodleNet'),
            'warnings' => new external_warnings(),
        ]);
    }

    /**
     * Handle return error.
     *
     * @param int $itemid Item id
     * @param string $warningcode Warning code
     * @param string $message Message
     * @return array
     */
    protected static function return_errors(int $itemid, string $warningcode, string $message): array {
        $warnings[] = [
            'item' => $itemid,
            'warningcode' => $warningcode,
            'message' => $message,
        ];

        return [
            'status' => false,
            'resourceurl' => '',
            'warnings' => $warnings,
        ];
    }
}
