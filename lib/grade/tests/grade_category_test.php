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

require_once(__DIR__.'/fixtures/lib.php');


class core_grade_category_testcase extends grade_base_testcase {

    public function test_grade_category() {
        $this->sub_test_grade_category_construct();
        $this->sub_test_grade_category_build_path();
        $this->sub_test_grade_category_fetch();
        $this->sub_test_grade_category_fetch_all();
        $this->sub_test_grade_category_update();
        $this->sub_test_grade_category_delete();
        $this->sub_test_grade_category_insert();
        $this->sub_test_grade_category_qualifies_for_regrading();
        $this->sub_test_grade_category_force_regrading();
        $this->sub_test_grade_category_aggregate_grades();
        $this->sub_test_grade_category_apply_limit_rules();
        $this->sub_test_grade_category_is_aggregationcoef_used();
        $this->sub_test_grade_category_aggregation_uses_aggregationcoef();
        $this->sub_test_grade_category_fetch_course_tree();
        $this->sub_test_grade_category_get_children();
        $this->sub_test_grade_category_load_grade_item();
        $this->sub_test_grade_category_get_grade_item();
        $this->sub_test_grade_category_load_parent_category();
        $this->sub_test_grade_category_get_parent_category();
        $this->sub_test_grade_category_get_name();
        $this->sub_test_grade_category_set_parent();
        $this->sub_test_grade_category_get_final();
        $this->sub_test_grade_category_get_sortorder();
        $this->sub_test_grade_category_set_sortorder();
        $this->sub_test_grade_category_is_editable();
        $this->sub_test_grade_category_move_after_sortorder();
        $this->sub_test_grade_category_is_course_category();
        $this->sub_test_grade_category_fetch_course_category();
        $this->sub_test_grade_category_is_locked();
        $this->sub_test_grade_category_set_locked();
        $this->sub_test_grade_category_is_hidden();
        $this->sub_test_grade_category_set_hidden();
        $this->sub_test_grade_category_can_control_visibility();

        // This won't work until MDL-11837 is complete.
        // $this->sub_test_grade_category_generate_grades();

        // Do this last as adding a second course category messes up the data.
        $this->sub_test_grade_category_insert_course_category();
        $this->sub_test_grade_category_is_extracredit_used();
        $this->sub_test_grade_category_aggregation_uses_extracredit();
    }

    // Adds 3 new grade categories at various depths.
    protected function sub_test_grade_category_construct() {
        $course_category = grade_category::fetch_course_category($this->courseid);

        $params = new stdClass();

        $params->courseid = $this->courseid;
        $params->fullname = 'unittestcategory4';

        $grade_category = new grade_category($params, false);
        $grade_category->insert();
        $this->grade_categories[] = $grade_category;

        $this->assertEquals($params->courseid, $grade_category->courseid);
        $this->assertEquals($params->fullname, $grade_category->fullname);
        $this->assertEquals(2, $grade_category->depth);
        $this->assertEquals("/$course_category->id/$grade_category->id/", $grade_category->path);
        $parentpath = $grade_category->path;

        // Test a child category.
        $params->parent = $grade_category->id;
        $params->fullname = 'unittestcategory5';
        $grade_category = new grade_category($params, false);
        $grade_category->insert();
        $this->grade_categories[] = $grade_category;

        $this->assertEquals(3, $grade_category->depth);
        $this->assertEquals($parentpath.$grade_category->id."/", $grade_category->path);
        $parentpath = $grade_category->path;

        // Test a third depth category.
        $params->parent = $grade_category->id;
        $params->fullname = 'unittestcategory6';
        $grade_category = new grade_category($params, false);
        $grade_category->insert();
        $this->grade_categories[50] = $grade_category;// Going to delete this one later hence the special index.

        $this->assertEquals(4, $grade_category->depth);
        $this->assertEquals($parentpath.$grade_category->id."/", $grade_category->path);
    }

    protected function sub_test_grade_category_build_path() {
        $grade_category = new grade_category($this->grade_categories[1]);
        $this->assertTrue(method_exists($grade_category, 'build_path'));
        $path = grade_category::build_path($grade_category);
        $this->assertEquals($grade_category->path, $path);
    }

    protected function sub_test_grade_category_fetch() {
        $grade_category = new grade_category();
        $this->assertTrue(method_exists($grade_category, 'fetch'));

        $grade_category = grade_category::fetch(array('id'=>$this->grade_categories[0]->id));
        $this->assertEquals($this->grade_categories[0]->id, $grade_category->id);
        $this->assertEquals($this->grade_categories[0]->fullname, $grade_category->fullname);
    }

