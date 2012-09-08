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
 * Self enrolment plugin tests.
 *
 * @package    enrol_self
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/enrol/self/lib.php');
require_once($CFG->dirroot.'/enrol/self/locallib.php');

class enrol_self_testcase extends advanced_testcase {

    public function test_basics() {
        $this->assertTrue(enrol_is_enabled('self'));
        $plugin = enrol_get_plugin('self');
        $this->assertInstanceOf('enrol_self_plugin', $plugin);
        $this->assertEquals(1, get_config('enrol_self', 'defaultenrol'));
        $this->assertEquals(ENROL_EXT_REMOVED_KEEP, get_config('enrol_self', 'expiredaction'));
    }

    public function test_sync_nothing() {
        global $SITE;

        $selfplugin = enrol_get_plugin('self');

        // Just make sure the sync does not throw any errors when nothing to do.
        $selfplugin->sync(NULL, false);
        $selfplugin->sync($SITE->id, false);
    }

    public function test_longtimnosee() {
        global $DB;
        $this->resetAfterTest();

        $selfplugin = enrol_get_plugin('self');
        $manualplugin = enrol_get_plugin('manual');
        $this->assertNotEmpty($manualplugin);

        $now = time();

        // Prepare some data.

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $teacherrole = $DB->get_record('role', array('shortname'=>'teacher'));
        $this->assertNotEmpty($teacherrole);

        $record = array('firstaccess'=>$now-60*60*24*800);
        $record['lastaccess'] = $now-60*60*24*100;
        $user1 = $this->getDataGenerator()->create_user($record);
        $record['lastaccess'] = $now-60*60*24*10;
        $user2 = $this->getDataGenerator()->create_user($record);
        $record['lastaccess'] = $now-60*60*24*1;
        $user3 = $this->getDataGenerator()->create_user($record);
        $record['lastaccess'] = $now-10;
        $user4 = $this->getDataGenerator()->create_user($record);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();
        $context1 = context_course::instance($course1->id);
        $context2 = context_course::instance($course2->id);
        $context3 = context_course::instance($course3->id);

        $this->assertEquals(3, $DB->count_records('enrol', array('enrol'=>'self')));
        $instance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'self'), '*', MUST_EXIST);
        $instance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'self'), '*', MUST_EXIST);
        $instance3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'self'), '*', MUST_EXIST);
        $id = $selfplugin->add_instance($course3, array('status'=>ENROL_INSTANCE_ENABLED, 'roleid'=>$teacherrole->id));
        $instance3b = $DB->get_record('enrol', array('id'=>$id), '*', MUST_EXIST);
        unset($id);

        $this->assertEquals($studentrole->id, $instance1->roleid);
        $instance1->customint2 = 60*60*24*14;
        $DB->update_record('enrol', $instance1);
        $selfplugin->enrol_user($instance1, $user1->id, $studentrole->id);
        $selfplugin->enrol_user($instance1, $user2->id, $studentrole->id);
        $selfplugin->enrol_user($instance1, $user3->id, $studentrole->id);
        $this->assertEquals(3, $DB->count_records('user_enrolments'));
        $DB->insert_record('user_lastaccess', array('userid'=>$user2->id, 'courseid'=>$course1->id, 'timeaccess'=>$now-60*60*24*20));
        $DB->insert_record('user_lastaccess', array('userid'=>$user3->id, 'courseid'=>$course1->id, 'timeaccess'=>$now-60*60*24*2));
        $DB->insert_record('user_lastaccess', array('userid'=>$user4->id, 'courseid'=>$course1->id, 'timeaccess'=>$now-60));

        $this->assertEquals($studentrole->id, $instance3->roleid);
        $instance3->customint2 = 60*60*24*50;
        $DB->update_record('enrol', $instance3);
        $selfplugin->enrol_user($instance3, $user1->id, $studentrole->id);
        $selfplugin->enrol_user($instance3, $user2->id, $studentrole->id);
        $selfplugin->enrol_user($instance3, $user3->id, $studentrole->id);
        $selfplugin->enrol_user($instance3b, $user1->id, $teacherrole->id);
        $selfplugin->enrol_user($instance3b, $user4->id, $teacherrole->id);
        $this->assertEquals(8, $DB->count_records('user_enrolments'));
        $DB->insert_record('user_lastaccess', array('userid'=>$user2->id, 'courseid'=>$course3->id, 'timeaccess'=>$now-60*60*24*11));
        $DB->insert_record('user_lastaccess', array('userid'=>$user3->id, 'courseid'=>$course3->id, 'timeaccess'=>$now-60*60*24*200));
        $DB->insert_record('user_lastaccess', array('userid'=>$user4->id, 'courseid'=>$course3->id, 'timeaccess'=>$now-60*60*24*200));

        $maninstance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual'), '*', MUST_EXIST);

        $manualplugin->enrol_user($maninstance2, $user1->id, $studentrole->id);
        $manualplugin->enrol_user($maninstance3, $user1->id, $teacherrole->id);

        $this->assertEquals(10, $DB->count_records('user_enrolments'));
        $this->assertEquals(9, $DB->count_records('role_assignments'));
        $this->assertEquals(7, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));

        // Execute sync - this is the same thing used from cron.

        $selfplugin->sync($course2->id, false);
        $this->assertEquals(10, $DB->count_records('user_enrolments'));

        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$instance1->id, 'userid'=>$user1->id)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$instance1->id, 'userid'=>$user2->id)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$instance3->id, 'userid'=>$user1->id)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$instance3->id, 'userid'=>$user3->id)));
        $selfplugin->sync(null, false);
        $this->assertEquals(6, $DB->count_records('user_enrolments'));
        $this->assertFalse($DB->record_exists('user_enrolments', array('enrolid'=>$instance1->id, 'userid'=>$user1->id)));
        $this->assertFalse($DB->record_exists('user_enrolments', array('enrolid'=>$instance1->id, 'userid'=>$user2->id)));
        $this->assertFalse($DB->record_exists('user_enrolments', array('enrolid'=>$instance3->id, 'userid'=>$user1->id)));
        $this->assertFalse($DB->record_exists('user_enrolments', array('enrolid'=>$instance3->id, 'userid'=>$user3->id)));

        $this->assertEquals(6, $DB->count_records('role_assignments'));
        $this->assertEquals(4, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));
    }

    public function test_expired() {
        global $DB;
        $this->resetAfterTest();

        $selfplugin = enrol_get_plugin('self');
        $manualplugin = enrol_get_plugin('manual');
        $this->assertNotEmpty($manualplugin);

        $now = time();

        // Prepare some data.

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $teacherrole = $DB->get_record('role', array('shortname'=>'teacher'));
        $this->assertNotEmpty($teacherrole);
        $managerrole = $DB->get_record('role', array('shortname'=>'manager'));
        $this->assertNotEmpty($managerrole);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();
        $context1 = context_course::instance($course1->id);
        $context2 = context_course::instance($course2->id);
        $context3 = context_course::instance($course3->id);

        $this->assertEquals(3, $DB->count_records('enrol', array('enrol'=>'self')));
        $instance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'self'), '*', MUST_EXIST);
        $this->assertEquals($studentrole->id, $instance1->roleid);
        $instance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'self'), '*', MUST_EXIST);
        $this->assertEquals($studentrole->id, $instance2->roleid);
        $instance3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'self'), '*', MUST_EXIST);
        $this->assertEquals($studentrole->id, $instance3->roleid);
        $id = $selfplugin->add_instance($course3, array('status'=>ENROL_INSTANCE_ENABLED, 'roleid'=>$teacherrole->id));
        $instance3b = $DB->get_record('enrol', array('id'=>$id), '*', MUST_EXIST);
        $this->assertEquals($teacherrole->id, $instance3b->roleid);
        unset($id);

        $maninstance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual'), '*', MUST_EXIST);

        $manualplugin->enrol_user($maninstance2, $user1->id, $studentrole->id);
        $manualplugin->enrol_user($maninstance3, $user1->id, $teacherrole->id);

        $this->assertEquals(2, $DB->count_records('user_enrolments'));
        $this->assertEquals(2, $DB->count_records('role_assignments'));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));

        $selfplugin->enrol_user($instance1, $user1->id, $studentrole->id);
        $selfplugin->enrol_user($instance1, $user2->id, $studentrole->id);
        $selfplugin->enrol_user($instance1, $user3->id, $studentrole->id, 0, $now-60);

        $selfplugin->enrol_user($instance3, $user1->id, $studentrole->id, 0, 0);
        $selfplugin->enrol_user($instance3, $user2->id, $studentrole->id, 0, $now-60*60);
        $selfplugin->enrol_user($instance3, $user3->id, $studentrole->id, 0, $now+60*60);
        $selfplugin->enrol_user($instance3b, $user1->id, $teacherrole->id, $now-60*60*24*7, $now-60);
        $selfplugin->enrol_user($instance3b, $user4->id, $teacherrole->id);

        role_assign($managerrole->id, $user3->id, $context1->id);

        $this->assertEquals(10, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(7, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));

        // Execute tests.

        $this->assertEquals(ENROL_EXT_REMOVED_KEEP, $selfplugin->get_config('expiredaction'));
        $selfplugin->sync(null, false);
        $this->assertEquals(10, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));


        $selfplugin->set_config('expiredaction', ENROL_EXT_REMOVED_SUSPENDNOROLES);
        $selfplugin->sync($course2->id, false);
        $this->assertEquals(10, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));

        $selfplugin->sync(null, false);
        $this->assertEquals(10, $DB->count_records('user_enrolments'));
        $this->assertEquals(7, $DB->count_records('role_assignments'));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user3->id, 'roleid'=>$studentrole->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context3->id, 'userid'=>$user2->id, 'roleid'=>$studentrole->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context3->id, 'userid'=>$user1->id, 'roleid'=>$teacherrole->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context3->id, 'userid'=>$user1->id, 'roleid'=>$studentrole->id)));


        $selfplugin->set_config('expiredaction', ENROL_EXT_REMOVED_UNENROL);

        role_assign($studentrole->id, $user3->id, $context1->id);
        role_assign($studentrole->id, $user2->id, $context3->id);
        role_assign($teacherrole->id, $user1->id, $context3->id);
        $this->assertEquals(10, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(7, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));

        $selfplugin->sync(null, false);
        $this->assertEquals(7, $DB->count_records('user_enrolments'));
        $this->assertFalse($DB->record_exists('user_enrolments', array('enrolid'=>$instance1->id, 'userid'=>$user3->id)));
        $this->assertFalse($DB->record_exists('user_enrolments', array('enrolid'=>$instance3->id, 'userid'=>$user2->id)));
        $this->assertFalse($DB->record_exists('user_enrolments', array('enrolid'=>$instance3b->id, 'userid'=>$user1->id)));
        $this->assertEquals(6, $DB->count_records('role_assignments'));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));
    }
}
