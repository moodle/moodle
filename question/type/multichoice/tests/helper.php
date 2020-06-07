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
 * Test helper code for the multiple choice question type.
 *
 * @package    qtype_multichoice
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the multiple choice question type.
 *
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multichoice_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('two_of_four', 'one_of_four');
    }

    /**
     * Get the question data, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_multichoice_question_data_two_of_four() {
        global $USER;

        $qdata = new stdClass();

        $qdata->createdby = $USER->id;
        $qdata->modifiedby = $USER->id;
        $qdata->qtype = 'multichoice';
        $qdata->name = 'Multiple choice question';
        $qdata->questiontext = 'Which are the odd numbers?';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'The odd numbers are One and Three.';
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->defaultmark = 1;
        $qdata->length = 1;
        $qdata->penalty = 0.3333333;
        $qdata->hidden = 0;

        $qdata->options = new stdClass();
        $qdata->options->shuffleanswers = 1;
        $qdata->options->answernumbering = '123';
        $qdata->options->showstandardinstruction = 0;
        $qdata->options->layout = 0;
        $qdata->options->single = 0;
        $qdata->options->correctfeedback =
                test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK;
        $qdata->options->correctfeedbackformat = FORMAT_HTML;
        $qdata->options->partiallycorrectfeedback =
                test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK;
        $qdata->options->partiallycorrectfeedbackformat = FORMAT_HTML;
        $qdata->options->shownumcorrect = 1;
        $qdata->options->incorrectfeedback =
                test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK;
        $qdata->options->incorrectfeedbackformat = FORMAT_HTML;

        $qdata->options->answers = array(
            13 => (object) array(
                'id' => 13,
                'answer' => 'One',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0.5,
                'feedback' => 'One is odd.',
                'feedbackformat' => FORMAT_HTML,
            ),
            14 => (object) array(
                'id' => 14,
                'answer' => 'Two',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0.0,
                'feedback' => 'Two is even.',
                'feedbackformat' => FORMAT_HTML,
            ),
            15 => (object) array(
                'id' => 15,
                'answer' => 'Three',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0.5,
                'feedback' => 'Three is odd.',
                'feedbackformat' => FORMAT_HTML,
            ),
            16 => (object) array(
                'id' => 16,
                'answer' => 'Four',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0.0,
                'feedback' => 'Four is even.',
                'feedbackformat' => FORMAT_HTML,
            ),
        );

        $qdata->hints = array(
            1 => (object) array(
                'hint' => 'Hint 1.',
                'hintformat' => FORMAT_HTML,
                'shownumcorrect' => 1,
                'clearwrong' => 0,
                'options' => 0,
            ),
            2 => (object) array(
                'hint' => 'Hint 2.',
                'hintformat' => FORMAT_HTML,
                'shownumcorrect' => 1,
                'clearwrong' => 1,
                'options' => 1,
            ),
        );

        return $qdata;
    }
    /**
     * Get the question data, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_multichoice_question_form_data_two_of_four() {
        $qdata = new stdClass();

        $qdata->name = 'multiple choice question';
        $qdata->questiontext = array('text' => 'Which are the odd numbers?', 'format' => FORMAT_HTML);
        $qdata->generalfeedback = array('text' => 'The odd numbers are One and Three.', 'format' => FORMAT_HTML);
        $qdata->defaultmark = 1;
        $qdata->noanswers = 5;
        $qdata->numhints = 2;
        $qdata->penalty = 0.3333333;

        $qdata->shuffleanswers = 1;
        $qdata->answernumbering = '123';
        $qdata->showstandardinstruction = 0;
        $qdata->single = '0';
        $qdata->correctfeedback = array('text' => test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK,
                                                 'format' => FORMAT_HTML);
        $qdata->partiallycorrectfeedback = array('text' => test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK,
                                                          'format' => FORMAT_HTML);
        $qdata->shownumcorrect = 1;
        $qdata->incorrectfeedback = array('text' => test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK,
                                                   'format' => FORMAT_HTML);
        $qdata->fraction = array('0.5', '0.0', '0.5', '0.0', '0.0');
        $qdata->answer = array(
            0 => array(
                'text' => 'One',
                'format' => FORMAT_PLAIN
            ),
            1 => array(
                'text' => 'Two',
                'format' => FORMAT_PLAIN
            ),
            2 => array(
                'text' => 'Three',
                'format' => FORMAT_PLAIN
            ),
            3 => array(
                'text' => 'Four',
                'format' => FORMAT_PLAIN
            ),
            4 => array(
                'text' => '',
                'format' => FORMAT_PLAIN
            )
        );

        $qdata->feedback = array(
            0 => array(
                'text' => 'One is odd.',
                'format' => FORMAT_HTML
            ),
            1 => array(
                'text' => 'Two is even.',
                'format' => FORMAT_HTML
            ),
            2 => array(
                'text' => 'Three is odd.',
                'format' => FORMAT_HTML
            ),
            3 => array(
                'text' => 'Four is even.',
                'format' => FORMAT_HTML
            ),
            4 => array(
                'text' => '',
                'format' => FORMAT_HTML
            )
        );

        $qdata->hint = array(
            0 => array(
                'text' => 'Hint 1.',
                'format' => FORMAT_HTML
            ),
            1 => array(
                'text' => 'Hint 2.',
                'format' => FORMAT_HTML
            )
        );
        $qdata->hintclearwrong = array(0, 1);
        $qdata->hintshownumcorrect = array(1, 1);

        return $qdata;
    }

    /**
     * Get the question data, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_multichoice_question_data_one_of_four() {
        global $USER;

        $qdata = new stdClass();

        $qdata->createdby = $USER->id;
        $qdata->modifiedby = $USER->id;
        $qdata->qtype = 'multichoice';
        $qdata->name = 'Multiple choice question';
        $qdata->questiontext = 'Which is the oddest number?';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'The oddest number is One.'; // Arguable possibly but it is a quick way to make a variation on
                                                                //this question with one correct answer.
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->defaultmark = 1;
        $qdata->length = 1;
        $qdata->penalty = 0.3333333;
        $qdata->hidden = 0;

        $qdata->options = new stdClass();
        $qdata->options->shuffleanswers = 1;
        $qdata->options->answernumbering = '123';
        $qdata->options->showstandardinstruction = 0;
        $qdata->options->layout = 0;
        $qdata->options->single = 1;
        $qdata->options->correctfeedback =
            test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK;
        $qdata->options->correctfeedbackformat = FORMAT_HTML;
        $qdata->options->partiallycorrectfeedback =
            test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK;
        $qdata->options->partiallycorrectfeedbackformat = FORMAT_HTML;
        $qdata->options->shownumcorrect = 1;
        $qdata->options->incorrectfeedback =
            test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK;
        $qdata->options->incorrectfeedbackformat = FORMAT_HTML;

        $qdata->options->answers = array(
            13 => (object) array(
                'id' => 13,
                'answer' => 'One',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 1,
                'feedback' => 'One is the oddest.',
                'feedbackformat' => FORMAT_HTML,
            ),
            14 => (object) array(
                'id' => 14,
                'answer' => 'Two',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0.0,
                'feedback' => 'Two is even.',
                'feedbackformat' => FORMAT_HTML,
            ),
            15 => (object) array(
                'id' => 15,
                'answer' => 'Three',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0,
                'feedback' => 'Three is odd.',
                'feedbackformat' => FORMAT_HTML,
            ),
            16 => (object) array(
                'id' => 16,
                'answer' => 'Four',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0.0,
                'feedback' => 'Four is even.',
                'feedbackformat' => FORMAT_HTML,
            ),
        );

        $qdata->hints = array(
            1 => (object) array(
                'hint' => 'Hint 1.',
                'hintformat' => FORMAT_HTML,
                'shownumcorrect' => 1,
                'clearwrong' => 0,
                'options' => 0,
            ),
            2 => (object) array(
                'hint' => 'Hint 2.',
                'hintformat' => FORMAT_HTML,
                'shownumcorrect' => 1,
                'clearwrong' => 1,
                'options' => 1,
            ),
        );

        return $qdata;
    }
    /**
     * Get the question data, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_multichoice_question_form_data_one_of_four() {
        $qdata = new stdClass();

        $qdata->name = 'multiple choice question';
        $qdata->questiontext = array('text' => 'Which is the oddest number?', 'format' => FORMAT_HTML);
        $qdata->generalfeedback = array('text' => 'The oddest number is One.', 'format' => FORMAT_HTML);
        $qdata->defaultmark = 1;
        $qdata->noanswers = 5;
        $qdata->numhints = 2;
        $qdata->penalty = 0.3333333;

        $qdata->shuffleanswers = 1;
        $qdata->answernumbering = '123';
        $qdata->showstandardinstruction = 0;
        $qdata->single = '1';
        $qdata->correctfeedback = array('text' => test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK,
                                        'format' => FORMAT_HTML);
        $qdata->partiallycorrectfeedback = array('text' => test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK,
                                                 'format' => FORMAT_HTML);
        $qdata->shownumcorrect = 1;
        $qdata->incorrectfeedback = array('text' => test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK,
                                          'format' => FORMAT_HTML);
        $qdata->fraction = array('1.0', '0.0', '0.0', '0.0', '0.0');
        $qdata->answer = array(
            0 => array(
                'text' => 'One',
                'format' => FORMAT_PLAIN
            ),
            1 => array(
                'text' => 'Two',
                'format' => FORMAT_PLAIN
            ),
            2 => array(
                'text' => 'Three',
                'format' => FORMAT_PLAIN
            ),
            3 => array(
                'text' => 'Four',
                'format' => FORMAT_PLAIN
            ),
            4 => array(
                'text' => '',
                'format' => FORMAT_PLAIN
            )
        );

        $qdata->feedback = array(
            0 => array(
                'text' => 'One is the oddest.',
                'format' => FORMAT_HTML
            ),
            1 => array(
                'text' => 'Two is even.',
                'format' => FORMAT_HTML
            ),
            2 => array(
                'text' => 'Three is odd.',
                'format' => FORMAT_HTML
            ),
            3 => array(
                'text' => 'Four is even.',
                'format' => FORMAT_HTML
            ),
            4 => array(
                'text' => '',
                'format' => FORMAT_HTML
            )
        );

        $qdata->hint = array(
            0 => array(
                'text' => 'Hint 1.',
                'format' => FORMAT_HTML
            ),
            1 => array(
                'text' => 'Hint 2.',
                'format' => FORMAT_HTML
            )
        );
        $qdata->hintclearwrong = array(0, 1);
        $qdata->hintshownumcorrect = array(1, 1);

        return $qdata;
    }
}
