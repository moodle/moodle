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

namespace qtype_ddimageortext;

use question_bank;
use question_possible_response;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/ddimageortext/tests/helper.php');


/**
 * Unit tests for the drag-and-drop onto image question definition class.
 *
 * @package   qtype_ddimageortext
 * @copyright 2010 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\qtype_ddimageortext::class)]
final class question_type_test extends \basic_testcase {
    /** @var qtype_ddimageortext instance of the question type class to test. */
    protected $qtype;

    protected function setUp(): void {
        parent::setUp();
        $this->qtype = question_bank::get_qtype('ddimageortext');
    }

    protected function tearDown(): void {
        $this->qtype = null;
        parent::tearDown();
    }

    public function test_name(): void {
        $this->assertEquals($this->qtype->name(), 'ddimageortext');
    }

    public function test_can_analyse_responses(): void {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_possible_responses(): void {
        $q = \test_question_maker::get_question_data('ddimageortext', 'fox');

        $this->assertEquals(
            [
                1 => [
                    1 => new question_possible_response('1. quick', 1),
                    2 => new question_possible_response('2. fox', 0),
                    '' => question_possible_response::no_response(),
                ],
                2 => [
                    1 => new question_possible_response('1. quick', 0),
                    2 => new question_possible_response('2. fox', 1),
                    '' => question_possible_response::no_response(),
                ],
                3 => [
                    3 => new question_possible_response('3. lazy', 1),
                    4 => new question_possible_response('4. dog', 0),
                    '' => question_possible_response::no_response(),
                ],
                4 => [
                    3 => new question_possible_response('3. lazy', 0),
                    4 => new question_possible_response('4. dog', 1),
                    '' => question_possible_response::no_response(),
                ],
            ],
            $this->qtype->get_possible_responses($q)
        );
    }
}
