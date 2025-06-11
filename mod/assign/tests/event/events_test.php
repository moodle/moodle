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
 * Contains the event tests for the module assign.
 *
 * @package   mod_assign
 * @copyright 2014 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_assign\event;

use mod_assign_test_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');
require_once($CFG->dirroot . '/mod/assign/tests/fixtures/event_mod_assign_fixtures.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Contains the event tests for the module assign.
 *
 * @package   mod_assign
 * @copyright 2014 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class events_test extends \advanced_testcase {
    // Use the generator helper.
    use mod_assign_test_generator;

    /**
     * Basic tests for the submission_created() abstract class.
     */
    public function test_base_event(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance(array('course' => $course->id));
        $modcontext = \context_module::instance($instance->cmid);

        $data = array(
            'context' => $modcontext,
        );

        $event = \mod_assign_unittests\event\nothing_happened::create($data);
        $assign = $event->get_assign();
        $this->assertDebuggingCalled();
        $this->assertInstanceOf('assign', $assign);

        $event = \mod_assign_unittests\event\nothing_happened::create($data);
        $event->set_assign($assign);
        $assign2 = $event->get_assign();
        $this->assertDebuggingNotCalled();
        $this->assertSame($assign, $assign2);
    }

    /**
     * Basic tests for the submission_created() abstract class.
     */
    public function test_submission_created(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance(array('course' => $course->id));
        $modcontext = \context_module::instance($instance->cmid);

        // Standard Event parameters.
        $params = array(
            'context' => $modcontext,
            'courseid' => $course->id
        );

        $eventinfo = $params;
        $eventinfo['other'] = array(
            'submissionid' => '17',
            'submissionattempt' => 0,
            'submissionstatus' => 'submitted'
        );

        $sink = $this->redirectEvents();
        $event = \mod_assign_unittests\event\submission_created::create($eventinfo);
        $event->trigger();
        $result = $sink->get_events();
        $event = reset($result);
        $sink->close();

        $this->assertEquals($modcontext->id, $event->contextid);
        $this->assertEquals($course->id, $event->courseid);

        // Check that an error occurs when teamsubmission is not set.
        try {
            \mod_assign_unittests\event\submission_created::create($params);
            $this->fail('Other must contain the key submissionid.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        // Check that the submission status debugging is fired.
        $subinfo = $params;
        $subinfo['other'] = array('submissionid' => '23');
        try {
            \mod_assign_unittests\event\submission_created::create($subinfo);
            $this->fail('Other must contain the key submissionattempt.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        $subinfo['other'] = array('submissionattempt' => '0');
        try {
            \mod_assign_unittests\event\submission_created::create($subinfo);
            $this->fail('Other must contain the key submissionstatus.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    /**
     * Basic tests for the submission_updated() abstract class.
     */
    public function test_submission_updated(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance(array('course' => $course->id));
        $modcontext = \context_module::instance($instance->cmid);

        // Standard Event parameters.
        $params = array(
            'context' => $modcontext,
            'courseid' => $course->id
        );

        $eventinfo = $params;
        $eventinfo['other'] = array(
            'submissionid' => '17',
            'submissionattempt' => 0,
            'submissionstatus' => 'submitted'
        );

        $sink = $this->redirectEvents();
        $event = \mod_assign_unittests\event\submission_updated::create($eventinfo);
        $event->trigger();
        $result = $sink->get_events();
        $event = reset($result);
        $sink->close();

        $this->assertEquals($modcontext->id, $event->contextid);
        $this->assertEquals($course->id, $event->courseid);

        // Check that an error occurs when teamsubmission is not set.
        try {
            \mod_assign_unittests\event\submission_created::create($params);
            $this->fail('Other must contain the key submissionid.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        // Check that the submission status debugging is fired.
        $subinfo = $params;
        $subinfo['other'] = array('submissionid' => '23');
        try {
            \mod_assign_unittests\event\submission_created::create($subinfo);
            $this->fail('Other must contain the key submissionattempt.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        $subinfo['other'] = array('submissionattempt' => '0');
        try {
            \mod_assign_unittests\event\submission_created::create($subinfo);
            $this->fail('Other must contain the key submissionstatus.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    /**
     * Test submission_removed event.
     *
     * @covers \mod_assign\event\submission_removed
     */
    public function test_submission_removed(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course);
        $this->add_submission($student, $assign);
        $submission = $assign->get_user_submission($student->id, 0);

        $sink = $this->redirectEvents();
        $assign->remove_submission($student->id);
        $events = $sink->get_events();
        $this->assertCount(2, $events);
        $event = $events[0];
        $this->assertInstanceOf('mod_assign\event\submission_removed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($submission->id, $event->objectid);
        $this->assertEquals($student->id, $event->relateduserid);
        $this->assertEquals($submission->id, $event->other['submissionid']);
        $this->assertEquals(0, $event->other['submissionattempt']);
        $this->assertEquals(ASSIGN_SUBMISSION_STATUS_NEW, $event->other['submissionstatus']);
        $this->assertEquals(0, $event->other['groupid']);
        $this->assertEquals(null, $event->other['groupname']);
        $sink->close();
    }

    /**
     * Test submission_removed event when a team submission is removed.
     *
     * @covers \mod_assign\event\submission_removed
     */
    public function test_team_submission_removed(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group, $student);

        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group, $otherstudent);

        $assign = $this->create_instance($course, [
            'teamsubmission' => 1,
        ]);
        $this->add_submission($student, $assign);
        $submission = $assign->get_group_submission($student->id, 0, true);

        $sink = $this->redirectEvents();
        $assign->remove_submission($student->id);
        $events = $sink->get_events();
        $this->assertCount(2, $events);
        $event = $events[0];
        $this->assertInstanceOf('mod_assign\event\submission_removed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($submission->id, $event->objectid);
        $this->assertEquals(null, $event->relateduserid);
        $this->assertEquals($submission->id, $event->other['submissionid']);
        $this->assertEquals(0, $event->other['submissionattempt']);
        $this->assertEquals(ASSIGN_SUBMISSION_STATUS_NEW, $event->other['submissionstatus']);
        $this->assertEquals($group->id, $event->other['groupid']);
        $this->assertEquals($group->name, $event->other['groupname']);
        $sink->close();
    }

    /**
     * Test event creation for save_user_extension().
     *
     * @covers \assign::save_user_extension
     */
    public function test_extension_granted(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/calendar/lib.php');

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);

        $now = time();
        $tomorrow = $now + DAYSECS;
        $yesterday = $now - DAYSECS;

        $assign = $this->create_instance($course, [
            'duedate' => $yesterday,
            'cutoffdate' => $yesterday,
        ]);
        $sink = $this->redirectEvents();

        $assign->testable_save_user_extension($student->id, $tomorrow);

        $events = $sink->get_events();

        // Event for extension granted and extension due date.
        $this->assertCount(2, $events);

        $grantedevent = $events[0];
        $this->assertInstanceOf('\mod_assign\event\extension_granted', $grantedevent);
        $this->assertEquals($assign->get_context(), $grantedevent->get_context());
        $this->assertEquals($assign->get_instance()->id, $grantedevent->objectid);
        $this->assertEquals($student->id, $grantedevent->relateduserid);

        $calendarevent = $events[1];
        $this->assertInstanceOf('\core\event\calendar_event_created', $calendarevent);

        // Check that the calendar event is deleted if extension is revoked.
        $assign->testable_save_user_extension($student->id, '');

        $isexist = $DB->record_exists('event', [
            'userid' => $student->id,
            'eventtype' => ASSIGN_EVENT_TYPE_EXTENSION,
            'modulename' => 'assign',
            'instance' => $assign->get_course_module()->id,
        ]);
        $this->assertFalse($isexist);

        $sink->close();
    }

    public function test_submission_locked(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $teacher->ignoresesskey = true;
        $this->setUser($teacher);

        $assign = $this->create_instance($course);
        $sink = $this->redirectEvents();

        $assign->lock_submission($student->id);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\submission_locked', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($student->id, $event->relateduserid);
        $sink->close();
    }

    public function test_identities_revealed(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $teacher->ignoresesskey = true;
        $this->setUser($teacher);

        $assign = $this->create_instance($course, ['blindmarking' => 1]);
        $sink = $this->redirectEvents();

        $assign->reveal_identities();

        $events = $sink->get_events();
        $eventscount = 0;

        foreach ($events as $event) {
            if ($event instanceof \mod_assign\event\identities_revealed) {
                $eventscount++;
                $this->assertInstanceOf('\mod_assign\event\identities_revealed', $event);
                $this->assertEquals($assign->get_context(), $event->get_context());
                $this->assertEquals($assign->get_instance()->id, $event->objectid);
            }
        }

        $this->assertEquals(1, $eventscount);
        $sink->close();
    }

    /**
     * Test the submission_status_viewed event.
     */
    public function test_submission_status_viewed(): void {
        global $PAGE;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $this->setUser($teacher);

        $assign = $this->create_instance($course);

        // We need to set the URL in order to view the feedback.
        $PAGE->set_url('/a_url');

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->view();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\submission_status_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
    }

    /**
     * Test submission_status_updated event when a submission is updated.
     *
     * @covers \mod_assign\event\submission_status_updated
     */
    public function test_submission_status_updated_on_update(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);

        $assign = $this->create_instance($course);
        $submission = $assign->get_user_submission($student->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, false);

        $sink = $this->redirectEvents();
        $assign->revert_to_draft($student->id);

        $events = $sink->get_events();
        $this->assertCount(2, $events);
        $event = $events[1];
        $this->assertInstanceOf('\mod_assign\event\submission_status_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($submission->id, $event->objectid);
        $this->assertEquals($student->id, $event->relateduserid);
        $this->assertEquals(ASSIGN_SUBMISSION_STATUS_DRAFT, $event->other['newstatus']);
        $sink->close();
    }

    /**
     * Test submission_status_updated event when a submission is removed.
     *
     * @covers \mod_assign\event\submission_status_updated
     */
    public function test_submission_status_updated_on_remove(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course);
        $this->add_submission($student, $assign);
        $submission = $assign->get_user_submission($student->id, false);

        $sink = $this->redirectEvents();
        $assign->remove_submission($student->id);

        $events = $sink->get_events();
        $this->assertCount(2, $events);

        $event = $events[1];
        $this->assertInstanceOf('\mod_assign\event\submission_status_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($submission->id, $event->objectid);
        $this->assertEquals($student->id, $event->relateduserid);
        $this->assertEquals(ASSIGN_SUBMISSION_STATUS_NEW, $event->other['newstatus']);
        $sink->close();
    }

    /**
     * Test submission_status_updated event when a team submission is removed.
     *
     * @covers \mod_assign\event\submission_status_updated
     */
    public function test_team_submission_status_updated_on_remove(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group, $student);

        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group, $otherstudent);

        $assign = $this->create_instance($course, [
            'teamsubmission' => 1,
        ]);
        $this->add_submission($student, $assign);
        $submission = $assign->get_group_submission($student->id, 0, false);

        $sink = $this->redirectEvents();
        $assign->remove_submission($student->id);

        $events = $sink->get_events();
        $this->assertCount(2, $events);

        $event = $events[1];
        $this->assertInstanceOf('\mod_assign\event\submission_status_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($submission->id, $event->objectid);
        $this->assertEquals(null, $event->relateduserid);
        $this->assertEquals(ASSIGN_SUBMISSION_STATUS_NEW, $event->other['newstatus']);
        $sink->close();
    }

    public function test_marker_updated(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $teacher->ignoresesskey = true;
        $this->setUser($teacher);

        $assign = $this->create_instance($course);

        $sink = $this->redirectEvents();
        $assign->testable_process_set_batch_marking_allocation($student->id, $teacher->id);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\marker_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($student->id, $event->relateduserid);
        $this->assertEquals($teacher->id, $event->userid);
        $this->assertEquals($teacher->id, $event->other['markerid']);
        $sink->close();
    }

    public function test_workflow_state_updated(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $teacher->ignoresesskey = true;
        $this->setUser($teacher);

        $assign = $this->create_instance($course);

        // Test process_set_batch_marking_workflow_state.
        $sink = $this->redirectEvents();
        $assign->testable_process_set_batch_marking_workflow_state($student->id, ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW);

        $events = $sink->get_events();
        $eventcount = 0;
        foreach ($events as $event) {
            if ($event instanceof \mod_assign\event\submission_graded) {
                $eventcount++;
                $this->assertInstanceOf('\mod_assign\event\submission_graded', $event);
                $this->assertEquals($assign->get_context(), $event->get_context());
            }
            if ($event instanceof \mod_assign\event\workflow_state_updated) {
                $eventcount++;
                $this->assertInstanceOf('\mod_assign\event\workflow_state_updated', $event);
                $this->assertEquals($assign->get_context(), $event->get_context());
                $this->assertEquals($assign->get_instance()->id, $event->objectid);
                $this->assertEquals($student->id, $event->relateduserid);
                $this->assertEquals($teacher->id, $event->userid);
                $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW, $event->other['newstate']);
            }
        }
        $this->assertEquals(2, $eventcount);
        $sink->close();

        // Test setting workflow state in apply_grade_to_user.
        $sink = $this->redirectEvents();
        $data = new \stdClass();
        $data->grade = '50.0';
        $data->workflowstate = 'readyforrelease';
        $assign->testable_apply_grade_to_user($data, $student->id, 0);

        $events = $sink->get_events();
        $this->assertCount(4, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\workflow_state_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($student->id, $event->relateduserid);
        $this->assertEquals($teacher->id, $event->userid);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_READYFORRELEASE, $event->other['newstate']);
        $sink->close();

        // Test setting workflow state in process_save_quick_grades.
        $sink = $this->redirectEvents();

        $data = array(
            'grademodified_' . $student->id => time(),
            'gradeattempt_' . $student->id => '',
            'quickgrade_' . $student->id => '60.0',
            'quickgrade_' . $student->id . '_workflowstate' => 'inmarking'
        );
        $assign->testable_process_save_quick_grades($data);

        $events = $sink->get_events();
        $this->assertCount(4, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\workflow_state_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($student->id, $event->relateduserid);
        $this->assertEquals($teacher->id, $event->userid);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_INMARKING, $event->other['newstate']);
        $sink->close();
    }

    public function test_submission_duplicated(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($student);

        $assign = $this->create_instance($course);
        $submission1 = $assign->get_user_submission($student->id, true, 0);
        $submission2 = $assign->get_user_submission($student->id, true, 1);
        $submission2->status = ASSIGN_SUBMISSION_STATUS_REOPENED;
        $assign->testable_update_submission($submission2, $student->id, time(), $assign->get_instance()->teamsubmission);

        $sink = $this->redirectEvents();
        $notices = null;
        $assign->copy_previous_attempt($notices);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\submission_duplicated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($submission2->id, $event->objectid);
        $this->assertEquals($student->id, $event->userid);
        $submission2->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
        $sink->close();
    }

    public function test_submission_unlocked(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $teacher->ignoresesskey = true;
        $this->setUser($teacher);

        $assign = $this->create_instance($course);
        $sink = $this->redirectEvents();

        $assign->unlock_submission($student->id);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\submission_unlocked', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($student->id, $event->relateduserid);
        $sink->close();
    }

    public function test_submission_graded(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $teacher->ignoresesskey = true;
        $this->setUser($teacher);

        $assign = $this->create_instance($course);

        // Test apply_grade_to_user.
        $sink = $this->redirectEvents();

        $data = new \stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $student->id, 0);
        $grade = $assign->get_user_grade($student->id, false, 0);

        $events = $sink->get_events();
        $this->assertCount(3, $events);
        $event = $events[2];
        $this->assertInstanceOf('\mod_assign\event\submission_graded', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($grade->id, $event->objectid);
        $this->assertEquals($student->id, $event->relateduserid);
        $sink->close();

        // Test process_save_quick_grades.
        $sink = $this->redirectEvents();

        $grade = $assign->get_user_grade($student->id, false);
        $data = array(
            'grademodified_' . $student->id => time(),
            'gradeattempt_' . $student->id => $grade->attemptnumber,
            'quickgrade_' . $student->id => '60.0'
        );
        $assign->testable_process_save_quick_grades($data);
        $grade = $assign->get_user_grade($student->id, false);
        $this->assertEquals(60.0, $grade->grade);

        $events = $sink->get_events();
        $this->assertCount(3, $events);
        $event = $events[2];
        $this->assertInstanceOf('\mod_assign\event\submission_graded', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($grade->id, $event->objectid);
        $this->assertEquals($student->id, $event->relateduserid);
        $sink->close();

        // Test update_grade.
        $sink = $this->redirectEvents();
        $data = clone($grade);
        $data->grade = '50.0';
        $assign->update_grade($data);
        $grade = $assign->get_user_grade($student->id, false, 0);
        $this->assertEquals(50.0, $grade->grade);
        $events = $sink->get_events();

        $this->assertCount(3, $events);
        $event = $events[2];
        $this->assertInstanceOf('\mod_assign\event\submission_graded', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($grade->id, $event->objectid);
        $this->assertEquals($student->id, $event->relateduserid);
        $sink->close();
    }

    /**
     * Test the submission_viewed event.
     */
    public function test_submission_viewed(): void {
        global $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);

        $assign = $this->create_instance($course);
        $submission = $assign->get_user_submission($student->id, true);

        // We need to set the URL in order to view the submission.
        $PAGE->set_url('/a_url');
        // A hack - these variables are used by the view_plugin_content function to
        // determine what we actually want to view - would usually be set in URL.
        global $_POST;
        $_POST['plugin'] = 'comments';
        $_POST['sid'] = $submission->id;

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->view('viewpluginassignsubmission');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\submission_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($submission->id, $event->objectid);
    }

    /**
     * Test the feedback_viewed event.
     */
    public function test_feedback_viewed(): void {
        global $DB, $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);

        $assign = $this->create_instance($course);
        $submission = $assign->get_user_submission($student->id, true);

        // Insert a grade for this submission.
        $grade = new \stdClass();
        $grade->assignment = $assign->get_instance()->id;
        $grade->userid = $student->id;
        $gradeid = $DB->insert_record('assign_grades', $grade);

        // We need to set the URL in order to view the feedback.
        $PAGE->set_url('/a_url');
        // A hack - these variables are used by the view_plugin_content function to
        // determine what we actually want to view - would usually be set in URL.
        global $_POST;
        $_POST['plugin'] = 'comments';
        $_POST['gid'] = $gradeid;
        $_POST['sid'] = $submission->id;

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->view('viewpluginassignfeedback');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\feedback_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($gradeid, $event->objectid);
    }

    /**
     * Test the grading_form_viewed event.
     */
    public function test_grading_form_viewed(): void {
        global $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);

        $assign = $this->create_instance($course);

        // We need to set the URL in order to view the feedback.
        $PAGE->set_url('/a_url');
        // A hack - this variable is used by the view_single_grade_page function.
        global $_POST;
        $_POST['rownum'] = 1;
        $_POST['userid'] = $student->id;

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->view('grade');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\grading_form_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
    }

    /**
     * Test the grading_table_viewed event.
     */
    public function test_grading_table_viewed(): void {
        global $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);

        $assign = $this->create_instance($course);

        // We need to set the URL in order to view the feedback.
        $PAGE->set_url('/a_url');
        // A hack - this variable is used by the view_single_grade_page function.
        global $_POST;
        $_POST['rownum'] = 1;
        $_POST['userid'] = $student->id;

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->view('grading');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\grading_table_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the submission_form_viewed event.
     */
    public function test_submission_form_viewed(): void {
        global $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($student);

        $assign = $this->create_instance($course);

        // We need to set the URL in order to view the submission form.
        $PAGE->set_url('/a_url');

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->view('editsubmission');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\submission_form_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the submission_form_viewed event.
     */
    public function test_submission_confirmation_form_viewed(): void {
        global $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($student);

        $assign = $this->create_instance($course);

        // We need to set the URL in order to view the submission form.
        $PAGE->set_url('/a_url');

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->view('submit');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\submission_confirmation_form_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the reveal_identities_confirmation_page_viewed event.
     */
    public function test_reveal_identities_confirmation_page_viewed(): void {
        global $PAGE;
        $this->resetAfterTest();

        // Set to the admin user so we have the permission to reveal identities.
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->create_instance($course);

        // We need to set the URL in order to view the submission form.
        $PAGE->set_url('/a_url');

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->view('revealidentities');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\reveal_identities_confirmation_page_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the statement_accepted event.
     */
    public function test_statement_accepted(): void {
        // We want to be a student so we can submit assignments.
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($student);

        // We do not want to send any messages to the student during the PHPUNIT test.
        set_config('submissionreceipts', false, 'assign');

        $assign = $this->create_instance($course);

        // Create the data we want to pass to the submit_for_grading function.
        $data = new \stdClass();
        $data->submissionstatement = 'We are the Borg. You will be assimilated. Resistance is futile. - do you agree
            to these terms?';

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->submit_for_grading($data, array());
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\statement_accepted', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEventContextNotUsed($event);

        // Enable the online text submission plugin.
        $submissionplugins = $assign->get_submission_plugins();
        foreach ($submissionplugins as $plugin) {
            if ($plugin->get_type() === 'onlinetext') {
                $plugin->enable();
                break;
            }
        }

        // Create the data we want to pass to the save_submission function.
        $data = new \stdClass();
        $data->onlinetext_editor = array(
            'text' => 'Online text',
            'format' => FORMAT_HTML,
            'itemid' => file_get_unused_draft_itemid()
        );
        $data->submissionstatement = 'We are the Borg. You will be assimilated. Resistance is futile. - do you agree
            to these terms?';

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->save_submission($data, $notices);
        $events = $sink->get_events();
        $event = $events[2];

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\statement_accepted', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the batch_set_workflow_state_viewed event.
     */
    public function test_batch_set_workflow_state_viewed(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->testable_view_batch_set_workflow_state($student->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\batch_set_workflow_state_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the batch_set_marker_allocation_viewed event.
     */
    public function test_batch_set_marker_allocation_viewed(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->testable_view_batch_markingallocation($student->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\batch_set_marker_allocation_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the user override created event.
     *
     * There is no external API for creating a user override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_user_override_created(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->get_plugin_generator('mod_assign')->create_instance(['course' => $course->id]);

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2,
            'context' => \context_module::instance($assign->cmid),
            'other' => array(
                'assignid' => $assign->id
            )
        );
        $event = \mod_assign\event\user_override_created::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_assign\event\user_override_created', $event);
        $this->assertEquals(\context_module::instance($assign->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the group override created event.
     *
     * There is no external API for creating a group override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_group_override_created(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->get_plugin_generator('mod_assign')->create_instance(['course' => $course->id]);

        $params = array(
            'objectid' => 1,
            'context' => \context_module::instance($assign->cmid),
            'other' => array(
                'assignid' => $assign->id,
                'groupid' => 2
            )
        );
        $event = \mod_assign\event\group_override_created::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_assign\event\group_override_created', $event);
        $this->assertEquals(\context_module::instance($assign->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the user override updated event.
     *
     * There is no external API for updating a user override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_user_override_updated(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->get_plugin_generator('mod_assign')->create_instance(['course' => $course->id]);

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2,
            'context' => \context_module::instance($assign->cmid),
            'other' => array(
                'assignid' => $assign->id
            )
        );
        $event = \mod_assign\event\user_override_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_assign\event\user_override_updated', $event);
        $this->assertEquals(\context_module::instance($assign->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the group override updated event.
     *
     * There is no external API for updating a group override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_group_override_updated(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->get_plugin_generator('mod_assign')->create_instance(['course' => $course->id]);

        $params = array(
            'objectid' => 1,
            'context' => \context_module::instance($assign->cmid),
            'other' => array(
                'assignid' => $assign->id,
                'groupid' => 2
            )
        );
        $event = \mod_assign\event\group_override_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_assign\event\group_override_updated', $event);
        $this->assertEquals(\context_module::instance($assign->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the user override deleted event.
     */
    public function test_user_override_deleted(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $assigninstance = $this->getDataGenerator()->create_module('assign', array('course' => $course->id));
        $cm = get_coursemodule_from_instance('assign', $assigninstance->id, $course->id);
        $context = \context_module::instance($cm->id);
        $assign = new \assign($context, $cm, $course);

        // Create an override.
        $override = new \stdClass();
        $override->assign = $assigninstance->id;
        $override->userid = 2;
        $override->id = $DB->insert_record('assign_overrides', $override);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->delete_override($override->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_assign\event\user_override_deleted', $event);
        $this->assertEquals(\context_module::instance($cm->id), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the group override deleted event.
     */
    public function test_group_override_deleted(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $assigninstance = $this->getDataGenerator()->create_module('assign', array('course' => $course->id));
        $cm = get_coursemodule_from_instance('assign', $assigninstance->id, $course->id);
        $context = \context_module::instance($cm->id);
        $assign = new \assign($context, $cm, $course);

        // Create an override.
        $override = new \stdClass();
        $override->assign = $assigninstance->id;
        $override->groupid = 2;
        $override->id = $DB->insert_record('assign_overrides', $override);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->delete_override($override->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_assign\event\group_override_deleted', $event);
        $this->assertEquals(\context_module::instance($cm->id), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the course module viewed event.
     */
    public function test_course_module_viewed(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->create_instance($course);

        $context = $assign->get_context();

        $params = array(
            'context' => $context,
            'objectid' => $assign->get_instance()->id
        );

        $event = \mod_assign\event\course_module_viewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
    }

    /**
     * Test that all events generated with blindmarking enabled are anonymous
     */
    public function test_anonymous_events(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance(array('course' => $course->id, 'blindmarking' => 1));

        $cm = get_coursemodule_from_instance('assign', $instance->id, $course->id);
        $context = \context_module::instance($cm->id);
        $assign = new \assign($context, $cm, $course);

        $this->setUser($teacher);
        $sink = $this->redirectEvents();

        $assign->lock_submission($student1->id);

        $events = $sink->get_events();
        $event = reset($events);

        $this->assertTrue((bool)$event->anonymous);

        $assign->reveal_identities();
        $sink = $this->redirectEvents();
        $assign->lock_submission($student2->id);

        $events = $sink->get_events();
        $event = reset($events);

        $this->assertFalse((bool)$event->anonymous);
    }

}
