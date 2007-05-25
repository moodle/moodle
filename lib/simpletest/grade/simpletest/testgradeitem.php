<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-2004  Martin Dougiamas  http://dougiamas.com       //
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
@set_time_limit(0);
/**
 * Unit tests for grade_item object.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */
require_once(dirname(__FILE__) . '/../../../../config.php');
global $CFG;
require_once($CFG->libdir . '/simpletest/testgradelib.php');

class grade_item_test extends gradelib_test {

    function test_grade_item_construct() { 
        $params = new stdClass();

        $params->courseid = $this->courseid;
        $params->categoryid = $this->grade_categories[1]->id;
        $params->itemname = 'unittestgradeitem4';
        $params->itemtype = 'mod';
        $params->itemmodule = 'database';
        $params->iteminfo = 'Grade item used for unit testing';

        $grade_item = new grade_item($params, false);

        $this->assertEqual($params->courseid, $grade_item->courseid);
        $this->assertEqual($params->categoryid, $grade_item->categoryid);
        $this->assertEqual($params->itemmodule, $grade_item->itemmodule);
    }

    function test_grade_item_insert() {
        $grade_item = new grade_item();
        $this->assertTrue(method_exists($grade_item, 'insert'));
        
        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitem4';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminfo = 'Grade item used for unit testing';

        // Check the grade_category's needsupdate variable first
        $category = $grade_item->get_category(); 
        $category->load_grade_item();
        $category->grade_item->needsupdate = false;
        $this->assertNotNull($category->grade_item);

        $grade_item->insert();

        // Now check the needsupdate variable, it should have been set to true
        $category->grade_item->update_from_db();
        $this->assertTrue($category->grade_item->needsupdate);

        $last_grade_item = end($this->grade_items);

        $this->assertEqual($grade_item->id, $last_grade_item->id + 1);
        $this->assertEqual(11, $grade_item->sortorder);
    }

