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
 * Unit tests for (some of) plagiarism/turnitin/classes/modules/turnitin_quiz.class.php.
 *
 * @package    plagiarism_turnitin
 * @copyright  2017 Turnitin
 * @copyright  2022 The University of Southern Queensland
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/plagiarism/turnitin/lib.php');

/**
 * Tests for Turnitin quiz class.
 *
 * @package plagiarism_turnitin
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plagiarism_turnitin_quiz_testcase extends advanced_testcase {
    /**
     * Proves that essay response marks are correctly updated.
     *
     * @copyright 2014 Tim Hunt
     */
    public function test_update_mark() {
        $this->resetAfterTest();

        // Create a user, course, and quiz activity with an essay question.
        // Lifted largely from mod_quiz_attempt_testcase.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $generator->create_instance([
            'course' => $course->id,
            'grade' => 100,
            'sumgrades' => 1,
            'layout' => '1,0',
        ]);

        if (class_exists('\mod_quiz\quiz_settings')) {
            $quizsettingsclass = '\mod_quiz\quiz_settings';
            $quizattemptclass = '\mod_quiz\quiz_attempt';
        } else {
            $quizsettingsclass = 'quiz';
            $quizattemptclass = 'quiz_attempt';
        }

        $quizobj = $quizsettingsclass::create($quiz->id, $user->id);
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('essay', null, ['category' => $cat->id]);
        quiz_add_quiz_question($question->id, $quiz, 1, 1);   // 1 mark for the question.

        // Start and finish an attempt at the quiz.
        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $user->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);
        $attemptobj = $quizattemptclass::create($attempt->id);
        $attemptobj->process_finish($timenow, false);

        // Expect no marks or grade for the attempt yet.
        $attemptobj = $quizattemptclass::create($attempt->id);
        $this->assertEquals(0.0, $attemptobj->get_sum_marks());
        $grade = quiz_get_best_grade($quiz, $user->id);
        $this->assertEquals(0.0, $grade);

        // Now update the grade of the essay question through the Turnitin quiz class.
        $tiiquiz = new turnitin_quiz;
        $answer = $attemptobj->get_question_attempt(1)->get_response_summary();
        $slot = 1;
        $identifier = sha1($answer.$slot);
        $tiiquiz->update_mark($attempt->id, $identifier, $user->id, 75, $quiz->grade);

        // Reload the attempt and check the total marks and grade are as we expect it.
        $attemptobj = $quizattemptclass::create($attempt->id);
        $this->assertEquals(0.75, $attemptobj->get_sum_marks());
        $grade = quiz_get_best_grade($quiz, $user->id);
        $this->assertEquals(75.0, $grade);
    }
}
