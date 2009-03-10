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
 * Unit tests for (some of) ../ajaxlib.php.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/ajax/ajaxlib.php');

/**
 * Unit tests of mathslib wrapper and underlying EvalMath library.
 *
 * @author Petr Skoda (skodak)
 * @version $Id$
 */
class ajax_test extends UnitTestCase {
    function test_ajax_get_lib() {
        global $CFG;
        $olddebug = $CFG->debug;
        $CFG->debug = DEBUG_DEVELOPER;
        $this->assertEqual(ajax_get_lib('yui_yahoo'), $CFG->wwwroot . '/lib/yui/yahoo/yahoo.js');
        $CFG->debug = DEBUG_MINIMAL;
        $this->assertEqual(ajax_get_lib('yui_yahoo'), $CFG->wwwroot . '/lib/yui/yahoo/yahoo-min.js');
        $CFG->debug = $olddebug;
        $this->assertEqual(ajax_get_lib('lib/javascript-static.js'), $CFG->wwwroot . '/lib/javascript-static.js');
        $this->assertEqual(ajax_get_lib($CFG->wwwroot . '/lib/javascript-static.js'), $CFG->wwwroot . '/lib/javascript-static.js');
        $this->expectException();
        ajax_get_lib('a_file_that_does_not_exist.js');
    }
}

?>