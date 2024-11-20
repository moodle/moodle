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
 * Unit tests for grade/report/lib.php.
 *
 * @package  core_grades
 * @category phpunit
 * @author   Andrew Davis
 * @license  http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/report/lib.php');

/**
 * A test class used to test grade_report, the abstract grade report parent class
 */
class grade_report_mock extends grade_report {
    public function __construct($courseid, $gpr, $context, $user) {
        parent::__construct($courseid, $gpr, $context);
        $this->user = $user;
    }

    /**
     * A wrapper around blank_hidden_total_and_adjust_bounds() to allow test code to call it directly
     */
    public function blank_hidden_total_and_adjust_bounds($courseid, $courseitem, $finalgrade) {
        return parent::blank_hidden_total_and_adjust_bounds($courseid, $courseitem, $finalgrade);
    }

    /**
     * Implementation of the abstract method process_data()
     */
    public function process_data($data) {
    }

    /**
     * Implementation of the abstract method process_action()
     */
    public function process_action($target, $action) {
    }
}

/**
 * Tests grade_report, the parent class for all grade reports.
 */
class reportlib_test extends advanced_testcase {

    /**
     * Tests grade_report::blank_hidden_total_and_adjust_bounds()
     */
    public function test_blank_hidden_total_and_adjust_bounds(): void {
        global $DB;

        $this->resetAfterTest(true);

        $student = $this->getDataGenerator()->create_user();
        $this->setUser($student);

        // Create a course and two activities.
        // One activity will be hidden.
        $course = $this->getDataGenerator()->create_course();
        $coursegradeitem = grade_item::fetch_course_item($course->id);
        $coursecontext = context_course::instance($course->id);

        $data = $this->getDataGenerator()->create_module('data', array('assessed' => 1, 'scale' => 100, 'course' => $course->id));
        $datacm = get_coursemodule_from_id('data', $data->cmid);

        $forum = $this->getDataGenerator()->create_module('forum', array('assessed' => 1, 'scale' => 100, 'course' => $course->id));
        $forumcm = get_coursemodule_from_id('forum', $forum->cmid);

        // Insert student grades for the two activities.
        $gi = grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => 'data', 'iteminstance' => $data->id, 'courseid' => $course->id));
        $datagrade = 50;
        $grade_grade = new grade_grade();
        $grade_grade->itemid = $gi->id;
        $grade_grade->userid = $student->id;
        $grade_grade->rawgrade = $datagrade;
        $grade_grade->finalgrade = $datagrade;
        $grade_grade->rawgrademax = 100;
        $grade_grade->rawgrademin = 0;
        $grade_grade->timecreated = time();
        $grade_grade->timemodified = time();
        $grade_grade->insert();

        $gi = grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => 'forum', 'iteminstance' => $forum->id, 'courseid' => $course->id));
        $forumgrade = 70;
        $grade_grade = new grade_grade();
        $grade_grade->itemid = $gi->id;
        $grade_grade->userid = $student->id;
        $grade_grade->rawgrade = $forumgrade;
        $grade_grade->finalgrade = $forumgrade;
        $grade_grade->rawgrademax = 100;
        $grade_grade->rawgrademin = 0;
        $grade_grade->timecreated = time();
        $grade_grade->timemodified = time();
        $grade_grade->insert();

        // Hide the database activity.
        set_coursemodule_visible($datacm->id, 0);

        $gpr = new grade_plugin_return(array('type' => 'report', 'courseid' => $course->id));
        $report = new grade_report_mock($course->id, $gpr, $coursecontext, $student);

        // Should return the supplied student total grade regardless of hiding.
        $report->showtotalsifcontainhidden = array($course->id => GRADE_REPORT_SHOW_REAL_TOTAL_IF_CONTAINS_HIDDEN);
        $result = $report->blank_hidden_total_and_adjust_bounds($course->id, $coursegradeitem, $datagrade + $forumgrade);
        $this->assertEquals(array('grade' => $datagrade + $forumgrade,
                                  'grademax' => $coursegradeitem->grademax,
                                  'grademin' => $coursegradeitem->grademin,
                                  'aggregationstatus' => 'unknown',
                                  'aggregationweight' => null), $result);
        // Should blank the student total as course grade depends on a hidden item.
        $report->showtotalsifcontainhidden = array($course->id => GRADE_REPORT_HIDE_TOTAL_IF_CONTAINS_HIDDEN);
        $result = $report->blank_hidden_total_and_adjust_bounds($course->id, $coursegradeitem, $datagrade + $forumgrade);
        $this->assertEquals(array('grade' => null,
                                  'grademax' => $coursegradeitem->grademax,
                                  'grademin' => $coursegradeitem->grademin,
                                  'aggregationstatus' => 'unknown',
                                  'aggregationweight' => null), $result);

        // Should return the course total minus the hidden database activity grade.
        $report->showtotalsifcontainhidden = array($course->id => GRADE_REPORT_SHOW_TOTAL_IF_CONTAINS_HIDDEN);
        $result = $report->blank_hidden_total_and_adjust_bounds($course->id, $coursegradeitem, $datagrade + $forumgrade);
        $this->assertEquals(array('grade' => floatval($forumgrade),
                                  'grademax' => $coursegradeitem->grademax,
                                  'grademin' => $coursegradeitem->grademin,
                                  'aggregationstatus' => 'unknown',
                                  'aggregationweight' => null), $result);

        // Note: we cannot simply hide modules and call $report->blank_hidden_total() again.
        // It stores grades in a static variable so $report->blank_hidden_total() will return incorrect totals
        // In practice this isn't a problem. Grade visibility isn't altered mid-request outside of the unit tests.

        // Add a second course to test:
        // 1) How a course with no visible activities behaves.
        // 2) That $report->blank_hidden_total() correctly moves on to the new course.
        $course = $this->getDataGenerator()->create_course();
        $coursegradeitem = grade_item::fetch_course_item($course->id);
        $coursecontext = context_course::instance($course->id);

        $data = $this->getDataGenerator()->create_module('data', array('assessed' => 1, 'scale' => 100, 'course' => $course->id));
        $datacm = get_coursemodule_from_id('data', $data->cmid);

        $forum = $this->getDataGenerator()->create_module('forum', array('assessed' => 1, 'scale' => 100, 'course' => $course->id));
        $forumcm = get_coursemodule_from_id('forum', $forum->cmid);

        $gi = grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => 'data', 'iteminstance' => $data->id, 'courseid' => $course->id));
        $datagrade = 50;
        $grade_grade = new grade_grade();
        $grade_grade->itemid = $gi->id;
        $grade_grade->userid = $student->id;
        $grade_grade->rawgrade = $datagrade;
        $grade_grade->finalgrade = $datagrade;
        $grade_grade->rawgrademax = 100;
        $grade_grade->rawgrademin = 0;
        $grade_grade->timecreated = time();
        $grade_grade->timemodified = time();
        $grade_grade->insert();

        $gi = grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => 'forum', 'iteminstance' => $forum->id, 'courseid' => $course->id));
        $forumgrade = 70;
        $grade_grade = new grade_grade();
        $grade_grade->itemid = $gi->id;
        $grade_grade->userid = $student->id;
        $grade_grade->rawgrade = $forumgrade;
        $grade_grade->finalgrade = $forumgrade;
        $grade_grade->rawgrademax = 100;
        $grade_grade->rawgrademin = 0;
        $grade_grade->timecreated = time();
        $grade_grade->timemodified = time();
        $grade_grade->insert();

        // Hide both activities.
        set_coursemodule_visible($datacm->id, 0);
        set_coursemodule_visible($forumcm->id, 0);

        $gpr = new grade_plugin_return(array('type' => 'report', 'courseid' => $course->id));
        $report = new grade_report_mock($course->id, $gpr, $coursecontext, $student);

        // Should return the supplied student total grade regardless of hiding.
        $report->showtotalsifcontainhidden = array($course->id => GRADE_REPORT_SHOW_REAL_TOTAL_IF_CONTAINS_HIDDEN);
        $result = $report->blank_hidden_total_and_adjust_bounds($course->id, $coursegradeitem, $datagrade + $forumgrade);
        $this->assertEquals(array('grade' => $datagrade + $forumgrade,
                                  'grademax' => $coursegradeitem->grademax,
                                  'grademin' => $coursegradeitem->grademin,
                                  'aggregationstatus' => 'unknown',
                                  'aggregationweight' => null), $result);

        // Should blank the student total as course grade depends on a hidden item.
        $report->showtotalsifcontainhidden = array($course->id => GRADE_REPORT_HIDE_TOTAL_IF_CONTAINS_HIDDEN);
        $result = $report->blank_hidden_total_and_adjust_bounds($course->id, $coursegradeitem, $datagrade + $forumgrade);
        $this->assertEquals(array('grade' => null,
                                  'grademax' => $coursegradeitem->grademax,
                                  'grademin' => $coursegradeitem->grademin,
                                  'aggregationstatus' => 'unknown',
                                  'aggregationweight' => null), $result);

        // Should return the course total minus the hidden activity grades.
        // They are both hidden so should return null.
        $report->showtotalsifcontainhidden = array($course->id => GRADE_REPORT_SHOW_TOTAL_IF_CONTAINS_HIDDEN);
        $result = $report->blank_hidden_total_and_adjust_bounds($course->id, $coursegradeitem, $datagrade + $forumgrade);
        $this->assertEquals(array('grade' => null,
                                  'grademax' => $coursegradeitem->grademax,
                                  'grademin' => $coursegradeitem->grademin,
                                  'aggregationstatus' => 'unknown',
                                  'aggregationweight' => null), $result);
    }
}
