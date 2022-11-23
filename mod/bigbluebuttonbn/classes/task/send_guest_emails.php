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
namespace mod_bigbluebuttonbn\task;
use core_user;
use html_writer;

/**
 * This adhoc task will send emails to guest users with the meeting's details
 *
 * @package   mod_bigbluebuttonbn
 * @copyright  2022 onwards, Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Laurent David  (laurent [at] call-learning [dt] fr)
 */
class send_guest_emails extends send_notification {

    /**
     * Get the notification type.
     *
     * @return string
     */
    protected function get_notification_type(): string {
        return 'guest_invited';
    }

    /**
     * Send all the emails
     */
    protected function send_all_notifications(): void {
        $customdata = $this->get_custom_data();
        if (!empty($customdata->emails)) {
            foreach ($customdata->emails as $email) {
                $user = core_user::get_noreply_user();
                $user->email = $email;
                $user->mailformat = 1; // HTML format.

                email_to_user(
                        $user,
                        core_user::get_noreply_user(),
                        $this->get_subject(),
                        $this->get_small_message(),
                        $this->get_html_message()
                );
            }
        }

    }

    /**
     * Get the subject of the notification.
     *
     * @return string
     */
    protected function get_subject(): string {
        return get_string('guest_invitation_subject', 'mod_bigbluebuttonbn', $this->get_string_vars());
    }

    /**
     * Get variables to make available to strings.
     *
     * @return array
     */
    protected function get_string_vars(): array {
        $customdata = $this->get_custom_data();
        $sender = core_user::get_user($customdata->useridfrom);
        return [
                'course_fullname' => $this->get_instance()->get_course()->fullname,
                'course_shortname' => $this->get_instance()->get_course()->shortname,
                'name' => $this->get_instance()->get_cm()->name,
                'guestjoinurl' => $this->get_instance()->get_guest_access_url()->out(false),
                'guestpassword' => $this->get_instance()->get_guest_access_password(),
                'sender' => fullname($sender)
        ];
    }

    /**
     * Get the short summary message.
     *
     * @return string
     */
    protected function get_small_message(): string {
        return get_string('guest_invitation_small_message', 'mod_bigbluebuttonbn', $this->get_string_vars());
    }

    /**
     * Get the HTML message content.
     *
     * @return string
     */
    protected function get_html_message(): string {
        return html_writer::tag(
                'p',
                get_string('guest_invitation_full_message', 'mod_bigbluebuttonbn', $this->get_string_vars())
        );
    }
}