    protected function sub_test_grade_category_fetch_all() {
        $grade_category = new grade_category();
        $this->assertTrue(method_exists($grade_category, 'fetch_all'));

        $grade_categories = grade_category::fetch_all(array('courseid'=>$this->courseid));
        $this->assertEquals(count($this->grade_categories), count($grade_categories)-1);
    }

    protected function sub_test_grade_category_update() {
        global $DB;
        $grade_category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($grade_category, 'update'));

        $grade_category->fullname = 'Updated info for this unittest grade_category';
        $grade_category->path = null; // Path must be recalculated if missing.
        $grade_category->depth = null;
        $grade_category->aggregation = GRADE_AGGREGATE_MAX; // Should force regrading.

        $grade_item = $grade_category->get_grade_item();
        $this->assertEquals(0, $grade_item->needsupdate);

        $this->assertTrue($grade_category->update());

        $fullname = $DB->get_field('grade_categories', 'fullname', array('id' => $this->grade_categories[0]->id));
        $this->assertEquals($grade_category->fullname, $fullname);

        $path = $DB->get_field('grade_categories', 'path', array('id' => $this->grade_categories[0]->id));
        $this->assertEquals($grade_category->path, $path);

        $depth = $DB->get_field('grade_categories', 'depth', array('id' => $this->grade_categories[0]->id));
        $this->assertEquals($grade_category->depth, $depth);

