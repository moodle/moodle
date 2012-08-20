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
 * Unit tests for the Moodle Blackboard format.
 *
 * @package    qformat_blackboard
 * @copyright  2012 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/format/blackboard/format.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the blackboard question import format.
 *
 * @copyright  2012 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_blackboard_test extends question_testcase {

    public function make_test_xml() {
        $xml = file_get_contents(__DIR__ . '/fixtures/sample_blackboard.dat');
        return $xml;
    }

    public function test_import_match() {

        $xmldata = xmlize($this->make_test_xml());
        $questions = array();

        $importer = new qformat_blackboard();
        $importer->process_matching($xmldata, $questions);
        $q = $questions[0];
        $expectedq = new stdClass();
        $expectedq->qtype = 'match';
        $expectedq->name = 'Classify the animals.';
        $expectedq->questiontext = '<i>Classify the animals.</i>';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->correctfeedback = array('text' => '',
                'format' => FORMAT_HTML, 'files' => array());
        $expectedq->partiallycorrectfeedback = array('text' => '',
                'format' => FORMAT_HTML, 'files' => array());
        $expectedq->incorrectfeedback = array('text' => '',
                'format' => FORMAT_HTML, 'files' => array());
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_HTML;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;
        $expectedq->shuffleanswers = get_config('quiz', 'shuffleanswers');
        $expectedq->subquestions = array(
            array('text' => 'cat', 'format' => FORMAT_HTML, 'files' => array()),
            array('text' => '', 'format' => FORMAT_HTML, 'files' => array()),
            array('text' => 'frog', 'format' => FORMAT_HTML, 'files' => array()),
            array('text' => 'newt', 'format' => FORMAT_HTML, 'files' => array()));
        $expectedq->subanswers = array('mammal', 'insect', 'amphibian', 'amphibian');

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_import_multichoice_single() {

        $xmldata = xmlize($this->make_test_xml());
        $questions = array();

        $importer = new qformat_blackboard();
        $importer->process_mc($xmldata, $questions);
        $q = $questions[0];

        $expectedq = new stdClass();
        $expectedq->qtype = 'multichoice';
        $expectedq->single = 1;
        $expectedq->name = 'What\'s between orange and green in the spectrum?';
        $expectedq->questiontext = '<span style="font-size:12pt">What\'s between orange and green in the spectrum?</span>';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->correctfeedback = array('text' => 'You gave the right answer.',
                'format' => FORMAT_HTML, 'files' => array());
        $expectedq->partiallycorrectfeedback = array('text' => '',
                'format' => FORMAT_HTML, 'files' => array());
        $expectedq->incorrectfeedback = array('text' => 'Only yellow is between orange and green in the spectrum.',
                'format' => FORMAT_HTML, 'files' => array());
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_HTML;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;
        $expectedq->shuffleanswers = get_config('quiz', 'shuffleanswers');
        $expectedq->answer = array(
                0 => array(
                    'text' => '<span style="font-size:12pt">red</span>',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                1 => array(
                    'text' => '<span style="font-size:12pt">yellow</span>',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                2 => array(
                    'text' => '<span style="font-size:12pt">blue</span>',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                )
            );
        $expectedq->fraction = array(0, 1, 0);
        $expectedq->feedback = array(
                0 => array(
                    'text' => '',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                1 => array(
                    'text' => '',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                2 => array(
                    'text' => '',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                )
            );

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_import_multichoice_multi() {

        $xmldata = xmlize($this->make_test_xml());
        $questions = array();

        $importer = new qformat_blackboard();
        $importer->process_ma($xmldata, $questions);
        $q = $questions[0];

        $expectedq = new stdClass();
        $expectedq->qtype = 'multichoice';
        $expectedq->single = 0;
        $expectedq->name = 'What\'s between orange and green in the spectrum?';
        $expectedq->questiontext = '<span style="font-size:12pt">What\'s between orange and green in the spectrum?</span>';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->correctfeedback = array(
                'text' => 'You gave the right answer.',
                'format' => FORMAT_HTML,
                'files' => array());
        $expectedq->partiallycorrectfeedback = array(
                'text' => 'Only yellow and off-beige are between orange and green in the spectrum.',
                'format' => FORMAT_HTML,
                'files' => array());
        $expectedq->incorrectfeedback = array(
                'text' => 'Only yellow and off-beige are between orange and green in the spectrum.',
                'format' => FORMAT_HTML,
                'files' => array());
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_HTML;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;
        $expectedq->shuffleanswers = get_config('quiz', 'shuffleanswers');
        $expectedq->answer = array(
                0 => array(
                    'text' => '<span style="font-size:12pt">yellow</span>',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                1 => array(
                    'text' => '<span style="font-size:12pt">red</span>',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                2 => array(
                    'text' => '<span style="font-size:12pt">off-beige</span>',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                3 => array(
                    'text' => '<span style="font-size:12pt">blue</span>',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                )
            );
        $expectedq->fraction = array(0.5, 0, 0.5, 0);
        $expectedq->feedback = array(
                0 => array(
                    'text' => '',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                1 => array(
                    'text' => '',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                2 => array(
                    'text' => '',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                3 => array(
                    'text' => '',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                )
            );

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_import_truefalse() {

        $xmldata = xmlize($this->make_test_xml());
        $questions = array();

        $importer = new qformat_blackboard();
        $importer->process_tf($xmldata, $questions);
        $q = $questions[0];

        $expectedq = new stdClass();
        $expectedq->qtype = 'truefalse';
        $expectedq->name = '42 is the Absolute Answer to everything.';
        $expectedq->questiontext = '<span style="font-size:12pt">42 is the Absolute Answer to everything.</span>';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_HTML;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->correctanswer = 0;
        $expectedq->feedbacktrue = array(
                'text' => '42 is the Ultimate Answer.',
                'format' => FORMAT_HTML,
                'files' => array(),
            );
        $expectedq->feedbackfalse = array(
                'text' => 'You gave the right answer.',
                'format' => FORMAT_HTML,
                'files' => array(),
            );
        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_import_fill_in_the_blank() {

        $xmldata = xmlize($this->make_test_xml());
        $questions = array();

        $importer = new qformat_blackboard();
        $importer->process_fib($xmldata, $questions);
        $q = $questions[0];

        $expectedq = new stdClass();
        $expectedq->qtype = 'shortanswer';
        $expectedq->name = 'Name an amphibian: __________.';
        $expectedq->questiontext = '<span style="font-size:12pt">Name an amphibian: __________.</span>';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_HTML;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->usecase = 0;
        $expectedq->answer = array('frog', '*');
        $expectedq->fraction = array(1, 0);
        $expectedq->feedback = array(
                0 => array(
                    'text' => '',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                ),
                1 => array(
                    'text' => '',
                    'format' => FORMAT_HTML,
                    'files' => array(),
                )
            );

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_import_essay() {

        $xmldata = xmlize($this->make_test_xml());
        $questions = array();

        $importer = new qformat_blackboard();
        $importer->process_essay($xmldata, $questions);
        $q = $questions[0];

        $expectedq = new stdClass();
        $expectedq->qtype = 'essay';
        $expectedq->name = 'How are you?';
        $expectedq->questiontext = 'How are you?';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_HTML;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->responseformat = 'editor';
        $expectedq->responsefieldlines = 15;
        $expectedq->attachments = 0;
        $expectedq->graderinfo = array(
                'text' => 'Blackboard answer for essay questions will be imported as informations for graders.',
                'format' => FORMAT_HTML,
                'files' => array());

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }
}
