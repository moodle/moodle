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

namespace mod_board\phpunit\local;

use mod_board\local\comment;
use mod_board\board;

/**
 * Test comment helper class.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\local\comment
 */
final class comment_test extends \advanced_testcase {
    public function test_create(): void {
        global $DB;
        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $course = $this->getDataGenerator()->create_course([]);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $board1 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);

        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));

        $note1 = $generator->create_note(['columnid' => $column1->id, 'userid' => $user2->id]);
        $note2 = $generator->create_note(['columnid' => $column1->id, 'userid' => $user1->id]);

        $this->setUser($user1);

        $this->setCurrentTimeStart();
        $comment1 = comment::create($note1->id, 'Cmt 1');
        $this->assertSame($note1->id, $comment1->noteid);
        $this->assertSame('Cmt 1', $comment1->content);
        $this->assertSame($user1->id, $comment1->userid);
        $this->assertTimeCurrent($comment1->timecreated);
        $this->assertSame('0', $comment1->deleted);
    }

    public function test_delete(): void {
        global $DB;
        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $course = $this->getDataGenerator()->create_course([]);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $board1 = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
        ]);

        [$column1, $column2, $column3]
            = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'));

        $note1 = $generator->create_note(['columnid' => $column1->id, 'userid' => $user2->id]);
        $note2 = $generator->create_note(['columnid' => $column1->id, 'userid' => $user1->id]);

        $this->setUser($user1);

        $comment1 = comment::create($note1->id, 'Cmt 1');
        $comment2 = comment::create($note1->id, 'Cmt 2');

        $this->setCurrentTimeStart();
        comment::delete($comment1->id);

        $comment1x = $DB->get_record('board_comments', ['id' => $comment1->id]);
        $this->assertSame($note1->id, $comment1x->noteid);
        $this->assertSame('Cmt 1', $comment1x->content);
        $this->assertSame($user1->id, $comment1x->userid);
        $this->assertSame($comment1->timecreated, $comment1x->timecreated);
        $this->assertSame('1', $comment1x->deleted);
    }
}
