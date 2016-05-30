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
 * This file contains tests that walks a question through the adaptive
 * behaviour.
 *
 * @package    qbehaviour
 * @subpackage adaptive
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(dirname(__FILE__) . '/../../../engine/lib.php');
require_once(dirname(__FILE__) . '/../../../engine/tests/helpers.php');


/**
 * Unit tests for the adaptive behaviour.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_adaptive_walkthrough_test extends qbehaviour_walkthrough_test_base {
    protected function get_contains_penalty_info_expectation($penalty) {
        $penaltyinfo = get_string('gradingdetailspenalty', 'qbehaviour_adaptive',
                                  format_float($penalty, $this->displayoptions->markdp));
        return new question_pattern_expectation('/'.preg_quote($penaltyinfo, '/').'/');
    }

    protected function get_does_not_contain_penalty_info_expectation() {
        $penaltyinfo = get_string('gradingdetailspenalty', 'qbehaviour_adaptive', 'XXXXX');
        $penaltypattern = '/'.str_replace('XXXXX', '\\w*', preg_quote($penaltyinfo, '/')).'/';
        return new question_no_pattern_expectation($penaltypattern);
    }

    protected function get_contains_total_penalty_expectation($penalty) {
        $penaltyinfo = get_string('gradingdetailspenaltytotal', 'qbehaviour_adaptive',
                                  format_float($penalty, $this->displayoptions->markdp));
        return new question_pattern_expectation('/'.preg_quote($penaltyinfo, '/').'/');
    }

    protected function get_does_not_contain_total_penalty_expectation() {
        $penaltyinfo = get_string('gradingdetailspenaltytotal', 'qbehaviour_adaptive', 'XXXXX');
        $penaltypattern = '/'.str_replace('XXXXX', '\\w*', preg_quote($penaltyinfo, '/')).'/';
        return new question_no_pattern_expectation($penaltypattern);
    }

    protected function get_contains_disregarded_info_expectation() {
        $penaltyinfo = get_string('disregardedwithoutpenalty', 'qbehaviour_adaptive');
        return new question_pattern_expectation('/'.preg_quote($penaltyinfo, '/').'/');
    }

    protected function get_does_not_contain_disregarded_info_expectation() {
        $penaltyinfo = get_string('disregardedwithoutpenalty', 'qbehaviour_adaptive');
        return new question_no_pattern_expectation('/'.preg_quote($penaltyinfo, '/').'/');
    }

    public function test_adaptive_multichoice() {

        // Create a multiple choice, single response question.
        $mc = test_question_maker::make_a_multichoice_single_question();
        $mc->penalty = 0.3333333;
        $this->start_attempt_at_question($mc, 'adaptive', 3);

        $rightindex = $this->get_mc_right_answer_index($mc);
        $wrongindex = ($rightindex + 1) % 3;

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_question_text_expectation($mc),
                $this->get_contains_mc_radio_expectation(0, true, false),
                $this->get_contains_mc_radio_expectation(1, true, false),
                $this->get_contains_mc_radio_expectation(2, true, false),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation());

        // Process a submit.
        $this->process_submission(array('answer' => $wrongindex, '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(0);
        $this->check_current_output(
                $this->get_contains_mark_summary(0),
                $this->get_contains_mc_radio_expectation($wrongindex, true, true),
                $this->get_contains_mc_radio_expectation(($wrongindex + 1) % 3, true, false),
                $this->get_contains_mc_radio_expectation(($wrongindex + 2) % 3, true, false),
                $this->get_contains_incorrect_expectation(),
                $this->get_contains_penalty_info_expectation(1.00),
                $this->get_does_not_contain_total_penalty_expectation());
        $this->assertRegExp('/B|C/',
                $this->quba->get_response_summary($this->slot));

        // Process a change of answer to the right one, but not sumbitted.
        $this->process_submission(array('answer' => $rightindex));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(0);
        $this->check_current_output(
                $this->get_contains_mark_summary(0),
                $this->get_contains_mc_radio_expectation($rightindex, true, true),
                $this->get_contains_mc_radio_expectation(($rightindex + 1) % 3, true, false),
                $this->get_contains_mc_radio_expectation(($rightindex + 2) % 3, true, false));
        $this->assertRegExp('/B|C/',
                $this->quba->get_response_summary($this->slot));

        // Now submit the right answer.
        $this->process_submission(array('answer' => $rightindex, '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(3 * (1 - $mc->penalty));
        $this->check_current_output(
                $this->get_contains_mark_summary(3 * (1 - $mc->penalty)),
                $this->get_contains_mc_radio_expectation($rightindex, true, true),
                $this->get_contains_mc_radio_expectation(($rightindex + 1) % 3, true, false),
                $this->get_contains_mc_radio_expectation(($rightindex + 2) % 3, true, false),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation());
        $this->assertEquals('A',
                $this->quba->get_response_summary($this->slot));

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3 * (1 - $mc->penalty));
        $this->check_current_output(
                $this->get_contains_mark_summary(3 * (1 - $mc->penalty)),
                $this->get_contains_mc_radio_expectation($rightindex, false, true),
                $this->get_contains_mc_radio_expectation(($rightindex + 1) % 3, false, false),
                $this->get_contains_mc_radio_expectation(($rightindex + 2) % 3, false, false),
                $this->get_contains_correct_expectation());

        // Process a manual comment.
        $this->manual_grade('Not good enough!', 1, FORMAT_HTML);

        // Verify.
        $this->check_current_state(question_state::$mangrpartial);
        $this->check_current_mark(1);
        $this->check_current_output(
                $this->get_contains_mark_summary(1),
                new question_pattern_expectation('/' . preg_quote('Not good enough!', '/') . '/'));

        // Now change the correct answer to the question, and regrade.
        $mc->answers[13]->fraction = -0.33333333;
        $mc->answers[14]->fraction = 1; // We don't know which "wrong" index we chose above!
        $mc->answers[15]->fraction = 1; // Therefore, treat answers B and C with the same score.
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$mangrpartial);
        $this->check_current_mark(1);
        $this->check_current_output(
                $this->get_contains_mark_summary(1),
                $this->get_contains_partcorrect_expectation());

        $autogradedstep = $this->get_step($this->get_step_count() - 2);
        $this->assertEquals($autogradedstep->get_fraction(), 1, '', 0.0000001);
    }

    public function test_adaptive_multichoice2() {

        // Create a multiple choice, multiple response question.
        $mc = test_question_maker::make_a_multichoice_multi_question();
        $mc->penalty = 0.3333333;
        $mc->shuffleanswers = 0;
        $this->start_attempt_at_question($mc, 'adaptive', 2);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_question_text_expectation($mc),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation());

        // Process a submit.
        $this->process_submission(array('choice0' => 1, 'choice2' => 1, '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(2);
        $this->check_current_output(
                $this->get_contains_mark_summary(2),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation());

        // Save the same correct answer again. Should not do anything.
        $numsteps = $this->get_step_count();
        $this->process_submission(array('choice0' => 1, 'choice2' => 1));

        // Verify.
        $this->check_step_count($numsteps);
        $this->check_current_mark(2);
        $this->check_current_state(question_state::$complete);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_step_count($numsteps + 1);
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(2);
        $this->check_current_output(
                $this->get_contains_mark_summary(2),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_correct_expectation());
    }

    public function test_adaptive_shortanswer_partially_right() {

        // Create a short answer question.
        $sa = test_question_maker::make_question('shortanswer');
        $this->start_attempt_at_question($sa, 'adaptive');

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_does_not_contain_text_input_with_class('answer', 'correct');
        $this->check_output_does_not_contain_text_input_with_class('answer', 'partiallycorrect');
        $this->check_output_does_not_contain_text_input_with_class('answer', 'incorrect');
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation());

        // Submit a partially correct answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'toad'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(0.8);
        $this->render();
        $this->check_output_contains_text_input_with_class('answer', 'partiallycorrect');
        $this->check_current_output(
                $this->get_contains_mark_summary(0.8),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_partcorrect_expectation(),
                $this->get_contains_penalty_info_expectation(0.33),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Submit an incorrect answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'bumblebee'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(0.8);
        $this->render();
        $this->check_output_contains_text_input_with_class('answer', 'incorrect');
        $this->check_current_output(
                $this->get_contains_mark_summary(0.8),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_incorrect_expectation(),
                $this->get_contains_penalty_info_expectation(0.33),
                $this->get_contains_total_penalty_expectation(0.67),
                $this->get_does_not_contain_validation_error_expectation());

        // Submit a correct answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'frog'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(0.8);
        $this->render();
        $this->check_output_contains_text_input_with_class('answer', 'correct');
        $this->check_current_output(
                $this->get_contains_mark_summary(0.8),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(0.8);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.8),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_validation_error_expectation());
    }

    public function test_adaptive_shortanswer_wrong_right_wrong() {

        // Create a short answer question.
        $sa = test_question_maker::make_question('shortanswer');
        $this->start_attempt_at_question($sa, 'adaptive', 6);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation());

        // Submit a wrong answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'hippopotamus'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(0);
        $this->check_current_output(
                $this->get_contains_mark_summary(0),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_incorrect_expectation(),
                $this->get_contains_penalty_info_expectation(2.00),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Submit the same wrong answer again. Nothing should change.
        $this->process_submission(array('-submit' => 1, 'answer' => 'hippopotamus'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(0);
        $this->check_current_output(
                $this->get_contains_mark_summary(0),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_incorrect_expectation(),
                $this->get_contains_penalty_info_expectation(2.00),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Submit a correct answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'frog'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(4.00);
        $this->check_current_output(
                $this->get_contains_mark_summary(4.00),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Submit another incorrect answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'bumblebee'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(4.00);
        $this->check_current_output(
                $this->get_contains_mark_summary(4.00),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_incorrect_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(4.00);
        $this->check_current_output(
                $this->get_contains_mark_summary(4.00),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_incorrect_expectation(),
                $this->get_does_not_contain_validation_error_expectation());
    }

    public function test_adaptive_shortanswer_invalid_after_complete() {

        // Create a short answer question.
        $sa = test_question_maker::make_question('shortanswer');
        $this->start_attempt_at_question($sa, 'adaptive');

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation());

        // Submit a wrong answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'hippopotamus'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(0);
        $this->check_current_output(
                $this->get_contains_mark_summary(0),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_incorrect_expectation(),
                $this->get_contains_penalty_info_expectation(0.33),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Submit a correct answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'frog'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(0.66666667);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.67),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Submit an empty answer.
        $this->process_submission(array('-submit' => 1, 'answer' => ''));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(0.66666667);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.67),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_contains_validation_error_expectation());

        // Submit another wrong answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'bumblebee'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(0.66666667);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.67),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_incorrect_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(0.66666667);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.67),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_incorrect_expectation(),
                $this->get_does_not_contain_validation_error_expectation());
    }

    public function test_adaptive_shortanswer_zero_penalty() {

        // Create a short answer question.
        $sa = test_question_maker::make_question('shortanswer');
        // Disable penalties for this question.
        $sa->penalty = 0;
        $this->start_attempt_at_question($sa, 'adaptive');

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation());

        // Submit a wrong answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'hippopotamus'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(0);
        $this->check_current_output(
                $this->get_contains_mark_summary(0),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_incorrect_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Submit a correct answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'frog'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(1.0);
        $this->check_current_output(
                $this->get_contains_mark_summary(1.0),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(1.0);
        $this->check_current_output(
                $this->get_contains_mark_summary(1.0),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_validation_error_expectation());
    }

    public function test_adaptive_shortanswer_try_to_submit_blank() {

        // Create a short answer question with correct answer true.
        $sa = test_question_maker::make_question('shortanswer');
        $this->start_attempt_at_question($sa, 'adaptive');

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation());

        // Submit with blank answer.
        $this->process_submission(array('-submit' => 1, 'answer' => ''));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_contains_validation_error_expectation(),
                $this->get_contains_disregarded_info_expectation());
        $this->assertNull($this->quba->get_response_summary($this->slot));

        // Now get it wrong.
        $this->process_submission(array('-submit' => 1, 'answer' => 'toad'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(0.8);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.8),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_partcorrect_expectation(),
                $this->get_contains_penalty_info_expectation(0.33),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Now submit blank again.
        $this->process_submission(array('-submit' => 1, 'answer' => ''));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(0.8);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.8),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_contains_validation_error_expectation());
    }

    public function test_adaptive_numerical() {

        // Create a numerical question.
        $sa = test_question_maker::make_question('numerical', 'pi');
        $this->start_attempt_at_question($sa, 'adaptive');

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation());

        // Submit the correct answer.
        $this->process_submission(array('-submit' => 1, 'answer' => '3.14'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(1);
        $this->check_current_output(
                $this->get_contains_mark_summary(1),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Submit an incorrect answer.
        $this->process_submission(array('-submit' => 1, 'answer' => '-5'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(1);
        $this->check_current_output(
                $this->get_contains_mark_summary(1),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_incorrect_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(1);
        $this->check_current_output(
                $this->get_contains_mark_summary(1),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_incorrect_expectation(),
                $this->get_does_not_contain_validation_error_expectation());
    }

    public function test_adaptive_numerical_invalid() {

        // Create a numerical question.
        $numq = test_question_maker::make_question('numerical', 'pi');
        $numq->penalty = 0.1;
        $this->start_attempt_at_question($numq, 'adaptive');

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation());

        // Submit a non-numerical answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'Pi'));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(1),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_contains_validation_error_expectation(),
                $this->get_contains_disregarded_info_expectation());

        // Submit an incorrect answer.
        $this->process_submission(array('-submit' => 1, 'answer' => '-5'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(0);
        $this->check_current_output(
                $this->get_contains_mark_summary(0),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_incorrect_expectation(),
                $this->get_contains_penalty_info_expectation(0.1),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_disregarded_info_expectation());

        // Submit another non-numerical answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'Pi*2'));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(0);
        $this->check_current_output(
                $this->get_contains_mark_summary(0),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_contains_validation_error_expectation(),
                $this->get_contains_disregarded_info_expectation());

        // Submit the correct answer.
        $this->process_submission(array('-submit' => 1, 'answer' => '3.14'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(0.9);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.9),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_disregarded_info_expectation());

        // Submit another non-numerical answer.
        $this->process_submission(array('-submit' => 1, 'answer' => 'Pi/3'));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(0.9);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.9),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_contains_validation_error_expectation(),
                $this->get_contains_disregarded_info_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(0.9);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.9),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_incorrect_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_disregarded_info_expectation());
    }

    public function test_adaptive_multianswer() {

        // Create a multianswer question.
        $q = test_question_maker::make_question('multianswer', 'twosubq');
        // To simplify testing, multichoice subquestion's answers are not shuffled.
        $q->subquestions[2]->shuffleanswers = 0;
        $choices = array('' => '', '0' => 'Bow-wow', '1' => 'Wiggly worm', '2' => 'Pussy-cat');

        $this->start_attempt_at_question($q, 'adaptive', 12);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->assertEquals('adaptive',
                $this->quba->get_question_attempt($this->slot)->get_behaviour_name());
        $this->render();
        $this->check_output_contains_text_input('sub1_answer', '', true);
        $this->check_output_does_not_contain_text_input_with_class('sub1_answer', 'correct');
        $this->check_output_does_not_contain_text_input_with_class('sub1_answer', 'partiallycorrect');
        $this->check_output_does_not_contain_text_input_with_class('sub1_answer', 'incorrect');
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_select_expectation('sub2_answer', $choices, null, true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_feedback_expectation());

        // Submit an invalid response.
        $this->process_submission(array('sub1_answer' => '', 'sub2_answer' => 1, '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_output_contains_text_input('sub1_answer', '', true);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub2_answer', $choices, 1, true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_contains_disregarded_info_expectation());

        // Check that extract responses will return the reset data.
        $prefix = $this->quba->get_field_prefix($this->slot);
        $this->assertEquals(array('sub2_answer' => 1),
                $this->quba->extract_responses($this->slot, array($prefix . 'sub2_answer' => 1)));

        // Submit an incorrect response.
        $this->process_submission(array('sub1_answer' => 'Dog',
                'sub2_answer' => 1, '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(0);
        $this->render();
        $this->check_output_contains_text_input('sub1_answer', 'Dog', true);
        $this->check_output_contains_text_input_with_class('sub1_answer', 'incorrect');
        $this->check_current_output(
                $this->get_contains_mark_summary(0),
                $this->get_contains_select_expectation('sub2_answer', $choices, 1, true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_incorrect_expectation(),
                $this->get_contains_penalty_info_expectation(4.00),
                $this->get_does_not_contain_validation_error_expectation());

        // Submit the right answer.
        $this->process_submission(array('sub1_answer' => 'Owl', 'sub2_answer' => 2, '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(8.00);
        $this->render();
        $this->check_output_contains_text_input('sub1_answer', 'Owl', true);
        $this->check_output_contains_text_input_with_class('sub1_answer', 'correct');
        $this->check_current_output(
                $this->get_contains_select_expectation('sub2_answer', $choices, '2', true),
                $this->get_contains_mark_summary(8.00),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_penalty_info_expectation(),
                $this->get_does_not_contain_total_penalty_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(8.00);
        $this->render();
        $this->check_output_contains_text_input('sub1_answer', 'Owl', false);
        $this->check_output_contains_text_input_with_class('sub1_answer', 'correct');
        $this->check_current_output(
                $this->get_contains_mark_summary(8.00),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_validation_error_expectation());
    }
}
