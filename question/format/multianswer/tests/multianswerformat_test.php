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
 * Unit tests for the Embedded answer (Cloze) question importer.
 *
 * @package   qformat_multianswer
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/format/multianswer/format.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the Embedded answer (Cloze) question importer.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_multianswer_test extends question_testcase {

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
}
