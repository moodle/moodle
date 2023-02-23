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
 * This file contains tests for the question_engine class.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question;

use advanced_testcase;
use moodle_exception;
use question_engine;

/**
 * Unit tests for the question_engine class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \question_engine
 */
class question_engine_test extends advanced_testcase {

    /**
     * Load required libraries.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/question/engine/lib.php");
    }

    /**
     * Tests for load_behaviour_class.
     *
     * @covers \question_engine::load_behaviour_class
     */
    public function test_load_behaviour_class(): void {
        // Exercise SUT.
        question_engine::load_behaviour_class('deferredfeedback');

        // Verify.
        $this->assertTrue(class_exists('qbehaviour_deferredfeedback'));
    }

    /**
     * Tests for load_behaviour_class when a class is missing.
     *
     * @covers \question_engine::load_behaviour_class
     */
    public function test_load_behaviour_class_missing(): void {
        // Exercise SUT.
        $this->expectException(moodle_exception::class);
        question_engine::load_behaviour_class('nonexistantbehaviour');
    }

    /**
     * Test the get_behaviour_unused_display_options with various options.
     *
     * @covers \question_engine::get_behaviour_unused_display_options
     * @dataProvider get_behaviour_unused_display_options_provider
     * @param string $behaviour
     * @param array $expected
     */
    public function test_get_behaviour_unused_display_options(string $behaviour, array $expected): void {
        $this->assertEquals($expected, question_engine::get_behaviour_unused_display_options($behaviour));
    }

    /**
     * Data provider for get_behaviour_unused_display_options.
     *
     * @return array
     */
    public function get_behaviour_unused_display_options_provider(): array {
        return [
            'interactive' => [
                'interactive',
                [],
            ],
            'deferredfeedback' => [
                'deferredfeedback',
                ['correctness', 'marks', 'specificfeedback', 'generalfeedback', 'rightanswer'],
            ],
            'deferredcbm' => [
                'deferredcbm',
                ['correctness', 'marks', 'specificfeedback', 'generalfeedback', 'rightanswer'],
            ],
            'manualgraded' => [
                'manualgraded',
                ['correctness', 'marks', 'specificfeedback', 'generalfeedback', 'rightanswer'],
            ],
        ];
    }

    /**
     * Tests for can_questions_finish_during_the_attempt.
     *
     * @covers \question_engine::can_questions_finish_during_the_attempt
     * @dataProvider can_questions_finish_during_the_attempt_provider
     * @param string $behaviour
     * @param bool $expected
     */
    public function test_can_questions_finish_during_the_attempt(string $behaviour, bool $expected): void {
        $this->assertEquals($expected, question_engine::can_questions_finish_during_the_attempt($behaviour));
    }

    /**
     * Data provider for can_questions_finish_during_the_attempt_provider.
     *
     * @return array
     */
    public function can_questions_finish_during_the_attempt_provider(): array {
        return [
            ['deferredfeedback', false],
            ['interactive', true],
        ];
    }

    /**
     * Tests for sort_behaviours
     *
     * @covers \question_engine::sort_behaviours
     * @dataProvider sort_behaviours_provider
     * @param array $input The params passed to sort_behaviours
     * @param array $expected
     */
    public function test_sort_behaviours(array $input, array $expected): void {
        $this->assertSame($expected, question_engine::sort_behaviours(...$input));
    }

    /**
     * Data provider for sort_behaviours.
     *
     * @return array
     */
    public function sort_behaviours_provider(): array {
        $in = [
            'b1' => 'Behave 1',
            'b2' => 'Behave 2',
            'b3' => 'Behave 3',
            'b4' => 'Behave 4',
            'b5' => 'Behave 5',
            'b6' => 'Behave 6',
        ];

        return [
            [
                [$in, '', '', ''],
                $in,
            ],
            [
                [$in, '', 'b4', 'b4'],
                $in,
            ],
            [
                [$in, '', 'b1,b2,b3,b4', 'b4'],
                ['b4' => 'Behave 4', 'b5' => 'Behave 5', 'b6' => 'Behave 6'],
            ],
            [
                [$in, 'b6,b1,b4', 'b2,b3,b4,b5', 'b4'],
                ['b6' => 'Behave 6', 'b1' => 'Behave 1', 'b4' => 'Behave 4'],
            ],
            [
                [$in, 'b6,b5,b4', 'b1,b2,b3', 'b4'],
                ['b6' => 'Behave 6', 'b5' => 'Behave 5', 'b4' => 'Behave 4'],
            ],
            [
                [$in, 'b1,b6,b5', 'b1,b2,b3,b4', 'b4'],
                ['b6' => 'Behave 6', 'b5' => 'Behave 5', 'b4' => 'Behave 4'],
            ],
            [
                [$in, 'b2,b4,b6', 'b1,b3,b5', 'b2'],
                ['b2' => 'Behave 2', 'b4' => 'Behave 4', 'b6' => 'Behave 6'],
            ],
            // Ignore unknown input in the order argument.
            [
                [$in, 'unknown', '', ''],
                $in,
            ],
            // Ignore unknown input in the disabled argument.
            [
                [$in, '', 'unknown', ''],
                $in,
            ],
        ];
    }

