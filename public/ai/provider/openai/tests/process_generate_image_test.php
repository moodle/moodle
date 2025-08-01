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

namespace aiprovider_openai;

use aiprovider_openai\test\testcase_helper_trait;
use core_ai\aiactions\base;
use core_ai\provider;
use GuzzleHttp\Psr7\Response;

/**
 * Test response_base OpenAI provider methods.
 *
 * @package    aiprovider_openai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \aiprovider_openai\provider
 * @covers     \aiprovider_openai\process_generate_image
 * @covers     \aiprovider_openai\abstract_processor
 */
final class process_generate_image_test extends \advanced_testcase {

    use testcase_helper_trait;

    /** @var string A successful response in JSON format. */
    protected string $responsebodyjson;

    /** @var \core_ai\manager */
    private $manager;

    /** @var provider The provider that will process the action. */
    protected provider $provider;

    /** @var base The action to process. */
    protected base $action;

    /**
     * Set up the test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        // Load a response body from a file.
        $this->responsebodyjson = file_get_contents(self::get_fixture_path('aiprovider_openai', 'image_request_success.json'));
        $this->manager = \core\di::get(\core_ai\manager::class);
        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_image::class,
            actionconfig: [
                'model' => 'dall-e-3',
            ],
        );
        $this->create_action();
    }

    /**
     * Create the action object.
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
     * Test calculate_size.
     */
    public function test_calculate_size(): void {
        $processor = new process_generate_image($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'calculate_size');

        $ratio = 'square';
        $size = $method->invoke($processor, $ratio);
        $this->assertEquals('1024x1024', $size);

        $ratio = 'portrait';
        $size = $method->invoke($processor, $ratio);
        $this->assertEquals('1024x1792', $size);

        $ratio = 'landscape';
        $size = $method->invoke($processor, $ratio);
        $this->assertEquals('1792x1024', $size);
    }

