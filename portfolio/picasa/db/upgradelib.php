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
 * Picasa portfolio upgrade script.
 *
 * @package   portfolio_picasa
 * @copyright 2013 Dan Poltawski <dan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Inform admins about setup required for picasa change.
 */
function portfolio_picasa_admin_upgrade_notification() {
    $admins = get_admins();

    if (empty($admins)) {
        return;
    }
    $a = new stdClass;
    $a->docsurl = get_docs_url('Google_OAuth_2.0_setup');

    foreach ($admins as $admin) {
        $message = new stdClass();
        $message->component         = 'moodle';
        $message->name              = 'notices';
        $message->userfrom          = get_admin();
        $message->userto            = $admin;
        $message->smallmessage      = get_string('oauth2upgrade_message_small', 'portfolio_picasa');
        $message->subject           = get_string('oauth2upgrade_message_subject', 'portfolio_picasa');
        $message->fullmessage       = get_string('oauth2upgrade_message_content', 'portfolio_picasa', $a);
        $message->fullmessagehtml   = get_string('oauth2upgrade_message_content', 'portfolio_picasa', $a);
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->notification      = 1;
        message_send($message);
    }
}
