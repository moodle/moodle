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
 * Unit tests for grade_scale object.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/simpletest/fixtures/gradetest.php');

class grade_scale_test extends grade_test {

    function setUp() {
        parent::setUp();
        $this->load_scale();
    }

    function tearDown() {
        parent::tearDown();
    }

    function test_scale_construct() {
        $params = new stdClass();

        $params->name        = 'unittestscale3';
        $params->courseid    = $this->courseid;
        $params->userid      = $this->userid;
        $params->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $params->description = 'This scale is used to mark standard assignments.';
        $params->timemodified = mktime();

        $scale = new grade_scale($params, false);

        $this->assertEqual($params->name, $scale->name);
        $this->assertEqual($params->scale, $scale->scale);
        $this->assertEqual($params->description, $scale->description);

    }

    function test_grade_scale_insert() {
        $grade_scale = new grade_scale();
        $grade_scale->lib_wrapper = new mock_lib_wrapper();

        $this->assertTrue(method_exists($grade_scale, 'insert'));

        $grade_scale->name        = 'unittestscale3';
        $grade_scale->courseid    = $this->courseid;
        $grade_scale->userid      = $this->userid;
        $grade_scale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $grade_scale->description = 'This scale is used to mark standard assignments.';

        $grade_scale->lib_wrapper->expectCallCount('insert_record', 2); // main insert and history table insert 
        $grade_scale->lib_wrapper->setReturnValue('insert_record', 1);
        $grade_scale->lib_wrapper->expectOnce('get_record'); // for update_from_db() method
        $grade_scale->lib_wrapper->setReturnValue('get_record', array(1));
        
        $grade_scale->insert();

        $this->assertEqual($grade_scale->id, 1);
        $this->assertFalse(empty($grade_scale->timecreated));
        $this->assertFalse(empty($grade_scale->timemodified));
    }

    function test_grade_scale_update() {
        $grade_scale = new grade_scale($this->scale[0], false);
        $grade_scale->lib_wrapper = new mock_lib_wrapper();
        $this->assertTrue(method_exists($grade_scale, 'update'));
        
        $grade_scale->timecreated = time() - 200000;
        $grade_scale->timemodified = $grade_scale->timecreated;
        $timemodified = $grade_scale->timemodified;
        $timecreated = $grade_scale->timecreated;

        $grade_scale->lib_wrapper->expectOnce('update_record');
        $grade_scale->lib_wrapper->setReturnValue('update_record', true);
        
        $grade_scale->name = 'Updated info for this unittest grade_scale';
        $this->assertTrue($grade_scale->update());
        
        // We expect timecreated to be unchanged, and timemodified to be updated
        $this->assertTrue($grade_scale->timemodified > $timemodified);
        $this->assertTrue($grade_scale->timemodified > $grade_scale->timecreated);
        $this->assertTrue($grade_scale->timecreated == $timecreated);
    }

    function test_grade_scale_delete() {
        $grade_scale = new grade_scale($this->scale[0], false);
        $grade_scale->lib_wrapper = new mock_lib_wrapper();
        $this->assertTrue(method_exists($grade_scale, 'delete'));

        $grade_scale->lib_wrapper->expectOnce('delete_records', array('scale', 'id', $grade_scale->id));
        $grade_scale->lib_wrapper->setReturnValue('delete_records', true);
        
        $grade_scale->lib_wrapper->expectOnce('insert_record'); // grade_history entry
        
        $this->assertTrue($grade_scale->delete());
    }

    function test_scale_load_items() {
        $scale = new grade_scale($this->scale[0], false);
        $this->assertTrue(method_exists($scale, 'load_items'));

        $scale->load_items();
        $this->assertEqual(7, count($scale->scale_items));
        $this->assertEqual('Fairly neutral', $scale->scale_items[2]);

        $newscale = 'Item1, Item2, Item3, Item4';
        $this->assertEqual(4, count($scale->load_items($newscale)));
    }

    function test_scale_compact_items() {
        $scale = new grade_scale($this->scale[0], false);
        $this->assertTrue(method_exists($scale, 'compact_items'));

        $scale->load_items();
        $scale->scale = null;
        $scale->compact_items();

        // The original string and the new string may have differences in whitespace around the delimiter, and that's OK
        $this->assertEqual(preg_replace('/\s*,\s*' . '/', ',', $this->scale[0]->scale), $scale->scale);
    }
}
?>
