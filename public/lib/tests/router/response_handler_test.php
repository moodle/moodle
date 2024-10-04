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

namespace core\router;

use core\di;
use core\exception\access_denied_exception;
use core\exception\response_aware_exception;
use core\router\schema\response\payload_response;
use core\router\schema\response\view_response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Tests for \core\router\response_handler.
 *
 * @package    core
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\router\response_handler
 */
final class response_handler_test extends \advanced_testcase {
    public function test_standardise_response_from_response(): void {
        $response = new Response();

        $handler = di::get(response_handler::class);

        $result = $handler->standardise_response($response);
        $this->assertEquals($response, $result);
    }

    public function test_standardise_response_from_payload_response(): void {
        $request = new ServerRequest('GET', 'http://example.com');
        $payload = new payload_response(['key' => 'value'], $request);

        $handler = di::get(response_handler::class);

        $result = $handler->standardise_response($payload);
        $this->assertInstanceOf(Response::class, $result);

        // The body should be json and contain the same data.
        $value = json_decode($result->getBody());
        $this->assertSame(
            ['key' => 'value'],
            (array) $value,
        );

        // The content type should be application/json.
        $this->assertStringContainsString('application/json', $result->getHeaderLine('Content-Type'));

        // The status code should be 200.
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function test_standardise_response_from_payload_response_and_response(): void {
        $request = new ServerRequest('GET', 'http://example.com');
        $response = new Response();

        // Add some test headers.
        $response = $response->withAddedHeader('Content-Type', 'text/plain')
            ->withAddedHeader('X-Example', 'example-value');

        $payload = new payload_response(['key' => 'value'], $request, $response);

        $handler = di::get(response_handler::class);

        $result = $handler->standardise_response($payload);
        $this->assertInstanceOf(Response::class, $result);

        // The body should be json and contain the same data.
        $value = json_decode($result->getBody());
        $this->assertSame(
            ['key' => 'value'],
            (array) $value,
        );

        // The content type should be application/json and the text/plain header should have been replaced.
        $this->assertStringContainsString('application/json', $result->getHeaderLine('Content-Type'));

        // The status code should be 200.
        $this->assertEquals(200, $result->getStatusCode());

        // The X-Example header should be present.
        $this->assertEquals('example-value', $result->getHeaderLine('X-Example'));
    }

    public function test_standardise_response_from_view_response(): void {
        global $OUTPUT;

        $request = new ServerRequest('GET', 'http://example.com');

        // Add some test headers.
        $initialresponse = new Response();
        $initialresponse = $initialresponse->withAddedHeader('Content-Type', 'application/json')
            ->withAddedHeader('X-Example', 'example-value');

        $response = new view_response(
            template: 'core/welcome',
            parameters: [
                'welcomemessage' => 'Hello, everybody!',
            ],
            request: $request,
            response: $initialresponse,
        );

        $handler = di::get(response_handler::class);

        $result = $handler->standardise_response($response);
        $this->assertInstanceOf(Response::class, $result);

        // The content type should be application/json and the text/plain header should have been replaced.
        $this->assertStringContainsString('text/html', $result->getHeaderLine('Content-Type'));

        // The status code should be 200.
        $this->assertEquals(200, $result->getStatusCode());

        // The X-Example header should be present.
        $this->assertEquals('example-value', $result->getHeaderLine('X-Example'));

        $body = (string) $result->getBody();
        $this->assertStringContainsString('Hello, everybody!', $body);
        $this->assertEquals(
            $OUTPUT->render_from_template('core/welcome', ['welcomemessage' => 'Hello, everybody!']),
            $body,
        );
    }

    /**
     * Test that the response handler can get a response from an exception.
     */
    public function test_get_response_from_exception(): void {
        $request = new ServerRequest('GET', 'http://example.com');
        $exception = new \Exception('Test exception');

        $handler = di::get(response_handler::class);

        $result = $handler->get_response_from_exception($request, $exception);
        $this->assertInstanceOf(Response::class, $result);

        // The body should be json and contain the exception message.
        $value = json_decode($result->getBody(), true);
        $this->assertArrayHasKey('message', (array) $value);
        $this->assertArrayHasKey('stacktrace', (array) $value);

        $this->assertEquals(
            'Test exception',
            $value['message'],
        );

        // The content type should be application/json.
        $this->assertStringContainsString('application/json', $result->getHeaderLine('Content-Type'));

        // The status code should be 500.
        $this->assertEquals(500, $result->getStatusCode());
    }

    /**
     * Test that the response handler can get a response from an exception.
     */
    public function test_get_response_from_response_aware_exception(): void {
        $request = new ServerRequest('GET', 'http://example.com');

        $exception = new access_denied_exception('Test exception');
        $handler = di::get(response_handler::class);

        $result = $handler->get_response_from_exception($request, $exception);
        $this->assertInstanceOf(Response::class, $result);

        // The body should be json and contain the exception message.
        $value = json_decode($result->getBody(), true);
        $this->assertArrayHasKey('message', (array) $value);
        $this->assertArrayHasKey('stacktrace', (array) $value);

        // The content type should be application/json.
        $this->assertStringContainsString('application/json', $result->getHeaderLine('Content-Type'));

        // The status code should be 500.
        $this->assertEquals(403, $result->getStatusCode());
    }
}
