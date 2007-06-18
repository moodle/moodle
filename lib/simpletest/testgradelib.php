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
 * Unit tests for (some of) ../gradelib.php.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/simpletest/fixtures/gradetest.php');

/**
 * Here is a brief explanation of the test data set up in these unit tests.
 * category1 => array(category2 => array(grade_item1, grade_item2), category3 => array(grade_item3))
 * 3 users for 3 grade_items
 */
class gradelib_test extends grade_test {

    function test_grade_get_items() {
        if (get_class($this) == 'gradelib_test') { 
            $grade_items = grade_get_items($this->courseid);

            $this->assertTrue(is_array($grade_items)); 
            $this->assertEqual(count($grade_items), 10);
        }
    }

/*
// obsolted function, should be replaced by grade_update() or removed completely
    function test_grade_create_category() {
        if (get_class($this) == 'gradelib_test') { 
            $grade_category = new stdClass();
            $grade_category->timecreated = mktime();
            $grade_category->timemodified = mktime();
        
            $items = array(new grade_item(), new grade_item());
            
            $grade_category->id = grade_create_category($this->courseid, 'unittestcategory4', $items, GRADE_AGGREGATE_MEAN);
            
            $last_grade_category = end($this->grade_categories);
            $this->assertEqual($grade_category->id, $last_grade_category->id + 1);

            $db_grade_category = get_record('grade_categories', 'id', $grade_category->id);
            $db_grade_category = new grade_category($db_grade_category);
            $db_grade_category->load_grade_item();
            $this->grade_categories[] = $db_grade_category;
            $this->grade_items[] = $db_grade_category->grade_item;
        }
    }
*/
    function test_grade_is_locked() {
        if (get_class($this) == 'gradelib_test') { 
            $grade_item = $this->grade_items[0];
            $this->assertFalse(grade_is_locked($grade_item->courseid, $grade_item->itemtype, $grade_item->itemmodule, $grade_item->iteminstance, $grade_item->itemnumber));
            $grade_item = $this->grade_items[1];
            $this->assertTrue(grade_is_locked($grade_item->courseid, $grade_item->itemtype, $grade_item->itemmodule, $grade_item->iteminstance, $grade_item->itemnumber)); 
        }
    }

    function test_grade_standardise_score() {
        $this->assertEqual(4, round(standardise_score(6, 0, 7, 0, 5)));
        $this->assertEqual(40, standardise_score(50, 30, 80, 0, 100));
    }

}

?>
