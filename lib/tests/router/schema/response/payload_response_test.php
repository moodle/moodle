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

namespace core\router\schema\response;

use core\router\response_handler;
use core\tests\route_testcase;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Tests for the payload response.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\schema\response\payload_response
 * @covers     \core\router\schema\response\abstract_response
 */
final class payload_response_test extends route_testcase {
    public function test_get_payload(): void {
        $request = new ServerRequest('GET', 'http://example.com/example/endpoint');

        $payload = [
            'example' => 'data',
            'goes' => 'here',
            'count' => 123,
        ];

        $response = new payload_response($payload, $request);
        $this->assertEquals($payload, $response->payload);
        $this->assertEquals($request, $response->get_request());
    }

    public function test_basics(): void {
        $request = new ServerRequest('GET', 'http://example.com/example/endpoint');
        $response = new Response();

        $payloaddata = [
            'example' => 'data',
            'goes' => 'here',
            'count' => 123,
        ];

        $payload = new payload_response($payloaddata, $request, $response);
        $this->assertSame($request, $payload->request);
        $this->assertSame($response, $payload->response);
    }

    public function test_response_standardisation(): void {
        $request = new ServerRequest('GET', 'http://example.com/example/endpoint');
        $response = new Response();

        $payloaddata = [
            'example' => 'data',
            'goes' => 'here',
            'count' => 123,
        ];

        $payload = new payload_response($payloaddata, $request, $response);

        $handler = new response_handler(\core\di::get_container());

        // Note: The standardisation itself is tested elsewhere.
        $response = $handler->standardise_response($payload);
        $this->assertInstanceOf(Response::class, $response);
    }
}
