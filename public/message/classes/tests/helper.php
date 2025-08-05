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

namespace core_message\tests;

use core\{clock, di};
use stdClass;

/**
 * The helper class providing util methods for testing.
 *
 * @package    core_message
 * @copyright  2018 Jake Dallimore <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Send a fake message.
     *
     * {@see message_send()} does not support transaction, this function will simulate a message
     * sent from a user to another. We should stop using it once {@see message_send()} will support
     * transactions. This is not clean at all, this is just used to add rows to the table.
     *
     * @param \stdClass $userfrom user object of the one sending the message.
     * @param \stdClass $userto user object of the one receiving the message.
     * @param string $message message to send.
     * @param int $notification if the message is a notification.
     * @param int $time the time the message was sent
     * @return int the id of the message
     */
    public static function send_fake_message(
        stdClass $userfrom,
        stdClass $userto,
        string $message = 'Hello world!',
        int $notification = 0,
        int $time = 0,
    ): int {
        global $DB;

        if (empty($time)) {
            $time = di::get(clock::class)->time();
        }

        if ($notification) {
            $record = new \stdClass();
            $record->useridfrom = $userfrom->id;
            $record->useridto = $userto->id;
            $record->subject = 'No subject';
            $record->fullmessage = $message;
            $record->smallmessage = $message;
            $record->timecreated = $time;

            return $DB->insert_record('notifications', $record);
        }

        if ($userfrom->id == $userto->id) {
            // It's a self conversation.
            $conversation = \core_message\api::get_self_conversation($userfrom->id);
            if (empty($conversation)) {
                $conversation = \core_message\api::create_conversation(
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF,
                    [$userfrom->id],
                );
            }
            $conversationid = $conversation->id;
        } else if (!$conversationid = \core_message\api::get_conversation_between_users([$userfrom->id, $userto->id])) {
            // It's an individual conversation between two different users.
            $conversation = \core_message\api::create_conversation(
                \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
                [
                    $userfrom->id,
                    $userto->id,
                ]
            );
            $conversationid = $conversation->id;
        }

        // Ok, send the message.
        $record = (object) [
            'useridfrom' => $userfrom->id,
            'conversationid' => $conversationid,
            'subject' => 'No subject',
            'fullmessage' => $message,
            'smallmessage' => $message,
            'timecreated' => $time,
        ];

        return $DB->insert_record('messages', $record);
    }

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
            ?int $time = null): int {
        global $DB;
        $conversationrec = $DB->get_record('message_conversations', ['id' => $convid], 'id', MUST_EXIST);
        $conversationid = $conversationrec->id;
        $time = $time ?? di::get(clock::class)->time();
        $record = new \stdClass();
        $record->useridfrom = $userfrom->id;
        $record->conversationid = $conversationid;
        $record->subject = 'No subject';
        $record->fullmessage = $message;
        $record->smallmessage = $message;
        $record->timecreated = $time;
        return $DB->insert_record('messages', $record);
    }

    /**
     * Send a fake unread notification.
     *
     * message_send() does not support transaction, this function will simulate a message
     * sent from a user to another. We should stop using it once message_send() will support
     * transactions. This is not clean at all, this is just used to add rows to the table.
     *
     * @param stdClass $userfrom user object of the one sending the message.
     * @param stdClass $userto user object of the one receiving the message.
     * @param string $message message to send.
     * @param int $timecreated time the message was created.
     * @return int the id of the message
     */
    public static function send_fake_unread_notification(\stdClass $userfrom, \stdClass $userto, string $message = 'Hello world!',
            int $timecreated = 0): int {
        global $DB;

        $record = new \stdClass();
        $record->useridfrom = $userfrom->id;
        $record->useridto = $userto->id;
        $record->notification = 1;
        $record->subject = 'No subject';
        $record->fullmessage = $message;
        $record->smallmessage = $message;
        $record->timecreated = $timecreated ?: di::get(clock::class)->time();
        $record->customdata  = json_encode(['datakey' => 'data']);

        return $DB->insert_record('notifications', $record);
    }

    /**
     * Send a fake read notification.
     *
     * message_send() does not support transaction, this function will simulate a message
     * sent from a user to another. We should stop using it once message_send() will support
     * transactions. This is not clean at all, this is just used to add rows to the table.
     *
     * @param stdClass $userfrom user object of the one sending the message.
     * @param stdClass $userto user object of the one receiving the message.
     * @param string $message message to send.
     * @param int $timecreated time the message was created.
     * @param int $timeread the the message was read
     * @return int the id of the message
     */
    public static function send_fake_read_notification(\stdClass $userfrom, \stdClass $userto, string $message = 'Hello world!',
                                                       int $timecreated = 0, int $timeread = 0): int {
        global $DB;

        $record = new \stdClass();
        $record->useridfrom = $userfrom->id;
        $record->useridto = $userto->id;
        $record->notification = 1;
        $record->subject = 'No subject';
        $record->fullmessage = $message;
        $record->smallmessage = $message;
        $record->timecreated = $timecreated ?: di::get(clock::class)->time();
        $record->timeread = $timeread ?: di::get(clock::class)->time();

        $record->id = $DB->insert_record('notifications', $record);

        // Mark it as read.
        \core_message\api::mark_notification_as_read($record);

        return $record->id;
    }
}
