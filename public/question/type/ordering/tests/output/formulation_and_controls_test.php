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
use question_display_options;
use test_question_maker;
use qtype_ordering_question;
use qtype_ordering_test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * A test class used to test formulation_and_controls.
 *
 * @package   qtype_ordering
 * @copyright 2023 Ilya Tregubov <ilya.a.tregubov@gmail.com.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \qtype_ordering\output\renderable_base
 * @covers    \qtype_ordering\output\formulation_and_controls
 * @covers    \qtype_ordering_renderer::feedback_image
 */
final class formulation_and_controls_test extends advanced_testcase {
    /**
     * Test the exported data for the template that renders the formulation and controls for a given question.
     *
     * @dataProvider export_for_template_provider
     * @param array $answeritems The array of ordered answers.
     * @param int $gradingtype Grading type.
     * @param string $layouttype The type of the layout.
     * @param array $expected The expected exported data.
     * @return void
     */
    public function test_export_for_template(array $answeritems, int $gradingtype, string $layouttype, array $expected): void {
        global $PAGE;

        $question = test_question_maker::make_question('ordering');
        $question->layouttype = $layouttype === 'horizontal' ? qtype_ordering_question::LAYOUT_HORIZONTAL :
            qtype_ordering_question::LAYOUT_VERTICAL;
        $qa = new \testable_question_attempt($question, 0);
        $step = new \question_attempt_step();
        $qa->add_step($step);
        $question->start_attempt($step, 1);

        $options = new question_display_options();
        $options->feedback = question_display_options::VISIBLE;
        $options->numpartscorrect = question_display_options::VISIBLE;
        $options->generalfeedback = question_display_options::VISIBLE;
        $options->rightanswer = question_display_options::VISIBLE;
        $options->manualcomment = question_display_options::VISIBLE;
        $options->history = question_display_options::VISIBLE;
        $question->gradingtype = $gradingtype;

        $keys = implode(',', array_keys($answeritems));
        $values = array_values($answeritems);

        $step->set_qt_var('_currentresponse', $keys);

        [$fraction, $state] = $question->grade_response(qtype_ordering_test_helper::get_response($question, $values));
        // Force the state to be complete if it is graded correct for testing purposes.
        if ($state === \question_state::$gradedright) {
            $state = \question_state::$complete;
        }
        $qa->get_last_step()->set_state($state);

        $renderer = $PAGE->get_renderer('core');
        $formulationandcontrols = new formulation_and_controls($qa, $options);
        $actual = $formulationandcontrols->export_for_template($renderer);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for the test_export_for_template test.
     *
     * @return array
     */
    public static function export_for_template_provider(): array {
        global $CFG, $OUTPUT;
        require_once($CFG->dirroot . '/question/type/ordering/question.php');

        $correct = $OUTPUT->pix_icon('i/grade_correct', get_string('correct', 'question'));
        $partiallycorrect = $OUTPUT->pix_icon('i/grade_partiallycorrect', get_string('partiallycorrect', 'question'));
        $incorrect = $OUTPUT->pix_icon('i/grade_incorrect', get_string('incorrect', 'question'));

        return [
            'Horizontal, correct and partially correct' => [
                [13 => 'Modular', 14 => 'Object', 15 => 'Oriented', 17 => 'Learning', 16 => 'Dynamic', 18 => 'Environment'],
                qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT,
                'horizontal',
                [
                    'readonly' => false,
                    'questiontext' => '<div class="clearfix">Put these words in order</div>',
                    'responsename' => 'q0:_response_0',
                    'responseid' => 'id_q0_response_0',
                    'value' => 'ordering_item_ac5fc041de63c8c5b34d0aabb96cf33d,' .
                        'ordering_item_497031794414a552435f90151ac3b54b,' .
                        'ordering_item_5a35edab0f2bf86dfa3901baa8c235dc,' .
                        'ordering_item_8af0f5c3edad8d8e158ff27b9f03afac,' .
                        'ordering_item_971fd8cc345d8bd9f92e9f7d88fdf20c,' .
                        'ordering_item_0ba29c6a1afacf586b03a26162c72274',
                    'ablockid' => 'id_ablock_0',
                    'layoutclass' => 'horizontal',
                    'numberingstyle' => 'none',
                    'horizontallayout' => true,
                    'active' => false,
                    'sortableid' => 'id_sortable_0',
                    'answers' => [
                        [
                            'scoreclass' => 'correct',
                            'id' => 'ordering_item_' . md5('Modular'),
                            'answertext' => "Modular",
                            'feedbackimage' => $correct,
                        ],
                        [
                            'scoreclass' => 'correct',
                            'id' => 'ordering_item_' . md5('Object'),
                            'answertext' => "Object",
                            'feedbackimage' => $correct,
                        ],
                        [
                            'scoreclass' => 'correct',
                            'id' => 'ordering_item_' . md5('Oriented'),
                            'answertext' => "Oriented",
                            'feedbackimage' => $correct,
                        ],
                        [
                            'scoreclass' => 'partial66',
                            'id' => 'ordering_item_' . md5('Learning'),
                            'answertext' => "Learning",
                            'feedbackimage' => $partiallycorrect,
                        ],
                        [
                            'scoreclass' => 'partial66',
                            'id' => 'ordering_item_' . md5('Dynamic'),
                            'answertext' => "Dynamic",
                            'feedbackimage' => $partiallycorrect,
                        ],
                        [
                            'scoreclass' => 'correct',
                            'id' => 'ordering_item_' . md5('Environment'),
                            'answertext' => "Environment",
                            'feedbackimage' => $correct,
                        ],
                    ],
                ],
            ],
            'Vertical, incorrect' => [
                [14 => 'Object', 16 => 'Dynamic', 13 => 'Modular', 17 => 'Learning', 18 => 'Environment', 15 => 'Oriented'],
                qtype_ordering_question::GRADING_ABSOLUTE_POSITION,
                'vertical',
                [
                    'readonly' => false,
                    'questiontext' => '<div class="clearfix">Put these words in order</div>',
                    'responsename' => 'q0:_response_0',
                    'responseid' => 'id_q0_response_0',
                    'value' => 'ordering_item_497031794414a552435f90151ac3b54b,' .
                        'ordering_item_971fd8cc345d8bd9f92e9f7d88fdf20c,' .
                        'ordering_item_ac5fc041de63c8c5b34d0aabb96cf33d,' .
                        'ordering_item_8af0f5c3edad8d8e158ff27b9f03afac,' .
                        'ordering_item_0ba29c6a1afacf586b03a26162c72274,' .
                        'ordering_item_5a35edab0f2bf86dfa3901baa8c235dc',
                    'ablockid' => 'id_ablock_0',
                    'layoutclass' => 'vertical',
                    'numberingstyle' => 'none',
                    'horizontallayout' => false,
                    'active' => false,
                    'sortableid' => 'id_sortable_0',
                    'answers' => [
                        [
                            'scoreclass' => 'incorrect',
                            'id' => 'ordering_item_' . md5('Object'),
                            'answertext' => "Object",
                            'feedbackimage' => $incorrect,
                        ],
                        [
                            'scoreclass' => 'incorrect',
                            'id' => 'ordering_item_' . md5('Dynamic'),
                            'answertext' => "Dynamic",
                            'feedbackimage' => $incorrect,
                        ],
                        [
                            'scoreclass' => 'incorrect',
                            'id' => 'ordering_item_' . md5('Modular'),
                            'answertext' => "Modular",
                            'feedbackimage' => $incorrect,
                        ],
                        [
                            'scoreclass' => 'incorrect',
                            'id' => 'ordering_item_' . md5('Learning'),
                            'answertext' => "Learning",
                            'feedbackimage' => $incorrect,
                        ],
                        [
                            'scoreclass' => 'incorrect',
                            'id' => 'ordering_item_' . md5('Environment'),
                            'answertext' => "Environment",
                            'feedbackimage' => $incorrect,
                        ],
                        [
                            'scoreclass' => 'incorrect',
                            'id' => 'ordering_item_' . md5('Oriented'),
                            'answertext' => "Oriented",
                            'feedbackimage' => $incorrect,
                        ],
                    ],
                ],
            ],
            'Horizontal, correct and complete' => [
                [13 => 'Modular', 14 => 'Object', 15 => 'Oriented', 16 => 'Dynamic', 17 => 'Learning', 18 => 'Environment'],
                qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT,
                'horizontal',
                [
                    'readonly' => false,
                    'questiontext' => '<div class="clearfix">Put these words in order</div>',
                    'responsename' => 'q0:_response_0',
                    'responseid' => 'id_q0_response_0',
                    'value' => 'ordering_item_ac5fc041de63c8c5b34d0aabb96cf33d,' .
                        'ordering_item_497031794414a552435f90151ac3b54b,' .
                        'ordering_item_5a35edab0f2bf86dfa3901baa8c235dc,' .
                        'ordering_item_971fd8cc345d8bd9f92e9f7d88fdf20c,' .
                        'ordering_item_8af0f5c3edad8d8e158ff27b9f03afac,' .
                        'ordering_item_0ba29c6a1afacf586b03a26162c72274',
                    'ablockid' => 'id_ablock_0',
                    'layoutclass' => 'horizontal',
                    'numberingstyle' => 'none',
                    'horizontallayout' => true,
                    'active' => true,
                    'sortableid' => 'id_sortable_0',
                    'answers' => [
                        [
                            'scoreclass' => 'correct',
                            'id' => 'ordering_item_' . md5('Modular'),
                            'answertext' => "Modular",
                            'feedbackimage' => $correct,
                        ],
                        [
                            'scoreclass' => 'correct',
                            'id' => 'ordering_item_' . md5('Object'),
                            'answertext' => "Object",
                            'feedbackimage' => $correct,
                        ],
                        [
                            'scoreclass' => 'correct',
                            'id' => 'ordering_item_' . md5('Oriented'),
                            'answertext' => "Oriented",
                            'feedbackimage' => $correct,
                        ],
                        [
                            'scoreclass' => 'correct',
                            'id' => 'ordering_item_' . md5('Dynamic'),
                            'answertext' => "Dynamic",
                            'feedbackimage' => $correct,
                        ],
                        [
                            'scoreclass' => 'correct',
                            'id' => 'ordering_item_' . md5('Learning'),
                            'answertext' => "Learning",
                            'feedbackimage' => $correct,
                        ],
                        [
                            'scoreclass' => 'correct',
                            'id' => 'ordering_item_' . md5('Environment'),
                            'answertext' => "Environment",
                            'feedbackimage' => $correct,
                        ],
                    ],
                ],
            ],
        ];
    }
}
