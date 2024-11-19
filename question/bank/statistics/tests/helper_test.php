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

use core_question\statistics\questions\all_calculated_for_qubaid_condition;
use quiz_statistics\tests\statistics_helper;
use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;
use question_engine;

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

/**
 * Tests for question statistics.
 *
 * @package    qbank_statistics
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper_test extends \advanced_testcase {

    use \quiz_question_helper_test_trait;

    /**
     * Test quizzes that contain a specified question.
     *
     * @covers ::get_all_places_where_questions_were_attempted
     * @deprecated since Moodle 4.3 please use the method from statistics_bulk_loader.
     * @todo MDL-78090 Final deprecation in Moodle 4.7
     */
    public function test_get_all_places_where_questions_were_attempted(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $rcm = new \ReflectionMethod(helper::class, 'get_all_places_where_questions_were_attempted');

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create three quizzes.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz1 = $quizgenerator->create_instance([
            'course' => $course->id,
            'grade' => 100.0, 'sumgrades' => 2,
            'layout' => '1,2,0'
        ]);
        $quiz1context = \context_module::instance($quiz1->cmid);

        $quiz2 = $quizgenerator->create_instance([
            'course' => $course->id,
            'grade' => 100.0, 'sumgrades' => 2,
            'layout' => '1,2,0'
        ]);
        $quiz2context = \context_module::instance($quiz2->cmid);

        $quiz3 = $quizgenerator->create_instance([
            'course' => $course->id,
            'grade' => 100.0, 'sumgrades' => 2,
            'layout' => '1,2,0'
        ]);
        $quiz3context = \context_module::instance($quiz3->cmid);

        // Create questions.
        /** @var \core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $question1 = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        $question2 = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));

        // Add question 1 to quiz 1 and make an attempt.
        quiz_add_quiz_question($question1->id, $quiz1);
        // Quiz 1 attempt.
        $this->submit_quiz($quiz1, [1 => ['answer' => 'frog']]);

        // Add questions 1 and 2 to quiz 2.
        quiz_add_quiz_question($question1->id, $quiz2);
        quiz_add_quiz_question($question2->id, $quiz2);
        $this->submit_quiz($quiz2, [1 => ['answer' => 'frog'], 2 => ['answer' => 10]]);

        // Checking quizzes that use question 1.
        $q1places = $rcm->invoke(null, [$question1->id]);
        $this->assertCount(2, $q1places);
        $this->assertEquals((object) ['component' => 'mod_quiz', 'contextid' => $quiz1context->id], $q1places[0]);
        $this->assertEquals((object) ['component' => 'mod_quiz', 'contextid' => $quiz2context->id], $q1places[1]);

        // Checking quizzes that contain question 2.
        $q2places = $rcm->invoke(null, [$question2->id]);
        $this->assertCount(1, $q2places);
        $this->assertEquals((object) ['component' => 'mod_quiz', 'contextid' => $quiz2context->id], $q2places[0]);

        // Add a random question to quiz3.
        $this->add_random_questions($quiz3->id, 0, $cat->id, 1);
        $this->submit_quiz($quiz3, [1 => ['answer' => 'willbewrong']]);

        // Quiz 3 will now be in one of these arrays.
        $q1places = $rcm->invoke(null, [$question1->id]);
        $q2places = $rcm->invoke(null, [$question2->id]);
        if (count($q1places) == 3) {
            $newplace = end($q1places);
        } else {
            $newplace = end($q2places);
        }
        $this->assertEquals((object) ['component' => 'mod_quiz', 'contextid' => $quiz3context->id], $newplace);
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

        /** @var \core_question_generator $questiongenerator */
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
        $quizobj = quiz_settings::create($quiz->id, $user->id);
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, null, $timenow, false, $user->id);
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
    private static function generate_attempt_answers(array $correctanswerflags): array {
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

        // Calculate the statistics.
        $this->expectOutputRegex('~.*Calculations completed.*~');
        statistics_helper::run_pending_recalculation_tasks();

        return [$quiz1, $quiz2, $questions];
    }

    /**
     * To use private helper::extract_item_value function.
     *
     * @param all_calculated_for_qubaid_condition $statistics the batch of statistics.
     * @param int $questionid a question id.
     * @param string $item one of the field names in all_calculated_for_qubaid_condition, e.g. 'facility'.
     * @return float|null the required value.
     */
    private function extract_item_value(all_calculated_for_qubaid_condition $statistics,
                                        int $questionid, string $item): ?float {
        $rcm = new \ReflectionMethod(helper::class, 'extract_item_value');
        return $rcm->invoke(null, $statistics, $questionid, $item);
    }

    /**
     * To use private helper::load_statistics_for_place function (with mod_quiz component).
     *
     * @param \context $context the context to load the statistics for.
     * @return all_calculated_for_qubaid_condition|null question statistics.
     */
    private function load_quiz_statistics_for_place(\context $context): ?all_calculated_for_qubaid_condition {
        $rcm = new \ReflectionMethod(helper::class, 'load_statistics_for_place');
        return $rcm->invoke(null, 'mod_quiz', $context);
    }

    /**
     * Data provider for {@see test_load_question_facility()}.
     *
     * @return \Generator
     */
    public static function load_question_facility_provider(): \Generator {
        yield 'Facility case 1' => [
            'Quiz 1 attempts' => [
                self::generate_attempt_answers([1, 0, 0, 0]),
            ],
            'Expected quiz 1 facilities' => ['100.00%', '0.00%', '0.00%', '0.00%'],
            'Quiz 2 attempts' => [
                self::generate_attempt_answers([1, 0, 0, 0]),
                self::generate_attempt_answers([1, 1, 0, 0]),
            ],
            'Expected quiz 2 facilities' => ['100.00%', '50.00%', '0.00%', '0.00%'],
            'Expected average facilities' => ['100.00%', '25.00%', '0.00%', '0.00%'],
        ];
        yield 'Facility case 2' => [
            'Quiz 1 attempts' => [
                self::generate_attempt_answers([1, 0, 0, 0]),
                self::generate_attempt_answers([1, 1, 0, 0]),
                self::generate_attempt_answers([1, 1, 1, 0]),
            ],
            'Expected quiz 1 facilities' => ['100.00%', '66.67%', '33.33%', '0.00%'],
            'Quiz 2 attempts' => [
                self::generate_attempt_answers([1, 0, 0, 0]),
                self::generate_attempt_answers([1, 1, 0, 0]),
                self::generate_attempt_answers([1, 1, 1, 0]),
                self::generate_attempt_answers([1, 1, 1, 1]),
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
     * @deprecated since Moodle 4.3 please use the method from statistics_bulk_loader.
     * @todo MDL-78090 Final deprecation in Moodle 4.7
     */
    public function test_load_question_facility(
        array $quiz1attempts,
        array $expectedquiz1facilities,
        array $quiz2attempts,
        array $expectedquiz2facilities,
        array $expectedaveragefacilities
    ): void {
        $this->resetAfterTest();

        list($quiz1, $quiz2, $questions) = $this->prepare_and_submit_quizzes($quiz1attempts, $quiz2attempts);

        // Quiz 1 facilities.
        $stats = $this->load_quiz_statistics_for_place(\context_module::instance($quiz1->cmid));
        $quiz1facility1 = $this->extract_item_value($stats, $questions[1]->id, 'facility');
        $quiz1facility2 = $this->extract_item_value($stats, $questions[2]->id, 'facility');
        $quiz1facility3 = $this->extract_item_value($stats, $questions[3]->id, 'facility');
        $quiz1facility4 = $this->extract_item_value($stats, $questions[4]->id, 'facility');

        $this->assertEquals($expectedquiz1facilities[0], helper::format_percentage($quiz1facility1));
        $this->assertEquals($expectedquiz1facilities[1], helper::format_percentage($quiz1facility2));
        $this->assertEquals($expectedquiz1facilities[2], helper::format_percentage($quiz1facility3));
        $this->assertEquals($expectedquiz1facilities[3], helper::format_percentage($quiz1facility4));

        // Quiz 2 facilities.
        $stats = $this->load_quiz_statistics_for_place(\context_module::instance($quiz2->cmid));
        $quiz2facility1 = $this->extract_item_value($stats, $questions[1]->id, 'facility');
        $quiz2facility2 = $this->extract_item_value($stats, $questions[2]->id, 'facility');
        $quiz2facility3 = $this->extract_item_value($stats, $questions[3]->id, 'facility');
        $quiz2facility4 = $this->extract_item_value($stats, $questions[4]->id, 'facility');

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

        $this->assertDebuggingCalledCount(4,
            [
                'Deprecated: please use statistics_bulk_loader instead, or get_required_statistics_fields ' .
                    'in your question bank column class.',
                'Deprecated: please use statistics_bulk_loader instead, or get_required_statistics_fields ' .
                    'in your question bank column class.',
                'Deprecated: please use statistics_bulk_loader instead, or get_required_statistics_fields ' .
                    'in your question bank column class.',
                'Deprecated: please use statistics_bulk_loader instead, or get_required_statistics_fields ' .
                    'in your question bank column class.',
            ],
            [DEBUG_DEVELOPER, DEBUG_DEVELOPER, DEBUG_DEVELOPER, DEBUG_DEVELOPER]);
    }

    /**
     * Data provider for {@see test_load_question_discriminative_efficiency()}.
     * @return \Generator
     */
    public static function load_question_discriminative_efficiency_provider(): \Generator {
        yield 'Discriminative efficiency' => [
            'Quiz 1 attempts' => [
                self::generate_attempt_answers([1, 0, 0, 0]),
                self::generate_attempt_answers([1, 1, 0, 0]),
                self::generate_attempt_answers([1, 0, 1, 0]),
                self::generate_attempt_answers([1, 1, 1, 1]),
            ],
            'Expected quiz 1 discriminative efficiency' => ['N/A', '33.33%', '33.33%', '100.00%'],
            'Quiz 2 attempts' => [
                self::generate_attempt_answers([1, 1, 1, 1]),
                self::generate_attempt_answers([0, 0, 0, 0]),
                self::generate_attempt_answers([1, 0, 0, 1]),
                self::generate_attempt_answers([0, 1, 1, 0]),
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
     * @deprecated since Moodle 4.3 please use the method from statistics_bulk_loader.
     * @todo MDL-78090 Final deprecation in Moodle 4.7
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
        $stats = $this->load_quiz_statistics_for_place(\context_module::instance($quiz1->cmid));
        $discriminativeefficiency1 = $this->extract_item_value($stats, $questions[1]->id, 'discriminativeefficiency');
        $discriminativeefficiency2 = $this->extract_item_value($stats, $questions[2]->id, 'discriminativeefficiency');
        $discriminativeefficiency3 = $this->extract_item_value($stats, $questions[3]->id, 'discriminativeefficiency');
        $discriminativeefficiency4 = $this->extract_item_value($stats, $questions[4]->id, 'discriminativeefficiency');

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
        $stats = $this->load_quiz_statistics_for_place(\context_module::instance($quiz2->cmid));
        $discriminativeefficiency1 = $this->extract_item_value($stats, $questions[1]->id, 'discriminativeefficiency');
        $discriminativeefficiency2 = $this->extract_item_value($stats, $questions[2]->id, 'discriminativeefficiency');
        $discriminativeefficiency3 = $this->extract_item_value($stats, $questions[3]->id, 'discriminativeefficiency');
        $discriminativeefficiency4 = $this->extract_item_value($stats, $questions[4]->id, 'discriminativeefficiency');

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

        $this->assertDebuggingCalledCount(4,
            [
                'Deprecated: please use statistics_bulk_loader instead, or get_required_statistics_fields ' .
                    'in your question bank column class.',
                'Deprecated: please use statistics_bulk_loader instead, or get_required_statistics_fields ' .
                    'in your question bank column class.',
                'Deprecated: please use statistics_bulk_loader instead, or get_required_statistics_fields ' .
                    'in your question bank column class.',
                'Deprecated: please use statistics_bulk_loader instead, or get_required_statistics_fields ' .
                    'in your question bank column class.',
            ],
            [DEBUG_DEVELOPER, DEBUG_DEVELOPER, DEBUG_DEVELOPER, DEBUG_DEVELOPER]);
    }

    /**
     * Data provider for {@see test_load_question_discrimination_index()}.
     * @return \Generator
     */
    public static function load_question_discrimination_index_provider(): \Generator {
        yield 'Discrimination Index' => [
            'Quiz 1 attempts' => [
                self::generate_attempt_answers([1, 0, 0, 0]),
                self::generate_attempt_answers([1, 1, 0, 0]),
                self::generate_attempt_answers([1, 0, 1, 0]),
                self::generate_attempt_answers([1, 1, 1, 1]),
            ],
            'Expected quiz 1 Discrimination Index' => ['N/A', '30.15%', '30.15%', '81.65%'],
            'Quiz 2 attempts' => [
                self::generate_attempt_answers([1, 1, 1, 1]),
                self::generate_attempt_answers([0, 0, 0, 0]),
                self::generate_attempt_answers([1, 0, 0, 1]),
                self::generate_attempt_answers([0, 1, 1, 0]),
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
     * @deprecated since Moodle 4.3 please use the method from statistics_bulk_loader.
     * @todo MDL-78090 Final deprecation in Moodle 4.7
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
        $stats = $this->load_quiz_statistics_for_place(\context_module::instance($quiz1->cmid));
        $discriminationindex1 = $this->extract_item_value($stats, $questions[1]->id, 'discriminationindex');
        $discriminationindex2 = $this->extract_item_value($stats, $questions[2]->id, 'discriminationindex');
        $discriminationindex3 = $this->extract_item_value($stats, $questions[3]->id, 'discriminationindex');
        $discriminationindex4 = $this->extract_item_value($stats, $questions[4]->id, 'discriminationindex');

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
        $stats = $this->load_quiz_statistics_for_place(\context_module::instance($quiz2->cmid));
        $discriminationindex1 = $this->extract_item_value($stats, $questions[1]->id, 'discriminationindex');
        $discriminationindex2 = $this->extract_item_value($stats, $questions[2]->id, 'discriminationindex');
        $discriminationindex3 = $this->extract_item_value($stats, $questions[3]->id, 'discriminationindex');
        $discriminationindex4 = $this->extract_item_value($stats, $questions[4]->id, 'discriminationindex');

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

        $this->assertDebuggingCalledCount(4,
            [
                'Deprecated: please use statistics_bulk_loader instead, or get_required_statistics_fields ' .
                    'in your question bank column class.',
                'Deprecated: please use statistics_bulk_loader instead, or get_required_statistics_fields ' .
                    'in your question bank column class.',
                'Deprecated: please use statistics_bulk_loader instead, or get_required_statistics_fields ' .
                    'in your question bank column class.',
                'Deprecated: please use statistics_bulk_loader instead, or get_required_statistics_fields ' .
                    'in your question bank column class.',
            ],
            [DEBUG_DEVELOPER, DEBUG_DEVELOPER, DEBUG_DEVELOPER, DEBUG_DEVELOPER]);
    }
}
