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

namespace mod_assign\external;

use core_external\external_api;
use dml_missing_record_exception;
use invalid_parameter_exception;
use mod_assign_override_test_trait;
use mod_assign\externallib_advanced_testcase;
use mod_assign_test_generator;
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
 * Test the override webservices for assignments.
 *
 * @package    mod_assign
 * @category   test
 * @copyright  2025 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(get_overrides::class)]
#[CoversClass(save_overrides::class)]
#[CoversClass(delete_overrides::class)]
final class overrides_test extends externallib_advanced_testcase {
    use mod_assign_test_generator;
    use mod_assign_override_test_trait;

    /**
     * Test get_overrides with no overrides.
     */
    public function test_get_overrides_empty(): void {
        $this->resetAfterTest();
        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $result = get_overrides::execute($data['assign']->id);
        $result = external_api::clean_returnvalue(get_overrides::execute_returns(), $result);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('overrides', $result);
        $this->assertCount(0, $result['overrides']);
    }

    /**
     * Test get_overrides returns existing overrides.
     */
    public function test_get_overrides_with_data(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $now = time();

        // Create a user override.
        $override1 = new stdClass();
        $override1->assignid = $data['assign']->id;
        $override1->userid = $data['student1']->id;
        $override1->duedate = $now + (10 * DAYSECS);
        $override1->cutoffdate = $now + (17 * DAYSECS);
        $override1->id = $DB->insert_record('assign_overrides', $override1);

        // Create a group override.
        $override2 = new stdClass();
        $override2->assignid = $data['assign']->id;
        $override2->groupid = $data['group1']->id;
        $override2->duedate = $now + (8 * DAYSECS);
        $override2->sortorder = 1;
        $override2->id = $DB->insert_record('assign_overrides', $override2);

        $result = get_overrides::execute($data['assign']->id);
        $result = external_api::clean_returnvalue(get_overrides::execute_returns(), $result);

        $this->assertCount(2, $result['overrides']);

        // Check user override.
        $useroverrides = array_values(array_filter($result['overrides'], function ($o) use ($data) {
            return isset($o['userid']) && $o['userid'] == $data['student1']->id;
        }));
        $this->assertNotEmpty($useroverrides, 'User override for student1 not found');
        $useroverride = $useroverrides[0];
        $this->assertEquals($data['student1']->id, $useroverride['userid']);
        $this->assertEquals($override1->duedate, $useroverride['duedate']);

        // Check group override.
        $groupoverrides = array_values(array_filter($result['overrides'], function ($o) use ($data) {
            return isset($o['groupid']) && $o['groupid'] == $data['group1']->id;
        }));
        $this->assertNotEmpty($groupoverrides, 'Group override for group1 not found');
        $groupoverride = $groupoverrides[0];
        $this->assertEquals($data['group1']->id, $groupoverride['groupid']);
        $this->assertEquals($override2->duedate, $groupoverride['duedate']);
    }

    /**
     * Test get_overrides returns reason and reasonformat fields (FORMAT_HTML pass-through).
     */
    public function test_get_overrides_returns_reason_and_reasonformat(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $now = time();
        $reason = 'Medical exemption';
        $reasonformat = FORMAT_HTML;

        // Create a user override with reason and reasonformat.
        $override = new stdClass();
        $override->assignid = $data['assign']->id;
        $override->userid = $data['student1']->id;
        $override->duedate = $now + (10 * DAYSECS);
        $override->reason = $reason;
        $override->reasonformat = $reasonformat;
        $override->id = $DB->insert_record('assign_overrides', $override);

        $result = get_overrides::execute($data['assign']->id);
        $result = external_api::clean_returnvalue(get_overrides::execute_returns(), $result);

        $this->assertCount(1, $result['overrides']);
        $returnedoverride = $result['overrides'][0];
        // Plain text stored as FORMAT_HTML passes through format_text unchanged.
        $this->assertEquals($reason, $returnedoverride['reason']);
        // Function format_text always normalises the returned format to FORMAT_HTML.
        $this->assertEquals(FORMAT_HTML, $returnedoverride['reasonformat']);
    }