        $grade_item = $grade_category->get_grade_item();
        $this->assertEquals(1, $grade_item->needsupdate);
    }

    protected function sub_test_grade_category_delete() {
        global $DB;

        $grade_category = new grade_category($this->grade_categories[50]);
        $this->assertTrue(method_exists($grade_category, 'delete'));

        $this->assertTrue($grade_category->delete());
        $this->assertFalse($DB->get_record('grade_categories', array('id' => $grade_category->id)));
    }

    protected function sub_test_grade_category_insert() {
        $course_category = grade_category::fetch_course_category($this->courseid);

        $grade_category = new grade_category();
        $this->assertTrue(method_exists($grade_category, 'insert'));

        $grade_category->fullname    = 'unittestcategory4';
        $grade_category->courseid    = $this->courseid;
        $grade_category->aggregation = GRADE_AGGREGATE_MEAN;
        $grade_category->aggregateonlygraded = 1;
        $grade_category->keephigh    = 100;
        $grade_category->droplow     = 10;
        $grade_category->hidden      = 0;
        $grade_category->parent      = $this->grade_categories[1]->id; // sub_test_grade_category_delete() removed the category at 0.

        $grade_category->insert();

        $this->assertEquals('/'.$course_category->id.'/'.$this->grade_categories[1]->parent.'/'.$this->grade_categories[1]->id.'/'.$grade_category->id.'/', $grade_category->path);
        $this->assertEquals(4, $grade_category->depth);

        $last_grade_category = end($this->grade_categories);

        $this->assertFalse(empty($grade_category->grade_item));
        $this->assertEquals($grade_category->id, $grade_category->grade_item->iteminstance);
        $this->assertEquals('category', $grade_category->grade_item->itemtype);

        $this->assertEquals($grade_category->id, $last_grade_category->id + 1);
        $this->assertFalse(empty($grade_category->timecreated));
        $this->assertFalse(empty($grade_category->timemodified));
    }

    protected function sub_test_grade_category_qualifies_for_regrading() {
        $grade_category = new grade_category($this->grade_categories[1]);
        $this->assertTrue(method_exists($grade_category, 'qualifies_for_regrading'));
        $this->assertFalse($grade_category->qualifies_for_regrading());

        $grade_category->aggregation = GRADE_AGGREGATE_MAX;
        $this->assertTrue($grade_category->qualifies_for_regrading());

        $grade_category = new grade_category($this->grade_categories[1]);
        $grade_category->droplow = 99;
        $this->assertTrue($grade_category->qualifies_for_regrading());

        $grade_category = new grade_category($this->grade_categories[1]);
        $grade_category->keephigh = 99;
        $this->assertTrue($grade_category->qualifies_for_regrading());
    }

    protected function sub_test_grade_category_force_regrading() {
        $grade_category = new grade_category($this->grade_categories[1]);
        $this->assertTrue(method_exists($grade_category, 'force_regrading'));

        $grade_category->load_grade_item();
        $this->assertEquals(0, $grade_category->grade_item->needsupdate);

        $grade_category->force_regrading();

        $grade_category->grade_item = null;
        $grade_category->load_grade_item();

        $this->assertEquals(1, $grade_category->grade_item->needsupdate);
    }

    /**
     * Tests the calculation of grades using the various aggregation methods with and without hidden grades
     * This will not work entirely until MDL-11837 is done
     */
    protected function sub_test_grade_category_generate_grades() {
        global $DB;

        // Inserting some special grade items to make testing the final grade calculation easier.
        $params = new stdClass();
        $params->courseid = $this->courseid;
        $params->fullname = 'unittestgradecalccategory';
        $params->aggregation = GRADE_AGGREGATE_MEAN;
        $params->aggregateonlygraded = 0;
        $grade_category = new grade_category($params, false);
        $grade_category->insert();

        $this->assertTrue(method_exists($grade_category, 'generate_grades'));

        $grade_category->load_grade_item();
        $cgi = $grade_category->get_grade_item();
        $cgi->grademin = 0;
        $cgi->grademax = 20; // 3 grade items out of 10 but category is out of 20 to force scaling to occur.
        $cgi->update();

        // 3 grade items each with a maximum grade of 10.
        $grade_items = array();
        for ($i=0; $i<3; $i++) {
            $grade_items[$i] = new grade_item();
            $grade_items[$i]->courseid = $this->courseid;
            $grade_items[$i]->categoryid = $grade_category->id;
            $grade_items[$i]->itemname = 'manual grade_item '.$i;
            $grade_items[$i]->itemtype = 'manual';
            $grade_items[$i]->itemnumber = 0;
            $grade_items[$i]->needsupdate = false;
            $grade_items[$i]->gradetype = GRADE_TYPE_VALUE;
            $grade_items[$i]->grademin = 0;
            $grade_items[$i]->grademax = 10;
            $grade_items[$i]->iteminfo = 'Manual grade item used for unit testing';
            $grade_items[$i]->timecreated = time();
            $grade_items[$i]->timemodified = time();

            // Used as the weight by weighted mean and as extra credit by mean with extra credit.
            // Will be 0, 1 and 2.
            $grade_items[$i]->aggregationcoef = $i;

            $grade_items[$i]->insert();
        }

        // A grade for each grade item.
        $grade_grades = array();
        for ($i=0; $i<3; $i++) {
            $grade_grades[$i] = new grade_grade();
            $grade_grades[$i]->itemid = $grade_items[$i]->id;
            $grade_grades[$i]->userid = $this->userid;
            $grade_grades[$i]->rawgrade = ($i+1)*2; // Produce grade grades of 2, 4 and 6.
            $grade_grades[$i]->finalgrade = ($i+1)*2;
            $grade_grades[$i]->timecreated = time();
            $grade_grades[$i]->timemodified = time();
            $grade_grades[$i]->information = '1 of 2 grade_grades';
            $grade_grades[$i]->informationformat = FORMAT_PLAIN;
            $grade_grades[$i]->feedback = 'Good, but not good enough..';
            $grade_grades[$i]->feedbackformat = FORMAT_PLAIN;

            $grade_grades[$i]->insert();
        }

        // 3 grade items with 1 grade_grade each.
        // grade grades have the values 2, 4 and 6.

        // First correct answer is the aggregate with all 3 grades.
        // Second correct answer is with the first grade (value 2) hidden.

        $this->helper_test_grade_agg_method($grade_category, $grade_items, $grade_grades, GRADE_AGGREGATE_MEDIAN, 'GRADE_AGGREGATE_MEDIAN', 8, 8);
        $this->helper_test_grade_agg_method($grade_category, $grade_items, $grade_grades, GRADE_AGGREGATE_MAX, 'GRADE_AGGREGATE_MAX', 12, 12);
        $this->helper_test_grade_agg_method($grade_category, $grade_items, $grade_grades, GRADE_AGGREGATE_MODE, 'GRADE_AGGREGATE_MODE', 12, 12);

        // Weighted mean. note grade totals are rounded to an int to prevent rounding discrepancies. correct final grade isnt actually exactly 10
        // 3 items with grades 2, 4 and 6 with weights 0, 1 and 2 and all out of 10. then doubled to be out of 20.
        $this->helper_test_grade_agg_method($grade_category, $grade_items, $grade_grades, GRADE_AGGREGATE_WEIGHTED_MEAN, 'GRADE_AGGREGATE_WEIGHTED_MEAN', 10, 10);

        // Simple weighted mean.
        // 3 items with grades 2, 4 and 6 equally weighted and all out of 10. then doubled to be out of 20.
        $this->helper_test_grade_agg_method($grade_category, $grade_items, $grade_grades, GRADE_AGGREGATE_WEIGHTED_MEAN2, 'GRADE_AGGREGATE_WEIGHTED_MEAN2', 8, 10);

        // Mean of grades with extra credit.
        // 3 items with grades 2, 4 and 6 with extra credit 0, 1 and 2 equally weighted and all out of 10. then doubled to be out of 20.
        $this->helper_test_grade_agg_method($grade_category, $grade_items, $grade_grades, GRADE_AGGREGATE_EXTRACREDIT_MEAN, 'GRADE_AGGREGATE_EXTRACREDIT_MEAN', 10, 13);

        // Aggregation tests the are affected by a hidden grade currently dont work as we dont store the altered grade in the database
        // instead an in memory recalculation is done. This should be remedied by MDL-11837.

        // Fails with 1 grade hidden. still reports 8 as being correct.
        $this->helper_test_grade_agg_method($grade_category, $grade_items, $grade_grades, GRADE_AGGREGATE_MEAN, 'GRADE_AGGREGATE_MEAN', 8, 10);

        // Fails with 1 grade hidden. still reports 4 as being correct.
        $this->helper_test_grade_agg_method($grade_category, $grade_items, $grade_grades, GRADE_AGGREGATE_MIN, 'GRADE_AGGREGATE_MIN', 4, 8);

        // Fails with 1 grade hidden. still reports 12 as being correct.
        $this->helper_test_grade_agg_method($grade_category, $grade_items, $grade_grades, GRADE_AGGREGATE_SUM, 'GRADE_AGGREGATE_SUM', 12, 10);
    }

    /**
     * Test grade category aggregation using the supplied grade objects and aggregation method
     * @param grade_category $grade_category the category to be tested
     * @param array $grade_items array of instance of grade_item
     * @param array $grade_grades array of instances of grade_grade
     * @param int $aggmethod the aggregation method to apply ie GRADE_AGGREGATE_MEAN
     * @param string $aggmethodname the name of the aggregation method to apply. Used to display any test failure messages
     * @param int $correct1 the correct final grade for the category with NO items hidden
     * @param int $correct2 the correct final grade for the category with the grade at $grade_grades[0] hidden
     * @return void
     */
    protected function helper_test_grade_agg_method($grade_category, $grade_items, $grade_grades, $aggmethod, $aggmethodname, $correct1, $correct2) {
        $grade_category->aggregation = $aggmethod;
        $grade_category->update();

        // Check grade_item isnt hidden from a previous test.
        $grade_items[0]->set_hidden(0, true);
        $this->helper_test_grade_aggregation_result($grade_category, $correct1, 'Testing aggregation method('.$aggmethodname.') with no items hidden %s');

        // Hide the grade item with grade of 2.
        $grade_items[0]->set_hidden(1, true);
        $this->helper_test_grade_aggregation_result($grade_category, $correct2, 'Testing aggregation method('.$aggmethodname.') with 1 item hidden %s');
    }

    /**
     * Verify the value of the category grade item for $this->userid
     * @param grade_category $grade_category the category to be tested
     * @param int $correctgrade the expected grade
     * @param string msg The message that should be displayed if the correct grade is not found
     * @return void
     */
    protected function helper_test_grade_aggregation_result($grade_category, $correctgrade, $msg) {
        global $DB;

        $category_grade_item = $grade_category->get_grade_item();

        // This creates all the grade_grades we need.
        grade_regrade_final_grades($this->courseid);

        $grade = $DB->get_record('grade_grades', array('itemid'=>$category_grade_item->id, 'userid'=>$this->userid));
        $this->assertWithinMargin($grade->rawgrade, $grade->rawgrademin, $grade->rawgrademax);
        $this->assertEquals(intval($correctgrade), intval($grade->finalgrade), $msg);

        /*
         * TODO this doesnt work as the grade_grades created by $grade_category->generate_grades(); dont
         * observe the category's max grade
        // delete the grade_grades for the category itself and check they get recreated correctly.
        $DB->delete_records('grade_grades', array('itemid'=>$category_grade_item->id));
        $grade_category->generate_grades();

        $grade = $DB->get_record('grade_grades', array('itemid'=>$category_grade_item->id, 'userid'=>$this->userid));
        $this->assertWithinMargin($grade->rawgrade, $grade->rawgrademin, $grade->rawgrademax);
        $this->assertEquals(intval($correctgrade), intval($grade->finalgrade), $msg);
         *
         */
    }

    protected function sub_test_grade_category_aggregate_grades() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'aggregate_grades'));
        // Tested more fully via test_grade_category_generate_grades().
    }

    protected function sub_test_grade_category_apply_limit_rules() {
        $items[$this->grade_items[0]->id] = new grade_item($this->grade_items[0], false);
        $items[$this->grade_items[1]->id] = new grade_item($this->grade_items[1], false);
        $items[$this->grade_items[2]->id] = new grade_item($this->grade_items[2], false);
        $items[$this->grade_items[4]->id] = new grade_item($this->grade_items[4], false);

        // Test excluding the lowest 2 out of 4 grades from aggregation with no 0 grades.
        $category = new grade_category();
        $category->droplow = 2;
        $grades = array($this->grade_items[0]->id=>5.374,
                        $this->grade_items[1]->id=>9.4743,
                        $this->grade_items[2]->id=>2.5474,
                        $this->grade_items[4]->id=>7.3754);
        $category->apply_limit_rules($grades, $items);
        $this->assertEquals(count($grades), 2);
        $this->assertEquals($grades[$this->grade_items[1]->id], 9.4743);
        $this->assertEquals($grades[$this->grade_items[4]->id], 7.3754);

        // Test aggregating only the highest 1 out of 4 grades.
        $category = new grade_category();
        $category->keephigh = 1;
        $category->droplow = 0;
        $grades = array($this->grade_items[0]->id=>5.374,
                        $this->grade_items[1]->id=>9.4743,
                        $this->grade_items[2]->id=>2.5474,
                        $this->grade_items[4]->id=>7.3754);
        $category->apply_limit_rules($grades, $items);
        $this->assertEquals(count($grades), 1);
        $grade = reset($grades);
        $this->assertEquals(9.4743, $grade);

        // Test excluding the lowest 2 out of 4 grades from aggregation with no 0 grades.
        // An extra credit grade item should be kept even if droplow means it would otherwise be excluded.
        $category = new grade_category();
        $category->droplow     = 2;
        $category->aggregation = GRADE_AGGREGATE_SUM;
        $items[$this->grade_items[2]->id]->aggregationcoef = 1; // Mark grade item 2 as "extra credit".
        $grades = array($this->grade_items[0]->id=>5.374,
                        $this->grade_items[1]->id=>9.4743,
                        $this->grade_items[2]->id=>2.5474,
                        $this->grade_items[4]->id=>7.3754);
        $category->apply_limit_rules($grades, $items);
        $this->assertEquals(count($grades), 2);
        $this->assertEquals($grades[$this->grade_items[1]->id], 9.4743);
        $this->assertEquals($grades[$this->grade_items[2]->id], 2.5474);

        // Test only aggregating the highest 1 out of 4 grades.
        // An extra credit grade item is retained in addition to the highest grade.
        $category = new grade_category();
        $category->keephigh = 1;
        $category->droplow = 0;
        $category->aggregation = GRADE_AGGREGATE_SUM;
        $items[$this->grade_items[2]->id]->aggregationcoef = 1; // Mark grade item 2 as "extra credit".
        $grades = array($this->grade_items[0]->id=>5.374,
                        $this->grade_items[1]->id=>9.4743,
                        $this->grade_items[2]->id=>2.5474,
                        $this->grade_items[4]->id=>7.3754);
        $category->apply_limit_rules($grades, $items);
        $this->assertEquals(count($grades), 2);
        $this->assertEquals($grades[$this->grade_items[1]->id], 9.4743);
        $this->assertEquals($grades[$this->grade_items[2]->id], 2.5474);

        // Test excluding the lowest 1 out of 4 grades from aggregation with two 0 grades.
        $items[$this->grade_items[2]->id]->aggregationcoef = 0; // Undo marking grade item 2 as "extra credit".
        $category = new grade_category();
        $category->droplow     = 1;
        $category->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN2; // Simple weighted mean.
        $grades = array($this->grade_items[0]->id=>0, // 0 out of 110. Should be excluded from aggregation.
                        $this->grade_items[1]->id=>5, // 5 out of 100.
                        $this->grade_items[2]->id=>2, // 0 out of 6.
                        $this->grade_items[4]->id=>0); // 0 out of 100.
        $category->apply_limit_rules($grades, $items);
        $this->assertEquals(count($grades), 3);
        $this->assertEquals($grades[$this->grade_items[1]->id], 5);
        $this->assertEquals($grades[$this->grade_items[2]->id], 2);
        $this->assertEquals($grades[$this->grade_items[4]->id], 0);

        // Test excluding the lowest 2 out of 4 grades from aggregation with three 0 grades.
        $category = new grade_category();
        $category->droplow     = 2;
        $category->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN2; // Simple weighted mean.
        $grades = array($this->grade_items[0]->id=>0, // 0 out of 110. Should be excluded from aggregation.
                        $this->grade_items[1]->id=>5, // 5 out of 100.
                        $this->grade_items[2]->id=>0, // 0 out of 6.
                        $this->grade_items[4]->id=>0); // 0 out of 100. Should be excluded from aggregation.
        $category->apply_limit_rules($grades, $items);
        $this->assertEquals(count($grades), 2);
        $this->assertEquals($grades[$this->grade_items[1]->id], 5);
        $this->assertEquals($grades[$this->grade_items[2]->id], 0);

        // Test excluding the lowest 5 out of 4 grades from aggregation.
        // Just to check we handle this sensibly.
        $category = new grade_category();
        $category->droplow     = 5;
        $category->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN2; // Simple weighted mean.
        $grades = array($this->grade_items[0]->id=>0, // 0 out of 110. Should be excluded from aggregation.
                        $this->grade_items[1]->id=>5, // 5 out of 100.
                        $this->grade_items[2]->id=>6, // 6 out of 6.
                        $this->grade_items[4]->id=>1);// 1 out of 100. Should be excluded from aggregation.
        $category->apply_limit_rules($grades, $items);
        $this->assertEquals(count($grades), 0);

        // Test excluding the lowest 4 out of 4 grades from aggregation with one marked as extra credit.
        $category = new grade_category();
        $category->droplow     = 4;
        $category->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN2; // Simple weighted mean.
        $items[$this->grade_items[2]->id]->aggregationcoef = 1; // Mark grade item 2 as "extra credit".
        $grades = array($this->grade_items[0]->id=>0, // 0 out of 110. Should be excluded from aggregation.
                        $this->grade_items[1]->id=>5, // 5 out of 100. Should be excluded from aggregation.
                        $this->grade_items[2]->id=>6, // 6 out of 6. Extra credit. Should be retained.
                        $this->grade_items[4]->id=>1);// 1 out of 100. Should be excluded from aggregation.
        $category->apply_limit_rules($grades, $items);
        $this->assertEquals(count($grades), 1);
        $this->assertEquals($grades[$this->grade_items[2]->id], 6);

        // MDL-35667 - There was an infinite loop if several items had the same grade and at least one was extra credit.
        $category = new grade_category();
        $category->droplow     = 1;
        $category->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN2; // Simple weighted mean.
        $items[$this->grade_items[1]->id]->aggregationcoef = 1; // Mark grade item 1 as "extra credit".
        $grades = array($this->grade_items[0]->id=>1, // 1 out of 110. Should be excluded from aggregation.
                        $this->grade_items[1]->id=>1, // 1 out of 100. Extra credit. Should be retained.
                        $this->grade_items[2]->id=>1, // 1 out of 6. Should be retained.
                        $this->grade_items[4]->id=>1);// 1 out of 100. Should be retained.
        $category->apply_limit_rules($grades, $items);
        $this->assertEquals(count($grades), 3);
        $this->assertEquals($grades[$this->grade_items[1]->id], 1);
        $this->assertEquals($grades[$this->grade_items[2]->id], 1);
        $this->assertEquals($grades[$this->grade_items[4]->id], 1);

    }

    protected function sub_test_grade_category_is_aggregationcoef_used() {
        $category = new grade_category();
        // Following use aggregationcoef.
        $category->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN;
        $this->assertTrue($category->is_aggregationcoef_used());
        $category->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN2;
        $this->assertTrue($category->is_aggregationcoef_used());
        $category->aggregation = GRADE_AGGREGATE_EXTRACREDIT_MEAN;
        $this->assertTrue($category->is_aggregationcoef_used());
        $category->aggregation = GRADE_AGGREGATE_SUM;
        $this->assertTrue($category->is_aggregationcoef_used());

        // Following don't use aggregationcoef.
        $category->aggregation = GRADE_AGGREGATE_MAX;
        $this->assertFalse($category->is_aggregationcoef_used());
        $category->aggregation = GRADE_AGGREGATE_MEAN;
        $this->assertFalse($category->is_aggregationcoef_used());
        $category->aggregation = GRADE_AGGREGATE_MEDIAN;
        $this->assertFalse($category->is_aggregationcoef_used());
        $category->aggregation = GRADE_AGGREGATE_MIN;
        $this->assertFalse($category->is_aggregationcoef_used());
        $category->aggregation = GRADE_AGGREGATE_MODE;
        $this->assertFalse($category->is_aggregationcoef_used());
    }

    protected function sub_test_grade_category_aggregation_uses_aggregationcoef() {

        $this->assertTrue(grade_category::aggregation_uses_aggregationcoef(GRADE_AGGREGATE_WEIGHTED_MEAN));
        $this->assertTrue(grade_category::aggregation_uses_aggregationcoef(GRADE_AGGREGATE_WEIGHTED_MEAN2));
        $this->assertTrue(grade_category::aggregation_uses_aggregationcoef(GRADE_AGGREGATE_EXTRACREDIT_MEAN));
        $this->assertTrue(grade_category::aggregation_uses_aggregationcoef(GRADE_AGGREGATE_SUM));

        $this->assertFalse(grade_category::aggregation_uses_aggregationcoef(GRADE_AGGREGATE_MAX));
        $this->assertFalse(grade_category::aggregation_uses_aggregationcoef(GRADE_AGGREGATE_MEAN));
        $this->assertFalse(grade_category::aggregation_uses_aggregationcoef(GRADE_AGGREGATE_MEDIAN));
        $this->assertFalse(grade_category::aggregation_uses_aggregationcoef(GRADE_AGGREGATE_MIN));
        $this->assertFalse(grade_category::aggregation_uses_aggregationcoef(GRADE_AGGREGATE_MODE));
    }

    protected function sub_test_grade_category_fetch_course_tree() {
        $category = new grade_category();
        $this->assertTrue(method_exists($category, 'fetch_course_tree'));
        // TODO: add some tests.
    }

    protected function sub_test_grade_category_get_children() {
        $course_category = grade_category::fetch_course_category($this->courseid);

        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'get_children'));

        $children_array = $category->get_children(0);

        $this->assertTrue(is_array($children_array));
        $this->assertFalse(empty($children_array[2]));
        $this->assertFalse(empty($children_array[2]['object']));
        $this->assertFalse(empty($children_array[2]['children']));
        $this->assertEquals($this->grade_categories[1]->id, $children_array[2]['object']->id);
        $this->assertEquals($this->grade_categories[2]->id, $children_array[5]['object']->id);
        $this->assertEquals($this->grade_items[0]->id, $children_array[2]['children'][3]['object']->id);
        $this->assertEquals($this->grade_items[1]->id, $children_array[2]['children'][4]['object']->id);
        $this->assertEquals($this->grade_items[2]->id, $children_array[5]['children'][6]['object']->id);
    }

    protected function sub_test_grade_category_load_grade_item() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'load_grade_item'));
        $this->assertEquals(null, $category->grade_item);
        $category->load_grade_item();
        $this->assertEquals($this->grade_items[3]->id, $category->grade_item->id);
    }

    protected function sub_test_grade_category_get_grade_item() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'get_grade_item'));
        $grade_item = $category->get_grade_item();
        $this->assertEquals($this->grade_items[3]->id, $grade_item->id);
    }

    protected function sub_test_grade_category_load_parent_category() {
        $category = new grade_category($this->grade_categories[1]);
        $this->assertTrue(method_exists($category, 'load_parent_category'));
        $this->assertEquals(null, $category->parent_category);
        $category->load_parent_category();
        $this->assertEquals($this->grade_categories[0]->id, $category->parent_category->id);
    }

    protected function sub_test_grade_category_get_parent_category() {
        $category = new grade_category($this->grade_categories[1]);
        $this->assertTrue(method_exists($category, 'get_parent_category'));
        $parent_category = $category->get_parent_category();
        $this->assertEquals($this->grade_categories[0]->id, $parent_category->id);
    }

    protected function sub_test_grade_category_get_name() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'get_name'));
        $this->assertEquals($this->grade_categories[0]->fullname, $category->get_name());
    }

    protected function sub_test_grade_category_set_parent() {
        $category = new grade_category($this->grade_categories[1]);
        $this->assertTrue(method_exists($category, 'set_parent'));
        // TODO: implement detailed tests.

        $course_category = grade_category::fetch_course_category($this->courseid);
        $this->assertTrue($category->set_parent($course_category->id));
        $this->assertEquals($course_category->id, $category->parent);
    }

    protected function sub_test_grade_category_get_final() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'get_final'));
        $category->load_grade_item();
        $this->assertEquals($category->get_final(), $category->grade_item->get_final());
    }

    protected function sub_test_grade_category_get_sortorder() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'get_sortorder'));
        $category->load_grade_item();
        $this->assertEquals($category->get_sortorder(), $category->grade_item->get_sortorder());
    }

    protected function sub_test_grade_category_set_sortorder() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'set_sortorder'));
        $category->load_grade_item();
        $this->assertEquals($category->set_sortorder(10), $category->grade_item->set_sortorder(10));
    }

    protected function sub_test_grade_category_move_after_sortorder() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'move_after_sortorder'));
        $category->load_grade_item();
        $this->assertEquals($category->move_after_sortorder(10), $category->grade_item->move_after_sortorder(10));
    }

    protected function sub_test_grade_category_is_course_category() {
        $category = grade_category::fetch_course_category($this->courseid);
        $this->assertTrue(method_exists($category, 'is_course_category'));
        $this->assertTrue($category->is_course_category());
    }

    protected function sub_test_grade_category_fetch_course_category() {
        $category = new grade_category();
        $this->assertTrue(method_exists($category, 'fetch_course_category'));
        $category = grade_category::fetch_course_category($this->courseid);
        $this->assertTrue(empty($category->parent));
    }
    /**
     * TODO implement
     */
    protected function sub_test_grade_category_is_editable() {

    }

    protected function sub_test_grade_category_is_locked() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'is_locked'));
        $category->load_grade_item();
        $this->assertEquals($category->is_locked(), $category->grade_item->is_locked());
    }

    protected function sub_test_grade_category_set_locked() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'set_locked'));

        // Will return false as cannot lock a grade that needs updating.
        $this->assertFalse($category->set_locked(1));
        grade_regrade_final_grades($this->courseid);

        // Get the category from the db again.
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue($category->set_locked(1));
    }

    protected function sub_test_grade_category_is_hidden() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'is_hidden'));
        $category->load_grade_item();
        $this->assertEquals($category->is_hidden(), $category->grade_item->is_hidden());
    }

    protected function sub_test_grade_category_set_hidden() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue(method_exists($category, 'set_hidden'));
        $category->set_hidden(1);
        $category->load_grade_item();
        $this->assertEquals(true, $category->grade_item->is_hidden());
    }

    protected function sub_test_grade_category_can_control_visibility() {
        $category = new grade_category($this->grade_categories[0]);
        $this->assertTrue($category->can_control_visibility());
    }

    protected function sub_test_grade_category_insert_course_category() {
        // Beware: adding a duplicate course category messes up the data in a way that's hard to recover from.
        $grade_category = new grade_category();
        $this->assertTrue(method_exists($grade_category, 'insert_course_category'));

        $id = $grade_category->insert_course_category($this->courseid);
        $this->assertNotNull($id);
        $this->assertEquals('?', $grade_category->fullname);
        $this->assertEquals(GRADE_AGGREGATE_WEIGHTED_MEAN2, $grade_category->aggregation);
        $this->assertEquals("/$id/", $grade_category->path);
        $this->assertEquals(1, $grade_category->depth);
        $this->assertNull($grade_category->parent);
    }

    protected function generate_random_raw_grade($item, $userid) {
        $grade = new grade_grade();
        $grade->itemid = $item->id;
        $grade->userid = $userid;
        $grade->grademin = 0;
        $grade->grademax = 1;
        $valuetype = "grade$item->gradetype";
        $grade->rawgrade = rand(0, 1000) / 1000;
        $grade->insert();
        return $grade->rawgrade;
    }

    protected function sub_test_grade_category_is_extracredit_used() {
        $category = new grade_category();
        // Following use aggregationcoef.
        $category->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN2;
        $this->assertTrue($category->is_extracredit_used());
        $category->aggregation = GRADE_AGGREGATE_EXTRACREDIT_MEAN;
        $this->assertTrue($category->is_extracredit_used());
        $category->aggregation = GRADE_AGGREGATE_SUM;
        $this->assertTrue($category->is_extracredit_used());

        // Following don't use aggregationcoef.
        $category->aggregation = GRADE_AGGREGATE_WEIGHTED_MEAN;
        $this->assertFalse($category->is_extracredit_used());
        $category->aggregation = GRADE_AGGREGATE_MAX;
        $this->assertFalse($category->is_extracredit_used());
        $category->aggregation = GRADE_AGGREGATE_MEAN;
        $this->assertFalse($category->is_extracredit_used());
        $category->aggregation = GRADE_AGGREGATE_MEDIAN;
        $this->assertFalse($category->is_extracredit_used());
        $category->aggregation = GRADE_AGGREGATE_MIN;
        $this->assertFalse($category->is_extracredit_used());
        $category->aggregation = GRADE_AGGREGATE_MODE;
        $this->assertFalse($category->is_extracredit_used());
    }

    protected function sub_test_grade_category_aggregation_uses_extracredit() {

        $this->assertTrue(grade_category::aggregation_uses_extracredit(GRADE_AGGREGATE_WEIGHTED_MEAN2));
        $this->assertTrue(grade_category::aggregation_uses_extracredit(GRADE_AGGREGATE_EXTRACREDIT_MEAN));
        $this->assertTrue(grade_category::aggregation_uses_extracredit(GRADE_AGGREGATE_SUM));

        $this->assertFalse(grade_category::aggregation_uses_extracredit(GRADE_AGGREGATE_WEIGHTED_MEAN));
        $this->assertFalse(grade_category::aggregation_uses_extracredit(GRADE_AGGREGATE_MAX));
        $this->assertFalse(grade_category::aggregation_uses_extracredit(GRADE_AGGREGATE_MEAN));
        $this->assertFalse(grade_category::aggregation_uses_extracredit(GRADE_AGGREGATE_MEDIAN));
        $this->assertFalse(grade_category::aggregation_uses_extracredit(GRADE_AGGREGATE_MIN));
        $this->assertFalse(grade_category::aggregation_uses_extracredit(GRADE_AGGREGATE_MODE));
    }
}
