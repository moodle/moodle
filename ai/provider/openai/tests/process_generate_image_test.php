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

use core_ai\aiactions\base;
use core_ai\provider;
use GuzzleHttp\Psr7\Response;

/**
 * Test response_base OpenAI provider methods.
 *
 * @package    aiprovider_openai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_ai\provider\openai
 */
final class process_generate_image_test extends \advanced_testcase {
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
        $this->responsebodyjson = file_get_contents(__DIR__ . '/fixtures/image_request_success.json');
        $this->provider = new \aiprovider_openai\provider();
        $this->action = new \core_ai\aiactions\generate_image(
            contextid: 1,
            userid: 1,
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
        $request = $method->invoke($processor, $this->action, 1);

        $this->assertEquals('This is a test prompt', $request->prompt);
        $this->assertEquals('dall-e-3', $request->model);
        $this->assertEquals('1', $request->n);
        $this->assertEquals('hd', $request->quality);
        $this->assertEquals('url', $request->response_format);
        $this->assertEquals('1024x1024', $request->size);
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

        $processor = new process_generate_image($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_error');

        foreach ($responses as $status => $response) {
            $result = $method->invoke($processor, $status, $response);
            $this->assertEquals($status, $result['errorcode']);
            if ($status == 500) {
                $this->assertEquals('Internal server error.', $result['errormessage']);
            } else if ($status == 503) {
                $this->assertEquals('Service unavailable.', $result['errormessage']);
            } else {
                $this->assertStringContainsString($response->getBody()->getContents(), $result['errormessage']);
            }
        }
    }

    /**
     * Test the API success response handler method.
     *
     */
    public function test_handle_api_success(): void {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson
        );

        // We're testing a private method, so we need to setup reflector magic.
        $processor = new process_generate_image($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_success');

        $result = $method->invoke($processor, $response);

        $this->stringContains('An image that represents the concept of a \'test\'.', $result['revisedprompt']);
        $this->stringContains('oaidalleapiprodscus.blob.core.windows.net', $result['sourceurl']);
    }

    /**
     * Test query_ai_api for a successful call.
     */
    public function test_query_ai_api_success(): void {
        // Mock the http client to return a successful response.
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson
        );
        $client = $this->createMock(\core\http_client::class);
        $client->method('request')->willReturn($response);

        // Create a request object.
        $requestobj = new \stdClass();
        $requestobj->prompt = 'generate a test image';
        $requestobj->model = 'awesome-ai-3';
        $requestobj->n = '3';
        $requestobj->quality = 'hd';
        $requestobj->response_format = 'url;';
        $requestobj->size = '1024x1024';
        $requestobj->style = 'vivid';
        $requestobj->user = 't3464h89dftjltestudfaser';

        $processor = new process_generate_image($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'query_ai_api');
        $result = $method->invoke($processor, $client, $requestobj);

        $this->stringContains('An image that represents the concept of a \'test\'.', $result['revisedprompt']);
        $this->stringContains('oaidalleapiprodscus.blob.core.windows.net', $result['sourceurl']);
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
        ];

        $result = $method->invoke($processor, $response);

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertTrue($result->get_success());
        $this->assertEquals('generate_image', $result->get_actionname());
        $this->assertEquals($response['success'], $result->get_success());
        $this->assertEquals($response['revisedprompt'], $result->get_response()['revisedprompt']);
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
     * Test url_to_file.
     */
    public function test_url_to_file(): void {
        $this->resetAfterTest();
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
        $this->resetAfterTest();
        // Log in user.
        $this->setUser($this->getDataGenerator()->create_user());

        // Mock the http client to return a successful response.
        $url = $this->getExternalTestFileUrl('/test.jpg', false);

        $responsebodyjson = json_encode([
            'created' => 1719140500,
            'data' => [
                (object) [
                    'revised_prompt' => 'An image that represents the concept of a \'test\'.',
                    'url' => $url,
                ],
            ],
        ]);

        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            $responsebodyjson,
        );

        $mockhttpclient = $this->createMock(\core\http_client::class);
        $mockhttpclient->method('request')->willReturn($response);

        // Mock the provider to return the mocked http client.
        $mockprovider = $this->getMockBuilder(\aiprovider_openai\provider::class)
            ->onlyMethods(['create_http_client'])
            ->getMock();

        $mockprovider->method('create_http_client')->willReturn($mockhttpclient);

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
            style: $style
        );

        $processor = new process_generate_image($mockprovider, $this->action);
        $result = $processor->process();

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertTrue($result->get_success());
        $this->assertEquals('generate_image', $result->get_actionname());
        $this->assertEquals('An image that represents the concept of a \'test\'.', $result->get_response()['revisedprompt']);
        $this->assertEquals($url, $result->get_response()['sourceurl']);
    }
}
