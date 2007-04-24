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
 * Unit tests for (some of) ../gradelib.php.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

/** $Id */
require_once(dirname(__FILE__) . '/../../config.php');

global $CFG;
require_once($CFG->libdir . '/simpletestlib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/dmllib.php');

class gradelib_test extends UnitTestCase {
    
    var $grade_items = array();
    var $grade_categories = array();
    var $courseid = 1;

    /**
     * Create temporary entries in the database for these tests.
     * These tests have to work no matter the data currently in the database
     * (meaning they should run on a brand new site). This means several items of
     * data have to be artificially inseminated (:-) in the DB.
     */
    function setUp() 
    {
        $grade_category = new stdClass();

        $grade_category->fullname    = 'unittestcategory1';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->keephigh    = 100;
        $grade_category->droplow     = 10;
        $grade_category->hidden      = 0;
        
        if ($grade_category->id = insert_record('grade_categories', $grade_category)) {
            $this->grade_categories[] = $grade_category;
        }

        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $grade_category->id;
        $grade_item->itemname = 'unittestgradeitem1';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminstance = 1;
        $grade_item->iteminfo = 'Grade item used for unit testing';

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[] = $grade_item;
        }
        
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->itemname = 'unittestgradeitem2';
        $grade_item->itemtype = 'import';
        $grade_item->itemmodule = 'assignment';
        $grade_item->iteminstance = 2;
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->locked = mktime() + 240000;

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[] = $grade_item;
        }

        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $grade_category->id;
        $grade_item->itemname = 'unittestgradeitem3';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'forum';
        $grade_item->iteminstance = 3;
        $grade_item->iteminfo = 'Grade item used for unit testing';

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[] = $grade_item;
        }
    }


    /**
     * Delete temporary entries from the database
     */
    function tearDown() 
    {
        foreach ($this->grade_items as $grade_item) {
            delete_records('grade_items', 'id', $grade_item->id);
        }

        foreach ($this->grade_categories as $grade_category) {
            delete_records('grade_categories', 'id', $grade_category->id);
        }
    }

    function test_grade_get_items()
    {
        $grade_items = grade_get_items($this->courseid);

        $this->assertTrue(is_array($grade_items)); 
        $this->assertEqual(count($grade_items), 3);
    }

    function test_grade_create_item()
    {
        $params = new stdClass();

        $params->courseid = $this->courseid;
        $params->categoryid = $this->grade_categories[0]->id;
        $params->itemname = 'unittestgradeitem4';
        $params->itemtype = 'mod';
        $params->itemmodule = 'database';
        $params->iteminstance = 4;
        $params->iteminfo = 'Grade item used for unit testing';

        $params->id = grade_create_item($params);
        $last_grade_item = end($this->grade_items);

        $this->assertEqual($params->id, $last_grade_item->id + 1);
        $this->grade_items[] = $params;
    }

    function test_grade_create_category()
    {
        $grade_category = new stdClass();
        
        $grade_category->id = grade_create_category($this->courseid, 'unittestcategory2', $this->grade_items, GRADE_AGGREGATE_MEAN);
        $last_grade_category = end($this->grade_categories);

        $this->assertEqual($grade_category->id, $last_grade_category->id + 1);
        $this->grade_categories[] = $grade_category;
    }

    function test_grade_is_locked()
    {
        $grade_item = $this->grade_items[0];
        $this->assertFalse(grade_is_locked($grade_item->itemtype, $grade_item->itemmodule, $grade_item->iteminstance));
        $grade_item = $this->grade_items[1];
        $this->assertTrue(grade_is_locked($grade_item->itemtype, $grade_item->itemmodule, $grade_item->iteminstance)); 
    }

    function test_grade_item_construct()
    { 
        $params = new stdClass();

        $params->courseid = $this->courseid;
        $params->categoryid = $this->grade_categories[0]->id;
        $params->itemname = 'unittestgradeitem4';
        $params->itemtype = 'mod';
        $params->itemmodule = 'database';
        $params->iteminstance = 4;
        $params->iteminfo = 'Grade item used for unit testing';

        $grade_item = new grade_item($params);

        $this->assertEqual($params->courseid, $grade_item->courseid);
        $this->assertEqual($params->categoryid, $grade_item->categoryid);
        $this->assertEqual($params->itemname, $grade_item->itemname);
        $this->assertEqual($params->itemtype, $grade_item->itemtype);
        $this->assertEqual($params->itemmodule, $grade_item->itemmodule);
        $this->assertEqual($params->iteminstance, $grade_item->iteminstance);
        $this->assertEqual($params->iteminfo, $grade_item->iteminfo);
    }

    function test_grade_item_insert()
    {
        $grade_item = new grade_item();

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[0]->id;
        $grade_item->itemname = 'unittestgradeitem4';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminstance = 1;
        $grade_item->iteminfo = 'Grade item used for unit testing';

        $grade_item->insert();

        $last_grade_item = end($this->grade_items);

        $this->assertEqual($grade_item->id, $last_grade_item->id + 1);
        $this->grade_items[] = $grade_item; 
    }

    function test_grade_item_delete()
    {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue($grade_item->delete());
        $this->assertFalse(get_record('grade_items', 'id', $grade_item->id));
    }

    function test_grade_item_update()
    {
        $grade_item = new grade_item($this->grade_items[0]);
        $grade_item->iteminfo = 'Updated info for this unittest grade_item';
        $this->assertTrue($grade_item->update());
        $this->assertEqual($grade_item->iteminfo, get_field('grade_items', 'iteminfo', 'id', $grade_item->id));
    }

    function test_grade_item_get_by_id()
    {

    }

    function test_grade_item_get_record()
    {

    }

    function test_grade_item_get_records_select()
    {

    }

    function test_grade_item_get_raw()
    {

    }

    function test_grade_item_get_final()
    {

    }

    function test_grade_item_get_calculation()
    {

    }

    function test_grade_item_get_category()
    {

    }

    function test_grade_category_construct()
    {

    }

    function test_grade_category_insert()
    {

    }

    function test_grade_category_update()
    {

    }

    function test_grade_category_delete()
    {

    }

    function test_grade_category_get_by_id()
    {

    }

    function test_grade_category_get_record()
    {

    } 
}

?>
