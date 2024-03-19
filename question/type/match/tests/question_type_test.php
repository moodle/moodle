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

namespace qtype_match;

use qtype_match;
use qtype_match_edit_form;
use question_bank;
use question_possible_response;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/match/questiontype.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/match/edit_match_form.php');


/**
 * Unit tests for the matching question definition class.
 *
 * @package   qtype_match
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_type_test extends \advanced_testcase {
    /** @var qtype_match instance of the question type class to test. */
    protected $qtype;

    protected function setUp(): void {
        $this->qtype = new qtype_match();
    }

    protected function tearDown(): void {
        $this->qtype = null;
    }

    protected function get_test_question_data() {
        global $USER;
        $q = new \stdClass();
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
        $q->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $q->version = 1;
        $q->versionid = 0;
        $q->questionbankentryid = 0;
        $q->idnumber = null;
        $q->timecreated = time();
        $q->timemodified = time();
        $q->createdby = $USER->id;
        $q->modifiedby = $USER->id;

        $q->options = new \stdClass();
        $q->options->shuffleanswers = false;
        \test_question_maker::set_standard_combined_feedback_fields($q->options);

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

    public function test_make_question_instance() {
        $questiondata = \test_question_maker::get_question_data('match', 'trickynums');
        $question = question_bank::make_question($questiondata);
        $this->assertEquals($questiondata->name, $question->name);
        $this->assertEquals($questiondata->questiontext, $question->questiontext);
        $this->assertEquals($questiondata->questiontextformat, $question->questiontextformat);
        $this->assertEquals($questiondata->generalfeedback, $question->generalfeedback);
        $this->assertEquals($questiondata->generalfeedbackformat, $question->generalfeedbackformat);
        $this->assertInstanceOf('qtype_match', $question->qtype);
        $this->assertEquals($questiondata->options->shuffleanswers, $question->shufflestems);

        $this->assertEquals(
                [14 => 'System.out.println(0);', 15 => 'System.out.println(0.0);'],
                $question->stems);

        $this->assertEquals([14 => '0', 15 => '0.0', 16 => 'NULL'], $question->choices);

        $this->assertEquals([14 => 14, 15 => 15], $question->right);
    }

    public function test_get_random_guess_score() {
        $q = $this->get_test_question_data();
        $this->assertEqualsWithDelta(0.3333333, $this->qtype->get_random_guess_score($q), 0.0000001);
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


    public function test_question_saving_foursubq() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $questiondata = \test_question_maker::get_question_data('match');
        $formdata = \test_question_maker::get_question_form_data('match');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category(array());

        $formdata->category = "{$cat->id},{$cat->contextid}";

        qtype_match_edit_form::mock_submit((array)$formdata);

        $form = \qtype_match_test_helper::get_question_editing_form($cat, $questiondata);
        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        // Create a new question version with the form submission.
        unset($questiondata->id);
        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $actualquestionsdata = question_load_questions([$returnedfromsave->id], 'qbe.idnumber');
        $actualquestiondata = end($actualquestionsdata);

        foreach ($questiondata as $property => $value) {
            if (!in_array($property, ['id', 'timemodified', 'timecreated', 'options', 'stamp',
                'versionid', 'questionbankentryid'])) {
                if (!empty($actualquestiondata)) {
                    $this->assertEquals($value, $actualquestiondata->$property);
                }
            }
        }

        foreach ($questiondata->options as $optionname => $value) {
            if ($optionname != 'subquestions') {
                $this->assertEquals($value, $actualquestiondata->options->$optionname);
            }
        }

        $this->assertObjectHasProperty('subquestions', $actualquestiondata->options);

        $subqpropstoignore = array('id');
        foreach ($questiondata->options->subquestions as $subq) {
            $actualsubq = array_shift($actualquestiondata->options->subquestions);
            foreach ($subq as $subqproperty => $subqvalue) {
                if (!in_array($subqproperty, $subqpropstoignore)) {
                    $this->assertEquals($subqvalue, $actualsubq->$subqproperty);
                }
            }
        }
    }
}
