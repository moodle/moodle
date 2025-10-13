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
 * Unit tests for (some of) mod/feedback/lib.php.
 *
 * @package    mod_feedback
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_feedback;

use mod_feedback_completion;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/mod/feedback/lib.php');

/**
 * Unit tests for (some of) mod/feedback/lib.php.
 *
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class lib_test extends \advanced_testcase {

    public function test_feedback_initialise(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $params['course'] = $course->id;
        $params['timeopen'] = time() - 5 * MINSECS;
        $params['timeclose'] = time() + DAYSECS;
        $params['anonymous'] = 1;
        $params['intro'] = 'Some introduction text';
        $feedback = $this->getDataGenerator()->create_module('feedback', $params);

        // Test different ways to construct the structure object.
        $pseudocm = get_coursemodule_from_instance('feedback', $feedback->id); // Object similar to cm_info.
        $cm = get_fast_modinfo($course)->instances['feedback'][$feedback->id]; // Instance of cm_info.

        $constructorparams = [
            [$feedback, null],
            [null, $pseudocm],
            [null, $cm],
            [$feedback, $pseudocm],
            [$feedback, $cm],
        ];

        foreach ($constructorparams as $params) {
            $structure = new mod_feedback_completion($params[0], $params[1], 0);
            $this->assertTrue($structure->is_open());
            $this->assertTrue($structure->get_cm() instanceof \cm_info);
            $this->assertEquals($feedback->cmid, $structure->get_cm()->id);
            $this->assertEquals($feedback->intro, $structure->get_feedback()->intro);
        }
    }

    /**
     * Tests for mod_feedback_refresh_events.
     */
    public function test_feedback_refresh_events(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $timeopen = time();
        $timeclose = time() + 86400;

        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $params['course'] = $course->id;
        $params['timeopen'] = $timeopen;
        $params['timeclose'] = $timeclose;
        $feedback = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('feedback', $feedback->id);
        $context = \context_module::instance($cm->id);

        // Normal case, with existing course.
        $this->assertTrue(feedback_refresh_events($course->id));
        $eventparams = array('modulename' => 'feedback', 'instance' => $feedback->id, 'eventtype' => 'open');
        $openevent = $DB->get_record('event', $eventparams, '*', MUST_EXIST);
        $this->assertEquals($openevent->timestart, $timeopen);

        $eventparams = array('modulename' => 'feedback', 'instance' => $feedback->id, 'eventtype' => 'close');
        $closeevent = $DB->get_record('event', $eventparams, '*', MUST_EXIST);
        $this->assertEquals($closeevent->timestart, $timeclose);
        // In case the course ID is passed as a numeric string.
        $this->assertTrue(feedback_refresh_events('' . $course->id));
        // Course ID not provided.
        $this->assertTrue(feedback_refresh_events());
        $eventparams = array('modulename' => 'feedback');
        $events = $DB->get_records('event', $eventparams);
        foreach ($events as $event) {
            if ($event->modulename === 'feedback' && $event->instance === $feedback->id && $event->eventtype === 'open') {
                $this->assertEquals($event->timestart, $timeopen);
            }
            if ($event->modulename === 'feedback' && $event->instance === $feedback->id && $event->eventtype === 'close') {
                $this->assertEquals($event->timestart, $timeclose);
            }
        }
    }

    /**
     * Test check_updates_since callback.
     */
    public function test_check_updates_since(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();

        // Create user.
        $student = self::getDataGenerator()->create_user();

        // User enrolment.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');

        $this->setCurrentTimeStart();
        $record = array(
            'course' => $course->id,
            'custom' => 0,
            'feedback' => 1,
        );
        $feedback = $this->getDataGenerator()->create_module('feedback', $record);
        $cm = get_coursemodule_from_instance('feedback', $feedback->id, $course->id);
        $cm = \cm_info::create($cm);

        $this->setUser($student);
        // Check that upon creation, the updates are only about the new configuration created.
        $onehourago = time() - HOURSECS;
        $updates = feedback_check_updates_since($cm, $onehourago);
        foreach ($updates as $el => $val) {
            if ($el == 'configuration') {
                $this->assertTrue($val->updated);
                $this->assertTimeCurrent($val->timeupdated);
            } else {
                $this->assertFalse($val->updated);
            }
        }

        $record = [
            'feedback' => $feedback->id,
            'userid' => $student->id,
            'timemodified' => time(),
            'random_response' => 0,
            'anonymous_response' => FEEDBACK_ANONYMOUS_NO,
            'courseid' => $course->id,
        ];
        $DB->insert_record('feedback_completed', (object)$record);
        $DB->insert_record('feedback_completedtmp', (object)$record);

        // Check now for finished and unfinished attempts.
        $updates = feedback_check_updates_since($cm, $onehourago);
        $this->assertTrue($updates->attemptsunfinished->updated);
        $this->assertCount(1, $updates->attemptsunfinished->itemids);

        $this->assertTrue($updates->attemptsfinished->updated);
        $this->assertCount(1, $updates->attemptsfinished->itemids);
    }

    /**
     * Test calendar event provide action open.
     */
    public function test_feedback_core_calendar_provide_event_action_open(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $now = time();
        $course = $this->getDataGenerator()->create_course();
        // Enrol admin as a student so they can complete the feedback.
        $this->getDataGenerator()->enrol_user(get_admin()->id, $course->id, 'student');
        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id,
                'timeopen' => $now - DAYSECS, 'timeclose' => $now + DAYSECS]);
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory);

        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('answerquestions', 'feedback'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    /**
     * Test calendar event provide action open, viewed by a different user.
     */
    public function test_feedback_core_calendar_provide_event_action_open_for_user(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $now = time();
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id, 'manual');

        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id,
            'timeopen' => $now - DAYSECS, 'timeclose' => $now + DAYSECS]);
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);
        $factory = new \core_calendar\action_factory();

        $this->setUser($user2);

        // User2 checking their events.
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory, $user2->id);
        $this->assertNull($actionevent);

        // User2 checking $user's events.
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory, $user->id);
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('answerquestions', 'feedback'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    /**
     * Test calendar event provide action closed.
     */
    public function test_feedback_core_calendar_provide_event_action_closed(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course->id,
                'timeclose' => time() - DAYSECS));
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory);

        // No event on the dashboard if feedback is closed.
        $this->assertNull($actionevent);
    }

    /**
     * Test calendar event provide action closed, viewed by a different user.
     */
    public function test_feedback_core_calendar_provide_event_action_closed_for_user(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id, 'manual');

        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course->id,
            'timeclose' => time() - DAYSECS));
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);
        $factory = new \core_calendar\action_factory();
        $this->setUser($user2);

        // User2 checking their events.
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory, $user2->id);
        $this->assertNull($actionevent);

        // User2 checking $user's events.
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory, $user->id);

        // No event on the dashboard if feedback is closed.
        $this->assertNull($actionevent);
    }

    /**
     * Test calendar event action open in future.
     *
     * @throws coding_exception
     */
    public function test_feedback_core_calendar_provide_event_action_open_in_future(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        // Enrol admin as a student so they can complete the feedback.
        $this->getDataGenerator()->enrol_user(get_admin()->id, $course->id, 'student');
        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id,
                'timeopen' => time() + DAYSECS]);
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory);

        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('answerquestions', 'feedback'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    /**
     * Test calendar event action open in future, viewed by a different user.
     *
     * @throws coding_exception
     */
    public function test_feedback_core_calendar_provide_event_action_open_in_future_for_user(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id, 'manual');

        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id,
            'timeopen' => time() + DAYSECS]);
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);

        $factory = new \core_calendar\action_factory();
        $this->setUser($user2);

        // User2 checking their events.
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory, $user2->id);
        $this->assertNull($actionevent);

        // User2 checking $user's events.
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory, $user->id);

        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('answerquestions', 'feedback'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    /**
     * Test calendar event with no time specified.
     *
     * @throws coding_exception
     */
    public function test_feedback_core_calendar_provide_event_action_no_time_specified(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        // Enrol admin as a student so they can complete the feedback.
        $this->getDataGenerator()->enrol_user(get_admin()->id, $course->id, 'student');
        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory);

        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('answerquestions', 'feedback'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    /**
     * Test calendar event with no time specified, viewed by a different user.
     *
     * @throws coding_exception
     */
    public function test_feedback_core_calendar_provide_event_action_no_time_specified_for_user(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id, 'manual');

        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);

        $factory = new \core_calendar\action_factory();
        $this->setUser($user2);

        // User2 checking their events.
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory, $user2->id);
        $this->assertNull($actionevent);

        // User2 checking $user's events.
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory, $user->id);

        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('answerquestions', 'feedback'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    /**
     * A user that can not submit feedback should not have an action.
     */
    public function test_feedback_core_calendar_provide_event_action_can_not_submit(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);
        $cm = get_coursemodule_from_instance('feedback', $feedback->id);
        $context = \context_module::instance($cm->id);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id, 'manual');

        $this->setUser($user);
        assign_capability('mod/feedback:complete', CAP_PROHIBIT, $studentrole->id, $context);

        $factory = new \core_calendar\action_factory();
        $action = mod_feedback_core_calendar_provide_event_action($event, $factory);

        $this->assertNull($action);
    }

    /**
     * A user that can not submit feedback should not have an action, viewed by a different user.
     */
    public function test_feedback_core_calendar_provide_event_action_can_not_submit_for_user(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);
        $cm = get_coursemodule_from_instance('feedback', $feedback->id);
        $context = \context_module::instance($cm->id);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id, 'manual');

        assign_capability('mod/feedback:complete', CAP_PROHIBIT, $studentrole->id, $context);
        $factory = new \core_calendar\action_factory();
        $this->setUser($user2);

        // User2 checking their events.

        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory, $user2->id);
        $this->assertNull($actionevent);

        // User2 checking $user's events.
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory, $user->id);

        $this->assertNull($actionevent);
    }

    /**
     * A user that has already submitted feedback should not have an action.
     */
    public function test_feedback_core_calendar_provide_event_action_already_submitted(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);
        $cm = get_coursemodule_from_instance('feedback', $feedback->id);
        $context = \context_module::instance($cm->id);

        $this->setUser($user);

        $record = [
            'feedback' => $feedback->id,
            'userid' => $user->id,
            'timemodified' => time(),
            'random_response' => 0,
            'anonymous_response' => FEEDBACK_ANONYMOUS_NO,
            'courseid' => 0,
        ];
        $DB->insert_record('feedback_completed', (object) $record);

        $factory = new \core_calendar\action_factory();
        $action = mod_feedback_core_calendar_provide_event_action($event, $factory);

        $this->assertNull($action);
    }

    /**
     * A user that has already submitted feedback should not have an action, viewed by a different user.
     */
    public function test_feedback_core_calendar_provide_event_action_already_submitted_for_user(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);
        $cm = get_coursemodule_from_instance('feedback', $feedback->id);
        $context = \context_module::instance($cm->id);

        $this->setUser($user);

        $record = [
            'feedback' => $feedback->id,
            'userid' => $user->id,
            'timemodified' => time(),
            'random_response' => 0,
            'anonymous_response' => FEEDBACK_ANONYMOUS_NO,
            'courseid' => 0,
        ];
        $DB->insert_record('feedback_completed', (object) $record);

        $factory = new \core_calendar\action_factory();
        $this->setUser($user2);

        // User2 checking their events.
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory, $user2->id);
        $this->assertNull($actionevent);

        // User2 checking $user's events.
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory, $user->id);

        $this->assertNull($actionevent);
    }

    public function test_feedback_core_calendar_provide_event_action_already_completed(): void {
        $this->resetAfterTest();
        set_config('enablecompletion', 1);
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('feedback', $feedback->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $feedback->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_feedback_core_calendar_provide_event_action_already_completed_for_user(): void {
        $this->resetAfterTest();
        set_config('enablecompletion', 1);
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Get some additional data.
        $cm = get_coursemodule_from_instance('feedback', $feedback->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $feedback->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed for the student.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm, $student->id);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_feedback_core_calendar_provide_event_action($event, $factory, $student->id);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid The course id.
     * @param int $instanceid The feedback id.
     * @param string $eventtype The event type. eg. FEEDBACK_EVENT_TYPE_OPEN.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new \stdClass();
        $event->name = 'Calendar event';
        $event->modulename = 'feedback';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return \calendar_event::create($event);
    }

    /**
     * Test the callback responsible for returning the completion rule descriptions.
     * This function should work given either an instance of the module (cm_info), such as when checking the active rules,
     * or if passed a stdClass of similar structure, such as when checking the the default completion settings for a mod type.
     */
    public function test_mod_feedback_completion_get_active_rule_descriptions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Two activities, both with automatic completion. One has the 'completionsubmit' rule, one doesn't.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 2]);
        $feedback1 = $this->getDataGenerator()->create_module('feedback', [
            'course' => $course->id,
            'completion' => 2,
            'completionsubmit' => 1
        ]);
        $feedback2 = $this->getDataGenerator()->create_module('feedback', [
            'course' => $course->id,
            'completion' => 2,
            'completionsubmit' => 0
        ]);
        $cm1 = \cm_info::create(get_coursemodule_from_instance('feedback', $feedback1->id));
        $cm2 = \cm_info::create(get_coursemodule_from_instance('feedback', $feedback2->id));

        // Data for the stdClass input type.
        // This type of input would occur when checking the default completion rules for an activity type, where we don't have
        // any access to cm_info, rather the input is a stdClass containing completion and customdata attributes, just like cm_info.
        $moddefaults = new \stdClass();
        $moddefaults->customdata = ['customcompletionrules' => ['completionsubmit' => 1]];
        $moddefaults->completion = 2;

        $activeruledescriptions = [get_string('completionsubmit', 'feedback')];
        $this->assertEquals(mod_feedback_get_completion_active_rule_descriptions($cm1), $activeruledescriptions);
        $this->assertEquals(mod_feedback_get_completion_active_rule_descriptions($cm2), []);
        $this->assertEquals(mod_feedback_get_completion_active_rule_descriptions($moddefaults), $activeruledescriptions);
        $this->assertEquals(mod_feedback_get_completion_active_rule_descriptions(new \stdClass()), []);
    }

    /**
     * An unknown event should not have min or max restrictions.
     */
    public function test_get_valid_event_timestart_range_unknown_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $feedbackgenerator = $generator->get_plugin_generator('mod_feedback');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $feedback = $feedbackgenerator->create_instance(['course' => $course->id]);
        $feedback->timeopen = $timeopen;
        $feedback->timeclose = $timeclose;
        $DB->update_record('feedback', $feedback);

        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'feedback',
            'instance' => $feedback->id,
            'eventtype' => 'SOME UNKNOWN EVENT',
            'timestart' => $timeopen,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        list($min, $max) = mod_feedback_core_calendar_get_valid_event_timestart_range($event, $feedback);
        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * A FEEDBACK_EVENT_TYPE_OPEN should have a max timestart equal to the activity
     * close time.
     */
    public function test_get_valid_event_timestart_range_event_type_open(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $feedbackgenerator = $generator->get_plugin_generator('mod_feedback');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $feedback = $feedbackgenerator->create_instance(['course' => $course->id]);
        $feedback->timeopen = $timeopen;
        $feedback->timeclose = $timeclose;
        $DB->update_record('feedback', $feedback);

        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'feedback',
            'instance' => $feedback->id,
            'eventtype' => FEEDBACK_EVENT_TYPE_OPEN,
            'timestart' => $timeopen,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        list($min, $max) = mod_feedback_core_calendar_get_valid_event_timestart_range($event, $feedback);
        $this->assertNull($min);
        $this->assertEquals($timeclose, $max[0]);
        $this->assertNotEmpty($max[1]);
    }

    /**
     * A FEEDBACK_EVENT_TYPE_OPEN should not have a max timestamp if the activity
     * doesn't have a close date.
     */
    public function test_get_valid_event_timestart_range_event_type_open_no_close(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $feedbackgenerator = $generator->get_plugin_generator('mod_feedback');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $feedback = $feedbackgenerator->create_instance(['course' => $course->id]);
        $feedback->timeopen = $timeopen;
        $feedback->timeclose = 0;
        $DB->update_record('feedback', $feedback);

        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'feedback',
            'instance' => $feedback->id,
            'eventtype' => FEEDBACK_EVENT_TYPE_OPEN,
            'timestart' => $timeopen,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        list($min, $max) = mod_feedback_core_calendar_get_valid_event_timestart_range($event, $feedback);
        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * A FEEDBACK_EVENT_TYPE_CLOSE should have a min timestart equal to the activity
     * open time.
     */
    public function test_get_valid_event_timestart_range_event_type_close(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $feedbackgenerator = $generator->get_plugin_generator('mod_feedback');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $feedback = $feedbackgenerator->create_instance(['course' => $course->id]);
        $feedback->timeopen = $timeopen;
        $feedback->timeclose = $timeclose;
        $DB->update_record('feedback', $feedback);

        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'feedback',
            'instance' => $feedback->id,
            'eventtype' => FEEDBACK_EVENT_TYPE_CLOSE,
            'timestart' => $timeopen,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        list($min, $max) = mod_feedback_core_calendar_get_valid_event_timestart_range($event, $feedback);
        $this->assertEquals($timeopen, $min[0]);
        $this->assertNotEmpty($min[1]);
        $this->assertNull($max);
    }

    /**
     * A FEEDBACK_EVENT_TYPE_CLOSE should not have a minimum timestamp if the activity
     * doesn't have an open date.
     */
    public function test_get_valid_event_timestart_range_event_type_close_no_open(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $feedbackgenerator = $generator->get_plugin_generator('mod_feedback');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $feedback = $feedbackgenerator->create_instance(['course' => $course->id]);
        $feedback->timeopen = 0;
        $feedback->timeclose = $timeclose;
        $DB->update_record('feedback', $feedback);

        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'feedback',
            'instance' => $feedback->id,
            'eventtype' => FEEDBACK_EVENT_TYPE_CLOSE,
            'timestart' => $timeopen,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        list($min, $max) = mod_feedback_core_calendar_get_valid_event_timestart_range($event, $feedback);
        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * An unkown event type should not change the feedback instance.
     */
    public function test_mod_feedback_core_calendar_event_timestart_updated_unknown_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $feedbackgenerator = $generator->get_plugin_generator('mod_feedback');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $feedback = $feedbackgenerator->create_instance(['course' => $course->id]);
        $feedback->timeopen = $timeopen;
        $feedback->timeclose = $timeclose;
        $DB->update_record('feedback', $feedback);

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'feedback',
            'instance' => $feedback->id,
            'eventtype' => FEEDBACK_EVENT_TYPE_OPEN . "SOMETHING ELSE",
            'timestart' => 1,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        mod_feedback_core_calendar_event_timestart_updated($event, $feedback);

        $feedback = $DB->get_record('feedback', ['id' => $feedback->id]);
        $this->assertEquals($timeopen, $feedback->timeopen);
        $this->assertEquals($timeclose, $feedback->timeclose);
    }

    /**
     * A FEEDBACK_EVENT_TYPE_OPEN event should update the timeopen property of
     * the feedback activity.
     */
    public function test_mod_feedback_core_calendar_event_timestart_updated_open_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $feedbackgenerator = $generator->get_plugin_generator('mod_feedback');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $timemodified = 1;
        $newtimeopen = $timeopen - DAYSECS;
        $feedback = $feedbackgenerator->create_instance(['course' => $course->id]);
        $feedback->timeopen = $timeopen;
        $feedback->timeclose = $timeclose;
        $feedback->timemodified = $timemodified;
        $DB->update_record('feedback', $feedback);

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'feedback',
            'instance' => $feedback->id,
            'eventtype' => FEEDBACK_EVENT_TYPE_OPEN,
            'timestart' => $newtimeopen,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        mod_feedback_core_calendar_event_timestart_updated($event, $feedback);

        $feedback = $DB->get_record('feedback', ['id' => $feedback->id]);
        // Ensure the timeopen property matches the event timestart.
        $this->assertEquals($newtimeopen, $feedback->timeopen);
        // Ensure the timeclose isn't changed.
        $this->assertEquals($timeclose, $feedback->timeclose);
        // Ensure the timemodified property has been changed.
        $this->assertNotEquals($timemodified, $feedback->timemodified);
    }

    /**
     * A FEEDBACK_EVENT_TYPE_CLOSE event should update the timeclose property of
     * the feedback activity.
     */
    public function test_mod_feedback_core_calendar_event_timestart_updated_close_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $feedbackgenerator = $generator->get_plugin_generator('mod_feedback');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $timemodified = 1;
        $newtimeclose = $timeclose + DAYSECS;
        $feedback = $feedbackgenerator->create_instance(['course' => $course->id]);
        $feedback->timeopen = $timeopen;
        $feedback->timeclose = $timeclose;
        $feedback->timemodified = $timemodified;
        $DB->update_record('feedback', $feedback);

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'feedback',
            'instance' => $feedback->id,
            'eventtype' => FEEDBACK_EVENT_TYPE_CLOSE,
            'timestart' => $newtimeclose,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        mod_feedback_core_calendar_event_timestart_updated($event, $feedback);

        $feedback = $DB->get_record('feedback', ['id' => $feedback->id]);
        // Ensure the timeclose property matches the event timestart.
        $this->assertEquals($newtimeclose, $feedback->timeclose);
        // Ensure the timeopen isn't changed.
        $this->assertEquals($timeopen, $feedback->timeopen);
        // Ensure the timemodified property has been changed.
        $this->assertNotEquals($timemodified, $feedback->timemodified);
    }

    /**
     * If a student somehow finds a way to update the calendar event
     * then the callback should not be executed to update the activity
     * properties as well because that would be a security issue.
     */
    public function test_student_role_cant_update_time_close_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $generator->create_role();
        $feedbackgenerator = $generator->get_plugin_generator('mod_feedback');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $timemodified = 1;
        $newtimeclose = $timeclose + DAYSECS;
        $feedback = $feedbackgenerator->create_instance(['course' => $course->id]);
        $feedback->timeopen = $timeopen;
        $feedback->timeclose = $timeclose;
        $feedback->timemodified = $timemodified;
        $DB->update_record('feedback', $feedback);

        $generator->enrol_user($user->id, $course->id, 'student');
        $generator->role_assign($roleid, $user->id, $context->id);

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => $user->id,
            'modulename' => 'feedback',
            'instance' => $feedback->id,
            'eventtype' => FEEDBACK_EVENT_TYPE_CLOSE,
            'timestart' => $newtimeclose,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        assign_capability('moodle/calendar:manageentries', CAP_ALLOW, $roleid, $context, true);
        assign_capability('moodle/course:manageactivities', CAP_PROHIBIT, $roleid, $context, true);

        $this->setUser($user);

        mod_feedback_core_calendar_event_timestart_updated($event, $feedback);

        $newfeedback = $DB->get_record('feedback', ['id' => $feedback->id]);
        // The activity shouldn't have been updated because the user
        // doesn't have permissions to do it.
        $this->assertEquals($timeclose, $newfeedback->timeclose);
    }

    /**
     * The activity should update if a teacher modifies the calendar
     * event.
     */
    public function test_teacher_role_can_update_time_close_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $generator->create_role();
        $feedbackgenerator = $generator->get_plugin_generator('mod_feedback');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $timemodified = 1;
        $newtimeclose = $timeclose + DAYSECS;
        $feedback = $feedbackgenerator->create_instance(['course' => $course->id]);
        $feedback->timeopen = $timeopen;
        $feedback->timeclose = $timeclose;
        $feedback->timemodified = $timemodified;
        $DB->update_record('feedback', $feedback);

        $generator->enrol_user($user->id, $course->id, 'teacher');
        $generator->role_assign($roleid, $user->id, $context->id);

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => $user->id,
            'modulename' => 'feedback',
            'instance' => $feedback->id,
            'eventtype' => FEEDBACK_EVENT_TYPE_CLOSE,
            'timestart' => $newtimeclose,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        assign_capability('moodle/calendar:manageentries', CAP_ALLOW, $roleid, $context, true);
        assign_capability('moodle/course:manageactivities', CAP_ALLOW, $roleid, $context, true);

        $this->setUser($user);

        $sink = $this->redirectEvents();

        mod_feedback_core_calendar_event_timestart_updated($event, $feedback);

        $triggeredevents = $sink->get_events();
        $moduleupdatedevents = array_filter($triggeredevents, function($e) {
            return is_a($e, 'core\event\course_module_updated');
        });

        $newfeedback = $DB->get_record('feedback', ['id' => $feedback->id]);
        // The activity should have been updated because the user
        // has permissions to do it.
        $this->assertEquals($newtimeclose, $newfeedback->timeclose);
        // A course_module_updated event should be fired if the module
        // was successfully modified.
        $this->assertNotEmpty($moduleupdatedevents);
    }

    /**
     * A user who does not have capabilities to add events to the calendar should be able to create an feedback.
     */
    public function test_creation_with_no_calendar_capabilities(): void {
        $this->resetAfterTest();
        $course = self::getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $user = self::getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $roleid = self::getDataGenerator()->create_role();
        self::getDataGenerator()->role_assign($roleid, $user->id, $context->id);
        assign_capability('moodle/calendar:manageentries', CAP_PROHIBIT, $roleid, $context, true);
        $generator = self::getDataGenerator()->get_plugin_generator('mod_feedback');
        // Create an instance as a user without the calendar capabilities.
        $this->setUser($user);
        $time = time();
        $params = array(
            'course' => $course->id,
            'timeopen' => $time + 200,
            'timeclose' => $time + 2000,
        );
        $generator->create_instance($params);
    }

    /**
     * Test that if a teacher (non editing) is not part of any group in separate group mode he will not receive notification emails.
     * @covers ::feedback_get_receivemail_users
     */
    public function test_feedback_get_receivemail_users(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['groupmode' => SEPARATEGROUPS, 'groupmodeforce' => 1]);
        $group = $generator->create_group(['courseid' => $course->id, 'name' => 'group0']);

        $feedbackgenerator = $generator->get_plugin_generator('mod_feedback');
        $feedback = $feedbackgenerator->create_instance(['course' => $course->id, 'email_notification' => true]);
        $teacher = [];
        $data = [
            'teacher1' => 'teacher',
            'teacher2' => 'teacher',
            'teacher3' => 'editingteacher',
            'teacher4' => 'editingteacher',
        ];
        foreach ($data as $username => $role) {
            $teacher[$username] = $generator->create_user(['username' => $username]);
            $generator->enrol_user($teacher[$username]->id, $course->id, $role);
        }
        $generator->create_group_member([
            'groupid' => $group->id,
            'userid' => $teacher['teacher1']->id,
        ]);
        $generator->create_group_member([
            'groupid' => $group->id,
            'userid' => $teacher['teacher4']->id,
        ]);

        $usergroup = $group->id;
        // Non editing Teachers (teacher1) in a group should receive notification emails.
        // Editing teachers (teacher4), in a group should also receive notification emails.
        $teachersingroup = feedback_get_receivemail_users($feedback->cmid, $usergroup);
        $this->assertCount(2, $teachersingroup);
        $this->assertNotEmpty($teachersingroup[$teacher['teacher1']->id]);
        $this->assertNotEmpty($teachersingroup[$teacher['teacher4']->id]);
        // Here we should return only the editing teachers (teacher3 and 4) who have access to all groups.
        $teacherwithnogroup = feedback_get_receivemail_users($feedback->cmid);
        $this->assertCount(2, $teacherwithnogroup);
        $this->assertNotEmpty($teacherwithnogroup[$teacher['teacher3']->id]);
        $this->assertNotEmpty($teacherwithnogroup[$teacher['teacher4']->id]);
    }

    /**
     * Test feedback_get_completeds().
     *
     * @covers ::feedback_get_completeds
     * @covers ::feedback_get_completeds_count
     * @dataProvider provider_feedback_get_completeds
     *
     * @param int $groupmode The group mode of the course.
     * @param array $selectedgroups The groups selected for filtering.
     * @param int $expectedcount The expected number of completeds.
     */
    public function test_feedback_get_completeds(
        int $groupmode,
        array $selectedgroups,
        int $expectedcount,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([
            'groupmode' => $groupmode,
            'groupmodeforce' => true,
        ]);
        $allgroups = [
            'groupa' => $this->getDataGenerator()->create_group(['courseid' => $course->id]),
            'groupb' => $this->getDataGenerator()->create_group(['courseid' => $course->id]),
            'groupc' => $this->getDataGenerator()->create_group(['courseid' => $course->id]),
        ];

        // Participant:  Role:           Groups:
        // student1a     student         groupa
        // student2a     student         groupa
        // student3b     student         groupb
        // teacher       editingteacher  no group .
        $student1a = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->getDataGenerator()->create_group_member([
            'groupid' => $allgroups['groupa']->id,
            'userid' => $student1a->id,
        ]);
        $student2a = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->getDataGenerator()->create_group_member([
            'groupid' => $allgroups['groupa']->id,
            'userid' => $student2a->id,
        ]);
        $student3b = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->getDataGenerator()->create_group_member([
            'groupid' => $allgroups['groupb']->id,
            'userid' => $student3b->id,
        ]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $activity = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        // Add a multichoice item to the feedback and create responses for it.
        /** @var  \mod_feedback_generator $feedbackgenerator */
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $item = $feedbackgenerator->create_item_multichoice($activity, ['values' => "y\nn"]);
        $feedbackgenerator->create_response([
            'userid' => $student1a->id,
            'cmid' => $cm->id,
            'anonymous' => false,
            $item->name => 'y',
        ]);
        $feedbackgenerator->create_response([
            'userid' => $student2a->id,
            'cmid' => $cm->id,
            'anonymous' => false,
            $item->name => 'n',
        ]);
        $feedbackgenerator->create_response([
            'userid' => $student3b->id,
            'cmid' => $cm->id,
            'anonymous' => false,
            $item->name => 'y',
        ]);

        $this->setUser($teacher);
        $groups = [];
        if (!empty($selectedgroups)) {
            foreach ($selectedgroups as $group) {
                if ($group === 'unexisting') {
                    $groups[666] = new \stdClass();
                } else {
                    $groups[$allgroups[$group]->id] = $allgroups[$group];
                }
            }
        }

        $result = feedback_get_completeds($activity, $groups);
        $count = feedback_get_completeds_count($activity, $groups);
        $this->assertCount($expectedcount, $result);
        $this->assertEquals($expectedcount, $count);
        foreach ($result as $completed) {
            $this->assertEquals($activity->id, $completed->feedback);
        }
    }

    /**
     * Data provider for test_feedback_get_completeds().
     *
     * @return array
     */
    public static function provider_feedback_get_completeds(): array {
        return [
            'Separate groups - All completeds, without filtering by groups' => [
                'groupmode' => SEPARATEGROUPS,
                'selectedgroups' => [],
                'expectedcount' => 3,
            ],
            'Separate groups - Filter by groupa' => [
                'groupmode' => SEPARATEGROUPS,
                'selectedgroups' => ['groupa'],
                'expectedcount' => 2,
            ],
            'Separate groups - Filter by groupb' => [
                'groupmode' => SEPARATEGROUPS,
                'selectedgroups' => ['groupb'],
                'expectedcount' => 1,
            ],
            'Separate groups - Filter by groupc' => [
                'groupmode' => SEPARATEGROUPS,
                'selectedgroups' => ['groupc'],
                'expectedcount' => 0,
            ],
            'Separate groups - Filter by groupa, groupb and groupc' => [
                'groupmode' => SEPARATEGROUPS,
                'selectedgroups' => ['groupa', 'groupb', 'groupc'],
                'expectedcount' => 3,
            ],
            'Separate groups - Unexisting groupid' => [
                'groupmode' => SEPARATEGROUPS,
                'selectedgroups' => ['unexisting'],
                'expectedcount' => 0,
            ],
            'Visible groups - All completeds, without filtering by groups' => [
                'groupmode' => VISIBLEGROUPS,
                'selectedgroups' => [],
                'expectedcount' => 3,
            ],
            'Visible groups - Filter by groupa' => [
                'groupmode' => VISIBLEGROUPS,
                'selectedgroups' => ['groupa'],
                'expectedcount' => 2,
            ],
            'Visible groups - Filter by groupb' => [
                'groupmode' => VISIBLEGROUPS,
                'selectedgroups' => ['groupb'],
                'expectedcount' => 1,
            ],
            'Visible groups - Filter by groupc' => [
                'groupmode' => VISIBLEGROUPS,
                'selectedgroups' => ['groupc'],
                'expectedcount' => 0,
            ],
            'Visible groups - Filter by groupa, groupb and groupc' => [
                'groupmode' => VISIBLEGROUPS,
                'selectedgroups' => ['groupa', 'groupb', 'groupc'],
                'expectedcount' => 3,
            ],
            'Visible groups - Unexisting groupid' => [
                'groupmode' => SEPARATEGROUPS,
                'selectedgroups' => ['unexisting'],
                'expectedcount' => 0,
            ],
        ];
    }
}
