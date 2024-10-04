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
use core\router\response_validator;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Tests for the validation middleware.
 *
 * @package    core
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\router\middleware\validation_middleware
 */
final class validation_middleware_test extends \advanced_testcase {
    /**
     * If a request fails request validation, the next middleware will not be called.
     */
    public function test_process_fails_request_validation(): void {
        $request = new ServerRequest('GET', '/test');

        // Mock the request validator to throw an exception.
        $requestvalidator = $this->getMockBuilder(request_validator::class)->getMock();
        $requestvalidator->expects($this->once())
            ->method('validate_request')
            ->with($request)
            ->willThrowException(new \Exception('Invalid request'));

        // If the request fails validation, it will not be passed to next Middleware.
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handler->expects($this->never())->method('handle');

        // It will never get a response.
        $responsevalidator = $this->getMockBuilder(response_validator::class)->getMock();
        $responsevalidator->expects($this->never())->method('validate_response');

        di::set('core\router\request_validator', $requestvalidator);
        di::set('core\router\response_validator', $responsevalidator);

        // Execute the middleware.
        $middleware = di::get(validation_middleware::class);
        $returns = $middleware->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $returns);
    }

    /**
     * If a request passes request validation, but fails response validation.
     */
    public function test_process_passes_request_validation_fails_response_validation(): void {
        $request = new ServerRequest('GET', '/test');
        $response = new Response();

        // Mock the request validator to throw an exception.
        $requestvalidator = $this->getMockBuilder(request_validator::class)->getMock();
        $requestvalidator->expects($this->once())
            ->method('validate_request')
            ->with($request)
            ->willReturnArgument(0);

        // If the request fails validation, it will not be passed to next Middleware.
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handler
            ->method('handle')
            ->willReturn($response);

        // It will never get a response.
        $responsevalidator = $this->getMockBuilder(response_validator::class)->getMock();
        $responsevalidator
            ->expects($this->once())
            ->method('validate_response')
            ->with($request, $response)
            ->willThrowException(new \Exception('Invalid response'));

        di::set('core\router\request_validator', $requestvalidator);
        di::set('core\router\response_validator', $responsevalidator);

        // Execute the middleware.
        $middleware = di::get(validation_middleware::class);
        $returns = $middleware->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $returns);
        $this->assertNotEquals($response, $returns);
    }

    /**
     * If a request passes request validation, the response middleware will be called.
     */
    public function test_process_passes_request_validation(): void {
        $request = new ServerRequest('GET', '/test');
        $response = new Response();

        // Mock the request validator to throw an exception.
        $requestvalidator = $this->getMockBuilder(request_validator::class)->getMock();
        $requestvalidator->expects($this->once())
            ->method('validate_request')
            ->with($request)
            ->willReturnArgument(0);

        // If the request fails validation, it will not be passed to next Middleware.
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        // It will never get a response.
        $responsevalidator = $this->getMockBuilder(response_validator::class)->getMock();
        $responsevalidator
            ->expects($this->once())
            ->method('validate_response')
            ->with($request, $response);

        di::set('core\router\request_validator', $requestvalidator);
        di::set('core\router\response_validator', $responsevalidator);

        // Execute the middleware.
        $middleware = di::get(validation_middleware::class);
        $this->assertEquals($response, $middleware->process($request, $handler));
    }
}
