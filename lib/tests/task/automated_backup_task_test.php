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

namespace core\task;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/helper/backup_cron_helper.class.php');

/**
 * Class containing unit tests for the task do the automation backup and report.
 *
 * @package    core
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class automated_backup_task_test extends \advanced_testcase {

    use task_trait;

    /**
     * Test the automated backup and report tasks.
     *
     * @covers \core\task\automated_backup_report_task::execute
     * @covers \backup_cron_automated_helper::send_backup_status_to_admin
     * @covers \backup_cron_automated_helper::run_automated_backup
     * @covers \backup_cron_automated_helper::check_and_push_automated_backups
     */
    public function test_automated_backup(): void {
        global $DB;
        $this->resetAfterTest();

        // Enable automated back up.
        set_config(
            'backup_auto_active',
            true,
            'backup',
        );
        set_config(
            'backup_auto_weekdays',
            '1111111',
            'backup',
        );

        // Create courses.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        // Create course backups.
        $DB->insert_records(
            'backup_courses',
            [
                [
                    'courseid' => $course1->id,
                    'laststatus' => \backup_cron_automated_helper::BACKUP_STATUS_NOTYETRUN,
                    'nextstarttime' => time() - 10,
                ],
                [
                    'courseid' => $course2->id,
                    'laststatus' => \backup_cron_automated_helper::BACKUP_STATUS_NOTYETRUN,
                    'nextstarttime' => time() - 10,
                ],
            ],
        );

        // Verify that we don't have any running backup tasks.
        $this->assertEmpty(get_config('backup', 'backup_auto_adhoctasks'));
        $this->assertEmpty(get_config('backup', 'backup_auto_emailpending'));

        // Redirect messages to sink.
        $sink = $this->redirectMessages();
        // Trigger the automated backup scheduled task.
        $this->execute_task('\core\task\automated_backup_task');
        $messages = $sink->get_messages();
        $sink->close();

        // Scheduled task should not send report yet, because there are still running backup tasks.
        $this->assertCount(0, $messages);

        // Check that the backup tasks have been created.
        $this->assertTrue($DB->record_exists('backup_courses', ['courseid' => $course1->id]));
        $this->assertTrue($DB->record_exists('backup_courses', ['courseid' => $course2->id]));
        $this->assertNotEmpty(get_config('backup', 'backup_auto_adhoctasks'));
        $this->assertEquals(1, get_config('backup', 'backup_auto_emailpending'));

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        // Trigger the automated backup report scheduled task.
        $this->execute_task('\core\task\automated_backup_report_task');
        $messages = $sink->get_messages();
        $sink->close();

        // Scheduled task should not send report yet, because there are still running backup tasks.
        $this->assertCount(0, $messages);

        // Execute only one ad-hoc backup task.
        $value = get_config('backup', 'backup_auto_adhoctasks');
        $queuedtasks = explode(',', $value);
        $task = manager::get_adhoc_task($queuedtasks[0]);
        $this->start_output_buffering();
        $task->execute();
        $this->stop_output_buffering();
        manager::adhoc_task_complete($task);

        // Redirect messages to sink and stop buffer output from CLI task.
        $sink = $this->redirectMessages();
        // Trigger the automated backup report scheduled task.
        $this->execute_task('\core\task\automated_backup_report_task');
        $messages = $sink->get_messages();
        $sink->close();

        // Scheduled task should not send report yet, because there are still running backup tasks.
        $this->assertCount(0, $messages);

        // Execute the remaining ad-hoc backup task.
        $this->start_output_buffering();
        $this->runAdhocTasks('\core\task\course_backup_task');
        $this->stop_output_buffering();

        // Redirect messages to sink.
        $sink = $this->redirectMessages();
        // Trigger the automated backup report scheduled task.
        $this->execute_task('\core\task\automated_backup_report_task');
        $messages = $sink->get_messages();
        $sink->close();

        // Verify that all the backup tasks have been completed and all the configs have been cleared.
        $this->assertEmpty(get_config('backup', 'backup_auto_adhoctasks'));
        $this->assertEmpty(get_config('backup', 'backup_auto_emailpending'));
        // Verify that the report has been sent.
        $this->assertCount(1, $messages);
        $message = reset($messages);
        $this->assertEquals(get_admin()->id, $message->useridto);
        $this->assertEquals('backup', $message->eventtype);
    }
}
