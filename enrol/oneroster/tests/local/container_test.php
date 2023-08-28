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
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/oneroster_testcase.php');
use enrol_oneroster\local\oneroster_testcase;

use enrol_oneroster\local\interfaces\client as client_interface;

/**
 * One Roster tests for container.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\local\container
 */
class container_testcase extends oneroster_testcase {

    /**
     * Get a mock of the abstract container.
     *
     * @param   null|client_interface $client If not specifie then the client is also mocked
     * @return  container
     */
    public function get_mock_abstract_container(?client_interface $client = null): container {
        if ($client === null) {
            $client = $this->mock_client();
        }

        $this->assertInstanceOf(\enrol_oneroster\local\interfaces\client::class, $client);

        return $this->getMockBuilder(container::class)
            ->setConstructorArgs([$client])
            ->setMethods([
                'get_filter_instance',
                'get_cache_factory',
                'get_collection_factory',
                'get_entity_factory',
                'get_rostering_endpoint',
            ])
            ->getMock();
    }

    /**
     * Test instantiation of the container.
     */
    public function test_instantiation(): void {
        $client = $this->mock_client();

        $container = $this->getMockBuilder(container::class)
            ->setConstructorArgs([$client])
            ->getMock();

        $this->assertInstanceOf(container::class, $container);
    }

    /**
     * Test `get_client` in the container.
     */
    public function test_get_client(): void {
        $client = $this->mock_client();
        $container = $this->get_mock_abstract_container($client);

        $this->assertEquals($client, $container->get_client());
    }

    /**
     * Test service setting and getting.
     */
    public function test_defined_service(): void {
        $container = $this->get_mock_abstract_container();

        $service = $this->getMockBuilder(service::class)
            ->setConstructorArgs([$container])
            ->getMock();

        $this->assertEquals($container, $container->set_service($service));
        $this->assertEquals($service, $container->get_service());
    }

    /**
     * Test service getting when a service has not been explicitly defined..
     */
    public function test_undefined_service(): void {
        $container = $this->get_mock_abstract_container();

        $service = $container->get_service();
        $this->assertInstanceOf(service::class, $service);
    }

    /**
     * Ensure that the `supports` method assumes that the service is supported  when the service is unknown.
     */
    public function test_supports(): void {
        $container = $this->get_mock_abstract_container();

        $this->assertTrue($container->supports('someFakeEndpoint'));
    }
}
