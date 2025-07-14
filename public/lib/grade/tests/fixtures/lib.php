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
 * @package    core_grades
 * @category   phpunit
 * @copyright  nicolas@moodle.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/gradelib.php');


/**
 * Shared code for all grade related tests.
 *
 * Here is a brief explanation of the test data set up in these unit tests.
 * category1 => array(category2 => array(grade_item1, grade_item2), category3 => array(grade_item3))
 * 3 users for 3 grade_items
 */
abstract class grade_base_testcase extends advanced_testcase {

    protected $course;
    protected $activities = array();
    protected $grade_items = array();
    protected $grade_categories = array();
    protected $grade_grades = array();
    protected $grade_outcomes = array();
    protected $scale = array();
    protected $scalemax = array();

    protected $courseid;
    protected $userid;

    /** @var array user object collection. */
    protected $user = [];

    /** @var array module object collection. */
    protected $course_module = [];

    protected function setUp(): void {
        global $CFG;
        parent::setup();

        $this->resetAfterTest(true);

        $CFG->grade_droplow = -1;
        $CFG->grade_keephigh = -1;
        $CFG->grade_aggregation = -1;
        $CFG->grade_aggregateonlygraded = -1;
        $CFG->grade_aggregateoutcomes = -1;

        $this->course = $this->getDataGenerator()->create_course();
        $this->courseid = $this->course->id;

        $this->user[0] = $this->getDataGenerator()->create_user();
        $this->user[1] = $this->getDataGenerator()->create_user();
        $this->user[2] = $this->getDataGenerator()->create_user();
        $this->user[3] = $this->getDataGenerator()->create_user();
        $this->userid = $this->user[0]->id;

        $this->load_modules();

        $this->load_scales();
        $this->load_grade_categories();
        $this->load_grade_items();
        $this->load_grade_grades();
        $this->load_grade_outcomes();
    }

    private function load_modules() {
        $this->activities[0] = $this->getDataGenerator()->create_module('assign', array('course'=>$this->course->id));
        $this->course_module[0] = get_coursemodule_from_instance('assign', $this->activities[0]->id);

        $this->activities[1] = $this->getDataGenerator()->create_module('assign', array('course'=>$this->course->id));
        $this->course_module[1] = get_coursemodule_from_instance('assign', $this->activities[1]->id);

        $this->activities[2] = $this->getDataGenerator()->create_module('forum', array('course'=>$this->course->id));
        $this->course_module[2] = get_coursemodule_from_instance('forum', $this->activities[2]->id);

        $this->activities[3] = $this->getDataGenerator()->create_module('page', array('course'=>$this->course->id));
        $this->course_module[3] = get_coursemodule_from_instance('page', $this->activities[3]->id);

        $this->activities[4] = $this->getDataGenerator()->create_module('forum', array('course'=>$this->course->id));
        $this->course_module[4] = get_coursemodule_from_instance('forum', $this->activities[4]->id);

        $this->activities[5] = $this->getDataGenerator()->create_module('forum', array('course'=>$this->course->id));
        $this->course_module[5] = get_coursemodule_from_instance('forum', $this->activities[5]->id);

        $this->activities[6] = $this->getDataGenerator()->create_module('forum', array('course'=>$this->course->id));
        $this->course_module[6] = get_coursemodule_from_instance('forum', $this->activities[6]->id);

        $this->activities[7] = $this->getDataGenerator()->create_module('quiz', array('course'=>$this->course->id));
        $this->course_module[7] = get_coursemodule_from_instance('quiz', $this->activities[7]->id);
    }

