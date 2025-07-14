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

declare(strict_types = 1);

namespace core_completion;

use advanced_testcase;
use coding_exception;
use moodle_exception;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class for unit testing core_completion/activity_custom_completion.
 *
 * @package   core_completion
 * @copyright 2021 Jun Pataleta <jun@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class activity_custom_completion_test extends advanced_testcase {

    /**
     * Fetches a mocked activity_custom_completion instance.
     *
     * @param string[] $methods List of methods to mock.
     * @return activity_custom_completion|MockObject
     */
    protected function setup_mock(array $methods) {
        return $this->getMockBuilder(activity_custom_completion::class)
            ->disableOriginalConstructor()
            ->onlyMethods($methods)
            ->getMockForAbstractClass();
    }

    /**
     * Data provider for test_get_overall_completion_state().
     */
    public static function overall_completion_state_provider(): array {
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');
        return [
            'First incomplete, second complete' => [
                ['completionsubmit', 'completioncreate'],
                [COMPLETION_INCOMPLETE, COMPLETION_COMPLETE],
                1,
                COMPLETION_INCOMPLETE,
            ],
            'First complete, second incomplete' => [
                ['completionsubmit', 'completioncreate'],
                [COMPLETION_COMPLETE, COMPLETION_INCOMPLETE],
                2,
                COMPLETION_INCOMPLETE,
            ],
            'First complete, second failed' => [
                ['completionsubmit', 'completioncreate'],
                [COMPLETION_COMPLETE, COMPLETION_COMPLETE_FAIL],
                2,
                COMPLETION_COMPLETE_FAIL,
            ],
            'First complete, second incomplete, third failed' => [
                ['completionsubmit', 'completioncreate'],
                [COMPLETION_COMPLETE, COMPLETION_INCOMPLETE, COMPLETION_COMPLETE_FAIL],
                2,
                COMPLETION_INCOMPLETE,
            ],
            'All complete' => [
                ['completionsubmit', 'completioncreate'],
                [COMPLETION_COMPLETE, COMPLETION_COMPLETE],
                2,
                COMPLETION_COMPLETE,
            ],
            'No rules' => [
                [],
                [],
                0,
                COMPLETION_COMPLETE,
            ],
        ];
    }

    /**
     * Test for \core_completion\activity_custom_completion::get_overall_completion_state().
     *
     * @dataProvider overall_completion_state_provider
     * @param string[] $rules The custom completion rules.
     * @param int[] $rulestates The completion states of these custom completion rules.
     * @param int $invokecount Expected invoke count of get_state().
     * @param int $state The expected overall completion state
     */
    public function test_get_overall_completion_state(array $rules, array $rulestates, int $invokecount, int $state): void {
        $stub = $this->setup_mock([
            'get_available_custom_rules',
            'get_state',
        ]);

        // Mock activity_custom_completion's get_available_custom_rules() method.
        $stub->expects($this->once())
            ->method('get_available_custom_rules')
            ->willReturn($rules);

        // Mock activity_custom_completion's get_state() method.
        if ($invokecount > 0) {
            $stateinvocations = $this->exactly($invokecount);
            $stub->expects($stateinvocations)
                ->method('get_state')
                ->willReturnCallback(function ($rule) use ($stateinvocations, $rules, $rulestates) {
                    $index = self::getInvocationCount($stateinvocations) - 1;
                    $this->assertEquals($rules[$index], $rule);
                    return $rulestates[$index];
                });
        } else {
            $stub->expects($this->never())
                ->method('get_state');
        }

        $this->assertEquals($state, $stub->get_overall_completion_state());
    }

    /**
     * Data provider for test_validate_rule().
     *
     * @return array[]
     */
    public static function validate_rule_provider(): array {
        return [
            'Not defined' => [
                false, true, coding_exception::class
            ],
            'Not available' => [
                true, false, moodle_exception::class
            ],
            'Defined and available' => [
                true, true, null
            ],
        ];
    }

    /**
     * Test for validate_rule()
     *
     * @dataProvider validate_rule_provider
     * @param bool $defined is_defined()'s mocked return value.
     * @param bool $available is_available()'s mocked return value.
     * @param string|null $expectedexception Expected expectation class name.
     */
    public function test_validate_rule(bool $defined, bool $available, ?string $expectedexception): void {
        $stub = $this->setup_mock([
            'is_defined',
            'is_available'
        ]);

        // Mock activity_custom_completion's is_defined() method.
        $stub->expects($this->any())
            ->method('is_defined')
            ->willReturn($defined);

        // Mock activity_custom_completion's is_available() method.
        $stub->expects($this->any())
            ->method('is_available')
            ->willReturn($available);

        if ($expectedexception) {
            $this->expectException($expectedexception);
        }
        $stub->validate_rule('customcompletionrule');
    }

    /**
     * Test for is_available().
     */
    public function test_is_available(): void {
        $stub = $this->setup_mock([
            'get_available_custom_rules',
        ]);

        // Mock activity_custom_completion's get_available_custom_rules() method.
        $stub->expects($this->any())
            ->method('get_available_custom_rules')
            ->willReturn(['rule1', 'rule2']);

        // Rule is available.
        $this->assertTrue($stub->is_available('rule1'));

        // Rule is not available.
        $this->assertFalse($stub->is_available('rule'));
    }
}
