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

namespace tool_mergeusers;

use advanced_testcase;
use coding_exception;
use dml_exception;
use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;
use question_engine;
use tool_mergeusers\local\merger\quiz_attempts_table_merger;
use tool_mergeusers\local\user_merger;

/**
 * Quiz merger tests.
 *
 * @package    tool_mergeusers
 * @author     Andrew Hancox <andrewdchancox@googlemail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class quiz_test extends advanced_testcase {
    /** @var object test course 1. */
    private object $course1;
    /** @var object test course 2. */
    private object $course2;
    /** @var object test user. */
    private object $usertoremove;
    /** @var object test user. */
    private object $usertokeep;
    /** @var object test quiz 1 */
    private object $quiz1;
    /** @var object test quiz 2 */
    private object $quiz2;

    /**
     * Configure the test.
     * Create two courses with a quiz in each.
     * Create two users.
     * Enrol the users onto the courses.
     */
    public function setUp(): void {
        global $CFG, $DB;
        parent::setUp();
        $this->resetAfterTest(true);

        // Setup two users to merge.
        $this->usertoremove = $this->getDataGenerator()->create_user();
        $this->usertokeep   = $this->getDataGenerator()->create_user();

        // Create three courses.
        $this->course1 = $this->getDataGenerator()->create_course();
        $this->course2 = $this->getDataGenerator()->create_course();

        $this->quiz1 = $this->add_quiz_to_course($this->course1);
        $this->quiz2 = $this->add_quiz_to_course($this->course2);

        $maninstance1 = $DB->get_record('enrol', [
            'courseid' => $this->course1->id,
            'enrol'    => 'manual',
        ], '*', MUST_EXIST);

        $maninstance2 = $DB->get_record('enrol', [
            'courseid' => $this->course2->id,
            'enrol'    => 'manual',
        ], '*', MUST_EXIST);

        $manual = enrol_get_plugin('manual');

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);

        // Enrol the users on the courses.
        $manual->enrol_user($maninstance1, $this->usertoremove->id, $studentrole->id);
        $manual->enrol_user($maninstance1, $this->usertokeep->id, $studentrole->id);

        $manual->enrol_user($maninstance2, $this->usertoremove->id, $studentrole->id);
        $manual->enrol_user($maninstance2, $this->usertokeep->id, $studentrole->id);
    }

    /**
     * Utility method to add a quiz to a course.
     *
     * @param object $course
     * @return object
     * @throws coding_exception
     */
    private function add_quiz_to_course(object $course): object {
        // Add a quiz to the course.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz          = $quizgenerator->create_instance([
            'course' => $course->id,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2,
        ]);

        // Create a couple of questions using test data in mod_quiz.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat               = $questiongenerator->create_question_category();
        $saq               = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        $numq              = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);

        // Add them to the quiz.
        quiz_add_quiz_question($saq->id, $quiz);
        quiz_add_quiz_question($numq->id, $quiz);

        return $quiz;
    }

    /**
     * Get an answer to the quiz that is 50% right.
     *
     * @return array
     */
    private function get_fiftypercent_answers() {
        return [
            1 => ['answer' => 'frog'],
            2 => ['answer' => '3.15'],
        ];
    }

    /**
     * Utility method to get the grade for a user.
     * @param object $user
     * @param object $quiz
     * @param object $course
     * @return string
     */
    private function get_user_quiz_grade(object $user, object $quiz, object $course): string {
        $gradebookgrades = \grade_get_grades($course->id, 'mod', 'quiz', $quiz->id, $user->id);
        $gradebookitem = array_shift($gradebookgrades->items);
        $grade = $gradebookitem->grades[$user->id];
        return $grade->str_grade;
    }

    /**
     * Get an answer to the quiz that is 100% right.
     *
     * @return array
     */
    private function get_hundredpercent_answers(): array {
        return [
            1 => ['answer' => 'frog'],
            2 => ['answer' => '3.14'],
        ];
    }

    /**
     * Utility method to submit an attempt on a quiz.
     *
     * @param object $quiz
     * @param object $user
     * @param array $answers
     * @return int submission finish time.
     */
    private function submit_quiz_attempt(object $quiz, object $user, array $answers): int {
        // Create a quiz attempt for the user.
        $quizobj = quiz_settings::create($quiz->id, $user->id);

        // Set up and start an attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $user->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submitted_actions($timenow, false, $answers);

        $timefinish = time();

        // Finish the attempt.
        $this->finish_attempt($attemptobj, $timefinish);

        return $timefinish;
    }

    /**
     * Provides a necessary abstraction to support Moodle 5.0 onwards for quiz testing.
     *
     * If this test is executed on Moodle 5, it executes the new attempt methods, preventing deprecation warnings.
     *
     * On Moodle 4.5, it executes the necessary attempt method for testing.
     *
     * TODO: If, at some time, this code is only supported for Moodle 5 onwards, we will be able to remove the condition
     * and leave only the Moodle-5-compatible code.
     *
     * @param quiz_attempt $attempt
     * @param int $timefinish
     * @return void
     */
    private function finish_attempt(quiz_attempt $attempt, int $timefinish): void {
        if (method_exists($attempt, 'process_submit')) {
            // Temporary patch for supporting for Moodle 5.0 and onwards.
            $attempt->process_submit($timefinish, false);
            $attempt->process_grade_submission($timefinish);
        } else {
            // Valid only for Moodle 4.5 branch.
            $attempt->process_finish($timefinish, false);
        }
    }

    /**
     * Have two users attempt the same quiz and then merge them.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_quiz
     * @throws dml_exception
     */
    public function test_merge_conflicting_quiz_attempts(): void {
        global $DB;

        $this->submit_quiz_attempt($this->quiz1, $this->usertokeep, $this->get_fiftypercent_answers());
        $this->submit_quiz_attempt($this->quiz1, $this->usertoremove, $this->get_hundredpercent_answers());

        // User to keep gets 50%, user to remove gets 100%.
        $this->assertEquals('50.00', $this->get_user_quiz_grade($this->usertokeep, $this->quiz1, $this->course1));
        $this->assertEquals('100.00', $this->get_user_quiz_grade($this->usertoremove, $this->quiz1, $this->course1));

        set_config('quizattemptsaction', quiz_attempts_table_merger::ACTION_RENUMBER, 'tool_mergeusers');

        $mut = new user_merger();
        $mut->merge($this->usertokeep->id, $this->usertoremove->id);

        // User to keep should now have 100%.
        $this->assertEquals('100.00', $this->get_user_quiz_grade($this->usertokeep, $this->quiz1, $this->course1));

        $userremove = $DB->get_record('user', ['id' => $this->usertoremove->id]);
        $this->assertEquals(1, $userremove->suspended);
    }

    /**
     * Have two users attempt different quizes and then merge them.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_quiz
     * @throws dml_exception
     */
    public function test_merge_non_conflicting_quiz_attempts(): void {
        global $DB;

        $this->submit_quiz_attempt($this->quiz1, $this->usertokeep, $this->get_fiftypercent_answers());
        $this->submit_quiz_attempt($this->quiz2, $this->usertoremove, $this->get_hundredpercent_answers());

        $this->assertEquals('50.00', $this->get_user_quiz_grade($this->usertokeep, $this->quiz1, $this->course1));
        $this->assertEquals('-', $this->get_user_quiz_grade($this->usertokeep, $this->quiz2, $this->course2));
        $this->assertEquals('-', $this->get_user_quiz_grade($this->usertoremove, $this->quiz1, $this->course1));
        $this->assertEquals('100.00', $this->get_user_quiz_grade($this->usertoremove, $this->quiz2, $this->course2));

        set_config('quizattemptsaction', quiz_attempts_table_merger::ACTION_RENUMBER, 'tool_mergeusers');

        $mut = new user_merger();
        $mut->merge($this->usertokeep->id, $this->usertoremove->id);

        $this->assertEquals('50.00', $this->get_user_quiz_grade($this->usertokeep, $this->quiz1, $this->course1));
        $this->assertEquals('100.00', $this->get_user_quiz_grade($this->usertokeep, $this->quiz2, $this->course2));

        $userremove = $DB->get_record('user', ['id' => $this->usertoremove->id]);
        $this->assertEquals(1, (int)$userremove->suspended);
    }
}
