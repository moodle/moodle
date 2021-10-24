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
 * Test helpers for the match question type.
 *
 * @package    qtype_match
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/match/question.php');


/**
 * Test helper class for the match question type.
 *
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_match_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('foursubq', 'trickynums');
    }

    /**
     * Makes a match question about completing two blanks in some text.
     * @return object the question definition data, as it might be returned from
     * get_question_options.
     */
    public function get_match_question_data_foursubq() {
        global $USER;
        $q = new stdClass();
        test_question_maker::initialise_question_data($q);
        $q->name = 'Matching question';
        $q->qtype = 'match';
        $q->parent = 0;
        $q->questiontext = 'Classify the animals.';
        $q->questiontextformat = FORMAT_HTML;
        $q->generalfeedback = 'General feedback.';
        $q->generalfeedbackformat = FORMAT_HTML;
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;
        $q->length = 1;
        $q->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $q->versionid = 0;
        $q->version = 1;
        $q->questionbankentryid = 0;
        $q->createdby = $USER->id;
        $q->modifiedby = $USER->id;

        $q->options = new stdClass();
        $q->options->shuffleanswers = 1;
        test_question_maker::set_standard_combined_feedback_fields($q->options);

        $q->options->subquestions = array(
            14 => (object) array(
                'id' => 14,
                'questiontext' => 'frog',
                'questiontextformat' => FORMAT_HTML,
                'answertext' => 'amphibian'),
            15 => (object) array(
                'id' => 15,
                'questiontext' => 'cat',
                'questiontextformat' => FORMAT_HTML,
                'answertext' => 'mammal'),
            16 => (object) array(
                'id' => 16,
                'questiontext' => 'newt',
                'questiontextformat' => FORMAT_HTML,
                'answertext' => 'amphibian'),
            17 => (object) array(
                'id' => 17,
                'questiontext' => '',
                'questiontextformat' => FORMAT_HTML,
                'answertext' => 'insect'),
        );

        return $q;
    }

    /**
     * Makes a match question about completing two blanks in some text.
     * @return object the question definition data, as it might be returned from
     *      the question editing form.
     */
    public function get_match_question_form_data_foursubq() {
        $q = new stdClass();
        $q->name = 'Matching question';
        $q->questiontext = array('text' => 'Classify the animals.', 'format' => FORMAT_HTML);
        $q->generalfeedback = array('text' => 'General feedback.', 'format' => FORMAT_HTML);
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;
        $q->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $q->versionid = 0;
        $q->version = 1;
        $q->questionbankentryid = 0;

        $q->shuffleanswers = 1;
        test_question_maker::set_standard_combined_feedback_form_data($q);

        $q->subquestions = array(
            0 => array('text' => 'frog', 'format' => FORMAT_HTML),
            1 => array('text' => 'cat', 'format' => FORMAT_HTML),
            2 => array('text' => 'newt', 'format' => FORMAT_HTML),
            3 => array('text' => '', 'format' => FORMAT_HTML));

        $q->subanswers = array(
            0 => 'amphibian',
            1 => 'mammal',
            2 => 'amphibian',
            3 => 'insect'
        );

        $q->noanswers = 4;

        return $q;
    }

    /**
     * Makes a matching question to classify 'Dog', 'Frog', 'Toad' and 'Cat' as
     * 'Mammal', 'Amphibian' or 'Insect'.
     * defaultmark 1. Stems are shuffled by default.
     * @return qtype_match_question
     */
    public static function make_match_question_foursubq() {
        question_bank::load_question_definition_classes('match');
        $match = new qtype_match_question();
        test_question_maker::initialise_a_question($match);
        $match->name = 'Matching question';
        $match->questiontext = 'Classify the animals.';
        $match->generalfeedback = 'Frogs and toads are amphibians, the others are mammals.';
        $match->qtype = question_bank::get_qtype('match');

        $match->shufflestems = 1;

        test_question_maker::set_standard_combined_feedback_fields($match);

        // Using unset to get 1-based arrays.
        $match->stems = array('', 'Dog', 'Frog', 'Toad', 'Cat');
        $match->stemformat = array('', FORMAT_HTML, FORMAT_HTML, FORMAT_HTML, FORMAT_HTML);
        $match->choices = array('', 'Mammal', 'Amphibian', 'Insect');
        $match->right = array('', 1, 2, 2, 1);
        unset($match->stems[0]);
        unset($match->stemformat[0]);
        unset($match->choices[0]);
        unset($match->right[0]);

        return $match;
    }

    /**
     * Makes a matching question with choices including '0' and '0.0'.
     *
     * @return object the question definition data, as it might be returned from
     * get_question_options.
     */
    public function get_match_question_data_trickynums() {
        global $USER;

        $q = new stdClass();
        test_question_maker::initialise_question_data($q);
        $q->name = 'Java matching';
        $q->qtype = 'match';
        $q->parent = 0;
        $q->questiontext = 'What is the output of each of these lines of code?';
        $q->questiontextformat = FORMAT_HTML;
        $q->generalfeedback = 'Java has some advantages over PHP I guess!';
        $q->generalfeedbackformat = FORMAT_HTML;
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;
        $q->length = 1;
        $q->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $q->versionid = 0;
        $q->version = 1;
        $q->questionbankentryid = 0;
        $q->createdby = $USER->id;
        $q->modifiedby = $USER->id;

        $q->options = new stdClass();
        $q->options->shuffleanswers = 1;
        test_question_maker::set_standard_combined_feedback_fields($q->options);

        $q->options->subquestions = array(
                14 => (object) array(
                        'id' => 14,
                        'questiontext' => 'System.out.println(0);',
                        'questiontextformat' => FORMAT_HTML,
                        'answertext' => '0'),
                15 => (object) array(
                        'id' => 15,
                        'questiontext' => 'System.out.println(0.0);',
                        'questiontextformat' => FORMAT_HTML,
                        'answertext' => '0.0'),
                16 => (object) array(
                        'id' => 16,
                        'questiontext' => '',
                        'questiontextformat' => FORMAT_HTML,
                        'answertext' => 'NULL'),
        );

        return $q;
    }

    /**
     * Makes a match question about completing two blanks in some text.
     * @return object the question definition data, as it might be returned from
     *      the question editing form.
     */
    public function get_match_question_form_data_trickynums() {
        $q = new stdClass();
        $q->name = 'Java matching';
        $q->questiontext = ['text' => 'What is the output of each of these lines of code?', 'format' => FORMAT_HTML];
        $q->generalfeedback = ['text' => 'Java has some advantages over PHP I guess!', 'format' => FORMAT_HTML];
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;
        $q->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $q->versionid = 0;
        $q->version = 1;
        $q->questionbankentryid = 0;

        $q->shuffleanswers = 1;
        test_question_maker::set_standard_combined_feedback_form_data($q);

        $q->subquestions = array(
            0 => array('text' => 'System.out.println(0);', 'format' => FORMAT_HTML),
            1 => array('text' => 'System.out.println(0.0);', 'format' => FORMAT_HTML),
            2 => array('text' => '', 'format' => FORMAT_HTML),
        );

        $q->subanswers = array(
            0 => '0',
            1 => '0.0',
            2 => 'NULL',
        );

        $q->noanswers = 3;

        return $q;
    }

    /**
     * Makes a matching question with choices including '0' and '0.0'.
     *
     * @return qtype_match_question
     */
    public static function make_match_question_trickynums() {
        question_bank::load_question_definition_classes('match');
        $match = new qtype_match_question();
        test_question_maker::initialise_a_question($match);
        $match->name = 'Java matching';
        $match->questiontext = 'What is the output of each of these lines of code?';
        $match->generalfeedback = 'Java has some advantages over PHP I guess!';
        $match->qtype = question_bank::get_qtype('match');

        $match->shufflestems = 1;

        test_question_maker::set_standard_combined_feedback_fields($match);

        // Using unset to get 1-based arrays.
        $match->stems = array('', 'System.out.println(0);', 'System.out.println(0.0);');
        $match->stemformat = array('', FORMAT_HTML, FORMAT_HTML);
        $match->choices = array('', '0', '0.0', 'NULL');
        $match->right = array('', 1, 2);
        unset($match->stems[0]);
        unset($match->stemformat[0]);
        unset($match->choices[0]);
        unset($match->right[0]);

        return $match;
    }
}
