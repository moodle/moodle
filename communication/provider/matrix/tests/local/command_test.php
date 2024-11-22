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

namespace communication_matrix\local;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use ReflectionMethod;

defined('MOODLE_INTERNAL') || die();
require_once(dirname(__DIR__) . '/matrix_client_test_trait.php');

/**
 * Tests for the Matrix command class.
 *
 * @package    communication_matrix
 * @category   test
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \communication_matrix\local\command
 * @coversDefaultClass \communication_matrix\local\command
 */
final class command_test extends \advanced_testcase {
    use \communication_matrix\matrix_client_test_trait;

    /**
     * Test instantiation of a command when no method is provided.
     */
    public function test_standard_instantiation(): void {
        $instance = $this->get_mocked_instance_for_version('v1.7');
        $command = new command(
            $instance,
            method: 'PUT',
            endpoint: 'example/endpoint',
        );

        // Check the standard functionality.
        $this->assertEquals('/example/endpoint', $command->getUri()->getPath());
        $this->assertEquals('PUT', $command->getMethod());
        $this->assertArrayHasKey('Authorization', $command->getHeaders());
    }

    /**
     * Test instantiation of a command when no method is provided.
     */
    public function test_instantiation_without_auth(): void {
        $instance = $this->get_mocked_instance_for_version('v1.7');
        $command = new command(
            $instance,
            method: 'PUT',
            endpoint: 'example/endpoint',
            requireauthorization: false,
        );

        // Check the standard functionality.
        $this->assertEquals('/example/endpoint', $command->getUri()->getPath());
        $this->assertEquals('PUT', $command->getMethod());
        $this->assertArrayNotHasKey('Authorization', $command->getHeaders());
    }

    /**
     * Test processing of command URL properties.
     *
     * @dataProvider url_parsing_provider
     * @param string $url
     * @param array $params
     * @param string $expected
     */
    public function test_url_parsing(
        string $url,
        array $params,
        string $expected,
    ): void {
        $instance = $this->get_mocked_instance_for_version('v1.7');

        $command = new command(
            $instance,
            method: 'PUT',
            endpoint: $url,
            params: $params,
        );

        $this->assertEquals($expected, $command->getUri()->getPath());
    }

    /**
     * Data provider for url parsing tests.
     *
     * @return array
     */
    public static function url_parsing_provider(): array {
        return [
            [
                'example/:id/endpoint',
                [':id' => '39492'],
                '/example/39492/endpoint',
            ],
            [
                'example/:id/endpoint/:id',
                [':id' => '39492'],
                '/example/39492/endpoint/39492',
            ],
            [
                'example/:id/endpoint/:id/:name',
                [
                    ':id' => '39492',
                    ':name' => 'matrix',
                ],
                '/example/39492/endpoint/39492/matrix',
            ],
        ];
    }

    /**
     * Test processing of command URL properties with an array which contains untranslated parameters.
     */
    public function test_url_parsing_extra_properties(): void {
        $instance = $this->get_mocked_instance_for_version('v1.7');
        $this->expectException(\OutOfRangeException::class);
        $this->expectExceptionMessage("URL contains untranslated parameters 'example/:id/endpoint'");

        new command(
            $instance,
            method: 'PUT',
            endpoint: 'example/:id/endpoint',
        );
    }

    /**
     * Test processing of command URL properties with an array which contains untranslated parameters.
     */
    public function test_url_parsing_unused_properites(): void {
        $instance = $this->get_mocked_instance_for_version('v1.7');
        $this->expectException(\OutOfRangeException::class);
        $this->expectExceptionMessage("Parameter not found in URL ':id'");

        new command(
            $instance,
            method: 'PUT',
            endpoint: 'example/:ids/endpoint',
            params: [
                ':id' => 12345,
            ],
        );
    }

    /**
     * Test the parameter fetching, processing, and parsing.
     *
     * @dataProvider parameter_and_option_provider
     * @param string $endpoint
     * @param array $params
     * @param array $remainingparams
     * @param array $allparams
     * @param array $options
     */
    public function test_parameters(
        string $endpoint,
        array $params,
        array $remainingparams,
        array $allparams,
        array $options,
    ): void {
        $instance = $this->get_mocked_instance_for_version('v1.7');

        $command = new command(
            $instance,
            method: 'PUT',
            endpoint: $endpoint,
            params: $params,
        );

        $this->assertSame($remainingparams, $command->get_remaining_params());
        $this->assertSame($allparams, $command->get_all_params());
        $this->assertSame($options, $command->get_options());
    }

