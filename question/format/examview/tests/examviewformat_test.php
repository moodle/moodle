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
 * Unit tests for the Moodle Examview format.
 *
 * @package    qformat_examview
 * @copyright  2012 jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/format/examview/format.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the examview question import format.
 *
 * @copyright  2012 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_examview_test extends question_testcase {

    public function make_test_xml() {
        $xml = "<?xml version='1.0' encoding='utf-8' standalone='yes'?>
<examview type='test' platform='Windows' app-version='4.0.2'>
    <header>
        <title>Moodle Example</title>
        <version>A</version>
    </header>
    <font-table>
        <font-entry number='1'>
            <charset>ansi</charset>
            <name>Times New Roman</name>
            <pitch>variable</pitch>
            <family>roman</family>
        </font-entry>
    </font-table>
    <preferences>
        <show>
            <show-answer value='yes'/>
            <show-difficulty value='yes'/>
            <show-reference value='yes'/>
            <show-text-objective value='yes'/>
            <show-state-objective value='yes'/>
            <show-topic value='yes'/>
            <show-keywords value='yes'/>
            <show-misc value='yes'/>
            <show-notes value='yes'/>
            <show-rationale value='yes'/>
        </show>
        <leave-answer-space>
            <tf value='yes'/>
            <mtf value='yes'/>
            <mc value='yes'/>
            <yn value='yes'/>
            <nr value='no'/>
            <co value='no'/>
            <ma value='yes'/>
            <sa value='no'/>
            <pr value='no'/>
            <es value='no'/>
            <ca value='no'/>
            <ot value='no'/>
        </leave-answer-space>
        <question-type-order value='tf,mtf,mc,yn,nr,co,ma,sa,pr,es,ca,ot'/>
        <section-page-break value='no'/>
        <bi-display-mode value='mc'/>
        <tf-show-choices value='no'/>
        <mc-conserve-paper value='no'/>
        <mc-choice-sequence value='abcde'/>
        <show-answer-lines value='no'/>
        <question-numbering value='continuous'/>
        <answer-style template='a.' style='none'/>
        <number-style template='1.' style='none'/>
        <max-question-id value='9'/>
        <max-narrative-id value='0'/>
        <max-group-id value='1'/>
        <default-font target='title'>
            <number>1</number>
            <size>13</size>
            <style>bold</style>
            <text-rgb>#000000</text-rgb>
        </default-font>
        <default-font target='sectiontitle'>
            <number>1</number>
            <size>11</size>
            <style>bold</style>
            <text-rgb>#000000</text-rgb>
        </default-font>
        <default-font target='questionnumber'>
            <number>1</number>
            <size>11</size>
            <text-rgb>#000000</text-rgb>
        </default-font>
        <default-font target='answerchoice'>
            <number>1</number>
            <size>11</size>
            <text-rgb>#000000</text-rgb>
        </default-font>
        <default-font target='newquestiondefault'>
            <number>1</number>
            <size>11</size>
            <text-rgb>#000000</text-rgb>
        </default-font>
    </preferences>
    <test-header page='first'><para tabs='R10440'><b>Name: ________________________  Class: ___________________  Date: __________    ID: <field field-type='version'/></b></para></test-header>
    <test-header page='subsequent'><para tabs='R10440'><b>Name: ________________________    ID: <field field-type='version'/></b></para></test-header>
    <test-footer page='first'><para justify='center'><field field-type='pageNumber'/>
</para></test-footer>
    <test-footer page='subsequent'><para justify='center'><field field-type='pageNumber'/>
</para></test-footer>
    <instruction type='tf'><b>True/False</b><font size='10'>
</font><i>Indicate whether the sentence or statement is true or false.</i></instruction>
    <instruction type='mtf'><b>Modified True/False</b><font size='10'>
</font><i>Indicate whether the sentence or statement is true or false.  If false, change the identified word or phrase to make the sentence or statement true.</i></instruction>
    <instruction type='mc'><b>Multiple Choice</b><font size='10'>
</font><i>Identify the letter of the choice that best completes the statement or answers the question.</i></instruction>
    <instruction type='yn'><b>Yes/No</b><font size='10'>
</font><i>Indicate whether you agree with the sentence or statement.</i></instruction>
    <instruction type='nr'><b>Numeric Response</b></instruction>
    <instruction type='co'><b>Completion</b><font size='10'>
</font><i>Complete each sentence or statement.</i></instruction>
    <instruction type='ma'><b>Matching</b></instruction>
    <instruction type='sa'><b>Short Answer</b></instruction>
    <instruction type='pr'><b>Problem</b></instruction>
    <instruction type='es'><b>Essay</b></instruction>
    <instruction type='ca'><b>Case</b></instruction>
    <instruction type='ot'><b>Other</b></instruction>
    <question type='tf' question-id='1' bank-id='0'>
        <text>42 is the Absolute Answer to everything.</text>
        <rationale>42 is the Ultimate Answer.</rationale>
        <answer>F</answer>
    </question>
    <question type='mc' question-id='2' bank-id='0'>
        <text><font size='10'>What's between orange and green in the spectrum?</font></text>
        <choices columns='2'>
            <choice-a><font size='10'>red</font></choice-a>
            <choice-b><font size='10'>yellow</font></choice-b>
            <choice-c><font size='10'>blue</font></choice-c>
        </choices>
        <answer>B</answer>
    </question>
    <question type='nr' question-id='3' bank-id='0'>
        <text>This is a numeric response question.  How much is 12 * 2?</text>
        <answer>24</answer>
        <info>
            <answer-space>-1</answer-space>
        </info>
    </question>
    <matching-group group-id='1' bank-id='0' name='Matching 1'>
        <text>Classify the animals.</text>
        <choices columns='2'>
            <choice-a>insect</choice-a>
            <choice-b>amphibian</choice-b>
            <choice-c>mammal</choice-c>
        </choices>
    </matching-group>
    <question type='ma' question-id='6' bank-id='0' group='Matching 1'>
        <text>cat</text>
        <answer>C</answer>
    </question>
    <question type='ma' question-id='7' bank-id='0' group='Matching 1'>
        <text>frog</text>
        <answer>B</answer>
    </question>
    <question type='ma' question-id='8' bank-id='0' group='Matching 1'>
        <text>newt</text>
        <answer>B</answer>
    </question>
    <question type='sa' question-id='5' bank-id='0'>
        <text>Name an amphibian: __________.</text>
        <answer>frog</answer>
        <info>
            <answer-space>-1</answer-space>
        </info>
    </question>
    <question type='es' question-id='4' bank-id='0'>
        <text>How are you?</text>
        <answer>Examview answer for essay questions will be imported as informations for graders.</answer>
        <info>
            <answer-space>-1</answer-space>
        </info>
    </question>
</examview>";
        return array(0=>$xml);
    }

    public function test_import_truefalse() {

        $xml = $this->make_test_xml();
        $questions = array();

        $importer = new qformat_examview();
        $questions = $importer->readquestions($xml);
        $q = $questions[0];

        $expectedq = new stdClass();
        $expectedq->qtype = 'truefalse';
        $expectedq->name = '42 is the Absolute Answer to everything.';
        $expectedq->questiontext = "42 is the Absolute Answer to everything.";
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_MOODLE;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->correctanswer = 0;
        $expectedq->feedbacktrue = array(
                'text' => get_string('incorrect', 'question'),
                'format' => FORMAT_HTML,
                'files' => array(),
            );
        $expectedq->feedbackfalse = array(
                'text' => get_string('correct', 'question'),
                'format' => FORMAT_HTML,
                'files' => array(),
            );

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }
    public function test_import_multichoice_single() {
        $xml = $this->make_test_xml();
        $questions = array();

        $importer = new qformat_examview();
        $questions = $importer->readquestions($xml);
        $q = $questions[1];

        $expectedq = new stdClass();
        $expectedq->qtype = 'multichoice';
        $expectedq->single = 1;
        $expectedq->name = "What's between orange and green in the spectrum?";
        $expectedq->questiontext = "What's between orange and green in the spectrum?";
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->correctfeedback = array('text' => '',
                'format' => FORMAT_HTML, 'files' => array());
        $expectedq->partiallycorrectfeedback = array('text' => '',
                'format' => FORMAT_HTML, 'files' => array());
        $expectedq->incorrectfeedback = array('text' => '',
                'format' => FORMAT_HTML, 'files' => array());
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_MOODLE;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;
        $expectedq->shuffleanswers = get_config('quiz', 'shuffleanswers');
        $expectedq->answer = array(
                0 => array(
                    'text' => 'red',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                1 => array(
                    'text' => 'yellow',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                2 => array(
                    'text' => 'blue',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                )
            );
        $expectedq->fraction = array(0, 1, 0);
        $expectedq->feedback = array(
                0 => array(
                    'text' => get_string('incorrect', 'question'),
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                1 => array(
                    'text' => get_string('correct', 'question'),
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                2 => array(
                    'text' => get_string('incorrect', 'question'),
                    'format' => FORMAT_HTML,
                    'files' => array(),
                )
            );

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_import_numerical() {

        $xml = $this->make_test_xml();
        $questions = array();

        $importer = new qformat_examview();
        $questions = $importer->readquestions($xml);
        $q = $questions[2];

        $expectedq = new stdClass();
        $expectedq->qtype = 'numerical';
        $expectedq->name = 'This is a numeric response question.  How much is 12 * 2?';
        $expectedq->questiontext = 'This is a numeric response question.  How much is 12 * 2?';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_MOODLE;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;
        $expectedq->shuffleanswers = get_config('quiz', 'shuffleanswers');
        $expectedq->answer = array('24');
        $expectedq->fraction = array(1);
        $expectedq->feedback = array(
                0 => array(
                    'text' => get_string('correct', 'question'),
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
            );

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }



    public function test_import_fill_in_the_blank() {

        $xml = $this->make_test_xml();
        $questions = array();

        $importer = new qformat_examview();
        $questions = $importer->readquestions($xml);
        $q = $questions[3];

        $expectedq = new stdClass();
        $expectedq->qtype = 'shortanswer';
        $expectedq->name = 'Name an amphibian: __________.';
        $expectedq->questiontext = 'Name an amphibian: __________.';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_MOODLE;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->usecase = 0;
        $expectedq->answer = array('frog', '*');
        $expectedq->fraction = array(1, 0);
        $expectedq->feedback = array(
                0 => array(
                    'text' => get_string('correct', 'question'),
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                1 => array(
                    'text' => get_string('incorrect', 'question'),
                    'format' => FORMAT_HTML,
                    'files' => array(),
                )
            );

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_import_essay() {

        $xml = $this->make_test_xml();
        $questions = array();

        $importer = new qformat_examview();
        $questions = $importer->readquestions($xml);
        $q = $questions[4];

        $expectedq = new stdClass();
        $expectedq->qtype = 'essay';
        $expectedq->name = 'How are you?';
        $expectedq->questiontext = 'How are you?';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_MOODLE;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->responseformat = 'editor';
        $expectedq->responsefieldlines = 15;
        $expectedq->attachments = 0;
        $expectedq->graderinfo = array(
                'text' => 'Examview answer for essay questions will be imported as informations for graders.',
                'format' => FORMAT_HTML,
                'files' => array());

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    // Due to the way matching questions are parsed,
    // the test for matching questions is somewhat different.
    // First we test the parse_matching_groups method alone.
    // Then we test the whole process wich involve parse_matching_groups,
    // parse_ma and process_matches methods.
    public function test_parse_matching_groups() {
        $lines = $this->make_test_xml();

        $importer = new qformat_examview();
        $text = implode($lines, ' ');

        $xml = xmlize($text, 0);
        $importer->parse_matching_groups($xml['examview']['#']['matching-group']);
        $matching = $importer->matching_questions;
        $group = new stdClass();
        $group->questiontext = 'Classify the animals.';
        $group->subchoices = array('A' => 'insect', 'B' => 'amphibian', 'C' =>'mammal');
            $group->subquestions = array();
            $group->subanswers = array();
        $expectedmatching = array( 'Matching 1' => $group);

        $this->assertEquals($matching, $expectedmatching);
    }

    public function test_import_match() {

        $xml = $this->make_test_xml();
        $questions = array();

        $importer = new qformat_examview();
        $questions = $importer->readquestions($xml);
        $q = $questions[5];

        $expectedq = new stdClass();
        $expectedq->qtype = 'match';
        $expectedq->name = 'Classify the animals.';
        $expectedq->questiontext = 'Classify the animals.';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->correctfeedback = array('text' => '',
                'format' => FORMAT_HTML, 'files' => array());
        $expectedq->partiallycorrectfeedback = array('text' => '',
                'format' => FORMAT_HTML, 'files' => array());
        $expectedq->incorrectfeedback = array('text' => '',
                'format' => FORMAT_HTML, 'files' => array());
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_MOODLE;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;
        $expectedq->shuffleanswers = get_config('quiz', 'shuffleanswers');
        $expectedq->subquestions = array(
            array('text' => '', 'format' => FORMAT_HTML, 'files' => array()),
            array('text' => 'frog', 'format' => FORMAT_HTML, 'files' => array()),
            array('text' => 'newt', 'format' => FORMAT_HTML, 'files' => array()),
            array('text' => 'cat', 'format' => FORMAT_HTML, 'files' => array()));
        $expectedq->subanswers = array('insect', 'amphibian', 'amphibian', 'mammal');

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }
}
