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

namespace mod_feedback;

use advanced_testcase;

/**
 * Class for unit testing mod_feedback\dates.
 *
 * @category  test
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2025 Laurent David <laurent.david@moodle.com>
 * @package   mod_feedback
 * @covers \mod_feedback\manager
 */
final class manager_test extends advanced_testcase {
    /**
     * Data provider for test_can_see_others_in_groups.
     *
     * @return array[]
     */
    public static function can_see_others_in_groups_provider(): array {
        return [
            'student no groups, separate group' => [
                'mode' => SEPARATEGROUPS, 'username' => 's1', 'expected' => false,
            ],
            'student in groups, separate group' => [
                'mode' => SEPARATEGROUPS, 'username' => 's2', 'expected' => true,
            ],
            'non editing teacher no groups, separate group' => [
                'mode' => SEPARATEGROUPS, 'username' => 't1', 'expected' => false,
            ],
            'non editing teacher in groups, separate group' => [
                'mode' => SEPARATEGROUPS, 'username' => 't2', 'expected' => true,
            ],
            'editing teacher no groups, separate group' => [
                'mode' => SEPARATEGROUPS, 'username' => 't3', 'expected' => true,
            ],
            'editing teacher in groups, separate group' => [
                'mode' => SEPARATEGROUPS, 'username' => 't4', 'expected' => true,
            ],
            'student no groups, visible group' => [
                'mode' => VISIBLEGROUPS, 'username' => 's1', 'expected' => true,
            ],
            'student in groups, visible group' => [
                'mode' => VISIBLEGROUPS, 'username' => 's2', 'expected' => true,
            ],
            'non editing teacher no groups, visible group' => [
                'mode' => VISIBLEGROUPS, 'username' => 't1', 'expected' => true,
            ],
            'non editing teacher in groups, visible group' => [
                'mode' => VISIBLEGROUPS, 'username' => 't2', 'expected' => true,
            ],
            'editing teacher no groups, visible group' => [
                'mode' => VISIBLEGROUPS, 'username' => 't3', 'expected' => true,
            ],
            'editing teacher in groups, visible group' => [
                'mode' => VISIBLEGROUPS, 'username' => 't4', 'expected' => true,
            ],
        ];
    }

    /**
     * Test if we can see or not others in groups.
     *
     * @param int $mode The group mode.
     * @param string $username The username of the user to test.
     * @param bool $expected The expected result.
     *
     * @covers ::can_see_others_in_groups
     * @dataProvider can_see_others_in_groups_provider
     */
    public function test_can_see_others_in_groups(int $mode, string $username, bool $expected): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['groupmode' => $mode, 'groupmodeforce' => 1]);
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Group 1']);
        $users = [];
        $data = [
            's1' => 'student',
            's2' => 'student',
            't1' => 'teacher',
            't2' => 'teacher',
            't3' => 'editingteacher',
            't4' => 'editingteacher',
        ];
        foreach ($data as $user => $role) {
            $users[$user] = $this->getDataGenerator()->create_and_enrol($course, $role, $user);
        }
        foreach (['s2', 't2', 't3'] as $uname) {
            $user = $users[$uname];
            $this->getDataGenerator()->create_group_member(
                ['groupid' => $group->id, 'userid' => $user->id]
            );
        }
        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course]);
        $this->setUser($users[$username]);
        $cm = get_fast_modinfo($course)->cms[$feedback->cmid];
        $this->assertEquals($expected, manager::can_see_others_in_groups($cm));
    }
}
