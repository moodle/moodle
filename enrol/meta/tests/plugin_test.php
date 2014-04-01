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
 * Meta enrolment sync functional test.
 *
 * @package    enrol_meta
 * @category   phpunit
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

class enrol_meta_plugin_testcase extends advanced_testcase {

    protected function enable_plugin() {
        $enabled = enrol_get_plugins(true);
        $enabled['meta'] = true;
        $enabled = array_keys($enabled);
        set_config('enrol_plugins_enabled', implode(',', $enabled));
    }

    protected function disable_plugin() {
        $enabled = enrol_get_plugins(true);
        unset($enabled['meta']);
        $enabled = array_keys($enabled);
        set_config('enrol_plugins_enabled', implode(',', $enabled));
    }

    protected function is_meta_enrolled($user, $enrol, $role = null) {
        global $DB;

        if (!$DB->record_exists('user_enrolments', array('enrolid'=>$enrol->id, 'userid'=>$user->id))) {
            return false;
        }

        if ($role === null) {
            return true;
        }

        return $this->has_role($user, $enrol, $role);
    }

    protected function has_role($user, $enrol, $role) {
        global $DB;

        $context = context_course::instance($enrol->courseid);

        if ($role === false) {
            if ($DB->record_exists('role_assignments', array('contextid'=>$context->id, 'userid'=>$user->id, 'component'=>'enrol_meta', 'itemid'=>$enrol->id))) {
                return false;
            }
        } else if (!$DB->record_exists('role_assignments', array('contextid'=>$context->id, 'userid'=>$user->id, 'roleid'=>$role->id, 'component'=>'enrol_meta', 'itemid'=>$enrol->id))) {
            return false;
        }

        return true;
    }

