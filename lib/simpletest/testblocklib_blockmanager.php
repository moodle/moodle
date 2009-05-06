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
        parent::mark_loaded();
    }
}

/**
 * Test functions that don't need to touch the database.
 */
class moodle_page_test extends UnitTestCase {
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

}

?>
