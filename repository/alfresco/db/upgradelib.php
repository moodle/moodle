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
 * Locallib.
 *
 * @package    repository_alfresco
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Send a message to the admin in regard with the APIv1 migration.
 *
 * @return void
 */
function repository_alfresco_admin_security_key_notice() {
    $admins = get_admins();

    if (empty($admins)) {
        return;
    }

    foreach ($admins as $admin) {
        $message = new stdClass();
        $message->component         = 'moodle';
        $message->name              = 'notices';
        $message->userfrom          = get_admin();
        $message->userto            = $admin;
        $message->smallmessage      = get_string('security_key_notice_message_small', 'repository_alfresco');
        $message->subject           = get_string('security_key_notice_message_subject', 'repository_alfresco');
        $message->fullmessage       = get_string('security_key_notice_message_content', 'repository_alfresco');
        $message->fullmessagehtml   = get_string('security_key_notice_message_content', 'repository_alfresco');
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->notification      = 1;
        message_send($message);
    }
}
