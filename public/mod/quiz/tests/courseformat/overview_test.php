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

namespace mod_quiz\courseformat;

use core_courseformat\local\overview\overviewfactory;
use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;

/**
 * Tests for Lesson overview.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright   2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\courseformat\overview
 */
final class overview_test extends \advanced_testcase {
    /**
     * Test get_due_date_overview.
     *
     * @param int|null $timeincrement the time increment in seconds to add to the current time for the deadline.
     * @return void
     * @dataProvider provider_test_get_due_date_overview
     **/
    public function test_get_due_date_overview(?int $timeincrement): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $timeclose = $timeincrement ? $this->mock_clock_with_frozen()->time() + $timeincrement : 0;
        $quiz = $this->getDataGenerator()->create_module(
            'quiz',
            [
                'course' => $course->id,
                'timeclose' => $timeclose,
            ],
        );

        $this->setUser($student);

        $cm = get_fast_modinfo($course)->get_cm($quiz->cmid);
        $item = overviewfactory::create($cm)->get_due_date_overview();

        $this->assertEquals(get_string('duedate', 'quiz'), $item->get_name());
        $this->assertEquals($timeclose, $item->get_value());
    }

    /**
     * Provider for test_get_due_date_overview.
     *
     * @return array
     */
    public static function provider_test_get_due_date_overview(): array {
        return [
            'no_due' => [
                'timeincrement' => null,
            ],
            'past_due' => [
                'timeincrement' => -1 * (4 * DAYSECS),
            ],
            'future_due' => [
                'timeincrement' => (4 * DAYSECS),
            ],
        ];
    }

    /**
     * Test get_actions_overview.
     *
     * @param string $currentuser
     * @param array|null $expected
     * @return void
     * @dataProvider provider_test_get_actions_overview
     */
    public function test_get_actions_overview(
        string $currentuser,
        ?array $expected
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        ['users' => $users, 'cm' => $cm] = $this->setup_users_course_groups(
            [
                's1' => ['student', 'g1', 2],
                's2' => ['student', null, 1],
                't1' => ['editingteacher', null, null],
                't2' => ['teacher', 'g1', null],
            ]
        );
        $this->setUser($users[$currentuser]);
        $cminfo = get_fast_modinfo($cm->course)->get_cm($cm->id);
        $item = overviewfactory::create($cminfo)->get_actions_overview();

        if ($expected === null) {
            $this->assertNull($item);
            return;
        }

        $this->assertEquals(
            $expected,
            ['name' => $item->get_name(), 'value' => $item->get_value()]
        );
    }

    /**
     * Data provider for test_get_actions_overview.
     *
     * @return array
     */
    public static function provider_test_get_actions_overview(): array {
        return [
            'Student' => [
                'currentuser' => 's1',
                'expected' => null,
            ],
            'Teacher' => [
                'currentuser' => 't1',
                'expected' => [
                    'name' => get_string('actions'),
                    'value' => '',
                ],
            ],
        ];
    }

    /**
     * Test get_total_attempts_overview.
     *
     * @param string $currentuser
     * @param string|null $expected
     * @return void
     * @dataProvider provider_test_get_total_attempts_overview
     */
    public function test_get_extra_totalattempts_overview(
        string $currentuser,
        ?string $expected
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        ['users' => $users, 'cm' => $cm] = $this->setup_users_course_groups(
            [
                's1' => ['student', 'g1', 2],
                's2' => ['student', null, 1],
                't1' => ['editingteacher', null, null],
                't2' => ['teacher', 'g1', null],
            ]
        );
        $this->setUser($users[$currentuser]);
        $cminfo = get_fast_modinfo($cm->course)->get_cm($cm->id);
        $overview = overviewfactory::create($cminfo);

        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_total_attempts_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);

        if ($expected === null) {
            $this->assertNull($item);
            return;
        }

        $this->assertEquals(
            $expected,
            $item->get_value()
        );
    }

    /**
     * Data provider for provider_test_get_total_attempts_overview.
     *
     * @return array
     */
    public static function provider_test_get_total_attempts_overview(): array {
        return [
            'Teacher t1' => [
                'currentuser' => 't1',
                'expected' => "3",
            ],
            'Student' => [
                'currentuser' => 's1',
                'expected' => null,
            ],
        ];
    }

    /**
     * Test get_students_who_attempted_overview.
     *
     * @param string $currentuser
     * @param ?string $expected
     * @return void
     * @dataProvider provider_test_get_students_who_attempted_overview
     **/
    public function test_get_extra_attemptedstudents_overview(
        string $currentuser,
        ?string $expected
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        ['users' => $users, 'cm' => $cm] = $this->setup_users_course_groups(
            [
                's1' => ['student', 'g1', 2],
                's2' => ['student', null, 1],
                't1' => ['editingteacher', null, null],
                't2' => ['teacher', 'g1', null],
            ]
        );
        $this->setUser($users[$currentuser]);
        $cminfo = get_fast_modinfo($cm->course)->get_cm($cm->id);
        $overview = overviewfactory::create($cminfo);

        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_students_who_attempted_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);

        if ($expected === null) {
            $this->assertNull($item);
            return;
        }
        $this->assertEquals(
            $expected,
            $item->get_value()
        );
    }

    /**
     * Data provider for test_get_students_who_attempted_overview.
     *
     * @return array
     */
    public static function provider_test_get_students_who_attempted_overview(): array {
        return [
            'Teacher' => [
                'currentuser' => 't1',
                'expected' => "2 of 2",
            ],
            'Student' => [
                'currentuser' => 's1',
                'expected' => null,
            ],
        ];
    }

    /**
     * Set up users, course, groups and quiz for testing.
     *
     * @param array $data Array of user data with username as key and an array of role, group and attempts number as value.
     * @return array An array containing users, groups, quiz, course module and attempts.
     */
    private function setup_users_course_groups(array $data): array {
        $generator = $this->getDataGenerator();

        // Create a course and a quiz.
        $course = $generator->create_course(['groupmodeforce' => 1, 'groupmode' => SEPARATEGROUPS]);
        $quiz = $generator->create_module('quiz', ['course' => $course->id, 'sumgrades' => 1]);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);

        // Add a question to the quiz.
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        quiz_add_quiz_question($question->id, $quiz);

        // Create users and groups.
        $groups = [
            'g1' => $generator->create_group(['courseid' => $course->id, 'name' => 'g1']),
            'g2' => $generator->create_group(['courseid' => $course->id, 'name' => 'g2']),
        ];
        $users = [];
        $attempts = [];
        foreach ($data as $username => [$role, $group, $attemptsnum]) {
            $users[$username] = $generator->create_and_enrol($course, $role, ['username' => $username]);
            if ($group) {
                $generator->create_group_member(['userid' => $users[$username]->id, 'groupid' => $groups[$group]->id]);
            }
            if ($attemptsnum) {
                // Create attempts for the user.
                for ($acount = 1; $acount <= $attemptsnum; $acount++) {
                    $quizobj = quiz_settings::create($quiz->id, $users[$username]->id);
                    // Create an attempt for the student in the quiz.
                    $timenow = time();
                    $attempt = quiz_create_attempt($quizobj, $acount, false, $timenow, false, $users[$username]->id);
                    $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
                    $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
                    quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
                    quiz_attempt_save_started($quizobj, $quba, $attempt);
                    // Finish the attempt.
                    $attemptobj = quiz_attempt::create($attempt->id);
                    $attemptobj->process_submit($timenow, false);
                    $attemptobj->process_grade_submission($timenow);
                    $attempts[] = $attempt;
                }
            }
        }
        return [
            'users' => $users,
            'groups' => $groups,
            'quiz' => $quiz,
            'cm' => $cm,
            'course' => $course,
            'attempts' => $attempts,
        ];
    }
}
