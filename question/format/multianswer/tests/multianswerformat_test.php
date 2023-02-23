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

namespace qformat_multianswer;

use qformat_multianswer;
use question_check_specified_fields_expectation;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/format/multianswer/format.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Unit tests for the Embedded answer (Cloze) question importer.
 *
 * @package   qformat_multianswer
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class multianswerformat_test extends \question_testcase {

    public function test_import() {
        $lines = file(__DIR__ . '/fixtures/questions.multianswer.txt');

        $importer = new qformat_multianswer();
        $qs = $importer->readquestions($lines);

        $expectedq = (object) array(
            'name' => 'Match the following cities with the correct state:
* San Francisco: {#1}
* ...',
            'questiontext' => 'Match the following cities with the correct state:
* San Francisco: {#1}
* Tucson: {#2}
* Los Angeles: {#3}
* Phoenix: {#4}
The capital of France is {#5}.
',
            'questiontextformat' => FORMAT_MOODLE,
            'generalfeedback' => '',
            'generalfeedbackformat' => FORMAT_MOODLE,
            'qtype' => 'multianswer',
            'defaultmark' => 5,
            'penalty' => 0.3333333,
            'length' => 1,
        );

        $this->assertEquals(1, count($qs));
        $this->assert(new question_check_specified_fields_expectation($expectedq), $qs[0]);

        $this->assertEquals('multichoice', $qs[0]->options->questions[1]->qtype);
        $this->assertEquals('multichoice', $qs[0]->options->questions[2]->qtype);
        $this->assertEquals('multichoice', $qs[0]->options->questions[3]->qtype);
        $this->assertEquals('multichoice', $qs[0]->options->questions[4]->qtype);
        $this->assertEquals('shortanswer', $qs[0]->options->questions[5]->qtype);
    }

    public function test_read_brokencloze_1() {
        $lines = file(__DIR__ . '/fixtures/broken_multianswer_1.txt');
        $importer = new qformat_multianswer();

        // The importer echoes some errors, so we need to capture and check that.
        ob_start();
        $questions = $importer->readquestions($lines);
        $output = ob_get_contents();
        ob_end_clean();

        // Check that there were some expected errors.
        $this->assertStringContainsString('Error importing question', $output);
        $this->assertStringContainsString('Invalid embedded answers (Cloze) question', $output);
        $this->assertStringContainsString('This type of question requires at least 2 choices', $output);

        // No question  have been imported.
        $this->assertCount(0, $questions);
    }

    public function test_read_brokencloze_2() {
        $lines = file(__DIR__ . '/fixtures/broken_multianswer_2.txt');
        $importer = new qformat_multianswer();

        // The importer echoes some errors, so we need to capture and check that.
        ob_start();
        $questions = $importer->readquestions($lines);
        $output = ob_get_contents();
        ob_end_clean();

        // Check that there were some expected errors.
        $this->assertStringContainsString('Error importing question', $output);
        $this->assertStringContainsString('Invalid embedded answers (Cloze) question', $output);
        $this->assertStringContainsString('One of the answers should have a score of 100% so it is possible to get full marks for this question.',
                $output);

        // No question  have been imported.
        $this->assertCount(0, $questions);
    }

    public function test_read_brokencloze_3() {
        $lines = file(__DIR__ . '/fixtures/broken_multianswer_3.txt');
        $importer = new qformat_multianswer();

        // The importer echoes some errors, so we need to capture and check that.
        ob_start();
        $questions = $importer->readquestions($lines);
        $output = ob_get_contents();
        ob_end_clean();

        // Check that there were some expected errors.
        $this->assertStringContainsString('Error importing question', $output);
        $this->assertStringContainsString('Invalid embedded answers (Cloze) question', $output);
        $this->assertStringContainsString('The answer must be a number, for example -1.234 or 3e8, or \'*\'.', $output);

        // No question  have been imported.
        $this->assertCount(0, $questions);
    }

    public function test_read_brokencloze_4() {
        $lines = file(__DIR__ . '/fixtures/broken_multianswer_4.txt');
        $importer = new qformat_multianswer();

        // The importer echoes some errors, so we need to capture and check that.
        ob_start();
        $questions = $importer->readquestions($lines);
        $output = ob_get_contents();
        ob_end_clean();

        // Check that there were some expected errors.
        $this->assertStringContainsString('Error importing question', $output);
        $this->assertStringContainsString('Invalid embedded answers (Cloze) question', $output);
        $this->assertStringContainsString('The question text must include at least one embedded answer.', $output);

        // No question  have been imported.
        $this->assertCount(0, $questions);
    }
}
