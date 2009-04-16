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
 * Tests for some of ../questiontype.php
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/questiontype.php');

class default_questiontype_test extends UnitTestCase {
    var $qtype;

    function setUp() {
        $this->qtype = new default_questiontype();
    }

    function tearDown() {
        $this->qtype = null;
    }

    function test_compare_responses() {
        $question = new stdClass;
        $state = new stdClass;
        $teststate = new stdClass;

        $state->responses = array();
        $teststate->responses = array();
        $this->assertTrue($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('' => 'frog');
        $teststate->responses = array('' => 'toad');
        $this->assertFalse($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array('x' => 'frog');
        $teststate->responses = array('y' => 'frog');
        $this->assertFalse($this->qtype->compare_responses($question, $state, $teststate));

        $state->responses = array(1 => 1, 2 => 2);
        $teststate->responses = array(2 => 2, 1 => 1);
        $this->assertTrue($this->qtype->compare_responses($question, $state, $teststate));
    }
}

?>
