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

namespace mod_quiz;

use moodle_url;
use question_bank;
use question_engine;
use mod_quiz\tests\question_helper_test_trait;

/**
 * Quiz attempt walk through.
 *
 * @package   mod_quiz
 * @category  test
 * @copyright 2013 The Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\quiz_attempt
 */
final class attempt_walkthrough_test extends \advanced_testcase {
    use question_helper_test_trait;

    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;

        parent::setUpBeforeClass();

        require_once($CFG->dirroot . '/mod/quiz/locallib.php');
    }

    /**
     * Create a quiz with questions and walk through a quiz attempt.
     */
    public function test_quiz_attempt_walkthrough(): void {
        global $SITE;

        $this->resetAfterTest(true);

        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance(['course' => $SITE->id, 'questionsperpage' => 0, 'grade' => 100.0,
                                                      'sumgrades' => 3]);

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $saq = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        $numq = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        $matchq = $questiongenerator->create_question('match', null, ['category' => $cat->id]);
        $description = $questiongenerator->create_question('description', null, ['category' => $cat->id]);

        // Add them to the quiz.
        quiz_add_quiz_question($saq->id, $quiz);
        quiz_add_quiz_question($numq->id, $quiz);
        quiz_add_quiz_question($matchq->id, $quiz);
        quiz_add_quiz_question($description->id, $quiz);

        // Make a user to do the quiz.
        $user1 = $this->getDataGenerator()->create_user();

        $quizobj = quiz_settings::create($quiz->id, $user1->id);

        // Start the attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $user1->id);

        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        $this->assertEquals('1,2,3,4,0', $attempt->layout);

        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Process some responses from the student.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertFalse($attemptobj->has_response_to_at_least_one_graded_question());
        // The student has not answered any questions.
        $this->assertEquals(3, $attemptobj->get_number_of_unanswered_questions());

        $tosubmit = [1 => ['answer' => 'frog'],
                          2 => ['answer' => '3.14']];

        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);
        // The student has answered two questions, and only one remaining.
        $this->assertEquals(1, $attemptobj->get_number_of_unanswered_questions());

        $tosubmit = [
            3 => [
                'frog' => 'amphibian',
                'cat' => 'mammal',
                'newt' => '',
            ],
        ];

        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);
        // The student has answered three questions but one is invalid, so there is still one remaining.
        $this->assertEquals(1, $attemptobj->get_number_of_unanswered_questions());

        $tosubmit = [
            3 => [
                'frog' => 'amphibian',
                'cat' => 'mammal',
                'newt' => 'amphibian',
            ],
        ];

        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);
        // The student has answered three questions, so there are no remaining.
        $this->assertEquals(0, $attemptobj->get_number_of_unanswered_questions());

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $attemptobj->process_submit($timenow, false);

        // Re-load quiz attempt data.
        $attemptobj = quiz_attempt::create($attempt->id);

        // Check that results are stored as expected.
        $this->assertEquals(1, $attemptobj->get_attempt_number());
        $this->assertEquals(false, $attemptobj->is_finished());
        $this->assertEquals($timenow, $attemptobj->get_submitted_date());
        $this->assertEquals($user1->id, $attemptobj->get_userid());
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $this->assertEquals(0, $attemptobj->get_number_of_unanswered_questions());
        // Check we don't have grades yet.
        $this->assertEmpty(quiz_get_user_grades($quiz, $user1->id));
        $this->assertNull($attemptobj->get_sum_marks());

        // Now grade the submission.
        $attemptobj->process_grade_submission($timenow);
        $attemptobj = quiz_attempt::create($attempt->id);

        // Check quiz grades.
        $this->assertEquals(true, $attemptobj->is_finished());
        $this->assertEquals(3, $attemptobj->get_sum_marks());

        $grades = quiz_get_user_grades($quiz, $user1->id);
        $grade = array_shift($grades);
        $this->assertEquals(100.0, $grade->rawgrade);

        // Check grade book.
        $gradebookgrades = grade_get_grades($SITE->id, 'mod', 'quiz', $quiz->id, $user1->id);
        $gradebookitem = array_shift($gradebookgrades->items);
        $gradebookgrade = array_shift($gradebookitem->grades);
        $this->assertEquals(100, $gradebookgrade->grade);

        // Update question in quiz.
        $newsa = $questiongenerator->update_question(
            $saq,
            null,
            ['name' => 'This is the second version of shortanswer']
        );
        $newnumbq = $questiongenerator->update_question(
            $numq,
            null,
            ['name' => 'This is the second version of numerical']
        );
        $newmatch = $questiongenerator->update_question(
            $matchq,
            null,
            ['name' => 'This is the second version of match']
        );
        $newdescription = $questiongenerator->update_question(
            $description,
            null,
            ['name' => 'This is the second version of description']
        );

        // Update the attempt to use this questions.
        // Would not normally be done for a non-preview, but this is just a unit test.
        $attemptobj->update_questions_to_new_version_if_changed();

        // Verify.
        $this->assertEquals($newsa->id, $attemptobj->get_question_attempt(1)->get_question_id());
        $this->assertEquals($newnumbq->id, $attemptobj->get_question_attempt(2)->get_question_id());
        $this->assertEquals($newmatch->id, $attemptobj->get_question_attempt(3)->get_question_id());
        $this->assertEquals($newdescription->id, $attemptobj->get_question_attempt(4)->get_question_id());

        // Repeat the checks from above.
        $this->assertEquals(1, $attemptobj->get_attempt_number());
        $this->assertEquals(3, $attemptobj->get_sum_marks());
        $this->assertEquals(true, $attemptobj->is_finished());
        $this->assertEquals($timenow, $attemptobj->get_submitted_date());
        $this->assertEquals($user1->id, $attemptobj->get_userid());
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $this->assertEquals(0, $attemptobj->get_number_of_unanswered_questions());

        // Re-load quiz attempt data and repeat the verification.
        $attemptobj = quiz_attempt::create($attempt->id);

        $this->assertEquals($newsa->id, $attemptobj->get_question_attempt(1)->get_question_id());
        $this->assertEquals($newnumbq->id, $attemptobj->get_question_attempt(2)->get_question_id());
        $this->assertEquals($newmatch->id, $attemptobj->get_question_attempt(3)->get_question_id());
        $this->assertEquals($newdescription->id, $attemptobj->get_question_attempt(4)->get_question_id());

        // Repeat the checks from above.
        $this->assertEquals(1, $attemptobj->get_attempt_number());
        $this->assertEquals(3, $attemptobj->get_sum_marks());
        $this->assertEquals(true, $attemptobj->is_finished());
        $this->assertEquals($timenow, $attemptobj->get_submitted_date());
        $this->assertEquals($user1->id, $attemptobj->get_userid());
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $this->assertEquals(0, $attemptobj->get_number_of_unanswered_questions());
    }

    /**
     * Tests that a quiz attempt with random questions correctly handles cases when question versions have been deleted.
     *
     * @param array $deleteversions Array of identifiers for the question versions to be deleted. Valid array values:
     *                              - q1v1 (question 1, version 1)
     *                              - q1v2 (question 1, version 2)
     *                              - q2v1 (question 2, version 1)
     *                              - q2v2 (question 2, version 2)
     * @param bool $canpreview Whether the given user has capability to preview the quiz.
     * @param array $expectedversions Array of expected question versions per slot after a new attempt following question
     *                                version removal. Valid key-value pairs:
     *                                - 1 => q1v1 (slot 1; question 1, version 1)
     *                                - 1 => q1v2 (slot 1; question 1, version 1)
     *                                - 2 => q2v1 (slot 2; question 2, version 1)
     *                                - 2 => q2v2 (slot 2; question 2, version 2)
     * @param string|null $expectedupdateattempterror The expected error message when quiz attempts are updated following
     *                                                question version removal (if applicable).
     * @param int|null $expectedslotfewquestions The expected random question slot (number) with insufficient questions
     *                                           in the category following question version removal (if applicable).
     * @dataProvider quiz_random_question_deletion_provider
     */
    public function test_quiz_attempt_with_random_questions_when_versions_are_deleted(
        array $deleteversions,
        bool $canpreview,
        array $expectedversions,
        ?string $expectedupdateattempterror = null,
        ?int $expectedslotfewquestions = null
    ): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and enrol a teacher.
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_user();
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        // Create quiz.
        $quiz = $this->getDataGenerator()->create_module('quiz', [
            'course' => $course->id,
            'grade' => 100.0,
            'sumgrades' => 3,
        ]);

        // Create question categories.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat1 = $questiongenerator->create_question_category();
        $cat2 = $questiongenerator->create_question_category();

        // Create questions.
        $questions = [
            'q1v1' => $questiongenerator->create_question('shortanswer', null, ['category' => $cat1->id]),
            'q2v1' => $questiongenerator->create_question('essay', null, ['category' => $cat2->id]),
        ];

        // Update the questions to generate new versions.
        $questions['q1v2'] = $questiongenerator->update_question(
            $questions['q1v1'],
            null,
            ['name' => 'This is the latest version']
        );
        $questions['q2v2'] = $questiongenerator->update_question(
            $questions['q2v1'],
            null,
            ['name' => 'This is the latest version']
        );

        // Add random questions to quiz.
        $this->add_random_questions($quiz->id, 1, $cat1->id, 1);
        $this->add_random_questions($quiz->id, 1, $cat2->id, 1);

        // Start attempt.
        $quizobj = quiz_settings::create($quiz->id, $teacher->id);
        $attempt = quiz_prepare_and_start_new_attempt($quizobj, 1, null, userid: $teacher->id);
        $attemptobj = quiz_attempt::create($attempt->id);

        // Delete the question versions defined in the data provider.
        $deleteids = array_map(fn($label) => $questions[$label]->id, $deleteversions);
        \qbank_deletequestion\helper::delete_questions($deleteids, false);

        if (!$canpreview) { // Update the 'mod/quiz:preview' capability as defined by the data provider.
            $this->setUser($teacher);
            unassign_capability('mod/quiz:preview', $teacherrole->id);
        }

        // Update the quiz attempts.
        try {
            $attemptobj->update_questions_to_new_version_if_changed();
            // An exception is expected if the last preview/attempt is invalidated due to question versions removed for
            // this preview/attempt.
            if ($expectedupdateattempterror) {
                $this->fail('Exception expected due to preview is deleted');
            }
        } catch (\moodle_exception $e) {
            // Verify the expected exception message.
            $this->assertEquals($expectedupdateattempterror, $e->getMessage());
        }

        // An exception is expected if an existing random question lacks sufficient questions in the category due to
        // question version removal.
        // The new attempt should fail as a result.
        if ($expectedslotfewquestions) {
            $structure = $quizobj->get_structure();
            $slot = $structure->get_slot_by_number($expectedslotfewquestions);
            // Verify the expected exception message.
            $this->expectExceptionMessage(
                get_string(
                    'notenoughrandomquestions',
                    'quiz',
                    (object) [
                        'category' => $slot->category,
                        'name' => 'Random question',
                        'id' => $slot->slotid,
                    ]
                )
            );
        }

        // Start a new quiz attempt.
        $attempt = quiz_prepare_and_start_new_attempt($quizobj, 2, $attempt, userid: $teacher->id);
        $attemptobj = quiz_attempt::create($attempt->id);

        // Verify the expected question versions for each slot for the given quiz attempt.
        foreach ($expectedversions as $slot => $expectedversion) {
            $this->assertEquals($questions[$expectedversion]->id, $attemptobj->get_question_attempt($slot)->get_question_id());
        }
    }

    /**
     * Data provider for test_quiz_attempt_with_random_questions_when_versions_are_deleted.
     *
     * @return array
     */
    public static function quiz_random_question_deletion_provider(): array {
        return [
            'Delete only latest versions for both q1 and q2; quiz preview available' => [
                ['q1v2', 'q2v2'],
                true,
                [
                    1 => 'q1v1',
                    2 => 'q2v1',
                ],
                get_string('attempterrorcontentchange', 'quiz'),
            ],
            'Delete only latest versions for both q1 and q2; quiz preview not available' => [
                ['q1v2', 'q2v2'],
                false,
                [
                    1 => 'q1v1',
                    2 => 'q2v1',
                ],
                get_string('attempterrorcontentchangeforuser', 'quiz'),
            ],
            'Delete all question versions for q1 only; quiz preview available' => [
                ['q1v1', 'q1v2'],
                true,
                [
                    1 => 'q1v1',
                    2 => 'q2v1',
                ],
                get_string('attempterrorcontentchange', 'quiz'),
                1,
            ],
            'Delete all question versions for q2 only; quiz preview not available' => [
                ['q2v1', 'q2v2'],
                false,
                [
                    1 => 'q1v1',
                    2 => 'q2v1',
                ],
                get_string('attempterrorcontentchangeforuser', 'quiz'),
                2,
            ],
            'Delete only initial question versions for q1 and q2; quiz preview available' => [
                ['q1v1', 'q2v1'],
                true,
                [
                    1 => 'q1v2',
                    2 => 'q2v2',
                ],
            ],
        ];
    }

    /**
     * Create a quiz containing one question and a close time.
     *
     * The question is the standard shortanswer test question.
     * The quiz is set to close 1 hour from now.
     * The quiz is set to use a grade period of 1 hour once time expires.
     *
     * @param string $overduehandling value for the overduehandling quiz setting.
     * @return \stdClass the quiz that was created.
     */
    protected function create_quiz_with_one_question(string $overduehandling = 'graceperiod'): \stdClass {
        global $SITE;
        $this->resetAfterTest();

        // Make a quiz.
        $timeclose = time() + HOURSECS;
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance(
            ['course' => $SITE->id, 'timeclose' => $timeclose,
            'overduehandling' => $overduehandling,
            'graceperiod' => HOURSECS]
        );

        // Create a question.
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $saq = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);

        // Add them to the quiz.
        $quizobj = quiz_settings::create($quiz->id);
        quiz_add_quiz_question($saq->id, $quiz, 0, 1);
        $quizobj->get_grade_calculator()->recompute_quiz_sumgrades();

        return $quiz;
    }

    public function test_quiz_attempt_walkthrough_submit_time_recorded_correctly_when_overdue(): void {

        $quiz = $this->create_quiz_with_one_question();

        // Make a user to do the quiz.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $quizobj = quiz_settings::create($quiz->id, $user->id);

        // Start the attempt.
        $attempt = quiz_prepare_and_start_new_attempt($quizobj, 1, null);

        // Process some responses from the student.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertEquals(1, $attemptobj->get_number_of_unanswered_questions());
        $attemptobj->process_submitted_actions($quiz->timeclose - 30 * MINSECS, false, [1 => ['answer' => 'frog']]);

        // Attempt goes overdue (e.g. if cron ran).
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_going_overdue($quiz->timeclose + 2 * get_config('quiz', 'graceperiodmin'), false);

        // Verify the attempt state.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertEquals(1, $attemptobj->get_attempt_number());
        $this->assertEquals(false, $attemptobj->is_finished());
        $this->assertEquals(0, $attemptobj->get_submitted_date());
        $this->assertEquals($user->id, $attemptobj->get_userid());
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $this->assertEquals(0, $attemptobj->get_number_of_unanswered_questions());

        // Student submits the attempt during the grace period.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_attempt($quiz->timeclose + 30 * MINSECS, true, false, 1);

        // Verify the attempt state.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertEquals(1, $attemptobj->get_attempt_number());
        $this->assertEquals(true, $attemptobj->is_finished());
        $this->assertEquals($quiz->timeclose + 30 * MINSECS, $attemptobj->get_submitted_date());
        $this->assertEquals($user->id, $attemptobj->get_userid());
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $this->assertEquals(0, $attemptobj->get_number_of_unanswered_questions());
    }

    public function test_quiz_attempt_walkthrough_close_time_extended_at_last_minute(): void {
        global $DB;

        $quiz = $this->create_quiz_with_one_question();
        $originaltimeclose = $quiz->timeclose;

        // Make a user to do the quiz.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $quizobj = quiz_settings::create($quiz->id, $user->id);

        // Start the attempt.
        $attempt = quiz_prepare_and_start_new_attempt($quizobj, 1, null);

        // Process some responses from the student during the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submitted_actions($originaltimeclose - 30 * MINSECS, false, [1 => ['answer' => 'frog']]);

        // Teacher edits the quiz to extend the time-limit by one minute.
        $DB->set_field('quiz', 'timeclose', $originaltimeclose + MINSECS, ['id' => $quiz->id]);
        \course_modinfo::clear_instance_cache($quiz->course);

        // Timer expires in the student browser and thinks it is time to submit the quiz.
        // This sets $finishattempt to false - since the student did not click the button, and $timeup to true.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_attempt($originaltimeclose, false, true, 1);

        // Verify the attempt state - the $timeup was ignored becuase things have changed server-side.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertEquals(1, $attemptobj->get_attempt_number());
        $this->assertFalse($attemptobj->is_finished());
        $this->assertEquals(quiz_attempt::IN_PROGRESS, $attemptobj->get_state());
        $this->assertEquals(0, $attemptobj->get_submitted_date());
        $this->assertEquals($user->id, $attemptobj->get_userid());
    }

    /**
     * Create a quiz with a random as well as other questions and walk through quiz attempts.
     */
    public function test_quiz_with_random_question_attempt_walkthrough(): void {
        global $SITE;

        $this->resetAfterTest(true);
        question_bank::get_qtype('random')->clear_caches_before_testing();

        $this->setAdminUser();

        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance(['course' => $SITE->id, 'questionsperpage' => 2, 'grade' => 100.0,
                                                      'sumgrades' => 4]);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Add two questions to question category.
        $cat = $questiongenerator->create_question_category();
        $saq = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        $numq = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);

        // Add random question to the quiz.
        $this->add_random_questions($quiz->id, 0, $cat->id, 1);

        // Make another category.
        $cat2 = $questiongenerator->create_question_category();
        $match = $questiongenerator->create_question('match', null, ['category' => $cat->id]);

        quiz_add_quiz_question($match->id, $quiz, 0);

        $multichoicemulti = $questiongenerator->create_question('multichoice', 'two_of_four', ['category' => $cat->id]);

        quiz_add_quiz_question($multichoicemulti->id, $quiz, 0);

        $multichoicesingle = $questiongenerator->create_question('multichoice', 'one_of_four', ['category' => $cat->id]);

        quiz_add_quiz_question($multichoicesingle->id, $quiz, 0);

        foreach ([$saq->id => 'frog', $numq->id => '3.14'] as $randomqidtoselect => $randqanswer) {
            // Make a new user to do the quiz each loop.
            $user1 = $this->getDataGenerator()->create_user();
            $this->setUser($user1);

            $quizobj = quiz_settings::create($quiz->id, $user1->id);

            // Start the attempt.
            $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
            $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

            $timenow = time();
            $attempt = quiz_create_attempt($quizobj, 1, false, $timenow);

            quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow, [1 => $randomqidtoselect]);
            $this->assertEquals('1,2,0,3,4,0', $attempt->layout);

            quiz_attempt_save_started($quizobj, $quba, $attempt);

            // Process some responses from the student.
            $attemptobj = quiz_attempt::create($attempt->id);
            $this->assertFalse($attemptobj->has_response_to_at_least_one_graded_question());
            $this->assertEquals(4, $attemptobj->get_number_of_unanswered_questions());

            $tosubmit = [];
            $selectedquestionid = $quba->get_question_attempt(1)->get_question_id();
            $tosubmit[1] = ['answer' => $randqanswer];
            $tosubmit[2] = [
                'frog' => 'amphibian',
                'cat'  => 'mammal',
                'newt' => 'amphibian'];
            $tosubmit[3] = ['One' => '1', 'Two' => '0', 'Three' => '1', 'Four' => '0']; // First and third choice.
            $tosubmit[4] = ['answer' => 'One']; // The first choice.

            $attemptobj->process_submitted_actions($timenow, false, $tosubmit);

            // Finish the attempt.
            $attemptobj = quiz_attempt::create($attempt->id);
            $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
            $this->assertEquals(0, $attemptobj->get_number_of_unanswered_questions());
            $attemptobj->process_submit($timenow, false);

            // Re-load quiz attempt data.
            $attemptobj = quiz_attempt::create($attempt->id);

            // Check that results are stored as expected.
            $this->assertEquals(1, $attemptobj->get_attempt_number());
            $this->assertEquals(false, $attemptobj->is_finished());
            $this->assertEquals($timenow, $attemptobj->get_submitted_date());
            $this->assertEquals($user1->id, $attemptobj->get_userid());
            $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
            $this->assertEquals(0, $attemptobj->get_number_of_unanswered_questions());

            // Check we don't have grades yet.
            $this->assertEmpty(quiz_get_user_grades($quiz, $user1->id));
            $this->assertNull($attemptobj->get_sum_marks());

            // Now grade the submission.
            $attemptobj->process_grade_submission($timenow);
            $attemptobj = quiz_attempt::create($attempt->id);

            // Check quiz grades.
            $this->assertEquals(true, $attemptobj->is_finished());
            $this->assertEquals(4, $attemptobj->get_sum_marks());

            // Check quiz grades.
            $grades = quiz_get_user_grades($quiz, $user1->id);
            $grade = array_shift($grades);
            $this->assertEquals(100.0, $grade->rawgrade);

            // Check grade book.
            $gradebookgrades = grade_get_grades($SITE->id, 'mod', 'quiz', $quiz->id, $user1->id);
            $gradebookitem = array_shift($gradebookgrades->items);
            $gradebookgrade = array_shift($gradebookitem->grades);
            $this->assertEquals(100, $gradebookgrade->grade);
        }
    }

    /**
     * Get the correct response for variants.
     *
     * @return array
     */
    public static function get_correct_response_for_variants(): array {
        return [[1, 9.9], [2, 8.5], [5, 14.2], [10, 6.8, true]];
    }

    /** @var ?\quiz A quiz with variants */
    protected $quizwithvariants = null;

    /**
     * Create a quiz with a single question with variants and walk through quiz attempts.
     *
     * @dataProvider get_correct_response_for_variants
     */
    public function test_quiz_with_question_with_variants_attempt_walkthrough($variantno, $correctresponse, $done = false): void {
        global $SITE;

        $this->resetAfterTest($done);

        $this->setAdminUser();

        if ($this->quizwithvariants === null) {
            // Make a quiz.
            $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

            $this->quizwithvariants = $quizgenerator->create_instance(['course' => $SITE->id,
                                                                            'questionsperpage' => 0,
                                                                            'grade' => 100.0,
                                                                            'sumgrades' => 1]);

            $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

            $cat = $questiongenerator->create_question_category();
            $calc = $questiongenerator->create_question('calculatedsimple', 'sumwithvariants', ['category' => $cat->id]);
            quiz_add_quiz_question($calc->id, $this->quizwithvariants, 0);
        }


        // Make a new user to do the quiz.
        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);
        $quizobj = quiz_settings::create($this->quizwithvariants->id, $user1->id);

        // Start the attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow);

        // Select variant.
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow, [], [1 => $variantno]);
        $this->assertEquals('1,0', $attempt->layout);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Process some responses from the student.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertFalse($attemptobj->has_response_to_at_least_one_graded_question());
        $this->assertEquals(1, $attemptobj->get_number_of_unanswered_questions());

        $tosubmit = [1 => ['answer' => $correctresponse]];
        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $this->assertEquals(0, $attemptobj->get_number_of_unanswered_questions());

        $attemptobj->process_submit($timenow, false);

        // Re-load quiz attempt data.
        $attemptobj = quiz_attempt::create($attempt->id);

        // Check that results are stored as expected.
        $this->assertEquals(1, $attemptobj->get_attempt_number());
        $this->assertEquals(false, $attemptobj->is_finished());
        $this->assertEquals($timenow, $attemptobj->get_submitted_date());
        $this->assertEquals($user1->id, $attemptobj->get_userid());
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $this->assertEquals(0, $attemptobj->get_number_of_unanswered_questions());

        // Check we don't have grades yet.
        $this->assertEmpty(quiz_get_user_grades($this->quizwithvariants, $user1->id));
        $this->assertNull($attemptobj->get_sum_marks());

        // Now grade the submission.
        $attemptobj->process_grade_submission($timenow);
        $attemptobj = quiz_attempt::create($attempt->id);

        // Check quiz grades.
        $this->assertEquals(true, $attemptobj->is_finished());
        $this->assertEquals(1, $attemptobj->get_sum_marks());
        // Check quiz grades.
        $grades = quiz_get_user_grades($this->quizwithvariants, $user1->id);
        $grade = array_shift($grades);
        $this->assertEquals(100.0, $grade->rawgrade);

        // Check grade book.
        $gradebookgrades = grade_get_grades($SITE->id, 'mod', 'quiz', $this->quizwithvariants->id, $user1->id);
        $gradebookitem = array_shift($gradebookgrades->items);
        $gradebookgrade = array_shift($gradebookitem->grades);
        $this->assertEquals(100, $gradebookgrade->grade);
    }

    public function test_quiz_attempt_walkthrough_abandoned_attempt_reopened_with_timelimit_override(): void {
        global $DB;

        $quiz = $this->create_quiz_with_one_question('autoabandon');
        $originaltimeclose = $quiz->timeclose;

        // Make a user to do the quiz.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $quizobj = quiz_settings::create($quiz->id, $user->id);

        // Start the attempt.
        $attempt = quiz_prepare_and_start_new_attempt($quizobj, 1, null);

        // Process some responses from the student during the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submitted_actions($originaltimeclose - 30 * MINSECS, false, [1 => ['answer' => 'frog']]);

        // Student leaves, so cron closes the attempt when time expires.
        $attemptobj->process_abandon($originaltimeclose + 5 * MINSECS, false);

        // Verify the attempt state.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertEquals(quiz_attempt::ABANDONED, $attemptobj->get_state());
        $this->assertEquals(0, $attemptobj->get_submitted_date());
        $this->assertEquals($user->id, $attemptobj->get_userid());

        // The teacher feels kind, so adds an override for the student, and re-opens the attempt.
        $sink = $this->redirectEvents();
        $overriddentimeclose = $originaltimeclose + HOURSECS;
        $DB->insert_record('quiz_overrides', [
            'quiz' => $quiz->id,
            'userid' => $user->id,
            'timeclose' => $overriddentimeclose,
        ]);
        $attemptobj = quiz_attempt::create($attempt->id);
        $reopentime = $originaltimeclose + 10 * MINSECS;
        $attemptobj->process_reopen_abandoned($reopentime);

        // Verify the attempt state.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertEquals(1, $attemptobj->get_attempt_number());
        $this->assertFalse($attemptobj->is_finished());
        $this->assertEquals(quiz_attempt::IN_PROGRESS, $attemptobj->get_state());
        $this->assertEquals(0, $attemptobj->get_submitted_date());
        $this->assertEquals($user->id, $attemptobj->get_userid());
        $this->assertEquals(
            $overriddentimeclose,
            $attemptobj->get_access_manager($reopentime)->get_end_time($attemptobj->get_attempt())
        );

        // Verify this was logged correctly.
        $events = $sink->get_events();
        $this->assertCount(1, $events);

        $reopenedevent = array_shift($events);
        $this->assertInstanceOf('\mod_quiz\event\attempt_reopened', $reopenedevent);
        $this->assertEquals($attemptobj->get_context(), $reopenedevent->get_context());
        $this->assertEquals(
            new moodle_url('/mod/quiz/review.php', ['attempt' => $attemptobj->get_attemptid()]),
            $reopenedevent->get_url()
        );
    }

    public function test_quiz_attempt_walkthrough_abandoned_attempt_reopened_after_close_time(): void {
        $quiz = $this->create_quiz_with_one_question('autoabandon');
        $originaltimeclose = $quiz->timeclose;

        // Make a user to do the quiz.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $quizobj = quiz_settings::create($quiz->id, $user->id);

        // Start the attempt.
        $attempt = quiz_prepare_and_start_new_attempt($quizobj, 1, null);

        // Process some responses from the student during the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submitted_actions($originaltimeclose - 30 * MINSECS, false, [1 => ['answer' => 'frog']]);

        // Student leaves, so cron closes the attempt when time expires.
        $attemptobj->process_abandon($originaltimeclose + 5 * MINSECS, false);

        // Verify the attempt state.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertEquals(quiz_attempt::ABANDONED, $attemptobj->get_state());
        $this->assertEquals(0, $attemptobj->get_submitted_date());
        $this->assertEquals($user->id, $attemptobj->get_userid());

        // The teacher reopens the attempt without granting more time, so previously submitted responess are graded.
        $sink = $this->redirectEvents();
        $reopentime = $originaltimeclose + 10 * MINSECS;
        $attemptobj->process_reopen_abandoned($reopentime);

        // Verify the attempt state.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertEquals(1, $attemptobj->get_attempt_number());
        $this->assertTrue($attemptobj->is_finished());
        $this->assertEquals(quiz_attempt::FINISHED, $attemptobj->get_state());
        $this->assertEquals($originaltimeclose, $attemptobj->get_submitted_date());
        $this->assertEquals($user->id, $attemptobj->get_userid());
        $this->assertEquals(1, $attemptobj->get_sum_marks());

        // Verify this was logged correctly - there are some gradebook events between the two we want to check.
        $events = $sink->get_events();
        $this->assertGreaterThanOrEqual(3, $events);

        $attempturl = new moodle_url(
            '/mod/quiz/review.php',
            ['attempt' => $attemptobj->get_attemptid()],
        );
        $reopenedevent = array_shift($events);
        $this->assertInstanceOf('\mod_quiz\event\attempt_reopened', $reopenedevent);
        $this->assertEquals($attemptobj->get_context(), $reopenedevent->get_context());

        $this->assertEquals($attempturl, $reopenedevent->get_url());

        $submittedevent = array_shift($events);
        $this->assertInstanceOf('\mod_quiz\event\attempt_submitted', $submittedevent);
        $this->assertEquals($attemptobj->get_context(), $submittedevent->get_context());

        $this->assertEquals($attempturl, $submittedevent->get_url());

        $gradedevent = array_pop($events);
        $this->assertInstanceOf('\mod_quiz\event\attempt_graded', $gradedevent);
        $this->assertEquals($attemptobj->get_context(), $gradedevent->get_context());
        $this->assertEquals($attempturl, $gradedevent->get_url());
    }

    /**
     * Create a quiz with questions, pre-create an attempt, edit a question, then begin the attempt.
     */
    public function test_quiz_attempt_walkthrough_update_question_after_precreate(): void {
        global $SITE;

        $this->resetAfterTest(true);

        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance(
            [
                'course' => $SITE->id,
                'questionsperpage' => 0,
                'grade' => 100.0,
                'sumgrades' => 3,
            ],
        );

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $saq = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        $numq = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        $matchq = $questiongenerator->create_question('match', null, ['category' => $cat->id]);
        $description = $questiongenerator->create_question('description', null, ['category' => $cat->id]);

        // Add them to the quiz.
        quiz_add_quiz_question($saq->id, $quiz);
        quiz_add_quiz_question($numq->id, $quiz);
        quiz_add_quiz_question($matchq->id, $quiz);
        quiz_add_quiz_question($description->id, $quiz);

        // Make a user to do the quiz.
        $user1 = $this->getDataGenerator()->create_user();

        $quizobj = quiz_settings::create($quiz->id, $user1->id);

        // Start the attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $user1->id);

        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        $this->assertEquals('1,2,3,4,0', $attempt->layout);

        quiz_attempt_save_not_started($quba, $attempt);

        $attemptobj = quiz_attempt::create($attempt->id);

        // Update question in quiz.
        $newsa = $questiongenerator->update_question($saq, null,
            ['name' => 'This is the second version of shortanswer']);
        $newnumbq = $questiongenerator->update_question($numq, null,
            ['name' => 'This is the second version of numerical']);
        $newmatch = $questiongenerator->update_question($matchq, null,
            ['name' => 'This is the second version of match']);
        $newdescription = $questiongenerator->update_question($description, null,
            ['name' => 'This is the second version of description']);

        $this->assertEquals($saq->id, $attemptobj->get_question_attempt(1)->get_question_id());
        $this->assertEquals($numq->id, $attemptobj->get_question_attempt(2)->get_question_id());
        $this->assertEquals($matchq->id, $attemptobj->get_question_attempt(3)->get_question_id());
        $this->assertEquals($description->id, $attemptobj->get_question_attempt(4)->get_question_id());

        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Verify that the started attempt contains the new questions.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertEquals($newsa->id, $attemptobj->get_question_attempt(1)->get_question_id());
        $this->assertEquals($newnumbq->id, $attemptobj->get_question_attempt(2)->get_question_id());
        $this->assertEquals($newmatch->id, $attemptobj->get_question_attempt(3)->get_question_id());
        $this->assertEquals($newdescription->id, $attemptobj->get_question_attempt(4)->get_question_id());
    }
}
