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
 * Test message popup API.
 *
 * @package message_popup
 * @category test
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/message/tests/messagelib_test.php');
require_once($CFG->dirroot . '/message/output/popup/tests/base.php');

/**
 * Test message popup API.
 *
 * @package message_popup
 * @category test
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_popup_api_testcase extends advanced_testcase {
    use message_popup_test_helper;

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

        $notifications = \message_popup\api::get_popup_notifications($recipient->id);

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

        $notifications = \message_popup\api::get_popup_notifications($recipient->id, 'DESC', 2, 0);

        $this->assertEquals($notifications[0]->fullmessage, 'Message 6');
        $this->assertEquals($notifications[1]->fullmessage, 'Message 5');

        $notifications = \message_popup\api::get_popup_notifications($recipient->id, 'DESC', 2, 2);

        $this->assertEquals($notifications[0]->fullmessage, 'Message 4');
        $this->assertEquals($notifications[1]->fullmessage, 'Message 3');

        $notifications = \message_popup\api::get_popup_notifications($recipient->id, 'DESC', 0, 3);

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

        $this->assertEquals(\message_popup\api::count_unread_popup_notifications($recipient1->id), 3);
        $this->assertEquals(\message_popup\api::count_unread_popup_notifications($recipient2->id), 5);
    }
}
