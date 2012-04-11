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
 * This file contains tests for the 'missing' question type.
 *
 * @package    qtype
 * @subpackage missingtype
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(dirname(__FILE__) . '/../../../engine/tests/helpers.php');
require_once(dirname(__FILE__) . '/../../../behaviour/deferredfeedback/behaviour.php');
require_once(dirname(__FILE__) . '/../question.php');


/**
 * Unit tests for the 'missing' question type.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_missing_test extends question_testcase {

    protected function get_unknown_questiondata() {
        $questiondata = new stdClass();
        $questiondata->id = 0;
        $questiondata->category = 0;
        $questiondata->contextid = 0;
        $questiondata->parent = 0;
        $questiondata->name = 'Test';
        $questiondata->questiontext = 'This is the question text.';
        $questiondata->questiontextformat = FORMAT_HTML;
        $questiondata->generalfeedback = 'This is the general feedback.';
        $questiondata->generalfeedbackformat = FORMAT_HTML;
        $questiondata->defaultmark = 1;
        $questiondata->penalty = 0.3333333;
        $questiondata->qtype = 'strange_unknown';
        $questiondata->length = 1;
        $questiondata->stamp = make_unique_id_code();
        $questiondata->version = make_unique_id_code();
        $questiondata->hidden = 0;
        $questiondata->timecreated = 0;
        $questiondata->timemodified = 0;
        $questiondata->createdby = 0;
        $questiondata->modifiedby = 0;

        return $questiondata;
    }

    public function test_cannot_grade() {
        $q = new qtype_missingtype_question();
        $this->setExpectedException('moodle_exception');
        $q->grade_response(array());
    }

    public function test_load_qtype_strict() {
        $this->setExpectedException('moodle_exception');
        $qtype = question_bank::get_qtype('strange_unknown');
    }

    public function test_load_qtype_lax() {
        $qtype = question_bank::get_qtype('strange_unknown', false);
        $this->assertInstanceOf('qtype_missingtype', $qtype);
    }

    public function test_make_question() {
        $questiondata = $this->get_unknown_questiondata();
        $q = question_bank::make_question($questiondata);
        $this->assertInstanceOf('qtype_missingtype_question', $q);
        $this->assertEquals($q->questiontext, html_writer::tag('div',
                get_string('missingqtypewarning', 'qtype_missingtype'),
                array('class' => 'warning missingqtypewarning')) .
                $questiondata->questiontext);
    }

    public function test_render_missing() {
        $questiondata = $this->get_unknown_questiondata();
        $q = question_bank::make_question($questiondata);
        $qa = new testable_question_attempt($q, 0);

        $step = new question_attempt_step(array('answer' => 'frog'));
        $step->set_state(question_state::$todo);
        $qa->add_step($step);
        $qa->set_behaviour(new qbehaviour_deferredfeedback($qa, 'deferredfeedback'));

        $output = $qa->render(new question_display_options(), '1');

        $this->assertRegExp('/' .
                preg_quote($qa->get_question()->questiontext, '/') . '/', $output);
        $this->assertRegExp('/' .
                preg_quote(get_string('missingqtypewarning', 'qtype_missingtype')) . '/', $output);
        $this->assert(new question_contains_tag_with_attribute(
                'div', 'class', 'warning missingqtypewarning'), $output);
    }

    public function test_get_question_summary() {
        $q = new qtype_missingtype_question();
        $q->questiontext = '<b>Test</b>';
        $this->assertEquals('TEST', $q->get_question_summary());
    }

    public function test_summarise_response() {
        $q = new qtype_missingtype_question();
        $this->assertNull($q->summarise_response(array('a' => 'irrelevant')));
    }

    public function test_can_analyse_responses() {
        $qtype = new qtype_missingtype();
        $this->assertFalse($qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $qtype = new qtype_missingtype();
        $this->assertNull($qtype->get_random_guess_score(null));
    }

    public function test_get_possible_responses() {
        $qtype = new qtype_missingtype();
        $this->assertEquals(array(), $qtype->get_possible_responses(null));
    }
}
