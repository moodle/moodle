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
require_once(dirname(__FILE__) . '/../../../../config.php');
global $CFG;
require_once($CFG->libdir . '/simpletest/testgradelib.php');

class grade_raw_test extends gradelib_test {
    
    function test_grade_raw_construct() {
        
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

} 
?>
