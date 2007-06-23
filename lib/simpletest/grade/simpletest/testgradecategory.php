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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/simpletest/fixtures/gradetest.php');

class grade_category_test extends grade_test {

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
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN_GRADED;
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
        $this->assertFalse(empty($children_array[2]));
        $this->assertFalse(empty($children_array[2]['object']));
        $this->assertFalse(empty($children_array[2]['children']));
        $this->assertEqual($this->grade_categories[1]->id, $children_array[2]['object']->id);
        $this->assertEqual($this->grade_categories[2]->id, $children_array[5]['object']->id);
        $this->assertEqual($this->grade_items[0]->id, $children_array[2]['children'][3]['object']->id);
        $this->assertEqual($this->grade_items[1]->id, $children_array[2]['children'][4]['object']->id);
        $this->assertEqual($this->grade_items[2]->id, $children_array[5]['children'][6]['object']->id);

        $children_array = $category->get_children(0, 'flat');
        $this->assertEqual(5, count($children_array));

        $children_array = $category->get_children(1, 'flat');
        $this->assertEqual(2, count($children_array));
    }

    function test_grade_category_children_to_array() {
        $children = get_records('grade_items', 'categoryid', $this->grade_categories[1]->id);
        $children_array = grade_category::children_to_array($children, 'nested', 'grade_item');
        $this->assertTrue(is_array($children_array));
        $this->assertTrue(isset($children_array[3]));
        $this->assertTrue(isset($children_array[3]['object']));
        $this->assertEqual($this->grade_items[0]->id, $children_array[3]['object']->id);
    }

    function test_grade_category_has_children() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'has_children'));
        $this->assertTrue($category->has_children());
        $category = new grade_category();
        $this->assertFalse($category->has_children());
    }

    function test_grade_category_generate_grades() {
        $category = new grade_category($this->grade_categories[3]);
        $this->assertTrue(method_exists($category, 'generate_grades'));
        $category->load_grade_item();

        $grades = get_records('grade_grades', 'itemid', $category->grade_item->id);
        $this->assertFalse($grades);

        $category->generate_grades();
        $grades = get_records('grade_grades', 'itemid', $category->grade_item->id);
        $this->assertEqual(3, count($grades));

        $rawvalues = array();
        foreach ($grades as $grade) {
            $this->assertWithinMargin($grade->rawgrade, $grade->rawgrademin, $grade->rawgrademax);
            $rawvalues[] = (int)$grade->rawgrade;
        }
        sort($rawvalues);
        // calculated mean results
        $this->assertEqual($rawvalues, array(20,50,100));
    }

    function test_grade_category_aggregate_grades() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'aggregate_grades'));
        // tested above in test_grade_category_generate_grades()
    }

    function generate_random_raw_grade($item, $userid) {
        $grade = new grade_grades();
        $grade->itemid = $item->id;
        $grade->userid = $userid;
        $grade->grademin = 0;
        $grade->grademax = 1;
        $valuetype = "grade$item->gradetype";
        $grade->rawgrade = rand(0, 1000) / 1000;
        $grade->insert();
        return $grade->rawgrade;
    }

    function test_grade_category_set_as_parent() {
        global $CFG;
        $debuglevel = $CFG->debug;

        // There are 3 constraints which, if violated, should return false and trigger a debugging message. Test each of them
        $grade_category = new grade_category();
        $grade_category->fullname    = 'new topcategory';
        $grade_category->courseid    = $this->courseid;
        $grade_category->insert();

        // 1. mixed types of children
        $child1 = new grade_item();
        $child1->sortorder = 1;
        $child2 = new grade_category();
        $child2->grade_item = new grade_item();
        $child2->grade_item->sortorder = 2;
        $CFG->debug = 2;
        $this->assertFalse($grade_category->set_as_parent(array($child1, $child2)));
        $CFG->debug = $debuglevel;

        // 2. Child is a top category
        $child1 = new grade_category($this->grade_categories[0]);
        $CFG->debug = 2;
        $this->assertFalse($grade_category->set_as_parent(array($child1)));
        $CFG->debug = $debuglevel;

        // 3. Children belong to different courses
        $child1 = new grade_item($this->grade_items[0]);
        $child2 = new grade_item($this->grade_items[1]);
        $child2->courseid = 543;
        $CFG->debug = 2;
        $this->assertFalse($grade_category->set_as_parent(array($child1, $child2)));
        $CFG->debug = $debuglevel;

        // Now test setting parent correctly
        $child1 = new grade_item();
        $child2 = new grade_item();
        $child1->itemname = 'new grade_item';
        $child2->itemname = 'new grade_item';
        $child1->sortorder = 1;
        $child2->sortorder = 2;
        $child1->courseid = $grade_category->courseid;
        $child2->courseid = $grade_category->courseid;
        $child1->insert();
        $child2->insert();
        $this->assertTrue($grade_category->set_as_parent(array($child1, $child2)));
    }

    function test_grade_category_apply_limit_rules() {
        $category = new grade_category();
        $grades = array(5.374, 9.4743, 2.5474, 7.3754);

        $category->droplow = 2;
        $category->apply_limit_rules($grades);
        sort($grades, SORT_NUMERIC);
        $this->assertEqual(array(7.3754, 9.4743), $grades);

        $category = new grade_category();
        $grades = array(5.374, 9.4743, 2.5474, 7.3754);

        $category->keephigh = 1;
        $category->droplow = 0;
        $category->apply_limit_rules($grades);
        $this->assertEqual(array(9.4743), $grades);
    }
}
?>
