<?php

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
 * Unit tests for Course lib.
 *
 * @author nicolasconnault@gmail.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/course/lib.php');

class courselib_test extends UnitTestCase {
    var $realDB;
    public  static $includecoverage = array('course/lib.php');

    function setUp() {
        global $DB;
        Mock::generate(get_class($DB), 'mockDB');
        $this->realDB = $DB;
        $DB = new mockDB();
    }

    function tearDown() {
        global $DB;
        $DB = $this->realDB;
    }

    function testMoveSection() {
        global $DB;
        $course = new stdClass();
        $course->numsections = 10;
        $course->id = 1;
        $sections = array(20 => 0, 21 => 1, 22 => 2, 23 => 3, 24 => 4, 25 => 5);

        $DB->setReturnValueAt(0, 'get_records_menu', $sections);
        $DB->expectAt(0, 'set_field', array('course_sections', 'section', 0, array('id' => 20)));
        $DB->expectAt(1, 'set_field', array('course_sections', 'section', 1, array('id' => 21)));
        $DB->expectAt(2, 'set_field', array('course_sections', 'section', 2, array('id' => 23)));
        $DB->expectAt(3, 'set_field', array('course_sections', 'section', 3, array('id' => 24)));
        $DB->expectAt(4, 'set_field', array('course_sections', 'section', 4, array('id' => 22)));
        $DB->expectAt(5, 'set_field', array('course_sections', 'section', 5, array('id' => 25)));
        move_section_to($course, 2, 4);

        $DB->setReturnValueAt(1, 'get_records_menu', $sections);
        $DB->expectAt(6, 'set_field', array('course_sections', 'section', 0, array('id' => 20)));
        $DB->expectAt(7, 'set_field', array('course_sections', 'section', 1, array('id' => 24)));
        $DB->expectAt(8, 'set_field', array('course_sections', 'section', 2, array('id' => 21)));
        $DB->expectAt(9, 'set_field', array('course_sections', 'section', 3, array('id' => 22)));
        $DB->expectAt(10, 'set_field', array('course_sections', 'section', 4, array('id' => 23)));
        $DB->expectAt(11, 'set_field', array('course_sections', 'section', 5, array('id' => 25)));
        move_section_to($course, 4, 0);
    }

    function testReorderSections() {
        $sections = array(20 => 0, 21 => 1, 22 => 2, 23 => 3, 24 => 4, 25 => 5);
        $this->assertFalse(reorder_sections(1,3,4));

        $newsections = reorder_sections($sections, 2, 4);
        $newsections_flipped = array_flip($newsections);

        $this->assertEqual(20, reset($newsections_flipped));
        $this->assertEqual(21, next($newsections_flipped));
        $this->assertEqual(23, next($newsections_flipped));
        $this->assertEqual(24, next($newsections_flipped));
        $this->assertEqual(22, next($newsections_flipped));
        $this->assertEqual(25, next($newsections_flipped));

        $newsections = reorder_sections($sections, 4, 0);
        $newsections_flipped = array_flip($newsections);

        $this->assertEqual(20, reset($newsections_flipped));
        $this->assertEqual(24, next($newsections_flipped));
        $this->assertEqual(21, next($newsections_flipped));
        $this->assertEqual(22, next($newsections_flipped));
        $this->assertEqual(23, next($newsections_flipped));
        $this->assertEqual(25, next($newsections_flipped));

        $newsections = reorder_sections($sections, 1, 5);
        $newsections_flipped = array_flip($newsections);

        $this->assertEqual(20, reset($newsections_flipped));
        $this->assertEqual(22, next($newsections_flipped));
        $this->assertEqual(23, next($newsections_flipped));
        $this->assertEqual(24, next($newsections_flipped));
        $this->assertEqual(25, next($newsections_flipped));
        $this->assertEqual(21, next($newsections_flipped));
    }
}
