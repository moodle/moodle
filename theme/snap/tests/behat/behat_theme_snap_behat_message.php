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
 * Overrides for behat messaging.
 * @author    Guy Thomas
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../message/tests/behat/behat_message.php');

/**
 * Overrides for behat messaging.
 * @author    Guy Thomas
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_snap_behat_message extends behat_message {

    public function i_send_message_to_user($messagecontent, $userfullname) {
        global $DB;

        $fromuser = $this->get_session_user();

        $fullnamesql = $DB->sql_concat('firstname', "' '", 'lastname');
        $sqlselect = $fullnamesql . ' = ?';
        $touser = $DB->get_record_select('user', $sqlselect, [$userfullname]);
        $smallmessage = shorten_text($messagecontent, 30);
        $subject = get_string_manager()->get_string('unreadnewmessage', 'message', fullname($fromuser), $touser->lang);
        $message = new \core\message\message();
        $message->courseid = SITEID;
        $message->userfrom = $fromuser->id;
        $message->userto = $touser->id;
        $message->subject = $subject;
        $message->smallmessage = $smallmessage;
        $message->fullmessage = $messagecontent;
        $message->fullmessageformat = 0;
        $message->fullmessagehtml = null;
        $message->notification = 0;
        $message->component = 'moodle';
        $message->name = "instantmessage";

        message_send($message);
    }
}
