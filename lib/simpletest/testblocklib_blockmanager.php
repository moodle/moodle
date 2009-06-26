<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Tests for the block_manager class in ../blocklib.php.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/pagelib.php');
require_once($CFG->libdir . '/blocklib.php');

/** Test-specific subclass to make some protected things public. */
class testable_block_manager extends block_manager {

    public function mark_loaded() {
        $this->birecordsbyregion = array();
    }
    public function get_loaded_blocks() {
        return $this->birecordsbyregion;
    }
    public function matching_page_type_patterns($pagetype) {
        return parent::matching_page_type_patterns($pagetype);
    }
}
class block_ablocktype extends block_base {
    public function init() {
    }
}

/**
 * Test functions that don't need to touch the database.
 */
class moodle_block_manager_test extends UnitTestCase {
    public  static $includecoverage = array('lib/pagelib.php', 'lib/blocklib.php');
    protected $testpage;
    protected $blockmanager;

    public function setUp() {
        $this->testpage = new moodle_page();
        $this->blockmanager = new testable_block_manager($this->testpage);
    }

    public function tearDown() {
        $this->testpage = NULL;
        $this->blockmanager = NULL;
    }

    public function test_no_regions_initially() {
        // Exercise SUT & Validate
        $this->assertEqual(array(), $this->blockmanager->get_regions());
    }

    public function test_add_region() {
        // Exercise SUT.
        $this->blockmanager->add_region('a-region-name');
        // Validate
        $this->assertEqual(array('a-region-name'), $this->blockmanager->get_regions());
    }

    public function test_add_regions() {
        // Set up fixture.
        $regions = array('a-region', 'another-region');
        // Exercise SUT.
        $this->blockmanager->add_regions($regions);
        // Validate
        $this->assert(new ArraysHaveSameValuesExpectation($regions), $this->blockmanager->get_regions());
    }

    public function test_add_region_twice() {
        // Exercise SUT.
        $this->blockmanager->add_region('a-region-name');
        $this->blockmanager->add_region('another-region');
        // Validate
        $this->assert(new ArraysHaveSameValuesExpectation(array('a-region-name', 'another-region')),
                $this->blockmanager->get_regions());
    }

    public function test_cannot_add_region_after_loaded() {
        // Set up fixture.
        $this->blockmanager->mark_loaded();
        // Set expectation
        $this->expectException();
        // Exercise SUT.
        $this->blockmanager->add_region('too-late');
    }

    public function test_set_default_region() {
        // Set up fixture.
        $this->blockmanager->add_region('a-region-name');
        // Exercise SUT.
        $this->blockmanager->set_default_region('a-region-name');
        // Validate
        $this->assertEqual('a-region-name', $this->blockmanager->get_default_region());
    }

    public function test_cannot_set_unknown_region_as_default() {
        // Set expectation
        $this->expectException();
        // Exercise SUT.
        $this->blockmanager->set_default_region('a-region-name');
    }

    public function test_cannot_change_default_region_after_loaded() {
        // Set up fixture.
        $this->blockmanager->mark_loaded();
        // Set expectation
        $this->expectException();
        // Exercise SUT.
        $this->blockmanager->set_default_region('too-late');
    }

    public function test_matching_page_type_patterns() {
        $this->assert(new ArraysHaveSameValuesExpectation(
                array('site-index', 'site-index-*', 'site-*', '*')),
                $this->blockmanager->matching_page_type_patterns('site-index'));

        $this->assert(new ArraysHaveSameValuesExpectation(
                array('mod-quiz-report-overview', 'mod-quiz-report-overview-*', 'mod-quiz-report-*', 'mod-quiz-*', 'mod-*', '*')),
                $this->blockmanager->matching_page_type_patterns('mod-quiz-report-overview'));

        $this->assert(new ArraysHaveSameValuesExpectation(
                array('mod-forum-view', 'mod-*-view', 'mod-forum-view-*', 'mod-forum-*', 'mod-*', '*')),
                $this->blockmanager->matching_page_type_patterns('mod-forum-view'));

        $this->assert(new ArraysHaveSameValuesExpectation(
                array('mod-forum-index', 'mod-*-index', 'mod-forum-index-*', 'mod-forum-*', 'mod-*', '*')),
                $this->blockmanager->matching_page_type_patterns('mod-forum-index'));
    }
}

/**
 * Test methods that load and save data from block_instances and block_positions.
 */
class moodle_block_manager_test_saving_loading extends UnitTestCaseUsingDatabase {

    public function setUp() {
        parent::setUp();
        $this->create_test_tables(array('block', 'block_instances', 'block_positions'), 'lib');
        $this->switch_to_test_db();
    }

    public function tearDown() {
        parent::tearDown();
    }

