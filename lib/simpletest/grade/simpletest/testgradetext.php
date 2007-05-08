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
 * Unit tests for grade_text object.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */
require_once(dirname(__FILE__) . '/../../../../config.php');
global $CFG;
require_once($CFG->libdir . '/simpletest/testgradelib.php');

class grade_text_test extends gradelib_test {

    function test_grade_grades_text_construct() {
        $params = new stdClass();

        $params->gradesid = $this->grade_grades_raw[0]->id;
        $params->information = 'Thumbs down';
        $params->informationformat = FORMAT_PLAIN;
        $params->feedback = 'Good, but not good enough..';
        $params->feedbackformat = FORMAT_PLAIN;

        $grade_grades_text = new grade_grades_text($params, false);
        $this->assertEqual($params->gradesid, $grade_grades_text->gradesid);
        $this->assertEqual($params->information, $grade_grades_text->information);
        $this->assertEqual($params->informationformat, $grade_grades_text->informationformat);
        $this->assertEqual($params->feedback, $grade_grades_text->feedback);
        $this->assertEqual($params->feedbackformat, $grade_grades_text->feedbackformat);
    }

    function test_grade_grades_text_insert() {
        $grade_grades_text = new grade_grades_text();
        $this->assertTrue(method_exists($grade_grades_text, 'insert'));
        
        $grade_grades_text->gradesid = $this->grade_grades_raw[0]->id;
        $grade_grades_text->information = 'Thumbs down';
        $grade_grades_text->informationformat = FORMAT_PLAIN;
        $grade_grades_text->feedback = 'Good, but not good enough..';
        $grade_grades_text->feedbackformat = FORMAT_PLAIN;

        $grade_grades_text->insert();

        $last_grade_grades_text = end($this->grade_grades_text);

        global $USER;
        
        $this->assertEqual($grade_grades_text->id, $last_grade_grades_text->id + 1);
        $this->assertFalse(empty($grade_grades_text->timecreated));
        $this->assertFalse(empty($grade_grades_text->timemodified));
        $this->assertEqual($USER->id, $grade_grades_text->usermodified);
    }

    function test_grade_grades_text_update() {
        $grade_grades_text = new grade_grades_text($this->grade_grades_text[0]);
        $this->assertTrue(method_exists($grade_grades_text, 'update'));
        
        $this->assertTrue($grade_grades_text->update(89));
        $information = get_field('grade_grades_text', 'information', 'id', $this->grade_grades_text[0]->id);
        $this->assertEqual($grade_grades_text->information, $information); 
    }

    function test_grade_grades_text_delete() {
        $grade_grades_text = new grade_grades_text($this->grade_grades_text[0]);
        $this->assertTrue(method_exists($grade_grades_text, 'delete'));
        
        $this->assertTrue($grade_grades_text->delete());
        $this->assertFalse(get_record('grade_grades_text', 'id', $grade_grades_text->id)); 
    }

    function test_grade_grades_text_fetch() {
        $grade_grades_text = new grade_grades_text(); 
        $this->assertTrue(method_exists($grade_grades_text, 'fetch'));

        $grade_grades_text = grade_grades_text::fetch('id', $this->grade_grades_text[0]->id);
        $this->assertEqual($this->grade_grades_text[0]->id, $grade_grades_text->id);
        $this->assertEqual($this->grade_grades_text[0]->information, $grade_grades_text->information); 
    } 
} 
?>