    /**
     * Test get_overrides applies \core_external\util::format_text to reason/reasonformat.
     *
     * Verifies that non-HTML formats are converted: the returned reasonformat is always
     * FORMAT_HTML, and the text is rendered (e.g. Markdown bold becomes <strong>).
     */
    public function test_get_overrides_formats_reason_text(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $now = time();
        // Store the reason using Markdown format.
        $reason = 'Medical **exemption**';
        $reasonformat = FORMAT_MARKDOWN;

        // Create a user override with a Markdown-formatted reason.
        $override = new stdClass();
        $override->assignid = $data['assign']->id;
        $override->userid = $data['student1']->id;
        $override->duedate = $now + (10 * DAYSECS);
        $override->reason = $reason;
        $override->reasonformat = $reasonformat;
        $override->id = $DB->insert_record('assign_overrides', $override);

        $result = get_overrides::execute($data['assign']->id);
        $result = external_api::clean_returnvalue(get_overrides::execute_returns(), $result);

        $this->assertCount(1, $result['overrides']);
        $returnedoverride = $result['overrides'][0];

        // Function format_text must have run: the format is always normalised to FORMAT_HTML on output.
        $this->assertEquals(FORMAT_HTML, $returnedoverride['reasonformat']);

        // The Markdown bold syntax (**exemption**) must have been converted to HTML <strong>.
        $this->assertStringContainsString('<strong>exemption</strong>', $returnedoverride['reason']);
    }

    /**
     * Test get_overrides handles null reason without error.
     */
    public function test_get_overrides_null_reason_is_not_formatted(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        // Create an override with no reason set.
        $override = new stdClass();
        $override->assignid = $data['assign']->id;
        $override->userid = $data['student1']->id;
        $override->duedate = time() + (10 * DAYSECS);
        $override->id = $DB->insert_record('assign_overrides', $override);

        $result = get_overrides::execute($data['assign']->id);
        $result = external_api::clean_returnvalue(get_overrides::execute_returns(), $result);

        $this->assertCount(1, $result['overrides']);
        $returnedoverride = $result['overrides'][0];
        // When reason is null, format_text is skipped and null is returned as-is.
        $this->assertNull($returnedoverride['reason']);
    }

    /**
     * Test get_overrides requires capability.
     */
    public function test_get_overrides_requires_capability(): void {
        $this->resetAfterTest();
        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['student1']);

