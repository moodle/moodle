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
 * Test helpers for the multianswerwiris question type.
 *
 * @package    qtype_multianswerwiris
 * @copyright  WIRIS Europe (Maths For More S.L.)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/multianswerwiris/question.php');


/**
 * Test helper class for the multianswerwiris question type.
 *
 * @copyright  WIRIS Europe (Maths For More S.L.)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswerwiris_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('threesubq', 'fourmc', 'numericalzero', 'dollarsigns', 'multiple');
    }

    /**
     * Makes a multianswerwiris question about completing two blanks in some text.
     * @return object the question definition data, as it might be returned from
     * get_question_options.
     */
    public function get_multianswerwiris_question_data_threesubq() {
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->name = 'Multi answer wiris';
        $qdata->questiontext = '<p>Type -10: {:SA:=\#a}</p><p>Type 5: {:SA:=5}</p>
                                <p>Choose 5/57: {:MC:=\#b~1~2}</p><p>Formula #b</p>';
        $qdata->generalfeedback = 'Just do it.';

        $qdata->defaultmark = 3.0;
        $qdata->qtype = 'multianswerwiris';

        $sa = new stdClass();
        test_question_maker::initialise_question_data($sa);

        $sa->name = 'Simple shortanswer';
        $sa->questiontext = '{:SA:=5}';
        $sa->generalfeedback = '';
        $sa->penalty = 0.0;
        $sa->qtype = 'shortanswerwiris';

        $sa->options = new stdClass();
        $sa->options->usecase = 0;

        $sa->options->answers = array(
            15 => new question_answer(15, '#a', 1, 'Well done!',    FORMAT_HTML)
        );

        $sa2 = new stdClass();
        test_question_maker::initialise_question_data($sa2);

        $sa2->name = 'Simple shortanswer 2';
        $sa2->questiontext = '{:SA:=5}';
        $sa2->generalfeedback = '';
        $sa2->penalty = 0.0;
        $sa2->qtype = 'shortanswerwiris';

        $sa2->options = new stdClass();
        $sa2->options->usecase = 0;

        $sa2->options->answers = array(
            14 => new question_answer(14, '5', 1, 'Well done!',    FORMAT_HTML)
        );

        $mc = new stdClass();
        test_question_maker::initialise_question_data($mc);

        $mc->name = 'Simple choice';
        $mc->questiontext = '{:MC:=\#b~1~2}';
        $mc->generalfeedback = '';
        $mc->penalty = 0.0;
        $mc->qtype = 'multichoicewiris';

        $answers = array(
            23 => new question_answer(23, '#b', 1, 'Well done!', FORMAT_HTML),
            24 => new question_answer(24,  '1', 0, '...',        FORMAT_HTML),
            25 => new question_answer(25,  '2', 0, '...',        FORMAT_HTML),
        );
        $this->set_mc_options($mc, $answers);

        $qdata->options = new stdClass();
        $qdata->options->questions = array(
            1 => $sa,
            2 => $sa2,
            3 => $mc,
        );

        $qdata->hints = array(
            new question_hint_with_parts(0, 'Hint 1', FORMAT_HTML, 0, 0),
            new question_hint_with_parts(0, 'Hint 2', FORMAT_HTML, 0, 0),
        );

        // Wiris specific information.
        $qdata->wirisquestion = '<question><wirisCasSession><![CDATA[<wiriscalc version="3.2"><title>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Untitled&#xa0;calc</mtext></math></title>
        <properties><property name="decimal_separator">.</property><property name="digit_group_separator"></property>
        <property name="float_format">mg</property><property name="imaginary_unit">i</property>
        <property name="implicit_times_operator">false</property><property name="item_separator">,</property><property name="lang">
        en</property><property name="precision">4</property><property name="quizzes_question_options">true</property>
        <property name="save_settings_in_cookies">false</property><property name="times_operator">·</property>
        <property name="use_degrees">false</property></properties><session version="3.0" lang="en"><task><title>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Sheet 1</mtext></math></title><group><command><input>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">a</mi><mo>=</mo><mo>-</mo><mn>10</mn></math>
        </input><output><math xmlns="http://www.w3.org/1998/Math/MathML"><mo>-</mo><mn>10</mn></math></output></command><command>
        <input><math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">b</mi><mo>=</mo><mfrac><mn>17</mn><mn>171
        </mn></mfrac></math></input><output><math xmlns="http://www.w3.org/1998/Math/MathML"><mfrac><mn>17</mn><mn>171</mn></mfrac>
        </math></output></command><command><input><math xmlns="http://www.w3.org/1998/Math/MathML"/></input></command></group>
        </task></session><constructions><construction group="1">
        {&quot;elements&quot;:[],&quot;constraints&quot;:[],&quot;displays&quot;:[],&quot;handwriting&quot;:[]}
        </construction></constructions></wiriscalc>]]></wirisCasSession><correctAnswers><correctAnswer></correctAnswer>
        </correctAnswers><assertions><assertion name="syntax_math"/><assertion name="equivalent_symbolic"><param name="tolerance">
        0.001</param><param name="tolerance_digits">false</param><param name="relative_tolerance">true</param></assertion>
        </assertions><slots><slot><initialContent></initialContent></slot></slots><localData><data name="inputField">textField
        </data></localData></question>';
        $qdata->wirislang = 'en';
        $qdata->wirisessay = '';

        return $qdata;
    }

    /**
     * Makes a multianswerwiris question onetaining one blank in some text.
     * This question has no hints.
     *
     * @return object the question definition data, as it might be returned from
     * get_question_options.
     */
    public function get_multianswerwiris_question_data_dollarsigns() {
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

        $answers = array(
            23 => new question_answer(23, 'y,y,$3', 0, '', FORMAT_HTML),
            24 => new question_answer(24, '$3,y,y', 0, '', FORMAT_HTML),
        );
        $this->set_mc_options($mc, $answers);

        $qdata->options = new stdClass();
        $qdata->options->questions = array(
            1 => $mc,
        );

        $qdata->hints = array(
        );

        return $qdata;
    }

    private function set_mc_options($mc, $answers) {
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

        $mc->options->answers = $answers;
    }

    /**
     * Makes a multianswerwiris question about completing two blanks in some text.
     * @return object the question definition data, as it might be returned from
     *      the question editing form.
     */
    public function get_multianswerwiris_question_form_data_threesubq() {
        $formdata = new stdClass();
        $formdata->name = 'Multi answer wiris';
        $formdata->questiontext = array('text' => '<p>Type -10: {:SA:=\#a}</p><p>Type 5: {:SA:=5}</p>
                                                   <p>Choose 5/57: {:MC:=\#b~1~2}</p><p>Formula #b</p>', 'format' => FORMAT_HTML);
        $formdata->generalfeedback = array('text' => 'Just do it!', 'format' => FORMAT_HTML);

        $formdata->hint = array(
            0 => array('text' => 'Hint 1', 'format' => FORMAT_HTML, 'itemid' => 0),
            1 => array('text' => 'Hint 2', 'format' => FORMAT_HTML, 'itemid' => 0),
        );

        // Wiris specific information.
        $formdata->wirisquestion = '<question><wirisCasSession><![CDATA[<wiriscalc version="3.2"><title>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Untitled&#xa0;calc</mtext></math></title><properties>
        <property name="decimal_separator">.</property><property name="digit_group_separator"></property>
        <property name="float_format">mg</property><property name="imaginary_unit">i</property>
        <property name="implicit_times_operator">false</property><property name="item_separator">,</property>
        <property name="lang">en</property><property name="precision">4</property><property name="quizzes_question_options">
        true</property><property name="save_settings_in_cookies">false</property><property name="times_operator">·</property>
        <property name="use_degrees">false</property></properties><session version="3.0" lang="en">
        <task><title><math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Sheet 1</mtext></math></title><group><command><input>
        <math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">a</mi><mo>=</mo><mo>-</mo><mn>10</mn></math>
        </input><output><math xmlns="http://www.w3.org/1998/Math/MathML"><mo>-</mo><mn>10</mn></math></output></command><command>
        <input><math xmlns="http://www.w3.org/1998/Math/MathML"><mi mathvariant="normal">b</mi><mo>=</mo><mfrac><mn>17</mn>
        <mn>171</mn></mfrac></math></input><output><math xmlns="http://www.w3.org/1998/Math/MathML"><mfrac><mn>17</mn><mn>171</mn>
        </mfrac></math></output></command><command><input><math xmlns="http://www.w3.org/1998/Math/MathML"/></input></command>
        </group></task></session><constructions><construction group="1">
        {&quot;elements&quot;:[],&quot;constraints&quot;:[],&quot;displays&quot;:[],&quot;handwriting&quot;:[]}
        </construction></constructions></wiriscalc>]]></wirisCasSession><correctAnswers><correctAnswer></correctAnswer>
        </correctAnswers><assertions><assertion name="syntax_math"/><assertion name="equivalent_symbolic">
        <param name="tolerance">0.001</param><param name="tolerance_digits">false</param><param name="relative_tolerance">
        true</param></assertion></assertions><slots><slot><initialContent></initialContent></slot></slots><localData>
        <data name="inputField">textField</data></localData></question>';
        $formdata->wirislang = 'en';

        return $formdata;
    }
}
