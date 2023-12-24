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
 * Test helpers for the calculated question type.
 *
 * @package    qtype
 * @subpackage calculatedmulti
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/calculatedmulti/question.php');
require_once($CFG->dirroot . '/question/type/calculated/tests/helper.php');

/**
 * Test helper class for the calculated multiple-choice question type.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculatedmulti_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('singleresponse', 'multiresponse');
    }

    /**
     * Makes a calculated multiple-choice question about summing two numbers.
     * @return qtype_calculatedmulti_single_question
     */
    public function make_calculatedmulti_question_singleresponse() {
        question_bank::load_question_definition_classes('calculated');
        $q = new qtype_calculatedmulti_single_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Simple sum';
        $q->questiontext = 'What is {a} + {b}?';
        $q->generalfeedback = 'Generalfeedback: {={a} + {b}} is the right answer.';
        $q->shuffleanswers = 0;
        $q->answernumbering = 'abc';
        $q->layout = 1;
        $q->correctfeedback = test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK;
        $q->correctfeedbackformat = FORMAT_HTML;
        $q->partiallycorrectfeedback = test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK;
        $q->partiallycorrectfeedbackformat = FORMAT_HTML;
        $q->shownumcorrect = 1;
        $q->incorrectfeedback = test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK;
        $q->incorrectfeedbackformat = FORMAT_HTML;
        $q->shownumcorrect = 1;
        $q->answers = array(
            13 => new \qtype_calculated\qtype_calculated_answer(13, '{={a} + {b}}', 1.0, 'Very good.', FORMAT_HTML, 0),
            14 => new \qtype_calculated\qtype_calculated_answer(14, '{={a} - {b}}', 0.0, 'Add. not subtract!', FORMAT_HTML, 0),
            17 => new \qtype_calculated\qtype_calculated_answer(17, '{={a} + 2 * {b}}', 0.0, 'Just add.', FORMAT_HTML, 0),
        );
        $q->answers[13]->correctanswerlength = 2;
        $q->answers[13]->correctanswerformat = 1;
        $q->answers[14]->correctanswerlength = 2;
        $q->answers[14]->correctanswerformat = 1;
        $q->answers[17]->correctanswerlength = 2;
        $q->answers[17]->correctanswerformat = 1;
        $q->qtype = question_bank::get_qtype('calculatedmulti');

        $q->datasetloader = new qtype_calculated_test_dataset_loader(0, array(
            array('a' => 1, 'b' => 5),
            array('a' => 3, 'b' => 4),
        ));

        return $q;
    }

    /**
     * Makes a calculated multiple-choice question with multiple right answers.
     * @return qtype_calculatedmulti_multi_question
     */
    public function make_calculatedmulti_question_multiresponse() {
        question_bank::load_question_definition_classes('calculated');
        $q = new qtype_calculatedmulti_multi_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Simple sum';
        $q->questiontext = 'What is {a} + {b}?';
        $q->generalfeedback = 'Generalfeedback: {={a} + {b}} is the right answer.';
        $q->shuffleanswers = 0;
        $q->answernumbering = 'abc';
        $q->layout = 1;
        $q->correctfeedback = test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK;
        $q->correctfeedbackformat = FORMAT_HTML;
        $q->partiallycorrectfeedback = test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK;
        $q->partiallycorrectfeedbackformat = FORMAT_HTML;
        $q->shownumcorrect = 1;
        $q->incorrectfeedback = test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK;
        $q->incorrectfeedbackformat = FORMAT_HTML;
        $q->shownumcorrect = 1;
        $q->answers = array(
                13 => new \qtype_calculated\qtype_calculated_answer(13, '{a} + {b}!', 0.5, 'Good', FORMAT_HTML, 0),
                14 => new \qtype_calculated\qtype_calculated_answer(14, '{={a} + {b}}', 0.5, 'Good',
                        FORMAT_HTML, 0),
                17 => new \qtype_calculated\qtype_calculated_answer(17, '{={a} - {b}}', -0.5, 'Wrong.', FORMAT_HTML, 0),
        );
        $q->answers[13]->correctanswerlength = 2;
        $q->answers[13]->correctanswerformat = 1;
        $q->answers[14]->correctanswerlength = 2;
        $q->answers[14]->correctanswerformat = 1;
        $q->answers[17]->correctanswerlength = 2;
        $q->answers[17]->correctanswerformat = 1;
        $q->qtype = question_bank::get_qtype('calculatedmulti');

        $q->datasetloader = new qtype_calculated_test_dataset_loader(0, array(
                array('a' => 1, 'b' => 5),
                array('a' => 3, 'b' => 4),
        ));

        return $q;
    }
}