    /**
     * Data provider for parameter tests.
     *
     * @return array
     */
    public static function parameter_and_option_provider(): array {
        $command = [
            'method' => 'PUT',
            'endpoint' => 'example/:id/endpoint',
        ];

        return [
            'no parameters' => [
                'endpoint' => 'example/endpoint',
                'params' => [],
                'remainingparams' => [],
                'allparams' => [],
                'options' => [
                    'json' => [],
                ],
            ],
            'named params' => [
                'endpoint' => 'example/:id/endpoint',
                'params' => [
                    ':id' => 12345,
                ],
                'remainingparams' => [],
                'allparams' => [
                    ':id' => 12345,
                ],
                'options' => [
                    'json' => [],
                ],
            ],
            'mixture of params' => [
                'endpoint' => 'example/:id/endpoint',
                'params' => [
                    ':id' => 12345,
                    'name' => 'matrix',
                ],
                'remainingparams' => [
                    'name' => 'matrix',
                ],
                'allparams' => [
                    ':id' => 12345,
                    'name' => 'matrix',
                ],
                'options' => [
                    'json' => [
                        'name' => 'matrix',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test the query parameter handling.
     *
     * @dataProvider query_provider
     * @param array $query
     * @param string $expected
     */
    public function test_query_parameters(
        array $query,
        string $expected,
    ): void {
        // The query parameter is only added at the time we call send.
        // That's because it can only be provided to Guzzle as an Option, not as part of the URL.
        // Options can only be applied at time of transfer.
        // Unfortuantely that leads to slightly less ideal testing that we'd like here.
        $mock = new MockHandler();
        $instance = $this->get_mocked_instance_for_version(
            'v1.7',
            mock: $mock,
        );

        $mock->append(function (Request $request) use ($expected): Response {
            $this->assertSame(
                $expected,
                $request->getUri()->getQuery(),
            );
            return new Response();
        });
        $command = new command(
            $instance,
            method: 'PUT',
            endpoint: 'example/endpoint',
            query: $query,
        );

        $execute = new ReflectionMethod($instance, 'execute');
        $execute->invoke($instance, $command);
    }

    /**
     * Data provider for query parameter tests.
     * @return array
     */
    public static function query_provider(): array {
        return [
            'no query' => [
                'query' => [],
                'expected' => '',
            ],
            'single query' => [
                'query' => [
                    'name' => 'matrix',
                ],
                'expected' => 'name=matrix',
            ],
            'multiple queries' => [
                'query' => [
                    'name' => 'matrix',
                    'type' => 'room',
                ],
                'expected' => 'name=matrix&type=room',
            ],
        ];
    }

    /**
     * Test the sendasjson constructor parameter.
     *
     * @dataProvider sendasjson_provider
     * @param bool $sendasjson
     * @param string $endpoint
     * @param array $params
     * @param array $remainingparams
     * @param array $allparams
     * @param array $expectedoptions
     */
    public function test_send_as_json(
        bool $sendasjson,
        string $endpoint,
        array $params,
        array $remainingparams,
        array $allparams,
        array $expectedoptions,
    ): void {
        $instance = $this->get_mocked_instance_for_version('v1.7');

        $command = new command(
            $instance,
            method: 'PUT',
            endpoint: $endpoint,
            params: $params,
            sendasjson: $sendasjson,
        );

        $this->assertSame($remainingparams, $command->get_remaining_params());
        $this->assertSame($allparams, $command->get_all_params());
        $this->assertSame($expectedoptions, $command->get_options());
    }

    /**
     * Test the sendasjosn option to the command constructor.
     *
     * @return array
     */
    public static function sendasjson_provider(): array {
        return [
            'As JSON' => [
                'sendasjson' => true,
                'endpoint' => 'example/:id/endpoint',
                'params' => [
                    ':id' => 12345,
                    'name' => 'matrix',
                ],
                'remainingparams' => [
                    'name' => 'matrix',
                ],
                'allparams' => [
                    ':id' => 12345,
                    'name' => 'matrix',
                ],
                'expectedoptions' => [
                    'json' => [
                        'name' => 'matrix',
                    ],
                ],
            ],
            'Not as JSON' => [
                'sendasjson' => false,
                'endpoint' => 'example/:id/endpoint',
                'params' => [
                    ':id' => 12345,
                    'name' => 'matrix',
                ],
                'remainingparams' => [
                    'name' => 'matrix',
                ],
                'allparams' => [
                    ':id' => 12345,
                    'name' => 'matrix',
                ],
                'expectedoptions' => [
                ],
            ],
        ];
    }

    /**
     * Test the sendasjosn option to the command constructor.
     */
    public function test_ignorehttperrors(): void {
        $instance = $this->get_mocked_instance_for_version('v1.7');

        $command = new command(
            $instance,
            method: 'PUT',
            endpoint: 'example/endpoint',
            ignorehttperrors: true,
        );

        $options = $command->get_options();
        $this->assertArrayHasKey('http_errors', $options);
        $this->assertFalse($options['http_errors']);
    }
}
