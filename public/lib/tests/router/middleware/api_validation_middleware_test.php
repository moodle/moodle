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
use core\router\request_validator;
use core\router\response_handler;
use core\router\response_validator;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Tests for the API validation middleware.
 *
 * @package    core
 * @category   test
 * @copyright  2026 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(api_validation_middleware::class)]
final class api_validation_middleware_test extends \advanced_testcase {
    /**
     * If request validation fails, API middleware returns an error response.
     */
    public function test_process_fails_request_validation_returns_error_response(): void {
        $request = new ServerRequest('GET', '/test');
        $errorresponse = (new Response())->withStatus(400);

        // Mock the request validator to throw an exception.
        $requestvalidator = $this->getMockBuilder(request_validator::class)->getMock();
        $requestvalidator->expects($this->once())
            ->method('validate_request')
            ->with($request)
            ->willThrowException(new \Exception('Invalid request'));

        // If the request fails validation, it will not be passed to next Middleware.
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handler->expects($this->never())->method('handle');

        // It will return an error response.
        $responsehandler = $this->getMockBuilder(response_handler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $responsehandler->expects($this->once())
            ->method('get_response_from_exception')
            ->with($request, $this->isInstanceOf(\Exception::class))
            ->willReturn($errorresponse);

        di::set(request_validator::class, $requestvalidator);
        di::set(response_handler::class, $responsehandler);

        $middleware = di::get(api_validation_middleware::class);
        $this->assertSame($errorresponse, $middleware->process($request, $handler));
    }

    /**
     * If response validation fails, API middleware returns an error response.
     */
    public function test_process_fails_response_validation_returns_error_response(): void {
        $request = new ServerRequest('GET', '/test');
        $response = new Response();
        $errorresponse = (new Response())->withStatus(500);

        // Mock the request validator to pass validation.
        $requestvalidator = $this->getMockBuilder(request_validator::class)->getMock();
        $requestvalidator->expects($this->once())
            ->method('validate_request')
            ->with($request)
            ->willReturnArgument(0);

        // The request will be passed to next Middleware.
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        // Mock the response validator to throw an exception.
        $responsevalidator = $this->getMockBuilder(response_validator::class)->getMock();
        $responsevalidator->expects($this->once())
            ->method('validate_response')
            ->with($request, $response)
            ->willThrowException(new \Exception('Invalid response'));

        // It will return an error response.
        $responsehandler = $this->getMockBuilder(response_handler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $responsehandler->expects($this->once())
            ->method('get_response_from_exception')
            ->with($request, $this->isInstanceOf(\Exception::class))
            ->willReturn($errorresponse);

        di::set(request_validator::class, $requestvalidator);
        di::set(response_validator::class, $responsevalidator);
        di::set(response_handler::class, $responsehandler);

        // Execute the middleware.
        $middleware = di::get(api_validation_middleware::class);
        $this->assertSame($errorresponse, $middleware->process($request, $handler));
    }
}
