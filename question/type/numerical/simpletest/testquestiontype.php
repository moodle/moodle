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
 * Unit tests for (some of) question/type/numerical/questiontype.php.
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/numerical/questiontype.php');


/**
 * Unit tests for question/type/numerical/questiontype.php.
 *
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_test extends UnitTestCase {
    public static $includecoverage = array('question/type/questiontype.php', 'question/type/numerical/questiontype.php');
    protected $tolerance = 0.00000001;
    protected $qtype;

    public function setUp() {
        $this->qtype = new qtype_numerical();
    }

    public function tearDown() {
        $this->qtype = null;   
    }

    protected function get_test_question_data() {
        $q = new stdClass;
        $q->id = 1;
        $q->options->answers[13] = (object) array(
            'id' => 13,
            'answer' => 42,
            'fraction' => 1,
            'feedback' => 'yes',
            'feedbackformat' => FORMAT_MOODLE,
            'tolerance' => 0.5
        );
        $q->options->answers[14] = (object) array(
            'id' => 14,
            'answer' => '*',
            'fraction' => 0.1,
            'feedback' => 'no',
            'feedbackformat' => FORMAT_MOODLE,
            'tolerance' => ''
        );

        $q->options->units = array(
            (object) array('unit' => 'm', 'multiplier' => 1),
            (object) array('unit' => 'cm', 'multiplier' => 0.01)
        );

        return $q;
    }

    public function test_name() {
        $this->assertEqual($this->qtype->name(), 'numerical');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $q = $this->get_test_question_data();
        $this->assertEqual(0.1, $this->qtype->get_random_guess_score($q));
    }

    public function test_get_possible_responses() {
        $q = $this->get_test_question_data();

        $this->assertEqual(array(
            $q->id => array(
                13 => new question_possible_response('42 m (41.5..42.5)', 1),
                14 => new question_possible_response('*', 0.1),
                null => question_possible_response::no_response()),
        ), $this->qtype->get_possible_responses($q));
    }
}
