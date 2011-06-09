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

require_once($CFG->dirroot . '/question/type/calculated/simpletest/helper.php');


/**
 * Test helper class for the simple calculated question type.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculatedsimple_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('sum');
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
}
