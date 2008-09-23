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

class questionlib_test extends MoodleUnitTestCase {


    function setUp() {
    }

    function tearDown() {
    }

    function test_question_state_is_closed() {
        $state = new object();
        $state->event = QUESTION_EVENTOPEN;
        $this->assertFalse(question_state_is_closed($state));

        $state->event = QUESTION_EVENTNAVIGATE;
        $this->assertFalse(question_state_is_closed($state));

        $state->event = QUESTION_EVENTSAVE;
        $this->assertFalse(question_state_is_closed($state));

        $state->event = QUESTION_EVENTGRADE;
        $this->assertFalse(question_state_is_closed($state));

        $state->event = QUESTION_EVENTDUPLICATE;
        $this->assertFalse(question_state_is_closed($state));

        $state->event = QUESTION_EVENTVALIDATE;
        $this->assertFalse(question_state_is_closed($state));

        $state->event = QUESTION_EVENTSUBMIT;
        $this->assertFalse(question_state_is_closed($state));

        $state->event = QUESTION_EVENTCLOSEANDGRADE;
        $this->assertTrue(question_state_is_closed($state));

        $state->event = QUESTION_EVENTCLOSE;
        $this->assertTrue(question_state_is_closed($state));

        $state->event = QUESTION_EVENTMANUALGRADE;
        $this->assertTrue(question_state_is_closed($state));

    }
    function test_question_state_is_graded() {
        $state = new object();
        $state->event = QUESTION_EVENTOPEN;
        $this->assertFalse(question_state_is_graded($state));

        $state->event = QUESTION_EVENTNAVIGATE;
        $this->assertFalse(question_state_is_graded($state));

        $state->event = QUESTION_EVENTSAVE;
        $this->assertFalse(question_state_is_graded($state));

        $state->event = QUESTION_EVENTDUPLICATE;
        $this->assertFalse(question_state_is_graded($state));

        $state->event = QUESTION_EVENTVALIDATE;
        $this->assertFalse(question_state_is_graded($state));

        $state->event = QUESTION_EVENTSUBMIT;
        $this->assertFalse(question_state_is_graded($state));

        $state->event = QUESTION_EVENTCLOSE;
        $this->assertFalse(question_state_is_graded($state));

        $state->event = QUESTION_EVENTCLOSEANDGRADE;
        $this->assertTrue(question_state_is_graded($state));

        $state->event = QUESTION_EVENTMANUALGRADE;
        $this->assertTrue(question_state_is_graded($state));

        $state->event = QUESTION_EVENTGRADE;
        $this->assertTrue(question_state_is_graded($state));

    }
}

?>
