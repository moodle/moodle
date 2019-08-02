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
 * Unit tests for backups cron helper.
 *
 * @package   core_backup
 * @category  phpunit
 * @copyright 2012 Frédéric Massart <fred@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/helper/backup_cron_helper.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php');
require_once("$CFG->dirroot/backup/backup.class.php");

/**
 * Unit tests for backup cron helper
 */
class backup_cron_helper_testcase extends advanced_testcase {
    /**
     * Test {@link backup_cron_automated_helper::calculate_next_automated_backup}.
     */
    public function test_next_automated_backup() {
        global $CFG;

        $this->resetAfterTest();
        set_config('backup_auto_active', '1', 'backup');

        $this->setTimezone('Australia/Perth');

        // Notes
        // - backup_auto_weekdays starts on Sunday
        // - Tests cannot be done in the past
        // - Only the DST on the server side is handled.

        // Every Tue and Fri at 11pm.
        set_config('backup_auto_weekdays', '0010010', 'backup');
        set_config('backup_auto_hour', '23', 'backup');
        set_config('backup_auto_minute', '0', 'backup');

        $timezone = 99; // Ignored, everything is calculated in server timezone!!!

        $now = strtotime('next Monday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-23:00', date('w-H:i', $next));

        $now = strtotime('next Tuesday 18:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-23:00', date('w-H:i', $next));

        $now = strtotime('next Wednesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('5-23:00', date('w-H:i', $next));

        $now = strtotime('next Thursday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('5-23:00', date('w-H:i', $next));

        $now = strtotime('next Friday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('5-23:00', date('w-H:i', $next));

        $now = strtotime('next Saturday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-23:00', date('w-H:i', $next));

        $now = strtotime('next Sunday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-23:00', date('w-H:i', $next));

        // Every Sun and Sat at 12pm.
        set_config('backup_auto_weekdays', '1000001', 'backup');
        set_config('backup_auto_hour', '0', 'backup');
        set_config('backup_auto_minute', '0', 'backup');

        $now = strtotime('next Monday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Tuesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Wednesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Thursday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Friday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Saturday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-00:00', date('w-H:i', $next));

        $now = strtotime('next Sunday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        // Every Sun at 4am.
        set_config('backup_auto_weekdays', '1000000', 'backup');
        set_config('backup_auto_hour', '4', 'backup');
        set_config('backup_auto_minute', '0', 'backup');

        $now = strtotime('next Monday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Tuesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Wednesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Thursday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Friday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Saturday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Sunday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        // Every day but Wed at 8:30pm.
        set_config('backup_auto_weekdays', '1110111', 'backup');
        set_config('backup_auto_hour', '20', 'backup');
        set_config('backup_auto_minute', '30', 'backup');

        $now = strtotime('next Monday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('1-20:30', date('w-H:i', $next));

        $now = strtotime('next Tuesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-20:30', date('w-H:i', $next));

        $now = strtotime('next Wednesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('4-20:30', date('w-H:i', $next));

        $now = strtotime('next Thursday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('4-20:30', date('w-H:i', $next));

        $now = strtotime('next Friday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('5-20:30', date('w-H:i', $next));

        $now = strtotime('next Saturday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-20:30', date('w-H:i', $next));

        $now = strtotime('next Sunday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-20:30', date('w-H:i', $next));

        // Sun, Tue, Thu, Sat at 12pm.
        set_config('backup_auto_weekdays', '1010101', 'backup');
        set_config('backup_auto_hour', '0', 'backup');
        set_config('backup_auto_minute', '0', 'backup');

        $now = strtotime('next Monday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-00:00', date('w-H:i', $next));

        $now = strtotime('next Tuesday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('4-00:00', date('w-H:i', $next));

        $now = strtotime('next Wednesday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('4-00:00', date('w-H:i', $next));

        $now = strtotime('next Thursday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Friday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Saturday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-00:00', date('w-H:i', $next));

        $now = strtotime('next Sunday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-00:00', date('w-H:i', $next));

        // None.
        set_config('backup_auto_weekdays', '0000000', 'backup');
        set_config('backup_auto_hour', '15', 'backup');
        set_config('backup_auto_minute', '30', 'backup');

        $now = strtotime('next Sunday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0', $next);

        // Playing with timezones.
        set_config('backup_auto_weekdays', '1111111', 'backup');
        set_config('backup_auto_hour', '20', 'backup');
        set_config('backup_auto_minute', '00', 'backup');

        $this->setTimezone('Australia/Perth');
        $now = strtotime('18:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals(date('w-20:00'), date('w-H:i', $next));

        $this->setTimezone('Europe/Brussels');
        $now = strtotime('18:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals(date('w-20:00'), date('w-H:i', $next));

        $this->setTimezone('America/New_York');
        $now = strtotime('18:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals(date('w-20:00'), date('w-H:i', $next));
    }

    /**
     * Test {@link backup_cron_automated_helper::get_backups_to_delete}.
     */
    public function test_get_backups_to_delete() {
        $this->resetAfterTest();
        // Active only backup_auto_max_kept config to 2 days.
        set_config('backup_auto_max_kept', '2', 'backup');
        set_config('backup_auto_delete_days', '0', 'backup');
        set_config('backup_auto_min_kept', '0', 'backup');

        // No backups to delete.
        $backupfiles = array(
            '1000000000' => 'file1.mbz',
            '1000432000' => 'file3.mbz'
        );
        $deletedbackups = testable_backup_cron_automated_helper::testable_get_backups_to_delete($backupfiles, 1000432000);
        $this->assertFalse($deletedbackups);

        // Older backup to delete.
        $backupfiles['1000172800'] = 'file2.mbz';
        $deletedbackups = testable_backup_cron_automated_helper::testable_get_backups_to_delete($backupfiles, 1000432000);
        $this->assertEquals(1, count($deletedbackups));
        $this->assertArrayHasKey('1000000000', $backupfiles);
        $this->assertEquals('file1.mbz', $backupfiles['1000000000']);

        // Activate backup_auto_max_kept to 5 days and backup_auto_delete_days to 10 days.
        set_config('backup_auto_max_kept', '5', 'backup');
        set_config('backup_auto_delete_days', '10', 'backup');
        set_config('backup_auto_min_kept', '0', 'backup');

        // No backups to delete. Timestamp is 1000000000 + 10 days.
        $backupfiles['1000432001'] = 'file4.mbz';
        $backupfiles['1000864000'] = 'file5.mbz';
        $deletedbackups = testable_backup_cron_automated_helper::testable_get_backups_to_delete($backupfiles, 1000864000);
        $this->assertFalse($deletedbackups);

        // One old backup to delete. Timestamp is 1000000000 + 10 days + 1 second.
        $deletedbackups = testable_backup_cron_automated_helper::testable_get_backups_to_delete($backupfiles, 1000864001);
        $this->assertEquals(1, count($deletedbackups));
        $this->assertArrayHasKey('1000000000', $backupfiles);
        $this->assertEquals('file1.mbz', $backupfiles['1000000000']);

        // Two old backups to delete. Timestamp is 1000000000 + 12 days + 1 second.
        $deletedbackups = testable_backup_cron_automated_helper::testable_get_backups_to_delete($backupfiles, 1001036801);
        $this->assertEquals(2, count($deletedbackups));
        $this->assertArrayHasKey('1000000000', $backupfiles);
        $this->assertEquals('file1.mbz', $backupfiles['1000000000']);
        $this->assertArrayHasKey('1000172800', $backupfiles);
        $this->assertEquals('file2.mbz', $backupfiles['1000172800']);

        // Activate backup_auto_max_kept to 5 days, backup_auto_delete_days to 10 days and backup_auto_min_kept to 2.
        set_config('backup_auto_max_kept', '5', 'backup');
        set_config('backup_auto_delete_days', '10', 'backup');
        set_config('backup_auto_min_kept', '2', 'backup');

        // Three instead of four old backups are deleted. Timestamp is 1000000000 + 16 days.
        $deletedbackups = testable_backup_cron_automated_helper::testable_get_backups_to_delete($backupfiles, 1001382400);
        $this->assertEquals(3, count($deletedbackups));
        $this->assertArrayHasKey('1000000000', $backupfiles);
        $this->assertEquals('file1.mbz', $backupfiles['1000000000']);
        $this->assertArrayHasKey('1000172800', $backupfiles);
        $this->assertEquals('file2.mbz', $backupfiles['1000172800']);
        $this->assertArrayHasKey('1000432000', $backupfiles);
        $this->assertEquals('file3.mbz', $backupfiles['1000432000']);

        // Three instead of all five backups are deleted. Timestamp is 1000000000 + 60 days.
        $deletedbackups = testable_backup_cron_automated_helper::testable_get_backups_to_delete($backupfiles, 1005184000);
        $this->assertEquals(3, count($deletedbackups));
        $this->assertArrayHasKey('1000000000', $backupfiles);
        $this->assertEquals('file1.mbz', $backupfiles['1000000000']);
        $this->assertArrayHasKey('1000172800', $backupfiles);
        $this->assertEquals('file2.mbz', $backupfiles['1000172800']);
        $this->assertArrayHasKey('1000432000', $backupfiles);
        $this->assertEquals('file3.mbz', $backupfiles['1000432000']);
    }

    /**
     * Test {@link backup_cron_automated_helper::is_course_modified}.
     */
    public function test_is_course_modified() {
        $this->resetAfterTest();
        $this->preventResetByRollback();

        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');
        set_config('logguests', 1, 'logstore_standard');

        $course = $this->getDataGenerator()->create_course();

        // New courses should be backed up.
        $this->assertTrue(testable_backup_cron_automated_helper::testable_is_course_modified($course->id, 0));

        $timepriortobackup = time();
        $this->waitForSecond();
        $otherarray = [
            'format' => backup::FORMAT_MOODLE,
            'mode' => backup::MODE_GENERAL,
            'interactive' => backup::INTERACTIVE_YES,
            'type' => backup::TYPE_1COURSE,
        ];
        $event = \core\event\course_backup_created::create([
            'objectid' => $course->id,
            'context'  => context_course::instance($course->id),
            'other'    => $otherarray
        ]);
        $event->trigger();

        // If the only action since last backup was a backup then no backup.
        $this->assertFalse(testable_backup_cron_automated_helper::testable_is_course_modified($course->id, $timepriortobackup));

        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = true;
        update_course($course);

        // Updated courses should be backed up.
        $this->assertTrue(testable_backup_cron_automated_helper::testable_is_course_modified($course->id, $timepriortobackup));
    }

    /**
     * Create courses and backup records for tests.
     *
     * @return array Created courses.
     */
    private function course_setup() {
        global $DB;

        // Create test courses.
        $course1 = $this->getDataGenerator()->create_course(array('timecreated' => 1553402000)); // Newest.
        $course2 = $this->getDataGenerator()->create_course(array('timecreated' => 1552179600));
        $course3 = $this->getDataGenerator()->create_course(array('timecreated' => 1552179600));
        $course4 = $this->getDataGenerator()->create_course(array('timecreated' => 1552179600));

        // Create backup course records for the courses that need them.
        $backupcourse3 = new stdClass;
        $backupcourse3->courseid = $course3->id;
        $backupcourse3->laststatus = testable_backup_cron_automated_helper::BACKUP_STATUS_OK;
        $backupcourse3->nextstarttime = 1554858160;
        $DB->insert_record('backup_courses', $backupcourse3);

        $backupcourse4 = new stdClass;
        $backupcourse4->courseid = $course4->id;
        $backupcourse4->laststatus = testable_backup_cron_automated_helper::BACKUP_STATUS_OK;
        $backupcourse4->nextstarttime = 1554858160;
        $DB->insert_record('backup_courses', $backupcourse4);

        return array($course1, $course2, $course3, $course4);
    }

    /**
     * Test the selection and ordering of courses to be backed up.
     */
    public function test_get_courses() {
        $this->resetAfterTest();

        list($course1, $course2, $course3, $course4) = $this->course_setup();

        $now = 1559215025;

        // Get the courses in order.
        $courseset = testable_backup_cron_automated_helper::testable_get_courses($now);

        $coursearray = array();
        foreach ($courseset as $course) {
            if ($course->id != SITEID) { // Skip system course for test.
                $coursearray[] = $course->id;
            }

        }
        $courseset->close();

        // First should be course 1, it is the more recently modified without a backup.
        $this->assertEquals($course1->id, $coursearray[0]);

        // Second should be course 2, it is the next more recently modified without a backup.
        $this->assertEquals($course2->id, $coursearray[1]);

        // Third should be course 3, it is the course with the oldest backup.
        $this->assertEquals($course3->id, $coursearray[2]);

        // Fourth should be course 4, it is the course with the newest backup.
        $this->assertEquals($course4->id, $coursearray[3]);
    }

    /**
     * Test the selection and ordering of courses to be backed up.
     * Where it is not yet time to start backups for courses with existing backups.
     */
    public function test_get_courses_starttime() {
        $this->resetAfterTest();

        list($course1, $course2, $course3, $course4) = $this->course_setup();

        $now = 1554858000;

        // Get the courses in order.
        $courseset = testable_backup_cron_automated_helper::testable_get_courses($now);

        $coursearray = array();
        foreach ($courseset as $course) {
            if ($course->id != SITEID) { // Skip system course for test.
                $coursearray[] = $course->id;
            }

        }
        $courseset->close();

        // Should only be two courses.
        // First should be course 1, it is the more recently modified without a backup.
        $this->assertEquals($course1->id, $coursearray[0]);

        // Second should be course 2, it is the next more recently modified without a backup.
        $this->assertEquals($course2->id, $coursearray[1]);
    }

}



/**
 * Provides access to protected methods we want to explicitly test
 *
 * @copyright 2015 Jean-Philippe Gaudreau <jp.gaudreau@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_backup_cron_automated_helper extends backup_cron_automated_helper {

    /**
     * Provides access to protected method get_backups_to_remove.
     *
     * @param array $backupfiles Existing backup files
     * @param int $now Starting time of the process
     * @return array Backup files to remove
     */
    public static function testable_get_backups_to_delete($backupfiles, $now) {
        return parent::get_backups_to_delete($backupfiles, $now);
    }

    /**
     * Provides access to protected method get_backups_to_remove.
     *
     * @param int $courseid course id to check
     * @param int $since timestamp, from which to check
     *
     * @return bool true if the course was modified, false otherwise. This also returns false if no readers are enabled. This is
     * intentional, since we cannot reliably determine if any modification was made or not.
     */
    public static function testable_is_course_modified($courseid, $since) {
        return parent::is_course_modified($courseid, $since);
    }

    /**
     * Provides access to protected method get_courses.
     *
     * @param int $now Timestamp to use.
     * @return moodle_recordset The returned courses as a Moodle recordest.
     */
    public static function testable_get_courses($now) {
        return parent::get_courses($now);
    }

}
