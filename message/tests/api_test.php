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

    public function test_message_mark_all_read_for_user_touser() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->send_fake_unread_popup_notification($sender, $recipient);
        $this->send_fake_unread_popup_notification($sender, $recipient);
        $this->send_fake_unread_popup_notification($sender, $recipient);
        $this->send_fake_message($sender, $recipient);
        $this->send_fake_message($sender, $recipient);
        $this->send_fake_message($sender, $recipient);

        \core_message\api::mark_all_read_for_user($recipient->id);
        $this->assertEquals(message_count_unread_messages($recipient), 0);
    }

    public function test_message_mark_all_read_for_user_touser_with_fromuser() {
        $sender1 = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $sender2 = $this->getDataGenerator()->create_user(array('firstname' => 'Test3', 'lastname' => 'User3'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->send_fake_unread_popup_notification($sender1, $recipient);
        $this->send_fake_unread_popup_notification($sender1, $recipient);
        $this->send_fake_unread_popup_notification($sender1, $recipient);
        $this->send_fake_message($sender1, $recipient);
        $this->send_fake_message($sender1, $recipient);
        $this->send_fake_message($sender1, $recipient);
        $this->send_fake_unread_popup_notification($sender2, $recipient);
        $this->send_fake_unread_popup_notification($sender2, $recipient);
        $this->send_fake_unread_popup_notification($sender2, $recipient);
        $this->send_fake_message($sender2, $recipient);
        $this->send_fake_message($sender2, $recipient);
        $this->send_fake_message($sender2, $recipient);

        \core_message\api::mark_all_read_for_user($recipient->id, $sender1->id);
        $this->assertEquals(message_count_unread_messages($recipient), 6);
    }

    public function test_message_mark_all_read_for_user_touser_with_type() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->send_fake_unread_popup_notification($sender, $recipient);
        $this->send_fake_unread_popup_notification($sender, $recipient);
        $this->send_fake_unread_popup_notification($sender, $recipient);
        $this->send_fake_message($sender, $recipient);
        $this->send_fake_message($sender, $recipient);
        $this->send_fake_message($sender, $recipient);

        \core_message\api::mark_all_read_for_user($recipient->id, 0, MESSAGE_TYPE_NOTIFICATION);
        $this->assertEquals(message_count_unread_messages($recipient), 3);

        \core_message\api::mark_all_read_for_user($recipient->id, 0, MESSAGE_TYPE_MESSAGE);
        $this->assertEquals(message_count_unread_messages($recipient), 0);
    }

    /**
     * Test that the get_popup_notifications function will return the correct notifications.
     */
    public function test_message_get_popup_notifications() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->send_fake_read_popup_notification($sender, $recipient, 'Message 1', 1);
        $this->send_fake_unread_popup_notification($sender, $recipient, 'Message 2', 2);
        $this->send_fake_read_popup_notification($sender, $recipient, 'Message 3', 3, 1);
        $this->send_fake_read_popup_notification($sender, $recipient, 'Message 4', 3, 2);
        $this->send_fake_unread_popup_notification($sender, $recipient, 'Message 5', 4);

        $notifications = \core_message\api::get_popup_notifications($recipient->id);

        $this->assertEquals($notifications[0]->fullmessage, 'Message 5');
        $this->assertEquals($notifications[1]->fullmessage, 'Message 4');
        $this->assertEquals($notifications[2]->fullmessage, 'Message 3');
        $this->assertEquals($notifications[3]->fullmessage, 'Message 2');
        $this->assertEquals($notifications[4]->fullmessage, 'Message 1');
    }

    /**
     * Test that the get_popup_notifications function works correctly with limiting and offsetting
     * the result set if requested.
     */
    public function test_message_get_popup_notifications_all_limit_and_offset() {
        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));

        $this->send_fake_read_popup_notification($sender, $recipient, 'Message 1', 1);
        $this->send_fake_unread_popup_notification($sender, $recipient, 'Message 2', 2);
        $this->send_fake_read_popup_notification($sender, $recipient, 'Message 3', 3, 1);
        $this->send_fake_read_popup_notification($sender, $recipient, 'Message 4', 3, 2);
        $this->send_fake_unread_popup_notification($sender, $recipient, 'Message 5', 4);
        $this->send_fake_unread_popup_notification($sender, $recipient, 'Message 6', 5);

        $notifications = \core_message\api::get_popup_notifications($recipient->id, 'DESC', 2, 0);

        $this->assertEquals($notifications[0]->fullmessage, 'Message 6');
        $this->assertEquals($notifications[1]->fullmessage, 'Message 5');

        $notifications = \core_message\api::get_popup_notifications($recipient->id, 'DESC', 2, 2);

        $this->assertEquals($notifications[0]->fullmessage, 'Message 4');
        $this->assertEquals($notifications[1]->fullmessage, 'Message 3');

        $notifications = \core_message\api::get_popup_notifications($recipient->id, 'DESC', 0, 3);

        $this->assertEquals($notifications[0]->fullmessage, 'Message 3');
        $this->assertEquals($notifications[1]->fullmessage, 'Message 2');
        $this->assertEquals($notifications[2]->fullmessage, 'Message 1');
    }

    /**
     * Test count_unread_popup_notifications.
     */
    public function test_message_count_unread_popup_notifications() {
        $sender1 = $this->getDataGenerator()->create_user(array('firstname' => 'Test1', 'lastname' => 'User1'));
        $sender2 = $this->getDataGenerator()->create_user(array('firstname' => 'Test2', 'lastname' => 'User2'));
        $recipient1 = $this->getDataGenerator()->create_user(array('firstname' => 'Test3', 'lastname' => 'User3'));
        $recipient2 = $this->getDataGenerator()->create_user(array('firstname' => 'Test4', 'lastname' => 'User4'));

        $this->send_fake_unread_popup_notification($sender1, $recipient1);
        $this->send_fake_unread_popup_notification($sender1, $recipient1);
        $this->send_fake_unread_popup_notification($sender2, $recipient1);
        $this->send_fake_unread_popup_notification($sender1, $recipient2);
        $this->send_fake_unread_popup_notification($sender2, $recipient2);
        $this->send_fake_unread_popup_notification($sender2, $recipient2);
        $this->send_fake_unread_popup_notification($sender2, $recipient2);
        $this->send_fake_unread_popup_notification($sender2, $recipient2);

        $this->assertEquals(\core_message\api::count_unread_popup_notifications($recipient1->id), 3);
        $this->assertEquals(\core_message\api::count_unread_popup_notifications($recipient2->id), 5);
    }

    /**
     * Test count_blocked_users.
     *
     */
    public function test_message_count_blocked_users() {
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
}
