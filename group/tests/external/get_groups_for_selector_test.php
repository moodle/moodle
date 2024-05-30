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

namespace core_group\external;

/**
 * Test for external event.
 *
 * @package   core_group
 * @copyright 2024 The Open University.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.4
 * @covers \core_group\external\get_groups_for_selector
 */
final class get_groups_for_selector_test extends \advanced_testcase {
    /**
     * Test test_get_groups_for_selector service.
     */
    public function test_get_groups_for_selector(): void {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $this->setAdminUser();

        // Setup user.
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        // Setup group 1.
        $groupinga = $this->getDataGenerator()->create_grouping(['courseid' => $course->id, 'name' => 'Grouping A']);
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Group 1 in Grouping A']);
        $this->getDataGenerator()->create_grouping_group([
            'groupid' => $group1->id,
            'groupingid' => $groupinga->id,
        ]);
        $this->getDataGenerator()->create_group_member(['groupid' => $group1->id, 'userid' => $user->id]);

        // Setup group 2.
        $groupingb = $this->getDataGenerator()->create_grouping(['courseid' => $course->id, 'name' => 'Grouping B']);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Group 2 in Grouping B']);
        $this->getDataGenerator()->create_grouping_group([
            'groupid' => $group2->id,
            'groupingid' => $groupingb->id,
        ]);

        $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Group not in any grouping']);

        // By default, group mode of quiz is no group.
        $groups = get_groups_for_selector::execute($course->id, $quiz->cmid);
        $this->assertCount(0, $groups['groups']);

        // Get group in course.
        $coursegroups = get_groups_for_selector::execute($course->id);
        // By default, group mode of course is no group.
        $this->assertCount(0, $coursegroups['groups']);

        // Now set the quiz to be group mode: separate group, and re-test.
        $DB->set_field('course_modules', 'groupmode', SEPARATEGROUPS, ['id' => $quiz->cmid]);
        $groups = get_groups_for_selector::execute($course->id, $quiz->cmid);

        $groupnames = [
            'All participants',
            'Group 1 in Grouping A',
            'Group 2 in Grouping B',
            'Group not in any grouping',
        ];
        // It contains two item: All participant and the new groups: Group 1 in Grouping A,
        // Group 2 in Grouping B, Group not in any grouping.
        $this->assertCount(4, $groups['groups']);

        foreach ($groups['groups'] as $key => $group) {
            $this->assertEquals($groupnames[$key], $group->name);
        }

        // Similarly, set up a group for the course.
        $DB->set_field('course', 'groupmode', SEPARATEGROUPS, ['id' => $course->id]);
        $coursegroups = get_groups_for_selector::execute($course->id);

        // It contains two item: All participant and the new group.
        $this->assertCount(4, $coursegroups['groups']);
        foreach ($coursegroups['groups'] as $key => $group) {
            $this->assertEquals($groupnames[$key], $group->name);
        }
    }
}
