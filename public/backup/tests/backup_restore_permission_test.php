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
 * @copyright Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class backup_restore_permission_test extends core_backup_backup_restore_base_testcase {

    /** @var stdClass A test course which is restored/imported from. */
    protected $course1;

    /** @var stdClass A test course which is restored/imported to. */
    protected $course2;

    /** @var stdClass A user for using in this test. */
    protected $user;

    /** @var string Capability name for using in this test. */
    protected $capabilityname;

    /** @var context_course Context instance for course1. */
    protected $course1context;

    /** @var context_course Context instance for course2. */
    protected $course2context;

    /**
     * Setup test data.
     */
    protected function setUp(): void {
        global $DB;

        parent::setUp();
        // Create a course with some availability data set.
        $generator = $this->getDataGenerator();
        $this->course1 = $generator->create_course();
        $this->course1context = \context_course::instance($this->course1->id);
        $this->course2 = $generator->create_course();
        $this->course2context = \context_course::instance($this->course2->id);
        $this->capabilityname = 'enrol/manual:enrol';
        $this->user = $generator->create_user();

        // Set additional permission for course 1.
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher'], '*', MUST_EXIST);
        role_change_permission($teacherrole->id, $this->course1context, $this->capabilityname, CAP_ALLOW);

        // Enrol to the courses.
        $generator->enrol_user($this->user->id, $this->course1->id, $teacherrole->id);
        $generator->enrol_user($this->user->id, $this->course2->id, $teacherrole->id);
    }

    /**
     * Test having settings.
     */
    public function test_having_settings(): void {
        $this->assertEquals(0, get_config('backup', 'backup_import_permissions'));
        $this->assertEquals(1, get_config('restore', 'restore_general_permissions'));
    }

    /**
     * Test for restore with permission.
     */
    public function test_backup_restore_with_permission(): void {

        // Set default setting to restore with permission.
        set_config('restore_general_permissions', 1, 'restore');

        // Confirm course1 has the capability for the user.
        $this->assertTrue(has_capability($this->capabilityname, $this->course1context, $this->user));

        // Confirm course2 does not have the capability for the user.
        $this->assertFalse(has_capability($this->capabilityname, $this->course2context, $this->user));

        // Perform backup and restore.
        $backupid = $this->perform_backup($this->course1);
        $this->perform_restore($backupid, $this->course2);

        // Confirm course2 has the capability for the user.
        $this->assertTrue(has_capability($this->capabilityname, $this->course2context, $this->user));
    }

    /**
     * Test for backup / restore without restore permission.
     */
    public function test_backup_restore_without_permission(): void {

        // Set default setting to restore without permission.
        set_config('restore_general_permissions', 0, 'restore');

        // Perform backup and restore.
        $backupid = $this->perform_backup($this->course1);
        $this->perform_restore($backupid, $this->course2);

        // Confirm course2 does not have the capability for the user.
        $this->assertFalse(has_capability($this->capabilityname, $this->course2context, $this->user));
    }

    /**
     * Test for import with permission.
     */
    public function test_backup_import_with_permission(): void {

        // Set default setting to restore with permission.
        set_config('backup_import_permissions', 1, 'backup');

        // Perform import.
        $this->perform_import($this->course1, $this->course2);

        // Confirm course2 does not have the capability for the user.
        $this->assertTrue(has_capability($this->capabilityname, $this->course2context, $this->user));
    }

    /**
     * Test for import without permission.
     */
    public function test_backup_import_without_permission(): void {

        // Set default setting to restore without permission.
        set_config('backup_import_permissions', 0, 'backup');

        // Perform import.
        $this->perform_import($this->course1, $this->course2);

        // Confirm course2 does not have the capability for the user.
        $this->assertFalse(has_capability($this->capabilityname, $this->course2context, $this->user));
    }

}
