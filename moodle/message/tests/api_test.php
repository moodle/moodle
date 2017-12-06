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
 * Test message API.
 *
 * @package core_message
 * @category test
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/message/tests/messagelib_test.php');

/**
 * Test message API.
 *
 * @package core_message
 * @category test
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_message_api_testcase extends core_message_messagelib_testcase {

    public function test_mark_all_read_for_user_touser() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->send_fake_message($sender, $recipient, 'Notification', 1);
        $this->send_fake_message($sender, $recipient, 'Notification', 1);
        $this->send_fake_message($sender, $recipient, 'Notification', 1);
        $this->send_fake_message($sender, $recipient);
        $this->send_fake_message($sender, $recipient);
        $this->send_fake_message($sender, $recipient);

        \core_message\api::mark_all_read_for_user($recipient->id);
        $this->assertEquals(message_count_unread_messages($recipient), 0);
    }

    public function test_mark_all_read_for_user_touser_with_fromuser() {
        $sender1 = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $sender2 = $this->getDataGenerator()->create_user(array('firstname' => 'Test3', 'lastname' => 'User3'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->send_fake_message($sender1, $recipient, 'Notification', 1);
        $this->send_fake_message($sender1, $recipient, 'Notification', 1);
        $this->send_fake_message($sender1, $recipient, 'Notification', 1);
        $this->send_fake_message($sender1, $recipient);
        $this->send_fake_message($sender1, $recipient);
        $this->send_fake_message($sender1, $recipient);
        $this->send_fake_message($sender2, $recipient, 'Notification', 1);
        $this->send_fake_message($sender2, $recipient, 'Notification', 1);
        $this->send_fake_message($sender2, $recipient, 'Notification', 1);
        $this->send_fake_message($sender2, $recipient);
        $this->send_fake_message($sender2, $recipient);
        $this->send_fake_message($sender2, $recipient);

        \core_message\api::mark_all_read_for_user($recipient->id, $sender1->id);
        $this->assertEquals(message_count_unread_messages($recipient), 3);
    }

    public function test_mark_all_read_for_user_touser_with_type() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->send_fake_message($sender, $recipient, 'Notification', 1);
        $this->send_fake_message($sender, $recipient, 'Notification', 1);
        $this->send_fake_message($sender, $recipient, 'Notification', 1);
        $this->send_fake_message($sender, $recipient);
        $this->send_fake_message($sender, $recipient);
        $this->send_fake_message($sender, $recipient);

        \core_message\api::mark_all_read_for_user($recipient->id, 0, MESSAGE_TYPE_NOTIFICATION);
        $this->assertEquals(message_count_unread_messages($recipient), 3);

        \core_message\api::mark_all_read_for_user($recipient->id, 0, MESSAGE_TYPE_MESSAGE);
        $this->assertEquals(message_count_unread_messages($recipient), 0);
    }

    /**
     * Test count_blocked_users.
     */
    public function test_count_blocked_users() {
        // Set this user as the admin.
        $this->setAdminUser();

        // Create users to add to the admin's contact list.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->assertEquals(0, \core_message\api::count_blocked_users());

        // Add 1 blocked and 1 normal contact to admin's contact list.
        message_add_contact($user1->id);
        message_add_contact($user2->id, 1);

        $this->assertEquals(0, \core_message\api::count_blocked_users($user2));
        $this->assertEquals(1, \core_message\api::count_blocked_users());
    }

    /**
     * Tests searching users in a course.
     */
    public function test_search_users_in_course() {
        // Create some users.
        $user1 = new stdClass();
        $user1->firstname = 'User';
        $user1->lastname = 'One';
        $user1 = self::getDataGenerator()->create_user($user1);

        // The person doing the search.
        $this->setUser($user1);

        // Second user is going to have their last access set to now, so they are online.
        $user2 = new stdClass();
        $user2->firstname = 'User';
        $user2->lastname = 'Two';
        $user2->lastaccess = time();
        $user2 = self::getDataGenerator()->create_user($user2);

        // Block the second user.
        message_block_contact($user2->id, $user1->id);

        $user3 = new stdClass();
        $user3->firstname = 'User';
        $user3->lastname = 'Three';
        $user3 = self::getDataGenerator()->create_user($user3);

        // Create a course.
        $course1 = new stdClass();
        $course1->fullname = 'Course';
        $course1->shortname = 'One';
        $course1 = $this->getDataGenerator()->create_course($course1);

        // Enrol the searcher and one user in the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);

        // Perform a search.
        $results = \core_message\api::search_users_in_course($user1->id, $course1->id, 'User');

        $this->assertEquals(1, count($results));

        $user = $results[0];
        $this->assertEquals($user2->id, $user->userid);
        $this->assertEquals(fullname($user2), $user->fullname);
        $this->assertFalse($user->ismessaging);
        $this->assertNull($user->lastmessage);
        $this->assertNull($user->messageid);
        $this->assertNull($user->isonline);
        $this->assertFalse($user->isread);
        $this->assertTrue($user->isblocked);
        $this->assertNull($user->unreadcount);
    }

    /**
     * Tests searching users.
     */
    public function test_search_users() {
        global $DB;

        // Create some users.
        $user1 = new stdClass();
        $user1->firstname = 'User';
        $user1->lastname = 'One';
        $user1 = self::getDataGenerator()->create_user($user1);

        // Set as the user performing the search.
        $this->setUser($user1);

        $user2 = new stdClass();
        $user2->firstname = 'User search';
        $user2->lastname = 'Two';
        $user2 = self::getDataGenerator()->create_user($user2);

        $user3 = new stdClass();
        $user3->firstname = 'User search';
        $user3->lastname = 'Three';
        $user3 = self::getDataGenerator()->create_user($user3);

        $user4 = new stdClass();
        $user4->firstname = 'User';
        $user4->lastname = 'Four';
        $user4 = self::getDataGenerator()->create_user($user4);

        $user5 = new stdClass();
        $user5->firstname = 'User search';
        $user5->lastname = 'Five';
        $user5 = self::getDataGenerator()->create_user($user5);

        $user6 = new stdClass();
        $user6->firstname = 'User';
        $user6->lastname = 'Six';
        $user6 = self::getDataGenerator()->create_user($user6);

        // Create some courses.
        $course1 = new stdClass();
        $course1->fullname = 'Course search';
        $course1->shortname = 'One';
        $course1 = $this->getDataGenerator()->create_course($course1);

        $course2 = new stdClass();
        $course2->fullname = 'Course';
        $course2->shortname = 'Two';
        $course2 = $this->getDataGenerator()->create_course($course2);

        $course3 = new stdClass();
        $course3->fullname = 'Course';
        $course3->shortname = 'Three search';
        $course3 = $this->getDataGenerator()->create_course($course3);

        $course4 = new stdClass();
        $course4->fullname = 'Course Four';
        $course4->shortname = 'CF100';
        $course4 = $this->getDataGenerator()->create_course($course4);

        $course5 = new stdClass();
        $course5->fullname = 'Course';
        $course5->shortname = 'Five search';
        $course5 = $this->getDataGenerator()->create_course($course5);

        $role = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $role->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $role->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id, $role->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course5->id, $role->id);

        // Add some users as contacts.
        message_add_contact($user2->id, 0, $user1->id);
        message_add_contact($user3->id, 0, $user1->id);
        message_add_contact($user4->id, 0, $user1->id);

        // Remove the viewparticipants capability from one of the courses.
        $course5context = context_course::instance($course5->id);
        assign_capability('moodle/course:viewparticipants', CAP_PROHIBIT, $role->id, $course5context->id);
        $course5context->mark_dirty();

        // Perform a search.
        list($contacts, $courses, $noncontacts) = \core_message\api::search_users($user1->id, 'search');

        // Check that we retrieved the correct contacts.
        $this->assertEquals(2, count($contacts));
        $this->assertEquals($user3->id, $contacts[0]->userid);
        $this->assertEquals($user2->id, $contacts[1]->userid);

        // Check that we retrieved the correct courses.
        $this->assertEquals(2, count($courses));
        $this->assertEquals($course3->id, $courses[0]->id);
        $this->assertEquals($course1->id, $courses[1]->id);

        // Check that we retrieved the correct non-contacts.
        $this->assertEquals(1, count($noncontacts));
        $this->assertEquals($user5->id, $noncontacts[0]->userid);
    }

    /**
     * Tests searching messages.
     */
    public function test_search_messages() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = 1;
        $this->send_fake_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_fake_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_fake_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_fake_message($user2, $user1, 'Word.', 0, $time + 3);

        // Perform a search.
        $messages = \core_message\api::search_messages($user1->id, 'o');

        // Confirm the data is correct.
        $this->assertEquals(2, count($messages));

        $message1 = $messages[0];
        $message2 = $messages[1];

        $this->assertEquals($user2->id, $message1->userid);
        $this->assertEquals($user2->id, $message1->useridfrom);
        $this->assertEquals(fullname($user2), $message1->fullname);
        $this->assertTrue($message1->ismessaging);
        $this->assertEquals('Word.', $message1->lastmessage);
        $this->assertNotEmpty($message1->messageid);
        $this->assertNull($message1->isonline);
        $this->assertFalse($message1->isread);
        $this->assertFalse($message1->isblocked);
        $this->assertNull($message1->unreadcount);

        $this->assertEquals($user2->id, $message2->userid);
        $this->assertEquals($user1->id, $message2->useridfrom);
        $this->assertEquals(fullname($user2), $message2->fullname);
        $this->assertTrue($message2->ismessaging);
        $this->assertEquals('Yo!', $message2->lastmessage);
        $this->assertNotEmpty($message2->messageid);
        $this->assertNull($message2->isonline);
        $this->assertTrue($message2->isread);
        $this->assertFalse($message2->isblocked);
        $this->assertNull($message2->unreadcount);
    }

    /**
     * Tests retrieving conversations.
     */
    public function test_get_conversations() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user1);

        // No conversations yet.
        $this->assertEquals([], \core_message\api::get_conversations($user1->id));

        // Send some messages back and forth, have some different conversations with different users.
        $time = 1;
        $this->send_fake_message($user1, $user2, 'Yo!', 0, $time + 1);
        $this->send_fake_message($user2, $user1, 'Sup mang?', 0, $time + 2);
        $this->send_fake_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 3);
        $messageid1 = $this->send_fake_message($user2, $user1, 'Word.', 0, $time + 4);

        $this->send_fake_message($user1, $user3, 'Booyah', 0, $time + 5);
        $this->send_fake_message($user3, $user1, 'Whaaat?', 0, $time + 6);
        $this->send_fake_message($user1, $user3, 'Nothing.', 0, $time + 7);
        $messageid2 = $this->send_fake_message($user3, $user1, 'Cool.', 0, $time + 8);

        $this->send_fake_message($user1, $user4, 'Hey mate, you see the new messaging UI in Moodle?', 0, $time + 9);
        $this->send_fake_message($user4, $user1, 'Yah brah, it\'s pretty rad.', 0, $time + 10);
        $messageid3 = $this->send_fake_message($user1, $user4, 'Dope.', 0, $time + 11);

        // Retrieve the conversations.
        $conversations = \core_message\api::get_conversations($user1->id);

        // Confirm the data is correct.
        $this->assertEquals(3, count($conversations));

        $message1 = array_shift($conversations);
        $message2 = array_shift($conversations);
        $message3 = array_shift($conversations);

        $this->assertEquals($user4->id, $message1->userid);
        $this->assertEquals($user1->id, $message1->useridfrom);
        $this->assertTrue($message1->ismessaging);
        $this->assertEquals('Dope.', $message1->lastmessage);
        $this->assertEquals($messageid3, $message1->messageid);
        $this->assertNull($message1->isonline);
        $this->assertFalse($message1->isread);
        $this->assertFalse($message1->isblocked);
        $this->assertEquals(1, $message1->unreadcount);

        $this->assertEquals($user3->id, $message2->userid);
        $this->assertEquals($user3->id, $message2->useridfrom);
        $this->assertTrue($message2->ismessaging);
        $this->assertEquals('Cool.', $message2->lastmessage);
        $this->assertEquals($messageid2, $message2->messageid);
        $this->assertNull($message2->isonline);
        $this->assertFalse($message2->isread);
        $this->assertFalse($message2->isblocked);
        $this->assertEquals(2, $message2->unreadcount);

        $this->assertEquals($user2->id, $message3->userid);
        $this->assertEquals($user2->id, $message3->useridfrom);
        $this->assertTrue($message3->ismessaging);
        $this->assertEquals('Word.', $message3->lastmessage);
        $this->assertEquals($messageid1, $message3->messageid);
        $this->assertNull($message3->isonline);
        $this->assertFalse($message3->isread);
        $this->assertFalse($message3->isblocked);
        $this->assertEquals(2, $message3->unreadcount);
    }

    /**
     * Tests retrieving conversations with a limit and offset to ensure pagination works correctly.
     */
    public function test_get_conversations_limit_offset() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user1);

        // Send some messages back and forth, have some different conversations with different users.
        $time = 1;
        $this->send_fake_message($user1, $user2, 'Yo!', 0, $time + 1);
        $this->send_fake_message($user2, $user1, 'Sup mang?', 0, $time + 2);
        $this->send_fake_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 3);
        $messageid1 = $this->send_fake_message($user2, $user1, 'Word.', 0, $time + 4);

        $this->send_fake_message($user1, $user3, 'Booyah', 0, $time + 5);
        $this->send_fake_message($user3, $user1, 'Whaaat?', 0, $time + 6);
        $this->send_fake_message($user1, $user3, 'Nothing.', 0, $time + 7);
        $messageid2 = $this->send_fake_message($user3, $user1, 'Cool.', 0, $time + 8);

        $this->send_fake_message($user1, $user4, 'Hey mate, you see the new messaging UI in Moodle?', 0, $time + 9);
        $this->send_fake_message($user4, $user1, 'Yah brah, it\'s pretty rad.', 0, $time + 10);
        $messageid3 = $this->send_fake_message($user1, $user4, 'Dope.', 0, $time + 11);

        // Retrieve the conversations.
        $conversations = \core_message\api::get_conversations($user1->id, 1, 1);

        // We should only have one conversation because of the limit.
        $this->assertCount(1, $conversations);

        $conversation = array_shift($conversations);

        $this->assertEquals($user3->id, $conversation->userid);
        $this->assertEquals($user3->id, $conversation->useridfrom);
        $this->assertTrue($conversation->ismessaging);
        $this->assertEquals('Cool.', $conversation->lastmessage);
        $this->assertEquals($messageid2, $conversation->messageid);
        $this->assertNull($conversation->isonline);
        $this->assertFalse($conversation->isread);
        $this->assertFalse($conversation->isblocked);
        $this->assertEquals(2, $conversation->unreadcount);

        // Retrieve the next conversation.
        $conversations = \core_message\api::get_conversations($user1->id, 2, 1);

        // We should only have one conversation because of the limit.
        $this->assertCount(1, $conversations);

        $conversation = array_shift($conversations);

        $this->assertEquals($user2->id, $conversation->userid);
        $this->assertEquals($user2->id, $conversation->useridfrom);
        $this->assertTrue($conversation->ismessaging);
        $this->assertEquals('Word.', $conversation->lastmessage);
        $this->assertEquals($messageid1, $conversation->messageid);
        $this->assertNull($conversation->isonline);
        $this->assertFalse($conversation->isread);
        $this->assertFalse($conversation->isblocked);
        $this->assertEquals(2, $conversation->unreadcount);

        // Ask for an offset that doesn't exist.
        $conversations = \core_message\api::get_conversations($user1->id, 4, 1);

        // We should not get any conversations back.
        $this->assertCount(0, $conversations);
    }

    /**
     * Tests retrieving conversations when a conversation contains a deleted user.
     */
    public function test_get_conversations_with_deleted_user() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // Send some messages back and forth, have some different conversations with different users.
        $time = 1;
        $this->send_fake_message($user1, $user2, 'Yo!', 0, $time + 1);
        $this->send_fake_message($user2, $user1, 'Sup mang?', 0, $time + 2);
        $this->send_fake_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 3);
        $this->send_fake_message($user2, $user1, 'Word.', 0, $time + 4);

        $this->send_fake_message($user1, $user3, 'Booyah', 0, $time + 5);
        $this->send_fake_message($user3, $user1, 'Whaaat?', 0, $time + 6);
        $this->send_fake_message($user1, $user3, 'Nothing.', 0, $time + 7);
        $this->send_fake_message($user3, $user1, 'Cool.', 0, $time + 8);

        // Delete the second user.
        delete_user($user2);

        // Retrieve the conversations.
        $conversations = \core_message\api::get_conversations($user1->id);

        // We should only have one conversation because the other user was deleted.
        $this->assertCount(1, $conversations);

        // Confirm the conversation is from the non-deleted user.
        $conversation = reset($conversations);
        $this->assertEquals($user3->id, $conversation->userid);
    }

   /**
    * The data provider for get_conversations_mixed.
    *
    * This provides sets of data to for testing.
    * @return array
    */
   public function get_conversations_mixed_provider() {
       return array(
            'Test that conversations with messages contacts is correctly ordered.' => array(
                'users' => array(
                    'user1',
                    'user2',
                    'user3',
                ),
                'contacts' => array(
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
                            'unreadcount'       => 0,
                        ),
                        // User1 has also conversed with user2. The most recent message is S2.
                        array(
                            'messageposition'   => 1,
                            'with'              => 'user2',
                            'subject'           => 'S2',
                            'unreadcount'       => 1,
                        ),
                    ),
                    'user2' => array(
                        // User2 has only conversed with user1. Their most recent shared message was S2.
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user1',
                            'subject'           => 'S2',
                            'unreadcount'       => 2,
                        ),
                    ),
                    'user3' => array(
                        // User3 has only conversed with user1. Their most recent shared message was S5.
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user1',
                            'subject'           => 'S5',
                            'unreadcount'       => 0,
                        ),
                    ),
                ),
            ),
            'Test that users with contacts and messages to self work as expected' => array(
                'users' => array(
                    'user1',
                    'user2',
                    'user3',
                ),
                'contacts' => array(
                    'user1' => array(
                        'user2' => 0,
                        'user3' => 0,
                    ),
                    'user2' => array(
                        'user3' => 0,
                    ),
                ),
                'messages' => array(
                    array(
                        'from'          => 'user1',
                        'to'            => 'user1',
                        'state'         => 'unread',
                        'subject'       => 'S1',
                    ),
                    array(
                        'from'          => 'user1',
                        'to'            => 'user1',
                        'state'         => 'unread',
                        'subject'       => 'S2',
                    ),
                ),
                'expectations' => array(
                    'user1' => array(
                        // User1 has conversed most recently with user1. The most recent message is S2.
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user1',
                            'subject'           => 'S2',
                            'unreadcount'       => 2,
                        ),
                    ),
                ),
            ),
            'Test conversations with a single user, where some messages are read and some are not.' => array(
                'users' => array(
                    'user1',
                    'user2',
                ),
                'contacts' => array(
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
                            'unreadcount'       => 0,
                        ),
                    ),
                    'user2' => array(
                        // The most recent message between user1 and user2 was S4.
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user1',
                            'subject'           => 'S4',
                            'unreadcount'       => 2,
                        ),
                    ),
                ),
            ),
            'Test conversations with a single user, where some messages are read and some are not, and messages ' .
            'are out of order' => array(
            // This can happen through a combination of factors including multi-master DB replication with messages
            // read somehow (e.g. API).
                'users' => array(
                    'user1',
                    'user2',
                ),
                'contacts' => array(
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
                            'unreadcount'       => 0,
                        ),
                    ),
                    'user2' => array(
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user1',
                            'subject'           => 'S2',
                            'unreadcount'       => 2
                        ),
                    ),
                ),
            ),
            'Test unread message count is correct for both users' => array(
                'users' => array(
                    'user1',
                    'user2',
                ),
                'contacts' => array(
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
                        'state'         => 'read',
                        'subject'       => 'S3',
                        'timemodifier'  => 3,
                    ),
                    array(
                        'from'          => 'user1',
                        'to'            => 'user2',
                        'state'         => 'read',
                        'subject'       => 'S4',
                        'timemodifier'  => 4,
                    ),
                    array(
                        'from'          => 'user1',
                        'to'            => 'user2',
                        'state'         => 'unread',
                        'subject'       => 'S5',
                        'timemodifier'  => 5,
                    ),
                    array(
                        'from'          => 'user2',
                        'to'            => 'user1',
                        'state'         => 'unread',
                        'subject'       => 'S6',
                        'timemodifier'  => 6,
                    ),
                    array(
                        'from'          => 'user1',
                        'to'            => 'user2',
                        'state'         => 'unread',
                        'subject'       => 'S7',
                        'timemodifier'  => 7,
                    ),
                    array(
                        'from'          => 'user1',
                        'to'            => 'user2',
                        'state'         => 'unread',
                        'subject'       => 'S8',
                        'timemodifier'  => 8,
                    ),
                ),
                'expectations' => array(
                    // The most recent message between user1 and user2 was S2, even though later IDs have not been read.
                    'user1' => array(
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user2',
                            'subject'           => 'S8',
                            'unreadcount'       => 1,
                        ),
                    ),
                    'user2' => array(
                        array(
                            'messageposition'   => 0,
                            'with'              => 'user1',
                            'subject'           => 'S8',
                            'unreadcount'       => 3,
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * Test get_conversations with a mixture of messages.
     *
     * @dataProvider get_conversations_mixed_provider
     * @param array $usersdata The list of users to create for this test.
     * @param array $messagesdata The list of messages to create.
     * @param array $expectations The list of expected outcomes.
     */
    public function test_get_conversations_mixed($usersdata, $contacts, $messagesdata, $expectations) {
        global $DB;

        // Create all of the users.
        $users = array();
        foreach ($usersdata as $username) {
            $users[$username] = $this->getDataGenerator()->create_user(array('username' => $username));
        }

        foreach ($contacts as $username => $contact) {
            foreach ($contact as $contactname => $blocked) {
                $record = new stdClass();
                $record->userid     = $users[$username]->id;
                $record->contactid  = $users[$contactname]->id;
                $record->blocked    = $blocked;
                $record->id = $DB->insert_record('message_contacts', $record);
            }
        }

        $defaulttimecreated = time();
        foreach ($messagesdata as $messagedata) {
            $from       = $users[$messagedata['from']];
            $to         = $users[$messagedata['to']];
            $subject    = $messagedata['subject'];

            if (isset($messagedata['state']) && $messagedata['state'] == 'unread') {
                $table = 'message';
                $messageid = $this->send_fake_message($from, $to, $subject);
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
            $conversations = array_values(\core_message\api::get_conversations($user->id));
            foreach ($data as $expectation) {
                $otheruser = $users[$expectation['with']];
                $conversation = $conversations[$expectation['messageposition']];
                $this->assertEquals($otheruser->id, $conversation->userid);
                $this->assertEquals($expectation['subject'], $conversation->lastmessage);
                $this->assertEquals($expectation['unreadcount'], $conversation->unreadcount);
            }
        }
    }

    /**
     * Tests retrieving contacts.
     */
    public function test_get_contacts() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();

        // Set as the user.
        $this->setUser($user1);

        $user2 = new stdClass();
        $user2->firstname = 'User';
        $user2->lastname = 'A';
        $user2 = self::getDataGenerator()->create_user($user2);

        $user3 = new stdClass();
        $user3->firstname = 'User';
        $user3->lastname = 'B';
        $user3 = self::getDataGenerator()->create_user($user3);

        $user4 = new stdClass();
        $user4->firstname = 'User';
        $user4->lastname = 'C';
        $user4 = self::getDataGenerator()->create_user($user4);

        $user5 = new stdClass();
        $user5->firstname = 'User';
        $user5->lastname = 'D';
        $user5 = self::getDataGenerator()->create_user($user5);

        // Add some users as contacts.
        message_add_contact($user2->id, 0, $user1->id);
        message_add_contact($user3->id, 0, $user1->id);
        message_add_contact($user4->id, 0, $user1->id);

        // Retrieve the contacts.
        $contacts = \core_message\api::get_contacts($user1->id);

        // Confirm the data is correct.
        $this->assertEquals(3, count($contacts));

        $contact1 = $contacts[0];
        $contact2 = $contacts[1];
        $contact3 = $contacts[2];

        $this->assertEquals($user2->id, $contact1->userid);
        $this->assertEmpty($contact1->useridfrom);
        $this->assertFalse($contact1->ismessaging);
        $this->assertNull($contact1->lastmessage);
        $this->assertNull($contact1->messageid);
        $this->assertNull($contact1->isonline);
        $this->assertFalse($contact1->isread);
        $this->assertFalse($contact1->isblocked);
        $this->assertNull($contact1->unreadcount);

        $this->assertEquals($user3->id, $contact2->userid);
        $this->assertEmpty($contact2->useridfrom);
        $this->assertFalse($contact2->ismessaging);
        $this->assertNull($contact2->lastmessage);
        $this->assertNull($contact2->messageid);
        $this->assertNull($contact2->isonline);
        $this->assertFalse($contact2->isread);
        $this->assertFalse($contact2->isblocked);
        $this->assertNull($contact2->unreadcount);

        $this->assertEquals($user4->id, $contact3->userid);
        $this->assertEmpty($contact3->useridfrom);
        $this->assertFalse($contact3->ismessaging);
        $this->assertNull($contact3->lastmessage);
        $this->assertNull($contact3->messageid);
        $this->assertNull($contact3->isonline);
        $this->assertFalse($contact3->isread);
        $this->assertFalse($contact3->isblocked);
        $this->assertNull($contact3->unreadcount);
    }

    /**
     * Tests retrieving messages.
     */
    public function test_get_messages() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = 1;
        $this->send_fake_message($user1, $user2, 'Yo!', 0, $time + 1);
        $this->send_fake_message($user2, $user1, 'Sup mang?', 0, $time + 2);
        $this->send_fake_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 3);
        $this->send_fake_message($user2, $user1, 'Word.', 0, $time + 4);

        // Retrieve the messages.
        $messages = \core_message\api::get_messages($user1->id, $user2->id);

        // Confirm the message data is correct.
        $this->assertEquals(4, count($messages));

        $message1 = $messages[0];
        $message2 = $messages[1];
        $message3 = $messages[2];
        $message4 = $messages[3];

        $this->assertEquals($user1->id, $message1->useridfrom);
        $this->assertEquals($user2->id, $message1->useridto);
        $this->assertTrue($message1->displayblocktime);
        $this->assertContains('Yo!', $message1->text);

        $this->assertEquals($user2->id, $message2->useridfrom);
        $this->assertEquals($user1->id, $message2->useridto);
        $this->assertFalse($message2->displayblocktime);
        $this->assertContains('Sup mang?', $message2->text);

        $this->assertEquals($user1->id, $message3->useridfrom);
        $this->assertEquals($user2->id, $message3->useridto);
        $this->assertFalse($message3->displayblocktime);
        $this->assertContains('Writing PHPUnit tests!', $message3->text);

        $this->assertEquals($user2->id, $message4->useridfrom);
        $this->assertEquals($user1->id, $message4->useridto);
        $this->assertFalse($message4->displayblocktime);
        $this->assertContains('Word.', $message4->text);
    }

    /**
     * Tests retrieving most recent message.
     */
    public function test_get_most_recent_message() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = 1;
        $this->send_fake_message($user1, $user2, 'Yo!', 0, $time + 1);
        $this->send_fake_message($user2, $user1, 'Sup mang?', 0, $time + 2);
        $this->send_fake_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 3);
        $this->send_fake_message($user2, $user1, 'Word.', 0, $time + 4);

        // Retrieve the most recent messages.
        $message = \core_message\api::get_most_recent_message($user1->id, $user2->id);

        // Check the results are correct.
        $this->assertEquals($user2->id, $message->useridfrom);
        $this->assertEquals($user1->id, $message->useridto);
        $this->assertContains('Word.', $message->text);
    }

    /**
     * Tests retrieving a user's profile.
     */
    public function test_get_profile() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();

        $user2 = new stdClass();
        $user2->country = 'AU';
        $user2->city = 'Perth';
        $user2 = self::getDataGenerator()->create_user($user2);

        // The person doing the search.
        $this->setUser($user1);

        // Get the profile.
        $profile = \core_message\api::get_profile($user1->id, $user2->id);

        $this->assertEquals($user2->id, $profile->userid);
        $this->assertEmpty($profile->email);
        $this->assertEmpty($profile->country);
        $this->assertEmpty($profile->city);
        $this->assertEquals(fullname($user2), $profile->fullname);
        $this->assertNull($profile->isonline);
        $this->assertFalse($profile->isblocked);
        $this->assertFalse($profile->iscontact);
    }

    /**
     * Tests retrieving a user's profile.
     */
    public function test_get_profile_as_admin() {
        // The person doing the search.
        $this->setAdminUser();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();

        $user2 = new stdClass();
        $user2->country = 'AU';
        $user2->city = 'Perth';
        $user2 = self::getDataGenerator()->create_user($user2);

        // Get the profile.
        $profile = \core_message\api::get_profile($user1->id, $user2->id);

        $this->assertEquals($user2->id, $profile->userid);
        $this->assertEquals($user2->email, $profile->email);
        $this->assertEquals($user2->country, $profile->country);
        $this->assertEquals($user2->city, $profile->city);
        $this->assertEquals(fullname($user2), $profile->fullname);
        $this->assertFalse($profile->isonline);
        $this->assertFalse($profile->isblocked);
        $this->assertFalse($profile->iscontact);
    }

    /**
     * Tests checking if a user can delete a conversation.
     */
    public function test_can_delete_conversation() {
        // Set as the admin.
        $this->setAdminUser();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The admin can do anything.
        $this->assertTrue(\core_message\api::can_delete_conversation($user1->id));

        // Set as the user 1.
        $this->setUser($user1);

        // They can delete their own messages.
        $this->assertTrue(\core_message\api::can_delete_conversation($user1->id));

        // They can't delete someone elses.
        $this->assertFalse(\core_message\api::can_delete_conversation($user2->id));
    }

    /**
     * Tests deleting a conversation.
     */
    public function test_delete_conversation() {
        global $DB;

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = 1;
        $this->send_fake_message($user1, $user2, 'Yo!', 0, $time + 1);
        $this->send_fake_message($user2, $user1, 'Sup mang?', 0, $time + 2);
        $this->send_fake_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 3);
        $this->send_fake_message($user2, $user1, 'Word.', 0, $time + 4);

        // Delete the conversation as user 1.
        \core_message\api::delete_conversation($user1->id, $user2->id);

        $messages = $DB->get_records('message', array(), 'timecreated ASC');
        $this->assertCount(4, $messages);

        $message1 = array_shift($messages);
        $message2 = array_shift($messages);
        $message3 = array_shift($messages);
        $message4 = array_shift($messages);

        $this->assertNotEmpty($message1->timeuserfromdeleted);
        $this->assertEmpty($message1->timeusertodeleted);

        $this->assertEmpty($message2->timeuserfromdeleted);
        $this->assertNotEmpty($message2->timeusertodeleted);

        $this->assertNotEmpty($message3->timeuserfromdeleted);
        $this->assertEmpty($message3->timeusertodeleted);

        $this->assertEmpty($message4->timeuserfromdeleted);
        $this->assertNotEmpty($message4->timeusertodeleted);

    }

    /**
     * Tests counting unread conversations.
     */
    public function test_count_unread_conversations() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // The person wanting the conversation count.
        $this->setUser($user1);

        // Send some messages back and forth, have some different conversations with different users.
        $this->send_fake_message($user1, $user2, 'Yo!');
        $this->send_fake_message($user2, $user1, 'Sup mang?');
        $this->send_fake_message($user1, $user2, 'Writing PHPUnit tests!');
        $this->send_fake_message($user2, $user1, 'Word.');

        $this->send_fake_message($user1, $user3, 'Booyah');
        $this->send_fake_message($user3, $user1, 'Whaaat?');
        $this->send_fake_message($user1, $user3, 'Nothing.');
        $this->send_fake_message($user3, $user1, 'Cool.');

        $this->send_fake_message($user1, $user4, 'Hey mate, you see the new messaging UI in Moodle?');
        $this->send_fake_message($user4, $user1, 'Yah brah, it\'s pretty rad.');
        $this->send_fake_message($user1, $user4, 'Dope.');

        // Check the amount for the current user.
        $this->assertEquals(3, core_message\api::count_unread_conversations());

        // Check the amount for the second user.
        $this->assertEquals(1, core_message\api::count_unread_conversations($user2));
    }

    /**
     * Tests deleting a conversation.
     */
    public function test_get_all_message_preferences() {
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);

        // Set a couple of preferences to test.
        set_user_preference('message_provider_mod_assign_assign_notification_loggedin', 'popup', $user);
        set_user_preference('message_provider_mod_assign_assign_notification_loggedoff', 'email', $user);

        $processors = get_message_processors();
        $providers = message_get_providers_for_user($user->id);
        $prefs = \core_message\api::get_all_message_preferences($processors, $providers, $user);

        $this->assertEquals(1, $prefs->mod_assign_assign_notification_loggedin['popup']);
        $this->assertEquals(1, $prefs->mod_assign_assign_notification_loggedoff['email']);
    }

    /**
     * Tests the user can post a message.
     */
    public function test_can_post_message() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Set as the user 1.
        $this->setUser($user1);

        // They can post to someone else.
        $this->assertTrue(\core_message\api::can_post_message($user2));
    }

    /**
     * Tests the user can't post a message without proper capability.
     */
    public function test_can_post_message_without_cap() {
        global $DB;

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Set as the user 1.
        $this->setUser($user1);

        // Remove the capability to send a message.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        unassign_capability('moodle/site:sendmessage', $roleids['user'],
            context_system::instance());

        // Check that we can not post a message without the capability.
        $this->assertFalse(\core_message\api::can_post_message($user2));
    }

    /**
     * Tests the user can't post a message if they are not a contact and the user
     * has requested messages only from contacts.
     */
    public function test_can_post_message_when_not_contact() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Set as the first user.
        $this->setUser($user1);

        // Set the second user's preference to not receive messages from non-contacts.
        set_user_preference('message_blocknoncontacts', 1, $user2->id);

        // Check that we can not send user 2 a message.
        $this->assertFalse(\core_message\api::can_post_message($user2));
    }

    /**
     * Tests the user can't post a message if they are blocked.
     */
    public function test_can_post_message_when_blocked() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Set the user.
        $this->setUser($user1);

        // Block the second user.
        message_block_contact($user2->id);

        // Check that the second user can no longer send the first user a message.
        $this->assertFalse(\core_message\api::can_post_message($user1, $user2));
    }

    /**
     * Tests that when blocking messages from non-contacts is enabled that
     * non-contacts trying to send a message return false.
     */
    public function test_is_user_non_contact_blocked() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Set as the first user.
        $this->setUser($user1);

        // User hasn't sent their preference to block non-contacts, so should return false.
        $this->assertFalse(\core_message\api::is_user_non_contact_blocked($user2));

        // Set the second user's preference to not receive messages from non-contacts.
        set_user_preference('message_blocknoncontacts', 1, $user2->id);

        // Check that the return result is now true.
        $this->assertTrue(\core_message\api::is_user_non_contact_blocked($user2));

        // Add the first user as a contact for the second user.
        message_add_contact($user1->id, 0, $user2->id);

        // Check that the return result is now false.
        $this->assertFalse(\core_message\api::is_user_non_contact_blocked($user2));
    }

    /**
     * Tests that we return true when a user is blocked, or false
     * if they are not blocked.
     */
    public function test_is_user_blocked() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Set the user.
        $this->setUser($user1);

        // User shouldn't be blocked.
        $this->assertFalse(\core_message\api::is_user_blocked($user1->id, $user2->id));

        // Block the user.
        message_block_contact($user2->id);

        // User should be blocked.
        $this->assertTrue(\core_message\api::is_user_blocked($user1->id, $user2->id));

        // Unblock the user.
        message_unblock_contact($user2->id);
        $this->assertFalse(\core_message\api::is_user_blocked($user1->id, $user2->id));
    }

    /**
     * Tests that the admin is not blocked even if someone has chosen to block them.
     */
    public function test_is_user_blocked_as_admin() {
        // Create a user.
        $user1 = self::getDataGenerator()->create_user();

        // Set the user.
        $this->setUser($user1);

        // Block the admin user.
        message_block_contact(2);

        // Now change to the admin user.
        $this->setAdminUser();

        // As the admin you should still be able to send messages to the user.
        $this->assertFalse(\core_message\api::is_user_blocked($user1->id));
    }

    /*
     * Tes get_message_processor api.
     */
    public function test_get_message_processor() {
        $processors = get_message_processors(true);
        if (empty($processors)) {
            $this->markTestSkipped("No message processors found");
        }

        list($name, $processor) = each($processors);
        $testprocessor = \core_message\api::get_message_processor($name);
        $this->assertEquals($processor->name, $testprocessor->name);
        $this->assertEquals($processor->enabled, $testprocessor->enabled);
        $this->assertEquals($processor->available, $testprocessor->available);
        $this->assertEquals($processor->configured, $testprocessor->configured);

        // Disable processor and test.
        \core_message\api::update_processor_status($testprocessor, 0);
        $testprocessor = \core_message\api::get_message_processor($name, true);
        $this->assertEmpty($testprocessor);
        $testprocessor = \core_message\api::get_message_processor($name);
        $this->assertEquals($processor->name, $testprocessor->name);
        $this->assertEquals(0, $testprocessor->enabled);

        // Enable again and test.
        \core_message\api::update_processor_status($testprocessor, 1);
        $testprocessor = \core_message\api::get_message_processor($name, true);
        $this->assertEquals($processor->name, $testprocessor->name);
        $this->assertEquals(1, $testprocessor->enabled);
        $testprocessor = \core_message\api::get_message_processor($name);
        $this->assertEquals($processor->name, $testprocessor->name);
        $this->assertEquals(1, $testprocessor->enabled);
    }

    /**
     * Test method update_processor_status.
     */
    public function test_update_processor_status() {
        $processors = get_message_processors();
        if (empty($processors)) {
            $this->markTestSkipped("No message processors found");
        }
        list($name, $testprocessor) = each($processors);

        // Enable.
        \core_message\api::update_processor_status($testprocessor, 1);
        $testprocessor = \core_message\api::get_message_processor($name);
        $this->assertEquals(1, $testprocessor->enabled);

        // Disable.
        \core_message\api::update_processor_status($testprocessor, 0);
        $testprocessor = \core_message\api::get_message_processor($name);
        $this->assertEquals(0, $testprocessor->enabled);

        // Enable again.
        \core_message\api::update_processor_status($testprocessor, 1);
        $testprocessor = \core_message\api::get_message_processor($name);
        $this->assertEquals(1, $testprocessor->enabled);
    }

    /**
     * Test method is_user_enabled.
     */
    public function is_user_enabled() {
        $processors = get_message_processors();
        if (empty($processors)) {
            $this->markTestSkipped("No message processors found");
        }
        list($name, $testprocessor) = each($processors);

        // Enable.
        \core_message\api::update_processor_status($testprocessor, 1);
        $status = \core_message\api::is_processor_enabled($name);
        $this->assertEquals(1, $status);

        // Disable.
        \core_message\api::update_processor_status($testprocessor, 0);
        $status = \core_message\api::is_processor_enabled($name);
        $this->assertEquals(0, $status);

        // Enable again.
        \core_message\api::update_processor_status($testprocessor, 1);
        $status = \core_message\api::is_processor_enabled($name);
        $this->assertEquals(1, $status);
    }

    /**
     * Test retrieving messages by providing a minimum timecreated value.
     */
    public function test_get_messages_time_from_only() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = 1;
        $this->send_fake_message($user1, $user2, 'Message 1', 0, $time + 1);
        $this->send_fake_message($user2, $user1, 'Message 2', 0, $time + 2);
        $this->send_fake_message($user1, $user2, 'Message 3', 0, $time + 3);
        $this->send_fake_message($user2, $user1, 'Message 4', 0, $time + 4);

        // Retrieve the messages from $time, which should be all of them.
        $messages = \core_message\api::get_messages($user1->id, $user2->id, 0, 0, 'timecreated ASC', $time);

        // Confirm the message data is correct.
        $this->assertEquals(4, count($messages));

        $message1 = $messages[0];
        $message2 = $messages[1];
        $message3 = $messages[2];
        $message4 = $messages[3];

        $this->assertContains('Message 1', $message1->text);
        $this->assertContains('Message 2', $message2->text);
        $this->assertContains('Message 3', $message3->text);
        $this->assertContains('Message 4', $message4->text);

        // Retrieve the messages from $time + 3, which should only be the 2 last messages.
        $messages = \core_message\api::get_messages($user1->id, $user2->id, 0, 0, 'timecreated ASC', $time + 3);

        // Confirm the message data is correct.
        $this->assertEquals(2, count($messages));

        $message1 = $messages[0];
        $message2 = $messages[1];

        $this->assertContains('Message 3', $message1->text);
        $this->assertContains('Message 4', $message2->text);
    }

    /**
     * Test retrieving messages by providing a maximum timecreated value.
     */
    public function test_get_messages_time_to_only() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = 1;
        $this->send_fake_message($user1, $user2, 'Message 1', 0, $time + 1);
        $this->send_fake_message($user2, $user1, 'Message 2', 0, $time + 2);
        $this->send_fake_message($user1, $user2, 'Message 3', 0, $time + 3);
        $this->send_fake_message($user2, $user1, 'Message 4', 0, $time + 4);

        // Retrieve the messages up until $time + 4, which should be all of them.
        $messages = \core_message\api::get_messages($user1->id, $user2->id, 0, 0, 'timecreated ASC', 0, $time + 4);

        // Confirm the message data is correct.
        $this->assertEquals(4, count($messages));

        $message1 = $messages[0];
        $message2 = $messages[1];
        $message3 = $messages[2];
        $message4 = $messages[3];

        $this->assertContains('Message 1', $message1->text);
        $this->assertContains('Message 2', $message2->text);
        $this->assertContains('Message 3', $message3->text);
        $this->assertContains('Message 4', $message4->text);

        // Retrieve the messages up until $time + 2, which should be the first two.
        $messages = \core_message\api::get_messages($user1->id, $user2->id, 0, 0, 'timecreated ASC', 0, $time + 2);

        // Confirm the message data is correct.
        $this->assertEquals(2, count($messages));

        $message1 = $messages[0];
        $message2 = $messages[1];

        $this->assertContains('Message 1', $message1->text);
        $this->assertContains('Message 2', $message2->text);
    }

    /**
     * Test retrieving messages by providing a minimum and maximum timecreated value.
     */
    public function test_get_messages_time_from_and_to() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = 1;
        $this->send_fake_message($user1, $user2, 'Message 1', 0, $time + 1);
        $this->send_fake_message($user2, $user1, 'Message 2', 0, $time + 2);
        $this->send_fake_message($user1, $user2, 'Message 3', 0, $time + 3);
        $this->send_fake_message($user2, $user1, 'Message 4', 0, $time + 4);

        // Retrieve the messages from $time + 2 up until $time + 3, which should be 2nd and 3rd message.
        $messages = \core_message\api::get_messages($user1->id, $user2->id, 0, 0, 'timecreated ASC', $time + 2, $time + 3);

        // Confirm the message data is correct.
        $this->assertEquals(2, count($messages));

        $message1 = $messages[0];
        $message2 = $messages[1];

        $this->assertContains('Message 2', $message1->text);
        $this->assertContains('Message 3', $message2->text);
    }
}
