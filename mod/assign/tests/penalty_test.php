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
}
