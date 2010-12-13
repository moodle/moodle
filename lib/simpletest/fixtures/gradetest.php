<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    moodlecore
 * @copyright  nicolas@moodle.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/gradelib.php');

/**
 * Shared code for all grade related tests.
 *
 * Here is a brief explanation of the test data set up in these unit tests.
 * category1 => array(category2 => array(grade_item1, grade_item2), category3 => array(grade_item3))
 * 3 users for 3 grade_items
 */
class grade_test extends UnitTestCaseUsingDatabase {

    public $grade_tables = array('lib' => array(
                                     'grade_categories', 'grade_categories_history',
                                     'scale', 'scale_history',
                                     'grade_items', 'grade_items_history',
                                     'grade_grades', 'grade_grades_history',
                                     'grade_outcomes', 'grade_outcomes_history','grade_outcomes_courses',
                                     'files',
                                     'modules',
                                     'course_modules'),
                                 'mod/quiz' => array('quiz')
                            );

    public $grade_items = array();
    public $grade_categories = array();
    public $grade_grades = array();
    public $grade_outcomes = array();
    public $scale = array();

    public $activities = array();
    public $courseid = 1;
    public $userid = 1;

    /**
     * Create temporary test tables and entries in the database for these tests.
     * These tests have to work on a brand new site.
     */
    function setUp() {
        global $CFG;

        parent::setup();
        $CFG->grade_droplow = -1;
        $CFG->grade_keephigh = -1;
        $CFG->grade_aggregation = -1;
        $CFG->grade_aggregateonlygraded = -1;
        $CFG->grade_aggregateoutcomes = -1;
        $CFG->grade_aggregatesubcats = -1;

        $this->switch_to_test_db(); // All operations until end of test method will happen in test DB

        foreach ($this->grade_tables as $dir => $tables) {
            $this->create_test_tables($tables, $dir); // Create tables
            foreach ($tables as $table) { // Fill them if load_xxx method is available
                $function = "load_$table";
                if (method_exists($this, $function)) {
                    $this->$function();
                }
            }
        }

    }

    function tearDown() {
        parent::tearDown(); // All the test tables created in setUp will be dropped by this
    }

    /**
     * Load scale data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_scale() {
        global $DB;
        $scale = new stdClass();

        $scale->name        = 'unittestscale1';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Way off topic, Not very helpful, Fairly neutral, Fairly helpful, Supportive, Some good information, Perfect answer!';
        $scale->description = 'This scale defines some of qualities that make posts helpful within the Moodle help forums.\n Your feedback will help others see how their posts are being received.';
        $scale->timemodified = mktime();

        $scale->id = $DB->insert_record('scale', $scale);
        $this->scale[0] = $scale;
        $temp = explode(',', $scale->scale);
        $this->scalemax[0] = count($temp) -1;

        $scale = new stdClass();

        $scale->name        = 'unittestscale2';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $scale->description = 'This scale is used to mark standard assignments.';
        $scale->timemodified = mktime();

        $scale->id = $DB->insert_record('scale', $scale);
        $this->scale[1] = $scale;
        $temp = explode(',', $scale->scale);
        $this->scalemax[1] = count($temp) -1;

        $scale = new stdClass();

        $scale->name        = 'unittestscale3';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Loner, Contentious, Disinterested, Participative, Follower, Leader';
        $scale->description = 'Describes the level of teamwork of a student.';
        $scale->timemodified = mktime();
        $temp  = explode(',', $scale->scale);
        $scale->max         = count($temp) -1;

        $scale->id = $DB->insert_record('scale', $scale);
        $this->scale[2] = $scale;
        $temp = explode(',', $scale->scale);
        $this->scalemax[2] = count($temp) -1;

        $scale->name        = 'unittestscale4';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Does not understand theory, Understands theory but fails practice, Manages through, Excels';
        $scale->description = 'Level of expertise at a technical task, with a theoretical framework.';
        $scale->timemodified = mktime();
        $temp  = explode(',', $scale->scale);
        $scale->max         = count($temp) -1;

        $scale->id = $DB->insert_record('scale', $scale);
        $this->scale[3] = $scale;
        $temp = explode(',', $scale->scale);
        $this->scalemax[3] = count($temp) -1;

        $scale->name        = 'unittestscale5';
        $scale->courseid    = $this->courseid;
        $scale->userid      = $this->userid;
        $scale->scale       = 'Insufficient, Acceptable, Excellent.';
        $scale->description = 'Description of skills.';
        $scale->timemodified = mktime();
        $temp  = explode(',', $scale->scale);
        $scale->max         = count($temp) -1;

        $scale->id = $DB->insert_record('scale', $scale);
        $this->scale[4] = $scale;
        $temp = explode(',', $scale->scale);
        $this->scalemax[4] = count($temp) -1;
    }

    /**
     * Load grade_category data into the database, and adds the corresponding objects to this class' variable.
     * category structure:
                              course category
                                    |
                           +--------+-------------+
                           |                      |
             unittestcategory1               level1category
                  |
         +--------+-------------+
         |                      |
        unittestcategory2  unittestcategory3
     */
    function load_grade_categories() {
        global $DB;

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

        $grade_category->id = $DB->insert_record('grade_categories', $grade_category);
        $grade_category->path = '/'.$course_category->id.'/'.$grade_category->id.'/';
        $DB->update_record('grade_categories', $grade_category);
        $this->grade_categories[0] = $grade_category;

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

        $grade_category->id = $DB->insert_record('grade_categories', $grade_category);
        $grade_category->path = $this->grade_categories[0]->path.$grade_category->id.'/';
        $DB->update_record('grade_categories', $grade_category);
        $this->grade_categories[1] = $grade_category;

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

        $grade_category->id = $DB->insert_record('grade_categories', $grade_category);
        $grade_category->path = $this->grade_categories[0]->path.$grade_category->id.'/';
        $DB->update_record('grade_categories', $grade_category);
        $this->grade_categories[2] = $grade_category;

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

        $grade_category->id = $DB->insert_record('grade_categories', $grade_category);
        $grade_category->path = '/'.$course_category->id.'/'.$grade_category->id.'/';
        $DB->update_record('grade_categories', $grade_category);
        $this->grade_categories[3] = $grade_category;
    }

