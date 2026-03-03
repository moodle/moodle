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

namespace aiprovider_awsbedrock;

use Aws\Result;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\MockObject\MockObject;
use Aws\Command;
use Aws\Exception\AwsException;
use GuzzleHttp\Psr7\Response;

/**
 * Trait for test cases.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait testcase_helper_trait {
    /**
     * Get the provider configuration.
     *
     * @return array The provider configuration.
     */
    public function get_provider_config(): array {
        return [
            'apikey' => '123',
            'apisecret' => '456',
            'enableuserratelimit' => true,
            'userratelimit' => 1,
            'enableglobalratelimit' => true,
            'globalratelimit' => 1,
        ];
    }

    /**
     * Get the action configuration.
     *
     * @param string $actionclass The action class to use.
     * @param array $actionconfig The action configuration to use.
     * @return array The action configuration.
     */
    public function get_action_config(string $actionclass, array $actionconfig = []): array {
        $defaultactionconfig = [
            $actionclass => [
                'settings' => [
                    'model' => 'amazon.nova-pro-v1:0',
                    'awsregion' => 'ap-southeast-2',
                ],
            ],
        ];
        foreach ($actionconfig as $key => $value) {
            $defaultactionconfig[$actionclass]['settings'][$key] = $value;
        }

        return $defaultactionconfig;
    }

    /**
     * Create the provider object.
     *
     * @param string $actionclass The action class to use.
     * @param array $actionconfig The action configuration to use.
     * @return provider The provider object.
     */
    public function create_provider(
        string $actionclass,
        array $actionconfig = [],
    ): provider {
        $manager = \core\di::get(\core_ai\manager::class);
        $config = $this->get_provider_config();
        $defaultactionconfig = [
            $actionclass => [
                'settings' => [
                    'model' => 'amazon.nova-pro-v1:0',
                    'awsregion' => 'ap-southeast-2',
                ],
            ],
        ];
        foreach ($actionconfig as $key => $value) {
            $defaultactionconfig[$actionclass]['settings'][$key] = $value;
        }
        $provider = $manager->create_provider_instance(
            classname: '\aiprovider_awsbedrock\provider',
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
        $this->testfile = $fs->create_file_from_string($fileinfo, 'This is test file content.');
        return $this->testfile;
    }

    /**
     * Create a mocked Aws\Result.
     */
    private function get_mocked_aws_result(): Result {
        $mockresponsebody = '{
            "output":{
                "message":{
                    "content":[{"text":"The capital of Australia is Canberra."}],
                    "role":"assistant"
                }
            },
            "stopReason":"end_turn",
            "usage":{
                "inputTokens":11,
                "outputTokens":568,
                "totalTokens":579,
                "cacheReadInputTokenCount":0,
                "cacheWriteInputTokenCount":0
            }
        }';

        // Create a PSR-7 Stream for the response body.
        $stream = Utils::streamFor($mockresponsebody);

        // Create a mocked Aws\Result.
        return new Result([
            'body' => $stream, // Simulate AWS SDK response body.
            'contentType' => 'application/json',
            '@metadata' => [
                'statusCode' => 200,
                'headers' => [
                    'x-amzn-requestid' => 'mock-request-id',
                    'x-amzn-bedrock-input-token-count' => '11',
                    'x-amzn-bedrock-output-token-count' => '568',
                ],
            ],
        ]);
    }

    /**
     * Mock the result from query_ai_api().
     * Returns a MockBuilder object for a processor.
     *
     * @param provider $provider The provider to use.
     * @param bool $success Whether the query was successful.
     * @return MockObject The mocked processor object.
     */
    private function get_mocked_query_ai_api_result(
        provider $provider,
        bool $success = true
    ): MockObject {
        // Define the mock successful response from query_ai_api().
        $responsesuccess = [
            'success' => true,
            'fingerprint' => 'mock-request-id',
            'prompttokens' => '11',
            'completiontokens' => '568',
            'model' => 'amazon.nova-pro-v1:0',
            'generatedcontent' => 'The capital of Australia is Canberra.',
            'finishreason' => 'FINISHED',
        ];

        $responseerror = [
            'success' => false,
            'errorcode' => 401,
            'error' => 'Internal server error',
            'errormessage' => 'Invalid Authentication',
        ];

        // Create a partial mock of `process_summarise_text`, only mocking `query_ai_api()`.
        $processor = $this->getMockBuilder(process_summarise_text::class)
            ->setConstructorArgs([$provider, $this->action])
            ->onlyMethods(['query_ai_api'])
            ->getMock();

        // Mock `query_ai_api()` to return the predefined response.
        if ($success) {
            $processor->method('query_ai_api')
                ->willReturn($responsesuccess);
        } else {
            $processor->method('query_ai_api')
                ->willReturn($responseerror);
        }

        return $processor;
    }

    /**
     * Data provider for AWS API error responses.
     * Provides a set of AWS exceptions with corresponding HTTP status codes to test error handling in the AWS Bedrock provider.
     *
     * @return array
     */
    public static function aws_api_error_provider_helper(): array {
        // Mock an AWS Command.
        $command = new Command('InvokeModel');

        // Define various error responses.
        return [
            '400 Validation Exception' => [
                new AwsException(
                    'ValidationException: Invalid modelId',
                    $command,
                    [
                        'code' => 'ValidationException',
                        'response' => new Response(400, [], json_encode([
                            'message' => 'Invalid modelId: invalid-model-id',
                            'code' => 'ValidationException',
                        ])),
                    ],
                ),
                400,
            ],
            '403 Access Denied' => [
                new AwsException(
                    'AccessDeniedException: You do not have permission to access this resource',
                    $command,
                    [
                        'code' => 'AccessDeniedException',
                        'response' => new Response(403, [], json_encode([
                            'message' => 'You do not have permission to invoke this model',
                            'code' => 'AccessDeniedException',
                        ])),
                    ],
                ),
                403,
            ],
            '429 Throttling Exception' => [
                new AwsException(
                    'ThrottlingException: Too many requests',
                    $command,
                    [
                        'code' => 'ThrottlingException',
                        'response' => new Response(429, [], json_encode([
                            'message' => 'Rate limit exceeded, please try again later',
                            'code' => 'ThrottlingException',
                        ])),
                    ],
                ),
                429,
            ],
            '500 Internal Server Error' => [
                new AwsException(
                    'InternalServerException: AWS Bedrock encountered an error',
                    $command,
                    [
                        'code' => 'InternalServerException',
                        'response' => new Response(500, [], json_encode([
                            'message' => 'An internal server error occurred',
                            'code' => 'InternalServerException',
                        ])),
                    ],
                ),
                500,
            ],
        ];
    }
}
