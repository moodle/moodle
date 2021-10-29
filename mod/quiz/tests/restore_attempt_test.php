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

namespace mod_quiz;

use backup;
use core_user;
use restore_controller;
use restore_dbops;

/**
 * Unit tests restoring quiz attempts
 *
 * @package     mod_quiz
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_attempt_test extends \advanced_testcase {

    /**
     * Load required libraries
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/backup/util/includes/restore_includes.php");
    }

    /**
     * Test restore dates.
     *
     * @covers \restore_quiz_activity_structure_step
     */
    public function test_restore_question_attempts_missing_users(): void {
        global $DB, $USER;

        // TODO: Remove this once MDL-72950 is fixed.
        if ($DB->get_dbfamily() == 'oracle') {
            $this->markTestSkipped("Skipping for Oracle until MDL-72950 is fixed.");
        }

        $this->resetAfterTest();
        $this->setAdminUser();

        // Contains a quiz with four attempts from different users. The users are as follows (user ID -> user):
        // 3 -> User 01, 4 -> User 02, 5 -> User 03, 6 -> User 04.
        // The user details for User 02 and User 03 have been removed from the backup file.
        $testfixture = __DIR__ . '/fixtures/question_attempts_missing_users.mbz';

        // Extract our test fixture, ready to be restored.
        $backuptempdir = 'aaa';
        $backuppath = make_backup_temp_directory($backuptempdir);
        get_file_packer('application/vnd.moodle.backup')->extract_to_pathname($testfixture, $backuppath);

        // Do the restore to new course with default settings.
        $categoryid = $DB->get_field('course_categories', 'MIN(id)', []);
        $courseid = restore_dbops::create_new_course('Test fullname', 'Test shortname', $categoryid);

        $controller = new restore_controller($backuptempdir, $courseid, backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
            backup::TARGET_NEW_COURSE);

        $this->assertTrue($controller->execute_precheck());
        $controller->execute_plan();
        $controller->destroy();

        // Grade restore also generates some debugging.
        $this->assertDebuggingCalledCount(2);

        $restoredquiz = $DB->get_record('quiz', []);

        // Assert logs were added for User 02 and User 03, due to missing mapping lookup.
        $loginfomessages = $DB->get_fieldset_select('backup_logs', 'message', 'backupid = ? AND loglevel = ?', [
            $controller->get_restoreid(),
            backup::LOG_INFO,
        ]);

        $this->assertContains("Mapped user ID not found for user 4, quiz {$restoredquiz->id}, attempt 1. Skipping quiz attempt",
            $loginfomessages);
        $this->assertContains("Mapped user ID not found for user 5, quiz {$restoredquiz->id}, attempt 1. Skipping quiz attempt",
            $loginfomessages);

        // User 01 has supplied the wrong answer, assert dates match the backup file too.
        $user01attempt = $DB->get_record('quiz_attempts', [
            'quiz' => $restoredquiz->id,
            'userid' => core_user::get_user_by_username('user01')->id,
        ]);

        $this->assertEquals(1634751274, $user01attempt->timestart);
        $this->assertEquals(1634751290, $user01attempt->timefinish);
        $this->assertEquals(0.0, (float) $user01attempt->sumgrades);

        // User 04 has supplied the correct answer, assert dates match the backup file too.
        $user04attempt = $DB->get_record('quiz_attempts', [
            'quiz' => $restoredquiz->id,
            'userid' => core_user::get_user_by_username('user04')->id,
        ]);

        $this->assertEquals(1634751341, $user04attempt->timestart);
        $this->assertEquals(1634751347, $user04attempt->timefinish);
        $this->assertEquals(1.0, (float) $user04attempt->sumgrades);
    }
}
