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

namespace qtype_multianswer;

use qtype_multianswer;
use question_bank;
use question_display_options;
use question_hint_with_parts;
use question_state;
use test_question_maker;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/multianswer/questiontype.php');


/**
 * Unit tests for the multianswer question type.
 *
 * @package    qtype_multianswer
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class walkthrough_test extends \qbehaviour_walkthrough_test_base {

    protected function get_contains_subq_status(question_state $state) {
        return new \question_pattern_expectation('~' .
                preg_quote($state->default_string(true), '~') . '~');
    }

    public function test_deferred_feedback(): void {

        // Create a multianswer question.
        $q = \test_question_maker::make_question('multianswer', 'fourmc');
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
                $this->get_contains_validation_error_expectation());

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
        $this->finish();

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(2);
        $this->check_current_output(
                $this->get_contains_mark_summary(2),
                $this->get_contains_partcorrect_expectation(),
                $this->get_does_not_contain_validation_error_expectation());
    }

    public function test_deferred_feedback_numericalzero_not_answered(): void {
        // Tests the situation found in MDL-35370.

        // Create a multianswer question with one numerical subquestion, right answer zero.
        $q = \test_question_maker::make_question('multianswer', 'numericalzero');
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation());

        // Now submit all and finish.
        $this->finish();

        // Verify.
        $this->check_current_state(question_state::$gaveup);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                new \question_pattern_expectation('~<input[^>]* class="[^"]*incorrect[^"]*" [^>]*/>~'),
                $this->get_contains_subq_status(question_state::$gaveup),
                $this->get_does_not_contain_validation_error_expectation());
    }

    public function test_deferred_feedback_numericalzero_0_answer(): void {
        // Tests the situation found in MDL-35370.

        // Create a multianswer question with one numerical subquestion, right answer zero.
        $q = \test_question_maker::make_question('multianswer', 'numericalzero');
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
        $this->finish();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(1);
        $this->check_current_output(
                $this->get_contains_mark_summary(1),
                $this->get_contains_correct_expectation(),
                $this->get_contains_subq_status(question_state::$gradedright),
                $this->get_does_not_contain_validation_error_expectation());
    }

    public function test_deferred_feedback_numericalzero_0_wrong(): void {
        // Tests the situation found in MDL-35370.

        // Create a multianswer question with one numerical subquestion, right answer zero.
        $q = \test_question_maker::make_question('multianswer', 'numericalzero');
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
        $this->finish();

        // Verify.
        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(0);
        $this->check_current_output(
                $this->get_contains_mark_summary(0),
                $this->get_contains_incorrect_expectation(),
                $this->get_contains_subq_status(question_state::$gradedwrong),
                $this->get_does_not_contain_validation_error_expectation());
    }

    public function test_interactive_feedback(): void {

        // Create a multianswer question.
        $q = \test_question_maker::make_question('multianswer', 'fourmc');
        $q->hints = array(
            new question_hint_with_parts(11, 'This is the first hint.', FORMAT_HTML, false, true),
            new question_hint_with_parts(12, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $choices = array('0' => 'California', '1' => 'Arizona');

        $this->start_attempt_at_question($q, 'interactive', 4);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->assertEquals('interactivecountback',
                $this->quba->get_question_attempt($this->slot)->get_behaviour_name());
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation());
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('sub1_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub2_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub3_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub4_answer', $choices, null, true));

        // Submit a completely wrong response.
        $this->process_submission(array('sub1_answer' => '1', 'sub2_answer' => '0',
                'sub3_answer' => '1', 'sub4_answer' => '0', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub1_answer', ''),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub2_answer', ''),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub3_answer', ''),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub4_answer', ''),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_contains_hint_expectation('This is the first hint.'));
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('sub1_answer', $choices, 1, false),
                $this->get_contains_select_expectation('sub2_answer', $choices, 0, false),
                $this->get_contains_select_expectation('sub3_answer', $choices, 1, false),
                $this->get_contains_select_expectation('sub4_answer', $choices, 0, false));

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
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(2),
                $this->get_no_hint_visible_expectation());
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('sub1_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub2_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub3_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub4_answer', $choices, null, true));

        // Submit a partially wrong response.
        $this->process_submission(array('sub1_answer' => '1', 'sub2_answer' => '1',
                'sub3_answer' => '1', 'sub4_answer' => '1', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_num_parts_correct(2),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub1_answer', ''),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub2_answer', '1'),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub3_answer', ''),
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'sub4_answer', '1'),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_hint_expectation('This is the second hint.'));
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('sub1_answer', $choices, 1, false),
                $this->get_contains_select_expectation('sub2_answer', $choices, 1, false),
                $this->get_contains_select_expectation('sub3_answer', $choices, 1, false),
                $this->get_contains_select_expectation('sub4_answer', $choices, 1, false));

        // Try again.
        $this->process_submission(array('sub1_answer' => '',
                'sub2_answer' => '1', 'sub3_answer' => '',
                'sub4_answer' => '1', '-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(1),
                $this->get_no_hint_visible_expectation());
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('sub1_answer', $choices, '', true),
                $this->get_contains_select_expectation('sub2_answer', $choices, '1', true),
                $this->get_contains_select_expectation('sub3_answer', $choices, '', true),
                $this->get_contains_select_expectation('sub4_answer', $choices, '1', true));
    }

    public function test_interactive_partial_response_does_not_reveal_answer(): void {

        // Create a multianswer question.
        $q = \test_question_maker::make_question('multianswer', 'fourmc');
        $q->hints = array(
                new question_hint_with_parts(11, 'This is the first hint.', FORMAT_HTML, false, true),
                new question_hint_with_parts(12, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $choices = array('0' => 'California', '1' => 'Arizona');

        $this->start_attempt_at_question($q, 'interactive', 4);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->assertEquals('interactivecountback',
                $this->quba->get_question_attempt($this->slot)->get_behaviour_name());
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation());
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('sub1_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub2_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub3_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub4_answer', $choices, null, true));

        // Submit an incomplete response response.
        $this->process_submission(array('sub1_answer' => '1', 'sub2_answer' => '1', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_contains_validation_error_expectation(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_no_hint_visible_expectation());
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('sub1_answer', $choices, 1, true),
                $this->get_contains_select_expectation('sub2_answer', $choices, 1, true),
                $this->get_contains_select_expectation('sub3_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub4_answer', $choices, null, true));
        $this->render();
        $a = array('mark' => '0.00', 'max' => '1.00');
        $this->assertDoesNotMatchRegularExpression('~' . preg_quote(get_string('markoutofmax', 'question', $a), '~') . '~',
                $this->currentoutput);
        $a['mark'] = '1.00';
        $this->assertDoesNotMatchRegularExpression('~' . preg_quote(get_string('markoutofmax', 'question', $a), '~') . '~',
                $this->currentoutput);
    }

    public function test_interactivecountback_feedback(): void {

        // Create a multianswer question.
        $q = \test_question_maker::make_question('multianswer', 'fourmc');
        $q->hints = array(
            new question_hint_with_parts(11, 'This is the first hint.', FORMAT_HTML, true, true),
            new question_hint_with_parts(12, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $choices = array('0' => 'California', '1' => 'Arizona');

        $this->start_attempt_at_question($q, 'interactive', 12);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->assertEquals('interactivecountback',
                $this->quba->get_question_attempt($this->slot)->get_behaviour_name());
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('sub1_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub2_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub3_answer', $choices, null, true),
                $this->get_contains_select_expectation('sub4_answer', $choices, null, true));

        // Submit an answer with two right, and two wrong.
        $this->process_submission(array('sub1_answer' => '1', 'sub2_answer' => '1',
                'sub3_answer' => '1', 'sub4_answer' => '1', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                new \question_pattern_expectation('/Tries remaining: 2/'),
                $this->get_contains_hint_expectation('This is the first hint.'));
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('sub1_answer', $choices, 1, false),
                $this->get_contains_select_expectation('sub2_answer', $choices, 1, false),
                $this->get_contains_select_expectation('sub3_answer', $choices, 1, false),
                $this->get_contains_select_expectation('sub4_answer', $choices, 1, false));

        // Check that extract responses will return the reset data.
        $prefix = $this->quba->get_field_prefix($this->slot);
        $this->assertEquals(array('sub1_answer' => 1),
                $this->quba->extract_responses($this->slot, array($prefix . 'sub1_answer' => 1)));

        // Do try again.
        $this->process_submission(array('sub1_answer' => '',
                'sub2_answer' => '1', 'sub3_answer' => '',
                'sub4_answer' => '1', '-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(2),
                $this->get_no_hint_visible_expectation());
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('sub1_answer', $choices, '', true),
                $this->get_contains_select_expectation('sub2_answer', $choices, '1', true),
                $this->get_contains_select_expectation('sub3_answer', $choices, '', true),
                $this->get_contains_select_expectation('sub4_answer', $choices, '1', true));

        // Submit the right answer.
        $this->process_submission(array('sub1_answer' => '0', 'sub2_answer' => '1',
                'sub3_answer' => '0', 'sub4_answer' => '1', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(10);
        $this->check_current_output(
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_contains_correct_expectation(),
                new \question_no_pattern_expectation('/class="control\b[^"]*\bpartiallycorrect"/'));
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('sub1_answer', $choices, '0', false),
                $this->get_contains_select_expectation('sub2_answer', $choices, '1', false),
                $this->get_contains_select_expectation('sub3_answer', $choices, '0', false),
                $this->get_contains_select_expectation('sub4_answer', $choices, '1', false));
    }

    public function test_deferred_feedback_multiple(): void {

        // Create a multianswer question.
        $q = \test_question_maker::make_question('multianswer', 'multiple');
        $this->start_attempt_at_question($q, 'deferredfeedback', 2);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_marked_out_of_summary(),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_does_not_contain_validation_error_expectation());

        // Save in incomplete answer.
        $this->process_submission(array('sub1_choice0' => '1', 'sub1_choice1' => '1',
                                        'sub1_choice2' => '', 'sub1_choice3' => '',
                                        'sub1_choice4' => '', 'sub1_choice5' => '1',
                                        ));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_marked_out_of_summary(),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_contains_validation_error_expectation());

        // Save a partially correct answer.
        $this->process_submission(array('sub1_choice0' => '1', 'sub1_choice1' => '',
                                        'sub1_choice2' => '', 'sub1_choice3' => '',
                                        'sub1_choice4' => '1', 'sub1_choice5' => '1',
                                        'sub2_choice0' => '', 'sub2_choice1' => '',
                                        'sub2_choice2' => '', 'sub2_choice3' => '',
                                        'sub2_choice4' => '1',
                                  ));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_marked_out_of_summary(),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_does_not_contain_validation_error_expectation());

        // Now submit all and finish.
        $this->finish();

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(1.5);
        $this->check_current_output(
            $this->get_contains_mark_summary(1.5),
            $this->get_contains_partcorrect_expectation(),
            $this->get_does_not_contain_validation_error_expectation());
    }

    /**
     * Test corrupted question display.
     *
     * @covers \qtype_multianswer_renderer::subquestion
     */
    public function test_corrupted_question(): void {
        global $DB;

        $syscontext = \context_system::instance();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category(['contextid' => $syscontext->id]);

        $fromform = test_question_maker::get_question_form_data('multianswer', 'twosubq');
        $fromform->category = $category->id . ',' . $syscontext->id;

        $question = new \stdClass();
        $question->category = $category->id;
        $question->qtype = 'multianswer';
        $question->createdby = 0;

        $question = (new qtype_multianswer)->save_question($question, $fromform);
        $questiondata = question_bank::load_question_data($question->id);
        $questiontodeletekey = array_keys($questiondata->options->questions)[0];
        $questiontodelete = $questiondata->options->questions[$questiontodeletekey];
        $DB->delete_records('question', ['id' => $questiontodelete->id]);

        question_bank::notify_question_edited($question->id);
        $question = question_bank::load_question($question->id);

        $this->start_attempt_at_question($question, 'deferredfeedback', 2);
        $this->check_current_output(
            $this->get_contains_marked_out_of_summary(),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_does_not_contain_validation_error_expectation(),
            $this->get_contains_corruption_notification(),
            $this->get_contains_corrupted_subquestion_message(),
        );
    }
}
