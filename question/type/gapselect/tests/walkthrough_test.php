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
 * This file contains tests that simulate a user attempt a gapselect question.
 *
 * @package   qtype_gapselect
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/gapselect/tests/helper.php');


/**
 * Unit tests for the gap-select question type.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_gapselect_walkthrough_test extends qbehaviour_walkthrough_test_base {
    public function test_interactive_behaviour() {

        // Create a gapselect question.
        $q = qtype_gapselect_test_helper::make_a_gapselect_question();
        $q->hints = array(
            new question_hint_with_parts(1, 'This is the first hint.', FORMAT_HTML, false, false),
            new question_hint_with_parts(2, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $q->shufflechoices = false;
        $this->start_attempt_at_question($q, 'interactive', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());
        // Note it is possible to check the first select option as below, but it is not required.
        // Also note the ' ' in the p2 example below is a nbsp (used when names are short).
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('p1',
                        ['' => get_string('choosedots'), '1' => 'quick', '2' => 'slow'], null, true),
                $this->get_contains_select_expectation('p2',
                        ['' => ' ', '1' => 'fox', '2' => 'dog'], null, true),
                $this->get_contains_select_expectation('p3',
                        ['1' => 'lazy', '2' => 'assiduous'], null, true));

        // Save the wrong answer.
        $this->process_submission(array('p1' => '2', 'p2' => '2', 'p3' => '2'));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('p1',
                        ['1' => 'quick', '2' => 'slow'], 2, true),
                $this->get_contains_select_expectation('p2',
                        ['1' => 'fox', '2' => 'dog'], 2, true),
                $this->get_contains_select_expectation('p3',
                        ['1' => 'lazy', '2' => 'assiduous'], 2, true));

        // Submit the wrong answer.
        $this->process_submission(array('p1' => '2', 'p2' => '2', 'p3' => '2', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_contains_hint_expectation('This is the first hint'));
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('p1',
                        ['1' => 'quick', '2' => 'slow'], 2, false),
                $this->get_contains_select_expectation('p2',
                        ['1' => 'fox', '2' => 'dog'], 2, false),
                $this->get_contains_select_expectation('p3',
                        ['1' => 'lazy', '2' => 'assiduous'], 2, false));

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
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('p1',
                        ['1' => 'quick', '2' => 'slow'], 2, true),
                $this->get_contains_select_expectation('p2',
                        ['1' => 'fox', '2' => 'dog'], 2, true),
                $this->get_contains_select_expectation('p3',
                        ['1' => 'lazy', '2' => 'assiduous'], 2, true));

        // Submit the right answer.
        $this->process_submission(array('p1' => '1', 'p2' => '1', 'p3' => '1', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(2);
        $this->check_current_output(
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_no_hint_visible_expectation());
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('p1',
                        ['1' => 'quick', '2' => 'slow'], 1, false),
                $this->get_contains_select_expectation('p2',
                        ['1' => 'fox', '2' => 'dog'], 1, false),
                $this->get_contains_select_expectation('p3',
                        ['1' => 'lazy', '2' => 'assiduous'], 1, false));

        // Check regrading does not mess anything up.
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(2);
    }

    public function test_multilang_behaviour() {

        // Enable multilang filter to on content and heading.
        filter_set_global_state('multilang', TEXTFILTER_ON);
        filter_set_applies_to_strings('multilang', 1);
        $filtermanager = filter_manager::instance();
        $filtermanager->reset_caches();

        // Create a multilang gapselect question.
        $q = qtype_gapselect_test_helper::make_a_multilang_gapselect_question();
        $q->shufflechoices = false;
        $this->start_attempt_at_question($q, 'interactive', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_output_contains_selectoptions(
                $this->get_contains_select_expectation('p1',
                        ['1' => 'cat', '2' => 'dog'], null, true),
                $this->get_contains_select_expectation('p2',
                        ['1' => 'mat', '2' => 'bat'], null, true));
    }

    public function test_choices_containing_dollars() {

        // Choices with a currency like entry (e.g. $3) should display.
        $q = qtype_gapselect_test_helper::make_a_currency_gapselect_question();
        $q->shufflechoices = false;
        $this->start_attempt_at_question($q, 'interactive', 1);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $html = $this->quba->render_question($this->slot, $this->displayoptions);
        preg_match_all('/<option value="([^>]*)">([^<]*)<\/option>/', $html, $matches);
        $this->assertEquals('$2', $matches[2][1]);
        $this->assertEquals('$3', $matches[2][2]);
        $this->assertEquals('$4.99', $matches[2][3]);
        $this->assertEquals('-1', $matches[2][4]);
    }
}
