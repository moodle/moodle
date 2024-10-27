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
 * booktool_importhtml tests.
 *
 * @package    booktool_importhtml
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace booktool_importhtml;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot.'/mod/book/tool/importhtml/locallib.php');

/**
 * booktool_importhtml tests class.
 *
 * @package    booktool_importhtml
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class locallib_test extends \advanced_testcase {

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    public function test_import_chapters_events(): void {
        $course = $this->getDataGenerator()->create_course();
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        $context = \context_module::instance($book->cmid);

        $record = new \stdClass();
        $record->contextid = $context->id;
        $record->component = 'phpunit';
        $record->filearea = 'test';
        $record->itemid = 0;
        $record->filepath = '/';
        $record->filename = 'chapters.zip';

        $fs = get_file_storage();
        $file = $fs->create_file_from_pathname($record, self::get_fixture_path(__NAMESPACE__, 'chapters.zip'));

        // Importing the chapters.
        $sink = $this->redirectEvents();
        toolbook_importhtml_import_chapters($file, 2, $book, $context, false);
        $events = $sink->get_events();

        // Checking the results.
        $this->assertCount(5, $events);
        foreach ($events as $event) {
            $this->assertInstanceOf('\mod_book\event\chapter_created', $event);
            $this->assertEquals($context, $event->get_context());
            $chapter = $event->get_record_snapshot('book_chapters', $event->objectid);
            $this->assertNotEmpty($chapter);
            $this->assertEventContextNotUsed($event);
        }
    }

}
