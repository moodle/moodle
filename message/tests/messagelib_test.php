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
    public function setUp() {
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

        if (!$conversationid = \core_message\api::get_conversation_between_users([$userfrom->id, $userto->id])) {
            $conversationid = \core_message\api::create_conversation_between_users([$userfrom->id,
                $userto->id]);
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
     * Test message_get_blocked_users.
     */
    public function test_message_get_blocked_users() {
        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Add users to the admin's contact list.
        message_add_contact($user1->id);
        message_add_contact($user2->id, 1);

        $this->assertCount(1, message_get_blocked_users());
        $this->assertDebuggingCalled();

        // Block other user.
        message_block_contact($user1->id);
        $this->assertCount(2, message_get_blocked_users());
        $this->assertDebuggingCalled();

        // Test deleting users.
        delete_user($user1);
        $this->assertCount(1, message_get_blocked_users());
        $this->assertDebuggingCalled();
    }

    /**
     * Test message_get_contacts.
     */
    public function test_message_get_contacts() {
        global $USER, $CFG;

        // Set this user as the admin.
        $this->setAdminUser();

        $noreplyuser = core_user::get_noreply_user();
        $supportuser = core_user::get_support_user();

        // Create a user to add to the admin's contact list.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user(); // Stranger.

        // Add users to the admin's contact list.
        message_add_contact($user1->id);
        message_add_contact($user2->id);

        // Send some messages.
        $this->send_fake_message($user1, $USER);
        $this->send_fake_message($user2, $USER);
        $this->send_fake_message($user3, $USER);

        list($onlinecontacts, $offlinecontacts, $strangers) = message_get_contacts();
        $this->assertDebuggingCalled();
        $this->assertCount(0, $onlinecontacts);
        $this->assertCount(2, $offlinecontacts);
        $this->assertCount(1, $strangers);

        // Send message from noreply and support users.
        $this->send_fake_message($noreplyuser, $USER);
        $this->send_fake_message($supportuser, $USER);
        list($onlinecontacts, $offlinecontacts, $strangers) = message_get_contacts();
        $this->assertDebuggingCalled();
        $this->assertCount(0, $onlinecontacts);
        $this->assertCount(2, $offlinecontacts);
        $this->assertCount(3, $strangers);

        // Block 1 user.
        message_block_contact($user2->id);
        list($onlinecontacts, $offlinecontacts, $strangers) = message_get_contacts();
        $this->assertDebuggingCalled();
        $this->assertCount(0, $onlinecontacts);
        $this->assertCount(1, $offlinecontacts);
        $this->assertCount(3, $strangers);

        // Noreply user being valid user.
        core_user::reset_internal_users();
        $CFG->noreplyuserid = $user3->id;
        list($onlinecontacts, $offlinecontacts, $strangers) = message_get_contacts();
        $this->assertDebuggingCalled();
        $this->assertCount(0, $onlinecontacts);
        $this->assertCount(1, $offlinecontacts);
        $this->assertCount(2, $strangers);

        // Test deleting users.
        delete_user($user1);
        delete_user($user3);
        core_user::reset_internal_users();
        list($onlinecontacts, $offlinecontacts, $strangers) = message_get_contacts();
        $this->assertDebuggingCalled();
        $this->assertCount(0, $onlinecontacts);
        $this->assertCount(0, $offlinecontacts);
        $this->assertCount(1, $strangers);
    }

    /**
     * Test message_count_unread_messages.
     */
    public function test_message_count_unread_messages() {
        // Create users to send and receive message.
        $userfrom1 = $this->getDataGenerator()->create_user();
        $userfrom2 = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();

        $this->assertEquals(0, message_count_unread_messages($userto));

        // Send fake messages.
        $this->send_fake_message($userfrom1, $userto);
        $this->send_fake_message($userfrom2, $userto);

        $this->assertEquals(2, message_count_unread_messages($userto));
        $this->assertEquals(1, message_count_unread_messages($userto, $userfrom1));
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
        $this->assertEquals(0, message_count_unread_messages($userto, $userfrom1));
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

        // Send fake messages.
        $messageid = $this->send_fake_message($userfrom1, $userto);
        $this->send_fake_message($userfrom2, $userto);

        // Delete a message.
        \core_message\api::delete_message($userto->id, $messageid);

        // Should only count the messages that weren't deleted by the current user.
        $this->assertEquals(1, message_count_unread_messages($userto));
        $this->assertEquals(0, message_count_unread_messages($userto, $userfrom1));
    }

    /**
     * Test message_count_unread_messages with sent messages.
     */
    public function test_message_count_unread_messages_with_sent_messages() {
        $userfrom = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();

        $this->send_fake_message($userfrom, $userto);

        $this->assertEquals(0, message_count_unread_messages($userfrom));
    }

    /**
     * Test message_add_contact.
     */
    public function test_message_add_contact() {
        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        message_add_contact($user1->id);
        message_add_contact($user2->id, 0);
        // Add duplicate contact and make sure only 1 record exists.
        message_add_contact($user2->id, 1);

        $this->assertNotEmpty(message_get_contact($user1->id));
        $this->assertNotEmpty(message_get_contact($user2->id));
        $this->assertEquals(false, message_get_contact($user3->id));
        $this->assertEquals(1, \core_message\api::count_blocked_users());
    }

    /**
     * Test message_remove_contact.
     */
    public function test_message_remove_contact() {
        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user = $this->getDataGenerator()->create_user();

        // Add the user to the admin's contact list.
        message_add_contact($user->id);
        $this->assertNotEmpty(message_get_contact($user->id));

        // Remove user from admin's contact list.
        message_remove_contact($user->id);
        $this->assertEquals(false, message_get_contact($user->id));
    }

    /**
     * Test message_block_contact.
     */
    public function test_message_block_contact() {
        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Add users to the admin's contact list.
        message_add_contact($user1->id);
        message_add_contact($user2->id);

        $this->assertEquals(0, \core_message\api::count_blocked_users());

        // Block 1 user.
        message_block_contact($user2->id);
        $this->assertEquals(1, \core_message\api::count_blocked_users());

    }

    /**
     * Test message_unblock_contact.
     */
    public function test_message_unblock_contact() {
        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Add users to the admin's contact list.
        message_add_contact($user1->id);
        message_add_contact($user2->id, 1); // Add blocked contact.

        $this->assertEquals(1, \core_message\api::count_blocked_users());

        // Unblock user.
        message_unblock_contact($user2->id);
        $this->assertEquals(0, \core_message\api::count_blocked_users());
    }

    /**
     * Test message_search_users.
     */
    public function test_message_search_users() {
        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user1 = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'user2'));

        // Add users to the admin's contact list.
        message_add_contact($user1->id);
        message_add_contact($user2->id); // Add blocked contact.

        $this->assertCount(1, message_search_users(0, 'Test1'));
        $this->assertCount(2, message_search_users(0, 'Test'));
        $this->assertCount(1, message_search_users(0, 'user1'));
        $this->assertCount(2, message_search_users(0, 'user'));
    }
}
