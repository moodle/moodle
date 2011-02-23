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
 * @subpackage ddwtos
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Test helper class for the drag-and-drop words into sentences question type.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddwtos_test_helper {
    /**
     * @return qtype_ddwtos_question
     */
    public static function make_a_ddwtos_question() {
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
     * @return qtype_ddwtos_question
     */
    public static function make_a_maths_ddwtos_question() {
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
}
