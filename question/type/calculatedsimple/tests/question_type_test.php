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

namespace qtype_calculatedsimple;

use qtype_calculated_dataset_loader;
use qtype_calculatedsimple;
use qtype_calculatedsimple_edit_form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/calculatedsimple/questiontype.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/calculatedsimple/edit_calculatedsimple_form.php');


/**
 * Unit tests for the calculatedsimple question type class.
 *
 * @package    qtype_calculatedsimple
 * @copyright  2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \question_type
 * @covers \qtype_calculatedsimple
 * @covers \question_wizard_form
 * @covers \question_edit_form
 * @covers \qtype_calculated_edit_form
 * @covers \qtype_calculatedsimple_edit_form
 *
 */
class question_type_test extends \advanced_testcase {
    protected $qtype;

    protected function setUp(): void {
        parent::setUp();
        $this->qtype = new qtype_calculatedsimple();
    }

    protected function tearDown(): void {
        $this->qtype = null;
        parent::tearDown();
    }

    public function test_name(): void {
        $this->assertEquals($this->qtype->name(), 'calculatedsimple');
    }

    public function test_can_analyse_responses(): void {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }


    public function test_question_saving_sumwithvariants(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $questiondata = \test_question_maker::get_question_data('calculatedsimple', 'sumwithvariants');
        $formdata = \test_question_maker::get_question_form_data('calculatedsimple', 'sumwithvariants');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category(array());

        $formdata->category = "{$cat->id},{$cat->contextid}";
        qtype_calculatedsimple_edit_form::mock_submit((array)$formdata);

        $form = \qtype_calculatedsimple_test_helper::get_question_editing_form($cat, $questiondata);

        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $actualquestionsdata = question_load_questions(array($returnedfromsave->id));
        $actualquestiondata = end($actualquestionsdata);

        foreach ($questiondata as $property => $value) {
            if (!in_array($property, array('id', 'timemodified', 'timecreated', 'options', 'idnumber'))) {
                $this->assertEquals($value, $actualquestiondata->$property);
            }
        }

        foreach ($questiondata->options as $optionname => $value) {
            if ($optionname != 'answers') {
                $this->assertEquals($value, $actualquestiondata->options->$optionname);
            }
        }

        foreach ($questiondata->options->answers as $answer) {
            $actualanswer = array_shift($actualquestiondata->options->answers);
            foreach ($answer as $ansproperty => $ansvalue) {
                if (!in_array($ansproperty, array('id', 'question', 'answerformat'))) {
                    $this->assertEquals($ansvalue, $actualanswer->$ansproperty);
                }
            }
        }

        $datasetloader = new qtype_calculated_dataset_loader($actualquestiondata->id);

        $this->assertEquals(10, $datasetloader->get_number_of_items());

        for ($itemno = 1; $itemno <= 10; $itemno++) {
            $item = $datasetloader->get_values($itemno);
            $this->assertEquals((float)$formdata->number[($itemno -1)*2 + 2], $item['a']);
            $this->assertEquals((float)$formdata->number[($itemno -1)*2 + 1], $item['b']);
        }
    }
}
