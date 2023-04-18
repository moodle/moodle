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
 * The external API to check whether a user has authorized for a given MoodleNet OAuth 2 issuer.
 *
 * @package    core
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodlenet_auth_check extends external_api {

    /**
     * Returns description of parameters.
     *
     * @return external_function_parameters
     * @since Moodle 4.2
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'issuerid' => new external_value(PARAM_INT, 'OAuth 2 issuer ID', VALUE_REQUIRED),
            'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED),
        ]);
    }

    /**
     * External function to check if the user is already authorized with MoodleNet.
     *
     * @param int $issuerid Issuer Id.
     * @param int $courseid The course ID that contains the activity which being shared
     * @return array
     * @since Moodle 4.2
     */
    public static function execute(int $issuerid, int $courseid): array {
        global $USER;
        [
            'issuerid' => $issuerid,
            'courseid' => $courseid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'issuerid' => $issuerid,
            'courseid' => $courseid,
        ]);

        // Check capability.
        $coursecontext = context_course::instance($courseid);
        $usercanshare = utilities::can_user_share($coursecontext, $USER->id);
        if (!$usercanshare) {
            return self::return_errors($courseid, 'errorpermission',
                get_string('nopermissions', 'error', get_string('moodlenet:sharetomoodlenet', 'moodle')));
        }

        // Get the issuer.
        $issuer = api::get_issuer($issuerid);
        // Validate the issuer and check if it is enabled or not.
        if (!utilities::is_valid_instance($issuer)) {
            return self::return_errors($issuerid, 'errorissuernotenabled', get_string('invalidparameter', 'debug'));
        }

        $returnurl = new moodle_url('/admin/moodlenet_oauth2_callback.php');
        $returnurl->param('issuerid', $issuerid);
        $returnurl->param('callback', 'yes');
        $returnurl->param('sesskey', sesskey());

        // Get the OAuth Client.
        if (!$oauthclient = api::get_user_oauth_client($issuer, $returnurl, moodlenet_client::API_SCOPE_CREATE_RESOURCE, true)) {
            return self::return_errors($issuerid, 'erroroauthclient', get_string('invalidparameter', 'debug'));
        }

        $status = false;
        $warnings = [];
        $loginurl = '';

        if (!$oauthclient->is_logged_in()) {
            $loginurl = $oauthclient->get_login_url()->out(false);
        } else {
            $status = true;
        }

        return [
            'status' => $status,
            'loginurl' => $loginurl,
            'warnings' => $warnings,
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
            'loginurl' => new external_value(PARAM_RAW, 'Login url'),
            'status' => new external_value(PARAM_BOOL, 'status: true if success'),
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
            'loginurl' => '',
            'warnings' => $warnings,
        ];
    }
}
