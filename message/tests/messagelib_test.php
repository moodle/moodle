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
     * @return int the id of the message
     */
    protected function send_fake_message($userfrom, $userto, $message = 'Hello world!') {
        global $DB;

        $record = new stdClass();
        $record->useridfrom = $userfrom->id;
        $record->useridto = $userto->id;
        $record->subject = 'No subject';
        $record->fullmessage = $message;
        $record->timecreated = time();

        return $DB->insert_record('message', $record);
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

        // Block other user.
        message_block_contact($user1->id);
        $this->assertCount(2, message_get_blocked_users());
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
        $this->assertCount(0, $onlinecontacts);
        $this->assertCount(2, $offlinecontacts);
        $this->assertCount(1, $strangers);

        // Send message from noreply and support users.
        $this->send_fake_message($noreplyuser, $USER);
        $this->send_fake_message($supportuser, $USER);
        list($onlinecontacts, $offlinecontacts, $strangers) = message_get_contacts();
        $this->assertCount(0, $onlinecontacts);
        $this->assertCount(2, $offlinecontacts);
        $this->assertCount(3, $strangers);

        // Block 1 user.
        message_block_contact($user2->id);
        list($onlinecontacts, $offlinecontacts, $strangers) = message_get_contacts();
        $this->assertCount(0, $onlinecontacts);
        $this->assertCount(1, $offlinecontacts);
        $this->assertCount(3, $strangers);

        // Noreply user being valid user.
        core_user::reset_internal_users();
        $CFG->noreplyuserid = $user3->id;
        $noreplyuser = core_user::get_noreply_user();
        list($onlinecontacts, $offlinecontacts, $strangers) = message_get_contacts();
        $this->assertCount(0, $onlinecontacts);
        $this->assertCount(1, $offlinecontacts);
        $this->assertCount(2, $strangers);
    }

    /**
     * Test message_count_messages.
     */
    public function test_message_count_messages() {
        global $DB;

        // Create users to send and receive message.
        $userfrom = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();

        message_post_message($userfrom, $userto, 'Message 1', FORMAT_PLAIN);
        message_post_message($userfrom, $userto, 'Message 2', FORMAT_PLAIN);
        message_post_message($userto, $userfrom, 'Message 3', FORMAT_PLAIN);

        // Return 0 when no message.
        $messages = array();
        $this->assertEquals(0, message_count_messages($messages, 'Test', 'Test'));

        // Check number of messages from userfrom and userto.
        $messages = $this->messagesink->get_messages();
        $this->assertEquals(2, message_count_messages($messages, 'useridfrom', $userfrom->id));
        $this->assertEquals(1, message_count_messages($messages, 'useridfrom', $userto->id));
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
     * Test message_count_blocked_users.
     *
     */
    public function test_message_count_blocked_users() {
        // Set this user as the admin.
        $this->setAdminUser();

        // Create users to add to the admin's contact list.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->assertEquals(0, message_count_blocked_users());

        // Add 1 blocked and 1 normal contact to admin's contact list.
        message_add_contact($user1->id);
        message_add_contact($user2->id, 1);

        $this->assertEquals(0, message_count_blocked_users($user2));
        $this->assertEquals(1, message_count_blocked_users());
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
        $this->assertEquals(1, message_count_blocked_users());
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

        $this->assertEquals(0, message_count_blocked_users());

        // Block 1 user.
        message_block_contact($user2->id);
        $this->assertEquals(1, message_count_blocked_users());

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

        $this->assertEquals(1, message_count_blocked_users());

        // Unblock user.
        message_unblock_contact($user2->id);
        $this->assertEquals(0, message_count_blocked_users());
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

    /**
     * Test message_search.
     */
    public function test_message_search() {
        global $USER;

        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user1 = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'user2'));

        // Send few messages, real (read).
        message_post_message($user1, $USER, 'Message 1', FORMAT_PLAIN);
        message_post_message($USER, $user1, 'Message 2', FORMAT_PLAIN);
        message_post_message($USER, $user2, 'Message 3', FORMAT_PLAIN);

        $this->assertCount(2, message_search(array('Message'), true, false));
        $this->assertCount(3, message_search(array('Message'), true, true));

        // Send fake message (not-read).
        $this->send_fake_message($USER, $user1, 'Message 4');
        $this->send_fake_message($user1, $USER, 'Message 5');
        $this->assertCount(3, message_search(array('Message'), true, false));
        $this->assertCount(5, message_search(array('Message'), true, true));

        // If courseid given then should be 0.
        $this->assertEquals(false, message_search(array('Message'), true, true, ''));
        $this->assertEquals(false, message_search(array('Message'), true, true, 2));
        $this->assertCount(5, message_search(array('Message'), true, true, SITEID));
    }

    /**
     * Test message_get_recent_conversations.
     */
    public function test_message_get_recent_conversations() {
        global $DB, $USER;

        // Set this user as the admin.
        $this->setAdminUser();

        // Create user's to send messages to/from.
        $user1 = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'user2'));

        // Add a few messages that have been read and some that are unread.
        $m1 = $this->send_fake_message($USER, $user1, 'Message 1'); // An unread message.
        $m2 = $this->send_fake_message($user1, $USER, 'Message 2'); // An unread message.
        $m3 = $this->send_fake_message($USER, $user1, 'Message 3'); // An unread message.
        $m4 = message_post_message($USER, $user2, 'Message 4', FORMAT_PLAIN);
        $m5 = message_post_message($user2, $USER, 'Message 5', FORMAT_PLAIN);
        $m6 = message_post_message($USER, $user2, 'Message 6', FORMAT_PLAIN);

        // We want to alter the timecreated values so we can ensure message_get_recent_conversations orders
        // by timecreated, not the max id, to begin with. However, we also want more than one message to have
        // the same timecreated value to ensure that when this happens we retrieve the one with the maximum id.

        // Store the current time.
        $time = time();

        // Set the first and second unread messages to have the same timecreated value.
        $updatemessage = new stdClass();
        $updatemessage->id = $m1;
        $updatemessage->timecreated = $time;
        $DB->update_record('message', $updatemessage);

        $updatemessage->id = $m2;
        $updatemessage->timecreated = $time;
        $DB->update_record('message', $updatemessage);

        // Set the third unread message to have a timecreated value of 0.
        $updatemessage->id = $m3;
        $updatemessage->timecreated = 0;
        $DB->update_record('message', $updatemessage);

        // Set the first and second read messages to have the same timecreated value.
        $updatemessage->id = $m4;
        $updatemessage->timecreated = $time + 1;
        $DB->update_record('message', $updatemessage);

        $updatemessage->id = $m5;
        $updatemessage->timecreated = $time + 1;
        $DB->update_record('message', $updatemessage);

        // Set the third read message to have a timecreated value of 0.
        $updatemessage->id = $m6;
        $updatemessage->timecreated = 0;
        $DB->update_record('message_read', $updatemessage);

        // Get the recent conversations for the current user.
        $conversations = message_get_recent_conversations($USER);

        // Confirm that we have received the messages with the maximum timecreated, rather than the max id.
        $this->assertEquals('Message 2', $conversations[0]->fullmessage);
        $this->assertEquals('Message 5', $conversations[1]->smallmessage);
    }

    /**
     * Test message_get_recent_notifications.
     */
    public function test_message_get_recent_notifications() {
        global $DB, $USER;

        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to send messages from.
        $user1 = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'user1'));

        // Add two messages - will mark them as notifications later.
        $m1 = message_post_message($user1, $USER, 'Message 1', FORMAT_PLAIN);
        $m2 = message_post_message($user1, $USER, 'Message 2', FORMAT_PLAIN);

        // Mark the second message as a notification.
        $updatemessage = new stdClass();
        $updatemessage->id = $m2;
        $updatemessage->notification = 1;
        $DB->update_record('message_read', $updatemessage);

        // Mark the first message as a notification and change the timecreated to 0.
        $updatemessage->id = $m1;
        $updatemessage->notification = 1;
        $updatemessage->timecreated = 0;
        $DB->update_record('message_read', $updatemessage);

        $notifications = message_get_recent_notifications($USER);

        // Get the messages.
        $firstmessage = array_shift($notifications);
        $secondmessage = array_shift($notifications);

        // Confirm that we have received the notifications with the maximum timecreated, rather than the max id.
        $this->assertEquals('Message 2', $firstmessage->smallmessage);
        $this->assertEquals('Message 1', $secondmessage->smallmessage);
    }
}
