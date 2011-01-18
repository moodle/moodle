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
 * Unit tests for the Moodle XML format.
 *
 * @package qformat_xml
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');


/**
 * Unit tests for the matching question definition class.
 *
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_xml_test extends UnitTestCase {
    public function assert_same_xml($expectedxml, $xml) {
        $this->assertEqual(str_replace("\r\n", "\n", $expectedxml),
                str_replace("\r\n", "\n", $xml));
    }

    public function make_test_question() {
        global $USER;
        $q = new stdClass;
        $q->id = 0;
        $q->contextid = 0;
        $q->category = 0;
        $q->parent = 0;
        $q->questiontextformat = FORMAT_HTML;
        $q->generalfeedbackformat = FORMAT_HTML;
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;
        $q->length = 1;
        $q->stamp = make_unique_id_code();
        $q->version = make_unique_id_code();
        $q->hidden = 0;
        $q->timecreated = time();
        $q->timemodified = time();
        $q->createdby = $USER->id;
        $q->modifiedby = $USER->id;
        return $q;
    }

    public function test_write_hint_basic() {
        $q = $this->make_test_question();
        $q->name = 'Short answer question';
        $q->questiontext = 'Name an amphibian: __________';
        $q->generalfeedback = 'Generalfeedback: frog or toad would have been OK.';
        $q->options->usecase = false;
        $q->options->answers = array(
            13 => new question_answer(13, 'frog', 1.0, 'Frog is a very good answer.', FORMAT_HTML),
            14 => new question_answer(14, 'toad', 0.8, 'Toad is an OK good answer.', FORMAT_HTML),
            15 => new question_answer(15, '*', 0.0, 'That is a bad answer.', FORMAT_HTML),
        );
        $q->qtype = 'shortanswer';
        $q->hints = array(
            new question_hint(0, 'This is the first hint.', FORMAT_MOODLE),
        );

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($q);

        $this->assertPattern('|<hint format=\"moodle_auto_format\">\s*<text>\s*This is the first hint\.\s*</text>\s*</hint>|', $xml);
        $this->assertNoPattern('|<shownumcorrect/>|', $xml);
        $this->assertNoPattern('|<clearwrong/>|', $xml);
        $this->assertNoPattern('|<options>|', $xml);
    }

    public function test_write_hint_with_parts() {
        $q = $this->make_test_question();
        $q->name = 'Matching question';
        $q->questiontext = 'Classify the animals.';
        $q->generalfeedback = 'Frogs and toads are amphibians, the others are mammals.';
        $q->qtype = 'match';

        $q->options->shuffleanswers = 1;
        $q->options->correctfeedback = '';
        $q->options->correctfeedbackformat = FORMAT_HTML;
        $q->options->partiallycorrectfeedback = '';
        $q->options->partiallycorrectfeedbackformat = FORMAT_HTML;
        $q->options->incorrectfeedback = '';
        $q->options->incorrectfeedbackformat = FORMAT_HTML;

        $q->options->subquestions = array();
        $q->hints = array(
            new question_hint_with_parts(0, 'This is the first hint.', FORMAT_HTML, false, true),
            new question_hint_with_parts(0, 'This is the second hint.', FORMAT_HTML, true, false),
        );

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($q);

        $this->assertPattern('|<hint format=\"html\">\s*<text>\s*This is the first hint\.\s*</text>|', $xml);
        $this->assertPattern('|<hint format=\"html\">\s*<text>\s*This is the second hint\.\s*</text>|', $xml);
        list($ignored, $hint1, $hint2) = explode('<hint', $xml);
        $this->assertNoPattern('|<shownumcorrect/>|', $hint1);
        $this->assertPattern('|<clearwrong/>|', $hint1);
        $this->assertPattern('|<shownumcorrect/>|', $hint2);
        $this->assertNoPattern('|<clearwrong/>|', $hint2);
        $this->assertNoPattern('|<options>|', $xml);
    }

    public function test_import_hints_no_parts() {
        $xml = <<<END
<question>
    <hint>
        <text>This is the first hint</text>
        <clearwrong/>
    </hint>
    <hint>
        <text>This is the second hint</text>
        <shownumcorrect/>
    </hint>
</question>
END;

        $questionxml = xmlize($xml);
        $qo = new stdClass;

        $importer = new qformat_xml();
        $importer->import_hints($qo, $questionxml['question']);

        $this->assertEqual(array('This is the first hint', 'This is the second hint'),
                $qo->hint);
        $this->assertFalse(isset($qo->hintclearwrong));
        $this->assertFalse(isset($qo->hintshownumcorrect));
    }

    public function test_import_hints_with_parts() {
        $xml = <<<END
<question>
    <hint>
        <text>This is the first hint</text>
        <clearwrong/>
    </hint>
    <hint>
        <text>This is the second hint</text>
        <shownumcorrect/>
    </hint>
</question>
END;

        $questionxml = xmlize($xml);
        $qo = new stdClass;

        $importer = new qformat_xml();
        $importer->import_hints($qo, $questionxml['question'], true, true);

        $this->assertEqual(array('This is the first hint', 'This is the second hint'),
                $qo->hint);
        $this->assertEqual(array(1, 0), $qo->hintclearwrong);
        $this->assertEqual(array(0, 1), $qo->hintshownumcorrect);
    }

    public function test_import_no_hints_no_error() {
        $xml = <<<END
<question>
</question>
END;

        $questionxml = xmlize($xml);
        $qo = new stdClass;

        $importer = new qformat_xml();
        $importer->import_hints($qo, $questionxml['question']);

        $this->assertFalse(isset($qo->hint));
    }

    public function test_import_description() {
        $xml = '  <question type="description">
    <name>
      <text>A description</text>
    </name>
    <questiontext format="html">
      <text>The question text.</text>
    </questiontext>
    <generalfeedback>
      <text>Here is some general feedback.</text>
    </generalfeedback>
    <defaultgrade>0</defaultgrade>
    <penalty>0</penalty>
    <hidden>0</hidden>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->import_description($xmldata['question']);

        $expectedq = new stdClass;
        $expectedq->qtype = 'description';
        $expectedq->name = 'A description';
        $expectedq->questiontext = 'The question text.';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = 'Here is some general feedback.';
        $expectedq->defaultmark = 0;
        $expectedq->length = 0;
        $expectedq->penalty = 0;

        $this->assert(new CheckSpecifiedFieldsExpectation($expectedq), $q);
    }

    public function test_export_description() {
        $qdata = new stdClass;
        $qdata->id = 123;
        $qdata->contextid = 0;
        $qdata->qtype = 'description';
        $qdata->name = 'A description';
        $qdata->questiontext = 'The question text.';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'Here is some general feedback.';
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->defaultmark = 0;
        $qdata->length = 0;
        $qdata->penalty = 0;
        $qdata->hidden = 0;

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($qdata);

        $expectedxml = '<!-- question: 123  -->
  <question type="description">
    <name>
      <text>A description</text>
    </name>
    <questiontext format="html">
      <text>The question text.</text>
    </questiontext>
    <generalfeedback format="html">
      <text>Here is some general feedback.</text>
    </generalfeedback>
    <defaultgrade>0</defaultgrade>
    <penalty>0</penalty>
    <hidden>0</hidden>
  </question>
';

        $this->assert_same_xml($expectedxml, $xml);
    }

    public function test_import_essay() {
        $xml = '  <question type="essay">
    <name>
      <text>An essay</text>
    </name>
    <questiontext format="moodle_auto_format">
      <text>Write something.</text>
    </questiontext>
    <generalfeedback>
      <text>I hope you wrote something interesting.</text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0</penalty>
    <hidden>0</hidden>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->import_essay($xmldata['question']);

        $expectedq = new stdClass;
        $expectedq->qtype = 'essay';
        $expectedq->name = 'An essay';
        $expectedq->questiontext = 'Write something.';
        $expectedq->questiontextformat = FORMAT_MOODLE;
        $expectedq->generalfeedback = 'I hope you wrote something interesting.';
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0;

        $this->assert(new CheckSpecifiedFieldsExpectation($expectedq), $q);
    }

    public function test_export_essay() {
        $qdata = new stdClass;
        $qdata->id = 123;
        $qdata->contextid = 0;
        $qdata->qtype = 'essay';
        $qdata->name = 'An essay';
        $qdata->questiontext = 'Write something.';
        $qdata->questiontextformat = FORMAT_MOODLE;
        $qdata->generalfeedback = 'I hope you wrote something interesting.';
        $qdata->generalfeedbackformat = FORMAT_MOODLE;
        $qdata->defaultmark = 1;
        $qdata->length = 1;
        $qdata->penalty = 0;
        $qdata->hidden = 0;

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($qdata);

        $expectedxml = '<!-- question: 123  -->
  <question type="essay">
    <name>
      <text>An essay</text>
    </name>
    <questiontext format="moodle_auto_format">
      <text>Write something.</text>
    </questiontext>
    <generalfeedback format="moodle_auto_format">
      <text>I hope you wrote something interesting.</text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0</penalty>
    <hidden>0</hidden>
  </question>
';

        $this->assert_same_xml($expectedxml, $xml);
    }

    public function test_import_match() {
        $xml = '  <question type="matching">
    <name>
      <text>Matching question</text>
    </name>
    <questiontext format="html">
      <text>Match the upper and lower case letters.</text>
    </questiontext>
    <generalfeedback>
      <text>The answer is A -> a, B -> b and C -> c.</text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <shuffleanswers>false</shuffleanswers>
    <correctfeedback>
      <text>Well done.</text>
    </correctfeedback>
    <partiallycorrectfeedback>
      <text>Not entirely.</text>
    </partiallycorrectfeedback>
    <incorrectfeedback>
      <text>Completely wrong!</text>
    </incorrectfeedback>
    <subquestion>
      <text>A</text>
      <answer>
        <text>a</text>
      </answer>
    </subquestion>
    <subquestion>
      <text>B</text>
      <answer>
        <text>b</text>
      </answer>
    </subquestion>
    <subquestion>
      <text>C</text>
      <answer>
        <text>c</text>
      </answer>
    </subquestion>
    <subquestion>
      <text></text>
      <answer>
        <text>d</text>
      </answer>
    </subquestion>
    <hint>
      <text>Hint 1</text>
      <shownumcorrect />
    </hint>
    <hint>
      <text></text>
      <shownumcorrect />
      <clearwrong />
    </hint>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->import_matching($xmldata['question']);

        $expectedq = new stdClass;
        $expectedq->qtype = 'match';
        $expectedq->name = 'Matching question';
        $expectedq->questiontext = 'Match the upper and lower case letters.';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->correctfeedback = 'Well done.';
        $expectedq->partiallycorrectfeedback = 'Not entirely.';
        $expectedq->shownumcorrect = false;
        $expectedq->incorrectfeedback = 'Completely wrong!';
        $expectedq->generalfeedback = 'The answer is A -> a, B -> b and C -> c.';
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;
        $expectedq->shuffleanswers = 0;
        $expectedq->subquestions = array('A', 'B', 'C', '');
        $expectedq->subanswers = array('a', 'b', 'c', 'd');
        $expectedq->hint = array('Hint 1', '');
        $expectedq->hintshownumcorrect = array(true, true);
        $expectedq->hintclearwrong = array(false, true);

        $this->assert(new CheckSpecifiedFieldsExpectation($expectedq), $q);
    }

    public function test_export_match() {
        $qdata = new stdClass;
        $qdata->id = 123;
        $qdata->contextid = 0;
        $qdata->qtype = 'match';
        $qdata->name = 'Matching question';
        $qdata->questiontext = 'Match the upper and lower case letters.';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'The answer is A -> a, B -> b and C -> c.';
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->defaultmark = 1;
        $qdata->length = 1;
        $qdata->penalty = 0.3333333;
        $qdata->hidden = 0;

        $qdata->options = new stdClass;
        $qdata->options->shuffleanswers = 1;
        $qdata->options->correctfeedback = 'Well done.';
        $qdata->options->correctfeedbackformat = FORMAT_HTML;
        $qdata->options->partiallycorrectfeedback = 'Not entirely.';
        $qdata->options->partiallycorrectfeedbackformat = FORMAT_HTML;
        $qdata->options->shownumcorrect = false;
        $qdata->options->incorrectfeedback = 'Completely wrong!';
        $qdata->options->incorrectfeedbackformat = FORMAT_HTML;

        $subq1 = new stdClass();
        $subq1->id = -4;
        $subq1->questiontext = 'A';
        $subq1->questiontextformat = FORMAT_HTML;
        $subq1->answertext = 'a';

        $subq2 = new stdClass();
        $subq2->id = -3;
        $subq2->questiontext = 'B';
        $subq2->questiontextformat = FORMAT_HTML;
        $subq2->answertext = 'b';

        $subq3 = new stdClass();
        $subq3->id = -2;
        $subq3->questiontext = 'C';
        $subq3->questiontextformat = FORMAT_HTML;
        $subq3->answertext = 'c';

        $subq4 = new stdClass();
        $subq4->id = -1;
        $subq4->questiontext = '';
        $subq4->questiontextformat = FORMAT_HTML;
        $subq4->answertext = 'd';

        $qdata->options->subquestions = array(
                $subq1, $subq2, $subq3, $subq4);

        $qdata->hints = array(
            new question_hint_with_parts(0, 'Hint 1', FORMAT_HTML, true, false),
            new question_hint_with_parts(0, '', FORMAT_HTML, true, true),
        );

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($qdata);

        $expectedxml = '<!-- question: 123  -->
  <question type="matching">
    <name>
      <text>Matching question</text>
    </name>
    <questiontext format="html">
      <text>Match the upper and lower case letters.</text>
    </questiontext>
    <generalfeedback format="html">
      <text><![CDATA[The answer is A -> a, B -> b and C -> c.]]></text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <shuffleanswers>true</shuffleanswers>
    <correctfeedback format="html">
      <text>Well done.</text>
    </correctfeedback>
    <partiallycorrectfeedback format="html">
      <text>Not entirely.</text>
    </partiallycorrectfeedback>
    <incorrectfeedback format="html">
      <text>Completely wrong!</text>
    </incorrectfeedback>
    <subquestion format="html">
      <text>A</text>
      <answer>
        <text>a</text>
      </answer>
    </subquestion>
    <subquestion format="html">
      <text>B</text>
      <answer>
        <text>b</text>
      </answer>
    </subquestion>
    <subquestion format="html">
      <text>C</text>
      <answer>
        <text>c</text>
      </answer>
    </subquestion>
    <subquestion format="html">
      <text></text>
      <answer>
        <text>d</text>
      </answer>
    </subquestion>
    <hint format="html">
      <text>Hint 1</text>
      <shownumcorrect/>
    </hint>
    <hint format="html">
      <text></text>
      <shownumcorrect/>
      <clearwrong/>
    </hint>
  </question>
';

        $this->assert_same_xml($expectedxml, $xml);
    }

    public function test_import_multichoice() {
        $xml = '  <question type="multichoice">
    <name>
      <text>Multiple choice question</text>
    </name>
    <questiontext format="html">
      <text>Which are the even numbers?</text>
    </questiontext>
    <generalfeedback>
      <text>The even numbers are 2 and 4.</text>
    </generalfeedback>
    <defaultgrade>2</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <single>false</single>
    <shuffleanswers>false</shuffleanswers>
    <answernumbering>abc</answernumbering>
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
    <answer fraction="0">
      <text>1</text>
      <feedback>
        <text></text>
      </feedback>
    </answer>
    <answer fraction="100">
      <text>2</text>
      <feedback>
        <text></text>
      </feedback>
    </answer>
    <answer fraction="0">
      <text>3</text>
      <feedback>
        <text></text>
      </feedback>
    </answer>
    <answer fraction="100">
      <text>4</text>
      <feedback>
        <text></text>
      </feedback>
    </answer>
    <hint>
      <text>Hint 1.</text>
    </hint>
    <hint>
      <text>Hint 2.</text>
    </hint>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->import_multichoice($xmldata['question']);

        $expectedq = new stdClass;
        $expectedq->qtype = 'multichoice';
        $expectedq->name = 'Multiple choice question';
        $expectedq->questiontext = 'Which are the even numbers?';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->correctfeedback = '<p>Your answer is correct.</p>';
        $expectedq->partiallycorrectfeedback = '<p>Your answer is partially correct.</p>';
        $expectedq->shownumcorrect = true;
        $expectedq->incorrectfeedback = '<p>Your answer is incorrect.</p>';
        $expectedq->generalfeedback = 'The even numbers are 2 and 4.';
        $expectedq->defaultmark = 2;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;
        $expectedq->shuffleanswers = 0;
        $expectedq->single = false;

        $expectedq->answer = array('1', '2', '3', '4');
        $expectedq->fraction = array(0, 1, 0, 1);
        $expectedq->feedback = array('', '', '', '');

        $expectedq->hint = array('Hint 1.', 'Hint 2.');
        $expectedq->hintshownumcorrect = array(false, false);
        $expectedq->hintclearwrong = array(false, false);

        $this->assert(new CheckSpecifiedFieldsExpectation($expectedq), $q);
    }

    public function test_export_multichoice() {
        $qdata = new stdClass;
        $qdata->id = 123;
        $qdata->contextid = 0;
        $qdata->qtype = 'multichoice';
        $qdata->name = 'Multiple choice question';
        $qdata->questiontext = 'Which are the even numbers?';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'The even numbers are 2 and 4.';
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->defaultmark = 2;
        $qdata->length = 1;
        $qdata->penalty = 0.3333333;
        $qdata->hidden = 0;

        $qdata->options = new stdClass;
        $qdata->options->single = 0;
        $qdata->options->shuffleanswers = 0;
        $qdata->options->answernumbering = 'abc';
        $qdata->options->correctfeedback = '<p>Your answer is correct.</p>';
        $qdata->options->correctfeedbackformat = FORMAT_HTML;
        $qdata->options->partiallycorrectfeedback = '<p>Your answer is partially correct.</p>';
        $qdata->options->partiallycorrectfeedbackformat = FORMAT_HTML;
        $qdata->options->shownumcorrect = 1;
        $qdata->options->incorrectfeedback = '<p>Your answer is incorrect.</p>';
        $qdata->options->incorrectfeedbackformat = FORMAT_HTML;

        $qdata->options->answers = array(
            13 => new question_answer(13, '1', 0, '', FORMAT_HTML),
            14 => new question_answer(14, '2', 1, '', FORMAT_HTML),
            15 => new question_answer(15, '3', 0, '', FORMAT_HTML),
            16 => new question_answer(16, '4', 1, '', FORMAT_HTML),
        );

        $qdata->hints = array(
            new question_hint_with_parts(0, 'Hint 1.', FORMAT_HTML, false, false),
            new question_hint_with_parts(0, 'Hint 2.', FORMAT_HTML, false, false),
        );

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($qdata);

        $expectedxml = '<!-- question: 123  -->
  <question type="multichoice">
    <name>
      <text>Multiple choice question</text>
    </name>
    <questiontext format="html">
      <text>Which are the even numbers?</text>
    </questiontext>
    <generalfeedback format="html">
      <text>The even numbers are 2 and 4.</text>
    </generalfeedback>
    <defaultgrade>2</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <single>false</single>
    <shuffleanswers>false</shuffleanswers>
    <answernumbering>abc</answernumbering>
    <correctfeedback format="html">
      <text><![CDATA[<p>Your answer is correct.</p>]]></text>
    </correctfeedback>
    <partiallycorrectfeedback format="html">
      <text><![CDATA[<p>Your answer is partially correct.</p>]]></text>
    </partiallycorrectfeedback>
    <incorrectfeedback format="html">
      <text><![CDATA[<p>Your answer is incorrect.</p>]]></text>
    </incorrectfeedback>
    <shownumcorrect/>
    <answer fraction="0">
      <text>1</text>
      <feedback format="html">
        <text></text>
      </feedback>
    </answer>
    <answer fraction="100">
      <text>2</text>
      <feedback format="html">
        <text></text>
      </feedback>
    </answer>
    <answer fraction="0">
      <text>3</text>
      <feedback format="html">
        <text></text>
      </feedback>
    </answer>
    <answer fraction="100">
      <text>4</text>
      <feedback format="html">
        <text></text>
      </feedback>
    </answer>
    <hint format="html">
      <text>Hint 1.</text>
    </hint>
    <hint format="html">
      <text>Hint 2.</text>
    </hint>
  </question>
';

        $this->assert_same_xml($expectedxml, $xml);
    }

    public function test_import_numerical() {
        $xml = '  <question type="numerical">
    <name>
      <text>Numerical question</text>
    </name>
    <questiontext format="html">
      <text>What is the answer?</text>
    </questiontext>
    <generalfeedback>
      <text>General feedback: Think Hitch-hikers guide to the Galaxy.</text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.1</penalty>
    <hidden>0</hidden>
    <answer fraction="100">
      <text>42</text>
      <feedback>
        <text>Well done!</text>
      </feedback>
      <tolerance>0.001</tolerance>
    </answer>
    <answer fraction="0">
      <text>13</text>
      <feedback>
        <text>What were you thinking?!</text>
      </feedback>
      <tolerance>1</tolerance>
    </answer>
    <answer fraction="0">
      <text>*</text>
      <feedback>
        <text>Completely wrong.</text>
      </feedback>
      <tolerance></tolerance>
    </answer>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->import_numerical($xmldata['question']);

        $expectedq = new stdClass;
        $expectedq->qtype = 'numerical';
        $expectedq->name = 'Numerical question';
        $expectedq->questiontext = 'What is the answer?';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = 'General feedback: Think Hitch-hikers guide to the Galaxy.';
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.1;

        $expectedq->answer = array('42', '13', '*');
        $expectedq->fraction = array(1, 0, 0);
        $expectedq->feedback = array('Well done!', 'What were you thinking?!', 'Completely wrong.');
        $expectedq->tolerance = array(0.001, 1, 0);

        $this->assert(new CheckSpecifiedFieldsExpectation($expectedq), $q);
    }

    public function test_export_numerical() {
        question_bank::load_question_definition_classes('numerical');

        $qdata = new stdClass;
        $qdata->id = 123;
        $qdata->qtype = 'numerical';
        $qdata->name = 'Numerical question';
        $qdata->questiontext = 'What is the answer?';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'General feedback: Think Hitch-hikers guide to the Galaxy.';
        $qdata->defaultmark = 1;
        $qdata->length = 1;
        $qdata->penalty = 0.1;
        $qdata->hidden = 0;

        $qdata->options->answers = array(
            new qtype_numerical_answer('42', 1, 'Well done!', 0.001),
            new qtype_numerical_answer('13', 0, 'What were you thinking?!', 1),
            new qtype_numerical_answer('*', 0, 'Completely wrong.', ''),
        );

        $qdata->options->units = array();

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($qdata);

        $expectedxml = '<!-- question: 123  -->
  <question type="numerical">
    <name>
      <text>Numerical question</text>
    </name>
    <questiontext format="html">
      <text>What is the answer?</text>
    </questiontext>
    <generalfeedback>
      <text>General feedback: Think Hitch-hikers guide to the Galaxy.</text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.1</penalty>
    <hidden>0</hidden>
    <answer fraction="100">
      <text>42</text>
      <feedback>
        <text>Well done!</text>
      </feedback>
      <tolerance>0.001</tolerance>
    </answer>
    <answer fraction="0">
      <text>13</text>
      <feedback>
        <text>What were you thinking?!</text>
      </feedback>
      <tolerance>1</tolerance>
    </answer>
    <answer fraction="0">
      <text>*</text>
      <feedback>
        <text>Completely wrong.</text>
      </feedback>
      <tolerance>0</tolerance>
    </answer>
  </question>
';

        $this->assert_same_xml($expectedxml, $xml);
    }

    public function test_import_shortanswer() {
        $xml = '  <question type="shortanswer">
    <name>
      <text>Short answer question</text>
    </name>
    <questiontext format="html">
      <text>Fill in the gap in this sequence: Alpha, ________, Gamma.</text>
    </questiontext>
    <generalfeedback>
      <text>The answer is Beta.</text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <usecase>0</usecase>
    <answer fraction="100">
      <text>Beta</text>
      <feedback>
        <text>Well done!</text>
      </feedback>
    </answer>
    <answer fraction="0">
      <text>*</text>
      <feedback>
        <text>Doh!</text>
      </feedback>
    </answer>
    <hint>
      <text>Hint 1</text>
    </hint>
    <hint>
      <text>Hint 2</text>
    </hint>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->import_shortanswer($xmldata['question']);

        $expectedq = new stdClass;
        $expectedq->qtype = 'shortanswer';
        $expectedq->name = 'Short answer question';
        $expectedq->questiontext = 'Fill in the gap in this sequence: Alpha, ________, Gamma.';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = 'The answer is Beta.';
        $expectedq->usecase = false;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;

        $expectedq->answer = array('Beta', '*');
        $expectedq->fraction = array(1, 0);
        $expectedq->feedback = array('Well done!', 'Doh!');

        $this->assert(new CheckSpecifiedFieldsExpectation($expectedq), $q);
    }

    public function test_export_shortanswer() {
        $qdata = new stdClass;
        $qdata->id = 123;
        $qdata->contextid = 0;
        $qdata->qtype = 'shortanswer';
        $qdata->name = 'Short answer question';
        $qdata->questiontext = 'Fill in the gap in this sequence: Alpha, ________, Gamma.';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'The answer is Beta.';
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->defaultmark = 1;
        $qdata->length = 1;
        $qdata->penalty = 0.3333333;
        $qdata->hidden = 0;

        $qdata->options->usecase = 0;

        $qdata->options->answers = array(
            13 => new question_answer(13, 'Beta', 1, 'Well done!', FORMAT_HTML),
            14 => new question_answer(14, '*', 0, 'Doh!', FORMAT_HTML),
        );

        $qdata->hints = array(
            new question_hint(0, 'Hint 1', FORMAT_HTML),
            new question_hint(0, 'Hint 2', FORMAT_HTML),
        );

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($qdata);

        $expectedxml = '<!-- question: 123  -->
  <question type="shortanswer">
    <name>
      <text>Short answer question</text>
    </name>
    <questiontext format="html">
      <text>Fill in the gap in this sequence: Alpha, ________, Gamma.</text>
    </questiontext>
    <generalfeedback>
      <text>The answer is Beta.</text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <usecase>0</usecase>
    <answer fraction="100">
      <text>Beta</text>
      <feedback>
        <text>Well done!</text>
      </feedback>
    </answer>
    <answer fraction="0">
      <text>*</text>
      <feedback>
        <text>Doh!</text>
      </feedback>
    </answer>
    <hint>
      <text>Hint 1</text>
    </hint>
    <hint>
      <text>Hint 2</text>
    </hint>
  </question>
';

        $this->assert_same_xml($expectedxml, $xml);
    }

    public function test_import_truefalse() {
        $xml = '  <question type="truefalse">
    <name>
      <text>True false question</text>
    </name>
    <questiontext format="html">
      <text>The answer is true.</text>
    </questiontext>
    <generalfeedback>
      <text>General feedback: You should have chosen true.</text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>1</penalty>
    <hidden>0</hidden>
    <answer fraction="100">
      <text>true</text>
      <feedback>
        <text>Well done!</text>
      </feedback>
    </answer>
    <answer fraction="0">
      <text>false</text>
      <feedback>
        <text>Doh!</text>
      </feedback>
    </answer>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->import_truefalse($xmldata['question']);

        $expectedq = new stdClass;
        $expectedq->qtype = 'truefalse';
        $expectedq->name = 'True false question';
        $expectedq->questiontext = 'The answer is true.';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = 'General feedback: You should have chosen true.';
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 1;

        $expectedq->feedbacktrue = 'Well done!';
        $expectedq->feedbackfalse = 'Doh!';
        $expectedq->correctanswer = true;

        $this->assert(new CheckSpecifiedFieldsExpectation($expectedq), $q);
    }

    public function test_export_truefalse() {
        $qdata = new stdClass;
        $qdata->id = 12;
        $qdata->contextid = 0;
        $qdata->qtype = 'truefalse';
        $qdata->name = 'True false question';
        $qdata->questiontext = 'The answer is true.';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'General feedback: You should have chosen true.';
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->defaultmark = 1;
        $qdata->length = 1;
        $qdata->penalty = 1;
        $qdata->hidden = 0;

        $qdata->options->answers = array(
            1 => new question_answer(1, 'True', 1, 'Well done!', FORMAT_HTML),
            2 => new question_answer(2, 'False', 0, 'Doh!', FORMAT_HTML),
        );
        $qdata->options->trueanswer = 1;
        $qdata->options->falseanswer = 2;

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($qdata);

        $expectedxml = '<!-- question: 12  -->
  <question type="truefalse">
    <name>
      <text>True false question</text>
    </name>
    <questiontext format="html">
      <text>The answer is true.</text>
    </questiontext>
    <generalfeedback format="html">
      <text>General feedback: You should have chosen true.</text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>1</penalty>
    <hidden>0</hidden>
    <answer fraction="100">
      <text>true</text>
      <feedback format="html">
        <text>Well done!</text>
      </feedback>
    </answer>
    <answer fraction="0">
      <text>false</text>
      <feedback format="html">
        <text>Doh!</text>
      </feedback>
    </answer>
  </question>
';

        $this->assert_same_xml($expectedxml, $xml);
    }
}
