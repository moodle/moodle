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
 * Unit tests for the lib/upgradelib.php library.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2013 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/upgradelib.php');
require_once($CFG->libdir.'/db/upgradelib.php');
require_once($CFG->dirroot . '/calendar/tests/helpers.php');

/**
 * Tests various classes and functions in upgradelib.php library.
 */
class upgradelib_test extends advanced_testcase {

    /**
     * Test the {@link upgrade_stale_php_files_present() function
     */
    public function test_upgrade_stale_php_files_present(): void {
        // Just call the function, must return bool false always
        // if there aren't any old files in the codebase.
        $this->assertFalse(upgrade_stale_php_files_present());
    }

    /**
     * Populate some fake grade items into the database with specified
     * sortorder and course id.
     *
     * NOTE: This function doesn't make much attempt to respect the
     * gradebook internals, its simply used to fake some data for
     * testing the upgradelib function. Please don't use it for other
     * purposes.
     *
     * @param int $courseid id of course
     * @param int $sortorder numeric sorting order of item
     * @return stdClass grade item object from the database.
     */
    private function insert_fake_grade_item_sortorder($courseid, $sortorder) {
        global $DB, $CFG;
        require_once($CFG->libdir.'/gradelib.php');

        $item = new stdClass();
        $item->courseid = $courseid;
        $item->sortorder = $sortorder;
        $item->gradetype = GRADE_TYPE_VALUE;
        $item->grademin = 30;
        $item->grademax = 110;
        $item->itemnumber = 1;
        $item->iteminfo = '';
        $item->timecreated = time();
        $item->timemodified = time();

        $item->id = $DB->insert_record('grade_items', $item);

        return $DB->get_record('grade_items', array('id' => $item->id));
    }

