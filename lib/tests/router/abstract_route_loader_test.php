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
 * Tests for the abstract route loader.
 *
 * Note: This is an abstract class used as an optional helper for any other route loader.
 * All methods on it are protected and testing them requires a concrete implementation.
 *
 * @package    core
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\router\abstract_route_loader
 */
final class abstract_route_loader_test extends \advanced_testcase {
    /**
     * Ensure that the abstract loader does not implement the interface. That would defeat the point.
     */
    public function test_abstract_route_loader_does_not_implement_interface(): void {
        $reflection = new \ReflectionClass(abstract_route_loader::class);
        $this->assertFalse($reflection->implementsInterface(route_loader_interface::class));
    }

    /**
     * Test that we can fetch routes in a namespace.
     */
    public function test_get_all_routes_in_namespace(): void {
        // phpcs:ignore
        $loader = new class() extends abstract_route_loader {
            // phpcs:ignore
            public function get_routes(): array {
                return $this->get_all_routes_in_namespace(
                    'route\api',
                    function (string $component): string {
                        return '/path/to/' . $component;
                    },
                );
            }
        };

        $routes = $loader->get_routes();
        $this->assertGreaterThan(1, count($routes));
        foreach ($routes as $route) {
            $this->assertArrayHasKey('methods', $route);
            $this->assertArrayHasKey('pattern', $route);
            $this->assertArrayHasKey('callable', $route);
            $this->assertStringStartsWith('/path/to/', $route['pattern']);
        }
    }

    /**
     * Test tha the normalise_component_path method works as expected.
     *
     * @dataProvider normalise_component_path_provider
     * @param string $input
     * @param string $expected
     */
    public function test_normalise_component_path(
        string $input,
        string $expected,
    ): void {
        // phpcs:ignore
        $loader = new class() extends abstract_route_loader {
            // phpcs:ignore
            public function method(...$args): string {
                return $this->normalise_component_path(...$args);
            }
        };

        $this->assertEquals(
            $expected,
            $loader->method($input),
        );
    }

    /**
     * Data provider for test_normalise_component_path.
     *
     * @return array
     */
    public static function normalise_component_path_provider(): array {
        return [
            ['core', 'core'],
            ['core_user', 'user'],
            ['mod_forum', 'mod_forum'],
            ['', ''],
        ];
    }

    /**
     * Tests for the set_route_name_for_callable method.
     */
    public function test_set_route_name_for_callable(): void {
        // phpcs:ignore
        $loader = new class() extends abstract_route_loader {
            // phpcs:ignore
            public function call(...$args): ?string {
                return $this->set_route_name_for_callable(...$args);
            }
        };

        $route = $this->createMock(\Slim\Routing\Route::class);
        $route->expects($this->once())
            ->method('setName')
            ->with('routename');

        $name = $loader->call($route, 'routename');
        $this->assertEquals('routename', $name);

        $route = $this->createMock(\Slim\Routing\Route::class);
        $route->expects($this->once())
            ->method('setName')
            ->with('class::method');

        $name = $loader->call($route, ['class', 'method']);
        $this->assertEquals('class::method', $name);

        $route = $this->createMock(\Slim\Routing\Route::class);
        $route->expects($this->never())
            ->method('setName');

        $name = $loader->call($route, fn () => '');
        $this->assertEquals(null, $name);
    }
}
