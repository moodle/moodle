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
 * Assignment upgrade script.
 *
 * @package   mod_assignment
 * @copyright 2013 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Inform admins about assignments that still need upgrading.
 */
function mod_assignment_pending_upgrades_notification($count) {
    $admins = get_admins();

    if (empty($admins)) {
        return;
    }

    $a = new stdClass;
    $a->count = $count;
    $a->docsurl = get_docs_url('Assignment_upgrade_tool');
    foreach ($admins as $admin) {
        $message = new stdClass();
        $message->component         = 'moodle';
        $message->name              = 'notices';
        $message->userfrom          = \core_user::get_noreply_user();
        $message->userto            = $admin;
        $message->smallmessage      = get_string('pendingupgrades_message_small', 'mod_assignment');
        $message->subject           = get_string('pendingupgrades_message_subject', 'mod_assignment');
        $message->fullmessage       = get_string('pendingupgrades_message_content', 'mod_assignment', $a);
        $message->fullmessagehtml   = get_string('pendingupgrades_message_content', 'mod_assignment', $a);
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->notification      = 1;
        message_send($message);
    }
}
