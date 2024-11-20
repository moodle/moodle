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
class qtype_gapselect_test_helper extends question_test_helper {

    public function get_test_questions() {
        return array('fox', 'maths', 'currency', 'multilang', 'missingchoiceno');
    }

    /**
     * Get data you would get by loading a typical select missing words question.
     *
     * @return stdClass as returned by question_bank::load_question_data for this qtype.
     */
    public static function get_gapselect_question_data_fox() {
        global $USER;

        $gapselect = new stdClass();
        $gapselect->id = 0;
        $gapselect->category = 0;
        $gapselect->contextid = 0;
        $gapselect->parent = 0;
        $gapselect->questiontextformat = FORMAT_HTML;
        $gapselect->generalfeedbackformat = FORMAT_HTML;
        $gapselect->defaultmark = 1;
        $gapselect->penalty = 0.3333333;
        $gapselect->length = 1;
        $gapselect->stamp = make_unique_id_code();
        $gapselect->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $gapselect->versionid = 0;
        $gapselect->version = 1;
        $gapselect->questionbankentryid = 0;
        $gapselect->idnumber = null;
        $gapselect->timecreated = time();
        $gapselect->timemodified = time();
        $gapselect->createdby = $USER->id;
        $gapselect->modifiedby = $USER->id;

        $gapselect->name = 'Selection from drop down list question';
        $gapselect->questiontext = 'The [[1]] brown [[2]] jumped over the [[3]] dog.';
        $gapselect->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $gapselect->qtype = 'gapselect';

        $gapselect->options = new stdClass();
        $gapselect->options->shuffleanswers = true;

        test_question_maker::set_standard_combined_feedback_fields($gapselect->options);

        $gapselect->options->answers = array(
            (object) array('answer' => 'quick', 'feedback' => '1'),
            (object) array('answer' => 'fox', 'feedback' => '2'),
            (object) array('answer' => 'lazy', 'feedback' => '3'),
            (object) array('answer' => 'assiduous', 'feedback' => '3'),
            (object) array('answer' => 'dog', 'feedback' => '2'),
            (object) array('answer' => 'slow', 'feedback' => '1'),
        );

        return $gapselect;
    }

    /**
     * Get data required to save a select missing words question where
     * the author missed out one of the group numbers.
     *
     * @return stdClass data to create a gapselect question.
     */
    public function get_gapselect_question_form_data_missingchoiceno() {
        $fromform = new stdClass();

        $fromform->name = 'Select missing words question';
        $fromform->questiontext = ['text' => 'The [[1]] sat on the [[3]].', 'format' => FORMAT_HTML];
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = ['text' => 'The right answer is: "The cat sat on the mat."', 'format' => FORMAT_HTML];
        $fromform->choices = [
                ['answer' => 'cat', 'choicegroup' => '1'],
                ['answer' => '',    'choicegroup' => '1'],
                ['answer' => 'mat', 'choicegroup' => '1'],
        ];
        test_question_maker::set_standard_combined_feedback_form_data($fromform);
        $fromform->shownumcorrect = 0;
        $fromform->penalty = 0.3333333;
        $fromform->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        return $fromform;
    }

