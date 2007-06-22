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
 * Unit tests for grade_raw object.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/simpletest/fixtures/gradetest.php');

class grade_grades_test extends grade_test {

    function test_grade_grades_construct() {
        $params = new stdClass();

        $params->itemid = $this->grade_items[0]->id;
        $params->userid = 1;
        $params->rawgrade = 88;
        $params->rawgrademax = 110;
        $params->rawgrademin = 18;

        $grade_grades = new grade_grades($params, false);
        $this->assertEqual($params->itemid, $grade_grades->itemid);
        $this->assertEqual($params->rawgrade, $grade_grades->rawgrade);
    }

    function test_grade_grades_insert() {
        $grade_grades = new grade_grades();
        $this->assertTrue(method_exists($grade_grades, 'insert'));

        $grade_grades->itemid = $this->grade_items[0]->id;
        $grade_grades->userid = 1;
        $grade_grades->rawgrade = 88;
        $grade_grades->rawgrademax = 110;
        $grade_grades->rawgrademin = 18;

        // Check the grade_item's needsupdate variable first
        $grade_grades->load_grade_item();
        $this->assertFalse($grade_grades->grade_item->needsupdate);

        $grade_grades->insert();

        $last_grade_grades = end($this->grade_grades);

        $this->assertEqual($grade_grades->id, $last_grade_grades->id + 1);
        $this->assertFalse(empty($grade_grades->timecreated));
        $this->assertFalse(empty($grade_grades->timemodified));
    }

    function test_grade_grades_update() {
        $grade_grades = new grade_grades($this->grade_grades[0]);
        $this->assertTrue(method_exists($grade_grades, 'update'));
    }

    function test_grade_grades_fetch() {
        $grade_grades = new grade_grades();
        $this->assertTrue(method_exists($grade_grades, 'fetch'));

        $grade_grades = grade_grades::fetch('id', $this->grade_grades[0]->id);
        $this->assertEqual($this->grade_grades[0]->id, $grade_grades->id);
        $this->assertEqual($this->grade_grades[0]->rawgrade, $grade_grades->rawgrade);
    }

    function test_grade_raw_update_feedback() {

    }

    function test_grade_raw_update_information() {

    }

    function test_grade_load_text() {
        $grade_grades = new grade_grades($this->grade_grades[0]);
        $this->assertTrue(method_exists($grade_grades, 'load_text'));
        $this->assertNull($grade_grades->grade_grades_text);
        $this->assertNotNull($grade_grades->load_text());
        $this->assertNotNull($grade_grades->grade_grades_text);
        $this->assertEqual($this->grade_grades_text[0]->id, $grade_grades->grade_grades_text->id);
    }

    function test_grade_grades_load_grade_item() {
        $grade_grades = new grade_grades($this->grade_grades[0]);
        $this->assertTrue(method_exists($grade_grades, 'load_grade_item'));
        $this->assertNull($grade_grades->grade_item);
        $this->assertTrue($grade_grades->itemid);
        $this->assertNotNull($grade_grades->load_grade_item());
        $this->assertNotNull($grade_grades->grade_item);
        $this->assertEqual($this->grade_items[0]->id, $grade_grades->grade_item->id);
    }


    function test_grade_grades_standardise_score() {
        $this->assertEqual(4, round(grade_grades::standardise_score(6, 0, 7, 0, 5)));
        $this->assertEqual(40, grade_grades::standardise_score(50, 30, 80, 0, 100));
    }


    function test_grade_grades_set_locked() {
        $grade_item = new grade_item($this->grade_items[0]);
        $grade = new grade_grades($grade_item->get_final(1));
        $this->assertTrue(method_exists($grade, 'set_locked'));

        $this->assertTrue(empty($grade_item->locked));
        $this->assertTrue(empty($grade->locked));

        $this->assertTrue($grade->set_locked(true));
        $this->assertFalse(empty($grade->locked));
        $this->assertTrue($grade->set_locked(false));
        $this->assertTrue(empty($grade->locked));

        $this->assertTrue($grade_item->set_locked(true));
        $grade = new grade_grades($grade_item->get_final(1));

        $this->assertFalse(empty($grade->locked));
        $this->assertFalse($grade->set_locked(false));

        $this->assertTrue($grade_item->set_locked(false));
        $grade = new grade_grades($grade_item->get_final(1));

        $this->assertTrue($grade->set_locked(false));
    }

    function test_grade_grades_is_locked() {
        $grade = new grade_grades($this->grade_grades[0]);
        $this->assertTrue(method_exists($grade, 'is_locked'));

        $this->assertFalse($grade->is_locked());
        $grade->locked = time();
        $this->assertTrue($grade->is_locked());
    }


}
?>
