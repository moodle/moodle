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

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff.
global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');


/**
 * Unit tests for how backup and restore handles role-related things.
 *
 * @package   core_backup
 * @copyright 2021 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class roles_backup_restore_test extends advanced_testcase {

    /**
     * Create a course where the (non-editing) Teacher role is overridden
     * to have 'moodle/user:loginas' and 'moodle/site:accessallgroups'.
     *
     * @return stdClass the new course.
     */
    protected function create_course_with_role_overrides(): stdClass {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $teacher = $generator->create_user();

        $context = context_course::instance($course->id);
        $generator->enrol_user($teacher->id, $course->id, 'teacher');

        $editingteacherrole = $this->get_role('teacher');
        role_change_permission($editingteacherrole->id, $context, 'moodle/user:loginas', CAP_ALLOW);
        role_change_permission($editingteacherrole->id, $context, 'moodle/site:accessallgroups', CAP_ALLOW);

        return $course;
    }

    /**
     * Get the role id from a shortname.
     *
     * @param string $shortname the role shortname.
     * @return stdClass the role from the DB.
     */
    protected function get_role(string $shortname): stdClass {
        global $DB;
        return $DB->get_record('role', ['shortname' => $shortname]);
    }

    /**
     * Get an array capability => CAP_... constant for all the orverrides set for a given role on a given context.
     *
     * @param string $shortname role shortname.
     * @param context $context context.
     * @return array the overrides set here.
     */
    protected function get_overrides_for_role_on_context(string $shortname, context $context): array {
        $overridedata = get_capabilities_from_role_on_context($this->get_role($shortname), $context);
        $overrides = [];
        foreach ($overridedata as $override) {
            $overrides[$override->capability] = $override->permission;
        }
        return $overrides;
    }

    /**
     * Makes a backup of the course.
     *
     * @param stdClass $course The course object.
     * @return string Unique identifier for this backup.
     */
    protected function backup_course(\stdClass $course): string {
        global $CFG, $USER;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do backup with default settings. MODE_IMPORT means it will just
        // create the directory and not zip it.
        $bc = new \backup_controller(backup::TYPE_1COURSE, $course->id,
                backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
                $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        return $backupid;
    }

    /**
     * Restores a backup that has been made earlier.
     *
     * @param string $backupid The unique identifier of the backup.
     * @param string $asroleshortname Which role in the new cousre the restorer should have.
     * @return int The new course id.
     */
    protected function restore_adding_to_course(string $backupid, string $asroleshortname): int {
        global $CFG, $USER;

        // Create course to restore into, and a user to do the restore.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $restorer = $generator->create_user();

        $generator->enrol_user($restorer->id, $course->id, $asroleshortname);
        $this->setUser($restorer);

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do restore to new course with default settings.
        $rc = new \restore_controller($backupid, $course->id,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_CURRENT_ADDING);

        $precheck = $rc->execute_precheck();
        $this->assertTrue($precheck);
        $rc->get_plan()->get_setting('role_assignments')->set_value(true);
        $rc->get_plan()->get_setting('permissions')->set_value(true);
        $rc->execute_plan();
        $rc->destroy();

        return $course->id;
    }

    public function test_restore_role_overrides_as_manager(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and back it up.
        $course = $this->create_course_with_role_overrides();
        $backupid = $this->backup_course($course);

        // When manager restores, both role overrides should be restored.
        $newcourseid = $this->restore_adding_to_course($backupid, 'manager');

        // Verify.
        $overrides = $this->get_overrides_for_role_on_context('teacher',
                context_course::instance($newcourseid));
        $this->assertArrayHasKey('moodle/user:loginas', $overrides);
        $this->assertEquals(CAP_ALLOW, $overrides['moodle/user:loginas']);
        $this->assertArrayHasKey('moodle/site:accessallgroups', $overrides);
        $this->assertEquals(CAP_ALLOW, $overrides['moodle/site:accessallgroups']);
    }

    public function test_restore_role_overrides_as_teacher(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and back it up.
        $course = $this->create_course_with_role_overrides();
        $backupid = $this->backup_course($course);

        // When teacher restores, only the safe override should be restored.
        $newcourseid = $this->restore_adding_to_course($backupid, 'editingteacher');

        // Verify.
        $overrides = $this->get_overrides_for_role_on_context('teacher',
                context_course::instance($newcourseid));
        $this->assertArrayNotHasKey('moodle/user:loginas', $overrides);
        $this->assertArrayHasKey('moodle/site:accessallgroups', $overrides);
        $this->assertEquals(CAP_ALLOW, $overrides['moodle/site:accessallgroups']);
    }
}
