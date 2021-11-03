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

namespace qbank_statistics;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
use quiz;
use question_engine;
use quiz_attempt;
/**
 * Tests for question statistics.
 *
 * @package    qbank_statistics
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper_test extends \advanced_testcase {

    /**
     * Test quizzes that contain a specified question.
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function test_get_quizziess(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create quizzes.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz1 = $quizgenerator->create_instance([
            'course' => $course->id,
            'grade' => 100.0, 'sumgrades' => 2,
            'layout' => '1,2,0'
        ]);
        $quiz2 = $quizgenerator->create_instance([
            'course' => $course->id,
            'grade' => 100.0, 'sumgrades' => 2,
            'layout' => '1,2,0'
        ]);
        $quiz3 = $quizgenerator->create_instance([
            'course' => $course->id,
            'grade' => 100.0, 'sumgrades' => 2,
            'layout' => '1,2,0'
        ]);
        $this->assertEquals(3, $DB->count_records('quiz'));

        // Create questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $question1 = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        $question2 = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));

        // Add question 1 to quiz 1, 2.
        quiz_add_quiz_question($question1->id, $quiz1);
        quiz_add_quiz_question($question1->id, $quiz2);
        // Quiz 1 attempt.
        $attempt = ['answer' => 'frog', 'answer' => 10];
        $this->submit_quiz($quiz1, $attempt);

        // Add question 2 to quiz 2.
        quiz_add_quiz_question($question2->id, $quiz2);
        $this->submit_quiz($quiz2, $attempt);

        // Checking quizzes that use question 1.
        $question1quizzes = helper::get_quizzes($question1->id);
        $this->assertCount(2, $question1quizzes);
        $this->assertContains($quiz1->id, $question1quizzes);
        $this->assertContains($quiz2->id, $question1quizzes);

        // Checking quizzes that contain question 2.
        $question2quizzes = helper::get_quizzes($question2->id);
        $this->assertCount(1, $question2quizzes);
        $this->assertContains($quiz2->id, $question2quizzes);

        // Add random question to quiz3.
        quiz_add_random_questions($quiz3, 0, $cat->id, 1, false);
        $this->submit_quiz($quiz3, $attempt);
        // Quiz 3 will be in one of these arrays.
        $question1quizzes = helper::get_quizzes($question1->id);
        $question2quizzes = helper::get_quizzes($question2->id);
        $this->assertContains($quiz3->id, array_merge($question1quizzes, $question2quizzes));
    }

    /**
     * Load facility for a question
     *
     * @param object $quiz quiz object
     * @param int $questionid question id
     * @return float|int
     */
    private function load_question_facility(object $quiz, int $questionid): ?float {
        return helper::load_question_stats_item($quiz->id, $questionid, 'facility');
    }

    /**
     * Load discriminative efficiency for a question
     *
     * @param object $quiz quiz object
     * @param int $questionid question id
     * @return float|int
     */
    private function load_question_discriminative_efficiency(object $quiz, int $questionid): ?float {
        return helper::load_question_stats_item($quiz->id, $questionid, 'discriminativeefficiency');
    }

    /**
     * Load discrimination index for a question
     *
     * @param object $quiz quiz object
     * @param int $questionid question id
     * @return float|int
     */
    private function load_question_discrimination_index(object $quiz, int $questionid): ?float {
        return helper::load_question_stats_item($quiz->id, $questionid, 'discriminationindex');
    }

    /**
     * Create 2 quizzes.
     *
     * @return array return 2 quizzes
     * @throws \coding_exception
     */
    private function prepare_quizzes(): array {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Make 2 quizzes.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $layout = '1,2,0,3,4,0';
        $quiz1 = $quizgenerator->create_instance([
            'course' => $course->id,
            'grade' => 100.0, 'sumgrades' => 2,
            'layout' => $layout
        ]);

        $quiz2 = $quizgenerator->create_instance([
            'course' => $course->id,
            'grade' => 100.0, 'sumgrades' => 2,
            'layout' => $layout
        ]);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();

        $page = 1;
        $questions = [];
        foreach (explode(',', $layout) as $slot) {
            if ($slot == 0) {
                $page += 1;
                continue;
            }

            $question = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
            $questions[$slot] = $question;
            quiz_add_quiz_question($question->id, $quiz1, $page);
            quiz_add_quiz_question($question->id, $quiz2, $page);
        }

        return [$quiz1, $quiz2, $questions];
    }

    /**
     * Submit quiz answers
     *
     * @param object $quiz
     * @param array $answers
     * @throws \moodle_exception
     */
    private function submit_quiz(object $quiz, array $answers): void {
        // Create user.
        $user = $this->getDataGenerator()->create_user();
        // Create attempt.
        $quizobj = quiz::create($quiz->id, $user->id);
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $user->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);
        // Submit attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submitted_actions($timenow, false, $answers);
        $attemptobj->process_finish($timenow, false);
    }

    /**
     * Generate attempt answers.
     *
     * @param array $correctanswerflags array of 1 or 0
     * 1 : generate correct answer
     * 0 : generate wrong answer
     *
     * @return array
     */
    private function generate_attempt_answers(array $correctanswerflags): array {
        $attempt = [];
        for ($i = 1; $i <= 4; $i++) {
            if (isset($correctanswerflags) && $correctanswerflags[$i - 1] == 1) {
                // Correct answer.
                $attempt[$i] = ['answer' => 'frog'];
            } else {
                $attempt[$i] = ['answer' => 'false'];
            }
        }
        return $attempt;
    }

    /**
     *
     * Generate quizzes and submit answers.
     *
     * @param array $quiz1attempts quiz 1 attempts
     * @param array $quiz2attempts quiz 2 attempts
     *
     * @return array
     */
    private function prepare_and_submit_quizzes(array $quiz1attempts, array $quiz2attempts): array {
        list($quiz1, $quiz2, $questions) = $this->prepare_quizzes();
        // Submit attempts of quiz1.
        foreach ($quiz1attempts as $attempt) {
            $this->submit_quiz($quiz1, $attempt);
        }
        // Submit attempts of quiz2.
        foreach ($quiz2attempts as $attempt) {
            $this->submit_quiz($quiz2, $attempt);
        }
        return [$quiz1, $quiz2, $questions];
    }

    /**
     * Data provider for {@see test_load_question_facility()}.
     *
     * @return \Generator
     */
    public function load_question_facility_provider(): \Generator {
        yield 'Facility case 1' => [
            'Quiz 1 attempts' => [
                $this->generate_attempt_answers([1, 0, 0, 0]),
            ],
            'Expected quiz 1 facilities' => ['100.00%', '0.00%', '0.00%', '0.00%'],
            'Quiz 2 attempts' => [
                $this->generate_attempt_answers([1, 0, 0, 0]),
                $this->generate_attempt_answers([1, 1, 0, 0]),
            ],
            'Expected quiz 2 facilities' => ['100.00%', '50.00%', '0.00%', '0.00%'],
            'Expected average facilities' => ['100.00%', '25.00%', '0.00%', '0.00%'],
        ];
        yield 'Facility case 2' => [
            'Quiz 1 attempts' => [
                $this->generate_attempt_answers([1, 0, 0, 0]),
                $this->generate_attempt_answers([1, 1, 0, 0]),
                $this->generate_attempt_answers([1, 1, 1, 0]),
            ],
            'Expected quiz 1 facilities' => ['100.00%', '66.67%', '33.33%', '0.00%'],
            'Quiz 2 attempts' => [
                $this->generate_attempt_answers([1, 0, 0, 0]),
                $this->generate_attempt_answers([1, 1, 0, 0]),
                $this->generate_attempt_answers([1, 1, 1, 0]),
                $this->generate_attempt_answers([1, 1, 1, 1]),
            ],
            'Expected quiz 2 facilities' => ['100.00%', '75.00%', '50.00%', '25.00%'],
            'Expected average facilities' => ['100.00%', '70.83%', '41.67%', '12.50%'],
        ];
    }

    /**
     * Test question facility
     *
     * @dataProvider load_question_facility_provider
     *
     * @param array $quiz1attempts quiz 1 attempts
     * @param array $expectedquiz1facilities expected quiz 1 facilities
     * @param array $quiz2attempts quiz 2 attempts
     * @param array $expectedquiz2facilities  expected quiz 2 facilities
     * @param array $expectedaveragefacilities expected average facilities
     */
    public function test_load_question_facility(
        array $quiz1attempts,
        array $expectedquiz1facilities,
        array $quiz2attempts,
        array $expectedquiz2facilities,
        array $expectedaveragefacilities)
    : void {
        $this->resetAfterTest();

        list($quiz1, $quiz2, $questions) = $this->prepare_and_submit_quizzes($quiz1attempts, $quiz2attempts);

        // Quiz 1 facilities.
        $quiz1facility1 = $this->load_question_facility($quiz1, $questions[1]->id);
        $quiz1facility2 = $this->load_question_facility($quiz1, $questions[2]->id);
        $quiz1facility3 = $this->load_question_facility($quiz1, $questions[3]->id);
        $quiz1facility4 = $this->load_question_facility($quiz1, $questions[4]->id);

        $this->assertEquals($expectedquiz1facilities[0], helper::format_percentage($quiz1facility1));
        $this->assertEquals($expectedquiz1facilities[1], helper::format_percentage($quiz1facility2));
        $this->assertEquals($expectedquiz1facilities[2], helper::format_percentage($quiz1facility3));
        $this->assertEquals($expectedquiz1facilities[3], helper::format_percentage($quiz1facility4));

        // Quiz 2 facilities.
        $quiz2facility1 = $this->load_question_facility($quiz2, $questions[1]->id);
        $quiz2facility2 = $this->load_question_facility($quiz2, $questions[2]->id);
        $quiz2facility3 = $this->load_question_facility($quiz2, $questions[3]->id);
        $quiz2facility4 = $this->load_question_facility($quiz2, $questions[4]->id);

        $this->assertEquals($expectedquiz2facilities[0], helper::format_percentage($quiz2facility1));
        $this->assertEquals($expectedquiz2facilities[1], helper::format_percentage($quiz2facility2));
        $this->assertEquals($expectedquiz2facilities[2], helper::format_percentage($quiz2facility3));
        $this->assertEquals($expectedquiz2facilities[3], helper::format_percentage($quiz2facility4));

        // Average question facilities.
        $averagefacility1 = helper::calculate_average_question_facility($questions[1]->id);
        $averagefacility2 = helper::calculate_average_question_facility($questions[2]->id);
        $averagefacility3 = helper::calculate_average_question_facility($questions[3]->id);
        $averagefacility4 = helper::calculate_average_question_facility($questions[4]->id);

        $this->assertEquals($expectedaveragefacilities[0], helper::format_percentage($averagefacility1));
        $this->assertEquals($expectedaveragefacilities[1], helper::format_percentage($averagefacility2));
        $this->assertEquals($expectedaveragefacilities[2], helper::format_percentage($averagefacility3));
        $this->assertEquals($expectedaveragefacilities[3], helper::format_percentage($averagefacility4));
    }

    /**
     * Data provider for {@see test_load_question_discriminative_efficiency()}.
     * @return \Generator
     */
    public function load_question_discriminative_efficiency_provider() {
        yield 'Discriminative efficiency' => [
            'Quiz 1 attempts' => [
                $this->generate_attempt_answers([1, 0, 0, 0]),
                $this->generate_attempt_answers([1, 1, 0, 0]),
                $this->generate_attempt_answers([1, 0, 1, 0]),
                $this->generate_attempt_answers([1, 1, 1, 1]),
            ],
            'Expected quiz 1 discriminative efficiency' => ['N/A', '33.33%', '33.33%', '100.00%'],
            'Quiz 2 attempts' => [
                $this->generate_attempt_answers([1, 1, 1, 1]),
                $this->generate_attempt_answers([0, 0, 0, 0]),
                $this->generate_attempt_answers([1, 0, 0, 1]),
                $this->generate_attempt_answers([0, 1, 1, 0]),
            ],
            'Expected quiz 2 discriminative efficiency' => ['50.00%', '50.00%', '50.00%', '50.00%'],
            'Expected average discriminative efficiency' => ['50.00%', '41.67%', '41.67%', '75.00%'],
        ];
    }

    /**
     * Test discriminative efficiency
     *
     * @dataProvider load_question_discriminative_efficiency_provider
     *
     * @param array $quiz1attempts quiz 1 attempts
     * @param array $expectedquiz1discriminativeefficiency expected quiz 1 discriminative efficiency
     * @param array $quiz2attempts quiz 2 attempts
     * @param array $expectedquiz2discriminativeefficiency expected quiz 2 discriminative efficiency
     * @param array $expectedaveragediscriminativeefficiency expected average discriminative efficiency
     */
    public function test_load_question_discriminative_efficiency(
        array $quiz1attempts,
        array $expectedquiz1discriminativeefficiency,
        array $quiz2attempts,
        array $expectedquiz2discriminativeefficiency,
        array $expectedaveragediscriminativeefficiency
    ): void {
        $this->resetAfterTest();

        list($quiz1, $quiz2, $questions) = $this->prepare_and_submit_quizzes($quiz1attempts, $quiz2attempts);

        // Quiz 1 discriminative efficiency.
        $discriminativeefficiency1 = $this->load_question_discriminative_efficiency($quiz1, $questions[1]->id);
        $discriminativeefficiency2 = $this->load_question_discriminative_efficiency($quiz1, $questions[2]->id);
        $discriminativeefficiency3 = $this->load_question_discriminative_efficiency($quiz1, $questions[3]->id);
        $discriminativeefficiency4 = $this->load_question_discriminative_efficiency($quiz1, $questions[4]->id);

        $this->assertEquals($expectedquiz1discriminativeefficiency[0],
            helper::format_percentage($discriminativeefficiency1, false),
            "Failure in quiz 1 - question 1 discriminative efficiency");
        $this->assertEquals($expectedquiz1discriminativeefficiency[1],
            helper::format_percentage($discriminativeefficiency2, false),
            "Failure in quiz 1 - question 2 discriminative efficiency");
        $this->assertEquals($expectedquiz1discriminativeefficiency[2],
            helper::format_percentage($discriminativeefficiency3, false),
            "Failure in quiz 1 - question 3 discriminative efficiency");
        $this->assertEquals($expectedquiz1discriminativeefficiency[3],
            helper::format_percentage($discriminativeefficiency4, false),
            "Failure in quiz 1 - question 4 discriminative efficiency");

        // Quiz 2 discriminative efficiency.
        $discriminativeefficiency1 = $this->load_question_discriminative_efficiency($quiz2, $questions[1]->id);
        $discriminativeefficiency2 = $this->load_question_discriminative_efficiency($quiz2, $questions[2]->id);
        $discriminativeefficiency3 = $this->load_question_discriminative_efficiency($quiz2, $questions[3]->id);
        $discriminativeefficiency4 = $this->load_question_discriminative_efficiency($quiz2, $questions[4]->id);

        $this->assertEquals($expectedquiz2discriminativeefficiency[0],
            helper::format_percentage($discriminativeefficiency1, false),
            "Failure in quiz 2 - question 1 discriminative efficiency");
        $this->assertEquals($expectedquiz2discriminativeefficiency[1],
            helper::format_percentage($discriminativeefficiency2, false),
            "Failure in quiz 2 - question 2 discriminative efficiency");
        $this->assertEquals($expectedquiz2discriminativeefficiency[2],
            helper::format_percentage($discriminativeefficiency3, false),
            "Failure in quiz 2 - question 3 discriminative efficiency");
        $this->assertEquals($expectedquiz2discriminativeefficiency[3],
            helper::format_percentage($discriminativeefficiency4, false),
            "Failure in quiz 2 - question 4 discriminative efficiency");

        // Average question discriminative efficiency.
        $avgdiscriminativeefficiency1 = helper::calculate_average_question_discriminative_efficiency($questions[1]->id);
        $avgdiscriminativeefficiency2 = helper::calculate_average_question_discriminative_efficiency($questions[2]->id);
        $avgdiscriminativeefficiency3 = helper::calculate_average_question_discriminative_efficiency($questions[3]->id);
        $avgdiscriminativeefficiency4 = helper::calculate_average_question_discriminative_efficiency($questions[4]->id);

        $this->assertEquals($expectedaveragediscriminativeefficiency[0],
            helper::format_percentage($avgdiscriminativeefficiency1, false),
            "Failure in question 1 average discriminative efficiency");
        $this->assertEquals($expectedaveragediscriminativeefficiency[1],
            helper::format_percentage($avgdiscriminativeefficiency2, false),
            "Failure in question 2 average discriminative efficiency");
        $this->assertEquals($expectedaveragediscriminativeefficiency[2],
            helper::format_percentage($avgdiscriminativeefficiency3, false),
            "Failure in question 3 average discriminative efficiency");
        $this->assertEquals($expectedaveragediscriminativeefficiency[3],
            helper::format_percentage($avgdiscriminativeefficiency4, false),
            "Failure in question 4 average discriminative efficiency");
    }

    /**
     * Data provider for {@see test_load_question_discrimination_index()}.
     * @return \Generator
     */
    public function load_question_discrimination_index_provider() {
        yield 'Discrimination Index' => [
            'Quiz 1 attempts' => [
                $this->generate_attempt_answers([1, 0, 0, 0]),
                $this->generate_attempt_answers([1, 1, 0, 0]),
                $this->generate_attempt_answers([1, 0, 1, 0]),
                $this->generate_attempt_answers([1, 1, 1, 1]),
            ],
            'Expected quiz 1 Discrimination Index' => ['N/A', '30.15%', '30.15%', '81.65%'],
            'Quiz 2 attempts' => [
                $this->generate_attempt_answers([1, 1, 1, 1]),
                $this->generate_attempt_answers([0, 0, 0, 0]),
                $this->generate_attempt_answers([1, 0, 0, 1]),
                $this->generate_attempt_answers([0, 1, 1, 0]),
            ],
            'Expected quiz 2 discrimination Index' => ['44.72%', '44.72%', '44.72%', '44.72%'],
            'Expected average discrimination Index' => ['44.72%', '37.44%', '37.44%', '63.19%'],
        ];
    }

    /**
     * Test discrimination index
     *
     * @dataProvider load_question_discrimination_index_provider
     *
     * @param array $quiz1attempts quiz 1 attempts
     * @param array $expectedquiz1discriminationindex expected quiz 1 discrimination index
     * @param array $quiz2attempts quiz 2 attempts
     * @param array $expectedquiz2discriminationindex expected quiz 2 discrimination index
     * @param array $expectedaveragediscriminationindex expected average discrimination index
     */
    public function test_load_question_discrimination_index(
        array $quiz1attempts,
        array $expectedquiz1discriminationindex,
        array $quiz2attempts,
        array $expectedquiz2discriminationindex,
        array $expectedaveragediscriminationindex
    ): void {
        $this->resetAfterTest();

        list($quiz1, $quiz2, $questions) = $this->prepare_and_submit_quizzes($quiz1attempts, $quiz2attempts);

        // Quiz 1 discrimination index.
        $discriminationindex1 = $this->load_question_discrimination_index($quiz1, $questions[1]->id);
        $discriminationindex2 = $this->load_question_discrimination_index($quiz1, $questions[2]->id);
        $discriminationindex3 = $this->load_question_discrimination_index($quiz1, $questions[3]->id);
        $discriminationindex4 = $this->load_question_discrimination_index($quiz1, $questions[4]->id);

        $this->assertEquals($expectedquiz1discriminationindex[0],
            helper::format_percentage($discriminationindex1, false),
            "Failure in quiz 1 - question 1 discrimination index");
        $this->assertEquals($expectedquiz1discriminationindex[1],
            helper::format_percentage($discriminationindex2, false),
            "Failure in quiz 1 - question 2 discrimination index");
        $this->assertEquals($expectedquiz1discriminationindex[2],
            helper::format_percentage($discriminationindex3, false),
            "Failure in quiz 1 - question 3 discrimination index");
        $this->assertEquals($expectedquiz1discriminationindex[3],
            helper::format_percentage($discriminationindex4, false),
            "Failure in quiz 1 - question 4 discrimination index");

        // Quiz 2 discrimination index.
        $discriminationindex1 = $this->load_question_discrimination_index($quiz2, $questions[1]->id);
        $discriminationindex2 = $this->load_question_discrimination_index($quiz2, $questions[2]->id);
        $discriminationindex3 = $this->load_question_discrimination_index($quiz2, $questions[3]->id);
        $discriminationindex4 = $this->load_question_discrimination_index($quiz2, $questions[4]->id);

        $this->assertEquals($expectedquiz2discriminationindex[0],
            helper::format_percentage($discriminationindex1, false),
            "Failure in quiz 2 - question 1 discrimination index");
        $this->assertEquals($expectedquiz2discriminationindex[1],
            helper::format_percentage($discriminationindex2, false),
            "Failure in quiz 2 - question 2 discrimination index");
        $this->assertEquals($expectedquiz2discriminationindex[2],
            helper::format_percentage($discriminationindex3, false),
            "Failure in quiz 2 - question 3 discrimination index");
        $this->assertEquals($expectedquiz2discriminationindex[3],
            helper::format_percentage($discriminationindex4, false),
            "Failure in quiz 2 - question 4 discrimination index");

        // Average question discrimination index.
        $avgdiscriminationindex1 = helper::calculate_average_question_discrimination_index($questions[1]->id);
        $avgdiscriminationindex2 = helper::calculate_average_question_discrimination_index($questions[2]->id);
        $avgdiscriminationindex3 = helper::calculate_average_question_discrimination_index($questions[3]->id);
        $avgdiscriminationindex4 = helper::calculate_average_question_discrimination_index($questions[4]->id);

        $this->assertEquals($expectedaveragediscriminationindex[0],
            helper::format_percentage($avgdiscriminationindex1, false),
            "Failure in question 1 average discrimination index");
        $this->assertEquals($expectedaveragediscriminationindex[1],
            helper::format_percentage($avgdiscriminationindex2, false),
            "Failure in question 2 average discrimination index");
        $this->assertEquals($expectedaveragediscriminationindex[2],
            helper::format_percentage($avgdiscriminationindex3, false),
            "Failure in question 3 average discrimination index");
        $this->assertEquals($expectedaveragediscriminationindex[3],
            helper::format_percentage($avgdiscriminationindex4, false),
            "Failure in question 4 average discrimination index");
    }
}
