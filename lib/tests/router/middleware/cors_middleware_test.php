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
use core\tests\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * Tests for the CORS middleware.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\router\middleware\cors_middleware
 */
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
        $request = new ServerRequest('GET', '/test');
        $returns = $app->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $returns);

        // Assert the relevant CORS headers.
        $this->assertEquals('*', $returns->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('GET', $returns->getHeaderLine('Access-Control-Allow-Methods'));

        // Check the allowed headers.
        $allowedheaders = $returns->getHeaderLine('Access-Control-Allow-Headers');
        $this->assertStringContainsString('Content-Type', $allowedheaders);
        $this->assertStringContainsString('api_key', $allowedheaders);
        $this->assertStringContainsString('Authorization', $allowedheaders);
    }

    /**
     * CORS methods are added for multiple routes matching the same path.
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
        $request = new ServerRequest('GET', '/test');
        $returns = $app->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $returns);

        // Assert the relevant CORS headers.
        $this->assertEquals('*', $returns->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('GET,POST,PUT,PATCH,DELETE', $returns->getHeaderLine('Access-Control-Allow-Methods'));
    }
}
