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
 * SCORM module library functions tests
 *
 * @package    mod_scorm
 * @category   test
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
namespace mod_scorm;

use mod_scorm_get_completion_active_rule_descriptions;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/scorm/lib.php');

/**
 * SCORM module library functions tests
 *
 * @package    mod_scorm
 * @category   test
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
final class lib_test extends \advanced_testcase {

    /** @var \stdClass course record. */
    protected \stdClass $course;

    /** @var \stdClass activity record. */
    protected \stdClass $scorm;

    /** @var \core\context\module context instance. */
    protected \core\context\module $context;

    /** @var \stdClass */
    protected \stdClass $cm;

    /** @var \stdClass user record. */
    protected \stdClass $student;

    /** @var \stdClass user record. */
    protected \stdClass $teacher;

    /** @var \stdClass a fieldset object, false or exception if error not found. */
    protected \stdClass $studentrole;

    /** @var \stdClass a fieldset object, false or exception if error not found. */
    protected \stdClass $teacherrole;

    /**
     * Set up for every test
     */
    public function setUp(): void {
        global $DB;
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->scorm = $this->getDataGenerator()->create_module('scorm', array('course' => $this->course->id));
        $this->context = \context_module::instance($this->scorm->cmid);
        $this->cm = get_coursemodule_from_instance('scorm', $this->scorm->id);

        // Create users.
        $this->student = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');
    }

    /** Test scorm_check_mode
     *
     * @return void
     */
    public function test_scorm_check_mode(): void {
        global $CFG;

        $newattempt = 'on';
        $attempt = 1;
        $mode = 'normal';
        scorm_check_mode($this->scorm, $newattempt, $attempt, $this->student->id, $mode);
        $this->assertEquals('off', $newattempt);

        $scoes = scorm_get_scoes($this->scorm->id);
        $sco = array_pop($scoes);
        scorm_insert_track($this->student->id, $this->scorm->id, $sco->id, 1, 'cmi.core.lesson_status', 'completed');
        $newattempt = 'on';
        scorm_check_mode($this->scorm, $newattempt, $attempt, $this->student->id, $mode);
        $this->assertEquals('on', $newattempt);

        // Now do the same with a SCORM 2004 package.
        $record = new \stdClass();
        $record->course = $this->course->id;
        $record->packagefilepath = $CFG->dirroot.'/mod/scorm/tests/packages/RuntimeBasicCalls_SCORM20043rdEdition.zip';
        $scorm13 = $this->getDataGenerator()->create_module('scorm', $record);
        $newattempt = 'on';
        $attempt = 1;
        $mode = 'normal';
        scorm_check_mode($scorm13, $newattempt, $attempt, $this->student->id, $mode);
        $this->assertEquals('off', $newattempt);

        $scoes = scorm_get_scoes($scorm13->id);
        $sco = array_pop($scoes);
        scorm_insert_track($this->student->id, $scorm13->id, $sco->id, 1, 'cmi.completion_status', 'completed');

        $newattempt = 'on';
        $attempt = 1;
        $mode = 'normal';
        scorm_check_mode($scorm13, $newattempt, $attempt, $this->student->id, $mode);
        $this->assertEquals('on', $newattempt);
    }

    /**
     * Test scorm_view
     * @return void
     */
    public function test_scorm_view(): void {
        global $CFG;

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        scorm_view($this->scorm, $this->course, $this->cm, $this->context);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_scorm\event\course_module_viewed', $event);
        $this->assertEquals($this->context, $event->get_context());
        $url = new \moodle_url('/mod/scorm/view.php', array('id' => $this->cm->id));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test scorm_get_availability_status and scorm_require_available
     * @return void
     */
    public function test_scorm_check_and_require_available(): void {
        global $DB;

        $this->setAdminUser();

        // User override case.
        $this->scorm->timeopen = time() + DAYSECS;
        $this->scorm->timeclose = time() - DAYSECS;
        list($status, $warnings) = scorm_get_availability_status($this->scorm, true, $this->context);
        $this->assertEquals(true, $status);
        $this->assertCount(0, $warnings);

        // Now check with a student.
        list($status, $warnings) = scorm_get_availability_status($this->scorm, true, $this->context, $this->student->id);
        $this->assertEquals(false, $status);
        $this->assertCount(2, $warnings);
        $this->assertArrayHasKey('notopenyet', $warnings);
        $this->assertArrayHasKey('expired', $warnings);
        $this->assertEquals(userdate($this->scorm->timeopen), $warnings['notopenyet']);
        $this->assertEquals(userdate($this->scorm->timeclose), $warnings['expired']);

        // Reset the scorm's times.
        $this->scorm->timeopen = $this->scorm->timeclose = 0;

        // Set to the student user.
        self::setUser($this->student);

        // Usual case.
        list($status, $warnings) = scorm_get_availability_status($this->scorm, false);
        $this->assertEquals(true, $status);
        $this->assertCount(0, $warnings);

        // SCORM not open.
        $this->scorm->timeopen = time() + DAYSECS;
        list($status, $warnings) = scorm_get_availability_status($this->scorm, false);
        $this->assertEquals(false, $status);
        $this->assertCount(1, $warnings);

        // SCORM closed.
        $this->scorm->timeopen = 0;
        $this->scorm->timeclose = time() - DAYSECS;
        list($status, $warnings) = scorm_get_availability_status($this->scorm, false);
        $this->assertEquals(false, $status);
        $this->assertCount(1, $warnings);

        // SCORM not open and closed.
        $this->scorm->timeopen = time() + DAYSECS;
        list($status, $warnings) = scorm_get_availability_status($this->scorm, false);
        $this->assertEquals(false, $status);
        $this->assertCount(2, $warnings);

        // Now additional checkings with different parameters values.
        list($status, $warnings) = scorm_get_availability_status($this->scorm, true, $this->context);
        $this->assertEquals(false, $status);
        $this->assertCount(2, $warnings);

        // SCORM not open.
        $this->scorm->timeopen = time() + DAYSECS;
        $this->scorm->timeclose = 0;
        list($status, $warnings) = scorm_get_availability_status($this->scorm, true, $this->context);
        $this->assertEquals(false, $status);
        $this->assertCount(1, $warnings);

        // SCORM closed.
        $this->scorm->timeopen = 0;
        $this->scorm->timeclose = time() - DAYSECS;
        list($status, $warnings) = scorm_get_availability_status($this->scorm, true, $this->context);
        $this->assertEquals(false, $status);
        $this->assertCount(1, $warnings);

        // SCORM not open and closed.
        $this->scorm->timeopen = time() + DAYSECS;
        list($status, $warnings) = scorm_get_availability_status($this->scorm, true, $this->context);
        $this->assertEquals(false, $status);
        $this->assertCount(2, $warnings);

        // As teacher now.
        self::setUser($this->teacher);

        // SCORM not open and closed.
        $this->scorm->timeopen = time() + DAYSECS;
        list($status, $warnings) = scorm_get_availability_status($this->scorm, false);
        $this->assertEquals(false, $status);
        $this->assertCount(2, $warnings);

        // Now, we use the special capability.
        // SCORM not open and closed.
        $this->scorm->timeopen = time() + DAYSECS;
        list($status, $warnings) = scorm_get_availability_status($this->scorm, true, $this->context);
        $this->assertEquals(true, $status);
        $this->assertCount(0, $warnings);

        // Check exceptions does not broke anything.
        scorm_require_available($this->scorm, true, $this->context);
        // Now, expect exceptions.
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string("notopenyet", "scorm", userdate($this->scorm->timeopen)));

        // Now as student other condition.
        self::setUser($this->student);
        $this->scorm->timeopen = 0;
        $this->scorm->timeclose = time() - DAYSECS;

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string("expired", "scorm", userdate($this->scorm->timeclose)));
        scorm_require_available($this->scorm, false);
    }

    /**
     * Test scorm_get_last_completed_attempt
     *
     * @return void
     */
    public function test_scorm_get_last_completed_attempt(): void {
        $this->assertEquals(1, scorm_get_last_completed_attempt($this->scorm->id, $this->student->id));
    }

    public function test_scorm_core_calendar_provide_event_action_open(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a scorm activity.
        $scorm = $this->getDataGenerator()->create_module('scorm', array('course' => $course->id,
            'timeopen' => time() - DAYSECS, 'timeclose' => time() + DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $scorm->id, SCORM_EVENT_TYPE_OPEN);

        // Only students see scorm events.
        $this->setUser($this->student);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_scorm_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('enter', 'scorm'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_scorm_core_calendar_provide_event_action_closed(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a scorm activity.
        $scorm = $this->getDataGenerator()->create_module('scorm', array('course' => $course->id,
            'timeclose' => time() - DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $scorm->id, SCORM_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_scorm_core_calendar_provide_event_action($event, $factory);

        // No event on the dashboard if module is closed.
        $this->assertNull($actionevent);
    }

    public function test_scorm_core_calendar_provide_event_action_open_in_future(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a scorm activity.
        $scorm = $this->getDataGenerator()->create_module('scorm', array('course' => $course->id,
            'timeopen' => time() + DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $scorm->id, SCORM_EVENT_TYPE_OPEN);

        // Only students see scorm events.
        $this->setUser($this->student);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_scorm_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('enter', 'scorm'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    public function test_scorm_core_calendar_provide_event_action_with_different_user_as_admin(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a scorm activity.
        $scorm = $this->getDataGenerator()->create_module('scorm', array('course' => $course->id,
            'timeopen' => time() + DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $scorm->id, SCORM_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event override with a passed in user.
        $actionevent = mod_scorm_core_calendar_provide_event_action($event, $factory, $this->student->id);
        $actionevent2 = mod_scorm_core_calendar_provide_event_action($event, $factory);

        // Only students see scorm events.
        $this->assertNull($actionevent2);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('enter', 'scorm'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    public function test_scorm_core_calendar_provide_event_action_no_time_specified(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a scorm activity.
        $scorm = $this->getDataGenerator()->create_module('scorm', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $scorm->id, SCORM_EVENT_TYPE_OPEN);

        // Only students see scorm events.
        $this->setUser($this->student);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_scorm_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('enter', 'scorm'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_scorm_core_calendar_provide_event_action_already_completed(): void {
        $this->resetAfterTest();
        set_config('enablecompletion', 1);
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $scorm = $this->getDataGenerator()->create_module('scorm', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('scorm', $scorm->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $scorm->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_scorm_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_scorm_core_calendar_provide_event_action_already_completed_for_user(): void {
        $this->resetAfterTest();
        set_config('enablecompletion', 1);
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $scorm = $this->getDataGenerator()->create_module('scorm', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Get some additional data.
        $cm = get_coursemodule_from_instance('scorm', $scorm->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $scorm->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed for the student.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm, $student->id);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_scorm_core_calendar_provide_event_action($event, $factory, $student->id);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid
     * @param int $instanceid The data id.
     * @param string $eventtype The event type. eg. DATA_EVENT_TYPE_OPEN.
     * @param int|null $timestart The start timestamp for the event
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype, $timestart = null) {
        $event = new \stdClass();
        $event->name = 'Calendar event';
        $event->modulename = 'scorm';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->eventtype = $eventtype;

        if ($timestart) {
            $event->timestart = $timestart;
        } else {
            $event->timestart = time();
        }

        return \calendar_event::create($event);
    }

    /**
     * Test the callback responsible for returning the completion rule descriptions.
     * This function should work given either an instance of the module (cm_info), such as when checking the active rules,
     * or if passed a stdClass of similar structure, such as when checking the the default completion settings for a mod type.
     */
    public function test_mod_scorm_completion_get_active_rule_descriptions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Two activities, both with automatic completion. One has the 'completionsubmit' rule, one doesn't.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 2]);
        $scorm1 = $this->getDataGenerator()->create_module('scorm', [
            'course' => $course->id,
            'completion' => 2,
            'completionstatusrequired' => 6,
            'completionscorerequired' => 5,
            'completionstatusallscos' => 1
        ]);
        $scorm2 = $this->getDataGenerator()->create_module('scorm', [
            'course' => $course->id,
            'completion' => 2,
            'completionstatusrequired' => null,
            'completionscorerequired' => null,
            'completionstatusallscos' => null
        ]);
        $cm1 = \cm_info::create(get_coursemodule_from_instance('scorm', $scorm1->id));
        $cm2 = \cm_info::create(get_coursemodule_from_instance('scorm', $scorm2->id));

        // Data for the stdClass input type.
        // This type of input would occur when checking the default completion rules for an activity type, where we don't have
        // any access to cm_info, rather the input is a stdClass containing completion and customdata attributes, just like cm_info.
        $moddefaults = new \stdClass();
        $moddefaults->customdata = ['customcompletionrules' => [
            'completionstatusrequired' => 6,
            'completionscorerequired' => 5,
            'completionstatusallscos' => 1
        ]];
        $moddefaults->completion = 2;

        // Determine the selected statuses using a bitwise operation.
        $cvalues = array();
        foreach (scorm_status_options(true) as $key => $value) {
            if (($scorm1->completionstatusrequired & $key) == $key) {
                $cvalues[] = $value;
            }
        }
        $statusstring = implode(', ', $cvalues);

        $activeruledescriptions = [
            get_string('completionstatusrequireddesc', 'scorm', $statusstring),
            get_string('completionscorerequireddesc', 'scorm', $scorm1->completionscorerequired),
            get_string('completionstatusallscos', 'scorm'),
        ];
        $this->assertEquals(mod_scorm_get_completion_active_rule_descriptions($cm1), $activeruledescriptions);
        $this->assertEquals(mod_scorm_get_completion_active_rule_descriptions($cm2), []);
        $this->assertEquals(mod_scorm_get_completion_active_rule_descriptions($moddefaults), $activeruledescriptions);
        $this->assertEquals(mod_scorm_get_completion_active_rule_descriptions(new \stdClass()), []);
    }

    /**
     * An unkown event type should not change the scorm instance.
     */
    public function test_mod_scorm_core_calendar_event_timestart_updated_unknown_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $scormgenerator = $generator->get_plugin_generator('mod_scorm');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $scorm = $scormgenerator->create_instance(['course' => $course->id]);
        $scorm->timeopen = $timeopen;
        $scorm->timeclose = $timeclose;
        $DB->update_record('scorm', $scorm);

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'scorm',
            'instance' => $scorm->id,
            'eventtype' => SCORM_EVENT_TYPE_OPEN . "SOMETHING ELSE",
            'timestart' => 1,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        mod_scorm_core_calendar_event_timestart_updated($event, $scorm);

        $scorm = $DB->get_record('scorm', ['id' => $scorm->id]);
        $this->assertEquals($timeopen, $scorm->timeopen);
        $this->assertEquals($timeclose, $scorm->timeclose);
    }

    /**
     * A SCORM_EVENT_TYPE_OPEN event should update the timeopen property of
     * the scorm activity.
     */
    public function test_mod_scorm_core_calendar_event_timestart_updated_open_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $scormgenerator = $generator->get_plugin_generator('mod_scorm');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $timemodified = 1;
        $newtimeopen = $timeopen - DAYSECS;
        $scorm = $scormgenerator->create_instance(['course' => $course->id]);
        $scorm->timeopen = $timeopen;
        $scorm->timeclose = $timeclose;
        $scorm->timemodified = $timemodified;
        $DB->update_record('scorm', $scorm);

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'scorm',
            'instance' => $scorm->id,
            'eventtype' => SCORM_EVENT_TYPE_OPEN,
            'timestart' => $newtimeopen,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        // Trigger and capture the event when adding a contact.
        $sink = $this->redirectEvents();

        mod_scorm_core_calendar_event_timestart_updated($event, $scorm);

        $triggeredevents = $sink->get_events();
        $moduleupdatedevents = array_filter($triggeredevents, function($e) {
            return is_a($e, 'core\event\course_module_updated');
        });

        $scorm = $DB->get_record('scorm', ['id' => $scorm->id]);
        // Ensure the timeopen property matches the event timestart.
        $this->assertEquals($newtimeopen, $scorm->timeopen);
        // Ensure the timeclose isn't changed.
        $this->assertEquals($timeclose, $scorm->timeclose);
        // Ensure the timemodified property has been changed.
        $this->assertNotEquals($timemodified, $scorm->timemodified);
        // Confirm that a module updated event is fired when the module
        // is changed.
        $this->assertNotEmpty($moduleupdatedevents);
    }

    /**
     * A SCORM_EVENT_TYPE_CLOSE event should update the timeclose property of
     * the scorm activity.
     */
    public function test_mod_scorm_core_calendar_event_timestart_updated_close_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $scormgenerator = $generator->get_plugin_generator('mod_scorm');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $timemodified = 1;
        $newtimeclose = $timeclose + DAYSECS;
        $scorm = $scormgenerator->create_instance(['course' => $course->id]);
        $scorm->timeopen = $timeopen;
        $scorm->timeclose = $timeclose;
        $scorm->timemodified = $timemodified;
        $DB->update_record('scorm', $scorm);

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'scorm',
            'instance' => $scorm->id,
            'eventtype' => SCORM_EVENT_TYPE_CLOSE,
            'timestart' => $newtimeclose,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        // Trigger and capture the event when adding a contact.
        $sink = $this->redirectEvents();

        mod_scorm_core_calendar_event_timestart_updated($event, $scorm);

        $triggeredevents = $sink->get_events();
        $moduleupdatedevents = array_filter($triggeredevents, function($e) {
            return is_a($e, 'core\event\course_module_updated');
        });

        $scorm = $DB->get_record('scorm', ['id' => $scorm->id]);
        // Ensure the timeclose property matches the event timestart.
        $this->assertEquals($newtimeclose, $scorm->timeclose);
        // Ensure the timeopen isn't changed.
        $this->assertEquals($timeopen, $scorm->timeopen);
        // Ensure the timemodified property has been changed.
        $this->assertNotEquals($timemodified, $scorm->timemodified);
        // Confirm that a module updated event is fired when the module
        // is changed.
        $this->assertNotEmpty($moduleupdatedevents);
    }

    /**
     * An unkown event type should not have any limits
     */
    public function test_mod_scorm_core_calendar_get_valid_event_timestart_range_unknown_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $scorm = new \stdClass();
        $scorm->timeopen = $timeopen;
        $scorm->timeclose = $timeclose;

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'scorm',
            'instance' => 1,
            'eventtype' => SCORM_EVENT_TYPE_OPEN . "SOMETHING ELSE",
            'timestart' => 1,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        list ($min, $max) = mod_scorm_core_calendar_get_valid_event_timestart_range($event, $scorm);
        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * The open event should be limited by the scorm's timeclose property, if it's set.
     */
    public function test_mod_scorm_core_calendar_get_valid_event_timestart_range_open_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $scorm = new \stdClass();
        $scorm->timeopen = $timeopen;
        $scorm->timeclose = $timeclose;

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'scorm',
            'instance' => 1,
            'eventtype' => SCORM_EVENT_TYPE_OPEN,
            'timestart' => 1,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        // The max limit should be bounded by the timeclose value.
        list ($min, $max) = mod_scorm_core_calendar_get_valid_event_timestart_range($event, $scorm);

        $this->assertNull($min);
        $this->assertEquals($timeclose, $max[0]);

        // No timeclose value should result in no upper limit.
        $scorm->timeclose = 0;
        list ($min, $max) = mod_scorm_core_calendar_get_valid_event_timestart_range($event, $scorm);

        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * The close event should be limited by the scorm's timeopen property, if it's set.
     */
    public function test_mod_scorm_core_calendar_get_valid_event_timestart_range_close_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $scorm = new \stdClass();
        $scorm->timeopen = $timeopen;
        $scorm->timeclose = $timeclose;

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'scorm',
            'instance' => 1,
            'eventtype' => SCORM_EVENT_TYPE_CLOSE,
            'timestart' => 1,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        // The max limit should be bounded by the timeclose value.
        list ($min, $max) = mod_scorm_core_calendar_get_valid_event_timestart_range($event, $scorm);

        $this->assertEquals($timeopen, $min[0]);
        $this->assertNull($max);

        // No timeclose value should result in no upper limit.
        $scorm->timeopen = 0;
        list ($min, $max) = mod_scorm_core_calendar_get_valid_event_timestart_range($event, $scorm);

        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * A user who does not have capabilities to add events to the calendar should be able to create a SCORM.
     */
    public function test_creation_with_no_calendar_capabilities(): void {
        $this->resetAfterTest();
        $course = self::getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $user = self::getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $roleid = self::getDataGenerator()->create_role();
        self::getDataGenerator()->role_assign($roleid, $user->id, $context->id);
        assign_capability('moodle/calendar:manageentries', CAP_PROHIBIT, $roleid, $context, true);
        $generator = self::getDataGenerator()->get_plugin_generator('mod_scorm');
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
}
