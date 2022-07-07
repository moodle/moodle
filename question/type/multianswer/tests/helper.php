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
 * @package    qtype_multianswer
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/multianswer/question.php');


/**
 * Test helper class for the multianswer question type.
 *
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('twosubq', 'fourmc', 'numericalzero', 'dollarsigns', 'multiple');
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

        $mc->shuffleanswers = 0;
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
        $mc->options->shuffleanswers = 0;
        $mc->options->correctfeedback = '';
        $mc->options->correctfeedbackformat = 1;
        $mc->options->partiallycorrectfeedback = '';
        $mc->options->partiallycorrectfeedbackformat = 1;
        $mc->options->incorrectfeedback = '';
        $mc->options->incorrectfeedbackformat = 1;
        $mc->options->answernumbering = 0;
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
            new question_hint_with_parts(0, 'Hint 1', FORMAT_HTML, 0, 0),
            new question_hint_with_parts(0, 'Hint 2', FORMAT_HTML, 0, 0),
        );

        return $qdata;
    }

    /**
     * Makes a multianswer question onetaining one blank in some text.
     * This question has no hints.
     *
     * @return object the question definition data, as it might be returned from
     * get_question_options.
     */
    public function get_multianswer_question_data_dollarsigns() {
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->name = 'Multianswer with $s';
        $qdata->questiontext =
                        'Which is the right order? {#1}';
        $qdata->generalfeedback = '';

        $qdata->defaultmark = 1.0;
        $qdata->qtype = 'multianswer';

        $mc = new stdClass();
        test_question_maker::initialise_question_data($mc);

        $mc->name = 'Multianswer with $s';
        $mc->questiontext = '{1:MULTICHOICE:=y,y,$3~$3,y,y}';
        $mc->generalfeedback = '';
        $mc->penalty = 0.0;
        $mc->qtype = 'multichoice';

        $mc->options = new stdClass();
        $mc->options->layout = 0;
        $mc->options->single = 1;
        $mc->options->shuffleanswers = 0;
        $mc->options->correctfeedback = '';
        $mc->options->correctfeedbackformat = 1;
        $mc->options->partiallycorrectfeedback = '';
        $mc->options->partiallycorrectfeedbackformat = 1;
        $mc->options->incorrectfeedback = '';
        $mc->options->incorrectfeedbackformat = 1;
        $mc->options->answernumbering = 0;
        $mc->options->shownumcorrect = 0;

        $mc->options->answers = array(
            23 => new question_answer(23, 'y,y,$3', 0, '', FORMAT_HTML),
            24 => new question_answer(24, '$3,y,y', 0, '', FORMAT_HTML),
        );

        $qdata->options = new stdClass();
        $qdata->options->questions = array(
            1 => $mc,
        );

        $qdata->hints = array(
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
        $formdata->name = 'Simple multianswer';
        $formdata->questiontext = array('text' => 'Complete this opening line of verse: "The ' .
                '{1:SHORTANSWER:Dog#Wrong, silly!~=Owl#Well done!~*#Wrong answer} ' .
                'and the {1:MULTICHOICE:Bow-wow#You seem to have a dog obsessions!' .
                '~Wiggly worm#Now you are just being ridiculous!~=Pussy-cat#Well done!}' .
                ' went to sea".', 'format' => FORMAT_HTML);
        $formdata->generalfeedback = array('text' => 'General feedback: It\'s from "The Owl and the Pussy-cat" ' .
                'by Lear: "The owl and the pussycat went to sea', 'format' => FORMAT_HTML);

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

    /**
     * Makes a multianswer question with multichoice_multiple questions in it.
     * @return qtype_multianswer_question
     */
    public function make_multianswer_question_multiple() {
        question_bank::load_question_definition_classes('multianswer');
        $q = new qtype_multianswer_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Multichoice multiple';
        $q->questiontext = 'Please select the fruits {#1} and vegetables {#2}';
        $q->generalfeedback = 'You should know which foods are fruits or vegetables.';
        $q->qtype = question_bank::get_qtype('multianswer');

        $q->textfragments = array(
            'Please select the fruits ',
            ' and vegetables ',
            ''
        );
        $q->places = array('1' => '1', '2' => '2');

        // Multiple-choice subquestion.
        question_bank::load_question_definition_classes('multichoice');
        $mc = new qtype_multichoice_multi_question();
        test_question_maker::initialise_a_question($mc);
        $mc->name = 'Multianswer 1';
        $mc->questiontext = '{1:MULTIRESPONSE:=Apple#Good~%-50%Burger~%-50%Hot dog#Not a fruit~%-50%Pizza' .
            '~=Orange#Correct~=Banana}';
        $mc->questiontextformat = FORMAT_HTML;
        $mc->generalfeedback = '';
        $mc->generalfeedbackformat = FORMAT_HTML;

        $mc->shuffleanswers = 0;
        $mc->answernumbering = 'none';
        $mc->layout = qtype_multichoice_base::LAYOUT_VERTICAL;
        $mc->single = 0;

        $mc->answers = array(
            16 => new question_answer(16, 'Apple', 0.3333333,
                                      'Good', FORMAT_HTML),
            17 => new question_answer(17, 'Burger', -0.5,
                                      '', FORMAT_HTML),
            18 => new question_answer(18, 'Hot dog', -0.5,
                                      'Not a fruit', FORMAT_HTML),
            19 => new question_answer(19, 'Pizza', -0.5,
                                      '', FORMAT_HTML),
            20 => new question_answer(20, 'Orange', 0.3333333,
                                      'Correct', FORMAT_HTML),
            21 => new question_answer(21, 'Banana', 0.3333333,
                                      '', FORMAT_HTML),
        );
        $mc->qtype = question_bank::get_qtype('multichoice');
        $mc->maxmark = 1;

        // Multiple-choice subquestion.
        question_bank::load_question_definition_classes('multichoice');
        $mc2 = new qtype_multichoice_multi_question();
        test_question_maker::initialise_a_question($mc2);
        $mc2->name = 'Multichoice 2';
        $mc2->questiontext = '{1:MULTIRESPONSE:=Raddish#Good~%-50%Chocolate~%-50%Biscuit#Not a vegetable~%-50%Cheese' .
            '~=Carrot#Correct}';
        $mc2->questiontextformat = FORMAT_HTML;
        $mc2->generalfeedback = '';
        $mc2->generalfeedbackformat = FORMAT_HTML;

        $mc2->shuffleanswers = 0;
        $mc2->answernumbering = 'none';
        $mc2->layout = qtype_multichoice_base::LAYOUT_VERTICAL;
        $mc2->single = 0;

        $mc2->answers = array(
            22 => new question_answer(22, 'Raddish', 0.5,
                                      'Good', FORMAT_HTML),
            23 => new question_answer(23, 'Chocolate', -0.5,
                                      '', FORMAT_HTML),
            24 => new question_answer(24, 'Biscuit', -0.5,
                                      'Not a vegetable', FORMAT_HTML),
            25 => new question_answer(25, 'Cheese', -0.5,
                                      '', FORMAT_HTML),
            26 => new question_answer(26, 'Carrot', 0.5,
                                      'Correct', FORMAT_HTML),
        );
        $mc2->qtype = question_bank::get_qtype('multichoice');
        $mc2->maxmark = 1;

        $q->subquestions = array(
            1 => $mc,
            2 => $mc2,
        );

        return $q;
    }

}
