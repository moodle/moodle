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

/**
 * Static routes config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\routing;

use coding_exception;

/**
 * Static routes config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class static_routes_config implements routes_config {

    /** @var array Routes. */
    protected $routes;

    /**
     * Constructor.
     *
     * @param route_definition[] $routes The routes.
     */
    public function __construct(array $routes) {
        $this->routes = array_reduce($routes, function($carry, $route) {
            $carry[$route->get_name()] = $route;
            return $carry;
        }, []);
    }

    /**
     * Get a route.
     *
     * @param string $name The route name.
     * @return route_definition
     */
    public function get_route($name) {
        if (!isset($this->routes[$name])) {
            throw new coding_exception('Unknown route named: ' . $name);
        }
        return $this->routes[$name];
    }

    /**
     * Return an array of routes.
     *
     * @return route_definition[]
     */
    public function get_routes() {
        return $this->routes;
    }

}
