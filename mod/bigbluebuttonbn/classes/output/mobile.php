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

/**
 * Mobile output class for bigbluebuttonbn
 *
 * @package    mod_bigbluebuttonbn
 * @copyright  2018 onwards, Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
namespace mod_bigbluebuttonbn\output;

defined('MOODLE_INTERNAL') || die();

use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\exceptions\bigbluebutton_exception;
use mod_bigbluebuttonbn\local\exceptions\meeting_join_exception;
use mod_bigbluebuttonbn\local\exceptions\server_not_available_exception;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;
use mod_bigbluebuttonbn\logger;
use mod_bigbluebuttonbn\meeting;

global $CFG;
require_once($CFG->dirroot . '/lib/grouplib.php');

/**
 * Mobile output class for bigbluebuttonbn
 *
 * @package    mod_bigbluebuttonbn
 * @copyright  2018 onwards, Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
class mobile {

    /**
     * Returns the bigbluebuttonbn course view for the mobile app.
     *
     * @param mixed $args
     * @return array HTML, javascript and other data.
     */
    public static function mobile_course_view($args): array {
        global $OUTPUT;

        $args = (object) $args;
        $versionname = $args->appversioncode >= 3950 ? 'latest' : 'ionic3';

        $instance = instance::get_from_cmid($args->cmid);
        if (!$instance) {
            return self::mobile_print_error(get_string('view_error_url_missing_parameters', 'bigbluebuttonbn'));
        }

        $cm = $instance->get_cm();
        $course = $instance->get_course();

        // Check activity status.
        if ($instance->before_start_time()) {
            $message = get_string('view_message_conference_not_started', 'bigbluebuttonbn');

            $notstarted = [
                'starts_at' => '',
                'ends_at' => '',
            ];
            if (!empty($instance->get_instance_var('openingtime'))) {
                $notstarted['starts_at'] = sprintf(
                    '%s: %s',
                    get_string('mod_form_field_openingtime', 'bigbluebuttonbn'),
                    userdate($instance->get_instance_var('openingtime'))
                );
            }

            if (!empty($instance->get_instance_var('closingtime'))) {
                $notstarted['ends_at'] = sprintf(
                    '%s: %s',
                    get_string('mod_form_field_closingtime', 'bigbluebuttonbn'),
                    userdate($instance->get_instance_var('closingtime'))
                );
            }

            return self::mobile_print_notification($instance, $message, $notstarted);
        }

        if ($instance->has_ended()) {
            $message = get_string('view_message_conference_has_ended', 'bigbluebuttonbn');
            return self::mobile_print_notification($instance, $message);
        }

        // Check if the BBB server is working.
        $serverversion = bigbluebutton_proxy::get_server_version();
        if ($serverversion === null) {
            return self::mobile_print_error(bigbluebutton_proxy::get_server_not_available_message($instance));
        }

        // Mark viewed by user (if required).
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm);

        // Validate if the user is in a role allowed to join.
        if (!$instance->can_join()) {
            return self::mobile_print_error(get_string('view_nojoin', 'bigbluebuttonbn'));
        }

        // Note: This logic should match bbb_view.php.

        // Logic of bbb_view for join to session.
        if ($instance->user_must_wait_to_join()) {
            // If user is not administrator nor moderator (user is student) and waiting is required.
            return self::mobile_print_notification(
                $instance,
                get_string('view_message_conference_wait_for_moderator', 'bigbluebuttonbn')
            );
        }

        // See if the BBB session is already in progress.
        $urltojoin = '';
        try {
            $urltojoin = meeting::join_meeting($instance);
        } catch (meeting_join_exception $e) {
            return self::mobile_print_notification($instance, $e->getMessage());
        } catch (server_not_available_exception $e) {
            return self::mobile_print_error(bigbluebutton_proxy::get_server_not_available_message($instance));
        }

        // Check groups access and show message.
        $msjgroup = [];
        $groupmode = groups_get_activity_groupmode($instance->get_cm());
        if ($groupmode != NOGROUPS) {
            $msjgroup['message'] = get_string('view_mobile_message_groups_not_supported', 'bigbluebuttonbn');
        }

        $data = [
            'bigbluebuttonbn' => $instance->get_instance_data(),
            'msjgroup' => $msjgroup,
            'urltojoin' => $urltojoin,
            'cmid' => $cm->id,
            'courseid' => $course->id,
        ];

        // We want to show a notification when user excedded 45 seconds without click button.
        $jstimecreatedmeeting = 'setTimeout(function(){
        document.getElementById("bigbluebuttonbn-mobile-notifications").style.display = "block";
        document.getElementById("bigbluebuttonbn-mobile-join").disabled = true;
        document.getElementById("bigbluebuttonbn-mobile-meetingready").style.display = "none";
        }, 45000);';

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template("mod_bigbluebuttonbn/mobile_view_page_$versionname", $data),
                ],
            ],
            'javascript' => $jstimecreatedmeeting,
            'otherdata' => '',
            'files' => '',
        ];
    }

    /**
     * Returns the view for errors.
     *
     * @param string $error Error to display.
     * @return array HTML, javascript and otherdata
     */
    protected static function mobile_print_error($error): array {
        global $OUTPUT;

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('mod_bigbluebuttonbn/mobile_view_error', [
                        'error' => $error,
                    ]),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
            'files' => '',
        ];
    }

    /**
     * Returns the view for messages.
     *
     * @param instance $instance
     * @param string $message Message to display.
     * @param array $notstarted Extra messages for not started session.
     * @return array HTML, javascript and otherdata
     */
    protected static function mobile_print_notification(instance $instance, $message, $notstarted = []): array {
        global $OUTPUT, $CFG;

        $data = [
            'bigbluebuttonbn' => $instance->get_instance_data(),
            'cmid' => $instance->get_cm_id(),
            'message' => $message,
            'not_started' => $notstarted,
        ];

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('mod_bigbluebuttonbn/mobile_view_notification', $data),
                ],
            ],
            'javascript' => file_get_contents($CFG->dirroot . '/mod/bigbluebuttonbn/mobileapp/mobile.notification.js'),
            'otherdata' => '',
            'files' => ''
        ];
    }
}
