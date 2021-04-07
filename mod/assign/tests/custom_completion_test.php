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
 * @package   mod_assign
 * @copyright Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_assign;

use advanced_testcase;
use cm_info;
use coding_exception;
use mod_assign\completion\custom_completion;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');
/**
 * Class for unit testing mod_assign/activity_custom_completion.
 *
 * @package   mod_assign
 * @copyright Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_custom_completion_test extends advanced_testcase {

    // Use the generator helper.
    use \mod_assign_test_generator;

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
     * @dataProvider get_state_provider
     * @param string $rule The custom completion rule.
     * @param int $available Whether this rule is available.
     * @param bool $submitted
     * @param int|null $status Expected status.
     * @param string|null $exception Expected exception.
     */
    public function test_get_state(string $rule, int $available, ?bool $submitted, ?int $status, ?string $exception) {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, ['completion' => COMPLETION_TRACKING_AUTOMATIC, $rule => $available]);

        // Submit the assignment as the student.
        $this->setUser($student);
        if ($submitted == true) {
            $this->add_submission($student, $assign);
            $this->submit_for_grading($student, $assign);
        }
        $cm = cm_info::create($assign->get_course_module());

        $customcompletion = new custom_completion($cm, (int)$student->id);
        $this->assertEquals($status, $customcompletion->get_state($rule));
    }

    /**
     * Test for get_state().
     *
     * @dataProvider get_state_provider
     * @param string $rule The custom completion rule.
     * @param int $available Whether this rule is available.
     * @param bool $submitted
     * @param int|null $status Expected status.
     * @param string|null $exception Expected exception.
     */
    public function test_get_state_group(string $rule, int $available, ?bool $submitted, ?int $status, ?string $exception) {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, ['completion' => COMPLETION_TRACKING_AUTOMATIC, $rule => $available,
                'teamsubmission' => 1]);

        // Submit the assignment as the student.
        $this->setUser($student);
        if ($submitted == true) {
            $this->add_submission($student, $assign);
            $this->submit_for_grading($student, $assign);
        }
        $cm = cm_info::create($assign->get_course_module());

        $customcompletion = new custom_completion($cm, (int)$student->id);
        $this->assertEquals($status, $customcompletion->get_state($rule));
    }


    /**
     * Test for get_defined_custom_rules().
     */
    public function test_get_defined_custom_rules() {
        $rules = custom_completion::get_defined_custom_rules();
        $this->assertCount(1, $rules);
        $this->assertEquals('completionsubmit', reset($rules));
    }

    /**
     * Test for get_defined_custom_rule_descriptions().
     */
    public function test_get_custom_rule_descriptions() {
        $this->resetAfterTest();
        // Get defined custom rules.
        $rules = custom_completion::get_defined_custom_rules();
        // Get custom rule descriptions.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $assign = $this->create_instance($course, [
            'submissiondrafts' => 0,
            'completionusegrade' => 1
        ]);

        $cm = cm_info::create($assign->get_course_module());
        $customcompletion = new custom_completion($cm, 1);
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
    public function test_is_defined() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $assign = $this->create_instance($course, [
            'submissiondrafts' => 0,
            'completionsubmit' => 1
        ]);

        $cm = cm_info::create($assign->get_course_module());

        $customcompletion = new custom_completion($cm, 1);

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
     * @dataProvider get_available_custom_rules_provider
     * @param int $status
     * @param array $expected
     */
    public function test_get_available_custom_rules(int $status, array $expected) {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => $status]);

        $params = [];
        if ($status == COMPLETION_ENABLED ) {
            $params = [
                'completion' => COMPLETION_TRACKING_AUTOMATIC,
                'completionsubmit' => 1
            ];
        }

        $assign = $this->create_instance($course, $params);
        $cm = cm_info::create($assign->get_course_module());

        $customcompletion = new custom_completion($cm, 1);
        $this->assertEquals($expected, $customcompletion->get_available_custom_rules());
    }
}
