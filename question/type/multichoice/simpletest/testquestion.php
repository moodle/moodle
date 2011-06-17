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
 * Unit tests for the multiple choice question definition classes.
 *
 * @package    qtype
 * @subpackage multichoice
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/simpletest/helpers.php');


/**
 * Unit tests for the multiple choice, multiple response question definition class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multichoice_single_question_test extends UnitTestCase {

    public function test_get_expected_data() {
        $question = test_question_maker::make_a_multichoice_single_question();
        $this->assertEqual(array('answer' => PARAM_INT), $question->get_expected_data());
    }

    public function test_is_complete_response() {
        $question = test_question_maker::make_a_multichoice_single_question();

        $this->assertFalse($question->is_complete_response(array()));
        $this->assertTrue($question->is_complete_response(array('answer' => '0')));
        $this->assertTrue($question->is_complete_response(array('answer' => '2')));
    }

    public function test_is_gradable_response() {
        $question = test_question_maker::make_a_multichoice_single_question();

        $this->assertFalse($question->is_gradable_response(array()));
        $this->assertTrue($question->is_gradable_response(array('answer' => '0')));
        $this->assertTrue($question->is_gradable_response(array('answer' => '2')));
    }

    public function test_grading() {
        $question = test_question_maker::make_a_multichoice_single_question();
        $question->shuffleanswers = false;
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEqual(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => 0)));
        $this->assertEqual(array(-0.3333333, question_state::$gradedwrong),
                $question->grade_response(array('answer' => 1)));
        $this->assertEqual(array(-0.3333333, question_state::$gradedwrong),
                $question->grade_response(array('answer' => 2)));
    }

    public function test_grading_rounding_three_right() {
        question_bank::load_question_definition_classes('multichoice');
        $mc = new qtype_multichoice_multi_question();
        test_question_maker::initialise_a_question($mc);
        $mc->name = 'Odd numbers';
        $mc->questiontext = 'Which are the odd numbers?';
        $mc->generalfeedback = '1, 3 and 5 are the odd numbers.';
        $mc->qtype = question_bank::get_qtype('multichoice');

        $mc->shuffleanswers = 0;
        $mc->answernumbering = 'abc';

        test_question_maker::set_standard_combined_feedback_fields($mc);

        $mc->answers = array(
            11 => new question_answer(11, '1', 0.3333333, '', FORMAT_HTML),
            12 => new question_answer(12, '2', -1, '', FORMAT_HTML),
            13 => new question_answer(13, '3', 0.3333333, '', FORMAT_HTML),
            14 => new question_answer(14, '4', -1, '', FORMAT_HTML),
            15 => new question_answer(15, '5', 0.3333333, '', FORMAT_HTML),
            16 => new question_answer(16, '6', -1, '', FORMAT_HTML),
        );

        $mc->start_attempt(new question_attempt_step(), 1);

        list($grade, $state) = $mc->grade_response(
                array('choice0' => 1, 'choice2' => 1, 'choice4' => 1));
        $this->assertWithinMargin(1, $grade, 0.000001);
        $this->assertEqual(question_state::$gradedright, $state);
    }

    public function test_get_correct_response() {
        $question = test_question_maker::make_a_multichoice_single_question();
        $question->shuffleanswers = false;
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEqual(array('answer' => 0),
                $question->get_correct_response());
    }

    public function test_summarise_response() {
        $mc = test_question_maker::make_a_multichoice_single_question();
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        $summary = $mc->summarise_response(array('answer' => 0),
                test_question_maker::get_a_qa($mc));

        $this->assertEqual('A', $summary);
    }

    public function test_classify_response() {
        $mc = test_question_maker::make_a_multichoice_single_question();
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        $this->assertEqual(array(
                $mc->id => new question_classified_response(14, 'B', -0.3333333),
                ), $mc->classify_response(array('answer' => 1)));

        $this->assertEqual(array(
                $mc->id => question_classified_response::no_response(),
            ), $mc->classify_response(array()));
    }

    public function test_make_html_inline() {
        $mc = test_question_maker::make_a_multichoice_single_question();
        $this->assertEqual('Frog', $mc->make_html_inline('<p>Frog</p>'));
        $this->assertEqual('Frog<br />Toad', $mc->make_html_inline("<p>Frog</p>\n<p>Toad</p>"));
        $this->assertEqual('<img src="http://example.com/pic.png" alt="Graph" />',
                $mc->make_html_inline(
                    '<p><img src="http://example.com/pic.png" alt="Graph" /></p>'));
        $this->assertEqual("Frog<br />XXX <img src='http://example.com/pic.png' alt='Graph' />",
                $mc->make_html_inline(" <p> Frog </p> \n\r
                    <p> XXX <img src='http://example.com/pic.png' alt='Graph' /> </p> "));
    }
}


/**
 * Unit tests for the multiple choice, single response question definition class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multichoice_multi_question_test extends UnitTestCase {

    public function test_get_expected_data() {
        $question = test_question_maker::make_a_multichoice_multi_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEqual(array('choice0' => PARAM_BOOL, 'choice1' => PARAM_BOOL,
                'choice2' => PARAM_BOOL, 'choice3' => PARAM_BOOL), $question->get_expected_data());
    }

    public function test_is_complete_response() {
        $question = test_question_maker::make_a_multichoice_multi_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($question->is_complete_response(array()));
        $this->assertFalse($question->is_complete_response(
                array('choice0' => '0', 'choice1' => '0', 'choice2' => '0', 'choice3' => '0')));
        $this->assertTrue($question->is_complete_response(array('choice1' => '1')));
        $this->assertTrue($question->is_complete_response(
                array('choice0' => '1', 'choice1' => '1', 'choice2' => '1', 'choice3' => '1')));
    }

    public function test_is_gradable_response() {
        $question = test_question_maker::make_a_multichoice_multi_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($question->is_gradable_response(array()));
        $this->assertFalse($question->is_gradable_response(
                array('choice0' => '0', 'choice1' => '0', 'choice2' => '0', 'choice3' => '0')));
        $this->assertTrue($question->is_gradable_response(array('choice1' => '1')));
        $this->assertTrue($question->is_gradable_response(
                array('choice0' => '1', 'choice1' => '1', 'choice2' => '1', 'choice3' => '1')));
    }

    public function test_grading() {
        $question = test_question_maker::make_a_multichoice_multi_question();
        $question->shuffleanswers = false;
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEqual(array(1, question_state::$gradedright),
                $question->grade_response(array('choice0' => '1', 'choice2' => '1')));
        $this->assertEqual(array(0.5, question_state::$gradedpartial),
                $question->grade_response(array('choice0' => '1')));
        $this->assertEqual(array(0, question_state::$gradedwrong),
                $question->grade_response(
                        array('choice0' => '1', 'choice1' => '1', 'choice2' => '1')));
        $this->assertEqual(array(0, question_state::$gradedwrong),
                $question->grade_response(array('choice1' => '1')));
    }

    public function test_get_correct_response() {
        $question = test_question_maker::make_a_multichoice_multi_question();
        $question->shuffleanswers = false;
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEqual(array('choice0' => '1', 'choice2' => '1'),
                $question->get_correct_response());
    }

    public function test_get_question_summary() {
        $mc = test_question_maker::make_a_multichoice_single_question();
        $mc->start_attempt(new question_attempt_step(), 1);

        $qsummary = $mc->get_question_summary();

        $this->assertPattern('/' . preg_quote($mc->questiontext) . '/', $qsummary);
        foreach ($mc->answers as $answer) {
            $this->assertPattern('/' . preg_quote($answer->answer) . '/', $qsummary);
        }
    }

    public function test_summarise_response() {
        $mc = test_question_maker::make_a_multichoice_multi_question();
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        $summary = $mc->summarise_response(array('choice1' => 1, 'choice2' => 1),
                test_question_maker::get_a_qa($mc));

        $this->assertEqual('B; C', $summary);
    }

    public function test_classify_response() {
        $mc = test_question_maker::make_a_multichoice_multi_question();
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        $this->assertEqual(array(
                    13 => new question_classified_response(13, 'A', 0.5),
                    14 => new question_classified_response(14, 'B', -1.0),
                ), $mc->classify_response(array('choice0' => 1, 'choice1' => 1)));

        $this->assertEqual(array(), $mc->classify_response(array()));
    }
}
