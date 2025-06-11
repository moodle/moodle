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

namespace qtype_ordering;

use test_question_maker;
use question_attempt_pending_step;
use question_state;
use qtype_ordering_question;
use question_attempt_step;
use question_classified_response;
use qtype_ordering_test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Unit tests for the ordering question definition class.
 *
 * @package   qtype_ordering
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \qtype_ordering
 * @covers    \qtype_ordering_question
 */
final class question_test extends \advanced_testcase {
    /**
     * Array of draggable items in correct order.
     */
    const CORRECTORDER = ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment'];

    /**
     * Array of draggable items in reverse order (incorrect).
     */
    const REVERSEORDER = ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular'];

    public function test_grading_all_or_nothing(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Zero grade on any error (no partial score at all, it is either 1 or 0).
        $question->gradingtype = qtype_ordering_question::GRADING_ALL_OR_NOTHING;
        $question->start_attempt(new question_attempt_pending_step(), 1);

        $this->assertEquals(
            [1, question_state::$gradedright],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']
                )
            )
        );
        $this->assertEquals(
            [0., question_state::$gradedwrong],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular']
                )
            )
        );
        $this->assertEquals(
            [0, question_state::$gradedwrong],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
    }

    public function test_grading_absolute_position(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Counts items, placed into right absolute place.
        $question->gradingtype = qtype_ordering_question::GRADING_ABSOLUTE_POSITION;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // Every item is in the correct position.
        $this->assertEquals(
            [1, question_state::$gradedright],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']
                )
            )
        );
        // None of the items are in the correct position.
        $this->assertEquals(
            [0., question_state::$gradedwrong],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular']
                )
            )
        );
        // 4 out of 6 items are in the correct position.
        $this->assertLessThan(
            [0.67, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Environment', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Modular']
                )
            )
        );
        $this->assertGreaterThan(
            [0.66, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Environment', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Modular']
                )
            )
        );
        // 1 out of 6 item is in the correct position.
        $this->assertLessThan(
            [0.17, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
        $this->assertGreaterThan(
            [0.16, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
    }

    public function test_grading_relative_next_exclude_last(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Every sequential pair in right order is graded (last pair is excluded).
        $question->gradingtype = qtype_ordering_question::GRADING_RELATIVE_NEXT_EXCLUDE_LAST;
        $question->start_attempt(new question_attempt_pending_step(), 1);

        // Every item is in the correct position.
        $this->assertEquals(
            [1, question_state::$gradedright],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']
                )
            )
        );
        // None of the items are in the correct position and there is not relative next.
        $this->assertEquals(
            [0., question_state::$gradedwrong],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular']
                )
            )
        );
        // 4 out of 6 items are in the correct position with relative next.
        $this->assertEquals(
            [0.6, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Environment', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Modular']
                )
            )
        );
        // 2 out of 6 item are in the correct position with relative next.
        $this->assertEquals(
            [0.4, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
    }

    public function test_grading_relative_next_include_last(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Every sequential pair in right order is graded (last pair is included).
        $question->gradingtype = qtype_ordering_question::GRADING_RELATIVE_NEXT_INCLUDE_LAST;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // Every item is in the correct position.
        $this->assertEquals(
            [1, question_state::$gradedright],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']
                )
            )
        );
        // None of the items are in the correct position and there is no relative next.
        $this->assertEquals(
            [0., question_state::$gradedwrong],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular']
                )
            )
        );
        // 3 out of 6 items are in the correct position with relative next.
        $this->assertEquals(
            [0.5, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Environment', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Modular']
                )
            )
        );
    }

    public function test_grading_relative_one_previous_and_next(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Single answers that are placed before and after each answer is graded if in right order.
        $question->gradingtype = qtype_ordering_question::GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // All items are in the correct position.
        $this->assertEquals(
            [1, question_state::$gradedright],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']
                )
            )
        );
        // None of the items are in the correct position.
        $this->assertEquals(
            [0., question_state::$gradedwrong],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular']
                )
            )
        );
        // Partically correct.
        $this->assertGreaterThan(
            [0.33, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
        $this->assertLessThan(
            [0.34, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
    }

    public function test_grading_relative_all_previous_and_next(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // All answers that are placed before and after each answer is graded if in right order.
        $question->gradingtype = qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // All items are in the correct position.
        $this->assertEquals(
            [1, question_state::$gradedright],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']
                )
            )
        );
        // None of the items are in the correct position.
        $this->assertEquals(
            [0., question_state::$gradedwrong],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular']
                )
            )
        );
        // Partially correct.
        $this->assertEquals(
            [0.6, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
        $this->assertLessThan(
            [0.7, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
        $this->assertGreaterThan(
            [0.6, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
    }

    public function test_grading_longest_ordered_subset(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Only longest ordered subset is graded.
        $question->gradingtype = qtype_ordering_question::GRADING_LONGEST_ORDERED_SUBSET;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // All items are in the correct position.
        $this->assertEquals(
            [1, question_state::$gradedright],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']
                )
            )
        );
        // None of the items are in the correct position.
        $this->assertEquals(
            [0., question_state::$gradedwrong],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular']
                )
            )
        );
        // 5 items make the longest ordered subset and the result is 5 out of 5 (0.8333333333....)
        $this->assertLessThan(
            [0.84, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
        $this->assertGreaterThan(
            [0.8, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
    }

    public function test_grading_longest_contiguous_subset(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Only longest ordered and contiguous subset is graded.
        $question->gradingtype = qtype_ordering_question::GRADING_LONGEST_CONTIGUOUS_SUBSET;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // All items are in the correct position.
        $this->assertEquals(
            [1, question_state::$gradedright],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']
                )
            )
        );
        // None of the items are in the correct position.
        $this->assertEquals(
            [0., question_state::$gradedwrong],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular']
                )
            )
        );
        // 5 items make the longest ordered subset and the result is 5 out of 6 (0.8333333333....)
        $this->assertLessThan(
            [0.84, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
        $this->assertGreaterThan(
            [0.8, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
    }

    public function test_grading_relative_to_correct(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Items are graded relative to their position in the correct answer.
        $question->gradingtype = qtype_ordering_question::GRADING_RELATIVE_TO_CORRECT;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // All items are in the correct position.
        $this->assertEquals(
            [1, question_state::$gradedright],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']
                )
            )
        );
        // None of the items are in the correct position.
        // TODO: This grading method is very generous. It has to be chnaged.
        $this->assertEquals(
            [0.4, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular']
                )
            )
        );

        $this->assertLessThan(
            [0.7, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
        $this->assertGreaterThan(
            [0.6, question_state::$gradedpartial],
            $question->grade_response(
                qtype_ordering_test_helper::get_response(
                    $question,
                    ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular']
                )
            )
        );
    }

    public function test_get_expected_data(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        $this->assertArrayHasKey('response_' . $question->id, $question->get_expected_data());
    }

    public function test_get_correct_response(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        $question->start_attempt(new question_attempt_pending_step(), 1);

        // The assertEquals() is used to replace the deprecated assertArraySubset(), because in this one case
        // they are equals. For more info see https://thephp.cc/articles/migrating-to-phpunit-9
        // and https://github.com/rdohms/phpunit-arraysubset-asserts.
        $this->assertEquals(
            qtype_ordering_test_helper::get_response($question, self::CORRECTORDER),
            $question->get_correct_response()
        );
    }

    public function test_is_same_response(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        $question->start_attempt(new question_attempt_pending_step(), 1);

        $old = qtype_ordering_test_helper::get_response(
            $question,
            ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']
        );
        $new = $old;
        $this->assertTrue($question->is_same_response($old, $new));

        $new = qtype_ordering_test_helper::get_response(
            $question,
            ['Environment', 'Modular', 'Object', 'Oriented', 'Dynamic', 'Learning']
        );
        $this->assertFalse($question->is_same_response($old, $new));
    }

    public function test_summarise_response(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        $question->start_attempt(new question_attempt_pending_step(), 1);

        $expected = 'Modular; Object; Oriented; Dynamic; Learning; Environ...';
        $actual = $question->summarise_response(
            qtype_ordering_test_helper::get_response(
                $question,
                ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']
            )
        );
        $this->assertEquals($expected, $actual);

        $expected = 'Environ...; Modular; Object; Oriented; Dynamic; Learning';
        $actual = $question->summarise_response(
            qtype_ordering_test_helper::get_response(
                $question,
                ['Environment', 'Modular', 'Object', 'Oriented', 'Dynamic', 'Learning']
            )
        );
        $this->assertEquals($expected, $actual);

        // Confirm that if a passed array contains an item that does not exist in the question, it is ignored.
        $actual = $question->summarise_response(
            qtype_ordering_test_helper::get_response(
                $question,
                ['notexist']
            )
        );
        $this->assertEquals('', $actual);
    }

    public function test_initialise_question_instance(): void {
        // Create an Ordering question.
        $questiondata = test_question_maker::get_question_data('ordering');
        $question = \question_bank::make_question($questiondata);
        $this->assertStringContainsString('ordering_item_', reset($question->answers)->md5key);
    }

    public function test_get_ordering_layoutclass(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');

        $question->layouttype = 0;
        $this->assertEquals('vertical', $question->get_ordering_layoutclass());

        $question->layouttype = 1;
        $this->assertEquals('horizontal', $question->get_ordering_layoutclass());

        // Confirm that if an invalid layouttype is set, an empty string is returned.
        $question->layouttype = 3;
        $error = $question->get_ordering_layoutclass();
        $this->assertEquals('', $error);
    }

    public function test_get_next_answerids(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        $answerids = array_keys($question->answers);

        // Get next answerids excluding the last.
        $nextansweidsexcludinglast = $question->get_next_answerids($answerids);
        $numberofids = count($answerids);
        foreach ($question->answers as $answerid => $notused) {
            if ($numberofids > 1) {
                $this->assertEquals($answerid + 1, $nextansweidsexcludinglast[$answerid]);
            }
            $numberofids--;
        }

        // Get next answerids including the last.
        $nextansweidsincludinglast = $question->get_next_answerids($answerids, true);
        $numberofids = count($answerids);
        foreach ($question->answers as $answerid => $notused) {
            if ($numberofids > 1) {
                $this->assertEquals($answerid + 1, $nextansweidsincludinglast[$answerid]);
            } else {
                $this->assertEquals(0, $nextansweidsincludinglast[$answerid]);
            }
            $numberofids--;
        }
    }

    public function test_get_previous_and_next_answerids(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        $answerids = array_keys($question->answers);

        // Get immediate prev and next answerid.
        $prevnextansweids = $question->get_previous_and_next_answerids($answerids);
        $numberofids = count($answerids);
        foreach ($question->answers as $answerid => $notused) {
            if ($numberofids > 1 && $numberofids < count($answerids)) {
                $this->assertEquals($answerid - 1, $prevnextansweids[$answerid]->prev[0]);
                $this->assertEquals($answerid + 1, $prevnextansweids[$answerid]->next[0]);
            } else if ($numberofids === count($answerids)) {
                $this->assertEquals(0, $prevnextansweids[$answerid]->prev[0]);
                $this->assertEquals($answerid + 1, $prevnextansweids[$answerid]->next[0]);
            } else if ($numberofids === 1) {
                $this->assertEquals($answerid - 1, $prevnextansweids[$answerid]->prev[0]);
                $this->assertEquals(0, $prevnextansweids[$answerid]->next[0]);
            }
            $numberofids--;
        }
    }

    public function test_classify_response_correct(): void {
        $question = test_question_maker::make_question('ordering');
        $question->start_attempt(new question_attempt_step(), 1);

        $response = qtype_ordering_test_helper::get_response(
            $question,
            ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']
        );
        $classifiedresponse = $question->classify_response($response);

        $expected = [
                'Modular' => new question_classified_response(1, 'Position 1', 0.1666667),
                'Object' => new question_classified_response(2, 'Position 2', 0.1666667),
                'Oriented' => new question_classified_response(3, 'Position 3', 0.1666667),
                'Dynamic' => new question_classified_response(4, 'Position 4', 0.1666667),
                'Learning' => new question_classified_response(5, 'Position 5', 0.1666667),
                'Environment' => new question_classified_response(6, 'Position 6', 0.1666667),
        ];

        $this->assertEqualsWithDelta($expected, $classifiedresponse, 0.0000005);
    }

    public function test_classify_response_partially_correct(): void {
        $question = test_question_maker::make_question('ordering');
        $question->start_attempt(new question_attempt_step(), 1);

        $response = qtype_ordering_test_helper::get_response(
            $question,
            ['Dynamic', 'Modular', 'Object', 'Oriented', 'Learning', 'Environment']
        );
        $classifiedresponse = $question->classify_response($response);

        $expected = [
                'Modular' => new question_classified_response(2, 'Position 2', 0),
                'Object' => new question_classified_response(3, 'Position 3', 0),
                'Oriented' => new question_classified_response(4, 'Position 4', 0),
                'Dynamic' => new question_classified_response(1, 'Position 1', 0),
                'Learning' => new question_classified_response(5, 'Position 5', 0.1666667),
                'Environment' => new question_classified_response(6, 'Position 6', 0.1666667),
        ];

        $this->assertEqualsWithDelta($expected, $classifiedresponse, 0.0000005);
    }

    /**
     * Test get number of correct|partial|incorrect on response.
     */
    public function test_get_num_parts_right(): void {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');

        $question->gradingtype = qtype_ordering_question::GRADING_RELATIVE_TO_CORRECT;
        $question->start_attempt(new question_attempt_pending_step(), 1);

        $response = qtype_ordering_test_helper::get_response(
            $question,
            ['Dynamic', 'Modular', 'Object', 'Oriented', 'Learning', 'Environment']
        );
        $numparts = $question->get_num_parts_right($response);

        $this->assertEquals([2, 4, 0], $numparts);
    }

    public function test_validate_can_regrade_with_other_version_bad(): void {
        if (!method_exists('question_definition', 'validate_can_regrade_with_other_version')) {
            $this->markTestSkipped('This test only applies to Moodle 4.x');
        }

        $question = test_question_maker::make_question('ordering');

        $newq = clone($question);
        $helper = new \qtype_ordering_test_helper();
        $newq->answers = [
            23 => $helper->make_answer(23, 'Modular', FORMAT_HTML, 1, true),
            24 => $helper->make_answer(24, 'Object', FORMAT_HTML, 2, true),
            25 => $helper->make_answer(25, 'Oriented', FORMAT_HTML, 3, true),
            26 => $helper->make_answer(26, 'Dynamic', FORMAT_HTML, 4, true),
            27 => $helper->make_answer(27, 'Learning', FORMAT_HTML, 5, true),
        ];

        $this->assertEquals(
            get_string('regradeissuenumitemschanged', 'qtype_ordering'),
            $newq->validate_can_regrade_with_other_version($question)
        );
    }

    public function test_validate_can_regrade_with_other_version_ok(): void {
        if (!method_exists('question_definition', 'validate_can_regrade_with_other_version')) {
            $this->markTestSkipped('This test only applies to Moodle 4.x');
        }

        $question = test_question_maker::make_question('ordering');

        $newq = clone($question);
        $helper = new \qtype_ordering_test_helper();
        $newq->answers = [
            23 => $helper->make_answer(23, 'Modular', FORMAT_HTML, 1, true),
            24 => $helper->make_answer(24, 'Object', FORMAT_HTML, 2, true),
            25 => $helper->make_answer(25, 'Oriented', FORMAT_HTML, 3, true),
            26 => $helper->make_answer(26, 'Dynamic', FORMAT_HTML, 4, true),
            27 => $helper->make_answer(27, 'Learning', FORMAT_HTML, 5, true),
            28 => $helper->make_answer(28, 'Environment', FORMAT_HTML, 6, true),
        ];

        $this->assertNull($newq->validate_can_regrade_with_other_version($question));
    }

    public function test_update_attempt_state_date_from_old_version_bad(): void {
        if (!method_exists('question_definition', 'update_attempt_state_data_for_new_version')) {
            $this->markTestSkipped('This test only applies to Moodle 4.x');
        }

        $question = test_question_maker::make_question('ordering');

        $newq = clone($question);
        $helper = new \qtype_ordering_test_helper();
        $newq->answers = [
            23 => $helper->make_answer(23, 'Modular', FORMAT_HTML, 1, true),
            24 => $helper->make_answer(24, 'Object', FORMAT_HTML, 2, true),
            25 => $helper->make_answer(25, 'Oriented', FORMAT_HTML, 3, true),
            26 => $helper->make_answer(26, 'Dynamic', FORMAT_HTML, 4, true),
            27 => $helper->make_answer(27, 'Learning', FORMAT_HTML, 5, true),
        ];

        $oldstep = new question_attempt_step();
        $oldstep->set_qt_var('_currentresponse', '15,13,17,16,18,14');
        $oldstep->set_qt_var('_correctresponse', '13,14,15,16,17,18');
        $this->expectExceptionMessage(get_string('regradeissuenumitemschanged', 'qtype_ordering'));
        $newq->update_attempt_state_data_for_new_version($oldstep, $question);
    }

    public function test_update_attempt_state_date_from_old_version_ok(): void {
        if (!method_exists('question_definition', 'update_attempt_state_data_for_new_version')) {
            $this->markTestSkipped('This test only applies to Moodle 4.x');
        }

        $question = test_question_maker::make_question('ordering');

        $newq = clone($question);
        $helper = new \qtype_ordering_test_helper();
        $newq->answers = [
            23 => $helper->make_answer(23, 'Modular', FORMAT_HTML, 1, true),
            24 => $helper->make_answer(24, 'Object', FORMAT_HTML, 2, true),
            25 => $helper->make_answer(25, 'Oriented', FORMAT_HTML, 3, true),
            26 => $helper->make_answer(26, 'Dynamic', FORMAT_HTML, 4, true),
            27 => $helper->make_answer(27, 'Learning', FORMAT_HTML, 5, true),
            28 => $helper->make_answer(28, 'Environment', FORMAT_HTML, 6, true),
        ];

        $oldstep = new question_attempt_step();
        $oldstep->set_qt_var('_currentresponse', '15,13,17,16,18,14');
        $oldstep->set_qt_var('_correctresponse', '13,14,15,16,17,18');

        $this->assertEquals(
            ['_currentresponse' => '25,23,27,26,28,24', '_correctresponse' => '23,24,25,26,27,28'],
            $newq->update_attempt_state_data_for_new_version($oldstep, $question)
        );
    }

    public function test_helpers(): void {
        $question = test_question_maker::make_question('ordering');
        $this->assertEquals(true, $question->is_complete_response([]));
        $this->assertEquals(true, $question->is_gradable_response([]));
        $this->assertEquals('', $question->get_validation_error([]));
    }
}
