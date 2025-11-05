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

/**
 * Test rate_note event.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\event\rate_note
 */
final class rate_note_test extends \advanced_testcase {
    public function test_event(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $user = $this->getDataGenerator()->create_user();
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);
        $context = board::context_for_board($board);

        $column = column::create($board->id, 'Col A');

        $this->setUser($user);

        set_config('addratingtolog', 1, 'mod_board');

        $note = note::create($column->id, $user->id, 0, 'NH 1', 'NC 1', []);

        $sink = $this->redirectEvents();
        note::rate($note->id);
        $note = board::get_note($note->id, MUST_EXIST);
        $events = $sink->get_events();
        $sink->close();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf(\mod_board\event\rate_note::class, $event);
        $this->assertSame($note->id, $event->objectid);
        $this->assertSame('board_notes', $event->objecttable);
        $this->assertSame($context->id, $event->contextid);
        $this->assertSame($user->id, $event->userid);
        $this->assertSame('u', $event->crud);
        $this->assertSame($event::LEVEL_PARTICIPATING, $event->edulevel);
        $this->assertSame('Post rated', $event->get_name());
        $this->assertSame($column->id, $event->other['columnid']);
        $this->assertSame(1, $event->other['rating']);
        $this->assertIsString($event->get_description());
        $this->assertSame('/mod/board/view.php?id=' . $board->cmid, $event->get_url()->out_as_local_url(false));

        set_config('addratingtolog', 0, 'mod_board');

        $sink = $this->redirectEvents();
        note::rate($note->id);
        $events = $sink->get_events();
        $sink->close();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertSame(null, $event->other['rating']);
        $this->assertIsString($event->get_description());
    }
}