    function test_grade_item_delete() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'delete'));
        
        // Check the grade_category's needsupdate variable first
        $category = $grade_item->get_category(); 
        $category->load_grade_item();
        $this->assertNotNull($category->grade_item);
        $category->grade_item->needsupdate = false;
        
        $this->assertTrue($grade_item->delete());

        // Now check the needsupdate variable, it should have been set to true
        $category->grade_item->update_from_db();
        $this->assertTrue($category->grade_item->needsupdate);

        $this->assertFalse(get_record('grade_items', 'id', $grade_item->id));
    }

    function test_grade_item_update() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'update'));
        
        $grade_item->iteminfo = 'Updated info for this unittest grade_item';

        // Check the grade_category's needsupdate variable first
        $category= $grade_item->get_category(); 
        $category->load_grade_item();
        $this->assertNotNull($category->grade_item);
        $category->grade_item->needsupdate = false;
        
        $this->assertTrue($grade_item->update());

        // Now check the needsupdate variable, it should NOT have been set to true, because insufficient changes to justify update.
        $this->assertFalse($category->grade_item->needsupdate);
        
        $grade_item->grademin = 14; 
        $this->assertTrue($grade_item->qualifies_for_update());
        $this->assertTrue($grade_item->update(true));
        
        // Now check the needsupdate variable, it should have been set to true
        $category->grade_item->update_from_db();
        $this->assertTrue($category->grade_item->needsupdate);

        // Also check parent
        $category->load_parent_category();
        $category->parent_category->load_grade_item();
        $this->assertTrue($category->parent_category->grade_item->needsupdate);
        
        $iteminfo = get_field('grade_items', 'iteminfo', 'id', $this->grade_items[0]->id);
        $this->assertEqual($grade_item->iteminfo, $iteminfo);
    }

    function test_grade_item_qualifies_for_update() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'qualifies_for_update'));
        
        $grade_item->iteminfo = 'Updated info for this unittest grade_item';
        
        $this->assertFalse($grade_item->qualifies_for_update());

        $grade_item->grademin = 14;

        $this->assertTrue($grade_item->qualifies_for_update()); 
    }

    function test_grade_item_fetch() {
        $grade_item = new grade_item(); 
        $this->assertTrue(method_exists($grade_item, 'fetch'));

        $grade_item = grade_item::fetch('id', $this->grade_items[0]->id);
        $this->assertEqual($this->grade_items[0]->id, $grade_item->id);
        $this->assertEqual($this->grade_items[0]->iteminfo, $grade_item->iteminfo); 
        
        $grade_item = grade_item::fetch('itemtype', $this->grade_items[1]->itemtype, 'itemmodule', $this->grade_items[1]->itemmodule);
        $this->assertEqual($this->grade_items[1]->id, $grade_item->id);
        $this->assertEqual($this->grade_items[1]->iteminfo, $grade_item->iteminfo); 
    }

    function test_grade_item_fetch_all_using_this() {
        $grade_item = new grade_item();
        $grade_item->itemtype = 'mod';
        $this->assertTrue(method_exists($grade_item, 'fetch_all_using_this'));
       
        $grade_items = $grade_item->fetch_all_using_this();
        $this->assertEqual(5, count($grade_items));
        $first_grade_item = reset($grade_items);
        $this->assertEqual($this->grade_items[0]->id, $first_grade_item->id);
    }
    
    /**
     * Retrieve all raw scores for a given grade_item.
     */
    function test_grade_item_get_all_raws() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'get_raw'));
        
        $raw_grades = $grade_item->get_raw();
        $this->assertEqual(3, count($raw_grades));
    }

    /**
     * Retrieve the raw score for a specific userid.
     */
    function test_grade_item_get_raw() { 
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'get_raw'));
        
        $raw_grades = $grade_item->get_raw($this->userid);
        $raw_grade = current($raw_grades);
        $this->assertEqual(1, count($raw_grades));        
        $this->assertEqual($this->grade_grades_raw[0]->gradevalue, $raw_grade->gradevalue);
    }

    
    /**
     * Retrieve all final scores for a given grade_item.
     */
    function test_grade_item_get_all_finals() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'get_final'));
        
        $final_grades = $grade_item->get_final();
        $this->assertEqual(3, count($final_grades)); 
    }

    
    /**
     * Retrieve all final scores for a specific userid.
     */
    function test_grade_item_get_final() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'get_final'));
        
        $final_grades = $grade_item->get_final($this->userid);
        $final_grade = current($final_grades);
        $this->assertEqual(1, count($final_grade));
        $this->assertEqual($this->grade_grades_final[0]->gradevalue, $final_grade->gradevalue); 
    }

    function test_grade_item_get_calculation() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'get_calculation'));
        
        $grade_calculation = $grade_item->get_calculation();
        $this->assertEqual($this->grade_calculations[0]->id, $grade_calculation->id);
    }

    function test_grade_item_set_calculation() {
        $grade_item = new grade_item($this->grade_items[1]);
        $this->assertTrue(method_exists($grade_item, 'set_calculation'));
        $this->assertTrue(method_exists($grade_item, 'get_calculation'));
        
        $calculation = 'SUM([unittestgradeitem1], [unittestgradeitem3])';
        $grade_item->set_calculation($calculation);
        $new_calculation = $grade_item->get_calculation();

        $this->assertEqual($calculation, $new_calculation->calculation);
    }

    function test_grade_item_get_category() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'get_category'));
        
        $category = $grade_item->get_category();
        $this->assertEqual($this->grade_categories[1]->fullname, $category->fullname);
    }

    /**
     * Test update of all final grades, then only 1 grade (give a $userid)
     */
    function test_grade_item_update_final_grades() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'update_final_grade'));
        $this->assertEqual(3, $grade_item->update_final_grade()); 
        $this->assertEqual(1, $grade_item->update_final_grade(1)); 
    }

    /**
     * Test loading of raw and final items into grade_item.
     */
    function test_grade_item_load() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'load_final'));
        $this->assertTrue(method_exists($grade_item, 'load_raw'));

        // Check that final and raw items are not yet loaded
        $this->assertTrue(empty($grade_item->grade_grades_final));
        $this->assertTrue(empty($grade_item->grade_grades_raw));
        
        // Load raw and final grades
        $grade_item->load_final();
        $grade_item->load_raw();
        
        // Check that final and raw grades are now loaded
        $this->assertFalse(empty($grade_item->grade_grades_final));
        $this->assertFalse(empty($grade_item->grade_grades_raw));
        $this->assertEqual($this->grade_grades_final[0]->gradevalue, $grade_item->grade_grades_final[1]->gradevalue);
        $this->assertEqual($this->grade_grades_raw[0]->gradevalue, $grade_item->grade_grades_raw[1]->gradevalue);
    }

    /**
     * Test loading final items, generating fake values to replace missing grades
     */
    function test_grade_item_load_fake_final() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'load_final'));
        global $CFG;
        $CFG->usenullgrades = true;

        // Delete one of the final grades
        $final_grade = new grade_grades_final($this->grade_grades_final[0]);
        $final_grade->delete();
        unset($this->grade_grades_final[0]);

        // Load the final grades
        $final_grades = $grade_item->load_final(true);
        $this->assertEqual(3, count($final_grades));
        $this->assertEqual($grade_item->grademin, $final_grades[1]->gradevalue); 

        // Load normal final grades
        $final_grades = $grade_item->load_final();
        $this->assertEqual(2, count($final_grades));
    }
    
    /**
     * Test the adjust_grade method
     */
    function test_grade_item_adjust_grade() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'adjust_grade'));
        $grade_raw = new stdClass();

        $grade_raw->gradevalue = 40;
        $grade_raw->grademax = 100;
        $grade_raw->grademin = 0;
        
        $grade_item->multfactor = 1;
        $grade_item->plusfactor = 0;
        $grade_item->grademax = 50;
        $grade_item->grademin = 0;
        
        $original_grade_raw  = clone($grade_raw);
        $original_grade_item = clone($grade_item);

        $this->assertEqual(20, $grade_item->adjust_grade($grade_raw)); 
        
        // Try a larger maximum grade
        $grade_item->grademax = 150;
        $grade_item->grademin = 0;
        $this->assertEqual(60, $grade_item->adjust_grade($grade_raw)); 

        // Try larger minimum grade
        $grade_item->grademin = 50;

        $this->assertEqual(90, $grade_item->adjust_grade($grade_raw)); 

        // Rescaling from a small scale (0-50) to a larger scale (0-100)
        $grade_raw->grademax = 50;
        $grade_raw->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->grademin = 0;

        $this->assertEqual(80, $grade_item->adjust_grade($grade_raw)); 

        // Rescaling from a small scale (0-50) to a larger scale with offset (40-100)
        $grade_item->grademax = 100;
        $grade_item->grademin = 40;

        $this->assertEqual(88, $grade_item->adjust_grade($grade_raw)); 

        // Try multfactor and plusfactor
        $grade_raw = clone($original_grade_raw);
        $grade_item = clone($original_grade_item);
        $grade_item->multfactor = 1.23;
        $grade_item->plusfactor = 3;

        $this->assertEqual(27.6, $grade_item->adjust_grade($grade_raw)); 

        // Try multfactor below 0 and a negative plusfactor
        $grade_raw = clone($original_grade_raw);
        $grade_item = clone($original_grade_item);
        $grade_item->multfactor = 0.23;
        $grade_item->plusfactor = -3;

        $this->assertEqual(round(1.6), round($grade_item->adjust_grade($grade_raw))); 
    }

    function test_grade_item_adjust_scale_grade() {
        // Load grade item and its scale
        $grade_item = new grade_item(array('scaleid' => $this->scale[1]->id), false);
        $grade_item->gradetype = GRADE_TYPE_SCALE;
        $grade_item->insert();
        $grade_item->load_scale();
        $this->assertEqual('Very Good', $grade_item->scale->scale_items[1]);
        
        // Load raw grade and its scale
        $grade_raw = new grade_grades_raw(array('scaleid' => $this->scale[0]->id), false);
        $grade_raw->gradevalue = 4;
        $grade_raw->itemid = $grade_item->id;
        $grade_raw->userid = 1;
        $grade_raw->insert();
        $grade_raw->load_scale();
        $this->assertEqual('Fairly neutral', $grade_raw->scale->scale_items[2]);
        
        // Test grade_item::adjust_scale
        $this->assertEqual(3, round($grade_item->adjust_grade($grade_raw, null, 'gradevalue')));
        $grade_raw->gradevalue = 6;
        $this->assertEqual(4, $grade_item->adjust_grade($grade_raw, null, 'gradevalue'));

        // Check that the final grades have the correct values now
        $grade_item->load_raw();
        $grade_item->update_final_grade();
        
        $this->assertFalse(empty($grade_item->grade_grades_final));
        $this->assertEqual($grade_item->id, $grade_item->grade_grades_final[1]->itemid);
        $this->assertEqual(2.66667, round($grade_item->grade_grades_final[1]->gradevalue, 5));
        $this->assertEqual(1, $grade_item->grade_grades_final[1]->userid);
    }

    function test_grade_item_toggle_locking() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'toggle_locking'));

        $this->assertFalse($grade_item->locked);
        $this->assertEqual(0, $grade_item->toggle_locking());
        $this->assertTrue($grade_item->locked);
        $grade_item->load_final();
        $this->assertFalse($grade_item->grade_grades_final[1]->locked);
        
        $grade_item->locked = false;
        $this->assertEqual(3, $grade_item->toggle_locking(true));
        $this->assertTrue($grade_item->locked);
        $this->assertTrue($grade_item->grade_grades_final[1]->locked);
        $this->assertTrue($grade_item->grade_grades_final[2]->locked);
        $this->assertTrue($grade_item->grade_grades_final[3]->locked);
    }

    function test_grade_item_toggle_hiding() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'toggle_hiding'));

        $this->assertFalse($grade_item->hidden);
        $this->assertEqual(0, $grade_item->toggle_hiding());
        $this->assertTrue($grade_item->hidden);
        $grade_item->load_final();
        $this->assertFalse($grade_item->grade_grades_final[1]->hidden);
        
        $grade_item->hidden = false;
        $this->assertEqual(3, $grade_item->toggle_hiding(true));
        $this->assertTrue($grade_item->hidden);
        $this->assertTrue($grade_item->grade_grades_final[1]->hidden);
        $this->assertTrue($grade_item->grade_grades_final[2]->hidden);
        $this->assertTrue($grade_item->grade_grades_final[3]->hidden);
    } 

    function test_grade_item_generate_final() {
        $grade_item = new grade_item();
        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitem4';
        $grade_item->itemtype = 'mod';
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminfo = 'Grade item used for unit testing';

        $grade_item->insert();

        $grade_grades_raw = new grade_grades_raw();
        $grade_grades_raw->itemid = $grade_item->id;
        $grade_grades_raw->userid = 1;
        $grade_grades_raw->gradevalue = 88;
        $grade_grades_raw->grademax = 110;
        $grade_grades_raw->grademin = 18;
        $grade_grades_raw->insert();

        $grade_grades_raw = new grade_grades_raw();
        $grade_grades_raw->itemid = $grade_item->id;
        $grade_grades_raw->userid = 2;
        $grade_grades_raw->gradevalue = 68;
        $grade_grades_raw->grademax = 110;
        $grade_grades_raw->grademin = 18;
        $grade_grades_raw->insert();

        $grade_grades_raw = new grade_grades_raw();
        $grade_grades_raw->itemid = $grade_item->id;
        $grade_grades_raw->userid = 3;
        $grade_grades_raw->gradevalue = 81;
        $grade_grades_raw->grademax = 110;
        $grade_grades_raw->grademin = 18;
        $grade_grades_raw->insert();

        $grade_item->load_raw();
        $this->assertEqual(3, count($grade_item->grade_grades_raw));

        $grade_item->generate_final();
        $grade_item->load_final();
        $this->assertEqual(3, count($grade_item->grade_grades_final)); 
    }

    function test_float_keys() {
    }
} 
?>
