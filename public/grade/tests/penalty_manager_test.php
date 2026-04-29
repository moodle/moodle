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

namespace core_grades;

use advanced_testcase;
use context_system;
use core\plugininfo\gradepenalty;
use grade_item;

/**
 * Unit tests for penalty_manager class.
 *
 * @package   core_grades
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_grades\penalty_manager
 */
final class penalty_manager_test extends advanced_testcase {
    /**
     * Test is_penalty_enabled_for_module method.
     */
    public function test_is_penalty_enabled_for_module(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // No modules are enabled by default.
        $this->assertEmpty(penalty_manager::get_enabled_modules());

        // Enable a module.
        penalty_manager::enable_module('assign');
        $this->assertCount(1, penalty_manager::get_enabled_modules());
        $this->assertTrue(penalty_manager::is_penalty_enabled_for_module('assign'));

        // Enable multiple modules.
        penalty_manager::enable_modules(['quiz', 'forum', 'page']);
        $this->assertCount(4, penalty_manager::get_enabled_modules());
        $this->assertTrue(penalty_manager::is_penalty_enabled_for_module('assign'));
        $this->assertTrue(penalty_manager::is_penalty_enabled_for_module('quiz'));
        $this->assertTrue(penalty_manager::is_penalty_enabled_for_module('forum'));
        $this->assertTrue(penalty_manager::is_penalty_enabled_for_module('page'));

        // Disable a module.
        penalty_manager::disable_module('assign');
        $this->assertCount(3, penalty_manager::get_enabled_modules());
        $this->assertTrue(penalty_manager::is_penalty_enabled_for_module('quiz'));
        $this->assertTrue(penalty_manager::is_penalty_enabled_for_module('forum'));
        $this->assertTrue(penalty_manager::is_penalty_enabled_for_module('page'));

        // Disable multiple modules.
        penalty_manager::disable_modules(['quiz', 'forum']);
        $this->assertCount(1, penalty_manager::get_enabled_modules());
        $this->assertTrue(penalty_manager::is_penalty_enabled_for_module('page'));
    }

    /**
     * Test apply_grade_penalty_to_user method.
     */
    public function test_apply_grade_penalty_to_user(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        // Create user, course and assignment.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        // Get grade item.
        $gradeitemparams = [
            'courseid' => $course->id,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $assign->id,
            'itemnumber' => 0,
        ];

        $gradeitem = grade_item::fetch($gradeitemparams);

        grade_update(
            'mod/assign',
            $course->id,
            'mod',
            'assign',
            $assign->id,
            0,
           ['userid' => $user->id, 'rawgrade' => 90],
        );

        $submissiondate = time();
        $duedate = time();
        $container = penalty_manager::apply_grade_penalty_to_user($user->id, $gradeitem, $submissiondate, $duedate);

        // No penalty by default.
        $this->assertEquals(90, $container->get_grade_after_penalties());
    }

