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

namespace mod_choice;

use context_module;

/**
 * Generator tests class.
 *
 * @package    mod_choice
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_choice\manager
 */
final class manager_test extends \advanced_testcase {
    /**
     * Set up the test environment.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test creating a manager instance from an instance record.
     *
     * @covers \mod_choice\manager::create_from_instance
     */
    public function test_create_manager_instance_from_instance_record(): void {
        $this->resetAfterTest();
        ['instance' => $instance] = $this->setup_users_and_activity();
        $manager = \mod_choice\manager::create_from_instance($instance);
        $manageractivity = $manager->get_instance();
        $this->assertEquals($instance->id, $manageractivity->id);
        $managercontext = $manager->get_context();
        $context = context_module::instance($instance->cmid);
        $this->assertEquals($context->id, $managercontext->id);
        $cm = get_coursemodule_from_id(manager::MODULE, $manageractivity->cmid, 0, false, MUST_EXIST);
        $this->assertEquals($cm->id, $manager->get_coursemodule()->id);
    }

    /**
     * Setup users and activity for testing answers retrieval.
     *
     * @param int $groupmode the group mode to use for the course.
     * @return array indexed array with 'users', 'course' and  'instance'.
     */
    private function setup_users_and_activity(int $groupmode = NOGROUPS): array {
        $db = \core\di::get(\moodle_database::class);
        $users = [];
        $generator = $this->getDataGenerator();
        $courseparams = [];
        if ($groupmode !== NOGROUPS) {
            // Set the group mode for the course.
            $courseparams['groupmode'] = $groupmode;
            $courseparams['groupmodeforce'] = 1; // Force the group mode.
        }
        $course = $generator->create_course($courseparams);
        $data = [
            's1' => 'student',
            's2' => 'student',
            's3' => 'student',
            't1' => 'teacher',
            't2' => 'teacher',
        ];
        foreach ($data as $username => $role) {
            $users[$username] = $generator->create_and_enrol($course, $role, ['username' => $username]);
        }

        $groups = [];
        if ($groupmode !== NOGROUPS) {
            // Create a group if the group mode is not NOGROUPS.
            $groups['g1'] = $generator->create_group(['courseid' => $course->id]);
            $groups['g2'] = $generator->create_group(['courseid' => $course->id]);
            // Add users to groups: s1, and t1 to group 1, s2 to group 2.
            groups_add_member($groups['g1'], $users['s1']->id);
            groups_add_member($groups['g2'], $users['s2']->id);
            groups_add_member($groups['g1'], $users['t1']->id);
        }
        $instance = $generator->create_module('choice', [
            'course' => $course,
            'option' => ['A', 'B', 'C'],
            'allowmultiple' => 1,
        ]);

        $choicesid = $db->get_records_menu('choice_options',  ['choiceid' => $instance->id], '', 'id, text');
        $choicestoid = array_flip($choicesid);
        $generator->get_plugin_generator('mod_choice')->create_response([
            'choiceid' => $instance->id,
            'responses' => [$choicestoid['A'], $choicestoid['B']], // Choose option A and B.
            'userid' => $users['s1']->id,
        ]);
        $generator->get_plugin_generator('mod_choice')->create_response([
            'choiceid' => $instance->id,
            'responses' => $choicestoid['B'], // Choose option B.
            'userid' => $users['s2']->id,
        ]);
        return [
            'users' => $users,
            'course' => $course,
            'instance' => $instance,
            'groups' => $groups,
        ];
    }

    /**
     * Test creating a manager instance from an instance record.
     *
     * @covers \mod_choice\manager::create_from_coursemodule
     */
    public function test_create_manager_from_coursemodule(): void {
        $this->resetAfterTest();
        ['instance' => $instance] = $this->setup_users_and_activity();
        $cm = get_coursemodule_from_instance('choice', $instance->id);
        $manager = \mod_choice\manager::create_from_coursemodule($cm);
        $managercm = $manager->get_coursemodule();
        $this->assertEquals($cm->id, $managercm->id);
        $managercontext = $manager->get_context();
        $context = context_module::instance($cm->id);
        $this->assertEquals($context->id, $managercontext->id);
    }

    /**
     * Test creating a manager instance from a course module.
     *
     * @covers \mod_choice\manager::create_from_coursemodule
     */
    public function test_create_manager_instance_from_coursemodule(): void {
        $this->resetAfterTest();
        ['instance' => $instance, 'course' => $course] = $this->setup_users_and_activity();
        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        $manager = \mod_choice\manager::create_from_coursemodule($cm);
        $this->assertNotNull($manager);
    }

