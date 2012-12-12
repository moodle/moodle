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
 * This file contains overall tests of multianswer questions.
 *
 * @package    qtype
 * @subpackage multianswer
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the multianswer question type.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_walkthrough_test extends qbehaviour_walkthrough_test_base {

    protected function get_contains_subq_status(question_state $state) {
        return new question_pattern_expectation('~' .
                preg_quote($state->default_string(true), '~') . '<br />~');
    }

    public function test_deferred_feedback() {

        // Create a multianswer question.
        $q = test_question_maker::make_question('multianswer', 'fourmc');
        $this->start_attempt_at_question($q, 'deferredfeedback', 4);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Save in incomplete answer.
        $this->process_submission(array('sub1_answer' => '1', 'sub2_answer' => '',
                'sub3_answer' => '', 'sub4_answer' => ''));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation()); // TODO, really, it should. See MDL-32049.

        // Save a partially correct answer.
        $this->process_submission(array('sub1_answer' => '1', 'sub2_answer' => '1',
                'sub3_answer' => '1', 'sub4_answer' => '1'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Now submit all and finish.
        $this->process_submission(array('-finish' => 1));

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(2);
        $this->check_current_output(
                $this->get_contains_mark_summary(2),
                $this->get_contains_partcorrect_expectation(),
                $this->get_does_not_contain_validation_error_expectation());
    }

    public function test_deferred_feedback_numericalzero_not_answered() {
        // Tests the situation found in MDL-35370.

        // Create a multianswer question with one numerical subquestion, right answer zero.
        $q = test_question_maker::make_question('multianswer', 'numericalzero');
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Now submit all and finish.
        $this->process_submission(array('-finish' => 1));

        // Verify.
        $this->check_current_state(question_state::$gaveup);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                new question_pattern_expectation('~<input[^>]* class="incorrect" [^>]*/>~'),
                $this->get_contains_subq_status(question_state::$gaveup),
                $this->get_does_not_contain_validation_error_expectation());
    }

    public function test_deferred_feedback_numericalzero_0_answer() {
        // Tests the situation found in MDL-35370.

        // Create a multianswer question with one numerical subquestion, right answer zero.
        $q = test_question_maker::make_question('multianswer', 'numericalzero');
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Save a the correct answer.
        $this->process_submission(array('sub1_answer' => '0'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Now submit all and finish.
        $this->process_submission(array('-finish' => 1));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(1);
        $this->check_current_output(
                $this->get_contains_mark_summary(1),
                $this->get_contains_correct_expectation(),
                $this->get_contains_subq_status(question_state::$gradedright),
                $this->get_does_not_contain_validation_error_expectation());
    }

    public function test_deferred_feedback_numericalzero_0_wrong() {
        // Tests the situation found in MDL-35370.

        // Create a multianswer question with one numerical subquestion, right answer zero.
        $q = test_question_maker::make_question('multianswer', 'numericalzero');
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Save a the correct answer.
        $this->process_submission(array('sub1_answer' => '42'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Now submit all and finish.
        $this->process_submission(array('-finish' => 1));

        // Verify.
        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(0);
        $this->check_current_output(
                $this->get_contains_mark_summary(0),
                $this->get_contains_incorrect_expectation(),
                $this->get_contains_subq_status(question_state::$gradedwrong),
                $this->get_does_not_contain_validation_error_expectation());
    }

    public function test_interactive_feedback() {

        // Create a multianswer question.
        $q = test_question_maker::make_question('multianswer', 'fourmc');
        $q->hints = array(
            new question_hint_with_parts(11, 'This is the first hint.', FORMAT_HTML, false, true),
            new question_hint_with_parts(12, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $choices = array('' => '', '0' => 'Califormia', '1' => 'Arizona');

        $this->start_attempt_at_question($q, 'interactive', 4);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        // TODO change to interactivecountback after MDL-36955 is integrated.
        $this->assertEquals('interactive',
                $this->quba->get_question_attempt($this->slot)->get_behaviour_name());
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_select_expectation('sub1_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub2_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub3_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub4_answer', $choices, null, true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation());

        // Submit a completely wrong response.
        $this->process_submission(array('sub1_answer' => '1', 'sub2_answer' => '0',
                'sub3_answer' => '1', 'sub4_answer' => '0', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub1_answer', $choices, 1, false),
                $this->get_contains_select_expectation('sub2_answer', $choices, 0, false),
                $this->get_contains_select_expectation('sub3_answer', $choices, 1, false),
                $this->get_contains_select_expectation('sub4_answer', $choices, 0, false),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub1_answer', ''),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub2_answer', ''),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub3_answer', ''),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub4_answer', ''),
                $this->get_contains_submit_button_expectation(false),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_contains_hint_expectation('This is the first hint.'));

        // Check that, if we review in this state, the try again button is disabled.
        $displayoptions = new question_display_options();
        $displayoptions->readonly = true;
        $html = $this->quba->render_question($this->slot, $displayoptions);
        $this->assert($this->get_contains_try_again_button_expectation(false), $html);

        // Try again.
        $this->process_submission(array('sub1_answer' => '',
                'sub2_answer' => '', 'sub3_answer' => '',
                'sub4_answer' => '', '-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub1_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub2_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub3_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub4_answer', $choices, null, true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(2),
                $this->get_no_hint_visible_expectation());

        // Submit a partially wrong response.
        $this->process_submission(array('sub1_answer' => '1', 'sub2_answer' => '1',
                'sub3_answer' => '1', 'sub4_answer' => '1', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub1_answer', $choices, 1, false),
                $this->get_contains_select_expectation('sub2_answer', $choices, 1, false),
                $this->get_contains_select_expectation('sub3_answer', $choices, 1, false),
                $this->get_contains_select_expectation('sub4_answer', $choices, 1, false),
                $this->get_contains_num_parts_correct(2),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub1_answer', ''),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub2_answer', '1'),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub3_answer', ''),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub4_answer', '1'),
                $this->get_contains_submit_button_expectation(false),
                $this->get_contains_hint_expectation('This is the second hint.'));

        // Try again.
        $this->process_submission(array('sub1_answer' => '',
                'sub2_answer' => '1', 'sub3_answer' => '',
                'sub4_answer' => '1', '-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub1_answer', $choices, '', true),
                $this->get_contains_select_expectation('sub2_answer', $choices, '1', true),
                $this->get_contains_select_expectation('sub3_answer', $choices, '', true),
                $this->get_contains_select_expectation('sub4_answer', $choices, '1', true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(1),
                $this->get_no_hint_visible_expectation());
    }
}