    /**
     * Load module entries in modules table
     */
    function load_modules() {
        global $DB;
        $module = new stdClass();
        $module->name = 'assignment';
        $module->id = $DB->insert_record('modules', $module);
        $this->modules[0] = $module;

        $module = new stdClass();
        $module->name = 'quiz';
        $module->id = $DB->insert_record('modules', $module);
        $this->modules[1] = $module;

        $module = new stdClass();
        $module->name = 'forum';
        $module->id = $DB->insert_record('modules', $module);
        $this->modules[2] = $module;
    }

    /**
     * Load module instance entries in course_modules table
     */
    function load_course_modules() {
        global $DB;
        $course_module = new stdClass();
        $course_module->course = $this->courseid;
        $quiz->module = 1;
        $quiz->instance = 2;
        $course_module->id = $DB->insert_record('course_modules', $course_module);
        $this->course_module[0] = $course_module;

        $course_module = new stdClass();
        $course_module->course = $this->courseid;
        $quiz->module = 2;
        $quiz->instance = 1;
        $course_module->id = $DB->insert_record('course_modules', $course_module);
        $this->course_module[0] = $course_module;

        $course_module = new stdClass();
        $course_module->course = $this->courseid;
        $quiz->module = 2;
        $quiz->instance = 5;
        $course_module->id = $DB->insert_record('course_modules', $course_module);
        $this->course_module[0] = $course_module;

        $course_module = new stdClass();
        $course_module->course = $this->courseid;
        $quiz->module = 3;
        $quiz->instance = 3;
        $course_module->id = $DB->insert_record('course_modules', $course_module);
        $this->course_module[0] = $course_module;

        $course_module = new stdClass();
        $course_module->course = $this->courseid;
        $quiz->module = 3;
        $quiz->instance = 7;
        $course_module->id = $DB->insert_record('course_modules', $course_module);
        $this->course_module[0] = $course_module;

        $course_module = new stdClass();
        $course_module->course = $this->courseid;
        $quiz->module = 3;
        $quiz->instance = 9;
        $course_module->id = $DB->insert_record('course_modules', $course_module);
        $this->course_module[0] = $course_module;
    }

    /**
     * Load test quiz data into the database
     */
    function load_quiz_activities() {
        global $DB;
        $quiz = new stdClass();
        $quiz->course = $this->courseid;
        $quiz->name = 'test quiz';
        $quiz->intro = 'let us quiz you!';
        $quiz->questions = '1,2';
        $quiz->id = $DB->insert_record('quiz', $quiz);
        $this->activities[0] = $quiz;

        $quiz = new stdClass();
        $quiz->course = $this->courseid;
        $quiz->name = 'test quiz 2';
        $quiz->intro = 'let us quiz you again!';
        $quiz->questions = '1,3';
        $quiz->id = $DB->insert_record('quiz', $quiz);
        $this->activities[1] = $quiz;
    }

