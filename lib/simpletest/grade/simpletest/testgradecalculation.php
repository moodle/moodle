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
 * Unit tests for grade_calculation object.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */
require_once(dirname(__FILE__) . '/../../../../config.php');
global $CFG;
require_once($CFG->libdir . '/simpletest/testgradelib.php');

class grade_calculation_test extends gradelib_test {

    function test_grade_calculation_construct() {
        $params = new stdClass();

        $params->itemid = $this->grade_items[0]->id;
        $params->calculation = 'MEAN(1, 2)';

        $grade_calculation = new grade_calculation($params, false);
        $this->assertEqual($params->itemid, $grade_calculation->itemid);
        $this->assertEqual($params->calculation, $grade_calculation->calculation);
    }

    function test_grade_calculation_insert() {
        $grade_calculation = new grade_calculation();
        $this->assertTrue(method_exists($grade_calculation, 'insert'));
        
        $grade_calculation->itemid = $this->grade_items[0]->id;
        $grade_calculation->calculation = 'MEAN(1, 2)';

        $grade_calculation->insert();

        $last_grade_calculation = end($this->grade_calculations);

        $this->assertEqual($grade_calculation->id, $last_grade_calculation->id + 1);
        $this->assertFalse(empty($grade_calculation->timecreated));
        $this->assertFalse(empty($grade_calculation->timemodified));

    }

    function test_grade_calculation_update() {
        $grade_calculation = new grade_calculation($this->grade_calculations[0]);
        $this->assertTrue(method_exists($grade_calculation, 'update'));
        $grade_calculation->calculation = 'MEAN(1, 2)';        
        $this->assertTrue($grade_calculation->update());
        $calculation = get_field('grade_calculations', 'calculation', 'id', $this->grade_calculations[0]->id);
        $this->assertEqual($grade_calculation->calculation, $calculation); 
    }

    function test_grade_calculation_delete() {
        $grade_calculation = new grade_calculation($this->grade_calculations[0]);
        $this->assertTrue(method_exists($grade_calculation, 'delete'));
        
        $this->assertTrue($grade_calculation->delete());
        $this->assertFalse(get_record('grade_calculations', 'id', $grade_calculation->id)); 
    }

    function test_grade_calculation_fetch() {
        $grade_calculation = new grade_calculation(); 
        $this->assertTrue(method_exists($grade_calculation, 'fetch'));

        $grade_calculation = grade_calculation::fetch('id', $this->grade_calculations[0]->id);
        $this->assertEqual($this->grade_calculations[0]->id, $grade_calculation->id);
        $this->assertEqual($this->grade_calculations[0]->calculation, $grade_calculation->calculation); 
    } 

} 
?>
