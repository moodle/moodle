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

/**
 * Unit tests for grade_category object.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */
require_once(dirname(__FILE__) . '/../../../../config.php');
global $CFG;
require_once($CFG->libdir . '/simpletest/testgradelib.php');

class grade_category_test extends gradelib_test {

    function test_grade_category_construct() {
        $params = new stdClass();

        $params->courseid = $this->courseid;
        $params->fullname = 'unittestcategory4';

        $grade_category = new grade_category($params, false);
        $grade_category->insert();

        $this->assertEqual($params->courseid, $grade_category->courseid);
        $this->assertEqual($params->fullname, $grade_category->fullname);
        $this->assertEqual(1, $grade_category->depth);
        $this->assertEqual("/$grade_category->id", $grade_category->path);
        $parentpath = $grade_category->path;

        // Test a child category
        $params->parent = $grade_category->id;
        $params->fullname = 'unittestcategory5';
        $grade_category = new grade_category($params, false);
        $grade_category->insert();
        
        $this->assertEqual(2, $grade_category->depth);
        $this->assertEqual("$parentpath/$grade_category->id", $grade_category->path); 
        $parentpath = $grade_category->path;
        
        // Test a third depth category
        $params->parent = $grade_category->id;
        $params->fullname = 'unittestcategory6';
        $grade_category = new grade_category($params, false);
        $grade_category->insert();
        $this->assertEqual(3, $grade_category->depth);
        $this->assertEqual("$parentpath/$grade_category->id", $grade_category->path); 
    }

    function test_grade_category_insert() {
        $grade_category = new grade_category();
        $this->assertTrue(method_exists($grade_category, 'insert'));
        
        $grade_category->fullname    = 'unittestcategory4';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MODE;
        $grade_category->keephigh    = 100;
        $grade_category->droplow     = 10;
        $grade_category->hidden      = 0;
        $grade_category->parent      = $this->grade_categories[0]->id;

        $grade_category->insert();

        $last_grade_category = end($this->grade_categories);
        
        $this->assertFalse(empty($grade_category->grade_item));
        $this->assertEqual($grade_category->id, $grade_category->grade_item->iteminstance);
        $this->assertEqual('category', $grade_category->grade_item->itemtype);

        $this->assertEqual($grade_category->id, $last_grade_category->id + 1);
        $this->assertFalse(empty($grade_category->timecreated));
        $this->assertFalse(empty($grade_category->timemodified));
    }

    function test_grade_category_update() {
        $grade_category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($grade_category, 'update'));
        
        $grade_category->fullname = 'Updated info for this unittest grade_category';
        $this->assertTrue($grade_category->update());
        $fullname = get_field('grade_categories', 'fullname', 'id', $this->grade_categories[0]->id);
        $this->assertEqual($grade_category->fullname, $fullname); 

    }

    function test_grade_category_delete() {
        $grade_category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($grade_category, 'delete'));
        
        $this->assertTrue($grade_category->delete());
        $this->assertFalse(get_record('grade_categories', 'id', $grade_category->id)); 
    }

    function test_grade_category_fetch() {
        $grade_category = new grade_category(); 
        $this->assertTrue(method_exists($grade_category, 'fetch'));

        $grade_category = grade_category::fetch('id', $this->grade_categories[0]->id);
        $this->assertEqual($this->grade_categories[0]->id, $grade_category->id);
        $this->assertEqual($this->grade_categories[0]->fullname, $grade_category->fullname); 
    } 

    function test_grade_category_get_children() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'get_children'));

        $children_array = $category->get_children(0);
        $this->assertTrue(is_array($children_array));
        $this->assertTrue(!empty($children_array[0]));
        $this->assertTrue(!empty($children_array[0]['object']));
        $this->assertTrue(!empty($children_array[0]['children']));
        $this->assertEqual($this->grade_categories[1]->id, $children_array[0]['object']->id);
        $this->assertEqual($this->grade_categories[2]->id, $children_array[1]['object']->id);
        $this->assertEqual($this->grade_items[0]->id, $children_array[0]['children'][0]['object']->id);
        $this->assertEqual($this->grade_items[1]->id, $children_array[0]['children'][1]['object']->id);
        $this->assertEqual($this->grade_items[2]->id, $children_array[1]['children'][0]['object']->id);

        $children_array = $category->get_children(0, 'flat');
        $this->assertEqual(5, count($children_array));
        
        $children_array = $category->get_children(1, 'flat');
        $this->assertEqual(2, count($children_array));
    }

    function test_grade_category_children_to_array() {
        $children = get_records('grade_items', 'categoryid', $this->grade_categories[1]->id);
        $children_array = grade_category::children_to_array($children, 'nested', 'grade_item');
        $this->assertTrue(is_array($children_array));
        $this->assertTrue(isset($children_array[0]));
        $this->assertTrue(isset($children_array[0]['object']));
        $this->assertEqual($this->grade_items[0]->id, $children_array[0]['object']->id); 
    }
    
    function test_grade_category_has_children() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'has_children')); 
        $this->assertTrue($category->has_children());
        $category = new grade_category();
        $this->assertFalse($category->has_children()); 
    }
    
    function test_grade_category_generate_grades() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'generate_grades'));
        $raw_grades = $category->generate_grades();
        $this->assertEqual(3, count($raw_grades));
    }

    function test_grade_category_aggregate_grades() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'aggregate_grades'));
        
        // Generate 3 random data sets
        $grade_sets = array();
        
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 200; $j++) {
                $grade_sets[$i][] = $this->generate_random_raw_grade($this->grade_items[$i], $j);
            }
        }

        $this->assertEqual(200, count($category->aggregate_grades($grade_sets)));

    }

    function generate_random_raw_grade($item, $userid) {
        $raw_grade = new grade_grades_raw();
        $raw_grade->itemid = $item->id;
        $raw_grade->userid = $userid;
        $raw_grade->grademin = $item->grademin;
        $raw_grade->grademax = $item->grademax;
        $valuetype = "grade$item->gradetype";
        $raw_grade->$valuetype = rand($raw_grade->grademin, $raw_grade->grademax);
        $raw_grade->insert();
        return $raw_grade;
    }
} 
?>
