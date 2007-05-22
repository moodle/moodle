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
    
    function test_grade_tree_locate_element() {
        $tree = new grade_tree($this->courseid);
        $element = $tree->locate_element(5);
        $this->assertEqual('1/5', $element->index);
        $this->assertNotNull($element->element);
        $this->assertEqual('unittestcategory3', $element->element['object']->fullname);
        $this->assertEqual('unittestgradeitem3', $element->element['children'][6]['object']->itemname);

        // Locate a grade_item
        $element = $tree->locate_element(9);
        $this->assertEqual('8/9', $element->index);
        $this->assertNotNull($element->element);
        $this->assertEqual('singleparentitem1', $element->element['object']->itemname); 
    }

    function test_grade_tree_insert_grade_subcategory() {
        $tree = new grade_tree($this->courseid);
        $grade_category = new grade_category($this->grade_categories[3]);
        $element = array('object' => $grade_category);
        
        $tree->insert_element($element, 5);
        $this->assertFalse(empty($tree->tree_array[1]['children'][1]['object']->fullname));
        $this->assertEqual($this->grade_categories[3]->fullname, $tree->tree_array[1]['children'][1]['object']->fullname);
        $this->assertFalse(empty($tree->tree_array[1]['children'][1]['children'][9]));
        $this->assertEqual($this->grade_items[7]->itemname, $tree->tree_array[1]['children'][1]['children'][9]['object']->itemname);
    }

    function test_grade_tree_insert_grade_topcategory() {
        $tree = new grade_tree($this->courseid);
        $grade_category = new grade_category($this->grade_categories[0]);
        $element = array('object' => $grade_category);
        
        $tree->insert_element($element, 8);

        $this->assertFalse(empty($tree->tree_array[2]['object']->fullname));
        $this->assertEqual($this->grade_categories[0]->fullname, $tree->tree_array[2]['object']->fullname);
        $this->assertFalse(empty($tree->tree_array[2]['children'][2]['object']->fullname));
        $this->assertEqual($this->grade_categories[1]->fullname, $tree->tree_array[2]['children'][2]['object']->fullname);
    }
    
    function test_grade_tree_insert_grade_item() {
        $tree = new grade_tree($this->courseid);
        $grade_item = new grade_item($this->grade_items[2]);
        $element = array('object' => $grade_item);
        $tree->insert_element($element, 4);
        $this->assertFalse(empty($tree->tree_array[1]['children'][2]['children'][1]['object']->itemname));
        $this->assertEqual($this->grade_items[2]->itemname, $tree->tree_array[1]['children'][2]['children'][1]['object']->itemname);
        $this->assertFalse(empty($tree->tree_array[1]['children'][2]['children'][1]['final_grades'][1]));
        $this->assertEqual($this->grade_grades_final[6]->gradevalue, $tree->tree_array[1]['children'][2]['children'][1]['final_grades'][1]->gradevalue);
        
        // Check the need_insert array
        $this->assertEqual(1, count($tree->need_insert));
    }

    function test_grade_tree_move_element() {
        $tree = new grade_tree($this->courseid);
        
        $tree->move_element(4, 10);
        $this->assertFalse(empty($tree->tree_array[8]['children'][1]));
        $this->assertEqual('unittestgradeitem2', $tree->tree_array[8]['children'][1]['object']->itemname);
        $tree->renumber();

        // Check need_? fields
        $this->assertFalse(empty($tree->need_update));
        $this->assertFalse(empty($tree->need_insert));
        $this->assertFalse(empty($tree->need_delete));
        $this->assertEqual(6, count($tree->need_update));
        $this->assertEqual(1, count($tree->need_delete));
        $this->assertEqual(1, count($tree->need_insert));
        $this->assertEqual($this->grade_items[1]->itemname, $tree->need_delete[$this->grade_items[1]->id]->itemname);
        $this->assertEqual($this->grade_items[1]->itemname, $tree->need_insert[$this->grade_items[1]->id]->itemname);

        $this->assertFalse(empty($tree->tree_array[1]['children'][4]['children'][5]));
        $this->assertEqual('unittestgradeitem3', $tree->tree_array[1]['children'][4]['children'][5]['object']->itemname);
        
        $tree->move_element(6, 3, 'after');
        $this->assertFalse(empty($tree->tree_array[1]['children'][2]['children'][1]));
        $this->assertEqual('unittestorphangradeitem1', $tree->tree_array[1]['children'][2]['children'][1]['object']->itemname);
        $tree->renumber();
        $this->assertFalse(empty($tree->tree_array[1]['children'][2]['children'][4]));
        $this->assertEqual('unittestorphangradeitem1', $tree->tree_array[1]['children'][2]['children'][4]['object']->itemname);

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
        $this->assertEqual('level1category', $tree->tree_array[1]['children'][1]['object']->fullname);
        $tree->renumber();
        $this->assertFalse(empty($tree->tree_array[1]['children'][5]));
        $this->assertEqual('level1category', $tree->tree_array[1]['children'][5]['object']->fullname);
        $this->assertEqual('singleparentitem1', $tree->tree_array[1]['children'][5]['children'][6]['object']->itemname);

        // Try moving a top category
        $tree = new grade_tree($this->courseid);
        $tree->move_element(1, 8);
        $this->assertFalse(empty($tree->tree_array[1]));
        $this->assertEqual('unittestcategory1', $tree->tree_array[1]['object']->fullname);
        $tree->renumber();
        $this->assertFalse(empty($tree->tree_array[2]));
        $this->assertEqual('unittestcategory1', $tree->tree_array[2]['object']->fullname);
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
        $this->assertEqual(44, count($tree->tree_array, COUNT_RECURSIVE));
    }
    
    function test_grade_tree_renumber() {
        $tree = new grade_tree($this->courseid);
        $tree1 = $tree;
        $tree->renumber();
        $this->assertEqual($tree1->tree_array[1]['object'], $tree->tree_array[1]['object']);
        $this->assertTrue(empty($tree->need_update));
    }

    function test_grade_tree_remove_element() {
        $tree = new grade_tree($this->courseid);

        // Removing the orphan grade_item
        $tree->remove_element(7);
        $this->assertTrue(empty($tree->tree_array[7]));
        $this->assertFalse(empty($tree->tree_array[1]));
        $this->assertFalse(empty($tree->tree_array[8]));
        $tree->renumber();
        $this->assertFalse(empty($tree->tree_array[7]));
        $this->assertFalse(empty($tree->tree_array[1]));
        $this->assertTrue(empty($tree->tree_array[8]));
        
        // Removing a grade_item with only 1 parent
        $tree->remove_element(8);
        $this->assertTrue(empty($tree->tree_array[7]['children'][8]));
        $this->assertFalse(empty($tree->tree_array[7]['children'][9]));
        $tree->renumber();
        $this->assertFalse(empty($tree->tree_array[7]['children'][8]));
        $this->assertTrue(empty($tree->tree_array[7]['children'][9]));

        // Now remove this sub-category (the one without a topcat)
        $tree->remove_element(7);
        $this->assertTrue(empty($tree->tree_array[7]));
        
        // At this point we're left with a topcat, 2 subcats and 3 items, so try removing an item first
        $tree->remove_element(4);
        $this->assertTrue(empty($tree->tree_array[1]['children'][2]['children'][4]));
        $this->assertFalse(empty($tree->tree_array[1]['children'][5]));
        $tree->renumber();
        $this->assertFalse(empty($tree->tree_array[1]['children'][4]));

        // Now remove a subcat sandwiched between a topcat and its items
        $tree->remove_element(4);
        $this->assertTrue(empty($tree->tree_array[1]['children'][4]));
        $tree->renumber();
        $this->assertTrue(empty($tree->tree_array[1]['children'][4])); 
        
        $this->assertEqual(12, count($tree->tree_array, COUNT_RECURSIVE));
        
        // Check the need_delete array
        $this->assertEqual(5, count($tree->need_delete));
    }

    function test_grade_tree_get_filler() {
        $tree = new grade_tree($this->courseid);
        $filler = $tree->get_filler($tree->tree_array[7]['object']);
        $this->assertEqual('filler', $filler['object']);
        $this->assertEqual('filler', $filler['children'][0]['object']);
        $this->assertEqual($this->grade_items[6]->itemname, $filler['children'][0]['children'][0]['object']->itemname);
    } 

    function test_grade_tree_build_tree_filled() {
        $tree = new grade_tree($this->courseid);

        $element = $tree->tree_array[7];
        $tree->remove_element(7);
        $tree->renumber();

        $tree->insert_element($element, 4);
        $tree->renumber();
    }

    function test_grade_tree_update_db() {
        $tree = new grade_tree($this->courseid);
        $tree->remove_element(7);
        $tree->renumber();
        $tree->update_db();
        $item = grade_item::fetch('id', $this->grade_items[6]->id);
        $this->assertTrue(empty($item->id));
        
        $tree->move_element(4, 9);
        $tree->renumber();
        $tree->update_db();
        $item = grade_item::fetch('id', $this->grade_items[1]->id);
        $this->assertFalse(empty($item->id));
        $this->assertEqual(8, $item->sortorder);
        
        $grade_item = new grade_item($this->grade_items[2]);
        $element = array('object' => $grade_item);
        $tree->insert_element($element, 9);

    } 
}
