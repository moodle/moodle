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

use core\tests\route_testcase;
use Slim\Routing\RoutingResults;

/**
 * Tests for the standard route loader.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\route_loader
 */
final class route_loader_test extends route_testcase {
    /**
     * Ensure that the abstract loader implements the interface.
     */
    public function test_class_implements(): void {
        $reflection = new \ReflectionClass(route_loader::class);
        $this->assertTrue($reflection->implementsInterface(route_loader_interface::class));
    }

    /**
     * Test that we are able to fetch all routes.
     */
    public function test_configure_api_routes(): void {
        $loader = new route_loader();

        $app = $this->get_simple_app();
        $routegroups = $loader->configure_routes($app);

        $this->assertIsArray($routegroups);
        $this->assertGreaterThanOrEqual(1, count($routegroups));
        foreach ($routegroups as $group) {
            // Each of the returned groups shoudl be a RouteGroupInterface.
            if (is_array($group)) {
                foreach ($group as $thisgroup) {
                    $this->assertInstanceOf(\Slim\Interfaces\RouteInterface::class, $thisgroup);
                }
            } else {
                $this->assertInstanceOf(\Slim\Interfaces\RouteGroupInterface::class, $group);
            }
        }

        // Note: It is not possible to test the actual routes that are added to
        // the group as they are added to the App which we cannot inspect.
        // We can, however, test that a known route is resolved.

        $collector = $app->getRouteCollector();
        $allroutes = $collector->getRoutes();
        foreach ($allroutes as $route) {
            $thisroutegroups = $route->getGroups();
            $this->assertGreaterThanOrEqual(1, $thisroutegroups);
            foreach ($thisroutegroups as $thisroutegroup) {
                $this->assertContains($thisroutegroup, $routegroups);
            }
        }

        // Resolve the OpenAPI route.
        $path = route_loader_interface::ROUTE_GROUP_API . '/openapi.json';
        $result = $app->getRouteResolver()->computeRoutingResults($path, 'GET');
        $this->assertNotNull($result);
        $this->assertInstanceOf(\Slim\Routing\RoutingResults::class, $result);

        // The result should be found.
        $this->assertEquals(RoutingResults::FOUND, $result->getRouteStatus());

        // It should have an identifier which resolves to a Route.
        $identifier = $result->getRouteIdentifier();
        $this->assertIsString($identifier);
        $route = $app->getRouteResolver()->resolveRoute($identifier);
        $this->assertInstanceOf(\Slim\Interfaces\RouteInterface::class, $route);
    }
}
