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

namespace core;

/**
 * Unit tests for session manager class.
 *
 * @package    core
 * @category   test
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class session_manager_test extends \advanced_testcase {
    public function test_start() {
        $this->resetAfterTest();
        // Session must be started only once...
        \core\session\manager::start();
        $this->assertDebuggingCalled('Session was already started!', DEBUG_DEVELOPER);
    }

    public function test_init_empty_session() {
        global $SESSION, $USER;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        $SESSION->test = true;
        $this->assertTrue($GLOBALS['SESSION']->test);
        $this->assertTrue($_SESSION['SESSION']->test);

        \core\session\manager::set_user($user);
        $this->assertSame($user, $USER);
        $this->assertSame($user, $GLOBALS['USER']);
        $this->assertSame($user, $_SESSION['USER']);

        \core\session\manager::init_empty_session();

        $this->assertInstanceOf('stdClass', $SESSION);
        $this->assertEmpty((array)$SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);

        $this->assertInstanceOf('stdClass', $USER);
        $this->assertEqualsCanonicalizing(array('id' => 0, 'mnethostid' => 1), (array)$USER);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        // Now test how references work.

        $GLOBALS['SESSION'] = new \stdClass();
        $GLOBALS['SESSION']->test = true;
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);

        $SESSION = new \stdClass();
        $SESSION->test2 = true;
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);

        $_SESSION['SESSION'] = new \stdClass();
        $_SESSION['SESSION']->test3 = true;
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);

        $GLOBALS['USER'] = new \stdClass();
        $GLOBALS['USER']->test = true;
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        $USER = new \stdClass();
        $USER->test2 = true;
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        $_SESSION['USER'] = new \stdClass();
        $_SESSION['USER']->test3 = true;
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);
    }

    public function test_set_user() {
        global $USER;
        $this->resetAfterTest();

        $this->assertEquals(0, $USER->id);

        $user = $this->getDataGenerator()->create_user();
        $this->assertObjectHasProperty('description', $user);
        $this->assertObjectHasProperty('password', $user);

        \core\session\manager::set_user($user);

        $this->assertEquals($user->id, $USER->id);
        $this->assertObjectNotHasProperty('description', $user);
        $this->assertObjectNotHasProperty('password', $user);
        $this->assertObjectHasProperty('sesskey', $user);
        $this->assertSame($user, $GLOBALS['USER']);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);
    }

    public function test_login_user() {
        global $USER;
        $this->resetAfterTest();

        $this->assertEquals(0, $USER->id);

        $user = $this->getDataGenerator()->create_user();

        @\core\session\manager::login_user($user); // Ignore header error messages.
        $this->assertEquals($user->id, $USER->id);

        $this->assertObjectNotHasProperty('description', $user);
        $this->assertObjectNotHasProperty('password', $user);
        $this->assertSame($user, $GLOBALS['USER']);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);
    }

    public function test_terminate_current() {
        global $USER, $SESSION;
        $this->resetAfterTest();

        $this->setAdminUser();
        \core\session\manager::terminate_current();
        $this->assertEquals(0, $USER->id);

        $this->assertInstanceOf('stdClass', $SESSION);
        $this->assertEmpty((array)$SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);

        $this->assertInstanceOf('stdClass', $USER);
        $this->assertEqualsCanonicalizing(array('id' => 0, 'mnethostid' => 1), (array)$USER);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);
    }

    public function test_write_close() {
        global $USER;
        $this->resetAfterTest();

        // Just make sure no errors and $USER->id is kept
        $this->setAdminUser();
        $userid = $USER->id;
        \core\session\manager::write_close();
        $this->assertSame($userid, $USER->id);

        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);
    }

    public function test_session_exists() {
        global $CFG, $DB;
        $this->resetAfterTest();

        $this->assertFalse(\core\session\manager::session_exists('abc'));

        $user = $this->getDataGenerator()->create_user();
        $guest = guest_user();

        // The file handler is used by default, so let's fake the data somehow.
        $sid = md5('hokus');
        mkdir("$CFG->dataroot/sessions/", $CFG->directorypermissions, true);
        touch("$CFG->dataroot/sessions/sess_$sid");

        $this->assertFalse(\core\session\manager::session_exists($sid));

        $record = new \stdClass();
        $record->userid = 0;
        $record->sid = $sid;
        $record->timecreated = time();
        $record->timemodified = $record->timecreated;
        $record->id = $DB->insert_record('sessions', $record);

        $this->assertTrue(\core\session\manager::session_exists($sid));

        $record->timecreated = time() - $CFG->sessiontimeout - 100;
        $record->timemodified = $record->timecreated + 10;
        $DB->update_record('sessions', $record);

        $this->assertTrue(\core\session\manager::session_exists($sid));

        $record->userid = $guest->id;
        $DB->update_record('sessions', $record);

        $this->assertTrue(\core\session\manager::session_exists($sid));

        $record->userid = $user->id;
        $DB->update_record('sessions', $record);

        $this->assertFalse(\core\session\manager::session_exists($sid));

        $CFG->sessiontimeout = $CFG->sessiontimeout + 3000;

        $this->assertTrue(\core\session\manager::session_exists($sid));
    }

    public function test_touch_session() {
        global $DB;
        $this->resetAfterTest();

        $sid = md5('hokus');
        $record = new \stdClass();
        $record->state        = 0;
        $record->sid          = $sid;
        $record->sessdata     = null;
        $record->userid       = 2;
        $record->timecreated  = time() - 60*60;
        $record->timemodified = time() - 30;
        $record->firstip      = $record->lastip = '10.0.0.1';
        $record->id = $DB->insert_record('sessions', $record);

        $now = time();
        \core\session\manager::touch_session($sid);
        $updated = $DB->get_field('sessions', 'timemodified', array('id'=>$record->id));

        $this->assertGreaterThanOrEqual($now, $updated);
        $this->assertLessThanOrEqual(time(), $updated);
    }

    public function test_kill_session() {
        global $DB, $USER;
        $this->resetAfterTest();

        $this->setAdminUser();
        $userid = $USER->id;

        $sid = md5('hokus');
        $record = new \stdClass();
        $record->state        = 0;
        $record->sid          = $sid;
        $record->sessdata     = null;
        $record->userid       = $userid;
        $record->timecreated  = time() - 60*60;
        $record->timemodified = time() - 30;
        $record->firstip      = $record->lastip = '10.0.0.1';
        $DB->insert_record('sessions', $record);

        $record->userid       = 0;
        $record->sid          = md5('pokus');
        $DB->insert_record('sessions', $record);

        $this->assertEquals(2, $DB->count_records('sessions'));

        \core\session\manager::kill_session($sid);

        $this->assertEquals(1, $DB->count_records('sessions'));
        $this->assertFalse($DB->record_exists('sessions', array('sid'=>$sid)));

        $this->assertSame($userid, $USER->id);
    }

    public function test_kill_user_sessions() {
        global $DB, $USER;
        $this->resetAfterTest();

        $this->setAdminUser();
        $userid = $USER->id;

        $sid = md5('hokus');
        $record = new \stdClass();
        $record->state        = 0;
        $record->sid          = $sid;
        $record->sessdata     = null;
        $record->userid       = $userid;
        $record->timecreated  = time() - 60*60;
        $record->timemodified = time() - 30;
        $record->firstip      = $record->lastip = '10.0.0.1';
        $DB->insert_record('sessions', $record);

        $record->sid          = md5('hokus2');
        $DB->insert_record('sessions', $record);

        $record->userid       = 0;
        $record->sid          = md5('pokus');
        $DB->insert_record('sessions', $record);

        $this->assertEquals(3, $DB->count_records('sessions'));

        \core\session\manager::kill_user_sessions($userid);

        $this->assertEquals(1, $DB->count_records('sessions'));
        $this->assertFalse($DB->record_exists('sessions', array('userid' => $userid)));

        $record->userid       = $userid;
        $record->sid          = md5('pokus3');
        $DB->insert_record('sessions', $record);

        $record->userid       = $userid;
        $record->sid          = md5('pokus4');
        $DB->insert_record('sessions', $record);

        $record->userid       = $userid;
        $record->sid          = md5('pokus5');
        $DB->insert_record('sessions', $record);

        $this->assertEquals(3, $DB->count_records('sessions', array('userid' => $userid)));

        \core\session\manager::kill_user_sessions($userid, md5('pokus5'));

        $this->assertEquals(1, $DB->count_records('sessions', array('userid' => $userid)));
        $this->assertEquals(1, $DB->count_records('sessions', array('userid' => $userid, 'sid' => md5('pokus5'))));
    }

    public function test_apply_concurrent_login_limit() {
        global $DB;
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $guest = guest_user();

        $record = new \stdClass();
        $record->state        = 0;
        $record->sessdata     = null;
        $record->userid       = $user1->id;
        $record->timemodified = time();
        $record->firstip      = $record->lastip = '10.0.0.1';

        $record->sid = md5('hokus1');
        $record->timecreated = 20;
        $DB->insert_record('sessions', $record);
        $record->sid = md5('hokus2');
        $record->timecreated = 10;
        $DB->insert_record('sessions', $record);
        $record->sid = md5('hokus3');
        $record->timecreated = 30;
        $DB->insert_record('sessions', $record);

        $record->userid = $user2->id;
        $record->sid = md5('pokus1');
        $record->timecreated = 20;
        $DB->insert_record('sessions', $record);
        $record->sid = md5('pokus2');
        $record->timecreated = 10;
        $DB->insert_record('sessions', $record);
        $record->sid = md5('pokus3');
        $record->timecreated = 30;
        $DB->insert_record('sessions', $record);

        $record->timecreated = 10;
        $record->userid = $guest->id;
        $record->sid = md5('g1');
        $DB->insert_record('sessions', $record);
        $record->sid = md5('g2');
        $DB->insert_record('sessions', $record);
        $record->sid = md5('g3');
        $DB->insert_record('sessions', $record);

        $record->userid = 0;
        $record->sid = md5('nl1');
        $DB->insert_record('sessions', $record);
        $record->sid = md5('nl2');
        $DB->insert_record('sessions', $record);
        $record->sid = md5('nl3');
        $DB->insert_record('sessions', $record);

        set_config('limitconcurrentlogins', 0);
        $this->assertCount(12, $DB->get_records('sessions'));

        \core\session\manager::apply_concurrent_login_limit($user1->id);
        \core\session\manager::apply_concurrent_login_limit($user2->id);
        \core\session\manager::apply_concurrent_login_limit($guest->id);
        \core\session\manager::apply_concurrent_login_limit(0);
        $this->assertCount(12, $DB->get_records('sessions'));

        set_config('limitconcurrentlogins', -1);

        \core\session\manager::apply_concurrent_login_limit($user1->id);
        \core\session\manager::apply_concurrent_login_limit($user2->id);
        \core\session\manager::apply_concurrent_login_limit($guest->id);
        \core\session\manager::apply_concurrent_login_limit(0);
        $this->assertCount(12, $DB->get_records('sessions'));

        set_config('limitconcurrentlogins', 2);

        \core\session\manager::apply_concurrent_login_limit($user1->id);
        $this->assertCount(11, $DB->get_records('sessions'));
        $this->assertTrue($DB->record_exists('sessions', array('userid' => $user1->id, 'timecreated' => 20)));
        $this->assertTrue($DB->record_exists('sessions', array('userid' => $user1->id, 'timecreated' => 30)));
        $this->assertFalse($DB->record_exists('sessions', array('userid' => $user1->id, 'timecreated' => 10)));

        $this->assertTrue($DB->record_exists('sessions', array('userid' => $user2->id, 'timecreated' => 20)));
        $this->assertTrue($DB->record_exists('sessions', array('userid' => $user2->id, 'timecreated' => 30)));
        $this->assertTrue($DB->record_exists('sessions', array('userid' => $user2->id, 'timecreated' => 10)));
        set_config('limitconcurrentlogins', 2);
        \core\session\manager::apply_concurrent_login_limit($user2->id, md5('pokus2'));
        $this->assertCount(10, $DB->get_records('sessions'));
        $this->assertFalse($DB->record_exists('sessions', array('userid' => $user2->id, 'timecreated' => 20)));
        $this->assertTrue($DB->record_exists('sessions', array('userid' => $user2->id, 'timecreated' => 30)));
        $this->assertTrue($DB->record_exists('sessions', array('userid' => $user2->id, 'timecreated' => 10)));

        \core\session\manager::apply_concurrent_login_limit($guest->id);
        \core\session\manager::apply_concurrent_login_limit(0);
        $this->assertCount(10, $DB->get_records('sessions'));

        set_config('limitconcurrentlogins', 1);

        \core\session\manager::apply_concurrent_login_limit($user1->id, md5('grrr'));
        $this->assertCount(9, $DB->get_records('sessions'));
        $this->assertFalse($DB->record_exists('sessions', array('userid' => $user1->id, 'timecreated' => 20)));
        $this->assertTrue($DB->record_exists('sessions', array('userid' => $user1->id, 'timecreated' => 30)));
        $this->assertFalse($DB->record_exists('sessions', array('userid' => $user1->id, 'timecreated' => 10)));

        \core\session\manager::apply_concurrent_login_limit($user1->id);
        $this->assertCount(9, $DB->get_records('sessions'));
        $this->assertFalse($DB->record_exists('sessions', array('userid' => $user1->id, 'timecreated' => 20)));
        $this->assertTrue($DB->record_exists('sessions', array('userid' => $user1->id, 'timecreated' => 30)));
        $this->assertFalse($DB->record_exists('sessions', array('userid' => $user1->id, 'timecreated' => 10)));

        \core\session\manager::apply_concurrent_login_limit($user2->id, md5('pokus2'));
        $this->assertCount(8, $DB->get_records('sessions'));
        $this->assertFalse($DB->record_exists('sessions', array('userid' => $user2->id, 'timecreated' => 20)));
        $this->assertFalse($DB->record_exists('sessions', array('userid' => $user2->id, 'timecreated' => 30)));
        $this->assertTrue($DB->record_exists('sessions', array('userid' => $user2->id, 'timecreated' => 10)));

        \core\session\manager::apply_concurrent_login_limit($user2->id);
        $this->assertCount(8, $DB->get_records('sessions'));
        $this->assertFalse($DB->record_exists('sessions', array('userid' => $user2->id, 'timecreated' => 20)));
        $this->assertFalse($DB->record_exists('sessions', array('userid' => $user2->id, 'timecreated' => 30)));
        $this->assertTrue($DB->record_exists('sessions', array('userid' => $user2->id, 'timecreated' => 10)));

        \core\session\manager::apply_concurrent_login_limit($guest->id);
        \core\session\manager::apply_concurrent_login_limit(0);
        $this->assertCount(8, $DB->get_records('sessions'));
    }

    public function test_kill_all_sessions() {
        global $DB, $USER;
        $this->resetAfterTest();

        $this->setAdminUser();
        $userid = $USER->id;

        $sid = md5('hokus');
        $record = new \stdClass();
        $record->state        = 0;
        $record->sid          = $sid;
        $record->sessdata     = null;
        $record->userid       = $userid;
        $record->timecreated  = time() - 60*60;
        $record->timemodified = time() - 30;
        $record->firstip      = $record->lastip = '10.0.0.1';
        $DB->insert_record('sessions', $record);

        $record->sid          = md5('hokus2');
        $DB->insert_record('sessions', $record);

        $record->userid       = 0;
        $record->sid          = md5('pokus');
        $DB->insert_record('sessions', $record);

        $this->assertEquals(3, $DB->count_records('sessions'));

        \core\session\manager::kill_all_sessions();

        $this->assertEquals(0, $DB->count_records('sessions'));
        $this->assertSame(0, $USER->id);
    }

    public function test_gc() {
        global $CFG, $DB, $USER;
        $this->resetAfterTest();

        $this->setAdminUser();
        $adminid = $USER->id;
        $this->setGuestUser();
        $guestid = $USER->id;
        $this->setUser(0);

        $CFG->sessiontimeout = 60*10;

        $record = new \stdClass();
        $record->state        = 0;
        $record->sid          = md5('hokus1');
        $record->sessdata     = null;
        $record->userid       = $adminid;
        $record->timecreated  = time() - 60*60;
        $record->timemodified = time() - 30;
        $record->firstip      = $record->lastip = '10.0.0.1';
        $r1 = $DB->insert_record('sessions', $record);

        $record->sid          = md5('hokus2');
        $record->userid       = $adminid;
        $record->timecreated  = time() - 60*60;
        $record->timemodified = time() - 60*20;
        $r2 = $DB->insert_record('sessions', $record);

        $record->sid          = md5('hokus3');
        $record->userid       = $guestid;
        $record->timecreated  = time() - 60*60*60;
        $record->timemodified = time() - 60*20;
        $r3 = $DB->insert_record('sessions', $record);

        $record->sid          = md5('hokus4');
        $record->userid       = $guestid;
        $record->timecreated  = time() - 60*60*60;
        $record->timemodified = time() - 60*10*5 - 60;
        $r4 = $DB->insert_record('sessions', $record);

        $record->sid          = md5('hokus5');
        $record->userid       = 0;
        $record->timecreated  = time() - 60*5;
        $record->timemodified = time() - 60*5;
        $r5 = $DB->insert_record('sessions', $record);

        $record->sid          = md5('hokus6');
        $record->userid       = 0;
        $record->timecreated  = time() - 60*60;
        $record->timemodified = time() - 60*10 -10;
        $r6 = $DB->insert_record('sessions', $record);

        $record->sid          = md5('hokus7');
        $record->userid       = 0;
        $record->timecreated  = time() - 60*60;
        $record->timemodified = time() - 60*9;
        $r7 = $DB->insert_record('sessions', $record);

        \core\session\manager::gc();

        $this->assertTrue($DB->record_exists('sessions', array('id'=>$r1)));
        $this->assertFalse($DB->record_exists('sessions', array('id'=>$r2)));
        $this->assertTrue($DB->record_exists('sessions', array('id'=>$r3)));
        $this->assertFalse($DB->record_exists('sessions', array('id'=>$r4)));
        $this->assertFalse($DB->record_exists('sessions', array('id'=>$r5)));
        $this->assertFalse($DB->record_exists('sessions', array('id'=>$r6)));
        $this->assertTrue($DB->record_exists('sessions', array('id'=>$r7)));
    }

    /**
     * Test loginas.
     * @copyright  2103 Rajesh Taneja <rajesh@moodle.com>
     */
    public function test_loginas() {
        global $USER, $SESSION;
        $this->resetAfterTest();

        // Set current user as Admin user and save it for later use.
        $this->setAdminUser();
        $adminuser = $USER;
        $adminsession = $SESSION;
        $user = $this->getDataGenerator()->create_user();
        $_SESSION['extra'] = true;

        // Try admin loginas this user in system context.
        $this->assertObjectNotHasProperty('realuser', $USER);
        \core\session\manager::loginas($user->id, \context_system::instance());

        $this->assertSame($user->id, $USER->id);
        $this->assertEquals(\context_system::instance(), $USER->loginascontext);
        $this->assertSame($adminuser->id, $USER->realuser);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);
        $this->assertNotSame($adminuser, $_SESSION['REALUSER']);
        $this->assertEquals($adminuser, $_SESSION['REALUSER']);

        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertNotSame($adminsession, $_SESSION['REALSESSION']);
        $this->assertEquals($adminsession, $_SESSION['REALSESSION']);

        $this->assertArrayNotHasKey('extra', $_SESSION);

        // Set user as current user and login as admin user in course context.
        \core\session\manager::init_empty_session();
        $this->setUser($user);
        $this->assertNotEquals($adminuser->id, $USER->id);
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        // Catch event triggered.
        $sink = $this->redirectEvents();
        \core\session\manager::loginas($adminuser->id, $coursecontext);
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
    }

    public function test_is_loggedinas() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->assertFalse(\core\session\manager::is_loggedinas());

        $this->setUser($user1);
        \core\session\manager::loginas($user2->id, \context_system::instance());

        $this->assertTrue(\core\session\manager::is_loggedinas());
    }

    public function test_get_realuser() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->setUser($user1);
        $normal = \core\session\manager::get_realuser();
        $this->assertSame($GLOBALS['USER'], $normal);

        \core\session\manager::loginas($user2->id, \context_system::instance());

        $real = \core\session\manager::get_realuser();

        unset($real->password);
        unset($real->description);
        unset($real->sesskey);
        unset($user1->password);
        unset($user1->description);
        unset($user1->sesskey);

        $this->assertEquals($real, $user1);
        $this->assertSame($_SESSION['REALUSER'], $real);
    }

    /**
     * Session lock info on pages.
     *
     * @return array
     */
    public function pages_sessionlocks() {
        return [
            [
                'url'      => '/good.php',
                'start'    => 1500000001.000,
                'gained'   => 1500000002.000,
                'released' => 1500000003.000,
                'wait'     => 1.0,
                'held'     => 1.0
            ],
            [
                'url'      => '/bad.php?wait=5',
                'start'    => 1500000003.000,
                'gained'   => 1500000005.000,
                'released' => 1500000007.000,
                'held'     => 2.0,
                'wait'     => 2.0
            ]
        ];
    }

    /**
     * Test to get recent session locks.
     */
    public function test_get_recent_session_locks() {
        global $CFG;

        $this->resetAfterTest();
        $CFG->debugsessionlock = 5;
        $pages = $this->pages_sessionlocks();
        // Recent session locks must be empty at first.
        $recentsessionlocks = \core\session\manager::get_recent_session_locks();
        $this->assertEmpty($recentsessionlocks);

        // Add page to the recentsessionlocks array.
        \core\session\manager::update_recent_session_locks($pages[0]);
        $recentsessionlocks = \core\session\manager::get_recent_session_locks();
        // Make sure we are getting the first page we added.
        $this->assertEquals($pages[0], $recentsessionlocks[0]);
        // There should be 1 page in the array.
        $this->assertCount(1, $recentsessionlocks);

        // Add second page to the recentsessionlocks array.
        \core\session\manager::update_recent_session_locks($pages[1]);
        $recentsessionlocks = \core\session\manager::get_recent_session_locks();
        // Make sure we are getting the second page we added.
        $this->assertEquals($pages[1], $recentsessionlocks[1]);
        // There should be 2 pages in the array.
        $this->assertCount(2, $recentsessionlocks);
    }

    /**
     * Test to update recent session locks.
     */
    public function test_update_recent_session_locks() {
        global $CFG;

        $this->resetAfterTest();
        $CFG->debugsessionlock = 5;
        $pages = $this->pages_sessionlocks();

        \core\session\manager::update_recent_session_locks($pages[0]);
        \core\session\manager::update_recent_session_locks($pages[1]);
        $recentsessionlocks = \core\session\manager::get_recent_session_locks();
        // There should be 2 pages in the array.
        $this->assertCount(2, $recentsessionlocks);
        // Make sure the last page is added at the end of the array.
        $this->assertEquals($pages[1], end($recentsessionlocks));

    }

    /**
     * Test to get session lock info.
     */
    public function test_get_session_lock_info() {
        global $PERF;

        $this->resetAfterTest();

        $pages = $this->pages_sessionlocks();
        $PERF->sessionlock = $pages[0];
        $sessionlock = \core\session\manager::get_session_lock_info();
        $this->assertEquals($pages[0], $sessionlock);
    }

    /**
     * Session lock info on some pages to serve as history.
     *
     * @return array
     */
    public function sessionlock_history() {
        return [
            [
                'url'      => '/good.php',
                'start'    => 1500000001.000,
                'gained'   => 1500000001.100,
                'released' => 1500000001.500,
                'wait'     => 0.1
            ],
            [
                // This bad request doesn't release the session for 10 seconds.
                'url'      => '/bad.php',
                'start'    => 1500000012.000,
                'gained'   => 1500000012.200,
                'released' => 1500000020.200,
                'wait'     => 0.2
            ],
            [
                // All subsequent requests are blocked and need to wait.
                'url'      => '/good.php?id=1',
                'start'    => 1500000012.900,
                'gained'   => 1500000020.200,
                'released' => 1500000022.000,
                'wait'     => 7.29
            ],
            [
                'url'      => '/good.php?id=2',
                'start'    => 1500000014.000,
                'gained'   => 1500000022.000,
                'released' => 1500000025.000,
                'wait'     => 8.0
            ],
            [
                'url'      => '/good.php?id=3',
                'start'    => 1500000015.000,
                'gained'   => 1500000025.000,
                'released' => 1500000026.000,
                'wait'     => 10.0
            ],
            [
                'url'      => '/good.php?id=4',
                'start'    => 1500000016.000,
                'gained'   => 1500000026.000,
                'released' => 1500000027.000,
                'wait'     => 10.0
            ]
        ];
    }

    /**
     * Data provider for test_get_locked_page_at function.
     *
     * @return array
     */
    public function sessionlocks_info_provider(): array {
        return [
            [
                'url'      => null,
                'time'    => 1500000001.000
            ],
            [
                'url'      => '/bad.php',
                'time'    => 1500000014.000
            ],
            [
                'url'      => '/good.php?id=2',
                'time'    => 1500000022.500
            ],
        ];
    }

    /**
     * Test to get locked page at a speficic timestamp.
     *
     * @dataProvider sessionlocks_info_provider
     * @param array $url Session lock page url.
     * @param array $time Session lock time.
     */
    public function test_get_locked_page_at($url, $time) {
        global $CFG, $SESSION;

        $this->resetAfterTest();
        $CFG->debugsessionlock = 5;
        $SESSION->recentsessionlocks = $this->sessionlock_history();

        $page = \core\session\manager::get_locked_page_at($time);
        $this->assertEquals($url, is_array($page) ? $page['url'] : null);
    }

    /**
     * Test cleanup recent session locks.
     */
    public function test_cleanup_recent_session_locks() {
        global $CFG, $SESSION;

        $this->resetAfterTest();
        $CFG->debugsessionlock = 5;

        $SESSION->recentsessionlocks = $this->sessionlock_history();
        $this->assertCount(6, $SESSION->recentsessionlocks);
        \core\session\manager::cleanup_recent_session_locks();
        // Make sure the session history has been cleaned up and only has the latest page.
        $this->assertCount(1, $SESSION->recentsessionlocks);
        $this->assertEquals('/good.php?id=4', $SESSION->recentsessionlocks[0]['url']);
    }

    /**
     * Data provider for the array_session_diff function.
     *
     * @return array
     */
    public function array_session_diff_provider() {
        // Create an instance of this object so the comparison object's identities are the same.
        // Used in one of the tests below.
        $compareobjectb = (object) ['array' => 'b'];

        return [
            'both same objects' => [
                'a' => ['example' => (object) ['array' => 'a']],
                'b' => ['example' => (object) ['array' => 'a']],
                'expected' => [],
            ],
            'both same arrays' => [
                'a' => ['example' => ['array' => 'a']],
                'b' => ['example' => ['array' => 'a']],
                'expected' => [],
            ],
            'both the same with nested objects' => [
                'a' => ['example' => (object) ['array' => 'a', 'deeper' => (object) []]],
                'b' => ['example' => (object) ['array' => 'a', 'deeper' => (object) []]],
                'expected' => [],
            ],
            'first array larger' => [
                'a' => ['x' => 1, 'y' => 2],
                'b' => ['x' => 1],
                'expected' => ['y' => 2]
            ],
            'second array larger' => [
                'a' => ['x' => 1],
                'b' => ['x' => 1, 'y' => 2],
                'expected' => ['y' => 2]
            ],
            'objects with different values but same keys' => [
                'a' => ['example' => (object) ['array' => 'a']],
                'b' => ['example' => $compareobjectb],
                'expected' => ['example' => $compareobjectb]
            ],
            'different arrays with top level indexes' => [
                'a' => ['x', 'y'],
                'b' => ['x', 'y', 'z'],
                'expected' => [2 => 'z']
            ],
            'different types but same values as first level' => [
                'a' => ['example' => (object) ['array' => 'a']],
                'b' => ['example' => ['array' => 'a']],
                'expected' => ['example' => ['array' => 'a']]
            ],
            'different types but same values nested' => [
                'a' => ['example' => (object) ['array' => ['a' => 'test']]],
                'b' => ['example' => (object) ['array' => (object) ['a' => 'test']]],
                // Type checking is not done further than the first level, so we expect no difference.
                'expected' => []
            ]
        ];
    }

    /**
     * Tests array diff method in various situations.
     *
     * @dataProvider array_session_diff_provider
     * @covers \core\session\manager::array_session_diff
     * @param array $a first value.
     * @param array $b second value to compare to $a.
     * @param array $expected the expected difference.
     */
    public function test_array_session_diff(array $a, array $b, array $expected) {
        $class = new \ReflectionClass('\core\session\manager');
        $method = $class->getMethod('array_session_diff');

        $result = $method->invokeArgs(null, [$a, $b]);
        $this->assertSame($expected, $result);
    }
}
