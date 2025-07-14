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
 * Unit tests for (some of) mod/quiz/locallib.php.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
namespace mod_quiz;

use context_module;
use core_external\external_api;
use mod_quiz\quiz_settings;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

/**
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
final class lib_test extends \advanced_testcase {
    use \quiz_question_helper_test_trait;

    public function test_quiz_has_grades(): void {
        $quiz = new \stdClass();
        $quiz->grade = '100.0000';
        $quiz->sumgrades = '100.0000';
        $this->assertTrue(quiz_has_grades($quiz));
        $quiz->sumgrades = '0.0000';
        $this->assertFalse(quiz_has_grades($quiz));
        $quiz->grade = '0.0000';
        $this->assertFalse(quiz_has_grades($quiz));
        $quiz->sumgrades = '100.0000';
        $this->assertFalse(quiz_has_grades($quiz));
    }

    public function test_quiz_format_grade(): void {
        $quiz = new \stdClass();
        $quiz->decimalpoints = 2;
        $this->assertEquals(quiz_format_grade($quiz, 0.12345678), format_float(0.12, 2));
        $this->assertEquals(quiz_format_grade($quiz, 0), format_float(0, 2));
        $this->assertEquals(quiz_format_grade($quiz, 1.000000000000), format_float(1, 2));
        $quiz->decimalpoints = 0;
        $this->assertEquals(quiz_format_grade($quiz, 0.12345678), '0');
    }

    public function test_quiz_get_grade_format(): void {
        $quiz = new \stdClass();
        $quiz->decimalpoints = 2;
        $this->assertEquals(quiz_get_grade_format($quiz), 2);
        $this->assertEquals($quiz->questiondecimalpoints, -1);
        $quiz->questiondecimalpoints = 2;
        $this->assertEquals(quiz_get_grade_format($quiz), 2);
        $quiz->decimalpoints = 3;
        $quiz->questiondecimalpoints = -1;
        $this->assertEquals(quiz_get_grade_format($quiz), 3);
        $quiz->questiondecimalpoints = 4;
        $this->assertEquals(quiz_get_grade_format($quiz), 4);
    }

    public function test_quiz_format_question_grade(): void {
        $quiz = new \stdClass();
        $quiz->decimalpoints = 2;
        $quiz->questiondecimalpoints = 2;
        $this->assertEquals(quiz_format_question_grade($quiz, 0.12345678), format_float(0.12, 2));
        $this->assertEquals(quiz_format_question_grade($quiz, 0), format_float(0, 2));
        $this->assertEquals(quiz_format_question_grade($quiz, 1.000000000000), format_float(1, 2));
        $quiz->decimalpoints = 3;
        $quiz->questiondecimalpoints = -1;
        $this->assertEquals(quiz_format_question_grade($quiz, 0.12345678), format_float(0.123, 3));
        $this->assertEquals(quiz_format_question_grade($quiz, 0), format_float(0, 3));
        $this->assertEquals(quiz_format_question_grade($quiz, 1.000000000000), format_float(1, 3));
        $quiz->questiondecimalpoints = 4;
        $this->assertEquals(quiz_format_question_grade($quiz, 0.12345678), format_float(0.1235, 4));
        $this->assertEquals(quiz_format_question_grade($quiz, 0), format_float(0, 4));
        $this->assertEquals(quiz_format_question_grade($quiz, 1.000000000000), format_float(1, 4));
    }

    /**
     * Test deleting a quiz instance.
     */
    public function test_quiz_delete_instance(): void {
        global $SITE, $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Setup a quiz with 1 standard and 1 random question.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $SITE->id, 'questionsperpage' => 3, 'grade' => 100.0]);
        $context = context_module::instance($quiz->cmid);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $standardq = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);

        quiz_add_quiz_question($standardq->id, $quiz);
        $this->add_random_questions($quiz->id, 0, $cat->id, 1);

        // Get the random question.
        $randomq = $DB->get_record('question', ['qtype' => 'random']);

        quiz_delete_instance($quiz->id);

        // Check that the random question was deleted.
        if ($randomq) {
            $this->assertEquals(0, $DB->count_records('question', ['id' => $randomq->id]));
        }
        // Check that the standard question was not deleted.
        $this->assertEquals(1, $DB->count_records('question', ['id' => $standardq->id]));

        // Check that all the slots were removed.
        $this->assertEquals(0, $DB->count_records('quiz_slots', ['quizid' => $quiz->id]));

        // Check that the quiz was removed.
        $this->assertEquals(0, $DB->count_records('quiz', ['id' => $quiz->id]));

        // Check that any question references linked to this quiz are gone.
        $this->assertEquals(0, $DB->count_records('question_references', ['usingcontextid' => $context->id]));
        $this->assertEquals(0, $DB->count_records('question_set_references', ['usingcontextid' => $context->id]));
    }

    public function test_quiz_get_user_attempts(): void {
        global $DB;
        $this->resetAfterTest();

        $dg = $this->getDataGenerator();
        $quizgen = $dg->get_plugin_generator('mod_quiz');
        $course = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $role = $DB->get_record('role', ['shortname' => 'student']);

        $dg->enrol_user($u1->id, $course->id, $role->id);
        $dg->enrol_user($u2->id, $course->id, $role->id);
        $dg->enrol_user($u3->id, $course->id, $role->id);
        $dg->enrol_user($u4->id, $course->id, $role->id);

        $quiz1 = $quizgen->create_instance(['course' => $course->id, 'sumgrades' => 2]);
        $quiz2 = $quizgen->create_instance(['course' => $course->id, 'sumgrades' => 2]);

        // Questions.
        $questgen = $dg->get_plugin_generator('core_question');
        $quizcat = $questgen->create_question_category();
        $question = $questgen->create_question('numerical', null, ['category' => $quizcat->id]);
        quiz_add_quiz_question($question->id, $quiz1);
        quiz_add_quiz_question($question->id, $quiz2);

        $quizobj1a = quiz_settings::create($quiz1->id, $u1->id);
        $quizobj1b = quiz_settings::create($quiz1->id, $u2->id);
        $quizobj1c = quiz_settings::create($quiz1->id, $u3->id);
        $quizobj1d = quiz_settings::create($quiz1->id, $u4->id);
        $quizobj2a = quiz_settings::create($quiz2->id, $u1->id);

        // Set attempts.
        $quba1a = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj1a->get_context());
        $quba1a->set_preferred_behaviour($quizobj1a->get_quiz()->preferredbehaviour);
        $quba1b = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj1b->get_context());
        $quba1b->set_preferred_behaviour($quizobj1b->get_quiz()->preferredbehaviour);
        $quba1c = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj1c->get_context());
        $quba1c->set_preferred_behaviour($quizobj1c->get_quiz()->preferredbehaviour);
        $quba1d = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj1d->get_context());
        $quba1d->set_preferred_behaviour($quizobj1d->get_quiz()->preferredbehaviour);
        $quba2a = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj2a->get_context());
        $quba2a->set_preferred_behaviour($quizobj2a->get_quiz()->preferredbehaviour);

        $timenow = time();

        // User 1 passes quiz 1.
        $attempt = quiz_create_attempt($quizobj1a, 1, false, $timenow, false, $u1->id);
        quiz_start_new_attempt($quizobj1a, $quba1a, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj1a, $quba1a, $attempt);
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submitted_actions($timenow, false, [1 => ['answer' => '3.14']]);
        $attemptobj->process_submit($timenow, false);
        $attemptobj->process_grade_submission($timenow);

        // User 2 goes overdue in quiz 1.
        $attempt = quiz_create_attempt($quizobj1b, 1, false, $timenow, false, $u2->id);
        quiz_start_new_attempt($quizobj1b, $quba1b, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj1b, $quba1b, $attempt);
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_going_overdue($timenow, true);

        // User 3 does not finish quiz 1.
        $attempt = quiz_create_attempt($quizobj1c, 1, false, $timenow, false, $u3->id);
        quiz_start_new_attempt($quizobj1c, $quba1c, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj1c, $quba1c, $attempt);

        // User 4 abandons the quiz 1.
        $attempt = quiz_create_attempt($quizobj1d, 1, false, $timenow, false, $u4->id);
        quiz_start_new_attempt($quizobj1d, $quba1d, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj1d, $quba1d, $attempt);
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_abandon($timenow, true);

        // User 1 attempts the quiz three times (abandon, finish, in progress).
        $quba2a = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj2a->get_context());
        $quba2a->set_preferred_behaviour($quizobj2a->get_quiz()->preferredbehaviour);

        $attempt = quiz_create_attempt($quizobj2a, 1, false, $timenow, false, $u1->id);
        quiz_start_new_attempt($quizobj2a, $quba2a, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj2a, $quba2a, $attempt);
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_abandon($timenow, true);

        $quba2a = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj2a->get_context());
        $quba2a->set_preferred_behaviour($quizobj2a->get_quiz()->preferredbehaviour);

        $attempt = quiz_create_attempt($quizobj2a, 2, false, $timenow, false, $u1->id);
        quiz_start_new_attempt($quizobj2a, $quba2a, $attempt, 2, $timenow);
        quiz_attempt_save_started($quizobj2a, $quba2a, $attempt);
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submit($timenow, false);
        $attemptobj->process_grade_submission($timenow);

        $quba2a = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj2a->get_context());
        $quba2a->set_preferred_behaviour($quizobj2a->get_quiz()->preferredbehaviour);

        $attempt = quiz_create_attempt($quizobj2a, 3, false, $timenow, false, $u1->id);
        quiz_start_new_attempt($quizobj2a, $quba2a, $attempt, 3, $timenow);
        quiz_attempt_save_started($quizobj2a, $quba2a, $attempt);

        // Check for user 1.
        $attempts = quiz_get_user_attempts($quiz1->id, $u1->id, 'all');
        $this->assertCount(1, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::FINISHED, $attempt->state);
        $this->assertEquals($u1->id, $attempt->userid);
        $this->assertEquals($quiz1->id, $attempt->quiz);

        $attempts = quiz_get_user_attempts($quiz1->id, $u1->id, 'finished');
        $this->assertCount(1, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::FINISHED, $attempt->state);
        $this->assertEquals($u1->id, $attempt->userid);
        $this->assertEquals($quiz1->id, $attempt->quiz);

        $attempts = quiz_get_user_attempts($quiz1->id, $u1->id, 'unfinished');
        $this->assertCount(0, $attempts);

        // Check for user 2.
        $attempts = quiz_get_user_attempts($quiz1->id, $u2->id, 'all');
        $this->assertCount(1, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::OVERDUE, $attempt->state);
        $this->assertEquals($u2->id, $attempt->userid);
        $this->assertEquals($quiz1->id, $attempt->quiz);

        $attempts = quiz_get_user_attempts($quiz1->id, $u2->id, 'finished');
        $this->assertCount(0, $attempts);

        $attempts = quiz_get_user_attempts($quiz1->id, $u2->id, 'unfinished');
        $this->assertCount(1, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::OVERDUE, $attempt->state);
        $this->assertEquals($u2->id, $attempt->userid);
        $this->assertEquals($quiz1->id, $attempt->quiz);

        // Check for user 3.
        $attempts = quiz_get_user_attempts($quiz1->id, $u3->id, 'all');
        $this->assertCount(1, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::IN_PROGRESS, $attempt->state);
        $this->assertEquals($u3->id, $attempt->userid);
        $this->assertEquals($quiz1->id, $attempt->quiz);

        $attempts = quiz_get_user_attempts($quiz1->id, $u3->id, 'finished');
        $this->assertCount(0, $attempts);

        $attempts = quiz_get_user_attempts($quiz1->id, $u3->id, 'unfinished');
        $this->assertCount(1, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::IN_PROGRESS, $attempt->state);
        $this->assertEquals($u3->id, $attempt->userid);
        $this->assertEquals($quiz1->id, $attempt->quiz);

        // Check for user 4.
        $attempts = quiz_get_user_attempts($quiz1->id, $u4->id, 'all');
        $this->assertCount(1, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::ABANDONED, $attempt->state);
        $this->assertEquals($u4->id, $attempt->userid);
        $this->assertEquals($quiz1->id, $attempt->quiz);

        $attempts = quiz_get_user_attempts($quiz1->id, $u4->id, 'finished');
        $this->assertCount(1, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::ABANDONED, $attempt->state);
        $this->assertEquals($u4->id, $attempt->userid);
        $this->assertEquals($quiz1->id, $attempt->quiz);

        $attempts = quiz_get_user_attempts($quiz1->id, $u4->id, 'unfinished');
        $this->assertCount(0, $attempts);

        // Multiple attempts for user 1 in quiz 2.
        $attempts = quiz_get_user_attempts($quiz2->id, $u1->id, 'all');
        $this->assertCount(3, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::ABANDONED, $attempt->state);
        $this->assertEquals($u1->id, $attempt->userid);
        $this->assertEquals($quiz2->id, $attempt->quiz);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::FINISHED, $attempt->state);
        $this->assertEquals($u1->id, $attempt->userid);
        $this->assertEquals($quiz2->id, $attempt->quiz);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::IN_PROGRESS, $attempt->state);
        $this->assertEquals($u1->id, $attempt->userid);
        $this->assertEquals($quiz2->id, $attempt->quiz);

        $attempts = quiz_get_user_attempts($quiz2->id, $u1->id, 'finished');
        $this->assertCount(2, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::ABANDONED, $attempt->state);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::FINISHED, $attempt->state);

        $attempts = quiz_get_user_attempts($quiz2->id, $u1->id, 'unfinished');
        $this->assertCount(1, $attempts);
        $attempt = array_shift($attempts);

        // Multiple quiz attempts fetched at once.
        $attempts = quiz_get_user_attempts([$quiz1->id, $quiz2->id], $u1->id, 'all');
        $this->assertCount(4, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::FINISHED, $attempt->state);
        $this->assertEquals($u1->id, $attempt->userid);
        $this->assertEquals($quiz1->id, $attempt->quiz);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::ABANDONED, $attempt->state);
        $this->assertEquals($u1->id, $attempt->userid);
        $this->assertEquals($quiz2->id, $attempt->quiz);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::FINISHED, $attempt->state);
        $this->assertEquals($u1->id, $attempt->userid);
        $this->assertEquals($quiz2->id, $attempt->quiz);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::IN_PROGRESS, $attempt->state);
        $this->assertEquals($u1->id, $attempt->userid);
        $this->assertEquals($quiz2->id, $attempt->quiz);
    }

    /**
     * Test for quiz_get_group_override_priorities().
     */
    public function test_quiz_get_group_override_priorities(): void {
        global $DB;
        $this->resetAfterTest();

        $dg = $this->getDataGenerator();
        $quizgen = $dg->get_plugin_generator('mod_quiz');
        $course = $dg->create_course();

        $quiz = $quizgen->create_instance(['course' => $course->id, 'sumgrades' => 2]);

        $this->assertNull(quiz_get_group_override_priorities($quiz->id));

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $now = 100;
        $override1 = (object)[
            'quiz' => $quiz->id,
            'groupid' => $group1->id,
            'timeopen' => $now,
            'timeclose' => $now + 20
        ];
        $DB->insert_record('quiz_overrides', $override1);

        $override2 = (object)[
            'quiz' => $quiz->id,
            'groupid' => $group2->id,
            'timeopen' => $now - 10,
            'timeclose' => $now + 10
        ];
        $DB->insert_record('quiz_overrides', $override2);

        $priorities = quiz_get_group_override_priorities($quiz->id);
        $this->assertNotEmpty($priorities);

        $openpriorities = $priorities['open'];
        // Override 2's time open has higher priority since it is sooner than override 1's.
        $this->assertEquals(2, $openpriorities[$override1->timeopen]);
        $this->assertEquals(1, $openpriorities[$override2->timeopen]);

        $closepriorities = $priorities['close'];
        // Override 1's time close has higher priority since it is later than override 2's.
        $this->assertEquals(1, $closepriorities[$override1->timeclose]);
        $this->assertEquals(2, $closepriorities[$override2->timeclose]);
    }

    public function test_quiz_core_calendar_provide_event_action_open(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        // Create a student and enrol into the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        // Create a quiz.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id,
            'timeopen' => time() - DAYSECS, 'timeclose' => time() + DAYSECS]);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $quiz->id, QUIZ_EVENT_TYPE_OPEN);
        // Now, log in as student.
        $this->setUser($student);
        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_quiz_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('attemptquiznow', 'quiz'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_quiz_core_calendar_provide_event_action_open_for_user(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        // Create a student and enrol into the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        // Create a quiz.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id,
            'timeopen' => time() - DAYSECS, 'timeclose' => time() + DAYSECS]);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $quiz->id, QUIZ_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_quiz_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('attemptquiznow', 'quiz'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_quiz_core_calendar_provide_event_action_closed(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a quiz.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id,
            'timeclose' => time() - DAYSECS]);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $quiz->id, QUIZ_EVENT_TYPE_CLOSE);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Confirm the result was null.
        $this->assertNull(mod_quiz_core_calendar_provide_event_action($event, $factory));
    }

    public function test_quiz_core_calendar_provide_event_action_closed_for_user(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a quiz.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id,
            'timeclose' => time() - DAYSECS]);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $quiz->id, QUIZ_EVENT_TYPE_CLOSE);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Confirm the result was null.
        $this->assertNull(mod_quiz_core_calendar_provide_event_action($event, $factory, $student->id));
    }

    public function test_quiz_core_calendar_provide_event_action_open_in_future(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        // Create a student and enrol into the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        // Create a quiz.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id,
            'timeopen' => time() + DAYSECS]);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $quiz->id, QUIZ_EVENT_TYPE_CLOSE);
        // Now, log in as student.
        $this->setUser($student);
        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_quiz_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('attemptquiznow', 'quiz'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    public function test_quiz_core_calendar_provide_event_action_open_in_future_for_user(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        // Create a student and enrol into the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        // Create a quiz.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id,
            'timeopen' => time() + DAYSECS]);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $quiz->id, QUIZ_EVENT_TYPE_CLOSE);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_quiz_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('attemptquiznow', 'quiz'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    public function test_quiz_core_calendar_provide_event_action_no_capability(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);

        // Enrol student.
        $this->assertTrue($this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id));

        // Create a quiz.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        // Remove the permission to attempt or review the quiz for the student role.
        $coursecontext = \context_course::instance($course->id);
        assign_capability('mod/quiz:reviewmyattempts', CAP_PROHIBIT, $studentrole->id, $coursecontext);
        assign_capability('mod/quiz:attempt', CAP_PROHIBIT, $studentrole->id, $coursecontext);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $quiz->id, QUIZ_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Set current user to the student.
        $this->setUser($student);

        // Confirm null is returned.
        $this->assertNull(mod_quiz_core_calendar_provide_event_action($event, $factory));
    }

    public function test_quiz_core_calendar_provide_event_action_no_capability_for_user(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);

        // Enrol student.
        $this->assertTrue($this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id));

        // Create a quiz.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        // Remove the permission to attempt or review the quiz for the student role.
        $coursecontext = \context_course::instance($course->id);
        assign_capability('mod/quiz:reviewmyattempts', CAP_PROHIBIT, $studentrole->id, $coursecontext);
        assign_capability('mod/quiz:attempt', CAP_PROHIBIT, $studentrole->id, $coursecontext);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $quiz->id, QUIZ_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Confirm null is returned.
        $this->assertNull(mod_quiz_core_calendar_provide_event_action($event, $factory, $student->id));
    }

    public function test_quiz_core_calendar_provide_event_action_already_finished(): void {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);

        // Enrol student.
        $this->assertTrue($this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id));

        // Create a quiz.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id,
            'sumgrades' => 1]);

        // Add a question to the quiz.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        quiz_add_quiz_question($question->id, $quiz);

        // Get the quiz object.
        $quizobj = quiz_settings::create($quiz->id, $student->id);

        // Create an attempt for the student in the quiz.
        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $student->id);
        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submit($timenow, false);
        $attemptobj->process_grade_submission($timenow);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $quiz->id, QUIZ_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Set current user to the student.
        $this->setUser($student);

        // Confirm null is returned.
        $this->assertNull(mod_quiz_core_calendar_provide_event_action($event, $factory));
    }

    public function test_quiz_core_calendar_provide_event_action_already_finished_for_user(): void {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);

        // Enrol student.
        $this->assertTrue($this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id));

        // Create a quiz.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id,
            'sumgrades' => 1]);

        // Add a question to the quiz.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        quiz_add_quiz_question($question->id, $quiz);

        // Get the quiz object.
        $quizobj = quiz_settings::create($quiz->id, $student->id);

        // Create an attempt for the student in the quiz.
        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $student->id);
        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submit($timenow, false);
        $attemptobj->process_grade_submission($timenow);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $quiz->id, QUIZ_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Confirm null is returned.
        $this->assertNull(mod_quiz_core_calendar_provide_event_action($event, $factory, $student->id));
    }

    public function test_quiz_core_calendar_provide_event_action_already_completed(): void {
        $this->resetAfterTest();
        set_config('enablecompletion', 1);
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id],
            ['completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS]);

        // Get some additional data.
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $quiz->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_quiz_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_quiz_core_calendar_provide_event_action_already_completed_for_user(): void {
        $this->resetAfterTest();
        set_config('enablecompletion', 1);
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id],
            ['completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS]);

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Get some additional data.
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $quiz->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed for the student.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm, $student->id);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_quiz_core_calendar_provide_event_action($event, $factory, $student->id);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid
     * @param int $instanceid The quiz id.
     * @param string $eventtype The event type. eg. QUIZ_EVENT_TYPE_OPEN.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new \stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'quiz';
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
    public function test_mod_quiz_completion_get_active_rule_descriptions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Two activities, both with automatic completion. One has the 'completionsubmit' rule, one doesn't.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 2]);
        $quiz1 = $this->getDataGenerator()->create_module('quiz', [
            'course' => $course->id,
            'completion' => 2,
            'completionusegrade' => 1,
            'completionpassgrade' => 1,
            'completionattemptsexhausted' => 1,
        ]);
        $quiz2 = $this->getDataGenerator()->create_module('quiz', [
            'course' => $course->id,
            'completion' => 2,
            'completionusegrade' => 0
        ]);
        $cm1 = \cm_info::create(get_coursemodule_from_instance('quiz', $quiz1->id));
        $cm2 = \cm_info::create(get_coursemodule_from_instance('quiz', $quiz2->id));

        // Data for the stdClass input type.
        // This type of input would occur when checking the default completion rules for an activity type, where we don't have
        // any access to cm_info, rather the input is a stdClass containing completion and customdata attributes, just like cm_info.
        $moddefaults = new \stdClass();
        $moddefaults->customdata = ['customcompletionrules' => [
            'completionattemptsexhausted' => 1,
        ]];
        $moddefaults->completion = 2;

        $activeruledescriptions = [
            get_string('completionpassorattemptsexhausteddesc', 'quiz'),
        ];
        $this->assertEquals(mod_quiz_get_completion_active_rule_descriptions($cm1), $activeruledescriptions);
        $this->assertEquals(mod_quiz_get_completion_active_rule_descriptions($cm2), []);
        $this->assertEquals(mod_quiz_get_completion_active_rule_descriptions($moddefaults), $activeruledescriptions);
        $this->assertEquals(mod_quiz_get_completion_active_rule_descriptions(new \stdClass()), []);
    }

    /**
     * A user who does not have capabilities to add events to the calendar should be able to create a quiz.
     */
    public function test_creation_with_no_calendar_capabilities(): void {
        $this->resetAfterTest();
        $course = self::getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $user = self::getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $roleid = self::getDataGenerator()->create_role();
        self::getDataGenerator()->role_assign($roleid, $user->id, $context->id);
        assign_capability('moodle/calendar:manageentries', CAP_PROHIBIT, $roleid, $context, true);
        $generator = self::getDataGenerator()->get_plugin_generator('mod_quiz');
        // Create an instance as a user without the calendar capabilities.
        $this->setUser($user);
        $time = time();
        $params = [
            'course' => $course->id,
            'timeopen' => $time + 200,
            'timeclose' => $time + 2000,
        ];
        $generator->create_instance($params);
    }

    /**
     * Data provider for summarise_response() test cases.
     *
     * @return array List of data sets (test cases)
     */
    public static function mod_quiz_inplace_editable_provider(): array {
        return [
            'set to A1' => [1, 'A1'],
            'set with HTML characters' => [2, 'A & &amp; <-:'],
            'set to integer' => [3, '3'],
            'set to blank' => [4, ''],
            'set with Unicode characters' => [1, 'L\'Aina Lluís^'],
            'set with Unicode at the truncation point' => [1, '123456789012345碁'],
            'set with HTML Char at the truncation point' => [1, '123456789012345>'],
        ];
    }

    /**
     * Test customised and automated question numbering for a given slot number and customised value.
     *
     * @dataProvider mod_quiz_inplace_editable_provider
     * @param int $slotnumber
     * @param string $newvalue
     * @covers ::mod_quiz_inplace_editable
     */
    public function test_mod_quiz_inplace_editable(int $slotnumber, string $newvalue): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/external/externallib.php');
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id, 'sumgrades' => 1]);
        $cm = get_coursemodule_from_id('quiz', $quiz->cmid);

        // Add few questions to the quiz.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();

        $question = $questiongenerator->create_question('truefalse', null, ['category' => $cat->id]);
        quiz_add_quiz_question($question->id, $quiz);

        $question = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        quiz_add_quiz_question($question->id, $quiz);

        $question = $questiongenerator->create_question('multichoice', null, ['category' => $cat->id]);
        quiz_add_quiz_question($question->id, $quiz);

        $question = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        quiz_add_quiz_question($question->id, $quiz);

        // Create the quiz object.
        $quizobj = new quiz_settings($quiz, $cm, $course);
        $structure = $quizobj->get_structure();

        $slots = $structure->get_slots();
        $this->assertEquals(4, count($slots));

        $slotid = $structure->get_slot_id_for_slot($slotnumber);
        $inplaceeditable = mod_quiz_inplace_editable('slotdisplaynumber', $slotid, $newvalue);
        $result = \core_external::update_inplace_editable('mod_quiz', 'slotdisplaynumber', $slotid, $newvalue);
        $result = external_api::clean_returnvalue(\core_external::update_inplace_editable_returns(), $result);

        $this->assertEquals(count((array) $inplaceeditable), count($result));
        $this->assertEquals($slotid, $result['itemid']);
        if ($newvalue === '' || is_null($newvalue)) {
            // Check against default.
            $this->assertEquals($slotnumber, $result['displayvalue']);
            $this->assertEquals($slotnumber, $result['value']);
        } else {
            // Check against the custom number.
            $this->assertEquals(s($newvalue), $result['displayvalue']);
            $this->assertEquals($newvalue, $result['value']);
        }
    }
}
