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

namespace smsgateway_modica;

use core_sms\message;
use core_sms\message_status;
use GuzzleHttp\Psr7\Response;

/**
 * Modica SMS gateway tests.
 *
 * @package    smsgateway_modica
 * @category   test
 * @copyright  2025 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \smsgateway_modica\gateway
 */
final class gateway_test extends \advanced_testcase {
    public function test_send(): void {
        $this->resetAfterTest();

        $config = (object) [
            'modica_url' => gateway::MODICA_DEFAULT_API,
            'modica_application_name' => 'test_application',
            'modica_application_password' => 'test_password',
        ];

        $manager = \core\di::get(\core_sms\manager::class);
        $gw = $manager->create_gateway_instance(
            classname: gateway::class,
            name: 'modica',
            enabled: true,
            config: $config,
        );

        // Mock the http client to return a successful response.
        ['mock' => $mock] = $this->get_mocked_http_client();

        // Append the response status.
        $mock->append(new Response(
            status: 201,
        ));

        $message = $manager->send(
            recipientnumber: '+447123456789',
            content: 'Hello, world!',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            async: false,
        );

        $this->assertInstanceOf(message::class, $message);
        $this->assertIsInt($message->id);
        $this->assertEquals(message_status::GATEWAY_SENT, $message->status);
        $this->assertEquals($gw->id, $message->gatewayid);
        $this->assertEquals('Hello, world!', $message->content);

        $storedmessage = $manager->get_message(['id' => $message->id]);
        $this->assertEquals($message, $storedmessage);

        // Now let's try with failed status.
        $mock->append(new Response(
            status: 200,
        ));

        $message = $manager->send(
            recipientnumber: '+447123456789',
            content: 'Hello, world!',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            async: false,
        );

        $this->assertEquals(message_status::GATEWAY_FAILED, $message->status);
        $this->assertEquals($gw->id, $message->gatewayid);
    }
}
