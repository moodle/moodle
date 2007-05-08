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
 * Unit tests for grade_history object.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */
require_once(dirname(__FILE__) . '/../../../../config.php');
global $CFG;
require_once($CFG->libdir . '/simpletest/testgradelib.php');

class grade_history_test extends gradelib_test {

    function test_grade_history_construct() {
        $params = new stdClass();

        $params->itemid = $this->grade_items[0]->id;
        $params->userid = 1;
        $params->oldgrade = 88;
        $params->newgrade = 90;
        $params->note = 'Modified manually in testgradehistory.php';
        $params->howmodified = 'manual';

        $grade_history = new grade_history($params, false);
        $this->assertEqual($params->itemid, $grade_history->itemid);
        $this->assertEqual($params->note, $grade_history->note);
    }
    
    function test_grade_history_insert() {
        $grade_history = new grade_history();
        $this->assertTrue(method_exists($grade_history, 'insert'));
        
        $grade_history->itemid = $this->grade_items[0]->id;
        $grade_history->userid = 1;
        $grade_history->oldgrade = 88;
        $grade_history->newgrade = 90;
        $grade_history->note = 'Modified manually in testgradehistory.php';
        $grade_history->howmodified = 'manual';
        
        $grade_history->insert();

        $last_grade_history = end($this->grade_history);

        $this->assertEqual($grade_history->id, $last_grade_history->id + 1);
        $this->assertFalse(empty($grade_history->timecreated));
        $this->assertFalse(empty($grade_history->timemodified));
    }

    function test_grade_history_update() {
        $grade_history = new grade_history($this->grade_history[0]);
        $this->assertTrue(method_exists($grade_history, 'update'));
        $grade_history->note = 'Modified manually in testgradehistory.php';        
        $this->assertTrue($grade_history->update());
        $note = get_field('grade_history', 'note', 'id', $this->grade_history[0]->id);
        $this->assertEqual($grade_history->note, $note); 
    }

    function test_grade_history_delete() {
        $grade_history = new grade_history($this->grade_history[0]);
        $this->assertTrue(method_exists($grade_history, 'delete'));
        
        $this->assertTrue($grade_history->delete());
        $this->assertFalse(get_record('grade_history', 'id', $grade_history->id)); 
    }

    function test_grade_history_fetch() {
        $grade_history = new grade_history(); 
        $this->assertTrue(method_exists($grade_history, 'fetch'));

        $grade_history = grade_history::fetch('id', $this->grade_history[0]->id);
        $this->assertEqual($this->grade_history[0]->id, $grade_history->id);
        $this->assertEqual($this->grade_history[0]->note, $grade_history->note); 
    } 
} 
?>