    private function load_scales() {
        $scale = new stdClass();
        $scale->name        = 'unittestscale1';
        $scale->courseid    = $this->course->id;
        $scale->userid      = $this->user[0]->id;
        $scale->scale       = 'Way off topic, Not very helpful, Fairly neutral, Fairly helpful, Supportive, Some good information, Perfect answer!';
        $scale->description = 'This scale defines some of qualities that make posts helpful within the Moodle help forums.\n Your feedback will help others see how their posts are being received.';

        $this->scale[0] = $this->getDataGenerator()->create_scale($scale);
        $this->scalemax[0] = substr_count($scale->scale, ',');

        $scale = new stdClass();
        $scale->name        = 'unittestscale2';
        $scale->courseid    = $this->course->id;
        $scale->userid      = $this->user[0]->id;
        $scale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $scale->description = 'This scale is used to mark standard assignments.';

        $this->scale[1] = $this->getDataGenerator()->create_scale($scale);
        $this->scalemax[1] = substr_count($scale->scale, ',');

        $scale = new stdClass();
        $scale->name        = 'unittestscale3';
        $scale->courseid    = $this->course->id;
        $scale->userid      = $this->user[0]->id;
        $scale->scale       = 'Loner, Contentious, Disinterested, Participative, Follower, Leader';
        $scale->description = 'Describes the level of teamwork of a student.';
        $temp  = explode(',', $scale->scale);
        $scale->max         = count($temp) -1;

        $this->scale[2] = $this->getDataGenerator()->create_scale($scale);
        $this->scalemax[2] = substr_count($scale->scale, ',');

        $scale = new stdClass();
        $scale->name        = 'unittestscale4';
        $scale->courseid    = $this->course->id;
        $scale->userid      = $this->user[0]->id;
        $scale->scale       = 'Does not understand theory, Understands theory but fails practice, Manages through, Excels';
        $scale->description = 'Level of expertise at a technical task, with a theoretical framework.';
        $temp  = explode(',', $scale->scale);
        $scale->max         = count($temp) -1;

        $this->scale[3] = $this->getDataGenerator()->create_scale($scale);
        $this->scalemax[3] = substr_count($scale->scale, ',');

        $scale = new stdClass();
        $scale->name        = 'unittestscale5';
        $scale->courseid    = $this->course->id;
        $scale->userid      = $this->user[0]->id;
        $scale->scale       = 'Insufficient, Acceptable, Excellent.';
        $scale->description = 'Description of skills.';

        $this->scale[4] = $this->getDataGenerator()->create_scale($scale);
        $this->scalemax[4] = substr_count($scale->scale, ',');
    }

