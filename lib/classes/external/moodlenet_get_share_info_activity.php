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
use core\moodlenet\utilities;
use core\oauth2\api;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;

/**
 * The external API to het the activity information for MoodleNet sharing.
 *
 * @package    core
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodlenet_get_share_info_activity extends external_api {

    /**
     * Returns description of parameters.
     *
     * @return external_function_parameters
     * @since Moodle 4.2
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'The cmid of the activity', VALUE_REQUIRED),
        ]);
    }

    /**
     * External function to get the activity information.
     *
     * @param int $cmid The course module id.
     * @return array
     * @since Moodle 4.2
     */
    public static function execute(int $cmid): array {
        global $CFG, $USER;

        [
            'cmid' => $cmid
        ] = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid
        ]);

        // Get course module.
        $coursemodule = get_coursemodule_from_id(false, $cmid);
        if (!$coursemodule) {
            return self::return_errors($cmid, 'errorgettingactivityinformation', get_string('invalidcoursemodule', 'error'));
        }

        // Get course.
        $course = get_course($coursemodule->course);

        // Check capability.
        $coursecontext = context_course::instance($course->id);
        $usercanshare = utilities::can_user_share($coursecontext, $USER->id);
        if (!$usercanshare) {
            return self::return_errors($cmid, 'errorpermission',
                get_string('nopermissions', 'error', get_string('moodlenet:sharetomoodlenet', 'moodle')));
        }

        $warnings = [];
        $supporturl = '';
        $issuerid = get_config('moodlenet', 'oauthservice');

        if (empty($issuerid)) {
            return self::return_errors(0, 'errorissuernotset', get_string('moodlenet:issuerisnotset', 'moodle'));
        }

        if ($CFG->supportavailability && $CFG->supportavailability != CONTACT_SUPPORT_DISABLED) {
            if (!empty($CFG->supportpage)) {
                $supporturl = $CFG->supportpage;
            } else {
                $supporturl = $CFG->wwwroot . '/user/contactsitesupport.php';
            }
        }

        // Get the issuer.
        $issuer = api::get_issuer($issuerid);
        // Validate the issuer and check if it is enabled or not.
        if (!utilities::is_valid_instance($issuer)) {
            return self::return_errors($issuerid, 'errorissuernotenabled', get_string('moodlenet:issuerisnotenabled', 'moodle'));
        }

        return [
            'status' => true,
            'name' => $coursemodule->name,
            'type' => get_string('modulename', $coursemodule->modname),
            'server' => $issuer->get_display_name(),
            'supportpageurl' => $supporturl,
            'issuerid' => $issuerid,
            'warnings' => $warnings
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
            'name' => new external_value(PARAM_TEXT, 'Activity name'),
            'type' => new external_value(PARAM_TEXT, 'Activity type'),
            'server' => new external_value(PARAM_TEXT, 'MoodleNet server'),
            'supportpageurl' => new external_value(PARAM_URL, 'Support page URL'),
            'issuerid' => new external_value(PARAM_INT, 'MoodleNet issuer id'),
            'status' => new external_value(PARAM_BOOL, 'status: true if success'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Handle return error.
     *
     * @param int $itemid Item id.
     * @param string $warningcode Warning code.
     * @param string $message Message.
     * @param int $issuerid Issuer id.
     * @return array
     */
    protected static function return_errors(int $itemid, string $warningcode, string $message, int $issuerid = 0): array {
        $warnings[] = [
            'item' => $itemid,
            'warningcode' => $warningcode,
            'message' => $message
        ];

        return [
            'status' => false,
            'name' => '',
            'type' => '',
            'server' => '',
            'supportpageurl' => '',
            'issuerid' => $issuerid,
            'warnings' => $warnings
        ];
    }
}
