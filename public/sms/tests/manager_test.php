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

use core_sms\task\send_sms_task;

/**
 * Tests for sms manager
 *
 * @package    core_sms
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_sms\manager
 * @covers \core_sms\message
 * @covers \core_sms\gateway
 */
final class manager_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        require_once(__DIR__ . "/fixtures/dummy_gateway.php");
        parent::setUpBeforeClass();
    }

    public function test_gateway_manipulation(): void {
        $this->resetAfterTest();
        $config = (object) [
            'data' => 'goeshere',
        ];

        $dummy = $this->getMockBuilder(\core_sms\gateway::class)
            ->setConstructorArgs([
                'enabled' => true,
                'name' => 'dummy',
                'config' => json_encode($config),
            ])
            ->onlyMethods(['get_send_priority', 'send'])
            ->getMock();
        $dummygw = get_class($dummy);

        $manager = \core\di::get(\core_sms\manager::class);
        $gateway = $manager->create_gateway_instance(
            classname: $dummygw,
            name: 'dummy',
            enabled: true,
            config: $config,
        );

        $this->assertIsInt($gateway->id);
        $this->assertTrue($gateway->enabled);
        $this->assertEquals('goeshere', $gateway->config->data);

        // Disable the gateway.
        $disabled = $manager->disable_gateway($gateway);
        $this->assertFalse($disabled->enabled);
        $this->assertEquals($gateway->id, $disabled->id);
        $this->assertEquals($gateway->config, $disabled->config);
        $this->assertTrue($gateway->enabled);

        // Enable the gateway.
        $enabled = $manager->enable_gateway($disabled);
        $this->assertTrue($enabled->enabled);
        $this->assertEquals($disabled->id, $enabled->id);
        $this->assertEquals($gateway->config, $enabled->config);
        $this->assertFalse($disabled->enabled);

        // Enabling an enabled gateway should return an identical object.
        // Note: Whether the object is identical is not guaranteed, and is internal logic we should not be concerned with.
        $reenabled = $manager->enable_gateway($enabled);
        $this->assertEquals($enabled, $reenabled);
    }

    public function test_create_gateway_instance_unknown_class(): void {
        $manager = \core\di::get(\core_sms\manager::class);

        $this->expectException(\coding_exception::class);
        $manager->create_gateway_instance(
            classname: \no\class\name\here::class,
            name: 'dummy',
            enabled: true,
            config: (object) [
                'data' => 'goeshere',
            ],
        );
    }

    public function test_create_gateway_instance_valid_but_wrong_class(): void {
        $manager = \core\di::get(\core_sms\manager::class);

        $this->expectException(\coding_exception::class);
        $manager->create_gateway_instance(
            classname: self::class,
            name: 'dummy',
            enabled: true,
            config: (object) [
                'data' => 'goeshere',
            ],
        );
    }

    /**
     * Test that uninstalled gateways do not cause failures in the workflow.
     */
    public function test_uninstalled_gateway(): void {
        // We should prevent removal of gateways which hold any data, but if one has been removed, we should not fail.
        $this->resetAfterTest();

        $config = (object) [
            'data' => 'goeshere',
        ];

        $dummy = $this->getMockBuilder(\core_sms\gateway::class)
            ->setConstructorArgs([
                'enabled' => true,
                'name' => 'dummy',
                'config' => json_encode($config),
            ])
            ->onlyMethods(['get_send_priority', 'send'])
            ->getMock();
        $dummygw = get_class($dummy);

        $manager = \core\di::get(\core_sms\manager::class);
        $gateway = $manager->create_gateway_instance(
            classname: $dummygw,
            name: 'dummy',
            enabled: true,
            config: $config,
        );
        $uninstalledgateway = $manager->create_gateway_instance(
            classname: $dummygw,
            name: 'dummy',
            enabled: true,
            config: $config,
        );

        $db = \core\di::get(\moodle_database::class);
        $db->set_field('sms_gateways', 'gateway', 'uninstalled', ['id' => $uninstalledgateway->id]);

        $instances = $manager->get_gateway_instances();
        $this->assertDebuggingCalled();
        $this->assertCount(1, $instances);
        $this->assertArrayHasKey($gateway->id, $instances);
        $this->assertArrayNotHasKey($uninstalledgateway->id, $instances);
    }

    /**
     * Test that multiple instances of the same gateway can be created.
     */
    public function test_multiple_gateway_instances(): void {
        $this->resetAfterTest();

        $config = (object) [
            'data' => 'goeshere',
        ];

        $dummy = $this->getMockBuilder(\core_sms\gateway::class)
            ->setConstructorArgs([
                'enabled' => true,
                'name' => 'dummy',
                'config' => json_encode($config),
            ])
            ->onlyMethods(['get_send_priority', 'send'])
            ->setMockClassName('dummygateway')
            ->getMock();
        $dummygw = get_class($dummy);
        $otherdummy = $this->getMockBuilder(\core_sms\gateway::class)
            ->setConstructorArgs([
                'enabled' => true,
                'name' => 'dummy',
                'config' => json_encode($config),
            ])
            ->onlyMethods(['get_send_priority', 'send'])
            ->setMockClassName('otherdummygw')
            ->getMock();
        $otherdummygw = get_class($otherdummy);

        $manager = \core\di::get(\core_sms\manager::class);
        $gatewaya = $manager->create_gateway_instance(
            classname: $dummygw,
            name: 'dummy',
            enabled: true,
            config: $config,
        );
        $gatewayb = $manager->create_gateway_instance(
            classname: $otherdummygw,
            name: 'dummy',
            enabled: true,
            config: $config,
        );
        $gatewayc = $manager->create_gateway_instance(
            classname: $dummygw,
            name: 'dummy',
            config: $config,
        );

        $this->assertNotEquals($gatewaya->id, $gatewayb->id);
        $this->assertNotEquals($gatewaya->id, $gatewayc->id);
        $this->assertNotEquals($gatewayb->id, $gatewayc->id);

        $instances = $manager->get_gateway_instances();
        $this->assertCount(3, $instances);
        $this->assertArrayHasKey($gatewaya->id, $instances);
        $this->assertArrayHasKey($gatewayb->id, $instances);
        $this->assertArrayHasKey($gatewayc->id, $instances);

        $enabled = $manager->get_enabled_gateway_instances();
        $this->assertCount(2, $enabled);
        $this->assertArrayHasKey($gatewaya->id, $enabled);
        $this->assertArrayHasKey($gatewayb->id, $enabled);
        $this->assertArrayNotHasKey($gatewayc->id, $enabled);

        $dummygwinstances = $manager->get_gateway_instances(['gateway' => $dummygw]);
        $this->assertCount(2, $dummygwinstances);
        $this->assertArrayHasKey($gatewaya->id, $dummygwinstances);
        $this->assertArrayNotHasKey($gatewayb->id, $dummygwinstances);
        $this->assertArrayHasKey($gatewayc->id, $dummygwinstances);
    }

    /**
     * Test that the manager can get gateways for a message.
     *
     * @dataProvider gateway_priority_provider
     * @param string $recipientnumber
     * @param int $matchcount
     * @param ?string $gw
     */
    public function test_get_gateways_for_message(
        string $recipientnumber,
        int $matchcount,
        ?string $gw,
    ): void {
        $this->resetAfterTest();

        $manager = \core\di::get(\core_sms\manager::class);
        $ukgw = $manager->create_gateway_instance(\smsgateway_dummy\gateway::class, 'dummy', true, (object) [
            'startswith' => (object) [
                '+44' => 100,
                '+61' => 1,
            ],
            'priority' => 0,
        ]);
        $augw = $manager->create_gateway_instance(\smsgateway_dummy\gateway::class, 'dummy', true, (object) [
            'startswith' => (object) [
                '+44' => 1,
                '+61' => 100,
            ],
            'priority' => 0,
        ]);

        $message = new message(
            recipientnumber: $recipientnumber,
            content: 'Hello, world!',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            issensitive: false,
        );

        $gateways = $manager->get_possible_gateways_for_message($message);
        $this->assertCount($matchcount, $gateways);

        $preferredgw = $manager->get_gateway_for_message($message);
        if ($gw === null) {
            $this->assertNull($preferredgw);
            $this->assertFalse($ukgw->can_send($message));
            $this->assertFalse($augw->can_send($message));
        } else {
            $this->assertEquals(${$gw}->id, $preferredgw->id);
            $this->assertTrue(${$gw}->can_send($message));
        }
    }

    /**
     * Data provider for test_get_gateways_for_message tests.
     *
     * @return array
     */
    public static function gateway_priority_provider(): array {
        return [
            'uk' => [
                '+447123456789',
                2,
                'ukgw',
            ],
            'au' => [
                '+61987654321',
                2,
                'augw',
            ],
            'us' => [
                '+1987654321',
                0,
                null,
            ],
        ];
    }

    public function test_save_message(): void {
        $this->resetAfterTest();

        $manager = \core\di::get(\core_sms\manager::class);
        $message = new message(
            recipientnumber: '+447123456789',
            content: 'Hello, world!',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            issensitive: false,
        );

        $saved = $manager->save_message($message);

        $this->assertFalse(isset($message->id));
        $this->assertTrue(isset($saved->id));

        $storedmessage = $manager->get_message(['id' => $saved->id]);
        $this->assertEquals($saved, $storedmessage);

        $updatedmessage = $manager->save_message($saved->with(status: message_status::GATEWAY_SENT));
        $this->assertEquals($saved->id, $updatedmessage->id);
        $this->assertEquals(message_status::GATEWAY_SENT, $updatedmessage->status);
        $this->assertEquals($saved->recipientnumber, $updatedmessage->recipientnumber);
        $this->assertEquals($saved->content, $updatedmessage->content);
        $this->assertEquals($saved->component, $updatedmessage->component);
        $this->assertEquals($saved->messagetype, $updatedmessage->messagetype);
        $this->assertEquals($saved->recipientuserid, $updatedmessage->recipientuserid);
        $this->assertEquals($saved->issensitive, $updatedmessage->issensitive);
    }

    public function test_send(): void {
        $this->resetAfterTest();

        $config = new \stdClass();
        $config->priority = 50;

        $manager = \core\di::get(\core_sms\manager::class);
        $gw = $manager->create_gateway_instance(
            classname: \smsgateway_dummy\gateway::class,
            name: 'dummy',
            enabled: true,
            config: $config,
        );

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
    }

    public function test_send_issensitive(): void {
        $this->resetAfterTest();

        $manager = \core\di::get(\core_sms\manager::class);
        $config = new \stdClass();
        $config->priority = 50;

        $gw = $manager->create_gateway_instance(\smsgateway_dummy\gateway::class, 'dummy', true, $config);

        $message = $manager->send(
            recipientnumber: '+447123456789',
            content: 'Hello, world!',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            issensitive: true,
            async: false,
        );

        $this->assertInstanceOf(message::class, $message);

        $this->assertIsInt($message->id);
        $this->assertEquals(message_status::GATEWAY_SENT, $message->status);
        $this->assertEquals($gw->id, $message->gatewayid);
        $this->assertNull($message->content);

        $storedmessage = $manager->get_message(['id' => $message->id]);
        $this->assertEquals($message, $storedmessage);
    }

    public function test_send_issensitive_async(): void {
        $this->resetAfterTest();

        $manager = \core\di::get(\core_sms\manager::class);

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Sensitive messages cannot be sent asynchronously');
        $manager->send(
            recipientnumber: '+447123456789',
            content: 'Hello, world!',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            issensitive: true,
            async: true,
        );
    }

    /**
     * Test sending SMS asynchronously.
     */
    public function test_send_async(): void {
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

        $message = $manager->send(
            recipientnumber: '+447123456789',
            content: 'Hello, world!',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
        );

        $this->assertInstanceOf(message::class, $message);
        $this->assertIsInt($message->id);
        $this->assertEquals(message_status::GATEWAY_QUEUED, $message->status);
        $this->assertEquals('Hello, world!', $message->content);

        $messagedbrecords = $manager->get_messages();
        $this->assertInstanceOf(\Generator::class, $messagedbrecords);
        $messages = iterator_to_array($messagedbrecords);
        $this->assertCount(1, $messages);

        $storedmessage = $manager->get_message(['id' => $message->id]);
        $this->assertEquals($message, $storedmessage);

        $adhoctask = \core\task\manager::get_adhoc_tasks(send_sms_task::class);
        $this->assertCount(1, $adhoctask);

        // Now lets run the task and check if SMS is sent.
        $this->run_all_adhoc_tasks();

        $message = $manager->get_message(['id' => $message->id]);
        $this->assertEquals(message_status::GATEWAY_SENT, $message->status);
    }

    public function test_send_no_gateway(): void {
        $this->resetAfterTest();

        $manager = \core\di::get(\core_sms\manager::class);

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
        $this->assertEquals(message_status::GATEWAY_NOT_AVAILABLE, $message->status);
        $this->assertEmpty($message->gatewayid);
    }

    /**
     * Test the truncate content process while sending the SMS.
     */
    public function test_send_truncate_content(): void {
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

        $message = $manager->send(
            recipientnumber: '+447123456789',
            content: str_repeat('a', 161),
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            async: false,
        );

        // Expected the message to be truncated with the length limit.
        $this->assertEquals(
            expected: str_repeat('a', 160), // 160 is the limit of the dummy gateway.
            actual: $message->content,
        );
    }

    public function test_get_messages(): void {
        $db = $this->createStub(\moodle_database::class);
        $db->method('get_records')->willReturn([
            (object) [
                'id' => 1,
                'recipientnumber' => '+447123456789',
                'content' => 'Hello, world!',
                'component' => 'core',
                'messagetype' => 'test',
                'recipientuserid' => null,
                'issensitive' => false,
                'status' => message_status::GATEWAY_SENT->value,
                'gatewayid' => 1,
                'timecreated' => time(),
            ],
        ]);
        \core\di::set(\moodle_database::class, $db);

        $manager = \core\di::get(\core_sms\manager::class);
        $result = $manager->get_messages();
        $this->assertInstanceOf(\Generator::class, $result);

        $messages = iterator_to_array($result);
        $this->assertCount(1, $messages);
        array_walk($messages, fn ($message) => $this->assertInstanceOf(message::class, $message));
    }
}
