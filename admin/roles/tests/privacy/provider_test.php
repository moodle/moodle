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
 * Privacy test for core_role
 *
 * @package    core_role
 * @category   test
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_role\privacy;

defined('MOODLE_INTERNAL') || die();

use core_role\privacy\provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;
use core_privacy\local\request\transform;
use tool_cohortroles\api;
use core_privacy\local\request\approved_userlist;

/**
 * Privacy test for core_role
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {
    /**
     * Test to check export_user_preferences.
     * returns user preferences data.
     */
    public function test_export_user_preferences() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $showadvanced = 1;
        set_user_preference('definerole_showadvanced', $showadvanced);
        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $prefs = $writer->get_user_preferences('core_role');
        $this->assertEquals(transform::yesno($showadvanced), transform::yesno($prefs->definerole_showadvanced->value));
        $this->assertEquals(get_string('privacy:metadata:preference:showadvanced', 'core_role'),
            $prefs->definerole_showadvanced->description);
    }

    /**
     * Check all contexts are returned if there is any user data for this user.
     */
    public function test_get_contexts_for_userid() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $this->assertEmpty(provider::get_contexts_for_userid($user->id));

        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = \context_user::instance($user2->id);
        $course = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $coursecat = $this->getDataGenerator()->create_category();
        $cm = $this->getDataGenerator()->create_module('chat', ['course' => $course->id]);
        $cmcontext = \context_module::instance($cm->cmid);
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id));
        $cmcontext2 = \context_module::instance($page->cmid);
        $coursecontext = \context_course::instance($course->id);
        $coursecontext2 = \context_course::instance($course2->id);
        $coursecatcontext = \context_coursecat::instance($coursecat->id);
        $systemcontext = \context_system::instance();
        $block = $this->getDataGenerator()->create_block('online_users');
        $blockcontext = \context_block::instance($block->id);

        $student = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $manager = $DB->get_record('role', array('shortname' => 'manager'), '*', MUST_EXIST);

        // Role assignments, where the user is assigned.
        role_assign($student->id, $user->id, $cmcontext2->id);
        role_assign($student->id, $user->id, $coursecontext2->id);
        role_assign($student->id, $user->id, $blockcontext->id);
        role_assign($manager->id, $user->id, $usercontext2->id);
        // Role assignments, where the user makes assignments.
        $this->setUser($user);
        role_assign($student->id, $user2->id, $coursecontext->id);
        role_assign($manager->id, $user2->id, $coursecatcontext->id);
        role_assign($manager->id, $user2->id, $systemcontext->id);

        // Role capabilities.
        $this->setUser($user);
        $result = assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $student->id, $cmcontext->id);

        $contextlist = provider::get_contexts_for_userid($user->id)->get_contextids();
        $this->assertCount(8, $contextlist);
        $this->assertTrue(in_array($cmcontext->id, $contextlist));
    }

    /**
     * Test that user data is exported correctly.
     */
    public function test_export_user_data() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = \context_user::instance($user2->id);
        $course = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $coursecat = $this->getDataGenerator()->create_category();
        $cm = $this->getDataGenerator()->create_module('chat', ['course' => $course->id]);
        $cmcontext = \context_module::instance($cm->cmid);
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id));
        $cmcontext2 = \context_module::instance($page->cmid);
        $coursecontext = \context_course::instance($course->id);
        $coursecontext2 = \context_course::instance($course2->id);
        $coursecatcontext = \context_coursecat::instance($coursecat->id);
        $systemcontext = \context_system::instance();
        $block = $this->getDataGenerator()->create_block('online_users');
        $blockcontext = \context_block::instance($block->id);

        $student = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $manager = $DB->get_record('role', array('shortname' => 'manager'), '*', MUST_EXIST);
        $rolesnames = self::get_roles_name();

        $subcontextstudent = [
            get_string('privacy:metadata:role_assignments', 'core_role'),
            $rolesnames[$student->id]
        ];
        $subcontextmanager = [
            get_string('privacy:metadata:role_assignments', 'core_role'),
            $rolesnames[$manager->id]
        ];
        $subcontextrc = [
            get_string('privacy:metadata:role_capabilities', 'core_role'),
            $rolesnames[$student->id]
        ];

        // Test over role assignments.
        // Where the user is assigned.
        role_assign($student->id, $user->id, $cmcontext2->id);
        role_assign($student->id, $user->id, $coursecontext2->id);
        role_assign($student->id, $user->id, $blockcontext->id);
        role_assign($manager->id, $user->id, $usercontext2->id);
        // Where the user makes assignments.
        $this->setUser($user);
        role_assign($manager->id, $user2->id, $coursecatcontext->id);
        role_assign($manager->id, $user2->id, $systemcontext->id);

        // Test overridable roles in module, course, category, user, system and block.
        assign_capability('moodle/backup:backupactivity', CAP_ALLOW, $student->id, $cmcontext->id, true);
        assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $student->id, $coursecontext->id, true);
        assign_capability('moodle/category:manage', CAP_ALLOW, $student->id, $coursecatcontext->id, true);
        assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $student->id, $systemcontext->id, true);
        assign_capability('moodle/block:edit', CAP_ALLOW, $student->id, $blockcontext->id, true);
        assign_capability('moodle/competency:evidencedelete', CAP_ALLOW, $student->id, $usercontext2->id, true);

        // Retrieve the user's context ids.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $approvedcontextlist = new approved_contextlist($user, 'core_role', $contextlist->get_contextids());

        $strpermissions = array(
            CAP_INHERIT => get_string('inherit', 'role'),
            CAP_ALLOW => get_string('allow', 'role'),
            CAP_PREVENT => get_string('prevent', 'role'),
            CAP_PROHIBIT => get_string('prohibit', 'role')
        );
        // Retrieve role capabilities and role assignments.
        provider::export_user_data($approvedcontextlist);
        foreach ($contextlist as $context) {
            $writer = writer::with_context($context);
            $this->assertTrue($writer->has_any_data());
            if ($context->contextlevel == CONTEXT_MODULE) {
                if ($data = $writer->get_data($subcontextstudent)) {
                    $this->assertEquals($user->id, reset($data)->userid);
                }
                if ($data = $writer->get_data($subcontextrc)) {
                    $this->assertEquals('moodle/backup:backupactivity', reset($data)->capability);
                    $this->assertEquals($strpermissions[CAP_ALLOW], reset($data)->permission);
                }
            }
            if ($context->contextlevel == CONTEXT_COURSE) {
                if ($data = $writer->get_data($subcontextstudent)) {
                    $this->assertEquals($user->id, reset($data)->userid);
                }
                if ($data = $writer->get_data($subcontextrc)) {
                    $this->assertEquals('moodle/backup:backupcourse', reset($data)->capability);
                }
            }
            if ($context->contextlevel == CONTEXT_COURSECAT) {
                if ($data = $writer->get_data($subcontextmanager)) {
                    $this->assertEquals($user->id, reset($data)->modifierid);
                }
                if ($data = $writer->get_data($subcontextrc)) {
                    $this->assertEquals('moodle/category:manage', reset($data)->capability);
                }
            }
            if ($context->contextlevel == CONTEXT_SYSTEM) {
                if ($data = $writer->get_data($subcontextmanager)) {
                    $this->assertEquals($user->id, reset($data)->modifierid);
                }
                if ($data = $writer->get_data($subcontextrc)) {
                    $this->assertEquals('moodle/backup:backupcourse', reset($data)->capability);
                }
            }
            if ($context->contextlevel == CONTEXT_BLOCK) {
                if ($data = $writer->get_data($subcontextstudent)) {
                    $this->assertEquals($user->id, reset($data)->userid);
                }
                if ($data = $writer->get_data($subcontextrc)) {
                    $this->assertEquals('moodle/block:edit', reset($data)->capability);
                }
            }
            if ($context->contextlevel == CONTEXT_USER) {
                if ($data = $writer->get_data($subcontextmanager)) {
                    $this->assertEquals($user->id, reset($data)->userid);
                }
                if ($data = $writer->get_data($subcontextrc)) {
                    $this->assertEquals('moodle/competency:evidencedelete', reset($data)->capability);
                }
            }
        }
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = \context_user::instance($user2->id);
        $user3 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $coursecat = $this->getDataGenerator()->create_category();
        $coursecatcontext = \context_coursecat::instance($coursecat->id);
        $systemcontext = \context_system::instance();
        $cm = $this->getDataGenerator()->create_module('chat', ['course' => $course->id]);
        $cmcontext = \context_module::instance($cm->cmid);
        $student = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $manager = $DB->get_record('role', array('shortname' => 'manager'), '*', MUST_EXIST);
        $block = $this->getDataGenerator()->create_block('online_users');
        $blockcontext = \context_block::instance($block->id);

        // Role assignments CONTEXT_COURSE.
        role_assign($student->id, $user->id, $coursecontext->id);
        role_assign($student->id, $user2->id, $coursecontext->id);
        role_assign($student->id, $user3->id, $coursecontext->id);
        $count = $DB->count_records('role_assignments', ['contextid' => $coursecontext->id]);
        $this->assertEquals(3, $count);
        // Role assignments CONTEXT_COURSECAT.
        role_assign($student->id, $user2->id, $coursecatcontext->id);
        role_assign($student->id, $user3->id, $coursecatcontext->id);
        $count = $DB->count_records('role_assignments', ['contextid' => $coursecatcontext->id]);
        $this->assertEquals(2, $count);
        // Role assignments CONTEXT_SYSTEM.
        role_assign($student->id, $user->id, $systemcontext->id);
        $count = $DB->count_records('role_assignments', ['contextid' => $systemcontext->id]);
        $this->assertEquals(1, $count);
        // Role assignments CONTEXT_MODULE.
        role_assign($student->id, $user->id, $cmcontext->id);
        $count = $DB->count_records('role_assignments', ['contextid' => $cmcontext->id]);
        $this->assertEquals(1, $count);
        // Role assigments CONTEXT_BLOCK.
        role_assign($student->id, $user->id, $blockcontext->id);
        $count = $DB->count_records('role_assignments', ['contextid' => $blockcontext->id]);
        $this->assertEquals(1, $count);
        // Role assigments CONTEXT_USER.
        role_assign($manager->id, $user->id, $usercontext2->id);
        $count = $DB->count_records('role_assignments', ['contextid' => $usercontext2->id]);
        $this->assertEquals(1, $count);

        // Delete data based on CONTEXT_COURSE context.
        provider::delete_data_for_all_users_in_context($coursecontext);
        // After deletion, the role_assignments entries for this context should have been deleted.
        $count = $DB->count_records('role_assignments', ['contextid' => $coursecontext->id]);
        $this->assertEquals(0, $count);
        // Check it is not removing data on other contexts.
        $count = $DB->count_records('role_assignments', ['contextid' => $coursecatcontext->id]);
        $this->assertEquals(2, $count);
        $count = $DB->count_records('role_assignments', ['contextid' => $systemcontext->id]);
        $this->assertEquals(1, $count);
        $count = $DB->count_records('role_assignments', ['contextid' => $cmcontext->id]);
        $this->assertEquals(1, $count);
        // Delete data based on CONTEXT_COURSECAT context.
        provider::delete_data_for_all_users_in_context($coursecatcontext);
        // After deletion, the role_assignments entries for this context should have been deleted.
        $count = $DB->count_records('role_assignments', ['contextid' => $coursecatcontext->id]);
        $this->assertEquals(0, $count);
        // Delete data based on CONTEXT_SYSTEM context.
        provider::delete_data_for_all_users_in_context($systemcontext);
        // After deletion, the role_assignments entries for this context should have been deleted.
        $count = $DB->count_records('role_assignments', ['contextid' => $systemcontext->id]);
        $this->assertEquals(0, $count);
        // Delete data based on CONTEXT_MODULE context.
        provider::delete_data_for_all_users_in_context($cmcontext);
        // After deletion, the role_assignments entries for this context should have been deleted.
        $count = $DB->count_records('role_assignments', ['contextid' => $cmcontext->id]);
        $this->assertEquals(0, $count);
        // Delete data based on CONTEXT_BLOCK context.
        provider::delete_data_for_all_users_in_context($usercontext2);
        // After deletion, the role_assignments entries for this context should have been deleted.
        $count = $DB->count_records('role_assignments', ['contextid' => $usercontext2->id]);
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = \context_user::instance($user2->id);
        $user3 = $this->getDataGenerator()->create_user();
        $usercontext3 = \context_user::instance($user3->id);
        $course = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $coursecontext2 = \context_course::instance($course2->id);
        $coursecontext3 = \context_course::instance($course3->id);
        $coursecat = $this->getDataGenerator()->create_category();
        $coursecatcontext = \context_coursecat::instance($coursecat->id);
        $systemcontext = \context_system::instance();
        $cm = $this->getDataGenerator()->create_module('chat', ['course' => $course->id]);
        $cmcontext = \context_module::instance($cm->cmid);
        $student = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $manager = $DB->get_record('role', array('shortname' => 'manager'), '*', MUST_EXIST);
        $block = $this->getDataGenerator()->create_block('online_users');
        $blockcontext = \context_block::instance($block->id);

        // Role assignments, Where the user is assigned.
        role_assign($student->id, $user->id, $coursecontext->id);
        role_assign($student->id, $user->id, $coursecontext2->id);
        role_assign($student->id, $user->id, $coursecatcontext->id);
        role_assign($student->id, $user->id, $cmcontext->id);
        role_assign($student->id, $user->id, $systemcontext->id);
        role_assign($student->id, $user->id, $blockcontext->id);
        role_assign($manager->id, $user->id, $usercontext2->id);
        role_assign($manager->id, $user->id, $usercontext3->id);
        $count = $DB->count_records('role_assignments', ['userid' => $user->id]);
        $this->assertEquals(8, $count);
        // Role assignments, where the user makes assignments.
        $this->setUser($user);
        role_assign($student->id, $user2->id, $coursecontext3->id);
        role_assign($student->id, $user3->id, $coursecontext3->id);
        $count = $DB->count_records('role_assignments', ['modifierid' => $user->id]);
        $this->assertEquals(2, $count);

        $contextlist = provider::get_contexts_for_userid($user->id);
        $approvedcontextlist = new approved_contextlist($user, 'core_role', $contextlist->get_contextids());
        provider::delete_data_for_user($approvedcontextlist);
        // After deletion, the role_assignments assigned to the user should have been deleted.
        $count = $DB->count_records('role_assignments', ['userid' => $user->id]);
        $this->assertEquals(0, $count);
        // After deletion, the role_assignments assigned by the user should not have been deleted.
        $count = $DB->count_records('role_assignments', ['modifierid' => $user->id]);
        $this->assertEquals(2, $count);
    }

    /**
     * Export for a user with a key against a script where no instance is specified.
     */
    public function test_export_user_role_to_cohort() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        // Assign user roles to cohort.
        $user = $this->getDataGenerator()->create_user();
        $contextuser = \context_user::instance($user->id);
        $teacher = $DB->get_record('role', array('shortname' => 'teacher'), '*', MUST_EXIST);
        $cohort = $this->getDataGenerator()->create_cohort();
        $userassignover = $this->getDataGenerator()->create_user();
        $contextuserassignover = \context_user::instance($userassignover->id);
        cohort_add_member($cohort->id, $userassignover->id);
        $this->setAdminUser();
        $params = (object) array(
            'userid' => $user->id,
            'roleid' => $teacher->id,
            'cohortid' => $cohort->id
        );
        api::create_cohort_role_assignment($params);
        api::sync_all_cohort_roles();
        $rolesnames = self::get_roles_name();
        $subcontextteacher = [
            get_string('privacy:metadata:role_cohortroles', 'core_role'),
            $rolesnames[$teacher->id]
        ];
        // Test User is assigned role teacher to cohort.
        provider::export_user_role_to_cohort($user->id);
        $writer = writer::with_context($contextuserassignover);
        $this->assertTrue($writer->has_any_data());
        $exported = $writer->get_related_data($subcontextteacher, 'cohortroles');
        $this->assertEquals($user->id, reset($exported)->userid);

        // Test User is member of a cohort which User2 is assigned to role to this cohort.
        $user2 = $this->getDataGenerator()->create_user();
        $cohort2 = $this->getDataGenerator()->create_cohort();
        cohort_add_member($cohort2->id, $user->id);
        $params = (object) array(
            'userid' => $user2->id,
            'roleid' => $teacher->id,
            'cohortid' => $cohort2->id
        );
        api::create_cohort_role_assignment($params);
        api::sync_all_cohort_roles();
        provider::export_user_role_to_cohort($user->id);
        $writer = writer::with_context($contextuser);
        $this->assertTrue($writer->has_any_data());
        $exported = $writer->get_related_data($subcontextteacher, 'cohortroles');
        $this->assertEquals($user2->id, reset($exported)->userid);
    }

    /**
     * Test for provider::delete_user_role_to_cohort().
     */
    public function test_delete_user_role_to_cohort() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        // Assign user roles to cohort.
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $teacher = $DB->get_record('role', array('shortname' => 'teacher'), '*', MUST_EXIST);
        $cohort = $this->getDataGenerator()->create_cohort();
        cohort_add_member($cohort->id, $user2->id);
        cohort_add_member($cohort->id, $user3->id);
        cohort_add_member($cohort->id, $user4->id);
        $this->setAdminUser();
        $params = (object) array(
            'userid' => $user->id,
            'roleid' => $teacher->id,
            'cohortid' => $cohort->id
        );
        api::create_cohort_role_assignment($params);
        api::sync_all_cohort_roles();

        $count = $DB->count_records('role_assignments', ['userid' => $user->id, 'component' => 'tool_cohortroles']);
        $this->assertEquals(3, $count);

        provider::delete_user_role_to_cohort($user->id);
        $count = $DB->count_records('role_assignments', ['userid' => $user->id, 'component' => 'tool_cohortroles']);
        $this->assertEquals(0, $count);
    }

    /**
     * Test that only users within a course context are fetched.
     */
    public function test_get_users_in_context() {
        global $DB;

        $this->resetAfterTest();

        $component = 'core_role';

        $this->setAdminUser();
        $admin = \core_user::get_user_by_username('admin');
        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        $usercontext1 = \context_user::instance($user1->id);
        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = \context_user::instance($user2->id);
        // Create course1.
        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        // Create course category.
        $coursecat = $this->getDataGenerator()->create_category();
        $coursecatcontext = \context_coursecat::instance($coursecat->id);
        // Create chat module.
        $cm = $this->getDataGenerator()->create_module('chat', ['course' => $course1->id]);
        $cmcontext = \context_module::instance($cm->cmid);

        $systemcontext = \context_system::instance();
        // Create a block.
        $block = $this->getDataGenerator()->create_block('online_users');
        $blockcontext = \context_block::instance($block->id);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'), '*', MUST_EXIST);

        // Role assignments CONTEXT_COURSE.
        role_assign($studentrole->id, $user1->id, $coursecontext1->id);
        role_assign($studentrole->id, $user2->id, $coursecontext1->id);
        // Role assignments CONTEXT_COURSECAT.
        role_assign($studentrole->id, $user2->id, $coursecatcontext->id);
        // Role assignments CONTEXT_SYSTEM.
        role_assign($studentrole->id, $user1->id, $systemcontext->id);
        // Role assignments CONTEXT_MODULE.
        role_assign($studentrole->id, $user2->id, $cmcontext->id);
        // Role assigments CONTEXT_BLOCK.
        role_assign($studentrole->id, $user1->id, $blockcontext->id);
        // Role assigments CONTEXT_USER.
        role_assign($managerrole->id, $user1->id, $usercontext2->id);

        // Role capabilities.
        $this->setUser($user1);
        assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $studentrole->id, $cmcontext->id);

        // The user list for usercontext1 should not return any users.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);
        // The user list for usercontext2 should user1 and admin (role creator).
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(2, $userlist2);
        $expected = [
            $user1->id,
            $admin->id
        ];
        $this->assertEqualsCanonicalizing($expected, $userlist2->get_userids());

        // The user list for coursecontext1 should user1, user2 and admin (role creator).
        $userlist3 = new \core_privacy\local\request\userlist($coursecontext1, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(3, $userlist3);
        $expected = [
            $user1->id,
            $user2->id,
            $admin->id
        ];
        $this->assertEqualsCanonicalizing($expected, $userlist3->get_userids());

        // The user list for coursecatcontext should user2 and admin (role creator).
        $userlist4 = new \core_privacy\local\request\userlist($coursecatcontext, $component);
        provider::get_users_in_context($userlist4);
        $this->assertCount(2, $userlist4);
        $expected = [
            $user2->id,
            $admin->id
        ];
        $this->assertEqualsCanonicalizing($expected, $userlist4->get_userids());

        // The user list for systemcontext should user1 and admin (role creator).
        $userlist6 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist6);
        $this->assertCount(2, $userlist6);
        $expected = [
            $user1->id,
            $admin->id
        ];
        $this->assertEqualsCanonicalizing($expected, $userlist6->get_userids());

        // The user list for cmcontext should user1, user2 and admin (role creator).
        $userlist7 = new \core_privacy\local\request\userlist($cmcontext, $component);
        provider::get_users_in_context($userlist7);
        $this->assertCount(3, $userlist7);
        $expected = [
            $user1->id,
            $user2->id,
            $admin->id
        ];
        $this->assertEqualsCanonicalizing($expected, $userlist7->get_userids());

        // The user list for blockcontext should user1 and admin (role creator).
        $userlist8 = new \core_privacy\local\request\userlist($blockcontext, $component);
        provider::get_users_in_context($userlist8);
        $this->assertCount(2, $userlist8);
        $expected = [
            $user1->id,
            $admin->id
        ];
        $this->assertEqualsCanonicalizing($expected, $userlist8->get_userids());
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {
        global $DB;

        $this->resetAfterTest();

        $component = 'core_role';

        $this->setAdminUser();
        $admin = \core_user::get_user_by_username('admin');
        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = \context_user::instance($user2->id);
        // Create course1.
        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        // Create course category.
        $coursecat = $this->getDataGenerator()->create_category();
        $coursecatcontext = \context_coursecat::instance($coursecat->id);
        // Create chat module.
        $cm = $this->getDataGenerator()->create_module('chat', ['course' => $course1->id]);
        $cmcontext = \context_module::instance($cm->cmid);

        $systemcontext = \context_system::instance();
        // Create a block.
        $block = $this->getDataGenerator()->create_block('online_users');
        $blockcontext = \context_block::instance($block->id);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'), '*', MUST_EXIST);

        // Role assignments CONTEXT_COURSE.
        role_assign($studentrole->id, $user1->id, $coursecontext1->id);
        role_assign($studentrole->id, $user2->id, $coursecontext1->id);
        // Role assignments CONTEXT_COURSECAT.
        role_assign($studentrole->id, $user2->id, $coursecatcontext->id);
        // Role assignments CONTEXT_SYSTEM.
        role_assign($studentrole->id, $user1->id, $systemcontext->id);
        // Role assignments CONTEXT_MODULE.
        role_assign($studentrole->id, $user2->id, $cmcontext->id);
        // Role assigments CONTEXT_BLOCK.
        role_assign($studentrole->id, $user1->id, $blockcontext->id);
        // Role assigments CONTEXT_USER.
        role_assign($managerrole->id, $user1->id, $usercontext2->id);

        // Role capabilities.
        $this->setUser($user1);
        assign_capability('moodle/backup:backupcourse', CAP_ALLOW, $studentrole->id, $cmcontext->id);

        // The user list for usercontext2 should user1 and admin (role creator).
        $userlist1 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(2, $userlist1);
        // The user list for coursecontext1 should user1, user2 and admin (role creator).
        $userlist2 = new \core_privacy\local\request\userlist($coursecontext1, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(3, $userlist2);
        // The user list for coursecatcontext should user2 and admin (role creator).
        $userlist3 = new \core_privacy\local\request\userlist($coursecatcontext, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(2, $userlist3);
        // The user list for systemcontext should user1 and admin (role creator).
        $userlist4 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist4);
        $this->assertCount(2, $userlist4);
        // The user list for cmcontext should user1, user2 and admin (role creator).
        $userlist5 = new \core_privacy\local\request\userlist($cmcontext, $component);
        provider::get_users_in_context($userlist5);
        $this->assertCount(3, $userlist5);
        // The user list for blockcontext should user1 and admin (role creator).
        $userlist6 = new \core_privacy\local\request\userlist($blockcontext, $component);
        provider::get_users_in_context($userlist6);
        $this->assertCount(2, $userlist6);

        // Convert $userlist1 into an approved_contextlist.
        $approvedlist1 = new approved_userlist($usercontext2, $component, $userlist1->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist1);
        // Re-fetch users in usercontext2.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);

        // Convert $userlist2 into an approved_contextlist.
        $approvedlist2 = new approved_userlist($coursecontext1, $component, $userlist2->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist2);
        // Re-fetch users in coursecontext1.
        $userlist2 = new \core_privacy\local\request\userlist($coursecontext1, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(0, $userlist2);

        // Convert $userlist3 into an approved_contextlist.
        $approvedlist3 = new approved_userlist($coursecatcontext, $component, $userlist3->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist3);
        // Re-fetch users in coursecatcontext.
        $userlist3 = new \core_privacy\local\request\userlist($coursecatcontext, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(0, $userlist3);

        // Convert $userlist4 into an approved_contextlist.
        $approvedlist4 = new approved_userlist($systemcontext, $component, $userlist4->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist4);
        // Re-fetch users in systemcontext.
        $userlist4 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist4);
        // The data from role_capabilities should still be present. The user list should return the admin user.
        $this->assertCount(1, $userlist4);
        $expected = [$admin->id];
        $this->assertEquals($expected, $userlist4->get_userids());

        // Convert $userlist5 into an approved_contextlist.
        $approvedlist5 = new approved_userlist($cmcontext, $component, $userlist5->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist5);
        // Re-fetch users in cmcontext.
        $userlist5 = new \core_privacy\local\request\userlist($cmcontext, $component);
        provider::get_users_in_context($userlist5);
        // The data from role_capabilities should still be present. The user list should return user1.
        $this->assertCount(1, $userlist5);
        $expected = [$user1->id];
        $this->assertEquals($expected, $userlist5->get_userids());

        // Convert $userlist6 into an approved_contextlist.
        $approvedlist6 = new approved_userlist($blockcontext, $component, $userlist6->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist6);
        // Re-fetch users in blockcontext.
        $userlist6 = new \core_privacy\local\request\userlist($blockcontext, $component);
        provider::get_users_in_context($userlist6);
        $this->assertCount(0, $userlist6);
    }

    /**
     * Supoort function to get all the localised roles name
     * in a simple array for testing.
     *
     * @return array Array of name of the roles by roleid.
     */
    protected static function get_roles_name() {
        $roles = role_fix_names(get_all_roles(), \context_system::instance(), ROLENAME_ORIGINAL);
        $rolesnames = array();
        foreach ($roles as $role) {
            $rolesnames[$role->id] = $role->localname;
        }
        return $rolesnames;
    }
}
