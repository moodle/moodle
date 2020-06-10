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
 * This file contains tests that walk mutichoice questions through various behaviours.
 *
 * Note, there are already lots of tests of the multichoice type in the behaviour
 * tests. (Search for test_question_maker::make_a_multichoice.) This file only
 * contains a few additional tests for problems that were found during testing.
 *
 * @package    qtype
 * @subpackage multichoice
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the mutiple choice question type.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multichoice_walkthrough_test extends qbehaviour_walkthrough_test_base {
    public function test_deferredfeedback_feedback_multichoice_single() {

        // Create a multichoice, single question.
        $mc = test_question_maker::make_a_multichoice_single_question();
        $mc->shuffleanswers = false;
        $mc->answers[14]->fraction = 0.1; // Make one of the choices partially right.
        $rightindex = 0;

        $this->start_attempt_at_question($mc, 'deferredfeedback', 3);
        $this->process_submission(array('answer' => $rightindex));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation($rightindex, true, true),
                $this->get_contains_mc_radio_expectation($rightindex + 1, true, false),
                $this->get_contains_mc_radio_expectation($rightindex + 2, true, false),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);
        $this->check_current_output(
                $this->get_contains_mc_radio_expectation($rightindex, false, true),
                $this->get_contains_correct_expectation(),
                new question_pattern_expectation('/class="r0 correct"/'),
                new question_pattern_expectation('/class="r1"/'));
    }

    public function test_deferredfeedback_feedback_multichoice_multi() {
        // Create a multichoice, multi question.
        $mc = test_question_maker::make_a_multichoice_multi_question();
        $mc->shuffleanswers = false;

        $this->start_attempt_at_question($mc, 'deferredfeedback', 2);
        $this->process_submission($mc->get_correct_response());
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(2);
        $this->check_current_output(
                $this->get_contains_mc_checkbox_expectation('choice0', false, true),
                $this->get_contains_mc_checkbox_expectation('choice1', false, false),
                $this->get_contains_mc_checkbox_expectation('choice2', false, true),
                $this->get_contains_mc_checkbox_expectation('choice3', false, false),
                $this->get_contains_correct_expectation(),
                new question_pattern_expectation('/class="r0 correct"/'),
                new question_pattern_expectation('/class="r1"/'));
    }

    /**
     * Test for clear choice option.
     */
    public function test_deferredfeedback_feedback_multichoice_clearchoice() {

        // Create a multichoice, single question.
        $mc = test_question_maker::make_a_multichoice_single_question();
        $mc->shuffleanswers = false;

        $clearchoice = -1;
        $rightchoice = 0;
        $wrongchoice = 2;

        $this->start_attempt_at_question($mc, 'deferredfeedback', 3);

        // Let's first submit the wrong choice (2).
        $this->process_submission(array('answer' => $wrongchoice));  // Wrong choice (2).

        $this->check_current_mark(null);
        // Clear choice radio should not be checked.
        $this->check_current_output(
            $this->get_contains_mc_radio_expectation($rightchoice, true, false), // Not checked.
            $this->get_contains_mc_radio_expectation($rightchoice + 1, true, false), // Not checked.
            $this->get_contains_mc_radio_expectation($rightchoice + 2, true, true), // Wrong choice (2) checked.
            $this->get_contains_mc_radio_expectation($clearchoice, true, false), // Not checked.
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation()
        );

        // Now, let's clear our previous choice.
        $this->process_submission(array('answer' => $clearchoice)); // Clear choice (-1).
        $this->check_current_mark(null);

        // This time, the clear choice radio should be the only one checked.
        $this->check_current_output(
            $this->get_contains_mc_radio_expectation($rightchoice, true, false), // Not checked.
            $this->get_contains_mc_radio_expectation($rightchoice + 1, true, false), // Not checked.
            $this->get_contains_mc_radio_expectation($rightchoice + 2, true, false), // Not checked.
            $this->get_contains_mc_radio_expectation($clearchoice, true, true), // Clear choice radio checked.
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation()
        );

        // Finally, let's submit the right choice.
        $this->process_submission(array('answer' => $rightchoice)); // Right choice (0).
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_mc_radio_expectation($rightchoice, true, true),
            $this->get_contains_mc_radio_expectation($rightchoice + 1, true, false),
            $this->get_contains_mc_radio_expectation($rightchoice + 2, true, false),
            $this->get_contains_mc_radio_expectation($clearchoice, true, false),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation()
        );

        // Finish the attempt.
        $this->finish();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);
        $this->check_current_output(
            $this->get_contains_mc_radio_expectation($rightchoice, false, true),
            $this->get_contains_correct_expectation(),
            new question_pattern_expectation('/class="r0 correct"/'),
            new question_pattern_expectation('/class="r1"/'));
    }
}
