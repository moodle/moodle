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
 * Tests for the block_manager class in ../blocklib.php.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/pagelib.php');
require_once($CFG->libdir . '/blocklib.php');
require_once($CFG->dirroot . '/blocks/moodleblock.class.php');


/**
 * Test various block related classes.
 */
class core_blocklib_testcase extends advanced_testcase {
    protected $testpage;
    protected $blockmanager;
    protected $isediting = null;

    protected function setUp() {
        parent::setUp();
        $this->testpage = new moodle_page();
        $this->testpage->set_context(context_system::instance());
        $this->testpage->set_pagetype('phpunit-block-test');
        $this->blockmanager = new testable_block_manager($this->testpage);
    }

    protected function tearDown() {
        $this->testpage = null;
        $this->blockmanager = null;
        parent::tearDown();
    }

    protected function purge_blocks() {
        global $DB;
        $this->resetAfterTest();

        $bis = $DB->get_records('block_instances');
        foreach ($bis as $instance) {
            blocks_delete_instance($instance);
        }
    }

    public function test_no_regions_initially() {
        // Exercise SUT & Validate.
        $this->assertEquals(array(), $this->blockmanager->get_regions());
    }

    public function test_add_region() {
        // Exercise SUT.
        $this->blockmanager->add_region('a-region-name', false);
        // Validate.
        $this->assertEquals(array('a-region-name'), $this->blockmanager->get_regions());
    }

    public function test_add_regions() {
        // Set up fixture.
        $regions = array('a-region', 'another-region');
        // Exercise SUT.
        $this->blockmanager->add_regions($regions, false);
        // Validate.
        $this->assertEquals($regions, $this->blockmanager->get_regions(), '', 0, 10, true);
    }

    public function test_add_region_twice() {
        // Exercise SUT.
        $this->blockmanager->add_region('a-region-name', false);
        $this->blockmanager->add_region('another-region', false);
        // Validate.
        $this->assertEquals(array('a-region-name', 'another-region'), $this->blockmanager->get_regions(), '', 0, 10, true);
    }

    /**
     * @expectedException coding_exception
     */
    public function test_cannot_add_region_after_loaded() {
        // Set up fixture.
        $this->blockmanager->mark_loaded();
        // Exercise SUT.
        $this->blockmanager->add_region('too-late', false);
    }

    /**
     * Testing adding a custom region.
     */
    public function test_add_custom_region() {
        global $SESSION;
        // Exercise SUT.
        $this->blockmanager->add_region('a-custom-region-name');
        // Validate.
        $this->assertEquals(array('a-custom-region-name'), $this->blockmanager->get_regions());
        $this->assertTrue(isset($SESSION->custom_block_regions));
        $this->assertArrayHasKey('phpunit-block-test', $SESSION->custom_block_regions);
        $this->assertTrue(in_array('a-custom-region-name', $SESSION->custom_block_regions['phpunit-block-test']));

    }

    /**
     * Test adding two custom regions using add_regions method.
     */
    public function test_add_custom_regions() {
        global $SESSION;
        // Set up fixture.
        $regions = array('a-region', 'another-custom-region');
        // Exercise SUT.
        $this->blockmanager->add_regions($regions);
        // Validate.
        $this->assertEquals($regions, $this->blockmanager->get_regions(), '', 0, 10, true);
        $this->assertTrue(isset($SESSION->custom_block_regions));
        $this->assertArrayHasKey('phpunit-block-test', $SESSION->custom_block_regions);
        $this->assertTrue(in_array('another-custom-region', $SESSION->custom_block_regions['phpunit-block-test']));
    }

    /**
     * Test adding two custom block regions.
     */
    public function test_add_custom_region_twice() {
        // Exercise SUT.
        $this->blockmanager->add_region('a-custom-region-name');
        $this->blockmanager->add_region('another-custom-region');
        // Validate.
        $this->assertEquals(
            array('a-custom-region-name', 'another-custom-region'),
            $this->blockmanager->get_regions(),
            '', 0, 10, true
        );
    }

    /**
     * Test to ensure that we cannot add a region after the blocks have been loaded.
     * @expectedException coding_exception
     */
    public function test_cannot_add_custom_region_after_loaded() {
        // Set up fixture.
        $this->blockmanager->mark_loaded();
        // Exercise SUT.
        $this->blockmanager->add_region('too-late');
    }

    public function test_set_default_region() {
        // Set up fixture.
        $this->blockmanager->add_region('a-region-name', false);
        // Exercise SUT.
        $this->blockmanager->set_default_region('a-region-name');
        // Validate.
        $this->assertEquals('a-region-name', $this->blockmanager->get_default_region());
    }