    /**
     * Test create_request_object
     */
    public function test_create_request_object(): void {
        $processor = new process_generate_image($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $requestdata = (object) json_decode($request->getBody()->getContents());

        $this->assertEquals('This is a test prompt', $requestdata->prompt);
        $this->assertEquals('dall-e-3', $requestdata->model);
        $this->assertEquals('1', $requestdata->n);
        $this->assertEquals('hd', $requestdata->quality);
        $this->assertEquals('url', $requestdata->response_format);
        $this->assertEquals('1024x1024', $requestdata->size);
    }

    /**
     * Test create_request_object with extra model settings.
     */
    public function test_create_request_object_with_model_settings(): void {
        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_image::class,
            actionconfig: [
                'model' => 'dall-e-3',
                'temperature' => '0.5',
                'max_completion_tokens' => '100',
            ],
        );
        $processor = new process_generate_image($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = (object) json_decode($request->getBody()->getContents());

        $this->assertEquals('dall-e-3', $body->model);
        $this->assertEquals('0.5', $body->temperature);
        $this->assertEquals('100', $body->max_completion_tokens);

        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_image::class,
            actionconfig: [
                'model' => 'my-custom-gpt',
                'modelextraparams' => '{"temperature": 0.5,"max_completion_tokens": 100}',
            ],
        );
        $processor = new process_generate_image($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = (object) json_decode($request->getBody()->getContents());

        $this->assertEquals('my-custom-gpt', $body->model);
        $this->assertEquals('0.5', $body->temperature);
        $this->assertEquals('100', $body->max_completion_tokens);
    }

    /**
     * Test the API error response handler method.
     */
    public function test_handle_api_error(): void {
        $responses = [
            500 => new Response(500, ['Content-Type' => 'application/json']),
            503 => new Response(503, ['Content-Type' => 'application/json']),
            401 => new Response(401, ['Content-Type' => 'application/json'],
                '{"error": {"message": "Invalid Authentication"}}'),
            404 => new Response(404, ['Content-Type' => 'application/json'],
                '{"error": {"message": "You must be a member of an organization to use the API"}}'),
            429 => new Response(429, ['Content-Type' => 'application/json'],
                '{"error": {"message": "Rate limit reached for requests"}}'),
        ];

        $processor = new process_generate_image($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_error');

        foreach ($responses as $status => $response) {
            $result = $method->invoke($processor, $response);
            $this->assertEquals($status, $result['errorcode']);
            if ($status == 500) {
                $this->assertEquals('Internal Server Error', $result['errormessage']);
            } else if ($status == 503) {
                $this->assertEquals('Service Unavailable', $result['errormessage']);
            } else {
                $this->assertStringContainsString($response->getBody()->getContents(), $result['errormessage']);
            }
        }
    }

    /**
     * Test the API success response handler method.
     */
    public function test_handle_api_success(): void {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        );

        // We're testing a private method, so we need to setup reflector magic.
        $processor = new process_generate_image($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_success');

        $result = $method->invoke($processor, $response);

        $this->stringContains('An image that represents the concept of a \'test\'.', $result['revisedprompt']);
        $this->stringContains('oaidalleapiprodscus.blob.core.windows.net', $result['sourceurl']);
        $this->assertEquals('dall-e-3', $result['model']);
    }

    /**
     * Test query_ai_api for a successful call.
     */
    public function test_query_ai_api_success(): void {
        // Mock the http client to return a successful response.
        ['mock' => $mock] = $this->get_mocked_http_client();

        // The response from OpenAI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));

        $mock->append(new Response(
            200,
            ['Content-Type' => 'image/jpeg'],
            \GuzzleHttp\Psr7\Utils::streamFor(fopen(
                self::get_fixture_path('aiprovider_openai', 'test.jpg'),
                'r',
            )),
        ));

        $this->setAdminUser();

        $processor = new process_generate_image($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'query_ai_api');
        $result = $method->invoke($processor);

        $this->stringContains('An image that represents the concept of a \'test\'.', $result['revisedprompt']);
        $this->stringContains('oaidalleapiprodscus.blob.core.windows.net', $result['sourceurl']);
        $this->assertEquals('dall-e-3', $result['model']);
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
            'model' => 'dall-e-3',
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
            'errormessage' => 'Try again later',
        ];

        $result = $method->invoke($processor, $response);

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertFalse($result->get_success());
        $this->assertEquals('generate_image', $result->get_actionname());
        $this->assertEquals($response['errorcode'], $result->get_errorcode());
        $this->assertEquals($response['errormessage'], $result->get_errormessage());
    }

    /**
     * Test url_to_file.
     */
    public function test_url_to_file(): void {
        // Log in user.
        $this->setUser($this->getDataGenerator()->create_user());

        $processor = new process_generate_image($this->provider, $this->action);
        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'url_to_file');

        $contextid = 1;
        $url = $this->getExternalTestFileUrl('/test.jpg', false);
        $filenobj = $method->invoke($processor, $contextid, $url);

        $this->assertEquals('test.jpg', $filenobj->get_filename());
    }

