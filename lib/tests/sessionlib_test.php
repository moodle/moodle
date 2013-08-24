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
 * Unit tests for (some of) ../sessionlib.php.
 *
 * @package    core_session
 * @category   phpunit
 * @copyright  2103 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/sessionlib.php');

/**
 * Unit tests for (some of) ../sessionlib.php.
 *
 * @package    core_session
 * @category   phpunit
 * @copyright  2103 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_sessionlib_testcase extends advanced_testcase {

    /**
     * Test session_loginas.
     */
    public function test_session_loginas() {
        global $USER;
        $this->resetAfterTest();

        // Set current user as Admin user and save it for later use.
        $this->setAdminUser();
        $adminuser = $USER;

        // Create a new user and try admin loginas this user.
        $user = $this->getDataGenerator()->create_user();
        session_loginas($user->id, context_system::instance());

        $this->assertSame($user->id, $USER->id);
        $this->assertSame(context_system::instance(), $USER->loginascontext);
        $this->assertSame($adminuser->id, $USER->realuser);

        // Set user as current user and login as admin user in course context.
        $this->setUser($user);
        $this->assertNotEquals($adminuser->id, $USER->id);
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        // Catch event triggred.
        $sink = $this->redirectEvents();
        session_loginas($adminuser->id, $coursecontext);
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        $this->assertSame($adminuser->id, $USER->id);
        $this->assertSame($coursecontext, $USER->loginascontext);
        $this->assertSame($user->id, $USER->realuser);

        // Test event captured has proper information.
        $this->assertInstanceOf('\core\event\user_loggedinas', $event);
        $this->assertSame($user->id, $event->objectid);
        $this->assertSame($adminuser->id, $event->relateduserid);
        $this->assertSame($course->id, $event->courseid);
        $this->assertEquals($coursecontext, $event->get_context());
        $oldfullname = fullname($user, true);
        $newfullname = fullname($adminuser, true);
        $expectedlogdata = array($course->id, "course", "loginas", "../user/view.php?id=$course->id&amp;user=$user->id", "$oldfullname -> $newfullname");
        $this->assertEventLegacyLogData($expectedlogdata, $event);
    }
}
