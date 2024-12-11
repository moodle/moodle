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
 * Contains unit tests for mod_lesson\dates.
 *
 * @package   mod_lesson
 * @category  test
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_lesson;

use advanced_testcase;
use cm_info;
use core\activity_dates;

/**
 * Class for unit testing mod_lesson\dates.
 *
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class dates_test extends advanced_testcase {

    /**
     * Data provider for get_dates_for_module().
     * @return array[]
     */
    public static function get_dates_for_module_provider(): array {
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
                    ['label' => get_string('activitydate:opens', 'course'), 'timestamp' => $after, 'dataid' => 'available'],
                ]
            ],
            'only with closing time' => [
                null, $after, null, null, null, null, [
                    ['label' => get_string('activitydate:closes', 'course'), 'timestamp' => $after, 'dataid' => 'deadline'],
                ]
            ],
            'with both times' => [
                $after, $later, null, null, null, null, [
                    ['label' => get_string('activitydate:opens', 'course'), 'timestamp' => $after, 'dataid' => 'available'],
                    ['label' => get_string('activitydate:closes', 'course'), 'timestamp' => $later, 'dataid' => 'deadline'],
                ]
            ],
            'between the dates' => [
                $before, $after, null, null, null, null, [
                    ['label' => get_string('activitydate:opened', 'course'), 'timestamp' => $before, 'dataid' => 'available'],
                    ['label' => get_string('activitydate:closes', 'course'), 'timestamp' => $after, 'dataid' => 'deadline'],
                ]
            ],
            'dates are past' => [
                $earlier, $before, null, null, null, null, [
                    ['label' => get_string('activitydate:opened', 'course'), 'timestamp' => $earlier, 'dataid' => 'available'],
                    ['label' => get_string('activitydate:closed', 'course'), 'timestamp' => $before, 'dataid' => 'deadline'],
                ]
            ],
            'with user override' => [
                $before, $after, $earlier, $later, null, null, [
                    ['label' => get_string('activitydate:opened', 'course'), 'timestamp' => $earlier, 'dataid' => 'available'],
                    ['label' => get_string('activitydate:closes', 'course'), 'timestamp' => $later, 'dataid' => 'deadline'],
                ]
            ],
            'with group override' => [
                $before, $after, null, null, $earlier, $later, [
                    ['label' => get_string('activitydate:opened', 'course'), 'timestamp' => $earlier, 'dataid' => 'available'],
                    ['label' => get_string('activitydate:closes', 'course'), 'timestamp' => $later, 'dataid' => 'deadline'],
                ]
            ],
            'with both user and group overrides' => [
                $before, $after, $earlier, $later, $earlier - DAYSECS, $later + DAYSECS, [
                    ['label' => get_string('activitydate:opened', 'course'), 'timestamp' => $earlier, 'dataid' => 'available'],
                    ['label' => get_string('activitydate:closes', 'course'), 'timestamp' => $later, 'dataid' => 'deadline'],
                ]
            ],
        ];
    }

    /**
     * Test for get_dates_for_module().
     *
     * @dataProvider get_dates_for_module_provider
     * @param int|null $available The 'available from' value of the lesson.
     * @param int|null $deadline The lesson's deadline.
     * @param int|null $useravailable The user override for opening the lesson.
     * @param int|null $userdeadline The user override for deadline of the lesson.
     * @param int|null $groupavailable The group override for opening the lesson.
     * @param int|null $groupuserdeadline The group override for deadline of the lesson.
     * @param array $expected The expected value of calling get_dates_for_module()
     */
    public function test_get_dates_for_module(?int $available, ?int $deadline,
            ?int $useravailable, ?int $userdeadline,
            ?int $groupavailable, ?int $groupuserdeadline,
            array $expected): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        /** @var \mod_lesson_generator $lessongenerator */
        $lessongenerator = $generator->get_plugin_generator('mod_lesson');

        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);

        $data = ['course' => $course->id];
        if ($available) {
            $data['available'] = $available;
        }
        if ($deadline) {
            $data['deadline'] = $deadline;
        }
        $this->setAdminUser();
        $lesson = $lessongenerator->create_instance($data);

        if ($useravailable || $userdeadline || $groupavailable || $groupuserdeadline) {
            $generator->enrol_user($user->id, $course->id);
            $group = $generator->create_group(['courseid' => $course->id]);
            $generator->create_group_member(['groupid' => $group->id, 'userid' => $user->id]);

            if ($useravailable || $userdeadline) {
                $lessongenerator->create_override([
                    'lessonid' => $lesson->id,
                    'userid' => $user->id,
                    'available' => $useravailable,
                    'deadline' => $userdeadline,
                ]);
            }

            if ($groupavailable || $groupuserdeadline) {
                $lessongenerator->create_override([
                    'lessonid' => $lesson->id,
                    'groupid' => $group->id,
                    'available' => $groupavailable,
                    'deadline' => $groupuserdeadline,
                ]);
            }
        }

        $this->setUser($user);

        $cm = get_coursemodule_from_instance('lesson', $lesson->id);
        // Make sure we're using a cm_info object.
        $cm = cm_info::create($cm);

        $dates = activity_dates::get_dates_for_module($cm, (int) $user->id);

        $this->assertEquals($expected, $dates);
    }
}
