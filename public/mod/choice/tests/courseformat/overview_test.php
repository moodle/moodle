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

namespace mod_choice\courseformat;

use core\output\pix_icon;
use core_courseformat\local\overview\overviewfactory;

/**
 * Tests for Choice integration.
 *
 * @package    mod_choice
 * @category   test
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overview::class)]
final class overview_test extends \advanced_testcase {
    /**
     * Test get_extra_status_for_user method.
     *
     * @param string $user
     * @param bool|null $answered
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('data_provider_get_extra_status_for_user')]
    public function test_get_extra_status_for_user(string $user, ?bool $answered): void {
        $this->resetAfterTest();
        ['users' => $users, 'course' => $course, 'instance' => $instance] =
            $this->setup_users_and_activity(false, $answered ?? false);
        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        $this->setUser($users[$user]);
        $overview = overviewfactory::create($cm);
        $actionoverview = $overview->get_extra_overview_items();
        if ($answered === null) {
            $this->assertNull($actionoverview['responded']);
        } else {
            $this->assertEquals($answered, $actionoverview['responded']->get_value());
            $content = $actionoverview['responded']->get_content();
            if (!$answered) {
                $this->assertEquals('-', $content);
            } else {
                $this->assertInstanceOf(pix_icon::class, $content);
            }
        }
    }

    /**
     * Data provider for test_get_extra_status_for_user.
     *
     * @return \Generator
     */
    public static function data_provider_get_extra_status_for_user(): \Generator {
        yield 'teacher view answered' => [
            'user' => 't1',
            'answered' => null, // Teacher can not see the status column.
        ];
        yield 'student view answered' => [
            'user' => 's1',
            'answered' => true,
        ];
        yield 'student view not answered' => [
            'user' => 's2',
            'answered' => false,
        ];
    }

    /**
     * Test get_extra_status_for_user method.
     *
     * @param int|null $timeincrement
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('data_provider_get_due_date_overview')]
    public function test_get_due_date_overview(?int $timeincrement = null): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $choicetemplate = ['course' => $course->id, 'allowmultiple' => 1, 'option' => ['A', 'B', 'C']];
        $clock = $this->mock_clock_with_frozen();

        if (!is_null($timeincrement)) {
            $choicetemplate['timeclose'] = $clock->time() + $timeincrement;
        }
        $activity = $this->getDataGenerator()->create_module(
            'choice',
            $choicetemplate,
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $this->setUser($student);
        $overview = overviewfactory::create($cm);
        $this->assertEquals(
            is_null($timeincrement) ? null : $clock->time() + $timeincrement,
            $overview->get_due_date_overview()->get_value(),
        );
    }

    /**
     * Data provider for test_get_due_date_overview.
     *
     * @return \Generator
     */
    public static function data_provider_get_due_date_overview(): \Generator {
        yield 'tomorrow' => [
            'timeincrement' => 1 * DAYSECS,
        ];
        yield 'yesterday' => [
            'timeincrement' => -1 * DAYSECS,
        ];
        yield 'today' => [
            'timeincrement' => 0,
        ];
        yield 'No date' => [
            'timeincrement' => null,
        ];
    }

    /**
     * Test get_actions_overview method.
     *
     * @param string $username The username of the user to test.
     * @param int|null $expectedcount the expected count of users who responded
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_actions_overview')]
    public function test_get_actions_overview(string $username, ?int $expectedcount = null): void {
        $this->resetAfterTest();
        ['users' => $users, 'course' => $course, 'instance' => $instance] = $this->setup_users_and_activity();

        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        $this->setUser($users[$username]);
        $overview = overviewfactory::create($cm);
        $actionoverview = $overview->get_actions_overview();
        if (is_null($expectedcount)) {
            $this->assertNull($actionoverview);
        } else {
            $this->assertEquals(
                get_string('viewallresponses', 'choice', $expectedcount),
                $actionoverview->get_value(),
            );
        }
    }

    /**
     * Data provider for test_get_actions_overview.
     *
     * @return \Generator the data provider array
     */
    public static function provider_test_get_actions_overview(): \Generator {
        yield 'Student' => [
            'username' => 's1',
            'expectedcount' => null,
        ];
        yield 'Teacher' => [
            'username' => 't1',
            'expectedcount' => 3,
        ];
    }

    /**
     * Test get_actions_overview method.
     *
     * @param string $currentuser The current user to test.
     * @param bool $allowmultiple whether the choice allows multiple answers
     * @param bool $withanswers whether the choice will be created with answers
     * @param int $groupmode The group mode for the choice activity.
     * @param int|null $expectedcount the expected count of users who responded
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_get_extra_students_who_responded')]
    public function test_get_extra_students_who_responded(
        string $currentuser,
        bool $allowmultiple = false,
        bool $withanswers = true,
        int $groupmode = NOGROUPS,
        ?int $expectedcount = null,
    ): void {
        $this->resetAfterTest();
        [
            'users' => $users,
            'course' => $course,
            'instance' => $instance,
        ] = $this->setup_users_and_activity($allowmultiple, $withanswers, $groupmode);
        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);

        $this->setUser($users[$currentuser]);

        $overview = overviewfactory::create($cm);
        $result = $overview->get_extra_overview_items();

        if (is_null($expectedcount)) {
            $this->assertNull($result['studentwhoresponded']);
        } else {
            $this->assertEquals(
                $expectedcount,
                $result['studentwhoresponded']->get_value(),
            );
            /** @var \core_courseformat\output\local\overview\overviewdialog $content */
            $content = $result['studentwhoresponded']->get_content();
            $reflection = new \ReflectionClass($content);
            $description = $reflection->getProperty('description');
            $description->setAccessible(true);
            if ($allowmultiple) {
                $this->assertEquals(
                    get_string('allowmultiple', 'mod_choice'),
                    $description->getValue($content),
                );
            } else {
                $this->assertEmpty($description->getValue($content));
            }

            $reflection = new \ReflectionClass($content);
            $items = $reflection->getProperty('items');
            $items->setAccessible(true);
            $this->assertEquals(
                3, // A, B, C.
                count($items->getValue($content)),
            );
        }
    }

    /**
     * Data provider for test_get_extra_students_who_responded.
     *
     * @return \Generator the data provider array
     */
    public static function provider_get_extra_students_who_responded(): \Generator {
        yield 'Student' => [
            'currentuser' => 's1',
            'expectedcount' => null,
        ];
        // No groups.
        yield 'No groups - Teacher - With answers - No multiple' => [
            'currentuser' => 't2',
            'allowmultiple' => false,
            'withanswers' => true,
            'groupmode' => NOGROUPS,
            'expectedcount' => 3,
        ];
        yield 'No groups - Teacher - Without answers - No multiple' => [
            'currentuser' => 't2',
            'withanswers' => false,
            'groupmode' => NOGROUPS,
            'expectedcount' => 0,
        ];
        yield 'No groups - Teacher - With answers - Multiple' => [
            'currentuser' => 't2',
            'allowmultiple' => true,
            'withanswers' => true,
            'groupmode' => NOGROUPS,
            'expectedcount' => 3,
        ];
        yield 'No groups - Teacher - Without answers - Multiple' => [
            'currentuser' => 't2',
            'allowmultiple' => true,
            'withanswers' => false,
            'groupmode' => NOGROUPS,
            'expectedcount' => 0,
        ];
        // Visible groups.
        yield 'Visible groups - Teacher - With answers - No multiple' => [
            'currentuser' => 't2',
            'allowmultiple' => false,
            'withanswers' => true,
            'groupmode' => VISIBLEGROUPS,
            'expectedcount' => 3,
        ];
        yield 'Visible groups - Teacher - Without answers - No multiple' => [
            'currentuser' => 't2',
            'withanswers' => false,
            'groupmode' => VISIBLEGROUPS,
            'expectedcount' => 0,
        ];
        yield 'Visible groups - Teacher - With answers - Multiple' => [
            'currentuser' => 't2',
            'allowmultiple' => true,
            'withanswers' => true,
            'groupmode' => VISIBLEGROUPS,
            'expectedcount' => 3,
        ];
        yield 'Visible groups - Teacher - Without answers - Multiple' => [
            'currentuser' => 't2',
            'allowmultiple' => true,
            'withanswers' => false,
            'groupmode' => VISIBLEGROUPS,
            'expectedcount' => 0,
        ];
        // Separate groups.
        yield 'Separate groups - Editing teacher - With answers - No multiple' => [
            'currentuser' => 't1',
            'allowmultiple' => false,
            'withanswers' => true,
            'groupmode' => SEPARATEGROUPS,
            'expectedcount' => 3,
        ];
        yield 'Separate groups - Editing teacher - Without answers - No multiple' => [
            'currentuser' => 't1',
            'withanswers' => false,
            'groupmode' => SEPARATEGROUPS,
            'expectedcount' => 0,
        ];
        yield 'Separate groups - Editing teacher - With answers - Multiple' => [
            'currentuser' => 't1',
            'allowmultiple' => true,
            'withanswers' => true,
            'groupmode' => SEPARATEGROUPS,
            'expectedcount' => 3,
        ];
        yield 'Separate groups - Editing teacher - Without answers - Multiple' => [
            'currentuser' => 't1',
            'allowmultiple' => true,
            'withanswers' => false,
            'groupmode' => SEPARATEGROUPS,
            'expectedcount' => 0,
        ];
        yield 'Separate groups - Non-editing teacher - With answers - No multiple' => [
            'currentuser' => 't2',
            'allowmultiple' => false,
            'withanswers' => true,
            'groupmode' => SEPARATEGROUPS,
            'expectedcount' => 1,
        ];
        yield 'Separate groups - Non-editing teacher - Without answers - No multiple' => [
            'currentuser' => 't2',
            'withanswers' => false,
            'groupmode' => SEPARATEGROUPS,
            'expectedcount' => 0,
        ];
        yield 'Separate groups - Non-editing teacher - With answers - Multiple' => [
            'currentuser' => 't2',
            'allowmultiple' => true,
            'withanswers' => true,
            'groupmode' => SEPARATEGROUPS,
            'expectedcount' => 1,
        ];
        yield 'Separate groups - Non-editing teacher - Without answers - Multiple' => [
            'currentuser' => 't2',
            'allowmultiple' => true,
            'withanswers' => false,
            'groupmode' => SEPARATEGROUPS,
            'expectedcount' => 0,
        ];
    }

    /**
     * Setup users and activity for the tests.
     *
     * @param bool $allowmultiple whether the choice allows multiple answers.
     * @param bool $withanswers whether to create answers for the users.
     * @param int $groupmode the group mode to use for the course.
     * @return array indexed array with 'users', 'course', 'instance' and 'groups'
     */
    private function setup_users_and_activity(
        bool $allowmultiple = false,
        bool $withanswers = true,
        int $groupmode = NOGROUPS,
    ): array {
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        // Force the group mode for the course.
        $course = $generator->create_course(['groupmode' => $groupmode, 'groupmodeforce' => 1]);

        $data = [
            's1' => 'student',
            's2' => 'student',
            's3' => 'student', // This user does not belong to any group.
            's4' => 'student',
            't1' => 'editingteacher',
            't2' => 'teacher',
            't3' => 'teacher', // This user does not belong to any group.
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
        // - group1: s1, t1, t2.
        // - group2: s2, s4.
        groups_add_member($groups['group1'], $users['s1']->id);
        groups_add_member($groups['group1'], $users['t1']->id);
        groups_add_member($groups['group1'], $users['t2']->id);
        groups_add_member($groups['group2'], $users['s2']->id);
        groups_add_member($groups['group2'], $users['s4']->id);

        $instance = $generator->create_module('choice', [
            'course' => $course,
            'option' => ['A', 'B', 'C'],
            'allowmultiple' => $allowmultiple,
        ]);

        if ($withanswers) {
            // Create options and responses:
            // s1: No multiple: A - Multiple: A, C.
            // s2: B.
            // s4: B.
            $db = \core\di::get(\moodle_database::class);
            $choicesid = $db->get_fieldset('choice_options', 'id', ['choiceid' => $instance->id]);
            $currentchoiceid = array_shift($choicesid); // Get the first choice.
            /** @var \mod_choice_generator $plugingenerator */
            $plugingenerator = $generator->get_plugin_generator('mod_choice');
            $plugingenerator->create_response([
                'choiceid' => $instance->id,
                'responses' => $currentchoiceid,
                'userid' => $users['s1']->id,
            ]);
            if ($allowmultiple) {
                $plugingenerator->create_response([
                    'choiceid' => $instance->id,
                    'responses' => end($choicesid), // Get the last choice.
                    'userid' => $users['s1']->id,
                ]);
            }
            $plugingenerator->create_response([
                'choiceid' => $instance->id,
                'responses' => $currentchoiceid,
                'userid' => $users['s2']->id,
            ]);
            $plugingenerator->create_response([
                'choiceid' => $instance->id,
                'responses' => $currentchoiceid,
                'userid' => $users['s4']->id,
            ]);
        }

        return [
            'users' => $users,
            'course' => $course,
            'instance' => $instance,
            'groups' => $groups,
        ];
    }
}
