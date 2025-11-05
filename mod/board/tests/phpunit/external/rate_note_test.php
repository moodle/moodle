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

use mod_board\external\rate_note;
use mod_board\board;

/**
 * Test external method for finding out if user can rate a note.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\external\rate_note
 */
final class rate_note_test extends \advanced_testcase {
    public function test_execute(): void {
        global $DB;

        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $course = $this->getDataGenerator()->create_course([]);
        $board0 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 1',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGDISABLED,
        ]);
        $board1 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 1',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGBYALL,
        ]);
        $board2 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 2',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGBYALL,
        ]);
        $board3 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 3',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PUBLIC,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGBYALL,
        ]);
        $board4 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 4',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => SEPARATEGROUPS,
            'addrating' => board::RATINGBYALL,
        ]);
        $board5 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 4',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => VISIBLEGROUPS,
            'addrating' => board::RATINGBYALL,
        ]);

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $teacher0 = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $student3 = $this->getDataGenerator()->create_user();
        $student4 = $this->getDataGenerator()->create_user();
        $student5 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher0->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student4->id, $course->id, 'guest');

        $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student2->id, 'groupid' => $group2->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student3->id, 'groupid' => $group1->id]);

        $columns0 = array_values($DB->get_records('board_columns', ['boardid' => $board0->id], 'id ASC'));
        $columns1 = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));
        $columns2 = array_values($DB->get_records('board_columns', ['boardid' => $board2->id], 'id ASC'));
        $columns3 = array_values($DB->get_records('board_columns', ['boardid' => $board3->id], 'id ASC'));
        $columns4 = array_values($DB->get_records('board_columns', ['boardid' => $board4->id], 'id ASC'));
        $columns5 = array_values($DB->get_records('board_columns', ['boardid' => $board5->id], 'id ASC'));

        $note0x1 = $generator->create_note(['columnid' => $columns0[0]->id, 'userid' => $student1->id]);
        $note1x1 = $generator->create_note(['columnid' => $columns1[0]->id, 'userid' => $student1->id]);
        $note2x1 = $generator->create_note(['columnid' => $columns2[0]->id, 'userid' => $student1->id]);
        $note3x1 = $generator->create_note(['columnid' => $columns3[0]->id, 'userid' => $student1->id]);
        $note4x1 = $generator->create_note(['columnid' => $columns4[0]->id, 'userid' => $student1->id, 'groupid' => $group1->id]);
        $note5x1 = $generator->create_note(['columnid' => $columns5[0]->id, 'userid' => $student1->id, 'groupid' => $group1->id]);

        $this->setUser($student1);

        $result = rate_note::execute($note1x1->id);
        $result = rate_note::clean_returnvalue(rate_note::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertSame(1, $result['rating']);
        $this->assertNotEmpty($result['historyid']);

        $this->setUser($student2);

        $result = rate_note::execute($note1x1->id);
        $result = rate_note::clean_returnvalue(rate_note::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertSame(2, $result['rating']);
        $this->assertNotEmpty($result['historyid']);

        $result = rate_note::execute($note2x1->id);
        $result = rate_note::clean_returnvalue(rate_note::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertSame(0, $result['rating']);
        $this->assertSame(0, $result['historyid']);

        $this->setUser($student4);

        try {
            rate_note::execute($note0x1->id);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\core\exception\require_login_exception::class, $ex);
            $this->assertSame('Course or activity not accessible. (Activity is hidden)', $ex->getMessage());
        }

        $this->setUser($student5);

        try {
            rate_note::execute($note0x1->id);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\core\exception\require_login_exception::class, $ex);
            $this->assertSame('Course or activity not accessible. (Not enrolled)', $ex->getMessage());
        }
    }
}
