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

/**
 * One Roster tests for filters.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\local\endpoint
 */
class endpoint_testcase extends oneroster_testcase {

    /**
     * Get a mocked command at the specified endpoint.
     *
     * @param   endpoint $endpoint
     * @param   string $method
     * @param   array $params
     * @param   array|null $collection
     * @return  command
     */
    protected function get_mocked_command(endpoint $endpoint, string $method, array $params, ?array $collection = null): command {
        return $this->getMockBuilder(command::class)
            ->setConstructorArgs([
                $endpoint,
                '/exampleMethod',
                $method,
                'This is an example method',
                $collection,
                null,
                null,
                $params
            ])
            ->setMethods(null)
            ->getMock();
    }

    /**
     * Test instantiation of the endpoint.
     */
    public function test_instantiation(): void {
        $container = $this->get_mocked_container();

        $endpoint = $this->getMockBuilder(endpoint::class)
            ->setConstructorArgs([$container])
            ->getMock();

        $this->assertInstanceOf(endpoint::class, $endpoint);
    }

    /**
     * Test the 'execute' method.
     */
    public function test_execute(): void {
        $container = $this->get_mocked_container();

        // Mock the endpoint under test.
        // The 'get_http_method' method must be mocked because no commands are available to test in the abstract
        // endpoint.
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->setConstructorArgs([$container])
            ->setMethods([
                'get_http_method',
            ])
            ->getMock();

        // Test data.
        $method = 'exampleEndpoint';
        $params = [
            'sort' => 'dateLastModified',
        ];
        $filter = $this->createMock(filter::class);

        $command = $this->get_mocked_command($endpoint, $method, $params);

        $endpoint
            ->expects($this->once())
            ->method('get_http_method')
            ->with(
                $this->equalTo($method),
                $this->equalTo($params)
            )
            ->willReturn($command);

        $client = $container->get_client();
        $client
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo($command),
                $this->equalTo($filter)
            )
            ->willReturn((object) [
                'response' => 'Example response',
            ]);

        $this->assertequals('Example response', $endpoint->execute($method, $filter, $params));
    }

    /**
     * Test the 'execute_command' method.
     */
    public function test_execute_command(): void {
        $container = $this->get_mocked_container();

        // Mock the endpoint under test.
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->setConstructorArgs([$container])
            ->setMethods(null)
            ->getMock();

        // Test data.
        $command = $this->get_mocked_command($endpoint, 'exampleEndpoint', []);
        $filter = $this->createMock(filter::class);

        $container->get_client()
            ->expects($this->once())
            ->method('execute')
            ->with(
                $this->equalTo($command),
                $this->equalTo($filter)
            )
            ->willReturn((object) [
                'response' => 'Example response',
            ]);

        $result = $endpoint->execute_command($command, $filter);
        $this->assertEquals('Example response', $result);
    }

    /**
     * Test the 'execute_paginated_function' method.
     */
    public function test_execute_paginated_function(): void {
        $container = $this->get_mocked_container();

        // Mock the endpoint under test.
        // The 'get_http_method' method must be mocked because no commands are available to test in the abstract
        // endpoint.
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->setConstructorArgs([$container])
            ->setMethods([
                'get_http_method',
            ])
            ->getMock();
        $this->assertInstanceOf(endpoint::class, $endpoint);
        $this->assertInstanceOf(\enrol_oneroster\local\interfaces\endpoint::class, $endpoint);

        // Test data.
        $method = 'exampleEndpoint';
        $params = [
            'sort' => 'dateLastModified',
        ];

        $filter = $this->createMock(filter::class);
        $command = $this->get_mocked_command($endpoint, $method, $params, ['examples']);
        $command1 = clone $command;
        $command2 = clone $command;
        $command3 = clone $command;

        $endpoint
            ->expects($this->exactly(3))
            ->method('get_http_method')
            ->withConsecutive(
                [$this->equalTo($method), $this->equalTo(array_merge($params, ['offset' => 0, 'limit' => 200]))],
                [$this->equalTo($method), $this->equalTo(array_merge($params, ['offset' => 200, 'limit' => 200]))],
                [$this->equalTo($method), $this->equalTo(array_merge($params, ['offset' => 400, 'limit' => 200]))]
            )
            ->will(
                $this->onConsecutiveCalls(
                    $command1,
                    $command2,
                    $command3
                )
            );

        $client = $container->get_client();
        $client
            ->method('execute')
            ->withConsecutive(
                [$this->equalTo($command1), $this->equalTo($filter)],
                [$this->equalTo($command2), $this->equalTo($filter)],
                [$this->equalTo($command3), $this->equalTo($filter)]
            )
            ->will($this->onConsecutiveCalls(
                (object) [
                    'response' => (object) [
                        'examples' => array_map(function($id) {
                            return (object) ['sourcedId' => $id];
                        }, array_keys(array_fill(1, 200, null))),
                    ],
                ],
                (object) [
                    'response' => (object) [
                        'examples' => array_map(function($id) {
                            return (object) ['sourcedId' => $id];
                        }, array_keys(array_fill(201, 400, null))),
                    ],
                ],
                (object) [
                    'response' => (object) [
                        'examples' => [],
                    ],
                ]
            ));

        $result = $endpoint->execute_paginated_function($method, $filter, $params, function($row) {
            return $row;
        });
        $this->assertInstanceOf(\Generator::class, $result);

        foreach ($result as $row) {
            $this->assertIsObject($row);
        }
    }

    /**
     * Ensure that the `get_all_commands` function returns an array.
     */
    public function test_get_all_data(): void {
        $container = $this->get_mocked_container();
        $endpoint = new endpoint($container);

        $this->assertIsArray($endpoint->get_all_commands());
    }

    /**
     * Ensure that the 'execute_command' method throws an exception for an invalid command.
     */
    public function test_execute_invalid(): void {
        $container = $this->get_mocked_container();

        // Mock the endpoint under test.
        $endpoint = new endpoint($container);

        $this->expectException(\BadMethodCallException::class);
        $endpoint->execute('unknownMethod');
    }
}
