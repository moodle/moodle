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
 * Unit tests for the base_block class.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/testable_core_search.php');
require_once(__DIR__ . '/fixtures/mock_block_area.php');

/**
 * Unit tests for the base_block class.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class base_block_testcase extends advanced_testcase {
    /**
     * Tests getting the name out of the class name.
     */
    public function test_get_block_name() {
        $area = new \block_mockblock\search\area();
        $this->assertEquals('mockblock', $area->get_block_name());
    }

    /**
     * Tests getting the recordset.
     */
    public function test_get_document_recordset() {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create course and activity module.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $coursecontext = \context_course::instance($course->id);
        $page = $generator->create_module('page', ['course' => $course->id]);
        $pagecontext = \context_module::instance($page->cmid);

        // Create another 2 courses (in same category and in a new category).
        $cat1context = \context_coursecat::instance($course->category);
        $course2 = $generator->create_course();
        $course2context = \context_course::instance($course2->id);
        $cat2 = $generator->create_category();
        $cat2context = \context_coursecat::instance($cat2->id);
        $course3 = $generator->create_course(['category' => $cat2->id]);
        $course3context = \context_course::instance($course3->id);

        // Add blocks by hacking table (because it's not a real block type).

        // 1. Block on course page.
        $configdata = base64_encode(serialize((object) ['example' => 'content']));
        $instance = (object)['blockname' => 'mockblock', 'parentcontextid' => $coursecontext->id,
                'showinsubcontexts' => 0, 'pagetypepattern' => 'course-view-*',
                'defaultweight' => 0, 'timecreated' => 1, 'timemodified' => 1,
                'configdata' => $configdata];
        $block1id = $DB->insert_record('block_instances', $instance);
        $block1context = \context_block::instance($block1id);

        // 2. Block on activity page.
        $instance->parentcontextid = $pagecontext->id;
        $instance->pagetypepattern = 'mod-page-view';
        $instance->timemodified = 2;
        $block2id = $DB->insert_record('block_instances', $instance);
        \context_block::instance($block2id);

        // 3. Block on site context.
        $sitecourse = get_site();
        $sitecontext = \context_course::instance($sitecourse->id);
        $instance->parentcontextid = $sitecontext->id;
        $instance->pagetypepattern = 'site-index';
        $instance->timemodified = 3;
        $block3id = $DB->insert_record('block_instances', $instance);
        $block3context = \context_block::instance($block3id);

        // 4. Block on course page but no data.
        $instance->parentcontextid = $coursecontext->id;
        $instance->pagetypepattern = 'course-view-*';
        unset($instance->configdata);
        $instance->timemodified = 4;
        $block4id = $DB->insert_record('block_instances', $instance);
        \context_block::instance($block4id);

        // 5. Block on course page but not this block.
        $instance->blockname = 'mockotherblock';
        $instance->configdata = $configdata;
        $instance->timemodified = 5;
        $block5id = $DB->insert_record('block_instances', $instance);
        \context_block::instance($block5id);

        // 6. Block on course page with '*' page type.
        $instance->blockname = 'mockblock';
        $instance->pagetypepattern = '*';
        $instance->timemodified = 6;
        $block6id = $DB->insert_record('block_instances', $instance);
        \context_block::instance($block6id);

        // 7. Block on course page with 'course-*' page type.
        $instance->pagetypepattern = 'course-*';
        $instance->timemodified = 7;
        $block7id = $DB->insert_record('block_instances', $instance);
        \context_block::instance($block7id);

        // 8. Block on course 2.
        $instance->parentcontextid = $course2context->id;
        $instance->timemodified = 8;
        $block8id = $DB->insert_record('block_instances', $instance);
        \context_block::instance($block8id);

        // 9. Block on course 3.
        $instance->parentcontextid = $course3context->id;
        $instance->timemodified = 9;
        $block9id = $DB->insert_record('block_instances', $instance);
        \context_block::instance($block9id);

        // Get all the blocks.
        $area = new block_mockblock\search\area();
        $results = self::recordset_to_indexed_array($area->get_document_recordset());

        // Only blocks 1, 3, 6, 7, 8, 9 should be returned. Check all the fields for the first two.
        $this->assertCount(6, $results);

        $this->assertEquals($block1id, $results[0]->id);
        $this->assertEquals(1, $results[0]->timemodified);
        $this->assertEquals(1, $results[0]->timecreated);
        $this->assertEquals($configdata, $results[0]->configdata);
        $this->assertEquals($course->id, $results[0]->courseid);
        $this->assertEquals($block1context->id, $results[0]->contextid);

        $this->assertEquals($block3id, $results[1]->id);
        $this->assertEquals(3, $results[1]->timemodified);
        $this->assertEquals(1, $results[1]->timecreated);
        $this->assertEquals($configdata, $results[1]->configdata);
        $this->assertEquals($sitecourse->id, $results[1]->courseid);
        $this->assertEquals($block3context->id, $results[1]->contextid);

        // For the later ones, just check it got the right ones!
        $this->assertEquals($block6id, $results[2]->id);
        $this->assertEquals($block7id, $results[3]->id);
        $this->assertEquals($block8id, $results[4]->id);
        $this->assertEquals($block9id, $results[5]->id);

        // Repeat with a time restriction.
        $results = self::recordset_to_indexed_array($area->get_document_recordset(2));

        // Only block 3, 6, 7, 8, and 9 are returned.
        $this->assertEquals([$block3id, $block6id, $block7id, $block8id, $block9id],
                self::records_to_ids($results));

        // Now use context restrictions. First, the whole site (no change).
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, context_system::instance()));
        $this->assertEquals([$block1id, $block3id, $block6id, $block7id, $block8id, $block9id],
                self::records_to_ids($results));

        // Course page only (leave out the one on site page and other courses).
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, $coursecontext));
        $this->assertEquals([$block1id, $block6id, $block7id],
                self::records_to_ids($results));

        // Other course page only.
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, $course2context));
        $this->assertEquals([$block8id], self::records_to_ids($results));

        // Activity module only (no results).
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, $pagecontext));
        $this->assertCount(0, $results);

        // Specific block context.
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, $block3context));
        $this->assertEquals([$block3id], self::records_to_ids($results));

        // User context (no results).
        $usercontext = context_user::instance($USER->id);
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, $usercontext));
        $this->assertCount(0, $results);

        // Category 1 context (courses 1 and 2).
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, $cat1context));
        $this->assertEquals([$block1id, $block6id, $block7id, $block8id],
                self::records_to_ids($results));

        // Category 2 context (course 3).
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, $cat2context));
        $this->assertEquals([$block9id], self::records_to_ids($results));

        // Combine context restriction (category 1) with timemodified.
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                7, $cat1context));
        $this->assertEquals([$block7id, $block8id], self::records_to_ids($results));
    }

    /**
     * Utility function to convert recordset to array for testing.
     *
     * @param moodle_recordset $rs Recordset to convert
     * @return array Array indexed by number (0, 1, 2, ...)
     */
    protected static function recordset_to_indexed_array(moodle_recordset $rs) {
        $results = [];
        foreach ($rs as $rec) {
            $results[] = $rec;
        }
        $rs->close();
        return $results;
    }

    /**
     * Utility function to convert records to array of IDs.
     *
     * @param array $recs Records which should have an 'id' field
     * @return array Array of ids
     */
    protected static function records_to_ids(array $recs) {
        $ids = [];
        foreach ($recs as $rec) {
            $ids[] = $rec->id;
        }
        return $ids;
    }

    /**
     * Tests the get_doc_url function.
     */
    public function test_get_doc_url() {
        global $DB;

        $this->resetAfterTest();

        // Create course and activity module.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $coursecontext = \context_course::instance($course->id);
        $page = $generator->create_module('page', ['course' => $course->id]);
        $pagecontext = \context_module::instance($page->cmid);

        // Create block on course page.
        $configdata = base64_encode(serialize(new \stdClass()));
        $instance = (object)['blockname' => 'mockblock', 'parentcontextid' => $coursecontext->id,
                'showinsubcontexts' => 0, 'pagetypepattern' => 'course-view-*', 'defaultweight' => 0,
                'timecreated' => 1, 'timemodified' => 1, 'configdata' => $configdata];
        $blockid = $DB->insert_record('block_instances', $instance);

        // Get document URL.
        $area = new block_mockblock\search\area();
        $doc = $this->get_doc($course->id, $blockid);
        $expected = new moodle_url('/course/view.php', ['id' => $course->id], 'inst' . $blockid);
        $this->assertEquals($expected, $area->get_doc_url($doc));
        $this->assertEquals($expected, $area->get_context_url($doc));

        // Repeat with block on site page.
        $sitecourse = get_site();
        $sitecontext = \context_course::instance($sitecourse->id);
        $instance->pagetypepattern = 'site-index';
        $instance->parentcontextid = $sitecontext->id;
        $block2id = $DB->insert_record('block_instances', $instance);

        // Get document URL.
        $doc2 = $this->get_doc($course->id, $block2id);
        $expected = new moodle_url('/', ['redirect' => 0], 'inst' . $block2id);
        $this->assertEquals($expected, $area->get_doc_url($doc2));
        $this->assertEquals($expected, $area->get_context_url($doc2));

        // Repeat with block on module page (this cannot happen yet because the search query will
        // only include course context blocks, but let's check it works for the future).
        $instance->pagetypepattern = 'mod-page-view';
        $instance->parentcontextid = $pagecontext->id;
        $block3id = $DB->insert_record('block_instances', $instance);

        // Get and check document URL, ignoring debugging message for unsupported page type.
        $debugmessage = 'Unexpected module-level page type for block ' . $block3id .
                ': mod-page-view';
        $doc3 = $this->get_doc($course->id, $block3id);
        $this->assertDebuggingCalledCount(2, [$debugmessage, $debugmessage]);

        $expected = new moodle_url('/mod/page/view.php', ['id' => $page->cmid], 'inst' . $block3id);
        $this->assertEquals($expected, $area->get_doc_url($doc3));
        $this->assertDebuggingCalled($debugmessage);
        $this->assertEquals($expected, $area->get_context_url($doc3));
        $this->assertDebuggingCalled($debugmessage);

        // Repeat with another block on course page but '*' pages.
        $instance->pagetypepattern = '*';
        $instance->parentcontextid = $coursecontext->id;
        $block4id = $DB->insert_record('block_instances', $instance);

        // Get document URL.
        $doc = $this->get_doc($course->id, $block4id);
        $expected = new moodle_url('/course/view.php', ['id' => $course->id], 'inst' . $block4id);
        $this->assertEquals($expected, $area->get_doc_url($doc));
        $this->assertEquals($expected, $area->get_context_url($doc));

        // And same thing but 'course-*' pages.
        $instance->pagetypepattern = 'course-*';
        $block5id = $DB->insert_record('block_instances', $instance);

        // Get document URL.
        $doc = $this->get_doc($course->id, $block5id);
        $expected = new moodle_url('/course/view.php', ['id' => $course->id], 'inst' . $block5id);
        $this->assertEquals($expected, $area->get_doc_url($doc));
        $this->assertEquals($expected, $area->get_context_url($doc));
    }

    /**
     * Tests the check_access function.
     */
    public function test_check_access() {
        global $DB;

        $this->resetAfterTest();

        // Create course and activity module.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $coursecontext = \context_course::instance($course->id);
        $page = $generator->create_module('page', ['course' => $course->id]);
        $pagecontext = \context_module::instance($page->cmid);

        // Create block on course page.
        $configdata = base64_encode(serialize(new \stdClass()));
        $instance = (object)['blockname' => 'mockblock', 'parentcontextid' => $coursecontext->id,
                'showinsubcontexts' => 0, 'pagetypepattern' => 'course-view-*', 'defaultweight' => 0,
                'timecreated' => 1, 'timemodified' => 1, 'configdata' => $configdata];
        $blockid = $DB->insert_record('block_instances', $instance);

        // Check access for block that exists.
        $area = new block_mockblock\search\area();
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $area->check_access($blockid));

        // Check access for nonexistent block.
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $area->check_access($blockid + 1));

        // Check if block is not in a course context any longer.
        $DB->set_field('block_instances', 'parentcontextid', $pagecontext->id, ['id' => $blockid]);
        \core_search\base_block::clear_static();
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $area->check_access($blockid));

        // Or what if it is in a course context but has supported vs. unsupported page type.
        $DB->set_field('block_instances', 'parentcontextid', $coursecontext->id, ['id' => $blockid]);

        $DB->set_field('block_instances', 'pagetypepattern', 'course-*', ['id' => $blockid]);
        \core_search\base_block::clear_static();
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $area->check_access($blockid));

        $DB->set_field('block_instances', 'pagetypepattern', '*', ['id' => $blockid]);
        \core_search\base_block::clear_static();
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $area->check_access($blockid));

        $DB->set_field('block_instances', 'pagetypepattern', 'course-view-frogs', ['id' => $blockid]);
        \core_search\base_block::clear_static();
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $area->check_access($blockid));

        $DB->set_field('block_instances', 'pagetypepattern', 'anythingelse', ['id' => $blockid]);
        \core_search\base_block::clear_static();
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $area->check_access($blockid));
    }

    /**
     * Tests the block version of get_contexts_to_reindex, which is supposed to return all the
     * block contexts in order of date added.
     */
    public function test_get_contexts_to_reindex() {
        global $DB;

        $this->resetAfterTest();

        // Create course and activity module.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $coursecontext = \context_course::instance($course->id);
        $page = $generator->create_module('page', ['course' => $course->id]);
        $pagecontext = \context_module::instance($page->cmid);

        // Create blocks on course page, with time modified non-sequential.
        $configdata = base64_encode(serialize(new \stdClass()));
        $instance = (object)['blockname' => 'mockblock', 'parentcontextid' => $coursecontext->id,
                'showinsubcontexts' => 0, 'pagetypepattern' => 'course-view-*', 'defaultweight' => 0,
                'timecreated' => 1, 'timemodified' => 100, 'configdata' => $configdata];
        $blockid1 = $DB->insert_record('block_instances', $instance);
        $context1 = \context_block::instance($blockid1);
        $instance->timemodified = 120;
        $blockid2 = $DB->insert_record('block_instances', $instance);
        $context2 = \context_block::instance($blockid2);
        $instance->timemodified = 110;
        $blockid3 = $DB->insert_record('block_instances', $instance);
        $context3 = \context_block::instance($blockid3);

        // Create another block on the activity page (not included).
        $instance->parentcontextid = $pagecontext->id;
        $blockid4 = $DB->insert_record('block_instances', $instance);
        \context_block::instance($blockid4);

        // Check list of contexts.
        $area = new block_mockblock\search\area();
        $contexts = iterator_to_array($area->get_contexts_to_reindex(), false);
        $expected = [
            $context2,
            $context3,
            $context1
        ];
        $this->assertEquals($expected, $contexts);
    }

    /**
     * Gets a search document object from the fake search area.
     *
     * @param int $courseid Course id in document
     * @param int $blockinstanceid Block instance id in document
     * @return \core_search\document Document object
     */
    protected function get_doc($courseid, $blockinstanceid) {
        $engine = testable_core_search::instance()->get_engine();
        $area = new block_mockblock\search\area();
        $docdata = ['id' => $blockinstanceid, 'courseid' => $courseid,
                'areaid' => $area->get_area_id(), 'itemid' => 0];
        return $engine->to_document($area, $docdata);
    }
}
