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

use aiprovider_openai\process_summarise_text;
use core_ai\aiactions\base;
use core_ai\provider;
use GuzzleHttp\Psr7\Response;

/**
 * Test Generate text provider class for OpenAI provider methods.
 *
 * @package    aiprovider_openai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \aiprovider_openai\provider
 * @covers     \aiprovider_openai\process_summarise_text
 * @covers     \aiprovider_openai\abstract_processor
 */
final class process_summarise_text_test extends \advanced_testcase {
    /** @var string A successful response in JSON format. */
    protected string $responsebodyjson;

    /** @var provider The provider that will process the action. */
    protected provider $provider;

    /** @var base The action to process. */
    protected base $action;

    /**
     * Set up the test.
     */
    protected function setUp(): void {
        parent::setUp();
        // Load a response body from a file.
        $this->responsebodyjson = file_get_contents(self::get_fixture_path('aiprovider_openai', 'text_request_success.json'));
        $this->provider = new \aiprovider_openai\provider();
        $this->action = new \core_ai\aiactions\summarise_text(
            contextid: 1,
            userid: 1,
            prompttext: 'This is a test prompt',
        );
    }

    /**
     * Test create_request_object
     */
    public function test_create_request_object(): void {
        $processor = new process_summarise_text($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = (object) json_decode($request->getBody()->getContents());

        $this->assertEquals('This is a test prompt', $body->messages[1]->content);
        $this->assertEquals('user', $body->messages[1]->role);
    }

    /**
     * Test the API error response handler method.
     *
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

        $processor = new process_summarise_text($this->provider, $this->action);
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

        // We're testing a private method, so we need to set up reflector magic.
        $processor = new process_summarise_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_success');

        $result = $method->invoke($processor, $response);

        $this->assertTrue($result['success']);
        $this->assertEquals('chatcmpl-9lkwPWOIiQEvI3nfcGofJcmS5lPYo', $result['id']);
        $this->assertEquals('fp_c4e5b6fa31', $result['fingerprint']);
        $this->assertStringContainsString('Sure, here is some sample text', $result['generatedcontent']);
        $this->assertEquals('stop', $result['finishreason']);
        $this->assertEquals('11', $result['prompttokens']);
        $this->assertEquals('568', $result['completiontokens']);

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

        $processor = new process_summarise_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'query_ai_api');
        $result = $method->invoke($processor);

        $this->assertTrue($result['success']);
        $this->assertEquals('chatcmpl-9lkwPWOIiQEvI3nfcGofJcmS5lPYo', $result['id']);
        $this->assertEquals('fp_c4e5b6fa31', $result['fingerprint']);
        $this->assertStringContainsString('Sure, here is some sample text', $result['generatedcontent']);
        $this->assertEquals('stop', $result['finishreason']);
        $this->assertEquals('11', $result['prompttokens']);
        $this->assertEquals('568', $result['completiontokens']);
    }

    /**
     * Test prepare_response success.
     */
    public function test_prepare_response_success(): void {
        $processor = new process_summarise_text($this->provider, $this->action);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($processor, 'prepare_response');

        $response = [
            'success' => true,
            'id' => 'chatcmpl-9lkwPWOIiQEvI3nfcGofJcmS5lPYo',
            'fingerprint' => 'fp_c4e5b6fa31',
            'generatedcontent' => 'Sure, here is some sample text',
            'finishreason' => 'stop',
            'prompttokens' => '11',
            'completiontokens' => '568',
        ];

        $result = $method->invoke($processor, $response);

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertTrue($result->get_success());
        $this->assertEquals('summarise_text', $result->get_actionname());
        $this->assertEquals($response['success'], $result->get_success());
        $this->assertEquals($response['generatedcontent'], $result->get_response_data()['generatedcontent']);
    }

    /**
     * Test prepare_response error.
     */
    public function test_prepare_response_error(): void {
        $processor = new process_summarise_text($this->provider, $this->action);

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
        $this->assertEquals('summarise_text', $result->get_actionname());
        $this->assertEquals($response['errorcode'], $result->get_errorcode());
        $this->assertEquals($response['errormessage'], $result->get_errormessage());
    }
}
