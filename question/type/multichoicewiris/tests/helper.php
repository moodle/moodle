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
 * Test helper code for the multiple choice wiris question type.
 *
 * @package    qtype_multichoicewiris
 * @copyright  2021 WIRIS Europe (Maths For More S.L.)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class qtype_multichoicewiris_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('four_of_five_science');
    }

    /**
     * Get the question data, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_multichoicewiris_question_data_four_of_five_science() {
        global $USER;

        $qdata = new stdClass();

        $qdata->createdby = $USER->id;
        $qdata->modifiedby = $USER->id;
        $qdata->qtype = 'multichoicewiris';
        $qdata->name = 'Multiple choice wiris question';
        $qdata->questiontext = 'Which are the odd numbers?';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'The odd numbers are #t1, #t2 and #t4.';
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
                'answer' => '#t1',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0.25,
                'feedback' => '#t1 is odd.',
                'feedbackformat' => FORMAT_HTML,
            ),
            14 => (object) array(
                'id' => 14,
                'answer' => '#t2',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0.25,
                'feedback' => '#t2 is odd.',
                'feedbackformat' => FORMAT_HTML,
            ),
            15 => (object) array(
                'id' => 15,
                'answer' => '#t3',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0.25,
                'feedback' => '#t3 is odd.',
                'feedbackformat' => FORMAT_HTML,
            ),
            16 => (object) array(
                'id' => 16,
                'answer' => '#t4',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0.25,
                'feedback' => '#t4 is odd.',
                'feedbackformat' => FORMAT_HTML,
            ),
            17 => (object) array(
                'id' => 17,
                'answer' => '#t5',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0.0,
                'feedback' => '#t5 is even.',
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

        // Wiris specific information.
        $qdata->wirisquestion = '<question><wirisCasSession><![CDATA[<wiriscalc version="3.2"><title>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Untitled calc</mtext></math></title><properties>
        <property name="decimal_separator">.</property><property name="digit_group_separator"></property>
        <property name="float_format">mg</property><property name="imaginary_unit">i</property>
        <property name="implicit_times_operator">false</property><property name="item_separator">,</property>
        <property name="lang">en</property><property name="precision">4</property><property name="quizzes_question_options">
        true</property><property name="save_settings_in_cookies">false</property><property name="times_operator">·</property>
        <property name="use_degrees">false</property></properties><session version="3.0" lang="en"><task><title>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Sheet 1</mtext></math></title><group><command><input>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">t1</mi><mo>=</mo><msqrt><mn>3</mn></msqrt></math>
        </input><output><math xmlns="http://www.w3.org/1998/Math/MathML"><msqrt><mn>3</mn></msqrt></math></output></command>
        <command><input><math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">t2</mi><mo>=</mo><mn>15</mn>
        </math></input><output><math xmlns="http://www.w3.org/1998/Math/MathML"><mn>15</mn></math></output></command><command>
        <input><math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">t3</mi><mo>=</mo><mn>55</mn></math>
        </input><output><math xmlns="http://www.w3.org/1998/Math/MathML"><mn>55</mn></math></output></command><command><input>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">t4</mi><mo>=</mo><mn>25</mn></math></input>
        <output><math xmlns="http://www.w3.org/1998/Math/MathML"><mn>25</mn></math></output></command><command><input>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">t5</mi><mo>=</mo><mn>30</mn></math></input>
        <output><math xmlns="http://www.w3.org/1998/Math/MathML"><mn>30</mn></math></output></command><command><input>
        <math xmlns="http://www.w3.org/1998/Math/MathML"/></input></command></group></task></session><constructions>
        <construction group="1">
        {&quot;elements&quot;:[],&quot;constraints&quot;:[],&quot;displays&quot;:[],&quot;handwriting&quot;:[]}
        </construction></constructions></wiriscalc>]]></wirisCasSession><correctAnswers><correctAnswer></correctAnswer>
        </correctAnswers><assertions><assertion name="syntax_math"/><assertion name="equivalent_symbolic">
        <param name="tolerance">0.001</param><param name="tolerance_digits">false</param><param name="relative_tolerance">true
        </param></assertion></assertions><slots><slot><localData><data name="cas">false</data><data name="auxiliaryTextInput">
        false</data></localData><initialContent></initialContent></slot></slots></question>';
        $qdata->wirislang = 'en';
        $qdata->wirisessay = '';

        return $qdata;
    }
    /**
     * Get the question data, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_multichoicewiris_question_form_data_four_of_five_science() {
        $qdata = new stdClass();

        $qdata->name = 'multiple choice question';
        $qdata->questiontext = array('text' => 'Which are the odd numbers?', 'format' => FORMAT_HTML);
        $qdata->generalfeedback = array('text' => 'The odd numbers are #t1, #t2 and #t4.', 'format' => FORMAT_HTML);
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
        $qdata->fraction = array('0.25', '0.25', '0.25', '0.25', '0.0');
        $qdata->answer = array(
            0 => array(
                'text' => '#t1',
                'format' => FORMAT_PLAIN
            ),
            1 => array(
                'text' => '#t2',
                'format' => FORMAT_PLAIN
            ),
            2 => array(
                'text' => '#t3',
                'format' => FORMAT_PLAIN
            ),
            3 => array(
                'text' => '#t4',
                'format' => FORMAT_PLAIN
            ),
            4 => array(
                'text' => '#t5',
                'format' => FORMAT_PLAIN
            )
        );

        $qdata->feedback = array(
            0 => array(
                'text' => '#t1 is odd.',
                'format' => FORMAT_HTML
            ),
            1 => array(
                'text' => '#t2 is odd.',
                'format' => FORMAT_HTML
            ),
            2 => array(
                'text' => '#t3 is odd.',
                'format' => FORMAT_HTML
            ),
            3 => array(
                'text' => '#t4 is odd.',
                'format' => FORMAT_HTML
            ),
            4 => array(
                'text' => 't# is even.',
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

        // Wiris specific information.
        $qdata->wirisquestion = '<question><wirisCasSession><![CDATA[<wiriscalc version="3.2"><title>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Untitled calc</mtext></math></title><properties>
        <property name="decimal_separator">.</property><property name="digit_group_separator"></property>
        <property name="float_format">mg</property><property name="imaginary_unit">i</property>
        <property name="implicit_times_operator">false</property><property name="item_separator">,</property>
        <property name="lang">en</property><property name="precision">4</property><property name="quizzes_question_options">
        true</property><property name="save_settings_in_cookies">false</property><property name="times_operator">·</property>
        <property name="use_degrees">false</property></properties><session version="3.0" lang="en"><task><title>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Sheet 1</mtext></math></title><group><command><input>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">t1</mi><mo>=</mo><msqrt><mn>3</mn></msqrt></math>
        </input><output><math xmlns="http://www.w3.org/1998/Math/MathML"><msqrt><mn>3</mn></msqrt></math></output></command>
        <command><input><math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">t2</mi><mo>=</mo><mn>15</mn>
        </math></input><output><math xmlns="http://www.w3.org/1998/Math/MathML"><mn>15</mn></math></output></command><command>
        <input><math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">t3</mi><mo>=</mo><mn>55</mn></math>
        </input><output><math xmlns="http://www.w3.org/1998/Math/MathML"><mn>55</mn></math></output></command><command><input>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">t4</mi><mo>=</mo><mn>25</mn></math></input>
        <output><math xmlns="http://www.w3.org/1998/Math/MathML"><mn>25</mn></math></output></command><command><input>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">t5</mi><mo>=</mo><mn>30</mn></math></input>
        <output><math xmlns="http://www.w3.org/1998/Math/MathML"><mn>30</mn></math></output></command><command><input>
        <math xmlns="http://www.w3.org/1998/Math/MathML"/></input></command></group></task></session><constructions>
        <construction group="1">
        {&quot;elements&quot;:[],&quot;constraints&quot;:[],&quot;displays&quot;:[],&quot;handwriting&quot;:[]}
        </construction></constructions></wiriscalc>]]></wirisCasSession><correctAnswers><correctAnswer></correctAnswer>
        </correctAnswers><assertions><assertion name="syntax_math"/><assertion name="equivalent_symbolic"><param name="tolerance">
        0.001</param><param name="tolerance_digits">false</param><param name="relative_tolerance">true</param></assertion>
        </assertions><slots><slot><localData><data name="cas">false</data><data name="auxiliaryTextInput">false</data></localData>
        <initialContent></initialContent></slot></slots></question>';
        $qdata->wirislang = 'en';
        $qdata->wirisessay = '';

        return $qdata;
    }
}
