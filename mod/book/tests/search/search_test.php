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
 * Book search unit tests.
 *
 * @package     mod_book
 * @category    test
 * @copyright   2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_book\search;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');

/**
 * Provides the unit tests for book search.
 *
 * @package     mod_book
 * @category    test
 * @copyright   2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_test extends \advanced_testcase {

    /**
     * @var string Area id
     */
    protected $bookchapterareaid = null;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        set_config('enableglobalsearch', true);

        $this->bookchapterareaid = \core_search\manager::generate_areaid('mod_book', 'chapter');

        // Set \core_search::instance to the mock_search_engine as we don't require the search engine to be working to test this.
        $search = \testable_core_search::instance();
    }

    /**
     * Availability.
     *
     * @return void
     */
    public function test_search_enabled(): void {

        $searcharea = \core_search\manager::get_search_area($this->bookchapterareaid);
        list($componentname, $varname) = $searcharea->get_config_var_name();

        // Enabled by default once global search is enabled.
        $this->assertTrue($searcharea->is_enabled());

        set_config($varname . '_enabled', 0, $componentname);
        $this->assertFalse($searcharea->is_enabled());

        set_config($varname . '_enabled', 1, $componentname);
        $this->assertTrue($searcharea->is_enabled());
    }

    /**
     * Indexing chapter contents.
     *
     * @return void
     */
    public function test_chapters_indexing(): void {
        global $DB;

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->bookchapterareaid);
        $this->assertInstanceOf('\mod_book\search\chapter', $searcharea);

        $course1 = self::getDataGenerator()->create_course();
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course1->id));

        $bookgenerator = $this->getDataGenerator()->get_plugin_generator('mod_book');
        $chapter1 = $bookgenerator->create_chapter(array('bookid' => $book->id, 'content' => 'Chapter1', 'title' => 'Title1'));
        $chapter2 = $bookgenerator->create_chapter(array('bookid' => $book->id, 'content' => 'Chapter2', 'title' => 'Title2'));

        // All records.
        $recordset = $searcharea->get_recordset_by_timestamp(0);
        $this->assertTrue($recordset->valid());
        $nrecords = 0;
        foreach ($recordset as $record) {
            $this->assertInstanceOf('stdClass', $record);
            $doc = $searcharea->get_document($record);
            $this->assertInstanceOf('\core_search\document', $doc);

            // Static caches are working.
            $dbreads = $DB->perf_get_reads();
            $doc = $searcharea->get_document($record);
            $this->assertEquals($dbreads, $DB->perf_get_reads());
            $this->assertInstanceOf('\core_search\document', $doc);
            $nrecords++;
        }
        // If there would be an error/failure in the foreach above the recordset would be closed on shutdown.
        $recordset->close();
        $this->assertEquals(2, $nrecords);

        // The +2 is to prevent race conditions.
        $recordset = $searcharea->get_recordset_by_timestamp(time() + 2);

        // No new records.
        $this->assertFalse($recordset->valid());
        $recordset->close();

        // Create another book and chapter.
        $book2 = $this->getDataGenerator()->create_module('book', array('course' => $course1->id));
        $bookgenerator->create_chapter(array('bookid' => $book2->id,
                'content' => 'Chapter3', 'title' => 'Title3'));

        // Query by context, first book.
        $recordset = $searcharea->get_document_recordset(0, \context_module::instance($book->cmid));
        $this->assertEquals(2, iterator_count($recordset));
        $recordset->close();

        // Second book.
        $recordset = $searcharea->get_document_recordset(0, \context_module::instance($book2->cmid));
        $this->assertEquals(1, iterator_count($recordset));
        $recordset->close();

        // Course.
        $recordset = $searcharea->get_document_recordset(0, \context_course::instance($course1->id));
        $this->assertEquals(3, iterator_count($recordset));
        $recordset->close();
    }

    /**
     * Document contents.
     *
     * @return void
     */
    public function test_check_access(): void {
        global $DB;

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->bookchapterareaid);
        $this->assertInstanceOf('\mod_book\search\chapter', $searcharea);

        $user1 = self::getDataGenerator()->create_user();
        $course1 = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'student');

        $book = $this->getDataGenerator()->create_module('book', array('course' => $course1->id));
        $bookgenerator = $this->getDataGenerator()->get_plugin_generator('mod_book');

        $chapter = array('bookid' => $book->id, 'content' => 'Chapter1', 'title' => 'Title1');
        $chapter1 = $bookgenerator->create_chapter($chapter);
        $chapter['content'] = 'Chapter2';
        $chapter['title'] = 'Title2';
        $chapter['hidden'] = 1;
        $chapter2 = $bookgenerator->create_chapter($chapter);

        $this->setAdminUser();
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($chapter1->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($chapter2->id));

        $this->setUser($user1);

        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($chapter1->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($chapter2->id));

        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $searcharea->check_access($chapter2->id + 10));
    }
}
