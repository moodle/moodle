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

use block_quickmail\persistents\notification;

class block_quickmail_notification_persistent_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        sets_up_notifications;

    public function test_get_all_ready_scheduled() {
        $this->resetAfterTest(true);

        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Create some notifications.
        $this->create_reminder_notifications_with_names($course, $userteacher, [
            ['name' => 'Reminder One', 'is_enabled' => 0],
            ['name' => 'Reminder Two', 'schedule_begin_at' => 1932448390],
            ['name' => 'Reminder Three'],
            ['name' => 'Reminder Four'],
        ]);

        $notifications = notification::get_all_ready_schedulables();

        $this->assertCount(2, $notifications);
        $this->assertInstanceOf(notification::class, $notifications[0]);
        $this->assertEquals('Reminder Three', $notifications[0]->get('name'));
    }

    // Helpers.
    private function create_reminder_notifications_with_names($course, $user, $instanceparams = []) {
        foreach ($instanceparams as $params) {
            $this->create_reminder_notification_for_course_user('course-non-participation', $course, $user, null, $params);
        }
    }

}
