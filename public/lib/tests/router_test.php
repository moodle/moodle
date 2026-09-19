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

namespace core;

use core\router\util;
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

/**
 * Tests for the router class.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\core\router::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\core\router\response_handler::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\core\router\util::class)]
final class router_test extends route_testcase {
    public function test_get_app(): void {
        $router = $this->get_router('/example');
        $app = $router->get_app();
        $this->assertInstanceOf(App::class, $app);

        $this->assertEquals(di::get_container(), $app->getContainer());
    }

    public function test_request_normalisation(): void {
        $this->set_basepath('');
        $router = $this->get_router('');

        // Create handlers for the routes.
        // Note: These must all be created before any are accessed as the data is cached after first use.
        $app = $router->get_app();
        $app->get('/test/path', fn ($response) => $response->withStatus(299));
        $app->get('/', fn ($response) => $response->withStatus(275));
        $app->get('/test/otherpath', fn ($response) => $response->withStatus(250));

        // Duplicate slashes.
        $request = $this->create_request('GET', '/test//path', '');
        $response = $router->handle_request($request);
        $this->assertEquals(299, $response->getStatusCode());

        // An empty route.
        $request = $this->create_request('GET', '', '');
        $response = $router->handle_request($request);
        $this->assertEquals(275, $response->getStatusCode());

        // A route with a trailing slash.
        $request = $this->create_request('GET', '/test/otherpath/', '');
        $response = $router->handle_request($request);
        $this->assertEquals(250, $response->getStatusCode());

        // A route with a trailing double slash.
        $request = $this->create_request('GET', '/test/otherpath/////', '');
        $response = $router->handle_request($request);
        $this->assertEquals(250, $response->getStatusCode());
    }

    /**
     * Unmatched API requests return problem details regardless of the router base path or Accept header.
     *
     * @param string $basepath The router base path.
     * @param string $path The requested API path.
     * @param array $headers The request headers.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('api_not_found_provider')]
    public function test_api_not_found(string $basepath, string $path, array $headers): void {
        $this->set_basepath($basepath);
        $response = $this->get_app()->handle($this->create_request(
            method: 'GET',
            path: $path,
            prefix: '',
            headers: $headers,
        ));

        $this->assertSame('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assert_not_found_response($response);
        $payload = $this->decode_response($response);
        $this->assertSame('about:blank', $payload->type);
        $this->assertSame($basepath . $path, $payload->instance);
    }

    /**
     * Requests which must return API problem details.
     *
     * @return \Generator
     */
    public static function api_not_found_provider(): \Generator {
        foreach (['', '/r.php', '/moodle', '/moodle/r.php'] as $basepath) {
            yield "{$basepath} without Accept" => [$basepath, '/api/rest/v2/missing', []];
            yield "{$basepath} accepting HTML" => [$basepath, '/api/rest/v2/missing', ['Accept' => 'text/html']];
        }
        yield 'API root' => ['', '/api/rest/v2', []];
    }

    /**
     * Unsupported API methods return problem details and the allowed methods.
     *
     * @param string $basepath The router base path.
     * @param array $headers The request headers.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('api_method_not_allowed_provider')]
    public function test_api_method_not_allowed(string $basepath, array $headers): void {
        $this->set_basepath($basepath);
        $renderer = $this->createMock(router\error_renderer::class);
        $renderer->expects($this->never())->method('__invoke');
        di::set(router\error_renderer::class, $renderer);
        $app = $this->get_app();
        $app->map(['GET', 'PUT'], '/api/rest/v2/test', fn () => $this->fail('The route must not be called'));
        $response = $app->handle($this->create_request(
            method: 'POST',
            path: '/test',
            headers: $headers,
        ));

        $this->assertSame('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assert_exception_response($response, 405);
        $this->assertSame('GET, PUT', $response->getHeaderLine('Allow'));
        $payload = $this->decode_response($response);
        $this->assertSame('Method Not Allowed', $payload->title);
        $this->assertSame('about:blank', $payload->type);
        $this->assertSame($basepath . '/api/rest/v2/test', $payload->instance);
    }

    /**
     * Base paths and Accept headers for unsupported API methods.
     *
     * @return \Generator
     */
    public static function api_method_not_allowed_provider(): \Generator {
        foreach (['', '/r.php', '/moodle', '/moodle/r.php'] as $basepath) {
            yield "{$basepath} without Accept" => [$basepath, []];
            yield "{$basepath} accepting HTML" => [$basepath, ['Accept' => 'text/html']];
        }
    }

    /**
     * Unsupported non-API methods retain HTML responses and the allowed methods.
     */
    public function test_non_api_method_not_allowed(): void {
        $this->set_basepath('');
        $renderer = $this->createMock(router\error_renderer::class);
        $renderer->expects($this->once())
            ->method('__invoke')
            ->willReturn('<html>Method not allowed</html>');
        di::set(router\error_renderer::class, $renderer);
        $app = $this->get_app();
        $app->map(['GET', 'PUT'], '/test', fn () => $this->fail('The route must not be called'));
        $response = $app->handle(new ServerRequest('POST', '/test', ['Accept' => 'text/html']));

        $this->assertSame(405, $response->getStatusCode());
        $this->assertSame('GET, PUT', $response->getHeaderLine('Allow'));
        $this->assertStringStartsWith('text/html', $response->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('<html', (string) $response->getBody());
    }

    /**
     * The outer API error handler preserves the status of supported exceptions.
     *
     * @param \Exception $exception The exception to throw.
     * @param int $expectedstatus The expected HTTP status code.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('api_exception_provider')]
    public function test_api_exception_response(\Exception $exception, int $expectedstatus): void {
        $this->set_basepath('');
        $app = $this->get_app();
        $app->get('/api/rest/v2/test', fn () => throw $exception);
        $response = $app->handle(new ServerRequest('GET', '/api/rest/v2/test'));

        $this->assertSame('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assert_exception_response($response, $expectedstatus);
    }

    /**
     * Exceptions handled outside the API group middleware.
     *
     * @return \Generator
     */
    public static function api_exception_provider(): \Generator {
        yield 'Forbidden' => [new \Slim\Exception\HttpForbiddenException(new ServerRequest('GET', '/api/rest/v2/test')), 403];
        yield 'Response-aware' => [new \core\exception\invalid_parameter_exception('Invalid API parameter'), 400];
    }

    /**
     * Unmatched non-API requests retain their HTML error responses.
     *
     * @param string $path The requested non-API path.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('non_api_not_found_provider')]
    public function test_non_api_not_found(string $path): void {
        $this->set_basepath('');
        $renderer = $this->createMock(router\error_renderer::class);
        $renderer->expects($this->once())
            ->method('__invoke')
            ->willReturn('<html>Not found</html>');
        di::set(router\error_renderer::class, $renderer);
        $response = $this->get_app()->handle(new ServerRequest('GET', $path, ['Accept' => 'text/html']));

        $this->assertSame(404, $response->getStatusCode());
        $this->assertStringStartsWith('text/html', $response->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('<html', (string) $response->getBody());
    }

    /**
     * Requests outside the API path boundary.
     *
     * @return \Generator
     */
    public static function non_api_not_found_provider(): \Generator {
        yield 'Page' => ['/missing'];
        yield 'Different API version' => ['/api/rest/v20/missing'];
        yield 'Similar prefix' => ['/api/rest/v2other/missing'];
    }

    /**
     * Test an API route.
     */
    public function test_preferences_no_login(): void {
        $this->add_class_routes_to_route_loader(\core_user\route\api\preferences::class);
        $response = $this->process_api_request('GET', '/current/preferences');

        $this->assert_valid_response($response);
        $payload = $this->decode_response($response);

        $this->assertEmpty((array) $payload);
    }

    /**
     * Test that a routed URL can be set on the page without triggering debugging output.
     */
    public function test_routed_url_can_be_set_on_page(): void {
        global $PAGE;

        $this->resetAfterTest();
        $this->add_class_routes_to_route_loader(\core_user\route\api\preferences::class);

        // We need to get the app so we initialise the basepath to an empty value.
        $this->get_app();
        $url = util::get_path_for_callable(
            [\core_user\route\api\preferences::class, 'get_preferences'],
            ['user' => 'current'],
        );
        $expectedurl = new url($url);
        $PAGE->set_url($url);

        $this->assertDebuggingNotCalled();
        $this->assertSame($expectedurl->out(false), $PAGE->url->out(false));
    }

    public function test_basepath_supplied(): void {
        $router = $this->get_router(
            basepath: '/example',
        );
        $this->assertEquals('/example', $router->basepath);
    }


    #[\PHPUnit\Framework\Attributes\DataProvider('basepath_provider')]
    public function test_basepath(
        string $wwwroot,
        bool $configured,
        string $expected,
    ): void {
        global $CFG;

        $this->resetAfterTest();
        $CFG->wwwroot = $wwwroot;
        $CFG->routerconfigured = $configured;

        $router = di::get(router::class);

        $this->assertEquals($expected, $router->basepath);
    }

    /**
     * Data provider for test_basepath.
     *
     * @return \Generator
     */
    public static function basepath_provider(): \Generator {
        yield 'Domain Router not configured' => ['http://example.com', false, '/r.php'];
        yield 'Domain Router configured' => ['http://example.com', true, ''];
        yield 'Subdirectory Router not configured' => ['http://example.com/moodle', false, '/moodle/r.php'];
        yield 'Subdirectory Router configured' => ['http://example.com/moodle', true, '/moodle'];
    }

    /**
     * Test that the basepath is correctly guessed when accessed via r.php.
     */
    public function test_basepath_guessed_rphp(): void {
        $wwwroot = new \moodle_url('/r.php');
        $_SERVER['SCRIPT_FILENAME'] = 'r.php';
        $_SERVER['REQUEST_URI'] = $wwwroot->get_path();

        $router = di::get(router::class);

        $this->assertEquals($wwwroot->get_path(), $router->basepath);
    }

    /**
     * Test that the basepath is correctly guessed when the router is configured.
     *
     * @param string $wwwroot The wwwroot to use.
     * @param bool|null $configured The value of $CFG->routerconfigured.
     * @param string $requestedpath The path that was requested.
     * @param string $expected The expected basepath.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('router_configured_basepath_provider')]
    public function test_basepath_guessed_rphp_configuration_provided(
        string $wwwroot,
        ?bool $configured,
        string $requestedpath,
        string $expected,
    ): void {
        global $CFG;

        $this->resetAfterTest();
        $CFG->wwwroot = $wwwroot;
        $CFG->routerconfigured = $configured;
        $_SERVER['SCRIPT_FILENAME'] = "{$CFG->dirroot}/r.php";
        $_SERVER['REQUEST_URI'] = $requestedpath;

        $router = di::get(router::class);

        $this->assertEquals($expected, $router->basepath);
    }

    /**
     * Data provider for test_basepath_guessed_rphp_configuration_provided.
     *
     * @return \Generator
     */
    public static function router_configured_basepath_provider(): \Generator {
        yield 'Root domain, Not configured, accessed via r.php' => [
            'http://example.com',
            null,
            '/r.php/example',
            '/r.php',
        ];
        yield 'Root domain, Configured true, accessed via r.php' => [
            'http://example.com',
            true,
            "/r.php/example",
            '/r.php',
        ];
        yield 'Root domain, Configured false, accessed via r.php' => [
            'http://example.com',
            false,
            '/r.php/example',
            '/r.php',
        ];
        yield 'Sub directory, Not configured, accessed via r.php' => [
            'http://example.com/moodle',
            null,
            '/moodle/r.php/example',
            '/moodle/r.php',
        ];
        yield 'Sub directory, Configured true, accessed via r.php' => [
            'http://example.com/moodle',
            true,
            '/moodle/r.php/example',
            '/moodle/r.php',
        ];
        yield 'Sub directory, Configured false, accessed via r.php' => [
            'http://example.com/moodle',
            false,
            '/moodle/r.php/example',
            '/moodle/r.php',
        ];
        yield 'Root domain, Not configured, accessed without r.php' => [
            'http://example.com',
            null,
            '/example',
            '/r.php',
        ];
        yield 'Root domain, Configured true, accessed without r.php' => [
            'http://example.com',
            true,
            '/example',
            '',
        ];
        yield 'Root domain, Configured false, accessed without r.php' => [
            'http://example.com',
            false,
            '/example',
            '/r.php',
        ];
        yield 'Sub directory, Not configured, accessed without r.php' => [
            'http://example.com/moodle',
            null,
            '/example',
            '/moodle/r.php',
        ];
        yield 'Sub directory, Configured true, accessed without r.php' => [
            'http://example.com/moodle',
            true,
            '/example',
            '/moodle',
        ];
        yield 'Sub directory, Configured false, accessed without r.php' => [
            'http://example.com/moodle',
            false,
            '/example',
            '/moodle/r.php',
        ];
    }

    /**
     * Test the expected error codes of various exception types.
     *
     * @param \Throwable $exception The exception to throw.
     * @param int $expectedstatus The expected HTTP status code.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('exception_provider')]
    public function test_error_codes_correct(
        \Throwable $exception,
        int $expectedstatus,
    ): void {
        $this->set_basepath('');
        $app = $this->get_app();

        $app->map(['GET'], '/test', fn ($request, $response) => throw $exception);
        // Handle the request.
        $request = new ServerRequest('GET', '/test');
        $returns = $app->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $returns);
        $this->assertEquals($expectedstatus, $returns->getStatusCode());
    }

    /**
     * Data provider for testing error handling.
     *
     * @return \Generator
     */
    public static function exception_provider(): \Generator {
        yield 'Generic Exception' => [new \Exception('Test'), 500];
        yield 'Moodle not_found_exception' => [
            new \core\exception\not_found_exception('test', 'thing'),
            404,
        ];
        yield 'Not Found Exception' => [new \Slim\Exception\HttpNotFoundException(new ServerRequest('GET', '/test')), 404];
        yield 'Method Not Allowed Exception' => [
            new \Slim\Exception\HttpMethodNotAllowedException(
                new ServerRequest('POST', '/test'),
                'GET',
            ),
            405,
        ];

        yield 'Moodle exception not implementing response_aware_exception_interface' => [
            new \core\exception\moodle_exception('test', 'thing'),
            500,
        ];
    }
}