    /**
     * @expectedException coding_exception
     */
    public function test_cannot_set_unknown_region_as_default() {
        // Exercise SUT.
        $this->blockmanager->set_default_region('a-region-name');
    }

    /**
     * @expectedException coding_exception
     */
    public function test_cannot_change_default_region_after_loaded() {
        // Set up fixture.
        $this->blockmanager->mark_loaded();
        // Exercise SUT.
        $this->blockmanager->set_default_region('too-late');
    }

    public function test_matching_page_type_patterns() {
        $this->assertEquals(array('site-index', 'site-index-*', 'site-*', '*'),
            matching_page_type_patterns('site-index'), '', 0, 10, true);

        $this->assertEquals(array('mod-quiz-report-overview', 'mod-quiz-report-overview-*', 'mod-quiz-report-*', 'mod-quiz-*', 'mod-*', '*'),
            matching_page_type_patterns('mod-quiz-report-overview'), '', 0, 10, true);

        $this->assertEquals(array('mod-forum-view', 'mod-*-view', 'mod-forum-view-*', 'mod-forum-*', 'mod-*', '*'),
            matching_page_type_patterns('mod-forum-view'), '', 0, 10, true);

        $this->assertEquals(array('mod-forum-index', 'mod-*-index', 'mod-forum-index-*', 'mod-forum-*', 'mod-*', '*'),
            matching_page_type_patterns('mod-forum-index'), '', 0, 10, true);
    }

    protected function get_a_page_and_block_manager($regions, $context, $pagetype, $subpage = '') {
        $page = new moodle_page;
        $page->set_context($context);
        $page->set_pagetype($pagetype);
        $page->set_subpage($subpage);

        $blockmanager = new testable_block_manager($page);
        $blockmanager->add_regions($regions, false);
        $blockmanager->set_default_region($regions[0]);

        return array($page, $blockmanager);
    }

    protected function get_a_known_block_type() {
        global $DB;
        $block = new stdClass;
        $block->name = 'ablocktype';
        $DB->insert_record('block', $block);
        return $block->name;
    }

    protected function assertContainsBlocksOfType($typearray, $blockarray) {
        if (!$this->assertEquals(count($typearray), count($blockarray), "Blocks array contains the wrong number of elements %s.")) {
            return;
        }
        $types = array_values($typearray);
        $i = 0;
        foreach ($blockarray as $block) {
            $blocktype = $types[$i];
            $this->assertEquals($blocktype, $block->name(), "Block types do not match at postition $i %s.");
            $i++;
        }
    }

