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

namespace gradepenalty_duedate;

use cm_info;
use context_course;
use context_module;
use context_system;
use core\plugininfo\gradepenalty;
use core_grades\penalty_manager;
use grade_item;
use gradepenalty_duedate\tests\penalty_testcase;

/**
 * Test for penalty calculator.
 *
 * @package   gradepenalty_duedate
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \gradepenalty_duedate\penalty_calculator
 */
final class penalty_calculator_test extends penalty_testcase {
    /**
     * Data provider for test_calculate_penalty.
     */
    public static function calculate_penalty_provider(): array {
        return [
            // Submission date, due date and expected grade.
            // No penalty.
            [0, 0, 100],
            // One day late.
            [1, 0, 90],
            [DAYSECS, 0, 90],
            // Two day late.
            [DAYSECS + 1, 0, 80],
            [DAYSECS * 2, 0, 80],
            // Three day late.
            [DAYSECS * 2 + 1, 0, 70],
            [DAYSECS * 3, 0, 70],
            // Four day late.
            [DAYSECS * 3 + 1, 0, 60],
            [DAYSECS * 4, 0, 60],
            // Five day late.
            [DAYSECS * 4 + 1, 0, 50],
            [DAYSECS * 5, 0, 50],
            // Six day late. Same penalty as five day late.
            [DAYSECS * 5 + 1, 0, 50],
            [DAYSECS * 6, 0, 50],
        ];
    }

    /**
     * Test calculate penalty.
     *
     * @dataProvider calculate_penalty_provider
     *
     * @param int $submissiondate The submission date.
     * @param int $duedate The due date.
     * @param int $expectedgrade The expected grade.
     */
    public function test_calculate_penalty(int $submissiondate, int $duedate, int $expectedgrade): void {
        $this->resetAfterTest();

        // Create a course and an assignment.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $assignment = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        // Create sample rules.
        $this->create_sample_rules();

        // Enable grade penalty.
        penalty_manager::enable_modules(['quiz', 'assign']);
        gradepenalty::enable_plugin('duedate', true);

        // Add a grade.
        grade_update(
            'mod/assign',
            $course->id,
            'mod',
            'assign',
            $assignment->id,
            0,
            ['userid' => $user->id, 'rawgrade' => 100],
        );

        // Get grade item.
        $gradeitemparams = [
            'courseid' => $course->id,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $assignment->id,
            'itemnumber' => 0,
        ];
        $gradeitem = grade_item::fetch($gradeitemparams);

        // Apply penalty.
        penalty_manager::apply_grade_penalty_to_user($user->id, $gradeitem, $submissiondate, $duedate);

        // Check the grade.
        $this->assertEquals($expectedgrade, $gradeitem->get_final($user->id)->finalgrade);
    }

    /**
     * Rules set at different contexts.
     */
    public function test_find_effective_penalty_rules(): void {
        global $DB;
        $this->resetAfterTest();

        // Create a course and an assignment.
        $course = $this->getDataGenerator()->create_course();
        $assignment = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('assign', $assignment->id, $course->id);
        $cm = cm_info::create($cm);

        // Create a penalty rule at the system context.
        $systemcontext = context_system::instance();
        $systemrule = [
            'contextid' => $systemcontext->id,
            'overdueby' => 1,
            'penalty' => 10,
            'sortorder' => 1,
        ];
        $DB->insert_record('gradepenalty_duedate_rule', (object)$systemrule);
        // The penalty should be 10%.
        $this->assertEquals(10, penalty_calculator::get_penalty_from_rules($cm, DAYSECS, 0));

        // Create a penalty rule at the course context.
        $coursecontext = context_course::instance($course->id);
        $courserule = [
            'contextid' => $coursecontext->id,
            'overdueby' => 1,
            'penalty' => 20,
            'sortorder' => 1,
        ];
        $DB->insert_record('gradepenalty_duedate_rule', (object)$courserule);
        // The penalty should be 20%.
        $this->assertEquals(20, penalty_calculator::get_penalty_from_rules($cm, DAYSECS, 0));

        // Create a penalty rule at the module context.
        $cm = get_coursemodule_from_instance('assign', $assignment->id, $course->id);
        $cm = cm_info::create($cm);
        $modulecontext = context_module::instance($cm->id);
        $modulerule = [
            'contextid' => $modulecontext->id,
            'overdueby' => 1,
            'penalty' => 30,
            'sortorder' => 1,
        ];
        $DB->insert_record('gradepenalty_duedate_rule', (object)$modulerule);
        // The penalty should be 30%.
        $this->assertEquals(30, penalty_calculator::get_penalty_from_rules($cm, DAYSECS, 0));
    }
}
