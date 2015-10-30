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

class core_message_events_testcase extends advanced_testcase {

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
    }

    /**
     * Test the message sent event.
     *
     * We can not use the message_send() function in the unit test to check that the event was fired as there is a
     * conditional check to ensure a fake message is sent during unit tests when calling that particular function.
     */
    public function test_message_sent() {
        $event = \core\event\message_sent::create(array(
            'userid' => 1,
            'context'  => context_system::instance(),
            'relateduserid' => 2,
            'other' => array(
                'messageid' => 3
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
    }

    /**
     * Test the message viewed event.
     */
    public function test_message_viewed() {
        global $DB;

        // Create a message to mark as read.
        $message = new stdClass();
        $message->useridfrom = '1';
        $message->useridto = '2';
        $message->subject = 'Subject';
        $message->message = 'Message';
        $message->id = $DB->insert_record('message', $message);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        message_mark_message_read($message, time());
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\message_viewed', $event);
        $this->assertEquals(context_user::instance(2), $event->get_context());
        $url = new moodle_url('/message/index.php', array('user1' => $event->userid, 'user2' => $event->relateduserid));
        $this->assertEquals($url, $event->get_url());
    }
}
