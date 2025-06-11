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
use block_quickmail\tasks\send_message_adhoc_task;
use core\task\manager as task_manager;

class block_quickmail_send_message_adhoc_task_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        submits_compose_message_form,
        sends_emails,
        sends_messages;

    public function test_send_message_adhoc_task_sends() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Specify recipients.
        $recipients['included']['user'] = $this->get_user_ids_from_user_array($userstudents);

        $sendtime = time() + 10000;

        /*
         *  Segun Babalola, 2020-10-31
         *
         *  Since the very action of creating messages with a "to_send_at" value of "now" will
         *  mean the messages are sent immediately after creation (see call chain
         *  $this->create_messages() => messenger::compose() => self::send_message_to_recipients()),
         *  some of the assertions below is failing.
         *
         *  I'm modifying this test to create the message for a future send time so that the
         *  test is more appropriate.
         *
         */

        // Get a compose form submission, sending message now.
        $composeformdata = $this->get_compose_message_form_submission($recipients, 'email', [
            'to_send_at' => $sendtime
        ]);

        // Schedule an email from the teacher to the students (as queued adhoc tasks).
        $message = messenger::compose($userteacher, $course, $composeformdata, null, true);

        \phpunit_util::run_all_adhoc_tasks();

        // Should be no tasks fire yet, so no emails.
        $this->assertEquals(0, $this->email_sink_email_count($sink));

        $task = new send_message_adhoc_task();

        $task->set_custom_data([
            'message_id' => $message->get('id')
        ]);

        // Queue job.
        task_manager::queue_adhoc_task($task);

        \phpunit_util::run_all_adhoc_tasks();

        // Should have executed the taks, so 4 emails.
        $this->assertEquals(4, $this->email_sink_email_count($sink));

        $this->close_email_sink($sink);
    }

}
