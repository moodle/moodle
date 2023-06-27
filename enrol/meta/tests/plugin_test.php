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

        // Disable the plugin to prevent add_instance from calling enrol_meta_sync.
        $this->disable_plugin();
        $e1 = $metalplugin->add_instance($course3, array('customint1'=>$course1->id));
        $e2 = $metalplugin->add_instance($course3, array('customint1'=>$course2->id));
        $e3 = $metalplugin->add_instance($course4, array('customint1'=>$course2->id));
        $enrol1 = $DB->get_record('enrol', array('id'=>$e1));
        $enrol2 = $DB->get_record('enrol', array('id'=>$e2));
        $enrol3 = $DB->get_record('enrol', array('id'=>$e3));
        $this->enable_plugin();

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

    public function test_add_to_group() {
        global $CFG, $DB;

        require_once($CFG->dirroot.'/group/lib.php');

        $this->resetAfterTest(true);

        $metalplugin = enrol_get_plugin('meta');

        $user1 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();
        $manualenrol1 = $DB->get_record('enrol', array('courseid' => $course1->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        $manualenrol2 = $DB->get_record('enrol', array('courseid' => $course2->id, 'enrol' => 'manual'), '*', MUST_EXIST);

        $student = $DB->get_record('role', array('shortname' => 'student'));
        $teacher = $DB->get_record('role', array('shortname' => 'teacher'));

        $id = groups_create_group((object)array('name' => 'Group 1 in course 3', 'courseid' => $course3->id));
        $group31 = $DB->get_record('groups', array('id' => $id), '*', MUST_EXIST);
        $id = groups_create_group((object)array('name' => 'Group 2 in course 4', 'courseid' => $course3->id));
        $group32 = $DB->get_record('groups', array('id' => $id), '*', MUST_EXIST);

        $this->enable_plugin();

        $e1 = $metalplugin->add_instance($course3, array('customint1' => $course1->id, 'customint2' => $group31->id));
        $e2 = $metalplugin->add_instance($course3, array('customint1' => $course2->id, 'customint2' => $group32->id));

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $student->id);
        $this->getDataGenerator()->enrol_user($user4->id, $course1->id, $teacher->id);

        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $student->id);

        // Now make sure users are in the correct groups.
        $this->assertTrue(groups_is_member($group31->id, $user1->id));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group31->id, 'userid' => $user1->id,
            'component' => 'enrol_meta', 'itemid' => $e1)));
        $this->assertTrue(groups_is_member($group32->id, $user1->id));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group32->id, 'userid' => $user1->id,
            'component' => 'enrol_meta', 'itemid' => $e2)));

        $this->assertTrue(groups_is_member($group31->id, $user4->id));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group31->id, 'userid' => $user4->id,
            'component' => 'enrol_meta', 'itemid' => $e1)));

        // Make sure everything is the same after sync.
        enrol_meta_sync(null, false);
        $this->assertTrue(groups_is_member($group31->id, $user1->id));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group31->id, 'userid' => $user1->id,
            'component' => 'enrol_meta', 'itemid' => $e1)));
        $this->assertTrue(groups_is_member($group32->id, $user1->id));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group32->id, 'userid' => $user1->id,
            'component' => 'enrol_meta', 'itemid' => $e2)));

        $this->assertTrue(groups_is_member($group31->id, $user4->id));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group31->id, 'userid' => $user4->id,
            'component' => 'enrol_meta', 'itemid' => $e1)));

        set_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL, 'enrol_meta');

        // When user 1 is unenrolled from course1, he is removed from group31 but still present in group32.
        enrol_get_plugin('manual')->unenrol_user($manualenrol1, $user1->id);
        $this->assertFalse(groups_is_member($group31->id, $user1->id));
        $this->assertTrue(groups_is_member($group32->id, $user1->id));
        $this->assertTrue(is_enrolled(context_course::instance($course3->id), $user1, '', true)); // He still has active enrolment.
        // And the same after sync.
        enrol_meta_sync(null, false);
        $this->assertFalse(groups_is_member($group31->id, $user1->id));
        $this->assertTrue(groups_is_member($group32->id, $user1->id));
        $this->assertTrue(is_enrolled(context_course::instance($course3->id), $user1, '', true));

        // Unenroll user1 from course2 and make sure he is completely unenrolled from course3.
        enrol_get_plugin('manual')->unenrol_user($manualenrol2, $user1->id);
        $this->assertFalse(groups_is_member($group32->id, $user1->id));
        $this->assertFalse(is_enrolled(context_course::instance($course3->id), $user1));

        set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPENDNOROLES, 'enrol_meta');

        // When user is unenrolled in this case, he is still a member of a group (but enrolment is suspended).
        enrol_get_plugin('manual')->unenrol_user($manualenrol1, $user4->id);
        $this->assertTrue(groups_is_member($group31->id, $user4->id));
        $this->assertTrue(is_enrolled(context_course::instance($course3->id), $user4));
        $this->assertFalse(is_enrolled(context_course::instance($course3->id), $user4, '', true));
        enrol_meta_sync(null, false);
        $this->assertTrue(groups_is_member($group31->id, $user4->id));
        $this->assertTrue(is_enrolled(context_course::instance($course3->id), $user4));
        $this->assertFalse(is_enrolled(context_course::instance($course3->id), $user4, '', true));
    }

    /**
     * Enrol users from another course into a course where one of the members is already enrolled
     * and is a member of the same group.
     */
    public function test_add_to_group_with_member() {
        global $CFG, $DB;

        require_once($CFG->dirroot.'/group/lib.php');

        $this->resetAfterTest(true);

        $metalplugin = enrol_get_plugin('meta');

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $manualenrol1 = $DB->get_record('enrol', array('courseid' => $course1->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        $manualenrol2 = $DB->get_record('enrol', array('courseid' => $course2->id, 'enrol' => 'manual'), '*', MUST_EXIST);

        $student = $DB->get_record('role', array('shortname' => 'student'));

        $groupid = groups_create_group((object)array('name' => 'Grp', 'courseid' => $course2->id));

        $this->enable_plugin();
        set_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL, 'enrol_meta');

        // Manually enrol user1 to course2 and add him to group.
        // Manually enrol user2 to course2 but do not add him to the group.
        enrol_get_plugin('manual')->enrol_user($manualenrol2, $user1->id, $student->id);
        groups_add_member($groupid, $user1->id);
        enrol_get_plugin('manual')->enrol_user($manualenrol2, $user2->id, $student->id);
        $this->assertTrue(groups_is_member($groupid, $user1->id));
        $this->assertFalse(groups_is_member($groupid, $user2->id));

        // Add instance of meta enrolment in course2 linking to course1 and enrol both users in course1.
        $metalplugin->add_instance($course2, array('customint1' => $course1->id, 'customint2' => $groupid));

        enrol_get_plugin('manual')->enrol_user($manualenrol1, $user1->id, $student->id);
        enrol_get_plugin('manual')->enrol_user($manualenrol1, $user2->id, $student->id);

        // Both users now should be members of the group.
        $this->assertTrue(groups_is_member($groupid, $user1->id));
        $this->assertTrue(groups_is_member($groupid, $user2->id));

        // Ununerol both users from course1.
        enrol_get_plugin('manual')->unenrol_user($manualenrol1, $user1->id);
        enrol_get_plugin('manual')->unenrol_user($manualenrol1, $user2->id);

        // User1 should still be member of the group because he was added there manually. User2 should no longer be there.
        $this->assertTrue(groups_is_member($groupid, $user1->id));
        $this->assertFalse(groups_is_member($groupid, $user2->id));

        // Assert that everything is the same after sync.
        enrol_meta_sync();

        $this->assertTrue(groups_is_member($groupid, $user1->id));
        $this->assertFalse(groups_is_member($groupid, $user2->id));

    }

    /**
     * Test user_enrolment_created event.
     */
    public function test_user_enrolment_created_event() {
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
     * Test user_enrolment_deleted event.
     */
    public function test_user_enrolment_deleted_event() {
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
    public function test_user_enrolment_updated_event() {
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

    /**
     * Test that a new group with the name of the course is created.
     */
    public function test_enrol_meta_create_new_group() {
        global $DB;
        $this->resetAfterTest();
        // Create two courses.
        $course = $this->getDataGenerator()->create_course(array('fullname' => 'Mathematics'));
        $course2 = $this->getDataGenerator()->create_course(array('fullname' => 'Physics'));
        $metacourse = $this->getDataGenerator()->create_course(array('fullname' => 'All sciences'));
        // Run the function.
        $groupid = enrol_meta_create_new_group($metacourse->id, $course->id);
        // Check the results.
        $group = $DB->get_record('groups', array('id' => $groupid));
        // The group name should match the course name.
        $this->assertEquals('Mathematics course', $group->name);
        // Group course id should match the course id.
        $this->assertEquals($metacourse->id, $group->courseid);

        // Create a group that will have the same name as the course.
        $groupdata = new stdClass();
        $groupdata->courseid = $metacourse->id;
        $groupdata->name = 'Physics course';
        groups_create_group($groupdata);
        // Create a group for the course 2 in metacourse.
        $groupid = enrol_meta_create_new_group($metacourse->id, $course2->id);
        $groupinfo = $DB->get_record('groups', array('id' => $groupid));
        // Check that the group name has been changed.
        $this->assertEquals('Physics course (2)', $groupinfo->name);

        // Create a group for the course 2 in metacourse.
        $groupid = enrol_meta_create_new_group($metacourse->id, $course2->id);
        $groupinfo = $DB->get_record('groups', array('id' => $groupid));
        // Check that the group name has been changed.
        $this->assertEquals('Physics course (3)', $groupinfo->name);
    }

    /**
     * Test that enrolment timestart-timeend is respected in meta course.
     */
    public function test_timeend() {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $timeinfuture = time() + DAYSECS;
        $timeinpast = time() - DAYSECS;

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
        $manual1 = $DB->get_record('enrol', array('courseid' => $course1->id, 'enrol' => 'manual'), '*', MUST_EXIST);

        $student = $DB->get_record('role', array('shortname' => 'student'));

        $this->enable_plugin();

        // Create instance of enrol_meta in course2 when there are no enrolments present.
        $meta2id = $metalplugin->add_instance($course2, array('customint1' => $course1->id));

        $expectedenrolments = array(
            $user1->id => array(0, 0, ENROL_USER_ACTIVE),
            $user2->id => array($timeinpast, 0, ENROL_USER_ACTIVE),
            $user3->id => array(0, $timeinfuture, ENROL_USER_ACTIVE),
            $user4->id => array($timeinpast, $timeinfuture, ENROL_USER_ACTIVE),
            $user5->id => array(0, 0, ENROL_USER_SUSPENDED),
        );
        foreach ($expectedenrolments as $userid => $data) {
            $expectedenrolments[$userid] = (object)(array('userid' => $userid) +
                    array_combine(array('timestart', 'timeend', 'status'), $data));
        }

        // Enrol users manually in course 1.
        foreach ($expectedenrolments as $e) {
            $manplugin->enrol_user($manual1, $e->userid, $student->id, $e->timestart, $e->timeend, $e->status);
        }

        $enrolments = $DB->get_records('user_enrolments', array('enrolid' => $manual1->id), 'userid', 'userid, timestart, timeend, status');
        $this->assertEquals($expectedenrolments, $enrolments);

        // Make sure that the same enrolments are now present in course2 under meta enrolment.
        $enrolments = $DB->get_records('user_enrolments', array('enrolid' => $meta2id), '', 'userid, timestart, timeend, status');
        $this->assertEquals($expectedenrolments, $enrolments);

        // Create instance of enrol_meta in course3 and run sync.
        $meta3id = $metalplugin->add_instance($course3, array('customint1' => $course1->id));
        enrol_meta_sync($course3->id);

        // Make sure that the same enrolments are now present in course3 under meta enrolment.
        $enrolments = $DB->get_records('user_enrolments', array('enrolid' => $meta3id), '', 'userid, timestart, timeend, status');
        $this->assertEquals($expectedenrolments, $enrolments);

        // Update some of the manual enrolments.
        $expectedenrolments[$user2->id]->timestart = $timeinpast - 60;
        $expectedenrolments[$user3->id]->timeend = $timeinfuture + 60;
        $expectedenrolments[$user4->id]->status = ENROL_USER_SUSPENDED;
        $expectedenrolments[$user5->id]->status = ENROL_USER_ACTIVE;
        foreach ($expectedenrolments as $e) {
            $manplugin->update_user_enrol($manual1, $e->userid, $e->status, $e->timestart, $e->timeend);
        }

        // Make sure meta courses are also updated.
        $enrolments = $DB->get_records('user_enrolments', array('enrolid' => $meta2id), '', 'userid, timestart, timeend, status');
        $this->assertEquals($expectedenrolments, $enrolments);
        $enrolments = $DB->get_records('user_enrolments', array('enrolid' => $meta3id), '', 'userid, timestart, timeend, status');
        $this->assertEquals($expectedenrolments, $enrolments);

        // Test meta sync. Imagine events are not working.
        $sink = $this->redirectEvents();
        $expectedenrolments[$user2->id]->timestart = $timeinpast;
        $expectedenrolments[$user3->id]->timeend = $timeinfuture;
        $expectedenrolments[$user4->id]->status = ENROL_USER_ACTIVE;
        $expectedenrolments[$user5->id]->status = ENROL_USER_SUSPENDED;
        foreach ($expectedenrolments as $e) {
            $manplugin->update_user_enrol($manual1, $e->userid, $e->status, $e->timestart, $e->timeend);
        }

        // Make sure meta courses are updated only for the course that was synced.
        enrol_meta_sync($course3->id);

        $enrolments = $DB->get_records('user_enrolments', array('enrolid' => $meta2id), '', 'userid, timestart, timeend, status');
        $this->assertNotEquals($expectedenrolments, $enrolments);

        $enrolments = $DB->get_records('user_enrolments', array('enrolid' => $meta3id), '', 'userid, timestart, timeend, status');
        $this->assertEquals($expectedenrolments, $enrolments);

        $sink->close();

        // Disable manual enrolment in course1 and make sure all user enrolments in course2 are suspended.
        $manplugin->update_status($manual1, ENROL_INSTANCE_DISABLED);
        $allsuspendedenrolemnts = array_combine(array_keys($expectedenrolments), array_fill(0, 5, ENROL_USER_SUSPENDED));
        $enrolmentstatuses = $DB->get_records_menu('user_enrolments', array('enrolid' => $meta2id), '', 'userid, status');
        $this->assertEquals($allsuspendedenrolemnts, $enrolmentstatuses);

        $manplugin->update_status($manual1, ENROL_INSTANCE_ENABLED);
        $enrolments = $DB->get_records('user_enrolments', array('enrolid' => $meta2id), '', 'userid, timestart, timeend, status');
        $this->assertEquals($expectedenrolments, $enrolments);

        // Disable events and repeat the same for course3 (testing sync):
        $sink = $this->redirectEvents();
        $manplugin->update_status($manual1, ENROL_INSTANCE_DISABLED);
        enrol_meta_sync($course3->id);
        $enrolmentstatuses = $DB->get_records_menu('user_enrolments', array('enrolid' => $meta3id), '', 'userid, status');
        $this->assertEquals($allsuspendedenrolemnts, $enrolmentstatuses);

        $manplugin->update_status($manual1, ENROL_INSTANCE_ENABLED);
        enrol_meta_sync($course3->id);
        $enrolments = $DB->get_records('user_enrolments', array('enrolid' => $meta3id), '', 'userid, timestart, timeend, status');
        $this->assertEquals($expectedenrolments, $enrolments);
        $sink->close();
    }

    /**
     * Test for getting user enrolment actions.
     */
    public function test_get_user_enrolment_actions() {
        global $CFG, $PAGE;
        $this->resetAfterTest();

        // Set page URL to prevent debugging messages.
        $PAGE->set_url('/enrol/editinstance.php');

        $pluginname = 'meta';

        // Only enable the meta enrol plugin.
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

        // Teachers don't have enrol/meta:unenrol capability by default. Login as admin for simplicity.
        $this->setAdminUser();
        require_once($CFG->dirroot . '/enrol/locallib.php');
        $manager = new course_enrolment_manager($PAGE, $course);

        $userenrolments = $manager->get_user_enrolments($student->id);
        $this->assertCount(1, $userenrolments);

        $ue = reset($userenrolments);
        $actions = $plugin->get_user_enrolment_actions($manager, $ue);
        // Meta-link enrolment has no enrol actions for active students.
        $this->assertCount(0, $actions);

        // Enrol actions for a suspended student.
        // Suspend the student.
        $ue->status = ENROL_USER_SUSPENDED;

        $actions = $plugin->get_user_enrolment_actions($manager, $ue);
        // Meta-link enrolment has enrol actions for suspended students -- unenrol.
        $this->assertCount(1, $actions);
    }

    /**
     * Test how data for instance editing is validated.
     */
    public function test_edit_instance_validation() {
        global $DB;

        $this->resetAfterTest();

        $metaplugin = enrol_get_plugin('meta');

        // A course with meta enrolment.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        // Create a meta enrolment instance.
        $instance = (object)$metaplugin->get_instance_defaults();
        $instance->id       = null;
        $instance->courseid = $course->id;
        $instance->status   = ENROL_INSTANCE_ENABLED;
        // Emulate the form data.
        $data = [
            'customint1' => 0,
            'customint2' => 0
        ];
        // Test when no valid 'customint1' field (meta courses links) is provided.
        $errors = $metaplugin->edit_instance_validation($data, [], $instance, $coursecontext);
        // We're going to check the string contents of the errors returned as this is the only way
        // to differentiate the errors produced by the 'edit_instance_validation()' method somehow.
        // The method always returns what the edit instance form expects and this is an array of form fields
        // with the corresponding errors messages.
        $this->assertEquals('Required', $errors['customint1']);

        // Test when 'customint1' contains an unknown course.
        // Fetch the max course id from the courses table and increment it to get
        // the course id which surely doesn't exist.
        $maxid = $DB->get_field_sql('SELECT MAX(id) FROM {course}');
        // Use the same instance as before but set another data.
        $data = [
            'customint1' => [$maxid + 1],
            'customint2' => 0
        ];
        $errors = $metaplugin->edit_instance_validation($data, [], $instance, $coursecontext);
        $this->assertEquals('You are trying to use an invalid course ID', $errors['customint1']);

        // Test when 'customint1' field already contains courses meta linked with the current one.
        $metacourse1 = $this->getDataGenerator()->create_course();
        $metaplugin->add_instance($course, array('customint1' => $metacourse1->id));
        // Use the same instance as before but set another data.
        $data = [
            'customint1' => [$metacourse1->id],
            'customint2' => 0
        ];
        $errors = $metaplugin->edit_instance_validation($data, [], $instance, $coursecontext);
        $this->assertEquals('You are trying to use an invalid course ID', $errors['customint1']);

        // Test when a course is set as a not visible and a user doesn't have the capability to use it here.
        $metacourse2record = new stdClass();
        $metacourse2record->visible = 0;
        $metacourse2 = $this->getDataGenerator()->create_course($metacourse2record);
        $metacourse2context = context_course::instance($metacourse2->id);

        $user = $this->getDataGenerator()->create_user();
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        role_assign($teacherrole->id, $user->id, $metacourse2context->id);
        unassign_capability('moodle/course:viewhiddencourses', $teacherrole->id);
        $this->setUser($user);

        // Use the same instance as before but set another data.
        $data = [
            'customint1' => [$metacourse2->id],
            'customint2' => 0
        ];
        $errors = $metaplugin->edit_instance_validation($data, [], $instance, $coursecontext);
        $this->assertEquals('Sorry, but you do not currently have permissions to do that (moodle/course:viewhiddencourses).',
            $errors['customint1']);

        // Revert some changes from the last assertion to reuse the course.
        $metacourse2->visible = 1;
        $DB->update_record('course', $metacourse2);
        assign_capability('moodle/course:viewhiddencourses', CAP_ALLOW,
            $teacherrole->id, context_course::instance($metacourse2->id));

        // Test with no 'enrol/meta:selectaslinked' capability.
        unassign_capability('enrol/meta:selectaslinked', $teacherrole->id);
        $errors = $metaplugin->edit_instance_validation($data, [], $instance, $coursecontext);
        $this->assertEquals('Sorry, but you do not currently have permissions to do that (enrol/meta:selectaslinked).',
            $errors['customint1']);

        // Back to admin user to regain the capabilities quickly.
        $this->setAdminUser();

        // Test when meta course id is the site id.
        $site = $DB->get_record('course', ['id' => SITEID]);
        // Use the same instance as before but set another data.
        $data = [
            'customint1' => [$site->id],
            'customint2' => 0
        ];
        $errors = $metaplugin->edit_instance_validation($data, [], $instance, $coursecontext);
        $this->assertEquals('You are trying to use an invalid course ID', $errors['customint1']);

        // Test when meta course id is id of the current course.
        // Use the same instance as before but set another data.
        $data = [
            'customint1' => [$course->id],
            'customint2' => 0
        ];
        $errors = $metaplugin->edit_instance_validation($data, [], $instance, $coursecontext);
        $this->assertEquals('You are trying to use an invalid course ID', $errors['customint1']);

        // Test with the 'customint2' field set (which is groups).
        // Prepare some groups data.
        $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $groups = [];
        foreach (groups_get_all_groups($course->id) as $group) {
            $groups[$group->id] = format_string($group->name, true, array('context' => $coursecontext));
        }

        // Use the same instance as before but set another data.
        // Use a non-existing group id.
        if (!$maxid = $DB->get_field_sql('SELECT MAX(id) FROM {groups}')) {
            $maxid = 0;
        }
        $data = [
            'customint1' => [$metacourse2->id],
            'customint2' => [$maxid + 1]
        ];
        $errors = $metaplugin->edit_instance_validation($data, [], $instance, $coursecontext);
        $this->assertArrayHasKey('customint2', $errors);

        // Test with valid data.
        reset($groups);
        $validgroup = key($groups);
        $data = [
            'customint1' => [$metacourse2->id],
            'customint2' => $validgroup
        ];
        $errors = $metaplugin->edit_instance_validation($data, [], $instance, $coursecontext);
        $this->assertArrayNotHasKey('customint1', $errors);
        $this->assertArrayNotHasKey('customint2', $errors);
    }
}
