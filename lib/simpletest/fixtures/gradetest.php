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
require_once($CFG->libdir . '/ddllib.php');

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
    var $tables = array('grade_categories',
                        'scale',
                        'grade_items',
                        'grade_grades',
                        'grade_grades_text',
                        'grade_outcomes',
                        'grade_history');

    var $grade_items = array();
    var $grade_categories = array();
    var $grade_grades = array();
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
            $table->addFieldInfo('calculation', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
            $table->addFieldInfo('gradetype', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '1');
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
            $table->addFieldInfo('locktime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('deleted', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('needsupdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
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

            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('parent', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('depth', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('path', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('fullname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('aggregation', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('keephigh', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('droplow', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
            $table->addKeyInfo('parent', XMLDB_KEY_FOREIGN, array('parent'), 'grade_categories', array('id'));

            /// Launch create table for grade_categories
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

        /// Define table grade_grades to be created
        $table = new XMLDBTable('grade_grades');

        if ($result && !table_exists($table)) {

            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('rawgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
            $table->addFieldInfo('rawgrademax', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '100');
            $table->addFieldInfo('rawgrademin', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('rawscaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('finalgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
            $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('locked', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('locktime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('exported', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

        /// Adding keys to table grade_grades
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
            $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
            $table->addKeyInfo('rawscaleid', XMLDB_KEY_FOREIGN, array('rawscaleid'), 'scale', array('id'));
            $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

            /// Launch create table for grade_grades
            $result = $result && create_table($table, true, false);
        }

        /// Define table scale to be created
        $table = new XMLDBTable('scale');

        if ($result && !table_exists($table)) {

            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('scale', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
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
        $temp  = explode(',', $scale->scale);
        $scale->max         = count($temp) -1;

        if ($scale->id = insert_record('scale', $scale)) {
            $this->scale[0] = $scale;
        }

        $scale = new stdClass();

        $scale->name        = 'unittestscale2';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $scale->description = 'This scale is used to mark standard assignments.';
        $scale->timemodified = mktime();
        $temp  = explode(',', $scale->scale);
        $scale->max         = count($temp) -1;

        if ($scale->id = insert_record('scale', $scale)) {
            $this->scale[1] = $scale;
        }

        $scale = new stdClass();

        $scale->name        = 'unittestscale3';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Loner, Contentious, Disinterested, Participative, Follower, Leader';
        $scale->description = 'Describes the level of teamwork of a student.';
        $scale->timemodified = mktime();
        $temp  = explode(',', $scale->scale);
        $scale->max         = count($temp) -1;

        if ($scale->id = insert_record('scale', $scale)) {
            $this->scale[2] = $scale;
        }

        $scale->name        = 'unittestscale4';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Does not understand theory, Understands theory but fails practice, Manages through, Excels';
        $scale->description = 'Level of expertise at a technical task, with a theoretical framework.';
        $scale->timemodified = mktime();
        $temp  = explode(',', $scale->scale);
        $scale->max         = count($temp) -1;

        if ($scale->id = insert_record('scale', $scale)) {
            $this->scale[3] = $scale;
        }

        $scale->name        = 'unittestscale5';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Insufficient, Acceptable, Excellent.';
        $scale->description = 'Description of skills.';
        $scale->timemodified = mktime();
        $temp  = explode(',', $scale->scale);
        $scale->max         = count($temp) -1;

        if ($scale->id = insert_record('scale', $scale)) {
            $this->scale[4] = $scale;
        }
    }

    /**
     * Load grade_category data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_categories() {
        $grade_category = new stdClass();

        $grade_category->fullname    = 'unittestcategory1';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN_GRADED;
        $grade_category->keephigh    = 100;
        $grade_category->droplow     = 0;
        $grade_category->hidden      = 0;
        $grade_category->timecreated = mktime();
        $grade_category->timemodified = mktime();
        $grade_category->depth = 1;

        if ($grade_category->id = insert_record('grade_categories', $grade_category)) {
            $this->grade_categories[0] = $grade_category;
        }

        $grade_category = new stdClass();

        $grade_category->fullname    = 'unittestcategory2';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN_GRADED;
        $grade_category->keephigh    = 100;
        $grade_category->droplow     = 0;
        $grade_category->hidden      = 0;
        $grade_category->parent      = $this->grade_categories[0]->id;
        $grade_category->timecreated = mktime();
        $grade_category->timemodified = mktime();
        $grade_category->depth = 2;

        if ($grade_category->id = insert_record('grade_categories', $grade_category)) {
            $this->grade_categories[1] = $grade_category;
        }

        $grade_category = new stdClass();

        $grade_category->fullname    = 'unittestcategory3';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN_GRADED;
        $grade_category->keephigh    = 100;
        $grade_category->droplow     = 0;
        $grade_category->hidden      = 0;
        $grade_category->parent      = $this->grade_categories[0]->id;
        $grade_category->timecreated = mktime();
        $grade_category->timemodified = mktime();
        $grade_category->depth = 2;

        if ($grade_category->id = insert_record('grade_categories', $grade_category)) {
            $this->grade_categories[2] = $grade_category;
        }

        // A category with no parent, but grade_items as children

        $grade_category = new stdClass();

        $grade_category->fullname    = 'level1category';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN_GRADED;
        $grade_category->keephigh    = 100;
        $grade_category->droplow     = 0;
        $grade_category->hidden      = 0;
        $grade_category->timecreated = mktime();
        $grade_category->timemodified = mktime();
        $grade_category->depth = 1;

        if ($grade_category->id = insert_record('grade_categories', $grade_category)) {
            $this->grade_categories[3] = $grade_category;
        }
    }

    /**
     * Load grade_item data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_items() {
        // id = 0
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitem1';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminstance = 1;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 30;
        $grade_item->grademax = 110;
        $grade_item->itemnumber = 1;
        $grade_item->idnumber = 'item id 0';
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 3;

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[0] = $grade_item;
        }

        // id = 1
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitem2';
        $grade_item->itemtype = 'import';
        $grade_item->itemmodule = 'assignment';
        $grade_item->calculation = '= [#gi'.$this->grade_items[0]->id.'#] + 30 + [item id 0] - [item id 0]';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->iteminstance = 2;
        $grade_item->itemnumber = null;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 4;

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[1] = $grade_item;
        }

        // id = 2
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[2]->id;
        $grade_item->itemname = 'unittestgradeitem3';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'forum';
        $grade_item->iteminstance = 3;
        $grade_item->gradetype = GRADE_TYPE_SCALE;
        $grade_item->scaleid = $this->scale[0]->id;
        $grade_item->grademin = 0;
        $grade_item->grademax = $this->scale[0]->max;
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 6;

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[2] = $grade_item;
        }

        // Load grade_items associated with the 3 categories
        // id = 3
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->iteminstance = $this->grade_categories[0]->id;
        $grade_item->itemname = 'unittestgradeitemcategory1';
        $grade_item->needsupdate = true;
        $grade_item->itemtype = 'category';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 1;

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[3] = $grade_item;
        }

        // id = 4
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->iteminstance = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitemcategory2';
        $grade_item->itemtype = 'category';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->needsupdate = true;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 2;

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[4] = $grade_item;
        }

        // id = 5
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->iteminstance = $this->grade_categories[2]->id;
        $grade_item->itemname = 'unittestgradeitemcategory3';
        $grade_item->itemtype = 'category';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->needsupdate = true;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 5;

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[5] = $grade_item;
        }

        // Orphan grade_item
        // id = 6
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->itemname = 'unittestorphangradeitem1';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminstance = 5;
        $grade_item->itemnumber = 0;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 10;
        $grade_item->grademax = 120;
        $grade_item->locked = time();
        $grade_item->iteminfo = 'Orphan Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 7;

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[6] = $grade_item;
        }

        // 2 grade items under level1category
        // id = 7
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[3]->id;
        $grade_item->itemname = 'singleparentitem1';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'forum';
        $grade_item->iteminstance = 7;
        $grade_item->gradetype = GRADE_TYPE_SCALE;
        $grade_item->scaleid = $this->scale[0]->id;
        $grade_item->grademin = 0;
        $grade_item->grademax = $this->scale[0]->max;
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 9;

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[7] = $grade_item;
        }

        // id = 8
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[3]->id;
        $grade_item->itemname = 'singleparentitem2';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'forum';
        $grade_item->iteminstance = 9;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 10;

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[8] = $grade_item;
        }

        // Grade_item for level1category
        // id = 9
        $grade_item = new stdClass();

        $grade_item->courseid = $this->courseid;
        $grade_item->itemname = 'grade_item for level1 category';
        $grade_item->itemtype = 'category';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminstance = $this->grade_categories[3]->id;
        $grade_item->needsupdate = true;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Orphan Grade item used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 8;

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[9] = $grade_item;
        }

    }

    /**
     * Load grade_grades data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_grades() {
        // Grades for grade_item 1
        $grade = new stdClass();
        $grade->itemid = $this->grade_items[0]->id;
        $grade->userid = 1;
        $grade->rawgrade = 15; // too small
        $grade->finalgrade = 30;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[0] = $grade;
        }

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[0]->id;
        $grade->userid = 2;
        $grade->rawgrade = 40;
        $grade->finalgrade = 40;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[1] = $grade;
        }

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[0]->id;
        $grade->userid = 3;
        $grade->rawgrade = 170; // too big
        $grade->finalgrade = 110;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[2] = $grade;
        }


        // No raw grades for grade_item 2 - it is calculated

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[1]->id;
        $grade->userid = 1;
        $grade->finalgrade = 60;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[3] = $grade;
        }

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[1]->id;
        $grade->userid = 2;
        $grade->finalgrade = 70;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[4] = $grade;
        }

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[1]->id;
        $grade->userid = 3;
        $grade->finalgrade = 100;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[5] = $grade;
        }


        // Grades for grade_item 3

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[2]->id;
        $grade->userid = 1;
        $grade->rawgrade = 2;
        $grade->finalgrade = 6;
        $grade->scaleid = $this->scale[3]->id;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[6] = $grade;
        }

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[2]->id;
        $grade->userid = 2;
        $grade->rawgrade = 3;
        $grade->finalgrade = 2;
        $grade->scaleid = $this->scale[3]->id;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[] = $grade;
        }

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[2]->id;
        $grade->userid = 3;
        $grade->rawgrade = 1;
        $grade->finalgrade = 3;
        $grade->scaleid = $this->scale[3]->id;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[] = $grade;
        }

        // Grades for grade_item 7

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[6]->id;
        $grade->userid = 1;
        $grade->rawgrade = 97;
        $grade->finalgrade = 69;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[] = $grade;
        }

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[6]->id;
        $grade->userid = 2;
        $grade->rawgrade = 49;
        $grade->finalgrade = 87;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[] = $grade;
        }

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[6]->id;
        $grade->userid = 3;
        $grade->rawgrade = 67;
        $grade->finalgrade = 94;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[] = $grade;
        }

        // Grades for grade_item 8

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[7]->id;
        $grade->userid = 2;
        $grade->rawgrade = 3;
        $grade->finalgrade = 3;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[] = $grade;
        }

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[7]->id;
        $grade->userid = 3;
        $grade->rawgrade = 6;
        $grade->finalgrade = 6;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[] = $grade;
        }

        // Grades for grade_item 9

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[8]->id;
        $grade->userid = 1;
        $grade->rawgrade = 20;
        $grade->finalgrade = 20;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[] = $grade;
        }

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[8]->id;
        $grade->userid = 2;
        $grade->rawgrade = 50;
        $grade->finalgrade = 50;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[] = $grade;
        }

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[7]->id;
        $grade->userid = 3;
        $grade->rawgrade = 100;
        $grade->finalgrade = 100;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();

        if ($grade->id = insert_record('grade_grades', $grade)) {
            $this->grade_grades[] = $grade;
        }
    }

    /**
     * Load grade_grades_text data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_grades_text() {
        $grade_grades_text = new stdClass();

        $grade_grades_text->itemid = $this->grade_grades[0]->itemid;
        $grade_grades_text->userid = $this->grade_grades[0]->userid;
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
 * No unit tests here
 */

}

?>
