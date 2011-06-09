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
