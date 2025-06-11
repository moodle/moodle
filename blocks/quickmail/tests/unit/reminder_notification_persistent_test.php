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

use block_quickmail\persistents\reminder_notification;
use block_quickmail\persistents\notification;
use block_quickmail\persistents\schedule;
use block_quickmail\persistents\interfaces\notification_type_interface;
use block_quickmail\notifier\models\reminder_notification_model;

class block_quickmail_reminder_notification_persistent_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        sets_up_notifications;

    public function test_has_a_notification_type_key() {
        $this->assertEquals('reminder', reminder_notification::$notificationtypekey);
    }

    public function test_creates_a_reminder_notification() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Create.
        $remindernotification = reminder_notification::create_type('course-non-participation',
            $course,
            $userteacher,
            $this->get_reminder_notification_params(),
            $course);

        $this->assertInstanceOf(reminder_notification::class, $remindernotification);
        $this->assertEquals('course-non-participation', $remindernotification->get('model'));
        $this->assertEquals($course->id, $remindernotification->get('object_id'));
        $this->assertEquals($this->get_reminder_notification_params('max_per_interval'),
                            $remindernotification->get('max_per_interval'));
        $this->assertEquals($this->get_reminder_notification_params('max_per_interval'),
                            $remindernotification->max_per_interval());
        $this->assertEquals(null, $remindernotification->get('last_run_at'));
        $this->assertTrue($remindernotification->is_schedulable());
        $this->assertEquals('Course Non-Participation', $remindernotification->get_title());

        // Get notification from reminder_notification.
        $notification = $remindernotification->get_notification();

        $this->assertInstanceOf(notification::class, $notification);
        $this->assertEquals($course->id, $notification->get('course_id'));
        $this->assertEquals($userteacher->id, $notification->get('user_id'));
        $this->assertEquals('reminder', $notification->get('type'));
        $this->assertEquals($this->get_reminder_notification_params('name'), $notification->get('name'));
        $this->assertEquals($this->get_reminder_notification_params('is_enabled'), $notification->get('is_enabled'));
        $this->assertEquals('time_amount:4,time_unit:day', $notification->get('conditions'));
        $this->assertEquals($this->get_reminder_notification_params('message_type'), $notification->get('message_type'));
        $this->assertEquals($this->get_reminder_notification_params('alternate_email_id'),
                            $notification->get('alternate_email_id'));
        $this->assertEquals($this->get_reminder_notification_params('signature_id'),
                            $notification->get('signature_id'));
        $this->assertEquals($this->get_reminder_notification_params('subject'), $notification->get('subject'));
        $this->assertEquals($this->get_reminder_notification_params('body'), $notification->get('body'));
        $this->assertEquals($this->get_reminder_notification_params('editor_format'), $notification->get('editor_format'));
        $this->assertEquals($this->get_reminder_notification_params('send_receipt'), $notification->get('send_receipt'));
        $this->assertEquals($this->get_reminder_notification_params('send_to_mentors'), $notification->get('send_to_mentors'));
        $this->assertEquals($this->get_reminder_notification_params('no_reply'), $notification->get('no_reply'));
        $this->assertEquals(reminder_notification::class, $notification->get_notification_type_interface_persistent_class_name());

        // Get notification interface (reminder) from notification.
        $notificationtypeinterface = $notification->get_notification_type_interface();

        $this->assertInstanceOf(notification_type_interface::class, $notificationtypeinterface);
        $this->assertEquals($notificationtypeinterface->get('notification_id'), $notification->get('id'));

        // Get schedule from reminder_notification.
        $schedule = $remindernotification->get_schedule();

        $this->assertInstanceOf(schedule::class, $schedule);
        $this->assertEquals($schedule->get('id'), $remindernotification->get('schedule_id'));
    }

    public function test_gets_a_reminder_notification_model_from_reminder_notification() {
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

        $remindernotificationmodel = $remindernotification->get_notification_model();

        $this->assertInstanceOf(reminder_notification_model::class, $remindernotificationmodel);
    }

}
