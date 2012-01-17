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
 * Unit tests for (some of) question/type/calculated/questiontype.php.
 *
 * @package    qtype_calculated
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/calculated/questiontype.php');


/**
 * Unit tests for question/type/calculated/questiontype.php.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated_test extends UnitTestCase {
    public static $includecoverage = array(
        'question/type/questiontypebase.php',
        'question/type/calculated/questiontype.php'
    );

    protected $tolerance = 0.00000001;
    protected $qtype;

    public function setUp() {
        $this->qtype = new qtype_calculated();
    }

    public function tearDown() {
        $this->qtype = null;
    }

    public function test_name() {
        $this->assertEqual($this->qtype->name(), 'calculated');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $q = test_question_maker::get_question_data('calculated');
        $q->options->answers[17]->fraction = 0.1;
        $this->assertEqual(0.1, $this->qtype->get_random_guess_score($q));
    }

    protected function get_possible_response($ans, $tolerance, $type) {
        $a = new stdClass();
        $a->answer = $ans;
        $a->tolerance = $tolerance;
        $a->tolerancetype = get_string($type, 'qtype_numerical');
        return get_string('answerwithtolerance', 'qtype_calculated', $a);
    }

    public function test_get_possible_responses() {
        $q = test_question_maker::get_question_data('calculated');

        $this->assertEqual(array(
            $q->id => array(
                13 => new question_possible_response(
                        $this->get_possible_response('{a} + {b}', 0.001, 'nominal'), 1.0),
                14 => new question_possible_response(
                        $this->get_possible_response('{a} - {b}', 0.001, 'nominal'), 0.0),
                17 => new question_possible_response('*', 0.0),
                null => question_possible_response::no_response()
            ),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_get_possible_responses_no_star() {
        $q = test_question_maker::get_question_data('calculated');
        unset($q->options->answers[17]);

        $this->assertEqual(array(
            $q->id => array(
                13 => new question_possible_response(
                        $this->get_possible_response('{a} + {b}', 0.001, 'nominal'), 1),
                14 => new question_possible_response(
                        $this->get_possible_response('{a} - {b}', 0.001, 'nominal'), 0),
                0  => new question_possible_response(
                        get_string('didnotmatchanyanswer', 'question'), 0),
                null => question_possible_response::no_response()
            ),
        ), $this->qtype->get_possible_responses($q));
    }
}
