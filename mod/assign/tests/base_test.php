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
 * Base class for unit tests for mod_assign.
 *
 * @package    mod_assign
 * @category   phpunit
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/assign/upgradelib.php');

/**
 * Unit tests for (some of) mod/assign/locallib.php.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_base_testcase extends advanced_testcase {

    /** @const Default number of students to create */
    const DEFAULT_STUDENT_COUNT = 3;
    /** @const Default number of teachers to create */
    const DEFAULT_TEACHER_COUNT = 2;
    /** @const Default number of editing teachers to create */
    const DEFAULT_EDITING_TEACHER_COUNT = 2;
    /** @const Optional extra number of students to create */
    const EXTRA_STUDENT_COUNT = 40;
    /** @const Optional extra number of teachers to create */
    const EXTRA_TEACHER_COUNT = 5;
    /** @const Optional extra number of editing teachers to create */
    const EXTRA_EDITING_TEACHER_COUNT = 5;
    /** @const Number of groups to create */
    const GROUP_COUNT = 6;

    /** @var stdClass $course New course created to hold the assignments */
    protected $course = null;

    /** @var array $teachers List of DEFAULT_TEACHER_COUNT teachers in the course*/
    protected $teachers = null;

    /** @var array $editingteachers List of DEFAULT_EDITING_TEACHER_COUNT editing teachers in the course */
    protected $editingteachers = null;

    /** @var array $students List of DEFAULT_STUDENT_COUNT students in the course*/
    protected $students = null;

    /** @var array $extrateachers List of EXTRA_TEACHER_COUNT teachers in the course*/
    protected $extrateachers = null;

    /** @var array $extraeditingteachers List of EXTRA_EDITING_TEACHER_COUNT editing teachers in the course*/
    protected $extraeditingteachers = null;

    /** @var array $extrastudents List of EXTRA_STUDENT_COUNT students in the course*/
    protected $extrastudents = null;

    /** @var array $groups List of 10 groups in the course */
    protected $groups = null;

    /**
     * Setup function - we will create a course and add an assign instance to it.
     */
    protected function setUp() {
        global $DB;

        $this->resetAfterTest(true);

        $this->course = $this->getDataGenerator()->create_course();
        $this->teachers = array();
        for ($i = 0; $i < self::DEFAULT_TEACHER_COUNT; $i++) {
            array_push($this->teachers, $this->getDataGenerator()->create_user());
        }

        $this->editingteachers = array();
        for ($i = 0; $i < self::DEFAULT_EDITING_TEACHER_COUNT; $i++) {
            array_push($this->editingteachers, $this->getDataGenerator()->create_user());
        }

        $this->students = array();
        for ($i = 0; $i < self::DEFAULT_STUDENT_COUNT; $i++) {
            array_push($this->students, $this->getDataGenerator()->create_user());
        }

        $this->groups = array();
        for ($i = 0; $i < self::GROUP_COUNT; $i++) {
            array_push($this->groups, $this->getDataGenerator()->create_group(array('courseid'=>$this->course->id)));
        }

        $teacherrole = $DB->get_record('role', array('shortname'=>'teacher'));
        foreach ($this->teachers as $i => $teacher) {
            $this->getDataGenerator()->enrol_user($teacher->id,
                                                  $this->course->id,
                                                  $teacherrole->id);
            groups_add_member($this->groups[$i % self::GROUP_COUNT], $teacher);
        }

        $editingteacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'));
        foreach ($this->editingteachers as $i => $editingteacher) {
            $this->getDataGenerator()->enrol_user($editingteacher->id,
                                                  $this->course->id,
                                                  $editingteacherrole->id);
            groups_add_member($this->groups[$i % self::GROUP_COUNT], $editingteacher);
        }

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        foreach ($this->students as $i => $student) {
            $this->getDataGenerator()->enrol_user($student->id,
                                                  $this->course->id,
                                                  $studentrole->id);
            groups_add_member($this->groups[$i % self::GROUP_COUNT], $student);
        }
    }

    /*
     * For tests that make sense to use alot of data, create extra students/teachers.
     */
    protected function create_extra_users() {
        global $DB;
        $this->extrateachers = array();
        for ($i = 0; $i < self::EXTRA_TEACHER_COUNT; $i++) {
            array_push($this->extrateachers, $this->getDataGenerator()->create_user());
        }

        $this->extraeditingteachers = array();
        for ($i = 0; $i < self::EXTRA_EDITING_TEACHER_COUNT; $i++) {
            array_push($this->extraeditingteachers, $this->getDataGenerator()->create_user());
        }

        $this->extrastudents = array();
        for ($i = 0; $i < self::EXTRA_STUDENT_COUNT; $i++) {
            array_push($this->extrastudents, $this->getDataGenerator()->create_user());
        }

        $teacherrole = $DB->get_record('role', array('shortname'=>'teacher'));
        foreach ($this->extrateachers as $i => $teacher) {
            $this->getDataGenerator()->enrol_user($teacher->id,
                                                  $this->course->id,
                                                  $teacherrole->id);
            groups_add_member($this->groups[$i % self::GROUP_COUNT], $teacher);
        }

        $editingteacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'));
        foreach ($this->extraeditingteachers as $i => $editingteacher) {
            $this->getDataGenerator()->enrol_user($editingteacher->id,
                                                  $this->course->id,
                                                  $editingteacherrole->id);
            groups_add_member($this->groups[$i % self::GROUP_COUNT], $editingteacher);
        }

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        foreach ($this->extrastudents as $i => $student) {
            $this->getDataGenerator()->enrol_user($student->id,
                                                  $this->course->id,
                                                  $studentrole->id);
            if ($i < (self::EXTRA_STUDENT_COUNT / 2)) {
                groups_add_member($this->groups[$i % self::GROUP_COUNT], $student);
            }
        }

    }

    /**
     * Convenience function to create a testable instance of an assignment.
     *
     * @param array $params Array of parameters to pass to the generator
     * @return testable_assign Testable wrapper around the assign class.
     */
    protected function create_instance($params=array()) {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $this->course->id;
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);
        return new testable_assign($context, $cm, $this->course);
    }

    public function test_create_instance() {
        $this->assertNotEmpty($this->create_instance());
    }

}

/**
 * Test subclass that makes all the protected methods we want to test public.
 */
class testable_assign extends assign {

    public function testable_process_reveal_identities() {
        return parent::process_reveal_identities();
    }

    public function testable_show_intro() {
        return parent::show_intro();
    }

    public function testable_delete_grades() {
        return parent::delete_grades();
    }

    public function testable_apply_grade_to_user($formdata, $userid, $attemptnumber) {
        return parent::apply_grade_to_user($formdata, $userid, $attemptnumber);
    }

    public function testable_get_grading_userid_list() {
        return parent::get_grading_userid_list();
    }

    public function testable_is_graded($userid) {
        return parent::is_graded($userid);
    }

    public function testable_update_submission(stdClass $submission, $userid, $updatetime, $teamsubmission) {
        return parent::update_submission($submission, $userid, $updatetime, $teamsubmission);
    }

    public function testable_process_add_attempt($userid = 0) {
        return parent::process_add_attempt($userid);
    }

    public function testable_submissions_open($userid = 0) {
        return parent::submissions_open($userid);
    }

    public function testable_save_user_extension($userid, $extensionduedate) {
        return parent::save_user_extension($userid, $extensionduedate);
    }

    public function testable_get_graders($userid) {
        // Changed method from protected to public.
        return parent::get_graders($userid);
    }
}
