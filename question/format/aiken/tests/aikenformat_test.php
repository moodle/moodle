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
 * Unit tests for the Moodle Aiken format.
 *
 * @package    qformat_aiken
 * @copyright  2018 Eric Merrill (eric.a.merrill@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/format/aiken/format.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the matching question definition class.
 *
 * @copyright  2018 Eric Merrill (eric.a.merrill@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aikenformat_test extends question_testcase {
    public function test_readquestions() {
        global $CFG;

        $lines = file($CFG->dirroot.'/question/format/aiken/tests/fixtures/aiken_errors.txt');
        $importer = new qformat_aiken($lines);

        // The importer echos some errors, so we need to capture and check that.
        ob_start();
        $questions = $importer->readquestions($lines);
        $output = ob_get_contents();
        ob_end_clean();

        // Check that there were some expected errors.
        $this->assertContains('Error importing question A question with too few answers', $output);
        $this->assertContains('Question must have at least 2 answers on line 3', $output);
        $this->assertContains('Question not started on line 5', $output);
        $this->assertContains('Question not started on line 7', $output);
        $this->assertContains('Error importing question A question started but not finished', $output);
        $this->assertContains('Question not completed before next question start on line 18', $output);

        // There are two expected questions.
        $this->assertCount(2, $questions);

        $q1 = null;
        $q2 = null;
        foreach ($questions as $question) {
            if ($question->name === 'A good question') {
                $q1 = $question;
            } else if ($question->name === 'A second good question') {
                $q2 = $question;
            }
        }

        // Check the first good question.
        $this->assertCount(2, $q1->answer);
        $this->assertEquals(1, $q1->fraction[0]);
        $this->assertEquals('Correct', $q1->answer[0]['text']);
        $this->assertEquals('Incorrect', $q1->answer[1]['text']);

        // Check the second good question.
        $this->assertCount(2, $q2->answer);
        $this->assertEquals(1, $q2->fraction[1]);
        $this->assertEquals('Incorrect (No space)', $q2->answer[0]['text']);
        $this->assertEquals('Correct (No space)', $q2->answer[1]['text']);
    }
}
