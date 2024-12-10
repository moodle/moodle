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

use core\attribute\deprecated;
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Tests for the deprecated API route middleware.
 *
 * @package    core
 * @category   test
 * @copyright  2026 Moodle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(deprecated_api_route_middleware::class)]
final class deprecated_api_route_middleware_test extends route_testcase {
    /**
     * A request without a routed method is passed to the next handler.
     */
    public function test_process_without_route_passes_request_to_handler(): void {
        $request = new ServerRequest('GET', '/test');
        $response = new Response();
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $middleware = \core\di::get(deprecated_api_route_middleware::class);

        $this->assertSame($response, $middleware->process($request, $handler));
    }

    /**
     * A routed method without a deprecation is passed to the next handler.
     */
    public function test_process_with_non_deprecated_route_passes_request_to_handler(): void {
        $request = $this->get_routed_request('not_deprecated_route');
        $response = new Response();
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $middleware = \core\di::get(deprecated_api_route_middleware::class);

        $this->assertSame($response, $middleware->process($request, $handler));
    }

    /**
     * A non-final deprecation is logged and the request is passed to the next handler.
     */
    public function test_process_with_non_final_deprecated_route_logs_and_passes_request(): void {
        $uri = 'https://example.com/test';
        $request = $this->get_routed_request('deprecated_route', $uri);
        $response = new Response();
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);
        $logfile = make_request_directory() . '/deprecated-api-route.log';
        $previouserrorlog = ini_get('error_log');
        ini_set('error_log', $logfile);

        try {
            $middleware = \core\di::get(deprecated_api_route_middleware::class);

            $this->assertSame($response, $middleware->process($request, $handler));
            $log = file_get_contents($logfile);
            $this->assertStringContainsString('has been deprecated', $log);
            $this->assertStringContainsString($uri, $log);
        } finally {
            ini_set('error_log', $previouserrorlog);
        }
    }

    /**
     * A final deprecation throws and does not pass the request to the next handler.
     */
    public function test_process_with_final_deprecated_route_throws(): void {
        $request = $this->get_routed_request('final_deprecated_route');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $middleware = \core\di::get(deprecated_api_route_middleware::class);

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('has been deprecated');
        $middleware->process($request, $handler);
    }

    /**
     * A deprecation with emission disabled is ignored, even when it is final.
     */
    public function test_process_with_non_emitting_final_deprecation_passes_request(): void {
        $request = $this->get_routed_request('non_emitting_final_deprecated_route');
        $response = new Response();
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $middleware = \core\di::get(deprecated_api_route_middleware::class);

        $this->assertSame($response, $middleware->process($request, $handler));
    }

    /**
     * Create a routed request for one of this test's handler methods.
     *
     * @param string $method The route method
     * @param string $uri The request URI
     * @return ServerRequestInterface
     */
    private function get_routed_request(
        string $method,
        string $uri = '/test',
    ): ServerRequestInterface {
        $app = $this->get_simple_app();
        $app->get('/test', [$this, $method]);

        return $this->route_request($app, new ServerRequest('GET', $uri));
    }

    /**
     * A route method without deprecation metadata.
     */
    public function not_deprecated_route(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        return $response;
    }

    /**
     * A route method with a non-final deprecation.
     */
    #[deprecated(replacement: 'the replacement route')]
    public function deprecated_route(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        return $response;
    }

    /**
     * A route method with a final deprecation.
     */
    #[deprecated(replacement: 'the replacement route', final: true)]
    public function final_deprecated_route(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        return $response;
    }

    /**
     * A route method with a final deprecation that should not be emitted.
     */
    #[deprecated(replacement: 'the replacement route', final: true, emit: false)]
    public function non_emitting_final_deprecated_route(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        return $response;
    }
}