    /**
     * Load grade_item data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_items() {
        global $DB;

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

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[0] = $grade_item;

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

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[1] = $grade_item;

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

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[2] = $grade_item;

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

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[3] = $grade_item;

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

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[4] = $grade_item;

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

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[5] = $grade_item;

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

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[6] = $grade_item;

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

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[7] = $grade_item;

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

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[8] = $grade_item;

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

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[9] = $grade_item;

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

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[10] = $grade_item;
    }

    /**
     * Load grade_grades data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_grades() {
        global $DB;

        //this method is called once for each test method. Avoid adding things to $this->grade_grades multiple times
        $this->grade_grades = array();

        // Grades for grade_item 1
        $grade = new stdClass();
        $grade->itemid = $this->grade_items[0]->id;
        $grade->userid = 1;
        $grade->rawgrade = 15; // too small
        $grade->finalgrade = 30;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '1 of 17 grade_grades';
        $grade->informationformat = FORMAT_PLAIN;
        $grade->feedback = 'Good, but not good enough..';
        $grade->feedbackformat = FORMAT_PLAIN;

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[0] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[0]->id;
        $grade->userid = 2;
        $grade->rawgrade = 40;
        $grade->finalgrade = 40;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '2 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[1] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[0]->id;
        $grade->userid = 3;
        $grade->rawgrade = 170; // too big
        $grade->finalgrade = 110;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '3 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[2] = $grade;


        // No raw grades for grade_item 2 - it is calculated

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[1]->id;
        $grade->userid = 1;
        $grade->finalgrade = 72;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '4 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[3] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[1]->id;
        $grade->userid = 2;
        $grade->finalgrade = 92;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '5 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[4] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[1]->id;
        $grade->userid = 3;
        $grade->finalgrade = 100;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '6 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[5] = $grade;


        // Grades for grade_item 3

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[2]->id;
        $grade->userid = 1;
        $grade->rawgrade = 2;
        $grade->finalgrade = 6;
        $grade->scaleid = $this->scale[3]->id;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '7 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[6] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[2]->id;
        $grade->userid = 2;
        $grade->rawgrade = 3;
        $grade->finalgrade = 2;
        $grade->scaleid = $this->scale[3]->id;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '8 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[2]->id;
        $grade->userid = 3;
        $grade->rawgrade = 1;
        $grade->finalgrade = 3;
        $grade->scaleid = $this->scale[3]->id;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '9 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        // Grades for grade_item 7

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[6]->id;
        $grade->userid = 1;
        $grade->rawgrade = 97;
        $grade->finalgrade = 69;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '10 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[6]->id;
        $grade->userid = 2;
        $grade->rawgrade = 49;
        $grade->finalgrade = 87;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '11 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[6]->id;
        $grade->userid = 3;
        $grade->rawgrade = 67;
        $grade->finalgrade = 94;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '12 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        // Grades for grade_item 8

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[7]->id;
        $grade->userid = 2;
        $grade->rawgrade = 3;
        $grade->finalgrade = 3;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '13 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[7]->id;
        $grade->userid = 3;
        $grade->rawgrade = 6;
        $grade->finalgrade = 6;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '14 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        // Grades for grade_item 9

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[8]->id;
        $grade->userid = 1;
        $grade->rawgrade = 20;
        $grade->finalgrade = 20;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '15 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[8]->id;
        $grade->userid = 2;
        $grade->rawgrade = 50;
        $grade->finalgrade = 50;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '16 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[8]->id;
        $grade->userid = 3;
        $grade->rawgrade = 100;
        $grade->finalgrade = 100;
        $grade->timecreated = mktime();
        $grade->timemodified = mktime();
        $grade->information = '17 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;
    }

    /**
     * Load grade_outcome data into the database, and adds the corresponding objects to this class' variable.
     */
    function load_grade_outcomes() {
        global $DB;

        //this method is called once for each test method. Avoid adding things to $this->grade_outcomes multiple times
        $this->grade_outcomes = array();

        // Calculation for grade_item 1
        $grade_outcome = new stdClass();
        $grade_outcome->fullname = 'Team work';
        $grade_outcome->shortname = 'Team work';
        $grade_outcome->fullname = 'Team work outcome';
        $grade_outcome->timecreated = mktime();
        $grade_outcome->timemodified = mktime();
        $grade_outcome->scaleid = $this->scale[2]->id;

        $grade_outcome->id = $DB->insert_record('grade_outcomes', $grade_outcome);
        $this->grade_outcomes[] = $grade_outcome;

        // Calculation for grade_item 2
        $grade_outcome = new stdClass();
        $grade_outcome->fullname = 'Complete circuit board';
        $grade_outcome->shortname = 'Complete circuit board';
        $grade_outcome->fullname = 'Complete circuit board';
        $grade_outcome->timecreated = mktime();
        $grade_outcome->timemodified = mktime();
        $grade_outcome->scaleid = $this->scale[3]->id;

        $grade_outcome->id = $DB->insert_record('grade_outcomes', $grade_outcome);
        $this->grade_outcomes[] = $grade_outcome;

        // Calculation for grade_item 3
        $grade_outcome = new stdClass();
        $grade_outcome->fullname = 'Debug Java program';
        $grade_outcome->shortname = 'Debug Java program';
        $grade_outcome->fullname = 'Debug Java program';
        $grade_outcome->timecreated = mktime();
        $grade_outcome->timemodified = mktime();
        $grade_outcome->scaleid = $this->scale[4]->id;

        $grade_outcome->id = $DB->insert_record('grade_outcomes', $grade_outcome);
        $this->grade_outcomes[] = $grade_outcome;
    }

/**
 * No unit tests here
 */

}


