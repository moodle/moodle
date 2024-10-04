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

namespace qtype_shortanswer;

use qtype_shortanswer;
use qtype_shortanswer_edit_form;
use question_possible_response;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/shortanswer/questiontype.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/shortanswer/edit_shortanswer_form.php');

/**
 * Unit tests for the shortanswer question type class.
 *
 * @package    qtype_shortanswer
 * @copyright  2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \question_type
 * @covers \qtype_shortanswer
 */
final class question_type_test extends \advanced_testcase {
    protected $qtype;

    protected function setUp(): void {
        parent::setUp();
        $this->qtype = new qtype_shortanswer();
    }

    protected function tearDown(): void {
        $this->qtype = null;
        parent::tearDown();
    }

    protected function get_test_question_data() {
        return \test_question_maker::get_question_data('shortanswer');
    }

    public function test_name(): void {
        $this->assertEquals($this->qtype->name(), 'shortanswer');
    }

    public function test_can_analyse_responses(): void {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score(): void {
        $q = \test_question_maker::get_question_data('shortanswer');
        $q->options->answers[15]->fraction = 0.1;
        $this->assertEquals(0.1, $this->qtype->get_random_guess_score($q));
    }

    public function test_get_possible_responses(): void {
        $q = \test_question_maker::get_question_data('shortanswer');

        $this->assertEquals(array(
            $q->id => array(
                13 => new question_possible_response('frog', 1),
                14 => new question_possible_response('toad', 0.8),
                15 => new question_possible_response('*', 0),
                null => question_possible_response::no_response()
            ),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_get_possible_responses_no_star(): void {
        $q = \test_question_maker::get_question_data('shortanswer', 'frogonly');

        $this->assertEquals(array(
            $q->id => array(
                13 => new question_possible_response('frog', 1),
                0 => new question_possible_response(get_string('didnotmatchanyanswer', 'question'), 0),
                null => question_possible_response::no_response()
            ),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_question_saving_frogtoad(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $questiondata = \test_question_maker::get_question_data('shortanswer');
        $formdata = \test_question_maker::get_question_form_data('shortanswer');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category(array());

        $formdata->category = "{$cat->id},{$cat->contextid}";
        qtype_shortanswer_edit_form::mock_submit((array)$formdata);

        $form = \qtype_shortanswer_test_helper::get_question_editing_form($cat, $questiondata);

        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        // Create a new question version with the form submission.
        unset($questiondata->id);
        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $actualquestionsdata = question_load_questions([$returnedfromsave->id], 'qbe.idnumber');
        $actualquestiondata = end($actualquestionsdata);

        foreach ($questiondata as $property => $value) {
            if (!in_array($property, ['id', 'timemodified', 'timecreated', 'options', 'hints'])) {
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
                // This question does not use 'answerformat', will ignore it.
                if (!in_array($ansproperty, array('id', 'question', 'answerformat'))) {
                    $this->assertEquals($ansvalue, $actualanswer->$ansproperty);
                }
            }
        }

        $this->assertCount(1, $actualquestiondata->hints);
        $hint = array_pop($actualquestiondata->hints);
        $this->assertEquals($formdata->hint[0]['text'], $hint->hint);
        $this->assertEquals($formdata->hint[0]['format'], $hint->hintformat);
    }

    public function test_question_saving_trims_answers(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $questiondata = \test_question_maker::get_question_data('shortanswer');
        $formdata = \test_question_maker::get_question_form_data('shortanswer');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category(array());

        $formdata->category = "{$cat->id},{$cat->contextid}";
        $formdata->answer[0] = '   frog   ';
        qtype_shortanswer_edit_form::mock_submit((array)$formdata);

        $form = \qtype_shortanswer_test_helper::get_question_editing_form($cat, $questiondata);

        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $actualquestionsdata = question_load_questions(array($returnedfromsave->id));
        $actualquestiondata = end($actualquestionsdata);

        $firstsavedanswer = reset($questiondata->options->answers);
        $this->assertEquals('frog', $firstsavedanswer->answer);
    }
}
