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
 * Unit tests for the ordering question type.
 *
 * @package   qtype_ordering
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_ordering;

use qtype_ordering\question_hint_ordering;
use test_question_maker;
use question_state;
use question_pattern_expectation;
use stdClass;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/ddwtos/tests/helper.php');

/**
 * Unit tests for the ordering question type.
 *
 * These tests simulate a student's complete interaction with a question.
 *
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \qtype_ordering
 */
class walkthrough_test extends \qbehaviour_walkthrough_test_base {

    /**
     * Get the array of post data that will .
     *
     * @param array Representation of the response we want to submit. The answers in order.
     * @return array simulated POST data.
     */
    protected function get_response($items) {
        $question = $this->quba->get_question($this->slot);
        $md5keys = [];
        foreach ($items as $item) {
            foreach ($question->answers as $answer) {
                if ($item === $answer->answer) {
                    $md5keys[] = $answer->md5key;
                    break;
                }
            }
        }
        return ['response_' . $question->id => implode(',', $md5keys)];
    }

    public function test_deferred_feedback() {

        // Create an ordering question.
        $question = test_question_maker::make_question('ordering');
        $this->start_attempt_at_question($question, 'deferredfeedback', 15);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'response_' . $question->id),
                $this->get_does_not_contain_feedback_expectation());

        // Save the right answer.
        $rightresponse = $this->get_response(['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']);
        $this->process_submission($rightresponse);

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation());

        // If the same answer is saved again, we should not generate another step.
        $stepcount = $this->get_step_count();
        $this->process_submission($rightresponse);

        // Verify.
        $this->check_step_count($stepcount);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(15);
        $this->check_current_output(
                $this->get_contains_correct_expectation(),
                $this->get_contains_general_feedback_expectation($question));
    }

    public function test_interactive_behaviour() {

        // Create a drag-and-drop question.
        $question = test_question_maker::make_question('ordering');
        $question->hints = array(
            new question_hint_ordering(13, 'This is the first hint.', FORMAT_HTML, true, false, true),
            new question_hint_ordering(14, 'This is the second hint.', FORMAT_HTML, false, false, false),
        );
        $this->start_attempt_at_question($question, 'interactive', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_hidden_expectation(
                        $this->quba->get_field_prefix($this->slot) . 'response_' . $question->id),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation());

        // Submit the wrong answer.
        $this->process_submission(['-submit' => 1] +
                $this->get_response(['Environment', 'Modular', 'Object', 'Oriented', 'Dynamic', 'Learning']));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_contains_num_parts_correct_ordering(0, 5, 1),
                $this->get_contains_hint_expectation('This is the first hint'));

        // Do try again.
        $this->process_submission(array('-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(2),
                $this->get_no_hint_visible_expectation());

        // Submit the right answer.
        $this->process_submission(['-submit' => 1] +
                $this->get_response(['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment']));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        // Note, this may not be the 'best possible' grade. Perhaps there should
        // partial credit for things that were right on the first try.
        $this->check_current_mark(2);
        $this->check_current_output(
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation());

        // Check regrading does not mess anything up.
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(2);
    }

    /**
     * Return question pattern expectation.
     *
     * @param int $numright The number of item correct.
     * @param int $numpartial The number of partial correct item.
     * @param int $numincorrect The number of incorrect item.
     * @return question_pattern_expectation
     */
    protected function get_contains_num_parts_correct_ordering($numright, $numpartial, $numincorrect) {
        $a = new stdClass();
        $a->numright = $numright;
        $a->numpartial = $numpartial;
        $a->numincorrect = $numincorrect;

        $output = '';
        if ($a->numright) {
            $a->numrightplural = get_string($a->numright > 1 ? 'itemplural' : 'itemsingular', 'qtype_ordering');
            $output .= '<div>' . get_string('yougotnright', 'qtype_ordering', $a) . '</div>';
        }

        if ($a->numpartial) {
            $a->numpartialplural = get_string($a->numpartial > 1 ? 'itemplural' : 'itemsingular', 'qtype_ordering');
            $output .= '<div>' . get_string('yougotnpartial', 'qtype_ordering', $a) . '</div>';
        }

        if ($a->numincorrect) {
            $a->numincorrectplural = get_string($a->numincorrect > 1 ? 'itemplural' : 'itemsingular', 'qtype_ordering');
            $output .= '<div>' . get_string('yougotnincorrect', 'qtype_ordering', $a) . '</div>';
        }

        $pattern = '/<div class="numpartscorrect">' . preg_quote($output, '/');

        return new question_pattern_expectation($pattern . '/');
    }
}
