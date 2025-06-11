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
use block_quickmail\tasks\queue_scheduled_notifications_task;

class block_quickmail_queue_scheduled_notifications_task_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        sets_up_notifications,
        sends_emails,
        sends_messages;

    public function test_send_all_ready_messages_task_sends_messages() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Create 4 notifications, 2 that should be sent now.
        $this->create_reminder_notifications_with_names($course, $userteacher, [
            ['name' => 'Reminder One', 'is_enabled' => 0],
            ['name' => 'Reminder Two', 'schedule_begin_at' => 1932448390],
            ['name' => 'Reminder Three'],
            ['name' => 'Reminder Four'],
        ]);

        \phpunit_util::run_all_adhoc_tasks();

        // Should be no tasks fire yet, so no emails.
        $this->assertEquals(0, $this->email_sink_email_count($sink));

        $task = new queue_scheduled_notifications_task();

        $task->execute();

        \phpunit_util::run_all_adhoc_tasks();
        $this->dispatch_queued_messages();

        // Should have executed the task, so 2 * 4 emails = 8.
        $this->assertEquals(8, $this->email_sink_email_count($sink));

        $this->close_email_sink($sink);
    }

    // Helpers.
    private function create_reminder_notifications_with_names($course, $user, $instanceparams = []) {
        foreach ($instanceparams as $params) {
            $this->create_reminder_notification_for_course_user('course-non-participation', $course, $user, null, $params);
        }
    }

}
