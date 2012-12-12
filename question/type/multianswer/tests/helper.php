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

global $CFG;
require_once($CFG->dirroot . '/question/type/multianswer/question.php');


/**
 * Test helper class for the multianswer question type.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('twosubq', 'fourmc', 'numericalzero');
    }

    /**
     * Makes a multianswer question about completing two blanks in some text.
     * @return qtype_multianswer_question
     */
    public function make_multianswer_question_twosubq() {
        question_bank::load_question_definition_classes('multianswer');
        $q = new qtype_multianswer_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Simple multianswer';
        $q->questiontext =
                'Complete this opening line of verse: "The {#1} and the {#2} went to sea".';
        $q->generalfeedback = 'General feedback: It\'s from "The Owl and the Pussy-cat" by Lear: ' .
                '"The owl and the pussycat went to sea';
        $q->qtype = question_bank::get_qtype('multianswer');

        $q->textfragments = array(
            'Complete this opening line of verse: "The ',
            ' and the ',
            ' went to sea".',
        );
        $q->places = array('1' => '1', '2' => '2');

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

    /**
     * Makes a multianswer question about completing two blanks in some text.
     * @return object the question definition data, as it might be returned from
     * get_question_options.
     */
    public function get_multianswer_question_data_twosubq() {
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->name = 'Simple multianswer';
        $qdata->questiontext =
                        'Complete this opening line of verse: "The {#1} and the {#2} went to sea".';
        $qdata->generalfeedback = 'General feedback: It\'s from "The Owl and the Pussy-cat" by Lear: ' .
                        '"The owl and the pussycat went to sea';

        $qdata->defaultmark = 2.0;
        $qdata->qtype = 'multianswer';

        $sa = new stdClass();
        test_question_maker::initialise_question_data($sa);

        $sa->name = 'Simple multianswer';
        $sa->questiontext = '{1:SHORTANSWER:Dog#Wrong, silly!~=Owl#Well done!~*#Wrong answer}';
        $sa->generalfeedback = '';
        $sa->penalty = 0.0;
        $sa->qtype = 'shortanswer';

        $sa->options = new stdClass();
        $sa->options->usecase = 0;

        $sa->options->answers = array(
            13 => new question_answer(13, 'Dog', 0, 'Wrong, silly!', FORMAT_HTML),
            14 => new question_answer(14, 'Owl', 1, 'Well done!',    FORMAT_HTML),
            15 => new question_answer(15, '*',   0, 'Wrong answer',  FORMAT_HTML),
        );

        $mc = new stdClass();
        test_question_maker::initialise_question_data($mc);

        $mc->name = 'Simple multianswer';
        $mc->questiontext = '{1:MULTICHOICE:Bow-wow#You seem to have a dog obsessions!~' .
                'Wiggly worm#Now you are just being ridiculous!~=Pussy-cat#Well done!}';
        $mc->generalfeedback = '';
        $mc->penalty = 0.0;
        $mc->qtype = 'multichoice';

        $mc->options = new stdClass();
        $mc->options->layout = 0;
        $mc->options->single = 1;
        $mc->options->shuffleanswers = 1;
        $mc->options->correctfeedback = '';
        $mc->options->correctfeedbackformat = 1;
        $mc->options->partiallycorrectfeedback = '';
        $mc->options->partiallycorrectfeedbackformat = 1;
        $mc->options->incorrectfeedback = '';
        $mc->options->incorrectfeedbackformat = 1;
        $mc->options->answernumbering = '';
        $mc->options->shownumcorrect = 0;

        $mc->options->answers = array(
            23 => new question_answer(23, 'Bow-wow',     0,
                    'You seem to have a dog obsessions!', FORMAT_HTML),
            24 => new question_answer(24, 'Wiggly worm', 0,
                    'Now you are just being ridiculous!', FORMAT_HTML),
            25 => new question_answer(25, 'Pussy-cat',   1,
                    'Well done!',                         FORMAT_HTML),
        );

        $qdata->options = new stdClass();
        $qdata->options->questions = array(
            1 => $sa,
            2 => $mc,
        );

        $qdata->hints = array(
            new question_hint(0, 'Hint 1', FORMAT_HTML),
            new question_hint(0, 'Hint 2', FORMAT_HTML),
        );

        return $qdata;
    }

    /**
     * Makes a multianswer question about completing two blanks in some text.
     * @return object the question definition data, as it might be returned from
     *      the question editing form.
     */
    public function get_multianswer_question_form_data_twosubq() {
        $formdata = new stdClass();
        test_question_maker::initialise_question_form_data($formdata);

        $formdata->name = 'Simple multianswer';
        $formdata->questiontext = 'Complete this opening line of verse: "The ' .
                '{1:SHORTANSWER:Dog#Wrong, silly!~=Owl#Well done!~*#Wrong answer} ' .
                'and the {1:MULTICHOICE:Bow-wow#You seem to have a dog obsessions!' .
                '~Wiggly worm#Now you are just being ridiculous!~=Pussy-cat#Well done!}' .
                ' went to sea".';
        $formdata->generalfeedback = 'General feedback: It\'s from "The Owl and the Pussy-cat" ' .
                'by Lear: "The owl and the pussycat went to sea';

        $formdata->hint = array(
            0 => array('text' => 'Hint 1', 'format' => FORMAT_HTML, 'itemid' => 0),
            1 => array('text' => 'Hint 2', 'format' => FORMAT_HTML, 'itemid' => 0),
        );

        return $formdata;
    }

    /**
     * Makes a multianswer question about completing two blanks in some text.
     * @return qtype_multianswer_question
     */
    public function make_multianswer_question_fourmc() {
        question_bank::load_question_definition_classes('multianswer');
        $q = new qtype_multianswer_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Multianswer four multi-choice';
        $q->questiontext = '<p>Match the following cities with the correct state:</p>
                <ul>
                <li>San Francisco: {#1}</li>
                <li>Tucson: {#2}</li>
                <li>Los Angeles: {#3}</li>
                <li>Phoenix: {#4}</li>
                </ul>';
        $q->questiontextformat = FORMAT_HTML;
        $q->generalfeedback = '';
        $q->qtype = question_bank::get_qtype('multianswer');

        $q->textfragments = array('<p>Match the following cities with the correct state:</p>
                <ul>
                <li>San Francisco: ', '</li>
                <li>Tucson: ', '</li>
                <li>Los Angeles: ', '</li>
                <li>Phoenix: ', '</li>
                </ul>');
        $q->places = array('1' => '1', '2' => '2', '3' => '3', '4' => '4');

        $subqdata = array(
            1 => array('qt' => '{1:MULTICHOICE:=California#OK~Arizona#Wrong}', 'California' => 'OK', 'Arizona' => 'Wrong'),
            2 => array('qt' => '{1:MULTICHOICE:%0%California#Wrong~=Arizona#OK}', 'California' => 'Wrong', 'Arizona' => 'OK'),
            3 => array('qt' => '{1:MULTICHOICE:=California#OK~Arizona#Wrong}', 'California' => 'OK', 'Arizona' => 'Wrong'),
            4 => array('qt' => '{1:MULTICHOICE:%0%California#Wrong~=Arizona#OK}', 'California' => 'Wrong', 'Arizona' => 'OK'),
        );

        foreach ($subqdata as $i => $data) {
            // Multiple-choice subquestion.
            question_bank::load_question_definition_classes('multichoice');
            $mc = new qtype_multichoice_single_question();
            test_question_maker::initialise_a_question($mc);
            $mc->name = 'Multianswer four multi-choice';
            $mc->questiontext = $data['qt'];
            $mc->questiontextformat = FORMAT_HTML;
            $mc->generalfeedback = '';
            $mc->generalfeedbackformat = FORMAT_HTML;

            $mc->shuffleanswers = 0; // TODO this is a cheat to make the unit tests easier to write.
            // In reality, multianswer questions always shuffle.
            $mc->answernumbering = 'none';
            $mc->layout = qtype_multichoice_base::LAYOUT_DROPDOWN;

            $mc->answers = array(
                10 * $i     => new question_answer(13, 'California', (float) ($data['California'] == 'OK'),
                        $data['California'], FORMAT_HTML),
                10 * $i + 1 => new question_answer(14, 'Arizona', (float) ($data['Arizona'] == 'OK'),
                        $data['Arizona'], FORMAT_HTML),
            );
            $mc->qtype = question_bank::get_qtype('multichoice');
            $mc->maxmark = 1;

            $q->subquestions[$i] = $mc;
        }

        return $q;
    }

    /**
     * Makes a multianswer question with one numerical subquestion, right answer 0.
     * This is used for testing the MDL-35370 bug.
     * @return qtype_multianswer_question
     */
    public function make_multianswer_question_numericalzero() {
        question_bank::load_question_definition_classes('multianswer');
        $q = new qtype_multianswer_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Numerical zero';
        $q->questiontext =
                'Enter zero: {#1}.';
        $q->generalfeedback = '';
        $q->qtype = question_bank::get_qtype('multianswer');

        $q->textfragments = array(
            'Enter zero: ',
            '.',
        );
        $q->places = array('1' => '1');

        // Numerical subquestion.
        question_bank::load_question_definition_classes('numerical');
        $sub = new qtype_numerical_question();
        test_question_maker::initialise_a_question($sub);
        $sub->name = 'Numerical zero';
        $sub->questiontext = '{1:NUMERICAL:=0:0}';
        $sub->questiontextformat = FORMAT_HTML;
        $sub->generalfeedback = '';
        $sub->generalfeedbackformat = FORMAT_HTML;
        $sub->answers = array(
            13 => new qtype_numerical_answer(13, '0', 1.0, '', FORMAT_HTML, 0),
        );
        $sub->qtype = question_bank::get_qtype('numerical');
        $sub->ap = new qtype_numerical_answer_processor(array());
        $sub->maxmark = 1;

        $q->subquestions = array(
            1 => $sub,
        );

        return $q;
    }
}
