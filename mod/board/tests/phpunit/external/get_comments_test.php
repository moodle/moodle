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

use mod_board\external\get_comments;
use mod_board\local\comment;
use mod_board\board;

/**
 * Test external method for getting of comments.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\external\get_comments
 */
final class get_comments_test extends \advanced_testcase {
    public function test_execute(): void {
        global $DB;

        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $course = $this->getDataGenerator()->create_course([]);
        $board1 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 1',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);

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

        $columns1 = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));

        $note1 = $generator->create_note(['columnid' => $columns1[0]->id, 'userid' => $student1->id]);
        $note2 = $generator->create_note(['columnid' => $columns1[0]->id, 'userid' => $student1->id]);
        $note3 = $generator->create_note(['columnid' => $columns1[0]->id, 'userid' => $teacher0->id]);

        $this->setUser($student1);

        $comment1x1 = comment::create($note1->id, 'C 1x1');
        $comment1x2 = comment::create($note1->id, 'C 1x2');
        $comment3x1 = comment::create($note3->id, 'C 3x1');

        $result = get_comments::execute($note1->id);
        $result = get_comments::clean_returnvalue(get_comments::execute_returns(), $result);
        $this->assertSame((int)$note1->id, $result['noteid']);
        $this->assertSame(2, $result['commentcount']);
        $this->assertSame(true, $result['canpost']);
        $this->assertSame((int)$comment1x2->id, $result['comments'][0]['id']);
        $this->assertSame((int)$comment1x1->id, $result['comments'][1]['id']);

        $this->setUser($student2);
    }
}
