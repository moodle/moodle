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
    }

    /**
     * Test the message contact blocked event.
     */
    public function test_message_contact_blocked() {
        // Set this user as the admin.
        $this->setAdminUser();

        // Create a user to add to the admin's contact list.
        $user = $this->getDataGenerator()->create_user();

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
    }
}
