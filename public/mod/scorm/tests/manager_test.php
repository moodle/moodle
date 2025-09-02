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

namespace mod_scorm;

use context_module;

/**
 * Generator tests class.
 *
 * @package    mod_scorm
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_scorm\manager
 */
final class manager_test extends \advanced_testcase {
    /**
     * Test creating a manager instance from an instance record.
     */
    public function test_create_from_instance(): void {
        $this->resetAfterTest();
        ['instances' => $instances] = $this->setup_users_and_activity();
        $manager = \mod_scorm\manager::create_from_instance($instances['withattempts']);
        $manageractivity = $manager->get_instance();
        $this->assertEquals($instances['withattempts']->id, $manageractivity->id);
        $managercontext = $manager->get_context();
        $context = context_module::instance($instances['withattempts']->cmid);
        $this->assertEquals($context->id, $managercontext->id);
        $cm = get_coursemodule_from_id(
            manager::MODULE,
            $manageractivity->cmid,
            0,
            false,
            MUST_EXIST
        );
        $this->assertEquals($cm->id, $manager->get_coursemodule()->id);
    }

    /**
     * Test creating a manager instance from an instance record.
     */
    public function test_create_from_instance_with_wrong_id(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $instance = $generator->create_module('page', [
            'course' => $course,
        ]);
        $this->expectExceptionMessage('mod_scorm/invalidcoursemodule');
        $manager = \mod_scorm\manager::create_from_instance($instance);
    }

    /**
     * Test creating a manager instance from an instance record.
     */
    public function test_create_from_coursemodule(): void {
        $this->resetAfterTest();
        ['instances' => $instances] = $this->setup_users_and_activity();
        $cm = get_coursemodule_from_instance('scorm', $instances['withattempts']->id);
        $manager = \mod_scorm\manager::create_from_coursemodule($cm);
        $managercm = $manager->get_coursemodule();
        $this->assertEquals($cm->id, $managercm->id);
        $managercontext = $manager->get_context();
        $context = context_module::instance($cm->id);
        $this->assertEquals($context->id, $managercontext->id);
    }

    /**
     * Test creating a manager instance from an instance record.
     */
    public function test_create_from_coursemodule_with_wrong_id(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $instance = $generator->create_module('page', [
            'course' => $course,
        ]);
        $cm = get_coursemodule_from_instance('page', $instance->id);
        $this->expectException(\dml_missing_record_exception::class);
        $manager = \mod_scorm\manager::create_from_coursemodule($cm);
    }

    /**
     * Test if the manager can view reports for a user.
     */
    public function test_can_view_reports(): void {
        $this->resetAfterTest();
        ['users' => $users, 'instances' => $instances] = $this->setup_users_and_activity();
        $manager = \mod_scorm\manager::create_from_instance($instances['withattempts']);
        // Create an attempt for the current user.
        $this->assertTrue($manager->can_view_reports($users['t1']));
        $this->assertFalse($manager->can_view_reports($users['s1']));
    }

    /**
     * Test the maximum number of attempts for a SCORM activity.
     */
    public function test_get_max_attempts(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $this->setAdminUser();
        $instance = $generator->create_module('scorm', [
            'course' => $course,
            'maxattempt' => 5,
        ]);
        $manager = \mod_scorm\manager::create_from_instance($instance);
        $this->assertEquals(5, $manager->get_max_attempts());
    }

    /**
     * Count the number of user who attempted the SCORM activity.
     *
     * @param int $groupmode the group mode to use for the course.
     * @param string $activity the activity name to test.
     * @param array $expected the expected participant counts for each activity and user.
     *
     * @dataProvider get_count_users_who_attempted_data
     */
    public function test_count_users_who_attempted(int $groupmode, string $activity, array $expected): void {
        $this->resetAfterTest();
        ['users' => $users, 'instances' => $instances] = $this->setup_users_and_activity(groupmode: $groupmode);

        $manager = \mod_scorm\manager::create_from_instance($instances[$activity]);
        // Check the count of users who attempted.
        foreach ($expected as $username => $count) {
            $this->setUser($users[$username]);
            $groups = [];
            if ($groupmode !== NOGROUPS) {
                // If group mode is not NOGROUPS, we need to get the groups for the user.
                $groups = groups_get_activity_allowed_groups($manager->get_coursemodule());
            }
            $groupids = array_map(
                fn($group) => $group->id,
                $groups
            );
            $this->assertEquals(
                $count,
                $manager->count_users_who_attempted($groupids),
                "Failed asserting that user {$username} can count {$count} users who attempted."
            );
        }
    }

