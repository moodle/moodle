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
        $course_category = grade_category::fetch_course_category($this->courseid);

        $params = new stdClass();

        $params->courseid = $this->courseid;
        $params->fullname = 'unittestcategory4';

        $grade_category = new grade_category($params, false);
        $grade_category->insert();

        $this->assertEqual($params->courseid, $grade_category->courseid);
        $this->assertEqual($params->fullname, $grade_category->fullname);
        $this->assertEqual(2, $grade_category->depth);
        $this->assertEqual("/$course_category->id/$grade_category->id/", $grade_category->path);
        $parentpath = $grade_category->path;

        // Test a child category
        $params->parent = $grade_category->id;
        $params->fullname = 'unittestcategory5';
        $grade_category = new grade_category($params, false);
        $grade_category->insert();

        $this->assertEqual(3, $grade_category->depth);
        $this->assertEqual($parentpath.$grade_category->id."/", $grade_category->path);
        $parentpath = $grade_category->path;

        // Test a third depth category
        $params->parent = $grade_category->id;
        $params->fullname = 'unittestcategory6';
        $grade_category = new grade_category($params, false);
        $grade_category->insert();
        $this->assertEqual(4, $grade_category->depth);
        $this->assertEqual($parentpath.$grade_category->id."/", $grade_category->path);
    }

    function test_grade_category_build_path() {
        $grade_category = new grade_category($this->grade_categories[1]);
        $this->assertTrue(method_exists($grade_category, 'build_path'));
        $path = grade_category::build_path($grade_category);
        $this->assertEqual($grade_category->path, $path);
    }

    function test_grade_category_fetch() {
        $grade_category = new grade_category();
        $this->assertTrue(method_exists($grade_category, 'fetch'));

        $grade_category = grade_category::fetch(array('id'=>$this->grade_categories[0]->id));
        $this->assertEqual($this->grade_categories[0]->id, $grade_category->id);
        $this->assertEqual($this->grade_categories[0]->fullname, $grade_category->fullname);
    }

    function test_grade_category_fetch_all() {
        $grade_category = new grade_category();
        $this->assertTrue(method_exists($grade_category, 'fetch_all'));

        $grade_categories = grade_category::fetch_all(array('courseid'=>$this->courseid));
        $this->assertEqual(count($this->grade_categories), count($grade_categories)-1);
    }

    function test_grade_category_update() {
        $grade_category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($grade_category, 'update'));

        $grade_category->fullname = 'Updated info for this unittest grade_category';
        $grade_category->path = null; // path must be recalculated if missing
        $grade_category->depth = null;
        $grade_category->aggregation = GRADE_AGGREGATE_MAX; // should force regrading

        $grade_item = $grade_category->get_grade_item();
        $this->assertEqual(0, $grade_item->needsupdate);

        $this->assertTrue($grade_category->update());

        $fullname = get_field('grade_categories', 'fullname', 'id', $this->grade_categories[0]->id);
        $this->assertEqual($grade_category->fullname, $fullname);

        $path = get_field('grade_categories', 'path', 'id', $this->grade_categories[0]->id);
        $this->assertEqual($grade_category->path, $path);

        $depth = get_field('grade_categories', 'depth', 'id', $this->grade_categories[0]->id);
        $this->assertEqual($grade_category->depth, $depth);

        $grade_item = $grade_category->get_grade_item();
        $this->assertEqual(1, $grade_item->needsupdate);
    }

    function test_grade_category_delete() {
        $grade_category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($grade_category, 'delete'));

        $this->assertTrue($grade_category->delete());
        $this->assertFalse(get_record('grade_categories', 'id', $grade_category->id));
    }

    function test_grade_category_insert() {
        $course_category = grade_category::fetch_course_category($this->courseid);

        $grade_category = new grade_category();
        $this->assertTrue(method_exists($grade_category, 'insert'));

        $grade_category->fullname    = 'unittestcategory4';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 100;
        $grade_category->droplow     = 10;
        $grade_category->hidden      = 0;
        $grade_category->parent      = $this->grade_categories[0]->id;

        $grade_category->insert();

        $this->assertEqual('/'.$course_category->id.'/'.$this->grade_categories[0]->id.'/'.$grade_category->id.'/', $grade_category->path);
        $this->assertEqual(3, $grade_category->depth);

        $last_grade_category = end($this->grade_categories);

        $this->assertFalse(empty($grade_category->grade_item));
        $this->assertEqual($grade_category->id, $grade_category->grade_item->iteminstance);
        $this->assertEqual('category', $grade_category->grade_item->itemtype);

        $this->assertEqual($grade_category->id, $last_grade_category->id + 1);
        $this->assertFalse(empty($grade_category->timecreated));
        $this->assertFalse(empty($grade_category->timemodified));
    }

    function test_grade_category_insert_course_category() {
        $grade_category = new grade_category();
        $this->assertTrue(method_exists($grade_category, 'insert_course_category'));

        $id = $grade_category->insert_course_category($this->courseid);
        $this->assertNotNull($id);
        $this->assertEqual('?', $grade_category->fullname);
        $this->assertEqual(GRADE_AGGREGATE_WEIGHTED_MEAN2, $grade_category->aggregation);
        $this->assertEqual("/$id/", $grade_category->path);
        $this->assertEqual(1, $grade_category->depth);
        $this->assertNull($grade_category->parent);
    }

    function test_grade_category_qualifies_for_regrading() {
        $grade_category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($grade_category, 'qualifies_for_regrading'));

        $this->assertFalse($grade_category->qualifies_for_regrading());

        $grade_category->aggregation = GRADE_AGGREGATE_MAX;
        $this->assertTrue($grade_category->qualifies_for_regrading());

        $grade_category = new grade_category($this->grade_categories[0]);
        $grade_category->droplow = 99;
        $this->assertTrue($grade_category->qualifies_for_regrading());

        $grade_category = new grade_category($this->grade_categories[0]);
        $grade_category->keephigh = 99;
        $this->assertTrue($grade_category->qualifies_for_regrading());
    }

    function test_grade_category_force_regrading() {
        $grade_category = new grade_category($this->grade_categories[1]);
        $this->assertTrue(method_exists($grade_category, 'force_regrading'));

        $grade_category->load_grade_item();
        $this->assertEqual(0, $grade_category->grade_item->needsupdate);

        $grade_category->force_regrading();

        $grade_category->grade_item = null;
        $grade_category->load_grade_item();

        $this->assertEqual(1, $grade_category->grade_item->needsupdate);
    }

    /*
     * I am disabling this test until we implement proper mock objects. This is meant
     * to be a private method called from within a grade_item method.

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

    */

    function test_grade_category_aggregate_grades() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'aggregate_grades'));
        // tested above in test_grade_category_generate_grades()
    }

    function test_grade_category_apply_limit_rules() {
        $items[$this->grade_items[0]->id] = new grade_item($this->grade_items[0], false);
        $items[$this->grade_items[1]->id] = new grade_item($this->grade_items[1], false);
        $items[$this->grade_items[2]->id] = new grade_item($this->grade_items[2], false);
        $items[$this->grade_items[4]->id] = new grade_item($this->grade_items[4], false);

        $category = new grade_category();
        $category->droplow = 2;
        $grades = array($this->grade_items[0]->id=>5.374,
                        $this->grade_items[1]->id=>9.4743,
                        $this->grade_items[2]->id=>2.5474,
                        $this->grade_items[4]->id=>7.3754);
        $category->apply_limit_rules($grades, $items);
        $this->assertEqual(count($grades), 2);
        $this->assertEqual($grades[$this->grade_items[1]->id], 9.4743);
        $this->assertEqual($grades[$this->grade_items[4]->id], 7.3754);

        $category = new grade_category();
        $category->keephigh = 1;
        $category->droplow = 0;
        $grades = array($this->grade_items[0]->id=>5.374,
                        $this->grade_items[1]->id=>9.4743,
                        $this->grade_items[2]->id=>2.5474,
                        $this->grade_items[4]->id=>7.3754);
        $category->apply_limit_rules($grades, $items);
        $this->assertEqual(count($grades), 1);
        $grade = reset($grades);
        $this->assertEqual(9.4743, $grade);

        $category = new grade_category();
        $category->droplow     = 2;
        $category->aggregation = GRADE_AGGREGATE_SUM;
        $items[$this->grade_items[2]->id]->aggregationcoef = 1;
        $grades = array($this->grade_items[0]->id=>5.374,
                        $this->grade_items[1]->id=>9.4743,
                        $this->grade_items[2]->id=>2.5474,
                        $this->grade_items[4]->id=>7.3754);

        $category->apply_limit_rules($grades, $items);
        $this->assertEqual(count($grades), 2);
        $this->assertEqual($grades[$this->grade_items[1]->id], 9.4743);
        $this->assertEqual($grades[$this->grade_items[2]->id], 2.5474);

        $category = new grade_category();
        $category->keephigh = 1;
        $category->droplow = 0;
        $category->aggregation = GRADE_AGGREGATE_SUM;
        $items[$this->grade_items[2]->id]->aggregationcoef = 1;
        $grades = array($this->grade_items[0]->id=>5.374,
                        $this->grade_items[1]->id=>9.4743,
                        $this->grade_items[2]->id=>2.5474,
                        $this->grade_items[4]->id=>7.3754);
        $category->apply_limit_rules($grades, $items);
        $this->assertEqual(count($grades), 2);
        $this->assertEqual($grades[$this->grade_items[1]->id], 9.4743);
        $this->assertEqual($grades[$this->grade_items[2]->id], 2.5474);
    }

    /**
     * TODO implement
     */
    function test_grade_category_is_aggregationcoef_used() {

    }

    function test_grade_category_fetch_course_tree() {
        $category = new grade_category();
        $this->assertTrue(method_exists($category, 'fetch_course_tree'));
        //TODO: add some tests
    }

    function test_grade_category_get_children() {
        $course_category = grade_category::fetch_course_category($this->courseid);

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
    }

    function test_grade_category_load_grade_item() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'load_grade_item'));
        $this->assertEqual(null, $category->grade_item);
        $category->load_grade_item();
        $this->assertEqual($this->grade_items[3]->id, $category->grade_item->id);
    }

    function test_grade_category_get_grade_item() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'get_grade_item'));
        $grade_item = $category->get_grade_item();
        $this->assertEqual($this->grade_items[3]->id, $grade_item->id);
    }

    function test_grade_category_load_parent_category() {
        $category = new grade_category($this->grade_categories[1]);
        $this->assertTrue(method_exists($category, 'load_parent_category'));
        $this->assertEqual(null, $category->parent_category);
        $category->load_parent_category();
        $this->assertEqual($this->grade_categories[0]->id, $category->parent_category->id);
    }

    function test_grade_category_get_parent_category() {
        $category = new grade_category($this->grade_categories[1]);
        $this->assertTrue(method_exists($category, 'get_parent_category'));
        $parent_category = $category->get_parent_category();
        $this->assertEqual($this->grade_categories[0]->id, $parent_category->id);
    }

    function test_grade_category_get_name() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'get_name'));
        $this->assertEqual($this->grade_categories[0]->fullname, $category->get_name());
    }

    function test_grade_category_set_parent() {
        $category = new grade_category($this->grade_categories[1]);
        $this->assertTrue(method_exists($category, 'set_parent'));
        // TODO: implement detailed tests

        $course_category = grade_category::fetch_course_category($this->courseid);
        $this->assertTrue($category->set_parent($course_category->id));
        $this->assertEqual($course_category->id, $category->parent);
    }

    function test_grade_category_get_final() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'get_final'));
        $category->load_grade_item();
        $this->assertEqual($category->get_final(), $category->grade_item->get_final());
    }

    function test_grade_category_get_sortorder() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'get_sortorder'));
        $category->load_grade_item();
        $this->assertEqual($category->get_sortorder(), $category->grade_item->get_sortorder());
    }

    function test_grade_category_set_sortorder() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'set_sortorder'));
        $category->load_grade_item();
        $this->assertEqual($category->set_sortorder(10), $category->grade_item->set_sortorder(10));
    }

    function test_grade_category_move_after_sortorder() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'move_after_sortorder'));
        $category->load_grade_item();
        $this->assertEqual($category->move_after_sortorder(10), $category->grade_item->move_after_sortorder(10));
    }

    function test_grade_category_is_course_category() {
        $category = grade_category::fetch_course_category($this->courseid);
        $this->assertTrue(method_exists($category, 'is_course_category'));
        $this->assertTrue($category->is_course_category());
    }

    function test_grade_category_fetch_course_category() {
        $category = new grade_category();
        $this->assertTrue(method_exists($category, 'fetch_course_category'));
        $category = grade_category::fetch_course_category($this->courseid);
        $this->assertTrue(empty($category->parent));
    }
    /**
     * TODO implement
     */
    function test_grade_category_is_editable() {

    }

    function test_grade_category_is_locked() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'is_locked'));
        $category->load_grade_item();
        $this->assertEqual($category->is_locked(), $category->grade_item->is_locked());
    }

    function test_grade_category_set_locked() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'set_locked'));
        $this->assertTrue($category->set_locked(1));
    }

    function test_grade_category_is_hidden() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'is_hidden'));
        $category->load_grade_item();
        $this->assertEqual($category->is_hidden(), $category->grade_item->is_hidden());
    }

    function test_grade_category_set_hidden() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'set_hidden'));
        $category->set_hidden(1);
        $category->load_grade_item();
        $this->assertEqual(true, $category->grade_item->is_hidden());
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
}
?>
