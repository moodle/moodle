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
 * Test helpers for the simple calculated question type.
 *
 * @package    qtype
 * @subpackage calculatedsimple
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/calculated/tests/helper.php');


/**
 * Test helper class for the simple calculated question type.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculatedsimple_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('sum', 'sumwithvariants');
    }

    /**
     * Makes a simple calculated question about summing two numbers.
     * @return qtype_calculatedsimple_question
     */
    public function make_calculatedsimple_question_sum() {
        question_bank::load_question_definition_classes('calculatedsimple');
        $q = new qtype_calculatedsimple_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Simple sum';
        $q->questiontext = 'What is {a} + {b}?';
        $q->generalfeedback = 'Generalfeedback: {={a} + {b}} is the right answer.';

        $q->answers = array(
            13 => new qtype_numerical_answer(13, '{a} + {b}', 1.0, 'Very good.', FORMAT_HTML, 0),
            14 => new qtype_numerical_answer(14, '{a} - {b}', 0.0, 'Add. not subtract!.',
                    FORMAT_HTML, 0),
            17 => new qtype_numerical_answer(17, '*', 0.0, 'Completely wrong.', FORMAT_HTML, 0),
        );
        foreach ($q->answers as $answer) {
            $answer->correctanswerlength = 2;
            $answer->correctanswerformat = 1;
        }

        $q->qtype = question_bank::get_qtype('calculated');
        $q->unitdisplay = qtype_numerical::UNITNONE;
        $q->unitgradingtype = 0;
        $q->unitpenalty = 0;
        $q->ap = new qtype_numerical_answer_processor(array());

        $q->datasetloader = new qtype_calculated_test_dataset_loader(0, array(
            array('a' => 1, 'b' => 5),
            array('a' => 3, 'b' => 4),
        ));

        return $q;
    }


    public function get_calculatedsimple_question_form_data_sumwithvariants() {
        $form = new stdClass();

        $form->name = 'Calculated simple';

        $form->qtype = 'calculatedsimple';

        $form->questiontext = array();
        $form->questiontext['text'] = '<p>This is a simple sum of two variables.</p>';
        $form->questiontext['format'] = '1';

        $form->defaultmark = 1;
        $form->generalfeedback = array();
        $form->generalfeedback['text'] = '<p>The answer is  {a} + {b}</p>';
        $form->generalfeedback['format'] = '1';

        $form->synchronize = 0;
        $form->initialcategory = 1;
        $form->reload = 1;
        $form->mform_isexpanded_id_answerhdr = 1;
        $form->noanswers = 1;
        $form->answer = array('{a} + {b}');

        $form->fraction = array('1.0');

        $form->tolerance = array(0.01);
        $form->tolerancetype = array('1');

        $form->correctanswerlength = array('2');
        $form->correctanswerformat = array('1');

        $form->feedback = array();
        $form->feedback[0] = array();
        $form->feedback[0]['text'] = '';
        $form->feedback[0]['format'] = '1';

        $form->unitrole = '3';
        $form->unitpenalty = 0.1;
        $form->unitgradingtypes = '1';
        $form->unitsleft = '0';
        $form->nounits = 1;
        $form->multiplier = array('1.0');

        $form->penalty = '0.3333333';
        $form->numhints = 2;
        $form->hint = array();
        $form->hint[0] = array();
        $form->hint[0]['text'] = '';
        $form->hint[0]['format'] = '1';

        $form->hint[1] = array();
        $form->hint[1]['text'] = '';
        $form->hint[1]['format'] = '1';

        $form->calcmin = array();
        $form->calcmin[1] = 1;
        $form->calcmin[2] = 1;

        $form->calcmax = array();
        $form->calcmax[1] = 10;
        $form->calcmax[2] = 10;

        $form->calclength = array();
        $form->calclength[1] = '1';
        $form->calclength[2] = '1';

        $form->calcdistribution = array();
        $form->calcdistribution[1] = 0;
        $form->calcdistribution[2] = 0;

        $form->datasetdef = array();
        $form->datasetdef[1] = '1-0-a';
        $form->datasetdef[2] = '1-0-b';

        $form->defoptions = array();
        $form->defoptions[1] = '';
        $form->defoptions[2] = '';

        $form->selectadd = '10';
        $form->selectshow = '10';
        $form->number = array();
        $form->number[1] = '2.3';
        $form->number[2] = '7.6';
        $form->number[3] = '2.1';
        $form->number[4] = '6.4';
        $form->number[5] = '1.4';
        $form->number[6] = '1.9';
        $form->number[7] = '9.9';
        $form->number[8] = '9.5';
        $form->number[9] = '9.0';
        $form->number[10] = '5.2';
        $form->number[11] = '2.1';
        $form->number[12] = '7.3';
        $form->number[13] = '7.9';
        $form->number[14] = '1.2';
        $form->number[15] = '2.3';
        $form->number[16] = '3.4';
        $form->number[17] = '1.9';
        $form->number[18] = '5.2';
        $form->number[19] = '3.4';
        $form->number[20] = '3.4';

        $form->itemid = array_fill(1, 20, 0);

        $form->definition = array();
        $form->definition[1] = '1-0-b';
        $form->definition[2] = '1-0-a';
        $form->definition[3] = '1-0-b';
        $form->definition[4] = '1-0-a';
        $form->definition[5] = '1-0-b';
        $form->definition[6] = '1-0-a';
        $form->definition[7] = '1-0-b';
        $form->definition[8] = '1-0-a';
        $form->definition[9] = '1-0-b';
        $form->definition[10] = '1-0-a';
        $form->definition[11] = '1-0-b';
        $form->definition[12] = '1-0-a';
        $form->definition[13] = '1-0-b';
        $form->definition[14] = '1-0-a';
        $form->definition[15] = '1-0-b';
        $form->definition[16] = '1-0-a';
        $form->definition[17] = '1-0-b';
        $form->definition[18] = '1-0-a';
        $form->definition[19] = '1-0-b';
        $form->definition[20] = '1-0-a';

        $form->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        return $form;
    }

    public function get_calculatedsimple_question_data_sumwithvariants() {
        global $USER;
        $q = new stdClass();

        $q->name = 'Calculated simple';
        $q->createdby = $USER->id;
        $q->questiontext = '<p>This is a simple sum of two variables.</p>';
        $q->questiontextformat = '1';
        $q->generalfeedback = '<p>The answer is  {a} + {b}</p>';
        $q->generalfeedbackformat = '1';
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;
        $q->qtype = 'calculatedsimple';
        $q->length = '1';
        $q->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $q->version = 1;
        $q->options = new stdClass();
        $q->options->synchronize = 0;
        $q->options->single = 0;
        $q->options->answernumbering = 'abc';
        $q->options->shuffleanswers = 0;
        $q->options->correctfeedback = '';
        $q->options->partiallycorrectfeedback = '';
        $q->options->incorrectfeedback = '';
        $q->options->correctfeedbackformat = 0;
        $q->options->partiallycorrectfeedbackformat = 0;
        $q->options->incorrectfeedbackformat = 0;
        $q->options->answers = array();
        $q->options->answers[0] = new stdClass();
        $q->options->answers[0]->id = '6977';
        $q->options->answers[0]->question = '3379';
        $q->options->answers[0]->answer = '{a} + {b}';
        $q->options->answers[0]->answerformat = '0';
        $q->options->answers[0]->fraction = 1.0;
        $q->options->answers[0]->feedback = '';
        $q->options->answers[0]->feedbackformat = '1';
        $q->options->answers[0]->tolerance = '0.01';
        $q->options->answers[0]->tolerancetype = '1';
        $q->options->answers[0]->correctanswerlength = '2';
        $q->options->answers[0]->correctanswerformat = '1';

        $q->options->units = array();

        $q->options->unitgradingtype = '0';
        $q->options->unitpenalty = 0.1;
        $q->options->showunits = '3';
        $q->options->unitsleft = '0';

        $q->hints = array();

        return $q;
    }
}
