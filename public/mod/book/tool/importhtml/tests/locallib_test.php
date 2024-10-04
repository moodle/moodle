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
final class locallib_test extends \advanced_testcase {

    /** @var object */
    private object $book;
    /** @var object */
    private object $context;
    /** @var object */
    private object $record;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $this->book = $this->getDataGenerator()->create_module('book', ['course' => $course->id]);
        $this->context = \context_module::instance($this->book->cmid);
        $this->record = (object) [
            'contextid' => $this->context->id,
            'component' => 'phpunit',
            'filearea'  => 'test',
            'itemid'    => 0,
            'filepath'  => '/',
        ];
    }

    /**
     * Tests the events within toolbook_importhtml_import_chapters
     * @covers ::toolbook_importhtml_import_chapters
     */
    public function test_import_chapters_events(): void {

        $this->record->filename = 'chapters.zip';
        $file = get_file_storage()->create_file_from_pathname(
            $this->record,
            self::get_fixture_path(__NAMESPACE__, 'chapters.zip')
        );

        // Importing the chapters.
        $sink = $this->redirectEvents();
        toolbook_importhtml_import_chapters($file, 2, $this->book, $this->context, false);
        $events = $sink->get_events();

        // Checking the results up to the triggered event.
        $this->assertCount(5, $events);
        foreach ($events as $event) {
            $this->assertInstanceOf('\mod_book\event\chapter_created', $event);
            $this->assertEquals($this->context, $event->get_context());
            $chapter = $event->get_record_snapshot('book_chapters', $event->objectid);
            $this->assertNotEmpty($chapter);
            $this->assertEventContextNotUsed($event);
        }
    }

    /**
     * Tests the conversion of (anchored) links within toolbook_importhtml_import_chapters
     * @covers ::toolbook_importhtml_import_chapters
     */
    public function test_import_chapters_links(): void {
        global $DB;

        $this->record->filename = 'chapters_links.zip';
        $file = get_file_storage()->create_file_from_pathname(
            $this->record,
            self::get_fixture_path(__NAMESPACE__, 'chapters_links.zip')
        );

        toolbook_importhtml_import_chapters($file, 2, $this->book, $this->context, false);

        $chapters = $DB->get_records('book_chapters', ['bookid' => $this->book->id]);
        foreach ($chapters as $chapter) {
            $this->assertStringNotContainsString('.html', $chapter->content);
            $this->assertStringNotContainsString('.html#anchor', $chapter->content);
            $this->assertMatchesRegularExpression('/chapterid=[0-9]*"/i', $chapter->content);
            $this->assertMatchesRegularExpression('/chapterid=[0-9]*#anchor"/i', $chapter->content);
        }
    }

}
