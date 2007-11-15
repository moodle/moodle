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
 * Unit tests for grade_item object.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/simpletest/fixtures/gradetest.php');

@set_time_limit(0);

class grade_item_test extends grade_test {
    
    function setUp() {
        parent::setUp();
        $this->load_grade_items();        
    }
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
        
        Mock::generatePartial('grade_item', 'mock_grade_item_for_insert', array('load_scale', 'is_course_item', 'is_category_item', 'force_regrading'));
        $grade_item = new mock_grade_item_for_insert($this);
        $grade_item->lib_wrapper = new mock_lib_wrapper();
        
        $this->assertTrue(method_exists($grade_item, 'insert'));

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitem4';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminfo = 'Grade item used for unit testing';

        $grade_item->lib_wrapper->expectCallCount('insert_record', 2); // main insert and history table insert 
        $grade_item->lib_wrapper->setReturnValue('insert_record', 4);
        $grade_item->lib_wrapper->expectOnce('get_record'); // for update_from_db() method
        $grade_item->lib_wrapper->setReturnValue('get_record', array(1));
        $grade_item->insert();

        $this->assertEqual($grade_item->id, 4);

        $this->assertFalse(empty($grade_item->timecreated));
        $this->assertFalse(empty($grade_item->timemodified));
    }

    function test_grade_item_delete() {
        
        $source = 'unit tests';
        
        Mock::generatePartial('grade_item', 'mock_grade_item_for_delete', array('is_course_item', 'force_regrading'));
        Mock::generatePartial('grade_grade', 'mock_grade_grade_for_item_delete', array('delete'));
        
        $grade_item =& new mock_grade_item_for_delete($this);
        $grade_item->lib_wrapper = new mock_lib_wrapper();
        $grade_item->id = 1;
        
        $grade_item->lib_wrapper->expectOnce('delete_records', array($grade_item->table, 'id', $grade_item->id));
        $grade_item->lib_wrapper->setReturnValue('delete_records', true);
        
        $grade_grade_instance = grade_object::get_instance('grade_grade');
        
        $grade_grades = array();
        $grade_grades[1] = new mock_grade_grade_for_item_delete($this);
        $grade_grades[1]->expectOnce('delete', array($source));
        $grade_grades[2] = new mock_grade_grade_for_item_delete($this);
        $grade_grades[2]->expectOnce('delete', array($source));
        $grade_grades[3] = new mock_grade_grade_for_item_delete($this);
        $grade_grades[3]->expectOnce('delete', array($source));
        
        $grade_grade_instance->expectOnce('fetch_all', array(array('itemid' => $grade_item->id)));
        $grade_grade_instance->setReturnValue('fetch_all', $grade_grades);
         
        $this->assertTrue(method_exists($grade_item, 'delete'));

        $this->assertTrue($grade_item->delete($source));
    }

    function test_grade_item_update() {
        
        Mock::generatePartial('grade_item', 'mock_grade_item_for_update', array('force_regrading', 'qualifies_for_regrading', 'load_scale'));
        $grade_item = new mock_grade_item_for_update($this);
        grade_object::set_properties($grade_item, $this->grade_items[0]);
        $grade_item->iteminfo = 'Updated info for this unittest grade_item';
        $grade_item->lib_wrapper = new mock_lib_wrapper();
        $grade_item->lib_wrapper->expectOnce('update_record', array($grade_item->table, '*'));
        $grade_item->lib_wrapper->setReturnValue('update_record', true);
        $this->assertTrue(method_exists($grade_item, 'update'));

        $grade_item->expectOnce('load_scale', array());
        $grade_item->expectOnce('qualifies_for_regrading', array());
        $grade_item->setReturnValue('qualifies_for_regrading', true);
        $grade_item->expectOnce('force_regrading', array());
        
        $this->assertTrue($grade_item->update()); 
    }

    function test_grade_item_load_scale() {
        $grade_item = new grade_item($this->grade_items[2], false);
        $this->assertTrue(method_exists($grade_item, 'load_scale'));
        $grade_item->lib_wrapper = new mock_lib_wrapper();
        $grade_item->gradetype = GRADE_TYPE_VALUE; // Should return null
        $this->assertNull($grade_item->load_scale());

        $grade_item->gradetype = GRADE_TYPE_SCALE;
        $grade_item->scaleid = 1;
        $grade_scale = grade_object::get_instance('grade_scale');
        $grade_scale->expectOnce('fetch', array(array('id' => $grade_item->scaleid)));
        $item_grade_scale = new mock_grade_scale();
        $item_grade_scale->expectOnce('load_items', array());
        $item_grade_scale->scale_items = array(1, 2, 3);
        $grade_scale->setReturnValue('fetch', $item_grade_scale);
        $grade_item->scale = $grade_scale;
        
        $this->assertEqual($item_grade_scale, $grade_item->load_scale());
        $this->assertEqual(3, $grade_item->grademax);
        $this->assertEqual(1, $grade_item->grademin);
    }

    function test_grade_item_load_outcome() {
        $this->load_grade_outcomes();
        $grade_item = new grade_item($this->grade_items[0], false);
        $grade_item->outcomeid = 1;
        $this->assertTrue(method_exists($grade_item, 'load_outcome'));
        $grade_item->lib_wrapper = new mock_lib_wrapper();
        $grade_outcome = grade_object::get_instance('grade_outcome');
        $grade_outcome->expectOnce('fetch', array(array('id' => $grade_item->outcomeid)));
        $return_outcome = new grade_outcome($this->grade_outcomes[0], false);
        $grade_outcome->setReturnValue('fetch', $return_outcome);
        $this->assertEqual($return_outcome, $grade_item->load_outcome());
    }

    function test_grade_item_qualifies_for_regrading() {
        // Setup
        Mock::generatePartial('grade_item', 'mock_grade_item_for_qualifies', array('get_instance')); 
        $grade_item = new mock_grade_item_for_qualifies($this);
        $this->assertTrue(method_exists($grade_item, 'qualifies_for_regrading'));
        grade_object::set_properties($grade_item, $this->grade_items[0]);

        // Should return false when no item->id given
        $grade_item->id = null; 
        $this->assertFalse($grade_item->qualifies_for_regrading());

        // Returns false because new record is identical to original
        $grade_item->id = 1;
        $grade_item->expectCallCount('get_instance', 2, array('grade_item', array('id' => $grade_item->id)));
        $db_item = grade_object::get_instance('grade_item', $this->grade_items[0], false);
        $grade_item->setReturnValue('get_instance', $db_item); 
        $this->assertFalse($grade_item->qualifies_for_regrading()); 
        
        // Should return true when one of the fields is different
        $grade_item->gradetype = GRADE_TYPE_NONE;
        $this->assertTrue($grade_item->qualifies_for_regrading());
    }

    function test_grade_item_force_regrading() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $grade_item->lib_wrapper = new mock_lib_wrapper();
        $grade_item->lib_wrapper->expectOnce('set_field_select', array('grade_items', 'needsupdate', 1, '*'));
        $this->assertTrue(method_exists($grade_item, 'force_regrading'));

        $this->assertEqual(0, $grade_item->needsupdate);

        $grade_item->force_regrading();
        $this->assertEqual(1, $grade_item->needsupdate);
    }

    function test_grade_item_get_all_finals() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'get_final'));
        $grade_item->lib_wrapper = new mock_lib_wrapper();
        $grade_item->lib_wrapper->expectOnce('get_records', array('grade_grades', 'itemid', $grade_item->id));
        $grade_grades = array();
        $grade_grades[1] = new mock_grade_grade();
        $grade_grades[1]->userid = 1;
        $grade_grades[2] = new mock_grade_grade();
        $grade_grades[2]->userid = 2;
        $grade_grades[3] = new mock_grade_grade();
        $grade_grades[3]->userid = 3;
        $grade_grades[4] = new mock_grade_grade(); // final grades are indexed by userid, so if 2 are given with the same userid, the last one will override the first
        $grade_grades[4]->userid = 3;
        $grade_item->lib_wrapper->setReturnValue('get_records', $grade_grades);

        $final_grades = $grade_item->get_final();
        $this->assertEqual(3, count($final_grades));
    } 

    function test_grade_item_get_final() {
        $this->load_grade_grades();
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'get_final'));
        $grade_item->lib_wrapper = new mock_lib_wrapper();
        $grade_item->lib_wrapper->expectOnce('get_record', array('grade_grades', 'itemid', $grade_item->id, 'userid', $this->userid));
        $grade_item->lib_wrapper->setReturnValue('get_record', $this->grade_grades[0]);
        $final_grade = $grade_item->get_final($this->userid);
        $this->assertEqual($this->grade_grades[0]->finalgrade, $final_grade->finalgrade);
    }

    function test_grade_item_set_sortorder() {
        Mock::generatePartial('grade_item', 'mock_grade_item_for_set_sortorder', array('update'));

        $grade_item = new mock_grade_item_for_set_sortorder($this);
        $this->assertTrue(method_exists($grade_item, 'set_sortorder'));
        $grade_item->expectOnce('update', array());
        $grade_item->sortorder = 1;
        $grade_item->set_sortorder(999);
        $this->assertEqual($grade_item->sortorder, 999);
    }

    function test_grade_item_move_after_sortorder() {
        Mock::generatePartial('grade_item', 'mock_grade_item_for_move_after', array('set_sortorder'));
        
        $sortorder = 5;
        $grade_item = new mock_grade_item_for_move_after($this);
        $this->assertTrue(method_exists($grade_item, 'move_after_sortorder'));
        $grade_item->lib_wrapper = new mock_lib_wrapper();
        $grade_item->lib_wrapper->expectOnce('execute_sql', array('*', false));
        $grade_item->expectOnce('set_sortorder', array($sortorder + 1));
        $grade_item->move_after_sortorder($sortorder);
    }

    function test_grade_item_set_parent() {

        Mock::generatePartial('grade_item', 'mock_grade_item_for_set_parent', array('force_regrading', 'update'));
        $grade_item = new mock_grade_item_for_set_parent($this);
        $grade_item->lib_wrapper = new mock_lib_wrapper();
        $this->assertTrue(method_exists($grade_item, 'set_parent'));
    
        // When categoryid == $parentid param, method should return true but no force_regrading or update should be called
        $grade_item->categoryid = 1;
        $grade_item->expectNever('update');
        $grade_item->expectNever('force_regrading');
        $this->assertTrue($grade_item->set_parent(1));

        // When parentid param is different from categoryid, force_regrading and update must be called
        $grade_item = new mock_grade_item_for_set_parent($this);
        $grade_item->categoryid = 2;
        $grade_item->courseid = $this->courseid;
        $parentid = 4;
        $grade_item->expectOnce('update', array());
        $grade_item->setReturnValue('update', true);
        $grade_item->expectOnce('force_regrading', array());
        $grade_category = grade_object::get_instance('grade_category');
        $parent_category = new mock_grade_category();
        $parent_category->id = 1;
        $grade_category->expectOnce('fetch', array(array('id' => $parentid, 'courseid' => $this->courseid)));
        $grade_category->setReturnValue('fetch', $parent_category);
        $this->assertTrue($grade_item->set_parent($parentid)); 
    }

    function test_grade_item_get_parent_category() {
        Mock::generatePartial('grade_item', 'mock_grade_item_for_get_parent_category', array('is_category_item', 'is_course_item', 'get_item_category'));
        
        // When item is a course or category item, the method should return the item category
        $grade_item = new mock_grade_item_for_get_parent_category($this);
        $grade_item->expectOnce('is_category_item', array());
        $grade_item->expectOnce('is_course_item', array());
        $grade_item->expectOnce('get_item_category', array());
        $grade_item->setReturnValue('is_category_item', false);
        $grade_item->setReturnValue('is_course_item', true);
        $grade_item->setReturnValue('get_item_category', 'item_category');
        $this->assertEqual('item_category', $grade_item->get_parent_category());
        
        // When the item is a normal grade item, the method should return the parent category
        $grade_item = new mock_grade_item_for_get_parent_category($this);
        $grade_item->expectOnce('is_category_item', array());
        $grade_item->expectOnce('is_course_item', array());
        $grade_item->setReturnValue('is_category_item', false);
        $grade_item->setReturnValue('is_course_item', false);
        $grade_item->categoryid = 4;
        $grade_category = grade_object::get_instance('grade_category');
        $grade_category->expectOnce('fetch', array(array('id' => $grade_item->categoryid)));
        $grade_category->setReturnValue('fetch', true);
        $this->assertTrue($grade_item->get_parent_category());
    }

    function test_grade_item_regrade_final_grades() {

        Mock::generatePartial('grade_item', 'mock_grade_item_for_regrade_final', array('is_locked', 'is_calculated', 'compute', 'is_outcome_item', 
                'is_category_item', 'is_course_item', 'get_item_category', 'is_manual_item', 'is_raw_used', 'get_instance', 'adjust_raw_grade')); 

        // If grade_item is locked, no regrading occurs but the method returns true
        $grade_item = new mock_grade_item_for_regrade_final($this);
        $grade_item->expectOnce('is_locked', array());
        $grade_item->setReturnValue('is_locked', true);
        $grade_item->expectNever('get_item_category');
        $this->assertTrue($grade_item->regrade_final_grades());
        
        // If the item is calculated and the computation is OK, no regrading occurs and the method returns true
        $grade_item = new mock_grade_item_for_regrade_final($this);
        $grade_item->expectOnce('is_calculated', array());
        $grade_item->setReturnValue('is_calculated', true);
        $grade_item->expectOnce('compute', array($this->userid));
        $grade_item->setReturnValue('compute', true);

        $grade_item->expectNever('get_item_category');
        $this->assertTrue($grade_item->regrade_final_grades($this->userid));
        
        // If the item is calculated but the grades cannot be calculated, method should return an error message
        $grade_item = new mock_grade_item_for_regrade_final($this);
        $grade_item->expectOnce('is_calculated', array());
        $grade_item->setReturnValue('is_calculated', true);
        $grade_item->expectOnce('compute', array($this->userid));
        $grade_item->setReturnValue('compute', false);
        $this->assertEqual("Could not calculate grades for grade item", $grade_item->regrade_final_grades($this->userid)); 

        // If the item is an outcome item, return true before regrading
        $grade_item = new mock_grade_item_for_regrade_final($this);
        $grade_item->expectOnce('is_outcome_item', array());
        $grade_item->setReturnValue('is_outcome_item', true); 
        $this->assertTrue($grade_item->regrade_final_grades($this->userid)); 

        // If the item is a category or course item, category must generate grades, then return true
        $grade_item = new mock_grade_item_for_regrade_final($this);
        $grade_item->expectCallCount('is_category_item', 2, array());
        $grade_item->expectCallCount('is_course_item', 2, array());
        $grade_item->setReturnValue('is_category_item', false); 
        $grade_item->setReturnValue('is_course_item', true);
        $grade_item->expectCallCount('get_item_category', 2, array());
        $category = new mock_grade_category();
        $category->expectCallCount('generate_grades', 2, array($this->userid));
        $category->setReturnValueAt(0, 'generate_grades', true);
        $category->setReturnValueAt(1, 'generate_grades', false); // if generate_grades() is false, method should return false
        $grade_item->setReturnValue('get_item_category', $category);
        $this->assertTrue($grade_item->regrade_final_grades($this->userid)); 
        $this->assertEqual("Could not aggregate final grades for category:".$grade_item->id, $grade_item->regrade_final_grades($this->userid)); 

        // If the item is a manual item, method should return true before regrading
        $grade_item = new mock_grade_item_for_regrade_final($this);
        $grade_item->expectOnce('is_manual_item', array());
        $grade_item->setReturnValue('is_manual_item', true); 
        $this->assertTrue($grade_item->regrade_final_grades($this->userid)); 

        // If the item is not using raw grades, method should return true without regrading 
        $grade_item = new mock_grade_item_for_regrade_final($this);
        $grade_item->expectOnce('is_raw_used', array());
        $grade_item->setReturnValue('is_raw_used', false); 
        $this->assertTrue($grade_item->regrade_final_grades($this->userid)); 
    }

