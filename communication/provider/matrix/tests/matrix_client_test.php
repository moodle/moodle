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

namespace communication_matrix;

use communication_matrix\local\command;
use communication_matrix\local\spec\v1p7;
use communication_matrix\local\spec\features;
use communication_matrix\tests\fixtures\mocked_matrix_client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/matrix_client_test_trait.php');

/**
 * Tests for the matrix_client class.
 *
 * @package    communication_matrix
 * @category   test
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \communication_matrix\matrix_client
 * @coversDefaultClass \communication_matrix\matrix_client
 */
class matrix_client_test extends \advanced_testcase {
    use matrix_client_test_trait;

    /**
     * Data provider for valid calls to ::instance.
     * @return array
     */
    public static function instance_provider(): array {
        $testcases = [
            'Standard versions' => [
                null,
                v1p7::class,
            ],
        ];

        // Remove a couple of versions.
        $versions = self::get_current_versions();
        array_pop($versions);
        array_pop($versions);

        $testcases['Older server'] = [
            $versions,
            array_key_last($versions),
        ];

        // Limited version compatibility, including newer than we support now.
        $testcases['Newer versions with crossover'] = [
            [
                'v1.6',
                'v1.7',
                'v7.9',
            ],
            \communication_matrix\local\spec\v1p7::class,
        ];

        return $testcases;
    }

    /**
     * Test that the instance method returns a valid instance for the given versions.
     *
     * @dataProvider instance_provider
     * @param array|null $versions
     * @param string $expectedversion
     */
    public function test_instance(
        ?array $versions,
        string $expectedversion,
    ): void {
        $container = [];
        ['client' => $client, 'mock' => $mock] = $this->get_mocked_http_client(
            history: $container,
        );

        $mock->append($this->get_mocked_version_response($versions));

        mocked_matrix_client::set_client($client);

        $instance = mocked_matrix_client::instance(
            'https://example.com',
            'testtoken',
        );

        $this->assertInstanceOf(matrix_client::class, $instance);

        // Only the version API has been called.
        $this->assertCount(1, $container);
        $request = reset($container);
        $this->assertEquals('/_matrix/client/versions', $request['request']->getUri()->getPath());

        // The client should be a v1p7 client as that is the highest compatible version.
        $this->assertInstanceOf($expectedversion, $instance);
    }

    /**
     * Test that the instance method returns a valid instance for the given versions.
     */
    public function test_instance_cached(): void {
        $container = [];
        ['client' => $client, 'mock' => $mock] = $this->get_mocked_http_client(
            history: $container,
        );

        // Queue two responses.
        $mock->append($this->get_mocked_version_response());
        $mock->append($this->get_mocked_version_response());

        mocked_matrix_client::set_client($client);

        $instance = mocked_matrix_client::instance('https://example.com', 'testtoken');

        $this->assertInstanceOf(matrix_client::class, $instance);

        // Only the version API has been called.
        $this->assertCount(1, $container);

        // Call the API again. It should not lead to additional fetches.
        $instance = mocked_matrix_client::instance('https://example.com', 'testtoken');
        $instance = mocked_matrix_client::instance('https://example.com', 'testtoken');
        $this->assertCount(1, $container);

        // But a different endpoint will.
        $instance = mocked_matrix_client::instance('https://example.org', 'testtoken');
        $this->assertCount(2, $container);
    }

    /**
     * Test that the instance method throws an appropriate exception if no support is found.
     */
    public function test_instance_no_support(): void {
        ['client' => $client, 'mock' => $mock] = $this->get_mocked_http_client();

        $mock->append($this->get_mocked_version_response(['v99.9']));

        mocked_matrix_client::set_client($client);

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('No supported Matrix API versions found.');

        mocked_matrix_client::instance(
            'https://example.com',
            'testtoken',
        );
    }

    /**
     * Test the feature implementation check methods.
     *
     * @covers ::implements_feature
     * @covers ::get_supported_versions
     * @dataProvider implements_feature_provider
     * @param string $version
     * @param array|string $features
     * @param bool $expected
     */
    public function test_implements_feature(
        string $version,
        array|string $features,
        bool $expected,
    ): void {
        $instance = $this->get_mocked_instance_for_version($version);
        $this->assertEquals($expected, $instance->implements_feature($features));
    }

    /**
     * Test the feature implementation requirement methods.
     *
     * @covers ::implements_feature
     * @covers ::get_supported_versions
     * @covers ::require_feature
     * @dataProvider implements_feature_provider
     * @param string $version
     * @param array|string $features
     * @param bool $expected
     */
    public function test_require_feature(
        string $version,
        array|string $features,
        bool $expected,
    ): void {
        $instance = $this->get_mocked_instance_for_version($version);

        if ($expected) {
            $this->assertEmpty($instance->require_feature($features));
        } else {
            $this->expectException('moodle_exception');
            $instance->require_feature($features);
        }
    }

    /**
     * Test the feature implementation requirement methods for a require all.
     *
     * @covers ::implements_feature
     * @covers ::get_supported_versions
     * @covers ::require_feature
     * @covers ::require_features
     * @dataProvider require_features_provider
     * @param string $version
     * @param array|string $features
     * @param bool $expected
     */
    public function test_require_features(
        string $version,
        array|string $features,
        bool $expected,
    ): void {
        $instance = $this->get_mocked_instance_for_version($version);

        if ($expected) {
            $this->assertEmpty($instance->require_features($features));
        } else {
            $this->expectException('moodle_exception');
            $instance->require_features($features);
        }
    }

