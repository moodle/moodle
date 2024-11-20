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

namespace core\router;

use core\router\middleware\moodle_route_attribute_middleware;
use core\tests\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;
use Slim\Middleware\RoutingMiddleware;

/**
 * Tests for the route utility class.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\util
 */
final class util_test extends route_testcase {
    /**
     * Ensure that no error is thrown when getting a route instance for a callable.
     */
    public function test_get_route_instance_for_method_not_array_callable(): void {
        $this->assertNull(util::get_route_instance_for_method(fn () => null));
    }

    /**
     * Test getting the path for a callable.
     */
    public function test_get_path_for_callable(): void {
        self::load_fixture('core', 'router/route_on_class.php');

        $this->add_route_to_route_loader(
            \core\fixtures\route_on_class::class,
            'method_with_route',
            grouppath: '/example',
        );

        $url = util::get_path_for_callable(
            [\core\fixtures\route_on_class::class, 'method_with_route'],
            [],
            [],
        );

        $parsedurl = parse_url($url);
        $this->assertEquals(
            (new \moodle_url('/example/class/path/method/path'))->get_path(),
            $parsedurl['path'],
        );
    }

    public function test_get_route_instance_for_method(): void {
        self::load_fixture('core', 'router/route_on_method_only.php');
        self::load_fixture('core', 'router/route_on_class.php');

        // The class has no route attribute.

        // Test a method that has no route attribute.
        $this->assertNull(util::get_route_instance_for_method('core\fixtures\route_on_method_only::method_without_route'));
        $this->assertNull(util::get_route_instance_for_method(['core\fixtures\route_on_method_only', 'method_without_route']));

        // Test a method that has a route attribute.
        $this->assert_route_callable_data(
            'core\fixtures\route_on_method_only::method_with_route',
            '/method/path',
            'core\fixtures\route_on_method_only::method_with_route',
        );
        $this->assert_route_callable_data(
            ['core\fixtures\route_on_method_only', 'method_with_route'],
            '/method/path',
            'core\fixtures\route_on_method_only::method_with_route',
        );

        // The class has a route attribute.

        // Test a method that has no route attribute.
        $this->assertNull(util::get_route_instance_for_method('core\fixtures\route_on_class::method_without_route'));
        $this->assertNull(util::get_route_instance_for_method(['core\fixtures\route_on_class', 'method_without_route']));

        // Test a method that has a route attribute - it is merged with parent.
        $this->assert_route_callable_data(
            'core\fixtures\route_on_class::method_with_route',
            '/class/path/method/path',
            'core\fixtures\route_on_class::method_with_route',
        );
        $this->assert_route_callable_data(
            ['core\fixtures\route_on_class', 'method_with_route'],
            '/class/path/method/path',
            'core\fixtures\route_on_class::method_with_route',
        );
    }

    /**
     * Assertion helper to asser that a callable is a route and has the expected path and name.
     *
     * @param callable $callable The callable to check.
     * @param string $path The expected path.
     * @param string $routename The expected route name.
     */
    protected function assert_route_callable_data(
        $callable,
        string $path,
        string $routename,
    ): void {
        $route = util::get_route_instance_for_method($callable);
        $this->assertInstanceOf(route::class, $route);
        $this->assertEquals($path, $route->get_path());
        $this->assertIsString(util::get_route_name_for_callable($callable));
        $this->assertEquals($routename, util::get_route_name_for_callable($callable));
    }

    /**
     * Test getting the route name for an anonymous callable.
     */
    public function test_get_route_for_callable_not_array_callable(): void {
        $this->expectException(\coding_exception::class);
        $this->assertNull(util::get_route_name_for_callable(fn () => null));
    }

    public function test_get_route_instance_for_request(): void {
        self::load_fixture('core', 'router/route_on_method_only.php');

        $app = $this->get_simple_app();
        $app->add(moodle_route_attribute_middleware::class);
        $app->addRoutingMiddleware();
        $app->get('/method/path', [\core\fixtures\route_on_method_only::class, 'method_with_route']);
        $app->handle(new ServerRequest('GET', '/method/path'));

        $request = $this->route_request($app, new ServerRequest('GET', '/method/path'));

        $route = util::get_route_instance_for_request($request);

        $this->assertInstanceOf(route::class, $route);
        $this->assertEquals('/method/path', $route->get_path());

        $secondroute = util::get_route_instance_for_request($request);
        $this->assertInstanceOf(route::class, $secondroute);
        $this->assertEquals('/method/path', $secondroute->get_path());
    }
}
