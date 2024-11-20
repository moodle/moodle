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

namespace core_grades;

use grade_plugin_return;
use grade_report_grader;
use mod_quiz\quiz_settings;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/report/grader/lib.php');

/**
 * Tests grade_report_grader (the grader report)
 *
 * @package  core_grades
 * @covers   \grade_report_grader
 * @category test
 * @copyright 2012 Andrew Davis
 * @license  http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class report_graderlib_test extends \advanced_testcase {

    /**
     * Tests grade_report_grader::process_data()
     *
     * process_data() processes submitted grade and feedback data
     */
    public function test_process_data(): void {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        // Create and enrol a student.
        $student = $this->getDataGenerator()->create_user(array('username' => 'student_sam'));
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $role->id);

        // Test with limited grades.
        $CFG->unlimitedgrades = 0;

        $forummax = 80;
        $forum1 = $this->getDataGenerator()->create_module('forum', array('assessed' => 1, 'scale' => $forummax, 'course' => $course->id));
        // Switch the stdClass instance for a grade item instance.
        $forum1 = \grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => 'forum', 'iteminstance' => $forum1->id, 'courseid' => $course->id));

        $report = $this->create_report($course);
        $testgrade = 60.00;

        $data = new \stdClass();
        $data->id = $course->id;
        $data->report = 'grader';
        $data->timepageload = time();

        $data->grade = array();
        $data->grade[$student->id] = array();
        $data->grade[$student->id][$forum1->id] = $testgrade;

        $warnings = $report->process_data($data);
        $this->assertEquals(count($warnings), 0);

        $studentgrade = \grade_grade::fetch(array('itemid' => $forum1->id, '' => $student->id));
        $this->assertEquals($studentgrade->finalgrade, $testgrade);

        // Grade above max. Should be pulled down to max.
        $toobig = 200.00;
        $data->grade[$student->id][$forum1->id] = $toobig;
        $data->timepageload = time();
        $warnings = $report->process_data($data);
        $this->assertEquals(count($warnings), 1);

        $studentgrade = \grade_grade::fetch(array('itemid' => $forum1->id, '' => $student->id));
        $this->assertEquals($studentgrade->finalgrade, $forummax);

        // Grade below min. Should be pulled up to min.
        $toosmall = -10.00;
        $data->grade[$student->id][$forum1->id] = $toosmall;
        $data->timepageload = time();
        $warnings = $report->process_data($data);
        $this->assertEquals(count($warnings), 1);

        $studentgrade = \grade_grade::fetch(array('itemid' => $forum1->id, '' => $student->id));
        $this->assertEquals($studentgrade->finalgrade, 0);

        // Test unlimited grades so we can give a student a grade about max.
        $CFG->unlimitedgrades = 1;

        $data->grade[$student->id][$forum1->id] = $toobig;
        $data->timepageload = time();
        $warnings = $report->process_data($data);
        $this->assertEquals(count($warnings), 0);

        $studentgrade = \grade_grade::fetch(array('itemid' => $forum1->id, '' => $student->id));
        $this->assertEquals($studentgrade->finalgrade, $toobig);
    }

    public function test_collapsed_preferences(): void {
        $this->resetAfterTest(true);

        $emptypreferences = array('aggregatesonly' => array(), 'gradesonly' => array());

        $user1 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $this->setUser($user1);

        $report = $this->create_report($course1);
        $this->assertEquals($emptypreferences, $report->collapsed);

        // Validating preferences set/get for one course.
        $report->process_action('cg13', 'switch_minus');
        $report = $this->create_report($course1);
        $this->assertEquals(array(13), $report->collapsed['aggregatesonly']);
        $this->assertEmpty($report->collapsed['gradesonly']);

        $report->process_action('cg13', 'switch_plus');
        $report = $this->create_report($course1);
        $this->assertEmpty($report->collapsed['aggregatesonly']);
        $this->assertEquals(array(13), $report->collapsed['gradesonly']);

        $report->process_action('cg13', 'switch_whole');
        $report = $this->create_report($course1);
        $this->assertEquals($emptypreferences, $report->collapsed);

        // Validating preferences set/get for several courses.

        $course1cats = $course2cats = $course3cats = array();
        for ($i=0;$i<10;$i++) {
            $course1cats[] = $this->create_grade_category($course1)->id;
            $course2cats[] = $this->create_grade_category($course2)->id;
            $course3cats[] = $this->create_grade_category($course3)->id;
        }

        $report1 = $this->create_report($course1);
        foreach ($course1cats as $catid) {
            $report1->process_action('cg'.$catid, 'switch_minus');
        }
        $report2 = $this->create_report($course2);
        foreach ($course2cats as $catid) {
            $report2->process_action('cg'.$catid, 'switch_minus');
            $report2->process_action('cg'.$catid, 'switch_plus');
        }
        $report3 = $this->create_report($course3);
        foreach ($course3cats as $catid) {
            $report3->process_action('cg'.$catid, 'switch_minus');
            if (($i++)%2) {
                $report3->process_action('cg'.$catid, 'switch_plus');
            }
        }

        $report1 = $this->create_report($course1);
        $this->assertEquals(10, count($report1->collapsed['aggregatesonly']));
        $this->assertEquals(0, count($report1->collapsed['gradesonly']));
        $report2 = $this->create_report($course2);
        $this->assertEquals(0, count($report2->collapsed['aggregatesonly']));
        $this->assertEquals(10, count($report2->collapsed['gradesonly']));
        $report3 = $this->create_report($course3);
        $this->assertEquals(5, count($report3->collapsed['aggregatesonly']));
        $this->assertEquals(5, count($report3->collapsed['gradesonly']));

        // Test upgrade script.
        // Combine data generated for user1 and set it in the old format for user2, Try to retrieve it and make sure it is converted.

        $user2 = $this->getDataGenerator()->create_user();
        $alldata = array(
            'aggregatesonly' => array_merge($report1->collapsed['aggregatesonly'], $report2->collapsed['aggregatesonly'], $report3->collapsed['aggregatesonly']),
            'gradesonly' => array_merge($report1->collapsed['gradesonly'], $report2->collapsed['gradesonly'], $report3->collapsed['gradesonly']),
        );
        set_user_preference('grade_report_grader_collapsed_categories', serialize($alldata), $user2);

        $this->setUser($user2);
        $convertedreport1 = $this->create_report($course1);
        $this->assertEquals($report1->collapsed, $convertedreport1->collapsed);
        $convertedreport2 = $this->create_report($course2);
        $this->assertEquals($report2->collapsed, $convertedreport2->collapsed);
        $convertedreport3 = $this->create_report($course3);
        $this->assertEquals($report3->collapsed, $convertedreport3->collapsed);
        // Make sure the old style user preference is removed now.
        $this->assertEmpty(get_user_preferences('grade_report_grader_collapsed_categories'));

        // Test overflowing the setting with non-existing categories (only validated if new setting size exceeds 1333 chars).

        $toobigvalue = $expectedvalue = $report1->collapsed;
        for ($i = 0; strlen(json_encode($toobigvalue)) < 1333; $i++) {
            $toobigvalue[($i < 7) ? 'gradesonly' : 'aggregatesonly'][] = $course1cats[9] + 1 + $i;
        }
        $lastvalue = array_pop($toobigvalue['gradesonly']);
        set_user_preference('grade_report_grader_collapsed_categories'.$course1->id, json_encode($toobigvalue));

        $report1 = $this->create_report($course1);
        $report1->process_action('cg'.$lastvalue, 'switch_minus');

        $report1 = $this->create_report($course1);
        $this->assertEquals($expectedvalue, $report1->collapsed);

        // Test overflowing the setting with existing categories.

        $toobigvalue = $report1->collapsed;
        for ($i = 0; strlen(json_encode($toobigvalue)) < 1333; $i++) {
            $catid = $this->create_grade_category($course1)->id;
            $toobigvalue[($i < 7) ? 'gradesonly' : 'aggregatesonly'][] = $catid;
        }
        $lastcatid = array_pop($toobigvalue['gradesonly']);
        set_user_preference('grade_report_grader_collapsed_categories'.$course1->id, json_encode($toobigvalue));
        $toobigvalue['aggregatesonly'][] = $lastcatid;

        $report1 = $this->create_report($course1);
        $report1->process_action('cg'.$lastcatid, 'switch_minus');

        // One last value should be removed from both arrays.
        $report1 = $this->create_report($course1);
        $this->assertEquals(count($toobigvalue['aggregatesonly']) - 1, count($report1->collapsed['aggregatesonly']));
        $this->assertEquals(count($toobigvalue['gradesonly']) - 1, count($report1->collapsed['gradesonly']));
    }

    /**
     * Test some special cases of the conversion from old preferences to new ones
     *
     * @covers \grade_report_grader::get_collapsed_preferences
     * @covers \grade_report_grader::filter_collapsed_categories
     */
    public function test_old_collapsed_preferences(): void {
        $this->resetAfterTest(true);

        $user1 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $course1cats = $course2cats = $course3cats = [];
        for ($i = 0; $i < 10; $i++) {
            $course1cats[] = $this->create_grade_category($course1)->id;
            $course2cats[] = $this->create_grade_category($course2)->id;
            $course3cats[] = $this->create_grade_category($course3)->id;
        }

        $report1 = $this->create_report($course1);
        // Collapse all the cats in course1.
        foreach ($course1cats as $catid) {
            $report1->process_action('cg'. $catid, 'switch_minus');
        }

        // Expand all the cats in course2.
        $report2 = $this->create_report($course2);
        foreach ($course2cats as $catid) {
            $report2->process_action('cg'.$catid, 'switch_minus');
            $report2->process_action('cg'.$catid, 'switch_plus');
        }

        // Collapse odd cats and expand even cats in course3.
        $report3 = $this->create_report($course3);
        foreach ($course3cats as $catid) {
            $report3->process_action('cg'.$catid, 'switch_minus');
            if (($i++) % 2) {
                $report3->process_action('cg'.$catid, 'switch_plus');
            }
        }

        $report1 = $this->create_report($course1);
        $this->assertEquals(10, count($report1->collapsed['aggregatesonly']));
        $this->assertEquals(0, count($report1->collapsed['gradesonly']));
        $report2 = $this->create_report($course2);
        $this->assertEquals(0, count($report2->collapsed['aggregatesonly']));
        $this->assertEquals(10, count($report2->collapsed['gradesonly']));
        $report3 = $this->create_report($course3);
        $this->assertEquals(5, count($report3->collapsed['aggregatesonly']));
        $this->assertEquals(5, count($report3->collapsed['gradesonly']));

        // Use the preferences generated for user1 and set it in the old format for other users.

        // User2: both gradesonly and aggregatesonly.
        $user2 = $this->getDataGenerator()->create_user();
        $alldata = [
            'gradesonly' => array_merge(
                $report1->collapsed['gradesonly'],
                $report2->collapsed['gradesonly'],
                $report3->collapsed['gradesonly']),
            'aggregatesonly' => array_merge(
                $report1->collapsed['aggregatesonly'],
                $report2->collapsed['aggregatesonly'],
                $report3->collapsed['aggregatesonly']),
        ];
        set_user_preference('grade_report_grader_collapsed_categories', serialize($alldata), $user2);

        $this->setUser($user2);
        $convertedreport1 = $this->create_report($course1);
        $this->assertEquals($report1->collapsed['gradesonly'], $convertedreport1->collapsed['gradesonly']);
        $this->assertEquals($report1->collapsed['aggregatesonly'], $convertedreport1->collapsed['aggregatesonly']);
        $newprefs1 = get_user_preferences('grade_report_grader_collapsed_categories' . $course1->id); // Also verify new prefs.
        $this->assertEquals($report1->collapsed['gradesonly'], json_decode($newprefs1, true)['gradesonly']);
        $this->assertEquals($report1->collapsed['aggregatesonly'], json_decode($newprefs1, true)['aggregatesonly']);

        $convertedreport2 = $this->create_report($course2);
        $this->assertEquals($report2->collapsed['gradesonly'], $convertedreport2->collapsed['gradesonly']);
        $this->assertEquals($report2->collapsed['aggregatesonly'], $convertedreport2->collapsed['aggregatesonly']);
        $newprefs2 = get_user_preferences('grade_report_grader_collapsed_categories' . $course2->id); // Also verify new prefs.
        $this->assertEquals($report2->collapsed['gradesonly'], json_decode($newprefs2, true)['gradesonly']);
        $this->assertEquals($report2->collapsed['aggregatesonly'], json_decode($newprefs2, true)['aggregatesonly']);

        $convertedreport3 = $this->create_report($course3);
        $this->assertEquals($report3->collapsed['gradesonly'], $convertedreport3->collapsed['gradesonly']);
        $this->assertEquals($report3->collapsed['aggregatesonly'], $convertedreport3->collapsed['aggregatesonly']);
        $newprefs3 = get_user_preferences('grade_report_grader_collapsed_categories' . $course3->id); // Also verify new prefs.
        $this->assertEquals($report3->collapsed['gradesonly'], json_decode($newprefs3, true)['gradesonly']);
        $this->assertEquals($report3->collapsed['aggregatesonly'], json_decode($newprefs3, true)['aggregatesonly']);

        // Make sure the old style user preference is removed now.
        $this->assertEmpty(get_user_preferences('grade_report_grader_collapsed_categories'));

        // User3: only gradesonly (missing aggregatesonly).
        $user3 = $this->getDataGenerator()->create_user();
        $alldata = [
            'gradesonly' => array_merge(
                $report1->collapsed['gradesonly'],
                $report2->collapsed['gradesonly'],
                $report3->collapsed['gradesonly']),
        ];
        set_user_preference('grade_report_grader_collapsed_categories', serialize($alldata), $user3);

        $this->setUser($user3);
        $convertedreport1 = $this->create_report($course1);
        $this->assertEquals($report1->collapsed['gradesonly'], $convertedreport1->collapsed['gradesonly']);
        $this->assertEquals([], $convertedreport1->collapsed['aggregatesonly']);
        $newprefs1 = get_user_preferences('grade_report_grader_collapsed_categories' . $course1->id); // Also verify new prefs.
        $this->assertNull($newprefs1);

        $convertedreport2 = $this->create_report($course2);
        $this->assertEquals($report2->collapsed['gradesonly'], $convertedreport2->collapsed['gradesonly']);
        $this->assertEquals([], $convertedreport2->collapsed['aggregatesonly']);
        $newprefs2 = get_user_preferences('grade_report_grader_collapsed_categories' . $course2->id); // Also verify new prefs.
        $this->assertEquals($report2->collapsed['gradesonly'], json_decode($newprefs2, true)['gradesonly']);
        $this->assertEquals([], json_decode($newprefs2, true)['aggregatesonly']);

        $convertedreport3 = $this->create_report($course3);
        $this->assertEquals($report3->collapsed['gradesonly'], $convertedreport3->collapsed['gradesonly']);
        $this->assertEquals([], $convertedreport3->collapsed['aggregatesonly']);
        $newprefs3 = get_user_preferences('grade_report_grader_collapsed_categories' . $course3->id); // Also verify new prefs.
        $this->assertEquals($report3->collapsed['gradesonly'], json_decode($newprefs3, true)['gradesonly']);
        $this->assertEquals([], json_decode($newprefs3, true)['aggregatesonly']);

        // Make sure the old style user preference is removed now.
        $this->assertEmpty(get_user_preferences('grade_report_grader_collapsed_categories'));

        // User4: only aggregatesonly (missing gradesonly).
        $user4 = $this->getDataGenerator()->create_user();
        $alldata = [
            'aggregatesonly' => array_merge(
                $report1->collapsed['aggregatesonly'],
                $report2->collapsed['aggregatesonly'],
                $report3->collapsed['aggregatesonly']),
        ];
        set_user_preference('grade_report_grader_collapsed_categories', serialize($alldata), $user4);

        $this->setUser($user4);
        $convertedreport1 = $this->create_report($course1);
        $this->assertEquals([], $convertedreport1->collapsed['gradesonly']);
        $this->assertEquals($report1->collapsed['aggregatesonly'], $convertedreport1->collapsed['aggregatesonly']);
        $newprefs1 = get_user_preferences('grade_report_grader_collapsed_categories' . $course1->id); // Also verify new prefs.
        $this->assertEquals([], json_decode($newprefs1, true)['gradesonly']);
        $this->assertEquals($report1->collapsed['aggregatesonly'], json_decode($newprefs1, true)['aggregatesonly']);

        $convertedreport2 = $this->create_report($course2);
        $this->assertEquals([], $convertedreport2->collapsed['gradesonly']);
        $this->assertEquals($report2->collapsed['aggregatesonly'], $convertedreport2->collapsed['aggregatesonly']);
        $newprefs2 = get_user_preferences('grade_report_grader_collapsed_categories' . $course2->id); // Also verify new prefs.
        $this->assertNull($newprefs2);

        $convertedreport3 = $this->create_report($course3);
        $this->assertEquals([], $convertedreport3->collapsed['gradesonly']);
        $this->assertEquals($report3->collapsed['aggregatesonly'], $convertedreport3->collapsed['aggregatesonly']);
        $newprefs3 = get_user_preferences('grade_report_grader_collapsed_categories' . $course3->id); // Also verify new prefs.
        $this->assertEquals([], json_decode($newprefs3, true)['gradesonly']);
        $this->assertEquals($report3->collapsed['aggregatesonly'], json_decode($newprefs3, true)['aggregatesonly']);

        // Make sure the old style user preference is removed now.
        $this->assertEmpty(get_user_preferences('grade_report_grader_collapsed_categories'));

        // User5: both missing gradesonly and aggregatesonly.
        $user5 = $this->getDataGenerator()->create_user();
        $alldata = [];
        set_user_preference('grade_report_grader_collapsed_categories', serialize($alldata), $user5);

        $this->setUser($user5);
        $convertedreport1 = $this->create_report($course1);
        $this->assertEquals([], $convertedreport1->collapsed['gradesonly']);
        $this->assertEquals([], $convertedreport1->collapsed['aggregatesonly']);
        $newprefs1 = get_user_preferences('grade_report_grader_collapsed_categories' . $course1->id); // Also verify new prefs.
        $this->assertNull($newprefs1);

        $convertedreport2 = $this->create_report($course2);
        $this->assertEquals([], $convertedreport2->collapsed['gradesonly']);
        $this->assertEquals([], $convertedreport2->collapsed['aggregatesonly']);
        $newprefs2 = get_user_preferences('grade_report_grader_collapsed_categories' . $course2->id); // Also verify new prefs.
        $this->assertNull($newprefs2);

        $convertedreport3 = $this->create_report($course3);
        $this->assertEquals([], $convertedreport3->collapsed['gradesonly']);
        $this->assertEquals([], $convertedreport3->collapsed['aggregatesonly']);
        $newprefs3 = get_user_preferences('grade_report_grader_collapsed_categories' . $course3->id); // Also verify new prefs.
        $this->assertNull($newprefs3);

        // Make sure the old style user preference is removed now.
        $this->assertEmpty(get_user_preferences('grade_report_grader_collapsed_categories'));

        // User6: both empty gradesonly and aggregatesonly.
        $user6 = $this->getDataGenerator()->create_user();
        $alldata = [
            'gradesonly' => [],
            'aggregatesonly' => []
        ];
        set_user_preference('grade_report_grader_collapsed_categories', serialize($alldata), $user6);

        $this->setUser($user6);
        $convertedreport1 = $this->create_report($course1);
        $this->assertEquals([], $convertedreport1->collapsed['gradesonly']);
        $this->assertEquals([], $convertedreport1->collapsed['aggregatesonly']);
        $newprefs1 = get_user_preferences('grade_report_grader_collapsed_categories' . $course1->id); // Also verify new prefs.
        $this->assertNull($newprefs1);

        $convertedreport2 = $this->create_report($course2);
        $this->assertEquals([], $convertedreport2->collapsed['gradesonly']);
        $this->assertEquals([], $convertedreport2->collapsed['aggregatesonly']);
        $newprefs2 = get_user_preferences('grade_report_grader_collapsed_categories' . $course2->id); // Also verify new prefs.
        $this->assertNull($newprefs2);

        $convertedreport3 = $this->create_report($course3);
        $this->assertEquals([], $convertedreport3->collapsed['gradesonly']);
        $this->assertEquals([], $convertedreport3->collapsed['aggregatesonly']);
        $newprefs3 = get_user_preferences('grade_report_grader_collapsed_categories' . $course3->id); // Also verify new prefs.
        $this->assertNull($newprefs3);

        // Make sure the old style user preference is removed now.
        $this->assertEmpty(get_user_preferences('grade_report_grader_collapsed_categories'));
    }

    /**
     * Tests the get_right_rows function with one 'normal' and one 'ungraded' quiz.
     *
     * Previously, with an ungraded quiz (which results in a grade item with type GRADETYPE_NONE)
     * there was a bug in get_right_rows in some situations.
     */
    public function test_get_right_rows(): void {
        global $USER, $DB;
        $this->resetAfterTest(true);

        // Create manager and student on a course.
        $generator = $this->getDataGenerator();
        $manager = $generator->create_user();
        $student = $generator->create_user();
        $course = $generator->create_course();
        $generator->enrol_user($manager->id, $course->id, 'manager');
        $generator->enrol_user($student->id, $course->id, 'student');

        // Create a couple of quizzes on the course.
        $normalquiz = $generator->create_module('quiz', array('course' => $course->id,
                'name' => 'NormalQuiz'));
        $ungradedquiz = $generator->create_module('quiz', array('course' => $course->id,
                'name' => 'UngradedQuiz'));

        // Set the grade for the second one to 0 (note, you have to do this after creating it,
        // otherwise it doesn't create an ungraded grade item).
        quiz_settings::create($ungradedquiz->id)->get_grade_calculator()->update_quiz_maximum_grade(0);

        // Set current user.
        $this->setUser($manager);
        $USER->editing = false;

        // Get the report.
        $report = $this->create_report($course);
        $report->load_users();
        $report->load_final_grades();
        $result = $report->get_right_rows(false);

        // There should be 3 rows (2 header rows, plus the grades for the first user).
        $this->assertCount(3, $result);

        // The second row should contain 2 cells - one for the graded quiz and course total.
        $this->assertCount(2, $result[1]->cells);
        $this->assertStringContainsString('NormalQuiz', $result[1]->cells[0]->text);
        $this->assertStringContainsString('Course total', $result[1]->cells[1]->text);

        // User row should contain grade values '-'.
        $this->assertCount(2, $result[2]->cells);
        $this->assertStringContainsString('>-<', $result[2]->cells[0]->text);
        $this->assertStringContainsString('>-<', $result[2]->cells[1]->text);

        // Supposing the user cannot view hidden grades, this shouldn't make any difference (due
        // to a bug, it previously did).
        $context = \context_course::instance($course->id);
        $managerroleid = $DB->get_field('role', 'id', array('shortname' => 'manager'));
        assign_capability('moodle/grade:viewhidden', CAP_PROHIBIT, $managerroleid, $context->id, true);
        $this->assertFalse(has_capability('moodle/grade:viewhidden', $context));

        // Recreate the report. Confirm it returns successfully still.
        $report = $this->create_report($course);
        $report->load_users();
        $report->load_final_grades();
        $result = $report->get_right_rows(false);
        $this->assertCount(3, $result);
    }

    /**
     * Test loading report users when per page preferences are set
     */
    public function test_load_users_paging_preference(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        // The report users will default to sorting by their lastname.
        $user1 = $this->getDataGenerator()->create_and_enrol($course, 'student', ['lastname' => 'Apple']);
        $user2 = $this->getDataGenerator()->create_and_enrol($course, 'student', ['lastname' => 'Banana']);
        $user3 = $this->getDataGenerator()->create_and_enrol($course, 'student', ['lastname' => 'Carrot']);

        // Set to empty string.
        $report = $this->create_report($course);
        $report->set_pref('studentsperpage', '');
        $users = $report->load_users();
        $this->assertEquals([$user1->id, $user2->id, $user3->id], array_column($users, 'id'));

        // Set to valid value.
        $report = $this->create_report($course);
        $report->set_pref('studentsperpage', 2);
        $users = $report->load_users();
        $this->assertEquals([$user1->id, $user2->id], array_column($users, 'id'));
    }

    /**
     * Test getting students per page report preference
     */
    public function test_get_students_per_page(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $report = $this->create_report($course);
        $report->set_pref('studentsperpage', 10);

        $perpage = $report->get_students_per_page();
        $this->assertSame(10, $perpage);
    }

    private function create_grade_category($course) {
        static $cnt = 0;
        $cnt++;
        $gradecat = new \grade_category(array('courseid' => $course->id, 'fullname' => 'Cat '.$cnt), false);
        $gradecat->apply_default_settings();
        $gradecat->apply_forced_settings();
        $gradecat->insert();
        return $gradecat;
    }

    private function create_report($course) {

        $coursecontext = \context_course::instance($course->id);
        $gpr = new grade_plugin_return(array('type' => 'report', 'plugin'=>'grader', 'courseid' => $course->id));
        $report = new grade_report_grader($course->id, $gpr, $coursecontext);

        return $report;
    }
}
