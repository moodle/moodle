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

namespace qtype_ddmarker;

use question_display_options;
use question_hint_ddmarker;
use question_pattern_expectation;
use question_state;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/ddmarker/tests/helper.php');


/**
 * Unit tests for the drag-and-drop markers question type.
 *
 * @package   qtype_ddmarker
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qtype_ddmarker_question
 * @covers \qtype_ddmarker_renderer
 * @covers \question_hint_ddmarker
 */
class walkthrough_test extends \qbehaviour_walkthrough_test_base {

    /**
     * Get an expectation that the output contains a marker.
     *
     * @param int $choice which choice.
     * @param bool $infinite whether there are infinitely many of that choice.
     * @return \question_contains_tag_with_attributes the expectation.
     */
    protected function get_contains_draggable_marker_home_expectation($choice, $infinite) {
        $class = 'marker choice'.$choice;
        if ($infinite) {
            $class .= ' infinite';
        }

        $expectedattrs = array();
        $expectedattrs['class'] = $class;

        return new \question_contains_tag_with_attributes('span', $expectedattrs);
    }

    /**
     * Get an expectation that the output contains a hidden input with certain name and optionally value.
     *
     * Like the parent method, but make it more specific to this question type.
     *
     * @param string $choiceno hidden field name.
     * @param string|null $value if passed, this value will also be asserted.
     * @return \question_contains_tag_with_attributes the expectation.
     */
    protected function get_contains_hidden_expectation($choiceno, $value = null) {
        $name = $this->quba->get_field_prefix($this->slot) .'c'. $choiceno;
        $expectedattributes = array('type' => 'hidden', 'name' => s($name));
        $expectedattributes['class'] = "choices choice{$choiceno}";
        if (!is_null($value)) {
            $expectedattributes['value'] = s($value);
        }
        return new \question_contains_tag_with_attributes('input', $expectedattributes);
    }