    /**
     * Load grade_category data into the database, and adds the corresponding objects to this class' variable.
     * category structure:
                              course category
                                    |
                           +--------+-------------+-----------------------+
                           |                      |                       |
             unittestcategory1               level1category       unittestcategory7
                  |                                                          |
         +--------+-------------+                               +------------+---------------+
         |                      |                               |                            |
        unittestcategory2  unittestcategory3          unittestcategory5               unittestcategory6
     */
    private function load_grade_categories() {
        global $DB;

        $course_category = grade_category::fetch_course_category($this->course->id);

        $grade_category = new stdClass();

        $grade_category->fullname    = 'unittestcategory1 &';
        $grade_category->courseid    = $this->course->id;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $course_category->id;
        $grade_category->timecreated = time();
        $grade_category->timemodified = time();
        $grade_category->depth = 2;

        $grade_category->id = $DB->insert_record('grade_categories', $grade_category);
        $grade_category->path = '/'.$course_category->id.'/'.$grade_category->id.'/';
        $DB->update_record('grade_categories', $grade_category);
        $this->grade_categories[0] = $grade_category;

        $grade_category = new stdClass();

        $grade_category->fullname    = 'unittestcategory2';
        $grade_category->courseid    = $this->course->id;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $this->grade_categories[0]->id;
        $grade_category->timecreated = time();
        $grade_category->timemodified = time();
        $grade_category->depth = 3;

        $grade_category->id = $DB->insert_record('grade_categories', $grade_category);
        $grade_category->path = $this->grade_categories[0]->path.$grade_category->id.'/';
        $DB->update_record('grade_categories', $grade_category);
        $this->grade_categories[1] = $grade_category;

        $grade_category = new stdClass();

        $grade_category->fullname    = 'unittestcategory3';
        $grade_category->courseid    = $this->course->id;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $this->grade_categories[0]->id;
        $grade_category->timecreated = time();
        $grade_category->timemodified = time();
        $grade_category->depth = 3;

        $grade_category->id = $DB->insert_record('grade_categories', $grade_category);
        $grade_category->path = $this->grade_categories[0]->path.$grade_category->id.'/';
        $DB->update_record('grade_categories', $grade_category);
        $this->grade_categories[2] = $grade_category;

        // A category with no parent, but grade_items as children.

        $grade_category = new stdClass();

        $grade_category->fullname    = 'level1category';
        $grade_category->courseid    = $this->course->id;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $course_category->id;
        $grade_category->timecreated = time();
        $grade_category->timemodified = time();
        $grade_category->depth = 2;

        $grade_category->id = $DB->insert_record('grade_categories', $grade_category);
        $grade_category->path = '/'.$course_category->id.'/'.$grade_category->id.'/';
        $DB->update_record('grade_categories', $grade_category);
        $this->grade_categories[3] = $grade_category;

        $grade_category = new stdClass();

        $grade_category->fullname    = 'unittestcategory7';
        $grade_category->courseid    = $this->course->id;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $course_category->id;
        $grade_category->timecreated = time();
        $grade_category->timemodified = time();
        $grade_category->depth = 2;

        $grade_category->id = $DB->insert_record('grade_categories', $grade_category);
        $grade_category->path = '/'.$course_category->id.'/'.$grade_category->id.'/';
        $DB->update_record('grade_categories', $grade_category);
        $this->grade_categories[4] = $grade_category;

        $grade_category = new stdClass();

        $grade_category->fullname    = 'unittestcategory5';
        $grade_category->courseid    = $this->course->id;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $this->grade_categories[4]->id;
        $grade_category->timecreated = time();
        $grade_category->timemodified = time();
        $grade_category->depth = 3;

        $grade_category->id = $DB->insert_record('grade_categories', $grade_category);
        $grade_category->path = $this->grade_categories[4]->path.$grade_category->id.'/';
        $DB->update_record('grade_categories', $grade_category);
        $this->grade_categories[5] = $grade_category;

        $grade_category = new stdClass();

        $grade_category->fullname    = 'unittestcategory6';
        $grade_category->courseid    = $this->course->id;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 0;
        $grade_category->droplow     = 0;
        $grade_category->parent      = $this->grade_categories[4]->id;
        $grade_category->timecreated = time();
        $grade_category->timemodified = time();
        $grade_category->depth = 3;

        $grade_category->id = $DB->insert_record('grade_categories', $grade_category);
        $grade_category->path = $this->grade_categories[4]->path.$grade_category->id.'/';
        $DB->update_record('grade_categories', $grade_category);
        $this->grade_categories[6] = $grade_category;
    }

