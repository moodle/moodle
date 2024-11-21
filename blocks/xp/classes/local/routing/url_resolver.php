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
 * URL resolver interface.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\routing;

/**
 * URL resolver interface.
 *
 * Such a class should be able to determine the routing path from the
 * current request. Find what route this path matches with. And
 * reverse to an absolute URL from route details.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface url_resolver {

    /**
     * Return the route's URL from the current request.
     *
     * @return string
     */
    public function get_route_url();

    /**
     * Match a given route URL with the routes.
     *
     * @param string $uri A route URL.
     * @return route|null
     */
    public function match($uri);

    /**
     * Reverse a route name to a URL.
     *
     * @param string $name The route name.
     * @param array $params The parameters for the route.
     * @return \moodle_url|url
     */
    public function reverse($name, array $params = []);

}
