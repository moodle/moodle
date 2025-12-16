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

use Google\Service\NetworkServices\ListMeshRouteViewsResponse;
use Google\Service\NetworkServices\MeshRouteView;

/**
 * The "routeViews" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkservicesService = new Google\Service\NetworkServices(...);
 *   $routeViews = $networkservicesService->projects_locations_meshes_routeViews;
 *  </code>
 */
class ProjectsLocationsMeshesRouteViews extends \Google\Service\Resource
{
  /**
   * Get a single RouteView of a Mesh. (routeViews.get)
   *
   * @param string $name Required. Name of the MeshRouteView resource. Format: pro
   * jects/{project_number}/locations/{location}/meshes/{mesh}/routeViews/{route_v
   * iew}
   * @param array $optParams Optional parameters.
   * @return MeshRouteView
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], MeshRouteView::class);
  }
  /**
   * Lists RouteViews (routeViews.listProjectsLocationsMeshesRouteViews)
   *
   * @param string $parent Required. The Mesh to which a Route is associated.
   * Format: projects/{project_number}/locations/{location}/meshes/{mesh}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of MeshRouteViews to return per call.
   * @opt_param string pageToken The value returned by the last
   * `ListMeshRouteViewsResponse` Indicates that this is a continuation of a prior
   * `ListMeshRouteViews` call, and that the system should return the next page of
   * data.
   * @return ListMeshRouteViewsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsMeshesRouteViews($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMeshRouteViewsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsMeshesRouteViews::class, 'Google_Service_NetworkServices_Resource_ProjectsLocationsMeshesRouteViews');
