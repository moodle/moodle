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
 * Quiz events tests.
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2013 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quiz\event;

use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;
use context_module;

/**
 * Unit tests for quiz events.
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2013 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_test extends \advanced_testcase {

    /**
     * Setup a quiz.
     *
     * @return quiz_settings the generated quiz.
     */
    protected function prepare_quiz() {

        $this->resetAfterTest(true);

        // Create a course
        $course = $this->getDataGenerator()->create_course();

        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'questionsperpage' => 0,
                'grade' => 100.0, 'sumgrades' => 2]);

        $cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id);

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $saq = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        $numq = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);

        // Add them to the quiz.
        quiz_add_quiz_question($saq->id, $quiz);
        quiz_add_quiz_question($numq->id, $quiz);

        // Make a user to do the quiz.
        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);

        return quiz_settings::create($quiz->id, $user1->id);
    }

    /**
     * Setup a quiz attempt at the quiz created by {@link prepare_quiz()}.
     *
     * @param \mod_quiz\quiz_settings $quizobj the generated quiz.
     * @param bool $ispreview Make the attempt a preview attempt when true.
     * @return array with three elements, array($quizobj, $quba, $attempt)
     */
    protected function prepare_quiz_attempt($quizobj, $ispreview = false) {
        // Start the attempt.
        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, $ispreview);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        return [$quizobj, $quba, $attempt];
    }

    /**
     * Setup some convenience test data with a single attempt.
     *
     * @param bool $ispreview Make the attempt a preview attempt when true.
     * @return array with three elements, array($quizobj, $quba, $attempt)
     */
    protected function prepare_quiz_data($ispreview = false) {
        $quizobj = $this->prepare_quiz();
        return $this->prepare_quiz_attempt($quizobj, $ispreview);
    }

    public function test_attempt_submitted() {

        list($quizobj, $quba, $attempt) = $this->prepare_quiz_data();
        $attemptobj = quiz_attempt::create($attempt->id);

        // Catch the event.
        $sink = $this->redirectEvents();

        $timefinish = time();
        $attemptobj->process_finish($timefinish, false);
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(3, $events);
        $event = $events[2];
        $this->assertInstanceOf('\mod_quiz\event\attempt_submitted', $event);
        $this->assertEquals('quiz_attempts', $event->objecttable);
        $this->assertEquals($quizobj->get_context(), $event->get_context());
        $this->assertEquals($attempt->userid, $event->relateduserid);
        $this->assertEquals(null, $event->other['submitterid']); // Should be the user, but PHP Unit complains...
        $this->assertEventContextNotUsed($event);
    }

    public function test_attempt_becameoverdue() {

        list($quizobj, $quba, $attempt) = $this->prepare_quiz_data();
        $attemptobj = quiz_attempt::create($attempt->id);

        // Catch the event.
        $sink = $this->redirectEvents();
        $timefinish = time();
        $attemptobj->process_going_overdue($timefinish, false);
        $events = $sink->get_events();
        $sink->close();

        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf('\mod_quiz\event\attempt_becameoverdue', $event);
        $this->assertEquals('quiz_attempts', $event->objecttable);
        $this->assertEquals($quizobj->get_context(), $event->get_context());
        $this->assertEquals($attempt->userid, $event->relateduserid);
        $this->assertNotEmpty($event->get_description());
        // Submitterid should be the user, but as we are in PHP Unit, CLI_SCRIPT is set to true which sets null in submitterid.
        $this->assertEquals(null, $event->other['submitterid']);
        $this->assertEventContextNotUsed($event);
    }

    public function test_attempt_abandoned() {

        list($quizobj, $quba, $attempt) = $this->prepare_quiz_data();
        $attemptobj = quiz_attempt::create($attempt->id);

        // Catch the event.
        $sink = $this->redirectEvents();
        $timefinish = time();
        $attemptobj->process_abandon($timefinish, false);
        $events = $sink->get_events();
        $sink->close();

        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf('\mod_quiz\event\attempt_abandoned', $event);
        $this->assertEquals('quiz_attempts', $event->objecttable);
        $this->assertEquals($quizobj->get_context(), $event->get_context());
        $this->assertEquals($attempt->userid, $event->relateduserid);
        // Submitterid should be the user, but as we are in PHP Unit, CLI_SCRIPT is set to true which sets null in submitterid.
        $this->assertEquals(null, $event->other['submitterid']);
        $this->assertEventContextNotUsed($event);
    }

    public function test_attempt_started() {
        $quizobj = $this->prepare_quiz();

        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        quiz_attempt_save_started($quizobj, $quba, $attempt);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_started', $event);
        $this->assertEquals('quiz_attempts', $event->objecttable);
        $this->assertEquals($attempt->id, $event->objectid);
        $this->assertEquals($attempt->userid, $event->relateduserid);
        $this->assertEquals($quizobj->get_context(), $event->get_context());
        $this->assertEquals(\context_module::instance($quizobj->get_cmid()), $event->get_context());
    }

    /**
     * Test the attempt question restarted event.
     *
     * There is no external API for replacing a question, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_attempt_question_restarted() {
        list($quizobj, $quba, $attempt) = $this->prepare_quiz_data();

        $params = [
            'objectid' => 1,
            'relateduserid' => 2,
            'courseid' => $quizobj->get_courseid(),
            'context' => \context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'page' => 2,
                'slot' => 3,
                'newquestionid' => 2
            ]
        ];
        $event = \mod_quiz\event\attempt_question_restarted::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_question_restarted', $event);
        $this->assertEquals(\context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the attempt updated event.
     *
     * There is no external API for updating an attempt, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_attempt_updated() {
        list($quizobj, $quba, $attempt) = $this->prepare_quiz_data();

        $params = [
            'objectid' => 1,
            'relateduserid' => 2,
            'courseid' => $quizobj->get_courseid(),
            'context' => \context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'page' => 0
            ]
        ];
        $event = \mod_quiz\event\attempt_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_updated', $event);
        $this->assertEquals(\context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the attempt auto-saved event.
     *
     * There is no external API for auto-saving an attempt, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_attempt_autosaved() {
        list($quizobj, $quba, $attempt) = $this->prepare_quiz_data();

        $params = [
            'objectid' => 1,
            'relateduserid' => 2,
            'courseid' => $quizobj->get_courseid(),
            'context' => \context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'page' => 0
            ]
        ];

        $event = \mod_quiz\event\attempt_autosaved::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_autosaved', $event);
        $this->assertEquals(\context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the edit page viewed event.
     *
     * There is no external API for updating a quiz, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_edit_page_viewed() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $params = [
            'courseid' => $course->id,
            'context' => \context_module::instance($quiz->cmid),
            'other' => [
                'quizid' => $quiz->id
            ]
        ];
        $event = \mod_quiz\event\edit_page_viewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\edit_page_viewed', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the attempt deleted event.
     */
    public function test_attempt_deleted() {
        list($quizobj, $quba, $attempt) = $this->prepare_quiz_data();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        quiz_delete_attempt($attempt, $quizobj->get_quiz());
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_deleted', $event);
        $this->assertEquals(\context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that preview attempt deletions are not logged.
     */
    public function test_preview_attempt_deleted() {
        // Create quiz with preview attempt.
        list($quizobj, $quba, $previewattempt) = $this->prepare_quiz_data(true);

        // Delete a preview attempt, capturing events.
        $sink = $this->redirectEvents();
        quiz_delete_attempt($previewattempt, $quizobj->get_quiz());

        // Verify that no events were generated.
        $this->assertEmpty($sink->get_events());
    }

    /**
     * Test the report viewed event.
     *
     * There is no external API for viewing reports, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_report_viewed() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $params = [
            'context' => $context = \context_module::instance($quiz->cmid),
            'other' => [
                'quizid' => $quiz->id,
                'reportname' => 'overview'
            ]
        ];
        $event = \mod_quiz\event\report_viewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\report_viewed', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the attempt reviewed event.
     *
     * There is no external API for reviewing attempts, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_attempt_reviewed() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $params = [
            'objectid' => 1,
            'relateduserid' => 2,
            'courseid' => $course->id,
            'context' => \context_module::instance($quiz->cmid),
            'other' => [
                'quizid' => $quiz->id
            ]
        ];
        $event = \mod_quiz\event\attempt_reviewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_reviewed', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the attempt summary viewed event.
     *
     * There is no external API for viewing the attempt summary, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_attempt_summary_viewed() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $params = [
            'objectid' => 1,
            'relateduserid' => 2,
            'courseid' => $course->id,
            'context' => \context_module::instance($quiz->cmid),
            'other' => [
                'quizid' => $quiz->id
            ]
        ];
        $event = \mod_quiz\event\attempt_summary_viewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_summary_viewed', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the user override created event.
     *
     * There is no external API for creating a user override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_user_override_created() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $params = [
            'objectid' => 1,
            'relateduserid' => 2,
            'context' => \context_module::instance($quiz->cmid),
            'other' => [
                'quizid' => $quiz->id
            ]
        ];
        $event = \mod_quiz\event\user_override_created::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\user_override_created', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the group override created event.
     *
     * There is no external API for creating a group override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_group_override_created() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $params = [
            'objectid' => 1,
            'context' => \context_module::instance($quiz->cmid),
            'other' => [
                'quizid' => $quiz->id,
                'groupid' => 2
            ]
        ];
        $event = \mod_quiz\event\group_override_created::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\group_override_created', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the user override updated event.
     *
     * There is no external API for updating a user override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_user_override_updated() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $params = [
            'objectid' => 1,
            'relateduserid' => 2,
            'context' => \context_module::instance($quiz->cmid),
            'other' => [
                'quizid' => $quiz->id
            ]
        ];
        $event = \mod_quiz\event\user_override_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\user_override_updated', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the group override updated event.
     *
     * There is no external API for updating a group override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_group_override_updated() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $params = [
            'objectid' => 1,
            'context' => \context_module::instance($quiz->cmid),
            'other' => [
                'quizid' => $quiz->id,
                'groupid' => 2
            ]
        ];
        $event = \mod_quiz\event\group_override_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\group_override_updated', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the user override deleted event.
     */
    public function test_user_override_deleted() {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $quizsettings = quiz_settings::create($quiz->id);

        // Create an override.
        $override = new \stdClass();
        $override->quiz = $quiz->id;
        $override->userid = 2;
        $override->id = $DB->insert_record('quiz_overrides', $override);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $quizsettings->get_override_manager()->delete_overrides(overrides: [$override]);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\user_override_deleted', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the group override deleted event.
     */
    public function test_group_override_deleted() {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $quizsettings = quiz_settings::create($quiz->id);

        // Create an override.
        $override = new \stdClass();
        $override->quiz = $quiz->id;
        $override->groupid = 2;
        $override->id = $DB->insert_record('quiz_overrides', $override);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $quizsettings->get_override_manager()->delete_overrides(overrides: [$override]);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\group_override_deleted', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the attempt viewed event.
     *
     * There is no external API for continuing an attempt, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_attempt_viewed() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $params = [
            'objectid' => 1,
            'relateduserid' => 2,
            'courseid' => $course->id,
            'context' => \context_module::instance($quiz->cmid),
            'other' => [
                'quizid' => $quiz->id,
                'page' => 0
            ]
        ];
        $event = \mod_quiz\event\attempt_viewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_viewed', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the attempt previewed event.
     */
    public function test_attempt_preview_started() {
        $quizobj = $this->prepare_quiz();

        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, true);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        quiz_attempt_save_started($quizobj, $quba, $attempt);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_preview_started', $event);
        $this->assertEquals(\context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the question manually graded event.
     *
     * There is no external API for manually grading a question, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_question_manually_graded() {
        list($quizobj, $quba, $attempt) = $this->prepare_quiz_data();

        $params = [
            'objectid' => 1,
            'courseid' => $quizobj->get_courseid(),
            'context' => \context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'attemptid' => 2,
                'slot' => 3
            ]
        ];
        $event = \mod_quiz\event\question_manually_graded::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\question_manually_graded', $event);
        $this->assertEquals(\context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the attempt regraded event.
     *
     * There is no external API for regrading attempts, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_attempt_regraded() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $params = [
            'objectid' => 1,
            'relateduserid' => 2,
            'courseid' => $course->id,
            'context' => \context_module::instance($quiz->cmid),
            'other' => [
                'quizid' => $quiz->id
            ]
        ];
        $event = \mod_quiz\event\attempt_regraded::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_regraded', $event);
        $this->assertEquals(\context_module::instance($quiz->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the attempt notify manual graded event.
     * There is no external API for notification email when manual grading of user's attempt is completed,
     * so the unit test will simply create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_attempt_manual_grading_completed() {
        $this->resetAfterTest();
        list($quizobj, $quba, $attempt) = $this->prepare_quiz_data();
        $attemptobj = quiz_attempt::create($attempt->id);

        $params = [
            'objectid' => $attemptobj->get_attemptid(),
            'relateduserid' => $attemptobj->get_userid(),
            'courseid' => $attemptobj->get_course()->id,
            'context' => \context_module::instance($attemptobj->get_cmid()),
            'other' => [
                'quizid' => $attemptobj->get_quizid()
            ]
        ];
        $event = \mod_quiz\event\attempt_manual_grading_completed::create($params);

        // Catch the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_quiz\event\attempt_manual_grading_completed', $event);
        $this->assertEquals('quiz_attempts', $event->objecttable);
        $this->assertEquals($quizobj->get_context(), $event->get_context());
        $this->assertEquals($attempt->userid, $event->relateduserid);
        $this->assertNotEmpty($event->get_description());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the page break created event.
     *
     * There is no external API for creating page break, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_page_break_created() {
        $quizobj = $this->prepare_quiz();

        $params = [
            'objectid' => 1,
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'slotnumber' => 3,
            ]
        ];
        $event = \mod_quiz\event\page_break_created::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\page_break_created', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the page break deleted event.
     *
     * There is no external API for deleting page break, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_page_deleted_created() {
        $quizobj = $this->prepare_quiz();

        $params = [
            'objectid' => 1,
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'slotnumber' => 3,
            ]
        ];
        $event = \mod_quiz\event\page_break_deleted::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\page_break_deleted', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the quiz grade updated event.
     *
     * There is no external API for updating quiz grade, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_quiz_grade_updated() {
        $quizobj = $this->prepare_quiz();

        $params = [
            'objectid' => $quizobj->get_quizid(),
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => [
                'oldgrade' => 1,
                'newgrade' => 3,
            ]
        ];
        $event = \mod_quiz\event\quiz_grade_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\quiz_grade_updated', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the quiz re-paginated event.
     *
     * There is no external API for re-paginating quiz, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_quiz_repaginated() {
        $quizobj = $this->prepare_quiz();

        $params = [
            'objectid' => $quizobj->get_quizid(),
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => [
                'slotsperpage' => 3,
            ]
        ];
        $event = \mod_quiz\event\quiz_repaginated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\quiz_repaginated', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the section break created event.
     *
     * There is no external API for creating section break, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_section_break_created() {
        $quizobj = $this->prepare_quiz();

        $params = [
            'objectid' => 1,
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'firstslotid' => 1,
                'firstslotnumber' => 2,
                'title' => 'New title'
            ]
        ];
        $event = \mod_quiz\event\section_break_created::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\section_break_created', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertStringContainsString($params['other']['title'], $event->get_description());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the section break deleted event.
     *
     * There is no external API for deleting section break, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_section_break_deleted() {
        $quizobj = $this->prepare_quiz();

        $params = [
            'objectid' => 1,
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'firstslotid' => 1,
                'firstslotnumber' => 2
            ]
        ];
        $event = \mod_quiz\event\section_break_deleted::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\section_break_deleted', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the section shuffle updated event.
     *
     * There is no external API for updating section shuffle, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_section_shuffle_updated() {
        $quizobj = $this->prepare_quiz();

        $params = [
            'objectid' => 1,
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'firstslotnumber' => 2,
                'shuffle' => true
            ]
        ];
        $event = \mod_quiz\event\section_shuffle_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\section_shuffle_updated', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the section title updated event.
     *
     * There is no external API for updating section title, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_section_title_updated() {
        $quizobj = $this->prepare_quiz();

        $params = [
            'objectid' => 1,
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'firstslotid' => 1,
                'firstslotnumber' => 2,
                'newtitle' => 'New title'
            ]
        ];
        $event = \mod_quiz\event\section_title_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\section_title_updated', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertStringContainsString($params['other']['newtitle'], $event->get_description());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the slot created event.
     *
     * There is no external API for creating slot, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_slot_created() {
        $quizobj = $this->prepare_quiz();

        $params = [
            'objectid' => 1,
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'slotnumber' => 1,
                'page' => 1
            ]
        ];
        $event = \mod_quiz\event\slot_created::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\slot_created', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the slot deleted event.
     *
     * There is no external API for deleting slot, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_slot_deleted() {
        $quizobj = $this->prepare_quiz();

        $params = [
            'objectid' => 1,
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'slotnumber' => 1,
            ]
        ];
        $event = \mod_quiz\event\slot_deleted::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\slot_deleted', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the slot mark updated event.
     *
     * There is no external API for updating slot mark, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_slot_mark_updated() {
        $quizobj = $this->prepare_quiz();

        $params = [
            'objectid' => 1,
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'previousmaxmark' => 1,
                'newmaxmark' => 2,
            ]
        ];
        $event = \mod_quiz\event\slot_mark_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\slot_mark_updated', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the slot moved event.
     *
     * There is no external API for moving slot, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_slot_moved() {
        $quizobj = $this->prepare_quiz();

        $params = [
            'objectid' => 1,
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'previousslotnumber' => 1,
                'afterslotnumber' => 2,
                'page' => 1
            ]
        ];
        $event = \mod_quiz\event\slot_moved::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\slot_moved', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the slot require previous updated event.
     *
     * There is no external API for updating slot require previous option, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_slot_requireprevious_updated() {
        $quizobj = $this->prepare_quiz();

        $params = [
            'objectid' => 1,
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => [
                'quizid' => $quizobj->get_quizid(),
                'requireprevious' => true
            ]
        ];
        $event = \mod_quiz\event\slot_requireprevious_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\slot_requireprevious_updated', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }
}