    /**
     * Test penalty is deducted from raw grade before grade-item factors are applied.
     *
     * @covers \core_grades\penalty_manager::apply_grade_penalty_to_user
     * @covers \core_grades\penalty_manager::apply_grade_item_factors
     */
    public function test_penalty_applied_before_grade_factors(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Enable assign penalties and the due date penalty plugin.
        penalty_manager::enable_module('assign');
        gradepenalty::enable_plugin('duedate', true);

        // Add a single penalty rule at system context: 10% penalty if overdue.
        $DB->insert_record('gradepenalty_duedate_rule', (object)[
            'contextid' => context_system::instance()->id,
            'overdueby' => 1,
            'penalty' => 10,
            'sortorder' => 1,
        ]);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'grade' => 200]);

        // Set grade factors to exercise adjustment logic.
        grade_update(
            source: 'mod/assign',
            courseid: $course->id,
            itemtype: 'mod',
            itemmodule: 'assign',
            iteminstance: $assign->id,
            itemnumber: 0,
            grades: ['userid' => $user->id, 'rawgrade' => 50],
            itemdetails: ['multfactor' => 2.0, 'plusfactor' => 5.0],
        );

        $gradeitem = grade_item::fetch([
            'courseid' => $course->id,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $assign->id,
            'itemnumber' => 0,
        ]);

        // Before penalty: (50 * 2) + 5 = 105.
        $before = $gradeitem->get_final($user->id);
        $this->assertEquals(105.0, (float)$before->finalgrade);

        // One day late applies 10% of grademax (200) = 20 raw-grade points deduction.
        penalty_manager::apply_grade_penalty_to_user($user->id, $gradeitem, DAYSECS + 1, 0);
        // Apply the same penalty twice to confirm it doesn't accumulate.
        penalty_manager::apply_grade_penalty_to_user($user->id, $gradeitem, DAYSECS + 1, 0);

        $after = $gradeitem->get_final($user->id);
        // Raw: 50 - 20 = 30, then (30 * 2) + 5 = 65.
        $this->assertEquals(65.0, (float)$after->finalgrade);
        $this->assertEquals(20.0, (float)$after->deductedmark);
        // Rawgrade should remain 50 (penalty stored separately in deductedmark).
        $this->assertEquals(50.0, (float)$after->rawgrade);
    }

    /**
     * Test that due date change triggers recalculation of penalty.
     *
     * @covers \core_grades\penalty_manager::apply_grade_penalty_to_user
     */
    public function test_apply_grade_penalty_with_due_date_extension(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        penalty_manager::enable_module('assign');
        gradepenalty::enable_plugin('duedate', true);

        $DB->insert_record('gradepenalty_duedate_rule', (object)[
            'contextid' => context_system::instance()->id,
            'overdueby' => 1,
            'penalty' => 10,
            'sortorder' => 1,
        ]);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'grade' => 200]);

        grade_update(
            source: 'mod/assign',
            courseid: $course->id,
            itemtype: 'mod',
            itemmodule: 'assign',
            iteminstance: $assign->id,
            itemnumber: 0,
            grades: ['userid' => $user->id, 'rawgrade' => 50],
            itemdetails: ['multfactor' => 2.0, 'plusfactor' => 5.0],
        );

        $gradeitem = grade_item::fetch([
            'courseid' => $course->id,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $assign->id,
            'itemnumber' => 0,
        ]);

        // Initial state: on time, no penalty.
        $before = $gradeitem->get_final($user->id);
        $this->assertEquals(105.0, (float)$before->finalgrade);
        $this->assertEquals(0.0, (float)$before->deductedmark);

        // Apply penalty when one day late.
        $submissiondate = DAYSECS + 1; // Late by 1 day.
        $duedate = 0; // Due at time 0.
        penalty_manager::apply_grade_penalty_to_user($user->id, $gradeitem, $submissiondate, $duedate);

        $afterpenalty = $gradeitem->get_final($user->id);
        // Raw: 50 - 20 = 30, then (30 * 2) + 5 = 65.
        $this->assertEquals(65.0, (float)$afterpenalty->finalgrade);
        $this->assertEquals(20.0, (float)$afterpenalty->deductedmark);

        // Now extend the due date so submission is on time.
        $newdue = DAYSECS + 2;  // New due date after submission.
        penalty_manager::apply_grade_penalty_to_user($user->id, $gradeitem, $submissiondate, $newdue);

        $afterextension = $gradeitem->get_final($user->id);
        // No penalty now: raw stays 50, so (50 * 2) + 5 = 105.
        $this->assertEquals(105.0, (float)$afterextension->finalgrade);
        $this->assertEquals(0.0, (float)$afterextension->deductedmark);
    }

    /**
     * Test that grade_item::regrade_final_grades() preserves a penalised grade.
     *
     * A full regrade must not overwrite a penalised finalgrade with the plain
     * adjust_raw_grade(rawgrade) result. This regression test covers the fix in
     * grade_item::regrade_final_grades() that checks deductedmark before recomputing.
     *
     * @covers \grade_item::regrade_final_grades
     */
    public function test_full_regrade_preserves_penalised_finalgrade(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        penalty_manager::enable_module('assign');
        gradepenalty::enable_plugin('duedate', true);

        // 10% penalty rule: any overdue submission loses 10% of grademax.
        $DB->insert_record('gradepenalty_duedate_rule', (object)[
            'contextid' => context_system::instance()->id,
            'overdueby' => 1,
            'penalty'   => 10,
            'sortorder' => 1,
        ]);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'grade' => 100]);

        // Grade item: multfactor=1.5, rawgrade=50 → unpenalised finalgrade = 50 * 1.5 = 75.
        grade_update(
            source: 'mod/assign',
            courseid: $course->id,
            itemtype: 'mod',
            itemmodule: 'assign',
            iteminstance: $assign->id,
            itemnumber: 0,
            grades: ['userid' => $user->id, 'rawgrade' => 50],
            itemdetails: ['multfactor' => 1.5, 'plusfactor' => 0.0],
        );

        $gradeitem = grade_item::fetch([
            'courseid' => $course->id,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $assign->id,
            'itemnumber' => 0,
        ]);
        $this->assertEqualsWithDelta(75.0, (float) $gradeitem->get_final($user->id)->finalgrade, 0.001);

        // Apply penalty: 1 day late -> 10% of grademax(100) = 10 raw points deducted.
        // Penalised finalgrade = (50 − 10) * 1.5 = 60.
        penalty_manager::apply_grade_penalty_to_user($user->id, $gradeitem, DAYSECS + 1, 0);

        $penalised = $gradeitem->get_final($user->id);
        $this->assertEqualsWithDelta(60.0, (float) $penalised->finalgrade, 0.001);
        $this->assertEqualsWithDelta(10.0, (float) $penalised->deductedmark, 0.001);

        // Simulate what happens when a course item (or category) triggers a full
        // regrade of the grade item (e.g. on first page load when needsupdate=1).
        $gradeitem->needsupdate = 1;
        $DB->set_field('grade_items', 'needsupdate', 1, ['id' => $gradeitem->id]);

        // Before the fix, regrade_final_grades() would compute
        // finalgrade = adjust_raw_grade(50) * 1.5 = 75, silently undoing the penalty.
        $gradeitem->regrade_final_grades($user->id);

        $after = $gradeitem->get_final($user->id);
        $this->assertEqualsWithDelta(
            60.0,
            (float) $after->finalgrade,
            0.001,
            'Full regrade must not undo an existing penalty.'
        );
        $this->assertEqualsWithDelta(
            10.0,
            (float) $after->deductedmark,
            0.001,
            'deductedmark must be unchanged after a full regrade.'
        );
    }
}
