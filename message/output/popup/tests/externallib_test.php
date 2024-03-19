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

namespace message_popup;

use message_popup_external;
use message_popup_test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/message/output/popup/externallib.php');
require_once($CFG->dirroot . '/message/output/popup/tests/base.php');

/**
 * Class for external message popup functions unit tests.
 *
 * @package    message_popup
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class externallib_test extends \advanced_testcase {
    use message_popup_test_helper;

    /** @var \phpunit_message_sink message redirection. */
    public $messagesink;

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
     * Test that get_popup_notifications throws an exception if the user
     * doesn't exist.
     */
    public function test_get_popup_notifications_no_user_exception() {
        $this->resetAfterTest(true);

        $this->expectException('moodle_exception');
        $result = message_popup_external::get_popup_notifications(-2132131, false, 0, 0);
    }

    /**
     * get_popup_notifications should throw exception if user isn't logged in
     * user.
     */
    public function test_get_popup_notifications_access_denied_exception() {
        $this->resetAfterTest(true);

        $sender = $this->getDataGenerator()->create_user();
        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);
        $this->expectException('moodle_exception');
        $result = message_popup_external::get_popup_notifications($sender->id, false, 0, 0);
    }

    /**
     * get_popup_notifications should return notifications for the recipient.
     */
    public function test_get_popup_notifications_as_recipient() {
        $this->resetAfterTest(true);

        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Sendy', 'lastname' => 'Sender'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Recipy', 'lastname' => 'Recipient'));

        $notificationids = array(
            $this->send_fake_unread_popup_notification($sender, $recipient),
            $this->send_fake_unread_popup_notification($sender, $recipient),
            $this->send_fake_read_popup_notification($sender, $recipient),
            $this->send_fake_read_popup_notification($sender, $recipient),
        );

        // Confirm that admin has super powers to retrieve any notifications.
        $this->setAdminUser();
        $result = message_popup_external::get_popup_notifications($recipient->id, false, 0, 0);
        $this->assertCount(4, $result['notifications']);
        // Check we receive custom data as a unserialisable json.
        $found = 0;
        foreach ($result['notifications'] as $notification) {
            if (!empty($notification->customdata)) {
                $this->assertObjectHasProperty('datakey', json_decode($notification->customdata));
                $found++;
            }
        }
        $this->assertEquals(2, $found);

        $this->setUser($recipient);
        $result = message_popup_external::get_popup_notifications($recipient->id, false, 0, 0);
        $this->assertCount(4, $result['notifications']);
    }

    /**
     * get_popup_notifications result set should work with limit and offset.
     */
    public function test_get_popup_notification_limit_offset() {
        $this->resetAfterTest(true);

        $sender = $this->getDataGenerator()->create_user(array('firstname' => 'Sendy', 'lastname' => 'Sender'));
        $recipient = $this->getDataGenerator()->create_user(array('firstname' => 'Recipy', 'lastname' => 'Recipient'));

        $this->setUser($recipient);

        $notificationids = array(
            $this->send_fake_unread_popup_notification($sender, $recipient, 'Notification 1', 1),
            $this->send_fake_unread_popup_notification($sender, $recipient, 'Notification 2', 2),
            $this->send_fake_unread_popup_notification($sender, $recipient, 'Notification 3', 3),
            $this->send_fake_unread_popup_notification($sender, $recipient, 'Notification 4', 4),
            $this->send_fake_read_popup_notification($sender, $recipient, 'Notification 5', 5),
            $this->send_fake_read_popup_notification($sender, $recipient, 'Notification 6', 6),
            $this->send_fake_read_popup_notification($sender, $recipient, 'Notification 7', 7),
            $this->send_fake_read_popup_notification($sender, $recipient, 'Notification 8', 8),
        );

        $result = message_popup_external::get_popup_notifications($recipient->id, true, 2, 0);

        $this->assertEquals($result['notifications'][0]->id, $notificationids[7]);
        $this->assertEquals($result['notifications'][1]->id, $notificationids[6]);

        $result = message_popup_external::get_popup_notifications($recipient->id, true, 2, 2);

        $this->assertEquals($result['notifications'][0]->id, $notificationids[5]);
        $this->assertEquals($result['notifications'][1]->id, $notificationids[4]);
    }

    /**
     * get_unread_popup_notification should throw an exception for an invalid user.
     */
    public function test_get_unread_popup_notification_count_invalid_user_exception() {
        $this->resetAfterTest(true);

        $this->expectException('moodle_exception');
        $result = message_popup_external::get_unread_popup_notification_count(-2132131, 0);
    }

    /**
     * get_unread_popup_notification_count should throw exception if being requested for
     * non-logged in user.
     */
    public function test_get_unread_popup_notification_count_access_denied_exception() {
        $this->resetAfterTest(true);

        $sender = $this->getDataGenerator()->create_user();
        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);
        $this->expectException('moodle_exception');
        $result = message_popup_external::get_unread_popup_notification_count($sender->id, 0);
    }

    /**
     * Test get_unread_popup_notification_count.
     */
    public function test_get_unread_popup_notification_count() {
        $this->resetAfterTest(true);

        $sender1 = $this->getDataGenerator()->create_user();
        $sender2 = $this->getDataGenerator()->create_user();
        $sender3 = $this->getDataGenerator()->create_user();
        $recipient = $this->getDataGenerator()->create_user();

        $this->setUser($recipient);

        $notificationids = array(
            $this->send_fake_unread_popup_notification($sender1, $recipient, 'Notification', 1),
            $this->send_fake_unread_popup_notification($sender1, $recipient, 'Notification', 2),
            $this->send_fake_unread_popup_notification($sender2, $recipient, 'Notification', 3),
            $this->send_fake_unread_popup_notification($sender2, $recipient, 'Notification', 4),
            $this->send_fake_unread_popup_notification($sender3, $recipient, 'Notification', 5),
            $this->send_fake_unread_popup_notification($sender3, $recipient, 'Notification', 6),
        );

        $count = message_popup_external::get_unread_popup_notification_count($recipient->id);
        $this->assertEquals($count, 6);
    }
}
