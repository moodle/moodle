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

use qtype_multichoice_multi_question;
use question_answer;
use question_attempt_step;
use question_bank;
use question_classified_response;
use question_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Unit tests for the multiple choice, single response question definition class.
 *
 * @package   qtype_multichoice
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qtype_multichoice_single_question
 */
class question_single_test extends \advanced_testcase {

    public function test_get_expected_data(): void {
        $question = \test_question_maker::make_a_multichoice_single_question();
        $this->assertEquals(array('answer' => PARAM_INT), $question->get_expected_data());
    }

    public function test_is_complete_response(): void {
        $question = \test_question_maker::make_a_multichoice_single_question();

        $this->assertFalse($question->is_complete_response(array()));
        $this->assertTrue($question->is_complete_response(array('answer' => '0')));
        $this->assertTrue($question->is_complete_response(array('answer' => '2')));
        $this->assertFalse($question->is_complete_response(array('answer' => '-1')));
        $this->assertFalse($question->is_complete_response(array('answer' => -1)));
    }

    public function test_is_gradable_response(): void {
        $question = \test_question_maker::make_a_multichoice_single_question();

        $this->assertFalse($question->is_gradable_response(array()));
        $this->assertTrue($question->is_gradable_response(array('answer' => '0')));
        $this->assertTrue($question->is_gradable_response(array('answer' => '2')));
        $this->assertFalse($question->is_gradable_response(array('answer' => '-1')));
    }

    public function test_is_same_response(): void {
        $question = \test_question_maker::make_a_multichoice_single_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertTrue($question->is_same_response(
                array(),
                array()));

        $this->assertFalse($question->is_same_response(
                array(),
                array('answer' => '0')));

        $this->assertTrue($question->is_same_response(
                array('answer' => '0'),
                array('answer' => '0')));

        $this->assertFalse($question->is_same_response(
                array('answer' => '0'),
                array('answer' => '1')));

        $this->assertTrue($question->is_same_response(
                array('answer' => '2'),
                array('answer' => '2')));

        $this->assertFalse($question->is_same_response(
                array('answer' => '0'),
                array('answer' => '-1')));

        $this->assertFalse($question->is_same_response(
                array('answer' => '-1'),
                array('answer' => '0')));

        $this->assertTrue($question->is_same_response(
                array('answer' => '-1'),
                array('answer' => '-1')));

        $this->assertTrue($question->is_same_response(
                array(),
                array('answer' => '-1')));
    }

