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
 * flatfile enrolment sync tests.
 *
 * @package    enrol_flatfile
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_flatfile;

use enrol_flatfile\task\flatfile_sync_task;

/**
 * flatfile enrolment sync tests.
 *
 * @package    enrol_flatfile
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class flatfile_test extends \advanced_testcase {

    protected function enable_plugin() {
        $enabled = enrol_get_plugins(true);
        $enabled['flatfile'] = true;
        $enabled = array_keys($enabled);
        set_config('enrol_plugins_enabled', implode(',', $enabled));
    }

    protected function disable_plugin() {
        $enabled = enrol_get_plugins(true);
        unset($enabled['flatfile']);
        $enabled = array_keys($enabled);
        set_config('enrol_plugins_enabled', implode(',', $enabled));
    }

    public function test_basics() {
        $this->assertFalse(enrol_is_enabled('flatfile'));
        $plugin = enrol_get_plugin('flatfile');
        $this->assertInstanceOf('enrol_flatfile_plugin', $plugin);
        $this->assertEquals(ENROL_EXT_REMOVED_SUSPENDNOROLES, get_config('enrol_flatfile', 'expiredaction'));
    }

    public function test_sync_nothing() {
        $this->resetAfterTest();

        $this->disable_plugin();
        $flatfileplugin = enrol_get_plugin('flatfile');

        // Just make sure the sync does not throw any errors when nothing to do.
        $flatfileplugin->sync(new \null_progress_trace());
        $this->enable_plugin();
        $flatfileplugin->sync(new \null_progress_trace());
    }

    public function test_sync() {
        global $CFG, $DB;
        $this->resetAfterTest();

        /** @var \enrol_flatfile_plugin $flatfileplugin  */
        $flatfileplugin = enrol_get_plugin('flatfile');
        /** @var \enrol_manual_plugin $manualplugin  */
        $manualplugin = enrol_get_plugin('manual');
        $this->assertNotEmpty($manualplugin);

        $trace = new \null_progress_trace();
        $this->enable_plugin();
        $file = "$CFG->dataroot/enrol.txt";

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $teacherrole = $DB->get_record('role', array('shortname'=>'teacher'));
        $this->assertNotEmpty($teacherrole);
        $managerrole = $DB->get_record('role', array('shortname'=>'manager'));
        $this->assertNotEmpty($managerrole);

        $user1 = $this->getDataGenerator()->create_user(array('idnumber'=>'u1'));
        $user2 = $this->getDataGenerator()->create_user(array('idnumber'=>'u2'));
        $user3 = $this->getDataGenerator()->create_user(array('idnumber'=>'u3'));
        $user4 = $this->getDataGenerator()->create_user(array('idnumber'=>'čtvrtý'));
        $user5 = $this->getDataGenerator()->create_user(array('idnumber'=>'u5'));
        $user6 = $this->getDataGenerator()->create_user(array('idnumber'=>'u6'));
        $user7 = $this->getDataGenerator()->create_user(array('idnumber'=>''));

        $course1 = $this->getDataGenerator()->create_course(array('idnumber'=>'c1'));
        $course2 = $this->getDataGenerator()->create_course(array('idnumber'=>'c2'));
        $course3 = $this->getDataGenerator()->create_course(array('idnumber'=>'c3'));
        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);
        $context3 = \context_course::instance($course3->id);

        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual'), '*', MUST_EXIST);

        // Rename teacher role.
        $flatfileplugin->set_config('map_'.$teacherrole->id, 'ucitel');
        // Disable manager role.
        $flatfileplugin->set_config('map_'.$managerrole->id, '');
        // Set file location.
        $flatfileplugin->set_config('location', $file);

        $now = time();
        $before = $now - 60;
        $future = $now + 60*60*5;
        $farfuture = $now + 60*60*24*5;


        // Test add action.

        $data ="'add','student','u1','c1'

            \"add\" , \"ucitel\", u2 , c2
            add,manager,u3,c1
            add,student,čtvrtý,c2,$before
            add,student,u5,c1,0,0,1
            add,student,u5,c2,20,10
            add,student,u6,c1,0,$future
            add,student,u6,c2,$future,0
            add,student,u6,c3,$future,$farfuture
            add,student,,c2";
        file_put_contents($file, $data);

        $this->assertEquals(0, $DB->count_records('user_enrolments'));
        $this->assertEquals(0, $DB->count_records('role_assignments'));
        $this->assertEquals(0, $DB->count_records('enrol_flatfile'));

        $this->assertTrue(file_exists($file));
        $flatfileplugin->sync($trace);
        $this->assertFalse(file_exists($file));

        $this->assertEquals(4, $DB->count_records('user_enrolments'));
        $this->assertEquals(4, $DB->count_records('role_assignments'));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user1->id, 'roleid'=>$studentrole->id, 'component'=>'enrol_flatfile')));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context2->id, 'userid'=>$user2->id, 'roleid'=>$teacherrole->id, 'component'=>'enrol_flatfile')));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context2->id, 'userid'=>$user4->id, 'roleid'=>$studentrole->id, 'component'=>'enrol_flatfile')));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user6->id, 'roleid'=>$studentrole->id, 'component'=>'enrol_flatfile')));


        // Test buffer.

        $this->assertEquals(2, $DB->count_records('enrol_flatfile'));

        $flatfileplugin->sync($trace);
        $this->assertEquals(2, $DB->count_records('enrol_flatfile'));
        $this->assertEquals(4, $DB->count_records('user_enrolments'));
        $this->assertEquals(4, $DB->count_records('role_assignments'));

        $DB->set_field('enrol_flatfile', 'timestart', time()-60, array('timestart'=>$future, 'timeend'=>0));

        $flatfileplugin->sync($trace);
        $this->assertEquals(1, $DB->count_records('enrol_flatfile'));
        $this->assertEquals(5, $DB->count_records('user_enrolments'));
        $this->assertEquals(5, $DB->count_records('role_assignments'));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context2->id, 'userid'=>$user6->id, 'roleid'=>$studentrole->id, 'component'=>'enrol_flatfile')));
        $this->assertTrue($DB->record_exists('enrol_flatfile', array('userid'=>$user6->id, 'roleid'=>$studentrole->id, 'timeend'=>$farfuture)));


        // Test encoding.

        $data = "add;student;čtvrtý;c3";
        $data = \core_text::convert($data, 'utf-8', 'iso-8859-2');
        file_put_contents($file, $data);
        $flatfileplugin->set_config('encoding', 'iso-8859-2');

        $flatfileplugin->sync($trace);
        $this->assertEquals(6, $DB->count_records('user_enrolments'));
        $this->assertEquals(6, $DB->count_records('role_assignments'));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context3->id, 'userid'=>$user4->id, 'roleid'=>$studentrole->id, 'component'=>'enrol_flatfile')));
        $flatfileplugin->set_config('encoding', 'UTF-8');

        // Test unenrolling purges buffer.

        $manualplugin->enrol_user($maninstance1, $user1->id, $teacherrole->id);
        $manualplugin->enrol_user($maninstance3, $user5->id, $teacherrole->id);

        $this->assertEquals(8, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(1, $DB->count_records('enrol_flatfile'));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user1->id, 'roleid'=>$teacherrole->id)));

        $flatfileplugin->set_config('unenrolaction', ENROL_EXT_REMOVED_KEEP);


        $data = "del,student,u1,c1\ndel,teacher,u6,c3";
        file_put_contents($file, $data);

        $flatfileplugin->sync($trace);
        $this->assertEquals(8, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(1, $DB->count_records('enrol_flatfile'));

        $data = "del,student,u6,c3";
        file_put_contents($file, $data);

        $flatfileplugin->sync($trace);
        $this->assertEquals(8, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(0, $DB->count_records('enrol_flatfile'));


        $flatfileplugin->set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPENDNOROLES);

        $data = "
            del,student,u1,c1
            del,grrr,u5,c1
            del,guest,u5,c2
            del,student,u6,c2
            del,ucitel,u5,c3";
        file_put_contents($file, $data);

        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user1->id, 'roleid'=>$studentrole->id, 'component'=>'enrol_flatfile')));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context2->id, 'userid'=>$user6->id, 'roleid'=>$studentrole->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context3->id, 'userid'=>$user5->id, 'roleid'=>$teacherrole->id)));

        $flatfileplugin->sync($trace);
        $this->assertEquals(8, $DB->count_records('user_enrolments'));
        $this->assertEquals(5, $DB->count_records('role_assignments'));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user1->id, 'roleid'=>$studentrole->id, 'component'=>'enrol_flatfile')));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user1->id, 'roleid'=>$teacherrole->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context2->id, 'userid'=>$user6->id, 'roleid'=>$studentrole->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context3->id, 'userid'=>$user5->id, 'roleid'=>$teacherrole->id)));


        $flatfileplugin->set_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL);

        $manualplugin->enrol_user($maninstance3, $user5->id, $teacherrole->id);
        $data = "
            add,student,u1,c1
            add,student,u6,c2";
        file_put_contents($file, $data);

        $flatfileplugin->sync($trace);

        $this->assertEquals(8, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(0, $DB->count_records('enrol_flatfile'));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user1->id, 'roleid'=>$studentrole->id, 'component'=>'enrol_flatfile')));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context2->id, 'userid'=>$user6->id, 'roleid'=>$studentrole->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context3->id, 'userid'=>$user5->id, 'roleid'=>$teacherrole->id)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('userid'=>$user5->id, 'enrolid'=>$maninstance3->id)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('userid'=>$user1->id, 'enrolid'=>$maninstance1->id)));

        $data = "
            del,student,u1,c1
            del,grrr,u5,c1
            del,guest,u5,c2
            del,student,u6,c2
            del,ucitel,u5,c3";
        file_put_contents($file, $data);

        $flatfileplugin->sync($trace);
        $this->assertEquals(5, $DB->count_records('user_enrolments'));
        $this->assertEquals(5, $DB->count_records('role_assignments'));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user1->id, 'roleid'=>$studentrole->id, 'component'=>'enrol_flatfile')));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user1->id, 'roleid'=>$teacherrole->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context2->id, 'userid'=>$user6->id, 'roleid'=>$studentrole->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context3->id, 'userid'=>$user5->id, 'roleid'=>$teacherrole->id)));
        $this->assertFalse($DB->record_exists('user_enrolments', array('userid'=>$user5->id, 'enrolid'=>$maninstance3->id)));
        $this->assertTrue($DB->record_exists('user_enrolments', array('userid'=>$user1->id, 'enrolid'=>$maninstance1->id)));
    }

    public function test_notification() {
        global $CFG, $DB;
        $this->resetAfterTest();

        $this->preventResetByRollback();

        /** @var \enrol_flatfile_plugin $flatfileplugin  */
        $flatfileplugin = enrol_get_plugin('flatfile');
        /** @var \enrol_manual_plugin $manualplugin  */
        $manualplugin = enrol_get_plugin('manual');
        $this->assertNotEmpty($manualplugin);

        $this->enable_plugin();

        $trace = new \progress_trace_buffer(new \text_progress_trace(), false);
        $file = "$CFG->dataroot/enrol.txt";
        $flatfileplugin->set_config('location', $file);

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'));
        $this->assertNotEmpty($teacherrole);

        $user1 = $this->getDataGenerator()->create_user(array('idnumber'=>'u1'));
        $user2 = $this->getDataGenerator()->create_user(array('idnumber'=>'u2'));
        $user3 = $this->getDataGenerator()->create_user(array('idnumber'=>'u3'));
        $admin = get_admin();

        $course1 = $this->getDataGenerator()->create_course(array('idnumber'=>'c1'));
        $course2 = $this->getDataGenerator()->create_course(array('idnumber'=>'c2'));
        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);

        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);

        $now = time();
        $future = $now + 60*60*5;
        $farfuture = $now + 60*60*24*5;

        $manualplugin->enrol_user($maninstance1, $user3->id, $teacherrole->id);

        $data =
            "add,student,u1,c1
            add,student,u2,c2
            add,student,u2,c1,$future,$farfuture";
        file_put_contents($file, $data);

        $this->assertEquals(1, $DB->count_records('user_enrolments'));
        $this->assertEquals(1, $DB->count_records('role_assignments'));
        $this->assertEquals(0, $DB->count_records('enrol_flatfile'));

        $flatfileplugin->set_config('mailadmins', 1);
        $flatfileplugin->set_config('mailteachers', 1);
        $flatfileplugin->set_config('mailstudents', 1);

        $sink = $this->redirectMessages();

        $flatfileplugin->sync($trace);

        $this->assertEquals(3, $DB->count_records('user_enrolments'));
        $this->assertEquals(3, $DB->count_records('role_assignments'));
        $this->assertEquals(1, $DB->count_records('enrol_flatfile'));

        $messages = $sink->get_messages();
        $this->assertCount(5, $messages);

        // Notify student from teacher.
        $this->assertEquals($user1->id, $messages[0]->useridto);
        $this->assertEquals($user3->id, $messages[0]->useridfrom);

        // Notify teacher.
        $this->assertEquals($user3->id, $messages[1]->useridto);
        $this->assertEquals($admin->id, $messages[1]->useridfrom);

        // Notify student when teacher not present.
        $this->assertEquals($user2->id, $messages[2]->useridto);
        $this->assertEquals($admin->id, $messages[2]->useridfrom);

        // Notify admin when teacher not present.
        $this->assertEquals($admin->id, $messages[3]->useridto);
        $this->assertEquals($admin->id, $messages[3]->useridfrom);

        // Sent report to admin from self.
        $this->assertEquals($admin->id, $messages[4]->useridto);
        $this->assertEquals($admin->id, $messages[4]->useridfrom);
    }

    public function test_expired() {
        global $DB;
        $this->resetAfterTest();

        /** @var \enrol_flatfile_plugin $flatfileplugin  */
        $flatfileplugin = enrol_get_plugin('flatfile');
        /** @var \enrol_manual_plugin $manualplugin  */
        $manualplugin = enrol_get_plugin('manual');
        $this->assertNotEmpty($manualplugin);

        $now = time();
        $trace = new \null_progress_trace();
        $this->enable_plugin();


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
        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);

        $data = array('roleid'=>$studentrole->id, 'courseid'=>$course1->id);
        $id = $flatfileplugin->add_instance($course1, $data);
        $instance1  = $DB->get_record('enrol', array('id'=>$id));
        $data = array('roleid'=>$studentrole->id, 'courseid'=>$course2->id);
        $id = $flatfileplugin->add_instance($course2, $data);
        $instance2 = $DB->get_record('enrol', array('id'=>$id));
        $data = array('roleid'=>$teacherrole->id, 'courseid'=>$course2->id);
        $id = $flatfileplugin->add_instance($course2, $data);
        $instance3 = $DB->get_record('enrol', array('id'=>$id));

        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'), '*', MUST_EXIST);

        $manualplugin->enrol_user($maninstance1, $user3->id, $studentrole->id);

        $this->assertEquals(1, $DB->count_records('user_enrolments'));
        $this->assertEquals(1, $DB->count_records('role_assignments'));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));

        $flatfileplugin->enrol_user($instance1, $user1->id, $studentrole->id);
        $flatfileplugin->enrol_user($instance1, $user2->id, $studentrole->id);
        $flatfileplugin->enrol_user($instance1, $user3->id, $studentrole->id, 0, $now-60);

        $flatfileplugin->enrol_user($instance2, $user1->id, $studentrole->id, 0, 0);
        $flatfileplugin->enrol_user($instance2, $user2->id, $studentrole->id, 0, $now-60*60);
        $flatfileplugin->enrol_user($instance2, $user3->id, $studentrole->id, 0, $now+60*60);

        $flatfileplugin->enrol_user($instance3, $user1->id, $teacherrole->id, $now-60*60*24*7, $now-60);
        $flatfileplugin->enrol_user($instance3, $user4->id, $teacherrole->id);

        role_assign($managerrole->id, $user3->id, $context1->id);

        $this->assertEquals(9, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(7, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$managerrole->id)));

        // Execute tests.

        $flatfileplugin->set_config('expiredaction', ENROL_EXT_REMOVED_KEEP);
        $code = $flatfileplugin->sync($trace);
        $this->assertSame(0, $code);
        $this->assertEquals(9, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));


        $flatfileplugin->set_config('expiredaction', ENROL_EXT_REMOVED_SUSPENDNOROLES);
        $flatfileplugin->sync($trace);
        $this->assertEquals(9, $DB->count_records('user_enrolments'));
        $this->assertEquals(7, $DB->count_records('role_assignments'));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$managerrole->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user3->id, 'roleid'=>$studentrole->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context2->id, 'userid'=>$user2->id, 'roleid'=>$studentrole->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context2->id, 'userid'=>$user1->id, 'roleid'=>$teacherrole->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context2->id, 'userid'=>$user1->id, 'roleid'=>$studentrole->id)));


        $flatfileplugin->set_config('expiredaction', ENROL_EXT_REMOVED_UNENROL);
        role_assign($studentrole->id, $user3->id, $context1->id, 'enrol_flatfile', $instance1->id);
        role_assign($studentrole->id, $user2->id, $context2->id, 'enrol_flatfile', $instance2->id);
        role_assign($teacherrole->id, $user1->id, $context2->id, 'enrol_flatfile', $instance3->id);
        $this->assertEquals(9, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(7, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$managerrole->id)));
        $flatfileplugin->sync($trace);
        $this->assertEquals(6, $DB->count_records('user_enrolments'));
        $this->assertFalse($DB->record_exists('user_enrolments', array('enrolid'=>$instance1->id, 'userid'=>$user3->id)));
        $this->assertFalse($DB->record_exists('user_enrolments', array('enrolid'=>$instance2->id, 'userid'=>$user2->id)));
        $this->assertFalse($DB->record_exists('user_enrolments', array('enrolid'=>$instance3->id, 'userid'=>$user1->id)));
        $this->assertEquals(6, $DB->count_records('role_assignments'));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('roleid'=>$studentrole->id)));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$teacherrole->id)));
        $this->assertEquals(0, $DB->count_records('role_assignments', array('roleid'=>$managerrole->id)));
    }

    /**
     * Flatfile enrolment sync task test.
     */
    public function test_flatfile_sync_task() {
        global $CFG, $DB;
        $this->resetAfterTest();

        $flatfileplugin = enrol_get_plugin('flatfile');

        $trace = new \null_progress_trace();
        $this->enable_plugin();
        $file = "$CFG->dataroot/enrol.txt";
        $flatfileplugin->set_config('location', $file);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->assertNotEmpty($studentrole);

        $user1 = $this->getDataGenerator()->create_user(array('idnumber' => 'u1'));
        $course1 = $this->getDataGenerator()->create_course(array('idnumber' => 'c1'));
        $context1 = \context_course::instance($course1->id);

        $data =
            "add,student,u1,c1";
        file_put_contents($file, $data);

        $task = new flatfile_sync_task;
        $task->execute();

        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid' => $studentrole->id)));
    }

    /**
     * Test for getting user enrolment actions.
     */
    public function test_get_user_enrolment_actions() {
        global $CFG, $PAGE;
        $this->resetAfterTest();

        // Set page URL to prevent debugging messages.
        $PAGE->set_url('/enrol/editinstance.php');

        $pluginname = 'flatfile';

        // Only enable the flatfile enrol plugin.
        $CFG->enrol_plugins_enabled = $pluginname;

        $generator = $this->getDataGenerator();

        // Get the enrol plugin.
        $plugin = enrol_get_plugin($pluginname);

        // Create a course.
        $course = $generator->create_course();
        // Enable this enrol plugin for the course.
        $plugin->add_instance($course);

        // Create a student.
        $student = $generator->create_user();
        // Enrol the student to the course.
        $generator->enrol_user($student->id, $course->id, 'student', $pluginname);

        // Teachers don't have enrol/flatfile:manage and enrol/flatfile:unenrol capabilities by default.
        // Login as admin for simplicity.
        $this->setAdminUser();

        require_once($CFG->dirroot . '/enrol/locallib.php');
        $manager = new \course_enrolment_manager($PAGE, $course);
        $userenrolments = $manager->get_user_enrolments($student->id);
        $this->assertCount(1, $userenrolments);

        $ue = reset($userenrolments);
        $actions = $plugin->get_user_enrolment_actions($manager, $ue);
        // Flatfile enrolment has 2 enrol actions for active users -- edit and unenrol.
        $this->assertCount(2, $actions);
    }
}
