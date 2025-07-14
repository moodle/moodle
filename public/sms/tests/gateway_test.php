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

namespace core_sms;

/**
 * Tests for sms gateway.
 *
 * @package    core_sms
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_sms\gateway
 */
final class gateway_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        require_once(__DIR__ . "/fixtures/dummy_gateway.php");
        parent::setUpBeforeClass();
    }

    public function test_update_message_status(): void {
        $this->resetAfterTest();

        $manager = \core\di::get(\core_sms\manager::class);
        $config = new \stdClass();
        $config->api_key = 'test_api_key';

        $gw = $manager->create_gateway_instance(
            classname: \smsgateway_dummy\gateway::class,
            name: 'dummy',
            enabled: true,
            config: $config,
        );
        $othergw = $manager->create_gateway_instance(
            classname: \smsgateway_dummy\gateway::class,
            name: 'dummy',
            enabled: true,
            config: $config,
        );

        $message = new message(
            recipientnumber: '1234567890',
            content: 'Hello, world!',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            issensitive: false,
            gatewayid: $gw->id,
        );
        $message2 = new message(
            recipientnumber: '1234567890',
            content: 'Hello, world!',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            issensitive: false,
            gatewayid: $gw->id,
        );

        $updatedmessage = $gw->update_message_status($message);
        $this->assertEquals($message, $updatedmessage);

        $updatedmessages = $gw->update_message_statuses([$message, $message2]);
        $this->assertEquals([$message, $message2], $updatedmessages);

        $this->expectException(\coding_exception::class);
        $othergw->update_message_status($message);
    }
}
