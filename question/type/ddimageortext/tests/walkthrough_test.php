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
 * Unit tests for the drag-and-drop onto image question type.
 *
 * @package   qtype_ddimageortext
 * @copyright 2010 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/ddimageortext/tests/helper.php');


/**
 * Unit tests for the drag-and-drop onto image question type.
 *
 * @copyright 2010 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddimageortext_walkthrough_test extends qbehaviour_walkthrough_test_base {

    /**
     * Get an expectation that the output contains an item ready to drag.
     * @param int $dragitemno the item number.
     * @param int $choice which choice this is.
     * @param int $group which drag group it belongs to.
     * @return question_contains_tag_with_attributes the required expectation.
     */
    protected function get_contains_drag_image_home_expectation($dragitemno, $choice, $group) {
        $class = 'group' . $group . ' draghome choice' . $choice;

        $expectedattrs = array();
        $expectedattrs['class'] = $class;

        return new question_contains_tag_with_attributes('div', $expectedattrs);
    }

    public function test_interactive_behaviour() {

        // Create a drag-and-drop question.
        $dd = test_question_maker::make_question('ddimageortext');
        $dd->hints = array(
            new question_hint_with_parts(13, 'This is the first hint.', FORMAT_HTML, false, false),
            new question_hint_with_parts(14, 'This is the second hint.', FORMAT_HTML, true, true),
        );
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'interactive', 12);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4'),
            $this->get_contains_submit_button_expectation(true),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_tries_remaining_expectation(3),
            $this->get_no_hint_visible_expectation());

        // Save the wrong answer.
        $this->process_submission(array('p1' => '2', 'p2' => '1', 'p3' => '2', 'p4' => '1'));
        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', 1),
            $this->get_contains_submit_button_expectation(true),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_tries_remaining_expectation(3),
            $this->get_no_hint_visible_expectation());
        // Submit the wrong answer.
        $this->process_submission(
            array('p1' => '2', 'p2' => '1', 'p3' => '2', 'p4' => '1', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', 1),
            $this->get_contains_try_again_button_expectation(true),
            $this->get_contains_hint_expectation('This is the first hint'));

        // Do try again.
        $this->process_submission(array('-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', '2'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', '1'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', '2'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', '1'),
            $this->get_contains_submit_button_expectation(true),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_tries_remaining_expectation(2),
            $this->get_no_hint_visible_expectation());

        // Submit the right answer.
        $this->process_submission(
            array('p1' => '1', 'p2' => '2', 'p3' => '1', 'p4' => '2', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(8);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', '1'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', '2'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', '1'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', '2'),
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
        $dd = test_question_maker::make_question('ddimageortext');
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'deferredfeedback', 12);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4'),
            $this->get_does_not_contain_feedback_expectation());

        // Save a partial answer.
        $this->process_submission(array('p1' => '2', 'p2' => '1'));
        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);

        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', ''),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', ''),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation());
        // Save the right answer.
        $this->process_submission(
            array('p1' => '1', 'p2' => '2', 'p3' => '1', 'p4' => '2'));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', 2),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(12);

        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', 2),
            $this->get_contains_correct_expectation());

        // Change the right answer a bit.
        $dd->rightchoices[2] = 1;

        // Check regrading does not mess anything up.
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(9);
    }

    public function test_deferred_feedback_unanswered() {

        // Create a drag-and-drop question.
        $dd = test_question_maker::make_question('ddimageortext');
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'deferredfeedback', 12);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4'),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(1);

        // Save a blank response.
        $this->process_submission(array('p1' => '', 'p2' => '', 'p3' => '', 'p4' => ''));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', ''),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', ''),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', ''),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', ''),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(1);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gaveup);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2));
    }

    public function test_deferred_feedback_partial_answer() {

        // Create a drag-and-drop question.
        $dd = test_question_maker::make_question('ddimageortext');
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'deferredfeedback', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4'),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation());

        // Save a partial response.
        $this->process_submission(array('p1' => '1', 'p2' => '2', 'p3' => '', 'p4' => ''));

        // Verify.
        $this->check_current_state(question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', 0),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', 0),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(1.5);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_partcorrect_expectation());
    }

    public function test_interactive_grading() {

        // Create a drag-and-drop question.
        $dd = test_question_maker::make_question('ddimageortext');
        $dd->hints = array(
            new question_hint_with_parts(1, 'This is the first hint.',
                FORMAT_MOODLE, true, true),
            new question_hint_with_parts(2, 'This is the second hint.',
                FORMAT_MOODLE, true, true),
        );
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'interactive', 12);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->assertEquals('interactivecountback',
            $this->quba->get_question_attempt($this->slot)->get_behaviour_name());
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4'),
            $this->get_contains_submit_button_expectation(true),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_tries_remaining_expectation(3),
            $this->get_does_not_contain_num_parts_correct(),
            $this->get_no_hint_visible_expectation());

        // Submit an response with the first two parts right.
        $this->process_submission(
            array('p1' => '1', 'p2' => '2', 'p3' => '2', 'p4' => '1', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_does_not_contain_submit_button_expectation(),
            $this->get_contains_try_again_button_expectation(true),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_contains_hint_expectation('This is the first hint'),
            $this->get_contains_num_parts_correct(2),
            $this->get_contains_standard_partiallycorrect_combined_feedback_expectation(),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', 1));

        // Check that extract responses will return the reset data.
        $prefix = $this->quba->get_field_prefix($this->slot);
        $this->assertEquals(array('p1' => '1', 'p2' => '2'),
            $this->quba->extract_responses($this->slot,
                array($prefix . 'p1' => '1', $prefix . 'p2' => '2', '-tryagain' => 1)));

        // Do try again.
        // keys p3 and p4 are extra hidden fields to clear data.
        $this->process_submission(
            array('p1' => '1', 'p2' => '2', 'p3' => '', 'p4' => '', '-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', 0),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', 0),
            $this->get_contains_submit_button_expectation(true),
            $this->get_does_not_contain_try_again_button_expectation(),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_tries_remaining_expectation(2),
            $this->get_no_hint_visible_expectation());

        // Submit an response with the first and last parts right.
        $this->process_submission(
            array('p1' => '1', 'p2' => '1', 'p3' => '2', 'p4' => '2', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_does_not_contain_submit_button_expectation(),
            $this->get_contains_try_again_button_expectation(true),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_contains_hint_expectation('This is the second hint'),
            $this->get_contains_num_parts_correct(2),
            $this->get_contains_standard_partiallycorrect_combined_feedback_expectation(),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', 2));

        // Do try again.
        $this->process_submission(
            array('p1' => '1', 'p2' => '', 'p3' => '', 'p4' => '2', '-tryagain' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', 0),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', 0),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', 2),
            $this->get_contains_submit_button_expectation(true),
            $this->get_does_not_contain_try_again_button_expectation(),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_tries_remaining_expectation(1),
            $this->get_no_hint_visible_expectation());

        // Submit the right answer.
        $this->process_submission(
            array('p1' => '1', 'p2' => '2', 'p3' => '1', 'p4' => '2', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(7);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', 1),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', 2),
            $this->get_does_not_contain_submit_button_expectation(),
            $this->get_does_not_contain_try_again_button_expectation(),
            $this->get_contains_correct_expectation(),
            $this->get_no_hint_visible_expectation(),
            $this->get_does_not_contain_num_parts_correct(),
            $this->get_contains_standard_correct_combined_feedback_expectation());
    }

    public function test_interactive_correct_no_submit() {

        // Create a drag-and-drop question.
        $dd = test_question_maker::make_question('ddimageortext');
        $dd->hints = array(
            new question_hint_with_parts(23, 'This is the first hint.',
                FORMAT_MOODLE, false, false),
            new question_hint_with_parts(24, 'This is the second hint.',
                FORMAT_MOODLE, true, true),
        );
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'interactive', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4'),
            $this->get_contains_submit_button_expectation(true),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_tries_remaining_expectation(3),
            $this->get_no_hint_visible_expectation());

        // Save the right answer.
        $this->process_submission(array('p1' => '1', 'p2' => '2', 'p3' => '1', 'p4' => '2'));

        // Finish the attempt without clicking check.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);
        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
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
        $dd = test_question_maker::make_question('ddimageortext');
        $dd->hints = array(
            new question_hint_with_parts(23, 'This is the first hint.',
                FORMAT_MOODLE, false, false),
            new question_hint_with_parts(24, 'This is the second hint.',
                FORMAT_MOODLE, true, true),
        );
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'interactive', 4);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4'),
            $this->get_contains_submit_button_expectation(true),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_tries_remaining_expectation(3),
            $this->get_no_hint_visible_expectation());

        // Save the a partially right answer.
        $this->process_submission(array('p1' => '1', 'p2' => '1', 'p3' => '2', 'p4' => '1'));

        // Finish the attempt without clicking check.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(1);

        $this->check_current_output(
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_does_not_contain_submit_button_expectation(),
            $this->get_contains_partcorrect_expectation(),
            $this->get_no_hint_visible_expectation());

        // Check regrading does not mess anything up.
        $this->quba->regrade_all_questions();

        // Verify.
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(1);
    }

    public function test_interactive_no_right_clears() {

        // Create a drag-and-drop question.
        $dd = test_question_maker::make_question('ddimageortext');
        $dd->hints = array(
            new question_hint_with_parts(23, 'This is the first hint.', FORMAT_MOODLE, false, true),
            new question_hint_with_parts(24, 'This is the second hint.', FORMAT_MOODLE, true, true),
        );
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'interactive', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
            $this->get_contains_marked_out_of_summary(),
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4'),
            $this->get_contains_submit_button_expectation(true),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_tries_remaining_expectation(3),
            $this->get_no_hint_visible_expectation());

        // Save the a completely wrong answer.
        $this->process_submission(
            array('p1' => '2', 'p2' => '1', 'p3' => '2', 'p4' => '1', '-submit' => 1));

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_marked_out_of_summary(),
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_does_not_contain_submit_button_expectation(),
            $this->get_contains_hint_expectation('This is the first hint'));

        // Do try again.
        $this->process_submission(
            array('p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', '-tryagain' => 1));

        // Check that all the wrong answers have been cleared.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_marked_out_of_summary(),
            $this->get_contains_drag_image_home_expectation(1, 1, 1),
            $this->get_contains_drag_image_home_expectation(2, 2, 1),
            $this->get_contains_drag_image_home_expectation(3, 1, 2),
            $this->get_contains_drag_image_home_expectation(4, 2, 2),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1', 0),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2', 0),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3', 0),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4', 0),
            $this->get_contains_submit_button_expectation(true),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_tries_remaining_expectation(2),
            $this->get_no_hint_visible_expectation());
    }

    public function test_display_of_right_answer_when_shuffled() {

        // Create a drag-and-drop question.
        $dd = test_question_maker::make_question('ddimageortext');
        $this->start_attempt_at_question($dd, 'deferredfeedback', 3);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_current_output(
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3'),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4'),
            $this->get_does_not_contain_feedback_expectation());

        // Save a partial answer.
        $this->process_submission($dd->get_correct_response());

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p1',
                $dd->get_right_choice_for(1)),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p2',
                $dd->get_right_choice_for(2)),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p3',
                $dd->get_right_choice_for(3)),
            $this->get_contains_hidden_expectation(
                $this->quba->get_field_prefix($this->slot) . 'p4',
                $dd->get_right_choice_for(4)),
            $this->get_does_not_contain_correctness_expectation(),
            $this->get_does_not_contain_feedback_expectation());

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->displayoptions->rightanswer = question_display_options::VISIBLE;
        $this->assertEquals('Drop zone 1 -> {1. quick} '.
                'Drop zone 2 -> {2. fox} '.
                'Drop zone 3 -> {3. lazy} '.
                'Drop zone 4 -> {4. dog}',
            $dd->get_right_answer_summary());
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(3);
    }

    public function test_mixed_lang_rendering() {

        // Create a mixe drag-and-drop question.
        $dd = test_question_maker::make_question('ddimageortext', 'mixedlang');
        $dd->shufflechoices = false;
        $this->start_attempt_at_question($dd, 'interactive', 1);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
                new question_pattern_expectation('~<div class="group1 draghome choice1"><span lang="fr">la</span></div>~'),
                new question_pattern_expectation('~<div class="group1 draghome choice2"><span lang="fr">ma</span></div>~')
        );
    }
}