    /**
     * Load grade_item data into the database, and adds the corresponding objects to this class' variable.
     */
    protected function load_grade_items() {
        global $DB;

        // Purge all items created by module generators.
        $DB->delete_records('grade_items', array('itemtype'=>'mod'));

        $course_category = grade_category::fetch_course_category($this->course->id);

        // id = 0
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->categoryid = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitem1 &';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = $this->course_module[0]->modname;
        $grade_item->iteminstance = $this->course_module[0]->instance;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 30;
        $grade_item->grademax = 110;
        $grade_item->itemnumber = 1;
        $grade_item->idnumber = 'item id 0';
        $grade_item->iteminfo = 'Grade item 0 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 3;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[0] = $grade_item;

        // id = 1
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->categoryid = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitem2';
        $grade_item->itemtype = 'import';
        $grade_item->itemmodule = $this->course_module[1]->modname;
        $grade_item->iteminstance = $this->course_module[1]->instance;
        $grade_item->calculation = '= ##gi'.$this->grade_items[0]->id.'## + 30 + [[item id 0]] - [[item id 0]]';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->itemnumber = null;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 1 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 4;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[1] = $grade_item;

        // id = 2
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->categoryid = $this->grade_categories[2]->id;
        $grade_item->itemname = 'unittestgradeitem3';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = $this->course_module[2]->modname;
        $grade_item->iteminstance = $this->course_module[2]->instance;
        $grade_item->gradetype = GRADE_TYPE_SCALE;
        $grade_item->scaleid = $this->scale[0]->id;
        $grade_item->grademin = 0;
        $grade_item->grademax = $this->scalemax[0];
        $grade_item->iteminfo = 'Grade item 2 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 6;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[2] = $grade_item;

        // Load grade_items associated with the 3 categories.
        // id = 3
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->iteminstance = $this->grade_categories[0]->id;
        $grade_item->itemname = 'unittestgradeitemcategory1';
        $grade_item->needsupdate = 0;
        $grade_item->itemtype = 'category';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 3 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 1;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[3] = $grade_item;

        // id = 4
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->iteminstance = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitemcategory2';
        $grade_item->itemtype = 'category';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->needsupdate = 0;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 4 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 2;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[4] = $grade_item;

        // id = 5
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->iteminstance = $this->grade_categories[2]->id;
        $grade_item->itemname = 'unittestgradeitemcategory3';
        $grade_item->itemtype = 'category';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->needsupdate = true;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 5 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 5;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[5] = $grade_item;

        // Orphan grade_item.
        // id = 6
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->categoryid = $course_category->id;
        $grade_item->itemname = 'unittestorphangradeitem1';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = $this->course_module[4]->modname;
        $grade_item->iteminstance = $this->course_module[4]->instance;
        $grade_item->itemnumber = 0;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 10;
        $grade_item->grademax = 120;
        $grade_item->locked = time();
        $grade_item->iteminfo = 'Orphan Grade 6 item used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 7;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[6] = $grade_item;

        // 2 grade items under level1category.
        // id = 7
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->categoryid = $this->grade_categories[3]->id;
        $grade_item->itemname = 'singleparentitem1';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = $this->course_module[5]->modname;
        $grade_item->iteminstance = $this->course_module[5]->instance;
        $grade_item->gradetype = GRADE_TYPE_SCALE;
        $grade_item->scaleid = $this->scale[0]->id;
        $grade_item->grademin = 0;
        $grade_item->grademax = $this->scalemax[0];
        $grade_item->iteminfo = 'Grade item 7 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 9;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[7] = $grade_item;

        // id = 8
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->categoryid = $this->grade_categories[3]->id;
        $grade_item->itemname = 'singleparentitem2';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = $this->course_module[6]->modname;
        $grade_item->iteminstance = $this->course_module[6]->instance;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 8 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 10;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[8] = $grade_item;

        // Grade_item for level1category.
        // id = 9
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->itemname = 'grade_item for level1 category';
        $grade_item->itemtype = 'category';
        $grade_item->iteminstance = $this->grade_categories[3]->id;
        $grade_item->needsupdate = true;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Orphan Grade item 9 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 8;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[9] = $grade_item;

        // Manual grade_item.
        // id = 10
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->categoryid = $course_category->id;
        $grade_item->itemname = 'manual grade_item';
        $grade_item->itemtype = 'manual';
        $grade_item->itemnumber = 0;
        $grade_item->needsupdate = false;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Manual grade item 10 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 10;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[10] = $grade_item;

        // Quiz grade_item (course_module = 7).
        // id = 11
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->categoryid = $course_category->id;
        $grade_item->itemname = 'Quiz grade item';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = $this->course_module[7]->modname;
        $grade_item->iteminstance = $this->course_module[7]->instance;
        $grade_item->itemnumber = 0;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->locked = 0;
        $grade_item->iteminfo = 'Quiz grade item used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 11;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[11] = $grade_item;

        // id = 12
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->iteminstance = $this->grade_categories[4]->id;
        $grade_item->itemname = 'unittestgradeitemcategory7';
        $grade_item->itemtype = 'category';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->needsupdate = true;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 12 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 12;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[12] = $grade_item;

        // id = 13
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->iteminstance = $this->grade_categories[5]->id;
        $grade_item->itemname = 'unittestgradeitemcategory5';
        $grade_item->itemtype = 'category';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->needsupdate = true;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 13 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 13;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[13] = $grade_item;

        // id = 14
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->iteminstance = $this->grade_categories[6]->id;
        $grade_item->itemname = 'unittestgradeitemcategory6';
        $grade_item->itemtype = 'category';
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->needsupdate = true;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Grade item 14 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 14;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[14] = $grade_item;

        // Manual grade_item
        // id = 15
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->categoryid = $this->grade_categories[5]->id;
        $grade_item->itemname = 'manual grade_item';
        $grade_item->itemtype = 'manual';
        $grade_item->itemnumber = 0;
        $grade_item->needsupdate = false;
        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Manual grade item 15 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 15;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[15] = $grade_item;

        // Manual grade_item
        // id = 16
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->categoryid = $this->grade_categories[6]->id;
        $grade_item->itemname = 'manual grade_item';
        $grade_item->itemtype = 'manual';
        $grade_item->itemnumber = 0;
        $grade_item->needsupdate = false;
        $grade_item->gradetype = GRADE_TYPE_SCALE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Manual grade item 16 used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->sortorder = 16;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[16] = $grade_item;

        // $this->grade_items[17] loaded in load_grade_outcomes() in order to use an outcome id.
    }

