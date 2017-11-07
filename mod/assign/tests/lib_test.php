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

use \core_calendar\local\api as calendar_local_api;
use \core_calendar\local\event\container as calendar_event_container;

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
        $pastassign = $this->create_instance(array('duedate' => time() - 370001,
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
        $this->assertDebuggingCalledCount(3);
        $this->assertEquals(1, count($overview));
        $this->assertRegExp('/.*Assignment 4.*/', $overview[$this->course->id]['assign']); // No valid submission.
        $this->assertNotRegExp('/.*Assignment 1.*/', $overview[$this->course->id]['assign']); // Has valid submission.

        // And now submit the submission.
        $opensubmission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $openassign->testable_update_submission($opensubmission, $this->students[0]->id, true, false);

        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertDebuggingCalledCount(3);
        $this->assertEquals(0, count($overview));

        $this->setUser($this->teachers[0]);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertDebuggingCalledCount(3);
        $this->assertEquals(1, count($overview));
        // Submissions without a grade.
        $this->assertRegExp('/.*Assignment 4.*/', $overview[$this->course->id]['assign']);
        $this->assertRegExp('/.*Assignment 2.*/', $overview[$this->course->id]['assign']);

        $this->setUser($this->editingteachers[0]);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertDebuggingCalledCount(3);
        $this->assertEquals(1, count($overview));
        // Submissions without a grade.
        $this->assertRegExp('/.*Assignment 4.*/', $overview[$this->course->id]['assign']);
        $this->assertRegExp('/.*Assignment 2.*/', $overview[$this->course->id]['assign']);

        // Let us grade a submission.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $openassign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // The assign_print_overview expects the grade date to be after the submission date.
        $graderecord = $DB->get_record('assign_grades', array('assignment' => $openassign->get_instance()->id,
            'userid' => $this->students[0]->id, 'attemptnumber' => 0));
        $graderecord->timemodified += 1;
        $DB->update_record('assign_grades', $graderecord);

        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertDebuggingCalledCount(3);
        $this->assertEquals(1, count($overview));
        // Now assignment 4 should not show up.
        $this->assertNotRegExp('/.*Assignment 4.*/', $overview[$this->course->id]['assign']);
        $this->assertRegExp('/.*Assignment 2.*/', $overview[$this->course->id]['assign']);

        $this->setUser($this->editingteachers[0]);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertDebuggingCalledCount(3);
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
        $this->assertDebuggingCalledCount(4);
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
        $newduedate = $duedate + DAYSECS;
        $this->setAdminUser();

        $assign = $this->create_instance(['duedate' => $duedate]);

        // Make sure the calendar event for assignment 1 matches the initial due date.
        $instance = $assign->get_instance();
        $eventparams = ['modulename' => 'assign', 'instance' => $instance->id];
        $eventtime = $DB->get_field('event', 'timestart', $eventparams, MUST_EXIST);
        $this->assertEquals($eventtime, $duedate);

        // Manually update assignment 1's due date.
        $DB->update_record('assign', (object)['id' => $instance->id, 'duedate' => $newduedate]);

        // Then refresh the assignment events of assignment 1's course.
        $this->assertTrue(assign_refresh_events($this->course->id));

        // Confirm that the assignment 1's due date event now has the new due date after refresh.
        $eventtime = $DB->get_field('event', 'timestart', $eventparams, MUST_EXIST);
        $this->assertEquals($eventtime, $newduedate);

        // Create a second course and assignment.
        $generator = $this->getDataGenerator();
        $course2 = $generator->create_course();
        $assign2 = $this->create_instance(['duedate' => $duedate, 'course' => $course2->id]);
        $instance2 = $assign2->get_instance();

        // Manually update assignment 1 and 2's due dates.
        $newduedate += DAYSECS;
        $DB->update_record('assign', (object)['id' => $instance->id, 'duedate' => $newduedate]);
        $DB->update_record('assign', (object)['id' => $instance2->id, 'duedate' => $newduedate]);

        // Refresh events of all courses.
        $this->assertTrue(assign_refresh_events());

        // Check the due date calendar event for assignment 1.
        $eventtime = $DB->get_field('event', 'timestart', $eventparams, MUST_EXIST);
        $this->assertEquals($eventtime, $newduedate);

        // Check the due date calendar event for assignment 2.
        $eventparams['instance'] = $instance2->id;
        $eventtime = $DB->get_field('event', 'timestart', $eventparams, MUST_EXIST);
        $this->assertEquals($eventtime, $newduedate);

        // In case the course ID is passed as a numeric string.
        $this->assertTrue(assign_refresh_events('' . $this->course->id));

        // Non-existing course ID.
        $this->assertFalse(assign_refresh_events(-1));

        // Invalid course ID.
        $this->assertFalse(assign_refresh_events('aaa'));
    }

    public function test_assign_core_calendar_is_event_visible_duedate_event_as_teacher() {
        $this->setAdminUser();

        // Create an assignment.
        $assign = $this->create_instance();

        // Create a calendar event.
        $event = $this->create_action_event($assign->get_instance()->id, ASSIGN_EVENT_TYPE_DUE);

        // Set the user to a teacher.
        $this->setUser($this->editingteachers[0]);

        // The teacher should see the due date event.
        $this->assertTrue(mod_assign_core_calendar_is_event_visible($event));
    }

    public function test_assign_core_calendar_is_event_visible_duedate_event_as_student() {
        $this->setAdminUser();

        // Create an assignment.
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled' => 1));

        // Create a calendar event.
        $event = $this->create_action_event($assign->get_instance()->id, ASSIGN_EVENT_TYPE_DUE);

        // Set the user to a student.
        $this->setUser($this->students[0]);

        // The student should care about the due date event.
        $this->assertTrue(mod_assign_core_calendar_is_event_visible($event));
    }

    public function test_assign_core_calendar_is_event_visible_gradingduedate_event_as_teacher() {
        $this->setAdminUser();

        // Create an assignment.
        $assign = $this->create_instance();

        // Create a calendar event.
        $event = $this->create_action_event($assign->get_instance()->id, ASSIGN_EVENT_TYPE_GRADINGDUE);

        // Set the user to a teacher.
        $this->setUser($this->editingteachers[0]);

        // The teacher should care about the grading due date event.
        $this->assertTrue(mod_assign_core_calendar_is_event_visible($event));
    }

    public function test_assign_core_calendar_is_event_visible_gradingduedate_event_as_student() {
        $this->setAdminUser();

        // Create an assignment.
        $assign = $this->create_instance();

        // Create a calendar event.
        $event = $this->create_action_event($assign->get_instance()->id, ASSIGN_EVENT_TYPE_GRADINGDUE);

        // Set the user to a student.
        $this->setUser($this->students[0]);

        // The student should not care about the grading due date event.
        $this->assertFalse(mod_assign_core_calendar_is_event_visible($event));
    }

    public function test_assign_core_calendar_provide_event_action_duedate_as_teacher() {
        $this->setAdminUser();

        // Create an assignment.
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled' => 1));

        // Create a calendar event.
        $event = $this->create_action_event($assign->get_instance()->id, ASSIGN_EVENT_TYPE_DUE);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Set the user to a teacher.
        $this->setUser($this->teachers[0]);

        // Decorate action event.
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory);

        // The teacher should not have an action for a due date event.
        $this->assertNull($actionevent);
    }

    public function test_assign_core_calendar_provide_event_action_duedate_as_student() {
        $this->setAdminUser();

        // Create an assignment.
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled' => 1));

        // Create a calendar event.
        $event = $this->create_action_event($assign->get_instance()->id, ASSIGN_EVENT_TYPE_DUE);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Set the user to a student.
        $this->setUser($this->students[0]);

        // Decorate action event.
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('addsubmission', 'assign'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_assign_core_calendar_provide_event_action_gradingduedate_as_teacher() {
        $this->setAdminUser();

        // Create an assignment.
        $assign = $this->create_instance();

        // Create a calendar event.
        $event = $this->create_action_event($assign->get_instance()->id, ASSIGN_EVENT_TYPE_GRADINGDUE);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Set the user to a teacher.
        $this->setUser($this->editingteachers[0]);

        // Decorate action event.
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('grade'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(0, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_assign_core_calendar_provide_event_action_gradingduedate_as_student() {
        $this->setAdminUser();

        // Create an assignment.
        $assign = $this->create_instance();

        // Create a calendar event.
        $event = $this->create_action_event($assign->get_instance()->id, ASSIGN_EVENT_TYPE_GRADINGDUE);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Set the user to a student.
        $this->setUser($this->students[0]);

        // Decorate action event.
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('grade'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(0, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    public function test_assign_core_calendar_provide_event_action_duedate_as_student_submitted() {
        $this->setAdminUser();

        // Create an assignment.
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled' => 1));

        // Create a calendar event.
        $event = $this->create_action_event($assign->get_instance()->id, ASSIGN_EVENT_TYPE_DUE);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Set the user to a student.
        $this->setUser($this->students[0]);

        // Submit the assignment.
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->students[0]->id, true, false);
        $data = (object) [
            'userid' => $this->students[0]->id,
            'onlinetext_editor' => [
                'itemid' => file_get_unused_draft_itemid(),
                'text' => 'Submission text',
                'format' => FORMAT_MOODLE,
            ],
        ];
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory);

        // Confirm there was no event to action.
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param int $instanceid The assign id.
     * @param string $eventtype The event type. eg. ASSIGN_EVENT_TYPE_DUE.
     * @return bool|calendar_event
     */
    private function create_action_event($instanceid, $eventtype) {
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'assign';
        $event->courseid = $this->course->id;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return calendar_event::create($event);
    }

    /**
     * Test the callback responsible for returning the completion rule descriptions.
     * This function should work given either an instance of the module (cm_info), such as when checking the active rules,
     * or if passed a stdClass of similar structure, such as when checking the the default completion settings for a mod type.
     */
    public function test_mod_assign_completion_get_active_rule_descriptions() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Two activities, both with automatic completion. One has the 'completionsubmit' rule, one doesn't.
        $cm1 = $this->create_instance(['completion' => '2', 'completionsubmit' => '1'])->get_course_module();
        $cm2 = $this->create_instance(['completion' => '2', 'completionsubmit' => '0'])->get_course_module();

        // Data for the stdClass input type.
        // This type of input would occur when checking the default completion rules for an activity type, where we don't have
        // any access to cm_info, rather the input is a stdClass containing completion and customdata attributes, just like cm_info.
        $moddefaults = new stdClass();
        $moddefaults->customdata = ['customcompletionrules' => ['completionsubmit' => '1']];
        $moddefaults->completion = 2;

        $activeruledescriptions = [get_string('completionsubmit', 'assign')];
        $this->assertEquals(mod_assign_get_completion_active_rule_descriptions($cm1), $activeruledescriptions);
        $this->assertEquals(mod_assign_get_completion_active_rule_descriptions($cm2), []);
        $this->assertEquals(mod_assign_get_completion_active_rule_descriptions($moddefaults), $activeruledescriptions);
        $this->assertEquals(mod_assign_get_completion_active_rule_descriptions(new stdClass()), []);
    }

    /**
     * Test that if some grades are not set, they are left alone and not rescaled
     */
    public function test_assign_rescale_activity_grades_some_unset() {
        $this->resetAfterTest();

        // As a teacher...
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        // Grade the student.
        $data = ['grade' => 50];
        $assign->testable_apply_grade_to_user((object)$data, $this->students[0]->id, 0);

        // Try getting another students grade. This will give a grade of ASSIGN_GRADE_NOT_SET (-1).
        $assign->get_user_grade($this->students[1]->id, true);

        // Rescale.
        assign_rescale_activity_grades($this->course, $assign->get_course_module(), 0, 100, 0, 10);

        // Get the grades for both students.
        $student0grade = $assign->get_user_grade($this->students[0]->id, true);
        $student1grade = $assign->get_user_grade($this->students[1]->id, true);

        // Make sure the real grade is scaled, but the ASSIGN_GRADE_NOT_SET stays the same.
        $this->assertEquals($student0grade->grade, 5);
        $this->assertEquals($student1grade->grade, ASSIGN_GRADE_NOT_SET);
    }

    /**
     * Return false when there are not overrides for this assign instance.
     */
    public function test_assign_is_override_calendar_event_no_override() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $userid = 1234;
        $duedate = time();
        $assign = $this->create_instance(['duedate' => $duedate]);

        $instance = $assign->get_instance();
        $event = new \calendar_event((object)[
            'modulename' => 'assign',
            'instance' => $instance->id,
            'userid' => $userid
        ]);

        $this->assertFalse($assign->is_override_calendar_event($event));
    }

    /**
     * Return false if the given event isn't an assign module event.
     */
    public function test_assign_is_override_calendar_event_no_nodule_event() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $userid = $this->students[0]->id;
        $duedate = time();
        $assign = $this->create_instance(['duedate' => $duedate]);

        $instance = $assign->get_instance();
        $event = new \calendar_event((object)[
            'userid' => $userid
        ]);

        $this->assertFalse($assign->is_override_calendar_event($event));
    }

    /**
     * Return false if there is overrides for this use but they belong to another assign
     * instance.
     */
    public function test_assign_is_override_calendar_event_different_assign_instance() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $userid = 1234;
        $duedate = time();
        $assign = $this->create_instance(['duedate' => $duedate]);
        $assign2 = $this->create_instance(['duedate' => $duedate]);

        $instance = $assign->get_instance();
        $event = new \calendar_event((object) [
            'modulename' => 'assign',
            'instance' => $instance->id,
            'userid' => $userid
        ]);

        $record = (object) [
            'assignid' => $assign2->get_instance()->id,
            'userid' => $userid
        ];

        $DB->insert_record('assign_overrides', $record);

        $this->assertFalse($assign->is_override_calendar_event($event));
    }

    /**
     * Return true if there is a user override for this event and assign instance.
     */
    public function test_assign_is_override_calendar_event_user_override() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $userid = 1234;
        $duedate = time();
        $assign = $this->create_instance(['duedate' => $duedate]);

        $instance = $assign->get_instance();
        $event = new \calendar_event((object) [
            'modulename' => 'assign',
            'instance' => $instance->id,
            'userid' => $userid
        ]);

        $record = (object) [
            'assignid' => $instance->id,
            'userid' => $userid
        ];

        $DB->insert_record('assign_overrides', $record);

        $this->assertTrue($assign->is_override_calendar_event($event));
    }

    /**
     * Return true if there is a group override for the event and assign instance.
     */
    public function test_assign_is_override_calendar_event_group_override() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $duedate = time();
        $assign = $this->create_instance(['duedate' => $duedate]);
        $instance = $assign->get_instance();
        $group = $this->getDataGenerator()->create_group(array('courseid' => $instance->course));
        $groupid = $group->id;

        $event = new \calendar_event((object) [
            'modulename' => 'assign',
            'instance' => $instance->id,
            'groupid' => $groupid
        ]);

        $record = (object) [
            'assignid' => $instance->id,
            'groupid' => $groupid
        ];

        $DB->insert_record('assign_overrides', $record);

        $this->assertTrue($assign->is_override_calendar_event($event));
    }

    /**
     * Unknown event types should not have any limit restrictions returned.
     */
    public function test_mod_assign_core_calendar_get_valid_event_timestart_range_unkown_event_type() {
        global $CFG;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $duedate = time();
        $assign = $this->create_instance(['duedate' => $duedate]);
        $instance = $assign->get_instance();

        $event = new \calendar_event((object) [
            'courseid' => $instance->course,
            'modulename' => 'assign',
            'instance' => $instance->id,
            'eventtype' => 'SOME RANDOM EVENT'
        ]);

        list($min, $max) = mod_assign_core_calendar_get_valid_event_timestart_range($event, $instance);
        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * Override events should not have any limit restrictions returned.
     */
    public function test_mod_assign_core_calendar_get_valid_event_timestart_range_override_event() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $duedate = time();
        $assign = $this->create_instance(['duedate' => $duedate]);
        $instance = $assign->get_instance();
        $userid = $this->students[0]->id;

        $event = new \calendar_event((object) [
            'courseid' => $instance->course,
            'modulename' => 'assign',
            'instance' => $instance->id,
            'userid' => $userid,
            'eventtype' => ASSIGN_EVENT_TYPE_DUE
        ]);

        $record = (object) [
            'assignid' => $instance->id,
            'userid' => $userid
        ];

        $DB->insert_record('assign_overrides', $record);

        list($min, $max) = mod_assign_core_calendar_get_valid_event_timestart_range($event, $instance);
        $this->assertFalse($min);
        $this->assertFalse($max);
    }

    /**
     * Assignments configured without a submissions from and cutoff date should not have
     * any limits applied.
     */
    public function test_mod_assign_core_calendar_get_valid_event_timestart_range_due_no_limit() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $duedate = time();
        $assign = $this->create_instance([
            'duedate' => $duedate,
            'allowsubmissionsfromdate' => 0,
            'cutoffdate' => 0,
        ]);
        $instance = $assign->get_instance();
        $userid = $this->students[0]->id;

        $event = new \calendar_event((object) [
            'courseid' => $instance->course,
            'modulename' => 'assign',
            'instance' => $instance->id,
            'eventtype' => ASSIGN_EVENT_TYPE_DUE
        ]);

        list($min, $max) = mod_assign_core_calendar_get_valid_event_timestart_range($event, $instance);
        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * Assignments should be bottom and top bound by the submissions from date and cutoff date
     * respectively.
     */
    public function test_mod_assign_core_calendar_get_valid_event_timestart_range_due_with_limits() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $duedate = time();
        $submissionsfromdate = $duedate - DAYSECS;
        $cutoffdate = $duedate + DAYSECS;
        $assign = $this->create_instance([
            'duedate' => $duedate,
            'allowsubmissionsfromdate' => $submissionsfromdate,
            'cutoffdate' => $cutoffdate,
        ]);
        $instance = $assign->get_instance();
        $userid = $this->students[0]->id;

        $event = new \calendar_event((object) [
            'courseid' => $instance->course,
            'modulename' => 'assign',
            'instance' => $instance->id,
            'eventtype' => ASSIGN_EVENT_TYPE_DUE
        ]);

        list($min, $max) = mod_assign_core_calendar_get_valid_event_timestart_range($event, $instance);
        $this->assertEquals($submissionsfromdate, $min[0]);
        $this->assertNotEmpty($min[1]);
        $this->assertEquals($cutoffdate, $max[0]);
        $this->assertNotEmpty($max[1]);
    }

    /**
     * Assignment grading due date should not have any limits of no due date and cutoff date is set.
     */
    public function test_mod_assign_core_calendar_get_valid_event_timestart_range_gradingdue_no_limit() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $assign = $this->create_instance([
            'duedate' => 0,
            'allowsubmissionsfromdate' => 0,
            'cutoffdate' => 0,
        ]);
        $instance = $assign->get_instance();

        $event = new \calendar_event((object) [
            'courseid' => $instance->course,
            'modulename' => 'assign',
            'instance' => $instance->id,
            'eventtype' => ASSIGN_EVENT_TYPE_GRADINGDUE
        ]);

        list($min, $max) = mod_assign_core_calendar_get_valid_event_timestart_range($event, $instance);
        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * Assignment grading due event is minimum bound by the due date, if it is set.
     */
    public function test_mod_assign_core_calendar_get_valid_event_timestart_range_gradingdue_with_due_date() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $duedate = time();
        $assign = $this->create_instance([
            'duedate' => $duedate
        ]);
        $instance = $assign->get_instance();
        $userid = $this->students[0]->id;

        $event = new \calendar_event((object) [
            'courseid' => $instance->course,
            'modulename' => 'assign',
            'instance' => $instance->id,
            'eventtype' => ASSIGN_EVENT_TYPE_GRADINGDUE
        ]);

        list($min, $max) = mod_assign_core_calendar_get_valid_event_timestart_range($event, $instance);
        $this->assertEquals($duedate, $min[0]);
        $this->assertNotEmpty($min[1]);
        $this->assertNull($max);
    }

    /**
     * Non due date events should not update the assignment due date.
     */
    public function test_mod_assign_core_calendar_event_timestart_updated_non_due_event() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $duedate = time();
        $submissionsfromdate = $duedate - DAYSECS;
        $cutoffdate = $duedate + DAYSECS;
        $assign = $this->create_instance([
            'duedate' => $duedate,
            'allowsubmissionsfromdate' => $submissionsfromdate,
            'cutoffdate' => $cutoffdate,
        ]);
        $instance = $assign->get_instance();

        $event = new \calendar_event((object) [
            'courseid' => $instance->course,
            'modulename' => 'assign',
            'instance' => $instance->id,
            'eventtype' => ASSIGN_EVENT_TYPE_GRADINGDUE,
            'timestart' => $duedate + 1
        ]);

        mod_assign_core_calendar_event_timestart_updated($event, $instance);

        $newinstance = $DB->get_record('assign', ['id' => $instance->id]);
        $this->assertEquals($duedate, $newinstance->duedate);
    }

    /**
     * Due date override events should not change the assignment due date.
     */
    public function test_mod_assign_core_calendar_event_timestart_updated_due_event_override() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $duedate = time();
        $submissionsfromdate = $duedate - DAYSECS;
        $cutoffdate = $duedate + DAYSECS;
        $assign = $this->create_instance([
            'duedate' => $duedate,
            'allowsubmissionsfromdate' => $submissionsfromdate,
            'cutoffdate' => $cutoffdate,
        ]);
        $instance = $assign->get_instance();
        $userid = $this->students[0]->id;

        $event = new \calendar_event((object) [
            'courseid' => $instance->course,
            'modulename' => 'assign',
            'instance' => $instance->id,
            'userid' => $userid,
            'eventtype' => ASSIGN_EVENT_TYPE_DUE,
            'timestart' => $duedate + 1
        ]);

        $record = (object) [
            'assignid' => $instance->id,
            'userid' => $userid,
            'duedate' => $duedate + 1
        ];

        $DB->insert_record('assign_overrides', $record);

        mod_assign_core_calendar_event_timestart_updated($event, $instance);

        $newinstance = $DB->get_record('assign', ['id' => $instance->id]);
        $this->assertEquals($duedate, $newinstance->duedate);
    }

    /**
     * Due date events should update the assignment due date.
     */
    public function test_mod_assign_core_calendar_event_timestart_updated_due_event() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $duedate = time();
        $newduedate = $duedate + 1;
        $submissionsfromdate = $duedate - DAYSECS;
        $cutoffdate = $duedate + DAYSECS;
        $assign = $this->create_instance([
            'duedate' => $duedate,
            'allowsubmissionsfromdate' => $submissionsfromdate,
            'cutoffdate' => $cutoffdate,
        ]);
        $instance = $assign->get_instance();

        $event = new \calendar_event((object) [
            'courseid' => $instance->course,
            'modulename' => 'assign',
            'instance' => $instance->id,
            'eventtype' => ASSIGN_EVENT_TYPE_DUE,
            'timestart' => $newduedate
        ]);

        mod_assign_core_calendar_event_timestart_updated($event, $instance);

        $newinstance = $DB->get_record('assign', ['id' => $instance->id]);
        $this->assertEquals($newduedate, $newinstance->duedate);
    }

    /**
     * If a student somehow finds a way to update the due date calendar event
     * then the callback should not be executed to update the assignment due
     * date as well otherwise that would be a security issue.
     */
    public function test_student_role_cant_update_due_event() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $mapper = calendar_event_container::get_event_mapper();
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = context_course::instance($course->id);
        $roleid = $generator->create_role();
        $now = time();
        $duedate = (new DateTime())->setTimestamp($now);
        $newduedate = (new DateTime())->setTimestamp($now)->modify('+1 day');
        $assign = $this->create_instance([
            'course' => $course->id,
            'duedate' => $duedate->getTimestamp(),
        ]);
        $instance = $assign->get_instance();

        $generator->enrol_user($user->id, $course->id, 'student');
        $generator->role_assign($roleid, $user->id, $context->id);

        $record = $DB->get_record('event', [
            'courseid' => $course->id,
            'modulename' => 'assign',
            'instance' => $instance->id,
            'eventtype' => ASSIGN_EVENT_TYPE_DUE
        ]);

        $event = new \calendar_event($record);

        assign_capability('moodle/calendar:manageentries', CAP_ALLOW, $roleid, $context, true);
        assign_capability('moodle/course:manageactivities', CAP_PROHIBIT, $roleid, $context, true);

        $this->setUser($user);

        calendar_local_api::update_event_start_day(
            $mapper->from_legacy_event_to_event($event),
            $newduedate
        );

        $newinstance = $DB->get_record('assign', ['id' => $instance->id]);
        $newevent = \calendar_event::load($event->id);
        // The due date shouldn't have changed even though we updated the calendar
        // event.
        $this->assertEquals($duedate->getTimestamp(), $newinstance->duedate);
        $this->assertEquals($newduedate->getTimestamp(), $newevent->timestart);
    }

    /**
     * A teacher with the capability to modify an assignment module should be
     * able to update the assignment due date by changing the due date calendar
     * event.
     */
    public function test_teacher_role_can_update_due_event() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $mapper = calendar_event_container::get_event_mapper();
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = context_course::instance($course->id);
        $roleid = $generator->create_role();
        $now = time();
        $duedate = (new DateTime())->setTimestamp($now);
        $newduedate = (new DateTime())->setTimestamp($now)->modify('+1 day');
        $assign = $this->create_instance([
            'course' => $course->id,
            'duedate' => $duedate->getTimestamp(),
        ]);
        $instance = $assign->get_instance();

        $generator->enrol_user($user->id, $course->id, 'teacher');
        $generator->role_assign($roleid, $user->id, $context->id);

        $record = $DB->get_record('event', [
            'courseid' => $course->id,
            'modulename' => 'assign',
            'instance' => $instance->id,
            'eventtype' => ASSIGN_EVENT_TYPE_DUE
        ]);

        $event = new \calendar_event($record);

        assign_capability('moodle/calendar:manageentries', CAP_ALLOW, $roleid, $context, true);
        assign_capability('moodle/course:manageactivities', CAP_ALLOW, $roleid, $context, true);

        $this->setUser($user);
        // Trigger and capture the event when adding a contact.
        $sink = $this->redirectEvents();

        calendar_local_api::update_event_start_day(
            $mapper->from_legacy_event_to_event($event),
            $newduedate
        );

        $triggeredevents = $sink->get_events();
        $moduleupdatedevents = array_filter($triggeredevents, function($e) {
            return is_a($e, 'core\event\course_module_updated');
        });

        $newinstance = $DB->get_record('assign', ['id' => $instance->id]);
        $newevent = \calendar_event::load($event->id);
        // The due date shouldn't have changed even though we updated the calendar
        // event.
        $this->assertEquals($newduedate->getTimestamp(), $newinstance->duedate);
        $this->assertEquals($newduedate->getTimestamp(), $newevent->timestart);
        // Confirm that a module updated event is fired when the module
        // is changed.
        $this->assertNotEmpty($moduleupdatedevents);
    }
}
