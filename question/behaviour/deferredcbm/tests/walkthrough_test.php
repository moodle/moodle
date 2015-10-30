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
 * with certainty base marking behaviour.
 *
 * @package    qbehaviour
 * @subpackage deferredcbm
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(dirname(__FILE__) . '/../../../engine/lib.php');
require_once(dirname(__FILE__) . '/../../../engine/tests/helpers.php');


/**
 * Unit tests for the deferred feedback with certainty base marking behaviour.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_deferredcbm_walkthrough_test extends qbehaviour_walkthrough_test_base {
    public function test_deferred_cbm_truefalse_high_certainty() {

        // Create a true-false question with correct answer true.
        $tf = test_question_maker::make_question('truefalse', 'true');
        $this->start_attempt_at_question($tf, 'deferredcbm', 2);

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_output_contains_lang_string('notyetanswered', 'question');
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_question_text_expectation($tf),
                $this->get_contains_tf_true_radio_expectation(true, false),
                $this->get_contains_tf_false_radio_expectation(true, false),
                $this->get_contains_cbm_radio_expectation(1, true, false),
                $this->get_contains_cbm_radio_expectation(2, true, false),
                $this->get_contains_cbm_radio_expectation(3, true, false),
                $this->get_does_not_contain_feedback_expectation());

        // Process the data extracted for this question.
        $this->process_submission(array('answer' => 1, '-certainty' => 3));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_output_contains_lang_string('answersaved', 'question');
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_tf_true_radio_expectation(true, true),
                $this->get_contains_cbm_radio_expectation(3, true, true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation());

        // Process the same data again, check it does not create a new step.
        $numsteps = $this->get_step_count();
        $this->process_submission(array('answer' => 1, '-certainty' => 3));
        $this->check_step_count($numsteps);

        // Process different data, check it creates a new step.
        $this->process_submission(array('answer' => 1, '-certainty' => 1));
        $this->check_step_count($numsteps + 1);
        $this->check_current_state(question_state::$complete);

        // Change back, check it creates a new step.
        $this->process_submission(array('answer' => 1, '-certainty' => 3));
        $this->check_step_count($numsteps + 2);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(6);
        $this->check_current_output(
                $this->get_contains_tf_true_radio_expectation(false, true),
                $this->get_contains_cbm_radio_expectation(3, false, true),
                $this->get_contains_correct_expectation());

        // Process a manual comment.
        $this->manual_grade('Not good enough!', 5, FORMAT_HTML);

        // Verify.
        $this->check_current_state(question_state::$mangrright);
        $this->check_current_mark(5);
        $this->check_current_output(new question_pattern_expectation('/' .
                preg_quote('Not good enough!', '/') . '/'));

        // Now change the correct answer to the question, and regrade.
        $tf->rightanswer = false;
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$mangrright);
        $this->check_current_mark(5);
        $autogradedstep = $this->get_step($this->get_step_count() - 2);
        $this->assertEquals(-6, $autogradedstep->get_fraction(), '', 0.0000001);
    }

    public function test_deferred_cbm_truefalse_low_certainty() {

        // Create a true-false question with correct answer true.
        $tf = test_question_maker::make_question('truefalse', 'true');
        $this->start_attempt_at_question($tf, 'deferredcbm', 2);

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_output_contains_lang_string('notyetanswered', 'question');
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_contains_cbm_radio_expectation(1, true, false),
                $this->get_does_not_contain_feedback_expectation());

        // Submit ansewer with low certainty.
        $this->process_submission(array('answer' => 1, '-certainty' => 1));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_output_contains_lang_string('answersaved', 'question');
        $this->check_current_mark(null);
        $this->check_current_output($this->get_does_not_contain_correctness_expectation(),
                $this->get_contains_cbm_radio_expectation(1, true, true),
                $this->get_does_not_contain_feedback_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(2);
        $this->check_current_output($this->get_contains_correct_expectation(),
                $this->get_contains_cbm_radio_expectation(1, false, true));
        $this->assertEquals(get_string('true', 'qtype_truefalse') . ' [' .
                question_cbm::get_short_string(question_cbm::LOW) . ']',
                $this->quba->get_response_summary($this->slot));
    }

    public function test_deferred_cbm_truefalse_default_certainty() {

        // Create a true-false question with correct answer true.
        $tf = test_question_maker::make_question('truefalse', 'true');
        $this->start_attempt_at_question($tf, 'deferredcbm', 2);

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_output_contains_lang_string('notyetanswered', 'question');
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_contains_cbm_radio_expectation(1, true, false),
                $this->get_does_not_contain_feedback_expectation());

        // Submit ansewer with low certainty and finish the attempt.
        $this->process_submission(array('answer' => 1));
        $this->quba->finish_all_questions();

        // Verify.
        $qa = $this->quba->get_question_attempt($this->slot);
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(2);
        $this->check_current_output($this->get_contains_correct_expectation(),
                $this->get_contains_cbm_radio_expectation(1, false, false),
                new question_pattern_expectation('/' . preg_quote(
                        get_string('assumingcertainty', 'qbehaviour_deferredcbm',
                        question_cbm::get_string(
                            $qa->get_last_behaviour_var('_assumedcertainty'))), '/') . '/'));
        $this->assertEquals(get_string('true', 'qtype_truefalse'),
                $this->quba->get_response_summary($this->slot));
    }

    public function test_deferredcbm_resume_multichoice_single() {

        // Create a multiple-choice question.
        $mc = test_question_maker::make_a_multichoice_single_question();

        // Attempt it getting it wrong.
        $this->start_attempt_at_question($mc, 'deferredcbm', 1);
        $rightindex = $this->get_mc_right_answer_index($mc);
        $wrongindex = ($rightindex + 1) % 3;
        $this->process_submission(array('answer' => $wrongindex, '-certainty' => 2));
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(-2);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation($wrongindex, false, true),
                $this->get_contains_cbm_radio_expectation(2, false, true),
                $this->get_contains_incorrect_expectation());
        $this->assertEquals('A [' . question_cbm::get_short_string(question_cbm::HIGH) . ']',
                $this->quba->get_right_answer_summary($this->slot));
        $this->assertRegExp('/' . preg_quote($mc->questiontext, '/') . '/',
                $this->quba->get_question_summary($this->slot));
        $this->assertRegExp('/(B|C) \[' . preg_quote(question_cbm::get_short_string(question_cbm::MED), '/') . '\]/',
                $this->quba->get_response_summary($this->slot));

        // Save the old attempt.
        $oldqa = $this->quba->get_question_attempt($this->slot);

        // Reinitialise.
        $this->setUp();
        $this->quba->set_preferred_behaviour('deferredcbm');
        $this->slot = $this->quba->add_question($mc, 1);
        $this->quba->start_question_based_on($this->slot, $oldqa);

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_output_contains_lang_string('notchanged', 'question');
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation($wrongindex, true, true),
                $this->get_contains_cbm_radio_expectation(2, true, true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_correctness_expectation());
        $this->assertEquals('A [' . question_cbm::get_short_string(question_cbm::HIGH) . ']',
                $this->quba->get_right_answer_summary($this->slot));
        $this->assertRegExp('/' . preg_quote($mc->questiontext, '/') . '/',
                $this->quba->get_question_summary($this->slot));
        $this->assertNull($this->quba->get_response_summary($this->slot));

        // Now get it right.
        $this->process_submission(array('answer' => $rightindex, '-certainty' => 3));
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation($rightindex, false, true),
                $this->get_contains_cbm_radio_expectation(question_cbm::HIGH, false, true),
                $this->get_contains_correct_expectation());
        $this->assertRegExp('/(A) \[' . preg_quote(question_cbm::get_short_string(question_cbm::HIGH), '/') . '\]/',
                $this->quba->get_response_summary($this->slot));
    }

    public function test_deferred_cbm_truefalse_no_certainty_feedback_when_not_answered() {

        // Create a true-false question with correct answer true.
        $tf = test_question_maker::make_question('truefalse', 'true');
        $this->start_attempt_at_question($tf, 'deferredcbm', 2);

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_output_contains_lang_string('notyetanswered', 'question');
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_contains_cbm_radio_expectation(1, true, false),
                $this->get_does_not_contain_feedback_expectation());

        // Finish without answering.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gaveup);
        $this->check_current_mark(null);
        $this->check_current_output(
                new question_no_pattern_expectation('/class=\"im-feedback/'));
    }
}
