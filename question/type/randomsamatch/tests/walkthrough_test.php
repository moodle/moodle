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
 * This file contains tests that walks a question through the interactive
 * behaviour.
 *
 * @package   qtype_randomsamatch
 * @copyright 2013 Jean-Michel Vedrine
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the randomsamatch question type.
 *
 * @copyright 2013 Jean-Michel Vedrine
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_randomsamatch_walkthrough_test extends qbehaviour_walkthrough_test_base {

    public function test_deferred_feedback_unanswered() {

        // Create a randomsamatch question.
        $m = test_question_maker::make_question('randomsamatch');
        $this->start_attempt_at_question($m, 'deferredfeedback', 4);

        $choiceorder = $m->get_choice_order();
        $orderforchoice = array_combine(array_values($choiceorder), array_keys($choiceorder));
        $choices = array(0 => get_string('choose') . '...');
        foreach ($choiceorder as $key => $choice) {
            $choices[$key] = $m->choices[$choice];
        }

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, null, true),
                $this->get_contains_select_expectation('sub1', $choices, null, true),
                $this->get_contains_select_expectation('sub2', $choices, null, true),
                $this->get_contains_select_expectation('sub3', $choices, null, true),
                $this->get_contains_question_text_expectation($m),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(1);

        // Save a blank response.
        $this->process_submission(array('sub0' => '0', 'sub1' => '0',
                'sub2' => '0', 'sub3' => '0'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, null, true),
                $this->get_contains_select_expectation('sub1', $choices, null, true),
                $this->get_contains_select_expectation('sub2', $choices, null, true),
                $this->get_contains_select_expectation('sub3', $choices, null, true),
                $this->get_contains_question_text_expectation($m),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(1);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gaveup);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, null, false),
                $this->get_contains_select_expectation('sub1', $choices, null, false),
                $this->get_contains_select_expectation('sub2', $choices, null, false),
                $this->get_contains_select_expectation('sub3', $choices, null, false));
    }

    public function test_deferred_feedback_partial_answer() {

        // Create a randomsamatching question.
        $m = test_question_maker::make_question('randomsamatch');
        $m->shufflestems = false;
        $this->start_attempt_at_question($m, 'deferredfeedback', 4);

        $choiceorder = $m->get_choice_order();
        $orderforchoice = array_combine(array_values($choiceorder), array_keys($choiceorder));
        $choices = array(0 => get_string('choose') . '...');
        foreach ($choiceorder as $key => $choice) {
            $choices[$key] = $m->choices[$choice];
        }

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, null, true),
                $this->get_contains_select_expectation('sub1', $choices, null, true),
                $this->get_contains_select_expectation('sub2', $choices, null, true),
                $this->get_contains_select_expectation('sub3', $choices, null, true),
                $this->get_contains_question_text_expectation($m),
                $this->get_does_not_contain_feedback_expectation());

        // Save a partial response.
        $this->process_submission(array('sub0' => $orderforchoice[13],
                'sub1' => $orderforchoice[16], 'sub2' => '0', 'sub3' => '0'));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, $orderforchoice[13], true),
                $this->get_contains_select_expectation('sub1', $choices, $orderforchoice[16], true),
                $this->get_contains_select_expectation('sub2', $choices, null, true),
                $this->get_contains_select_expectation('sub3', $choices, null, true),
                $this->get_contains_question_text_expectation($m),
                $this->get_does_not_contain_feedback_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(2);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, $orderforchoice[13], false),
                $this->get_contains_select_expectation('sub1', $choices, $orderforchoice[16], false),
                $this->get_contains_select_expectation('sub2', $choices, null, false),
                $this->get_contains_select_expectation('sub3', $choices, null, false),
                $this->get_contains_partcorrect_expectation());
    }

    public function test_interactive_correct_no_submit() {

        // Create a randomsamatching question.
        $m = test_question_maker::make_question('randomsamatch');
        $m->hints = array(
            new question_hint_with_parts(11, 'This is the first hint.', FORMAT_HTML, false, false),
            new question_hint_with_parts(12, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $m->shufflestems = false;
        $this->start_attempt_at_question($m, 'interactive', 4);

        $choiceorder = $m->get_choice_order();
        $orderforchoice = array_combine(array_values($choiceorder), array_keys($choiceorder));
        $choices = array(0 => get_string('choose') . '...');
        foreach ($choiceorder as $key => $choice) {
            $choices[$key] = $m->choices[$choice];
        }

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, null, true),
                $this->get_contains_select_expectation('sub1', $choices, null, true),
                $this->get_contains_select_expectation('sub2', $choices, null, true),
                $this->get_contains_select_expectation('sub3', $choices, null, true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());

        // Save the right answer.
        $this->process_submission(array('sub0' => $orderforchoice[13],
                'sub1' => $orderforchoice[16], 'sub2' => $orderforchoice[16],
                'sub3' => $orderforchoice[13]));

        // Finish the attempt without clicking check.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(4);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, $orderforchoice[13], false),
                $this->get_contains_select_expectation('sub1', $choices, $orderforchoice[16], false),
                $this->get_contains_select_expectation('sub2', $choices, $orderforchoice[16], false),
                $this->get_contains_select_expectation('sub3', $choices, $orderforchoice[13], false),
                $this->get_contains_submit_button_expectation(false),
                $this->get_contains_correct_expectation(),
                $this->get_no_hint_visible_expectation());
    }

    public function test_interactive_partial_no_submit() {

        // Create a randomsamatching question.
        $m = test_question_maker::make_question('randomsamatch');
        $m->hints = array(
            new question_hint_with_parts(11, 'This is the first hint.', FORMAT_HTML, false, false),
            new question_hint_with_parts(12, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $m->shufflestems = false;
        $this->start_attempt_at_question($m, 'interactive', 4);

        $choiceorder = $m->get_choice_order();
        $orderforchoice = array_combine(array_values($choiceorder), array_keys($choiceorder));
        $choices = array(0 => get_string('choose') . '...');
        foreach ($choiceorder as $key => $choice) {
            $choices[$key] = $m->choices[$choice];
        }

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, null, true),
                $this->get_contains_select_expectation('sub1', $choices, null, true),
                $this->get_contains_select_expectation('sub2', $choices, null, true),
                $this->get_contains_select_expectation('sub3', $choices, null, true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());

        // Save the right answer.
        $this->process_submission(array('sub0' => $orderforchoice[13],
                'sub1' => $orderforchoice[16], 'sub2' => $orderforchoice[13],
                'sub3' => '0'));

        // Finish the attempt without clicking check.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(2);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, $orderforchoice[13], false),
                $this->get_contains_select_expectation('sub1', $choices, $orderforchoice[16], false),
                $this->get_contains_select_expectation('sub2', $choices, $orderforchoice[13], false),
                $this->get_contains_select_expectation('sub3', $choices, null, false),
                $this->get_contains_submit_button_expectation(false),
                $this->get_contains_partcorrect_expectation(),
                $this->get_no_hint_visible_expectation());
    }

    public function test_interactive_with_invalid() {

        // Create a randomsamatching question.
        $m = test_question_maker::make_question('randomsamatch');
        $m->hints = array(
            new question_hint_with_parts(11, 'This is the first hint.', FORMAT_HTML, false, false),
            new question_hint_with_parts(12, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $m->shufflestems = false;
        $this->start_attempt_at_question($m, 'interactive', 4);

        $choiceorder = $m->get_choice_order();
        $orderforchoice = array_combine(array_values($choiceorder), array_keys($choiceorder));
        $choices = array(0 => get_string('choose') . '...');
        foreach ($choiceorder as $key => $choice) {
            $choices[$key] = $m->choices[$choice];
        }

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, null, true),
                $this->get_contains_select_expectation('sub1', $choices, null, true),
                $this->get_contains_select_expectation('sub2', $choices, null, true),
                $this->get_contains_select_expectation('sub3', $choices, null, true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());

        // Try to submit an invalid answer.
        $this->process_submission(array('sub0' => '0',
                'sub1' => '0', 'sub2' => '0',
                'sub3' => '0', '-submit' => '1'));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, null, true),
                $this->get_contains_select_expectation('sub1', $choices, null, true),
                $this->get_contains_select_expectation('sub2', $choices, null, true),
                $this->get_contains_select_expectation('sub3', $choices, null, true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_invalid_answer_expectation(),
                $this->get_no_hint_visible_expectation());

        // Now submit the right answer.
        $this->process_submission(array('sub0' => $orderforchoice[13],
                'sub1' => $orderforchoice[16], 'sub2' => $orderforchoice[16],
                'sub3' => $orderforchoice[13], '-submit' => '1'));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(4);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, $orderforchoice[13], false),
                $this->get_contains_select_expectation('sub1', $choices, $orderforchoice[16], false),
                $this->get_contains_select_expectation('sub2', $choices, $orderforchoice[16], false),
                $this->get_contains_select_expectation('sub3', $choices, $orderforchoice[13], false),
                $this->get_contains_submit_button_expectation(false),
                $this->get_contains_correct_expectation(),
                $this->get_no_hint_visible_expectation());
    }

    public function test_randomsamatch_clear_wrong() {

        // Create a randomsamatching question.
        $m = test_question_maker::make_question('randomsamatch');
        $m->hints = array(
            new question_hint_with_parts(11, 'This is the first hint.', FORMAT_HTML, false, true),
            new question_hint_with_parts(12, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $m->shufflestems = false;
        $this->start_attempt_at_question($m, 'interactive', 4);

        $choiceorder = $m->get_choice_order();
        $orderforchoice = array_combine(array_values($choiceorder), array_keys($choiceorder));
        $choices = array(0 => get_string('choose') . '...');
        foreach ($choiceorder as $key => $choice) {
            $choices[$key] = $m->choices[$choice];
        }

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, null, true),
                $this->get_contains_select_expectation('sub1', $choices, null, true),
                $this->get_contains_select_expectation('sub2', $choices, null, true),
                $this->get_contains_select_expectation('sub3', $choices, null, true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());

        // Submit a completely wrong response.
        $this->process_submission(array('sub0' => $orderforchoice[16],
                'sub1' => $orderforchoice[13], 'sub2' => $orderforchoice[13],
                'sub3' => $orderforchoice[16], '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, $orderforchoice[16], false),
                $this->get_contains_select_expectation('sub1', $choices, $orderforchoice[13], false),
                $this->get_contains_select_expectation('sub2', $choices, $orderforchoice[13], false),
                $this->get_contains_select_expectation('sub3', $choices, $orderforchoice[16], false),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub0', '0'),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub1', '0'),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub2', '0'),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub3', '0'),
                $this->get_contains_submit_button_expectation(false),
                $this->get_contains_hint_expectation('This is the first hint.'));

        // Try again.
        $this->process_submission(array('sub0' => 0,
                'sub1' => 0, 'sub2' => 0,
                'sub3' => 0, '-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, null, true),
                $this->get_contains_select_expectation('sub1', $choices, null, true),
                $this->get_contains_select_expectation('sub2', $choices, null, true),
                $this->get_contains_select_expectation('sub3', $choices, null, true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(2),
                $this->get_no_hint_visible_expectation());

        // Submit a partially wrong response.
        $this->process_submission(array('sub0' => $orderforchoice[16],
                'sub1' => $orderforchoice[13], 'sub2' => $orderforchoice[16],
                'sub3' => $orderforchoice[13], '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, $orderforchoice[16], false),
                $this->get_contains_select_expectation('sub1', $choices, $orderforchoice[13], false),
                $this->get_contains_select_expectation('sub2', $choices, $orderforchoice[16], false),
                $this->get_contains_select_expectation('sub3', $choices, $orderforchoice[13], false),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub0', '0'),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub1', '0'),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub2', $orderforchoice[16]),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub3', $orderforchoice[13]),
                $this->get_contains_submit_button_expectation(false),
                $this->get_contains_hint_expectation('This is the second hint.'));

        // Try again.
        $this->process_submission(array('sub0' => 0,
                'sub1' => 0, 'sub2' => $orderforchoice[16],
                'sub3' => $orderforchoice[13], '-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, null, true),
                $this->get_contains_select_expectation('sub1', $choices, null, true),
                $this->get_contains_select_expectation('sub2', $choices, $orderforchoice[16], true),
                $this->get_contains_select_expectation('sub3', $choices, $orderforchoice[13], true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(1),
                $this->get_no_hint_visible_expectation());
    }
}
