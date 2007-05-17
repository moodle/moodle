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
 * Unit tests for grade_tree object.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */
require_once(dirname(__FILE__) . '/../../../../config.php');
global $CFG;
require_once($CFG->libdir . '/simpletest/testgradelib.php');

class grade_tree_test extends gradelib_test {
    
    function test_grade_tree_move_element() {
        $tree = new grade_tree($this->courseid);
        
        $tree->move_element(4, 9);
        $this->assertFalse(empty($tree->tree_array[8]['children'][1]));
        $this->assertEqual('unittestgradeitem2', $tree->tree_array[8]['children'][1]['object']->itemname);
        $tree->renumber();
        $this->assertFalse(empty($tree->tree_array[1]['children'][4]['children'][5]));
        $this->assertEqual('unittestgradeitem2', $tree->tree_array[1]['children'][4]['children'][5]['object']->itemname);
        
        $tree->move_element(6, 3, 'after');
        $this->assertFalse(empty($tree->tree_array[1]['children'][2]['children'][1]));
        $this->assertEqual('unittestgradeitem3', $tree->tree_array[1]['children'][2]['children'][1]['object']->itemname);
        $tree->renumber();
        $this->assertFalse(empty($tree->tree_array[1]['children'][2]['children'][4]));
        $this->assertEqual('unittestgradeitem3', $tree->tree_array[1]['children'][2]['children'][4]['object']->itemname);

        // Try moving a subcategory
        $tree->move_element(2, 5, 'after');
        $this->assertFalse(empty($tree->tree_array[1]['children'][1]));
        $this->assertEqual('unittestcategory2', $tree->tree_array[1]['children'][1]['object']->fullname);
        $tree->renumber();
        $this->assertFalse(empty($tree->tree_array[1]['children'][4]));
        $this->assertEqual('unittestcategory2', $tree->tree_array[1]['children'][4]['object']->fullname);

        // Try moving a subcategory
        $tree = new grade_tree($this->courseid);
        $original_count = count($tree->tree_array, COUNT_RECURSIVE);
        $tree->move_element(8, 5);
        $new_count = count($tree->tree_array, COUNT_RECURSIVE);
        $this->assertEqual($original_count, $new_count);
        $this->assertFalse(empty($tree->tree_array[1]['children'][1]));
        $this->assertEqual('unittestcategory2', $tree->tree_array[1]['children'][1]['object']->fullname);
        $tree->renumber();
        $this->assertFalse(empty($tree->tree_array[1]['children'][5]));
        $this->assertEqual('unittestcategory2', $tree->tree_array[1]['children'][5]['object']->fullname);
        $this->assertEqual('unittestcategory2', $tree->tree_array[1]['children'][5]['children'][6]->itemname);
    }

    
    function test_grade_tree_constructor() {
        $tree = new grade_tree($this->courseid);

    }

    function test_grade_tree_display_grades() {
        $tree = new grade_tree($this->courseid);
        $result_html = $tree->display_grades();

        $expected_html = '<table style="text-align: center" border="1"><tr><th colspan="3">unittestcategory1</th><td class="topfiller">&nbsp;</td><td colspan="2" class="topfiller">&nbsp;</td></tr><tr><td colspan="2">unittestcategory2</td><td colspan="1">unittestcategory3</td><td class="subfiller">&nbsp;</td><td colspan="2">level1category</td></tr><tr><td>unittestgradeitem1</td><td>unittestgradeitem2</td><td>unittestgradeitem3</td><td>unittestorphangradeitem1</td><td>singleparentitem1</td><td>singleparentitem2</td></tr></table>';
        $this->assertEqual($expected_html, $result_html);
    }

    function test_grade_tree_get_tree() {
        $tree = new grade_tree($this->courseid);
        $this->assertEqual(58, count($tree->tree_filled, COUNT_RECURSIVE));
        $this->assertEqual(29, count($tree->tree_array, COUNT_RECURSIVE));
    }
    
    function test_grade_tree_locate_element() {
        $tree = new grade_tree($this->courseid);
        $element = $tree->locate_element(5);
        $this->assertEqual(1, $element->topcatindex);
        $this->assertEqual(5, $element->subcatindex);
        $this->assertTrue(empty($element->itemindex));
        $this->assertNotNull($element->element);
        $this->assertEqual('unittestcategory3', $element->element['object']->fullname);
        $this->assertEqual('unittestgradeitem3', $element->element['children'][6]['object']->itemname);
    }

    function test_grade_tree_renumber() {
        $tree = new grade_tree($this->courseid);
        $tree->renumber();

    }

    function test_grade_tree_insert_element() {
        $tree = new grade_tree($this->courseid);

    }

    function test_grade_tree_remove_element() {
        $tree = new grade_tree($this->courseid);

    }

    function test_grade_tree_get_filler() {
        $tree = new grade_tree($this->courseid);

    } 
}
