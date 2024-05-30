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

namespace qbehaviour_interactive;

use question_display_options;
use question_hint;
use question_hint_with_parts;
use question_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../../../engine/lib.php');
require_once(__DIR__ . '/../../../engine/tests/helpers.php');


/**
 * Unit tests for the interactive behaviour.
 *
 * @package    qbehaviour_interactive
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class walkthrough_test extends \qbehaviour_walkthrough_test_base {

    public function test_interactive_feedback_multichoice_right(): void {

        // Create a multichoice single question.
        $mc = \test_question_maker::make_a_multichoice_single_question();
        $mc->hints = array(
            new question_hint_with_parts(0, 'This is the first hint.', FORMAT_HTML, false, false),
            new question_hint_with_parts(0, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $this->start_attempt_at_question($mc, 'interactive');

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
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());

        // Save the wrong answer.
        $this->process_submission(array('answer' => $wrongindex));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_mc_radio_expectation($wrongindex, true, true),
                $this->get_contains_mc_radio_expectation(($wrongindex + 1) % 3, true, false),
                $this->get_contains_mc_radio_expectation(($wrongindex + 1) % 3, true, false),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());

        // Submit the wrong answer.
        $this->process_submission(array('answer' => $wrongindex, '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_mc_radio_expectation($wrongindex, false, true),
                $this->get_contains_mc_radio_expectation(($wrongindex + 1) % 3, false, false),
                $this->get_contains_mc_radio_expectation(($wrongindex + 1) % 3, false, false),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                new \question_pattern_expectation('/Tries remaining: 2/'),
                $this->get_contains_hint_expectation('This is the first hint'));

        // Check that, if we review in this state, the try again button is disabled.
        $displayoptions = new question_display_options();
        $displayoptions->readonly = true;
        $html = $this->quba->render_question($this->slot, $displayoptions);
        $this->assert($this->get_contains_try_again_button_expectation(false), $html);

        // Do try again.
        $this->process_submission(array('-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_mc_radio_expectation($wrongindex, true, true),
                $this->get_contains_mc_radio_expectation(($wrongindex + 1) % 3, true, false),
                $this->get_contains_mc_radio_expectation(($wrongindex + 1) % 3, true, false),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(2),
                $this->get_no_hint_visible_expectation());

        // Submit the right answer.
        $this->process_submission(array('answer' => $rightindex, '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(0.6666667);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.6666667),
                $this->get_contains_mc_radio_expectation($rightindex, false, true),
                $this->get_contains_mc_radio_expectation(($rightindex + 1) % 3, false, false),
                $this->get_contains_mc_radio_expectation(($rightindex + 1) % 3, false, false),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_no_hint_visible_expectation());

        // Finish the attempt - should not need to add a new state.
        $numsteps = $this->get_step_count();
        $this->quba->finish_all_questions();

        // Verify.
        $this->assertEquals($numsteps, $this->get_step_count());
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(0.6666667);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.6666667),
                $this->get_contains_mc_radio_expectation($rightindex, false, true),
                $this->get_contains_mc_radio_expectation(($rightindex + 1) % 3, false, false),
                $this->get_contains_mc_radio_expectation(($rightindex + 1) % 3, false, false),
                $this->get_contains_correct_expectation(),
                $this->get_no_hint_visible_expectation());

        // Process a manual comment.
        $this->manual_grade('Not good enough!', 0.5, FORMAT_HTML);

        // Verify.
        $this->check_current_state(question_state::$mangrpartial);
        $this->check_current_mark(0.5);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.5),
                $this->get_contains_partcorrect_expectation(),
                new \question_pattern_expectation('/' . preg_quote('Not good enough!', '/') . '/'));

        // Check regrading does not mess anything up.
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$mangrpartial);
        $this->check_current_mark(0.5);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.5),
                $this->get_contains_partcorrect_expectation());

        $autogradedstep = $this->get_step($this->get_step_count() - 2);
        $this->assertEqualsWithDelta($autogradedstep->get_fraction(), 0.6666667, 0.0000001);
    }

    public function test_interactive_finish_when_try_again_showing(): void {

        // Create a multichoice single question.
        $mc = \test_question_maker::make_a_multichoice_single_question();
        $mc->showstandardinstruction = true;
        $mc->hints = array(
            new question_hint_with_parts(0, 'This is the first hint.', FORMAT_HTML, false, false),
        );
        $this->start_attempt_at_question($mc, 'interactive');

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
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(2),
                $this->get_no_hint_visible_expectation(),
                new \question_pattern_expectation('/' .
                        preg_quote(get_string('selectone', 'qtype_multichoice'), '/') . '/'));

        // Submit the wrong answer.
        $this->process_submission(array('answer' => $wrongindex, '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_mc_radio_expectation($wrongindex, false, true),
                $this->get_contains_mc_radio_expectation(($wrongindex + 1) % 3, false, false),
                $this->get_contains_mc_radio_expectation(($wrongindex + 1) % 3, false, false),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                new \question_pattern_expectation('/Tries remaining: 1/'),
                $this->get_contains_hint_expectation('This is the first hint'));

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(0);
        $this->check_current_output(
                $this->get_contains_mark_summary(0),
                $this->get_contains_mc_radio_expectation($wrongindex, false, true),
                $this->get_contains_mc_radio_expectation(($wrongindex + 1) % 3, false, false),
                $this->get_contains_mc_radio_expectation(($wrongindex + 1) % 3, false, false),
                $this->get_contains_incorrect_expectation(),
                $this->get_no_hint_visible_expectation());
    }

    public function test_interactive_shortanswer_try_to_submit_blank(): void {

        // Create a short answer question.
        $sa = \test_question_maker::make_question('shortanswer');
        $sa->hints = array(
            new question_hint(0, 'This is the first hint.', FORMAT_HTML),
            new question_hint(0, 'This is the second hint.', FORMAT_HTML),
        );
        $this->start_attempt_at_question($sa, 'interactive');

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        // Submit blank.
        $this->process_submission(array('-submit' => 1, 'answer' => ''));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_contains_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        // Now get it wrong.
        $this->process_submission(array('-submit' => 1, 'answer' => 'newt'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_contains_try_again_button_expectation(true),
                new \question_pattern_expectation('/Tries remaining: 2/'),
                $this->get_contains_hint_expectation('This is the first hint'));
        $this->assertEquals('newt',
                $this->quba->get_response_summary($this->slot));

        // Try again.
        $this->process_submission(array('-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        // Now submit blank again.
        $this->process_submission(array('-submit' => 1, 'answer' => ''));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_contains_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        // Now get it right.
        $this->process_submission(array('-submit' => 1, 'answer' => 'frog'));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(0.6666667);
        $this->check_current_output(
                $this->get_contains_mark_summary(0.6666667),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_no_hint_visible_expectation());
        $this->assertEquals('frog',
                $this->quba->get_response_summary($this->slot));
    }

    public function test_interactive_feedback_multichoice_multiple_reset(): void {

        // Create a multichoice multiple question.
        $mc = \test_question_maker::make_a_multichoice_multi_question();
        $mc->showstandardinstruction = true;
        $mc->hints = array(
            new question_hint_with_parts(0, 'This is the first hint.', FORMAT_HTML, true, true),
            new question_hint_with_parts(0, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $this->start_attempt_at_question($mc, 'interactive', 2);

        $right = array_keys($mc->get_correct_response());
        $wrong = array_diff(array('choice0', 'choice1', 'choice2', 'choice3'), $right);
        $wrong = array_values(array_diff(
                array('choice0', 'choice1', 'choice2', 'choice3'), $right));

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_question_text_expectation($mc),
                $this->get_contains_mc_checkbox_expectation('choice0', true, false),
                $this->get_contains_mc_checkbox_expectation('choice1', true, false),
                $this->get_contains_mc_checkbox_expectation('choice2', true, false),
                $this->get_contains_mc_checkbox_expectation('choice3', true, false),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation(),
                new \question_pattern_expectation('/' .
                        preg_quote(get_string('selectmulti', 'qtype_multichoice'), '/') . '/'));

        // Submit an answer with one right, and one wrong.
        $this->process_submission(array($right[0] => 1, $wrong[0] => 1, '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_mc_checkbox_expectation($right[0], false, true),
                $this->get_contains_mc_checkbox_expectation($right[1], false, false),
                $this->get_contains_mc_checkbox_expectation($wrong[0], false, true),
                $this->get_contains_mc_checkbox_expectation($wrong[1], false, false),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                new \question_pattern_expectation('/Tries remaining: 2/'),
                $this->get_contains_hint_expectation('This is the first hint'),
                $this->get_contains_num_parts_correct(1),
                $this->get_contains_standard_incorrect_combined_feedback_expectation(),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . $right[0], '1'),
                $this->get_does_not_contain_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . $right[1]),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . $wrong[0], '0'),
                $this->get_does_not_contain_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . $wrong[1]));

        // Do try again.
        $this->process_submission(array($right[0] => 1, '-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_mc_checkbox_expectation($right[0], true, true),
                $this->get_contains_mc_checkbox_expectation($right[1], true, false),
                $this->get_contains_mc_checkbox_expectation($wrong[0], true, false),
                $this->get_contains_mc_checkbox_expectation($wrong[1], true, false),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(2),
                $this->get_no_hint_visible_expectation());
    }

    public function test_interactive_regrade_changing_num_tries_leaving_open(): void {
        // Create a multichoice multiple question.
        $q = \test_question_maker::make_question('shortanswer');
        $q->hints = array(
            new question_hint_with_parts(0, 'This is the first hint.', FORMAT_HTML, true, true),
            new question_hint_with_parts(0, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $this->start_attempt_at_question($q, 'interactive', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_tries_remaining_expectation(3));

        // Submit the right answer.
        $this->process_submission(array('answer' => 'frog', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);

        // Now change the quiestion so that answer is only partially right, and regrade.
        $q->answers[13]->fraction = 0.6666667;
        $q->answers[14]->fraction = 1;

        $this->quba->regrade_all_questions(false);

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
    }

    public function test_interactive_regrade_changing_num_tries_finished(): void {
        // Create a multichoice multiple question.
        $q = \test_question_maker::make_question('shortanswer');
        $q->hints = array(
            new question_hint_with_parts(0, 'This is the first hint.', FORMAT_HTML, true, true),
            new question_hint_with_parts(0, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $this->start_attempt_at_question($q, 'interactive', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_tries_remaining_expectation(3));

        // Submit the right answer.
        $this->process_submission(array('answer' => 'frog', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);

        // Now change the quiestion so that answer is only partially right, and regrade.
        $q->answers[13]->fraction = 0.6666667;
        $q->answers[14]->fraction = 1;

        $this->quba->regrade_all_questions(true);

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        // TODO I don't think 1 is the right fraction here. However, it is what
        // you get attempting a question like this without regrading being involved,
        // and I am currently interested in testing regrading here.
        $this->check_current_mark(1);
    }

    public function test_review_of_interactive_questions_before_finished(): void {
        // Create a multichoice multiple question.
        $q = \test_question_maker::make_question('shortanswer');
        $q->hints = array(
                new question_hint_with_parts(0, 'This is the first hint.', FORMAT_HTML, true, true),
                new question_hint_with_parts(0, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $this->start_attempt_at_question($q, 'interactive', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_does_not_contain_try_again_button_expectation());

        // Now check what the teacher sees when they review the question.
        $this->displayoptions->readonly = true;
        $this->check_current_output(
                $this->get_contains_submit_button_expectation(false),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_does_not_contain_try_again_button_expectation());
        $this->displayoptions->readonly = false;

        // Submit a wrong answer.
        $this->process_submission(array('answer' => 'cat', '-submit' => 1));

        // Check the Try again button now shows up correctly.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_hint_expectation('This is the first hint.'),
                $this->get_tries_remaining_expectation(2),
                $this->get_contains_try_again_button_expectation(true));

        // And check that a disabled Try again button shows up when the question is reviewed.
        $this->displayoptions->readonly = true;
        $this->check_current_output(
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_hint_expectation('This is the first hint.'),
                $this->get_tries_remaining_expectation(2),
                $this->get_contains_try_again_button_expectation(false));
    }
}
