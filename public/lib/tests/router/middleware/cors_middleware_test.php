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

namespace core\router\middleware;

use core\di;
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * Tests for the CORS middleware.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(cors_middleware::class)]
final class cors_middleware_test extends route_testcase {
    /**
     * Standard CORS headers are added.
     */
    public function test_cors_headers(): void {
        $app = $this->get_simple_app();
        $app->add(di::get(cors_middleware::class));
        $app->addRoutingMiddleware();

        $app->map(['GET'], '/test', function ($request, $response) {
            return $response;
        });

        // Handle the request.
        $request = new ServerRequest('GET', '/test', ['Accept' => 'application/json']);
        $returns = $app->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $returns);

        // Assert the relevant CORS headers.
        $this->assertEquals('*', $returns->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('GET', $returns->getHeaderLine('Access-Control-Allow-Methods'));
        $this->assertEquals('application/json', $returns->getHeaderLine('Content-Type'));
        $this->assertEquals('inline', $returns->getHeaderLine('Content-Disposition'));

        // Check the allowed headers.
        $allowedheaders = $returns->getHeaderLine('Access-Control-Allow-Headers');
        $this->assertStringContainsString('Content-Type', $allowedheaders);
        $this->assertStringContainsString('api_key', $allowedheaders);
        $this->assertStringContainsString('Authorization', $allowedheaders);
    }

    /**
     * CORS headers are not added for a request which does not accept JSON and is not an OPTIONS
     * or HEAD request.
     */
    public function test_cors_headers_not_added_for_non_json_request(): void {
        $app = $this->get_simple_app();
        $app->add(di::get(cors_middleware::class));
        $app->addRoutingMiddleware();

        $app->map(['GET'], '/test', function ($request, $response) {
            return $response;
        });

        // Handle the request. No Accept header is provided.
        $request = new ServerRequest('GET', '/test');
        $returns = $app->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $returns);

        // No CORS headers should have been added, and the response should be returned unmodified.
        $this->assertEquals('', $returns->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('', $returns->getHeaderLine('Access-Control-Allow-Methods'));
        $this->assertEquals('', $returns->getHeaderLine('Access-Control-Allow-Headers'));
        $this->assertEquals('', $returns->getHeaderLine('Content-Type'));
        $this->assertEquals('', $returns->getHeaderLine('Content-Disposition'));
    }

    /**
     * CORS headers are still added for an OPTIONS request, even when the Accept header does not
     * request JSON.
     */
    public function test_cors_headers_added_for_options_request(): void {
        $app = $this->get_simple_app();
        $app->add(di::get(cors_middleware::class));
        $app->addRoutingMiddleware();

        $app->map(['OPTIONS'], '/test', function ($request, $response) {
            return $response;
        });

        // Handle the request. No Accept header is provided.
        $request = new ServerRequest('OPTIONS', '/test');
        $returns = $app->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $returns);

        $this->assertEquals('*', $returns->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('OPTIONS', $returns->getHeaderLine('Access-Control-Allow-Methods'));
        $this->assertEquals('application/json', $returns->getHeaderLine('Content-Type'));
        $this->assertEquals('inline', $returns->getHeaderLine('Content-Disposition'));
    }

    /**
     * CORS headers are still added for a HEAD request, even when the Accept header does not
     * request JSON.
     */
    public function test_cors_headers_added_for_head_request(): void {
        $app = $this->get_simple_app();
        $app->add(di::get(cors_middleware::class));
        $app->addRoutingMiddleware();

        $app->map(['HEAD'], '/test', function ($request, $response) {
            return $response;
        });

        // Handle the request. No Accept header is provided.
        $request = new ServerRequest('HEAD', '/test');
        $returns = $app->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $returns);

        $this->assertEquals('*', $returns->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('HEAD', $returns->getHeaderLine('Access-Control-Allow-Methods'));
        $this->assertEquals('application/json', $returns->getHeaderLine('Content-Type'));
        $this->assertEquals('inline', $returns->getHeaderLine('Content-Disposition'));
    }

    /**
     * CORS headers are added when 'application/json' is present alongside other mime types in the
     * Accept header, including when separated by ", " (with a space) as is typical of real-world
     * Accept headers, and when qualified with a quality value (e.g. ';q=0.9').
     *
     * @param string $acceptheader
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('accept_header_provider')]
    public function test_cors_headers_added_for_compound_accept_header(string $acceptheader): void {
        $app = $this->get_simple_app();
        $app->add(di::get(cors_middleware::class));
        $app->addRoutingMiddleware();

        $app->map(['GET'], '/test', function ($request, $response) {
            return $response;
        });

        $request = new ServerRequest('GET', '/test', ['Accept' => $acceptheader]);
        $returns = $app->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $returns);

        $this->assertEquals('*', $returns->getHeaderLine('Access-Control-Allow-Origin'));
    }

    /**
     * Data provider for test_cors_headers_added_for_compound_accept_header.
     *
     * @return array
     */
    public static function accept_header_provider(): array {
        return [
            'Exact match' => ['application/json'],
            'Leading whitespace after comma' => ['text/html, application/json'],
            'Trailing whitespace before comma' => ['application/json ,text/html'],
            'Quality value suffix' => ['application/json;q=0.9'],
            'Multiple types with whitespace and quality value' => [
                'text/html;q=0.8, application/json;q=0.9, */*;q=0.1',
            ],
        ];
    }

    /**
     * CORS methods are added for multiple routes matching the same path.
     */
    public function test_cors_multiple_methods_headers(): void {
        $app = $this->get_simple_app();
        $app->add(di::get(cors_middleware::class));
        $app->addRoutingMiddleware();

        $app->map(['GET'], '/test', fn ($request, $response) => $response);
        $app->map(['POST'], '/test', fn ($request, $response) => $response);
        $app->map(['PUT', 'PATCH'], '/test', fn ($request, $response) => $response);
        $app->map(['DELETE'], '/test', fn ($request, $response) => $response);

        // Handle the request.
        $request = new ServerRequest('GET', '/test', ['Accept' => 'application/json']);
        $returns = $app->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $returns);

        // Assert the relevant CORS headers.
        $this->assertEquals('*', $returns->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('GET,POST,PUT,PATCH,DELETE', $returns->getHeaderLine('Access-Control-Allow-Methods'));
    }
}
