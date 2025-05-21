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

use core_courseformat\local\overview\overviewfactory;

/**
 * Tests for Choice integration.
 *
 * @covers \mod_choice\courseformat\overview
 * @package    mod_choice
 * @category   test
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class overview_test extends \advanced_testcase {

    /**
     * Test get_extra_status_for_user method.
     *
     * @covers ::get_extra_status_for_user
     * @dataProvider data_provider_get_extra_status_for_user
     * @param string $user
     * @param bool|null $answered
     */
    public function test_get_extra_status_for_user(string $user, ?bool $answered): void {
        $this->resetAfterTest();
        ['users' => $users, 'course' => $course, 'instance' => $instance] =
            $this->setup_users_and_activity($answered ?? false);
        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        $this->setUser($users[$user]);
        $overview = overviewfactory::create($cm);
        $actionoverview = $overview->get_extra_overview_items();
        if ($answered === null) {
            $this->assertArrayNotHasKey('responded', $actionoverview);
        } else {
            $this->assertArrayHasKey('responded', $actionoverview);
            $this->assertEquals($answered, $actionoverview['responded']->get_value());
            if (!$answered) {
                $this->assertEquals('-', $actionoverview['responded']->get_content());
            }
        }
    }

    /**
     * Data provider for test_get_extra_status_for_user.
     *
     * @return array
     */
    public static function data_provider_get_extra_status_for_user(): array {
        return [
            'teacher view answered' => [
                'user' => 't1',
                'answered' => null, // Teacher can not see the status column.
            ],
            'student view answered' => [
                'user' => 's1',
                'answered' => true,
            ],
            'student view not answered' => [
                'user' => 's2',
                'answered' => false,
            ],
        ];
    }

    /**
     * Test get_extra_status_for_user method.
     *
     * @param int|null $timeincrement
     *
     * @covers ::get_due_date_overview
     * @dataProvider data_provider_get_due_date_overview
     */
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
     * @return array
     */
    public static function data_provider_get_due_date_overview(): array {
        return [
            'tomorrow' => [
                'timeincrement' => 1 * DAYSECS,
            ],
            'yesterday' => [
                'timeincrement' => -1 * DAYSECS,
            ],
            'today' => [
                'timeincrement' => 0,
            ],
            'No date' => [
                'timeincrement' => null,
            ],
        ];
    }

    /**
     * Test get_actions_overview method.
     *
     * @param string $username
     * @param int|null $expectedcount
     *
     * @covers ::get_actions_overview
     * @dataProvider data_provider_get_student_responded_count
     */
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
     * Test get_actions_overview method.
     *
     * @param string $username
     * @param int|null $expectedcount
     *
     * @covers ::get_actions_overview
     * @dataProvider data_provider_get_student_responded_count
     */
    public function test_get_students_who_responded(string $username, ?int $expectedcount = null): void {
        $this->resetAfterTest();
        ['users' => $users, 'course' => $course, 'instance' => $instance] = $this->setup_users_and_activity();
        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        $this->setUser($users[$username]);
        $overview = overviewfactory::create($cm);
        $actionoverview = $overview->get_extra_overview_items();

        if (is_null($expectedcount)) {
            $this->assertArrayNotHasKey('studentwhoresponded', $actionoverview);
        } else {
            $this->assertArrayHasKey('studentwhoresponded', $actionoverview);
            $this->assertEquals(
                $expectedcount,
                $actionoverview['studentwhoresponded']->get_value(),
            );
        }
    }
    /**
     * Data provider for get_actions_overview.
     *
     * @return array
     */
    public static function data_provider_get_student_responded_count(): array {
            return [
                'teacher 1' => ['t1', 2],
                'student 1' => ['s1', null],
            ];
    }

    /**
     * Setup users and activity for the tests.
     *
     * @param bool $withanswers whether to create answers for the users.
     *
     * @return array
     */
    private function setup_users_and_activity(bool $withanswers = true): array {
        $this->setAdminUser();
        $db = \core\di::get(\moodle_database::class);
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        foreach (['s1' => 'student', 's2' => 'student', 't1' => 'teacher'] as $uname => $role) {
            $users[$uname] = $generator->create_and_enrol($course, $role, ['username' => $uname]);
        }
        $instance = $generator->create_module('choice', [
            'course' => $course,
            'option' => ['A', 'B'],
        ]);

        if ($withanswers) {
            // Create responses for the first two users.
            $choicesid = $db->get_fieldset('choice_options', 'id', ['choiceid' => $instance->id]);
            $currentchoiceid = array_shift($choicesid); // Get the first choice.
            $generator->get_plugin_generator('mod_choice')->create_response([
                'choiceid' => $instance->id,
                'responses' => $currentchoiceid,
                'userid' => $users['s1']->id,
            ]);
            $generator->get_plugin_generator('mod_choice')->create_response([
                'choiceid' => $instance->id,
                'responses' => $currentchoiceid,
                'userid' => $users['s2']->id,
            ]);
        }
        return [
            'users' => $users,
            'course' => $course,
            'instance' => $instance,
        ];
    }
}
