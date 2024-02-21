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

namespace qtype_ordering\output;

use advanced_testcase;
use test_question_maker;
use qtype_ordering_question;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * A test class used to test specific_grade_detail_feedback.
 *
 * @package   qtype_ordering
 * @copyright 2023 Ilya Tregubov <ilya.a.tregubov@gmail.com.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \qtype_ordering\output\renderable_base
 * @covers    \qtype_ordering\output\num_parts_correct
 */
final class num_parts_correct_test extends advanced_testcase {
    /**
     * Test the exported data for the template that renders the specific grade detail feedback test to a given question attempt.
     *
     * @dataProvider export_for_template_provider
     * @param array $answeritems The array of ordered answers.
     * @param int $gradingtype Grading type.
     * @param array $expected The expected exported data.
     * @return void
     */
    public function test_export_for_template(array $answeritems, int $gradingtype, array $expected): void {
        global $PAGE;

        $question = test_question_maker::make_question('ordering');
        $qa = new \testable_question_attempt($question, 0);
        $step = new \question_attempt_step();
        $qa->add_step($step);
        $question->start_attempt($step, 1);
        $question->gradingtype = $gradingtype;

        $keys = implode(',', array_keys($answeritems));
        $step->set_qt_var('_currentresponse', $keys);

        $renderer = $PAGE->get_renderer('core');
        $specificgradedetailfeedback = new num_parts_correct($qa);
        $actual = $specificgradedetailfeedback->export_for_template($renderer);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for the test_export_for_template test.
     *
     * @return array
     */
    public static function export_for_template_provider(): array {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/ordering/question.php');

        return [
            'Partially correct question attempt. Absolute position' => [
                [13 => 'Modular', 14 => 'Object', 15 => 'Oriented', 17 => 'Learning', 16 => 'Dynamic', 18 => 'Environment'],
                qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT,
                [
                    'numcorrect' => 4,
                    'numpartial' => 2,
                    'numincorrect' => 0,
                ],
            ],
            'Incorrect question attempt (horizontal layout). Relative to ALL the previous and next items' => [
                [14 => 'Object', 16 => 'Dynamic', 13 => 'Modular', 17 => 'Learning', 18 => 'Environment', 15 => 'Oriented'],
                qtype_ordering_question::GRADING_ABSOLUTE_POSITION,
                [
                    'numcorrect' => 0,
                    'numpartial' => 0,
                    'numincorrect' => 6,
                ],
            ],
        ];
    }
}
