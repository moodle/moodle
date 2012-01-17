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
 * Unit tests for the shortanswer question type class.
 *
 * @package    qtype
 * @subpackage shortanswer
 * @copyright  2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/shortanswer/questiontype.php');
require_once($CFG->dirroot . '/question/engine/simpletest/helpers.php');


/**
 * Unit tests for the shortanswer question type class.
 *
 * @copyright  2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_shortanswer_test extends UnitTestCase {
    public static $includecoverage = array(
        'question/type/questiontypebase.php',
        'question/type/shortanswer/questiontype.php',
    );

    protected $qtype;

    public function setUp() {
        $this->qtype = new qtype_shortanswer();
    }

    public function tearDown() {
        $this->qtype = null;
    }

    protected function get_test_question_data() {
        return test_question_maker::get_question_data('shortanswer');
    }

    public function test_name() {
        $this->assertEqual($this->qtype->name(), 'shortanswer');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $q = test_question_maker::get_question_data('shortanswer');
        $q->options->answers[15]->fraction = 0.1;
        $this->assertEqual(0.1, $this->qtype->get_random_guess_score($q));
    }

    public function test_get_possible_responses() {
        $q = test_question_maker::get_question_data('shortanswer');

        $this->assertEqual(array(
            $q->id => array(
                13 => new question_possible_response('frog', 1),
                14 => new question_possible_response('toad', 0.8),
                15 => new question_possible_response('*', 0),
                null => question_possible_response::no_response()
            ),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_get_possible_responses_no_star() {
        $q = test_question_maker::get_question_data('shortanswer', 'frogonly');

        $this->assertEqual(array(
            $q->id => array(
                13 => new question_possible_response('frog', 1),
                0 => new question_possible_response(get_string('didnotmatchanyanswer', 'question'), 0),
                null => question_possible_response::no_response()
            ),
        ), $this->qtype->get_possible_responses($q));
    }
}
