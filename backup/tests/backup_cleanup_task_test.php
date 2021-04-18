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
 * Tests for the \core\task\backup_cleanup_task scheduled task.
 *
 * @package    core_backup
 * @copyright  2021 Mikhail Golenkov <mikhailgolenkov@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

/**
 * Tests for the \core\task\backup_cleanup_task scheduled task.
 *
 * @copyright  2021 Mikhail Golenkov <mikhailgolenkov@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_backup_cleanup_task_testcase extends advanced_testcase {

    /**
     * Set up tasks for all tests.
     */
    protected function setUp(): void {
        $this->resetAfterTest(true);
    }

    /**
     * Take a backup of the course provided and return backup id.
     *
     * @param int $courseid Course id to be backed up.
     * @return string Backup id.
     */
    private function backup_course(int $courseid): string {
        // Backup the course.
        $user = get_admin();
        $controller = new \backup_controller(
            \backup::TYPE_1COURSE,
            $courseid,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_AUTOMATED,
            $user->id
        );
        $controller->execute_plan();
        $controller->destroy(); // Unset all structures, close files...
        return $controller->get_backupid();
    }

    /**
     * Test the task idle run. Nothing should explode.
     */
    public function test_backup_cleanup_task_idle() {
        $task = new \core\task\backup_cleanup_task();
        $task->execute();
    }

    /**
     * Test the task exits when backup | loglifetime setting is not set.
     */
    public function test_backup_cleanup_task_exits() {
        set_config('loglifetime', 0, 'backup');
        $task = new \core\task\backup_cleanup_task();
        ob_start();
        $task->execute();
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertStringContainsString('config is not set', $output);
    }

    /**
     * Test the task deletes records from DB.
     */
    public function test_backup_cleanup_task_deletes_records() {
        global $DB;

        // Create a course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Take two backups of the course.
        $backupid1 = $this->backup_course($course->id);
        $backupid2 = $this->backup_course($course->id);

        // Emulate the first backup to be done 31 days ago.
        $bcrecord = $DB->get_record('backup_controllers', ['backupid' => $backupid1]);
        $bcrecord->timecreated -= DAYSECS * 31;
        $DB->update_record('backup_controllers', $bcrecord);

        // Run the task.
        $task = new \core\task\backup_cleanup_task();
        $task->execute();

        // There should be no records related to the first backup.
        $this->assertEquals(0, $DB->count_records('backup_controllers', ['backupid' => $backupid1]));
        $this->assertEquals(0, $DB->count_records('backup_logs', ['backupid' => $backupid1]));

        // Records related to the second backup should remain.
        $this->assertGreaterThan(0, $DB->count_records('backup_controllers', ['backupid' => $backupid2]));
        $this->assertGreaterThanOrEqual(0, $DB->count_records('backup_logs', ['backupid' => $backupid2]));
    }

    /**
     * Test the task deletes files from file system.
     */
    public function test_backup_cleanup_task_deletes_files() {
        global $CFG;

        // Create a course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Take two backups of the course and get their logs.
        $backupid1 = $this->backup_course($course->id);
        $backupid2 = $this->backup_course($course->id);
        $filepath1 = $CFG->backuptempdir . '/' . $backupid1 . '.log';
        $filepath2 = $CFG->backuptempdir . '/' . $backupid2 . '.log';

        // Create a subdirectory.
        $subdir = $CFG->backuptempdir . '/subdir';
        make_writable_directory($subdir);

        // Both logs and the dir should exist.
        $this->assertTrue(file_exists($filepath1));
        $this->assertTrue(file_exists($filepath2));
        $this->assertTrue(file_exists($subdir));

        // Change modification time of the first log and the sub dir to be 8 days ago.
        touch($filepath1, time() - 8 * DAYSECS);
        touch($subdir, time() - 8 * DAYSECS);

        // Run the task.
        $task = new \core\task\backup_cleanup_task();
        $task->execute();

        // Files and directories older than a week are supposed to be removed.
        $this->assertFalse(file_exists($filepath1));
        $this->assertFalse(file_exists($subdir));
        $this->assertTrue(file_exists($filepath2));
    }
}
