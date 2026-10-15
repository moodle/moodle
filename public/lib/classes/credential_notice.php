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

namespace core;

/**
 * Tells someone that an API credential was created, by email and notification.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class credential_notice {
    /**
     * Send the notice to one user.
     *
     * @param \stdClass $user The recipient.
     * @param string $provider The core message provider for the notification.
     * @param string $subject The subject.
     * @param string $html The body, as HTML.
     * @param url $url The page to manage the credential on.
     * @param string $urlname The name of that page.
     * @return void
     */
    public static function send(
        \stdClass $user,
        string $provider,
        string $subject,
        string $html,
        url $url,
        string $urlname,
    ): void {
        $noreply = \core_user::get_noreply_user();
        $text = html_to_text($html);

        // Emailed directly rather than through messaging, so nobody can switch it off in the
        // messaging settings before creating a credential.
        email_to_user($user, $noreply, $subject, $text, $html);

        // The provider never emails, so the notification above is the only email sent.
        $message = new message\message();
        $message->courseid = SITEID;
        $message->component = 'moodle';
        $message->name = $provider;
        $message->userfrom = $noreply;
        $message->userto = $user;
        $message->notification = 1;
        $message->subject = $subject;
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = $html;
        $message->fullmessage = $text;
        $message->smallmessage = $subject;
        $message->contexturl = $url->out(false);
        $message->contexturlname = $urlname;
        message_send($message);
    }
}
