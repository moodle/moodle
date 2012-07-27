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
    public function test_deferred_feedback() {

        // Create a gapselect question.
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
}
