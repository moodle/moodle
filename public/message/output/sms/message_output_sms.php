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

defined('MOODLE_INTERNAL') || die();

use core_sms\message_status;

require_once($CFG->dirroot . '/message/output/lib.php');
require_once($CFG->libdir . '/moodlelib.php');

/**
 * Message processor for SMS.
 *
 * @package    message_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_output_sms extends message_output {

    #[\Override]
    public function send_message($message): bool {
        $userdata = is_object($message->userto) ? $message->userto : get_complete_user_data('id', $message->userto);

        if (empty($userdata) || !$this->is_user_configured($userdata) || !$this->should_send_sms($message)) {
            return false;
        }

        $manager = \core\di::get(\core_sms\manager::class);
        $result = $manager->send(
            recipientnumber: $userdata->phone2,
            content: $message->fullmessagesms,
            component: $message->component,
            messagetype: $message->name,
            recipientuserid: $userdata->id,
        );

        if ($result->status === message_status::GATEWAY_QUEUED ||
            $result->status === message_status::GATEWAY_SENT
        ) {
            return true;
        }

        debugging($result->status->description());
        return false;
    }

    #[\Override]
    public function is_user_configured($user = null): bool {
        if (empty($user)) {
            return false;
        }

        // Skip any SMS if user doesn't have a mobile number.
        if (empty($user->phone2)) {
            mtrace('No mobile number found for userid: ' . $user->id);
            return false;
        }

        // Skip any messaging of suspended and deleted users.
        if (
            $user->auth === 'nologin' ||
            $user->suspended ||
            $user->deleted
        ) {
            mtrace('The user with userid: ' . $user->id . ' is either deleted or suspended. Can not send SMS.');
            return false;
        }

        return true;
    }

    /**
     * Check whether the SMS message data fulfills the requirements.
     *
     * @param stdclass $messagedata The message data
     * @return bool
     */
    public function should_send_sms(stdclass $messagedata): bool {
        // Don't send SMS if it's not a production site and the following config is set.
        if (!empty($CFG->nosmsever)) {
            mtrace('Can not send SMS while nosmsever is enabled.');
            return false;
        }
        // We don't have a fallback for SMS text. It has to be included.
        if (!isset($messagedata->fullmessagesms)) {
            mtrace('No SMS string found for the message');
            return false;
        }
        // Check support for SMS from the component.
        if (!core_message\helper::supports_sms_notifications($messagedata)) {
            return false;
        }

        return true;
    }

    #[\Override]
    public function load_data(&$preferences, $userid) {
        return;
    }

    #[\Override]
    public function config_form($preferences) {
        return;
    }

    #[\Override]
    public function process_form($form, &$preferences) {
        return;
    }

    #[\Override]
    public function get_default_messaging_settings() {
        return MESSAGE_DISALLOWED;
    }

    #[\Override]
    public function can_send_to_any_users() {
        return true;
    }
}