    /**
     * Test process.
     */
    public function test_process(): void {
        // Log in user.
        $this->setUser($this->getDataGenerator()->create_user());

        // Mock the http client to return a successful response.
        ['mock' => $mock] = $this->get_mocked_http_client();

        $url = 'https://example.com/test.jpg';

        // The response from OpenAI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'created' => 1719140500,
                'data' => [
                    (object) [
                        'revised_prompt' => 'An image that represents the concept of a \'test\'.',
                        'url' => $url,
                    ],
                ],
            ]),
        ));

        // The image downloaded from the server successfully.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'image/jpeg'],
            \GuzzleHttp\Psr7\Utils::streamFor(fopen(self::get_fixture_path('aiprovider_openai', 'test.jpg'), 'r')),
        ));

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

        $processor = new process_generate_image($this->provider, $this->action);
        $result = $processor->process();

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertTrue($result->get_success());
        $this->assertEquals('generate_image', $result->get_actionname());
        $this->assertEquals('An image that represents the concept of a \'test\'.', $result->get_response_data()['revisedprompt']);
        $this->assertEquals($url, $result->get_response_data()['sourceurl']);
    }

    /**
     * Test process method with error.
     */
    public function test_process_error(): void {
        // Log in user.
        $this->setUser($this->getDataGenerator()->create_user());

        // Mock the http client to return a successful response.
        ['mock' => $mock] = $this->get_mocked_http_client();

        // The response from OpenAI.
        $mock->append(new Response(
            401,
            ['Content-Type' => 'application/json'],
            json_encode(['error' => ['message' => 'Invalid Authentication']]),
        ));

        $processor = new process_generate_image($this->provider, $this->action);
        $result = $processor->process();

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertFalse($result->get_success());
        $this->assertEquals('generate_image', $result->get_actionname());
        $this->assertEquals(401, $result->get_errorcode());
        $this->assertEquals('Invalid Authentication', $result->get_errormessage());
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
            classname: '\aiprovider_openai\provider',
            name: 'dummy',
            config: $config,
            actionconfig: [
                \core_ai\aiactions\generate_image::class => [
                    'settings' => [
                        'model' => 'dall-e-3',
                        'endpoint' => "https://api.openai.com/v1/chat/completions",
                    ],
                ],
            ],
        );

        // Mock the http client to return a successful response.
        ['mock' => $mock] = $this->get_mocked_http_client();
        $url = 'https://example.com/test.jpg';

        // Case 1: User rate limit has not been reached.
        $this->create_action($user1->id);
        // The response from OpenAI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'created' => 1719140500,
                'data' => [
                    (object) [
                        'revised_prompt' => 'An image that represents the concept of a \'test\'.',
                        'url' => $url,
                    ],
                ],
            ]),
        ));
        // The image downloaded from the server successfully.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'image/jpeg'],
            \GuzzleHttp\Psr7\Utils::streamFor(fopen(self::get_fixture_path('aiprovider_openai', 'test.jpg'), 'r')),
        ));
        $processor = new process_generate_image($provider, $this->action);
        $result = $processor->process();
        $this->assertTrue($result->get_success());

        // Case 2: User rate limit has been reached.
        $clock->bump(HOURSECS - 10);
        // The response from OpenAI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'created' => 1719140500,
                'data' => [
                    (object) [
                        'revised_prompt' => 'An image that represents the concept of a \'test\'.',
                        'url' => $url,
                    ],
                ],
            ]),
        ));
        // The image downloaded from the server successfully.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'image/jpeg'],
            \GuzzleHttp\Psr7\Utils::streamFor(fopen(self::get_fixture_path('aiprovider_openai', 'test.jpg'), 'r')),
        ));
        $this->create_action($user1->id);
        $processor = new process_generate_image($provider, $this->action);
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
        // The response from OpenAI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'created' => 1719140500,
                'data' => [
                    (object) [
                        'revised_prompt' => 'An image that represents the concept of a \'test\'.',
                        'url' => $url,
                    ],
                ],
            ]),
        ));
        // The image downloaded from the server successfully.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'image/jpeg'],
            \GuzzleHttp\Psr7\Utils::streamFor(fopen(self::get_fixture_path('aiprovider_openai', 'test.jpg'), 'r')),
        ));
        $processor = new process_generate_image($this->provider, $this->action);
        $result = $processor->process();
        $this->assertTrue($result->get_success());

        // Case 4: Time window has passed, user rate limit should be reset.
        $clock->bump(11);
        // Log in user1.
        $this->setUser($user1);
        // The response from OpenAI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'created' => 1719140500,
                'data' => [
                    (object) [
                        'revised_prompt' => 'An image that represents the concept of a \'test\'.',
                        'url' => $url,
                    ],
                ],
            ]),
        ));
        // The image downloaded from the server successfully.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'image/jpeg'],
            \GuzzleHttp\Psr7\Utils::streamFor(fopen(self::get_fixture_path('aiprovider_openai', 'test.jpg'), 'r')),
        ));
        $this->create_action($user1->id);
        $processor = new process_generate_image($provider, $this->action);
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
            classname: '\aiprovider_openai\provider',
            name: 'dummy',
            config: $config,
            actionconfig: [
                \core_ai\aiactions\generate_image::class => [
                    'settings' => [
                        'model' => 'dall-e-3',
                        'endpoint' => "https://api.openai.com/v1/chat/completions",
                    ],
                ],
            ],
        );

        // Mock the http client to return a successful response.
        ['mock' => $mock] = $this->get_mocked_http_client();
        $url = 'https://example.com/test.jpg';

        // Case 1: Global rate limit has not been reached.
        $this->create_action($user1->id);
        // The response from OpenAI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'created' => 1719140500,
                'data' => [
                    (object) [
                        'revised_prompt' => 'An image that represents the concept of a \'test\'.',
                        'url' => $url,
                    ],
                ],
            ]),
        ));
        // The image downloaded from the server successfully.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'image/jpeg'],
            \GuzzleHttp\Psr7\Utils::streamFor(fopen(self::get_fixture_path('aiprovider_openai', 'test.jpg'), 'r')),
        ));
        $processor = new process_generate_image($provider, $this->action);
        $result = $processor->process();
        $this->assertTrue($result->get_success());

        // Case 2: Global rate limit has been reached.
        $clock->bump(HOURSECS - 10);
        // The response from OpenAI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'created' => 1719140500,
                'data' => [
                    (object) [
                        'revised_prompt' => 'An image that represents the concept of a \'test\'.',
                        'url' => $url,
                    ],
                ],
            ]),
        ));
        // The image downloaded from the server successfully.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'image/jpeg'],
            \GuzzleHttp\Psr7\Utils::streamFor(fopen(self::get_fixture_path('aiprovider_openai', 'test.jpg'), 'r')),
        ));
        $this->provider = $this->create_provider(\core_ai\aiactions\generate_image::class);
        $this->create_action($user1->id);
        $processor = new process_generate_image($provider, $this->action);
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
        $this->provider = $this->create_provider(\core_ai\aiactions\generate_image::class);
        $this->create_action($user2->id);
        // The response from OpenAI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'created' => 1719140500,
                'data' => [
                    (object) [
                        'revised_prompt' => 'An image that represents the concept of a \'test\'.',
                        'url' => $url,
                    ],
                ],
            ]),
        ));
        // The image downloaded from the server successfully.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'image/jpeg'],
            \GuzzleHttp\Psr7\Utils::streamFor(fopen(self::get_fixture_path('aiprovider_openai', 'test.jpg'), 'r')),
        ));
        $processor = new process_generate_image($provider, $this->action);
        $result = $processor->process();
        $this->assertFalse($result->get_success());

        // Case 4: Time window has passed, global rate limit should be reset.
        $clock->bump(11);
        // Log in user1.
        $this->setUser($user1);
        // The response from OpenAI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'created' => 1719140500,
                'data' => [
                    (object) [
                        'revised_prompt' => 'An image that represents the concept of a \'test\'.',
                        'url' => $url,
                    ],
                ],
            ]),
        ));
        // The image downloaded from the server successfully.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'image/jpeg'],
            \GuzzleHttp\Psr7\Utils::streamFor(fopen(self::get_fixture_path('aiprovider_openai', 'test.jpg'), 'r')),
        ));
        $this->provider = $this->create_provider(\core_ai\aiactions\generate_image::class);
        $this->create_action($user1->id);
        $processor = new process_generate_image($provider, $this->action);
        $result = $processor->process();
        $this->assertTrue($result->get_success());
    }
}