    /**
     * Data provider for feature implementation check tests.
     *
     * @return array
     */
    public static function implements_feature_provider(): array {
        return [
            'Basic supported feature' => [
                'v1.7',
                features\matrix\media_create_v1::class,
                true,
            ],
            'Basic unsupported feature' => [
                'v1.6',
                features\matrix\media_create_v1::class,
                false,
            ],
            '[supported] as array' => [
                'v1.6',
                [features\matrix\create_room_v3::class],
                true,
            ],
            '[supported, supported] as array' => [
                'v1.6',
                [
                    features\matrix\create_room_v3::class,
                    features\matrix\update_room_avatar_v3::class,
                ],
                true,
            ],
            '[unsupported] as array' => [
                'v1.6',
                [
                    features\matrix\media_create_v1::class,
                ],
                false,
            ],
            '[unsupported, supported] as array' => [
                'v1.6',
                [
                    features\matrix\media_create_v1::class,
                    features\matrix\update_room_avatar_v3::class,
                ],
                true,
            ],
        ];
    }

    /**
     * Data provider for feature implementation check tests.
     *
     * @return array
     */
    public static function require_features_provider(): array {
        // We'll just add to the standard testcases.
        $testcases = array_map(static function (array $testcase): array {
            $testcase[1] = [$testcase[1]];
            return $testcase;
        }, self::implements_feature_provider());

        $testcases['Require many supported features'] = [
            'v1.6',
            [
                features\matrix\create_room_v3::class,
                features\matrix\update_room_avatar_v3::class,
            ],
            true,
        ];

        $testcases['Require many including an unsupported feature'] = [
            'v1.6',
            [
                features\matrix\create_room_v3::class,
                features\matrix\media_create_v1::class,
            ],
            false,
        ];

        $testcases['Require many including an unsupported feature which has an alternate'] = [
            'v1.6',
            [
                features\matrix\create_room_v3::class,
                [
                    features\matrix\media_create_v1::class,
                    features\matrix\update_room_avatar_v3::class,
                ],
            ],
            true,
        ];

        return $testcases;
    }

    /**
     * Test the get_version method.
     *
     * @param string $version
     * @param string $expectedversion
     * @dataProvider get_version_provider
     * @covers ::get_version
     * @covers ::get_version_from_classname
     */
    public function test_get_version(
        string $version,
        string $expectedversion,
    ): void {
        $instance = $this->get_mocked_instance_for_version($version);
        $this->assertEquals($expectedversion, $instance->get_version());
    }

    /**
     * Data provider for get_version tests.
     *
     * @return array
     */
    public static function get_version_provider(): array {
        return [
            ['v1.1', '1.1'],
            ['v1.7', '1.7'],
        ];
    }

    /**
     * Tests the meets_version method.
     *
     * @param string $version The version of the API to test against
     * @param string $testversion The version to test
     * @param bool $expected Whether the version meets the requirement
     * @dataProvider meets_version_provider
     * @covers ::meets_version
     */
    public function test_meets_version(
        string $version,
        string $testversion,
        bool $expected,
    ): void {
        $instance = $this->get_mocked_instance_for_version($version);
        $this->assertEquals($expected, $instance->meets_version($testversion));
    }

    /**
     * Tests the requires_version method.
     *
     * @param string $version The version of the API to test against
     * @param string $testversion The version to test
     * @param bool $expected Whether the version meets the requirement
     * @dataProvider meets_version_provider
     * @covers ::requires_version
     */
    public function test_requires_version(
        string $version,
        string $testversion,
        bool $expected,
    ): void {
        $instance = $this->get_mocked_instance_for_version($version);

        if ($expected) {
            $this->assertEmpty($instance->requires_version($testversion));
        } else {
            $this->expectException('moodle_exception');
            $instance->requires_version($testversion);
        }
    }

    /**
     * Data provider for meets_version tests.
     *
     * @return array
     */
    public static function meets_version_provider(): array {
        return [
            'Same version' => ['v1.1', '1.1', true],
            'Same version latest' => ['v1.7', '1.7', true],
            'Newer version rejected' => ['v1.1', '1.7', false],
            'Older version accepted' => ['v1.7', '1.1', true],
        ];
    }

    /**
     * Test the execute method with a command.
     *
     * @covers ::execute
     */
    public function test_command_is_executed(): void {
        $historycontainer = [];
        $mock = new MockHandler();

        $instance = $this->get_mocked_instance_for_version('v1.6', $historycontainer, $mock);
        $command = new command(
            $instance,
            method: 'GET',
            endpoint: 'test/endpoint',
            params: [
                'test' => 'test',
            ],
        );

        $mock->append(new Response(200));

        $rc = new \ReflectionClass($instance);
        $rcm = $rc->getMethod('execute');
        $result = $rcm->invoke($instance, $command);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertCount(1, $historycontainer);
        $request = array_shift($historycontainer);
        $this->assertEquals('GET', $request['request']->getMethod());
        $this->assertEquals('/test/endpoint', $request['request']->getUri()->getPath());
    }
}
