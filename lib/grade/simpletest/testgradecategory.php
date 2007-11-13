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
 * Unit tests for grade_category object.
 *
 * @author nicolasconnault@gmail.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/simpletest/fixtures/gradetest.php');

class grade_category_test extends grade_test {

    function setUp() {
        parent::setUp();
        $this->load_grade_items();        
    }

    function test_grade_category_build_path() {
        $grade_category = new grade_category(array('parent' => 2, 'id' => 3, 'path' => '/1/2/3/'), false);
        $grade_category->lib_wrapper = new mock_lib_wrapper();
        $this->assertTrue(method_exists($grade_category, 'build_path'));
        
        // Mock get_record of parent category (2) then (1)
        $obj = grade_object::get_instance('grade_category');
        $obj->lib_wrapper->expectCallCount('get_record', 2);
        $parent2 = new stdClass();
        $parent2->parent = 1;
        $parent2->id = 2;
        $obj->lib_wrapper->setReturnValueAt(0, 'get_record', $parent2);
        $parent1 = new stdClass();
        $parent1->parent = null;
        $parent1->id = 1;
        $obj->lib_wrapper->setReturnValueAt(1, 'get_record', $parent1);
       var_dump($grade_category); 
        $path = $grade_category->build_path($grade_category);
        $this->assertEqual($grade_category->path, $path);
    }

    function test_grade_category_update() {

        Mock::generatePartial('grade_category', 'partial_mock', array('load_grade_item',
                                                                      'build_path', 
                                                                      'apply_forced_settings', 
                                                                      'qualifies_for_regrading',
                                                                      'force_regrading'));

        $grade_category = &new partial_mock($this);
        $grade_category->grade_category($this->grade_categories[1], false);

        $this->assertTrue(method_exists($grade_category, 'update'));

        $grade_category->fullname = 'Updated info for this unittest grade_category';
        $grade_category->path = null; // path must be recalculated if missing
        $grade_category->depth = null;
        $grade_category->aggregation = GRADE_AGGREGATE_MAX; // should force regrading
        $grade_category->droplow = 1;
        $grade_category->keephigh = 1;
        $grade_category->lib_wrapper = new mock_lib_wrapper();
        
        $grade_category->lib_wrapper->expectOnce('update_record');
        $grade_category->lib_wrapper->setReturnValue('update_record', true);
        
        $grade_item = new grade_item(array('id' => 99, 'itemtype' => 'course', 'courseid' => $this->courseid, 'needsupdate' => 0), false);
        $grade_category->grade_item = $grade_item;
        $this->assertEqual(0, $grade_item->needsupdate);
        
        // Set method expectations
        $grade_category->expectOnce('load_grade_item');
        $grade_category->expectOnce('apply_forced_settings');
        $grade_category->setReturnValue('qualifies_for_regrading', true);
        $grade_category->expectOnce('force_regrading'); 
        
        $this->assertTrue($grade_category->update());
        
        $this->assertEqual(0, $grade_category->keephigh);
        $this->assertEqual(1, $grade_category->droplow);
        $this->assertTrue($grade_category->timemodified > 0);
        $this->assertTrue($grade_category->timemodified <= time());
    }

    function test_grade_category_delete() {

        Mock::generatePartial('grade_category', 'mock_grade_category_for_delete', array('load_grade_item',
                                                                                        'is_course_category', 
                                                                                        'load_parent_category', 
                                                                                        'force_regrading'));

        $grade_category = &new mock_grade_category_for_delete($this);
        $grade_category->lib_wrapper = new mock_lib_wrapper();
        $grade_category->id = 1;
        $grade_category->expectOnce('load_grade_item');
        $grade_category->setReturnValue('is_course_category', false);
        $grade_category->expectOnce('force_regrading');
        $grade_category->expectOnce('load_parent_category');

        $parent_category = new mock_grade_category();
        $grade_category->lib_wrapper = new mock_lib_wrapper();
        $parent_category->id = 1;
        $grade_category->setReturnReference('load_parent_category', $parent_category);

        $grade_item = new mock_grade_item();
        $grade_item->id = 2;
        $grade_category->setReturnReference('load_grade_item', $grade_item);
        
        $grade_category->lib_wrapper->expectOnce('delete_records', array('grade_categories', 'id', $grade_category->id));
        $grade_category->lib_wrapper->setReturnValue('delete_records', true);
        
        $grade_category->lib_wrapper->expectOnce('insert_record'); // grade_history entry
        
        $this->assertTrue(method_exists($grade_category, 'delete'));
        $this->assertTrue($grade_category->delete());
    }

