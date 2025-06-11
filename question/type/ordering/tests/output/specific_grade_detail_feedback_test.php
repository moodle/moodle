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
use qtype_ordering_test_helper;

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
 * @covers    \qtype_ordering\output\specific_grade_detail_feedback
 */
final class specific_grade_detail_feedback_test extends advanced_testcase {
    /**
     * Test the exported data for the template that renders the specific grade detail feedback test to a given question attempt.
     *
     * @dataProvider export_for_template_provider
     * @param array $answeritems The array of ordered answers.
     * @param int $gradingtype Grading type.
     * @param string $layouttype The type of the layout.
     * @param array $expected The expected exported data.
     * @param int $selecttype The type of the select.
     * @return void
     */
    public function test_export_for_template(array $answeritems, int $gradingtype, string $layouttype, array $expected,
            int $selecttype): void {
        global $PAGE;
        $this->resetAfterTest();
        $question = test_question_maker::make_question('ordering');
        // Options need to be set before starting the attempt otherwise they are not passed along.
        $question->layouttype = $layouttype === 'horizontal' ? qtype_ordering_question::LAYOUT_HORIZONTAL :
            qtype_ordering_question::LAYOUT_VERTICAL;
        $question->gradingtype = $gradingtype;
        $question->selecttype = $selecttype;
        $qa = new \testable_question_attempt($question, 0);
        $step = new \question_attempt_step();
        $qa->add_step($step);
        $question->start_attempt($step, 1);

        $keys = implode(',', array_keys($answeritems));
        $values = array_values($answeritems);

        $step->set_qt_var('_currentresponse', $keys);

        list($fraction, $state) = $question->grade_response(qtype_ordering_test_helper::get_response($question, $values));
        $qa->get_last_step()->set_state($state);

        $renderer = $PAGE->get_renderer('core');
        $specificgradedetailfeedback = new specific_grade_detail_feedback($qa);
        $actual = $specificgradedetailfeedback->export_for_template($renderer);

        if ($selecttype === qtype_ordering_question::SELECT_ALL) {
            $this->assertEquals($expected, $actual);
        } else {
            // Since the order of the scores is random or contiguous, we need to check the score details separately.
            $this->assertEquals($expected['showpartialwrong'], $actual['showpartialwrong']);
            $this->assertEquals($expected['gradingtype'], $actual['gradingtype']);
            $this->assertEquals($expected['orderinglayoutclass'], $actual['orderinglayoutclass']);
            // All or nothing grading type does not have score details.
            if ($gradingtype !== qtype_ordering_question::GRADING_ALL_OR_NOTHING) {
                $this->assertEquals($expected['totalmaxscore'], $actual['totalmaxscore']);
                $this->assertArrayHasKey('scoredetails', $actual);
            }
        }
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
            'Do not show partial or wrong' => [
                [13 => 'Modular', 14 => 'Object', 15 => 'Oriented', 16 => 'Dynamic', 17 => 'Learning', 18 => 'Environment'],
                qtype_ordering_question::GRADING_RELATIVE_NEXT_EXCLUDE_LAST,
                'horizontal',
                [
                    'showpartialwrong' => false,
                ],
                qtype_ordering_question::SELECT_ALL,
            ],
            'Partially correct question attempt (horizontal layout). Relative to ALL the previous and next items' => [
                [13 => 'Modular', 14 => 'Object', 15 => 'Oriented', 17 => 'Learning', 16 => 'Dynamic', 18 => 'Environment'],
                qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT,
                'horizontal',
                [
                    'showpartialwrong' => 1,
                    'gradingtype' => 'Grading type: Relative to ALL the previous and next items',
                    'orderinglayoutclass' => 'horizontal',
                    'gradedetails' => 93.0,
                    'totalscore' => 28,
                    'totalmaxscore' => 30,
                    'scoredetails' => [
                        ['score' => 5, 'maxscore' => 5, 'percent' => 100],
                        ['score' => 5, 'maxscore' => 5, 'percent' => 100],
                        ['score' => 5, 'maxscore' => 5, 'percent' => 100],
                        ['score' => 4, 'maxscore' => 5, 'percent' => 80],
                        ['score' => 4, 'maxscore' => 5, 'percent' => 80],
                        ['score' => 5, 'maxscore' => 5, 'percent' => 100],
                    ],
                ],
                qtype_ordering_question::SELECT_ALL,
            ],
            'Incorrect question attempt (horizontal layout). Relative to ALL the previous and next items' => [
                [14 => 'Object', 16 => 'Dynamic', 13 => 'Modular', 17 => 'Learning', 18 => 'Environment', 15 => 'Oriented'],
                qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT,
                'horizontal',
                [
                    'showpartialwrong' => 1,
                    'gradingtype' => 'Grading type: Relative to ALL the previous and next items',
                    'orderinglayoutclass' => 'horizontal',
                    'gradedetails' => 67.0,
                    'totalscore' => 20,
                    'totalmaxscore' => 30,
                    'scoredetails' => [
                        ['score' => 4, 'maxscore' => 5, 'percent' => 80],
                        ['score' => 3, 'maxscore' => 5, 'percent' => 60],
                        ['score' => 3, 'maxscore' => 5, 'percent' => 60],
                        ['score' => 4, 'maxscore' => 5, 'percent' => 80],
                        ['score' => 4, 'maxscore' => 5, 'percent' => 80],
                        ['score' => 2, 'maxscore' => 5, 'percent' => 40],
                    ],
                ],
                qtype_ordering_question::SELECT_ALL,
            ],
            'Incorrect question attempt (vertical layout). Grading type: Relative to the next item (excluding last)' => [
                [14 => 'Object', 16 => 'Dynamic', 13 => 'Modular', 17 => 'Learning', 18 => 'Environment', 15 => 'Oriented'],
                qtype_ordering_question::GRADING_RELATIVE_NEXT_EXCLUDE_LAST,
                'vertical',
                [
                    'showpartialwrong' => 1,
                    'gradingtype' => 'Grading type: Relative to the next item (excluding last)',
                    'orderinglayoutclass' => 'vertical',
                    'gradedetails' => 20.0,
                    'totalscore' => 1,
                    'totalmaxscore' => 5,
                    'scoredetails' => [
                        ['score' => 0, 'maxscore' => 1, 'percent' => 0.0],
                        ['score' => 0, 'maxscore' => 1, 'percent' => 0.0],
                        ['score' => 0, 'maxscore' => 1, 'percent' => 0.0],
                        ['score' => 1, 'maxscore' => 1, 'percent' => 100.0],
                        ['score' => 'No score', 'maxscore' => null, 'percent' => 0],
                        ['score' => 0, 'maxscore' => 1, 'percent' => 0.0],
                    ],
                ],
                qtype_ordering_question::SELECT_ALL,
            ],
            'Incorrect question attempt (vertical layout). Grading type: GRADING_LONGEST_ORDERED_SUBSET' => [
                [14 => 'Object', 16 => 'Dynamic', 13 => 'Modular', 17 => 'Learning', 18 => 'Environment', 15 => 'Oriented'],
                qtype_ordering_question::GRADING_LONGEST_ORDERED_SUBSET,
                'vertical',
                [
                    'showpartialwrong' => 1,
                    'gradingtype' => 'Grading type: Longest ordered subset',
                    'orderinglayoutclass' => 'vertical',
                    'gradedetails' => 67.0,
                    'totalscore' => 4,
                    'totalmaxscore' => 6,
                    'scoredetails' => [
                        ['score' => 1, 'maxscore' => 1, 'percent' => 100.0],
                        ['score' => 1, 'maxscore' => 1, 'percent' => 100.0],
                        ['score' => 0, 'maxscore' => 1, 'percent' => 0.0],
                        ['score' => 1, 'maxscore' => 1, 'percent' => 100.0],
                        ['score' => 1, 'maxscore' => 1, 'percent' => 100.0],
                        ['score' => 0, 'maxscore' => 1, 'percent' => 0.0],
                    ],
                ],
                qtype_ordering_question::SELECT_ALL,
            ],
            'Incorrect question attempt (SELECT_RANDOM). Grading type: GRADING_ABSOLUTE_POSITION' => [
                [16 => 'Dynamic', 14 => 'Object', 13 => 'Modular', 17 => 'Learning', 18 => 'Environment', 15 => 'Oriented'],
                qtype_ordering_question::GRADING_ABSOLUTE_POSITION,
                'vertical',
                [
                    'showpartialwrong' => 1,
                    'gradingtype' => 'Grading type: Absolute position',
                    'orderinglayoutclass' => 'vertical',
                    'gradedetails' => 0,
                    'totalscore' => 0,
                    'totalmaxscore' => 2,
                    'scoredetails' => [
                        ['score' => 0, 'maxscore' => 1, 'percent' => 0.0],
                        ['score' => 0, 'maxscore' => 1, 'percent' => 0.0],
                        ['score' => 0, 'maxscore' => 1, 'percent' => 0.0],
                        ['score' => 'No score', 'maxscore' => null, 'percent' => 0],
                        ['score' => 'No score', 'maxscore' => null, 'percent' => 0],
                        ['score' => 'No score', 'maxscore' => null, 'percent' => 0],
                    ],
                ],
                qtype_ordering_question::SELECT_RANDOM,
            ],
            'Incorrect question attempt (SELECT_CONTIGUOUS). Grading type: GRADING_ABSOLUTE_POSITION' => [
                [14 => 'Object', 16 => 'Dynamic', 13 => 'Modular', 17 => 'Learning', 18 => 'Environment', 15 => 'Oriented'],
                qtype_ordering_question::GRADING_ABSOLUTE_POSITION,
                'vertical',
                [
                    'showpartialwrong' => 1,
                    'gradingtype' => 'Grading type: Absolute position',
                    'orderinglayoutclass' => 'vertical',
                    'gradedetails' => 0,
                    'totalscore' => 0,
                    'totalmaxscore' => 2,
                    'scoredetails' => [
                        ['score' => 0, 'maxscore' => 1, 'percent' => 0],
                        ['score' => 1, 'maxscore' => 1, 'percent' => 100.0],
                        ['score' => 0, 'maxscore' => 1, 'percent' => 0],
                        ['score' => 'No score', 'maxscore' => null, 'percent' => 0],
                        ['score' => 'No score', 'maxscore' => null, 'percent' => 0],
                        ['score' => 'No score', 'maxscore' => null, 'percent' => 0],
                    ],
                ],
                qtype_ordering_question::SELECT_CONTIGUOUS,
            ],
            'Incorrect question attempt (SELECT_CONTIGUOUS). Grading type: GRADING_ALL_OR_NOTHING' => [
                [14 => 'Object', 16 => 'Dynamic', 13 => 'Modular', 17 => 'Learning', 18 => 'Environment', 15 => 'Oriented'],
                qtype_ordering_question::GRADING_ALL_OR_NOTHING,
                'vertical',
                [
                    'showpartialwrong' => 1,
                    'gradingtype' => 'Grading type: All or nothing',
                    'orderinglayoutclass' => 'vertical',
                    'gradedetails' => 0,
                    'totalscore' => 0,
                    'totalmaxscore' => 2,
                    'scoredetails' => [
                        ['score' => 0, 'maxscore' => 1, 'percent' => 0],
                        ['score' => 1, 'maxscore' => 1, 'percent' => 100.0],
                        ['score' => 0, 'maxscore' => 1, 'percent' => 0],
                        ['score' => 'No score', 'maxscore' => null, 'percent' => 0],
                        ['score' => 'No score', 'maxscore' => null, 'percent' => 0],
                        ['score' => 'No score', 'maxscore' => null, 'percent' => 0],
                    ],
                ],
                qtype_ordering_question::SELECT_CONTIGUOUS,
            ],
        ];
    }
}
