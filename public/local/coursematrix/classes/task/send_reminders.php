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
 * Scheduled task to send learning plan reminders and check overdue status.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coursematrix\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/coursematrix/lib.php');

/**
 * Send reminders scheduled task.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_reminders extends \core\task\scheduled_task {

    /**
     * Return the task's name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_sendreminders', 'local_coursematrix');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB, $CFG;

        mtrace('Starting learning plan reminders task...');

        // Check for overdue plans and get reminders to send.
        $reminders = local_coursematrix_check_overdue_and_reminders();

        if (empty($reminders)) {
            mtrace('No reminders to send.');
            return;
        }

        mtrace('Found ' . count($reminders) . ' reminder(s) to send.');

        // Load message API.
        require_once($CFG->dirroot . '/lib/messagelib.php');

        foreach ($reminders as $reminder) {
            $this->send_reminder($reminder);
        }

        mtrace('Finished sending reminders.');
    }

    /**
     * Send a reminder email to a user.
     *
     * @param object $reminder Reminder data with userid, planid, courseid, daysremaining, duedate
     */
    protected function send_reminder($reminder) {
        global $DB;

        $user = $DB->get_record('user', ['id' => $reminder->userid]);
        $plan = $DB->get_record('local_coursematrix_plans', ['id' => $reminder->planid]);
        $course = $DB->get_record('course', ['id' => $reminder->courseid]);

        if (!$user || !$plan || !$course) {
            mtrace('  Skipping reminder - missing data for user ' . $reminder->userid);
            return;
        }

        // Build message.
        $a = new \stdClass();
        $a->username = fullname($user);
        $a->planname = $plan->name;
        $a->coursename = $course->fullname;
        $a->daysremaining = $reminder->daysremaining;
        $a->duedate = userdate($reminder->duedate);

        $subject = get_string('remindersubject', 'local_coursematrix', $a);
        $body = get_string('reminderbody', 'local_coursematrix', $a);

        // Create message object.
        $message = new \core\message\message();
        $message->component = 'local_coursematrix';
        $message->name = 'planreminder';
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = $user;
        $message->subject = $subject;
        $message->fullmessage = $body;
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml = '<p>' . nl2br(s($body)) . '</p>';
        $message->smallmessage = $subject;
        $message->notification = 1;
        $message->contexturl = new \moodle_url('/course/view.php', ['id' => $course->id]);
        $message->contexturlname = $course->fullname;

        // Send via Moodle's messaging system (uses SMTP).
        $messageid = message_send($message);

        if ($messageid) {
            mtrace('  Sent reminder to ' . $user->email . ' for course "' . $course->fullname . '"');
        } else {
            mtrace('  FAILED to send reminder to ' . $user->email);
        }
    }
}
