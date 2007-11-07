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
 * Unit tests for grade_raw object.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/simpletest/fixtures/gradetest.php');

class grade_grade_test extends grade_test {
    
    function setUp() {
        parent::setUp();
        $this->load_grade_grades();
    }

    function tearDown() {
        parent::tearDown();
    }

    function test_grade_grade_construct() {
        $params = new stdClass();

        $params->itemid = $this->grade_items[0]->id;
        $params->userid = 1;
        $params->rawgrade = 88;
        $params->rawgrademax = 110;
        $params->rawgrademin = 18;

        $grade_grade = new grade_grade($params, false);
        $this->assertEqual($params->itemid, $grade_grade->itemid);
        $this->assertEqual($params->rawgrade, $grade_grade->rawgrade);
    }

    function test_grade_grade_insert() {
        global $db;
        $grade_grade = new grade_grade();
        $this->assertTrue(method_exists($grade_grade, 'insert'));

        $grade_grade->itemid = $this->grade_items[0]->id;
        $grade_grade->userid = 1;
        $grade_grade->rawgrade = 88;
        $grade_grade->rawgrademax = 110;
        $grade_grade->rawgrademin = 18;
        
        $grade_item = new grade_item($this->grade_items[0], false);
        $grade_grade->grade_item = $grade_item;

        // Check the grade_item's needsupdate variable first
        $this->assertFalse($grade_grade->grade_item->needsupdate);
        
        // Mock insert of data in history table
        $this->rs->setReturnValue('RecordCount', 1);
        $this->rs->fields = array(1); 
        
        // Mock insert of outcome object
        $db->setReturnValue('GetInsertSQL', true);
        $db->setReturnValue('Insert_ID', 1);

        $grade_grade->insert();

        $this->assertEqual($grade_grade->id, 1);

        // timecreated doesn't refer to creation in the DB, but to time of submission. Timemodified refers to date of grading
        $this->assertTrue(empty($grade_grade->timecreated));
        $this->assertTrue(empty($grade_grade->timemodified));
    }

    function test_grade_grade_update() {
        $grade_grade = new grade_grade($this->grade_grades[0], false);
        $this->assertTrue(method_exists($grade_grade, 'update'));
    }

    function test_grade_grade_fetch() {
        global $db;
        $grade_grade = new grade_grade($this->grade_grades[0], false);
        $this->assertTrue(method_exists($grade_grade, 'fetch'));

        // Mock fetch
        $column = new stdClass();
        $column->name = 'id';
        $this->rs->setReturnValue('FetchField', $column); // Fetching the name of the first column
        $this->rs->setReturnValue('GetAssoc', array($grade_grade->id => (array) $grade_grade)); 
        
        $grades = grade_grade::fetch(array('id'=>$grade_grade->id));

        $this->assertEqual($grade_grade->id, $grades->id);
        $this->assertEqual($grade_grade->rawgrade, $grades->rawgrade);
    }

    function test_grade_grade_fetch_all() {
        $grade_grade = new grade_grade();
        $this->assertTrue(method_exists($grade_grade, 'fetch_all'));

        // Mock fetch_all
        $return_array = array();
        foreach ($this->grade_grades as $gg) {
            $return_array[$gg->id] = (array) $gg;
        }

        $column = new stdClass();
        $column->name = 'id';
        $this->rs->setReturnValue('FetchField', $column); // Fetching the name of the first column
        $this->rs->setReturnValue('GetAssoc', $return_array); 
        
        $grades = grade_grade::fetch_all(array());
        $this->assertEqual(count($this->grade_grades), count($grades)); 
    }

    function test_grade_grade_load_grade_item() {
        $grade_grade = new grade_grade($this->grade_grades[0], false);
        $this->assertTrue(method_exists($grade_grade, 'load_grade_item'));
        $this->assertNull($grade_grade->grade_item);
        $this->assertTrue($grade_grade->itemid);
        
        // Mock fetch
        $grade_item = $this->grade_items[0];
        $column = new stdClass();
        $column->name = 'id';
        $this->rs->setReturnValue('FetchField', $column); // Fetching the name of the first column
        $this->rs->setReturnValue('GetAssoc', array($grade_item->id => (array) $grade_item)); 
        
        $this->assertNotNull($grade_grade->load_grade_item());
        $this->assertNotNull($grade_grade->grade_item);
        $this->assertEqual($this->grade_items[0]->id, $grade_grade->grade_item->id);
    }

    function test_grade_grade_standardise_score() {
        $this->assertEqual(4, round(grade_grade::standardise_score(6, 0, 7, 0, 5)));
        $this->assertEqual(40, grade_grade::standardise_score(50, 30, 80, 0, 100));
    }

    /**
     * In this test we always set the 2nd param of set_locked() to false, because it 
     * would otherwise trigger the refresh_grades method, which is not being tested here.
     */ 
    function test_grade_grade_set_locked() {
        global $db;
        $grade_item = new grade_item($this->grade_items[0], false);
        $grade = new grade_grade($grade_item->get_final(1), false);
        $grade->grade_item = $grade_item;
        $grade->itemid = $grade_item->id;

        $this->assertTrue(method_exists($grade, 'set_locked'));

        $this->assertTrue(empty($grade_item->locked));
        $this->assertTrue(empty($grade->locked));
        
        // Test locking the grade when needsupdate is true
        $grade->grade_item->needsupdate = true;
        $this->assertFalse($grade->set_locked(true, false, false)); 

        // Test locking the grade when needsupdate is false
        $grade->grade_item->needsupdate = false;
        
        $column = new stdClass();
        $column->name = 'locked';
        $db->setReturnValue('MetaColumns', array($column));

        $this->assertTrue($grade->set_locked(true, false, false));
        $this->assertFalse(empty($grade->locked));
        $this->assertTrue($grade->set_locked(false, false, false));
        $this->assertTrue(empty($grade->locked));

        $grade = new grade_grade($grade_item->get_final(1), false);
        $grade->grade_item = $grade_item;
        $grade->itemid = $grade_item->id;

        $this->assertTrue($grade->set_locked(false, false, false));
    }


    function test_grade_grade_is_locked() {
        $grade = new grade_grade($this->grade_grades[0], false);
        $grade->grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade, 'is_locked'));

        $this->assertFalse($grade->is_locked());
        $grade->locked = time();
        $this->assertTrue($grade->is_locked());
    }

    function test_grade_grade_set_hidden() {
        global $db;

        $grade_item = new grade_item($this->grade_items[0], false);
        $grade = new grade_grade($grade_item->get_final(1), false);
        $grade->grade_item = $grade_item;
        $grade->itemid = $grade_item->id;
        $this->assertTrue(method_exists($grade, 'set_hidden'));

        $this->assertEqual(0, $grade_item->hidden);
        $this->assertEqual(0, $grade->hidden);

        $column = new stdClass();
        $column->name = 'hidden';
        $db->setReturnValue('MetaColumns', array($column));
        
        $grade->set_hidden(0);
        $this->assertEqual(0, $grade->hidden);

        $grade->set_hidden(1);
        $this->assertEqual(1, $grade->hidden);

        // @TODO test with cascading on (2nd param set to true)
    }

    function test_grade_grade_is_hidden() {
        $grade = new grade_grade($this->grade_grades[0], false);
        $grade->grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade, 'is_hidden'));

        $this->assertFalse($grade->is_hidden());
        $grade->hidden = 1;
        $this->assertTrue($grade->is_hidden());

        $grade->hidden = time()-666;
        $this->assertFalse($grade->is_hidden());

        $grade->hidden = time()+666;
        $this->assertTrue($grade->is_hidden());
    }
}
?>
