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
 * Unit tests for the mulitple choice question definition class.
 *
 * @package    qtype_multichoice
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/multichoice/questiontype.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/multichoice/edit_multichoice_form.php');

/**
 * Unit tests for the multiple choice question definition class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multichoice_test extends advanced_testcase {
    protected $qtype;

    protected function setUp() {
        $this->qtype = new qtype_multichoice();
    }

    protected function tearDown() {
        $this->qtype = null;
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'multichoice');
    }

    protected function get_test_question_data() {
        $q = new stdClass();
        $q->id = 1;
        $q->options = new stdClass();
        $q->options->single = true;
        $q->options->answers[1] = (object) array('answer' => 'frog',
                'answerformat' => FORMAT_HTML, 'fraction' => 1);
        $q->options->answers[2] = (object) array('answer' => 'toad',
                'answerformat' => FORMAT_HTML, 'fraction' => 0);

        return $q;
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $q = $this->get_test_question_data();
        $this->assertEquals(0.5, $this->qtype->get_random_guess_score($q));
    }

    public function test_get_random_guess_score_multi() {
        $q = $this->get_test_question_data();
        $q->options->single = false;
        $this->assertNull($this->qtype->get_random_guess_score($q));
    }

    public function test_get_possible_responses_single() {
        $q = $this->get_test_question_data();
        $responses = $this->qtype->get_possible_responses($q);

        $this->assertEquals(array(
            $q->id => array(
                1 => new question_possible_response('frog', 1),
                2 => new question_possible_response('toad', 0),
                null => question_possible_response::no_response(),
            )), $this->qtype->get_possible_responses($q));
    }

    public function test_get_possible_responses_multi() {
        $q = $this->get_test_question_data();
        $q->options->single = false;

        $this->assertEquals(array(
            1 => array(1 => new question_possible_response('frog', 1)),
            2 => array(2 => new question_possible_response('toad', 0)),
        ), $this->qtype->get_possible_responses($q));
    }

    public function get_question_saving_which() {
        return array(array('two_of_four'), array('one_of_four'));
    }

    /**
     * @dataProvider get_question_saving_which
     */
    public function test_question_saving_two_of_four($which) {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $questiondata = test_question_maker::get_question_data('multichoice', $which);
        $formdata = test_question_maker::get_question_form_data('multichoice', $which);

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category(array());

        $formdata->category = "{$cat->id},{$cat->contextid}";
        qtype_multichoice_edit_form::mock_submit((array)$formdata);

        $form = qtype_multichoice_test_helper::get_question_editing_form($cat, $questiondata);

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
            if ($optionname != 'answers') {
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

        foreach ($questiondata->options->answers as $answer) {
            $actualanswer = array_shift($actualquestiondata->options->answers);
            foreach ($answer as $ansproperty => $ansvalue) {
                // This question does not use 'answerformat', will ignore it.
                if (!in_array($ansproperty, array('id', 'question', 'answerformat'))) {
                    $this->assertAttributeEquals($ansvalue, $ansproperty, $actualanswer);
                }
            }
        }
    }
}
