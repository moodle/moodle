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
 * Unit tests for the multianswer question definition class.
 *
 * @package    qtype
 * @subpackage multianswer
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/multianswer/questiontype.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/multianswer/edit_multianswer_form.php');


/**
 * Unit tests for the multianswer question definition class.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_test extends advanced_testcase {
    /** @var qtype_multianswer instance of the question type class to test. */
    protected $qtype;

    protected function setUp() {
        $this->qtype = new qtype_multianswer();
    }

    protected function tearDown() {
        $this->qtype = null;
    }

    protected function get_test_question_data() {
        global $USER;
        $q = new stdClass();
        $q->id = 0;
        $q->name = 'Simple multianswer';
        $q->category = 0;
        $q->contextid = 0;
        $q->parent = 0;
        $q->questiontext =
                'Complete this opening line of verse: "The {#1} and the {#2} went to sea".';
        $q->questiontextformat = FORMAT_HTML;
        $q->generalfeedback = 'Generalfeedback: It\'s from "The Owl and the Pussy-cat" by Lear: ' .
                '"The owl and the pussycat went to see';
        $q->generalfeedbackformat = FORMAT_HTML;
        $q->defaultmark = 2;
        $q->penalty = 0.3333333;
        $q->length = 1;
        $q->stamp = make_unique_id_code();
        $q->version = make_unique_id_code();
        $q->hidden = 0;
        $q->timecreated = time();
        $q->timemodified = time();
        $q->createdby = $USER->id;
        $q->modifiedby = $USER->id;

        $sadata = new stdClass();
        $sadata->id = 1;
        $sadata->qtype = 'shortanswer';
        $sadata->defaultmark = 1;
        $sadata->options->usecase = true;
        $sadata->options->answers[1] = (object) array('answer' => 'Bow-wow', 'fraction' => 0);
        $sadata->options->answers[2] = (object) array('answer' => 'Wiggly worm', 'fraction' => 0);
        $sadata->options->answers[3] = (object) array('answer' => 'Pussy-cat', 'fraction' => 1);

        $mcdata = new stdClass();
        $mcdata->id = 1;
        $mcdata->qtype = 'multichoice';
        $mcdata->defaultmark = 1;
        $mcdata->options->single = true;
        $mcdata->options->answers[1] = (object) array('answer' => 'Dog', 'fraction' => 0);
        $mcdata->options->answers[2] = (object) array('answer' => 'Owl', 'fraction' => 1);
        $mcdata->options->answers[3] = (object) array('answer' => '*', 'fraction' => 0);

        $q->options->questions = array(
            1 => $sadata,
            2 => $mcdata,
        );

        return $q;
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'multianswer');
    }

    public function test_can_analyse_responses() {
        $this->assertFalse($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $q = test_question_maker::get_question_data('multianswer', 'twosubq');
        $this->assertEquals(0.1666667, $this->qtype->get_random_guess_score($q), '', 0.0000001);
    }

    public function test_question_saving_twosubq() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $questiondata = test_question_maker::get_question_data('multianswer');
        $formdata = test_question_maker::get_question_form_data('multianswer');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category(array());

        $formdata->category = "{$cat->id},{$cat->contextid}";
        qtype_multianswer_edit_form::mock_submit((array)$formdata);

        $form = qtype_multianswer_test_helper::get_question_editing_form($cat, $questiondata);

        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $actualquestionsdata = question_load_questions(array($returnedfromsave->id));
        $actualquestiondata = end($actualquestionsdata);

        foreach ($questiondata as $property => $value) {
            if (!in_array($property, array('id', 'version', 'timemodified', 'timecreated', 'options', 'hints', 'stamp'))) {
                $this->assertAttributeEquals($value, $property, $actualquestiondata);
            }
        }

        foreach ($questiondata->options as $optionname => $value) {
            if ($optionname != 'questions') {
                $this->assertAttributeEquals($value, $optionname, $actualquestiondata->options);
            }
        }

        foreach ($questiondata->hints as $hint) {
            $actualhint = array_shift($actualquestiondata->hints);
            foreach ($hint as $property => $value) {
                if (!in_array($property, array('id', 'questionid', 'options'))) {
                    $this->assertAttributeEquals($value, $property, $actualhint);
                }
            }
        }

        $this->assertObjectHasAttribute('questions', $actualquestiondata->options);

        $subqpropstoignore =
            array('id', 'category', 'parent', 'contextid', 'question', 'options', 'stamp', 'version', 'timemodified',
                'timecreated');
        foreach ($questiondata->options->questions as $subqno => $subq) {
            $actualsubq = $actualquestiondata->options->questions[$subqno];
            foreach ($subq as $subqproperty => $subqvalue) {
                if (!in_array($subqproperty, $subqpropstoignore)) {
                    $this->assertAttributeEquals($subqvalue, $subqproperty, $actualsubq);
                }
            }
            foreach ($subq->options as $optionname => $value) {
                if (!in_array($optionname, array('answers'))) {
                    $this->assertAttributeEquals($value, $optionname, $actualsubq->options);
                }
            }
            foreach ($subq->options->answers as $answer) {
                $actualanswer = array_shift($actualsubq->options->answers);
                foreach ($answer as $ansproperty => $ansvalue) {
                    // These questions do not use 'answerformat', will ignore it.
                    if (!in_array($ansproperty, array('id', 'question', 'answerformat'))) {
                        $this->assertAttributeEquals($ansvalue, $ansproperty, $actualanswer);
                    }
                }
            }
        }
    }
}
