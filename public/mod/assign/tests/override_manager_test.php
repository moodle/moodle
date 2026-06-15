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

use invalid_parameter_exception;
use mod_assign_override_test_trait;
use mod_assign_test_generator;
use mod_assign_testable_assign;
use PHPUnit\Framework\Attributes\CoversClass;
use required_capability_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/assign/tests/externallib_advanced_testcase.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');
require_once($CFG->dirroot . '/mod/assign/tests/mod_assign_override_test_trait.php');

/**
 * Test the override_manager class for assignments.
 *
 * @package    mod_assign
 * @category   test
 * @copyright  2025 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(override_manager::class)]
final class override_manager_test extends externallib_advanced_testcase {
    use mod_assign_test_generator;
    use mod_assign_override_test_trait;

    /**
     * Create an assignment with test data for override manager testing.
     *
     * @return array Array containing course, assignment, users, groups, context, and manager
     */
    private function create_test_data(): array {
        // Use shared trait method.
        $data = $this->create_assign_with_overrides_test_data();

        // Create override manager instance (specific to manager tests).
        $manager = new override_manager($data['assign'], $data['context']);
        $data['manager'] = $manager;

        return $data;
    }

    /**
     * Test manager constructor and basic properties.
     */
    public function test_constructor(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        $this->assertInstanceOf(override_manager::class, $manager);
        $this->assertEquals($data['context'], $manager->context);
    }

    /**
     * Test get_all_overrides returns empty array when no overrides exist.
     */
    public function test_get_all_overrides_empty(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        $overrides = $manager->get_all_overrides();
        $this->assertIsArray($overrides);
        $this->assertEmpty($overrides);
    }

    /**
     * Test get_all_overrides returns existing overrides.
     */
    public function test_get_all_overrides_with_data(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        // Create a user override.
        $override1 = new stdClass();
        $override1->assignid = $data['assign']->id;
        $override1->userid = $data['student1']->id;
        $override1->duedate = time() + (10 * DAYSECS);
        $override1id = $DB->insert_record('assign_overrides', $override1);

        // Create a group override.
        $override2 = new stdClass();
        $override2->assignid = $data['assign']->id;
        $override2->groupid = $data['group1']->id;
        $override2->duedate = time() + (8 * DAYSECS);
        $override2->sortorder = 1;
        $override2id = $DB->insert_record('assign_overrides', $override2);

        $overrides = $manager->get_all_overrides();
        $this->assertCount(2, $overrides);

        // Check override ids.
        $overrideids = array_keys($overrides);
        $this->assertContains($override1id, $overrideids);
        $this->assertContains($override2id, $overrideids);
    }

    /**
     * Test get_accessible_overrides filters by user access.
     */
    public function test_get_accessible_overrides(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Create overrides.
        $override1 = new stdClass();
        $override1->assignid = $data['assign']->id;
        $override1->userid = $data['student1']->id;
        $override1->duedate = time() + (10 * DAYSECS);
        $override1->id = $DB->insert_record('assign_overrides', $override1);

        $override2 = new stdClass();
        $override2->assignid = $data['assign']->id;
        $override2->groupid = $data['group1']->id;
        $override2->duedate = time() + (8 * DAYSECS);
        $override2->sortorder = 1;
        $override2->id = $DB->insert_record('assign_overrides', $override2);

        $accessibleoverrides = $manager->get_accessible_overrides();
        $this->assertIsArray($accessibleoverrides);
        // Teacher should be able to see all overrides.
        $this->assertCount(2, $accessibleoverrides);
    }

    /**
     * Test validate_data with valid user override data.
     */
    public function test_validate_data_valid_user_override(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        $now = time();
        $formdata = [
            'userid' => $data['student1']->id,
            'duedate' => $now + (10 * DAYSECS),
            'cutoffdate' => $now + (17 * DAYSECS),
        ];

        $errors = $manager->validate_data($formdata);
        $this->assertEmpty($errors);
    }

    /**
     * Test validate_data with valid group override data.
     */
    public function test_validate_data_valid_group_override(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        $now = time();
        $formdata = [
            'groupid' => $data['group1']->id,
            'duedate' => $now + (8 * DAYSECS),
            'cutoffdate' => $now + (15 * DAYSECS),
        ];

        $errors = $manager->validate_data($formdata);
        $this->assertEmpty($errors);
    }

    /**
     * Test validate_data requires either userid or groupid.
     */
    public function test_validate_data_requires_user_or_group(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        $formdata = [
            'duedate' => time() + (10 * DAYSECS),
        ];

        $errors = $manager->validate_data($formdata);
        $this->assertArrayHasKey('general', $errors);
    }

    /**
     * Test validate_data prevents both userid and groupid.
     */
    public function test_validate_data_prevents_both_user_and_group(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        $formdata = [
            'userid' => $data['student1']->id,
            'groupid' => $data['group1']->id,
            'duedate' => time() + (10 * DAYSECS),
        ];

        $errors = $manager->validate_data($formdata);
        $this->assertArrayHasKey('general', $errors);
    }

    /**
     * Test validate_data validates date ordering.
     */
    public function test_validate_data_validates_date_order(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        // Try cutoff before due date.
        $formdata = [
            'userid' => $data['student1']->id,
            'duedate' => time() + (10 * DAYSECS),
            'cutoffdate' => time() + (5 * DAYSECS),
        ];

        $errors = $manager->validate_data($formdata);
        $this->assertArrayHasKey('cutoffdate', $errors);
    }

    /**
     * Test validate_data with invalid user ID.
     */
    public function test_validate_data_invalid_user(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        $formdata = [
            'userid' => 99999, // Non-existent user.
            'duedate' => time() + (10 * DAYSECS),
        ];

        $errors = $manager->validate_data($formdata);
        $this->assertArrayHasKey('userid', $errors);
    }

    /**
     * Test validate_data with invalid group ID.
     */
    public function test_validate_data_invalid_group(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        $formdata = [
            'groupid' => 99999, // Non-existent group.
            'duedate' => time() + (10 * DAYSECS),
        ];

        $errors = $manager->validate_data($formdata);
        $this->assertArrayHasKey('groupid', $errors);
    }

    /**
     * Test validate_data respects extension dates for user override.
     */
    public function test_validate_data_respects_extension_dates(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        // Set an extension date for student1.
        $userflags = new stdClass();
        $userflags->assignment = $data['assign']->id;
        $userflags->userid = $data['student1']->id;
        $userflags->extensionduedate = time() + (5 * DAYSECS);
        $DB->insert_record('assign_user_flags', $userflags);

        // Try to create override with due date after extension.
        $formdata = [
            'userid' => $data['student1']->id,
            'duedate' => time() + (10 * DAYSECS), // After extension.
        ];

        $errors = $manager->validate_data($formdata);
        $this->assertArrayHasKey('duedate', $errors);
    }

    /**
     * Test validate_data requires at least one overrideable setting.
     */
    public function test_validate_data_requires_override_data(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        $formdata = [
            'userid' => $data['student1']->id,
            // No overrideable settings provided.
        ];

        $errors = $manager->validate_data($formdata);
        $this->assertArrayHasKey('general', $errors);
    }

    /**
     * Test save_overrides creates a user override.
     */
    public function test_save_overrides_create_user_override(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        $duedate = time() + (10 * DAYSECS);
        $overridesdata = [[
            'userid' => $data['student1']->id,
            'duedate' => $duedate,
        ]];

        $ids = $manager->save_overrides($overridesdata);
        $this->assertCount(1, $ids);
        $this->assertIsInt($ids[0]);

        // Verify in database.
        $override = $DB->get_record('assign_overrides', ['id' => $ids[0]]);
        $this->assertNotFalse($override);
        $this->assertEquals($data['student1']->id, $override->userid);
        $this->assertEquals($duedate, $override->duedate);
    }

    /**
     * Test save_overrides creates a group override.
     */
    public function test_save_overrides_create_group_override(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        $duedate = time() + (8 * DAYSECS);
        $overridesdata = [[
            'groupid' => $data['group1']->id,
            'duedate' => $duedate,
        ]];

        $ids = $manager->save_overrides($overridesdata);
        $this->assertCount(1, $ids);

        // Verify in database.
        $override = $DB->get_record('assign_overrides', ['id' => $ids[0]]);
        $this->assertNotFalse($override);
        $this->assertEquals($data['group1']->id, $override->groupid);
        $this->assertEquals($duedate, $override->duedate);
        $this->assertNotNull($override->sortorder);
    }

    /**
     * Test save_overrides with grade recalculation.
     */
    public function test_save_overrides_with_recalculation(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Set up late submission with penalty.
        $this->setup_late_submission_with_penalty($data, $data['student1']);

        // Verify penalty was applied by checking final grade.
        $course = $DB->get_record('course', ['id' => $data['assign']->course]);
        $initialfinalgrade = $this->get_final_grade(
            $data['assign']->id,
            $course->id,
            $data['student1']->id
        );
        $this->assertEquals(90, $initialfinalgrade); // 100 - 10% penalty = 90.

        // Return to teacher to save override.
        $this->setUser($data['teacher']);

        // Save override with recalculate flag.
        // The override moves due date to the future (after submission), so penalty should be removed.
        $overridesdata = [[
            'userid' => $data['student1']->id,
            'duedate' => time() + (10 * DAYSECS), // Due date now in future.
        ]];

        $ids = $manager->save_overrides($overridesdata, true);
        // Debug messages should be logged for recalculation.
        $this->assertDebuggingCalledCount(2);
        $this->assertCount(1, $ids);

        // Verify penalty was REMOVED after recalculation.
        // Since due date is now after submission date, the submission is no longer late.
        $updatedfinalgrade = $this->get_final_grade(
            $data['assign']->id,
            $course->id,
            $data['student1']->id
        );
        $this->assertEquals(100, $updatedfinalgrade); // Penalty removed: 90 → 100.
    }

    /**
     * Test save_overrides updates existing override.
     */
    public function test_save_overrides_update_existing(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Create initial override.
        $initialdata = [[
            'userid' => $data['student1']->id,
            'duedate' => time() + (10 * DAYSECS),
        ]];

        $ids = $manager->save_overrides($initialdata);
        $overrideid = $ids[0];

        // Update the override.
        $newduedate = time() + (12 * DAYSECS);
        $updatedata = [[
            'id' => $overrideid,
            'userid' => $data['student1']->id,
            'duedate' => $newduedate,
        ]];

        $updateids = $manager->save_overrides($updatedata);
        $this->assertEquals($overrideid, $updateids[0]);

        // Verify update.
        $updated = $DB->get_record('assign_overrides', ['id' => $overrideid]);
        $this->assertEquals($newduedate, $updated->duedate);
    }

    /**
     * Test save_overrides merge with existing override.
     */
    public function test_save_overrides_merges_with_existing_same_user(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Create initial override with cutoffdate.
        $manager->save_overrides([[
            'userid' => $data['student1']->id,
            'cutoffdate' => time() + (20 * DAYSECS),
        ]]);

        // Create a new override for the same user with only duedate.
        // Should merge and delete the old one.
        $ids = $manager->save_overrides([[
            'userid' => $data['student1']->id,
            'duedate' => time() + (10 * DAYSECS),
        ]]);

        // Should have exactly 1 override (old deleted, new contains merged values).
        $overrides = $DB->get_records('assign_overrides', ['assignid' => $data['assign']->id]);
        $this->assertCount(1, $overrides);

        $override = reset($overrides);
        $this->assertEquals($ids[0], $override->id);
        $this->assertNotNull($override->cutoffdate); // Merged from old override.
        $this->assertNotNull($override->duedate);
    }

    /**
     * Test delete_overrides_by_id removes overrides.
     */
    public function test_delete_overrides_by_id(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Create override.
        $overridesdata = [[
            'userid' => $data['student1']->id,
            'duedate' => time() + (10 * DAYSECS),
        ]];

        $ids = $manager->save_overrides($overridesdata);
        $overrideid = $ids[0];

        // Verify it exists.
        $this->assertTrue($DB->record_exists('assign_overrides', ['id' => $overrideid]));

        // Delete it.
        $manager->delete_overrides_by_id([$overrideid]);

        // Verify it's gone.
        $this->assertFalse($DB->record_exists('assign_overrides', ['id' => $overrideid]));
    }

    /**
     * Test delete_overrides_by_id with recalculation.
     */
    public function test_delete_overrides_by_id_with_recalculation(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Enable assignment penalty system.
        $this->enable_assign_penalty($data['assign']);

        // Set times for late submission scenario.
        $duedate = time() - (2 * DAYSECS); // Due date 2 days ago.
        $submissiontime = time() - DAYSECS; // Submitted 1 day ago (1 day AFTER due date = late).

        // Update assignment due date.
        $data['assign']->duedate = $duedate;
        $DB->update_record('assign', $data['assign']);

        // Create an override that extends the due date.
        $overridesdata = [[
            'userid' => $data['student1']->id,
            'duedate' => time() + (10 * DAYSECS), // Future date.
        ]];

        $ids = $manager->save_overrides($overridesdata);
        $overrideid = $ids[0];

        // Create testable assign instance.
        $course = $DB->get_record('course', ['id' => $data['assign']->course]);
        $assign = new mod_assign_testable_assign($data['context'], $data['cm'], $course);

        // Add submission and grade using proper methods.
        $this->add_submission($data['student1'], $assign, 'Sample text');
        $this->submit_for_grading($data['student1'], $assign);

        // Set submission time to be late (AFTER the assignment due date, but before override due date).
        $DB->set_field('assign_submission', 'timemodified', $submissiontime, [
            'assignment' => $data['assign']->id,
            'userid' => $data['student1']->id,
        ]);

        // Apply grade using testable method - penalty should be calculated here.
        $assign->testable_apply_grade_to_user((object)['grade' => 100], $data['student1']->id, 0);

        // Penalty system should have logged debug messages.
        $this->assertDebuggingCalledCount(2);

        // Verify NO penalty applied initially (override makes due date future).
        // With the override active, due date is in the future, so submission is not late.
        $initialfinalgrade = $this->get_final_grade(
            $data['assign']->id,
            $course->id,
            $data['student1']->id
        );
        $this->assertEquals(100, $initialfinalgrade); // No penalty with override active.

        // Return to teacher to delete override.
        $this->setUser($data['teacher']);

        // Delete override with recalculation.
        // After deletion, due date reverts to assignment default (2 days ago), making submission late.
        $manager->delete_overrides_by_id([$overrideid], true, true);

        // Debug messages should be logged for recalculation.
        $this->assertDebuggingCalledCount(2);

        // Verify override is deleted.
        $this->assertFalse($DB->record_exists('assign_overrides', ['id' => $overrideid]));

        // Verify penalty is NOW applied after recalculation.
        // With override deleted, due date is in past, so 10% penalty is applied.
        $updatedfinalgrade = $this->get_final_grade(
            $data['assign']->id,
            $course->id,
            $data['student1']->id
        );
        $this->assertEquals(90, $updatedfinalgrade); // Penalty applied: 100 → 90.
    }

    /**
     * Test delete_all_overrides removes all overrides.
     */
    public function test_delete_all_overrides(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Create multiple overrides.
        $overridesdata = [
            [
                'userid' => $data['student1']->id,
                'duedate' => time() + (10 * DAYSECS),
            ],
            [
                'userid' => $data['student2']->id,
                'duedate' => time() + (11 * DAYSECS),
            ],
            [
                'groupid' => $data['group1']->id,
                'duedate' => time() + (9 * DAYSECS),
            ],
        ];

        $manager->save_overrides($overridesdata);

        // Verify they exist.
        $overrides = $DB->get_records('assign_overrides', ['assignid' => $data['assign']->id]);
        $this->assertCount(3, $overrides);

        // Delete all.
        $manager->delete_all_overrides();

        // Verify all gone.
        $overrides = $DB->get_records('assign_overrides', ['assignid' => $data['assign']->id]);
        $this->assertEmpty($overrides);
    }

    /**
     * Test require_manage_capability throws exception for unauthorized user.
     */
    public function test_require_manage_capability_unauthorized(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['student1']);

        $this->expectException(required_capability_exception::class);
        $manager->require_manage_capability();
    }

    /**
     * Test require_manage_capability passes for authorized user.
     */
    public function test_require_manage_capability_authorized(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Should not throw exception.
        $manager->require_manage_capability();
        $this->assertTrue(true); // If we get here, test passed.
    }

    /**
     * Test delete_orphaned_group_overrides removes orphaned overrides.
     */
    public function test_delete_orphaned_group_overrides(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        // Create a group override.
        $override = new stdClass();
        $override->assignid = $data['assign']->id;
        $override->groupid = $data['group1']->id;
        $override->duedate = time() + (8 * DAYSECS);
        $override->sortorder = 1;
        $overrideid = $DB->insert_record('assign_overrides', $override);

        // Delete the group to make override orphaned.
        $DB->delete_records('groups', ['id' => $data['group1']->id]);

        // Clean up orphaned overrides.
        $deleted = $manager->delete_orphaned_group_overrides();
        $this->assertEquals(1, $deleted);

        // Verify override is gone.
        $this->assertFalse($DB->record_exists('assign_overrides', ['id' => $overrideid]));
    }

    /**
     * Test move_group_override functionality.
     */
    public function test_move_group_override(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Create two group overrides.
        $override1data = [[
            'groupid' => $data['group1']->id,
            'duedate' => time() + (8 * DAYSECS),
        ]];
        $override2data = [[
            'groupid' => $data['group2']->id,
            'duedate' => time() + (9 * DAYSECS),
        ]];

        $ids1 = $manager->save_overrides($override1data);
        $ids2 = $manager->save_overrides($override2data);

        $override1id = $ids1[0];
        $override2id = $ids2[0];

        // Get initial sort orders.
        $override1 = $DB->get_record('assign_overrides', ['id' => $override1id]);
        $override2 = $DB->get_record('assign_overrides', ['id' => $override2id]);

        $initialorder1 = $override1->sortorder;
        $initialorder2 = $override2->sortorder;

        // Move first override down.
        $result = $manager->move_group_override($override1id, 'down');
        $this->assertTrue($result);

        // Verify orders swapped.
        $override1after = $DB->get_record('assign_overrides', ['id' => $override1id]);
        $override2after = $DB->get_record('assign_overrides', ['id' => $override2id]);

        $this->assertEquals($initialorder2, $override1after->sortorder);
        $this->assertEquals($initialorder1, $override2after->sortorder);
    }

    /**
     * Test move_group_override with invalid direction.
     */
    public function test_move_group_override_invalid_direction(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        $this->expectException(invalid_parameter_exception::class);
        $manager->move_group_override(1, 'invalid');
    }

    /**
     * Test get_group_overrides_for_listing.
     */
    public function test_get_group_overrides_for_listing(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Create group override.
        $overridesdata = [[
            'groupid' => $data['group1']->id,
            'duedate' => time() + (8 * DAYSECS),
        ]];

        $manager->save_overrides($overridesdata);

        $groups = [$data['group1']->id => $data['group1']];
        $listing = $manager->get_group_overrides_for_listing($groups);

        $this->assertCount(1, $listing);
        $override = reset($listing);
        $this->assertEquals($data['group1']->id, $override->groupid);
        $this->assertEquals($data['group1']->name, $override->name);
    }

    /**
     * Test get_user_overrides_for_listing with access all groups.
     */
    public function test_get_user_overrides_for_listing_access_all(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Create user override.
        $overridesdata = [[
            'userid' => $data['student1']->id,
            'duedate' => time() + (10 * DAYSECS),
        ]];

        $manager->save_overrides($overridesdata);

        $listing = $manager->get_user_overrides_for_listing(true, []);

        $this->assertCount(1, $listing);
        $override = reset($listing);
        $this->assertEquals($data['student1']->id, $override->userid);
    }

    /**
     * Test reorder_group_overrides.
     */
    public function test_reorder_group_overrides(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Create multiple group overrides.
        $override1data = [['groupid' => $data['group1']->id, 'duedate' => time() + (8 * DAYSECS)]];
        $override2data = [['groupid' => $data['group2']->id, 'duedate' => time() + (9 * DAYSECS)]];

        $manager->save_overrides($override1data);
        $manager->save_overrides($override2data);

        // Manually mess up sort orders.
        $overrides = $DB->get_records('assign_overrides', ['assignid' => $data['assign']->id]);
        foreach ($overrides as $override) {
            $override->sortorder = 5;
            $DB->update_record('assign_overrides', $override);
        }

        // Reorder.
        $manager->reorder_group_overrides();

        // Verify orders are sequential.
        $overrides = $DB->get_records('assign_overrides', ['assignid' => $data['assign']->id], 'sortorder ASC');
        $orders = array_column(array_values($overrides), 'sortorder');
        $this->assertEquals([1, 2], $orders);
    }

    /**
     * Test save_overrides with multiple overrides in single call.
     */
    public function test_save_overrides_multiple_at_once(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Create multiple overrides at once.
        $overridesdata = [
            [
                'userid' => $data['student1']->id,
                'duedate' => time() + (10 * DAYSECS),
            ],
            [
                'userid' => $data['student2']->id,
                'duedate' => time() + (11 * DAYSECS),
            ],
            [
                'groupid' => $data['group1']->id,
                'duedate' => time() + (9 * DAYSECS),
            ],
        ];

        $ids = $manager->save_overrides($overridesdata);
        $this->assertCount(3, $ids);

        // Verify all exist in database.
        foreach ($ids as $id) {
            $this->assertTrue($DB->record_exists('assign_overrides', ['id' => $id]));
        }
    }

    /**
     * Test validate_data with zero/null date values.
     */
    public function test_validate_data_with_null_dates(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];

        // Test with null duedate (should be valid - removes override).
        $formdata = [
            'userid' => $data['student1']->id,
            'duedate' => 0,
            'cutoffdate' => time() + (10 * DAYSECS),
        ];

        $errors = $manager->validate_data($formdata);
        $this->assertEmpty($errors);
    }

    /**
     * Test move_group_override attempting to move first item up.
     */
    public function test_move_group_override_first_item_up(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Create two group overrides.
        $override1data = [[
            'groupid' => $data['group1']->id,
            'duedate' => time() + (8 * DAYSECS),
        ]];
        $override2data = [[
            'groupid' => $data['group2']->id,
            'duedate' => time() + (9 * DAYSECS),
        ]];

        $ids1 = $manager->save_overrides($override1data);
        $manager->save_overrides($override2data);

        $override1id = $ids1[0];
        $override1 = $DB->get_record('assign_overrides', ['id' => $override1id]);
        $initialsortorder = $override1->sortorder;

        // Try to move first override up (should fail).
        $result = $manager->move_group_override($override1id, 'up');
        $this->assertFalse($result);

        // Verify sort order unchanged.
        $override1after = $DB->get_record('assign_overrides', ['id' => $override1id]);
        $this->assertEquals($initialsortorder, $override1after->sortorder);
    }

    /**
     * Test move_group_override attempting to move last item down.
     */
    public function test_move_group_override_last_item_down(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Create two group overrides.
        $override1data = [[
            'groupid' => $data['group1']->id,
            'duedate' => time() + (8 * DAYSECS),
        ]];
        $override2data = [[
            'groupid' => $data['group2']->id,
            'duedate' => time() + (9 * DAYSECS),
        ]];

        $manager->save_overrides($override1data);
        $ids2 = $manager->save_overrides($override2data);

        $override2id = $ids2[0];
        $override2 = $DB->get_record('assign_overrides', ['id' => $override2id]);
        $initialsortorder = $override2->sortorder;

        // Try to move last override down (should fail).
        $result = $manager->move_group_override($override2id, 'down');
        $this->assertFalse($result);

        // Verify sort order unchanged.
        $override2after = $DB->get_record('assign_overrides', ['id' => $override2id]);
        $this->assertEquals($initialsortorder, $override2after->sortorder);
    }

    /**
     * Test delete_overrides_by_id with empty array.
     */
    public function test_delete_overrides_by_id_empty_array(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Should not throw exception with empty array.
        $manager->delete_overrides_by_id([]);
        $this->assertTrue(true); // Test passes if no exception.
    }

    /**
     * Test get_user_overrides_for_listing with group restrictions.
     */
    public function test_get_user_overrides_for_listing_with_group_filter(): void {
        $this->resetAfterTest();

        $data = $this->create_test_data();
        $manager = $data['manager'];
        $this->setUser($data['teacher']);

        // Create user overrides for students in different groups.
        $overridesdata = [
            [
                'userid' => $data['student1']->id, // In group1.
                'duedate' => time() + (10 * DAYSECS),
            ],
            [
                'userid' => $data['student3']->id, // In group2.
                'duedate' => time() + (11 * DAYSECS),
            ],
        ];

        $manager->save_overrides($overridesdata);

        // Get overrides for only group1.
        $groups = [$data['group1']->id => $data['group1']];
        $listing = $manager->get_user_overrides_for_listing(false, $groups);

        // Should only return student1 (who is in group1).
        $this->assertCount(1, $listing);
        $override = reset($listing);
        $this->assertEquals($data['student1']->id, $override->userid);
    }
}
