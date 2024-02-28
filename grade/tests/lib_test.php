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
 * Unit tests for grade/lib.php.
 *
 * @package   core_grades
 * @category  test
 * @copyright 2016 Jun Pataleta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
namespace core_grades;

use assign;
use cm_info;
use grade_item;
use grade_plugin_return;
use grade_report_grader;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/grade/lib.php');

/**
 * Unit tests for grade/lib.php.
 *
 * @package   core_grades
 * @category  test
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends \advanced_testcase {

    /**
     * Test can_output_item.
     */
    public function test_can_output_item() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Course level grade category.
        $course = $generator->create_course();
        // Grade tree looks something like:
        // - Test course    (Rendered).
        $gradetree = \grade_category::fetch_course_tree($course->id);
        $this->assertTrue(\grade_tree::can_output_item($gradetree));

        // Add a grade category with default settings.
        $generator->create_grade_category(array('courseid' => $course->id));
        // Grade tree now looks something like:
        // - Test course n        (Rendered).
        // -- Grade category n    (Rendered).
        $gradetree = \grade_category::fetch_course_tree($course->id);
        $this->assertNotEmpty($gradetree['children']);
        foreach ($gradetree['children'] as $child) {
            $this->assertTrue(\grade_tree::can_output_item($child));
        }

        // Add a grade category with grade type = None.
        $nototalcategory = 'No total category';
        $nototalparams = [
            'courseid' => $course->id,
            'fullname' => $nototalcategory,
            'aggregation' => GRADE_AGGREGATE_WEIGHTED_MEAN
        ];
        $nototal = $generator->create_grade_category($nototalparams);
        $catnototal = \grade_category::fetch(array('id' => $nototal->id));
        // Set the grade type of the grade item associated to the grade category.
        $catitemnototal = $catnototal->load_grade_item();
        $catitemnototal->gradetype = GRADE_TYPE_NONE;
        $catitemnototal->update();

        // Grade tree looks something like:
        // - Test course n        (Rendered).
        // -- Grade category n    (Rendered).
        // -- No total category   (Not rendered).
        $gradetree = \grade_category::fetch_course_tree($course->id);
        foreach ($gradetree['children'] as $child) {
            if ($child['object']->fullname == $nototalcategory) {
                $this->assertFalse(\grade_tree::can_output_item($child));
            } else {
                $this->assertTrue(\grade_tree::can_output_item($child));
            }
        }

        // Add another grade category with default settings under 'No total category'.
        $normalinnototalparams = [
            'courseid' => $course->id,
            'fullname' => 'Normal category in no total category',
            'parent' => $nototal->id
        ];
        $generator->create_grade_category($normalinnototalparams);

        // Grade tree looks something like:
        // - Test course n                           (Rendered).
        // -- Grade category n                       (Rendered).
        // -- No total category                      (Rendered).
        // --- Normal category in no total category  (Rendered).
        $gradetree = \grade_category::fetch_course_tree($course->id);
        foreach ($gradetree['children'] as $child) {
            // All children are now visible.
            $this->assertTrue(\grade_tree::can_output_item($child));
            if (!empty($child['children'])) {
                foreach ($child['children'] as $grandchild) {
                    $this->assertTrue(\grade_tree::can_output_item($grandchild));
                }
            }
        }

        // Add a grade category with grade type = None.
        $nototalcategory2 = 'No total category 2';
        $nototal2params = [
            'courseid' => $course->id,
            'fullname' => $nototalcategory2,
            'aggregation' => GRADE_AGGREGATE_WEIGHTED_MEAN
        ];
        $nototal2 = $generator->create_grade_category($nototal2params);
        $catnototal2 = \grade_category::fetch(array('id' => $nototal2->id));
        // Set the grade type of the grade item associated to the grade category.
        $catitemnototal2 = $catnototal2->load_grade_item();
        $catitemnototal2->gradetype = GRADE_TYPE_NONE;
        $catitemnototal2->update();

        // Add a category with no total under 'No total category'.
        $nototalinnototalcategory = 'Category with no total in no total category';
        $nototalinnototalparams = [
            'courseid' => $course->id,
            'fullname' => $nototalinnototalcategory,
            'aggregation' => GRADE_AGGREGATE_WEIGHTED_MEAN,
            'parent' => $nototal2->id
        ];
        $nototalinnototal = $generator->create_grade_category($nototalinnototalparams);
        $catnototalinnototal = \grade_category::fetch(array('id' => $nototalinnototal->id));
        // Set the grade type of the grade item associated to the grade category.
        $catitemnototalinnototal = $catnototalinnototal->load_grade_item();
        $catitemnototalinnototal->gradetype = GRADE_TYPE_NONE;
        $catitemnototalinnototal->update();

        // Grade tree looks something like:
        // - Test course n                                    (Rendered).
        // -- Grade category n                                (Rendered).
        // -- No total category                               (Rendered).
        // --- Normal category in no total category           (Rendered).
        // -- No total category 2                             (Not rendered).
        // --- Category with no total in no total category    (Not rendered).
        $gradetree = \grade_category::fetch_course_tree($course->id);
        foreach ($gradetree['children'] as $child) {
            if ($child['object']->fullname == $nototalcategory2) {
                $this->assertFalse(\grade_tree::can_output_item($child));
            } else {
                $this->assertTrue(\grade_tree::can_output_item($child));
            }
            if (!empty($child['children'])) {
                foreach ($child['children'] as $grandchild) {
                    if ($grandchild['object']->fullname == $nototalinnototalcategory) {
                        $this->assertFalse(\grade_tree::can_output_item($grandchild));
                    } else {
                        $this->assertTrue(\grade_tree::can_output_item($grandchild));
                    }
                }
            }
        }
    }

    /**
     * Tests that ungraded_counts calculates count and sum of grades correctly when there are graded users.
     *
     * @covers \grade_report::ungraded_counts
     */
    public function test_ungraded_counts_count_sumgrades() {
        global $DB;

        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $studentrole = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher'], '*', MUST_EXIST);

        // Custom roles (gradable and non gradable).
        $gradeblerole = create_role('New student role', 'gradable',
            'Gradable role', 'student');
        $nongradeblerole = create_role('New student role', 'nongradable',
            'Non gradable role', 'student');

        // Set up gradable roles.
        set_config('gradebookroles', $studentrole->id . ',' . $gradeblerole);

        // Create users.

        // These will be gradable users.
        $student1 = $this->getDataGenerator()->create_user(['username' => 'student1']);
        $student2 = $this->getDataGenerator()->create_user(['username' => 'student2']);
        $student3 = $this->getDataGenerator()->create_user(['username' => 'student3']);
        $student5 = $this->getDataGenerator()->create_user(['username' => 'student5']);

        // These will be non-gradable users.
        $student4 = $this->getDataGenerator()->create_user(['username' => 'student4']);
        $student6 = $this->getDataGenerator()->create_user(['username' => 'student6']);
        $teacher = $this->getDataGenerator()->create_user(['username' => 'teacher']);

        // Enrol students.
        $this->getDataGenerator()->enrol_user($student1->id, $course1->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($student2->id, $course1->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($student3->id, $course1->id, $gradeblerole);

        $this->getDataGenerator()->enrol_user($student5->id, $course1->id, $nongradeblerole);
        $this->getDataGenerator()->enrol_user($student6->id, $course1->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($teacher->id, $course1->id, $teacherrole->id);

        // User that is enrolled in a different course.
        $this->getDataGenerator()->enrol_user($student4->id, $course2->id, $studentrole->id);

        // Mark user as deleted.
        $student6->deleted = 1;
        $DB->update_record('user', $student6);

        // Create grade items in course 1.
        $assign1 = $this->getDataGenerator()->create_module('assign', ['course' => $course1->id]);
        $assign2 = $this->getDataGenerator()->create_module('assign', ['course' => $course1->id]);
        $quiz1 = $this->getDataGenerator()->create_module('quiz', ['course' => $course1->id]);

        $manuaitem = new \grade_item($this->getDataGenerator()->create_grade_item([
            'itemname'        => 'Grade item1',
            'idnumber'        => 'git1',
            'courseid'        => $course1->id,
        ]));

        // Create grade items in course 2.
        $assign3 = $this->getDataGenerator()->create_module('assign', ['course' => $course2->id]);

        // Grade users in first course.
        $cm = cm_info::create(get_coursemodule_from_instance('assign', $assign1->id));
        $assigninstance = new assign($cm->context, $cm, $course1);
        $grade = $assigninstance->get_user_grade($student1->id, true);
        $grade->grade = 40;
        $assigninstance->update_grade($grade);

        $cm = cm_info::create(get_coursemodule_from_instance('assign', $assign2->id));
        $assigninstance = new assign($cm->context, $cm, $course1);
        $grade = $assigninstance->get_user_grade($student3->id, true);
        $grade->grade = 50;
        $assigninstance->update_grade($grade);

        // Override grade for assignment in gradebook.
        $gi = \grade_item::fetch([
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $cm->instance,
            'courseid' => $course1->id
        ]);
        $gi->update_final_grade($student3->id, 55);

        // Grade user in second course.
        $cm = cm_info::create(get_coursemodule_from_instance('assign', $assign3->id));
        $assigninstance = new assign($cm->context, $cm, $course2);
        $grade = $assigninstance->get_user_grade($student4->id, true);
        $grade->grade = 40;
        $assigninstance->update_grade($grade);

        $manuaitem->update_final_grade($student1->id, 1);
        $manuaitem->update_final_grade($student3->id, 2);

        // Trigger a regrade.
        grade_force_full_regrading($course1->id);
        grade_force_full_regrading($course2->id);
        grade_regrade_final_grades($course1->id);
        grade_regrade_final_grades($course2->id);

        // Initialise reports.
        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);

        $gpr1 = new grade_plugin_return(
            [
                'type' => 'report',
                'plugin' => 'grader',
                'course' => $course1,
            ]
        );

        $gpr2 = new grade_plugin_return(
            [
                'type' => 'report',
                'plugin' => 'grader',
                'course' => $course2,
            ]
        );

        $report1 = new grade_report_grader($course1->id, $gpr1, $context1);
        $report2 = new grade_report_grader($course2->id, $gpr2, $context2);

        $ungradedcounts = [];
        $ungradedcounts[$course1->id] = $report1->ungraded_counts(false);
        $ungradedcounts[$course2->id] = $report2->ungraded_counts(false);

        foreach ($ungradedcounts as $key => $ungradedcount) {
            $gradeitems = grade_item::fetch_all(['courseid' => $key]);
            if ($key == $course1->id) {
                $gradeitemkeys = array_keys($gradeitems);
                $ungradedcountskeys = array_keys($ungradedcount['ungradedcounts']);

                // For each grade item there is some student that is not graded yet in course 1.
                $this->assertEmpty(array_diff_key($gradeitemkeys, $ungradedcountskeys));

                // Only quiz does not have any grades, the remaning 4 grade items should have some.
                // We can do more and match by gradeitem id numbers. But feels like overengeneering.
                $this->assertEquals(4, count($ungradedcount['sumarray']));
            } else {

                // In course 2 there is one student, and he is graded.
                $this->assertEmpty($ungradedcount['ungradedcounts']);

                // There are 2 grade items and they both have some grades.
                $this->assertEquals(2, count($ungradedcount['sumarray']));
            }

            foreach ($gradeitems as $gradeitem) {
                $sumgrades = null;
                if (array_key_exists($gradeitem->id, $ungradedcount['ungradedcounts'])) {
                    $ungradeditem = $ungradedcount['ungradedcounts'][$gradeitem->id];
                    if ($gradeitem->itemtype === 'course') {
                        $this->assertEquals(1, $ungradeditem->count);
                    } else if ($gradeitem->itemmodule === 'assign') {
                        $this->assertEquals(2, $ungradeditem->count);
                    } else if ($gradeitem->itemmodule === 'quiz') {
                        $this->assertEquals(3, $ungradeditem->count);
                    } else if ($gradeitem->itemtype === 'manual') {
                        $this->assertEquals(1, $ungradeditem->count);
                    }
                }

                if (array_key_exists($gradeitem->id, $ungradedcount['sumarray'])) {
                    $sumgrades = $ungradedcount['sumarray'][$gradeitem->id];
                    if ($gradeitem->itemtype === 'course') {
                        if ($key == $course1->id) {
                            $this->assertEquals('98.00000', $sumgrades); // 40 + 55 + 1 + 2
                        } else {
                            $this->assertEquals('40.00000', $sumgrades);
                        }
                    } else if ($gradeitem->itemmodule === 'assign') {
                        if (($gradeitem->itemname === $assign1->name) || ($gradeitem->itemname === $assign3->name)) {
                            $this->assertEquals('40.00000', $sumgrades);
                        } else {
                            $this->assertEquals('55.00000', $sumgrades);
                        }
                    } else if ($gradeitem->itemtype === 'manual') {
                        $this->assertEquals('3.00000', $sumgrades);
                    }
                }
            }
        }
    }

    /**
     * Tests that ungraded_counts calculates count and sum of grades correctly when there are hidden grades.
     * @dataProvider ungraded_counts_hidden_grades_data()
     * @param bool $hidden Whether to inlcude hidden grades or not.
     * @param array $expectedcount expected count value (i.e. number of ugraded grades)
     * @param array $expectedsumarray expceted sum of grades
     *
     * @covers \grade_report::ungraded_counts
     */
    public function test_ungraded_counts_hidden_grades(bool $hidden, array $expectedcount, array $expectedsumarray) {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        // Create users.
        $student1 = $this->getDataGenerator()->create_user(['username' => 'student1']);
        $student2 = $this->getDataGenerator()->create_user(['username' => 'student2']);
        $student3 = $this->getDataGenerator()->create_user(['username' => 'student3']);

        // Enrol students.
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, 'student');

        // Create grade items in course.
        $manuaitem = new \grade_item($this->getDataGenerator()->create_grade_item([
            'itemname' => 'Grade item1',
            'idnumber' => 'git1',
            'courseid' => $course->id,
        ]));

        // Grade users.
        $manuaitem->update_final_grade($student1->id, 1);
        $manuaitem->update_final_grade($student3->id, 2);

        // Create a hidden grade.
        $manuaitem->update_final_grade($student2->id, 3);
        $grade = \grade_grade::fetch(['itemid' => $manuaitem->id, 'userid' => $student2->id]);
        $grade->hidden = 1;
        $grade->update();

        // Trigger a regrade.
        grade_force_full_regrading($course->id);
        grade_regrade_final_grades($course->id);

        // Initialise reports.
        $context = \context_course::instance($course->id);

        $gpr = new grade_plugin_return(
            [
                'type' => 'report',
                'plugin' => 'grader',
                'course' => $course,
            ]
        );

        $report = new grade_report_grader($course->id, $gpr, $context);

        $ungradedcounts = $report->ungraded_counts(false, $hidden);

        $gradeitems = grade_item::fetch_all(['courseid' => $course->id]);

        foreach ($gradeitems as $gradeitem) {
            $sumgrades = null;
            if (array_key_exists($gradeitem->id, $ungradedcounts['ungradedcounts'])) {
                $ungradeditem = $ungradedcounts['ungradedcounts'][$gradeitem->id];
                if ($gradeitem->itemtype === 'course') {
                    $this->assertEquals($expectedcount['course'], $ungradeditem->count);
                } else if ($gradeitem->itemtype === 'manual') {
                    $this->assertEquals($expectedcount['Grade item1'], $ungradeditem->count);
                }
            }

            if (array_key_exists($gradeitem->id, $ungradedcounts['sumarray'])) {
                $sumgrades = $ungradedcounts['sumarray'][$gradeitem->id];
                if ($gradeitem->itemtype === 'course') {
                    $this->assertEquals($expectedsumarray['course'], $sumgrades);
                } else if ($gradeitem->itemtype === 'manual') {
                    $this->assertEquals($expectedsumarray['Grade item1'], $sumgrades);
                }
            }
        }
    }

    /**
     * Data provider for test_ungraded_counts_hidden_grades
     *
     * @return array of testing scenarios
     */
    public function ungraded_counts_hidden_grades_data(): array {
        return [
            'nohidden' => [
                'hidden' => false,
                'count' => ['course' => 1, 'Grade item1' => 1],
                'sumarray' => ['course' => 6.00000, 'Grade item1' => 3.00000],
            ],
            'includehidden' => [
                'hidden' => true,
                'count' => ['course' => 1, 'Grade item1' => 2],
                'sumarray' => ['course' => 6.00000, 'Grade item1' => 6.00000],
            ],
        ];
    }

    /**
     * Tests that ungraded_counts calculates count and sum of grades correctly for groups when there are graded users.
     *
     * @covers \grade_report::ungraded_counts
     */
    public function test_ungraded_count_sumgrades_groups() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $studentrole = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);

        // Create users.
        $student1 = $this->getDataGenerator()->create_user(['username' => 'student1']);
        $student2 = $this->getDataGenerator()->create_user(['username' => 'student2']);
        $student3 = $this->getDataGenerator()->create_user(['username' => 'student3']);

        // Enrol students.
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, $studentrole->id);

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student2->id, 'groupid' => $group2->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student3->id, 'groupid' => $group2->id]);
        $DB->set_field('course', 'groupmode', SEPARATEGROUPS, ['id' => $course->id]);

        // Create grade items.
        $assign1 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $assign2 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $quiz1 = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $manuaitem = new \grade_item($this->getDataGenerator()->create_grade_item([
            'itemname'        => 'Grade item1',
            'idnumber'        => 'git1',
            'courseid'        => $course->id,
        ]));

        // Grade users.
        $cm = cm_info::create(get_coursemodule_from_instance('assign', $assign1->id));
        $assigninstance = new assign($cm->context, $cm, $course);
        $grade = $assigninstance->get_user_grade($student1->id, true);
        $grade->grade = 40;
        $assigninstance->update_grade($grade);

        $cm = cm_info::create(get_coursemodule_from_instance('assign', $assign2->id));
        $assigninstance = new assign($cm->context, $cm, $course);
        $grade = $assigninstance->get_user_grade($student3->id, true);
        $grade->grade = 50;
        $assigninstance->update_grade($grade);

        $manuaitem->update_final_grade($student1->id, 1);
        $manuaitem->update_final_grade($student3->id, 2);

        // Trigger a regrade.
        grade_force_full_regrading($course->id);
        grade_regrade_final_grades($course->id);

        // Initialise report.
        $context = \context_course::instance($course->id);

        $gpr1 = new grade_plugin_return(
            [
                'type' => 'report',
                'plugin' => 'grader',
                'course' => $course,
                'groupid' => $group1->id,
            ]
        );

        $gpr2 = new grade_plugin_return(
            [
                'type' => 'report',
                'plugin' => 'grader',
                'course' => $course,
                'groupid' => $group2->id,
            ]
        );

        $report1 = new grade_report_grader($course->id, $gpr1, $context);
        $report2 = new grade_report_grader($course->id, $gpr2, $context);

        $ungradedcounts = [];
        $ungradedcounts[$group1->id] = $report1->ungraded_counts(true);
        $ungradedcounts[$group2->id] = $report2->ungraded_counts(true);

        $gradeitems = grade_item::fetch_all(['courseid' => $course->id]);

        // In group1 there is 1 student and assign1 and quiz1 are not graded for him.
        $this->assertEquals(2, count($ungradedcounts[$group1->id]['ungradedcounts']));

        // In group1 manual grade item, assign1 and course total have some grades.
        $this->assertEquals(3, count($ungradedcounts[$group1->id]['sumarray']));

        // In group2 student2 has no grades at all so all 5 grade items should present.
        $this->assertEquals(5, count($ungradedcounts[$group2->id]['ungradedcounts']));

        // In group2 manual grade item, assign2 and course total have some grades.
        $this->assertEquals(3, count($ungradedcounts[$group2->id]['sumarray']));

        foreach ($gradeitems as $gradeitem) {
            $sumgrades = null;

            foreach ($ungradedcounts as $key => $ungradedcount) {
                if (array_key_exists($gradeitem->id, $ungradedcount['ungradedcounts'])) {
                    $ungradeditem = $ungradedcount['ungradedcounts'][$gradeitem->id];
                    if ($key == $group1->id) {
                        // Both assign2 and quiz1 are not graded for student1.
                        $this->assertEquals(1, $ungradeditem->count);
                    } else {
                        if ($gradeitem->itemtype === 'course') {
                            $this->assertEquals(1, $ungradeditem->count);
                        } else if ($gradeitem->itemmodule === 'assign') {
                            if ($gradeitem->itemname === $assign1->name) {
                                // In group2 assign1 is not graded for anyone.
                                $this->assertEquals(2, $ungradeditem->count);
                            } else {
                                // In group2 assign2 is graded for student3.
                                $this->assertEquals(1, $ungradeditem->count);
                            }
                        } else if ($gradeitem->itemmodule === 'quiz') {
                            $this->assertEquals(2, $ungradeditem->count);
                        } else if ($gradeitem->itemtype === 'manual') {
                            $this->assertEquals(1, $ungradeditem->count);
                        }
                    }
                }

                if (array_key_exists($gradeitem->id, $ungradedcount['sumarray'])) {
                    $sumgrades = $ungradedcount['sumarray'][$gradeitem->id];
                    if ($key == $group1->id) {
                        if ($gradeitem->itemtype === 'course') {
                            $this->assertEquals('41.00000', $sumgrades);
                        } else if ($gradeitem->itemmodule === 'assign') {
                            $this->assertEquals('40.00000', $sumgrades);
                        } else if ($gradeitem->itemtype === 'manual') {
                            $this->assertEquals('1.00000', $sumgrades);
                        }
                    } else {
                        if ($gradeitem->itemtype === 'course') {
                            $this->assertEquals('52.00000', $sumgrades);
                        } else if ($gradeitem->itemmodule === 'assign') {
                            $this->assertEquals('50.00000', $sumgrades);
                        } else if ($gradeitem->itemtype === 'manual') {
                            $this->assertEquals('2.00000', $sumgrades);
                        }
                    }
                }
            }
        }
    }

    /**
     * Tests that ungraded_counts calculates count and sum of grades correctly when there are hidden grades.
     * @dataProvider ungraded_counts_only_active_enrol_data()
     * @param bool $onlyactive Site setting to show only active users.
     * @param int $hascapability Capability constant
     * @param bool|null $showonlyactiveenrolpref Show only active user preference.
     * @param array $expectedcount expected count value (i.e. number of ugraded grades)
     * @param array $expectedsumarray expected sum of grades
     *
     * @covers \grade_report::ungraded_counts
     */
    public function test_ungraded_counts_only_active_enrol(bool $onlyactive,
            int $hascapability, ?bool $showonlyactiveenrolpref, array $expectedcount, array $expectedsumarray) {
        global $CFG, $DB;

        $this->resetAfterTest();

        $CFG->grade_report_showonlyactiveenrol = $onlyactive;
        $course = $this->getDataGenerator()->create_course();

        // Create users.
        $student1 = $this->getDataGenerator()->create_user(['username' => 'student1']);
        $student2 = $this->getDataGenerator()->create_user(['username' => 'student2']);
        $student3 = $this->getDataGenerator()->create_user(['username' => 'student3']);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        // Enrol students.
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');

        // Give teacher 'viewsuspendedusers' capability and set a preference to display suspended users.
        $roleteacher = $DB->get_record('role', ['shortname' => 'teacher'], '*', MUST_EXIST);
        $coursecontext = \context_course::instance($course->id);
        assign_capability('moodle/course:viewsuspendedusers', $hascapability, $roleteacher->id, $coursecontext, true);
        set_user_preference('grade_report_showonlyactiveenrol', $showonlyactiveenrolpref, $teacher);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($teacher);

        // Suspended student.
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, 'student', 'manual', 0, 0, ENROL_USER_SUSPENDED);

        // Create grade items in course.
        $manuaitem = new \grade_item($this->getDataGenerator()->create_grade_item([
            'itemname' => 'Grade item1',
            'idnumber' => 'git1',
            'courseid' => $course->id,
        ]));

        // Grade users.
        $manuaitem->update_final_grade($student1->id, 1);
        $manuaitem->update_final_grade($student3->id, 2);

        // Trigger a regrade.
        grade_force_full_regrading($course->id);
        grade_regrade_final_grades($course->id);

        // Initialise reports.
        $context = \context_course::instance($course->id);

        $gpr = new grade_plugin_return(
            [
                'type' => 'report',
                'plugin' => 'grader',
                'course' => $course,
            ]
        );

        $report = new grade_report_grader($course->id, $gpr, $context);

        $showonlyactiveenrol = $report->show_only_active();
        $ungradedcounts = $report->ungraded_counts(false, false, $showonlyactiveenrol);

        $gradeitems = grade_item::fetch_all(['courseid' => $course->id]);

        foreach ($gradeitems as $gradeitem) {
            $sumgrades = null;
            if (array_key_exists($gradeitem->id, $ungradedcounts['ungradedcounts'])) {
                $ungradeditem = $ungradedcounts['ungradedcounts'][$gradeitem->id];
                if ($gradeitem->itemtype === 'course') {
                    $this->assertEquals($expectedcount['course'], $ungradeditem->count);
                } else if ($gradeitem->itemtype === 'manual') {
                    $this->assertEquals($expectedcount['Grade item1'], $ungradeditem->count);
                }
            }

            if (array_key_exists($gradeitem->id, $ungradedcounts['sumarray'])) {
                $sumgrades = $ungradedcounts['sumarray'][$gradeitem->id];
                if ($gradeitem->itemtype === 'course') {
                    $this->assertEquals($expectedsumarray['course'], $sumgrades);
                } else if ($gradeitem->itemtype === 'manual') {
                    $this->assertEquals($expectedsumarray['Grade item1'], $sumgrades);
                }
            }
        }
    }

    /**
     * Data provider for test_ungraded_counts_hidden_grades
     *
     * @return array of testing scenarios
     */
    public function ungraded_counts_only_active_enrol_data(): array {
        return [
            'Show only active and no user preference' => [
                'onlyactive' => true,
                'hascapability' => 1,
                'showonlyactiveenrolpref' => null,
                'count' => ['course' => 1, 'Grade item1' => 1],
                'sumarray' => ['course' => 1, 'Grade item1' => 1.00000],
            ],
            'Show only active and user preference set to true' => [
                'onlyactive' => true,
                'hascapability' => 1,
                'showonlyactiveenrolpref' => true,
                'count' => ['course' => 1, 'Grade item1' => 1],
                'sumarray' => ['course' => 1, 'Grade item1' => 1.00000],
            ],
            'Show only active and user preference set to false' => [
                'onlyactive' => true,
                'hascapability' => 1,
                'showonlyactiveenrolpref' => false,
                'count' => ['course' => 1, 'Grade item1' => 1],
                'sumarray' => ['course' => 3.00000, 'Grade item1' => 3.00000],
            ],
            'Include suspended with capability and user preference set to true' => [
                'onlyactive' => false,
                'hascapability' => 1,
                'showonlyactiveenrolpref' => true,
                'count' => ['course' => 1, 'Grade item1' => 1],
                'sumarray' => ['course' => 1.00000, 'Grade item1' => 1.00000],
            ],
            'Include suspended with capability and user preference set to false' => [
                'onlyactive' => false,
                'hascapability' => 1,
                'showonlyactiveenrolpref' => false,
                'count' => ['course' => 1, 'Grade item1' => 1],
                'sumarray' => ['course' => 3.00000, 'Grade item1' => 3.00000],
            ],
            'Include suspended with capability and no user preference' => [
                'onlyactive' => false,
                'hascapability' => 1,
                'showonlyactiveenrolpref' => null,
                'count' => ['course' => 1, 'Grade item1' => 1],
                'sumarray' => ['course' => 3.00000, 'Grade item1' => 3.00000],
            ],
            'Include suspended without capability' => [
                'onlyactive' => false,
                'hascapability' => -1,
                'showonlyactiveenrolpref' => null,
                'count' => ['course' => 1, 'Grade item1' => 1],
                'sumarray' => ['course' => 1.00000, 'Grade item1' => 1.00000],
            ],
        ];
    }

    /**
     * Tests for calculate_average.
     * @dataProvider calculate_average_data()
     * @param int $meanselection Whether to inlcude all grades or non-empty grades in aggregation.
     * @param array $expectedmeancount expected meancount value
     * @param array $expectedaverage expceted average value
     *
     * @covers \grade_report::calculate_average
     */
    public function test_calculate_average(int $meanselection, array $expectedmeancount, array $expectedaverage) {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        $student1 = $this->getDataGenerator()->create_user(['username' => 'student1']);
        $student2 = $this->getDataGenerator()->create_user(['username' => 'student2']);
        $student3 = $this->getDataGenerator()->create_user(['username' => 'student3']);

        $studentrole = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);

        // Enrol students.
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, $studentrole->id);

        // Create activities.
        $assign1 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $assign2 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $quiz1 = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        // Grade users.
        $cm = cm_info::create(get_coursemodule_from_instance('assign', $assign1->id));
        $assigninstance = new assign($cm->context, $cm, $course);
        $grade = $assigninstance->get_user_grade($student1->id, true);
        $grade->grade = 40;
        $assigninstance->update_grade($grade);

        $grade = $assigninstance->get_user_grade($student2->id, true);
        $grade->grade = 30;
        $assigninstance->update_grade($grade);

        $cm = cm_info::create(get_coursemodule_from_instance('assign', $assign2->id));
        $assigninstance = new assign($cm->context, $cm, $course);
        $grade = $assigninstance->get_user_grade($student3->id, true);
        $grade->grade = 50;
        $assigninstance->update_grade($grade);

        $grade = $assigninstance->get_user_grade($student1->id, true);
        $grade->grade = 100;
        $assigninstance->update_grade($grade);

        // Make a manual grade items.
        $manuaitem = new \grade_item($this->getDataGenerator()->create_grade_item([
            'itemname'        => 'Grade item1',
            'idnumber'        => 'git1',
            'courseid'        => $course->id,
        ]));
        $manuaitem->update_final_grade($student1->id, 1);
        $manuaitem->update_final_grade($student3->id, 2);

        // Initialise report.
        $context = \context_course::instance($course->id);

        $gpr = new grade_plugin_return(
            [
                'type' => 'report',
                'plugin' => 'grader',
                'course' => $course,
            ]
        );

        $report = new grade_report_grader($course->id, $gpr, $context);

        $ungradedcounts = $report->ungraded_counts(false);
        $ungradedcounts['report']['meanselection'] = $meanselection;

        $gradeitems = grade_item::fetch_all(['courseid' => $course->id]);

        foreach ($gradeitems as $gradeitem) {
            $name = $gradeitem->itemname . ' ' . $gradeitem->itemtype;
            $aggr = $report->calculate_average($gradeitem, $ungradedcounts);

            $this->assertEquals($expectedmeancount[$name], $aggr['meancount']);
            $this->assertEquals($expectedaverage[$name], $aggr['average']);
        }
    }

    /**
     * Data provider for test_calculate_average
     *
     * @return array of testing scenarios
     */
    public function calculate_average_data(): array {
        return [
            'Non-empty grades' => [
                'meanselection' => 1,
                'expectedmeancount' => [' course' => 3, 'Assignment 1 mod' => 2, 'Assignment 2 mod' => 2,
                    'Quiz 1 mod' => 0, 'Grade item1 manual' => 2],
                'expectedaverage' => [' course' => 73.33333333333333, 'Assignment 1 mod' => 35.0,
                    'Assignment 2 mod' => 75.0, 'Quiz 1 mod' => null, 'Grade item1 manual' => 1.5],
            ],
            'All grades' => [
                'meanselection' => 0,
                'expectedmeancount' => [' course' => 3, 'Assignment 1 mod' => 3, 'Assignment 2 mod' => 3,
                    'Quiz 1 mod' => 3, 'Grade item1 manual' => 3],
                'expectedaverage' => [' course' => 73.33333333333333, 'Assignment 1 mod' => 23.333333333333332,
                    'Assignment 2 mod' => 50.0, 'Quiz 1 mod' => null, 'Grade item1 manual' => 1.0],
            ],
        ];
    }

    /**
     * Tests for item types.
     *
     * @covers \grade_report::item_types
     */
    public function test_item_types() {
        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        // Create activities.
        $this->getDataGenerator()->create_module('assign', ['course' => $course1->id]);
        $this->getDataGenerator()->create_module('assign', ['course' => $course1->id]);
        $this->getDataGenerator()->create_module('quiz', ['course' => $course1->id]);

        $this->getDataGenerator()->create_module('assign', ['course' => $course2->id]);

        // Create manual grade items.
        new \grade_item($this->getDataGenerator()->create_grade_item([
            'itemname'        => 'Grade item1',
            'idnumber'        => 'git1',
            'courseid'        => $course1->id,
        ]));

        new \grade_item($this->getDataGenerator()->create_grade_item([
            'itemname'        => 'Grade item2',
            'idnumber'        => 'git2',
            'courseid'        => $course2->id,
        ]));

        // Create a grade category (it should not be fetched by item_types).
        new \grade_category($this->getDataGenerator()
            ->create_grade_category(['courseid' => $course1->id]), false);

        // Initialise reports.
        $context = \context_course::instance($course1->id);

        $gpr = new grade_plugin_return(
            [
                'type' => 'report',
                'plugin' => 'grader',
                'course' => $course1,
            ]
        );

        $report1 = new grade_report_grader($course1->id, $gpr, $context);

        $context = \context_course::instance($course2->id);

        $gpr = new grade_plugin_return(
            [
                'type' => 'report',
                'plugin' => 'grader',
                'course' => $course2,
            ]
        );

        $report2 = new grade_report_grader($course2->id, $gpr, $context);

        $gradeitems1 = $report1->item_types();
        $gradeitems2 = $report2->item_types();

        $this->assertEquals(3, count($gradeitems1));
        $this->assertEquals(2, count($gradeitems2));

        $this->assertArrayHasKey('assign', $gradeitems1);
        $this->assertArrayHasKey('quiz', $gradeitems1);
        $this->assertArrayHasKey('manual', $gradeitems1);

        $this->assertArrayHasKey('assign', $gradeitems2);
        $this->assertArrayHasKey('manual', $gradeitems2);
    }

    /**
     * Test get_gradable_users() function.
     *
     * @covers ::get_gradable_users
     */
    public function test_get_gradable_users() {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest(true);

        $roleteacher = $DB->get_record('role', ['shortname' => 'teacher'], '*', MUST_EXIST);

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        // Create groups.
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        // Create and enrol a teacher and some students into the course.
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student3 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        // Add student1 and student2 to group1.
        $this->getDataGenerator()->create_group_member(['groupid' => $group1->id, 'userid' => $student1->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $group1->id, 'userid' => $student2->id]);
        // Add student3 to group2.
        $this->getDataGenerator()->create_group_member(['groupid' => $group2->id, 'userid' => $student3->id]);

        // Perform a regrade before creating the report.
        grade_regrade_final_grades($course->id);
        // Should return all gradable users (only students).
        $gradableusers = get_gradable_users($course->id);
        $this->assertEqualsCanonicalizing([$student1->id, $student2->id, $student3->id], array_keys($gradableusers));

        // Now, let's suspend the enrolment of student2.
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student', 'manual', 0, 0, ENROL_USER_SUSPENDED);
        // Should return only the active gradable users (student1 and student3).
        $gradableusers = \grade_report::get_gradable_users($course->id);
        $this->assertEqualsCanonicalizing([$student1->id, $student3->id], array_keys($gradableusers));

        // Give teacher 'viewsuspendedusers' capability and set a preference to display suspended users.
        assign_capability('moodle/course:viewsuspendedusers', CAP_ALLOW, $roleteacher->id, $coursecontext, true);
        set_user_preference('grade_report_showonlyactiveenrol', false, $teacher);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($teacher);
        // Should return all gradable users (including suspended enrolments).
        $gradableusers = \grade_report::get_gradable_users($course->id);
        $this->assertEqualsCanonicalizing([$student1->id, $student2->id, $student3->id], array_keys($gradableusers));

        // Reactivate the course enrolment of student2.
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student', 'manual', 0, 0, ENROL_USER_ACTIVE);
        $this->setAdminUser();
        // Should return all gradable users from group1 (student1 and student2).
        $gradableusers = \grade_report::get_gradable_users($course->id, $group1->id);
        $this->assertEqualsCanonicalizing([$student1->id, $student2->id], array_keys($gradableusers));
        // Should return all gradable users from group2 (student3).
        $gradableusers = \grade_report::get_gradable_users($course->id, $group2->id);
        $this->assertEqualsCanonicalizing([$student3->id], array_keys($gradableusers));
    }
}