    /**
     * Load grade_grades data into the database, and adds the corresponding objects to this class' variable.
     */
    private function load_grade_grades() {
        global $DB;

        // This method is called once for each test method. Avoid adding things to $this->grade_grades multiple times.
        $this->grade_grades = array();

        // Grades for grade_item 1.
        $grade = new stdClass();
        $grade->itemid = $this->grade_items[0]->id;
        $grade->userid = $this->user[1]->id;
        $grade->rawgrade = 15; // too small
        $grade->finalgrade = 30;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '1 of 17 grade_grades';
        $grade->informationformat = FORMAT_PLAIN;
        $grade->feedback = 'Good, but not good enough..';
        $grade->feedbackformat = FORMAT_PLAIN;

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[0] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[0]->id;
        $grade->userid = $this->user[2]->id;
        $grade->rawgrade = 40;
        $grade->finalgrade = 40;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '2 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[1] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[0]->id;
        $grade->userid = $this->user[3]->id;
        $grade->rawgrade = 170; // too big
        $grade->finalgrade = 110;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '3 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[2] = $grade;


        // No raw grades for grade_item 2 - it is calculated.

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[1]->id;
        $grade->userid = $this->user[1]->id;
        $grade->finalgrade = 60;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '4 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[3] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[1]->id;
        $grade->userid = $this->user[2]->id;
        $grade->finalgrade = 70;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '5 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[4] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[1]->id;
        $grade->userid = $this->user[3]->id;
        $grade->finalgrade = 100;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '6 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[5] = $grade;


        // Grades for grade_item 3.

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[2]->id;
        $grade->userid = $this->user[1]->id;
        $grade->rawgrade = 2;
        $grade->finalgrade = 6;
        $grade->scaleid = $this->scale[3]->id;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '7 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[6] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[2]->id;
        $grade->userid = $this->user[2]->id;
        $grade->rawgrade = 3;
        $grade->finalgrade = 2;
        $grade->scaleid = $this->scale[3]->id;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '8 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[2]->id;
        $grade->userid = $this->user[3]->id;
        $grade->rawgrade = 1;
        $grade->finalgrade = 3;
        $grade->scaleid = $this->scale[3]->id;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '9 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        // Grades for grade_item 7.

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[6]->id;
        $grade->userid = $this->user[1]->id;
        $grade->rawgrade = 97;
        $grade->finalgrade = 69;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '10 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[6]->id;
        $grade->userid = $this->user[2]->id;
        $grade->rawgrade = 49;
        $grade->finalgrade = 87;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '11 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[6]->id;
        $grade->userid = $this->user[3]->id;
        $grade->rawgrade = 67;
        $grade->finalgrade = 94;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '12 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        // Grades for grade_item 8.

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[7]->id;
        $grade->userid = $this->user[2]->id;
        $grade->rawgrade = 3;
        $grade->finalgrade = 3;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '13 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[7]->id;
        $grade->userid = $this->user[3]->id;
        $grade->rawgrade = 6;
        $grade->finalgrade = 6;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '14 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        // Grades for grade_item 9.

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[8]->id;
        $grade->userid = $this->user[1]->id;
        $grade->rawgrade = 20;
        $grade->finalgrade = 20;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '15 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[8]->id;
        $grade->userid = $this->user[2]->id;
        $grade->rawgrade = 50;
        $grade->finalgrade = 50;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '16 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;

        $grade = new stdClass();
        $grade->itemid = $this->grade_items[8]->id;
        $grade->userid = $this->user[3]->id;
        $grade->rawgrade = 100;
        $grade->finalgrade = 100;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '17 of 17 grade_grades';

        $grade->id = $DB->insert_record('grade_grades', $grade);
        $this->grade_grades[] = $grade;
    }

