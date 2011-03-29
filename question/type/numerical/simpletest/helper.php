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
 * Test helpers for the drag-and-drop words into sentences question type.
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the drag-and-drop words into sentences question type.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('pi', 'unit', 'currency');
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
            13 => new qtype_numerical_answer(13, '3.14', 1.0, 'Very good.', FORMAT_HTML, 0),
            14 => new qtype_numerical_answer(14, '3.142', 0.0, 'Too accurate.', FORMAT_HTML, 0.005),
            15 => new qtype_numerical_answer(15, '3.1', 0.0, 'Not accurate enough.', FORMAT_HTML, 0.05),
            16 => new qtype_numerical_answer(16, '3', 0.0, 'Not accurate enough.', FORMAT_HTML, 0.5),
            17 => new qtype_numerical_answer(17, '*', 0.0, 'Completely wrong.', FORMAT_HTML, 0),
        );
        $num->qtype = question_bank::get_qtype('numerical');
        $num->unitdisplay = qtype_numerical::UNITOPTIONAL;
        $num->unitgradingtype = 0;
        $num->unitpenalty = 0.2;
        $num->ap = new qtype_numerical_answer_processor(array());

        return $num;
    }

    /**
     * Makes a numerical question with correct ansewer 3.14, and various incorrect
     * answers with different feedback.
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
        $num->ap = new qtype_numerical_answer_processor(array('m' => 1, 'cm' => 0.01));

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
