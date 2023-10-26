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

namespace core_message\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use external_api;
use externallib_advanced_testcase;
use \core_message\tests\helper as testhelper;

/**
 * External function test for get_unread_notification_count.
 *
 * @package    core_message
 * @category   test
 * @copyright  2021 Dani Palou <dani@moodle.com>, based on Ryan Wyllie <ryan@moodle.com> code
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.0
 */
class get_unread_notification_count_test extends externallib_advanced_testcase {

    /**
     * get_unread_notification should throw an exception for an invalid user.
     */
    public function test_get_unread_notification_count_invalid_user_exception(): void {
        $this->resetAfterTest(true);

        $this->expectException('moodle_exception');
        $result = get_unread_notification_count::execute(-2132131);
    }

    /**
     * get_unread_notification_count should throw exception if being requested for non-logged in user.
     */
    public function test_get_unread_notification_count_access_denied_exception(): void {
        $this->resetAfterTest(true);

        $sender = $this->getDataGenerator()->create_user();
        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);
        $this->expectException('moodle_exception');
        $result = get_unread_notification_count::execute($sender->id);
    }

    /**
     * Test get_unread_notification_count.
     */
    public function test_get_unread_notification_count(): void {
        $this->resetAfterTest(true);

        $sender1 = $this->getDataGenerator()->create_user();
        $sender2 = $this->getDataGenerator()->create_user();
        $sender3 = $this->getDataGenerator()->create_user();
        $recipient = $this->getDataGenerator()->create_user();

        $this->setUser($recipient);

        $notificationids = [
            testhelper::send_fake_unread_notification($sender1, $recipient, 'Notification', 1),
            testhelper::send_fake_unread_notification($sender1, $recipient, 'Notification', 2),
            testhelper::send_fake_unread_notification($sender2, $recipient, 'Notification', 3),
            testhelper::send_fake_unread_notification($sender2, $recipient, 'Notification', 4),
            testhelper::send_fake_unread_notification($sender3, $recipient, 'Notification', 5),
            testhelper::send_fake_unread_notification($sender3, $recipient, 'Notification', 6),
        ];

        $count = get_unread_notification_count::execute($recipient->id);
        $this->assertEquals($count, 6);

    }

}
