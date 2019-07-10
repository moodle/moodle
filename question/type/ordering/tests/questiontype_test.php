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
 * Unit tests for the ordering question type class.
 *
 * @package   qtype_ordering
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/ordering/questiontype.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/ordering/edit_ordering_form.php');


/**
 * Unit tests for the ordering question type class.
 *
 * @copyright 20018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering_test extends advanced_testcase {
    /** @var qtype_ordering instance of the question type class to test. */
    protected $qtype;

    protected function setUp() {
        $this->qtype = new qtype_ordering();
    }

    protected function tearDown() {
        $this->qtype = null;
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'ordering');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_question_saving() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $questiondata = test_question_maker::get_question_data('ordering');
        $formdata = test_question_maker::get_question_form_data('ordering');

        /** @var core_question_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category([]);

        $formdata->category = "{$cat->id},{$cat->contextid}";

        qtype_ordering_edit_form::mock_submit((array) $formdata);

        $form = qtype_ordering_test_helper::get_question_editing_form($cat, $questiondata);
        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $actualquestiondata = question_bank::load_question_data($returnedfromsave->id);

        foreach ($questiondata as $property => $value) {
            if (!in_array($property, array('id', 'version', 'timemodified', 'timecreated', 'options', 'stamp'))) {
                $this->assertAttributeEquals($value, $property, $actualquestiondata);
            }
        }

        foreach ($questiondata->options as $optionname => $value) {
            if ($optionname != 'answers') {
                $this->assertAttributeEquals($value, $optionname, $actualquestiondata->options);
            }
        }

        foreach ($questiondata->options->answers as $answer) {
            $actualanswer = array_shift($actualquestiondata->options->answers);
            foreach ($answer as $ansproperty => $ansvalue) {
                if ($ansproperty === 'question') {
                    $this->assertAttributeEquals($returnedfromsave->id, $ansproperty, $actualanswer);
                } else if ($ansproperty !== 'id') {
                    $this->assertAttributeEquals($ansvalue, $ansproperty, $actualanswer);
                }
            }
        }
    }

    public function test_get_possible_responses() {
        $questiondata = test_question_maker::get_question_data('ordering');
        $possibleresponses = $this->qtype->get_possible_responses($questiondata);
        $expectedresponseclasses = array(
            'Modular' => array(
                    1 => new question_possible_response('Position 1', 0.1666667),
                    2 => new question_possible_response('Position 2', 0),
                    3 => new question_possible_response('Position 3', 0),
                    4 => new question_possible_response('Position 4', 0),
                    5 => new question_possible_response('Position 5', 0),
                    6 => new question_possible_response('Position 6', 0),
            ),
            'Object' => array(
                    1 => new question_possible_response('Position 1', 0),
                    2 => new question_possible_response('Position 2', 0.1666667),
                    3 => new question_possible_response('Position 3', 0),
                    4 => new question_possible_response('Position 4', 0),
                    5 => new question_possible_response('Position 5', 0),
                    6 => new question_possible_response('Position 6', 0),
            ),
            'Oriented' => array(
                    1 => new question_possible_response('Position 1', 0),
                    2 => new question_possible_response('Position 2', 0),
                    3 => new question_possible_response('Position 3', 0.1666667),
                    4 => new question_possible_response('Position 4', 0),
                    5 => new question_possible_response('Position 5', 0),
                    6 => new question_possible_response('Position 6', 0),
            ),
            'Dynamic' => array(
                    1 => new question_possible_response('Position 1', 0),
                    2 => new question_possible_response('Position 2', 0),
                    3 => new question_possible_response('Position 3', 0),
                    4 => new question_possible_response('Position 4', 0.1666667),
                    5 => new question_possible_response('Position 5', 0),
                    6 => new question_possible_response('Position 6', 0),
            ),
            'Learning' => array(
                    1 => new question_possible_response('Position 1', 0),
                    2 => new question_possible_response('Position 2', 0),
                    3 => new question_possible_response('Position 3', 0),
                    4 => new question_possible_response('Position 4', 0),
                    5 => new question_possible_response('Position 5', 0.1666667),
                    6 => new question_possible_response('Position 6', 0),
            ),
            'Environment' => array(
                    1 => new question_possible_response('Position 1', 0),
                    2 => new question_possible_response('Position 2', 0),
                    3 => new question_possible_response('Position 3', 0),
                    4 => new question_possible_response('Position 4', 0),
                    5 => new question_possible_response('Position 5', 0),
                    6 => new question_possible_response('Position 6', 0.1666667),
            ),
        );
        $this->assertEquals($expectedresponseclasses, $possibleresponses, '', 0.0000005);
    }

    public function test_get_answernumbering() {
        $questiondata = test_question_maker::get_question_data('ordering');
        $expected = qtype_ordering_question::ANSWER_NUMBERING_DEFAULT;
        $actual = $this->qtype->get_answernumbering($questiondata);
        $this->assertEquals($expected, $actual);

        $questiondata->options->answernumbering = 'abc';
        $expected = 'abc';
        $actual = $this->qtype->get_answernumbering($questiondata);
        $this->assertEquals($expected, $actual);

        $questiondata->options->answernumbering = 'ABCD';
        $expected = 'ABCD';
        $actual = $this->qtype->get_answernumbering($questiondata);
        $this->assertEquals($expected, $actual);

        $questiondata->options->answernumbering = '123';
        $expected = '123';
        $actual = $this->qtype->get_answernumbering($questiondata);
        $this->assertEquals($expected, $actual);

        $questiondata->options->answernumbering = 'iii';
        $expected = 'iii';
        $actual = $this->qtype->get_answernumbering($questiondata);
        $this->assertEquals($expected, $actual);

        $questiondata->options->answernumbering = 'III';
        $expected = 'III';
        $actual = $this->qtype->get_answernumbering($questiondata);
        $this->assertEquals($expected, $actual);
    }

}
