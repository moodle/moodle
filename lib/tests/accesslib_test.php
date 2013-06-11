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
 * Full functional accesslib test
 *
 * @package    core
 * @category   phpunit
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Context caching fixture
 */
class context_inspection extends context_helper {
    public static function test_context_cache_size() {
        return self::$cache_count;
    }
}


/**
 * Functional test for accesslib.php
 *
 * Note: execution may take many minutes especially on slower servers.
 */
class accesslib_testcase extends advanced_testcase {

    //TODO: add more tests for the remaining accesslib parts such as enrol related api

    /**
     * Verify comparison of context instances in phpunit asserts
     * @return void
     */
    public function test_context_comparisons() {
        $frontpagecontext1 = context_course::instance(SITEID);
        context_helper::reset_caches();
        $frontpagecontext2 = context_course::instance(SITEID);
        $this->assertEquals($frontpagecontext1, $frontpagecontext2);

        $user1 = context_user::instance(1);
        $user2 = context_user::instance(2);
        $this->assertNotEquals($user1, $user2);
    }

    /**
     * Test resetting works.
     */
    public function test_accesslib_clear_all_caches() {
        global $ACCESSLIB_PRIVATE;

        $this->resetAfterTest();

        $this->setAdminUser();
        load_all_capabilities();

        $this->assertNotEmpty($ACCESSLIB_PRIVATE->rolepermissions);
        $this->assertNotEmpty($ACCESSLIB_PRIVATE->rolepermissions);
        $this->assertNotEmpty($ACCESSLIB_PRIVATE->accessdatabyuser);
        accesslib_clear_all_caches(true);
        $this->assertEmpty($ACCESSLIB_PRIVATE->rolepermissions);
        $this->assertEmpty($ACCESSLIB_PRIVATE->rolepermissions);
        $this->assertEmpty($ACCESSLIB_PRIVATE->dirtycontexts);
        $this->assertEmpty($ACCESSLIB_PRIVATE->accessdatabyuser);
    }

    /**
     * Test getting of role access
     * @return void
     */
    public function test_get_role_access() {
        global $DB;

        $roles = $DB->get_records('role');
        foreach ($roles as $role) {
            $access = get_role_access($role->id);

            $this->assertTrue(is_array($access));
            $this->assertTrue(is_array($access['ra']));
            $this->assertTrue(is_array($access['rdef']));
            $this->assertTrue(isset($access['rdef_count']));
            $this->assertTrue(is_array($access['loaded']));
            $this->assertTrue(isset($access['time']));
            $this->assertTrue(is_array($access['rsw']));
        }

        // Note: the data is validated in the functional permission evaluation test at the end of this testcase.
    }

    /**
     * Test getting of guest role.
     * @return void
     */
    public function test_get_guest_role() {
        global $CFG;

        $guest = get_guest_role();
        $this->assertEquals('guest', $guest->archetype);
        $this->assertEquals('guest', $guest->shortname);

        $this->assertEquals($CFG->guestroleid, $guest->id);
    }

    /**
     * Test if user is admin.
     * @return void
     */
    public function test_is_siteadmin() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $users = $DB->get_records('user');

        foreach ($users as $user) {
            $this->setUser(0);
            if ($user->username === 'admin') {
                $this->assertTrue(is_siteadmin($user));
                $this->assertTrue(is_siteadmin($user->id));
                $this->setUser($user);
                $this->assertTrue(is_siteadmin());
                $this->assertTrue(is_siteadmin(null));
            } else {
                $this->assertFalse(is_siteadmin($user));
                $this->assertFalse(is_siteadmin($user->id));
                $this->setUser($user);
                $this->assertFalse(is_siteadmin());
                $this->assertFalse(is_siteadmin(null));
            }
        }

