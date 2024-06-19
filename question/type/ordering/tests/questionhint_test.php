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

namespace qtype_ordering;

use advanced_testcase;
use qtype_ordering;
use question_bank;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/ordering/questiontype.php');

/**
 * A test class used to test question_hint_ordering.
 *
 * @package   qtype_ordering
 * @copyright 2024 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \qtype_ordering\question_hint_ordering
 * @covers    \qtype_ordering
 */
final class questionhint_test extends advanced_testcase {
    /** @var qtype_ordering Instance of the question type class to test. */
    protected $qtype;

    protected function setUp(): void {
        parent::setUp();
        $this->qtype = new qtype_ordering();
    }

    protected function tearDown(): void {
        $this->qtype = null;
        parent::tearDown();
    }
    /**
     * Test that hints can be fetched from the DB.
     * @return void
     */
    public function test_load_from_record(): void {
        $this->resetAfterTest();

        $obj = (object) [
            'id' => 13,
            'hint' => 'Hint 1',
            'hintformat' => FORMAT_HTML,
            'shownumcorrect' => 0,
            'clearwrong' => 0,
            'options' => null,
        ];
        $hint = question_hint_ordering::load_from_record($obj);
        $this->assertInstanceOf(question_hint_ordering::class, $hint);
    }

    public function test_make_hint(): void {
        $this->resetAfterTest();

        $syscontext = \context_system::instance();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category(['contextid' => $syscontext->id]);

        $fromdata = \test_question_maker::get_question_form_data('ordering');
        $fromdata->category = $category->id . ',' . $syscontext->id;

        $question = new \stdClass();
        $question->category = $category->id;
        $question->qtype = 'ordering';
        $question->createdby = 0;

        $question = $this->qtype->save_question($question, $fromdata);
        $questiondata = question_bank::load_question_data($question->id);

        // Build the expected hint base.
        $hintbase = [
            'questionid' => $questiondata->id,
            'shownumcorrect' => '0',
            'clearwrong' => '0',
            'options' => '0',
        ];

        $expectedhints = [];
        foreach ($fromdata->hint as $key => $value) {
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
    }
}
