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
    
    function test_grade_tree_display_grades() {
        $tree = new grade_tree($this->courseid);
        $result_html = $tree->display_grades();

        $expected_html = '<table style="text-align: center" border="1"><tr><th colspan="3">unittestcategory1</th><td class="topfiller">&nbsp;</td><td colspan="2" class="topfiller">&nbsp;</td></tr><tr><td colspan="2">unittestcategory2</td><td colspan="1">unittestcategory3</td><td class="subfiller">&nbsp;</td><td colspan="2">level1category</td></tr><tr><td>unittestgradeitem1</td><td>unittestgradeitem2</td><td>unittestgradeitem3</td><td>unittestorphangradeitem1</td><td>singleparentitem1</td><td>singleparentitem2</td></tr></table>';
        $this->assertEqual($expected_html, $result_html);
    }

    function test_grade_tree_get_tree() {
        $tree = new grade_tree($this->courseid);
        $result_count = count($tree->tree_array, COUNT_RECURSIVE);
        $this->assertEqual(58, $result_count);
        print_object($tree);
    }

    function test_grade_tree_get_filler() {

    }

}
