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

namespace message_popup;

use core\task\messaging_cleanup_task;
use message_popup_test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/message/output/popup/tests/base.php');

/**
 * Test class
 *
 * @package     message_popup
 * @category    test
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class messaging_cleanup_test extends \advanced_testcase {

    // Helper trait for sending fake popup notifications.
    use message_popup_test_helper;

    /**
     * Test that all popup notifications are cleaned up
     *
     * @return void
     */
    public function test_cleanup_all_notifications(): void {
        global $DB;

        $this->resetAfterTest();

        $userfrom = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();

        $now = time();

        $this->send_fake_unread_popup_notification($userfrom, $userto, 'Message 1', $now - 10);
        $notificationid = $this->send_fake_unread_popup_notification($userfrom, $userto, 'Message 2', $now);

        // Sanity check.
        $this->assertEquals(2, $DB->count_records('message_popup_notifications'));

        // Delete all notifications >5 seconds old.
        set_config('messagingdeleteallnotificationsdelay', 5);
        (new messaging_cleanup_task())->execute();

        // We should have just one record now, matching the second notification we sent.
        $records = $DB->get_records('message_popup_notifications');
        $this->assertCount(1, $records);
        $this->assertEquals($notificationid, reset($records)->notificationid);
    }

    /**
     * Test that read popup notifications are cleaned up
     *
     * @return void
     */
    public function test_cleanup_read_notifications(): void {
        global $DB;

        $this->resetAfterTest();

        $userfrom = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();

        $now = time();

        $this->send_fake_read_popup_notification($userfrom, $userto, 'Message 1', $now - 20, $now - 10);
        $notificationid = $this->send_fake_read_popup_notification($userfrom, $userto, 'Message 2', $now - 15, $now);

        // Sanity check.
        $this->assertEquals(2, $DB->count_records('message_popup_notifications'));

        // Delete read notifications >5 seconds old.
        set_config('messagingdeletereadnotificationsdelay', 5);
        (new messaging_cleanup_task())->execute();

        // We should have just one record now, matching the second notification we sent.
        $records = $DB->get_records('message_popup_notifications');
        $this->assertCount(1, $records);
        $this->assertEquals($notificationid, reset($records)->notificationid);
    }
}
