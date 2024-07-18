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

namespace core_message;

use core_message\tests\helper as testhelper;

/**
 * Test api's in message lib.
 *
 * @package core_message
 * @category test
 * @copyright 2014 Rajesh Taneja <rajesh@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class messagelib_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/message/lib.php');

        parent::setUpBeforeClass();
    }

    /**
     * Test message_search_users.
     */
    public function test_message_search_users(): void {
        global $USER;

        $this->resetAfterTest();

        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user1 = $this->getDataGenerator()->create_user(['firstname' => 'Test1', 'lastname' => 'user1']);
        $user2 = $this->getDataGenerator()->create_user(['firstname' => 'Test2', 'lastname' => 'user2']);

        // Add users to the admin's contact list.
        api::add_contact($USER->id, $user1->id);
        api::add_contact($USER->id, $user2->id);

        $this->assertCount(1, message_search_users(0, 'Test1'));
        $this->assertCount(2, message_search_users(0, 'Test'));
        $this->assertCount(1, message_search_users(0, 'user1'));
        $this->assertCount(2, message_search_users(0, 'user'));
    }

    /**
     * Test message_get_messages.
     */
    public function test_message_get_messages(): void {
        global $DB;

        $this->resetAfterTest();

        // Set this user as the admin.
        $this->setAdminUser();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        api::add_contact($user1->id, $user2->id);
        api::add_contact($user1->id, $user3->id);

        // Create some individual conversations.
        $ic1 = api::create_conversation(
            api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user2->id]
        );
        $ic2 = api::create_conversation(
            api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user3->id]
        );

        // Send some messages to individual conversations.
        $im1 = testhelper::send_fake_message_to_conversation($user1, $ic1->id, 'Message 1');
        $im2 = testhelper::send_fake_message_to_conversation($user2, $ic1->id, 'Message 2');
        $im3 = testhelper::send_fake_message_to_conversation($user1, $ic1->id, 'Message 3');
        $im4 = testhelper::send_fake_message_to_conversation($user1, $ic2->id, 'Message 4');

        // Mark a message as read by user2.
        $message = $DB->get_record('messages', ['id' => $im1]);
        api::mark_message_as_read($user2->id, $message);

        // Retrieve unread messages sent from user1 to user2.
        $lastmessages = message_get_messages($user2->id, $user1->id, 0, MESSAGE_GET_UNREAD);
        $this->assertCount(1, $lastmessages);
        $this->assertArrayHasKey($im3, $lastmessages);

        // Get only read messages.
        $lastmessages = message_get_messages($user2->id, $user1->id, 0, MESSAGE_GET_READ);
        $this->assertCount(1, $lastmessages);
        $this->assertArrayHasKey($im1, $lastmessages);

        // Get both read and unread.
        $lastmessages = message_get_messages($user2->id, $user1->id, 0, MESSAGE_GET_READ_AND_UNREAD);
        $this->assertCount(2, $lastmessages);
        $this->assertArrayHasKey($im1, $lastmessages);
        $this->assertArrayHasKey($im3, $lastmessages);

        // Repeat retrieve read/unread messages but using a bool to test backwards compatibility.
        $lastmessages = message_get_messages($user2->id, $user1->id, 0, false);
        $this->assertCount(1, $lastmessages);
        $this->assertArrayHasKey($im3, $lastmessages);

        $lastmessages = message_get_messages($user2->id, $user1->id, 0, true);
        $this->assertCount(1, $lastmessages);
        $this->assertArrayHasKey($im1, $lastmessages);

        // Create some group conversations.
        $gc1 = api::create_conversation(
            api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id],
            'Group chat'
        );

        // Send some messages to group conversations.
        $gm1 = testhelper::send_fake_message_to_conversation($user1, $gc1->id, 'Group message 1');

        // Retrieve all messages sent from user1 to user2 (the result should be the same as before, because only individual
        // conversations should be considered by the message_get_messages function).
        $lastmessages = message_get_messages($user2->id, $user1->id, 0, MESSAGE_GET_READ_AND_UNREAD);
        $this->assertCount(2, $lastmessages);
        $this->assertArrayHasKey($im1, $lastmessages);
        $this->assertArrayHasKey($im3, $lastmessages);
    }

    /**
     * Test message_get_messages with only group conversations between users.
     */
    public function test_message_get_messages_only_group_conversations(): void {
        $this->resetAfterTest();

        // Set this user as the admin.
        $this->setAdminUser();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // Create some group conversations.
        $gc1 = api::create_conversation(
            api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id],
            'Group chat'
        );

        // Send some messages to group conversations.
        $gm1 = testhelper::send_fake_message_to_conversation($user1, $gc1->id, 'Group message 1');
        $gm2 = testhelper::send_fake_message_to_conversation($user2, $gc1->id, 'Group message 2');

        // Retrieve all messages sent from user1 to user2. There shouldn't be messages, because only individual
        // conversations should be considered by the message_get_messages function.
        $lastmessages = message_get_messages($user2->id, $user1->id, 0, MESSAGE_GET_READ_AND_UNREAD);
        $this->assertCount(0, $lastmessages);
    }
}
