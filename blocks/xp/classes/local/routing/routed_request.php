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
 * Routed request.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\routing;

/**
 * Routed request.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class routed_request implements request {

    /** @var url The URL. */
    protected $url;
    /** @var route The route. */
    protected $route;
    /** @var string The HTTP method. */
    protected $method;

    /**
     * Constructor.
     *
     * @param string $method The HTTP method.
     * @param url $url The full URL.
     * @param route $route The route.
     */
    public function __construct($method, url $url, route $route) {
        $this->method = $method;
        $this->url = $url;
        $this->route = $route;
    }

    /**
     * Get method.
     *
     * @return string
     */
    public function get_method() {
        return $this->method;
    }

    /**
     * Get route.
     *
     * @return route
     */
    public function get_route() {
        return $this->route;
    }

    /**
     * Get URL.
     *
     * @return url
     */
    public function get_url() {
        return $this->url;
    }

}