    function test_grade_category_insert() {

        Mock::generatePartial('grade_category', 'mock_grade_category_for_insert', array('update',
                                                                                        'apply_forced_settings',
                                                                                        'force_regrading'));

        $course_category = $this->grade_categories[0];

        $grade_category = new mock_grade_category_for_insert($this);
        $grade_category->lib_wrapper = new mock_lib_wrapper();
        
        $this->assertTrue(method_exists($grade_category, 'insert'));

        $grade_category->fullname    = 'unittestcategory4';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 100;
        $grade_category->droplow     = 10;
        $grade_category->hidden      = 0;
        $grade_category->parent      = $this->grade_categories[1]->id;

        $grade_category->lib_wrapper->expectCallCount('insert_record', 2); // main insert and history table insert 
        $grade_category->lib_wrapper->setReturnValue('insert_record', 4);
        $grade_category->lib_wrapper->expectOnce('get_record'); // for update_from_db() method
        $grade_category->lib_wrapper->setReturnValue('get_record', array(1));

        $grade_category->insert();
        
        // Don't test path and depth, they're the job of the update method 
        $this->assertEqual($grade_category->id, 4);

        $this->assertFalse(empty($grade_category->timecreated));
        $this->assertFalse(empty($grade_category->timemodified));
    }

    function test_grade_category_insert_course_category() {
        $grade_category = new mock_grade_category_for_insert($this);
        $grade_category->lib_wrapper = new mock_lib_wrapper();
        $this->assertTrue(method_exists($grade_category, 'insert_course_category'));
        $grade_category->expectOnce('apply_forced_settings');
        $grade_category->expectOnce('update', array('system'));

        $grade_category->lib_wrapper->expectCallCount('insert_record', 2); // main insert and history table insert 
        $grade_category->lib_wrapper->setReturnValue('insert_record', 1);
        $grade_category->lib_wrapper->expectOnce('get_record'); // for update_from_db() method
        $grade_category->lib_wrapper->setReturnValue('get_record', array(1));
        
        $id = $grade_category->insert_course_category($this->courseid);
        
        $this->assertNotNull($id);
        $this->assertEqual(get_string('coursegradecategory', 'grades'), $grade_category->fullname);
        $this->assertEqual(GRADE_AGGREGATE_MEAN, $grade_category->aggregation);
        $this->assertNull($grade_category->parent);
        $this->assertFalse(empty($grade_category->timecreated));
        $this->assertFalse(empty($grade_category->timemodified));
    }

    function test_grade_category_qualifies_for_regrading() {
        $grade_category = new grade_category($this->grade_categories[0], false);
        $this->assertTrue(method_exists($grade_category, 'qualifies_for_regrading'));
        
        $obj = grade_object::get_instance('grade_category');
        $obj->expectCallCount('fetch', 4, array(array('id' => $grade_category->id)));
        $obj->setReturnValue('fetch', fullclone($grade_category));
        $grade_item = new stdClass();
        $grade_item->lib_wrapper = new mock_lib_wrapper();
        $grade_item->aggregation = GRADE_AGGREGATE_MEAN;
        
        $this->assertFalse($grade_category->qualifies_for_regrading());

        $grade_category->aggregation = GRADE_AGGREGATE_MAX;
        $this->assertTrue($grade_category->qualifies_for_regrading());

        $grade_category->droplow = 99;
        $this->assertTrue($grade_category->qualifies_for_regrading());

        $grade_category->keephigh = 99;
        $this->assertTrue($grade_category->qualifies_for_regrading());
    }

