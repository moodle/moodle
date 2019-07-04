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
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');

use \core_calendar\local\api as calendar_local_api;
use \core_calendar\local\event\container as calendar_event_container;

/**
 * Unit tests for (some of) mod/assign/lib.php.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class mod_assign_lib_testcase extends advanced_testcase {

    // Use the generator helper.
    use mod_assign_test_generator;

    public function test_assign_print_overview() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setAdminUser();

        // Assignment with default values.
        $firstassign = $this->create_instance($course, ['name' => 'First Assignment']);

        // Assignment with submissions.
        $secondassign = $this->create_instance($course, [
                'name' => 'Assignment with submissions',
                'duedate' => time(),
                'attemptreopenmethod' => ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL,
                'maxattempts' => 3,
                'submissiondrafts' => 1,
                'assignsubmission_onlinetext_enabled' => 1,
            ]);
        $this->add_submission($student, $secondassign);
        $this->submit_for_grading($student, $secondassign);
        $this->mark_submission($teacher, $secondassign, $student, 50.0);

        // Past assignments should not show up.
        $pastassign = $this->create_instance($course, [
                'name' => 'Past Assignment',
                'duedate' => time() - DAYSECS - 1,
                'cutoffdate' => time() - DAYSECS,
                'nosubmissions' => 0,
                'assignsubmission_onlinetext_enabled' => 1,
            ]);

        // Open assignments should show up only if relevant.
        $openassign = $this->create_instance($course, [
                'name' => 'Open Assignment',
                'duedate' => time(),
                'cutoffdate' => time() + DAYSECS,
                'nosubmissions' => 0,
                'assignsubmission_onlinetext_enabled' => 1,
            ]);
        $pastsubmission = $pastassign->get_user_submission($student->id, true);
        $opensubmission = $openassign->get_user_submission($student->id, true);

        // Check the overview as the different users.
        // For students , open assignments should show only when there are no valid submissions.
        $this->setUser($student);
        $overview = array();
        $courses = $DB->get_records('course', array('id' => $course->id));
        assign_print_overview($courses, $overview);
        $this->assertDebuggingCalledCount(3);
        $this->assertEquals(1, count($overview));
        $this->assertRegExp('/.*Open Assignment.*/', $overview[$course->id]['assign']); // No valid submission.
        $this->assertNotRegExp('/.*First Assignment.*/', $overview[$course->id]['assign']); // Has valid submission.

        // And now submit the submission.
        $opensubmission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $openassign->testable_update_submission($opensubmission, $student->id, true, false);

        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertDebuggingCalledCount(3);
        $this->assertEquals(0, count($overview));

        $this->setUser($teacher);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertDebuggingCalledCount(3);
        $this->assertEquals(1, count($overview));
        // Submissions without a grade.
        $this->assertRegExp('/.*Open Assignment.*/', $overview[$course->id]['assign']);
        $this->assertNotRegExp('/.*Assignment with submissions.*/', $overview[$course->id]['assign']);

        $this->setUser($teacher);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertDebuggingCalledCount(3);
        $this->assertEquals(1, count($overview));
        // Submissions without a grade.
        $this->assertRegExp('/.*Open Assignment.*/', $overview[$course->id]['assign']);
        $this->assertNotRegExp('/.*Assignment with submissions.*/', $overview[$course->id]['assign']);

        // Let us grade a submission.
        $this->setUser($teacher);
        $data = new stdClass();
        $data->grade = '50.0';
        $openassign->testable_apply_grade_to_user($data, $student->id, 0);

        // The assign_print_overview expects the grade date to be after the submission date.
        $graderecord = $DB->get_record('assign_grades', array('assignment' => $openassign->get_instance()->id,
            'userid' => $student->id, 'attemptnumber' => 0));
        $graderecord->timemodified += 1;
        $DB->update_record('assign_grades', $graderecord);

        $overview = array();
        assign_print_overview($courses, $overview);
        // Now assignment 4 should not show up.
        $this->assertDebuggingCalledCount(3);
        $this->assertEmpty($overview);

        $this->setUser($teacher);
        $overview = array();
        assign_print_overview($courses, $overview);
        $this->assertDebuggingCalledCount(3);
        // Now assignment 4 should not show up.
        $this->assertEmpty($overview);
    }

    /**
     * Test that assign_print_overview does not return any assignments which are Open Offline.
     */
    public function test_assign_print_overview_open_offline() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setAdminUser();
        $openassign = $this->create_instance($course, [
                'duedate' => time() + DAYSECS,
                'cutoffdate' => time() + (DAYSECS * 2),
            ]);

        $this->setUser($student);
        $overview = [];
        assign_print_overview([$course], $overview);

        $this->assertDebuggingCalledCount(1);
        $this->assertEquals(0, count($overview));
    }

    /**
     * Test that assign_print_recent_activity shows ungraded submitted assignments.
     */
    public function test_print_recent_activity() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);
        $this->submit_for_grading($student, $assign);

        $this->setUser($teacher);
        $this->expectOutputRegex('/submitted:/');
        assign_print_recent_activity($course, true, time() - 3600);
    }

    /**
     * Test that assign_print_recent_activity does not display any warnings when a custom fullname has been configured.
     */
    public function test_print_recent_activity_fullname() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);
        $this->submit_for_grading($student, $assign);

        $this->setUser($teacher);
        $this->expectOutputRegex('/submitted:/');
        set_config('fullnamedisplay', 'firstname, lastnamephonetic');
        assign_print_recent_activity($course, false, time() - 3600);
    }

    /**
     * Test that assign_print_recent_activity shows the blind marking ID.
     */
    public function test_print_recent_activity_fullname_blind_marking() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
                'blindmarking' => 1,
            ]);
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        $this->setUser($teacher);
        $uniqueid = $assign->get_uniqueid_for_user($student->id);
        $expectedstr = preg_quote(get_string('participant', 'mod_assign'), '/') . '.*' . $uniqueid;
        $this->expectOutputRegex("/{$expectedstr}/");
        assign_print_recent_activity($course, false, time() - 3600);
    }

    /**
     * Test that assign_get_recent_mod_activity fetches the assignment correctly.
     */
    public function test_assign_get_recent_mod_activity() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        $index = 1;
        $activities = [
            $index => (object) [
                'type' => 'assign',
                'cmid' => $assign->get_course_module()->id,
            ],
        ];

        $this->setUser($teacher);
        assign_get_recent_mod_activity($activities, $index, time() - HOURSECS, $course->id, $assign->get_course_module()->id);

        $activity = $activities[1];
        $this->assertEquals("assign", $activity->type);
        $this->assertEquals($student->id, $activity->user->id);
    }

    /**
     * Ensure that assign_user_complete displays information about drafts.
     */
    public function test_assign_user_complete() {
        global $PAGE, $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, ['submissiondrafts' => 1]);
        $this->add_submission($student, $assign);

        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));

        $submission = $assign->get_user_submission($student->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
        $DB->update_record('assign_submission', $submission);

        $this->expectOutputRegex('/Draft/');
        assign_user_complete($course, $student, $assign->get_course_module(), $assign->get_instance());
    }

    /**
     * Ensure that assign_user_outline fetches updated grades.
     */
    public function test_assign_user_outline() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);

        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);
        $this->mark_submission($teacher, $assign, $student, 50.0);

        $this->setUser($teacher);
        $data = $assign->get_user_grade($student->id, true);
        $data->grade = '50.5';
        $assign->update_grade($data);

        $result = assign_user_outline($course, $student, $assign->get_course_module(), $assign->get_instance());

        $this->assertRegExp('/50.5/', $result->info);
    }

    /**
     * Ensure that assign_get_completion_state reflects the correct status at each point.
     */
    public function test_assign_get_completion_state() {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, [
                'submissiondrafts' => 0,
                'completionsubmit' => 1
            ]);

        $this->setUser($student);
        $result = assign_get_completion_state($course, $assign->get_course_module(), $student->id, false);
        $this->assertFalse($result);

        $this->add_submission($student, $assign);
        $result = assign_get_completion_state($course, $assign->get_course_module(), $student->id, false);
        $this->assertFalse($result);

        $this->submit_for_grading($student, $assign);
        $result = assign_get_completion_state($course, $assign->get_course_module(), $student->id, false);
        $this->assertTrue($result);

        $this->mark_submission($teacher, $assign, $student, 50.0);
        $result = assign_get_completion_state($course, $assign->get_course_module(), $student->id, false);
        $this->assertTrue($result);
    }

    /**
     * Tests for mod_assign_refresh_events.
     */
    public function test_assign_refresh_events() {
        global $DB;

        $this->resetAfterTest();

        $duedate = time();
        $newduedate = $duedate + DAYSECS;

        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, [
                'duedate' => $duedate,
            ]);

        $instance = $assign->get_instance();
        $eventparams = [
            'modulename' => 'assign',
            'instance' => $instance->id,
            'eventtype' => ASSIGN_EVENT_TYPE_DUE,
            'groupid' => 0
        ];

        // Make sure the calendar event for assignment 1 matches the initial due date.
        $eventtime = $DB->get_field('event', 'timestart', $eventparams, MUST_EXIST);
        $this->assertEquals($eventtime, $duedate);

        // Manually update assignment 1's due date.
        $DB->update_record('assign', (object) [
            'id' => $instance->id,
            'duedate' => $newduedate,
            'course' => $course->id
        ]);

        // Then refresh the assignment events of assignment 1's course.
        $this->assertTrue(assign_refresh_events($course->id));

        // Confirm that the assignment 1's due date event now has the new due date after refresh.
        $eventtime = $DB->get_field('event', 'timestart', $eventparams, MUST_EXIST);
        $this->assertEquals($eventtime, $newduedate);

        // Create a second course and assignment.
        $othercourse = $this->getDataGenerator()->create_course();;
        $otherassign = $this->create_instance($othercourse, [
            'duedate' => $duedate,
        ]);
        $otherinstance = $otherassign->get_instance();

        // Manually update assignment 1 and 2's due dates.
        $newduedate += DAYSECS;
        $DB->update_record('assign', (object)[
            'id' => $instance->id,
            'duedate' => $newduedate,
            'course' => $course->id
        ]);
        $DB->update_record('assign', (object)[
            'id' => $otherinstance->id,
            'duedate' => $newduedate,
            'course' => $othercourse->id
        ]);

        // Refresh events of all courses and check the calendar events matches the new date.
        $this->assertTrue(assign_refresh_events());

        // Check the due date calendar event for assignment 1.
        $eventtime = $DB->get_field('event', 'timestart', $eventparams, MUST_EXIST);
        $this->assertEquals($eventtime, $newduedate);

        // Check the due date calendar event for assignment 2.
        $eventparams['instance'] = $otherinstance->id;
        $eventtime = $DB->get_field('event', 'timestart', $eventparams, MUST_EXIST);
        $this->assertEquals($eventtime, $newduedate);

        // In case the course ID is passed as a numeric string.
        $this->assertTrue(assign_refresh_events('' . $course->id));

        // Non-existing course ID.
        $this->assertFalse(assign_refresh_events(-1));

        // Invalid course ID.
        $this->assertFalse(assign_refresh_events('aaa'));
    }

    public function test_assign_core_calendar_is_event_visible_duedate_event_as_teacher() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $assign = $this->create_instance($course);

        $this->setAdminUser();

        // Create a calendar event.
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_DUE);

        // The teacher should see the due date event.
        $this->setUser($teacher);
        $this->assertTrue(mod_assign_core_calendar_is_event_visible($event));
    }

    public function test_assign_core_calendar_is_event_visible_duedate_event_for_teacher() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $assign = $this->create_instance($course);

        $this->setAdminUser();

        // Create a calendar event.
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_DUE);

        // Now, log out.
        $this->setUser();

        // The teacher should see the due date event.
        $this->assertTrue(mod_assign_core_calendar_is_event_visible($event, $teacher->id));
    }

    public function test_assign_core_calendar_is_event_visible_duedate_event_as_student() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, ['assignsubmission_onlinetext_enabled' => 1]);

        $this->setAdminUser();

        // Create a calendar event.
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_DUE);

        // The student should care about the due date event.
        $this->setUser($student);
        $this->assertTrue(mod_assign_core_calendar_is_event_visible($event));
    }

    public function test_assign_core_calendar_is_event_visible_duedate_event_for_student() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, ['assignsubmission_onlinetext_enabled' => 1]);

        $this->setAdminUser();

        // Create a calendar event.
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_DUE);

        // Now, log out.
        $this->setUser();

        // The student should care about the due date event.
        $this->assertTrue(mod_assign_core_calendar_is_event_visible($event, $student->id));
    }

    public function test_assign_core_calendar_is_event_visible_gradingduedate_event_as_teacher() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $assign = $this->create_instance($course);

        // Create a calendar event.
        $this->setAdminUser();
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_GRADINGDUE);

        // The teacher should see the due date event.
        $this->setUser($teacher);
        $this->assertTrue(mod_assign_core_calendar_is_event_visible($event));
    }


    public function test_assign_core_calendar_is_event_visible_gradingduedate_event_for_teacher() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $assign = $this->create_instance($course);

        // Create a calendar event.
        $this->setAdminUser();
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_GRADINGDUE);

        // Now, log out.
        $this->setUser();

        // The teacher should see the due date event.
        $this->assertTrue(mod_assign_core_calendar_is_event_visible($event, $teacher->id));
    }

    public function test_assign_core_calendar_is_event_visible_gradingduedate_event_as_student() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);

        // Create a calendar event.
        $this->setAdminUser();
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_GRADINGDUE);

        // The student should not see the due date event.
        $this->setUser($student);
        $this->assertFalse(mod_assign_core_calendar_is_event_visible($event));
    }


    public function test_assign_core_calendar_is_event_visible_gradingduedate_event_for_student() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);

        // Create a calendar event.
        $this->setAdminUser();
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_GRADINGDUE);

        // Now, log out.
        $this->setUser();

        // The student should not see the due date event.
        $this->assertFalse(mod_assign_core_calendar_is_event_visible($event, $student->id));
    }

    public function test_assign_core_calendar_provide_event_action_duedate_as_teacher() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $assign = $this->create_instance($course);

        // Create a calendar event.
        $this->setAdminUser();
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_DUE);

        // The teacher should see the event.
        $this->setUser($teacher);
        $factory = new \core_calendar\action_factory();
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory);

        // The teacher should not have an action for a due date event.
        $this->assertNull($actionevent);
    }

    public function test_assign_core_calendar_provide_event_action_duedate_for_teacher() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $assign = $this->create_instance($course);

        // Create a calendar event.
        $this->setAdminUser();
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_DUE);

        // Now, log out.
        $this->setUser();

        // Decorate action event for a teacher.
        $factory = new \core_calendar\action_factory();
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory, $teacher->id);

        // The teacher should not have an action for a due date event.
        $this->assertNull($actionevent);
    }

    public function test_assign_core_calendar_provide_event_action_duedate_as_student() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, ['assignsubmission_onlinetext_enabled' => 1]);

        // Create a calendar event.
        $this->setAdminUser();
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_DUE);

        // The student should see the event.
        $this->setUser($student);
        $factory = new \core_calendar\action_factory();
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('addsubmission', 'assign'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_assign_core_calendar_provide_event_action_duedate_for_student() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, ['assignsubmission_onlinetext_enabled' => 1]);

        // Create a calendar event.
        $this->setAdminUser();
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_DUE);

        // Now, log out.
        $this->setUser();

        // Decorate action event for a student.
        $factory = new \core_calendar\action_factory();
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('addsubmission', 'assign'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_assign_core_calendar_provide_event_action_gradingduedate_as_teacher() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $assign = $this->create_instance($course);

        // Create a calendar event.
        $this->setAdminUser();
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_GRADINGDUE);

        $this->setUser($teacher);
        $factory = new \core_calendar\action_factory();
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('grade'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(0, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_assign_core_calendar_provide_event_action_gradingduedate_for_teacher() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $assign = $this->create_instance($course);

        // Create a calendar event.
        $this->setAdminUser();
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_GRADINGDUE);

        // Now, log out.
        $this->setUser();

        // Decorate action event for a teacher.
        $factory = new \core_calendar\action_factory();
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory, $teacher->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('grade'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(0, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_assign_core_calendar_provide_event_action_gradingduedate_as_student() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);

        // Create a calendar event.
        $this->setAdminUser();
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_GRADINGDUE);

        $this->setUser($student);
        $factory = new \core_calendar\action_factory();
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('grade'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(0, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    public function test_assign_core_calendar_provide_event_action_gradingduedate_for_student() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);

        // Create a calendar event.
        $this->setAdminUser();
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_GRADINGDUE);

        // Now, log out.
        $this->setUser();

        // Decorate action event for a student.
        $factory = new \core_calendar\action_factory();
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('grade'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(0, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    public function test_assign_core_calendar_provide_event_action_duedate_as_student_submitted() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, ['assignsubmission_onlinetext_enabled' => 1]);

        $this->setAdminUser();

        // Create a calendar event.
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_DUE);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Submit as the student.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        // Confirm there was no event to action.
        $factory = new \core_calendar\action_factory();
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory);
        $this->assertNull($actionevent);
    }

    public function test_assign_core_calendar_provide_event_action_duedate_for_student_submitted() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, ['assignsubmission_onlinetext_enabled' => 1]);

        $this->setAdminUser();

        // Create a calendar event.
        $event = $this->create_action_event($course, $assign, ASSIGN_EVENT_TYPE_DUE);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Submit as the student.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        // Now, log out.
        $this->setUser();

        // Confirm there was no event to action.
        $factory = new \core_calendar\action_factory();
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory, $student->id);
        $this->assertNull($actionevent);
    }

    public function test_assign_core_calendar_provide_event_action_already_completed() {
        $this->resetAfterTest();
        set_config('enablecompletion', 1);
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $assign = $this->create_instance($course,
         ['completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS]);

        // Get some additional data.
        $cm = get_coursemodule_from_instance('assign', $assign->get_instance()->id);

        // Create a calendar event.
        $event = $this->create_action_event($course, $assign,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_assign_core_calendar_provide_event_action_already_completed_for_user() {
        $this->resetAfterTest();
        set_config('enablecompletion', 1);
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $assign = $this->create_instance($course,
         ['completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS]);

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Get some additional data.
        $cm = get_coursemodule_from_instance('assign', $assign->get_instance()->id);

        // Create a calendar event.
        $event = $this->create_action_event($course, $assign,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed for the student.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm, $student->id);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_assign_core_calendar_provide_event_action($event, $factory, $student->id);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param \stdClass $course The course the assignment is in
     * @param assign $assign The assignment to create an event for
     * @param string $eventtype The event type. eg. ASSIGN_EVENT_TYPE_DUE.
     * @return bool|calendar_event
     */
    private function create_action_event($course, $assign, $eventtype) {
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'assign';
        $event->courseid = $course->id;
        $event->instance = $assign->get_instance()->id;
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
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);

        $this->setAdminUser();

        // Two activities, both with automatic completion. One has the 'completionsubmit' rule, one doesn't.
        $cm1 = $this->create_instance($course, ['completion' => '2', 'completionsubmit' => '1'])->get_course_module();
        $cm2 = $this->create_instance($course, ['completion' => '2', 'completionsubmit' => '0'])->get_course_module();

        // Data for the stdClass input type.
        // This type of input would occur when checking the default completion rules for an activity type, where we don't have
        // any access to cm_info, rather the input is a stdClass containing completion and customdata attributes, just like cm_info.
        $moddefaults = (object) [
            'customdata' => [
                'customcompletionrules' => [
                    'completionsubmit' => '1',
                ],
            ],
            'completion' => 2,
        ];

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
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // As a teacher.
        $this->setUser($teacher);
        $assign = $this->create_instance($course);

        // Grade the student.
        $data = ['grade' => 50];
        $assign->testable_apply_grade_to_user((object)$data, $student->id, 0);

        // Try getting another students grade. This will give a grade of ASSIGN_GRADE_NOT_SET (-1).
        $assign->get_user_grade($otherstudent->id, true);

        // Rescale.
        assign_rescale_activity_grades($course, $assign->get_course_module(), 0, 100, 0, 10);

        // Get the grades for both students.
        $studentgrade = $assign->get_user_grade($student->id, true);
        $otherstudentgrade = $assign->get_user_grade($otherstudent->id, true);

        // Make sure the real grade is scaled, but the ASSIGN_GRADE_NOT_SET stays the same.
        $this->assertEquals($studentgrade->grade, 5);
        $this->assertEquals($otherstudentgrade->grade, ASSIGN_GRADE_NOT_SET);
    }

    /**
     * Return false when there are not overrides for this assign instance.
     */
    public function test_assign_is_override_calendar_event_no_override() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setAdminUser();

        $duedate = time();
        $assign = $this->create_instance($course, ['duedate' => $duedate]);

        $instance = $assign->get_instance();
        $event = new \calendar_event((object)[
            'modulename' => 'assign',
            'instance' => $instance->id,
            'userid' => $student->id,
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
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setAdminUser();

        $userid = $student->id;
        $duedate = time();
        $assign = $this->create_instance($course, ['duedate' => $duedate]);

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
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setAdminUser();

        $duedate = time();
        $assign = $this->create_instance($course, ['duedate' => $duedate]);
        $instance = $assign->get_instance();

        $otherassign = $this->create_instance($course, ['duedate' => $duedate]);
        $otherinstance = $otherassign->get_instance();

        $event = new \calendar_event((object) [
            'modulename' => 'assign',
            'instance' => $instance->id,
            'userid' => $student->id,
        ]);

        $DB->insert_record('assign_overrides', (object) [
                'assignid' => $otherinstance->id,
                'userid' => $student->id,
            ]);

        $this->assertFalse($assign->is_override_calendar_event($event));
    }

    /**
     * Return true if there is a user override for this event and assign instance.
     */
    public function test_assign_is_override_calendar_event_user_override() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setAdminUser();

        $duedate = time();
        $assign = $this->create_instance($course, ['duedate' => $duedate]);

        $instance = $assign->get_instance();
        $event = new \calendar_event((object) [
            'modulename' => 'assign',
            'instance' => $instance->id,
            'userid' => $student->id,
        ]);


        $DB->insert_record('assign_overrides', (object) [
                'assignid' => $instance->id,
                'userid' => $student->id,
            ]);

        $this->assertTrue($assign->is_override_calendar_event($event));
    }

    /**
     * Return true if there is a group override for the event and assign instance.
     */
    public function test_assign_is_override_calendar_event_group_override() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();

        $this->setAdminUser();

        $duedate = time();
        $assign = $this->create_instance($course, ['duedate' => $duedate]);
        $instance = $assign->get_instance();
        $group = $this->getDataGenerator()->create_group(array('courseid' => $instance->course));

        $event = new \calendar_event((object) [
            'modulename' => 'assign',
            'instance' => $instance->id,
            'groupid' => $group->id,
        ]);

        $DB->insert_record('assign_overrides', (object) [
                'assignid' => $instance->id,
                'groupid' => $group->id,
            ]);

        $this->assertTrue($assign->is_override_calendar_event($event));
    }

    /**
     * Unknown event types should not have any limit restrictions returned.
     */
    public function test_mod_assign_core_calendar_get_valid_event_timestart_range_unkown_event_type() {
        global $CFG;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();

        $this->setAdminUser();

        $duedate = time();
        $assign = $this->create_instance($course, ['duedate' => $duedate]);
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
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setAdminUser();

        $duedate = time();
        $assign = $this->create_instance($course, ['duedate' => $duedate]);
        $instance = $assign->get_instance();

        $event = new \calendar_event((object) [
            'courseid' => $instance->course,
            'modulename' => 'assign',
            'instance' => $instance->id,
            'userid' => $student->id,
            'eventtype' => ASSIGN_EVENT_TYPE_DUE
        ]);

        $record = (object) [
            'assignid' => $instance->id,
            'userid' => $student->id,
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
        $course = $this->getDataGenerator()->create_course();

        $this->setAdminUser();

        $duedate = time();
        $assign = $this->create_instance($course, [
            'duedate' => $duedate,
            'allowsubmissionsfromdate' => 0,
            'cutoffdate' => 0,
        ]);
        $instance = $assign->get_instance();

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
        $course = $this->getDataGenerator()->create_course();

        $this->setAdminUser();

        $duedate = time();
        $submissionsfromdate = $duedate - DAYSECS;
        $cutoffdate = $duedate + DAYSECS;
        $assign = $this->create_instance($course, [
            'duedate' => $duedate,
            'allowsubmissionsfromdate' => $submissionsfromdate,
            'cutoffdate' => $cutoffdate,
        ]);
        $instance = $assign->get_instance();

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
        $course = $this->getDataGenerator()->create_course();

        $this->setAdminUser();

        $assign = $this->create_instance($course, [
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
        $course = $this->getDataGenerator()->create_course();

        $this->setAdminUser();

        $duedate = time();
        $assign = $this->create_instance($course, ['duedate' => $duedate]);
        $instance = $assign->get_instance();

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
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setAdminUser();

        $duedate = time();
        $submissionsfromdate = $duedate - DAYSECS;
        $cutoffdate = $duedate + DAYSECS;
        $assign = $this->create_instance($course, [
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
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setAdminUser();

        $duedate = time();
        $submissionsfromdate = $duedate - DAYSECS;
        $cutoffdate = $duedate + DAYSECS;
        $assign = $this->create_instance($course, [
            'duedate' => $duedate,
            'allowsubmissionsfromdate' => $submissionsfromdate,
            'cutoffdate' => $cutoffdate,
        ]);
        $instance = $assign->get_instance();

        $event = new \calendar_event((object) [
            'courseid' => $instance->course,
            'modulename' => 'assign',
            'instance' => $instance->id,
            'userid' => $student->id,
            'eventtype' => ASSIGN_EVENT_TYPE_DUE,
            'timestart' => $duedate + 1
        ]);

        $record = (object) [
            'assignid' => $instance->id,
            'userid' => $student->id,
            'duedate' => $duedate + 1,
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
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setAdminUser();

        $duedate = time();
        $newduedate = $duedate + 1;
        $submissionsfromdate = $duedate - DAYSECS;
        $cutoffdate = $duedate + DAYSECS;
        $assign = $this->create_instance($course, [
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
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        $roleid = $this->getDataGenerator()->create_role();
        $role = $DB->get_record('role', ['id' => $roleid]);
        $user = $this->getDataGenerator()->create_and_enrol($course, $role->shortname);

        $this->setAdminUser();

        $mapper = calendar_event_container::get_event_mapper();
        $now = time();
        $duedate = (new DateTime())->setTimestamp($now);
        $newduedate = (new DateTime())->setTimestamp($now)->modify('+1 day');
        $assign = $this->create_instance($course, [
            'course' => $course->id,
            'duedate' => $duedate->getTimestamp(),
        ]);
        $instance = $assign->get_instance();

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
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'teacher']);

        $this->setAdminUser();

        $mapper = calendar_event_container::get_event_mapper();
        $now = time();
        $duedate = (new DateTime())->setTimestamp($now);
        $newduedate = (new DateTime())->setTimestamp($now)->modify('+1 day');
        $assign = $this->create_instance($course, [
            'course' => $course->id,
            'duedate' => $duedate->getTimestamp(),
        ]);
        $instance = $assign->get_instance();

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

    /**
     * A user who does not have capabilities to add events to the calendar should be able to create an assignment.
     */
    public function test_creation_with_no_calendar_capabilities() {
        $this->resetAfterTest();
        $course = self::getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $user = self::getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $roleid = self::getDataGenerator()->create_role();
        self::getDataGenerator()->role_assign($roleid, $user->id, $context->id);
        assign_capability('moodle/calendar:manageentries', CAP_PROHIBIT, $roleid, $context, true);
        $generator = self::getDataGenerator()->get_plugin_generator('mod_assign');
        // Create an instance as a user without the calendar capabilities.
        $this->setUser($user);
        $time = time();
        $params = array(
            'course' => $course->id,
            'allowsubmissionsfromdate' => $time,
            'duedate' => $time + 500,
            'cutoffdate' => $time + 600,
            'gradingduedate' => $time + 700,
        );
        $generator->create_instance($params);
    }
}
