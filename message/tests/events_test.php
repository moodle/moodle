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
 * Events tests.
 *
 * @package core_message
 * @category test
 * @copyright 2014 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/message/tests/messagelib_test.php');

/**
 * Class containing the tests for message related events.
 *
 * @package core_message
 * @category test
 * @copyright 2014 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_message_events_testcase extends core_message_messagelib_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Test the message contact added event.
     */
    public function test_message_contact_added() {
        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user = $this->getDataGenerator()->create_user();

        // Trigger and capture the event when adding a contact.
        $sink = $this->redirectEvents();
        message_add_contact($user->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\message_contact_added', $event);
        $this->assertEquals(context_user::instance(2), $event->get_context());
        $expected = array(SITEID, 'message', 'add contact', 'index.php?user1=' . $user->id .
            '&amp;user2=2', $user->id);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new moodle_url('/message/index.php', array('user1' => $event->userid, 'user2' => $event->relateduserid));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the message contact removed event.
     */
    public function test_message_contact_removed() {
        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user = $this->getDataGenerator()->create_user();

        // Add the user to the admin's contact list.
        message_add_contact($user->id);

        // Trigger and capture the event when adding a contact.
        $sink = $this->redirectEvents();
        message_remove_contact($user->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\message_contact_removed', $event);
        $this->assertEquals(context_user::instance(2), $event->get_context());
        $expected = array(SITEID, 'message', 'remove contact', 'index.php?user1=' . $user->id .
            '&amp;user2=2', $user->id);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new moodle_url('/message/index.php', array('user1' => $event->userid, 'user2' => $event->relateduserid));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the message contact blocked event.
     */
    public function test_message_contact_blocked() {
        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Add the user to the admin's contact list.
        message_add_contact($user->id);

        // Trigger and capture the event when blocking a contact.
        $sink = $this->redirectEvents();
        message_block_contact($user->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\message_contact_blocked', $event);
        $this->assertEquals(context_user::instance(2), $event->get_context());
        $expected = array(SITEID, 'message', 'block contact', 'index.php?user1=' . $user->id . '&amp;user2=2', $user->id);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new moodle_url('/message/index.php', array('user1' => $event->userid, 'user2' => $event->relateduserid));
        $this->assertEquals($url, $event->get_url());

        // Make sure that the contact blocked event is not triggered again.
        $sink->clear();
        message_block_contact($user->id);
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertEmpty($event);
        // Make sure that we still have 1 blocked user.
        $this->assertEquals(1, \core_message\api::count_blocked_users());

        // Now blocking a user that is not a contact.
        $sink->clear();
        message_block_contact($user2->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\message_contact_blocked', $event);
        $this->assertEquals(context_user::instance(2), $event->get_context());
        $expected = array(SITEID, 'message', 'block contact', 'index.php?user1=' . $user2->id . '&amp;user2=2', $user2->id);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new moodle_url('/message/index.php', array('user1' => $event->userid, 'user2' => $event->relateduserid));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the message contact unblocked event.
     */
    public function test_message_contact_unblocked() {
        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user = $this->getDataGenerator()->create_user();

        // Add the user to the admin's contact list.
        message_add_contact($user->id);

        // Block the user.
        message_block_contact($user->id);
        // Make sure that we have 1 blocked user.
        $this->assertEquals(1, \core_message\api::count_blocked_users());

        // Trigger and capture the event when unblocking a contact.
        $sink = $this->redirectEvents();
        message_unblock_contact($user->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\message_contact_unblocked', $event);
        $this->assertEquals(context_user::instance(2), $event->get_context());
        $expected = array(SITEID, 'message', 'unblock contact', 'index.php?user1=' . $user->id . '&amp;user2=2', $user->id);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new moodle_url('/message/index.php', array('user1' => $event->userid, 'user2' => $event->relateduserid));
        $this->assertEquals($url, $event->get_url());

        // Make sure that we have no blocked users.
        $this->assertEmpty(\core_message\api::count_blocked_users());

        // Make sure that the contact unblocked event is not triggered again.
        $sink->clear();
        message_unblock_contact($user->id);
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertEmpty($event);

        // Make sure that we still have no blocked users.
        $this->assertEmpty(\core_message\api::count_blocked_users());
    }

    /**
     * Test the message sent event.
     *
     * We can not use the message_send() function in the unit test to check that the event was fired as there is a
     * conditional check to ensure a fake message is sent during unit tests when calling that particular function.
     */
    public function test_message_sent() {
        $event = \core\event\message_sent::create(array(
            'objectid' => 3,
            'userid' => 1,
            'context'  => context_system::instance(),
            'relateduserid' => 2,
            'other' => array(
                'courseid' => 4
            )
        ));

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\message_sent', $event);
        $this->assertEquals(context_system::instance(), $event->get_context());
        $expected = array(SITEID, 'message', 'write', 'index.php?user=1&id=2&history=1#m3', 1);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new moodle_url('/message/index.php', array('user1' => $event->userid, 'user2' => $event->relateduserid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals(3, $event->objectid);
        $this->assertEquals(4, $event->other['courseid']);
    }

    public function test_mesage_sent_without_other_courseid() {

        // Creating a message_sent event without other[courseid] leads to exception.
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('The \'courseid\' value must be set in other');

        $event = \core\event\message_sent::create(array(
            'userid' => 1,
            'context'  => context_system::instance(),
            'relateduserid' => 2,
            'other' => array(
                'messageid' => 3,
            )
        ));
    }

    public function test_mesage_sent_via_create_from_ids() {
        // Containing courseid.
        $event = \core\event\message_sent::create_from_ids(1, 2, 3, 4);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\message_sent', $event);
        $this->assertEquals(context_system::instance(), $event->get_context());
        $expected = array(SITEID, 'message', 'write', 'index.php?user=1&id=2&history=1#m3', 1);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new moodle_url('/message/index.php', array('user1' => $event->userid, 'user2' => $event->relateduserid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals(3, $event->objectid);
        $this->assertEquals(4, $event->other['courseid']);
    }

    public function test_mesage_sent_via_create_from_ids_without_other_courseid() {

        // Creating a message_sent event without courseid leads to debugging + SITEID.
        // TODO: MDL-55449 Ensure this leads to exception instead of debugging in Moodle 3.6.
        $event = \core\event\message_sent::create_from_ids(1, 2, 3);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertDebuggingCalled();
        $this->assertEquals(SITEID, $event->other['courseid']);
    }

    /**
     * Test the message viewed event.
     */
    public function test_message_viewed() {
        global $DB;

        // Create users to send messages between.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $messageid = $this->send_fake_message($user1, $user2);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $message = $DB->get_record('messages', ['id' => $messageid]);
        \core_message\api::mark_message_as_read($user1->id, $message);
        $events = $sink->get_events();
        $event = reset($events);

        // Get the usage action.
        $mua = $DB->get_record('message_user_actions', ['userid' => $user1->id, 'messageid' => $messageid,
            'action' => \core_message\api::MESSAGE_ACTION_READ]);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\message_viewed', $event);
        $this->assertEquals(context_user::instance($user1->id), $event->get_context());
        $this->assertEquals($mua->id, $event->objectid);
        $this->assertEquals($messageid, $event->other['messageid']);
        $url = new moodle_url('/message/index.php', array('user1' => $event->userid, 'user2' => $event->relateduserid));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the message deleted event.
     */
    public function test_message_deleted() {
        global $DB;

        // Create users to send messages between.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $messageid = $this->send_fake_message($user1, $user2);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        \core_message\api::delete_message($user1->id, $messageid);
        $events = $sink->get_events();
        $event = reset($events);

        // Get the usage action.
        $mua = $DB->get_record('message_user_actions', ['userid' => $user1->id, 'messageid' => $messageid,
            'action' => \core_message\api::MESSAGE_ACTION_DELETED]);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\message_deleted', $event);
        $this->assertEquals($user1->id, $event->userid); // The user who deleted it.
        $this->assertEquals($user2->id, $event->relateduserid);
        $this->assertEquals($mua->id, $event->objectid);
        $this->assertEquals($messageid, $event->other['messageid']);
        $this->assertEquals($user1->id, $event->other['useridfrom']);
        $this->assertEquals($user2->id, $event->other['useridto']);

        // Create a read message.
        $messageid = $this->send_fake_message($user1, $user2);
        $m = $DB->get_record('messages', ['id' => $messageid]);
        \core_message\api::mark_message_as_read($user2->id, $m);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        \core_message\api::delete_message($user2->id, $messageid);
        $events = $sink->get_events();
        $event = reset($events);

        // Get the usage action.
        $mua = $DB->get_record('message_user_actions', ['userid' => $user2->id, 'messageid' => $messageid,
            'action' => \core_message\api::MESSAGE_ACTION_DELETED]);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\message_deleted', $event);
        $this->assertEquals($user2->id, $event->userid);
        $this->assertEquals($user1->id, $event->relateduserid);
        $this->assertEquals($mua->id, $event->objectid);
        $this->assertEquals($messageid, $event->other['messageid']);
        $this->assertEquals($user1->id, $event->other['useridfrom']);
        $this->assertEquals($user2->id, $event->other['useridto']);
    }

    /**
     * Test the message deleted event is fired when deleting a conversation.
     */
    public function test_message_deleted_whole_conversation() {
        global $DB;

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = 1;
        $messages = [];
        $messages[] = $this->send_fake_message($user1, $user2, 'Yo!', 0, $time + 1);
        $messages[] = $this->send_fake_message($user2, $user1, 'Sup mang?', 0, $time + 2);
        $messages[] = $this->send_fake_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 3);
        $messages[] = $this->send_fake_message($user2, $user1, 'Word.', 0, $time + 4);
        $messages[] = $this->send_fake_message($user1, $user2, 'You doing much?', 0, $time + 5);
        $messages[] = $this->send_fake_message($user2, $user1, 'Nah', 0, $time + 6);
        $messages[] = $this->send_fake_message($user1, $user2, 'You nubz0r!', 0, $time + 7);
        $messages[] = $this->send_fake_message($user2, $user1, 'Ouch.', 0, $time + 8);

        // Mark the last 4 messages as read.
        $m5 = $DB->get_record('messages', ['id' => $messages[4]]);
        $m6 = $DB->get_record('messages', ['id' => $messages[5]]);
        $m7 = $DB->get_record('messages', ['id' => $messages[6]]);
        $m8 = $DB->get_record('messages', ['id' => $messages[7]]);
        \core_message\api::mark_message_as_read($user2->id, $m5);
        \core_message\api::mark_message_as_read($user1->id, $m6);
        \core_message\api::mark_message_as_read($user2->id, $m7);
        \core_message\api::mark_message_as_read($user1->id, $m8);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        \core_message\api::delete_conversation($user1->id, $user2->id);
        $events = $sink->get_events();

        // Get the user actions for the messages deleted by that user.
        $muas = $DB->get_records('message_user_actions', ['userid' => $user1->id,
            'action' => \core_message\api::MESSAGE_ACTION_DELETED], 'timecreated ASC');
        $this->assertCount(8, $muas);

        // Create a list we can use for testing.
        $muatest = [];
        foreach ($muas as $mua) {
            $muatest[$mua->messageid] = $mua;
        }

        // Check that there were the correct number of events triggered.
        $this->assertEquals(8, count($events));

        // Check that the event data is valid.
        $i = 1;
        foreach ($events as $event) {
            $useridfromid = ($i % 2 == 0) ? $user2->id : $user1->id;
            $useridtoid = ($i % 2 == 0) ? $user1->id : $user2->id;
            $messageid = $messages[$i - 1];

            $this->assertInstanceOf('\core\event\message_deleted', $event);

            $this->assertEquals($muatest[$messageid]->id, $event->objectid);
            $this->assertEquals($user1->id, $event->userid);
            $this->assertEquals($user2->id, $event->relateduserid);
            $this->assertEquals($messageid, $event->other['messageid']);
            $this->assertEquals($useridfromid, $event->other['useridfrom']);
            $this->assertEquals($useridtoid, $event->other['useridto']);

            $i++;
        }
    }

    /**
     * Test the notification sent event.
     */
    public function test_notification_sent() {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create users to send notification between.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Send a notification.
        $notificationid = $this->send_fake_message($user1, $user2, 'Hello world!', 1);

        // Containing courseid.
        $event = \core\event\notification_sent::create_from_ids($user1->id, $user2->id, $notificationid, $course->id);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\notification_sent', $event);
        $this->assertEquals($notificationid, $event->objectid);
        $this->assertEquals($user1->id, $event->userid);
        $this->assertEquals($user2->id, $event->relateduserid);
        $this->assertEquals(context_system::instance(), $event->get_context());
        $this->assertEquals($course->id, $event->other['courseid']);
        $url = new moodle_url('/message/output/popup/notifications.php', array('notificationid' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the notification sent event when null passed as course.
     */
    public function test_notification_sent_with_null_course() {
        $event = \core\event\notification_sent::create_from_ids(1, 1, 1, null);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\notification_sent', $event);
        $this->assertEquals(SITEID, $event->other['courseid']);
    }

    /**
     * Test the notification viewed event.
     */
    public function test_notification_viewed() {
        global $DB;

        // Create users to send notifications between.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Send a notification.
        $notificationid = $this->send_fake_message($user1, $user2, 'Hello world!', 1);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $notification = $DB->get_record('notifications', ['id' => $notificationid]);
        \core_message\api::mark_notification_as_read($notification);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\notification_viewed', $event);
        $this->assertEquals($notificationid, $event->objectid);
        $this->assertEquals($user2->id, $event->userid);
        $this->assertEquals($user1->id, $event->relateduserid);
        $this->assertEquals(context_user::instance($user2->id), $event->get_context());
        $url = new moodle_url('/message/output/popup/notifications.php', array('notificationid' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
    }
}
