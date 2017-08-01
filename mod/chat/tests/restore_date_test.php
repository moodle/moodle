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
 * Restore date tests.
 *
 * @package    mod_chat
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . "/phpunit/classes/restore_date_testcase.php");

/**
 * Restore date tests.
 *
 * @package    mod_chat
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_chat_restore_date_testcase extends restore_date_testcase {

    public function test_restore_dates() {
        global $DB;

        list($course, $chat) = $this->create_course_and_module('chat');
        $result = mod_chat_external::login_user($chat->id);
        $result = external_api::clean_returnvalue(mod_chat_external::login_user_returns(), $result);
        $chatsid = $result['chatsid'];

        $result = mod_chat_external::send_chat_message($chatsid, 'hello!');
        $result = external_api::clean_returnvalue(mod_chat_external::send_chat_message_returns(), $result);
        $message = $DB->get_record('chat_messages', ['id' => $result['messageid']]);
        $timestamp = 1000;
        $DB->set_field('chat_messages', 'timestamp', $timestamp);

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newchat = $DB->get_record('chat', ['course' => $newcourseid]);

        $this->assertFieldsNotRolledForward($chat, $newchat, ['timemodified']);
        $props = ['chattime'];
        $this->assertFieldsRolledForward($chat, $newchat, $props);

        $newmessages = $DB->get_records('chat_messages', ['chatid' => $newchat->id]);

        foreach ($newmessages as $message) {
            $this->assertEquals($timestamp, $message->timestamp);
        }

    }
}
