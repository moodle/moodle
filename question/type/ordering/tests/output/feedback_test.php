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

use qbehaviour_walkthrough_test_base;
use qtype_ordering\question_hint_ordering;
use test_question_maker;
use qtype_ordering_question;
use qtype_ordering_test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Test the feedback exporter.
 *
 * @package   qtype_ordering
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \qtype_ordering\output\renderable_base
 * @covers    \qtype_ordering\output\feedback
 */
final class feedback_test extends qbehaviour_walkthrough_test_base {
    /** @var array The correct answers for the question, added to quickly reference. */
    const CORRECTANSWERS = [
        0 => [
            'answertext' => 'Modular',
        ],
        1 => [
            'answertext' => 'Object',
        ],
        2 => [
            'answertext' => 'Oriented',
        ],
        3 => [
            'answertext' => 'Dynamic',
        ],
        4 => [
            'answertext' => 'Learning',
        ],
        5 => [
            'answertext' => 'Environment',
        ],
    ];

    /**
     * Test the exported data for the template that renders the feedback test to a given question attempt.
     *
     * @dataProvider export_for_template_provider
     * @param array $answeritems The array of ordered answers.
     * @param int $gradingtype Grading type.
     * @param array $testoptions Do we want to change direction, is it in progress and do we want feedback.
     * @param array $expected The expected exported data.
     * @return void
     */
    public function test_export_for_template(array $answeritems, int $gradingtype, array $testoptions, array $expected): void {
        global $PAGE;

        $question = test_question_maker::make_question('ordering');
        $question->hints = [
            new question_hint_ordering(13, 'This is the first hint.', FORMAT_HTML, true, false, true),
            new question_hint_ordering(14, 'This is the second hint.', FORMAT_HTML, false, false, false),
        ];
        $question->layouttype = $testoptions['rot'] === 'horizontal' ? qtype_ordering_question::LAYOUT_HORIZONTAL :
            qtype_ordering_question::LAYOUT_VERTICAL;

        // If we need to access the attempt midway through, we need a flow where we don't grade instantly.
        if (!$testoptions['inprogress']) {
            $qa = new \testable_question_attempt($question, 0);
            $step = new \question_attempt_step();
            $qa->add_step($step);
            $qa->set_behaviour($question->make_behaviour($qa, 'interactive'));
            $question->gradingtype = $gradingtype;
            $question->start_attempt($step, 1);
            // Process a response and check the expected result.
            $keys = implode(',', array_keys($answeritems));
            $values = array_values($answeritems);
            $step->set_qt_var('_currentresponse', $keys);

            list($fraction, $state) = $question->grade_response(qtype_ordering_test_helper::get_response($question, $values));
            $qa->get_last_step()->set_state($state);
            $attempt = $qa;
        } else {
            $this->start_attempt_at_question($question, 'interactive');
            $response = qtype_ordering_test_helper::get_response($question, array_values($answeritems));
            $this->process_submission(array_merge(['-submit' => 1], $response));
            $attempt = $this->get_question_attempt();
            // Omit the numparts as we are not testing it here, and it can be a bit flaky when manually processing an attempt.
            $this->displayoptions->numpartscorrect = false;
        }
        if (!$testoptions['feedback']) {
            $this->displayoptions->feedback = false;
        }

        $renderer = $PAGE->get_renderer('core');
        $feedbackobj = new feedback($attempt, $this->displayoptions);
        $actual = $feedbackobj->export_for_template($renderer);

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
            'Do not show partial or wrong' => [
                [13 => 'Modular', 14 => 'Object', 15 => 'Oriented', 16 => 'Dynamic', 17 => 'Learning', 18 => 'Environment'],
                qtype_ordering_question::GRADING_RELATIVE_NEXT_EXCLUDE_LAST,
                ['rot' => 'horizontal', 'inprogress' => false, 'feedback' => true],
                [
                    'specificfeedback' => 'Well done!',
                    'numpartscorrect' => [
                        'numcorrect' => 5,
                        'numpartial' => 0,
                        'numincorrect' => 0,
                    ],
                    'specificgradedetailfeedback' => [
                        'showpartialwrong' => 0,
                    ],
                    'generalfeedback' => 'The correct answer is "Modular Object Oriented Dynamic Learning Environment".',
                    'rightanswer' => [
                        'hascorrectresponse' => true,
                        'showcorrect' => false,
                    ],
                ],
            ],
            'Partially correct question attempt (horizontal layout). Relative to ALL the previous and next items' => [
                [13 => 'Modular', 14 => 'Object', 15 => 'Oriented', 17 => 'Learning', 16 => 'Dynamic', 18 => 'Environment'],
                qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT,
                ['rot' => 'horizontal', 'inprogress' => false, 'feedback' => true],
                [
                    'specificfeedback' => 'Parts, but only parts, of your response are correct.',
                    'numpartscorrect' => [
                        'numcorrect' => 4,
                        'numpartial' => 2,
                        'numincorrect' => 0,
                    ],
                    'specificgradedetailfeedback' => [
                        'showpartialwrong' => 1,
                        'gradingtype' => 'Grading type: Relative to ALL the previous and next items',
                        'orderinglayoutclass' => 'horizontal',
                        'scoredetails' => [
                            0 => [
                                'score' => 5,
                                'maxscore' => 5,
                                'percent' => 100.00,
                            ],
                            1 => [
                                'score' => 5,
                                'maxscore' => 5,
                                'percent' => 100.00,
                            ],
                            2 => [
                                'score' => 5,
                                'maxscore' => 5,
                                'percent' => 100.00,
                            ],
                            3 => [
                                'score' => 4,
                                'maxscore' => 5,
                                'percent' => 80.00,
                            ],
                            4 => [
                                'score' => 4,
                                'maxscore' => 5,
                                'percent' => 80.00,
                            ],
                            5 => [
                                'score' => 5,
                                'maxscore' => 5,
                                'percent' => 100.00,
                            ],
                        ],
                        'gradedetails' => 93.0,
                        'totalscore' => 28,
                        'totalmaxscore' => 30,
                    ],
                    'generalfeedback' => 'The correct answer is "Modular Object Oriented Dynamic Learning Environment".',
                    'rightanswer' => [
                        'hascorrectresponse' => true,
                        'showcorrect' => true,
                        'orderinglayoutclass' => 'horizontal',
                        'correctanswers' => self::CORRECTANSWERS,
                    ],
                ],
            ],
            'Partially correct question attempt in progress (horizontal layout). Relative to ALL the previous and next ' .
            'items with hints' => [
                [13 => 'Modular', 14 => 'Object', 15 => 'Oriented', 17 => 'Learning', 16 => 'Dynamic', 18 => 'Environment'],
                qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT,
                ['rot' => 'horizontal', 'inprogress' => true, 'feedback' => true],
                [
                    'specificfeedback' => 'Parts, but only parts, of your response are correct.',
                    'specificgradedetailfeedback' => [
                        'showpartialwrong' => 0,
                    ],
                    'generalfeedback' => 'The correct answer is "Modular Object Oriented Dynamic Learning Environment".',
                    'rightanswer' => [
                        'hascorrectresponse' => true,
                        'showcorrect' => false,
                    ],
                    'hint' => 'This is the first hint.',
                ],
            ],
            'Partially correct question attempt in progress (No feedback). Relative to ALL the previous and next items' => [
                [13 => 'Modular', 14 => 'Object', 15 => 'Oriented', 17 => 'Learning', 16 => 'Dynamic', 18 => 'Environment'],
                qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT,
                ['rot' => 'horizontal', 'inprogress' => false, 'feedback' => false],
                [
                    'generalfeedback' => 'The correct answer is "Modular Object Oriented Dynamic Learning Environment".',
                    'rightanswer' => [
                        'hascorrectresponse' => true,
                        'showcorrect' => true,
                        'orderinglayoutclass' => 'horizontal',
                        'correctanswers' => self::CORRECTANSWERS,
                    ],
                    'numpartscorrect' => [
                        'numcorrect' => 4,
                        'numpartial' => 2,
                        'numincorrect' => 0,
                    ],
                ],
            ],
            'Incorrect question attempt (horizontal layout). Relative to ALL the previous and next items' => [
                [14 => 'Object', 16 => 'Dynamic', 13 => 'Modular', 17 => 'Learning', 18 => 'Environment', 15 => 'Oriented'],
                qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT,
                ['rot' => 'horizontal', 'inprogress' => false, 'feedback' => true],
                [
                    'specificfeedback' => 'Parts, but only parts, of your response are correct.',
                    'numpartscorrect' => [
                        'numcorrect' => 0,
                        'numpartial' => 6,
                        'numincorrect' => 0,
                    ],
                    'specificgradedetailfeedback' => [
                        'showpartialwrong' => 1,
                        'gradingtype' => 'Grading type: Relative to ALL the previous and next items',
                        'orderinglayoutclass' => 'horizontal',
                        'scoredetails' => [
                            0 => [
                                'score' => 4,
                                'maxscore' => 5,
                                'percent' => 80.00,
                            ],
                            1 => [
                                'score' => 3,
                                'maxscore' => 5,
                                'percent' => 60.00,
                            ],
                            2 => [
                                'score' => 3,
                                'maxscore' => 5,
                                'percent' => 60.00,
                            ],
                            3 => [
                                'score' => 4,
                                'maxscore' => 5,
                                'percent' => 80.0,
                            ],
                            4 => [
                                'score' => 4,
                                'maxscore' => 5,
                                'percent' => 80.00,
                            ],
                            5 => [
                                'score' => 2,
                                'maxscore' => 5,
                                'percent' => 40.0,
                            ],
                        ],
                        'gradedetails' => 67.0,
                        'totalscore' => 20,
                        'totalmaxscore' => 30,
                    ],
                    'generalfeedback' => 'The correct answer is "Modular Object Oriented Dynamic Learning Environment".',
                    'rightanswer' => [
                        'hascorrectresponse' => true,
                        'showcorrect' => true,
                        'orderinglayoutclass' => 'horizontal',
                        'correctanswers' => self::CORRECTANSWERS,
                    ],
                ],
            ],
            'Incorrect question attempt (vertical layout). Grading type: Relative to the next item (excluding last)' => [
                [14 => 'Object', 16 => 'Dynamic', 13 => 'Modular', 17 => 'Learning', 18 => 'Environment', 15 => 'Oriented'],
                qtype_ordering_question::GRADING_RELATIVE_NEXT_EXCLUDE_LAST,
                ['rot' => 'vertical', 'inprogress' => false, 'feedback' => true],
                [
                    'specificfeedback' => 'Parts, but only parts, of your response are correct.',
                    'numpartscorrect' => [
                        'numcorrect' => 1,
                        'numpartial' => 0,
                        'numincorrect' => 4,
                    ],
                    'specificgradedetailfeedback' => [
                        'showpartialwrong' => 1,
                        'gradingtype' => 'Grading type: Relative to the next item (excluding last)',
                        'orderinglayoutclass' => 'vertical',
                        'scoredetails' => [
                            0 => [
                                'score' => 0,
                                'maxscore' => 1,
                                'percent' => 0.0,
                            ],
                            1 => [
                                'score' => 0,
                                'maxscore' => 1,
                                'percent' => 0.0,
                            ],
                            2 => [
                                'score' => 0,
                                'maxscore' => 1,
                                'percent' => 0.0,
                            ],
                            3 => [
                                'score' => 1,
                                'maxscore' => 1,
                                'percent' => 100.0,
                            ],
                            4 => [
                                'score' => 'No score',
                                'maxscore' => null,
                                'percent' => 0,
                            ],
                            5 => [
                                'score' => 0,
                                'maxscore' => 1,
                                'percent' => 0.0,
                            ],
                        ],
                        'gradedetails' => 20.0,
                        'totalscore' => 1,
                        'totalmaxscore' => 5,
                    ],
                    'generalfeedback' => 'The correct answer is "Modular Object Oriented Dynamic Learning Environment".',
                    'rightanswer' => [
                        'hascorrectresponse' => true,
                        'showcorrect' => true,
                        'orderinglayoutclass' => 'vertical',
                        'correctanswers' => self::CORRECTANSWERS,
                    ],
                ],
            ],
        ];
    }
}
