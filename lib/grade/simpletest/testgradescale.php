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
        global $db;
        $grade_scale = new grade_scale();
        $this->assertTrue(method_exists($grade_scale, 'insert'));

        $grade_scale->name        = 'unittestscale3';
        $grade_scale->courseid    = $this->courseid;
        $grade_scale->userid      = $this->userid;
        $grade_scale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $grade_scale->description = 'This scale is used to mark standard assignments.';

        // Mock insert of data in history table
        $this->rs->setReturnValue('RecordCount', 1);
        $this->rs->fields = array(1); 
        
        // Mock insert of outcome object
        $db->setReturnValue('GetInsertSQL', true);
        $db->setReturnValue('Insert_ID', 1);

        $grade_scale->insert();

        $this->assertEqual($grade_scale->id, 1);
        $this->assertFalse(empty($grade_scale->timecreated));
        $this->assertFalse(empty($grade_scale->timemodified));
    }

    function test_grade_scale_update() {
        global $db;
        $grade_scale = new grade_scale($this->scale[0], false);
        $this->assertTrue(method_exists($grade_scale, 'update'));
        
        $grade_scale->timecreated = time() - 200000;
        $grade_scale->timemodified = $grade_scale->timecreated;
        $timemodified = $grade_scale->timemodified;
        $timecreated = $grade_scale->timecreated;

        // Mock update: MetaColumns is first returned to compare existing data with new
        $column = new stdClass();
        $column->name = 'name';
        $db->setReturnValue('MetaColumns', array($column));
        
        $grade_scale->name = 'Updated info for this unittest grade_scale';
        $this->assertTrue($grade_scale->update());
        
        // We expect timecreated to be unchanged, and timemodified to be updated
        $this->assertTrue($grade_scale->timemodified > $timemodified);
        $this->assertTrue($grade_scale->timemodified > $grade_scale->timecreated);
        $this->assertTrue($grade_scale->timecreated == $timecreated);
    }

    function test_grade_scale_delete() {
        $grade_scale = new grade_scale($this->scale[0], false);
        $this->assertTrue(method_exists($grade_scale, 'delete'));

        $this->assertTrue($grade_scale->delete());
    }

    function test_grade_scale_fetch() {
        global $db;

        $grade_scale = new grade_scale();
        $this->assertTrue(method_exists($grade_scale, 'fetch'));

        // Mock fetch
        $column = new stdClass();
        $column->name = 'id';
        $this->rs->setReturnValue('FetchField', $column); // Fetching the name of the first column
        $this->rs->setReturnValue('GetAssoc', array($this->scale[0]->id => (array) $this->scale[0])); 
        
        $grade_scale = grade_scale::fetch(array('id'=>$this->scale[0]->id));
        $this->assertEqual($this->scale[0]->id, $grade_scale->id);
        $this->assertEqual($this->scale[0]->name, $grade_scale->name);
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
