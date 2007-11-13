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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/**
 * Shared code for all grade related tests.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/dmllib.php');

Mock::generate('grade_item', 'mock_grade_item');
Mock::generate('grade_scale', 'mock_grade_scale');
Mock::generate('grade_category', 'mock_grade_category');
Mock::generate('grade_grade', 'mock_grade_grade');
Mock::generate('grade_outcome', 'mock_grade_outcome');
Mock::generate('grade_lib_wrapper', 'mock_lib_wrapper');
Mock::generate('ADODB_' . $CFG->dbtype, 'mock_db');
Mock::generate('ADORecordSet_' . $CFG->dbtype, 'mock_rs');

// Prepare partial mocks for the static grade object instances
Mock::generatePartial('grade_category', 'mock_grade_category_partial', array('fetch', 'fetch_all'));
Mock::generatePartial('grade_item', 'mock_grade_item_partial', array('fetch', 'fetch_all'));
Mock::generatePartial('grade_grade', 'mock_grade_grade_partial', array('fetch', 'fetch_all'));
Mock::generatePartial('grade_outcome', 'mock_grade_outcome_partial', array('fetch', 'fetch_all'));
Mock::generatePartial('grade_scale', 'mock_grade_scale_partial', array('fetch', 'fetch_all'));

/**
 * Here is a brief explanation of the test data set up in these unit tests.
 * category1 => array(category2 => array(grade_item1, grade_item2), category3 => array(grade_item3))
 * 3 users for 3 grade_items
 */
class grade_test extends UnitTestCase {

    /**
     * Each database table receives a number of test entries. These are saved as
     * arrays of stcClass objects available to this class. This means that
     * every test has access to these test data. The order of the following array is
     * crucial, because of the interrelationships between objects.
     */
    var $tables = array('modules',
                        'quiz',
                        'assignment',
                        'forum',
                        'course_modules',
                        'grade_categories',
                        'scale',
                        'grade_items',
                        'grade_grades',
                        'grade_outcomes'
                        );

    var $grade_items = array();
    var $grade_categories = array();
    var $grade_grades = array();
    var $grade_outcomes = array();
    var $scale = array();
    var $modules = array();
    var $course_modules = array();

    var $assignments = array();
    var $quizzes = array();
    var $forums = array();
    var $courseid = 1;
    var $userid = 1;
	
    var $loaded_tables = array(); // An array of the data tables that were loaded for a specific test. Only these will be "unloaded" at tearDown time
	var $real_db;
    var $rs;  

    /**
     * Create temporary test tables and entries in the database for these tests.
     * These tests have to work on a brand new site.
     * Override $CFG->prefix while these tests run.
     */
    function setUp() {
        // Set global category settings to -1 (not force)
        global $CFG, $db;
        $CFG->grade_droplow = -1;
        $CFG->grade_keephigh = -1;
        $CFG->grade_aggregation = -1;
        $CFG->grade_aggregateonlygraded = -1;
        $CFG->grade_aggregateoutcomes = -1;
        $CFG->grade_aggregatesubcats = -1;
        $CFG->disablegradehistory = false;
		$this->real_db = fullclone($db);
        // $this->reset_mocks();
        grade_object::get_instance('grade_item', null, false, true, new mock_grade_item_partial($this));
        grade_object::get_instance('grade_category', null, false, true, new mock_grade_category_partial($this));
        grade_object::get_instance('grade_grade', null, false, true, new mock_grade_grade_partial($this));
        grade_object::get_instance('grade_outcome', null, false, true, new mock_grade_outcome_partial($this));
        grade_object::get_instance('grade_scale', null, false, true, new mock_grade_scale_partial($this)); 
    }

    /**
     * Drop test tables from DB.
     * Restore original $CFG->prefix.
     */
    function tearDown() {
        global $CFG, $db;
		// delete the contents of tables before the test run - the unit test might fail on fatal error and the data would not be deleted!
        foreach ($this->loaded_tables as $table) {
            unset($this->$table);
        }
        $this->loaded_tables = array();
		$db = $this->real_db; 
    }

