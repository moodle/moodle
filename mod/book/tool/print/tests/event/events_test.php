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

/**
 * Events tests.
 *
 * @package    booktool_print
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace booktool_print\event;

/**
 * Events tests class.
 *
 * @package    booktool_print
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class events_test extends \advanced_testcase {

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    public function test_book_printed(): void {
        // There is no proper API to call to test the event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        $context = \context_module::instance($book->cmid);

        $event = \booktool_print\event\book_printed::create_from_book($book, $context);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\booktool_print\event\book_printed', $event);
        $this->assertEquals(\context_module::instance($book->cmid), $event->get_context());
        $this->assertEquals($book->id, $event->objectid);
    }


    public function test_chapter_printed(): void {
        // There is no proper API to call to test the event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        $bookgenerator = $this->getDataGenerator()->get_plugin_generator('mod_book');
        $chapter = $bookgenerator->create_chapter(array('bookid' => $book->id));
        $context = \context_module::instance($book->cmid);

        $event = \booktool_print\event\chapter_printed::create_from_chapter($book, $context, $chapter);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\booktool_print\event\chapter_printed', $event);
        $this->assertEquals(\context_module::instance($book->cmid), $event->get_context());
        $this->assertEquals($chapter->id, $event->objectid);
    }

}
