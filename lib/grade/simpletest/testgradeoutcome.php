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

    function test_grade_outcome() {
        $this->sub_test_grade_outcome_construct();
        $this->sub_test_grade_outcome_insert();
        $this->sub_test_grade_outcome_update();
        $this->sub_test_grade_outcome_delete();
        //$this->sub_test_grade_outcome_fetch();
        $this->sub_test_grade_outcome_fetch_all();
    }

    function sub_test_grade_outcome_construct() {
        $params = new stdClass();

        $params->courseid = $this->courseid;
        $params->shortname = 'Team work';

        $grade_outcome = new grade_outcome($params, false);
        $this->assertEqual($params->courseid, $grade_outcome->courseid);
        $this->assertEqual($params->shortname, $grade_outcome->shortname);
    }

    function sub_test_grade_outcome_insert() {
        $grade_outcome = new grade_outcome();
        $this->assertTrue(method_exists($grade_outcome, 'insert'));

        $grade_outcome->courseid = $this->courseid;
        $grade_outcome->shortname = 'tw';
        $grade_outcome->fullname = 'Team work';

        $grade_outcome->insert();

        $last_grade_outcome = end($this->grade_outcomes);

        $this->assertEqual($grade_outcome->id, $last_grade_outcome->id + 1);
        $this->assertFalse(empty($grade_outcome->timecreated));
        $this->assertFalse(empty($grade_outcome->timemodified));
    }

    function sub_test_grade_outcome_update() {
        global $DB;
        $grade_outcome = new grade_outcome($this->grade_outcomes[0]);
        $this->assertTrue(method_exists($grade_outcome, 'update'));
        $grade_outcome->shortname = 'Team work';
        $this->assertTrue($grade_outcome->update());
        $shortname = $DB->get_field('grade_outcomes', 'shortname', array('id' => $this->grade_outcomes[0]->id));
        $this->assertEqual($grade_outcome->shortname, $shortname);
    }

    function sub_test_grade_outcome_delete() {
        global $DB;
        $grade_outcome = new grade_outcome($this->grade_outcomes[0]);
        $this->assertTrue(method_exists($grade_outcome, 'delete'));

        $this->assertTrue($grade_outcome->delete());
        $this->assertFalse($DB->get_record('grade_outcomes', array('id' => $grade_outcome->id)));
    }

    function sub_test_grade_outcome_fetch() {
        $grade_outcome = new grade_outcome();
        $this->assertTrue(method_exists($grade_outcome, 'fetch'));

        $grade_outcome = grade_outcome::fetch(array('id'=>$this->grade_outcomes[0]->id));
        $grade_outcome->load_scale();
        $this->assertEqual($this->grade_outcomes[0]->id, $grade_outcome->id);
        $this->assertEqual($this->grade_outcomes[0]->shortname, $grade_outcome->shortname);

        $this->assertEqual($this->scale[2]->id, $grade_outcome->scale->id);
    }

    function sub_test_grade_outcome_fetch_all() {
        $grade_outcome = new grade_outcome();
        $this->assertTrue(method_exists($grade_outcome, 'fetch_all'));

        $grade_outcomes = grade_outcome::fetch_all(array());
        $this->assertEqual(count($this->grade_outcomes), count($grade_outcomes));
    }
}
