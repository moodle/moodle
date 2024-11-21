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
 * Dead simple router.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\routing;

use coding_exception;
use moodle_exception;

/**
 * Dead simple router.
 *
 * I don't usually like re-inventing the wheel, but as there are so many different
 * routing libraries for PHP, most of them being larger than I need. Symfony
 * would be a good option but there is always the risk of conflicting with other
 * plugins which already include the library. So, I took the easy path... the best
 * would of course be for Moodle core to have something similar to this.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class router {

    /** @var url_resolver_interface The URL resolver. */
    protected $urlresolver;
    /** @var controller_resolver The controller resolver. */
    protected $ctrlresolver;

    /**
     * Constructor.
     *
     * @param url_resolver $urlresolver The URL resolver.
     */
    public function __construct(url_resolver $urlresolver) {
        $this->urlresolver = $urlresolver;
    }

    /**
     * Dispatch.
     *
     * @return void
     */
    public function dispatch() {
        $uri = $this->urlresolver->get_route_url();
        $route = $this->urlresolver->match($uri);

        if (!$route) {
            throw new moodle_exception('Unknown route: ' . $uri);
        }

        $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
        $url = $this->urlresolver->reverse($route->get_definition()->get_name(), $route->get_params());
        $request = new routed_request($method, $url, $route);

        $ctrl = $this->get_controller_from_request($request);
        $ctrl->handle($request);
    }

    /**
     * Find the controller from the request.
     *
     * @param routed_request $request The request.
     * @return block_xp\local\controller\controller_interface
     */
    protected function get_controller_from_request(routed_request $request) {
        $route = $request->get_route();
        $name = $route->get_definition()->get_controller_name();
        $class = "block_xp\\local\\controller\\{$name}_controller";

        if (!class_exists($class)) {
            throw new coding_exception('Controller for route not found.');
        }

        return new $class();
    }

}
