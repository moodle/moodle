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
 * Tests of the scheduled task for cleaning up random questions.
 *
 * @package    qtype_random
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace qtype_random;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');


/**
 * Tests of the scheduled task for cleaning up random questions.
 *
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \qtype_random\task\remove_unused_questions
 */
final class cleanup_task_test extends \advanced_testcase {
    /**
     * Test that remove_unused_questions deletes questions as appropriate.
     *
     * @covers ::execute
     */
    public function test_cleanup_task_removes_unused_question(): void {
        global $DB, $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        // To do the test, we will be restoring a backup that contains 3 questions:
        // A non-hidden broken question, a hidden broken question, and a non-broken question.
        // Only the non-hidden broken question should be deleted,
        // because questions are hidden when a delete was attempted but failed,
        // and this is used to indicate we should skip over them in future deletes,
        // to avoid continuingly attempting to delete undeletable questions.

        // Extract backup file.
        $backupid = 'test_cleanup_task_removes_unused_question';
        $backuppath = make_backup_temp_directory($backupid);
        get_file_packer('application/vnd.moodle.backup')->extract_to_pathname(
            __DIR__ . '/fixtures/broken_question_course.mbz',
            $backuppath
        );

        // Do restore to new course with default settings.
        $categoryid = $DB->get_field_sql("SELECT MIN(id) FROM {course_categories}");
        $newcourseid = \restore_dbops::create_new_course('Broken Question Course', 'BQC', $categoryid);
        $rc = new \restore_controller(
            $backupid,
            $newcourseid,
            \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL,
            $USER->id,
            \backup::TARGET_NEW_COURSE
        );
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Check the hidden question was unhidden during the restore,
        // to make it eligible for deletion.
        $hiddenquestionid = $DB->get_field('question', 'id', ['name' => 'Random (BQC hidden broken question)']);
        $this->assertNotEquals(
            'hidden',
            $DB->get_field('question_versions', 'status', ['questionid' => $hiddenquestionid])
        );

        // Revert the hidden question back to hidden, so we can check it isn't deleted.
        $DB->set_field('question_versions', 'status', 'hidden', ['questionid' => $hiddenquestionid]);

        // Run the scheduled task.
        $task = new \qtype_random\task\remove_unused_questions();
        $this->expectOutputString("Cleaned up 1 unused random questions.\n");
        $task->execute();

        // Verify.
        $this->assertFalse(
            $DB->record_exists('question', ['name' => 'Random (BQC non-hidden broken question)'])
        );
        $this->assertTrue(
            $DB->record_exists('question', ['name' => 'Random (BQC hidden broken question)'])
        );
        $this->assertTrue(
            $DB->record_exists('question', ['name' => 'BQC non-broken question'])
        );
    }

    /**
     * Test that remove_unused_questions aborts when there is a course restore in progress.
     *
     * @covers ::execute
     */
    public function test_cleanup_task_checks_for_active_restores(): void {
        $this->resetAfterTest();

        // Get ready the tasks.
        $cleanuptask = new \qtype_random\task\remove_unused_questions();
        $restoretask = new \core\task\asynchronous_restore_task();
        \core\task\manager::queue_adhoc_task($restoretask);
        $copytask = new \core\task\asynchronous_copy_task();
        \core\task\manager::queue_adhoc_task($copytask);

        // Start the first adhoc task. This might be either restore or copy adhoc task.
        $task1 = \core\task\manager::get_next_adhoc_task(time());
        \core\task\manager::adhoc_task_starting($task1);
        $cleanuptask->execute();

        // Complete the first task and start the second one.
        \core\task\manager::adhoc_task_complete($task1);
        $task2 = \core\task\manager::get_next_adhoc_task(time());
        \core\task\manager::adhoc_task_starting($task2);
        $cleanuptask->execute();

        // Complete the second adhoc task.
        \core\task\manager::adhoc_task_complete($task2);
        $cleanuptask->execute();

        $aborted = 'Detected running async restore. Aborting the task.';
        $completed = 'Cleaned up 0 unused random questions.';
        $this->expectOutputRegex("/.*$aborted.*\s.*$aborted.*\s.*$completed.*/");
    }
}
