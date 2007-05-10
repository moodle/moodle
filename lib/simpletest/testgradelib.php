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
require_once($CFG->libdir . '/ddllib.php');

/**
 * Here is a brief explanation of the test data set up in these unit tests.
 * category1 => array(category2 => array(grade_item1, grade_item2), category3 => array(grade_item3))
 * 3 users for 3 grade_items
 */
class gradelib_test extends UnitTestCase {
   
    /**
     * Each database table receives a number of test entries. These are saved as
     * arrays of stcClass objects available to this class. This means that
     * every test has access to these test data. The order of the following array is 
     * crucial, because of the interrelationships between objects.
     */
    var $tables = array('grade_categories',
                        'grade_items',
                        'grade_calculations',
                        'scale',
                        'grade_grades_raw',
                        'grade_grades_final',
                        'grade_grades_text',
                        'grade_outcomes',
                        'grade_history');

    var $grade_items = array();
    var $grade_categories = array();
    var $grade_calculations = array();
    var $grade_grades_raw = array();
    var $grade_grades_final = array();
    var $grade_grades_text = array();
    var $grade_outcomes = array();
    var $grade_history = array();
    var $scale = array();

    var $courseid = 1;
    var $userid = 1;

    /**
     * Create temporary test tables and entries in the database for these tests.
     * These tests have to work on a brand new site. 
     * Override $CFG->prefix while these tests run.
     */
    function setUp() {
        global $CFG;
        $CFG->old_prefix = $CFG->prefix;
        $CFG->prefix .= 'unittest_';
        if (!$this->create_test_tables()) {
            die("Could not create all the test tables!");
        }
        
        foreach ($this->tables as $table) {
            $function = "load_$table";
            $this->$function();
        }
    }