/*
    function test_grade_item_adjust_raw_grade() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'adjust_raw_grade'));
        $grade_raw = new stdClass();

        $grade_raw->rawgrade = 40;
        $grade_raw->grademax = 100;
        $grade_raw->grademin = 0;

        $grade_item->multfactor = 1;
        $grade_item->plusfactor = 0;
        $grade_item->grademax = 50;
        $grade_item->grademin = 0;

        $original_grade_raw  = clone($grade_raw);
        $original_grade_item = clone($grade_item);

        $this->assertEqual(20, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Try a larger maximum grade
        $grade_item->grademax = 150;
        $grade_item->grademin = 0;
        $this->assertEqual(60, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Try larger minimum grade
        $grade_item->grademin = 50;

        $this->assertEqual(90, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Rescaling from a small scale (0-50) to a larger scale (0-100)
        $grade_raw->grademax = 50;
        $grade_raw->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->grademin = 0;

        $this->assertEqual(80, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Rescaling from a small scale (0-50) to a larger scale with offset (40-100)
        $grade_item->grademax = 100;
        $grade_item->grademin = 40;

        $this->assertEqual(88, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Try multfactor and plusfactor
        $grade_raw = clone($original_grade_raw);
        $grade_item = clone($original_grade_item);
        $grade_item->multfactor = 1.23;
        $grade_item->plusfactor = 3;

        $this->assertEqual(27.6, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Try multfactor below 0 and a negative plusfactor
        $grade_raw = clone($original_grade_raw);
        $grade_item = clone($original_grade_item);
        $grade_item->multfactor = 0.23;
        $grade_item->plusfactor = -3;

        $this->assertEqual(round(1.6), round($grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax)));
    }

    function test_grade_item_set_locked() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'set_locked'));

        $grade = new grade_grade($grade_item->get_final(1));
        $this->assertTrue(empty($grade_item->locked));
        $this->assertTrue(empty($grade->locked));

        $this->assertTrue($grade_item->set_locked(true, true));
        $grade = new grade_grade($grade_item->get_final(1));

        $this->assertFalse(empty($grade_item->locked));
        $this->assertFalse(empty($grade->locked)); // individual grades should be locked too

        $this->assertTrue($grade_item->set_locked(false, true));
        $grade = new grade_grade($grade_item->get_final(1));

        $this->assertTrue(empty($grade_item->locked));
        $this->assertTrue(empty($grade->locked)); // individual grades should be unlocked too
    }

    function test_grade_item_is_locked() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'is_locked'));

        $this->assertFalse($grade_item->is_locked());
        $this->assertFalse($grade_item->is_locked(1));
        $this->assertTrue($grade_item->set_locked(true));
        $this->assertTrue($grade_item->is_locked());
        $this->assertTrue($grade_item->is_locked(1));
    }

    function test_grade_item_set_hidden() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'set_hidden'));

        $grade = new grade_grade($grade_item->get_final(1));
        $this->assertEqual(0, $grade_item->hidden);
        $this->assertEqual(0, $grade->hidden);

        $grade_item->set_hidden(666, true);
        $grade = new grade_grade($grade_item->get_final(1));

        $this->assertEqual(666, $grade_item->hidden);
        $this->assertEqual(666, $grade->hidden);
    }

    function test_grade_item_is_hidden() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'is_hidden'));

        $this->assertFalse($grade_item->is_hidden());
        $this->assertFalse($grade_item->is_hidden(1));

        $grade_item->set_hidden(1);
        $this->assertTrue($grade_item->is_hidden());
        $this->assertTrue($grade_item->is_hidden(1));

        $grade_item->set_hidden(666);
        $this->assertFalse($grade_item->is_hidden());
        $this->assertFalse($grade_item->is_hidden(1));

        $grade_item->set_hidden(time()+666);
        $this->assertTrue($grade_item->is_hidden());
        $this->assertTrue($grade_item->is_hidden(1));
    }

    function test_grade_item_is_category_item() {
        $grade_item = new grade_item($this->grade_items[3]);
        $this->assertTrue(method_exists($grade_item, 'is_category_item'));
        $this->assertTrue($grade_item->is_category_item());
    }

    function test_grade_item_is_course_item() {
        $grade_item = grade_item::fetch_course_item($this->courseid);
        $this->assertTrue(method_exists($grade_item, 'is_course_item'));
        $this->assertTrue($grade_item->is_course_item());
    }

    function test_grade_item_fetch_course_item() {
        $grade_item = grade_item::fetch_course_item($this->courseid);
        $this->assertTrue(method_exists($grade_item, 'fetch_course_item'));
        $this->assertTrue($grade_item->itemtype, 'course');
    }

    function test_grade_item_depends_on() {
        $grade_item = new grade_item($this->grade_items[1]);

        // calculated grade dependency
        $deps = $grade_item->depends_on();
        sort($deps, SORT_NUMERIC); // for comparison
        $this->assertEqual(array($this->grade_items[0]->id), $deps);

        // simulate depends on returns none when locked
        $grade_item->locked = time();
        $grade_item->update();
        $deps = $grade_item->depends_on();
        sort($deps, SORT_NUMERIC); // for comparison
        $this->assertEqual(array(), $deps);

        // category dependency
        $grade_item = new grade_item($this->grade_items[3]);
        $deps = $grade_item->depends_on();
        sort($deps, SORT_NUMERIC); // for comparison
        $res = array($this->grade_items[4]->id, $this->grade_items[5]->id);
        $this->assertEqual($res, $deps);
    }

    function test_grade_item_is_calculated() {
        $grade_item = new grade_item($this->grade_items[1]);
        $this->assertTrue(method_exists($grade_item, 'is_calculated'));
        $grade_itemsource = new grade_item($this->grade_items[0]);
        $normalizedformula = str_replace('[['.$grade_itemsource->idnumber.']]', '##gi'.$grade_itemsource->id.'##', $this->grade_items[1]->calculation);

        $this->assertTrue($grade_item->is_calculated());
        $this->assertEqual($normalizedformula, $grade_item->calculation);
    }

    function test_grade_item_set_calculation() {
        $grade_item = new grade_item($this->grade_items[1]);
        $this->assertTrue(method_exists($grade_item, 'set_calculation'));
        $grade_itemsource = new grade_item($this->grade_items[0]);

        $grade_item->set_calculation('=[['.$grade_itemsource->idnumber.']]');

        $this->assertTrue(!empty($grade_item->needsupdate));
        $this->assertEqual('=##gi'.$grade_itemsource->id.'##', $grade_item->calculation);
    }

    function test_grade_item_get_calculation() {
        $grade_item = new grade_item($this->grade_items[1]);
        $this->assertTrue(method_exists($grade_item, 'get_calculation'));
        $grade_itemsource = new grade_item($this->grade_items[0]);

        $denormalizedformula = str_replace('##gi'.$grade_itemsource->id.'##', '[['.$grade_itemsource->idnumber.']]', $this->grade_items[1]->calculation);

        $formula = $grade_item->get_calculation();
        $this->assertTrue(!empty($grade_item->needsupdate));
        $this->assertEqual($denormalizedformula, $formula);
    }

    function test_grade_item_compute() {
        $grade_item = new grade_item($this->grade_items[1]);
        $this->assertTrue(method_exists($grade_item, 'compute'));

        $grade_grade = grade_grade::fetch(array('id'=>$this->grade_grades[3]->id));
        $grade_grade->delete();
        $grade_grade = grade_grade::fetch(array('id'=>$this->grade_grades[4]->id));
        $grade_grade->delete();
        $grade_grade = grade_grade::fetch(array('id'=>$this->grade_grades[5]->id));
        $grade_grade->delete();

        $grade_item->compute();

        $grade_grade = grade_grade::fetch(array('userid'=>$this->grade_grades[3]->userid, 'itemid'=>$this->grade_grades[3]->itemid));
        $this->assertEqual($this->grade_grades[3]->finalgrade, $grade_grade->finalgrade);
        $grade_grade = grade_grade::fetch(array('userid'=>$this->grade_grades[4]->userid, 'itemid'=>$this->grade_grades[4]->itemid));
        $this->assertEqual($this->grade_grades[4]->finalgrade, $grade_grade->finalgrade);
        $grade_grade = grade_grade::fetch(array('userid'=>$this->grade_grades[5]->userid, 'itemid'=>$this->grade_grades[5]->itemid));
        $this->assertEqual($this->grade_grades[5]->finalgrade, $grade_grade->finalgrade);
    }
*/
}
?>
