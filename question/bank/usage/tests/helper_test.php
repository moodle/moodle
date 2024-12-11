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

defined('MOODLE_INTERNAL') || die();

use mod_quiz\quiz_attempt;

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

/**
 * Helper test.
 *
 * @package    qbank_usage
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \qbank_usage\helper
 */
final class helper_test extends \advanced_testcase {

    use \quiz_question_helper_test_trait;

    /**
     * @var \stdClass $quiz
     */
    protected $quiz;

    /**
     * @var \stdClass $user
     */
    protected $user;

    /**
     * @var \core_question_generator $questiongenerator
     */
    protected $questiongenerator;

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
        $this->user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $this->quiz = $quizgenerator->create_instance(['course' => $course->id,
                'grade' => 100.0, 'sumgrades' => 2, 'layout' => $layout]);

        $this->questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $this->questiongenerator->create_question_category();

        $page = 1;
        foreach (explode(',', $layout) as $slot) {
            if ($slot == 0) {
                $page += 1;
                continue;
            }

            $question = $this->questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
            quiz_add_quiz_question($question->id, $this->quiz, $page);
            $this->questions [] = $question;
        }
    }

    /**
     * Record a quiz attempt.
     *
     * @return void
     */
    protected function attempt_quiz(): void {
        $quizobj = \mod_quiz\quiz_settings::create($this->quiz->id, $this->user->id);

        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $this->user->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);
        quiz_attempt::create($attempt->id);
    }

    /**
     * Test question attempt count.
     *
     * @covers ::get_question_attempts_count_in_quiz
     */
    public function test_get_question_attempts_count_in_quiz(): void {
        $this->attempt_quiz();
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
    public function test_get_question_entry_usage_count(): void {
        foreach ($this->questions as $question) {
            $count = helper::get_question_entry_usage_count(\question_bank::load_question($question->id));
            // Test that the attempt data matches the usage data for the count.
            $this->assertEquals(1, $count);
        }
    }

    /**
     * If a question has been included via a random question attempt, this should be counted as a usage.
     *
     * @covers ::get_question_entry_usage_count
     * @return void
     */
    public function test_get_random_question_attempts_usage_count(): void {
        $this->setAdminUser();
        $cat = $this->questiongenerator->create_question_category();
        $question = $this->questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        $this->add_random_questions($this->quiz->id, 1, $cat->id, 1);

        $qdef = \question_bank::load_question($question->id);
        $count = helper::get_question_entry_usage_count($qdef);
        $this->assertEquals(0, $count);

        $this->attempt_quiz();

        $count = helper::get_question_entry_usage_count($qdef);
        $this->assertEquals(1, $count);
    }

    /**
     * When a question referenced directly is edited, the usage count of all versions remains the same.
     *
     * When checking usage of separate versions, the new version should show usages but the original version should not.
     *
     * @covers ::get_question_entry_usage_count
     * @return void
     */
    public function test_edited_question_usage_counts(): void {
        foreach ($this->questions as $question) {
            $qdef = \question_bank::load_question($question->id);
            $count1 = helper::get_question_entry_usage_count($qdef);
            // Each question should have 1 usage.
            $this->assertEquals(1, $count1);

            $newversion = $this->questiongenerator->update_question($question);
            $newqdef = \question_bank::load_question($newversion->id);

            // Either version should return the same count if not checking a specific version.
            $count2 = helper::get_question_entry_usage_count($qdef);
            $this->assertEquals(1, $count2);
            $count3 = helper::get_question_entry_usage_count($newqdef);
            $this->assertEquals(1, $count3);
            // Checking the specific version count should return the counts for each version.
            // The original version is no longer included in the quiz, so has 0 usages.
            $count4 = helper::get_question_entry_usage_count($qdef, true);
            $this->assertEquals(0, $count4);
            // The new version is now included in the quiz, so has 1 usage.
            $count5 = helper::get_question_entry_usage_count($newqdef, true);
            $this->assertEquals(1, $count5);
        }
    }

    /**
     * When a question referenced directly with attempts is edited, the usage count of all versions remains the same.
     *
     * When checking usage of separate versions, both versions should show usage.
     *
     * @covers ::get_question_entry_usage_count
     * @return void
     */
    public function test_edited_attempted_question_usage_counts(): void {
        $this->attempt_quiz();

        foreach ($this->questions as $question) {
            $qdef = \question_bank::load_question($question->id);
            $count1 = helper::get_question_entry_usage_count($qdef);
            // Each question should have 1 usage.
            $this->assertEquals(1, $count1);

            $newversion = $this->questiongenerator->update_question($question);
            $newqdef = \question_bank::load_question($newversion->id);

            // Either version should return the same count if not checking a specific version.
            $count2 = helper::get_question_entry_usage_count($qdef);
            $this->assertEquals(1, $count2);
            $count3 = helper::get_question_entry_usage_count($newqdef);
            $this->assertEquals(1, $count3);
            // Checking the specific version count should return the counts for each version.
            // The original version is no longer included in the quiz. However, the is still an attempt using this question version,
            // so it has 1 usage.
            $count4 = helper::get_question_entry_usage_count($qdef, true);
            $this->assertEquals(1, $count4);
            // The new version is now included in the quiz, so has 1 usage.
            $count5 = helper::get_question_entry_usage_count($newqdef, true);
            $this->assertEquals(1, $count5);
        }
    }

    /**
     * When a random question with attempts is edited, it should still have the same usage count.
     *
     * When checking usage of separate versions, the original version should still show usage but the new version should not.
     *
     * @covers ::get_question_entry_usage_count
     * @return void
     */
    public function test_edited_attempted_random_question_usage_count(): void {
        $this->setAdminUser();
        $cat = $this->questiongenerator->create_question_category();
        $question = $this->questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        $this->add_random_questions($this->quiz->id, 1, $cat->id, 1);

        $this->attempt_quiz();

        $qdef = \question_bank::load_question($question->id);
        $count1 = helper::get_question_entry_usage_count($qdef);
        $this->assertEquals(1, $count1);

        $newversion = $this->questiongenerator->update_question($question);
        $newqdef = \question_bank::load_question($newversion->id);

        // Either version should return the same count if not checking a specific version.
        $count2 = helper::get_question_entry_usage_count($qdef);
        $this->assertEquals(1, $count2);
        $count3 = helper::get_question_entry_usage_count($newqdef);
        $this->assertEquals(1, $count3);
        // Checking the specific version count should return the counts for each version.
        // There is still an attempt of the original version has part of the random question attempt, so it has 1 usage.
        $count4 = helper::get_question_entry_usage_count($qdef, true);
        $this->assertEquals(1, $count4);
        // There is no attempt of the new version, so it has 0 usages.
        $count5 = helper::get_question_entry_usage_count($newqdef, true);
        $this->assertEquals(0, $count5);
    }
}
