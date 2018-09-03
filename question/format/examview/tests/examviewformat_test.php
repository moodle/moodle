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
        $xml = $xml = file_get_contents(__DIR__ . '/fixtures/examview_sample.xml');
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