    /**
     * Data provider for participant count tests.
     *
     * @return array
     */
    public static function get_count_users_who_attempted_data(): array {
        return [
            'No groups' => [
                'groupmode' => NOGROUPS,
                'activity' => 'withattempts',
                'expected' => [
                    't1' => 2, // We count s1 and s2.
                    't2' => 2,
                    's1' => 2, // Same here.
                ],
            ],
            'Separate groups' => [
                'groupmode' => SEPARATEGROUPS,
                'activity' => 'withattempts',
                'expected' => [
                    't1' => 1, // We count only the participants in group g1 (so s1).
                    't2' => 2, // Here is a false positive, we have no groups so no filtering is applied.
                    's1' => 1, // User s1 is in group g1 and has mod/scorm:savetrack permission, same for t1.
                ],
            ],
            'Visible groups' => [
                'groupmode' => VISIBLEGROUPS,
                'activity' => 'withattempts',
                'expected' => [
                    't1' => 2, // We count only the participants in group g1 and g2 (so s1 and s2).
                    't2' => 2, // Same for t2, we count the participants in group g1 and g2 (so s1 and s2).
                    's1' => 2, // User s1 is in group g1 and has mod/scorm:savetrack permission, same for t1.
                ],
            ],
        ];
    }

    /**
     * Count the number of attempts for the SCORM activity.
     *
     * @param int $groupmode the group mode to use for the course.
     * @param string $activity the activity name to test.
     * @param array $currentgroups
     * @param int $expectedcount
     * @throws \moodle_exception
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_count_all_attempts_data')]
    public function test_count_all_attempts(int $groupmode, string $activity, array $currentgroups, int $expectedcount): void {
        $this->resetAfterTest();
        ['instances' => $instances, 'groups' => $groups] = $this->setup_users_and_activity(groupmode: $groupmode);
        $manager = \mod_scorm\manager::create_from_instance($instances[$activity]);
        $groupids = array_map(
                fn($gname) => $groups[$gname]->id,
                $currentgroups
        );
        $this->assertEquals(
                $expectedcount,
                $manager->count_all_attempts($groupids),
        );
    }

    /**
     * Data provider for participant count tests.
     *
     * @return \Generator
     */
    public static function get_count_all_attempts_data(): \Generator {
        yield 'No groups' => [
                'groupmode' => NOGROUPS,
                'activity' => 'withattempts',
                'currentgroups' => [],
                'expectedcount' => 3,
        ];
        yield 'Separate groups, g1 selected' => [
                'groupmode' => SEPARATEGROUPS,
                'activity' => 'withattempts',
                'currentgroups' => ['g1'],
                'expectedcount' => 2,
        ];
        yield 'Separate groups, g2 selected' => [
                'groupmode' => SEPARATEGROUPS,
                'activity' => 'withattempts',
                'currentgroups' => ['g2'],
                'expectedcount' => 1,
        ];
        yield 'Separate groups, g1 and g2 selected' => [
                'groupmode' => SEPARATEGROUPS,
                'activity' => 'withattempts',
                'currentgroups' => ['g1', 'g2'],
                'expectedcount' => 3,
        ];
        yield 'Separate groups, no group selected' => [
                'groupmode' => SEPARATEGROUPS,
                'activity' => 'withattempts',
                'currentgroups' => [],
                'expectedcount' => 3, // Seems counter-intuitive but with no group selected we count all attempts.
        ];
        yield 'Visible groups' => [
                'groupmode' => VISIBLEGROUPS,
                'activity' => 'withattempts',
                'currentgroups' => ['g1', 'g2'],
                'expectedcount' => 3,
        ];
        yield 'Visible groups g1 selected' => [
                'groupmode' => VISIBLEGROUPS,
                'activity' => 'withattempts',
                'currentgroups' => ['g1'],
                'expectedcount' => 2,
        ];
    }

