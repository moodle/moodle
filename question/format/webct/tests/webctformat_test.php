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
 * Unit tests for Web CT question importer.
 *
 * @package    qformat_webct
 * @copyright  2013 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/format/webct/format.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the webct question import format.
 *
 * @copyright  2013 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_webct_test extends question_testcase {

    public function make_test() {
        $lines = file(__DIR__ . '/fixtures/sample_webct.txt');
        return $lines;
    }

    public function test_import_match() {

        $txt = $this->make_test();
        $importer = new qformat_webct();
        $questions = $importer->readquestions($txt);
        $q = $questions[3];

        $expectedq = new stdClass();
        $expectedq->qtype = 'match';
        $expectedq->name = 'Classify the animals.';
        $expectedq->questiontext = '<i>Classify the animals.</i>';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->correctfeedback = array(
                'text' => '',
                'format' => FORMAT_HTML,
                'files' => array(),
            );
        $expectedq->partiallycorrectfeedback = array(
                'text' => '',
                'format' => FORMAT_HTML,
                'files' => array(),
            );
        $expectedq->incorrectfeedback = array(
                'text' => '',
                'format' => FORMAT_HTML,
                'files' => array(),
                );
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_MOODLE;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;
        $expectedq->shuffleanswers = get_config('quiz', 'shuffleanswers');
        $expectedq->subquestions = array(
            1 => array('text' => 'cat', 'format' => FORMAT_HTML),
            2 => array('text' => 'frog', 'format' => FORMAT_HTML),
            3 => array('text' => 'newt', 'format' => FORMAT_HTML));
        $expectedq->subanswers = array(1 => 'mammal', 2 => 'amphibian', 3 => 'amphibian');

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_import_multichoice_single() {

        $txt = $this->make_test();

        $importer = new qformat_webct();
        $questions = $importer->readquestions($txt);
        $q = $questions[1];

        $expectedq = new stdClass();
        $expectedq->qtype = 'multichoice';
        $expectedq->single = 1;
        $expectedq->name = 'USER-2';
        $expectedq->questiontext = '<font size="+1">What\'s between orange and green in the spectrum?</font>';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->correctfeedback = array(
                'text' => '',
                'format' => FORMAT_HTML,
                'files' => array(),
            );
        $expectedq->partiallycorrectfeedback = array(
                'text' => '',
                'format' => FORMAT_HTML,
                'files' => array(),
            );
        $expectedq->incorrectfeedback = array(
                'text' => '',
                'format' => FORMAT_HTML,
                'files' => array(),
                );
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_MOODLE;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;
        $expectedq->shuffleanswers = get_config('quiz', 'shuffleanswers');
        $expectedq->answer = array(
                1 => array(
                    'text' => 'red',
                    'format' => FORMAT_HTML,
                ),
                2 => array(
                    'text' => 'yellow',
                    'format' => FORMAT_HTML,
                ),
                3 => array(
                    'text' => 'blue',
                    'format' => FORMAT_HTML,
                )
            );
        $expectedq->fraction = array(
                1 => 0,
                2 => 1,
                3 => 0,
            );
        $expectedq->feedback = array(
                1 => array(
                    'text' => 'Red is not between orange and green in the spectrum but yellow is.',
                    'format' => FORMAT_HTML,
                ),
                2 => array(
                    'text' => 'You gave the right answer.',
                    'format' => FORMAT_HTML,
                ),
                3 => array(
                    'text' => 'Blue is not between orange and green in the spectrum but yellow is.',
                    'format' => FORMAT_HTML,
                )
            );

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_import_multichoice_multi() {

        $txt = $this->make_test();

        $importer = new qformat_webct();
        $questions = $importer->readquestions($txt);
        $q = $questions[2];

        $expectedq = new stdClass();
        $expectedq->qtype = 'multichoice';
        $expectedq->single = 0;
        $expectedq->name = 'USER-3';
        $expectedq->questiontext = '<i>What\'s between orange and green in the spectrum?</i>';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->correctfeedback = array(
                'text' => '',
                'format' => FORMAT_HTML,
                'files' => array(),
            );
        $expectedq->partiallycorrectfeedback = array(
                'text' => '',
                'format' => FORMAT_HTML,
                'files' => array(),
            );
        $expectedq->incorrectfeedback = array(
                'text' => '',
                'format' => FORMAT_HTML,
                'files' => array(),
                );
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_MOODLE;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->penalty = 0.3333333;
        $expectedq->shuffleanswers = get_config('quiz', 'shuffleanswers');
        $expectedq->answer = array(
                1 => array(
                    'text' => 'yellow',
                    'format' => FORMAT_HTML,
                ),
                2 => array(
                    'text' => 'red',
                    'format' => FORMAT_HTML,
                ),
                3 => array(
                    'text' => 'off-beige',
                    'format' => FORMAT_HTML,
                ),
                4 => array(
                    'text' => 'blue',
                    'format' => FORMAT_HTML,
                )
            );
        $expectedq->fraction = array(
                1 => 0.5,
                2 => 0,
                3 => 0.5,
                4 => 0,
            );
        $expectedq->feedback = array(
                1 => array(
                    'text' => 'True, yellow is between orange and green in the spectrum,',
                    'format' => FORMAT_HTML,
                ),
                2 => array(
                    'text' => 'False, red is not between orange and green in the spectrum,',
                    'format' => FORMAT_HTML,
                ),
                3 => array(
                    'text' => 'True, off-beige is between orange and green in the spectrum,',
                    'format' => FORMAT_HTML,
                ),
                4 => array(
                    'text' => 'False, red is not between orange and green in the spectrum,',
                    'format' => FORMAT_HTML,
                )
            );

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_import_truefalse() {

        $txt = $this->make_test();

        $importer = new qformat_webct();
        $questions = $importer->readquestions($txt);
        $q = $questions[0];

        $expectedq = new stdClass();
        $expectedq->qtype = 'multichoice';
        $expectedq->single = 1;
        $expectedq->name = 'USER-1';
        $expectedq->questiontext = '42 is the Absolute Answer to everything.';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->correctfeedback = array(
                'text' => '',
                'format' => FORMAT_HTML,
                'files' => array(),
            );
        $expectedq->partiallycorrectfeedback = array(
                'text' => '',
                'format' => FORMAT_HTML,
                'files' => array(),
            );
        $expectedq->incorrectfeedback = array(
                'text' => '',
                'format' => FORMAT_HTML,
                'files' => array(),
                );
        $expectedq->generalfeedback = '';
        $expectedq->generalfeedbackformat = FORMAT_MOODLE;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->shuffleanswers = get_config('quiz', 'shuffleanswers');
        $expectedq->answer = array(
                1 => array(
                    'text' => 'True',
                    'format' => FORMAT_HTML,
                ),
                2 => array(
                    'text' => 'False',
                    'format' => FORMAT_HTML,
                ),
            );
        $expectedq->fraction = array(
                1 => 0,
                2 => 1,
            );
        $expectedq->feedback = array(
                1 => array(
                    'text' => '42 is the <b>Ultimate</b> Answer.',
                    'format' => FORMAT_HTML,
                ),
                2 => array(
                    'text' => '42 is the <b>Ultimate</b> Answer.',
                    'format' => FORMAT_HTML,
                ),
            );

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_import_fill_in_the_blank() {

        $txt = $this->make_test();

        $importer = new qformat_webct();
        $questions = $importer->readquestions($txt);
        $q = $questions[4];

        $expectedq = new stdClass();
        $expectedq->qtype = 'shortanswer';
        $expectedq->name = 'USER-5';
        $expectedq->questiontext = 'Name an amphibian&#58; __________';
        $expectedq->questiontextformat = FORMAT_HTML;
        $expectedq->generalfeedback = 'A frog is an amphibian';
        $expectedq->generalfeedbackformat = FORMAT_HTML;
        $expectedq->defaultmark = 1;
        $expectedq->length = 1;
        $expectedq->usecase = 0;
        $expectedq->answer = array(
                1 => 'frog',
            );
        $expectedq->fraction = array(
                1 => 1,
            );
        $expectedq->feedback = array(
                1 => array(
                    'text' => '',
                    'format' => FORMAT_HTML,
                ),
            );

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_import_essay() {

        $txt = $this->make_test();

        $importer = new qformat_webct();
        $questions = $importer->readquestions($txt);
        $q = $questions[5];

        $expectedq = new stdClass();
        $expectedq->qtype = 'essay';
        $expectedq->name = 'USER-6';
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
            );

        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }
}
