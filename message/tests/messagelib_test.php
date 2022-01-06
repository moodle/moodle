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
 * Test api's in message lib.
 *
 * @package core_message
 * @category test
 * @copyright 2014 Rajesh Taneja <rajesh@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/message/lib.php');

use \core_message\tests\helper as testhelper;

/**
 * Test api's in message lib.
 *
 * @package core_message
 * @category test
 * @copyright 2014 Rajesh Taneja <rajesh@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_message_messagelib_testcase extends advanced_testcase {

    /** @var phpunit_message_sink keep track of messages. */
    protected $messagesink = null;

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp(): void {
        $this->preventResetByRollback(); // Messaging is not compatible with transactions.
        $this->messagesink = $this->redirectMessages();
        $this->resetAfterTest();
    }

    /**
     * Send a fake message.
     *
     * {@link message_send()} does not support transaction, this function will simulate a message
     * sent from a user to another. We should stop using it once {@link message_send()} will support
     * transactions. This is not clean at all, this is just used to add rows to the table.
     *
     * @param stdClass $userfrom user object of the one sending the message.
     * @param stdClass $userto user object of the one receiving the message.
     * @param string $message message to send.
     * @param int $notification if the message is a notification.
     * @param int $time the time the message was sent
     * @return int the id of the message
     */
    protected function send_fake_message($userfrom, $userto, $message = 'Hello world!', $notification = 0, $time = 0) {
        global $DB;

        if (empty($time)) {
            $time = time();
        }

        if ($notification) {
            $record = new stdClass();
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
                    [$userfrom->id]
                );
            }
            $conversationid = $conversation->id;
        } else if (!$conversationid = \core_message\api::get_conversation_between_users([$userfrom->id, $userto->id])) {
            // It's an individual conversation between two different users.
            $conversation = \core_message\api::create_conversation(
                \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
                [
                    $userfrom->id,
                    $userto->id
                ]
            );
            $conversationid = $conversation->id;
        }

        // Ok, send the message.
        $record = new stdClass();
        $record->useridfrom = $userfrom->id;
        $record->conversationid = $conversationid;
        $record->subject = 'No subject';
        $record->fullmessage = $message;
        $record->smallmessage = $message;
        $record->timecreated = $time;

        return $DB->insert_record('messages', $record);
    }

    /**
     * Test message_get_blocked_users throws an exception because has been removed.
     */
    public function test_message_get_blocked_users() {
        $this->expectException('coding_exception');
        $this->expectExceptionMessage(
            'message_get_blocked_users() has been removed, please use \core_message\api::get_blocked_users() instead.'
        );
        message_get_blocked_users();
    }

    /**
     * Test message_get_contacts throws an exception because has been removed.
     */
    public function test_message_get_contacts() {
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('message_get_contacts() has been removed.');
        message_get_contacts();
    }

    /**
     * Test message_count_unread_messages.
     * TODO: MDL-69643
     */
    public function test_message_count_unread_messages() {
        // Create users to send and receive message.
        $userfrom1 = $this->getDataGenerator()->create_user();
        $userfrom2 = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();

        $this->assertEquals(0, message_count_unread_messages($userto));
        $this->assertDebuggingCalled();

        // Send fake messages.
        $this->send_fake_message($userfrom1, $userto);
        $this->send_fake_message($userfrom2, $userto);

        $this->assertEquals(2, message_count_unread_messages($userto));
        $this->assertDebuggingCalled();

        $this->assertEquals(1, message_count_unread_messages($userto, $userfrom1));
        $this->assertDebuggingCalled();
    }

    /**
     * Test message_count_unread_messages with read messages.
     */
    public function test_message_count_unread_messages_with_read_messages() {
        global $DB;

        // Create users to send and receive messages.
        $userfrom1 = $this->getDataGenerator()->create_user();
        $userfrom2 = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();

        $this->assertEquals(0, message_count_unread_messages($userto));

        // Send fake messages.
        $messageid = $this->send_fake_message($userfrom1, $userto);
        $this->send_fake_message($userfrom2, $userto);

        // Mark message as read.
        $message = $DB->get_record('messages', ['id' => $messageid]);
        \core_message\api::mark_message_as_read($userto->id, $message);

        // Should only count the messages that weren't read by the current user.
        $this->assertEquals(1, message_count_unread_messages($userto));
        $this->assertDebuggingCalledCount(2);

        $this->assertEquals(0, message_count_unread_messages($userto, $userfrom1));
        $this->assertDebuggingCalled();
    }

    /**
     * Test message_count_unread_messages with deleted messages.
     */
    public function test_message_count_unread_messages_with_deleted_messages() {
        global $DB;

        // Create users to send and receive messages.
        $userfrom1 = $this->getDataGenerator()->create_user();
        $userfrom2 = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();

        $this->assertEquals(0, message_count_unread_messages($userto));
        $this->assertDebuggingCalled();

        // Send fake messages.
        $messageid = $this->send_fake_message($userfrom1, $userto);
        $this->send_fake_message($userfrom2, $userto);

        // Delete a message.
        \core_message\api::delete_message($userto->id, $messageid);

        // Should only count the messages that weren't deleted by the current user.
        $this->assertEquals(1, message_count_unread_messages($userto));
        $this->assertDebuggingCalled();
        $this->assertEquals(0, message_count_unread_messages($userto, $userfrom1));
        $this->assertDebuggingCalled();
    }

    /**
     * Test message_count_unread_messages with sent messages.
     */
    public function test_message_count_unread_messages_with_sent_messages() {
        $userfrom = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();

        $this->send_fake_message($userfrom, $userto);

        // Ensure an exception is thrown.
        $this->assertEquals(0, message_count_unread_messages($userfrom));
        $this->assertDebuggingCalled();
    }

    /**
     * Test message_search_users.
     */
    public function test_message_search_users() {
        global $USER;

        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user1 = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'user2'));

        // Add users to the admin's contact list.
        \core_message\api::add_contact($USER->id, $user1->id);
        \core_message\api::add_contact($USER->id, $user2->id);

        $this->assertCount(1, message_search_users(0, 'Test1'));
        $this->assertCount(2, message_search_users(0, 'Test'));
        $this->assertCount(1, message_search_users(0, 'user1'));
        $this->assertCount(2, message_search_users(0, 'user'));
    }

    /**
     * Test message_get_messages.
     */
    public function test_message_get_messages() {
        $this->resetAfterTest(true);

        // Set this user as the admin.
        $this->setAdminUser();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user1->id, $user3->id);

        // Create some individual conversations.
        $ic1 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user2->id]);
        $ic2 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user3->id]);

        // Send some messages to individual conversations.
        $im1 = testhelper::send_fake_message_to_conversation($user1, $ic1->id, 'Message 1');
        $im2 = testhelper::send_fake_message_to_conversation($user2, $ic1->id, 'Message 2');
        $im3 = testhelper::send_fake_message_to_conversation($user1, $ic1->id, 'Message 3');
        $im4 = testhelper::send_fake_message_to_conversation($user1, $ic2->id, 'Message 4');

        // Retrieve all messages sent from user1 to user2.
        $lastmessages = message_get_messages($user2->id, $user1->id, 0, false);
        $this->assertCount(2, $lastmessages);
        $this->assertArrayHasKey($im1, $lastmessages);
        $this->assertArrayHasKey($im3, $lastmessages);

        // Create some group conversations.
        $gc1 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id], 'Group chat');

        // Send some messages to group conversations.
        $gm1 = testhelper::send_fake_message_to_conversation($user1, $gc1->id, 'Group message 1');

        // Retrieve all messages sent from user1 to user2 (the result should be the same as before, because only individual
        // conversations should be considered by the message_get_messages function).
        $lastmessages = message_get_messages($user2->id, $user1->id, 0, false);
        $this->assertCount(2, $lastmessages);
        $this->assertArrayHasKey($im1, $lastmessages);
        $this->assertArrayHasKey($im3, $lastmessages);
    }

    /**
     * Test message_get_messages with only group conversations between users.
     */
    public function test_message_get_messages_only_group_conversations() {
        $this->resetAfterTest(true);

        // Set this user as the admin.
        $this->setAdminUser();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // Create some group conversations.
        $gc1 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id], 'Group chat');

        // Send some messages to group conversations.
        $gm1 = testhelper::send_fake_message_to_conversation($user1, $gc1->id, 'Group message 1');
        $gm2 = testhelper::send_fake_message_to_conversation($user2, $gc1->id, 'Group message 2');

        // Retrieve all messages sent from user1 to user2. There shouldn't be messages, because only individual
        // conversations should be considered by the message_get_messages function.
        $lastmessages = message_get_messages($user2->id, $user1->id, 0, false);
        $this->assertCount(0, $lastmessages);
    }

}
