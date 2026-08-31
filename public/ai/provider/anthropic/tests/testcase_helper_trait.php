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

namespace aiprovider_anthropic;

use GuzzleHttp\Psr7\Response;

/**
 * Trait providing shared setup helpers for Anthropic provider test cases.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait testcase_helper_trait {
    /**
     * Create a configured Anthropic provider instance for testing.
     *
     * @param string $actionclass The action class to configure.
     * @param array $actionconfig Additional action-level settings to merge in.
     * @return \core_ai\provider
     */
    protected function create_provider(
        string $actionclass,
        array $actionconfig = [],
    ): \core_ai\provider {
        $manager = \core\di::get(\core_ai\manager::class);
        $config = [
            'apikey' => 'test-api-key',
            'enableuserratelimit' => true,
            'userratelimit' => 1,
            'enableglobalratelimit' => true,
            'globalratelimit' => 1,
        ];
        $defaultactionconfig = [
            $actionclass => [
                'settings' => [
                    'model' => 'claude-sonnet-4-5-20250929',
                    'endpoint' => abstract_processor::ANTHROPIC_API_ENDPOINT,
                    'max_tokens' => 8096,
                ],
            ],
        ];
        foreach ($actionconfig as $key => $value) {
            $defaultactionconfig[$actionclass]['settings'][$key] = $value;
        }
        return $manager->create_provider_instance(
            classname: '\aiprovider_anthropic\provider',
            name: 'dummy',
            config: $config,
            actionconfig: $defaultactionconfig,
        );
    }

    /**
     * Get a set of representative HTTP error responses from the Anthropic API.
     *
     * @return array HTTP status code => Response object.
     */
    private function get_error_responses(): array {
        return [
            500 => new Response(
                500,
                ['Content-Type' => 'application/json'],
            ),
            503 => new Response(
                503,
                ['Content-Type' => 'application/json'],
            ),
            401 => new Response(
                401,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'type' => 'error',
                    'error' => [
                        'type' => 'authentication_error',
                        'message' => 'invalid x-api-key',
                    ],
                ]),
            ),
            404 => new Response(
                404,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'type' => 'error',
                    'error' => [
                        'type' => 'not_found_error',
                        'message' => 'The requested resource could not be found.',
                    ],
                ]),
            ),
            429 => new Response(
                429,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'type' => 'error',
                    'error' => [
                        'type' => 'rate_limit_error',
                        'message' => 'Rate limit exceeded for requests.',
                    ],
                ]),
            ),
        ];
    }
}
