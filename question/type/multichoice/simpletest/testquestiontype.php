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
 * Unit tests for the mulitple choice question definition class.
 *
 * @package    qtype
 * @subpackage multichoice
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/multichoice/questiontype.php');


/**
 * Unit tests for the multiple choice question definition class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multichoice_test extends UnitTestCase {
    protected $qtype;

    public function setUp() {
        $this->qtype = new qtype_multichoice();
    }

    public function tearDown() {
        $this->qtype = null;
    }

    public function test_name() {
        $this->assertEqual($this->qtype->name(), 'multichoice');
    }

    protected function get_test_question_data() {
        $q = new stdClass();
        $q->id = 1;
        $q->options->single = true;
        $q->options->answers[1] = (object) array('answer' => 'frog',
                'answerformat' => FORMAT_HTML, 'fraction' => 1);
        $q->options->answers[2] = (object) array('answer' => 'toad',
                'answerformat' => FORMAT_HTML, 'fraction' => 0);

        return $q;
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $q = $this->get_test_question_data();
        $this->assertEqual(0.5, $this->qtype->get_random_guess_score($q));
    }

    public function test_get_random_guess_score_multi() {
        $q = $this->get_test_question_data();
        $q->options->single = false;
        $this->assertNull($this->qtype->get_random_guess_score($q));
    }

    public function test_get_possible_responses_single() {
        $q = $this->get_test_question_data();
        $responses = $this->qtype->get_possible_responses($q);

        $this->assertEqual(array(
            $q->id => array(
                1 => new question_possible_response('frog', 1),
                2 => new question_possible_response('toad', 0),
                null => question_possible_response::no_response(),
            )), $this->qtype->get_possible_responses($q));
    }

    public function test_get_possible_responses_multi() {
        $q = $this->get_test_question_data();
        $q->options->single = false;

        $this->assertEqual(array(
            1 => array(1 => new question_possible_response('frog', 1)),
            2 => array(2 => new question_possible_response('toad', 0)),
        ), $this->qtype->get_possible_responses($q));
    }
}
