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
use core_communication\processor as communication_processor;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../provider/matrix/tests/matrix_test_helper_trait.php');
require_once(__DIR__ . '/communication_test_helper_trait.php');

/**
 * Test communication helper methods.
 *
 * @package    core_communication
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_communication\helper
 */
class helper_test extends \advanced_testcase {
    use communication_test_helper_trait;
    use matrix_test_helper_trait;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setup_communication_configs();
        $this->initialise_mock_server();
    }

    /**
     * Test load_by_group.
     */
    public function test_load_by_group(): void {

        // As communication is created by default.
        $course = $this->get_course(
            extrafields: ['groupmode' => SEPARATEGROUPS],
        );
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $context = \context_course::instance(courseid: $course->id);

        $groupcommunication = helper::load_by_group(
            groupid: $group->id,
            context: $context,
        );
        $this->assertInstanceOf(
            expected: communication_processor::class,
            actual: $groupcommunication->get_processor(),
        );
    }

    /**
     * Test load_by_course.
     */
    public function test_load_by_course(): void {
        // As communication is created by default.
        $course = $this->get_course();
        $coursecontext = \context_course::instance(courseid: $course->id);
        $coursecommunication = helper::load_by_course(
            courseid: $course->id,
            context: $coursecontext,
        );
        $this->assertInstanceOf(
            expected: communication_processor::class,
            actual: $coursecommunication->get_processor(),
        );
    }

    /**
     * Test get_access_to_all_group_cap_users.
     */
    public function test_get_users_has_access_to_all_groups(): void {
        global $DB;
        // Set up the data with course, group, user etc.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->get_course();
        $coursecontext = \context_course::instance(courseid: $course->id);

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

        $allgroupaccessusers = helper::get_users_has_access_to_all_groups(
            userids: [$user1->id, $user2->id],
            courseid: $course->id,
        );
        $this->assertContains(
            needle: $user1->id,
            haystack: $allgroupaccessusers,
        );
        $this->assertNotContains(
            needle: $user2->id,
            haystack: $allgroupaccessusers,
        );
    }

    /**
     * Test update_communication_room_membership.
     */
    public function test_update_communication_room_membership(): void {
        global $DB;

        // Set up the data with course, group, user etc.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->get_course();
        $coursecontext = \context_course::instance(courseid: $course->id);
        $teacherrole = $DB->get_record(
            table: 'role',
            conditions: ['shortname' => 'manager'],
        );
        $this->getDataGenerator()->enrol_user(
            userid: $user->id,
            courseid: $course->id,
        );
        role_assign(
            roleid: $teacherrole->id,
            userid:$user->id,
            contextid: $coursecontext->id,
        );

        // Now remove members from room.
        helper::update_course_communication_room_membership(
            course: $course,
            userids: [$user->id],
            memberaction: 'remove_members_from_room',
        );

        // Now test that there is communication instances for the course and the user removed from that instance.
        $coursecommunication = helper::load_by_course(
            courseid: $course->id,
            context: $coursecontext,
        );

        // Check the user is added for course communication instance.
        $courseusers = $coursecommunication->get_processor()->get_all_delete_flagged_userids();
        $courseusers = reset($courseusers);
        $this->assertEquals(
            expected: $user->id,
            actual: $courseusers,
        );

        // Now add members to room.
        helper::update_course_communication_room_membership(
            course: $course,
            userids: [$user->id],
            memberaction: 'add_members_to_room',
        );

        $coursecommunication->reload();
        // Check the user is added for course communication instance.
        $courseusers = $coursecommunication->get_processor()->get_instance_userids();
        $courseusers = reset($courseusers);
        $this->assertEquals(
            expected: $user->id,
            actual: $courseusers,
        );

        // Now update membership.
        helper::update_course_communication_room_membership(
            course: $course,
            userids: [$user->id],
            memberaction: 'update_room_membership',
        );

        $coursecommunication->reload();
        // Check the user is added for course communication instance.
        $courseusers = $coursecommunication->get_processor()->get_instance_userids();
        $courseusers = reset($courseusers);
        $this->assertEquals(
            expected: $user->id,
            actual: $courseusers,
        );

        // Now try using invalid action.
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Invalid action provided.');
        helper::update_course_communication_room_membership(
            course: $course,
            userids: [$user->id],
            memberaction: 'a_funny_action',
        );
    }

    /**
     * Test format_group_room_name.
     */
    public function test_format_group_room_name(): void {
        $baseroomname = 'Course A';
        $groupname = 'Group 1';
        $formattedroomname = helper::format_group_room_name($baseroomname, $groupname);
        // Check the room name is formatted as expected.
        $this->assertEquals('Group 1 (Course A)', $formattedroomname);
    }
}
