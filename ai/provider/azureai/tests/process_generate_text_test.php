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

namespace aiprovider_azureai;

use core_ai\aiactions\base;
use core_ai\manager;
use GuzzleHttp\Psr7\Response;

/**
 * Test Generate text provider class for azureai provider methods.
 *
 * @package    aiprovider_azureai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \aiprovider_azureai\provider
 * @covers     \aiprovider_azureai\process_generate_text
 * @covers     \aiprovider_azureai\abstract_processor
 */
final class process_generate_text_test extends \advanced_testcase {
    /** @var string A successful response in JSON format. */
    protected string $responsebodyjson;

    /** @var manager $manager */
    private manager $manager;

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
        $this->responsebodyjson = file_get_contents(self::get_fixture_path('aiprovider_azureai', 'text_request_success.json'));
        $this->create_provider();
        $this->create_action();
    }

    /**
     * Create the provider object.
     */
    private function create_provider(): void {
        $this->manager = \core\di::get(\core_ai\manager::class);
        $config = [
            'apikey' => '123',
            'endpoint' => 'https://api.example.com',
            'enableuserratelimit' => true,
            'userratelimit' => 1,
            'enableglobalratelimit' => true,
            'globalratelimit' => 1,
        ];
        $this->provider = $this->manager->create_provider_instance(
            classname: '\aiprovider_azureai\provider',
            name: 'dummy',
            config: $config,
        );
    }

    /**
     * Create the action object.
     *
     * @param int $userid The user id to use in the action.
     */
    private function create_action(int $userid = 1): void {
        $this->action = new \core_ai\aiactions\generate_text(
            contextid: 1,
            userid: $userid,
            prompttext: 'This is a test prompt',
        );
    }

    /**
     * Test create_request_object
     */
    public function test_create_request_object(): void {
        $processor = new process_generate_text($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = (object) json_decode($request->getBody()->getContents());

        $this->assertEquals('This is a test prompt', $body->messages[1]->content);
        $this->assertEquals('user', $body->messages[1]->role);
    }

    /**
     * Test the API error response handler method.
     */
    public function test_handle_api_error(): void {
        $responses = [
            500 => new Response(500, ['Content-Type' => 'application/json']),
            503 => new Response(503, ['Content-Type' => 'application/json']),
            401 => new Response(
                401,
                ['Content-Type' => 'application/json'],
                json_encode(['error' => ['message' => 'Invalid Authentication']]),
            ),
            404 => new Response(
                404,
                ['Content-Type' => 'application/json'],
                json_encode(['error' => ['message' => 'You must be a member of an organization to use the API']]),
            ),
            429 => new Response(
                429,
                ['Content-Type' => 'application/json'],
                json_encode(['error' => ['message' => 'Rate limit reached for requests']]),
            ),
        ];

        $processor = new process_generate_text($this->provider, $this->action);
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
            $this->responsebodyjson
        );

        // We're testing a private method, so we need to setup reflector magic.
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_success');

        $result = $method->invoke($processor, $response);

        $this->assertTrue($result['success']);
        $this->assertEquals('chatcmpl-9ooaXlMSUIhOkd2pfxKBgpipMynkX', $result['id']);
        $this->assertEquals('fp_abc28019ad', $result['fingerprint']);
        $this->assertStringContainsString('Sure, I\'m here to help', $result['generatedcontent']);
        $this->assertEquals('stop', $result['finishreason']);
        $this->assertEquals('12', $result['prompttokens']);
        $this->assertEquals('14', $result['completiontokens']);
        $this->assertEquals('gpt-4o-2024-05-13', $result['model']);
    }

    /**
     * Test query_ai_api for a successful call.
     */
    public function test_query_ai_api_success(): void {
        // Mock the http client to return a successful response.
        ['mock' => $mock] = $this->get_mocked_http_client();

        // The response from Azure AI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));

        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'query_ai_api');
        $result = $method->invoke($processor);

        $this->assertTrue($result['success']);
        $this->assertEquals('chatcmpl-9ooaXlMSUIhOkd2pfxKBgpipMynkX', $result['id']);
        $this->assertEquals('fp_abc28019ad', $result['fingerprint']);
        $this->assertStringContainsString('Sure, I\'m here to help', $result['generatedcontent']);
        $this->assertEquals('stop', $result['finishreason']);
        $this->assertEquals('12', $result['prompttokens']);
        $this->assertEquals('14', $result['completiontokens']);
        $this->assertEquals('gpt-4o-2024-05-13', $result['model']);
    }

    /**
     * Test prepare_response success.
     */
    public function test_prepare_response_success(): void {
        $processor = new process_generate_text($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'prepare_response');

        $response = [
            'success' => true,
            'id' => 'chatcmpl-9lkwPWOIiQEvI3nfcGofJcmS5lPYo',
            'fingerprint' => 'fp_c4e5b6fa31',
            'generatedcontent' => 'Sure, here is some sample text',
            'finishreason' => 'stop',
            'prompttokens' => '11',
            'completiontokens' => '14',
            'model' => 'gpt-4o',
        ];

        $result = $method->invoke($processor, $response);

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertTrue($result->get_success());
        $this->assertEquals('generate_text', $result->get_actionname());
        $this->assertEquals($response['success'], $result->get_success());
        $this->assertEquals($response['generatedcontent'], $result->get_response_data()['generatedcontent']);
        $this->assertEquals($response['model'], $result->get_response_data()['model']);
    }

    /**
     * Test prepare_response error.
     */
    public function test_prepare_response_error(): void {
        $processor = new process_generate_text($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'prepare_response');

        $response = [
            'success' => false,
            'errorcode' => 500,
            'errormessage' => 'Internal server error.',
        ];

        $result = $method->invoke($processor, $response);

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertFalse($result->get_success());
        $this->assertEquals('generate_text', $result->get_actionname());
        $this->assertEquals($response['errorcode'], $result->get_errorcode());
        $this->assertEquals($response['errormessage'], $result->get_errormessage());
    }

    /**
     * Test process method.
     */
    public function test_process(): void {
        // Log in user.
        $this->setUser($this->getDataGenerator()->create_user());

        // Mock the http client to return a successful response.
        ['mock' => $mock] = $this->get_mocked_http_client();

        // The response from Azure AI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));

        $processor = new process_generate_text($this->provider, $this->action);
        $result = $processor->process();

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertTrue($result->get_success());
        $this->assertEquals('generate_text', $result->get_actionname());
    }

    /**
     * Test process method with error.
     */
    public function test_process_error(): void {
        // Log in user.
        $this->setUser($this->getDataGenerator()->create_user());

        // Mock the http client to return an usuccessful response.
        ['mock' => $mock] = $this->get_mocked_http_client();

        // The response from Azure AI.
        $mock->append(new Response(
            401,
            ['Content-Type' => 'application/json'],
            json_encode(['error' => ['message' => 'Invalid Authentication']]),
        ));

        $processor = new process_generate_text($this->provider, $this->action);
        $result = $processor->process();

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertFalse($result->get_success());
        $this->assertEquals('generate_text', $result->get_actionname());
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
            'endpoint' => 'https://api.example.com',
            'enableuserratelimit' => true,
            'userratelimit' => 1,
        ];
        $actionconfig = [
            'core_ai\\aiactions\\generate_text' => [
                'enabled' => true,
                'settings' => [
                    'deployment' => 'test',
                    'apiversion' => '2024-06-01',
                    'systeminstruction' => '',
                ],
            ],
        ];
        $provider = $this->manager->create_provider_instance(
            classname: provider::class,
            name: 'dummy',
            config: $config,
        );
        $provider = $this->manager->update_provider_instance(
            provider: $provider,
            actionconfig: $actionconfig,
        );

        // Mock the http client to return a successful response.
        ['mock' => $mock] = $this->get_mocked_http_client();

        // Case 1: User rate limit has not been reached.
        $this->create_action($user1->id);
        // The response from Azure AI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));
        $processor = new process_generate_text($provider, $this->action);
        $result = $processor->process();
        $this->assertTrue($result->get_success());

        // Case 2: User rate limit has been reached.
        $clock->bump(HOURSECS - 10);
        // The response from Azure AI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));
        $this->create_action($user1->id);
        $processor = new process_generate_text($provider, $this->action);
        $result = $processor->process();
        $this->assertEquals(429, $result->get_errorcode());
        $this->assertEquals('User rate limit exceeded', $result->get_errormessage());
        $this->assertFalse($result->get_success());

        // Case 3: User rate limit has not been reached for a different user.
        // Log in user2.
        $this->setUser($user2);
        $this->create_action($user2->id);
        // The response from Azure AI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));
        $processor = new process_generate_text($provider, $this->action);
        $result = $processor->process();
        $this->assertTrue($result->get_success());

        // Case 4: Time window has passed, user rate limit should be reset.
        $clock->bump(11);
        // Log in user1.
        $this->setUser($user1);
        // The response from Azure AI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));
        $this->create_action($user1->id);
        $processor = new process_generate_text($provider, $this->action);
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
            'endpoint' => 'https://api.example.com',
            'enableglobalratelimit' => true,
            'globalratelimit' => 1,
        ];
        $actionconfig = [
            'core_ai\\aiactions\\generate_text' => [
                'enabled' => true,
                'settings' => [
                    'deployment' => 'test',
                    'apiversion' => '2024-06-01',
                    'systeminstruction' => '',
                ],
            ],
        ];
        $provider = $this->manager->create_provider_instance(
            classname: provider::class,
            name: 'dummy',
            config: $config,
        );
        $provider = $this->manager->update_provider_instance(
            provider: $provider,
            actionconfig: $actionconfig,
        );

        // Mock the http client to return a successful response.
        ['mock' => $mock] = $this->get_mocked_http_client();

        // Case 1: Global rate limit has not been reached.
        $this->create_action($user1->id);
        // The response from Azure AI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));
        $processor = new process_generate_text($provider, $this->action);
        $result = $processor->process();
        $this->assertTrue($result->get_success());

        // Case 2: Global rate limit has been reached.
        $clock->bump(HOURSECS - 10);
        // The response from Azure AI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));
        $this->create_action($user1->id);
        $processor = new process_generate_text($provider, $this->action);
        $result = $processor->process();
        $this->assertEquals(429, $result->get_errorcode());
        $this->assertEquals('Global rate limit exceeded', $result->get_errormessage());
        $this->assertFalse($result->get_success());

        // Case 3: Global rate limit has been reached for a different user too.
        // Log in user2.
        $this->setUser($user2);
        $this->create_action($user2->id);
        // The response from Azure AI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));
        $processor = new process_generate_text($provider, $this->action);
        $result = $processor->process();
        $this->assertFalse($result->get_success());

        // Case 4: Time window has passed, global rate limit should be reset.
        $clock->bump(11);
        // Log in user1.
        $this->setUser($user1);
        // The response from Azure AI.
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));
        $this->create_action($user1->id);
        $processor = new process_generate_text($provider, $this->action);
        $result = $processor->process();
        $this->assertTrue($result->get_success());
    }
}
