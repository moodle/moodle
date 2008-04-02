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
require_once($CFG->libdir . '/ddllib.php');

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
    var $tables = array('grade_categories',
                        'scale',
                        'grade_items',
                        'grade_grades',
                        'grade_outcomes');

    var $grade_items = array();
    var $grade_categories = array();
    var $grade_grades = array();
    var $grade_outcomes = array();
    var $scale = array();

    var $activities = array();
    var $courseid = 1;
    var $userid = 1;

    /**
     * Create temporary test tables and entries in the database for these tests.
     * These tests have to work on a brand new site.
     * Override $CFG->prefix while these tests run.
     */
    function setUp() {
        // Set global category settings to -1 (not force)
        global $CFG;
        $CFG->grade_droplow = -1;
        $CFG->grade_keephigh = -1;
        $CFG->grade_aggregation = -1;
        $CFG->grade_aggregateonlygraded = -1;
        $CFG->grade_aggregateoutcomes = -1;
        $CFG->grade_aggregatesubcats = -1;

        $CFG->old_prefix = $CFG->prefix;
        $CFG->prefix .= 'unittest_';
        if (!$this->prepare_test_tables()) {
            die("Could not create all the test tables!");
        }

        if (!$this->prepare_test_history_tables()) {
            die("Could not create all the test tables!");
        }

        foreach ($this->tables as $table) {
            $function = "load_$table";
            $this->$function();
        }
    }

    function prepare_test_tables() {
        $result = true;

        /// Define table course_modules to be created
        $table = new XMLDBTable('course_modules');

        if (!table_exists($table)) {
            /// Adding fields to table course_modules
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('module', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('instance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('section', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null, null, null);
            $table->addFieldInfo('added', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('score', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('indent', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('visible', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, null, '1');
            $table->addFieldInfo('visibleold', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, null, '1');
            $table->addFieldInfo('groupmode', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('groupingid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('groupmembersonly', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');

            /// Adding keys to table course_modules
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('groupingid', XMLDB_KEY_FOREIGN, array('groupingid'), 'groupings', array('id'));

            /// Adding indexes to table course_modules
            $table->addIndexInfo('visible', XMLDB_INDEX_NOTUNIQUE, array('visible'));
            $table->addIndexInfo('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
            $table->addIndexInfo('module', XMLDB_INDEX_NOTUNIQUE, array('module'));
            $table->addIndexInfo('instance', XMLDB_INDEX_NOTUNIQUE, array('instance'));
            $table->addIndexInfo('idnumber-course', XMLDB_INDEX_NOTUNIQUE, array('idnumber', 'course'));

            /// Launch create table for course_modules
            $result = $result && create_table($table, true, false);
        } else {
            delete_records($table->name);
        }

        /// Define table modules to be created
        $table = new XMLDBTable('modules');

        if (!table_exists($table)) {

            /// Adding fields to table modules
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('version', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('cron', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('lastcron', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('search', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('visible', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, null, '1');

            /// Adding keys to table modules
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

            /// Adding indexes to table modules
            $table->addIndexInfo('name', XMLDB_INDEX_NOTUNIQUE, array('name'));

            /// Launch create table for modules
            $result = $result && create_table($table, true, false);
        } else {
            delete_records($table->name);
        }

        /// Define table grade_items to be created
        $table = new XMLDBTable('grade_items');

        if (!table_exists($table)) {
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('categoryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('itemname', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
            $table->addFieldInfo('itemtype', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('itemmodule', XMLDB_TYPE_CHAR, '30', null, null, null, null, null, null);
            $table->addFieldInfo('iteminstance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('itemnumber', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('iteminfo', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
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
            $table->addFieldInfo('display', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('decimals', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('locked', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('locktime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
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

        } else {
            delete_records($table->name);
        }


        /// Define table grade_categories to be created
        $table = new XMLDBTable('grade_categories');

        if ($result && !table_exists($table)) {

            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('parent', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('depth', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('path', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
            $table->addFieldInfo('fullname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('aggregation', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('keephigh', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('droplow', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('aggregateonlygraded', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('aggregateoutcomes', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('aggregatesubcats', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
            $table->addKeyInfo('parent', XMLDB_KEY_FOREIGN, array('parent'), 'grade_categories', array('id'));

            /// Launch create table for grade_categories
            $result = $result && create_table($table, true, false);

        } else {
            delete_records($table->name);
        }


        /// Define table grade_grades to be created
        $table = new XMLDBTable('grade_grades');

        if ($result && !table_exists($table)) {

            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
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
            $table->addFieldInfo('overridden', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('excluded', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('feedback', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
            $table->addFieldInfo('feedbackformat', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('information', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
            $table->addFieldInfo('informationformat', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
            $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
            $table->addKeyInfo('rawscaleid', XMLDB_KEY_FOREIGN, array('rawscaleid'), 'scale', array('id'));
            $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

            /// Launch create table for grade_grades
            $result = $result && create_table($table, true, false);

        } else {
            delete_records($table->name);
        }


        /// Define table grade_outcomes to be created
        $table = new XMLDBTable('grade_outcomes');

        if ($result && !table_exists($table)) {

            /// Adding fields to table grade_outcomes
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
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

        } else {
            delete_records($table->name);
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

        } else {
            delete_records($table->name);
        }

        /// Define table quiz to be created
        $table = new XMLDBTable('quiz');

        if ($result && !table_exists($table)) {
            /// Adding fields to table quiz
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('intro', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('timeopen', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timeclose', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('optionflags', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('penaltyscheme', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('attempts', XMLDB_TYPE_INTEGER, '6', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('attemptonlast', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('grademethod', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '1');
            $table->addFieldInfo('decimalpoints', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '2');
            $table->addFieldInfo('review', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('questionsperpage', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('shufflequestions', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('shuffleanswers', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('questions', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('sumgrades', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('grade', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timelimit', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('password', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('subnet', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('popup', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('delay1', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('delay2', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');

            /// Adding keys to table quiz
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

            /// Adding indexes to table quiz
            $table->addIndexInfo('course', XMLDB_INDEX_NOTUNIQUE, array('course'));

            /// Launch create table for quiz
            $result = $result && create_table($table, true, false);
        } else {
            delete_records($table->name);
        }

        return $result;
    }


    function prepare_test_history_tables() {
        $result = true;

        /// Define table grade_items to be created
        $table = new XMLDBTable('grade_items_history');

        if (!table_exists($table)) {

        /// Adding fields to table grade_items_history
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('action', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('oldid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('source', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('loggeduser', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('categoryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('itemname', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
            $table->addFieldInfo('itemtype', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('itemmodule', XMLDB_TYPE_CHAR, '30', null, null, null, null, null, null);
            $table->addFieldInfo('iteminstance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('itemnumber', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('iteminfo', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
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
            $table->addFieldInfo('display', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('decimals', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('locked', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('locktime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('needsupdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');

        /// Adding keys to table grade_items_history
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('oldid', XMLDB_KEY_FOREIGN, array('oldid'), 'grade_items', array('id'));
            $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
            $table->addKeyInfo('categoryid', XMLDB_KEY_FOREIGN, array('categoryid'), 'grade_categories', array('id'));
            $table->addKeyInfo('scaleid', XMLDB_KEY_FOREIGN, array('scaleid'), 'scale', array('id'));
            $table->addKeyInfo('outcomeid', XMLDB_KEY_FOREIGN, array('outcomeid'), 'grade_outcomes', array('id'));

        /// Adding indexes to table grade_items_history
            $table->addIndexInfo('action', XMLDB_INDEX_NOTUNIQUE, array('action'));

        /// Launch create table for grade_items_history
            $result = $result && create_table($table, true, false);

        } else {
            delete_records($table->name);
        }

        /// Define table grade_categories to be created
        $table = new XMLDBTable('grade_categories_history');


        if ($result && !table_exists($table)) {

        /// Adding fields to table grade_categories_history
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('action', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('oldid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('source', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('loggeduser', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('parent', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('depth', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('path', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
            $table->addFieldInfo('fullname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('aggregation', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('keephigh', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('droplow', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('aggregateonlygraded', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('aggregateoutcomes', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('aggregatesubcats', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');

        /// Adding keys to table grade_categories_history
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('oldid', XMLDB_KEY_FOREIGN, array('oldid'), 'grade_categories', array('id'));
            $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
            $table->addKeyInfo('parent', XMLDB_KEY_FOREIGN, array('parent'), 'grade_categories', array('id'));

        /// Adding indexes to table grade_categories_history
            $table->addIndexInfo('action', XMLDB_INDEX_NOTUNIQUE, array('action'));

        /// Launch create table for grade_categories_history
            $result = $result && create_table($table, true, false);

        } else {
            delete_records($table->name);
        }


        /// Define table grade_grades to be created
        $table = new XMLDBTable('grade_grades_history');

        if ($result && !table_exists($table)) {

        /// Adding fields to table grade_grades_history
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('action', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('oldid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('source', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('loggeduser', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
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
            $table->addFieldInfo('overridden', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('excluded', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('feedback', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
            $table->addFieldInfo('feedbackformat', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('information', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
            $table->addFieldInfo('informationformat', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');

        /// Adding keys to table grade_grades_history
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('oldid', XMLDB_KEY_FOREIGN, array('oldid'), 'grade_grades', array('id'));
            $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
            $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
            $table->addKeyInfo('rawscaleid', XMLDB_KEY_FOREIGN, array('rawscaleid'), 'scale', array('id'));
            $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));
            $table->addKeyInfo('loggeduser', XMLDB_KEY_FOREIGN, array('loggeduser'), 'user', array('id'));

        /// Adding indexes to table grade_grades_history
            $table->addIndexInfo('action', XMLDB_INDEX_NOTUNIQUE, array('action'));

        /// Launch create table for grade_grades_history
            $result = $result && create_table($table, true, false);

        } else {
            delete_records($table->name);
        }


        /// Define table grade_outcomes to be created
        $table = new XMLDBTable('grade_outcomes_history');

        if ($result && !table_exists($table)) {

        /// Adding fields to table grade_outcomes_history
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('action', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('oldid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('source', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('loggeduser', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

        /// Adding keys to table grade_outcomes_history
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('oldid', XMLDB_KEY_FOREIGN, array('oldid'), 'grade_outcomes', array('id'));
            $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
            $table->addKeyInfo('scaleid', XMLDB_KEY_FOREIGN, array('scaleid'), 'scale', array('id'));
            $table->addKeyInfo('loggeduser', XMLDB_KEY_FOREIGN, array('loggeduser'), 'user', array('id'));

        /// Adding indexes to table grade_outcomes_history
            $table->addIndexInfo('action', XMLDB_INDEX_NOTUNIQUE, array('action'));

        /// Launch create table for grade_outcomes_history
            $result = $result && create_table($table, true, false);

        } else {
            delete_records($table->name);
        }

        /// Define table scale to be created
        $table = new XMLDBTable('scale_history');


        if ($result && !table_exists($table)) {

        /// Adding fields to table scale_history
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('action', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('oldid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('source', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('loggeduser', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('scale', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);

        /// Adding keys to table scale_history
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('oldid', XMLDB_KEY_FOREIGN, array('oldid'), 'scales', array('id'));
            $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));

        /// Adding indexes to table scale_history
            $table->addIndexInfo('action', XMLDB_INDEX_NOTUNIQUE, array('action'));

        /// Launch create table for scale_history
            $result = $result && create_table($table, true, false);

        } else {
            delete_records($table->name);
        }

        return $result;
    }

    /**
     * Drop test tables from DB.
     * Restore original $CFG->prefix.
     */
    function tearDown() {
        global $CFG;
        // delete the contents of tables before the test run - the unit test might fail on fatal error and the data would not be deleted!
        foreach ($this->tables as $table) {
            unset($this->$table);
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

        if ($scale->id = insert_record('scale', $scale)) {
            $this->scale[0] = $scale;
            $temp = explode(',', $scale->scale);
            $this->scalemax[0] = count($temp) -1;
        }

        $scale = new stdClass();

        $scale->name        = 'unittestscale2';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $scale->description = 'This scale is used to mark standard assignments.';
        $scale->timemodified = mktime();

        if ($scale->id = insert_record('scale', $scale)) {
            $this->scale[1] = $scale;
            $temp = explode(',', $scale->scale);
            $this->scalemax[1] = count($temp) -1;
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
            $temp = explode(',', $scale->scale);
            $this->scalemax[2] = count($temp) -1;
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
            $temp = explode(',', $scale->scale);
            $this->scalemax[3] = count($temp) -1;
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
            $temp = explode(',', $scale->scale);
            $this->scalemax[4] = count($temp) -1;
        }
    }

    /**
     * Load grade_category data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_categories() {

        $course_category = grade_category::fetch_course_category($this->courseid);

        $grade_category = new stdClass();

        $grade_category->fullname    = 'unittestcategory1';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $course_category->id;
        $grade_category->timecreated = mktime();
        $grade_category->timemodified = mktime();
        $grade_category->depth = 2;

        if ($grade_category->id = insert_record('grade_categories', $grade_category)) {
            $grade_category->path = '/'.$course_category->id.'/'.$grade_category->id.'/';
            update_record('grade_categories', $grade_category);
            $this->grade_categories[0] = $grade_category;
        }

        $grade_category = new stdClass();

        $grade_category->fullname    = 'unittestcategory2';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $this->grade_categories[0]->id;
        $grade_category->timecreated = mktime();
        $grade_category->timemodified = mktime();
        $grade_category->depth = 3;

        if ($grade_category->id = insert_record('grade_categories', $grade_category)) {
            $grade_category->path = $this->grade_categories[0]->path.$grade_category->id.'/';
            update_record('grade_categories', $grade_category);
            $this->grade_categories[1] = $grade_category;
        }

        $grade_category = new stdClass();

        $grade_category->fullname    = 'unittestcategory3';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $this->grade_categories[0]->id;
        $grade_category->timecreated = mktime();
        $grade_category->timemodified = mktime();
        $grade_category->depth = 3;

        if ($grade_category->id = insert_record('grade_categories', $grade_category)) {
            $grade_category->path = $this->grade_categories[0]->path.$grade_category->id.'/';
            update_record('grade_categories', $grade_category);
            $this->grade_categories[2] = $grade_category;
        }

        // A category with no parent, but grade_items as children

        $grade_category = new stdClass();

        $grade_category->fullname    = 'level1category';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $course_category->id;
        $grade_category->timecreated = mktime();
        $grade_category->timemodified = mktime();
        $grade_category->depth = 2;

        if ($grade_category->id = insert_record('grade_categories', $grade_category)) {
            $grade_category->path = '/'.$course_category->id.'/'.$grade_category->id.'/';
            update_record('grade_categories', $grade_category);
            $this->grade_categories[3] = $grade_category;
        }
    }

    /**
     * Load module entries in modules table\
     */
    function load_modules() {
        $module = new stdClass();
        $module->name = 'assignment';
        if ($module->id = insert_record('modules', $module)) {
            $this->modules[0] = $module;
        }

        $module = new stdClass();
        $module->name = 'quiz';
        if ($module->id = insert_record('modules', $module)) {
            $this->modules[1] = $module;
        }

        $module = new stdClass();
        $module->name = 'forum';
        if ($module->id = insert_record('modules', $module)) {
            $this->modules[2] = $module;
        }
    }

    /**
     * Load module instance entries in course_modules table
     */
    function load_course_modules() {
        $course_module = new stdClass();
        $course_module->course = $this->courseid;
        $quiz->module = 1;
        $quiz->instance = 2;
        if ($course_module->id = insert_record('course_modules', $course_module)) {
            $this->course_module[0] = $course_module;
        }

        $course_module = new stdClass();
        $course_module->course = $this->courseid;
        $quiz->module = 2;
        $quiz->instance = 1;
        if ($course_module->id = insert_record('course_modules', $course_module)) {
            $this->course_module[0] = $course_module;
        }

        $course_module = new stdClass();
        $course_module->course = $this->courseid;
        $quiz->module = 2;
        $quiz->instance = 5;
        if ($course_module->id = insert_record('course_modules', $course_module)) {
            $this->course_module[0] = $course_module;
        }

        $course_module = new stdClass();
        $course_module->course = $this->courseid;
        $quiz->module = 3;
        $quiz->instance = 3;
        if ($course_module->id = insert_record('course_modules', $course_module)) {
            $this->course_module[0] = $course_module;
        }

        $course_module = new stdClass();
        $course_module->course = $this->courseid;
        $quiz->module = 3;
        $quiz->instance = 7;
        if ($course_module->id = insert_record('course_modules', $course_module)) {
            $this->course_module[0] = $course_module;
        }

        $course_module = new stdClass();
        $course_module->course = $this->courseid;
        $quiz->module = 3;
        $quiz->instance = 9;
        if ($course_module->id = insert_record('course_modules', $course_module)) {
            $this->course_module[0] = $course_module;
        }
    }

    /**
     * Load test quiz data into the database
     */
    function load_quiz_activities() {
        $quiz = new stdClass();
        $quiz->course = $this->courseid;
        $quiz->name = 'test quiz';
        $quiz->intro = 'let us quiz you!';
        $quiz->questions = '1,2';
        if ($quiz->id = insert_record('quiz', $quiz)) {
            $this->activities[0] = $quiz;
        }

        $quiz = new stdClass();
        $quiz->course = $this->courseid;
        $quiz->name = 'test quiz 2';
        $quiz->intro = 'let us quiz you again!';
        $quiz->questions = '1,3';
        if ($quiz->id = insert_record('quiz', $quiz)) {
            $this->activities[1] = $quiz;
        }

    }

    /**
     * Load grade_item data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_items() {

        $course_category = grade_category::fetch_course_category($this->courseid);

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
        $grade_item->iteminfo = 'Grade item 0 used for unit testing';
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
        $grade_item->calculation = '= ##gi'.$this->grade_items[0]->id.'## + 30 + [[item id 0]] - [[item id 0]]';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->iteminstance = 2;
        $grade_item->itemnumber = null;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 1 used for unit testing';
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
        $grade_item->grademax = $this->scalemax[0];
        $grade_item->iteminfo = 'Grade item 2 used for unit testing';
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
        $grade_item->needsupdate = 0;
        $grade_item->itemtype = 'category';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 3 used for unit testing';
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
        $grade_item->needsupdate = 0;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 4 used for unit testing';
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
        $grade_item->iteminfo = 'Grade item 5 used for unit testing';
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
        $grade_item->categoryid = $course_category->id;
        $grade_item->itemname = 'unittestorphangradeitem1';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminstance = 5;
        $grade_item->itemnumber = 0;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 10;
        $grade_item->grademax = 120;
        $grade_item->locked = time();
        $grade_item->iteminfo = 'Orphan Grade 6 item used for unit testing';
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
        $grade_item->grademax = $this->scalemax[0];
        $grade_item->iteminfo = 'Grade item 7 used for unit testing';
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
        $grade_item->iteminfo = 'Grade item 8 used for unit testing';
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
        $grade_item->iteminfo = 'Orphan Grade item 9 used for unit testing';
        $grade_item->timecreated = mktime();
        $grade_item->timemodified = mktime();
        $grade_item->sortorder = 8;

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[9] = $grade_item;
        }

        // Manual grade_item
        // id = 10
        $grade_item = new stdClass();

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

        if ($grade_item->id = insert_record('grade_items', $grade_item)) {
            $this->grade_items[10] = $grade_item;
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
        $grade->information = 'Thumbs down';
        $grade->informationformat = FORMAT_PLAIN;
        $grade->feedback = 'Good, but not good enough..';
        $grade->feedbackformat = FORMAT_PLAIN;

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
     * Load grade_outcome data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_outcomes() {
        // Calculation for grade_item 1
        $grade_outcome = new stdClass();
        $grade_outcome->fullname = 'Team work';
        $grade_outcome->shortname = 'Team work';
        $grade_outcome->timecreated = mktime();
        $grade_outcome->timemodified = mktime();
        $grade_outcome->scaleid = $this->scale[2]->id;

        if ($grade_outcome->id = insert_record('grade_outcomes', $grade_outcome)) {
            $this->grade_outcomes[] = $grade_outcome;
        }

        // Calculation for grade_item 2
        $grade_outcome = new stdClass();
        $grade_outcome->fullname = 'Complete circuit board';
        $grade_outcome->shortname = 'Complete circuit board';
        $grade_outcome->timecreated = mktime();
        $grade_outcome->timemodified = mktime();
        $grade_outcome->scaleid = $this->scale[3]->id;

        if ($grade_outcome->id = insert_record('grade_outcomes', $grade_outcome)) {
            $this->grade_outcomes[] = $grade_outcome;
        }

        // Calculation for grade_item 3
        $grade_outcome = new stdClass();
        $grade_outcome->fullname = 'Debug Java program';
        $grade_outcome->shortname = 'Debug Java program';
        $grade_outcome->timecreated = mktime();
        $grade_outcome->timemodified = mktime();
        $grade_outcome->scaleid = $this->scale[4]->id;

        if ($grade_outcome->id = insert_record('grade_outcomes', $grade_outcome)) {
            $this->grade_outcomes[] = $grade_outcome;
        }
    }

/**
 * No unit tests here
 */

}

?>
