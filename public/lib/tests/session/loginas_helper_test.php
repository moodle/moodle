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

namespace core\session;

use core\context\course as context_course;
use core\context\system as context_system;

/**
 * Unit tests for loginas_helper class.
 *
 * @package core
 * @author Jason den Dulk <jasondendulk@catalyst-au.net>
 * @covers \core\session\loginas_helper
 * @copyright 2025 Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class loginas_helper_test extends \advanced_testcase {
    /**
     * Tests various users wanting to login as other users of the same role.
     */
    public function test_loginas_same_role(): void {
        global $CFG, $DB;

        $this->resetAfterTest();

        $managerrole = $DB->get_field('role', 'id', ['shortname' => 'manager']);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // By default, users cannot login as other users.
        $this->assertNull(loginas_helper::get_context_user_can_login_as($user1, $user2));
        $this->assertNull(loginas_helper::get_context_user_can_login_as($user2, $user1));

        // Admins can login as other admins.
        $originalsiteadmins = $CFG->siteadmins;
        $CFG->siteadmins .= ',' . $user1->id . ',' . $user2->id;
        $systemcontext = context_system::instance();
        $this->assertEquals($systemcontext, loginas_helper::get_context_user_can_login_as($user1, $user2));
        $this->assertEquals($systemcontext, loginas_helper::get_context_user_can_login_as($user2, $user1));

        // Managers can login as other managers.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        role_assign($managerrole, $user1->id, $systemcontext->id);
        role_assign($managerrole, $user2->id, $systemcontext->id);

        $this->assertEquals($systemcontext, loginas_helper::get_context_user_can_login_as($user1, $user2));
        $this->assertEquals($systemcontext, loginas_helper::get_context_user_can_login_as($user2, $user1));

        // Course managers can login as other course managers, but only in the course context.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'manager');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'manager');

        $this->assertNull(loginas_helper::get_context_user_can_login_as($user1, $user2));
        $this->assertNull(loginas_helper::get_context_user_can_login_as($user2, $user1));

        $this->assertEquals($coursecontext, loginas_helper::get_context_user_can_login_as($user1, $user2, $course));
        $this->assertEquals($coursecontext, loginas_helper::get_context_user_can_login_as($user2, $user1, $course));

        // Students cannot login as another student.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');

        $this->assertNull(loginas_helper::get_context_user_can_login_as($user1, $user2));
        $this->assertNull(loginas_helper::get_context_user_can_login_as($user2, $user1));

        $this->assertNull(loginas_helper::get_context_user_can_login_as($user1, $user2, $course));
        $this->assertNull(loginas_helper::get_context_user_can_login_as($user2, $user1, $course));
    }

    /**
     * Tests trying to login as a deleted user.
     */
    public function test_loginas_deleted_user(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Sanity check that ordinary login as works.
        $user = $this->getDataGenerator()->create_user();
        $this->assertEquals(context_system::instance(), loginas_helper::get_context_user_can_login_as($USER, $user));

        // Cannot login as a user that has been deleted.
        $user = $this->getDataGenerator()->create_user(['deleted' => true]);
        $this->assertNull(loginas_helper::get_context_user_can_login_as($USER, $user));
    }

    /**
     * Tests various users wanting to login as other users of differing roles.
     */
    public function test_loginas_different_roles(): void {
        global $CFG, $DB;

        $this->resetAfterTest();

        $systemcontext = context_system::instance();
        $managerrole = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        $originalsiteadmins = $CFG->siteadmins;

        $users = [];
        for ($i = 0; $i < 11; ++$i) {
            $users[] = $this->getDataGenerator()->create_user();
        }

        $courses = [
            $this->getDataGenerator()->create_course(),
            $this->getDataGenerator()->create_course(),
        ];
        $coursecontexts = [
            context_course::instance($courses[0]->id),
            context_course::instance($courses[1]->id),
        ];

        // User 0 is an admin.
        $CFG->siteadmins .= ',' . $users[0]->id;

        // User 1 is a manager.
        role_assign($managerrole, $users[1]->id, $systemcontext->id);

        // User 2 is a manager and an admin.
        $CFG->siteadmins .= ',' . $users[2]->id;
        role_assign($managerrole, $users[2]->id, $systemcontext->id);

        // User 3 is a course manager for course 0.
        $this->getDataGenerator()->enrol_user($users[3]->id, $courses[0]->id, 'manager');

        // User 4 is a course manager for course 0 and a site manager.
        $this->getDataGenerator()->enrol_user($users[4]->id, $courses[0]->id, 'manager');
        role_assign($managerrole, $users[4]->id, $systemcontext->id);

        // User 5 is a course manager for course 0 and an admin.
        $this->getDataGenerator()->enrol_user($users[5]->id, $courses[0]->id, 'manager');
        $CFG->siteadmins .= ',' . $users[5]->id;

        // User 6 is a student for course 0.
        $this->getDataGenerator()->enrol_user($users[6]->id, $courses[0]->id, 'student');

        // User 7 is a student for course 0 and an admin.
        $this->getDataGenerator()->enrol_user($users[7]->id, $courses[0]->id, 'student');
        $CFG->siteadmins .= ',' . $users[7]->id;

        // User 8 is a course manager for course 1.
        $this->getDataGenerator()->enrol_user($users[8]->id, $courses[1]->id, 'manager');

        // User 9 is a student for course 1.
        $this->getDataGenerator()->enrol_user($users[9]->id, $courses[1]->id, 'student');

        // User 10 is a user without courses or roles.

        // This matrix defines loginas expectations. 'S' = system context. # = course context. 'X' = nothing.
        $matrix = [
            // ... 0    1    2    3    4    5    6    7    8    9    10.
            0 => ['X', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S'],
            1 => ['X', 'X', 'X', 'S', 'S', 'X', 'S', 'X', 'S', 'S', 'S'],
            2 => ['S', 'S', 'X', 'S', 'S', 'S', 'S', 'S', 'S', 'S', 'S'],
            3 => ['X', 'X', 'X', 'X', 'X', 'X', '0', 'X', 'X', 'X', 'X'],
            4 => ['X', 'S', 'X', 'S', 'X', 'X', 'S', 'X', 'S', 'S', 'S'],
            5 => ['S', 'S', 'S', 'S', 'S', 'X', 'S', 'S', 'S', 'S', 'S'],
            6 => ['X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X'],
            7 => ['S', 'S', 'S', 'S', 'S', 'S', 'S', 'X', 'S', 'S', 'S'],
            8 => ['X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', '1', 'X'],
            9 => ['X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X'],
            10 => ['X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X', 'X'],
        ];

        // Now test each user against each other user, and compare the results to the expectation matrix.
        foreach ($users as $uid => $user) {
            foreach ($users as $oid => $other) {
                $resultsnocourse = loginas_helper::get_context_user_can_login_as($user, $other);
                $resultscourse0 = loginas_helper::get_context_user_can_login_as($user, $other, $courses[0]);
                $resultscourse1 = loginas_helper::get_context_user_can_login_as($user, $other, $courses[1]);

                switch ($matrix[$uid][$oid]) {
                    case 'X':
                        // No loginas is possible.
                        $this->assertNull($resultsnocourse);
                        $this->assertNull($resultscourse0);
                        $this->assertNull($resultscourse1);
                        break;
                    case 'S':
                        // Loginas can happen at the site level.
                        $this->assertEquals($systemcontext, $resultsnocourse);
                        $this->assertEquals($systemcontext, $resultscourse0);
                        $this->assertEquals($systemcontext, $resultscourse1);
                        break;
                    case '0':
                        // Loginas can only happen within the context of course 0.
                        $this->assertNull($resultsnocourse);
                        $this->assertEquals($coursecontexts[0], $resultscourse0);
                        $this->assertNull($resultscourse1);
                        break;
                    case '1':
                        // Loginas can only happen within the context of course 1.
                        $this->assertNull($resultsnocourse);
                        $this->assertNull($resultscourse0);
                        $this->assertEquals($coursecontexts[1], $resultscourse1);
                        break;
                }
            }
        }
    }

    /**
     * Providor function for test_loginas_groups().
     *
     * @return array[]
     */
    public static function loginas_groups_providor(): array {
        return [
            'Separate groups' => [
                'groupmode' => SEPARATEGROUPS,
                'accessallgroups' => true,
                'canloginassamegroup' => true,
                'canloginasdifferentgroup' => true,
            ],
            'Separate groups, no access' => [
                'groupmode' => SEPARATEGROUPS,
                'accessallgroups' => false,
                'canloginassamegroup' => true,
                'canloginasdifferentgroup' => false,
            ],
            'Visible groups' => [
                'groupmode' => VISIBLEGROUPS,
                'accessallgroups' => true,
                'canloginassamegroup' => true,
                'canloginasdifferentgroup' => true,
            ],
            'Visible groups, no access' => [
                'groupmode' => VISIBLEGROUPS,
                'accessallgroups' => false,
                'canloginassamegroup' => true,
                'canloginasdifferentgroup' => true,
            ],
        ];
    }

    /**
     * Tests users wanting to login as other users of different groups.
     *
     * @param int $groupmode
     * @param bool $accessallgroups
     * @param bool $canloginassamegroup
     * @param bool $canloginasdifferentgroup
     *
     * @dataProvider loginas_groups_providor
     */
    public function test_loginas_groups(
        int $groupmode,
        bool $accessallgroups,
        bool $canloginassamegroup,
        bool $canloginasdifferentgroup
    ): void {
        global $DB;

        $this->resetAfterTest();

        // Set up manager and students.
        $manager = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course(['groupmode' => $groupmode]);
        $coursecontext = context_course::instance($course->id);

        // Add or remove accessallgroups permission.
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager'], MUST_EXIST);
        $permission = $accessallgroups ? CAP_ALLOW : CAP_PREVENT;
        assign_capability('moodle/site:accessallgroups', $permission, $managerroleid, $coursecontext, true);

        $this->getDataGenerator()->enrol_user($manager->id, $course->id, 'manager');
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');

        // Manager and student 1 are in the same group. Student 2 is in a different group.
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $this->getDataGenerator()->create_group_member(['groupid' => $group1->id, 'userid' => $manager->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $group1->id, 'userid' => $student1->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $group2->id, 'userid' => $student2->id]);

        // Manager wants to login as a student in the same group.
        $this->assertEquals(
            $canloginassamegroup,
            (bool) loginas_helper::get_context_user_can_login_as($manager, $student1, $course)
        );
        // Manager wants to login as a student in a different group.
        $this->assertEquals(
            $canloginasdifferentgroup,
            (bool) loginas_helper::get_context_user_can_login_as($manager, $student2, $course)
        );
    }
}
