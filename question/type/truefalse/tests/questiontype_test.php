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
 * Unit tests for the true-false question definition class.
 *
 * @package    qtype
 * @subpackage truefalse
 * @copyright  2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/truefalse/questiontype.php');


/**
 * Unit tests for the true-false question definition class.
 *
 * @copyright  2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_truefalse_test extends advanced_testcase {
    protected $qtype;

    protected function setUp() {
        $this->qtype = new qtype_truefalse();
    }

    protected function tearDown() {
        $this->qtype = null;
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'truefalse');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $this->assertEquals(0.5, $this->qtype->get_random_guess_score(null));
    }

    public function test_get_possible_responses() {
        $q = new stdClass();
        $q->id = 1;
        $q->options = new stdClass();
        $q->options->trueanswer = 1;
        $q->options->falseanswer = 2;
        $q->options->answers[1] = (object) array('fraction' => 1);
        $q->options->answers[2] = (object) array('fraction' => 0);

        $this->assertEquals(array(
            $q->id => array(
                0 => new question_possible_response(get_string('false', 'qtype_truefalse'), 0),
                1 => new question_possible_response(get_string('true', 'qtype_truefalse'), 1),
                null => question_possible_response::no_response()),
        ), $this->qtype->get_possible_responses($q));
    }
}
