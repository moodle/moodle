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
 * Unit tests for the drag-and-drop words into sentences question definition class.
 *
 * @package    qtype
 * @subpackage ddimagetoimage
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/simpletest/helpers.php');
require_once($CFG->dirroot . '/question/type/ddimagetoimage/simpletest/helper.php');


/**
 * Unit tests for the drag-and-drop words into sentences question definition class.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddimagetoimage_test extends UnitTestCase {
    /** @var qtype_ddimagetoimage instance of the question type class to test. */
    protected $qtype;

    public function setUp() {
        $this->qtype = question_bank::get_qtype('ddimagetoimage');;
    }

    public function tearDown() {
        $this->qtype = null;
    }

    public function assert_same_xml($expectedxml, $xml) {
        $this->assertEqual(str_replace("\r\n", "\n", $expectedxml),
                str_replace("\r\n", "\n", $xml));
    }

    /**
     * @return object the data to construct a question like
     * {@link qtype_ddimagetoimage_test_helper::make_ddimagetoimage_question_fox()}.
     */
    protected function get_test_question_data() {
        global $USER;

        $dd = new stdClass();
        $dd->id = 0;
        $dd->category = 0;
        $dd->contextid = 0;
        $dd->parent = 0;
        $dd->questiontextformat = FORMAT_HTML;
        $dd->generalfeedbackformat = FORMAT_HTML;
        $dd->defaultmark = 1;
        $dd->penalty = 0.3333333;
        $dd->length = 1;
        $dd->stamp = make_unique_id_code();
        $dd->version = make_unique_id_code();
        $dd->hidden = 0;
        $dd->timecreated = time();
        $dd->timemodified = time();
        $dd->createdby = $USER->id;
        $dd->modifiedby = $USER->id;

        $dd->name = 'Drag-and-drop words into sentences question';
        $dd->questiontext = 'The [[1]] brown [[2]] jumped over the [[3]] dog.';
        $dd->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $dd->qtype = 'ddimagetoimage';

        $dd->options->shuffleanswers = true;

        test_question_maker::set_standard_combined_feedback_fields($dd->options);

        $dd->options->answers = array(
            (object) array('answer' => 'quick', 'feedback' =>
                    'O:8:"stdClass":2:{s:9:"draggroup";s:1:"1";s:8:"infinite";i:0;}'),
            (object) array('answer' => 'fox', 'feedback' =>
                    'O:8:"stdClass":2:{s:9:"draggroup";s:1:"2";s:8:"infinite";i:0;}'),
            (object) array('answer' => 'lazy', 'feedback' =>
                    'O:8:"stdClass":2:{s:9:"draggroup";s:1:"3";s:8:"infinite";i:0;}'),
            (object) array('answer' => 'assiduous', 'feedback' =>
                    'O:8:"stdClass":2:{s:9:"draggroup";s:1:"3";s:8:"infinite";i:0;}'),
            (object) array('answer' => 'dog', 'feedback' =>
                    'O:8:"stdClass":2:{s:9:"draggroup";s:1:"2";s:8:"infinite";i:0;}'),
            (object) array('answer' => 'slow', 'feedback' =>
                    'O:8:"stdClass":2:{s:9:"draggroup";s:1:"1";s:8:"infinite";i:0;}'),
        );

        return $dd;
    }

    public function test_name() {
        $this->assertEqual($this->qtype->name(), 'ddimagetoimage');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_initialise_question_instance() {
        $qdata = $this->get_test_question_data();

        $expected = test_question_maker::make_question('ddimagetoimage');
        $expected->stamp = $qdata->stamp;
        $expected->version = $qdata->version;

        $q = $this->qtype->make_question($qdata);

        $this->assertEqual($expected, $q);
    }

    public function test_get_random_guess_score() {
        $q = $this->get_test_question_data();
        $this->assertWithinMargin(0.5, $this->qtype->get_random_guess_score($q), 0.0000001);
    }

    public function test_get_possible_responses() {
        $q = $this->get_test_question_data();

        $this->assertEqual(array(
            1 => array(
                1 => new question_possible_response('quick', 1),
                2 => new question_possible_response('slow', 0),
                null => question_possible_response::no_response()),
            2 => array(
                1 => new question_possible_response('fox', 1),
                2 => new question_possible_response('dog', 0),
                null => question_possible_response::no_response()),
            3 => array(
                1 => new question_possible_response('lazy', 1),
                2 => new question_possible_response('assiduous', 0),
                null => question_possible_response::no_response()),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_xml_import() {
        $xml = '  <question type="ddimagetoimage">
    <name>
      <text>A drag-and-drop question</text>
    </name>
    <questiontext format="moodle_auto_format">
      <text>Put these in order: [[1]], [[2]], [[3]].</text>
    </questiontext>
    <generalfeedback>
      <text>The answer is Alpha, Beta, Gamma.</text>
    </generalfeedback>
    <defaultgrade>3</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <shuffleanswers>1</shuffleanswers>
    <correctfeedback>
      <text><![CDATA[<p>Your answer is correct.</p>]]></text>
    </correctfeedback>
    <partiallycorrectfeedback>
      <text><![CDATA[<p>Your answer is partially correct.</p>]]></text>
    </partiallycorrectfeedback>
    <incorrectfeedback>
      <text><![CDATA[<p>Your answer is incorrect.</p>]]></text>
    </incorrectfeedback>
    <shownumcorrect/>
    <dragbox>
      <text>Alpha</text>
      <group>1</group>
    </dragbox>
    <dragbox>
      <text>Beta</text>
      <group>1</group>
    </dragbox>
    <dragbox>
      <text>Gamma</text>
      <group>1</group>
      <infinite/>
    </dragbox>
    <hint>
      <text>Try again.</text>
      <shownumcorrect />
    </hint>
    <hint>
      <text>These are the first three letters of the Greek alphabet.</text>
      <shownumcorrect />
      <clearwrong />
    </hint>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->try_importing_using_qtypes(
                $xmldata['question'], null, null, 'ddimagetoimage');

        $expectedq = new stdClass();
        $expectedq->qtype = 'ddimagetoimage';
        $expectedq->name = 'A drag-and-drop question';
        $expectedq->questiontext = 'Put these in order: [[1]], [[2]], [[3]].';
        $expectedq->questiontextformat = FORMAT_MOODLE;
        $expectedq->generalfeedback = 'The answer is Alpha, Beta, Gamma.';
        $expectedq->defaultmark = 3;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;

        $expectedq->shuffleanswers = 1;
        $expectedq->correctfeedback = array('text' => '<p>Your answer is correct.</p>',
                'format' => FORMAT_MOODLE, 'files' => array());
        $expectedq->partiallycorrectfeedback = array(
                'text' => '<p>Your answer is partially correct.</p>',
                'format' => FORMAT_MOODLE, 'files' => array());
        $expectedq->shownumcorrect = true;
        $expectedq->incorrectfeedback = array('text' => '<p>Your answer is incorrect.</p>',
                'format' => FORMAT_MOODLE, 'files' => array());

        $expectedq->choices = array(
            array('answer' => 'Alpha', 'choicegroup' => 1, 'infinite' => false),
            array('answer' => 'Beta', 'choicegroup' => 1, 'infinite' => false),
            array('answer' => 'Gamma', 'choicegroup' => 1, 'infinite' => true),
        );

        $expectedq->hint = array(
                array('text' => 'Try again.', 'format' => FORMAT_MOODLE, 'files' => array()),
                array('text' => 'These are the first three letters of the Greek alphabet.',
                        'format' => FORMAT_MOODLE, 'files' => array()));
        $expectedq->hintshownumcorrect = array(true, true);
        $expectedq->hintclearwrong = array(false, true);

        $this->assert(new CheckSpecifiedFieldsExpectation($expectedq), $q);
    }

    public function test_xml_import_legacy() {
        $xml = '  <question type="ddimagetoimage">
    <name>
      <text>QDandD1 Base definition</text>
    </name>
    <questiontext format="html">
      <text>&lt;p&gt;Drag and drop the words from the list below to fill the blank spaces ' .
            'and correctly complete the sentence.&lt;/p&gt; &lt;p&gt;At 25°C all aqueous basic ' .
            'solutions have [[1]]&#160;ion concentrations less than [[8]]&lt;br /&gt;mol ' .
            'litre&lt;sup&gt;-1&lt;/sup&gt; and pH values [[9]] than [[6]].&lt;/p&gt; ' .
            '&lt;!--DONOTCLEAN--&gt;</text>
    </questiontext>
    <image></image>
    <generalfeedback>
      <text>&lt;p&gt;At 25 &amp;#xB0;C all aqueous basic solutions have hydrogen ion ' .
            'concentrations less than 10&lt;sup&gt;&amp;#x2212;7&lt;/sup&gt; mol ' .
            'litre&lt;sup&gt;&amp;#x2212;1&lt;/sup&gt; and pH values greater than 7.&lt;/p&gt; ' .
            '&lt;p&gt;See Section 9 of S103 &lt;em class="italic"&gt;Discovering ' .
            'Science&lt;/em&gt; Block 8.&lt;/p&gt;</text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.33</penalty>
    <hidden>0</hidden>
    <shuffleanswers>0</shuffleanswers>
    <shuffleanswers>false</shuffleanswers>
    <answer>
      <correctanswer>1</correctanswer>
      <text>hydrogen</text>
      <feedback>
        <text>O:8:"stdClass":2:{s:9:"draggroup";s:1:"1";s:8:"infinite";i:0;}</text>
      </feedback>
    </answer>
    <answer>
      <correctanswer>0</correctanswer>
      <text>positive</text>
      <feedback>
        <text>O:8:"stdClass":2:{s:9:"draggroup";s:1:"1";s:8:"infinite";i:0;}</text>
      </feedback>
    </answer>
    <answer>
      <correctanswer>0</correctanswer>
      <text>hydroxide</text>
      <feedback>
        <text>O:8:"stdClass":2:{s:9:"draggroup";s:1:"1";s:8:"infinite";i:0;}</text>
      </feedback>
    </answer>
    <answer>
      <correctanswer>0</correctanswer>
      <text>negative</text>
      <feedback>
        <text>O:8:"stdClass":2:{s:9:"draggroup";s:1:"1";s:8:"infinite";i:0;}</text>
      </feedback>
    </answer>
    <answer>
      <correctanswer>0</correctanswer>
      <text>10&lt;sup&gt;7&lt;/sup&gt;</text>
      <feedback>
        <text>O:8:"stdClass":2:{s:9:"draggroup";s:1:"2";s:8:"infinite";i:0;}</text>
      </feedback>
    </answer>
    <answer>
      <correctanswer>1</correctanswer>
      <text>7</text>
      <feedback>
        <text>O:8:"stdClass":2:{s:9:"draggroup";s:1:"2";s:8:"infinite";i:0;}</text>
      </feedback>
    </answer>
    <answer>
      <correctanswer>0</correctanswer>
      <text>1</text>
      <feedback>
        <text>O:8:"stdClass":2:{s:9:"draggroup";s:1:"2";s:8:"infinite";i:0;}</text>
      </feedback>
    </answer>
    <answer>
      <correctanswer>1</correctanswer>
      <text>10&lt;sup&gt;-7&lt;/sup&gt;</text>
      <feedback>
        <text>O:8:"stdClass":2:{s:9:"draggroup";s:1:"2";s:8:"infinite";i:0;}</text>
      </feedback>
    </answer>
    <answer>
      <correctanswer>1</correctanswer>
      <text>greater</text>
      <feedback>
        <text>O:8:"stdClass":2:{s:9:"draggroup";s:1:"3";s:8:"infinite";i:0;}</text>
      </feedback>
    </answer>
    <answer>
      <correctanswer>0</correctanswer>
      <text>less</text>
      <feedback>
        <text>O:8:"stdClass":2:{s:9:"draggroup";s:1:"3";s:8:"infinite";i:0;}</text>
      </feedback>
    </answer>
    <correctfeedback>
      <text>Your answer is correct.</text>
    </correctfeedback>
    <correctresponsesfeedback>1</correctresponsesfeedback>
    <partiallycorrectfeedback>
      <text>Your answer is partially correct.</text>
    </partiallycorrectfeedback>
    <incorrectfeedback>
      <text>Your answer is incorrect.</text>
    </incorrectfeedback>
    <unlimited>0</unlimited>
    <penalty>0.33</penalty>
    <hint>
      <statenumberofcorrectresponses>1</statenumberofcorrectresponses>
      <clearincorrectresponses>0</clearincorrectresponses>
      <hintcontent>
        <text>You may wish to read&#160;Section 9 of&#160;&lt;em ' .
            'class="italic"&gt;Discovering Science&lt;/em&gt; Block 8.</text>
      </hintcontent>
    </hint>
    <hint>
      <statenumberofcorrectresponses>1</statenumberofcorrectresponses>
      <clearincorrectresponses>1</clearincorrectresponses>
      <hintcontent>
        <text>Any incorrect choices&#160;will be removed before your final try.</text>
      </hintcontent>
    </hint>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->try_importing_using_qtypes(
                $xmldata['question'], null, null, 'ddimagetoimage');

        $expectedq = new stdClass();
        $expectedq->qtype = 'ddimagetoimage';
        $expectedq->name = 'QDandD1 Base definition';
        $expectedq->questiontext = '<p>Drag and drop the words from the list below ' .
                'to fill the blank spaces and correctly complete the sentence.</p>' .
                '<p>At 25°C all aqueous basic solutions have [[1]] ion concentrations ' .
                'less than [[8]]<br />mol litre<sup>-1</sup> and pH values [[9]] than [[6]].</p>' .
                '<!--DONOTCLEAN-->';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = '<p>At 25 &#xB0;C all aqueous basic solutions ' .
                'have hydrogen ion concentrations less than 10<sup>&#x2212;7</sup> ' .
                'mol litre<sup>&#x2212;1</sup> and pH values greater than 7.</p><p>See ' .
                'Section 9 of S103 <em class="italic">Discovering Science</em> Block 8.</p>';
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;

        $expectedq->shuffleanswers = 0;
        $expectedq->correctfeedback = array('text' => 'Your answer is correct.',
                'format' => FORMAT_MOODLE, 'files' => array());
        $expectedq->partiallycorrectfeedback = array(
                'text' => 'Your answer is partially correct.',
                'format' => FORMAT_MOODLE, 'files' => array());
        $expectedq->shownumcorrect = true;
        $expectedq->incorrectfeedback = array('text' => 'Your answer is incorrect.',
                'format' => FORMAT_MOODLE, 'files' => array());

        $expectedq->choices = array(
            array('answer' => array('text' => 'hydrogen',        'format' => FORMAT_MOODLE,
                    'files' => array()), 'choicegroup' => 1, 'infinite' => false),
            array('answer' => array('text' => 'positive',        'format' => FORMAT_MOODLE,
                    'files' => array()), 'choicegroup' => 1, 'infinite' => false),
            array('answer' => array('text' => 'hydroxide',       'format' => FORMAT_MOODLE,
                    'files' => array()), 'choicegroup' => 1, 'infinite' => false),
            array('answer' => array('text' => 'negative',        'format' => FORMAT_MOODLE,
                    'files' => array()), 'choicegroup' => 1, 'infinite' => false),
            array('answer' => array('text' => '10<sup>7</sup>',  'format' => FORMAT_MOODLE,
                    'files' => array()), 'choicegroup' => 2, 'infinite' => false),
            array('answer' => array('text' => '7',               'format' => FORMAT_MOODLE,
                    'files' => array()), 'choicegroup' => 2, 'infinite' => false),
            array('answer' => array('text' => '1',               'format' => FORMAT_MOODLE,
                    'files' => array()), 'choicegroup' => 2, 'infinite' => false),
            array('answer' => array('text' => '10<sup>-7</sup>', 'format' => FORMAT_MOODLE,
                    'files' => array()), 'choicegroup' => 2, 'infinite' => false),
            array('answer' => array('text' => 'greater',         'format' => FORMAT_MOODLE,
                    'files' => array()), 'choicegroup' => 3, 'infinite' => false),
            array('answer' => array('text' => 'less',            'format' => FORMAT_MOODLE,
                    'files' => array()), 'choicegroup' => 3, 'infinite' => false),
        );

        $expectedq->hint = array(array('text' => 'You may wish to read Section 9 of ' .
                '<em class="italic">Discovering Science</em> Block 8.',
                    'format' => FORMAT_HTML, 'files' => array()),
                array('text' => 'Any incorrect choices will be removed before your final try.',
                    'format' => FORMAT_HTML, 'files' => array()),
        );
        $expectedq->hintshownumcorrect = array(true, true);
        $expectedq->hintclearwrong = array(false, true);

        $this->assert(new CheckSpecifiedFieldsExpectation($expectedq), $q);
        $this->assertEqual($expectedq->hint, $q->hint);
    }

    public function test_xml_export() {
        $qdata = new stdClass();
        $qdata->id = 123;
        $qdata->contextid = 0;
        $qdata->qtype = 'ddimagetoimage';
        $qdata->name = 'A drag-and-drop question';
        $qdata->questiontext = 'Put these in order: [[1]], [[2]], [[3]].';
        $qdata->questiontextformat = FORMAT_MOODLE;
        $qdata->generalfeedback = 'The answer is Alpha, Beta, Gamma.';
        $qdata->generalfeedbackformat = FORMAT_MOODLE;
        $qdata->defaultmark = 3;
        $qdata->length = 1;
        $qdata->penalty = 0.3333333;
        $qdata->hidden = 0;

        $qdata->options->shuffleanswers = 1;
        $qdata->options->correctfeedback = '<p>Your answer is correct.</p>';
        $qdata->options->correctfeedbackformat = FORMAT_MOODLE;
        $qdata->options->partiallycorrectfeedback = '<p>Your answer is partially correct.</p>';
        $qdata->options->partiallycorrectfeedbackformat = FORMAT_MOODLE;
        $qdata->options->shownumcorrect = 1;
        $qdata->options->incorrectfeedback = '<p>Your answer is incorrect.</p>';
        $qdata->options->incorrectfeedbackformat = FORMAT_MOODLE;

        $qdata->options->answers = array(
            13 => new question_answer(13, 'Alpha', 0,
                    'O:8:"stdClass":2:{s:9:"draggroup";s:1:"1";s:8:"infinite";b:0;}',
                    FORMAT_MOODLE),
            14 => new question_answer(14, 'Beta', 0,
                    'O:8:"stdClass":2:{s:9:"draggroup";s:1:"1";s:8:"infinite";b:0;}',
                    FORMAT_MOODLE),
            15 => new question_answer(15, 'Gamma', 0,
                    'O:8:"stdClass":2:{s:9:"draggroup";s:1:"1";s:8:"infinite";b:1;}',
                    FORMAT_MOODLE),
        );

        $qdata->hints = array(
            1 => new question_hint_with_parts(1, 'Try again.', FORMAT_MOODLE, true, false),
            2 => new question_hint_with_parts(2,
                    'These are the first three letters of the Greek alphabet.',
                    FORMAT_MOODLE, true, true),
        );

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($qdata);

        $expectedxml = '<!-- question: 123  -->
  <question type="ddimagetoimage">
    <name>
      <text>A drag-and-drop question</text>
    </name>
    <questiontext format="moodle_auto_format">
      <text>Put these in order: [[1]], [[2]], [[3]].</text>
    </questiontext>
    <generalfeedback format="moodle_auto_format">
      <text>The answer is Alpha, Beta, Gamma.</text>
    </generalfeedback>
    <defaultgrade>3</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <shuffleanswers>1</shuffleanswers>
    <correctfeedback format="moodle_auto_format">
      <text><![CDATA[<p>Your answer is correct.</p>]]></text>
    </correctfeedback>
    <partiallycorrectfeedback format="moodle_auto_format">
      <text><![CDATA[<p>Your answer is partially correct.</p>]]></text>
    </partiallycorrectfeedback>
    <incorrectfeedback format="moodle_auto_format">
      <text><![CDATA[<p>Your answer is incorrect.</p>]]></text>
    </incorrectfeedback>
    <shownumcorrect/>
    <dragbox>
      <text>Alpha</text>
      <group>1</group>
    </dragbox>
    <dragbox>
      <text>Beta</text>
      <group>1</group>
    </dragbox>
    <dragbox>
      <text>Gamma</text>
      <group>1</group>
      <infinite/>
    </dragbox>
    <hint format="moodle_auto_format">
      <text>Try again.</text>
      <shownumcorrect/>
    </hint>
    <hint format="moodle_auto_format">
      <text>These are the first three letters of the Greek alphabet.</text>
      <shownumcorrect/>
      <clearwrong/>
    </hint>
  </question>
';

        $this->assert_same_xml($expectedxml, $xml);
    }
}
