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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for Templates API.
 *
 * @package    core
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(\core\route\api\templates::class)]
final class templates_test extends route_testcase {
    /**
     * Test fetching templates.
     *
     * Note: This is a risky test because it relies on data in other parts of Moodle.
     *
     * @param string $path
     * @param array $requiredtemplates
     * @param array $requiredstrings
     */
    #[DataProvider('fetch_templates_provider')]
    public function test_fetch_known_templates(
        string $path,
        array $requiredtemplates,
        array $requiredstrings,
    ): void {
        $logfile = make_request_directory() . '/deprecated-api-route.log';
        $previouserrorlog = ini_get('error_log');
        ini_set('error_log', $logfile);

        $this->add_class_routes_to_route_loader(\core\route\api\templates::class);
        $response = $this->process_api_request('GET', "/templates/{$path}");

        $this->assert_valid_response($response);
        $payload = $this->decode_response($response, true);

        $this->assert_payload_contains($payload, $requiredtemplates, $requiredstrings);
        ini_set('error_log', $previouserrorlog);
    }

    /**
     * Data provider for template tests.
     *
     * @return array
     */
    public static function fetch_templates_provider(): array {
        return [
            'fetch single template' => [
                'boost/core/modal',
                ['core/modal'],
                [],
            ],
            'fetch nested template' => [
                'boost/core/local/modal/alert',
                [
                    'core/local/modal/alert',
                    'core/modal',
                ],
                [
                    'moodle/ok',
                ],
            ],
            'multiple templates' => [
                'boost/core/notification',
                [
                    'core/notification',
                    'core/notification_success',
                    'core/notification_warning',
                    'core/notification_error',
                    'core/notification_info',
                ],
                [
                    'core/dismissnotification',
                ],
            ],
        ];
    }

    /**
     * Assertthat the payload contains the required templates and strings.
     *
     * @param array $payload
     * @param array $requiredtemplates
     * @param array $requiredstrings
     */
    protected function assert_payload_contains(
        array $payload,
        array $requiredtemplates = [],
        array $requiredstrings = [],
    ): void {
        $this->assertArrayHasKey('templates', $payload);
        $this->assertArrayHasKey('strings', $payload);

        foreach ($requiredtemplates as $template) {
            $this->assertArrayHasKey($template, $payload['templates']);
        }
        foreach ($requiredstrings as $string) {
            $this->assertArrayHasKey($string, $payload['strings']);
        }
    }

    public function test_template_missing(): void {
        $logfile = make_request_directory() . '/deprecated-api-route.log';
        $previouserrorlog = ini_get('error_log');
        ini_set('error_log', $logfile);

        $this->add_class_routes_to_route_loader(\core\route\api\templates::class);
        $response = $this->process_api_request('GET', '/templates/boost/core/missing');

        ini_set('error_log', $previouserrorlog);

        $this->assert_not_found_response($response);
    }

    /**
     * The get_templates route is explicitly marked as unscoped, since it serves public template markup and
     * does not require any OAuth2 scope to access.
     */
    public function test_get_templates_is_unscoped(): void {
        $this->assert_route_is_unscoped([templates::class, 'get_templates']);
    }
}
