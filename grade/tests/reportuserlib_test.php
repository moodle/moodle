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
use grade_report_user;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/report/user/lib.php');

/**
 * Tests grade_report_user (the gradebook's user report)
 *
 * @package  core_grades
 * @category test
 * @copyright 2012 Andrew Davis
 * @license  http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class reportuserlib_test extends \advanced_testcase {

    /**
     * Tests grade_report_user::inject_rowspans()
     *
     * inject_rowspans() returns the count of the number of elements, sets maxdepth on the
     *  report object and sets the rowspan property on any element that has children.
     */
    public function test_inject_rowspans() {
        global $CFG, $USER, $DB;

        parent::setUp();
        $this->resetAfterTest(true);

        $CFG->enableavailability = 1;
        $CFG->enablecompletion = 1;

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $coursecategory = \grade_category::fetch_course_category($course->id);
        $coursecontext = \context_course::instance($course->id);

        // Create and enrol test users.
        $student = $this->getDataGenerator()->create_user(array('username' => 'student_sam'));
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $role->id);

        $teacher = $this->getDataGenerator()->create_user(array('username' => 'teacher_t'));
        $role = $DB->get_record('role', array('shortname' => 'editingteacher'), '*', MUST_EXIST);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $role->id);

        // An array so we can test with both users in a loop.
        $users = array($student, $teacher);

        // Make the student the current user.
        $this->setUser($student);

        // Test an empty course.
        $report = $this->create_report($course, $student, $coursecontext);
        // a lead column that spans all children + course grade item = 2
        $this->assertEquals(2, $report->inject_rowspans($report->gtree->top_element));
        $this->assertEquals(2, $report->gtree->top_element['rowspan']);
        $this->assertEquals(2, $report->maxdepth);

        // Only elements with children should have rowspan set.
        if (array_key_exists('rowspan', $report->gtree->top_element['children'][1])) {
            $this->fail('Elements without children should not have rowspan set');
        }

        // Add 2 activities.
        $data1 = $this->getDataGenerator()->create_module('data', array('assessed' => 1, 'scale' => 100, 'course' => $course->id));

        $forum1 = $this->getDataGenerator()->create_module('forum', array('assessed' => 1, 'scale' => 100, 'course' => $course->id));
        $forum1cm = get_coursemodule_from_id('forum', $forum1->cmid);
        // Switch the stdClass instance for a grade item instance so \grade_item::set_parent() is available.
        $forum1 = \grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => 'forum', 'iteminstance' => $forum1->id, 'courseid' => $course->id));

        $report = $this->create_report($course, $student, $coursecontext);
        // Lead column + course + (2 x activity) = 4
        $this->assertEquals(4, $report->inject_rowspans($report->gtree->top_element));
        $this->assertEquals(4, $report->gtree->top_element['rowspan']);
        // Lead column + 1 level (course + 2 activities) = 2
        $this->assertEquals(2, $report->maxdepth);

        // Only elements with children should have rowspan set.
        if (array_key_exists('rowspan', $report->gtree->top_element['children'][1])) {
            $this->fail('Elements without children should not have rowspan set');
        }

        // Hide the forum activity.
        set_coursemodule_visible($forum1cm->id, 0);

        foreach ($users as $user) {

            $this->setUser($user);
            $message = 'Testing with ' . $user->username;
            accesslib_clear_all_caches_for_unit_testing();

            $report = $this->create_report($course, $user, $coursecontext);
            // Lead column + course + (2 x activity) = 4 (element count isn't affected by hiding)
            $this->assertEquals(4, $report->inject_rowspans($report->gtree->top_element), $message);
            $this->assertEquals(4, $report->gtree->top_element['rowspan'], $message);
            // Lead column -> 1 level containing the course + 2 activities = 2
            $this->assertEquals(2, $report->maxdepth, $message);
        }

        // Unhide the forum activity.
        set_coursemodule_visible($forum1cm->id, 1);

        // Create a category and put the forum in it.
        $params = new \stdClass();
        $params->courseid = $course->id;
        $params->fullname = 'unittestcategory';
        $params->parent = $coursecategory->id;
        $gradecategory = new \grade_category($params, false);
        $gradecategory->insert();

        $forum1->set_parent($gradecategory->id);

        $report = $this->create_report($course, $student, $coursecontext);
        // Lead column + course + (category + category grade item) + (2 x activity) = 6
        $this->assertEquals(6, $report->inject_rowspans($report->gtree->top_element));
        $this->assertEquals(6, $report->gtree->top_element['rowspan']);
        // Lead column -> the category -> the forum activity = 3
        $this->assertEquals(3, $report->maxdepth);

        // Check rowspan on the category. The category itself + category grade item + forum = 3
        $this->assertEquals(3, $report->gtree->top_element['children'][4]['rowspan']);
        // check the forum doesn't have rowspan set
        if (array_key_exists('rowspan', $report->gtree->top_element['children'][4]['children'][3])) {
            $this->fail('The forum has no children so should not have rowspan set');
        }

        // Conditional activity tests.
        // Note: I have ported this test to the new conditional availability
        // system, but it does not appear to actually test anything - in fact,
        // if you remove the code that sets the condition, it still passes
        // because it apparently is intended to have the same number of rows
        // even when some are hidden. The  same is true of the
        // set_coursemodule_visible test above. I don't feel this is a very
        // good test; somebody with more knowledge of this report might want to
        // fix it to check that the row actually is being hidden.
        $DB->set_field('course_modules', 'availability', '{"op":"|","show":false,"c":[' .
                '{"type":"grade","min":5.5,"id":37}]}', array('id' => $forum1cm->id));
        get_fast_modinfo($course->id, 0, true);
        foreach ($users as $user) {

            $this->setUser($user);
            $message = 'Testing with ' . $user->username;
            accesslib_clear_all_caches_for_unit_testing();

            $report = $this->create_report($course, $user, $coursecontext);
            // Lead column + course + (category + category grade item) + (2 x activity) = 6
            $this->assertEquals(6, $report->inject_rowspans($report->gtree->top_element), $message);
            $this->assertEquals(6, $report->gtree->top_element['rowspan'], $message);
            // Lead column -> the category -> the forum activity = 3
            $this->assertEquals(3, $report->maxdepth, $message);
        }
    }

    private function create_report($course, $user, $coursecontext) {

        $gpr = new grade_plugin_return(array('type' => 'report', 'plugin'=>'user', 'courseid' => $course->id, 'userid' => $user->id));
        $report = new grade_report_user($course->id, $gpr, $coursecontext, $user->id);

        return $report;
    }

}