    /**
     * Tests for is_manual_grade_in_range.
     *
     * @dataProvider is_manual_grade_in_range_provider
     * @covers \question_engine::is_manual_grade_in_range
     * @param array $post The values to add to $_POST
     * @param array $params The params to pass to is_manual_grade_in_range
     * @param bool $expected
     */
    public function test_is_manual_grade_in_range(array $post, array $params, bool $expected): void {
        $_POST[] = $post;
        $this->assertEquals($expected, question_engine::is_manual_grade_in_range(...$params));
    }

    /**
     * Data provider for is_manual_grade_in_range tests.
     *
     * @return array
     */
    public function is_manual_grade_in_range_provider(): array {
        return [
            'In range' => [
                'post' => [
                    'q1:2_-mark' => 0.5,
                    'q1:2_-maxmark' => 1.0,
                    'q1:2_:minfraction' => 0,
                    'q1:2_:maxfraction' => 1,
                ],
                'range' => [1, 2],
                'expected' => true,
            ],
            'Bottom end' => [
                'post' => [
                    'q1:2_-mark' => -1.0,
                    'q1:2_-maxmark' => 2.0,
                    'q1:2_:minfraction' => -0.5,
                    'q1:2_:maxfraction' => 1,
                ],
                'range' => [1, 2],
                'expected' => true,
            ],
            'Too low' => [
                'post' => [
                    'q1:2_-mark' => -1.1,
                    'q1:2_-maxmark' => 2.0,
                    'q1:2_:minfraction' => -0.5,
                    'q1:2_:maxfraction' => 1,
                ],
                'range' => [1, 2],
                'expected' => true,
            ],
            'Top end' => [
                'post' => [
                    'q1:2_-mark' => 3.0,
                    'q1:2_-maxmark' => 1.0,
                    'q1:2_:minfraction' => -6.0,
                    'q1:2_:maxfraction' => 3.0,
                ],
                'range' => [1, 2],
                'expected' => true,
            ],
            'Too high' => [
                'post' => [
                    'q1:2_-mark' => 3.1,
                    'q1:2_-maxmark' => 1.0,
                    'q1:2_:minfraction' => -6.0,
                    'q1:2_:maxfraction' => 3.0,
                ],
                'range' => [1, 2],
                'expected' => true,
            ],
        ];
    }

    /**
     * Tests for is_manual_grade_in_range.
     *
     * @covers \question_engine::is_manual_grade_in_range
     */
    public function test_is_manual_grade_in_range_ungraded(): void {
        $this->assertTrue(question_engine::is_manual_grade_in_range(1, 2));
    }

    /**
     * Ensure that the number renderer performs as expected.
     *
     * @covers \core_question_renderer::number
     * @dataProvider render_question_number_provider
     * @param mixed $value
     * @param string $expected
     */
    public function test_render_question_number($value, string $expected): void {
        global $PAGE;

        $renderer = new \core_question_renderer($PAGE, 'core_question');
        $rc = new \ReflectionClass($renderer);
        $rcm = $rc->getMethod('number');
        $rcm->setAccessible(true);

        $this->assertEquals($expected, $rcm->invoke($renderer, $value));
    }

    /**
     * Data provider for test_render_question_number.
     *
     * @return array
     */
    public function render_question_number_provider(): array {
        return [
            'Test with number is i character' => [
                'i',
                '<h3 class="no">Information</h3>',
            ],
            'Test with number is empty string' => [
                '',
                '',
            ],
            'Test with null' => [
                null,
                '',
            ],
            'Test with number is 0' => [
                0,
                '<h3 class="no">Question <span class="qno">0</span></h3>',
            ],
            'Test with number is numeric' => [
                1,
                '<h3 class="no">Question <span class="qno">1</span></h3>',
            ],
            'Test with number is string' => [
                '1 of 2',
                '<h3 class="no">Question <span class="qno">1 of 2</span></h3>',
            ],
        ];
    }
}
