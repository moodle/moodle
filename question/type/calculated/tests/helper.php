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
 * @subpackage calculated
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/calculated/question.php');
require_once($CFG->dirroot . '/question/type/numerical/question.php');
require_once($CFG->dirroot . '/question/type/numerical/questiontype.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Test helper class for the calculated question type.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('sum');
    }

    /**
     * Makes a calculated question about summing two numbers.
     * @return qtype_calculated_question
     */
    public function make_calculated_question_sum() {
        question_bank::load_question_definition_classes('calculated');
        $q = new qtype_calculated_question();
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
        $q->synchronised = false;

        $q->datasetloader = new qtype_calculated_test_dataset_loader(0, array(
            array('a' => 1, 'b' => 5),
            array('a' => 3, 'b' => 4),
            array('a' => 3, 'b' => 0.01416),
            array('a' => 31, 'b' => 0.01416),
        ));

        return $q;
    }

    /**
     * Makes a calculated question about summing two numbers.
     * @return qtype_calculated_question
     */
    public function get_calculated_question_data_sum() {
        question_bank::load_question_definition_classes('calculated');
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->qtype = 'calculated';
        $qdata->name = 'Simple sum';
        $qdata->questiontext = 'What is {a} + {b}?';
        $qdata->generalfeedback = 'Generalfeedback: {={a} + {b}} is the right answer.';
        $qdata->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        $qdata->options = new stdClass();
        $qdata->options->unitgradingtype = 0;
        $qdata->options->unitpenalty = 0.0;
        $qdata->options->showunits = qtype_numerical::UNITNONE;
        $qdata->options->unitsleft = 0;
        $qdata->options->synchronize = 0;

        $qdata->options->answers = array(
            13 => new qtype_numerical_answer(13, '{a} + {b}', 1.0, 'Very good.', FORMAT_HTML, 0.001),
            14 => new qtype_numerical_answer(14, '{a} - {b}', 0.0, 'Add. not subtract!.',
                    FORMAT_HTML, 0.001),
            17 => new qtype_numerical_answer(17, '*', 0.0, 'Completely wrong.', FORMAT_HTML, 0),
        );
        foreach ($qdata->options->answers as $answer) {
            $answer->correctanswerlength = 2;
            $answer->correctanswerformat = 1;
        }

        $qdata->options->units = array();

        return $qdata;
    }

    /**
     * Makes a calculated question about summing two numbers.
     * @return qtype_calculated_question
     */
    public function get_calculated_question_form_data_sum() {
        question_bank::load_question_definition_classes('calculated');
        $fromform = new stdClass();

        $fromform->name = 'Simple sum';
        $fromform->questiontext = 'What is {a} + {b}?';
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = 'Generalfeedback: {={a} + {b}} is the right answer.';

        $fromform->unitrole = '3';
        $fromform->unitpenalty = 0.1;
        $fromform->unitgradingtypes = '1';
        $fromform->unitsleft = '0';
        $fromform->nounits = 1;
        $fromform->multiplier = array();
        $fromform->multiplier[0] = '1.0';
        $fromform->synchronize = 0;
        $fromform->answernumbering = 0;
        $fromform->shuffleanswers = 0;

        $fromform->noanswers = 6;
        $fromform->answer = array();
        $fromform->answer[0] = '{a} + {b}';
        $fromform->answer[1] = '{a} - {b}';
        $fromform->answer[2] = '*';

        $fromform->fraction = array();
        $fromform->fraction[0] = '1.0';
        $fromform->fraction[1] = '0.0';
        $fromform->fraction[2] = '0.0';

        $fromform->tolerance = array();
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

        $fromform->feedback = array();
        $fromform->feedback[0] = array();
        $fromform->feedback[0]['format'] = FORMAT_HTML;
        $fromform->feedback[0]['text'] = 'Very good.';

        $fromform->feedback[1] = array();
        $fromform->feedback[1]['format'] = FORMAT_HTML;
        $fromform->feedback[1]['text'] = 'Add. not subtract!';

        $fromform->feedback[2] = array();
        $fromform->feedback[2]['format'] = FORMAT_HTML;
        $fromform->feedback[2]['text'] = 'Completely wrong.';

        $fromform->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        return $fromform;
    }
}


/**
 * Test implementation of {@link qtype_calculated_dataset_loader}. Gets the values
 * from an array passed to the constructor, rather than querying the database.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated_test_dataset_loader extends qtype_calculated_dataset_loader{
    protected $valuesets;
    protected $aresynchronised = array();

    public function __construct($questionid, array $valuesets) {
        parent::__construct($questionid);
        $this->valuesets = $valuesets;
    }

    public function get_number_of_items() {
        return count($this->valuesets);
    }

    public function load_values($itemnumber) {
        return $this->valuesets[$itemnumber - 1];
    }

    public function datasets_are_synchronised($category) {
        return !empty($this->aresynchronised[$category]);
    }

    /**
     * Allows the test to mock the return value of {@link datasets_are_synchronised()}.
     * @param int $category
     * @param bool $aresychronised
     */
    public function set_are_synchronised($category, $aresychronised) {
        $this->aresynchronised[$category] = $aresychronised;
    }
}
