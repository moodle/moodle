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

use core\plugininfo\gradepenalty;
use core_grades\penalty_manager;

/**
 * Trait providing common functionality for assignment override tests.
 *
 * @package    mod_assign
 * @copyright  2025 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait mod_assign_override_test_trait {
    /**
     * Enable assignment penalties for testing.
     *
     * This enables the penalty for assignment, loads the fake_deduction test plugin
     * (which deducts 10% for late submissions), and enables gradepenalty on the assignment.
     *
     * @param stdClass $assign Assignment record to enable penalty on
     */
    protected function enable_assign_penalty(stdClass $assign): void {
        global $DB, $CFG;

        // Enable penalty for assignment.
        penalty_manager::enable_module('assign');

        // Load the fake penalty plugin for testing.
        $mockedcomponent = new ReflectionClass(core_component::class);
        $mockedplugins = $mockedcomponent->getProperty('plugins');
        $plugins = $mockedplugins->getValue();
        $plugins['gradepenalty'] = ["fake_deduction" => "{$CFG->dirroot}/mod/assign/tests/fixtures/fakeplugins/fake_deduction"];
        require_once($CFG->dirroot . '/mod/assign/tests/fixtures/fakeplugins/fake_deduction/classes/penalty_calculator.php');
        $mockedplugins->setValue(null, $plugins);
        gradepenalty::enable_plugin('fake_deduction', true);

        // Enable gradepenalty to allow grade recalculation.
        $assign->gradepenalty = 1;
        $DB->update_record('assign', $assign);
    }

    /**
     * Get the final grade for a user.
     *
     * @param int $assignid Assignment ID
     * @param int $courseid Course ID
     * @param int $userid User ID
     * @return float|null The final grade, or null if not graded
     */
    protected function get_final_grade(int $assignid, int $courseid, int $userid): ?float {
        $gradeitem = grade_item::fetch([
            'courseid' => $courseid,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $assignid,
            'itemnumber' => 0,
        ]);

        if (!$gradeitem) {
            return null;
        }

        $finalgrade = $gradeitem->get_final($userid);
        return $finalgrade?->finalgrade;
    }

    /**
     * Helper method to set up a late submission scenario with grade penalties.
     *
     * This method:
     * - Enables the penalty system for the assignment
     * - Sets a due date in the past
     * - Creates a submission after the due date (late submission)
     * - Applies a grade which should trigger penalty calculation
     *
     * Note: This method requires the test class to also use mod_assign_test_generator trait
     * for add_submission() and submit_for_grading() methods.
     *
     * @param array $data Test data array (should contain 'assign', 'context', 'cm' keys)
     * @param stdClass $student Student to create submission for
     * @param int $grade Initial grade to apply before penalty (default 100)
     * @return mod_assign_testable_assign The testable assign instance
     */
    protected function setup_late_submission_with_penalty(
        array $data,
        stdClass $student,
        int $grade = 100
    ): mod_assign_testable_assign {
        global $DB;

        // Enable assignment penalty system.
        $this->enable_assign_penalty($data['assign']);

        // Set times for late submission scenario.
        $duedate = time() - (2 * DAYSECS); // Due date 2 days ago.
        $submissiontime = time() - DAYSECS; // Submitted 1 day ago (1 day AFTER due date = late).

        // Update assignment due date.
        $data['assign']->duedate = $duedate;
        $DB->update_record('assign', $data['assign']);

        // Create testable assign instance.
        $course = $DB->get_record('course', ['id' => $data['assign']->course]);
        $assign = new mod_assign_testable_assign($data['context'], $data['cm'], $course);

        // Add submission and grade using proper methods.
        $this->add_submission($student, $assign, 'Sample text');
        $this->submit_for_grading($student, $assign);

        // Set submission time to be late (AFTER the due date).
        $DB->set_field('assign_submission', 'timemodified', $submissiontime, [
            'assignment' => $data['assign']->id,
            'userid' => $student->id,
        ]);

        // Penalty should be calculated here.
        $assign->testable_apply_grade_to_user((object)['grade' => $grade], $student->id, 0);

        // Penalty system should have logged debug messages.
        $this->assertDebuggingCalledCount(2);

        return $assign;
    }

    /**
     * Create basic assignment test data structure for override testing.
     *
     * Creates a course with groups, an assignment, teacher and students.
     * This is the common setup used by both external and manager override tests.
     *
     * @return array Array containing course, assignment, cm, context, users, and groups
     */
    protected function create_assign_with_overrides_test_data(): array {
        // Create course with group mode.
        $course = $this->getDataGenerator()->create_course([
            'enablecompletion' => 1,
            'groupmode' => SEPARATEGROUPS,
            'groupmodeforce' => 0,
        ]);

        // Create groups.
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Group 1']);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Group 2']);

        // Create assignment.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $now = time();
        $instance = $generator->create_instance([
            'course' => $course->id,
            'name' => 'Test Assignment',
            'allowsubmissionsfromdate' => $now,
            'duedate' => $now + (7 * 24 * 60 * 60),
            'cutoffdate' => $now + (14 * 24 * 60 * 60),
        ]);

        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);

        // Create and enrol users.
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student3 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Add students to groups.
        $this->getDataGenerator()->create_group_member(['groupid' => $group1->id, 'userid' => $student1->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $group1->id, 'userid' => $student2->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $group2->id, 'userid' => $student3->id]);

        return [
            'course' => $course,
            'assign' => $instance,
            'cm' => $cm,
            'context' => $context,
            'teacher' => $teacher,
            'student1' => $student1,
            'student2' => $student2,
            'student3' => $student3,
            'group1' => $group1,
            'group2' => $group2,
        ];
    }
}
