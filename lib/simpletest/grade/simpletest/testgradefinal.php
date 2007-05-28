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
 * Unit tests for grade_final object.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */
require_once(dirname(__FILE__) . '/../../../../config.php');
global $CFG;
require_once($CFG->libdir . '/simpletest/testgradelib.php');

class grade_final_test extends gradelib_test {

    function test_grade_grades_final_construct() {
        $params = new stdClass();

        $params->itemid = $this->grade_items[0]->id;
        $params->userid = 1;
        $params->gradevalue = 88;

        $grade_grades_final = new grade_grades_final($params, false);
        $this->assertEqual($params->itemid, $grade_grades_final->itemid);
        $this->assertEqual($params->gradevalue, $grade_grades_final->gradevalue);
    }

    function test_grade_grades_final_insert() {
        $grade_grades_final = new grade_grades_final();
        $this->assertTrue(method_exists($grade_grades_final, 'insert'));
        
        $grade_grades_final->itemid = $this->grade_items[0]->id;
        $grade_grades_final->userid = 4;
        $grade_grades_final->gradevalue = 88;

        $this->assertTrue($grade_grades_final->insert()); 

        $last_grade_grades_final = end($this->grade_grades_final);

        $this->assertEqual($last_grade_grades_final->id + 1, $grade_grades_final->id);
        $this->assertFalse(empty($grade_grades_final->timecreated));
        $this->assertFalse(empty($grade_grades_final->timemodified));
    }

    function test_grade_grades_final_update() {
        $grade_grades_final = new grade_grades_final($this->grade_grades_final[0]);
        $this->assertTrue(method_exists($grade_grades_final, 'update'));
        $grade_grades_final->gradevalue = 89;        
        $this->assertTrue($grade_grades_final->update());
        $gradevalue = get_field('grade_grades_final', 'gradevalue', 'id', $this->grade_grades_final[0]->id);
        $this->assertEqual($grade_grades_final->gradevalue, $gradevalue); 
    }

    function test_grade_grades_final_delete() {
        $grade_grades_final = new grade_grades_final($this->grade_grades_final[0]);
        $this->assertTrue(method_exists($grade_grades_final, 'delete'));
        
        $this->assertTrue($grade_grades_final->delete());
        $this->assertFalse(get_record('grade_grades_final', 'id', $grade_grades_final->id)); 
    }

    function test_grade_grades_final_fetch() {
        $grade_grades_final = new grade_grades_final(); 
        $this->assertTrue(method_exists($grade_grades_final, 'fetch'));

        $grade_grades_final = grade_grades_final::fetch('id', $this->grade_grades_final[0]->id);
        $this->assertEqual($this->grade_grades_final[0]->id, $grade_grades_final->id);
        $this->assertEqual($this->grade_grades_final[0]->gradevalue, $grade_grades_final->gradevalue); 
    } 

    function test_grade_grades_final_load_grade_item() {
        $grade_grades_final = new grade_grades_final($this->grade_grades_final[0]);
        $this->assertTrue(method_exists($grade_grades_final, 'load_grade_item'));
        $this->assertNull($grade_grades_final->grade_item);
        $this->assertTrue($grade_grades_final->itemid);
        $this->assertNotNull($grade_grades_final->load_grade_item());
        $this->assertNotNull($grade_grades_final->grade_item);
        $this->assertEqual($this->grade_items[0]->id, $grade_grades_final->grade_item->id);
    }
} 
?>
