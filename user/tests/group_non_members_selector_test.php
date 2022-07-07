<?php
// This file is part of Moodle - https://moodle.org/
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
 * Unit tests for {@link group_non_members_selector} class.
 *
 * @package     core_user
 * @category    test
 * @copyright   2019 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/user/selector/lib.php');

/**
 * Unit tests for {@link group_non_members_selector} class.
 *
 * @package     core_user
 * @category    test
 * @copyright   2019 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_group_non_members_selector_testcase extends advanced_testcase {

    /**
     * Test find_users that only return group non members
     *
     * @throws coding_exception
     */
    public function test_find_users_only_return_group_non_member() {
        $this->resetAfterTest();

        // Create course.
        $course = $this->getDataGenerator()->create_course();

        // Create users.
        $user1 = $this->getDataGenerator()->create_user(['firstname' => 'User', 'lastname' => '1']);
        $user2 = $this->getDataGenerator()->create_user(['firstname' => 'User', 'lastname' => '2']);
        $user3 = $this->getDataGenerator()->create_user(['firstname' => 'User', 'lastname' => '3']);
        $user4 = $this->getDataGenerator()->create_user(['firstname' => 'User', 'lastname' => '4']);
        $user5 = $this->getDataGenerator()->create_user(['firstname' => 'User', 'lastname' => '5']);

        // Create group.
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        // Enroll users to course. Except User5.
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course->id);
        $this->getDataGenerator()->enrol_user($user4->id, $course->id);

        // Assign User1 to Group.
        $this->getDataGenerator()->create_group_member(['groupid' => $group->id, 'userid' => $user1->id]);

        // User1 and User5 will not exist in the result.
        // User2, User3 and User4 will exist in the result.
        $potentialmembersselector = new group_non_members_selector('addselect',
                ['groupid' => $group->id, 'courseid' => $course->id]);
        foreach ($potentialmembersselector->find_users('User') as $found) {
            $this->assertCount(3, $found);
            $this->assertArrayNotHasKey($user5->id, $found);
            $this->assertArrayNotHasKey($user1->id, $found);
            $this->assertArrayHasKey($user2->id, $found);
            $this->assertArrayHasKey($user3->id, $found);
            $this->assertArrayHasKey($user4->id, $found);
        }

        // Assign User2 to Group.
        $this->getDataGenerator()->create_group_member(['groupid' => $group->id, 'userid' => $user2->id]);

        // User1, User2 and User5 will not exist in the result.
        // User3 and User4 will exist in the result.
        $potentialmembersselector = new group_non_members_selector('addselect',
                ['groupid' => $group->id, 'courseid' => $course->id]);
        foreach ($potentialmembersselector->find_users('User') as $found) {
            $this->assertCount(2, $found);
            $this->assertArrayNotHasKey($user5->id, $found);
            $this->assertArrayNotHasKey($user1->id, $found);
            $this->assertArrayNotHasKey($user2->id, $found);
            $this->assertArrayHasKey($user3->id, $found);
            $this->assertArrayHasKey($user4->id, $found);
        }

        // Assign User3 to Group.
        $this->getDataGenerator()->create_group_member(['groupid' => $group->id, 'userid' => $user3->id]);

        // User1, User2, User3 and User5 will not exist in the result.
        // Only User4 will exist in the result.
        $potentialmembersselector = new group_non_members_selector('addselect',
                ['groupid' => $group->id, 'courseid' => $course->id]);
        foreach ($potentialmembersselector->find_users('User') as $found) {
            $this->assertCount(1, $found);
            $this->assertArrayNotHasKey($user5->id, $found);
            $this->assertArrayNotHasKey($user1->id, $found);
            $this->assertArrayNotHasKey($user2->id, $found);
            $this->assertArrayNotHasKey($user3->id, $found);
            $this->assertArrayHasKey($user4->id, $found);
        }
    }

}
