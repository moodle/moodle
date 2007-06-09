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

global $CFG;
require_once($CFG->libdir.'/simpletest/fixtures/gradetest.php');

class grade_raw_test extends grade_test {

    function test_grade_grades_raw_construct() {
        $params = new stdClass();

        $params->itemid = $this->grade_items[0]->id;
        $params->userid = 1;
        $params->gradevalue = 88;
        $params->grademax = 110;
        $params->grademin = 18;

        $grade_grades_raw = new grade_grades_raw($params, false);
        $this->assertEqual($params->itemid, $grade_grades_raw->itemid);
        $this->assertEqual($params->gradevalue, $grade_grades_raw->gradevalue);
    }

    function test_grade_grades_raw_insert() {
        $grade_grades_raw = new grade_grades_raw();
        $this->assertTrue(method_exists($grade_grades_raw, 'insert'));
        
        $grade_grades_raw->itemid = $this->grade_items[0]->id;
        $grade_grades_raw->userid = 1;
        $grade_grades_raw->gradevalue = 88;
        $grade_grades_raw->grademax = 110;
        $grade_grades_raw->grademin = 18;

        // Check the grade_item's needsupdate variable first
        $grade_grades_raw->load_grade_item(); 
        $this->assertFalse($grade_grades_raw->grade_item->needsupdate);

        $grade_grades_raw->insert();

        // Now check the needsupdate variable, it should have been set to true
        $this->assertTrue($grade_grades_raw->grade_item->needsupdate);
        
        $last_grade_grades_raw = end($this->grade_grades_raw);

        $this->assertEqual($grade_grades_raw->id, $last_grade_grades_raw->id + 1);
        $this->assertFalse(empty($grade_grades_raw->timecreated));
        $this->assertFalse(empty($grade_grades_raw->timemodified));

        // try a scale raw grade
        $grade_grades_raw = new grade_grades_raw();
        
        $grade_grades_raw->itemid = $this->grade_items[0]->id;
        $grade_grades_raw->userid = 1;
        $grade_grades_raw->gradevalue = 6;
        $grade_grades_raw->scaleid = $this->scale[0]->id;

        $grade_grades_raw->insert();

        $this->assertEqual(7, $grade_grades_raw->grademax);
        $this->assertEqual(0, $grade_grades_raw->grademin); 
    }

    function test_grade_grades_raw_update() {
        $grade_grades_raw = new grade_grades_raw($this->grade_grades_raw[0]);
        $this->assertTrue(method_exists($grade_grades_raw, 'update'));
        
        // Check the grade_item's needsupdate variable first
        $grade_grades_raw->load_grade_item();
        $this->assertFalse($grade_grades_raw->grade_item->needsupdate);
        $this->assertTrue($grade_grades_raw->update(89));
        $gradevalue = get_field('grade_grades_raw', 'gradevalue', 'id', $this->grade_grades_raw[0]->id);
        $this->assertEqual($grade_grades_raw->gradevalue, $gradevalue); 

        // Now check the needsupdate variable, it should have been set to true
        $this->assertTrue($grade_grades_raw->grade_item->needsupdate);
    }

    function test_grade_grades_raw_delete() {
        $grade_grades_raw = new grade_grades_raw($this->grade_grades_raw[0]);
        $this->assertTrue(method_exists($grade_grades_raw, 'delete'));
        
        // Check the grade_item's needsupdate variable first
        $grade_grades_raw->load_grade_item(); 
        $this->assertFalse($grade_grades_raw->grade_item->needsupdate);

        $this->assertTrue($grade_grades_raw->delete());
        $this->assertFalse(get_record('grade_grades_raw', 'id', $grade_grades_raw->id)); 
        
        // Now check the needsupdate variable, it should have been set to true
        $this->assertTrue($grade_grades_raw->grade_item->needsupdate);
    }

    function test_grade_grades_raw_fetch() {
        $grade_grades_raw = new grade_grades_raw(); 
        $this->assertTrue(method_exists($grade_grades_raw, 'fetch'));

        $grade_grades_raw = grade_grades_raw::fetch('id', $this->grade_grades_raw[0]->id);
        $this->assertEqual($this->grade_grades_raw[0]->id, $grade_grades_raw->id);
        $this->assertEqual($this->grade_grades_raw[0]->gradevalue, $grade_grades_raw->gradevalue); 
    } 
    
    /**
     * Make sure that an update of a grade_raw object also updates the history table.
     */
    function test_grade_raw_update_history() {
        $grade_raw = new grade_grades_raw($this->grade_grades_raw[0]);
        $oldgrade = $grade_raw->gradevalue;
        $newgrade = 88;
        $howmodified = 'manual';
        $note = 'unittest editing grade manually';
        $grade_raw->update($newgrade, $howmodified, $note);
        
        // Get last entry in the history log and check its attributes
        $results = get_records('grade_history', 'itemid', $grade_raw->itemid, 'id DESC', '*', 0, 1);
        $history_log = current($results);
        $this->assertEqual($grade_raw->userid, $history_log->userid);
        $this->assertEqual($oldgrade, $history_log->oldgrade);
        $this->assertEqual($newgrade, $history_log->newgrade);
        $this->assertEqual($howmodified, $history_log->howmodified);
        $this->assertEqual($note, $history_log->note);
    }
    
    function test_grade_raw_update_feedback() {

    }

    function test_grade_raw_update_information() {

    }

    function test_grade_raw_load_text() {
        $grade_grades_raw = new grade_grades_raw($this->grade_grades_raw[0]);
        $this->assertTrue(method_exists($grade_grades_raw, 'load_text'));
        $this->assertNull($grade_grades_raw->grade_grades_text);
        $this->assertFalse(array_key_exists('feedback', $grade_grades_raw));
        $this->assertFalse(array_key_exists('feedbackformat', $grade_grades_raw));
        $this->assertFalse(array_key_exists('information', $grade_grades_raw));
        $this->assertFalse(array_key_exists('informationformat', $grade_grades_raw));
        $this->assertNotNull($grade_grades_raw->load_text());
        $this->assertNotNull($grade_grades_raw->grade_grades_text);
        $this->assertTrue(array_key_exists('feedback', $grade_grades_raw));
        $this->assertTrue(array_key_exists('feedbackformat', $grade_grades_raw));
        $this->assertTrue(array_key_exists('information', $grade_grades_raw));
        $this->assertTrue(array_key_exists('informationformat', $grade_grades_raw));
        $this->assertEqual($this->grade_grades_text[0]->id, $grade_grades_raw->grade_grades_text->id); 
    }

    function test_grade_grades_raw_load_grade_item() {
        $grade_grades_raw = new grade_grades_raw($this->grade_grades_raw[0]);
        $this->assertTrue(method_exists($grade_grades_raw, 'load_grade_item'));
        $this->assertNull($grade_grades_raw->grade_item);
        $this->assertTrue($grade_grades_raw->itemid);
        $this->assertNotNull($grade_grades_raw->load_grade_item());
        $this->assertNotNull($grade_grades_raw->grade_item);
        $this->assertEqual($this->grade_items[0]->id, $grade_grades_raw->grade_item->id);
    }

} 
?>
