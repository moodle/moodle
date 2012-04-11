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
 * Unit tests for the matching question definition class.
 *
 * @package    qtype
 * @subpackage match
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/match/questiontype.php');


/**
 * Unit tests for the matching question definition class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_match_test extends advanced_testcase {
    /** @var qtype_match instance of the question type class to test. */
    protected $qtype;

    protected function setUp() {
        $this->qtype = new qtype_match();
    }

    protected function tearDown() {
        $this->qtype = null;
    }

    protected function get_test_question_data() {
        global $USER;
        $q = new stdClass();
        $q->id = 0;
        $q->name = 'Matching question';
        $q->category = 0;
        $q->contextid = 0;
        $q->parent = 0;
        $q->questiontext = 'Classify the animals.';
        $q->questiontextformat = FORMAT_HTML;
        $q->generalfeedback = 'General feedback.';
        $q->generalfeedbackformat = FORMAT_HTML;
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;
        $q->length = 1;
        $q->stamp = make_unique_id_code();
        $q->version = make_unique_id_code();
        $q->hidden = 0;
        $q->timecreated = time();
        $q->timemodified = time();
        $q->createdby = $USER->id;
        $q->modifiedby = $USER->id;

        $q->options = new stdClass();
        $q->options->shuffleanswers = false;
        test_question_maker::set_standard_combined_feedback_fields($q->options);

        $q->options->subquestions = array(
            14 => (object) array(
                'id' => 14,
                'questiontext' => 'frog',
                'questiontextformat' => FORMAT_HTML,
                'answertext' => 'amphibian'),
            15 => (object) array(
                'id' => 15,
                'questiontext' => 'cat',
                'questiontextformat' => FORMAT_HTML,
                'answertext' => 'mammal'),
            16 => (object) array(
                'id' => 16,
                'questiontext' => 'newt',
                'questiontextformat' => FORMAT_HTML,
                'answertext' => 'amphibian'),
            17 => (object) array(
                'id' => 17,
                'questiontext' => '',
                'questiontextformat' => FORMAT_HTML,
                'answertext' => 'insect'),
        );

        return $q;
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'match');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $q = $this->get_test_question_data();
        $this->assertEquals(0.3333333, $this->qtype->get_random_guess_score($q), '', 0.0000001);
    }

    public function test_get_possible_responses() {
        $q = $this->get_test_question_data();

        $this->assertEquals(array(
            14 => array(
                14 => new question_possible_response('frog: amphibian', 1/3),
                15 => new question_possible_response('frog: mammal', 0),
                17 => new question_possible_response('frog: insect', 0),
                null => question_possible_response::no_response()),
            15 => array(
                14 => new question_possible_response('cat: amphibian', 0),
                15 => new question_possible_response('cat: mammal', 1/3),
                17 => new question_possible_response('cat: insect', 0),
                null => question_possible_response::no_response()),
            16 => array(
                14 => new question_possible_response('newt: amphibian', 1/3),
                15 => new question_possible_response('newt: mammal', 0),
                17 => new question_possible_response('newt: insect', 0),
                null => question_possible_response::no_response()),
        ), $this->qtype->get_possible_responses($q));
    }
}
