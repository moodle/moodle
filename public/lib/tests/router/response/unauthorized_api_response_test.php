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

use core\exception\expired_api_token_exception;
use core\exception\invalid_api_token_exception;
use core\exception\revoked_api_token_exception;
use core\router\response\exception_response;
use core\router\response\unauthorized_api_response;
use core\router\schema\response\payload_response;
use core\router\schema\specification;
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Tests for the unauthorized API response.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(unauthorized_api_response::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(exception_response::class)]
final class unauthorized_api_response_test extends route_testcase {
    public function test_basics(): void {
        $this->assertIsInt(unauthorized_api_response::get_exception_status_code());
        $this->assertEquals(401, unauthorized_api_response::get_exception_status_code());
    }

    /**
     * The response payload must comply with the RFC-9457 problem details format.
     */
    public function test_get_response_rfc_9457_payload(): void {
        $exception = new expired_api_token_exception();
        $request = new ServerRequest('GET', '/api/example');

        $payload = unauthorized_api_response::get_response($request, $exception);
        $this->assertInstanceOf(payload_response::class, $payload);

        $response = $payload->get_response($this->get_router()->get_response_factory());
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
        $this->assertEquals(401, $response->getStatusCode());

        $content = json_decode((string) $response->getBody(), true);
        $this->assertEquals('about:blank', $content['type']);
        $this->assertEquals('Unauthorized', $content['title']);
        $this->assertEquals(401, $content['status']);
        $this->assertEquals($exception->getMessage(), $content['detail']);
        $this->assertEquals('/api/example', $content['instance']);
    }

    /**
     * All API token exceptions should map to this response class, and be treated equally.
     */
    public function test_api_token_exceptions_map_to_this_response(): void {
        $this->assertEquals(
            unauthorized_api_response::class,
            (new expired_api_token_exception())->get_response_classname(),
        );
        $this->assertEquals(
            unauthorized_api_response::class,
            (new invalid_api_token_exception())->get_response_classname(),
        );
        $this->assertEquals(
            unauthorized_api_response::class,
            (new revoked_api_token_exception())->get_response_classname(),
        );
    }

    public function test_openapi_description(): void {
        $response = new unauthorized_api_response();
        $openapi = $response->get_openapi_description(new specification());

        // The OpenAPI description should be present.
        // Note: We do not need to test the value of it. Doing so just reduces maintainability.
        $this->assertIsString($openapi->description);
    }
}
