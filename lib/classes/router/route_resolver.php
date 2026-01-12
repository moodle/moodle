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

use Slim\Routing\RouteResolver;
use Slim\Routing\RoutingResults;

/**
 * Route Resolver that supports routing via r.php.
 *
 * Note: This is a temporary shim to support legacy shims until Moodle 6.0.
 * After that, this class and its references can be removed.
 * See MDL-87625 for more details.
 * This class and it's DI definitions will be removed in Moodle 6.0.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class route_resolver extends RouteResolver {
    #[\Override]
    public function computeRoutingResults(string $uri, string $method): RoutingResults {
        $originalresult = parent::computeRoutingResults($uri, $method);
        if ($originalresult->getRouteStatus() === RoutingResults::FOUND) {
            return $originalresult;
        }

        // In some situations, like hardcoded shims, the request will look like /course/tags.php.
        // When the router is not correctly configured (that ism it still has /r.php in the basepath),
        // we need to adjust the URI to include /r.php so that the routing can work.
        // The basepath includes the wwwroot and /r.php. We need to remove the /r.php from that to leave just the wwwroot.
        // Then we can replae that with the basepath including /r.php.
        $basepath = \core\di::get(\core\router::class)->basepath;

        if (str_ends_with($basepath, '/r.php')) {
            if (str_starts_with($uri, $basepath)) {
                // The router is in legacy mode and the requested URI already includes /r.php.
                return $originalresult;
            } else {
                // The router is in legacy mode but the requested URI does not include /r.php, so add it.
                $updateduri = $basepath . substr($uri, strlen($basepath) - strlen('/r.php'));
            }
        } else {
            $suffixedbasepath = $basepath . '/r.php';
            if (str_starts_with($uri, $suffixedbasepath)) {
                // The router is correctly configured but the requested URI includes /r.php, so remove it.
                $updateduri = $basepath . substr($uri, strlen($suffixedbasepath));
            } else {
                // The router is correctly configured and the requested URI does not include /r.php.
                return $originalresult;
            }
        }

        // Try again with the updated URI.
        $result = parent::computeRoutingResults($updateduri, $method);

        if ($result->getRouteStatus() === RoutingResults::FOUND) {
            return $result;
        }

        // Still not found, return the original result.
        return $originalresult;
    }
}