    public function test_empty_initially() {
        $this->purge_blocks();

        // Set up fixture.
        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array('a-region'),
            context_system::instance(), 'page-type');
        // Exercise SUT.
        $blockmanager->load_blocks();
        // Validate.
        $blocks = $blockmanager->get_loaded_blocks();
        $this->assertEquals(array('a-region' => array()), $blocks);
    }

    public function test_adding_and_retrieving_one_block() {
        $this->purge_blocks();

        // Set up fixture.
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();
        $context = context_system::instance();

        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array($regionname),
            $context, 'page-type');

        // Exercise SUT.
        $blockmanager->add_block($blockname, $regionname, 0, false);
        $blockmanager->load_blocks();
        // Validate.
        $blocks = $blockmanager->get_blocks_for_region($regionname);
        $this->assertContainsBlocksOfType(array($blockname), $blocks);
    }

    public function test_adding_and_retrieving_two_blocks() {
        $this->purge_blocks();

        // Set up fixture.
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();
        $context = context_system::instance();

        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array($regionname),
            $context, 'page-type');

        // Exercise SUT.
        $blockmanager->add_block($blockname, $regionname, 0, false);
        $blockmanager->add_block($blockname, $regionname, 1, false);
        $blockmanager->load_blocks();
        // Validate.
        $blocks = $blockmanager->get_blocks_for_region($regionname);
        $this->assertContainsBlocksOfType(array($blockname, $blockname), $blocks);
    }

    public function test_adding_blocks() {
        $this->purge_blocks();

        // Set up fixture.
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();
        $context = context_system::instance();

        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array($regionname),
            $context, 'page-type');

        $blockmanager->add_blocks(array($regionname => array($blockname, $blockname)), null, null, false, 3);
        $blockmanager->load_blocks();

        $blocks = $blockmanager->get_blocks_for_region($regionname);

        $this->assertEquals('3', $blocks[0]->instance->weight);
        $this->assertEquals('4', $blocks[1]->instance->weight);
    }

    public function test_block_not_included_in_different_context() {
        $this->purge_blocks();

        // Set up fixture.
        $syscontext = context_system::instance();
        $cat = $this->getDataGenerator()->create_category(array('name' => 'testcategory'));
        $fakecontext = context_coursecat::instance($cat->id);
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();

        list($addpage, $addbm) = $this->get_a_page_and_block_manager(array($regionname), $fakecontext, 'page-type');
        list($viewpage, $viewbm) = $this->get_a_page_and_block_manager(array($regionname), $syscontext, 'page-type');

        $addbm->add_block($blockname, $regionname, 0, false);

        // Exercise SUT.
        $viewbm->load_blocks();
        // Validate.
        $blocks = $viewbm->get_blocks_for_region($regionname);
        $this->assertContainsBlocksOfType(array(), $blocks);
    }

    public function test_block_included_in_sub_context() {
        $this->purge_blocks();

        // Set up fixture.
        $syscontext = context_system::instance();
        $childcontext = context_coursecat::instance(1);
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();

        list($addpage, $addbm) = $this->get_a_page_and_block_manager(array($regionname), $syscontext, 'page-type');
        list($viewpage, $viewbm) = $this->get_a_page_and_block_manager(array($regionname), $childcontext, 'page-type');

        $addbm->add_block($blockname, $regionname, 0, true);

        // Exercise SUT.
        $viewbm->load_blocks();
        // Validate.
        $blocks = $viewbm->get_blocks_for_region($regionname);
        $this->assertContainsBlocksOfType(array($blockname), $blocks);
    }

    public function test_block_not_included_on_different_page_type() {
        $this->purge_blocks();

        // Set up fixture.
        $syscontext = context_system::instance();
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();

        list($addpage, $addbm) = $this->get_a_page_and_block_manager(array($regionname), $syscontext, 'page-type');
        list($viewpage, $viewbm) = $this->get_a_page_and_block_manager(array($regionname), $syscontext, 'other-page-type');

        $addbm->add_block($blockname, $regionname, 0, true);

        // Exercise SUT.
        $viewbm->load_blocks();
        // Validate.
        $blocks = $viewbm->get_blocks_for_region($regionname);
        $this->assertContainsBlocksOfType(array(), $blocks);
    }

    public function test_block_not_included_on_different_sub_page() {
        $this->purge_blocks();

        // Set up fixture.
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();
        $syscontext = context_system::instance();

        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array($regionname),
            $syscontext, 'page-type', 'sub-page');

        $blockmanager->add_block($blockname, $regionname, 0, true, $page->pagetype, 'other-sub-page');

        // Exercise SUT.
        $blockmanager->load_blocks();
        // Validate.
        $blocks = $blockmanager->get_blocks_for_region($regionname);
        $this->assertContainsBlocksOfType(array(), $blocks);
    }

    public function test_block_included_with_explicit_sub_page() {
        $this->purge_blocks();

        // Set up fixture.
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();
        $syscontext = context_system::instance();

        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array($regionname),
            $syscontext, 'page-type', 'sub-page');

        $blockmanager->add_block($blockname, $regionname, 0, true, $page->pagetype, $page->subpage);

        // Exercise SUT.
        $blockmanager->load_blocks();
        // Validate.
        $blocks = $blockmanager->get_blocks_for_region($regionname);
        $this->assertContainsBlocksOfType(array($blockname), $blocks);
    }

    public function test_block_included_with_page_type_pattern() {
        $this->purge_blocks();

        // Set up fixture.
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();
        $syscontext = context_system::instance();

        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array($regionname),
            $syscontext, 'page-type', 'sub-page');

        $blockmanager->add_block($blockname, $regionname, 0, true, 'page-*', $page->subpage);

        // Exercise SUT.
        $blockmanager->load_blocks();
        // Validate.
        $blocks = $blockmanager->get_blocks_for_region($regionname);
        $this->assertContainsBlocksOfType(array($blockname), $blocks);
    }

    public function test_matching_page_type_patterns_from_pattern() {
        $pattern = '*';
        $expected = array('*');
        $this->assertEquals($expected, array_values(matching_page_type_patterns_from_pattern($pattern)));

        $pattern = 'admin-*';
        $expected = array('admin-*', 'admin', '*');
        $this->assertEquals($expected, array_values(matching_page_type_patterns_from_pattern($pattern)));

        $pattern = 'blog-index';
        $expected = array('blog-index', 'blog-index-*', 'blog-*', '*');
        $this->assertEquals($expected, array_values(matching_page_type_patterns_from_pattern($pattern)));

        $pattern = 'course-index-*';
        $expected = array('course-index-*', 'course-index', 'course-*', '*');
        $this->assertEquals($expected, array_values(matching_page_type_patterns_from_pattern($pattern)));

        $pattern = 'course-index-category';
        $expected = array('course-index-category', 'course-index-category-*', 'course-index-*', 'course-*', '*');
        $this->assertEquals($expected, array_values(matching_page_type_patterns_from_pattern($pattern)));

        $pattern = 'mod-assign-view';
        $expected = array('mod-assign-view', 'mod-*-view', 'mod-assign-view-*', 'mod-assign-*', 'mod-*', '*');
        $this->assertEquals($expected, array_values(matching_page_type_patterns_from_pattern($pattern)));

        $pattern = 'mod-assign-index';
        $expected = array('mod-assign-index', 'mod-*-index', 'mod-assign-index-*', 'mod-assign-*', 'mod-*', '*');
        $this->assertEquals($expected, array_values(matching_page_type_patterns_from_pattern($pattern)));

        $pattern = 'mod-forum-*';
        $expected = array('mod-forum-*', 'mod-forum', 'mod-*', '*');
        $this->assertEquals($expected, array_values(matching_page_type_patterns_from_pattern($pattern)));

        $pattern = 'mod-*-view';
        $expected = array('mod-*-view', 'mod', 'mod-*', '*');
        $this->assertEquals($expected, array_values(matching_page_type_patterns_from_pattern($pattern)));

        $pattern = 'mod-*-index';
        $expected = array('mod-*-index', 'mod', 'mod-*', '*');
        $this->assertEquals($expected, array_values(matching_page_type_patterns_from_pattern($pattern)));

        $pattern = 'my-index';
        $expected = array('my-index', 'my-index-*', 'my-*', '*');
        $this->assertEquals($expected, array_values(matching_page_type_patterns_from_pattern($pattern)));

        $pattern = 'user-profile';
        $expected = array('user-profile', 'user-profile-*', 'user-*', '*');
        $this->assertEquals($expected, array_values(matching_page_type_patterns_from_pattern($pattern)));
    }

    public function test_delete_instances() {
        global $DB;
        $this->purge_blocks();
        $this->setAdminUser();

        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();
        $context = context_system::instance();

        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array($regionname),
            $context, 'page-type');

        $blockmanager->add_blocks(array($regionname => array($blockname, $blockname, $blockname)), null, null, false, 3);
        $blockmanager->load_blocks();

        $blocks = $blockmanager->get_blocks_for_region($regionname);
        $blockids = array();
        $preferences = array();

        // Create block related data.
        foreach ($blocks as $block) {
            $instance = $block->instance;
            $pref = 'block' . $instance->id . 'hidden';
            set_user_preference($pref, '123', 123);
            $preferences[] = $pref;
            $pref = 'docked_block_instance_' . $instance->id;
            set_user_preference($pref, '123', 123);
            $preferences[] = $pref;
            blocks_set_visibility($instance, $page, 1);
            $blockids[] = $instance->id;
        }

        // Confirm what has been set.
        $this->assertCount(3, $blockids);
        list($insql, $inparams) = $DB->get_in_or_equal($blockids);
        $this->assertEquals(3, $DB->count_records_select('block_positions', "blockinstanceid $insql", $inparams));
        list($insql, $inparams) = $DB->get_in_or_equal($preferences);
        $this->assertEquals(6, $DB->count_records_select('user_preferences', "name $insql", $inparams));

        // Keep a block on the side.
        $allblockids = $blockids;
        $tokeep = array_pop($blockids);

        // Delete and confirm what should have happened.
        blocks_delete_instances($blockids);

        // Reload the manager.
        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array($regionname),
            $context, 'page-type');
        $blockmanager->load_blocks();
        $blocks = $blockmanager->get_blocks_for_region($regionname);

        $this->assertCount(1, $blocks);
        list($insql, $inparams) = $DB->get_in_or_equal($allblockids);
        $this->assertEquals(1, $DB->count_records_select('block_positions', "blockinstanceid $insql", $inparams));
        list($insql, $inparams) = $DB->get_in_or_equal($preferences);
        $this->assertEquals(2, $DB->count_records_select('user_preferences', "name $insql", $inparams));

        $this->assertFalse(context_block::instance($blockids[0], IGNORE_MISSING));
        $this->assertFalse(context_block::instance($blockids[1], IGNORE_MISSING));
        context_block::instance($tokeep);   // Would throw an exception if it was deleted.
    }

}

/**
 * Test-specific subclass to make some protected things public.
 */
class testable_block_manager extends block_manager {

    public function mark_loaded() {
        $this->birecordsbyregion = array();
    }
    public function get_loaded_blocks() {
        return $this->birecordsbyregion;
    }
}

/**
 * Test-specific subclass to make some protected things public.
 */
class block_ablocktype extends block_base {
    public function init() {
    }
}
