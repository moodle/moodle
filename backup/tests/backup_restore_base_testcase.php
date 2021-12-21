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

/**
 * Backup restore base tests.
 *
 * @package   core_backup
 * @copyright Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Basic testcase class for backup / restore functionality.
 */
abstract class core_backup_backup_restore_base_testcase extends advanced_testcase {

    /**
     * Setup test data.
     */
    protected function setUp(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Backup the course by general mode.
     *
     * @param  stdClass $course Course for backup.
     * @return string Hash string ID from the backup.
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function perform_backup($course): string {
        global $CFG, $USER;

        $coursecontext = context_course::instance($course->id);

        // Start backup process.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id);
        $bc->execute_plan();
        $backupid = $bc->get_backupid();
        $bc->destroy();

        // Get the backup file.
        $fs = get_file_storage();
        $files = $fs->get_area_files($coursecontext->id, 'backup', 'course', false, 'id ASC');
        $backupfile = reset($files);

        // Extract backup file.
        $path = $CFG->tempdir . DIRECTORY_SEPARATOR . "backup" . DIRECTORY_SEPARATOR . $backupid;

        $fp = get_file_packer('application/vnd.moodle.backup');
        $fp->extract_to_pathname($backupfile, $path);

        return $backupid;
    }

    /**
     * Restore from backupid to course.
     *
     * @param  string   $backupid Hash string ID from backup.
     * @param  stdClass $course Course which is restored for.
     * @throws restore_controller_exception
     */
    protected function perform_restore($backupid, $course): void {
        global $USER;

        // Set up restore.
        $rc = new restore_controller($backupid, $course->id,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id, backup::TARGET_EXISTING_ADDING);
        // Execute restore.
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();
    }

    /**
     * Import course from course1 to course2.
     *
     * @param stdClass $course1 Course to be backuped up.
     * @param stdClass $course2 Course to be restored.
     * @throws restore_controller_exception
     */
    protected function perform_import($course1, $course2): void {
        global $USER;

        // Start backup process.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course1->id, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Set up restore.
        $rc = new restore_controller($backupid, $course2->id,
                backup::INTERACTIVE_NO, backup::MODE_SAMESITE, $USER->id, backup::TARGET_EXISTING_ADDING);
        // Execute restore.
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();
    }

}
