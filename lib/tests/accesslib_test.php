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
 * Full functional accesslib test.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Functional test for accesslib.php
 *
 * Note: execution may take many minutes especially on slower servers.
 */
class accesslib_test extends advanced_testcase {
    /**
     * Verify comparison of context instances in phpunit asserts.
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
     *
     * @covers ::accesslib_clear_all_caches_for_unit_testing
     */
    public function test_accesslib_clear_all_caches() {
        global $ACCESSLIB_PRIVATE;

        $this->resetAfterTest();

        $this->setAdminUser();
        load_all_capabilities();

        $this->assertNotEmpty($ACCESSLIB_PRIVATE->accessdatabyuser);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assertEmpty($ACCESSLIB_PRIVATE->dirtycontexts);
        $this->assertEmpty($ACCESSLIB_PRIVATE->accessdatabyuser);
    }

    /**
     * Check modifying capability record is not exposed to other code.
     */
    public function test_capabilities_mutation() {
        $oldcap = get_capability_info('moodle/site:config');
        $cap = get_capability_info('moodle/site:config');
        unset($cap->name);
        $newcap = get_capability_info('moodle/site:config');

        $this->assertFalse(isset($cap->name));
        $this->assertTrue(isset($newcap->name));
        $this->assertTrue(isset($oldcap->name));
    }

    /**
     * Test getting of role access
     *
     * @covers ::get_role_access
     */
    public function test_get_role_access() {
        global $DB;

        $roles = $DB->get_records('role');
        foreach ($roles as $role) {
            $access = get_role_access($role->id);

            $this->assertTrue(is_array($access));
            $this->assertTrue(is_array($access['ra']));
            $this->assertFalse(isset($access['rdef']));
            $this->assertFalse(isset($access['rdef_count']));
            $this->assertFalse(isset($access['loaded']));
            $this->assertTrue(isset($access['time']));
            $this->assertTrue(is_array($access['rsw']));
        }

        // Note: the data is validated in the functional permission evaluation test at the end of this testcase.
    }

    /**
     * Test getting of guest role.
     *
     * @covers ::get_guest_role
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
     *
     * @covers ::is_siteadmin
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
     *
     * @covers ::is_enrolled
     */
    public function test_is_enrolled() {
        global $DB;

        $this->resetAfterTest();

        // Generate data.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $role = $DB->get_record('role', array('shortname'=>'student'));

        // There should be a manual enrolment as part of the default install.
        $plugin = enrol_get_plugin('manual');
        $instance = $DB->get_record('enrol', array(
            'courseid' => $course->id,
            'enrol' => 'manual',
        ));
        $this->assertNotSame(false, $instance);

        // Enrol the user in the course.
        $plugin->enrol_user($instance, $user->id, $role->id);

        // We'll test with the mod/assign:submit capability.
        $capability= 'mod/assign:submit';
        $this->assertTrue($DB->record_exists('capabilities', array('name' => $capability)));

        // Switch to our user.
        $this->setUser($user);

        // Ensure that the user has the capability first.
        $this->assertTrue(has_capability($capability, $coursecontext, $user->id));

        // We first test whether the user is enrolled on the course as this
        // seeds the cache, then we test for the capability.
        $this->assertTrue(is_enrolled($coursecontext, $user, '', true));
        $this->assertTrue(is_enrolled($coursecontext, $user, $capability));

        // Prevent the capability for this user role.
        assign_capability($capability, CAP_PROHIBIT, $role->id, $coursecontext);
        $this->assertFalse(has_capability($capability, $coursecontext, $user->id));

        // Again, we seed the cache first by checking initial enrolment,
        // and then we test the actual capability.
        $this->assertTrue(is_enrolled($coursecontext, $user, '', true));
        $this->assertFalse(is_enrolled($coursecontext, $user, $capability));
    }

    /**
     * Test logged in test.
     *
     * @covers ::isloggedin
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
     *
     * @covers ::isguestuser
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
     *
     * @covers ::is_safe_capability
     */
    public function test_is_safe_capability() {
        global $DB;
        // Note: there is not much to test, just make sure no notices are throw for the most dangerous cap.
        $capability = $DB->get_record('capabilities', array('name'=>'moodle/site:config'), '*', MUST_EXIST);
        $this->assertFalse(is_safe_capability($capability));
    }

    /**
     * Test context fetching.
     *
     * @covers ::get_context_info_array
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
        $this->assertEquals($syscontext, $result[0]);
        $this->assertNull($result[1]);
        $this->assertNull($result[2]);

        $result = get_context_info_array($usercontext->id);
        $this->assertCount(3, $result);
        $this->assertEquals($usercontext, $result[0]);
        $this->assertNull($result[1]);
        $this->assertNull($result[2]);

        $result = get_context_info_array($catcontext->id);
        $this->assertCount(3, $result);
        $this->assertEquals($catcontext, $result[0]);
        $this->assertNull($result[1]);
        $this->assertNull($result[2]);

        $result = get_context_info_array($coursecontext->id);
        $this->assertCount(3, $result);
        $this->assertEquals($coursecontext, $result[0]);
        $this->assertEquals($course->id, $result[1]->id);
        $this->assertSame($course->shortname, $result[1]->shortname);
        $this->assertNull($result[2]);

        $result = get_context_info_array($block1context->id);
        $this->assertCount(3, $result);
        $this->assertEquals($block1context, $result[0]);
        $this->assertEquals($course->id, $result[1]->id);
        $this->assertEquals($course->shortname, $result[1]->shortname);
        $this->assertNull($result[2]);

        $result = get_context_info_array($modcontext->id);
        $this->assertCount(3, $result);
        $this->assertEquals($modcontext, $result[0]);
        $this->assertEquals($course->id, $result[1]->id);
        $this->assertSame($course->shortname, $result[1]->shortname);
        $this->assertEquals($cm->id, $result[2]->id);

        $result = get_context_info_array($block2context->id);
        $this->assertCount(3, $result);
        $this->assertEquals($block2context, $result[0]);
        $this->assertEquals($course->id, $result[1]->id);
        $this->assertSame($course->shortname, $result[1]->shortname);
        $this->assertEquals($cm->id, $result[2]->id);
    }

    /**
     * Test looking for course contacts.
     *
     * @covers ::has_coursecontact_role
     */
    public function test_has_coursecontact_role() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $users = $DB->get_records('user');

