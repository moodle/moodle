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

namespace core\router\response;

use core\exception\access_denied_exception;
use core\router\schema\response\payload_response;
use core\router\schema\specification;
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Tests for the access denied response.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\response\exception_response
 * @covers     \core\router\response\access_denied_response
 */
final class access_denied_response_test extends route_testcase {
    public function test_basics(): void {
        $this->assertIsInt(access_denied_response::get_exception_status_code());
        $this->assertEquals(403, access_denied_response::get_exception_status_code());
    }

    public function test_get_response(): void {
        $exception = new access_denied_exception('the thing', 'theidentifier');
        $request = new ServerRequest('GET', '/example');

        $payload = access_denied_response::get_response($request, $exception);
        $this->assertInstanceOf(payload_response::class, $payload);

        $response = $payload->get_response($this->get_router()->get_response_factory());
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
        $content = (string) $response->getBody();

        $this->assertStringContainsString(
            'the thing',
            $content,
        );
        $this->assertStringContainsString(
            'theidentifier',
            $content,
        );
    }

    public function test_openapi_description(): void {
        $response = new access_denied_response();
        $openapi = $response->get_openapi_description(new specification());

        // The OpenAPI description should be present.
        // Note: We do not need to test the value of it. Doing so just reduces maintainability.
        $this->assertIsString($openapi->description);
    }
}
