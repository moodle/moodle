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

use mod_board\board;
use mod_board\local\column;
use mod_board\local\note;

/**
 * Test column helper class.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\local\column
 */
final class column_test extends \advanced_testcase {
    public function test_create(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course([]);

        $board = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
        ]);

        $column4 = column::create($board->id, 'Col X');
        $this->assertNotEmpty($column4->historyid);
        $this->assertSame($board->id, $column4->boardid);
        $this->assertSame('Col X', $column4->name);
        $this->assertSame('0', $column4->locked);
        $this->assertSame('4', $column4->sortorder);

        unset($column4->historyid);
        $this->assertEquals($column4, $DB->get_record('board_columns', ['id' => $column4->id]));
    }

    public function test_update(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course([]);

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $board = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
        ]);
        $column4 = $generator->create_column(['boardid' => $board->id, 'name' => 'Col X']);

        $column4 = column::update($column4->id, 'Col Y');
        $this->assertNotEmpty($column4->historyid);
        $this->assertSame($board->id, $column4->boardid);
        $this->assertSame('Col Y', $column4->name);
        $this->assertSame('0', $column4->locked);
        $this->assertSame('4', $column4->sortorder);

        unset($column4->historyid);
        $this->assertEquals($column4, $DB->get_record('board_columns', ['id' => $column4->id]));
    }

    public function test_lock(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course([]);

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $board = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
        ]);
        $column4 = $generator->create_column(['boardid' => $board->id, 'name' => 'Col X']);

        $historyid = column::lock($column4->id, true);
        $this->assertNotEmpty($historyid);
        $column4 = $DB->get_record('board_columns', ['id' => $column4->id], '*', MUST_EXIST);
        $this->assertSame($board->id, $column4->boardid);
        $this->assertSame('Col X', $column4->name);
        $this->assertSame('1', $column4->locked);
        $this->assertSame('4', $column4->sortorder);

        $historyid = column::lock($column4->id, false);
        $this->assertNotEmpty($historyid);
        $column4 = $DB->get_record('board_columns', ['id' => $column4->id], '*', MUST_EXIST);
        $this->assertSame($board->id, $column4->boardid);
        $this->assertSame('Col X', $column4->name);
        $this->assertSame('0', $column4->locked);
        $this->assertSame('4', $column4->sortorder);
    }

    public function test_delete(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course([]);
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');
        $fs = get_file_storage();

        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);
        $context = board::context_for_board($board);
        [$column1, $column2, $column3] = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));

        $note1 = $generator->create_note(['columnid' => $column1->id, 'userid' => $user->id]);
        $note2 = $generator->create_note(['columnid' => $column2->id, 'userid' => $user->id]);
        $comment1 = $generator->create_comment(['noteid' => $note1->id]);
        $comment2 = $generator->create_comment(['noteid' => $note2->id]);
        $this->setUser($user);
        \mod_board\local\note::rate($note1->id);
        \mod_board\local\note::rate($note2->id);
        $draftfile = $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6661,
            'filepath' => '/',
            'filename' => 'image.png',
        ], 'xx');
        $note1 = note::update_attachment(
            $note1->id,
            ['type' => board::MEDIATYPE_IMAGE, 'info' => 'Some image', 'draftitemid' => 6661],
            $context
        );
        $draftfile = $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 6662,
            'filepath' => '/',
            'filename' => 'image.png',
        ], 'xx');
        $note2 = note::update_attachment(
            $note2->id,
            ['type' => board::MEDIATYPE_IMAGE, 'info' => 'Some image', 'draftitemid' => 6662],
            $context
        );

        $historyid = column::delete($column1->id);
        $this->assertNotEmpty($historyid);

        $this->assertFalse($DB->record_exists('board_columns', ['id' => $column1->id]));
        $this->assertFalse($DB->record_exists('board_notes', ['columnid' => $column1->id]));
        $this->assertFalse($DB->record_exists('board_note_ratings', ['noteid' => $note1->id]));
        $this->assertFalse($DB->record_exists('board_comments', ['noteid' => $note1->id]));
        $this->assertFalse($fs->file_exists($context->id, 'mod_board', 'images', $note1->id, '/', 'image.png'));

        $this->assertTrue($DB->record_exists('board_columns', ['id' => $column2->id]));
        $this->assertTrue($DB->record_exists('board_notes', ['columnid' => $column2->id]));
        $this->assertTrue($DB->record_exists('board_note_ratings', ['noteid' => $note2->id]));
        $this->assertTrue($DB->record_exists('board_comments', ['noteid' => $note2->id]));
        $this->assertTrue($fs->file_exists($context->id, 'mod_board', 'images', $note2->id, '/', 'image.png'));
    }

    public function test_move(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course([]);

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $board = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
        ]);
        $generator->create_column(['boardid' => $board->id, 'name' => 'Col X']);
        [$column1, $column2, $column3, $column4]
            = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));
        $this->assertSame('1', $column1->sortorder);
        $this->assertSame('2', $column2->sortorder);
        $this->assertSame('3', $column3->sortorder);
        $this->assertSame('4', $column4->sortorder);

        $historyid = column::move($column4->id, 2);
        $this->assertNotEmpty($historyid);
        [$column1, $column2, $column3, $column4]
            = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));
        $this->assertSame('1', $column1->sortorder);
        $this->assertSame('2', $column2->sortorder);
        $this->assertSame('3', $column4->sortorder);
        $this->assertSame('4', $column3->sortorder);

        $historyid = column::move($column4->id, 2);
        $this->assertNotEmpty($historyid);        [$column1, $column2, $column3, $column4]
            = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));
        $this->assertSame('1', $column1->sortorder);
        $this->assertSame('2', $column2->sortorder);
        $this->assertSame('3', $column4->sortorder);
        $this->assertSame('4', $column3->sortorder);

        $historyid = column::move($column4->id, 0);
        $this->assertNotEmpty($historyid);
        [$column1, $column2, $column3, $column4]
            = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));
        $this->assertSame('1', $column4->sortorder);
        $this->assertSame('2', $column1->sortorder);
        $this->assertSame('3', $column2->sortorder);
        $this->assertSame('4', $column3->sortorder);

        $historyid = column::move($column4->id, 10);
        $this->assertNotEmpty($historyid);
        [$column1, $column2, $column3, $column4]
            = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'id ASC'));
        $this->assertSame('1', $column1->sortorder);
        $this->assertSame('2', $column2->sortorder);
        $this->assertSame('3', $column3->sortorder);
        $this->assertSame('4', $column4->sortorder);
    }
}
