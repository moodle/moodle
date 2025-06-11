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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

use block_quickmail\repos\user_repo;

class block_quickmail_user_repo_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        assigns_mentors;

    public function test_get_course_user_selectable_users() {
        $this->resetAfterTest(true);

        list($course, $coursecontext, $enrolledusers, $groups) = $this->create_course_with_users_and_groups();

        // Manager: 0.
        // Coursecreator: 0.
        // Editingteacher: 1.
        // Teacher: 3.
        // Student: 40.
        // Guest: 0.
        // User: 0.
        // Frontpage: 0.
        $teacher = $enrolledusers['teacher'][0];

        // Should have access to all users.
        $editingteacher = $enrolledusers['editingteacher'][0];
        $student = $enrolledusers['student'][0];

        $users = user_repo::get_course_user_selectable_users($course, $editingteacher, $coursecontext);

        $firstuser = current($users);

        $this->assertIsArray($users);
        $this->assertCount(44, $users);
        $this->assertArrayHasKey($editingteacher->id, $users);
        $this->assertArrayHasKey($student->id, $users);
        $this->assertIsObject($firstuser);
        $this->assertObjectHasAttribute('id', $firstuser);
        $this->assertObjectHasAttribute('firstname', $firstuser);
        $this->assertObjectHasAttribute('lastname', $firstuser);

        // Should have limited access.
        $users = user_repo::get_course_user_selectable_users($course, $student, $coursecontext);

        $this->assertIsArray($users);
        $this->assertCount(22, $users);
    }

    public function test_get_course_users() {
        $this->resetAfterTest(true);

        list($course, $coursecontext, $enrolledusers, $groups) = $this->create_course_with_users_and_groups();

        $users = user_repo::get_course_users($coursecontext);

        $this->assertCount(44, $users);

        $firstuser = reset($users);

        $this->assertObjectHasAttribute('id', $firstuser);
        $this->assertObjectHasAttribute('firstname', $firstuser);
        $this->assertObjectHasAttribute('lastname', $firstuser);

        // Create course with enrolled users.
        list($course, $coursecontext, $users) = $this->setup_course_with_users();

        // Get users from course with no users.
        $users = user_repo::get_course_users($coursecontext);

        $this->assertCount(0, $users);
    }

    public function test_get_course_group_users() {
        $this->resetAfterTest(true);

        list($course, $coursecontext, $enrolledusers, $groups) = $this->create_course_with_users_and_groups();

        // Get red group users.
        $users = user_repo::get_course_group_users($coursecontext, $groups['red']->id);
        $this->assertCount(11, $users);

        $firstuser = reset($users);

        $this->assertObjectHasAttribute('id', $firstuser);
        $this->assertObjectHasAttribute('firstname', $firstuser);
        $this->assertObjectHasAttribute('lastname', $firstuser);

        // Get yellow group users.
        $users = user_repo::get_course_group_users($coursecontext, $groups['yellow']->id);
        $this->assertCount(15, $users);

        // Get blue group users.
        $users = user_repo::get_course_group_users($coursecontext, $groups['blue']->id);
        $this->assertCount(15, $users);

        // Create course with enrolled users.
        list($course, $coursecontext, $users) = $this->setup_course_with_users();

        // Get users from non-existent group.
        $users = user_repo::get_course_group_users($coursecontext, 123456);

        $this->assertCount(0, $users);
    }

    public function test_get_course_role_users() {
        $this->resetAfterTest(true);

        list($course, $coursecontext, $enrolledusers, $groups) = $this->create_course_with_users_and_groups();

        // Editingteacher id: 3.
        // Teacher id: 4.
        // Student id: 5.

        // Get editingteacher users.
        $users = user_repo::get_course_role_users($coursecontext, 3);
        $this->assertCount(1, $users);

        $firstuser = reset($users);

        $this->assertObjectHasAttribute('id', $firstuser);
        $this->assertObjectHasAttribute('firstname', $firstuser);
        $this->assertObjectHasAttribute('lastname', $firstuser);

        // Get teacher users.
        $users = user_repo::get_course_role_users($coursecontext, 4);
        $this->assertCount(3, $users);

        // Get student users.
        $users = user_repo::get_course_role_users($coursecontext, 5);
        $this->assertCount(40, $users);

        // Create course with no enrolled users.
        list($course, $coursecontext, $users) = $this->setup_course_with_users();

        // Get editingteacher users.
        $users = user_repo::get_course_role_users($coursecontext, 3);
        $this->assertCount(0, $users);

        // Get teacher users.
        $users = user_repo::get_course_role_users($coursecontext, 3);
        $this->assertCount(0, $users);

        // Get student users.
        $users = user_repo::get_course_role_users($coursecontext, 3);
        $this->assertCount(0, $users);
    }

    public function test_get_unique_course_user_ids_from_selected_entities_scenario_one() {
        $this->resetAfterTest(true);

        list($course, $coursecontext, $enrolledusers, $groups) = $this->create_course_with_users_and_groups();

        $editingteacher = $enrolledusers['editingteacher'][0];
        $student = $enrolledusers['student'][0];

        // Get posted includs/excludes.
        list($includedentityids, $excludedentityids) = $this->get_post_scenario_one($enrolledusers, $groups);

        $userids = user_repo::get_unique_course_user_ids_from_selected_entities($course,
                                                                                $editingteacher,
                                                                                $includedentityids,
                                                                                $excludedentityids);

        $this->assertCount(23, $userids);
    }

    public function test_get_unique_course_user_ids_from_selected_entities_scenario_two() {
        $this->resetAfterTest(true);

        list($course, $coursecontext, $enrolledusers, $groups) = $this->create_course_with_users_and_groups();

        $editingteacher = $enrolledusers['editingteacher'][0];
        $student = $enrolledusers['student'][0];

        // Get posted includs/excludes.
        list($includedentityids, $excludedentityids) = $this->get_post_scenario_two($enrolledusers, $groups);

        $userids = user_repo::get_unique_course_user_ids_from_selected_entities($course,
                                                                                $editingteacher,
                                                                                $includedentityids,
                                                                                $excludedentityids);

        $this->assertCount(10, $userids);
    }

    public function test_get_unique_course_user_ids_from_selected_entities_scenario_three() {
        $this->resetAfterTest(true);

        list($course, $coursecontext, $enrolledusers, $groups) = $this->create_course_with_users_and_groups();

        $editingteacher = $enrolledusers['editingteacher'][0];
        $student = $enrolledusers['student'][0];

        // Get posted includs/excludes.
        list($includedentityids, $excludedentityids) = $this->get_post_scenario_three($enrolledusers);

        $userids = user_repo::get_unique_course_user_ids_from_selected_entities($course,
                                                                                $editingteacher,
                                                                                $includedentityids,
                                                                                $excludedentityids);

        $this->assertCount(13, $userids);
    }

    public function test_get_unique_course_user_ids_from_selected_entities_scenario_four() {
        $this->resetAfterTest(true);

        list($course, $coursecontext, $enrolledusers, $groups) = $this->create_course_with_users_and_groups();

        $editingteacher = $enrolledusers['editingteacher'][0];
        $student = $enrolledusers['student'][0];

        // Get posted includs/excludes.
        list($includedentityids, $excludedentityids) = $this->get_post_scenario_four($enrolledusers);

        $userids = user_repo::get_unique_course_user_ids_from_selected_entities($course,
                                                                                $editingteacher,
                                                                                $includedentityids,
                                                                                $excludedentityids);

        $this->assertCount(15, $userids);
    }

    public function test_get_unique_course_user_ids_from_selected_entities_scenario_five() {
        $this->resetAfterTest(true);

        list($course, $coursecontext, $enrolledusers, $groups) = $this->create_course_with_users_and_groups();

        $editingteacher = $enrolledusers['editingteacher'][0];
        $student = $enrolledusers['student'][0];

        // Get posted includs/excludes.
        list($includedentityids, $excludedentityids) = $this->get_post_scenario_five($enrolledusers, $groups);

        $userids = user_repo::get_unique_course_user_ids_from_selected_entities($course,
                                                                                $editingteacher,
                                                                                $includedentityids,
                                                                                $excludedentityids);

        $this->assertCount(15, $userids);
    }

    public function test_get_mentors_of_user() {
        $this->resetAfterTest(true);

        list($course, $coursecontext, $enrolledusers, $groups) = $this->create_course_with_users_and_groups();

        // Pick the first student to be the mentee.
        $menteeuser = reset($enrolledusers['student']);

        // Attempt to fetch all mentors of this mentee (should be none).
        $mentorusers = user_repo::get_mentors_of_user($menteeuser);

        $this->assertCount(0, $mentorusers);

        // Create mentor for the mentee.
        $mentoruser = $this->create_mentor_for_user($menteeuser);

        $mentorusers = user_repo::get_mentors_of_user($menteeuser);

        $this->assertCount(1, $mentorusers);

        $firstmentor = reset($mentorusers);

        $this->assertObjectHasAttribute('id', $firstmentor);
        $this->assertObjectHasAttribute('firstname', $firstmentor);
        $this->assertObjectHasAttribute('lastname', $firstmentor);
    }

    // Helpers.
    // First 4 students from group "red" are in group "yellow" as well.
    // First 4 students from group "yellow" are in group "blue" as well.

    /**
     * This returns include and excluded "entity ids" (user_, role_, group_)
     *
     * This scenario will return included:
     * - 16 users (students)
     * - 1 group (yellow)
     * --- 15 users (1 teacher, 14 students)
     * --- 8 students already included
     *
     * This scenario will return excluded:
     * - none
     */
    private function get_post_scenario_one($enrolledusers, $groups) {
        $includedentityids = [];
        $excludedentityids = [];

        // Includes.
        // Include a specified amount of students from each group.
        $offset = 0;
        foreach ([3, 5, 8] as $amount) {
            // Pull amount of students from group.
            $includedstudents = array_slice($enrolledusers['student'], $offset, $amount);

            // Push with user_ prefix into included.
            $includedentityids = array_merge($includedentityids, array_map(function($user) {
                return 'user_' . $user->id;
            }, $includedstudents));

            // Skip to next group.
            $offset += 10;
        }

        // Include yellow group.
        $includedentityids = array_merge($includedentityids, ['group_' . $groups['yellow']->id]);

        return [$includedentityids, $excludedentityids];
    }

    /**
     * This returns include and excluded "entity ids" (user_, role_, group_)
     *
     * This scenario will return included:
     * - 12 users (students)
     * - 1 group (red)
     * --- 15 users (1 teacher, 14 students)
     * --- 8 students already included
     *
     * This scenario will return excluded:
     * - none
     */
    private function get_post_scenario_two($enrolledusers, $groups) {
        $includedentityids = [];
        $excludedentityids = [];

        // Includes.
        // Include a specified amount of students from each group.
        $offset = 0;
        foreach ([9, 1, 3] as $amount) {
            // Pull amount of students from group.
            $includedstudents = array_slice($enrolledusers['student'], $offset, $amount);

            // Push with user_ prefix into included.
            $includedentityids = array_merge($includedentityids, array_map(function($user) {
                return 'user_' . $user->id;
            }, $includedstudents));

            // Skip to next group.
            $offset += 10;
        }

        // Include red group.
        $includedentityids = array_merge($includedentityids, ['group_' . $groups['red']->id]);

        // Include blue group.
        $includedentityids = array_merge($includedentityids, ['group_' . $groups['blue']->id]);

        // Excludes.
        // Exclude red group.
        $excludedentityids = array_merge($excludedentityids, ['group_' . $groups['red']->id]);

        // Exclude yellow group.
        $excludedentityids = array_merge($excludedentityids, ['group_' . $groups['yellow']->id]);

        return [$includedentityids, $excludedentityids];
    }

    /**
     * This returns include and excluded "entity ids" (user_, role_, group_)
     *
     * This scenario will return included:
     * - 12 users (students)
     * - 1 role (editing teacher)
     * --- 1 user
     *
     * This scenario will return excluded:
     * - none
     */
    private function get_post_scenario_three($enrolledusers) {
        $includedentityids = [];
        $excludedentityids = [];

        // Includes.
        // Include a specified amount of students from each group.
        $offset = 0;
        foreach ([2, 4, 6] as $amount) {
            // Pull amount of students from group.
            $includedstudents = array_slice($enrolledusers['student'], $offset, $amount);

            // Push with user_ prefix into included.
            $includedentityids = array_merge($includedentityids, array_map(function($user) {
                return 'user_' . $user->id;
            }, $includedstudents));

            // Skip to next group.
            $offset += 10;
        }

        // Include editingteacher role.
        $includedentityids = array_merge($includedentityids, ['role_3']);

        return [$includedentityids, $excludedentityids];
    }

    /**
     * This returns: 15 net included users
     */
    private function get_post_scenario_four($enrolledusers) {
        $includedentityids = [];
        $excludedentityids = [];

        // Includes.
        // Include a specified amount of students from each group.
        $offset = 0;
        foreach ([2, 4, 6] as $amount) {
            // Pull amount of students from group.
            $includedstudents = array_slice($enrolledusers['student'], $offset, $amount);

            // Push with user_ prefix into included.
            $includedentityids = array_merge($includedentityids, array_map(function($user) {
                return 'user_' . $user->id;
            }, $includedstudents));

            // Skip to next group.
            $offset += 10;
        }

        // Include editingteacher role.
        $includedentityids = array_merge($includedentityids, ['role_3']);

        // Include teacher role.
        $includedentityids = array_merge($includedentityids, ['role_4']);

        // Excludes.
        // Exclude editingteacher role.
        $excludedentityids = array_merge($excludedentityids, ['role_3']);

        return [$includedentityids, $excludedentityids];
    }

    /**
     * This returns: 15 net included users
     */
    private function get_post_scenario_five($enrolledusers, $groups) {
        $includedentityids = [];
        $excludedentityids = [];

        // Includes.
        // Include a specified amount of students from each group.
        $offset = 0;
        foreach ([3, 10, 3] as $amount) {
            // Pull amount of students from group.
            $includedstudents = array_slice($enrolledusers['student'], $offset, $amount);

            // Push with user_ prefix into included.
            $includedentityids = array_merge($includedentityids, array_map(function($user) {
                return 'user_' . $user->id;
            }, $includedstudents));

            // Skip to next group.
            $offset += 10;
        }

        // Include teacher role.
        $includedentityids = array_merge($includedentityids, ['role_4']);

        // Excludes.
        // Exclude editingteacher role.
        $excludedentityids = array_merge($excludedentityids, ['role_3']);

        // Exclude red group.
        $excludedentityids = array_merge($excludedentityids, ['group_' . $groups['red']->id]);

        return [$includedentityids, $excludedentityids];
    }

}