    /**
     * Load grade_outcome data into the database, and adds the corresponding objects to this class' variable.
     */
    private function load_grade_outcomes() {
        global $DB;

        // This method is called once for each test method. Avoid adding things to $this->grade_outcomes multiple times.
        $this->grade_outcomes = array();

        // Calculation for grade_item 1.
        $grade_outcome = new stdClass();
        $grade_outcome->fullname = 'Team work';
        $grade_outcome->shortname = 'Team work';
        $grade_outcome->fullname = 'Team work outcome';
        $grade_outcome->timecreated = time();
        $grade_outcome->timemodified = time();
        $grade_outcome->scaleid = $this->scale[2]->id;

        $grade_outcome->id = $DB->insert_record('grade_outcomes', $grade_outcome);
        $this->grade_outcomes[] = $grade_outcome;

        // Calculation for grade_item 2.
        $grade_outcome = new stdClass();
        $grade_outcome->fullname = 'Complete circuit board';
        $grade_outcome->shortname = 'Complete circuit board';
        $grade_outcome->fullname = 'Complete circuit board';
        $grade_outcome->timecreated = time();
        $grade_outcome->timemodified = time();
        $grade_outcome->scaleid = $this->scale[3]->id;

        $grade_outcome->id = $DB->insert_record('grade_outcomes', $grade_outcome);
        $this->grade_outcomes[] = $grade_outcome;

        // Calculation for grade_item 3.
        $grade_outcome = new stdClass();
        $grade_outcome->fullname = 'Debug Java program';
        $grade_outcome->shortname = 'Debug Java program';
        $grade_outcome->fullname = 'Debug Java program';
        $grade_outcome->timecreated = time();
        $grade_outcome->timemodified = time();
        $grade_outcome->scaleid = $this->scale[4]->id;

        $grade_outcome->id = $DB->insert_record('grade_outcomes', $grade_outcome);
        $this->grade_outcomes[] = $grade_outcome;

        // Manual grade_item with outcome
        // id = 17
        $grade_item = new stdClass();

        $grade_item->courseid = $this->course->id;
        $grade_item->categoryid = $this->grade_categories[6]->id;
        $grade_item->itemname = 'manual grade_item';
        $grade_item->itemtype = 'manual';
        $grade_item->itemnumber = 0;
        $grade_item->needsupdate = false;
        $grade_item->gradetype = GRADE_TYPE_SCALE;
        $grade_item->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->iteminfo = 'Manual grade item 16 with outcome used for unit testing';
        $grade_item->timecreated = time();
        $grade_item->timemodified = time();
        $grade_item->outcomeid = $this->grade_outcomes[2]->id;
        $grade_item->sortorder = 17;

        $grade_item->id = $DB->insert_record('grade_items', $grade_item);
        $this->grade_items[17] = $grade_item;
    }
}

/**
 * Allow calling protected method.
 */
class test_grade_grade_flatten_dependencies_array extends grade_grade {
    public static function test_flatten_dependencies_array(&$a,&$b) {
        return self::flatten_dependencies_array($a, $b);
    }
}

