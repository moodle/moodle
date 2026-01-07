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

namespace Google\Service\NetworkServices\Resource;

use Google\Service\NetworkServices\GatewayRouteView;
use Google\Service\NetworkServices\ListGatewayRouteViewsResponse;

/**
 * The "routeViews" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkservicesService = new Google\Service\NetworkServices(...);
 *   $routeViews = $networkservicesService->projects_locations_gateways_routeViews;
 *  </code>
 */
class ProjectsLocationsGatewaysRouteViews extends \Google\Service\Resource
{
  /**
   * Get a single RouteView of a Gateway. (routeViews.get)
   *
   * @param string $name Required. Name of the GatewayRouteView resource. Formats:
   * projects/{project_number}/locations/{location}/gateways/{gateway}/routeViews/
   * {route_view}
   * @param array $optParams Optional parameters.
   * @return GatewayRouteView
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GatewayRouteView::class);
  }
  /**
   * Lists RouteViews (routeViews.listProjectsLocationsGatewaysRouteViews)
   *
   * @param string $parent Required. The Gateway to which a Route is associated.
   * Formats: projects/{project_number}/locations/{location}/gateways/{gateway}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of GatewayRouteViews to return per
   * call.
   * @opt_param string pageToken The value returned by the last
   * `ListGatewayRouteViewsResponse` Indicates that this is a continuation of a
   * prior `ListGatewayRouteViews` call, and that the system should return the
   * next page of data.
   * @return ListGatewayRouteViewsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsGatewaysRouteViews($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListGatewayRouteViewsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsGatewaysRouteViews::class, 'Google_Service_NetworkServices_Resource_ProjectsLocationsGatewaysRouteViews');