    function reset_mocks() {
        global $db, $CFG;
        $db = new mock_db();
        $this->rs = new mock_rs();
        $this->rs->EOF = false;
        $db->setReturnReference('Execute', $this->rs);
        $db->setReturnReference('SelectLimit', $this->rs); 
    }

    /**
     * Load scale data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_scale() {
        $scale = new stdClass();

        $scale->id          = 1;
        $scale->name        = 'unittestscale1';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Way off topic, Not very helpful, Fairly neutral, Fairly helpful, Supportive, Some good information, Perfect answer!';
        $scale->description = 'This scale defines some of qualities that make posts helpful within the Moodle help forums.\n Your feedback will help others see how their posts are being received.';
        $scale->timemodified = mktime();

        $this->scale[0] = $scale;
        $temp = explode(',', $scale->scale);
        $this->scalemax[0] = count($temp) -1;

        $scale = new stdClass();

        $scale->id          = 2;
        $scale->name        = 'unittestscale2';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $scale->description = 'This scale is used to mark standard assignments.';
        $scale->timemodified = mktime();

        $this->scale[1] = $scale;
        $temp = explode(',', $scale->scale);
        $this->scalemax[1] = count($temp) -1;

        $scale = new stdClass();

        $scale->id          = 3;
        $scale->name        = 'unittestscale3';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Loner, Contentious, Disinterested, Participative, Follower, Leader';
        $scale->description = 'Describes the level of teamwork of a student.';
        $scale->timemodified = mktime();
        $temp  = explode(',', $scale->scale);
        $scale->max         = count($temp) -1;

        $this->scale[2] = $scale;
        $temp = explode(',', $scale->scale);
        $this->scalemax[2] = count($temp) -1;

        $scale->id          = 4;
        $scale->name        = 'unittestscale4';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Does not understand theory, Understands theory but fails practice, Manages through, Excels';
        $scale->description = 'Level of expertise at a technical task, with a theoretical framework.';
        $scale->timemodified = mktime();
        $temp  = explode(',', $scale->scale);
        $scale->max         = count($temp) -1;

        $this->scale[3] = $scale;
        $temp = explode(',', $scale->scale);
        $this->scalemax[3] = count($temp) -1;

        $scale->id          = 5;
        $scale->name        = 'unittestscale5';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Insufficient, Acceptable, Excellent.';
        $scale->description = 'Description of skills.';
        $scale->timemodified = mktime();
        $temp  = explode(',', $scale->scale);
        $scale->max         = count($temp) -1;

        $this->scale[4] = $scale;
        $temp = explode(',', $scale->scale);
        $this->scalemax[4] = count($temp) -1;

        $this->loaded_tables[] = 'scale';
    }

    /**
     * Load grade_category data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_categories() {
        $id = 1;

        $course_category = new stdClass();
        $course_category->id = $id++;
        $course_category->courseid    = $this->courseid;
        $course_category->fullname = "Course grade category";
        $course_category->path = null;
        $course_category->parent = null;
        $course_category->aggregate = GRADE_AGGREGATE_MEAN;
        $course_category->timecreated = $course_category->timemodified = time();
        
        $this->grade_categories[] = $course_category;

        $grade_category = new stdClass();

        $grade_category->id          = $id++;
        $grade_category->fullname    = 'unittestcategory1';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $course_category->id;
        $grade_category->timecreated = $grade_category->timemodified = mktime();
        $grade_category->depth = 2;

        $grade_category->path = '/'.$course_category->id.'/'.$grade_category->id.'/';
        $this->grade_categories[] = $grade_category;

        $grade_category = new stdClass();

        $grade_category->id          = $id++;
        $grade_category->fullname    = 'unittestcategory2';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $this->grade_categories[1]->id;
        $grade_category->timecreated = $grade_category->timemodified = mktime();
        $grade_category->depth = 3;

        $grade_category->path = $this->grade_categories[1]->path.$grade_category->id.'/';
        $this->grade_categories[] = $grade_category;

        $grade_category = new stdClass();

        $grade_category->id          = $id++;
        $grade_category->fullname    = 'unittestcategory3';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $this->grade_categories[1]->id;
        $grade_category->timecreated = $grade_category->timemodified = mktime();
        $grade_category->depth = 3;

        $grade_category->path = $this->grade_categories[1]->path.$grade_category->id.'/';
        $this->grade_categories[] = $grade_category;

        // A category with no parent, but grade_items as children

        $grade_category = new stdClass();

        $grade_category->id          = $id++;
        $grade_category->fullname    = 'level1category';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $course_category->id;
        $grade_category->timecreated = $grade_category->timemodified = mktime();
        $grade_category->depth = 2;

        $grade_category->path = '/'.$course_category->id.'/'.$grade_category->id.'/';
        $this->grade_categories[] = $grade_category;

        $this->loaded_tables[] = 'grade_categories';
    }

    /**
     * Load module entries in modules table\
     */
    function load_modules() {
        $module = new stdClass();
        $module->id   = 1;
        $module->name = 'assignment';
        $this->modules[] = $module;

        $module = new stdClass();
        $module->id   = 2;
        $module->name = 'quiz';
        $this->modules[] = $module;

        $module = new stdClass();
        $module->id   = 3;
        $module->name = 'forum';
        $this->modules[] = $module;

        $this->loaded_tables[] = 'modules';
    }

