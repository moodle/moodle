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
 * Unit tests for user/lib.php.
 *
 * @package    core_user
 * @category   phpunit
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/user/lib.php');

/**
 * Unit tests for user lib api.
 *
 * @package    core_user
 * @category   phpunit
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_userliblib_testcase extends advanced_testcase {
    /**
     * Test user_update_user.
     */
    public function test_user_update_user() {
        global $DB;

        $this->resetAfterTest();

        // Create user and modify user profile.
        $user = $this->getDataGenerator()->create_user();
        $user->firstname = 'Test';
        $user->password = 'M00dLe@T';

        // Update user and capture event.
        $sink = $this->redirectEvents();
        user_update_user($user);
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        // Test updated value.
        $dbuser = $DB->get_record('user', array('id' => $user->id));
        $this->assertSame($user->firstname, $dbuser->firstname);
        $this->assertNotSame('M00dLe@T', $dbuser->password);

        // Test event.
        $this->assertInstanceOf('\core\event\user_updated', $event);
        $this->assertSame($user->id, $event->objectid);
        $this->assertSame('user_updated', $event->get_legacy_eventname());
        $this->assertEventLegacyData($dbuser, $event);
        $this->assertEquals(context_user::instance($user->id), $event->get_context());
        $expectedlogdata = array(SITEID, 'user', 'update', 'view.php?id='.$user->id, '');
        $this->assertEventLegacyLogData($expectedlogdata, $event);

        // Update user with no password update.
        $password = $user->password = hash_internal_user_password('M00dLe@T');
        user_update_user($user, false);
        $dbuser = $DB->get_record('user', array('id' => $user->id));
        $this->assertSame($password, $dbuser->password);

        // Verify event is not triggred by user_update_user when needed.
        $sink = $this->redirectEvents();
        user_update_user($user, false, false);
        $events = $sink->get_events();
        $sink->close();
        $this->assertCount(0, $events);

        // With password, there should be 1 event.
        $sink = $this->redirectEvents();
        user_update_user($user, true, false);
        $events = $sink->get_events();
        $sink->close();
        $this->assertCount(1, $events);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_password_updated', $event);
    }

    /**
     * Test create_users.
     */
    public function test_create_users() {
        global $DB;

        $this->resetAfterTest();

        $user = array(
            'username' => 'usernametest1',
            'password' => 'Moodle2012!',
            'idnumber' => 'idnumbertest1',
            'firstname' => 'First Name User Test 1',
            'lastname' => 'Last Name User Test 1',
            'middlename' => 'Middle Name User Test 1',
            'lastnamephonetic' => '最後のお名前のテスト一号',
            'firstnamephonetic' => 'お名前のテスト一号',
            'alternatename' => 'Alternate Name User Test 1',
            'email' => 'usertest1@example.com',
            'description' => 'This is a description for user 1',
            'city' => 'Perth',
            'country' => 'au'
            );

        // Create user and capture event.
        $sink = $this->redirectEvents();
        $user['id'] = user_create_user($user);
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        // Test user info in DB.
        $dbuser = $DB->get_record('user', array('id' => $user['id']));
        $this->assertEquals($dbuser->username, $user['username']);
        $this->assertEquals($dbuser->idnumber, $user['idnumber']);
        $this->assertEquals($dbuser->firstname, $user['firstname']);
        $this->assertEquals($dbuser->lastname, $user['lastname']);
        $this->assertEquals($dbuser->email, $user['email']);
        $this->assertEquals($dbuser->description, $user['description']);
        $this->assertEquals($dbuser->city, $user['city']);
        $this->assertEquals($dbuser->country, $user['country']);

        // Test event.
        $this->assertInstanceOf('\core\event\user_created', $event);
        $this->assertEquals($user['id'], $event->objectid);
        $this->assertEquals('user_created', $event->get_legacy_eventname());
        $this->assertEquals(context_user::instance($user['id']), $event->get_context());
        $this->assertEventLegacyData($dbuser, $event);
        $expectedlogdata = array(SITEID, 'user', 'add', '/view.php?id='.$event->objectid, fullname($dbuser));
        $this->assertEventLegacyLogData($expectedlogdata, $event);

        // Verify event is not triggred by user_create_user when needed.
        $user = array('username' => 'usernametest2'); // Create another user.
        $sink = $this->redirectEvents();
        user_create_user($user, true, false);
        $events = $sink->get_events();
        $sink->close();
        $this->assertCount(0, $events);
    }

    /**
     * Test function user_count_login_failures().
     */
    public function test_user_count_login_failures() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->assertEquals(0, get_user_preferences('login_failed_count_since_success', 0, $user));
        for ($i = 0; $i < 10; $i++) {
            login_attempt_failed($user);
        }
        $this->assertEquals(10, get_user_preferences('login_failed_count_since_success', 0, $user));
        $count = user_count_login_failures($user); // Reset count.
        $this->assertEquals(10, $count);
        $this->assertEquals(0, get_user_preferences('login_failed_count_since_success', 0, $user));

        for ($i = 0; $i < 10; $i++) {
            login_attempt_failed($user);
        }
        $this->assertEquals(10, get_user_preferences('login_failed_count_since_success', 0, $user));
        $count = user_count_login_failures($user, false); // Do not reset count.
        $this->assertEquals(10, $count);
        $this->assertEquals(10, get_user_preferences('login_failed_count_since_success', 0, $user));
    }

    /**
     * Test function user_add_password_history().
     */
    public function test_user_add_password_history() {
        global $DB;

        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $DB->delete_records('user_password_history', array());

        set_config('passwordreuselimit', 0);

        user_add_password_history($user1->id, 'pokus');
        $this->assertEquals(0, $DB->count_records('user_password_history'));

        // Test adding and discarding of old.

        set_config('passwordreuselimit', 3);

        user_add_password_history($user1->id, 'pokus');
        $this->assertEquals(1, $DB->count_records('user_password_history'));
        $this->assertEquals(1, $DB->count_records('user_password_history', array('userid' => $user1->id)));

        user_add_password_history($user1->id, 'pokus2');
        user_add_password_history($user1->id, 'pokus3');
        user_add_password_history($user1->id, 'pokus4');
        $this->assertEquals(3, $DB->count_records('user_password_history'));
        $this->assertEquals(3, $DB->count_records('user_password_history', array('userid' => $user1->id)));

        user_add_password_history($user2->id, 'pokus1');
        $this->assertEquals(4, $DB->count_records('user_password_history'));
        $this->assertEquals(3, $DB->count_records('user_password_history', array('userid' => $user1->id)));
        $this->assertEquals(1, $DB->count_records('user_password_history', array('userid' => $user2->id)));

        user_add_password_history($user2->id, 'pokus2');
        user_add_password_history($user2->id, 'pokus3');
        $this->assertEquals(3, $DB->count_records('user_password_history', array('userid' => $user2->id)));

        $ids = array_keys($DB->get_records('user_password_history', array('userid' => $user2->id), 'timecreated ASC, id ASC'));
        user_add_password_history($user2->id, 'pokus4');
        $this->assertEquals(3, $DB->count_records('user_password_history', array('userid' => $user2->id)));
        $newids = array_keys($DB->get_records('user_password_history', array('userid' => $user2->id), 'timecreated ASC, id ASC'));

        $removed = array_shift($ids);
        $added = array_pop($newids);
        $this->assertSame($ids, $newids);
        $this->assertGreaterThan($removed, $added);

        // Test disabling prevents changes.

        set_config('passwordreuselimit', 0);

        $this->assertEquals(6, $DB->count_records('user_password_history'));

        $ids = array_keys($DB->get_records('user_password_history', array('userid' => $user2->id), 'timecreated ASC, id ASC'));
        user_add_password_history($user2->id, 'pokus5');
        user_add_password_history($user3->id, 'pokus1');
        $newids = array_keys($DB->get_records('user_password_history', array('userid' => $user2->id), 'timecreated ASC, id ASC'));
        $this->assertSame($ids, $newids);
        $this->assertEquals(6, $DB->count_records('user_password_history'));

        set_config('passwordreuselimit', -1);

        $ids = array_keys($DB->get_records('user_password_history', array('userid' => $user2->id), 'timecreated ASC, id ASC'));
        user_add_password_history($user2->id, 'pokus6');
        user_add_password_history($user3->id, 'pokus6');
        $newids = array_keys($DB->get_records('user_password_history', array('userid' => $user2->id), 'timecreated ASC, id ASC'));
        $this->assertSame($ids, $newids);
        $this->assertEquals(6, $DB->count_records('user_password_history'));
    }

    /**
     * Test function user_add_password_history().
     */
    public function test_user_is_previously_used_password() {
        global $DB;

        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $DB->delete_records('user_password_history', array());

        set_config('passwordreuselimit', 0);

        user_add_password_history($user1->id, 'pokus');
        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus'));

        set_config('passwordreuselimit', 3);

        user_add_password_history($user2->id, 'pokus1');
        user_add_password_history($user2->id, 'pokus2');

        user_add_password_history($user1->id, 'pokus1');
        $this->assertTrue(user_is_previously_used_password($user1->id, 'pokus1'));
        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus2'));
        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus3'));
        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus4'));

        user_add_password_history($user1->id, 'pokus2');
        $this->assertTrue(user_is_previously_used_password($user1->id, 'pokus1'));
        $this->assertTrue(user_is_previously_used_password($user1->id, 'pokus2'));
        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus3'));
        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus4'));

        user_add_password_history($user1->id, 'pokus3');
        $this->assertTrue(user_is_previously_used_password($user1->id, 'pokus1'));
        $this->assertTrue(user_is_previously_used_password($user1->id, 'pokus2'));
        $this->assertTrue(user_is_previously_used_password($user1->id, 'pokus3'));
        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus4'));

        user_add_password_history($user1->id, 'pokus4');
        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus1'));
        $this->assertTrue(user_is_previously_used_password($user1->id, 'pokus2'));
        $this->assertTrue(user_is_previously_used_password($user1->id, 'pokus3'));
        $this->assertTrue(user_is_previously_used_password($user1->id, 'pokus4'));

        set_config('passwordreuselimit', 2);

        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus1'));
        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus2'));
        $this->assertTrue(user_is_previously_used_password($user1->id, 'pokus3'));
        $this->assertTrue(user_is_previously_used_password($user1->id, 'pokus4'));

        set_config('passwordreuselimit', 3);

        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus1'));
        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus2'));
        $this->assertTrue(user_is_previously_used_password($user1->id, 'pokus3'));
        $this->assertTrue(user_is_previously_used_password($user1->id, 'pokus4'));

        set_config('passwordreuselimit', 0);

        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus1'));
        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus2'));
        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus3'));
        $this->assertFalse(user_is_previously_used_password($user1->id, 'pokus4'));
    }

    /**
     * Test that password history is deleted together with user.
     */
    public function test_delete_of_hashes_on_user_delete() {
        global $DB;

        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $DB->delete_records('user_password_history', array());

        set_config('passwordreuselimit', 3);

        user_add_password_history($user1->id, 'pokus');
        user_add_password_history($user2->id, 'pokus1');
        user_add_password_history($user2->id, 'pokus2');

        $this->assertEquals(3, $DB->count_records('user_password_history'));
        $this->assertEquals(1, $DB->count_records('user_password_history', array('userid' => $user1->id)));
        $this->assertEquals(2, $DB->count_records('user_password_history', array('userid' => $user2->id)));

        delete_user($user2);
        $this->assertEquals(1, $DB->count_records('user_password_history'));
        $this->assertEquals(1, $DB->count_records('user_password_history', array('userid' => $user1->id)));
        $this->assertEquals(0, $DB->count_records('user_password_history', array('userid' => $user2->id)));
    }

    /**
     * Test user_list_view function
     */
    public function test_user_list_view() {

        $this->resetAfterTest();

        // Course without sections.
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        $this->setAdminUser();

        // Redirect events to the sink, so we can recover them later.
        $sink = $this->redirectEvents();

        user_list_view($course, $context);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check the event details are correct.
        $this->assertInstanceOf('\core\event\user_list_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($course->shortname, $event->other['courseshortname']);
        $this->assertEquals($course->fullname, $event->other['coursefullname']);

    }

}
