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
require_once($CFG->dirroot . '/question/type/matchwiris/question.php');


/**
 * Test helper class for the match question type.
 *
 * @copyright  2021 WIRIS Europe (Maths For More S.L.)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_matchwiris_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('foursubq', 'twosubqformula');
    }

    /**
     * Makes a match question about completing four blanks in some text.
     * @return object the question definition data, as it might be returned from
     *      the question editing form.
     */
    public function get_matchwiris_question_form_data_foursubq() {
        $q = new stdClass();
        $q->name = 'Matching wiris question';
        $q->questiontext = array('text' => 'Match the numbers.', 'format' => FORMAT_HTML);
        $q->generalfeedback = array('text' => 'General feedback.', 'format' => FORMAT_HTML);
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;

        $q->shuffleanswers = 0;
        test_question_maker::set_standard_combined_feedback_form_data($q);

        $q->subquestions = array(
            0 => array('text' => 'One', 'format' => FORMAT_HTML),
            1 => array('text' => 'Two', 'format' => FORMAT_HTML),
            2 => array('text' => 'Three', 'format' => FORMAT_HTML),
            3 => array('text' => 'Four', 'format' => FORMAT_HTML));

        $q->subanswers = array(
            0 => '#a',
            1 => '#b',
            2 => '#c',
            3 => '#d'
        );

        $q->noanswers = 4;

        // Wiris specific information.
        $q->wirisquestion = '<question><wirisCasSession><![CDATA[<wiriscalc version="3.2"><title>
                            <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Untitled&#xa0;calc</mtext></math></title>
                            <properties><property name="decimal_separator">.</property><property name="digit_group_separator">
                            </property><property name="float_format">mg</property><property name="imaginary_unit">i</property>
                            <property name="implicit_times_operator">false</property><property name="item_separator">,</property>
                            <property name="lang">en</property><property name="precision">4</property>
                            <property name="quizzes_question_options">true</property><property name="save_settings_in_cookies">
                            false</property><property name="times_operator">·</property><property name="use_degrees">false
                            </property></properties><session version="3.0" lang="en"><task><title>
                            <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Sheet 1</mtext></math></title><group><command>
                            <input><math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">a</mi><mo>=</mo>
                            <mn>1</mn></math></input><output><math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn></math>
                            </output></command><command><input><math xmlns="http://www.w3.org/1998/Math/MathML">
                            <mi mathvariant="normal">b</mi><mo>=</mo><mn>2</mn></math></input><output>
                            <math xmlns="http://www.w3.org/1998/Math/MathML"><mn>2</mn></math></output></command><command><input>
                            <math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">c</mi><mo>=</mo><mn>3</mn>
                            </math></input><output><math xmlns="http://www.w3.org/1998/Math/MathML"><mn>3</mn></math></output>
                            </command><command><input><math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">
                            d</mi><mo>=</mo><mn>4</mn></math></input><output><math xmlns="http://www.w3.org/1998/Math/MathML"><mn>4
                            </mn></math></output></command><command><input><math xmlns="http://www.w3.org/1998/Math/MathML"/>
                            </input></command></group></task></session><constructions><construction group="1">
                            {&quot;elements&quot;:[],&quot;constraints&quot;:[],&quot;displays&quot;:[],&quot;handwriting&quot;:[]}
                            </construction></constructions></wiriscalc>]]></wirisCasSession><correctAnswers><correctAnswer>
                            </correctAnswer></correctAnswers><assertions><assertion name="syntax_math"/>
                            <assertion name="equivalent_symbolic"><param name="tolerance">0.001</param>
                            <param name="tolerance_digits">false</param><param name="relative_tolerance">true</param></assertion>
                            </assertions><slots><slot><initialContent></initialContent></slot></slots></question>';
        $q->wirislang = 'en';

        return $q;
    }

    /**
     * Makes a match question about completing four blanks in some text.
     * @return object the question definition data, as it might be returned from
     *      the question editing form.
     */
    public function get_matchwiris_question_form_data_twosubqformula() {
        $q = new stdClass();
        $q->name = 'Matching wiris question';
        $q->questiontext = array('text' => 'Match the formulas.', 'format' => FORMAT_HTML);
        $q->generalfeedback = array('text' => 'General feedback.', 'format' => FORMAT_HTML);
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;

        $q->shuffleanswers = 0;
        test_question_maker::set_standard_combined_feedback_form_data($q);

        $q->subquestions = array(
            0 => array('text' => 'Formula #a', 'format' => FORMAT_HTML),
            1 => array('text' => 'Formula #b', 'format' => FORMAT_HTML));

        $q->subanswers = array(
            0 => '#a',
            1 => '#b'
        );

        $q->noanswers = 2;

        // Wiris specific information.
        $q->wirisquestion = '<question><wirisCasSession><![CDATA[<wiriscalc version="3.2"><title>
                            <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Untitled&#xa0;calc</mtext></math></title>
                            <properties><property name="decimal_separator">.</property><property name="digit_group_separator">
                            </property><property name="float_format">mg</property><property name="imaginary_unit">i</property>
                            <property name="implicit_times_operator">false</property><property name="item_separator">,</property>
                            <property name="lang">en</property><property name="precision">4</property>
                            <property name="quizzes_question_options">true</property><property name="save_settings_in_cookies">
                            false</property><property name="times_operator">·</property><property name="use_degrees">false
                            </property></properties><session version="3.0" lang="en"><task><title>
                            <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Sheet 1</mtext></math></title><group><command>
                            <input><math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">a</mi><mo>=</mo>
                            <mfrac><msqrt><mn>2</mn></msqrt><mn>45</mn></mfrac></math></input><output>
                            <math xmlns="http://www.w3.org/1998/Math/MathML"><mfrac><msqrt><mn>2</mn></msqrt><mn>45</mn></mfrac>
                            </math></output></command><command><input><math xmlns="http://www.w3.org/1998/Math/MathML">
                            <mi mathvariant="normal">b</mi><mo>=</mo><mn>2</mn><msup><mi>x</mi><mn>2</mn></msup><mo>+</mo><mfrac>
                            <mn>2</mn><mn>3</mn></mfrac></math></input><output><math xmlns="http://www.w3.org/1998/Math/MathML">
                            <mn>2</mn><mo>·</mo><msup><mi>x</mi><mn>2</mn></msup><mo>+</mo><mfrac><mn>2</mn><mn>3</mn></mfrac>
                            </math></output></command><command><input><math xmlns="http://www.w3.org/1998/Math/MathML"/></input>
                            </command></group></task></session><constructions><construction group="1">
                            {&quot;elements&quot;:[],&quot;constraints&quot;:[],&quot;displays&quot;:[],&quot;handwriting&quot;:[]}
                            </construction></constructions></wiriscalc>]]></wirisCasSession><correctAnswers><correctAnswer>
                            </correctAnswer></correctAnswers><assertions><assertion name="syntax_math"/>
                            <assertion name="equivalent_symbolic"><param name="tolerance">0.001</param>
                            <param name="tolerance_digits">false</param><param name="relative_tolerance">true</param></assertion>
                            </assertions><slots><slot><initialContent></initialContent></slot></slots></question>';
        $q->wirislang = 'en';

        return $q;
    }

    /**
     * Makes a matching question to classify 'Dog', 'Frog', 'Toad' and 'Cat' as
     * 'Mammal', 'Amphibian' or 'Insect'.
     * defaultmark 1. Stems are shuffled by default.
     * @return qtype_match_question
     */
    public static function make_matchwiris_question_foursubq() {
        question_bank::load_question_definition_classes('match');
        $match = new qtype_matchwiris_question();
        test_question_maker::initialise_a_question($match);
        $match->name = 'Matching wiris question';
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
    public function get_matchwiris_question_data_trickynums() {
        global $USER;

        $q = new stdClass();
        test_question_maker::initialise_question_data($q);
        $q->name = 'Java matching';
        $q->qtype = 'matchwiris';
        $q->parent = 0;
        $q->questiontext = 'What is the output of each of these lines of code?';
        $q->questiontextformat = FORMAT_HTML;
        $q->generalfeedback = 'Java has some advantages over PHP I guess!';
        $q->generalfeedbackformat = FORMAT_HTML;
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;
        $q->length = 1;
        $q->hidden = 0;
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
    public function get_matchwiris_question_form_data_trickynums() {
        $q = new stdClass();
        $q->name = 'Java matching';
        $q->questiontext = ['text' => 'What is the output of each of these lines of code?', 'format' => FORMAT_HTML];
        $q->generalfeedback = ['text' => 'Java has some advantages over PHP I guess!', 'format' => FORMAT_HTML];
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;

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
     * @return qtype_matchwiris_question
     */
    public static function make_match_question_trickynums() {
        question_bank::load_question_definition_classes('match');
        $match = new qtype_matchwiris_question();
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
