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

use core_ai\aiactions\base;
use core_ai\provider;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use GuzzleHttp\Psr7\Utils;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/testcase_helper_trait.php');

/**
 * Test Generate image provider class for Gemini provider methods.
 *
 * @package    aiprovider_gemini
 * @copyright  2026 Anupama Sarjoshi <anupama.sarjoshi@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(\aiprovider_gemini\provider::class)]
#[CoversClass(\aiprovider_gemini\process_generate_image::class)]
#[CoversClass(\aiprovider_gemini\abstract_processor::class)]
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

    /** @var \stored_file Test stored file. */
    protected \stored_file $testfile;

    /**
     * Set up the test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        // Load a response body from a file.
        $this->responsebodyjson = file_get_contents(self::get_fixture_path('aiprovider_gemini', 'image_request_success.json'));
        $this->manager = \core\di::get(\core_ai\manager::class);
        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_image::class,
            actionconfig: [
                'model' => 'imagen-4.0-generate-001',
            ],
        );
        $this->create_action();
        $this->testfile = $this->create_test_file();
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
     * Create a mocked Gemini response.
     *
     * @param bool $success Whether to mock a successful response or not.
     */
    private function mock_gemini_response(bool $success = true): void {
        // Mock the http client to return a response.
        ['mock' => $mock] = $this->get_mocked_http_client();
        // Create a PSR-7 Stream for the response body.
        $stream = Utils::streamFor($this->responsebodyjson);

        $responsesuccess = new Response(
            200,
            ['Content-Type' => 'application/json'],
            $stream,
        );

        $responseserror = new Response(
            401,
            ['Content-Type' => 'application/json'],
            json_encode([
                'error' => [
                    'code' => 401,
                    'message' => 'Invalid Authentication',
                    'status' => 'UNAUTHENTICATED',
                ],
            ]),
        );

        if ($success) {
            $mock->append($responsesuccess);
            return;
        } else {
            $mock->append($responseserror);
            return;
        }
    }

    /**
     * Test calculate_aspect_ratio.
     */
    public function test_calculate_aspect_ratio(): void {
        $processor = new process_generate_image($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'calculate_aspect_ratio');

        $orientation = 'square';
        $ratio = $method->invoke($processor, $orientation);
        $this->assertEquals('1:1', $ratio);

        $orientation = 'portrait';
        $ratio = $method->invoke($processor, $orientation);
        $this->assertEquals('9:16', $ratio);

        $orientation = 'landscape';
        $ratio = $method->invoke($processor, $orientation);
        $this->assertEquals('16:9', $ratio);
    }

    /**
     * Test calculate_image_quality.
     */
    public function test_calculate_image_quality(): void {
        $processor = new process_generate_image($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'calculate_image_quality');
        $quality = 'standard';
        $size = $method->invoke($processor, $quality);
        $this->assertEquals('1k', $size);

        $quality = 'hd';
        $size = $method->invoke($processor, $quality);
        $this->assertEquals('2k', $size);
    }

    /**
     * Test create_request_object.
     */
    public function test_create_request_object(): void {
        $processor = new process_generate_image($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $requestdata = (object) json_decode($request->getBody()->getContents());

        $this->assertEquals('This is a test prompt', $requestdata->instances[0]->prompt);
        $this->assertEquals('1', $requestdata->parameters->sampleCount);
        $this->assertEquals('2k', $requestdata->parameters->imageSize);
        $this->assertEquals('1:1', $requestdata->parameters->aspectRatio);
    }

    /**
     * Test the API error response handler method.
     */
    public function test_handle_api_error(): void {
        $responses = $this->get_error_responses();
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
        // Create a PSR-7 Stream for the response body.
        $stream = Utils::streamFor($this->responsebodyjson);
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            $stream,
        );

        // Log in user.
        $this->setAdminUser();

        // We're testing a private method, so we need to setup reflector magic.
        $processor = new process_generate_image($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_success');

        $result = $method->invoke($processor, $response);

        $this->assertTrue($result['success']);
        $this->assertEquals('7cc6025f8a6ce71a.png', $result['draftfile']->get_filename());
    }

    /**
     * Test query_ai_api for a successful call.
     */
    public function test_query_ai_api_success(): void {
        // Mock successful response from Gemini.
        $this->mock_gemini_response();

        // Log in user.
        $this->setAdminUser();

        $processor = new process_generate_image($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'query_ai_api');
        $result = $method->invoke($processor);

        $this->assertTrue($result['success']);
        $this->assertEquals('7cc6025f8a6ce71a.png', $result['draftfile']->get_filename());
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
            'draftfile' => $this->testfile,
        ];

        $result = $method->invoke($processor, $response);

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertTrue($result->get_success());
        $this->assertEquals($response['success'], $result->get_success());
        $this->assertEquals('testfile.txt', $result->get_response_data()['draftfile']->get_filename());
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
     * Test process.
     */
    public function test_process(): void {
        // Log in user.
        $this->setUser($this->getDataGenerator()->create_user());

        // Mock successful response from Gemini.
        $this->mock_gemini_response();

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
        $this->assertEquals('7cc6025f8a6ce71a.png', $result->get_response_data()['draftfile']->get_filename());
    }

    /**
     * Test process method with error.
     */
    public function test_process_error(): void {
        // Log in user.
        $this->setUser($this->getDataGenerator()->create_user());

        // Mock error response from Gemini.
        $this->mock_gemini_response(false);

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
            classname: '\aiprovider_gemini\provider',
            name: 'dummy',
            config: $config,
            actionconfig: [
                \core_ai\aiactions\generate_image::class => [
                    'settings' => [
                        'model' => 'imagen-4.0-generate-001',
                        'endpoint' => "https://generativelanguage.googleapis.com/v1beta/models/imagen-4.0-generate-001:predict",
                    ],
                ],
            ],
        );

        // Case 1: User rate limit has not been reached.
        $this->create_action($user1->id);
        // Mock response from Gemini.
        $this->mock_gemini_response();

        $processor = new process_generate_image($provider, $this->action);
        $result = $processor->process();
        $this->assertTrue($result->get_success());

        // Case 2: User rate limit has been reached.
        $clock->bump(HOURSECS - 10);
        // Mock response from Gemini.
        $this->mock_gemini_response();

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
        // Mock response from Gemini.
        $this->mock_gemini_response();
        $processor = new process_generate_image($this->provider, $this->action);
        $result = $processor->process();
        $this->assertTrue($result->get_success());

        // Case 4: Time window has passed, user rate limit should be reset.
        $clock->bump(11);
        // Log in user1.
        $this->setUser($user1);
        // Mock response from Gemini.
        $this->mock_gemini_response();

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
            classname: '\aiprovider_gemini\provider',
            name: 'dummy',
            config: $config,
            actionconfig: [
                \core_ai\aiactions\generate_image::class => [
                    'settings' => [
                        'model' => 'imagen-4.0-generate-001',
                        'endpoint' => "https://generativelanguage.googleapis.com/v1beta/models/imagen-4.0-generate-001:predict",
                    ],
                ],
            ],
        );

        // Case 1: Global rate limit has not been reached.
        $this->create_action($user1->id);
        // Mock response from Gemini.
        $this->mock_gemini_response();

        $processor = new process_generate_image($provider, $this->action);
        $result = $processor->process();
        $this->assertTrue($result->get_success());

        // Case 2: Global rate limit has been reached.
        $clock->bump(HOURSECS - 10);
        // Mock response from Gemini.
        $this->mock_gemini_response();

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
        // Mock response from Gemini.
        $this->mock_gemini_response();

        $processor = new process_generate_image($provider, $this->action);
        $result = $processor->process();
        $this->assertFalse($result->get_success());

        // Case 4: Time window has passed, global rate limit should be reset.
        $clock->bump(11);
        // Log in user1.
        $this->setUser($user1);
        // Mock response from Gemini.
        $this->mock_gemini_response();

        $this->provider = $this->create_provider(\core_ai\aiactions\generate_image::class);
        $this->create_action($user1->id);
        $processor = new process_generate_image($provider, $this->action);
        $result = $processor->process();
        $this->assertTrue($result->get_success());
    }
}
