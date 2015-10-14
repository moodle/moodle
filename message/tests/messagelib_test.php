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
        $record->smallmessage = $message;
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

        // Test deleting users.
        delete_user($user1);
        $this->assertCount(1, message_get_blocked_users());
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

        // Test deleting users.
        delete_user($user1);
        delete_user($user3);

        list($onlinecontacts, $offlinecontacts, $strangers) = message_get_contacts();
        $this->assertCount(0, $onlinecontacts);
        $this->assertCount(0, $offlinecontacts);
        $this->assertCount(1, $strangers);
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
     * The data provider for message_get_recent_conversations.
     *
     * This provides sets of data to for testing.
     * @return array
     */
    public function message_get_recent_conversations_provider() {
        return array(
            array(
                // Test that conversations with multiple contacts is correctly ordered.
                'users' => array(
                    'user1',
                    'user2',
                    'user3',
                ),
                'messages' => array(
                    array(
                        'from'          => 'user1',
                        'to'            => 'user2',
                        'state'         => 'unread',
                        'subject'       => 'S1',
                    ),
                    array(
                        'from'          => 'user2',
                        'to'            => 'user1',
                        'state'         => 'unread',
                        'subject'       => 'S2',
                    ),
                    array(
                        'from'          => 'user1',
                        'to'            => 'user2',
                        'state'         => 'unread',
                        'timecreated'   => 0,
                        'subject'       => 'S3',
                    ),
                    array(
                        'from'          => 'user1',
                        'to'            => 'user3',
                        'state'         => 'read',
                        'timemodifier'  => 1,
                        'subject'       => 'S4',
                    ),
                    array(
                        'from'          => 'user3',
                        'to'            => 'user1',
                        'state'         => 'read',
                        'timemodifier'  => 1,
                        'subject'       => 'S5',
                    ),
                    array(
                        'from'          => 'user1',
                        'to'            => 'user3',
                        'state'         => 'read',
                        'timecreated'   => 0,
                        'subject'       => 'S6',
                    ),
                ),
                'expectations' => array(
                    'user1' => array(
                        // User1 has conversed most recently with user3. The most recent message is M5.
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user3',
                            'subject'           => 'S5',
                        ),
                        // User1 has also conversed with user2. The most recent message is S2.
                        array(
                            'messageposition'   => 1,
                            'with'              => 'user2',
                            'subject'           => 'S2',
                        ),
                    ),
                    'user2' => array(
                        // User2 has only conversed with user1. Their most recent shared message was S2.
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user1',
                            'subject'           => 'S2',
                        ),
                    ),
                    'user3' => array(
                        // User3 has only conversed with user1. Their most recent shared message was S5.
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user1',
                            'subject'           => 'S5',
                        ),
                    ),
                ),
            ),
            array(
                // Test conversations with a single user, where some messages are read and some are not.
                'users' => array(
                    'user1',
                    'user2',
                ),
                'messages' => array(
                    array(
                        'from'          => 'user1',
                        'to'            => 'user2',
                        'state'         => 'read',
                        'subject'       => 'S1',
                    ),
                    array(
                        'from'          => 'user2',
                        'to'            => 'user1',
                        'state'         => 'read',
                        'subject'       => 'S2',
                    ),
                    array(
                        'from'          => 'user1',
                        'to'            => 'user2',
                        'state'         => 'unread',
                        'timemodifier'  => 1,
                        'subject'       => 'S3',
                    ),
                    array(
                        'from'          => 'user1',
                        'to'            => 'user2',
                        'state'         => 'unread',
                        'timemodifier'  => 1,
                        'subject'       => 'S4',
                    ),
                ),
                'expectations' => array(
                    // The most recent message between user1 and user2 was S4.
                    'user1' => array(
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user2',
                            'subject'           => 'S4',
                        ),
                    ),
                    'user2' => array(
                        // The most recent message between user1 and user2 was S4.
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user1',
                            'subject'           => 'S4',
                        ),
                    ),
                ),
            ),
            array(
                // Test conversations with a single user, where some messages are read and some are not, and messages
                // are out of order.
                // This can happen through a combination of factors including multi-master DB replication with messages
                // read somehow (e.g. API).
                'users' => array(
                    'user1',
                    'user2',
                ),
                'messages' => array(
                    array(
                        'from'          => 'user1',
                        'to'            => 'user2',
                        'state'         => 'read',
                        'subject'       => 'S1',
                        'timemodifier'  => 1,
                    ),
                    array(
                        'from'          => 'user2',
                        'to'            => 'user1',
                        'state'         => 'read',
                        'subject'       => 'S2',
                        'timemodifier'  => 2,
                    ),
                    array(
                        'from'          => 'user1',
                        'to'            => 'user2',
                        'state'         => 'unread',
                        'subject'       => 'S3',
                    ),
                    array(
                        'from'          => 'user1',
                        'to'            => 'user2',
                        'state'         => 'unread',
                        'subject'       => 'S4',
                    ),
                ),
                'expectations' => array(
                    // The most recent message between user1 and user2 was S2, even though later IDs have not been read.
                    'user1' => array(
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user2',
                            'subject'           => 'S2',
                        ),
                    ),
                    'user2' => array(
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user1',
                            'subject'           => 'S2',
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * Test message_get_recent_conversations with a mixture of messages.
     *
     * @dataProvider message_get_recent_conversations_provider
     * @param array $usersdata The list of users to create for this test.
     * @param array $messagesdata The list of messages to create.
     * @param array $expectations The list of expected outcomes.
     */
    public function test_message_get_recent_conversations($usersdata, $messagesdata, $expectations) {
        global $DB;

        // Create all of the users.
        $users = array();
        foreach ($usersdata as $username) {
            $users[$username] = $this->getDataGenerator()->create_user(array('username' => $username));
        }

        $defaulttimecreated = time();
        foreach ($messagesdata as $messagedata) {
            $from       = $users[$messagedata['from']];
            $to         = $users[$messagedata['to']];
            $subject    = $messagedata['subject'];

            if (isset($messagedata['state']) && $messagedata['state'] == 'unread') {
                $table = 'message';
                $messageid = $this->send_fake_message($from, $to, $subject, FORMAT_PLAIN);
            } else {
                // If there is no state, or the state is not 'unread', assume the message is read.
                $table = 'message_read';
                $messageid = message_post_message($from, $to, $subject, FORMAT_PLAIN);
            }

            $updatemessage = new stdClass();
            $updatemessage->id = $messageid;
            if (isset($messagedata['timecreated'])) {
                $updatemessage->timecreated = $messagedata['timecreated'];
            } else if (isset($messagedata['timemodifier'])) {
                $updatemessage->timecreated = $defaulttimecreated + $messagedata['timemodifier'];
            } else {
                $updatemessage->timecreated = $defaulttimecreated;
            }
            $DB->update_record($table, $updatemessage);
        }

        foreach ($expectations as $username => $data) {
            // Get the recent conversations for the specified user.
            $user = $users[$username];
            $conversations = message_get_recent_conversations($user);
            foreach ($data as $expectation) {
                $otheruser = $users[$expectation['with']];
                $conversation = $conversations[$expectation['messageposition']];
                $this->assertEquals($otheruser->id, $conversation->id);
                $this->assertEquals($expectation['subject'], $conversation->smallmessage);
            }
        }
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

    /**
     * Test that message_can_post_message returns false if the sender does not have the
     * moode/site:sendmessage capability.
     */
    public function test_message_can_post_message_returns_false_without_capability() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));
        $context = context_system::instance();
        $roleid = $this->getDataGenerator()->create_role();
        $this->getDataGenerator()->role_assign($roleid, $sender->id, $context->id);

        assign_capability('moodle/site:sendmessage', CAP_PROHIBIT, $roleid, $context);

        $this->assertFalse(message_can_post_message($recipient, $sender));
    }

    /**
     * Test that message_can_post_message returns false if the receiver only accepts
     * messages from contacts and the sender isn't a contact.
     */
    public function test_message_can_post_message_returns_false_non_contact_blocked() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        set_user_preference('message_blocknoncontacts', true, $recipient);

        $this->assertFalse(message_can_post_message($recipient, $sender));
    }

    /**
     * Test that message_can_post_message returns false if the receiver has blocked the
     * sender from messaging them.
     */
    public function test_message_can_post_message_returns_false_if_blocked() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->setUser($recipient);
        message_block_contact($sender->id);

        $this->assertFalse(message_can_post_message($recipient, $sender));
    }

    /**
     * Test that message_can_post_message returns false if the receiver has blocked the
     * sender from messaging them.
     */
    public function test_message_can_post_message_returns_true() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->assertTrue(message_can_post_message($recipient, $sender));
    }

    /**
     * Test that message_is_user_non_contact_blocked returns false if the recipient allows
     * messages from non-contacts.
     */
    public function test_message_is_user_non_contact_blocked_false_without_preference() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        set_user_preference('message_blocknoncontacts', false, $recipient);

        $this->assertFalse(message_is_user_non_contact_blocked($recipient, $sender));
    }

    /**
     * Test that message_is_user_non_contact_blocked returns true if the recipient doesn't
     * allow messages from non-contacts and the sender isn't a contact.
     */
    public function test_message_is_user_non_contact_blocked_true_with_preference() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        set_user_preference('message_blocknoncontacts', true, $recipient);

        $this->assertTrue(message_is_user_non_contact_blocked($recipient, $sender));
    }

    /**
     * Test that message_is_user_non_contact_blocked returns false if the recipient doesn't
     * allow messages from non-contacts but the sender is a contact.
     */
    public function test_message_is_user_non_contact_blocked_false_with_if_contact() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->setUser($recipient);
        set_user_preference('message_blocknoncontacts', true, $recipient);
        message_add_contact($sender->id);

        $this->assertFalse(message_is_user_non_contact_blocked($recipient, $sender));
    }

    /**
     * Test that message_is_user_blocked returns false if the sender is not a contact of
     * the recipient.
     */
    public function test_message_is_user_blocked_false_no_contact() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->assertFalse(message_is_user_blocked($recipient, $sender));
    }

    /**
     * Test that message_is_user_blocked returns false if the sender is a contact that is
     * blocked by the recipient but has the moodle/site:readallmessages capability.
     */
    public function test_message_is_user_blocked_false_if_readallmessages() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->setUser($recipient);
        message_block_contact($sender->id);

        $context = context_system::instance();
        $roleid = $this->getDataGenerator()->create_role();
        $this->getDataGenerator()->role_assign($roleid, $sender->id, $context->id);

        assign_capability('moodle/site:readallmessages', CAP_ALLOW, $roleid, $context);

        $this->assertFalse(message_is_user_blocked($recipient, $sender));
    }

    /**
     * Test that message_is_user_blocked returns true if the sender is a contact that is
     * blocked by the recipient and does not have the moodle/site:readallmessages capability.
     */
    public function test_message_is_user_blocked_true_if_blocked() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->setUser($recipient);
        message_block_contact($sender->id);

        $context = context_system::instance();
        $roleid = $this->getDataGenerator()->create_role();
        $this->getDataGenerator()->role_assign($roleid, $sender->id, $context->id);

        assign_capability('moodle/site:readallmessages', CAP_PROHIBIT, $roleid, $context);

        $this->assertTrue(message_is_user_blocked($recipient, $sender));
    }
}
