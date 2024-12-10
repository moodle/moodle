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

namespace core\route\api;

use core\tests\router\route_testcase;

/**
 * Tests for Templates API.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(strings::class)]
final class strings_test extends route_testcase {
    /**
     * Test fetching strings.
     *
     * @param string $component
     * @param string $identifier
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('fetch_templates_provider')]
    public function test_known_strings(
        string $component,
        string $identifier,
    ): void {
        $this->add_class_routes_to_route_loader(\core\route\api\strings::class);
        $response = $this->process_api_request('GET', "/strings/en/{$component}/{$identifier}");

        $this->assert_valid_response($response);
        $payload = $this->decode_response($response, true);
        $this->assertArrayHasKey('strings', $payload);

        $this->assertEquals(
            [
                "{$component}/{$identifier}" => get_string($identifier, $component),
            ],
            $payload['strings'],
        );
    }

    /**
     * Test fetching missing strings.
     *
     * @param string $component
     * @param string $identifier
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('fetch_missing_strings_provider')]
    public function test_missing_strings(
        string $component,
        string $identifier,
    ): void {
        $this->add_class_routes_to_route_loader(\core\route\api\strings::class);
        $response = $this->process_api_request('GET', "/strings/en/{$component}/{$identifier}");

        $this->assertDebuggingCalled();

        $this->assert_valid_response($response);
        $payload = $this->decode_response($response, true);
        $this->assertArrayHasKey('strings', $payload);

        $this->assertEquals(
            [
                "{$component}/{$identifier}" => "[[{$identifier}]]",
            ],
            $payload['strings'],
        );
    }

    /**
     * Data propvider for template tests.
     *
     * @return \Generator
     */
    public static function fetch_templates_provider(): \Generator {
        yield 'Standard string' => [
            'core',
            'accept',
        ];

        yield 'String contains a slash' => [
            'core_mimetypes',
            'application/dash_xml',
        ];
        yield 'String contains a colon' => [
            'core',
            'privacy:metadata:log',
        ];
    }

    /**
     * Data provider for missing string tests.
     *
     * @return \Generator
     */
    public static function fetch_missing_strings_provider(): \Generator {
        yield 'Standard string' => [
            'core',
            'missingstring',
        ];
        yield 'Missing component' => [
            'core_made_up_component',
            'missingstring',
        ];
    }
}
