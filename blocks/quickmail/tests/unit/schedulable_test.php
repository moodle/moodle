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

use block_quickmail\persistents\schedule;
use block_quickmail\persistents\reminder_notification;
use block_quickmail\persistents\interfaces\schedulable_interface;

class block_quickmail_schedulable_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        sets_up_notifications;

    public function test_gets_schedule_from_schedulable() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Create schedulable (reminder_notification).
        $schedulable = $this->create_test_schedulable_reminder_notification([
            'unit' => 'week',
            'amount' => 1,
            'begin_at' => $this->get_soon_time(),
            'end_at' => null,
        ]);

        $schedule = $schedulable->get_schedule();

        $this->assertInstanceOf(schedule::class, $schedule);
    }

    public function test_schedulable_getters() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Create schedulable (reminder_notification) with default creation params.
        $schedulable = $this->create_test_schedulable_reminder_notification([
            'unit' => 'week',
            'amount' => 1,
            'begin_at' => $this->get_soon_time(),
            'end_at' => $this->get_future_time(),
        ]);

        $this->assertNull($schedulable->get_last_run_time());
        $this->assertNull($schedulable->get_next_run_time());
        $this->assertFalse($schedulable->is_running());
    }

    public function test_sets_next_run_time_for_never_run_schedulable() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $soon = $this->get_soon_time();

        // Create schedulable (reminder_notification) with default creation params.
        $schedulable = $this->create_test_schedulable_reminder_notification([
            'unit' => 'week',
            'amount' => 1,
            'begin_at' => $this->get_soon_time(),
            'end_at' => $this->get_future_time(),
        ]);

        $schedulable->set_next_run_time();

        $this->assertEquals($soon, $schedulable->get_next_run_time());
        $this->assertNull($schedulable->get_last_run_time());

        // Attempt to set the next run time again, should not change.
        $schedulable->set_next_run_time();

        $this->assertEquals($soon, $schedulable->get_next_run_time());
    }

    public function test_increments_next_run_time_for_non_expired_schedule() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $begin = $this->get_timestamp_for_date('may 13 2018 08:30:00');

        // Create schedulable (reminder_notification).
        $schedulable = $this->create_test_schedulable_reminder_notification([
            'unit' => 'week',
            'amount' => 1,
            'begin_at' => $begin,
            'end_at' => $this->get_timestamp_for_date('may 17 2020'),
        ]);

        $schedulable->set_next_run_time();
        $this->assertEquals($begin, $schedulable->get_next_run_time());

        // Mark this schedulable as having been run once.
        $lastrun = $this->get_timestamp_for_date('may 13 2018 09:00:00');
        $schedulable = $this->update_schedulable_reminder_notification_last_run_time($schedulable, $lastrun);
        $this->assertEquals($lastrun, $schedulable->get_last_run_time());

        $schedulable->set_next_run_time();

        // Segun Babalola, 2020-10-30.
        // Next run should be 1 week from last run time time.
        $secondsinweek = (7 * 24 * 60 * 60);
        $nextrun = $lastrun + $secondsinweek;

        $this->assertEquals($nextrun, $schedulable->get_next_run_time());
    }

    public function test_nulls_next_run_time_for_expired_schedule() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $begin = $this->get_timestamp_for_date('may 5 2018 08:30:00');
        $end = $this->get_timestamp_for_date('may 10 2018 08:30:00');

        // Create schedulable (reminder_notification).
        $schedulable = $this->create_test_schedulable_reminder_notification([
            'unit' => 'week',
            'amount' => 1,
            'begin_at' => $begin,
            'end_at' => $end,
        ]);

        $schedulable->set_next_run_time();
        $this->assertEquals($begin, $schedulable->get_next_run_time());

        // Mark this schedulable as having been run once.
        $lastrun = $this->get_timestamp_for_date('may 5 2018 09:00:00');
        $schedulable = $this->update_schedulable_reminder_notification_last_run_time($schedulable, $lastrun);

        $schedulable->set_next_run_time();

        // Next run should be null since schedule has expired.
        $this->assertNull($schedulable->get_next_run_time());
    }

    public function test_sets_next_run_time_when_created() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Create a reminder notification to run soon.
        $remindernotification = reminder_notification::create_type('course-non-participation',
            $course,
            $userteacher,
            $this->get_reminder_notification_params([], [
                'schedule_unit' => 'week',
                'schedule_amount' => 2,
                'schedule_begin_at' => $this->get_soon_time()
            ]),
            $course);

        // Next run time should be soon.
        $this->assertEquals($remindernotification->get_next_run_time(), $this->get_soon_time());

        // Create a reminder notification to run past.
        $remindernotification = reminder_notification::create_type('course-non-participation',
            $course,
            $userteacher,
            $this->get_reminder_notification_params([], [
                'schedule_unit' => 'week',
                'schedule_amount' => 2,
                'schedule_begin_at' => $this->get_past_time()
            ]),
            $course);

        // Next run time should be past.
        $this->assertEquals($remindernotification->get_next_run_time(), $this->get_past_time());
    }

    // Helpers.
    /**
     * Generates a reminder notification for an internally generated course/teacher with default params,
     * and explicit schedule params
     *
     * @param  array  $scheduleparams  (unit,amount,begin_at,end_at)
     * @return int  reminder_notification id
     */
    private function create_test_schedulable_reminder_notification($scheduleparams) {
        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        global $DB;

        $params = $this->get_reminder_notification_params();
        $now = time();

        // Create the parent notification record.
        $notification = new \stdClass();
        $notification->name = $params['name'];
        $notification->type = 'reminder';
        $notification->course_id = $course->id;
        $notification->user_id = $userteacher->id;
        $notification->is_enabled = $params['is_enabled'];
        $notification->conditions = $params['conditions'];
        $notification->message_type = $params['message_type'];
        $notification->alternate_email_id = $params['alternate_email_id'];
        $notification->subject = $params['subject'];
        $notification->signature_id = $params['signature_id'];
        $notification->body = $params['body'];
        $notification->editor_format = $params['editor_format'];
        $notification->send_receipt = $params['send_receipt'];
        $notification->send_to_mentors = $params['send_to_mentors'];
        $notification->no_reply = $params['no_reply'];
        $notification->usermodified = $userteacher->id;
        $notification->timecreated = $now;
        $notification->timemodified = $now;
        $notification->timedeleted = 0;
        $notification->no_reply = $params['no_reply'];
        $notificationid = $DB->insert_record('block_quickmail_notifs', $notification, true);

        // Create the schedule record.
        $schedule = new \stdClass();
        $schedule->unit = $scheduleparams['unit'];
        $schedule->amount = $scheduleparams['amount'];
        $schedule->begin_at = $scheduleparams['begin_at'];
        $schedule->end_at = $scheduleparams['end_at'];
        $schedule->usermodified = $userteacher->id;
        $schedule->timecreated = $now;
        $schedule->timemodified = $now;
        $schedule->timedeleted = 0;
        $scheduleid = $DB->insert_record('block_quickmail_schedules', $schedule, true);

        // Create the schedulable reminder notification record.
        $schedulable = new \stdClass();
        $schedulable->notification_id = $notificationid;
        $schedulable->type = 'course-non-participation';
        $schedulable->object_id = $course->id;
        $schedulable->max_per_interval = $params['max_per_interval'];
        $schedulable->schedule_id = $scheduleid;
        $schedulable->last_run_at = null;
        $schedulable->next_run_at = null;
        $schedulable->usermodified = $userteacher->id;
        $schedulable->timecreated = $now;
        $schedulable->timemodified = $now;
        $schedulable->timedeleted = 0;
        $schedulableid = $DB->insert_record('block_quickmail_rem_notifs', $schedulable, true);

        return reminder_notification::find_or_null($schedulableid);
    }

    public function update_schedulable_reminder_notification_last_run_time($schedulable, $timestamp) {
        $data = $schedulable->to_record();

        global $DB;

        $data->last_run_at = $timestamp;

        $DB->update_record('block_quickmail_rem_notifs', $data);

        return reminder_notification::find_or_null($data->id);
    }

}
