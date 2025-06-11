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

use Slim\App;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Routing\RouteCollectorProxy;

/**
 * Route Loader and Discovery agent.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class route_loader extends abstract_route_loader implements route_loader_interface {
    #[\Override]
    public function configure_routes(App $app): array {
        return [
            route_loader_interface::ROUTE_GROUP_API => $this->configure_api_routes($app, route_loader_interface::ROUTE_GROUP_API),
        ];
    }

    /**
     * Configure all API routes.
     *
     * @param App $app
     * @param string $path
     * @return RouteGroupInterface
     */
    protected function configure_api_routes(App $app, string $path): RouteGroupInterface {
        return $app->group($path, function (
            RouteCollectorProxy $group,
        ): void {
            // Add all API routes located in the route\api L2\L3 namespace.
            foreach ($this->get_all_api_routes() as $apiroute) {
                $slimroute = $group->map(...$apiroute);
                $this->set_route_name_for_callable($slimroute, $apiroute['callable']);
            }

            // Add the OpenAPI docs route.
            $callable = [apidocs::class, 'openapi_docs'];
            $slimroute = $group->get('/openapi.json', $callable);
            $this->set_route_name_for_callable($slimroute, $callable);
        });
    }

    /**
     * Fetch all API routes.
     *
     * Note: This method caches results in MUC.
     *
     * @return array[]
     */
    protected function get_all_api_routes(): array {
        $cache = \cache::make('core', 'routes');

        if (!($routes = $cache->get('api_routes'))) {
            $routes = $this->get_all_routes_in_namespace(
                namespace: 'route\api',
                componentpathcallback: $this->normalise_component_path(...),
            );

            $cache->set('api_routes', $routes);
        }

        return $routes;
    }
}
