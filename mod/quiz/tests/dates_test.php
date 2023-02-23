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
 * Contains unit tests for mod_quiz\dates.
 *
 * @package   mod_quiz
 * @category  test
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_quiz;

use advanced_testcase;
use cm_info;
use core\activity_dates;

/**
 * Class for unit testing mod_quiz\dates.
 *
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dates_test extends advanced_testcase {

    /**
     * Data provider for get_dates_for_module().
     * @return array[]
     */
    public function get_dates_for_module_provider(): array {
        $now = time();
        $before = $now - DAYSECS;
        $earlier = $before - DAYSECS;
        $after = $now + DAYSECS;
        $later = $after + DAYSECS;

        return [
            'without any dates' => [
                null, null, null, null, null, null, []
            ],
            'only with opening time' => [
                $after, null, null, null, null, null, [
                    ['label' => get_string('activitydate:opens', 'course'), 'timestamp' => $after, 'dataid' => 'timeopen'],
                ]
            ],
            'only with closing time' => [
                null, $after, null, null, null, null, [
                    ['label' => get_string('activitydate:closes', 'course'), 'timestamp' => $after, 'dataid' => 'timeclose'],
                ]
            ],
            'with both times' => [
                $after, $later, null, null, null, null, [
                    ['label' => get_string('activitydate:opens', 'course'), 'timestamp' => $after, 'dataid' => 'timeopen'],
                    ['label' => get_string('activitydate:closes', 'course'), 'timestamp' => $later, 'dataid' => 'timeclose'],
                ]
            ],
            'between the dates' => [
                $before, $after, null, null, null, null, [
                    ['label' => get_string('activitydate:opened', 'course'), 'timestamp' => $before, 'dataid' => 'timeopen'],
                    ['label' => get_string('activitydate:closes', 'course'), 'timestamp' => $after, 'dataid' => 'timeclose'],
                ]
            ],
            'dates are past' => [
                $earlier, $before, null, null, null, null, [
                    ['label' => get_string('activitydate:opened', 'course'), 'timestamp' => $earlier, 'dataid' => 'timeopen'],
                    ['label' => get_string('activitydate:closed', 'course'), 'timestamp' => $before, 'dataid' => 'timeclose'],
                ]
            ],
            'with user override' => [
                $before, $after, $earlier, $later, null, null, [
                    ['label' => get_string('activitydate:opened', 'course'), 'timestamp' => $earlier, 'dataid' => 'timeopen'],
                    ['label' => get_string('activitydate:closes', 'course'), 'timestamp' => $later, 'dataid' => 'timeclose'],
                ]
            ],
            'with group override' => [
                $before, $after, null, null, $earlier, $later, [
                    ['label' => get_string('activitydate:opened', 'course'), 'timestamp' => $earlier, 'dataid' => 'timeopen'],
                    ['label' => get_string('activitydate:closes', 'course'), 'timestamp' => $later, 'dataid' => 'timeclose'],
                ]
            ],
            'with both user and group overrides' => [
                $before, $after, $earlier, $later, $earlier - DAYSECS, $later + DAYSECS, [
                    ['label' => get_string('activitydate:opened', 'course'), 'timestamp' => $earlier, 'dataid' => 'timeopen'],
                    ['label' => get_string('activitydate:closes', 'course'), 'timestamp' => $later, 'dataid' => 'timeclose'],
                ]
            ],
        ];
    }

    /**
     * Test for get_dates_for_module().
     *
     * @dataProvider get_dates_for_module_provider
     * @param int|null $timeopen Time of opening the quiz.
     * @param int|null $timeclose Time of closing the quiz.
     * @param int|null $usertimeopen The user override for opening the quiz.
     * @param int|null $usertimeclose The user override for closing the quiz.
     * @param int|null $grouptimeopen The group override for opening the quiz.
     * @param int|null $grouptimeclose The group override for closing the quiz.
     * @param array $expected The expected value of calling get_dates_for_module()
     */
    public function test_get_dates_for_module(?int $timeopen, ?int $timeclose,
            ?int $usertimeopen, ?int $usertimeclose,
            ?int $grouptimeopen, ?int $grouptimeclose,
            array $expected) {

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        /** @var \mod_quiz_generator $quizgenerator */
        $quizgenerator = $generator->get_plugin_generator('mod_quiz');

        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);

        $data = ['course' => $course->id];
        if ($timeopen) {
            $data['timeopen'] = $timeopen;
        }
        if ($timeclose) {
            $data['timeclose'] = $timeclose;
        }
        $quiz = $quizgenerator->create_instance($data);

        if ($usertimeopen || $usertimeclose || $grouptimeopen || $grouptimeclose) {
            $generator->enrol_user($user->id, $course->id);
            $group = $generator->create_group(['courseid' => $course->id]);
            $generator->create_group_member(['groupid' => $group->id, 'userid' => $user->id]);

            if ($usertimeopen || $usertimeclose) {
                $quizgenerator->create_override([
                    'quiz' => $quiz->id,
                    'userid' => $user->id,
                    'timeopen' => $usertimeopen,
                    'timeclose' => $usertimeclose,
                ]);
            }

            if ($grouptimeopen || $grouptimeclose) {
                $quizgenerator->create_override([
                    'quiz' => $quiz->id,
                    'groupid' => $group->id,
                    'timeopen' => $grouptimeopen,
                    'timeclose' => $grouptimeclose,
                ]);
            }
        }

        $this->setUser($user);

        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
        // Make sure we're using a cm_info object.
        $cm = cm_info::create($cm);

        $dates = activity_dates::get_dates_for_module($cm, (int) $user->id);

        $this->assertEquals($expected, $dates);
    }
}
