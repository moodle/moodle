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
use core\router\route_loader_interface;
use core\tests\route_testcase;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Tests for the Moodle route attribute middleware.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\router\middleware\moodle_route_attribute_middleware
 */
final class moodle_route_attribute_middleware_test extends route_testcase {
    /**
     * Test the Moodle route will be set on a request which has a Moodle route attached.
     */
    public function test_has_route(): void {
        self::load_fixture('core', '/router/route_on_method_only.php');
        self::load_fixture('core', '/router/route_on_class.php');

        $this->add_route_to_route_loader(
            \core\fixtures\route_on_method_only::class,
            'method_with_route',
            grouppath: '',
        );

        $testcase = $this;

        $app = $this->get_simple_app();

        $app->add(function ($request, $handler) use ($testcase) {
            $route = $request->getAttribute(\core\router\route::class);
            $testcase->assertInstanceOf(\core\router\route::class, $route);

            return $handler->handle($request);
        });

        $app->add(di::get(moodle_route_attribute_middleware::class));
        $app->addRoutingMiddleware();
        $app->add(function ($request, $handler) use ($testcase) {
            $testcase->assertNull($request->getAttribute(\core\router\route::class));

            return $handler->handle($request);
        });

        di::get(route_loader_interface::class)->configure_routes($app);

        $request = new ServerRequest('GET', '/method/path');
        $app->handle($request);
    }

    /**
     * Test that no error occurs when no Moodle route is found.
     */
    public function test_has_no_route(): void {
        self::load_fixture('core', '/router/route_on_method_only.php');
        self::load_fixture('core', '/router/route_on_class.php');

        $testcase = $this;

        $app = $this->get_simple_app();
        $app->map(['GET'], '/method/path', fn ($request, $response) => $response);

        $app->add(function ($request, $handler) use ($testcase) {
            $testcase->assertNull($request->getAttribute(\core\router\route::class));

            return $handler->handle($request);
        });

        $app->add(di::get(moodle_route_attribute_middleware::class));
        $app->addRoutingMiddleware();
        $app->add(function ($request, $handler) use ($testcase) {
            $testcase->assertNull($request->getAttribute(\core\router\route::class));

            return $handler->handle($request);
        });

        di::get(route_loader_interface::class)->configure_routes($app);

        $request = new ServerRequest('GET', '/method/path');
        $app->handle($request);
    }
}
