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
 * Test helper for the ordering question type.
 *
 * @package    qtype_ordering
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/ordering/question.php');

/**
 * Test helper for the ordering question type.
 *
 * The class has code to generate question data structures for sample ordering questions.
 *
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('moodle');
    }

    /**
     * Makes an ordering question to sort the words Modular Object Oriented Dynamic Learning Environment.
     *
     * @return qtype_ordering_question the question instance.
     */
    public function make_ordering_question_moodle() {
        question_bank::load_question_definition_classes('ordering');
        $q = new qtype_ordering_question();
        $q->questionid = $q->id;
        test_question_maker::initialise_a_question($q);
        $q->qtype = question_bank::get_qtype('ordering');
        $q->name = 'Moodle';
        $q->questiontext = 'Put these words in order';
        $q->generalfeedback = 'The correct answer is "Modular Object Oriented Dynamic Learning Environment".';
        test_question_maker::set_standard_combined_feedback_fields($q);
        $q->answers = [
            13 => $this->make_answer(13, 'Modular', FORMAT_HTML, 1, true),
            14 => $this->make_answer(14, 'Object', FORMAT_HTML, 2, true),
            15 => $this->make_answer(15, 'Oriented', FORMAT_HTML, 3, true),
            16 => $this->make_answer(16, 'Dynamic', FORMAT_HTML, 4, true),
            17 => $this->make_answer(17, 'Learning', FORMAT_HTML, 5, true),
            18 => $this->make_answer(18, 'Environment', FORMAT_HTML, 6, true),
        ];
        $q->options = new stdClass();
        $q->options->layouttype = qtype_ordering_question::LAYOUT_HORIZONTAL;
        $q->options->selecttype = qtype_ordering_question::SELECT_ALL;
        $q->options->selectcount = 0;
        $q->options->gradingtype = qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT;
        $q->options->showgrading = true;
        $q->options->numberingstyle = qtype_ordering_question::NUMBERING_STYLE_DEFAULT;
        return $q;
    }

    /**
     * Create an answer record to use in a test question.
     *
     * @param int $id the id to set.
     * @param string $text
     * @param int $textformat one of the FORMAT_... constanst.
     * @param int $order the position in order, numbered from 1.
     * @param bool $addmd5 whether to add the md5key property.
     * @return stdClass the answer.
     */
    protected function make_answer($id, $text, $textformat, $order, $addmd5 = false) {
        global $CFG;

        $answer = new stdClass();
        $answer->id = $id;
        $answer->question = 0;
        $answer->answer = $text;
        $answer->answerformat = $textformat;
        $answer->fraction = $order;
        $answer->feedback = '';
        $answer->feedbackformat = FORMAT_MOODLE;

        if ($addmd5) {
            if (isset($CFG->passwordsaltmain)) {
                $salt = $CFG->passwordsaltmain;
            } else {
                $salt = '';
            }
            $answer->md5key = 'ordering_item_' . md5($salt . $answer->answer);
        }

        return $answer;
    }

    /**
     * Get the form data that corresponds an ordering question.
     *
     * The question is to sort the words Modular Object Oriented Dynamic Learning Environment.
     *
     * @return stdClass simulated question form data.
     */
    public function get_ordering_question_form_data_moodle() {
        $form = new stdClass();
        $form->name = 'Moodle';
        $form->questiontext = ['text' => 'Put these words in order.', 'format' => FORMAT_HTML];
        $form->defaultmark = 1;
        $form->generalfeedback = [
            'text' => 'The correct answer is "Modular Object Oriented Dynamic Learning Environment".',
            'format' => FORMAT_HTML
        ];

        $form->layouttype = qtype_ordering_question::LAYOUT_HORIZONTAL;
        $form->selecttype = qtype_ordering_question::SELECT_ALL;
        $form->selectcount = 0;
        $form->gradingtype = qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT;
        $form->showgrading = true;
        $form->numberingstyle = qtype_ordering_question::NUMBERING_STYLE_DEFAULT;

        $form->countanswers = 6;
        $form->answer = [
            ['text' => 'Modular', 'format' => FORMAT_HTML],
            ['text' => 'Object', 'format' => FORMAT_HTML],
            ['text' => 'Oriented', 'format' => FORMAT_HTML],
            ['text' => 'Dynamic', 'format' => FORMAT_HTML],
            ['text' => 'Learning', 'format' => FORMAT_HTML],
            ['text' => 'Environment', 'format' => FORMAT_HTML],
        ];

        test_question_maker::set_standard_combined_feedback_form_data($form);

        $form->penalty = '0.3333333';
        $form->numhints = 0;
        $form->hint = [];

        $form->qtype = 'ordering';
        return $form;
    }

    /**
     * Get the raw data that corresponds an ordering question.
     *
     * The question is to sort the words Modular Object Oriented Dynamic Learning Environment.
     *
     * @return stdClass simulated question form data.
     */
    public function get_ordering_question_data_moodle() {
        $questiondata = new stdClass();
        test_question_maker::initialise_question_data($questiondata);
        $questiondata->qtype = 'ordering';
        $questiondata->name = 'Moodle';
        $questiondata->questiontext = 'Put these words in order';
        $questiondata->generalfeedback = 'The correct answer is "Modular Object Oriented Dynamic Learning Environment".';

        $questiondata->options = new stdClass();
        test_question_maker::set_standard_combined_feedback_fields($questiondata->options);
        unset($questiondata->options->shownumcorrect);
        $questiondata->options->layouttype = qtype_ordering_question::LAYOUT_HORIZONTAL;
        $questiondata->options->selecttype = qtype_ordering_question::SELECT_ALL;
        $questiondata->options->selectcount = 0;
        $questiondata->options->gradingtype = qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT;
        $questiondata->options->showgrading = true;
        $questiondata->options->numberingstyle = qtype_ordering_question::NUMBERING_STYLE_DEFAULT;

        $questiondata->options->answers = [
            13 => $this->make_answer(13, 'Modular', FORMAT_HTML, 1),
            14 => $this->make_answer(14, 'Object', FORMAT_HTML, 2),
            15 => $this->make_answer(15, 'Oriented', FORMAT_HTML, 3),
            16 => $this->make_answer(16, 'Dynamic', FORMAT_HTML, 4),
            17 => $this->make_answer(17, 'Learning', FORMAT_HTML, 5),
            18 => $this->make_answer(18, 'Environment', FORMAT_HTML, 6),
        ];
        return $questiondata;
    }
}