    public function test_upgrade_extra_credit_weightoverride(): void {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        require_once($CFG->libdir . '/db/upgradelib.php');

        $c = array();
        $a = array();
        $gi = array();
        for ($i=0; $i<5; $i++) {
            $c[$i] = $this->getDataGenerator()->create_course();
            $a[$i] = array();
            $gi[$i] = array();
            for ($j=0;$j<3;$j++) {
                $a[$i][$j] = $this->getDataGenerator()->create_module('assign', array('course' => $c[$i], 'grade' => 100));
                $giparams = array('itemtype' => 'mod', 'itemmodule' => 'assign', 'iteminstance' => $a[$i][$j]->id,
                    'courseid' => $c[$i]->id, 'itemnumber' => 0);
                $gi[$i][$j] = grade_item::fetch($giparams);
            }
        }

        // Case 1: Course $c[0] has aggregation method different from natural.
        $coursecategory = grade_category::fetch_course_category($c[0]->id);
        $coursecategory->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN;
        $coursecategory->update();
        $gi[0][1]->aggregationcoef = 1;
        $gi[0][1]->update();
        $gi[0][2]->weightoverride = 1;
        $gi[0][2]->update();

        // Case 2: Course $c[1] has neither extra credits nor overrides

        // Case 3: Course $c[2] has extra credits but no overrides
        $gi[2][1]->aggregationcoef = 1;
        $gi[2][1]->update();

        // Case 4: Course $c[3] has no extra credits and has overrides
        $gi[3][2]->weightoverride = 1;
        $gi[3][2]->update();

        // Case 5: Course $c[4] has both extra credits and overrides
        $gi[4][1]->aggregationcoef = 1;
        $gi[4][1]->update();
        $gi[4][2]->weightoverride = 1;
        $gi[4][2]->update();

        // Run the upgrade script and make sure only course $c[4] was marked as needed to be fixed.
        upgrade_extra_credit_weightoverride();

        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $c[0]->id}));
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $c[1]->id}));
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $c[2]->id}));
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $c[3]->id}));
        $this->assertEquals(20150619, $CFG->{'gradebook_calculations_freeze_' . $c[4]->id});

        set_config('gradebook_calculations_freeze_' . $c[4]->id, null);

        // Run the upgrade script for a single course only.
        upgrade_extra_credit_weightoverride($c[0]->id);
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $c[0]->id}));
        upgrade_extra_credit_weightoverride($c[4]->id);
        $this->assertEquals(20150619, $CFG->{'gradebook_calculations_freeze_' . $c[4]->id});
    }

    /**
     * Test the upgrade function for flagging courses with calculated grade item problems.
     */
    public function test_upgrade_calculated_grade_items_freeze(): void {
        global $DB, $CFG;

        $this->resetAfterTest();

        require_once($CFG->libdir . '/db/upgradelib.php');

        // Create a user.
        $user = $this->getDataGenerator()->create_user();

        // Create a couple of courses.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        // Enrol the user in the courses.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $maninstance1 = $DB->get_record('enrol', array('courseid' => $course1->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        $maninstance2 = $DB->get_record('enrol', array('courseid' => $course2->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        $maninstance3 = $DB->get_record('enrol', array('courseid' => $course3->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        $manual = enrol_get_plugin('manual');
        $manual->enrol_user($maninstance1, $user->id, $studentrole->id);
        $manual->enrol_user($maninstance2, $user->id, $studentrole->id);
        $manual->enrol_user($maninstance3, $user->id, $studentrole->id);

        // To create the data we need we freeze the grade book to use the old behaviour.
        set_config('gradebook_calculations_freeze_' . $course1->id, 20150627);
        set_config('gradebook_calculations_freeze_' . $course2->id, 20150627);
        set_config('gradebook_calculations_freeze_' . $course3->id, 20150627);
        $CFG->grade_minmaxtouse = 2;

        // Creating a category for a grade item.
        $gradecategory = new grade_category();
        $gradecategory->fullname = 'calculated grade category';
        $gradecategory->courseid = $course1->id;
        $gradecategory->insert();
        $gradecategoryid = $gradecategory->id;

        // This is a manual grade item.
        $gradeitem = new grade_item();
        $gradeitem->itemname = 'grade item one';
        $gradeitem->itemtype = 'manual';
        $gradeitem->categoryid = $gradecategoryid;
        $gradeitem->courseid = $course1->id;
        $gradeitem->idnumber = 'gi1';
        $gradeitem->insert();

        // Changing the category into a calculated grade category.
        $gradecategoryitem = grade_item::fetch(array('iteminstance' => $gradecategory->id));
        $gradecategoryitem->calculation = '=##gi' . $gradeitem->id . '##/2';
        $gradecategoryitem->update();

        // Setting a grade for the student.
        $grade = $gradeitem->get_grade($user->id, true);
        $grade->finalgrade = 50;
        $grade->update();
        // Creating all the grade_grade items.
        grade_regrade_final_grades($course1->id);
        // Updating the grade category to a new grade max and min.
        $gradecategoryitem->grademax = 50;
        $gradecategoryitem->grademin = 5;
        $gradecategoryitem->update();

        // Different manual grade item for course 2. We are creating a course with a calculated grade item that has a grade max of
        // 50. The grade_grade will have a rawgrademax of 100 regardless.
        $gradeitem = new grade_item();
        $gradeitem->itemname = 'grade item one';
        $gradeitem->itemtype = 'manual';
        $gradeitem->courseid = $course2->id;
        $gradeitem->idnumber = 'gi1';
        $gradeitem->grademax = 25;
        $gradeitem->insert();

        // Calculated grade item for course 2.
        $calculatedgradeitem = new grade_item();
        $calculatedgradeitem->itemname = 'calculated grade';
        $calculatedgradeitem->itemtype = 'manual';
        $calculatedgradeitem->courseid = $course2->id;
        $calculatedgradeitem->calculation = '=##gi' . $gradeitem->id . '##*2';
        $calculatedgradeitem->grademax = 50;
        $calculatedgradeitem->insert();

        // Assigning a grade for the user.
        $grade = $gradeitem->get_grade($user->id, true);
        $grade->finalgrade = 10;
        $grade->update();

        // Setting all of the grade_grade items.
        grade_regrade_final_grades($course2->id);

        // Different manual grade item for course 3. We are creating a course with a calculated grade item that has a grade max of
        // 50. The grade_grade will have a rawgrademax of 100 regardless.
        $gradeitem = new grade_item();
        $gradeitem->itemname = 'grade item one';
        $gradeitem->itemtype = 'manual';
        $gradeitem->courseid = $course3->id;
        $gradeitem->idnumber = 'gi1';
        $gradeitem->grademax = 25;
        $gradeitem->insert();

        // Calculated grade item for course 2.
        $calculatedgradeitem = new grade_item();
        $calculatedgradeitem->itemname = 'calculated grade';
        $calculatedgradeitem->itemtype = 'manual';
        $calculatedgradeitem->courseid = $course3->id;
        $calculatedgradeitem->calculation = '=##gi' . $gradeitem->id . '##*2';
        $calculatedgradeitem->grademax = 50;
        $calculatedgradeitem->insert();

        // Assigning a grade for the user.
        $grade = $gradeitem->get_grade($user->id, true);
        $grade->finalgrade = 10;
        $grade->update();

        // Setting all of the grade_grade items.
        grade_regrade_final_grades($course3->id);
        // Need to do this first before changing the other courses, otherwise they will be flagged too early.
        set_config('gradebook_calculations_freeze_' . $course3->id, null);
        upgrade_calculated_grade_items($course3->id);
        $this->assertEquals(20150627, $CFG->{'gradebook_calculations_freeze_' . $course3->id});

        // Change the setting back to null.
        set_config('gradebook_calculations_freeze_' . $course1->id, null);
        set_config('gradebook_calculations_freeze_' . $course2->id, null);
        // Run the upgrade.
        upgrade_calculated_grade_items();
        // The setting should be set again after the upgrade.
        $this->assertEquals(20150627, $CFG->{'gradebook_calculations_freeze_' . $course1->id});
        $this->assertEquals(20150627, $CFG->{'gradebook_calculations_freeze_' . $course2->id});
    }

    /**
     * Test the upgrade function for final grade after setting grade max for category and grade item.
     */
    public function test_upgrade_update_category_grademax_regrade_final_grades(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();

        // Create a new course.
        $course = $generator->create_course();

        // Set the course aggregation to weighted mean of grades.
        $unitcategory = \grade_category::fetch_course_category($course->id);
        $unitcategory->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN;
        $unitcategory->update();

        // Set grade max for category.
        $gradecategoryitem = grade_item::fetch(array('iteminstance' => $unitcategory->id));
        $gradecategoryitem->grademax = 50;
        $gradecategoryitem->update();

        // Make new grade item.
        $gradeitem = new \grade_item($generator->create_grade_item([
            'itemname'        => 'Grade item',
            'idnumber'        => 'git1',
            'courseid'        => $course->id,
            'grademin'        => 0,
            'grademax'        => 50,
            'aggregationcoef' => 100.0,
        ]));

        // Set final grade.
        $grade = $gradeitem->get_grade($user->id, true);
        $grade->finalgrade = 20;
        $grade->update();

        $courseitem = \grade_item::fetch(['courseid' => $course->id, 'itemtype' => 'course']);
        $gradeitem->force_regrading();

        // Trigger regrade because the grade items needs to be updated.
        grade_regrade_final_grades($course->id);

        $coursegrade = new \grade_grade($courseitem->get_final($user->id), false);
        $this->assertEquals(20, $coursegrade->finalgrade);
    }

    function test_upgrade_calculated_grade_items_regrade(): void {
        global $DB, $CFG;

        $this->resetAfterTest();

        require_once($CFG->libdir . '/db/upgradelib.php');

        // Create a user.
        $user = $this->getDataGenerator()->create_user();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Enrol the user in the course.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $maninstance1 = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        $manual = enrol_get_plugin('manual');
        $manual->enrol_user($maninstance1, $user->id, $studentrole->id);

        set_config('upgrade_calculatedgradeitemsonlyregrade', 1);

        // Creating a category for a grade item.
        $gradecategory = new grade_category();
        $gradecategory->fullname = 'calculated grade category';
        $gradecategory->courseid = $course->id;
        $gradecategory->insert();
        $gradecategoryid = $gradecategory->id;

        // This is a manual grade item.
        $gradeitem = new grade_item();
        $gradeitem->itemname = 'grade item one';
        $gradeitem->itemtype = 'manual';
        $gradeitem->categoryid = $gradecategoryid;
        $gradeitem->courseid = $course->id;
        $gradeitem->idnumber = 'gi1';
        $gradeitem->insert();

        // Changing the category into a calculated grade category.
        $gradecategoryitem = grade_item::fetch(array('iteminstance' => $gradecategory->id));
        $gradecategoryitem->calculation = '=##gi' . $gradeitem->id . '##/2';
        $gradecategoryitem->grademax = 50;
        $gradecategoryitem->grademin = 15;
        $gradecategoryitem->update();

        // Setting a grade for the student.
        $grade = $gradeitem->get_grade($user->id, true);
        $grade->finalgrade = 50;
        $grade->update();

        grade_regrade_final_grades($course->id);
        $grade = grade_grade::fetch(array('itemid' => $gradecategoryitem->id, 'userid' => $user->id));
        $grade->rawgrademax = 100;
        $grade->rawgrademin = 0;
        $grade->update();
        $this->assertNotEquals($gradecategoryitem->grademax, $grade->rawgrademax);
        $this->assertNotEquals($gradecategoryitem->grademin, $grade->rawgrademin);

        // This is the function that we are testing. If we comment out this line, then the test fails because the grade items
        // are not flagged for regrading.
        upgrade_calculated_grade_items();
        grade_regrade_final_grades($course->id);

        $grade = grade_grade::fetch(array('itemid' => $gradecategoryitem->id, 'userid' => $user->id));

        $this->assertEquals($gradecategoryitem->grademax, $grade->rawgrademax);
        $this->assertEquals($gradecategoryitem->grademin, $grade->rawgrademin);
    }

    /**
     * Test that the upgrade script correctly flags courses to be frozen due to letter boundary problems.
     */
    public function test_upgrade_course_letter_boundary(): void {
        global $CFG, $DB;
        $this->resetAfterTest(true);

        require_once($CFG->libdir . '/db/upgradelib.php');

        // Create a user.
        $user = $this->getDataGenerator()->create_user();

        // Create some courses.
        $courses = array();
        $contexts = array();
        for ($i = 0; $i < 45; $i++) {
            $course = $this->getDataGenerator()->create_course();
            $context = context_course::instance($course->id);
            if (in_array($i, array(2, 5, 10, 13, 14, 19, 23, 25, 30, 34, 36))) {
                // Assign good letter boundaries.
                $this->assign_good_letter_boundary($context->id);
            }
            if (in_array($i, array(3, 6, 11, 15, 20, 24, 26, 31, 35))) {
                // Assign bad letter boundaries.
                $this->assign_bad_letter_boundary($context->id);
            }

            if (in_array($i, array(3, 9, 10, 11, 18, 19, 20, 29, 30, 31, 40))) {
                grade_set_setting($course->id, 'displaytype', '3');
            } else if (in_array($i, array(8, 17, 28))) {
                grade_set_setting($course->id, 'displaytype', '2');
            }

            if (in_array($i, array(37, 43))) {
                // Show.
                grade_set_setting($course->id, 'report_user_showlettergrade', '1');
            } else if (in_array($i, array(38, 42))) {
                // Hide.
                grade_set_setting($course->id, 'report_user_showlettergrade', '0');
            }

            $assignrow = $this->getDataGenerator()->create_module('assign', array('course' => $course->id, 'name' => 'Test!'));
            $gi = grade_item::fetch(
                    array('itemtype' => 'mod',
                          'itemmodule' => 'assign',
                          'iteminstance' => $assignrow->id,
                          'courseid' => $course->id));
            if (in_array($i, array(6, 13, 14, 15, 23, 24, 34, 35, 36, 41))) {
                grade_item::set_properties($gi, array('display' => 3));
                $gi->update();
            } else if (in_array($i, array(12, 21, 32))) {
                grade_item::set_properties($gi, array('display' => 2));
                $gi->update();
            }
            $gradegrade = new grade_grade();
            $gradegrade->itemid = $gi->id;
            $gradegrade->userid = $user->id;
            $gradegrade->rawgrade = 55.5563;
            $gradegrade->finalgrade = 55.5563;
            $gradegrade->rawgrademax = 100;
            $gradegrade->rawgrademin = 0;
            $gradegrade->timecreated = time();
            $gradegrade->timemodified = time();
            $gradegrade->insert();

            $contexts[] = $context;
            $courses[] = $course;
        }

        upgrade_course_letter_boundary();

        // No system setting for grade letter boundaries.
        // [0] A course with no letter boundaries.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[0]->id}));
        // [1] A course with letter boundaries which are default.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[1]->id}));
        // [2] A course with letter boundaries which are custom but not affected.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[2]->id}));
        // [3] A course with letter boundaries which are custom and will be affected.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[3]->id});
        // [4] A course with no letter boundaries, but with a grade item with letter boundaries which are default.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[4]->id}));
        // [5] A course with no letter boundaries, but with a grade item with letter boundaries which are not default, but not affected.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[5]->id}));
        // [6] A course with no letter boundaries, but with a grade item with letter boundaries which are not default which will be affected.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[6]->id});

        // System setting for grade letter boundaries (default).
        set_config('grade_displaytype', '3');
        for ($i = 0; $i < 45; $i++) {
            unset_config('gradebook_calculations_freeze_' . $courses[$i]->id);
        }
        upgrade_course_letter_boundary();

        // [7] A course with no grade display settings for the course or grade items.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[7]->id}));
        // [8] A course with grade display settings, but for something that isn't letters.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[8]->id}));
        // [9] A course with grade display settings of letters which are default.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[9]->id}));
        // [10] A course with grade display settings of letters which are not default, but not affected.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[10]->id}));
        // [11] A course with grade display settings of letters which are not default, which will be affected.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[11]->id});
        // [12] A grade item with display settings that are not letters.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[12]->id}));
        // [13] A grade item with display settings of letters which are default.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[13]->id}));
        // [14] A grade item with display settings of letters which are not default, but not affected.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[14]->id}));
        // [15] A grade item with display settings of letters which are not default, which will be affected.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[15]->id});

        // System setting for grade letter boundaries (custom with problem).
        $systemcontext = context_system::instance();
        $this->assign_bad_letter_boundary($systemcontext->id);
        for ($i = 0; $i < 45; $i++) {
            unset_config('gradebook_calculations_freeze_' . $courses[$i]->id);
        }
        upgrade_course_letter_boundary();

        // [16] A course with no grade display settings for the course or grade items.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[16]->id});
        // [17] A course with grade display settings, but for something that isn't letters.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[17]->id}));
        // [18] A course with grade display settings of letters which are default.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[18]->id});
        // [19] A course with grade display settings of letters which are not default, but not affected.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[19]->id}));
        // [20] A course with grade display settings of letters which are not default, which will be affected.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[20]->id});
        // [21] A grade item with display settings which are not letters. Grade total will be affected so should be frozen.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[21]->id});
        // [22] A grade item with display settings of letters which are default.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[22]->id});
        // [23] A grade item with display settings of letters which are not default, but not affected. Course uses new letter boundary setting.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[23]->id}));
        // [24] A grade item with display settings of letters which are not default, which will be affected.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[24]->id});
        // [25] A course which is using the default grade display setting, but has updated the grade letter boundary (not 57) Should not be frozen.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[25]->id}));
        // [26] A course that is using the default display setting (letters) and altered the letter boundary with 57. Should be frozen.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[26]->id});

        // System setting not showing letters.
        set_config('grade_displaytype', '2');
        for ($i = 0; $i < 45; $i++) {
            unset_config('gradebook_calculations_freeze_' . $courses[$i]->id);
        }
        upgrade_course_letter_boundary();

        // [27] A course with no grade display settings for the course or grade items.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[27]->id}));
        // [28] A course with grade display settings, but for something that isn't letters.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[28]->id}));
        // [29] A course with grade display settings of letters which are default.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[29]->id});
        // [30] A course with grade display settings of letters which are not default, but not affected.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[30]->id}));
        // [31] A course with grade display settings of letters which are not default, which will be affected.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[31]->id});
        // [32] A grade item with display settings which are not letters.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[32]->id}));
        // [33] All system defaults.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[33]->id}));
        // [34] A grade item with display settings of letters which are not default, but not affected. Course uses new letter boundary setting.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[34]->id}));
        // [35] A grade item with display settings of letters which are not default, which will be affected.
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[35]->id});
        // [36] A course with grade display settings of letters with modified and good boundary (not 57) Should not be frozen.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[36]->id}));

        // Previous site conditions still exist.
        for ($i = 0; $i < 45; $i++) {
            unset_config('gradebook_calculations_freeze_' . $courses[$i]->id);
        }
        upgrade_course_letter_boundary();

        // [37] Site setting for not showing the letter column and course setting set to show (frozen).
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[37]->id});
        // [38] Site setting for not showing the letter column and course setting set to hide.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[38]->id}));
        // [39] Site setting for not showing the letter column and course setting set to default.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[39]->id}));
        // [40] Site setting for not showing the letter column and course setting set to default. Course display set to letters (frozen).
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[40]->id});
        // [41] Site setting for not showing the letter column and course setting set to default. Grade item display set to letters (frozen).
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[41]->id});

        // Previous site conditions still exist.
        for ($i = 0; $i < 45; $i++) {
            unset_config('gradebook_calculations_freeze_' . $courses[$i]->id);
        }
        set_config('grade_report_user_showlettergrade', '1');
        upgrade_course_letter_boundary();

        // [42] Site setting for showing the letter column, but course setting set to hide.
        $this->assertTrue(empty($CFG->{'gradebook_calculations_freeze_' . $courses[42]->id}));
        // [43] Site setting for showing the letter column and course setting set to show (frozen).
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[43]->id});
        // [44] Site setting for showing the letter column and course setting set to default (frozen).
        $this->assertEquals(20160518, $CFG->{'gradebook_calculations_freeze_' . $courses[44]->id});
    }

    /**
     * Test upgrade_letter_boundary_needs_freeze function.
     */
    public function test_upgrade_letter_boundary_needs_freeze(): void {
        global $CFG;

        $this->resetAfterTest();

        require_once($CFG->libdir . '/db/upgradelib.php');

        $courses = array();
        $contexts = array();
        for ($i = 0; $i < 3; $i++) {
            $courses[] = $this->getDataGenerator()->create_course();
            $contexts[] = context_course::instance($courses[$i]->id);
        }

        // Course one is not using a letter boundary.
        $this->assertFalse(upgrade_letter_boundary_needs_freeze($contexts[0]));

        // Let's make course 2 use the bad boundary.
        $this->assign_bad_letter_boundary($contexts[1]->id);
        $this->assertTrue(upgrade_letter_boundary_needs_freeze($contexts[1]));
        // Course 3 has letter boundaries that are fine.
        $this->assign_good_letter_boundary($contexts[2]->id);
        $this->assertFalse(upgrade_letter_boundary_needs_freeze($contexts[2]));
        // Try the system context not using a letter boundary.
        $systemcontext = context_system::instance();
        $this->assertFalse(upgrade_letter_boundary_needs_freeze($systemcontext));
    }

    /**
     * Assigns letter boundaries with comparison problems.
     *
     * @param int $contextid Context ID.
     */
    private function assign_bad_letter_boundary($contextid) {
        global $DB;
        $newlettersscale = array(
                array('contextid' => $contextid, 'lowerboundary' => 90.00000, 'letter' => 'A'),
                array('contextid' => $contextid, 'lowerboundary' => 85.00000, 'letter' => 'A-'),
                array('contextid' => $contextid, 'lowerboundary' => 80.00000, 'letter' => 'B+'),
                array('contextid' => $contextid, 'lowerboundary' => 75.00000, 'letter' => 'B'),
                array('contextid' => $contextid, 'lowerboundary' => 70.00000, 'letter' => 'B-'),
                array('contextid' => $contextid, 'lowerboundary' => 65.00000, 'letter' => 'C+'),
                array('contextid' => $contextid, 'lowerboundary' => 57.00000, 'letter' => 'C'),
                array('contextid' => $contextid, 'lowerboundary' => 50.00000, 'letter' => 'C-'),
                array('contextid' => $contextid, 'lowerboundary' => 40.00000, 'letter' => 'D+'),
                array('contextid' => $contextid, 'lowerboundary' => 25.00000, 'letter' => 'D'),
                array('contextid' => $contextid, 'lowerboundary' => 0.00000, 'letter' => 'F'),
            );

        $DB->delete_records('grade_letters', array('contextid' => $contextid));
        foreach ($newlettersscale as $record) {
            // There is no API to do this, so we have to manually insert into the database.
            $DB->insert_record('grade_letters', $record);
        }
    }

    /**
     * Assigns letter boundaries with no comparison problems.
     *
     * @param int $contextid Context ID.
     */
    private function assign_good_letter_boundary($contextid) {
        global $DB;
        $newlettersscale = array(
                array('contextid' => $contextid, 'lowerboundary' => 90.00000, 'letter' => 'A'),
                array('contextid' => $contextid, 'lowerboundary' => 85.00000, 'letter' => 'A-'),
                array('contextid' => $contextid, 'lowerboundary' => 80.00000, 'letter' => 'B+'),
                array('contextid' => $contextid, 'lowerboundary' => 75.00000, 'letter' => 'B'),
                array('contextid' => $contextid, 'lowerboundary' => 70.00000, 'letter' => 'B-'),
                array('contextid' => $contextid, 'lowerboundary' => 65.00000, 'letter' => 'C+'),
                array('contextid' => $contextid, 'lowerboundary' => 54.00000, 'letter' => 'C'),
                array('contextid' => $contextid, 'lowerboundary' => 50.00000, 'letter' => 'C-'),
                array('contextid' => $contextid, 'lowerboundary' => 40.00000, 'letter' => 'D+'),
                array('contextid' => $contextid, 'lowerboundary' => 25.00000, 'letter' => 'D'),
                array('contextid' => $contextid, 'lowerboundary' => 0.00000, 'letter' => 'F'),
            );

        $DB->delete_records('grade_letters', array('contextid' => $contextid));
        foreach ($newlettersscale as $record) {
            // There is no API to do this, so we have to manually insert into the database.
            $DB->insert_record('grade_letters', $record);
        }
    }

    /**
     * Test libcurl custom check api.
     */
    public function test_check_libcurl_version(): void {
        $supportedversion = 0x071304;
        $curlinfo = curl_version();
        $currentversion = $curlinfo['version_number'];

        $result = new environment_results("custom_checks");
        if ($currentversion < $supportedversion) {
            $this->assertFalse(check_libcurl_version($result)->getStatus());
        } else {
            $this->assertNull(check_libcurl_version($result));
        }
    }

    /**
     * Create a collection of test themes to test determining parent themes.
     *
     * @return Url to the path containing the test themes
     */
    public function create_testthemes() {
        global $CFG;

        $themedircontent = [
            'testtheme' => [
                'config.php' => '<?php $THEME->name = "testtheme"; $THEME->parents = [""];',
            ],
            'childoftesttheme' => [
                'config.php' => '<?php $THEME->name = "childofboost"; $THEME->parents = ["testtheme"];',
            ],
            'infinite' => [
                'config.php' => '<?php $THEME->name = "infinite"; $THEME->parents = ["forever"];',
            ],
            'forever' => [
                'config.php' => '<?php $THEME->name = "forever"; $THEME->parents = ["infinite", "childoftesttheme"];',
            ],
            'orphantheme' => [
                'config.php' => '<?php $THEME->name = "orphantheme"; $THEME->parents = [];',
            ],
            'loop' => [
                'config.php' => '<?php $THEME->name = "loop"; $THEME->parents = ["around"];',
            ],
            'around' => [
                'config.php' => '<?php $THEME->name = "around"; $THEME->parents = ["loop"];',
            ],
            'themewithbrokenparent' => [
                'config.php' => '<?php $THEME->name = "orphantheme"; $THEME->parents = ["nonexistent", "testtheme"];',
            ],
        ];
        $vthemedir = \org\bovigo\vfs\vfsStream::setup('themes', null, $themedircontent);

        return \org\bovigo\vfs\vfsStream::url('themes');
    }

    /**
     * Data provider of serialized string.
     *
     * @return array
     */
    public static function serialized_strings_dataprovider(): array {
        return [
            'A configuration that uses the old object' => [
                'O:6:"object":3:{s:4:"text";s:32:"Nothing that anyone cares about.";s:5:"title";s:16:"Really old block";s:6:"format";s:1:"1";}',
                true,
                'O:8:"stdClass":3:{s:4:"text";s:32:"Nothing that anyone cares about.";s:5:"title";s:16:"Really old block";s:6:"format";s:1:"1";}'
            ],
            'A configuration that uses stdClass' => [
                'O:8:"stdClass":5:{s:5:"title";s:4:"Tags";s:12:"numberoftags";s:2:"80";s:12:"showstandard";s:1:"0";s:3:"ctx";s:3:"289";s:3:"rec";s:1:"1";}',
                false,
                'O:8:"stdClass":5:{s:5:"title";s:4:"Tags";s:12:"numberoftags";s:2:"80";s:12:"showstandard";s:1:"0";s:3:"ctx";s:3:"289";s:3:"rec";s:1:"1";}'
            ],
            'A setting I saw when importing a course with blocks from 1.9' => [
                'N;',
                false,
                'N;'
            ],
            'An object in an object' => [
                'O:6:"object":2:{s:2:"id";i:5;s:5:"other";O:6:"object":1:{s:4:"text";s:13:"something new";}}',
                true,
                'O:8:"stdClass":2:{s:2:"id";i:5;s:5:"other";O:8:"stdClass":1:{s:4:"text";s:13:"something new";}}'
            ],
            'An array with an object in it' => [
                'a:3:{s:4:"name";s:4:"Test";s:10:"additional";O:6:"object":2:{s:2:"id";i:5;s:4:"info";s:18:"text in the object";}s:4:"type";i:1;}',
                true,
                'a:3:{s:4:"name";s:4:"Test";s:10:"additional";O:8:"stdClass":2:{s:2:"id";i:5;s:4:"info";s:18:"text in the object";}s:4:"type";i:1;}'
            ]
        ];
    }

    /**
     * Test that objects in serialized strings will be changed over to stdClass.
     *
     * @dataProvider serialized_strings_dataprovider
     * @param string $initialstring The initial serialized setting.
     * @param bool $expectededited If the string is expected to be edited.
     * @param string $expectedresult The expected serialized setting to be returned.
     */
    public function test_upgrade_fix_serialized_objects($initialstring, $expectededited, $expectedresult): void {
        list($edited, $resultstring) = upgrade_fix_serialized_objects($initialstring);
        $this->assertEquals($expectededited, $edited);
        $this->assertEquals($expectedresult, $resultstring);
    }

    /**
     * Data provider for base64_encoded block instance config data.
     */
    public function encoded_strings_dataprovider() {
        return [
            'Normal data using stdClass' => [
                'Tzo4OiJzdGRDbGFzcyI6NTp7czo1OiJ0aXRsZSI7czo0OiJUYWdzIjtzOjEyOiJudW1iZXJvZnRhZ3MiO3M6MjoiODAiO3M6MTI6InNob3dzdGFuZGFyZCI7czoxOiIwIjtzOjM6ImN0eCI7czozOiIyODkiO3M6MzoicmVjIjtzOjE6IjEiO30=',
                'Tzo4OiJzdGRDbGFzcyI6NTp7czo1OiJ0aXRsZSI7czo0OiJUYWdzIjtzOjEyOiJudW1iZXJvZnRhZ3MiO3M6MjoiODAiO3M6MTI6InNob3dzdGFuZGFyZCI7czoxOiIwIjtzOjM6ImN0eCI7czozOiIyODkiO3M6MzoicmVjIjtzOjE6IjEiO30='
            ],
            'No data at all' => [
                '',
                ''
            ],
            'Old data using object' => [
                'Tzo2OiJvYmplY3QiOjM6e3M6NDoidGV4dCI7czozMjoiTm90aGluZyB0aGF0IGFueW9uZSBjYXJlcyBhYm91dC4iO3M6NToidGl0bGUiO3M6MTY6IlJlYWxseSBvbGQgYmxvY2siO3M6NjoiZm9ybWF0IjtzOjE6IjEiO30=',
                'Tzo4OiJzdGRDbGFzcyI6Mzp7czo0OiJ0ZXh0IjtzOjMyOiJOb3RoaW5nIHRoYXQgYW55b25lIGNhcmVzIGFib3V0LiI7czo1OiJ0aXRsZSI7czoxNjoiUmVhbGx5IG9sZCBibG9jayI7czo2OiJmb3JtYXQiO3M6MToiMSI7fQ=='
            ]
        ];
    }

    /**
     * Check that orphaned files are deleted.
     */
    public function test_upgrade_delete_orphaned_file_records(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/repository/lib.php');

        $this->resetAfterTest();
        // Create user.
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $this->setUser($user);
        $usercontext = context_user::instance($user->id);
        $syscontext = context_system::instance();

        $fs = get_file_storage();

        $userrepository = array();
        $newstoredfile = array();
        $repositorypluginname = array('user', 'areafiles');

        // Create two repositories with one file in each.
        foreach ($repositorypluginname as $key => $value) {
            // Override repository permission.
            $capability = 'repository/' . $value . ':view';
            $guestroleid = $DB->get_field('role', 'id', array('shortname' => 'guest'));
            assign_capability($capability, CAP_ALLOW, $guestroleid, $syscontext->id, true);

            $args = array();
            $args['type'] = $value;
            $repos = repository::get_instances($args);
            $userrepository[$key] = reset($repos);

            $this->assertInstanceOf('repository', $userrepository[$key]);

            $component = 'user';
            $filearea  = 'private';
            $itemid    = $key;
            $filepath  = '/';
            $filename  = 'userfile.txt';

            $filerecord = array(
                'contextid' => $usercontext->id,
                'component' => $component,
                'filearea'  => $filearea,
                'itemid'    => $itemid,
                'filepath'  => $filepath,
                'filename'  => $filename,
            );

            $content = 'Test content';
            $originalfile = $fs->create_file_from_string($filerecord, $content);
            $this->assertInstanceOf('stored_file', $originalfile);

            $newfilerecord = array(
                'contextid' => $syscontext->id,
                'component' => 'core',
                'filearea'  => 'phpunit',
                'itemid'    => $key,
                'filepath'  => $filepath,
                'filename'  => $filename,
            );
            $ref = $fs->pack_reference($filerecord);
            $newstoredfile[$key] = $fs->create_file_from_reference($newfilerecord, $userrepository[$key]->id, $ref);

            // Look for references by repository ID.
            $files = $fs->get_external_files($userrepository[$key]->id);
            $file = reset($files);
            $this->assertEquals($file, $newstoredfile[$key]);
        }

        // Make one file orphaned by deleting first repository.
        $DB->delete_records('repository_instances', array('id' => $userrepository[0]->id));
        $DB->delete_records('repository_instance_config', array('instanceid' => $userrepository[0]->id));

        upgrade_delete_orphaned_file_records();

        $files = $fs->get_external_files($userrepository[0]->id);
        $file = reset($files);
        $this->assertFalse($file);

        $files = $fs->get_external_files($userrepository[1]->id);
        $file = reset($files);
        $this->assertEquals($file, $newstoredfile[1]);
    }

    /**
     * Test the functionality of {@link upgrade_core_licenses} function.
     */
    public function test_upgrade_core_licenses(): void {
        global $CFG, $DB;

        $this->resetAfterTest();

        // Emulate that upgrade is in process.
        $CFG->upgraderunning = time();

        $deletedcorelicenseshortname = 'unknown';
        $DB->delete_records('license', ['shortname' => $deletedcorelicenseshortname]);

        upgrade_core_licenses();

        $expectedshortnames = ['allrightsreserved', 'cc-4.0', 'cc-nc-4.0', 'cc-nc-nd-4.0', 'cc-nc-sa-4.0', 'cc-nd-4.0', 'cc-sa-4.0', 'public'];
        $licenses = $DB->get_records('license');

        foreach ($licenses as $license) {
            $this->assertContains($license->shortname, $expectedshortnames);
            $this->assertObjectHasProperty('custom', $license);
            $this->assertObjectHasProperty('sortorder', $license);
        }
        // A core license which was deleted prior to upgrade should not be reinstalled.
        $actualshortnames = $DB->get_records_menu('license', null, '', 'id, shortname');
        $this->assertNotContains($deletedcorelicenseshortname, $actualshortnames);
    }

    /**
     * Execute same problematic query from upgrade step.
     *
     * @return bool
     */
    public function run_upgrade_step_query() {
        global $DB;

        return $DB->execute("UPDATE {event} SET userid = 0 WHERE eventtype <> 'user' OR priority <> 0");
    }

    /**
     * Test the functionality of upgrade_calendar_events_status() function.
     */
    public function test_upgrade_calendar_events_status(): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        $events = create_standard_events(5);
        $eventscount = count($events);

        // Run same DB query as the problematic upgrade step.
        $this->run_upgrade_step_query();

        // Get the events info.
        $status = upgrade_calendar_events_status(false);

        // Total events.
        $expected = [
            'total' => (object)[
                'count' => $eventscount,
                'bad' => $eventscount - 5, // Event count excluding user events.
            ],
            'standard' => (object)[
                'count' => $eventscount,
                'bad' => $eventscount - 5, // Event count excluding user events.
            ],
        ];

        $this->assertEquals($expected['standard']->count, $status['standard']->count);
        $this->assertEquals($expected['standard']->bad, $status['standard']->bad);
        $this->assertEquals($expected['total']->count, $status['total']->count);
        $this->assertEquals($expected['total']->bad, $status['total']->bad);
    }

    /**
     * Test the functionality of upgrade_calendar_events_get_teacherid() function.
     */
    public function test_upgrade_calendar_events_get_teacherid(): void {
        global $DB;

        $this->resetAfterTest();

        // Create a new course and enrol a user as editing teacher.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $teacher = $generator->create_and_enrol($course, 'editingteacher');

        // There's a teacher enrolled in the course, return its user id.
        $userid = upgrade_calendar_events_get_teacherid($course->id);

        // It should return the enrolled teacher by default.
        $this->assertEquals($teacher->id, $userid);

        // Un-enrol teacher from course.
        $instance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);
        enrol_get_plugin('manual')->unenrol_user($instance, $teacher->id);

        // Since there are no teachers enrolled in the course, fallback to admin user id.
        $admin = get_admin();
        $userid = upgrade_calendar_events_get_teacherid($course->id);
        $this->assertEquals($admin->id, $userid);
    }

    /**
     * Test the functionality of upgrade_calendar_standard_events_fix() function.
     */
    public function test_upgrade_calendar_standard_events_fix(): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        $events = create_standard_events(5);
        $eventscount = count($events);

        // Get the events info.
        $info = upgrade_calendar_events_status(false);

        // There should be no standard events to be fixed.
        $this->assertEquals(0, $info['standard']->bad);

        // No events to be fixed, should return false.
        $this->assertFalse(upgrade_calendar_standard_events_fix($info['standard'], false));

        // Run same problematic DB query.
        $this->run_upgrade_step_query();

        // Get the events info.
        $info = upgrade_calendar_events_status(false);

        // There should be 20 events to be fixed (five from each type except user).
        $this->assertEquals($eventscount - 5, $info['standard']->bad);

        // Test the function runtime, passing -1 as end time.
        // It should not be able to fix all events so fast, so some events should remain to be fixed in the next run.
        $result = upgrade_calendar_standard_events_fix($info['standard'], false, -1);
        $this->assertNotFalse($result);

        // Call the function again, this time it will run until all events have been fixed.
        $this->assertFalse(upgrade_calendar_standard_events_fix($info['standard'], false));

        // Get the events info again.
        $info = upgrade_calendar_events_status(false);

        // All standard events should have been recovered.
        // There should be no standard events flagged to be fixed.
        $this->assertEquals(0, $info['standard']->bad);
    }

    /**
     * Test the functionality of upgrade_calendar_subscription_events_fix() function.
     */
    public function test_upgrade_calendar_subscription_events_fix(): void {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/calendar/lib.php');
        require_once($CFG->dirroot . '/lib/bennu/bennu.inc.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create event subscription.
        $subscription = new stdClass;
        $subscription->name = 'Repeated events';
        $subscription->importfrom = CALENDAR_IMPORT_FROM_FILE;
        $subscription->eventtype = 'site';
        $id = calendar_add_subscription($subscription);

        // Get repeated events ICS file.
        $calendar = file_get_contents($CFG->dirroot . '/lib/tests/fixtures/repeated_events.ics');
        $ical = new iCalendar();
        $ical->unserialize($calendar);

        // Import subscription events.
        calendar_import_events_from_ical($ical, $id);

        // Subscription should have added 18 events.
        $eventscount = $DB->count_records('event');

        // Get the events info.
        $info = upgrade_calendar_events_status(false);

        // There should be no subscription events to be fixed at this point.
        $this->assertEquals(0, $info['subscription']->bad);

        // No events to be fixed, should return false.
        $this->assertFalse(upgrade_calendar_subscription_events_fix($info['subscription'], false));

        // Run same problematic DB query.
        $this->run_upgrade_step_query();

        // Get the events info and assert total number of events is correct.
        $info = upgrade_calendar_events_status(false);
        $subscriptioninfo = $info['subscription'];

        $this->assertEquals($eventscount, $subscriptioninfo->count);

        // Since we have added our subscription as site, all sub events have been affected.
        $this->assertEquals($eventscount, $subscriptioninfo->bad);

        // Test the function runtime, passing -1 as end time.
        // It should not be able to fix all events so fast, so some events should remain to be fixed in the next run.
        $result = upgrade_calendar_subscription_events_fix($subscriptioninfo, false, -1);
        $this->assertNotFalse($result);

        // Call the function again, this time it will run until all events have been fixed.
        $this->assertFalse(upgrade_calendar_subscription_events_fix($subscriptioninfo, false));

        // Get the events info again.
        $info = upgrade_calendar_events_status(false);

        // All standard events should have been recovered.
        // There should be no standard events flagged to be fixed.
        $this->assertEquals(0, $info['subscription']->bad);
    }

    /**
     * Test the functionality of upgrade_calendar_action_events_fix() function.
     */
    public function test_upgrade_calendar_action_events_fix(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a new course and a choice activity.
        $course = $this->getDataGenerator()->create_course();
        $choice = $this->getDataGenerator()->create_module('choice', ['course' => $course->id]);

        // Create some action events.
        create_action_event(['courseid' => $course->id, 'modulename' => 'choice', 'instance' => $choice->id,
            'eventtype' => CHOICE_EVENT_TYPE_OPEN]);
        create_action_event(['courseid' => $course->id, 'modulename' => 'choice', 'instance' => $choice->id,
            'eventtype' => CHOICE_EVENT_TYPE_CLOSE]);

        $eventscount = $DB->count_records('event');

        // Get the events info.
        $info = upgrade_calendar_events_status(false);
        $actioninfo = $info['action'];

        // There should be no standard events to be fixed.
        $this->assertEquals(0, $actioninfo->bad);

        // No events to be fixed, should return false.
        $this->assertFalse(upgrade_calendar_action_events_fix($actioninfo, false));

        // Run same problematic DB query.
        $this->run_upgrade_step_query();

        // Get the events info.
        $info = upgrade_calendar_events_status(false);
        $actioninfo = $info['action'];

        // There should be 2 events to be fixed.
        $this->assertEquals($eventscount, $actioninfo->bad);

        // Test the function runtime, passing -1 as end time.
        // It should not be able to fix all events so fast, so some events should remain to be fixed in the next run.
        $this->assertNotFalse(upgrade_calendar_action_events_fix($actioninfo, false, -1));

        // Call the function again, this time it will run until all events have been fixed.
        $this->assertFalse(upgrade_calendar_action_events_fix($actioninfo, false));

        // Get the events info again.
        $info = upgrade_calendar_events_status(false);

        // All standard events should have been recovered.
        // There should be no standard events flagged to be fixed.
        $this->assertEquals(0, $info['action']->bad);
    }

    /**
     * Test the user override part of upgrade_calendar_override_events_fix() function.
     */
    public function test_upgrade_calendar_user_override_events_fix(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();

        // Create a new course.
        $course = $generator->create_course();

        // Create few users and enrol as students.
        $student1 = $generator->create_and_enrol($course, 'student');
        $student2 = $generator->create_and_enrol($course, 'student');
        $student3 = $generator->create_and_enrol($course, 'student');

        // Create some activities and some override events.
        foreach (['assign', 'lesson', 'quiz'] as $modulename) {
            $instance = $generator->create_module($modulename, ['course' => $course->id]);
            create_user_override_event($modulename, $instance->id, $student1->id);
            create_user_override_event($modulename, $instance->id, $student2->id);
            create_user_override_event($modulename, $instance->id, $student3->id);
        }

        // There should be 9 override events to be fixed (three from each module).
        $eventscount = $DB->count_records('event');
        $this->assertEquals(9, $eventscount);

        // Get the events info.
        $info = upgrade_calendar_events_status(false);
        $overrideinfo = $info['override'];

        // There should be no standard events to be fixed.
        $this->assertEquals(0, $overrideinfo->bad);

        // No events to be fixed, should return false.
        $this->assertFalse(upgrade_calendar_override_events_fix($overrideinfo, false));

        // Run same problematic DB query.
        $this->run_upgrade_step_query();

        // Get the events info.
        $info = upgrade_calendar_events_status(false);
        $overrideinfo = $info['override'];

        // There should be 9 events to be fixed (three from each module).
        $this->assertEquals($eventscount, $overrideinfo->bad);

        // Call the function again, this time it will run until all events have been fixed.
        $this->assertFalse(upgrade_calendar_override_events_fix($overrideinfo, false));

        // Get the events info again.
        $info = upgrade_calendar_events_status(false);

        // All standard events should have been recovered.
        // There should be no standard events flagged to be fixed.
        $this->assertEquals(0, $info['override']->bad);
    }

    /**
     * Test the group override part of upgrade_calendar_override_events_fix() function.
     */
    public function test_upgrade_calendar_group_override_events_fix(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();

        // Create a new course and few groups.
        $course = $generator->create_course();
        $group1 = $generator->create_group(['courseid' => $course->id]);
        $group2 = $generator->create_group(['courseid' => $course->id]);
        $group3 = $generator->create_group(['courseid' => $course->id]);

        // Create some activities and some override events.
        foreach (['assign', 'lesson', 'quiz'] as $modulename) {
            $instance = $generator->create_module($modulename, ['course' => $course->id]);
            create_group_override_event($modulename, $instance->id, $course->id, $group1->id);
            create_group_override_event($modulename, $instance->id, $course->id, $group2->id);
            create_group_override_event($modulename, $instance->id, $course->id, $group3->id);
        }

        // There should be 9 override events to be fixed (three from each module).
        $eventscount = $DB->count_records('event');
        $this->assertEquals(9, $eventscount);

        // Get the events info.
        $info = upgrade_calendar_events_status(false);

        // We classify group overrides as action events since they do not record the userid.
        $groupoverrideinfo = $info['action'];

        // There should be no events to be fixed.
        $this->assertEquals(0, $groupoverrideinfo->bad);

        // No events to be fixed, should return false.
        $this->assertFalse(upgrade_calendar_action_events_fix($groupoverrideinfo, false));

        // Run same problematic DB query.
        $this->run_upgrade_step_query();

        // Get the events info.
        $info = upgrade_calendar_events_status(false);
        $this->assertEquals(9, $info['action']->bad);

        // Call the function again, this time it will run until all events have been fixed.
        $this->assertFalse(upgrade_calendar_action_events_fix($info['action'], false));

        // Since group override events do not set userid, these events should not be flagged to be fixed.
        $this->assertEquals(0, $groupoverrideinfo->bad);
    }

    /**
     * Test the admin_dir_usage check with no admin setting specified.
     */
    public function test_admin_dir_usage_not_set(): void {
        $result = new environment_results("custom_checks");

        $this->assertNull(check_admin_dir_usage($result));
    }

    /**
     * Test the admin_dir_usage check with the default admin setting specified.
     */
    public function test_admin_dir_usage_is_default(): void {
        global $CFG;

        $CFG->admin = 'admin';

        $result = new environment_results("custom_checks");
        $this->assertNull(check_admin_dir_usage($result));
    }

    /**
     * Test the admin_dir_usage check with a custom admin setting specified.
     */
    public function test_admin_dir_usage_non_standard(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $CFG->admin = 'notadmin';

        $result = new environment_results("custom_checks");
        $this->assertInstanceOf(environment_results::class, check_admin_dir_usage($result));
        $this->assertEquals('admin_dir_usage', $result->getInfo());
        $this->assertFalse($result->getStatus());
    }

    /**
     * Test the check_xmlrpc_usage check when the XML-RPC web service method is not set.
     *
     * @return void
     */
    public function test_check_xmlrpc_webservice_is_not_set(): void {
        global $CFG;

        $this->resetAfterTest();

        $result = new environment_results('custom_checks');
        $this->assertNull(check_xmlrpc_usage($result));

        $CFG->webserviceprotocols = 'rest';
        $result = new environment_results('custom_checks');
        $this->assertNull(check_xmlrpc_usage($result));
    }

    /**
     * Test the check_xmlrpc_usage check when the XML-RPC web service method is set.
     *
     * @return void
     */
    public function test_check_xmlrpc_webservice_is_set(): void {
        global $CFG;

        $this->resetAfterTest();
        $CFG->webserviceprotocols = 'xmlrpc,rest';

        $result = new environment_results('custom_checks');
        $this->assertInstanceOf(environment_results::class, check_xmlrpc_usage($result));
        $this->assertEquals('xmlrpc_webservice_usage', $result->getInfo());
        $this->assertFalse($result->getStatus());
    }

    /**
     * Test the check_mod_assignment check if mod_assignment is still used.
     *
     * @covers ::check_mod_assignment
     * @return void
     */
    public function test_check_mod_assignment_is_used(): void {
        global $CFG, $DB;

        $this->resetAfterTest();
        $result = new environment_results('custom_checks');

        if (file_exists("{$CFG->dirroot}/mod/assignment/version.php")) {
            // This is for when the test is run on sites where mod_assignment is most likely reinstalled.
            $this->assertNull(check_mod_assignment($result));
        } else {
            // This is for when the test is run on sites with mod_assignment now gone.
            $this->assertFalse($DB->get_manager()->table_exists('assignment'));
            $this->assertNull(check_mod_assignment($result));

            // Then we can simulate a scenario here where the assignment records are still present during the upgrade
            // by recreating the assignment table and adding a record to it.
            $dbman = $DB->get_manager();
            $table = new xmldb_table('assignment');
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $table->add_field('name', XMLDB_TYPE_CHAR, '255');
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $dbman->create_table($table);
            $DB->insert_record('assignment', (object)['name' => 'test_assign']);

            $this->assertNotNull(check_mod_assignment($result));
            $this->assertEquals('Assignment 2.2 is in use', $result->getInfo());
            $this->assertFalse($result->getStatus());
        }
    }

    /**
     * Test the check_oracle_usage check when the Moodle instance is not using Oracle as a database architecture.
     *
     * @covers ::check_oracle_usage
     */
    public function test_check_oracle_usage_is_not_used(): void {
        global $CFG;

        $this->resetAfterTest();
        $CFG->dbtype = 'pgsql';

        $result = new environment_results('custom_checks');
        $this->assertNull(check_oracle_usage($result));
    }

    /**
     * Test the check_oracle_usage check when the Moodle instance is using Oracle as a database architecture.
     *
     * @covers ::check_oracle_usage
     */
    public function test_check_oracle_usage_is_used(): void {
        global $CFG;

        $this->resetAfterTest();
        $CFG->dbtype = 'oci';

        $result = new environment_results('custom_checks');
        $this->assertInstanceOf(environment_results::class, check_oracle_usage($result));
        $this->assertEquals('oracle_database_usage', $result->getInfo());
        $this->assertFalse($result->getStatus());
    }

    /**
     * Data provider of usermenu items.
     *
     * @return array
     */
    public static function usermenu_items_dataprovider(): array {
        return [
            'Add new item to empty usermenu' => [
                '',
                'reports,core_reportbuilder|/reportbuilder/index.php',
                'reports,core_reportbuilder|/reportbuilder/index.php',
            ],
            'Add new item to usermenu' => [
                'profile,moodle|/user/profile.php
grades,grades|/grade/report/mygrades.php',
                'reports,core_reportbuilder|/reportbuilder/index.php',
                'profile,moodle|/user/profile.php
grades,grades|/grade/report/mygrades.php
reports,core_reportbuilder|/reportbuilder/index.php',
            ],
            'Add existing item to usermenu' => [
                'profile,moodle|/user/profile.php
reports,core_reportbuilder|/reportbuilder/index.php
calendar,core_calendar|/calendar/view.php?view=month',
                'reports,core_reportbuilder|/reportbuilder/index.php',
                'profile,moodle|/user/profile.php
reports,core_reportbuilder|/reportbuilder/index.php
calendar,core_calendar|/calendar/view.php?view=month',
            ],
        ];
    }

    /**
     * Test the functionality of the {@link upgrade_add_item_to_usermenu()} function.
     *
     * @covers ::upgrade_add_item_to_usermenu
     * @dataProvider usermenu_items_dataprovider
     */
    public function test_upgrade_add_item_to_usermenu(string $initialmenu, string $newmenuitem, string $expectedmenu): void {
        global $CFG;

        $this->resetAfterTest();
        // Set the base user menu.
        $CFG->customusermenuitems = $initialmenu;

        // Add the new item to the user menu.
        upgrade_add_item_to_usermenu($newmenuitem);
        $newcustomusermenu = $CFG->customusermenuitems;

        $this->assertEquals($expectedmenu, $newcustomusermenu);
    }

    /**
     * Test that file timestamps are corrected for copied files.
     */
    public function test_upgrade_fix_file_timestamps(): void {
        global $DB;
        $this->resetAfterTest();

        // Add 2 files for testing, one with edited old timestamps.
        $origtime = time();
        $new = [
            'contextid' => 123,
            'component' => 'mod_label',
            'filearea' => 'intro',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'file.txt',
        ];
        $old = [
            'contextid' => 321,
            'component' => 'mod_label',
            'filearea' => 'intro',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'file.txt',
        ];

        // Create the file records. This will create a directory listing with the current time.
        $fs = get_file_storage();
        $newfile = $fs->create_file_from_string($new, 'new');
        $oldfile = $fs->create_file_from_string($old, 'old');

        // Manually set the timestamps to use on files.
        $DB->set_field('files', 'timecreated', $origtime, [
            'contextid' => $newfile->get_contextid(),
            'component' => $newfile->get_component(),
            'filearea' => $newfile->get_filearea(),
            'itemid' => $newfile->get_itemid(),
        ]);
        $DB->set_field('files', 'timemodified', $origtime, [
            'contextid' => $newfile->get_contextid(),
            'component' => $newfile->get_component(),
            'filearea' => $newfile->get_filearea(),
            'itemid' => $newfile->get_itemid(),
        ]);

        $DB->set_field('files', 'timecreated', 1, ['id' => $oldfile->get_id()]);
        $DB->set_field('files', 'timemodified', 1, ['id' => $oldfile->get_id()]);

        upgrade_fix_file_timestamps();

        // Check nothing changed on the new file.
        $updatednew = $DB->get_record('files', ['id' => $newfile->get_id()]);
        $this->assertEquals($origtime, $updatednew->timecreated);
        $this->assertEquals($origtime, $updatednew->timemodified);

        // Confirm that the file with old timestamps has been fixed.
        $updatedold = $DB->get_record('files', ['id' => $oldfile->get_id()]);
        $this->assertNotEquals(1, $updatedold->timecreated);
        $this->assertNotEquals(1, $updatedold->timemodified);
        $this->assertTrue($updatedold->timecreated >= $origtime);
        $this->assertTrue($updatedold->timemodified >= $origtime);
    }

    /**
     * Test the upgrade status check alongside the outageless flags.
     *
     * @covers ::moodle_needs_upgrading
     */
    public function test_moodle_upgrade_check_outageless(): void {
        global $CFG;
        $this->resetAfterTest();
        // Get a baseline.
        $this->assertFalse(moodle_needs_upgrading());

        // First lets check a plain upgrade ready.
        $CFG->version = '';
        $this->assertTrue(moodle_needs_upgrading());

        // Now set the locking config and confirm we shouldn't upgrade.
        set_config('outagelessupgrade', true);
        $this->assertFalse(moodle_needs_upgrading());

        // Test the ignorelock flag is functioning.
        $this->assertTrue(moodle_needs_upgrading(false));
    }

    /**
     * Test the upgrade status check alongside the outageless flags.
     *
     * @covers ::upgrade_started
     */
    public function test_moodle_start_upgrade_outageless(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->assertObjectNotHasProperty('upgraderunning', $CFG);

        // Confirm that starting normally sets the upgraderunning flag.
        upgrade_started();
        $upgrade = get_config('core', 'upgraderunning');
        $this->assertTrue($upgrade > (time() - 5));

        // Confirm that the config flag doesnt affect the internal upgrade processes.
        unset($CFG->upgraderunning);
        set_config('upgraderunning', null);
        set_config('outagelessupgrade', true);
        upgrade_started();
        $upgrade = get_config('core', 'upgraderunning');
        $this->assertTrue($upgrade > (time() - 5));
    }

    /**
     * Test the upgrade timeout setter alongside the outageless flags.
     *
     * @covers ::upgrade_set_timeout
     */
    public function test_moodle_set_upgrade_timeout_outageless(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->assertObjectNotHasProperty('upgraderunning', $CFG);

        // Confirm running normally sets the timeout.
        upgrade_set_timeout(120);
        $upgrade = get_config('core', 'upgraderunning');
        $this->assertTrue($upgrade > (time() - 5));

        // Confirm that the config flag doesnt affect the internal upgrade processes.
        unset($CFG->upgraderunning);
        set_config('upgraderunning', null);
        set_config('outagelessupgrade', true);
        upgrade_set_timeout(120);
        $upgrade = get_config('core', 'upgraderunning');
        $this->assertTrue($upgrade > (time() - 5));
    }

    /**
     * Test the components of the upgrade process being run outageless.
     *
     * @covers ::moodle_needs_upgrading
     * @covers ::upgrade_started
     * @covers ::upgrade_set_timeout
     */
    public function test_upgrade_components_with_outageless(): void {
        global $CFG;
        $this->resetAfterTest();

        // We can now define the outageless constant for use in upgrade, and test the effects.
        define('CLI_UPGRADE_RUNNING', true);

        // First test the upgrade check. Even when locked via config this should return true.
        // This can happen when attempting to fix a broken upgrade, so needs to work.
        set_config('outagelessupgrade', true);
        $CFG->version = '';
        $this->assertTrue(moodle_needs_upgrading());

        // Now confirm that starting upgrade with the constant will not set the upgraderunning flag.
        set_config('upgraderunning', null);
        upgrade_started();
        $upgrade = get_config('core', 'upgraderunning');
        $this->assertFalse($upgrade);

        // The same for timeouts, it should not be set if the constant is set.
        set_config('upgraderunning', null);
        upgrade_set_timeout(120);
        $upgrade = get_config('core', 'upgraderunning');
        $this->assertFalse($upgrade);
    }

    /**
     * Data provider for {@see test_upgrade_change_binary_column_to_int()}.
     *
     * @return array[]
     */
    public static function upgrade_change_binary_column_to_int_provider(): array {
        return [
            'Binary column' => [
                XMLDB_TYPE_BINARY,
                null,
                true,
                false,
            ],
            'Integer column' => [
                XMLDB_TYPE_INTEGER,
                '1',
                false,
                false,
            ],
            'Non-binary and non-integer column' => [
                XMLDB_TYPE_TEXT,
                null,
                false,
                true,
            ],
        ];
    }

    /**
     * Unit test for {@see upgrade_change_binary_column_to_int()}.
     *
     * @dataProvider upgrade_change_binary_column_to_int_provider
     * @covers ::upgrade_change_binary_column_to_int()
     * @param int $type The field type.
     * @param string|null $length The field length.
     * @param bool $expectedresult Whether the conversion succeeded.
     * @param bool $expecexception Whether to expect an exception.
     * @return void
     */
    public function test_upgrade_change_binary_column_to_int(
        int $type,
        ?string $length,
        bool $expectedresult,
        bool $expecexception,
    ): void {
        global $DB;
        $this->resetAfterTest();

        $dbman = $DB->get_manager();
        $tmptablename = 'test_convert_table';
        $fieldname = 'success';
        $table = new xmldb_table($tmptablename);
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field($fieldname, $type, $length, null, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $dbman->create_table($table);

        // Insert sample data.
        $ones = [];
        $truerecord = (object)[$fieldname => 1];
        $falserecord = (object)[$fieldname => 0];
        $ones[] = $DB->insert_record($tmptablename, $truerecord);
        $DB->insert_record($tmptablename, $falserecord);
        $ones[] = $DB->insert_record($tmptablename, $truerecord);
        $DB->insert_record($tmptablename, $falserecord);
        $ones[] = $DB->insert_record($tmptablename, $truerecord);
        $ones[] = $DB->insert_record($tmptablename, $truerecord);

        if ($expecexception) {
            $this->expectException(coding_exception::class);
        }

        $result = upgrade_change_binary_column_to_int($tmptablename, $fieldname);
        $this->assertEquals($expectedresult, $result);

        // Verify converted column and data.
        if ($result) {
            $columns = $DB->get_columns($tmptablename);
            // Verify the new field has been created and is no longer a binary field.
            $this->assertArrayHasKey($fieldname, $columns);
            $field = $columns[$fieldname];
            $this->assertFalse($field->binary);

            // Verify that the renamed old field has now been removed.
            $this->assertArrayNotHasKey("tmp$fieldname", $columns);

            // Confirm that the values for the converted column are the same.
            $records = $DB->get_fieldset($tmptablename, 'id', [$fieldname => 1]);
            $this->assertEqualsCanonicalizing($ones, $records);
        }

        // Cleanup.
        $dbman->drop_table($table);
    }

    /**
     * Test for upgrade script replacing full urls with relative urls in defaulthomepage setting
     *
     * @covers ::upgrade_change_binary_column_to_int()
     */
    public function test_upgrade_store_relative_url_sitehomepage(): void {
        global $CFG;
        $this->resetAfterTest();

        // Check updating the value for the defaulthomepage.
        $CFG->defaulthomepage = $CFG->wwwroot . '/page1';
        upgrade_store_relative_url_sitehomepage();
        $this->assertEquals('/page1', $CFG->defaulthomepage);

        $CFG->defaulthomepage = HOMEPAGE_SITE;
        upgrade_store_relative_url_sitehomepage();
        $this->assertEquals(HOMEPAGE_SITE, $CFG->defaulthomepage);

        // Check updating user preferences.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        set_user_preference('user_home_page_preference', $CFG->wwwroot . '/page2', $user1);
        set_user_preference('user_home_page_preference', HOMEPAGE_MY, $user2);
        upgrade_store_relative_url_sitehomepage();
        $this->assertEquals('/page2', get_user_preferences('user_home_page_preference', null, $user1->id));
        $this->assertEquals(HOMEPAGE_MY, get_user_preferences('user_home_page_preference', null, $user2->id));
    }
}
