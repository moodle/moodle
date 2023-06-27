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
 * Unit test for search indexing.
 *
 * @package block_html
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_html;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit test for search indexing.
 *
 * @package block_html
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_content_testcase extends \advanced_testcase {

    /**
     * Creates an HTML block on a course.
     *
     * @param \stdClass $course Course object
     * @return \block_html Block instance object
     */
    protected function create_block($course) {
        $page = self::construct_page($course);
        $page->blocks->add_block_at_end_of_default_region('html');

        // Load the block.
        $page = self::construct_page($course);
        $page->blocks->load_blocks();
        $blocks = $page->blocks->get_blocks_for_region($page->blocks->get_default_region());
        $block = end($blocks);
        return $block;
    }

    /**
     * Constructs a page object for the test course.
     *
     * @param \stdClass $course Moodle course object
     * @return \moodle_page Page object representing course view
     */
    protected static function construct_page($course) {
        $context = \context_course::instance($course->id);
        $page = new \moodle_page();
        $page->set_context($context);
        $page->set_course($course);
        $page->set_pagelayout('standard');
        $page->set_pagetype('course-view');
        $page->blocks->load_blocks();
        return $page;
    }

    /**
     * Tests all functionality in the search area.
     */
    public function test_search_area() {
        global $CFG, $USER, $DB;
        require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create course and add HTML block.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $before = time();
        $block = $this->create_block($course);

        // Change block settings to add some text and a file.
        $itemid = file_get_unused_draft_itemid();
        $fs = get_file_storage();
        $usercontext = \context_user::instance($USER->id);
        $fs->create_file_from_string(['component' => 'user', 'filearea' => 'draft',
                'contextid' => $usercontext->id, 'itemid' => $itemid, 'filepath' => '/',
                'filename' => 'file.txt'], 'File content');
        $data = (object)['title' => 'Block title', 'text' => ['text' => 'Block text',
                'itemid' => $itemid, 'format' => FORMAT_HTML]];
        $block->instance_config_save($data);
        $after = time();

        // Set up fake search engine so we can create documents.
        \testable_core_search::instance();

        // Do indexing query.
        $area = new \block_html\search\content();
        $this->assertEquals('html', $area->get_block_name());
        $rs = $area->get_recordset_by_timestamp();
        $count = 0;
        foreach ($rs as $record) {
            $count++;

            $this->assertEquals($course->id, $record->courseid);

            // Check context is correct.
            $blockcontext = \context::instance_by_id($record->contextid);
            $this->assertInstanceOf('\context_block', $blockcontext);
            $coursecontext = $blockcontext->get_parent_context();
            $this->assertEquals($course->id, $coursecontext->instanceid);

            // Check created and modified times are correct.
            $this->assertTrue($record->timecreated >= $before && $record->timecreated <= $after);
            $this->assertTrue($record->timemodified >= $before && $record->timemodified <= $after);

            // Get config data.
            $data = unserialize(base64_decode($record->configdata));
            $this->assertEquals('Block title', $data->title);
            $this->assertEquals('Block text', $data->text);
            $this->assertEquals(FORMAT_HTML, $data->format);

            // Check the get_document function 'new' flag.
            $doc = $area->get_document($record, ['lastindexedtime' => 1]);
            $this->assertTrue($doc->get_is_new());
            $doc = $area->get_document($record, ['lastindexedtime' => time() + 1]);
            $this->assertFalse($doc->get_is_new());

            // Check the attach_files function results in correct list of associated files.
            $this->assertCount(0, $doc->get_files());
            $area->attach_files($doc);
            $files = $doc->get_files();
            $this->assertCount(2, $files);
            foreach ($files as $file) {
                if ($file->is_directory()) {
                    continue;
                }
                $this->assertEquals('file.txt', $file->get_filename());
                $this->assertEquals('File content', $file->get_content());
            }

            // Check the document fields are all as expected.
            $this->assertEquals('Block title', $doc->get('title'));
            $this->assertEquals('Block text', $doc->get('content'));
            $this->assertEquals($blockcontext->id, $doc->get('contextid'));
            $this->assertEquals(\core_search\manager::TYPE_TEXT, $doc->get('type'));
            $this->assertEquals($course->id, $doc->get('courseid'));
            $this->assertEquals($record->timemodified, $doc->get('modified'));
            $this->assertEquals(\core_search\manager::NO_OWNER_ID, $doc->get('owneruserid'));

            // Also check getting the doc url and context url.
            $url = new \moodle_url('/course/view.php', ['id' => $course->id], 'inst' . $record->id);
            $this->assertTrue($url->compare($area->get_doc_url($doc)));
            $this->assertTrue($url->compare($area->get_context_url($doc)));
        }
        $rs->close();

        // Should only be one HTML block systemwide.
        $this->assertEquals(1, $count);

        // If we run the query starting from 1 second after now, there should be no results.
        $rs = $area->get_recordset_by_timestamp($after + 1);
        $count = 0;
        foreach ($rs as $record) {
            $count++;
        }
        $rs->close();
        $this->assertEquals(0, $count);

        // Create another block, but this time leave it empty (no data set). Hack the time though.
        $block = $this->create_block($course);
        $DB->set_field('block_instances', 'timemodified',
                $after + 10, ['id' => $block->instance->id]);
        $rs = $area->get_recordset_by_timestamp($after + 10);
        $count = 0;
        foreach ($rs as $record) {
            // Because there is no configdata we don't index it.
            $count++;
        }
        $rs->close();
        $this->assertEquals(0, $count);
    }
}

