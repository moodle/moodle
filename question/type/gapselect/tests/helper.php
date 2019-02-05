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
 * Contains the helper class for the select missing words question type tests.
 *
 * @package   qtype_gapselect
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the select missing words question type.
 *
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_gapselect_test_helper {
    /**
     * Get an example gapselect question to use for testing. This examples has one of each item.
     * @return qtype_gapselect_question
     */
    public static function make_a_gapselect_question() {
        question_bank::load_question_definition_classes('gapselect');
        $gapselect = new qtype_gapselect_question();

        test_question_maker::initialise_a_question($gapselect);

        $gapselect->name = 'Selection from drop down list question';
        $gapselect->questiontext = 'The [[1]] brown [[2]] jumped over the [[3]] dog.';
        $gapselect->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $gapselect->qtype = question_bank::get_qtype('gapselect');

        $gapselect->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($gapselect);

        $gapselect->choices = array(
            1 => array(
                1 => new qtype_gapselect_choice('quick', 1),
                2 => new qtype_gapselect_choice('slow', 1)),
            2 => array(
                1 => new qtype_gapselect_choice('fox', 2),
                2 => new qtype_gapselect_choice('dog', 2)),
            3 => array(
                1 => new qtype_gapselect_choice('lazy', 3),
                2 => new qtype_gapselect_choice('assiduous', 3)),
        );

        $gapselect->places = array(1 => 1, 2 => 2, 3 => 3);
        $gapselect->rightchoices = array(1 => 1, 2 => 1, 3 => 1);
        $gapselect->textfragments = array('The ', ' brown ', ' jumped over the ', ' dog.');

        return $gapselect;
    }

    /**
     * Get an example gapselect question to use for testing. This exmples had unlimited items.
     * @return qtype_gapselect_question
     */
    public static function make_a_maths_gapselect_question() {
        question_bank::load_question_definition_classes('gapselect');
        $gapselect = new qtype_gapselect_question();

        test_question_maker::initialise_a_question($gapselect);

        $gapselect->name = 'Selection from drop down list question';
        $gapselect->questiontext = 'Fill in the operators to make this equation work: ' .
                '7 [[1]] 11 [[2]] 13 [[1]] 17 [[2]] 19 = 3';
        $gapselect->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $gapselect->qtype = question_bank::get_qtype('gapselect');

        $gapselect->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($gapselect);

        $gapselect->choices = array(
            1 => array(
                1 => new qtype_gapselect_choice('+', 1, true),
                2 => new qtype_gapselect_choice('-', 1, true),
                3 => new qtype_gapselect_choice('*', 1, true),
                4 => new qtype_gapselect_choice('/', 1, true),
            ));

        $gapselect->places = array(1 => 1, 2 => 1, 3 => 1, 4 => 1);
        $gapselect->rightchoices = array(1 => 1, 2 => 2, 3 => 1, 4 => 2);
        $gapselect->textfragments = array('7 ', ' 11 ', ' 13 ', ' 17 ', ' 19 = 3');

        return $gapselect;
    }

    /**
     * Get an example gapselect question with multilang entries to use for testing.
     * @return qtype_gapselect_question
     */
    public static function make_a_multilang_gapselect_question() {
        question_bank::load_question_definition_classes('gapselect');
        $gapselect = new qtype_gapselect_question();

        test_question_maker::initialise_a_question($gapselect);

        $gapselect->name = 'Multilang select missing words question';
        $gapselect->questiontext = '<span lang="en" class="multilang">The </span><span lang="ru" class="multilang"></span>[[1]] ' .
            '<span lang="en" class="multilang">sat on the</span><span lang="ru" class="multilang">сидела на</span> [[2]].';
        $gapselect->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $gapselect->qtype = question_bank::get_qtype('gapselect');

        $gapselect->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($gapselect);

        $gapselect->choices = array(
                1 => array(
                    1 => new qtype_gapselect_choice('<span lang="en" class="multilang">cat</span><span lang="ru" ' .
                        'class="multilang">кошка</span>', 1, true),
                    2 => new qtype_gapselect_choice('<span lang="en" class="multilang">dog</span><span lang="ru" ' .
                        'class="multilang">пес</span>', 1, true)),
                2 => array(
                    1 => new qtype_gapselect_choice('<span lang="en" class="multilang">mat</span><span lang="ru" ' .
                        'class="multilang">коврике</span>', 2, true),
                    2 => new qtype_gapselect_choice('<span lang="en" class="multilang">bat</span><span lang="ru" ' .
                        'class="multilang">бита</span>', 2, true))
                );

        $gapselect->places = array(1 => 1, 2 => 2);
        $gapselect->rightchoices = array(1 => 1, 2 => 1);
        $gapselect->textfragments = array('<span lang="en" class="multilang">The </span><span lang="ru" class="multilang"></span>',
            ' <span lang="en" class="multilang">sat on the</span><span lang="ru" class="multilang">сидела на</span> ', '.');

        return $gapselect;
    }

    /**
     * This examples includes choices with currency like options.
     * @return qtype_gapselect_question
     */
    public static function make_a_currency_gapselect_question() {
        question_bank::load_question_definition_classes('gapselect');
        $gapselect = new qtype_gapselect_question();

        test_question_maker::initialise_a_question($gapselect);

        $gapselect->name = 'Selection from currency like choices';
        $gapselect->questiontext = 'The price of the ball is [[1]] approx.';
        $gapselect->generalfeedback = 'The choice is yours';
        $gapselect->qtype = question_bank::get_qtype('gapselect');

        $gapselect->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($gapselect);

        $gapselect->choices = [
                1 => [
                        1 => new qtype_gapselect_choice('$2', 1),
                        2 => new qtype_gapselect_choice('$3', 1),
                        3 => new qtype_gapselect_choice('$4.99', 1),
                        4 => new qtype_gapselect_choice('-1', 1)
                ]
        ];

        $gapselect->places = array(1 => 1);
        $gapselect->rightchoices = array(1 => 1);
        $gapselect->textfragments = array('The price of the ball is ', ' approx.');

        return $gapselect;
    }
}
