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
use core\router\response_handler;
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
 * @covers \core\router\middleware\error_handling_middleware
 */
final class error_handling_middleware_test extends route_testcase {
    /**
     * When no errors, the error handle is not called.
     */
    public function test_no_errors(): void {
        $responsehandler = $this->getMockBuilder(response_handler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $responsehandler->expects($this->never())->method('get_response_from_exception');

        di::set(response_handler::class, $responsehandler);

        $app = $this->get_simple_app();
        $app->add(di::get(error_handling_middleware::class));

        $app->map(['GET'], '/test', fn ($request, $response) => $response);

        // Handle the request.
        $request = new ServerRequest('GET', '/test');
        $returns = $app->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $returns);
    }

    /**
     * When no errors, the error handle is not called.
     */
    public function test_error_handling(): void {
        $responsehandler = $this->getMockBuilder(response_handler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $responsehandler->expects($this->once())->method('get_response_from_exception');

        di::set(response_handler::class, $responsehandler);

        // Configure the app with one middleware that throws an exception.
        $app = $this->get_simple_app();
        $app->add(fn ($request, $handler) => throw new \Exception('Test'));
        $app->add(di::get(error_handling_middleware::class));

        $app->map(['GET'], '/test', fn ($request, $response) => $response);

        // Handle the request.
        $request = new ServerRequest('GET', '/test');
        $returns = $app->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $returns);
    }
}
