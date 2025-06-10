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
 * Test helpers for the regexp question type.
 *
 * @package    qtype_regexp
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Test helper class for the regexp question type.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_regexp_test_helper extends question_test_helper {

    /**
     * must be implemented or class made abstract
     * @return array
     */
    public function get_test_questions() {
        return ['frenchflag', 'frenchflagletterhint', 'frenchflagwordhint', 'cat_bat_rat'];
    }

    /**
     * Makes a REGEXP question with (first) correct answer "it's blue, white and red",
     * partially correct answer must match "(it('s| is) |they are )?blue, white, red".
     * This question also has a '.*' match anything answer.
     * @return qtype_regexp_question
     */
    public function make_regexp_question_frenchflag() {
        question_bank::load_question_definition_classes('regexp');
        $rx = new qtype_regexp_question();
        test_question_maker::initialise_a_question($rx);
        $rx->name = 'Regular expression short answer question';
        $rx->questiontext = 'What are the colours of the French flag?';
        $rx->generalfeedback = 'General feedback: OK';
        $rx->usecase = false;
        $rx->answers = [
            13 => new question_answer(13, "it's blue, white and red", 1.0, 'The best answer.', FORMAT_HTML),
            14 => new question_answer(14, "(it('s| is) |they are )?blue, white, red", 0.8, 'An acceptable answer.', FORMAT_HTML),
            15 => new question_answer(15, '--.*(blue|red|white).*', 0.0,
                'You have not even found one of the colors of the French flag!', FORMAT_HTML),
            16 => new question_answer(16, '--.*blue.*', 0.0, 'Missing blue!', FORMAT_HTML),
            17 => new question_answer(17, '--.*(&&blue&&red&&white).*', 0.0,
                'You have not found <em>all</em> the colors of the French flag!', FORMAT_HTML),
            18 => new question_answer(18, '.*', 0.0, 'No, no, no! Try again', FORMAT_HTML),
        ];
        $rx->qtype = question_bank::get_qtype('regexp');

        return $rx;
    }

    /**
     * Gets the question data for a regexp question with correct answer "it's blue, white and red",
     * partially correct answer must match "(it('s| is) |they are )?blue, white, red".
     * This question also has a '.*' match anything answer.
     * @return stdClass
     */
    public function get_regexp_question_data_frenchflag() {
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->qtype = 'regexp';
        $qdata->name = 'Regular expression short answer question';
        $qdata->questiontext = 'What are the colours of the French flag?';
        $qdata->generalfeedback = 'General feedback: OK';
        $qdata->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        $qdata->options = new stdClass();
        $qdata->options->usecase = 0;
        $qdata->options->answers = [
            13 => new question_answer(13, "it's blue, white and red", 1.0, 'The best answer.', FORMAT_HTML),
            14 => new question_answer(14, "(it('s| is) |they are )?blue, white, red", 0.8, 'An acceptable answer.', FORMAT_HTML),
            15 => new question_answer(15, '--.*(blue|red|white).*', 0.0,
                'You have not even found one of the colors of the French flag!', FORMAT_HTML),
            16 => new question_answer(16, '--.*blue.*', 0.0, 'Missing blue!', FORMAT_HTML),
            17 => new question_answer(17, '--.*(&&blue&&red&&white).*', 0.0,
                'You have not found <em>all</em> the colors of the French flag!', FORMAT_HTML),
            18 => new question_answer(18, '.*', 0.0, 'No, no, no! Try again', FORMAT_HTML),
        ];

        return $qdata;
    }

    /**
     * Gets the question form data for a regexp question with correct answer "it's blue, white and red",
     * partially correct answer must match "(it('s| is) |they are )?blue, white, red".
     * This question also has a '.*' match anything answer.
     * @return stdClass
     */
    public function get_regexp_question_form_data_frenchflag() {
        $form = new stdClass();

        $form->name = 'Regular expression short answer question';
        $form->questiontext = ['text' => 'What are the colours of the French flag?', 'format' => FORMAT_HTML];
        $form->defaultmark = 1.0;
        $form->generalfeedback = ['text' => 'General feedback: OK.', 'format' => FORMAT_HTML];
        $form->usecase = false;
        $form->answer = ["it's blue, white and red", "(it('s| is) |they are )?blue, white, red",
            '--.*(blue|red|white).*', '--.*blue.*', '--.*(&&blue&&red&&white).*', '.*'];
        $form->fraction = ['1.0', '0.8', '0.0', '0.0', '0.0', '0.0'];
        $form->feedback = [
            ['text' => 'The best answer.', 'format' => FORMAT_HTML],
            ['text' => 'An acceptable answer.', 'format' => FORMAT_HTML],
            ['text' => 'You have not even found one of the colors of the French flag!', 'format' => FORMAT_HTML],
            ['text' => 'Missing blue!', 'format' => FORMAT_HTML],
            ['text' => 'You have not found <em>all</em> the colors of the French flag!', 'format' => FORMAT_HTML],
            ['text' => 'No, no, no! Try again.', 'format' => FORMAT_HTML],
        ];
        $form->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        return $form;
    }

    /**
     * Makes a REGEXP question with (first) correct answer "it's blue, white and red",
     * partially correct answer must match "(it('s| is) |they are )?blue, white, red".
     * This question also has a '.*' match anything answer.
     * @return qtype_regexp_question
     */
    public function make_regexp_question_frenchflagletterhint() {
        question_bank::load_question_definition_classes('regexp');
        $rx = new qtype_regexp_question();
        test_question_maker::initialise_a_question($rx);
        $rx->name = 'Regular expression short answer question';
        $rx->questiontext = 'What are the colours of the French flag?';
        $rx->generalfeedback = 'General feedback: OK';
        $rx->usehint = 1; // Help button mode = Letter.
        $rx->usecase = false;
        $rx->penalty = 0.1;
        $rx->answers = [
            13 => new question_answer(13, "it's blue, white and red", 1.0, 'The best answer.', FORMAT_HTML),
            14 => new question_answer(14, "(it('s| is) |they are )?blue, white, red", 0.8,
                'An acceptable answer.', FORMAT_HTML),
            15 => new question_answer(15, '--.*blue.*', 0.0, 'Missing blue!', FORMAT_HTML),
            16 => new question_answer(16, '.*', 0.0, 'No, no, no! Try again', FORMAT_HTML),
        ];
        $rx->qtype = question_bank::get_qtype('regexp');

        return $rx;
    }

    /**
     * Gets the question data for a regexp question with correct answer "it's blue, white and red",
     * partially correct answer must match "(it('s| is) |they are )?blue, white, red".
     * This question also has a '.*' match anything answer.
     * @return stdClass
     */
    public function get_regexp_question_data_frenchflagletterhint() {
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->qtype = 'regexp';
        $qdata->name = 'Regular expression short answer question';
        $qdata->questiontext = 'What are the colours of the French flag?';
        $qdata->generalfeedback = 'General feedback: OK';
        $qdata->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        $qdata->options = new stdClass();
        $qdata->options->usehint = 1; // Help button mode = Letter.
        $qdata->options->usecase = 0;
        $qdata->options->penalty = 0.1;
        $qdata->options->answers = [
            13 => new question_answer(13, "it's blue, white and red", 1.0, 'The best answer.', FORMAT_HTML),
            14 => new question_answer(14, "(it('s| is) |they are )?blue, white, red", 0.8, 'An acceptable answer.', FORMAT_HTML),
            15 => new question_answer(15, '--.*blue.*', 0.0, 'Missing blue!', FORMAT_HTML),
            16 => new question_answer(16, '.*', 0.0, 'No, no, no! Try again.', FORMAT_HTML),
        ];

        return $qdata;
    }

    /**
     * Gets the question form data for a regexp question with correct answer "it's blue, white and red",
     * partially correct answer must match "(it('s| is) |they are )?blue, white, red".
     * This question also has a '.*' match anything answer.
     * @return stdClass
     */
    public function get_regexp_question_form_data_frenchflagletterhint() {
        $form = new stdClass();

        $form->name = 'Regular expression short answer question';
        $form->questiontext = ['text' => 'What are the colours of the French flag?', 'format' => FORMAT_HTML];
        $form->defaultmark = 1.0;
        $form->generalfeedback = ['text' => 'General feedback: OK.', 'format' => FORMAT_HTML];
        $form->usehint = 1; // Help button mode = Letter.
        $form->usecase = false;
        $form->penalty = 0.1;
        $form->answer = ["it's blue, white and red", "(it('s| is) |they are )?blue, white, red", '--.*blue.*', '.*'];
        $form->fraction = ['1.0', '0.8', '0.0', '0.0'];
        $form->feedback = [
            ['text' => 'The best answer.', 'format' => FORMAT_HTML],
            ['text' => 'An acceptable answer.', 'format' => FORMAT_HTML],
            ['text' => 'Missing blue!', 'format' => FORMAT_HTML],
            ['text' => 'No, no, no! Try again.', 'format' => FORMAT_HTML],
        ];

        $form->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        return $form;
    }


    /**
     * Makes a REGEXP question with (first) correct answer "it's blue, white and red",
     * partially correct answer must match "(it('s| is) |they are )?blue, white, red".
     * This question also has a '.*' match anything answer.
     * @return qtype_regexp_question
     */
    public function make_regexp_question_frenchflagwordhint() {
        question_bank::load_question_definition_classes('regexp');
        $rx = new qtype_regexp_question();
        test_question_maker::initialise_a_question($rx);
        $rx->name = 'Regular expression short answer question';
        $rx->questiontext = 'What are the colours of the French flag?';
        $rx->generalfeedback = 'General feedback: OK';
        $rx->usehint = 2; // Help button mode = word.
        $rx->usecase = false;
        $rx->penalty = 0.2;
        $rx->answers = [
            13 => new question_answer(13, "it's blue, white and red", 1.0, 'The best answer.', FORMAT_HTML),
            14 => new question_answer(14, "(it('s| is) |they are )?blue, white, red", 0.8,
                'An acceptable answer.', FORMAT_HTML),
            15 => new question_answer(15, '--.*blue.*', 0.0, 'Missing blue!', FORMAT_HTML),
            16 => new question_answer(16, '.*', 0.0, 'No, no, no! Try again', FORMAT_HTML),
        ];
        $rx->qtype = question_bank::get_qtype('regexp');

        return $rx;
    }

    /**
     * Gets the question data for a regexp question with correct answer "it's blue, white and red",
     * partially correct answer must match "(it('s| is) |they are )?blue, white, red".
     * This question also has a '.*' match anything answer.
     * @return stdClass
     */
    public function get_regexp_question_data_frenchflagwordhint() {
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->qtype = 'regexp';
        $qdata->name = 'Regular expression short answer question';
        $qdata->questiontext = 'What are the colours of the French flag?';
        $qdata->generalfeedback = 'General feedback: OK';
        $qdata->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        $qdata->options = new stdClass();
        $qdata->options->usehint = 2; // Help button mode = word.
        $qdata->options->usecase = 0;
        $qdata->options->penalty = 0.2;
        $qdata->options->answers = [
            13 => new question_answer(13, "it's blue, white and red", 1.0, 'The best answer.', FORMAT_HTML),
            14 => new question_answer(14, "(it('s| is) |they are )?blue, white, red", 0.8,
                'An acceptable answer.', FORMAT_HTML),
            15 => new question_answer(15, '--.*blue.*', 0.0, 'Missing blue!', FORMAT_HTML),
            16 => new question_answer(16, '.*', 0.0, 'No, no, no! Try again.', FORMAT_HTML),
        ];

        return $qdata;
    }

    /**
     * Gets the question form data for a regexp question with correct answer "it's blue, white and red",
     * partially correct answer must match "(it('s| is) |they are )?blue, white, red".
     * This question also has a '.*' match anything answer.
     * @return stdClass
     */
    public function get_regexp_question_form_data_frenchflagwordhint() {
        $form = new stdClass();

        $form->name = 'Regular expression short answer question';
        $form->questiontext = ['text' => 'What are the colours of the French flag?', 'format' => FORMAT_HTML];
        $form->defaultmark = 1.0;
        $form->generalfeedback = ['text' => 'General feedback: OK.', 'format' => FORMAT_HTML];
        $form->usehint = 2; // Help button mode = word.
        $form->usecase = false;
        $form->penalty = 0.2;
        $form->answer = ["it's blue, white and red", "(it('s| is) |they are )?blue, white, red", '--.*blue.*', '.*'];
        $form->fraction = ['1.0', '0.8', '0.0', '0.0'];
        $form->feedback = [
            ['text' => 'The best answer.', 'format' => FORMAT_HTML],
            ['text' => 'An acceptable answer.', 'format' => FORMAT_HTML],
            ['text' => 'Missing blue!', 'format' => FORMAT_HTML],
            ['text' => 'No, no, no! Try again.', 'format' => FORMAT_HTML],
        ];

        $form->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        return $form;
    }


    /**
     * Makes a REGEXP question with (first) correct answer "cat" and an input 'fill in blank',
     * Other correct answer must match "bat|rat".
     * @return qtype_regexp_question
     */
    public function make_regexp_question_cat_bat_rat() {
        question_bank::load_question_definition_classes('regexp');
        $rx = new qtype_regexp_question();
        test_question_maker::initialise_a_question($rx);
        $rx->name = 'Regular expression short answer question';
        $rx->questiontext = 'Name an animal whose name consists of 3 letters and the middle letter is the vowel "a": _____';
        $rx->generalfeedback = 'General feedback: OK';
        $rx->usecase = false;
        $rx->answers = [
            13 => new question_answer(13, "cat", 1.0, 'The best answer.', FORMAT_HTML),
            14 => new question_answer(14, "[br]at", 1.0, 'An acceptable answer.', FORMAT_HTML),
        ];
        $rx->qtype = question_bank::get_qtype('regexp');

        return $rx;
    }

    /**
     * Gets the question data for a  a REGEXP question with (first) correct answer "cat" and an input 'fill in blank',
     * Other correct answer must match "bat|rat".
     * @return stdClass
     */
    public function get_regexp_question_data_cat_bat_rat() {
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->qtype = 'regexp';
        $qdata->name = 'Regular expression short answer question';
        $qdata->questiontext = 'Name an animal whose name consists of 3 letters and the middle letter is the vowel "a": _____';
        $qdata->generalfeedback = 'General feedback: OK';
        $qdata->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $qdata->options = new stdClass();
        $qdata->options->usecase = 0;
        $qdata->options->answers = [
            13 => new question_answer(13, "cat", 1.0, 'The best answer.', FORMAT_HTML),
            14 => new question_answer(14, "[br]at", 1.0, 'An acceptable answer.', FORMAT_HTML),
        ];

        return $qdata;
    }

    /**
     * Gets the question form data for a regexp question with correct answer "it's blue, white and red",
     * partially correct answer must match "(it('s| is) |they are )?blue, white, red".
     * This question also has a '.*' match anything answer.
     * @return stdClass
     */
    public function get_regexp_question_form_data_cat_bat_rat() {
        $form = new stdClass();

        $form->name = 'Regular expression short answer question';
        $form->questiontext = ['text' => 'Name an animal whose name consists of 3 letters'.
            ' and the middle letter is the vowel "a": _____', 'format' => FORMAT_HTML];
        $form->defaultmark = 1.0;
        $form->generalfeedback = ['text' => 'General feedback: OK.', 'format' => FORMAT_HTML];
        $form->usecase = false;
        $form->answer = ["cat", "[br]at"];
        $form->fraction = ['1.0', '1.0'];
        $form->feedback = [
            ['text' => 'The best answer.', 'format' => FORMAT_HTML],
            ['text' => 'An acceptable answer.', 'format' => FORMAT_HTML],
        ];

        $form->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;

        return $form;
    }

}
