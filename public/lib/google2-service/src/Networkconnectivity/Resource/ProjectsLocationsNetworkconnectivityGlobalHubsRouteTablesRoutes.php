<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Networkconnectivity\Resource;

use Google\Service\Networkconnectivity\ListRoutesResponse;
use Google\Service\Networkconnectivity\Route;

/**
 * The "routes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkconnectivityService = new Google\Service\Networkconnectivity(...);
 *   $routes = $networkconnectivityService->projects_locations_global_hubs_routeTables_routes;
 *  </code>
 */
class ProjectsLocationsNetworkconnectivityGlobalHubsRouteTablesRoutes extends \Google\Service\Resource
{
  /**
   * Gets details about the specified route. (routes.get)
   *
   * @param string $name Required. The name of the route resource.
   * @param array $optParams Optional parameters.
   * @return Route
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Route::class);
  }
  /**
   * Lists routes in a given route table.
   * (routes.listProjectsLocationsNetworkconnectivityGlobalHubsRouteTablesRoutes)
   *
   * @param string $parent Required. The parent resource's name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter An expression that filters the list of results.
   * @opt_param string orderBy Sort the results by a certain order.
   * @opt_param int pageSize The maximum number of results to return per page.
   * @opt_param string pageToken The page token.
   * @return ListRoutesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsNetworkconnectivityGlobalHubsRouteTablesRoutes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListRoutesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsNetworkconnectivityGlobalHubsRouteTablesRoutes::class, 'Google_Service_Networkconnectivity_Resource_ProjectsLocationsNetworkconnectivityGlobalHubsRouteTablesRoutes');
