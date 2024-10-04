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

namespace core\event;

/**
 * Tests for event \core\event\user_password_updated
 *
 * @package    core
 * @category   test
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class user_password_updated_test extends \advanced_testcase {
    /**
     * Test the event.
     */
    public function test_event(): void {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $user2 = $this->getDataGenerator()->create_user();
        $context2 = \context_user::instance($user2->id);

        $this->setUser($user1);

        // Changing own password.
        $event = \core\event\user_password_updated::create_from_user($user1);
        $this->assertEventContextNotUsed($event);
        $this->assertEquals($user1->id, $event->relateduserid);
        $this->assertSame($context1, $event->get_context());
        $this->assertFalse($event->other['forgottenreset']);
        $event->trigger();

        // Changing password of other user.
        $event = \core\event\user_password_updated::create_from_user($user2);
        $this->assertEventContextNotUsed($event);
        $this->assertEquals($user2->id, $event->relateduserid);
        $this->assertSame($context2, $event->get_context());
        $this->assertFalse($event->other['forgottenreset']);
        $event->trigger();

        // Password reset.
        $event = \core\event\user_password_updated::create_from_user($user1, true);
        $this->assertEventContextNotUsed($event);
        $this->assertEquals($user1->id, $event->relateduserid);
        $this->assertSame($context1, $event->get_context());
        $this->assertTrue($event->other['forgottenreset']);
        $event->trigger();
    }
}