    function test_grade_category_generate_grades() {

        Mock::generatePartial('grade_category', 'mock_grade_category_for_generate_grades', array('load_grade_item',
                                                                                                 'aggregate_grades'));

        // No generating should occur if the item is locked, but the method still returns true
        $grade_category = new mock_grade_category_for_generate_grades($this);
        $grade_category->lib_wrapper = new mock_lib_wrapper();
        $grade_item = new mock_grade_item();
        $grade_item->setReturnValue('is_locked', true); 
        $grade_category->grade_item = $grade_item;
        $this->assertTrue(method_exists($grade_category, 'generate_grades'));
        $grade_category->expectNever('aggregate_grades');
        $this->assertTrue($grade_category->generate_grades());
        
        // We can't verify DB records, so let's check method calls
        $grade_category = new mock_grade_category_for_generate_grades($this);
        $grade_category->lib_wrapper = new mock_lib_wrapper();
        $grade_item = new mock_grade_item();
        $grade_item->id = 1;
        $grade_category->grade_item = $grade_item;
        $grade_item->setReturnValue('is_locked', false);
        $grade_item->setReturnValue('depends_on', array(1, 2, 3)); 
        $grade_category->lib_wrapper->expectOnce('get_records_sql');
        $grade_category->lib_wrapper->setReturnValue('get_records_sql', array($this->grade_items[2], $this->grade_items[3], $this->grade_items[4]));
        $grade_category->lib_wrapper->expectOnce('get_recordset_sql');
        $grade_category->lib_wrapper->setReturnValue('get_recordset_sql', 1);
        $grade_category->lib_wrapper->expectCallCount('rs_fetch_next_record', 4);

        $record = new stdClass();
        $record->userid = 1;
        $record->itemid = 1;
        $record->finalgrade = 20; 
        $record->excluded = false;
        $grade_category->lib_wrapper->setReturnValueAt(0, 'rs_fetch_next_record', $record);
        $record2 = new stdClass();
        $record2->userid = 2;
        $record2->itemid = 2;
        $record2->finalgrade = 20; 
        $record2->excluded = false;
        $grade_category->lib_wrapper->setReturnValueAt(1, 'rs_fetch_next_record', $record2);
        $record3 = new stdClass();
        $record3->userid = 3;
        $record3->itemid = 3;
        $record3->finalgrade = 20; 
        $record3->excluded = false;
        $grade_category->lib_wrapper->setReturnValueAt(2, 'rs_fetch_next_record', $record3);
        $this->assertTrue($grade_category->generate_grades()); 
    }

    function test_grade_category_aggregate_grades() {
        Mock::generatePartial('grade_category', 'mock_grade_category_for_aggregate_grades', array('aggregate_values', 'apply_limit_rules'));
        // Setup method arguments (no optionals!)
        $arg_userid = null;
        $arg_items = array();
        $arg_grade_values = array(1 => 20); // should get unset early on in the method
        $arg_oldgrade = new stdClass();
        $arg_oldgrade->finalgrade = null; // These two are set to null to avoid a grade_grade->update() in the aggregate_grades() method
        $arg_oldgrade->rawgrade = null;
        $arg_oldgrade->rawgrademin = 0;
        $arg_oldgrade->rawgrademax = 100;
        $arg_oldgrade->rawscaleid = null;
        $arg_excluded = false;
    
        // Set up rest of objects
        $grade_category = new mock_grade_category_for_aggregate_grades($this);
        $grade_category->lib_wrapper = new mock_lib_wrapper();
        $grade_item = new mock_grade_item();
        $grade_item->id = 1;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $this->assertTrue(method_exists($grade_category, 'aggregate_grades'));

        // If no userid given, should return null;
        $grade_category->expectNever('aggregate_values');
        $grade_category->expectNever('apply_limit_rules');
        $this->assertNull($grade_category->aggregate_grades($arg_userid, $arg_items, $arg_grade_values, $arg_oldgrade, $arg_excluded));
        // Return without doing anything if the grade or grade_item is locked (we have control over the grade_item here because
        // the grade_grade->grade_item is the same as the grade_category->grade_item
        $arg_userid = 1;
        $grade_item->setReturnValueAt(0, 'is_locked', true);
        $grade_category->grade_item = $grade_item;
        $this->assertNull($grade_category->aggregate_grades($arg_userid, $arg_items, $arg_grade_values, $arg_oldgrade, $arg_excluded));

        // Proceed further by setting grade_item->is_locked to false. Still return null because no other grade_values than main item's 
        $grade_item->setReturnValueAt(1, 'is_locked', false);
        $this->assertNull($grade_category->aggregate_grades($arg_userid, $arg_items, $arg_grade_values, $arg_oldgrade, $arg_excluded));
        
        // Going further now with a proper array of gradevalues and items. Also provide an excluded itemid
        $gi = new mock_grade_item();
        $gi->grademin = 0;
        $gi->grademax = 100;
        $arg_items = array(1 => $grade_item, 2 => fullclone($gi), 3 => fullclone($gi), 4 => fullclone($gi), 5 => fullclone($gi));
        $arg_grade_values = array(1 => 20, 2 => null, 3 => 8, 4 => 67, 5 => 53);
        $arg_excluded = array(3);
        $grade_category = new mock_grade_category_for_aggregate_grades($this);
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->scaleid = null;
        $grade_category->grade_item = $grade_item;
        $grade_category->expectOnce('apply_limit_rules');
        $grade_category->expectOnce('aggregate_values');
        $grade_category->aggregateonlygraded = true;
        $this->assertNull($grade_category->aggregate_grades($arg_userid, $arg_items, $arg_grade_values, $arg_oldgrade, $arg_excluded));
    }

