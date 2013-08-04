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
 * Tests for auth.
 *
 * @package    core_auth
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/authlib.php');

/**
 * Auth testcase class.
 *
 * @package    core_auth
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_auth_testcase extends advanced_testcase {

    public function test_user_loggedin_event() {
        global $USER;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $sink = $this->redirectEvents();
        $user = clone($USER);
        login_attempt_valid($user);
        $events = $sink->get_events();
        $sink->close();

        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\core_auth\event\user_loggedin', $event);
        $this->assertEquals('user', $event->objecttable);
        $this->assertEquals('2', $event->objectid);
        $this->assertEquals(context_system::instance()->id, $event->contextid);
        $this->assertEquals($user, $event->get_record_snapshot('user', 2));
    }

    public function test_user_loggedin_event_exceptions() {
        try {
            $event = \core_auth\event\user_loggedin::create(array('objectid' => 1));
            $this->fail('\core_auth\event\user_loggedin requires other[\'username\']');
        } catch(Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $event = \core_auth\event\user_loggedin::create(array('other' => array('username' => 'test')));
            $this->fail('\core_auth\event\user_loggedin requires objectid');
        } catch(Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

}
