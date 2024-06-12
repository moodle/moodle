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

namespace qtype_multichoice;

use question_attempt_step;
use question_classified_response;
use question_display_options;
use question_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Unit tests for the multiple choice, multi-response question definition class.
 *
 * @package   qtype_multichoice
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qtype_multichoice_multi_question
 */
class question_multi_test extends \advanced_testcase {

    public function test_get_expected_data(): void {
        $question = \test_question_maker::make_a_multichoice_multi_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array('choice0' => PARAM_BOOL, 'choice1' => PARAM_BOOL,
                'choice2' => PARAM_BOOL, 'choice3' => PARAM_BOOL), $question->get_expected_data());
    }

    public function test_is_complete_response(): void {
        $question = \test_question_maker::make_a_multichoice_multi_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($question->is_complete_response(array()));
        $this->assertFalse($question->is_complete_response(
                array('choice0' => '0', 'choice1' => '0', 'choice2' => '0', 'choice3' => '0')));
        $this->assertTrue($question->is_complete_response(array('choice1' => '1')));
        $this->assertTrue($question->is_complete_response(
                array('choice0' => '1', 'choice1' => '1', 'choice2' => '1', 'choice3' => '1')));
    }

    public function test_is_gradable_response(): void {
        $question = \test_question_maker::make_a_multichoice_multi_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($question->is_gradable_response(array()));
        $this->assertFalse($question->is_gradable_response(
                array('choice0' => '0', 'choice1' => '0', 'choice2' => '0', 'choice3' => '0')));
        $this->assertTrue($question->is_gradable_response(array('choice1' => '1')));
        $this->assertTrue($question->is_gradable_response(
                array('choice0' => '1', 'choice1' => '1', 'choice2' => '1', 'choice3' => '1')));
    }

    public function test_is_same_response(): void {
        $question = \test_question_maker::make_a_multichoice_multi_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertTrue($question->is_same_response(
                array(),
                array('choice0' => '0', 'choice1' => '0', 'choice2' => '0', 'choice3' => '0')));

        $this->assertTrue($question->is_same_response(
                array('choice0' => '0', 'choice1' => '0', 'choice2' => '0', 'choice3' => '0'),
                array('choice0' => '0', 'choice1' => '0', 'choice2' => '0', 'choice3' => '0')));

        $this->assertFalse($question->is_same_response(
                array('choice0' => '0', 'choice1' => '0', 'choice2' => '0', 'choice3' => '0'),
                array('choice0' => '1', 'choice1' => '0', 'choice2' => '0', 'choice3' => '0')));

        $this->assertTrue($question->is_same_response(
                array('choice0' => '1', 'choice1' => '0', 'choice2' => '1', 'choice3' => '0'),
                array('choice0' => '1', 'choice1' => '0', 'choice2' => '1', 'choice3' => '0')));
    }

    public function test_grading(): void {
        $question = \test_question_maker::make_a_multichoice_multi_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response($question->prepare_simulated_post_data(array('A' => 1, 'C' => 1))));
        $this->assertEquals(array(0.5, question_state::$gradedpartial),
                $question->grade_response($question->prepare_simulated_post_data(array('A' => 1))));
        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response($question->prepare_simulated_post_data(array('A' => 1, 'B' => 1, 'C' => 1))));
        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response($question->prepare_simulated_post_data(array('B' => 1))));
    }

    public function test_get_correct_response(): void {
        $question = \test_question_maker::make_a_multichoice_multi_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals($question->prepare_simulated_post_data(array('A' => 1, 'C' => 1)), $question->get_correct_response());
    }

    public function test_get_question_summary(): void {
        $mc = \test_question_maker::make_a_multichoice_single_question();
        $mc->start_attempt(new question_attempt_step(), 1);

        $qsummary = $mc->get_question_summary();

        $this->assertMatchesRegularExpression('/' . preg_quote($mc->questiontext, '/') . '/', $qsummary);
        foreach ($mc->answers as $answer) {
            $this->assertMatchesRegularExpression('/' . preg_quote($answer->answer, '/') . '/', $qsummary);
        }
    }

    public function test_summarise_response(): void {
        $mc = \test_question_maker::make_a_multichoice_multi_question();
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        $summary = $mc->summarise_response($mc->prepare_simulated_post_data(['B' => 1, 'C' => 1]));

        $this->assertEquals('B; C', $summary);
    }

    public function test_summarise_response_clearchoice(): void {
        $mc = \test_question_maker::make_a_multichoice_multi_question();
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        $summary = $mc->summarise_response($mc->prepare_simulated_post_data(['clearchoice' => -1]));

        $this->assertNull($summary);
    }

    public function test_un_summarise_response(): void {
        $mc = \test_question_maker::make_a_multichoice_multi_question();
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(['choice1' => '1', 'choice2' => '1'], $mc->un_summarise_response('B; C'));

        $this->assertEquals([], $mc->un_summarise_response(''));
    }

    public function test_classify_response(): void {
        $mc = \test_question_maker::make_a_multichoice_multi_question();
        $mc->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(
                    13 => new question_classified_response(13, 'A', 0.5),
                    14 => new question_classified_response(14, 'B', -1.0),
                ), $mc->classify_response($mc->prepare_simulated_post_data(array('A' => 1, 'B' => 1))));

        $this->assertEquals(array(), $mc->classify_response(array()));
    }

    public function test_prepare_simulated_post_data(): void {
        $mc = \test_question_maker::make_a_multichoice_multi_question();
        $mc->start_attempt(new question_attempt_step(), 1);
        $correctanswers = array(
            array(),
            array('A' => 1),
            array('B' => 1, 'D' => 0),
            array('A' => 0, 'B' => 0, 'C' => 0, 'D' => 0),
            array('A' => 1, 'B' => 0, 'C' => 1, 'D' => 0),
            array('A' => 1, 'B' => 0, 'C' => 1, 'D' => 1),
            array('A' => 1, 'B' => 1, 'C' => 1, 'D' => 1)
        );
        foreach ($correctanswers as $correctanswer) {
            $postdata = $mc->prepare_simulated_post_data($correctanswer);
            $simulatedreponse = $mc->get_student_response_values_for_simulation($postdata);
            $this->assertEqualsCanonicalizing($correctanswer, $simulatedreponse);
        }
    }

    /**
     * test_get_question_definition_for_external_rendering
     */
    public function test_get_question_definition_for_external_rendering(): void {
        $question = \test_question_maker::make_a_multichoice_multi_question();
        $question->start_attempt(new question_attempt_step(), 1);
        $qa = \test_question_maker::get_a_qa($question);
        $displayoptions = new question_display_options();

        $options = $question->get_question_definition_for_external_rendering($qa, $displayoptions);
        $this->assertEquals(1, $options['shuffleanswers']);
        $this->assertEquals('abc', $options['answernumbering']);
        $this->assertEquals(0, $options['showstandardinstruction']);
        $this->assertEquals(1, $options['shuffleanswers']);
    }
}