    public function test_sync() {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $metalplugin = enrol_get_plugin('meta');
        $manplugin = enrol_get_plugin('manual');

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();
        $course4 = $this->getDataGenerator()->create_course();
        $manual1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $manual2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $manual3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $manual4 = $DB->get_record('enrol', array('courseid'=>$course4->id, 'enrol'=>'manual'), '*', MUST_EXIST);

        $student = $DB->get_record('role', array('shortname'=>'student'));
        $teacher = $DB->get_record('role', array('shortname'=>'teacher'));
        $manager = $DB->get_record('role', array('shortname'=>'manager'));

        $this->disable_plugin();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $student->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, $student->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id, 0);
        $this->getDataGenerator()->enrol_user($user4->id, $course1->id, $teacher->id);
        $this->getDataGenerator()->enrol_user($user5->id, $course1->id, $manager->id);

        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $student->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, $teacher->id);

        $this->assertEquals(7, $DB->count_records('user_enrolments'));
        $this->assertEquals(6, $DB->count_records('role_assignments'));

        set_config('syncall', 0, 'enrol_meta');
        set_config('nosyncroleids', $manager->id, 'enrol_meta');

        require_once($CFG->dirroot.'/enrol/meta/locallib.php');

        enrol_meta_sync(null, false);
        $this->assertEquals(7, $DB->count_records('user_enrolments'));
        $this->assertEquals(6, $DB->count_records('role_assignments'));

        $this->enable_plugin();
        enrol_meta_sync(null, false);
        $this->assertEquals(7, $DB->count_records('user_enrolments'));
        $this->assertEquals(6, $DB->count_records('role_assignments'));

        $e1 = $metalplugin->add_instance($course3, array('customint1'=>$course1->id));
        $e2 = $metalplugin->add_instance($course3, array('customint1'=>$course2->id));
        $e3 = $metalplugin->add_instance($course4, array('customint1'=>$course2->id));
        $enrol1 = $DB->get_record('enrol', array('id'=>$e1));
        $enrol2 = $DB->get_record('enrol', array('id'=>$e2));
        $enrol3 = $DB->get_record('enrol', array('id'=>$e3));

        enrol_meta_sync($course4->id, false);
        $this->assertEquals(9, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol3, $student));
        $this->assertTrue($this->is_meta_enrolled($user2, $enrol3, $teacher));

        enrol_meta_sync(null, false);
        $this->assertEquals(14, $DB->count_records('user_enrolments'));
        $this->assertEquals(13, $DB->count_records('role_assignments'));

        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));
        $this->assertTrue($this->is_meta_enrolled($user2, $enrol1, $student));
        $this->assertFalse($this->is_meta_enrolled($user3, $enrol1));
        $this->assertTrue($this->is_meta_enrolled($user4, $enrol1, $teacher));
        $this->assertFalse($this->is_meta_enrolled($user5, $enrol1));

        $this->assertTrue($this->is_meta_enrolled($user1, $enrol2, $student));
        $this->assertTrue($this->is_meta_enrolled($user2, $enrol2, $teacher));

        $this->assertTrue($this->is_meta_enrolled($user1, $enrol3, $student));
        $this->assertTrue($this->is_meta_enrolled($user2, $enrol3, $teacher));

        set_config('syncall', 1, 'enrol_meta');
        enrol_meta_sync(null, false);
        $this->assertEquals(16, $DB->count_records('user_enrolments'));
        $this->assertEquals(13, $DB->count_records('role_assignments'));

        $this->assertTrue($this->is_meta_enrolled($user3, $enrol1, false));
        $this->assertTrue($this->is_meta_enrolled($user5, $enrol1, false));

        $this->assertEquals(16, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->disable_plugin();
        $manplugin->unenrol_user($manual1, $user1->id);
        $manplugin->unenrol_user($manual2, $user1->id);

        $this->assertEquals(14, $DB->count_records('user_enrolments'));
        $this->assertEquals(11, $DB->count_records('role_assignments'));
        $this->assertEquals(14, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));

        $this->enable_plugin();

        set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPEND, 'enrol_meta');
        enrol_meta_sync($course4->id, false);
        $this->assertEquals(14, $DB->count_records('user_enrolments'));
        $this->assertEquals(11, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol3, $student));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$enrol3->id, 'status'=>ENROL_USER_SUSPENDED, 'userid'=>$user1->id)));

        enrol_meta_sync(null, false);
        $this->assertEquals(14, $DB->count_records('user_enrolments'));
        $this->assertEquals(11, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$enrol1->id, 'status'=>ENROL_USER_SUSPENDED, 'userid'=>$user1->id)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol2, $student));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$enrol2->id, 'status'=>ENROL_USER_SUSPENDED, 'userid'=>$user1->id)));

        set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPENDNOROLES, 'enrol_meta');
        enrol_meta_sync($course4->id, false);
        $this->assertEquals(14, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol3, false));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$enrol3->id, 'status'=>ENROL_USER_SUSPENDED, 'userid'=>$user1->id)));

        enrol_meta_sync(null, false);
        $this->assertEquals(14, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, false));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$enrol1->id, 'status'=>ENROL_USER_SUSPENDED, 'userid'=>$user1->id)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol2, false));
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$enrol2->id, 'status'=>ENROL_USER_SUSPENDED, 'userid'=>$user1->id)));

        set_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL, 'enrol_meta');
        enrol_meta_sync($course4->id, false);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertFalse($this->is_meta_enrolled($user1, $enrol3));

        enrol_meta_sync(null, false);
        $this->assertEquals(11, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertFalse($this->is_meta_enrolled($user1, $enrol1));
        $this->assertFalse($this->is_meta_enrolled($user1, $enrol2));


        // Now try sync triggered by events.

        set_config('syncall', 1, 'enrol_meta');

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $student->id);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));
        enrol_meta_sync(null, false);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));

        $manplugin->unenrol_user($manual1, $user1->id);
        $this->assertEquals(11, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertFalse($this->is_meta_enrolled($user1, $enrol1));
        enrol_meta_sync(null, false);
        $this->assertEquals(11, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertFalse($this->is_meta_enrolled($user1, $enrol1));

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 0);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, false));
        enrol_meta_sync(null, false);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, false));

        $manplugin->unenrol_user($manual1, $user1->id);
        $this->assertEquals(11, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertFalse($this->is_meta_enrolled($user1, $enrol1));
        enrol_meta_sync(null, false);
        $this->assertEquals(11, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertFalse($this->is_meta_enrolled($user1, $enrol1));

        set_config('syncall', 0, 'enrol_meta');
        enrol_meta_sync(null, false);
        $this->assertEquals(9, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(9, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertFalse($this->is_meta_enrolled($user1, $enrol1));

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 0);
        $this->assertEquals(10, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(10, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertFalse($this->is_meta_enrolled($user1, $enrol1, $student));
        enrol_meta_sync(null, false);
        $this->assertEquals(10, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(10, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertFalse($this->is_meta_enrolled($user1, $enrol1, $student));

        role_assign($teacher->id, $user1->id, context_course::instance($course1->id)->id);
        $this->assertEquals(11, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $teacher));
        enrol_meta_sync(null, false);
        $this->assertEquals(11, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $teacher));

        role_unassign($teacher->id, $user1->id, context_course::instance($course1->id)->id);
        $this->assertEquals(10, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(10, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertFalse($this->is_meta_enrolled($user1, $enrol1, $student));
        enrol_meta_sync(null, false);
        $this->assertEquals(10, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(10, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertFalse($this->is_meta_enrolled($user1, $enrol1, $student));

        $manplugin->unenrol_user($manual1, $user1->id);
        $this->assertEquals(9, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(9, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertFalse($this->is_meta_enrolled($user1, $enrol1));

        set_config('syncall', 1, 'enrol_meta');
        set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPEND, 'enrol_meta');
        enrol_meta_sync(null, false);
        $this->assertEquals(11, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $student->id);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));
        enrol_meta_sync(null, false);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));

        $manplugin->update_user_enrol($manual1, $user1->id, ENROL_USER_SUSPENDED);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));
        enrol_meta_sync(null, false);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));

        $manplugin->unenrol_user($manual1, $user1->id);
        $this->assertEquals(12, $DB->count_records('user_enrolments'));
        $this->assertEquals(9, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));
        enrol_meta_sync(null, false);
        $this->assertEquals(12, $DB->count_records('user_enrolments'));
        $this->assertEquals(9, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $student->id);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));
        enrol_meta_sync(null, false);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));

        set_config('syncall', 1, 'enrol_meta');
        set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPENDNOROLES, 'enrol_meta');
        enrol_meta_sync(null, false);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $student->id);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));
        enrol_meta_sync(null, false);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));

        $manplugin->unenrol_user($manual1, $user1->id);
        $this->assertEquals(12, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, false));
        enrol_meta_sync(null, false);
        $this->assertEquals(12, $DB->count_records('user_enrolments'));
        $this->assertEquals(8, $DB->count_records('role_assignments'));
        $this->assertEquals(11, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, false));

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $student->id);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));
        enrol_meta_sync(null, false);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        $this->assertTrue($this->is_meta_enrolled($user1, $enrol1, $student));


        set_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL, 'enrol_meta');
        enrol_meta_sync(null, false);
        $this->assertEquals(13, $DB->count_records('user_enrolments'));
        $this->assertEquals(10, $DB->count_records('role_assignments'));
        $this->assertEquals(13, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));

        delete_course($course1, false);
        $this->assertEquals(3, $DB->count_records('user_enrolments'));
        $this->assertEquals(3, $DB->count_records('role_assignments'));
        $this->assertEquals(3, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        enrol_meta_sync(null, false);
        $this->assertEquals(3, $DB->count_records('user_enrolments'));
        $this->assertEquals(3, $DB->count_records('role_assignments'));
        $this->assertEquals(3, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));

        delete_course($course2, false);
        $this->assertEquals(0, $DB->count_records('user_enrolments'));
        $this->assertEquals(0, $DB->count_records('role_assignments'));
        $this->assertEquals(0, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));
        enrol_meta_sync(null, false);
        $this->assertEquals(0, $DB->count_records('user_enrolments'));
        $this->assertEquals(0, $DB->count_records('role_assignments'));
        $this->assertEquals(0, $DB->count_records('user_enrolments', array('status'=>ENROL_USER_ACTIVE)));

        delete_course($course3, false);
        delete_course($course4, false);

    }

    /**
     * Test user_enrolment_created event.
     */
    public function test_user_enrolment_created_observer() {
        global $DB;

        $this->resetAfterTest();

        $metaplugin = enrol_get_plugin('meta');
        $user1 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $student = $DB->get_record('role', array('shortname' => 'student'));

        $e1 = $metaplugin->add_instance($course2, array('customint1' => $course1->id));
        $enrol1 = $DB->get_record('enrol', array('id' => $e1));

        // Enrol user and capture event.
        $sink = $this->redirectEvents();

        $metaplugin->enrol_user($enrol1, $user1->id, $student->id);
        $events = $sink->get_events();
        $sink->close();
        $event = array_shift($events);

        // Test Event.
        $dbuserenrolled = $DB->get_record('user_enrolments', array('userid' => $user1->id));
        $this->assertInstanceOf('\core\event\user_enrolment_created', $event);
        $this->assertEquals($dbuserenrolled->id, $event->objectid);
        $this->assertEquals('user_enrolled', $event->get_legacy_eventname());
        $expectedlegacyeventdata = $dbuserenrolled;
        $expectedlegacyeventdata->enrol = 'meta';
        $expectedlegacyeventdata->courseid = $course2->id;
        $this->assertEventLegacyData($expectedlegacyeventdata, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test user_enrolment_deleted observer.
     */
    public function test_user_enrolment_deleted_observer() {
        global $DB;

        $this->resetAfterTest(true);

        $metalplugin = enrol_get_plugin('meta');
        $user1 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $student = $DB->get_record('role', array('shortname'=>'student'));

        $e1 = $metalplugin->add_instance($course2, array('customint1' => $course1->id));
        $enrol1 = $DB->get_record('enrol', array('id' => $e1));

        // Enrol user.
        $metalplugin->enrol_user($enrol1, $user1->id, $student->id);
        $this->assertEquals(1, $DB->count_records('user_enrolments'));

        // Unenrol user and capture event.
        $sink = $this->redirectEvents();
        $metalplugin->unenrol_user($enrol1, $user1->id);
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        $this->assertEquals(0, $DB->count_records('user_enrolments'));
        $this->assertInstanceOf('\core\event\user_enrolment_deleted', $event);
        $this->assertEquals('user_unenrolled', $event->get_legacy_eventname());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test user_enrolment_updated event.
     */
    public function test_user_enrolment_updated_observer() {
        global $DB;

        $this->resetAfterTest(true);

        $metalplugin = enrol_get_plugin('meta');
        $user1 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $student = $DB->get_record('role', array('shortname'=>'student'));

        $e1 = $metalplugin->add_instance($course2, array('customint1' => $course1->id));
        $enrol1 = $DB->get_record('enrol', array('id' => $e1));

        // Enrol user.
        $metalplugin->enrol_user($enrol1, $user1->id, $student->id);
        $this->assertEquals(1, $DB->count_records('user_enrolments'));

        // Updated enrolment for user and capture event.
        $sink = $this->redirectEvents();
        $metalplugin->update_user_enrol($enrol1, $user1->id, ENROL_USER_SUSPENDED, null, time());
        $events = $sink->get_events();
        $sink->close();
        $event = array_shift($events);

        // Test Event.
        $dbuserenrolled = $DB->get_record('user_enrolments', array('userid' => $user1->id));
        $this->assertInstanceOf('\core\event\user_enrolment_updated', $event);
        $this->assertEquals($dbuserenrolled->id, $event->objectid);
        $this->assertEquals('user_enrol_modified', $event->get_legacy_eventname());
        $expectedlegacyeventdata = $dbuserenrolled;
        $expectedlegacyeventdata->enrol = 'meta';
        $expectedlegacyeventdata->courseid = $course2->id;
        $url = new \moodle_url('/enrol/editenrolment.php', array('ue' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventLegacyData($expectedlegacyeventdata, $event);
        $this->assertEventContextNotUsed($event);
    }
}
