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

use advanced_testcase;
use enrol_oneroster\local\interfaces\client as client_interface;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\collection_factory as collection_factory_interface;
use enrol_oneroster\local\interfaces\entity_factory as entity_factory_interface;
use enrol_oneroster\local\interfaces\filter as filter_interface;
use enrol_oneroster\local\interfaces\rostering_endpoint as rostering_endpoint_interface;
use enrol_oneroster\local\filter as abstract_filter;
use enrol_oneroster\local\oneroster_client as root_oneroster_client;
use enrol_oneroster\local\endpoints\rostering as rostering_endpoint;
use enrol_oneroster\local\factories\collection_factory;
use enrol_oneroster\local\factories\entity_factory;

/**
 * One Roster Entity tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class oneroster_testcase extends advanced_testcase {
    /**
     * Get a mocked container.
     *
     * @return  container_interface
     */
    protected function get_mocked_container(): container_interface {
        $client = $this->mock_client();
        $mock = $this->getMockBuilder(container::class)
            ->setConstructorArgs([$client])
            ->setMethods([
                '__construct',
                'get_client',
                'get_rostering_endpoint',
                'get_entity_factory',
                'get_collection_factory',
                'get_cache_factory',
                'get_filter_instance',
            ])
            ->getMock();

        $mock->method('get_client')->willReturn($client);
        $mock->method('get_filter_instance')->willReturn($this->mock_filter());

        return $mock;
    }

    /**
     * Mock a filter.
     *
     * @return filter_interface
     */
    protected function mock_filter(): filter_interface {
        return $this->createMock(abstract_filter::class);
    }

    /**
     * Mock a OneRoster Client.
     *
     * @return  client_interface
     */
    protected function mock_client(): client_interface {
        require_once(__DIR__ . '/../fixtures/oneroster_client.php');
        return $this->createMock(\enrol_oneroster\tests\fixtures\local\oneroster_client::class);
    }

    /**
     * Mock the Rostering endpoint.
     *
     * @param   container_interface $container
     * @param   array $mockfunctions The functions to be mocked
     * @return  rostering_endpoint_interface
     */
    protected function mock_rostering_endpoint(container_interface $container, array $mockfunctions): rostering_endpoint_interface {
        $mock = $this->getMockBuilder(rostering_endpoint::class)
            ->setConstructorArgs([$container])
            ->setMethods(array_values($mockfunctions))
            ->getMock();

        $container->method('get_rostering_endpoint')->willReturn($mock);

        return $mock;
    }

    /**
     * Mock an entity factory.
     *
     * @param   container_interface $container
     * @param   array $mockfunctions The functions to be mocked
     * @return  entity_factory_interface
     */
    protected function mock_entity_factory(container_interface $container, array $mockfunctions): entity_factory_interface {
        $mock = $this->getMockBuilder(entity_factory::class)
            ->setConstructorArgs([$container])
            ->setMethods(array_values($mockfunctions))
            ->getMock();

        $container->method('get_entity_factory')->willReturn($mock);

        return $mock;
    }

    /**
     * Mock a Collection factory.
     *
     * @param   container_interface $container
     * @param   array $mockfunctions The functions to be mocked
     * @return  collection_factory_interface
     */
    protected function mock_collection_factory(container_interface $container, array $mockfunctions): collection_factory_interface {
        $mock = $this->getMockBuilder(collection_factory::class)
            ->setConstructorArgs([$container])
            ->setMethods(array_values($mockfunctions))
            ->getMock();

        $container->method('get_collection_factory')->willReturn($mock);

        return $mock;
    }
}
