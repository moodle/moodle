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
 * Unit tests for (some of) mod/book/lib.php.
 *
 * @package    mod_book
 * @category   phpunit
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/book/lib.php');

/**
 * Unit tests for (some of) mod/book/lib.php.
 *
 * @package    mod_book
 * @category   phpunit
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_book_lib_testcase extends advanced_testcase {

    public function test_export_contents() {
        global $DB;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course(array('enablecomment' => 1));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        // Test book with 3 chapters.
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        $cm = get_coursemodule_from_id('book', $book->cmid);

        $bookgenerator = $this->getDataGenerator()->get_plugin_generator('mod_book');
        $chapter1 = $bookgenerator->create_chapter(array('bookid' => $book->id, "pagenum" => 1));
        $chapter2 = $bookgenerator->create_chapter(array('bookid' => $book->id, "pagenum" => 2));
        $subchapter = $bookgenerator->create_chapter(array('bookid' => $book->id, "pagenum" => 3, "subchapter" => 1));
        $chapter3 = $bookgenerator->create_chapter(array('bookid' => $book->id, "pagenum" => 4, "hidden" => 1));

        $this->setUser($user);

        $contents = book_export_contents($cm, '');
        // The hidden chapter must not be included, and additional page with the structure must be included.
        $this->assertCount(4, $contents);

        $this->assertEquals('structure', $contents[0]['filename']);
        $this->assertEquals('index.html', $contents[1]['filename']);
        $this->assertEquals('Chapter 1', $contents[1]['content']);
        $this->assertEquals('index.html', $contents[2]['filename']);
        $this->assertEquals('Chapter 2', $contents[2]['content']);
        $this->assertEquals('index.html', $contents[3]['filename']);
        $this->assertEquals('Chapter 3', $contents[3]['content']);

        // Test empty book.
        $emptybook = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        $cm = get_coursemodule_from_id('book', $emptybook->cmid);
        $contents = book_export_contents($cm, '');

        $this->assertCount(1, $contents);
        $this->assertEquals('structure', $contents[0]['filename']);
        $this->assertEquals(json_encode(array()), $contents[0]['content']);

    }
}
