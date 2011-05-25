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
 * Unit tests for the multianswer question definition class.
 *
 * @package    qtype
 * @subpackage multianswer
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->dirroot . '/question/engine/simpletest/helpers.php');


/**
 * Unit tests for qtype_multianswer_question.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_question_test extends UnitTestCase {
    public function test_get_expected_data() {
        $question = test_question_maker::make_question('multianswer');
        $this->assertEqual(array('sub1_answer' => PARAM_RAW_TRIMMED,
                'sub2_answer' => PARAM_RAW), $question->get_expected_data());
    }

    public function test_is_complete_response() {
        $question = test_question_maker::make_question('multianswer');

        $this->assertFalse($question->is_complete_response(array()));
        $this->assertTrue($question->is_complete_response(array('sub1_answer' => 'Owl',
                'sub2_answer' => '2')));
        $this->assertTrue($question->is_complete_response(array('sub1_answer' => '0',
                'sub2_answer' => 0)));
        $this->assertFalse($question->is_complete_response(array('sub1_answer' => 'Owl')));
    }

    public function test_is_gradable_response() {
        $question = test_question_maker::make_question('multianswer');

        $this->assertFalse($question->is_gradable_response(array()));
        $this->assertTrue($question->is_gradable_response(array('sub1_answer' => 'Owl',
                'sub2_answer' => '2')));
        $this->assertTrue($question->is_gradable_response(array('sub1_answer' => '0',
                'sub2_answer' => 0)));
        $this->assertTrue($question->is_gradable_response(array('sub1_answer' => 'Owl')));
    }

    public function test_grading() {
        $question = test_question_maker::make_question('multianswer');
        $question->start_attempt(new question_attempt_step(), 1);

        $rightchoice = $question->subquestions[2]->get_correct_response();

        $this->assertEqual(array(1, question_state::$gradedright), $question->grade_response(
                array('sub1_answer' => 'Owl', 'sub2_answer' => reset($rightchoice))));
        $this->assertEqual(array(0.5, question_state::$gradedpartial), $question->grade_response(
                array('sub1_answer' => 'Owl')));
        $this->assertEqual(array(0.5, question_state::$gradedpartial), $question->grade_response(
                array('sub1_answer' => 'Goat', 'sub2_answer' => reset($rightchoice))));
        $this->assertEqual(array(0, question_state::$gradedwrong), $question->grade_response(
                array('sub1_answer' => 'Dog')));
    }

    public function test_get_correct_response() {
        $question = test_question_maker::make_question('multianswer');
        $question->start_attempt(new question_attempt_step(), 1);

        $rightchoice = $question->subquestions[2]->get_correct_response();

        $this->assertEqual(array('sub1_answer' => 'Owl', 'sub2_answer' => reset($rightchoice)),
                $question->get_correct_response());
    }

    public function test_get_question_summary() {
        $question = test_question_maker::make_question('multianswer');

        // Bit of a hack to make testing easier.
        $question->subquestions[2]->shuffleanswers = false;

        $question->start_attempt(new question_attempt_step(), 1);

        $qsummary = $question->get_question_summary();
        $this->assertEqual('Complete this opening line of verse: "The _____ and the ' .
                '{Bow-wow; Wiggly worm; Pussy-cat} went to sea".', $qsummary);
    }

    public function test_summarise_response() {
        $question = test_question_maker::make_question('multianswer');
        $question->start_attempt(new question_attempt_step(), 1);

        $rightchoice = $question->subquestions[2]->get_correct_response();

        $this->assertEqual(get_string('subqresponse', 'qtype_multianswer',
                array('i' => 1, 'response' => 'Owl')) . '; ' .
                get_string('subqresponse', 'qtype_multianswer',
                array('i' => 2, 'response' => 'Pussy-cat')), $question->summarise_response(
                array('sub1_answer' => 'Owl', 'sub2_answer' => reset($rightchoice))));
    }
}