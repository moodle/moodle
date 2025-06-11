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

namespace core_backup;

use core_backup_backup_restore_base_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once('backup_restore_base_testcase.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Backup restore permission tests.
 *
 * @package   core_backup
 * @author    Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @copyright 2023 Catalyst IT Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class backup_restore_group_test extends core_backup_backup_restore_base_testcase {

    /**
     * Test for backup/restore with customfields.
     * @covers \backup_groups_structure_step
     * @covers \restore_groups_structure_step
     */
    public function test_backup_restore_group_with_customfields(): void {

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        $groupfieldcategory = self::getDataGenerator()->create_custom_field_category([
            'component' => 'core_group',
            'area' => 'group',
        ]);
        $groupcustomfield = self::getDataGenerator()->create_custom_field([
            'shortname' => 'testgroupcustomfield1',
            'type' => 'text',
            'categoryid' => $groupfieldcategory->get('id'),
        ]);
        $groupingfieldcategory = self::getDataGenerator()->create_custom_field_category([
            'component' => 'core_group',
            'area' => 'grouping',
        ]);
        $groupingcustomfield = self::getDataGenerator()->create_custom_field([
            'shortname' => 'testgroupingcustomfield1',
            'type' => 'text',
            'categoryid' => $groupingfieldcategory->get('id'),
        ]);

        $group1 = self::getDataGenerator()->create_group([
            'courseid' => $course1->id,
            'name' => 'Test group 1',
            'customfield_testgroupcustomfield1' => 'Custom input for group1',
        ]);
        $grouping1 = self::getDataGenerator()->create_grouping([
            'courseid' => $course1->id,
            'name' => 'Test grouping 1',
            'customfield_testgroupingcustomfield1' => 'Custom input for grouping1',
        ]);

        // Perform backup and restore.
        $backupid = $this->perform_backup($course1);
        $this->perform_restore($backupid, $course2);

        // Test group.
        $groups = groups_get_all_groups($course2->id);
        $this->assertCount(1, $groups);
        $group = reset($groups);

        // Confirm the group is not same group as original one.
        $this->assertNotEquals($group1->id, $group->id);
        $this->assertEquals($group1->name, $group->name);

        // Confirm custom field is restored in the new group.
        $grouphandler = \core_group\customfield\group_handler::create();
        $data = $grouphandler->export_instance_data_object($group->id);
        $this->assertSame('Custom input for group1', $data->testgroupcustomfield1);

        // Test grouping.
        $groupings = groups_get_all_groupings($course2->id);
        $this->assertCount(1, $groupings);
        $grouping = reset($groupings);

        // Confirm this is not same grouping as original one.
        $this->assertNotEquals($grouping1->id, $grouping->id);

        // Confirm custom field is restored in the new grouping.
        $groupinghandler = \core_group\customfield\grouping_handler::create();
        $data = $groupinghandler->export_instance_data_object($grouping->id);
        $this->assertSame('Custom input for grouping1', $data->testgroupingcustomfield1);
    }
}
