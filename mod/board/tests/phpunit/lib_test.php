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

namespace mod_board\phpunit;

use mod_board\board;
use mod_board\local\note;

/**
 * Board lib.php tests.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class lib_test extends \advanced_testcase {
    /**
     * Test deleting of board activity.
     * @covers \board_delete_instance
     */
    public function test_board_delete_instance(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course([]);
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');
        $fs = get_file_storage();

        $board1 = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);
        $context1 = board::context_for_board($board1);
        $board2 = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);
        $context2 = board::context_for_board($board2);

        $column1 = array_values($DB->get_records('board_columns', ['boardid' => $board1->id], 'id ASC'))[0];
        $column2 = array_values($DB->get_records('board_columns', ['boardid' => $board2->id], 'id ASC'))[0];

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
            $context1
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
            $context2
        );

        board_delete_instance($board1->id);

        $this->assertFalse($DB->record_exists('board_columns', ['boardid' => $board1->id]));
        $this->assertFalse($DB->record_exists('board_columns', ['id' => $column1->id]));
        $this->assertFalse($DB->record_exists('board_notes', ['columnid' => $column1->id]));
        $this->assertFalse($DB->record_exists('board_note_ratings', ['noteid' => $note1->id]));
        $this->assertFalse($DB->record_exists('board_comments', ['noteid' => $note1->id]));
        $this->assertFalse($fs->file_exists($context1->id, 'mod_board', 'images', $note1->id, '/', 'image.png'));

        $this->assertTrue($DB->record_exists('board_columns', ['id' => $column2->id]));
        $this->assertTrue($DB->record_exists('board_columns', ['boardid' => $board2->id]));
        $this->assertTrue($DB->record_exists('board_notes', ['columnid' => $column2->id]));
        $this->assertTrue($DB->record_exists('board_note_ratings', ['noteid' => $note2->id]));
        $this->assertTrue($DB->record_exists('board_comments', ['noteid' => $note2->id]));
        $this->assertTrue($fs->file_exists($context2->id, 'mod_board', 'images', $note2->id, '/', 'image.png'));
    }
}
