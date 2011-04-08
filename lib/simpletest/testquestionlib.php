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
 * Unit tests for (some of) ../questionlib.php.
 *
 * @copyright &copy; 2006 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/questionlib.php');

class questionlib_test extends UnitTestCase {

    public static $includecoverage = array('lib/questionlib.php');

    function test_question_reorder_qtypes() {
        $this->assertEqual(question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 't1', +1),
                array(0 => 't2', 1 => 't1', 2 => 't3'));
        $this->assertEqual(question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 't1', -1),
                array(0 => 't1', 1 => 't2', 2 => 't3'));
        $this->assertEqual(question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 't2', -1),
                array(0 => 't2', 1 => 't1', 2 => 't3'));
        $this->assertEqual(question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 't3', +1),
                array(0 => 't1', 1 => 't2', 2 => 't3'));
        $this->assertEqual(question_reorder_qtypes(array('t1' => '', 't2' => '', 't3' => ''), 'missing', +1),
                array(0 => 't1', 1 => 't2', 2 => 't3'));
    }

}


