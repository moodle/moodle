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

namespace message_sms;

use core_sms\message;
use core_sms\message_status;
use core_sms\task\send_sms_task;
use mod_assign\notification_helper;

/**
 * SMS processor tests.
 *
 * @package    message_sms
 * @copyright  2025 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \message_output_sms
 */
final class message_output_sms_test extends \advanced_testcase {

    /**
     * Test the SMS output for the SMS processor.
     */
    public function test_sms_output(): void {
        global $CFG;
        require_once($CFG->dirroot . "/sms/tests/fixtures/dummy_gateway.php");

        $this->preventResetByRollback();
        $this->resetAfterTest();

        $config = new \stdClass();
        $config->priority = 50;

        $manager = \core\di::get(\core_sms\manager::class);
        $manager->create_gateway_instance(
            classname: \smsgateway_dummy\gateway::class,
            name: 'dummy',
            enabled: true,
            config: $config,
        );

        // Set the configs and enable SMS.
        \core\plugininfo\message::enable_plugin(
            pluginname: 'sms',
            enabled: 1,
        );
        // We have to use assign because this is the only component supports sms at the moment.
        set_config('message_provider_mod_assign_assign_due_digest_enabled', 'sms', 'message');

        $user = self::getDataGenerator()->create_user();
        $user->phone2 = '+61000000000';

        $user2 = self::getDataGenerator()->create_user();
        $CFG->noreplyuser = $user2->id;

        // Send a notification.
        $message = new \core\message\message();
        $message->component = 'mod_assign';
        $message->name = notification_helper::TYPE_DUE_DIGEST;
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = $user;
        $message->subject = 'Hello';
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessage = 'Hello message';
        $message->fullmessagehtml = 'Hello message';
        $message->smallmessage = 'Hello message';
        $message->fullmessagesms = 'Hello sms message';
        $message->notification = 1;

        message_send($message);

        $messagedbrecords = $manager->get_messages();
        $this->assertInstanceOf(\Generator::class, $messagedbrecords);
        $messages = iterator_to_array($messagedbrecords);
        $this->assertCount(1, $messages);

        $message = $messages[0];
        $this->assertInstanceOf(message::class, $message);
        $this->assertIsInt($message->id);
        $this->assertEquals(message_status::GATEWAY_QUEUED, $message->status);
        $this->assertEquals('Hello sms message', $message->content);

        $storedmessage = $manager->get_message(['id' => $message->id]);
        $this->assertEquals($message, $storedmessage);

        $adhoctask = \core\task\manager::get_adhoc_tasks(send_sms_task::class);
        $this->assertCount(1, $adhoctask);

        // Now lets run the task and check if SMS is sent.
        $this->run_all_adhoc_tasks();

        $message = $manager->get_message(['id' => $message->id]);
        $this->assertEquals(message_status::GATEWAY_SENT, $message->status);
    }

}
