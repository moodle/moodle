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

require_once($CFG->dirroot . '/question/type/calculated/question.php');


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
