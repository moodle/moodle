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
 * Test helpers for the multianswer question type.
 *
 * @package    qtype
 * @subpackage multianswer
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/multianswer/question.php');


/**
 * Test helper class for the multianswer question type.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('twosubq');
    }

    /**
     * Makes a multianswer question about summing two numbers.
     * @return qtype_multianswer_question
     */
    public function make_multianswer_question_twosubq() {
        question_bank::load_question_definition_classes('multianswer');
        $q = new qtype_multianswer_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Simple multianswer';
        $q->questiontext =
                'Complete this opening line of verse: "The {#1} and the {#2} went to sea".';
        $q->generalfeedback = 'Generalfeedback: It\'s from "The Owl and the Pussy-cat" by Lear: ' .
                '"The owl and the pussycat went to see';
        $q->questiontextformat = FORMAT_HTML;
        $q->generalfeedbackformat = FORMAT_HTML;

        // Shortanswer subquestion.
        question_bank::load_question_definition_classes('shortanswer');
        $sa = new qtype_shortanswer_question();
        test_question_maker::initialise_a_question($sa);
        $sa->name = 'Simple multianswer';
        $sa->questiontext = '{1:SHORTANSWER:Dog#Wrong, silly!~=Owl#Well done!~*#Wrong answer}';
        $sa->questiontextformat = FORMAT_HTML;
        $sa->generalfeedback = '';
        $sa->generalfeedbackformat = FORMAT_HTML;
        $sa->usecase = true;
        $sa->answers = array(
            13 => new question_answer(13, 'Dog', 0.0, 'Wrong, silly!', FORMAT_HTML),
            14 => new question_answer(14, 'Owl', 1.0, 'Well done!', FORMAT_HTML),
            15 => new question_answer(15, '*', 0.0, 'Wrong answer', FORMAT_HTML),
        );
        $sa->qtype = question_bank::get_qtype('shortanswer');
        $sa->maxmark = 1;

        // Multiple-choice subquestion.
        question_bank::load_question_definition_classes('multichoice');
        $mc = new qtype_multichoice_single_question();
        test_question_maker::initialise_a_question($mc);
        $mc->name = 'Simple multianswer';
        $mc->questiontext = '{1:MULTICHOICE:Bow-wow#You seem to have a dog obsessions!' .
                '~Wiggly worm#Now you are just being rediculous!~=Pussy-cat#Well done!}';
        $mc->questiontextformat = FORMAT_HTML;
        $mc->generalfeedback = '';
        $mc->generalfeedbackformat = FORMAT_HTML;

        $mc->shuffleanswers = 1;
        $mc->answernumbering = 'none';
        $mc->layout = qtype_multichoice_base::LAYOUT_DROPDOWN;

        $mc->answers = array(
            13 => new question_answer(13, 'Bow-wow', 0,
                    'You seem to have a dog obsessions!', FORMAT_HTML),
            14 => new question_answer(14, 'Wiggly worm', 0,
                    'Now you are just being rediculous!', FORMAT_HTML),
            15 => new question_answer(15, 'Pussy-cat', 1,
                    'Well done!', FORMAT_HTML),
        );
        $mc->qtype = question_bank::get_qtype('multichoice');
        $mc->maxmark = 1;

        $q->subquestions = array(
            1 => $sa,
            2 => $mc,
        );

        return $q;
    }
}
