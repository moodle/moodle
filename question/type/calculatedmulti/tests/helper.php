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

    /**
     * Return the form data for a question with a single response.
     *
     * @return stdClass
     */
    public function get_calculatedmulti_question_form_data_singleresponse(): stdClass {
        question_bank::load_question_definition_classes('calculated');
        $fromform = new stdClass();

        $fromform->name = 'Simple sum';
        $fromform->questiontext['text'] = 'What is {a} + {b}?';
        $fromform->questiontext['format'] = FORMAT_HTML;
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback['text'] = 'Generalfeedback: {={a} + {b}} is the right answer.';
        $fromform->generalfeedback['format'] = FORMAT_HTML;

        $fromform->unitrole = '3';
        $fromform->unitpenalty = 0.1;
        $fromform->unitgradingtypes = '1';
        $fromform->unitsleft = '0';
        $fromform->nounits = 1;
        $fromform->multiplier = [];
        $fromform->multiplier[0] = '1.0';
        $fromform->synchronize = 0;
        $fromform->answernumbering = 0;
        $fromform->shuffleanswers = 0;
        $fromform->single = 1;
        $fromform->correctfeedback['text'] = 'Very good';
        $fromform->correctfeedback['format'] = FORMAT_HTML;
        $fromform->partiallycorrectfeedback['text'] = 'Mostly good';
        $fromform->partiallycorrectfeedback['format'] = FORMAT_HTML;
        $fromform->incorrectfeedback['text'] = 'Completely Wrong';
        $fromform->incorrectfeedback['format'] = FORMAT_HTML;
        $fromform->shownumcorrect = 1;

        $fromform->noanswers = 6;
        $fromform->answer = [];
        $fromform->answer[0]['text'] = '{a} + {b}';
        $fromform->answer[0]['format'] = FORMAT_HTML;
        $fromform->answer[1]['text'] = '{a} - {b}';
        $fromform->answer[1]['format'] = FORMAT_HTML;
        $fromform->answer[2]['text'] = '*';
        $fromform->answer[2]['format'] = FORMAT_HTML;

        $fromform->fraction = [];
        $fromform->fraction[0] = '1.0';
        $fromform->fraction[1] = '0.0';
        $fromform->fraction[2] = '0.0';

        $fromform->tolerance = [];
        $fromform->tolerance[0] = 0.001;
        $fromform->tolerance[1] = 0.001;
        $fromform->tolerance[2] = 0;

        $fromform->tolerancetype[0] = 1;
        $fromform->tolerancetype[1] = 1;
        $fromform->tolerancetype[2] = 1;

        $fromform->correctanswerlength[0] = 2;
        $fromform->correctanswerlength[1] = 2;
        $fromform->correctanswerlength[2] = 2;

        $fromform->correctanswerformat[0] = 1;
        $fromform->correctanswerformat[1] = 1;
        $fromform->correctanswerformat[2] = 1;

        $fromform->feedback = [];
        $fromform->feedback[0] = [];
        $fromform->feedback[0]['format'] = FORMAT_HTML;
        $fromform->feedback[0]['text'] = 'Very good.';

        $fromform->feedback[1] = [];
        $fromform->feedback[1]['format'] = FORMAT_HTML;
        $fromform->feedback[1]['text'] = 'Add. not subtract!';

        $fromform->feedback[2] = [];
        $fromform->feedback[2]['format'] = FORMAT_HTML;
        $fromform->feedback[2]['text'] = 'Completely wrong.';

        $fromform->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        $fromform->hint = [
            [
                'text' => 'Add',
                'format' => FORMAT_HTML,
            ],
        ];

        return $fromform;
    }
}
