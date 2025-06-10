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

namespace qbehaviour_deferredfeedbackexplain;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../engine/lib.php');
require_once(dirname(__FILE__) . '/../../../engine/tests/helpers.php');

/**
 * Walk-through tests for the deferred feedback with explanation behaviour.
 *
 * @package   qbehaviour_deferredfeedbackexplain
 * @copyright 2014 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class walkthrough_test extends \qbehaviour_walkthrough_test_base {

    /**
     * Assert that the current output contains a text area with given content.
     *
     * @param string $name name of the textarea. (Prefix is prepended.)
     * @param string $content the expected content.
     */
    protected function check_contains_textarea($name, $content) {
        $fieldname = $this->quba->get_field_prefix($this->slot) . $name;

        $this->assertTag(array('tag' => 'textarea',
                'attributes' => array('cols' => '60', 'rows' => 5, 'name' => $fieldname)),
                $this->currentoutput);

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression('/' . preg_quote(s($content), '/') . '/',
                    $this->currentoutput);
        } else {
            // Fallback for old PHPunit.
            $this->assertRegExp('/' . preg_quote(s($content), '/') . '/',
                    $this->currentoutput);
        }
    }

    public function test_deferredfeedbackexplain_truefalse() {
        global $PAGE;

        // Required to init a text editor.
        $this->setAdminUser();
        $PAGE->set_url('/');

        // Create a true-false question with correct answer true.
        $tf = \test_question_maker::make_question('truefalse', 'true');
        $this->start_attempt_at_question($tf, 'deferredfeedbackexplain', 1);

        // Verify.
        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_lang_string('notyetanswered', 'question');
        $this->check_contains_textarea('-explanation', '');
        $this->check_output_contains_lang_string('pleaseexplain', 'qbehaviour_deferredfeedbackexplain');
        $this->check_current_output(
                $this->get_contains_question_text_expectation($tf),
                $this->get_contains_tf_true_radio_expectation(true, false),
                $this->get_contains_tf_false_radio_expectation(true, false),
                $this->get_does_not_contain_feedback_expectation());

        // Save a response with no explanation.
        $this->process_submission(['answer' => 1, '-explanation' => '', '-explanationformat' => FORMAT_HTML]);

        // Verify.
        $this->check_current_state(\question_state::$complete);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_lang_string('answersaved', 'question');
        $this->check_contains_textarea('-explanation', '');
        $this->check_output_contains_lang_string('pleaseexplain', 'qbehaviour_deferredfeedbackexplain');
        $this->check_current_output(
                $this->get_contains_tf_true_radio_expectation(true, true),
                $this->get_contains_tf_false_radio_expectation(true, false),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation());

        // Save a response adding an explanation.
        $this->process_submission(['answer' => 1, '-explanation' => 'I just know this', '-explanationformat' => FORMAT_HTML]);

        // Verify.
        $this->check_current_state(\question_state::$complete);
        $this->check_current_mark(null);
        $this->render();
        $this->check_output_contains_lang_string('answersaved', 'question');
        $this->check_contains_textarea('-explanation', 'I just know this');
        $this->check_output_contains_lang_string('pleaseexplain', 'qbehaviour_deferredfeedbackexplain');
        $this->check_current_output(
                $this->get_contains_tf_true_radio_expectation(true, true),
                $this->get_contains_tf_false_radio_expectation(true, false),
                $this->get_does_not_contain_correctness_expectation(),
                $this->get_does_not_contain_feedback_expectation());

        // Process the same data again, check it does not create a new step.
        $numsteps = $this->get_step_count();
        $this->process_submission(['answer' => 1, '-explanation' => 'I just know this', '-explanationformat' => FORMAT_HTML]);
                $this->check_step_count($numsteps);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(\question_state::$gradedright);
        $this->check_current_mark(1);
        $this->render();
        $this->check_output_contains_lang_string('pleaseexplain', 'qbehaviour_deferredfeedbackexplain');
        $this->check_current_output(
                $this->get_contains_tf_true_radio_expectation(false, true),
                $this->get_contains_tf_false_radio_expectation(false, false),
                $this->get_contains_correct_expectation());
        $this->check_output_contains('I just know this');
        $this->assertEquals('True | Reason: I just know this', $this->quba->get_response_summary($this->slot));
    }
}
