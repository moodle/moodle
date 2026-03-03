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

use Aws\BedrockRuntime\BedrockRuntimeClient;
use Aws\Command;
use Aws\Exception\AwsException;
use Aws\Result;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\MockObject\MockObject;
use core_ai\aiactions\base;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/testcase_helper_trait.php');

/**
 * Test generate image provider class for AWS Bedrock provider methods.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \aiprovider_awsbedrock\provider
 * @covers     \aiprovider_awsbedrock\process_generate_image
 * @covers     \aiprovider_awsbedrock\abstract_processor
 */
final class process_generate_image_test extends \advanced_testcase {
    use testcase_helper_trait;

    /** @var \core_ai\manager */
    private $manager;

    /** @var provider The provider that will process the action. */
    protected provider $provider;

    /** @var base The action to process. */
    protected base $action;

    /** @var \stored_file Test stored file. */
    protected \stored_file $testfile;

    /**
     * Set up the test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->manager = \core\di::get(\core_ai\manager::class);
        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_image::class,
            actionconfig: [
                'model' => 'amazon.nova-canvas-v1:0',
            ],
        );
        $this->create_action();
        $this->testfile = $this->create_test_file();
    }

    /**
     * Create the action object.
     *
     * @param int $userid The user id to use in the action.
     */
    private function create_action(int $userid = 1): void {
        $this->action = new \core_ai\aiactions\generate_image(
            contextid: 1,
            userid: $userid,
            prompttext: 'This is a test prompt',
            quality: 'hd',
            aspectratio: 'square',
            numimages: 1,
            style: 'vivid',
        );
    }

    /**
     * Create a mocked Aws\Result.
     *
     * @param bool $success Whether the result should be successful or not.
     * @return Result| AwsException The mocked result.
     */
    private function get_mocked_aws_result(bool $success): Result| AwsException {

        if ($success) {
            $mockresponsebody = '{
                "images":[
                    "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIW2NgYGD4DwABBAEAwS2OUAAAAABJRU5ErkJggg=="
                ]
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
        } else {
            return new AwsException(
                'AccessDeniedException: You do not have permission to access this resource',
                new Command('InvokeModel'),
                [
                    'code' => 'AccessDeniedException',
                    'message' => 'You do not have permission to invoke this model',
                    'response' => new Response(403),
                ]
            );
        }
    }

    /**
     * Create a mocked AWS invokeModel result.
     *
     * @param bool $success Whether the result should be successful or not.
     * @return MockObject The mocked provider.
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    private function get_mocked_aws_invoke_model_result(bool $success = true): MockObject {
        $actionconfig = $this->get_action_config(\core_ai\aiactions\generate_image::class);

        // Create a mock of the Bedrock client.
        $mockclient = $this->createMock(BedrockRuntimeClient::class);

        // Properly mock the invokeModel call using __call.
        if ($success) {
            $mockclient->expects($this->any())
                ->method('__call')
                ->with('invokeModel', $this->anything()) // AWS SDK calls are dynamic via __call.
                ->willReturn($this->get_mocked_aws_result($success));
        } else {
            $mockclient->expects($this->any())
                ->method('__call')
                ->with('invokeModel', $this->anything()) // AWS SDK calls are dynamic via __call.
                ->willThrowException($this->get_mocked_aws_result($success));
        };

        // Now properly mock the provider while calling its constructor.
        $mockprovider = $this->getMockBuilder(get_class($this->provider))
            ->setConstructorArgs([
                true, // Enable the provider.
                'mockprovider', // Provider name.
                '{}', // Empty config is ok here.
                json_encode($actionconfig), // Action config.
            ])
            ->onlyMethods(['create_bedrock_client']) // Only mock this method.
            ->getMock();

        // Ensure the mock returns our fake client.
        $mockprovider->method('create_bedrock_client')
            ->willReturn($mockclient);

        // Ensure the mock returns our fake client.
        $mockprovider->method('create_bedrock_client')
            ->willReturn($mockclient);

        return $mockprovider;
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
            'revisedprompt' => 'This is a test prompt',
            'draftfile' => $this->testfile,
        ];

        $responseerror = [
            'success' => false,
            'errorcode' => 401,
            'error' => 'Internal server error',
            'errormessage' => 'Invalid Authentication',
        ];

        $processor = $this->getMockBuilder(process_generate_image::class)
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
     * Test create_request
     */
    public function test_create_request(): void {
        $processor = new process_generate_image($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'create_request');
        $request = $method->invoke($processor);

        $body = (object) json_decode($request['body']);

        $this->assertEquals('amazon.nova-canvas-v1:0', $request['modelId']);
        $this->assertnotNull($body);
    }

