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
        global $db;
        $grade_outcome = new grade_outcome();
        $this->assertTrue(method_exists($grade_outcome, 'insert'));

        $grade_outcome->courseid = $this->courseid;
        $grade_outcome->shortname = 'tw';
        $grade_outcome->fullname = 'Team work';
        
        // Mock insert of data in history table
        $this->rs->setReturnValue('RecordCount', 1);
        $this->rs->fields = array(1); 
        
        // Mock insert of outcome object
        $db->setReturnValue('GetInsertSQL', true);
        $db->setReturnValue('Insert_ID', 1);

        $grade_outcome->insert();

        $this->assertEqual($grade_outcome->id, 1);
        $this->assertFalse(empty($grade_outcome->timecreated));
        $this->assertFalse(empty($grade_outcome->timemodified));
    }

    function test_grade_outcome_update() {
        global $db;

        $grade_outcome = new grade_outcome($this->grade_outcomes[0], false);
        $grade_outcome->timecreated = time() - 200000;
        $grade_outcome->timemodified = $grade_outcome->timecreated;
        $timemodified = $grade_outcome->timemodified;
        $timecreated = $grade_outcome->timecreated;
        $grade_outcome->courseid = null;

        $this->assertTrue(method_exists($grade_outcome, 'update'));
        $grade_outcome->shortname = 'Team work';
        
        // Mock update: MetaColumns is first returned to compare existing data with new
        $column = new stdClass();
        $column->name = 'shortname';
        $db->setReturnValue('MetaColumns', array($column));
        
        $this->assertTrue($grade_outcome->update());
        
        // We expect timecreated to be unchanged, and timemodified to be updated
        $this->assertTrue($grade_outcome->timemodified > $timemodified);
        $this->assertTrue($grade_outcome->timemodified > $grade_outcome->timecreated);
        $this->assertTrue($grade_outcome->timecreated == $timecreated);

        // @TODO When the grade_outcome has a courseid but no match in the grade_outcomes_courses table, the update method should insert a new record in that table
        
        // @TODO If history switch is on, an insert should be performed in the grade_outcomes_history table

    }

    function test_grade_outcome_delete() {
        global $db; 
        $grade_outcome = new grade_outcome($this->grade_outcomes[0], false);
        
        // Mock delete 
        $this->assertTrue(method_exists($grade_outcome, 'delete')); 
        $this->assertTrue($grade_outcome->delete());

        // @TODO If history switch is on, an insert should be performed in the grade_outcomes_history table

        // @TODO If grade_outcome has a courseid, associated records from grade_outcomes_courses should be deleted also
    }

    function test_grade_outcome_fetch() {
        global $db;

        $grade_outcome = new grade_outcome();
        $this->assertTrue(method_exists($grade_outcome, 'fetch'));
        
        // Mock fetch
        $column = new stdClass();
        $column->name = 'id';
        $this->rs->setReturnValue('FetchField', $column); // Fetching the name of the first column
        $this->rs->setReturnValue('GetAssoc', array($this->grade_outcomes[0]->id => (array) $this->grade_outcomes[0])); 

        $grade_outcome = grade_outcome::fetch(array('id'=>$this->grade_outcomes[0]->id));
        $this->assertEqual($this->grade_outcomes[0]->id, $grade_outcome->id);
        $this->assertEqual($this->grade_outcomes[0]->shortname, $grade_outcome->shortname);
        
        // Mock fetching of scale object
        $this->reset_mocks();
        $this->rs->setReturnValue('FetchField', $column); // Fetching the name of the first column
        $this->rs->setReturnValue('GetAssoc', array($this->scale[2]->id => (array) $this->scale[2])); 
        $grade_outcome->load_scale();
        $this->assertEqual($this->scale[2]->id, $grade_outcome->scale->id);
    }

    function test_grade_outcome_fetch_all() {
        $grade_outcome = new grade_outcome();
        $this->assertTrue(method_exists($grade_outcome, 'fetch_all'));
        
        // Mock fetch_all
        $return_array = array();
        foreach ($this->grade_outcomes as $go) {
            $return_array[$go->id] = (array) $go;
        }

        $column = new stdClass();
        $column->name = 'id';
        $this->rs->setReturnValue('FetchField', $column); // Fetching the name of the first column
        $this->rs->setReturnValue('GetAssoc', $return_array); 

        $grade_outcomes = grade_outcome::fetch_all(array());
        $this->assertEqual(count($this->grade_outcomes), count($grade_outcomes));
    }
}
?>
