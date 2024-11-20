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
namespace mod_book;
/**
 * Helper test class
 *
 * @package    mod_book
 * @copyright  2023 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper_test extends \advanced_testcase {

    /**
     * Test view_book
     * @covers \mod_book\helper::is_last_visible_chapter
     */
    public function test_is_last_chapter(): void {
        $this->resetAfterTest(true);

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        $bookgenerator = $this->getDataGenerator()->get_plugin_generator('mod_book');
        $firstchapter =
            $bookgenerator->create_chapter(array('bookid' => $book->id, 'pagenum' => 1)); // Create a first chapter to check that
        // viewing the last chapter is enough for completing the activity.
        $chapterhidden = $bookgenerator->create_chapter(array('bookid' => $book->id, 'hidden' => 1, 'pagenum' => 2));
        $lastchapter = $bookgenerator->create_chapter(array('bookid' => $book->id, 'pagenum' => 3));
        $chapters = book_preload_chapters($book);
        $this->assertFalse(helper::is_last_visible_chapter($firstchapter->id, $chapters));
        $this->assertFalse(helper::is_last_visible_chapter($chapterhidden->id, $chapters));
        $this->assertTrue(helper::is_last_visible_chapter($lastchapter->id, $chapters));
    }
}