        // Nobody is expected to have any course level roles.
        $this->assertNotEmpty($CFG->coursecontact);
        foreach ($users as $user) {
            $this->assertFalse(has_coursecontact_role($user->id));
        }

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $contactroles = preg_split('/,/', $CFG->coursecontact);
        $roleid = reset($contactroles);
        role_assign($roleid, $user->id, context_course::instance($course->id));
        $this->assertTrue(has_coursecontact_role($user->id));
    }

    /**
     * Test creation of roles.
     *
     * @covers ::create_role
     */
    public function test_create_role() {
        global $DB;

        $this->resetAfterTest();

        $id = create_role('New student role', 'student2', 'New student description', 'student');
        $role = $DB->get_record('role', array('id'=>$id));

        $this->assertNotEmpty($role);
        $this->assertSame('New student role', $role->name);
        $this->assertSame('student2', $role->shortname);
        $this->assertSame('New student description', $role->description);
        $this->assertSame('student', $role->archetype);
    }

    /**
     * Test adding of capabilities to roles.
     *
     * @covers ::assign_capability
     */
    public function test_assign_capability() {
        global $DB, $USER;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $syscontext = context_system::instance();
        $frontcontext = context_course::instance(SITEID);
        $student = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $this->assertTrue($DB->record_exists('capabilities', array('name'=>'moodle/backup:backupcourse'))); // Any capability assigned to student by default.
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
        $this->assertEquals($user->id, $permission->modifierid);

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

        // Test event triggered.
        $sink = $this->redirectEvents();
        $capability = 'moodle/backup:backupcourse';
        assign_capability($capability, CAP_ALLOW, $student->id, $syscontext);
        $events = $sink->get_events();
        $sink->close();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf('\core\event\capability_assigned', $event);
        $this->assertSame('role_capabilities', $event->objecttable);
        $this->assertEquals($student->id, $event->objectid);
        $this->assertEquals($syscontext->id, $event->contextid);
        $other = ['capability' => $capability, 'oldpermission' => CAP_INHERIT, 'permission' => CAP_ALLOW];
        $this->assertEquals($other, $event->other);
        $description = "The user id '$USER->id' assigned the '$capability' capability for " .
            "role '$student->id' with 'Allow' permission";
        $this->assertEquals($description, $event->get_description());

        // Test if the event has different description when updating the capability permission.
        $sink = $this->redirectEvents();
        assign_capability($capability, CAP_PROHIBIT, $student->id, $syscontext, true);
        $events = $sink->get_events();
        $sink->close();
        $event = $events[0];
        $description = "The user id '$USER->id' changed the '$capability' capability permission for " .
            "role '$student->id' from 'Allow' to 'Prohibit'";
        $this->assertEquals($description, $event->get_description());
    }

    /**
     * Test removing of capabilities from roles.
     *
     * @covers ::unassign_capability
     */
    public function test_unassign_capability() {
        global $DB, $USER;

        $this->resetAfterTest();

        $syscontext = context_system::instance();
        $frontcontext = context_course::instance(SITEID);
        $manager = $DB->get_record('role', array('shortname'=>'manager'), '*', MUST_EXIST);
        $this->assertTrue($DB->record_exists('capabilities', array('name'=>'moodle/backup:backupcourse'))); // Any capability assigned to manager by default.
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

        // Test event triggered.
        $sink = $this->redirectEvents();
        $capability = 'moodle/backup:backupcourse';
        unassign_capability($capability, CAP_ALLOW, $manager->id);
        $events = $sink->get_events();
        $sink->close();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf('\core\event\capability_unassigned', $event);
        $this->assertSame('role_capabilities', $event->objecttable);
        $this->assertEquals($manager->id, $event->objectid);
        $this->assertEquals($syscontext->id, $event->contextid);
        $this->assertEquals($capability, $event->other['capability']);
        $description = "The user id id '$USER->id' has unassigned the '$capability' capability for role '$manager->id'";
        $this->assertEquals($description, $event->get_description());
    }

    /**
     * Test role assigning.
     *
     * @covers ::role_assign
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

        // Test event triggered.

        $user2 = $this->getDataGenerator()->create_user();
        $sink = $this->redirectEvents();
        $raid = role_assign($role->id, $user2->id, $context->id);
        $events = $sink->get_events();
        $sink->close();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf('\core\event\role_assigned', $event);
        $this->assertSame('role', $event->target);
        $this->assertSame('role', $event->objecttable);
        $this->assertEquals($role->id, $event->objectid);
        $this->assertEquals($context->id, $event->contextid);
        $this->assertEquals($user2->id, $event->relateduserid);
        $this->assertCount(3, $event->other);
        $this->assertEquals($raid, $event->other['id']);
        $this->assertSame('', $event->other['component']);
        $this->assertEquals(0, $event->other['itemid']);
        $this->assertInstanceOf('moodle_url', $event->get_url());
        $this->assertSame('role_assigned', $event::get_legacy_eventname());
        $roles = get_all_roles();
        $rolenames = role_fix_names($roles, $context, ROLENAME_ORIGINAL, true);
        $expectedlegacylog = array($course->id, 'role', 'assign',
            'admin/roles/assign.php?contextid='.$context->id.'&roleid='.$role->id, $rolenames[$role->id], '', $USER->id);
        $this->assertEventLegacyLogData($expectedlegacylog, $event);
    }

    /**
     * Test role unassigning.
     *
     * @covers ::role_unassign
     */
    public function test_role_unassign() {
        global $DB, $USER;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $role = $DB->get_record('role', array('shortname'=>'student'));

        $context = context_course::instance($course->id);
        role_assign($role->id, $user->id, $context->id);
        $this->assertTrue($DB->record_exists('role_assignments', array('userid'=>$user->id, 'roleid'=>$role->id, 'contextid'=>$context->id)));
        role_unassign($role->id, $user->id, $context->id);
        $this->assertFalse($DB->record_exists('role_assignments', array('userid'=>$user->id, 'roleid'=>$role->id, 'contextid'=>$context->id)));

        role_assign($role->id, $user->id, $context->id, 'enrol_self', 1);
        $this->assertTrue($DB->record_exists('role_assignments', array('userid'=>$user->id, 'roleid'=>$role->id, 'contextid'=>$context->id)));
        role_unassign($role->id, $user->id, $context->id, 'enrol_self', 1);
        $this->assertFalse($DB->record_exists('role_assignments', array('userid'=>$user->id, 'roleid'=>$role->id, 'contextid'=>$context->id)));

        // Test event triggered.

        role_assign($role->id, $user->id, $context->id);
        $sink = $this->redirectEvents();
        role_unassign($role->id, $user->id, $context->id);
        $events = $sink->get_events();
        $sink->close();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf('\core\event\role_unassigned', $event);
        $this->assertSame('role', $event->target);
        $this->assertSame('role', $event->objecttable);
        $this->assertEquals($role->id, $event->objectid);
        $this->assertEquals($context->id, $event->contextid);
        $this->assertEquals($user->id, $event->relateduserid);
        $this->assertCount(3, $event->other);
        $this->assertSame('', $event->other['component']);
        $this->assertEquals(0, $event->other['itemid']);
        $this->assertInstanceOf('moodle_url', $event->get_url());
        $roles = get_all_roles();
        $rolenames = role_fix_names($roles, $context, ROLENAME_ORIGINAL, true);
        $expectedlegacylog = array($course->id, 'role', 'unassign',
            'admin/roles/assign.php?contextid='.$context->id.'&roleid='.$role->id, $rolenames[$role->id], '', $USER->id);
        $this->assertEventLegacyLogData($expectedlegacylog, $event);
    }

    /**
     * Test role unassigning.
     *
     * @covers ::role_unassign_all
     */
    public function test_role_unassign_all() {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $role = $DB->get_record('role', array('shortname'=>'student'));
        $role2 = $DB->get_record('role', array('shortname'=>'teacher'));
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

        // Test events triggered.

        role_assign($role2->id, $user->id, $coursecontext->id);
        role_assign($role2->id, $user->id, $modcontext->id);
        $sink = $this->redirectEvents();
        role_unassign_all(array('userid'=>$user->id, 'roleid'=>$role2->id));
        $events = $sink->get_events();
        $sink->close();
        $this->assertCount(2, $events);
        $this->assertInstanceOf('\core\event\role_unassigned', $events[0]);
        $this->assertInstanceOf('\core\event\role_unassigned', $events[1]);
    }

    /**
     * Test role queries.
     *
     * @covers ::get_roles_with_capability
     */
    public function test_get_roles_with_capability() {
        global $DB;

        $this->resetAfterTest();

        $syscontext = context_system::instance();
        $frontcontext = context_course::instance(SITEID);
        $manager = $DB->get_record('role', array('shortname'=>'manager'), '*', MUST_EXIST);
        $teacher = $DB->get_record('role', array('shortname'=>'teacher'), '*', MUST_EXIST);

        $this->assertTrue($DB->record_exists('capabilities', array('name'=>'moodle/backup:backupcourse'))); // Any capability is ok.
        $DB->delete_records('role_capabilities', array('capability'=>'moodle/backup:backupcourse'));

        $roles = get_roles_with_capability('moodle/backup:backupcourse');
        $this->assertEquals(array(), $roles);

        assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $manager->id, $syscontext->id);
        assign_capability('moodle/backup:backupcourse', CAP_PROHIBIT, $manager->id, $frontcontext->id);
        assign_capability('moodle/backup:backupcourse', CAP_PREVENT, $teacher->id, $frontcontext->id);

        $roles = get_roles_with_capability('moodle/backup:backupcourse');
        $this->assertEqualsCanonicalizing(array($teacher->id, $manager->id), array_keys($roles), true);

        $roles = get_roles_with_capability('moodle/backup:backupcourse', CAP_ALLOW);
        $this->assertEqualsCanonicalizing(array($manager->id), array_keys($roles), true);

        $roles = get_roles_with_capability('moodle/backup:backupcourse', null, $syscontext);
        $this->assertEqualsCanonicalizing(array($manager->id), array_keys($roles), true);
    }

    /**
     * Test deleting of roles.
     *
     * @covers ::delete_role
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

        // Delete role and get event.
        $sink = $this->redirectEvents();
        $result = delete_role($role->id);
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

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

        // Test triggered event.
        $this->assertInstanceOf('\core\event\role_deleted', $event);
        $this->assertSame('role', $event->target);
        $this->assertSame('role', $event->objecttable);
        $this->assertSame($role->id, $event->objectid);
        $this->assertEquals(context_system::instance(), $event->get_context());
        $this->assertSame($role->shortname, $event->other['shortname']);
        $this->assertSame($role->description, $event->other['description']);
        $this->assertSame($role->archetype, $event->other['archetype']);

        $expectedlegacylog = array(SITEID, 'role', 'delete', 'admin/roles/manage.php?action=delete&roleid='.$role->id,
                                   $role->shortname, '');
        $this->assertEventLegacyLogData($expectedlegacylog, $event);
    }

    /**
     * Test fetching of all roles.
     *
     * @covers ::get_all_roles
     */
    public function test_get_all_roles() {
        global $DB;

        $this->resetAfterTest();

        $allroles = get_all_roles();
        $this->assertIsArray($allroles);
        $initialrolescount = count($allroles);
        $this->assertTrue($initialrolescount >= 8); // There are 8 roles is standard install.
        $rolenames = array_column($allroles, 'shortname');
        foreach (get_role_archetypes() as $archetype) {
            $this->assertContains($archetype, $rolenames);
        }

        $role = reset($allroles);
        $role = (array)$role;

        $this->assertEqualsCanonicalizing(array('id', 'name', 'shortname', 'description', 'sortorder', 'archetype'),
            array_keys($role));

        foreach ($allroles as $roleid => $role) {
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
        $renames = $DB->get_records_menu('role_names', array('contextid'=>$coursecontext->id), '', 'roleid, name');

        $allroles = get_all_roles($coursecontext);
        $this->assertIsArray($allroles);
        $this->assertCount($initialrolescount + 1, $allroles);
        $role = reset($allroles);
        $role = (array)$role;

        $this->assertEqualsCanonicalizing(array('id', 'name', 'shortname', 'description', 'sortorder', 'archetype', 'coursealias'), array_keys($role));

        foreach ($allroles as $roleid => $role) {
            $this->assertEquals($role->id, $roleid);
            if (isset($renames[$roleid])) {
                $this->assertSame($renames[$roleid], $role->coursealias);
            } else {
                $this->assertNull($role->coursealias);
            }
        }
    }

    /**
     * Test getting of all archetypes.
     *
     * @covers ::get_role_archetypes
     */
    public function test_get_role_archetypes() {
        $archetypes = get_role_archetypes();
        $this->assertCount(8, $archetypes); // There are 8 archetypes in standard install.
        foreach ($archetypes as $k => $v) {
            $this->assertSame($k, $v);
        }
    }

    /**
     * Test getting of roles with given archetype.
     *
     * @covers ::get_archetype_roles
     */
    public function test_get_archetype_roles() {
        $this->resetAfterTest();

        // New install should have at least 1 role for each archetype.
        $archetypes = get_role_archetypes();
        foreach ($archetypes as $archetype) {
            $roles = get_archetype_roles($archetype);
            $this->assertGreaterThanOrEqual(1, count($roles));
            $role = reset($roles);
            $this->assertSame($archetype, $role->archetype);
        }

        create_role('New student role', 'student2', 'New student description', 'student');
        $roles = get_archetype_roles('student');
        $this->assertGreaterThanOrEqual(2, count($roles));
    }

    /**
     * Test aliased role names.
     *
     * @covers ::role_get_name
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
        $renames = $DB->get_records_menu('role_names', array('contextid'=>$coursecontext->id), '', 'roleid, name');

        foreach ($allroles as $role) {
            if (in_array($role->shortname, get_role_archetypes())) {
                // Standard roles do not have a set name.
                $this->assertSame('', $role->name);
            }
            // Get localised name from lang pack.
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
                $this->assertNull(role_get_name($role, $coursecontext, ROLENAME_ALIAS_RAW));
                $this->assertSame($name, role_get_name($role, $coursecontext, ROLENAME_BOTH));
            }
            $this->assertSame($name, role_get_name($role));
            $this->assertSame($name, role_get_name($role, $coursecontext, ROLENAME_ORIGINAL));
            $this->assertSame($name, role_get_name($role, null, ROLENAME_ORIGINAL));
            $this->assertSame($role->shortname, role_get_name($role, $coursecontext, ROLENAME_SHORT));
            $this->assertSame($role->shortname, role_get_name($role, null, ROLENAME_SHORT));
            $this->assertSame("$name ($role->shortname)", role_get_name($role, $coursecontext, ROLENAME_ORIGINALANDSHORT));
            $this->assertSame("$name ($role->shortname)", role_get_name($role, null, ROLENAME_ORIGINALANDSHORT));
            $this->assertNull(role_get_name($role, null, ROLENAME_ALIAS_RAW));
        }
    }

    /**
     * Test tweaking of role name arrays.
     *
     * @covers ::role_fix_names
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
        $renames = $DB->get_records_menu('role_names', array('contextid'=>$coursecontext->id), '', 'roleid, name');

        // Make sure all localname contain proper values for each ROLENAME_ constant,
        // note role_get_name() on frontpage is used to get the original name for future compatibility.
        $roles = $allroles;
        unset($roles[$student->id]); // Remove one role to make sure no role is added or removed.
        $rolenames = array();
        foreach ($roles as $role) {
            $rolenames[$role->id] = $role->name;
        }

        $alltypes = array(ROLENAME_ALIAS, ROLENAME_ALIAS_RAW, ROLENAME_BOTH, ROLENAME_ORIGINAL, ROLENAME_ORIGINALANDSHORT, ROLENAME_SHORT);
        foreach ($alltypes as $type) {
            $fixed = role_fix_names($roles, $coursecontext, $type);
            $this->assertCount(count($roles), $fixed);
            foreach ($fixed as $roleid => $rolename) {
                $this->assertInstanceOf('stdClass', $rolename);
                $role = $allroles[$roleid];
                $name = role_get_name($role, $coursecontext, $type);
                $this->assertSame($name, $rolename->localname);
            }
            $fixed = role_fix_names($rolenames, $coursecontext, $type);
            $this->assertCount(count($rolenames), $fixed);
            foreach ($fixed as $roleid => $rolename) {
                $role = $allroles[$roleid];
                $name = role_get_name($role, $coursecontext, $type);
                $this->assertSame($name, $rolename);
            }
        }
    }

    /**
     * Test role default allows.
     *
     * @covers ::get_default_role_archetype_allows
     */
    public function test_get_default_role_archetype_allows() {
        $archetypes = get_role_archetypes();
        foreach ($archetypes as $archetype) {

            $result = get_default_role_archetype_allows('assign', $archetype);
            $this->assertIsArray($result);

            $result = get_default_role_archetype_allows('override', $archetype);
            $this->assertIsArray($result);

            $result = get_default_role_archetype_allows('switch', $archetype);
            $this->assertIsArray($result);

            $result = get_default_role_archetype_allows('view', $archetype);
            $this->assertIsArray($result);
        }

        $result = get_default_role_archetype_allows('assign', '');
        $this->assertSame(array(), $result);

        $result = get_default_role_archetype_allows('override', '');
        $this->assertSame(array(), $result);

        $result = get_default_role_archetype_allows('switch', '');
        $this->assertSame(array(), $result);

        $result = get_default_role_archetype_allows('view', '');
        $this->assertSame(array(), $result);

        $result = get_default_role_archetype_allows('assign', 'wrongarchetype');
        $this->assertSame(array(), $result);
        $this->assertDebuggingCalled();

        $result = get_default_role_archetype_allows('override', 'wrongarchetype');
        $this->assertSame(array(), $result);
        $this->assertDebuggingCalled();

        $result = get_default_role_archetype_allows('switch', 'wrongarchetype');
        $this->assertSame(array(), $result);
        $this->assertDebuggingCalled();

        $result = get_default_role_archetype_allows('view', 'wrongarchetype');
        $this->assertSame(array(), $result);
        $this->assertDebuggingCalled();
    }

    /**
     * Test allowing of role assignments.
     *
     * @covers ::core_role_set_assign_allowed
     */
    public function test_core_role_set_assign_allowed() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $otherid = create_role('Other role', 'other', 'Some other role', '');
        $student = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);

        $this->assertFalse($DB->record_exists('role_allow_assign', array('roleid'=>$otherid, 'allowassign'=>$student->id)));
        core_role_set_assign_allowed($otherid, $student->id);
        $this->assertTrue($DB->record_exists('role_allow_assign', array('roleid'=>$otherid, 'allowassign'=>$student->id)));

        // Test event trigger.
        $allowroleassignevent = \core\event\role_allow_assign_updated::create([
            'context' => context_system::instance(),
            'objectid' => $otherid,
            'other' => ['targetroleid' => $student->id]
        ]);
        $sink = $this->redirectEvents();
        $allowroleassignevent->trigger();
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\role_allow_assign_updated', $event);
        $mode = 'assign';
        $baseurl = new moodle_url('/admin/roles/allow.php', array('mode' => $mode));
        $expectedlegacylog = array(SITEID, 'role', 'edit allow ' . $mode, str_replace($CFG->wwwroot . '/', '', $baseurl));
        $this->assertEventLegacyLogData($expectedlegacylog, $event);
    }

    /**
     * Test allowing of role overrides.
     *
     * @covers ::core_role_set_override_allowed
     */
    public function test_core_role_set_override_allowed() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $otherid = create_role('Other role', 'other', 'Some other role', '');
        $student = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);

        $this->assertFalse($DB->record_exists('role_allow_override', array('roleid'=>$otherid, 'allowoverride'=>$student->id)));
        core_role_set_override_allowed($otherid, $student->id);
        $this->assertTrue($DB->record_exists('role_allow_override', array('roleid'=>$otherid, 'allowoverride'=>$student->id)));

        // Test event trigger.
        $allowroleassignevent = \core\event\role_allow_override_updated::create([
            'context' => context_system::instance(),
            'objectid' => $otherid,
            'other' => ['targetroleid' => $student->id]
        ]);
        $sink = $this->redirectEvents();
        $allowroleassignevent->trigger();
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\role_allow_override_updated', $event);
        $mode = 'override';
        $baseurl = new moodle_url('/admin/roles/allow.php', array('mode' => $mode));
        $expectedlegacylog = array(SITEID, 'role', 'edit allow ' . $mode, str_replace($CFG->wwwroot . '/', '', $baseurl));
        $this->assertEventLegacyLogData($expectedlegacylog, $event);
    }

    /**
     * Test allowing of role switching.
     *
     * @covers ::core_role_set_switch_allowed
     */
    public function test_core_role_set_switch_allowed() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $otherid = create_role('Other role', 'other', 'Some other role', '');
        $student = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);

        $this->assertFalse($DB->record_exists('role_allow_switch', array('roleid'=>$otherid, 'allowswitch'=>$student->id)));
        core_role_set_switch_allowed($otherid, $student->id);
        $this->assertTrue($DB->record_exists('role_allow_switch', array('roleid'=>$otherid, 'allowswitch'=>$student->id)));

        // Test event trigger.
        $allowroleassignevent = \core\event\role_allow_switch_updated::create([
            'context' => context_system::instance(),
            'objectid' => $otherid,
            'other' => ['targetroleid' => $student->id]
        ]);
        $sink = $this->redirectEvents();
        $allowroleassignevent->trigger();
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\role_allow_switch_updated', $event);
        $mode = 'switch';
        $baseurl = new moodle_url('/admin/roles/allow.php', array('mode' => $mode));
        $expectedlegacylog = array(SITEID, 'role', 'edit allow ' . $mode, str_replace($CFG->wwwroot . '/', '', $baseurl));
        $this->assertEventLegacyLogData($expectedlegacylog, $event);
    }

    /**
     * Test allowing of role switching.
     *
     * @covers ::core_role_set_view_allowed
     */
    public function test_core_role_set_view_allowed() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $otherid = create_role('Other role', 'other', 'Some other role', '');
        $student = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);

        $this->assertFalse($DB->record_exists('role_allow_view', array('roleid' => $otherid, 'allowview' => $student->id)));
        core_role_set_view_allowed($otherid, $student->id);
        $this->assertTrue($DB->record_exists('role_allow_view', array('roleid' => $otherid, 'allowview' => $student->id)));

        // Test event trigger.
        $allowroleassignevent = \core\event\role_allow_view_updated::create([
            'context' => context_system::instance(),
            'objectid' => $otherid,
            'other' => ['targetroleid' => $student->id]
        ]);
        $sink = $this->redirectEvents();
        $allowroleassignevent->trigger();
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\role_allow_view_updated', $event);
        $mode = 'view';
        $baseurl = new moodle_url('/admin/roles/allow.php', array('mode' => $mode));
        $expectedlegacylog = array(SITEID, 'role', 'edit allow ' . $mode, str_replace($CFG->wwwroot . '/', '', $baseurl));
        $this->assertEventLegacyLogData($expectedlegacylog, $event);
    }

    /**
     * Test returning of assignable roles in context.
     *
     * @covers ::get_assignable_roles
     */
    public function test_get_assignable_roles() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        $teacherename = (object)array('roleid'=>$teacherrole->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherename);

        $studentrole = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $student = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $student->id, $coursecontext);

        $contexts = $DB->get_records('context');
        $users = $DB->get_records('user');
        $allroles = $DB->get_records('role');

        // Evaluate all results for all users in all contexts.
        foreach ($users as $user) {
            $this->setUser($user);
            foreach ($contexts as $contextid => $unused) {
                $context = context_helper::instance_by_id($contextid);
                $roles = get_assignable_roles($context, ROLENAME_SHORT);
                foreach ($allroles as $roleid => $role) {
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

        // Not-logged-in user.
        $this->setUser(0);
        foreach ($contexts as $contextid => $unused) {
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
        foreach ($allroles as $roleid => $role) {
            set_role_contextlevels($roleid, $alllevels);
        }
        $alltypes = array(ROLENAME_ALIAS, ROLENAME_ALIAS_RAW, ROLENAME_BOTH, ROLENAME_ORIGINAL, ROLENAME_ORIGINALANDSHORT, ROLENAME_SHORT);
        foreach ($alltypes as $type) {
            $rolenames = role_fix_names($allroles, $coursecontext, $type);
            $roles = get_assignable_roles($coursecontext, $type, false, $admin);
            foreach ($roles as $roleid => $rolename) {
                $this->assertSame($rolenames[$roleid]->localname, $rolename);
            }
        }

        // Verify counts.
        $alltypes = array(ROLENAME_ALIAS, ROLENAME_ALIAS_RAW, ROLENAME_BOTH, ROLENAME_ORIGINAL, ROLENAME_ORIGINALANDSHORT, ROLENAME_SHORT);
        foreach ($alltypes as $type) {
            $roles = get_assignable_roles($coursecontext, $type, false, $admin);
            list($rolenames, $rolecounts, $nameswithcounts) = get_assignable_roles($coursecontext, $type, true, $admin);
            $this->assertEquals($roles, $rolenames);
            foreach ($rolenames as $roleid => $name) {
                if ($roleid == $teacherrole->id or $roleid == $studentrole->id) {
                    $this->assertEquals(1, $rolecounts[$roleid]);
                } else {
                    $this->assertEquals(0, $rolecounts[$roleid]);
                }
                $this->assertSame("$name ($rolecounts[$roleid])", $nameswithcounts[$roleid]);
            }
        }
    }

    /**
     * Test user count of assignable roles in context where users are assigned the role via different components.
     *
     * @covers ::get_assignable_roles
     */
    public function test_get_assignable_roles_distinct_usercount() {
        global $DB;

        $this->resetAfterTest(true);

        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);

        // Assign each user the student role in course.
        role_assign($studentrole->id, $user1->id, $context->id);
        role_assign($studentrole->id, $user2->id, $context->id);

        list($rolenames, $rolecounts, $nameswithcounts) = get_assignable_roles($context, ROLENAME_SHORT, true);
        $this->assertEquals(2, $rolecounts[$studentrole->id]);

        // Assign first user the student role in course again (this time via 'enrol_self' component).
        role_assign($studentrole->id, $user1->id, $context->id, 'enrol_self', 1);

        // There are still only two distinct users.
        list($rolenames, $rolecounts, $nameswithcounts) = get_assignable_roles($context, ROLENAME_SHORT, true);
        $this->assertEquals(2, $rolecounts[$studentrole->id]);
    }

    /**
     * Test getting of all switchable roles.
     *
     * @covers ::get_switchable_roles
     */
    public function test_get_switchable_roles() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        $teacherename = (object)array('roleid'=>$teacherrole->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherename);

        $contexts = $DB->get_records('context');
        $users = $DB->get_records('user');
        $allroles = $DB->get_records('role');

        // Evaluate all results for all users in all contexts.
        foreach ($users as $user) {
            $this->setUser($user);
            foreach ($contexts as $contextid => $unused) {
                $context = context_helper::instance_by_id($contextid);
                $roles = get_switchable_roles($context);
                foreach ($allroles as $roleid => $role) {
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
                        $this->assertSame(role_get_name($role, $coursecontext), $roles[$roleid]);
                    }
                }
            }
        }
    }

    /**
     * Test getting of all overridable roles.
     *
     * @covers ::get_overridable_roles
     */
    public function test_get_overridable_roles() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        $teacherename = (object)array('roleid'=>$teacherrole->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherename);
        $this->assertTrue($DB->record_exists('capabilities', array('name'=>'moodle/backup:backupcourse'))); // Any capability is ok.
        assign_capability('moodle/backup:backupcourse', CAP_PROHIBIT, $teacherrole->id, $coursecontext->id);

        $studentrole = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $student = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $student->id, $coursecontext);

        $contexts = $DB->get_records('context');
        $users = $DB->get_records('user');
        $allroles = $DB->get_records('role');

        // Evaluate all results for all users in all contexts.
        foreach ($users as $user) {
            $this->setUser($user);
            foreach ($contexts as $contextid => $unused) {
                $context = context_helper::instance_by_id($contextid);
                $roles = get_overridable_roles($context, ROLENAME_SHORT);
                foreach ($allroles as $roleid => $role) {
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
            foreach ($roles as $roleid => $rolename) {
                $this->assertSame($rolenames[$roleid]->localname, $rolename);
            }
        }

        // Verify counts.
        $roles = get_overridable_roles($coursecontext, ROLENAME_ALIAS, false);
        list($rolenames, $rolecounts, $nameswithcounts) = get_overridable_roles($coursecontext, ROLENAME_ALIAS, true);
        $this->assertEquals($roles, $rolenames);
        foreach ($rolenames as $roleid => $name) {
            if ($roleid == $teacherrole->id) {
                $this->assertEquals(1, $rolecounts[$roleid]);
            } else {
                $this->assertEquals(0, $rolecounts[$roleid]);
            }
            $this->assertSame("$name ($rolecounts[$roleid])", $nameswithcounts[$roleid]);
        }
    }

    /**
     * Test getting of all overridable roles.
     *
     * @covers ::get_viewable_roles
     */
    public function test_get_viewable_roles_course() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $studentrolerename = (object) array('roleid' => $studentrole->id, 'name' => 'Učitel', 'contextid' => $coursecontext->id);
        $DB->insert_record('role_names', $studentrolerename);

        // By default teacher can see student.
        $this->setUser($teacher);
        $viewableroles = get_viewable_roles($coursecontext);
        $this->assertContains($studentrolerename->name, array_values($viewableroles));
        // Remove view permission.
        $DB->delete_records('role_allow_view', array('roleid' => $teacherrole->id, 'allowview' => $studentrole->id));
        $viewableroles = get_viewable_roles($coursecontext);
        // Teacher can no longer see student role.
        $this->assertNotContains($studentrolerename->name, array_values($viewableroles));
        // Allow again teacher to view student.
        core_role_set_view_allowed($teacherrole->id, $studentrole->id);
        // Teacher can now see student role.
        $viewableroles = get_viewable_roles($coursecontext);
        $this->assertContains($studentrolerename->name, array_values($viewableroles));
    }

    /**
     * Test getting of all overridable roles.
     *
     * @covers ::get_viewable_roles
     */
    public function test_get_viewable_roles_system() {
        global $DB;

        $this->resetAfterTest();

        $context = context_system::instance();

        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $context);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $studentrolename = role_get_name($studentrole, $context);

        // By default teacher can see student.
        $this->setUser($teacher);
        $viewableroles = get_viewable_roles($context);
        $this->assertContains($studentrolename, array_values($viewableroles));
        // Remove view permission.
        $DB->delete_records('role_allow_view', array('roleid' => $teacherrole->id, 'allowview' => $studentrole->id));
        $viewableroles = get_viewable_roles($context);
        // Teacher can no longer see student role.
        $this->assertNotContains($studentrolename, array_values($viewableroles));
        // Allow again teacher to view student.
        core_role_set_view_allowed($teacherrole->id, $studentrole->id);
        // Teacher can now see student role.
        $viewableroles = get_viewable_roles($context);
        $this->assertContains($studentrolename, array_values($viewableroles));
    }

    /**
     * Test we have context level defaults.
     *
     * @covers ::get_default_contextlevels
     */
    public function test_get_default_contextlevels() {
        $archetypes = get_role_archetypes();
        $alllevels = context_helper::get_all_levels();
        foreach ($archetypes as $archetype) {
            $defaults = get_default_contextlevels($archetype);
            $this->assertIsArray($defaults);
            foreach ($defaults as $level) {
                $this->assertTrue(isset($alllevels[$level]));
            }
        }
    }

    /**
     * Test role context level setup.
     *
     * @covers ::set_role_contextlevels
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
     *
     * @covers ::get_roles_for_contextlevels
     */
    public function test_get_roles_for_contextlevels() {
        global $DB;

        $allroles = get_all_roles();
        foreach (context_helper::get_all_levels() as $level => $unused) {
            $roles = get_roles_for_contextlevels($level);
            foreach ($allroles as $roleid => $unused) {
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
     *
     * @covers ::get_default_enrol_roles
     */
    public function test_get_default_enrol_roles() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $id2 = create_role('New student role', 'student2', 'New student description', 'student');
        set_role_contextlevels($id2, array(CONTEXT_COURSE));

        $allroles = get_all_roles();
        $expected = array($id2=>$allroles[$id2]);

        foreach (get_roles_for_contextlevels(CONTEXT_COURSE) as $roleid) {
            $expected[$roleid] = $roleid;
        }

        $roles = get_default_enrol_roles($coursecontext);
        foreach ($allroles as $role) {
            $this->assertEquals(isset($expected[$role->id]), isset($roles[$role->id]));
            if (isset($roles[$role->id])) {
                $this->assertSame(role_get_name($role, $coursecontext), $roles[$role->id]);
            }
        }
    }

    /**
     * Test getting of role users.
     *
     * @covers ::get_role_users
     */
    public function test_get_role_users() {
        global $DB;

        $this->resetAfterTest();

        $systemcontext = context_system::instance();
        $studentrole = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $noeditteacherrole = $DB->get_record('role', array('shortname' => 'teacher'), '*', MUST_EXIST);
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
        $this->getDataGenerator()->enrol_user($user4->id, $course->id, $noeditteacherrole->id);

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

        $users = get_role_users($teacherrole->id, $coursecontext, false, 'u.id, u.email');
        $this->assertDebuggingCalled('get_role_users() adding u.lastname, u.firstname to the query result because they were required by $sort but missing in $fields');
        $this->assertCount(2, $users);
        $this->assertArrayHasKey($user1->id, $users);
        $this->assertObjectHasAttribute('lastname', $users[$user1->id]);
        $this->assertObjectHasAttribute('firstname', $users[$user1->id]);
        $this->assertArrayHasKey($user3->id, $users);
        $this->assertObjectHasAttribute('lastname', $users[$user3->id]);
        $this->assertObjectHasAttribute('firstname', $users[$user3->id]);

        $users = get_role_users($teacherrole->id, $coursecontext, false, 'u.id AS id_alias');
        $this->assertDebuggingCalled('get_role_users() adding u.lastname, u.firstname to the query result because they were required by $sort but missing in $fields');
        $this->assertCount(2, $users);
        $this->assertArrayHasKey($user1->id, $users);
        $this->assertObjectHasAttribute('id_alias', $users[$user1->id]);
        $this->assertObjectHasAttribute('lastname', $users[$user1->id]);
        $this->assertObjectHasAttribute('firstname', $users[$user1->id]);
        $this->assertArrayHasKey($user3->id, $users);
        $this->assertObjectHasAttribute('id_alias', $users[$user3->id]);
        $this->assertObjectHasAttribute('lastname', $users[$user3->id]);
        $this->assertObjectHasAttribute('firstname', $users[$user3->id]);

        $users = get_role_users($teacherrole->id, $coursecontext, false, 'u.id, u.email, u.idnumber', 'u.idnumber', null, $group->id);
        $this->assertCount(1, $users);
        $this->assertArrayHasKey($user3->id, $users);

        $users = get_role_users($teacherrole->id, $coursecontext, true, 'u.id, u.email, u.idnumber, u.firstname', 'u.idnumber', null, '', '', '', 'u.firstname = :xfirstname', array('xfirstname'=>'John'));
        $this->assertCount(1, $users);
        $this->assertArrayHasKey($user1->id, $users);

        $users = get_role_users(array($noeditteacherrole->id, $studentrole->id), $coursecontext, false, 'ra.id', 'ra.id');
        $this->assertDebuggingNotCalled();
        $users = get_role_users(array($noeditteacherrole->id, $studentrole->id), $coursecontext, false, 'ra.userid', 'ra.userid');
        $this->assertDebuggingCalled('get_role_users() without specifying one single roleid needs to be called prefixing ' .
            'role assignments id (ra.id) as unique field, you can use $fields param for it.');
        $users = get_role_users(array($noeditteacherrole->id, $studentrole->id), $coursecontext, false);
        $this->assertDebuggingCalled('get_role_users() without specifying one single roleid needs to be called prefixing ' .
            'role assignments id (ra.id) as unique field, you can use $fields param for it.');
        $users = get_role_users(array($noeditteacherrole->id, $studentrole->id), $coursecontext,
            false, 'u.id, u.firstname', 'u.id, u.firstname');
        $this->assertDebuggingCalled('get_role_users() without specifying one single roleid needs to be called prefixing ' .
            'role assignments id (ra.id) as unique field, you can use $fields param for it.');
    }

    /**
     * Test used role query.
     *
     * @covers ::get_roles_used_in_context
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
        $this->assertSame($teacherrole->name, $role->name);
        $this->assertSame($teacherrole->shortname, $role->shortname);
        $this->assertEquals($teacherrole->sortorder, $role->sortorder);
        $this->assertSame($teacherrename->name, $role->coursealias);

        $user2 = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $user2->id, $systemcontext->id);
        role_assign($otherid, $user2->id, $systemcontext->id);

        $roles = get_roles_used_in_context($systemcontext);
        $this->assertCount(2, $roles);
    }

    /**
     * Test roles used in course.
     *
     * @covers ::get_user_roles_in_course
     */
    public function test_get_user_roles_in_course() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $studentrole = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'), '*', MUST_EXIST);
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $teacherrename = (object)array('roleid'=>$teacherrole->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherrename);

        $roleids = explode(',', $CFG->profileroles); // Should include teacher and student in new installs.
        $this->assertTrue(in_array($teacherrole->id, $roleids));
        $this->assertTrue(in_array($studentrole->id, $roleids));
        $this->assertFalse(in_array($managerrole->id, $roleids));

        $user1 = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $user1->id, $coursecontext->id);
        role_assign($studentrole->id, $user1->id, $coursecontext->id);
        $user2 = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $user2->id, $coursecontext->id);
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        role_assign($managerrole->id, $user4->id, $coursecontext->id);

        $this->setAdminUser();

        $roles = get_user_roles_in_course($user1->id, $course->id);
        $this->assertEquals([
            role_get_name($teacherrole, $coursecontext),
            role_get_name($studentrole, $coursecontext),
        ], array_map('strip_tags', explode(', ', $roles)));

        $roles = get_user_roles_in_course($user2->id, $course->id);
        $this->assertEquals([
            role_get_name($studentrole, $coursecontext),
        ], array_map('strip_tags', explode(', ', $roles)));

        $roles = get_user_roles_in_course($user3->id, $course->id);
        $this->assertEmpty($roles);

        // Managers should be able to see a link to their own role type, given they can assign it in the context.
        $this->setUser($user4);
        $roles = get_user_roles_in_course($user4->id, $course->id);
        $this->assertEquals([
            role_get_name($managerrole, $coursecontext),
        ], array_map('strip_tags', explode(', ', $roles)));

        // Managers should see 2 roles if viewing a user who has been enrolled as a student and a teacher in the course.
        $roles = get_user_roles_in_course($user1->id, $course->id);
        $this->assertEquals([
            role_get_name($teacherrole, $coursecontext),
            role_get_name($studentrole, $coursecontext),
        ], array_map('strip_tags', explode(', ', $roles)));

        // Students should not see the manager role if viewing a manager's profile.
        $this->setUser($user2);
        $roles = get_user_roles_in_course($user4->id, $course->id);
        $this->assertEmpty($roles); // Should see 0 roles on the manager's profile.
    }

    /**
     * Test get_user_roles and get_users_roles
     *
     * @covers ::get_user_roles
     */
    public function test_get_user_roles() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $studentrole = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $teacherrename = (object)array('roleid'=>$teacherrole->id, 'name'=>'Učitel', 'contextid'=>$coursecontext->id);
        $DB->insert_record('role_names', $teacherrename);

        $roleids = explode(',', $CFG->profileroles); // Should include teacher and student in new installs.

        $user1 = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $user1->id, $coursecontext->id);
        role_assign($studentrole->id, $user1->id, $coursecontext->id);
        $user2 = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $user2->id, $coursecontext->id);
        $user3 = $this->getDataGenerator()->create_user();

        $u1roles = get_user_roles($coursecontext, $user1->id);

        $u2roles = get_user_roles($coursecontext, $user2->id);

        $allroles = get_users_roles($coursecontext, [], false);
        $specificuserroles = get_users_roles($coursecontext, [$user1->id, $user2->id]);
        $this->assertEquals($u1roles, $allroles[$user1->id]);
        $this->assertEquals($u1roles, $specificuserroles[$user1->id]);
        $this->assertEquals($u2roles, $allroles[$user2->id]);
        $this->assertEquals($u2roles, $specificuserroles[$user2->id]);
    }

    /**
     * Test has_capability(), has_any_capability() and has_all_capabilities().
     *
     * @covers ::has_capability
     * @covers ::has_any_capability
     * @covers ::has_all_capabilities
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
     * Utility method to fake a plugin
     *
     * @param string $pluginname plugin name
     * @return void
     */
    protected function setup_fake_plugin($pluginname) {
        global $CFG;
        // Here we have to hack the component loader so we can insert our fake plugin and test that
        // the access.php works.
        $mockedcomponent = new ReflectionClass(core_component::class);
        $mockedplugins = $mockedcomponent->getProperty('plugins');
        $mockedplugins->setAccessible(true);
        $plugins = $mockedplugins->getValue();
        $plugins['fake'] = [$pluginname => "{$CFG->dirroot}/lib/tests/fixtures/fakeplugins/$pluginname"];
        $mockedplugins->setValue($plugins);
        update_capabilities('fake_access');
        $this->resetDebugging(); // We have debugging messages here that we need to get rid of.
        // End of the component loader mock.
    }

    /**
     * Test get_deprecated_capability_info()
     *
     * @covers ::get_deprecated_capability_info
     */
    public function test_get_deprecated_capability_info() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $user = $this->getDataGenerator()->create_and_enrol($course);
        $this->setup_fake_plugin('access');

        // For now we have deprecated fake/access:fakecapability.
        $capinfo = get_deprecated_capability_info('fake/access:fakecapability');
        $this->assertNotEmpty($capinfo);
        $this->assertEquals("The capability 'fake/access:fakecapability' is"
            . " deprecated.This capability should not be used anymore.", $capinfo['fullmessage']);
    }

    /**
     * Test get_deprecated_capability_info() through has_capability
     *
     * @covers ::get_deprecated_capability_info
     */
    public function test_get_deprecated_capability_info_through_has_capability() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $user = $this->getDataGenerator()->create_and_enrol($course);
        $this->setup_fake_plugin('access');

        // For now we have deprecated fake/access:fakecapability.
        $hascap = has_capability('fake/access:fakecapability', $coursecontext, $user);
        $this->assertTrue($hascap);
        $this->assertDebuggingCalled("The capability 'fake/access:fakecapability' is deprecated."
            . "This capability should not be used anymore.");
    }

    /**
     * Test get_deprecated_capability_info() through get_user_capability_contexts()
     *
     * @covers ::get_deprecated_capability_info
     */
    public function test_get_deprecated_capability_info_through_get_user_capability_contexts() {
        $this->resetAfterTest();
        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['categoryid' => $category->id]);
        $user = $this->getDataGenerator()->create_and_enrol($course);
        $this->setup_fake_plugin('access');

        // For now we have deprecated fake/access:fakecapability.
        list($categories, $courses) = get_user_capability_contexts('fake/access:fakecapability', false, $user->id);
        $this->assertNotEmpty($courses);
        $this->assertDebuggingCalled("The capability 'fake/access:fakecapability' is deprecated."
                . "This capability should not be used anymore.");
    }

    /**
     * Test get_deprecated_capability_info with a capability that does not exist
     *
     * @param string $capability the capability name
     * @param array $debugmessages the debug messsages we expect
     * @param bool $expectedexisting does the capability exist
     * @covers ::get_deprecated_capability_info
     * @dataProvider deprecated_capabilities_use_cases
     */
    public function test_get_deprecated_capability_specific_cases(string $capability, array $debugmessages,
        bool $expectedexisting) {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $user = $this->getDataGenerator()->create_and_enrol($course);
        $this->setup_fake_plugin('access');

        // For now we have deprecated fake/access:fakecapability.
        $this->resetDebugging();
        $hascap = has_capability($capability, $coursecontext, $user);
        $this->assertEquals($expectedexisting, $hascap);
        $this->assertDebuggingCalledCount(count($debugmessages), $debugmessages);
    }

    /**
     * Specific use case for deprecated capabilities
     *
     * @return array
     */
    public function deprecated_capabilities_use_cases() {
        return [
            'capability missing' => [
                'fake/access:missingcapability',
                [
                    "Capability \"fake/access:missingcapability\" was not found! This has to be fixed in code."
                ],
                false
            ],
            'replacement no info' => [
                'fake/access:replacementnoinfo',
                [
                    "The capability 'fake/access:replacementnoinfo' is deprecated.",
                ],
                true
            ],
            'replacement missing' => [
                'fake/access:replacementmissing',
                [
                    "The capability 'fake/access:replacementmissing' is deprecated.This capability should not be used anymore.",
                ],
                true
            ],
            'replacement with non existing cap' => [
                'fake/access:replacementwithwrongcapability',
                [
                    "Capability 'fake/access:replacementwithwrongcapability' was supposed to be replaced with"
                    . " 'fake/access:nonexistingcapabilty', which does not exist !",
                    "The capability 'fake/access:replacementwithwrongcapability' is deprecated."
                    . "This capability should not be used anymore.It will be replaced by 'fake/access:nonexistingcapabilty'."
                ],
                true
            ],
            'replacement with existing' => [
                'fake/access:replacementwithexisting', // Existing capability buf for a different role.
                [
                    "The capability 'fake/access:replacementwithexisting' is deprecated.This capability should not be used anymore."
                    . "It will be replaced by 'fake/access:existingcapability'.",
                ],
                false // As the capability is applied to managers, we should not have this capability for this simple user.
            ],
        ];
    }

    /**
     * Test that assigning a fake cap does not return.
     *
     * @covers ::get_users_by_capability
     * @covers ::get_with_capability_join
     * @covers ::get_with_capability_sql
     * @covers ::has_capability
     */
    public function test_fake_capability() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();

        $fakecapname = 'moodle/fake:capability';

        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        $admin = $DB->get_record('user', array('username' => 'admin'));

        // Test a capability which does not exist.
        // Note: Do not use assign_capability because it will not allow fake caps.
        $DB->insert_record('role_capabilities', (object) [
            'contextid' => $coursecontext->id,
            'roleid' => $teacherrole->id,
            'capability' => $fakecapname,
            'permission' => CAP_ALLOW,
            'timemodified' => time(),
            'modifierid' => 0,
        ]);

        // Check `has_capability`.
        $this->assertFalse(has_capability($fakecapname, $coursecontext, $teacher));
        $this->assertDebuggingCalled("Capability \"{$fakecapname}\" was not found! This has to be fixed in code.");
        $this->assertFalse(has_capability($fakecapname, $coursecontext, $admin));
        $this->assertDebuggingCalled("Capability \"{$fakecapname}\" was not found! This has to be fixed in code.");

        // Check `get_with_capability_sql` (with uses `get_with_capability_join`).
        list($sql, $params) = get_with_capability_sql($coursecontext, $fakecapname);
        $users = $DB->get_records_sql($sql, $params);

        $this->assertFalse(array_key_exists($teacher->id, $users));
        $this->assertFalse(array_key_exists($admin->id, $users));

        // Check `get_users_by_capability`.
        $users = get_users_by_capability($coursecontext, $fakecapname);

        $this->assertFalse(array_key_exists($teacher->id, $users));
        $this->assertFalse(array_key_exists($admin->id, $users));
    }

    /**
     * Test that assigning a fake cap does not return.
     *
     * @covers ::assign_capability
     */
    public function test_fake_capability_assign() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();

        $capability = 'moodle/fake:capability';

        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        $admin = $DB->get_record('user', array('username' => 'admin'));

        $this->expectException('coding_exception');
        $this->expectExceptionMessage("Capability '{$capability}' was not found! This has to be fixed in code.");
        assign_capability($capability, CAP_ALLOW, $teacherrole->id, $coursecontext);
    }

    /**
     * Test that assigning a fake cap does not return.
     *
     * @covers ::unassign_capability
     */
    public function test_fake_capability_unassign() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();

        $capability = 'moodle/fake:capability';

        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        $admin = $DB->get_record('user', array('username' => 'admin'));

        $this->expectException('coding_exception');
        $this->expectExceptionMessage("Capability '{$capability}' was not found! This has to be fixed in code.");
        unassign_capability($capability, CAP_ALLOW, $teacherrole->id, $coursecontext);
    }

    /**
     * Test that the caching in get_role_definitions() and get_role_definitions_uncached()
     * works as intended.
     *
     * @covers ::get_role_definitions
     * @covers ::role_change_permission
     */
    public function test_role_definition_caching() {
        global $DB;

        $this->resetAfterTest();

        // Get some role ids.
        $authenticatedrole = $DB->get_record('role', array('shortname' => 'user'), '*', MUST_EXIST);
        $studentrole = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $emptyroleid = create_role('No capabilities', 'empty', 'A role with no capabilties');
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        // Instantiate the cache instance, since that does DB queries (get_config)
        // and we don't care about those.
        cache::make('core', 'roledefs');

        // One database query is not necessarily one database read, it seems. Find out how many.
        $startdbreads = $DB->perf_get_reads();
        $rs = $DB->get_recordset('user');
        $rs->close();
        $readsperquery = $DB->perf_get_reads() - $startdbreads;

        // Now load some role definitions, and check when it queries the database.

        // Load the capabilities for two roles. Should be one query.
        $startdbreads = $DB->perf_get_reads();
        get_role_definitions([$authenticatedrole->id, $studentrole->id]);
        $this->assertEquals(1 * $readsperquery, $DB->perf_get_reads() - $startdbreads);

        // Load the capabilities for same two roles. Should not query the DB.
        $startdbreads = $DB->perf_get_reads();
        get_role_definitions([$authenticatedrole->id, $studentrole->id]);
        $this->assertEquals(0 * $readsperquery, $DB->perf_get_reads() - $startdbreads);

        // Include a third role. Should do one DB query.
        $startdbreads = $DB->perf_get_reads();
        get_role_definitions([$authenticatedrole->id, $studentrole->id, $emptyroleid]);
        $this->assertEquals(1 * $readsperquery, $DB->perf_get_reads() - $startdbreads);

        // Repeat call. No DB queries.
        $startdbreads = $DB->perf_get_reads();
        get_role_definitions([$authenticatedrole->id, $studentrole->id, $emptyroleid]);
        $this->assertEquals(0 * $readsperquery, $DB->perf_get_reads() - $startdbreads);

        // Alter a role.
        role_change_permission($studentrole->id, $coursecontext, 'moodle/course:tag', CAP_ALLOW);

        // Should now know to do one query.
        $startdbreads = $DB->perf_get_reads();
        get_role_definitions([$authenticatedrole->id, $studentrole->id]);
        $this->assertEquals(1 * $readsperquery, $DB->perf_get_reads() - $startdbreads);

        // Now clear the in-memory cache, and verify that it does not query the DB.
        // Cannot use accesslib_clear_all_caches_for_unit_testing since that also
        // clears the MUC cache.
        global $ACCESSLIB_PRIVATE;
        $ACCESSLIB_PRIVATE->cacheroledefs = array();

        // Get all roles. Should not need the DB.
        $startdbreads = $DB->perf_get_reads();
        get_role_definitions([$authenticatedrole->id, $studentrole->id, $emptyroleid]);
        $this->assertEquals(0 * $readsperquery, $DB->perf_get_reads() - $startdbreads);
    }

    /**
     * Tests get_user_capability_course() which checks a capability across all courses.
     *
     * @covers ::get_user_capability_course
     */
    public function test_get_user_capability_course() {
        global $CFG, $USER;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $cap = 'moodle/course:view';

        // The structure being created here is this:
        //
        // All tests work with the single capability 'moodle/course:view'.
        //
        //             ROLE DEF/OVERRIDE                        ROLE ASSIGNS
        //    Role:  Allow    Prohib    Empty   Def user      u1  u2  u3  u4   u5  u6  u7  u8
        // System    ALLOW    PROHIBIT                            A   E   A+E
        //   cat1                       ALLOW
        //     C1                               (ALLOW)                            P
        //     C2             ALLOW                                                    E   P
        //     cat2                     PREVENT
        //       C3                     ALLOW                                      E
        //       C4
        //   Misc.                                                             A
        //     C5    PREVENT                                                       A
        //     C6                       PROHIBIT
        //
        // Front-page and guest role stuff from the end of this test not included in the diagram.

        // Create a role which allows course:view and one that prohibits it, and one neither.
        $allowroleid = $generator->create_role();
        $prohibitroleid = $generator->create_role();
        $emptyroleid = $generator->create_role();
        $systemcontext = context_system::instance();
        assign_capability($cap, CAP_ALLOW, $allowroleid, $systemcontext->id);
        assign_capability($cap, CAP_PROHIBIT, $prohibitroleid, $systemcontext->id);

        // Create two categories (nested).
        $cat1 = $generator->create_category();
        $cat2 = $generator->create_category(['parent' => $cat1->id]);

        // Create six courses - two in cat1, two in cat2, and two in default category.
        // Shortnames are used for a sorting test. Otherwise they are not significant.
        $c1 = $generator->create_course(['category' => $cat1->id, 'shortname' => 'Z']);
        $c2 = $generator->create_course(['category' => $cat1->id, 'shortname' => 'Y']);
        $c3 = $generator->create_course(['category' => $cat2->id, 'shortname' => 'X']);
        $c4 = $generator->create_course(['category' => $cat2->id]);
        $c5 = $generator->create_course();
        $c6 = $generator->create_course();

        // Category overrides: in cat 1, empty role is allowed; in cat 2, empty role is prevented.
        assign_capability($cap, CAP_ALLOW, $emptyroleid,
                context_coursecat::instance($cat1->id)->id);
        assign_capability($cap, CAP_PREVENT, $emptyroleid,
                context_coursecat::instance($cat2->id)->id);

        // Course overrides: in C5, allow role is prevented; in C6, empty role is prohibited; in
        // C3, empty role is allowed.
        assign_capability($cap, CAP_PREVENT, $allowroleid,
                context_course::instance($c5->id)->id);
        assign_capability($cap, CAP_PROHIBIT, $emptyroleid,
                context_course::instance($c6->id)->id);
        assign_capability($cap, CAP_ALLOW, $emptyroleid,
                context_course::instance($c3->id)->id);
        assign_capability($cap, CAP_ALLOW, $prohibitroleid,
                context_course::instance($c2->id)->id);

        // User 1 has no roles except default user role.
        $u1 = $generator->create_user();

        // It returns false (annoyingly) if there are no courses.
        $this->assertFalse(get_user_capability_course($cap, $u1->id, true, '', 'id'));

        // Final override: in C1, default user role is allowed.
        assign_capability($cap, CAP_ALLOW, $CFG->defaultuserroleid,
                context_course::instance($c1->id)->id);

        // Should now get C1 only.
        $courses = get_user_capability_course($cap, $u1->id, true, '', 'id');
        $this->assert_course_ids([$c1->id], $courses);

        // User 2 has allow role (system wide).
        $u2 = $generator->create_user();
        role_assign($allowroleid, $u2->id, $systemcontext->id);

        // Should get everything except C5.
        $courses = get_user_capability_course($cap, $u2->id, true, '', 'id');
        $this->assert_course_ids([SITEID, $c1->id, $c2->id, $c3->id, $c4->id, $c6->id], $courses);

        // User 3 has empty role (system wide).
        $u3 = $generator->create_user();
        role_assign($emptyroleid, $u3->id, $systemcontext->id);

        // Should get cat 1 courses but not cat2, except C3.
        $courses = get_user_capability_course($cap, $u3->id, true, '', 'id');
        $this->assert_course_ids([$c1->id, $c2->id, $c3->id], $courses);

        // User 4 has allow and empty role (system wide).
        $u4 = $generator->create_user();
        role_assign($allowroleid, $u4->id, $systemcontext->id);
        role_assign($emptyroleid, $u4->id, $systemcontext->id);

        // Should get everything except C5 and C6.
        $courses = get_user_capability_course($cap, $u4->id, true, '', 'id');
        $this->assert_course_ids([SITEID, $c1->id, $c2->id, $c3->id, $c4->id], $courses);

        // User 5 has allow role in default category only.
        $u5 = $generator->create_user();
        role_assign($allowroleid, $u5->id, context_coursecat::instance($c5->category)->id);

        // Should get C1 and the default category courses but not C5.
        $courses = get_user_capability_course($cap, $u5->id, true, '', 'id');
        $this->assert_course_ids([$c1->id, $c6->id], $courses);

        // User 6 has a bunch of course roles: prohibit role in C1, empty role in C3, allow role in
        // C6.
        $u6 = $generator->create_user();
        role_assign($prohibitroleid, $u6->id, context_course::instance($c1->id)->id);
        role_assign($emptyroleid, $u6->id, context_course::instance($c3->id)->id);
        role_assign($allowroleid, $u6->id, context_course::instance($c5->id)->id);

        // Should get C3 only because the allow role is prevented in C5.
        $courses = get_user_capability_course($cap, $u6->id, true, '', 'id');
        $this->assert_course_ids([$c3->id], $courses);

        // User 7 has empty role in C2.
        $u7 = $generator->create_user();
        role_assign($emptyroleid, $u7->id, context_course::instance($c2->id)->id);

        // Should get C1 by the default user role override, and C2 by the cat1 level override.
        $courses = get_user_capability_course($cap, $u7->id, true, '', 'id');
        $this->assert_course_ids([$c1->id, $c2->id], $courses);

        // User 8 has prohibit role as system context, to verify that prohibits can't be overridden.
        $u8 = $generator->create_user();
        role_assign($prohibitroleid, $u8->id, context_course::instance($c2->id)->id);

        // Should get C1 by the default user role override, no other courses because the prohibit cannot be overridden.
        $courses = get_user_capability_course($cap, $u8->id, true, '', 'id');
        $this->assert_course_ids([$c1->id], $courses);

        // Admin user gets everything....
        $courses = get_user_capability_course($cap, get_admin()->id, true, '', 'id');
        $this->assert_course_ids([SITEID, $c1->id, $c2->id, $c3->id, $c4->id, $c5->id, $c6->id],
                $courses);

        // Unless you turn off doanything, when it only has the things a user with no role does.
        $courses = get_user_capability_course($cap, get_admin()->id, false, '', 'id');
        $this->assert_course_ids([$c1->id], $courses);

        // Using u3 as an example, test the limit feature.
        $courses = get_user_capability_course($cap, $u3->id, true, '', 'id', 2);
        $this->assert_course_ids([$c1->id, $c2->id], $courses);

        // Check sorting.
        $courses = get_user_capability_course($cap, $u3->id, true, '', 'shortname');
        $this->assert_course_ids([$c3->id, $c2->id, $c1->id], $courses);

        // Check returned fields - default.
        $courses = get_user_capability_course($cap, $u3->id, true, '', 'id');
        $this->assertEquals((object)['id' => $c1->id], $courses[0]);

        // Add a selection of fields, including the context ones with special handling.
        $courses = get_user_capability_course($cap, $u3->id, true, 'shortname, ctxlevel, ctxdepth, ctxinstance', 'id');
        $this->assertEquals((object)['id' => $c1->id, 'shortname' => 'Z', 'ctxlevel' => 50,
                'ctxdepth' => 3, 'ctxinstance' => $c1->id], $courses[0]);

        // Test front page role - user 1 has no roles, but if we change the front page role
        // definition so that it has our capability, then they should see the front page course.
        // as well as C1.
        assign_capability($cap, CAP_ALLOW, $CFG->defaultfrontpageroleid, $systemcontext->id);
        $courses = get_user_capability_course($cap, $u1->id, true, '', 'id');
        $this->assert_course_ids([SITEID, $c1->id], $courses);

        // Check that temporary guest access (in this case, given on course 2 for user 1)
        // also is included, if it has this capability.
        assign_capability($cap, CAP_ALLOW, $CFG->guestroleid, $systemcontext->id);
        $this->setUser($u1);
        load_temp_course_role(context_course::instance($c2->id), $CFG->guestroleid);
        $courses = get_user_capability_course($cap, $USER->id, true, '', 'id');
        $this->assert_course_ids([SITEID, $c1->id, $c2->id], $courses);
    }

    /**
     * Tests get_user_capability_contexts() which checks a capability across all courses and categories.
     * Testing for categories only because courses results are covered by test_get_user_capability_course.
     *
     * @covers ::get_user_capability_contexts
     */
    public function test_get_user_capability_contexts() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $cap = 'moodle/contentbank:access';
        $defaultcategoryid = 1;

