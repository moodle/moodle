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
 * Unit tests for (some of) mod/assign/lib.php.
 *
 * @package    mod_assign
 * @category   phpunit
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/lib.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

/**
 * Unit tests for (some of) mod/assign/lib.php.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_lib_testcase extends mod_assign_base_testcase {

    public function test_assign_print_overview() {
        global $DB;
        $this->setUser($this->editingteachers[0]);
        $this->create_instance();
        $this->create_instance(array('duedate'=>time()));

        $courses = $DB->get_records('course', array('id' => $this->course->id));

        $this->setUser($this->students[0]);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertEquals(count($overview), 1);

        $this->setUser($this->teachers[0]);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertEquals(count($overview), 1);

        $this->setUser($this->editingteachers[0]);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertEquals(1, count($overview));
    }

    public function test_print_recent_activity() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $submission = $assign->get_user_submission($this->students[0]->id, true);

        $this->expectOutputRegex('/submitted:/');
        assign_print_recent_activity($this->course, true, time() - 3600);
    }

    /** Make sure fullname dosn't trigger any warnings when assign_print_recent_activity is triggered. */
    public function test_print_recent_activity_fullname() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $assign->get_user_submission($this->students[0]->id, true);

        $this->expectOutputRegex('/submitted:/');
        set_config('fullnamedisplay', 'firstname, lastnamephonetic');
        assign_print_recent_activity($this->course, false, time() - 3600);
    }

    public function test_assign_get_recent_mod_activity() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $submission = $assign->get_user_submission($this->students[0]->id, true);

        $activities = array();
        $index = 0;

        $activity = new stdClass();
        $activity->type    = 'activity';
        $activity->cmid    = $assign->get_course_module()->id;
        $activities[$index++] = $activity;

        assign_get_recent_mod_activity( $activities,
                                        $index,
                                        time() - 3600,
                                        $this->course->id,
                                        $assign->get_course_module()->id);

        $this->assertEquals("assign", $activities[1]->type);
    }

    public function test_assign_user_complete() {
        global $PAGE;

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('submissiondrafts' => 1));
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id'=>$assign->get_course_module()->id)));

        $submission = $assign->get_user_submission($this->students[0]->id, true);

        $this->expectOutputRegex('/Draft/');
        assign_user_complete($this->course, $this->students[0], $assign->get_course_module(), $assign->get_instance());
    }

    public function test_assign_user_outline() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $this->setUser($this->teachers[0]);
        $data = $assign->get_user_grade($this->students[0]->id, true);
        $data->grade = '50.5';
        $assign->update_grade($data);

        $result = assign_user_outline($this->course, $this->students[0], $assign->get_course_module(), $assign->get_instance());

        $this->assertRegExp('/50.5/', $result->info);
    }

    public function test_assign_get_completion_state() {
        global $DB;
        $assign = $this->create_instance(array('submissiondrafts'=>0, 'completionsubmit'=>1));

        $this->setUser($this->students[0]);
        $result = assign_get_completion_state($this->course, $assign->get_course_module(), $this->students[0]->id, false);
        $this->assertFalse($result);
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $DB->update_record('assign_submission', $submission);

        $result = assign_get_completion_state($this->course, $assign->get_course_module(), $this->students[0]->id, false);

        $this->assertTrue($result);
    }

}
