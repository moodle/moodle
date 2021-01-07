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

    protected function setUp(): void {
        $this->qtype = new qtype_multianswer();
    }

    protected function tearDown(): void {
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
        $this->assertEqualsWithDelta(0.1666667, $this->qtype->get_random_guess_score($q), 0.0000001);
    }

    public function test_load_question() {
        $this->resetAfterTest();

        $syscontext = context_system::instance();
        /** @var core_question_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category(['contextid' => $syscontext->id]);

        $fromform = test_question_maker::get_question_form_data('multianswer');
        $fromform->category = $category->id . ',' . $syscontext->id;

        $question = new stdClass();
        $question->category = $category->id;
        $question->qtype = 'multianswer';
        $question->createdby = 0;

        // Note, $question gets modified during save because of the way subquestions
        // are extracted.
        $question = $this->qtype->save_question($question, $fromform);

        $questiondata = question_bank::load_question_data($question->id);

        $this->assertEquals(['id', 'category', 'parent', 'name', 'questiontext', 'questiontextformat',
                'generalfeedback', 'generalfeedbackformat', 'defaultmark', 'penalty', 'qtype',
                'length', 'stamp', 'version', 'hidden', 'timecreated', 'timemodified',
                'createdby', 'modifiedby', 'idnumber', 'contextid', 'options', 'hints', 'categoryobject'],
                array_keys(get_object_vars($questiondata)));
        $this->assertEquals($category->id, $questiondata->category);
        $this->assertEquals(0, $questiondata->parent);
        $this->assertEquals($fromform->name, $questiondata->name);
        $this->assertEquals($fromform->questiontext, $questiondata->questiontext);
        $this->assertEquals($fromform->questiontextformat, $questiondata->questiontextformat);
        $this->assertEquals($fromform->generalfeedback['text'], $questiondata->generalfeedback);
        $this->assertEquals($fromform->generalfeedback['format'], $questiondata->generalfeedbackformat);
        $this->assertEquals($fromform->defaultmark, $questiondata->defaultmark);
        $this->assertEquals(0, $questiondata->penalty);
        $this->assertEquals('multianswer', $questiondata->qtype);
        $this->assertEquals(1, $questiondata->length);
        $this->assertEquals(0, $questiondata->hidden);
        $this->assertEquals($question->createdby, $questiondata->createdby);
        $this->assertEquals($question->createdby, $questiondata->modifiedby);
        $this->assertEquals('', $questiondata->idnumber);
        $this->assertEquals($syscontext->id, $questiondata->contextid);

        // Build the expected hint base.
        $hintbase = [
            'questionid' => $questiondata->id,
            'shownumcorrect' => 0,
            'clearwrong' => 0,
            'options' => null];
        $expectedhints = [];
        foreach ($fromform->hint as $key => $value) {
            $hint = $hintbase + [
                'hint' => $value['text'],
                'hintformat' => $value['format'],
            ];
            $expectedhints[] = (object)$hint;
        }
        // Need to get rid of ids.
        $gothints = array_map(function($hint) {
            unset($hint->id);
            return $hint;
        }, $questiondata->hints);
        // Compare hints.
        $this->assertEquals($expectedhints, array_values($gothints));

        // Options.
        $this->assertEquals(['answers', 'questions'], array_keys(get_object_vars($questiondata->options)));
        $this->assertEquals(count($fromform->options->questions), count($questiondata->options->questions));

        // Option answers.
        $this->assertEquals([], $questiondata->options->answers);

        // Build the expected questions. We aren't going deeper to subquestion answers, options... that's another qtype job.
        $expectedquestions = [];
        foreach ($fromform->options->questions as $key => $value) {
            $question = [
                'id' => $value->id,
                'category' => $category->id,
                'parent' => $questiondata->id,
                'name' => $value->name,
                'questiontext' => $value->questiontext,
                'questiontextformat' => $value->questiontextformat,
                'generalfeedback' => $value->generalfeedback,
                'generalfeedbackformat' => $value->generalfeedbackformat,
                'defaultmark' => (float) $value->defaultmark,
                'penalty' => (float)$value->penalty,
                'qtype' => $value->qtype,
                'length' => $value->length,
                'stamp' => $value->stamp,
                'hidden' => 0,
                'timecreated' => $value->timecreated,
                'timemodified' => $value->timemodified,
                'createdby' => $value->createdby,
                'modifiedby' => $value->modifiedby,
            ];
            $expectedquestions[] = (object)$question;
        }
        // Need to get rid of (version, idnumber, options, hints, maxmark). They are missing @ fromform.
        $gotquestions = array_map(function($question) {
                unset($question->version);
                unset($question->idnumber);
                unset($question->options);
                unset($question->hints);
                unset($question->maxmark);
                return $question;
        }, $questiondata->options->questions);
        // Compare questions.
        $this->assertEquals($expectedquestions, array_values($gotquestions));
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
                $this->assertEquals($value, $actualquestiondata->$property);
            }
        }

        foreach ($questiondata->options as $optionname => $value) {
            if ($optionname != 'questions') {
                $this->assertEquals($value, $actualquestiondata->options->$optionname);
            }
        }

        foreach ($questiondata->hints as $hint) {
            $actualhint = array_shift($actualquestiondata->hints);
            foreach ($hint as $property => $value) {
                if (!in_array($property, array('id', 'questionid', 'options'))) {
                    $this->assertEquals($value, $actualhint->$property);
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
                    $this->assertEquals($subqvalue, $actualsubq->$subqproperty);
                }
            }
            foreach ($subq->options as $optionname => $value) {
                if (!in_array($optionname, array('answers'))) {
                    $this->assertEquals($value, $actualsubq->options->$optionname);
                }
            }
            foreach ($subq->options->answers as $answer) {
                $actualanswer = array_shift($actualsubq->options->answers);
                foreach ($answer as $ansproperty => $ansvalue) {
                    // These questions do not use 'answerformat', will ignore it.
                    if (!in_array($ansproperty, array('id', 'question', 'answerformat'))) {
                        $this->assertEquals($ansvalue, $actualanswer->$ansproperty);
                    }
                }
            }
        }
    }

    /**
     *  Verify that the multiplechoice variants parameters are correctly interpreted from
     *  the question text
     */
    public function test_questiontext_extraction_of_multiplechoice_subquestions_variants() {
        $questiontext = array();
        $questiontext['format'] = FORMAT_HTML;
        $questiontext['itemid'] = '';
        $questiontext['text'] = '<p>Match the following cities with the correct state:</p>
            <ul>
            <li>1 San Francisco:{1:MULTICHOICE:=California#OK~%33.33333%Ohio#Not really~Arizona#Wrong}</li>
            <li>2 Tucson:{1:MC:%0%California#Wrong~%33,33333%Ohio#Not really~=Arizona#OK}</li>
            <li>3 Los Angeles:{1:MULTICHOICE_S:=California#OK~%33.33333%Ohio#Not really~Arizona#Wrong}</li>
            <li>4 Phoenix:{1:MCS:%0%California#Wrong~%33,33333%Ohio#Not really~=Arizona#OK}</li>
            <li>5 San Francisco:{1:MULTICHOICE_H:=California#OK~%33.33333%Ohio#Not really~Arizona#Wrong}</li>
            <li>6 Tucson:{1:MCH:%0%California#Wrong~%33,33333%Ohio#Not really~=Arizona#OK}</li>
            <li>7 Los Angeles:{1:MULTICHOICE_HS:=California#OK~%33.33333%Ohio#Not really~Arizona#Wrong}</li>
            <li>8 Phoenix:{1:MCHS:%0%California#Wrong~%33,33333%Ohio#Not really~=Arizona#OK}</li>
            <li>9 San Francisco:{1:MULTICHOICE_V:=California#OK~%33.33333%Ohio#Not really~Arizona#Wrong}</li>
            <li>10 Tucson:{1:MCV:%0%California#Wrong~%33,33333%Ohio#Not really~=Arizona#OK}</li>
            <li>11 Los Angeles:{1:MULTICHOICE_VS:=California#OK~%33.33333%Ohio#Not really~Arizona#Wrong}</li>
            <li>12 Phoenix:{1:MCVS:%0%California#Wrong~%33,33333%Ohio#Not really~=Arizona#OK}</li>
            </ul>';

        $q = qtype_multianswer_extract_question($questiontext);
        foreach ($q->options->questions as $key => $sub) {
            $this->assertSame($sub->qtype, 'multichoice');
            if ($key == 1 || $key == 2 || $key == 5 || $key == 6 || $key == 9 || $key == 10) {
                $this->assertSame($sub->shuffleanswers, 0);
            } else {
                $this->assertSame($sub->shuffleanswers, 1);
            }
            if ($key == 1 || $key == 2 || $key == 3 || $key == 4) {
                $this->assertSame($sub->layout, qtype_multichoice_base::LAYOUT_DROPDOWN);
            } else if ($key == 5 || $key == 6 || $key == 7 || $key == 8) {
                $this->assertSame($sub->layout, qtype_multichoice_base::LAYOUT_HORIZONTAL);
            } else if ($key == 9 || $key == 10 || $key == 11 || $key == 12) {
                $this->assertSame($sub->layout, qtype_multichoice_base::LAYOUT_VERTICAL);
            }
            foreach ($sub->feedback as $key => $feedback) {
                if ($feedback['text'] === 'OK') {
                    $this->assertEquals(1, $sub->fraction[$key]);
                } else if ($feedback['text'] === 'Wrong') {
                    $this->assertEquals(0, $sub->fraction[$key]);
                } else {
                    $this->assertEquals('Not really', $feedback['text']);
                    $this->assertEquals(0.3333333, $sub->fraction[$key]);
                }
            }
        }
    }
}