    function test_grade_category_apply_limit_rules() {
        $grade_category = new grade_category();
        $grades = array(5.374, 9.4743, 2.5474, 7.3754);

        $grade_category->droplow = 2;
        $grade_category->apply_limit_rules($grades);
        sort($grades, SORT_NUMERIC);
        $this->assertEqual(array(7.3754, 9.4743), $grades);

        $grade_category = new grade_category();
        $grades = array(5.374, 9.4743, 2.5474, 7.3754);

        $grade_category->keephigh = 1;
        $grade_category->droplow = 0;
        $grade_category->apply_limit_rules($grades);
        $this->assertEqual(count($grades), 1);
        $grade = reset($grades);
        $this->assertEqual(9.4743, $grade);
    }

    function test_grade_category_is_aggregationcoef_used() {

    }

    function test_grade_category_fetch_course_tree() {
        $grade_category = new grade_category();
        $this->assertTrue(method_exists($grade_category, 'fetch_course_tree'));
        //TODO: add some tests
    }

    function test_grade_category_get_children() {
        $grade_category = new grade_category();
        $grade_category->id = 1;
        $grade_category->lib_wrapper = new mock_lib_wrapper();

        // Setup course cats and items: 
        // 1 course
        // |_ 2 category
        // | |_ 3 item
        // | |_ 4 item
        // |_ 5 category
        // | |_ 6 item
        // | |_ 7 item

        $cats = array();
        $cat = new stdClass();
        $cat->id = 1;
        $cat->parent = null;
        $cat->sortorder = 1;
        $cat->depth = 0;
        $cats[1] = fullclone($cat);
        $cat->id = 3;
        $cat->parent = 1;
        $cat->sortorder = 2;
        $cat->depth = 1;
        $cats[3] = fullclone($cat);
        $cat->id = 4;
        $cat->sortorder = 5;
        $cat->depth = 1;
        $cats[4] = fullclone($cat);

        $item = new stdClass();
        $item->itemtype = 'course';
        $item->iteminstance = 1;
        $item->sortorder = 1;
        $items = array();
        $items[5] = fullclone($item);
        $item->itemtype = 'category';
        $item->iteminstance = 3;
        $item->sortorder = 2;
        $items[6] = fullclone($item);
        $item->iteminstance = 4;
        $item->sortorder = 5;
        $items[7] = fullclone($item);
        $item->itemtype = 'item';
        $item->categoryid = 3;
        $item->sortorder = 3;
        $items[8] = fullclone($item);
        $item->categoryid = 3;
        $item->sortorder = 4;
        $items[9] = fullclone($item);
        $item->categoryid = 4;
        $item->sortorder = 6;
        $items[10] = fullclone($item);
        $item->categoryid = 4;
        $item->sortorder = 7;
        $items[11] = fullclone($item);

        $grade_category->lib_wrapper->setReturnValueAt(0, 'get_records', $cats);
        $grade_category->lib_wrapper->setReturnValueAt(1, 'get_records', $items);
        $this->assertTrue(method_exists($grade_category, 'get_children'));
    
        // Do not test recursion
        $children_array = $grade_category->get_children(true);

        $this->assertTrue(is_array($children_array));
        $this->assertEqual(3, count($children_array));
        $this->assertEqual('grade_item', get_class($children_array[1]['object']));
        $this->assertEqual('courseitem', $children_array[1]['type']);
        $this->assertequal(0, $children_array[1]['depth']);
        $this->assertEqual('course', $children_array[1]['object']->itemtype);

        $this->assertEqual('grade_category', get_class($children_array[2]['object']));
        $this->assertEqual('category', $children_array[2]['type']);
        $this->assertequal(1, $children_array[2]['depth']);
        $this->assertEqual(3, count($children_array[2]['children']));
    }

