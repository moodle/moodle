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

namespace core\tests\router;

use core\router;
use core\router\bridge;
use core\router\route_loader_interface;
use core\router\schema\openapi_base;
use core\router\schema\referenced_object;
use core\router\schema\specification;
use stdClass;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\ExpectationFailedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Slim\App;
use Slim\Middleware\RoutingMiddleware;
use Slim\Routing\Route;
use Slim\Routing\RouteContext;

/**
 * Tests for user preference API handler.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class route_testcase extends \advanced_testcase {
    /**
     * Update the test route loader using the supplied callback.
     *
     * @param callable $modifier
     */
    protected function update_test_route_loader(
        callable $modifier,
    ): void {
        $routeloader = \core\di::get(mocking_route_loader::class);
        $modifier($routeloader);
        \core\di::set(route_loader_interface::class, $routeloader);
    }

    /**
     * Add a route from a class method.
     *
     * @param string $classname The class to add the route from
     * @param string $methodname The method name to add
     * @param null|string $grouppath The path to the route group
     */
    protected function add_route_to_route_loader(
        string $classname,
        string $methodname,
        ?string $grouppath = null,
    ) {
        $grouppath = $grouppath ?? $this->guess_group_path_from_classname($classname);
        $this->update_test_route_loader(fn (mocking_route_loader $routeloader) => $routeloader->mock_route_from_class_method(
            $grouppath,
            new \ReflectionMethod($classname, $methodname),
        ));
    }

    /**
     * Add all routes from the specified class to the test loader.
     *
     * Only methods within the class with a #[route] attribute will be added.
     *
     * @param string $classname The class to add routes from
     * @param null|string $grouppath The path of the route group
     */
    protected function add_class_routes_to_route_loader(
        string $classname,
        ?string $grouppath = null,
    ): void {
        $this->update_test_route_loader(
            fn (mocking_route_loader $routeloader) => $routeloader->add_all_routes_in_class(
                grouppath: $grouppath ?? $this->guess_group_path_from_classname($classname),
                class: $classname,
            ),
        );
    }

    /**
     * Guess the group path from a class name.
     *
     * @param string $classname
     * @return string
     */
    protected function guess_group_path_from_classname(
        string $classname,
    ): string {
        [, , $l3] = explode('\\', $classname, 4);

        if ($l3 === 'api') {
            return route_loader_interface::ROUTE_GROUP_API;
        }

        throw new \coding_exception("Unable to determine route path for '{$classname}'");
    }

    /**
     * Mock a route from a route attribute.
     *
     * @param string $grouppath
     * @param \core\router\route $route
     * @param string $name
     * @param callable|null $callable
     */
    protected function mock_route_from_route_attribute(
        string $grouppath,
        \core\router\route $route,
        string $name = 'route',
        ?callable $callable = null,
    ): void {
        if ($callable === null) {
            $callable = fn ($request, $response) => $response->withStatus(200);
        }

        $this->update_test_route_loader(fn (mocking_route_loader $routeloader) => $routeloader->mock_route_from_callable(
            grouppath: $grouppath,
            methods: $route->get_methods(['GET']),
            pattern: $route->get_path(),
            callable: $callable,
            name: $name,
        ));
    }

    /**
     * Get a fully-configured instance of the Moodle Routing Application.
     *
     * @return App
     */
    protected function get_app(): App {
        $router = $this->get_router();

        return $router->get_app();
    }

    /**
     * Get a fully-configured instance of the Moodle Routing Application.
     *
     * @param string $basepath The basepath for the router
     * @return router
     */
    protected function get_router(string $basepath = ''): router {
        \core\di::set(
            router::class,
            \DI\autowire(router::class)->constructorParameter('basepath', $basepath),
        );

        return \core\di::get(router::class);
    }

    /**
     * Get an unconfigured instance of the Slim Application.
     *
     * @return App
     */
    protected function get_simple_app(): App {
        return bridge::create(
            container: \core\di::get_container(),
        );
    }

    /**
     * Get the request for a route which is known to the router.
     *
     * @param \core\router\route $route
     * @param string $path
     * @return ServerRequestInterface
     */
    protected function get_request_for_routed_route(
        \core\router\route $route,
        string $path,
    ): ServerRequestInterface {
        $this->mock_route_from_route_attribute('', $route);

        // Grab just one method.
        $methods = $route->get_methods();
        $method = $methods ? reset($methods) : 'GET';

        $request = $this->create_request(
            method: $method,
            path: $path,
            prefix: '',
            route: $route,
        );

        $request = $this->route_request(
            $this->get_app(),
            $request,
        );

        return $request;
    }

    /**
     * Create a Request object.
     *
     * @param string $method
     * @param string $path
     * @param string $prefix
     * @param array  $headers
     * @param array  $cookies
     * @param array  $serverparams
     * @param null|\core\router\route $route
     * @return ServerRequestInterface
     */
    protected function create_request(
        string $method,
        string $path,
        string $prefix = route_loader_interface::ROUTE_GROUP_API,
        array $headers = ['Content-Type' => 'application/json'],
        array $cookies = [],
        array $serverparams = [],
        ?\core\router\route $route = null,
    ): ServerRequestInterface {
        $uri = new Uri($prefix . $path);

        $request = new ServerRequest(
            method: $method,
            uri: $uri,
            headers: $headers,
            serverParams: $serverparams,
        );

        // Sadly Guzzle's Uri only deals with query strings, not query params.
        $query = $uri->getQuery();
        if ($query) {
            $queryparams = [];
            foreach (explode('&', $query) as $queryparam) {
                [$key, $value] = explode('=', $queryparam, 2);
                $queryparams[$key] = $value;
            }
            $request = $request->withQueryParams($queryparams);
        }

        if ($route) {
            $request = $request->withAttribute(\core\router\route::class, $route);
        }

        return $request
            ->withCookieParams($cookies);
    }

    /**
     * Process a request with the app.
     *
     * @param string $method
     * @param string $path
     * @param string $prefix
     * @param array  $headers
     * @param null|StreamInterface $body
     * @param null|string $contenttype
     * @param array  $cookies
     * @param array  $serverparams
     * @return ResponseInterface
     */
    protected function process_request(
        string $method,
        string $path,
        string $prefix = '',
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        ?StreamInterface $body = null,
        ?string $contenttype = 'application/json',
        array $cookies = [],
        array $serverparams = [],
    ): ResponseInterface {
        $app = $this->get_app();
        if ($contenttype !== null) {
            $headers['Content-Type'] = $contenttype;
        }
        $request = $this->create_request(
            $method,
            $path,
            $prefix,
            $headers,
            $cookies,
            $serverparams,
        );

        if ($body) {
            $request = $request->withBody($body);
        }

        return $app->handle($request);
    }

    /**
     * Process a request with the app.
     *
     * @param string $method
     * @param string $path
     * @param array  $headers
     * @param null|StreamInterface $body
     * @param array  $cookies
     * @param array  $serverparams
     * @return ResponseInterface
     */
    protected function process_api_request(
        string $method,
        string $path,
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        ?StreamInterface $body = null,
        array $cookies = [],
        array $serverparams = [],
    ): ResponseInterface {
        return $this->process_request(
            method: $method,
            path: $path,
            prefix: route_loader_interface::ROUTE_GROUP_API,
            headers: $headers,
            body: $body,
            cookies: $cookies,
            serverparams: $serverparams,
        );
    }

    /**
     * Route a request within the app.
     *
     * @param App $app
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    protected function route_request(
        App $app,
        ServerRequestInterface $request,
    ): ServerRequestInterface {
        $routingmiddleware = new RoutingMiddleware(
            $app->getRouteResolver(),
            $app->getRouteCollector()->getRouteParser(),
        );

        return $routingmiddleware->performRouting($request);
    }

    /**
     * Create a route and route it to create a request.
     *
     * @param string $routepath
     * @param string $requestpath
     * @return ServerRequestInterface
     */
    protected function create_route(
        string $routepath,
        string $requestpath,
    ): ServerRequestInterface {
        $app = $this->get_simple_app();
        $app->get($routepath, fn () => new Response());
        $request = $this->route_request($app, new ServerRequest('GET', $requestpath));

        return $request;
    }

    /**
     * Get the Slim Route object from a Request object.
     *
     * @param ServerRequestInterface $request
     * @return Route
     */
    protected function get_slim_route_from_request(
        ServerRequestInterface $request,
    ): Route {
        return $request->getAttribute(RouteContext::ROUTE);
    }

    /**
     * Assert that a Response object was valid.
     *
     * @param ResponseInterface $response
     * @param null|int $statuscode The expected status code
     * @throws ExpectationFailedException
     */
    protected function assert_valid_response(
        ResponseInterface $response,
        ?int $statuscode = 200,
    ): void {
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(
            $statuscode,
            $response->getStatusCode(),
            "Response status code is not $statuscode",
        );
    }

    /**
     * Assert that the supplied response related to an exception.
     *
     * @param ResponseInterface $response
     * @param null|int $responsecode The expected response code
     */
    protected function assert_exception_response(
        ResponseInterface $response,
        ?int $responsecode = null,
    ): void {
        $this->assertInstanceOf(Response::class, $response);
        $this->assertNotEquals(
            200,
            $response->getStatusCode(),
        );

        if ($responsecode !== null) {
            $this->assertEquals(
                $responsecode,
                $response->getStatusCode(),
            );
        }

        $payload = $this->decode_response($response);
        $this->assertObjectHasProperty('message', $payload);
        $this->assertObjectHasProperty('stacktrace', $payload);
        foreach ($payload->stacktrace as $frame) {
            $this->assertObjectNotHasProperty('args', $frame);
        }
    }

    /**
     * Assert that the supplied response was an invalid_parameter_exception response.
     *
     * @param ResponseInterface $response
     */
    protected function assert_invalid_parameter_response(
        ResponseInterface $response,
    ): void {
        $this->assert_exception_response($response, 400);

        $payload = $this->decode_response($response);
        $this->assertObjectHasProperty('errorcode', $payload);
        $this->assertEquals('invalidparameter', $payload->errorcode);
    }

    /**
     * Assert that the supplied response was an access_denied exception response.
     *
     * @param ResponseInterface $response
     */
    protected function assert_access_denied_response(
        ResponseInterface $response,
    ): void {
        $this->assert_exception_response($response, 403);

        $payload = $this->decode_response($response);
        $this->assertObjectHasProperty('errorcode', $payload);
    }

    /**
     * Assert that the supplied response was a not_found exception response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    protected function assert_not_found_response(
        ResponseInterface $response,
    ): void {
        $this->assert_exception_response($response, 404);

        $payload = $this->decode_response($response);
        $this->assertObjectHasProperty('errorcode', $payload);
    }

    /**
     * Decode the JSON response for a Response object.
     *
     * @param ResponseInterface $response
     * @param bool $forcearray Force the contents to Array instead of Object
     * @return stdClass|array
     */
    protected function decode_response(
        ResponseInterface $response,
        bool $forcearray = false,
    ): stdClass|array {
        if ($forcearray) {
            return json_decode(
                json: (string) $response->getBody(),
                associative: true,
            );
        } else {
            return (object) json_decode(
                json: (string) $response->getBody(),
                associative: false,
                flags: JSON_FORCE_OBJECT,
            );
        }
    }

    /**
     * Get the schema for an OpenAPI Component.
     *
     * Components include headers, parameters, responses, examples, requestBodies, and schemas.
     *
     * All components are subclasses of the openapi_base class and may be referenced.
     *
     * Any component which implements the referenced_object interface will return a reference
     * to the stored internal object.
     *
     * @param specification $api
     * @param openapi_base $component
     * @return stdClass|null
     */
    protected function get_api_component_schema(
        specification $api,
        openapi_base $component,
    ): ?stdClass {
        $this->assertInstanceOf(referenced_object::class, $component);

        if (is_a($component, \core\router\schema\header_object::class)) {
            $type = 'headers';
        } else if (is_a($component, \core\router\schema\parameter::class)) {
            $type = 'parameters';
        } else if (is_a($component, \core\router\schema\response\response::class)) {
            $type = 'responses';
        } else if (is_a($component, \core\router\schema\example::class)) {
            $type = 'examples';
        } else if (is_a($component, \core\router\schema\request_body::class)) {
            $type = 'requestBodies';
        } else if (is_a($component, \core\router\schema\objects\type_base::class)) {
            $type = 'schemas';
        } else {
            $this->fail('Component is not a recognised type');
        }

        $ref = $component->get_reference(false);

        $schema = $api->get_schema();
        $components = $schema->components;
        $component = $components->{$type}->{$ref} ?? null;

        return $component;
    }
}
