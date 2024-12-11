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

namespace qtype_description;

use qtype_description;
use qtype_description_edit_form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/description/questiontype.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/description/edit_description_form.php');


/**
 * Unit tests for the description question type class.
 *
 * @package    qtype_description
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class question_type_test extends \advanced_testcase {
    protected $qtype;

    protected function setUp(): void {
        parent::setUp();
        $this->qtype = new qtype_description();
    }

    protected function tearDown(): void {
        $this->qtype = null;
        parent::tearDown();
    }

    public function test_name(): void {
        $this->assertEquals($this->qtype->name(), 'description');
    }

    public function test_actual_number_of_questions(): void {
        $this->assertEquals(0, $this->qtype->actual_number_of_questions(null));
    }

    public function test_can_analyse_responses(): void {
        $this->assertFalse($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score(): void {
        $this->assertNull($this->qtype->get_random_guess_score(null));
    }

    public function test_get_possible_responses(): void {
        $this->assertEquals(array(), $this->qtype->get_possible_responses(null));
    }


    public function test_question_saving(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $questiondata = \test_question_maker::get_question_data('description');
        $formdata = \test_question_maker::get_question_form_data('description');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category(array());

        $formdata->category = "{$cat->id},{$cat->contextid}";
        qtype_description_edit_form::mock_submit((array)$formdata);

        $form = \qtype_description_test_helper::get_question_editing_form($cat, $questiondata);

        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $actualquestionsdata = question_load_questions(array($returnedfromsave->id));
        $actualquestiondata = end($actualquestionsdata);

        foreach ($questiondata as $property => $value) {
            if (!in_array($property, array('id', 'timemodified', 'timecreated', 'idnumber'))) {
                $this->assertEquals($value, $actualquestiondata->$property);
            }
        }
    }
}
