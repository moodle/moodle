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
 * @package   qtype_ddwtos
 * @copyright 2010 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the drag-and-drop words into sentences question type.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddwtos_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('fox', 'maths', 'oddgroups', 'missingchoiceno', 'infinite');
    }

    /**
     * @return qtype_ddwtos_question
     */
    public function make_ddwtos_question_fox() {
        question_bank::load_question_definition_classes('ddwtos');
        $dd = new qtype_ddwtos_question();

        test_question_maker::initialise_a_question($dd);

        $dd->name = 'Drag-and-drop words into sentences question';
        $dd->questiontext = 'The [[1]] brown [[2]] jumped over the [[3]] dog.';
        $dd->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $dd->qtype = question_bank::get_qtype('ddwtos');

        $dd->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($dd);

        $dd->choices = array(
            1 => array(
                1 => new qtype_ddwtos_choice('quick', 1),
                2 => new qtype_ddwtos_choice('slow', 1)),
            2 => array(
                1 => new qtype_ddwtos_choice('fox', 2),
                2 => new qtype_ddwtos_choice('dog', 2)),
            3 => array(
                1 => new qtype_ddwtos_choice('lazy', 3),
                2 => new qtype_ddwtos_choice('assiduous', 3)),
        );

        $dd->places = array(1 => 1, 2 => 2, 3 => 3);
        $dd->rightchoices = array(1 => 1, 2 => 1, 3 => 1);
        $dd->textfragments = array('The ', ' brown ', ' jumped over the ', ' dog.');

        return $dd;
    }

    /**
     * This is a simple question with choices in three groups.
     *
     * @return stdClass data to create a ddwtos question.
     */
    public function get_ddwtos_question_form_data_fox() {
        $fromform = new stdClass();

        $fromform->name = 'Drag-and-drop words into sentences question';
        $fromform->questiontext = array('text' => 'The [[1]] brown [[2]] jumped over the [[3]] dog.', 'format' => FORMAT_HTML);
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = array('text' => 'This sentence uses each letter of the alphabet.', 'format' => FORMAT_HTML);
        $fromform->choices = array(
            array('answer' => 'quick',     'choicegroup' => '1'),
            array('answer' => 'fox',       'choicegroup' => '2'),
            array('answer' => 'lazy',      'choicegroup' => '3'),
            array('answer' => 'slow',      'choicegroup' => '1'),
            array('answer' => 'dog',       'choicegroup' => '2'),
            array('answer' => 'assiduous', 'choicegroup' => '3'),
        );
        test_question_maker::set_standard_combined_feedback_form_data($fromform);
        $fromform->shownumcorrect = 0;
        $fromform->penalty = 0.3333333;

        return $fromform;
    }

    /**
     * Similar to the 'fox' example above, but using different, non-continuous group numbers.
     *
     * @return stdClass data to create a ddwtos question.
     */
    public function get_ddwtos_question_form_data_oddgroups() {
        $fromform = new stdClass();

        $fromform->name = 'Drag-and-drop words with strange groups';
        $fromform->questiontext = array('text' => 'The [[1]] brown [[2]] jumped over the [[3]] dog.', 'format' => FORMAT_HTML);
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = array('text' => 'This sentence uses each letter of the alphabet.', 'format' => FORMAT_HTML);
        $fromform->choices = array(
            array('answer' => 'quick',     'choicegroup' => '5'),
            array('answer' => 'fox',       'choicegroup' => '7'),
            array('answer' => 'lazy',      'choicegroup' => '3'),
            array('answer' => 'slow',      'choicegroup' => '5'),
            array('answer' => 'dog',       'choicegroup' => '7'),
            array('answer' => 'assiduous', 'choicegroup' => '3'),
        );
        test_question_maker::set_standard_combined_feedback_form_data($fromform);
        $fromform->shownumcorrect = 0;
        $fromform->penalty = 0.3333333;

        return $fromform;
    }

    /**
     * Get data required to save a drag-drop into text question where the author
     * missed out one of the group numbers.
     *
     * @return stdClass data to create a ddwtos question.
     */
    public function get_ddwtos_question_form_data_missingchoiceno() {
        $fromform = new stdClass();

        $fromform->name = 'Drag-drop into text question with one index missing';
        $fromform->questiontext = ['text' => 'The [[1]] sat on the [[3]].', 'format' => FORMAT_HTML];
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = array('text' => 'The right answer is: "The cat sat on the mat."', 'format' => FORMAT_HTML);
        $fromform->choices = array(
                array('answer' => 'cat', 'choicegroup' => '1'),
                array('answer' => '',    'choicegroup' => '1'),
                array('answer' => 'mat', 'choicegroup' => '1'),
        );
        test_question_maker::set_standard_combined_feedback_form_data($fromform);
        $fromform->shownumcorrect = 0;
        $fromform->penalty = 0.3333333;

        return $fromform;
    }

    /**
     * @return qtype_ddwtos_question
     */
    public function make_ddwtos_question_maths() {
        question_bank::load_question_definition_classes('ddwtos');
        $dd = new qtype_ddwtos_question();

        test_question_maker::initialise_a_question($dd);

        $dd->name = 'Drag-and-drop words into sentences question';
        $dd->questiontext = 'Fill in the operators to make this equation work: ' .
                '7 [[1]] 11 [[2]] 13 [[1]] 17 [[2]] 19 = 3';
        $dd->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $dd->qtype = question_bank::get_qtype('ddwtos');

        $dd->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($dd);

        $dd->choices = array(
            1 => array(
                1 => new qtype_ddwtos_choice('+', 1, true),
                2 => new qtype_ddwtos_choice('-', 1, true),
                3 => new qtype_ddwtos_choice('*', 1, true),
                4 => new qtype_ddwtos_choice('/', 1, true),
            ));

        $dd->places = array(1 => 1, 2 => 1, 3 => 1, 4 => 1);
        $dd->rightchoices = array(1 => 1, 2 => 2, 3 => 1, 4 => 2);
        $dd->textfragments = array('7 ', ' 11 ', ' 13 ', ' 17 ', ' 19 = 3');

        return $dd;
    }

    /**
     * This is a simple question with infinite mode.
     *
     * @return stdClass data to create a ddwtos question.
     */
    public function get_ddwtos_question_form_data_infinite() {
        $fromform = new stdClass();

        $fromform->name = 'Drag-and-drop infinite question';
        $fromform->questiontext = ['text' => 'One [[1]] Two [[2]] Three [[3]]', 'format' => FORMAT_HTML];
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = ['text' => 'This is general feedback', 'format' => FORMAT_HTML];
        $fromform->choices = [
                ['answer' => 'Option1', 'choicegroup' => '1', 'infinite' => true],
                ['answer' => 'Option2', 'choicegroup' => '1', 'infinite' => true],
                ['answer' => 'Option3', 'choicegroup' => '1', 'infinite' => true]
        ];
        test_question_maker::set_standard_combined_feedback_form_data($fromform);
        $fromform->shownumcorrect = 0;
        $fromform->penalty = 0.3333333;

        return $fromform;
    }
}