        $this->expectException(required_capability_exception::class);
        get_overrides::execute($data['assign']->id);
    }

    /**
     * Test get_overrides with invalid assignment ID.
     */
    public function test_get_overrides_invalid_assign(): void {
        $this->resetAfterTest();
        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $this->expectException(dml_missing_record_exception::class);
        get_overrides::execute(99999);
    }

    /**
     * Test save_overrides creates a user override.
     */
    public function test_save_overrides_create_user_override(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $now = time();
        $duedate = $now + (10 * DAYSECS);
        $cutoffdate = $now + (17 * DAYSECS);

        $result = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => $duedate,
                'cutoffdate' => $cutoffdate,
            ]],
        ]);
        $result = external_api::clean_returnvalue(save_overrides::execute_returns(), $result);

        $this->assertCount(1, $result['ids']);

        // Verify in database.
        $override = $DB->get_record('assign_overrides', ['id' => $result['ids'][0]]);
        $this->assertNotFalse($override);
        $this->assertEquals($data['student1']->id, $override->userid);
        $this->assertEquals($duedate, $override->duedate);
        $this->assertEquals($cutoffdate, $override->cutoffdate);
    }

    /**
     * Test save_overrides stores reason and reasonformat.
     */
    public function test_save_overrides_with_reason_and_reasonformat(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $reason = 'Medical exemption';
        $reasonformat = FORMAT_HTML;

        $result = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => time() + (10 * DAYSECS),
                'reason' => $reason,
                'reasonformat' => $reasonformat,
            ]],
        ]);
        $result = external_api::clean_returnvalue(save_overrides::execute_returns(), $result);

        $this->assertCount(1, $result['ids']);

        // Verify reason and reasonformat are stored in database.
        $override = $DB->get_record('assign_overrides', ['id' => $result['ids'][0]]);
        $this->assertNotFalse($override);
        $this->assertEquals($reason, $override->reason);
        $this->assertEquals($reasonformat, $override->reasonformat);
    }

    /**
     * Test save_overrides creates a group override.
     */
    public function test_save_overrides_create_group_override(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $now = time();
        $duedate = $now + (8 * DAYSECS);
        $cutoffdate = $now + (15 * DAYSECS);

        $result = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'groupid' => $data['group1']->id,
                'duedate' => $duedate,
                'cutoffdate' => $cutoffdate,
            ]],
        ]);
        $result = external_api::clean_returnvalue(save_overrides::execute_returns(), $result);

        $this->assertCount(1, $result['ids']);

        // Verify in database.
        $override = $DB->get_record('assign_overrides', ['id' => $result['ids'][0]]);
        $this->assertNotFalse($override);
        $this->assertEquals($data['group1']->id, $override->groupid);
        $this->assertEquals($duedate, $override->duedate);
        $this->assertNotNull($override->sortorder);
    }

    /**
     * Test save_overrides updates an existing override.
     */
    public function test_save_overrides_update_existing(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $now = time();

        // Create initial override.
        $override = new stdClass();
        $override->assignid = $data['assign']->id;
        $override->userid = $data['student1']->id;
        $override->duedate = $now + (10 * DAYSECS);
        $override->id = $DB->insert_record('assign_overrides', $override);

        // Check if the override exists.
        $initialoverride = $DB->get_record('assign_overrides', ['id' => $override->id]);
        $this->assertEquals($override->duedate, $initialoverride->duedate);

        // Update it.
        $newduedate = $now + (12 * DAYSECS);
        $result = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'id' => $override->id,
                'userid' => $data['student1']->id,
                'duedate' => $newduedate,
            ]],
        ]);
        $result = external_api::clean_returnvalue(save_overrides::execute_returns(), $result);

        $this->assertEquals($override->id, $result['ids'][0]);

        // Verify update in database.
        $updated = $DB->get_record('assign_overrides', ['id' => $override->id]);
        $this->assertEquals($newduedate, $updated->duedate);
    }

    /**
     * Test save_overrides merge with existing override.
     */
    public function test_save_overrides_merges_with_existing_same_user(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);
        $now = time();

        // Create initial override.
        $newduedate = $now + (12 * DAYSECS);
        $cutoffdate = $newduedate + DAYSECS;
        $result = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => $newduedate,
                'cutoffdate' => $cutoffdate,
            ]],
        ]);
        $result = external_api::clean_returnvalue(save_overrides::execute_returns(), $result);

        $this->assertCount(1, $result['ids']);

        // Verify update in database.
        $oldid = $result['ids'][0];
        $updated = $DB->get_record('assign_overrides', ['id' => $oldid]);
        $this->assertEquals($newduedate, $updated->duedate);
        $this->assertEquals($cutoffdate, $updated->cutoffdate);

        // Create new override without cutoffdate.
        $newduedate = $newduedate + DAYSECS;
        $result = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => $newduedate,
            ]],
        ]);
        $result = external_api::clean_returnvalue(save_overrides::execute_returns(), $result);

        $this->assertCount(1, $result['ids']);

        // Verify new override in database.
        $newid = $result['ids'][0];
        // The id should be different.
        $this->assertNotEquals($oldid, $newid);
        $updated = $DB->get_record('assign_overrides', ['id' => $newid]);
        $this->assertEquals($newduedate, $updated->duedate);
        // Cutoffdate should be merged with existing one.
        $this->assertEquals($cutoffdate, $updated->cutoffdate);
    }

    /**
     * Test save_overrides with batch creation.
     */
    public function test_save_overrides_batch_create(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $now = time();

        $result = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [
                [
                    'userid' => $data['student1']->id,
                    'duedate' => $now + (10 * DAYSECS),
                ],
                [
                    'userid' => $data['student2']->id,
                    'duedate' => $now + (11 * DAYSECS),
                ],
                [
                    'groupid' => $data['group1']->id,
                    'duedate' => $now + (9 * DAYSECS),
                ],
            ],
        ]);
        $result = external_api::clean_returnvalue(save_overrides::execute_returns(), $result);

        $this->assertCount(3, $result['ids']);

        // Verify all created.
        $overrides = $DB->get_records('assign_overrides', ['assignid' => $data['assign']->id]);
        $this->assertCount(3, $overrides);
    }

    /**
     * Test save_overrides validates date ordering.
     */
    public function test_save_overrides_validates_date_order(): void {
        $this->resetAfterTest();
        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $now = time();

        // Try to create override with cutoff before due date.
        $this->expectException(invalid_parameter_exception::class);
        save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => $now + (10 * DAYSECS),
                'cutoffdate' => $now + (5 * DAYSECS),
            ]],
        ]);
    }

    /**
     * Test save_overrides validates allow submissions from date.
     */
    public function test_save_overrides_validates_allowsubmissionsfromdate(): void {
        $this->resetAfterTest();
        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $now = time();

        // Try to create override with due date before allow submissions from.
        $this->expectException(invalid_parameter_exception::class);
        save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'allowsubmissionsfromdate' => $now + (10 * DAYSECS),
                'duedate' => $now + (8 * DAYSECS),
            ]],
        ]);
    }

    /**
     * Test save_overrides defaults reasonformat to FORMAT_MOODLE when reason is set without reasonformat.
     */
    public function test_save_overrides_reason_defaults_reasonformat_to_format_moodle(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        // Save override with reason but without reasonformat - should default to FORMAT_MOODLE.
        $result = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => time() + (10 * DAYSECS),
                'reason' => 'Medical exemption',
                // Note: reasonformat intentionally omitted - should default to FORMAT_MOODLE.
            ]],
        ]);
        $result = external_api::clean_returnvalue(save_overrides::execute_returns(), $result);

        $this->assertCount(1, $result['ids']);

        // Verify the override was saved with FORMAT_MOODLE as the default reasonformat.
        $override = $DB->get_record('assign_overrides', ['id' => $result['ids'][0]]);
        $this->assertNotFalse($override);
        $this->assertEquals('Medical exemption', $override->reason);
        $this->assertEquals(FORMAT_MOODLE, $override->reasonformat);
    }

    /**
     * Test save_overrides requires either userid or groupid.
     */
    public function test_save_overrides_requires_user_or_group(): void {
        $this->resetAfterTest();
        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $this->expectException(invalid_parameter_exception::class);
        save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'duedate' => time() + (10 * DAYSECS),
            ]],
        ]);
    }

    /**
     * Test save_overrides prevents both userid and groupid.
     */
    public function test_save_overrides_prevents_both_user_and_group(): void {
        $this->resetAfterTest();
        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $this->expectException(invalid_parameter_exception::class);
        save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'groupid' => $data['group1']->id,
                'duedate' => time() + (10 * DAYSECS),
            ]],
        ]);
    }

    /**
     * Test save_overrides requires capability.
     */
    public function test_save_overrides_requires_capability(): void {
        $this->resetAfterTest();
        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['student1']);

        $this->expectException(required_capability_exception::class);
        save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => time() + (10 * DAYSECS),
            ]],
        ]);
    }

    /**
     * Test save_overrides creates calendar events.
     */
    public function test_save_overrides_creates_calendar_events(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $duedate = time() + (10 * DAYSECS);

        save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => $duedate,
            ]],
        ]);

        // Check calendar event was created.
        $events = $DB->get_records('event', [
            'modulename' => 'assign',
            'instance' => $data['assign']->id,
            'userid' => $data['student1']->id,
        ]);
        $this->assertCount(1, $events);

        $event = reset($events);
        $this->assertEquals($duedate, $event->timestart);
    }

    /**
     * Test delete_overrides removes an override.
     */
    public function test_delete_overrides_single(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        // Create an override.
        $override = new stdClass();
        $override->assignid = $data['assign']->id;
        $override->userid = $data['student1']->id;
        $override->duedate = time() + (10 * DAYSECS);
        $override->id = $DB->insert_record('assign_overrides', $override);

        // Delete it.
        $result = delete_overrides::execute([
            'assignid' => $data['assign']->id,
            'ids' => [$override->id],
        ]);
        $result = external_api::clean_returnvalue(delete_overrides::execute_returns(), $result);

        $this->assertCount(1, $result['ids']);
        $this->assertEquals($override->id, $result['ids'][0]);

        // Verify deleted from database.
        $this->assertFalse($DB->record_exists('assign_overrides', ['id' => $override->id]));
    }

    /**
     * Test delete_overrides with multiple overrides.
     */
    public function test_delete_overrides_multiple(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $now = time();

        // Create overrides.
        $override1 = new stdClass();
        $override1->assignid = $data['assign']->id;
        $override1->userid = $data['student1']->id;
        $override1->duedate = $now + (10 * DAYSECS);
        $override1->id = $DB->insert_record('assign_overrides', $override1);

        $override2 = new stdClass();
        $override2->assignid = $data['assign']->id;
        $override2->userid = $data['student2']->id;
        $override2->duedate = $now + (11 * DAYSECS);
        $override2->id = $DB->insert_record('assign_overrides', $override2);

        // Delete both.
        $result = delete_overrides::execute([
            'assignid' => $data['assign']->id,
            'ids' => [$override1->id, $override2->id],
        ]);
        $result = external_api::clean_returnvalue(delete_overrides::execute_returns(), $result);

        $this->assertCount(2, $result['ids']);

        // Verify both deleted.
        $this->assertFalse($DB->record_exists('assign_overrides', ['id' => $override1->id]));
        $this->assertFalse($DB->record_exists('assign_overrides', ['id' => $override2->id]));
    }

    /**
     * Test delete_overrides requires capability.
     */
    public function test_delete_overrides_requires_capability(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();

        // Create override as teacher.
        $this->setUser($data['teacher']);
        $override = new stdClass();
        $override->assignid = $data['assign']->id;
        $override->userid = $data['student1']->id;
        $override->duedate = time() + (10 * DAYSECS);
        $override->id = $DB->insert_record('assign_overrides', $override);

        // Try to delete as student.
        $this->setUser($data['student1']);
        $this->expectException(required_capability_exception::class);
        delete_overrides::execute([
            'assignid' => $data['assign']->id,
            'ids' => [$override->id],
        ]);
    }

    /**
     * Test delete_overrides removes calendar events.
     */
    public function test_delete_overrides_removes_calendar_events(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        // Create override (which should create calendar event).
        $duedate = time() + (10 * DAYSECS);
        $result = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => $duedate,
            ]],
        ]);
        $result = external_api::clean_returnvalue(save_overrides::execute_returns(), $result);
        $overrideid = $result['ids'][0];

        // Verify calendar event exists.
        $this->assertTrue($DB->record_exists('event', [
            'modulename' => 'assign',
            'instance' => $data['assign']->id,
            'userid' => $data['student1']->id,
        ]));

        // Delete override.
        $result = delete_overrides::execute([
            'assignid' => $data['assign']->id,
            'ids' => [$overrideid],
        ]);
        $result = external_api::clean_returnvalue(delete_overrides::execute_returns(), $result);

        // Verify calendar event removed.
        $this->assertFalse($DB->record_exists('event', [
            'modulename' => 'assign',
            'instance' => $data['assign']->id,
            'userid' => $data['student1']->id,
        ]));
    }

    /**
     * Test save_overrides respects extension due dates.
     */
    public function test_save_overrides_respects_extension_dates(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        // Set an extension due date for student1.
        $extensiondate = time() + (5 * DAYSECS);
        $userflags = new stdClass();
        $userflags->assignment = $data['assign']->id;
        $userflags->userid = $data['student1']->id;
        $userflags->extensionduedate = $extensiondate;
        $DB->insert_record('assign_user_flags', $userflags);

        // Try to create override with due date after extension (should fail).
        $this->expectException(invalid_parameter_exception::class);
        save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => time() + (10 * DAYSECS),
            ]],
        ]);
    }

    /**
     * Test save_overrides with grade recalculation for user override.
     */
    public function test_save_overrides_recalculate_user_grades(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        // Set up late submission with penalty using shared trait method.
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
        $result = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => time() + (10 * DAYSECS), // Due date now in future.
            ]],
            'recalculatepenalties' => true,
        ]);
        $result = external_api::clean_returnvalue(save_overrides::execute_returns(), $result);

        // Expect debugging calls from recalculation.
        $this->assertDebuggingCalledCount(2);

        // Verify override was created.
        $this->assertCount(1, $result['ids']);

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
     * Test save_overrides does not recalculate grades when gradepenalty is disabled.
     */
    public function test_save_overrides_no_recalculate_when_penalty_disabled(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        // Ensure gradepenalty is disabled (default is 0).
        $data['assign']->gradepenalty = 0;
        $DB->update_record('assign', $data['assign']);

        // Set times for late submission scenario.
        $duedate = time() - (2 * DAYSECS); // Due date 2 days ago.
        $submissiontime = time() - DAYSECS; // Submitted 1 day ago (1 day late).
        $pasttime = time() - (3 * DAYSECS); // Initial grade time.

        // Update assignment due date.
        $data['assign']->duedate = $duedate;
        $DB->update_record('assign', $data['assign']);

        // Create a submission for student1 (submitted late).
        $submission = new stdClass();
        $submission->assignment = $data['assign']->id;
        $submission->userid = $data['student1']->id;
        $submission->status = 'submitted';
        $submission->latest = 1;
        $submission->attemptnumber = 0;
        $submission->timecreated = $submissiontime;
        $submission->timemodified = $submissiontime;
        $DB->insert_record('assign_submission', $submission);

        // Create initial grade.
        $grade = new stdClass();
        $grade->assignment = $data['assign']->id;
        $grade->userid = $data['student1']->id;
        $grade->grader = $data['teacher']->id;
        $grade->grade = 100;
        $grade->attemptnumber = 0;
        $grade->timecreated = $pasttime;
        $grade->timemodified = $pasttime;
        $gradeid = $DB->insert_record('assign_grades', $grade);

        // Verify initial grade exists.
        $initialgrade = $DB->get_record('assign_grades', ['id' => $gradeid]);
        $this->assertNotFalse($initialgrade);
        $this->assertEquals(100, $initialgrade->grade);

        // Save override with recalculate flag when gradepenalty is disabled.
        // This should complete successfully without recalculating grades.
        $result = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => time() + (10 * DAYSECS),
            ]],
            'recalculatepenalties' => true,
        ]);

        // Verify the override was created.
        $this->assertNotEmpty($result['ids']);
        $this->assertCount(1, $result['ids']);

        // Verify the grade was not recalculated (should remain unchanged).
        $finalgrade = $DB->get_record('assign_grades', ['id' => $gradeid]);
        $this->assertNotFalse($finalgrade);
        $this->assertEquals(100, $finalgrade->grade);
        $this->assertEquals($initialgrade->timemodified, $finalgrade->timemodified);
    }

    /**
     * Test save_overrides without grade recalculation (default behavior).
     */
    public function test_save_overrides_no_recalculate(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $now = time();

        // Create a submission and grade for student1.
        $submission = new stdClass();
        $submission->assignment = $data['assign']->id;
        $submission->userid = $data['student1']->id;
        $submission->status = 'submitted';
        $submission->latest = 1;
        $submission->attemptnumber = 0;
        $submission->timecreated = $now;
        $submission->timemodified = $now;
        $DB->insert_record('assign_submission', $submission);

        $grade = new stdClass();
        $grade->assignment = $data['assign']->id;
        $grade->userid = $data['student1']->id;
        $grade->grader = $data['teacher']->id;
        $grade->grade = 75;
        $grade->attemptnumber = 0;
        $timecreated = $now - 3600; // 1 hour ago.
        $grade->timecreated = $timecreated;
        $grade->timemodified = $timecreated;
        $gradeid = $DB->insert_record('assign_grades', $grade);

        // Save override without recalculate flag (default is false).
        $result = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => $now + (10 * DAYSECS),
            ]],
            'recalculatepenalties' => false,
        ]);
        $result = external_api::clean_returnvalue(save_overrides::execute_returns(), $result);

        // Verify override was created.
        $this->assertCount(1, $result['ids']);

        // Verify grade was not modified.
        $unchangedgrade = $DB->get_record('assign_grades', ['id' => $gradeid]);
        $this->assertNotFalse($unchangedgrade);
        $this->assertEquals($timecreated, $unchangedgrade->timemodified);
    }

    /**
     * Test save_overrides with grade recalculation for group override.
     */
    public function test_save_overrides_recalculate_group_grades(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        // Set up late submissions with penalties for both group members using shared trait method.
        $this->setup_late_submission_with_penalty($data, $data['student1']);

        // Set up second student with same late submission scenario.
        $this->setup_late_submission_with_penalty($data, $data['student2']);

        // Verify penalties applied by checking final grades.
        $course = $DB->get_record('course', ['id' => $data['assign']->course]);
        $initial1 = $this->get_final_grade($data['assign']->id, $course->id, $data['student1']->id);
        $initial2 = $this->get_final_grade($data['assign']->id, $course->id, $data['student2']->id);
        $this->assertEquals(90, $initial1); // 100 - 10% penalty = 90.
        $this->assertEquals(90, $initial2); // 100 - 10% penalty = 90.

        // Switch back to teacher for override operations.
        $this->setUser($data['teacher']);
        // Save group override with recalculate flag.
        // The override moves due date to the future (after submissions), so penalties should be removed.
        $result = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'groupid' => $data['group1']->id,
                'duedate' => time() + (10 * DAYSECS), // Due date now in future.
            ]],
            'recalculatepenalties' => true,
        ]);
        $result = external_api::clean_returnvalue(save_overrides::execute_returns(), $result);

        // Expect debugging calls from recalculation (2 per student = 4 total).
        $this->assertDebuggingCalledCount(4);

        // Verify override was created.
        $this->assertCount(1, $result['ids']);

        // Verify penalties were REMOVED after recalculation.
        // Since due date is now after submission dates, submissions are no longer late.
        $updated1 = $this->get_final_grade($data['assign']->id, $course->id, $data['student1']->id);
        $updated2 = $this->get_final_grade($data['assign']->id, $course->id, $data['student2']->id);
        $this->assertEquals(100, $updated1); // Penalty removed: 90 → 100.
        $this->assertEquals(100, $updated2); // Penalty removed: 90 → 100.
    }

    /**
     * Test delete_overrides with grade recalculation for user override.
     */
    public function test_delete_overrides_recalculate_user_grades(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        // Enable assignment penalty system.
        $this->enable_assign_penalty($data['assign']);

        // Set times for late submission scenario.
        $duedate = time() - (2 * DAYSECS); // Due date 2 days ago.

        // Update assignment due date.
        $data['assign']->duedate = $duedate;
        $DB->update_record('assign', $data['assign']);

        // Create an override with future due date (so submission won't be late initially).
        $override = new stdClass();
        $override->assignid = $data['assign']->id;
        $override->userid = $data['student1']->id;
        $override->duedate = time() + (10 * DAYSECS);
        $override->id = $DB->insert_record('assign_overrides', $override);

        // Set up late submission (but override prevents penalty).
        $this->setup_late_submission_with_penalty($data, $data['student1']);

        // Verify NO penalty applied initially (override makes due date future).
        // With the override active, due date is in the future, so submission is not late.
        $course = $DB->get_record('course', ['id' => $data['assign']->course]);
        $initialfinalgrade = $this->get_final_grade(
            $data['assign']->id,
            $course->id,
            $data['student1']->id
        );
        $this->assertEquals(100, $initialfinalgrade); // No penalty with override active.

        // Return to teacher to delete override.
        $this->setUser($data['teacher']);
        // Delete override with recalculate flag.
        // After deletion, due date reverts to assignment default (2 days ago), making submission late.
        $result = delete_overrides::execute([
            'assignid' => $data['assign']->id,
            'ids' => [$override->id],
            'recalculatepenalties' => true,
        ]);
        $result = external_api::clean_returnvalue(delete_overrides::execute_returns(), $result);

        // Expect debugging calls from recalculation.
        $this->assertDebuggingCalledCount(2);

        // Verify override was deleted.
        $this->assertCount(1, $result['ids']);
        $this->assertFalse($DB->record_exists('assign_overrides', ['id' => $override->id]));

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
     * Test delete_overrides without grade recalculation (default behavior).
     */
    public function test_delete_overrides_no_recalculate(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $now = time();

        // Create an override.
        $override = new stdClass();
        $override->assignid = $data['assign']->id;
        $override->userid = $data['student1']->id;
        $override->duedate = $now + (10 * DAYSECS);
        $override->id = $DB->insert_record('assign_overrides', $override);

        // Create a submission and grade for student1.
        $submission = new stdClass();
        $submission->assignment = $data['assign']->id;
        $submission->userid = $data['student1']->id;
        $submission->status = 'submitted';
        $submission->latest = 1;
        $submission->attemptnumber = 0;
        $submission->timecreated = $now;
        $submission->timemodified = $now;
        $DB->insert_record('assign_submission', $submission);

        $grade = new stdClass();
        $grade->assignment = $data['assign']->id;
        $grade->userid = $data['student1']->id;
        $grade->grader = $data['teacher']->id;
        $grade->grade = 75;
        $grade->attemptnumber = 0;
        $timecreated = $now - 3600; // 1 hour ago.
        $grade->timecreated = $timecreated;
        $grade->timemodified = $timecreated;
        $gradeid = $DB->insert_record('assign_grades', $grade);

        // Delete override without recalculate flag (default is false).
        $result = delete_overrides::execute([
            'assignid' => $data['assign']->id,
            'ids' => [$override->id],
            'recalculatepenalties' => false,
        ]);
        $result = external_api::clean_returnvalue(delete_overrides::execute_returns(), $result);

        // Verify override was deleted.
        $this->assertCount(1, $result['ids']);
        $this->assertFalse($DB->record_exists('assign_overrides', ['id' => $override->id]));

        // Verify grade was not modified.
        $unchangedgrade = $DB->get_record('assign_grades', ['id' => $gradeid]);
        $this->assertNotFalse($unchangedgrade);
        $this->assertEquals($timecreated, $unchangedgrade->timemodified);
    }

    /**
     * Test delete_overrides with grade recalculation for group override.
     */
    public function test_delete_overrides_recalculate_group_grades(): void {
        global $DB;
        $this->resetAfterTest();

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        // Enable assignment penalty system.
        $this->enable_assign_penalty($data['assign']);

        // Set times for late submission scenario.
        $duedate = time() - (2 * DAYSECS); // Due date 2 days ago.

        // Update assignment due date.
        $data['assign']->duedate = $duedate;
        $DB->update_record('assign', $data['assign']);

        // Create a group override with future due date (so submissions won't be late initially).
        $override = new stdClass();
        $override->assignid = $data['assign']->id;
        $override->groupid = $data['group1']->id;
        $override->duedate = time() + (10 * DAYSECS);
        $override->sortorder = 1;
        $override->id = $DB->insert_record('assign_overrides', $override);

        // Set up late submissions with penalties for both group members using shared trait method.
        $this->setup_late_submission_with_penalty($data, $data['student1']);

        // Set up second student with same late submission scenario.
        $this->setup_late_submission_with_penalty($data, $data['student2']);

        // Verify NO penalties applied initially (override makes due date future).
        // With the override active, due date is in the future, so submissions are not late.
        $course = $DB->get_record('course', ['id' => $data['assign']->course]);
        $initial1 = $this->get_final_grade($data['assign']->id, $course->id, $data['student1']->id);
        $initial2 = $this->get_final_grade($data['assign']->id, $course->id, $data['student2']->id);
        $this->assertEquals(100, $initial1); // No penalty with override active.
        $this->assertEquals(100, $initial2); // No penalty with override active.

        // Return to teacher to delete override.
        $this->setUser($data['teacher']);
        // Delete group override with recalculate flag.
        // After deletion, due date reverts to assignment default (2 days ago), making submissions late.
        $result = delete_overrides::execute([
            'assignid' => $data['assign']->id,
            'ids' => [$override->id],
            'recalculatepenalties' => true,
        ]);
        $result = external_api::clean_returnvalue(delete_overrides::execute_returns(), $result);

        // Expect debugging calls from recalculation (2 per student = 4 total).
        $this->assertDebuggingCalledCount(4);

        // Verify override was deleted.
        $this->assertCount(1, $result['ids']);
        $this->assertFalse($DB->record_exists('assign_overrides', ['id' => $override->id]));

        // Verify penalties are NOW applied after recalculation.
        // With override deleted, due date is in past, so 10% penalty is applied to both.
        $updated1 = $this->get_final_grade($data['assign']->id, $course->id, $data['student1']->id);
        $updated2 = $this->get_final_grade($data['assign']->id, $course->id, $data['student2']->id);
        $this->assertEquals(90, $updated1); // Penalty applied: 100 → 90.
        $this->assertEquals(90, $updated2); // Penalty applied: 100 → 90.
    }

    /**
     * Test complete workflow: create, get, update, delete.
     */
    public function test_complete_workflow(): void {
        $this->resetAfterTest();
        global $DB;

        $data = $this->create_assign_with_overrides_test_data();
        $this->setUser($data['teacher']);

        $now = time();

        // Step 1: Verify no overrides initially.
        $result = get_overrides::execute($data['assign']->id);
        $result = external_api::clean_returnvalue(get_overrides::execute_returns(), $result);
        $this->assertCount(0, $result['overrides']);

        // Step 2: Create an user override with both due date and cutoff date.
        $initialduedate = $now + (10 * DAYSECS);
        $initialcutoffdate = $now + (17 * DAYSECS);
        $createresult = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'userid' => $data['student1']->id,
                'duedate' => $initialduedate,
                'cutoffdate' => $initialcutoffdate,
            ]],
        ]);
        $createresult = external_api::clean_returnvalue(save_overrides::execute_returns(), $createresult);
        $this->assertCount(1, $createresult['ids']);
        $overrideid = $createresult['ids'][0];
        $this->assertIsInt($overrideid);
        $this->assertGreaterThan(0, $overrideid);

        // Step 3: Get and verify override exists with correct properties.
        $result = get_overrides::execute($data['assign']->id);
        $result = external_api::clean_returnvalue(get_overrides::execute_returns(), $result);
        $this->assertCount(1, $result['overrides']);

        $override = $result['overrides'][0];
        $this->assertEquals($overrideid, $override['id']);
        $this->assertEquals($data['student1']->id, $override['userid']);
        $this->assertEquals($initialduedate, $override['duedate']);
        $this->assertEquals($initialcutoffdate, $override['cutoffdate']);
        $this->assertNull($override['groupid']);

        // Step 4: Update the override (change due date, remove cutoff date).
        $newduedate = $now + (12 * DAYSECS);
        $rawupdateresult = save_overrides::execute([
            'assignid' => $data['assign']->id,
            'overrides' => [[
                'id' => $overrideid,
                'userid' => $data['student1']->id,
                'duedate' => $newduedate,
                // Note: not providing cutoffdate should clear it.
            ]],
        ]);
        $updateresult = external_api::clean_returnvalue(save_overrides::execute_returns(), $rawupdateresult);
        $this->assertCount(1, $updateresult['ids']);
        $this->assertEquals($overrideid, $updateresult['ids'][0]);

        // Step 5: Verify update took effect.
        $result = get_overrides::execute($data['assign']->id);
        $result = external_api::clean_returnvalue(get_overrides::execute_returns(), $result);
        $this->assertCount(1, $result['overrides']);

        $updatedoverride = $result['overrides'][0];
        $this->assertEquals($overrideid, $updatedoverride['id']);
        $this->assertEquals($newduedate, $updatedoverride['duedate']);
        $this->assertEquals($data['student1']->id, $updatedoverride['userid']);
        // Verify cutoffdate was cleared (should be null or not present).
        $this->assertFalse(
            isset($updatedoverride['cutoffdate']) && $updatedoverride['cutoffdate'] > 0,
            'Cutoff date should be cleared when not provided in update'
        );

        // Verify in database as well.
        $dboverride = $DB->get_record('assign_overrides', ['id' => $overrideid]);
        $this->assertNotFalse($dboverride);
        $this->assertEquals($newduedate, $dboverride->duedate);
        $this->assertNull($dboverride->cutoffdate);

        // Step 6: Delete the override.
        $rawdeleteresult = delete_overrides::execute([
            'assignid' => $data['assign']->id,
            'ids' => [$overrideid],
        ]);
        $deleteresult = external_api::clean_returnvalue(delete_overrides::execute_returns(), $rawdeleteresult);
        $this->assertCount(1, $deleteresult['ids']);
        $this->assertEquals($overrideid, $deleteresult['ids'][0]);

        // Step 7: Verify deleted from both API and database.
        $result = get_overrides::execute($data['assign']->id);
        $result = external_api::clean_returnvalue(get_overrides::execute_returns(), $result);
        $this->assertCount(0, $result['overrides']);

        $this->assertFalse(
            $DB->record_exists('assign_overrides', ['id' => $overrideid]),
            'Override should be completely removed from database'
        );
    }
}
