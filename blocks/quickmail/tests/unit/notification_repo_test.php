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

use block_quickmail\repos\notification_repo;
use block_quickmail\persistents\notification;
use block_quickmail\repos\pagination\paginated;

class block_quickmail_notification_repo_testcase extends advanced_testcase {
    use has_general_helpers,
        sets_up_courses,
        sets_up_notifications;

    public function test_get_all_for_course() {
        $this->resetAfterTest(true);
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Create some notifications.
        $this->create_reminder_notifications_with_names($course, $userteacher, [
            ['name' => 'Reminder One'],
            ['name' => 'Reminder Two'],
            ['name' => 'Reminder Three'],
            ['name' => 'Reminder Four'],
        ]);

        $notifications = notification_repo::get_all_for_course($course->id);
        $this->assertCount(4, $notifications->data);
    }

    public function test_gets_paginated_results_for_user() {
        $this->resetAfterTest(true);
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Create some notifications.
        $this->create_reminder_notifications_with_names($course, $userteacher, [
            ['name' => 'Reminder One'],
            ['name' => 'Reminder Two'],
            ['name' => 'Reminder Three'],
            ['name' => 'Reminder Four'],
            ['name' => 'Reminder Five'],
            ['name' => 'Reminder Six'],
            ['name' => 'Reminder Seven'],
            ['name' => 'Reminder Eight'],
        ]);

        // Sort by id, paginated.
        $notifications = notification_repo::get_all_for_course($course->id, $userteacher->id, [
            'sort' => 'id',
            'dir' => 'asc',
            'paginate' => true,
            'page' => '2',
            'per_page' => '4',
            'uri' => '/blocks/quickmail/notifications.php?courseid=' . $course->id . '&sort=id&dir=asc',
        ]);

        $this->assertCount(4, $notifications->data);
        $this->assertInstanceOf(paginated::class, $notifications->pagination);
        $this->assertEquals(2, $notifications->pagination->page_count);
        $this->assertEquals(4, $notifications->pagination->offset);
        $this->assertEquals(4, $notifications->pagination->per_page);
        $this->assertEquals(2, $notifications->pagination->current_page);
        $this->assertEquals(2, $notifications->pagination->next_page);
        $this->assertEquals(1, $notifications->pagination->previous_page);
        $this->assertEquals(8, $notifications->pagination->total_count);
        $this->assertEquals('/blocks/quickmail/notifications.php?courseid=' . $course->id . '&sort=id&dir=asc&page=2',
            $notifications->pagination->uri_for_page);
        $this->assertEquals('/blocks/quickmail/notifications.php?courseid=' . $course->id . '&sort=id&dir=asc&page=1',
            $notifications->pagination->first_page_uri);
        $this->assertEquals('/blocks/quickmail/notifications.php?courseid=' . $course->id . '&sort=id&dir=asc&page=2',
            $notifications->pagination->last_page_uri);
        $this->assertEquals('/blocks/quickmail/notifications.php?courseid=' . $course->id . '&sort=id&dir=asc&page=2',
            $notifications->pagination->next_page_uri);
        $this->assertEquals('/blocks/quickmail/notifications.php?courseid=' . $course->id . '&sort=id&dir=asc&page=1',
            $notifications->pagination->previous_page_uri);

        // Sort by name, paginated.
        $notifications = notification_repo::get_all_for_course($course->id, null, [
            'sort' => 'name',
            'dir' => 'asc',
            'paginate' => true,
            'page' => '1',
            'per_page' => '6',
            'uri' => '/blocks/quickmail/notifications.php?courseid=' . $course->id . '&sort=name&dir=asc',
        ]);

        $this->assertEquals('Reminder Eight', $notifications->data[0]->get('name'));
        $this->assertEquals('Reminder Five', $notifications->data[1]->get('name'));
        $this->assertEquals('Reminder Four', $notifications->data[2]->get('name'));
        $this->assertEquals('Reminder One', $notifications->data[3]->get('name'));
        $this->assertEquals('Reminder Seven', $notifications->data[4]->get('name'));
        $this->assertEquals('Reminder Six', $notifications->data[5]->get('name'));
    }

    public function test_get_for_course_user() {
        $this->resetAfterTest(true);
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Create a notification.
        $remindernotification = $this->create_reminder_notification_for_course_user('course-non-participation',
                                                                                    $course,
                                                                                    $userteacher);

        // Attempt to fetch this notification by the creator.
        $notification = notification_repo::get_notification_for_course_user_or_null(
                            $remindernotification->get_notification()->get('id'),
                            $course->id,
                            $userteacher->id);

        $this->assertInstanceOf(notification::class, $notification);
        $this->assertEquals($remindernotification->get_notification()->get('id'), $notification->get('id'));

        // Attempt to fetch this notification by a student.
        $notification = notification_repo::get_notification_for_course_user_or_null(
                            $remindernotification->get_notification()->get('id'),
                            $course->id,
                            $userstudents[0]->id);

        $this->assertNull($notification);
    }

    private function create_reminder_notifications_with_names($course, $user, $instanceparams = []) {
        foreach ($instanceparams as $params) {
            $this->create_reminder_notification_for_course_user('course-non-participation', $course, $user, null, $params);
        }
    }

}
