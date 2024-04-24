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

namespace mod_quiz\external;

use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;

/**
 * Test events for external service.
 *
 * @package   mod_quiz
 * @copyright 2024 The Open University.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.4
 */
class events_test extends \advanced_testcase {

    /**
     * Test get_users_in_report service.
     *
     * @covers ::get_users_in_report
     */
    public function test_get_users_in_report(): void {
        $this->resetAfterTest();

        $dg = $this->getDataGenerator();
        $course = $dg->create_course();
        $quizgen = $dg->get_plugin_generator('mod_quiz');
        $this->setAdminUser();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $dg->enrol_user($u1->id, $course->id, 'student');
        $dg->enrol_user($u2->id, $course->id, 'student');

        $quiz = $quizgen->create_instance(['course' => $course->id, 'sumgrades' => 2]);

        // Questions.
        $questgen = $dg->get_plugin_generator('core_question');
        $quizcat = $questgen->create_question_category();
        $question = $questgen->create_question('numerical', null, ['category' => $quizcat->id]);
        quiz_add_quiz_question($question->id, $quiz);

        $quizobj1a = quiz_settings::create($quiz->id, $u1->id);
        $quizobj1b = quiz_settings::create($quiz->id, $u2->id);

        // Set attempts.
        $quba1a = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj1a->get_context());
        $quba1a->set_preferred_behaviour($quizobj1a->get_quiz()->preferredbehaviour);
        $quba1b = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj1b->get_context());
        $quba1b->set_preferred_behaviour($quizobj1b->get_quiz()->preferredbehaviour);

        $timenow = time();

        $users = get_users_in_report::execute($quiz->cmid, 'overview',
            'quiz_overview_table', 'quiz_overview_options', 'enrolled_with', '', false)['users'];
        $this->assertCount(0, $users);

        // User 1 passes quiz 1.
        $attempt = quiz_create_attempt($quizobj1a, 1, false, $timenow, false, $u1->id);
        quiz_start_new_attempt($quizobj1a, $quba1a, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj1a, $quba1a, $attempt);
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submitted_actions($timenow, false, [1 => ['answer' => '3.14']]);
        $attemptobj->process_finish($timenow, false);

        // User 2 does not finish quiz.
        $attempt = quiz_create_attempt($quizobj1b, 1, false, $timenow, false, $u2->id);
        quiz_start_new_attempt($quizobj1b, $quba1b, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj1b, $quba1b, $attempt);

        // Check all users.
        $users = get_users_in_report::execute($quiz->cmid, 'overview',
            'quiz_overview_table', 'quiz_overview_options', 'enrolled_with', '', false)['users'];
        $this->assertCount(2, $users);

        // Get only attempt has the state is finished.
        $users = get_users_in_report::execute($quiz->cmid, 'overview',
            'quiz_overview_table', 'quiz_overview_options', 'enrolled_with', 'finished', false)['users'];
        $this->assertCount(1, $users);
    }
}
