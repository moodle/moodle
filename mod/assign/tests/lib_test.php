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

/**
 * Unit tests for (some of) mod/assign/lib.php.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_lib_testcase extends advanced_testcase {

    /** @var stdClass $course New course created to hold the assignments */
    protected $course = null;

    /** @var array $teachers List of 5 default teachers in the course*/
    protected $teachers = null;

    /** @var array $editingteachers List of 5 default editing teachers in the course*/
    protected $editingteachers = null;

    /** @var array $students List of 100 default students in the course*/
    protected $students = null;

    /** @var array $groups List of 10 groups in the course */
    protected $groups = null;

    /**
     * Setup function - we will create a course and users.
     */
    protected function setUp() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $this->course = $this->getDataGenerator()->create_course();
        $this->teachers = array();
        for ($i = 0; $i < 5; $i++) {
            array_push($this->teachers, $this->getDataGenerator()->create_user());
        }

        $this->editingteachers = array();
        for ($i = 0; $i < 5; $i++) {
            array_push($this->editingteachers, $this->getDataGenerator()->create_user());
        }

        $this->students = array();
        for ($i = 0; $i < 100; $i++) {
            array_push($this->students, $this->getDataGenerator()->create_user());
        }

        $this->groups = array();
        for ($i = 0; $i < 10; $i++) {
            array_push($this->groups, $this->getDataGenerator()->create_group(array('courseid'=>$this->course->id)));
        }

        $teacherrole = $DB->get_record('role', array('shortname'=>'teacher'));
        foreach ($this->teachers as $i => $teacher) {
            $this->getDataGenerator()->enrol_user($teacher->id,
                                                  $this->course->id,
                                                  $teacherrole->id);
            groups_add_member($this->groups[$i % 10], $teacher);
        }

        $editingteacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'));
        foreach ($this->editingteachers as $i => $editingteacher) {
            $this->getDataGenerator()->enrol_user($editingteacher->id,
                                                  $this->course->id,
                                                  $editingteacherrole->id);
            groups_add_member($this->groups[$i % 10], $editingteacher);
        }

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        foreach ($this->students as $i => $student) {
            $this->getDataGenerator()->enrol_user($student->id,
                                                  $this->course->id,
                                                  $studentrole->id);
            if ($i < 80) {
                groups_add_member($this->groups[$i % 10], $student);
            }
        }
    }

    /**
     * Create an assignment in the current course.
     *
     * @param array $params - A list of params used to configure the assignment
     * @return assign class
     */
    private function create_instance($params=array()) {
        $this->setUser($this->editingteachers[0]);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $this->course->id;
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);
        return new assign($context, $cm, $this->course);
    }

    public function test_assign_print_overview() {
        $this->create_instance();
        $this->create_instance(array('duedate'=>time()));

        $this->setUser($this->students[0]);
        $overview = array();
        assign_print_overview(array($this->course->id => $this->course), $overview);
        $this->assertEquals(count($overview), 1);

        $this->setUser($this->teachers[0]);
        $overview = array();
        assign_print_overview(array($this->course->id => $this->course), $overview);
        $this->assertEquals(count($overview), 1);

        $this->setUser($this->editingteachers[0]);
        $overview = array();
        assign_print_overview(array($this->course->id => $this->course), $overview);
        $this->assertEquals(1, count($overview));
    }

    public function test_print_recent_activity() {
        $assign = $this->create_instance();

        $submission = $assign->get_user_submission($this->students[0]->id, true);

        $this->expectOutputRegex('/submitted:/');
        assign_print_recent_activity($this->course, true, time() - 3600);
    }

    public function test_assign_get_recent_mod_activity() {
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
        $assign = $this->create_instance(array('submissiondrafts' => 1));
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id'=>$assign->get_course_module()->id)));

        $submission = $assign->get_user_submission($this->students[0]->id, true);

        $this->expectOutputRegex('/Draft/');
        assign_user_complete($this->course, $this->students[0], $assign->get_course_module(), $assign->get_instance());
    }

    public function test_assign_user_outline() {
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

