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

namespace mod_assign;

use core_component;
use core_grades\penalty_manager;
use grade_item;
use mod_assign_test_generator;
use mod_assign_testable_assign;
use ReflectionClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');

/**
 * Penalty test.
 *
 * @package    mod_assign
 * @copyright  2024 Catalyst IT Australia Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class penalty_test extends \advanced_testcase {
    // Use the generator helper.
    use mod_assign_test_generator;

    /**
     * Set up test
     *
     * @return array The course and student.
     */
    protected function set_up_test(): array {
        global $CFG;
        $this->setAdminUser();

        penalty_manager::enable_module('assign');

        // Load a mocked grade penalty plugin.
        $mockedcomponent = new ReflectionClass(core_component::class);
        $mockedplugins = $mockedcomponent->getProperty('plugins');
        $plugins = $mockedplugins->getValue();
        $plugins['gradepenalty'] = ["fake_deduction" => "{$CFG->dirroot}/mod/assign/tests/fixtures/fakeplugins/fake_deduction"];
        // Load the penalty_calculator class.
        require_once($CFG->dirroot . '/mod/assign/tests/fixtures/fakeplugins/fake_deduction/classes/penalty_calculator.php');
        $mockedplugins->setValue(null, $plugins);

        \core\plugininfo\gradepenalty::enable_plugin('fake_deduction', true);

        // Create a course with user.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course);

        return [$course, $student];
    }

    /**
     * Test penalty support.
     *
     * @covers ::assign_supports
     */
    public function test_penalty_support(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Assign should be in the supported list.
        $this->assertTrue(in_array('assign', penalty_manager::get_supported_modules()));

        // Penalty is not enabled for any modules by default.
        $this->assertFalse(penalty_manager::is_penalty_enabled_for_module('assign'));

        // Enable penalty for assign.
        penalty_manager::enable_module('assign');

        // Assign should be enabled by now.
        $this->assertTrue(penalty_manager::is_penalty_enabled_for_module('assign'));
    }

    /**
     * Data provider for test_hook_callback.
     *
     * @return array
     */
    public static function apply_penalty_provider(): array {
        return [
            // Submission date, Due date, User override, Group override, Extension due date, Expected messages, Expected grade.
            // No overrides.
            [50, DAYSECS, DAYSECS, null, null, null, ['Submission date: 86400', 'Due date: 86400'], 50],
            [50, DAYSECS + 1, DAYSECS, null, null, null, ['Submission date: 86401', 'Due date: 86400'], 30],
            // User override.
            [50, DAYSECS + 1, DAYSECS, DAYSECS + 1, null, null, ['Submission date: 86401', 'Due date: 86401'], 50],
            [50, DAYSECS + 2, DAYSECS, DAYSECS + 1, null, null, ['Submission date: 86402', 'Due date: 86401'], 30],
            // Group override.
            [50, DAYSECS + 1, DAYSECS, null, DAYSECS + 1, null, ['Submission date: 86401', 'Due date: 86401'], 50],
            [50, DAYSECS + 2, DAYSECS, null, DAYSECS + 1, null, ['Submission date: 86402', 'Due date: 86401'], 30],
            // User and group override.
            [50, DAYSECS + 1, DAYSECS, DAYSECS + 1, DAYSECS + 2, null, ['Submission date: 86401', 'Due date: 86401'], 50],
            [50, DAYSECS + 2, DAYSECS, DAYSECS + 1, DAYSECS + 2, null, ['Submission date: 86402', 'Due date: 86401'], 30],
            // User, group override and extension.
            [50, DAYSECS + 3, DAYSECS, DAYSECS + 1, DAYSECS + 2, DAYSECS + 3, ['Submission date: 86403', 'Due date: 86403'], 50],
            [50, DAYSECS + 4, DAYSECS, DAYSECS + 1, DAYSECS + 2, DAYSECS + 3, ['Submission date: 86404', 'Due date: 86403'], 30],
            // Zero grade.
            [0, DAYSECS, DAYSECS, null, null, null, [], 0],
            [0, DAYSECS + 1, DAYSECS, null, null, null, [], 0],
        ];
    }

    /**
     * Test for hook_listener class.
     *
     * @dataProvider apply_penalty_provider
     *
     * @covers \mod_assign\penalty\helper::apply_penalty_to_submission
     *
     * @param float $usergrade the grade given to user.
     * @param int $submissiondate The submission date.
     * @param int $duedate The due date.
     * @param int $useroverrideduedate The user override due date.
     * @param int $groupoverrideduedate The group override due date.
     * @param int $extensionduedate The extension due date.
     * @param array $expectedmessages The expected debug messages.
     * @param float $expectedgrade The expected final grade.
     *
     */
    public function test_apply_penalty(
        $usergrade,
        $submissiondate,
        $duedate,
        $useroverrideduedate,
        $groupoverrideduedate,
        $extensionduedate,
        $expectedmessages,
        $expectedgrade,
    ): void {
        global $DB;

        $this->resetAfterTest();
        [$course, $student] = $this->set_up_test();

        // Assignment.
        $generator = $this->getDataGenerator();
        $assignmentgenerator = $generator->get_plugin_generator('mod_assign');
        $instance = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $duedate,
            'assignsubmission_onlinetext_enabled' => 1,
            'gradepenalty' => 1,
            'grade' => 200,
        ]);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = \context_module::instance($cm->id);
        $assign = new mod_assign_testable_assign($context, $cm, $course);

        // If there is user override.
        if ($useroverrideduedate) {
            $assignmentgenerator->create_override([
                'assignid' => $instance->id,
                'userid' => $student->id,
                'duedate' => $useroverrideduedate,
            ]);
        }

        // If there is extension.
        if ($extensionduedate) {
            $flags = $assign->get_user_flags($student->id, true);
            $flags->extensionduedate = $extensionduedate;
            $assign->update_user_flags($flags);
        }

        // If there is group override.
        if ($groupoverrideduedate) {
            $group = $generator->create_group(['courseid' => $course->id]);
            $generator->create_group_member(['groupid' => $group->id, 'userid' => $student->id]);
            $assignmentgenerator->create_override([
                'assignid' => $instance->id,
                'groupid' => $group->id,
                'duedate' => $groupoverrideduedate,
            ]);
        }

        // Add submission and grade.
        $this->add_submission($student, $assign, 'Sample text');
        $this->submit_for_grading($student, $assign);
        // Submission date.
        $DB->set_field('assign_submission', 'timemodified', $submissiondate, ['userid' => $student->id]);
        $assign->testable_apply_grade_to_user((object)['grade' => $usergrade], $student->id, 0);

        // Expect debug messages.
        $this->assertdebuggingcalledcount(count($expectedmessages), $expectedmessages);

        // The expected final grade.
        $gradeitem = grade_item::fetch([
            'courseid' => $course->id,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $instance->id,
            'itemnumber' => 0,
        ]);
        $this->assertEquals($expectedgrade, $gradeitem->get_final($student->id)->finalgrade);
    }

    /**
     * Test recalculation.
     *
     * @covers \mod_assign\penalty\helper::apply_penalty_to_submission
     *
     */
    public function test_recalculate_penalty(): void {
        global $DB;

        $this->resetAfterTest();

        [$course, $student] = $this->set_up_test();

        // Assignment.
        $duedate = time() + DAYSECS;
        $generator = $this->getDataGenerator();
        $assignmentgenerator = $generator->get_plugin_generator('mod_assign');
        $instance = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $duedate,
            'assignsubmission_onlinetext_enabled' => 1,
            'gradepenalty' => 1,
            'grade' => 200,
        ]);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = \context_module::instance($cm->id);
        $assign = new mod_assign_testable_assign($context, $cm, $course);

        // Add submission and grade.
        $submissiondate = $duedate + HOURSECS;
        $this->add_submission($student, $assign, 'Sample text');
        $this->submit_for_grading($student, $assign);
        // Submission date.
        $DB->set_field('assign_submission', 'timemodified', $submissiondate, ['userid' => $student->id]);
        $assign->testable_apply_grade_to_user((object)['grade' => 50.0], $student->id, 0);

        $this->assertdebuggingcalledcount(2);

        // Check the grade.
        $gradeitem = grade_item::fetch([
            'courseid' => $course->id,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $instance->id,
            'itemnumber' => 0,
        ]);
        $this->assertEquals(30, $gradeitem->get_final($student->id)->finalgrade);

        // Change the due date.
        $duedate = time() + DAYSECS * 2;
        $DB->set_field('assign', 'duedate', $duedate, ['id' => $instance->id]);

        // Recalculate the penalty.
        $clonedassign = clone $assign->get_instance();
        $clonedassign->cmidnumber = $assign->get_course_module()->idnumber;
        assign_update_grades($clonedassign);
        $this->assertdebuggingcalledcount(2);

        // Check the grade.
        $this->assertEquals(50, $gradeitem->get_final($student->id)->finalgrade);
    }

    /**
     * Test that the penalty in assign_grades is calculated from rawgrade,
     * and that opening a new attempt preserves the existing penalty.
     *
     * @covers \mod_assign\penalty\helper::apply_penalty_to_user
     * @covers \grade_item::update_raw_grade
     */
    public function test_assign_grades_penalty_uses_rawgrade(): void {
        global $DB, $USER;

        $this->resetAfterTest();
        [$course, $student] = $this->set_up_test();

        $generator = $this->getDataGenerator();
        $assignmentgenerator = $generator->get_plugin_generator('mod_assign');

        // Grademax=200; fake_deduction will deduct 10% of grademax = 20 points when late.
        $duedate = DAYSECS;
        $instance = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $duedate,
            'assignsubmission_onlinetext_enabled' => 1,
            'gradepenalty' => 1,
            'grade' => 200,
        ]);

        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = \context_module::instance($cm->id);
        $assign = new mod_assign_testable_assign($context, $cm, $course);

        // Apply grade-item factors.
        grade_update(
            source: 'mod/assign',
            courseid: $course->id,
            itemtype: 'mod',
            itemmodule: 'assign',
            iteminstance: $instance->id,
            itemnumber: 0,
            grades: null,
            itemdetails: ['multfactor' => 2.0, 'plusfactor' => 5.0],
        );

        $this->add_submission($student, $assign, 'Sample text');
        $this->submit_for_grading($student, $assign);

        // Submit one day after due date so penalty applies.
        $DB->set_field('assign_submission', 'timemodified', $duedate + 1, ['userid' => $student->id]);

        // Teacher grades at 60/200.
        $assign->testable_apply_grade_to_user((object)['grade' => 60.0], $student->id, 0);
        $this->assertDebuggingCalledCount(2);

        // Penalty deducted: 10% of grademax(200) = 20 raw points.
        // The penalty in assign_grades is based on rawgrade (60): 20 / 60 * 100 = 33.333%.
        $assigngrade = $DB->get_record('assign_grades', ['assignment' => $instance->id, 'userid' => $student->id]);
        $this->assertEqualsWithDelta(33.333, (float) $assigngrade->penalty, 0.001);

        // Verify the grade item recorded finalgrade = ((60 - 20) * 2) + 5 = 85.
        $gradeitem = grade_item::fetch([
            'courseid' => $course->id,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $instance->id,
            'itemnumber' => 0,
        ]);
        $this->assertEquals(85.0, (float) $gradeitem->get_final($student->id)->finalgrade);
        $this->assertEquals(60.0, (float) $gradeitem->get_final($student->id)->rawgrade);

        // Teacher allows another attempt — triggers update_raw_grade(rawgrade=false).
        $this->setAdminUser();
        $USER->ignoresesskey = true;
        $assign->testable_process_add_attempt($student->id);

        // The penalty must be preserved: finalgrade and deductedmark must not change.
        $after = $gradeitem->get_final($student->id);
        $this->assertEquals(85.0, (float) $after->finalgrade, 'Penalty must be preserved after add_attempt.');
        $this->assertEquals(20.0, (float) $after->deductedmark, 'deductedmark must be preserved after add_attempt.');
    }

    /**
     * Data provider for test_calculate_penalised_grade.
     *
     * @return array
     */
    public static function calculate_penalised_grade_provider(): array {
        // A raw grade of 50 is used in all test cases. The grade item applies
        // a multiplication factor of 1.5 and an offset of 10.
        return [
            'late submission: penalty and grade-item factors applied' => [
                'submissiontime'     => DAYSECS + 1,
                // The fake_deduction plugin emits these messages on every call.
                'debugmessages' => ['Submission date: ' . (DAYSECS + 1), 'Due date: ' . DAYSECS],
                'expectedpenalty' => 20.0,
                'expectedfinalgrade' => 70.0,
                'expecteddeducted' => 10.0,
            ],
            'on-time submission: only grade-item factors applied' => [
                'submissiontime' => DAYSECS - 1,
                'debugmessages' => ['Submission date: ' . (DAYSECS - 1), 'Due date: ' . DAYSECS],
                'expectedpenalty' => 0.0,
                'expectedfinalgrade' => 85.0,
                'expecteddeducted' => 0.0,
            ],
        ];
    }

    /**
     * Test that calculate_penalised_grade applies grade-item multfactor/plusfactor,
     * both when a penalty is deducted and when the submission is on time.
     *
     * @dataProvider calculate_penalised_grade_provider
     * @covers \assign::calculate_penalised_grade
     *
     * @param int $submissiontime Submission timemodified (controls late vs on-time).
     * @param array $debugmessages Expected debugging messages from the penalty plugin.
     * @param float $expectedpenalty Expected penalty percentage stored in assign_grades.
     * @param float $expectedfinalgrade Expected gradebook finalgrade.
     * @param float $expecteddeducted Expected deducted mark returned by calculate_penalised_grade.
     */
    public function test_calculate_penalised_grade(
        int $submissiontime,
        array $debugmessages,
        float $expectedpenalty,
        float $expectedfinalgrade,
        float $expecteddeducted,
    ): void {
        global $DB;

        $this->resetAfterTest();
        [$course, $student] = $this->set_up_test();

        $generator = $this->getDataGenerator();
        $assignmentgenerator = $generator->get_plugin_generator('mod_assign');

        // The grade is set to 100 so the fake_deduction plugin deducts
        // 10% of grademax (10 raw points) for late submissions.
        $instance = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => DAYSECS,
            'assignsubmission_onlinetext_enabled' => 1,
            'gradepenalty' => 1,
            'grade' => 100,
        ]);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = \context_module::instance($cm->id);
        $assign = new mod_assign_testable_assign($context, $cm, $course);

        // Set grade-item factors: finalgrade = rawgrade * 1.5 + 10.
        grade_update(
            source: 'mod/assign',
            courseid: $course->id,
            itemtype: 'mod',
            itemmodule: 'assign',
            iteminstance: $instance->id,
            itemnumber: 0,
            grades: null,
            itemdetails: ['multfactor' => 1.5, 'plusfactor' => 10.0],
        );

        $this->add_submission($student, $assign, 'Test submission');
        $this->submit_for_grading($student, $assign);
        $DB->set_field('assign_submission', 'timemodified', $submissiontime, ['userid' => $student->id]);

        // Teacher grades at 50/100.
        $assign->testable_apply_grade_to_user((object)['grade' => 50.0], $student->id, 0);
        // Verify both the count and the exact content of the debug messages from fake_deduction.
        $this->assertDebuggingCalledCount(count($debugmessages), $debugmessages);

        $assigngrade = $DB->get_record('assign_grades', ['assignment' => $instance->id, 'userid' => $student->id]);
        $this->assertEqualsWithDelta(
            $expectedpenalty,
            (float) $assigngrade->penalty,
            .001,
            'Penalty percentage stored in assign_grades is incorrect.'
        );

        $gradeitem = grade_item::fetch([
            'courseid' => $course->id,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $instance->id,
            'itemnumber' => 0,
        ]);
        $this->assertEqualsWithDelta(
            $expectedfinalgrade,
            (float) $gradeitem->get_final($student->id)->finalgrade,
            0.001,
            'Gradebook finalgrade is incorrect.'
        );

        // The calculate_penalised_grade() must return a value on the same scale as the gradebook finalgrade.
        [$penalisedgrade, $deductedmark] = $assign->calculate_penalised_grade($assigngrade);
        $this->assertEqualsWithDelta(
            $expecteddeducted,
            $deductedmark,
            0.001,
            'Deducted mark returned by calculate_penalised_grade is incorrect.'
        );
        $this->assertEqualsWithDelta(
            $expectedfinalgrade,
            $penalisedgrade,
            0.001,
            'calculate_penalised_grade result must match the gradebook finalgrade.'
        );
    }
}