    protected function get_a_page_and_block_manager($regions, $context, $pagetype, $subpage = '') {
        $page = new moodle_page;
        $page->set_context($context);
        $page->set_pagetype($pagetype);
        $page->set_subpage($subpage);

        $blockmanager = new testable_block_manager($page);
        $blockmanager->add_regions($regions);
        $blockmanager->set_default_region($regions[0]);

        return array($page, $blockmanager);
    }

    protected function get_a_known_block_type() {
        global $DB;
        $block = new stdClass;
        $block->name = 'ablocktype';
        $this->testdb->insert_record('block', $block);
        return $block->name;
    }

    protected function assertContainsBlocksOfType($typearray, $blockarray) {
        if (!$this->assertEqual(count($typearray), count($blockarray), "Blocks array contains the wrong number of elements %s.")) {
            return;
        }
        $types = array_values($typearray);
        $i = 0;
        foreach ($blockarray as $block) {
            $blocktype = $types[$i];
            $this->assertEqual($blocktype, $block->name(), "Block types do not match at postition $i %s.");
            $i++;
        }
    }

    public function test_empty_initially() {
        // Set up fixture.
        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array('a-region'),
                get_context_instance(CONTEXT_SYSTEM), 'page-type');
        // Exercise SUT.
        $blockmanager->load_blocks();
        // Validate.
        $blocks = $blockmanager->get_loaded_blocks();
        $this->assertEqual(array('a-region' => array()), $blocks);
    }

    public function test_adding_and_retrieving_one_block() {
        // Set up fixture.
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();

        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array($regionname),
                get_context_instance(CONTEXT_SYSTEM), 'page-type');

        // Exercise SUT.
        $blockmanager->add_block($blockname, $regionname, 0, false);
        $blockmanager->load_blocks();
        // Validate.
        $blocks = $blockmanager->get_blocks_for_region($regionname);
        $this->assertContainsBlocksOfType(array($blockname), $blocks);
    }

    public function test_adding_and_retrieving_two_blocks() {
        // Set up fixture.
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();

        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array($regionname),
                get_context_instance(CONTEXT_SYSTEM), 'page-type');

        // Exercise SUT.
        $blockmanager->add_block($blockname, $regionname, 0, false);
        $blockmanager->add_block($blockname, $regionname, 1, false);
        $blockmanager->load_blocks();
        // Validate.
        $blocks = $blockmanager->get_blocks_for_region($regionname);
        $this->assertContainsBlocksOfType(array($blockname, $blockname), $blocks);
    }

    public function test_block_not_included_in_different_context() {
        // Set up fixture.
        $syscontext = get_context_instance(CONTEXT_SYSTEM);
        $fakecontext = new stdClass;
        $fakecontext->id = $syscontext->id + 1;
        $fakecontext->contextlevel = CONTEXT_COURSECAT;
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
        // Set up fixture.
        $syscontext = get_context_instance(CONTEXT_SYSTEM);
        $childcontext = new stdClass;
        $childcontext->id = $syscontext->id + 1;
        $childcontext->path = '/' . $syscontext->id . '/' . $childcontext->id;
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
        // Set up fixture.
        $syscontext = get_context_instance(CONTEXT_SYSTEM);
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
        // Set up fixture.
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();

        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array($regionname),
                get_context_instance(CONTEXT_SYSTEM), 'page-type', 'sub-page');

        $blockmanager->add_block($blockname, $regionname, 0, true, $page->pagetype, 'other-sub-page');

        // Exercise SUT.
        $blockmanager->load_blocks();
        // Validate.
        $blocks = $blockmanager->get_blocks_for_region($regionname);
        $this->assertContainsBlocksOfType(array(), $blocks);
    }

    public function test_block_included_with_explicit_sub_page() {
        // Set up fixture.
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();

        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array($regionname),
                get_context_instance(CONTEXT_SYSTEM), 'page-type', 'sub-page');

        $blockmanager->add_block($blockname, $regionname, 0, true, $page->pagetype, $page->subpage);

        // Exercise SUT.
        $blockmanager->load_blocks();
        // Validate.
        $blocks = $blockmanager->get_blocks_for_region($regionname);
        $this->assertContainsBlocksOfType(array($blockname), $blocks);
    }

    public function test_block_included_with_page_type_pattern() {
        // Set up fixture.
        $regionname = 'a-region';
        $blockname = $this->get_a_known_block_type();

        list($page, $blockmanager) = $this->get_a_page_and_block_manager(array($regionname),
                get_context_instance(CONTEXT_SYSTEM), 'page-type', 'sub-page');

        $blockmanager->add_block($blockname, $regionname, 0, true, 'page-*', $page->subpage);

        // Exercise SUT.
        $blockmanager->load_blocks();
        // Validate.
        $blocks = $blockmanager->get_blocks_for_region($regionname);
        $this->assertContainsBlocksOfType(array($blockname), $blocks);
    }
}
?>
