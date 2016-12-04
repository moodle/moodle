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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');
require_once($CFG->dirroot . '/mod/assign/tests/fixtures/event_mod_assign_fixtures.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Contains the event tests for the module assign.
 *
 * @package   mod_assign
 * @copyright 2014 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_events_testcase extends mod_assign_base_testcase {

    /**
     * Basic tests for the submission_created() abstract class.
     */
    public function test_base_event() {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance(array('course' => $this->course->id));
        $modcontext = context_module::instance($instance->cmid);

        $data = array(
            'context' => $modcontext,
        );
        /** @var \mod_assign_unittests\event\nothing_happened $event */
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
    public function test_submission_created() {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance(array('course' => $this->course->id));
        $modcontext = context_module::instance($instance->cmid);

        // Standard Event parameters.
        $params = array(
            'context' => $modcontext,
            'courseid' => $this->course->id
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
        $this->assertEquals($this->course->id, $event->courseid);

        // Check that an error occurs when teamsubmission is not set.
        try {
            \mod_assign_unittests\event\submission_created::create($params);
            $this->fail('Other must contain the key submissionid.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        // Check that the submission status debugging is fired.
        $subinfo = $params;
        $subinfo['other'] = array('submissionid' => '23');
        try {
            \mod_assign_unittests\event\submission_created::create($subinfo);
            $this->fail('Other must contain the key submissionattempt.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        $subinfo['other'] = array('submissionattempt' => '0');
        try {
            \mod_assign_unittests\event\submission_created::create($subinfo);
            $this->fail('Other must contain the key submissionstatus.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    /**
     * Basic tests for the submission_updated() abstract class.
     */
    public function test_submission_updated() {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance(array('course' => $this->course->id));
        $modcontext = context_module::instance($instance->cmid);

        // Standard Event parameters.
        $params = array(
            'context' => $modcontext,
            'courseid' => $this->course->id
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
        $this->assertEquals($this->course->id, $event->courseid);

        // Check that an error occurs when teamsubmission is not set.
        try {
            \mod_assign_unittests\event\submission_created::create($params);
            $this->fail('Other must contain the key submissionid.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        // Check that the submission status debugging is fired.
        $subinfo = $params;
        $subinfo['other'] = array('submissionid' => '23');
        try {
            \mod_assign_unittests\event\submission_created::create($subinfo);
            $this->fail('Other must contain the key submissionattempt.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        $subinfo['other'] = array('submissionattempt' => '0');
        try {
            \mod_assign_unittests\event\submission_created::create($subinfo);
            $this->fail('Other must contain the key submissionstatus.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    public function test_extension_granted() {
        $this->setUser($this->editingteachers[0]);

        $tomorrow = time() + 24*60*60;
        $yesterday = time() - 24*60*60;

        $assign = $this->create_instance(array('duedate' => $yesterday, 'cutoffdate' => $yesterday));
        $sink = $this->redirectEvents();

        $assign->testable_save_user_extension($this->students[0]->id, $tomorrow);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\extension_granted', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'grant extension',
            'view.php?id=' . $assign->get_course_module()->id,
            $this->students[0]->id,
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();
    }

    public function test_submission_locked() {
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();
        $sink = $this->redirectEvents();

        $assign->lock_submission($this->students[0]->id);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\submission_locked', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'lock submission',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('locksubmissionforstudent', 'assign', array('id' => $this->students[0]->id,
                'fullname' => fullname($this->students[0]))),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Revert to defaults.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    public function test_identities_revealed() {
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance(array('blindmarking'=>1));
        $sink = $this->redirectEvents();

        $assign->reveal_identities();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\identities_revealed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'reveal identities',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('revealidentities', 'assign'),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Revert to defaults.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    /**
     * Test the submission_status_viewed event.
     */
    public function test_submission_status_viewed() {
        global $PAGE;

        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();

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
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'view',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('viewownsubmissionstatus', 'assign'),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    public function test_submission_status_updated() {
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->students[0]->id, true, false);

        $sink = $this->redirectEvents();
        $assign->revert_to_draft($this->students[0]->id);

        $events = $sink->get_events();
        $this->assertCount(2, $events);
        $event = $events[1];
        $this->assertInstanceOf('\mod_assign\event\submission_status_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($submission->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $this->assertEquals(ASSIGN_SUBMISSION_STATUS_DRAFT, $event->other['newstatus']);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'revert submission to draft',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('reverttodraftforstudent', 'assign', array('id' => $this->students[0]->id,
                'fullname' => fullname($this->students[0]))),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Revert to defaults.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    public function test_marker_updated() {
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();

        $sink = $this->redirectEvents();
        $assign->testable_process_set_batch_marking_allocation($this->students[0]->id, $this->teachers[0]->id);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\marker_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $this->assertEquals($this->editingteachers[0]->id, $event->userid);
        $this->assertEquals($this->teachers[0]->id, $event->other['markerid']);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'set marking allocation',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('setmarkerallocationforlog', 'assign', array('id' => $this->students[0]->id,
                'fullname' => fullname($this->students[0]), 'marker' => fullname($this->teachers[0]))),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Revert to defaults.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    public function test_workflow_state_updated() {
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();

        // Test process_set_batch_marking_workflow_state.
        $sink = $this->redirectEvents();
        $assign->testable_process_set_batch_marking_workflow_state($this->students[0]->id, ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\workflow_state_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $this->assertEquals($this->editingteachers[0]->id, $event->userid);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW, $event->other['newstate']);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'set marking workflow state',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('setmarkingworkflowstateforlog', 'assign', array('id' => $this->students[0]->id,
                'fullname' => fullname($this->students[0]), 'state' => ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW)),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Test setting workflow state in apply_grade_to_user.
        $sink = $this->redirectEvents();
        $data = new stdClass();
        $data->grade = '50.0';
        $data->workflowstate = 'readyforrelease';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        $events = $sink->get_events();
        $this->assertCount(4, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\workflow_state_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $this->assertEquals($this->editingteachers[0]->id, $event->userid);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_READYFORRELEASE, $event->other['newstate']);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'set marking workflow state',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('setmarkingworkflowstateforlog', 'assign', array('id' => $this->students[0]->id,
                'fullname' => fullname($this->students[0]), 'state' => ASSIGN_MARKING_WORKFLOW_STATE_READYFORRELEASE)),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Test setting workflow state in process_save_quick_grades.
        $sink = $this->redirectEvents();

        $data = array(
            'grademodified_' . $this->students[0]->id => time(),
            'gradeattempt_' . $this->students[0]->id => '',
            'quickgrade_' . $this->students[0]->id => '60.0',
            'quickgrade_' . $this->students[0]->id . '_workflowstate' => 'inmarking'
        );
        $assign->testable_process_save_quick_grades($data);

        $events = $sink->get_events();
        $this->assertCount(4, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\workflow_state_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $this->assertEquals($this->editingteachers[0]->id, $event->userid);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_INMARKING, $event->other['newstate']);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'set marking workflow state',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('setmarkingworkflowstateforlog', 'assign', array('id' => $this->students[0]->id,
                'fullname' => fullname($this->students[0]), 'state' => ASSIGN_MARKING_WORKFLOW_STATE_INMARKING)),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Revert to defaults.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    public function test_submission_duplicated() {
        $this->setUser($this->students[0]);

        $assign = $this->create_instance();
        $submission1 = $assign->get_user_submission($this->students[0]->id, true, 0);
        $submission2 = $assign->get_user_submission($this->students[0]->id, true, 1);
        $submission2->status = ASSIGN_SUBMISSION_STATUS_REOPENED;
        $assign->testable_update_submission($submission2, $this->students[0]->id, time(), $assign->get_instance()->teamsubmission);

        $sink = $this->redirectEvents();
        $notices = null;
        $assign->copy_previous_attempt($notices);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\submission_duplicated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($submission2->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->userid);
        $submission2->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'submissioncopied',
            'view.php?id=' . $assign->get_course_module()->id,
            $assign->testable_format_submission_for_log($submission2),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();
    }

    public function test_submission_unlocked() {
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();
        $sink = $this->redirectEvents();

        $assign->unlock_submission($this->students[0]->id);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\submission_unlocked', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'unlock submission',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('unlocksubmissionforstudent', 'assign', array('id' => $this->students[0]->id,
                'fullname' => fullname($this->students[0]))),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Revert to defaults.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    public function test_submission_graded() {
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        // Test apply_grade_to_user.
        $sink = $this->redirectEvents();

        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);
        $grade = $assign->get_user_grade($this->students[0]->id, false, 0);

        $events = $sink->get_events();
        $this->assertCount(3, $events);
        $event = $events[2];
        $this->assertInstanceOf('\mod_assign\event\submission_graded', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($grade->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'grade submission',
            'view.php?id=' . $assign->get_course_module()->id,
            $assign->format_grade_for_log($grade),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Test process_save_quick_grades.
        $sink = $this->redirectEvents();

        $grade = $assign->get_user_grade($this->students[0]->id, false);
        $data = array(
            'grademodified_' . $this->students[0]->id => time(),
            'gradeattempt_' . $this->students[0]->id => $grade->attemptnumber,
            'quickgrade_' . $this->students[0]->id => '60.0'
        );
        $assign->testable_process_save_quick_grades($data);
        $grade = $assign->get_user_grade($this->students[0]->id, false);
        $this->assertEquals('60.0', $grade->grade);

        $events = $sink->get_events();
        $this->assertCount(3, $events);
        $event = $events[2];
        $this->assertInstanceOf('\mod_assign\event\submission_graded', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($grade->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'grade submission',
            'view.php?id=' . $assign->get_course_module()->id,
            $assign->format_grade_for_log($grade),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Test update_grade.
        $sink = $this->redirectEvents();
        $data = clone($grade);
        $data->grade = '50.0';
        $assign->update_grade($data);
        $grade = $assign->get_user_grade($this->students[0]->id, false, 0);
        $this->assertEquals('50.0', $grade->grade);
        $events = $sink->get_events();

        $this->assertCount(3, $events);
        $event = $events[2];
        $this->assertInstanceOf('\mod_assign\event\submission_graded', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($grade->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'grade submission',
            'view.php?id=' . $assign->get_course_module()->id,
            $assign->format_grade_for_log($grade),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();
        // Revert to defaults.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    /**
     * Test the submission_viewed event.
     */
    public function test_submission_viewed() {
        global $PAGE;

        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();
        $submission = $assign->get_user_submission($this->students[0]->id, true);

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
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'view submission',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('viewsubmissionforuser', 'assign', $this->students[0]->id),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the feedback_viewed event.
     */
    public function test_feedback_viewed() {
        global $DB, $PAGE;

        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();
        $submission = $assign->get_user_submission($this->students[0]->id, true);

        // Insert a grade for this submission.
        $grade = new stdClass();
        $grade->assignment = $assign->get_instance()->id;
        $grade->userid = $this->students[0]->id;
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
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'view feedback',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('viewfeedbackforuser', 'assign', $this->students[0]->id),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the grading_form_viewed event.
     */
    public function test_grading_form_viewed() {
        global $PAGE;

        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();

        // We need to set the URL in order to view the feedback.
        $PAGE->set_url('/a_url');
        // A hack - this variable is used by the view_single_grade_page function.
        global $_POST;
        $_POST['rownum'] = 1;
        $_POST['userid'] = $this->students[0]->id;

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->view('grade');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\grading_form_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'view grading form',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('viewgradingformforstudent', 'assign', array('id' => $this->students[0]->id,
                'fullname' => fullname($this->students[0]))),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the grading_table_viewed event.
     */
    public function test_grading_table_viewed() {
        global $PAGE;

        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();

        // We need to set the URL in order to view the feedback.
        $PAGE->set_url('/a_url');
        // A hack - this variable is used by the view_single_grade_page function.
        global $_POST;
        $_POST['rownum'] = 1;
        $_POST['userid'] = $this->students[0]->id;

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->view('grading');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\grading_table_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'view submission grading table',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('viewsubmissiongradingtable', 'assign'),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the submission_form_viewed event.
     */
    public function test_submission_form_viewed() {
        global $PAGE;

        $this->setUser($this->students[0]);

        $assign = $this->create_instance();

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
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'view submit assignment form',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('editsubmission', 'assign'),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the submission_form_viewed event.
     */
    public function test_submission_confirmation_form_viewed() {
        global $PAGE;

        $this->setUser($this->students[0]);

        $assign = $this->create_instance();

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
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'view confirm submit assignment form',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('viewownsubmissionform', 'assign'),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the reveal_identities_confirmation_page_viewed event.
     */
    public function test_reveal_identities_confirmation_page_viewed() {
        global $PAGE;

        // Set to the admin user so we have the permission to reveal identities.
        $this->setAdminUser();

        $assign = $this->create_instance();

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
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'view',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('viewrevealidentitiesconfirm', 'assign'),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the statement_accepted event.
     */
    public function test_statement_accepted() {
        // We want to be a student so we can submit assignments.
        $this->setUser($this->students[0]);

        // We do not want to send any messages to the student during the PHPUNIT test.
        set_config('submissionreceipts', false, 'assign');

        $assign = $this->create_instance();

        // Create the data we want to pass to the submit_for_grading function.
        $data = new stdClass();
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
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'submission statement accepted',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('submissionstatementacceptedlog',
                'mod_assign',
                fullname($this->students[0])),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
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
        $data = new stdClass();
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
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the batch_set_workflow_state_viewed event.
     */
    public function test_batch_set_workflow_state_viewed() {
        $assign = $this->create_instance();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->testable_view_batch_set_workflow_state($this->students[0]->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\batch_set_workflow_state_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'view batch set marking workflow state',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('viewbatchsetmarkingworkflowstate', 'assign'),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the batch_set_marker_allocation_viewed event.
     */
    public function test_batch_set_marker_allocation_viewed() {
        $assign = $this->create_instance();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $assign->testable_view_batch_markingallocation($this->students[0]->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_assign\event\batch_set_marker_allocation_viewed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'view batch set marker allocation',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('viewbatchmarkingallocation', 'assign'),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the user override created event.
     *
     * There is no external API for creating a user override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_user_override_created() {

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id));

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2,
            'context' => context_module::instance($assign->cmid),
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
        $this->assertEquals(context_module::instance($assign->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the group override created event.
     *
     * There is no external API for creating a group override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_group_override_created() {

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id));

        $params = array(
            'objectid' => 1,
            'context' => context_module::instance($assign->cmid),
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
        $this->assertEquals(context_module::instance($assign->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the user override updated event.
     *
     * There is no external API for updating a user override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_user_override_updated() {

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id));

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2,
            'context' => context_module::instance($assign->cmid),
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
        $this->assertEquals(context_module::instance($assign->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the group override updated event.
     *
     * There is no external API for updating a group override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_group_override_updated() {

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id));

        $params = array(
            'objectid' => 1,
            'context' => context_module::instance($assign->cmid),
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
        $this->assertEquals(context_module::instance($assign->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the user override deleted event.
     */
    public function test_user_override_deleted() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id));
        $this->assign = new assign($assign, null, null);

        // Create an override.
        $override = new stdClass();
        $override->assign = $this->assign->get_context()->id;
        $override->userid = 2;
        $override->id = $DB->insert_record('assign_overrides', $override);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $this->assign->delete_override($override->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_assign\event\user_override_deleted', $event);
        $this->assertEquals(context_module::instance($assign->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the group override deleted event.
     */
    public function test_group_override_deleted() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id));
        $this->assign = new assign($assign, null, null);

        // Create an override.
        $override = new stdClass();
        $override->assign = $this->assign->get_context()->id;
        $override->groupid = 2;
        $override->id = $DB->insert_record('assign_overrides', $override);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $this->assign->delete_override($override->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_assign\event\group_override_deleted', $event);
        $this->assertEquals(context_module::instance($assign->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }
}
