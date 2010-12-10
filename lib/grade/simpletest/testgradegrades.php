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

class grade_grade_test extends grade_test {

    function test_grade_grade() {
        $this->sub_test_grade_grade_construct();
        $this->sub_test_grade_grade_insert();
        $this->sub_test_grade_grade_update();
        $this->sub_test_grade_grade_fetch();
        $this->sub_test_grade_grade_fetch_all();
        $this->sub_test_grade_grade_load_grade_item();
        $this->sub_test_grade_grade_standardise_score();
        $this->sub_test_grade_grade_is_locked();
        $this->sub_test_grade_grade_set_hidden();
        $this->sub_test_grade_grade_is_hidden();
    }

    function sub_test_grade_grade_construct() {
        $params = new stdClass();

        $params->itemid = $this->grade_items[0]->id;
        $params->userid = 1;
        $params->rawgrade = 88;
        $params->rawgrademax = 110;
        $params->rawgrademin = 18;

        $grade_grade = new grade_grade($params, false);
        $this->assertEqual($params->itemid, $grade_grade->itemid);
        $this->assertEqual($params->rawgrade, $grade_grade->rawgrade);
    }

    function sub_test_grade_grade_insert() {
        $grade_grade = new grade_grade();
        $this->assertTrue(method_exists($grade_grade, 'insert'));

        $grade_grade->itemid = $this->grade_items[0]->id;
        $grade_grade->userid = 10;
        $grade_grade->rawgrade = 88;
        $grade_grade->rawgrademax = 110;
        $grade_grade->rawgrademin = 18;

        // Check the grade_item's needsupdate variable first
        $grade_grade->load_grade_item();
        $this->assertFalse($grade_grade->grade_item->needsupdate);

        $grade_grade->insert();

        $last_grade_grade = end($this->grade_grades);

        $this->assertEqual($grade_grade->id, $last_grade_grade->id + 1);

        // timecreated will only be set if the grade was submitted by an activity module
        $this->assertTrue(empty($grade_grade->timecreated));
        // timemodified will only be set if the grade was submitted by an activity module
        $this->assertTrue(empty($grade_grade->timemodified));

        //keep our collection the same as is in the database
        $this->grade_grades[] = $grade_grade;
    }

    function sub_test_grade_grade_update() {
        $grade_grade = new grade_grade($this->grade_grades[0]);
        $this->assertTrue(method_exists($grade_grade, 'update'));
    }

    function sub_test_grade_grade_fetch() {
        $grade_grade = new grade_grade();
        $this->assertTrue(method_exists($grade_grade, 'fetch'));

        $grades = grade_grade::fetch(array('id'=>$this->grade_grades[0]->id));
        $this->assertEqual($this->grade_grades[0]->id, $grades->id);
        $this->assertEqual($this->grade_grades[0]->rawgrade, $grades->rawgrade);
    }

    function sub_test_grade_grade_fetch_all() {
        $grade_grade = new grade_grade();
        $this->assertTrue(method_exists($grade_grade, 'fetch_all'));

        $grades = grade_grade::fetch_all(array());
        $this->assertEqual(count($this->grade_grades), count($grades));
    }

    function sub_test_grade_grade_load_grade_item() {
        $grade_grade = new grade_grade($this->grade_grades[0]);
        $this->assertTrue(method_exists($grade_grade, 'load_grade_item'));
        $this->assertNull($grade_grade->grade_item);
        $this->assertTrue($grade_grade->itemid);
        $this->assertNotNull($grade_grade->load_grade_item());
        $this->assertNotNull($grade_grade->grade_item);
        $this->assertEqual($this->grade_items[0]->id, $grade_grade->grade_item->id);
    }


    function sub_test_grade_grade_standardise_score() {
        $this->assertEqual(4, round(grade_grade::standardise_score(6, 0, 7, 0, 5)));
        $this->assertEqual(40, grade_grade::standardise_score(50, 30, 80, 0, 100));
    }


    /*
     * Disabling this test: the set_locked() arguments have been modified, rendering these tests useless until they are re-written

    function test_grade_grade_set_locked() {
        $grade_item = new grade_item($this->grade_items[0]);
        $grade = new grade_grade($grade_item->get_final(1));
        $this->assertTrue(method_exists($grade, 'set_locked'));

        $this->assertTrue(empty($grade_item->locked));
        $this->assertTrue(empty($grade->locked));

        $this->assertTrue($grade->set_locked(true));
        $this->assertFalse(empty($grade->locked));
        $this->assertTrue($grade->set_locked(false));
        $this->assertTrue(empty($grade->locked));

        $this->assertTrue($grade_item->set_locked(true, true));
        $grade = new grade_grade($grade_item->get_final(1));

        $this->assertFalse(empty($grade->locked));
        $this->assertFalse($grade->set_locked(true, false));

        $this->assertTrue($grade_item->set_locked(true, false));
        $grade = new grade_grade($grade_item->get_final(1));

        $this->assertTrue($grade->set_locked(true, false));
    }
    */

    function sub_test_grade_grade_is_locked() {
        $grade = new grade_grade($this->grade_grades[0]);
        $this->assertTrue(method_exists($grade, 'is_locked'));

        $this->assertFalse($grade->is_locked());
        $grade->locked = time();
        $this->assertTrue($grade->is_locked());
    }

    function sub_test_grade_grade_set_hidden() {
        $grade_item = new grade_item($this->grade_items[0]);
        $grade = new grade_grade($grade_item->get_final(1));
        $this->assertTrue(method_exists($grade, 'set_hidden'));

        $this->assertEqual(0, $grade_item->hidden);
        $this->assertEqual(0, $grade->hidden);

        $grade->set_hidden(0);
        $this->assertEqual(0, $grade->hidden);

        $grade->set_hidden(1);
        $this->assertEqual(1, $grade->hidden);
    }

    function sub_test_grade_grade_is_hidden() {
        $grade = new grade_grade($this->grade_grades[0]);
        $this->assertTrue(method_exists($grade, 'is_hidden'));

        //$this->grade_grades[0] is hidden by sub_test_grade_grade_set_hidden()
        //$this->assertFalse($grade->is_hidden());
        //$grade->hidden = 1;
        $this->assertTrue($grade->is_hidden());

        $grade->hidden = time()-666;
        $this->assertFalse($grade->is_hidden());

        $grade->hidden = time()+666;
        $this->assertTrue($grade->is_hidden());
    }


}
