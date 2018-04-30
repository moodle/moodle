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

global $CFG;
require_once($CFG->dirroot . '/question/type/calculated/questiontype.php');
require_once($CFG->dirroot . '/question/type/calculated/tests/helper.php');


/**
 * Unit tests for question/type/calculated/questiontype.php.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated_test extends advanced_testcase {
    public static $includecoverage = array(
        'question/type/questiontypebase.php',
        'question/type/calculated/questiontype.php'
    );

    protected $tolerance = 0.00000001;
    protected $qtype;

    protected function setUp() {
        $this->qtype = new qtype_calculated();
    }

    protected function tearDown() {
        $this->qtype = null;
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'calculated');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $q = test_question_maker::get_question_data('calculated');
        $q->options->answers[17]->fraction = 0.1;
        $this->assertEquals(0.1, $this->qtype->get_random_guess_score($q));
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

        $this->assertEquals(array(
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

        $this->assertEquals(array(
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

    public function test_placehodler_regex() {
        preg_match_all(qtype_calculated::PLACEHODLER_REGEX, '= {={a} + {b}}', $matches);
        $this->assertEquals([['{a}', '{b}'], ['a', 'b']], $matches);
    }

    public function test_formulas_in_text_regex() {
        preg_match_all(qtype_calculated::FORMULAS_IN_TEXT_REGEX, '= {={a} + {b}}', $matches);
        $this->assertEquals([['{={a} + {b}}'], ['{a} + {b}']], $matches);
    }

    public function test_find_dataset_names() {
        $this->assertEquals([], $this->qtype->find_dataset_names('Frog.'));

        $this->assertEquals(['a' => 'a', 'b' => 'b'],
                $this->qtype->find_dataset_names('= {={a} + {b}}'));

        $this->assertEquals(['a' => 'a', 'b' => 'b'],
                $this->qtype->find_dataset_names('What is {a} plus {b}? (Hint, it is not {={a}*{b}}.)'));

        $this->assertEquals(['a' => 'a', 'b' => 'b', 'c' => 'c'],
                $this->qtype->find_dataset_names('
                        <p>If called with $a = {a} and $b = {b}, what does this PHP function return?</p>
                        <pre>
                        /**
                         * What does this do?
                         */
                        function mystery($a, $b) {
                            return {c}*$a + $b;
                        }
                        </pre>
                        '));
    }
}
