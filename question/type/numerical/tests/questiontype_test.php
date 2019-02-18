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

global $CFG;
require_once($CFG->dirroot . '/question/type/numerical/questiontype.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/numerical/edit_numerical_form.php');

/**
 * Unit tests for question/type/numerical/questiontype.php.
 *
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_test extends advanced_testcase {
    public static $includecoverage = array(
        'question/type/questiontypebase.php',
        'question/type/numerical/questiontype.php'
    );

    protected $tolerance = 0.00000001;
    protected $qtype;

    protected function setUp() {
        $this->qtype = new qtype_numerical();
    }

    protected function tearDown() {
        $this->qtype = null;
    }

    protected function get_test_question_data() {
        $q = new stdClass;
        $q->id = 1;
        $q->options = new stdClass();
        $q->options->unitpenalty = 0;
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
        $this->assertEquals($this->qtype->name(), 'numerical');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $q = $this->get_test_question_data();
        $this->assertEquals(0.1, $this->qtype->get_random_guess_score($q));
    }

    public function test_get_possible_responses() {
        $q = $this->get_test_question_data();

        $this->assertEquals(array(
            $q->id => array(
                13 => new question_possible_response('42 m (41.5..42.5)', 1),
                14 => new question_possible_response('*', 0.1),
                null => question_possible_response::no_response()
            ),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_get_possible_responses_no_star() {
        $q = $this->get_test_question_data();
        unset($q->options->answers[14]);

        $this->assertEquals(array(
            $q->id => array(
                13 => new question_possible_response('42 m (41.5..42.5)', 1),
                0 => new question_possible_response(
                        get_string('didnotmatchanyanswer', 'question'), 0),
                null => question_possible_response::no_response()
            ),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_question_saving_pi() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $questiondata = test_question_maker::get_question_data('numerical');
        $formdata = test_question_maker::get_question_form_data('numerical');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category(array());

        $formdata->category = "{$cat->id},{$cat->contextid}";
        qtype_numerical_edit_form::mock_submit((array)$formdata);

        $form = qtype_numerical_test_helper::get_question_editing_form($cat, $questiondata);

        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $actualquestionsdata = question_load_questions(array($returnedfromsave->id));
        $actualquestiondata = end($actualquestionsdata);

        foreach ($questiondata as $property => $value) {
            if (!in_array($property, array('options'))) {
                $this->assertAttributeEquals($value, $property, $actualquestiondata);
            }
        }

        foreach ($questiondata->options as $optionname => $value) {
            if (!in_array($optionname, array('answers'))) {
                $this->assertAttributeEquals($value, $optionname, $actualquestiondata->options);
            }
        }

        foreach ($questiondata->options->answers as $ansindex => $answer) {
            $actualanswer = array_shift($actualquestiondata->options->answers);
            foreach ($answer as $ansproperty => $ansvalue) {
                // This question does not use 'answerformat', will ignore it.
                if (!in_array($ansproperty, array('id', 'question', 'answerformat'))) {
                    $this->assertAttributeEquals($ansvalue, $ansproperty, $actualanswer);
                }
            }
        }
    }

    public function test_is_valid_number() {
        $this->assertTrue(qtype_numerical::is_valid_number('1,001'));
        $this->assertTrue(qtype_numerical::is_valid_number('1.001'));
        $this->assertTrue(qtype_numerical::is_valid_number('1'));
        $this->assertTrue(qtype_numerical::is_valid_number('1,e8'));
        $this->assertFalse(qtype_numerical::is_valid_number('1001 xxx'));
        $this->assertTrue(qtype_numerical::is_valid_number('1.e8'));
    }
}
