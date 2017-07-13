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
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/mod/feedback/lib.php');

/**
 * Unit tests for (some of) mod/feedback/lib.php.
 *
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_feedback_lib_testcase extends advanced_testcase {

    public function test_feedback_initialise() {
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
            $this->assertTrue($structure->get_cm() instanceof cm_info);
            $this->assertEquals($feedback->cmid, $structure->get_cm()->id);
            $this->assertEquals($feedback->intro, $structure->get_feedback()->intro);
        }
    }

    /**
     * Tests for mod_feedback_refresh_events.
     */
    public function test_feedback_refresh_events() {
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
        $context = context_module::instance($cm->id);

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
    public function test_check_updates_since() {
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
        $cm = cm_info::create($cm);

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
    public function test_feedback_core_calendar_provide_event_action_open() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $now = time();
        $course = $this->getDataGenerator()->create_course();
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
     * Test calendar event provide action closed.
     */
    public function test_feedback_core_calendar_provide_event_action_closed() {
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
     * Test calendar event action open in future.
     *
     * @throws coding_exception
     */
    public function test_feedback_core_calendar_provide_event_action_open_in_future() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
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
     * Test calendar event with no time specified.
     *
     * @throws coding_exception
     */
    public function test_feedback_core_calendar_provide_event_action_no_time_specified() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
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
     * A user that cannot submit the feedback should not see the event.
     */
    public function test_feedback_core_calendar_is_event_visible_can_not_submit() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);
        $cm = get_coursemodule_from_instance('feedback', $feedback->id);
        $context = context_module::instance($cm->id);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id, 'manual');

        $this->setUser($user);

        assign_capability('mod/feedback:complete', CAP_PROHIBIT, $studentrole->id, $context);
        $context->mark_dirty();

        $visible = mod_feedback_core_calendar_is_event_visible($event);

        $this->assertFalse($visible);
    }

    /**
     * A user that can submit the feedback should see the event.
     */
    public function test_feedback_core_calendar_is_event_visible_can_submit() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);
        $cm = get_coursemodule_from_instance('feedback', $feedback->id);
        $context = context_module::instance($cm->id);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id, 'manual');

        $this->setUser($user);

        assign_capability('mod/feedback:complete', CAP_ALLOW, $studentrole->id, $context->id);
        $context->mark_dirty();

        $visible = mod_feedback_core_calendar_is_event_visible($event);

        $this->assertTrue($visible);
    }

    /**
     * A user that has already submitted feedback should not have an action.
     */
    public function test_feedback_core_calendar_provide_event_action_already_submitted() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);
        $event = $this->create_action_event($course->id, $feedback->id, FEEDBACK_EVENT_TYPE_OPEN);
        $cm = get_coursemodule_from_instance('feedback', $feedback->id);
        $context = context_module::instance($cm->id);

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
     * Creates an action event.
     *
     * @param int $courseid The course id.
     * @param int $instanceid The feedback id.
     * @param string $eventtype The event type. eg. FEEDBACK_EVENT_TYPE_OPEN.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename = 'feedback';
        $event->courseid = $courseid;
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
    public function test_mod_feedback_completion_get_active_rule_descriptions() {
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
        $cm1 = cm_info::create(get_coursemodule_from_instance('feedback', $feedback1->id));
        $cm2 = cm_info::create(get_coursemodule_from_instance('feedback', $feedback2->id));

        // Data for the stdClass input type.
        // This type of input would occur when checking the default completion rules for an activity type, where we don't have
        // any access to cm_info, rather the input is a stdClass containing completion and customdata attributes, just like cm_info.
        $moddefaults = new stdClass();
        $moddefaults->customdata = ['customcompletionrules' => ['completionsubmit' => 1]];
        $moddefaults->completion = 2;

        $activeruledescriptions = [get_string('completionsubmit', 'feedback')];
        $this->assertEquals(mod_feedback_get_completion_active_rule_descriptions($cm1), $activeruledescriptions);
        $this->assertEquals(mod_feedback_get_completion_active_rule_descriptions($cm2), []);
        $this->assertEquals(mod_feedback_get_completion_active_rule_descriptions($moddefaults), $activeruledescriptions);
        $this->assertEquals(mod_feedback_get_completion_active_rule_descriptions(new stdClass()), []);
    }
}
