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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/attemptlib.php');

/**
 * Unit tests for quiz events.
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2013 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_events_testcase extends advanced_testcase {

    /**
     * Setup some convenience test data with a single attempt.
     *
     * @param bool $ispreview Make the attempt a preview attempt when true.
     */
    protected function prepare_quiz_data($ispreview = false) {

        $this->resetAfterTest(true);

        // Create a course
        $course = $this->getDataGenerator()->create_course();

        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance(array('course'=>$course->id, 'questionsperpage' => 0,
            'grade' => 100.0, 'sumgrades' => 2));

        $cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id);

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $saq = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        $numq = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));

        // Add them to the quiz.
        quiz_add_quiz_question($saq->id, $quiz);
        quiz_add_quiz_question($numq->id, $quiz);

        // Make a user to do the quiz.
        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);

        $quizobj = quiz::create($quiz->id, $user1->id);

        // Start the attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, $ispreview);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        return array($quizobj, $quba, $attempt);
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
        $this->assertEquals('quiz_attempt_submitted', $event->get_legacy_eventname());
        $legacydata = new stdClass();
        $legacydata->component = 'mod_quiz';
        $legacydata->attemptid = (string) $attempt->id;
        $legacydata->timestamp = $timefinish;
        $legacydata->userid = $attempt->userid;
        $legacydata->cmid = $quizobj->get_cmid();
        $legacydata->courseid = $quizobj->get_courseid();
        $legacydata->quizid = $quizobj->get_quizid();
        // Submitterid should be the user, but as we are in PHP Unit, CLI_SCRIPT is set to true which sets null in submitterid.
        $legacydata->submitterid = null;
        $legacydata->timefinish = $timefinish;
        $this->assertEventLegacyData($legacydata, $event);
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
        $this->assertEquals('quiz_attempt_overdue', $event->get_legacy_eventname());
        $legacydata = new stdClass();
        $legacydata->component = 'mod_quiz';
        $legacydata->attemptid = (string) $attempt->id;
        $legacydata->timestamp = $timefinish;
        $legacydata->userid = $attempt->userid;
        $legacydata->cmid = $quizobj->get_cmid();
        $legacydata->courseid = $quizobj->get_courseid();
        $legacydata->quizid = $quizobj->get_quizid();
        $legacydata->submitterid = null; // Should be the user, but PHP Unit complains...
        $this->assertEventLegacyData($legacydata, $event);
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
        $this->assertEquals('quiz_attempt_abandoned', $event->get_legacy_eventname());
        $legacydata = new stdClass();
        $legacydata->component = 'mod_quiz';
        $legacydata->attemptid = (string) $attempt->id;
        $legacydata->timestamp = $timefinish;
        $legacydata->userid = $attempt->userid;
        $legacydata->cmid = $quizobj->get_cmid();
        $legacydata->courseid = $quizobj->get_courseid();
        $legacydata->quizid = $quizobj->get_quizid();
        $legacydata->submitterid = null; // Should be the user, but PHP Unit complains...
        $this->assertEventLegacyData($legacydata, $event);
        $this->assertEventContextNotUsed($event);
    }

    public function test_attempt_started() {
        list($quizobj, $quba, $attempt) = $this->prepare_quiz_data();

        // Create another attempt.
        $attempt = quiz_create_attempt($quizobj, 1, false, time(), false, 2);

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
        $this->assertEquals('quiz_attempt_started', $event->get_legacy_eventname());
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        // Check legacy log data.
        $expected = array($quizobj->get_courseid(), 'quiz', 'attempt', 'review.php?attempt=' . $attempt->id,
            $quizobj->get_quizid(), $quizobj->get_cmid());
        $this->assertEventLegacyLogData($expected, $event);
        // Check legacy event data.
        $legacydata = new stdClass();
        $legacydata->component = 'mod_quiz';
        $legacydata->attemptid = $attempt->id;
        $legacydata->timestart = $attempt->timestart;
        $legacydata->timestamp = $attempt->timestart;
        $legacydata->userid = $attempt->userid;
        $legacydata->quizid = $quizobj->get_quizid();
        $legacydata->cmid = $quizobj->get_cmid();
        $legacydata->courseid = $quizobj->get_courseid();
        $this->assertEventLegacyData($legacydata, $event);
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
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        $params = array(
            'courseid' => $course->id,
            'context' => context_module::instance($quiz->cmid),
            'other' => array(
                'quizid' => $quiz->id
            )
        );
        $event = \mod_quiz\event\edit_page_viewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\edit_page_viewed', $event);
        $this->assertEquals(context_module::instance($quiz->cmid), $event->get_context());
        $expected = array($course->id, 'quiz', 'editquestions', 'view.php?id=' . $quiz->cmid, $quiz->id, $quiz->cmid);
        $this->assertEventLegacyLogData($expected, $event);
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
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $expected = array($quizobj->get_courseid(), 'quiz', 'delete attempt', 'report.php?id=' . $quizobj->get_cmid(),
            $attempt->id, $quizobj->get_cmid());
        $this->assertEventLegacyLogData($expected, $event);
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
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        $params = array(
            'context' => $context = context_module::instance($quiz->cmid),
            'other' => array(
                'quizid' => $quiz->id,
                'reportname' => 'overview'
            )
        );
        $event = \mod_quiz\event\report_viewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\report_viewed', $event);
        $this->assertEquals(context_module::instance($quiz->cmid), $event->get_context());
        $expected = array($course->id, 'quiz', 'report', 'report.php?id=' . $quiz->cmid . '&mode=overview',
            $quiz->id, $quiz->cmid);
        $this->assertEventLegacyLogData($expected, $event);
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
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2,
            'courseid' => $course->id,
            'context' => context_module::instance($quiz->cmid),
            'other' => array(
                'quizid' => $quiz->id
            )
        );
        $event = \mod_quiz\event\attempt_reviewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_reviewed', $event);
        $this->assertEquals(context_module::instance($quiz->cmid), $event->get_context());
        $expected = array($course->id, 'quiz', 'review', 'review.php?attempt=1', $quiz->id, $quiz->cmid);
        $this->assertEventLegacyLogData($expected, $event);
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
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2,
            'courseid' => $course->id,
            'context' => context_module::instance($quiz->cmid),
            'other' => array(
                'quizid' => $quiz->id
            )
        );
        $event = \mod_quiz\event\attempt_summary_viewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_summary_viewed', $event);
        $this->assertEquals(context_module::instance($quiz->cmid), $event->get_context());
        $expected = array($course->id, 'quiz', 'view summary', 'summary.php?attempt=1', $quiz->id, $quiz->cmid);
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
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2,
            'context' => context_module::instance($quiz->cmid),
            'other' => array(
                'quizid' => $quiz->id
            )
        );
        $event = \mod_quiz\event\user_override_created::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\user_override_created', $event);
        $this->assertEquals(context_module::instance($quiz->cmid), $event->get_context());
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
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        $params = array(
            'objectid' => 1,
            'context' => context_module::instance($quiz->cmid),
            'other' => array(
                'quizid' => $quiz->id,
                'groupid' => 2
            )
        );
        $event = \mod_quiz\event\group_override_created::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\group_override_created', $event);
        $this->assertEquals(context_module::instance($quiz->cmid), $event->get_context());
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
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2,
            'context' => context_module::instance($quiz->cmid),
            'other' => array(
                'quizid' => $quiz->id
            )
        );
        $event = \mod_quiz\event\user_override_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\user_override_updated', $event);
        $this->assertEquals(context_module::instance($quiz->cmid), $event->get_context());
        $expected = array($course->id, 'quiz', 'edit override', 'overrideedit.php?id=1', $quiz->id, $quiz->cmid);
        $this->assertEventLegacyLogData($expected, $event);
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
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        $params = array(
            'objectid' => 1,
            'context' => context_module::instance($quiz->cmid),
            'other' => array(
                'quizid' => $quiz->id,
                'groupid' => 2
            )
        );
        $event = \mod_quiz\event\group_override_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\group_override_updated', $event);
        $this->assertEquals(context_module::instance($quiz->cmid), $event->get_context());
        $expected = array($course->id, 'quiz', 'edit override', 'overrideedit.php?id=1', $quiz->id, $quiz->cmid);
        $this->assertEventLegacyLogData($expected, $event);
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
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        // Create an override.
        $override = new stdClass();
        $override->quiz = $quiz->id;
        $override->userid = 2;
        $override->id = $DB->insert_record('quiz_overrides', $override);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        quiz_delete_override($quiz, $override->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\user_override_deleted', $event);
        $this->assertEquals(context_module::instance($quiz->cmid), $event->get_context());
        $expected = array($course->id, 'quiz', 'delete override', 'overrides.php?cmid=' . $quiz->cmid, $quiz->id, $quiz->cmid);
        $this->assertEventLegacyLogData($expected, $event);
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
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        // Create an override.
        $override = new stdClass();
        $override->quiz = $quiz->id;
        $override->groupid = 2;
        $override->id = $DB->insert_record('quiz_overrides', $override);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        quiz_delete_override($quiz, $override->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\group_override_deleted', $event);
        $this->assertEquals(context_module::instance($quiz->cmid), $event->get_context());
        $expected = array($course->id, 'quiz', 'delete override', 'overrides.php?cmid=' . $quiz->cmid, $quiz->id, $quiz->cmid);
        $this->assertEventLegacyLogData($expected, $event);
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
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2,
            'courseid' => $course->id,
            'context' => context_module::instance($quiz->cmid),
            'other' => array(
                'quizid' => $quiz->id
            )
        );
        $event = \mod_quiz\event\attempt_viewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_viewed', $event);
        $this->assertEquals(context_module::instance($quiz->cmid), $event->get_context());
        $expected = array($course->id, 'quiz', 'continue attempt', 'review.php?attempt=1', $quiz->id, $quiz->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the attempt previewed event.
     */
    public function test_attempt_preview_started() {
        list($quizobj, $quba, $attempt) = $this->prepare_quiz_data();

        // We want to preview this attempt.
        $attempt = quiz_create_attempt($quizobj, 1, false, time(), false, 2);
        $attempt->preview = 1;

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        quiz_attempt_save_started($quizobj, $quba, $attempt);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\attempt_preview_started', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $expected = array($quizobj->get_courseid(), 'quiz', 'preview', 'view.php?id=' . $quizobj->get_cmid(),
            $quizobj->get_quizid(), $quizobj->get_cmid());
        $this->assertEventLegacyLogData($expected, $event);
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

        $params = array(
            'objectid' => 1,
            'courseid' => $quizobj->get_courseid(),
            'context' => context_module::instance($quizobj->get_cmid()),
            'other' => array(
                'quizid' => $quizobj->get_quizid(),
                'attemptid' => 2,
                'slot' => 3
            )
        );
        $event = \mod_quiz\event\question_manually_graded::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_quiz\event\question_manually_graded', $event);
        $this->assertEquals(context_module::instance($quizobj->get_cmid()), $event->get_context());
        $expected = array($quizobj->get_courseid(), 'quiz', 'manualgrade', 'comment.php?attempt=2&slot=3',
            $quizobj->get_quizid(), $quizobj->get_cmid());
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }
}
