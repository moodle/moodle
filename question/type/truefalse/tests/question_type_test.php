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

namespace qtype_truefalse;

use qtype_truefalse;
use qtype_truefalse_edit_form;
use question_bank;
use question_possible_response;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/truefalse/questiontype.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/truefalse/edit_truefalse_form.php');

/**
 * Unit tests for the true-false question definition class.
 *
 * @package    qtype_truefalse
 * @copyright  2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_type_test extends \advanced_testcase {
    protected $qtype;

    protected function setUp(): void {
        $this->qtype = new qtype_truefalse();
    }

    protected function tearDown(): void {
        $this->qtype = null;
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'truefalse');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $this->assertEquals(0.5, $this->qtype->get_random_guess_score(null));
    }

    public function test_load_question() {
        $this->resetAfterTest();

        $syscontext = \context_system::instance();
        /** @var core_question_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category(['contextid' => $syscontext->id]);

        $fromform = \test_question_maker::get_question_form_data('truefalse');
        $fromform->category = $category->id . ',' . $syscontext->id;

        $question = new \stdClass();
        $question->category = $category->id;
        $question->qtype = 'truefalse';
        $question->createdby = 0;

        $this->qtype->save_question($question, $fromform);
        $questiondata = question_bank::load_question_data($question->id);

        $this->assertEquals(['id', 'category', 'parent', 'name', 'questiontext', 'questiontextformat',
                'generalfeedback', 'generalfeedbackformat', 'defaultmark', 'penalty', 'qtype',
                'length', 'stamp', 'timecreated', 'timemodified', 'createdby', 'modifiedby', 'idnumber', 'contextid',
                'status', 'versionid', 'version', 'questionbankentryid', 'categoryobject', 'options', 'hints'],
                array_keys(get_object_vars($questiondata)));
        $this->assertEquals($category->id, $questiondata->category);
        $this->assertEquals(0, $questiondata->parent);
        $this->assertEquals($fromform->name, $questiondata->name);
        $this->assertEquals($fromform->questiontext, $questiondata->questiontext);
        $this->assertEquals($fromform->questiontextformat, $questiondata->questiontextformat);
        $this->assertEquals($fromform->generalfeedback['text'], $questiondata->generalfeedback);
        $this->assertEquals($fromform->generalfeedback['format'], $questiondata->generalfeedbackformat);
        $this->assertEquals($fromform->defaultmark, $questiondata->defaultmark);
        $this->assertEquals(1, $questiondata->penalty);
        $this->assertEquals('truefalse', $questiondata->qtype);
        $this->assertEquals(1, $questiondata->length);
        $this->assertEquals(\core_question\local\bank\question_version_status::QUESTION_STATUS_READY, $questiondata->status);
        $this->assertEquals($question->createdby, $questiondata->createdby);
        $this->assertEquals($question->createdby, $questiondata->modifiedby);
        $this->assertEquals('', $questiondata->idnumber);
        $this->assertEquals($syscontext->id, $questiondata->contextid);

        // Options.
        $this->assertEquals($questiondata->id, $questiondata->options->question);
        $this->assertEquals('True', $questiondata->options->answers[$questiondata->options->trueanswer]->answer);
        $this->assertEquals('False', $questiondata->options->answers[$questiondata->options->falseanswer]->answer);
        $this->assertEquals(1.0, $questiondata->options->answers[$questiondata->options->trueanswer]->fraction);
        $this->assertEquals(0.0, $questiondata->options->answers[$questiondata->options->falseanswer]->fraction);
        $this->assertEquals('This is the right answer.',
                $questiondata->options->answers[$questiondata->options->trueanswer]->feedback);
        $this->assertEquals('This is the wrong answer.',
                $questiondata->options->answers[$questiondata->options->falseanswer]->feedback);
        $this->assertEquals(FORMAT_HTML, $questiondata->options->answers[$questiondata->options->trueanswer]->feedbackformat);
        $this->assertEquals(FORMAT_HTML, $questiondata->options->answers[$questiondata->options->falseanswer]->feedbackformat);

        // Hints.
        $this->assertEquals([], $questiondata->hints);
    }

    public function test_get_possible_responses() {
        $q = new \stdClass();
        $q->id = 1;
        $q->options = new \stdClass();
        $q->options->trueanswer = 1;
        $q->options->falseanswer = 2;
        $q->options->answers[1] = (object) array('fraction' => 1);
        $q->options->answers[2] = (object) array('fraction' => 0);

        $this->assertEquals(array(
            $q->id => array(
                0 => new question_possible_response(get_string('false', 'qtype_truefalse'), 0),
                1 => new question_possible_response(get_string('true', 'qtype_truefalse'), 1),
                null => question_possible_response::no_response()),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_question_saving_true() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $questiondata = \test_question_maker::get_question_data('truefalse');
        $formdata = \test_question_maker::get_question_form_data('truefalse');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category(array());

        $formdata->category = "{$cat->id},{$cat->contextid}";
        qtype_truefalse_edit_form::mock_submit((array)$formdata);

        $form = \qtype_truefalse_test_helper::get_question_editing_form($cat, $questiondata);

        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $actualquestionsdata = question_load_questions([$returnedfromsave->id], 'qbe.idnumber');
        $actualquestiondata = end($actualquestionsdata);

        foreach ($questiondata as $property => $value) {
            if (!in_array($property, array('options'))) {
                $this->assertEquals($value, $actualquestiondata->$property);
            }
        }

        foreach ($questiondata->options as $optionname => $value) {
            if (!in_array($optionname, array('trueanswer', 'falseanswer', 'answers'))) {
                $this->assertEquals($value, $actualquestiondata->options->$optionname);
            }
        }

        $answerindexes = array();
        foreach ($questiondata->options->answers as $ansindex => $answer) {
            $actualanswer = array_shift($actualquestiondata->options->answers);
            foreach ($answer as $ansproperty => $ansvalue) {
                // This question does not use 'answerformat', will ignore it.
                if (!in_array($ansproperty, array('id', 'question', 'answerformat'))) {
                    $this->assertEquals($ansvalue, $actualanswer->$ansproperty);
                }
            }
            $answerindexes[$answer->answer] = $ansindex;
        }

        $this->assertEquals($questiondata->options->trueanswer, $answerindexes['True']);
        $this->assertEquals($questiondata->options->falseanswer, $answerindexes['False']);
    }
}