    public function test_grading(): void {
        $question = \test_question_maker::make_a_multichoice_single_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response($question->prepare_simulated_post_data(array('answer' => 'A'))));
        $this->assertEquals(array(-0.3333333, question_state::$gradedwrong),
                $question->grade_response($question->prepare_simulated_post_data(array('answer' => 'B'))));
        $this->assertEquals(array(-0.3333333, question_state::$gradedwrong),
                $question->grade_response($question->prepare_simulated_post_data(array('answer' => 'C'))));
    }

    public function test_grading_rounding_three_right(): void {
        question_bank::load_question_definition_classes('multichoice');
        $mc = new qtype_multichoice_multi_question();
        \test_question_maker::initialise_a_question($mc);
        $mc->name = 'Odd numbers';
        $mc->questiontext = 'Which are the odd numbers?';
        $mc->generalfeedback = '1, 3 and 5 are the odd numbers.';
        $mc->qtype = question_bank::get_qtype('multichoice');

        $mc->answernumbering = 'abc';
        $mc->showstandardinstruction = 0;

        \test_question_maker::set_standard_combined_feedback_fields($mc);

        $mc->answers = array(
            11 => new question_answer(11, '1', 0.3333333, '', FORMAT_HTML),
            12 => new question_answer(12, '2', -1, '', FORMAT_HTML),
            13 => new question_answer(13, '3', 0.3333333, '', FORMAT_HTML),
            14 => new question_answer(14, '4', -1, '', FORMAT_HTML),
            15 => new question_answer(15, '5', 0.3333333, '', FORMAT_HTML),
            16 => new question_answer(16, '6', -1, '', FORMAT_HTML),
        );

        $mc->start_attempt(new question_attempt_step(), 1);

        list($grade, $state) = $mc->grade_response($mc->prepare_simulated_post_data(array('1' => '1', '3' => '1', '5' => '1')));
        $this->assertEqualsWithDelta(1, $grade, 0.000001);
        $this->assertEquals(question_state::$gradedright, $state);
    }

    public function test_get_correct_response(): void {
        $question = \test_question_maker::make_a_multichoice_single_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals($question->prepare_simulated_post_data(array('answer' => 'A')), $question->get_correct_response());
    }

    public function test_summarise_response(): void {
        $mc = \test_question_maker::make_a_multichoice_single_question();
        $mc->start_attempt(new question_attempt_step(), 1);

        $summary = $mc->summarise_response($mc->prepare_simulated_post_data(array('answer' => 'A')),
                                            \test_question_maker::get_a_qa($mc));

        $this->assertEquals('A', $summary);

        $this->assertNull($mc->summarise_response([]));
        $this->assertNull($mc->summarise_response(['answer' => '-1']));
    }

    public function test_un_summarise_response(): void {
        $mc = \test_question_maker::make_a_multichoice_single_question();
        $mc->shuffleanswers = false;
        $mc->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(['answer' => '1'], $mc->un_summarise_response('B'));

        $this->assertEquals([], $mc->un_summarise_response(''));
    }

    public function test_classify_response(): void {
        $mc = \test_question_maker::make_a_multichoice_single_question();
        $mc->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array($mc->id => new question_classified_response(14, 'B', -0.3333333)),
                            $mc->classify_response($mc->prepare_simulated_post_data(array('answer' => 'B'))));

        $this->assertEquals(array(
                $mc->id => question_classified_response::no_response(),
        ), $mc->classify_response(array()));

        $this->assertEquals(array(
                $mc->id => question_classified_response::no_response(),
        ), $mc->classify_response(array('answer' => '-1')));
    }

    public function test_make_html_inline(): void {
        $mc = \test_question_maker::make_a_multichoice_single_question();
        $this->assertEquals('Frog', $mc->make_html_inline('<p>Frog</p>'));
        $this->assertEquals('Frog<br />Toad', $mc->make_html_inline("<p>Frog</p>\n<p>Toad</p>"));
        $this->assertEquals('<img src="http://example.com/pic.png" alt="Graph" />',
                $mc->make_html_inline(
                    '<p><img src="http://example.com/pic.png" alt="Graph" /></p>'));
        $this->assertEquals("Frog<br />XXX <img src='http://example.com/pic.png' alt='Graph' />",
                $mc->make_html_inline(" <p> Frog </p> \n\r
                    <p> XXX <img src='http://example.com/pic.png' alt='Graph' /> </p> "));
        $this->assertEquals('Frog', $mc->make_html_inline('<p>Frog</p><p></p>'));
        $this->assertEquals('Frog<br />†', $mc->make_html_inline('<p>Frog</p><p>†</p>'));
    }

    public function test_simulated_post_data(): void {
        $mc = \test_question_maker::make_a_multichoice_single_question();
        $mc->shuffleanswers = false;
        $mc->answers[13]->answer = '<p>A</p>';
        $mc->answers[14]->answer = '<p>B</p>';
        $mc->answers[15]->answer = '<p>C</p>';
        $mc->start_attempt(new question_attempt_step(), 1);

        $originalresponse = array('answer' => 1);

        $simulated = $mc->get_student_response_values_for_simulation($originalresponse);
        $this->assertEquals(array('answer' => 'B'), $simulated);

        $reconstucted = $mc->prepare_simulated_post_data($simulated);
        $this->assertEquals($originalresponse, $reconstucted);
    }

    public function test_validate_can_regrade_with_other_version_bad(): void {
        $mc = \test_question_maker::make_a_multichoice_single_question();

        $newmc = clone($mc);
        $newmc->answers = array(
            23 => new question_answer(13, 'A', 1, 'A is right', FORMAT_HTML),
            24 => new question_answer(14, 'B', -0.3333333, 'B is wrong', FORMAT_HTML),
        );

        $this->assertEquals(get_string('regradeissuenumchoiceschanged', 'qtype_multichoice'),
                $newmc->validate_can_regrade_with_other_version($mc));
    }

    public function test_validate_can_regrade_with_other_version_ok(): void {
        $mc = \test_question_maker::make_a_multichoice_single_question();

        $newmc = clone($mc);
        $newmc->answers = array(
            23 => new question_answer(13, 'A', 1, 'A is right', FORMAT_HTML),
            24 => new question_answer(14, 'B', -0.3333333, 'B is wrong', FORMAT_HTML),
            25 => new question_answer(15, 'C', -0.3333333, 'C is wrong', FORMAT_HTML),
        );

        $this->assertNull($newmc->validate_can_regrade_with_other_version($mc));
    }

    public function test_update_attempt_state_date_from_old_version_bad(): void {
        $mc = \test_question_maker::make_a_multichoice_single_question();

        $newmc = clone($mc);
        $newmc->answers = array(
            23 => new question_answer(13, 'A', 1, 'A is right', FORMAT_HTML),
            24 => new question_answer(14, 'B', -0.3333333, 'B is wrong', FORMAT_HTML),
        );

        $oldstep = new question_attempt_step();
        $oldstep->set_qt_var('_order', '14,13,15');
        $this->expectExceptionMessage(get_string('regradeissuenumchoiceschanged', 'qtype_multichoice'));
        $newmc->update_attempt_state_data_for_new_version($oldstep, $mc);
    }

    public function test_update_attempt_state_date_from_old_version_ok(): void {
        $mc = \test_question_maker::make_a_multichoice_single_question();

        $newmc = clone($mc);
        $newmc->answers = array(
            23 => new question_answer(13, 'A', 1, 'A is right', FORMAT_HTML),
            24 => new question_answer(14, 'B', -0.3333333, 'B is wrong', FORMAT_HTML),
            25 => new question_answer(15, 'C', -0.3333333, 'C is wrong', FORMAT_HTML),
        );

        $oldstep = new question_attempt_step();
        $oldstep->set_qt_var('_order', '14,13,15');
        $this->assertEquals(['_order' => '24,23,25'],
                $newmc->update_attempt_state_data_for_new_version($oldstep, $mc));
    }
}
