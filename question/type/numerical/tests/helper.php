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
 * Test helpers for the numerical question type.
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the numerical question type.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('pi', 'unit', 'currency', 'pi3tries');
    }

    /**
     * Makes a numerical question with correct ansewer 3.14, and various incorrect
     * answers with different feedback.
     * @return qtype_numerical_question
     */
    public function make_numerical_question_pi() {
        question_bank::load_question_definition_classes('numerical');
        $num = new qtype_numerical_question();
        test_question_maker::initialise_a_question($num);
        $num->name = 'Pi to two d.p.';
        $num->questiontext = 'What is pi to two d.p.?';
        $num->generalfeedback = 'Generalfeedback: 3.14 is the right answer.';
        $num->answers = array(
            13 => new qtype_numerical_answer(13, '3.14',  1.0, 'Very good.',
                    FORMAT_HTML, 0),
            14 => new qtype_numerical_answer(14, '3.142', 0.0, 'Too accurate.',
                    FORMAT_HTML, 0.005),
            15 => new qtype_numerical_answer(15, '3.1',   0.0, 'Not accurate enough.',
                    FORMAT_HTML, 0.05),
            16 => new qtype_numerical_answer(16, '3',     0.0, 'Not accurate enough.',
                    FORMAT_HTML, 0.5),
            17 => new qtype_numerical_answer(17, '*',     0.0, 'Completely wrong.',
                    FORMAT_HTML, 0),
        );
        $num->qtype = question_bank::get_qtype('numerical');
        $num->unitdisplay = qtype_numerical::UNITOPTIONAL;
        $num->unitgradingtype = 0;
        $num->unitpenalty = 0.2;
        $num->ap = new qtype_numerical_answer_processor(array());

        return $num;
    }

    /**
     * Get the form data that corresponds to saving a numerical question.
     *
     * This question asks for Pi to two decimal places. It has feedback
     * for various wrong responses. There is hint data there, but
     * it is all blank, so no hints are created if this question is saved.
     *
     * @return stdClass simulated question form data.
     */
    public function get_numerical_question_form_data_pi() {
        $form = new stdClass();
        $form->name = 'Pi to two d.p.';
        $form->questiontext = array();
        $form->questiontext['format'] = '1';
        $form->questiontext['text'] = 'What is pi to two d.p.?';

        $form->defaultmark = 1;
        $form->generalfeedback = array();
        $form->generalfeedback['format'] = '1';
        $form->generalfeedback['text'] = 'Generalfeedback: 3.14 is the right answer.';

        $form->noanswers = 6;
        $form->answer = array();
        $form->answer[0] = '3.14';
        $form->answer[1] = '3.142';
        $form->answer[2] = '3.1';
        $form->answer[3] = '3';
        $form->answer[4] = '*';
        $form->answer[5] = '';

        $form->tolerance = array();
        $form->tolerance[0] = 0;
        $form->tolerance[1] = 0;
        $form->tolerance[2] = 0;
        $form->tolerance[3] = 0;
        $form->tolerance[4] = 0;
        $form->tolerance[5] = 0;

        $form->fraction = array();
        $form->fraction[0] = '1.0';
        $form->fraction[1] = '0.0';
        $form->fraction[2] = '0.0';
        $form->fraction[3] = '0.0';
        $form->fraction[4] = '0.0';
        $form->fraction[5] = '0.0';

        $form->feedback = array();
        $form->feedback[0] = array();
        $form->feedback[0]['format'] = '1';
        $form->feedback[0]['text'] = 'Very good.';

        $form->feedback[1] = array();
        $form->feedback[1]['format'] = '1';
        $form->feedback[1]['text'] = 'Too accurate.';

        $form->feedback[2] = array();
        $form->feedback[2]['format'] = '1';
        $form->feedback[2]['text'] = 'Not accurate enough.';

        $form->feedback[3] = array();
        $form->feedback[3]['format'] = '1';
        $form->feedback[3]['text'] = 'Not accurate enough.';

        $form->feedback[4] = array();
        $form->feedback[4]['format'] = '1';
        $form->feedback[4]['text'] = 'Completely wrong.';

        $form->feedback[5] = array();
        $form->feedback[5]['format'] = '1';
        $form->feedback[5]['text'] = '';

        $form->unitrole = '3';
        $form->unitpenalty = 0.1;
        $form->unitgradingtypes = '1';
        $form->unitsleft = '0';
        $form->nounits = 1;
        $form->multiplier = array();
        $form->multiplier[0] = '1.0';

        $form->penalty = '0.3333333';
        $form->numhints = 2;
        $form->hint = array();
        $form->hint[0] = array();
        $form->hint[0]['format'] = '1';
        $form->hint[0]['text'] = '';

        $form->hint[1] = array();
        $form->hint[1]['format'] = '1';
        $form->hint[1]['text'] = '';

        $form->qtype = 'numerical';
        return $form;
    }

    /**
     * Get the form data that corresponds to saving a numerical question.
     *
     * Like {@link get_numerical_question_form_data_pi()}, but
     * this time with two hints, making this suitable for use
     * with the Interactive with multiple tries behaviour.
     *
     * @return stdClass simulated question form data.
     */
    public function get_numerical_question_form_data_pi3tries() {
        $form = $this->get_numerical_question_form_data_pi();
        $form->hint[0]['text'] = 'First hint';
        $form->hint[1]['text'] = 'Second hint';
        return $form;
    }

    public function get_numerical_question_data_pi() {
        $q = new stdClass();
        $q->name = 'Pi to two d.p.';
        $q->questiontext = 'What is pi to two d.p.?';
        $q->questiontextformat = FORMAT_HTML;
        $q->generalfeedback = 'Generalfeedback: 3.14 is the right answer.';
        $q->generalfeedbackformat = FORMAT_HTML;
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;
        $q->qtype = 'numerical';
        $q->length = '1';
        $q->hidden = '0';
        $q->createdby = '2';
        $q->modifiedby = '2';
        $q->options = new stdClass();
        $q->options->answers = array();
        $q->options->answers[0] = new stdClass();
        $q->options->answers[0]->answer = '3.14';
        $q->options->answers[0]->fraction = '1.0000000';
        $q->options->answers[0]->feedback = 'Very good.';
        $q->options->answers[0]->feedbackformat = FORMAT_HTML;
        $q->options->answers[0]->tolerance = '0';

        $q->options->answers[1] = new stdClass();
        $q->options->answers[1]->answer = '3.142';
        $q->options->answers[1]->fraction = '0.0000000';
        $q->options->answers[1]->feedback = 'Too accurate.';
        $q->options->answers[1]->feedbackformat = FORMAT_HTML;
        $q->options->answers[1]->tolerance = '0';

        $q->options->answers[2] = new stdClass();
        $q->options->answers[2]->answer = '3.1';
        $q->options->answers[2]->fraction = '0.0000000';
        $q->options->answers[2]->feedback = 'Not accurate enough.';
        $q->options->answers[2]->feedbackformat = FORMAT_HTML;
        $q->options->answers[2]->tolerance = '0';

        $q->options->answers[3] = new stdClass();
        $q->options->answers[3]->answer = '3';
        $q->options->answers[3]->answerformat = '0';
        $q->options->answers[3]->fraction = '0.0000000';
        $q->options->answers[3]->feedback = 'Not accurate enough.';
        $q->options->answers[3]->feedbackformat = FORMAT_HTML;
        $q->options->answers[3]->tolerance = '0';

        $q->options->answers[4] = new stdClass();
        $q->options->answers[4]->answer = '*';
        $q->options->answers[4]->answerformat = '0';
        $q->options->answers[4]->fraction = '0.0000000';
        $q->options->answers[4]->feedback = 'Completely wrong.';
        $q->options->answers[4]->feedbackformat = FORMAT_HTML;
        $q->options->answers[4]->tolerance = '0';

        $q->options->units = array();

        $q->options->unitgradingtype = '0';
        $q->options->unitpenalty = '0.1000000';
        $q->options->showunits = '3';
        $q->options->unitsleft = '0';

        return $q;
    }

        /**
     * Makes a numerical question with a choice (select menu) of units.
     * @return qtype_numerical_question
     */
    public function make_numerical_question_unit() {
        question_bank::load_question_definition_classes('numerical');
        $num = new qtype_numerical_question();
        test_question_maker::initialise_a_question($num);
        $num->name = 'Numerical with units';
        $num->questiontext = 'What is 1 m + 25 cm?';
        $num->generalfeedback = 'Generalfeedback: 1.25m or 125cm is the right answer.';
        $num->answers = array(
            13 => new qtype_numerical_answer(13, '1.25', 1.0, 'Very good.', FORMAT_HTML, 0),
            14 => new qtype_numerical_answer(14, '1.25', 0.5, 'Vaguely right.', FORMAT_HTML, 0.05),
            17 => new qtype_numerical_answer(17, '*', 0.0, 'Completely wrong.', FORMAT_HTML, 0),
        );
        $num->qtype = question_bank::get_qtype('numerical');
        $num->unitdisplay = qtype_numerical::UNITSELECT;
        $num->unitgradingtype = qtype_numerical::UNITGRADEDOUTOFMARK;
        $num->unitpenalty = 0.5;
        $num->ap = new qtype_numerical_answer_processor(array('m' => 1, 'cm' => 100));

        return $num;
    }

    /**
     * Makes a numerical question with correct ansewer 3.14, and various incorrect
     * answers with different feedback.
     * @return qtype_numerical_question
     */
    public function make_numerical_question_currency() {
        question_bank::load_question_definition_classes('numerical');
        $num = new qtype_numerical_question();
        test_question_maker::initialise_a_question($num);
        $num->name = 'Add money';
        $num->questiontext = 'What is $666 + $666?';
        $num->generalfeedback = 'Generalfeedback: $1,332 is the right answer.';
        $num->answers = array(
            13 => new qtype_numerical_answer(13, '1332', 1.0, 'Very good.', FORMAT_HTML, 0),
            14 => new qtype_numerical_answer(14, '*', 0.0, 'Wrong.', FORMAT_HTML, 0),
        );
        $num->qtype = question_bank::get_qtype('numerical');
        $num->unitdisplay = qtype_numerical::UNITINPUT;
        $num->unitgradingtype = qtype_numerical::UNITGRADEDOUTOFMAX;
        $num->unitpenalty = 0.2;
        $num->ap = new qtype_numerical_answer_processor(array('$' => 1), true);

        return $num;
    }
}