        // Change the site admin list and check that it still works with
        // multiple admins. We do this with userids only (not real user
        // accounts) because it makes the test simpler.
        $before = $CFG->siteadmins;
        set_config('siteadmins', '666,667,668');
        $this->assertTrue(is_siteadmin(666));
        $this->assertTrue(is_siteadmin(667));
        $this->assertTrue(is_siteadmin(668));
        $this->assertFalse(is_siteadmin(669));
        set_config('siteadmins', '13');
        $this->assertTrue(is_siteadmin(13));
        $this->assertFalse(is_siteadmin(666));
        set_config('siteadmins', $before);
    }

    /**
     * Test if user is enrolled in a course
     * @return void
     */
    public function test_is_enrolled() {
        global $DB;

        // Generate data
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $role = $DB->get_record('role', array('shortname'=>'student'));

        // There should be a manual enrolment as part of the default install
        $plugin = enrol_get_plugin('manual');
        $instance = $DB->get_record('enrol', array(
            'courseid' => $course->id,
            'enrol' => 'manual',
        ));
        $this->assertNotEquals($instance, false);

        // Enrol the user in the course
        $plugin->enrol_user($instance, $user->id, $role->id);

        // We'll test with the mod/assign:submit capability
        $capability= 'mod/assign:submit';
        $this->assertTrue($DB->record_exists('capabilities', array('name' => $capability)));

        // Switch to our user
        $this->setUser($user);

        // Ensure that the user has the capability first
        $this->assertTrue(has_capability($capability, $coursecontext, $user->id));

        // We first test whether the user is enrolled on the course as this
        // seeds the cache, then we test for the capability
        $this->assertTrue(is_enrolled($coursecontext, $user, '', true));
        $this->assertTrue(is_enrolled($coursecontext, $user, $capability));

        // Prevent the capability for this user role
        assign_capability($capability, CAP_PROHIBIT, $role->id, $coursecontext);
        $coursecontext->mark_dirty();
        $this->assertFalse(has_capability($capability, $coursecontext, $user->id));

        // Again, we seed the cache first by checking initial enrolment,
        // and then we test the actual capability
        $this->assertTrue(is_enrolled($coursecontext, $user, '', true));
        $this->assertFalse(is_enrolled($coursecontext, $user, $capability));

        // We need variable states to be reset for the next test
        $this->resetAfterTest(true);
    }

    /**
     * Test logged in test.
     * @return void
     */
    public function test_isloggedin() {
        global $USER;

        $this->resetAfterTest();

        $USER->id = 0;
        $this->assertFalse(isloggedin());
        $USER->id = 1;
        $this->assertTrue(isloggedin());
    }

    /**
     * Test guest user test.
     * @return void
     */
    public function test_isguestuser() {
        global $DB;

        $this->resetAfterTest();

        $guest = $DB->get_record('user', array('username'=>'guest'));
        $this->setUser(0);
        $this->assertFalse(isguestuser());
        $this->setAdminUser();
        $this->assertFalse(isguestuser());
        $this->assertTrue(isguestuser($guest));
        $this->assertTrue(isguestuser($guest->id));
        $this->setUser($guest);
        $this->assertTrue(isguestuser());

        $users = $DB->get_records('user');
        foreach ($users as $user) {
            if ($user->username === 'guest') {
                continue;
            }
            $this->assertFalse(isguestuser($user));
        }
    }

    /**
     * Test capability riskiness.
     * @return void
     */
    public function test_is_safe_capability() {
        global $DB;
        // Note: there is not much to test, just make sure no notices are throw for the most dangerous cap.
        $capability = $DB->get_record('capabilities', array('name'=>'moodle/site:config'), '*', MUST_EXIST);
        $this->assertFalse(is_safe_capability($capability));
    }

    /**
     * Test context fetching.
     * @return void
     */
    public function test_get_context_info_array() {
        $this->resetAfterTest();

        $syscontext = context_system::instance();
        $user = $this->getDataGenerator()->create_user();
        $usercontext = context_user::instance($user->id);
        $course = $this->getDataGenerator()->create_course();
        $catcontext = context_coursecat::instance($course->category);
        $coursecontext = context_course::instance($course->id);
        $page = $this->getDataGenerator()->create_module('page', array('course'=>$course->id));
        $modcontext = context_module::instance($page->cmid);
        $cm = get_coursemodule_from_instance('page', $page->id);
        $block1 = $this->getDataGenerator()->create_block('online_users', array('parentcontextid'=>$coursecontext->id));
        $block1context = context_block::instance($block1->id);
        $block2 = $this->getDataGenerator()->create_block('online_users', array('parentcontextid'=>$modcontext->id));
        $block2context = context_block::instance($block2->id);

        $result = get_context_info_array($syscontext->id);
        $this->assertCount(3, $result);
        $this->assertSame($syscontext, $result[0]);
        $this->assertSame(null, $result[1]);
        $this->assertSame(null, $result[2]);

        $result = get_context_info_array($usercontext->id);
        $this->assertCount(3, $result);
        $this->assertSame($usercontext, $result[0]);
        $this->assertSame(null, $result[1]);
        $this->assertSame(null, $result[2]);

        $result = get_context_info_array($catcontext->id);
        $this->assertCount(3, $result);
        $this->assertSame($catcontext, $result[0]);
        $this->assertSame(null, $result[1]);
        $this->assertSame(null, $result[2]);

        $result = get_context_info_array($coursecontext->id);
        $this->assertCount(3, $result);
        $this->assertSame($coursecontext, $result[0]);
        $this->assertEquals($course->id, $result[1]->id);
        $this->assertEquals($course->shortname, $result[1]->shortname);
        $this->assertSame(null, $result[2]);

        $result = get_context_info_array($block1context->id);
        $this->assertCount(3, $result);
        $this->assertSame($block1context, $result[0]);
        $this->assertEquals($course->id, $result[1]->id);
        $this->assertEquals($course->shortname, $result[1]->shortname);
        $this->assertSame(null, $result[2]);

        $result = get_context_info_array($modcontext->id);
        $this->assertCount(3, $result);
        $this->assertSame($modcontext, $result[0]);
        $this->assertEquals($course->id, $result[1]->id);
        $this->assertEquals($course->shortname, $result[1]->shortname);
        $this->assertEquals($cm->id, $result[2]->id);
        $this->assertEquals($cm->groupmembersonly, $result[2]->groupmembersonly);

        $result = get_context_info_array($block2context->id);
        $this->assertCount(3, $result);
        $this->assertSame($block2context, $result[0]);
        $this->assertEquals($course->id, $result[1]->id);
        $this->assertEquals($course->shortname, $result[1]->shortname);
        $this->assertEquals($cm->id, $result[2]->id);
        $this->assertEquals($cm->groupmembersonly, $result[2]->groupmembersonly);
    }

    /**
     * Test looking for course contacts.
     * @return void
     */
    public function test_has_coursecontact_role() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $users = $DB->get_records('user');

        // Nobody is expected to have any course level roles.
        $this->assertNotEmpty($CFG->coursecontact);
        foreach($users as $user) {
            $this->assertFalse(has_coursecontact_role($user->id));
        }

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        role_assign($CFG->coursecontact, $user->id, context_course::instance($course->id));
        $this->assertTrue(has_coursecontact_role($user->id));
    }

    /**
     * Test creation of roles.
     * @return void
     */
    public function test_create_role() {
        global $DB;

        $this->resetAfterTest();

        $id = create_role('New student role', 'student2', 'New student description', 'student');
        $role = $DB->get_record('role', array('id'=>$id));

        $this->assertNotEmpty($role);
        $this->assertEquals('New student role', $role->name);
        $this->assertEquals('student2', $role->shortname);
        $this->assertEquals('New student description', $role->description);
        $this->assertEquals('student', $role->archetype);
    }

    /**
     * Test adding of capabilities to roles.
     */
    public function test_assign_capability() {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $syscontext = context_system::instance();
        $frontcontext = context_course::instance(SITEID);
        $student = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $this->assertTrue($DB->record_exists('capabilities', array('name'=>'moodle/backup:backupcourse'))); // any capability assigned to student by default
        $this->assertFalse($DB->record_exists('role_capabilities', array('contextid'=>$syscontext->id, 'roleid'=>$student->id, 'capability'=>'moodle/backup:backupcourse')));
        $this->assertFalse($DB->record_exists('role_capabilities', array('contextid'=>$frontcontext->id, 'roleid'=>$student->id, 'capability'=>'moodle/backup:backupcourse')));

        $this->setUser($user);
        $result = assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $student->id, $frontcontext->id);
        $this->assertTrue($result);
        $permission = $DB->get_record('role_capabilities', array('contextid'=>$frontcontext->id, 'roleid'=>$student->id, 'capability'=>'moodle/backup:backupcourse'));
        $this->assertNotEmpty($permission);
        $this->assertEquals(CAP_ALLOW, $permission->permission);
        $this->assertEquals($user->id, $permission->modifierid);

        $this->setUser(0);
        $result = assign_capability('moodle/backup:backupcourse', CAP_PROHIBIT, $student->id, $frontcontext->id, false);
        $this->assertTrue($result);
        $permission = $DB->get_record('role_capabilities', array('contextid'=>$frontcontext->id, 'roleid'=>$student->id, 'capability'=>'moodle/backup:backupcourse'));
        $this->assertNotEmpty($permission);
        $this->assertEquals(CAP_ALLOW, $permission->permission);
        $this->assertEquals(3, $permission->modifierid);

        $result = assign_capability('moodle/backup:backupcourse', CAP_PROHIBIT, $student->id, $frontcontext->id, true);
        $this->assertTrue($result);
        $permission = $DB->get_record('role_capabilities', array('contextid'=>$frontcontext->id, 'roleid'=>$student->id, 'capability'=>'moodle/backup:backupcourse'));
        $this->assertNotEmpty($permission);
        $this->assertEquals(CAP_PROHIBIT, $permission->permission);
        $this->assertEquals(0, $permission->modifierid);

        $result = assign_capability('moodle/backup:backupcourse', CAP_INHERIT, $student->id, $frontcontext->id);
        $this->assertTrue($result);
        $permission = $DB->get_record('role_capabilities', array('contextid'=>$frontcontext->id, 'roleid'=>$student->id, 'capability'=>'moodle/backup:backupcourse'));
        $this->assertEmpty($permission);
    }

    /**
     * Test removing of capabilities from roles.
     */
    public function test_unassign_capability() {
        global $DB;

        $this->resetAfterTest();

        $syscontext = context_system::instance();
        $frontcontext = context_course::instance(SITEID);
        $manager = $DB->get_record('role', array('shortname'=>'manager'), '*', MUST_EXIST);
        $this->assertTrue($DB->record_exists('capabilities', array('name'=>'moodle/backup:backupcourse'))); // any capability assigned to manager by default
        assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $manager->id, $frontcontext->id);

        $this->assertTrue($DB->record_exists('role_capabilities', array('contextid'=>$syscontext->id, 'roleid'=>$manager->id, 'capability'=>'moodle/backup:backupcourse')));
        $this->assertTrue($DB->record_exists('role_capabilities', array('contextid'=>$frontcontext->id, 'roleid'=>$manager->id, 'capability'=>'moodle/backup:backupcourse')));

        $result = unassign_capability('moodle/backup:backupcourse', $manager->id, $syscontext->id);
        $this->assertTrue($result);
        $this->assertFalse($DB->record_exists('role_capabilities', array('contextid'=>$syscontext->id, 'roleid'=>$manager->id, 'capability'=>'moodle/backup:backupcourse')));
        $this->assertTrue($DB->record_exists('role_capabilities', array('contextid'=>$frontcontext->id, 'roleid'=>$manager->id, 'capability'=>'moodle/backup:backupcourse')));
        unassign_capability('moodle/backup:backupcourse', $manager->id, $frontcontext);
        $this->assertFalse($DB->record_exists('role_capabilities', array('contextid'=>$frontcontext->id, 'roleid'=>$manager->id, 'capability'=>'moodle/backup:backupcourse')));

        assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $manager->id, $syscontext->id);
        assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $manager->id, $frontcontext->id);
        $this->assertTrue($DB->record_exists('role_capabilities', array('contextid'=>$frontcontext->id, 'roleid'=>$manager->id, 'capability'=>'moodle/backup:backupcourse')));

        $result = unassign_capability('moodle/backup:backupcourse', $manager->id);
        $this->assertTrue($result);
        $this->assertFalse($DB->record_exists('role_capabilities', array('contextid'=>$syscontext->id, 'roleid'=>$manager->id, 'capability'=>'moodle/backup:backupcourse')));
        $this->assertFalse($DB->record_exists('role_capabilities', array('contextid'=>$frontcontext->id, 'roleid'=>$manager->id, 'capability'=>'moodle/backup:backupcourse')));
    }

    /**
     * Test role assigning
     */
    public function test_role_assign() {
        global $DB, $USER;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $role = $DB->get_record('role', array('shortname'=>'student'));

        $this->setUser(0);
        $context = context_system::instance();
        $this->assertFalse($DB->record_exists('role_assignments', array('userid'=>$user->id, 'roleid'=>$role->id, 'contextid'=>$context->id)));
        role_assign($role->id, $user->id, $context->id);
        $ras = $DB->get_record('role_assignments', array('userid'=>$user->id, 'roleid'=>$role->id, 'contextid'=>$context->id));
        $this->assertNotEmpty($ras);
        $this->assertSame('', $ras->component);
        $this->assertSame('0', $ras->itemid);
        $this->assertEquals($USER->id, $ras->modifierid);

        $this->setAdminUser();
        $context = context_course::instance($course->id);
        $this->assertFalse($DB->record_exists('role_assignments', array('userid'=>$user->id, 'roleid'=>$role->id, 'contextid'=>$context->id)));
        role_assign($role->id, $user->id, $context->id, 'enrol_self', 1, 666);
        $ras = $DB->get_record('role_assignments', array('userid'=>$user->id, 'roleid'=>$role->id, 'contextid'=>$context->id));
        $this->assertNotEmpty($ras);
        $this->assertSame('enrol_self', $ras->component);
        $this->assertSame('1', $ras->itemid);
        $this->assertEquals($USER->id, $ras->modifierid);
        $this->assertEquals(666, $ras->timemodified);
    }

    /**
     * Test role unassigning
     * @return void
     */
    public function test_role_unassign() {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $role = $DB->get_record('role', array('shortname'=>'student'));

        $context = context_system::instance();
        role_assign($role->id, $user->id, $context->id);
        $this->assertTrue($DB->record_exists('role_assignments', array('userid'=>$user->id, 'roleid'=>$role->id, 'contextid'=>$context->id)));
        role_unassign($role->id, $user->id, $context->id);
        $this->assertFalse($DB->record_exists('role_assignments', array('userid'=>$user->id, 'roleid'=>$role->id, 'contextid'=>$context->id)));

        role_assign($role->id, $user->id, $context->id, 'enrol_self', 1);
        $this->assertTrue($DB->record_exists('role_assignments', array('userid'=>$user->id, 'roleid'=>$role->id, 'contextid'=>$context->id)));
        role_unassign($role->id, $user->id, $context->id, 'enrol_self', 1);
        $this->assertFalse($DB->record_exists('role_assignments', array('userid'=>$user->id, 'roleid'=>$role->id, 'contextid'=>$context->id)));
    }

    /**
     * Test role unassigning
     * @return void
     */
    public function test_role_unassign_all() {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $role = $DB->get_record('role', array('shortname'=>'student'));
        $syscontext = context_system::instance();
        $coursecontext = context_course::instance($course->id);
        $page = $this->getDataGenerator()->create_module('page', array('course'=>$course->id));
        $modcontext = context_module::instance($page->cmid);

        role_assign($role->id, $user->id, $syscontext->id);
        role_assign($role->id, $user->id, $coursecontext->id, 'enrol_self', 1);
        $this->assertEquals(2, $DB->count_records('role_assignments', array('userid'=>$user->id)));
        role_unassign_all(array('userid'=>$user->id, 'roleid'=>$role->id));
        $this->assertEquals(0, $DB->count_records('role_assignments', array('userid'=>$user->id)));

        role_assign($role->id, $user->id, $syscontext->id);
        role_assign($role->id, $user->id, $coursecontext->id, 'enrol_self', 1);
        role_assign($role->id, $user->id, $modcontext->id);
        $this->assertEquals(3, $DB->count_records('role_assignments', array('userid'=>$user->id)));
        role_unassign_all(array('userid'=>$user->id, 'contextid'=>$coursecontext->id), false);
        $this->assertEquals(2, $DB->count_records('role_assignments', array('userid'=>$user->id)));
        role_unassign_all(array('userid'=>$user->id, 'contextid'=>$coursecontext->id), true);
        $this->assertEquals(1, $DB->count_records('role_assignments', array('userid'=>$user->id)));
        role_unassign_all(array('userid'=>$user->id));
        $this->assertEquals(0, $DB->count_records('role_assignments', array('userid'=>$user->id)));

        role_assign($role->id, $user->id, $syscontext->id);
        role_assign($role->id, $user->id, $coursecontext->id, 'enrol_self', 1);
        role_assign($role->id, $user->id, $coursecontext->id);
        role_assign($role->id, $user->id, $modcontext->id);
        $this->assertEquals(4, $DB->count_records('role_assignments', array('userid'=>$user->id)));
        role_unassign_all(array('userid'=>$user->id, 'contextid'=>$coursecontext->id, 'component'=>'enrol_self'), true, true);
        $this->assertEquals(1, $DB->count_records('role_assignments', array('userid'=>$user->id)));
    }

    /**
     * Test role queries.
     * @return void
     */
    public function test_get_roles_with_capability() {
        global $DB;

        $this->resetAfterTest();

        $syscontext = context_system::instance();
        $frontcontext = context_course::instance(SITEID);
        $manager = $DB->get_record('role', array('shortname'=>'manager'), '*', MUST_EXIST);
        $teacher = $DB->get_record('role', array('shortname'=>'teacher'), '*', MUST_EXIST);

        $this->assertTrue($DB->record_exists('capabilities', array('name'=>'moodle/backup:backupcourse'))); // any capability is ok
        $DB->delete_records('role_capabilities', array('capability'=>'moodle/backup:backupcourse'));

        $roles = get_roles_with_capability('moodle/backup:backupcourse');
        $this->assertEquals(array(), $roles);

        assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $manager->id, $syscontext->id);
        assign_capability('moodle/backup:backupcourse', CAP_PROHIBIT, $manager->id, $frontcontext->id);
        assign_capability('moodle/backup:backupcourse', CAP_PREVENT, $teacher->id, $frontcontext->id);

        $roles = get_roles_with_capability('moodle/backup:backupcourse');
        $this->assertEquals(array($teacher->id, $manager->id), array_keys($roles), '', 0, 10, true);

        $roles = get_roles_with_capability('moodle/backup:backupcourse', CAP_ALLOW);
        $this->assertEquals(array($manager->id), array_keys($roles), '', 0, 10, true);

        $roles = get_roles_with_capability('moodle/backup:backupcourse', NULL, $syscontext);
        $this->assertEquals(array($manager->id), array_keys($roles), '', 0, 10, true);
    }

    /**
     * Test deleting of roles.
     * @return void
     */
    public function test_delete_role() {
        global $DB;

        $this->resetAfterTest();

        $role = $DB->get_record('role', array('shortname'=>'manager'), '*', MUST_EXIST);
        $user = $this->getDataGenerator()->create_user();
        role_assign($role->id, $user->id, context_system::instance());
        $course = $this->getDataGenerator()->create_course();
        $rolename = (object)array('roleid'=>$role->id, 'name'=>'Man', 'contextid'=>context_course::instance($course->id)->id);
        $DB->insert_record('role_names', $rolename);

        $this->assertTrue($DB->record_exists('role_assignments', array('roleid'=>$role->id)));
        $this->assertTrue($DB->record_exists('role_capabilities', array('roleid'=>$role->id)));
        $this->assertTrue($DB->record_exists('role_names', array('roleid'=>$role->id)));
        $this->assertTrue($DB->record_exists('role_context_levels', array('roleid'=>$role->id)));
        $this->assertTrue($DB->record_exists('role_allow_assign', array('roleid'=>$role->id)));
        $this->assertTrue($DB->record_exists('role_allow_assign', array('allowassign'=>$role->id)));
        $this->assertTrue($DB->record_exists('role_allow_override', array('roleid'=>$role->id)));
        $this->assertTrue($DB->record_exists('role_allow_override', array('allowoverride'=>$role->id)));

        $result = delete_role($role->id);
        $this->assertTrue($result);
        $this->assertFalse($DB->record_exists('role', array('id'=>$role->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('roleid'=>$role->id)));
        $this->assertFalse($DB->record_exists('role_capabilities', array('roleid'=>$role->id)));
        $this->assertFalse($DB->record_exists('role_names', array('roleid'=>$role->id)));
        $this->assertFalse($DB->record_exists('role_context_levels', array('roleid'=>$role->id)));
        $this->assertFalse($DB->record_exists('role_allow_assign', array('roleid'=>$role->id)));
        $this->assertFalse($DB->record_exists('role_allow_assign', array('allowassign'=>$role->id)));
        $this->assertFalse($DB->record_exists('role_allow_override', array('roleid'=>$role->id)));
        $this->assertFalse($DB->record_exists('role_allow_override', array('allowoverride'=>$role->id)));
    }

    /**
     * Test fetching of all roles.
     * @return void
     */
    public function test_get_all_roles() {
        global $DB;

        $this->resetAfterTest();

        $allroles = get_all_roles();
        $this->assertEquals('array', gettype($allroles));
        $this->assertCount(8, $allroles); // there are 8 roles is standard install

        $role = reset($allroles);
        $role = (array)$role;

        $this->assertEquals(array('id', 'name', 'shortname', 'description', 'sortorder', 'archetype'), array_keys($role), '', 0, 10, true);

        foreach($allroles as $roleid => $role) {
            $this->assertEquals($role->id, $roleid);
        }

        $teacher = $DB->get_record('role', array('shortname'=>'teacher'), '*', MUST_EXIST);
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $otherid = create_role('Other role', 'other', 'Some other role', '');
        $teacherename = (object)array('roleid'=>$teacher->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherename);
        $otherrename = (object)array('roleid'=>$otherid, 'name'=>'Ostatní', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $otherrename);
        $renames = $DB->get_records_menu('role_names', array('contextid'=>$coursecontext->id), '', 'roleid,name');

        $allroles = get_all_roles($coursecontext);
        $this->assertEquals('array', gettype($allroles));
        $this->assertCount(9, $allroles);
        $role = reset($allroles);
        $role = (array)$role;

        $this->assertEquals(array('id', 'name', 'shortname', 'description', 'sortorder', 'archetype', 'coursealias'), array_keys($role), '', 0, 10, true);

        foreach($allroles as $roleid => $role) {
            $this->assertEquals($role->id, $roleid);
            if (isset($renames[$roleid])) {
                $this->assertSame($renames[$roleid], $role->coursealias);
            } else {
                $this->assertSame(null, $role->coursealias);
            }
        }
    }

    /**
     * Test getting of all archetypes.
     * @return void
     */
    public function test_get_role_archetypes() {
        $archetypes = get_role_archetypes();
        $this->assertCount(8, $archetypes); // there are 8 archetypes in standard install
        foreach ($archetypes as $k=>$v) {
            $this->assertSame($k, $v);
        }
    }

    /**
     * Test getting of roles with given archetype.
     * @return void
     */
    public function test_get_archetype_roles() {
        $this->resetAfterTest();

        // New install should have 1 role for each archetype.
        $archetypes = get_role_archetypes();
        foreach ($archetypes as $archetype) {
            $roles = get_archetype_roles($archetype);
            $this->assertCount(1, $roles);
            $role = reset($roles);
            $this->assertEquals($archetype, $role->archetype);
        }

        create_role('New student role', 'student2', 'New student description', 'student');
        $roles = get_archetype_roles('student');
        $this->assertCount(2, $roles);
    }

    /**
     * Test aliased role names
     * @return void
     */
    public function test_role_get_name() {
        global $DB;

        $this->resetAfterTest();

        $allroles = $DB->get_records('role');
        $teacher = $DB->get_record('role', array('shortname'=>'teacher'), '*', MUST_EXIST);
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $otherid = create_role('Other role', 'other', 'Some other role', '');
        $teacherename = (object)array('roleid'=>$teacher->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherename);
        $otherrename = (object)array('roleid'=>$otherid, 'name'=>'Ostatní', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $otherrename);
        $renames = $DB->get_records_menu('role_names', array('contextid'=>$coursecontext->id), '', 'roleid,name');

        foreach ($allroles as $role) {
            // Get localised name from lang pack.
            $this->assertSame('', $role->name);
            $name = role_get_name($role, null, ROLENAME_ORIGINAL);
            $this->assertNotEmpty($name);
            $this->assertNotEquals($role->shortname, $name);

            if (isset($renames[$role->id])) {
                $this->assertSame($renames[$role->id], role_get_name($role, $coursecontext));
                $this->assertSame($renames[$role->id], role_get_name($role, $coursecontext, ROLENAME_ALIAS));
                $this->assertSame($renames[$role->id], role_get_name($role, $coursecontext, ROLENAME_ALIAS_RAW));
                $this->assertSame("{$renames[$role->id]} ($name)", role_get_name($role, $coursecontext, ROLENAME_BOTH));
            } else {
                $this->assertSame($name, role_get_name($role, $coursecontext));
                $this->assertSame($name, role_get_name($role, $coursecontext, ROLENAME_ALIAS));
                $this->assertSame(null, role_get_name($role, $coursecontext, ROLENAME_ALIAS_RAW));
                $this->assertSame($name, role_get_name($role, $coursecontext, ROLENAME_BOTH));
            }
            $this->assertSame($name, role_get_name($role));
            $this->assertSame($name, role_get_name($role, $coursecontext, ROLENAME_ORIGINAL));
            $this->assertSame($name, role_get_name($role, null, ROLENAME_ORIGINAL));
            $this->assertSame($role->shortname, role_get_name($role, $coursecontext, ROLENAME_SHORT));
            $this->assertSame($role->shortname, role_get_name($role, null, ROLENAME_SHORT));
            $this->assertSame("$name ($role->shortname)", role_get_name($role, $coursecontext, ROLENAME_ORIGINALANDSHORT));
            $this->assertSame("$name ($role->shortname)", role_get_name($role, null, ROLENAME_ORIGINALANDSHORT));
            $this->assertSame(null, role_get_name($role, null, ROLENAME_ALIAS_RAW));
        }
    }

    /**
     * Test tweaking of role name arrays
     * @return void
     */
    public function test_role_fix_names() {
        global $DB;

        $this->resetAfterTest();

        $teacher = $DB->get_record('role', array('shortname'=>'teacher'), '*', MUST_EXIST);
        $student = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $otherid = create_role('Other role', 'other', 'Some other role', '');
        $anotherid = create_role('Another role', 'another', 'Yet another other role', '');
        $allroles = $DB->get_records('role');

        $syscontext = context_system::instance();
        $frontcontext = context_course::instance(SITEID);
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $category = $DB->get_record('course_categories', array('id'=>$course->category), '*', MUST_EXIST);
        $categorycontext = context_coursecat::instance($category->id);

        $teacherename = (object)array('roleid'=>$teacher->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherename);
        $otherrename = (object)array('roleid'=>$otherid, 'name'=>'Ostatní', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $otherrename);
        $renames = $DB->get_records_menu('role_names', array('contextid'=>$coursecontext->id), '', 'roleid,name');

        // Make sure all localname contain proper values for each ROLENAME_ constant,
        // note role_get_name() on frontpage is used to get the original name for future comatibility.
        $roles = $allroles;
        unset($roles[$student->id]); // Remove one roel to make sure no role is added or removed.
        $rolenames = array();
        foreach ($roles as $role) {
            $rolenames[$role->id] = $role->name;
        }

        $alltypes = array(ROLENAME_ALIAS, ROLENAME_ALIAS_RAW, ROLENAME_BOTH, ROLENAME_ORIGINAL, ROLENAME_ORIGINALANDSHORT, ROLENAME_SHORT);
        foreach ($alltypes as $type) {
            $fixed = role_fix_names($roles, $coursecontext, $type);
            $this->assertCount(count($roles), $fixed);
            foreach($fixed as $roleid=>$rolename) {
                $this->assertInstanceOf('stdClass', $rolename);
                $role = $allroles[$roleid];
                $name = role_get_name($role, $coursecontext, $type);
                $this->assertSame($name, $rolename->localname);
            }
            $fixed = role_fix_names($rolenames, $coursecontext, $type);
            $this->assertCount(count($rolenames), $fixed);
            foreach($fixed as $roleid=>$rolename) {
                $role = $allroles[$roleid];
                $name = role_get_name($role, $coursecontext, $type);
                $this->assertSame($name, $rolename);
            }
        }
    }

    /**
     * Test allowing of role assignments.
     * @return void
     */
    public function test_allow_assign() {
        global $DB;

        $this->resetAfterTest();

        $otherid = create_role('Other role', 'other', 'Some other role', '');
        $student = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);

        $this->assertFalse($DB->record_exists('role_allow_assign', array('roleid'=>$otherid, 'allowassign'=>$student->id)));
        allow_assign($otherid, $student->id);
        $this->assertTrue($DB->record_exists('role_allow_assign', array('roleid'=>$otherid, 'allowassign'=>$student->id)));
    }

    /**
     * Test allowing of role overrides.
     * @return void
     */
    public function test_allow_override() {
        global $DB;

        $this->resetAfterTest();

        $otherid = create_role('Other role', 'other', 'Some other role', '');
        $student = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);

        $this->assertFalse($DB->record_exists('role_allow_override', array('roleid'=>$otherid, 'allowoverride'=>$student->id)));
        allow_override($otherid, $student->id);
        $this->assertTrue($DB->record_exists('role_allow_override', array('roleid'=>$otherid, 'allowoverride'=>$student->id)));
    }

    /**
     * Test allowing of role switching.
     * @return void
     */
    public function test_allow_switch() {
        global $DB;

        $this->resetAfterTest();

        $otherid = create_role('Other role', 'other', 'Some other role', '');
        $student = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);

        $this->assertFalse($DB->record_exists('role_allow_switch', array('roleid'=>$otherid, 'allowswitch'=>$student->id)));
        allow_switch($otherid, $student->id);
        $this->assertTrue($DB->record_exists('role_allow_switch', array('roleid'=>$otherid, 'allowswitch'=>$student->id)));
    }

    /**
     * Test returning of assignable roles in context.
     * @return void
     */
    public function test_get_assignable_roles() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        $teacherename = (object)array('roleid'=>$teacher->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherename);

        $studentrole = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $student = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $student->id, $coursecontext);

        $contexts = $DB->get_records('context');
        $users = $DB->get_records('user');
        $allroles = $DB->get_records('role');

        // Evaluate all results for all users in all contexts.
        foreach($users as $user) {
            $this->setUser($user);
            foreach ($contexts as $contextid=>$unused) {
                $context = context_helper::instance_by_id($contextid);
                $roles = get_assignable_roles($context, ROLENAME_SHORT);
                foreach ($allroles as $roleid=>$role) {
                    if (isset($roles[$roleid])) {
                        if (is_siteadmin()) {
                            $this->assertTrue($DB->record_exists('role_context_levels', array('contextlevel'=>$context->contextlevel, 'roleid'=>$roleid)));
                        } else {
                            $this->assertTrue(user_can_assign($context, $roleid), "u:$user->id r:$roleid");
                        }
                        $this->assertEquals($role->shortname, $roles[$roleid]);
                    } else {
                        $allowed = $DB->record_exists('role_context_levels', array('contextlevel'=>$context->contextlevel, 'roleid'=>$roleid));
                        if (is_siteadmin()) {
                            $this->assertFalse($allowed);
                        } else {
                            $this->assertFalse($allowed and user_can_assign($context, $roleid), "u:$user->id, r:{$allroles[$roleid]->name}, c:$context->contextlevel");
                        }
                    }
                }
            }
        }

        // not-logged-in user
        $this->setUser(0);
        foreach ($contexts as $contextid=>$unused) {
            $context = context_helper::instance_by_id($contextid);
            $roles = get_assignable_roles($context, ROLENAME_SHORT);
            $this->assertSame(array(), $roles);
        }

        // Test current user.
        $this->setUser(0);
        $admin = $DB->get_record('user', array('username'=>'admin'), '*', MUST_EXIST);
        $roles1 = get_assignable_roles($coursecontext, ROLENAME_SHORT, false, $admin);
        $roles2 = get_assignable_roles($coursecontext, ROLENAME_SHORT, false, $admin->id);
        $this->setAdminUser();
        $roles3 = get_assignable_roles($coursecontext, ROLENAME_SHORT);
        $this->assertSame($roles1, $roles3);
        $this->assertSame($roles2, $roles3);

        // Test parameter defaults.
        $this->setAdminUser();
        $roles1 = get_assignable_roles($coursecontext);
        $roles2 = get_assignable_roles($coursecontext, ROLENAME_ALIAS, false, $admin);
        $this->assertEquals($roles2, $roles1);

        // Verify returned names - let's allow all roles everywhere to simplify this a bit.
        $alllevels = context_helper::get_all_levels();
        $alllevels = array_keys($alllevels);
        foreach($allroles as $roleid=>$role) {
            set_role_contextlevels($roleid, $alllevels);
        }
        $alltypes = array(ROLENAME_ALIAS, ROLENAME_ALIAS_RAW, ROLENAME_BOTH, ROLENAME_ORIGINAL, ROLENAME_ORIGINALANDSHORT, ROLENAME_SHORT);
        foreach ($alltypes as $type) {
            $rolenames = role_fix_names($allroles, $coursecontext, $type);
            $roles = get_assignable_roles($coursecontext, $type, false, $admin);
            foreach ($roles as $roleid=>$rolename) {
                $this->assertSame($rolenames[$roleid]->localname, $rolename);
            }
        }

        // Verify counts.
        $alltypes = array(ROLENAME_ALIAS, ROLENAME_ALIAS_RAW, ROLENAME_BOTH, ROLENAME_ORIGINAL, ROLENAME_ORIGINALANDSHORT, ROLENAME_SHORT);
        foreach ($alltypes as $type) {
            $roles = get_assignable_roles($coursecontext, $type, false, $admin);
            list($rolenames, $rolecounts, $nameswithcounts) = get_assignable_roles($coursecontext, $type, true, $admin);
            $this->assertEquals($roles, $rolenames);
            foreach ($rolenames as $roleid=>$name) {
                if ($roleid == $teacherrole->id or $roleid == $studentrole->id) {
                    $this->assertEquals(1, $rolecounts[$roleid]);
                } else {
                    $this->assertEquals(0, $rolecounts[$roleid]);
                }
                $this->assertEquals("$name ($rolecounts[$roleid])", $nameswithcounts[$roleid]);
            }
        }
    }

    /**
     * Test getting of all switchable roles.
     * @retrun void
     */
    public function test_get_switchable_roles() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        $teacherename = (object)array('roleid'=>$teacher->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherename);

        $contexts = $DB->get_records('context');
        $users = $DB->get_records('user');
        $allroles = $DB->get_records('role');

        // Evaluate all results for all users in all contexts.
        foreach($users as $user) {
            $this->setUser($user);
            foreach ($contexts as $contextid=>$unused) {
                $context = context_helper::instance_by_id($contextid);
                $roles = get_switchable_roles($context);
                foreach ($allroles as $roleid=>$role) {
                    if (is_siteadmin()) {
                        $this->assertTrue(isset($roles[$roleid]));
                    } else {
                        $parents = $context->get_parent_context_ids(true);
                        $pcontexts = implode(',' , $parents);
                        $allowed = $DB->record_exists_sql(
                            "SELECT r.id
                               FROM {role} r
                               JOIN {role_allow_switch} ras ON ras.allowswitch = r.id
                               JOIN {role_assignments} ra ON ra.roleid = ras.roleid
                              WHERE ra.userid = :userid AND ra.contextid IN ($pcontexts) AND r.id = :roleid
                            ",
                            array('userid'=>$user->id, 'roleid'=>$roleid)
                        );
                        if (isset($roles[$roleid])) {
                            $this->assertTrue($allowed);
                        } else {
                            $this->assertFalse($allowed);
                        }
                    }

                    if (isset($roles[$roleid])) {
                        $coursecontext = $context->get_course_context(false);
                        $this->assertEquals(role_get_name($role, $coursecontext), $roles[$roleid]);
                    }
                }
            }
        }
    }

    /**
     * Test getting of all overridable roles.
     * @return void
     */
    public function test_get_overridable_roles() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        $teacherename = (object)array('roleid'=>$teacher->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherename);
        $this->assertTrue($DB->record_exists('capabilities', array('name'=>'moodle/backup:backupcourse'))); // any capability is ok
        assign_capability('moodle/backup:backupcourse', CAP_PROHIBIT, $teacher->id, $coursecontext->id);

        $studentrole = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $student = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $student->id, $coursecontext);

        $contexts = $DB->get_records('context');
        $users = $DB->get_records('user');
        $allroles = $DB->get_records('role');

        // Evaluate all results for all users in all contexts.
        foreach($users as $user) {
            $this->setUser($user);
            foreach ($contexts as $contextid=>$unused) {
                $context = context_helper::instance_by_id($contextid);
                $roles = get_overridable_roles($context, ROLENAME_SHORT);
                foreach ($allroles as $roleid=>$role) {
                    $hascap = has_any_capability(array('moodle/role:safeoverride', 'moodle/role:override'), $context);
                    if (is_siteadmin()) {
                        $this->assertTrue(isset($roles[$roleid]));
                    } else {
                        $parents = $context->get_parent_context_ids(true);
                        $pcontexts = implode(',' , $parents);
                        $allowed = $DB->record_exists_sql(
                            "SELECT r.id
                               FROM {role} r
                               JOIN {role_allow_override} rao ON r.id = rao.allowoverride
                               JOIN {role_assignments} ra ON rao.roleid = ra.roleid
                              WHERE ra.userid = :userid AND ra.contextid IN ($pcontexts) AND r.id = :roleid
                            ",
                            array('userid'=>$user->id, 'roleid'=>$roleid)
                        );
                        if (isset($roles[$roleid])) {
                            $this->assertTrue($hascap);
                            $this->assertTrue($allowed);
                        } else {
                            $this->assertFalse($hascap and $allowed);
                        }
                    }

                    if (isset($roles[$roleid])) {
                        $this->assertEquals($role->shortname, $roles[$roleid]);
                    }
                }
            }
        }

        // Test parameter defaults.
        $this->setAdminUser();
        $roles1 = get_overridable_roles($coursecontext);
        $roles2 = get_overridable_roles($coursecontext, ROLENAME_ALIAS, false);
        $this->assertEquals($roles2, $roles1);

        $alltypes = array(ROLENAME_ALIAS, ROLENAME_ALIAS_RAW, ROLENAME_BOTH, ROLENAME_ORIGINAL, ROLENAME_ORIGINALANDSHORT, ROLENAME_SHORT);
        foreach ($alltypes as $type) {
            $rolenames = role_fix_names($allroles, $coursecontext, $type);
            $roles = get_overridable_roles($coursecontext, $type, false);
            foreach ($roles as $roleid=>$rolename) {
                $this->assertSame($rolenames[$roleid]->localname, $rolename);
            }
        }

        // Verify counts.
        $roles = get_overridable_roles($coursecontext, ROLENAME_ALIAS, false);
        list($rolenames, $rolecounts, $nameswithcounts) = get_overridable_roles($coursecontext, ROLENAME_ALIAS, true);
        $this->assertEquals($roles, $rolenames);
        foreach ($rolenames as $roleid=>$name) {
            if ($roleid == $teacherrole->id) {
                $this->assertEquals(1, $rolecounts[$roleid]);
            } else {
                $this->assertEquals(0, $rolecounts[$roleid]);
            }
            $this->assertEquals("$name ($rolecounts[$roleid])", $nameswithcounts[$roleid]);
        }
    }

    /**
     * Test we have context level defaults.
     * @return void
     */
    public function test_get_default_contextlevels() {
        $archetypes = get_role_archetypes();
        $alllevels = context_helper::get_all_levels();
        foreach ($archetypes as $archetype) {
            $defaults = get_default_contextlevels($archetype);
            $this->assertTrue(is_array($defaults));
            foreach ($defaults as $level) {
                $this->assertTrue(isset($alllevels[$level]));
            }
        }
    }

    /**
     * Test role context level setup.
     * @return void
     */
    public function test_set_role_contextlevels() {
        global $DB;

        $this->resetAfterTest();

        $roleid = create_role('New student role', 'student2', 'New student description', 'student');

        $this->assertFalse($DB->record_exists('role_context_levels', array('roleid' => $roleid)));

        set_role_contextlevels($roleid, array(CONTEXT_COURSE, CONTEXT_MODULE));
        $levels = $DB->get_records('role_context_levels', array('roleid' => $roleid), '', 'contextlevel, contextlevel');
        $this->assertCount(2, $levels);
        $this->assertTrue(isset($levels[CONTEXT_COURSE]));
        $this->assertTrue(isset($levels[CONTEXT_MODULE]));

        set_role_contextlevels($roleid, array(CONTEXT_COURSE));
        $levels = $DB->get_records('role_context_levels', array('roleid' => $roleid), '', 'contextlevel, contextlevel');
        $this->assertCount(1, $levels);
        $this->assertTrue(isset($levels[CONTEXT_COURSE]));
    }

    /**
     * Test getting of role context levels
     * @return void
     */
    public function test_get_roles_for_contextlevels() {
        global $DB;

        $allroles = get_all_roles();
        foreach (context_helper::get_all_levels() as $level=>$unused) {
            $roles = get_roles_for_contextlevels($level);
            foreach ($allroles as $roleid=>$unused) {
                $exists = $DB->record_exists('role_context_levels', array('contextlevel'=>$level, 'roleid'=>$roleid));
                if (in_array($roleid, $roles)) {
                    $this->assertTrue($exists);
                } else {
                    $this->assertFalse($exists);
                }
            }
        }
    }

    /**
     * Test default enrol roles.
     * @return void
     */
    public function test_get_default_enrol_roles() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $id2 = create_role('New student role', 'student2', 'New student description', 'student');
        set_role_contextlevels($id2, array(CONTEXT_COURSE));

        $allroles = get_all_roles();
        $expected = array($id2=>$allroles[$id2]);

        foreach(get_role_archetypes() as $archetype) {
            $defaults = get_default_contextlevels($archetype);
            if (in_array(CONTEXT_COURSE, $defaults)) {
                $roles = get_archetype_roles($archetype);
                foreach($roles as $role) {
                    $expected[$role->id] = $role;
                }
            }
        }

        $roles = get_default_enrol_roles($coursecontext);
        foreach ($allroles as $role) {
            $this->assertEquals(isset($expected[$role->id]), isset($roles[$role->id]));
            if (isset($roles[$role->id])) {
                $this->assertEquals(role_get_name($role, $coursecontext), $roles[$role->id]);
            }
        }
    }

    /**
     * Test getting of role users.
     * @return void
     */
    public function test_get_role_users() {
        global $DB;

        $this->resetAfterTest();

        $systemcontext = context_system::instance();
        $studentrole = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $otherid = create_role('Other role', 'other', 'Some other role', '');
        $teacherrename = (object)array('roleid'=>$teacherrole->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherrename);
        $otherrename = (object)array('roleid'=>$otherid, 'name'=>'Ostatní', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $otherrename);

        $user1 = $this->getDataGenerator()->create_user(array('firstname'=>'John', 'lastname'=>'Smith'));
        role_assign($teacherrole->id, $user1->id, $coursecontext->id);
        $user2 = $this->getDataGenerator()->create_user(array('firstname'=>'Jan', 'lastname'=>'Kovar'));
        role_assign($teacherrole->id, $user2->id, $systemcontext->id);
        $user3 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, $teacherrole->id);
        $user4 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user4->id, $course->id, $studentrole->id);

        $group = $this->getDataGenerator()->create_group(array('courseid'=>$course->id));
        groups_add_member($group, $user3);

        $users = get_role_users($teacherrole->id, $coursecontext);
        $this->assertCount(2, $users);
        $this->assertArrayHasKey($user1->id, $users);
        $this->assertEquals($users[$user1->id]->id, $user1->id);
        $this->assertEquals($users[$user1->id]->roleid, $teacherrole->id);
        $this->assertEquals($users[$user1->id]->rolename, $teacherrole->name);
        $this->assertEquals($users[$user1->id]->roleshortname, $teacherrole->shortname);
        $this->assertEquals($users[$user1->id]->rolecoursealias, $teacherrename->name);
        $this->assertArrayHasKey($user3->id, $users);
        $this->assertEquals($users[$user3->id]->id, $user3->id);
        $this->assertEquals($users[$user3->id]->roleid, $teacherrole->id);
        $this->assertEquals($users[$user3->id]->rolename, $teacherrole->name);
        $this->assertEquals($users[$user3->id]->roleshortname, $teacherrole->shortname);
        $this->assertEquals($users[$user3->id]->rolecoursealias, $teacherrename->name);

        $users = get_role_users($teacherrole->id, $coursecontext, true);
        $this->assertCount(3, $users);

        $users = get_role_users($teacherrole->id, $coursecontext, true, '', null, null, '', 2, 1);
        $this->assertCount(1, $users);

        $users = get_role_users($teacherrole->id, $coursecontext, false, 'u.id, u.email, u.idnumber', 'u.idnumber');
        $this->assertCount(2, $users);
        $this->assertArrayHasKey($user1->id, $users);
        $this->assertArrayHasKey($user3->id, $users);

        $users = get_role_users($teacherrole->id, $coursecontext, false, 'u.id, u.email, u.idnumber', 'u.idnumber', null, $group->id);
        $this->assertCount(1, $users);
        $this->assertArrayHasKey($user3->id, $users);

        $users = get_role_users($teacherrole->id, $coursecontext, true, 'u.id, u.email, u.idnumber, u.firstname', 'u.idnumber', null, '', '', '', 'u.firstname = :xfirstname', array('xfirstname'=>'John'));
        $this->assertCount(1, $users);
        $this->assertArrayHasKey($user1->id, $users);
    }

    /**
     * Test used role query.
     * @return void
     */
    public function test_get_roles_used_in_context() {
        global $DB;

        $this->resetAfterTest();

        $systemcontext = context_system::instance();
        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $otherid = create_role('Other role', 'other', 'Some other role', '');
        $teacherrename = (object)array('roleid'=>$teacherrole->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherrename);
        $otherrename = (object)array('roleid'=>$otherid, 'name'=>'Ostatní', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $otherrename);

        $user1 = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $user1->id, $coursecontext->id);

        $roles = get_roles_used_in_context($coursecontext);
        $this->assertCount(1, $roles);
        $role = reset($roles);
        $roleid = key($roles);
        $this->assertEquals($roleid, $role->id);
        $this->assertEquals($teacherrole->id, $role->id);
        $this->assertEquals($teacherrole->name, $role->name);
        $this->assertEquals($teacherrole->shortname, $role->shortname);
        $this->assertEquals($teacherrole->sortorder, $role->sortorder);
        $this->assertEquals($teacherrename->name, $role->coursealias);

        $user2 = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $user2->id, $systemcontext->id);
        role_assign($otherid, $user2->id, $systemcontext->id);

        $roles = get_roles_used_in_context($systemcontext);
        $this->assertCount(2, $roles);
    }

    /**
     * Test roles used in course.
     * @return void
     */
    public function test_get_user_roles_in_course() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $studentrole = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $teacherrename = (object)array('roleid'=>$teacherrole->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherrename);

        $roleids = explode(',', $CFG->profileroles); // should include teacher and student in new installs
        $this->assertTrue(in_array($teacherrole->id, $roleids));
        $this->assertTrue(in_array($studentrole->id, $roleids));

        $user1 = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $user1->id, $coursecontext->id);
        role_assign($studentrole->id, $user1->id, $coursecontext->id);
        $user2 = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $user2->id, $coursecontext->id);
        $user3 = $this->getDataGenerator()->create_user();

        $roles = get_user_roles_in_course($user1->id, $course->id);
        $this->assertEquals(1, preg_match_all('/,/', $roles, $matches));
        $this->assertTrue(strpos($roles, role_get_name($teacherrole, $coursecontext)) !== false);

        $roles = get_user_roles_in_course($user2->id, $course->id);
        $this->assertEquals(0, preg_match_all('/,/', $roles, $matches));
        $this->assertTrue(strpos($roles, role_get_name($studentrole, $coursecontext)) !== false);

        $roles = get_user_roles_in_course($user3->id, $course->id);
        $this->assertSame('', $roles);
    }

    /**
     * Test has_capability(), has_any_capability() and has_all_capabilities().
     */
    public function test_has_capability_and_friends() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        $admin = $DB->get_record('user', array('username'=>'admin'));

        // Note: Here are used default capabilities, the full test is in permission evaluation bellow,
        // use two capabilities that teacher has and one does not, none of them should be allowed for not-logged-in user.

        $this->assertTrue($DB->record_exists('capabilities', array('name'=>'moodle/backup:backupsection')));
        $this->assertTrue($DB->record_exists('capabilities', array('name'=>'moodle/backup:backupcourse')));
        $this->assertTrue($DB->record_exists('capabilities', array('name'=>'moodle/site:approvecourse')));

        $sca = array('moodle/backup:backupsection', 'moodle/backup:backupcourse', 'moodle/site:approvecourse');
        $sc = array('moodle/backup:backupsection', 'moodle/backup:backupcourse');

        $this->setUser(0);
        $this->assertFalse(has_capability('moodle/backup:backupsection', $coursecontext));
        $this->assertFalse(has_capability('moodle/backup:backupcourse', $coursecontext));
        $this->assertFalse(has_capability('moodle/site:approvecourse', $coursecontext));
        $this->assertFalse(has_any_capability($sca, $coursecontext));
        $this->assertFalse(has_all_capabilities($sca, $coursecontext));

        $this->assertTrue(has_capability('moodle/backup:backupsection', $coursecontext, $teacher));
        $this->assertTrue(has_capability('moodle/backup:backupcourse', $coursecontext, $teacher));
        $this->assertFalse(has_capability('moodle/site:approvecourse', $coursecontext, $teacher));
        $this->assertTrue(has_any_capability($sca, $coursecontext, $teacher));
        $this->assertTrue(has_all_capabilities($sc, $coursecontext, $teacher));
        $this->assertFalse(has_all_capabilities($sca, $coursecontext, $teacher));

        $this->assertTrue(has_capability('moodle/backup:backupsection', $coursecontext, $admin));
        $this->assertTrue(has_capability('moodle/backup:backupcourse', $coursecontext, $admin));
        $this->assertTrue(has_capability('moodle/site:approvecourse', $coursecontext, $admin));
        $this->assertTrue(has_any_capability($sca, $coursecontext, $admin));
        $this->assertTrue(has_all_capabilities($sc, $coursecontext, $admin));
        $this->assertTrue(has_all_capabilities($sca, $coursecontext, $admin));

        $this->assertFalse(has_capability('moodle/backup:backupsection', $coursecontext, $admin, false));
        $this->assertFalse(has_capability('moodle/backup:backupcourse', $coursecontext, $admin, false));
        $this->assertFalse(has_capability('moodle/site:approvecourse', $coursecontext, $admin, false));
        $this->assertFalse(has_any_capability($sca, $coursecontext, $admin, false));
        $this->assertFalse(has_all_capabilities($sc, $coursecontext, $admin, false));
        $this->assertFalse(has_all_capabilities($sca, $coursecontext, $admin, false));

        $this->setUser($teacher);
        $this->assertTrue(has_capability('moodle/backup:backupsection', $coursecontext));
        $this->assertTrue(has_capability('moodle/backup:backupcourse', $coursecontext));
        $this->assertFalse(has_capability('moodle/site:approvecourse', $coursecontext));
        $this->assertTrue(has_any_capability($sca, $coursecontext));
        $this->assertTrue(has_all_capabilities($sc, $coursecontext));
        $this->assertFalse(has_all_capabilities($sca, $coursecontext));

        $this->setAdminUser();
        $this->assertTrue(has_capability('moodle/backup:backupsection', $coursecontext));
        $this->assertTrue(has_capability('moodle/backup:backupcourse', $coursecontext));
        $this->assertTrue(has_capability('moodle/site:approvecourse', $coursecontext));
        $this->assertTrue(has_any_capability($sca, $coursecontext));
        $this->assertTrue(has_all_capabilities($sc, $coursecontext));
        $this->assertTrue(has_all_capabilities($sca, $coursecontext));

        $this->assertFalse(has_capability('moodle/backup:backupsection', $coursecontext, 0));
        $this->assertFalse(has_capability('moodle/backup:backupcourse', $coursecontext, 0));
        $this->assertFalse(has_capability('moodle/site:approvecourse', $coursecontext, 0));
        $this->assertFalse(has_any_capability($sca, $coursecontext, 0));
        $this->assertFalse(has_all_capabilities($sca, $coursecontext, 0));
    }

    /**
     * Test require_capability() exceptions.
     * @return void
     */
    public function test_require_capability() {
        $this->resetAfterTest();

        $syscontext = context_system::instance();

        $this->setUser(0);
        $this->assertFalse(has_capability('moodle/site:config', $syscontext));
        try {
            require_capability('moodle/site:config', $syscontext);
            $this->fail('Exception expected from require_capability()');
        } catch (Exception $e) {
            $this->assertInstanceOf('required_capability_exception', $e);
        }
        $this->setAdminUser();
        $this->assertFalse(has_capability('moodle/site:config', $syscontext, 0));
        try {
            require_capability('moodle/site:config', $syscontext, 0);
            $this->fail('Exception expected from require_capability()');
        } catch (Exception $e) {
            $this->assertInstanceOf('required_capability_exception', $e);
        }
        $this->assertFalse(has_capability('moodle/site:config', $syscontext, null, false));
        try {
            require_capability('moodle/site:config', $syscontext, null, false);
            $this->fail('Exception expected from require_capability()');
        } catch (Exception $e) {
            $this->assertInstanceOf('required_capability_exception', $e);
        }


    }

    /**
     * A small functional test of permission evaluations.
     * @return void
     */
    public function test_permission_evaluation() {
        global $USER, $SITE, $CFG, $DB, $ACCESSLIB_PRIVATE;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Fill the site with some real data
        $testcategories = array();
        $testcourses = array();
        $testpages = array();
        $testblocks = array();
        $allroles = $DB->get_records_menu('role', array(), 'id', 'archetype, id');

        $systemcontext = context_system::instance();
        $frontpagecontext = context_course::instance(SITEID);

        // Add block to system context
        $bi = $generator->create_block('online_users');
        context_block::instance($bi->id);
        $testblocks[] = $bi->id;

        // Some users
        $testusers = array();
        for($i=0; $i<20; $i++) {
            $user = $generator->create_user();
            $testusers[$i] = $user->id;
            $usercontext = context_user::instance($user->id);

            // Add block to user profile
            $bi = $generator->create_block('online_users', array('parentcontextid'=>$usercontext->id));
            $testblocks[] = $bi->id;
        }
        // Deleted user - should be ignored everywhere, can not have context
        $generator->create_user(array('deleted'=>1));

        // Add block to frontpage
        $bi = $generator->create_block('online_users', array('parentcontextid'=>$frontpagecontext->id));
        $frontpageblockcontext = context_block::instance($bi->id);
        $testblocks[] = $bi->id;

        // Add a resource to frontpage
        $page = $generator->create_module('page', array('course'=>$SITE->id));
        $testpages[] = $page->id;
        $frontpagepagecontext = context_module::instance($page->cmid);

        // Add block to frontpage resource
        $bi = $generator->create_block('online_users', array('parentcontextid'=>$frontpagepagecontext->id));
        $frontpagepageblockcontext = context_block::instance($bi->id);
        $testblocks[] = $bi->id;

        // Some nested course categories with courses
        $manualenrol = enrol_get_plugin('manual');
        $parentcat = 0;
        for($i=0; $i<5; $i++) {
            $cat = $generator->create_category(array('parent'=>$parentcat));
            $testcategories[] = $cat->id;
            $catcontext = context_coursecat::instance($cat->id);
            $parentcat = $cat->id;

            if ($i >= 4) {
                continue;
            }

            // Add resource to each category
            $bi = $generator->create_block('online_users', array('parentcontextid'=>$catcontext->id));
            context_block::instance($bi->id);

            // Add a few courses to each category
            for($j=0; $j<6; $j++) {
                $course = $generator->create_course(array('category'=>$cat->id));
                $testcourses[] = $course->id;
                $coursecontext = context_course::instance($course->id);

                if ($j >= 5) {
                    continue;
                }
                // Add manual enrol instance
                $manualenrol->add_default_instance($DB->get_record('course', array('id'=>$course->id)));

                // Add block to each course
                $bi = $generator->create_block('online_users', array('parentcontextid'=>$coursecontext->id));
                $testblocks[] = $bi->id;

                // Add a resource to each course
                $page = $generator->create_module('page', array('course'=>$course->id));
                $testpages[] = $page->id;
                $modcontext = context_module::instance($page->cmid);

                // Add block to each module
                $bi = $generator->create_block('online_users', array('parentcontextid'=>$modcontext->id));
                $testblocks[] = $bi->id;
            }
        }

        // Make sure all contexts were created properly
        $count = 1; //system
        $count += $DB->count_records('user', array('deleted'=>0));
        $count += $DB->count_records('course_categories');
        $count += $DB->count_records('course');
        $count += $DB->count_records('course_modules');
        $count += $DB->count_records('block_instances');
        $this->assertEquals($DB->count_records('context'), $count);
        $this->assertEquals($DB->count_records('context', array('depth'=>0)), 0);
        $this->assertEquals($DB->count_records('context', array('path'=>NULL)), 0);


        // ====== context_helper::get_level_name() ================================

        $levels = context_helper::get_all_levels();
        foreach ($levels as $level=>$classname) {
            $name = context_helper::get_level_name($level);
            $this->assertFalse(empty($name));
        }


        // ======= context::instance_by_id(), context_xxx::instance();

        $context = context::instance_by_id($frontpagecontext->id);
        $this->assertSame($context->contextlevel, CONTEXT_COURSE);
        $this->assertFalse(context::instance_by_id(-1, IGNORE_MISSING));
        try {
            context::instance_by_id(-1);
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        $this->assertTrue(context_system::instance() instanceof context_system);
        $this->assertTrue(context_coursecat::instance($testcategories[0]) instanceof context_coursecat);
        $this->assertTrue(context_course::instance($testcourses[0]) instanceof context_course);
        $this->assertTrue(context_module::instance($testpages[0]) instanceof context_module);
        $this->assertTrue(context_block::instance($testblocks[0]) instanceof context_block);

        $this->assertFalse(context_coursecat::instance(-1, IGNORE_MISSING));
        $this->assertFalse(context_course::instance(-1, IGNORE_MISSING));
        $this->assertFalse(context_module::instance(-1, IGNORE_MISSING));
        $this->assertFalse(context_block::instance(-1, IGNORE_MISSING));
        try {
            context_coursecat::instance(-1);
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            context_course::instance(-1);
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            context_module::instance(-1);
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            context_block::instance(-1);
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }


        // ======= $context->get_url(), $context->get_context_name(), $context->get_capabilities() =========

        $testcontexts = array();
        $testcontexts[CONTEXT_SYSTEM]    = context_system::instance();
        $testcontexts[CONTEXT_COURSECAT] = context_coursecat::instance($testcategories[0]);
        $testcontexts[CONTEXT_COURSE]    = context_course::instance($testcourses[0]);
        $testcontexts[CONTEXT_MODULE]    = context_module::instance($testpages[0]);
        $testcontexts[CONTEXT_BLOCK]     = context_block::instance($testblocks[0]);

        foreach ($testcontexts as $context) {
            $name = $context->get_context_name(true, true);
            $this->assertFalse(empty($name));

            $this->assertTrue($context->get_url() instanceof moodle_url);

            $caps = $context->get_capabilities();
            $this->assertTrue(is_array($caps));
            foreach ($caps as $cap) {
                $cap = (array)$cap;
                $this->assertSame(array_keys($cap), array('id', 'name', 'captype', 'contextlevel', 'component', 'riskbitmask'));
            }
        }
        unset($testcontexts);

        // ===== $context->get_course_context() =========================================

        $this->assertFalse($systemcontext->get_course_context(false));
        try {
            $systemcontext->get_course_context();
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        $context = context_coursecat::instance($testcategories[0]);
        $this->assertFalse($context->get_course_context(false));
        try {
            $context->get_course_context();
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        $this->assertSame($frontpagecontext->get_course_context(true), $frontpagecontext);
        $this->assertSame($frontpagepagecontext->get_course_context(true), $frontpagecontext);
        $this->assertSame($frontpagepageblockcontext->get_course_context(true), $frontpagecontext);


        // ======= $context->get_parent_context(), $context->get_parent_contexts(), $context->get_parent_context_ids() =======

        $userid = reset($testusers);
        $usercontext = context_user::instance($userid);
        $this->assertSame($usercontext->get_parent_context(), $systemcontext);
        $this->assertSame($usercontext->get_parent_contexts(), array($systemcontext->id=>$systemcontext));
        $this->assertSame($usercontext->get_parent_contexts(true), array($usercontext->id=>$usercontext, $systemcontext->id=>$systemcontext));

        $this->assertSame($systemcontext->get_parent_contexts(), array());
        $this->assertSame($systemcontext->get_parent_contexts(true), array($systemcontext->id=>$systemcontext));
        $this->assertSame($systemcontext->get_parent_context_ids(), array());
        $this->assertSame($systemcontext->get_parent_context_ids(true), array($systemcontext->id));

        $this->assertSame($frontpagecontext->get_parent_context(), $systemcontext);
        $this->assertSame($frontpagecontext->get_parent_contexts(), array($systemcontext->id=>$systemcontext));
        $this->assertSame($frontpagecontext->get_parent_contexts(true), array($frontpagecontext->id=>$frontpagecontext, $systemcontext->id=>$systemcontext));
        $this->assertSame($frontpagecontext->get_parent_context_ids(), array($systemcontext->id));
        $this->assertEquals($frontpagecontext->get_parent_context_ids(true), array($frontpagecontext->id, $systemcontext->id));

        $this->assertSame($systemcontext->get_parent_context(), false);
        $frontpagecontext = context_course::instance($SITE->id);
        $parent = $systemcontext;
        foreach ($testcategories as $catid) {
            $catcontext = context_coursecat::instance($catid);
            $this->assertSame($catcontext->get_parent_context(), $parent);
            $parent = $catcontext;
        }
        $this->assertSame($frontpagepagecontext->get_parent_context(), $frontpagecontext);
        $this->assertSame($frontpageblockcontext->get_parent_context(), $frontpagecontext);
        $this->assertSame($frontpagepageblockcontext->get_parent_context(), $frontpagepagecontext);


        // ====== $context->get_child_contexts() ================================

        $children = $systemcontext->get_child_contexts();
        $this->resetDebugging();
        $this->assertEquals(count($children)+1, $DB->count_records('context'));

        $context = context_coursecat::instance($testcategories[3]);
        $children = $context->get_child_contexts();
        $countcats    = 0;
        $countcourses = 0;
        $countblocks  = 0;
        foreach ($children as $child) {
            if ($child->contextlevel == CONTEXT_COURSECAT) {
                $countcats++;
            }
            if ($child->contextlevel == CONTEXT_COURSE) {
                $countcourses++;
            }
            if ($child->contextlevel == CONTEXT_BLOCK) {
                $countblocks++;
            }
        }
        $this->assertEquals(count($children), 8);
        $this->assertEquals($countcats, 1);
        $this->assertEquals($countcourses, 6);
        $this->assertEquals($countblocks, 1);

        $context = context_course::instance($testcourses[2]);
        $children = $context->get_child_contexts();
        $this->assertEquals(count($children), 7); // depends on number of default blocks

        $context = context_module::instance($testpages[3]);
        $children = $context->get_child_contexts();
        $this->assertEquals(count($children), 1);

        $context = context_block::instance($testblocks[1]);
        $children = $context->get_child_contexts();
        $this->assertEquals(count($children), 0);

        unset($children);
        unset($countcats);
        unset($countcourses);
        unset($countblocks);


        // ======= context_helper::reset_caches() ============================

        context_helper::reset_caches();
        $this->assertEquals(context_inspection::test_context_cache_size(), 0);
        context_course::instance($SITE->id);
        $this->assertEquals(context_inspection::test_context_cache_size(), 1);


        // ======= context preloading ========================================

        context_helper::reset_caches();
        $sql = "SELECT ".context_helper::get_preload_record_columns_sql('c')."
                  FROM {context} c
                 WHERE c.contextlevel <> ".CONTEXT_SYSTEM;
        $records = $DB->get_records_sql($sql);
        $firstrecord = reset($records);
        $columns = context_helper::get_preload_record_columns('c');
        $firstrecord = (array)$firstrecord;
        $this->assertSame(array_keys($firstrecord), array_values($columns));
        context_helper::reset_caches();
        foreach ($records as $record) {
            context_helper::preload_from_record($record);
            $this->assertEquals($record, new stdClass());
        }
        $this->assertEquals(context_inspection::test_context_cache_size(), count($records));
        unset($records);
        unset($columns);

        context_helper::reset_caches();
        context_helper::preload_course($SITE->id);
        $numfrontpagemodules = $DB->count_records('course_modules', array('course' => $SITE->id));
        $this->assertEquals(6 + $numfrontpagemodules, context_inspection::test_context_cache_size()); // depends on number of default blocks

        // ====== assign_capability(), unassign_capability() ====================

        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertFalse($rc);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $allroles['teacher'], $frontpagecontext->id);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertEquals($rc->permission, CAP_ALLOW);
        assign_capability('moodle/site:accessallgroups', CAP_PREVENT, $allroles['teacher'], $frontpagecontext->id);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertEquals($rc->permission, CAP_ALLOW);
        assign_capability('moodle/site:accessallgroups', CAP_PREVENT, $allroles['teacher'], $frontpagecontext, true);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertEquals($rc->permission, CAP_PREVENT);

        assign_capability('moodle/site:accessallgroups', CAP_INHERIT, $allroles['teacher'], $frontpagecontext);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertFalse($rc);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $allroles['teacher'], $frontpagecontext);
        unassign_capability('moodle/site:accessallgroups', $allroles['teacher'], $frontpagecontext, true);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertFalse($rc);
        unassign_capability('moodle/site:accessallgroups', $allroles['teacher'], $frontpagecontext->id, true);
        unset($rc);

        accesslib_clear_all_caches(false); // must be done after assign_capability()


        // ======= role_assign(), role_unassign(), role_unassign_all() ==============

        $context = context_course::instance($testcourses[1]);
        $this->assertEquals($DB->count_records('role_assignments', array('contextid'=>$context->id)), 0);
        role_assign($allroles['teacher'], $testusers[1], $context->id);
        role_assign($allroles['teacher'], $testusers[2], $context->id);
        role_assign($allroles['manager'], $testusers[1], $context->id);
        $this->assertEquals($DB->count_records('role_assignments', array('contextid'=>$context->id)), 3);
        role_unassign($allroles['teacher'], $testusers[1], $context->id);
        $this->assertEquals($DB->count_records('role_assignments', array('contextid'=>$context->id)), 2);
        role_unassign_all(array('contextid'=>$context->id));
        $this->assertEquals($DB->count_records('role_assignments', array('contextid'=>$context->id)), 0);
        unset($context);

        accesslib_clear_all_caches(false); // just in case


        // ====== has_capability(), get_users_by_capability(), role_switch(), reload_all_capabilities() and friends ========================

        $adminid = get_admin()->id;
        $guestid = $CFG->siteguest;

        // Enrol some users into some courses
        $course1 = $DB->get_record('course', array('id'=>$testcourses[22]), '*', MUST_EXIST);
        $course2 = $DB->get_record('course', array('id'=>$testcourses[7]), '*', MUST_EXIST);
        $cms = $DB->get_records('course_modules', array('course'=>$course1->id), 'id');
        $cm1 = reset($cms);
        $blocks = $DB->get_records('block_instances', array('parentcontextid'=>context_module::instance($cm1->id)->id), 'id');
        $block1 = reset($blocks);
        $instance1 = $DB->get_record('enrol', array('enrol'=>'manual', 'courseid'=>$course1->id));
        $instance2 = $DB->get_record('enrol', array('enrol'=>'manual', 'courseid'=>$course2->id));
        for($i=0; $i<9; $i++) {
            $manualenrol->enrol_user($instance1, $testusers[$i], $allroles['student']);
        }
        $manualenrol->enrol_user($instance1, $testusers[8], $allroles['teacher']);
        $manualenrol->enrol_user($instance1, $testusers[9], $allroles['editingteacher']);

        for($i=10; $i<15; $i++) {
            $manualenrol->enrol_user($instance2, $testusers[$i], $allroles['student']);
        }
        $manualenrol->enrol_user($instance2, $testusers[15], $allroles['editingteacher']);

        // Add tons of role assignments - the more the better
        role_assign($allroles['coursecreator'], $testusers[11], context_coursecat::instance($testcategories[2]));
        role_assign($allroles['manager'], $testusers[12], context_coursecat::instance($testcategories[1]));
        role_assign($allroles['student'], $testusers[9], context_module::instance($cm1->id));
        role_assign($allroles['teacher'], $testusers[8], context_module::instance($cm1->id));
        role_assign($allroles['guest'], $testusers[13], context_course::instance($course1->id));
        role_assign($allroles['teacher'], $testusers[7], context_block::instance($block1->id));
        role_assign($allroles['manager'], $testusers[9], context_block::instance($block1->id));
        role_assign($allroles['editingteacher'], $testusers[9], context_course::instance($course1->id));

        role_assign($allroles['teacher'], $adminid, context_course::instance($course1->id));
        role_assign($allroles['editingteacher'], $adminid, context_block::instance($block1->id));

        // Add tons of overrides - the more the better
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultuserroleid, $frontpageblockcontext, true);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultfrontpageroleid, $frontpageblockcontext, true);
        assign_capability('moodle/block:view', CAP_PROHIBIT, $allroles['guest'], $frontpageblockcontext, true);
        assign_capability('block/online_users:viewlist', CAP_PREVENT, $allroles['user'], $frontpageblockcontext, true);
        assign_capability('block/online_users:viewlist', CAP_PREVENT, $allroles['student'], $frontpageblockcontext, true);

        assign_capability('moodle/site:accessallgroups', CAP_PREVENT, $CFG->defaultuserroleid, $frontpagepagecontext, true);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultfrontpageroleid, $frontpagepagecontext, true);
        assign_capability('mod/page:view', CAP_PREVENT, $allroles['guest'], $frontpagepagecontext, true);
        assign_capability('mod/page:view', CAP_ALLOW, $allroles['user'], $frontpagepagecontext, true);
        assign_capability('moodle/page:view', CAP_ALLOW, $allroles['student'], $frontpagepagecontext, true);

        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultuserroleid, $frontpagecontext, true);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultfrontpageroleid, $frontpagecontext, true);
        assign_capability('mod/page:view', CAP_ALLOW, $allroles['guest'], $frontpagecontext, true);
        assign_capability('mod/page:view', CAP_PROHIBIT, $allroles['user'], $frontpagecontext, true);

        assign_capability('mod/page:view', CAP_PREVENT, $allroles['guest'], $systemcontext, true);

        accesslib_clear_all_caches(false); // must be done after assign_capability()

        // Extra tests for guests and not-logged-in users because they can not be verified by cross checking
        // with get_users_by_capability() where they are ignored
        $this->assertFalse(has_capability('moodle/block:view', $frontpageblockcontext, $guestid));
        $this->assertFalse(has_capability('mod/page:view', $frontpagepagecontext, $guestid));
        $this->assertTrue(has_capability('mod/page:view', $frontpagecontext, $guestid));
        $this->assertFalse(has_capability('mod/page:view', $systemcontext, $guestid));

        $this->assertFalse(has_capability('moodle/block:view', $frontpageblockcontext, 0));
        $this->assertFalse(has_capability('mod/page:view', $frontpagepagecontext, 0));
        $this->assertTrue(has_capability('mod/page:view', $frontpagecontext, 0));
        $this->assertFalse(has_capability('mod/page:view', $systemcontext, 0));

        $this->assertFalse(has_capability('moodle/course:create', $systemcontext, $testusers[11]));
        $this->assertTrue(has_capability('moodle/course:create', context_coursecat::instance($testcategories[2]), $testusers[11]));
        $this->assertFalse(has_capability('moodle/course:create', context_course::instance($testcourses[1]), $testusers[11]));
        $this->assertTrue(has_capability('moodle/course:create', context_course::instance($testcourses[19]), $testusers[11]));

        $this->assertFalse(has_capability('moodle/course:update', context_course::instance($testcourses[1]), $testusers[9]));
        $this->assertFalse(has_capability('moodle/course:update', context_course::instance($testcourses[19]), $testusers[9]));
        $this->assertFalse(has_capability('moodle/course:update', $systemcontext, $testusers[9]));

        // Test the list of enrolled users
        $coursecontext = context_course::instance($course1->id);
        $enrolled = get_enrolled_users($coursecontext);
        $this->assertEquals(count($enrolled), 10);
        for($i=0; $i<10; $i++) {
            $this->assertTrue(isset($enrolled[$testusers[$i]]));
        }
        $enrolled = get_enrolled_users($coursecontext, 'moodle/course:update');
        $this->assertEquals(count($enrolled), 1);
        $this->assertTrue(isset($enrolled[$testusers[9]]));
        unset($enrolled);

        // role switching
        $userid = $testusers[9];
        $USER = $DB->get_record('user', array('id'=>$userid));
        load_all_capabilities();
        $coursecontext = context_course::instance($course1->id);
        $this->assertTrue(has_capability('moodle/course:update', $coursecontext));
        $this->assertFalse(is_role_switched($course1->id));
        role_switch($allroles['student'], $coursecontext);
        $this->assertTrue(is_role_switched($course1->id));
        $this->assertEquals($USER->access['rsw'][$coursecontext->path],  $allroles['student']);
        $this->assertFalse(has_capability('moodle/course:update', $coursecontext));
        reload_all_capabilities();
        $this->assertFalse(has_capability('moodle/course:update', $coursecontext));
        role_switch(0, $coursecontext);
        $this->assertTrue(has_capability('moodle/course:update', $coursecontext));
        $userid = $adminid;
        $USER = $DB->get_record('user', array('id'=>$userid));
        load_all_capabilities();
        $coursecontext = context_course::instance($course1->id);
        $blockcontext = context_block::instance($block1->id);
        $this->assertTrue(has_capability('moodle/course:update', $blockcontext));
        role_switch($allroles['student'], $coursecontext);
        $this->assertEquals($USER->access['rsw'][$coursecontext->path],  $allroles['student']);
        $this->assertFalse(has_capability('moodle/course:update', $blockcontext));
        reload_all_capabilities();
        $this->assertFalse(has_capability('moodle/course:update', $blockcontext));
        load_all_capabilities();
        $this->assertTrue(has_capability('moodle/course:update', $blockcontext));

        // temp course role for enrol
        $DB->delete_records('cache_flags', array()); // this prevents problem with dirty contexts immediately resetting the temp role - this is a known problem...
        $userid = $testusers[5];
        $roleid = $allroles['editingteacher'];
        $USER = $DB->get_record('user', array('id'=>$userid));
        load_all_capabilities();
        $coursecontext = context_course::instance($course1->id);
        $this->assertFalse(has_capability('moodle/course:update', $coursecontext));
        $this->assertFalse(isset($USER->access['ra'][$coursecontext->path][$roleid]));
        load_temp_course_role($coursecontext, $roleid);
        $this->assertEquals($USER->access['ra'][$coursecontext->path][$roleid], $roleid);
        $this->assertTrue(has_capability('moodle/course:update', $coursecontext));
        remove_temp_course_roles($coursecontext);
        $this->assertFalse(has_capability('moodle/course:update', $coursecontext, $userid));
        load_temp_course_role($coursecontext, $roleid);
        reload_all_capabilities();
        $this->assertFalse(has_capability('moodle/course:update', $coursecontext, $userid));
        $USER = new stdClass();
        $USER->id = 0;

        // Now cross check has_capability() with get_users_by_capability(), each using different code paths,
        // they have to be kept in sync, usually only one of them breaks, so we know when something is wrong,
        // at the same time validate extra restrictions (guest read only no risks, admin exception, non existent and deleted users)
        $contexts = $DB->get_records('context', array(), 'id');
        $contexts = array_values($contexts);
        $capabilities = $DB->get_records('capabilities', array(), 'id');
        $capabilities = array_values($capabilities);
        $roles = array($allroles['guest'], $allroles['user'], $allroles['teacher'], $allroles['editingteacher'], $allroles['coursecreator'], $allroles['manager']);
        $userids = array_values($testusers);
        $userids[] = get_admin()->id;

        if (!PHPUNIT_LONGTEST) {
            $contexts = array_slice($contexts, 0, 10);
            $capabilities = array_slice($capabilities, 0, 5);
            $userids = array_slice($userids, 0, 5);
        }

        // Random time!
        //srand(666);
        foreach($userids as $userid) { // no guest or deleted
            // each user gets 0-10 random roles
            $rcount = rand(0, 10);
            for($j=0; $j<$rcount; $j++) {
                $roleid = $roles[rand(0, count($roles)-1)];
                $contextid = $contexts[rand(0, count($contexts)-1)]->id;
                role_assign($roleid, $userid, $contextid);
            }
        }

        $permissions = array(CAP_ALLOW, CAP_PREVENT, CAP_INHERIT, CAP_PREVENT);
        $maxoverrides = count($contexts)*10;
        for($j=0; $j<$maxoverrides; $j++) {
            $roleid = $roles[rand(0, count($roles)-1)];
            $contextid = $contexts[rand(0, count($contexts)-1)]->id;
            $permission = $permissions[rand(0,count($permissions)-1)];
            $capname = $capabilities[rand(0, count($capabilities)-1)]->name;
            assign_capability($capname, $permission, $roleid, $contextid, true);
        }
        unset($permissions);
        unset($roles);

        accesslib_clear_all_caches(false); // must be done after assign_capability()

        // Test time - let's set up some real user, just in case the logic for USER affects the others...
        $USER = $DB->get_record('user', array('id'=>$testusers[3]));
        load_all_capabilities();

        $userids[] = $CFG->siteguest;
        $userids[] = 0; // not-logged-in user
        $userids[] = -1; // non-existent user

        foreach ($contexts as $crecord) {
            $context = context::instance_by_id($crecord->id);
            if ($coursecontext = $context->get_course_context(false)) {
                $enrolled = get_enrolled_users($context);
            } else {
                $enrolled = array();
            }
            foreach ($capabilities as $cap) {
                $allowed = get_users_by_capability($context, $cap->name, 'u.id, u.username');
                if ($enrolled) {
                    $enrolledwithcap = get_enrolled_users($context, $cap->name);
                } else {
                    $enrolledwithcap = array();
                }
                foreach ($userids as $userid) {
                    if ($userid == 0 or isguestuser($userid)) {
                        if ($userid == 0) {
                            $CFG->forcelogin = true;
                            $this->assertFalse(has_capability($cap->name, $context, $userid));
                            unset($CFG->forcelogin);
                        }
                        if (($cap->captype === 'write') or ($cap->riskbitmask & (RISK_XSS | RISK_CONFIG | RISK_DATALOSS))) {
                            $this->assertFalse(has_capability($cap->name, $context, $userid));
                        }
                        $this->assertFalse(isset($allowed[$userid]));
                    } else {
                        if (is_siteadmin($userid)) {
                            $this->assertTrue(has_capability($cap->name, $context, $userid, true));
                        }
                        $hascap = has_capability($cap->name, $context, $userid, false);
                        $this->assertSame($hascap, isset($allowed[$userid]), "Capability result mismatch user:$userid, context:$context->id, $cap->name, hascap: ".(int)$hascap." ");
                        if (isset($enrolled[$userid])) {
                            $this->assertSame(isset($allowed[$userid]), isset($enrolledwithcap[$userid]), "Enrolment with capability result mismatch user:$userid, context:$context->id, $cap->name, hascap: ".(int)$hascap." ");
                        }
                    }
                }
            }
        }
        // Back to nobody
        $USER = new stdClass();
        $USER->id = 0;
        unset($contexts);
        unset($userids);
        unset($capabilities);

        // Now let's do all the remaining tests that break our carefully prepared fake site



        // ======= $context->mark_dirty() =======================================

        $DB->delete_records('cache_flags', array());
        accesslib_clear_all_caches(false);
        $systemcontext->mark_dirty();
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertTrue(isset($dirty[$systemcontext->path]));
        $this->assertTrue(isset($ACCESSLIB_PRIVATE->dirtycontexts[$systemcontext->path]));


        // ======= $context->reload_if_dirty(); =================================

        $DB->delete_records('cache_flags', array());
        accesslib_clear_all_caches(false);
        load_all_capabilities();
        $context = context_course::instance($testcourses[2]);
        $page = $DB->get_record('page', array('course'=>$testcourses[2]));
        $pagecm = get_coursemodule_from_instance('page', $page->id);
        $pagecontext = context_module::instance($pagecm->id);

        $context->mark_dirty();
        $this->assertTrue(isset($ACCESSLIB_PRIVATE->dirtycontexts[$context->path]));
        $USER->access['test'] = true;
        $context->reload_if_dirty();
        $this->assertFalse(isset($USER->access['test']));

        $context->mark_dirty();
        $this->assertTrue(isset($ACCESSLIB_PRIVATE->dirtycontexts[$context->path]));
        $USER->access['test'] = true;
        $pagecontext->reload_if_dirty();
        $this->assertFalse(isset($USER->access['test']));


        // ======= context_helper::build_all_paths() ============================

        $oldcontexts = $DB->get_records('context', array(), 'id');
        $DB->set_field_select('context', 'path', NULL, "contextlevel <> ".CONTEXT_SYSTEM);
        $DB->set_field_select('context', 'depth', 0, "contextlevel <> ".CONTEXT_SYSTEM);
        context_helper::build_all_paths();
        $newcontexts = $DB->get_records('context', array(), 'id');
        $this->assertEquals($oldcontexts, $newcontexts);
        unset($oldcontexts);
        unset($newcontexts);


        // ======= $context->reset_paths() ======================================

        $context = context_course::instance($testcourses[2]);
        $children = $context->get_child_contexts();
        $context->reset_paths(false);
        $this->assertSame($DB->get_field('context', 'path', array('id'=>$context->id)), NULL);
        $this->assertEquals($DB->get_field('context', 'depth', array('id'=>$context->id)), 0);
        foreach ($children as $child) {
            $this->assertSame($DB->get_field('context', 'path', array('id'=>$child->id)), NULL);
            $this->assertEquals($DB->get_field('context', 'depth', array('id'=>$child->id)), 0);
        }
        $this->assertEquals(count($children)+1, $DB->count_records('context', array('depth'=>0)));
        $this->assertEquals(count($children)+1, $DB->count_records('context', array('path'=>NULL)));

        $context = context_course::instance($testcourses[2]);
        $context->reset_paths(true);
        $context = context_course::instance($testcourses[2]);
        $this->assertEquals($DB->get_field('context', 'path', array('id'=>$context->id)), $context->path);
        $this->assertEquals($DB->get_field('context', 'depth', array('id'=>$context->id)), $context->depth);
        $this->assertEquals(0, $DB->count_records('context', array('depth'=>0)));
        $this->assertEquals(0, $DB->count_records('context', array('path'=>NULL)));


        // ====== $context->update_moved(); ======================================

        accesslib_clear_all_caches(false);
        $DB->delete_records('cache_flags', array());
        $course = $DB->get_record('course', array('id'=>$testcourses[0]));
        $context = context_course::instance($course->id);
        $oldpath = $context->path;
        $miscid = $DB->get_field_sql("SELECT MIN(id) FROM {course_categories}");
        $categorycontext = context_coursecat::instance($miscid);
        $course->category = $miscid;
        $DB->update_record('course', $course);
        $context->update_moved($categorycontext);

        $context = context_course::instance($course->id);
        $this->assertEquals($context->get_parent_context(), $categorycontext);
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertTrue(isset($dirty[$oldpath]));
        $this->assertTrue(isset($dirty[$context->path]));


        // ====== $context->delete_content() =====================================

        context_helper::reset_caches();
        $context = context_module::instance($testpages[3]);
        $this->assertTrue($DB->record_exists('context', array('id'=>$context->id)));
        $this->assertEquals(1, $DB->count_records('block_instances', array('parentcontextid'=>$context->id)));
        $context->delete_content();
        $this->assertTrue($DB->record_exists('context', array('id'=>$context->id)));
        $this->assertEquals(0, $DB->count_records('block_instances', array('parentcontextid'=>$context->id)));


        // ====== $context->delete() =============================

        context_helper::reset_caches();
        $context = context_module::instance($testpages[4]);
        $this->assertTrue($DB->record_exists('context', array('id'=>$context->id)));
        $this->assertEquals(1, $DB->count_records('block_instances', array('parentcontextid'=>$context->id)));
        $bi = $DB->get_record('block_instances', array('parentcontextid'=>$context->id));
        $bicontext = context_block::instance($bi->id);
        $DB->delete_records('cache_flags', array());
        $context->delete(); // should delete also linked blocks
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertTrue(isset($dirty[$context->path]));
        $this->assertFalse($DB->record_exists('context', array('id'=>$context->id)));
        $this->assertFalse($DB->record_exists('context', array('id'=>$bicontext->id)));
        $this->assertFalse($DB->record_exists('context', array('contextlevel'=>CONTEXT_MODULE, 'instanceid'=>$testpages[4])));
        $this->assertFalse($DB->record_exists('context', array('contextlevel'=>CONTEXT_BLOCK, 'instanceid'=>$bi->id)));
        $this->assertEquals(0, $DB->count_records('block_instances', array('parentcontextid'=>$context->id)));
        context_module::instance($testpages[4]);


        // ====== context_helper::delete_instance() =============================

        context_helper::reset_caches();
        $lastcourse = array_pop($testcourses);
        $this->assertTrue($DB->record_exists('context', array('contextlevel'=>CONTEXT_COURSE, 'instanceid'=>$lastcourse)));
        $coursecontext = context_course::instance($lastcourse);
        $this->assertEquals(context_inspection::test_context_cache_size(), 1);
        $this->assertFalse($coursecontext->instanceid == CONTEXT_COURSE);
        $DB->delete_records('cache_flags', array());
        context_helper::delete_instance(CONTEXT_COURSE, $lastcourse);
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertTrue(isset($dirty[$coursecontext->path]));
        $this->assertEquals(context_inspection::test_context_cache_size(), 0);
        $this->assertFalse($DB->record_exists('context', array('contextlevel'=>CONTEXT_COURSE, 'instanceid'=>$lastcourse)));
        context_course::instance($lastcourse);


        // ======= context_helper::create_instances() ==========================

        $prevcount = $DB->count_records('context');
        $DB->delete_records('context', array('contextlevel'=>CONTEXT_BLOCK));
        context_helper::create_instances(null, true);
        $this->assertSame($DB->count_records('context'), $prevcount);
        $this->assertEquals($DB->count_records('context', array('depth'=>0)), 0);
        $this->assertEquals($DB->count_records('context', array('path'=>NULL)), 0);

        $DB->delete_records('context', array('contextlevel'=>CONTEXT_BLOCK));
        $DB->delete_records('block_instances', array());
        $prevcount = $DB->count_records('context');
        $DB->delete_records_select('context', 'contextlevel <> '.CONTEXT_SYSTEM);
        context_helper::create_instances(null, true);
        $this->assertSame($DB->count_records('context'), $prevcount);
        $this->assertEquals($DB->count_records('context', array('depth'=>0)), 0);
        $this->assertEquals($DB->count_records('context', array('path'=>NULL)), 0);


        // ======= context_helper::cleanup_instances() ==========================

        $lastcourse = $DB->get_field_sql("SELECT MAX(id) FROM {course}");
        $DB->delete_records('course', array('id'=>$lastcourse));
        $lastcategory = $DB->get_field_sql("SELECT MAX(id) FROM {course_categories}");
        $DB->delete_records('course_categories', array('id'=>$lastcategory));
        $lastuser = $DB->get_field_sql("SELECT MAX(id) FROM {user} WHERE deleted=0");
        $DB->delete_records('user', array('id'=>$lastuser));
        $DB->delete_records('block_instances', array('parentcontextid'=>$frontpagepagecontext->id));
        $DB->delete_records('course_modules', array('id'=>$frontpagepagecontext->instanceid));
        context_helper::cleanup_instances();
        $count = 1; //system
        $count += $DB->count_records('user', array('deleted'=>0));
        $count += $DB->count_records('course_categories');
        $count += $DB->count_records('course');
        $count += $DB->count_records('course_modules');
        $count += $DB->count_records('block_instances');
        $this->assertEquals($DB->count_records('context'), $count);


        // ======= context cache size restrictions ==============================

        $testusers= array();
        for ($i=0; $i<CONTEXT_CACHE_MAX_SIZE + 100; $i++) {
            $user = $generator->create_user();
            $testusers[$i] = $user->id;
        }
        context_helper::create_instances(null, true);
        context_helper::reset_caches();
        for ($i=0; $i<CONTEXT_CACHE_MAX_SIZE + 100; $i++) {
            context_user::instance($testusers[$i]);
            if ($i == CONTEXT_CACHE_MAX_SIZE - 1) {
                $this->assertEquals(context_inspection::test_context_cache_size(), CONTEXT_CACHE_MAX_SIZE);
            } else if ($i == CONTEXT_CACHE_MAX_SIZE) {
                // once the limit is reached roughly 1/3 of records should be removed from cache
                $this->assertEquals(context_inspection::test_context_cache_size(), (int)(CONTEXT_CACHE_MAX_SIZE * (2/3) +102));
            }
        }
        // We keep the first 100 cached
        $prevsize = context_inspection::test_context_cache_size();
        for ($i=0; $i<100; $i++) {
            context_user::instance($testusers[$i]);
            $this->assertEquals(context_inspection::test_context_cache_size(), $prevsize);
        }
        context_user::instance($testusers[102]);
        $this->assertEquals(context_inspection::test_context_cache_size(), $prevsize+1);
        unset($testusers);



        // =================================================================
        // ======= basic test of legacy functions ==========================
        // =================================================================
        // note: watch out, the fake site might be pretty borked already

        $this->assertSame(get_system_context(), context_system::instance());

        foreach ($DB->get_records('context') as $contextid=>$record) {
            $context = context::instance_by_id($contextid);
            $this->assertSame(context::instance_by_id($contextid, IGNORE_MISSING), $context);
            $this->assertSame(get_context_instance($record->contextlevel, $record->instanceid), $context);
            $this->assertSame(get_parent_contexts($context), $context->get_parent_context_ids());
            if ($context->id == SYSCONTEXTID) {
                $this->assertSame(get_parent_contextid($context), false);
            } else {
                $this->assertSame(get_parent_contextid($context), $context->get_parent_context()->id);
            }
        }

        $children = get_child_contexts($systemcontext);
        $this->resetDebugging();
        $this->assertEquals(count($children), $DB->count_records('context')-1);
        unset($children);

        $DB->delete_records('context', array('contextlevel'=>CONTEXT_BLOCK));
        create_contexts();
        $this->assertFalse($DB->record_exists('context', array('contextlevel'=>CONTEXT_BLOCK)));

        $DB->set_field('context', 'depth', 0, array('contextlevel'=>CONTEXT_BLOCK));
        build_context_path();
        $this->assertFalse($DB->record_exists('context', array('depth'=>0)));

        $lastcourse = $DB->get_field_sql("SELECT MAX(id) FROM {course}");
        $DB->delete_records('course', array('id'=>$lastcourse));
        $lastcategory = $DB->get_field_sql("SELECT MAX(id) FROM {course_categories}");
        $DB->delete_records('course_categories', array('id'=>$lastcategory));
        $lastuser = $DB->get_field_sql("SELECT MAX(id) FROM {user} WHERE deleted=0");
        $DB->delete_records('user', array('id'=>$lastuser));
        $DB->delete_records('block_instances', array('parentcontextid'=>$frontpagepagecontext->id));
        $DB->delete_records('course_modules', array('id'=>$frontpagepagecontext->instanceid));
        cleanup_contexts();
        $count = 1; //system
        $count += $DB->count_records('user', array('deleted'=>0));
        $count += $DB->count_records('course_categories');
        $count += $DB->count_records('course');
        $count += $DB->count_records('course_modules');
        $count += $DB->count_records('block_instances');
        $this->assertEquals($DB->count_records('context'), $count);

        context_helper::reset_caches();
        preload_course_contexts($SITE->id);
        $this->assertEquals(1 + $DB->count_records('course_modules', array('course' => $SITE->id)),
                context_inspection::test_context_cache_size());

        context_helper::reset_caches();
        list($select, $join) = context_instance_preload_sql('c.id', CONTEXT_COURSECAT, 'ctx');
        $sql = "SELECT c.id $select FROM {course_categories} c $join";
        $records = $DB->get_records_sql($sql);
        foreach ($records as $record) {
            context_instance_preload($record);
            $record = (array)$record;
            $this->assertEquals(1, count($record)); // only id left
        }
        $this->assertEquals(count($records), context_inspection::test_context_cache_size());

        accesslib_clear_all_caches(true);
        $DB->delete_records('cache_flags', array());
        mark_context_dirty($systemcontext->path);
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertTrue(isset($dirty[$systemcontext->path]));

        accesslib_clear_all_caches(false);
        $DB->delete_records('cache_flags', array());
        $course = $DB->get_record('course', array('id'=>$testcourses[2]));
        $context = context_course::instance($course->id);
        $oldpath = $context->path;
        $miscid = $DB->get_field_sql("SELECT MIN(id) FROM {course_categories}");
        $categorycontext = context_coursecat::instance($miscid);
        $course->category = $miscid;
        $DB->update_record('course', $course);
        context_moved($context, $categorycontext);
        $context = context_course::instance($course->id);
        $this->assertEquals($context->get_parent_context(), $categorycontext);

        $this->assertTrue($DB->record_exists('context', array('contextlevel'=>CONTEXT_COURSE, 'instanceid'=>$testcourses[2])));
        delete_context(CONTEXT_COURSE, $testcourses[2]);
        $this->assertFalse($DB->record_exists('context', array('contextlevel'=>CONTEXT_COURSE, 'instanceid'=>$testcourses[2])));

        $name = get_contextlevel_name(CONTEXT_COURSE);
        $this->assertFalse(empty($name));

        $context = context_course::instance($testcourses[2]);
        $name = print_context_name($context);
        $this->assertFalse(empty($name));

        $url = get_context_url($coursecontext);
        $this->assertFalse($url instanceof modole_url);

        $pagecm = get_coursemodule_from_instance('page', $testpages[7]);
        $context = context_module::instance($pagecm->id);
        $coursecontext = get_course_context($context);
        $this->assertEquals($coursecontext->contextlevel, CONTEXT_COURSE);
        $this->assertEquals(get_courseid_from_context($context), $pagecm->course);

        $caps = fetch_context_capabilities($systemcontext);
        $this->assertTrue(is_array($caps));
        unset($caps);
    }

    /**
     * Test updating of role capabilities during upgrade
     * @return void
     */
    public function test_update_capabilities() {
        global $DB, $SITE;

        $this->resetAfterTest(true);

        $froncontext = context_course::instance($SITE->id);
        $student = $DB->get_record('role', array('archetype'=>'student'));
        $teacher = $DB->get_record('role', array('archetype'=>'teacher'));

        $existingcaps = $DB->get_records('capabilities', array(), 'id', 'name, captype, contextlevel, component, riskbitmask');

        $this->assertFalse(isset($existingcaps['moodle/site:restore']));         // moved to new 'moodle/restore:restorecourse'
        $this->assertTrue(isset($existingcaps['moodle/restore:restorecourse'])); // new cap from 'moodle/site:restore'
        $this->assertTrue(isset($existingcaps['moodle/site:sendmessage']));      // new capability
        $this->assertTrue(isset($existingcaps['moodle/backup:backupcourse']));
        $this->assertTrue(isset($existingcaps['moodle/backup:backupsection']));  // cloned from 'moodle/backup:backupcourse'
        $this->assertTrue(isset($existingcaps['moodle/site:approvecourse']));    // updated bitmask
        $this->assertTrue(isset($existingcaps['moodle/course:manageactivities']));
        $this->assertTrue(isset($existingcaps['mod/page:addinstance']));         // cloned from core 'moodle/course:manageactivities'

        // fake state before upgrade
        $DB->set_field('capabilities', 'name', 'moodle/site:restore', array('name'=>'moodle/restore:restorecourse'));
        $DB->set_field('role_capabilities', 'capability', 'moodle/site:restore', array('capability'=>'moodle/restore:restorecourse'));
        assign_capability('moodle/site:restore', CAP_PROHIBIT, $teacher->id, $froncontext->id, true);
        $perms1 = array_values($DB->get_records('role_capabilities', array('capability'=>'moodle/site:restore', 'roleid'=>$teacher->id), 'contextid, permission', 'contextid, permission'));

        $DB->delete_records('role_capabilities', array('capability'=>'moodle/site:sendmessage'));
        $DB->delete_records('capabilities', array('name'=>'moodle/site:sendmessage'));

        $DB->delete_records('role_capabilities', array('capability'=>'moodle/backup:backupsection'));
        $DB->delete_records('capabilities', array('name'=>'moodle/backup:backupsection'));
        assign_capability('moodle/backup:backupcourse', CAP_PROHIBIT, $student->id, $froncontext->id, true);
        assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $teacher->id, $froncontext->id, true);

        $DB->set_field('capabilities', 'riskbitmask', 0, array('name'=>'moodle/site:approvecourse'));

        $DB->delete_records('role_capabilities', array('capability'=>'mod/page:addinstance'));
        $DB->delete_records('capabilities', array('name'=>'mod/page:addinstance'));
        assign_capability('moodle/course:manageactivities', CAP_PROHIBIT, $student->id, $froncontext->id, true);
        assign_capability('moodle/course:manageactivities', CAP_ALLOW, $teacher->id, $froncontext->id, true);

        // execute core
        update_capabilities('moodle');

        // only core should be upgraded
        $caps = $DB->get_records('capabilities', array(), 'id', 'name, captype, contextlevel, component, riskbitmask');

        $this->assertFalse(isset($existingcaps['moodle/site:restore']));
        $this->assertTrue(isset($caps['moodle/restore:restorecourse']));
        $this->assertEquals($existingcaps['moodle/restore:restorecourse'], $caps['moodle/restore:restorecourse']);
        $perms2 = array_values($DB->get_records('role_capabilities', array('capability'=>'moodle/restore:restorecourse', 'roleid'=>$teacher->id), 'contextid, permission', 'contextid, permission'));
        $this->assertEquals($perms1, $perms2);

        $this->assertTrue(isset($caps['moodle/site:sendmessage']));
        $this->assertEquals($existingcaps['moodle/site:sendmessage'], $caps['moodle/site:sendmessage']);

        $this->assertTrue(isset($caps['moodle/backup:backupsection']));
        $this->assertEquals($existingcaps['moodle/backup:backupsection'], $caps['moodle/backup:backupsection']);
        $roles = $DB->get_records_sql('SELECT DISTINCT roleid AS id FROM {role_capabilities} WHERE capability=? OR capability=?', array('moodle/backup:backupcourse', 'moodle/backup:backupsection'));
        foreach ($roles as $role) {
            $perms1 = array_values($DB->get_records('role_capabilities', array('capability'=>'moodle/backup:backupcourse', 'roleid'=>$role->id), 'contextid, permission', 'contextid, permission'));
            $perms2 = array_values($DB->get_records('role_capabilities', array('capability'=>'moodle/backup:backupsection', 'roleid'=>$role->id), 'contextid, permission', 'contextid, permission'));
            $this->assertEquals($perms1, $perms2);
        }

        $this->assertTrue(isset($caps['moodle/site:approvecourse']));
        $this->assertEquals($existingcaps['moodle/site:approvecourse'], $caps['moodle/site:approvecourse']);

        $this->assertFalse(isset($caps['mod/page:addinstance']));

        // execute plugin
        update_capabilities('mod_page');
        $caps = $DB->get_records('capabilities', array(), 'id', 'name, captype, contextlevel, component, riskbitmask');
        $this->assertTrue(isset($caps['mod/page:addinstance']));
        $roles = $DB->get_records_sql('SELECT DISTINCT roleid AS id FROM {role_capabilities} WHERE capability=? OR capability=?', array('moodle/course:manageactivities', 'mod/page:addinstance'));
        foreach ($roles as $role) {
            $perms1 = array_values($DB->get_records('role_capabilities', array('capability'=>'moodle/course:manageactivities', 'roleid'=>$role->id), 'contextid, permission', 'contextid, permission'));
            $perms2 = array_values($DB->get_records('role_capabilities', array('capability'=>'mod/page:addinstance', 'roleid'=>$role->id), 'contextid, permission', 'contextid, permission'));
        }
        $this->assertEquals($perms1, $perms2);
    }
}

