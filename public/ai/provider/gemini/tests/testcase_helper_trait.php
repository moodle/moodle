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

namespace aiprovider_gemini;

use GuzzleHttp\Psr7\Response;

/**
 * Trait for test cases.
 *
 * @package    aiprovider_gemini
 * @copyright  2026 Anupama Sarjoshi <anupama.sarjoshi@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait testcase_helper_trait {
    /**
     * Create the provider object.
     *
     * @param string $actionclass The action class to use.
     * @param array $actionconfig The action configuration to use.
     */
    public function create_provider(
        string $actionclass,
        array $actionconfig = [],
    ): \core_ai\provider {
        $manager = \core\di::get(\core_ai\manager::class);
        $config = [
            'apikey' => '123',
            'enableuserratelimit' => true,
            'userratelimit' => 1,
            'enableglobalratelimit' => true,
            'globalratelimit' => 1,
        ];
        $defaultactionconfig = [
            $actionclass => [
                'settings' => [
                    'model' => 'gemini-2.5-flash',
                    'endpoint' => "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent",
                ],
            ],
        ];
        foreach ($actionconfig as $key => $value) {
            $defaultactionconfig[$actionclass]['settings'][$key] = $value;
        }
        $provider = $manager->create_provider_instance(
            classname: '\aiprovider_gemini\provider',
            name: 'dummy',
            config: $config,
            actionconfig: $defaultactionconfig,
        );

        return $provider;
    }

    /**
     * Create a test file.
     *
     * @return \stored_file The test file.
     */
    private function create_test_file(): \stored_file {
        $fs = get_file_storage();
        $fileinfo = [
            'contextid' => 1,
            'component' => 'draft',
            'filearea' => 'user',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'testfile.txt',
        ];
        $testfile = $fs->create_file_from_string($fileinfo, 'This is test file content.');
        return $testfile;
    }

    /**
     * Get the error responses.
     *
     * @return array The error responses.
     */
    private function get_error_responses(): array {
        return [
            500 => new Response(
                500,
                ['Content-Type' => 'application/json']
            ),
            503 => new Response(
                503,
                ['Content-Type' => 'application/json']
            ),
            401 => new Response(
                401,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'error' => [
                        'code' => 401,
                        'message' => 'Invalid Authentication',
                        'status' => 'UNAUTHENTICATED',
                    ],
                ]),
            ),
            404 => new Response(
                404,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'error' => [
                        'code' => 404,
                        'message' => 'You must be a member of an organization to use the API',
                        'status' => 'NOT_FOUND',
                    ],
                ]),
            ),
            429 => new Response(
                429,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'error' => [
                        'code' => 429,
                        'message' => 'Rate limit reached for requests',
                        'status' => 'RESOURCE_EXHAUSTED',
                    ],
                ]),
            ),
        ];
    }
}
