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
 * Unit tests for the select missing words question question definition class.
 *
 * @package   qtype_gapselect
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/gapselect/tests/helper.php');


/**
 * Unit tests for the select missing words question definition class.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_gapselect_test extends question_testcase {
    /** @var qtype_gapselect instance of the question type class to test. */
    protected $qtype;

    protected function setUp() {
        $this->qtype = question_bank::get_qtype('gapselect');;
    }

    protected function tearDown() {
        $this->qtype = null;
    }

    /**
     * Asserts that two strings containing XML are the same ignoring the line-endings.
     * @param unknown $expectedxml
     * @param unknown $xml
     */
    public function assert_same_xml($expectedxml, $xml) {
        $this->assertEquals(str_replace("\r\n", "\n", $expectedxml),
                str_replace("\r\n", "\n", $xml));
    }

    /**
     * Get some test question data.
     * @return object the data to construct a question like
     * {@link qtype_gapselect_test_helper::make_a_gapselect_question()}.
     */
    protected function get_test_question_data() {
        global $USER;

        $gapselect = new stdClass();
        $gapselect->id = 0;
        $gapselect->category = 0;
        $gapselect->contextid = 0;
        $gapselect->parent = 0;
        $gapselect->questiontextformat = FORMAT_HTML;
        $gapselect->generalfeedbackformat = FORMAT_HTML;
        $gapselect->defaultmark = 1;
        $gapselect->penalty = 0.3333333;
        $gapselect->length = 1;
        $gapselect->stamp = make_unique_id_code();
        $gapselect->version = make_unique_id_code();
        $gapselect->hidden = 0;
        $gapselect->timecreated = time();
        $gapselect->timemodified = time();
        $gapselect->createdby = $USER->id;
        $gapselect->modifiedby = $USER->id;

        $gapselect->name = 'Selection from drop down list question';
        $gapselect->questiontext = 'The [[1]] brown [[2]] jumped over the [[3]] dog.';
        $gapselect->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $gapselect->qtype = 'gapselect';

        $gapselect->options = new stdClass();
        $gapselect->options->shuffleanswers = true;

        test_question_maker::set_standard_combined_feedback_fields($gapselect->options);

        $gapselect->options->answers = array(
            (object) array('answer' => 'quick', 'feedback' => '1'),
            (object) array('answer' => 'fox', 'feedback' => '2'),
            (object) array('answer' => 'lazy', 'feedback' => '3'),
            (object) array('answer' => 'assiduous', 'feedback' => '3'),
            (object) array('answer' => 'dog', 'feedback' => '2'),
            (object) array('answer' => 'slow', 'feedback' => '1'),
        );

        return $gapselect;
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'gapselect');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_initialise_question_instance() {
        $qdata = $this->get_test_question_data();

        $expected = qtype_gapselect_test_helper::make_a_gapselect_question();
        $expected->stamp = $qdata->stamp;
        $expected->version = $qdata->version;

        $q = $this->qtype->make_question($qdata);

        $this->assertEquals($expected, $q);
    }

    public function test_get_random_guess_score() {
        $q = $this->get_test_question_data();
        $this->assertEquals(0.5, $this->qtype->get_random_guess_score($q), '', 0.0000001);
    }

    public function test_get_possible_responses() {
        $q = $this->get_test_question_data();

        $this->assertEquals(array(
            1 => array(
                1 => new question_possible_response('quick', 1 / 3),
                2 => new question_possible_response('slow', 0),
                null => question_possible_response::no_response()),
            2 => array(
                1 => new question_possible_response('fox', 1 / 3),
                2 => new question_possible_response('dog', 0),
                null => question_possible_response::no_response()),
            3 => array(
                1 => new question_possible_response('lazy', 1 / 3),
                2 => new question_possible_response('assiduous', 0),
                null => question_possible_response::no_response()),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_xml_import() {
        $xml = '  <question type="gapselect">
    <name>
      <text>A select missing words question</text>
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
    <selectoption>
      <text>Alpha</text>
      <group>1</group>
    </selectoption>
    <selectoption>
      <text>Beta</text>
      <group>1</group>
    </selectoption>
    <selectoption>
      <text>Gamma</text>
      <group>1</group>
    </selectoption>
    <hint format="moodle_auto_format">
      <text>Try again.</text>
      <shownumcorrect />
    </hint>
    <hint format="moodle_auto_format">
      <text>These are the first three letters of the Greek alphabet.</text>
      <shownumcorrect />
      <clearwrong />
    </hint>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->try_importing_using_qtypes(
                $xmldata['question'], null, null, 'gapselect');

        $expectedq = new stdClass();
        $expectedq->qtype = 'gapselect';
        $expectedq->name = 'A select missing words question';
        $expectedq->questiontext = 'Put these in order: [[1]], [[2]], [[3]].';
        $expectedq->questiontextformat = FORMAT_MOODLE;
        $expectedq->generalfeedback = 'The answer is Alpha, Beta, Gamma.';
        $expectedq->defaultmark = 3;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;

        $expectedq->shuffleanswers = 1;
        $expectedq->correctfeedback = array('text' => '<p>Your answer is correct.</p>',
                'format' => FORMAT_MOODLE);
        $expectedq->partiallycorrectfeedback = array(
                'text' => '<p>Your answer is partially correct.</p>',
                'format' => FORMAT_MOODLE);
        $expectedq->shownumcorrect = true;
        $expectedq->incorrectfeedback = array('text' => '<p>Your answer is incorrect.</p>',
                'format' => FORMAT_MOODLE);

        $expectedq->choices = array(
            array('answer' => 'Alpha', 'choicegroup' => 1),
            array('answer' => 'Beta', 'choicegroup' => 1),
            array('answer' => 'Gamma', 'choicegroup' => 1),
        );

        $expectedq->hint = array(
                array('text' => 'Try again.', 'format' => FORMAT_MOODLE),
                array('text' => 'These are the first three letters of the Greek alphabet.',
                        'format' => FORMAT_MOODLE));
        $expectedq->hintshownumcorrect = array(true, true);
        $expectedq->hintclearwrong = array(false, true);
        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
        $this->assertEquals($expectedq->hint, $q->hint);
    }

    public function test_xml_export() {
        $qdata = new stdClass();
        $qdata->id = 123;
        $qdata->contextid = \context_system::instance()->id;
        $qdata->qtype = 'gapselect';
        $qdata->name = 'A select missing words question';
        $qdata->questiontext = 'Put these in order: [[1]], [[2]], [[3]].';
        $qdata->questiontextformat = FORMAT_MOODLE;
        $qdata->generalfeedback = 'The answer is Alpha, Beta, Gamma.';
        $qdata->generalfeedbackformat = FORMAT_MOODLE;
        $qdata->defaultmark = 3;
        $qdata->length = 1;
        $qdata->penalty = 0.3333333;
        $qdata->hidden = 0;

        $qdata->options = new stdClass();
        $qdata->options->shuffleanswers = 1;
        $qdata->options->correctfeedback = '<p>Your answer is correct.</p>';
        $qdata->options->correctfeedbackformat = FORMAT_MOODLE;
        $qdata->options->partiallycorrectfeedback = '<p>Your answer is partially correct.</p>';
                $qdata->options->partiallycorrectfeedbackformat = FORMAT_MOODLE;
        $qdata->options->shownumcorrect = true;
        $qdata->options->incorrectfeedback = '<p>Your answer is incorrect.</p>';
        $qdata->options->incorrectfeedbackformat = FORMAT_MOODLE;

        $qdata->options->answers = array(
            13 => new question_answer(13, 'Alpha', 0, '1', FORMAT_MOODLE),
            14 => new question_answer(14, 'Beta', 0, '1', FORMAT_MOODLE),
            15 => new question_answer(15, 'Gamma', 0, '1', FORMAT_MOODLE),
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
  <question type="gapselect">
    <name>
      <text>A select missing words question</text>
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
    <selectoption>
      <text>Alpha</text>
      <group>1</group>
    </selectoption>
    <selectoption>
      <text>Beta</text>
      <group>1</group>
    </selectoption>
    <selectoption>
      <text>Gamma</text>
      <group>1</group>
    </selectoption>
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
