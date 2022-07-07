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
 * Test helpers for the truefalse question type.
 *
 * @package    qtype
 * @subpackage truefalse
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the truefalse question type.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_truefalse_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('true', 'false');
    }

    /**
     * Makes a truefalse question with correct answer true.
     * @return qtype_truefalse_question
     */
    public function make_truefalse_question_true() {
        question_bank::load_question_definition_classes('truefalse');
        $tf = new qtype_truefalse_question();
        test_question_maker::initialise_a_question($tf);
        $tf->name = 'True/false question';
        $tf->questiontext = 'The answer is true.';
        $tf->generalfeedback = 'You should have selected true.';
        $tf->penalty = 1;
        $tf->qtype = question_bank::get_qtype('truefalse');

        $tf->rightanswer = true;
        $tf->truefeedback = 'This is the right answer.';
        $tf->falsefeedback = 'This is the wrong answer.';
        $tf->truefeedbackformat = FORMAT_HTML;
        $tf->falsefeedbackformat = FORMAT_HTML;
        $tf->trueanswerid = 13;
        $tf->falseanswerid = 14;

        return $tf;
    }

    public function get_truefalse_question_form_data_true() {

        $form = new stdClass();
        $form->name = 'True/false question';
        $form->questiontext = array();
        $form->questiontext['format'] = '1';
        $form->questiontext['text'] = 'The answer is true.';

        $form->defaultmark = 1;
        $form->generalfeedback = array();
        $form->generalfeedback['format'] = '1';
        $form->generalfeedback['text'] = 'You should have selected true.';

        $form->correctanswer = '1';
        $form->feedbacktrue = array();
        $form->feedbacktrue['format'] = '1';
        $form->feedbacktrue['text'] = 'This is the right answer.';

        $form->feedbackfalse = array();
        $form->feedbackfalse['format'] = '1';
        $form->feedbackfalse['text'] = 'This is the wrong answer.';

        $form->penalty = 1;

        return $form;
    }

    function get_truefalse_question_data_true() {

        $q = new stdClass();
        $q->name = 'True/false question';
        $q->questiontext = 'The answer is true.';
        $q->questiontextformat = FORMAT_HTML;
        $q->generalfeedback = 'You should have selected true.';
        $q->generalfeedbackformat = FORMAT_HTML;
        $q->defaultmark = 1;
        $q->penalty = 1;
        $q->qtype = 'truefalse';
        $q->length = '1';
        $q->hidden = '0';
        $q->createdby = '2';
        $q->modifiedby = '2';
        $q->options = new stdClass();
        $q->options->trueanswer = '0';
        $q->options->falseanswer = '1';
        $q->options->answers = array();
        $q->options->answers[0] = new stdClass();
        $q->options->answers[0]->answer = 'True';
        $q->options->answers[0]->fraction = 1.0;
        $q->options->answers[0]->feedback = 'This is the right answer.';
        $q->options->answers[0]->feedbackformat = FORMAT_HTML;

        $q->options->answers[1] = new stdClass();
        $q->options->answers[1]->answer = 'False';
        $q->options->answers[1]->fraction = 0.0;
        $q->options->answers[1]->feedback = 'This is the wrong answer.';
        $q->options->answers[1]->feedbackformat = FORMAT_HTML;

        return $q;
    }

    /**
     * Makes a truefalse question with correct answer false.
     * @return qtype_truefalse_question
     */
    public function make_truefalse_question_false() {
        question_bank::load_question_definition_classes('truefalse');
        $tf = new qtype_truefalse_question();
        test_question_maker::initialise_a_question($tf);
        $tf->name = 'True/false question';
        $tf->questiontext = 'The answer is false.';
        $tf->generalfeedback = 'You should have selected false.';
        $tf->penalty = 1;
        $tf->qtype = question_bank::get_qtype('truefalse');

        $tf->rightanswer = false;
        $tf->truefeedback = 'This is the wrong answer.';
        $tf->falsefeedback = 'This is the right answer.';
        $tf->truefeedbackformat = FORMAT_HTML;
        $tf->falsefeedbackformat = FORMAT_HTML;
        $tf->trueanswerid = 13;
        $tf->falseanswerid = 14;

        return $tf;
    }
}
