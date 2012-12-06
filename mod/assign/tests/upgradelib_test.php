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
 * Unit tests for (some of) mod/assign/upgradelib.php.
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
require_once($CFG->dirroot . '/mod/assignment/lib.php');

/**
 * Unit tests for (some of) mod/assign/upgradelib.php.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_upgradelib_testcase extends advanced_testcase {

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
     * Setup function - we will create a course and add users to it.
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

    public function test_upgrade_upload_assignment() {
        global $DB;

        $this->setUser($this->editingteachers[0]);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assignment');
        $params = array('course'=>$this->course->id,
                        'assignmenttype'=>'upload');
        $record = $generator->create_instance($params);

        $assignment = new assignment_base($record->cmid);

        $this->setAdminUser();
        $log = '';
        $upgrader = new assign_upgrade_manager();

        $this->assertTrue($upgrader->upgrade_assignment($assignment->assignment->id, $log));
        $record = $DB->get_record('assign', array('course'=>$this->course->id));

        $cm = get_coursemodule_from_instance('assign', $record->id);
        $context = context_module::instance($cm->id);

        $assign = new assign($context, $cm, $this->course);

        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('file');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('file');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('offline');
        $this->assertEmpty($plugin->is_enabled());

        $assign->delete_instance();
        delete_course_module($cm->id);
        delete_mod_from_section($cm->id, $cm->section);
    }

    public function test_upgrade_uploadsingle_assignment() {
        global $DB;

        $this->setUser($this->editingteachers[0]);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assignment');
        $params = array('course'=>$this->course->id,
                        'assignmenttype'=>'uploadsingle');
        $record = $generator->create_instance($params);

        $assignment = new assignment_base($record->cmid);

        $this->setAdminUser();
        $log = '';
        $upgrader = new assign_upgrade_manager();

        $this->assertTrue($upgrader->upgrade_assignment($assignment->assignment->id, $log));
        $record = $DB->get_record('assign', array('course'=>$this->course->id));

        $cm = get_coursemodule_from_instance('assign', $record->id);
        $context = context_module::instance($cm->id);

        $assign = new assign($context, $cm, $this->course);

        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('file');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('file');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('offline');
        $this->assertEmpty($plugin->is_enabled());

        $assign->delete_instance();
        delete_course_module($cm->id);
        delete_mod_from_section($cm->id, $cm->section);
    }

    public function test_upgrade_onlinetext_assignment() {
        global $DB;

        $this->setUser($this->editingteachers[0]);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assignment');
        $params = array('course'=>$this->course->id,
                        'assignmenttype'=>'online');
        $record = $generator->create_instance($params);

        $assignment = new assignment_base($record->cmid);

        $this->setAdminUser();
        $log = '';
        $upgrader = new assign_upgrade_manager();

        $this->assertTrue($upgrader->upgrade_assignment($assignment->assignment->id, $log));
        $record = $DB->get_record('assign', array('course'=>$this->course->id));

        $cm = get_coursemodule_from_instance('assign', $record->id);
        $context = context_module::instance($cm->id);

        $assign = new assign($context, $cm, $this->course);

        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('file');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('file');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('offline');
        $this->assertEmpty($plugin->is_enabled());

        $assign->delete_instance();
        delete_course_module($cm->id);
        delete_mod_from_section($cm->id, $cm->section);
    }

    public function test_upgrade_offline_assignment() {
        global $DB;

        $this->setUser($this->editingteachers[0]);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assignment');
        $params = array('course'=>$this->course->id,
                        'assignmenttype'=>'offline');
        $record = $generator->create_instance($params);

        $assignment = new assignment_base($record->cmid);

        $this->setAdminUser();
        $log = '';
        $upgrader = new assign_upgrade_manager();

        $this->assertTrue($upgrader->upgrade_assignment($assignment->assignment->id, $log));
        $record = $DB->get_record('assign', array('course'=>$this->course->id));

        $cm = get_coursemodule_from_instance('assign', $record->id);
        $context = context_module::instance($cm->id);

        $assign = new assign($context, $cm, $this->course);

        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('file');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('file');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('offline');
        $this->assertEmpty($plugin->is_enabled());

        $assign->delete_instance();
        delete_course_module($cm->id);
        delete_mod_from_section($cm->id, $cm->section);
    }
}
