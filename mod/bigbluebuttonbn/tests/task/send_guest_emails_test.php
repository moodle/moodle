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

namespace mod_bigbluebuttonbn\task;

use advanced_testcase;

/**
 * Send guest email tests
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2019 onwards, Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_bigbluebuttonbn\task\send_guest_emails
 * @coversDefaultClass \mod_bigbluebuttonbn\task\send_guest_emails
 */
final class send_guest_emails_test extends advanced_testcase {
    /**
     * Check if set instance ID works correctly
     *
     */
    public function test_send_emails(): void {
        $this->resetAfterTest();
        $emailsink = $this->redirectEmails();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $instancedata = $generator->create_module('bigbluebuttonbn', [
                'course' => $course->id,
        ]);
        $moderatoruser = $generator->create_user();

        $guestemail = new send_guest_emails();
        $guestemail->set_custom_data(
                [
                        'emails' => ['test1@email.com', 'test2@email.com'],
                        'useridfrom' => $moderatoruser->id
                ]
        );
        $guestemail->set_instance_id($instancedata->id);
        \core\task\manager::queue_adhoc_task($guestemail);
        $this->runAdhocTasks();

        // Check the events.
        $messages = $emailsink->get_messages();
        $this->assertCount(2, $messages);

        $this->assertEquals('Invitation: BigBlueButton 1 session in Test course 1', $messages[0]->subject);
        $this->assertEquals('noreply@www.example.com', $messages[0]->from);
        $this->assertEquals('test1@email.com', $messages[0]->to);
        $this->assertEquals('test2@email.com', $messages[1]->to);

    }
}
