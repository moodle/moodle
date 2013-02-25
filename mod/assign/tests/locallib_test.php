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
 * Unit tests for (some of) mod/assign/locallib.php.
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
class mod_assign_locallib_testcase extends advanced_testcase {

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
    private function createExtraUsers() {
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

    private function create_instance($params=array()) {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $this->course->id;
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);
        return new testable_assign($context, $cm, $this->course);
    }

    public function test_return_links() {
        global $PAGE;
        $this->setUser($this->editingteachers[0]);
        $returnaction = 'RETURNACTION';
        $returnparams = array('param'=>'1');
        $assign = $this->create_instance();
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));
        $assign->register_return_link($returnaction, $returnparams);
        $this->assertEquals($returnaction, $assign->get_return_action());
        $this->assertEquals($returnparams, $assign->get_return_params());
    }

    public function test_get_feedback_plugins() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();
        $this->assertEquals(3, count($assign->get_feedback_plugins()));
    }

    public function test_get_submission_plugins() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();
        $this->assertEquals(3, count($assign->get_submission_plugins()));
    }

    public function test_is_blind_marking() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('blindmarking'=>1));
        $this->assertEquals(true, $assign->is_blind_marking());

        // Test cannot see student names.
        $gradingtable = new assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertEquals(true, strpos($output, get_string('hiddenuser', 'assign')));

        // Test students cannot reveal identities.
        $nopermission = false;
        $this->setUser($this->students[0]);
        $this->setExpectedException('required_capability_exception');
        $assign->testable_process_reveal_identities();

        // Test teachers cannot reveal identities.
        $nopermission = false;
        $this->setUser($this->teachers[0]);
        $this->setExpectedException('required_capability_exception');
        $assign->testable_process_reveal_identities();

        // Test sesskey is required.
        $nosesskey = true;
        $this->setUser($this->editingteachers[0]);
        $this->setExpectedException('moodle_exception');
        $assign->testable_process_reveal_identities();

        // Test editingteacher can reveal identities if sesskey is ignored.
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);
        $assign->testable_process_reveal_identities();
        $this->assertEquals(false, $assign->is_blind_marking());

        // Test student names are visible.
        $gradingtable = new assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertEquals(false, strpos($output, get_string('hiddenuser', 'assign')));

        // Set this back to default.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    public function test_show_intro() {
        // Test whether we are showing the intro at the correct times.
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('alwaysshowdescription'=>1));

        $this->assertEquals(true, $assign->testable_show_intro());

        $tomorrow = time() + (24*60*60);

        $assign = $this->create_instance(array('alwaysshowdescription'=>0,
                                               'allowsubmissionsfromdate'=>$tomorrow));
        $this->assertEquals(false, $assign->testable_show_intro());
        $yesterday = time() - (24*60*60);
        $assign = $this->create_instance(array('alwaysshowdescription'=>0,
                                               'allowsubmissionsfromdate'=>$yesterday));
        $this->assertEquals(true, $assign->testable_show_intro());
    }

    public function test_has_submissions_or_grades() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled'=>1));

        $instance = $assign->get_instance();

        // Should start empty.
        $this->assertEquals(false, $assign->has_submissions_or_grades());

        // Simulate a submission.
        $this->setUser($this->students[0]);
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Now test again.
        $this->assertEquals(true, $assign->has_submissions_or_grades());
        // Set this back to default.
        $this->students[0]->ignoresesskey = false;
    }

    public function test_delete_grades() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id);

        // Now see if the data is in the gradebook.
        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id);

        $this->assertNotEquals(0, count($gradinginfo->items));

        $assign->testable_delete_grades();
        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id);

        $this->assertEquals(0, count($gradinginfo->items));
    }

    public function test_delete_instance() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled'=>1));

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id);

        // Simulate a submission.
        $this->setUser($this->students[0]);
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Now try and delete.
        $this->assertEquals(true, $assign->delete_instance());
    }

    public function test_reset_userdata() {
        global $DB;

        $now = time();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled'=>1,
                                               'duedate'=>$now));

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id);

        // Simulate a submission.
        $this->setUser($this->students[0]);
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        $this->assertEquals(true, $assign->has_submissions_or_grades());
        // Now try and reset.
        $data = new stdClass();
        $data->reset_assign_submissions = 1;
        $data->reset_gradebook_grades = 1;
        $data->courseid = $this->course->id;
        $data->timeshift = 24*60*60;
        $this->setUser($this->editingteachers[0]);
        $assign->reset_userdata($data);
        $this->assertEquals(false, $assign->has_submissions_or_grades());

        // Reload the instance data.
        $instance = $DB->get_record('assign', array('id'=>$assign->get_instance()->id));
        $this->assertEquals($now + 24*60*60, $instance->duedate);
    }

    public function test_plugin_settings() {
        global $DB;

        $now = time();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('assignsubmission_file_enabled'=>1,
                                               'assignsubmission_file_maxfiles'=>12,
                                               'assignsubmission_file_maxsizebytes'=>10));

        $plugin = $assign->get_submission_plugin_by_type('file');
        $this->assertEquals('12', $plugin->get_config('maxfilesubmissions'));
    }

    public function test_update_calendar() {
        global $DB;

        $now = time();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('duedate'=>$now));

        // See if there is an event in the calendar.
        $params = array('modulename'=>'assign', 'instance'=>$assign->get_instance()->id);
        $id = $DB->get_field('event', 'id', $params);

        $this->assertEquals(false, empty($id));
    }

    public function test_update_instance() {
        global $DB;

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled'=>1));

        $now = time();
        $instance = $assign->get_instance();
        $instance->duedate = $now;
        $instance->instance = $instance->id;
        $instance->assignsubmission_onlinetext_enabled = 1;
        $instance->assignsubmission_file_enabled = 0;
        $instance->assignsubmission_comments_enabled = 0;
        $instance->assignfeedback_comments_enabled = 0;
        $instance->assignfeedback_file_enabled = 0;
        $instance->assignfeedback_offline_enabled = 0;

        $assign->update_instance($instance);

        $instance = $DB->get_record('assign', array('id'=>$assign->get_instance()->id));
        $this->assertEquals($now, $instance->duedate);
    }

    public function test_list_participants() {
        $this->createExtraUsers();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('grade'=>100));

        $this->assertEquals(self::DEFAULT_STUDENT_COUNT + self::EXTRA_STUDENT_COUNT, count($assign->list_participants(null, true)));
    }

    public function test_count_teams() {
        $this->createExtraUsers();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('teamsubmission'=>1));

        $this->assertEquals(self::GROUP_COUNT + 1, $assign->count_teams());
    }

    public function test_count_submissions() {
        $this->createExtraUsers();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled'=>1));

        // Simulate a submission.
        $this->setUser($this->extrastudents[0]);
        $submission = $assign->get_user_submission($this->extrastudents[0]->id, true);
        // Leave this one as DRAFT.
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->extrastudents[0]->id);

        // Simulate a submission.
        $this->setUser($this->extrastudents[1]);
        $submission = $assign->get_user_submission($this->extrastudents[1]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->extrastudents[1]->id, true, false);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Simulate a submission.
        $this->setUser($this->extrastudents[2]);
        $submission = $assign->get_user_submission($this->extrastudents[2]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->extrastudents[2]->id, true, false);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Simulate a submission.
        $this->setUser($this->extrastudents[3]);
        $submission = $assign->get_user_submission($this->extrastudents[3]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->extrastudents[3]->id, true, false);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->extrastudents[3]->id);

        $this->assertEquals(2, $assign->count_grades());
        $this->assertEquals(4, $assign->count_submissions());
        $this->assertEquals(2, $assign->count_submissions_need_grading());
        $this->assertEquals(3, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED));
        $this->assertEquals(1, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_DRAFT));
    }

    public function test_get_grading_userid_list() {
        $this->createExtraUsers();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $users = $assign->testable_get_grading_userid_list();
        $this->assertEquals(self::DEFAULT_STUDENT_COUNT + self::EXTRA_STUDENT_COUNT, count($users));
    }

    public function test_cron() {
        // First run cron so there are no messages waiting to be sent (from other tests).
        cron_setup_user();
        assign::cron();

        // Now create an assignment and add some feedback.
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id);

        // Now run cron and see that one message was sent.
        $this->preventResetByRollback();
        $sink = $this->redirectMessages();
        cron_setup_user();
        $this->expectOutputRegex('/Done processing 1 assignment submissions/');
        assign::cron();

        $messages = $sink->get_messages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals(1, $messages[0]->notification);
        $this->assertEquals($assign->get_instance()->name, $messages[0]->contexturlname);
    }

    public function test_is_graded() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id);

        $this->assertEquals(true, $assign->testable_is_graded($this->students[0]->id));
        $this->assertEquals(false, $assign->testable_is_graded($this->students[1]->id));
    }

    public function test_can_view_submission() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $this->setUser($this->students[0]);
        $this->assertEquals(true, $assign->can_view_submission($this->students[0]->id));
        $this->assertEquals(false, $assign->can_view_submission($this->students[1]->id));
        $this->assertEquals(false, $assign->can_view_submission($this->teachers[0]->id));
        $this->setUser($this->teachers[0]);
        $this->assertEquals(true, $assign->can_view_submission($this->students[0]->id));
        $this->assertEquals(true, $assign->can_view_submission($this->students[1]->id));
        $this->assertEquals(true, $assign->can_view_submission($this->teachers[0]->id));
        $this->setUser($this->editingteachers[0]);
        $this->assertEquals(true, $assign->can_view_submission($this->students[0]->id));
        $this->assertEquals(true, $assign->can_view_submission($this->students[1]->id));
        $this->assertEquals(true, $assign->can_view_submission($this->teachers[0]->id));
    }


    public function test_update_submission() {
        $this->createExtraUsers();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $this->setUser($this->extrastudents[0]);
        $now = time();
        $submission = $assign->get_user_submission($this->extrastudents[0]->id, true);
        $assign->testable_update_submission($submission, $this->extrastudents[0]->id, true, false);

        $this->setUser($this->teachers[0]);
        // Verify the gradebook update.
        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id,
                                        $this->extrastudents[0]->id);

        $this->assertEquals($this->extrastudents[0]->id,
                            $gradinginfo->items[0]->grades[$this->extrastudents[0]->id]->usermodified);

        // Now verify group assignments.
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('teamsubmission'=>1));

        $this->setUser($this->extrastudents[0]);
        $now = time();
        $submission = $assign->get_group_submission($this->extrastudents[0]->id, 0, true);
        $assign->testable_update_submission($submission, $this->extrastudents[0]->id, true, true);

        // Check that at least 2 members of the submission group had their submission updated.

        $this->setUser($this->editingteachers[0]);
        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id,
                                        $this->extrastudents[0]->id);

        $this->assertEquals($this->extrastudents[0]->id,
                            $gradinginfo->items[0]->grades[$this->extrastudents[0]->id]->usermodified);

        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id,
                                        $this->extrastudents[self::GROUP_COUNT]->id);

        $this->assertEquals($this->extrastudents[self::GROUP_COUNT]->id,
                            $gradinginfo->items[0]->grades[$this->extrastudents[self::GROUP_COUNT]->id]->usermodified);

        // Now verify blind marking.
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('blindmarking'=>1));

        $this->setUser($this->extrastudents[0]);
        $now = time();
        $submission = $assign->get_user_submission($this->extrastudents[0]->id, true);
        $assign->testable_update_submission($submission, $this->extrastudents[0]->id, true, false);

        $this->setUser($this->editingteachers[0]);
        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id,
                                        $this->extrastudents[0]->id);

        $this->assertEquals(null, $gradinginfo->items[0]->grades[$this->extrastudents[0]->id]->datesubmitted);
    }

    public function test_submissions_open() {
        $this->setUser($this->editingteachers[0]);

        $now = time();
        $tomorrow = $now + 24*60*60;
        $oneweek = $now + 7*24*60*60;
        $yesterday = $now - 24*60*60;

        $assign = $this->create_instance();
        $this->assertEquals(true, $assign->testable_submissions_open($this->students[0]->id));

        $assign = $this->create_instance(array('duedate'=>$tomorrow));
        $this->assertEquals(true, $assign->testable_submissions_open($this->students[0]->id));

        $assign = $this->create_instance(array('duedate'=>$yesterday));
        $this->assertEquals(true, $assign->testable_submissions_open($this->students[0]->id));

        $assign = $this->create_instance(array('duedate'=>$yesterday, 'cutoffdate'=>$tomorrow));
        $this->assertEquals(true, $assign->testable_submissions_open($this->students[0]->id));

        $assign = $this->create_instance(array('duedate'=>$yesterday, 'cutoffdate'=>$yesterday));
        $this->assertEquals(false, $assign->testable_submissions_open($this->students[0]->id));

        $assign->testable_save_user_extension($this->students[0]->id, $tomorrow);
        $this->assertEquals(true, $assign->testable_submissions_open($this->students[0]->id));

        $assign = $this->create_instance(array('submissiondrafts'=>1));
        $this->assertEquals(true, $assign->testable_submissions_open($this->students[0]->id));

        $this->setUser($this->students[0]);
        $now = time();
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->students[0]->id, true, false);
        $this->setUser($this->editingteachers[0]);
        $this->assertEquals(false, $assign->testable_submissions_open($this->students[0]->id));
    }

    public function test_get_graders() {
        $this->createExtraUsers();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $this->assertCount(self::DEFAULT_TEACHER_COUNT +
                           self::DEFAULT_EDITING_TEACHER_COUNT +
                           self::EXTRA_TEACHER_COUNT +
                           self::EXTRA_EDITING_TEACHER_COUNT,
                           $assign->testable_get_graders($this->students[0]->id));

        $assign = $this->create_instance();
        // Force create an assignment with SEPARATEGROUPS.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params = array('course'=>$this->course->id);
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        set_coursemodule_groupmode($cm->id, SEPARATEGROUPS);
        $cm->groupmode = SEPARATEGROUPS;
        $context = context_module::instance($cm->id);
        $assign = new testable_assign($context, $cm, $this->course);

        $this->setUser($this->students[1]);
        $this->assertCount(4, $assign->testable_get_graders($this->students[0]->id));
    }

    public function test_get_uniqueid_for_user() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        foreach ($this->students as $student) {
            $uniqueid = $assign->get_uniqueid_for_user($student->id);
            $this->assertEquals($student->id, $assign->get_user_id_for_uniqueid($uniqueid));
        }
    }

    public function test_show_student_summary() {
        global $CFG, $PAGE;

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));

        // No feedback should be available because this student has not been graded.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertEquals(false, strpos($output, 'Feedback'), 'Do not show feedback if there is no grade');
        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id);

        // Now we should see the feedback.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertNotEquals(false, strpos($output, 'Feedback'), 'Show feedback if there is a grade');

        // Now hide the grade in gradebook.
        $this->setUser($this->teachers[0]);
        require_once($CFG->libdir.'/gradelib.php');
        $gradeitem = new grade_item(array(
            'itemtype'      => 'mod',
            'itemmodule'    => 'assign',
            'iteminstance'  => $assign->get_instance()->id,
            'courseid'      => $this->course->id));

        $gradeitem->set_hidden(1, false);

        // No feedback should be available because the grade is hidden.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertEquals(false, strpos($output, 'Feedback'), 'Do not show feedback if the grade is hidden in the gradebook');

        // Do the same but add feedback.
        $assign = $this->create_instance(array('assignfeedback_comments_enabled' => 1));

        $this->setUser($this->teachers[0]);
        $grade = $assign->get_user_grade($this->students[0]->id, true);
        $data = new stdClass();
        $data->assignfeedbackcomments_editor = array('text'=>'Tomato sauce',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $plugin->save($grade, $data);

        // Should have feedback but no grade.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertNotEquals(false, strpos($output, 'Tomato sauce'), 'Show feedback even if there is no grade');
        $this->assertEquals(false, strpos($output, 'Grade'), 'Do not show grade when there is no grade.');
        $this->assertEquals(false, strpos($output, 'Graded on'), 'Do not show graded date when there is no grade.');
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

    public function testable_apply_grade_to_user($formdata, $userid) {
        return parent::apply_grade_to_user($formdata, $userid);
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