    public function test_interactive_behaviour() {

        // Create a drag-and-drop question.
        $dd = \test_question_maker::make_question('ddmarker');
        $dd->hints = array(
            new question_hint_ddmarker(13, 'This is the first hint.',
                                                            FORMAT_HTML, false, false, false),
            new question_hint_ddmarker(14, 'This is the second hint.',
                                                            FORMAT_HTML, true, true, false),
        );
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'interactive', 12);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1),
                $this->get_contains_hidden_expectation(2),
                $this->get_contains_hidden_expectation(3),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());

        $completelywrong = array('c1' => '0,250', 'c2' => '100,250', 'c3' => '150,250');
        // Save the wrong answer.
        $this->process_submission($completelywrong);
        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1, '0,250'),
                $this->get_contains_hidden_expectation(2, '100,250'),
                $this->get_contains_hidden_expectation(3, '150,250'),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());
        // Submit the wrong answer.
        $this->process_submission($completelywrong + array('-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1, '0,250'),
                $this->get_contains_hidden_expectation(2, '100,250'),
                $this->get_contains_hidden_expectation(3, '150,250'),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_contains_hint_expectation('This is the first hint'));

        // Do try again.
        $this->process_submission(array('-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1, '0,250'),
                $this->get_contains_hidden_expectation(2, '100,250'),
                $this->get_contains_hidden_expectation(3, '150,250'),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(2),
                $this->get_no_hint_visible_expectation());

        // Submit the right answer.
        $this->process_submission(
                    array('c1' => '50,50', 'c2' => '150,50', 'c3' => '100,150', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(8);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1, '50,50'),
                $this->get_contains_hidden_expectation(2, '150,50'),
                $this->get_contains_hidden_expectation(3, '100,150'),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_no_hint_visible_expectation());

        // Check regrading does not mess anything up.
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(8);
    }

    public function test_deferred_feedback() {

        // Create a drag-and-drop question.
        $dd = \test_question_maker::make_question('ddmarker');
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'deferredfeedback', 12);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1),
                $this->get_contains_hidden_expectation(2),
                $this->get_contains_hidden_expectation(3),
                $this->get_does_not_contain_feedback_expectation());

        // Save a partial answer.
        $this->process_submission(array('c1' => '150,50', 'c2' => '50,50'));
        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);

        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1, '150,50'),
                $this->get_contains_hidden_expectation(2, '50,50'),
                $this->get_contains_hidden_expectation(3, ''),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation());
        // Save the right answer.
        $this->process_submission(
                        array('c1' => '50,50', 'c2' => '150,50', 'c3' => '100,150'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1, '50,50'),
                $this->get_contains_hidden_expectation(2, '150,50'),
                $this->get_contains_hidden_expectation(3, '100,150'),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(12);

        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1, '50,50'),
                $this->get_contains_hidden_expectation(2, '150,50'),
                $this->get_contains_hidden_expectation(3, '100,150'),
                $this->get_contains_correct_expectation());

        // Change the right answer a bit.
        $dd->rightchoices[2] = 1;

        // Check regrading does not mess anything up.
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(8);
    }

    public function test_deferred_feedback_unanswered() {

        // Create a drag-and-drop question.
        $dd = \test_question_maker::make_question('ddmarker');
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'deferredfeedback', 12);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1),
                $this->get_contains_hidden_expectation(2),
                $this->get_contains_hidden_expectation(3),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(1);

        // Save a blank response.
        $this->process_submission(array('c1' => '', 'c2' => '', 'c3' => ''));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1, ''),
                $this->get_contains_hidden_expectation(2, ''),
                $this->get_contains_hidden_expectation(3, ''),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(1);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gaveup);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false));
    }

    public function test_deferred_feedback_partial_answer() {

        // Create a drag-and-drop question.
        $dd = \test_question_maker::make_question('ddmarker');
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'deferredfeedback', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1),
                $this->get_contains_hidden_expectation(2),
                $this->get_contains_hidden_expectation(3),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation());

        $this->process_submission(array('c1' => '50,50', 'c2' => '150,50', 'c3' => ''));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1, '50,50'),
                $this->get_contains_hidden_expectation(2, '150,50'),
                $this->get_contains_hidden_expectation(3, ''),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(2);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_partcorrect_expectation());
    }

    public function test_interactive_grading() {

        // Create a drag-and-drop question.
        $dd = \test_question_maker::make_question('ddmarker');
        $dd->hints = array(
            new question_hint_ddmarker(1, 'This is the first hint.',
                    FORMAT_MOODLE, true, true, false),
            new question_hint_ddmarker(2, 'This is the second hint.',
                    FORMAT_MOODLE, true, true, false),
        );
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'interactive', 12);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->assertEquals('interactivecountback',
                $this->quba->get_question_attempt($this->slot)->get_behaviour_name());
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1),
                $this->get_contains_hidden_expectation(2),
                $this->get_contains_hidden_expectation(3),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_no_hint_visible_expectation());

        // Submit an response with the first two parts right.
        $this->process_submission(
                    array('c1' => '50,50', 'c2' => '150,50', 'c3' => '150,50', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_contains_hint_expectation('This is the first hint'),
                $this->get_contains_num_parts_correct(2),
                $this->get_contains_standard_partiallycorrect_combined_feedback_expectation(),
                $this->get_contains_hidden_expectation(1, '50,50'),
                $this->get_contains_hidden_expectation(2, '150,50'),
                $this->get_contains_hidden_expectation(3, '150,50'));

        // Check that extract responses will return the reset data.
        $prefix = $this->quba->get_field_prefix($this->slot);
        $this->assertEquals(array('c1' => '50,50', 'c2' => '150,50'),
                $this->quba->extract_responses($this->slot,
                array($prefix . 'c1' => '50,50', $prefix . 'c2' => '150,50', '-tryagain' => 1)));

        // Do try again.
        // keys c3 is an extra hidden fields to clear data.
        $this->process_submission(
                        array('c1' => '50,50', 'c2' => '150,50', 'c3' => '', '-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1, '50,50'),
                $this->get_contains_hidden_expectation(2, '150,50'),
                $this->get_contains_hidden_expectation(3, ''),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(2),
                $this->get_no_hint_visible_expectation());

        // Submit an response with the first and last parts right.
        $this->process_submission(
                    array('c1' => '50,50', 'c2' => '150,150', 'c3' => '100,150', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_try_again_button_expectation(true),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_contains_hint_expectation('This is the second hint'),
                $this->get_contains_num_parts_correct(2),
                $this->get_contains_standard_partiallycorrect_combined_feedback_expectation(),
                $this->get_contains_hidden_expectation(1, '50,50'),
                $this->get_contains_hidden_expectation(2, '150,150'),
                $this->get_contains_hidden_expectation(3, '100,150'));

        // Do try again.
        $this->process_submission(
                        array('c1' => '50,50', 'c2' => '', 'c3' => '', '-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1, '50,50'),
                $this->get_contains_hidden_expectation(2, ''),
                $this->get_contains_hidden_expectation(3, ''),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(1),
                $this->get_no_hint_visible_expectation());

        // Submit the right answer.
        $this->process_submission(
                    array('c1' => '50,50', 'c2' => '150,50', 'c3' => '100,150', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(8);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1, '50,50'),
                $this->get_contains_hidden_expectation(2, '150,50'),
                $this->get_contains_hidden_expectation(3, '100,150'),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_no_hint_visible_expectation(),
                $this->get_does_not_contain_num_parts_correct(),
                $this->get_contains_standard_correct_combined_feedback_expectation());
    }

    public function test_interactive_correct_no_submit() {

        // Create a drag-and-drop question.
        $dd = \test_question_maker::make_question('ddmarker');
        $dd->hints = array(
            new question_hint_ddmarker(23, 'This is the first hint.',
                    FORMAT_MOODLE, false, false, false),
            new question_hint_ddmarker(24, 'This is the second hint.',
                    FORMAT_MOODLE, true, true, false),
        );
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'interactive', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1),
                $this->get_contains_hidden_expectation(2),
                $this->get_contains_hidden_expectation(3),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());

        // Save the right answer.
        $this->process_submission(array('c1' => '50,50', 'c2' => '150,50', 'c3' => '100,150'));

        // Finish the attempt without clicking check.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);
        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_correct_expectation(),
                $this->get_no_hint_visible_expectation());

        // Check regrading does not mess anything up.
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);
    }

    public function test_interactive_partial_no_submit() {

        // Create a drag-and-drop question.
        $dd = \test_question_maker::make_question('ddmarker');
        $dd->hints = array(
            new question_hint_ddmarker(23, 'This is the first hint.',
                    FORMAT_MOODLE, false, false, false),
            new question_hint_ddmarker(24, 'This is the second hint.',
                    FORMAT_MOODLE, true, true, false),
        );
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'interactive', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1),
                $this->get_contains_hidden_expectation(2),
                $this->get_contains_hidden_expectation(3),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());

        // Save the a partially right answer.
        $this->process_submission(array('c1' => '50,50', 'c2' => '50,50', 'c3' => '100,150'));

        // Finish the attempt without clicking check.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(2);

        $this->check_current_output(
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_partcorrect_expectation(),
                $this->get_no_hint_visible_expectation());

        // Check regrading does not mess anything up.
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(2);
    }

    public function test_interactive_no_right_clears() {

        // Create a drag-and-drop question.
        $dd = \test_question_maker::make_question('ddmarker');
        $dd->hints = array(
            new question_hint_ddmarker(23, 'This is the first hint.',
                                                                FORMAT_MOODLE, false, true, false),
            new question_hint_ddmarker(24, 'This is the second hint.',
                                                                FORMAT_MOODLE, true, true, false),
        );
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'interactive', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1),
                $this->get_contains_hidden_expectation(2),
                $this->get_contains_hidden_expectation(3),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(3),
                $this->get_no_hint_visible_expectation());

        // Save the a completely wrong answer.
        $this->process_submission(
                    array('c1' => '100,150', 'c2' => '100,150', 'c3' => '50,50', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_does_not_contain_submit_button_expectation(),
                $this->get_contains_hint_expectation('This is the first hint'));

        // Do try again.
        $this->process_submission(
                        array('c1' => '', 'c2' => '', 'c3' => '', '-tryagain' => 1));

        // Check that all the wrong answers have been cleared.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1, ''),
                $this->get_contains_hidden_expectation(2, ''),
                $this->get_contains_hidden_expectation(3, ''),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(2),
                $this->get_no_hint_visible_expectation());
    }

    public function test_display_of_right_answer_when_shuffled() {

        // Create a drag-and-drop question.
        $dd = \test_question_maker::make_question('ddmarker');
        $this->start_attempt_at_question($dd, 'deferredfeedback', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
                $this->get_contains_hidden_expectation(1),
                $this->get_contains_hidden_expectation(2),
                $this->get_contains_hidden_expectation(3),
                $this->get_does_not_contain_feedback_expectation());

        // Save a partial answer.
        $this->process_submission($dd->get_correct_response());

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $rightanswer = array($dd->get_right_choice_for(1) => '50,50',
                                $dd->get_right_choice_for(2) => '150,50',
                                $dd->get_right_choice_for(3) => '100,150');
        $this->check_current_output(
            $this->get_contains_hidden_expectation(1, $rightanswer[1]),
            $this->get_contains_hidden_expectation(2, $rightanswer[2]),
            $this->get_contains_hidden_expectation(3, $rightanswer[3]),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->displayoptions->rightanswer = question_display_options::VISIBLE;
        $this->assertEquals('{Drop zone 1 -> quick}, '.
                            '{Drop zone 2 -> fox}, '.
                            '{Drop zone 3 -> lazy}',
                            $dd->get_right_answer_summary());
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);
    }

    public function test_interactive_state_which_incorrect() {

        // Create a drag-and-drop question.
        $dd = \test_question_maker::make_question('ddmarker');
        $dd->hints = [
            new question_hint_ddmarker(23, 'This is the first hint.',
                    FORMAT_MOODLE, false, true, true),
        ];
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'interactive', 2);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_contains_hidden_expectation(1),
                $this->get_contains_hidden_expectation(2),
                $this->get_contains_hidden_expectation(3),
                $this->get_contains_submit_button_expectation(true),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_tries_remaining_expectation(2),
                $this->get_no_hint_visible_expectation());

        // Save the a completely wrong answer.
        $this->process_submission(
                ['c1' => '100,150', 'c2' => '100,150', 'c3' => '50,50', '-submit' => 1]);

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_contains_draggable_marker_home_expectation(1, false),
                $this->get_contains_draggable_marker_home_expectation(2, false),
                $this->get_contains_draggable_marker_home_expectation(3, false),
                $this->get_does_not_contain_submit_button_expectation(),
                new question_pattern_expectation('~' . preg_quote(
                        '<div class="wrongparts">Markers placed in the wrong area: ' .
                        '<span class="wrongpart">quick</span>, <span class="wrongpart">fox</span>, ' .
                        '<span class="wrongpart">lazy</span>',
                    '~') . '~'),
                $this->get_contains_hint_expectation('This is the first hint'));
    }
}
