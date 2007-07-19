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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/simpletest/fixtures/gradetest.php');

class grade_text_test extends grade_test {

    function test_grade_grade_text_construct() {
        $params = new stdClass();

        $params->gradeid = $this->grade_grades[0]->id;
        $params->information = 'Thumbs down';
        $params->informationformat = FORMAT_PLAIN;
        $params->feedback = 'Good, but not good enough..';
        $params->feedbackformat = FORMAT_PLAIN;

        $grade_grade_text = new grade_grade_text($params, false);
        $this->assertEqual($params->gradeid, $grade_grade_text->gradeid);
        $this->assertEqual($params->information, $grade_grade_text->information);
        $this->assertEqual($params->informationformat, $grade_grade_text->informationformat);
        $this->assertEqual($params->feedback, $grade_grade_text->feedback);
        $this->assertEqual($params->feedbackformat, $grade_grade_text->feedbackformat);
    }

    function test_grade_grade_text_insert() {
        global $USER;

        $grade_grade_text = new grade_grade_text();
        $this->assertTrue(method_exists($grade_grade_text, 'insert'));

        $grade_grade_text->gradeid = $this->grade_grades[0]->id;
        $grade_grade_text->information = 'Thumbs down';
        $grade_grade_text->informationformat = FORMAT_PLAIN;
        $grade_grade_text->feedback = 'Good, but not good enough..';
        $grade_grade_text->feedbackformat = FORMAT_PLAIN;
        $grade_grade_text->usermodified = $USER->id;

        $grade_grade_text->insert();

        $last_grade_grade_text = end($this->grade_grades_text);

        $this->assertEqual($grade_grade_text->id, $last_grade_grade_text->id + 1);
        $this->assertFalse(empty($grade_grade_text->timecreated));
        $this->assertFalse(empty($grade_grade_text->timemodified));
        $this->assertEqual($USER->id, $grade_grade_text->usermodified);
    }

    function test_grade_grade_text_update() {
        $grade_grade_text = new grade_grade_text($this->grade_grades_text[0]);
        $this->assertTrue(method_exists($grade_grade_text, 'update'));

        $this->assertTrue($grade_grade_text->update(89));
        $information = get_field('grade_grades_text', 'information', 'id', $this->grade_grades_text[0]->id);
        $this->assertEqual($grade_grade_text->information, $information);
    }

    function test_grade_grade_text_delete() {
        $grade_grade_text = new grade_grade_text($this->grade_grades_text[0]);
        $this->assertTrue(method_exists($grade_grade_text, 'delete'));

        $this->assertTrue($grade_grade_text->delete());
        $this->assertFalse(get_record('grade_grades_text', 'id', $grade_grade_text->id));
    }

    function test_grade_grade_text_fetch() {
        $grade_grade_text = new grade_grade_text();
        $this->assertTrue(method_exists($grade_grade_text, 'fetch'));

        $grade_grade_text = grade_grade_text::fetch(array('id'=>$this->grade_grades_text[0]->id));
        $this->assertEqual($this->grade_grades_text[0]->id, $grade_grade_text->id);
        $this->assertEqual($this->grade_grades_text[0]->information, $grade_grade_text->information);
    }

    function test_grade_grade_text_fetch_all() {
        $grade_grade_text = new grade_grade_text();
        $this->assertTrue(method_exists($grade_grade_text, 'fetch_all'));

        $grade_grade_texts = grade_grade_text::fetch_all(array());
        $this->assertEqual(count($this->grade_grades_text[0]), count($grade_grade_texts));
    }
}
?>
