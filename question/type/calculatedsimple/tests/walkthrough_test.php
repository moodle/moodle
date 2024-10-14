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

namespace qtype_calculatedsimple;

use question_hint;
use question_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the simple calculated question type.
 *
 * @package    qtype_calculatedsimple
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class walkthrough_test extends \qbehaviour_walkthrough_test_base {
    public function test_interactive(): void {

        // Create a gapselect question.
        $q = \test_question_maker::make_question('calculatedsimple');
        $q->hints = array(
            new question_hint(1, 'This is the first hint.', FORMAT_HTML),
            new question_hint(2, 'This is the second hint.', FORMAT_HTML),
        );
        $this->start_attempt_at_question($q, 'interactive', 3);
        $values = $q->vs->get_values();

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

        // Sumit something that does not look like a number.
        $this->process_submission(array('-submit' => 1, 'answer' => 'newt'));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_contains_validation_error_expectation(),
                new \question_pattern_expectation('/' .
                        preg_quote(get_string('invalidnumber', 'qtype_numerical'), '/') . '/'),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        // Now get it right.
        $this->process_submission(array('-submit' => 1, 'answer' => $values['a'] + $values['b']));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);
        $this->check_current_output(
                $this->get_contains_mark_summary(3),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_no_hint_visible_expectation());
    }
}
