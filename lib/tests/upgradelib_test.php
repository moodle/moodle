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

/**
 * Tests various classes and functions in upgradelib.php library.
 */
class core_upgradelib_testcase extends advanced_testcase {

    /**
     * Test the {@link upgrade_stale_php_files_present() function
     */
    public function test_upgrade_stale_php_files_present() {
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

    public function test_upgrade_extra_credit_weightoverride() {
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
    public function test_upgrade_calculated_grade_items_freeze() {
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

    function test_upgrade_calculated_grade_items_regrade() {
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
    public function test_upgrade_course_letter_boundary() {
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
    public function test_upgrade_letter_boundary_needs_freeze() {
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
    public function test_check_libcurl_version() {
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
     * Create two pages with blocks, delete one page and make sure upgrade script deletes orphaned blocks
     */
    public function test_delete_block_positions() {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/my/lib.php');
        $this->resetAfterTest();

        // Make sure each block on system dashboard page has a position.
        $systempage = $DB->get_record('my_pages', array('userid' => null, 'private' => MY_PAGE_PRIVATE));
        $systemcontext = context_system::instance();
        $blockinstances = $DB->get_records('block_instances', array('parentcontextid' => $systemcontext->id,
            'pagetypepattern' => 'my-index', 'subpagepattern' => $systempage->id));
        $this->assertNotEmpty($blockinstances);
        foreach ($blockinstances as $bi) {
            $DB->insert_record('block_positions', ['subpage' => $systempage->id, 'pagetype' => 'my-index', 'contextid' => $systemcontext->id,
                'blockinstanceid' => $bi->id, 'visible' => 1, 'weight' => $bi->defaultweight]);
        }

        // Create two users and make two copies of the system dashboard.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $page1 = my_copy_page($user1->id, MY_PAGE_PRIVATE, 'my-index');
        $page2 = my_copy_page($user2->id, MY_PAGE_PRIVATE, 'my-index');

        $context1 = context_user::instance($user1->id);
        $context2 = context_user::instance($user2->id);

        // Delete second page without deleting block positions.
        $DB->delete_records('my_pages', ['id' => $page2->id]);

        // Blocks are still here.
        $this->assertEquals(count($blockinstances), $DB->count_records('block_positions', ['subpage' => $page1->id, 'pagetype' => 'my-index', 'contextid' => $context1->id]));
        $this->assertEquals(count($blockinstances), $DB->count_records('block_positions', ['subpage' => $page2->id, 'pagetype' => 'my-index', 'contextid' => $context2->id]));

        // Run upgrade script that should delete orphaned block_positions.
        upgrade_block_positions();

        // First user still has all his block_positions, second user does not.
        $this->assertEquals(count($blockinstances), $DB->count_records('block_positions', ['subpage' => $page1->id, 'pagetype' => 'my-index', 'contextid' => $context1->id]));
        $this->assertEquals(0, $DB->count_records('block_positions', ['subpage' => $page2->id, 'pagetype' => 'my-index']));
    }

    /**
     * Test the conversion of auth plugin settings names.
     */
    public function test_upgrade_fix_config_auth_plugin_names() {
        $this->resetAfterTest();

        // Let the plugin auth_foo use legacy format only.
        set_config('name1', 'val1', 'auth/foo');
        set_config('name2', 'val2', 'auth/foo');

        // Let the plugin auth_bar use new format only.
        set_config('name1', 'val1', 'auth_bar');
        set_config('name2', 'val2', 'auth_bar');

        // Let the plugin auth_baz use a mix of legacy and new format, with no conflicts.
        set_config('name1', 'val1', 'auth_baz');
        set_config('name1', 'val1', 'auth/baz');
        set_config('name2', 'val2', 'auth/baz');
        set_config('name3', 'val3', 'auth_baz');

        // Let the plugin auth_qux use a mix of legacy and new format, with conflicts.
        set_config('name1', 'val1', 'auth_qux');
        set_config('name1', 'val2', 'auth/qux');

        // Execute the migration.
        upgrade_fix_config_auth_plugin_names('foo');
        upgrade_fix_config_auth_plugin_names('bar');
        upgrade_fix_config_auth_plugin_names('baz');
        upgrade_fix_config_auth_plugin_names('qux');

        // Assert that legacy settings are gone and no new were introduced.
        $this->assertEmpty((array) get_config('auth/foo'));
        $this->assertEmpty((array) get_config('auth/bar'));
        $this->assertEmpty((array) get_config('auth/baz'));
        $this->assertEmpty((array) get_config('auth/qux'));

        // Assert values were simply kept where there was no conflict.
        $this->assertSame('val1', get_config('auth_foo', 'name1'));
        $this->assertSame('val2', get_config('auth_foo', 'name2'));

        $this->assertSame('val1', get_config('auth_bar', 'name1'));
        $this->assertSame('val2', get_config('auth_bar', 'name2'));

        $this->assertSame('val1', get_config('auth_baz', 'name1'));
        $this->assertSame('val2', get_config('auth_baz', 'name2'));
        $this->assertSame('val3', get_config('auth_baz', 'name3'));

        // Assert the new format took precedence in case of conflict.
        $this->assertSame('val1', get_config('auth_qux', 'name1'));
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
     * Test finding theme locations.
     */
    public function test_upgrade_find_theme_location() {
        global $CFG;

        $this->resetAfterTest();

        $CFG->themedir = $this->create_testthemes();

        $this->assertSame($CFG->dirroot . '/theme/boost', upgrade_find_theme_location('boost'));
        $this->assertSame($CFG->dirroot . '/theme/clean', upgrade_find_theme_location('clean'));
        $this->assertSame($CFG->dirroot . '/theme/bootstrapbase', upgrade_find_theme_location('bootstrapbase'));

        $this->assertSame($CFG->themedir . '/testtheme', upgrade_find_theme_location('testtheme'));
        $this->assertSame($CFG->themedir . '/childoftesttheme', upgrade_find_theme_location('childoftesttheme'));
    }

    /**
     * Test figuring out if theme is or is a child of a certain theme.
     */
    public function test_upgrade_theme_is_from_family() {
        global $CFG;

        $this->resetAfterTest();

        $CFG->themedir = $this->create_testthemes();

        $this->assertTrue(upgrade_theme_is_from_family('boost', 'boost'), 'Boost is a boost theme');
        $this->assertTrue(upgrade_theme_is_from_family('bootstrapbase', 'clean'), 'Clean is a bootstrap base theme');
        $this->assertFalse(upgrade_theme_is_from_family('boost', 'clean'), 'Clean is not a boost theme');

        $this->assertTrue(upgrade_theme_is_from_family('testtheme', 'childoftesttheme'), 'childoftesttheme is a testtheme');
        $this->assertFalse(upgrade_theme_is_from_family('testtheme', 'orphantheme'), 'ofphantheme is not a testtheme');
        $this->assertTrue(upgrade_theme_is_from_family('testtheme', 'infinite'), 'Infinite loop with testtheme parent is true');
        $this->assertFalse(upgrade_theme_is_from_family('testtheme', 'loop'), 'Infinite loop without testtheme parent is false');
        $this->assertTrue(upgrade_theme_is_from_family('testtheme', 'themewithbrokenparent'), 'No error on broken parent');
    }

    /**
     * Data provider of serialized string.
     *
     * @return array
     */
    public function serialized_strings_dataprovider() {
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
    public function test_upgrade_fix_serialized_objects($initialstring, $expectededited, $expectedresult) {
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
     * Check that entries in the block_instances table are coverted over correctly.
     *
     * @dataProvider encoded_strings_dataprovider
     * @param string $original The original base64_encoded block config setting.
     * @param string $expected The expected base64_encoded block config setting.
     */
    public function test_upgrade_fix_block_instance_configuration($original, $expected) {
        global $DB;

        $this->resetAfterTest();

        $data = new stdClass();
        $data->blockname = 'html';
        $data->parentcontextid = 1;
        $data->showinsubcontexts = 0;
        $data->requirebytheme = 0;
        $data->pagetypepattern = 'admin-setting-frontpagesettings';
        $data->defaultregion = 'side-post';
        $data->defaultweight = 1;
        $data->timecreated = time();
        $data->timemodified = time();

        $data->configdata = $original;
        $entryid = $DB->insert_record('block_instances', $data);
        upgrade_fix_block_instance_configuration();
        $record = $DB->get_record('block_instances', ['id' => $entryid]);
        $this->assertEquals($expected, $record->configdata);
    }

    /**
     * Check that orphaned files are deleted.
     */
    public function test_upgrade_delete_orphaned_file_records() {
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
}
