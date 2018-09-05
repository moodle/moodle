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
 * Test helpers for the randomsamatch question type.
 *
 * @package    qtype_randomsamatch
 * @copyright  2013 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/randomsamatch/question.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Test helper class for the randomsamatch question type.
 *
 * @copyright  2013 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_randomsamatch_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('animals');
    }

    /**
     * Makes a randomsamatch question similar to the match question returned
     * by {@link make_a_matching_question}, but with no 'insect' distractor.
     * @return qtype_randomsamatch_question
     */
    public function make_randomsamatch_question_animals() {
        question_bank::load_question_definition_classes('randomsamatch');
        $q = new qtype_randomsamatch_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Random shortanswer matching question';
        $q->questiontext = 'Classify the animals.';
        $q->generalfeedback = 'Frogs and toads are amphibians, the others are mammals.';
        $q->qtype = question_bank::get_qtype('randomsamatch');
        test_question_maker::set_standard_combined_feedback_fields($q);
        $q->shufflestems = false;
        $q->stems = array();
        $q->choices = array();
        $q->right = array();
        // Now we create 4 shortanswers question,
        // but we just fill the needed fields.
        question_bank::load_question_definition_classes('shortanswer');
        $sa1 = new qtype_shortanswer_question();
        test_question_maker::initialise_a_question($sa1);
        $sa1->id = 25;
        $sa1->questiontext = 'Dog';
        $sa1->answers = array(
            13 => new question_answer(13, 'Mammal', 1.0, 'Correct.', FORMAT_HTML),
            14 => new question_answer(14, 'Animal', 0.5, 'There is a betterresponse.', FORMAT_HTML),
            15 => new question_answer(15, '*', 0.0, 'That is a bad answer.', FORMAT_HTML),
        );
        $sa1->qtype = question_bank::get_qtype('shortanswer');

        $sa2 = new qtype_shortanswer_question();
        test_question_maker::initialise_a_question($sa2);
        $sa2->id = 26;
        $sa2->questiontext = 'Frog';
        $sa2->answers = array(
            16 => new question_answer(16, 'Amphibian', 1.0, 'Correct.', FORMAT_HTML),
            17 => new question_answer(17, 'A Prince', 1.0, 'Maybe.', FORMAT_HTML),
            18 => new question_answer(18, '*', 0.0, 'That is a bad answer.', FORMAT_HTML),
        );
        $sa2->qtype = question_bank::get_qtype('shortanswer');

        $sa3 = new qtype_shortanswer_question();
        test_question_maker::initialise_a_question($sa3);
        $sa3->id = 27;
        $sa3->questiontext = 'Toad';
        $sa3->answers = array(
            19 => new question_answer(19, 'Amphibian', 1.0, 'Correct.', FORMAT_HTML),
            20 => new question_answer(20, '*', 0.0, 'That is a bad answer.', FORMAT_HTML),
        );
        $sa3->qtype = question_bank::get_qtype('shortanswer');

        $sa4 = new qtype_shortanswer_question();
        test_question_maker::initialise_a_question($sa4);
        $sa4->id = 28;
        $sa4->questiontext = 'Cat';
        $sa4->answers = array(
            21 => new question_answer(21, 'Mammal', 1.0, 'Correct.', FORMAT_HTML),
        );
        $sa4->qtype = question_bank::get_qtype('shortanswer');
        $q->questionsloader = new qtype_randomsamatch_test_question_loader(array(), 4, array($sa1, $sa2, $sa3, $sa4));
        return $q;
    }
}

/**
 * Test implementation of {@link qtype_randomsamatch_question_loader}. Gets the questions
 * from an array passed to the constructor, rather than querying the database.
 *
 * @copyright  2013 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_randomsamatch_test_question_loader extends qtype_randomsamatch_question_loader {
    /** @var array hold available shortanswers questions to choose from. */
    protected $questions;

    /**
     * Constructor
     * @param array $availablequestions not used for tests.
     * @param int $choose how many questions to load (not used here).
     * @param array $questions array of questions to use.
     */
    public function __construct($availablequestions, $choose, $questions) {
        parent::__construct($availablequestions, $choose);
        $this->questions = $questions;
    }

    /**
     * Just return the shortanswers questions passed to the constructor.
     * @return array of short answer questions.
     */
    public function load_questions() {
        return $this->questions;
    }
}
