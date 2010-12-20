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
 * @package qbehaviour_adaptive
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../engine/lib.php');
require_once(dirname(__FILE__) . '/../../../engine/simpletest/helpers.php');

class qbehaviour_adaptive_walkthrough_test extends qbehaviour_walkthrough_test_base {
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
                $this->get_contains_incorrect_expectation());
        $this->assertPattern('/B|C/',
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
        $this->assertPattern('/B|C/',
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
                $this->get_contains_correct_expectation());
        $this->assertEqual('A',
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
        $this->manual_grade('Not good enough!', 1);

        // Verify.
        $this->check_current_state(question_state::$mangrpartial);
        $this->check_current_mark(1);
        $this->check_current_output(
                $this->get_contains_mark_summary(1),
                new PatternExpectation('/' . preg_quote('Not good enough!') . '/'));

        // Now change the correct answer to the question, and regrade.
        $mc->answers[13]->fraction = -0.33333333;
        $mc->answers[15]->fraction = 1;
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$mangrpartial);
        $this->check_current_mark(1);
        $this->check_current_output(
                $this->get_contains_mark_summary(1),
                $this->get_contains_partcorrect_expectation());

        $autogradedstep = $this->get_step($this->get_step_count() - 2);
        $this->assertWithinMargin($autogradedstep->get_fraction(), 0, 0.0000001);
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
                $this->get_contains_correct_expectation());

        // Save the same correct answer again. Should no do anything.
        $numsteps = $this->get_step_count();
        $this->process_submission(array('choice0' => 1, 'choice2' => 1));

        // Verify.
        $this->check_step_count($numsteps);
        $this->check_current_state(question_state::$complete);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_step_count($numsteps + 1);
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(2);
        $this->check_current_output(
                $this->get_contains_mark_summary(2),
                $this->get_contains_submit_button_expectation(false),
                $this->get_contains_correct_expectation());
    }

    public function test_adaptive_shortanswer_try_to_submit_blank() {

        // Create a short answer question with correct answer true.
        $sa = test_question_maker::make_a_shortanswer_question();
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
                $this->get_contains_validation_error_expectation());
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
                $this->get_does_not_contain_validation_error_expectation());


        // Now submit blank again.
        $this->process_submission(array('-submit' => 1, 'answer' => ''));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(0.8);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.8),
                $this->get_contains_submit_button_expectation(true),
                $this->get_contains_partcorrect_expectation(),
                $this->get_contains_validation_error_expectation());
    }
}