    function test_grade_category_load_parent_category() {
        Mock::generatePartial('grade_category', 'mock_grade_category_for_load_parent_category', array('get_parent_category'));

        $grade_category = new mock_grade_category_for_load_parent_category($this);;
        $grade_category->parent = 1;
        $parent = new grade_category($this->grade_categories[0], false);
        $grade_category->setReturnReference('get_parent_category', $parent);
        $grade_category->expectOnce('get_parent_category', array());
        $this->assertTrue(method_exists($grade_category, 'load_parent_category'));
        $this->assertEqual(null, $grade_category->parent_category);
        $grade_category->load_parent_category();
        $this->assertEqual($this->grade_categories[0]->id, $grade_category->parent_category->id);
    }

    function test_grade_category_get_name() {
        $grade_category = new grade_category($this->grade_categories[0], false);
        $grade_category->lib_wrapper = new mock_lib_wrapper();
        $this->assertTrue(method_exists($grade_category, 'get_name'));
        $grade_category->lib_wrapper->expectOnce('get_record', array('course', 'id', $this->courseid));
        $course = new stdClass();
        $course->fullname = $grade_category->fullname;
        $grade_category->lib_wrapper->setReturnValue('get_record', $course);
        $grade_category->parent = null;
        $this->assertEqual(format_string($this->grade_categories[0]->fullname), $grade_category->get_name());

        // Test with a parent id
        $grade_category = new grade_category($this->grade_categories[0], false);
        $grade_category->lib_wrapper = new mock_lib_wrapper();
        $grade_category->parent = 1;
        $grade_category->lib_wrapper->expectNever('get_record');
        $this->assertEqual(format_string($this->grade_categories[0]->fullname), $grade_category->get_name()); 

    }

    function test_grade_category_set_parent() {
        $methods_to_mock = array('is_course_category', 'update', 'fetch', 'force_regrading');
        
        Mock::generatePartial('grade_category', 'mock_grade_category_for_set_parent', $methods_to_mock);
        $grade_category_template = new mock_grade_category_for_set_parent($this);
        $grade_category_template->parent = 1;
        $grade_category_template->courseid = $this->courseid;
        
        $this->assertTrue(method_exists($grade_category_template, 'set_parent'));
        
        // Test when requested parentid is already the category's parent id (1 in this case)
        $grade_category = fullclone($grade_category_template);
        $grade_category->expectNever('is_course_category');
        $grade_category->expectNever('update');
        $grade_category->expectNever('fetch');
        $this->assertTrue($grade_category->set_parent(1));

        $grade_category_template->parent = 2;
        
        // Test when parent category is not found in DB
        $this->reset_mocks();
        $grade_category = fullclone($grade_category_template);
        $grade_category->expectOnce('is_course_category');
        $grade_category->setReturnValue('is_course_category', false);
        $obj = grade_object::get_instance('grade_category');
        $obj->expectOnce('fetch', array(array('id' => 1, 'courseid' => $this->courseid)));
        $obj->setReturnValue('fetch', false);
        $grade_category->expectNever('update');
        $grade_category->expectNever('force_regrading');
        $this->assertFalse($grade_category->set_parent(1));
    
        // Test when parent category is found in DB
        $this->reset_mocks();
        $grade_category = fullclone($grade_category_template);
        $grade_category->expectOnce('is_course_category');
        $grade_category->setReturnValue('is_course_category', false);
        $obj = grade_object::get_instance('grade_category');
        $obj->expectOnce('fetch', array(array('id' => 1, 'courseid' => $this->courseid)));
        $obj->setReturnValue('fetch', $this->grade_categories[0]); 
        $grade_category->expectOnce('force_regrading', array());
        $grade_category->expectOnce('update');
        $grade_category->setReturnValue('update', true);
        $this->assertTrue($grade_category->set_parent(1));
    }

