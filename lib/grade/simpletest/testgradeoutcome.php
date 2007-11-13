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
 * Unit tests for grade_outcome object.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/simpletest/fixtures/gradetest.php');

class grade_outcome_test extends grade_test {
    function setUp() {
        parent::setUp();
        $this->load_grade_outcomes();
    }

    function tearDown() {
        parent::tearDown();
    }

    function test_grade_outcome_construct() {
        $params = new stdClass();

        $params->courseid = $this->courseid;
        $params->shortname = 'Team work';

        $grade_outcome = new grade_outcome($params, false);
        $this->assertEqual($params->courseid, $grade_outcome->courseid);
        $this->assertEqual($params->shortname, $grade_outcome->shortname);
    }

    function test_grade_outcome_insert() {
        $grade_outcome = new grade_outcome();
        $grade_outcome->lib_wrapper = new mock_lib_wrapper();
        $this->assertTrue(method_exists($grade_outcome, 'insert'));

        $grade_outcome->courseid = $this->courseid;
        $grade_outcome->shortname = 'tw';
        $grade_outcome->fullname = 'Team work';
        
        $grade_outcome->lib_wrapper->expectCallCount('insert_record', 3); // main insert, history table insert and grade_outcomes_courses insert
        $grade_outcome->lib_wrapper->setReturnValue('insert_record', 1);
        $grade_outcome->lib_wrapper->expectOnce('get_record'); // for update_from_db() method
        $grade_outcome->lib_wrapper->setReturnValue('get_record', array(1));

        $grade_outcome->insert();

        $this->assertEqual($grade_outcome->id, 1);
        $this->assertFalse(empty($grade_outcome->timecreated));
        $this->assertFalse(empty($grade_outcome->timemodified));
    }
    
    function test_grade_outcome_update() {
        $grade_outcome = new grade_outcome($this->grade_outcomes[0], false);
        $grade_outcome->lib_wrapper = new mock_lib_wrapper();
        $grade_outcome->timecreated = time() - 200000;
        $grade_outcome->timemodified = $grade_outcome->timecreated;
        $grade_outcome->timemodified = $grade_outcome->timecreated;
        $timemodified = $grade_outcome->timemodified;
        $timecreated = $grade_outcome->timecreated;
        $grade_outcome->courseid = $this->courseid;

        $this->assertTrue(method_exists($grade_outcome, 'update'));
        $grade_outcome->shortname = 'Team work';
        
        $grade_outcome->lib_wrapper->expectOnce('update_record');
        $grade_outcome->lib_wrapper->setReturnValue('update_record', true);
        $grade_outcome->lib_wrapper->expectOnce('get_records');
        $grade_outcome->lib_wrapper->setReturnValue('get_records', false); // Pretend there is no record in grade_outcoms_courses table
        $grade_outcome->lib_wrapper->expectCallCount('insert_record', 2); // 1. grade_outcome_courses, 2. grade_outcomes_history

        $this->assertTrue($grade_outcome->update());
        
        // We expect timecreated to be unchanged, and timemodified to be updated
        $this->assertTrue($grade_outcome->timemodified > $timemodified);
        $this->assertTrue($grade_outcome->timemodified > $grade_outcome->timecreated);
        $this->assertTrue($grade_outcome->timecreated == $timecreated);
    }

    function test_grade_outcome_delete() {
        $grade_outcome = new grade_outcome($this->grade_outcomes[0], false);
        $grade_outcome->courseid = $this->courseid;
        $grade_outcome->lib_wrapper = new mock_lib_wrapper();
        
        $grade_outcome->lib_wrapper->expectCallCount('delete_records', 2);
        $grade_outcome->lib_wrapper->expectAt(0, 'delete_records', array('grade_outcomes_courses', 'outcomeid', $grade_outcome->id, 'courseid', $this->courseid));
        $grade_outcome->lib_wrapper->expectAt(1, 'delete_records', array('grade_outcomes', 'id', $grade_outcome->id));
        $grade_outcome->lib_wrapper->setReturnValue('delete_records', true);

        $grade_outcome->lib_wrapper->expectOnce('insert_record'); // grade_history entry

        $this->assertTrue(method_exists($grade_outcome, 'delete')); 
        $this->assertTrue($grade_outcome->delete());
    }

}
?>