    /**
     * Get an example gapselect question to use for testing. This examples has one of each item.
     * @return qtype_gapselect_question
     */
    public static function make_gapselect_question_fox() {
        question_bank::load_question_definition_classes('gapselect');
        $gapselect = new qtype_gapselect_question();

        test_question_maker::initialise_a_question($gapselect);

        $gapselect->name = 'Selection from drop down list question';
        $gapselect->questiontext = 'The [[1]] brown [[2]] jumped over the [[3]] dog.';
        $gapselect->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $gapselect->qtype = question_bank::get_qtype('gapselect');
        $gapselect->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

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
    public static function make_gapselect_question_maths() {
        question_bank::load_question_definition_classes('gapselect');
        $gapselect = new qtype_gapselect_question();

        test_question_maker::initialise_a_question($gapselect);

        $gapselect->name = 'Selection from drop down list question';
        $gapselect->questiontext = 'Fill in the operators to make this equation work: ' .
                '7 [[1]] 11 [[2]] 13 [[1]] 17 [[2]] 19 = 3';
        $gapselect->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $gapselect->qtype = question_bank::get_qtype('gapselect');
        $gapselect->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        $gapselect->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($gapselect);

        $gapselect->choices = array(
            1 => array(
                1 => new qtype_gapselect_choice('+', 1),
                2 => new qtype_gapselect_choice('-', 1),
                3 => new qtype_gapselect_choice('*', 1),
                4 => new qtype_gapselect_choice('/', 1),
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
    public static function make_gapselect_question_multilang() {
        question_bank::load_question_definition_classes('gapselect');
        $gapselect = new qtype_gapselect_question();

        test_question_maker::initialise_a_question($gapselect);

        $gapselect->name = 'Multilang select missing words question';
        $gapselect->questiontext = '<span lang="en" class="multilang">The </span><span lang="ru" class="multilang"></span>[[1]] ' .
            '<span lang="en" class="multilang">sat on the</span><span lang="ru" class="multilang">сидела на</span> [[2]].';
        $gapselect->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $gapselect->qtype = question_bank::get_qtype('gapselect');
        $gapselect->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        $gapselect->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($gapselect);

        $gapselect->choices = array(
                1 => array(
                    1 => new qtype_gapselect_choice('<span lang="en" class="multilang">cat</span><span lang="ru" ' .
                        'class="multilang">кошка</span>', 1),
                    2 => new qtype_gapselect_choice('<span lang="en" class="multilang">dog</span><span lang="ru" ' .
                        'class="multilang">пес</span>', 1)),
                2 => array(
                    1 => new qtype_gapselect_choice('<span lang="en" class="multilang">mat</span><span lang="ru" ' .
                        'class="multilang">коврике</span>', 2),
                    2 => new qtype_gapselect_choice('<span lang="en" class="multilang">bat</span><span lang="ru" ' .
                        'class="multilang">бита</span>', 2))
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
    public static function make_gapselect_question_currency() {
        question_bank::load_question_definition_classes('gapselect');
        $gapselect = new qtype_gapselect_question();

        test_question_maker::initialise_a_question($gapselect);

        $gapselect->name = 'Selection from currency like choices';
        $gapselect->questiontext = 'The price of the ball is [[1]] approx.';
        $gapselect->generalfeedback = 'The choice is yours';
        $gapselect->qtype = question_bank::get_qtype('gapselect');
        $gapselect->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

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

    /**
     * Just for backwards compatibility.
     *
     * @return qtype_gapselect_question
     */
    public static function make_a_gapselect_question() {
        debugging('qtype_gapselect_test_helper::make_a_gapselect_question is deprecated. ' .
                "Please use test_question_maker::make_question('gapselect') instead.");
        return self::make_gapselect_question_fox();
    }

    /**
     * Just for backwards compatibility.
     *
     * @return qtype_gapselect_question
     */
    public static function make_a_maths_gapselect_question() {
        debugging('qtype_gapselect_test_helper::make_a_maths_gapselect_question is deprecated. ' .
                "Please use test_question_maker::make_question('gapselect', 'maths') instead.");
        return self::make_gapselect_question_maths();
    }

    /**
     * Just for backwards compatibility.
     *
     * @return qtype_gapselect_question
     */
    public static function make_a_currency_gapselect_question() {
        debugging('qtype_gapselect_test_helper::make_a_currency_gapselect_question is deprecated. ' .
                "Please use test_question_maker::make_question('gapselect', 'currency') instead.");
        return self::make_gapselect_question_currency();
    }

    /**
     * Just for backwards compatibility.
     *
     * @return qtype_gapselect_question
     */
    public static function make_a_multilang_gapselect_question() {
        debugging('qtype_gapselect_test_helper::make_a_multilang_gapselect_question is deprecated. ' .
                "Please use test_question_maker::make_question('gapselect', 'multilang') instead.");
        return self::make_gapselect_question_multilang();
    }
}
