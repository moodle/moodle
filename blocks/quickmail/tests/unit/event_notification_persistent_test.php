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

use block_quickmail\persistents\event_notification;
use block_quickmail\persistents\notification;
use block_quickmail\notifier\models\event_notification_model;

class block_quickmail_event_notification_persistent_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        sets_up_notifications;

    public function test_has_a_notification_type_key() {
        $this->assertEquals('event', event_notification::$notificationtypekey);
    }

    public function test_creates_an_event_notification() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Create.
        $eventnotification = event_notification::create_type('course-entered',
            $course,
            $userteacher,
            $this->get_event_notification_params(),
            $course);

        $this->assertInstanceOf(event_notification::class, $eventnotification);
        $this->assertEquals('course-entered', $eventnotification->get('model'));
        $this->assertEquals(5, $eventnotification->get('time_delay_amount'));
        $this->assertEquals('minute', $eventnotification->get('time_delay_unit'));
        $this->assertEquals(2, $eventnotification->get('mute_time_amount'));
        $this->assertEquals('day', $eventnotification->get('mute_time_unit'));
        $this->assertFalse($eventnotification->is_schedulable());

        // Test getters.

        // 5 minutes = 300 seconds.
        $this->assertEquals(300, $eventnotification->time_delay());
        // 2 days = 172800 seconds.
        $this->assertEquals(172800, $eventnotification->mute_time());
        // May be some race conditions for these two.
        // Send time = now + time_delay.
        $this->assertEquals(time() + $eventnotification->time_delay(), $eventnotification->calculated_send_time());
        // Send time = now + time_delay - mute_time.
        $this->assertEquals(time() + $eventnotification->time_delay() - $eventnotification->mute_time(),
            $eventnotification->next_available_send_time());

        // Get notification from event_notification.
        $notification = $eventnotification->get_notification();

        $this->assertInstanceOf(notification::class, $notification);
        $this->assertEquals($course->id, $notification->get('course_id'));
        $this->assertEquals($userteacher->id, $notification->get('user_id'));
        $this->assertEquals('event', $notification->get('type'));
        $this->assertEquals($this->get_event_notification_params('name'), $notification->get('name'));
        $this->assertEquals($this->get_event_notification_params('is_enabled'), $notification->get('is_enabled'));
        $this->assertEquals($this->get_event_notification_params('conditions'), $notification->get('conditions'));
        $this->assertEquals($this->get_event_notification_params('message_type'), $notification->get('message_type'));
        $this->assertEquals($this->get_event_notification_params('alternate_email_id'), $notification->get('alternate_email_id'));
        $this->assertEquals($this->get_event_notification_params('signature_id'), $notification->get('signature_id'));
        $this->assertEquals($this->get_event_notification_params('subject'), $notification->get('subject'));
        $this->assertEquals($this->get_event_notification_params('body'), $notification->get('body'));
        $this->assertEquals($this->get_event_notification_params('editor_format'), $notification->get('editor_format'));
        $this->assertEquals($this->get_event_notification_params('send_receipt'), $notification->get('send_receipt'));
        $this->assertEquals($this->get_event_notification_params('send_to_mentors'), $notification->get('send_to_mentors'));
        $this->assertEquals($this->get_event_notification_params('no_reply'), $notification->get('no_reply'));

        $notificationtypeinterface = $notification->get_notification_type_interface();

        $this->assertInstanceOf(event_notification::class, $notificationtypeinterface);
    }

    public function test_creates_an_event_notification_with_no_time_delay() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Create.
        $eventnotification = event_notification::create_type('course-entered',
            $course,
            $userteacher,
            $this->get_event_notification_params([], [
                'time_delay_amount' => '',
                'time_delay_unit' => '',
            ]),
            $course);

        $this->assertEquals(0, $eventnotification->get('time_delay_amount'));
        $this->assertEquals(null, $eventnotification->get('time_delay_unit'));
        $this->assertEquals(2, $eventnotification->get('mute_time_amount'));
        $this->assertEquals('day', $eventnotification->get('mute_time_unit'));

        // Test getters.
        $this->assertEquals(0, $eventnotification->time_delay());

        // May be some race conditions for these two....
        // Send time = now.
        $this->assertEquals(time(), $eventnotification->calculated_send_time());
        // Send time = now + time_delay - mute_time.
        $this->assertEquals(time() - $eventnotification->mute_time(), $eventnotification->next_available_send_time());
    }

    public function test_creates_an_event_notification_with_no_mute_time() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Create.
        $eventnotification = event_notification::create_type('course-entered',
            $course,
            $userteacher,
            $this->get_event_notification_params([], [
                'mute_time_amount' => '',
                'mute_time_unit' => '',
            ]),
            $course);

        $this->assertEquals(5, $eventnotification->get('time_delay_amount'));
        $this->assertEquals('minute', $eventnotification->get('time_delay_unit'));
        $this->assertEquals(0, $eventnotification->get('mute_time_amount'));
        $this->assertEquals(null, $eventnotification->get('mute_time_unit'));

        // Test getters.
        $this->assertEquals(0, $eventnotification->mute_time());

        // May be some race conditions for these two.
        // Send time = now + time_delay.
        $this->assertEquals(time() + $eventnotification->time_delay(), $eventnotification->calculated_send_time());
        // Send time = now + time_delay.
        $this->assertEquals(time() + $eventnotification->time_delay(), $eventnotification->next_available_send_time());
    }

    public function test_gets_an_event_notification_model_from_event_notification() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Create an event notification.
        $eventnotification = event_notification::create_type('course-entered',
            $course,
            $userteacher,
            $this->get_event_notification_params(),
            $course);

        $eventnotificationmodel = $eventnotification->get_notification_model();

        $this->assertInstanceOf(event_notification_model::class, $eventnotificationmodel);
    }

}
