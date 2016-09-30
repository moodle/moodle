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

        $this->send_fake_message($sender, $recipient, 'Notification', 1);
        $this->send_fake_message($sender, $recipient, 'Notification', 1);
        $this->send_fake_message($sender, $recipient, 'Notification', 1);
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
        $this->assertEquals(message_count_unread_messages($recipient), 6);
    }

    public function test_message_mark_all_read_for_user_touser_with_type() {
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
