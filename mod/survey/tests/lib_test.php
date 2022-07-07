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
 * Unit tests for mod_survey lib
 *
 * @package    mod_survey
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for mod_survey lib
 *
 * @package    mod_survey
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_survey_lib_testcase extends advanced_testcase {

    /**
     * Prepares things before this test case is initialised
     * @return void
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/survey/lib.php');
    }

    /**
     * Test survey_view
     * @return void
     */
    public function test_survey_view() {
        global $CFG;

        $CFG->enablecompletion = 1;
        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id),
                                                            array('completion' => 2, 'completionview' => 1));
        $context = context_module::instance($survey->cmid);
        $cm = get_coursemodule_from_instance('survey', $survey->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        survey_view($survey, $course, $cm, $context, 'form');

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_survey\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/survey/view.php', array('id' => $cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEquals('form', $event->other['viewed']);
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
        // Check completion status.
        $completion = new completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);

    }

    /**
     * Test survey_order_questions
     */
    public function test_survey_order_questions() {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id));

        $orderedquestionids = explode(',', $survey->questions);
        $surveyquestions = $DB->get_records_list("survey_questions", "id", $orderedquestionids);

        $questionsordered = survey_order_questions($surveyquestions, $orderedquestionids);

        // Check one by one the correct order.
        for ($i = 0; $i < count($orderedquestionids); $i++) {
            $this->assertEquals($orderedquestionids[$i], $questionsordered[$i]->id);
        }
    }

    /**
     * Test survey_save_answers
     */
    public function test_survey_save_answers() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id));
        $context = context_module::instance($survey->cmid);

        // Build our questions and responses array.
        $realquestions = array();
        $questions = survey_get_questions($survey);
        $i = 5;
        foreach ($questions as $q) {
            if ($q->type > 0) {
                if ($q->multi) {
                    $subquestions = survey_get_subquestions($q);
                    foreach ($subquestions as $sq) {
                        $key = 'q' . $sq->id;
                        $realquestions[$key] = $i % 5 + 1;
                        $i++;
                    }
                } else {
                    $key = 'q' . $q->id;
                    $realquestions[$key] = $i % 5 + 1;
                    $i++;
                }
            }
        }

        $sink = $this->redirectEvents();
        survey_save_answers($survey, $realquestions, $course, $context);

        // Check the stored answers, they must match.
        $dbanswers = $DB->get_records_menu('survey_answers', array('survey' => $survey->id), '', 'question, answer1');
        foreach ($realquestions as $key => $value) {
            $id = str_replace('q', '', $key);
            $this->assertEquals($value, $dbanswers[$id]);
        }

        // Check events.
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_survey\event\response_submitted', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($survey->id, $event->other['surveyid']);
    }

    public function test_survey_core_calendar_provide_event_action() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $survey->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_survey_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_survey_core_calendar_provide_event_action_for_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id));

        // Create a student and enrol into the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $survey->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_survey_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_survey_core_calendar_provide_event_action_as_non_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $survey->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Log out the user and set force login to true.
        \core\session\manager::init_empty_session();
        $CFG->forcelogin = true;

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_survey_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_survey_core_calendar_provide_event_action_already_completed() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('survey', $survey->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $survey->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_survey_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_survey_core_calendar_provide_event_action_already_completed_for_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Create 2 students and enrol them into the course.
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Get some additional data.
        $cm = get_coursemodule_from_instance('survey', $survey->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $survey->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed for the $student1.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm, $student1->id);

        // Now log in as $student2.
        $this->setUser($student2);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for $student1.
        $actionevent = mod_survey_core_calendar_provide_event_action($event, $factory, $student1->id);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid The course id.
     * @param int $instanceid The instance id.
     * @param string $eventtype The event type.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'survey';
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
    public function test_mod_survey_completion_get_active_rule_descriptions() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Two activities, both with automatic completion. One has the 'completionsubmit' rule, one doesn't.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 2]);
        $survey1 = $this->getDataGenerator()->create_module('survey', [
            'course' => $course->id,
            'completion' => 2,
            'completionsubmit' => 1,
        ]);
        $survey2 = $this->getDataGenerator()->create_module('survey', [
            'course' => $course->id,
            'completion' => 2,
            'completionsubmit' => 0,
        ]);
        $cm1 = cm_info::create(get_coursemodule_from_instance('survey', $survey1->id));
        $cm2 = cm_info::create(get_coursemodule_from_instance('survey', $survey2->id));

        // Data for the stdClass input type.
        // This type of input would occur when checking the default completion rules for an activity type, where we don't have
        // any access to cm_info, rather the input is a stdClass containing completion and customdata attributes, just like cm_info.
        $moddefaults = new stdClass();
        $moddefaults->customdata = ['customcompletionrules' => ['completionsubmit' => 1]];
        $moddefaults->completion = 2;

        $activeruledescriptions = [get_string('completionsubmit', 'survey')];
        $this->assertEquals(mod_survey_get_completion_active_rule_descriptions($cm1), $activeruledescriptions);
        $this->assertEquals(mod_survey_get_completion_active_rule_descriptions($cm2), []);
        $this->assertEquals(mod_survey_get_completion_active_rule_descriptions($moddefaults), $activeruledescriptions);
        $this->assertEquals(mod_survey_get_completion_active_rule_descriptions(new stdClass()), []);
    }
}