    /**
     * Load test quiz data into the database
     */
    function load_quiz() {
        $quiz = new stdClass();
        $quiz->id = 1;
        $quiz->course = $this->courseid;
        $quiz->name = 'test quiz';
        $quiz->intro = 'let us quiz you!';
        $quiz->questions = '1,2';
        $this->quizzes[] = $quiz;

        $quiz = new stdClass();
        $quiz->id = 2;
        $quiz->course = $this->courseid;
        $quiz->name = 'test quiz 2';
        $quiz->intro = 'let us quiz you again!';
        $quiz->questions = '1,3';
        $this->quizzes[] = $quiz;

        $this->loaded_tables[] = 'quiz';
    }

    /**
     * Load test assignment data into the database
     */
    function load_assignment() {
        $assignment = new stdClass();
        $assignment->id = 1;
        $assignment->course = $this->courseid;
        $assignment->name = 'test assignment';
        $assignment->description = 'What is the purpose of life?';
        $this->assignments[] = $assignment;

        $this->loaded_tables[] = 'assignment';
    }

    /**
     * Load test forum data into the database
     */
    function load_forum() {
        $forum = new stdClass();
        $forum->id = 1;
        $forum->course = $this->courseid;
        $forum->name = 'test forum 1';
        $forum->intro = 'Another test forum';
        $this->forums[] = $forum;
    
        $forum = new stdClass();
        $forum->id = 2;
        $forum->course = $this->courseid;
        $forum->name = 'test forum 2';
        $forum->intro = 'Another test forum';
        $this->forums[] = $forum;
    
        $forum = new stdClass();
        $forum->id = 3;
        $forum->course = $this->courseid;
        $forum->name = 'test forum 3';
        $forum->intro = 'Another test forum';
        $this->forums[] = $forum;

        $this->loaded_tables[] = 'forum';
    }

