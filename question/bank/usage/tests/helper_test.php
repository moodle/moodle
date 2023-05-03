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

namespace qbank_usage;

use mod_quiz\quiz_attempt;

/**
 * Helper test.
 *
 * @package    qbank_usage
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \qbank_usage\helper
 */
class helper_test extends \advanced_testcase {

    /**
     * @var \stdClass $quiz
     */
    protected $quiz;

    /**
     * @var array $questions
     */
    protected $questions = [];

    /**
     * Test setup.
     */
    public function setup(): void {
        $this->resetAfterTest();
        $layout = '1,2,0';
        // Make a user to do the quiz.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $this->quiz = $quizgenerator->create_instance(['course' => $course->id,
            'grade' => 100.0, 'sumgrades' => 2, 'layout' => $layout]);

        $quizobj = \mod_quiz\quiz_settings::create($this->quiz->id, $user->id);

        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();

        $page = 1;
        foreach (explode(',', $layout) as $slot) {
            if ($slot == 0) {
                $page += 1;
                continue;
            }

            $question = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
            quiz_add_quiz_question($question->id, $this->quiz, $page);
            $this->questions [] = $question;
        }

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $user->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);
        quiz_attempt::create($attempt->id);
    }

    /**
     * Test question attempt count.
     *
     * @covers ::get_question_attempts_count_in_quiz
     */
    public function test_get_question_attempts_count_in_quiz() {
        foreach ($this->questions as $question) {
            $questionattemptcount = helper::get_question_attempts_count_in_quiz($question->id, $this->quiz->id);
            // Test the attempt count matches the usage count, each question should have one count.
            $this->assertEquals(1, $questionattemptcount);
        }
    }

    /**
     * Test test usage data.
     *
     * @covers ::get_question_entry_usage_count
     */
    public function test_get_question_entry_usage_count() {
        foreach ($this->questions as $question) {
            $count = helper::get_question_entry_usage_count(\question_bank::load_question($question->id));
            // Test that the attempt data matches the usage data for the count.
            $this->assertEquals(1, $count);
        }
    }
}
