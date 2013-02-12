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
 * Unit tests for grade/report/user/lib.php.
 *
 * @package  core_grade
 * @category phpunit
 * @copyright 2012 Andrew Davis
 * @license  http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/report/grader/lib.php');

/**
 * Tests grade_report_grader (the grader report)
 */
class grade_report_graderlib_testcase extends advanced_testcase {

    /**
     * Tests grade_report_grader::process_data()
     *
     * process_data() processes submitted grade and feedback data
     */
    public function test_process_data() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        // Create and enrol a student.
        $student = $this->getDataGenerator()->create_user(array('username' => 'Student Sam'));
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $role->id);

        // Test with limited grades.
        $CFG->unlimitedgrades = 0;

        $forummax = 80;
        $forum1 = $this->getDataGenerator()->create_module('forum', array('assessed' => 1, 'scale' => $forummax, 'course' => $course->id));
        // Switch the stdClass instance for a grade item instance.
        $forum1 = grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => 'forum', 'iteminstance' => $forum1->id, 'courseid' => $course->id));

        $report = $this->create_report($course, $coursecontext);
        $testgrade = 60.00;

        $data = new stdClass();
        $data->id = $course->id;
        $data->report = 'grader';

        $data->grade = array();
        $data->grade[$student->id] = array();
        $data->grade[$student->id][$forum1->id] = $testgrade;

        $warnings = $report->process_data($data);
        $this->assertEquals(count($warnings), 0);

        $studentgrade = grade_grade::fetch(array('itemid' => $forum1->id, '' => $student->id));
        $this->assertEquals($studentgrade->finalgrade, $testgrade);

        // Grade above max. Should be pulled down to max.
        $toobig = 200.00;
        $data->grade[$student->id][$forum1->id] = $toobig;
        $warnings = $report->process_data($data);
        $this->assertEquals(count($warnings), 1);

        $studentgrade = grade_grade::fetch(array('itemid' => $forum1->id, '' => $student->id));
        $this->assertEquals($studentgrade->finalgrade, $forummax);

        // Grade below min. Should be pulled up to min.
        $toosmall = -10.00;
        $data->grade[$student->id][$forum1->id] = $toosmall;
        $warnings = $report->process_data($data);
        $this->assertEquals(count($warnings), 1);

        $studentgrade = grade_grade::fetch(array('itemid' => $forum1->id, '' => $student->id));
        $this->assertEquals($studentgrade->finalgrade, 0);

        // Test unlimited grades so we can give a student a grade about max.
        $CFG->unlimitedgrades = 1;

        $data->grade[$student->id][$forum1->id] = $toobig;
        $warnings = $report->process_data($data);
        $this->assertEquals(count($warnings), 0);

        $studentgrade = grade_grade::fetch(array('itemid' => $forum1->id, '' => $student->id));
        $this->assertEquals($studentgrade->finalgrade, $toobig);
    }

    private function create_report($course, $coursecontext) {

        $gpr = new grade_plugin_return(array('type' => 'report', 'plugin'=>'grader', 'courseid' => $course->id));
        $report = new grade_report_grader($course->id, $gpr, $coursecontext);

        return $report;
    }
}