    /**
     * Load module instance entries in course_modules table
     */
    function load_course_modules() {
        if (!in_array('modules', $this->loaded_tables)) {
            $this->load_modules();
        }
        if (!in_array('quiz', $this->loaded_tables)) {
            $this->load_quiz();
        }
        if (!in_array('assignment', $this->loaded_tables)) {
            $this->load_assignment();
        }
        if (!in_array('forum', $this->loaded_tables)) {
            $this->load_forum();
        }

        $course_module = new stdClass();
        $course_module->id = 1;
        $course_module->course = $this->courseid;
        $course_module->module = $this->modules[0]->id;
        $course_module->instance = $this->assignments[0]->id;
        $this->course_modules[] = $course_module;

        $course_module = new stdClass();
        $course_module->id = 2;
        $course_module->course = $this->courseid;
        $course_module->module = $this->modules[1]->id;
        $course_module->instance = $this->quizzes[0]->id;
        $this->course_modules[] = $course_module;

        $course_module = new stdClass();
        $course_module->id = 3;
        $course_module->course = $this->courseid;
        $course_module->module = $this->modules[1]->id;
        $course_module->instance = $this->quizzes[1]->id;
        $this->course_modules[] = $course_module;

        $course_module = new stdClass();
        $course_module->id = 4;
        $course_module->course = $this->courseid;
        $course_module->module = $this->modules[2]->id;
        $course_module->instance = $this->forums[0]->id;
        $this->course_modules[] = $course_module;

        $course_module = new stdClass();
        $course_module->id = 5;
        $course_module->course = $this->courseid;
        $course_module->module = $this->modules[2]->id;
        $course_module->instance = $this->forums[1]->id;
        $this->course_modules[] = $course_module;

        $course_module = new stdClass();
        $course_module->id = 6;
        $course_module->course = $this->courseid;
        $course_module->module = $this->modules[2]->id;
        $course_module->instance = $this->forums[2]->id;
        $this->course_modules[] = $course_module;

        $this->loaded_tables[] = 'course_modules';
    }

