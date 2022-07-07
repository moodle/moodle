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
 * Contains a helper class providing util methods for testing.
 *
 * @package    core_message
 * @copyright  2018 Jake Dallimore <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\tests;

defined('MOODLE_INTERNAL') || die();

/**
 * The helper class providing util methods for testing.
 *
 * @copyright  2018 Jake Dallimore <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Sends a message to a conversation.
     *
     * @param \stdClass $userfrom user object of the one sending the message.
     * @param int $convid id of the conversation in which we'll send the message.
     * @param string $message message to send.
     * @param int $time the time the message was sent.
     * @return int the id of the message which was sent.
     * @throws \dml_exception if the conversation doesn't exist.
     */
    public static function send_fake_message_to_conversation(\stdClass $userfrom, int $convid, string $message = 'Hello world!',
            int $time = null) : int {
        global $DB;
        $conversationrec = $DB->get_record('message_conversations', ['id' => $convid], 'id', MUST_EXIST);
        $conversationid = $conversationrec->id;
        $time = $time ?? time();
        $record = new \stdClass();
        $record->useridfrom = $userfrom->id;
        $record->conversationid = $conversationid;
        $record->subject = 'No subject';
        $record->fullmessage = $message;
        $record->smallmessage = $message;
        $record->timecreated = $time;
        return $DB->insert_record('messages', $record);
    }
}
