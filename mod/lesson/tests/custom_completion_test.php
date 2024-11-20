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

namespace mod_lesson;

use advanced_testcase;
use cm_info;
use coding_exception;
use mod_lesson\completion\custom_completion;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/completionlib.php');

/**
 * Class for unit testing mod_lesson/custom_completion.
 *
 * @package   mod_lesson
 * @copyright 2021 Michael Hawkins <michaelh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class custom_completion_test extends advanced_testcase {

    /**
     * Data provider for get_state().
     *
     * @return array[]
     */
    public static function get_state_provider(): array {
        return [
            'Undefined completion requirement' => [
                'somenonexistentrule', COMPLETION_ENABLED, 3, null, coding_exception::class
            ],
            'Minimum time spent requirement not available' => [
                'completionstatusrequired', COMPLETION_DISABLED, 3, null, moodle_exception::class
            ],
            'Minimum time spent required, user has not spent time in the lesson' => [
                'completiontimespent', 30, false, COMPLETION_INCOMPLETE, null
            ],
            'Minimum time spent required, user has not met completion requirement' => [
                'completiontimespent', 30, 10, COMPLETION_INCOMPLETE, null
            ],
            'Minimum time spent required, user has met completion requirement' => [
                'completiontimespent', 30, 30, COMPLETION_COMPLETE, null
            ],
            'Minimum time spent required, user has exceeded completion requirement' => [
                'completiontimespent', 30, 40, COMPLETION_COMPLETE, null
            ],
            'User must reach end of lesson, has not met completion requirement' => [
                'completionendreached', 1, false, COMPLETION_INCOMPLETE, null
            ],
            'User must reach end of lesson, has met completion requirement' => [
                'completionendreached', 1, true, COMPLETION_COMPLETE, null
            ],
        ];
    }

    /**
     * Test for get_state().
     *
     * @dataProvider get_state_provider
     * @param string $rule The custom completion condition.
     * @param int $rulevalue The custom completion rule value.
     * @param mixed $uservalue The database value returned when checking the rule for the user.
     * @param int|null $status Expected completion status for the rule.
     * @param string|null $exception Expected exception.
     */
    public function test_get_state(string $rule, int $rulevalue, $uservalue, ?int $status, ?string $exception): void {
        global $DB;

        if (!is_null($exception)) {
            $this->expectException($exception);
        }

        // Custom completion rule data for cm_info::customdata.
        $customdataval = [
            'customcompletionrules' => [
                $rule => $rulevalue
            ]
        ];

        // Build a mock cm_info instance.
        $mockcminfo = $this->getMockBuilder(cm_info::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__get'])
            ->getMock();

        // Mock the return of the magic getter method when fetching the cm_info object's
        // customdata and instance values.
        $mockcminfo->expects($this->any())
            ->method('__get')
            ->will($this->returnValueMap([
                ['customdata', $customdataval],
                ['instance', 1],
            ]));

        if ($rule === 'completiontimespent') {
            // Mock the DB call fetching user's lesson time spent.
            $DB = $this->createMock(get_class($DB));
            $DB->expects($this->atMost(1))
                ->method('get_field_sql')
                ->willReturn($uservalue);
        } else if ($rule === 'completionendreached') {
            // Mock the DB call fetching user's end reached state.
            $DB = $this->createMock(get_class($DB));
            $DB->expects($this->atMost(1))
                ->method('record_exists')
                ->willReturn($uservalue);
        }

        $customcompletion = new custom_completion($mockcminfo, 2);

        $this->assertEquals($status, $customcompletion->get_state($rule));
    }

    /**
     * Test for get_defined_custom_rules().
     */
    public function test_get_defined_custom_rules(): void {
        $expectedrules = [
            'completiontimespent',
            'completionendreached',
        ];

        $definedrules = custom_completion::get_defined_custom_rules();
        $this->assertCount(2, $definedrules);

        foreach ($definedrules as $definedrule) {
            $this->assertContains($definedrule, $expectedrules);
        }
    }

    /**
     * Test for get_defined_custom_rule_descriptions().
     */
    public function test_get_custom_rule_descriptions(): void {
        // Get defined custom rules.
        $rules = custom_completion::get_defined_custom_rules();

        // Build a mock cm_info instance.
        $mockcminfo = $this->getMockBuilder(cm_info::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__get'])
            ->getMock();

        // Instantiate a custom_completion object using the mocked cm_info.
        $customcompletion = new custom_completion($mockcminfo, 1);

        // Get custom rule descriptions.
        $ruledescriptions = $customcompletion->get_custom_rule_descriptions();

        // Confirm that defined rules and rule descriptions are consistent with each other.
        $this->assertEquals(count($rules), count($ruledescriptions));
        foreach ($rules as $rule) {
            $this->assertArrayHasKey($rule, $ruledescriptions);
        }
    }

    /**
     * Test for is_defined().
     */
    public function test_is_defined(): void {
        // Build a mock cm_info instance.
        $mockcminfo = $this->getMockBuilder(cm_info::class)
            ->disableOriginalConstructor()
            ->getMock();

        $customcompletion = new custom_completion($mockcminfo, 1);

        // All rules are defined.
        $this->assertTrue($customcompletion->is_defined('completiontimespent'));
        $this->assertTrue($customcompletion->is_defined('completionendreached'));

        // Undefined rule is not found.
        $this->assertFalse($customcompletion->is_defined('somerandomrule'));
    }

    /**
     * Data provider for test_get_available_custom_rules().
     *
     * @return array[]
     */
    public static function get_available_custom_rules_provider(): array {
        return [
            'No completion conditions enabled' => [
                [
                    'completiontimespent' => COMPLETION_DISABLED,
                    'completionendreached' => COMPLETION_DISABLED,
                ],
                [],
            ],
            'Completion end reached enabled only' => [
                [
                    'completiontimespent' => COMPLETION_DISABLED,
                    'completionendreached' => COMPLETION_ENABLED,
                ],
                ['completionendreached'],
            ],
            'Completion time spent enabled only' => [
                [
                    'completiontimespent' => 60,
                    'completionendreached' => COMPLETION_DISABLED,
                ],
                ['completiontimespent'],
            ],
            'Completion end reached and time spent both enabled' => [
                [
                    'completiontimespent' => 90,
                    'completionendreached' => COMPLETION_ENABLED,
                ],
                ['completiontimespent', 'completionendreached'],
            ],
        ];
    }

    /**
     * Test for get_available_custom_rules().
     *
     * @dataProvider get_available_custom_rules_provider
     * @param array $completionrulesvalues
     * @param array $expected
     */
    public function test_get_available_custom_rules(array $completionrulesvalues, array $expected): void {
        $customcompletionrules = [
            'customcompletionrules' => $completionrulesvalues,
        ];

        // Build a mock cm_info instance.
        $mockcminfo = $this->getMockBuilder(cm_info::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__get'])
            ->getMock();

        // Mock the return of magic getter for the customdata attribute.
        $mockcminfo->expects($this->any())
            ->method('__get')
            ->with('customdata')
            ->willReturn($customcompletionrules);

        $customcompletion = new custom_completion($mockcminfo, 1);
        $this->assertEquals($expected, $customcompletion->get_available_custom_rules());
    }
}
