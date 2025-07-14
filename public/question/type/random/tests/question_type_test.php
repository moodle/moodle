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

namespace qtype_random;

use qtype_random;
use question_bank;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/random/questiontype.php');


/**
 * Unit tests for the random question type class.
 *
 * @package    qtype_random
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class question_type_test extends \advanced_testcase {
    protected $qtype;

    protected function setUp(): void {
        parent::setUp();
        $this->qtype = new qtype_random();
    }

    protected function tearDown(): void {
        $this->qtype = null;
        parent::tearDown();
    }

    public function test_name(): void {
        $this->assertEquals($this->qtype->name(), 'random');
    }

    public function test_can_analyse_responses(): void {
        $this->assertFalse($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score(): void {
        $this->assertNull($this->qtype->get_random_guess_score(null));
    }

    public function test_load_question(): void {
        $this->resetAfterTest();

        $syscontext = \context_system::instance();
        /** @var core_question_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category(['contextid' => $syscontext->id]);

        $fromform = \test_question_maker::get_question_form_data('random');
        $fromform->category = $category->id . ',' . $syscontext->id;

        $question = new \stdClass();
        $question->category = $category->id;
        $question->qtype = 'random';
        $question->createdby = 0;

        $this->qtype->save_question($question, $fromform);
        $questiondata = question_bank::load_question_data($question->id);

        $this->assertEquals(['id', 'category', 'parent', 'name', 'questiontext', 'questiontextformat',
                'generalfeedback', 'generalfeedbackformat', 'defaultmark', 'penalty', 'qtype',
                'length', 'stamp', 'timecreated', 'timemodified', 'createdby', 'modifiedby', 'idnumber', 'contextid',
                'status', 'versionid', 'version', 'questionbankentryid', 'categoryobject', 'options', 'hints'],
                array_keys(get_object_vars($questiondata)));
        $this->assertEquals($category->id, $questiondata->category);

        // Random questions are not real questions. This is signaled by parent
        // being non-zero - and in fact equal to question id.
        $this->assertEquals($questiondata->id, $questiondata->parent);
        $this->assertEquals('Random (' . $category->name . ')', $questiondata->name);
        $this->assertEquals(0, $questiondata->questiontext); // Used to store 'Select from subcategories'.
        $this->assertEquals('random', $questiondata->qtype);
        $this->assertEquals(1, $questiondata->length);
        $this->assertEquals(\core_question\local\bank\question_version_status::QUESTION_STATUS_READY, $questiondata->status);
        $this->assertEquals($category->contextid, $questiondata->contextid);

        // Options - not used.
        $this->assertEquals(['answers'], array_keys(get_object_vars($questiondata->options)));
        $this->assertEquals([], $questiondata->options->answers);

        // Hints - not used.
        $this->assertEquals([], $questiondata->hints);
    }

    public function test_get_possible_responses(): void {
        $this->assertEquals(array(), $this->qtype->get_possible_responses(null));
    }

    public function test_question_creation(): void {
        $this->resetAfterTest();
        question_bank::get_qtype('random')->clear_caches_before_testing();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('shortanswer', null, array('category' => $cat->id));
        $question2 = $generator->create_question('numerical', null, array('category' => $cat->id));

        $randomquestion = $generator->create_question('random', null, array('category' => $cat->id));

        $expectedids = array($question1->id, $question2->id);
        $actualids = question_bank::get_qtype('random')->get_available_questions_from_category($cat->id, 0);
        sort($expectedids);
        sort($actualids);
        $this->assertEquals($expectedids, $actualids);

        $q = question_bank::load_question($randomquestion->id);

        $this->assertContainsEquals($q->id, array($question1->id, $question2->id));
    }
}
