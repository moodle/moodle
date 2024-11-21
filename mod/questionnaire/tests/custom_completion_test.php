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
 * Contains unit tests for core_completion/activity_custom_completion.
 *
 * @package   mod_questionnaire
 * @copyright 2022 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_questionnaire;

use cm_info;
use coding_exception;
use mod_questionnaire\completion\custom_completion;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/mod/questionnaire/classes/question/question.php');

/**
 * Class for unit testing mod_questionnaire/custom_completion.
 *
 * @package   mod_questionnaire
 * @copyright 2022 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_completion_test extends \advanced_testcase {

    /**
     * Data provider for get_state().
     *
     * @return array[]
     */
    public function get_state_provider(): array {
        return [
            'Undefined rule' => [
                'somenonexistentrule', COMPLETION_DISABLED, false, null, coding_exception::class
            ],
            'Rule not available' => [
                'completionsubmit', COMPLETION_DISABLED, false, null, moodle_exception::class
            ],
            'Rule available, user has not submitted' => [
                'completionsubmit', COMPLETION_ENABLED, false, COMPLETION_INCOMPLETE, null
            ],
            'Rule available, user has submitted' => [
                'completionsubmit', COMPLETION_ENABLED, true, COMPLETION_COMPLETE, null
            ],
        ];
    }

    /**
     * Test for get_state().
     *
     * @covers \mod_questionnaire\completion\custom_completion::get_state
     * @dataProvider get_state_provider
     * @param string $rule The custom completion rule.
     * @param int $available Whether this rule is available.
     * @param bool $submitted
     * @param int|null $status Expected status.
     * @param string|null $exception Expected exception.
     * @throws coding_exception
     *
     * @covers \mod_questionnaire\completion\custom_completion
     */
    public function test_get_state(string $rule, int $available, ?bool $submitted, ?int $status, ?string $exception) {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_questionnaire');

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $questionnaire = $generator->create_instance(['course' => $course->id, 'completion' => COMPLETION_TRACKING_AUTOMATIC,
            $rule => $available]);

        $questiondata['type_id'] = 1;
        $questiondata['surveyid'] = $questionnaire->sid;
        $questiondata['name'] = 'Q1';
        $questiondata['content'] = 'Test content';
        $question = $generator->create_question($questionnaire, $questiondata);

        // For case user done completion.
        if ($status !== COMPLETION_INCOMPLETE) {
            $response = $generator->create_question_response($questionnaire, $question, 'y', (int)$student->id);
        }

        $this->setUser($student);
        $cm = get_coursemodule_from_instance('questionnaire', $questionnaire->id);
        $cm = cm_info::create($cm);

        $customcompletion = new custom_completion($cm, (int)$student->id);
        $this->assertEquals($status, $customcompletion->get_state($rule));
    }

    /**
     * Test for get_defined_custom_rules().
     *
     * @covers \mod_questionnaire\completion\custom_completion
     */
    public function test_get_defined_custom_rules() {
        $rules = custom_completion::get_defined_custom_rules();
        $this->assertCount(1, $rules);
        $this->assertEquals('completionsubmit', reset($rules));
    }

    /**
     * Test for get_defined_custom_rule_descriptions().
     *
     * @covers \mod_questionnaire\completion\custom_completion
     */
    public function test_get_custom_rule_descriptions() {
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
     *
     * @covers \mod_questionnaire\completion\custom_completion
     */
    public function test_is_defined() {
        // Build a mock cm_info instance.
        $mockcminfo = $this->getMockBuilder(cm_info::class)
            ->disableOriginalConstructor()
            ->getMock();

        $customcompletion = new custom_completion($mockcminfo, 1);

        // Rule is defined.
        $this->assertTrue($customcompletion->is_defined('completionsubmit'));

        // Undefined rule.
        $this->assertFalse($customcompletion->is_defined('somerandomrule'));
    }

    /**
     * Data provider for test_get_available_custom_rules().
     *
     * @return array[]
     */
    public function get_available_custom_rules_provider(): array {
        return [
            'Completion submit available' => [
                COMPLETION_ENABLED, ['completionsubmit']
            ],
            'Completion submit not available' => [
                COMPLETION_DISABLED, []
            ],
        ];
    }

    /**
     * Test for get_available_custom_rules().
     *
     * @covers \mod_questionnaire\completion\custom_completion::get_available_custom_rules
     * @dataProvider get_available_custom_rules_provider
     * @param int $status
     * @param array $expected
     *
     * @covers \mod_questionnaire\completion\custom_completion
     */
    public function test_get_available_custom_rules(int $status, array $expected) {
        $customdataval = [
            'customcompletionrules' => [
                'completionsubmit' => $status
            ]
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
            ->willReturn($customdataval);

        $customcompletion = new custom_completion($mockcminfo, 1);
        $this->assertEquals($expected, $customcompletion->get_available_custom_rules());
    }
}
