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

    protected function setUp() {
        parent::setUp();

        // Add additional default data (some real attempts and stuff).
        $this->setUser($this->editingteachers[0]);
        $this->create_instance();
        $assign = $this->create_instance(array('duedate' => time(),
                                               'attemptreopenmethod' => ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL,
                                               'maxattempts' => 3,
                                               'submissiondrafts' => 1,
                                               'assignsubmission_onlinetext_enabled' => 1));

        // Add a submission.
        $this->setUser($this->students[0]);
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid' => file_get_unused_draft_itemid(),
                                         'text' => 'Submission text',
                                         'format' => FORMAT_HTML);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // And now submit it for marking.
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->students[0]->id, true, false);

        // Mark the submission.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // This is required so that the submissions timemodified > the grade timemodified.
        $this->waitForSecond();

        // Edit the submission again.
        $this->setUser($this->students[0]);
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $assign->testable_update_submission($submission, $this->students[0]->id, true, false);

        // This is required so that the submissions timemodified > the grade timemodified.
        $this->waitForSecond();

        // Allow the student another attempt.
        $this->teachers[0]->ignoresesskey = true;
        $this->setUser($this->teachers[0]);
        $result = $assign->testable_process_add_attempt($this->students[0]->id);
        // Add another submission.
        $this->setUser($this->students[0]);
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid' => file_get_unused_draft_itemid(),
                                         'text' => 'Submission text 2',
                                         'format' => FORMAT_HTML);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // And now submit it for marking (again).
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->students[0]->id, true, false);
    }

    public function test_assign_print_overview() {
        global $DB;

        // Create one more assignment instance.
        $this->setAdminUser();
        $courses = $DB->get_records('course', array('id' => $this->course->id));
        // Past assignments should not show up.
        $pastassign = $this->create_instance(array('duedate' => time(),
                                                   'cutoffdate' => time() - 370000,
                                                   'nosubmissions' => 0,
                                                   'assignsubmission_onlinetext_enabled' => 1));
        // Open assignments should show up only if relevant.
        $openassign = $this->create_instance(array('duedate' => time(),
                                                   'cutoffdate' => time() + 370000,
                                                   'nosubmissions' => 0,
                                                   'assignsubmission_onlinetext_enabled' => 1));
        $pastsubmission = $pastassign->get_user_submission($this->students[0]->id, true);
        $opensubmission = $openassign->get_user_submission($this->students[0]->id, true);

        // Check the overview as the different users.
        // For students , open assignments should show only when there are no valid submissions.
        $this->setUser($this->students[0]);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertEquals(1, count($overview));
        $this->assertRegExp('/.*Assignment 4.*/', $overview[$this->course->id]['assign']); // No valid submission.
        $this->assertNotRegExp('/.*Assignment 1.*/', $overview[$this->course->id]['assign']); // Has valid submission.

        // And now submit the submission.
        $opensubmission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $openassign->testable_update_submission($opensubmission, $this->students[0]->id, true, false);

        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertEquals(0, count($overview));

        $this->setUser($this->teachers[0]);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertEquals(1, count($overview));
        // Submissions without a grade.
        $this->assertRegExp('/.*Assignment 4.*/', $overview[$this->course->id]['assign']);
        $this->assertRegExp('/.*Assignment 2.*/', $overview[$this->course->id]['assign']);

        $this->setUser($this->editingteachers[0]);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertEquals(1, count($overview));
        // Submissions without a grade.
        $this->assertRegExp('/.*Assignment 4.*/', $overview[$this->course->id]['assign']);
        $this->assertRegExp('/.*Assignment 2.*/', $overview[$this->course->id]['assign']);

        // Let us grade a submission.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $openassign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertEquals(1, count($overview));
        // Now assignment 4 should not show up.
        $this->assertNotRegExp('/.*Assignment 4.*/', $overview[$this->course->id]['assign']);
        $this->assertRegExp('/.*Assignment 2.*/', $overview[$this->course->id]['assign']);

        $this->setUser($this->editingteachers[0]);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertEquals(1, count($overview));
        // Now assignment 4 should not show up.
        $this->assertNotRegExp('/.*Assignment 4.*/', $overview[$this->course->id]['assign']);
        $this->assertRegExp('/.*Assignment 2.*/', $overview[$this->course->id]['assign']);

        // Open offline assignments should not show any notification to students.
        $openassign = $this->create_instance(array('duedate' => time(),
                                                   'cutoffdate' => time() + 370000));
        $this->setUser($this->students[0]);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertEquals(0, count($overview));
    }

    public function test_print_recent_activity() {
        // Submitting an assignment generates a notification.
        $this->preventResetByRollback();
        $sink = $this->redirectMessages();

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();
        $data = new stdClass();
        $data->userid = $this->students[0]->id;
        $notices = array();
        $this->setUser($this->students[0]);
        $assign->submit_for_grading($data, $notices);

        $this->setUser($this->editingteachers[0]);
        $this->expectOutputRegex('/submitted:/');
        assign_print_recent_activity($this->course, true, time() - 3600);

        $sink->close();
    }

    /** Make sure fullname dosn't trigger any warnings when assign_print_recent_activity is triggered. */
    public function test_print_recent_activity_fullname() {
        // Submitting an assignment generates a notification.
        $this->preventResetByRollback();
        $sink = $this->redirectMessages();

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $data = new stdClass();
        $data->userid = $this->students[0]->id;
        $notices = array();
        $this->setUser($this->students[0]);
        $assign->submit_for_grading($data, $notices);

        $this->setUser($this->editingteachers[0]);
        $this->expectOutputRegex('/submitted:/');
        set_config('fullnamedisplay', 'firstname, lastnamephonetic');
        assign_print_recent_activity($this->course, false, time() - 3600);

        $sink->close();
    }

    /** Make sure blind marking shows participant \d+ not fullname when assign_print_recent_activity is triggered. */
    public function test_print_recent_activity_fullname_blind_marking() {
        // Submitting an assignment generates a notification in blind marking.
        $this->preventResetByRollback();
        $sink = $this->redirectMessages();

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('blindmarking' => 1));

        $data = new stdClass();
        $data->userid = $this->students[0]->id;
        $notices = array();
        $this->setUser($this->students[0]);
        $assign->submit_for_grading($data, $notices);

        $this->setUser($this->editingteachers[0]);
        $uniqueid = $assign->get_uniqueid_for_user($data->userid);
        $expectedstr = preg_quote(get_string('participant', 'mod_assign'), '/') . '.*' . $uniqueid;
        $this->expectOutputRegex("/{$expectedstr}/");
        assign_print_recent_activity($this->course, false, time() - 3600);

        $sink->close();
    }

    public function test_assign_get_recent_mod_activity() {
        // Submitting an assignment generates a notification.
        $this->preventResetByRollback();
        $sink = $this->redirectMessages();

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $data = new stdClass();
        $data->userid = $this->students[0]->id;
        $notices = array();
        $this->setUser($this->students[0]);
        $assign->submit_for_grading($data, $notices);

        $this->setUser($this->editingteachers[0]);
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
        $sink->close();
    }

    public function test_assign_user_complete() {
        global $PAGE, $DB;

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('submissiondrafts' => 1));
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));

        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
        $DB->update_record('assign_submission', $submission);

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
        $assign = $this->create_instance(array('submissiondrafts' => 0, 'completionsubmit' => 1));

        $this->setUser($this->students[0]);
        $result = assign_get_completion_state($this->course, $assign->get_course_module(), $this->students[0]->id, false);
        $this->assertFalse($result);
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $DB->update_record('assign_submission', $submission);

        $result = assign_get_completion_state($this->course, $assign->get_course_module(), $this->students[0]->id, false);

        $this->assertTrue($result);
    }

    /**
     * Tests for mod_assign_refresh_events.
     */
    public function test_assign_refresh_events() {
        global $DB;
        $duedate = time();
        $this->setAdminUser();

        $assign = $this->create_instance(array('duedate' => $duedate));

        // Normal case, with existing course.
        $this->assertTrue(assign_refresh_events($this->course->id));

        $instance = $assign->get_instance();
        $eventparams = array('modulename' => 'assign', 'instance' => $instance->id);
        $event = $DB->get_record('event', $eventparams, '*', MUST_EXIST);
        $this->assertEquals($event->timestart, $duedate);

        // In case the course ID is passed as a numeric string.
        $this->assertTrue(assign_refresh_events('' . $this->course->id));

        // Course ID not provided.
        $this->assertTrue(assign_refresh_events());

        $eventparams = array('modulename' => 'assign');
        $events = $DB->get_records('event', $eventparams);
        foreach ($events as $event) {
            if ($event->modulename === 'assign' && $event->instance === $instance->id) {
                $this->assertEquals($event->timestart, $duedate);
            }
        }

        // Non-existing course ID.
        $this->assertFalse(assign_refresh_events(-1));

        // Invalid course ID.
        $this->assertFalse(assign_refresh_events('aaa'));
    }

}
