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
 * @category   test
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_choice\manager
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
     * @param string $currentuser The current user to filter answers for.
     * @param int $groupmode The group mode.
     * @param array $selectedgroups The groups to filter by, empty array means no filtering.
     * @param array $expectedcount The expected count of answers for the user.
     *
     * @covers       ::count_all_users_answered
     * @dataProvider provider_count_all_answers
     */
    public function test_count_all_users_answered(
        string $currentuser,
        int $groupmode,
        array $selectedgroups,
        array $expectedcount
    ): void {
        [
            'users' => $users,
            'instance' => $instance,
            'groups' => $allgroups,
        ] = $this->setup_users_and_activity($groupmode);

        $this->setUser($users[$currentuser]);
        $manager = \mod_choice\manager::create_from_instance($instance);

        $groups = [];
        if (!empty($selectedgroups)) {
            foreach ($selectedgroups as $group) {
                if ($group === 'unexisting') {
                    $groups = [666];
                } else {
                    $groups[] = $allgroups[$group]->id;
                }
            }
        }

        $count = $manager->count_all_users_answered($groups);
        $this->assertEquals($expectedcount['all'], $count);

        // Check answers count for each option.
        $db = \core\di::get(\moodle_database::class);
        $options = $db->get_records_menu('choice_options', ['choiceid' => $instance->id], '', 'id, text');
        foreach ($options as $optionid => $optiontext) {
            $count = $manager->count_all_users_answered($groups, $optionid);
            $this->assertEquals($expectedcount[$optiontext], $count);
        }
    }

    /**
     * Data provider for test_get_all_answers_count.
     *
     * @return array
     */
    public static function provider_count_all_answers(): array {
        return [
            'Teacher - No group' => [
                'currentuser' => 't1',
                'groupmode' => NOGROUPS,
                'selectedgroups' => [],
                'expectedcount' => [
                    'all' => 3,
                    'A' => 1,
                    'B' => 3,
                    'C' => 0,
                ],
            ],
            'Teacher - Visible groups' => [
                'currentuser' => 't1',
                'groupmode' => VISIBLEGROUPS,
                'selectedgroups' => [],
                'expectedcount' => [
                    'all' => 3,
                    'A' => 1,
                    'B' => 3,
                    'C' => 0,
                ],
            ],
            'Teacher - Separate groups (all)' => [
                'currentuser' => 't1',
                'groupmode' => SEPARATEGROUPS,
                'selectedgroups' => [],
                'expectedcount' => [
                    'all' => 3,
                    'A' => 1,
                    'B' => 3,
                    'C' => 0,
                ],
            ],
            'Teacher - Separate groups (group1)' => [
                'currentuser' => 't1',
                'groupmode' => SEPARATEGROUPS,
                'selectedgroups' => ['group1'],
                'expectedcount' => [
                    'all' => 1,
                    'A' => 1,
                    'B' => 1,
                    'C' => 0,
                ],
            ],
            'Teacher - Separate groups (group2)' => [
                'currentuser' => 't1',
                'groupmode' => SEPARATEGROUPS,
                'selectedgroups' => ['group2'],
                'expectedcount' => [
                    'all' => 2,
                    'A' => 0,
                    'B' => 2,
                    'C' => 0,
                ],
            ],
            'Teacher - Separate groups (group1, group2)' => [
                'currentuser' => 't1',
                'groupmode' => SEPARATEGROUPS,
                'selectedgroups' => ['group1', 'group2'],
                'expectedcount' => [
                    'all' => 3,
                    'A' => 1,
                    'B' => 3,
                    'C' => 0,
                ],
            ],
            'Student' => [
                'currentuser' => 's1',
                'groupmode' => NOGROUPS,
                'selectedgroups' => [],
                'expectedcount' => [
                    'all' => 0, // Students cannot see the answers count.
                    'A' => 0,
                    'B' => 0,
                    'C' => 0,
                ],
            ],
            'Unexisting group - Separate groups' => [
                'currentuser' => 't1',
                'groupmode' => SEPARATEGROUPS,
                'selectedgroups' => ['unexisting'],
                'expectedcount' => [
                    'all' => 0,
                    'A' => 0,
                    'B' => 0,
                    'C' => 0,
                ],
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

    /**
     * Setup users and activity for testing answers retrieval.
     *
     * @param int $groupmode the group mode to use for the course.
     * @return array indexed array with 'users', 'course', 'instance' and 'groups'.
     */
    private function setup_users_and_activity(int $groupmode = NOGROUPS): array {
        $db = \core\di::get(\moodle_database::class);
        $users = [];
        $generator = $this->getDataGenerator();

        // Force the group mode for the course.
        $course = $generator->create_course(['groupmode' => $groupmode, 'groupmodeforce' => 1]);
        $data = [
            's1' => 'student',
            's2' => 'student',
            's3' => 'student', // This user does not belong to any group.
            's4' => 'student',
            't1' => 'editingteacher',
            't2' => 'teacher', // This user does not belong to any group.
        ];
        foreach ($data as $username => $role) {
            $users[$username] = $generator->create_and_enrol($course, $role, ['username' => $username]);
        }

        // Create groups.
        $groups = [
            'group1' => $generator->create_group(['courseid' => $course->id]),
            'group2' => $generator->create_group(['courseid' => $course->id]),
        ];

        // Add users to groups:
        // - group1: s1, t1.
        // - group2: s2, s4.
        groups_add_member($groups['group1'], $users['s1']->id);
        groups_add_member($groups['group1'], $users['t1']->id);
        groups_add_member($groups['group2'], $users['s2']->id);
        groups_add_member($groups['group2'], $users['s4']->id);

        $instance = $generator->create_module('choice', [
            'course' => $course,
            'option' => ['A', 'B', 'C'],
            'allowmultiple' => 1,
        ]);

        // Create options and responses:
        // s1: A, B.
        // s2: B.
        // s4: B.
        $choicesid = $db->get_records_menu('choice_options', ['choiceid' => $instance->id], '', 'id, text');
        $choicestoid = array_flip($choicesid);
        /** @var \mod_choice_generator $plugingenerator */
        $plugingenerator = $generator->get_plugin_generator('mod_choice');
        $plugingenerator->create_response([
            'choiceid' => $instance->id,
            'responses' => [$choicestoid['A'], $choicestoid['B']],
            'userid' => $users['s1']->id,
        ]);
        $plugingenerator->create_response([
            'choiceid' => $instance->id,
            'responses' => $choicestoid['B'],
            'userid' => $users['s2']->id,
        ]);
        $plugingenerator->create_response([
            'choiceid' => $instance->id,
            'responses' => $choicestoid['B'],
            'userid' => $users['s4']->id,
        ]);

        return [
            'users' => $users,
            'course' => $course,
            'instance' => $instance,
            'groups' => $groups,
        ];
    }
}
