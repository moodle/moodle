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

/**
 * Tests for the route resolver that supports routing with and without the r.php prefix.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(route_resolver::class)]
final class route_resolver_test extends \core\tests\router\route_testcase {
    /**
     * Ensure that the computation of routing results can add or remove /r.php as needed.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('compute_routing_results_provider')]
    public function test_compute_routing_results(
        string $basepath,
        string $path,
        string $requestedpath,
        int $expectedresult,
    ): void {
        $this->add_route_to_route_loader(
            self::class,
            'method_under_test',
            '',
        );

        $router = $this->get_router($basepath);
        $result = $router->get_app()->getRouteResolver()->computeRoutingResults(
            $requestedpath,
            'GET',
        );

        $this->assertEquals($expectedresult, $result->getRouteStatus());
    }

    /**
     * A test route handler.
     */
    #[\core\router\route(path: '/method_under_test')]
    public function method_under_test(): void {
        // Do nothing.
    }

    /**
     * Data provider for the routing provider.
     *
     * @return \Generator<string, array<int|string>, mixed, void>
     */
    public static function compute_routing_results_provider(): \Generator {
        $prefixes = ['/moodle', ''];

        foreach ($prefixes as $prefix) {
            yield "Router in legacy mode, request without r.php, wwwroot is '{$prefix}'" => [
                "{$prefix}/r.php",
                '/method_under_test',
                "{$prefix}/method_under_test",
                \Slim\Routing\RoutingResults::FOUND,
            ];

            yield "Router in legacy mode, request with r.php, wwwroot is '{$prefix}'" => [
                "{$prefix}/r.php",
                '/method_under_test',
                "{$prefix}/r.php/method_under_test",
                \Slim\Routing\RoutingResults::FOUND,
            ];

            yield "Router in normal mode, request without r.php, wwwroot is '{$prefix}'" => [
                "{$prefix}",
                '/method_under_test',
                "{$prefix}/method_under_test",
                \Slim\Routing\RoutingResults::FOUND,
            ];

            yield "Router in normal mode, request with r.php, wwwroot is '{$prefix}'" => [
                "{$prefix}",
                '/method_under_test',
                "{$prefix}/r.php/method_under_test",
                \Slim\Routing\RoutingResults::FOUND,
            ];

            yield "Router in legacy mode, request unrelated path, wwwroot is '{$prefix}'" => [
                "{$prefix}/r.php",
                '/method_under_test',
                "{$prefix}/otherpath",
                \Slim\Routing\RoutingResults::NOT_FOUND,
            ];

            yield "Router in normal mode, request unrelated path, wwwroot is '{$prefix}'" => [
                "{$prefix}",
                '/method_under_test',
                "{$prefix}/otherpath",
                \Slim\Routing\RoutingResults::NOT_FOUND,
            ];

            yield "Router in legacy mode, request root path, wwwroot is '{$prefix}'" => [
                "{$prefix}/r.php",
                '/method_under_test',
                "{$prefix}/",
                \Slim\Routing\RoutingResults::NOT_FOUND,
            ];

            yield "Router in normal mode, request root path, wwwroot is '{$prefix}'" => [
                "{$prefix}",
                '/method_under_test',
                "{$prefix}/",
                \Slim\Routing\RoutingResults::NOT_FOUND,
            ];

            yield "Router in legacy mode, request r.php only, wwwroot is '{$prefix}'" => [
                "{$prefix}/r.php",
                '/method_under_test',
                "{$prefix}/r.php",
                \Slim\Routing\RoutingResults::NOT_FOUND,
            ];

            yield "Router in normal mode, request r.php only, wwwroot is '{$prefix}'" => [
                "{$prefix}",
                '/method_under_test',
                "{$prefix}/r.php",
                \Slim\Routing\RoutingResults::NOT_FOUND,
            ];

            yield "Router in legacy mode, request empty path, wwwroot is '{$prefix}'" => [
                "{$prefix}/r.php",
                '/method_under_test',
                "{$prefix}",
                \Slim\Routing\RoutingResults::NOT_FOUND,
            ];
        }
    }
}