    /**
     * Test if the manager can view reports for a user.
     *
     * @param int $groupmode the group mode to use for the course.
     * @param array $expected the expected participant counts for each user.
     *
     * @dataProvider get_count_participants_data
     */
    public function test_count_participants(int $groupmode, array $expected): void {
        $this->resetAfterTest();
        ['users' => $users, 'instances' => $instances] = $this->setup_users_and_activity($groupmode);
        $manager = \mod_scorm\manager::create_from_instance($instances['withattempts']);
        // Check the count of participants.
        foreach ($expected as $username => $count) {
            $this->setUser($users[$username]);
            $groups = [];
            if ($groupmode !== NOGROUPS) {
                // If group mode is not NOGROUPS, we need to get the groups for the user.
                $groups = groups_get_activity_allowed_groups($manager->get_coursemodule());
            }
            $groupids = array_map(
                fn($group) => $group->id,
                $groups
            );
            $this->assertEquals(
                $count,
                $manager->count_participants($groupids),
                "Failed asserting that user {$username} can count {$count} participants."
            );
        }
    }

    /**
     * Data provider for participant count tests.
     *
     * @return array
     */
    public static function get_count_participants_data(): array {
        return [
            'No groups' => [
                'groupmode' => NOGROUPS,
                'expected' => [
                    't1' => 4, // We count students and teachers because teachers can submit attempts (not s3).
                    't2' => 4,
                    's1' => 4,
                    's2' => 4,
                    's3' => 4, // User s3 is a test role that does not have mod/scorm:savetrack permission.
                ],
            ],
            'Separate groups' => [
                'groupmode' => SEPARATEGROUPS,
                'expected' => [
                    't1' => 2, // Only the participants in group g1 that have mod/scorm:savetrack permission (so s1 and t1).
                    't2' => 4, // Here is a false positive, we have no groups so no filtering is applied.
                    's1' => 2, // User s1 is in group g1 and has mod/scorm:savetrack permission, same for t1.
                    's2' => 1, // Only s2 and s3 are in group g2, but s3 does not have mod/scorm:savetrack permission.
                    's3' => 3, // User s3 is a test role that does not have mod/scorm:savetrack permission,
                    // so we count only s1, s2 and t1.
                ],
            ],
            'Visible groups' => [
                'groupmode' => VISIBLEGROUPS,
                'expected' => [
                    't1' => 3, // We count the participants in group g1 and also in other groups like s2.
                    't2' => 3, // Same for t2, we count the participants in group g1 and also in other groups like s2.
                    's1' => 3,
                    's2' => 3,
                    's3' => 3, // User s3 is a test role that does not have mod/scorm:savetrack permission.
                ],
            ],
        ];
    }

