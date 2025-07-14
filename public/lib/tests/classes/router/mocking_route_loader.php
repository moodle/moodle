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

use core\router\abstract_route_loader;
use core\router\route_loader_interface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * A route loader containing mocked routes.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mocking_route_loader extends abstract_route_loader implements route_loader_interface {
    /** @var array[] The mocked routes to configure in the loader */
    private array $groupdata = [];

    #[\Override]
    public function configure_routes(App $app): array {
        $routegroups = [];

        foreach ($this->groupdata as $path => $groupdata) {
            $routegroups[$path] = $app->group($path, function (
                RouteCollectorProxy $group,
            ) use (
                $groupdata,
            ): void {
                foreach ($groupdata as $data) {
                    $group
                        ->map(...$data['mapdata'])
                        ->setName($data['name']);
                }
            });
        }

        return $routegroups;
    }

    /**
     * Add a mocked route to the loader.
     *
     * @param string $grouppath The path of the RouteGroup to add the route to
     * @param array $methods The HTTP methods to add the route for
     * @param string $pattern The path to add the route for
     * @param callable $callable The callable to add the route for
     * @param string $name The name of the route
     */
    public function mock_route_from_callable(
        string $grouppath,
        array $methods,
        string $pattern,
        callable $callable,
        string $name,
    ): void {
        $this->add_groupdata(
            $grouppath,
            [
                'methods' => $methods,
                'pattern' => $pattern,
                'callable' => $callable,
            ],
            $name,
        );
    }

    /**
     * Add all routes in a class to the loader.
     *
     * @param string $grouppath Thegroup to add the route to
     * @param string|\ReflectionMethod $class The class to add to the loader
     */
    public function add_all_routes_in_class(
        string $grouppath,
        \ReflectionMethod|string $class,
    ) {
        $classinfo = $class instanceof \ReflectionClass ? $class : new \ReflectionClass($class);

        $routes = $this->get_all_routes_in_class(
            componentpath: '',
            classinfo: $classinfo,
        );

        foreach ($routes as $mapdata) {
            $this->add_groupdata(
                $grouppath,
                $mapdata,
                implode('::', $mapdata['callable']),
            );
        }
    }

    /**
     * Mock a route from a class method.
     *
     * @param string $grouppath The path to add the route to
     * @param \ReflectionMethod $method The method to mock the route from
     */
    public function mock_route_from_class_method(
        string $grouppath,
        \ReflectionMethod $method,
    ) {
        $mapdata = $this->get_route_data_for_method(
            componentpath: '',
            classinfo: $method->getDeclaringClass(),
            methodinfo: $method,
        );

        $this->add_groupdata(
            $grouppath,
            $mapdata,
            implode('::', $mapdata['callable']),
        );
    }

    /**
     * Add group data to the loader.
     *
     * @param string $grouppath The path of the RouteGroup to add the data to
     * @param array $data The data to add to the group
     * @param string $name The name of the group
     */
    protected function add_groupdata(
        string $grouppath,
        array $data,
        string $name,
    ): void {
        if (!array_key_exists($grouppath, $this->groupdata)) {
            $this->groupdata[$grouppath] = [];
        }

        $this->groupdata[$grouppath][] = [
            'mapdata' => $data,
            'name' => $name,
        ];
    }
}
