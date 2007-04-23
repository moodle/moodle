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

/** $Id */
require_once(dirname(__FILE__) . '/../../config.php');

global $CFG;
require_once($CFG->libdir . '/simpletestlib.php');
require_once($CFG->libdir . '/gradelib.php');

class gradelib_test extends UnitTestCase {
    
    /**
     * Create temporary entries in the database for these tests.
     */
    function setUp() 
    {
    
    }

    /**
     * Delete temporary entries from the database
     */
    function tearDown() 
    {
    
    }

    function test_grade_get_items()
    {
        $courseid = 1;
        $itemmname = 'grade_item_1';
        $itemtype = 'mod';
        $itemmodule = 'quiz';
        
        $grade_items = grade_get_items($courseid, $itemname, $itemtype, $itemmodule);

        $this->assertTrue(is_array($grade_items)); 
        $this->assertEqual(count($grade_items), 4);
    }

    function test_grade_create_item()
    {

    }

    function test_grade_create_category()
    {

    }

    function test_grade_is_locked()
    {

    }
}

?>