    /**
     * Test the grading method for a SCORM activity.
     *
     * @param array $scormparams the parameters to create the SCORM activity.
     * @param string $expectedmethod the expected grading method.
     *
     * @dataProvider get_grading_method_data
     */
    public function test_get_grading_method(array $scormparams, string $expectedmethod): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $scormparams['course'] = $course->id;
        $this->setAdminUser();
        $scorm = $generator->create_module('scorm', $scormparams);
        $manager = \mod_scorm\manager::create_from_instance($scorm);
        $this->assertEquals($expectedmethod, $manager->get_grading_method());
    }

    /**
     * Data provider for grading method tests.
     *
     * @return array
     */
    public static function get_grading_method_data(): array {
        global $CFG;
        require_once($CFG->dirroot . '/mod/scorm/locallib.php');
        return [
            'Max attempt 1, gradehighest' => [
                'scormparams' => ['maxattempt' => 1, 'grademethod' => GRADEHIGHEST],
                'expectedmethod' => get_string('gradehighest', 'scorm'),
            ],
            'Max attempt 1, gradeaverage' => [
                'scormparams' => ['maxattempt' => 1, 'grademethod' => GRADEAVERAGE],
                'expectedmethod' => get_string('gradeaverage', 'scorm'),
            ],
            'Max attempt 1, gradesum' => [
                'scormparams' => ['maxattempt' => 1, 'grademethod' => GRADESUM],
                'expectedmethod' => get_string('gradesum', 'scorm'),
            ],
            'Max attempt 1, gradescoes' => [
                'scormparams' => ['maxattempt' => 1, 'grademethod' => GRADESCOES],
                'expectedmethod' => get_string('gradescoes', 'scorm'),
            ],
            'Highestattempt' => [
                'scormparams' => ['maxattempt' => 5, 'whatgrade' => HIGHESTATTEMPT],
                'expectedmethod' => get_string('highestattempt', 'scorm'),
            ],
            'Averageattempt' => [
                'scormparams' => ['maxattempt' => 5, 'whatgrade' => AVERAGEATTEMPT],
                'expectedmethod' => get_string('averageattempt', 'scorm'),
            ],
            'Firstattempt' => [
                'scormparams' => ['maxattempt' => 5, 'whatgrade' => FIRSTATTEMPT],
                'expectedmethod' => get_string('firstattempt', 'scorm'),
            ],
            'Lastattempt' => [
                'scormparams' => ['maxattempt' => 5, 'whatgrade' => LASTATTEMPT],
                'expectedmethod' => get_string('lastattempt', 'scorm'),
            ],
        ];
    }

    /**
     * Setup users and activity for testing answers retrieval.
     *
     * @param int $groupmode the group mode to use for the course. Note that for now group mode is not supported
     * in the course overview page and will need to be implemented.
     * @return array indexed array with 'users', 'course' and  'instance'.
     */
    private function setup_users_and_activity(int $groupmode = NOGROUPS): array {
        global $CFG;
        require_once($CFG->dirroot . '/mod/scorm/locallib.php');
        $users = [];
        $generator = $this->getDataGenerator();
        $courseparams = [];
        if ($groupmode !== NOGROUPS) {
            // Set the group mode for the course.
            $courseparams['groupmode'] = $groupmode;
            $courseparams['groupmodeforce'] = 1; // Force the group mode.
        }
        $course = $generator->create_course($courseparams);
        // Create a role that does not have mod/scorm:savetrack permission.
        $testrole = $generator->create_role([
            'shortname' => 'testrole',
            'name' => 'Test role',
            'archetype' => 'student',
        ]);
        assign_capability('mod/scorm:savetrack', CAP_PROHIBIT, $testrole, \context_course::instance($course->id));

        $data = [
            's1' => ['role' => 'student', 'groups' => ['g1']],
            's2' => ['role' => 'student', 'groups' => ['g2']],
            's3' => ['role' => 'testrole', 'groups' => ['g1', 'g2']], // Should not be counted as participant.
            't1' => ['role' => 'teacher', 'groups' => ['g1']],
            't2' => ['role' => 'teacher', 'groups' => []],
        ];
        $groups = [];
        foreach ($data as $username => $userinfo) {
            ['role' => $role, 'groups' => $groupstoassign] = $userinfo;
            $users[$username] = $generator->create_and_enrol($course, $role, ['username' => $username]);
            foreach ($groupstoassign as $grouptoassign) {
                if (!isset($groups[$grouptoassign])) {
                    // Create the group if it does not exist.
                    $groups[$grouptoassign] = $generator->create_group(['courseid' => $course->id, 'name' => $grouptoassign]);
                }
                // Add the user to the group.
                groups_add_member($groups[$grouptoassign], $users[$username]->id);
            }
        }
        $this->setAdminUser();
        $instances = [];
        $instances['withattempts'] = $generator->create_module('scorm', [
            'course' => $course,
        ]);
        $instances['withoutattempts'] = $generator->create_module('scorm', [
            'course' => $course,
        ]);
        $scormgenerator = $this->getDataGenerator()->get_plugin_generator('mod_scorm');
        $scormgenerator->create_attempt(['scormid' => $instances['withattempts']->id, 'userid' => $users['s1']->id]);
        $scormgenerator->create_attempt(['scormid' => $instances['withattempts']->id, 'userid' => $users['s1']->id]);
        $scormgenerator->create_attempt(['scormid' => $instances['withattempts']->id, 'userid' => $users['s2']->id]);
        return [
            'users' => $users,
            'course' => $course,
            'instances' => $instances,
            'groups' => $groups,
        ];
    }
}
