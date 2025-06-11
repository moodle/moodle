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

namespace qtype_regexp;

use qtype_regexp;
use qtype_regexp_edit_form;
use question_possible_response;
use question_bank;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/regexp/questiontype.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/regexp/edit_regexp_form.php');

/**
 * Unit tests for the regexp question type class.
 *
 * @package    qtype_regexp
 * @copyright 2021 Joseph Rézeau <joseph@rezeau.org>
 * @copyright based on work by 2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class question_type_test extends \advanced_testcase {
    /** @var qtype_regexp instance of the question type class to test. */
    protected $qtype;

    protected function setUp(): void {
        parent::setUp();
        $this->qtype = question_bank::get_qtype('regexp');
    }

    protected function tearDown(): void {
        parent::tearDown();
        $this->qtype = null;
    }

    /**
     * template for common example of question instance
     * @return \stdClass
     */
    protected function get_test_question_data() {
        return \test_question_maker::get_question_data('regexp');
    }

     /**
      * Test the valuue returned by name  method.
      *
      * @covers ::name()
      */
    public function test_name(): void {
        $this->assertEquals($this->qtype->name(), 'regexp');
    }

    /**
     * Test response of can_analyse_responses
     * Which determines if this question type can perform a frequency analysis of student responses.
     *
     *  If it returns true, it must implement the get_possible_responses method, and  question_definition class must
     *  implement the classify_response method.
     *
     * @covers ::can_analyse_responses()
     */
    public function test_can_analyse_responses(): void {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    /**
     * Test the behaviour of get_possible_responses method.
     *
     * @covers ::get_possible_responses
     */
    public function test_get_possible_responses(): void {
        $q = \test_question_maker::get_question_data('regexp');

        $this->assertEquals([
            $q->id => [
                13 => new question_possible_response("it's blue, white and red", 1),
                14 => new question_possible_response("(it('s| is) |they are )?blue, white, red", 0.8),
                15 => new question_possible_response('--.*(blue|red|white).*', 0),
                16 => new question_possible_response('--.*blue.*', 0),
                17 => new question_possible_response('--.*(&&blue&&red&&white).*', 0),
                18 => new question_possible_response('.*', 0),
                null => question_possible_response::no_response(),
                ],
        ], $this->qtype->get_possible_responses($q));
    }

    /**
     * Test the behaviour of save_question method.
     *
     * @covers ::save_question
     */
    public function test_question_saving_frenchflag(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $questiondata = \test_question_maker::get_question_data('regexp');
        $formdata = \test_question_maker::get_question_form_data('regexp');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category([]);

        $formdata->category = "{$cat->id},{$cat->contextid}";
        qtype_regexp_edit_form::mock_submit((array)$formdata);

        $form = \qtype_regexp_test_helper::get_question_editing_form($cat, $questiondata);

        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $actualquestionsdata = question_load_questions([$returnedfromsave->id], 'qbe.idnumber');
        $actualquestiondata = end($actualquestionsdata);

        foreach ($questiondata as $property => $value) {
            if (!in_array($property, ['id', 'version', 'timemodified', 'timecreated', 'options'])) {
                $this->assertEquals($value, $actualquestiondata->$property);
            }
        }

        foreach ($questiondata->options as $optionname => $value) {
            if ($optionname != 'answers') {
                $this->assertEquals($value, $actualquestiondata->options->$optionname);
            }
        }
    }

}
