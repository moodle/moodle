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

use advanced_testcase;
use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;
use mod_quiz\task\grade_submission;

/**
 * Unit tests for grade_submission task.
 *
 * @package   mod_quiz
 * @copyright 2026 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Conn Warwicker <conn.warwicker@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \mod_quiz\task\grade_submission
 */
final class grade_submission_test extends advanced_testcase {
    /**
     * Create a quiz attempt to use for testing
     * @return quiz_attempt
     */
    private function create_attempt(): quiz_attempt {
        $this->resetAfterTest(true);

        // Make a user to do the quiz.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'grade' => 100.0, 'sumgrades' => 2]);
        $quizobj = quiz_settings::create($quiz->id, $user->id);

        // Add a question.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        quiz_add_quiz_question($question->id, $quiz, 1);

        $attempt = quiz_prepare_and_start_new_attempt($quizobj, 1, null, false, [], [], $user->id);
        return quiz_attempt::create($attempt->id);
    }

    /**
     * Test that passing through an invalid attempt id does not break the task in any way
     */
    public function test_invalid_attempt(): void {
        // Try a negative number which will always be invalid as an ID.
        $task = grade_submission::instance(-1);

        // Execute the task and confirm that nothing untoward happens.
        $task->execute();
        $this->expectOutputString("Attempt ID -1 not found, or not in submitted state.\n");
    }

    /**
     * Test executing the task on an inprogress attempt.
     */
    public function test_attempt_state_inprogress(): void {
        // Try and inprogress attempt.
        $attempt = $this->create_attempt();
        $this->assertEquals(quiz_attempt::IN_PROGRESS, $attempt->get_state());

        // Execute the task for this attempt.
        $task = grade_submission::instance($attempt->get_attemptid());
        $task->execute();

        // This should not be executed by the task.
        $this->expectOutputString("Attempt ID " . $attempt->get_attemptid() . " not found, or not in submitted state.\n");
    }

    /**
     * Test executing the task on a submitted attempt.
     */
    public function test_attempt_state_submitted(): void {
        // Try a "submitted" attempt, which is the expected state to be processed by the task.
        $attempt = $this->create_attempt();
        $attempt->process_submit(time(), false);
        $this->assertEquals(quiz_attempt::SUBMITTED, $attempt->get_state());

        // Execute the task for this attempt.
        $task = grade_submission::instance($attempt->get_attemptid());
        $task->execute();

        // This should grade the attempt and mark it as finished.
        $this->expectOutputRegex('/Grading attempt for user ID \d+ for quiz Quiz 1 on course tc_1/');

        // Reload the attempt, as the `attempt` record on the quiz_attempt object doesn't seem to get updated.
        $attempt = quiz_attempt::create($attempt->get_attemptid());
        $this->assertEquals(quiz_attempt::FINISHED, $attempt->get_state());
    }

    /**
     * Test executing the task on a finished attempt.
     */
    public function test_attempt_state_finished(): void {
        // Try a "finished" attempt.
        $attempt = $this->create_attempt();
        $attempt->process_grade_submission(time());

        // At this point it should be finished.
        $this->assertEquals(quiz_attempt::FINISHED, $attempt->get_state());

        // Now try the task again, on the finished attempt.
        $task = grade_submission::instance($attempt->get_attemptid());
        $task->execute();

        // This should not be executed by the task.
        $this->expectOutputString("Attempt ID {$attempt->get_attemptid()} not found, or not in submitted state.\n");
    }
}
