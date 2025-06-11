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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

use block_quickmail\messenger\messenger;
use block_quickmail\persistents\reminder_notification;
use block_quickmail\tasks\run_schedulable_notification_adhoc_task;
use core\task\manager as task_manager;

class block_quickmail_run_schedulable_notification_adhoc_task_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        sets_up_notifications,
        sends_emails,
        sends_messages;

    public function test_runs_scheduled_via_adhoc_task() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $remindernotification = $this->create_reminder_notification_for_course_user('course-non-participation',
                                                                                    $course,
                                                                                    $userteacher,
                                                                                    null,
                                                                                    ['name' => 'My non participation reminder']);

        \phpunit_util::run_all_adhoc_tasks();

        // Should not have run yet.
        $this->assertNull($remindernotification->get('last_run_at'));
        $this->assertNotNull($remindernotification->get('next_run_at'));

        // Should be no tasks fire yet, so no emails.
        $this->assertEquals(0, $this->email_sink_email_count($sink));

        $task = new run_schedulable_notification_adhoc_task();

        $task->set_custom_data([
            'notification_id' => $remindernotification->get_notification()->get('id')
        ]);

        // Queue and run job.
        task_manager::queue_adhoc_task($task);
        \phpunit_util::run_all_adhoc_tasks();
        $this->dispatch_queued_messages();

        // Get the updated reminder notification for checking calculating run times.
        $updatedremindernotification = reminder_notification::find_or_null($remindernotification->get('id'));

        // Should have run.
        $this->assertNotNull($updatedremindernotification->get('last_run_at'));
        $this->assertGreaterThan((int) $remindernotification->get('next_run_at'),
            (int) $updatedremindernotification->get('next_run_at'));

        // Should have executed the taks, so 4 emails.
        $this->assertEquals(4, $this->email_sink_email_count($sink));

        $this->close_email_sink($sink);
    }

}
