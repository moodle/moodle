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
 * This file contains tests that walks essay questions through some attempts.
 *
 * @package   qtype_essay
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the essay question type.
 *
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essay_walkthrough_test extends qbehaviour_walkthrough_test_base {

    protected function check_contains_textarea($name, $content = '', $height = 10) {
        $fieldname = $this->quba->get_field_prefix($this->slot) . $name;

        $this->assertTag(array('tag' => 'textarea',
                'attributes' => array('cols' => '60', 'rows' => $height,
                        'name' => $fieldname)),
                $this->currentoutput);

        if ($content) {
            $this->assertRegExp('/' . preg_quote(s($content), '/') . '/', $this->currentoutput);
        }
    }

    public function test_deferred_feedback_html_editor() {

        // Create a matching question.
        $q = test_question_maker::make_question('essay', 'editor');
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $prefix = $this->quba->get_field_prefix($this->slot);
        $fieldname = $prefix . 'answer';
        $response = '<p>The <b>cat</b> sat on the mat. Then it ate a <b>frog</b>.</p>';

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_contains_textarea('answer', '');
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(1);

        // Save a response.
        $this->quba->process_all_actions(null, array(
            'slots'                    => $this->slot,
            $fieldname                 => $response,
            $fieldname . 'format'      => FORMAT_HTML,
            $prefix . ':sequencecheck' => '1',
        ));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->render();
        $this->check_contains_textarea('answer', $response);
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(2);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$needsgrading);
        $this->check_current_mark(null);
        $this->render();
        $this->assertRegExp('/' . preg_quote($response, '/') . '/', $this->currentoutput);
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_contains_general_feedback_expectation($q));
    }

    public function test_deferred_feedback_plain_text() {

        // Create a matching question.
        $q = test_question_maker::make_question('essay', 'plain');
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $prefix = $this->quba->get_field_prefix($this->slot);
        $fieldname = $prefix . 'answer';
        $response = "x < 1\nx > 0\nFrog & Toad were friends.";

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_contains_textarea('answer', '');
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(1);

        // Save a response.
        $this->quba->process_all_actions(null, array(
            'slots'                    => $this->slot,
            $fieldname                 => $response,
            $fieldname . 'format'      => FORMAT_HTML,
            $prefix . ':sequencecheck' => '1',
        ));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->render();
        $this->check_contains_textarea('answer', $response);
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(2);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$needsgrading);
        $this->check_current_mark(null);
        $this->render();
        $this->assertRegExp('/' . preg_quote(s($response), '/') . '/', $this->currentoutput);
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_contains_general_feedback_expectation($q));
    }
}