    /**
     * Load grade_item data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_items() {
        if (!in_array('scale', $this->loaded_tables)) {
            $this->load_scale();
        }
        if (!in_array('grade_categories', $this->loaded_tables)) {
            $this->load_grade_categories();
        }
        if (!in_array('quiz', $this->loaded_tables)) {
            $this->load_quiz();
        }
        if (!in_array('assignment', $this->loaded_tables)) {
            $this->load_assignment();
        }
        if (!in_array('forum', $this->loaded_tables)) {
            $this->load_forum();
        }
        
        $id = 1;
        
        $course_category = $this->grade_categories[0];

        $grade_item = new stdClass();

        $grade_item->id = $id++;
        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitem1';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminstance = $this->quizzes[0]->id;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 30;
        $grade_item->grademax = 110;
        $grade_item->itemnumber = 1;
        $grade_item->idnumber = 'item id 0';
        $grade_item->iteminfo = 'Grade item 0 used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 3;
        $grade_item->needsupdate = false;

        $this->grade_items[] = $grade_item;

        $grade_item = new stdClass();

        $grade_item->id = $id++;
        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[2]->id;
        $grade_item->itemname = 'unittestgradeitem2';
        $grade_item->itemtype = 'import';
        $grade_item->itemmodule = 'assignment';
        $grade_item->calculation = '= ##gi'.$this->grade_items[0]->id.'## + 30 + [[item id 0]] - [[item id 0]]';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->iteminstance = $this->assignments[0]->id;
        $grade_item->itemnumber = null;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 1 used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 4;

        $this->grade_items[] = $grade_item;

        $grade_item = new stdClass();

        $grade_item->id = $id++;
        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[3]->id;
        $grade_item->itemname = 'unittestgradeitem3';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'forum';
        $grade_item->iteminstance = $this->forums[0]->id;
        $grade_item->gradetype = GRADE_TYPE_SCALE;
        $grade_item->scaleid = $this->scale[0]->id;
        $grade_item->grademin = 0;
        $grade_item->grademax = $this->scalemax[0];
        $grade_item->iteminfo = 'Grade item 2 used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 6;

        $this->grade_items[] = $grade_item;

        // Load grade_items associated with the 3 categories
        $grade_item = new stdClass();

        $grade_item->id = $id++;
        $grade_item->courseid = $this->courseid;
        $grade_item->iteminstance = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitemcategory1';
        $grade_item->needsupdate = 0;
        $grade_item->itemtype = 'category';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 3 used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 1;

        $this->grade_items[] = $grade_item;

        $grade_item = new stdClass();

        $grade_item->id = $id++;
        $grade_item->courseid = $this->courseid;
        $grade_item->iteminstance = $this->grade_categories[2]->id;
        $grade_item->itemname = 'unittestgradeitemcategory2';
        $grade_item->itemtype = 'category';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->needsupdate = 0;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 4 used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 2;

        $this->grade_items[] = $grade_item;

        $grade_item = new stdClass();

        $grade_item->id = $id++;
        $grade_item->courseid = $this->courseid;
        $grade_item->iteminstance = $this->grade_categories[3]->id;
        $grade_item->itemname = 'unittestgradeitemcategory3';
        $grade_item->itemtype = 'category';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->needsupdate = true;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 5 used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 5;

        $this->grade_items[] = $grade_item;

        // Orphan grade_item
        $grade_item = new stdClass();

        $grade_item->id = $id++;
        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $course_category->id;
        $grade_item->itemname = 'unittestorphangradeitem1';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminstance = $this->quizzes[1]->id;
        $grade_item->itemnumber = 0;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 10;
        $grade_item->grademax = 120;
        $grade_item->locked = time();
        $grade_item->iteminfo = 'Orphan Grade 6 item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 7;

        $this->grade_items[] = $grade_item;

        // 2 grade items under level1category
        $grade_item = new stdClass();

        $grade_item->id = $id++;
        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[4]->id;
        $grade_item->itemname = 'singleparentitem1';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'forum';
        $grade_item->iteminstance = $this->forums[1]->id;
        $grade_item->gradetype = GRADE_TYPE_SCALE;
        $grade_item->scaleid = $this->scale[0]->id;
        $grade_item->grademin = 0;
        $grade_item->grademax = $this->scalemax[0];
        $grade_item->iteminfo = 'Grade item 7 used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 9;

        $this->grade_items[] = $grade_item;

        $grade_item = new stdClass();

        $grade_item->id = $id++;
        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[4]->id;
        $grade_item->itemname = 'singleparentitem2';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'forum';
        $grade_item->iteminstance = $this->forums[2]->id;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 8 used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 10;

        $this->grade_items[] = $grade_item;

        // Grade_item for level1category
        $grade_item = new stdClass();

        $grade_item->id = $id++;
        $grade_item->courseid = $this->courseid;
        $grade_item->itemname = 'grade_item for level1 category';
        $grade_item->itemtype = 'category';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminstance = $this->grade_categories[4]->id;
        $grade_item->needsupdate = true;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Orphan Grade item 9 used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 8;

        $this->grade_items[] = $grade_item;

        // Manual grade_item
        $grade_item = new stdClass();

        $grade_item->id = $id++;
        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $course_category->id;
        $grade_item->itemname = 'manual grade_item';
        $grade_item->itemtype = 'manual';
        $grade_item->itemnumber = 0;
        $grade_item->needsupdate = false;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Manual grade item 10 used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();

        $this->grade_items[] = $grade_item;

        $this->loaded_tables[] = 'grade_items';

    }

    /**
     * Load grade_grades data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_grades() {
        if (!in_array('grade_items', $this->loaded_tables)) {
            $this->load_grade_items();
        }
        $id = 1;
        $course_category = $this->grade_categories[0];

        // Grades for grade_item 1
        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[0]->id;
        $grade->grade_item = $this->grade_items[0];
        $grade->userid = 1;
        $grade->parent = $course_category->id;
        $grade->rawgrade = 15; // too small
        $grade->finalgrade = 30;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = 'Thumbs down';
        $grade->informationformat = FORMAT_PLAIN;
        $grade->feedback = 'Good, but not good enough..';
        $grade->feedbackformat = FORMAT_PLAIN;

        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[0]->id;
        $grade->parent = $course_category->id;
        $grade->userid = 2;
        $grade->rawgrade = 40;
        $grade->finalgrade = 40;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[0]->id;
        $grade->parent = $course_category->id;
        $grade->userid = 3;
        $grade->rawgrade = 170; // too big
        $grade->finalgrade = 110;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;


        // No raw grades for grade_item 2 - it is calculated

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[1]->id;
        $grade->userid = 1;
        $grade->finalgrade = 60;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[1]->id;
        $grade->userid = 2;
        $grade->finalgrade = 70;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[1]->id;
        $grade->userid = 3;
        $grade->finalgrade = 100;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;


        // Grades for grade_item 3

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[2]->id;
        $grade->userid = 1;
        $grade->rawgrade = 2;
        $grade->finalgrade = 6;
        $grade->scaleid = $this->scale[3]->id;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[2]->id;
        $grade->userid = 2;
        $grade->rawgrade = 3;
        $grade->finalgrade = 2;
        $grade->scaleid = $this->scale[3]->id;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[2]->id;
        $grade->userid = 3;
        $grade->rawgrade = 1;
        $grade->finalgrade = 3;
        $grade->scaleid = $this->scale[3]->id;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        // Grades for grade_item 7

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[6]->id;
        $grade->userid = 1;
        $grade->rawgrade = 97;
        $grade->finalgrade = 69;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[6]->id;
        $grade->userid = 2;
        $grade->rawgrade = 49;
        $grade->finalgrade = 87;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[6]->id;
        $grade->userid = 3;
        $grade->rawgrade = 67;
        $grade->finalgrade = 94;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        // Grades for grade_item 8

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[7]->id;
        $grade->userid = 2;
        $grade->rawgrade = 3;
        $grade->finalgrade = 3;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[7]->id;
        $grade->userid = 3;
        $grade->rawgrade = 6;
        $grade->finalgrade = 6;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        // Grades for grade_item 9

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[8]->id;
        $grade->userid = 1;
        $grade->rawgrade = 20;
        $grade->finalgrade = 20;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[8]->id;
        $grade->userid = 2;
        $grade->rawgrade = 50;
        $grade->finalgrade = 50;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->id = $id++;
        $grade->itemid = $this->grade_items[7]->id;
        $grade->userid = 3;
        $grade->rawgrade = 100;
        $grade->finalgrade = 100;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        $this->grade_grades[] = $grade;

        $this->loaded_tables[] = 'grade_grades';
    }

    /**
     * Load grade_outcome data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_outcomes() {
        if (!in_array('scale', $this->loaded_tables)) {
            $this->load_scale();
        }
        $id = 1;
        
        // Calculation for grade_item 1
        $grade_outcome = new stdClass();
        $grade_outcome->id = $id++;
        $grade_outcome->shortname = 'Team work';
        $grade_outcome->timecreated = mktime();
        $grade_outcome->timemodified = mktime();
        $grade_outcome->scaleid = $this->scale[2]->id;

        $this->grade_outcomes[] = $grade_outcome;

        // Calculation for grade_item 2
        $grade_outcome = new stdClass();
        $grade_outcome->id = $id++;
        $grade_outcome->shortname = 'Complete circuit board';
        $grade_outcome->timecreated = mktime();
        $grade_outcome->timemodified = mktime();
        $grade_outcome->scaleid = $this->scale[3]->id;

        $this->grade_outcomes[] = $grade_outcome;

        // Calculation for grade_item 3
        $grade_outcome = new stdClass();
        $grade_outcome->id = $id++;
        $grade_outcome->shortname = 'Debug Java program';
        $grade_outcome->timecreated = mktime();
        $grade_outcome->timemodified = mktime();
        $grade_outcome->scaleid = $this->scale[4]->id;

        $this->grade_outcomes[] = $grade_outcome;

        $this->loaded_tables[] = 'grade_outcomes';
    }

/**
 * No unit tests here
 */

}

?>
