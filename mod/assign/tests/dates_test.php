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
 * Contains unit tests for mod_assign\dates.
 *
 * @package   mod_assign
 * @category  test
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_assign;

use advanced_testcase;
use cm_info;
use core\activity_dates;

/**
 * Class for unit testing mod_assign\dates.
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
                    ['label' => get_string('activitydate:submissionsopen', 'mod_assign'), 'timestamp' => $after,
                        'dataid' => 'allowsubmissionsfromdate'],
                ]
            ],
            'only with closing time' => [
                null, $after, null, null, null, null, [
                    ['label' => get_string('activitydate:submissionsdue', 'mod_assign'), 'timestamp' => $after,
                        'dataid' => 'duedate'],
                ]
            ],
            'with both times' => [
                $after, $later, null, null, null, null, [
                    ['label' => get_string('activitydate:submissionsopen', 'mod_assign'), 'timestamp' => $after,
                        'dataid' => 'allowsubmissionsfromdate'],
                    ['label' => get_string('activitydate:submissionsdue', 'mod_assign'), 'timestamp' => $later,
                        'dataid' => 'duedate'],
                ]
            ],
            'between the dates' => [
                $before, $after, null, null, null, null, [
                    ['label' => get_string('activitydate:submissionsopened', 'mod_assign'), 'timestamp' => $before,
                        'dataid' => 'allowsubmissionsfromdate'],
                    ['label' => get_string('activitydate:submissionsdue', 'mod_assign'), 'timestamp' => $after,
                        'dataid' => 'duedate'],
                ]
            ],
            'dates are past' => [
                $earlier, $before, null, null, null, null, [
                    ['label' => get_string('activitydate:submissionsopened', 'mod_assign'), 'timestamp' => $earlier,
                        'dataid' => 'allowsubmissionsfromdate'],
                    ['label' => get_string('activitydate:submissionsdue', 'mod_assign'), 'timestamp' => $before,
                        'dataid' => 'duedate'],
                ]
            ],
            'with user override' => [
                $before, $after, $earlier, $later, null, null, [
                    ['label' => get_string('activitydate:submissionsopened', 'mod_assign'), 'timestamp' => $earlier,
                        'dataid' => 'allowsubmissionsfromdate'],
                    ['label' => get_string('activitydate:submissionsdue', 'mod_assign'), 'timestamp' => $later,
                        'dataid' => 'duedate'],
                ]
            ],
            'with group override' => [
                $before, $after, null, null, $earlier, $later, [
                    ['label' => get_string('activitydate:submissionsopened', 'mod_assign'), 'timestamp' => $earlier,
                        'dataid' => 'allowsubmissionsfromdate'],
                    ['label' => get_string('activitydate:submissionsdue', 'mod_assign'), 'timestamp' => $later,
                        'dataid' => 'duedate'],
                ]
            ],
            'with both user and group overrides' => [
                $before, $after, $earlier, $later, $earlier - DAYSECS, $later + DAYSECS, [
                    ['label' => get_string('activitydate:submissionsopened', 'mod_assign'), 'timestamp' => $earlier,
                        'dataid' => 'allowsubmissionsfromdate'],
                    ['label' => get_string('activitydate:submissionsdue', 'mod_assign'), 'timestamp' => $later,
                        'dataid' => 'duedate'],
                ]
            ],
        ];
    }

    /**
     * Test for get_dates_for_module().
     *
     * @dataProvider get_dates_for_module_provider
     * @param int|null $from Time of opening submissions in the assignment.
     * @param int|null $due Assignment's due date.
     * @param int|null $userfrom The user override for opening submissions.
     * @param int|null $userdue The user override for due date.
     * @param int|null $groupfrom The group override for opening submissions.
     * @param int|null $groupdue The group override for due date.
     * @param array $expected The expected value of calling get_dates_for_module()
     */
    public function test_get_dates_for_module(?int $from, ?int $due,
            ?int $userfrom, ?int $userdue,
            ?int $groupfrom, ?int $groupdue,
            array $expected) {

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        /** @var \mod_assign_generator $assigngenerator */
        $assigngenerator = $generator->get_plugin_generator('mod_assign');

        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);

        $data = ['course' => $course->id];
        if ($from) {
            $data['allowsubmissionsfromdate'] = $from;
        }
        if ($due) {
            $data['duedate'] = $due;
        }
        $assign = $assigngenerator->create_instance($data);

        if ($userfrom || $userdue || $groupfrom || $groupdue) {
            $generator->enrol_user($user->id, $course->id);
            $group = $generator->create_group(['courseid' => $course->id]);
            $generator->create_group_member(['groupid' => $group->id, 'userid' => $user->id]);

            if ($userfrom || $userdue) {
                $assigngenerator->create_override([
                    'assignid' => $assign->id,
                    'userid' => $user->id,
                    'allowsubmissionsfromdate' => $userfrom,
                    'duedate' => $userdue,
                ]);
            }

            if ($groupfrom || $groupdue) {
                $assigngenerator->create_override([
                    'assignid' => $assign->id,
                    'groupid' => $group->id,
                    'allowsubmissionsfromdate' => $groupfrom,
                    'duedate' => $groupdue,
                ]);
            }
        }

        $this->setUser($user);

        $cm = get_coursemodule_from_instance('assign', $assign->id);
        // Make sure we're using a cm_info object.
        $cm = cm_info::create($cm);

        $dates = activity_dates::get_dates_for_module($cm, (int) $user->id);

        $this->assertEquals($expected, $dates);
    }
}
