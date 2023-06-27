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
 * Genarator tests.
 *
 * @package    mod_book
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Genarator tests class.
 *
 * @package    mod_book
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_book_generator_testcase extends advanced_testcase {

    public function test_create_instance() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        $this->assertFalse($DB->record_exists('book', array('course' => $course->id)));
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        $this->assertEquals(1, $DB->count_records('book', array('course' => $course->id)));
        $this->assertTrue($DB->record_exists('book', array('course' => $course->id, 'id' => $book->id)));

        $params = array('course' => $course->id, 'name' => 'One more book');
        $book = $this->getDataGenerator()->create_module('book', $params);
        $this->assertEquals(2, $DB->count_records('book', array('course' => $course->id)));
        $this->assertEquals('One more book', $DB->get_field_select('book', 'name', 'id = :id', array('id' => $book->id)));
    }

    public function test_create_chapter() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        $bookgenerator = $this->getDataGenerator()->get_plugin_generator('mod_book');

        $this->assertFalse($DB->record_exists('book_chapters', array('bookid' => $book->id)));
        $bookgenerator->create_chapter(array('bookid' => $book->id));
        $this->assertTrue($DB->record_exists('book_chapters', array('bookid' => $book->id)));

        $chapter = $bookgenerator->create_chapter(
            array('bookid' => $book->id, 'content' => 'Yay!', 'title' => 'Oops', 'tags' => array('Cats', 'mice')));
        $this->assertEquals(2, $DB->count_records('book_chapters', array('bookid' => $book->id)));
        $this->assertEquals('Oops', $DB->get_field_select('book_chapters', 'title', 'id = :id', array('id' => $chapter->id)));
        $this->assertEquals('Yay!', $DB->get_field_select('book_chapters', 'content', 'id = :id', array('id' => $chapter->id)));
        $this->assertEquals(array('Cats', 'mice'),
            array_values(core_tag_tag::get_item_tags_array('mod_book', 'book_chapters', $chapter->id)));

        $chapter = $bookgenerator->create_content($book);
        $this->assertEquals(3, $DB->count_records('book_chapters', array('bookid' => $book->id)));
    }

}