//         The structure being created here is this:
//
//         All tests work with the single capability 'moodle/contentbank:access'.
//         ROLE DEF/OVERRIDE                                                    .
//         Role:                Allow       Prohibit        Empty               .
//                  System      ALLOW       PROHIBIT                            .
//                  cat1        PREVENT     ALLOW           ALLOW               .
//                      cat3    ALLOW       PROHIBIT                            .
//                 cat2        PROHIBIT    PROHIBIT        PROHIBIT             .

        // Create a role which allows contentbank:access and one that prohibits it, and one neither.
        $allowroleid = $generator->create_role();
        $prohibitroleid = $generator->create_role();
        $emptyroleid = $generator->create_role();
        $systemcontext = context_system::instance();
        assign_capability($cap, CAP_ALLOW, $allowroleid, $systemcontext->id);
        assign_capability($cap, CAP_PROHIBIT, $prohibitroleid, $systemcontext->id);

        // Create three categories (two of them nested).
        $cat1 = $generator->create_category(['name' => 'Aardvarks']);
        $cat2 = $generator->create_category(['name' => 'Badgers']);
        $cat3 = $generator->create_category(['parent' => $cat1->id, 'name' => 'Cheetahs']);

        // Category overrides: in cat 1, empty role is allowed; in cat 2, empty role is prevented.
        assign_capability($cap, CAP_ALLOW, $emptyroleid,
            context_coursecat::instance($cat1->id)->id);
        assign_capability($cap, CAP_PREVENT, $emptyroleid,
            context_coursecat::instance($cat2->id)->id);

        // Course category overrides: in cat1, allow role is prevented and prohibit role is allowed;
        // in Cat2, allow role is prohibited.
        assign_capability($cap, CAP_PREVENT, $allowroleid,
            context_coursecat::instance($cat1->id)->id);
        assign_capability($cap, CAP_ALLOW, $prohibitroleid,
            context_coursecat::instance($cat1->id)->id);
        assign_capability($cap, CAP_PROHIBIT, $allowroleid,
            context_coursecat::instance($cat2->id)->id);

        // User 1 has no roles except default user role.
        $u1 = $generator->create_user();

        // It returns false (annoyingly) if there are no course categories.
        list($categories, $courses) = get_user_capability_contexts($cap, true, $u1->id);
        $this->assertFalse($categories);

        // User 2 has allow role (system wide).
        $u2 = $generator->create_user();
        role_assign($allowroleid, $u2->id, $systemcontext->id);

        // Should get $defaultcategory only. cat2 is prohibited; cat1 is prevented, so cat3 is not allowed.
        list($categories, $courses) = get_user_capability_contexts($cap, true, $u2->id);
        // Using same assert_course_ids helper even when we are checking course category ids.
        $this->assert_course_ids([$defaultcategoryid], $categories);

        // User 3 has empty role (system wide).
        $u3 = $generator->create_user();
        role_assign($emptyroleid, $u3->id, $systemcontext->id);

        // Should get cat1 and cat3. cat2 is prohibited; no access to system level. Sorted by category name.
        list($categories, $courses) = get_user_capability_contexts($cap, true, $u3->id, true, '', '', '', 'name');
        $this->assert_course_ids([$cat1->id, $cat3->id], $categories);

        // User 4 has prohibit role (system wide).
        $u4 = $generator->create_user();
        role_assign($prohibitroleid, $u4->id, $systemcontext->id);

        // Should not get any, because all of them are prohibited at system level.
        // Even if we try to allow an specific category.
        list($categories, $courses) = get_user_capability_contexts($cap, true, $u4->id);
        $this->assertFalse($categories);
    }

    /**
     * Extracts an array of course ids to make the above test script shorter.
     *
     * @param int[] $expected Array of expected course ids
     * @param stdClass[] $courses Array of course objects
     */
    protected function assert_course_ids(array $expected, array $courses) {
        $courseids = array_map(function($c) {
            return $c->id;
        }, $courses);
        $this->assertEquals($expected, $courseids);
    }

    /**
     * Test if course creator future capability lookup works.
     *
     * @covers ::guess_if_creator_will_have_course_capability
     * @covers ::has_capability
     */
    public function test_guess_if_creator_will_have_course_capability() {
        global $DB, $CFG, $USER;

        $this->resetAfterTest();

        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(array('category'=>$category->id));

        $syscontext = context_system::instance();
        $categorycontext = context_coursecat::instance($category->id);
        $coursecontext = context_course::instance($course->id);
        $studentrole = $DB->get_record('role', array('shortname'=>'student'), '*', MUST_EXIST);
        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $creatorrole = $DB->get_record('role', array('shortname'=>'coursecreator'), '*', MUST_EXIST);
        $managerrole = $DB->get_record('role', array('shortname'=>'manager'), '*', MUST_EXIST);

        $this->assertEquals($teacherrole->id, $CFG->creatornewroleid);

        $creator = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();
        role_assign($managerrole->id, $manager->id, $categorycontext);

        $this->assertFalse(has_capability('moodle/course:view', $categorycontext, $creator));
        $this->assertFalse(has_capability('moodle/role:assign', $categorycontext, $creator));
        $this->assertFalse(has_capability('moodle/course:visibility', $categorycontext, $creator));
        $this->assertFalse(has_capability('moodle/course:visibility', $coursecontext, $creator));
        $this->assertFalse(guess_if_creator_will_have_course_capability('moodle/course:visibility', $categorycontext, $creator));
        $this->assertFalse(guess_if_creator_will_have_course_capability('moodle/course:visibility', $coursecontext, $creator));

        $this->assertTrue(has_capability('moodle/role:assign', $categorycontext, $manager));
        $this->assertTrue(has_capability('moodle/course:visibility', $categorycontext, $manager));
        $this->assertTrue(has_capability('moodle/course:visibility', $coursecontext, $manager));
        $this->assertTrue(guess_if_creator_will_have_course_capability('moodle/course:visibility', $categorycontext, $manager->id));
        $this->assertTrue(guess_if_creator_will_have_course_capability('moodle/course:visibility', $coursecontext, $manager->id));

        $this->assertEquals(0, $USER->id);
        $this->assertFalse(has_capability('moodle/course:view', $categorycontext));
        $this->assertFalse(has_capability('moodle/role:assign', $categorycontext));
        $this->assertFalse(has_capability('moodle/course:visibility', $categorycontext));
        $this->assertFalse(has_capability('moodle/course:visibility', $coursecontext));
        $this->assertFalse(guess_if_creator_will_have_course_capability('moodle/course:visibility', $categorycontext));
        $this->assertFalse(guess_if_creator_will_have_course_capability('moodle/course:visibility', $coursecontext));

        $this->setUser($manager);
        $this->assertTrue(has_capability('moodle/role:assign', $categorycontext));
        $this->assertTrue(has_capability('moodle/course:visibility', $categorycontext));
        $this->assertTrue(has_capability('moodle/course:visibility', $coursecontext));
        $this->assertTrue(guess_if_creator_will_have_course_capability('moodle/course:visibility', $categorycontext));
        $this->assertTrue(guess_if_creator_will_have_course_capability('moodle/course:visibility', $coursecontext));

        $this->setAdminUser();
        $this->assertTrue(has_capability('moodle/role:assign', $categorycontext));
        $this->assertTrue(has_capability('moodle/course:visibility', $categorycontext));
        $this->assertTrue(has_capability('moodle/course:visibility', $coursecontext));
        $this->assertTrue(guess_if_creator_will_have_course_capability('moodle/course:visibility', $categorycontext));
        $this->assertTrue(guess_if_creator_will_have_course_capability('moodle/course:visibility', $coursecontext));
        $this->setUser(0);

        role_assign($creatorrole->id, $creator->id, $categorycontext);

        $this->assertFalse(has_capability('moodle/role:assign', $categorycontext, $creator));
        $this->assertFalse(has_capability('moodle/course:visibility', $categorycontext, $creator));
        $this->assertFalse(has_capability('moodle/course:visibility', $coursecontext, $creator));
        $this->assertTrue(guess_if_creator_will_have_course_capability('moodle/course:visibility', $categorycontext, $creator));
        $this->assertTrue(guess_if_creator_will_have_course_capability('moodle/course:visibility', $coursecontext, $creator));

        $this->setUser($creator);
        $this->assertFalse(has_capability('moodle/role:assign', $categorycontext, null));
        $this->assertFalse(has_capability('moodle/course:visibility', $categorycontext, null));
        $this->assertFalse(has_capability('moodle/course:visibility', $coursecontext, null));
        $this->assertTrue(guess_if_creator_will_have_course_capability('moodle/course:visibility', $categorycontext, null));
        $this->assertTrue(guess_if_creator_will_have_course_capability('moodle/course:visibility', $coursecontext, null));
        $this->setUser(0);

        set_config('creatornewroleid', $studentrole->id);

        $this->assertFalse(has_capability('moodle/course:visibility', $categorycontext, $creator));
        $this->assertFalse(has_capability('moodle/course:visibility', $coursecontext, $creator));
        $this->assertFalse(guess_if_creator_will_have_course_capability('moodle/course:visibility', $categorycontext, $creator));
        $this->assertFalse(guess_if_creator_will_have_course_capability('moodle/course:visibility', $coursecontext, $creator));

        set_config('creatornewroleid', $teacherrole->id);

        role_change_permission($managerrole->id, $categorycontext, 'moodle/course:visibility', CAP_PREVENT);
        role_assign($creatorrole->id, $manager->id, $categorycontext);

        $this->assertTrue(has_capability('moodle/course:view', $categorycontext, $manager));
        $this->assertTrue(has_capability('moodle/course:view', $coursecontext, $manager));
        $this->assertTrue(has_capability('moodle/role:assign', $categorycontext, $manager));
        $this->assertTrue(has_capability('moodle/role:assign', $coursecontext, $manager));
        $this->assertFalse(has_capability('moodle/course:visibility', $categorycontext, $manager));
        $this->assertFalse(has_capability('moodle/course:visibility', $coursecontext, $manager));
        $this->assertFalse(guess_if_creator_will_have_course_capability('moodle/course:visibility', $categorycontext, $manager));
        $this->assertFalse(guess_if_creator_will_have_course_capability('moodle/course:visibility', $coursecontext, $manager));

        role_change_permission($managerrole->id, $categorycontext, 'moodle/course:view', CAP_PREVENT);
        $this->assertTrue(has_capability('moodle/role:assign', $categorycontext, $manager));
        $this->assertFalse(has_capability('moodle/course:visibility', $categorycontext, $manager));
        $this->assertFalse(has_capability('moodle/course:visibility', $coursecontext, $manager));
        $this->assertTrue(guess_if_creator_will_have_course_capability('moodle/course:visibility', $categorycontext, $manager));
        $this->assertTrue(guess_if_creator_will_have_course_capability('moodle/course:visibility', $coursecontext, $manager));

        $this->getDataGenerator()->enrol_user($manager->id, $course->id, 0);

        $this->assertTrue(has_capability('moodle/role:assign', $categorycontext, $manager));
        $this->assertTrue(has_capability('moodle/role:assign', $coursecontext, $manager));
        $this->assertTrue(is_enrolled($coursecontext, $manager));
        $this->assertFalse(has_capability('moodle/course:visibility', $categorycontext, $manager));
        $this->assertFalse(has_capability('moodle/course:visibility', $coursecontext, $manager));
        $this->assertTrue(guess_if_creator_will_have_course_capability('moodle/course:visibility', $categorycontext, $manager));
        $this->assertFalse(guess_if_creator_will_have_course_capability('moodle/course:visibility', $coursecontext, $manager));

        // Test problems.

        try {
            guess_if_creator_will_have_course_capability('moodle/course:visibility', $syscontext, $creator);
            $this->fail('Exception expected when non course/category context passed to guess_if_creator_will_have_course_capability()');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    /**
     * Test require_capability() exceptions.
     *
     * @covers ::require_capability
     */
    public function test_require_capability() {
        $this->resetAfterTest();

        $syscontext = context_system::instance();

        $this->setUser(0);
        $this->assertFalse(has_capability('moodle/site:config', $syscontext));
        try {
            require_capability('moodle/site:config', $syscontext);
            $this->fail('Exception expected from require_capability()');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('required_capability_exception', $e);
        }
        $this->setAdminUser();
        $this->assertFalse(has_capability('moodle/site:config', $syscontext, 0));
        try {
            require_capability('moodle/site:config', $syscontext, 0);
            $this->fail('Exception expected from require_capability()');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('required_capability_exception', $e);
        }
        $this->assertFalse(has_capability('moodle/site:config', $syscontext, null, false));
        try {
            require_capability('moodle/site:config', $syscontext, null, false);
            $this->fail('Exception expected from require_capability()');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('required_capability_exception', $e);
        }
    }

    /**
     * Test that enrolled users SQL does not return any values for users in
     * other courses.
     *
     * @covers ::get_enrolled_users
     * @covers ::get_suspended_userids
     */
    public function test_get_enrolled_sql_different_course() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $student = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $user = $this->getDataGenerator()->create_user();

        // This user should not appear anywhere, we're not interested in that context.
        $course2 = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course2->id, $student->id);

        $enrolled   = get_enrolled_users($context, '', 0, 'u.id', null, 0, 0, false);
        $active     = get_enrolled_users($context, '', 0, 'u.id', null, 0, 0, true);
        $suspended  = get_suspended_userids($context);

        $this->assertFalse(isset($enrolled[$user->id]));
        $this->assertFalse(isset($active[$user->id]));
        $this->assertFalse(isset($suspended[$user->id]));
        $this->assertCount(0, $enrolled);
        $this->assertCount(0, $active);
        $this->assertCount(0, $suspended);
    }

    /**
     * Test that enrolled users SQL does not return any values for role
     * assignments without an enrolment.
     *
     * @covers ::get_enrolled_users
     * @covers ::get_suspended_userids
     */
    public function test_get_enrolled_sql_role_only() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $student = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $user = $this->getDataGenerator()->create_user();

        // Role assignment is not the same as course enrollment.
        role_assign($student->id, $user->id, $context->id);

        $enrolled   = get_enrolled_users($context, '', 0, 'u.id', null, 0, 0, false);
        $active     = get_enrolled_users($context, '', 0, 'u.id', null, 0, 0, true);
        $suspended  = get_suspended_userids($context);

        $this->assertFalse(isset($enrolled[$user->id]));
        $this->assertFalse(isset($active[$user->id]));
        $this->assertFalse(isset($suspended[$user->id]));
        $this->assertCount(0, $enrolled);
        $this->assertCount(0, $active);
        $this->assertCount(0, $suspended);
    }

    /**
     * Test that multiple enrolments for the same user are counted correctly.
     *
     * @covers ::get_enrolled_users
     * @covers ::get_suspended_userids
     */
    public function test_get_enrolled_sql_multiple_enrolments() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $student = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $user = $this->getDataGenerator()->create_user();

        // Add a suspended enrol.
        $selfinstance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'self'));
        $selfplugin = enrol_get_plugin('self');
        $selfplugin->update_status($selfinstance, ENROL_INSTANCE_ENABLED);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $student->id, 'self', 0, 0, ENROL_USER_SUSPENDED);

        // Should be enrolled, but not active - user is suspended.
        $enrolled   = get_enrolled_users($context, '', 0, 'u.id', null, 0, 0, false);
        $active     = get_enrolled_users($context, '', 0, 'u.id', null, 0, 0, true);
        $suspended  = get_suspended_userids($context);

        $this->assertTrue(isset($enrolled[$user->id]));
        $this->assertFalse(isset($active[$user->id]));
        $this->assertTrue(isset($suspended[$user->id]));
        $this->assertCount(1, $enrolled);
        $this->assertCount(0, $active);
        $this->assertCount(1, $suspended);

        // Add an active enrol for the user. Any active enrol makes them enrolled.
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $student->id);

        // User should be active now.
        $enrolled   = get_enrolled_users($context, '', 0, 'u.id', null, 0, 0, false);
        $active     = get_enrolled_users($context, '', 0, 'u.id', null, 0, 0, true);
        $suspended  = get_suspended_userids($context);

        $this->assertTrue(isset($enrolled[$user->id]));
        $this->assertTrue(isset($active[$user->id]));
        $this->assertFalse(isset($suspended[$user->id]));
        $this->assertCount(1, $enrolled);
        $this->assertCount(1, $active);
        $this->assertCount(0, $suspended);

    }

    /**
     * Test that enrolled users SQL does not return any values for users
     * without a group when $context is not a valid course context.
     *
     * @covers ::get_enrolled_users
     */
    public function test_get_enrolled_sql_userswithoutgroup() {
        global $DB;

        $this->resetAfterTest();

        $systemcontext = context_system::instance();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);

        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        groups_add_member($group, $user1);

        $enrolled   = get_enrolled_users($coursecontext);
        $this->assertCount(2, $enrolled);

        // Get users without any group on the course context.
        $enrolledwithoutgroup = get_enrolled_users($coursecontext, '', USERSWITHOUTGROUP);
        $this->assertCount(1, $enrolledwithoutgroup);
        $this->assertFalse(isset($enrolledwithoutgroup[$user1->id]));

        // Get users without any group on the system context (it should throw an exception).
        $this->expectException('coding_exception');
        get_enrolled_users($systemcontext, '', USERSWITHOUTGROUP);
    }

    public function get_enrolled_sql_provider() {
        return array(
            array(
                // Two users who are enrolled.
                'users' => array(
                    array(
                        'enrolled'  => true,
                        'active'    => true,
                    ),
                    array(
                        'enrolled'  => true,
                        'active'    => true,
                    ),
                ),
                'counts' => array(
                    'enrolled'      => 2,
                    'active'        => 2,
                    'suspended'     => 0,
                ),
            ),
            array(
                // A user who is suspended.
                'users' => array(
                    array(
                        'status'    => ENROL_USER_SUSPENDED,
                        'enrolled'  => true,
                        'suspended' => true,
                    ),
                ),
                'counts' => array(
                    'enrolled'      => 1,
                    'active'        => 0,
                    'suspended'     => 1,
                ),
            ),
            array(
                // One of each.
                'users' => array(
                    array(
                        'enrolled'  => true,
                        'active'    => true,
                    ),
                    array(
                        'status'    => ENROL_USER_SUSPENDED,
                        'enrolled'  => true,
                        'suspended' => true,
                    ),
                ),
                'counts' => array(
                    'enrolled'      => 2,
                    'active'        => 1,
                    'suspended'     => 1,
                ),
            ),
            array(
                // One user who is not yet enrolled.
                'users' => array(
                    array(
                        'timestart' => DAYSECS,
                        'enrolled'  => true,
                        'active'    => false,
                        'suspended' => true,
                    ),
                ),
                'counts' => array(
                    'enrolled'      => 1,
                    'active'        => 0,
                    'suspended'     => 1,
                ),
            ),
            array(
                // One user who is no longer enrolled
                'users' => array(
                    array(
                        'timeend'   => -DAYSECS,
                        'enrolled'  => true,
                        'active'    => false,
                        'suspended' => true,
                    ),
                ),
                'counts' => array(
                    'enrolled'      => 1,
                    'active'        => 0,
                    'suspended'     => 1,
                ),
            ),
            array(
                // One user who is not yet enrolled, and one who is no longer enrolled.
                'users' => array(
                    array(
                        'timeend'   => -DAYSECS,
                        'enrolled'  => true,
                        'active'    => false,
                        'suspended' => true,
                    ),
                    array(
                        'timestart' => DAYSECS,
                        'enrolled'  => true,
                        'active'    => false,
                        'suspended' => true,
                    ),
                ),
                'counts' => array(
                    'enrolled'      => 2,
                    'active'        => 0,
                    'suspended'     => 2,
                ),
            ),
        );
    }

    /**
     * @dataProvider get_enrolled_sql_provider
     * @covers ::get_enrolled_users
     * @covers ::get_suspended_userids
     */
    public function test_get_enrolled_sql_course($users, $counts) {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $student = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $createdusers = array();

        foreach ($users as &$userdata) {
            $user = $this->getDataGenerator()->create_user();
            $userdata['id'] = $user->id;

            $timestart  = 0;
            $timeend    = 0;
            $status     = null;
            if (isset($userdata['timestart'])) {
                $timestart = time() + $userdata['timestart'];
            }
            if (isset($userdata['timeend'])) {
                $timeend = time() + $userdata['timeend'];
            }
            if (isset($userdata['status'])) {
                $status = $userdata['status'];
            }

            // Enrol the user in the course.
            $this->getDataGenerator()->enrol_user($user->id, $course->id, $student->id, 'manual', $timestart, $timeend, $status);
        }

        // After all users have been enroled, check expectations.
        $enrolled   = get_enrolled_users($context, '', 0, 'u.id', null, 0, 0, false);
        $active     = get_enrolled_users($context, '', 0, 'u.id', null, 0, 0, true);
        $suspended  = get_suspended_userids($context);

        foreach ($users as $userdata) {
            if (isset($userdata['enrolled']) && $userdata['enrolled']) {
                $this->assertTrue(isset($enrolled[$userdata['id']]));
            } else {
                $this->assertFalse(isset($enrolled[$userdata['id']]));
            }

            if (isset($userdata['active']) && $userdata['active']) {
                $this->assertTrue(isset($active[$userdata['id']]));
            } else {
                $this->assertFalse(isset($active[$userdata['id']]));
            }

            if (isset($userdata['suspended']) && $userdata['suspended']) {
                $this->assertTrue(isset($suspended[$userdata['id']]));
            } else {
                $this->assertFalse(isset($suspended[$userdata['id']]));
            }
        }

        $this->assertCount($counts['enrolled'],     $enrolled);
        $this->assertCount($counts['active'],       $active);
        $this->assertCount($counts['suspended'],    $suspended);
    }

    /**
     * A small functional test of permission evaluations.
     */
    public function test_permission_evaluation() {
        global $USER, $SITE, $CFG, $DB, $ACCESSLIB_PRIVATE;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Fill the site with some real data.
        $testcategories = array();
        $testcourses = array();
        $testpages = array();
        $testblocks = array();
        $allroles = $DB->get_records_menu('role', array(), 'id', 'shortname, id');

        $systemcontext = context_system::instance();
        $frontpagecontext = context_course::instance(SITEID);

        // Add block to system context.
        $bi = $generator->create_block('online_users');
        context_block::instance($bi->id);
        $testblocks[] = $bi->id;

        // Some users.
        $testusers = array();
        for ($i=0; $i<20; $i++) {
            $user = $generator->create_user();
            $testusers[$i] = $user->id;
            $usercontext = context_user::instance($user->id);

            // Add block to user profile.
            $bi = $generator->create_block('online_users', array('parentcontextid'=>$usercontext->id));
            $testblocks[] = $bi->id;
        }

        // Add block to frontpage.
        $bi = $generator->create_block('online_users', array('parentcontextid'=>$frontpagecontext->id));
        $frontpageblockcontext = context_block::instance($bi->id);
        $testblocks[] = $bi->id;

        // Add a resource to frontpage.
        $page = $generator->create_module('page', array('course'=>$SITE->id));
        $testpages[] = $page->cmid;
        $frontpagepagecontext = context_module::instance($page->cmid);

        // Add block to frontpage resource.
        $bi = $generator->create_block('online_users', array('parentcontextid'=>$frontpagepagecontext->id));
        $frontpagepageblockcontext = context_block::instance($bi->id);
        $testblocks[] = $bi->id;

        // Some nested course categories with courses.
        $manualenrol = enrol_get_plugin('manual');
        $parentcat = 0;
        for ($i=0; $i<5; $i++) {
            $cat = $generator->create_category(array('parent'=>$parentcat));
            $testcategories[] = $cat->id;
            $catcontext = context_coursecat::instance($cat->id);
            $parentcat = $cat->id;

            if ($i >= 4) {
                continue;
            }

            // Add resource to each category.
            $bi = $generator->create_block('online_users', array('parentcontextid'=>$catcontext->id));
            context_block::instance($bi->id);

            // Add a few courses to each category.
            for ($j=0; $j<6; $j++) {
                $course = $generator->create_course(array('category'=>$cat->id));
                $testcourses[] = $course->id;
                $coursecontext = context_course::instance($course->id);

                if ($j >= 5) {
                    continue;
                }
                // Add manual enrol instance.
                $manualenrol->add_default_instance($DB->get_record('course', array('id'=>$course->id)));

                // Add block to each course.
                $bi = $generator->create_block('online_users', array('parentcontextid'=>$coursecontext->id));
                $testblocks[] = $bi->id;

                // Add a resource to each course.
                $page = $generator->create_module('page', array('course'=>$course->id));
                $testpages[] = $page->cmid;
                $modcontext = context_module::instance($page->cmid);

                // Add block to each module.
                $bi = $generator->create_block('online_users', array('parentcontextid'=>$modcontext->id));
                $testblocks[] = $bi->id;
            }
        }

        // Make sure all contexts were created properly.
        $count = 1; // System.
        $count += $DB->count_records('user', array('deleted'=>0));
        $count += $DB->count_records('course_categories');
        $count += $DB->count_records('course');
        $count += $DB->count_records('course_modules');
        $count += $DB->count_records('block_instances');
        $this->assertEquals($count, $DB->count_records('context'));
        $this->assertEquals(0, $DB->count_records('context', array('depth'=>0)));
        $this->assertEquals(0, $DB->count_records('context', array('path'=>null)));


        // Test context_helper::get_level_name() method.

        $levels = context_helper::get_all_levels();
        foreach ($levels as $level => $classname) {
            $name = context_helper::get_level_name($level);
            $this->assertNotEmpty($name);
        }


        // Test context::instance_by_id(), context_xxx::instance() methods.

        $context = context::instance_by_id($frontpagecontext->id);
        $this->assertSame(CONTEXT_COURSE, $context->contextlevel);
        $this->assertFalse(context::instance_by_id(-1, IGNORE_MISSING));
        try {
            context::instance_by_id(-1);
            $this->fail('exception expected');
        } catch (moodle_exception $e) {
            $this->assertTrue(true);
        }
        $this->assertInstanceOf('context_system', context_system::instance());
        $this->assertInstanceOf('context_coursecat', context_coursecat::instance($testcategories[0]));
        $this->assertInstanceOf('context_course', context_course::instance($testcourses[0]));
        $this->assertInstanceOf('context_module', context_module::instance($testpages[0]));
        $this->assertInstanceOf('context_block', context_block::instance($testblocks[0]));

        $this->assertFalse(context_coursecat::instance(-1, IGNORE_MISSING));
        $this->assertFalse(context_course::instance(-1, IGNORE_MISSING));
        $this->assertFalse(context_module::instance(-1, IGNORE_MISSING));
        $this->assertFalse(context_block::instance(-1, IGNORE_MISSING));
        try {
            context_coursecat::instance(-1);
            $this->fail('exception expected');
        } catch (moodle_exception $e) {
            $this->assertTrue(true);
        }
        try {
            context_course::instance(-1);
            $this->fail('exception expected');
        } catch (moodle_exception $e) {
            $this->assertTrue(true);
        }
        try {
            context_module::instance(-1);
            $this->fail('exception expected');
        } catch (moodle_exception $e) {
            $this->assertTrue(true);
        }
        try {
            context_block::instance(-1);
            $this->fail('exception expected');
        } catch (moodle_exception $e) {
            $this->assertTrue(true);
        }


        // Test $context->get_url(), $context->get_context_name(), $context->get_capabilities() methods.

        $testcontexts = array();
        $testcontexts[CONTEXT_SYSTEM]    = context_system::instance();
        $testcontexts[CONTEXT_COURSECAT] = context_coursecat::instance($testcategories[0]);
        $testcontexts[CONTEXT_COURSE]    = context_course::instance($testcourses[0]);
        $testcontexts[CONTEXT_MODULE]    = context_module::instance($testpages[0]);
        $testcontexts[CONTEXT_BLOCK]     = context_block::instance($testblocks[0]);

        foreach ($testcontexts as $context) {
            $name = $context->get_context_name(true, true);
            $this->assertNotEmpty($name);

            $this->assertInstanceOf('moodle_url', $context->get_url());

            $caps = $context->get_capabilities();
            $this->assertTrue(is_array($caps));
            foreach ($caps as $cap) {
                $cap = (array)$cap;
                $this->assertSame(array_keys($cap), array('id', 'name', 'captype', 'contextlevel', 'component', 'riskbitmask'));
            }
        }
        unset($testcontexts);

        // Test $context->get_course_context() method.

        $this->assertFalse($systemcontext->get_course_context(false));
        try {
            $systemcontext->get_course_context();
            $this->fail('exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        $context = context_coursecat::instance($testcategories[0]);
        $this->assertFalse($context->get_course_context(false));
        try {
            $context->get_course_context();
            $this->fail('exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        $this->assertEquals($frontpagecontext, $frontpagecontext->get_course_context(true));
        $this->assertEquals($frontpagecontext, $frontpagepagecontext->get_course_context(true));
        $this->assertEquals($frontpagecontext, $frontpagepageblockcontext->get_course_context(true));


        // Test $context->get_parent_context(), $context->get_parent_contexts(), $context->get_parent_context_ids() methods.

        $userid = reset($testusers);
        $usercontext = context_user::instance($userid);
        $this->assertEquals($systemcontext, $usercontext->get_parent_context());
        $this->assertEquals(array($systemcontext->id=>$systemcontext), $usercontext->get_parent_contexts());
        $this->assertEquals(array($usercontext->id=>$usercontext, $systemcontext->id=>$systemcontext), $usercontext->get_parent_contexts(true));

        $this->assertEquals(array(), $systemcontext->get_parent_contexts());
        $this->assertEquals(array($systemcontext->id=>$systemcontext), $systemcontext->get_parent_contexts(true));
        $this->assertEquals(array(), $systemcontext->get_parent_context_ids());
        $this->assertEquals(array($systemcontext->id), $systemcontext->get_parent_context_ids(true));
        $this->assertEquals(array(), $systemcontext->get_parent_context_paths());
        $this->assertEquals(array($systemcontext->id => $systemcontext->path), $systemcontext->get_parent_context_paths(true));

        $this->assertEquals($systemcontext, $frontpagecontext->get_parent_context());
        $this->assertEquals(array($systemcontext->id=>$systemcontext), $frontpagecontext->get_parent_contexts());
        $this->assertEquals(array($frontpagecontext->id=>$frontpagecontext, $systemcontext->id=>$systemcontext), $frontpagecontext->get_parent_contexts(true));
        $this->assertEquals(array($systemcontext->id), $frontpagecontext->get_parent_context_ids());
        $this->assertEquals(array($frontpagecontext->id, $systemcontext->id), $frontpagecontext->get_parent_context_ids(true));
        $this->assertEquals(array($systemcontext->id => $systemcontext->path), $frontpagecontext->get_parent_context_paths());
        $expected = array($systemcontext->id => $systemcontext->path, $frontpagecontext->id => $frontpagecontext->path);
        $this->assertEquals($expected, $frontpagecontext->get_parent_context_paths(true));

        $this->assertFalse($systemcontext->get_parent_context());
        $frontpagecontext = context_course::instance($SITE->id);
        $parent = $systemcontext;
        foreach ($testcategories as $catid) {
            $catcontext = context_coursecat::instance($catid);
            $this->assertEquals($parent, $catcontext->get_parent_context());
            $parent = $catcontext;
        }
        $this->assertEquals($frontpagecontext, $frontpagepagecontext->get_parent_context());
        $this->assertEquals($frontpagecontext, $frontpageblockcontext->get_parent_context());
        $this->assertEquals($frontpagepagecontext, $frontpagepageblockcontext->get_parent_context());


        // Test $context->get_child_contexts() method.

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
        $this->assertCount(8, $children);
        $this->assertEquals(1, $countcats);
        $this->assertEquals(6, $countcourses);
        $this->assertEquals(1, $countblocks);

        $context = context_course::instance($testcourses[2]);
        $children = $context->get_child_contexts();

        $context = context_module::instance($testpages[3]);
        $children = $context->get_child_contexts();
        $this->assertCount(1, $children);

        $context = context_block::instance($testblocks[1]);
        $children = $context->get_child_contexts();
        $this->assertCount(0, $children);

        unset($children);
        unset($countcats);
        unset($countcourses);
        unset($countblocks);


        // Test context_helper::reset_caches() method.

        context_helper::reset_caches();
        $this->assertEquals(0, context_inspection::test_context_cache_size());
        context_course::instance($SITE->id);
        $this->assertEquals(1, context_inspection::test_context_cache_size());


        // Test context preloading.

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
            $this->assertEquals(new stdClass(), $record);
        }
        $this->assertEquals(count($records), context_inspection::test_context_cache_size());
        unset($records);
        unset($columns);

        context_helper::reset_caches();
        context_helper::preload_course($SITE->id);
        $numfrontpagemodules = $DB->count_records('course_modules', array('course' => $SITE->id));
        $this->assertEquals(3 + $numfrontpagemodules, context_inspection::test_context_cache_size()); // Depends on number of default blocks.

        // Test assign_capability(), unassign_capability() functions.

        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertFalse($rc);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $allroles['teacher'], $frontpagecontext->id);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertEquals(CAP_ALLOW, $rc->permission);
        assign_capability('moodle/site:accessallgroups', CAP_PREVENT, $allroles['teacher'], $frontpagecontext->id);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertEquals(CAP_ALLOW, $rc->permission);
        assign_capability('moodle/site:accessallgroups', CAP_PREVENT, $allroles['teacher'], $frontpagecontext, true);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertEquals(CAP_PREVENT, $rc->permission);

        assign_capability('moodle/site:accessallgroups', CAP_INHERIT, $allroles['teacher'], $frontpagecontext);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertFalse($rc);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $allroles['teacher'], $frontpagecontext);
        unassign_capability('moodle/site:accessallgroups', $allroles['teacher'], $frontpagecontext, true);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertFalse($rc);
        unassign_capability('moodle/site:accessallgroups', $allroles['teacher'], $frontpagecontext->id, true);
        unset($rc);

        accesslib_clear_all_caches_for_unit_testing(); // Must be done after assign_capability().


        // Test role_assign(), role_unassign(), role_unassign_all() functions.

        $context = context_course::instance($testcourses[1]);
        $this->assertEquals(0, $DB->count_records('role_assignments', array('contextid'=>$context->id)));
        role_assign($allroles['teacher'], $testusers[1], $context->id);
        role_assign($allroles['teacher'], $testusers[2], $context->id);
        role_assign($allroles['manager'], $testusers[1], $context->id);
        $this->assertEquals(3, $DB->count_records('role_assignments', array('contextid'=>$context->id)));
        role_unassign($allroles['teacher'], $testusers[1], $context->id);
        $this->assertEquals(2, $DB->count_records('role_assignments', array('contextid'=>$context->id)));
        role_unassign_all(array('contextid'=>$context->id));
        $this->assertEquals(0, $DB->count_records('role_assignments', array('contextid'=>$context->id)));
        unset($context);

        accesslib_clear_all_caches_for_unit_testing(); // Just in case.


        // Test has_capability(), get_users_by_capability(), role_switch(), reload_all_capabilities() and friends functions.

        $adminid = get_admin()->id;
        $guestid = $CFG->siteguest;

        // Enrol some users into some courses.
        $course1 = $DB->get_record('course', array('id'=>$testcourses[22]), '*', MUST_EXIST);
        $course2 = $DB->get_record('course', array('id'=>$testcourses[7]), '*', MUST_EXIST);
        $cms = $DB->get_records('course_modules', array('course'=>$course1->id), 'id');
        $cm1 = reset($cms);
        $blocks = $DB->get_records('block_instances', array('parentcontextid'=>context_module::instance($cm1->id)->id), 'id');
        $block1 = reset($blocks);
        $instance1 = $DB->get_record('enrol', array('enrol'=>'manual', 'courseid'=>$course1->id));
        $instance2 = $DB->get_record('enrol', array('enrol'=>'manual', 'courseid'=>$course2->id));
        for ($i=0; $i<9; $i++) {
            $manualenrol->enrol_user($instance1, $testusers[$i], $allroles['student']);
        }
        $manualenrol->enrol_user($instance1, $testusers[8], $allroles['teacher']);
        $manualenrol->enrol_user($instance1, $testusers[9], $allroles['editingteacher']);

        for ($i=10; $i<15; $i++) {
            $manualenrol->enrol_user($instance2, $testusers[$i], $allroles['student']);
        }
        $manualenrol->enrol_user($instance2, $testusers[15], $allroles['editingteacher']);

        // Add tons of role assignments - the more the better.
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

        // Add tons of overrides - the more the better.
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultuserroleid, $frontpageblockcontext, true);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultfrontpageroleid, $frontpageblockcontext, true);
        assign_capability('moodle/block:view', CAP_PROHIBIT, $allroles['guest'], $frontpageblockcontext, true);
        assign_capability('block/online_users:viewlist', CAP_PREVENT, $allroles['user'], $frontpageblockcontext, true);
        assign_capability('block/online_users:viewlist', CAP_PREVENT, $allroles['student'], $frontpageblockcontext, true);

        assign_capability('moodle/site:accessallgroups', CAP_PREVENT, $CFG->defaultuserroleid, $frontpagepagecontext, true);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultfrontpageroleid, $frontpagepagecontext, true);
        assign_capability('mod/page:view', CAP_PREVENT, $allroles['guest'], $frontpagepagecontext, true);
        assign_capability('mod/page:view', CAP_ALLOW, $allroles['user'], $frontpagepagecontext, true);
        assign_capability('mod/page:view', CAP_ALLOW, $allroles['student'], $frontpagepagecontext, true);

        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultuserroleid, $frontpagecontext, true);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultfrontpageroleid, $frontpagecontext, true);
        assign_capability('mod/page:view', CAP_ALLOW, $allroles['guest'], $frontpagecontext, true);
        assign_capability('mod/page:view', CAP_PROHIBIT, $allroles['user'], $frontpagecontext, true);

        assign_capability('mod/page:view', CAP_PREVENT, $allroles['guest'], $systemcontext, true);

        // Prepare for prohibit test.
        role_assign($allroles['editingteacher'], $testusers[19], context_system::instance());
        role_assign($allroles['teacher'], $testusers[19], context_course::instance($testcourses[17]));
        role_assign($allroles['editingteacher'], $testusers[19], context_course::instance($testcourses[17]));
        assign_capability('moodle/course:update', CAP_PROHIBIT, $allroles['teacher'], context_course::instance($testcourses[17]), true);

        accesslib_clear_all_caches_for_unit_testing(); /// Must be done after assign_capability().

        // Extra tests for guests and not-logged-in users because they can not be verified by cross checking
        // with get_users_by_capability() where they are ignored.
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

        // Test prohibits.
        $this->assertTrue(has_capability('moodle/course:update', context_system::instance(), $testusers[19]));
        $ids = get_users_by_capability(context_system::instance(), 'moodle/course:update', 'u.id');
        $this->assertArrayHasKey($testusers[19], $ids);
        $this->assertFalse(has_capability('moodle/course:update', context_course::instance($testcourses[17]), $testusers[19]));
        $ids = get_users_by_capability(context_course::instance($testcourses[17]), 'moodle/course:update', 'u.id');
        $this->assertArrayNotHasKey($testusers[19], $ids);

        // Test the list of enrolled users.
        $coursecontext = context_course::instance($course1->id);
        $enrolled = get_enrolled_users($coursecontext);
        $this->assertCount(10, $enrolled);
        for ($i=0; $i<10; $i++) {
            $this->assertTrue(isset($enrolled[$testusers[$i]]));
        }
        $enrolled = get_enrolled_users($coursecontext, 'moodle/course:update');
        $this->assertCount(1, $enrolled);
        $this->assertTrue(isset($enrolled[$testusers[9]]));
        unset($enrolled);

        // Role switching.
        $userid = $testusers[9];
        $USER = $DB->get_record('user', array('id'=>$userid));
        load_all_capabilities();
        $coursecontext = context_course::instance($course1->id);
        $this->assertTrue(has_capability('moodle/course:update', $coursecontext));
        $this->assertFalse(is_role_switched($course1->id));
        role_switch($allroles['student'], $coursecontext);
        $this->assertTrue(is_role_switched($course1->id));
        $this->assertEquals($allroles['student'], $USER->access['rsw'][$coursecontext->path]);
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
        $this->assertEquals($allroles['student'], $USER->access['rsw'][$coursecontext->path]);
        $this->assertFalse(has_capability('moodle/course:update', $blockcontext));
        reload_all_capabilities();
        $this->assertFalse(has_capability('moodle/course:update', $blockcontext));
        load_all_capabilities();
        $this->assertTrue(has_capability('moodle/course:update', $blockcontext));

        // Temp course role for enrol.
        $DB->delete_records('cache_flags', array()); // This prevents problem with dirty contexts immediately resetting the temp role - this is a known problem...
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
        // at the same time validate extra restrictions (guest read only no risks, admin exception, non existent and deleted users).
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

        foreach ($userids as $userid) { // No guest or deleted.
            // Each user gets 0-10 random roles.
            $rcount = rand(0, 10);
            for ($j=0; $j<$rcount; $j++) {
                $roleid = $roles[rand(0, count($roles)-1)];
                $contextid = $contexts[rand(0, count($contexts)-1)]->id;
                role_assign($roleid, $userid, $contextid);
            }
        }

        $permissions = array(CAP_ALLOW, CAP_PREVENT, CAP_INHERIT, CAP_PREVENT);
        $maxoverrides = count($contexts)*10;
        for ($j=0; $j<$maxoverrides; $j++) {
            $roleid = $roles[rand(0, count($roles)-1)];
            $contextid = $contexts[rand(0, count($contexts)-1)]->id;
            $permission = $permissions[rand(0, count($permissions)-1)];
            $capname = $capabilities[rand(0, count($capabilities)-1)]->name;
            assign_capability($capname, $permission, $roleid, $contextid, true);
        }
        unset($permissions);
        unset($roles);

        accesslib_clear_all_caches_for_unit_testing(); // must be done after assign_capability().

        // Test time - let's set up some real user, just in case the logic for USER affects the others...
        $USER = $DB->get_record('user', array('id'=>$testusers[3]));
        load_all_capabilities();

        $userids[] = $CFG->siteguest;
        $userids[] = 0; // Not-logged-in user.
        $userids[] = -1; // Non-existent user.

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
        // Back to nobody.
        $USER = new stdClass();
        $USER->id = 0;
        unset($contexts);
        unset($userids);
        unset($capabilities);

        // Now let's do all the remaining tests that break our carefully prepared fake site.


        // Test $context->mark_dirty() method.

        $DB->delete_records('cache_flags', array());
        accesslib_clear_all_caches(false);
        $systemcontext->mark_dirty();
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertTrue(isset($dirty[$systemcontext->path]));
        $this->assertTrue(isset($ACCESSLIB_PRIVATE->dirtycontexts[$systemcontext->path]));


        // Test $context->reload_if_dirty() method.

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


        // Test context_helper::build_all_paths() method.

        $oldcontexts = $DB->get_records('context', array(), 'id');
        $DB->set_field_select('context', 'path', null, "contextlevel <> ".CONTEXT_SYSTEM);
        $DB->set_field_select('context', 'depth', 0, "contextlevel <> ".CONTEXT_SYSTEM);
        context_helper::build_all_paths();
        $newcontexts = $DB->get_records('context', array(), 'id');
        $this->assertEquals($oldcontexts, $newcontexts);
        unset($oldcontexts);
        unset($newcontexts);


        // Test $context->reset_paths() method.

        $context = context_course::instance($testcourses[2]);
        $children = $context->get_child_contexts();
        $context->reset_paths(false);
        $this->assertNull($DB->get_field('context', 'path', array('id'=>$context->id)));
        $this->assertEquals(0, $DB->get_field('context', 'depth', array('id'=>$context->id)));
        foreach ($children as $child) {
            $this->assertNull($DB->get_field('context', 'path', array('id'=>$child->id)));
            $this->assertEquals(0, $DB->get_field('context', 'depth', array('id'=>$child->id)));
        }
        $this->assertEquals(count($children)+1, $DB->count_records('context', array('depth'=>0)));
        $this->assertEquals(count($children)+1, $DB->count_records('context', array('path'=>null)));

        $context = context_course::instance($testcourses[2]);
        $context->reset_paths(true);
        $context = context_course::instance($testcourses[2]);
        $this->assertSame($context->path, $DB->get_field('context', 'path', array('id'=>$context->id)));
        $this->assertSame($context->depth, $DB->get_field('context', 'depth', array('id'=>$context->id)));
        $this->assertEquals(0, $DB->count_records('context', array('depth'=>0)));
        $this->assertEquals(0, $DB->count_records('context', array('path'=>null)));


        // Test $context->update_moved() method.

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
        $this->assertEquals($categorycontext, $context->get_parent_context());
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertFalse(isset($dirty[$oldpath]));
        $this->assertTrue(isset($dirty[$context->path]));


        // Test $context->delete_content() method.

        context_helper::reset_caches();
        $context = context_module::instance($testpages[3]);
        $this->assertTrue($DB->record_exists('context', array('id'=>$context->id)));
        $this->assertEquals(1, $DB->count_records('block_instances', array('parentcontextid'=>$context->id)));
        $context->delete_content();
        $this->assertTrue($DB->record_exists('context', array('id'=>$context->id)));
        $this->assertEquals(0, $DB->count_records('block_instances', array('parentcontextid'=>$context->id)));


        // Test $context->delete() method.

        context_helper::reset_caches();
        $context = context_module::instance($testpages[4]);
        $this->assertTrue($DB->record_exists('context', array('id'=>$context->id)));
        $this->assertEquals(1, $DB->count_records('block_instances', array('parentcontextid'=>$context->id)));
        $bi = $DB->get_record('block_instances', array('parentcontextid'=>$context->id));
        $bicontext = context_block::instance($bi->id);
        $DB->delete_records('cache_flags', array());
        $context->delete(); // Should delete also linked blocks.
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertFalse(isset($dirty[$context->path]));
        $this->assertFalse($DB->record_exists('context', array('id'=>$context->id)));
        $this->assertFalse($DB->record_exists('context', array('id'=>$bicontext->id)));
        $this->assertFalse($DB->record_exists('context', array('contextlevel'=>CONTEXT_MODULE, 'instanceid'=>$testpages[4])));
        $this->assertFalse($DB->record_exists('context', array('contextlevel'=>CONTEXT_BLOCK, 'instanceid'=>$bi->id)));
        $this->assertEquals(0, $DB->count_records('block_instances', array('parentcontextid'=>$context->id)));
        context_module::instance($testpages[4]);


        // Test context_helper::delete_instance() method.

        context_helper::reset_caches();
        $lastcourse = array_pop($testcourses);
        $this->assertTrue($DB->record_exists('context', array('contextlevel'=>CONTEXT_COURSE, 'instanceid'=>$lastcourse)));
        $coursecontext = context_course::instance($lastcourse);
        $this->assertEquals(1, context_inspection::test_context_cache_size());
        $this->assertNotEquals(CONTEXT_COURSE, $coursecontext->instanceid);
        $DB->delete_records('cache_flags', array());
        context_helper::delete_instance(CONTEXT_COURSE, $lastcourse);
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertFalse(isset($dirty[$coursecontext->path]));
        $this->assertEquals(0, context_inspection::test_context_cache_size());
        $this->assertFalse($DB->record_exists('context', array('contextlevel'=>CONTEXT_COURSE, 'instanceid'=>$lastcourse)));
        context_course::instance($lastcourse);


        // Test context_helper::create_instances() method.

        $prevcount = $DB->count_records('context');
        $DB->delete_records('context', array('contextlevel'=>CONTEXT_BLOCK));
        context_helper::create_instances(null, true);
        $this->assertSame($DB->count_records('context'), $prevcount);
        $this->assertEquals(0, $DB->count_records('context', array('depth'=>0)));
        $this->assertEquals(0, $DB->count_records('context', array('path'=>null)));

        $DB->delete_records('context', array('contextlevel'=>CONTEXT_BLOCK));
        $DB->delete_records('block_instances', array());
        $prevcount = $DB->count_records('context');
        $DB->delete_records_select('context', 'contextlevel <> '.CONTEXT_SYSTEM);
        context_helper::create_instances(null, true);
        $this->assertSame($prevcount, $DB->count_records('context'));
        $this->assertEquals(0, $DB->count_records('context', array('depth'=>0)));
        $this->assertEquals(0, $DB->count_records('context', array('path'=>null)));

        // Test context_helper::cleanup_instances() method.

        $lastcourse = $DB->get_field_sql("SELECT MAX(id) FROM {course}");
        $DB->delete_records('course', array('id'=>$lastcourse));
        $lastcategory = $DB->get_field_sql("SELECT MAX(id) FROM {course_categories}");
        $DB->delete_records('course_categories', array('id'=>$lastcategory));
        $lastuser = $DB->get_field_sql("SELECT MAX(id) FROM {user} WHERE deleted=0");
        $DB->delete_records('user', array('id'=>$lastuser));
        $DB->delete_records('block_instances', array('parentcontextid'=>$frontpagepagecontext->id));
        $DB->delete_records('course_modules', array('id'=>$frontpagepagecontext->instanceid));
        context_helper::cleanup_instances();
        $count = 1; // System.
        $count += $DB->count_records('user', array('deleted'=>0));
        $count += $DB->count_records('course_categories');
        $count += $DB->count_records('course');
        $count += $DB->count_records('course_modules');
        $count += $DB->count_records('block_instances');
        $this->assertEquals($count, $DB->count_records('context'));


        // Test context cache size restrictions.

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
                $this->assertEquals(CONTEXT_CACHE_MAX_SIZE, context_inspection::test_context_cache_size());
            } else if ($i == CONTEXT_CACHE_MAX_SIZE) {
                // Once the limit is reached roughly 1/3 of records should be removed from cache.
                $this->assertEquals((int)ceil(CONTEXT_CACHE_MAX_SIZE * (2/3) + 101), context_inspection::test_context_cache_size());
            }
        }
        // We keep the first 100 cached.
        $prevsize = context_inspection::test_context_cache_size();
        for ($i=0; $i<100; $i++) {
            context_user::instance($testusers[$i]);
            $this->assertEquals($prevsize, context_inspection::test_context_cache_size());
        }
        context_user::instance($testusers[102]);
        $this->assertEquals($prevsize+1, context_inspection::test_context_cache_size());
        unset($testusers);



        // Test basic test of legacy functions.
        // Note: watch out, the fake site might be pretty borked already.

        $this->assertEquals(get_system_context(), context_system::instance());
        $this->assertDebuggingCalled('get_system_context() is deprecated, please use context_system::instance() instead.', DEBUG_DEVELOPER);

        foreach ($DB->get_records('context') as $contextid => $record) {
            $context = context::instance_by_id($contextid);
            $this->assertEquals($context, get_context_instance($record->contextlevel, $record->instanceid));
            $this->assertDebuggingCalled('get_context_instance() is deprecated, please use context_xxxx::instance() instead.', DEBUG_DEVELOPER);
        }

        // Make sure a debugging is thrown.
        get_context_instance($record->contextlevel, $record->instanceid);
        $this->assertDebuggingCalled('get_context_instance() is deprecated, please use context_xxxx::instance() instead.', DEBUG_DEVELOPER);
        get_system_context();
        $this->assertDebuggingCalled('get_system_context() is deprecated, please use context_system::instance() instead.', DEBUG_DEVELOPER);
    }

    /**
     * Helper that verifies a list of capabilities, as returned by
     * $context->get_capabilities() contains certain capabilities.
     *
     * @param array $expected a list of capability names
     * @param array $actual a list of capability info from $context->get_capabilities().
     */
    protected function assert_capability_list_contains($expected, $actual) {
        $actualnames = [];
        foreach ($actual as $cap) {
            $actualnames[] = $cap->name;
        }
        // Verify each expected element exists.
        foreach ($expected as $key => $value) {
            $this->assertContains($value, $actualnames);
        }
    }

    /**
     * Test that context_system::get_capabilities returns capabilities relevant to all modules.
     *
     * @covers \context_system::get_capabilities
     */
    public function test_context_module_caps_returned_by_get_capabilities_in_sys_context() {
        $actual = context_system::instance()->get_capabilities();

        // Just test a few representative capabilities.
        $expectedcapabilities = ['moodle/site:accessallgroups', 'moodle/site:viewfullnames',
                'repository/upload:view', 'atto/recordrtc:recordaudio'];

        $this->assert_capability_list_contains($expectedcapabilities, $actual);
    }

    /**
     * Test that context_coursecat::get_capabilities returns capabilities relevant to all modules.
     *
     * @covers \context_coursecat::get_capabilities
     */
    public function test_context_module_caps_returned_by_get_capabilities_in_course_cat_context() {
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();
        $cat = $generator->create_category();

        $actual = context_coursecat::instance($cat->id)->get_capabilities();

        // Just test a few representative capabilities.
        $expectedcapabilities = ['moodle/site:accessallgroups', 'moodle/site:viewfullnames',
                'repository/upload:view', 'atto/recordrtc:recordaudio'];

        $this->assert_capability_list_contains($expectedcapabilities, $actual);
    }

    /**
     * Test that context_course::get_capabilities returns capabilities relevant to all modules.
     *
     * @covers \context_course::get_capabilities
     */
    public function test_context_module_caps_returned_by_get_capabilities_in_course_context() {
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();
        $cat = $generator->create_category();
        $course = $generator->create_course(['category' => $cat->id]);

        $actual = context_course::instance($course->id)->get_capabilities();

        // Just test a few representative capabilities.
        $expectedcapabilities = ['moodle/site:accessallgroups', 'moodle/site:viewfullnames',
                'repository/upload:view', 'atto/recordrtc:recordaudio'];

        $this->assert_capability_list_contains($expectedcapabilities, $actual);
    }

    /**
     * Test that context_module::get_capabilities returns capabilities relevant to all modules.
     *
     * @covers \context_module::get_capabilities
     */
    public function test_context_module_caps_returned_by_get_capabilities_mod_context() {
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();
        $cat = $generator->create_category();
        $course = $generator->create_course(['category' => $cat->id]);
        $page = $generator->create_module('page', ['course' => $course->id]);

        $actual = context_module::instance($page->cmid)->get_capabilities();

        // Just test a few representative capabilities.
        $expectedcapabilities = ['moodle/site:accessallgroups', 'moodle/site:viewfullnames',
                'repository/upload:view', 'atto/recordrtc:recordaudio'];

        $this->assert_capability_list_contains($expectedcapabilities, $actual);
    }

    /**
     * Test that {@see context_block::get_capabilities} returns capabilities relevant to blocks
     *
     * @covers \context_block::get_capabilities
     */
    public function test_context_block_caps_returned_by_get_capabilities_block_context(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $block = $this->getDataGenerator()->create_block('online_users', [
            'parentcontextid' => context_course::instance($course->id)->id,
        ]);

        $capabilities = context_block::instance($block->id)->get_capabilities();

        // Just test a few representative capabilities.
        $expected = ['block/online_users:addinstance', 'moodle/block:edit', 'moodle/block:view'];
        $this->assert_capability_list_contains($expected, $capabilities);

        // Now test with different sorting.
        $capabilitiesbyname = context_block::instance($block->id)->get_capabilities('riskbitmask');

        $capabilitynames = array_column($capabilities, 'name');
        $capabilitynamesordered = array_column($capabilitiesbyname, 'name');

        // Each array should contain the same data, ordered differently.
        $this->assertEqualsCanonicalizing($capabilitynames, $capabilitynamesordered);
        $this->assertNotSame($capabilitynames, $capabilitynamesordered);
    }

    /**
     * Test that {@see context_user::get_capabilities} returns capabilities relevant to users
     *
     * @covers \context_user::get_capabilities
     */
    public function test_context_user_caps_returned_by_get_capabilities_user_context(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $capabilities = context_user::instance($user->id)->get_capabilities();

        // Just test a few representative capabilities.
        $expected = ['moodle/user:editmessageprofile', 'moodle/user:editprofile', 'moodle/user:viewalldetails'];
        $this->assert_capability_list_contains($expected, $capabilities);

        // Now test with different sorting.
        $capabilitiesbyname = context_user::instance($user->id)->get_capabilities('name');

        $capabilitynames = array_column($capabilities, 'name');
        $capabilitynamesordered = array_column($capabilitiesbyname, 'name');

        // Each array should contain the same data, ordered differently.
        $this->assertEqualsCanonicalizing($capabilitynames, $capabilitynamesordered);
        $this->assertNotSame($capabilitynames, $capabilitynamesordered);
    }

    /**
     * Test updating of role capabilities during upgrade
     *
     * @covers ::update_capabilities
     * @covers ::update_capabilities
     */
    public function test_update_capabilities() {
        global $DB, $SITE;

        $this->resetAfterTest(true);

        $froncontext = context_course::instance($SITE->id);
        $student = $DB->get_record('role', array('shortname'=>'student'));
        $teacher = $DB->get_record('role', array('shortname'=>'teacher'));

        $existingcaps = $DB->get_records('capabilities', array(), 'id', 'name, captype, contextlevel, component, riskbitmask');

        $this->assertFalse(isset($existingcaps['moodle/site:restore']));         // Moved to new 'moodle/restore:restorecourse'.
        $this->assertTrue(isset($existingcaps['moodle/restore:restorecourse'])); // New cap from 'moodle/site:restore'.
        $this->assertTrue(isset($existingcaps['moodle/site:sendmessage']));      // New capability.
        $this->assertTrue(isset($existingcaps['moodle/backup:backupcourse']));
        $this->assertTrue(isset($existingcaps['moodle/backup:backupsection']));  // Cloned from 'moodle/backup:backupcourse'.
        $this->assertTrue(isset($existingcaps['moodle/site:approvecourse']));    // Updated bitmask.
        $this->assertTrue(isset($existingcaps['moodle/course:manageactivities']));
        $this->assertTrue(isset($existingcaps['mod/page:addinstance']));         // Cloned from core 'moodle/course:manageactivities'.

        // Fake state before upgrade.
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

        // Execute core.
        update_capabilities('moodle');

        // Only core should be upgraded.
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

        // Execute plugin.
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

    /**
     * Tests reset_role_capabilities function.
     *
     * @covers ::reset_role_capabilities
     */
    public function test_reset_role_capabilities() {
        global $DB;
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        // Create test course and user, enrol one in the other.
        $course = $generator->create_course();
        $user = $generator->create_user();
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'student'), MUST_EXIST);
        $generator->enrol_user($user->id, $course->id, $roleid);

        // Change student role so it DOES have 'mod/forum:addinstance'.
        $systemcontext = context_system::instance();
        assign_capability('mod/forum:addinstance', CAP_ALLOW, $roleid, $systemcontext->id);

        // Override course so it does NOT allow students 'mod/forum:viewdiscussion'.
        $coursecontext = context_course::instance($course->id);
        assign_capability('mod/forum:viewdiscussion', CAP_PREVENT, $roleid, $coursecontext->id);

        // Check expected capabilities so far.
        $this->assertTrue(has_capability('mod/forum:addinstance', $coursecontext, $user));
        $this->assertFalse(has_capability('mod/forum:viewdiscussion', $coursecontext, $user));

        // Oops, allowing student to add forums was a mistake, let's reset the role.
        reset_role_capabilities($roleid);

        // Check new expected capabilities - role capabilities should have been reset,
        // while the override at course level should remain.
        $this->assertFalse(has_capability('mod/forum:addinstance', $coursecontext, $user));
        $this->assertFalse(has_capability('mod/forum:viewdiscussion', $coursecontext, $user));
    }

    /**
     * Tests count_role_users function.
     *
     * @covers ::count_role_users
     */
    public function test_count_role_users() {
        global $DB;
        $this->resetAfterTest(true);
        $generator = self::getDataGenerator();
        // Create a course in a category, and some users.
        $category = $generator->create_category();
        $course = $generator->create_course(array('category' => $category->id));
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();
        $user4 = $generator->create_user();
        $user5 = $generator->create_user();
        $roleid1 = $DB->get_field('role', 'id', array('shortname' => 'manager'), MUST_EXIST);
        $roleid2 = $DB->get_field('role', 'id', array('shortname' => 'coursecreator'), MUST_EXIST);
        // Enrol two users as managers onto the course, and 1 onto the category.
        $generator->enrol_user($user1->id, $course->id, $roleid1);
        $generator->enrol_user($user2->id, $course->id, $roleid1);
        $generator->role_assign($roleid1, $user3->id, context_coursecat::instance($category->id));
        // Enrol 1 user as a coursecreator onto the course, and another onto the category.
        // This is to ensure we do not count users with roles that are not specified.
        $generator->enrol_user($user4->id, $course->id, $roleid2);
        $generator->role_assign($roleid2, $user5->id, context_coursecat::instance($category->id));
        // Check that the correct users are found on the course.
        $this->assertEquals(2, count_role_users($roleid1, context_course::instance($course->id), false));
        $this->assertEquals(3, count_role_users($roleid1, context_course::instance($course->id), true));
        // Check for the category.
        $this->assertEquals(1, count_role_users($roleid1, context_coursecat::instance($category->id), false));
        $this->assertEquals(1, count_role_users($roleid1, context_coursecat::instance($category->id), true));
        // Have a user with the same role at both the category and course level.
        $generator->role_assign($roleid1, $user1->id, context_coursecat::instance($category->id));
        // The course level checks should remain the same.
        $this->assertEquals(2, count_role_users($roleid1, context_course::instance($course->id), false));
        $this->assertEquals(3, count_role_users($roleid1, context_course::instance($course->id), true));
    }

    /**
     * Test fetching users by capability.
     *
     * @covers ::get_users_by_capability
     */
    public function test_get_users_by_capability() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $student = $this->getDataGenerator()->create_user();
        $guest = $DB->get_record('user', array('username' => 'guest'));

        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        role_assign($studentrole->id, $student->id, $coursecontext);
        $admin = $DB->get_record('user', array('username' => 'admin'));

        // Note: Here are used default capabilities, the full test is in permission evaluation below,
        // use two capabilities that teacher has and one does not, none of them should be allowed for not-logged-in user.
        $this->assertTrue($DB->record_exists('capabilities', array('name' => 'moodle/backup:backupcourse')));
        $this->assertTrue($DB->record_exists('capabilities', array('name' => 'moodle/site:approvecourse')));

        $users = get_users_by_capability($coursecontext, 'moodle/backup:backupcourse');

        $this->assertTrue(array_key_exists($teacher->id, $users));
        $this->assertFalse(array_key_exists($admin->id, $users));
        $this->assertFalse(array_key_exists($student->id, $users));
        $this->assertFalse(array_key_exists($guest->id, $users));

        $users = get_users_by_capability($coursecontext, 'moodle/site:approvecourse');

        $this->assertFalse(array_key_exists($teacher->id, $users));
        $this->assertFalse(array_key_exists($admin->id, $users));
        $this->assertFalse(array_key_exists($student->id, $users));
        $this->assertFalse(array_key_exists($guest->id, $users));

        // Test role override.
        assign_capability('moodle/backup:backupcourse', CAP_PROHIBIT, $teacherrole->id, $coursecontext, true);
        assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $studentrole->id, $coursecontext, true);

        $users = get_users_by_capability($coursecontext, 'moodle/backup:backupcourse');

        $this->assertFalse(array_key_exists($teacher->id, $users));
        $this->assertFalse(array_key_exists($admin->id, $users));
        $this->assertTrue(array_key_exists($student->id, $users));
        $this->assertFalse(array_key_exists($guest->id, $users));
    }


    /**
     * @covers ::get_with_capability_sql
     */
    public function test_get_with_capability_sql() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $student = $this->getDataGenerator()->create_user();
        $guest = $DB->get_record('user', array('username' => 'guest'));

        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        role_assign($studentrole->id, $student->id, $coursecontext);
        $admin = $DB->get_record('user', array('username' => 'admin'));

        // Note: Here are used default capabilities, the full test is in permission evaluation below,
        // use two capabilities that teacher has and one does not, none of them should be allowed for not-logged-in user.
        $this->assertTrue($DB->record_exists('capabilities', array('name' => 'moodle/backup:backupcourse')));
        $this->assertTrue($DB->record_exists('capabilities', array('name' => 'moodle/site:approvecourse')));

        list($sql, $params) = get_with_capability_sql($coursecontext, 'moodle/backup:backupcourse');
        $users = $DB->get_records_sql($sql, $params);

        $this->assertTrue(array_key_exists($teacher->id, $users));
        $this->assertFalse(array_key_exists($admin->id, $users));
        $this->assertFalse(array_key_exists($student->id, $users));
        $this->assertFalse(array_key_exists($guest->id, $users));

        list($sql, $params) = get_with_capability_sql($coursecontext, 'moodle/site:approvecourse');
        $users = $DB->get_records_sql($sql, $params);

        $this->assertFalse(array_key_exists($teacher->id, $users));
        $this->assertFalse(array_key_exists($admin->id, $users));
        $this->assertFalse(array_key_exists($student->id, $users));
        $this->assertFalse(array_key_exists($guest->id, $users));

        // Test role override.
        assign_capability('moodle/backup:backupcourse', CAP_PROHIBIT, $teacherrole->id, $coursecontext, true);
        assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $studentrole->id, $coursecontext, true);

        list($sql, $params) = get_with_capability_sql($coursecontext, 'moodle/backup:backupcourse');
        $users = $DB->get_records_sql($sql, $params);

        $this->assertFalse(array_key_exists($teacher->id, $users));
        $this->assertFalse(array_key_exists($admin->id, $users));
        $this->assertTrue(array_key_exists($student->id, $users));
        $this->assertFalse(array_key_exists($guest->id, $users));
    }


    /**
     * Get the test cases for {@link test_get_with_capability_join_when_overrides_present()}.
     *
     * The particular capabilties used here do not really matter. What is important is
     * that they are capabilities which the Student roles has by default, but the
     * authenticated suser role does not.
     *
     * @return array
     */
    public function get_get_with_capability_join_override_cases() {
        return [
                'no overrides' => [true, []],
                'one override' => [true, ['moodle/course:viewscales']],
                'both overrides' => [false, ['moodle/course:viewscales', 'moodle/question:flag']],
        ];
    }

    /**
     * Test get_with_capability_join.
     *
     * @dataProvider get_get_with_capability_join_override_cases
     * @covers ::get_with_capability_join
     *
     * @param bool $studentshouldbereturned whether, with this combination of capabilities, the student should be in the results.
     * @param array $capabilitiestoprevent capabilities to override to prevent in the course context.
     */
    public function test_get_with_capability_join_when_overrides_present(
            bool $studentshouldbereturned, array $capabilitiestoprevent) {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        // Create a course.
        $category = $generator->create_category();
        $course = $generator->create_course(['category' => $category->id]);

        // Create a user.
        $student = $generator->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);
        $generator->enrol_user($student->id, $course->id, $studentrole->id);

        // This test assumes that by default the student roles has the two
        // capabilities. Check this now in case the role definitions are every changed.
        $coursecontext = context_course::instance($course->id);
        $this->assertTrue(has_capability('moodle/course:viewscales', $coursecontext, $student));
        $this->assertTrue(has_capability('moodle/question:flag', $coursecontext, $student));

        // We test cases where there are a varying number of prevent overrides.
        foreach ($capabilitiestoprevent as $capability) {
            role_change_permission($studentrole->id, $coursecontext, $capability, CAP_PREVENT);
        }

        // So now, assemble our query using the method under test, and verify that it returns the student.
        $sqljoin = get_with_capability_join($coursecontext,
                ['moodle/course:viewscales', 'moodle/question:flag'], 'u.id');

        $users = $DB->get_records_sql("SELECT u.*
                  FROM {user} u
                       {$sqljoin->joins}
                 WHERE {$sqljoin->wheres}", $sqljoin->params);
        if ($studentshouldbereturned) {
            $this->assertEquals([$student->id], array_keys($users));
        } else {
            $this->assertEmpty($users);
        }
    }

    /**
     * Test the get_profile_roles() function.
     *
     * @covers ::get_profile_roles
     */
    public function test_get_profile_roles() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        // Assign a student role.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $user1 = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $user1->id, $coursecontext);

        // Assign an editing teacher role.
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'), '*', MUST_EXIST);
        $user2 = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $user2->id, $coursecontext);

        // Create a custom role that can be assigned at course level, but don't assign it yet.
        create_role('Custom role', 'customrole', 'Custom course role');
        $customrole = $DB->get_record('role', array('shortname' => 'customrole'), '*', MUST_EXIST);
        set_role_contextlevels($customrole->id, [CONTEXT_COURSE]);
        core_role_set_assign_allowed($teacherrole->id, $customrole->id); // Allow teacher to assign the role in the course.

        // Set the site policy 'profileroles' to show student, teacher and non-editing teacher roles (i.e. not the custom role).
        $neteacherrole = $DB->get_record('role', array('shortname' => 'teacher'), '*', MUST_EXIST);
        set_config('profileroles', "{$studentrole->id}, {$teacherrole->id}, {$neteacherrole->id}");

        // A student in the course (given they can't assign roles) should see those roles which are:
        // - listed in the 'profileroles' site policy AND
        // - are assigned in the course context (or parent contexts).
        // In this case, the non-editing teacher role is not assigned and should not be returned.
        $expected = [
            $teacherrole->id => (object) [
                'id' => $teacherrole->id,
                'name' => '',
                'shortname' => $teacherrole->shortname,
                'sortorder' => $teacherrole->sortorder,
                'coursealias' => null
            ],
            $studentrole->id => (object) [
                'id' => $studentrole->id,
                'name' => '',
                'shortname' => $studentrole->shortname,
                'sortorder' => $studentrole->sortorder,
                'coursealias' => null
            ]
        ];
        $this->setUser($user1);
        $this->assertEquals($expected, get_profile_roles($coursecontext));

        // An editing teacher should also see only 2 roles at this stage as only 2 roles are assigned: 'teacher' and 'student'.
        $this->setUser($user2);
        $this->assertEquals($expected, get_profile_roles($coursecontext));

        // Assign a custom role in the course.
        $user3 = $this->getDataGenerator()->create_user();
        role_assign($customrole->id, $user3->id, $coursecontext);

        // Confirm that the teacher can see the custom role now that it's assigned.
        $expectedteacher = [
            $teacherrole->id => (object) [
                'id' => $teacherrole->id,
                'name' => '',
                'shortname' => $teacherrole->shortname,
                'sortorder' => $teacherrole->sortorder,
                'coursealias' => null
            ],
            $studentrole->id => (object) [
                'id' => $studentrole->id,
                'name' => '',
                'shortname' => $studentrole->shortname,
                'sortorder' => $studentrole->sortorder,
                'coursealias' => null
            ],
            $customrole->id => (object) [
                'id' => $customrole->id,
                'name' => 'Custom role',
                'shortname' => $customrole->shortname,
                'sortorder' => $customrole->sortorder,
                'coursealias' => null
            ]
        ];
        $this->setUser($user2);
        $this->assertEquals($expectedteacher, get_profile_roles($coursecontext));

        // And that the student can't, because the role isn't included in the 'profileroles' site policy.
        $expectedstudent = [
            $teacherrole->id => (object) [
                'id' => $teacherrole->id,
                'name' => '',
                'shortname' => $teacherrole->shortname,
                'sortorder' => $teacherrole->sortorder,
                'coursealias' => null
            ],
            $studentrole->id => (object) [
                'id' => $studentrole->id,
                'name' => '',
                'shortname' => $studentrole->shortname,
                'sortorder' => $studentrole->sortorder,
                'coursealias' => null
            ]
        ];
        $this->setUser($user1);
        $this->assertEquals($expectedstudent, get_profile_roles($coursecontext));

        // If we have no roles listed in the site policy, the teacher should be able to see the assigned roles.
        $expectedteacher = [
            $studentrole->id => (object) [
                'id' => $studentrole->id,
                'name' => '',
                'shortname' => $studentrole->shortname,
                'sortorder' => $studentrole->sortorder,
                'coursealias' => null
            ],
            $customrole->id => (object) [
                'id' => $customrole->id,
                'name' => 'Custom role',
                'shortname' => $customrole->shortname,
                'sortorder' => $customrole->sortorder,
                'coursealias' => null
            ],
            $teacherrole->id => (object) [
                'id' => $teacherrole->id,
                'name' => '',
                'shortname' => $teacherrole->shortname,
                'sortorder' => $teacherrole->sortorder,
                'coursealias' => null
            ],
        ];
        set_config('profileroles', "");
        $this->setUser($user2);
        $this->assertEquals($expectedteacher, get_profile_roles($coursecontext));
    }

    /**
     * Data provider for is_parent_of context checks.
     *
     * @return  array
     */
    public function is_parent_of_provider(): array {
        $provideboth = function(string $desc, string $contextpath, string $testpath, bool $expected): array {
            return [
                "includeself: true; {$desc}" => [
                    $contextpath,
                    $testpath,
                    true,
                    $expected,
                ],
                "includeself: false; {$desc}" => [
                    $contextpath,
                    $testpath,
                    false,
                    $expected,
                ],
            ];
        };

        return array_merge(
            [
                'includeself: true, testing self' => [
                    '/1/4/17/291/1001/17105',
                    '/1/4/17/291/1001/17105',
                    true,
                    true,
                ],
                'includeself: false, testing self' => [
                    '/1/4/17/291/1001/17105',
                    '/1/4/17/291/1001/17105',
                    false,
                    false,
                ],
            ],
            $provideboth(
                'testing parent',
                '/1/4/17/291/1001/17105',
                '/1/4/17/291/1001',
                false
            ),
            $provideboth(
                'testing child',
                '/1/4/17/291/1001',
                '/1/4/17/291/1001/17105',
                true
            ),
            $provideboth(
                'testing grandchild',
                '/1',
                '/1/4/17/291/1001/17105',
                true
            )
        );
    }

    /**
     * Ensure that the is_parent_of() function works as anticipated.
     *
     * @dataProvider is_parent_of_provider
     * @covers \context::is_parent_of
     * @covers \context_block::is_parent_of
     * @covers \context_course::is_parent_of
     * @covers \context_coursecat::is_parent_of
     * @covers \context_module::is_parent_of
     * @covers \context_system::is_parent_of
     * @covers \context_user::is_parent_of
     * @param   string $contextpath The path of the context being compared with
     * @param   string $testpath The path of the context being compared
     * @param   bool $testself Whether to check the current context
     * @param   bool $expected The expected result
     */
    public function test_is_parent_of(string $contextpath, string $testpath, bool $testself, bool $expected): void {
        $context = $this->getMockBuilder(\context::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'get_url',
                'get_capabilities',
            ])
            ->getMock();

        $rcp = new ReflectionProperty($context, '_path');
        $rcp->setAccessible(true);
        $rcp->setValue($context, $contextpath);

        $comparisoncontext = $this->getMockBuilder(\context::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'get_url',
                'get_capabilities',
            ])
            ->getMock();

        $rcp = new ReflectionProperty($comparisoncontext, '_path');
        $rcp->setAccessible(true);
        $rcp->setValue($comparisoncontext, $testpath);

        $this->assertEquals($expected, $context->is_parent_of($comparisoncontext, $testself));
    }

    /**
     * Data provider for is_child_of context checks.
     *
     * @return  array
     */
    public function is_child_of_provider(): array {
        $provideboth = function(string $desc, string $contextpath, string $testpath, bool $expected): array {
            return [
                "includeself: true; {$desc}" => [
                    $contextpath,
                    $testpath,
                    true,
                    $expected,
                ],
                "includeself: false; {$desc}" => [
                    $contextpath,
                    $testpath,
                    false,
                    $expected,
                ],
            ];
        };

        return array_merge(
            [
                'includeself: true, testing self' => [
                    '/1/4/17/291/1001/17105',
                    '/1/4/17/291/1001/17105',
                    true,
                    true,
                ],
                'includeself: false, testing self' => [
                    '/1/4/17/291/1001/17105',
                    '/1/4/17/291/1001/17105',
                    false,
                    false,
                ],
            ],
            $provideboth(
                'testing child',
                '/1/4/17/291/1001/17105',
                '/1/4/17/291/1001',
                true
            ),
            $provideboth(
                'testing parent',
                '/1/4/17/291/1001',
                '/1/4/17/291/1001/17105',
                false
            ),
            $provideboth(
                'testing grandchild',
                '/1/4/17/291/1001/17105',
                '/1',
                true
            ),
            $provideboth(
                'testing grandparent',
                '/1',
                '/1/4/17/291/1001/17105',
                false
            )
        );
    }

    /**
     * Ensure that the is_child_of() function works as anticipated.
     *
     * @dataProvider is_child_of_provider
     * @covers \context::is_child_of
     * @covers \context_block::is_child_of
     * @covers \context_course::is_child_of
     * @covers \context_coursecat::is_child_of
     * @covers \context_module::is_child_of
     * @covers \context_system::is_child_of
     * @covers \context_user::is_child_of
     * @param   string $contextpath The path of the context being compared with
     * @param   string $testpath The path of the context being compared
     * @param   bool $testself Whether to check the current context
     * @param   bool $expected The expected result
     */
    public function test_is_child_of(string $contextpath, string $testpath, bool $testself, bool $expected): void {
        $context = $this->getMockBuilder(\context::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'get_url',
                'get_capabilities',
            ])
            ->getMock();

        $rcp = new ReflectionProperty($context, '_path');
        $rcp->setAccessible(true);
        $rcp->setValue($context, $contextpath);

        $comparisoncontext = $this->getMockBuilder(\context::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'get_url',
                'get_capabilities',
            ])
            ->getMock();

        $rcp = new ReflectionProperty($comparisoncontext, '_path');
        $rcp->setAccessible(true);
        $rcp->setValue($comparisoncontext, $testpath);

        $this->assertEquals($expected, $context->is_child_of($comparisoncontext, $testself));
    }

    /**
     * Ensure that the get_parent_contexts() function limits the number of queries it performs.
     *
     * @covers ::get_parent_contexts
     */
    public function test_get_parent_contexts_preload() {
        global $DB;

        $this->resetAfterTest();

        /*
         * Given the following data structure:
         * System
         * - Category
         * --- Category
         * ----- Category
         * ------- Category
         * --------- Course
         * ----------- Activity (Forum)
         */

        $contexts = [];

        $cat1 = $this->getDataGenerator()->create_category();
        $cat2 = $this->getDataGenerator()->create_category(['parent' => $cat1->id]);
        $cat3 = $this->getDataGenerator()->create_category(['parent' => $cat2->id]);
        $cat4 = $this->getDataGenerator()->create_category(['parent' => $cat3->id]);
        $course = $this->getDataGenerator()->create_course(['category' => $cat4->id]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        $modcontext = context_module::instance($forum->cmid);

        context_helper::reset_caches();

        // There should only be a single DB query.
        $predbqueries = $DB->perf_get_reads();

        $parents = $modcontext->get_parent_contexts();
        // Note: For some databases There is one read, plus one FETCH, plus one CLOSE.
        // These all show as reads, when there has actually only been a single query.
        $this->assertLessThanOrEqual(3, $DB->perf_get_reads() - $predbqueries);
    }

    /**
     * Ensure that get_with_capability_sql and get_with_capability_join respect context locking.
     *
     * @covers ::get_with_capability_join
     * @covers ::get_with_capability_sql
     */
    public function test_get_with_capability_sql_locked() {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        $cat1 = $generator->create_category();
        $cat2 = $generator->create_category();
        $cat1course1 = $generator->create_course(['category' => $cat1->id]);
        $cat1course1forum = $generator->create_module('forum', ['course' => $cat1course1]);

        $contexts = (object) [
            'system' => \context_system::instance(),
            'cat1' => \context_coursecat::instance($cat1->id),
            'cat2' => \context_coursecat::instance($cat2->id),
            'cat1course1' => \context_course::instance($cat1course1->id),
            'cat1course1forum' => \context_module::instance($cat1course1forum->cmid),
        ];

        // Test with the 'mod/forum:startdiscussion' capability.
        $caput = 'mod/forum:startdiscussion';

        // Create a test user.
        $uut = $generator->create_and_enrol($cat1course1, 'teacher');

        // Initially the user will be returned by get_users_by_capability.
        list($sql, $params) = get_with_capability_sql($contexts->cat1course1forum, $caput);
        $users = $DB->get_records_sql($sql, $params);
        $this->assertArrayHasKey($uut->id, $users);

        // Freezing the forum will remove the user.
        set_config('contextlocking', 1);
        $contexts->cat1course1forum->set_locked(true);
        list($sql, $params) = get_with_capability_sql($contexts->cat1course1forum, $caput);
        $users = $DB->get_records_sql($sql, $params);
        $this->assertArrayNotHasKey($uut->id, $users);

        // But not if context locking is disabled.
        set_config('contextlocking', 0);
        list($sql, $params) = get_with_capability_sql($contexts->cat1course1forum, $caput);
        $users = $DB->get_records_sql($sql, $params);
        $this->assertArrayHasKey($uut->id, $users);

        $contexts->cat1course1forum->set_locked(false);

        // Freezing the course will have the same effect.
        set_config('contextlocking', 1);
        $contexts->cat1course1->set_locked(true);
        list($sql, $params) = get_with_capability_sql($contexts->cat1course1forum, $caput);
        $users = $DB->get_records_sql($sql, $params);
        $this->assertArrayNotHasKey($uut->id, $users);

        // But not if context locking is disabled.
        set_config('contextlocking', 0);
        list($sql, $params) = get_with_capability_sql($contexts->cat1course1forum, $caput);
        $users = $DB->get_records_sql($sql, $params);
        $this->assertArrayHasKey($uut->id, $users);

        $contexts->cat1course1->set_locked(false);

        // Freezing the category will have the same effect.
        set_config('contextlocking', 1);
        $contexts->cat1->set_locked(true);
        list($sql, $params) = get_with_capability_sql($contexts->cat1course1forum, $caput);
        $users = $DB->get_records_sql($sql, $params);
        $this->assertArrayNotHasKey($uut->id, $users);

        // But not if context locking is disabled.
        set_config('contextlocking', 0);
        list($sql, $params) = get_with_capability_sql($contexts->cat1course1forum, $caput);
        $users = $DB->get_records_sql($sql, $params);
        $this->assertArrayHasKey($uut->id, $users);

        $contexts->cat1->set_locked(false);

        // Freezing an unrelated category will have no effect.
        set_config('contextlocking', 1);
        $contexts->cat2->set_locked(true);
        list($sql, $params) = get_with_capability_sql($contexts->cat1course1forum, $caput);
        $users = $DB->get_records_sql($sql, $params);
        $this->assertArrayHasKey($uut->id, $users);
    }

    /**
     * Ensure that get_users_by_capability respects context freezing.
     *
     * @covers ::get_users_by_capability
     */
    public function test_get_users_by_capability_locked() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        $cat1 = $generator->create_category();
        $cat2 = $generator->create_category();
        $cat1course1 = $generator->create_course(['category' => $cat1->id]);
        $cat1course1forum = $generator->create_module('forum', ['course' => $cat1course1]);

        $contexts = (object) [
            'system' => \context_system::instance(),
            'cat1' => \context_coursecat::instance($cat1->id),
            'cat2' => \context_coursecat::instance($cat2->id),
            'cat1course1' => \context_course::instance($cat1course1->id),
            'cat1course1forum' => \context_module::instance($cat1course1forum->cmid),
        ];

        // Test with the 'mod/forum:startdiscussion' capability.
        $caput = 'mod/forum:startdiscussion';

        // Create a test user.
        $uut = $generator->create_and_enrol($cat1course1, 'teacher');

        // Initially the user will be returned by get_users_by_capability.
        $users = get_users_by_capability($contexts->cat1course1forum, $caput);
        $this->assertArrayHasKey($uut->id, $users);

        // Freezing the forum will remove the user.
        set_config('contextlocking', 1);
        $contexts->cat1course1forum->set_locked(true);
        $users = get_users_by_capability($contexts->cat1course1forum, $caput);
        $this->assertArrayNotHasKey($uut->id, $users);

        // But not if context locking is disabled.
        set_config('contextlocking', 0);
        $users = get_users_by_capability($contexts->cat1course1forum, $caput);
        $this->assertArrayHasKey($uut->id, $users);

        $contexts->cat1course1forum->set_locked(false);

        // Freezing the course will have the same effect.
        set_config('contextlocking', 1);
        $contexts->cat1course1->set_locked(true);
        $users = get_users_by_capability($contexts->cat1course1forum, $caput);
        $this->assertArrayNotHasKey($uut->id, $users);

        // But not if context locking is disabled.
        set_config('contextlocking', 0);
        $users = get_users_by_capability($contexts->cat1course1forum, $caput);
        $this->assertArrayHasKey($uut->id, $users);

        $contexts->cat1course1->set_locked(false);

        // Freezing the category will have the same effect.
        set_config('contextlocking', 1);
        $contexts->cat1->set_locked(true);
        $users = get_users_by_capability($contexts->cat1course1forum, $caput);
        $this->assertArrayNotHasKey($uut->id, $users);

        // But not if context locking is disabled.
        set_config('contextlocking', 0);
        $users = get_users_by_capability($contexts->cat1course1forum, $caput);
        $this->assertArrayHasKey($uut->id, $users);

        $contexts->cat1->set_locked(false);

        // Freezing an unrelated category will have no effect.
        set_config('contextlocking', 1);
        $contexts->cat2->set_locked(true);
        $users = get_users_by_capability($contexts->cat1course1forum, $caput);
        $this->assertArrayHasKey($uut->id, $users);
    }

    /**
     * Test require_all_capabilities.
     *
     * @covers ::require_all_capabilities
     */
    public function test_require_all_capabilities() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);

        // Note: Here are used default capabilities, the full test is in permission evaluation bellow,
        // use two capabilities that teacher has and one does not, none of them should be allowed for not-logged-in user.
        $this->assertTrue($DB->record_exists('capabilities', array('name' => 'moodle/backup:backupsection')));
        $this->assertTrue($DB->record_exists('capabilities', array('name' => 'moodle/backup:backupcourse')));

        $sca = array('moodle/backup:backupsection', 'moodle/backup:backupcourse');

        $this->setUser($teacher);
        require_all_capabilities($sca, $coursecontext);
        require_all_capabilities($sca, $coursecontext, $teacher);

        // Guest users should not have any of these perms.
        $this->setUser(0);
        $this->expectException(\required_capability_exception::class);
        require_all_capabilities($sca, $coursecontext);
    }

    /**
     * Test get_navigation_filter_context.
     *
     * @covers ::get_navigation_filter_context
     */
    public function test_get_navigation_filter_context() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        set_config('filternavigationwithsystemcontext', 0);
        // First test passed values are returned if disabled.
        $this->assertNull(context_helper::get_navigation_filter_context(null));
        $coursecontext = context_course::instance($course->id);
        $filtercontext = context_helper::get_navigation_filter_context($coursecontext);
        $this->assertEquals($coursecontext->id, $filtercontext->id);

        // Now test that any input returns system context if enabled.
        set_config('filternavigationwithsystemcontext', 1);
        $filtercontext = context_helper::get_navigation_filter_context(null);
        $this->assertInstanceOf('\context_system', $filtercontext);
        $filtercontext = context_helper::get_navigation_filter_context($coursecontext);
        $this->assertInstanceOf('\context_system', $filtercontext);
    }
}

/**
 * Context caching fixture
 */
class context_inspection extends context_helper {
    public static function test_context_cache_size() {
        return self::$cache_count;
    }
}
