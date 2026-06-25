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
use core\output\datafilter;
use core_tag_area;
use mod_quiz\question\display_options;
use mod_quiz\tests\question_helper_test_trait;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/tests/classes/question_helper_test_trait.php');

/**
 * Unit tests for the quiz class
 *
 * @package    mod_quiz
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\quiz_settings
 */
final class quizobj_test extends advanced_testcase {
    use question_helper_test_trait;

    /**
     * Test cases for {@see test_cannot_review_message()}.
     *
     * @return array[]
     */
    public static function cannot_review_message_testcases(): array {
        return [
            // Review       Time close
            // Later close  quiz attempt    When                Expected
            // Quiz with no close date.
            [false, false, null, null, display_options::DURING, ''],
            [false, false, null, -60, display_options::IMMEDIATELY_AFTER, 'noreview'],
            [false, false, null, -180, display_options::LATER_WHILE_OPEN, 'noreview'],
            [false, false, null, -180, display_options::AFTER_CLOSE, 'noreview'],
            [false, true, null, null, display_options::DURING, ''],
            [false, true, null, -60, display_options::IMMEDIATELY_AFTER, 'noreview'],
            [false, true, null, -180, display_options::LATER_WHILE_OPEN, 'noreview'],
            [false, true, null, -180, display_options::AFTER_CLOSE, 'noreview'],
            // Quiz with a close in the future date, review only after close.
            [false, true, 300, null, display_options::DURING, ''],
            [false, true, 300, -60, display_options::IMMEDIATELY_AFTER, 300],
            [false, true, 300, -180, display_options::LATER_WHILE_OPEN, 300],
            // Quiz with a close in the future date, review later while open, or after close.
            [true, true, 300, null, display_options::DURING, ''],
            [true, true, 300, -60, display_options::IMMEDIATELY_AFTER, 60],
            [true, false, 300, -60, display_options::IMMEDIATELY_AFTER, 60],
            // Quiz with no closer date, review later while open.
            [true, false, 300, null, display_options::DURING, ''],
            [true, false, 300, -60, display_options::IMMEDIATELY_AFTER, 60],
        ];
    }

    /**
     * Unit test for {@see quiz_settings::cannot_review_message()}.
     *
     * @dataProvider cannot_review_message_testcases
     * @param bool $reviewlater whether the quiz allows reivew 'later while the quiz is still open'.
     * @param bool $reviewafterclose whether the quiz allows rievew 'after the quiz is closed'.
     * @param int|null $quizcloseoffset quiz close date, relative to now. Null means not set.
     * @param int|null $attemptsubmitoffset quiz attempt sumbite time relative to now. Null means not submitted yet.
     * @param int $attemptstate current state of the attempt, one of the display_options constants.
     * @param string|int $expectation expected result: '' means '', 'noreview' means noreview lang string,
     *      int means noreviewuntil with that time relative to now.
     */
    public function test_cannot_review_message(
        bool $reviewlater,
        bool $reviewafterclose,
        ?int $quizcloseoffset,
        ?int $attemptsubmitoffset,
        int $attemptstate,
        string|int $expectation
    ): void {
        $quiz = new stdClass();
        $now = time();

        $cm = new stdClass();
        $cm->id = 123;

        // Prepare quiz settings.
        $quiz->reviewattempt = display_options::DURING;
        if ($reviewlater) {
            $quiz->reviewattempt |= display_options::LATER_WHILE_OPEN;
        }
        if ($reviewafterclose) {
            $quiz->reviewattempt |= display_options::AFTER_CLOSE;
        }
        $quiz->attempts = 0;

        if ($quizcloseoffset === null) {
            $quiz->timeclose = 0;
        } else {
            $quiz->timeclose = $now + $quizcloseoffset;
        }
        if ($attemptsubmitoffset === null) {
            $submittime = 0;
        } else {
            $submittime = $now + $attemptsubmitoffset;
        }

        $quizobj = new quiz_settings($quiz, $cm, new stdClass(), false);

        // Prepare expected message.
        if ($expectation === 'noreview') {
            $expectation = get_string('noreview', 'quiz');
        } else if (is_int($expectation)) {
            $expectation = get_string('noreviewuntil', 'quiz', userdate($now + $expectation));
        }

        // Test.
        $this->assertEquals($expectation,
            $quizobj->cannot_review_message($attemptstate, false, $submittime));
    }

    /**
     * Data provider for testing the correct question types are returned.
     *
     * @return array[]
     */
    public static function question_types(): array {
        return [
            'only direct questions' => [
                'potential' => false,
                'types' => ['numerical', 'shortanswer'],
            ],
            'include potential questions' => [
                'potential' => true,
                'types' => ['essay', 'numerical', 'shortanswer', 'truefalse'],
            ],
        ];
    }

    /**
     * Return the question types used by a quiz.
     *
     * @param bool $potential Include potential types from random questions.
     * @param array $types List of types to expect, in alphabetical order.
     */
    #[DataProvider('question_types')]
    public function test_get_all_question_types_used(bool $potential, array $types): void {
        $this->setAdminUser();
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $quiz = $this->create_test_quiz($course);
        [, $cm] = get_course_and_cm_from_cmid($quiz->cmid, 'quiz');
        $quizobj = new quiz_settings($quiz, $cm, $course);
        // Add shortanswer and numerical questions.
        $this->add_two_regular_questions($questiongenerator, $quiz);
        // Add truefalse and essay as potential random questions.
        $this->add_one_random_question($questiongenerator, $quiz);
        $quizobj->preload_questions();
        $usedtypes = $quizobj->get_all_question_types_used($potential);
        $this->assertEquals($types, $usedtypes);
    }

    /**
     * Return the question types based on all filters in a random question.
     */
    public function test_get_all_question_types_used_with_tag(): void {
        $this->setAdminUser();
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $quiz = $this->create_test_quiz($course);
        [, $cm] = get_course_and_cm_from_cmid($quiz->cmid, 'quiz');
        $quizobj = new quiz_settings($quiz, $cm, $course);
        // Add shortanswer and numerical questions.
        $this->add_two_regular_questions($questiongenerator, $quiz);
        // Add essay as a potential random question with a tag, and truefalse as another question in the category.
        $randomcategory = $questiongenerator->create_question_category();
        $questiongenerator->create_question('truefalse', null, ['category' => $randomcategory->id]);
        $taggedquestion = $questiongenerator->create_question('essay', null, ['category' => $randomcategory->id]);
        $questiongenerator->create_question_tag(['questionid' => $taggedquestion->id, 'tag' => 'test']);
        $tagcollid = core_tag_area::get_collection('core_question', 'question');
        $tag = \core_tag_tag::get_by_name($tagcollid, 'test');
        $filtercondition = [
            'filter' => [
                'category' => [
                    'jointype' => datafilter::JOINTYPE_ALL,
                    'values' => [$randomcategory->id],
                    'filteroptions' => ['includesubcategories' => false],
                ],
                'qtagids' => [
                    'jointype' => datafilter::JOINTYPE_ALL,
                    'values' => [$tag->id],
                ],
            ],
        ];
        $quizobj->get_structure()->add_random_questions(1, 1, $filtercondition);
        $quizobj->preload_questions();
        $usedtypes = $quizobj->get_all_question_types_used(true);
        $this->assertCount(3, $usedtypes);
        $this->assertEquals(['essay', 'numerical', 'shortanswer'], $usedtypes);
    }
}
