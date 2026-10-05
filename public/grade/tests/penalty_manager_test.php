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
use grade_grade;
use grade_item;

/**
 * Unit tests for penalty_manager class.
 *
 * @package   core_grades
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\core_grades\penalty_manager::class)]
#[\PHPUnit\Framework\Attributes\CoversMethod(\core_grades\penalty_manager::class, 'apply_grade_penalty_to_user')]
#[\PHPUnit\Framework\Attributes\CoversMethod(\core_grades\penalty_manager::class, 'apply_grade_item_factors')]
#[\PHPUnit\Framework\Attributes\CoversMethod(\grade_item::class, 'regrade_final_grades')]
#[\PHPUnit\Framework\Attributes\CoversMethod(\core_grades\penalty_manager::class, 'requires_legacy_penalty_calculation')]
#[\PHPUnit\Framework\Attributes\CoversMethod(\core_grades\penalty_manager::class, 'get_authoritative_user_grades')]
#[\PHPUnit\Framework\Attributes\CoversMethod(\core_grades\penalty_container::class, 'get_grade_before_penalties')]
#[\PHPUnit\Framework\Attributes\CoversMethod(\grade_item::class, 'update_raw_grade')]
#[\PHPUnit\Framework\Attributes\CoversMethod(\core_grades\penalty_manager::class, 'repair_penalised_rawgrade')]
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

    /**
     * Test that a frozen gradebook uses the pre-MDL-88407 penalty calculation.
     */
    public function test_frozen_gradebook_uses_legacy_penalty_calculation(): void {
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

        // Simulate a legacy-corrupted grade: the Assignment's authoritative grade (30) differs from the
        // stored rawgrade (50), so the legacy calculation must be used while the gradebook is frozen.
        $gradeitem = $this->create_assignment_grade_item($course->id, $user->id, grade: 30.0);
        grade_update(
            source: 'mod/assign',
            courseid: $course->id,
            itemtype: 'mod',
            itemmodule: 'assign',
            iteminstance: $gradeitem->iteminstance,
            itemnumber: 0,
            grades: ['userid' => $user->id, 'rawgrade' => 50],
        );

        // Freeze the gradebook so the pre-MDL-88407 calculation is retained.
        set_config('gradebook_calculations_freeze_' . $course->id, 20260808);

        penalty_manager::apply_grade_penalty_to_user(
            $user->id,
            $gradeitem,
            DAYSECS + 1,
            0
        );

        // The frozen path uses the deprecated legacy calculation.
        $this->assertDebuggingCalled();

        $grade = $gradeitem->get_grade($user->id, true);

        // The legacy path stores the penalised grade as rawgrade.
        $this->assertEqualsWithDelta(85.0, (float)$grade->rawgrade, 0.001);
        $this->assertEqualsWithDelta(20.0, (float)$grade->deductedmark, 0.001);

        // The grade-item factors are then applied again when calculating finalgrade.
        $this->assertEqualsWithDelta(175.0, (float)$grade->finalgrade, 0.001);
    }

    /**
     * Test that a verified Assignment grade uses the fixed calculation while the course is frozen.
     *
     * This also covers the ordering requirement in apply_grade_penalty_to_user(), where the penalised
     * raw grade must be read before deductedmark is updated (see MDL-89749).
     */
    public function test_frozen_gradebook_uses_fixed_calculation_for_verified_grade(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        penalty_manager::enable_module('assign');
        gradepenalty::enable_plugin('duedate', true);

        $DB->insert_record('gradepenalty_duedate_rule', (object) [
            'contextid' => context_system::instance()->id,
            'overdueby' => 1,
            'penalty' => 10,
            'sortorder' => 1,
        ]);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        // Assignment's own authoritative grade matches the mark about to be penalised: 50.
        $gradeitem = $this->create_assignment_grade_item($course->id, $user->id, grade: 50.0);
        grade_update(
            source: 'mod/assign',
            courseid: $course->id,
            itemtype: 'mod',
            itemmodule: 'assign',
            iteminstance: $gradeitem->iteminstance,
            itemnumber: 0,
            grades: ['userid' => $user->id, 'rawgrade' => 50],
        );

        // Freeze the course, as if the upgrade had found unrelated legacy corruption elsewhere in it.
        set_config('gradebook_calculations_freeze_' . $course->id, 20260808);

        penalty_manager::apply_grade_penalty_to_user($user->id, $gradeitem, DAYSECS + 1, 0);

        // A verified grade must use the fixed calculation even while the course is frozen.
        $this->assertDebuggingNotCalled();

        $grade = $gradeitem->get_grade($user->id, true);

        // Rawgrade is left holding the original, unpenalised mark.
        $this->assertEqualsWithDelta(50.0, (float)$grade->rawgrade, 0.001);
        $this->assertEqualsWithDelta(20.0, (float)$grade->deductedmark, 0.001);
        // Expected: (50 - 20) * 2 + 5 = 65.
        $this->assertEqualsWithDelta(65.0, (float)$grade->finalgrade, 0.001);
    }

    /**
     * Test that re-applying a penalty to an already-correct grade does not compound the penalty,
     * even while the gradebook is frozen.
     */
    public function test_frozen_gradebook_reapplying_penalty_does_not_compound(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        penalty_manager::enable_module('assign');
        gradepenalty::enable_plugin('duedate', true);

        $DB->insert_record('gradepenalty_duedate_rule', (object) [
            'contextid' => context_system::instance()->id,
            'overdueby' => 1,
            'penalty' => 10,
            'sortorder' => 1,
        ]);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        $gradeitem = $this->create_assignment_grade_item($course->id, $user->id, grade: 50.0);
        grade_update(
            source: 'mod/assign',
            courseid: $course->id,
            itemtype: 'mod',
            itemmodule: 'assign',
            iteminstance: $gradeitem->iteminstance,
            itemnumber: 0,
            grades: ['userid' => $user->id, 'rawgrade' => 50],
        );

        // Establish the correct post-MDL-88407 state: (50 - 20) * 2 + 5 = 65.
        penalty_manager::apply_grade_penalty_to_user($user->id, $gradeitem, DAYSECS + 1, 0);

        // Freeze the course, as if the upgrade had found unrelated legacy corruption elsewhere in it.
        set_config('gradebook_calculations_freeze_' . $course->id, 20260808);

        // Re-apply the same penalty twice while frozen; the result must not compound.
        penalty_manager::apply_grade_penalty_to_user($user->id, $gradeitem, DAYSECS + 1, 0);
        penalty_manager::apply_grade_penalty_to_user($user->id, $gradeitem, DAYSECS + 1, 0);

        $grade = $gradeitem->get_grade($user->id, true);
        $this->assertEqualsWithDelta(50.0, (float)$grade->rawgrade, 0.001);
        $this->assertEqualsWithDelta(20.0, (float)$grade->deductedmark, 0.001);
        $this->assertEqualsWithDelta(65.0, (float)$grade->finalgrade, 0.001);
    }

    /**
     * Test that a frozen regrade preserves the penalty on an already-correct grade in a mixed-state course.
     */
    public function test_frozen_regrade_preserves_penalty_on_already_correct_row(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // A genuinely legacy-corrupted row elsewhere in the course - the reason the course is frozen.
        $this->create_corrupted_assignment_grade($course->id, $user->id, [0 => 25.0], 0);

        // A second Assignment with a correctly stored post-MDL-88407 grade:
        // rawgrade = 50, deductedmark = 20, finalgrade = (50 - 20) * 2 + 5 = 65.
        $gradeitem = $this->create_assignment_grade_item($course->id, $user->id);
        $grade = $gradeitem->get_grade($user->id, true);
        $grade->rawgrade = 50;
        $grade->deductedmark = 20;
        $grade->finalgrade = 65;
        $grade->update();

        // Freeze the course, as if the upgrade had found the legacy row above.
        set_config('gradebook_calculations_freeze_' . $course->id, 20260808);

        // Simulate an intervening full regrade before the freeze is accepted (e.g. adding a grade item).
        $DB->set_field('grade_items', 'needsupdate', 1, ['id' => $gradeitem->id]);
        grade_regrade_final_grades($course->id);

        // The already-correct row's penalty must survive the frozen regrade.
        $after = $gradeitem->get_final($user->id);
        $this->assertEqualsWithDelta(50.0, (float)$after->rawgrade, 0.001);
        $this->assertEqualsWithDelta(20.0, (float)$after->deductedmark, 0.001);
        $this->assertEqualsWithDelta(65.0, (float)$after->finalgrade, 0.001);
    }

    /**
     * Test that a frozen regrade still protects a genuinely legacy-corrupted grade.
     */
    public function test_frozen_regrade_still_protects_legacy_row(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Legacy-corrupted row: Assignment's authoritative grade is 25, but the gradebook rawgrade
        // still contains the legacy penalised value of 85.
        [, $gradeitem, ] = $this->create_corrupted_assignment_grade($course->id, $user->id, [0 => 25.0], 0);

        set_config('gradebook_calculations_freeze_' . $course->id, 20260808);

        $DB->set_field('grade_items', 'needsupdate', 1, ['id' => $gradeitem->id]);
        grade_regrade_final_grades($course->id);

        // Preserve the legacy finalgrade (85 * 2 + 5 = 175) rather than treating 85 as the
        // unpenalised Assignment grade.
        $after = $gradeitem->get_final($user->id);
        $this->assertEqualsWithDelta(85.0, (float)$after->rawgrade, 0.001);
        $this->assertEqualsWithDelta(175.0, (float)$after->finalgrade, 0.001);
    }

    /**
     * Test that a legacy-corrupted grade whose stored rawgrade coincidentally matches the authoritative
     * Assignment grade survives an intervening regrade while the course is frozen.
     */
    public function test_frozen_regrade_protects_legacy_row_with_coincidental_rawgrade(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $gradeitem = $this->create_assignment_grade_item(
            $course->id,
            $user->id,
            grade: 50.0,
            multfactor: 1.0,
            plusfactor: 20.0,
        );
        $grade = $gradeitem->get_grade($user->id, true);
        $DB->update_record('grade_grades', (object) [
            'id' => $grade->id,
            'rawgrade' => 50,
            'deductedmark' => 20,
            'finalgrade' => 70,
        ]);

        // Freeze the course, as if the upgrade had detected this coincidence-affected row.
        set_config('gradebook_calculations_freeze_' . $course->id, 20260808);

        // Simulate an intervening full regrade before the freeze is accepted (e.g. adding a grade
        // item to the course), exactly as described in MDL-89749.
        $DB->set_field('grade_items', 'needsupdate', 1, ['id' => $gradeitem->id]);
        grade_regrade_final_grades($course->id);

        // The legacy finalgrade must survive the regrade - it must only change after Accept.
        $after = $gradeitem->get_final($user->id);
        $this->assertEqualsWithDelta(50.0, (float)$after->rawgrade, 0.001);
        $this->assertEqualsWithDelta(20.0, (float)$after->deductedmark, 0.001);
        $this->assertEqualsWithDelta(70.0, (float)$after->finalgrade, 0.001);
    }

    /**
     * Test that update_raw_grade() preserves the penalty on an already-correct grade while frozen.
     */
    public function test_frozen_update_raw_grade_preserves_penalty_on_already_correct_row(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // An Assignment with a correctly stored post-MDL-88407 grade:
        // rawgrade = 50, deductedmark = 20, finalgrade = (50 - 20) * 2 + 5 = 65.
        $gradeitem = $this->create_assignment_grade_item($course->id, $user->id);
        $grade = $gradeitem->get_grade($user->id, true);
        $grade->rawgrade = 50;
        $grade->deductedmark = 20;
        $grade->finalgrade = 65;
        $grade->update();

        // Freeze the course, as if the upgrade had found a legacy-corrupted row elsewhere in it.
        set_config('gradebook_calculations_freeze_' . $course->id, 20260808);

        // Simulate a grade update with no new rawgrade supplied.
        $gradeitem->update_raw_grade($user->id, false);

        // The already-correct row's penalty must survive this update.
        $after = $gradeitem->get_final($user->id);
        $this->assertEqualsWithDelta(50.0, (float)$after->rawgrade, 0.001);
        $this->assertEqualsWithDelta(20.0, (float)$after->deductedmark, 0.001);
        $this->assertEqualsWithDelta(65.0, (float)$after->finalgrade, 0.001);
    }

    /**
     * Test that reopening an Assignment preserves the penalty on an already-correct grade while frozen.
     */
    public function test_frozen_update_raw_grade_preserves_penalty_after_reopening_attempt(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // An Assignment with a correctly stored post-MDL-88407 grade:
        // rawgrade = 50, deductedmark = 20, finalgrade = (50 - 20) * 2 + 5 = 65.
        $gradeitem = $this->create_assignment_grade_item($course->id, $user->id);
        $grade = $gradeitem->get_grade($user->id, true);
        $grade->rawgrade = 50;
        $grade->deductedmark = 20;
        $grade->finalgrade = 65;
        $grade->update();

        // Freeze the course, as if the upgrade had found a legacy-corrupted row elsewhere in it.
        set_config('gradebook_calculations_freeze_' . $course->id, 20260808);

        // Reopen the submission so the latest attempt is ungraded and has no authoritative grade.
        $now = time();
        $DB->set_field('assign_submission', 'latest', 0, ['assignment' => $gradeitem->iteminstance, 'userid' => $user->id]);
        $DB->insert_record('assign_submission', (object) [
            'assignment' => $gradeitem->iteminstance,
            'userid' => $user->id,
            'timecreated' => $now,
            'timemodified' => $now,
            'status' => 'reopened',
            'groupid' => 0,
            'attemptnumber' => 1,
            'latest' => 1,
        ]);

        // Simulate the gradebook sync triggered by reopening, with no new rawgrade.
        $gradeitem->update_raw_grade($user->id, false);

        // The already-correct row's penalty must survive the reopen.
        $after = $gradeitem->get_final($user->id);
        $this->assertEqualsWithDelta(50.0, (float)$after->rawgrade, 0.001);
        $this->assertEqualsWithDelta(20.0, (float)$after->deductedmark, 0.001);
        $this->assertEqualsWithDelta(65.0, (float)$after->finalgrade, 0.001);
    }

    /**
     * Test that a feedback-only update preserves deductedmark on an already-correct grade while frozen.
     */
    public function test_frozen_update_raw_grade_preserves_deductedmark_on_feedback_only_update(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // An Assignment with a correctly stored post-MDL-88407 grade:
        // rawgrade = 50, deductedmark = 20, finalgrade = 65.
        $gradeitem = $this->create_assignment_grade_item($course->id, $user->id);
        $grade = $gradeitem->get_grade($user->id, true);
        $grade->rawgrade = 50;
        $grade->deductedmark = 20;
        $grade->finalgrade = 65;
        $grade->update();

        // Freeze the course, as if the upgrade had found a legacy-corrupted row elsewhere in it.
        set_config('gradebook_calculations_freeze_' . $course->id, 20260808);

        // A feedback-only update: no new rawgrade, but a new feedback comment, so timemodified changes.
        $gradeitem->update_raw_grade($user->id, false, null, 'New feedback comment');

        $after = $gradeitem->get_final($user->id);
        $this->assertEqualsWithDelta(50.0, (float)$after->rawgrade, 0.001);
        $this->assertEqualsWithDelta(20.0, (float)$after->deductedmark, 0.001);
        $this->assertEqualsWithDelta(65.0, (float)$after->finalgrade, 0.001);

        // A later, unrelated full regrade must still reflect the penalty, proving deductedmark was
        // genuinely preserved in the database and not just coincidentally still correct in-memory.
        $DB->set_field('grade_items', 'needsupdate', 1, ['id' => $gradeitem->id]);
        grade_regrade_final_grades($course->id);
        $regraded = $gradeitem->get_final($user->id);
        $this->assertEqualsWithDelta(50.0, (float)$after->rawgrade, 0.001);
        $this->assertEqualsWithDelta(20.0, (float)$regraded->deductedmark, 0.001);
        $this->assertEqualsWithDelta(65.0, (float)$regraded->finalgrade, 0.001);
    }

    /**
     * Repairs legacy penalised rawgrades for Assignment grades using the Assignment gradebook API.
     */
    public function test_repair_penalised_rawgrade_uses_assignment_gradebook_grade(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // With a single graded attempt, the Assignment gradebook API returns that attempt's grade.
        [, $singleitem] = $this->create_corrupted_assignment_grade($course->id, $user->id, [0 => 50.0], 0);
        penalty_manager::repair_penalised_rawgrade($course->id);
        $this->assertEqualsWithDelta(50.0, (float)$singleitem->get_grade($user->id)->rawgrade, 0.001);

        // Assignment selects attempt 1 as the current attempt, so its grade of 50 is used rather than
        // the older attempt's grade of 10.
        [, $multiitem] = $this->create_corrupted_assignment_grade($course->id, $user->id, [0 => 10.0, 1 => 50.0], 1);
        penalty_manager::repair_penalised_rawgrade($course->id);
        $this->assertEqualsWithDelta(50.0, (float)$multiitem->get_grade($user->id)->rawgrade, 0.001);
    }

    /**
     * Assignment repair respects locked and overridden grades and lock times.
     *
     * The rawgrade and the finalgrade must not be modified if the grade is locked or overridden.
     */
    public function test_repair_penalised_rawgrade_respects_grade_lock_state(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // A grade locked explicitly must not be modified by the subsequent grade-item recalculation.
        [, $lockeditem, $lockedgrade] = $this->create_corrupted_assignment_grade($course->id, $user->id, [0 => 50.0], 0);
        $lockedgrade->locked = time();
        $lockedgrade->update();

        [, $overriddenitem, $overriddengrade] = $this->create_corrupted_assignment_grade($course->id, $user->id, [0 => 50.0], 0);
        $overriddengrade->overridden = time();
        $overriddengrade->update('overridden');

        [, $pastitem, $pastgrade] = $this->create_corrupted_assignment_grade($course->id, $user->id, [0 => 50.0], 0);
        $basetime = time();
        $this->mock_clock_with_frozen($basetime);
        $pastgrade->locktime = $basetime - DAYSECS;
        $pastgrade->update();
        // A past locktime must be materialised as locked before the repair runs, as it would be during
        // normal gradebook processing.
        grade_grade::check_locktime_all([$pastitem->id]);

        [, $futureitem, $futuregrade] = $this->create_corrupted_assignment_grade($course->id, $user->id, [0 => 50.0], 0);
        $basetime = time();
        $this->mock_clock_with_frozen($basetime);
        $futuregrade->locktime = $basetime + DAYSECS;
        $futuregrade->update('locktime');

        // One grade item can contain both locked and unlocked grades. Only unlocked grades
        // should have their rawgrade repaired, while locked grades must remain untouched.
        [, $gradeitem, $grade1] = $this->create_corrupted_assignment_grade($course->id, $user->id, [0 => 50.0], 0);
        $user2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        [, $gradeitem, $grade2] = $this->create_corrupted_assignment_grade($course->id, $user2->id, [0 => 60.0], 0, $gradeitem);

        $grade2->locked = time();
        $grade2->update();

        penalty_manager::repair_penalised_rawgrade($course->id);

        // The locked grade must not be modified.
        $this->assertEqualsWithDelta(85.0, (float)$lockeditem->get_grade($user->id)->rawgrade, 0.001);
        $this->assertEqualsWithDelta(175.0, (float)$lockeditem->get_grade($user->id)->finalgrade, 0.001);

        // The overridden grade must not be modified.
        $this->assertEqualsWithDelta(85.0, (float)$overriddenitem->get_grade($user->id)->rawgrade, 0.001);
        $this->assertEqualsWithDelta(175.0, (float)$overriddenitem->get_grade($user->id)->finalgrade, 0.001);

        // The past-locked finalgrade must not be modified.
        $this->assertEqualsWithDelta(85.0, (float)$pastitem->get_grade($user->id)->rawgrade, 0.001);
        $this->assertEqualsWithDelta(175.0, (float)$pastitem->get_grade($user->id)->finalgrade, 0.001);

        // The future-locked grade, so its rawgrade can be repaired.
        $this->assertEqualsWithDelta(50.0, (float)$futureitem->get_grade($user->id)->rawgrade, 0.001);
        $this->assertEquals(0, $futureitem->get_grade($user->id)->locked);

        // The unlocked grade is repaired and the grade item is marked for recalculation.
        $this->assertEqualsWithDelta(50.0, (float)$gradeitem->get_grade($user->id)->rawgrade, 0.001);
        $this->assertEquals(1, $DB->get_field('grade_items', 'needsupdate', ['id' => $gradeitem->id]));
        // The locked grade in the same grade item is not repaired.
        $this->assertEqualsWithDelta(85.0, (float)$gradeitem->get_grade($user2->id)->rawgrade, 0.001);
    }

     /**
      * Test that repair_penalised_rawgrade() repairs Gradebook grades for Assignments whose rawgrade still contains
      * the legacy pre-MDL-88407 representation, while leaving grades that cannot be safely repaired untouched.
      */
    public function test_repair_penalised_rawgrade(): void {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Case 1: Legacy-corrupted Assignment grade. Assignment's current grade is 25, while the
        // gradebook rawgrade contains the legacy penalised value of 85.
        $course1 = $this->getDataGenerator()->create_course();
        [, $item1, ] = $this->create_corrupted_assignment_grade(
            $course1->id,
            $user->id,
            [0 => 25.0],
            0
        );

        // Case 2: default multiplier/offset - not affected, must be left untouched.
        $course2 = $this->getDataGenerator()->create_course();
        [$item2, ] = $this->create_penalised_grade(
            courseid: $course2->id,
            userid: $user->id,
            multfactor: 1.0,
            plusfactor: 0.0,
            rawgrade: 50.0,
            deductedmark: 20.0,
            finalgrade: 30.0,
        );

        // Case 3: non-default multiplier/offset but no penalty deducted - not affected.
        $course3 = $this->getDataGenerator()->create_course();
        [$item3, ] = $this->create_penalised_grade(
            courseid: $course3->id,
            userid: $user->id,
            multfactor: 2.0,
            plusfactor: 5.0,
            rawgrade: 50.0,
            deductedmark: 0.0,
            finalgrade: 50.0,
        );

        // Case 4: The grade is locked and must not be repaired.
        $course4 = $this->getDataGenerator()->create_course();
        [$item4, ] = $this->create_penalised_grade(
            courseid: $course4->id,
            userid: $user->id,
            multfactor: 2.0,
            plusfactor: 5.0,
            rawgrade: 85.0,
            deductedmark: 20.0,
            finalgrade: 135.0,
            locked: true,
        );

        // Case 5: The grade is overridden and must not be repaired.
        $course5 = $this->getDataGenerator()->create_course();
        [$item5, ] = $this->create_penalised_grade(
            courseid: $course5->id,
            userid: $user->id,
            multfactor: 2.0,
            plusfactor: 5.0,
            rawgrade: 85.0,
            deductedmark: 20.0,
            finalgrade: 135.0,
            overridden: true,
        );

        // Case 6: The grade has no finalgrade, so it must be left untouched.
        $course6 = $this->getDataGenerator()->create_course();
        [$item6, ] = $this->create_penalised_grade(
            courseid: $course6->id,
            userid: $user->id,
            multfactor: 2.0,
            plusfactor: 5.0,
            rawgrade: 85.0,
            deductedmark: 20.0,
        );

        penalty_manager::repair_penalised_rawgrade();

        // The course-level item must also be marked for recalculation when one of its grade items
        // has been repaired.
        $courseitem1 = grade_item::fetch(['courseid' => $course1->id, 'itemtype' => 'course']);

        // Only case 1 is repaired, so its grade item and its course total should require recalculation.
        $items = $DB->get_records('grade_items', ['needsupdate' => 1], 'id ASC');
        $needsupdateitemids = array_keys($items);

        // Case 1: repaired from the legacy rawgrade of 85 to the original rawgrade of 25.
        $this->assertEqualsWithDelta(25.0, (float)grade_grade::fetch(['itemid' => $item1->id])->rawgrade, 0.001);

        // Cases 2 and 3: unaffected.
        $this->assertEqualsWithDelta(50.0, (float)grade_grade::fetch(['itemid' => $item2->id])->rawgrade, 0.001);
        $this->assertEqualsWithDelta(50.0, (float)grade_grade::fetch(['itemid' => $item3->id])->rawgrade, 0.001);

        // Cases 4 and 5: locked/overridden grades must not be changed.
        $this->assertEqualsWithDelta(85.0, (float)grade_grade::fetch(['itemid' => $item4->id])->rawgrade, 0.001);
        $this->assertEqualsWithDelta(85.0, (float)grade_grade::fetch(['itemid' => $item5->id])->rawgrade, 0.001);

        // Case 6: finalgrade is not present. The rawgrade should not be modified.
        $this->assertEqualsWithDelta(85.0, (float)grade_grade::fetch(['itemid' => $item6->id])->rawgrade, 0.001);

        // Running the repair for a single course must not modify grades in other courses.
        $course7 = $this->getDataGenerator()->create_course();
        [$item7, ] = $this->create_penalised_grade(
            courseid: $course7->id,
            userid: $user->id,
            multfactor: 3.0,
            plusfactor: 1.0,
            rawgrade: 106.0,
            deductedmark: 15.0,
            finalgrade: 100.0,
        );

        penalty_manager::repair_penalised_rawgrade($course1->id);
        $this->assertEqualsWithDelta(106.0, (float)grade_grade::fetch(['itemid' => $item7->id])->rawgrade, 0.001);
    }

    /**
     * A null Assignment grade must be treated as an ungraded attempt and skipped.
     */
    public function test_repair_penalised_rawgrade_skips_null_assignment_grade(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        [, $gradeitem, $grade] = $this->create_corrupted_assignment_grade($course->id, $user->id, [0 => 50.0], 0);

        // Simulate a grader saving feedback with no grade given, which stores a null assign_grades.grade.
        $DB->set_field('assign_grades', 'grade', null, ['id' => $DB->get_field(
            'assign_grades',
            'id',
            ['assignment' => $gradeitem->iteminstance, 'userid' => $user->id],
        )]);

        // Clear flags set automatically when creating the grade items so that we can verify that the
        // repair function marks only the items it actually modifies as needing an update.
        $DB->set_field('grade_items', 'needsupdate', 0, ['id' => $gradeitem->id]);

        penalty_manager::repair_penalised_rawgrade($course->id);

        // The legacy rawgrade and finalgrade must be left untouched.
        $unchanged = grade_grade::fetch(['id' => $grade->id]);
        $this->assertEqualsWithDelta(85.0, (float)$unchanged->rawgrade, 0.001);
        $this->assertEqualsWithDelta(175.0, (float)$unchanged->finalgrade, 0.001);
        $this->assertEquals(0, $DB->get_field('grade_items', 'needsupdate', ['id' => $gradeitem->id]));
    }

    /**
     * The repair is deliberately restricted to Assignment: {$modname}_get_user_grades() is an internal
     * convention followed by only a handful of core modules, not a formal inter-component contract, and
     * how an ungraded attempt is represented is left up to each module - so it cannot be safely
     * interpreted for an arbitrary one. A non-Assignment grade item with a deducted mark must therefore
     * be left untouched, even though mod_quiz happens to implement the same get_user_grades()
     * convention as Assignment.
     */
    public function test_repair_penalised_rawgrade_does_not_affect_non_assignment_modules(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $quiz = $this->getDataGenerator()->create_module('quiz', [
            'course' => $course->id,
            'grade' => 200,
        ]);

        $now = time();
        $DB->insert_record('quiz_attempts', (object) [
            'quiz' => $quiz->id,
            'userid' => $user->id,
            'attempt' => 1,
            'uniqueid' => 0,
            'layout' => '',
            'currentpage' => 0,
            'preview' => 0,
            'state' => 'finished',
            'timestart' => $now,
            'timefinish' => $now,
            'timemodified' => $now,
            'timemodifiedoffline' => 0,
        ]);
        // Add a Quiz grade to ensure the authoritative grade lookup does not use grades from other modules.
        $DB->insert_record('quiz_grades', (object) [
            'quiz' => $quiz->id,
            'userid' => $user->id,
            'grade' => 25,
            'timemodified' => $now,
        ]);

        $gradeitem = grade_item::fetch([
            'courseid' => $course->id,
            'itemtype' => 'mod',
            'itemmodule' => 'quiz',
            'iteminstance' => $quiz->id,
            'itemnumber' => 0,
        ]);
        $gradeitem->multfactor = 2.0;
        $gradeitem->plusfactor = 5.0;
        $gradeitem->update();

        // Deliberately write the legacy-corrupted state directly, as if the pre-fix calculation had
        // stored the penalised, factor-adjusted value of 85 as rawgrade.
        $grade = $gradeitem->get_grade($user->id, true);
        $grade->rawgrade = 85;
        $grade->deductedmark = 20;
        $grade->finalgrade = 175;
        $grade->update();

        $this->assertNull(penalty_manager::get_authoritative_user_grades($gradeitem));

        penalty_manager::repair_penalised_rawgrade($course->id);

        $unchanged = grade_grade::fetch(['id' => $grade->id]);
        $this->assertEqualsWithDelta(85.0, (float)$unchanged->rawgrade, 0.001);
    }

    /**
     * Helper to create a grade item and a single user's grade_grade with full control over the stored
     * rawgrade, deductedmark and finalgrade.
     *
     * @param int $courseid The course id.
     * @param int $userid The graded user id.
     * @param float $multfactor The grade item's multiplier.
     * @param float $plusfactor The grade item's offset.
     * @param float $rawgrade The stored rawgrade.
     * @param float $deductedmark The mark deducted from the grade as a penalty.
     * @param float|null $finalgrade The stored finalgrade, or null if not set.
     * @param int $locked Whether the grade is locked.
     * @param int $overridden Whether the grade is overridden.
     * @param int $locktime The time when the grade becomes locked.
     * @return array{0: grade_item, 1: grade_grade}
     */
    private function create_penalised_grade(
        int $courseid,
        int $userid,
        float $multfactor,
        float $plusfactor,
        float $rawgrade,
        float $deductedmark,
        ?float $finalgrade = null,
        int $locked = 0,
        int $overridden = 0,
        int $locktime = 0,
    ): array {
        $assign = $this->getDataGenerator()->create_module('assign', [
            'course' => $courseid,
            'grade' => 200,
        ]);

        $gradeitem = grade_item::fetch([
            'courseid' => $courseid,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $assign->id,
            'itemnumber' => 0,
        ]);
        $gradeitem->multfactor = $multfactor;
        $gradeitem->plusfactor = $plusfactor;
        $gradeitem->update();

        $grade = $gradeitem->get_grade($userid, true);
        $grade->rawgrade = $rawgrade;
        $grade->deductedmark = $deductedmark;
        $grade->finalgrade = $finalgrade;
        $grade->locked = $locked;
        $grade->overridden = $overridden;
        $grade->locktime = $locktime;
        $grade->update();

        return [$gradeitem, $grade];
    }

    /**
     * Creates an Assignment grade item with deliberately corrupted gradebook data.
     *
     * The Assignment grade data represents the authoritative grade, while the grade_grade record is
     * deliberately written with the legacy pre-MDL-88407 rawgrade/finalgrade representation. This
     * allows repair_penalised_rawgrade() to be tested against the Assignment grade.
     *
     * @param int $courseid The course id.
     * @param int $userid The graded user id.
     * @param array $attemptgrades Attempt number to Assignment grade, or null if no grade exists.
     * @param int $latestattempt The attempt selected by Assignment.
     * @param grade_item|null $existinggradeitem The existing grade item to reuse, if any.
     * @return array{0: stdClass, 1: grade_item, 2: grade_grade} The Assignment, grade item and grade record.
     */
    private function create_corrupted_assignment_grade(
        int $courseid,
        int $userid,
        array $attemptgrades,
        int $latestattempt,
        ?grade_item $existinggradeitem = null,
    ): array {
        global $DB;

        if ($existinggradeitem === null) {
            $assign = $this->getDataGenerator()->create_module(
                'assign',
                [
                    'course' => $courseid,
                    'grade' => 200,
                ]
            );

            grade_update(
                source: 'mod/assign',
                courseid: $courseid,
                itemtype: 'mod',
                itemmodule: 'assign',
                iteminstance: $assign->id,
                itemnumber: 0,
                grades: null,
                itemdetails: ['multfactor' => 2.0, 'plusfactor' => 5.0],
            );

            $gradeitem = grade_item::fetch([
                'courseid' => $courseid,
                'itemtype' => 'mod',
                'itemmodule' => 'assign',
                'iteminstance' => $assign->id,
                'itemnumber' => 0,
            ]);
        } else {
            $gradeitem = $existinggradeitem;
            $assign = $DB->get_record('assign', ['id' => $gradeitem->iteminstance], '*', MUST_EXIST);
        }

        $now = time();
        $grader = $this->getDataGenerator()->create_user();

        foreach ($attemptgrades as $attemptnumber => $attemptgrade) {
            $DB->insert_record('assign_submission', (object)[
                'assignment' => $assign->id,
                'userid' => $userid,
                'timecreated' => $now,
                'timemodified' => $now,
                'status' => ASSIGN_SUBMISSION_STATUS_SUBMITTED,
                'groupid' => 0,
                'attemptnumber' => $attemptnumber,
                'latest' => $attemptnumber === $latestattempt ? 1 : 0,
            ]);
            if ($attemptgrade !== null) {
                $DB->insert_record('assign_grades', (object)[
                    'assignment' => $assign->id,
                    'userid' => $userid,
                    'timecreated' => $now,
                    'timemodified' => $now,
                    'grader' => $grader->id,
                    'grade' => $attemptgrade,
                    'penalty' => 0,
                    'attemptnumber' => $attemptnumber,
                ]);
            }
        }

        // Create the grade_grade record, then deliberately write the legacy-corrupted state directly
        // so the test setup does not invoke normal penalty/recalculation logic.
        $grade = $gradeitem->get_grade($userid, true);
        $DB->update_record('grade_grades', (object)[
            'id' => $grade->id,
            'rawgrade' => 85,
            'deductedmark' => 20,
            'finalgrade' => 175,
        ]);

        $grade = grade_grade::fetch(['id' => $grade->id]);

        return [$assign, $gradeitem, $grade];
    }

    /**
     * Creates an Assignment grade item with a submitted, graded attempt, so it has an authoritative
     * grade that get_authoritative_user_grades() can check against the stored grade_grades.rawgrade.
     *
     * This only sets up Assignment's submission/grade records and the grade item's multiplier and offset.
     * It does not write anything to grade_grades itself. Callers that need to control the stored
     * rawgrade, deductedmark or finalgrade must set those separately.
     *
     * @param int $courseid The course id.
     * @param int $userid The graded user id.
     * @param float $grade The grade recorded in assign_grades for the submitted attempt.
     * @param float $multfactor The grade item's multiplier.
     * @param float $plusfactor The grade item's offset.
     * @return grade_item The Assignment's grade item.
     */
    private function create_assignment_grade_item(
        int $courseid,
        int $userid,
        float $grade = 50.0,
        float $multfactor = 2.0,
        float $plusfactor = 5.0,
    ): grade_item {
        global $DB;

        $assign = $this->getDataGenerator()->create_module('assign', [
            'course' => $courseid,
            'grade' => 200,
        ]);

        $now = time();
        $grader = $this->getDataGenerator()->create_user();

        $DB->insert_record('assign_submission', (object) [
            'assignment' => $assign->id,
            'userid' => $userid,
            'timecreated' => $now,
            'timemodified' => $now,
            'status' => ASSIGN_SUBMISSION_STATUS_SUBMITTED,
            'groupid' => 0,
            'attemptnumber' => 0,
            'latest' => 1,
        ]);

        $DB->insert_record('assign_grades', (object) [
            'assignment' => $assign->id,
            'userid' => $userid,
            'timecreated' => $now,
            'timemodified' => $now,
            'grader' => $grader->id,
            'grade' => $grade,
            'penalty' => 0,
            'attemptnumber' => 0,
        ]);

        $gradeitem = grade_item::fetch([
            'courseid' => $courseid,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $assign->id,
            'itemnumber' => 0,
        ]);

        $gradeitem->multfactor = $multfactor;
        $gradeitem->plusfactor = $plusfactor;
        $gradeitem->update();

        return $gradeitem;
    }
}
