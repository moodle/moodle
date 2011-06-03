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
 * This file contains tests that walks a question through the interactive with
 * countback behaviour.
 *
 * @package    qbehaviour
 * @subpackage interactivecountback
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../engine/lib.php');
require_once(dirname(__FILE__) . '/../../../engine/simpletest/helpers.php');


/**
 * Unit tests for the interactive with countback behaviour.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_interactivecountback_walkthrough_test extends qbehaviour_walkthrough_test_base {
    public function test_interactive_feedback_match_reset() {

        // Create a matching question.
        $m = test_question_maker::make_a_matching_question();
        $m->shufflestems = false;
        $m->hints = array(
            new question_hint_with_parts(0, 'This is the first hint.', FORMAT_HTML, true, true),
            new question_hint_with_parts(0, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $this->start_attempt_at_question($m, 'interactive', 12);

        $choiceorder = $m->get_choice_order();
        $orderforchoice = array_combine(array_values($choiceorder), array_keys($choiceorder));
        $choices = array(0 => get_string('choose') . '...');
        foreach ($choiceorder as $key => $choice) {
            $choices[$key] = $m->choices[$choice];
        }

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->assertEqual('interactivecountback',
                $this->quba->get_question_attempt($this->slot)->get_behaviour_name());
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, null, true),
                $this->get_contains_select_expectation('sub1', $choices, null, true),
                $this->get_contains_select_expectation('sub2', $choices, null, true),
                $this->get_contains_select_expectation('sub3', $choices, null, true),
                $this->get_contains_question_text_expectation($m),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation());

        // Submit an answer with two right, and two wrong.
        $this->process_submission(array('sub0' => $orderforchoice[1],
                'sub1' => $orderforchoice[1], 'sub2' => $orderforchoice[1],
                'sub3' => $orderforchoice[1], '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, $orderforchoice[1], false),
                $this->get_contains_select_expectation('sub1', $choices, $orderforchoice[1], false),
                $this->get_contains_select_expectation('sub2', $choices, $orderforchoice[1], false),
                $this->get_contains_select_expectation('sub3', $choices, $orderforchoice[1], false),
                $this->get_contains_submit_button_expectation(false),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                new PatternExpectation('/' .
                        preg_quote(get_string('notcomplete', 'qbehaviour_interactive')) . '/'),
                $this->get_contains_hint_expectation('This is the first hint'),
                $this->get_contains_num_parts_correct(2),
                $this->get_contains_standard_partiallycorrect_combined_feedback_expectation(),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub0', $orderforchoice[1]),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub1', '0'),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub2', '0'),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub3', $orderforchoice[1]));

        // Check that extract responses will return the reset data.
        $prefix = $this->quba->get_field_prefix($this->slot);
        $this->assertEqual(array('sub0' => 1),
                $this->quba->extract_responses($this->slot, array($prefix . 'sub0' => 1)));

        // Do try again.
        $this->process_submission(array('sub0' => $orderforchoice[1],
                'sub3' => $orderforchoice[1], '-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, $orderforchoice[1], true),
                $this->get_contains_select_expectation('sub1', $choices, null, true),
                $this->get_contains_select_expectation('sub2', $choices, null, true),
                $this->get_contains_select_expectation('sub3', $choices, $orderforchoice[1], true),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(2),
                $this->get_no_hint_visible_expectation());

        // Submit the right answer.
        $this->process_submission(array('sub0' => $orderforchoice[1],
                'sub1' => $orderforchoice[2], 'sub2' => $orderforchoice[2],
                'sub3' => $orderforchoice[1], '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(10);
        $this->check_current_output(
                $this->get_contains_select_expectation('sub0', $choices, $orderforchoice[1], false),
                $this->get_contains_select_expectation('sub1', $choices, $orderforchoice[2], false),
                $this->get_contains_select_expectation('sub2', $choices, $orderforchoice[2], false),
                $this->get_contains_select_expectation('sub3', $choices, $orderforchoice[1], false),
                $this->get_contains_submit_button_expectation(false),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_contains_standard_correct_combined_feedback_expectation(),
                new NoPatternExpectation('/class="control\b[^"]*\bpartiallycorrect"/'));
    }
}
