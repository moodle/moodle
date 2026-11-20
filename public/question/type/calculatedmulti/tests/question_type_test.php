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

namespace qtype_calculatedmulti;

use qtype_calculatedmulti;
use question_possible_response;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/calculatedmulti/questiontype.php');

/**
 * Unit tests for question/type/calculatedmulti/questiontype.php.
 *
 * @package   qtype_calculatedmulti
 * @copyright 2026 Daniel Ziegenberg <daniel@ziegenberg.at>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\qtype_calculatedmulti::class)]
final class question_type_test extends \basic_testcase {
    /** @var qtype_calculatedmulti instance of the question type class to test. */
    protected $qtype;

    protected function setUp(): void {
        parent::setUp();
        $this->qtype = new qtype_calculatedmulti();
    }

    protected function tearDown(): void {
        $this->qtype = null;
        parent::tearDown();
    }

    public function test_name(): void {
        $this->assertEquals($this->qtype->name(), 'calculatedmulti');
    }

    public function test_can_analyse_responses(): void {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    /**
     * Test that get_possible_responses returns a single set of responses for a
     * single-answer (radio) calculated multi-choice question.
     */
    public function test_get_possible_responses_singleresponse(): void {
        $qdata = new \stdClass();
        $qdata->id = 123;
        $qdata->options = new \stdClass();
        $qdata->options->single = 1;
        $qdata->options->answers = [
            13 => (object) ['answer' => '0.1', 'fraction' => 0.5],
            14 => (object) ['answer' => '*', 'fraction' => 0.1],
        ];

        $this->assertEquals([
            123 => [
                13 => new question_possible_response('0.1', 0.5),
                14 => new question_possible_response('*', 0.1),
                '' => question_possible_response::no_response(),
            ],
        ], $this->qtype->get_possible_responses($qdata));
    }

    /**
     * Test that get_possible_responses returns one set of responses per answer
     * for a multi-answer (checkbox) calculated multi-choice question.
     */
    public function test_get_possible_responses_multiresponse(): void {
        $qdata = new \stdClass();
        $qdata->id = 123;
        $qdata->options = new \stdClass();
        $qdata->options->single = 0;
        $qdata->options->answers = [
            13 => (object) ['answer' => '0.1', 'fraction' => 0.5],
            14 => (object) ['answer' => '*', 'fraction' => 0.1],
        ];

        $this->assertEquals([
            13 => [
                13 => new question_possible_response('0.1', 0.5),
            ],
            14 => [
                14 => new question_possible_response('*', 0.1),
            ],
        ], $this->qtype->get_possible_responses($qdata));
    }
}
