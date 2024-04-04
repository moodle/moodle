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
 * Manual enrolment tests.
 *
 * @package    enrol_manual
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace enrol_manual;

use course_enrolment_manager;
use stdClass;

defined('MOODLE_INTERNAL') || die();


/**
 * Manual enrolment tests.
 *
 * @package    enrol_manual
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends \advanced_testcase {
    /**
     * Test enrol migration function used when uninstalling enrol plugins.
     */
    public function test_migrate_plugin_enrolments() {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/enrol/manual/locallib.php');

        $this->resetAfterTest();

        /** @var $manplugin enrol_manual_plugin */
        $manplugin = enrol_get_plugin('manual');

        // Setup a few courses and users.

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $teacherrole = $DB->get_record('role', array('shortname'=>'teacher'));
        $this->assertNotEmpty($teacherrole);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();
        $course4 = $this->getDataGenerator()->create_course();
        $course5 = $this->getDataGenerator()->create_course();

        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);
        $context3 = \context_course::instance($course3->id);
        $context4 = \context_course::instance($course4->id);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        // We expect manual, self and guest instances to be created by default.

        $this->assertEquals(5, $DB->count_records('enrol', array('enrol'=>'manual')));
        $this->assertEquals(5, $DB->count_records('enrol', array('enrol'=>'self')));
        $this->assertEquals(5, $DB->count_records('enrol', array('enrol'=>'guest')));
        $this->assertEquals(15, $DB->count_records('enrol', array()));

        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));

        // Enrol some users to manual instances.

        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $DB->set_field('enrol', 'status', ENROL_INSTANCE_DISABLED, array('id'=>$maninstance1->id));
        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $DB->delete_records('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual'));
        $DB->delete_records('enrol', array('courseid'=>$course4->id, 'enrol'=>'manual'));
        $DB->delete_records('enrol', array('courseid'=>$course5->id, 'enrol'=>'manual'));

        $manplugin->enrol_user($maninstance1, $user1->id, $studentrole->id);
        $manplugin->enrol_user($maninstance1, $user2->id, $studentrole->id);
        $manplugin->enrol_user($maninstance1, $user3->id, $teacherrole->id);
        $manplugin->enrol_user($maninstance2, $user3->id, $teacherrole->id);

        $this->assertEquals(4, $DB->count_records('user_enrolments', array()));

        // Set up some bogus enrol plugin instances and enrolments.

        $xxxinstance1 = $DB->insert_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'xxx', 'status'=>ENROL_INSTANCE_ENABLED));
        $xxxinstance1 = $DB->get_record('enrol', array('id'=>$xxxinstance1));
        $xxxinstance3 = $DB->insert_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'xxx', 'status'=>ENROL_INSTANCE_DISABLED));
        $xxxinstance3 = $DB->get_record('enrol', array('id'=>$xxxinstance3));
        $xxxinstance4 = $DB->insert_record('enrol', array('courseid'=>$course4->id, 'enrol'=>'xxx', 'status'=>ENROL_INSTANCE_ENABLED));
        $xxxinstance4 = $DB->get_record('enrol', array('id'=>$xxxinstance4));
        $xxxinstance4b = $DB->insert_record('enrol', array('courseid'=>$course4->id, 'enrol'=>'xxx', 'status'=>ENROL_INSTANCE_DISABLED));
        $xxxinstance4b = $DB->get_record('enrol', array('id'=>$xxxinstance4b));


        $DB->insert_record('user_enrolments', array('enrolid'=>$xxxinstance1->id, 'userid'=>$user1->id, 'status'=>ENROL_USER_SUSPENDED));
        role_assign($studentrole->id, $user1->id, $context1->id, 'enrol_xxx', $xxxinstance1->id);
        role_assign($teacherrole->id, $user1->id, $context1->id, 'enrol_xxx', $xxxinstance1->id);
        $DB->insert_record('user_enrolments', array('enrolid'=>$xxxinstance1->id, 'userid'=>$user4->id, 'status'=>ENROL_USER_ACTIVE));
        role_assign($studentrole->id, $user4->id, $context1->id, 'enrol_xxx', $xxxinstance1->id);
        $this->assertEquals(2, $DB->count_records('user_enrolments', array('enrolid'=>$xxxinstance1->id)));
        $this->assertEquals(6, $DB->count_records('role_assignments', array('contextid'=>$context1->id)));


        $DB->insert_record('user_enrolments', array('enrolid'=>$xxxinstance3->id, 'userid'=>$user1->id, 'status'=>ENROL_USER_ACTIVE));
        role_assign($studentrole->id, $user1->id, $context3->id, 'enrol_xxx', $xxxinstance3->id);
        $DB->insert_record('user_enrolments', array('enrolid'=>$xxxinstance3->id, 'userid'=>$user2->id, 'status'=>ENROL_USER_SUSPENDED));
        $this->assertEquals(2, $DB->count_records('user_enrolments', array('enrolid'=>$xxxinstance3->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('contextid'=>$context3->id)));

        $DB->insert_record('user_enrolments', array('enrolid'=>$xxxinstance4->id, 'userid'=>$user1->id, 'status'=>ENROL_USER_ACTIVE));
        role_assign($studentrole->id, $user1->id, $context4->id, 'enrol_xxx', $xxxinstance4->id);
        $DB->insert_record('user_enrolments', array('enrolid'=>$xxxinstance4->id, 'userid'=>$user2->id, 'status'=>ENROL_USER_ACTIVE));
        role_assign($studentrole->id, $user2->id, $context4->id, 'enrol_xxx', $xxxinstance4->id);
        $DB->insert_record('user_enrolments', array('enrolid'=>$xxxinstance4b->id, 'userid'=>$user1->id, 'status'=>ENROL_USER_SUSPENDED));
        role_assign($teacherrole->id, $user1->id, $context4->id, 'enrol_xxx', $xxxinstance4b->id);
        $DB->insert_record('user_enrolments', array('enrolid'=>$xxxinstance4b->id, 'userid'=>$user4->id, 'status'=>ENROL_USER_ACTIVE));
        role_assign($teacherrole->id, $user4->id, $context4->id, 'enrol_xxx', $xxxinstance4b->id);
        $this->assertEquals(2, $DB->count_records('user_enrolments', array('enrolid'=>$xxxinstance4->id)));
        $this->assertEquals(2, $DB->count_records('user_enrolments', array('enrolid'=>$xxxinstance4b->id)));
        $this->assertEquals(4, $DB->count_records('role_assignments', array('contextid'=>$context4->id)));

        // Finally do the migration.

        enrol_manual_migrate_plugin_enrolments('xxx');

        // Verify results.

        $this->assertEquals(1, $DB->count_records('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual')));
        $this->assertEquals(1, $DB->count_records('enrol', array('courseid'=>$course1->id, 'enrol'=>'xxx')));
        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $this->assertEquals(ENROL_INSTANCE_DISABLED, $maninstance1->status);
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance1->id, 'userid'=>$user1->id, 'status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance1->id, 'userid'=>$user2->id, 'status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance1->id, 'userid'=>$user3->id, 'status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance1->id, 'userid'=>$user4->id, 'status'=>ENROL_USER_ACTIVE)));
        $this->assertEquals(4, $DB->count_records('user_enrolments', array('enrolid'=>$maninstance1->id)));
        $this->assertEquals(0, $DB->count_records('user_enrolments', array('enrolid'=>$xxxinstance1->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('itemid'=>0, 'component'=>'', 'userid'=>$user1->id, 'roleid'=>$studentrole->id, 'contextid'=>$context1->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('itemid'=>0, 'component'=>'', 'userid'=>$user1->id, 'roleid'=>$teacherrole->id, 'contextid'=>$context1->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('itemid'=>0, 'component'=>'', 'userid'=>$user2->id, 'roleid'=>$studentrole->id, 'contextid'=>$context1->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('itemid'=>0, 'component'=>'', 'userid'=>$user3->id, 'roleid'=>$teacherrole->id, 'contextid'=>$context1->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('itemid'=>0, 'component'=>'', 'userid'=>$user4->id, 'roleid'=>$studentrole->id, 'contextid'=>$context1->id)));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('contextid'=>$context1->id)));


        $this->assertEquals(1, $DB->count_records('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual')));
        $this->assertEquals(0, $DB->count_records('enrol', array('courseid'=>$course2->id, 'enrol'=>'xxx')));
        $maninstance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $this->assertEquals(ENROL_INSTANCE_ENABLED, $maninstance2->status);


        $this->assertEquals(1, $DB->count_records('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual')));
        $this->assertEquals(1, $DB->count_records('enrol', array('courseid'=>$course3->id, 'enrol'=>'xxx')));
        $maninstance3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $this->assertEquals(ENROL_INSTANCE_DISABLED, $maninstance3->status);
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance3->id, 'userid'=>$user1->id, 'status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance3->id, 'userid'=>$user2->id, 'status'=>ENROL_USER_SUSPENDED)));
        $this->assertEquals(2, $DB->count_records('user_enrolments', array('enrolid'=>$maninstance3->id)));
        $this->assertEquals(0, $DB->count_records('user_enrolments', array('enrolid'=>$xxxinstance3->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('itemid'=>0, 'component'=>'', 'userid'=>$user1->id, 'roleid'=>$studentrole->id, 'contextid'=>$context3->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('contextid'=>$context3->id)));


        $this->assertEquals(1, $DB->count_records('enrol', array('courseid'=>$course4->id, 'enrol'=>'manual')));
        $this->assertEquals(2, $DB->count_records('enrol', array('courseid'=>$course4->id, 'enrol'=>'xxx')));
        $maninstance4 = $DB->get_record('enrol', array('courseid'=>$course4->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $this->assertEquals(ENROL_INSTANCE_ENABLED, $maninstance4->status);
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance4->id, 'userid'=>$user1->id, 'status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance4->id, 'userid'=>$user2->id, 'status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance4->id, 'userid'=>$user4->id, 'status'=>ENROL_USER_SUSPENDED)));
        $this->assertEquals(3, $DB->count_records('user_enrolments', array('enrolid'=>$maninstance4->id)));
        $this->assertEquals(0, $DB->count_records('user_enrolments', array('enrolid'=>$xxxinstance4->id)));
        $this->assertEquals(0, $DB->count_records('user_enrolments', array('enrolid'=>$xxxinstance4b->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('itemid'=>0, 'component'=>'', 'userid'=>$user1->id, 'roleid'=>$studentrole->id, 'contextid'=>$context4->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('itemid'=>0, 'component'=>'', 'userid'=>$user1->id, 'roleid'=>$teacherrole->id, 'contextid'=>$context4->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('itemid'=>0, 'component'=>'', 'userid'=>$user2->id, 'roleid'=>$studentrole->id, 'contextid'=>$context4->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('itemid'=>0, 'component'=>'', 'userid'=>$user4->id, 'roleid'=>$teacherrole->id, 'contextid'=>$context4->id)));
        $this->assertEquals(4, $DB->count_records('role_assignments', array('contextid'=>$context4->id)));


        $this->assertEquals(0, $DB->count_records('enrol', array('courseid'=>$course5->id, 'enrol'=>'manual')));
        $this->assertEquals(0, $DB->count_records('enrol', array('courseid'=>$course5->id, 'enrol'=>'xxx')));

        // Make sure wrong params do not produce errors or notices.

        enrol_manual_migrate_plugin_enrolments('manual');
        enrol_manual_migrate_plugin_enrolments('yyyy');
    }

    public function test_expired() {
        global $DB;
        $this->resetAfterTest();

        /** @var $manualplugin enrol_manual_plugin */
        $manualplugin = enrol_get_plugin('manual');

        $trace = new \null_progress_trace();

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
        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);
        $context3 = \context_course::instance($course3->id);

        $this->assertEquals(3, $DB->count_records('enrol', array('enrol'=>'manual')));
        $instance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $this->assertEquals($studentrole->id, $instance1->roleid);
        $instance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $this->assertEquals($studentrole->id, $instance2->roleid);
        $instance3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $this->assertEquals($studentrole->id, $instance3->roleid);

        $this->assertEquals(0, $DB->count_records('user_enrolments'));
        $this->assertEquals(0, $DB->count_records('role_assignments'));

        $manualplugin->enrol_user($instance1, $user1->id, $studentrole->id);
        $manualplugin->enrol_user($instance1, $user2->id, $studentrole->id);
        $manualplugin->enrol_user($instance1, $user3->id, $studentrole->id, 0, $now-60);

        $manualplugin->enrol_user($instance3, $user1->id, $studentrole->id, 0, 0);
        $manualplugin->enrol_user($instance3, $user2->id, $studentrole->id, 0, $now+60*60);
        $manualplugin->enrol_user($instance3, $user3->id, $teacherrole->id, 0, $now-60*60);

        role_assign($managerrole->id, $user4->id, $context1->id);

        $this->assertEquals(6, $DB->count_records('user_enrolments'));
        $this->assertEquals(7, $DB->count_records('role_assignments'));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$managerrole->id)));

        // Execute tests.

        $this->assertEquals(ENROL_EXT_REMOVED_KEEP, $manualplugin->get_config('expiredaction'));
        $manualplugin->sync($trace, null);
        $this->assertEquals(6, $DB->count_records('user_enrolments'));
        $this->assertEquals(7, $DB->count_records('role_assignments'));


        $manualplugin->set_config('expiredaction', ENROL_EXT_REMOVED_SUSPENDNOROLES);
        $manualplugin->sync($trace, $course2->id);
        $this->assertEquals(6, $DB->count_records('user_enrolments'));
        $this->assertEquals(7, $DB->count_records('role_assignments'));

        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user3->id, 'roleid'=>$studentrole->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context3->id, 'userid'=>$user3->id, 'roleid'=>$teacherrole->id)));
        $manualplugin->sync($trace, null);
        $this->assertEquals(6, $DB->count_records('user_enrolments'));
        $this->assertEquals(5, $DB->count_records('role_assignments'));
        $this->assertEquals(4, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(0, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user3->id, 'roleid'=>$studentrole->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context3->id, 'userid'=>$user3->id, 'roleid'=>$teacherrole->id)));


        $manualplugin->set_config('expiredaction', ENROL_EXT_REMOVED_UNENROL);

        role_assign($studentrole->id, $user3->id, $context1->id);
        role_assign($teacherrole->id, $user3->id, $context3->id);
        $this->assertEquals(6, $DB->count_records('user_enrolments'));
        $this->assertEquals(7, $DB->count_records('role_assignments'));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$managerrole->id)));

        $manualplugin->sync($trace, null);
        $this->assertEquals(4, $DB->count_records('user_enrolments'));
        $this->assertFalse($DB->record_exists('user_enrolments', array('enrolid'=>$instance1->id, 'userid'=>$user3->id)));
        $this->assertFalse($DB->record_exists('user_enrolments', array('enrolid'=>$instance3->id, 'userid'=>$user3->id)));
        $this->assertEquals(5, $DB->count_records('role_assignments'));
        $this->assertEquals(4, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(0, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$managerrole->id)));


        $manualplugin->set_config('expiredaction', ENROL_EXT_REMOVED_SUSPEND);
        $manualplugin->enrol_user($instance1, $user3->id, $studentrole->id, 0, $now-60);
        $manualplugin->enrol_user($instance3, $user3->id, $teacherrole->id, 0, $now-60*60);
        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance2 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual'), '*', MUST_EXIST);

        $this->assertEquals(6, $DB->count_records('user_enrolments'));
        $this->assertEquals(7, $DB->count_records('role_assignments'));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance1->id, 'userid'=>$user3->id, 'status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance2->id, 'userid'=>$user3->id, 'status'=>ENROL_USER_ACTIVE)));

        $manualplugin->sync($trace, null);
        $this->assertEquals(6, $DB->count_records('user_enrolments'));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$instance1->id, 'userid'=>$user3->id)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$instance3->id, 'userid'=>$user3->id)));
        $this->assertEquals(7, $DB->count_records('role_assignments'));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance1->id, 'userid'=>$user3->id, 'status'=>ENROL_USER_SUSPENDED)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance2->id, 'userid'=>$user3->id, 'status'=>ENROL_USER_SUSPENDED)));
    }

    public function test_send_expiry_notifications() {
        global $DB, $CFG;
        $this->resetAfterTest();
        $this->preventResetByRollback(); // Messaging does not like transactions...

        $trace = new \null_progress_trace();

        /** @var $manualplugin enrol_manual_plugin */
        $manualplugin = enrol_get_plugin('manual');
        $now = time();
        $admin = get_admin();

        // Note: hopefully nobody executes the unit tests the last second before midnight...

        $manualplugin->set_config('expirynotifylast', $now - 60*60*24);
        $manualplugin->set_config('expirynotifyhour', 0);

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $editingteacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'));
        $this->assertNotEmpty($editingteacherrole);
        $managerrole = $DB->get_record('role', array('shortname'=>'manager'));
        $this->assertNotEmpty($managerrole);

        $user1 = $this->getDataGenerator()->create_user(array('lastname'=>'xuser1'));
        $user2 = $this->getDataGenerator()->create_user(array('lastname'=>'xuser2'));
        $user3 = $this->getDataGenerator()->create_user(array('lastname'=>'xuser3'));
        $user4 = $this->getDataGenerator()->create_user(array('lastname'=>'xuser4'));
        $user5 = $this->getDataGenerator()->create_user(array('lastname'=>'xuser5'));
        $user6 = $this->getDataGenerator()->create_user(array('lastname'=>'xuser6'));
        $user7 = $this->getDataGenerator()->create_user(array('lastname'=>'xuser6'));
        $user8 = $this->getDataGenerator()->create_user(array('lastname'=>'xuser6'));

        $course1 = $this->getDataGenerator()->create_course(array('fullname'=>'xcourse1'));
        $course2 = $this->getDataGenerator()->create_course(array('fullname'=>'xcourse2'));
        $course3 = $this->getDataGenerator()->create_course(array('fullname'=>'xcourse3'));
        $course4 = $this->getDataGenerator()->create_course(array('fullname'=>'xcourse4'));

        $this->assertEquals(4, $DB->count_records('enrol', array('enrol'=>'manual')));

        $instance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $instance1->expirythreshold = 60*60*24*4;
        $instance1->expirynotify    = 1;
        $instance1->notifyall       = 1;
        $DB->update_record('enrol', $instance1);

        $instance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $instance2->expirythreshold = 60*60*24*1;
        $instance2->expirynotify    = 1;
        $instance2->notifyall       = 1;
        $DB->update_record('enrol', $instance2);

        $instance3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $instance3->expirythreshold = 60*60*24*1;
        $instance3->expirynotify    = 1;
        $instance3->notifyall       = 0;
        $DB->update_record('enrol', $instance3);

        $instance4 = $DB->get_record('enrol', array('courseid'=>$course4->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $instance4->expirythreshold = 60*60*24*1;
        $instance4->expirynotify    = 0;
        $instance4->notifyall       = 0;
        $DB->update_record('enrol', $instance4);

        $manualplugin->enrol_user($instance1, $user1->id, $editingteacherrole->id, 0, $now + 60*60*24*1, ENROL_USER_SUSPENDED); // Suspended users are not notified.
        $manualplugin->enrol_user($instance1, $user2->id, $studentrole->id, 0, $now + 60*60*24*5);                       // Above threshold are not notified.
        $manualplugin->enrol_user($instance1, $user3->id, $studentrole->id, 0, $now + 60*60*24*3 + 60*60);               // Less than one day after threshold - should be notified.
        $manualplugin->enrol_user($instance1, $user4->id, $studentrole->id, 0, $now + 60*60*24*4 - 60*3);                // Less than one day after threshold - should be notified.
        $manualplugin->enrol_user($instance1, $user5->id, $studentrole->id, 0, $now + 60*60);                            // Should have been already notified.
        $manualplugin->enrol_user($instance1, $user6->id, $studentrole->id, 0, $now - 60);                               // Already expired.
        $manualplugin->enrol_user($instance1, $user7->id, $editingteacherrole->id);
        $manualplugin->enrol_user($instance1, $user8->id, $managerrole->id);                                             // Highest role --> enroller.

        $manualplugin->enrol_user($instance2, $user1->id, $studentrole->id);
        $manualplugin->enrol_user($instance2, $user2->id, $studentrole->id, 0, $now + 60*60*24*1 + 60*3);                // Above threshold are not notified.
        $manualplugin->enrol_user($instance2, $user3->id, $studentrole->id, 0, $now + 60*60*24*1 - 60*60);               // Less than one day after threshold - should be notified.

        $manualplugin->enrol_user($instance3, $user1->id, $editingteacherrole->id);
        $manualplugin->enrol_user($instance3, $user2->id, $studentrole->id, 0, $now + 60*60*24*1 + 60);                  // Above threshold are not notified.
        $manualplugin->enrol_user($instance3, $user3->id, $studentrole->id, 0, $now + 60*60*24*1 - 60*60);               // Less than one day after threshold - should be notified.

        $manualplugin->enrol_user($instance4, $user4->id, $editingteacherrole->id);
        $manualplugin->enrol_user($instance4, $user5->id, $studentrole->id, 0, $now + 60*60*24*1 + 60);
        $manualplugin->enrol_user($instance4, $user6->id, $studentrole->id, 0, $now + 60*60*24*1 - 60*60);

        // The notification is sent out in fixed order first individual users,
        // then summary per course by enrolid, user lastname, etc.
        $this->assertGreaterThan($instance1->id, $instance2->id);
        $this->assertGreaterThan($instance2->id, $instance3->id);

        $sink = $this->redirectMessages();

        $manualplugin->send_expiry_notifications($trace);

        $messages = $sink->get_messages();

        $this->assertEquals(2+1 + 1+1 + 1 + 0, count($messages));

        // First individual notifications from course1.
        $this->assertEquals($user3->id, $messages[0]->useridto);
        $this->assertEquals($user8->id, $messages[0]->useridfrom);
        $this->assertStringContainsString('xcourse1', $messages[0]->fullmessagehtml);

        $this->assertEquals($user4->id, $messages[1]->useridto);
        $this->assertEquals($user8->id, $messages[1]->useridfrom);
        $this->assertStringContainsString('xcourse1', $messages[1]->fullmessagehtml);

        // Then summary for course1.
        $this->assertEquals($user8->id, $messages[2]->useridto);
        $this->assertEquals($admin->id, $messages[2]->useridfrom);
        $this->assertStringContainsString('xcourse1', $messages[2]->fullmessagehtml);
        $this->assertStringNotContainsString('xuser1', $messages[2]->fullmessagehtml);
        $this->assertStringNotContainsString('xuser2', $messages[2]->fullmessagehtml);
        $this->assertStringContainsString('xuser3', $messages[2]->fullmessagehtml);
        $this->assertStringContainsString('xuser4', $messages[2]->fullmessagehtml);
        $this->assertStringContainsString('xuser5', $messages[2]->fullmessagehtml);
        $this->assertStringNotContainsString('xuser6', $messages[2]->fullmessagehtml);

        // First individual notifications from course2.
        $this->assertEquals($user3->id, $messages[3]->useridto);
        $this->assertEquals($admin->id, $messages[3]->useridfrom);
        $this->assertStringContainsString('xcourse2', $messages[3]->fullmessagehtml);

        // Then summary for course2.
        $this->assertEquals($admin->id, $messages[4]->useridto);
        $this->assertEquals($admin->id, $messages[4]->useridfrom);
        $this->assertStringContainsString('xcourse2', $messages[4]->fullmessagehtml);
        $this->assertStringNotContainsString('xuser1', $messages[4]->fullmessagehtml);
        $this->assertStringNotContainsString('xuser2', $messages[4]->fullmessagehtml);
        $this->assertStringContainsString('xuser3', $messages[4]->fullmessagehtml);
        $this->assertStringNotContainsString('xuser4', $messages[4]->fullmessagehtml);
        $this->assertStringNotContainsString('xuser5', $messages[4]->fullmessagehtml);
        $this->assertStringNotContainsString('xuser6', $messages[4]->fullmessagehtml);

        // Only summary in course3.
        $this->assertEquals($user1->id, $messages[5]->useridto);
        $this->assertEquals($admin->id, $messages[5]->useridfrom);
        $this->assertStringContainsString('xcourse3', $messages[5]->fullmessagehtml);
        $this->assertStringNotContainsString('xuser1', $messages[5]->fullmessagehtml);
        $this->assertStringNotContainsString('xuser2', $messages[5]->fullmessagehtml);
        $this->assertStringContainsString('xuser3', $messages[5]->fullmessagehtml);
        $this->assertStringNotContainsString('xuser4', $messages[5]->fullmessagehtml);
        $this->assertStringNotContainsString('xuser5', $messages[5]->fullmessagehtml);
        $this->assertStringNotContainsString('xuser6', $messages[5]->fullmessagehtml);


        // Make sure that notifications are not repeated.
        $sink->clear();

        $manualplugin->send_expiry_notifications($trace);
        $this->assertEquals(0, $sink->count());

        // use invalid notification hour to verify that before the hour the notifications are not sent.
        $manualplugin->set_config('expirynotifylast', time() - 60*60*24);
        $manualplugin->set_config('expirynotifyhour', '24');

        $manualplugin->send_expiry_notifications($trace);
        $this->assertEquals(0, $sink->count());

        $manualplugin->set_config('expirynotifyhour', '0');
        $manualplugin->send_expiry_notifications($trace);
        $this->assertEquals(6, $sink->count());
    }

    /**
     * Test for getting user enrolment actions.
     */
    public function test_get_user_enrolment_actions() {
        global $CFG, $PAGE;
        $this->resetAfterTest();

        // Set page URL to prevent debugging messages.
        $PAGE->set_url('/enrol/editinstance.php');

        $pluginname = 'manual';

        // Only enable the manual enrol plugin.
        $CFG->enrol_plugins_enabled = $pluginname;

        $generator = $this->getDataGenerator();

        // Get the enrol plugin.
        $plugin = enrol_get_plugin($pluginname);

        // Create a course.
        $course = $generator->create_course();
        // Enable this enrol plugin for the course.
        $plugin->add_instance($course);

        // Create a teacher.
        $teacher = $generator->create_user();
        // Enrol the teacher to the course.
        $generator->enrol_user($teacher->id, $course->id, 'editingteacher', $pluginname);
        // Create a student.
        $student = $generator->create_user();
        // Enrol the student to the course.
        $generator->enrol_user($student->id, $course->id, 'student', $pluginname);

        // Login as the teacher.
        $this->setUser($teacher);
        require_once($CFG->dirroot . '/enrol/locallib.php');
        $manager = new course_enrolment_manager($PAGE, $course);
        $userenrolments = $manager->get_user_enrolments($student->id);
        $this->assertCount(1, $userenrolments);

        $ue = reset($userenrolments);
        $actions = $plugin->get_user_enrolment_actions($manager, $ue);
        // Manual enrol has 2 enrol actions -- edit and unenrol.
        $this->assertCount(2, $actions);
    }

    /**
     * Test how the default enrolment instance inherits its settings from the global plugin settings.
     *
     * @dataProvider default_enrolment_instance_data_provider
     * @param stdClass $expectation
     * @param stdClass $globalsettings
     * @covers \enrol_manual::add_default_instance
     */
    public function test_default_enrolment_instance_acquires_correct_settings(stdClass $expectation, stdClass $globalsettings) {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Given the plugin is globally configured with the following settings.
        $plugin = enrol_get_plugin('manual');
        $plugin->set_config('status', $globalsettings->status);
        $plugin->set_config('roleid', $globalsettings->roleid);
        $plugin->set_config('enrolperiod', $globalsettings->enrolperiod);
        $plugin->set_config('expirynotify', $globalsettings->expirynotify);
        $plugin->set_config('expirythreshold', $globalsettings->expirythreshold);

        // When creating a course.
        $course = $generator->create_course();

        // Then the default manual enrolment instance being created is properly configured.
        $enrolinstance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);
        $this->assertEquals($expectation->status, $enrolinstance->status);
        $this->assertEquals($expectation->roleid, $enrolinstance->roleid);
        $this->assertEquals($expectation->enrolperiod, $enrolinstance->enrolperiod);
        $this->assertEquals($expectation->expirynotify, $enrolinstance->expirynotify);
        $this->assertEquals($expectation->notifyall, $enrolinstance->notifyall);
        $this->assertEquals($expectation->expirythreshold, $enrolinstance->expirythreshold);
    }

    /**
     * Data provider for test_default_enrolment_instance_acquires_correct_settings().
     *
     * @return array
     */
    public function default_enrolment_instance_data_provider(): array {
        $studentroles = get_archetype_roles('student');
        $studentrole = array_shift($studentroles);

        $teacherroles = get_archetype_roles('teacher');
        $teacherrole = array_shift($teacherroles);

        return [
            'enabled, student role, no duration set, notify no one on expiry, 12 hours notification threshold' => [
                'expectation' => (object) [
                    'status' => ENROL_INSTANCE_ENABLED,
                    'roleid' => $studentrole->id,
                    'enrolperiod' => 0,
                    'expirynotify' => 0,
                    'notifyall' => 0,
                    'expirythreshold' => 12 * HOURSECS,
                ],
                'global settings' => (object) [
                    'status' => ENROL_INSTANCE_ENABLED,
                    'roleid' => $studentrole->id,
                    'enrolperiod' => 0,
                    'expirynotify' => 0,
                    'expirythreshold' => 12 * HOURSECS,
                ],
            ],
            'enabled, student role, 72 hours duration, notify enroller only on expiry, 1 day notification threshold' => [
                'expectation' => (object) [
                    'status' => ENROL_INSTANCE_ENABLED,
                    'roleid' => $studentrole->id,
                    'enrolperiod' => 72 * HOURSECS,
                    'expirynotify' => 1,
                    'notifyall' => 0,
                    'expirythreshold' => DAYSECS,
                ],
                'global settings' => (object) [
                    'status' => ENROL_INSTANCE_ENABLED,
                    'roleid' => $studentrole->id,
                    'enrolperiod' => 72 * HOURSECS,
                    'expirynotify' => 1,
                    'expirythreshold' => DAYSECS,
                ],
            ],
            'disabled, teacher role, no duration set, notify enroller and enrolled on expiry, 0 notification threshold' => [
                'expectation' => (object) [
                    'status' => ENROL_INSTANCE_DISABLED,
                    'roleid' => $teacherrole->id,
                    'enrolperiod' => 0,
                    'expirynotify' => 2,
                    'notifyall' => 1,
                    'expirythreshold' => 0
                ],
                'global settings' => (object) [
                    'status' => ENROL_INSTANCE_DISABLED,
                    'roleid' => $teacherrole->id,
                    'enrolperiod' => 0,
                    'expirynotify' => 2,
                    'expirythreshold' => 0,
                ],
            ],
        ];
    }

    /**
     * Tests an enrolment instance is updated properly.
     *
     * @covers \enrol_manual::update_instance
     * @dataProvider update_enrolment_instance_data_provider
     *
     * @param stdClass $expectation
     * @param stdClass $updatedata
     */
    public function test_enrolment_instance_is_updated(stdClass $expectation, stdClass $updatedata): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        $studentroles = get_archetype_roles('student');
        $studentrole = array_shift($studentroles);

        // Given the plugin is globally configured with the following settings.
        $plugin = enrol_get_plugin('manual');
        $plugin->set_config('status', ENROL_INSTANCE_ENABLED);
        $plugin->set_config('roleid', $studentrole->id);
        $plugin->set_config('enrolperiod', 30 * DAYSECS);
        $plugin->set_config('expirynotify', 1);
        $plugin->set_config('expirythreshold', 2 * DAYSECS);

        // And a course is created with the default enrolment instance.
        $course = $generator->create_course();

        // When the enrolment instance is being updated.
        $enrolinstance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);
        $successfullyupdated = $plugin->update_instance($enrolinstance, $updatedata);

        // Then the update is successful.
        $this->assertTrue($successfullyupdated);

        // And the updated enrolment instance contains the expected values.
        $enrolinstance = $DB->get_record('enrol', ['id' => $enrolinstance->id]);
        $this->assertEquals($expectation->status, $enrolinstance->status);
        $this->assertEquals($expectation->roleid, $enrolinstance->roleid);
        $this->assertEquals($expectation->enrolperiod, $enrolinstance->enrolperiod);
        $this->assertEquals($expectation->expirynotify, $enrolinstance->expirynotify);
        $this->assertEquals($expectation->notifyall, $enrolinstance->notifyall);
        $this->assertEquals($expectation->expirythreshold, $enrolinstance->expirythreshold);
    }

    /**
     * Data provider for test_enrolment_instance_is_updated().
     *
     * @return array
     */
    public function update_enrolment_instance_data_provider(): array {
        $studentroles = get_archetype_roles('student');
        $studentrole = array_shift($studentroles);

        $teacherroles = get_archetype_roles('teacher');
        $teacherrole = array_shift($teacherroles);

        return [
            'disabled, all the others are default' => [
                'expectation' => (object) [
                    'status' => ENROL_INSTANCE_DISABLED,
                    'roleid' => $studentrole->id,
                    'enrolperiod' => 30 * DAYSECS,
                    'expirynotify' => 1,
                    'notifyall' => 0,
                    'expirythreshold' => 2 * DAYSECS,
                ],
                'update data' => (object) [
                    'status' => ENROL_INSTANCE_DISABLED,
                    'roleid' => $studentrole->id,
                    'enrolperiod' => 30 * DAYSECS,
                    'expirynotify' => 1,
                    'expirythreshold' => 2 * DAYSECS,
                ],
            ],
            'enabled, teacher role, no duration set, notify no one on expiry, 0 notification threshold' => [
                'expectation' => (object) [
                    'status' => ENROL_INSTANCE_ENABLED,
                    'roleid' => $teacherrole->id,
                    'enrolperiod' => 0,
                    'expirynotify' => 0,
                    'notifyall' => 0,
                    'expirythreshold' => 0,
                ],
                'update data' => (object) [
                    'status' => ENROL_INSTANCE_ENABLED,
                    'roleid' => $teacherrole->id,
                    'enrolperiod' => 0,
                    'expirynotify' => 0,
                    'expirythreshold' => 0,
                ],
            ],
            'notify enroller and enrolled on expiry, all the others are default' => [
                'expectation' => (object) [
                    'status' => ENROL_INSTANCE_ENABLED,
                    'roleid' => $studentrole->id,
                    'enrolperiod' => 30 * DAYSECS,
                    'expirynotify' => 2,
                    'notifyall' => 1,
                    'expirythreshold' => 2 * DAYSECS,
                ],
                'update data' => (object) [
                    'status' => ENROL_INSTANCE_ENABLED,
                    'roleid' => $studentrole->id,
                    'enrolperiod' => 30 * DAYSECS,
                    'expirynotify' => 2,
                    'expirythreshold' => 2 * DAYSECS,
                ],
            ],
        ];
    }

    /**
     * Test the behaviour of find_instance().
     *
     * @covers ::find_instance
     */
    public function test_find_instance() {
        global $DB;
        $this->resetAfterTest();

        $cat = $this->getDataGenerator()->create_category();
        // When we create a course, a manual enrolment instance is also created.
        $course = $this->getDataGenerator()->create_course(['category' => $cat->id, 'shortname' => 'ANON']);

        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $manualplugin = enrol_get_plugin('manual');

        $expected = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);

        // Let's try to add second instance - only 1 manual instance is possible.
        $instanceid2 = $manualplugin->add_instance($course, ['roleid' => $teacherrole->id]);
        $this->assertNull($instanceid2);

        $enrolmentdata = [];
        $actual = $manualplugin->find_instance($enrolmentdata, $course->id);
        $this->assertEquals($expected->id, $actual->id);
    }

    /**
     * Test send_course_welcome_message_to_user() method.
     *
     * @covers \enrol_plugin::send_course_welcome_message_to_user
     */
    public function test_send_course_welcome_message(): void {
        global $DB;
        $this->resetAfterTest();

        // Create course.
        $course = $this->getDataGenerator()->create_course([
            'fullname' => 'Course 1',
            'shortname' => 'C1',
        ]);
        // Create users.
        $student = $this->getDataGenerator()->create_user();
        $teacher1 = $this->getDataGenerator()->create_user();
        $teacher2 = $this->getDataGenerator()->create_user();
        $noreplyuser = \core_user::get_noreply_user();
        // Enrol users.
        $this->getDataGenerator()->enrol_user($teacher1->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($teacher2->id, $course->id, 'editingteacher');
        // Get manual plugin.
        $manualplugin = enrol_get_plugin('manual');
        $maninstance = $DB->get_record(
            'enrol',
            ['courseid' => $course->id, 'enrol' => 'manual'],
            '*',
            MUST_EXIST,
        );

        // Test 1: Send welcome message to user from course contact with default message.
        // Redirect messages.
        $messagesink = $this->redirectMessages();
        $manualplugin->send_course_welcome_message_to_user(
            instance: $maninstance,
            userid: $student->id,
            sendoption: ENROL_SEND_EMAIL_FROM_COURSE_CONTACT,
            message: '',
        );
        $messages = $messagesink->get_messages_by_component_and_type(
            'moodle',
            'enrolcoursewelcomemessage',
        );
        $this->assertNotEmpty($messages);
        $message = reset($messages);

        // The message should be sent from the first teacher.
        $this->assertEquals($teacher1->id, $message->useridfrom);
        $this->assertStringContainsString($course->fullname, $message->subject);
        $this->assertEquals(
            get_string(
                'customwelcomemessageplaceholder',
                'core_enrol',
                ['fullname' => fullname($student), 'coursename' => $course->fullname],
            ),
            $message->fullmessage,
        );

        // Clear sink.
        $messagesink->clear();

        // Test 2: Send welcome message to user from course contact with a custom message.
        // Unenrol the first teacher from course.
        $manualplugin->unenrol_user($maninstance, $teacher1->id);
        // Redirect messages.
        $messagesink = $this->redirectMessages();
        $manualplugin->send_course_welcome_message_to_user(
            instance: $maninstance,
            userid: $student->id,
            sendoption: ENROL_SEND_EMAIL_FROM_COURSE_CONTACT,
            message: 'Your email address: {$a->email}, your first name: {$a->firstname}, your last name: {$a->lastname}',
        );
        $messages = $messagesink->get_messages_by_component_and_type(
            'moodle',
            'enrolcoursewelcomemessage',
        );
        $this->assertNotEmpty($messages);
        $message = reset($messages);

        // The message should be sent from the second teacher.
        $this->assertEquals($teacher2->id, $message->useridfrom);
        $this->assertStringContainsString($course->fullname, $message->subject);
        $this->assertEquals(
            'Your email address: ' . $student->email . ', your first name: ' . $student->firstname . ', your last name: ' .
                $student->lastname,
            $message->fullmessage,
        );
        // Clear sink.
        $messagesink->clear();

        // Test 3: Send welcome message to user from no-reply user with a custom message.
        // Redirect messages.
        $messagesink = $this->redirectMessages();
        $manualplugin->send_course_welcome_message_to_user(
            instance: $maninstance,
            userid: $student->id,
            sendoption: ENROL_SEND_EMAIL_FROM_NOREPLY,
            message: 'Your email address: {$a->email}, your first name: {$a->firstname}, your last name: {$a->lastname}',
        );
        $messages = $messagesink->get_messages_by_component_and_type(
            'moodle',
            'enrolcoursewelcomemessage',
        );
        $this->assertNotEmpty($messages);
        $message = reset($messages);

        // The message should be sent from the noreply user.
        $this->assertEquals($noreplyuser->id, $message->useridfrom);
        $this->assertStringContainsString($course->fullname, $message->subject);
        $this->assertEquals(
            'Your email address: ' . $student->email . ', your first name: ' . $student->firstname . ', your last name: ' .
            $student->lastname,
            $message->fullmessage,
        );
        // Clear sink.
        $messagesink->clear();

    }

    /**
     * Test send_course_welcome_message_to_user() method via hook.
     *
     * @covers \enrol_plugin::send_course_welcome_message_to_user
     */
    public function test_send_course_welcome_message_via_hook(): void {
        global $DB;
        $this->resetAfterTest();
        $messagesink = $this->redirectMessages();
        $course = $this->getDataGenerator()->create_course([
            'fullname' => 'Course 1',
            'shortname' => 'C1',
        ]);
        $maninstance = $DB->get_record(
            'enrol',
            ['courseid' => $course->id, 'enrol' => 'manual'],
            '*',
            MUST_EXIST,
        );
        $maninstance->customint1 = ENROL_SEND_EMAIL_FROM_NOREPLY;
        $DB->update_record('enrol', $maninstance);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id);
        $messages = $messagesink->get_messages_by_component_and_type(
            'moodle',
            'enrolcoursewelcomemessage',
        );
        $this->assertNotEmpty($messages);
        $message = reset($messages);
        $this->assertStringContainsString($course->fullname, $message->subject);
    }

}
