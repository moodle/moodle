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
 * URL resolver.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\routing;

use coding_exception;
use moodle_url;

/**
 * URL resolver class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_url_resolver implements url_resolver {

    /** The GET argument for routing without $CFG->slasharguments. */
    const ROUTE_GET_PARAM = '_r';

    /** @var moodle_url The base URL. */
    protected $baseurl;
    /** @var routes_config The routes config. */
    protected $routesconfig;

    /**
     * Constructor.
     *
     * @param moodle_url $baseurl The base routing URL.
     * @param routes_config $routesconfig Route config.
     */
    public function __construct(moodle_url $baseurl, routes_config $routesconfig) {
        $this->baseurl = new moodle_url($baseurl->out_omit_querystring());
        $this->routesconfig = $routesconfig;
    }

    /**
     * Return the route's URL from the current request.
     *
     * This makes assumptions on the current request, etc...
     * Shamelessly mostly copied from {@see get_file_arguments()}.
     *
     * @return string
     */
    public function get_route_url() {
        global $SCRIPT;

        $relativepath = false;
        $hasforcedslashargs = false;
        $routepath = $this->baseurl->get_path();

        if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
            // Checks whether $_SERVER['REQUEST_URI'] contains '.../index.php/' instead of '.../index.php?'.
            if ((strpos($_SERVER['REQUEST_URI'], $routepath . '/') !== false)
                    && isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
                $hasforcedslashargs = true;
            }
        }

        if (!$hasforcedslashargs) {
            $relativepath = optional_param(static::ROUTE_GET_PARAM, false, PARAM_PATH);
        }

        // Did we have a relative path yet?
        if ($relativepath !== false && $relativepath !== '') {
            return $relativepath;
        }
        $relativepath = false;

        // Then try extract file from the slasharguments.
        if (stripos($_SERVER['SERVER_SOFTWARE'], 'iis') !== false) {
            if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '') {
                if (strpos($_SERVER['PATH_INFO'], $SCRIPT) === false) {
                    $relativepath = clean_param(urldecode($_SERVER['PATH_INFO']), PARAM_PATH);
                }
            }
        } else {
            // All other apache-like servers depend on PATH_INFO.
            if (isset($_SERVER['PATH_INFO'])) {
                if (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['PATH_INFO'], $_SERVER['SCRIPT_NAME']) === 0) {
                    $relativepath = substr($_SERVER['PATH_INFO'], strlen($_SERVER['SCRIPT_NAME']));
                } else {
                    $relativepath = $_SERVER['PATH_INFO'];
                }
                $relativepath = clean_param($relativepath, PARAM_PATH);
            }
        }

        if (empty($relativepath) || $relativepath[0] !== '/') {
            return '/';
        }

        return $relativepath;
    }

    /**
     * Match a given route URL with the routes.
     *
     * @param string $uri A route URL.
     * @return route|null
     */
    public function match($uri) {
        $route = null;

        foreach ($this->routesconfig->get_routes() as $candidate) {
            $matches = null;
            if (preg_match($candidate->get_regex(), $uri, $matches)) {
                $route = $candidate;
                break;
            }
        }

        if (!$route) {
            return null;
        }

        $params = [];
        $mapping = $route->get_mapping();
        if (count($matches) > 0 && !empty($mapping)) {
            foreach ($matches as $key => $match) {
                if (isset($mapping[$key])) {
                    $params[$mapping[$key]] = $match;
                }
            }
        }

        return new route($route, $params);
    }

    /**
     * Reverse a route name to a URL.
     *
     * This does not support multiple parameters starting with the same
     * text. For instance 'course' and 'courseid' won't work well together.
     *
     * @param string $name The route name.
     * @param array $params The parameters for the route.
     * @return url
     */
    public function reverse($name, array $params = []) {
        $route = $this->routesconfig->get_route($name);
        $url = $route->get_url();
        $mapping = $route->get_mapping();

        if (count($params) != count($mapping)) {
            throw new coding_exception('Could not reverse the route: ' . $name);
        }

        foreach ($mapping as $placeholder) {
            if (!isset($params[$placeholder])) {
                throw new coding_exception('Value for route parameter not found: ' . $placeholder);
            }
            $url = str_replace(':' . $placeholder, $params[$placeholder], $url);
        }

        $absurl = new url($this->baseurl);
        $absurl->set_slashargument($url, static::ROUTE_GET_PARAM);
        return $absurl;
    }

}