    function test_grade_category_fetch_course_category() {
        $methods_to_mock = array('instantiate_new_grade_category', 'fetch', 'insert_course_category'); 
        Mock::generatePartial('grade_category', 'mock_grade_category_for_fetch_course_category', $methods_to_mock);
        $grade_category = new mock_grade_category_for_fetch_course_category($this);
        $grade_category->lib_wrapper = new mock_lib_wrapper();
        $this->assertTrue(method_exists($grade_category, 'fetch_course_category'));
        
        // Test method when course category already exists
        $grade_category->expectNever('instantiate_new_grade_category');
        $obj = grade_object::get_instance('grade_category');
        $obj->setReturnValue('fetch', $this->grade_categories[0]);
        $this->assertEqual($this->grade_categories[0], $grade_category->fetch_course_category($this->courseid));
        
        // Test method when course category does not exists 
    }
    

    function test_grade_category_set_locked() {
        $methods_to_mock = array('load_grade_item', 'instantiate_new_grade_item', 'fetch_all');
        
        $lockedstate = true;
        $cascade = false;
        $refresh = false;
        
        // Test non-cascading set_locked
        Mock::generatePartial('grade_category', 'mock_grade_category_for_set_locked', $methods_to_mock);
        $grade_item = new mock_grade_item();
        $grade_item->expectCallCount('set_locked', 2, array($lockedstate, $cascade, true));
        $grade_item->setReturnValue('set_locked', true); 
        $grade_item->expectNever('fetch_all');
        $grade_category = new mock_grade_category_for_set_locked($this);
        $grade_category->expectOnce('load_grade_item');
        $grade_category->expectNever('instantiate_new_grade_item');
        $this->assertTrue(method_exists($grade_category, 'set_locked'));
        $grade_category->grade_item = $grade_item;

        $this->assertTrue($grade_category->set_locked($lockedstate, $cascade, $refresh));
        
        // Test cascading set_locked
        $cascading = true;
        $grade_category = new mock_grade_category_for_set_locked($this);
        $obj = grade_object::get_instance('grade_item');
        $obj->expectOnce('fetch_all');
        $obj->setReturnValue('fetch_all', array(fullclone($grade_item), fullclone($grade_item)));
        $grade_category->expectOnce('load_grade_item');
        $grade_category->grade_item = $grade_item;

        $this->assertTrue($grade_category->set_locked($lockedstate, $cascade, $refresh));
    }

/*
    function test_grade_category_is_hidden() {
        $grade_category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($grade_category, 'is_hidden'));
        $grade_category->load_grade_item();
        $this->assertEqual($grade_category->is_hidden(), $grade_category->grade_item->is_hidden());
    }

    function test_grade_category_set_hidden() {
        $grade_category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($grade_category, 'set_hidden'));
        $grade_category->set_hidden(1);
        $grade_category->load_grade_item();
        $this->assertEqual(true, $grade_category->grade_item->is_hidden());
    }

    function generate_random_raw_grade($item, $userid) {
        $grade = new grade_grade();
        $grade->itemid = $item->id;
        $grade->userid = $userid;
        $grade->grademin = 0;
        $grade->grademax = 1;
        $valuetype = "grade$item->gradetype";
        $grade->rawgrade = rand(0, 1000) / 1000;
        $grade->insert();
        return $grade->rawgrade;
    }
    */
}
?>
