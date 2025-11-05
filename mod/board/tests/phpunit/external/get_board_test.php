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

namespace mod_board\phpunit\external;

use mod_board\external\get_board;
use mod_board\board;

/**
 * Test external method for getting of board data.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\external\get_board
 */
final class get_board_test extends \advanced_testcase {
    public function test_execute(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $board1 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 1',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGDISABLED,
            'hideheaders' => 0,
            'sortby' => board::SORTBYDATE,
            'enableblanktarget' => 0,
        ]);
        $cm1 = get_coursemodule_from_instance('board', $board1->id, $course->id, false, MUST_EXIST);
        $context1 = \context_module::instance($cm1->id);
        $board2 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 2',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGDISABLED,
            'hideheaders' => 0,
            'sortby' => board::SORTBYDATE,
            'enableblanktarget' => 0,
        ]);
        $cm2 = get_coursemodule_from_instance('board', $board2->id, $course->id, false, MUST_EXIST);
        $context2 = \context_module::instance($cm2->id);
        $board3 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 3',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PUBLIC,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGDISABLED,
            'hideheaders' => 0,
            'sortby' => board::SORTBYDATE,
            'enableblanktarget' => 0,
        ]);
        $cm3 = get_coursemodule_from_instance('board', $board3->id, $course->id, false, MUST_EXIST);
        $context3 = \context_module::instance($cm3->id);
        $board4 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 4',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => SEPARATEGROUPS,
            'addrating' => board::RATINGDISABLED,
            'hideheaders' => 0,
            'sortby' => board::SORTBYDATE,
            'enableblanktarget' => 0,
        ]);
        $cm4 = get_coursemodule_from_instance('board', $board4->id, $course->id, false, MUST_EXIST);
        $context4 = \context_module::instance($cm4->id);

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $teacher1 = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $student3 = $this->getDataGenerator()->create_user();
        $student4 = $this->getDataGenerator()->create_user();
        $student5 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher1->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student4->id, $course->id, 'guest');

        $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student2->id, 'groupid' => $group2->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student3->id, 'groupid' => $group1->id]);

        $columns1 = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));
        $columns2 = array_values($DB->get_records('board_columns', ['boardid' => $board2->id], 'id ASC'));
        $columns3 = array_values($DB->get_records('board_columns', ['boardid' => $board3->id], 'id ASC'));
        $columns4 = array_values($DB->get_records('board_columns', ['boardid' => $board4->id], 'id ASC'));

        $this->setUser($student1);

        $response = get_board::execute($board1->id, 0, 0);
        $response = get_board::clean_returnvalue(get_board::execute_returns(), $response);
        $this->assertCount(3, $response);
        $this->assertSame((int)$columns1[0]->id, $response[0]['id']);
        $this->assertSame($columns1[0]->name, $response[0]['name']);
        $this->assertSame(false, $response[0]['locked']);
        $this->assertSame([], $response[0]['notes']);

        // NOTE: add more coverage later.
    }
}
