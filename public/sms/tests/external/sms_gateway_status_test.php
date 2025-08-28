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

declare(strict_types=1);

namespace core_sms\external;

/**
 * Test the webservice to change the status of a gateway.
 *
 * @package    core_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_sms\external\sms_gateway_status::execute
 */
final class sms_gateway_status_test extends \core_external\tests\externallib_testcase {
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

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

        // Now let's disable the gateway.
        sms_gateway_status::execute(
            plugin: $gateway->id,
            state: 0,
        );
        $gatewaymanagers = $manager->get_gateway_instances(
            filter: ['id' => $gateway->id],
        );
        $gatewaymanager = reset($gatewaymanagers);
        $this->assertFalse($gatewaymanager->enabled);

        // Let's enable again.
        sms_gateway_status::execute(
            plugin: $gateway->id,
            state: 1,
        );
        $gatewaymanagers = $manager->get_gateway_instances(
            filter: ['id' => $gateway->id],
        );
        $gatewaymanager = reset($gatewaymanagers);
        $this->assertTrue($gatewaymanager->enabled);
    }
}