    function create_test_tables() {
        $result = true;
    
        /// Define table grade_items to be created
        $table = new XMLDBTable('grade_items');
        
        if (!table_exists($table)) {
            /// Adding fields to table grade_items
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('categoryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('itemname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('itemtype', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('itemmodule', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('iteminstance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('itemnumber', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('iteminfo', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('idnumber', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
            $table->addFieldInfo('gradetype', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('grademax', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '100');
            $table->addFieldInfo('grademin', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('outcomeid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null, null);
            $table->addFieldInfo('gradepass', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('multfactor', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '1.0');
            $table->addFieldInfo('plusfactor', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('locked', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('needsupdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

            /// Adding keys to table grade_items
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
            $table->addKeyInfo('categoryid', XMLDB_KEY_FOREIGN, array('categoryid'), 'grade_categories', array('id'));
            $table->addKeyInfo('scaleid', XMLDB_KEY_FOREIGN, array('scaleid'), 'scale', array('id'));
            $table->addKeyInfo('outcomeid', XMLDB_KEY_FOREIGN, array('outcomeid'), 'grade_outcomes', array('id'));

            /// Launch create table for grade_items
            $result = $result && create_table($table, true, false);
        }
        
        /// Define table grade_categories to be created
        $table = new XMLDBTable('grade_categories');
        
        if ($result && !table_exists($table)) {

            /// Adding fields to table grade_categories
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('parent', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('depth', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('path', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('fullname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('aggregation', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('keephigh', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('droplow', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

            /// Adding keys to table grade_categories
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
            $table->addKeyInfo('parent', XMLDB_KEY_FOREIGN, array('parent'), 'grade_categories', array('id'));

            /// Launch create table for grade_categories
            $result = $result && create_table($table, true, false);
        }

        /// Define table grade_calculations to be created
        $table = new XMLDBTable('grade_calculations');
        
        if ($result && !table_exists($table)) {

            /// Adding fields to table grade_calculations
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('calculation', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

            /// Adding keys to table grade_calculations
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
            $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

            /// Launch create table for grade_calculations
            $result = $result && create_table($table, true, false);
        }

        /// Define table grade_grades_text to be created
        $table = new XMLDBTable('grade_grades_text');
        
        if ($result && !table_exists($table)) {

            /// Adding fields to table grade_grades_text
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('information', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
            $table->addFieldInfo('informationformat', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('feedback', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
            $table->addFieldInfo('feedbackformat', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null, null);
            $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null, null);

            /// Adding keys to table grade_grades_text
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
            $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
            $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

            /// Launch create table for grade_grades_text
            $result = $result && create_table($table, true, false);
        }

        /// Define table grade_outcomes to be created
        $table = new XMLDBTable('grade_outcomes');

        if ($result && !table_exists($table)) {

            /// Adding fields to table grade_outcomes
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

            /// Adding keys to table grade_outcomes
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
            $table->addKeyInfo('scaleid', XMLDB_KEY_FOREIGN, array('scaleid'), 'scale', array('id'));
            $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

            /// Launch create table for grade_outcomes
            $result = $result && create_table($table, true, false);
        }

        /// Define table grade_history to be created
        $table = new XMLDBTable('grade_history');

        if ($result && !table_exists($table)) {

            /// Adding fields to table grade_history
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('oldgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
            $table->addFieldInfo('newgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
            $table->addFieldInfo('note', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null);
            $table->addFieldInfo('howmodified', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, 'manual');
            $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

            /// Adding keys to table grade_history
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
            $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
            $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

            /// Launch create table for grade_history
            $result = $result && create_table($table, true, false);
        }

        /// Define table grade_grades_final to be created
        $table = new XMLDBTable('grade_grades_final');

        if ($result && !table_exists($table)) {

            /// Adding fields to table grade_grades_final
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('gradevalue', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
            $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('locked', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('exported', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

            /// Adding keys to table grade_grades_final
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
            $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
            $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

            /// Launch create table for grade_grades_final
            $result = $result && create_table($table, true, false);
        }

        /// Define table grade_grades_raw to be created
        $table = new XMLDBTable('grade_grades_raw');

        if ($result && !table_exists($table)) {

            /// Adding fields to table grade_grades_raw
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('gradevalue', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
            $table->addFieldInfo('grademax', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '100');
            $table->addFieldInfo('grademin', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

            /// Adding keys to table grade_grades_raw
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
            $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
            $table->addKeyInfo('scaleid', XMLDB_KEY_FOREIGN, array('scaleid'), 'scale', array('id'));
            $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

            /// Launch create table for grade_grades_raw
            $result = $result && create_table($table, true, false);
        }

        /// Define table scale to be created
        $table = new XMLDBTable('scale');

        if ($result && !table_exists($table)) {

            /// Adding fields to table scale
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('scale', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');

            /// Adding keys to table scale
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

            /// Adding indexes to table scale
            $table->addIndexInfo('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));

            /// Launch create table for scale
            $result = $result && create_table($table, true, false);
        }
        
        return $result; 
    }
    
    /**
     * Drop test tables from DB.
     * Restore original $CFG->prefix.
     */
    function tearDown() {
        global $CFG;
        foreach ($this->tables as $table) {
            delete_records($table);
            if (count($this->$table) > 0) {
                unset ($this->$table);
            }
        } 
        $CFG->prefix = $CFG->old_prefix;
    }

    /**
     * In PHP5, this is called to drop the test tables after all the tests have been performed. 
     * Until we move to PHP5, I know no easy way to accomplish this.
     */
    function __destruct() {
        foreach ($this->tables as $table) {
            $xmldbtable = new XMLDBTable($table);
            drop_table($xmldbtable, true, false);
        }
    }
   
    /**
     * Load grade_category data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_categories() {
        $grade_category = new stdClass();
        
        $grade_category->fullname    = 'unittestcategory1';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->keephigh    = 100;
        $grade_category->droplow     = 10;
        $grade_category->hidden      = 0;
        $grade_category->timecreated = mktime();
        $grade_category->timemodified = mktime();
        
        if ($grade_category->id = insert_record('grade_categories', $grade_category)) {
            $this->grade_categories[] = $grade_category;
        } 
        
        $grade_category = new stdClass();
        
        $grade_category->fullname    = 'unittestcategory2';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MODE;
        $grade_category->keephigh    = 100;
        $grade_category->droplow     = 10;
        $grade_category->hidden      = 0;
        $grade_category->parent      = $this->grade_categories[0]->id;
        $grade_category->timecreated = mktime();
        $grade_category->timemodified = mktime();
        
        if ($grade_category->id = insert_record('grade_categories', $grade_category)) {
            $this->grade_categories[] = $grade_category;
        } 
        
        $grade_category = new stdClass();
        
        $grade_category->fullname    = 'unittestcategory3';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MODE;
        $grade_category->keephigh    = 100;
        $grade_category->droplow     = 10;
        $grade_category->hidden      = 0;
        $grade_category->parent      = $this->grade_categories[0]->id;
        $grade_category->timecreated = mktime();
        $grade_category->timemodified = mktime();
        
        if ($grade_category->id = insert_record('grade_categories', $grade_category)) {
            $this->grade_categories[] = $grade_category;
        } 
    }

    /**
     * Load grade_item data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_items() {
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitem1';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminstance = 1;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 30;
        $grade_item->grademax = 140;
        $grade_item->itemnumber = 1;
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[] = $grade_item;
        }
        
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitem2';
        $grade_item->itemtype = 'import';
        $grade_item->itemmodule = 'assignment';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->iteminstance = 2;
        $grade_item->itemnumber = null;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->locked = mktime() + 240000;
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        
        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[] = $grade_item;
        }

        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[2]->id;
        $grade_item->itemname = 'unittestgradeitem3';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'forum';
        $grade_item->iteminstance = 3;
        $grade_item->gradetype = GRADE_TYPE_SCALE;
        $grade_item->scaleid = 1;
        $grade_item->grademin = 0;
        $grade_item->grademax = 7;
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[] = $grade_item;
        }

        // Load grade_items associated with the 3 categories
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->iteminstance = $this->grade_categories[0]->id;
        $grade_item->itemname = 'unittestgradeitemcategory1';
        $grade_item->itemtype = 'category';
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[] = $grade_item;
        }
        
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->iteminstance = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitemcategory2';
        $grade_item->itemtype = 'category';
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[] = $grade_item;
        }

        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->iteminstance = $this->grade_categories[2]->id;
        $grade_item->itemname = 'unittestgradeitemcategory3';
        $grade_item->itemtype = 'category';
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[] = $grade_item;
        }

    }

    /**
     * Load grade_calculation data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_calculations() {
        // Calculation for grade_item 1
        $grade_calculation = new stdClass();
        $grade_calculation->itemid = $this->grade_items[0]->id;
        $grade_calculation->calculation = '[unittestgradeitem1] * 1.4 - 3';
        $grade_calculation->timecreated = mktime();
        $grade_calculation->timemodified = mktime();
        
        if ($grade_calculation->id = insert_record('grade_calculations', $grade_calculation)) {
            $this->grade_calculations[] = $grade_calculation;
            $this->grade_items[0]->calculation = $grade_calculation;
        } 
        
        // Calculation for grade_item 2
        $grade_calculation = new stdClass();
        $grade_calculation->itemid = $this->grade_items[1]->id;
        $grade_calculation->calculation = '[unittestgradeitem2] + 3';
        $grade_calculation->timecreated = mktime();
        $grade_calculation->timemodified = mktime();
        
        if ($grade_calculation->id = insert_record('grade_calculations', $grade_calculation)) {
            $this->grade_calculations[] = $grade_calculation;
            $this->grade_items[1]->calculation = $grade_calculation;
        } 
        
        // Calculation for grade_item 3
        $grade_calculation = new stdClass();
        $grade_calculation->itemid = $this->grade_items[2]->id;
        $grade_calculation->calculation = '[unittestgradeitem3] / 2 + 40';
        $grade_calculation->timecreated = mktime();
        $grade_calculation->timemodified = mktime();
        
        if ($grade_calculation->id = insert_record('grade_calculations', $grade_calculation)) {
            $this->grade_calculations[] = $grade_calculation;
            $this->grade_items[2]->calculation = $grade_calculation;
        } 
    }

    /**
     * Load scale data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_scale() {
        $scale = new stdClass();
        
        $scale->name        = 'unittestscale1';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Way off topic, Not very helpful, Fairly neutral, Fairly helpful, Supportive, Some good information, Perfect answer!';
        $scale->description = 'This scale defines some of qualities that make posts helpful within the Moodle help forums.\n Your feedback will help others see how their posts are being received.';
        $scale->timemodified = mktime();
        
        if ($scale->id = insert_record('scale', $scale)) {
            $this->scale[] = $scale;
        } 

        $scale = new stdClass();
        
        $scale->name        = 'unittestscale2';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $scale->description = 'This scale is used to mark standard assignments.';
        $scale->timemodified = mktime();
        
        if ($scale->id = insert_record('scale', $scale)) {
            $this->scale[] = $scale;
        } 

        $scale = new stdClass();
        
        $scale->name        = 'unittestscale3';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Loner, Contentious, Disinterested, Participative, Follower, Leader';
        $scale->description = 'Describes the level of teamwork of a student.';
        $scale->timemodified = mktime();
        
        if ($scale->id = insert_record('scale', $scale)) {
            $this->scale[] = $scale;
        } 

        $scale->name        = 'unittestscale4';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Does not understand theory, Understands theory but fails practice, Manages through, Excels';
        $scale->description = 'Level of expertise at a technical task, with a theoretical framework.';
        $scale->timemodified = mktime();
        
        if ($scale->id = insert_record('scale', $scale)) {
            $this->scale[] = $scale;
        }

        $scale->name        = 'unittestscale5';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Insufficient, Acceptable, Excellent.';
        $scale->description = 'Description of skills.';
        $scale->timemodified = mktime();
        
        if ($scale->id = insert_record('scale', $scale)) {
            $this->scale[] = $scale;
        }
    }

    /**
     * Load grade_grades_raw data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_grades_raw() {
        // Grades for grade_item 1
        $grade_raw = new stdClass();
        $grade_raw->itemid = $this->grade_items[0]->id;
        $grade_raw->userid = 1;
        $grade_raw->gradevalue = 72;
        $grade_raw->timecreated = mktime();
        $grade_raw->timemodified = mktime();

        if ($grade_raw->id = insert_record('grade_grades_raw', $grade_raw)) {
            $this->grade_grades_raw[] = $grade_raw;
        }
        
        $grade_raw = new stdClass();
        $grade_raw->itemid = $this->grade_items[0]->id;
        $grade_raw->userid = 2;
        $grade_raw->gradevalue = 78;
        $grade_raw->timecreated = mktime();
        $grade_raw->timemodified = mktime();

        if ($grade_raw->id = insert_record('grade_grades_raw', $grade_raw)) {
            $this->grade_grades_raw[] = $grade_raw;
        }
        
        $grade_raw = new stdClass();
        $grade_raw->itemid = $this->grade_items[0]->id;
        $grade_raw->userid = 3;
        $grade_raw->gradevalue = 68;
        $grade_raw->timecreated = mktime();
        $grade_raw->timemodified = mktime();

        if ($grade_raw->id = insert_record('grade_grades_raw', $grade_raw)) {
            $this->grade_grades_raw[] = $grade_raw;
        }

        // Grades for grade_item 2

        $grade_raw = new stdClass();
        $grade_raw->itemid = $this->grade_items[1]->id;
        $grade_raw->userid = 1;
        $grade_raw->gradevalue = 66;
        $grade_raw->timecreated = mktime();
        $grade_raw->timemodified = mktime();

        if ($grade_raw->id = insert_record('grade_grades_raw', $grade_raw)) {
            $this->grade_grades_raw[] = $grade_raw;
        }
        
        $grade_raw = new stdClass();
        $grade_raw->itemid = $this->grade_items[1]->id;
        $grade_raw->userid = 2;
        $grade_raw->gradevalue = 84;
        $grade_raw->timecreated = mktime();
        $grade_raw->timemodified = mktime();

        if ($grade_raw->id = insert_record('grade_grades_raw', $grade_raw)) {
            $this->grade_grades_raw[] = $grade_raw;
        }
        
        $grade_raw = new stdClass();
        $grade_raw->itemid = $this->grade_items[1]->id;
        $grade_raw->userid = 3;
        $grade_raw->gradevalue = 91;
        $grade_raw->timecreated = mktime();
        $grade_raw->timemodified = mktime();

        if ($grade_raw->id = insert_record('grade_grades_raw', $grade_raw)) {
            $this->grade_grades_raw[] = $grade_raw;
        }

        // Grades for grade_item 3

        $grade_raw = new stdClass();
        $grade_raw->itemid = $this->grade_items[2]->id;
        $grade_raw->userid = 1;
        $grade_raw->gradevalue = 2;
        $grade_raw->scaleid = $this->scale[3]->id;
        $grade_raw->timecreated = mktime();
        $grade_raw->timemodified = mktime();

        if ($grade_raw->id = insert_record('grade_grades_raw', $grade_raw)) {
            $this->grade_grades_raw[] = $grade_raw;
        }
        
        $grade_raw = new stdClass();
        $grade_raw->itemid = $this->grade_items[2]->id;
        $grade_raw->userid = 2;
        $grade_raw->gradevalue = 3;
        $grade_raw->scaleid = $this->scale[3]->id;
        $grade_raw->timecreated = mktime();
        $grade_raw->timemodified = mktime();

        if ($grade_raw->id = insert_record('grade_grades_raw', $grade_raw)) {
            $this->grade_grades_raw[] = $grade_raw;
        }
        
        $grade_raw = new stdClass();
        $grade_raw->itemid = $this->grade_items[2]->id;
        $grade_raw->userid = 3;
        $grade_raw->gradevalue = 1;
        $grade_raw->scaleid = $this->scale[3]->id;
        $grade_raw->timecreated = mktime();
        $grade_raw->timemodified = mktime();

        if ($grade_raw->id = insert_record('grade_grades_raw', $grade_raw)) {
            $this->grade_grades_raw[] = $grade_raw;
        }
    }

    /**
     * Load grade_grades_final data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_grades_final() {
        // Grades for grade_item 1

        $grade_final = new stdClass();
        $grade_final->itemid = $this->grade_items[0]->id;
        $grade_final->userid = 1;
        $grade_final->gradevalue = 97.8;
        $grade_final->timecreated = mktime();
        $grade_final->timemodified = mktime();

        if ($grade_final->id = insert_record('grade_grades_final', $grade_final)) {
            $this->grade_grades_final[] = $grade_final;
        } 
        
        $grade_final = new stdClass();
        $grade_final->itemid = $this->grade_items[0]->id;
        $grade_final->userid = 2;
        $grade_final->gradevalue = 106.2;
        $grade_final->timecreated = mktime();
        $grade_final->timemodified = mktime();
        $grade_final->locked = true; 

        if ($grade_final->id = insert_record('grade_grades_final', $grade_final)) {
            $this->grade_grades_final[] = $grade_final;
        }

        $grade_final = new stdClass();
        $grade_final->itemid = $this->grade_items[0]->id;
        $grade_final->userid = 3;
        $grade_final->gradevalue = 92.2;
        $grade_final->timecreated = mktime();
        $grade_final->timemodified = mktime();
        $grade_final->locked = false; 

        if ($grade_final->id = insert_record('grade_grades_final', $grade_final)) {
            $this->grade_grades_final[] = $grade_final;
        } 
        
        // Grades for grade_item 2

        $grade_final = new stdClass();
        $grade_final->itemid = $this->grade_items[1]->id;
        $grade_final->userid = 1;
        $grade_final->gradevalue = 69;
        $grade_final->timecreated = mktime();
        $grade_final->timemodified = mktime();
        $grade_final->locked = true; 

        if ($grade_final->id = insert_record('grade_grades_final', $grade_final)) {
            $this->grade_grades_final[] = $grade_final;
        } 
        
        $grade_final = new stdClass();
        $grade_final->itemid = $this->grade_items[1]->id;
        $grade_final->userid = 2;
        $grade_final->gradevalue = 87;
        $grade_final->timecreated = mktime();
        $grade_final->timemodified = mktime();
        $grade_final->locked = true; 

        if ($grade_final->id = insert_record('grade_grades_final', $grade_final)) {
            $this->grade_grades_final[] = $grade_final;
        }

        $grade_final = new stdClass();
        $grade_final->itemid = $this->grade_items[1]->id;
        $grade_final->userid = 3;
        $grade_final->gradevalue = 94;
        $grade_final->timecreated = mktime();
        $grade_final->timemodified = mktime();
        $grade_final->locked = false; 

        if ($grade_final->id = insert_record('grade_grades_final', $grade_final)) {
            $this->grade_grades_final[] = $grade_final;
        } 
        
        // Grades for grade_item 3

        $grade_final = new stdClass();
        $grade_final->itemid = $this->grade_items[2]->id;
        $grade_final->userid = 1;
        $grade_final->gradevalue = 70.5;
        $grade_final->timecreated = mktime();
        $grade_final->timemodified = mktime();
        $grade_final->locked = true; 

        if ($grade_final->id = insert_record('grade_grades_final', $grade_final)) {
            $this->grade_grades_final[] = $grade_final;
        } 
        
        $grade_final = new stdClass();
        $grade_final->itemid = $this->grade_items[2]->id;
        $grade_final->userid = 2;
        $grade_final->gradevalue = 80.5;
        $grade_final->timecreated = mktime();
        $grade_final->timemodified = mktime();
        $grade_final->locked = true; 

        if ($grade_final->id = insert_record('grade_grades_final', $grade_final)) {
            $this->grade_grades_final[] = $grade_final;
        }

        $grade_final = new stdClass();
        $grade_final->itemid = $this->grade_items[2]->id;
        $grade_final->userid = 3;
        $grade_final->gradevalue = 64.5;
        $grade_final->timecreated = mktime();
        $grade_final->timemodified = mktime();
        $grade_final->locked = false; 

        if ($grade_final->id = insert_record('grade_grades_final', $grade_final)) {
            $this->grade_grades_final[] = $grade_final;
        } 
    }
    
    /**
     * Load grade_grades_text data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_grades_text() {
        $grade_grades_text = new stdClass();

        $grade_grades_text->itemid = $this->grade_grades_raw[0]->itemid;
        $grade_grades_text->userid = $this->grade_grades_raw[0]->userid;
        $grade_grades_text->information = 'Thumbs down';
        $grade_grades_text->informationformat = FORMAT_PLAIN;
        $grade_grades_text->feedback = 'Good, but not good enough..';
        $grade_grades_text->feedbackformat = FORMAT_PLAIN;
        
        if ($grade_grades_text->id = insert_record('grade_grades_text', $grade_grades_text)) {
            $this->grade_grades_text[] = $grade_grades_text;
        } 
    }
    
    /**
     * Load grade_outcome data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_outcomes() {
        // Calculation for grade_item 1
        $grade_outcome = new stdClass();
        $grade_outcome->itemid = $this->grade_items[0]->id;
        $grade_outcome->shortname = 'Team work';
        $grade_outcome->timecreated = mktime();
        $grade_outcome->timemodified = mktime();
        $grade_outcome->scaleid = $this->scale[2]->id;
        
        if ($grade_outcome->id = insert_record('grade_outcomes', $grade_outcome)) {
            $this->grade_outcomes[] = $grade_outcome;
        } 
        
        // Calculation for grade_item 2
        $grade_outcome = new stdClass();
        $grade_outcome->itemid = $this->grade_items[1]->id;
        $grade_outcome->shortname = 'Complete circuit board';
        $grade_outcome->timecreated = mktime();
        $grade_outcome->timemodified = mktime();
        $grade_outcome->scaleid = $this->scale[3]->id;
        
        if ($grade_outcome->id = insert_record('grade_outcomes', $grade_outcome)) {
            $this->grade_outcomes[] = $grade_outcome;
        } 
        
        // Calculation for grade_item 3
        $grade_outcome = new stdClass();
        $grade_outcome->itemid = $this->grade_items[2]->id;
        $grade_outcome->shortname = 'Debug Java program';
        $grade_outcome->timecreated = mktime();
        $grade_outcome->timemodified = mktime();
        $grade_outcome->scaleid = $this->scale[4]->id;
        
        if ($grade_outcome->id = insert_record('grade_outcomes', $grade_outcome)) {
            $this->grade_outcomes[] = $grade_outcome;
        } 
    }

    /**
     * Load grade_history data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_history() {
        $grade_history = new stdClass();
        
        $grade_history->itemid = $this->grade_items[0]->id;
        $grade_history->userid = 1;
        $grade_history->oldgrade = 88;
        $grade_history->newgrade = 90;
        $grade_history->note = 'Modified manually in testgradehistory.php';
        $grade_history->howmodified = 'manual';

        if ($grade_history->id = insert_record('grade_history', $grade_history)) {
            $this->grade_history[] = $grade_history;
        } 
    }
/** 
 * TESTS BEGIN HERE
 */

// API FUNCTIONS

    function test_grade_get_items() {
        if (get_class($this) == 'gradelib_test') { 
            $grade_items = grade_get_items($this->courseid);

            $this->assertTrue(is_array($grade_items)); 
            $this->assertEqual(count($grade_items), 6);
        }
    }
    
    function test_grade_create_item() {
        if (get_class($this) == 'gradelib_test') { 
            $params = new stdClass();

            $params->courseid = $this->courseid;
            $params->categoryid = $this->grade_categories[0]->id;
            $params->itemname = 'unittestgradeitem4';
            $params->itemtype = 'mod';
            $params->itemmodule = 'database';
            $params->iteminstance = 4;
            $params->iteminfo = 'Grade item used for unit testing';
            $params->timecreated = mktime();
            $params->timemodified = mktime();

            $params->id = grade_create_item($params);
            $last_grade_item = end($this->grade_items);

            $this->assertEqual($params->id, $last_grade_item->id + 1);
            $this->grade_items[] = $params;
        }
    }

    function test_grade_create_category() {
        if (get_class($this) == 'gradelib_test') { 
            $grade_category = new stdClass();
            $grade_category->timecreated = mktime();
            $grade_category->timemodified = mktime();
        
            $items = array(new grade_item(), new grade_item());
            
            $grade_category->id = grade_create_category($this->courseid, 'unittestcategory4', $items, GRADE_AGGREGATE_MEAN);
            
            $last_grade_category = end($this->grade_categories);
            $this->assertEqual($grade_category->id, $last_grade_category->id + 1);

            $db_grade_category = get_record('grade_categories', 'id', $grade_category->id);
            $db_grade_category = new grade_category($db_grade_category);
            $db_grade_category->load_grade_item();
            $this->grade_categories[] = $db_grade_category;
            $this->grade_items[] = $db_grade_category->grade_item;
        }
    }

    function test_grade_is_locked() {
        if (get_class($this) == 'gradelib_test') { 
            $grade_item = $this->grade_items[0];
            $this->assertFalse(grade_is_locked($grade_item->itemtype, $grade_item->itemmodule, $grade_item->iteminstance, $grade_item->itemnumber));
            $grade_item = $this->grade_items[1];
            $this->assertTrue(grade_is_locked($grade_item->itemtype, $grade_item->itemmodule, $grade_item->iteminstance, $grade_item->itemnumber)); 
        }
    }

    function test_grade_standardise_score() {
        $this->assertEqual(4, round(standardise_score(6, 0, 7, 0, 5)));
        $this->assertEqual(40, standardise_score(50, 30, 80, 0, 100));
    }
}

?>
