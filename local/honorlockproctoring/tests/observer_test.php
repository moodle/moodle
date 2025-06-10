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

namespace local_honorlockproctoring;

use quiz;

/**
 * Honorlock proctoring test for module.
 *
 * @package   local_honorlockproctoring
 * @copyright 2023 Honorlock (https://honorlock.com/)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer_test extends \advanced_testcase {
    /**
     * @var observer Keeps the Honorlock Class.
     */
    protected $observer;

    /**
     * @var course Keeps the Course.
     */
    protected $course;

    /**
     * @var quiz Keeps the Quiz.
     */
    protected $quiz;

    /**
     * @var quizobj Keeps the Quiz.
     */
    protected $quizobj;

    /**
     * @var user A generic user.
     */
    protected $user1;

    /**
     * @var question_engine Question Engine.
     */
    protected $quba;

    /**
     * @var quiz_attempt Quiz Attempt Object.
     */
    protected $attemptobj;


    /**
     * Setup test data.
     */
    protected function setUp(): void {
        $this->resetAfterTest();
        $this->observer = new observer;
        $honorlockapi = new honorlockapi();

        $reflection = new \ReflectionClass(get_class($honorlockapi));
        $method = $reflection->getMethod('get_token');
        $method->setAccessible(true);

        $tokenresponse = (object)[
        "data" => [
          "access_token" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI...",
          "expires_in" => 86000,
        ],
        ];

        \curl::mock_response(json_encode($tokenresponse));

        $method->invoke($honorlockapi);

        $this->course = $this->getDataGenerator()->create_course();
        $this->user1 = $this->getDataGenerator()->create_user();

    }

    /**
     * Test quiz_viewed function
     *
     * @covers \local_honorlockproctoring\observer::quiz_viewed
     */
    public function test_quiz_viewed(): void {
        global $SESSION;
        $this->create_quiz();
        $SESSION->passwordcheckedquizzes[$this->quiz->id] = true;
        $context = \context_module::instance($this->quiz->cmid);
        $cm = get_coursemodule_from_instance('quiz', $this->quiz->id);

        $this->assertFalse(empty($SESSION->passwordcheckedquizzes[$this->quiz->id]));
        quiz_view($this->quiz, $this->course, $cm, $context);

        $this->assertEquals('HL_NO_EDIT', $this->quiz->password);
        $this->assertTrue(empty($SESSION->passwordcheckedquizzes[$this->quiz->id]));
    }

    /**
     * Test quiz_viewed function
     *
     * @covers \local_honorlockproctoring\observer::quiz_viewed
     */
    public function test_quiz_viewed_does_not_unset_session_variable_if_not_honorlock_enabled(): void {
        global $SESSION;
        $this->create_quiz(false);
        $SESSION->passwordcheckedquizzes[$this->quiz->id] = true;
        $context = \context_module::instance($this->quiz->cmid);
        $cm = get_coursemodule_from_instance('quiz', $this->quiz->id);

        quiz_view($this->quiz, $this->course, $cm, $context);

        $this->assertFalse(empty($SESSION->passwordcheckedquizzes[$this->quiz->id]));
    }

    /**
     * Test quiz_attempt_viewed function
     *
     * @covers \local_honorlockproctoring\observer::quiz_attempt_viewed
     */
    public function test_quiz_attempt_viewed(): void {
        $this->create_quiz();

        $event = $this->create_attempt_view_event(false);
        $event->trigger();

        $this->assertEquals('HL_NO_EDIT', $this->quiz->password);
        $this->assertInstanceOf('\mod_quiz\event\attempt_viewed', $event);
        $this->assertEquals(\context_module::instance($this->quiz->cmid), $event->get_context());
    }

    /**
     * Test quiz_attempt_viewed function
     *
     * @covers \local_honorlockproctoring\observer::quiz_attempt_viewed
     */
    public function test_quiz_attempt_viewed_returns_early_when_not_honorlock_enabled(): void {
        $this->create_quiz(false);
        $event = $this->create_attempt_view_event($this->quiz);

        $event->trigger();

        $this->assertNotEquals('HL_NO_EDIT', $this->quiz->password);
        $this->assertInstanceOf('\mod_quiz\event\attempt_viewed', $event);
        $this->assertEquals(\context_module::instance($this->quiz->cmid), $event->get_context());
    }

    /**
     * Test quiz_attempt_viewed function
     *
     * @covers \local_honorlockproctoring\observer::quiz_attempt_viewed
     */
    public function test_quiz_attempt_viewed_returns_early_when_not_in_progress(): void {
        $this->create_quiz();
        $event = $this->create_attempt_view_event(true);

        $event->trigger();

        $this->assertEquals('HL_NO_EDIT', $this->quiz->password);
        $this->assertInstanceOf('\mod_quiz\event\attempt_viewed', $event);
        $this->assertEquals(\context_module::instance($this->quiz->cmid), $event->get_context());
    }

    /**
     * Test quiz_attempt_submitted function
     *
     * @covers \local_honorlockproctoring\observer::quiz_attempt_submitted
     */
    public function test_quiz_attempt_submitted(): void {
        $this->create_quiz();
        $this->create_attempt_view_event(true);

        $this->assertEquals('HL_NO_EDIT', $this->quiz->password);
    }

    /**
     * Test quiz_attempt_submitted function
     *
     * @covers \local_honorlockproctoring\observer::quiz_attempt_submitted
     */
    public function test_quiz_attempt_submitted_returns_early_when_honorlock_not_enabled(): void {
        $this->create_quiz(false);
        $this->create_attempt_view_event(true);

        $this->assertNotEquals('HL_NO_EDIT', $this->quiz->password);
    }

    /**
     * Test is_honorlock_enabled_quiz private function
     *
     * @covers \local_honorlockproctoring\observer::is_honorlock_enabled_quiz
     */
    public function test_is_honorlock_enabled_private_static_function(): void {
        $this->create_quiz();

        $reflection = new \ReflectionClass(get_class($this->observer));
        $method = $reflection->getMethod('is_honorlock_enabled_quiz');
        $method->setAccessible(true);
        $result = $method->invokeArgs(null, [$this->quiz->id]);

        $this->assertTrue($result);
    }

    /**
     * Test get_honorlock_instance private function
     *
     * @covers \local_honorlockproctoring\observer::get_honorlock_instance
     */
    public function test_get_honorlock_instance_private_static_function(): void {

        $reflection = new \ReflectionClass(get_class($this->observer));
        $method = $reflection->getMethod('get_honorlock_instance');
        $method->setAccessible(true);

        $result = $method->invoke(null);

        $this->assertInstanceOf('local_honorlockproctoring\honorlock', $result);
    }

    /**
     * Create the Quiz
     *
     * @param bool $honorlockenabled determine whether quiz will be Honorlock Enabled
     * @return void the generated event that has been triggered.
     */
    private function create_quiz($honorlockenabled = true): void {
        $params = [
            'course' => $this->course->id,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ];
        if ($honorlockenabled) {
            $params['quizpassword'] = 'HL_NO_EDIT';
        }

        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $this->quiz = $quizgenerator->create_instance($params);

        $this->quizobj = quiz::create($this->quiz->id, $this->user1->id);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);

        quiz_add_quiz_question($question->id, $this->quiz);

        $this->quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $this->quizobj->get_context());
        $this->quba->set_preferred_behaviour($this->quizobj->get_quiz()->preferredbehaviour);
    }

    /**
     * Setup the Quiz, Generate the Attempt Viewed Event.
     *
     * @param bool $completed determine whether the quiz will be completed
     * @return event the generated event that has been triggered.
     */
    private function create_attempt_view_event($completed = false): \mod_quiz\event\attempt_viewed {

        $this->setUser($this->user1);

        $timenow = time();
        $attempt = quiz_create_attempt($this->quizobj, 1, false, $timenow);
        quiz_start_new_attempt($this->quizobj, $this->quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($this->quizobj, $this->quba, $attempt);

        $params = [
            'objectid' => $attempt->id,
            'relateduserid' => $this->user1->id,
            'courseid' => $this->course->id,
            'context' => \context_module::instance($this->quiz->cmid),
            'other' => [
                'quizid' => $this->quiz->id,
                'page' => 0,
            ],
        ];

        if ($completed) {
            $this->attemptobj = \quiz_attempt::create($attempt->id);
            $this->attemptobj->process_finish(time(), false);
        }

        $event = \mod_quiz\event\attempt_viewed::create($params);

        $testresponse = (object)[
            "message" => "Awesome",
            "data" => [
                "event_type" => "string",
                "exam_taker_name" => "TestTaker",
                "created_at" => "2023-08-24T14:15:22Z",
            ],
            ];

        \curl::mock_response(json_encode($testresponse));

        return $event;
    }
}
