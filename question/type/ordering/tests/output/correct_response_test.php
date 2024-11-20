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
 * A test class used to test correct_response.
 *
 * @package   qtype_ordering
 * @copyright 2023 Mihail Geshoski <mihail@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \qtype_ordering\output\renderable_base
 * @covers    \qtype_ordering\output\correct_response
 */
final class correct_response_test extends advanced_testcase {
    /**
     * Test the exported data for the template that renders the correct response to a given question attempt.
     *
     * @dataProvider export_for_template_provider
     * @param array $currentresponse The array of items representing the current response.
     * @param string $layouttype The type of the layout.
     * @param array $expected The expected exported data.
     * @return void
     */
    public function test_export_for_template(array $currentresponse, string $layouttype, array $expected): void {
        global $PAGE;

        $question = test_question_maker::make_question('ordering');
        // Set the grading type and layout type options.
        $question->gradingtype = qtype_ordering_question::GRADING_ABSOLUTE_POSITION;
        $question->layouttype = $layouttype === 'horizontal' ? qtype_ordering_question::LAYOUT_HORIZONTAL :
            qtype_ordering_question::LAYOUT_VERTICAL;

        // Create a question attempt.
        $qa = new \testable_question_attempt($question, 0);
        // Create a question attempt step and add it to the question attempt.
        $step = new \question_attempt_step();
        $qa->add_step($step);
        $question->start_attempt($qa->get_last_step(), 1);
        // Get the grading state based on the correct response and the current response, and later set it in the question
        // attempt step.
        [$fraction, $state] = $question->grade_response(qtype_ordering_test_helper::get_response($question, $currentresponse));
        $qa->get_last_step()->set_state($state);
        if ($expected['hascorrectresponse'] === false) {
            $qa->get_question()->correctresponse = 0;
        }

        $renderer = $PAGE->get_renderer('core');
        $correctresponse = new correct_response($qa);
        // Validate the exported data for the template.
        $this->assertEquals($expected, $correctresponse->export_for_template($renderer));
    }

    /**
     * Data provider for the test_export_for_template test.
     *
     * @return array
     */
    public static function export_for_template_provider(): array {

        return [
            'Correct question attempt.' => [
                ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment'],
                'horizontal',
                [
                    'hascorrectresponse' => true,
                    'showcorrect' => false,
                ],
            ],
            'Partially correct question attempt (horizontal layout).' => [
                ['Modular', 'Object', 'Dynamic', 'Learning', 'Oriented', 'Environment'],
                'horizontal',
                [
                    'hascorrectresponse' => true,
                    'showcorrect' => true,
                    'orderinglayoutclass' => 'horizontal',
                    'correctanswers' => [
                        ['answertext' => 'Modular'],
                        ['answertext' => 'Object'],
                        ['answertext' => 'Oriented'],
                        ['answertext' => 'Dynamic'],
                        ['answertext' => 'Learning'],
                        ['answertext' => 'Environment'],
                    ],
                ],
            ],
            'Incorrect question attempt (horizontal layout).' => [
                ['Object', 'Dynamic', 'Modular', 'Learning', 'Environment', 'Oriented'],
                'horizontal',
                [
                    'hascorrectresponse' => true,
                    'showcorrect' => true,
                    'orderinglayoutclass' => 'horizontal',
                    'correctanswers' => [
                        ['answertext' => 'Modular'],
                        ['answertext' => 'Object'],
                        ['answertext' => 'Oriented'],
                        ['answertext' => 'Dynamic'],
                        ['answertext' => 'Learning'],
                        ['answertext' => 'Environment'],
                    ],
                ],
            ],
            'Incorrect question attempt (vertical layout).' => [
                ['Object', 'Dynamic', 'Modular', 'Learning', 'Environment', 'Oriented'],
                'vertical',
                [
                    'hascorrectresponse' => true,
                    'showcorrect' => true,
                    'orderinglayoutclass' => 'vertical',
                    'correctanswers' => [
                        ['answertext' => 'Modular'],
                        ['answertext' => 'Object'],
                        ['answertext' => 'Oriented'],
                        ['answertext' => 'Dynamic'],
                        ['answertext' => 'Learning'],
                        ['answertext' => 'Environment'],
                    ],
                ],
            ],
            'Correct state not set somehow' => [
                ['Modular', 'Object', 'Oriented', 'Dynamic', 'Learning', 'Environment'],
                'horizontal',
                [
                    'hascorrectresponse' => false,
                ],
            ],
        ];
    }
}
