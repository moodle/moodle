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
 * This file contains tests that walks a question through the deferred feedback
 * behaviour.
 *
 * @package    qbehaviour
 * @subpackage deferredfeedback
 * @copyright  2009 The Open University
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../../../engine/lib.php');
require_once(__DIR__ . '/../../../engine/tests/helpers.php');


/**
 * Unit tests for the deferred feedback behaviour.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_deferredfeedback_walkthrough_test extends qbehaviour_walkthrough_test_base {
    public function test_deferredfeedback_feedback_truefalse() {

        // Create a true-false question with correct answer true.
        $tf = test_question_maker::make_question('truefalse', 'true');
        $this->start_attempt_at_question($tf, 'deferredfeedback', 2);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_output_contains_lang_string('notyetanswered', 'question');
        $this->check_current_mark(null);
        $this->check_current_output($this->get_contains_question_text_expectation($tf),
                $this->get_does_not_contain_feedback_expectation());
        $this->assertEquals(get_string('true', 'qtype_truefalse'),
                $this->quba->get_right_answer_summary($this->slot));
        $this->assertRegExp('/' . preg_quote($tf->questiontext, '/') . '/',
                $this->quba->get_question_summary($this->slot));
        $this->assertNull($this->quba->get_response_summary($this->slot));

        // Process a true answer and check the expected result.
        $this->process_submission(array('answer' => 1));

        $this->check_current_state(question_state::$complete);
        $this->check_output_contains_lang_string('answersaved', 'question');
        $this->check_current_mark(null);
        $this->check_current_output($this->get_contains_tf_true_radio_expectation(true, true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation());

        // Process the same data again, check it does not create a new step.
        $numsteps = $this->get_step_count();
        $this->process_submission(array('answer' => 1));
        $this->check_step_count($numsteps);

        // Process different data, check it creates a new step.
        $this->process_submission(array('answer' => 0));
        $this->check_step_count($numsteps + 1);
        $this->check_current_state(question_state::$complete);

        // Change back, check it creates a new step.
        $this->process_submission(array('answer' => 1));
        $this->check_step_count($numsteps + 2);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(2);
        $this->check_current_output($this->get_contains_correct_expectation(),
                $this->get_contains_tf_true_radio_expectation(false, true),
                new question_pattern_expectation('/class="r0 correct"/'));
        $this->assertEquals(get_string('true', 'qtype_truefalse'),
                $this->quba->get_response_summary($this->slot));

        // Process a manual comment.
        $this->manual_grade('Not good enough!', 1, FORMAT_HTML);

        $this->check_current_state(question_state::$mangrpartial);
        $this->check_current_mark(1);
        $this->check_current_output(
                new question_pattern_expectation('/' . preg_quote('Not good enough!', '/') . '/'));

        // Now change the correct answer to the question, and regrade.
        $tf->rightanswer = false;
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$mangrpartial);
        $this->check_current_mark(1);

        $autogradedstep = $this->get_step($this->get_step_count() - 2);
        $this->assertEqualsWithDelta($autogradedstep->get_fraction(), 0, 0.0000001);
    }

    public function test_deferredfeedback_feedback_multichoice_single() {

        // Create a true-false question with correct answer true.
        $mc = test_question_maker::make_a_multichoice_single_question();
        $this->start_attempt_at_question($mc, 'deferredfeedback', 3);

        // Start a deferred feedback attempt and add the question to it.
        $rightindex = $this->get_mc_right_answer_index($mc);

        $this->check_current_state(question_state::$todo);
        $this->check_output_contains_lang_string('notyetanswered', 'question');
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_question_text_expectation($mc),
                $this->get_contains_mc_radio_expectation(0, true, false),
                $this->get_contains_mc_radio_expectation(1, true, false),
                $this->get_contains_mc_radio_expectation(2, true, false),
                $this->get_does_not_contain_feedback_expectation());

        // Process the data extracted for this question.
        $this->process_submission(array('answer' => $rightindex));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_output_contains_lang_string('answersaved', 'question');
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation($rightindex, true, true),
                $this->get_contains_mc_radio_expectation(($rightindex + 1) % 3, true, false),
                $this->get_contains_mc_radio_expectation(($rightindex + 1) % 3, true, false),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation($rightindex, false, true),
                $this->get_contains_correct_expectation());

        // Now change the correct answer to the question, and regrade.
        $mc->answers[13]->fraction = -0.33333333;
        $mc->answers[14]->fraction = 1;
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(-1);
        $this->check_current_output(
                $this->get_contains_incorrect_expectation());
    }

    public function test_deferredfeedback_resume_multichoice_single() {

        // Create a multiple-choice question.
        $mc = test_question_maker::make_a_multichoice_single_question();

        // Attempt it getting it wrong.
        $this->start_attempt_at_question($mc, 'deferredfeedback', 3);
        $rightindex = $this->get_mc_right_answer_index($mc);
        $wrongindex = ($rightindex + 1) % 3;

        $this->check_current_state(question_state::$todo);
        $this->check_output_contains_lang_string('notyetanswered', 'question');
        $this->check_current_mark(null);
        $this->process_submission(array('answer' => $wrongindex));
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(-1);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation($wrongindex, false, true),
                $this->get_contains_incorrect_expectation());

        // Save the old attempt.
        $oldqa = $this->quba->get_question_attempt($this->slot);

        // Reinitialise.
        $this->setUp();
        $this->quba->set_preferred_behaviour('deferredfeedback');
        $this->slot = $this->quba->add_question($mc, 3);
        $this->quba->start_question_based_on($this->slot, $oldqa);

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_output_contains_lang_string('notchanged', 'question');
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation($wrongindex, true, true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_correctness_expectation());

        // Now get it right.
        $this->process_submission(array('answer' => $rightindex));
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation($rightindex, false, true),
                $this->get_contains_correct_expectation());
    }

    public function test_deferredfeedback_resume_multichoice_single_emptyanswer_first() {

        // Create a multiple-choice question.
        $mc = test_question_maker::make_a_multichoice_single_question();

        // Attempt it and submit empty.
        $this->start_attempt_at_question($mc, 'deferredfeedback', 3);
        $rightindex = $this->get_mc_right_answer_index($mc);
        $wrongindex = ($rightindex + 1) % 3;

        $this->check_current_state(question_state::$todo);
        $this->check_output_contains_lang_string('notyetanswered', 'question');
        $this->check_current_mark(null);
        $this->process_submission(array('-submit' => 1));
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gaveup);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation(0, false, false),
                $this->get_contains_mc_radio_expectation(1, false, false),
                $this->get_contains_mc_radio_expectation(2, false, false),
                $this->get_contains_general_feedback_expectation($mc));

        // Save the old attempt.
        $oldqa = $this->quba->get_question_attempt($this->slot);

        // Reinitialise.
        $this->setUp();
        $this->quba->set_preferred_behaviour('deferredfeedback');
        $this->slot = $this->quba->add_question($mc, 3);
        $this->quba->start_question_based_on($this->slot, $oldqa);

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_output_contains_lang_string('notyetanswered', 'question');
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation(0, true, false),
                $this->get_contains_mc_radio_expectation(1, true, false),
                $this->get_contains_mc_radio_expectation(2, true, false),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_correctness_expectation());

        // Now get it wrong.
        $this->process_submission(array('answer' => $wrongindex));
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(-1);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation($wrongindex, false, true),
                $this->get_contains_incorrect_expectation());

        // Save the old attempt.
        $oldqa = $this->quba->get_question_attempt($this->slot);

        // Reinitialise.
        $this->setUp();
        $this->quba->set_preferred_behaviour('deferredfeedback');
        $this->slot = $this->quba->add_question($mc, 3);
        $this->quba->start_question_based_on($this->slot, $oldqa);

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_output_contains_lang_string('notchanged', 'question');
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation($wrongindex, true, true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_correctness_expectation());

        // Now get it right.
        $this->process_submission(array('answer' => $rightindex));
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation($rightindex, false, true),
                $this->get_contains_correct_expectation());
    }
}