    /**
     * Test retrieving answers count for all users.
     *
     * @param string $username the username of the user to retrieve answers count for.
     * @param int $coursegroupmode the group mode of the course.
     * @param string|null $currentgroup the current group for the user.
     * @param int $expectedcount the expected count of answers for the user.
     *
     * @covers       \mod_choice\manager::count_all_users_answered
     * @dataProvider provider_count_all_answers
     */
    public function test_count_all_users_answered(
        string $username,
        int $coursegroupmode,
        ?string $currentgroup,
        int $expectedcount
    ): void {
        global $SESSION;

        $db = \core\di::get(\moodle_database::class);
        [
            'users' => $users,
            'instance' => $instance,
            'course' => $course,
            'groups' => $groups,
        ] = $this->setup_users_and_activity($coursegroupmode);
        $manager = \mod_choice\manager::create_from_instance($instance);
        $this->setUser($users[$username]);
        if (!is_null($currentgroup) && $coursegroupmode !== NOGROUPS) {
            $group = $groups[$currentgroup];
            $SESSION->activegroup[$course->id][$coursegroupmode][$course->defaultgroupingid] = $group->id;
        }
        $count = $manager->count_all_users_answered();
        $this->assertEquals($expectedcount, $count);

        // Check answers count for each option.
        $options = $db->get_records_menu('choice_options', ['choiceid' => $instance->id], '', 'id, text');
        foreach ($options as $optionid => $optiontext) {
            $count = $manager->count_all_users_answered($optionid);
            if ($optiontext === 'A') {
                $this->assertEquals(1, $count);
            } else if ($optiontext === 'B') {
                $this->assertEquals(2, $count);
            } else {
                // Option C has no answers.
                $this->assertEquals(0, $count);
            }
        }

    }

    /**
     * Data provider for test_get_all_answers_count.
     *
     * @return array
     */
    public static function provider_count_all_answers(): array {
        return [
            'Teacher in a group - No group mode' => [
                'username' => 't1',
                'coursegroupmode' => NOGROUPS,
                'currentgroup' => null,
                'expectedcount' => 2,
            ],
            // This test about SEPARATEGROUPS it will be the subject of an follow up ticket (MDL-85852).
            'Teacher in a group - Separate group mode' => [
                'username' => 't1',
                'coursegroupmode' => SEPARATEGROUPS,
                'currentgroup' => null,
                'expectedcount' => 2,
            ],
            'Teacher in a group - Separate group mode - Group1' => [
                'username' => 't1',
                'coursegroupmode' => SEPARATEGROUPS,
                'currentgroup' => 'g1',
                'expectedcount' => 2,
            ],
            'Teacher in a group - Visible group mode' => [
                'username' => 't1',
                'coursegroupmode' => VISIBLEGROUPS,
                'currentgroup' => null,
                'expectedcount' => 2,
            ],
            // Teacher 2 does not belong to any group.
            'Teacher without group - No group mode' => [
                'username' => 't2',
                'coursegroupmode' => NOGROUPS,
                'currentgroup' => null,
                'expectedcount' => 2,
            ],
            // These tests about SEPARATEGROUPS will be the subject of an follow up ticket (MDL-85852).
            'Teacher without group - Separate group mode' => [
                'username' => 't2',
                'coursegroupmode' => SEPARATEGROUPS,
                'currentgroup' => null,
                'expectedcount' => 2,
            ],
            'Teacher without group - Visible group mode' => [
                'username' => 't2',
                'coursegroupmode' => VISIBLEGROUPS,
                'currentgroup' => null,
                'expectedcount' => 2,
            ],
        ];
    }

    /**
     * Test checking if user has answered the choice.
     *
     * @covers \mod_choice\manager::has_answered
     */
    public function test_has_answered(): void {
        ['users' => $users, 'instance' => $instance] = $this->setup_users_and_activity();
        $manager = \mod_choice\manager::create_from_instance($instance);
        $this->setUser($users['s1']);
        $this->assertTrue($manager->has_answered());
        $this->setUser($users['s3']);
        $this->assertFalse($manager->has_answered());
    }

    /**
     * Test get_options method.
     *
     * @covers \mod_choice\manager::get_options
     */
    public function test_get_options(): void {
        $course = $this->getDataGenerator()->create_course();
        $instance1 = $this->getDataGenerator()->create_module('choice', [
            'course' => $course,
            'option' => ['A', 'B', 'C'],
        ]);
        $manager = \mod_choice\manager::create_from_instance($instance1);
        $options = $manager->get_options();
        $this->assertCount(3, $options);

        $instance2 = $this->getDataGenerator()->create_module('choice', [
            'course' => $course,
            'option' => ['111'],
        ]);
        $manager = \mod_choice\manager::create_from_instance($instance2);
        $options = $manager->get_options();
        $this->assertCount(1, $options);
    }
}
