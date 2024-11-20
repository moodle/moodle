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

namespace core_communication;

use communication_matrix\matrix_test_helper_trait;
use core_communication\task\add_members_to_room_task;
use core_communication\task\create_and_configure_room_task;
use core_communication\task\delete_room_task;
use core_communication\task\update_room_membership_task;
use core_communication\task\update_room_task;
use core_communication\processor as communication_processor;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../provider/matrix/tests/matrix_test_helper_trait.php');
require_once(__DIR__ . '/communication_test_helper_trait.php');

/**
 * Test communication hook listeners.
 *
 * @package    core_communication
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_communication\hook_listener
 */
class hook_listener_test extends \advanced_testcase {

    use communication_test_helper_trait;
    use matrix_test_helper_trait;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setup_communication_configs();
        $this->initialise_mock_server();
    }

    /**
     * Test create_group_communication.
     */
    public function test_create_update_delete_group_communication(): void {
        global $DB;

        $course = $this->get_course(
            roomname: 'Test room name',
            extrafields: ['groupmode' => SEPARATEGROUPS],
        );
        $coursecontext = \context_course::instance(courseid: $course->id);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Enrol user1 as teacher.
        $teacherrole = $DB->get_record(
            table: 'role',
            conditions: ['shortname' => 'manager'],
        );
        $this->getDataGenerator()->enrol_user(
            userid: $user1->id,
            courseid: $course->id,
        );
        role_assign(
            roleid: $teacherrole->id,
            userid: $user1->id,
            contextid: $coursecontext->id,
        );

        // Enrol user2 as student.
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user(
            userid: $user2->id,
            courseid: $course->id,
        );
        role_assign(
            roleid: $studentrole->id,
            userid: $user2->id,
            contextid: $coursecontext->id,
        );

        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $context = \context_course::instance($course->id);

        $groupcommunication = helper::load_by_group(
            groupid: $group->id,
            context: $context,
        );
        $this->assertInstanceOf(
            expected: communication_processor::class,
            actual: $groupcommunication->get_processor(),
        );

        $this->assertEquals(
            expected: $group->id,
            actual: $groupcommunication->get_processor()->get_instance_id(),
        );

        // Task to create room should be added.
        $adhoctask = \core\task\manager::get_adhoc_tasks(create_and_configure_room_task::class);
        $this->assertCount(1, $adhoctask);

        // Task to add members to room should not be there as the room is yet to be created.
        $adhoctask = \core\task\manager::get_adhoc_tasks(add_members_to_room_task::class);
        $this->assertCount(0, $adhoctask);

        // Only users with access to all groups should be added to the room at this point.
        $groupcommunicationusers = $groupcommunication->get_processor()->get_all_userids_for_instance();
        $this->assertEquals(
            expected: [$user1->id],
            actual: $groupcommunicationusers,
        );

        // Now delete all the ad-hoc tasks.
        $DB->delete_records('task_adhoc');

        // Now cann the update group but don't change the group name.
        groups_update_group($group);

        // No task should be added as nothing changed.
        $adhoctask = \core\task\manager::get_adhoc_tasks(update_room_task::class);
        $this->assertCount(0, $adhoctask);

        // Now change the group name.
        $group->name = 'Changed group name';
        groups_update_group($group);

        // Now one task should be there to update the group room name.
        $adhoctask = \core\task\manager::get_adhoc_tasks(update_room_task::class);
        $this->assertCount(1, $adhoctask);

        $groupcommunication->reload();
        $this->assertEquals(
            expected: 'Changed group name (Test room name)',
            actual: $groupcommunication->get_processor()->get_room_name(),
        );

        // Now delete the group.
        groups_delete_group($group->id);

        $adhoctask = \core\task\manager::get_adhoc_tasks(delete_room_task::class);
        $this->assertCount(1, $adhoctask);
    }

    /**
     * Test inactive users are not included when being mapped to a new communication instance.
     */
    public function test_inactive_users_are_not_mapped_to_new_communication(): void {
        // Create a course without a communication provider set.
        $course = $this->getDataGenerator()->create_course();

        // Enrol some users that are both active and inactive (suspended).
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user(
            userid: $user1->id,
            courseid: $course->id,
            roleidorshortname: 'teacher',
        );

        $this->getDataGenerator()->enrol_user(
            userid: $user2->id,
            courseid: $course->id,
            roleidorshortname: 'student',
        );

        $this->getDataGenerator()->enrol_user(
            userid: $user3->id,
            courseid: $course->id,
            roleidorshortname: 'teacher',
            status: ENROL_USER_SUSPENDED,
        );

        $this->getDataGenerator()->enrol_user(
            userid: $user4->id,
            courseid: $course->id,
            roleidorshortname: 'student',
            status: ENROL_USER_SUSPENDED,
        );

        // Set Matrix as the communication provider and update.
        $course->selectedcommunication = 'communication_matrix';
        $course->communication_matrixroomname = 'testroom';
        update_course($course);

        helper::update_course_communication_instance(
            course: $course,
            changesincoursecat: false,
        );

        // Load the communication instance and check that only the 2 active users are returned.
        $communication = helper::load_by_course(
            courseid: $course->id,
            context: \context_course::instance($course->id),
        );

        $userids = $communication->get_processor()->get_all_userids_for_instance();

        $this->assertEquals(
            expected: 2,
            actual: count($userids),
        );

        $this->assertContains(
            needle: $user1->id,
            haystack: $userids,
        );

        $this->assertContains(
            needle: $user2->id,
            haystack: $userids,
        );
    }

    /**
     * Test inactive users are not included when being mapped to a new communication instance using groups.
     */
    public function test_inactive_users_are_not_mapped_to_new_group_communication(): void {
        // Create a course without a communication provider set.
        $course = $this->getDataGenerator()->create_course(
            options: ['groupmode' => SEPARATEGROUPS],
        );

        // Enrol some users that are both active and inactive (suspended).
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user(
            userid: $user1->id,
            courseid: $course->id,
            roleidorshortname: 'teacher',
        );

        $this->getDataGenerator()->enrol_user(
            userid: $user2->id,
            courseid: $course->id,
            roleidorshortname: 'student',
        );

        $this->getDataGenerator()->enrol_user(
            userid: $user3->id,
            courseid: $course->id,
            roleidorshortname: 'teacher',
            status: ENROL_USER_SUSPENDED,
        );

        $this->getDataGenerator()->enrol_user(
            userid: $user4->id,
            courseid: $course->id,
            roleidorshortname: 'student',
            status: ENROL_USER_SUSPENDED,
        );

        // Create a group and add all users to it.
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_add_member(
            grouporid: $group,
            userorid: $user1,
        );
        groups_add_member(
            grouporid: $group,
            userorid: $user2,
        );
        groups_add_member(
            grouporid: $group,
            userorid: $user3,
        );
        groups_add_member(
            grouporid: $group,
            userorid: $user4,
        );

        // Set Matrix as the communication provider and update.
        $course->selectedcommunication = 'communication_matrix';
        $course->communication_matrixroomname = 'testroom';
        update_course($course);

        helper::update_group_communication_instances_for_course(
            course: $course,
            provider: 'communication_matrix',
        );

        // Load the communication instance and check that only the 2 active users are returned.
        $communication = helper::load_by_group(
            groupid: $group->id,
            context: \context_course::instance($course->id),
        );

        $userids = $communication->get_processor()->get_all_userids_for_instance();

        $this->assertEquals(
            expected: 2,
            actual: count($userids),
        );

        $this->assertContains(
            needle: $user1->id,
            haystack: $userids,
        );

        $this->assertContains(
            needle: $user2->id,
            haystack: $userids,
        );
    }

    /**
     * Test add_members_to_group_room.
     */
    public function test_add_members_to_group_room(): void {
        global $DB;

        $course = $this->get_course(
            extrafields: ['groupmode' => SEPARATEGROUPS],
        );
        $coursecontext = \context_course::instance(courseid: $course->id);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Enrol user1 as teacher.
        $teacherrole = $DB->get_record(
            table: 'role',
            conditions: ['shortname' => 'manager'],
        );
        $this->getDataGenerator()->enrol_user(
            userid: $user1->id,
            courseid: $course->id,
        );
        role_assign(
            roleid: $teacherrole->id,
            userid: $user1->id,
            contextid: $coursecontext->id,
        );

        // Enrol user2 as student.
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user(
            userid: $user2->id,
            courseid: $course->id,
        );
        role_assign(
            roleid: $studentrole->id,
            userid: $user2->id,
            contextid: $coursecontext->id,
        );

        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        // Now check if the teacher is added to the group room as the teacher has access to all groups.
        $context = \context_course::instance($course->id);
        $groupcommunication = helper::load_by_group(
            groupid: $group->id,
            context: $context,
        );

        // Now the communication instance should not have the student added yet.
        $this->assertNotContains(
            needle: $user2->id,
            haystack: $groupcommunication->get_processor()->get_all_userids_for_instance(),
        );

        groups_add_member(
            grouporid: $group,
            userorid: $user2,
        );

        // Now it should have the student.
        $this->assertContains(
            needle: $user2->id,
            haystack: $groupcommunication->get_processor()->get_all_userids_for_instance(),
        );
    }

    /**
     * Test if the course instances are created properly for course default provider.
     */
    public function test_course_default_provider(): void {
        $defaultprovider = 'communication_matrix';
        // Set the default communication for course.
        set_config(
            name: 'coursecommunicationprovider',
            value: $defaultprovider,
            plugin: 'moodlecourse',
        );

        // Test that the default communication is created for course mode.
        $course = $this->get_course();
        $coursecontext = \context_course::instance(courseid: $course->id);
        $coursecommunication = helper::load_by_course(
            courseid: $course->id,
            context: $coursecontext,
        );
        $this->assertEquals(
            expected: $defaultprovider,
            actual: $coursecommunication->get_provider(),
        );
        $this->assertEquals(
            expected: 'core_course',
            actual: $coursecommunication->get_processor()->get_component(),
        );
        $this->assertEquals(
            expected: $course->id,
            actual: $coursecommunication->get_processor()->get_instance_id(),
        );
    }

    /**
     * Test update_course_communication.
     */
    public function test_update_course_communication(): void {
        global $DB;

        // Set up the data with course, group, user etc.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->get_course();
        $group = $this->getDataGenerator()->create_group(record: ['courseid' => $course->id]);
        $coursecontext = \context_course::instance(courseid: $course->id);
        $teacherrole = $DB->get_record(
            table: 'role',
            conditions: ['shortname' => 'teacher'],
        );
        $this->getDataGenerator()->enrol_user(
            userid: $user->id,
            courseid: $course->id,
        );
        role_assign(
            roleid: $teacherrole->id,
            userid: $user->id,
            contextid: $coursecontext->id,
        );
        groups_add_member(
            grouporid: $group->id,
            userorid: $user->id,
        );

        // Now test that there is communication instances for the course and the user added for that instance.
        $coursecommunication = helper::load_by_course(
            courseid: $course->id,
            context: $coursecontext,
        );
        $this->assertInstanceOf(
            expected: communication_processor::class,
            actual: $coursecommunication->get_processor(),
        );

        // Check the user is added for course communication instance.
        $courseusers = $coursecommunication->get_processor()->get_all_userids_for_instance();
        $courseusers = reset($courseusers);
        $this->assertEquals(
            expected: $user->id,
            actual: $courseusers,
        );

        // Group should not have any instance yet.
        $groupcommunication = helper::load_by_group(
            groupid: $group->id,
            context: $coursecontext,
        );
        $this->assertNull(actual: $groupcommunication->get_processor());

        // Now update the course.
        $course->groupmode = SEPARATEGROUPS;
        $course->selectedcommunication = 'communication_matrix';
        update_course(data: $course);

        // Now there should be a group communication instance.
        $groupcommunication->reload();
        $this->assertInstanceOf(
            expected: communication_processor::class,
            actual: $groupcommunication->get_processor(),
        );

        // The course communication instance must be active.
        $coursecommunication->reload();
        $this->assertInstanceOf(
            expected: communication_processor::class,
            actual: $coursecommunication->get_processor(),
        );

        // All the course instance users must be marked as deleted.
        $coursecommunication->reload();
        $courseusers = $coursecommunication->get_processor()->get_all_delete_flagged_userids();
        $courseusers = reset($courseusers);
        $this->assertEquals(
            expected: $user->id,
            actual: $courseusers,
        );

        // Group instance should have the user.
        $groupusers = $groupcommunication->get_processor()->get_all_userids_for_instance();
        $groupusers = reset($groupusers);
        $this->assertEquals(
            expected: $user->id,
            actual: $groupusers,
        );

        // Now disable the communication instance for the course.
        $course->selectedcommunication = communication_processor::PROVIDER_NONE;
        update_course(data: $course);

        // Now both course and group instance should be disabled.
        $coursecommunication->reload();
        $this->assertNull(actual: $coursecommunication->get_processor());

        $groupcommunication->reload();
        $this->assertNull(actual: $groupcommunication->get_processor());
    }

    /**
     * Test create_course_communication_instance.
     */
    public function test_create_course_communication_instance(): void {
        $course = $this->get_course();
        $coursecontext = \context_course::instance(courseid: $course->id);
        $coursecommunication = helper::load_by_course(
            courseid: $course->id,
            context: $coursecontext,
        );

        $processor = $coursecommunication->get_processor();
        $this->assertEquals(
            expected: 'communication_matrix',
            actual: $processor->get_provider(),
        );
        $this->assertEquals(
            expected: 'Sampleroom',
            actual: $processor->get_room_name(),
        );
    }

    /**
     * Test delete_course_communication.
     */
    public function test_delete_course_communication(): void {
        $course = $this->get_course();
        delete_course(
            courseorid: $course,
            showfeedback: false,
        );

        $adhoctask = \core\task\manager::get_adhoc_tasks(delete_room_task::class);
        $this->assertCount(1, $adhoctask);
    }

    /**
     * Test update of room membership when user changes occur.
     */
    public function test_update_user_room_memberships(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $course = $this->get_course();
        $coursecontext = \context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        role_assign($teacherrole->id, $user->id, $coursecontext->id);

        $coursecommunication = helper::load_by_course($course->id, $coursecontext);
        $courseusers = $coursecommunication->get_processor()->get_all_userids_for_instance();
        $courseusers = reset($courseusers);
        $this->assertEquals($user->id, $courseusers);

        $user->suspended = 1;
        user_update_user($user, false);

        $coursecommunication->reload();
        $courseusers = $coursecommunication->get_processor()->get_all_delete_flagged_userids();
        $courseusers = reset($courseusers);
        $this->assertEquals($user->id, $courseusers);
    }

    /**
     * Test deletion of user room memberships when a user is deleted.
     */
    public function test_delete_user_room_memberships(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $course = $this->get_course();
        $coursecontext = \context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        role_assign($teacherrole->id, $user->id, $coursecontext->id);

        delete_user($user);
        $coursecommunication = helper::load_by_course($course->id, $coursecontext);
        $courseusers = $coursecommunication->get_processor()->get_all_userids_for_instance();
        $this->assertEmpty($courseusers);
    }

    /**
     * Test user room membership updates with role changes in a course.
     */
    public function test_update_user_membership_for_role_changes(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $course = $this->get_course();
        $coursecontext = \context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $adhoctask = \core\task\manager::get_adhoc_tasks(update_room_membership_task::class);
        $this->assertCount(1, $adhoctask);

        role_assign($teacherrole->id, $user->id, $coursecontext->id);

        $adhoctask = \core\task\manager::get_adhoc_tasks(update_room_membership_task::class);
        $this->assertCount(2, $adhoctask);
    }
}