    /**
     * Test create_request with extra model settings.
     */
    public function test_create_request_with_model_settings(): void {
        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_image::class,
            actionconfig: [
                'model' => 'amazon.nova-canvas-v1:0',
                'cfgScale' => 6.5,
                'seed' => 12,
            ],
        );
        $processor = new process_generate_image($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'create_request');
        $request = $method->invoke($processor);

        $body = (object) json_decode($request['body']);

        $this->assertEquals('amazon.nova-canvas-v1:0', $request['modelId']);
        $this->assertEquals('6.5', $body->imageGenerationConfig->cfgScale);
        $this->assertEquals('12', $body->imageGenerationConfig->seed);
    }

    /**
     * Data provider for AWS API error responses.
     *
     * @return array The test cases from aws_api_error_provider_helper().
     */
    public static function aws_api_error_provider(): array {
        return self::aws_api_error_provider_helper();
    }

    /**
     * Test handling of various API errors from AWS Bedrock.
     *
     * @dataProvider aws_api_error_provider
     * @param AwsException $exception The AWS exception to simulate.
     * @param int $expectedstatus The expected HTTP status code in the error response.
     */
    public function test_handle_api_error(AwsException $exception, int $expectedstatus): void {
        // Create an instance of the class that processes API errors.
        $processor = new process_generate_image($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_error');

        $result = $method->invoke($processor, $exception);
        // Assert that the returned error code matches the expected HTTP status.
        $this->assertEquals($expectedstatus, $result['errorcode'], "Failed asserting for status $expectedstatus");
    }

    /**
     * Test the API success response handler method.
     */
    public function test_handle_api_success(): void {
        // Mock the response from the base64_to_file method.
        $processor = $this->getMockBuilder(process_generate_image::class)
            ->setConstructorArgs([$this->provider, $this->action])
            ->onlyMethods(['base64_to_file'])
            ->getMock();
        $processor->method('base64_to_file')
            ->willReturn($this->testfile);

        $method = new \ReflectionMethod($processor, 'handle_api_success');
        $result = $method->invoke($processor, $this->get_mocked_aws_result(true));

        $this->assertTrue($result['success']);
        $this->assertEquals('mock-request-id', $result['fingerprint']);
        $this->assertEquals('11', $result['prompttokens']);
        $this->assertEquals('568', $result['completiontokens']);
        $this->assertEquals('amazon.nova-canvas-v1:0', $result['model']);
    }

    /**
     * Test API success response handling when Bedrock metadata headers are missing.
     */
    public function test_handle_api_success_with_missing_headers(): void {
        $processor = $this->getMockBuilder(process_generate_image::class)
            ->setConstructorArgs([$this->provider, $this->action])
            ->onlyMethods(['base64_to_file'])
            ->getMock();
        $processor->method('base64_to_file')
            ->willReturn($this->testfile);
        $method = new \ReflectionMethod($processor, 'handle_api_success');
        $stream = Utils::streamFor('{
            "images":[
                "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIW2NgYGD4DwABBAEAwS2OUAAAAABJRU5ErkJggg=="
            ]
        }');
        $resultobj = new Result([
            'body' => $stream,
            'contentType' => 'application/json',
            '@metadata' => [
                'statusCode' => 200,
                'headers' => [],
            ],
        ]);

        $result = $method->invoke($processor, $resultobj);

        $this->assertTrue($result['success']);
        $this->assertEquals('', $result['fingerprint']);
        $this->assertEquals('0', $result['prompttokens']);
        $this->assertEquals('0', $result['completiontokens']);
    }

    /**
     * Test API success handling when Bedrock response does not contain an image payload.
     */
    public function test_handle_api_success_with_missing_image_payload(): void {
        $processor = new process_generate_image($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_success');
        $stream = Utils::streamFor('{}');
        $resultobj = new Result([
            'body' => $stream,
            'contentType' => 'application/json',
            '@metadata' => [
                'statusCode' => 200,
                'headers' => [],
            ],
        ]);

        $result = $method->invoke($processor, $resultobj);

        $this->assertFalse($result['success']);
        $this->assertEquals(200, $result['errorcode']);
        $this->assertEquals('InvalidResponseException', $result['error']);
    }

    /**
     * Test base64_to_file.
     */
    public function test_base64_to_file(): void {
        // Log in user.
        $this->setUser($this->getDataGenerator()->create_user());
        $base64 = file_get_contents(self::get_fixture_path('aiprovider_awsbedrock', 'test_image.base64'));

        $processor = new process_generate_image($this->provider, $this->action);
        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'base64_to_file');

        $filenobj = $method->invoke($processor, $base64);

        $this->assertEquals('aa7ff9e16b9cdafd.jpeg', $filenobj->get_filename());
        $this->assertEquals('image/jpeg', $filenobj->get_mimetype());
        $this->assertgreaterThan(0, $filenobj->get_filesize());
    }

    /**
     * Test base64_to_file with invalid base64 payload.
     */
    public function test_base64_to_file_with_invalid_base64(): void {
        $processor = new process_generate_image($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'base64_to_file');

        $this->expectException(\coding_exception::class);
        $method->invoke($processor, '#invalid-base64#');
    }

    /**
     * Test query_ai_api for a successful call.
     */
    public function test_query_ai_api_success(): void {
        // Log in user.
        $this->setUser($this->getDataGenerator()->create_user());

        // Create an instance of the processor with the mocked provider.
        $mockclient = $this->get_mocked_aws_invoke_model_result();
        $processor = new process_generate_image($mockclient, $this->action);

        // We're testing a protected method, so we need to setup reflector magic.
        $method = new \ReflectionMethod($processor, 'query_ai_api');

        // Invoke the query_ai_api method.
        $result = $method->invoke($processor);

        // Assertions.
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals('mock-request-id', $result['fingerprint']);
        $this->assertEquals('This is a test prompt', $result['revisedprompt']);
        $this->assertEquals('11', $result['prompttokens']);
        $this->assertEquals('568', $result['completiontokens']);
        $this->assertEquals('15a539ba5aa81f35.png', $result['draftfile']->get_filename());
    }

    /**
     * Test prepare_response success.
     */
    public function test_prepare_response_success(): void {
        $processor = new process_generate_image($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'prepare_response');

        $response = [
            'success' => true,
            'revisedprompt' => 'An image that represents the concept of a \'test\'.',
            'imageurl' => 'oaidalleapiprodscus.blob.core.windows.net',
            'model' => 'amazon.nova-canvas-v1:0',
        ];

        $result = $method->invoke($processor, $response);

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertTrue($result->get_success());
        $this->assertEquals('generate_image', $result->get_actionname());
        $this->assertEquals($response['success'], $result->get_success());
        $this->assertEquals($response['revisedprompt'], $result->get_response_data()['revisedprompt']);
        $this->assertEquals($response['model'], $result->get_response_data()['model']);
    }

    /**
     * Test prepare_response error.
     */
    public function test_prepare_response_error(): void {
        $processor = new process_generate_image($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'prepare_response');

        $response = [
            'success' => false,
            'errorcode' => 500,
            'error' => 'Internal server error',
            'errormessage' => 'Internal server error.',
        ];

        $result = $method->invoke($processor, $response);

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertFalse($result->get_success());
        $this->assertEquals('generate_image', $result->get_actionname());
        $this->assertEquals($response['errorcode'], $result->get_errorcode());
        $this->assertEquals($response['errormessage'], $result->get_errormessage());
    }

    /**
     * Test process.
     */
    public function test_process(): void {
        // Log in user.
        $this->setUser($this->getDataGenerator()->create_user());

        // Create a request object.
        $contextid = 1;
        $userid = 1;
        $prompttext = 'This is a test prompt';
        $aspectratio = 'square';
        $quality = 'hd';
        $numimages = 1;
        $style = 'vivid';
        $this->action = new \core_ai\aiactions\generate_image(
            contextid: $contextid,
            userid: $userid,
            prompttext: $prompttext,
            quality: $quality,
            aspectratio: $aspectratio,
            numimages: $numimages,
            style: $style,
        );

        $mockclient = $this->get_mocked_aws_invoke_model_result();
        $processor = new process_generate_image($mockclient, $this->action);
        $result = $processor->process();

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertTrue($result->get_success());
        $this->assertEquals('generate_image', $result->get_actionname());
        $this->assertEquals('This is a test prompt', $result->get_response_data()['revisedprompt']);
        $this->assertEquals('15a539ba5aa81f35.png', $result->get_response_data()['draftfile']->get_filename());
    }

    /**
     * Test process method with error.
     */
    public function test_process_error(): void {
        // Log in user.
        $this->setUser($this->getDataGenerator()->create_user());

        $mockclient = $this->get_mocked_aws_invoke_model_result(false);
        $processor = new process_generate_image($mockclient, $this->action);

        $result = $processor->process();

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertFalse($result->get_success());
        $this->assertEquals('generate_image', $result->get_actionname());
        $this->assertEquals(403, $result->get_errorcode());
        $this->assertEquals('You do not have permission to invoke this model', $result->get_errormessage());
    }

    /**
     * Test process method with user rate limiter.
     */
    public function test_process_with_user_rate_limiter(): void {
        // Create users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        // Log in user1.
        $this->setUser($user1);
        // Mock clock.
        $clock = $this->mock_clock_with_frozen();

        // Set the user rate limiter.
        $config = [
            'apikey' => '123',
            'enableuserratelimit' => true,
            'userratelimit' => 1,
        ];
        $provider = $this->manager->create_provider_instance(
            classname: '\aiprovider_awsbedrock\provider',
            name: 'dummy',
            config: $config,
            actionconfig: [
                \core_ai\aiactions\generate_image::class => [
                    'settings' => [
                        'model' => 'amazon.nova-canvas-v1:0',
                        'cfgScale' => 6.5,
                        'seed' => 12,
                        'awsregion' => 'us-east-1',
                    ],
                ],
            ],
        );

        // Case 1: User rate limit has not been reached.
        $this->create_action($user1->id);
        $processor = $this->get_mocked_query_ai_api_result($provider);
        $result = $processor->process();
        $this->assertTrue($result->get_success());

        // Case 2: User rate limit has been reached.
        $clock->bump(HOURSECS - 10);
        $this->create_action($user1->id);

        $result = $processor->process();
        $this->assertEquals(429, $result->get_errorcode());
        $this->assertEquals(
            expected: 'You have reached the maximum number of AI requests you can make in an hour. Try again later.',
            actual: $result->get_errormessage(),
        );
        $this->assertFalse($result->get_success());

        // Case 3: User rate limit has not been reached for a different user.
        // Log in user2.
        $this->setUser($user2);
        $this->create_action($user2->id);

        $processor = $this->get_mocked_query_ai_api_result($provider);
        $result = $processor->process();
        $this->assertTrue($result->get_success());

        // Case 4: Time window has passed, user rate limit should be reset.
        $clock->bump(11);
        // Log in user1.
        $this->setUser($user1);
        $this->provider = $this->create_provider(\core_ai\aiactions\generate_image::class);
        $this->create_action($user1->id);
        $processor = $this->get_mocked_query_ai_api_result($provider);
        $result = $processor->process();
        $this->assertTrue($result->get_success());
    }

    /**
     * Test process method with global rate limiter.
     */
    public function test_process_with_global_rate_limiter(): void {
        // Create users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        // Log in user1.
        $this->setUser($user1);
        // Mock clock.
        $clock = $this->mock_clock_with_frozen();

        // Set the global rate limiter.
        $config = [
            'apikey' => '123',
            'enableglobalratelimit' => true,
            'globalratelimit' => 1,
        ];
        $provider = $this->manager->create_provider_instance(
            classname: '\aiprovider_awsbedrock\provider',
            name: 'dummy',
            config: $config,
            actionconfig: [
                \core_ai\aiactions\generate_image::class => [
                    'settings' => [
                        'model' => 'amazon.nova-canvas-v1:0',
                        'cfgScale' => 6.5,
                        'seed' => 12,
                        'awsregion' => 'us-east-1',
                    ],
                ],
            ],
        );

        // Case 1: Global rate limit has not been reached.
        $this->create_action($user1->id);
        $processor = $this->get_mocked_query_ai_api_result($provider);
        $result = $processor->process();
        $this->assertTrue($result->get_success());

        // Case 2: Global rate limit has been reached.
        $clock->bump(HOURSECS - 10);
        $this->create_action($user1->id);
        $processor = $this->get_mocked_query_ai_api_result($provider);
        $result = $processor->process();
        $this->assertEquals(429, $result->get_errorcode());
        $this->assertEquals(
            expected: 'The AI service has reached the maximum number of site-wide requests per hour. Try again later.',
            actual: $result->get_errormessage(),
        );
        $this->assertFalse($result->get_success());

        // Case 3: Global rate limit has been reached for a different user too.
        // Log in user2.
        $this->setUser($user2);
        $this->create_action($user2->id);
        $processor = $this->get_mocked_query_ai_api_result($provider);
        $result = $processor->process();
        $this->assertFalse($result->get_success());

        // Case 4: Time window has passed, global rate limit should be reset.
        $clock->bump(11);
        // Log in user1.
        $this->setUser($user1);
        $this->create_action($user1->id);
        $processor = $this->get_mocked_query_ai_api_result($provider);
        $result = $processor->process();
        $this->assertTrue($result->get_success());
    }

    /**
     * Test create_amazon_request method.
     */
    public function test_create_amazon_request(): void {
        $processor = new process_generate_image($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'create_amazon_request');

        $requestobj = new \stdClass();
        $modelsettings = [
            'model' => 'amazon.nova-canvas-v1:0',
            'cfgScale' => 6.5,
            'seed' => 12,
        ];
        $requestobj = $method->invoke($processor, $requestobj, $modelsettings);

        $this->assertEquals('6.5', $requestobj->imageGenerationConfig->cfgScale);
        $this->assertEquals('12', $requestobj->imageGenerationConfig->seed);
        $this->assertEquals('TEXT_IMAGE', $requestobj->taskType);
        $this->assertEquals('This is a test prompt', $requestobj->textToImageParams->text);
        $this->assertEquals(1792, $requestobj->imageGenerationConfig->width);
        $this->assertEquals(1792, $requestobj->imageGenerationConfig->height);
    }

    /**
     * Tests the create_stability_request() method.
     *
     * Verifies that the request object is correctly populated with the expected values based on the provided model settings.
     */
    public function test_create_stability_request(): void {
        $processor = new process_generate_image($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'create_stability_request');

        $requestobj = new \stdClass();
        $modelsettings = [
            'model' => 'stability.stable-image-core-v1:1',
            'seed' => 12,
            'negative_prompt' => 'pink',
        ];
        $requestobj = $method->invoke($processor, $requestobj, $modelsettings);
        $this->assertEquals('This is a test prompt', $requestobj->prompt);
        $this->assertEquals('12', $requestobj->seed);
        $this->assertEquals('1:1', $requestobj->aspect_ratio);
        $this->assertEquals('pink', $requestobj->negative_prompt);
    }

    /**
     * Tests the get_stability_aspect_ratio() method.
     * Verifies that the correct aspect ratio value is returned for a given combination of aspect ratio and quality settings.
     *
     * @covers ::get_stability_aspect_ratio
     * @dataProvider aspect_ratio_provider
     *
     * @param string $aspectratio The requested aspect ratio (e.g. square, landscape, portrait).
     * @param string $quality     The requested quality setting (e.g. standard, hd).
     * @param string $expected    The expected Stability aspect ratio value.
     */
    public function test_get_stability_aspect_ratio(string $aspectratio, string $quality, string $expected): void {
        $processor = new process_generate_image($this->provider, $this->action);

        $method = new \ReflectionMethod($processor, 'get_stability_aspect_ratio');

        $result = $method->invoke($processor, $aspectratio, $quality);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for test_get_stability_aspect_ratio().
     * Provides combinations of aspect ratio and quality values and their expected Stability aspect ratio outputs.
     *
     * @return array
     */
    public static function aspect_ratio_provider(): array {
        return [
            'square standard' => ['square', 'standard', '1:1'],
            'square hd' => ['square', 'hd', '1:1'],
            'landscape standard' => ['landscape', 'standard', '16:9'],
            'landscape hd' => ['landscape', 'hd', '3:2'],
            'portrait standard' => ['portrait', 'standard', '9:16'],
            'portrait hd' => ['portrait', 'hd', '4:5'],
            'invalid aspect falls back to square' => ['invalid', 'standard', '1:1'],
            'invalid quality falls back to standard' => ['landscape', 'invalid', '1:1'],
        ];
    }
}
