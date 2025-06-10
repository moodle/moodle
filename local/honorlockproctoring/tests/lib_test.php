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

 use moodle_page;

 /**
  * Honorlock proctoring test for module.
  *
  * @package   local_honorlockproctoring
  * @copyright 2023 Honorlock (https://honorlock.com/)
  * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
  */
class lib_test extends \advanced_testcase {
    /**
     * @var course Keeps the Course.
     */
    protected $course;

    /**
     * @var quiz Keeps the Honorlock Quiz.
     */
    protected $quiz;

    /**
     * @var quizobj Keeps the Honorlock Quiz.
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
     * Setup test data.
     */
    protected function setUp(): void {
        $this->resetAfterTest();

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

         $this->user1 = $this->getDataGenerator()->create_user();
         $this->setUser($this->user1);

         $this->create_quiz();

         $this->quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $this->quizobj->get_context());
         $this->quba->set_preferred_behaviour($this->quizobj->get_quiz()->preferredbehaviour);

         $extensioncheckresponse = (object)[
            "data" => [
             "iframe_src" => "https://app.honorlock.com/install/extension?locale=en",
             "extension_id" => "easrpoxsvfplyfubtodkzvtjezcsfqrz",
            ]];

          $getinstructionsresponse = (object)[
          "data" => [
             "launch_screen_url" => "https://example.com/extension/",
          ],
          ];

          $startsessionresponse = (object)[
          "data" => [
             "session" => [],
             "camera_url" => "string",
             "configurations" => [],
          ],
          ];

          $endsessionresponse = (object)[
            "data" => [
               "session" => [],
               "camera_url" => "string",
               "configurations" => [],
            ],
            ];

          \curl::mock_response(json_encode($endsessionresponse));
          \curl::mock_response(json_encode($getinstructionsresponse));
          \curl::mock_response(json_encode($startsessionresponse));
          \curl::mock_response(json_encode($extensioncheckresponse));

    }

     /**
      * Test local_honorlockproctoring_extend_navigation function in lib
      *
      * @covers \local_honorlockproctoring_extend_navigation
      */
    public function test_local_honorlockproctoring_extend_navigation() {
        Global $PAGE;

        $timenow = time();
        $attempt = quiz_create_attempt($this->quizobj, 1, false, $timenow);
        quiz_start_new_attempt($this->quizobj, $this->quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($this->quizobj, $this->quba, $attempt);

        $PAGE = new moodle_page();
        $cm = get_coursemodule_from_instance('quiz', $this->quiz->id);

        $PAGE->set_cm($cm);

        $PAGE->set_url('/mod/quiz/startattempt.php',
           ['cmid' => $cm->id, 'sesskey' => sesskey()]);

        $nav = new \global_navigation($PAGE);

        local_honorlockproctoring_extend_navigation($nav);
    }

     /**
      * Test local_honorlockproctoring_extend_navigation function in lib
      *
      * @covers \local_honorlockproctoring_extend_navigation
      */
    public function test_local_honorlockproctoring_extend_navigation_with_completed_attempt() {
        Global $PAGE;

        $timenow = time();
        $attempt = quiz_create_attempt($this->quizobj, 1, false, $timenow);
        quiz_start_new_attempt($this->quizobj, $this->quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($this->quizobj, $this->quba, $attempt);

        $testresponse = (object)[
           "data" => [
             "event_type" => "string",
             "exam_taker_name" => "TestTaker",
             "created_at" => "2023-08-24T14:15:22Z",
           ],
        ];

        \curl::mock_response(json_encode($testresponse));

        $attemptobj = \quiz_attempt::create($attempt->id);
        $attemptobj->process_finish(time(), false);

        $PAGE = new moodle_page();
        $cm = get_coursemodule_from_instance('quiz', $this->quiz->id);

        $PAGE->set_cm($cm);

        $PAGE->set_url('/mod/quiz/startattempt.php',
           ['cmid' => $cm->id, 'sesskey' => sesskey()]);

        $nav = new \global_navigation($PAGE);

        local_honorlockproctoring_extend_navigation($nav);
    }

     /**
      * Test local_honorlockproctoring_extend_navigation function in lib
      *
      * @covers \local_honorlockproctoring_extend_navigation
      */
    public function test_local_honorlockproctoring_extend_navigation_with_summary_page() {
        Global $PAGE;

        $timenow = time();
        $attempt = quiz_create_attempt($this->quizobj, 1, false, $timenow);
        quiz_start_new_attempt($this->quizobj, $this->quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($this->quizobj, $this->quba, $attempt);

        $PAGE = new moodle_page();
        $cm = get_coursemodule_from_instance('quiz', $this->quiz->id);

        $PAGE->set_cm($cm);

        $PAGE->set_url('/mod/quiz/summary.php',
           ['cmid' => $cm->id, 'sesskey' => sesskey()]);

        $nav = new \global_navigation($PAGE);

        local_honorlockproctoring_extend_navigation($nav);
    }

      /**
       * Test is_honorlock_enabled_quiz function in lib
       *
       * @covers \is_honorlock_enabled_quiz
       */
    public function test_is_honorlock_enabled_quiz() {
        Global $PAGE;

        $result = is_honorlock_enabled_quiz($this->quiz);

        $this->assertTrue($result);
    }

    /**
     * Create the Quiz
     *
     * @return void the generated event that has been triggered.
     */
    private function create_quiz() : void {
        $this->course = $this->getDataGenerator()->create_course();

        $params = [
         'course' => $this->course->id,
         'questionsperpage' => 0,
         'grade' => 100.0,
         'sumgrades' => 2,
         'quizpassword' => 'HL_NO_EDIT',
        ];

        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $this->quiz = $quizgenerator->create_instance($params);

        $this->quizobj = \quiz::create($this->quiz->id, $this->user1->id);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);

        quiz_add_quiz_question($question->id, $this->quiz);
    }
}
