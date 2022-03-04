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

namespace qtype_calculatedmulti;

use question_hint_with_parts;
use question_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the calculated multiple-choice question type.
 *
 * @package    qtype_calculatedmulti
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class walkthrough_test extends \qbehaviour_walkthrough_test_base {

    public function test_interactive_single_response() {

        // Create a gapselect question.
        $q = \test_question_maker::make_question('calculatedmulti', 'singleresponse');
        $q->shuffleanswers = false;
        $q->hints = array(
            new question_hint_with_parts(1, 'This is the first hint.', FORMAT_HTML, true, false),
            new question_hint_with_parts(2, 'This is the second hint.', FORMAT_HTML, true, false),
        );
        $this->start_attempt_at_question($q, 'interactive', 3, 2);
        $values = $q->vs->get_values();
        $this->assertEquals($values, $q->datasetloader->load_values(2));

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

        // Submit a wrong answer.
        $this->process_submission(array('-submit' => 1, 'answer' => '1'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_contains_hint_expectation('This is the first hint.'));

        // Do try again.
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

        // Now get it right.
        $this->process_submission(array('-submit' => 1, 'answer' => '0'));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(2);
        $this->check_current_output(
                $this->get_contains_mark_summary(2),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_no_hint_visible_expectation());
    }

    public function test_interactive_multi_response() {

        // Create a gapselect question.
        $q = \test_question_maker::make_question('calculatedmulti', 'multiresponse');
        $q->shuffleanswers = false;
        $q->hints = array(
                new question_hint_with_parts(1, 'This is the first hint.', FORMAT_HTML, true, false),
                new question_hint_with_parts(2, 'This is the second hint.', FORMAT_HTML, true, false),
        );
        $this->start_attempt_at_question($q, 'interactive', 3, 2);
        $values = $q->vs->get_values();
        $this->assertEquals($values, $q->datasetloader->load_values(2));

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

        // Submit all boxes ticked.
        $this->process_submission(array('-submit' => 1, 'choice0' => '1', 'choice1' => '1', 'choice2' => '1'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_contains_hint_expectation('This is the first hint.'));

        // Do try again.
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

        // Now get it right.
        $this->process_submission(array('-submit' => 1, 'choice0' => '1', 'choice1' => '1', 'choice2' => '0'));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(2);
        $this->check_current_output(
                $this->get_contains_mark_summary(2),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_no_hint_visible_expectation());
    }
}
