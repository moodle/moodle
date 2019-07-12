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
 * Unit tests for the ordering question definition class.
 *
 * @package    qtype_ordering
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Unit tests for the ordering question definition class.
 *
 * @copyright 2018 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering_question_test extends advanced_testcase {
    /**
     * Array of draggable items in correct order.
     */
    const CORRECTORDER = ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment'];

    /**
     * Array of draggable items in reverse order (incorrect).
     */
    const REVERSEORDER = ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular'];

    /**
     * return an array of answer codes in the order of given response.
     * @param object $question the question object
     * @param array $items array of input items
     */
    private function get_response($question, $items) {
        $md5keys = [];
        foreach ($items as $item) {
            foreach ($question->answers as $answer) {
                if ($item === $answer->answer) {
                    $md5keys[] = $answer->md5key;
                    break;
                }
            }
        }
        return ['response_' . $question->id => implode(',', $md5keys)];
    }

    public function test_grading_all_or_nothing () {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Zero grade on any error (no partial scroe at all, it is either 1 or 0).
        $question->options->gradingtype = qtype_ordering_question::GRADING_ALL_OR_NOTHING;
        $question->start_attempt(new question_attempt_pending_step(), 1);

        $this->assertEquals([1, question_state::$gradedright],
                $question->grade_response($this->get_response($question,
                        ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment'])));
        $this->assertEquals([0., question_state::$gradedwrong],
                $question->grade_response($this->get_response($question,
                        ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular'])));
        $this->assertEquals([0, question_state::$gradedwrong],
                $question->grade_response($this->get_response($question,
                        ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular'])));
    }
    public function test_grading_absolute_position () {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Counts items, placed into right absolute place.
        $question->options->gradingtype = qtype_ordering_question::GRADING_ABSOLUTE_POSITION;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // Every item is in the correct position.
        $this->assertEquals([1, question_state::$gradedright],
                $question->grade_response($this->get_response($question,
                        ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment'])));
        // None of the items are in the correct position.
        $this->assertEquals([0., question_state::$gradedwrong],
                $question->grade_response($this->get_response($question,
                        ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular'])));
        // 4 out of 6 items are in the correct position.
        $this->assertLessThan([0.67, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Environment', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Modular'])));
        $this->assertGreaterThan([0.66, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Environment', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Modular'])));
        // 1 out of 6 item is in the correct position.
        $this->assertLessThan([0.17, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular'])));
        $this->assertGreaterThan([0.16, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular'])));
    }
    public function test_grading_relative_next_exclude_last () {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Every sequential pair in right order is graded (last pair is excluded).
        $question->options->gradingtype = qtype_ordering_question::GRADING_RELATIVE_NEXT_EXCLUDE_LAST;
        $question->start_attempt(new question_attempt_pending_step(), 1);

        // Every item is in the correct position.
        $this->assertEquals([1, question_state::$gradedright],
                $question->grade_response($this->get_response($question,
                        ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment'])));
        // None of the items are in the correct position and there is not relative next.
        $this->assertEquals([0., question_state::$gradedwrong],
                $question->grade_response($this->get_response($question,
                        ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular'])));
        // 4 out of 6 items are in the correct position with relative next.
        $this->assertEquals([0.6, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Environment', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Modular'])));
        // 2 out of 6 item are in the correct position with relative next.
        $this->assertEquals([0.4, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular'])));

    }
    public function test_grading_relative_next_include_last () {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Every sequential pair in right order is graded (last pair is included).
        $question->options->gradingtype = qtype_ordering_question::GRADING_RELATIVE_NEXT_INCLUDE_LAST;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // Every item is in the correct position.
        $this->assertEquals([1, question_state::$gradedright],
                $question->grade_response($this->get_response($question,
                        ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment'])));
        // None of the items are in the correct position and there is not relative next.
        $this->assertEquals([0., question_state::$gradedwrong],
                $question->grade_response($this->get_response($question,
                        ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular'])));
        // 3 out of 6 items are in the correct position with relative next.
        $this->assertEquals([0.5, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Environment', 'Object', 'Oriented',  'Dynamic', 'Learning', 'Modular'])));

    }
    public function test_grading_relative_one_previous_and_next () {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Single answers that are placed before and after each answer is graded if in right order.
        $question->options->gradingtype = qtype_ordering_question::GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // All items are in the correct position.
        $this->assertEquals([1, question_state::$gradedright],
                        $question->grade_response($this->get_response($question,
                                ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment'])));
        // None of the items are in the correct position.
        $this->assertEquals([0., question_state::$gradedwrong],
                $question->grade_response($this->get_response($question,
                        ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular'])));
        // Partically correct.
        $this->assertGreaterThan([0.33, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular'])));
        $this->assertLessThan([0.34, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular'])));
    }
    public function test_grading_relative_all_previous_and_next () {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // All answers that are placed before and after each answer is graded if in right order.
        $question->options->gradingtype = qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // All items are in the correct position.
        $this->assertEquals([1, question_state::$gradedright],
                        $question->grade_response($this->get_response($question,
                                ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment'])));
        // None of the items are in the correct position.
        $this->assertEquals([0., question_state::$gradedwrong],
                $question->grade_response($this->get_response($question,
                        ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular'])));
        // Partially correct.
        $this->assertEquals([0.6, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Oriented', 'Object', 'Dynamic', 'Learning', 'Environment', 'Modular'])));
        $this->assertLessThan([0.7, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular'])));
        $this->assertGreaterThan([0.6, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular'])));

    }
    public function test_grading_longest_ordered_subset () {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Only longest ordered subset is graded.
        $question->options->gradingtype = qtype_ordering_question::GRADING_LONGEST_ORDERED_SUBSET;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // All items are in the correct position.
        $this->assertEquals([1, question_state::$gradedright],
                $question->grade_response($this->get_response($question,
                        ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment'])));
        // None of the items are in the correct position.
        $this->assertEquals([0., question_state::$gradedwrong],
                $question->grade_response($this->get_response($question,
                        ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular'])));
        // 5 items make the longest ordered subset and the result is 5 out of 5 (0.8333333333....)
        $this->assertLessThan([0.84, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular'])));
        $this->assertGreaterThan([0.8, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular'])));
    }
    public function test_grading_longest_contiguous_subset () {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Only longest ordered and contiguous subset is graded.
        $question->options->gradingtype = qtype_ordering_question::GRADING_LONGEST_CONTIGUOUS_SUBSET;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // All items are in the correct position.
        $this->assertEquals([1, question_state::$gradedright],
                $question->grade_response($this->get_response($question,
                        ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment'])));
        // None of the items are in the correct position.
        $this->assertEquals([0., question_state::$gradedwrong],
                $question->grade_response($this->get_response($question,
                        ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular'])));
        // 5 items make the longest ordered subset and the result is 5 out of 6 (0.8333333333....)
        $this->assertLessThan([0.84, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular'])));
        $this->assertGreaterThan([0.8, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular'])));
    }
    public function test_grading_relative_to_correct () {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        // Items are graded relative to their position in the correct answer.
        $question->options->gradingtype = qtype_ordering_question::GRADING_RELATIVE_TO_CORRECT;
        $question->start_attempt(new question_attempt_pending_step(), 1);
        // All items are in the correct position.
        $this->assertEquals([1, question_state::$gradedright],
                $question->grade_response($this->get_response($question,
                        ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment'])));
        // None of the items are in the correct position.
        // TODO: This grading method is very generous. It has to be chnaged.
        $this->assertEquals([0.4, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Environment', 'Learning', 'Dynamic', 'Oriented', 'Object', 'Modular'])));

        $this->assertLessThan([0.7, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular'])));
        $this->assertGreaterThan([0.6, question_state::$gradedpartial],
                $question->grade_response($this->get_response($question,
                        ['Object', 'Oriented', 'Dynamic', 'Learning', 'Environment', 'Modular'])));
    }

    public function test_get_expected_data() {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        $this->assertArrayHasKey('response_' . $question->id, $question->get_expected_data());
    }

    public function test_get_correct_response() {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        $question->start_attempt(new question_attempt_pending_step(), 1);
        $this->assertArraySubset($this->get_response($question, self::CORRECTORDER), $question->get_correct_response());
    }
    public function test_is_same_response() {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        $question->start_attempt(new question_attempt_pending_step(), 1);

        $old = $this->get_response($question, ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']);
        $new = $old;
        $this->assertTrue($question->is_same_response($old, $new));

        $new = $this->get_response($question, ['Environment', 'Modular', 'Object', 'Oriented', 'Dynamic', 'Learning']);
        $this->assertFalse($question->is_same_response($old, $new));
    }

    public function test_summarise_response() {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        $question->start_attempt(new question_attempt_pending_step(), 1);

        $expected = 'Modular; Object; Oriented; Dynamic; Learning; Environ...';
        $actual = $question->summarise_response($this->get_response($question,
                ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']));
        $this->assertEquals($expected, $actual);

        $expected = 'Environ...; Modular; Object; Oriented; Dynamic; Learning';
        $actual = $question->summarise_response($this->get_response($question,
                ['Environment', 'Modular', 'Object', 'Oriented', 'Dynamic', 'Learning']));
        $this->assertEquals($expected, $actual);
    }

    public function test_get_ordering_answers() {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');
        $this->assertEquals($question->answers, $question->get_ordering_answers());
    }

    public function test_get_ordering_layoutclass() {
        // Create an Ordering question.
        $question = test_question_maker::make_question('ordering');

        if ($question->options->layouttype === 0) {
            $this->assertEquals('vertical', $question->get_ordering_layoutclass());
        } else if ($question->options->layouttype === 1) {
            $this->assertEquals('horizontal', $question->get_ordering_layoutclass());
        }
    }

    public function test_get_next_answerids() {
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

    public function test_get_previous_and_next_answerids() {
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

    public function test_classify_response_correct() {
        $question = test_question_maker::make_question('ordering');
        $question->start_attempt(new question_attempt_step(), 1);

        $response = $this->get_response($question, ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']);
        $classifiedresponse = $question->classify_response($response);

        $expected = [
                'Modular' => new question_classified_response(1, 'Position 1', 0.1666667),
                'Object' => new question_classified_response(2, 'Position 2', 0.1666667),
                'Oriented' => new question_classified_response(3, 'Position 3', 0.1666667),
                'Dynamic' => new question_classified_response(4, 'Position 4', 0.1666667),
                'Learning' => new question_classified_response(5, 'Position 5', 0.1666667),
                'Environment' => new question_classified_response(6, 'Position 6', 0.1666667),
        ];

        $this->assertEquals($expected, $classifiedresponse, '', 0.0000005);
    }

    public function test_classify_response_partially_correct() {
        $question = test_question_maker::make_question('ordering');
        $question->start_attempt(new question_attempt_step(), 1);

        $response = $this->get_response($question, ['Dynamic', 'Modular', 'Object', 'Oriented', 'Learning', 'Environment']);
        $classifiedresponse = $question->classify_response($response);

        $expected = [
                'Modular' => new question_classified_response(2, 'Position 2', 0),
                'Object' => new question_classified_response(3, 'Position 3', 0),
                'Oriented' => new question_classified_response(4, 'Position 4', 0),
                'Dynamic' => new question_classified_response(1, 'Position 1', 0),
                'Learning' => new question_classified_response(5, 'Position 5', 0.1666667),
                'Environment' => new question_classified_response(6, 'Position 6', 0.1666667),
        ];

        $this->assertEquals($expected, $classifiedresponse, '', 0.0000005);
    }
}
