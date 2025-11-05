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

namespace mod_board\phpunit\event;

use mod_board\board;
use mod_board\local\column;
use mod_board\local\note;
use mod_board\local\comment;

/**
 * Test delete_comment event.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\event\delete_comment
 */
final class delete_comment_test extends \advanced_testcase {
    public function test_event(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $user = $this->getDataGenerator()->create_user();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);
        $context = board::context_for_board($board);

        $column = column::create($board->id, 'Col A');

        $this->setUser($user);

        $note = note::create($column->id, $user->id, 0, 'NH 1', 'NC 1', []);
        $comment = comment::create($note->id, 'Comment 1');

        $sink = $this->redirectEvents();
        comment::delete($comment->id);
        $events = $sink->get_events();
        $sink->close();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(\mod_board\event\delete_comment::class, $event);
        $this->assertSame($comment->id, $event->objectid);
        $this->assertSame('board_comments', $event->objecttable);
        $this->assertSame($context->id, $event->contextid);
        $this->assertSame($user->id, $event->userid);
        $this->assertSame('d', $event->crud);
        $this->assertSame($event::LEVEL_PARTICIPATING, $event->edulevel);
        $this->assertSame('Comment deleted', $event->get_name());
        $this->assertSame($note->id, $event->other['noteid']);
        $this->assertIsString($event->get_description());
        $this->assertSame('/mod/board/view.php?id=' . $board->cmid, $event->get_url()->out_as_local_url(false));
    }
}
